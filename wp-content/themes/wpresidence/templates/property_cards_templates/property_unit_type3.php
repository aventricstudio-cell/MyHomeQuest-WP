<?php
/** MILLDONE
 * Template for displaying Property Unit Type 3
 * src: templates\property_cards_templates\property_unit_type3.php
 * This file is part of the WpResidence theme and is used to render
 * a specific type of property listing card (Type 3).
 */

// Set up necessary variables
$conten_class = isset($wpestate_options['content_class']) ? $wpestate_options['content_class'] : "";


$title = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'title');
$link = esc_url(wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'permalink')); 
$main_image = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'featured_media', 'listing_full_slider');


$wp_estate_use_composer_details = wpresidence_get_option('wp_estate_use_composer_details', '');

// Determine the data-link value based on the slider setting
$data_link = ( $wpresidence_property_cards_context['wpestate_property_unit_slider'] == 0) ? esc_url($link) : '';
?>

<div class="<?php echo esc_html($wpresidence_property_cards_context['property_unit_class']['col_class']); ?> listing_wrapper   property_unit_type3" 
    data-org="<?php echo esc_attr($wpresidence_property_cards_context['property_unit_class']['col_org']); ?>"   
    data-main-modal="<?php echo esc_attr($main_image); ?>"
    data-modal-title="<?php echo esc_attr($title); ?>"
    data-modal-link="<?php echo esc_attr($link); ?>"
    data-listid="<?php echo intval($postID); ?>">

    <div class="property_listing property_unit_type3 <?php echo wpestate_interior_classes($wpresidence_property_cards_context['wpestate_uset_unit']); ?>" 
         data-link="<?php echo $data_link; ?>">

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
                include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_price_type3.php')); 
                include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_details_type3.php')); 
                include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_adress_type3.php'));
            }

            // Check if agent information should be displayed
            if (wpresidence_get_option('property_card_agent_show_row', '') == 'yes') {
                ?>     
                <div class="property_location">
                    <?php 
                    // Include agent details
                    include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_agent_details.php')); 
                    ?>
                    <div class="unit_type3_details">
                        <a href="<?php echo esc_url($link); ?>"><?php echo esc_html__('details', 'wpresidence') ?></a>
                    </div>
                </div>    
                <?php
            } 
            ?>
            </div>
            <?php
        }
        ?>
    </div>             
</div>