<?php 
/**
 * Get property terms as a comma-separated string using cached data when available
 *
 * @param int $post_id Post ID of the property
 * @param string $taxonomy Taxonomy name to retrieve terms from
 * @param array $cached_data Optional cached property data array
 * @return string Comma-separated list of term names
 */
function wpestate_get_property_terms_from_cache($post_id, $taxonomy, $cached_data = null) {
    // Return empty string if no post ID or taxonomy provided
    if(empty($post_id) || empty($taxonomy)) {
        return '';
    }
    
    // If cached data wasn't passed, try to get it
    if(empty($cached_data)) {
        $cached_data = wpestate_api_get_cached_post_data($post_id, 'estate_property');
    }
    
    // If we have cached data with terms for this taxonomy
    if(isset($cached_data) && !empty($cached_data) && isset($cached_data['terms'][$taxonomy])) {
        $terms = $cached_data['terms'][$taxonomy];
        $term_names = array();
        
        foreach($terms as $term) {
            if(isset($term['name'])) {
                $term_names[] = $term['name'];
            }
        }
        
        return implode(', ', $term_names);
    }
    
    // Fallback to database query if no cache or terms not in cache
    return get_the_term_list($post_id, $taxonomy, '', ', ', '');
}

