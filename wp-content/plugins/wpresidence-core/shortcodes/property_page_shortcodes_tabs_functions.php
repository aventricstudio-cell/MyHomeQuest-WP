<?php
/**
 * Property Page Design Tab Functions
 *
 * This file contains functions for generating and displaying property page tabs
 * in the WpResidence theme. It includes the main tab generation function and
 * a helper function for creating the tab content.
 *
 * @package WpResidence
 * @subpackage PropertyTabs
 * @since 1.0.0
 */

/**
 * Generate property page design tabs
 *
 * This function creates the structure for property page tabs, including
 * description, address, details, amenities, map, and more.
 *
 * @param array $attributes Shortcode attributes
 * @param string|null $content Shortcode content (unused)
 * @return string HTML markup for property page tabs
 */
if (!function_exists('wpestate_property_page_design_tab')) {
    function wpestate_property_page_design_tab($attributes, $content = null) {
  
        global $post;
        global $propid;
        
        // Initialize variables
        $return_string = '';
        $tab_titles = array(
            'description'        => '',
            'property_address'   => '',
            'property_details'   => '',
            'amenities_features' => '',
            'map'                => '',
            'walkscore'          => '',
            'floor_plans'        => '',
            'page_views'         => '',
            'yelp_details'       => '',
            'virtual_tour'     => '',
        );

        // Set up shortcode attributes
        $attributes = shortcode_atts(
            array(
                'description'        => esc_html__("Description", "wpresidence-core"),
                'property_address'   => esc_html__("Property Address", "wpresidence-core"),
                'property_details'   => esc_html__("Property Details", "wpresidence-core"),
                'amenities_features' => esc_html__("Amenities and Features", "wpresidence-core"),
                'map'                => esc_html__("Map", "wpresidence-core"),
                'virtual_tour'       => esc_html__("Virtual Tour", "wpresidence-core"),
                'walkscore'          => esc_html__("Walkscore", "wpresidence-core"),
                'floor_plans'        => esc_html__("Floor Plans", "wpresidence-core"),
                'page_views'         => esc_html__("Page Views", "wpresidence-core"),
                'yelp_details'       => esc_html__("Yelp Details", "wpresidence-core"),
                'is_elementor'       => '',
            ),
            $attributes
        );

        // Handle Elementor compatibility
        if (intval($propid) == 0 && isset($attributes['is_elementor']) && intval($attributes['is_elementor'] == 1)) {
        $propid = wpestate_return_elementor_id();
        }

        // Assign attribute values to tab titles
        foreach ($tab_titles as $key => $value) {
            if (isset($attributes[$key])) {
                $tab_titles[$key] = $attributes[$key];
            }
        }

        // Generate tab content
        $return_string .= estate_property_page_generated_tab($propid, $tab_titles, $attributes);

        return $return_string;
    }
}

/**
 * Generate property page tab content
 *
 * This function creates the content for each property page tab.
 *
 * @param int $propid Property ID
 * @param array $tab_titles Array of tab titles
 * @param array $attributes Shortcode attributes
 * @return string HTML markup for tab content
 */
function estate_property_page_generated_tab($propid, $tab_titles, $attributes) {
    // Initialize variables
    $walkscore_api = esc_html(wpresidence_get_option('wp_estate_walkscore_api', ''));
    $show_graph_prop_page = esc_html(wpresidence_get_option('wp_estate_show_graph_prop_page', ''));
    $random = rand(0, 99999);
    $active_class = "active";
    $active_class_tab = "active";
    $yelp_client_id = wpresidence_get_option('wp_estate_yelp_client_id', '');
    $yelp_client_api_key_2018 = wpresidence_get_option('wp_estate_yelp_client_api_key_2018', '');

    // Start building the tab structure
    $return = '<div role="tabpanel" id="tab_prpg">';
    $return .= '<ul class="nav nav-tabs"  role="tablist">';

 
    // Generate tab headers
    foreach ($tab_titles as $key => $title) {
        if (!empty($title)) {
            $return .= generate_tab_header($key, $title, $random, $active_class);
            $active_class = '';
        }
    }

    $return .= '</ul>';

    // Generate tab content
    $return .= '<div class="tab-content">';

    foreach ($tab_titles as $key => $title) {
        if (!empty($title)) {
            $return .= generate_tab_content($key, $propid, $random, $active_class_tab, $attributes);
            $active_class_tab = '';
        }
    }

    $return .= '</div></div>';

    return $return;
}

/**
 * Generate tab header
 *
 * Helper function to create individual tab headers.
 *
 * @param string $key Tab key
 * @param string $title Tab title
 * @param int $random Random number for unique IDs
 * @param string $active_class Active class for the first tab
 * @return string HTML markup for tab header
 */
function generate_tab_header($key, $title, $random, $active_class) {
    $tab_id = esc_attr($key . $random);
    return sprintf(
        '<li class="nav-item" role="presentation">
            <button class="nav-link %s" id="%s-tab" data-bs-toggle="tab" data-bs-target="#%s" type="button" role="tab" aria-controls="%s" aria-selected="%s">%s</button>
        </li>',
        esc_attr($active_class),
        $tab_id,
        $tab_id,
        $tab_id,
        $active_class === 'active' ? 'true' : 'false',
        esc_html($title)
    );
}

/**
 * Generate tab content
 *
 * This function creates the content for individual property page tabs.
 * It handles different types of content based on the tab key and uses
 * various helper functions to generate the appropriate content.
 *
 * @param string $key Tab key identifier
 * @param int $propid Property ID
 * @param int $random Random number for unique IDs
 * @param string $active_class_tab Active class for the first tab content
 * @param array $attributes Shortcode attributes
 * @return string HTML markup for tab content
 */
function generate_tab_content($key, $propid, $random, $active_class_tab, $attributes) {
    // Sanitize inputs
    $tab_id = esc_attr($key . $random);
    $content = '';

    // Generate content based on tab key
    switch ($key) {
        case 'description':
            $content = estate_listing_content($propid);
            break;

        case 'property_address':
            $content = estate_listing_address($propid);
            break;

        case 'property_details':
            $wpestate_prop_all_details = get_post_custom($propid);
            $content = estate_listing_details($propid, $wpestate_prop_all_details, '');
            break;

        case 'amenities_features':
            $wpestate_prop_all_details = get_post_custom($propid);
            $content = estate_listing_features($propid, '', 0, '', $wpestate_prop_all_details);
            break;

        case 'map':
            $content = do_shortcode('[property_page_map propertyid="' . esc_attr($propid) . '" istab="1"][/property_page_map]');
            break;

        case 'virtual_tour':
            $virtual_tour_content = trim(get_post_meta($propid, 'embed_virtual_tour', true));
            if (!empty($virtual_tour_content)) {
                ob_start();
       
                wpestate_virtual_tour_details($propid);
                $content = ob_get_clean();
            }
            break;

        case 'walkscore':
            $walkscore_api = esc_html(wpresidence_get_option('wp_estate_walkscore_api', ''));
            if (!empty($walkscore_api)) {
                ob_start();
                wpestate_walkscore_details($propid);
                $content = ob_get_clean();
            }
            break;

        case 'floor_plans':
            $plan_title_array = get_post_meta($propid, 'plan_title', true);
            if (is_array($plan_title_array)) {
                ob_start();
                estate_floor_plan($propid);
                $content = ob_get_clean();
            }
            break;

        case 'page_views':
            $ajax_nonce = wp_create_nonce("wpestate_tab_stats");
            $content = '<div class="panel-body">
                            <input type="hidden" id="wpestate_tab_stats" value="' . esc_attr($ajax_nonce) . '" />
                            <canvas id="myChart"></canvas>
                        </div>';
            break;

        case 'yelp_details':
            $yelp_client_id = wpresidence_get_option('wp_estate_yelp_client_id', '');
            $yelp_client_api_key_2018 = wpresidence_get_option('wp_estate_yelp_client_api_key_2018', '');
            if (!empty($yelp_client_id) && !empty($yelp_client_api_key_2018)) {
                ob_start();
                wpestate_yelp_details($propid);
                $content = ob_get_clean();
            }
            break;

        // Add more cases as needed for additional tabs
    }

    // Generate the tab content HTML structure
    $tab_content = sprintf(
        '<div class="tab-pane fade %s" id="%s" role="tabpanel" aria-labelledby="%s-tab">%s</div>',
        esc_attr($active_class_tab . ($active_class_tab === 'active' ? ' show' : '')),
        $tab_id,
        $tab_id,
        $content
    );

    return $tab_content;
}