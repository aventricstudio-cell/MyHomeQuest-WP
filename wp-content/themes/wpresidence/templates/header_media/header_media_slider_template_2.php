<?php
/** MILLDONE
 * Template for individual slides in the WPResidence theme slider type 2
 *
 * This template generates the HTML for a single slide in the type 2 slider,
 * including property details, pricing, and agent information.
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

// Retrieve property price and label information
$price = floatval(get_post_meta($postID, 'property_price', true));
$price_label = '<span class="">' . esc_html(get_post_meta($postID, 'property_label', true)) . '</span>';
$price_label_before = '<span class="">' . esc_html(get_post_meta($postID, 'property_label_before', true)) . '</span>';

// Retrieve property location information
$property_city = get_the_term_list($postID, 'property_city', '', ', ', '');
$property_area = get_the_term_list($postID, 'property_area', '', ', ', '');

// Format the price display
if ($price != 0) {
    $price = wpestate_show_price($postID, $wpestate_currency, $where_currency, 1);
} else {
    $price = $price_label_before . '' . $price_label;
}

// Retrieve agent details for the property
$realtor_details = wpestate_return_agent_details($postID);
?>

<!-- Main slide container -->
<div class="item_type2 <?php echo $active; ?>" style="background-image:url('<?php echo esc_url($property_featured_image_url); ?>');height:<?php echo $theme_slider_height; ?>px;">
    <!-- Property details container -->
    <div class="prop_new_details" data-href="<?php echo esc_url(get_permalink($postID)); ?>">
        <!-- Background overlay for better text visibility -->
        <div class="prop_new_details_back"></div>
        
        <!-- Container for property information -->
        <div class="prop_new_detals_info">

            <div class="prop_new_detals_info-image">
                <!-- Agent picture with conditional link -->
                <?php if ($realtor_details['link'] != ''): ?>
                    <a href="<?php echo esc_url($realtor_details['link']); ?>">
                        <div class="theme_slider2_agent_picture" style="background-image:url('<?php echo esc_url(wpestate_agent_picture($postID)); ?>');"></div>
                    </a>
                <?php else: ?>
                    <div class="theme_slider2_agent_picture" style="background-image:url('<?php echo esc_url(wpestate_agent_picture($postID)); ?>');"></div>
                <?php endif; ?>
            </div>
        
            <div class="prop_new_detals_info-details">

                <!-- Property price display -->
                <div class="theme-slider-price">
                    <?php echo $price; ?>
                </div>
                
                <!-- Property title with link -->
                <h3>
                    <a href="<?php echo esc_url(get_permalink($postID)); ?>">
                        <?php echo get_the_title($postID); ?>
                    </a>
                </h3>
                
                <!-- Property location information -->
                <div class="theme-slider-location">
                    <?php
                    if ($property_area != '') {
                        echo wp_kses_post($property_area) . ', ';
                    }
                    if ($property_city != '') {
                        echo wp_kses_post($property_city);
                    }
                    ?>
                </div>
            </div>


        </div>
    </div>
</div>