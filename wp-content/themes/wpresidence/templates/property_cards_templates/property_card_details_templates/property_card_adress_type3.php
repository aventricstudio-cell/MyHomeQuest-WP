<?php
/**
 * Template for displaying property address on Type 3 property cards
 * src:templates\property_cards_templates\property_card_details_templates\property_card_adress_type3.php
 * This template is responsible for rendering the property address information,
 * including city and address fields on Type 3 property cards.
 * Modified to use wpestate_return_data_from_cache_if_exists for improved performance and fallback capability.
 */

// Get property address using cache function with fallback
$property_address = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_address');
$property_address = !empty($property_address) ? esc_html($property_address) : '';

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
?>
<div class="property_card_categories_wrapper">
    <?php
        // Check and print property address if it exists
        if (!empty($property_address)) {
            print $property_address;
        }
        // Only print the city if it exists and add comma if address exists
        if (!empty($property_city)) {
            if (!empty($property_address)) {
                print ', ';
            }
            print $property_city;
        }
    ?>
</div>