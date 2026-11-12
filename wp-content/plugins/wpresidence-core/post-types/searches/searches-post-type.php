<?php
/**
 * WpResidence Saved Search Post Type Registration
 *
 * This file registers and configures the custom post type 'wpestate_search' used
 * for storing saved property searches in the WpResidence real estate plugin.
 * Saved searches allow users to save their property search criteria and potentially
 * receive notifications about new matching properties.
 *
 * The custom post type is configured to be managed in the WordPress admin area
 * but not publicly visible on the frontend website.
 *
 * @package    WpResidence
 * @subpackage Search
 * @version    1.0
 * @author     WpResidence
 */

/**
 * Registers the 'wpestate_search' custom post type
 *
 * This function creates a custom post type for saved property searches with
 * specific configuration options to control its behavior and appearance in
 * the WordPress admin interface. Key settings include:
 *
 * - Custom labels for UI elements
 * - Limited visibility (admin-only, not public)
 * - Custom URL rewrite rules
 * - Support for title field only
 * - Association with a metabox callback function
 * - Custom admin menu icon
 *
 * The post type is designed to store search parameters and user preferences
 * for saved property searches, allowing site administrators to manage these
 * and potentially use them for email notifications or other features.
 *
 * @uses register_post_type() WordPress function to register custom post types
 * @uses esc_html__() For translation/internationalization of UI text
 * @uses WPESTATE_PLUGIN_DIR_URL Constant containing plugin directory URL
 * @uses wpestate_add_searches Callback function for metabox registration
 * @return void
 */
// register the custom post type
add_action( 'init', 'wpestate_create_saved_search' );
if( !function_exists('wpestate_create_saved_search') ):
function wpestate_create_saved_search() {
    if ( function_exists( 'wpresidence_ptc_is_post_type_enabled' ) && ! wpresidence_ptc_is_post_type_enabled( 'wpestate_search' ) ) {
        return;
    }
    
    $labels= array(
        'name'          => esc_html__( 'Searches','wpresidence-core'),
        'singular_name' => esc_html__( 'Searches','wpresidence-core'),
        'add_new'       => esc_html__('Add New Searches','wpresidence-core'),
        'add_new_item'          =>  esc_html__('Add Searches','wpresidence-core'),
        'edit'                  =>  esc_html__('Edit Searches' ,'wpresidence-core'),
        'edit_item'             =>  esc_html__('Edit Searches','wpresidence-core'),
        'new_item'              =>  esc_html__('New Searches','wpresidence-core'),
        'view'                  =>  esc_html__('View Searches','wpresidence-core'),
        'view_item'             =>  esc_html__('View Searches','wpresidence-core'),
        'search_items'          =>  esc_html__('Search Searches','wpresidence-core'),
        'not_found'             =>  esc_html__('No Searches found','wpresidence-core'),
        'not_found_in_trash'    =>  esc_html__('No Searches found','wpresidence-core'),
        'parent'                =>  esc_html__('Parent Searches','wpresidence-core')
    );
    
    // Setup post type capabilities
    $search_capabilities = array(
        'edit_post'              => 'edit_wpestate_search',
        'read_post'              => 'read_wpestate_search',
        'delete_post'            => 'delete_wpestate_search',
        'edit_posts'             => 'edit_wpestate_searches',
        'edit_others_posts'      => 'edit_others_wpestate_searches',
        'publish_posts'          => 'publish_wpestate_searches',
        'read_private_posts'     => 'read_private_wpestate_searches',
        'create_posts'           => 'create_wpestate_searches',
        'delete_posts'           => 'delete_wpestate_searches',
        'delete_private_posts'   => 'delete_private_wpestate_searches',
        'delete_published_posts' => 'delete_published_wpestate_searches',
        'delete_others_posts'    => 'delete_others_wpestate_searches',
        'edit_private_posts'     => 'edit_private_wpestate_searches',
        'edit_published_posts'   => 'edit_published_wpestate_searches',
    );

    register_post_type( 'wpestate_search',
        array(
            'labels'        =>  $labels,
            'public'        =>  false,        // Not publicly accessible/viewable
            'show_ui'       =>  true,         // Visible in admin UI
            'has_archive'   =>  false,        // No archive page for searches
            'rewrite'       =>  array('slug' => 'searches'), // URL structure if needed
            'supports'      =>  array('title'), // Only supports title field (not content/editor)
            'can_export'    =>  true,         // Can be exported with WordPress tools
            'register_meta_box_cb' => 'wpestate_add_searches', // Callback for custom metaboxes
            'menu_icon'=>WPESTATE_PLUGIN_DIR_URL.'/img/searches.png', // Custom admin menu icon,
            'map_meta_cap' => true,
            'capability_type' => array('wpestate_search', 'wpestate_searches'),
            'capabilities' => $search_capabilities
        )
    );
}
endif; // end   wpestate_create_invoice_type



/**
 * Ensures proper capabilities are set for the administrator role
 * This function runs early in the WordPress init process to set up required permissions
 * for managing saved searches
 *
 * @since 4.0.0
 * @return void
 */
add_action('init', 'wpestate_ensure_search_caps', 0);
if (!function_exists('wpestate_ensure_search_caps')):
    function wpestate_ensure_search_caps() {
        $admin = get_role('administrator');
        if ($admin) {
            // Add saved search editing capabilities
            $admin->add_cap('edit_wpestate_search');
            $admin->add_cap('read_wpestate_search');
            $admin->add_cap('delete_wpestate_search');
            $admin->add_cap('edit_wpestate_searches');
            $admin->add_cap('edit_others_wpestate_searches');
            $admin->add_cap('publish_wpestate_searches');
            $admin->add_cap('read_private_wpestate_searches');
            $admin->add_cap('create_wpestate_searches');
            $admin->add_cap('delete_wpestate_searches');
            $admin->add_cap('delete_private_wpestate_searches');
            $admin->add_cap('delete_published_wpestate_searches');
            $admin->add_cap('delete_others_wpestate_searches');
            $admin->add_cap('edit_private_wpestate_searches');
            $admin->add_cap('edit_published_wpestate_searches');
            
            // Add additional search management capabilities
            $admin->add_cap('manage_wpestate_searches');
            $admin->add_cap('view_wpestate_searches');
            $admin->add_cap('export_wpestate_searches');
        }
    }
endif;