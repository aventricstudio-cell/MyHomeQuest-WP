<?php
/**
 * Property List Shortcode for WpResidence Theme
 *
 * This file contains the main function for generating a property list shortcode output.
 * It's part of the WpResidence theme's custom shortcode system for displaying
 * property listings, agents, or blog posts in various layouts.
 *
 * @package WpResidence
 * @subpackage Shortcodes
 * @since 1.0.0
 *
 * @uses wpestate_prepare_arguments_shortcode()
 * @uses wpestate_recent_posts_shortocodes_create_arg()
 * @uses wpestate_return_favorite_listings_per_user()
 * @uses wpestate_return_unit_class()
 * @uses wpestate_shortocode_return_column()
 */

/**
 * Generate property list shortcode output
 * 
 * @param array $attributes Shortcode attributes
 * @param string $content Shortcode content (unused)
 * @return string Generated HTML for the property list
 */

if( !function_exists('wpestate_recent_posts_pictures_new') ):
function wpestate_recent_posts_pictures_new($attributes, $content = null) {



    // Define default attributes and merge with user-provided attributes
    $default_attributes = array(
        'title' => '',
        'type' => 'properties',
        'category_ids' => '',
        'action_ids' => '',
        'city_ids' => '',
        'area_ids' => '',
        'state_ids' => '',
        'status_ids' => '',
        'features_ids' => '',
        'number' => 4,
        'rownumber' => 4,
        'align' => 'vertical',
        'control_terms_id' => '',
        'form_fields' => '',
        'show_featured_only' => 'no',
        'random_pick' => 'no',
        'featured_first' => 'no',
        'sort_by' => 0,
        'card_version' => '',
        'only_favorites' => '',
        'display_grid' => 'no',
        'agentid' => '',
        'topbar_select_model'=>''
    );
    $attributes = shortcode_atts($default_attributes, $attributes);

    // Keep shortcode_arguments for UI elements (taxonomy strings for data attributes,
    // transient name). Agents/blog branches still use the legacy query path.
    $shortcode_arguments = wpestate_prepare_arguments_shortcode($attributes);

    // Set up display variables
    $return_string = '';
    $button = '';
    $class = '';
    $orderby = 'meta_value';

    // Determine post type and adjust query if necessary
    $type = ($attributes['type'] == 'properties') ? 'estate_property' : (($attributes['type'] == 'agents') ? 'agents' : 'post');

    if ($attributes['random_pick'] === 'yes') {
        $orderby = 'rand';
    }

    // Handle favorites display
    $curent_fav        = wpestate_return_favorite_listings_per_user();
    $display_favorites = '';
    $args              = array();
    $params_2026       = null;

    if ($attributes['only_favorites'] == 'yes') {
        // Favorites — direct WP_Query with post__in, no 2026 layer.
        $args = array(
            'post_type'      => 'estate_property',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'post__in'       => empty($curent_fav) ? array(0) : $curent_fav,
            'orderby'        => 'ID',
        );
        $display_favorites = 'yes';
        $class .= " front-end-favorite-wrapper ";
    } elseif ($type == 'estate_property') {
        // ── Build 2026 params directly from shortcode attributes ──
        $params_2026 = array();

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
                $ids = array_filter( $ids, function( $v ) { return '' !== $v; } );
                if ( ! empty( $ids ) ) {
                    $params_2026[ $param_key ] = array_map( 'intval', $ids );
                }
            }
        }

        // Ordering — random pick overrides sort_by.
        $params_2026['order'] = ( 'yes' === $attributes['random_pick'] ) ? 99 : intval( $attributes['sort_by'] );

        // Posts per page.
        $params_2026['posts_per_page'] = intval( $attributes['number'] );

        // Featured-only filter via meta_query.
        if ( 'yes' === $attributes['show_featured_only'] ) {
            $params_2026['meta_query']   = isset( $params_2026['meta_query'] ) ? $params_2026['meta_query'] : array();
            $params_2026['meta_query'][] = array(
                'key'     => 'prop_featured',
                'value'   => 1,
                'compare' => '=',
            );
        }

        // Agent filter via meta_query.
        if ( ! empty( $attributes['agentid'] ) ) {
            $agent_ids = array_map( 'trim', explode( ',', $attributes['agentid'] ) );
            $params_2026['meta_query']   = isset( $params_2026['meta_query'] ) ? $params_2026['meta_query'] : array();
            $params_2026['meta_query'][] = array(
                'key'     => 'property_agent',
                'value'   => count( $agent_ids ) === 1 ? $agent_ids[0] : $agent_ids,
                'compare' => count( $agent_ids ) === 1 ? '=' : 'IN',
            );
        }
    } else {
        // Agents/blog — legacy query path.
        $args = wpestate_recent_posts_shortocodes_create_arg($shortcode_arguments);
    }

    // Prepare 'load more' button
    if ($display_favorites == '') {
        $button_text = ($type == 'estate_property') ? esc_html__('load more listings', 'wpresidence-core') :
                       (($type == 'agents') ? esc_html__('load agents', 'wpresidence-core') : esc_html__('load articles', 'wpresidence-core'));
        $button = sprintf('<div class="listinglink-wrapper_sh_listings exclude-rtl"><span class="wpresidence_button wpestate_item_list_sh">%s</span></div>', $button_text);
   
    }

    // Prepare taxonomy strings
    $taxonomy_types = array('category', 'action', 'city', 'area', 'state', 'status', 'features');
    foreach ($taxonomy_types as $tax_type) {
        ${$tax_type} = !empty($shortcode_arguments[$tax_type]) ? rtrim($shortcode_arguments[$tax_type], ',') : '';
    }
    
    // agent id
    if (isset($attributes['agentid'])) {
        $agentid = $attributes['agentid'];
    }
    // Process agent IDs
    $agent_ids=array();
    if($attributes['agentid']!=''){
        $agent_ids = array_map('trim', explode(',', $attributes['agentid']));
    }


    // Add grid class if necessary
    if ($attributes['display_grid'] == 'yes') {
        $class .= ' wpestate_grid_view_wrapper ';
    }

    // Generate unique ID for the container
    $container_id = 'wpestate_sh_anime_' . wp_rand(1, 999);

    // Build the main container HTML
    $return_string .= sprintf(
        '<div id="%s" class="wpresidence_shortcode_listings_wrapper wpestate_anime wpestate_latest_listings_sh bottom-%s %s" data-type="%s" data-category_ids="%s" data-action_ids="%s" data-city_ids="%s"  data-area_ids="%s" data-state_ids="%s" data-status_ids="%s" data-features_ids="%s" data-selected-category_ids="" data-selected-action_ids="" data-selected-city_ids="" data-selected-area_ids="" data-selected-state_ids="" data-selected-status_ids="" data-selected-features_ids="" data-number="%s" data-row-number="%s" data-card-version="%s" data-align="%s" data-show_featured_only="%s" data-random_pick="%s" data-featured_first="%s" data-sort-by="%s" 
        data-display-grid="%s" data-agent-id="%s" data-topbar_select_model="%s"   data-page="1">',
        esc_attr($container_id),
        esc_attr($type),
        esc_attr($class),
        esc_attr($type),
        esc_attr($category),
        esc_attr($action),
        esc_attr($city),
        esc_attr($area),
        esc_attr($state),
        esc_attr($status),
        esc_attr($features),
        esc_attr($attributes['number']),
        esc_attr($attributes['rownumber']),
        esc_attr($attributes['card_version']),
        esc_attr($attributes['align']),
        esc_attr($attributes['show_featured_only']),
        esc_attr($attributes['random_pick']),
        esc_attr($attributes['featured_first']),
        esc_attr($attributes['sort_by']),
        esc_attr($attributes['display_grid']),
        esc_attr($attributes['agentid']),
        esc_attr($attributes['topbar_select_model'])
    );

    // Add title if provided
    if (!empty($attributes['title'])) {
        $return_string .= sprintf('<h2 class="shortcode_title">%s</h2>', esc_html($attributes['title']));
    }

    // Add taxonomy controls if provided
    if (!empty($attributes['control_terms_id'])) {
        $return_string .= wpestate_add_taxonomy_controls($attributes['control_terms_id']);
    }

    // Add form fields if provided
    if (!empty($attributes['form_fields']) && is_array($attributes['form_fields'])) {
        $return_string .= wpestate_add_form_field_controls($attributes);
    }

    // Prepare items wrapper
    $wrapper_class = $attributes['display_grid'] == 'yes' ? 'items_shortcode_wrapper_grid' : 'items_shortcode_wrapper row';
    $return_string .= sprintf('<div class="%s" data-row-number="'.esc_attr($attributes['rownumber']).'">', esc_attr($wrapper_class));

    // Generate transient name for caching
    $transient_name = wpestate_generate_transient_name(
        'wpestate_recent_posts_pictures_query',
        $attributes,
        $type,
        $category,
        $action,
        $city,
        $area,
        $state
    );

    // Attempt to retrieve cached content
    $templates = wpestate_get_cached_content($transient_name);
    $templates === false ;
    // Generate content if cache is empty or conditions require fresh content
    if ($templates === false || $attributes['random_pick'] == 'yes' || $display_favorites == 'yes') {
        $templates = wpestate_generate_listing_content($args, $attributes, $type, $display_favorites, $params_2026);
        
        // Cache the generated content if applicable
        if ($orderby !== 'rand' && function_exists('wpestate_set_transient_cache')) {
           // wpestate_set_transient_cache($transient_name, wpestate_html_compress($templates), 60 * 60 * 4);
        }
    }

    // Append generated content and close containers
    $return_string .= $templates;
    $return_string .= '</div>';
    $return_string .= '<div class="wpestate_listing_sh_loader"><div class="new_prelader"></div></div>';
    $return_string .= $button;
    $return_string .= '</div>';

    // Reset query and global variables
    wp_reset_query();
    $is_shortcode = 0;

    return $return_string;
}
endif;




/**
 * Generate transient name for caching
 *
 * @param string $prefix_name Transient name prefix
 * @param array $attributes Shortcode attributes
 * @param string $type Post type
 * @param string $category Category string
 * @param string $action Action string
 * @param string $city City string
 * @param string $area Area string
 * @param string $state State string
 * @return string Transient name
 */
function wpestate_generate_transient_name($prefix_name, $attributes, $type, $category, $action, $city, $area, $state) {
    $transient_name = sprintf(
        '%s_%s_%s_%s_%s_%s_%s_%s_%s_%s_%s_%s',
        $prefix_name,
        $type,
        $category,
        $action,
        $city,
        $area,
        $state,
        isset($attributes['row_number']) ? $attributes['row_number'] : '',
        isset($attributes['number']) ? $attributes['number'] : '',
        isset($attributes['featured_first']) ? $attributes['featured_first'] : '',
        isset($attributes['align']) ? $attributes['align'] : '',
        isset($attributes['random_pick']) ? $attributes['random_pick'] : ''
    );
    return wpestate_add_global_details_transient($transient_name);
}



/**
 * Retrieve cached content
 *
 * @param string $transient_name Name of the transient
 * @return mixed Cached content or false if not found
 */
function wpestate_get_cached_content($transient_name) {
    return function_exists('wpestate_request_transient_cache') ? wpestate_request_transient_cache($transient_name) : false;
}

/**
 * Generate listing content
 *
 * @param array $args Query arguments
 * @param array $attributes Shortcode attributes
 * @param string $type Post type
 * @param string $display_favorites Display favorites flag
 * @return string Generated HTML content
 */
function wpestate_generate_listing_content($args, $attributes, $type, $display_favorites, $params_2026 = null) {
    ob_start();
error_log('wpestate_generate_listing_content');
    if ( $type == 'estate_property' && $display_favorites == '' && $params_2026 !== null ) {
        // 2026 query path — handles sold exclusion, featured priority, ordering internally.
        $result       = wpestate_query_2026_property( 'directory', $params_2026 );
        $recent_posts = $result['query'];
    } elseif ( $type == 'estate_property' && $display_favorites == '' ) {
        $recent_posts = wpestate_return_filtered_query($args, $attributes['featured_first']);
    } else {
        $recent_posts = new WP_Query($args);
    }
   
  

    if($type == 'estate_property'){
        //display property list as html
        wpresidence_display_property_list_as_html($recent_posts,array()  ,'shortcode_list',$attributes);
    }
    else if ($type == 'agents') {
        //display agent list as html
        wpresidence_display_agent_list_as_html($recent_posts, 'estate_agent', array() ,'shortcode',$attributes);
             
    } else{
        //display blog list as html
        wpresidence_display_blog_list_as_html($recent_posts, array(), $context = 'shortcode',$attributes);
  
    }

    return ob_get_clean();
}

/**
 * Add taxonomy controls
 *
 * @param string $control_terms_id Comma-separated list of term IDs
 * @return string HTML for taxonomy controls
 */
function wpestate_add_taxonomy_controls($control_terms_id) {
    $control_taxonomy_array = explode(',', $control_terms_id);
    $output = '<div class="control_tax_wrapper">';

    foreach ($control_taxonomy_array as $term_name) {
        $term_data = get_term($term_name);
        if (isset($term_data->term_id)) {
            $output .= sprintf(
                '<div class="control_tax_sh" data-taxid="%d" data-taxonomy="%s">%s</div>',
                $term_data->term_id,
                esc_attr($term_data->taxonomy),
                esc_html($term_data->name)
            );
        }
    }

    $output .= '</div>';
    return $output;
}

/**
 * Add form field controls
 *
 * @param array $attributes Shortcode attributes
 * @return string HTML for form field controls
 */
function wpestate_add_form_field_controls($attributes) {
    // Step 1: Start the top-bar controls wrapper.
    $output = '<div class="control_tax_wrapper">';

    // Step 2: Render each configured top-bar term as an unselected control.
    foreach ($attributes['form_fields'] as $field) {
        $term_data = get_term($field['field_type']);
        if (isset($term_data->term_id)) {
            $output .= sprintf(
                '<div class="control_tax_sh" data-taxid="%d" data-taxonomy="%s">',
                $term_data->term_id,
                esc_attr($term_data->taxonomy)
            );

            // Step 3: Render the configured icon when one exists.
            if (isset($field['icon']) && $field['icon'] != '') {
                ob_start();
                \Elementor\Icons_Manager::render_icon($field['icon'], ['aria-hidden' => 'true']);
                $item_icon = ob_get_clean();
                $output .= $item_icon;
            }

            // Step 4: Render the visible filter label.
            $output .= esc_html($field['field_label']) . '</div>';
        }
    }

    // Step 5: Close the controls wrapper and return it.
    $output .= '</div>';
    return $output;
}

/**
 * Check if a taxonomy item is active
 *
 * @param array $attributes Shortcode attributes
 * @param int $term_id Term ID to check
 * @return string CSS class if the item is active
 */
function wpestate_topbar_sh_is_item_active($attributes, $term_id) {
    $term_id_string = strval($term_id);
    $taxonomy_fields = ['category_ids', 'action_ids', 'city_ids', 'area_ids', 'state_ids', 'status_ids', 'features_ids'];

    foreach ($taxonomy_fields as $field) {
        if (strpos(strval($attributes[$field]), $term_id_string) !== false) {
            return 'tax_active';
        }
    }

    return '';
}


