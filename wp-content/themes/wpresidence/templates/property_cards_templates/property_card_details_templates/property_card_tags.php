<?php
/** MILLDONE
 * Property Card Tags Template
 * src: templates\property_cards_templates\property_card_details_templates\property_card_tags.php
 * This template is responsible for displaying tags (featured status and property status)
 * on property cards in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyCard
 * @since 1.0
 */

/**
 * Start of the tag wrapper.
 * This div contains all the tags that will be displayed on the property card.
 */
?>
<div class="tag-wrapper">
    <?php
    // Check if we should display the featured tag
    if (wpresidence_get_option('property_card_agent_show_featured', '') === 'yes') {
        // Only fetch the featured status if we're going to use it
      
        $featured = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID,  'meta',  'prop_featured');
        if (intval($featured )=== 1) {
            // Output the featured tag
            echo '<div class="featured_div">' . esc_html__('Featured', 'wpresidence') . '</div>';
        }
    }
    
    // Check if we should display the property status
    if (wpresidence_get_option('property_card_agent_show_status', '') === 'yes') {
        /**
         * Include the property status template.
         * This template is responsible for displaying the current status of the property
         * (e.g., for sale, for rent, etc.)
         */
        include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_status.php'));
    }
    ?>
</div>