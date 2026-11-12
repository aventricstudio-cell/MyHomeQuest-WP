<?php 

/*
*
* Duplicate functions used to avoid fatal errors when theme is not active
*
*/

/**
 * Returns the mapping of taxonomy slugs to their corresponding transient keys.
 * 
 * This function provides a centralized way to manage the relationship between
 * property taxonomies and their transient storage keys. It's used throughout
 * the theme for consistent cache handling of taxonomy terms.
 * 
 * @return array Associative array of taxonomy slugs to transient keys
 */

 if(!function_exists('wpestate_get_taxonomy_transient_mapping')):
    function wpestate_get_taxonomy_transient_mapping() {
        return array(
            'property_action_category'  => 'wpestate_action_terms',
            'property_category'         => 'wpestate_category_terms',
            'property_county_state'     => 'wpestate_county_state_terms',
            'property_city'             => 'wpestate_city_terms',
            'property_area'             => 'wpestate_area_terms',
            'property_status'           => 'wpestate_status_terms',
            'property_features'         => 'wpestate_features_terms'
        );
    }
endif;




/**
 * Retrieves and caches taxonomy terms with transients for improved performance
 * 
 * This function checks for cached terms in WordPress transients before querying the database.
 * If cached terms exist and are valid, they are returned; otherwise, fresh terms are fetched,
 * cached for the specified duration, and then returned.
 * 
 * @param string $taxonomy   The taxonomy name to retrieve terms from
 * @param array  $args       Optional. Arguments to pass to get_terms(). Default is array('hide_empty' => 0)
 * @param int    $cache_time Optional. Time in seconds to keep the cache valid. Default is 12 hours (43200 seconds)
 * 
 * @return array|WP_Error|false Array of term objects on success, WP_Error on failure, or false for unmapped taxonomies
 * 
 * @uses wpestate_get_taxonomy_transient_mapping() To get mapping between taxonomies and their transient keys
 * @uses get_transient() To retrieve cached terms
 * @uses get_terms() To fetch terms from the database when cache is empty
 * @uses set_transient() To cache fetched terms
 * 
 * @since 4.0.0
 */

 if(!function_exists('wpestate_get_cached_terms')) :
    function wpestate_get_cached_terms($taxonomy, $args = array('hide_empty' => 0), $cache_time = 43200) {
        // Define taxonomy to transient key mapping
        $taxonomy_transients = wpestate_get_taxonomy_transient_mapping();
        
        // Check if taxonomy is in our mapping
        if (!isset($taxonomy_transients[$taxonomy])) {
        
            return false;
        }
      
        
        // Create unique key based on taxonomy and args
        $args_hash = md5(serialize($args));
        $transient_key = 'wpestate_'.$taxonomy_transients[$taxonomy] . '_' . $args_hash;
        if ( function_exists( 'wpestate_set_transient_name_multilang' ) ) {
            $transient_key = wpestate_set_transient_name_multilang( $transient_key );
            
        }
        
        // Try to get cached terms
        $terms = get_transient($transient_key);
        
        // If no cache exists or it has expired
        if ($terms === false) {
          
            
            // Get fresh terms
            $terms = get_terms($taxonomy, $args);
            
            // Cache the results
            if (!is_wp_error($terms)) {
                set_transient($transient_key, $terms, $cache_time);
            }
        }else{
            //error_log("WP Rentals: Service from cache transients for transient_key: {$transient_key}");
        }
        
        return $terms;
    }
endif;

 /**
 * Get theme option with improved performance
 * 
 * Retrieves theme option values using lazy-loading cache
 * to reduce database queries while maintaining compatibility
 * with existing code.
 * 
 * @param string $theme_option  The option name to retrieve
 * @param mixed  $option        Optional sub-option key
 * @param mixed  $in_case_not   Default value if option not found
 * @return mixed                The option value
 */
if (!function_exists('wpresidence_get_option')):
    function wpresidence_get_option($theme_option, $option = false, $in_case_not = false) {
        // Get options using lazy-loading singleton
        $wpresidence_admin = wpresidence_get_admin_options();
        $theme_option = trim($theme_option);
        
        $post_id='';
        global $post;
        if(isset($post->ID)){
            $post_id=$post->ID;
        }


        // Handle special cases that need custom processing

        if($theme_option=='wp_estate_search_fields_no_per_row'){
            if (wpestate_half_map_conditions($post_id) &&  $wpresidence_admin['wp_estate_half_map_search_version'] ) {
                  return $wpresidence_admin['wp_estate_half_map_search_fields_no']; ;
            }

        } else if ($theme_option == 'wpestate_currency' || $theme_option == 'wp_estate_multi_curr') {
            // Currency requires special conversion
            $return = wpestate_reverse_convert_redux_wp_estate_multi_curr();
            return $return;
        } else if ($theme_option == 'wpestate_custom_fields_list' || $theme_option == 'wp_estate_custom_fields') {

            if(function_exists('wpestate_reverse_convert_redux_wp_estate_custom_fields')){
                // Custom fields require special conversion
                $return = wpestate_reverse_convert_redux_wp_estate_custom_fields();
                return $return;
            }
        } else if ($theme_option == 'wp_estate_url_rewrites') {
            // URL rewrites stored in separate option
            $return = get_option('wp_estate_url_rewrites', true);
            return $return;
        } else if ($theme_option == 'wp_estate_adv_search_what' || $theme_option == 'wp_estate_adv_search_label' || $theme_option == 'wp_estate_adv_search_how') {
            // Advanced search parameters need custom handling
            $custom_advanced_search = isset($wpresidence_admin['wp_estate_custom_advanced_search']) 
                ? $wpresidence_admin['wp_estate_custom_advanced_search'] 
                : null;
            if(function_exists('wpestate_return_search_parameters')){
                return wpestate_return_search_parameters($wpresidence_admin, $theme_option, $custom_advanced_search);
            }
        }
          
        
        // Standard option retrieval
        if (isset($wpresidence_admin[$theme_option]) && $wpresidence_admin[$theme_option] != '') {
            $return = $wpresidence_admin[$theme_option];
            
            // Handle nested options if requested
            if ($option && isset( $wpresidence_admin[$theme_option][$option])) {
                $return = $wpresidence_admin[$theme_option][$option];
            }
        } else {
            // Return default if option not found
            $return = $in_case_not;
        }
        
        return $return;
    }
endif;


/**
 * Lazy-loading singleton pattern for Redux options
 * 
 * This function retrieves theme options only when first needed
 * and caches them in memory for subsequent calls.
 * 
 * @return array The WPResidence theme options
 */
if(!function_exists('wpresidence_get_admin_options')): 
    function wpresidence_get_admin_options() {
        // Static variable persists between function calls
        // Will only query database on first call
        static $wpresidence_admin = null;
       
        // Load options from database only once
        // Load options from database only once
        if ($wpresidence_admin === null) {
            $wpresidence_admin = get_option('wpresidence_admin', array());
        }
        
        return $wpresidence_admin;
    }
endif;



/*

 * 
 * 
 * 
 * 
 * 
 * 
 * 
 **/    
if(!function_exists('wpestate_return_taxonomy_terms_elementor')):    
    function wpestate_return_taxonomy_terms_elementor($taxonomy){

           
            $transient_key = wpestate_set_transient_name_multilang('wpestate_elementor_tax_');
        
        
            $return_array=get_transient($transient_key);
             
    
            if($return_array==false || $return_array=='' ){

                $terms = get_terms( array(
                    'taxonomy' => $taxonomy,
                    'hide_empty' => false,
                    'orderby'   =>'name',
                    'order'     =>'ASC'
                ) );
                
        

                $return_array=array();
        
                if($terms){
                    
                    foreach($terms as $key=>$term){
                        
                        $return_array[$term->term_id]=$term->name;
                    }
                }
                set_transient($transient_key,$return_array,60*60*6);
            }
            return $return_array;
            
    }
endif;