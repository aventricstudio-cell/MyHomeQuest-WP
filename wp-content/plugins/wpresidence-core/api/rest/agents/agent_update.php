<?php
/**
 * WPResidence Agent Update API Functions
 *
 * Functions for updating existing agents via the REST API,
 * including permission verification and data processing.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */







/**
 * Update an existing agent.
 * 
 * Processes and applies updates to an agent's data, including:
 * - Basic post data (title, content)
 * - Taxonomy terms
 * - Custom and standard meta fields
 * - Media attachments
 *
 * @param WP_REST_Request $request The REST API request containing the agent data.
 * @return WP_REST_Response|WP_Error Response object or error.
 */
function wpresidence_update_agent(WP_REST_Request $request) {
    // Get agent ID from request
    $agent_id = $request->get_param('id');
    
    // Parse and sanitize input data
    $input_data = wpresidence_parse_request_params($request);

    // Validate the agent ID
    $agent = get_post($agent_id);
    if (!$agent || $agent->post_type !== 'estate_agent') {
        return new WP_Error(
            'rest_agent_not_found',
            __('Agent not found.'),
            ['status' => 404]
        );
    }

    // Update the title if first_name and last_name are provided
    if (isset($input_data['first_name']) && isset($input_data['last_name'])) {
        $agent_title = $input_data['first_name'] . ' ' . $input_data['last_name'];
        wp_update_post([
            'ID'         => $agent_id,
            'post_title' => $agent_title,
            'post_content' => $input_data['agent_description'],
        ]);
    }


    // Process taxonomies and meta fields
    foreach ($input_data as $key => $value) {
        if (taxonomy_exists($key)) {
            // Handle taxonomy terms
            if (is_array($value)) {
                wp_set_object_terms($agent_id, $value, $key);
            }
        } else {
            // Handle special meta fields
            if ($key == 'agent_custom_data' && isset($input_data['agent_custom_data']) && is_array($input_data['agent_custom_data'])) {
                wpresidence_process_agent_custom_fields($agent_id, $input_data['agent_custom_data']);
            } else {
                // Standard meta field
                update_post_meta($agent_id, $key, $value);
            }
        }
    }
    
    // Process featured image
    if (!empty($input_data['featured_image'])) {
        wpresidence_set_agent_featured_image($agent_id, $input_data['featured_image']);
    }


    // Sync data to WordPress user if this agent is linked to a user
    $user_id = get_post_meta($agent_id, 'user_meda_id', true);
    if ($user_id && is_numeric($user_id)) {
        wpresidence_sync_agent_with_user($agent_id, intval($user_id), $input_data);
    }


    
    // Return success response
    return rest_ensure_response([
        'status'     => 'success',
        'agent_id'   => $agent_id,
        'message'    => __('Agent updated successfully.')
    ]);
}









/**
 * Sync agent data with the linked WordPress user.
 * 
 * Updates the WordPress user meta fields with corresponding
 * agent data to maintain consistency between the two.
 *
 * @param int $agent_id The ID of the agent post.
 * @param int $user_id The ID of the linked WordPress user.
 * @param array $data The agent data to sync.
 * @return bool True on success, false on failure.
 */
function wpresidence_sync_agent_with_user($agent_id, $user_id, $data) {
    // Ensure the user exists
    $user = get_userdata($user_id);
    if (!$user) {
        return false;
    }
    
    // Mapping of agent meta fields to user meta fields
    $meta_mapping = array(
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'agent_position' => 'title',
        'agent_phone' => 'phone',
        'agent_mobile' => 'mobile',
        'agent_skype' => 'skype',
        'agent_facebook' => 'facebook',
        'agent_twitter' => 'twitter',
        'agent_linkedin' => 'linkedin',
        'agent_pinterest' => 'pinterest',
        'agent_instagram' => 'instagram',
        'agent_youtube' => 'youtube',
        'agent_tiktok' => 'tiktok',
        'agent_telegram' => 'telegram',
        'agent_vimeo' => 'vimeo',
        'agent_website' => 'website',
        'agent_member' => 'agent_member',
        'agent_address' => 'agent_address',
        'agent_private_notes' => 'private_notes'
    );
    
    // Update user meta fields based on agent data
    foreach ($meta_mapping as $agent_key => $user_key) {
        if (isset($data[$agent_key])) {
            update_user_meta($user_id, $user_key, $data[$agent_key]);
        }
    }
    
    // Handle description/content separately
    if (isset($data['description'])) {
        update_user_meta($user_id, 'description', $data['description']);
    }
    
    // Update user email if provided (requires additional validation)
    if (isset($data['agent_email'])) {
        $new_email = sanitize_email($data['agent_email']);
        if (is_email($new_email)) {
            // Check if email is already in use by another user
            $existing_user = get_user_by('email', $new_email);
            if (!$existing_user || $existing_user->ID == $user_id) {
                // Email is available or belongs to this user
                wp_update_user(array(
                    'ID' => $user_id,
                    'user_email' => $new_email
                ));
            }
        }
    }
    
    // Handle featured image/profile picture
    $image_id = get_post_thumbnail_id($agent_id);
    if ($image_id) {
        $image_url = wp_get_attachment_url($image_id);
        if ($image_url) {
            update_user_meta($user_id, 'custom_picture', $image_url);
            update_user_meta($user_id, 'small_custom_picture', $image_id);
        }
    }
    
    return true;
}