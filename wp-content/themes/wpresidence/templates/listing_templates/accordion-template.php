<?php
/**MILLDONE
 * WpResidence Theme - Accordion Template for Property Pages
 * src: templates\listing_templates\accordion-template.php
 * This file contains the structure for displaying property information in an accordion format.
 * It loads different sections of the property page based on the layout order defined in theme options.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since 1.0.0
 *
 * Dependencies:
 * - wpresidence_get_option()
 * - Various wpestate_property_*_v2() functions for each section
 *
 * Usage: This template is included in property page layouts to display property information
 * in an accordion format. The order and visibility of sections are controlled by theme options.
 */

// Retrieve the layout order from theme options
$layout = wpresidence_get_option('wp_estate_property_page_acc_order');

// Loop through the enabled sections in the layout
foreach ($layout['enabled'] as $key => $label) :
    switch ($key) {
        case 'overview':
            wpestate_property_overview_v2($post->ID);
            break;
        case 'description':
            wpestate_property_description_v2($post->ID);
            break;
        case 'booking_shortcode':
            wpestate_property_booking_shortcode_v2($post->ID);
            break;
        case 'documents':
            wpestate_property_documents_v2($post->ID);
            break;
        case 'energy-savings':
            wpestate_property_energy_savings_v2($post->ID);
            break;
        case 'multi-units':
            wpestate_property_multi_units_v2($post->ID);
            break;
        case 'address':
            wpestate_property_address_v2($post->ID);
            break;
        case 'listing_details':
            wpestate_property_listing_details_v2($post->ID);
            break;
        case 'features':
            wpestate_property_features_v2($post->ID);
            break;
        case 'video':
            wpestate_property_video_v2($post->ID);
            break;
        case 'map':
            wpestate_property_map_v2($post->ID);
            break;
        case 'virtual_tour':
            wpestate_property_virtual_tour_v2($post->ID);
            break;
        case 'walkscore':
            wpestate_property_walkscore_v2($post->ID);
            break;
        case 'nearby':
            wpestate_property_nearby_v2($post->ID);
            break;
        case 'payment_calculator':
            wpestate_property_payment_calculator_v2($post->ID);
            break;
        case 'floor_plans':
            wpestate_property_floor_plans_v2($post->ID); 
            break;
        case 'page_views':
            wpestate_property_page_views_v2($post->ID);
            break;
        case 'schedule_tour':
            wpestate_property_schedule_tour_v2($post->ID); 
            break;
        case 'agent_area':
            wpestate_property_agent_area_v2($post->ID, $wpestate_options);
            break;
        case 'other_agents':
            wpestate_property_other_agents_v2($post->ID, $wpestate_options);
            break;
        case 'reviews':
            wpestate_property_reviews_v2($post->ID);
            break;              
        case 'similar':
            wpestate_property_similar_listings_v2($post->ID);
            break;
    }
endforeach;
?>