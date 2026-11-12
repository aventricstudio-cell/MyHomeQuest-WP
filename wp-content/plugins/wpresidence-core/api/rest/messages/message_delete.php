<?php
/**
 * WPResidence Message Deletion API Functions
 *
 * Functions for managing message deletion via the REST API.
 * Messages are typically not completely removed but marked as deleted
 * for one or both participants.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Delete a message.
 * 
 * Marks a message as deleted for the current user.
 * The message is only permanently removed when both sender
 * and recipient have deleted it.
 *
 * @param WP_REST_Request $request The REST API request containing the message ID.
 * @return WP_REST_Response|WP_Error Success response or error details.
 */
function wpresidence_delete_message(WP_REST_Request $request) {
    // Get message ID from request
    $message_id = $request->get_param('id');
    
    // Get current user ID
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    
    // Validate the message exists
    $message = get_post($message_id);
    if (!$message || $message->post_type !== 'wpestate_message') {
        return new WP_Error(
            'rest_message_not_found',
            __('Message not found.'),
            ['status' => 404]
        );
    }
    
    // Get message participants
    $from_user_id = get_post_meta($message_id, 'message_from_user', true);
    $to_user_id = get_post_meta($message_id, 'message_to_user', true);
    
    // Track who's deleting the message
    $is_sender = (intval($from_user_id) === intval($user_id));
    $is_recipient = (intval($to_user_id) === intval($user_id));
    
    // Ensure user has permission to delete this message
    if (!$is_sender && !$is_recipient) {
        return new WP_Error(
            'rest_forbidden',
            __('You do not have permission to delete this message.'),
            ['status' => 403]
        );
    }
    
    // Mark deleted for current user
    update_post_meta($message_id, 'delete_destination'.$user_id, 1);
    
    // If message was unread, update unread count
    $mess_status = get_post_meta($message_id, 'message_status'.$user_id, true);
    if($mess_status !== 'read'){
        $unread = abs(intval(get_user_meta($user_id, 'unread_mess', true)) - 1);
        update_user_meta($user_id, 'unread_mess', $unread);
    }
    
    // Check if both users have deleted the message
    $delete_from_user = get_post_meta($message_id, 'delete_destination'.$from_user_id, true);
    $delete_to_user = get_post_meta($message_id, 'delete_destination'.$to_user_id, true);
    
    // If both users have deleted, completely remove the message and all replies
    if($delete_from_user == 1 && $delete_to_user == 1){
        // Query for all child messages (replies)
        $args_child = array(
            'post_type'         => 'wpestate_message',
            'posts_per_page'    => -1,
            'post_parent'       => $message_id,
        );
        
        // Delete all child messages first
        $message_selection_child = new WP_Query($args_child);
        while ($message_selection_child->have_posts()):
            $message_selection_child->the_post();
            $delete_id = get_the_ID();
            wp_delete_post($delete_id, true); // Permanently delete
        endwhile;
        
        // Clean up query
        wp_reset_query();
        wp_reset_postdata();
        
        // Delete the parent message
        wp_delete_post($message_id, true); // Permanently delete
        
        // Return success response for permanent deletion
        return rest_ensure_response([
            'status'  => 'success',
            'message' => __('Message permanently deleted.')
        ]);
    }
    
    // Return success response for marked as deleted
    return rest_ensure_response([
        'status'  => 'success',
        'message' => __('Message deleted successfully.')
    ]);
}