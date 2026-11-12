<?php
/**
 * WP Residence Menu Optimization Functions
 * 
 * This file contains optimized menu rendering functions that improve site performance
 * by implementing transient caching and reducing database queries.
 *
 * @package WP Residence
 * @subpackage Performance
 * @since 4.0.0
 */



/**
 * Reset primary menu transient when navigation menu is updated
 * 
 * This anonymous function is hooked to the wp_update_nav_menu action
 * and clears the primary menu cache when a menu is edited in the admin.
 */
add_action('wp_update_nav_menu', function() {
    delete_transient('wpresidence_primary_menu_complete');
    if (defined('ICL_LANGUAGE_CODE')) {
        global $sitepress;
        if (isset($sitepress)) {
            $languages = $sitepress->get_active_languages();
            foreach ($languages as $lang_code => $language) {
                delete_transient('wpresidence_primary_menu_complete_' . $lang_code);
            }
        }
    }


    
});





/**
 * Optimized secondary navigation menu with caching and metadata preloading
 * 
 * Renders the secondary navigation menu (used in header type 6) with performance optimizations:
 * - Uses transient caching to store the complete HTML output
 * - Preloads menu item metadata to reduce individual database queries
 * - Maintains compatibility with existing theme hooks and filters
 *
 * @param string $classes CSS classes to apply to the navigation menu
 * @since 4.0.0
 */
if(!function_exists('wpresidence_display_secondary_nav_menu')):
    function wpresidence_display_secondary_nav_menu($classes) {
        // Create transient name with language support
        if ( function_exists( 'wpestate_set_transient_name_multilang' ) ) {
            $transient_name = wpestate_set_transient_name_multilang( 'wpestate_wpresidence_secondary_menu_complete' );
        } else {
            $transient_name = 'wpestate_wpresidence_secondary_menu_complete';
        }


        // Try to get cached menu
        $secondary_menu = get_transient($transient_name);
        
        if ($secondary_menu === false) {
            ob_start();
            
            // Apply filters and actions as in the original function
            $classes = apply_filters('wpresidence_secondary_nav_menu_classes', $classes);
            do_action('wpresidence_before_secondary_nav_menu', $classes);
            
            // Output the nav opening tag
            echo '<nav class="wpresidence-navigation-menu ' . esc_attr($classes) . '">';
            
            // Check if a secondary menu location is defined
            if (has_nav_menu('header_6_second_menu')) {
                // Get menu items and preload metadata to avoid slow queries
                $locations = get_nav_menu_locations();
                if (isset($locations['header_6_second_menu'])) {
                    $menu_id = $locations['header_6_second_menu'];
                    $menu_object = wp_get_nav_menu_object($menu_id);
                    
                    if ($menu_object) {
                        // Get menu items with minimal overhead
                        $menu_items = wp_get_nav_menu_items($menu_object->term_id, array(
                            'update_post_term_cache' => false,
                        ));
                        
                        // Preload all menu item metadata at once
                        if (!empty($menu_items)) {
                            $menu_item_ids = wp_list_pluck($menu_items, 'ID');
                            update_postmeta_cache($menu_item_ids);
                        }
                        
                        // Original menu args
                        $nav_menu_args = apply_filters('wpresidence_secondary_nav_menu_args', array(
                            'theme_location' => 'header_6_second_menu',
                            'walker'         => new wpestate_custom_walker,
                            'container_class'=> 'menu-mega-menu-updated-container'
                        ));
                        
                        wp_nav_menu($nav_menu_args);
                    }
                }
            }
            
            // Close the nav tag
            print '</nav><!-- end .wpresidence-navigation-menu -->';
            
            // Run the after action
            do_action('wpresidence_after_secondary_nav_menu', $classes);
            
            // Get the buffered output and cache it
            $secondary_menu = ob_get_clean();
            set_transient($transient_name, $secondary_menu, 60 * 60 * 4); // 4 hours
        }
        
        echo $secondary_menu;
    }
endif;

/**
 * Reset secondary menu transient when navigation menu is updated
 * 
 * This anonymous function is hooked to the wp_update_nav_menu action
 * and clears the secondary menu cache when a menu is edited in the admin.
 * It handles WPML compatibility by clearing all language versions.
 */
add_action('wp_update_nav_menu', function() {
    delete_transient('wpresidence_secondary_menu_complete');
    if (defined('ICL_LANGUAGE_CODE')) {
        global $sitepress;
        if (isset($sitepress)) {
            $languages = $sitepress->get_active_languages();
            foreach ($languages as $lang_code => $language) {
                delete_transient('wpresidence_secondary_menu_complete_' . $lang_code);
            }
        }
    }
    if (function_exists('wpestate_translation_get_active_languages')) {
        $languages = wpestate_translation_get_active_languages();
        if (is_array($languages)) {
            foreach ($languages as $language) {
                $code = isset($language['code']) ? sanitize_key($language['code']) : '';
                if ('' !== $code) {
                    delete_transient('wpresidence_secondary_menu_complete_' . $code);
                }
            }
        }
    }
});


/**
 * Function to efficiently cache the mobile menu HTML with post meta
 * 
 * This function optimizes the mobile menu rendering by:
 * - Using WordPress transients to cache the complete HTML output
 * - Preloading all menu item metadata in a single database query
 * - Handling multilingual sites through WPML integration
 * 
 * @return string Cached or freshly generated mobile menu HTML
 * @since 4.0.0
 */
function wpestate_get_cached_mobile_menu() {
    // Create a unique transient name with language support for WPML
    if ( function_exists( 'wpestate_set_transient_name_multilang' ) ) {
        $transient_name = wpestate_set_transient_name_multilang( 'wpestate_mobile_menu_complete' );
    } else {
        $transient_name = 'wpestate_mobile_menu_complete';
    }

    // Use direct WordPress transient function
    // wpml add ones
    $use_cache = ! has_filter( 'wpml_active_languages' );
    $mobile_menu = $use_cache ? get_transient( $transient_name ) : false;
    
    
    // If cache is empty, generate the menu
    if ($mobile_menu === false) {
        // Start output buffering to capture the menu HTML
        ob_start();
       
        // Get the mobile menu locations
        $locations = get_nav_menu_locations();
       
        if (isset($locations['mobile'])) {
            $menu_id = $locations['mobile'];
            $menu_object = wp_get_nav_menu_object($menu_id);
           
            if ($menu_object) {
                // Get menu items with essential fields only to reduce query overhead
                $menu_items = wp_get_nav_menu_items($menu_object->term_id, array(
                    'update_post_term_cache' => false, // Don't update term cache
                ));
               
                // Preload all menu item metadata at once to avoid individual queries
                if (!empty($menu_items)) {
                    $menu_item_ids = wp_list_pluck($menu_items, 'ID');
                    update_postmeta_cache($menu_item_ids);
                }
               
                // Generate menu HTML - use identical parameters as original code
                wp_nav_menu(array(
                    'theme_location' => 'mobile',
                    'container' => false,
                    'menu_class' => 'mobilex-menu',
                    'menu_id' => 'menu-main-menu'
                ));
            }
        } 
       
        // Get menu HTML from buffer
        $mobile_menu = ob_get_clean();
       
        // Cache the menu for 4 hours
        //wpml add on
        if ( $use_cache ) {
            set_transient($transient_name, $mobile_menu, 60 * 60 * 24);
        }
    }
   
    return $mobile_menu;
}

/**
 * Reset mobile menu transient when any menu is updated
 * 
 * This function is hooked to menu modification actions and ensures 
 * that the mobile menu cache is cleared whenever menus are updated.
 * It also handles WPML compatibility for multilingual sites.
 * 
 * @since 4.0.0
 */
function wpestate_reset_mobile_menu_transient() {
    $transient_name = 'wpestate_mobile_menu_complete';
    delete_transient($transient_name);
    
    // Handle WPML if active
    if (defined('ICL_LANGUAGE_CODE')) {
        global $sitepress;
        if (isset($sitepress)) {
            $languages = $sitepress->get_active_languages();
            foreach ($languages as $lang_code => $language) {
                delete_transient($transient_name . '_' . $lang_code);
            }
        }
    }
    if (function_exists('wpestate_translation_get_active_languages')) {
        $languages = wpestate_translation_get_active_languages();
        if (is_array($languages)) {
            foreach ($languages as $language) {
                $code = isset($language['code']) ? sanitize_key($language['code']) : '';
                if ('' !== $code) {
                    delete_transient($transient_name . '_' . $code);
                }
            }
        }
    }
}

// Hook to menu update events
add_action('wp_update_nav_menu', 'wpestate_reset_mobile_menu_transient');
add_action('wp_create_nav_menu', 'wpestate_reset_mobile_menu_transient');
add_action('wp_delete_nav_menu', 'wpestate_reset_mobile_menu_transient');
