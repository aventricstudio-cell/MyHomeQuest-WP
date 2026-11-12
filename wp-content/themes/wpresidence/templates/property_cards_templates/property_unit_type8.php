<?php
/** MILLDONE
 * Template for displaying a property unit of type 8
 * src: templates\property_cards_templates\property_unit_type8.php
 * This template is part of the WpResidence theme and is used to display individual
 * property units in a grid or list view. It's typically called by loop files or
 * shortcodes that display multiple properties.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since WpResidence 1.0
 */

// Initialize content class
$content_class = isset($wpestate_options['content_class']) ? $wpestate_options['content_class'] : '';



// Retrieve essential property details
$title = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'title');
$link = esc_url(wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'permalink')); 

// Get featured media using cache function with fallback
$main_image = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'featured_media', 'listing_full_slider');


// Check if we should use the property page composer for details
$wp_estate_use_composer_details = wpresidence_get_option('wp_estate_use_composer_details', '');



?>  

<div class="<?php echo esc_html($wpresidence_property_cards_context['property_unit_class']['col_class']); ?> listing_wrapper  property_unit_type8" 
    data-org="<?php echo esc_attr($wpresidence_property_cards_context['property_unit_class']['col_org']); ?>"   
    data-main-modal="<?php echo esc_attr($main_image); ?>"
    data-modal-title="<?php echo esc_attr($title); ?>"
    data-modal-link="<?php echo esc_attr($link); ?>"
    data-listid="<?php echo intval($postID); ?>"> 
    
    <div class="property_listing property_unit_type8 <?php echo wpestate_interior_classes($wpresidence_property_cards_context['wpestate_uset_unit']); ?>" 
         data-link="<?php echo $wpresidence_property_cards_context['wpestate_property_unit_slider'] == 0 ? esc_url($link) : ''; ?>">

        <?php 
        if ($wpresidence_property_cards_context['wpestate_uset_unit'] == 1) {
            // Build custom unit structure if enabled
            wpestate_build_unit_custom_structure($wpestate_custom_unit_structure, $postID, $wpestate_property_unit_slider);
        } else {
            // Default structure
            ?>
            <div class="listing-unit-img-wrapper">
                <div class="featured_gradient"></div>
                <?php include(locate_template('templates/property_cards_templates/property_card_details_templates/property_card_slider.php')); ?>
                <?php include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_tags.php')); ?>
                
                <?php  include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_agent_details_type8.php')); ?>
                <?php  
                if (wpresidence_get_option('property_card_agent_show_favorite', '') == 'yes') { 
                    include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_favorite.php')); 
                }
                ?>
            </div>
    
            <div class="property-unit-information-wrapper">
            <?php 
                if ($wp_estate_use_composer_details == 'yes') {         
                    // Use property page composer for details
                    wpestate_return_property_card_content($postID, $property_unit_cached_data,$wpresidence_property_cards_context,8);
                } else {
                    // Display default property information
                    include(locate_template('templates/property_cards_templates/property_card_details_templates/property_card_title.php'));
                    echo wpestate_return_property_card_categories($postID, 1);
               
                    include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_details_type8.php'));
                    include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_price.php'));
                }
            ?>
            </div>
        <?php
        } // end if custom structure
        ?>
    </div>    
</div>