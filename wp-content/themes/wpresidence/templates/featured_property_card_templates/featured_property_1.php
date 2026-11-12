<?php
/** MILLDONE
 * Featured Property Display Template for WpResidence Theme
 * src: templates/featured_property_card_templates/featured_property_1.php
 * This template file generates HTML for displaying featured properties
 * in the WpResidence WordPress theme.
 *
 * @package WpResidence
 * @subpackage PropertyDisplay
 * @since 1.0.0
 *
 * @dependencies
 * - WordPress core functions
 * - WpResidence theme functions (e.g., wpestate_return_agent_details, wpestate_show_price)
 *
 * @usage
 * This template is likely included within other template files or called by
 * functions in the WpResidence theme to display featured properties.
 */

// Retrieve and prepare property data

$property_featured_image_url = wpestate_get_property_featured_image($prop_id, 'listing_full_slider');

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
$agent_posit    = $realtor_details['realtor_position'];
$agent_permalink = $realtor_details['link'];

// Format price display
if ($price != 0) {
    $price_display = wpestate_show_price($prop_id, $wpestate_currency, $where_currency, 1);
} else {
    $price_display = $price_label_before ? "<span class='price_label price_label_before'>{$price_label_before}</span>" : '';
    $price_display .= "<span class='price_label'>{$price_label}</span>";
}

// Prepare slider content if needed
$wpestate_property_unit_slider = 1; 

// Now, let's output the HTML
?>

<div class="featured_property featured_property_type1">
    <div class="featured_prop_price"><?php echo wp_kses_post($price_display); ?></div>
    <div class="featured_img">
        <div class="tag-wrapper">
            <?php if ($featured == 1): ?>
                <div class="featured_div"><?php esc_html_e('Featured', 'wpresidence'); ?></div>
            <?php endif; ?>
            <?php echo wpestate_return_property_status($prop_id); ?>
        </div>

        <?php
        if ($wpestate_property_unit_slider == 1) {
            echo wpestate_generate_property_slider_content($prop_id, $link, $title,'featured_property_1');
        } else {
            echo wpestate_featured_property_single_image($property_featured_image_url, $link);
        }
        ?>
    </div>

    <div class="featured_secondline" data-link="<?php echo esc_attr($link); ?>">
        <?php if ($agent_id != ''): ?>
            <div class="agent_face">
                <img src="<?php echo esc_url($agent_face); ?>" width="55" height="55" class="img-responsive" alt="<?php esc_attr_e('Agent Image', 'wpresidence'); ?>">
                <div class="agent_face_details">
                    <img src="<?php echo esc_url($agent_face); ?>" width="120" height="120" class="img-responsive" alt="<?php esc_attr_e('Agent Image', 'wpresidence'); ?>">
                    <h4><a href="<?php echo esc_url($agent_permalink); ?>"><?php echo get_the_title($agent_id); ?></a></h4>
                    <div class="agent_position"><?php echo esc_html($agent_posit); ?></div>
                    <a class="see_my_list" href="<?php echo esc_url($agent_permalink); ?>" target="_blank">
                        <span class="wpresidence_button wpb_wpb_button"><?php esc_html_e('My Listings', 'wpresidence'); ?></span>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <h2><a href="<?php echo esc_url($link); ?>">
            <?php echo esc_html(mb_substr($title, 0, 37) . (mb_strlen($title) > 37 ? '...' : '')); ?>
        </a></h2>
        <div class="sale_line"><?php echo esc_html($sale_line); ?></div>
    </div>
</div>