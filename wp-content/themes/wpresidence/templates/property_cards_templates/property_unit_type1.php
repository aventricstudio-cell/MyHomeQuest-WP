<?php
/** MILLDONE
 * Template for displaying a property unit of type 1 in the WpResidence theme.
 * This file is typically included in property listing templates.
 * src: templates\property_cards_templates\property_unit_type1.php
 * @package WpResidence
 * @subpackage PropertyCard
 */

// Retrieve property details
$title = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'title');
$link = esc_url(wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'permalink')); 

// Get featured media using cache function with fallback
$main_image = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'featured_media', 'listing_full_slider');

$use_composer_details = wpresidence_get_option('wp_estate_use_composer_details', '') === 'yes';

// Retrieve theme options for displaying various elements
$show_featured = wpresidence_get_option('property_card_agent_show_featured', '') === 'yes';
$show_favorite = wpresidence_get_option('property_card_agent_show_favorite', '') === 'yes';
$show_status = wpresidence_get_option('property_card_agent_show_status', '') === 'yes';
$show_agent_row = wpresidence_get_option('property_card_agent_show_row', '') === 'yes';

// Determine the content class (if set in options)
$content_class = isset($wpestate_options['content_class']) ? $wpestate_options['content_class'] : '';


?>

<div class="<?php echo esc_attr($wpresidence_property_cards_context['property_unit_class']['col_class']); ?> listing_wrapper  property_unit_type1"
     data-org="<?php echo esc_attr($wpresidence_property_cards_context['property_unit_class']['col_org']); ?>"
     data-main-modal="<?php echo esc_attr($main_image); ?>"
     data-modal-title="<?php echo esc_attr($title); ?>"
     data-modal-link="<?php echo esc_attr($link); ?>"
     data-listid="<?php echo intval($postID); ?>">

    <div class="property_listing property_unit_type1 <?php echo esc_attr(wpestate_interior_classes($wpresidence_property_cards_context['wpestate_uset_unit']) ); ?>"
         data-link="<?php echo $wpresidence_property_cards_context['wpestate_property_unit_slider'] == 0 ? esc_url($link) : ''; ?>">

        <?php if ($wpresidence_property_cards_context['wpestate_uset_unit'] == 1) : ?>
            <?php wpestate_build_unit_custom_structure($wpestate_custom_unit_structure, $postID, $wpestate_property_unit_slider); ?>
        <?php else : ?>
            <div class="listing-unit-img-wrapper">
                <div class="prop_new_details">
                    <div class="prop_new_details_back"></div>
                    <div class="featured_gradient"></div>
                </div>

                <?php
                
                include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_slider.php') );
                     include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_price.php'));

                if ($show_favorite) {
                    include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_favorite.php'));
                }
                if ($show_status) {
                    include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_status.php'));
                }
                ?>
            </div>

            <?php if ($show_featured) : ?>
                <?php  include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_featured_label.php')); ?>
            <?php endif; ?>

            <div class="property-unit-information-wrapper">
                <?php
                if ($use_composer_details) {
                    wpestate_return_property_card_content($postID,$property_unit_cached_data,$wpresidence_property_cards_context);
                } else {
                    include( locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_title.php'));
                    include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_adress_type2.php'));
                    include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_category_type1.php'));
                    include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_details_type1.php'));
                }

                if ($show_agent_row) : ?>
                    <div class="property_location">
                        <?php
                        include (locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_agent_details_default.php'));
                        include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_actions_type2.php'));
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>