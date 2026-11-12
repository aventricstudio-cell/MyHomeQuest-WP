<?php
/**
 * WPResidence Developer Deletion API Functions
 *
 * Functions for permanently removing developers via the REST API
 * with proper validation and response handling.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Delete a developer.
 * 
 * Permanently removes a developer listing and all its associated data.
 * Validates the developer existence before deletion and provides
 * appropriate error responses for failure cases.
 *
 * @param WP_REST_Request $request The REST API request containing the developer ID.
 * @return WP_REST_Response|WP_Error Success response or error details.
 */
function wpresidence_delete_developer(WP_REST_Request $request) {
    return wpresidence_delete_entity($request, 'developer');
}

/**
 * Clean up user meta entries after developer deletion.
 * 
 * Updates WordPress user meta to reflect that the associated
 * developer post has been deleted.
 *
 * @param int $user_id The ID of the WordPress user linked to the deleted developer.
 * @return void
 */
function wpresidence_cleanup_developer_user_association($user_id) {
    // Make sure user exists
    $user = get_userdata($user_id);
    if (!$user) {
        return;
    }
    
    // Update user_developer_id meta to indicate no associated developer post
    update_user_meta($user_id, 'user_developer_id', '');
}