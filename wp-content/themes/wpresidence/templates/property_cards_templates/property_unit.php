<?php
/** MILLDONE
 * Property Unit Template
 * src: templates\property_cards_templates\property_unit.php
 * This file is responsible for rendering individual property units in the WpResidence theme.
 * It includes various template parts and conditional logic to display property information.
 * Modified to use wpestate_return_data_from_cache_if_exists for retrieving cached data.
 *
 * @package WpResidence
 * @subpackage PropertyUnit
 * @since 1.0
 */

// Check if the property unit slider variable is set
if (!isset($wpestate_property_unit_slider)) {
    $wpestate_property_unit_slider = '';
}

// Get the content class from options, if set
$conten_class = isset($wpestate_options['content_class']) ? $wpestate_options['content_class'] : '';

// Get property details using cache with fallback
$title = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'title');
$link = esc_url(wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'permalink')); 


// Get featured media using cache function with fallback
$main_image = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'featured_media', 'listing_full_slider');


$wp_estate_use_composer_details = wpresidence_get_option('wp_estate_use_composer_details', '');
?>

<div class="<?php echo esc_html($wpresidence_property_cards_context['property_unit_class']['col_class']); ?>  listing_wrapper "
    data-org="<?php echo esc_attr($wpresidence_property_cards_context['property_unit_class']['col_org']); ?>"
    data-main-modal="<?php echo esc_attr($main_image); ?>"
    data-modal-title="<?php echo esc_attr($title); ?>"
    data-modal-link="<?php echo esc_attr($link); ?>"
    data-listid="<?php echo intval($postID); ?>">

    <div class="property_listing property_card_default <?php echo wpestate_interior_classes($wpresidence_property_cards_context['wpestate_uset_unit']); ?>"
        data-link="<?php if ($wpestate_property_unit_slider == 0) {
            echo esc_url($link);
        } ?>">

        <?php
        // Check if using custom unit structure
        if ($wpresidence_property_cards_context['wpestate_uset_unit'] == 1) {
            wpestate_build_unit_custom_structure($wpestate_custom_unit_structure, $postID, $wpestate_property_unit_slider);
        } else {
            // Default unit structure
        ?>
            <div class="listing-unit-img-wrapper">
                <div class="prop_new_details">
                    <div class="prop_new_details_back"></div>
                    <?php
                    // Include property card media details
                    include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_media_details.php'));
                    
                    // Include property card location
                    include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_location.php'));
               
                    ?>
                    <div class="featured_gradient"></div>
                </div>

                <?php
                // Include property card slider
                include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_slider.php') );
                
                // Include property card tags
                     include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_tags.php'));
                ?>
            </div>

            <div class="property-unit-information-wrapper">
                <?php
                // Check if using composer details
                if ($wp_estate_use_composer_details == 'yes') {
                    // Load property card content using a custom function
                 
                    wpestate_return_property_card_content($postID,$property_unit_cached_data,$wpresidence_property_cards_context);
                } else {
                    // Load default property card details using separate template parts
                    // Load property card title
                    include( locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_title.php'));
                    // Load property card price
                    include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_price.php'));
                    // Load property card content
                    include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_content.php'));
                    // Load property card additional details
                    include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_details_default.php'));
                }

                // Check if showing agent row
                if (wpresidence_get_option('property_card_agent_show_row', '') === 'yes') {
                ?>
                    <div class="property_location">
                        <?php
                        // Include agent details
                        include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_agent_details_default.php'));
                        
                        // Include property actions
                        include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_actions_type_default.php'));
                        ?>
                    </div>
                <?php
                }
                ?>
            </div>
        <?php
        } // End if custom structure
        ?>
    </div>
</div>