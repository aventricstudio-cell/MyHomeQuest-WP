<?php
/**
 * WPResidence API Order Query Builder
 *
 * This file contains functionality for building WordPress order parameters
 * specifically for the WPResidence real estate plugin.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Creates an array of ordering parameters for WP_Query based on the order code
 *
 * Converts numerical order codes to appropriate WordPress ordering parameters
 * for sorting property listings in different ways.
 * 
 * Available order codes:
 * 0 = Default (Featured properties first)
 * 1 = Price High to Low
 * 2 = Price Low to High
 * 3 = Newest first (by ID)
 * 4 = Oldest first (by ID)
 * 5 = Bedrooms High to Low
 * 6 = Bedrooms Low to High
 * 7 = Bathrooms High to Low
 * 8 = Bathrooms Low to High
 * 11 = Newest Edited (by modified date)
 * 12 = Oldest Edited (by modified date)
 * 99 = Random order
 * 
 * @param int $order The order code representing the desired sorting method
 * 
 * @return array     WordPress-compatible orderby parameters
 */
function wpestate_api_create_query_order_by_array($order){
    // Set default ordering parameters (Featured properties first)
    $meta_directions    =   'DESC';    // Direction: descending
    $meta_order         =   'prop_featured'; // Meta key for featured properties
    $order_by           =   'meta_value_num'; // Sort by numeric meta value
    
    // Ensure order parameter is an integer
    $order = intval($order);
 
    // Determine ordering parameters based on the order code
    switch ($order){
        case 1:
            // Price High to Low
            $meta_order='property_price';
            $meta_directions='DESC';
            $order_by='meta_value_num';
            break;
        case 2:
            // Price Low to High
            $meta_order='property_price';
            $meta_directions='ASC';
            $order_by='meta_value_num';
            break;
        case 3:
            // Newest first (by post ID)
            $meta_order='';  // No meta key needed
            $meta_directions='DESC';
            $order_by='ID';
            break;
        case 4:
            // Oldest first (by post ID)
            $meta_order='';  // No meta key needed
            $meta_directions='ASC';
            $order_by='ID';
            break;
        case 5:
            // Bedrooms High to Low
            $meta_order='property_bedrooms';
            $meta_directions='DESC';
            $order_by='meta_value_num';
            break;
        case 6:
            // Bedrooms Low to High
            $meta_order='property_bedrooms';
            $meta_directions='ASC';
            $order_by='meta_value_num';
            break;
        case 7:
            // Bathrooms High to Low
            $meta_order='property_bathrooms';
            $meta_directions='DESC';
            $order_by='meta_value_num';
            break;
        case 8:
            // Bathrooms Low to High
            $meta_order='property_bathrooms';
            $meta_directions='ASC';
            $order_by='meta_value_num';
            break;
        case 11:
            // Newest Edited (by modified date)
            $meta_order='';  // No meta key needed
            $meta_directions='DESC';
            $order_by='modified';
            break;
        case 12:
            // Oldest Edited (by modified date)
            $meta_order='';  // No meta key needed
            $meta_directions='ASC';
            $order_by='modified';
            break;
        case 99:
            // Random order
            $meta_order='';  // No meta key needed
            $meta_directions='ASC';  // Direction doesn't matter for random
            $order_by='rand';
            break;
        // Case 0 (default) uses the initial values set above
    }
    
    // Build the order array starting with the orderby parameter
    $order_array=array(
        'orderby' => $order_by,
    );

    // Add meta_key parameter only if a specific meta field is being used for ordering
    if($meta_order!=''){
        $order_array['meta_key']=$meta_order;
    }
    
    // Add order direction parameter if specified
    if($meta_directions!=''){
        $order_array['order']=$meta_directions;
    }

    return $order_array;
}