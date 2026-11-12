<?php
/**
 * Property Post Type and Taxonomy Registration
 *
 * This file handles the registration of the custom post type 'estate_property'
 * and all its associated taxonomies for the WPResidence real estate plugin.
 * It sets up the property listings structure including categories, types,
 * locations, features, and status taxonomies.
 *
 * @package WPResidence
 * @subpackage Core
 * @since 1.0
 */

// Register the custom post type on init with priority 20
add_action('init', 'wpestate_create_property_type', 20);

if (!function_exists('wpestate_create_property_type')):
    /**
     * Creates the estate_property custom post type and its taxonomies
     * 
     * Registers the main property post type with all necessary labels and parameters.
     * Also registers hierarchical taxonomies for:
     * - Property Categories
     * - Property Types
     * - Cities
     * - Neighborhoods
     * - Counties/States
     * - Features & Amenities
     * - Property Status
     *
     * @since 1.0
     * @return void
     */
function wpestate_create_property_type() {
        if ( function_exists( 'wpresidence_ptc_is_post_type_enabled' ) && ! wpresidence_ptc_is_post_type_enabled( 'estate_property' ) ) {
            return;
        }
        // Get safe rewrite rules for URLs
        $rewrites = wpestate_safe_rewite();
        
        $labels =    array(
            'name' => esc_html__('Properties', 'wpresidence-core'),
            'singular_name' => esc_html__('Property', 'wpresidence-core'),
            'add_new' => esc_html__('Add New Property', 'wpresidence-core'),
            'add_new_item' => esc_html__('Add Property', 'wpresidence-core'),
            'edit' => esc_html__('Edit', 'wpresidence-core'),
            'edit_item' => esc_html__('Edit Property', 'wpresidence-core'),
            'new_item' => esc_html__('New Property', 'wpresidence-core'),
            'view' => esc_html__('View', 'wpresidence-core'),
            'view_item' => esc_html__('View Property', 'wpresidence-core'),
            'search_items' => esc_html__('Search Property By Name or ID', 'wpresidence-core'),
            'not_found' => esc_html__('No Properties found', 'wpresidence-core'),
            'not_found_in_trash' => esc_html__('No Properties found in Trash', 'wpresidence-core'),
            'parent' => esc_html__('Parent Property', 'wpresidence-core'),
            'featured_image' => esc_html__('Featured Image', 'wpresidence-core'),
            'set_featured_image' => esc_html__('Set Featured Image', 'wpresidence-core'),
            'remove_featured_image' => esc_html__('Remove Featured Image', 'wpresidence-core'),
            'use_featured_image' => esc_html__('Use Featured Image', 'wpresidence-core'),
        );

        

        // Setup post type capabilities
        $property_capabilities = array(
            'edit_post'              => 'edit_estate_property',
            'read_post'              => 'read_estate_property',
            'delete_post'            => 'delete_estate_property',
            'edit_posts'             => 'edit_estate_properties',
            'edit_others_posts'      => 'edit_others_estate_properties',
            'publish_posts'          => 'publish_estate_properties',
            'read_private_posts'     => 'read_private_estate_properties',
            'create_posts'           => 'create_estate_properties',
            'delete_posts'           => 'delete_estate_properties',
            'delete_private_posts'   => 'delete_private_estate_properties',
            'delete_published_posts' => 'delete_published_estate_properties',
            'delete_others_posts'    => 'delete_others_estate_properties',
            'edit_private_posts'     => 'edit_private_estate_properties',
            'edit_published_posts'   => 'edit_published_estate_properties',
        );


        // Register the main property post type
        // Sets up labels, permissions, and features supported
        register_post_type('estate_property', array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => $rewrites[0]),
            'supports' => array('title', 'editor', 'thumbnail', 'comments', 'excerpt'),
            'can_export' => true,
            'register_meta_box_cb' => 'wpestate_add_property_metaboxes',
            'menu_icon' => WPESTATE_PLUGIN_DIR_URL . '/img/properties.png',
            'show_in_rest' => true,
            'map_meta_cap'       => true,
            'capability_type'    => array('estate_property', 'estate_properties'),
            'capabilities'       => $property_capabilities
            )
        );

        // Register Property Categories taxonomy
        // Used for broad property classifications
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_category' ) ) {
        register_taxonomy('property_category', array('estate_property'), array(
            'labels' => array(
                'name' => esc_html__('Categories', 'wpresidence-core'),
                'add_new_item' => esc_html__('Add New Property Category', 'wpresidence-core'),
                'new_item_name' => esc_html__('New Property Category', 'wpresidence-core')
            ),
            'hierarchical' => true,
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => $rewrites[1])
                )
        );
        }

        // Register Property Type taxonomy
        // Used for specific property types (apartment, house, etc)
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_action_category' ) ) {
        register_taxonomy('property_action_category', array('estate_property'), array(
            'labels' => array(
                'name' => esc_html__('Type', 'wpresidence-core'),
                'add_new_item' => esc_html__('Add New Type', 'wpresidence-core'),
                'new_item_name' => esc_html__('New Type', 'wpresidence-core')
            ),
            'hierarchical' => true,
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => $rewrites[2])
                )
        );
        }

        // Register City taxonomy
        // Handles property location at city level
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_city' ) ) {
        register_taxonomy('property_city', array('estate_property'), array(
            'labels' => array(
                'name' => esc_html__('City', 'wpresidence-core'),
                'add_new_item' => esc_html__('Add New City', 'wpresidence-core'),
                'new_item_name' => esc_html__('New City', 'wpresidence-core')
            ),
            'hierarchical' => true,
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => $rewrites[3], 'with_front' => false)
                )
        );
        }

        // Register Neighborhood taxonomy
        // For sub-city level location organization
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_area' ) ) {
        register_taxonomy('property_area', array('estate_property'), array(
            'labels' => array(
                'name' => esc_html__('Neighborhood', 'wpresidence-core'),
                'add_new_item' => esc_html__('Add New Neighborhood', 'wpresidence-core'),
                'new_item_name' => esc_html__('New Neighborhood', 'wpresidence-core')
            ),
            'hierarchical' => true,
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => $rewrites[4])
                )
        );
        }

        // Register County/State taxonomy
        // For regional/state level organization
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_county_state' ) ) {
        register_taxonomy('property_county_state', array('estate_property'), array(
            'labels' => array(
                'name' => esc_html__('County / State', 'wpresidence-core'),
                'add_new_item' => esc_html__('Add New County / State', 'wpresidence-core'),
                'new_item_name' => esc_html__('New County / State', 'wpresidence-core')
            ),
            'hierarchical' => true,
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => $rewrites[5])
                )
        );
        }

        // Register Features taxonomy
        // For property amenities and features
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_features' ) ) {
        register_taxonomy('property_features', 'estate_property', array(
            'labels' => array(
                'name' => esc_html__('Features & Amenities', 'wpresidence-core'),
                'add_new_item' => esc_html__('Add New Feature', 'wpresidence-core'),
                'new_item_name' => esc_html__('New Feature', 'wpresidence-core')
            ),
            'hierarchical' => true,
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => $rewrites[24])
                )
        );
        }

        // Register Status taxonomy
        // For property availability status (e.g., for sale, sold, rented)
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_status' ) ) {
        register_taxonomy('property_status', 'estate_property', array(
            'labels' => array(
                'name' => esc_html__('Property Status', 'wpresidence-core'),
                'add_new_item' => esc_html__('Add New Status', 'wpresidence-core'),
                'new_item_name' => esc_html__('New Status', 'wpresidence-core')
            ),
            'hierarchical' => true,
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => $rewrites[25])
                )
        );
        }

        // Convert any existing features/status to the taxonomy system
        wpestate_convert_features_status_to_tax();
    }
endif;




add_action('init', 'wpestate_my_custom_post_status');
if (!function_exists('wpestate_my_custom_post_status')):
    /**
     * Registers custom post statuses for properties
     *
     * Adds two custom statuses:
     * - expired: For properties with expired membership
     * - disabled: For properties disabled by users
     * 
     * These statuses are used to manage property visibility and membership states
     *
     * @since 1.0
     * @return void
     */
    function wpestate_my_custom_post_status() {
        // Register 'expired' status for expired memberships
        register_post_status( 'expired', array(
            'label'                     => _x( 'Expired',  'post status', 'wpresidence-core' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'label_count'               => _n_noop( 'Membership Expired <span class="count">(%s)</span>',
                                                    'Membership Expired <span class="count">(%s)</span>',
                                                    'wpresidence-core' ),
        ) );

        register_post_status( 'disabled', array(
            'label'                     => _x( 'Disabled', 'post status', 'wpresidence-core' ),
            'public'                    => false,
            'exclude_from_search'       => false,
            'show_in_admin_status_list' => true,
            'show_in_admin_all_list'    => true,
            'label_count'               => _n_noop( 'Disabled by user <span class="count">(%s)</span>',
                                                    'Disabled by user <span class="count">(%s)</span>',
                                                    'wpresidence-core' ),
        ) );
    }
endif;



add_action( 'admin_footer', function () {
    ?>
    <script>
    jQuery(function($){
        const labels = {
            'expired': 'Expired',
            'disabled': 'Disabled'
        };
        const status = $('#hidden_post_status').val();


        if ( labels[status] ) {
            $('#post-status-display').text( labels[status] );
        }
    });
    </script>
    <?php
});





// Ensure custom statuses appear in Admin "All" list for properties
add_action('pre_get_posts', 'wpestate_admin_all_includes_custom_statuses', 20);
if ( ! function_exists('wpestate_admin_all_includes_custom_statuses') ):
function wpestate_admin_all_includes_custom_statuses( $query ) {
    // Only affect main admin list query for our CPT
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $post_type = $query->get('post_type');
    if ( $post_type !== 'estate_property' ) {
        return;
    }

    // If a specific status is requested via filter, don't override
    $requested = isset($_GET['post_status']) ? sanitize_text_field( wp_unslash($_GET['post_status']) ) : '';
    if ( $requested && $requested !== 'all' ) {
        return;
    }

    // For the default "All" view, include disabled/expired explicitly
    $current_status = $query->get('post_status');
    if ( empty($current_status) || $current_status === 'any' ) {
        $query->set('post_status', array('publish','pending','draft','future','private','expired','disabled'));
    }
}
endif;




/**
 * Ensures proper taxonomy capabilities are set for the administrator role
 * This function runs early in the WordPress init process to set up required permissions
 *
 * @since 4.0.0
 * @return void
 */
add_action('init', 'wpestate_ensure_property_taxonomy_caps', 0);
if (!function_exists('wpestate_ensure_property_taxonomy_caps')):
    function wpestate_ensure_property_taxonomy_caps() {
        $admin = get_role('administrator');
        if ($admin) {
            // Add property editing capabilities
            $admin->add_cap('edit_estate_property');
            $admin->add_cap('read_estate_property');
            $admin->add_cap('delete_estate_property');
            $admin->add_cap('edit_estate_properties');
            $admin->add_cap('edit_others_estate_properties');
            $admin->add_cap('publish_estate_properties');
            $admin->add_cap('read_private_estate_properties');
            $admin->add_cap('create_estate_properties');
            $admin->add_cap('delete_estate_properties');
            $admin->add_cap('delete_private_estate_properties');
            $admin->add_cap('delete_published_estate_properties');
            $admin->add_cap('delete_others_estate_properties');
            $admin->add_cap('edit_private_estate_properties');
            $admin->add_cap('edit_published_estate_properties');
            
            // Define taxonomy capabilities array
            $taxonomy_caps = array(
                'manage_property_category',
                'edit_property_category',
                'delete_property_category',
                'assign_property_category',
                'manage_property_action_category',
                'edit_property_action_category',
                'delete_property_action_category',
                'assign_property_action_category',
                'manage_property_city',
                'edit_property_city',
                'delete_property_city',
                'assign_property_city',
                'manage_property_area',
                'edit_property_area',
                'delete_property_area',
                'assign_property_area',
                'manage_property_county_state',
                'edit_property_county_state',
                'delete_property_county_state',
                'assign_property_county_state',
                'manage_property_features',
                'edit_property_features',
                'delete_property_features',
                'assign_property_features',
                'manage_property_status',
                'edit_property_status',
                'delete_property_status',
                'assign_property_status'
            );
            
            // Add each capability to admin role
            foreach ($taxonomy_caps as $cap) {
                $admin->add_cap($cap);
            }
        }
    }
endif;
