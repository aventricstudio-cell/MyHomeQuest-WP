<?php
/**
 * WPResidence User Role Assignment
 * Handles assigning and checking user roles
 *
 * @package WPResidence Core
 * @subpackage User
 * @since 4.0.0
 */


 
/**
 * Safely assigns a role to a user
 *
 * @param int    $user_id The ID of the user
 * @param string $role    The role to assign
 * @return bool  True if successful, false otherwise
 */
if(!function_exists('wpresidence_core_set_role_to_user')):
    function wpresidence_core_set_role_to_user($user_id, $role) {
        // Check nonce
        if (!check_admin_referer('wpresidence_role_assignment', 'role_nonce')) {
            return false;
        }

        // Input validation
        if (!is_numeric($user_id) || empty($role)) {
            return false;
        }

        // Security checks
        if (!current_user_can('edit_users')) {
            return false;
        }

        // Sanitize inputs
        $user_id = absint($user_id);
        $role = sanitize_key($role);

        // Verify role exists
        if (!get_role($role)) {
            return false;
        }

        // Prevent privilege escalation
        if (!current_user_can('promote_users') && $role === 'administrator') {
           
            return false;
        }

        do_action('wpresidence_before_role_assignment', $user_id, $role);

        $user = get_user_by('id', $user_id);
        if ($user) {
            $user->add_role($role);
            do_action('wpresidence_after_role_assignment', $user_id, $role);
            return true;
        }
        return false;
    }
endif;

/**
 * Checks if a user has a specific role
 *
 * @param int    $user_id The ID of the user
 * @param string $role    The role to check
 * @return bool  True if user has the role, false otherwise
 */
if(!function_exists('wpresidence_core_user_has_role')):
    function wpresidence_core_user_has_role($user_id, $role) {
        // Sanitize inputs
        $user_id = absint($user_id);
        $role = sanitize_key($role);
        
        // Get user
        $user = get_user_by('id', $user_id);
        if (!$user) {
            return false;
        }
        
        // Check role
        return in_array($role, (array) $user->roles, true);
    }
endif;




/**
 * Check if the current user has either the wpresidence_developer_role or wpresidence_agency_role.
 *
 * @return bool True if user has one of the roles, false otherwise.
 */
function is_wpresidence_developer_or_agency() {
    // Check if the user has the 'wpresidence_developer_role' or 'wpresidence_agency_role'
    return current_user_can('wpresidence_developer_role') || current_user_can('wpresidence_agency_role') || current_user_can('administrator');
}


/**
 * Check if the current user has one of the following roles:
 * wpresidence_developer_role, wpresidence_agency_role, or wpresidence_agent_role.
 *
 * @return bool True if user has any of the roles, false otherwise.
 */
function is_wpresidence_developer_agency_or_agent() {
    // Check if the user has developer, agency, or agent role
    return current_user_can('wpresidence_developer_role') ||
           current_user_can('wpresidence_agency_role') ||
           current_user_can('wpresidence_agent_role');
}


/**
 * Check if the current user has the wpresidence_developer_role.
 *
 * @return bool True if user has the developer role, false otherwise.
 */
function is_wpresidence_developer() {
    return current_user_can('wpresidence_developer_role');
}

/**
 * Check if the current user has the wpresidence_agency_role.
 *
 * @return bool True if user has the agency role, false otherwise.
 */
function is_wpresidence_agency() {
    return current_user_can('wpresidence_agency_role');
}

/**
 * Check if the current user has the wpresidence_agent_role.
 *
 * @return bool True if user has the agent role, false otherwise.
 */
function is_wpresidence_agent() {
    return current_user_can('wpresidence_agent_role');
}

/**
 * Get a mapping of user roles to their display names.
 *
 * @return array An associative array where keys are role slugs and values are display names.
 */
function wpresidence_rolemap()  {
    $roles_map = array( 
        'subscriber'               => esc_html__('User', 'wpresidence-core'), 
        'wpresidence_agent_role'  => esc_html__('Agent', 'wpresidence-core'), 
        'wpresidence_agency_role' => esc_html__('Agency', 'wpresidence-core'), 
        'wpresidence_developer_role' => esc_html__('Developer', 'wpresidence-core'),
    );

    return $roles_map;
}

/** * Convert user roles to post types for compatibility with WPResidence.
 *
 * @return array An array of user post types.
 */
function wpresidence_convert_user_roles_to_post_types( $roles = array() ) {

    if (!is_array($roles) || empty($roles)) {
        // If no roles are provided, use the default role map
        $roles = array_flip(wpresidence_rolemap());
    }



    $user_post_types = array();

    // Loop through each role and register it as a post type
    foreach ($roles as $role_slug => $role_name) {

        $postType = 'estate_agent';
        if ( $role_name == 'wpresidence_agency_role' ) {
            $postType = 'estate_agency';
        } elseif ( $role_name == 'wpresidence_developer_role' ) {
            $postType = 'estate_developer';
        }

        $user_post_types[$role_name] = $postType;
    }

    return $user_post_types;
        
}
