<?php
/**MILLDONE
 * WPEstate Property Slider Shortcode V2 Template
 * src: templates\property_shortcode_slider_templates\property_slider_shortcode_v2.php
 * This template file is used to display individual property slides in the property slider shortcode v2.
 *
 * @package WPEstate
 * @subpackage Templates
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core functions
 * - WPEstate theme functions (wpestate_get_converted_measure, wpestate_show_price)
 *
 * Usage:
 * This template is included within the wpestate_slider_properties_v2 function.
 * It expects several variables to be set before inclusion, including $prop_id, $counter, $active, etc.
 */

// Retrieve property details
$title = get_the_title($prop_id);
$link = get_permalink($prop_id);
$property_bathrooms = get_post_meta($prop_id, 'property_bathrooms', true);
$property_rooms = get_post_meta($prop_id, 'property_bedrooms', true);
$property_size = wpestate_get_converted_measure($prop_id, 'property_size');
$price = floatval(get_post_meta($prop_id, 'property_price', true));
$price_label = '<span class="price_label">' . esc_html(get_post_meta($prop_id, 'property_label', true)) . '</span>';
$price_label_before = '<span class="price_label price_label_before">' . esc_html(get_post_meta($prop_id, 'property_label_before', true)) . '</span>';

// Format the price
if ($price != 0) {
    $price = wpestate_show_price($prop_id, $wpestate_currency, $where_currency, 1);
} else {
    $price = $price_label_before . $price_label;
}

// Get the featured image
$property_featured_image_url = wpestate_get_property_featured_image($prop_id, 'property_listings');



// Generate the HTML for the property slide
?>
<div class="item <?php echo esc_attr($active); ?>" data-number="<?php echo esc_attr($counter); ?>">
    <div class="property_slider_carousel_elementor_v2_image_wrapper">
        <div class="property_slider_carousel_elementor_v2_price"><?php echo wp_kses_post(trim($price)); ?></div>
        <div class="places_cover"></div>
        <div class="property_slider_carousel_elementor_v2_image_container" style="background-image:url('<?php echo esc_url($property_featured_image_url); ?>');">
        </div>
    </div>
    <a href="<?php echo esc_url($link); ?>" class="property_slider_carousel_elementor_v2_title">
        <?php echo esc_html($title); ?>
    </a>
</div>