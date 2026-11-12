<?php
/**
 * WPResidence API Custom Query Builder
 *
 * This file contains the main query function for retrieving property listings
 * from WPResidence using various filtering parameters.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Executes a custom WP_Query for property listings with various filters
 *
 * Creates and runs a WordPress query with support for meta, taxonomy, 
 * ordering, and pagination filters. Returns either formatted HTML (web mode)
 * or structured data (API mode).
 * 
 * @param string $post_type       Post type to query, defaults to 'estate_property'
 * @param int    $paged           Current page number for pagination
 * @param int    $posts_per_page  Number of posts per page
 * @param array  $meta_input      Array of meta query parameters
 * @param array  $taxonomy_input  Array of taxonomy query parameters
 * @param int    $order           Order code (see wpestate_api_create_query_order_by_array)
 * @param int    $userID          Optional user ID to filter properties by author
 * @param string $query_type      'web' for HTML output or anything else for data return
 * 
 * @return array|void             Returns array of results for API mode, outputs HTML for web mode
 */
function wpestate_api_custom_query(
    $post_type='estate_property',
    $paged=1,
    $posts_per_page=10,
    $meta_input=array(),
    $taxonomy_input=array(),
    $order=1,
    $userID=null,
    $query_type = 'web') {
        
        // Create order parameters array based on the order code
        $order_array = wpestate_api_create_query_order_by_array($order);

        // Build the base query arguments
        $args = array(
            'post_type'         => $post_type,      // Post type (usually 'estate_property')
            'post_status'       => 'publish',       // Only published properties
            'paged'             => $paged,          // Current page number
            'posts_per_page'    => $posts_per_page, // Number of properties per page
            'fields'            => 'ids'            // Only get post IDs for efficiency
        );

        // Add author filter if userID is provided
        // This limits results to properties created by a specific user
        if (!empty($userID)) {
            $args['author'] = $userID;
        }

        // Build and add meta query if meta input parameters are provided
        // This handles filtering by price, bedrooms, bathrooms, etc.
        $meta_query = wpestate_api_build_meta_query($meta_input);
        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }

        // Build and add taxonomy query if taxonomy input parameters are provided
        // This handles filtering by city, property type, features, etc.
        $tax_query = wpestate_api_build_taxonomy_query($taxonomy_input);
        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        // Merge ordering parameters into the main query arguments
        $args = array_merge($args, $order_array);

        // Execute the query based on the requested output type
        if ($query_type === 'web') {
            // Web mode - output HTML directly
            
            // Special handling for default sort order
            if ($order == 0) {
                if (function_exists('wpestate_return_filtered_by_order')) {
                    // Use theme's custom ordering function if available
                    $prop_selection = wpestate_return_filtered_by_order($args);
                }
            } else {
                // Standard WP_Query for other sort orders
                $prop_selection = new WP_Query($args);
            }
            
            // Output property IDs if results found
            if ($prop_selection->have_posts()) {
                foreach ($prop_selection->posts as $postID) {
                    print $postID . '   <br>';
                }
            }
        } else {
            // API mode - return structured data
            $query = new WP_Query($args);
            
            // Prepare the return array with query results
            $return_array = array(
                'post_ids' => $query->posts,         // Array of property IDs
                'total_posts' => $query->found_posts, // Total number of properties found
                'max_num_pages' => $query->max_num_pages, // Total number of pages
                'args' => $args                       // The query arguments used (for debugging)
            );
        }

        // Reset global post data to avoid conflicts with other queries
        wp_reset_postdata();

        // Return the results array for API mode
        if ($query_type !== 'web') {
            return $return_array;
        }
}