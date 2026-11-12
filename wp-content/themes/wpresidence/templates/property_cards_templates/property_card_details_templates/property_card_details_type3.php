<?php
/**
 * Template for displaying Property Card Details (Type 3)
 *
 * This file is part of the WpResidence theme and is used to render
 * the details section of a property card for Type 3 layout.
 * Uses cached property data instead of direct database queries for improved performance.
 */



// Set up necessary variables from cached data
$link = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'permalink');
 
// Get bedroom and bathroom counts from cached meta data
$property_bedrooms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_bedrooms');
$property_bedrooms = ($property_bedrooms !== '') ? floatval($property_bedrooms) : '';
                     
$property_bathrooms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_bathrooms');
$property_bathrooms = ($property_bathrooms !== '') ? floatval($property_bathrooms) : '';

// Get property size using the helper function
$property_size = wpestate_get_converted_measure_from_cache($property_unit_cached_data, 'property_size');

// Get garage size from cached custom meta
$property_garage_size = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'custom_meta', 'property-garage-size');

// Get lot size using the helper function
$property_lot_size = wpestate_get_converted_measure_from_cache($property_unit_cached_data, 'property_lot_size');
?>
<div class="property_listing_details">
    <?php
    // Display number of bedrooms
    if ($property_bedrooms != '' && $property_bedrooms != 0) {
        echo '<div class="property_listing_details_v3_item" data-bs-toggle="tooltip" title="' . esc_attr__('Bedrooms', 'wpresidence') . '">';
        echo '<div class="icon_label">';
        include(locate_template('templates/svg_icons/single_bedrooms.svg'));
        echo '</div>';
        echo esc_html($property_bedrooms);
        echo '</div>';
    }
   
    // Display number of bathrooms
    if ($property_bathrooms != '' && $property_bathrooms != 0) {
        echo '<div class="property_listing_details_v3_item" data-bs-toggle="tooltip" title="' . esc_attr__('Bathrooms', 'wpresidence') . '">';
        echo '<div class="icon_label">';
        include(locate_template('templates/svg_icons/infobath_unit_card_default.svg'));
        echo '</div>';
        echo esc_html($property_bathrooms);
        echo '</div>';
    }
   
    // Display property size
    if ($property_size != '') {
        echo '<div class="property_listing_details_v3_item" data-bs-toggle="tooltip" title="' . esc_attr__('Property Size', 'wpresidence') . '">';
        echo '<div class="icon_label">';
        include(locate_template('templates/svg_icons/infosize_unit_card_default.svg'));
        echo '</div>';
        echo trim($property_size);
        echo '</div>';
    }
   
    // Display garage size
    if ($property_garage_size != '') {
        echo '<div class="property_listing_details_v3_item" data-bs-toggle="tooltip" title="' . esc_attr__('Garage Size', 'wpresidence') . '">';
        echo '<div class="icon_label">';
        include(locate_template('templates/svg_icons/single_garage.svg'));
        echo '</div>';
        echo trim($property_garage_size);
        echo '</div>';
    }
    ?>
</div>