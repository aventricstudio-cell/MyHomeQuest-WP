<?php
/**
 * Template for displaying Property Card Details (Type 5)
 * src: templates\property_cards_templates\property_card_details_templates\property_card_details_type5.php
 * This file is part of the WpResidence theme and is used to render
 * the details section of a property card for Type 5 layout.
 * Uses cached property data instead of direct database queries for improved performance.
 */


// Set up necessary variables from cached data
$property_size = wpestate_get_converted_measure_from_cache($property_unit_cached_data, 'property_size');

// Get bedroom and bathroom counts from cached meta data
$property_bedrooms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_bedrooms');
$property_bathrooms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_bathrooms');
?>
<div class="property_unit_type5_content_details_second_row">
    <?php
    // Display number of bedrooms
    if ($property_bedrooms != '' && $property_bedrooms != 0) {
        echo '<div class="inforoom_unit_type5">' . esc_html($property_bedrooms) . ' ' . esc_html__('BD', 'wpresidence') . '</div>';
    }
   
    // Display number of bathrooms
    if ($property_bathrooms != '' && $property_bathrooms != 0) {
        echo '<div class="inforoom_unit_type5">' . esc_html($property_bathrooms) . ' ' . esc_html__('BA', 'wpresidence') . '<span></span></div>';
    }
   
    // Display property size
    if ($property_size != '' && strval($property_size) != '0') {
        echo '<div class="inforoom_unit_type5">' . trim($property_size) . '</div>';
    }
    ?>
</div>