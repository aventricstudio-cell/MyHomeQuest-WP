<?php
/**
 * Template for displaying property details on Type 1 property cards
 * src: templates\property_cards_templates\property_card_details_templates\property_card_details_type1.php
 * This template is responsible for rendering key property details such as
 * number of rooms, bathrooms, property size, and property ID on Type 1 property cards.
 * Uses cached property data instead of direct database queries for improved performance.
 */
?>
<div class="property_details_type1_wrapper">
    <?php

    // Retrieve property details using the wpestate_return_data_from_cache_if_exists function
    $property_size = wpestate_get_converted_measure_from_cache($property_unit_cached_data, 'property_size');
    $property_rooms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_rooms');
    $property_bathrooms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_bathrooms');
   
    // Display number of rooms
    if ($property_rooms != '' && $property_rooms != 0) {
        $room_string = sprintf(
            _n('<span class="property_details_type1_value">%d</span> Room', '<span class="property_details_type1_value">%d</span> Rooms', $property_rooms, 'wpresidence'),
            $property_rooms
        );
        echo ' <span class="property_details_type1_rooms">' . $room_string . '</span>';
       
        // Add separator if there are more details to display
        if (($property_bathrooms != '' && $property_bathrooms != 0) ||
            ($postID != '' && $postID != 0) ||
            ($property_size != '' && strval($property_size) != '0')) {
            echo ' ' . trim('<span>&#183;</span>') . ' ';
        }
    }
   
    // Display number of bathrooms
    if ($property_bathrooms != '' && $property_bathrooms != 0) {
        $bath_string = sprintf(
            _n('<span class="property_details_type1_value">%d</span> Bath', '<span class="property_details_type1_value">%d</span> Baths', $property_bathrooms, 'wpresidence'),
            $property_bathrooms
        );
        echo '<span class="property_details_type1_baths">' . $bath_string . '</span>';
       
        // Add separator if there are more details to display
        if (($postID != '' && $postID != 0) ||
            ($property_size != '' && strval($property_size) != '0')) {
            echo ' ' . trim('<span>&#183;</span>') . ' ';
        }
    }
   
    // Display property ID
    if ($postID != '' && $postID != 0) {
        echo ' <span class="property_details_type1_id">' . esc_html__('ID', 'wpresidence') .
             ' <span class="property_details_type1_value">' . esc_html($postID) . '</span></span>';
       
        // Add separator if there's property size to display
        if ($property_size != '' && strval($property_size) != '0') {
            echo ' ' . trim('<span>&#183;</span>') . ' ';
        }
    }
   
    // Display property size
    if ($property_size != '' && strval($property_size) != '0') {
        echo ' <span class="property_details_type1_size"><span class="property_details_type1_value">' .
             esc_html__('Size', 'wpresidence') . ' ' . wp_kses_post($property_size) . '</span>';
    }
    ?>
</div>