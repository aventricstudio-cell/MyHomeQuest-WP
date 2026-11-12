<?php
/** MILLDONE
 * Property Tabs Template for WpResidence Theme
 * src: templates\listing_templates\tabs-template.php
 * This template generates the tabbed content for a single property page,
 * including both the tab structure and the content after the tabs.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0
 *
 * Dependencies:
 * - WordPress core
 * - WpResidence theme functions (various wpestate_property_* functions)
 *
 * Usage:
 * This template is typically included within the single property page template
 * of the WpResidence theme to display detailed property information.
 */

// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve the layout configuration for property page tabs
$layout = wpresidence_get_option('wp_estate_property_page_tab_order');

$tab_active_class = 'active';
$tab_data = array('list' => array(), 'tab_panel' => array());

if(isset($layout['enabled']['placebo'])) {
    unset( $layout['enabled'] ['placebo']);
}


// redux possible fuck up
if( isset($layout['enabled']['array'])  && $layout['enabled']['array']='Array' ){
    unset ( $layout['enabled']['array']);
}



// Build Tabs
foreach ($layout['enabled'] as $key => $label) :
    
    $partial_data = array();

    // Generate content for each tab based on the key
    switch ($key) {
        case 'overview':
            $partial_data = wpestate_property_overview_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'description':
            $partial_data = wpestate_property_description_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'booking_shortcode':
            $partial_data = wpestate_property_booking_shortcode_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'documents':
            $partial_data = wpestate_property_documents_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'energy-savings':
            $partial_data = wpestate_property_energy_savings_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'multi-units':
            $partial_data = wpestate_property_multi_units_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'address':
            $partial_data = wpestate_property_address_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'listing_details':
            $partial_data = wpestate_property_listing_details_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'features':
            $partial_data = wpestate_property_features_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'video':
            $partial_data = wpestate_property_video_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'map':
            $partial_data = wpestate_property_map_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'virtual_tour':
            $partial_data = wpestate_property_virtual_tour_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'walkscore':
            $partial_data = wpestate_property_walkscore_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'nearby':
            $partial_data = wpestate_property_nearby_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'payment_calculator':
            $partial_data = wpestate_property_payment_calculator_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'floor_plans':
            $partial_data = wpestate_property_floor_plans_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'page_views':
            $partial_data = wpestate_property_page_views_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'schedule_tour':
            $partial_data = wpestate_property_schedule_tour_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'agent_area':
            $partial_data = wpestate_property_agent_area_v2($post->ID, $wpestate_options, 'yes', $tab_active_class);
            break;
        case 'other_agents':
            $partial_data = wpestate_property_other_agents_v2($post->ID, $wpestate_options, 'yes', $tab_active_class);
            break;
        case 'reviews':
            $partial_data = wpestate_property_reviews_v2($post->ID, 'yes', $tab_active_class);
            break;
        case 'similar':
            $partial_data = wpestate_property_similar_listings_v2($post->ID, 'yes', $tab_active_class);
            break;
    }

    // Store the generated content if it exists
    if (isset($partial_data['list'])) {
        $tab_data['list'][] = $partial_data['list'];
        $tab_data['tab_panel'][] = $partial_data['tab_panel'];
    }

    $tab_active_class = ''; // Reset active class for next iteration
endforeach;

// Start output buffering to separate HTML from PHP
ob_start();


?>
<div role="tabpanel" id="tab_prpg">
    <ul class="nav nav-tabs" role="tablist">
        <?php
        foreach ($tab_data['list'] as $item) :
            echo wp_kses_post(trim($item));
        endforeach;
        ?>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <?php
        foreach ($tab_data['tab_panel'] as $item) :
            echo (trim($item));
        endforeach;
        ?>
    </div>
</div>

<?php
// Get the buffered content
$html_output = ob_get_clean();

// Output the tabs HTML
echo $html_output;

// After Tabs
foreach ($layout['after'] as $key => $label) :
    switch ($key) {
        case 'overview':
            include(locate_template( 'templates/listing_templates/property-page-templates/single-overview-section.php' ) );
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