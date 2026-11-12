<?php

include_once('property_list_shortcode.php');

include_once('property_list_shortcodes_v5.php');


include_once('slider_property_shortcode.php');
include_once('grids_shortcode.php');
include_once('filter_list_shortcode.php');
include_once('advanced_filter_list_shortcode.php');
include_once('places_shortcodes.php');
include_once('login_register_shortcodes.php');

include_once('elementor/property_page_title.php');
include_once('elementor/property_header_section_function.php');
include_once('elementor/property_page_overview_section.php');
include_once('elementor/property_page_description_section.php');

/*
 * Widget wpresidence grids function
 *
 *
 */

if (!function_exists('wpresidence_display_agents_grid')):

    function wpresidence_display_agents_grid($args) {
        $display_grids = wpresidence_display_grids_setup();

        $type = intval($args['type']);
        $place_type = intval($args['wpresidence_design_type']);
        $use_grid = $display_grids[$type];

        $item_height_style = '';
        $item_height = 300;

        $category_tax = $args['grid_taxonomy'];
        $agents = $args['grid_taxonomy'];

        if (!is_array($agents)) {
            return;
        }

        $agent_args = array(
            'post_type' => 'estate_agent',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'post__in' => $agents,
            'orderby' => $args['orderby'],
            'order' => $args['order'],
            'fields' => 'ids',
        );

        $agents_ids = array_values(get_posts($agent_args));
      
        $container = '<div class="row elementor_wpresidece_grid flex-sm-row flex-column ">';
        $agent_unit = wpestate_agent_card_selector(2);

      

        foreach ($use_grid['position'] as $key => $item_length) {
           
         
            if (isset($agents_ids[$key - 1])) {
           
                    ob_start();         
                        $agent_unit_col_class['col_class']=$item_length.' col-12 col-sm-6 ';
                        $agent_id = $agents_ids[$key - 1];
                        include( locate_template($agent_unit) ) ;      
                    $container .= ob_get_contents();
                    ob_end_clean();
               
            }

        }
        $container .= '</div>';

        return $container;
    }

endif;

/*
 * 
 * 
 * Get list agents for agent grid 
 * 
 * 
 * 
 * 
 * */

function get_list_agents_elementor() {
    $args = array(
        'post_type' => 'estate_agent',
        'paged' => 1,
        'posts_per_page' => 50);
    $return_array = array();
    $agent_selection = new WP_Query($args);
    while ($agent_selection->have_posts()): $agent_selection->the_post();
        $return_array[get_the_ID()] = get_the_title();
    endwhile;
    wp_reset_postdata();
    wp_reset_query();
    return $return_array;
}

/*
 * 
 * 
 *  Testimonial sloder function
 * 
 * 
 * 
 * 
 * */

function wpestate_testimonial_slider($settings) {
    wp_enqueue_script('owl_carousel');
    $return_string = '';

    if (isset($settings['list']) && is_array($settings['list'])) {
        $items_list = '';
        foreach ($settings['list'] as $key => $testimonial):
            $items_list .= '<div class="item">';
            $items_list .= '<div class="item_testimonial_content">
                    <div class="item_testimonial_title">' . wp_kses_post($testimonial['testimonial_title']) . '</div>
                    <div class="item_testimonial_text">' . wp_kses_post($testimonial['testimonial_text']) . '</div>
                    <div class="item_testimonial_stars">' . wpestate_starts_reviews_core(floatval($testimonial['testimonial_stars'])) . '</div>
                    <div class="item_testimonial_name">' . wp_kses_post($testimonial['testimonial_name']) . '</div>
                    <div class="item_testimonial_job">' . wp_kses_post($testimonial['testimonial_job']) . '</div>
            
                    </div>';
            $items_list .= '<div class="item_testimonal_image" style="background-image:url(' .esc_url( $testimonial['testimonial_image']['url']) . ');"></div>';

            $items_list .= '</div>';
        endforeach;

        $slider_id = 'wpestate_testimonial_slider_' . rand(1, 99999);

        $return_string .= '<div class="owl-carousel owl-theme wpestate_testimonial_slider" id="' . esc_attr($slider_id) . '" data-auto="0">' . $items_list . '</div>';

        $return_string .= '
         <script type="text/javascript">
             //<![CDATA[
             jQuery(document).ready(function(){
                wpestate_testimonial_slider("' . $slider_id . '");
             });
             //]]>
         </script>';
    }
    return $return_string;
}

/*
 *
 * return property id for elementor builder
 *
 * function not used anymore 
 * 
 *
 */

function wpestate_return_property_id_elementor_builder_legacy($attributes) {

    // wpresidence temaplte system
    $wp_estate_global_page_template = intval(wpresidence_get_option('wp_estate_global_property_page_template'));
    if ($wp_estate_global_page_template != 0) {
        if (isset($attributes['is_elementor_edit']) && $attributes['is_elementor_edit'] == 1) {
            $property_id = wpestate_return_elementor_id();
            return $property_id;
        }
        global $propid;
        return $propid;
    } else {
        // wpresidence temaplte system per item
        global $propid;
        $wp_estate_local_page_template = intval(get_post_meta($propid, 'property_page_desing_local', true));
        if ($wp_estate_local_page_template != 0) {
            return $propid;
        }
    }







    // elementor template system
    $property_id = get_the_ID();
    if (isset($attributes['is_elementor_edit']) && $attributes['is_elementor_edit'] == 1) {
        $property_id = wpestate_return_elementor_id();
    }
    return $property_id;
}


/**
 * Determines the correct property ID for Elementor widget rendering across different template contexts.
 *
 * This function handles property ID determination for WpResidence Elementor widgets in various scenarios:
 * 1. WpResidence global template system (legacy)
 * 2. WpResidence per-item template system (legacy)
 * 3. WpResidence Studio Elementor template system (current)
 * 
 * The function ensures that widgets always display meaningful property data, whether in:
 * - Elementor edit mode (shows latest property for preview)
 * - Actual property pages (shows current property)
 * - Studio template preview (shows latest property instead of template metadata)
 * - Legacy template systems (maintains backwards compatibility)
 *
 * @since 5.0.9
 * @param array $attributes Widget attributes containing context information
 *                         - 'is_elementor': Set to 1 when called from Elementor widget
 *                         - 'is_elementor_edit': Set to 1 when in Elementor edit mode
 * @return int Property post ID to be used by the widget for data retrieval
 */
function wpestate_return_property_id_elementor_builder($attributes) {
    
    // =========================================================================
    // LEGACY WPRESIDENCE GLOBAL TEMPLATE SYSTEM
    // =========================================================================
    // Check if global property page template is enabled in theme options
    // This is the original WpResidence template system that uses a single global template
    $wp_estate_global_page_template = intval(wpresidence_get_option('wp_estate_global_property_page_template'));
    
    if ($wp_estate_global_page_template != 0) {
        // Global template is enabled - handle edit mode vs live mode
        
        if (isset($attributes['is_elementor_edit']) && $attributes['is_elementor_edit'] == 1) {
            // EDIT MODE: Return latest property for preview purposes
            $property_id = wpestate_return_elementor_id();
            return $property_id;
        }
        
        // LIVE MODE: Use the global property ID variable set by the theme
        global $propid;
        return $propid;
        
    } else {
        
        // =====================================================================
        // LEGACY WPRESIDENCE PER-ITEM TEMPLATE SYSTEM
        // =====================================================================
        // Check if individual property has its own custom template assigned
        global $propid;
        $wp_estate_local_page_template = intval(get_post_meta($propid, 'property_page_desing_local', true));
        
        if ($wp_estate_local_page_template != 0) {
            // Individual property template is enabled - use the global property ID
            return $propid;
        }
    }
    
    // =========================================================================
    // WPRESIDENCE STUDIO ELEMENTOR TEMPLATE SYSTEM
    // =========================================================================
    // This is the new system using wpestate-studio post type templates
    // Handle property ID determination for Elementor-based templates
    
    // Get the current post information
    $property_id = get_the_ID();
    $current_post_type = get_post_type($property_id);
    
    // Determine if we're in Elementor edit mode
    $is_edit_mode = isset($attributes['is_elementor_edit']) && $attributes['is_elementor_edit'] == 1;
    
    if ($is_edit_mode) {
        // =====================================================================
        // ELEMENTOR EDIT MODE
        // =====================================================================
        // When editing templates in Elementor, always show latest property data
        // This provides meaningful preview content for template designers
        $property_id = wpestate_return_elementor_id();
        
    } else {
        // =====================================================================
        // LIVE/PREVIEW MODE
        // =====================================================================
        
        if ($current_post_type === 'wpestate-studio') {
            // We're viewing a Studio template directly (not in edit mode)
            // This happens when users preview templates or when templates are displayed
            
            // Get the template type to determine appropriate content
            $template_type = get_post_meta($property_id, 'wpestate_head_foot_template', true);
            
            if ($template_type === 'wpestate_single_property_page') {
                // This is a single property page template
                // Instead of showing template metadata, show actual property data
                
                // Query for the most recent published property
                $latest_property = get_posts([
                    'post_type'      => 'estate_property',     // WpResidence property post type
                    'post_status'    => 'publish',             // Only published properties
                    'numberposts'    => 1,                     // Just get one
                    'orderby'        => 'ID',                  // Order by ID for consistency
                    'order'          => 'DESC',                // Get the latest
                    'fields'         => 'ids'                  // Only return IDs for performance
                ]);
                
                if (!empty($latest_property)) {
                    // Use the latest property ID for meaningful preview content
                    $property_id = $latest_property[0];
                }
                // If no properties exist, fall back to using the template ID
                // (widgets should handle this gracefully)
            }
            
            // For other template types (agent, agency, etc.), keep using template ID
            // Those widgets should implement their own latest content logic if needed
            
        }
        
        // For all other cases:
        // - If current post is already an 'estate_property', keep using its ID
        // - If current post is any other type, keep using its ID (existing behavior)
        // This maintains backwards compatibility and normal property page functionality
    }
    
    return $property_id;
}


/*
 *
 * return bootstrap column number
 *
 *
 *
 *
 */

if (!function_exists('wpestate_shortocode_return_column')):

    function wpestate_shortocode_return_column($row_number) {
        if ($row_number > 4) {
            $row_number = 4;
        }
        if ($row_number == 4) {
            $row_number_col = 3; // col value is 3
        } else if ($row_number == 3) {
            $row_number_col = 4; // col value is 4
        } else if ($row_number == 2) {
            $row_number_col = 6; // col value is 6
        } else if ($row_number == 1) {
            $row_number_col = 12; // col value is 12
        }
        return $row_number_col;
    }

endif;

/*
 *
 * create arguments array for proeprties list and shortlist slider
 *
 *
 *
 *
 */
if (!function_exists('wpestate_recent_posts_shortocodes_create_arg')):

    function wpestate_recent_posts_shortocodes_create_arg($shortcode_arguments) {

        if ($shortcode_arguments['type'] == 'properties' || $shortcode_arguments['type'] == 'estate_property') {
            /*
             * if we have properties
             */
            $type = 'estate_property';

            // Normalize taxonomy filters to arrays of term ids.
            // AJAX now sends arrays, but we still support CSV strings from other callers.
            $tax_arguments = array(
                'by_field' => 'term_id',
                'property_action_category' => isset($shortcode_arguments['action']) ? (is_array($shortcode_arguments['action']) ? $shortcode_arguments['action'] : explode(',', $shortcode_arguments['action'])) : '',
                'property_category' => isset($shortcode_arguments['category']) ? (is_array($shortcode_arguments['category']) ? $shortcode_arguments['category'] : explode(',', $shortcode_arguments['category'])) : '',
                'property_area' => isset($shortcode_arguments['area']) ? (is_array($shortcode_arguments['area']) ? $shortcode_arguments['area'] : explode(',', $shortcode_arguments['area'])) : '',
                'property_city' => isset($shortcode_arguments['city']) ? (is_array($shortcode_arguments['city']) ? $shortcode_arguments['city'] : explode(',', $shortcode_arguments['city'])) : '',
                'property_county_state' => isset($shortcode_arguments['state']) ? (is_array($shortcode_arguments['state']) ? $shortcode_arguments['state'] : explode(',', $shortcode_arguments['state'])) : '',
                'property_status' => isset($shortcode_arguments['status']) ? (is_array($shortcode_arguments['status']) ? $shortcode_arguments['status'] : explode(',', $shortcode_arguments['status'])) : '',
                'property_features' => isset($shortcode_arguments['features']) ? (is_array($shortcode_arguments['features']) ? $shortcode_arguments['features'] : explode(',', $shortcode_arguments['features'])) : '',
            );

            $meta_data = array();
            $meta_query = array();


            if (isset($shortcode_arguments['show_featured_only']) && $shortcode_arguments['show_featured_only'] == 'yes') {
                $meta_data['prop_featured'] = array(
                    'key' => 'prop_featured',
                    'value' => 1,
                    'compare' => '=',
                );
                $meta_query = wpestate_create_query_meta_by_array($meta_data);
            }

            $agent_ids=array();
            if(isset($shortcode_arguments['agentid']) && $shortcode_arguments['agentid']!=''){
                $agent_ids = array_map('trim', explode(',', $shortcode_arguments['agentid']));
           
                $meta_query[]=array(
                                'key' => 'property_agent',
                                'value' => count($agent_ids) === 1 ? $agent_ids[0] : $agent_ids,
                                'compare' => count($agent_ids) === 1 ? '=' : 'IN'
                            );
                                    
            }




            $order = intval($shortcode_arguments['sort_by']);
            if (isset($shortcode_arguments['random_pick']) && $shortcode_arguments['random_pick'] == 'yes') {
                $order = 99;
            }
            $paged = (isset($shortcode_arguments['paged'])) ? $shortcode_arguments['paged'] : 1;

            $temp_arguments = array(
                'post_type' => 'estate_property',
                'post_status' => 'publish',
                'paged' => $paged,
                'order' => $order,
                'posts_per_page' => $shortcode_arguments['number'],
                'tax_arguments' => $tax_arguments,
                'meta_query' => $meta_query
            );
            
            // ugly fix
            if( isset($meta_query['meta_query_array']) && is_array($meta_query['meta_query_array'])  ){
                $temp_arguments[  'meta_query'] = reset($meta_query);
            }
            $arguments_array = wpestate_create_query_arguments($temp_arguments);
            $args = $arguments_array['query_arguments'];


        } else if ($shortcode_arguments['type'] == 'agents') {

            /*
             * if we have agents
             */
            $paged = (isset($shortcode_arguments['paged'])) ? $shortcode_arguments['paged'] : 1;
            $type = 'estate_agent';
            $args = array(
                'post_type' => $type,
                'post_status' => 'publish',
                'paged' => $paged,
                'posts_per_page' => $shortcode_arguments['number'],
            );
        } else {
            /*
             * if we have simple blog posts
             */
            $paged = (isset($shortcode_arguments['paged'])) ? $shortcode_arguments['paged'] : 1;
            $type = 'post';
            $args = array(
                'post_type' => $type,
                'post_status' => 'publish',
                'paged' => $paged,
                'posts_per_page' => $shortcode_arguments['number'],
                'cat' => $shortcode_arguments['category'],
            );
        }

        return $args;
    }

endif;

if (!function_exists("wpestate_return_filter_data")):

    function wpestate_return_filter_data($filter_selection) {

        $values_array = array(
            'types' => array(
                'label' => esc_html__('Types', 'wpresidence-core'),
                'meta' => 'Types',
            ),
            'category' => array(
                'label' => esc_html__('Categories', 'wpresidence-core'),
                'meta' => 'Categories',
            ),
            'county' => array(
                'label' => esc_html__('States', 'wpresidence-core'),
                'meta' => 'States',
            ),
            'city' => array(
                'label' => esc_html__('Cities', 'wpresidence-core'),
                'meta' => 'Cities',
            ),
            'area' => array(
                'label' => esc_html__('Areas', 'wpresidence-core'),
                'meta' => 'Areas',
            ),
            'status' => array(
                'label' => esc_html__('Statuses', 'wpresidence-core'),
                'meta' => 'Statuses',
            ),
            'features' => array(
                'label' => esc_html__('Features', 'wpresidence-core'),
                'meta' => 'Features',
            ),
        );

        $return_arrray = array();
        foreach ($filter_selection as $key => $value) {

            if ($value == 'all') {

                if (isset($values_array[$key]['label'])) {
                    $return_arrray[$key]['label'] = $values_array[$key]['label'];
                } else {
                    $return_arrray[$key]['label'] = '';
                }

                if (isset($values_array[$key]['meta'])) {
                    $return_arrray[$key]['meta'] = $values_array[$key]['meta'];
                } else {
                    $values_array[$key]['meta'] = '';
                }
            } else {
                $return_arrray[$key]['label'] = ucwords(str_replace('-', ' ', $value));
                $return_arrray[$key]['meta'] = sanitize_title($value);
            }
        }
        return $return_arrray;
    }

endif;

///////////////////////////////////////////////////////////////////////////////////////////
// advanced search function
///////////////////////////////////////////////////////////////////////////////////////////
if (!function_exists("wpestate_advanced_search_function")):

    function wpestate_advanced_search_function($attributes, $content = null) {
        $return_string = '';
        $random_id = '';
    
        $actions_select = '';
        $categ_select = '';
        $title = '';
        $search_col = 3;
        $search_col_but = 3;
        $search_col_price = 6;
        if (isset($attributes['title'])) {
            $title = $attributes['title'];
        }

        $args = wpestate_get_select_arguments();
        $action_select_list = wpestate_get_action_select_list($args);
        $categ_select_list = wpestate_get_category_select_list($args);
        $select_city_list = wpestate_get_city_select_list($args);
        $select_area_list = wpestate_get_area_select_list($args);
        $select_county_state_list = wpestate_get_county_state_select_list($args);

        $adv_submit = wpestate_get_template_link('page-templates/advanced_search_results.php');

        
        $return_string .= '<h2 class="shortcode_title_adv">' .esc_html( $title) . '</h2>';
        $return_string .= '<div class="advanced_search_shortcode" id="advanced_search_shortcode">
        <form role="search" method="get" class="row  gx-2 gy-2"   action="' . $adv_submit . '" >';

        if (function_exists('icl_translate')) {
            $return_string .= do_action('wpml_add_language_form_field');
        }
        $adv_search_type = wpresidence_get_option('wp_estate_adv_search_type', '');
        if ($adv_search_type == 6) {
            $return_string = '<div class="advanced_search_shortcode" id="advanced_search_shortcode">' . wpestate_show_advanced_search_tabs($adv_submit, 'shortcode') . '</div>';
            return $return_string;
        }





            $adv_search_what = wpresidence_get_option('wp_estate_adv_search_what', '');
            $adv_search_label = wpresidence_get_option('wp_estate_adv_search_label', '');
            $adv_search_how = wpresidence_get_option('wp_estate_adv_search_how', '');
            $count = 0;
            ob_start();
            $search_field = '';
            $adv_search_fields_no_per_row = ( floatval(wpresidence_get_option('wp_estate_search_fields_no_per_row')) );

            if ($adv_search_type == 10) {
                $adv_actions_value = esc_html__('Types', 'wpresidence-core');
                $adv_actions_value1 = 'all';

                print '
                    <div class="col-md-8">
                        <input type="text" id="adv_locationsh" class="form-control" name="adv_location"  placeholder="'. esc_html__('Type address, state, city or area','wpresidence-core').'" value="">
                    </div>';
  
                    $availableTags  =  wpestate_return_data_for_location_autocomplete();

                    print '<script type="text/javascript">    //<![CDATA[
                    jQuery(document).ready(function(){
                        var availableTagsData = ' . $availableTags . ';
                        wpresidenceInitializeAutocomplete(availableTagsData,"adv_locationsh");
                    });
                    //]]>
                    </script>';
                    print'
                    <div class="col-md-4">
                         '. wpestate_show_dropdown_taxonomy_v21('types',$adv_actions_value , '').'
                    </div>';


               
                print '<input type="hidden" name="is10" value="10">';
            }



            if ($adv_search_type == 11) {
                $adv_actions_value = esc_html__('Types', 'wpresidence-core');
                $adv_actions_value1 = 'all';
                $adv_categ_value = esc_html__('Categories', 'wpresidence-core');
                $adv_categ_value1 = 'all';

                print'
                    <div class="col-md-6">
                    <input type="text" id="keyword_search" class="form-control" name="keyword_search"  placeholder="' . esc_html__('Type Keyword', 'wpresidence-core') . '" value="">
                    </div>';

                print '<div class="col-md-3">'
                    .wpestate_show_dropdown_taxonomy_v21('categories',$adv_categ_value , '').
                '</div>';
            

         
                print'<div class="col-md-3">'
                    .wpestate_show_dropdown_taxonomy_v21('types',$adv_actions_value , '').
                '</div>';


                print ' <input type="hidden" name="is11" value="11">';
            }



            foreach ($adv_search_what as $key => $search_field) {

                $search_col = 3;
                $search_col_but = 3;
                $search_col_price = 6;
                if ($adv_search_fields_no_per_row == 2) {
                    $search_col = 6;
                    $search_col_but = 6;
                    $search_col_price = 12;
                } else if ($adv_search_fields_no_per_row == 3) {
                    $search_col = 4;
                    $search_col_but = 4;
                    $search_col_price = 8;
                }
                if ($search_field == 'property price' && wpresidence_get_option('wp_estate_show_slider_price', '') == 'yes') {
                    $search_col = $search_col_price;
                }

                print '<div class="col-md-' . $search_col . ' ' . str_replace(" ", "_", $search_field) . '">';
                wpestate_show_search_field($adv_search_label[$key], 'shortcode', $search_field, $action_select_list, $categ_select_list, $select_city_list, $select_area_list, $key, $select_county_state_list);
                print '</div>';
            } // end foreach
            $templates = ob_get_contents();
            ob_end_clean();
            $return_string .= $templates;
        
        $extended_search = wpresidence_get_option('wp_estate_show_adv_search_extended', '');
        if ($extended_search == 'yes') {
            ob_start();
            show_extended_search('short');
            $templates = ob_get_contents();
            ob_end_clean();
            $return_string = $return_string . $templates;
        }
        $search_field = "submit";
        $return_string .= '<div class="col-md-' . $search_col_but . ' ' . str_replace(" ", "_", $search_field) . '">
            <button class="wpresidence_button" id="advanced_submit_shorcode">' . esc_html__('Search', 'wpresidence-core') . '</button>
        ' . wp_nonce_field('wpestate_regular_search', 'wpestate_regular_search_nonce', true, false) . '
        </div>

    </form>
</div>';

        return $return_string;
    }

endif;

if (!function_exists('wpestate_full_map_shortcode')):

    function wpestate_full_map_shortcode($attributes, $content = null) {

        $attributes = shortcode_atts(
                array(
                    'map_shortcode_for' => 'no',
                    'map_shorcode_show_contact_form' => 'yes',
                    'map_height' => 600,
                    'map_snazy' => '',
                    'map_zoom' => 20,
                    'category_ids' => '',
                    'action_ids' => '',
                    'city_ids' => '',
                    'area_ids' => '',
                    'state_ids' => '',
                    'status_ids' => '',
                    'is_elementor' => 0,
                ), $attributes);

        if (isset($attributes['map_shortcode_for'])) {
            $map_shortcode_for = $attributes['map_shortcode_for'];
        }

        if (isset($attributes['map_shorcode_show_contact_form'])) {
            $map_shorcode_show_contact_form = $attributes['map_shorcode_show_contact_form'];
        }

        if (isset($attributes['map_height'])) {
            $map_height = $attributes['map_height'];
        }


        $map_style = '';
        if (isset($attributes['map_snazy'])) {
            $map_style = $attributes['map_snazy'];
        }

        if (isset($attributes['map_zoom'])) {
            $map_zoom = intval($attributes['map_zoom']);
        }
        if (isset($attributes['category_ids'])) {
            $category = $attributes['category_ids'];
        }

        if (isset($attributes['action_ids'])) {
            $action = $attributes['action_ids'];
        }

        if (isset($attributes['city_ids'])) {
            $city = $attributes['city_ids'];
        }

        if (isset($attributes['area_ids'])) {
            $area = $attributes['area_ids'];
        }

        if (isset($attributes['state_ids'])) {
            $state = $attributes['state_ids'];
        }

        if (isset($attributes['status_ids'])) {
            $status = $attributes['status_ids'];
        }

        $category_array = '';
        $action_array = '';
        $city_array = '';
        $area_array = '';
        $state_array = '';
        $status_array = '';

        // build category array
        if ($category != '') {
            $category_of_tax = array();
            $category_of_tax = explode(',', $category);
            $category_array = array(
                'taxonomy' => 'property_category',
                'field' => 'term_id',
                'terms' => $category_of_tax
            );
        }


        // build action array
        if ($action != '') {
            $action_of_tax = array();
            $action_of_tax = explode(',', $action);
            $action_array = array(
                'taxonomy' => 'property_action_category',
                'field' => 'term_id',
                'terms' => $action_of_tax
            );
        }

        // build city array
        if ($city != '') {
            $city_of_tax = array();
            $city_of_tax = explode(',', $city);
            $city_array = array(
                'taxonomy' => 'property_city',
                'field' => 'term_id',
                'terms' => $city_of_tax
            );
        }

        // build city array
        if ($area != '') {
            $area_of_tax = array();
            $area_of_tax = explode(',', $area);
            $area_array = array(
                'taxonomy' => 'property_area',
                'field' => 'term_id',
                'terms' => $area_of_tax
            );
        }

        if ($state != '') {
            $state_of_tax = array();
            $state_of_tax = explode(',', $state);
            $state_array = array(
                'taxonomy' => 'property_county_state',
                'field' => 'term_id',
                'terms' => $state_of_tax
            );
        }
        if ($status != '') {
            $state_of_tax = array();
            $state_of_tax = explode(',', $status);
            $status_array = array(
                'taxonomy' => 'property_status',
                'field' => 'term_id',
                'terms' => $state_of_tax
            );
        }



        $args = array(
            'post_type' => 'estate_property',
            'post_status' => 'publish',
            'paged' => 1,
            'fields' => 'ids',
            'posts_per_page' => intval(wpresidence_get_option('wp_estate_map_max_pins', '')),
            'tax_query' => array(
                $category_array,
                $action_array,
                $city_array,
                $area_array,
                $state_array,
                $status_array
            ),
        );

        $selected_pins = wpestate_listing_pins('full_shortcode', 1, $args, 1, '', ''); //call the new pins




        $is_contact = 'yes';
        $map_style_encoded = '';
        if (isset($attributes['is_elementor']) && $attributes['is_elementor'] == 1) {
            $map_style_encoded = $map_style;
        } else {
            $map_style_encoded = rawurldecode(base64_decode($map_style));
        }


        ob_start();

        include( locate_template('templates/google_maps_base.php') );
        $return_string = ob_get_contents();
        $return_string .= '<div id="wpestate_full_map_control_data"  data-zoom="' . esc_attr($map_zoom ). '"></div>';
        ob_end_clean();

        if (!wp_script_is('googlemap', 'enqueued')) {
            $is_map_shortcode = 1;
            wpestate_load_google_map();
        }




        if ($map_shortcode_for == 'contact') {
            $return_string .= '<script type="text/javascript">
                    //<![CDATA[
                    var is_map_shortcode=1;
                    var map_style_shortcode="";';
            if ($map_style_encoded != '') {
                $return_string .= ' map_style_shortcode=' . $map_style_encoded . ';';
            }

            $return_string .= 'jQuery(document).ready(function(){

                        if (typeof google === "object" && typeof google.maps === "object") {
                            google.maps.event.addDomListener(window, "load", wpresidence_initialize_map_contact);
                        }else{
                            wpresidence_initialize_map_contact_leaflet();
                             setTimeout(function(){map.invalidateSize();  },1000)
                        }
                    });
                    //]]>
                </script>';
        } else {
            $return_string .= '<script type="text/javascript">
                //<![CDATA[
                var is_map_shortcode=1;
                var map_style_shortcode="";';
            if ($map_style_encoded != '') {
                $return_string .= ' map_style_shortcode=' . $map_style_encoded . ';';
            }

            $return_string .= 'jQuery(document).ready(function(){
                    googlecode_regular_vars.generated_pins="0";
                    googlecode_regular_vars.markers=' . json_encode($selected_pins) . '
                    if (typeof google === "object" && typeof google.maps === "object") {
                        google.maps.event.addDomListener(window, "load", wpresidence_initialize_map);
                    }else{
             
                        wpresidence_initialize_map();
                        setTimeout(function(){map.invalidateSize();  },1000)
                    }
                });
                //]]>
            </script>';
        }


        return $return_string;
    }

endif;

if (!function_exists('wpestate_taxonomy_list')):

    function wpestate_taxonomy_list($attributes, $content = null) {
        $return_string = '<ul class="wpestate_term_list">';
        $taxonomy_list_type_array = array(
            'category' => 'property_category',
            'action category' => 'property_action_category',
            'city' => 'property_city',
            'area' => 'property_area',
            'county/state' => 'property_county_state',
            'status' => 'property_status',
            'features and ammenities' => 'property_features'
        );

        $attributes = shortcode_atts(
                array(
                    'taxonomy_list_type' => 'category',
                    'taxonomy_list_type_show' => 'yes'
                ), $attributes);

        if (isset($attributes['taxonomy_list_type'])) {
            $taxonomy_list_type = $attributes['taxonomy_list_type'];
        }
        if (isset($attributes['taxonomy_list_type_show'])) {
            $taxonomy_list_type_show = $attributes['taxonomy_list_type_show'];
        }

       

        $terms =  wpestate_get_cached_terms( $taxonomy_list_type_array[$taxonomy_list_type], array(
            'hide_empty' => false,
        ) );
        
        foreach ($terms as $item) {
            $return_string .= '<li><a href="' . get_term_link($item->term_id) . '">' . $item->name . '</a>';
            if ($taxonomy_list_type_show == 'yes') {
                $return_string .= '<span>' . $item->count . '</span>';
            }
            $return_string .= '</li>';
        }

        $return_string .= '</ul>';
        return $return_string;
    }

endif;



/**
 * WPEstate Design Property Slider 2
 *
 * This file contains the function to create a second design variation of the property slider for the WPEstate theme.
 *
 * @package WPEstate
 * @subpackage Widgets
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core
 * - WPEstate theme functions (wpresidence_get_option, wpestate_return_agent_details, wpestate_return_property_status, wpestate_show_price, wpestate_strip_excerpt_by_char)
 * - Owl Carousel script
 *
 * Usage:
 * This function is typically called within the WPEstate theme to display a property slider.
 * Example: $slider_html = wpestate_design_property_slider_2($property_ids);
 */


function wpestate_design_property_slider_2($ids_array) {
    // Enqueue required scripts
    wp_enqueue_script('owl_carousel');

    // Initialize variables
    $counter = 0;
    $slides = '';
    $indicators = '';

    // Retrieve currency settings
    $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
    $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));

    // Loop through each property ID
    foreach ($ids_array as $theid) {
        // Get property details
        $preview = wp_get_attachment_image_src(get_post_thumbnail_id($theid), 'listing_full_slider');
        if (empty($preview[0])) {
            $preview[0] = get_theme_file_uri('/img/defaults/default_property_featured.jpg');
        }

        $property_size = wpestate_get_converted_measure($theid, 'property_size');
        $property_bedrooms = get_post_meta($theid, 'property_bedrooms', true);
        $property_bathrooms = get_post_meta($theid, 'property_bathrooms', true);
        $realtor_details = wpestate_return_agent_details($theid);
        $agent_id = $realtor_details['agent_id'];
        $agent_face = $realtor_details['agent_face_img'];
        $featured = intval(get_post_meta($theid, 'prop_featured', true));
        
        $active = ($counter === 0) ? 'active' : '';

        // Include the slide template
        ob_start();
        include(locate_template('templates/property_shortcode_slider_templates/property_slider_shortcode1_v2.php'));
        $slides .= ob_get_clean();

        // Generate indicators
        $indicators .= sprintf(
            '<a data-target="#estate-property_slider2" href="#item%1$s" class="button secondary url %2$s"></a>',
            esc_attr($counter),
            esc_attr($active)
        );

        $counter++;
    }

   // Construct the final HTML
   $return_string = '<div class="property_slider2_wrapper owl-carousel owl-theme" id="estate-property_slider2">';
   $return_string .= $slides;
   $return_string .= '</div>';
   $return_string .= '<ol class="theme_slider_3_carousel-indicators">' . $indicators . '</ol>';
   $return_string .= '<script type="text/javascript">
       jQuery(document).ready(function(){
          wpestate_property_slider_2();
       });
   </script>';

   return $return_string;
    
}














/**
 * WPEstate Properties Slider
 *
 * This file contains the function to create a properties slider for the WPEstate theme.
 *
 * @package WPEstate
 * @subpackage Widgets
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core
 * - WPEstate theme functions
 * - WPEstate theme options
 *
 * Usage:
 * This function is typically called via a shortcode in the WPEstate theme.
 * Example: [wpestate_slider_properties propertyid="1,2,3" design_type="1"]
 */

if (!function_exists('wpestate_slider_properties')):
    function wpestate_slider_properties($attributes, $content = null) {
        $return_string = '';
        $ids = '';
        $ids_array = array();
        $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
        $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
        $attributes = shortcode_atts(
                array(
                    'propertyid' => '',
                    'design_type' => 1,
                ), $attributes);

        if (isset($attributes['propertyid'])) {
            $ids = $attributes['propertyid'];
            $ids_array = explode(',', $ids);
        }

        if (isset($attributes['design_type'])) {
            $design_type = $attributes['design_type'];
        }

        if ($design_type == 2) {
            return wpestate_design_property_slider_2($ids_array);
        }

        $return_string .= '<div class="sections"><div class="facts">
                        <div class="facts__toggle">
                                <span class="facts__toggle-inner facts__toggle-inner--more">
                                    <span class="facts__toggle-text">' . esc_html__('see more facts', 'wpresidence-core') . '</span>
                                </span>
                                <span class="facts__toggle-inner facts__toggle-inner--less">
                                    <span class="facts__toggle-text">' . esc_html__('see less facts', 'wpresidence-core') . '</span>
                                </span>
                        </div>

                    </div>';

        $return_string .= '<!-- index -->
    <div class="sections__index">
            <span class="sections__index-current">
                 <span class="sections__index-inner">01</span>
            </span>
            <span class="sections__index-total">0' . count($ids_array) . '</span>
    </div>';

        $return_string .= ' <nav class="sections__nav">
            <button class="sections__nav-item sections__nav-item--prev">

            </button>
            <button class="sections__nav-item sections__nav-item--next">

            </button>
    </nav>';
        $initial_section = ' section--current ';

        wpestate_enqueue_slider_scripts();

        ob_start();
        foreach ($ids_array as $prop_id) {
            include(locate_template('templates/property_shortcode_slider_templates/property_slider_shortcode.php'));
            
            $initial_section = '';
        }
        $templates = ob_get_contents();
        ob_end_clean();

        $return_string .= $templates;
        $return_string .= '</div>';

        return $return_string;
    }
    

endif;


/**
 * Enqueue necessary scripts and styles for the slider
 */
function wpestate_enqueue_slider_scripts() {
    wp_enqueue_style('wpestate_sh5', get_theme_file_uri('/public/css/wpestate_sh5.css'), array(), '1.0', 'all');
    wp_enqueue_script('imagesloaded.pkgd.min', get_template_directory_uri() . '/js/imagesloaded.pkgd.min.js', array('jquery'), '1.0', false);
    wp_enqueue_script('charming.min', get_template_directory_uri() . '/js/charming.min.js', array('jquery'), '1.0', false);
    wp_enqueue_script('anime.min', get_template_directory_uri() . '/js/anime.min.js', array('jquery'), '1.0', false);
    wp_enqueue_script('wpestate_featured5', get_template_directory_uri() . '/js/featured5.js', array('jquery', 'imagesloaded.pkgd.min', 'anime.min', 'charming.min'), '1.0', false);
}



/**
 * WPEstate Slider Properties V2
 *
 * This file contains the function to create a property slider for the WPEstate theme.
 *
 * @package WPEstate
 * @subpackage Widgets
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core
 * - WPEstate theme functions (wpresidence_get_option, wpestate_return_favorite_listings_per_user, 
 *   wpestate_prepare_arguments_shortcode, wpestate_recent_posts_shortocodes_create_arg)
 * - Owl Carousel script
 *
 * Usage:
 * This function is typically called within the WPEstate theme to display a property slider.
 * Example: $slider_html = wpestate_slider_properties_v2($attributes, $slider_id);
 */

if (!function_exists('wpestate_slider_properties_v2')):
    function wpestate_slider_properties_v2($attributes, $slider_id, $content = null) {
        // Enqueue required scripts
        wp_enqueue_script('owl_carousel');

        // Initialize variables
        $return_string = '';
        $templates = '';

        // Retrieve theme options
        $wpestate_custom_unit_structure = wpresidence_get_option('wpestate_property_unit_structure');
        $wpestate_uset_unit = intval(wpresidence_get_option('wpestate_uset_unit', ''));
        $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
        $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));

        // Get user favorites
        $curent_fav = wpestate_return_favorite_listings_per_user();

        // Determine slider orientation
        $is_rtl = (empty($attributes['slider_orientation'])) ? 'yes' : 'no';

        // Process shortcode attributes and prepare query arguments
        if (isset($attributes['propertyid']) && $attributes['propertyid'] != '') {
            $attributes = shortcode_atts(['propertyid' => ''], $attributes);
            $ids = $attributes['propertyid'];
            $ids_array = explode(',', $ids);
            $args = [
                'post_type' => 'estate_property',
                'paged' => 1,
                'posts_per_page' => count($ids_array),
                'post_status' => 'publish',
                'post__in' => $ids_array,
                'orderby' => 'post__in'
            ];
        } else {
            $attributes = shortcode_atts([
                'title' => '',
                'type' => 'properties',
                'arrows' => 'top',
                'category_ids' => '',
                'action_ids' => '',
                'city_ids' => '',
                'area_ids' => '',
                'state_ids' => '',
                'status_ids' => '',
                'number' => 7,
                'show_featured_only' => 'no',
                'autoscroll' => 0,
                'sort_by' => 0,
            ], $attributes);
            $shortcode_arguments = wpestate_prepare_arguments_shortcode($attributes);
            $args = wpestate_recent_posts_shortocodes_create_arg($shortcode_arguments);
        }

        // Query properties
        $recent_posts = new WP_Query($args);

        // Generate slider HTML
        ob_start();
        $counter = 0;
        while ($recent_posts->have_posts()): $recent_posts->the_post();
            $prop_id = get_the_ID();
            $active = ($counter++ == 0) ? "active" : "";
            include(locate_template('templates/property_shortcode_slider_templates/property_slider_shortcode_v2.php'));
        endwhile;
        $templates = ob_get_clean();

        // Construct final HTML
        $return_string = sprintf(
            '<div class="owl-carousel owl-theme property_slider_carousel_elementor_v2 slider_orientation_%1$s" id="%2$s" data-auto="0">%3$s</div>',
            esc_attr($is_rtl),
            esc_attr($slider_id),
            $templates
        );

        wp_reset_query();

        return [
            'return' => $return_string,
            'items' => $recent_posts->post_count
        ];
    }
endif;

/**
 * WPEstate Slider Properties V3
 *
 * This file contains the function to create a third variation of the property slider for the WPEstate theme.
 *
 * @package WPEstate
 * @subpackage Widgets
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core
 * - WPEstate theme functions (wpresidence_get_option, wpestate_return_favorite_listings_per_user, 
 *   wpestate_prepare_arguments_shortcode, wpestate_recent_posts_shortocodes_create_arg)
 * - Owl Carousel script
 *
 * Usage:
 * This function is typically called within the WPEstate theme to display a property slider.
 * Example: $slider_html = wpestate_slider_properties_v3($attributes, $slider_id);
 */

 if (!function_exists('wpestate_slider_properties_v3')):
    function wpestate_slider_properties_v3($attributes, $slider_id, $content = null) {
        // Enqueue required scripts
        wp_enqueue_script('owl_carousel');

        // Initialize variables
        $return_string = '';
        $templates = '';

        // Retrieve theme options
        $wpestate_custom_unit_structure = wpresidence_get_option('wpestate_property_unit_structure');
        $wpestate_uset_unit = intval(wpresidence_get_option('wpestate_uset_unit', ''));
        $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
        $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));

        // Get user favorites
        $curent_fav = wpestate_return_favorite_listings_per_user();

        // Set RTL direction
        $is_rtl = "no";

        // Process shortcode attributes and prepare query arguments
        if (isset($attributes['propertyid']) && $attributes['propertyid'] != '') {
            $attributes = shortcode_atts(['propertyid' => ''], $attributes);
            $ids = $attributes['propertyid'];
            $ids_array = explode(',', $ids);
            $args = [
                'post_type' => 'estate_property',
                'paged' => 1,
                'posts_per_page' => count($ids_array),
                'post_status' => 'publish',
                'post__in' => $ids_array,
                'orderby' => 'post__in'
            ];
        } else {
            $attributes = shortcode_atts([
                'title' => '',
                'type' => 'properties',
                'arrows' => 'top',
                'category_ids' => '',
                'action_ids' => '',
                'city_ids' => '',
                'area_ids' => '',
                'state_ids' => '',
                'status_ids' => '',
                'number' => 7,
                'show_featured_only' => 'no',
                'autoscroll' => 0,
                'sort_by' => 0,
            ], $attributes);
            $shortcode_arguments = wpestate_prepare_arguments_shortcode($attributes);
            $args = wpestate_recent_posts_shortocodes_create_arg($shortcode_arguments);
        }

        // Query properties
        $recent_posts = new WP_Query($args);

        // Generate slider HTML
        ob_start();
        $counter = 0;
        while ($recent_posts->have_posts()): $recent_posts->the_post();
            $prop_id = get_the_ID();
            $active = ($counter++ == 0) ? "active" : "";
            include(locate_template('templates/property_shortcode_slider_templates/property_slider_shortcode_v3.php'));
        endwhile;
        $templates = ob_get_clean();

        // Construct final HTML
        $return_string = '<div class="owl-carousel owl-theme property_slider_carousel_elementor_v3 slider_orientation_' . esc_attr($is_rtl) . '" ';
        $return_string .= 'id="' . esc_attr($slider_id) . '" ';
        $return_string .= 'data-rtl="' . esc_attr($is_rtl) . '" ';
        $return_string .= 'data-auto="0">';
        $return_string .= $templates;
        $return_string .= '</div>';

        wp_reset_query();

        return [
            'return' => $return_string,
            'items' => $recent_posts->post_count
        ];
    }
endif;

/*
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * */

function wpestate_insert_elementor($post_id) {
    if (!class_exists('Elementor\Plugin')) {
        return '';
    }


    $pluginElementor = \Elementor\Plugin::instance();
    $response = $pluginElementor->frontend->get_builder_content($post_id);

    return $response;
}


/**
 * WpResidence Theme - Contact Form Shortcode
 *
 * This file contains the function to generate a customizable contact form shortcode
 * for the WpResidence WordPress theme.
 *
 * @package WpResidence
 * @subpackage Shortcodes
 * @since 1.0.0
 *
 * Dependencies: 
 * - WpResidence core functions
 * - WordPress core functions (wp_create_nonce, esc_html__, esc_attr)
 *
 * Usage: [contact_us_form text_align="left" form_back_color="#ffffff" form_text_color="#000000" form_border_color="#cccccc" form_button_size="normal"]
 */

if (!function_exists('wpestate_contact_us_form')):
    /**
     * Generate a contact form shortcode
     *
     * @param array $attributes Shortcode attributes
     * @param string|null $content Shortcode content (not used in this function)
     * @return string HTML output of the contact form
     */
    function wpestate_contact_us_form($attributes, $content = null) {
        // Initialize variables
        $return_string = '';
        $custom_css = '';

        // Define default attribute values and merge with user-provided attributes
        $attributes = shortcode_atts(
            array(
                'text_align'        => 'left',
                'form_back_color'   => '',
                'form_text_color'   => '',
                'form_border_color' => '',
                'form_button_size'  => 'normal',
            ),
            $attributes
        );

        // Generate custom CSS based on attributes
        $custom_css = 'style="';
        if (!empty($attributes['form_text_color'])) {
            $custom_css .= "color:" . esc_attr($attributes['form_text_color']) . " !important;";
        }
        if (!empty($attributes['form_back_color'])) {
            $custom_css .= "background:" . esc_attr($attributes['form_back_color']) . " !important;";
        }
        if (!empty($attributes['form_border_color'])) {
            $custom_css .= "border:1px solid " . esc_attr($attributes['form_border_color']) . " !important;";
        }
        $custom_css .= '"';

        // Start building the form HTML
        $return_string .= sprintf(
            '<div class="wpestate_contact_form_parent shortcode_contact_form sh_form_align_%s">
                <div class="alert-message" id="footer_alert-agent-contact_sh"></div>',
            esc_attr($attributes['text_align'])
        );

       // Define form fields with translation-ready strings
        $form_fields = array(
            array('type' => 'text',   'name' => 'contact_name',    'placeholder' => __('Name', 'wpresidence-core'),           'required' => true),
            array('type' => 'email',  'name' => 'contact_email',   'placeholder' => __('Email', 'wpresidence-core'),          'required' => true),
            array('type' => 'email',  'name' => 'contact_phone',   'placeholder' => __('Phone', 'wpresidence-core'),          'required' => true),
            array('type' => 'textarea', 'name' => 'contact_content', 'placeholder' => __('Type your message...', 'wpresidence-core'), 'required' => true),
        );

        // Generate form fields
        foreach ($form_fields as $field) {
            $tag = $field['type'] === 'textarea' ? 'textarea' : 'input';
            $return_string .= '<' . $tag . ' ';
            $return_string .= $custom_css . ' ';
            $return_string .= 'placeholder="' . esc_attr($field['placeholder']) . '" ';
            $return_string .= $field['required'] ? 'required="required" ' : '';
            $return_string .= 'id="foot_' . esc_attr($field['name']) . '_sh" ';
            $return_string .= 'name="' . esc_attr($field['name']) . '" ';
            $return_string .= 'class="form-control';
            
            if ($field['type'] === 'textarea') {
                $return_string .= ' wpestate-form-control-sh" rows="4"';
            } elseif ($field['type'] === 'email') {
                $return_string .= ' wpestate-form-control-sh"';
            } else {
                $return_string .= '"';
            }
            
            if ($field['type'] !== 'textarea') {
                $return_string .= ' type="' . esc_attr($field['type']) . '"';
            }
            
            $return_string .= '>';
            
            if ($field['type'] === 'textarea') {
                $return_string .= '</textarea>';
            }
        }


        // Add hidden fields and GDPR check
        $return_string .= sprintf(
            '<input type="hidden" name="contact_ajax_nonce" id="agent_property_ajax_nonce" value="%s" />',
            wp_create_nonce('ajax-property-contact')
        );
        $return_string .= wpestate_check_gdpr_case();

        // Add submit button
        $return_string .= sprintf(
            '<div class="btn-cont">
                <button type="submit" id="btn-cont-submit_sh" class="wpresidence_button sh_but_%s">%s</button>
                <input type="hidden" value="" name="contact_to">
                <div class="bottom-arrow"></div>
            </div>
        </div>',
            esc_attr($attributes['form_button_size']),
            esc_html__('Send Message', 'wpresidence-core')
        );

        // Add custom styles and JavaScript
        $return_string .= sprintf(
            '<style>
                .shortcode_contact_form textarea::-webkit-input-placeholder,
                .shortcode_contact_form input::-webkit-input-placeholder {
                    color: %s !important;
                }
            </style>
            <script type="text/javascript">
                jQuery(document).ready(function(){
                    wpestate_contact_us_shortcode();
                });
            </script>',
            esc_attr($attributes['form_text_color'])
        );

        return $return_string;
    }
endif;










/*
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * */

if (!function_exists('wpestate_property_page_map_modal_function')):

    function wpestate_property_page_map_modal_function($the_id) {

        $use_mimify = wpresidence_get_option('wp_estate_use_mimify', '');
        $mimify_prefix = '';
        if ($use_mimify === 'yes') {
            $mimify_prefix = '.min';
        }

        if (!wp_script_is('googlemap', 'enqueued')) {
            wpestate_load_google_map();
        }


        $return_string = '';
        $istab = 0;

        $wpestate_currency = wpresidence_get_option('wp_estate_currency_symbol', '');
        $where_currency = wpresidence_get_option('wp_estate_where_currency_symbol', '');
        $title_orig = get_the_title($the_id);
        $title_orig = str_replace('%', '', $title_orig);
        $types = get_the_terms($the_id, 'property_category');
        if ($types && !is_wp_error($types)) {
            foreach ($types as $single_type) {
                $prop_type[] = $single_type->name; //$single_type->slug;
                $prop_type_name[] = $single_type->name;
                $slug = $single_type->slug;
                $parent_term = $single_type->parent;
            }

            $single_first_type = $prop_type[0];
            $single_first_type_pin = $prop_type[0];
            if ($parent_term != 0) {
                $single_first_type = $single_first_type . wpestate_add_parent_infobox($parent_term, 'property_category');
            }
            $single_first_type_name = $prop_type_name[0];
        } else {
            $single_first_type = '';
            $single_first_type_name = '';
            $single_first_type_pin = '';
        }


        $types_act = get_the_terms($the_id, 'property_action_category');
        if ($types_act && !is_wp_error($types_act)) {
            foreach ($types_act as $single_type) {
                $prop_action[] = $single_type->name; //$single_type->slug;
                $prop_action_name[] = $single_type->name;
                $slug = $single_type->slug;
                $parent_term = $single_type->parent;
            }
            $single_first_action = $prop_action[0];
            $single_first_action_pin = $prop_action[0];

            if ($parent_term != 0) {
                $single_first_action = $single_first_action . wpestate_add_parent_infobox($parent_term, 'property_action_category');
            }
            $single_first_action_name = $prop_action_name[0];
        } else {
            $single_first_action = '';
            $single_first_action_name = '';
            $single_first_action_pin = '';
        }


        if ($single_first_action == '' || $single_first_action == '') {
            $pin = sanitize_key(wpestate_limit54($single_first_type_pin . $single_first_action_pin));
        } else {
            $pin = sanitize_key(wpestate_limit27($single_first_type_pin)) . sanitize_key(wpestate_limit27($single_first_action_pin));
        }

        $hidingprice = wpestate_hide_show_price( $the_id );

        if ( $hidingprice ) {
            $hiddenPriceLabel = wpresidence_get_option( 'wp_estate_property_hide_price_text', '', 'Price on application' );
            // If price is hidden, return empty string or print nothing
            $price = $hiddenPriceLabel;
            $pin_price = $hiddenPriceLabel;
            $clean_price = $hiddenPriceLabel;
        } else {
        // If price is not hidden, format it as usual

        //// get price
        $price = floatval(get_post_meta($the_id, 'property_price', true));
        $price_label = esc_html(get_post_meta($the_id, 'property_label', true));
        $price_label_before = esc_html(get_post_meta($the_id, 'property_label_before', true));
        $clean_price = floatval(get_post_meta($the_id, 'property_price', true));
        if ($price == 0) {
            $price = $price_label_before . '' . $price_label;
            $pin_price = '';
        } else {
            $th_separator = stripslashes(wpresidence_get_option('wp_estate_prices_th_separator', ''));
            $pin_price = $price;

            $price = wpestate_format_number_price($price, $th_separator);

            if ($where_currency == 'before') {
                $price = $wpestate_currency . $price;
            } else {
                $price = $price . $wpestate_currency;
            }

            if (wpresidence_get_option('wp_estate_use_price_pins_full_price', '') == 'no') {

                $pin_price = wpestate_price_pin_converter($pin_price, $where_currency, $wpestate_currency);
            } else {
                $pin_price == "<span class='infocur infocur_first'>" . $price_label_before . "</span>" . $price . "<span class='infocur'>" . $price_label . "</span>";
            }

            $price = "<span class='infocur infocur_first'>" . $price_label_before . "</span>" . $price . "<span class='infocur'>" . $price_label . "</span>";
        }

        }

        $rooms = get_post_meta($the_id, 'property_bedrooms', true);
        $bathrooms = get_post_meta($the_id, 'property_bathrooms', true);
        $zoom = get_post_meta($the_id, 'page_custom_zoom', true);
        $size = wpestate_get_converted_measure($the_id, 'property_size');

        $gmap_lat = esc_html(get_post_meta($the_id, 'property_latitude', true));
        $gmap_long = esc_html(get_post_meta($the_id, 'property_longitude', true));
        $property_add_on = ' data-post_id="' . $the_id . '" data-cur_lat="' . $gmap_lat . '" data-cur_long="' . $gmap_long . '" data-prop-zoom="'.$zoom.'"';
        $property_add_on .= ' data-title="' . $title_orig . '"  data-pin="' . $pin . '" data-thumb="' . rawurlencode(get_the_post_thumbnail($the_id, 'agent_picture_thumb')) . '" ';
        $property_add_on .= ' data-price="' . rawurlencode($price) . '" ';
        $property_add_on .= ' data-single-first-type="' . rawurlencode($single_first_type) . '"  data-single-first-action="' . rawurlencode($single_first_action) . '" ';
        $property_add_on .= ' data-rooms="' . rawurlencode($rooms) . '" data-size="' . rawurlencode($size) . '" data-bathrooms="' . rawurlencode($bathrooms) . '" ';
        $property_add_on .= ' data-prop_url="' . rawurlencode(esc_url(get_permalink($the_id))) . '" ';
        $property_add_on .= ' data-pin_price="' . rawurlencode($pin_price) . '" ';
        $property_add_on .= ' data-clean_price="' . rawurlencode($clean_price) . '" ';

        wpestate_load_google_map();
        $unique_id=rand(1,9999);
        $return_string = '<div class="google_map_shortcode_wrapper  ' . wpresidence_return_class_leaflet() . '">
                <div id="gmapzoomplus_sh_'.intval($unique_id).'"  class="smallslidecontrol gmapzoomplus_sh shortcode_control" ><i class="fas fa-plus"></i> </div>
                <div id="gmapzoomminus_sh_'.intval($unique_id).'" class="smallslidecontrol gmapzoomminus_sh shortcode_control" ><i class="fas fa-minus"></i></div>';
        $return_string .= wpestate_show_poi_onmap('sh');
        $return_string .= '<div id="slider_enable_street_sh_'.intval($unique_id).'" class="slider_enable_street_sh" data-placement="bottom" data-original-title="' . esc_html__('Street View', 'wpresidence-core') . '"> <i class="fas fa-location-arrow"></i>    </div>';
        $return_string .= '<div class="googleMap_shortcode_class" id="googleMap_shortcode_'.intval($unique_id).'" ' . $property_add_on . ' ></div></div>';

        

      
        
        
        return $return_string;
    }

endif;

/*
 * 
 * 
 * shortcode - Listings per agent
 * 
 * 
 * 
 * 
 * */


if (!function_exists('wplistingsperagent_shortcode_function')):

    function wplistingsperagent_shortcode_function($attributes, $content = null) {
        
        $return_string = '';
        $attributes = shortcode_atts(
                array(
                    'agentid' => '',
                    'nooflisting' => '',
                    'type' => 'estate_property',
                    'display_grid'=>'no'
                ), $attributes);

        if (isset($attributes['agentid'])) {
            $agentid = $attributes['agentid'];
        }

        if (isset($attributes['nooflisting'])) {
            $nooflisting = $attributes['nooflisting'];
        }
        if (isset($attributes['type'])) {
            $type = $attributes['type'];
        }
        // Process agent IDs
        $agent_ids = array_map('trim', explode(',', $attributes['agentid']));


        $args = array(
            'post_type' => $type,
            'post_status' => 'publish',
            'meta_key' => 'prop_featured',
            'orderby' => 'meta_value',
            'order' => 'DESC',
            'paged' => 0,
            'posts_per_page' => $nooflisting,
            'meta_query' => array(
                  array(
                    'key' => 'property_agent',
                    'value' => count($agent_ids) === 1 ? $agent_ids[0] : $agent_ids,
                    'compare' => count($agent_ids) === 1 ? '=' : 'IN'
                )
            )
        );


        $display_grid_class= 'wpresidence_shortcode_listings_wrapper items_shortcode_wrapper row';
        if($attributes['display_grid']=='yes'){
                $display_grid_class= 'items_shortcode_wrapper_grid';
        
        }
    
    
        add_filter('posts_orderby', 'wpestate_my_order');
        $listings_per_agent = new WP_Query($args);
        remove_filter('posts_orderby', 'wpestate_my_order');

        ob_start();
        $unit_counter=1;

        wpresidence_display_property_list_as_html($listings_per_agent,array()  ,'shortcode_list',$attributes);

        $return_string = '<div class="  '.esc_attr($display_grid_class).'">' . ob_get_contents() . '</div>';
        ob_end_clean();

        return $return_string;
    }

endif;

/**
 * WpResidence List Agents Function
 *
 * This file contains the function to display a list of agents for the WpResidence theme.
 * It handles various filtering options and can display agents in a grid or list format.
 *
 * @package WpResidence
 * @subpackage Shortcodes
 * @since 1.0.0
 *
 * @dependencies 
 * - wp_get_current_user()
 * - wpestate_request_transient_cache()
 * - wpresidence_display_agent_list_as_html()
 * - wpestate_set_transient_cache()
 * - wpestate_html_compress()
 *
 * Usage: [list_agents title="Our Agents" number="4" rownumber="2" link="http://example.com/agents" random_pick="no" order="ASC" display_grid="no"]
 */

 if (!function_exists('wpestate_list_agents_function')):
    function wpestate_list_agents_function($attributes, $content = null) {
        // Get current user
        $current_user = wp_get_current_user();

        // Set up default attributes
        $default_attributes = array(
            'title' => '',
            'type' => 'estate_agent',
            'category_ids' => '',
            'action_ids' => '',
            'city_ids' => '',
            'area_ids' => '',
            'number' => 4,
            'rownumber' => 4,
            'align' => 'vertical',
            'link' => '',
            'random_pick' => 'no',
            'orderby' => 'ID',
            'order' => 'ASC',
            'unit_type' => 1,
            'display_grid' => 'no'
        );

        // Parse and extract attributes
        $attributes = shortcode_atts($default_attributes, $attributes);
        extract($attributes);

        // Initialize variables
        $return_string = '';
        $button = '';
        $class = '';
        // $orderby = $attributes['orderby'];
        $transient_name = 'wpestate_sh_agent_list';

        // Build transient name based on attributes
        $transient_name .= '_' . implode('_', array_filter(array($category_ids, $action_ids, $city_ids, $area_ids)));
        $transient_name .= ($random_pick === 'yes') ? '_rand' : '';
        $transient_name .= '_row' . $rownumber . '_' . $number;
      
        if ( function_exists( 'wpestate_set_transient_name_multilang' ) ) {
            $transient_name = wpestate_set_transient_name_multilang( $transient_name . '_' );
        } else {
            $transient_name .= '_';
        }

        // Set up query arguments
        $tax_query = array();
        $taxonomies = array(
            'property_category_agent' => $category_ids,
            'property_action_category_agent' => $action_ids,
            'property_city_agent' => $city_ids,
            'property_area_agent' => $area_ids
        );

        foreach ($taxonomies as $taxonomy => $ids) {
            if (!empty($ids)) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field' => 'term_id',
                    'terms' => explode(',', $ids)
                );
            }
        }

        $args = array(
            'post_type' => 'estate_agent',
            'post_status' => 'publish',
            'posts_per_page' => $number,
            'orderby' => ($random_pick === 'yes') ? 'rand' : $orderby,
            'order' => $order,
            'tax_query' => $tax_query
        );

        // Prepare wrapper class
        $wrap_class = ($display_grid === 'yes') ? 'items_shortcode_wrapper_grid' : 'row';

        // Prepare "More Agents" button
        if (!empty($link)) {
            $button = sprintf(
                '<div class="listinglink-wrapper"><a href="%s"><span class="wpresidence_button">%s</span></a></div>',
                esc_url($link),
                esc_html__('more agents', 'wpresidence-core')
            );
        } else {
            $class = "nobutton";
        }

        // Start building return string
        $return_string .= sprintf(
            '<div class="wpresidence_shortcode_listings_wrapper bottom-%s %s %s">',
            esc_attr($type),
            esc_attr($class),
            esc_attr($wrap_class)
        );

        if (!empty($title)) {
            $return_string .= sprintf('<h2 class="shortcode_title">%s</h2>', esc_html($title));
        }

        // Try to get cached template
        $templates = function_exists('wpestate_request_transient_cache') ? wpestate_request_transient_cache($transient_name) : false;

        // If no cached template or random pick is requested, generate new content
        if ($templates === false || $random_pick === 'yes') {
            $recent_posts = new WP_Query($args);
            
            ob_start();
            if ($display_grid === 'yes') {
                echo '<div class="items_shortcode_wrapper_grid">';
            }
            wpresidence_display_agent_list_as_html($recent_posts, 'estate_agent', array(), 'shortcode', $attributes);
            if ($display_grid === 'yes') {
                echo '</div>';
            }
            $templates = ob_get_contents();
            ob_end_clean();

            // Cache the generated content if not using random pick
            if ($random_pick !== 'yes' && function_exists('wpestate_set_transient_cache')) {
                wpestate_set_transient_cache($transient_name, wpestate_html_compress($templates), 60 * 60 * 24);
            }
        }

        // Append templates and button to return string
        $return_string .= $templates . $button . '</div>';

        wp_reset_query();
        return $return_string;
    }
endif;










////////////////////////////////////////////////////////////////////////////////////
/// wpestate_icon_container_function
////////////////////////////////////////////////////////////////////////////////////

if (!function_exists("wpestate_icon_container_function")):

    function wpestate_icon_container_function($attributes, $content = null) {
        $return_string = '';
        $link = '';
        $title = '';
        $image = '';
        $content_box = '';
        $haseffect = '';

        $title = '';
        if (isset($attributes['title'])) {
            $title = $attributes['title'];
        }



        $attributes = shortcode_atts(
                array(
                    'title' => 'title',
                    'image' => '',
                    'content_box' => 'Content of the box goes here',
                    'image_effect' => 'yes',
                    'link' => ''
                ), $attributes);

        if (isset($attributes['image'])) {
            $image = $attributes['image'];
        }
        if (isset($attributes['content_box'])) {
            $content_box = $attributes['content_box'];
        }

        if (isset($attributes['link'])) {
            $link = $attributes['link'];
        }

        if (isset($attributes['image_effect'])) {
            $haseffect = $attributes['image_effect'];
        }

        $return_string .= '<div class="iconcol">';
        if ($image != '') {
            $return_string .= '<div class="icon_img">';

        
            $return_string .= '  <a href="' . $link . '"><img src="' . $image . '"  class="img-responsive" alt="thumb"/ ></a>
            </div>';
        }

        $return_string .= '<h3><a href="' . $link . '">' . $title . '</a></h3>';
        $return_string .= '<p>' . do_shortcode($content_box) . '</p>';
        $return_string .= '</div>';

        return $return_string;
    }

endif;

////////////////////////////////////////////////////////////////////////////////////
/// spacer
////////////////////////////////////////////////////////////////////////////////////

if (!function_exists("wpestate_spacer_shortcode_function")):

    function wpestate_spacer_shortcode_function($attributes, $content = null) {
        $height = '';
        $type = 1;

        $attributes = shortcode_atts(
                array(
                    'type' => '1',
                    'height' => '40',
                ), $attributes);

        if (isset($attributes['type'])) {
            $type = $attributes['type'];
        }

        if (isset($attributes['height'])) {
            $height = $attributes['height'];
        }


        $return_string = '';
        $return_string .= '<div class="spacer" style="height:' . $height . 'px;">';
        if ($type == 2) {
            $return_string .= '<span class="spacer_line"></span>';
        }
        $return_string .= '</div>';
        return $return_string;
    }

endif;

///////////////////////////////////////////////////////////////////////////////////////////
// font awesome function
///////////////////////////////////////////////////////////////////////////////////////////
if (!function_exists("wpestate_font_awesome_function")):

    function wpestate_font_awesome_function($attributes, $content = null) {
        $icon = $attributes['icon'];
        $size = $attributes['size'];
        $return_string = '<i class="' . $icon . '" style="' . $size . '"></i>';
        return $return_string;
    }

endif;

/**
 * WpResidence List Items by ID Function
 *
 * This file contains the function to display a list of items (properties, agents, or blog posts)
 * based on their IDs for the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Shortcodes
 * @since 1.0.0
 *
 * @dependencies 
 * - WP_Query
 * - wpestate_request_transient_cache()
 * - wpestate_set_transient_cache()
 * - wpestate_html_compress()
 * - wpresidence_display_property_list_as_html()
 * - wpresidence_display_agent_list_as_html()
 * - wpresidence_display_blog_list_as_html()
 *
 * 
 */

 if (!function_exists('wpestate_list_items_by_id_function')):

    function wpestate_list_items_by_id_function($attributes, $content = null) {
        // Initialize variables
        $return_string = '';
        $button = '';
        $ids = '';
        $ids_array = array();
        $title = '';
        $display_grid = 'no';

        // Set default attributes and merge with user-provided attributes
        $attributes = shortcode_atts(
            array(
                'title' => '',
                'type' => 'properties',
                'ids' => '',
                'number' => 3,
                'rownumber' => 4,
                'align' => 'vertical',
                'link' => '#',
                'display_grid' => 'no'
            ), 
            $attributes
        );

        // Extract title and IDs from attributes
        $title = isset($attributes['title']) ? $attributes['title'] : '';
        $ids = $transient_ids = isset($attributes['ids']) ? $attributes['ids'] : '';
        $ids_array = explode(',', $ids);
        $display_grid = isset($attributes['display_grid']) ? $attributes['display_grid'] : 'no';

        // Determine post type based on 'type' attribute
        $type = ($attributes['type'] == 'properties') ? 'estate_property' : 'post';

        // Generate button HTML if link is provided
        if ($attributes['link'] != '') {
            $button_text = ($attributes['type'] == 'properties') ? esc_html__(' more listings', 'wpresidence-core') : esc_html__(' more articles', 'wpresidence-core');
            $button = sprintf(
                '<div class="listinglink-wrapper"><a href="%s"><span class="wpresidence_button">%s</span></a></div>',
                esc_url($attributes['link']),
                $button_text
            );
        }

        // Set up query arguments
        $args = array(
            'post_type' => $type,
            'post_status' => 'publish',
            'paged' => 0,
            'posts_per_page' => count($ids_array),
            'post__in' => $ids_array,
            'orderby' => 'post__in'
        );

        // Determine wrapper classes
        $wrap_class = $display_grid == 'yes' ? ' ' : '';
        $grid_class = $display_grid == 'yes' ? 'items_shortcode_wrapper_grid' : 'row items_shortcode_wrapper';

        // Start building the return string
        $return_string .= sprintf('<div class="wpresidence_shortcode_listings_wrapper %s">', esc_attr($wrap_class));
        if ($title != '') {
            $return_string .= sprintf('<h2 class="shortcode_title">%s</h2>', esc_html($title));
        }
        $return_string .= sprintf('<div class="wpestate_list_items_by_id_wrapper  %s">', esc_attr($grid_class));

        // Generate transient name
        $transient_name = 'wpestate_list_items_by_id_' . $transient_ids;
        if ( function_exists( 'wpestate_set_transient_name_multilang' ) ) {
            $transient_name = wpestate_set_transient_name_multilang( $transient_name . '_' );
        } else {
            $transient_name .= '_';
        }

        if (isset($_COOKIE['my_custom_curr_symbol'])) {
            $transient_name .= '_' . $_COOKIE['my_custom_curr_symbol'];
        }
        if (isset($_COOKIE['my_measure_unit'])) {
            $transient_name .= $_COOKIE['my_measure_unit'];
        }

        // Try to get cached template
        $templates = false;
        if (function_exists('wpestate_request_transient_cache')) {
            $templates = wpestate_request_transient_cache($transient_name);
        }

        // If no cached template, generate new content
        if ($templates === false) {
            $recent_posts = new WP_Query($args);
            ob_start();

            // Display appropriate list based on type
            if ($type == 'estate_property') {
                wpresidence_display_property_list_as_html($recent_posts, array(), 'shortcode_list', $attributes);
            } elseif ($type == 'estate_agents') {
                wpresidence_display_agent_list_as_html($recent_posts, 'estate_agent', array(), 'shortcode', $attributes);
            } else {
                wpresidence_display_blog_list_as_html($recent_posts, array(), 'shortcode', $attributes);
            }

            $templates = ob_get_contents();
            ob_end_clean();

            // Cache the generated content
            if (function_exists('wpestate_set_transient_cache')) {
                wpestate_set_transient_cache($transient_name, wpestate_html_compress($templates), 4 * 60 * 60);
            }
        }

        // Finalize the return string
        $return_string .= $templates;
        $return_string .= $button;
        $return_string .= '</div></div>';

        // Reset query and return the generated content
        wp_reset_query();
        return $return_string;
    }

endif; // end wpestate_list_items_by_id_function






/**
 * Featured Article Shortcode for WpResidence Theme
 *
 * This file contains the wpestate_featured_article function, which generates
 * a featured article display for use within the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Shortcodes
 * @since 1.0.1
 *
 * Dependencies:
 * - WordPress core
 * - WpResidence theme functions and templates
 *
 * Usage:
 * [featured_article id="123" second_line="Optional text" design_type="1"]
 */

 if ( ! function_exists( 'wpestate_featured_article' ) ) :
    /**
     * Generate HTML for a featured article
     *
     * @param array  $attributes Shortcode attributes
     * @param string $content    Shortcode content (not used in this function)
     * @return string HTML content of the featured article
     */
    function wpestate_featured_article( $attributes, $content = null ) {
        $attributes = shortcode_atts(
            array(
                'id'           => '',
                'second_line'  => '',
                'design_type'  => 1
            ),
            $attributes,
            'featured_article'
        );

        // Sanitize and validate inputs
        $article_id   = absint( $attributes['id'] );
        $second_line  = sanitize_text_field( $attributes['second_line'] );
        $design_type  = absint( $attributes['design_type'] );

        // Check if the article exists and is published
        if ( 'publish' !== get_post_status( $article_id ) || 'post' !== get_post_type( $article_id ) ) {
            return '';
        }

        // Generate a unique transient name
        $transient_name = 'wpestate_featured_article_' . $article_id;
        if ( function_exists( 'wpestate_set_transient_name_multilang' ) ) {
            $transient_name = wpestate_set_transient_name_multilang( $transient_name . '_' );
        } else {
            $transient_name .= '_';
        }

        $transient_name .= '_type' . $design_type;

        // Try to get cached result
        $return_string = function_exists( 'wpestate_request_transient_cache' ) ? wpestate_request_transient_cache( $transient_name ) : false;

        if ( false === $return_string ) {
            // Prepare data for template
            $post_data = array(
                'thumb_id'    => get_post_thumbnail_id( $article_id ),
                'preview'     => wp_get_attachment_image_src( get_post_thumbnail_id( $article_id ), 'property_featured' ),
                'avatar'      => wpestate_get_avatar_url( get_avatar( get_the_author_meta( 'email', get_post_field( 'post_author', $article_id ) ), 55 ) ),
                'content'     => get_the_excerpt( $article_id ),
                'title'       => get_the_title( $article_id ),
                'link'        => get_permalink( $article_id ),
                'second_line' => $second_line
            );

            // Set default image if no thumbnail
            if ( ! isset( $post_data['preview'][0] ) || $post_data['preview'][0] == '' ) {
                $post_data['preview'][0] = WPESTATE_PLUGIN_DIR_URL . '/img/default_property_featured.jpg';
            }

            // Start output buffering
            ob_start();

            // Load appropriate template based on design type
            $template_path = get_template_directory() . '/templates/blog_card_templates/featured_blog_';
            $template_path .= ( $design_type == 2 ) ? '2.php' : '1.php';

            if ( $design_type == 2 ) {
                $post_data['preview'] = wp_get_attachment_image_src( get_post_thumbnail_id( $article_id ), 'full' );
            }

            load_template( $template_path, false, $post_data );

            $return_string = ob_get_clean();

            // Cache the result
            if ( function_exists( 'wpestate_set_transient_cache' ) ) {
                wpestate_set_transient_cache( $transient_name, wpestate_html_compress( $return_string ), 4 * HOUR_IN_SECONDS );
            }
        }

        return $return_string;
    }
endif; // end featured_article function




/**
 * Avatar URL Extraction Function for WpResidence Theme
 *
 * This file contains the wpestate_get_avatar_url function, which extracts
 * the URL of an avatar from the HTML string returned by get_avatar().
 *
 * @package WpResidence
 * @subpackage Utilities
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core (for get_avatar function)
 *
 * Usage:
 * $avatar_url = wpestate_get_avatar_url(get_avatar($user_id, 96));
 */

 if (!function_exists('wpestate_get_avatar_url')):
    /**
     * Extract avatar URL from get_avatar() HTML string
     *
     * @param string $get_avatar HTML string returned by get_avatar()
     * @return string URL of the avatar image or empty string if not found
     */
    function wpestate_get_avatar_url($get_avatar) {
        preg_match("/src='(.*?)'/i", $get_avatar, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        } else {
            return'';
        }
    }
endif; // end wpestate_get_avatar_url

/**
 * Featured Property Function for WpResidence Theme
 *
 * This file contains the wpestate_featured_property function, which generates
 * HTML for displaying a featured property in the WpResidence theme.
 *
 * Package: WpResidence
 * Version: 1.0.2
 *
 * Dependencies:
 * - WordPress core
 * - WpResidence theme functions and options
 *
 * Usage:
 * This function is typically called via a shortcode in WordPress posts or pages.
 * Example: [featured_property id="123" sale_line="Special Offer" design_type="2"]
 *
 * @package WpResidence
 */

 if ( ! function_exists( 'wpestate_featured_property' ) ) :
    /**
     * Generate HTML for a featured property
     *
     * @param array  $attributes Shortcode attributes.
     * @param string $content    Shortcode content (not used).
     * @return string HTML output for the featured property.
     */
    function wpestate_featured_property( $attributes, $content = null ) {
        $attributes = shortcode_atts(
            array(
                'id'          => '',
                'sale_line'   => '',
                'design_type' => 1,
            ),
            $attributes,
            'featured_property'
        );

        $prop_id     = absint( $attributes['id'] );
        $design_type = absint( $attributes['design_type'] );
        $sale_line   = sanitize_text_field( $attributes['sale_line'] );

        // Check if the property exists and is published
        if ( 'publish' !== get_post_status( $prop_id ) || 'estate_property' !== get_post_type( $prop_id ) ) {
            return '';
        }

        // Generate a unique transient name
        $transient_name = 'wpestate_featured_prop_' . $prop_id;
        if ( function_exists( 'wpestate_set_transient_name_multilang' ) ) {
            $transient_name = wpestate_set_transient_name_multilang( $transient_name . '_' );
        } else {
            $transient_name .= '_';
        }

        $transient_name .= isset( $_COOKIE['my_custom_curr_symbol'] ) ? '_' . sanitize_text_field( $_COOKIE['my_custom_curr_symbol'] ) : '';
        $transient_name .= isset( $_COOKIE['my_measure_unit'] ) ? sanitize_text_field( $_COOKIE['my_measure_unit'] ) : '';
        $transient_name .= '_type' . $design_type;

        // Try to get cached result
        $return_string = function_exists( 'wpestate_request_transient_cache' ) ? wpestate_request_transient_cache( $transient_name ) : false;

        if ( false === $return_string ) {
            // Set up necessary variables for the template
            $wpestate_property_unit_slider = wpresidence_get_option('wp_estate_prop_list_slider', '');
            
            // Start output buffering
            ob_start();
            
            // Include the appropriate template based on design type
            $template_path = 'templates/featured_property_card_templates/featured_property_';
            $template_path .= in_array( $design_type, array( 1, 2, 3, 5 ) ) ? $design_type : '4b';
            $template_path .= '.php';
            
            include( locate_template( $template_path ) );
            
            $return_string = ob_get_clean();

            // Cache the result
            if ( function_exists( 'wpestate_set_transient_cache' ) ) {
                wpestate_set_transient_cache( $transient_name, wpestate_html_compress( $return_string ), 4 * HOUR_IN_SECONDS );
            }
        }

        return $return_string;
    }
endif;




/**
 * Featured Agent Shortcode for WpResidence Theme
 *
 * This file contains the wpestate_featured_agent function, which generates
 * a featured agent card display for use within the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Shortcodes
 * @since 1.0.2
 *
 * Dependencies:
 * - WordPress core
 * - WpResidence theme functions and templates
 *
 * Usage:
 * [featured_agent id="123" notes="Optional notes"]
 */

if ( ! function_exists( 'wpestate_featured_agent' ) ) :
    /**
     * Generate HTML for a featured agent card
     *
     * @param array  $attributes Shortcode attributes
     * @param string $content    Shortcode content (not used in this function)
     * @return string HTML content of the featured agent card
     */
    function wpestate_featured_agent( $attributes, $content = null ) {
        $attributes = shortcode_atts(
            array(
                'id'    => 0,
                'notes' => '',
            ),
            $attributes,
            'featured_agent'
        );

        $agent_id = absint( $attributes['id'] );
        $notes    = wp_kses_post( $attributes['notes'] );

        // Check if the agent exists and is published
        if ( 'publish' !== get_post_status( $agent_id ) || 'estate_agent' !== get_post_type( $agent_id ) ) {
            return '';
        }

        // Generate a unique transient name
        $transient_name = 'wpestate_featured_agent_' . $agent_id;
        if ( function_exists( 'wpestate_set_transient_name_multilang' ) ) {
            $transient_name = wpestate_set_transient_name_multilang( $transient_name . '_' );
        } else {
            $transient_name .= '_';
        }
        
        // Try to get cached result
        $return_string = function_exists( 'wpestate_request_transient_cache' ) ? wpestate_request_transient_cache( $transient_name ) : false;

        if ( false === $return_string ) {
            // Start output buffering
            ob_start();

            // Set $postID for the template
            $postID = $agent_id;

            // Include the template
            include( locate_template( 'templates/agent_card_templates/agent_unit_featured.php' ) );

            // Get the buffered content
            $return_string = ob_get_clean();

            // Cache the result
            if ( function_exists( 'wpestate_set_transient_cache' ) ) {
                wpestate_set_transient_cache( $transient_name, wpestate_html_compress( $return_string ), 4 * HOUR_IN_SECONDS );
            }
        }

        return $return_string;
    }
endif; // End wpestate_featured_agent function check











////////////////////////////////////////////////////////////////////////////////////////////
///  shortcode - recent post with picture
////////////////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_recent_posts_pictures')):

    function wpestate_recent_posts_pictures($attributes, $content = null) {
        global $options;
        global $align;
        global $align_class;
        global $post;
        global $wpestate_currency;
        global $where_currency;
        global $is_shortcode;
        global $show_compare_only;
        global $row_number_col;
        global $current_user;
        global $curent_fav;
        global $wpestate_property_unit_slider;
        global $wpestate_no_listins_per_row;
        global $wpestate_uset_unit;
        global $wpestate_custom_unit_structure;

        $wpestate_custom_unit_structure = wpresidence_get_option('wpestate_property_unit_structure');
        $wpestate_uset_unit = intval(wpresidence_get_option('wpestate_uset_unit', ''));
        $wpestate_no_listins_per_row = intval(wpresidence_get_option('wp_estate_listings_per_row', ''));
        $blog_unit                   =   wpestate_blog_unit_selector(); 
        $current_user = wp_get_current_user();

        $title = '';
        if (isset($attributes['title'])) {
            $title = $attributes['title'];
        }

        $attributes = shortcode_atts(
                array(
                    'title' => '',
                    'type' => 'properties',
                    'category_ids' => '',
                    'action_ids' => '',
                    'city_ids' => '',
                    'area_ids' => '',
                    'state_ids' => '',
                    'number' => 4,
                    'rownumber' => 4,
                    'align' => 'vertical',
                    'link' => '',
                    'show_featured_only' => 'no',
                    'random_pick' => 'no',
                    'featured_first' => 'no'
                ), $attributes);

        $userID = $current_user->ID;
        $user_option = 'favorites' . $userID;
        $curent_fav = get_option($user_option);
        $wpestate_property_unit_slider = wpresidence_get_option('wp_estate_prop_list_slider', '');

        $options = wpestate_page_details_sh($post->ID);
        $return_string = '';
        $pictures = '';
        $button = '';
        $class = '';
        $category = $action = $city = $area = $state = '';

        $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
        $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
        $is_shortcode = 1;
        $show_compare_only = 'no';
        $row_number_col = '';
        $row_number = '';
        $show_featured_only = '';
        $random_pick = '';
        $featured_first = '';
        $orderby = 'meta_value';

        $property_card_type = intval(wpresidence_get_option('wp_estate_unit_card_type'));
        $property_card_type_string = '';
        if ($property_card_type == 0) {
            $property_card_type_string = '';
        } else {
            $property_card_type_string = '_type' . $property_card_type;
        }

        if (isset($attributes['category_ids'])) {
            $category = $attributes['category_ids'];
        }

        if (isset($attributes['action_ids'])) {
            $action = $attributes['action_ids'];
        }

        if (isset($attributes['city_ids'])) {
            $city = $attributes['city_ids'];
        }

        if (isset($attributes['area_ids'])) {
            $area = $attributes['area_ids'];
        }

        if (isset($attributes['state_ids'])) {
            $state = $attributes['state_ids'];
        }

        if (isset($attributes['show_featured_only'])) {
            $show_featured_only = $attributes['show_featured_only'];
        }

        if (isset($attributes['random_pick'])) {
            $random_pick = $attributes['random_pick'];
            if ($random_pick === 'yes') {
                $orderby = 'rand';
            }
        }


        if (isset($attributes['featured_first'])) {
            $featured_first = $attributes['featured_first'];
        }


        $post_number_total = $attributes['number'];
        if (isset($attributes['rownumber'])) {
            $row_number = $attributes['rownumber'];
        }

        // max 4 per row
        if ($row_number > 4) {
            $row_number = 4;
        }

        if ($row_number == 4) {
            $row_number_col = 3; // col value is 3
        } else if ($row_number == 3) {
            $row_number_col = 4; // col value is 4
        } else if ($row_number == 2) {
            $row_number_col = 6; // col value is 6
        } else if ($row_number == 1) {
            $row_number_col = 12; // col value is 12
            if ($attributes['align'] == 'vertical') {
                $row_number_col = 0;
            }
        }

        $align = '';
        $align_class = '';
        if (isset($attributes['align']) && $attributes['align'] == 'horizontal') {
            $align = "col-md-12";
            $align_class = 'the_list_view';
            $row_number_col = '12';
        }


        if ($attributes['type'] == 'properties') {
            $type = 'estate_property';

            $category_array = '';
            $action_array = '';
            $city_array = '';
            $area_array = '';
            $state_array = '';

            // build category array
            if ($category != '') {
                $category_of_tax = array();
                $category_of_tax = explode(',', $category);
                $category_array = array(
                    'taxonomy' => 'property_category',
                    'field' => 'term_id',
                    'terms' => $category_of_tax
                );
            }


            // build action array
            if ($action != '') {
                $action_of_tax = array();
                $action_of_tax = explode(',', $action);
                $action_array = array(
                    'taxonomy' => 'property_action_category',
                    'field' => 'term_id',
                    'terms' => $action_of_tax
                );
            }

            // build city array
            if ($city != '') {
                $city_of_tax = array();
                $city_of_tax = explode(',', $city);
                $city_array = array(
                    'taxonomy' => 'property_city',
                    'field' => 'term_id',
                    'terms' => $city_of_tax
                );
            }

            // build city array
            if ($area != '') {
                $area_of_tax = array();
                $area_of_tax = explode(',', $area);
                $area_array = array(
                    'taxonomy' => 'property_area',
                    'field' => 'term_id',
                    'terms' => $area_of_tax
                );
            }

            if ($state != '') {
                $state_of_tax = array();
                $state_of_tax = explode(',', $state);
                $state_array = array(
                    'taxonomy' => 'property_county_state',
                    'field' => 'term_id',
                    'terms' => $state_of_tax
                );
            }
            $meta_query = array();
            if ($show_featured_only == 'yes') {
                $compare_array = array();
                $compare_array['key'] = 'prop_featured';
                $compare_array['value'] = 1;
                $compare_array['type'] = 'numeric';
                $compare_array['compare'] = '=';
                $meta_query[] = $compare_array;
            }

            if ($featured_first == "no") {
                $orderby = 'ID';
            }

            $args = array(
                'post_type' => $type,
                'post_status' => 'publish',
                'paged' => 1,
                'posts_per_page' => $post_number_total,
                'meta_key' => 'prop_featured',
                'orderby' => $orderby,
                'order' => 'DESC',
                'meta_query' => $meta_query,
                'tax_query' => array(
                    $category_array,
                    $action_array,
                    $city_array,
                    $area_array,
                    $state_array
                )
            );
        } else {
            $type = 'post';

            $args = array(
                'post_type' => $type,
                'post_status' => 'publish',
                'paged' => 0,
                'posts_per_page' => $post_number_total,
                'cat' => $category
            );
        }


        if (isset($attributes['link']) && $attributes['link'] != '') {
            if ($attributes['type'] == 'properties') {
                $button .= '<div class="listinglink-wrapper">
               <a href="' .esc_url( $attributes['link'] ). '"> <span class="wpresidence_button">' . esc_html__('more listings', 'wpresidence-core') . ' </span></a>
               </div>';
            } else {
                $button .= '<div class="listinglink-wrapper">
               <a href="' . esc_url($attributes['link']) . '"> <span class="wpresidence_button">  ' . esc_html__('more articles', 'wpresidence-core') . ' </span></a>
               </div>';
            }
        } else {
            $class = "nobutton";
        }

    
        if ($attributes['type'] == 'properties') {
            if ($random_pick !== 'yes') {
                if ($featured_first == 'yes') {
                    add_filter('posts_orderby', 'wpestate_my_order');
                }

                $recent_posts = new WP_Query($args);
                $count = 1;
                if ($featured_first == 'yes') {
                    remove_filter('posts_orderby', 'wpestate_my_order');
                }
            } else {

                $args['orderby'] = 'rand';
                $recent_posts = new WP_Query($args);
                $count = 1;
            }
        } else {
            $recent_posts = new WP_Query($args);
            $count = 1;
        }

        $return_string .= '<div class="wpresidence_shortcode_listings_wrapper bottom-' . $type . ' ' . $class . '" >';
        if ($title != '') {
            $return_string .= '<h2 class="shortcode_title">' . $title . '</h2>';
        }

        ob_start();
        print 'NU E FACUT 22';
        while ($recent_posts->have_posts()): $recent_posts->the_post();
            if ($type == 'estate_property') {
                include( locate_template('templates/property_cards_templates/property_unit' . $property_card_type_string . '.php') );
            } else {
                include( locate_template($blog_unit) ) ;
            }
        endwhile;

        $templates = ob_get_contents();
        ob_end_clean();
        $return_string .= $templates;
        $return_string .= $button;
        $return_string .= '</div>';
        wp_reset_query();
        $is_shortcode = 0;
        return $return_string;
    }

endif; // end   wpestate_recent_posts_pictures


if (!function_exists('wpestate_limit_words')):

    function wpestate_limit_words($string, $max_no) {
        $words_no = explode(' ', $string, ($max_no + 1));

        if (count($words_no) > $max_no) {
            array_pop($words_no);
        }

        return implode(' ', $words_no);
    }

endif; // end   wpestate_limit_words
////////////////////////////////////////////////////////////////////////////////////////////////////////////////..
///  shortcode - testimonials
////////////////////////////////////////////////////////////////////////////////////////////////////////////////..


if (!function_exists('wpestate_testimonial_function')):

    function wpestate_testimonial_function($attributes, $content = null) {
        $return_string = '';
        $title_client = '';
        $client_name = '';
        $imagelinks = '';
        $testimonial_text = '';
        $type = 1;
        $stars_client = '';
        $testimonial_title = '';
        $attributes = shortcode_atts(
                array(
                    'client_name' => 'Name Here',
                    'title_client' => "happy client",
                    'imagelinks' => '',
                    'testimonial_text' => '',
                    'testimonial_type' => '1',
                    'stars_client' => '5',
                    'testimonial_title' => ''
                ), $attributes);

        if ($attributes['client_name']) {
            $client_name = $attributes['client_name'];
        }

        if ($attributes['title_client']) {
            $title_client = $attributes['title_client'];
        }

        if ($attributes['imagelinks']) {
            $imagelinks = $attributes['imagelinks'];
        }

        if ($attributes['testimonial_text']) {
            $testimonial_text = $attributes['testimonial_text'];
        }

        if ($attributes['testimonial_type']) {
            $type = 'type_class_' . $attributes['testimonial_type'];
        }
        if ($attributes['stars_client']) {
            $stars_client = floatval($attributes['stars_client']);
        }
        if ($attributes['testimonial_title']) {
            $testimonial_title = $attributes['testimonial_title'];
        }




        if ($type == 'type_class_1') {
            $return_string .= '     <div class="testimonial-container ' . $type . ' ">';
            $return_string .= '     <div class="testimonial-image" style="background-image:url(' . $imagelinks . ')"></div>';
            $return_string .= '     <div class="testimonial-text">' . $testimonial_text . '</div>';
            $return_string .= '     <div class="testimonial-author-line"><span class="testimonial-author">' . $client_name . '</span>, ' . $title_client . ' </div>';
            $return_string .= '     </div>';
        } else if ($type == 'type_class_2') {
            $return_string .= '     <div class="testimonial-container ' . $type . ' ">';
            $return_string .= '     <div class="testimonial-text">' . $testimonial_text . '</div>';
            $return_string .= '     <div class="testimonial-image" style="background-image:url(' . $imagelinks . ')"></div>';
            $return_string .= '     <div class="testimonial-author-line"><span class="testimonial-author">' . $client_name . '</span>, ' . $title_client . ' </div>';
            $return_string .= '     </div>';
        } else if ($type == 'type_class_3') {
            $return_string .= '     <div class="testimonial-container ' . $type . ' ">';
            $return_string .= '     <div class="testimonial-image" style="background-image:url(' . $imagelinks . ')"></div>';
            $return_string .= '     <div class="testimonial_title">' . $testimonial_title . '</div>';

            $return_string .= '     <div class="testimmonials_starts">' . wpestate_starts_reviews_core($stars_client) . '</div>';
            $return_string .= '     <div class="testimonial-text">' . $testimonial_text . '</div>';

            $return_string .= '     <div class="testimonial-author-line"><span class="testimonial-author">' . $client_name . '</span>, ' . $title_client . ' </div>';
            $return_string .= '     </div>';
        } else if ($type == 'type_class_4') {
            $return_string .= '     <div class="testimonial-container ' . $type . ' ">';
            $return_string .= '     <div class="testimonial-image" style="background-image:url(' . $imagelinks . ')"></div>';
            $return_string .= '     <div class="testimonial-author-line">' . $client_name . ' </div>';
            $return_string .= '     <div class="testimonial-location-line"> ' . $title_client . ' </div>';

            $return_string .= '     <div class="testimonial-text">' . $testimonial_text . '</div>';
            $return_string .= '     <div class="testimmonials_starts">' . wpestate_starts_reviews_core($stars_client) . '</div>';

            $return_string .= '     </div>';
        }





        return $return_string;
    }

endif; // end   wpestate_testimonial_function


if (!function_exists('wpestate_testimonial_function2')):

    function wpestate_testimonial_function2($attributes, $content = null) {
        $return_string = '';
        $title_client = '';
        $client_name = '';
        $imagelinks = '';
        $testimonial_text = '';
        $type = 1;
        $stars_client = '';
        $testimonial_title = '';
        $attributes = shortcode_atts(
                array(
                    'client_name' => 'Name Here',
                    'title_client' => "happy client",
                    'imagelinks' => '',
                    'testimonial_text' => '',
                    'testimonial_type' => '1',
                    'stars_client' => '5',
                    'testimonial_title' => ''
                ), $attributes);

        if ($attributes['client_name']) {
            $client_name = $attributes['client_name'];
        }

        if ($attributes['title_client']) {
            $title_client = $attributes['title_client'];
        }

        if ($attributes['imagelinks']) {
            $imagelinks = $attributes['imagelinks'];
        }

        if ($attributes['testimonial_text']) {
            $testimonial_text = $attributes['testimonial_text'];
        }

        if ($attributes['testimonial_type']) {
            $type = 'type_class_' . $attributes['testimonial_type'];
        }
        if ($attributes['stars_client']) {
            $stars_client = floatval($attributes['stars_client']);
        }
        if ($attributes['testimonial_title']) {
            $testimonial_title = $attributes['testimonial_title'];
        }




        if ($type == 'type_class_1') {
            $return_string .= '     <div class="testimonial-container ' . $type . ' ">';
            $return_string .= '     <div class="testimonial-image" style="background-image:url(' . $imagelinks . ')"></div>';
            $return_string .= '     <div class="testimonial-text">' . $testimonial_text . '</div>';
            $return_string .= '     <div class="testimonial-author-line"><span class="testimonial-author">' . $client_name . '</span>, ' . $title_client . ' </div>';
            $return_string .= '     </div>';
        } else if ($type == 'type_class_2') {
            $return_string .= '     <div class="testimonial-container ' . $type . ' ">';
            $return_string .= '     <div class="testimonial-text">' . $testimonial_text . '</div>';
            $return_string .= '     <div class="testimonial-image" style="background-image:url(' . $imagelinks . ')"></div>';
            $return_string .= '     <div class="testimonial-author-line"><span class="testimonial-author">' . $client_name . '</span>, ' . $title_client . ' </div>';
            $return_string .= '     </div>';
        } else if ($type == 'type_class_3') {
            $return_string .= '     <div class="testimonial-container ' . $type . ' ">';
            $return_string .= '     <div class="testimonial-image" style="background-image:url(' . $imagelinks . ')"></div>';
            $return_string .= '     <div class="testimonial_title">' . $testimonial_title . '</div>';

            $return_string .= '     <div class="testimmonials_starts">' . wpestate_starts_reviews_core($stars_client) . '</div>';
            $return_string .= '     <div class="testimonial-text">' . $testimonial_text . '</div>';

            $return_string .= '     <div class="testimonial-author-line"><span class="testimonial-author">' . $client_name . '</span>, ' . $title_client . ' </div>';
            $return_string .= '     </div>';
        }





        print $return_string;
    }

endif; // end   wpestate_testimonial_function

function wpestate_starts_reviews_core($stars) {
    $stars=floatval($stars);
    $whole = floor($stars);
    $fraction = $stars - $whole;
    $return_string = '';

    for ($i = 1; $i <= $whole; $i++) {
        $return_string .= '<i class="fas fa-star"></i>';
    }
    if ($fraction > 0) {
        $return_string .= '<i class="fas fa-star-half"></i>';
    }
    return $return_string;
}

if (!function_exists('wpestate_testimonial_slider_function_gutenberg')):

    function wpestate_testimonial_slider_function_gutenberg($attributes, $content = null) {
        $return_string = '';
        $title = '';
        $visible_items = '';
        $slider_types = '';
        $attributes = shortcode_atts(
                array(
                    'title' => '',
                    'visible_items' => '1',
                    'slider_types' => '1',
                ), $attributes);

        wp_enqueue_script('slick.min');

        if ($attributes['title']) {
            $title = $attributes['title'];
        }

        if ($attributes['visible_items']) {
            $visible_items = $attributes['visible_items'];
        }

        if ($attributes['slider_types']) {
            $slider_types = $attributes['slider_types'];
        }


        $return_string .= '<div class="testimonial-slider-container container_type_' . $slider_types . '" data-visible-items="' . $visible_items . '" data-auto="0">';
        $return_string .= $title . $content;
        $return_string .= '</div>';

        $return_string .= '<script type="text/javascript">
                //<![CDATA[

                jQuery(document).ready(function(){
                   wpestate_enable_slick_testimonial();
                });
                //]]>
            </script>';

        return $return_string;
    }

endif; // end   wpestate_testimonial_function


if (!function_exists('wpestate_testimonial_slider_function')):

    function wpestate_testimonial_slider_function($attributes, $content = null) {
        $return_string = '';
        $title = '';
        $visible_items = '';
        $slider_types = '';
        $attributes = shortcode_atts(
                array(
                    'title' => '',
                    'visible_items' => '1',
                    'slider_types' => '1',
                ), $attributes);

        wp_enqueue_script('slick.min');
        if ($attributes['title']) {
            $title = $attributes['title'];
        }

        if ($attributes['visible_items']) {
            $visible_items = $attributes['visible_items'];
        }

        if ($attributes['slider_types']) {
            $slider_types = $attributes['slider_types'];
        }


        $return_string .= '<div class="testimonial-slider-container container_type_' . $slider_types . '" data-visible-items="' . $visible_items . '" data-auto="0">';
        $return_string .= $title . do_shortcode($content);
        $return_string .= '</div>';

        $return_string .= '<script type="text/javascript">
                //<![CDATA[

                jQuery(document).ready(function(){
                   wpestate_enable_slick_testimonial();
                });
                //]]>
            </script>';

        return $return_string;
    }

endif; // end   wpestate_testimonial_function
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///  shortcode - reccent post function
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_recent_posts_function')):

    function wpestate_recent_posts_function($attributes, $heading = null) {
        $return_string = '';
        extract(shortcode_atts(array(
            'posts' => 1,
                        ), $attributes));

        query_posts(array('orderby' => 'date', 'order' => 'DESC', 'showposts' => $posts));
        $return_string = '<div id="recent_posts"><ul><h3>' . $heading . '</h3>';
        if (have_posts()) :
            while (have_posts()) : the_post();
                $return_string .= '<li><a href="' . esc_url(get_permalink()) . '">' . get_the_title() . '</a></li>';
            endwhile;
        endif;

        $return_string .= '</div></ul>';
        wp_reset_query();

        return $return_string;
    }

endif; // end   wpestate_recent_posts_function
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///  shortcode - memerbership packages function
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_membership_packages_function')):

    function wpestate_membership_packages_function($atts, $content = null) {
        $package_id = '';
        $pack_featured_sh = array('no', 'yes');
        $package_content = '';
        $return_string = '';

        $attributes = shortcode_atts(
                array(
                    'package_id' => '',
                    'pack_featured_sh' => 'no',
                    'package_content' => ''
                ), $atts);

        if (isset($attributes['package_id'])) {
            $package_id = $attributes['package_id'];
        }
        if (isset($attributes['pack_featured_sh'])) {
            $pack_featured_sh = $attributes['pack_featured_sh'];
        }
        if (isset($attributes['package_content'])) {
            if (is_array($attributes['package_content'])) {
                $package_content = $attributes['package_content'][0];
            } else {
                $package_content = $attributes['package_content'];
            }
        }



        if ($pack_featured_sh == 'yes') {
            $pack_featured_sh = 'featured_pack_sh';
        } else {
            $pack_featured_sh = '';
        }

        $pack_price = get_post_meta($package_id, 'pack_price', true);
        $biling_period = get_post_meta($package_id, 'biling_period', true);
        $billing_freq = get_post_meta($package_id, 'billing_freq', true);
        $pack_image_included = get_post_meta($package_id, 'pack_image_included', true);
        $pack_featured = get_post_meta($package_id, 'pack_featured_listings', true);
        $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_submission_curency', ''));
        $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
        if ($billing_freq > 1) {
            $biling_period .= 's';
        }




        switch (strtolower($biling_period)) {
            case 'day':
                $biling_period = esc_html__('Day', 'wpresidence-core');
                break;
            case 'days':
                $biling_period = esc_html__('Days', 'wpresidence-core');
                break;
            case 'week':
                $biling_period = esc_html__('Week', 'wpresidence-core');
                break;
            case 'weeks':
                $biling_period = esc_html__('Weeks', 'wpresidence-core');
                break;
            case 'month':
                $biling_period = esc_html__('Month', 'wpresidence-core');
                break;
            case 'months':
                $biling_period = esc_html__('Months', 'wpresidence-core');
                break;
            case 'year':
                $biling_period = esc_html__('Year', 'wpresidence-core');
                break;
            case 'years':
                $biling_period = esc_html__('Years', 'wpresidence-core');
                break;
        }






        if (intval($pack_image_included) == 0) {
            $pack_image_included = esc_html__('Unlimited', 'wpresidence-core');
        }


        $pack_list = get_post_meta($package_id, 'pack_listings', true);
        $unlimited_listings = get_post_meta($package_id, 'mem_list_unl', true);
        if ($unlimited_listings == 1) {
            $unlimited_listings_sh = '<div><strong>' . esc_html__('Unlimited', 'wpresidence-core') . ' </strong> ' . esc_html__('Listings', 'wpresidence-core') . ' </div>';
        } else {
            $unlimited_listings_sh = '<div>' .sprintf(_n( '<strong> %s</strong> Listing', '<strong>%s</strong> Listings', $pack_list, 'wpresidence-core' ),number_format_i18n($pack_list)). ' </div>';
        }

        $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
        $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));

        $link = wpestate_get_template_link('page-templates/user_dashboard_profile.php');
        $link = add_query_arg('packet', $package_id, $link);
        $return_string .= '<div class="membership_package_product ' . $pack_featured_sh . '">'
                . '<div class="pack-price_title"><h4>' . get_the_title($package_id) . '</h4></div>'
                . '<div class="pack-price_sh">' . wpestate_show_price_floor($pack_price, $wpestate_currency, $where_currency, 1) . '</div>'
                . '<div class="pack_content">' . $package_content . '</div>'
                . '<div class="pack-bill_freg_sh"><strong>' . $billing_freq . '</strong> ' . $biling_period . '</div>'
                . '<div class="pack-listing_sh"> ' . $unlimited_listings_sh . '</div>'
                . '<div class="pack-listing-period_sh"><strong> ' . $pack_image_included . '</strong>  ' . esc_html__('Images / listing', 'wpresidence-core') . '</div> '
                . '<div class="pack-listing_feat_sh">'. sprintf( _n( '<strong> %s</strong> Featured Listing', '<strong>%s</strong> Featured Listings', $pack_featured, 'wpresidence-core' ),number_format_i18n($pack_featured)). '</div> '
                . '<div class="buy_package_sh"><a href="' . $link . '" class="wpresidence_button';
        if ($pack_featured_sh == '') {
            $return_string .= ' wpresidence_button_inverse ';
        }
        $return_string .= '">' . esc_html__('Get started', 'wpresidence-core') . '</a></div>'
                . '</div>';

        return '<div class="">' . $return_string . '</div>';
    }

endif; //end memerbership packages function









/**
 * Featured Agency/Developer Shortcode for WpResidence Theme
 *
 * This file contains the wpestate_featured_user_role_shortcode function, which generates
 * a Featured Agency/Developer Shortcode for display within the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Shortcodes
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core
 * - WpResidence theme functions and templates
 *
 * Usage:
 * [featured_user_role user_role_id="123" status="Featured" user_shortcode_imagelink="http://example.com/image.jpg"]
 */

 if (!function_exists('wpestate_featured_agency_developer')):
    /**
     * Generate HTML for a featured user role card
     *
     * @param array  $atts    Shortcode attributes
     * @param string $content Shortcode content (not used in this function)
     * @return string HTML content of the featured user role card
     */
    function wpestate_featured_agency_developer($atts, $content = null) {
        $attributes = shortcode_atts(
            array(
                'user_role_id' => '',
                'status' => '',
                'user_shortcode_imagelink' => ''
            ),
            $atts
        );

        // Sanitize and validate inputs
        $realtor_id = intval($attributes['user_role_id']);
        $status = sanitize_text_field($attributes['status']);
        $user_shortcode_imagelink = esc_url($attributes['user_shortcode_imagelink']);

    

        // Determine user role and fetch contact details
        $user_id = get_post_meta($realtor_id, 'user_meda_id', true);

        $is_agency = (is_wpresidence_agency() || get_post_type($realtor_id) == 'estate_agency');

        $phone_meta_key = $is_agency ? 'agency_phone' : 'developer_phone';
        $email_meta_key = $is_agency ? 'agency_email' : 'developer_email';

        $phone = get_post_meta($realtor_id, $phone_meta_key, true);
        $email = get_post_meta($realtor_id, $email_meta_key, true);

        // Prepare data for template
        $template_data = array(
            'status' => $status,
            'thumbnail_url' => wp_get_attachment_thumb_url(get_post_thumbnail_id($realtor_id)),
            'permalink' => get_permalink($realtor_id),
            'title' => get_the_title($realtor_id),
            'phone' => $phone,
            'email' => $email,
            'content' => wpestate_strip_excerpt_by_char(get_the_excerpt( $realtor_id ), 180, $realtor_id),
            'user_shortcode_imagelink' => $user_shortcode_imagelink
        );

        // Load and return the template
        ob_start();
        load_template(get_template_directory() . '/templates/agency__developers_cards_templates/featured_agency_developer.php', false, $template_data);
        return ob_get_clean();
    }
endif; // end featured user role function













if (!function_exists('wpestate_page_details_sh')):

    function wpestate_page_details_sh($post_id) {

        $return_array = array();

        if ($post_id != '' && !is_home() && !is_tax()) {
            $sidebar_name = esc_html(get_post_meta($post_id, 'sidebar_select', true));
            $sidebar_status = esc_html(get_post_meta($post_id, 'sidebar_option', true));
        } else {
            $sidebar_name = esc_html(wpresidence_get_option('wp_estate_blog_sidebar_name', ''));
            $sidebar_status = esc_html(wpresidence_get_option('wp_estate_blog_sidebar', ''));
        }

        if ('estate_agent' == get_post_type() && $sidebar_name == '' & $sidebar_status == '') {
            $sidebar_status = esc_html(wpresidence_get_option('wp_estate_agent_sidebar', ''));
            $sidebar_name = esc_html(wpresidence_get_option('wp_estate_agent_sidebar_name', ''));
        }

        if ($post_id != '') {
            if ('estate_property' == get_post_type() && ($sidebar_status == '' || $sidebar_status == 'global' )) {
                $sidebar_status = esc_html(wpresidence_get_option('wp_estate_property_sidebar', ''));
                $sidebar_name = esc_html(wpresidence_get_option('wp_estate_property_sidebar_name', ''));
            }
        }


        if ('' == $sidebar_name) {
            $sidebar_name = 'primary-widget-area';
        }
        if ('' == $sidebar_status) {
            $sidebar_status = 'right';
        }



        if ('left' == $sidebar_status) {
            $return_array['content_class'] = 'col-md-9 col-md-push-3 rightmargin';
            $return_array['sidebar_class'] = 'col-md-3 col-md-pull-9 ';
        } else if ($sidebar_status == 'right') {
            $return_array['content_class'] = 'col-md-9 rightmargin';
            $return_array['sidebar_class'] = 'col-md-3';
        } else {
            $return_array['content_class'] = 'col-md-12';
            $return_array['sidebar_class'] = 'none';
        }

        $return_array['sidebar_name'] = $sidebar_name;

        return $return_array;
    }

endif; // end   wpestate_page_details
?>
