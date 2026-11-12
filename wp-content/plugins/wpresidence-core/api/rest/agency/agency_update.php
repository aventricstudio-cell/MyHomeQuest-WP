<?php
/**
 * WPResidence Agency Update API Functions
 *
 * Functions for updating existing agencies via the REST API,
 * including permission verification and data processing.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Update an existing agency.
 * 
 * Processes and applies updates to an agency's data, including:
 * - Basic post data (title, content)
 * - Taxonomy terms
 * - Custom and standard meta fields
 * - Media attachments
 *
 * @param WP_REST_Request $request The REST API request containing the agency data.
 * @return WP_REST_Response|WP_Error Response object or error.
 */
function wpresidence_update_agency(WP_REST_Request $request) {
    // Get agency ID from request
    $agency_id = $request->get_param('id');
    
    // Parse and sanitize input data
    $input_data = wpresidence_parse_request_params($request);

    // Validate the agency ID
    $agency = get_post($agency_id);
    if (!$agency || $agency->post_type !== 'estate_agency') {
        return new WP_Error(
            'rest_agency_not_found',
            __('Agency not found.'),
            ['status' => 404]
        );
    }

    // Update the title if agency_name is provided
    if (isset($input_data['agency_name'])) {
        wp_update_post([
            'ID'         => $agency_id,
            'post_title' => $input_data['agency_name'],
            'post_content' => isset($input_data['agency_description']) ? $input_data['agency_description'] : $agency->post_content,
        ]);
    }

    // Process taxonomies and meta fields
    foreach ($input_data as $key => $value) {
        if (taxonomy_exists($key)) {
            // Handle taxonomy terms
            if (is_array($value)) {
                wp_set_object_terms($agency_id, $value, $key);
            }
        } else {
            // Handle special meta fields
            if ($key == 'agency_custom_data' && isset($input_data['agency_custom_data']) && is_array($input_data['agency_custom_data'])) {
                wpresidence_process_agency_custom_fields($agency_id, $input_data['agency_custom_data']);
            } else {
                // Standard meta field
                update_post_meta($agency_id, $key, $value);
            }
        }
    }
    
    // Process featured image
    if (!empty($input_data['featured_image'])) {
        wpresidence_set_agency_featured_image($agency_id, $input_data['featured_image']);
    }

    // Sync data to WordPress user if this agency is linked to a user
    $user_id = get_post_meta($agency_id, 'user_meda_id', true);
    if ($user_id && is_numeric($user_id)) {
        wpresidence_sync_agency_with_user($agency_id, intval($user_id), $input_data);
    }
    
    // Return success response
    return rest_ensure_response([
        'status'     => 'success',
        'agency_id'   => $agency_id,
        'message'    => __('Agency updated successfully.')
    ]);
}

/**
 * Sync agency data with the linked WordPress user.
 * 
 * Updates the WordPress user meta fields with corresponding
 * agency data to maintain consistency between the two.
 *
 * @param int $agency_id The ID of the agency post.
 * @param int $user_id The ID of the linked WordPress user.
 * @param array $data The agency data to sync.
 * @return bool True on success, false on failure.
 */
function wpresidence_sync_agency_with_user($agency_id, $user_id, $data) {
    // Ensure the user exists
    $user = get_userdata($user_id);
    if (!$user) {
        return false;
    }
    
    // Get the featured image
    $image_id = get_post_thumbnail_id($agency_id);
    $full_img = $image_id ? wp_get_attachment_image_src($image_id, 'property_listings') : null;
    
    // Basic fields mapping - agency field name => user meta field name
    $fields_map = [
        'agency_phone' => 'phone',
        'agency_mobile' => 'mobile',
        'agency_description' => 'description',
        'agency_skype' => 'skype',
        'agency_address' => 'title',
        'agency_facebook' => 'facebook',
        'agency_twitter' => 'twitter', 
        'agency_linkedin' => 'linkedin',
        'agency_pinterest' => 'pinterest',
        'agency_instagram' => 'instagram',
        'agency_website' => 'website',
        'agency_opening_hours' => 'agency_opening_hours'
    ];
    
    // Update all mapped fields
    foreach ($fields_map as $agency_key => $user_key) {
        if (isset($data[$agency_key])) {
            update_user_meta($user_id, $user_key, $data[$agency_key]);
        }
    }
    
    // Handle image fields
    if ($full_img) {
        update_user_meta($user_id, 'aim', '/'.$full_img[0].'/');
        update_user_meta($user_id, 'custom_picture', $full_img[0]);
        update_user_meta($user_id, 'small_custom_picture', $image_id);
    }
    
    // Update user email if provided and valid
    if (isset($data['agency_email'])) {
        $new_email = sanitize_email($data['agency_email']);
        if (is_email($new_email)) {
            $existing_user = get_user_by('email', $new_email);
            if (!$existing_user || $existing_user->ID == $user_id) {
                wp_update_user(['ID' => $user_id, 'user_email' => $new_email]);
            }
        }
    }
    
    return true;
}