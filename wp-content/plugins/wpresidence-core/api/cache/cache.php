<?php
/**
 * src: api\cache\cache.php
 * Purpose: Main cache configuration and initialization
 * Defines cached post types and their associated data
 * Provides core cache utility functions
 */

require_once WPESTATE_PLUGIN_PATH . 'api/cache/cache_actions.php';
require_once WPESTATE_PLUGIN_PATH . 'api/cache/cache_admin_interface.php';
require_once WPESTATE_PLUGIN_PATH . 'api/cache/cache_trigger.php';
require_once WPESTATE_PLUGIN_PATH . 'api/cache/cache_get_data_functions.php';
require_once WPESTATE_PLUGIN_PATH . 'api/cache/cache_reset.php';
require_once WPESTATE_PLUGIN_PATH . 'api/cache/performance_tests.php';

/**
 * Defines post types and data structures for caching
 * @return array Post types with their meta and taxonomy configurations
 * @since 4.0.0
 */
if(!function_exists('wpestate_api_get_cached_post_types_and_data')){
	function wpestate_api_get_cached_post_types_and_data(){
		
		$data= array(

			'estate_property'  => array(
								'taxonomies' => array(
													'property_category',
													'property_action_category', 
													'property_city',
													'property_area',
													'property_features',	
													'property_status',
													'property_county_state'
												),
				
								'meta'       => array(					
													'property_price',
													'property_second_price',
													'property_price_after_label',
													'property_price_before_label',
													'property_label_before_second_price',
													'property_second_price_label',
													'property_label',
													'property_label_before',
													'property_year_tax',
													'property_size',
													'property_rooms',
													'property_bedrooms',
													'property_bathrooms',
													'property_bedrooms_details',
													'property_address',
													'property_county',
													'property_state',
													'property_zip',
													'property_country',
													'property_status',
													'property_latitude',
													'property_longitude',
													'google_camera_angle',
													'property_agent',
													'property_agent_secondary',
													'property_user',
													'energy_class',
													'energy_index',
													'embed_video_type',
													'embed_video_id',
													'property_custom_video',
													'embed_virtual_tour',
													'use_floor_plans',
													'plan_title',
													'plan_description',
													'plan_image',
													'plan_image_attach',
													'plan_size',
													'plan_rooms',
													'plan_bath',
													'plan_price',
													'property_subunits_list',
													'property_subunits_master',
													'property_has_subunits',
													'property_subunits_list_manual',
													'prop_featured',
													'property_theme_slider',
													'private_notes',
													'wpestate_property_gallery',
													'property_custom_details',
													'property_taxes',
													'property_hoa',
													'property_area',
													'property_city',
													'property_map',
													'property_google_view',
                                                                                                        'property_hide_map_marker',
                                                                                                        'property_lot_size',
                                                                                                        'owner_notes',
                                                                                                        'property_booking_shortcode',
                                                                                                        'co2_class',
                                                                                                        'co2_index',
                                                                                                        'renew_energy_index',
                                                                                                        'building_energy_index',
													'epc_current_rating',
													'epc_potential_rating'
								),
				
								'custom_meta'=> wpestate_api_return_custom_fields_array_for_cache(),
			
			),


			'estate_agent' => array(
						'taxonomies' => array(
							'property_category_agent',
							'property_action_category_agent',
							'property_city_agent',
							'property_area_agent',
							'property_county_state_agent'
						),
						'meta' => array(
							'first_name',
							'last_name',
							'agent_position',
							'agent_email',
							'agent_phone',
							'agent_mobile',
							'agent_skype',
							'agent_member',
							'agent_address',
							'agent_facebook',
							'agent_twitter',
							'agent_linkedin',
							'agent_pinterest',
							'agent_instagram',
							'agent_youtube',
							'agent_tiktok',
							'agent_telegram',
							'agent_vimeo',
							'agent_website',
							'agent_private_notes',
							'user_meda_id',
							'agent_custom_data'
						)
			),

			'estate_agency' => array(
					'taxonomies' => array(
						'category_agency',
						'action_category_agency',
						'city_agency',
						'area_agency',
						'county_state_agency'
					),
					'meta' => array(
						'agency_name',
						'agency_address',
						'agency_email',
						'agency_phone',
						'agency_mobile',
						'agency_skype',
						'agency_facebook',
						'agency_twitter',
						'agency_linkedin',
						'agency_pinterest',
						'agency_instagram',
						'agency_website',
						'agency_languages',
						'agency_license',
						'agency_opening_hours',
						'agency_taxes',
						'agency_description',
						'agency_lat',
						'agency_long',
						'user_meda_id',
						'agency_custom_data'
					)
			),
			
			'estate_developer' => array(
				'taxonomies' => array(
					'property_category_developer',
					'property_action_developer',
					'property_city_developer',
					'property_area_developer',
					'property_county_state_developer'
				),
				'meta' => array(
					'developer_name',
					'developer_address',
					'developer_email',
					'developer_phone',
					'developer_mobile',
					'developer_skype',
					'developer_facebook',
					'developer_twitter',
					'developer_linkedin',
					'developer_pinterest',
					'developer_instagram',
					'developer_website',
					'developer_license',
					'developer_taxes',
					'developer_languages',
					'developer_opening_hours',
					'developer_description',
					'developer_lat',
					'developer_long',
					'user_meda_id',
					'developer_custom_data'
				)
			)
					
					

		);
	
		return $data;
	}
}




/**
 * Retrieves custom fields for caching
 * @return array Custom field slugs
 * @since 4.0.0
 */
function wpestate_api_return_custom_fields_array_for_cache(){
	$custom_fields = wpresidence_get_option('wp_estate_custom_fields', '');
	$custom_fields_array=array();

	if (is_array($custom_fields)) {
		foreach ($custom_fields as $key => $custom_field ){
	
			if(isset($custom_field[0])  && $custom_field[0]!=''){
				$name 		  =   $custom_field[0];
				if (function_exists('wpestate_limit45')) {
					$slug = wpestate_limit45(sanitize_title($name));
				} else {
					$slug = sanitize_title($name);
				}
				$slug         =   sanitize_key($slug);
				$custom_fields_array[]=$slug;
			}
			
		}
	}

	return $custom_fields_array;

}




/**
 * Checks if post type supports caching
 * @param string $post_type Post type to check
 * @return boolean True if caching is supported
 * @since 4.0.0
 */
function wpestate_api_permit_cache_operations($post_type){
	if( in_array($post_type,array_keys( wpestate_api_get_cached_post_types_and_data() ) ) ){
		return true;
	}
	return false;
}



/**
 * Generate a cache key for a custom post type and post ID.
 *
 * @param string $post_type The custom post type.
 * @param int    $post_id   The ID of the post.
 *
 * @return string The generated cache key.
 */
if(!function_exists('wpestate_api_get_cache_key')){
	function wpestate_api_get_cache_key($post_type, $post_id){
		return "wpestate_api_{$post_type}_{$post_id}_cache";
	}
}





/**
 *
 * Manually trigger cache recreation for a given post ID.
 * This function allows manually refreshing the cache by clearing the
 * old cache and generating a new one.
 *
 * @param int    $post_id   The ID of the post.
 * @param string $post_type The post type of the post.
 */
if(!function_exists('wpestate_api_manually_refresh_cache')){
	function wpestate_api_manually_refresh_cache($post_id, $post_type){
		// Check if the post type is one that should be cached
		if(!wpestate_api_permit_cache_operations($post_type)){  
			return;
		}

		wpestate_api_set_cache_post_data($post_id, $post_type); // Re-cache the data
	}
}



if (!function_exists('wpestate_request_transient_cache')):

    function wpestate_request_transient_cache($transient_name) {

        if (wpresidence_get_option('wp_estate_disable_theme_cache') == 'yes') {
            return false;
        } else {
            return get_transient($transient_name);
        }
    }

endif;

function wpestate_set_transient_cache($transient_name, $value, $time) {
    if (wpresidence_get_option('wp_estate_disable_theme_cache') !== 'yes') {
        set_transient($transient_name, $value, $time);
    }
}