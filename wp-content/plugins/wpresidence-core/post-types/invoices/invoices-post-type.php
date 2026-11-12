<?php
/**
 * WPEstate Invoice Custom Post Type Registration
 * 
 * This file handles the registration of the 'wpestate_invoice' custom post type
 * which is used to store and manage invoice records within the WPResidence real
 * estate plugin. The custom post type provides a structured way to track payments,
 * subscriptions, and financial transactions in the WordPress admin interface.
 * 
 * The invoice system is designed to work with the WPResidence payment processing
 * system, supporting both one-time and recurring payments for listings and packages.
 * 
 * @package WPResidence
 * @subpackage Invoicing
 * @version 1.0
 */

// register the custom post type
add_action( 'init', 'wpestate_create_invoice_type' );

if( !function_exists('wpestate_create_invoice_type') ):
/**
 * Registers the 'wpestate_invoice' custom post type
 * 
 * This function registers a new custom post type specifically designed for
 * handling invoices in the WPResidence plugin. The function defines the post type's
 * labels, capabilities, UI settings, and other properties needed for proper
 * integration with WordPress admin.
 * 
 * Key features of this custom post type:
 * - Non-public (admin-only) visibility with admin UI
 * - Custom admin menu icon
 * - Custom metabox callback for invoice details
 * - Limited support for title only (other data stored in meta)
 * - Excluded from front-end search
 * 
 * @uses register_post_type() WordPress function to register a custom post type
 * @uses wpestate_add_pack_invoices() Callback function for registering invoice metaboxes
 * 
 * @return void
 */
function wpestate_create_invoice_type() {
        if ( function_exists( 'wpresidence_ptc_is_post_type_enabled' ) && ! wpresidence_ptc_is_post_type_enabled( 'wpestate_invoice' ) ) {
                return;
        }

        $labels = array(
                // Primary labels for admin interface and management
                'name'          => esc_html__( 'Invoices','wpresidence-core'),
                'singular_name' => esc_html__( 'Invoices','wpresidence-core'),
                'add_new'       => esc_html__('Add New Invoice','wpresidence-core'),
                'add_new_item'          =>  esc_html__('Add Invoice','wpresidence-core'),
                'edit'                  =>  esc_html__('Edit Invoice' ,'wpresidence-core'),
                'edit_item'             =>  esc_html__('Edit Invoice','wpresidence-core'),
                'new_item'              =>  esc_html__('New Invoice','wpresidence-core'),
                'view'                  =>  esc_html__('View Invoices','wpresidence-core'),
                'view_item'             =>  esc_html__('View Invoices','wpresidence-core'),
                'search_items'          =>  esc_html__('Search Invoices','wpresidence-core'),
                'not_found'             =>  esc_html__('No Invoices found','wpresidence-core'),
                'not_found_in_trash'    =>  esc_html__('No Invoices found','wpresidence-core'),
                'parent'                =>  esc_html__('Parent Invoice','wpresidence-core')
        );

        // Setup post type capabilities
        $invoice_capabilities = array(
                'edit_post'              => 'edit_wpestate_invoice',
                'read_post'              => 'read_wpestate_invoice',
                'delete_post'            => 'delete_wpestate_invoice',
                'edit_posts'             => 'edit_wpestate_invoices',
                'edit_others_posts'      => 'edit_others_wpestate_invoices',
                'publish_posts'          => 'publish_wpestate_invoices',
                'read_private_posts'     => 'read_private_wpestate_invoices',
                'create_posts'           => 'create_wpestate_invoices',
                'delete_posts'           => 'delete_wpestate_invoices',
                'delete_private_posts'   => 'delete_private_wpestate_invoices',
                'delete_published_posts' => 'delete_published_wpestate_invoices',
                'delete_others_posts'    => 'delete_others_wpestate_invoices',
                'edit_private_posts'     => 'edit_private_wpestate_invoices',
                'edit_published_posts'   => 'edit_published_wpestate_invoices',
        );

        register_post_type( 'wpestate_invoice',
                array(
                        'labels' =>$labels,
                        // Set as non-public but visible in admin
                        'public' => false,
                        // UI/UX settings for admin interface
                        'show_ui'=>true,
                        'show_in_nav_menus'=>true,
                        'show_in_menu'=>true,
                        'show_in_admin_bar'=>true,
                        // Archive and permalink structure
                        'has_archive' => true,
                        'rewrite' => array('slug' => 'invoice'),
                        // Only support the title field (other data in custom meta)
                        'supports' => array('title'),
                        // Export capability for data portability
                        'can_export' => true,
                        // Register custom metabox callback for invoice details
                        'register_meta_box_cb' => 'wpestate_add_pack_invoices',
                        // Custom menu icon in admin dashboard
                        'menu_icon'=>WPESTATE_PLUGIN_DIR_URL.'/img/invoices.png',
                        // Hide from frontend search results
                        'exclude_from_search'   => true,
                        'map_meta_cap' => true,
                        'capability_type' => array('wpestate_invoice', 'wpestate_invoices'),
                        'capabilities' => $invoice_capabilities
                )
        );
}
endif; // end   wpestate_create_invoice_type



/**
 * Ensures proper capabilities are set for the administrator role
 * This function runs early in the WordPress init process to set up required permissions
 * for managing invoices
 *
 * @since 4.0.0
 * @return void
 */
add_action('init', 'wpestate_ensure_invoice_caps', 0);
if (!function_exists('wpestate_ensure_invoice_caps')):
    function wpestate_ensure_invoice_caps() {
        $admin = get_role('administrator');
        if ($admin) {
            // Add invoice editing capabilities
            $admin->add_cap('edit_wpestate_invoice');
            $admin->add_cap('read_wpestate_invoice');
            $admin->add_cap('delete_wpestate_invoice');
            $admin->add_cap('edit_wpestate_invoices');
            $admin->add_cap('edit_others_wpestate_invoices');
            $admin->add_cap('publish_wpestate_invoices');
            $admin->add_cap('read_private_wpestate_invoices');
            $admin->add_cap('create_wpestate_invoices');
            $admin->add_cap('delete_wpestate_invoices');
            $admin->add_cap('delete_private_wpestate_invoices');
            $admin->add_cap('delete_published_wpestate_invoices');
            $admin->add_cap('delete_others_wpestate_invoices');
            $admin->add_cap('edit_private_wpestate_invoices');
            $admin->add_cap('edit_published_wpestate_invoices');
            
            // Add additional invoice management capabilities
            $admin->add_cap('manage_wpestate_invoices');
            $admin->add_cap('view_wpestate_invoices');
            $admin->add_cap('export_wpestate_invoices');
        }
    }
endif;