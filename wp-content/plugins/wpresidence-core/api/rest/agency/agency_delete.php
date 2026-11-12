<?php
/**
 * WPResidence Agency Deletion API Functions
 *
 * Functions for permanently removing agencies via the REST API
 * with proper validation and response handling.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Delete an agency.
 * 
 * Permanently removes an agency listing and all its associated data.
 * Validates the agency existence before deletion and provides
 * appropriate error responses for failure cases.
 *
 * @param WP_REST_Request $request The REST API request containing the agency ID.
 * @return WP_REST_Response|WP_Error Success response or error details.
 */
function wpresidence_delete_agency(WP_REST_Request $request) {
    return wpresidence_delete_entity($request, 'agency');
}

/**
 * Clean up user meta entries after agency deletion.
 * 
 * Updates WordPress user meta to reflect that the associated
 * agency post has been deleted.
 *
 * @param int $user_id The ID of the WordPress user linked to the deleted agency.
 * @return void
 */
function wpresidence_cleanup_agency_user_association($user_id) {
    // Make sure user exists
    $user = get_userdata($user_id);
    if (!$user) {
        return;
    }
    
    // Update user_agency_id meta to indicate no associated agency post
    update_user_meta($user_id, 'user_agency_id', '');
}