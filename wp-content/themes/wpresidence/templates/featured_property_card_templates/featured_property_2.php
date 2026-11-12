<?php
/** MILLDONE
 * Featured Property Type 2 Display Template for WpResidence Theme
 * src:  templates/featured_property_card_templates/featured_property_2.php
 * This template file generates HTML for displaying featured properties of type 2
 * in the WpResidence WordPress theme.
 *
 * @package WpResidence
 * @subpackage PropertyDisplay
 * @since 1.0.0
 */

// Retrieve and prepare property data
$property_featured_image_url = wpestate_get_property_featured_image($prop_id, 'listing_full_slider_1');

$link           = get_permalink($prop_id);
$title          = get_the_title($prop_id);
$price          = floatval(get_post_meta($prop_id, 'property_price', true));
$price_label    = get_post_meta($prop_id, 'property_label', true);
$price_label_before = get_post_meta($prop_id, 'property_label_before', true);
$wpestate_currency = wpresidence_get_option('wp_estate_currency_symbol', '');
$where_currency = wpresidence_get_option('wp_estate_where_currency_symbol', '');
$featured       = intval(get_post_meta($prop_id, 'prop_featured', true));

// Retrieve agent details
$realtor_details = wpestate_return_agent_details($prop_id);
$agent_id       = $realtor_details['agent_id'];
$agent_face     = $realtor_details['agent_face_img'];

// Format price display
if ($price != 0) {
    $price_display = wpestate_show_price($prop_id, $wpestate_currency, $where_currency, 1);  
} else {
    $price_display = $price_label_before . $price_label;
}

// Prepare image content
$wpestate_property_unit_slider = 1; // Assuming this is defined elsewhere
if ($wpestate_property_unit_slider == 1) {
    $image_content = wpestate_generate_property_slider_content($prop_id, $link, $title, 'featured_property_2');
} else {
    $image_content = wpestate_featured_property_single_image($property_featured_image_url, $link);
}

// Prepare the HTML structure
?>
<div class="featured_property featured_property_type2">
    <div class="featured_img">
        <div class="tag-wrapper">
            <?php if ($featured == 1): ?>
                <div class="featured_div"><?php esc_html_e('Featured', 'wpresidence'); ?></div>
            <?php endif; ?>
            <?php echo wpestate_return_property_status($prop_id); ?>
        </div>
        <?php echo $image_content; ?>
    </div>
    <div class="featured_secondline">
        <?php if ($agent_id != ''): ?>
            <div class="agent_face" style="background-image:url(<?php echo esc_url($agent_face); ?>)"></div>
        <?php endif; ?>
        <h2><a href="<?php echo esc_url($link); ?>">
            <?php echo esc_html(mb_substr($title, 0, 37) . (mb_strlen($title) > 37 ? '...' : '')); ?>
        </a></h2>
        <div class="featured_prop_price"><?php echo $price_display; ?></div>
    </div>
</div>