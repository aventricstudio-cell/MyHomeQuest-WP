<?php
/**
 * WPResidence User Role Management
 * This file handles the creation, modification, and removal of custom user roles
 * and their associated capabilities with comprehensive security measures.
 *
 * @package WPResidence Core
 * @subpackage User
 * @since 4.0.0
 */


if (!defined('ABSPATH')) {
    exit;
}


// Define role constants
define('WPRESIDENCE_ROLE_AGENT', 'wpresidence_agent_role');
define('WPRESIDENCE_ROLE_AGENCY', 'wpresidence_agency_role');
define('WPRESIDENCE_ROLE_DEVELOPER', 'wpresidence_developer_role');





function wpresidence_plugin_activated() {
    do_action('wpestate_residence_plugin_activated');
}



/**
 * Creates the custom user roles: Agent, Agency and Developer with proper security measures
 * Runs on plugin activation
 *
 * @return void
 */
add_action('wpestate_residence_plugin_activated', 'wpresidence_create_custom_roles');

if (!function_exists('wpresidence_create_custom_roles')):
    function wpresidence_create_custom_roles() {
        global $wp_roles;
        if (empty($wp_roles) || !($subscriber = get_role('subscriber'))) {
        
            return;
        }

        do_action('wpresidence_before_roles_creation');
      

        // Start with base subscriber capabilities
        $base_capabilities = $subscriber->capabilities;
        
        // Get role-specific capabilities
        $roles = wpresidence_get_role_capabilities();

        foreach ($roles as $role_name => $custom_caps) {
            if (!get_role($role_name)) {
                $display_name = 'WpResidence ' . ucfirst(str_replace(array('wpresidence_', '_role'), '', $role_name));
              

                $role = add_role($role_name, $display_name, $base_capabilities);
                if ($role) {
                    foreach ($custom_caps as $cap => $grant) {
                        $role->add_cap($cap, $grant);
                    }
                  
                } else {
                  
                }
            } else {
               
            }
        }

        do_action('wpresidence_after_roles_creation');
    }
endif;





/**
 * Removes user roles
 */

if(!function_exists('wpresidence_remove_user_role')):
    function wpresidence_remove_user_role() {
    
        do_action('wpresidence_before_roles_removal');

        $roles = array(
            WPRESIDENCE_ROLE_AGENT,
            WPRESIDENCE_ROLE_AGENCY,
            WPRESIDENCE_ROLE_DEVELOPER
        );

        foreach ($roles as $role) {
          
            $role_obj = get_role($role);
            if ($role_obj) {
                foreach (array_keys($role_obj->capabilities) as $cap) {
                    $role_obj->remove_cap($cap);
                }
                remove_role($role);
         
            } else {
               // error_log('WP Residence: Role not found for removal: ' . $role);
            }
        }
        
   
        global $wpdb;
        foreach ($roles as $role) {
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM $wpdb->usermeta 
                     WHERE meta_key = %s 
                     AND meta_value = %s",
                    $wpdb->prefix . 'capabilities',
                    $role
                )
            );
        }

   
        do_action('wpresidence_after_roles_removal');
    }
endif;





/**
 * Assigns a role to a new user
 */
if(!function_exists('wpresidence_register_user_role')):
    function wpresidence_register_user_role($user_id, $role) {
     
        if (!is_numeric($user_id) || empty($role)) {
       
            return false;
        }

        $user_id = absint($user_id);
        $role = sanitize_key($role);

        $allowed_roles = array(
            'subscriber',
            WPRESIDENCE_ROLE_AGENT,
            WPRESIDENCE_ROLE_AGENCY,
            WPRESIDENCE_ROLE_DEVELOPER
        );
        
        if (!get_role($role) || !in_array($role, $allowed_roles, true)) {
   
            return false;
        }

        $user = get_user_by('id', $user_id);
        if (!$user) {
 
            return false;
        }
        
  
        $existing_roles = (array) $user->roles;
        foreach ($existing_roles as $existing_role) {
            $user->remove_role($existing_role);
        }

    
        $user->add_role($role);
        
     
        return true;
    }
endif;

/**
 * Recovery function for role capabilities
 */
if(!function_exists('wpresidence_repair_role_capabilities')):
    function wpresidence_repair_role_capabilities() {

        
        if (!current_user_can('manage_options') || 
            !check_admin_referer('wpresidence_repair_roles', 'repair_nonce')) {
         
            return;
        }

        $roles = array(
            WPRESIDENCE_ROLE_AGENT,
            WPRESIDENCE_ROLE_AGENCY,
            WPRESIDENCE_ROLE_DEVELOPER
        );
        
        foreach ($roles as $role_name) {
          
            $role = get_role($role_name);
            if (!$role) {
            
                continue;
            }

         
            $existing_caps = array_keys($role->capabilities);
            foreach ($existing_caps as $cap) {
                $role->remove_cap($cap);
            }
        }

   
        wpresidence_create_custom_roles();
    }
endif;

/**
 * Returns capabilities for each role
 */
if(!function_exists('wpresidence_get_role_capabilities')):
    function wpresidence_get_role_capabilities() {
    
        
        // Common capabilities for all roles
        $common_caps = array(
            'edit_estate_property' => true,
            'read_estate_property' => true,
            'delete_estate_property' => true,
            'edit_estate_properties' => true,
            'edit_published_estate_properties' => true,
            'publish_estate_properties' => true,
            'delete_estate_properties' => true,
            'delete_published_estate_properties' => true,
            
            'edit_wpestate_message' => true,
            'read_wpestate_message' => true,
            'delete_wpestate_message' => true,
            'edit_wpestate_messages' => true,
            'publish_wpestate_messages' => true,
            'read_private_wpestate_messages' => true,
            'delete_wpestate_messages' => true,
            
            'edit_wpestate_search' => true,
            'read_wpestate_search' => true,
            'delete_wpestate_search' => true,
            'edit_wpestate_searches' => true,
            'publish_wpestate_searches' => true,
            'read_private_wpestate_searches' => true,
            'delete_wpestate_searches' => true,
            
            'read_wpestate_invoice' => true,
            'read_private_wpestate_invoices' => true,
        );

        // Agency capabilities
        $agency_caps = array_merge($common_caps, array(
            'edit_estate_agent' => true,
            'read_estate_agent' => true,
            'delete_estate_agent' => true,
            'edit_estate_agents' => true,
            'publish_estate_agents' => true,
            'edit_published_estate_agents' => true,
            'delete_estate_agents' => true,
        ));

        // Developer capabilities
        $developer_caps = array_merge($agency_caps, array(
            'edit_estate_developer' => true,
            'read_estate_developer' => true,
            'delete_estate_developer' => true,
            'edit_estate_developers' => true,
            'publish_estate_developers' => true,
            'edit_published_estate_developers' => true,
            'delete_estate_developers' => true,
        ));

     
        return array(
            WPRESIDENCE_ROLE_AGENT => $common_caps,
            WPRESIDENCE_ROLE_AGENCY => $agency_caps,
            WPRESIDENCE_ROLE_DEVELOPER => $developer_caps
        );
    }
endif;



/**
 * Ensure proper nonce field output
 */
add_action('admin_footer', 'wpresidence_output_role_nonce');
if(!function_exists('wpresidence_output_role_nonce')):
    function wpresidence_output_role_nonce() {
        if (current_user_can('edit_users')) {
            wp_nonce_field('wpresidence_role_assignment', 'role_nonce');
         
        }
    }
endif;