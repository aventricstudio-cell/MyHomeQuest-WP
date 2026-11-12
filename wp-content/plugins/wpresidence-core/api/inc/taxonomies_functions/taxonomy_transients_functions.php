<?php 





/**
 * Resets the cached transients for property taxonomies
 * 
 * This function handles cache invalidation for all property-related taxonomies
 * including both the taxonomy-specific transients and the dropdown select list transients.
 * It detects and clears multilingual (WPML) transients as well.
 *
 * @param int    $term_id  Term ID being modified
 * @param int    $tt_id    Term taxonomy ID
 * @param string $taxonomy Taxonomy slug
 */
function wpresidence_reset_taxonomy_transients($term_id, $tt_id, $taxonomy) {
    global $wpdb;
    $taxonomy_transients = wpestate_get_taxonomy_transient_mapping();
   
    // Check if this taxonomy has a corresponding transient
    if (isset($taxonomy_transients[$taxonomy])) {
        $transient_key_base = $taxonomy_transients[$taxonomy];
       
        // Get all transients that start with our base key
        $transient_like = $wpdb->esc_like('_transient_' . $transient_key_base) . '%';
        $sql = $wpdb->prepare(
            "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
            $transient_like
        );
        $transients = $wpdb->get_col($sql);
       
        // Delete each matching transient
        foreach($transients as $transient) {
            $transient_name = str_replace('_transient_', '', $transient);
            delete_transient($transient_name);
        
        }
    }
   
    // Clear select list transients
    $select_list_transients = array(
        'wpestate_get_action_select_list',
        'wpestate_get_category_select_list',
        'wpestate_get_city_select_list',
        'wpestate_get_area_select_list',
        'wpestate_get_county_state_select_list',
        'wpestate_get_status_select_list'
    );
    
    // Add dropdown multiple select transients
    $dropdown_taxonomies = array(
        'property_action_category',
        'property_category',
        'property_city',
        'property_area',
        'property_county_state',
        'property_status'
    );
    
    foreach ($dropdown_taxonomies as $dropdown_taxonomy) {
        $select_list_transients[] = 'wpestate_get_dropdown_multiple_select_list_' . $dropdown_taxonomy;
    }
   
    // Get active languages if WPML or WPR translation plugin is enabled
    $active_languages = array('');  // Default empty string for non-multilingual sites
   
    if (defined('ICL_LANGUAGE_CODE')) {
        // If WPML is active, try to get all active languages
        if (function_exists('icl_get_languages')) {
            $wpml_languages = icl_get_languages('skip_missing=0');
            if (is_array($wpml_languages)) {
                $active_languages = array_keys($wpml_languages);
            } else {
                // Fallback to just current language if we can't get all languages
                $active_languages = array(ICL_LANGUAGE_CODE);
            }
        } else {
            // Fallback to just current language
            $active_languages = array(ICL_LANGUAGE_CODE);
        }
    }
    if (function_exists('wpestate_translation_get_active_languages')) {
        $wpr_languages = wpestate_translation_get_active_languages();
        if (is_array($wpr_languages)) {
            foreach ($wpr_languages as $language) {
                $code = isset($language['code']) ? sanitize_key($language['code']) : '';
                if ('' !== $code) {
                    $active_languages[] = $code;
                }
            }
        }
    }
    $active_languages = array_values(array_unique($active_languages));
   
    // Clear all transients for all languages
    foreach ($select_list_transients as $transient_base) {
        foreach ($active_languages as $lang_code) {
            $transient_key = $transient_base;
            if (!empty($lang_code)) {
                $transient_key .= '_' . $lang_code;
            }
            delete_transient($transient_key);
       
        }
    }
    
    // Also clear using direct SQL for dropdown transients with language code in the middle
    if (defined('ICL_LANGUAGE_CODE') || function_exists('wpestate_translation_get_active_languages')) {
        foreach ($dropdown_taxonomies as $dropdown_taxonomy) {
            $pattern = $wpdb->esc_like('_transient_wpestate_get_dropdown_multiple_select_list_') . '%' . 
                       $wpdb->esc_like($dropdown_taxonomy) . '%';
            
            $sql = $wpdb->prepare(
                "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
                $pattern
            );
            
            $dropdown_transients = $wpdb->get_col($sql);
            
            foreach ($dropdown_transients as $transient) {
                $transient_name = str_replace('_transient_', '', $transient);
                delete_transient($transient_name);
            
            }
        }
    }
}


// Hook into all term modification events
add_action('create_term', 'wpresidence_reset_taxonomy_transients', 10, 3);
add_action('edited_term', 'wpresidence_reset_taxonomy_transients', 10, 3);
add_action('delete_term', 'wpresidence_reset_taxonomy_transients', 10, 3);





/**
 * Additional hooks for property taxonomy changes
 */
function wpestate_clear_property_widget_cache_on_term_change($term_id, $tt_id, $taxonomy) {
    // Only run for property taxonomies
    $property_taxonomies = array(
        'property_category',
        'property_action_category',
        'property_city',
        'property_area'
    );
    
    if (!in_array($taxonomy, $property_taxonomies)) {
        return;
    }
    
    // Get all transients from the options table
    global $wpdb;
    
    // Define the prefix for our widget transients
    $transient_prefix = '%wpestate_widget_recent_query_output_%';
    
    // Delete all matching transients
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE %s 
             OR option_name LIKE %s",
            '_transient_' . $transient_prefix,
            '_transient_timeout_' . $transient_prefix
        )
    );
}

// Hook the cleanup function to taxonomy events
add_action('edited_term', 'wpestate_clear_property_widget_cache_on_term_change', 10, 3);
add_action('create_term', 'wpestate_clear_property_widget_cache_on_term_change', 10, 3);
add_action('delete_term', 'wpestate_clear_property_widget_cache_on_term_change', 10, 3);
