<?php
/**
 * WP Estate Membership Package Post Type
 *
 * This file contains the custom post type registration for membership packages
 * in the WP Estate real estate platform. Membership packages are used to define
 * subscription plans that users can purchase to list properties on the site.
 * 
 * The membership_package post type stores information about:
 * - Package pricing
 * - Number of listings allowed
 * - Featured listing allowances
 * - Billing periods and durations
 * - Package visibility settings
 *
 * @package WP Estate
 * @subpackage Membership
 * @since 1.0.0
 */

// register the custom post type
add_action( 'init', 'wpestate_create_membership_type' );
if( !function_exists('wpestate_create_membership_type') ):
/**
 * Registers the membership_package custom post type
 *
 * This function creates a custom post type for membership packages with specific
 * settings and capabilities. The post type is used for creating and managing
 * different membership plans that users can purchase.
 * 
 * Key settings:
 * - Not publicly queryable (public => false)
 * - Visible in admin UI and menus
 * - Supports title and thumbnail
 * - Uses custom metaboxes for package configuration
 * - Has a custom menu icon
 * - Custom permalink structure using 'package' slug
 *
 * @return void
 */
function wpestate_create_membership_type() {
        if ( function_exists( 'wpresidence_ptc_is_post_type_enabled' ) && ! wpresidence_ptc_is_post_type_enabled( 'membership_package' ) ) {
                return;
        }
        $labels= array(
                // Labels for various UI elements related to this post type
                'name'          => esc_html__( 'Membership Packages','wpresidence-core'),
                'singular_name' => esc_html__( 'Membership Packages','wpresidence-core'),
                'add_new'       => esc_html__('Add New Membership Package','wpresidence-core'),
                'add_new_item'          =>  esc_html__('Add Membership Packages','wpresidence-core'),
                'edit'                  =>  esc_html__('Edit Membership Packages' ,'wpresidence-core'),
                'edit_item'             =>  esc_html__('Edit Membership Package','wpresidence-core'),
                'new_item'              =>  esc_html__('New Membership Packages','wpresidence-core'),
                'view'                  =>  esc_html__('View Membership Packages','wpresidence-core'),
                'view_item'             =>  esc_html__('View Membership Packages','wpresidence-core'),
                'search_items'          =>  esc_html__('Search Membership Packages','wpresidence-core'),
                'not_found'             =>  esc_html__('No Membership Packages found','wpresidence-core'),
                'not_found_in_trash'    =>  esc_html__('No Membership Packages found','wpresidence-core'),
                'parent'                =>  esc_html__('Parent Membership Package','wpresidence-core')
        );

        // Setup post type capabilities
        $membership_capabilities = array(
                'edit_post'              => 'edit_membership_package',
                'read_post'              => 'read_membership_package',
                'delete_post'            => 'delete_membership_package',
                'edit_posts'             => 'edit_membership_packages',
                'edit_others_posts'      => 'edit_others_membership_packages',
                'publish_posts'          => 'publish_membership_packages',
                'read_private_posts'     => 'read_private_membership_packages',
                'create_posts'           => 'create_membership_packages',
                'delete_posts'           => 'delete_membership_packages',
                'delete_private_posts'   => 'delete_private_membership_packages',
                'delete_published_posts' => 'delete_published_membership_packages',
                'delete_others_posts'    => 'delete_others_membership_packages',
                'edit_private_posts'     => 'edit_private_membership_packages',
                'edit_published_posts'   => 'edit_published_membership_packages',
        );
        

        register_post_type( 'membership_package',
                array(
                'labels' =>$labels,
                // Post type is not publicly queryable, but is visible in admin
                'public' => false,
                'show_ui'=>true,
                'show_in_nav_menus'=>true,
                'show_in_menu'=>true,
                'show_in_admin_bar'=>true,
                'has_archive' => true,
                // Custom permalink structure with 'package' as the base
                'rewrite' => array('slug' => 'package'),
                // Only supports title and featured image (no content editor)
                'supports' => array('title','thumbnail'),
                'can_export' => true,
                // Register callback function for adding custom meta boxes
                'register_meta_box_cb' => 'wpestate_add_pack_metaboxes',
                // Custom menu icon from plugin directory
                'menu_icon'=> WPESTATE_PLUGIN_DIR_URL.'/img/membership.png',
                'map_meta_cap' => true,
                'capability_type' => array('membership_package', 'membership_packages'),
                'capabilities' => $membership_capabilities
                )
        );
}
endif; // end   wpestate_create_membership_type



/**
 * Ensures proper capabilities are set for the administrator role
 * This function runs early in the WordPress init process to set up required permissions
 * for managing membership packages
 *
 * @since 4.0.0
 * @return void
 */
add_action('init', 'wpestate_ensure_membership_caps', 0);
if (!function_exists('wpestate_ensure_membership_caps')):
    function wpestate_ensure_membership_caps() {
        $admin = get_role('administrator');
        if ($admin) {
            // Add membership package editing capabilities
            $admin->add_cap('edit_membership_package');
            $admin->add_cap('read_membership_package');
            $admin->add_cap('delete_membership_package');
            $admin->add_cap('edit_membership_packages');
            $admin->add_cap('edit_others_membership_packages');
            $admin->add_cap('publish_membership_packages');
            $admin->add_cap('read_private_membership_packages');
            $admin->add_cap('create_membership_packages');
            $admin->add_cap('delete_membership_packages');
            $admin->add_cap('delete_private_membership_packages');
            $admin->add_cap('delete_published_membership_packages');
            $admin->add_cap('delete_others_membership_packages');
            $admin->add_cap('edit_private_membership_packages');
            $admin->add_cap('edit_published_membership_packages');
            
            // Add additional membership management capabilities
            $admin->add_cap('manage_membership_packages');
            $admin->add_cap('view_membership_packages');
            $admin->add_cap('activate_membership_packages');
            $admin->add_cap('deactivate_membership_packages');
        }
    }
endif;