<?php
/**
 * WPResidence Capabilities Management
 * This file handles the validation, sanitization, and management of capabilities
 * for the WPResidence custom roles (Agent, Agency, Developer).
 *
 * @package WPResidence Core
 * @subpackage User
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}
//error_log('loading some capabilities ');
/**
 * Validates if a capability name is legitimate and allowed
 *
 * @param string $capability The capability to validate
 * @return bool Whether the capability is valid
 */
if (!function_exists('wpresidence_is_valid_capability')):
    function wpresidence_is_valid_capability($capability) {
        // Basic sanitization
        $capability = sanitize_key($capability);
        
        // List of valid custom capabilities
        $valid_capabilities = array(
            // Property capabilities
            'edit_estate_property', 'read_estate_property', 'delete_estate_property',
            'edit_estate_properties', 'edit_others_estate_properties',
            'publish_estate_properties', 'read_private_estate_properties',
            'create_estate_properties', 'delete_estate_properties',
            'delete_private_estate_properties', 'delete_published_estate_properties',
            'delete_others_estate_properties', 'edit_private_estate_properties',
            'edit_published_estate_properties',
            
            // Agent capabilities
            'edit_estate_agent', 'read_estate_agent', 'delete_estate_agent',
            'edit_estate_agents', 'edit_others_estate_agents',
            'publish_estate_agents', 'read_private_estate_agents',
            'create_estate_agents', 'delete_estate_agents',
            'delete_private_estate_agents', 'delete_published_estate_agents',
            'delete_others_estate_agents', 'edit_private_estate_agents',
            'edit_published_estate_agents',

            // Agency capabilities
            'edit_estate_agency', 'read_estate_agency', 'delete_estate_agency',
            'edit_estate_agencies', 'edit_others_estate_agencies',
            'publish_estate_agencies', 'read_private_estate_agencies',
            'create_estate_agencies', 'delete_estate_agencies',
            'delete_private_estate_agencies', 'delete_published_estate_agencies',
            'delete_others_estate_agencies', 'edit_private_estate_agencies',
            'edit_published_estate_agencies',

            // Developer capabilities
            'edit_estate_developer', 'read_estate_developer', 'delete_estate_developer',
            'edit_estate_developers', 'edit_others_estate_developers',
            'publish_estate_developers', 'read_private_estate_developers',
            'create_estate_developers', 'delete_estate_developers',
            'delete_private_estate_developers', 'delete_published_estate_developers',
            'delete_others_estate_developers', 'edit_private_estate_developers',
            'edit_published_estate_developers',
            
            // Message capabilities
            'edit_wpestate_message', 'read_wpestate_message', 'delete_wpestate_message',
            'edit_wpestate_messages', 'publish_wpestate_messages',
            
            // Search capabilities
            'edit_wpestate_search', 'read_wpestate_search', 'delete_wpestate_search',
            'edit_wpestate_searches', 'publish_wpestate_searches',
            
            // Invoice capabilities (read-only)
            'read_wpestate_invoice', 'read_wpestate_invoices',
            
            // Taxonomy capabilities
            'manage_property_category', 'edit_property_category',
            'delete_property_category', 'assign_property_category',
            'manage_property_action_category', 'edit_property_action_category',
            'delete_property_action_category', 'assign_property_action_category',
            'manage_property_city', 'edit_property_city',
            'delete_property_city', 'assign_property_city',
            'manage_property_area', 'edit_property_area',
            'delete_property_area', 'assign_property_area',
            'manage_property_county_state', 'edit_property_county_state',
            'delete_property_county_state', 'assign_property_county_state',
            'manage_property_features', 'edit_property_features',
            'delete_property_features', 'assign_property_features',
            'manage_property_status', 'edit_property_status',
            'delete_property_status', 'assign_property_status'
        );
        
        // Check if capability is in our whitelist
        if (in_array($capability, $valid_capabilities, true)) {
            return true;
        }
        
        // Check if it's a valid WordPress core capability
        $wp_roles = wp_roles();
        foreach ($wp_roles->roles as $role) {
            if (isset($role['capabilities'][$capability])) {
                return true;
            }
        }
        
        return false;
    }
endif;

/**
 * Sanitizes and validates an array of capabilities
 *
 * @param array $capabilities Array of capabilities to validate
 * @return array Sanitized and validated capabilities
 */
if (!function_exists('wpresidence_sanitize_capabilities')):
    function wpresidence_sanitize_capabilities($capabilities) {
        if (!is_array($capabilities)) {
            return array();
        }

        $sanitized = array();
        foreach ($capabilities as $cap => $grant) {
            $cap = sanitize_key($cap);
            
            if (wpresidence_is_valid_capability($cap)) {
                $sanitized[$cap] = (bool) $grant;
            } else {
              /*  error_log(sprintf(
                    'WPResidence: Invalid capability "%s" attempted to be assigned',
                    esc_html($cap)
                ));
                */
            }
        }
        
        return $sanitized;
    }
endif;

/**
 * Rate limiting for role modifications
 *
 * @param int $user_id User ID attempting the modification
 * @return bool Whether the action should be allowed
 */
if (!function_exists('wpresidence_check_role_modification_limit')):
    function wpresidence_check_role_modification_limit($user_id) {
        $count = get_transient('wpestate_wpresidence_role_mod_count_' . $user_id);
        
        if ($count > 100) { // Max 100 modifications per hour
            /*error_log(sprintf(
                'WPResidence: Rate limit exceeded for user %d',
                $user_id
            ));
            */
            return false;
        }
        
        set_transient('wpestate_wpresidence_role_mod_count_' . $user_id, ($count ? $count + 1 : 1), HOUR_IN_SECONDS);
        return true;
    }
endif;

/**
 * Verifies if current user can modify roles
 *
 * @return bool True if user can modify roles, false otherwise
 */
if(!function_exists('wpresidence_can_modify_roles')):
    function wpresidence_can_modify_roles() {
        if (!current_user_can('edit_users')) {
            /*error_log(sprintf(
                'WPResidence: Unauthorized role modification attempt by user %d',
                get_current_user_id()
            ));
            */
            return false;
        }

        if (!wpresidence_check_role_modification_limit(get_current_user_id())) {
           
            return false;
        }

        return true;
    }
endif;

/**
 * Handle failed role modifications
 *
 * @param string $message Error message
 * @param string $code Error code
 * @return void
 */
if(!function_exists('wpresidence_handle_role_error')):
    function wpresidence_handle_role_error($message, $code = '') {
        /*error_log(sprintf(
            'WPResidence Role Error: %s (Code: %s) by user %d',
            $message,
            $code,
            get_current_user_id()
        ));*/

        if (wp_doing_ajax()) {
            wp_send_json_error(array(
                'message' => $message,
                'code' => $code
            ));
        } else {
            set_transient(
                'wpestate_wpresidence_role_error_' . get_current_user_id(),
                array(
                    'message' => $message,
                    'code' => $code
                ),
                60 // Expire after 1 minute
            );
        }
    }
endif;