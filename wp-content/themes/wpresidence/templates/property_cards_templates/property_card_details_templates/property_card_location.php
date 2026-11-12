<?php
/** MILLDONE
 * Property Card Location Template
 * src: templates\property_cards_templates\property_card_details_templates\property_card_location.php
 * This template is responsible for displaying the location information
 * (city and area) for a property card in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyCard
 * @since 1.0
 */

// Retrieve property city and area terms
$property_city = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'terms', 'property_city');

if (!empty($property_city)) {
    $city_list = array_map(function($term) {
        // Check if term is an object (WP_Term) or an array
        if (is_object($term)) {
            return esc_html($term->name); // Access as object property
        } else {
            return wp_kses_post($term['name']); // Access as array key
        }
    }, $property_city);
    $property_city = implode(', ', $city_list);
} else {
    $property_city = ''; // Fallback to empty string if no city exists
}


$property_area = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'terms', 'property_area');
if (!empty($property_area)) {
    $area_list = array_map(function($term) {
        // Check if term is an object (WP_Term) or an array
        if (is_object($term)) {
            return esc_html($term->name); // Access as object property
        } else {
            return wp_kses_post($term['name']); // Access as array key
        }
    }, $property_area);
    $property_area = implode(', ', $area_list);
} else {
    $property_area = ''; // Fallback to empty string if no area exists
}


// Check if either city or area exists
if (!empty($property_city) || !empty($property_area)) {
    // Start building the location HTML
    $location_html = '<div class="property_location_image">';
    $location_html .= '<i class="fas fa-map-marker-alt"></i>';
    
    // Add area if it exists
    if (!empty($property_area)) {
        $location_html .= wp_kses_post($property_area);
    }
    
    // Add comma between area and city if both exist
    $location_html .= (!empty($property_area) && !empty($property_city)) ? ', ' : '';
    
    // Add city if it exists
    if (!empty($property_city)) {
        $location_html .= wp_kses_post($property_city);
    }
    
    // Close the location div
    $location_html .= '</div>';
    
    // Output the location HTML
    echo $location_html;
}
?>