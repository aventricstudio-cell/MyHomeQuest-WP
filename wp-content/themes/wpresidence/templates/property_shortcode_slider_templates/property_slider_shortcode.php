<?php
/**
 * WPResidence Property Slider Shortcode Template
 *
 * This template file is used to display individual property slides in the property slider shortcode.
 *
 * @package WPResidence
 * @subpackage Templates
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core functions
 * - WPResidence theme functions (wpestate_get_converted_measure, wpestate_show_price, wpestate_strip_excerpt_by_char)
 *
 * Usage:
 * This template is typically included within a loop in the wpestate_slider_properties function.
 * It expects $prop_id and $initial_section variables to be set before inclusion.
 */

// Retrieve property details

$title              = get_the_title($prop_id);
$link               = get_permalink($prop_id);
$property_bathrooms = get_post_meta($prop_id, 'property_bathrooms', true);
$property_rooms     = get_post_meta($prop_id, 'property_bedrooms', true);
$property_size      = wpestate_get_converted_measure($prop_id, 'property_size');
$price              = floatval(get_post_meta($prop_id, 'property_price', true));
$price_label        = '<span class="price_label">' . esc_html(get_post_meta($prop_id, 'property_label', true)) . '</span>';
$price_label_before = '<span class="price_label price_label_before">' . esc_html(get_post_meta($prop_id, 'property_label_before', true)) . '</span>';

// Format the price
if ($price != 0) {
    $price = wpestate_show_price($prop_id, $wpestate_currency, $where_currency, 1);  
} else {
    $price = $price_label_before . $price_label;
}

// Get the featured image
$property_featured_image_url = wpestate_get_property_featured_image($prop_id, 'property_featured');


// Get property taxonomies
$property_city     = get_the_term_list($prop_id, 'property_city', '', ', ', '');
$property_area     = get_the_term_list($prop_id, 'property_area', '', ', ', '');
$property_action   = get_the_term_list($prop_id, 'property_action_category', '', ', ', '');  
$property_category = get_the_term_list($prop_id, 'property_category', '', ', ', '');  
?>

<!-- Property Slide Section -->
<section class="section <?php echo esc_attr($initial_section); ?>">
<div class="section__content">
                <h2 class="section__title keep-ltr"><a class="keep-ltr" href="<?php echo esc_url($link);?>"><?php echo esc_html($title); ?> </a> </h2>
                    <p class="section__description">
                        <span class="section_price"><?php echo wp_kses_post($price); ?> </span>
                        <span class="section__description-inner"><?php echo wpestate_strip_excerpt_by_char(get_the_excerpt($prop_id),270,$prop_id);?></span>
                    </p>
                    
            </div>
    
    <div class="section__img">
        <div class="section__img-inner" style="background-image: url(<?php echo esc_url($property_featured_image_url); ?>)"></div>
        
        <div class="section__expander">
            <ul class="section__facts">
                <li class="section__facts-item">
                    <h3 class="section__facts-title"><?php esc_html_e('Category', 'wpresidence'); ?></h3>
                    <span class="section__facts-detail">
                        <?php echo wp_kses_post($property_category . ' ' . esc_html__('in', 'wpresidence') . ' ' . $property_action); ?>
                    </span>
                </li>
                <li class="section__facts-item">
                    <h3 class="section__facts-title"><?php esc_html_e('Location', 'wpresidence'); ?></h3>
                    <span class="section__facts-detail">
                        <?php echo wp_kses_post($property_city . ', ' . $property_area); ?>
                    </span>
                </li>
                <li class="section__facts-item">
                    <span class="section__facts-detail">
                        <?php echo esc_html(intval($property_rooms) . ' ' . __('Rooms', 'wpresidence')); ?>
                    </span>
                </li>
                <li class="section__facts-item">
                    <span class="section__facts-detail">
                        <?php echo esc_html(intval($property_bathrooms) . ' ' . __('Bathrooms', 'wpresidence')); ?>
                    </span>
                </li>
                <li class="section__facts-item">
                    <span class="section__facts-detail">
                        <?php echo esc_html__('Size', 'wpresidence') . ' ' . wp_kses_post(trim($property_size)); ?>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</section>