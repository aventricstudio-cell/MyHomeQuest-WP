<?php
/**
 * Template for displaying property details in a type 2 card layout
 * src: templates\property_cards_templates\property_card_details_templates\property_card_details_type2.php
 * This template is part of the WpResidence theme and is used to show
 * key property features such as size, number of bedrooms, bathrooms,
 * and garage spaces in a compact format within property cards.
 *
 * @package WpResidence
 * @subpackage PropertyCard
 * @since WpResidence 1.0
 * Uses cached property data instead of direct database queries for improved performance.
 */
?>
<div class="property_listing_details">
    <?php

    // Retrieve property size using the helper function
    $property_size = wpestate_get_converted_measure_from_cache($property_unit_cached_data, 'property_size');
   
    // Retrieve number of bedrooms, bathrooms from cached data using the utility function
    $property_bedrooms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_bedrooms');
    $property_bathrooms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_bathrooms');
   
    // Retrieve garage spaces from cached custom meta using the utility function
    $garage_no = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'custom_meta', 'property-garage');
   
    // Display number of bedrooms if available
    if ($property_bedrooms != '' && $property_bedrooms != 0) {
        echo ' <span class="inforoom_unit_type2">' . esc_html($property_bedrooms) . '</span>';
    }
   
    // Display number of bathrooms if available
    if ($property_bathrooms != '' && $property_bathrooms != 0) {
        echo '<span class="infobath_unit_type2">' . esc_html($property_bathrooms) . '</span>';
    }
   
    // Display number of garage spaces if available
    if ($garage_no != '' && $garage_no != 0) {
        echo ' <span class="infogarage_unit_type2">' . esc_html($garage_no) . '</span>';
    }
   
    // Display property size if available
    if ($property_size != '' && $property_size != '0') {
        echo ' <span class="infosize_unit_type2">' . $property_size . '</span>';
    }
    ?>          
</div>