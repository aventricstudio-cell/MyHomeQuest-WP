<?php
/** MILLDONE
 * WpResidence Property Listing Template
 * src: templates\shortcode_templates\content_grid_small_estate_property_type_1.php
 * This file contains the code for displaying a single property listing in the WpResidence theme.
 * It is typically used within property listing widgets, shortcodes, or archive templates.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0.0
 */

// Retrieve and prepare property data
$main_image = wp_get_attachment_image_src(get_post_thumbnail_id($itemID), 'blog_thumb');
$main_image_url = isset($main_image[0]) ? $main_image[0] : wpresidence_get_option('wp_estate_prop_list_slider_image_palceholder', 'url');
$title = get_the_title($itemID);
$link = get_permalink($itemID);

// Prepare address components
$property_address = get_post_meta($itemID, 'property_address', true);
$property_city = get_the_term_list($itemID, 'property_city', '', ', ', '');
$property_area = get_the_term_list($itemID, 'property_area', '', ', ', '');
$address_parts = array_filter([$property_address, $property_city, $property_area]);

// Retrieve property details
$property_size = wpestate_get_converted_measure($itemID, 'property_size');
$property_rooms = get_post_meta($itemID, 'property_rooms', true);
$property_bathrooms = get_post_meta($itemID, 'property_bathrooms', true);

// Currency settings
$wpestate_currency = wpresidence_get_option('wp_estate_currency_symbol', '');
$where_currency = wpresidence_get_option('wp_estate_where_currency_symbol', '');


// Determine link target
$new_page_option = wpresidence_get_option('wp_estate_unit_card_new_page', '');
$target = $new_page_option === '_self' ? '' : 'target="' . esc_attr($new_page_option) . '"';

// Prepare property details string
$details = array_filter([
    $property_rooms ? $property_rooms . ' ' . esc_html__('Rooms', 'wpresidence') : '',
    $property_bathrooms ? $property_bathrooms . ' ' . esc_html__('Baths', 'wpresidence') : '',
    $property_size
]);
$details_string = implode('<span class="wpestate_separator_dot">&#183;</span>', $details);
?>

<div class="wpestate_content_grid_wrapper_second_col_item_wrapper flex-sm-row flex-column">
    <div class="wpestate_content_grid_wrapper_second_col_image property_listing" 
         style="background-image:url('<?php echo esc_url($main_image_url); ?>')" 
         data-link="<?php echo esc_url($link); ?>">
    </div>
    <div class="property_unit_content_grid_small_details">
        <div class="listing_unit_price_wrapper">
            <?php echo wpestate_show_price($itemID, $wpestate_currency, $where_currency); ?>
        </div>
        <h4>
            <a href="<?php echo esc_url($link); ?>" <?php echo $target; ?>>
                <?php echo wp_kses_post($title); ?>
            </a>
        </h4>
        <div class="property_unit_content_grid_small_details_location property_unit_content_grid_small_address">
            <?php echo wp_kses_post(implode(', ', $address_parts)); ?>
        </div>
        <div class="property_unit_content_grid_small_details_location">
            <?php echo wp_kses_post($details_string); ?>
        </div>
    </div>
</div>