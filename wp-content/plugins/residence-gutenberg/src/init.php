<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * `wp-blocks`: includes block type registration and related functions.
 *
 * @since 1.0.0
 */
function residence_latest_items_cgb_block_assets() {
	// Styles.
	wp_enqueue_style(
		'residence_latest_items-cgb-style-css', // Handle.
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		array(  ) // Dependency to include the CSS after it. wp-blocks
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: filemtime — Gets file modification time.
	);
} // End function residence_latest_items_cgb_block_assets().

// Hook: Frontend assets.
add_action( 'enqueue_block_assets', 'residence_latest_items_cgb_block_assets' );

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * `wp-blocks`: includes block type registration and related functions.
 * `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
 * `wp-i18n`: To internationalize the block's text.
 *
 * @since 1.0.0
 */

function residence_gutenberg_blocks() {
	// Scripts.
 
	wp_enqueue_script(
		'residence_gutenberg_blocks-js', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element','wp-editor' ), // Dependencies, defined above.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);
        $out_agent_tax_array                    =   wpestate_js_composer_out_agent_tax_array();
        $property_category_agent                =   '';
        $property_action_category_agent         =   '';
        $property_city_agent                    =   '';
        $property_area_agent                    =   '';
        $property_category_agent_label          =   '';
        $property_action_category_agent_label   =   '';
        $property_city_agent_label              =   '';
        $property_area_agent_label              =   '';
                
        if(isset($out_agent_tax_array['property_category_agent'])){
            $property_category_agent=$out_agent_tax_array['property_category_agent'];
        }
        if(isset($out_agent_tax_array['property_action_category_agent'])){
            $property_action_category_agent=$out_agent_tax_array['property_action_category_agent'];
        }
        if(isset($out_agent_tax_array['property_city_agent'])){
            $property_city_agent=$out_agent_tax_array['property_city_agent'];
        }
        if(isset($out_agent_tax_array['property_area_agent'])){
            $property_area_agent=$out_agent_tax_array['property_area_agent'];
        }

        if(isset($out_agent_tax_array['property_category_agent_label'])){
            $property_category_agent_label=$out_agent_tax_array['property_category_agent_label'];
        }
        if(isset($out_agent_tax_array['property_action_category_agent_label'])){
            $property_action_category_agent_label=$out_agent_tax_array['property_action_category_agent_label'];
        }
        if(isset($out_agent_tax_array['property_city_agent_label'])){
            $property_city_agent_label=$out_agent_tax_array['property_city_agent_label'];
        }
        if(isset($out_agent_tax_array['property_area_agent_label'])){
            $property_area_agent_label=$out_agent_tax_array['property_area_agent_label'];
        }
        
        
        wp_localize_script('residence_gutenberg_blocks-js', 'residence_gutenberg_vars', 
            array(  
                'plugin_dir_path'           =>  plugin_dir_url(__DIR__),
                'membership_auto'           =>  json_encode( wpestate_generate_membershiop_packages_shortcodes() ),
                'pack_labels'               =>  json_encode(  get_transient('wpestate_js_composer_pack_label') ),
                'featured_developer_auto'   =>  json_encode( wpestate_generate_developers_array()),
                'control_terms'             =>  json_encode( wpestate_generate_all_tax() ),
                'control_terms_labels'      =>  json_encode( wpestate_generate_all_tax_labels() ),
                'category_list'             =>  json_encode( wpestate_generate_category_values_shortcode() ),
                'category_labels'           =>  json_encode( get_transient('wpestate_js_composer_property_category_label')),
                'action_list'               =>  json_encode( wpestate_generate_action_values_shortcode() ),
                'action_labels'             =>  json_encode( get_transient('wpestate_js_composer_property_action_label')),
                'city_list'                 =>  json_encode( wpestate_generate_city_values_shortcode() ),
                'city_labels'               =>  json_encode( get_transient('wpestate_js_composer_property_city_label')),
                'area_list'                 =>  json_encode( wpestate_generate_area_values_shortcode() ),
                'area_labels'               =>  json_encode( get_transient('wpestate_js_composer_property_area_label')),
                'county_list'               =>  json_encode( wpestate_generate_county_values_shortcode() ),
                'county_labels'             =>  json_encode( get_transient('wpestate_js_composer_property_county_label')),
                'agent_array'               =>  json_encode( wpestate_return_agent_array()),
                'article_array'             =>  json_encode( wpestate_return_article_array()),
                'agent_category_list'       =>  json_encode( $property_category_agent ),
                'agent_action_list'         =>  json_encode( $property_action_category_agent),
                'agent_city_list'           =>  json_encode( $property_city_agent),
                'agent_area_list'           =>  json_encode( $property_area_agent),
                'agent_category_list_label' =>  json_encode( $property_category_agent_label),
                'agent_action_list_label'   =>  json_encode( $property_action_category_agent_label),
                'agent_city_list_label'     =>  json_encode( $property_city_agent_label),
                'agent_area_list_label'     =>  json_encode( $property_area_agent_label),
               
               
                
                
            )
        );
	


        // Styles.
	wp_enqueue_style(
		'residence_gutenberg_blocks-editor-css', // Handle.
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: filemtime — Gets file modification time.
	);
        wp_enqueue_style(
		'residence_gutenberg_blocks-editor-common', // Handle.
		plugins_url( 'common.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: filemtime — Gets file modification time.
	);
} // End function residence_latest_items_cgb_editor_assets().

// Hook: Editor assets.
add_action( 'enqueue_block_editor_assets', 'residence_gutenberg_blocks' );


if ( function_exists( 'register_block_type' ) ) {
    add_filter( 'render_block', function ( $block_content, $block ) {
        
    $plugins =array('residence-gutenberg-block/slider-items',
        'residence-gutenberg-block/recentitems',
        'residence-gutenberg-block/membership-package-settings',
        'residence-gutenberg-block/featured-agency',
        'residence-gutenberg-block/featured-agent',
        'residence-gutenberg-block/featured-article',
        'residence-gutenberg-block/featured-property',
        'residence-gutenberg-block/google-map-property',
        'residence-gutenberg-block/listings-per-agent',
        'residence-gutenberg-block/places-list',
        'residence-gutenberg-block/places-slider',
        'residence-gutenberg-block/list-agents',
        'residence-gutenberg-block/list-items-by-id',
        'residence-gutenberg-block/login-form',
        'residence-gutenberg-block/register-form',
        'residence-gutenberg-block/advanced-search',
        'residence-gutenberg-block/contact-us-form');    
        
    if (in_array($block['blockName'], $plugins) ) {
        remove_filter( 'the_content', 'wpautop' );
    }

        return $block_content;
    }, 10, 2 );

    register_block_type( 'residence-gutenberg-block/slider-items', array(
        'render_callback' => 'wpestate_slider_recent_posts_pictures',
    ) );
    register_block_type( 'residence-gutenberg-block/recentitems', array(
        'render_callback' => 'wpestate_recent_posts_pictures_new',
    ) );
    register_block_type( 'residence-gutenberg-block/membership-package-settings', array(
        'render_callback' => 'wpestate_membership_packages_function',
    ) );
    register_block_type( 'residence-gutenberg-block/featured-agency', array(
        'render_callback' => 'wpestate_featured_user_role_shortcode',
    ) );
    
    register_block_type( 'residence-gutenberg-block/featured-agent', array(
        'render_callback' => 'wpestate_featured_agent',
    ) );
     
    register_block_type( 'residence-gutenberg-block/featured-article', array(
        'render_callback' => 'wpestate_featured_article',
    ) );
    
    register_block_type( 'residence-gutenberg-block/featured-property', array(
        'render_callback' => 'wpestate_featured_property',
    ) );
    
    register_block_type( 'residence-gutenberg-block/google-map-property', array(
        'render_callback' => 'wpestate_property_page_map_function',
    ) );
    
    register_block_type( 'residence-gutenberg-block/listings-per-agent', array(
        'render_callback' => 'wplistingsperagent_shortcode_function',
    ) );
    
    register_block_type( 'residence-gutenberg-block/places-list', array(
        'render_callback' => 'wpestate_places_list_function',
    ) );
    
    register_block_type( 'residence-gutenberg-block/places-slider', array(
        'render_callback' => 'wpestate_places_slider',
    ) );
    
    register_block_type( 'residence-gutenberg-block/list-agents', array(
        'render_callback' => 'wpestate_list_agents_function',
    ) );
    
    register_block_type( 'residence-gutenberg-block/list-items-by-id', array(
        'render_callback' => 'wpestate_list_items_by_id_function',
    ) );
    
    register_block_type( 'residence-gutenberg-block/login-form', array(
        'render_callback' => 'wpestate_login_form_function',
    ) );
    
    register_block_type( 'residence-gutenberg-block/register-form', array(
        'render_callback' => 'wpestate_register_form_function',
    ) );
    
    
    register_block_type( 'residence-gutenberg-block/advanced-search', array(
        'render_callback' => 'wpestate_advanced_search_function',
    ) );
    
    register_block_type( 'residence-gutenberg-block/contact-us-form', array(
        'render_callback' => 'wpestate_contact_us_form',
    ) );
    
  
//    register_block_type( 'residence-gutenberg-block/testimonial-slider', array(
//        'render_callback' => 'wpestate_testimonial_slider_function_gutenberg',
//    ) );
    
//    register_block_type( 'residence-gutenberg-block/testimonial', array(
//        'render_callback'   =>  'wpestate_testimonial_function',
//    ) );
   
}





function wpestate_generate_developers_array(){
    $agent_array = get_transient('wpestate_js_composer_agent_array_gutenberg');
            
    if($agent_array ===false ){
        $args_inner = array(
                        'post_type' => array(
                                            'estate_agency',
                                            'estate_developer'),
                        'showposts' => -1
                    );
        $all_agent_packages = get_posts( $args_inner );
        if( count($all_agent_packages) > 0 ){
                foreach( $all_agent_packages as $single_package ){
                        $temp_array=array();
                        $temp_array['label'] = $single_package->post_title;
                        $temp_array['value'] = $single_package->ID;

                        $agent_array[] = $temp_array;
                }
        }
        set_transient('wpestate_js_composer_agent_array_gutenberg',$agent_array,60*60*4);
    }
    return $agent_array;
}


function wpestate_recent_posts_pictures_new_gut($attributes){
    if(!is_admin()){
        print  wpestate_recent_posts_pictures_new($attributes);
    }
}

function wpestate_slider_recent_posts_pictures_gut($attributes){
    if(!is_admin()){
        print  wpestate_slider_recent_posts_pictures($attributes);
    }
}

function wpestate_membership_packages_function_gut($attributes){
    if(!is_admin()){
        print wpestate_membership_packages_function($attributes);
    }
}

function wpestate_featured_user_role_shortcode_gut($attributes){
    if(!is_admin()){
        print wpestate_featured_user_role_shortcode($attributes);
    }
}

add_filter( 'block_categories', function( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'WpResidence',
				'title' => __( 'WpResidence Blocks', 'wpresidence-gutenberg' ),
			),
		)
	);
}, 10, 2 );