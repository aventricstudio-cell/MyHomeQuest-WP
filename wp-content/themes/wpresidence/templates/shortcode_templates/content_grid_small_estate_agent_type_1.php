<?php
/** MILLDONE
 * WpResidence Agent Listing Grid Template
 * src: templates\shortcode_templates\content_grid_small_estate_agent_type_1.php
 * This file contains the code for displaying a single agent in a grid layout
 * for the WpResidence theme. It is typically used within agent listing pages,
 * widgets, or shortcodes.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0.0
 */

// Retrieve and prepare agent data
$main_image = wp_get_attachment_image_src(get_post_thumbnail_id($itemID), 'blog_thumb');
$main_image_url = isset($main_image[0]) ? $main_image[0] : wpresidence_get_option('wp_estate_prop_list_slider_image_palceholder', 'url');
$title = get_the_title($itemID);
$link = get_permalink($itemID);
$date = get_the_date(get_option('date_format'), $itemID);
$excerpt = wpestate_strip_excerpt_by_char(get_the_excerpt($itemID), 85, $itemID, '...');
$agent_position = get_post_meta($itemID, 'agent_position', true);

// Determine link target
$new_page_option = wpresidence_get_option('wp_estate_unit_card_new_page', '');
$target = $new_page_option === '_self' ? '' : 'target="' . esc_attr($new_page_option) . '"';


?>

<div class="wpestate_content_grid_wrapper_second_col_item_wrapper flex-sm-row flex-column">
    <div class="wpestate_content_grid_wrapper_second_col_image property_listing" 
         style="background-image:url('<?php echo esc_url($main_image_url); ?>')" 
         data-link="<?php echo esc_url($link); ?>">
    </div>
    <div class="property_unit_content_grid_small_details">
        <div class="blog_unit_meta">
            <?php echo esc_html(trim($agent_position)); ?>
        </div>
        <h4>
            <a href="<?php echo esc_url($link); ?>" <?php echo $target; ?>>
                <?php echo wp_kses_post($title); ?>
            </a>
        </h4>
        <div class="property_unit_content_grid_small_details_location">
            <?php echo wp_kses_post(trim($excerpt)); ?>
        </div>
    </div>
</div>