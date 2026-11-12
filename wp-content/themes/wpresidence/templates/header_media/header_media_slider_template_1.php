<?php
/** MILLDONE
 * Template for individual slides in the WPResidence theme classic slider
 *
 * This template generates the HTML for a single slide in the classic slider,
 * including property details, pricing, and key features.
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

// Retrieve property features
$beds = floatval(get_post_meta($postID, 'property_bedrooms', true));
$baths = floatval(get_post_meta($postID, 'property_bathrooms', true));
$size = wpestate_get_converted_measure($postID, 'property_size');

// Format the price display
if ($price != 0) {
    $price = wpestate_show_price($postID, $wpestate_currency, $where_currency, 1);
} else {
    $price = $price_label_before . '' . $price_label;
}
?>

<!-- Main slide container -->
<div class="item carousel-item theme_slider_classic <?php echo esc_attr($active); ?>" data-href="<?php echo esc_url(get_permalink($postID)); ?>"
    style="background-image:url('<?php echo esc_url($property_featured_image_url); ?>');height:<?php echo esc_attr($theme_slider_height); ?>px;">
   
    <!-- Gradient overlay for better text visibility -->
    <div class="featured_gradient"></div>

    <!-- Wrapper for slider content -->
    <div class="slider-content-wrapper">
        <div class="slider-content">
            <!-- Property title with link and truncation -->
            <h3>
                <a href="<?php echo esc_url(get_permalink($postID)); ?>">
                    <?php
                    $title = get_the_title($postID);
                    echo mb_substr($title, 0, 28);
                    if (mb_strlen($title) > 28) {
                        echo '...';
                    }
                    ?>
                </a>
            </h3>

            <!-- Truncated excerpt of the property description -->
            <span><?php echo wpestate_strip_words(get_the_excerpt($postID), 20); ?>...</span>

            <!-- Custom contact button or form -->
            <?php echo wpestate_theme_slider_contact($postID); ?>
           
            <!-- Price and property features section -->
            <div class="theme-slider-price">
                <?php echo $price; ?>
                <div class="listing-details">
                    <?php if ($beds != 0): ?>
                        <span class="inforoom">
                            <?php
                                include(locate_template('/templates/svg_icons/inforoom_icon.svg'));  
                                echo floatval($beds); ?>
                        </span>

  

                    <?php endif; ?>
                    <?php if ($baths != 0): ?>
                        <span class="infobath">
                            <?php 
                            include(locate_template('/templates/svg_icons/infobath_icon.svg'));  
                            echo floatval($baths); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($size != 0): ?>
                        <span class="infosize">
                            <?php 
                            include(locate_template('/templates/svg_icons/infosize_icon.svg'));  
                            echo  wp_kses_post( $size) ; ?>
                            </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Navigation arrows for the slider -->
            <a class="carousel-control-theme-next" href="#estate-carousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span></a>
            <a class="carousel-control-theme-prev" href="#estate-carousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span></a>
        </div>
    </div>
</div>