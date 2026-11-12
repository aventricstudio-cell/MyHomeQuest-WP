<?php
/** MILLDONE
 * Property Price Display Template
 * src: templates\dashboard-templates\dashboard-unit-templates\dashboard-unit-price.php
 * Retrieves and displays the property price with appropriate currency formatting.
 * Handles price labels, currency position, and proper sanitization.
 *
 * @package WpResidence
 * @subpackage Dashboard/Templates
 * @since 1.0
 * 
 * Required variables:
 * @param int $post_id The property post ID
 */

// Get price data from post meta
$price_label = esc_html(get_post_meta($post_id, 'property_label', true));
$price_label_before = esc_html(get_post_meta($post_id, 'property_label_before', true));
$price = floatval(get_post_meta($post_id, 'property_price', true));

// Get currency settings from theme options
$currency_symbol = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
$currency_position = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));

// Generate formatted price display
$formatted_price = wpestate_show_price(
    $post_id,
    $currency_symbol,
    $currency_position,
    1
);

// Output clean price string without HTML tags
echo strip_tags($formatted_price);
?>