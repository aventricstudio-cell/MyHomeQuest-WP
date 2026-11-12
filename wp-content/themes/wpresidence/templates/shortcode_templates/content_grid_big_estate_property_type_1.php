<?php
/**
 * WpResidence Property Listing Large Image with Price Template
 *
 * This file contains the code for displaying a single property in a grid layout
 * with a larger image and price for the WpResidence theme. It is typically used
 * within property listing pages, widgets, or shortcodes.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0.0
 */

// Retrieve and prepare property data
$main_image = wp_get_attachment_image_src(get_post_thumbnail_id($itemID), 'listing_full_slider');
$main_image_url = isset($main_image[0]) ? $main_image[0] : wpresidence_get_option('wp_estate_prop_list_slider_image_palceholder', 'url');
$title = get_the_title($itemID);
$link = get_permalink($itemID);

// Prepare property address
$property_address = get_post_meta($itemID, 'property_address', true);
$property_city = get_the_term_list($itemID, 'property_city', '', ', ', '');
$property_area = get_the_term_list($itemID, 'property_area', '', ', ', '');
$address_parts = array_filter([$property_address, $property_city, $property_area]);

// Get currency settings
$wpestate_currency = wpresidence_get_option('wp_estate_currency_symbol', '');
$where_currency = wpresidence_get_option('wp_estate_where_currency_symbol', '');

// Determine link target
$new_page_option = wpresidence_get_option('wp_estate_unit_card_new_page', '');
$target = $new_page_option === '_self' ? '' : 'target="' . esc_attr($new_page_option) . '"';


?>

<div class="property_unit_type5_content_wrapper property_listing" data-link="<?php echo esc_url($link); ?>">
    <div class="property_unit_type5_content" style="background-image:url('<?php echo esc_url($main_image_url); ?>')"></div>
    <div class="featured_gradient"></div>
    <div class="property_unit_content_grid_big_details">
        <div class="listing_unit_price_wrapper">
            <?php echo wpestate_show_price($itemID, $wpestate_currency, $where_currency); ?>
        </div>
        <h4>
            <a href="<?php echo esc_url($link); ?>" <?php echo $target; ?>>
                <?php echo wp_kses_post($title); ?>
            </a>
        </h4>
        <div class="property_unit_content_grid_big_details_location">
            <?php echo wp_kses_post(implode(', ', $address_parts)); ?>
        </div>
    </div>
</div>