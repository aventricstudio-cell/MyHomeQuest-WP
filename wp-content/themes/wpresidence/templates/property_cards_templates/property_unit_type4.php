<?php
/**MILLDONE
 * Template for displaying Property Unit Type 4
 * src: templates\property_cards_templates\property_unit_type4.php
 * This file is part of the WpResidence theme and is used to render
 * a specific type of property listing card (Type 4).
 */

 
// Set up necessary variables
$conten_class = $wpestate_options['content_class'] ?? '';
$wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
$where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
$wp_estate_use_composer_details = wpresidence_get_option('wp_estate_use_composer_details', '');

// Retrieve essential property details
$title = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'title');
$link = esc_url(wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'permalink')); 


// Get featured media using cache function with fallback
$main_image = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'featured_media', 'listing_full_slider');



?>

<div class="<?php echo esc_html($wpresidence_property_cards_context['property_unit_class']['col_class']); ?> listing_wrapper  property_unit_type4"
    data-org="<?php echo esc_attr($wpresidence_property_cards_context['property_unit_class']['col_org']); ?>"
    data-main-modal="<?php echo esc_attr($main_image); ?>"
    data-modal-title="<?php echo esc_attr($title); ?>"
    data-modal-link="<?php echo esc_attr($link); ?>"
    data-listid="<?php echo intval($postID); ?>">

    <div class="property_listing property_unit_type4 <?php echo wpestate_interior_classes($wpresidence_property_cards_context['wpestate_uset_unit']); ?>"
         data-link="<?php echo ( $wpresidence_property_cards_context['wpestate_property_unit_slider'] == 0) ? esc_url($link) : ''; ?>">

        <?php 
        if ($wpresidence_property_cards_context['wpestate_uset_unit'] == 1) {
            // Custom unit structure
            wpestate_build_unit_custom_structure($wpestate_custom_unit_structure, $postID, $wpestate_property_unit_slider);
        } else { 
            // Default unit structure
        ?>
            <div class="listing-unit-img-wrapper">
                <div class="featured_gradient"></div>
                <?php 
                // Include property card slider
                include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_slider.php') );
                
                // Include property card tags
                     include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_tags.php'));
                
                // Include property card actions
                     include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_actions_type_default.php'));
                ?>
            </div>

            <div class="property-unit-information-wrapper">
                <?php
                if ($wp_estate_use_composer_details == 'yes') {         
                    // Use composer details
                    wpestate_return_property_card_content($postID,$property_unit_cached_data,$wpresidence_property_cards_context);
                } else {
                    // Use default template parts
                    include( locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_title.php'));
                    include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_details_type4.php'));
                }

                // Check if agent information should be displayed
                if (wpresidence_get_option('property_card_agent_show_row', '') == 'yes') { 
                ?>   
                <div class="property_location">
                    <div class="propery_price4_grid">
                        <?php  wpestate_show_price_from_cache($property_unit_cached_data, $wpestate_currency, $where_currency);?>
                    </div>

                    <?php  include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_agent_details_type4.php')); ?>
                </div>
                <?php 
                } 
                ?>
            </div>
        <?php
        } // end if custom structure
        ?>
    </div>
</div>