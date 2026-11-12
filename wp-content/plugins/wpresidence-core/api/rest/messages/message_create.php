<?php
/**
 * WPResidence Message Creation API Functions
 *
 * Functions for creating new messages via the REST API,
 * including data validation and message handling.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Create a new message.
 * 
 * Creates a message post with associated metadata.
 * Validates required fields and handles message tracking.
 *
 * @param WP_REST_Request $request The REST API request containing the message data.
 * @return WP_REST_Response|WP_Error The response or error.
 */
function wpresidence_create_message(WP_REST_Request $request) {
    // Parse and sanitize input data
    $input_data = wpresidence_parse_request_params($request);

    // Get current user ID
    $current_user = wp_get_current_user();
    $from_user_id = $current_user->ID;
    
    // Validate required fields
    $required_fields = ['to_user', 'subject', 'message'];
    foreach ($required_fields as $field) {
        if (empty($input_data[$field])) {
            return new WP_Error(
                'rest_missing_field',
                __('Missing required field: ' . $field),
                ['status' => 400]
            );
        }
    }
    
    // Get recipient user ID
    $to_user_id = intval($input_data['to_user']);
    
    // Validate recipient exists
    $to_user = get_user_by('id', $to_user_id);
    if (!$to_user) {
        return new WP_Error(
            'rest_invalid_recipient',
            __('Invalid recipient user ID.'),
            ['status' => 400]
        );
    }
    
    // Format subject line with sender information
    $subject = '';
    if (!empty($input_data['subject'])) {
        $subject = $input_data['subject'] . ' ' . $current_user->user_login;
    } else {
        $subject = esc_html__('Message from ', 'wpresidence-core') . $current_user->user_login;
    }
    
    // Create the message post
    $post = array(
        'post_title'   => esc_html__('Message from ', 'wpresidence-core') . $current_user->user_login,
        'post_content' => $input_data['message'],
        'post_status'  => 'publish',
        'post_type'    => 'wpestate_message',
        'post_author'  => $from_user_id
    );
    $post_id = wp_insert_post($post);
    
    // Handle post creation errors
    if (is_wp_error($post_id)) {
        return $post_id;
    }
    
    // Set up message metadata
    update_post_meta($post_id, 'mess_status', 'new');
    update_post_meta($post_id, 'message_from_user', $from_user_id);
    update_post_meta($post_id, 'message_to_user', $to_user_id);
    
    // Increase unread message count for recipient
    wpestate_increment_mess_mo($to_user_id);
    
    // Initialize deletion flags for both users
    update_post_meta($post_id, 'delete_destination' . $from_user_id, 0);
    update_post_meta($post_id, 'delete_destination' . $to_user_id, 0);
    update_post_meta($post_id, 'message_status', 'unread');
    update_post_meta($post_id, 'delete_source', 0);
    update_post_meta($post_id, 'delete_destination', 0);
    
    // Set first_content flag if this is a new conversation
    $first_content = $input_data['first_content'] ?? '';
    if ($first_content != '') {
        update_post_meta($post_id, 'first_content', 1);
        update_post_meta($post_id, 'message_status' . $to_user_id, 'unread');
    }
    
    // Return success response
    return rest_ensure_response([
        'status'     => 'success',
        'message_id' => $post_id,
        'message'    => __('Message sent successfully.')
    ]);
}