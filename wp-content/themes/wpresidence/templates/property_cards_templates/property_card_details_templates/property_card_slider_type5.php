<?php
/**
 * Template for displaying property slider on property cards (Type 5)
 *
 * This template is part of the WpResidence theme and is used to show
 * a slider of property images on property cards. It's compatible with
 * Bootstrap 5.3 and provides fallback for non-slider display.
 *
 * @package WpResidence
 * @subpackage PropertyCard
 * @since WpResidence 1.0
 */

// Get property details
$title = get_the_title();
$link = esc_url(get_permalink());


$max_num_images= $wpresidence_property_cards_context['wp_estate_prop_list_slider_image_number'];
$post_attachments = wpestate_generate_property_slider_image_ids_cache($postID, $property_unit_cached_data, true);


// Check if slider is enabled
if ($wpresidence_property_cards_context['wpestate_property_unit_slider'] == 1) {
    $slides = '';
    $no_slides = 0;
    $first_item = true;

    // Generate carousel items
    foreach ($post_attachments as $attachment_id) {
        if (!wp_attachment_is_image($attachment_id)) {
            continue; // Skip this attachment if it's not an image
        }
        $active_class = $first_item ? 'active' : ''; // Add 'active' class to the first item


        $preview = wp_get_attachment_image_src($attachment_id, 'listing_full_slider');
        $slides .= sprintf(
            '<div class="carousel-item item %s" style="background-image:url(%s)"></div>',
            esc_attr($active_class),
            esc_url($preview[0])
        );

        $no_slides++;
        $first_item = false; // Set to false after the first item

        // exit if we got the max number of images
        if ($no_slides >= $max_num_images) {
            break;
        }
    }

    if ($no_slides === 0) {
        $placeholder_url = wpestate_get_property_card_placeholder_image();
        $slides .= sprintf(
            '<div class="carousel-item item active" style="background-image:url(%s)"></div>',
            esc_url($placeholder_url)
        );
        $no_slides = 1;
    }

    $unique_prop_id = uniqid();

    // Output carousel structure
    ?>
    <div id="property_unit_carousel_<?php echo esc_attr($unique_prop_id); ?>" class="carousel property_unit_carousel slide" data-bs-interval="false">
        <a href="<?php echo esc_url($link); ?>" target="<?php echo esc_attr(wpresidence_get_option('wp_estate_unit_card_new_page', '')); ?>">
        <div class="carousel-inner">
     
            <?php echo $slides; ?>
        </div>

       </a>

        <?php if ($no_slides > 0) : ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#property_unit_carousel_<?php echo esc_attr($unique_prop_id); ?>" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden"><?php esc_html_e('Previous', 'wpresidence'); ?></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#property_unit_carousel_<?php echo esc_attr($unique_prop_id); ?>" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden"><?php esc_html_e('Next', 'wpresidence'); ?></span>
            </button>
        <?php endif; ?>
    </div>
    <?php
} else {

    // Get main image
    $main_image = wp_get_attachment_image_src(get_post_thumbnail_id($postID), 'listing_full_slider');
    $main_image_url = isset($main_image[0]) ? $main_image[0] : wpestate_get_property_card_placeholder_image();


    // Display single image if slider is disabled
    printf(
        '<div class="property_unit_type5_content" style="background-image:url(%s)"></div>',
        esc_url($main_image_url)
    );
}
?>