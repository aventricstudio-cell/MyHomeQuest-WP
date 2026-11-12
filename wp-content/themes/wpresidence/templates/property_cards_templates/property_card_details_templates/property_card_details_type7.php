<?php
/**
 * Template for displaying property details in a type 7 card layout
 *
 * This template is part of the WpResidence theme and is used to show
 * key property features such as bedrooms, bathrooms, property size,
 * garage size, and lot size in a compact format with icons.
 *
 * @package WpResidence
 * @subpackage PropertyCard
 * @since WpResidence 1.0
 * Uses cached property data instead of direct database queries for improved performance.
 */

// Get permalink from cached data
$link = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'permalink');

// Retrieve property details from cached data
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
    $property_features = [
        [
            'value' => $property_bedrooms,
            'title' => esc_attr__('Bedrooms', 'wpresidence'),
            'icon' => 'templates/svg_icons/single_bedrooms.svg'
        ],
        [
            'value' => $property_bathrooms,
            'title' => esc_attr__('Bathrooms', 'wpresidence'),
            'icon' => 'templates/svg_icons/infobath_unit_card_default.svg'
        ],
        [
            'value' => $property_size,
            'title' => esc_attr__('Property Size', 'wpresidence'),
            'icon' => 'templates/svg_icons/infosize_unit_card_default.svg'
        ],
        [
            'value' => $property_garage_size,
            'title' => esc_attr__('Garage Size', 'wpresidence'),
            'icon' => 'templates/svg_icons/single_garage.svg'
        ],
        [
            'value' => $property_lot_size,
            'title' => esc_attr__('Lot Size', 'wpresidence'),
            'icon' => 'templates/svg_icons/single_lot_size.svg'
        ]
    ];
   
    foreach ($property_features as $feature) {
        if ($feature['value'] !== '' && $feature['value'] != 0) {
            echo '<div class="property_listing_details_v7_item" data-bs-toggle="tooltip" title="' . $feature['title'] . '">';
            echo '<div class="icon_label">';
            include(locate_template($feature['icon']));
            echo '</div>';
            echo wp_kses_post($feature['value']);
            echo '</div>';
        }
    }
    ?>
</div>