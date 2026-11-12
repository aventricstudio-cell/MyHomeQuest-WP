<?php
/** MILLDONE
 * Template for individual slides in the WPResidence theme slider type 3
 *
 * This template is included for each property in the slider within the 
 * wpestate_present_theme_slider_type3 function. It generates the HTML 
 * for a single slide, including property details and styling.
 *
 * Global Variables Used:
 * @global int $postID - The ID of the current property post
 * @global int $counter - The current slide number
 * @global int $theme_slider_height - The height of the slider
 * @global string $wpestate_currency - The currency symbol
 * @global string $where_currency - The currency position (before/after)
 *
 * @package WPResidence
 */

// Retrieve the featured image for the property
$property_featured_image_url = wpestate_get_property_featured_image($postID, 'property_full_map');



// Determine if this is the active (first) slide
if ($counter == 0) {
    $active = " active ";
} else {
    $active = " ";
}

// Retrieve property details
$property_size = wpestate_get_converted_measure($postID, 'property_size');
$property_bedrooms = get_post_meta($postID, 'property_bedrooms', true);
$property_bathrooms = get_post_meta($postID, 'property_bathrooms', true);

// Get a thumbnail version of the featured image for the indicator
$preview_indicator = wp_get_attachment_image_src(get_post_thumbnail_id($postID), 'agent_picture_thumb');
$ex_cont = '<img src="' . $preview_indicator[0] . '" alt="preview_indicator">';
?>

<!-- Main slide container -->
<div class="item <?php echo $active; ?>" data-hash="item<?php echo esc_attr($counter); ?>" data-href="<?php echo esc_url(get_permalink($postID)); ?>"
    style="height:<?php echo $theme_slider_height; ?>px;background-image:url('<?php echo esc_url($property_featured_image_url); ?>');">
   
    <!-- Gradient overlay for better text visibility -->
    <div class="theme_slider_3_gradient"></div>

    <!-- Container for property details -->
    <div class="slide_cont_block">
        <!-- Property price -->
        <div class="theme_slider_3_price">
            <?php echo wpestate_show_price($postID, $wpestate_currency, $where_currency, 1); ?>
        </div>

        <!-- Property title with link -->
        <a href="<?php echo esc_url(get_permalink($postID)); ?>" target="_blank">
            <h2><?php echo get_the_title($postID); ?></h2>
        </a>

        <!-- Property details row -->
        <div class="theme_slider_3_sec_row">
            <?php if ($property_bedrooms != ''): ?>
                <div class="inforoom_unit_type5"><?php echo esc_html($property_bedrooms) . ' ' . esc_html__('BD', 'wpresidence'); ?></div>
            <?php endif; ?>
            <?php if ($property_bathrooms != ''): ?>
                <div class="inforoom_unit_type5"><?php echo esc_html($property_bathrooms) . ' ' . esc_html__('BA', 'wpresidence'); ?><span></span></div>
            <?php endif; ?>
            <?php if ($property_size != ''): ?>
                <div class="inforoom_unit_type5"><?php echo trim($property_size); ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>