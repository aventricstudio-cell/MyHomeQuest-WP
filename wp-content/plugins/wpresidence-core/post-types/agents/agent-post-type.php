<?php 
/**
 * Agent Post Type and Taxonomies Registration
 *
 * This file defines and registers the 'estate_agent' custom post type
 * along with its associated taxonomies for the WP Estate real estate management system.
 * 
 * The file handles:
 * 1. Custom post type registration with appropriate labels and supports
 * 2. Agent category taxonomy registration
 * 3. Agent action taxonomy registration
 * 4. Geographic taxonomies (city, neighborhood, county/state)
 * 5. Proper rewrite rules for SEO-friendly URLs
 *
 * Each taxonomy is hierarchical and REST API enabled for modern development approaches.
 *
 * @package WPResidence
 * @subpackage CustomPostTypes
 * @since 1.0.0
 */

// Register the custom post type on WordPress init hook with priority 20
add_action( 'init', 'wpestate_create_agent_type', 20 );

// Hook taxonomy forms to the generic property callbacks
add_action('property_category_agent_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_category_agent_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_category_agent', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_category_agent', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

add_action('property_action_category_agent_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_action_category_agent_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_action_category_agent', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_action_category_agent', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

add_action('property_city_agent_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_city_agent_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_city_agent', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_city_agent', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

add_action('property_area_agent_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_area_agent_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_area_agent', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_area_agent', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

add_action('property_county_state_agent_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_county_state_agent_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_county_state_agent', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_county_state_agent', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

if( !function_exists('wpestate_create_agent_type') ):
/**
 * Register Agent Custom Post Type and Taxonomies
 * 
 * This function handles the complete registration process for:
 * - The 'estate_agent' custom post type
 * - Five hierarchical taxonomies for organizing agents:
 *   1. Category (property_category_agent)
 *   2. Action (property_action_category_agent)
 *   3. City (property_city_agent)
 *   4. Neighborhood (property_area_agent)
 *   5. County/State (property_county_state_agent)
 *
 * All registrations use translatable strings and implement
 * custom rewrite rules from the wpestate_safe_rewite() function.
 *
 * @since 1.0.0
 * @return void
 */
function wpestate_create_agent_type() {
    if ( function_exists( 'wpresidence_ptc_is_post_type_enabled' ) && ! wpresidence_ptc_is_post_type_enabled( 'estate_agent' ) ) {
        return;
    }
    // Get custom rewrite rules from WP Estate system
    $rewrites   =   wpestate_safe_rewite();
    


    $labels =    array(
            'name'          => esc_html__( 'Agents','wpresidence-core'),
            'singular_name' => esc_html__( 'Agent','wpresidence-core'),
            'add_new'       => esc_html__('Add New Agent','wpresidence-core'),
            'add_new_item'          =>  esc_html__('Add Agent','wpresidence-core'),
            'edit'                  =>  esc_html__('Edit' ,'wpresidence-core'),
            'edit_item'             =>  esc_html__('Edit Agent','wpresidence-core'),
            'new_item'              =>  esc_html__('New Agent','wpresidence-core'),
            'view'                  =>  esc_html__('View','wpresidence-core'),
            'view_item'             =>  esc_html__('View Agent','wpresidence-core'),
            'search_items'          =>  esc_html__('Search Agent','wpresidence-core'),
            'not_found'             =>  esc_html__('No Agents found','wpresidence-core'),
            'not_found_in_trash'    =>  esc_html__('No Agents found','wpresidence-core'),
            'parent'                =>  esc_html__('Parent Agent','wpresidence-core'),
            'featured_image'        => esc_html__('Featured Image','wpresidence-core'),
            'set_featured_image'    => esc_html__('Set Featured Image','wpresidence-core'),
            'remove_featured_image' => esc_html__('Remove Featured Image','wpresidence-core'),
            'use_featured_image'    => esc_html__('Use Featured Image','wpresidence-core'),
        
    );



    // Define the capabilities for this post type
    $capabilities = array(
        'edit_post'              => 'edit_estate_agent',
        'read_post'              => 'read_estate_agent',
        'delete_post'            => 'delete_estate_agent',
        'edit_posts'             => 'edit_estate_agents',
        'edit_others_posts'      => 'edit_others_estate_agents',
        'publish_posts'          => 'publish_estate_agents',
        'read_private_posts'     => 'read_private_estate_agents',
        'delete_posts'           => 'delete_estate_agents',
        'delete_private_posts'   => 'delete_private_estate_agents',
        'delete_published_posts' => 'delete_published_estate_agents',
        'delete_others_posts'    => 'delete_others_estate_agents',
        'edit_private_posts'     => 'edit_private_estate_agents',
        'edit_published_posts'   => 'edit_published_estate_agents',
    );





    // Register the main estate_agent custom post type
    register_post_type( 'estate_agent',
            array(
                'labels' => $labels,
                'public' => true,                   // Makes the post type publicly accessible
                'has_archive' => true,              // Enables archive page for agents
                'rewrite' => array('slug' => $rewrites[6]), // Custom permalink structure
                'supports' => array('title', 'editor', 'thumbnail','comments','excerpt'), // Supported features
                'can_export' => true,               // Allows export via WordPress tools
                'register_meta_box_cb' => 'wpestate_add_agents_metaboxes', // Callback for custom metaboxes
                'menu_icon'=> WPESTATE_PLUGIN_DIR_URL.'/img/agents.png',   // Custom menu icon
                'show_in_rest'=>true,               // Enables Gutenberg/REST API support
                'map_meta_cap' => true,
                'capabilities' => $capabilities
            )
    );


    
    // Register the Agent Category taxonomy
    // This taxonomy allows categorizing agents (e.g., "Commercial", "Residential", etc.)
    if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_category_agent' ) ) {
    register_taxonomy('property_category_agent', array('estate_agent'), array(
        'labels' => array(
            'name'              => esc_html__('Agent Categories','wpresidence-core'),
            'add_new_item'      => esc_html__('Add New Agent Category','wpresidence-core'),
            'new_item_name'     => esc_html__('New Agent Category','wpresidence-core')
        ),
        'hierarchical'  => true,     // Category-like behavior (not tag-like)
        'query_var'     => true,     // Can be queried
        'show_in_rest'  => true,     // Enables REST API
        'rewrite'       => array( 'slug' => $rewrites[7] ) // Custom URL structure
        )
    );
    }

    // Register the Agent Action taxonomy
    // This taxonomy categorizes agents by action type (e.g., "Selling", "Renting", etc.)
    if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_action_category_agent' ) ) {
    register_taxonomy('property_action_category_agent', 'estate_agent', array(
        'labels' => array(
            'name'              => esc_html__('Agent Action Categories','wpresidence-core'),
            'add_new_item'      => esc_html__('Add New Agent Action','wpresidence-core'),
            'new_item_name'     => esc_html__('New Agent Action','wpresidence-core')
        ),
        'hierarchical'  => true,     // Category-like behavior
        'query_var'     => true,     // Can be queried
        'show_in_rest'  => true,     // Enables REST API
        'rewrite'       => array( 'slug' => $rewrites[8] ) // Custom URL structure
       )
    );
    }

    // Register the Agent City taxonomy
    // Geographic organization by city
    if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_city_agent' ) ) {
    register_taxonomy('property_city_agent','estate_agent', array(
        'labels' => array(
            'name'              => esc_html__('Agent City','wpresidence-core'),
            'add_new_item'      => esc_html__('Add New Agent City','wpresidence-core'),
            'new_item_name'     => esc_html__('New Agent City','wpresidence-core')
        ),
        'hierarchical'  => true,     // Allows parent-child city relationships
        'query_var'     => true,     // Can be queried
        'show_in_rest'  => true,     // Enables REST API
        'rewrite'       => array( 'slug' => $rewrites[9],'with_front' => false) // Prevents "/blog/" in URLs
        )
    );
    }

    // Register the Agent Neighborhood taxonomy
    // Geographic organization by neighborhood/area within cities
    if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_area_agent' ) ) {
    register_taxonomy('property_area_agent', 'estate_agent', array(
        'labels' => array(
            'name'              => esc_html__('Agent Neighborhood','wpresidence-core'),
            'add_new_item'      => esc_html__('Add New Agent Neighborhood','wpresidence-core'),
            'new_item_name'     => esc_html__('New Agent Neighborhood','wpresidence-core')
        ),
        'hierarchical'  => true,     // Allows parent-child neighborhood relationships
        'query_var'     => true,     // Can be queried
        'show_in_rest'  => true,     // Enables REST API
        'rewrite'       => array( 'slug' => $rewrites[10] ) // Custom URL structure
        )
    );
    }

    // Register the Agent County/State taxonomy
    // Geographic organization by county/state level
    if ( function_exists( 'wpresidence_ptc_is_taxonomy_enabled' ) && wpresidence_ptc_is_taxonomy_enabled( 'property_county_state_agent' ) ) {
    register_taxonomy('property_county_state_agent', array('estate_agent'), array(
        'labels' => array(
            'name'              => esc_html__('Agent County / State','wpresidence-core'),
            'add_new_item'      => esc_html__('Add New Agent County / State','wpresidence-core'),
            'new_item_name'     => esc_html__('New Agent County / State','wpresidence-core')
        ),
        'hierarchical'  => true,     // Allows parent-child relationships
        'query_var'     => true,     // Can be queried
        'show_in_rest'  => true,     // Enables REST API
        'rewrite'       => array( 'slug' =>  $rewrites[11] ) // Custom URL structure
        )
    );
    }
}
endif; // end   wpestate_create_agent_type



/**
 * Ensures proper taxonomy capabilities are set for the administrator role
 * This function runs early in the WordPress init process to set up required permissions
 * for agent post type and taxonomies
 *
 * @since 4.0.0
 * @return void
 */
add_action('init', 'wpestate_ensure_agent_taxonomy_caps', 0);
if (!function_exists('wpestate_ensure_agent_taxonomy_caps')):
    function wpestate_ensure_agent_taxonomy_caps() {
        $admin = get_role('administrator');
        if ($admin) {
            // Add agent post type editing capabilities
            $admin->add_cap('edit_estate_agent');
            $admin->add_cap('read_estate_agent');
            $admin->add_cap('delete_estate_agent');
            $admin->add_cap('edit_estate_agents');
            $admin->add_cap('edit_others_estate_agents');
            $admin->add_cap('publish_estate_agents');
            $admin->add_cap('read_private_estate_agents');
            $admin->add_cap('create_estate_agents');
            $admin->add_cap('delete_estate_agents');
            $admin->add_cap('delete_private_estate_agents');
            $admin->add_cap('delete_published_estate_agents');
            $admin->add_cap('delete_others_estate_agents');
            $admin->add_cap('edit_private_estate_agents');
            $admin->add_cap('edit_published_estate_agents');
            
            // Define taxonomy capabilities array
            $taxonomy_caps = array(
                'manage_property_category_agent',
                'edit_property_category_agent',
                'delete_property_category_agent',
                'assign_property_category_agent',
                'manage_property_action_category_agent',
                'edit_property_action_category_agent',
                'delete_property_action_category_agent',
                'assign_property_action_category_agent',
                'manage_property_city_agent',
                'edit_property_city_agent',
                'delete_property_city_agent',
                'assign_property_city_agent',
                'manage_property_area_agent',
                'edit_property_area_agent',
                'delete_property_area_agent',
                'assign_property_area_agent',
                'manage_property_county_state_agent',
                'edit_property_county_state_agent',
                'delete_property_county_state_agent',
                'assign_property_county_state_agent'
            );
            
            // Add each capability to admin role
            foreach ($taxonomy_caps as $cap) {
                $admin->add_cap($cap);
            }
        }
    }
endif;