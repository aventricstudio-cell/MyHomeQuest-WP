<?php
/** MILLDONE
 * Template for displaying a property unit of type 2
 * src: templates\property_cards_templates\property_unit_type2.php
 * This template is part of the WpResidence theme and is used to display individual
 * property units in a grid or list view. It's typically called by loop files or
 * shortcodes that display multiple properties.
 *
 * @package WpResidence
 * @subpackage Property Templates
 * @since WpResidence 1.0
 */

 
// Initialize content class
// This variable is used to add additional classes to the main wrapper,
// allowing for layout customization based on theme options.
$content_class = isset($wpestate_options['content_class']) ? $wpestate_options['content_class'] : '';

// Retrieve essential property details
// These variables are used throughout the template to display property information
$title = get_the_title(); // The property title
$link = esc_url(get_permalink()); // The property's permalink, sanitized for security
$main_image = wpestate_return_property_card_main_image($postID, 'listing_full_slider');


// Check if we should use the property page composer for details
// This option allows for custom layouts of property details using a page builder
$wp_estate_use_composer_details = wpresidence_get_option('wp_estate_use_composer_details', '');

?>

<!-- Main property unit wrapper -->
<!-- The classes and data attributes here are used for filtering and layout purposes -->
<div class="<?php echo esc_html($wpresidence_property_cards_context['property_unit_class']['col_class']); ?> listing_wrapper  property_unit_type2"
    data-org="<?php echo esc_attr($wpresidence_property_cards_context['property_unit_class']['col_org']); ?>"
    data-main-modal="<?php echo esc_attr($main_image); ?>"
    data-modal-title="<?php echo esc_attr($title); ?>"
    data-modal-link="<?php echo esc_attr($link); ?>"
    data-listid="<?php echo intval($postID); ?>">

    <!-- Property listing inner wrapper -->
    <!-- This div contains all the property information and media -->
    <div class="property_listing property_unit_type2 <?php echo wpestate_interior_classes($wpresidence_property_cards_context['wpestate_uset_unit']); ?>"
         data-link="<?php echo $wpresidence_property_cards_context['wpestate_property_unit_slider'] == 0 ? esc_url($link) : ''; ?>">

        <?php
        // Check if we're using a custom unit structure
        // This allows for fully customizable property card layouts
        if ($wpresidence_property_cards_context['wpestate_uset_unit'] == 1) :
            // Build custom unit structure
            // This function is typically defined in estate_functions.php and allows for dynamic property card layouts
            wpestate_build_unit_custom_structure($wpestate_custom_unit_structure, $postID, $wpestate_property_unit_slider);
        else :
            // If not using custom structure, use the default layout
        ?>
            <!-- Property image wrapper -->
            <div class="listing-unit-img-wrapper">
                <?php
                // Display media details (e.g., number of images, if there's a video)
                include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_media_details.php'));
                ?>
                <!-- Gradient overlay for featured properties -->
                <div class="featured_gradient"></div>
                <?php
                // Include the property image slider
                // This file contains the logic for displaying multiple property images in a slider
                include(locate_template('templates/property_cards_templates/property_card_details_templates/property_card_slider.php'));
                
                // Display property tags (e.g., 'For Sale', 'For Rent')
                     include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_tags.php'));
                
                // Display favorite button if enabled in theme options
                if (wpresidence_get_option('property_card_agent_show_favorite', '') == 'yes') :
                    include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_favorite.php') );
                endif;
                ?>
            </div>

            <!-- Property information wrapper -->
            <div class="property-unit-information-wrapper">
            <?php
            // Check if we're using the property page composer for details
            if ($wp_estate_use_composer_details == 'yes') {
                // Display property content using the page composer
                // This function is typically defined in composer_functions.php
                wpestate_return_property_card_content($postID,$property_unit_cached_data,$wpresidence_property_cards_context);
            } else {
                // If not using page composer, include default property information templates
                include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_title.php'));
                include( locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_price.php'));
                include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_content.php'));
                include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_details_type2.php'));
            }

            // Check if we should display the agent information row
            if (wpresidence_get_option('property_card_agent_show_row', '') == 'yes') :
            ?>
                <!-- Agent information and property actions -->
                <div class="property_location">
                    <?php
                    // Display agent details
                         include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_agent_details_default.php'));
                    
                    // Set up for displaying property actions
                    $remove_fav = 1; // This variable is used in the actions template to determine if the favorite action should be removed
                    
                    // Include property actions (e.g., share, compare)
                    include(locate_template('templates/property_cards_templates/property_card_details_templates/property_card_actions_type_default.php'));
                    ?>
                </div>
            <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>