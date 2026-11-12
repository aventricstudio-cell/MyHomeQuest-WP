<?php
/**
 * Property Page Design Accordion Functions
 *
 * This file contains functions for generating and displaying property page accordions
 * in the WpResidence theme. It includes the main accordion generation function and
 * a helper function for creating the accordion content.
 *
 * @package WpResidence
 * @subpackage PropertyAccordions
 * @since 1.0.0
 */

/**
 * Generate property page design accordion
 *
 * This function creates the structure for property page accordions, including
 * description, address, details, amenities, map, and more.
 *
 * @param array $attributes Shortcode attributes
 * @param string|null $content Shortcode content (unused)
 * @return string HTML markup for property page accordions
 */
if (!function_exists('wpestate_property_page_design_acc')) :
    function wpestate_property_page_design_acc($attributes, $content = null) {
        global $post;
        global $propid;

        $default_attributes = array(
            'css'                => '',
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
            'style'              => esc_html__("all open", "wpresidence-core"),
            'is_elementor'       => ''
        );

        $attributes = wp_parse_args($attributes, $default_attributes);

        // Handle Elementor compatibility
        if (intval($propid) == 0 && isset($attributes['is_elementor']) && intval($attributes['is_elementor'] == 1)) {
            $propid = wpestate_return_elementor_id();
        }

        $css_class = '';
        if (function_exists('vc_shortcode_custom_css_class')) {
            $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($attributes['css'], ' '), '', $attributes);
        }

        $return_string = '<div class="' . esc_attr($css_class) . '">';
        $return_string .= estate_property_page_generated_acc($css_class, $propid, $attributes['style'], $attributes);
        $return_string .= '</div>';

        return $return_string;
    }
endif;

/**
 * Generate property page accordion content
 *
 * This function creates the content for each property page accordion section.
 *
 * @param string $css_class CSS class for styling
 * @param int $propid Property ID
 * @param string $style Accordion style (all open, all closed, or only first open)
 * @param array $attributes Shortcode attributes
 * @return string HTML markup for accordion content
 */
if (!function_exists('estate_property_page_generated_acc')) :
    function estate_property_page_generated_acc($css_class, $propid, $style, $attributes) {
        $walkscore_api = esc_html(wpresidence_get_option('wp_estate_walkscore_api', ''));
        $show_graph_prop_page = esc_html(wpresidence_get_option('wp_estate_show_graph_prop_page', ''));
        $random = rand(0, 99999);
        $return = '';

        $is_all_open = $style === esc_html__("all open", "wpresidence-core");
        $is_first_open = $style === esc_html__("only the first one open", "wpresidence-core");

        $accordion_sections = array(
            'description'        => $attributes['description'],
            'property_address'   => $attributes['property_address'],
            'property_details'   => $attributes['property_details'],
            'amenities_features' => $attributes['amenities_features'],
            'map'                => $attributes['map'],
            'virtual_tour'       => $attributes['virtual_tour'],
            'walkscore'          => $attributes['walkscore'],
            'floor_plans'        => $attributes['floor_plans'],
            'page_views'         => $attributes['page_views'],
            'yelp_details'       => $attributes['yelp_details']
        );

        $return .= '<div class="accordion property-panel" id="propertyAccordion' . $random . '">';

        $first_item = true;
        foreach ($accordion_sections as $section_key => $section_title) {
            if (!empty($section_title)) {
                $is_expanded = ($is_all_open || ($is_first_open && $first_item));
                $return .= generate_accordion_item($section_key, $section_title, $propid, $random, $is_expanded, $attributes);
                $first_item = false;
            }
        }

        $return .= '</div>';

        // Add JavaScript for initializing accordions
        $return .= '<script type="text/javascript">
            jQuery(document).ready(function($) {
                $("#propertyAccordion' . $random . '").on("shown.bs.collapse", function () {
                    if (typeof wpestate_show_stat_accordion === "function") {
                        setTimeout(wpestate_show_stat_accordion, 200);
                    }
                });
            });
        </script>';

        return $return;
    }
endif;

/**
 * Generate individual accordion item
 *
 * Helper function to create a single accordion item.
 *
 * @param string $section_key Unique identifier for the accordion section
 * @param string $section_title Title of the accordion section
 * @param int $propid Property ID
 * @param int $random Random number for unique IDs
 * @param bool $is_expanded Whether the accordion item should be expanded by default
 * @param array $attributes Shortcode attributes
 * @return string HTML markup for a single accordion item
 */
function generate_accordion_item($section_key, $section_title, $propid, $random, $is_expanded, $attributes) {
    $item_id = esc_attr($section_key . $random);
    $expanded = $is_expanded ? 'true' : 'false';
    $show = $is_expanded ? 'show' : '';

    $content = get_accordion_content($section_key, $propid, $attributes);

    return sprintf(
        '<div class="accordion-item">
            <h2 class="accordion-header" id="heading%1$s">
                <button class="accordion-button %2$s" type="button" data-bs-toggle="collapse" data-bs-target="#collapse%1$s" aria-expanded="%3$s" aria-controls="collapse%1$s">
                    %4$s
                </button>
            </h2>
            <div id="collapse%1$s" class="accordion-collapse collapse %5$s" aria-labelledby="heading%1$s" data-bs-parent="#propertyAccordion%6$s">
                <div class="accordion-body">
                    %7$s
                </div>
            </div>
        </div>',
        $item_id,
        $is_expanded ? '' : 'collapsed',
        $expanded,
        esc_html($section_title),
        $show,
        $random,
        $content
    );
}

/**
 * Get content for accordion item
 *
 * Helper function to generate content for each accordion section.
 *
 * @param string $section_key Unique identifier for the accordion section
 * @param int $propid Property ID
 * @param array $attributes Shortcode attributes
 * @return string HTML content for the accordion section
 */
function get_accordion_content($section_key, $propid, $attributes) {
    switch ($section_key) {
        case 'description':
            return estate_listing_content($propid);
        case 'property_address':
            return estate_listing_address($propid);
        case 'property_details':
            $wpestate_prop_all_details = get_post_custom($propid);
            return estate_listing_details($propid, $wpestate_prop_all_details, '');
        case 'amenities_features':
            $wpestate_prop_all_details = get_post_custom($propid);
            return estate_listing_features($propid, '', 0, '', $wpestate_prop_all_details);
        case 'map':
            return do_shortcode('[property_page_map propertyid="' . esc_attr($propid) . '" istab="2"][/property_page_map]');
        case 'virtual_tour':
            return get_virtual_tour_content($propid);
        case 'walkscore':
            return get_walkscore_content($propid);
        case 'floor_plans':
            return get_floor_plans_content($propid);
        case 'page_views':
            return get_page_views_content($propid);
        case 'yelp_details':
            return get_yelp_content($propid);
        default:
            return '';
    }
}

// Helper functions for specific content types
function get_virtual_tour_content($propid) {
    $virtual_tour_content = trim(get_post_meta($propid, 'embed_virtual_tour', true));
    if (!empty($virtual_tour_content)) {
        ob_start();
        wpestate_virtual_tour_details($propid);
        return ob_get_clean();
    }
    return '';
}

function get_walkscore_content($propid) {
    $walkscore_api = esc_html(wpresidence_get_option('wp_estate_walkscore_api', ''));
    if (!empty($walkscore_api)) {
        ob_start();
        wpestate_walkscore_details($propid);
        return ob_get_clean();
    }
    return '';
}

function get_floor_plans_content($propid) {
    $plan_title_array = get_post_meta($propid, 'plan_title', true);
    if (is_array($plan_title_array)) {
        ob_start();
        estate_floor_plan($propid);
        return ob_get_clean();
    }
    return '';
}

function get_page_views_content($propid) {
    return '<canvas id="myChart" style="min-height:400px;width:100%;"></canvas>';
}

function get_yelp_content($propid) {
    $yelp_client_id = wpresidence_get_option('wp_estate_yelp_client_id', '');
    $yelp_client_api_key_2018 = wpresidence_get_option('wp_estate_yelp_client_api_key_2018', '');
    if (!empty($yelp_client_id) && !empty($yelp_client_api_key_2018)) {
        ob_start();
        wpestate_yelp_details($propid);
        return ob_get_clean();
    }
    return '';
}