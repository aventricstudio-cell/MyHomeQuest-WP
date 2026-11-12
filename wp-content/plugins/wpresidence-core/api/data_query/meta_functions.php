<?php
/**
 * WPResidence API Meta Query Builder
 *
 * This file contains functionality for building WordPress meta queries
 * specifically for the WPResidence real estate plugin.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Builds a WordPress meta_query array from meta input parameters
 *
 * Takes an array of meta conditions and constructs a properly formatted 
 * WordPress meta_query array for use in WP_Query.
 * 
 * @param array $meta_query Array of meta conditions with keys, values, and comparison operators
 *                          Example: [
 *                              ['key' => 'property_price', 'value' => 500000, 'compare' => '>=', 'type' => 'NUMERIC'],
 *                              ['key' => 'property_bedrooms', 'value' => 2, 'compare' => '>=', 'type' => 'NUMERIC']
 *                          ]
 * 
 * @return array           WordPress-compatible meta_query array or empty array
 */
function wpestate_api_build_meta_query($meta_query) {
    // Check if meta query input is empty and return early if so
    if (empty($meta_query)) {
        return array();  // Return empty array instead of array with relation
    }
    
    // Prepare the meta query array with a default relation of 'AND'
    // This means all meta conditions must be met
    $meta_query_array = array('relation' => 'AND');
    
    // Iterate over each meta condition provided
    foreach ($meta_query as $meta) {
        // Ensure the meta condition is an array and has a key specified
        if (is_array($meta) && isset($meta['key'])) {
            // Start building the meta condition with required elements
            $meta_condition = array(
                'key'     => $meta['key'],     // The meta key to query (e.g., 'property_price')
                'compare' => isset($meta['compare']) ? $meta['compare'] : '=', // Default to '=' if not specified
            );

            // Add value if it exists and isn't an EXISTS/NOT EXISTS comparison
            // These special comparisons don't require values
            if (isset($meta['value']) && 
                !in_array($meta['compare'], ['EXISTS', 'NOT EXISTS'])) {
                $meta_condition['value'] = $meta['value'];
            }

            // Add type if specified (e.g., 'NUMERIC', 'BINARY', 'CHAR', etc.)
            // This helps WordPress properly format comparisons
            if (isset($meta['type'])) {
                $meta_condition['type'] = $meta['type'];
            }

            // Add the complete meta condition to the query array
            $meta_query_array[] = $meta_condition;
        }
    }

    // Only return the meta query if at least one condition was added
    return !empty($meta_query_array) ? $meta_query_array : array();
}