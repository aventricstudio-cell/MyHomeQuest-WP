<?php
/**
 * WpResidence Filter List Properties Function
 *
 * This file contains the function to generate a filtered list of properties
 * with a filter bar for the WpResidence theme. It handles arrays of IDs for taxonomies.
 *
 * @package WpResidence
 * @subpackage Shortcodes
 * @since 1.0.0
 *
 * @dependencies 
 * - wpestate_return_first_term()
 * - wpestate_return_filter_data()
 * - wpestate_filter_bar()
 * - wpestate_shortcode_build_list()
 *
 * Usage: [filter_list_properties category_ids="1,2,3" action_ids="4,5,6" city_ids="7,8,9" status_ids="10,11" features_ids="12,13" number="6" rownumber="3"]
 */

if (!function_exists("wpestate_filter_list_properties")):
function wpestate_filter_list_properties($attributes, $content = null) {
    // Extract and process taxonomy IDs
 
     if (is_tax()) {
        $current_term = get_queried_object();
        if ($current_term && isset($current_term->taxonomy)) {
            switch ($current_term->taxonomy) {
                case 'property_city':
                    $attributes['city_ids'] = isset($attributes['city_ids']) 
                        ? $attributes['city_ids'] . ',' . $current_term->term_id 
                        : $current_term->term_id;
                    break;
                case 'property_area':
                    $attributes['area_ids'] = isset($attributes['area_ids']) 
                        ? $attributes['area_ids'] . ',' . $current_term->term_id 
                        : $current_term->term_id;
                    break;
                case 'property_category':
                    $attributes['category_ids'] = isset($attributes['category_ids']) 
                        ? $attributes['category_ids'] . ',' . $current_term->term_id 
                        : $current_term->term_id;
                    break;
                case 'property_action_category':
                    $attributes['action_ids'] = isset($attributes['action_ids']) 
                        ? $attributes['action_ids'] . ',' . $current_term->term_id 
                        : $current_term->term_id;
                    break;
                case 'property_county_state':
                    $attributes['state_ids'] = isset($attributes['state_ids'])
                        ? $attributes['state_ids'] . ',' . $current_term->term_id
                        : $current_term->term_id;
                    break;
                case 'property_status':
                    $attributes['status_ids'] = isset($attributes['status_ids'])
                        ? $attributes['status_ids'] . ',' . $current_term->term_id
                        : $current_term->term_id;
                    break;
                case 'property_features':
                    $attributes['features_ids'] = isset($attributes['features_ids'])
                        ? $attributes['features_ids'] . ',' . $current_term->term_id
                        : $current_term->term_id;
                    break;

                 case 'property_features':
                    $attributes['features_ids'] = isset($attributes['features_ids']) 
                        ? $attributes['features_ids'] . ',' . $current_term->term_id 
                        : $current_term->term_id;
                    break;  
                    
                    
                 case 'property_status ':
                    $attributes['status_ids'] = isset($attributes['status_ids']) 
                        ? $attributes['status_ids'] . ',' . $current_term->term_id 
                        : $current_term->term_id;
                    break; 
            }
        }
    }

 



    $category_ids = isset($attributes['category_ids']) ? $attributes['category_ids'] : '';
    $action_ids = isset($attributes['action_ids']) ? $attributes['action_ids'] : '';
    $city_ids = isset($attributes['city_ids']) ? $attributes['city_ids'] : '';
    $area_ids = isset($attributes['area_ids']) ? $attributes['area_ids'] : '';
    $state_ids = isset($attributes['state_ids']) ? $attributes['state_ids'] : '';
    $status_ids = isset($attributes['status_ids']) ? $attributes['status_ids'] : '';
    $features_ids = isset($attributes['features_ids']) ? $attributes['features_ids'] : '';
    
    // Extract other attributes
    $sort_by = isset($attributes['sort_by']) ? $attributes['sort_by'] : '';
    $align = isset($attributes['align']) ? $attributes['align'] : '';
    $card_version = isset($attributes['card_version']) ? $attributes['card_version'] : '';
    $context = isset($attributes['context']) ? $attributes['context'] : '';





    // Get first terms for each taxonomy
    $city = wpestate_return_first_term($city_ids, 'property_city');
    $area = wpestate_return_first_term($area_ids, 'property_area');
    $category = wpestate_return_first_term($category_ids, 'property_category');
    $types = wpestate_return_first_term($action_ids, 'property_action_category');
    $county = wpestate_return_first_term($state_ids, 'property_county_state');
    $status = wpestate_return_first_term($status_ids, 'property_status');
    $features = wpestate_return_first_term($features_ids, 'property_features');

    // Build filter selection array
    $filter_selection = array(
        'city' => $city,
        'area' => $area,
        'types' => $types,
        'category' => $category,
        'county' => $county,
        'status' => $status,
        'features' => $features,
        'sort_by' => $sort_by
    );

    // Get filter data
    $filter_data = wpestate_return_filter_data($filter_selection);
    $filter_data['sort_by'] = $sort_by;
    $filter_data['listing_filter'] = 0;

    // Generate filter bar
   
    $return_string = wpestate_filter_bar($filter_data);

    // Set type for shortcode
    $attributes['type'] = 'properties';



    // Build wrapper for property list
    $return_string .= '<div class="wpestate_filter_list_properties_wrapper row" 
                            data-ishortcode="1" 
                            data-context="'.esc_attr($context).'"
                            data-number="' . esc_attr($attributes['number']) . '"  
                            data-rownumber="' . esc_attr($attributes['rownumber']) . '" 
                            data-card_version="' . esc_attr($card_version) . '" 
                            data-align="' . esc_attr($align) . '">';
    $return_string .= wpestate_shortcode_build_list($attributes);
    $return_string .= '<div class="spinner" id="listing_loader2">
                           <div class="new_prelader"></div>
                       </div>';
    $return_string .= '</div>';

    return $return_string;
}
endif;




/**
 * WpResidence Shortcode Build List Function
 *
 * This file contains the function to build and display a filtered list of properties
 * for the WpResidence theme's filter list properties shortcode.
 *
 * @package WpResidence
 * @subpackage Shortcodes
 * @since 1.0.0
 *
 * @dependencies 
 * - wpestate_prepare_arguments_shortcode()
 * - wpestate_recent_posts_shortocodes_create_arg()
 * - wpestate_add_global_details_transient()
 * - wpestate_request_transient_cache()
 * - wpestate_return_filtered_query()
 * - wpresidence_display_property_list_as_html()
 * - wpestate_set_transient_cache()
 * - wpestate_html_compress()
 *
 * Usage: [filter_list_properties category="residential" action="sale" city="New York" status_ids="10,11" features_ids="12,13" number="6" rownumber="3"]
 */

 if (!function_exists('wpestate_shortcode_build_list')):
    function wpestate_shortcode_build_list($attributes) {
        // Initialize variables.
        $return_string = '';

        // Extract and sanitize shortcode attributes.
        $order              = isset( $attributes['sort_by'] )            ? intval( $attributes['sort_by'] )                          : 0;
        $show_featured_only = isset( $attributes['show_featured_only'] ) ? sanitize_text_field( $attributes['show_featured_only'] )  : '';
        $featured_first     = isset( $attributes['featured_first'] )     ? sanitize_text_field( $attributes['featured_first'] )       : 'no';
        $random_pick        = isset( $attributes['$random_pick'] )       ? sanitize_text_field( $attributes['$random_pick'] )         : '';
        $post_number_total  = isset( $attributes['number'] )             ? intval( $attributes['number'] )                            : 0;
        $row_number         = isset( $attributes['rownumber'] )          ? intval( $attributes['rownumber'] )                         : 0;

        // ── Build 2026 params directly from shortcode attributes ──
        $params = array();

        // Map shortcode taxonomy attrs to 2026 canonical param keys (all term IDs).
        $taxonomy_map = array(
            'category_ids' => 'property_category',
            'action_ids'   => 'property_action_category',
            'city_ids'     => 'property_city',
            'area_ids'     => 'property_area',
            'state_ids'    => 'property_county_state',
            'status_ids'   => 'property_status',
            'features_ids' => 'property_features',
        );
        foreach ( $taxonomy_map as $attr_key => $param_key ) {
            if ( ! empty( $attributes[ $attr_key ] ) ) {
                $ids = is_array( $attributes[ $attr_key ] )
                    ? $attributes[ $attr_key ]
                    : array_map( 'trim', explode( ',', $attributes[ $attr_key ] ) );
                // Filter out empty values.
                $ids = array_filter( $ids, function( $v ) { return '' !== $v; } );
                if ( ! empty( $ids ) ) {
                    $params[ $param_key ] = array_map( 'intval', $ids );
                }
            }
        }

        // Ordering — random pick overrides sort_by.
        $params['order'] = ( 'yes' === $random_pick ) ? 99 : $order;

        // Posts per page.
        $params['posts_per_page'] = $post_number_total;

        // Featured-only filter via meta_query.
        if ( 'yes' === $show_featured_only ) {
            $params['meta_query']   = isset( $params['meta_query'] ) ? $params['meta_query'] : array();
            $params['meta_query'][] = array(
                'key'     => 'prop_featured',
                'value'   => 1,
                'compare' => '=',
            );
        }

        // Agent filter via meta_query.
        if ( ! empty( $attributes['agentid'] ) ) {
            $agent_ids = array_map( 'trim', explode( ',', $attributes['agentid'] ) );
            $params['meta_query']   = isset( $params['meta_query'] ) ? $params['meta_query'] : array();
            $params['meta_query'][] = array(
                'key'     => 'property_agent',
                'value'   => count( $agent_ids ) === 1 ? $agent_ids[0] : $agent_ids,
                'compare' => count( $agent_ids ) === 1 ? '=' : 'IN',
            );
        }

        // ── Transient cache ──
        // Build taxonomy terms string for transient name (preserves existing cache key shape).
        $taxonomy_terms = array(
            'category' => isset( $attributes['category_ids'] ) ? sanitize_text_field( $attributes['category_ids'] ) : '',
            'action'   => isset( $attributes['action_ids'] )   ? sanitize_text_field( $attributes['action_ids'] )   : '',
            'city'     => isset( $attributes['city_ids'] )     ? sanitize_text_field( $attributes['city_ids'] )      : '',
            'area'     => isset( $attributes['area_ids'] )     ? sanitize_text_field( $attributes['area_ids'] )      : '',
            'state'    => isset( $attributes['state_ids'] )    ? sanitize_text_field( $attributes['state_ids'] )     : '',
            'status'   => isset( $attributes['status_ids'] )   ? sanitize_text_field( $attributes['status_ids'] )    : '',
            'features' => isset( $attributes['features_ids'] ) ? sanitize_text_field( $attributes['features_ids'] )  : '',
        );
        $transient_name = 'wpestate_properties_list_filter_query_estate_property_' .
                          implode( '_', $taxonomy_terms ) . '_' . $row_number . '_' .
                          $post_number_total . '_' . $featured_first;
        $transient_name = wpestate_add_global_details_transient( $transient_name );

        // Try to get cached template.
        $templates = false;
        if ( function_exists( 'wpestate_request_transient_cache' ) ) {
            $templates = wpestate_request_transient_cache( $transient_name );
        }

        // If no cached template or random pick is requested, generate new content.
        if ( false === $templates || 'yes' === $random_pick ) {
            // Run query through the 2026 layer (handles sold exclusion, featured
            // priority, ordering, and pagination internally).
            $result       = wpestate_query_2026_property( 'directory', $params );
            $recent_posts = $result['query'];

            ob_start();
            $pagination_parameters = array(
                'display' => 'yes',
                'order'   => $order,
                'paged'   => 1,
            );
            wpresidence_display_property_list_as_html( $recent_posts, '', 'ajax_list_shortcode', $attributes, $pagination_parameters );
            $templates = ob_get_contents();
            ob_end_clean();

            // Cache the generated content if not using random order.
            if ( 99 !== $params['order'] && function_exists( 'wpestate_set_transient_cache' ) ) {
                wpestate_set_transient_cache( $transient_name, wpestate_html_compress( $templates ), 60 * 60 * 4 );
            }

            wp_reset_query();
        }

        // Append templates to return string.
        $return_string .= $templates;

        return $return_string;
    }
endif;