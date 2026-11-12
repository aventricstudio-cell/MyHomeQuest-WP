<?php
/**
 * WPResidence Developer Creation API Functions
 *
 * Functions for creating new developers via the REST API,
 * including permission checks, data validation, and media handling.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Permission Callback for Creating a New Developer via REST API
 *
 * Validates user authentication and permission to create developers.
 * Checks JWT token validity, user login status, and membership restrictions.
 *
 * @param WP_REST_Request $request The current REST request.
 * @return true|WP_Error True if the user has permission, or WP_Error if not.
 */
function wpresidence_check_permissions_for_posting_developer(WP_REST_Request $request) {
    // Verify the JWT token
    $userID = apply_filters('determine_current_user', null);
    if (!$userID) {
        return new WP_Error(
            'jwt_auth_failed',
            __('Invalid or missing JWT token.'),
            ['status' => 403]
        );
    }
    wp_set_current_user($userID);

    // Fetch the current user details
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;

    // Check if the user is logged in
    if (!$userID || !is_user_logged_in()) {
        return new WP_Error(
            'rest_forbidden',
            esc_html__('You must be logged in to create a developer.', 'wpresidence-core'),
            ['status' => 403]
        );
    }

    // Check user capabilities for posting
    if (!current_user_can('publish_estate_developers')) {
        return new WP_Error(
            'rest_forbidden',
            esc_html__('You do not have permission to create a developer.', 'wpresidence-core'),
            ['status' => 403]
        );
    }

    // For developers, we could check membership or specific permissions
    // Similar to property creation but adapted for developers

    return true;
}

/**
 * Create a new developer.
 * 
 * Creates a developer post with associated taxonomies, metadata, and media.
 * Validates required fields and handles membership status updates.
 *
 * @param WP_REST_Request $request The REST API request containing the developer data.
 * @return WP_REST_Response|WP_Error The response or error.
 */
function wpresidence_create_developer(WP_REST_Request $request) {
    // Retrieve mandatory fields from options
    $mandatory_fields = array('developer_name', 'developer_email');
  
    // Parse and sanitize input data
    $input_data = wpresidence_parse_request_params($request);

    // Validate mandatory fields
    foreach ($mandatory_fields as $field) {
        if (empty($input_data[$field])) {
            return new WP_Error(
                'rest_missing_field',
                __('Missing mandatory field: ' . $field),
                ['status' => 400]
            );
        }
    }

    // Additional validation for email field
    if (!is_email($input_data['developer_email'])) {
        return new WP_Error(
            'rest_invalid_email',
            __('Invalid email address.'),
            ['status' => 400]
        );
    }

    // Set current user
    $current_user = get_current_user_id();
  
    // Get the developer name
    $developer_title = $input_data['developer_name'];
    
    // Create the developer post
    $post_id = wp_insert_post([
        'post_type'   => 'estate_developer',
        'post_title'  => $developer_title,
        'post_status' => 'publish',
        'post_author' => $current_user,
        'post_content' => isset($input_data['developer_description']) ? $input_data['developer_description'] : '',
    ]);

    // Handle post creation errors
    if (is_wp_error($post_id)) {
        return $post_id;
    }

    // Process taxonomies and meta fields
    foreach ($input_data as $key => $value) {
        if (taxonomy_exists($key)) {
            // Handle taxonomy terms
            if (is_array($value)) {
                wp_set_object_terms($post_id, $value, $key);
            }
        } else {
            // Handle special meta fields
            if ($key == 'developer_custom_data' && isset($input_data['developer_custom_data']) && is_array($input_data['developer_custom_data'])) {
                wpresidence_process_developer_custom_fields($post_id, $input_data['developer_custom_data']);
            } else if ($key=='user_registration' && is_array($input_data['user_registration'])) {
                $user_result = wpresidence_process_user_registration($post_id, $input_data['user_registration'], 'estate_developer', $input_data);
           
                // Check for error
                if (is_wp_error($user_result)) {
                    // Delete the agent post we just created
                    wp_delete_post($post_id, true);
                    
                    // Return the specific error
                    return new WP_Error(
                        'user_registration_failed',
                        $user_result->get_error_message(),
                        ['status' => 400]
                    );
                }


            } else {
                // Standard meta field
                update_post_meta($post_id, $key, $value);
            }
        }
    }

    // Process featured image
    if (!empty($input_data['featured_image'])) {
        wpresidence_set_developer_featured_image($post_id, $input_data['featured_image']);
    }

    // Return success response
    return rest_ensure_response([
        'status'    => 'success',
        'developer_id'  => $post_id,
        'message'   => __('Developer created successfully.')
    ]);
}

/**
 * Process custom fields and save them as post meta for a developer.
 * 
 * Processes an array of custom fields with label-value pairs
 * and saves them as developer_custom_data meta entry.
 *
 * @param int $post_id The ID of the developer post.
 * @param array $custom_fields The array of custom field data containing labels and values.
 */
function wpresidence_process_developer_custom_fields($post_id, $custom_fields) {
    if (is_array($custom_fields)) {
        // Sanitize and format the custom fields
        $developer_fields_array = array();
        foreach ($custom_fields as $field) {
            if (isset($field['label'], $field['value'])) {
                $developer_fields_array[] = array(
                    'label' => sanitize_text_field($field['label']),
                    'value' => sanitize_text_field($field['value'])
                );
            }
        }
        
        // Save the formatted custom fields
        update_post_meta($post_id, 'developer_custom_data', $developer_fields_array);
    }
}

/**
 * Set the featured image for a developer.
 * 
 * Handles attachment creation and setting the post thumbnail
 * from an image URL or attachment ID.
 *
 * @param int $post_id The ID of the developer post.
 * @param mixed $image_url The image data (URL or attachment ID).
 * @return bool True on success, false on failure.
 */
function wpresidence_set_developer_featured_image($post_id, $image_url) {
    // If the image is a numeric value, treat it as an attachment ID
    if (!empty($image_url)) {
        // Validate that the file is an image
        $file_info = pathinfo($image_url);
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array(strtolower($file_info['extension']), $valid_extensions)) {
            return false;
        }

        // Download the image to the WordPress uploads directory
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($image_url);
        $filename = basename($image_url);

        if ($image_data) {
            // Save image to uploads directory
            $file_path = $upload_dir['path'] . '/' . $filename;
            file_put_contents($file_path, $image_data);

            // Prepare the file for attachment
            $wp_filetype = wp_check_filetype($filename, null);
            if (!in_array($wp_filetype['ext'], $valid_extensions)) {
                unlink($file_path); // Remove invalid files
                return false;
            }

            // Create attachment post
            $attachment = [
                'post_mime_type' => $wp_filetype['type'],
                'post_title'     => sanitize_file_name($filename),
                'post_content'   => '',
                'post_status'    => 'inherit',
            ];

            // Insert the attachment
            $attachment_id = wp_insert_attachment($attachment, $file_path, $post_id);

            // Generate attachment metadata and update
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $attach_data = wp_generate_attachment_metadata($attachment_id, $file_path);
            wp_update_attachment_metadata($attachment_id, $attach_data);

            set_post_thumbnail($post_id, $attachment_id);
            return true;
        }
    }
    
    return false;
}