<?php
/** MILLDONE
 * Template for displaying Property Unit Type 5
 * src: templates\property_cards_templates\property_unit_type5.php
 * This file is part of the WpResidence theme and is used to render
 * a specific type of property listing card (Type 5).
 */


// Set up necessary variables
$conten_class = $wpestate_options['content_class'] ?? '';
// Retrieve essential property details
$title = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'title');
$link = esc_url(wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'permalink')); 


// Get featured media using cache function with fallback
$main_image = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'featured_media', 'listing_full_slider');


?>

<div class="<?php echo esc_html($wpresidence_property_cards_context['property_unit_class']['col_class']); ?> listing_wrapper  property_unit_type5"
     data-org="<?php echo esc_attr($wpresidence_property_cards_context['property_unit_class']['col_org']); ?>"
     data-main-modal="<?php echo esc_attr($main_image); ?>"
     data-modal-title="<?php echo esc_attr($title); ?>"
     data-modal-link="<?php echo esc_attr($link); ?>"
     data-listid="<?php echo intval($postID); ?>">

    <div class="property_unit_type5_content_wrapper property_listing" data-link="<?php echo esc_attr($link); ?>">
        <?php
        // Display property tags (e.g., featured, status)
             include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_tags.php'));
        ?>

        <div class="featured_gradient"></div>

        <?php
        // Display property slider
        include(locate_template('templates/property_cards_templates/property_card_details_templates/property_card_slider_type5.php'));
      
        ?>

        <div class="property_unit_type5_content_details">
            <?php
            // Display property price
            include ( locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_price.php'));

            // Display property title
            include ( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_title.php'));

            // Display property details specific to Type 5
            include ( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_details_type5.php'));
            ?>
        </div>
    </div>
</div>