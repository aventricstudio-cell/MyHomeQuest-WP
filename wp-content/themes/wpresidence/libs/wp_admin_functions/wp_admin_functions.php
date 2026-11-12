<?php


/**
 * WPResidence Admin Menu functions for managing the WordPress admin menu
 * specific to the WPResidence theme, including adding custom submenu items.
 *
 * @package WPResidence
 * @subpackage AdminCustomization
 * @since 1.0.0
 */

// Only run in admin
if ( is_admin() ) {
    add_action( 'admin_menu', 'wpestate_manage_admin_menu' );
}

if ( ! function_exists( 'wpestate_manage_admin_menu' ) ) :
    /**
     * Manage WPResidence admin menu
     *
     * This function adds custom submenu items to the WordPress admin menu
     * for the WPResidence theme, including demo import and cache clearing options.
     */
    function wpestate_manage_admin_menu() {
       // Define the parent slug for Redux Framework menu
       $redux_parent_slug = 'wpresidence_admin'; // Default slug for Redux menu, adjust if necessary

       // Check if Redux Framework is active and accessible
       if (!class_exists('ReduxFramework')) {
           return; // Exit if Redux is not active
       }

       // Define the submenu label and link
       $label_import = esc_html__("Import Demo","wpresidence"); ;
       $link = 'themes.php?page=one-click-demo-import';

       // Check if the One Click Demo Import plugin is active
       if (!class_exists('OCDI_Plugin')) {
           $label_import = esc_html__( "Import Demo - Activate Plugin","wpresidence"); 
           $link         = admin_url('plugins.php'); // Redirect to plugins page if plugin isn't active
       }

       // Add the submenu under Redux Framework menu
       add_submenu_page(
           $redux_parent_slug, // Parent menu slug (Redux Framework menu)
           $label_import,      // Page title
           $label_import,      // Menu title
           'manage_options',   // Capability
           $link,              // Menu slug or link
           '',                 // Callback (not used for external links)
           1                   // Position (low value ensures it appears first)
       );

        add_submenu_page(
            'libs/theme-admin.php',
            $label_import,
            $label_import,
            'manage_options',
            $link,
            ''
        );
        
        add_submenu_page(
            'libs/theme-admin.php',
            'Clear Theme Cache',
            'Clear Theme Cache',
            'manage_options',
            'libs/theme-cache.php',
            'wpestate_clear_cache_theme'
        );
        
        // Include required files

    }
endif;





/**
 * Counts the total number of pages in the WordPress site.
 *
 * This function retrieves the count of all pages, regardless of their status.
 * It's used within the WpResidence theme to determine the total number of pages.
 *
 * @package WpResidence
 * @subpackage Functions
 * @since WpResidence 1.0
 *
 * @return int The total number of pages.
 */

if (!function_exists('wpestate_how_many_pages')) :
    function wpestate_how_many_pages() {
        // Set up arguments for WP_Query
        $args = array(
            'post_type'   => 'page',
            'post_status' => 'any',
            'fields'      => 'ids', // Only get post IDs to improve performance
            'nopaging'    => true,  // Get all pages without pagination
        );

        // Perform the query
        $query = new WP_Query($args);

        // Get the total number of pages found
        $current_pages = $query->found_posts;

        // Clean up after the query
        wp_reset_postdata();

        // Return the total number of pages
        return $current_pages;
    }
endif;




if( !function_exists('wpestate_ajax_apperance_set') ):
    function wpestate_ajax_apperance_set(){
        $args = array(
            'post_type'         => 'estate_property',
            'post_status'       => 'any',
            'paged'             => -1,
        );

        $query = new WP_Query($args);

        $current_listed= $query->found_posts;
        wp_reset_postdata();
        wp_reset_query();
        return $current_listed;

    }
endif;


/**
 * Counts the total number of properties in the WordPress site.
 *
 * This function retrieves the count of all estate_property posts, regardless of their status.
 * It's used within the WpResidence theme to determine the total number of properties.
 * Note: The function name doesn't accurately reflect its purpose and should be considered for renaming.
 *
 * @package WpResidence
 * @subpackage Functions
 * @since WpResidence 1.0
 *
 * @return int The total number of properties.
 */

if (!function_exists('wpestate_ajax_apperance_set')) :
    function wpestate_ajax_apperance_set() {
        // Set up arguments for WP_Query
        $args = array(
            'post_type'   => 'estate_property',
            'post_status' => 'any',
            'fields'      => 'ids', // Only get post IDs to improve performance
            'nopaging'    => true,  // Get all properties without pagination
        );

        // Perform the query
        $query = new WP_Query($args);

        // Get the total number of properties found
        $current_listed = $query->found_posts;

        // Clean up after the query
        wp_reset_postdata();

        // Return the total number of properties
        return $current_listed;
    }
endif;





