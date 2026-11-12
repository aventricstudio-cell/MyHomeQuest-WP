<?php
/**
 * Developer Post Type and Taxonomies Registration for WP Estate
 *
 * This file defines and registers the 'estate_developer' custom post type
 * along with its associated taxonomies for the WP Estate real estate management system.
 * 
 * The file handles:
 * 1. Custom post type registration with appropriate labels, supports, and permalinks
 * 2. Developer categorization taxonomies (category and action)
 * 3. Geographic taxonomies (city, neighborhood, county/state)
 * 4. Custom permalink structure based on site settings
 *
 * All taxonomies are hierarchical and REST API enabled for modern development approaches,
 * and use custom rewrite rules from the wpestate_safe_rewite() function.
 *
 * @package WPResidence
 * @subpackage CustomPostTypes
 * @since 1.0.0
 */

// Register the custom post type on WordPress init hook with priority 20
add_action( 'init', 'wpestate_create_developer_type', 20);

if( !function_exists('wpestate_create_developer_type') ):
/**
 * Register Developer Custom Post Type and Associated Taxonomies
 * 
 * This comprehensive function registers:
 * - The 'estate_developer' custom post type with SEO-friendly URLs
 * - Five hierarchical taxonomies for organizing developers:
 *   1. Category (property_category_developer)
 *   2. Action (property_action_developer)
 *   3. City (property_city_developer)
 *   4. Neighborhood (property_area_developer)
 *   5. County/State (property_county_state_developer)
 *
 * Each entity uses translatable strings and customizable URL structures
 * based on the rewrite rules retrieved from wpestate_safe_rewite().
 *
 * @since 1.0.0
 * @return void
 */
    function wpestate_create_developer_type() {
        if ( function_exists( 'wpresidence_ptc_is_post_type_enabled' ) && ! wpresidence_ptc_is_post_type_enabled( 'estate_developer' ) ) {
            return;
        }

        // Get custom rewrite rules from theme settings
        $rewrites = wpestate_safe_rewite();
        
        // Set developer slug from settings or use default
        $slug = 'developer';
        if(isset($rewrites[23])){
            $slug = $rewrites[23];
        }


        $labels=  array(
            'name'          => esc_html__( 'Developer','wpresidence-core'),
            'singular_name' => esc_html__( 'Developer','wpresidence-core'),
            'add_new'       => esc_html__('Add New Developer','wpresidence-core'),
            'add_new_item'          =>  esc_html__('Add Developer','wpresidence-core'),
            'edit'                  =>  esc_html__('Edit' ,'wpresidence-core'),
            'edit_item'             =>  esc_html__('Edit Developer','wpresidence-core'),
            'new_item'              =>  esc_html__('New Developer','wpresidence-core'),
            'view'                  =>  esc_html__('View','wpresidence-core'),
            'view_item'             =>  esc_html__('View Developer','wpresidence-core'),
            'search_items'          =>  esc_html__('Search Developer','wpresidence-core'),
            'not_found'             =>  esc_html__('No Developer found','wpresidence-core'),
            'not_found_in_trash'    =>  esc_html__('No Developer found','wpresidence-core'),
            'parent'                =>  esc_html__('Parent Developer','wpresidence-core'),
            'featured_image'        => esc_html__('Featured Image','wpresidence-core'),
            'set_featured_image'    => esc_html__('Set Featured Image','wpresidence-core'),
            'remove_featured_image' => esc_html__('Remove Featured Image','wpresidence-core'),
            'use_featured_image'    => esc_html__('Use Featured Image','wpresidence-core'),
        );


        // Setup post type capabilities
        $developer_capabilities = array(
            'edit_post'              => 'edit_estate_developer',
            'read_post'              => 'read_estate_developer',
            'delete_post'            => 'delete_estate_developer',
            'edit_posts'             => 'edit_estate_developers',
            'edit_others_posts'      => 'edit_others_estate_developers',
            'publish_posts'          => 'publish_estate_developers',
            'read_private_posts'     => 'read_private_estate_developers',
            'create_posts'           => 'create_estate_developers',
            'delete_posts'           => 'delete_estate_developers',
            'delete_private_posts'   => 'delete_private_estate_developers',
            'delete_published_posts' => 'delete_published_estate_developers',
            'delete_others_posts'    => 'delete_others_estate_developers',
            'edit_private_posts'     => 'edit_private_estate_developers',
            'edit_published_posts'   => 'edit_published_estate_developers',
        );
                


        // Register the main estate_developer custom post type
        register_post_type( 'estate_developer',
                array(
                'labels' => $labels,
                'public' => true,                   // Makes the post type publicly accessible
                'has_archive' => true,              // Enables archive page for developers
                'rewrite' => array('slug' => $slug), // Custom permalink structure
                'supports' => array('title', 'editor', 'thumbnail','comments','excerpt'), // Supported features
                'can_export' => true,               // Allows export via WordPress tools
                'register_meta_box_cb' => 'wpestate_add_developer_metaboxes', // Callback for custom metaboxes
                'menu_icon'=> WPESTATE_PLUGIN_DIR_URL.'/img/developer.png',   // Custom menu icon
                'show_in_rest'=>true,               // Enables Gutenberg/REST API support
                'map_meta_cap' => true,
                'capability_type' => array('estate_developer', 'estate_developers'),
                'capabilities' => $developer_capabilities
                )
        );
        
        // Get developer category slug from settings or use default
        if(!isset($rewrites[17]) || $rewrites[17]==''){
            $property_category_developer = 'developer-category';
        }else{
            $property_category_developer = $rewrites[17];
        }

        
        // Register the Developer Category taxonomy
        // This taxonomy allows categorizing developers (e.g., "Residential", "Commercial", etc.)
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_category_developer' ) ) {
        register_taxonomy('property_category_developer', array('estate_developer'), array(
            'labels' => array(
                'name'              => esc_html__('Developer Categories','wpresidence-core'),
                'add_new_item'      => esc_html__('Add New Developer Category','wpresidence-core'),
                'new_item_name'     => esc_html__('New Developer Category','wpresidence-core')
            ),
            'hierarchical'  => true,     // Category-like behavior (not tag-like)
            'query_var'     => true,     // Can be queried
            'show_in_rest'  => true,     // Enables REST API
            'rewrite'       => array( 'slug' => $property_category_developer ) // Custom URL structure
            )
        );
        }

        // Get developer action slug from settings or use default
        if(!isset($rewrites[18]) || $rewrites[18]==''){
            $property_action_developer = 'developer-action-category';
        }else{
            $property_action_developer = $rewrites[18];
        }

        // Register the Developer Action taxonomy
        // This taxonomy categorizes developers by specialization/action (e.g., "New Construction", "Renovation")
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_action_developer' ) ) {
        register_taxonomy('property_action_developer', 'estate_developer', array(
            'labels' => array(
                'name'              => esc_html__('Developer Action Categories','wpresidence-core'),
                'add_new_item'      => esc_html__('Add New Developer Action','wpresidence-core'),
                'new_item_name'     => esc_html__('New Developer Action','wpresidence-core')
            ),
            'hierarchical'  => true,     // Category-like behavior
            'query_var'     => true,     // Can be queried
            'show_in_rest'  => true,     // Enables REST API
            'rewrite'       => array( 'slug' => $property_action_developer ) // Custom URL structure
           )
        );
        }

        // Get developer city slug from settings or use default
        if(!isset($rewrites[19]) || $rewrites[19]==''){
            $property_city_developer = 'developer-city';
        }else{
            $property_city_developer = $rewrites[19];
        }

        // Register the Developer City taxonomy
        // Geographic organization by city/municipality
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_city_developer' ) ) {
        register_taxonomy('property_city_developer','estate_developer', array(
            'labels' => array(
                'name'              => esc_html__('Developer City','wpresidence-core'),
                'add_new_item'      => esc_html__('Add New Developer City','wpresidence-core'),
                'new_item_name'     => esc_html__('New Developer City','wpresidence-core')
            ),
            'hierarchical'  => true,     // Allows parent-child city relationships
            'query_var'     => true,     // Can be queried
            'rewrite'       => array( 'slug' => $property_city_developer ) // Custom URL structure
            )
        );
        }

        // Get developer area/neighborhood slug from settings or use default
        if(!isset($rewrites[20]) || $rewrites[20]==''){
            $property_area_developer = 'developer-area';
        }else{
            $property_area_developer = $rewrites[20];
        }

        // Register the Developer Neighborhood taxonomy
        // Geographic organization by neighborhood/area within cities
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_area_developer' ) ) {
        register_taxonomy('property_area_developer', 'estate_developer', array(
            'labels' => array(
                'name'              => esc_html__('Developer Neighborhood','wpresidence-core'),
                'add_new_item'      => esc_html__('Add New Developer Neighborhood','wpresidence-core'),
                'new_item_name'     => esc_html__('New Developer Neighborhood','wpresidence-core')
            ),
            'hierarchical'  => true,     // Allows parent-child neighborhood relationships
            'query_var'     => true,     // Can be queried
            'show_in_rest'  => true,     // Enables REST API
            'rewrite'       => array( 'slug' => $property_area_developer ) // Custom URL structure
            )
        );
        }

        // Get developer county/state slug from settings or use default
        if(!isset($rewrites[21]) || $rewrites[21]==''){
            $property_county_state_developer = 'developer-county';
        }else{
            $property_county_state_developer = $rewrites[21];
        }
        
        // Register the Developer County/State taxonomy
        // Geographic organization by county/state/region level
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_county_state_developer' ) ) {
        register_taxonomy('property_county_state_developer', array('estate_developer'), array(
            'labels' => array(
                'name'              => esc_html__('Developer County / State','wpresidence-core'),
                'add_new_item'      => esc_html__('Add New Developer County / State','wpresidence-core'),
                'new_item_name'     => esc_html__('New Developer County / State','wpresidence-core')
            ),
            'hierarchical'  => true,     // Allows parent-child relationships
            'query_var'     => true,     // Can be queried
            'show_in_rest'  => true,     // Enables REST API
            'rewrite'       => array( 'slug' => $property_county_state_developer ) // Custom URL structure
            )
        );
        }
    }
endif; // end   wpestate_create_developer_type



/**
 * Ensures proper taxonomy capabilities are set for the administrator role
 * This function runs early in the WordPress init process to set up required permissions
 *
 * @since 4.0.0
 * @return void
 */
add_action('init', 'wpestate_ensure_developer_taxonomy_caps', 0);
if (!function_exists('wpestate_ensure_developer_taxonomy_caps')):
    function wpestate_ensure_developer_taxonomy_caps() {
        $admin = get_role('administrator');
        if ($admin) {
            // Add developer editing capabilities
            $admin->add_cap('edit_estate_developer');
            $admin->add_cap('read_estate_developer');
            $admin->add_cap('delete_estate_developer');
            $admin->add_cap('edit_estate_developers');
            $admin->add_cap('edit_others_estate_developers');
            $admin->add_cap('publish_estate_developers');
            $admin->add_cap('read_private_estate_developers');
            $admin->add_cap('create_estate_developers');
            $admin->add_cap('delete_estate_developers');
            $admin->add_cap('delete_private_estate_developers');
            $admin->add_cap('delete_published_estate_developers');
            $admin->add_cap('delete_others_estate_developers');
            $admin->add_cap('edit_private_estate_developers');
            $admin->add_cap('edit_published_estate_developers');
            
            // Define taxonomy capabilities array
            $taxonomy_caps = array(
                'manage_property_category_developer',
                'edit_property_category_developer',
                'delete_property_category_developer',
                'assign_property_category_developer',
                'manage_property_action_developer',
                'edit_property_action_developer',
                'delete_property_action_developer',
                'assign_property_action_developer',
                'manage_property_city_developer',
                'edit_property_city_developer',
                'delete_property_city_developer',
                'assign_property_city_developer',
                'manage_property_area_developer',
                'edit_property_area_developer',
                'delete_property_area_developer',
                'assign_property_area_developer',
                'manage_property_county_state_developer',
                'edit_property_county_state_developer',
                'delete_property_county_state_developer',
                'assign_property_county_state_developer'
            );
            
            // Add each capability to admin role
            foreach ($taxonomy_caps as $cap) {
                $admin->add_cap($cap);
            }
        }
    }
endif;


// Reuse the property category form callbacks for developer taxonomies
add_action('property_category_developer_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_category_developer_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_category_developer', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_category_developer', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

add_action('property_action_developer_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_action_developer_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_action_developer', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_action_developer', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

add_action('property_city_developer_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_city_developer_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_city_developer', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_city_developer', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

add_action('property_area_developer_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_area_developer_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_area_developer', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_area_developer', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

add_action('property_county_state_developer_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_county_state_developer_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_county_state_developer', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_county_state_developer', 'wpestate_property_city_save_extra_fields_callback', 10, 2);