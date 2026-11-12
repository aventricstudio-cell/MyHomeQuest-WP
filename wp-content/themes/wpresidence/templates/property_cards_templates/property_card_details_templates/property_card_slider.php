<?php
/** 
 * Property Card Slider Template (Bootstrap 5 version, Thumbnail First)
 * src: templates\property_cards_templates\property_card_details_templates\property_card_slider.php
 * This template displays the image slider for a property card in the WpResidence theme,
 * using Bootstrap 5. The thumbnail is always the first slide, there's no image duplication,
 * and the active class is correctly assigned.
 *
 * @package WpResidence
 * @subpackage PropertyCard
 * @since 1.0
 */
// Get property ID from cached data or fallback to post ID


// Retrieve property details
$title = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'title');
$link = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'permalink');
$unique_prop_id = uniqid();
$post_attachments = wpestate_generate_property_slider_image_ids_cache($postID, $property_unit_cached_data, true);

$max_num_images = $wpresidence_property_cards_context['wp_estate_prop_list_slider_image_number'];

// Get thumbnail image
$thumb_prop = wpestate_return_property_card_thumb_cache($property_unit_cached_data, $postID, 'property_listings');

// Check if slider is enabled
if ($wpresidence_property_cards_context['wpestate_property_unit_slider'] == 1) {
    // Generate carousel structure
    $slides = '';
    $no_slides = 0;
    $first_item = true;
    
    foreach ($post_attachments as $attachment_id) {
        if (!wp_attachment_is_image($attachment_id)) {
            continue; // Skip this attachment if it's not an image
        }

        $preview = wp_get_attachment_image_src($attachment_id, 'property_listings');

        $active_class = $first_item ? 'active' : ''; // Add 'active' class to the first item

        $slides .= sprintf(
            '<div class="carousel-item %s">
                <a href="%s" target="%s">
                    <img width="%dpx" height="%dpx" src="%s" alt="%s" class="d-block w-100 img-fluid" loading="lazy" />
                </a>
            </div>',
            esc_attr($active_class),
            esc_url($link),
            esc_attr(wpresidence_get_option('wp_estate_unit_card_new_page', '')),
            esc_attr($preview[1]),
            esc_attr($preview[2]),
            esc_attr($preview[0]),
            esc_attr($title)
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
            '<div class="carousel-item active">
                <a href="%s" target="%s">
                    <img src="%s" alt="%s" class="d-block w-100 img-fluid" loading="lazy" />
                </a>
            </div>',
            esc_url($link),
            esc_attr(wpresidence_get_option('wp_estate_unit_card_new_page', '')),
            esc_url($placeholder_url),
            esc_attr($title)
        );
        $no_slides = 1;
    }
    ?>
    <div id="property_unit_carousel_<?php echo esc_attr($unique_prop_id); ?>" class="carousel property_unit_carousel slide" data-bs-interval="false">
        <div class="carousel-inner">
           
            <?php echo $slides; ?>
        </div>
        <?php if ($no_slides > 0) : ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#property_unit_carousel_<?php echo esc_attr($unique_prop_id); ?>" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#property_unit_carousel_<?php echo esc_attr($unique_prop_id); ?>" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        <?php endif; ?>
    </div>
    <?php
} else {
    // Display single thumbnail
    printf(
        '<a href="%s" target="%s">%s</a>',
        esc_url($link),
        esc_attr(wpresidence_get_option('wp_estate_unit_card_new_page', '')),
        $thumb_prop
    );
}
?>