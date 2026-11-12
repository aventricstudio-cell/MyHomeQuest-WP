<?php
/** MILLDONE
 * WpResidence Property Price Display
 * src: templates\listing_templates\property-page-templates\price_display.php
 * This template file is responsible for rendering the price display section
 * of a property page in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @version 1.0
 * 
 * @uses get_post_meta()
 * @uses wpestate_show_price()
 * 
 * Dependencies:
 * - WordPress core functions
 * - WpResidence theme-specific functions and variables
 * 
 * Expected global variables:
 * - $post
 * - $wpestate_currency
 * - $where_currency
 * - $property_action
 * - $property_category
 */

// Fetch primary price details
$price_details = array(
    'price' => floatval(get_post_meta($selectedPropertyID, 'property_price', true)),
    'label' => esc_html(get_post_meta($selectedPropertyID, 'property_label', true)),
    'label_before' => esc_html(get_post_meta($selectedPropertyID, 'property_label_before', true))
);


// Fetch secondary price details
$second_price_details = array(
    'price' => floatval(get_post_meta($selectedPropertyID, 'property_second_price', true)),
    'label' => esc_html(get_post_meta($selectedPropertyID, 'property_second_price_label', true)),
    'label_before' => esc_html(get_post_meta($selectedPropertyID, 'property_label_before_second_price', true))
);

// Format primary price
$price = ($price_details['price'] != 0) 
    ? wpestate_show_price($selectedPropertyID, $wpestate_currency, $where_currency, 1)
    : sprintf(
        '<span class="price_label price_label_before">%s</span><span class="price_label">%s</span>',
        $price_details['label_before'],
        $price_details['label']
    );

// Format secondary price
$property_second_price = ($second_price_details['price'] != 0)
    ? wpestate_show_price($selectedPropertyID, $wpestate_currency, $where_currency, 1, "yes")
    : sprintf(
        '<span class="price_label price_label_before">%s</span><span class="price_label">%s</span>',
        $second_price_details['label_before'],
        $second_price_details['label']
    );
?>

<div class="single_property_labels">
    <div class="property_title_label"><?php echo wp_kses_post($property_action); ?></div>
    <div class="property_title_label actioncat"><?php echo wp_kses_post($property_category); ?></div>
</div>

<h1 class="entry-title entry-prop"><?php echo get_the_title($selectedPropertyID); ?></h1>

<div class="price_area">
    <div class="second_price_area"><?php echo wp_kses_post($property_second_price); ?></div>    
    <?php echo wp_kses_post($price); ?>
</div>