<?php
/**
 * WPResidence Agent Deletion API Functions
 *
 * Functions for permanently removing agents via the REST API
 * with proper validation and response handling.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Delete an agent.
 * 
 * Permanently removes an agent listing and all its associated data.
 * Validates the agent existence before deletion and provides
 * appropriate error responses for failure cases.
 *
 * @param WP_REST_Request $request The REST API request containing the agent ID.
 * @return WP_REST_Response|WP_Error Success response or error details.
 */
function wpresidence_delete_agent(WP_REST_Request $request) {
    return wpresidence_delete_entity($request, 'agent');
}
/**
 * Clean up user meta entries after agent deletion.
 * 
 * Updates WordPress user meta to reflect that the associated
 * agent post has been deleted.
 *
 * @param int $user_id The ID of the WordPress user linked to the deleted agent.
 * @return void
 */
function wpresidence_cleanup_agent_user_association($user_id) {
    // Make sure user exists
    $user = get_userdata($user_id);
    if (!$user) {
        return;
    }
    
    // Update user_agent_id meta to indicate no associated agent post
    update_user_meta($user_id, 'user_agent_id', '');
}

/**
 * Update an agency's agent list after agent deletion.
 * 
 * Removes an agent from an agency's list of associated agents
 * or adds an agent to the list.
 *
 * @param int $agency_id The ID of the agency user.
 * @param int $agent_id The ID of the agent user.
 * @param string $action Either 'add' or 'remove' to specify the operation.
 * @return void
 */
function wpresidence_update_agency_agent_list($agency_id, $agent_id, $action = 'remove') {
    // Skip if either ID is not valid
    if (!$agency_id || !$agent_id) {
        return;
    }
    
    // Get current list of agents for this agency
    $current_agent_list = get_user_meta($agency_id, 'current_agent_list', true);
    $agent_list = is_array($current_agent_list) ? $current_agent_list : array();
    
    if ($action === 'remove') {
        // Remove agent from list
        if (($key = array_search($agent_id, $agent_list)) !== false) {
            unset($agent_list[$key]);
        }
    } else if ($action === 'add') {
        // Add agent to list if not already present
        if (!in_array($agent_id, $agent_list)) {
            $agent_list[] = $agent_id;
        }
    }
    
    // Ensure list is unique and reindex array
    $agent_list = array_values(array_unique($agent_list));
    
    // Update agency's agent list
    update_user_meta($agency_id, 'current_agent_list', $agent_list);
}