<?php
/**
 * WPResidence Capability Management
 * Handles admin access and capability checks
 * Complements the user role system defined in users.php
 * 
 * @package WPResidence Core
 * @subpackage User
 * @since 4.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define default manageable post types for user roles
 * This is a helper function that can be used by the role creation system
 */
if (!function_exists('wpresidence_get_role_post_types')):
    function wpresidence_get_role_post_types() {
        $default_post_types = array(
            'estate_property',
            'estate_agent',
            'estate_developer',
            'wpestate_message',
            'wpestate_search',
            'wpestate_invoice'
        );

        return apply_filters('wpresidence_role_post_types', $default_post_types);
    }
endif;

/**
 * Filter to display the admin bar only for administrators
 */
add_filter('show_admin_bar', function($show) {
    return current_user_can('administrator');
});

/**
 * Restrict access to the WordPress admin area
 * Only administrators and editors can access the admin panel
 */
add_action('admin_init', function () {
    $allowed_roles = array('administrator', 'editor');
    
    $user = wp_get_current_user();
    $user_roles = (array) $user->roles;
    
    $has_access = array_intersect($allowed_roles, $user_roles);
    
    if (empty($has_access) && !defined('DOING_AJAX')) {
        wp_redirect(home_url());
        exit;
    }
});


