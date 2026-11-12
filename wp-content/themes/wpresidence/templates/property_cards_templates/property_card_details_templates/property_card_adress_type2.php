<?php
/**
 * Template for displaying property address on Type 2 property cards
 * src: templates\property_cards_templates\property_card_details_templates\property_card_adress_type2.php
 * This template is responsible for rendering the property address information,
 * including city, area, and custom address fields on Type 2 property cards.
 * Modified to use wpestate_return_data_from_cache_if_exists for improved performance and fallback capability.
 */
?>
<div class="property_address_type1_wrapper">
    <?php
    // Get address information using cache function with fallback
    $property_address = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_address');
    $property_address = !empty($property_address) ? esc_html($property_address) : '';
   
    // Get property area using cache function with fallback
    $property_area_terms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'terms', 'property_area');
    $property_area = '';
    if (!empty($property_area_terms) && is_array($property_area_terms)) {
        $areas = array();
        foreach ($property_area_terms as $area) {
            $areas[] = $area['name'];
        }
        $property_area = implode(', ', $areas);
    }
   
    // Get property city using cache function with fallback
    $property_city_terms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'terms', 'property_city');
    $property_city = '';
    if (!empty($property_city_terms) && is_array($property_city_terms)) {
        $cities = array();
        foreach ($property_city_terms as $city) {
            $cities[] = $city['name'];
        }
        $property_city = implode(', ', $cities);
    }
   
    // Display the location icon
    echo '<i class="fas fa-map-marker-alt"></i>';
   
    // Display property address if available
    if (!empty($property_address)) {
        echo '<span class="property_address_type1">' . $property_address . '</span>';
    }
   
    // Display property area if available (with comma if address exists)
    if (!empty($property_area)) {
        if (!empty($property_address)) {
            echo ', ';
        }
        echo '<span class="property_area_type1">' . $property_area . '</span>';
    }
   
    // Display property city if available (with comma if address or area exists)
    if (!empty($property_city)) {
        if (!empty($property_address) || !empty($property_area)) {
            echo ', ';
        }
        echo '<span class="property_city_type1">' . $property_city . '</span>';
    }
    ?>
</div>