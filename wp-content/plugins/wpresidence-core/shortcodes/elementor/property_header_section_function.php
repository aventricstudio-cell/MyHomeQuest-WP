<?php
/**
 * Generate and display the property header section for Elementor in WpResidence theme.
 *
 * This function retrieves property details and includes the property header
 * template. It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 */

if (!function_exists('wpestate_estate_property_page_header_section')) :

function wpestate_estate_property_page_header_section($attributes) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Fetch property taxonomies
    $property_city     = get_the_term_list($property_id, 'property_city', '', ', ', '');
    $property_area     = get_the_term_list($property_id, 'property_area', '', ', ', '');
    $property_category = get_the_term_list($property_id, 'property_category', '', ', ', '');
    $property_action   = get_the_term_list($property_id, 'property_action_category', '', ', ', '');

    // Get currency settings
    $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
    $where_currency    = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));

    // Support for WPML translation
    if (function_exists('icl_translate')) {
        $where_currency = icl_translate('wpestate', 'wp_estate_where_currency_symbol', $where_currency);
    }

    // Fetch all property details
    $wpestate_prop_all_details = get_post_custom($property_id);

    // Set display options
    $display_options = array(
        'print'    => 'yes',
        'favorite' => 'yes',
        'share'    => 'yes',
        'address'  => 'yes',
    );

    // Include the property header template
    include(locate_template('templates/listing_templates/property-page-templates/property_header_area_template.php'));
}

endif; // End of function_exists check