<?php
/** MILLDONE
 * WpResidence Property Listing Grid Template
 * src: templates\shortcode_templates\content_grid_big_estate_agent_type_1.php
 * This file contains the code for displaying a single property in a grid layout
 * for the WpResidence theme. It is typically used within property listing pages,
 * widgets, or shortcodes.
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
$excerpt = wpestate_strip_excerpt_by_char(get_the_excerpt($itemID), 115, $itemID, '...');
$agent_position = get_post_meta($itemID, 'agent_position', true);

// Determine link target
$new_page_option = wpresidence_get_option('wp_estate_unit_card_new_page', '');
$target = $new_page_option === '_self' ? '' : 'target="' . esc_attr($new_page_option) . '"';


?>

<div class="property_unit_type5_content_wrapper property_listing" data-link="<?php echo esc_url($link); ?>">
    <div class="property_unit_type5_content" style="background-image:url('<?php echo esc_url($main_image_url); ?>')"></div>
    <div class="featured_gradient"></div>
    <div class="property_unit_content_grid_big_details">
        <div class="blog_unit_meta">
            <?php echo esc_html(trim($agent_position)); ?>
        </div>
        <h4>
            <a href="<?php echo esc_url($link); ?>" <?php echo $target; ?>>
                <?php echo wp_kses_post($title); ?>
            </a>
        </h4>
        <div class="property_unit_content_grid_big_details_location">
            <?php echo wp_kses_post(trim($excerpt)); ?>
        </div>
    </div>
</div>