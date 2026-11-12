<?php 
/**
 * Agency Custom Post Type Registration
 *
 * This file handles the registration of the 'estate_agency' custom post type and its
 * associated taxonomies. It defines the structure for agency listings within the
 * WPResidence real estate plugin ecosystem, including categories, actions, locations
 * and geographical hierarchies.
 *
 * The post type allows for agencies to be listed, searched, and filtered through various
 * taxonomical relationships including categories, action types, cities, neighborhoods,
 * and counties/states.
 *
 * @package WPResidence
 * @subpackage CustomPostTypes
 * @version 1.0
 * @author WPResidence Team
 * @copyright Copyright (c) WPResidence
 * @license GPL2+
 */

// register the custom post type
add_action( 'init', 'wpestate_create_agency_type',20);

/**
 * Creates the estate_agency custom post type and its taxonomies
 *
 * This function registers the main agency post type with WordPress and creates
 * five hierarchical taxonomies for categorizing and filtering agencies:
 * - category_agency: General agency categories
 * - action_category_agency: Types of agency actions/services
 * - city_agency: Cities where agencies operate
 * - area_agency: Neighborhoods/areas of operation
 * - county_state_agency: Counties or states of operation
 *
 * Each taxonomy and the post type itself use custom rewrite rules that can be
 * modified through the theme's rewrite settings.
 *
 * @since 1.0
 * @uses register_post_type() To register the custom post type
 * @uses register_taxonomy() To register the custom taxonomies
 * @uses wpestate_safe_rewite() To retrieve custom permalink structure settings
 * @return void
 */
if( !function_exists('wpestate_create_agency_type') ):
    function wpestate_create_agency_type() {
        if ( function_exists( 'wpresidence_ptc_is_post_type_enabled' ) && ! wpresidence_ptc_is_post_type_enabled( 'estate_agency' ) ) {
            return;
        }
        // Get custom permalink structure from plugin settings
        $rewrites   =   wpestate_safe_rewite();
        
        // Set default slug for agency post type
        $slug='agency';
        if(isset( $rewrites[22])){
            $slug=$rewrites[22];
        }

        $labels = array(
            'name'          => esc_html__( 'Agency','wpresidence-core'),
            'singular_name' => esc_html__( 'Agency','wpresidence-core'),
            'add_new'       => esc_html__('Add New Agency','wpresidence-core'),
            'add_new_item'          =>  esc_html__('Add Agency','wpresidence-core'),
            'edit'                  =>  esc_html__('Edit' ,'wpresidence-core'),
            'edit_item'             =>  esc_html__('Edit Agency','wpresidence-core'),
            'new_item'              =>  esc_html__('New Agency','wpresidence-core'),
            'view'                  =>  esc_html__('View','wpresidence-core'),
            'view_item'             =>  esc_html__('View Agency','wpresidence-core'),
            'search_items'          =>  esc_html__('Search Agency','wpresidence-core'),
            'not_found'             =>  esc_html__('No Agency found','wpresidence-core'),
            'not_found_in_trash'    =>  esc_html__('No Agency found','wpresidence-core'),
            'parent'                =>  esc_html__('Parent Agency','wpresidence-core'),
            'featured_image'        => esc_html__('Featured Image','wpresidence-core'),
            'set_featured_image'    => esc_html__('Set Featured Image','wpresidence-core'),
            'remove_featured_image' => esc_html__('Remove Featured Image','wpresidence-core'),
            'use_featured_image'    => esc_html__('Use Featured Image','wpresidence-core'),
                    
        );
   

        // Setup post type capabilities
        $agency_capabilities = array(
            'edit_post'              => 'edit_estate_agency',
            'read_post'              => 'read_estate_agency',
            'delete_post'            => 'delete_estate_agency',
            'edit_posts'             => 'edit_estate_agencies',
            'edit_others_posts'      => 'edit_others_estate_agencies',
            'publish_posts'          => 'publish_estate_agencies',
            'read_private_posts'     => 'read_private_estate_agencies',
            'create_posts'           => 'create_estate_agencies',
            'delete_posts'           => 'delete_estate_agencies',
            'delete_private_posts'   => 'delete_private_estate_agencies',
            'delete_published_posts' => 'delete_published_estate_agencies',
            'delete_others_posts'    => 'delete_others_estate_agencies',
            'edit_private_posts'     => 'edit_private_estate_agencies',
            'edit_published_posts'   => 'edit_published_estate_agencies',
        );
        

        // Register the estate_agency custom post type
        register_post_type( 'estate_agency',
                array(
                    'labels' => $labels,
                    'public' => true,
                    'has_archive' => true,
                    'rewrite' => array('slug' => $slug),
                    'supports' => array('title', 'editor', 'thumbnail','comments','excerpt'),
                    'can_export' => true,
                    'register_meta_box_cb' => 'wpestate_add_agency_metaboxes',
                    'menu_icon'=> WPESTATE_PLUGIN_DIR_URL.'/img/agency.png',
                    'show_in_rest'=>true,
                    'map_meta_cap' => true,
                    'capability_type' => array('estate_agency', 'estate_agencies'),
                    'capabilities' => $agency_capabilities
                )
        );
        
        /**
         * Register Agency Category Taxonomy
         * 
         * This taxonomy allows agencies to be grouped by general categories
         * like "Commercial", "Residential", "Property Management", etc.
         */
        // Check for custom permalink structure for agency categories
        if(!isset($rewrites[12]) || $rewrites[12]==''){
            $category_rewrite='agency-category';
        }else{
            $category_rewrite =  $rewrites[12];
        }
        
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'category_agency' ) ) {
        register_taxonomy('category_agency', array('estate_agency'), array(
            'labels' => array(
                'name'              => esc_html__('Agency Categories','wpresidence-core'),
                'add_new_item'      => esc_html__('Add New Agency Category','wpresidence-core'),
                'new_item_name'     => esc_html__('New Agency Category','wpresidence-core')
            ),
            'hierarchical'  => true,
            'query_var'     => true,
             'show_in_rest'      => true,
             'rewrite'       => array( 'slug' => $category_rewrite )
            )
        );
        }

        /**
         * Register Agency Action Category Taxonomy
         * 
         * This taxonomy classifies agencies by the types of services or actions they perform,
         * such as "Selling", "Renting", "Property Management", "Consulting", etc.
         */
        if(!isset($rewrites[13]) || $rewrites[13]==''){
            $action_category_agency='agency-action-category';
        }else{
            $action_category_agency =  $rewrites[13];
        }

        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'action_category_agency' ) ) {
        register_taxonomy('action_category_agency', 'estate_agency', array(
            'labels' => array(
                'name'              => esc_html__('Agency Action Categories','wpresidence-core'),
                'add_new_item'      => esc_html__('Add New Agency Action','wpresidence-core'),
                'new_item_name'     => esc_html__('New Agency Action','wpresidence-core')
            ),
            'hierarchical'  => true,
            'query_var'     => true,
            'show_in_rest'      => true,
            'rewrite'       => array( 'slug' => $action_category_agency )
           )
        );
        }

        /**
         * Register Agency City Taxonomy
         * 
         * This taxonomy allows filtering agencies by the cities where they operate,
         * enabling location-based searches.
         */
        if(!isset($rewrites[14]) || $rewrites[14]==''){
            $city_agency    =   'agency-city';
        }else{
            $city_agency    =  $rewrites[14];
        }
        
        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'city_agency' ) ) {
        register_taxonomy('city_agency','estate_agency', array(
            'labels' => array(
                'name'              => esc_html__('Agency City','wpresidence-core'),
                'add_new_item'      => esc_html__('Add New Agency City','wpresidence-core'),
                'new_item_name'     => esc_html__('New Agency City','wpresidence-core')
            ),
            'hierarchical'  => true,
            'query_var'     => true,
            'show_in_rest'      => true,
            'rewrite'       => array( 'slug' => $city_agency )
            )
        );
        }

        /**
         * Register Agency Area/Neighborhood Taxonomy
         * 
         * This taxonomy provides a more granular location filter than city,
         * allowing agencies to be associated with specific neighborhoods or areas.
         */
        if(!isset($rewrites[15]) || $rewrites[15]==''){
            $area_agency    =   'agency-area';
        }else{
            $area_agency    =  $rewrites[15];
        }

        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'area_agency' ) ) {
        register_taxonomy('area_agency', 'estate_agency', array(
            'labels' => array(
                'name'              => esc_html__('Agency Neighborhood','wpresidence-core'),
                'add_new_item'      => esc_html__('Add New Agency Neighborhood','wpresidence-core'),
                'new_item_name'     => esc_html__('New Agency Neighborhood','wpresidence-core')
            ),
            'hierarchical'  => true,
            'query_var'     => true,
            'show_in_rest'      => true,
            'rewrite'       => array( 'slug' => $area_agency )

            )
        );
        }
        
        /**
         * Register Agency County/State Taxonomy
         * 
         * This taxonomy enables broader geographical categorization of agencies
         * by county or state, particularly useful for larger regions or multi-state operations.
         */
        if(!isset($rewrites[16]) || $rewrites[16]==''){
            $county_state_agency    =   'agency-county';
        }else{
            $county_state_agency   =  $rewrites[16];
        }

        if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'county_state_agency' ) ) {
        register_taxonomy('county_state_agency', array('estate_agency'), array(
            'labels' => array(
                'name'              => esc_html__('Agency County / State','wpresidence-core'),
                'add_new_item'      => esc_html__('Add New Agency County / State','wpresidence-core'),
                'new_item_name'     => esc_html__('New Agency County / State','wpresidence-core')
            ),
            'hierarchical'  => true,
            'query_var'     => true,
             'show_in_rest'      => true,
             'rewrite'       => array( 'slug' => $county_state_agency )

            )
        );
        }
    }
endif; // end   wpestate_create_agency_type



/**
 * Ensures proper taxonomy capabilities are set for the administrator role
 * This function runs early in the WordPress init process to set up required permissions
 *
 * @since 4.0.0
 * @return void
 */
add_action('init', 'wpestate_ensure_agency_taxonomy_caps', 0);
if (!function_exists('wpestate_ensure_agency_taxonomy_caps')):
    function wpestate_ensure_agency_taxonomy_caps() {
        $admin = get_role('administrator');
        if ($admin) {
            // Add agency editing capabilities
            $admin->add_cap('edit_estate_agency');
            $admin->add_cap('read_estate_agency');
            $admin->add_cap('delete_estate_agency');
            $admin->add_cap('edit_estate_agencies');
            $admin->add_cap('edit_others_estate_agencies');
            $admin->add_cap('publish_estate_agencies');
            $admin->add_cap('read_private_estate_agencies');
            $admin->add_cap('create_estate_agencies');
            $admin->add_cap('delete_estate_agencies');
            $admin->add_cap('delete_private_estate_agencies');
            $admin->add_cap('delete_published_estate_agencies');
            $admin->add_cap('delete_others_estate_agencies');
            $admin->add_cap('edit_private_estate_agencies');
            $admin->add_cap('edit_published_estate_agencies');
            
            // Define taxonomy capabilities array
            $taxonomy_caps = array(
                'manage_category_agency',
                'edit_category_agency',
                'delete_category_agency',
                'assign_category_agency',
                'manage_action_category_agency',
                'edit_action_category_agency',
                'delete_action_category_agency',
                'assign_action_category_agency',
                'manage_city_agency',
                'edit_city_agency',
                'delete_city_agency',
                'assign_city_agency',
                'manage_area_agency',
                'edit_area_agency',
                'delete_area_agency',
                'assign_area_agency',
                'manage_county_state_agency',
                'edit_county_state_agency',
                'delete_county_state_agency',
                'assign_county_state_agency'
            );
            
            // Add each capability to admin role
            foreach ($taxonomy_caps as $cap) {
                $admin->add_cap($cap);
            }
        }
    }
endif;


add_action('category_agency_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('category_agency_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_category_agency', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_category_agency', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

add_action('action_category_agency_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('action_category_agency_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_action_category_agency', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_action_category_agency', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

add_action('city_agency_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('city_agency_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_city_agency', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_city_agency', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

add_action('area_agency_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('area_agency_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_area_agency', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_area_agency', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

add_action('county_state_agency_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('county_state_agency_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_county_state_agency', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_county_state_agency', 'wpestate_property_city_save_extra_fields_callback', 10, 2);