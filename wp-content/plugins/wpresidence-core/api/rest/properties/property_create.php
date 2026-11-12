<?php
/**
 * WPResidence Property Creation API Functions
 *
 * Functions for creating new properties via the REST API,
 * including permission checks, data validation, and media handling.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Permission Callback for Creating a New Property via REST API
 *
 * Validates user authentication and permission to create properties.
 * Checks JWT token validity, user login status, and membership restrictions.
 *
 * @param WP_REST_Request $request The current REST request.
 * @return true|WP_Error True if the user has permission, or WP_Error if not.
 */
function wpresidence_check_permissions_for_posting(WP_REST_Request $request) {
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
            esc_html__('You must be logged in to create a property.', 'wpresidence-core'),
            ['status' => 403]
        );
    }

    // Check user capabilities for posting
    if (!current_user_can('publish_estate_properties')) {
        return new WP_Error(
            'rest_forbidden',
            esc_html__('You do not have permission to create a property.', 'wpresidence-core'),
            ['status' => 403]
        );
    }

    // Check membership status and available listings
    $paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));
    $user_number_listings   = intval(wpestate_get_current_user_listings($userID));
    
    if ($paid_submission_status == 'no') {
        return true;
    }else if ($paid_submission_status == 'membership' && $user_number_listings > 0) { // if user can submit
        return true;
    } else {
        return new WP_Error(
            'rest_forbidden',
            esc_html__('Your membership does not allow you to create a new property.'.    $paid_submission_status , 'wpresidence-core'),
            [
                'status' => 403,
            ]
        );
    }

    return true;
}

/**
 * Create a new property.
 * 
 * Creates a property post with associated taxonomies, metadata, and media.
 * Handles membership status updates.
 *
 * @param WP_REST_Request $request The REST API request containing the property data.
 * @return WP_REST_Response|WP_Error The response or error.
 */
function wpresidence_create_property(WP_REST_Request $request) {
    // Parse and sanitize input data
    $input_data = wpresidence_parse_request_params($request);

    // Set current user
    $current_user = get_current_user_id();
  
    // Create the property post
    $post_id = wp_insert_post([
        'post_type'   => 'estate_property',
        'post_title'  => $input_data['title'],
        'post_status' => 'publish',
        'post_author' => $current_user,
        'post_content'  => $input_data['property_description'],
    ]);

    // Handle post creation errors
    if (is_wp_error($post_id)) {
        return $post_id;
    }
    
    // Update membership package count if using membership system
    $paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));
    if ($paid_submission_status == 'membership') { // update pack status
        wpestate_update_listing_no($current_user);
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
            if ($key === 'custom_fields') {
                wpresidence_process_custom_fields($post_id, $value);
            } else {
                // Standard meta field
                update_post_meta($post_id, $key, $value);
            }
        }
    }

    //
    update_post_meta($post_id, 'sidebar_agent_option', 'global');  

    // Process image uploads
    if (!empty($input_data['images']) && is_array($input_data['images'])) {
        wpresidence_process_images($post_id, $input_data['images']);
    }

    //set the slider type
    update_post_meta($post_id, 'local_pgpr_slider_type', 'global');

    // Reset and regenerate cache for the newly created property
    if (function_exists('wpestate_delete_cache')) {
        wpestate_delete_cache();
    }
    if (function_exists('wpestate_api_set_cache_post_data')) {
        wpestate_api_set_cache_post_data($post_id, 'estate_property');
    }

    // Return success response
    return rest_ensure_response([
        'status'       => 'success',
        'property_id'  => $post_id,
    ]);
}

/**
 * Process custom fields and save them as post meta.
 * 
 * Processes an array of custom fields with slug-value pairs
 * and saves them as individual post meta entries.
 *
 * @param int $post_id The ID of the property post.
 * @param array $custom_fields The array of custom field data containing slugs and values.
 */
function wpresidence_process_custom_fields($post_id, $custom_fields) {
    if (is_array($custom_fields)) {
        foreach ($custom_fields as $field) {
            // Validate field structure before processing
            if (isset($field['slug'], $field['value'])) {
                $safe_slug = sanitize_key($field['slug']);
                if (!empty($safe_slug)) {
                    update_post_meta($post_id, $safe_slug, sanitize_text_field($field['value']));
                }
            }
        }
    }
}

/**
 * Process images and save them as attachments in WordPress.
 * 
 * Downloads external images with proper validation, creates WordPress attachments,
 * sets featured image, and creates a property gallery with robust security checks:
 * - URL validation (HTTPS only)
 * - File size limits
 * - Strict MIME type verification
 * - Proper error handling
 * - Secure file operations
 *
 * @param int $post_id The ID of the property post.
 * @param array $images The array of image data containing IDs and URLs.
 * @return array Status information about processed images
 */
function wpresidence_process_images($post_id, $images) {
    $gallery_images = [];
    $results = [
        'success' => [],
        'errors' => []
    ];
    
    // Define security constraints
    $max_file_size = 5 * 1024 * 1024; // 5MB limit
    $valid_mime_types = [
        'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'
    ];
    $valid_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $timeout = 10; // 10 second timeout for downloads
    
    foreach ($images as $index => $image) {
        // Basic parameter validation
        if (empty($image['id']) || empty($image['url'])) {
            $results['errors'][] = "Image at index $index missing required fields";
            continue;
        }

        // URL validation
        $url = esc_url_raw($image['url']);
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            $results['errors'][] = "Invalid URL format for image at index $index";
            continue;
        }
        
        // Only allow HTTPS URLs
        if (strpos($url, 'https://') !== 0) {
            $results['errors'][] = "Only HTTPS URLs are allowed for image at index $index";
            continue;
        }
        
        // Validate file extension from URL
        $file_info = pathinfo($url);
        $extension = strtolower($file_info['extension'] ?? '');
        if (!in_array($extension, $valid_extensions, true)) {
            $results['errors'][] = "Invalid file extension for image at index $index";
            continue;
        }
        
        // Create temporary file
        $temp_file = wp_tempnam();
        if (!$temp_file) {
            $results['errors'][] = "Failed to create temporary file for image at index $index";
            continue;
        }
        
        // Download file with timeout and size constraints
        $context_options = [
            'http' => [
                'timeout' => $timeout,
                'header' => "User-Agent: WordPress/" . get_bloginfo('version')
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true
            ]
        ];
        $context = stream_context_create($context_options);
        
        // Use wp_safe_remote_get instead of file_get_contents
        $response = wp_safe_remote_get($url, [
            'timeout' => $timeout,
            'stream' => true,
            'filename' => $temp_file,
            'sslverify' => true,
            'user-agent' => 'WordPress/' . get_bloginfo('version')
        ]);
        
        // Check for download errors
        if (is_wp_error($response)) {
            @unlink($temp_file); // Clean up
            $results['errors'][] = "Failed to download image: " . $response->get_error_message();
            continue;
        }
        
        // Check file size
        $filesize = filesize($temp_file);
        if ($filesize > $max_file_size || $filesize <= 0) {
            @unlink($temp_file); // Clean up
            $results['errors'][] = "File size invalid or exceeds maximum allowed size";
            continue;
        }
        
        // Verify MIME type (using PHP's fileinfo)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detected_mime = finfo_file($finfo, $temp_file);
        finfo_close($finfo);
        
        if (!in_array($detected_mime, $valid_mime_types, true)) {
            @unlink($temp_file); // Clean up
            $results['errors'][] = "Invalid file type detected: $detected_mime";
            continue;
        }
        
        // Additional image validation with getimagesize()
        $image_info = @getimagesize($temp_file);
        if ($image_info === false) {
            @unlink($temp_file); // Clean up
            $results['errors'][] = "Invalid image file detected";
            continue;
        }
        
        // Prepare for WordPress media library
        $upload_dir = wp_upload_dir();
        $filename = wp_unique_filename($upload_dir['path'], sanitize_file_name(basename($url)));
        $filepath = $upload_dir['path'] . '/' . $filename;
        
        // Move file from temp to uploads directory
        if (!@copy($temp_file, $filepath)) {
            @unlink($temp_file); // Clean up
            $results['errors'][] = "Failed to move uploaded file";
            continue;
        }
        @unlink($temp_file); // Clean up temp file
        
        // Set correct file permissions
        $stat = @stat(dirname($filepath));
        $perms = $stat['mode'] & 0000666;
        @chmod($filepath, $perms);
        
        // Create attachment post
        $filetype = wp_check_filetype($filename, null);
        $attachment = [
            'guid' => $upload_dir['url'] . '/' . $filename,
            'post_mime_type' => $filetype['type'],
            'post_title' => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
            'post_content' => '',
            'post_parent' => $post_id,
            'post_status' => 'inherit',
        ];
        
        // Insert attachment and generate metadata
        $attachment_id = wp_insert_attachment($attachment, $filepath, $post_id);
        if (is_wp_error($attachment_id)) {
            @unlink($filepath); // Clean up
            $results['errors'][] = "Failed to create attachment: " . $attachment_id->get_error_message();
            continue;
        }
        
        // Generate and update attachment metadata
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attachment_id, $filepath);
        wp_update_attachment_metadata($attachment_id, $attach_data);
        
        // Add to gallery images
        $gallery_images[] = $attachment_id;
        $results['success'][] = $attachment_id;
    }
    
    // Use the wpestate_upload_images_dashboard function to save images
    if (!empty($gallery_images)) {
        // Convert array to comma-separated string for the function
        $images_ids_string = implode(',', $gallery_images);
        
        // Use first image as thumbnail (you can modify this logic as needed)
        $property_thumb = !empty($gallery_images) ? $gallery_images[0] : '';
        
        // Call the wpestate function to handle image saving
        if (function_exists('wpestate_upload_images_dashboard')) {
            wpestate_upload_images_dashboard($post_id, $images_ids_string, $property_thumb, false);
        } else {
            // Fallback to original method if function doesn't exist
            update_post_meta($post_id, 'wpestate_property_gallery', $gallery_images);
            if (!empty($gallery_images)) {
                set_post_thumbnail($post_id, $gallery_images[0]);
            }
        }
    }
    
    return $results;
}