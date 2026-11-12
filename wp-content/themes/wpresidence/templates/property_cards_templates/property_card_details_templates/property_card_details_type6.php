<?php
/**
 * Template for displaying property details in a type 6 card layout
 * src: templates\property_cards_templates\property_card_details_templates\property_card_details_type6.php
 * This template is part of the WpResidence theme and is used to show
 * key property features such as size, number of rooms, bedrooms, and
 * bathrooms in a grid view format with icons.
 *
 * @package WpResidence
 * @subpackage PropertyCard
 * @since WpResidence 1.0
 * Uses cached property data instead of direct database queries for improved performance.
 */



// Retrieve property details from cached data
$property_size = wpestate_get_converted_measure_from_cache($property_unit_cached_data, 'property_size');

// Get property details from cached meta data
$property_bedrooms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_bedrooms');
$property_bathrooms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_bathrooms');
$property_rooms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_rooms');
?>
<div class="property_listing_details6_grid_view">
    <?php
    // Display number of rooms if available
    if ($property_rooms != '' && $property_rooms != 0) : ?>
        <div class="inforoom_unit_type6">
            <?php
            // Include SVG icon for rooms
            include(locate_template('templates/svg_icons/single_rooms.svg'));
            echo esc_html($property_rooms) . ' ' . esc_html__('Rooms', 'wpresidence');
            ?>
        </div>
    <?php endif;
   
    // Display number of bedrooms if available
    if ($property_bedrooms != '' && $property_bedrooms != 0) : ?>
        <div class="inforoom_unit_type6">
            <?php
            // Include SVG icon for bedrooms
            include(locate_template('templates/svg_icons/single_bedrooms.svg'));
            echo esc_html($property_bedrooms) . ' ' . esc_html__('Beds', 'wpresidence');
            ?>
        </div>
    <?php endif;
   
    // Display number of bathrooms if available
    if ($property_bathrooms != '' && $property_bathrooms != 0) : ?>
        <div class="inforoom_unit_type6">
            <?php
            // Include SVG icon for bathrooms
            include(locate_template('templates/svg_icons/single_bath.svg'));
            echo esc_html($property_bathrooms) . ' ' . esc_html__('Baths', 'wpresidence');
            ?>
        </div>
    <?php endif;
   
    // Display property size if available
    if ($property_size != '' && strval($property_size) != '0') : ?>
        <div class="inforoom_unit_type6">
            <?php
            // Include SVG icon for property size
            include(locate_template('templates/svg_icons/single_floor_plan.svg'));
            echo trim($property_size); // Note: $property_size is already escaped by wpestate_get_converted_measure()
            ?>
        </div>
    <?php endif; ?>
</div>