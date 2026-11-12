<?php
/**
 * WPResidence Developer Update API Functions
 *
 * Functions for updating existing developers via the REST API,
 * including permission verification and data processing.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Update an existing developer.
 * 
 * Processes and applies updates to a developer's data, including:
 * - Basic post data (title, content)
 * - Taxonomy terms
 * - Custom and standard meta fields
 * - Media attachments
 *
 * @param WP_REST_Request $request The REST API request containing the developer data.
 * @return WP_REST_Response|WP_Error Response object or error.
 */
function wpresidence_update_developer(WP_REST_Request $request) {
    // Get developer ID from request
    $developer_id = $request->get_param('id');
    
    // Parse and sanitize input data
    $input_data = wpresidence_parse_request_params($request);

    // Validate the developer ID
    $developer = get_post($developer_id);
    if (!$developer || $developer->post_type !== 'estate_developer') {
        return new WP_Error(
            'rest_developer_not_found',
            __('Developer not found.'),
            ['status' => 404]
        );
    }

    // Update the title if developer_name is provided
    if (isset($input_data['developer_name'])) {
        wp_update_post([
            'ID'         => $developer_id,
            'post_title' => $input_data['developer_name'],
            'post_content' => isset($input_data['developer_description']) ? $input_data['developer_description'] : $developer->post_content,
        ]);
    }

    // Process taxonomies and meta fields
    foreach ($input_data as $key => $value) {
        if (taxonomy_exists($key)) {
            // Handle taxonomy terms
            if (is_array($value)) {
                wp_set_object_terms($developer_id, $value, $key);
            }
        } else {
            // Handle special meta fields
            if ($key == 'developer_custom_data' && isset($input_data['developer_custom_data']) && is_array($input_data['developer_custom_data'])) {
                wpresidence_process_developer_custom_fields($developer_id, $input_data['developer_custom_data']);
            } else {
                // Standard meta field
                update_post_meta($developer_id, $key, $value);
            }
        }
    }
    
    // Process featured image
    if (!empty($input_data['featured_image'])) {
        wpresidence_set_developer_featured_image($developer_id, $input_data['featured_image']);
    }

    // Sync data to WordPress user if this developer is linked to a user
    $user_id = get_post_meta($developer_id, 'user_meda_id', true);
    if ($user_id && is_numeric($user_id)) {
        wpresidence_sync_developer_with_user($developer_id, intval($user_id), $input_data);
    }
    
    // Return success response
    return rest_ensure_response([
        'status'     => 'success',
        'developer_id'   => $developer_id,
        'message'    => __('Developer updated successfully.')
    ]);
}

/**
 * Sync developer data with the linked WordPress user.
 * 
 * Updates the WordPress user meta fields with corresponding
 * developer data to maintain consistency between the two.
 *
 * @param int $developer_id The ID of the developer post.
 * @param int $user_id The ID of the linked WordPress user.
 * @param array $data The developer data to sync.
 * @return bool True on success, false on failure.
 */
function wpresidence_sync_developer_with_user($developer_id, $user_id, $data) {
    // Ensure the user exists
    $user = get_userdata($user_id);
    if (!$user) {
        return false;
    }
    
    // Get the featured image
    $image_id = get_post_thumbnail_id($developer_id);
    $full_img = $image_id ? wp_get_attachment_image_src($image_id, 'property_listings') : null;
    
    // Basic fields mapping - developer field name => user meta field name
    $fields_map = [
        'developer_phone' => 'phone',
        'developer_mobile' => 'mobile',
        'developer_description' => 'description',
        'developer_skype' => 'skype',
        'developer_address' => 'title',
        'developer_facebook' => 'facebook',
        'developer_twitter' => 'twitter', 
        'developer_linkedin' => 'linkedin',
        'developer_pinterest' => 'pinterest',
        'developer_instagram' => 'instagram',
        'developer_website' => 'website',
        'developer_license' => 'developer_license',
        'developer_languages' => 'developer_languages',
        'developer_taxes' => 'developer_taxes',
        'developer_opening_hours' => 'developer_opening_hours'
    ];
    
    // Update all mapped fields
    foreach ($fields_map as $developer_key => $user_key) {
        if (isset($data[$developer_key])) {
            update_user_meta($user_id, $user_key, $data[$developer_key]);
        }
    }
    
    // Handle image fields
    if ($full_img) {
        update_user_meta($user_id, 'aim', '/'.$full_img[0].'/');
        update_user_meta($user_id, 'custom_picture', $full_img[0]);
        update_user_meta($user_id, 'small_custom_picture', $image_id);
    }
    
    // Update user email if provided and valid
    if (isset($data['developer_email'])) {
        $new_email = sanitize_email($data['developer_email']);
        if (is_email($new_email)) {
            $existing_user = get_user_by('email', $new_email);
            if (!$existing_user || $existing_user->ID == $user_id) {
                wp_update_user(['ID' => $user_id, 'user_email' => $new_email]);
            }
        }
    }
    
    return true;
}