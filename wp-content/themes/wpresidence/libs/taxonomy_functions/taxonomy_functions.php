<?php
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
        $transient_key = wpestate_set_transient_name_multilang($transient_key);
     



        
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
     
        }
        
        return $terms;
    }
endif;