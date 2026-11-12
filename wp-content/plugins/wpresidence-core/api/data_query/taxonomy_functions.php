<?php
/**
 * WPResidence API Taxonomy Query Builder
 *
 * This file contains functionality for building WordPress taxonomy queries
 * specifically for the WPResidence real estate plugin.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Builds a WordPress tax_query array from taxonomy input parameters
 *
 * Takes an associative array of taxonomies and their terms and constructs
 * a properly formatted WordPress tax_query array for use in WP_Query.
 * 
 * @param array $taxonomy_input Associative array with taxonomy names as keys
 *                             and arrays of term slugs or IDs as values
 *                             Example: ['property_city' => ['new-york', 'miami']]
 * 
 * @return array               WordPress-compatible tax_query array or empty array
 */
function wpestate_api_build_taxonomy_query($taxonomy_input) {
    // Check if taxonomy input is empty and return early if so
    if (empty($taxonomy_input)) {
        return array();  // Return empty array instead of array with relation
    }
    
    // Initialize the main tax query array with AND relation
    // This means all taxonomy conditions must be met
    $tax_query_array = array('relation' => 'AND');
    
    // Loop through each taxonomy and its terms
    foreach ($taxonomy_input as $taxonomy => $terms) {
        // Skip processing if the terms array for this taxonomy is empty
        if (empty($terms)) {
            continue;
        }
        
        // Filter out 'all' values and empty terms
        // 'all' is a special case that should be ignored as it doesn't filter results
        $filtered_terms = array_filter($terms, function($term) {
            $term = trim($term);
            return !empty($term) && strtolower($term) !== 'all';
        });
        
        // If no valid terms remain after filtering, skip this taxonomy
        if (empty($filtered_terms)) {
            continue;
        }
        
        // Auto-detect if we're dealing with term IDs (numeric) or slugs (strings)
        // This allows flexibility in how terms are provided in the input
        $field = is_numeric($filtered_terms[array_key_first($filtered_terms)]) ? 'term_id' : 'slug';
        
        // Add the taxonomy query condition to the main array
        // Each taxonomy gets its own condition with the specified terms
        $tax_query_array[] = array(
            'taxonomy' => $taxonomy,   // The taxonomy name (e.g., 'property_city')
            'field'    => $field,      // Whether to query by 'term_id' or 'slug'
            'terms'    => $filtered_terms, // The array of term IDs or slugs
            'operator' => 'IN'         // Posts must have at least one of these terms
        );
    }
    
    // Only return the tax query if at least one taxonomy condition was added
    // The count must be > 1 because the first item is the 'relation' => 'AND'
    return count($tax_query_array) > 1 ? $tax_query_array : array();
}