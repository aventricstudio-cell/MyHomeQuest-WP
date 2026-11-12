<?php 

/*
*
*  Ahac handler for display listings
*
*
*
*
*/

add_action( $handler.'_nopriv_wpestate_load_recent_items_sh', 'wpestate_load_recent_items_sh' );
add_action( $handler.'_wpestate_load_recent_items_sh', 'wpestate_load_recent_items_sh' );

if( !function_exists('wpestate_sanitize_ajax_taxonomy_values') ):
    /**
     * Sanitize AJAX taxonomy values for shortcode filtering.
     *
     * Reads a taxonomy key from $_POST, normalizes scalar/array input into
     * an array, sanitizes each value, and removes empty/all placeholders.
     *
     * @param string $post_key AJAX payload key.
     *
     * @return array Clean taxonomy values.
     */
    function wpestate_sanitize_ajax_taxonomy_values( $post_key ) {
        // Step 1: Start with an empty result list.
        $clean_values = array();

        // Step 2: Exit early when key is missing.
        if ( ! isset( $_POST[ $post_key ] ) ) {
            return $clean_values;
        }

        // Step 3: Normalize scalar input to array input.
        $raw_values = $_POST[ $post_key ];
        if ( ! is_array( $raw_values ) ) {
            $raw_values = array( $raw_values );
        }

        // Step 4: Sanitize each value and skip placeholders.
        foreach ( $raw_values as $raw_value ) {
            $sanitized_value = sanitize_text_field( wp_unslash( $raw_value ) );
            if ( '' === $sanitized_value || 'all' === strtolower( $sanitized_value ) ) {
                continue;
            }
            $clean_values[] = $sanitized_value;
        }

        return $clean_values;
    }
endif;


/**
 * Extract taxonomy params from $_POST into a 2026-layer $params array.
 *
 * Reads each taxonomy key from $_POST, sanitizes and unslashes values,
 * filters out empty strings and the 'all' placeholder. Arrays stay as
 * arrays; scalars stay as scalars (the 2026 layer accepts both).
 *
 * @param array  &$params       2026 params array — taxonomy keys are added in-place.
 * @param array  $taxonomy_keys List of taxonomy POST keys to read (e.g. 'property_city').
 * @return void
 */
function wpestate_ajax_extract_taxonomy_params( &$params, $taxonomy_keys ) {
    // Loop through each expected taxonomy key in the POST payload.
    foreach ( $taxonomy_keys as $key ) {
        if ( ! isset( $_POST[ $key ] ) ) {
            continue;
        }
        $val = $_POST[ $key ];

        // Array values: sanitize + unslash each element, drop empties.
        if ( is_array( $val ) ) {
            $val = array_filter( array_map( 'sanitize_text_field', array_map( 'wp_unslash', $val ) ) );
            if ( ! empty( $val ) ) {
                $params[ $key ] = $val;
            }
        } else {
            // Scalar values: sanitize, unslash, skip 'all' placeholder.
            $sanitized = sanitize_text_field( wp_unslash( $val ) );
            if ( '' !== $sanitized && 'all' !== strtolower( $sanitized ) ) {
                $params[ $key ] = $sanitized;
            }
        }
    }
}


/**
 * Resolve a meta key from an AJAX payload to the actual wp_postmeta key.
 *
 * Standard property meta keys (ASCII, already contain underscores) are
 * returned as-is via sanitize_text_field(). Non-ASCII keys (e.g. Cyrillic
 * custom field names) are run through wpestate_normalize_custom_field_slug()
 * which applies sanitize_title() + sanitize_key() — matching how the theme
 * stores custom field slugs in wp_postmeta.
 *
 * @param string $raw_key  Raw meta key from the AJAX payload (already wp_unslash'd).
 * @return string          Resolved meta key matching wp_postmeta storage.
 */
function wpestate_ajax_resolve_meta_key( $raw_key ) {
    $raw_key = trim( (string) $raw_key );

    // If the key is pure ASCII (standard meta keys like property_bedrooms),
    // just sanitize and return — no slug transformation needed.
    if ( preg_match( '/^[a-zA-Z0-9_\-]+$/', $raw_key ) ) {
        return sanitize_text_field( $raw_key );
    }

    // Non-ASCII key: this is a custom field display name (e.g. "Остин").
    // Normalize it to match how the theme stores custom field meta keys.
    return wpestate_normalize_custom_field_slug( $raw_key );
}


/**
 * Extract meta_query clauses from $_POST into a 2026-layer $params array.
 *
 * Reads $_POST['meta_query'] (array of {key, value, compare} objects sent
 * by JS), sanitizes each clause, and adds type=NUMERIC for numeric
 * comparison operators so MySQL casts correctly.
 *
 * @param array &$params 2026 params array — 'meta_query' key is added in-place.
 * @return void
 */
function wpestate_ajax_extract_meta_query_params( &$params ) {
    // Operators that require NUMERIC type casting in WP_Query meta_query.
    $numeric_compare_ops = array( '>', '>=', '<', '<=', '=', '!=', 'BETWEEN', 'NOT BETWEEN' );

    if ( ! empty( $_POST['meta_query'] ) && is_array( $_POST['meta_query'] ) ) {
        $meta_clauses = array();

        // Sanitize each clause from the JS payload.
        foreach ( $_POST['meta_query'] as $clause ) {
            if ( ! is_array( $clause ) || empty( $clause['key'] ) ) {
                continue;
            }

            // 'keyword' is not a real meta key — it maps to WP search ('s' parameter).
            // Extract it as a 2026 keyword param instead of a meta_query clause.
            if ( 'keyword' === $clause['key'] && ! empty( $clause['value'] ) ) {
                $params['keyword'] = sanitize_text_field( wp_unslash( $clause['value'] ) );
                continue;
            }
            $compare = sanitize_text_field( $clause['compare'] ?? '=' );
            $compare = trim( $compare );

            // Normalize UI compare labels to WP_Query operators.
            // Example: "smaller" -> "<=", "greater" -> ">=".
            $compare_map = array(
                'equal'       => '=',
                'greater'     => '>=',
                'smaller'     => '<=',
                'like'        => 'LIKE',
                'date bigger' => '>=',
                'date smaller' => '<=',
            );
            $compare_lc = strtolower( $compare );
            if ( isset( $compare_map[ $compare_lc ] ) ) {
                $compare = $compare_map[ $compare_lc ];
            }
            if ( $compare === '' ) {
                $compare = '=';
            }

            $built   = array(
                'key'     => wpestate_ajax_resolve_meta_key( wp_unslash( $clause['key'] ) ),
                'value'   => sanitize_text_field( wp_unslash( (string) ( $clause['value'] ?? '' ) ) ),
                'compare' => $compare,
            );

            // Add NUMERIC type for numeric comparison operators.
            if ( in_array( $compare, $numeric_compare_ops, true ) ) {
                $built['type'] = 'NUMERIC';
            }
            $meta_clauses[] = $built;
        }

        if ( ! empty( $meta_clauses ) ) {
            $params['meta_query'] = $meta_clauses;
        }
    }
}


/**
 * Extract float params from $_POST into a 2026-layer $params array.
 *
 * Reads each key from $_POST, converts to float, and skips zero values.
 * Used for numeric range filters (price, beds, baths, size, lot_size,
 * rooms) and geo coordinates (geo_lat, geo_long, geo_rad).
 *
 * @param array &$params    2026 params array — float keys are added in-place.
 * @param array $float_keys List of POST keys to read as floats.
 * @return void
 */
function wpestate_ajax_extract_float_params( &$params, $float_keys ) {
    // Read each key, convert to float, skip zeros.
    foreach ( $float_keys as $key ) {
        if ( isset( $_POST[ $key ] ) && '' !== $_POST[ $key ] ) {
            $val = floatval( $_POST[ $key ] );
            if ( 0.0 !== $val ) {
                $params[ $key ] = $val;
            }
        }
    }
}


/**
 * Convert multi-currency prices from displayed currency back to base currency.
 *
 * Reads price_min and price_max from $_POST (using configurable key names),
 * applies the exchange rate from the user's cookie, and stores the
 * converted values into the 2026 $params array.
 *
 * @param array  &$params      2026 params array — 'price_min'/'price_max' added in-place.
 * @param string $post_key_min POST key for minimum price (default 'price_min').
 * @param string $post_key_max POST key for maximum price (default 'price_max').
 * @return void
 */
function wpestate_ajax_convert_multicurrency_prices( &$params, $post_key_min = 'price_min', $post_key_max = 'price_max' ) {
    // Read raw price values from POST.
    $price_min = isset( $_POST[ $post_key_min ] ) ? floatval( $_POST[ $post_key_min ] ) : 0;
    $price_max = isset( $_POST[ $post_key_max ] ) ? floatval( $_POST[ $post_key_max ] ) : 0;

    // Apply exchange rate from cookie if multi-currency is enabled.
    $multi_curr = wpresidence_get_option( 'wp_estate_multi_curr', '' );
    if (
        ! empty( $multi_curr )
        && isset( $_COOKIE['my_custom_curr'] )
        && isset( $_COOKIE['my_custom_curr_pos'] )
        && isset( $_COOKIE['my_custom_curr_symbol'] )
        && -1 != $_COOKIE['my_custom_curr_pos']
    ) {
        $curr_pos = intval( $_COOKIE['my_custom_curr_pos'] );
        if ( isset( $multi_curr[ $curr_pos ][2] ) && floatval( $multi_curr[ $curr_pos ][2] ) > 0 ) {
            $rate      = floatval( $multi_curr[ $curr_pos ][2] );
            $price_min = $price_min / $rate;
            $price_max = $price_max / $rate;
        }
    }

    // Store non-zero prices into params.
    if ( $price_min > 0 ) {
        $params['price_min'] = $price_min;
    }
    if ( $price_max > 0 ) {
        $params['price_max'] = $price_max;
    }
}


/**
 * Render property search result cards with scroll anchor and pagination.
 *
 * Wraps the standard ob_start → scrollhere → render → ob_get pattern
 * used by search AJAX handlers into a single call.
 *
 * @param WP_Query $query             The property query result.
 * @param array    $wpestate_options   Page display options.
 * @param int      $paged             Current page number.
 * @param string   $context           Render context (default 'ajax_search_list').
 * @return string Rendered HTML including scroll anchor and pagination.
 */
function wpestate_ajax_render_search_cards( $query, $wpestate_options, $paged, $context = 'ajax_search_list' ) {
    ob_start();

    // Scroll anchor used by JS to scroll to results after AJAX load.
    print '<span id="scrollhere"></span>';

    // Pagination parameters for the card renderer.
    $pagination_parameters = array(
        'display' => 'yes',
        'paged'   => $paged,
    );
    wpresidence_display_property_list_as_html(
        $query,
        $wpestate_options,
        $context,
        array(),
        $pagination_parameters
    );

    return ob_get_clean();
}


if( !function_exists('wpestate_load_recent_items_sh') ):
    /**
     * AJAX handler for shortcode "load more" listing requests.
     *
     * Accepts taxonomy filters from the frontend as arrays
     * (property_category, property_action_category, property_city,
     * property_area, property_county_state, property_status,
     * property_features), normalizes them, and forwards the prepared
     * shortcode arguments to the listing renderer.
     *
     * @return void Outputs JSON and exits.
     */
    function wpestate_load_recent_items_sh(){
       // check_ajax_referer( 'wpestate_ajax_filtering', 'security' );

        // Step 1: Normalize taxonomy arrays from AJAX payload.
        $category_values = wpestate_sanitize_ajax_taxonomy_values( 'property_category' );
        $action_values   = wpestate_sanitize_ajax_taxonomy_values( 'property_action_category' );
        $city_values     = wpestate_sanitize_ajax_taxonomy_values( 'property_city' );
        $area_values     = wpestate_sanitize_ajax_taxonomy_values( 'property_area' );
        $state_values    = wpestate_sanitize_ajax_taxonomy_values( 'property_county_state' );
        $status_values   = wpestate_sanitize_ajax_taxonomy_values( 'property_status' );
        $feature_values  = wpestate_sanitize_ajax_taxonomy_values( 'property_features' );

        // Step 2: Build shortcode arguments used by existing render/query code.
        $shortcode_arguments['type']               = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
        $shortcode_arguments['property_category']        = $category_values;
        $shortcode_arguments['property_action_category'] = $action_values;
        $shortcode_arguments['property_city']               = $city_values;
        $shortcode_arguments['property_area']               = $area_values;
        $shortcode_arguments['property_county_state']       = $state_values;
        $shortcode_arguments['property_status']             = $status_values;
        $shortcode_arguments['property_features']           = $feature_values;
        $shortcode_arguments['number']             = isset( $_POST['number'] ) ? sanitize_text_field( wp_unslash( $_POST['number'] ) ) : '';
        $shortcode_arguments['sort_by']            = isset( $_POST['sort_by'] ) ? sanitize_text_field( wp_unslash( $_POST['sort_by'] ) ) : '';
        $shortcode_arguments['show_featured_only'] = isset( $_POST['show_featured_only'] ) ? sanitize_text_field( wp_unslash( $_POST['show_featured_only'] ) ) : '';
        $shortcode_arguments['random_pick']        = isset( $_POST['random_pick'] ) ? sanitize_text_field( wp_unslash( $_POST['random_pick'] ) ) : '';
        $shortcode_arguments['row_number']         = isset( $_POST['row_number'] ) ? sanitize_text_field( wp_unslash( $_POST['row_number'] ) ) : '';
        $shortcode_arguments['align']              = isset( $_POST['align'] ) ? sanitize_text_field( wp_unslash( $_POST['align'] ) ) : '';
        $shortcode_arguments['featured_first']     = isset( $_POST['featured_first'] ) ? sanitize_text_field( wp_unslash( $_POST['featured_first'] ) ) : '';
        $shortcode_arguments['paged']              = isset( $_POST['page'] ) ? sanitize_text_field( wp_unslash( $_POST['page'] ) ) : '';
        $shortcode_arguments['card_version']       = isset( $_POST['card_version'] ) ? sanitize_text_field( wp_unslash( $_POST['card_version'] ) ) : '';
        $shortcode_arguments['display_grid']       = isset( $_POST['display_grid'] ) ? sanitize_text_field( wp_unslash( $_POST['display_grid'] ) ) : '';
        $shortcode_arguments['agentid']            = isset( $_POST['agentid'] ) ? sanitize_text_field( wp_unslash( $_POST['agentid'] ) ) : '';

        // Step 3: Keep separate integer page variable used by display helper.
        $page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;

        // Step 4: Render response markup and return JSON payload.
         $html_response = wpestate_display_listings_sh($page, $shortcode_arguments);
         $answer = array(
             'success' => true,
             'html' => $html_response
         );
         
        if (strpos($html_response, '<span class="no_results">') !== false) {
            $answer['no_results'] = true;
        }


        print json_encode($answer);
        die();

    }
endif;



/**
 * Build listings markup for shortcode pagination/filter updates.
 *
 * For estate_property type the query is delegated to wpestate_query_2026_property.
 * Agent and blog post types stay on the legacy WP_Query path.
 *
 * @param int|string $page                Kept for signature compatibility — not used.
 * @param array      $shortcode_arguments Shortcode configuration and filter options.
 * @return string Rendered listing HTML.
 */
function wpestate_display_listings_sh( $page, $shortcode_arguments ) {

    // Step 1: Keep backward compatibility between rownumber and row_number keys.
    $shortcode_arguments['rownumber'] = isset( $shortcode_arguments['row_number'] ) ? $shortcode_arguments['row_number'] : '';

    if ( $shortcode_arguments['type'] === 'estate_property' ) {

        // Step 2: Resolve order code — random_pick overrides sort_by.
        $order = intval( isset( $shortcode_arguments['sort_by'] ) ? $shortcode_arguments['sort_by'] : 0 );
        if ( isset( $shortcode_arguments['random_pick'] ) && $shortcode_arguments['random_pick'] === 'yes' ) {
            $order = 99;
        }

        // Step 3: Build 2026 params from shortcode arguments.
        // Taxonomy arrays already use full taxonomy names (set by wpestate_load_recent_items_sh).
        $params = array(
            'posts_per_page'           => intval( $shortcode_arguments['number'] ),
            'paged'                    => isset( $shortcode_arguments['paged'] ) ? intval( $shortcode_arguments['paged'] ) : 1,
            'order'                    => $order,
            'featured_only'            => isset( $shortcode_arguments['show_featured_only'] ) && $shortcode_arguments['show_featured_only'] === 'yes',
            'no_found_rows'            => true,
            'property_category'        => isset( $shortcode_arguments['property_category'] )        ? $shortcode_arguments['property_category']        : array(),
            'property_action_category' => isset( $shortcode_arguments['property_action_category'] ) ? $shortcode_arguments['property_action_category'] : array(),
            'property_city'            => isset( $shortcode_arguments['property_city'] )            ? $shortcode_arguments['property_city']            : array(),
            'property_area'            => isset( $shortcode_arguments['property_area'] )            ? $shortcode_arguments['property_area']            : array(),
            'property_county_state'    => isset( $shortcode_arguments['property_county_state'] )    ? $shortcode_arguments['property_county_state']    : array(),
            'property_status'          => isset( $shortcode_arguments['property_status'] )          ? $shortcode_arguments['property_status']          : array(),
            'property_features'        => isset( $shortcode_arguments['property_features'] )        ? $shortcode_arguments['property_features']        : array(),
        );

        // Step 4: Agent meta filtering — property_agent is a meta key, not author.
        if ( ! empty( $shortcode_arguments['agentid'] ) ) {
            $agent_ids = array_filter( array_map( 'trim', explode( ',', $shortcode_arguments['agentid'] ) ) );
            if ( ! empty( $agent_ids ) ) {
                $params['meta_query'] = array(
                    array(
                        'key'     => 'property_agent',
                        'value'   => count( $agent_ids ) === 1 ? reset( $agent_ids ) : array_values( $agent_ids ),
                        'compare' => count( $agent_ids ) === 1 ? '=' : 'IN',
                    ),
                );
            }
        }

        // Step 5: Execute query via the 2026 layer — single entry point.
        $result       = wpestate_query_2026_property( 'directory', $params );
        $recent_posts = $result['query'];

    } elseif ( $shortcode_arguments['type'] === 'agents' ) {

        // Step 6: Agent queries stay on the legacy path.
        $args         = wpestate_recent_posts_shortocodes_create_arg( $shortcode_arguments );
        $recent_posts = new WP_Query( $args );

    } else {

        // Step 7: Blog post queries stay on the legacy path.
        $args         = wpestate_recent_posts_shortocodes_create_arg( $shortcode_arguments );
        $recent_posts = new WP_Query( $args );

    }

    // Step 8: Render the appropriate card list.
    ob_start();
    if ( $shortcode_arguments['type'] === 'estate_property' ) {
        wpresidence_display_property_list_as_html( $recent_posts, array(), 'shortcode_list', $shortcode_arguments );
    } elseif ( $shortcode_arguments['type'] === 'agents' ) {
        wpresidence_display_agent_list_as_html( $recent_posts, 'estate_agent', array(), 'shortcode', $shortcode_arguments );
    } else {
        wpresidence_display_blog_list_as_html( $recent_posts, array(), 'shortcode', $shortcode_arguments );
    }

    return ob_get_clean();
}




 


add_action( 'wp_ajax_nopriv_wpestate_advanced_search_filters', 'wpestate_advanced_search_filters' );
add_action( 'wp_ajax_wpestate_advanced_search_filters', 'wpestate_advanced_search_filters' );

if( !function_exists('wpestate_advanced_search_filters') ):

    /**
     * AJAX handler: refresh advanced-search property list with sorting.
     *
     * Accepts serialized query args from frontend, applies requested ordering,
     * executes query, and renders refreshed listing cards.
     *
     * @return void Echoes rendered HTML and exits.
     */
    function wpestate_advanced_search_filters(){
       // check_ajax_referer( 'wpestate_search_nonce', 'security' );
        // Step 1: Reset query/post globals and initialize runtime context.
        wp_suspend_cache_addition(true);
        wp_reset_query();
        wp_reset_postdata();

        global $wpestate_currency;
        global $where_currency;
        global $post;
        global $wpestate_options;
        global $wpestate_prop_unit;
        global $prop_unit_class;
        global $wpestate_property_unit_slider;
        global $wpestate_no_listins_per_row;
        global $wpestate_uset_unit;
        global $wpestate_custom_unit_structure;

        $wpestate_custom_unit_structure    =   wpresidence_get_option('wpestate_property_unit_structure');
        $wpestate_uset_unit       =   intval ( wpresidence_get_option('wpestate_uset_unit','') );
        $current_user             =   wp_get_current_user();
        $userID                   =   $current_user->ID;
        $user_option              =   'favorites'.$userID;
        $curent_fav               =   get_option($user_option);
        $wpestate_currency                 =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency           =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
        $show_compare             =   1;
        $wpestate_options                  =   wpestate_page_details(intval($_POST['page_id']));
        $allowed_html             =   array();
        $wpestate_property_unit_slider     =   wpresidence_get_option('wp_estate_prop_list_slider','');
        $wpestate_no_listins_per_row       =   intval( wpresidence_get_option('wp_estate_listings_per_row', '') );
        $args1 = stripslashes($_POST['args']);

        $property_card_type         =   intval(wpresidence_get_option('wp_estate_unit_card_type'));
        $property_card_type_string  =   '';
        if($property_card_type==0){
            $property_card_type_string='';
        }else{
            $property_card_type_string='_type'.$property_card_type;
        }

        $args=  json_decode($args1,true);
        //$args = get_object_vars($args2);
        $wpestate_prop_unit          =   esc_html ( wpresidence_get_option('wp_estate_prop_unit','') );
        $prop_unit_class    =   '';
        if($wpestate_prop_unit=='list'){
            $prop_unit_class="ajax12";
        }


        // Step 2: Apply selected sort order to incoming query args.
        $order          =   esc_html(wp_kses($_POST['value'],$allowed_html));
        $order_array    =   wpestate_create_query_order_by_array($order);
        $args           =   array_merge($args,$order_array['order_array']);
               
       

        $args['cache_results']          =   false;
        $args['update_post_meta_cache'] =   false;
        $args['update_post_term_cache'] =   false;

        $prop_no    =   intval( wpresidence_get_option('wp_estate_prop_no', '') );

        // Step 3: Guard clause - only allow published estate_property queries.
        if ( $args['post_type']!='estate_property' || $args['post_status']!='publish'){
            exit('out pls');
        }



        $prop_selection = new WP_Query($args);
        print '<span id="scrollhere"></span>';
    
        wpresidence_display_property_list_as_html($prop_selection,$wpestate_options  ,'shortcode_list');
    
        wp_suspend_cache_addition(false);
        die();
  }

endif; // end   ajax_filter_listings_search





add_action( $handler.'_nopriv_wpestate_ajax_filter_listings', 'wpestate_ajax_filter_listings' );
add_action( $handler.'_wpestate_ajax_filter_listings', 'wpestate_ajax_filter_listings' );

if ( ! function_exists( 'wpestate_ajax_filter_listings' ) ) :

    /**
     * AJAX handler: filter property listings for the classic search endpoint.
     *
     * Called by the frontend JS with full taxonomy names as term IDs resolved
     * client-side. Maps JS payload to 2026 param keys and delegates to
     * wpestate_query_2026_property for the query.
     *
     * JS payload keys → 2026 param keys:
     *   property_action_category → property_action_category  (direct)
     *   property_category        → property_category         (direct)
     *   property_city            → property_city             (direct)
     *   property_area            → property_area             (direct)
     *   property_county_state    → property_county_state     (direct)
     *   property_status          → property_status           (direct)
     *   property_features        → property_features         (direct, may be array)
     *   order                    → order
     *   newpage                  → paged
     *   page_id                  → page context (not a query param)
     *   number                   → posts_per_page (when ishortcode=1)
     *
     * @return void Echoes JSON and exits.
     */
    function wpestate_ajax_filter_listings() {

        // Step 1: Security — verify nonce.
        $nonce = isset( $_POST['security'] ) ? $_POST['security'] : '';
        if ( ! wp_verify_nonce( $nonce, 'wpestate_ajax_filtering' ) ) {
            wp_send_json_error( array( 'message' => 'Security check failed' ) );
            return;
        }

        // Step 2: Suspend cache for performance.
        wp_suspend_cache_addition( true );

        // Step 3: Resolve page context and display options.
        $page_id          = isset( $_POST['page_id'] ) ? intval( $_POST['page_id'] ) : 0;
        $wpestate_options = wpestate_page_details( $page_id !== 0 ? $page_id : '' );

        // Step 4: Posts per page — shortcode context can override the theme default.
        $prop_no = intval( wpresidence_get_option( 'wp_estate_prop_no', '' ) );
        if ( isset( $_POST['ishortcode'] ) && intval( $_POST['ishortcode'] ) === 1 ) {
            $prop_no = intval( $_POST['number'] );
        }

        // Step 5: Pagination and order code.
        $paged = isset( $_POST['newpage'] ) ? intval( $_POST['newpage'] ) : 1;
        $order = isset( $_POST['order'] )   ? intval( $_POST['order'] )   : 0;

        // Step 6: Seed params with pagination and order.
        $params = array(
            'paged'          => $paged,
            'posts_per_page' => $prop_no,
            'order'          => $order,
            'no_found_rows'  => false,
        );

        // Step 7: Taxonomy term IDs — JS sends full taxonomy names already resolved client-side.
        $taxonomy_keys = array(
            'property_action_category',
            'property_category',
            'property_city',
            'property_area',
            'property_county_state',
            'property_status',
            'property_features',
        );
        wpestate_ajax_extract_taxonomy_params( $params, $taxonomy_keys );

        // Step 8: Execute query via the 2026 layer — single entry point.
        $result         = wpestate_query_2026_property( 'directory', $params );
        $prop_selection = $result['query'];
        $query_args     = $result['args'];

        // Step 9: Resolve render context.
        $context = 'ajax_list_shortcode';
        if ( isset( $_POST['context'] ) && ! empty( $_POST['context'] ) ) {
            $context = sanitize_text_field( $_POST['context'] );
        }

        // Step 10: Render property cards HTML.
        $pagination_parameters = array(
            'display' => 'yes',
            'order'   => $order,
            'paged'   => $paged,
        );

        $to_show = '<span id="scrollhere"></span>';
        ob_start();
        wpresidence_display_property_list_as_html( $prop_selection, $wpestate_options, $context, $_POST, $pagination_parameters );
        $to_show .= ob_get_clean();

        // Step 11: Resume cache.
        wp_suspend_cache_addition( false );

        // Step 12: Build on-demand map markers via 2026 params.
        $map_params                   = $params;
        $map_params['paged']          = 1;
        $map_params['posts_per_page'] = intval( wpresidence_get_option( 'wp_estate_map_max_pins' ) );
        $map_params['offset']         = ( $paged - 1 ) * $prop_no;
        $on_demand_results            = wpestate_listing_pins_on_demand( $map_params );

        // Step 13: Return JSON response.
        echo wp_json_encode(
            array(
                'context'          => 'ajax_list_shortcode',
                'wpestate_options' => $wpestate_options,
                'args'             => $query_args,
                'markers'          => $on_demand_results['markers'],
                'no_results'       => $on_demand_results['results'],
                'to_show'          => $to_show,
            )
        );
        die();
    }

endif;





add_action( $handler.'_nopriv_wpestate_custom_adv_ajax_filter_listings_search', 'wpestate_custom_adv_ajax_filter_listings_search' );
add_action( $handler.'_wpestate_custom_adv_ajax_filter_listings_search', 'wpestate_custom_adv_ajax_filter_listings_search' );

if ( ! function_exists( 'wpestate_custom_adv_ajax_filter_listings_search' ) ) :

    /**
     * AJAX handler: custom advanced search filter using the 2026 query layer.
     *
     * Reads POST data sent by wpestate_custom_search_start_filtering_ajax() in
     * ajaxcalls.js. The JS payload uses full taxonomy names as keys
     * (property_city, property_action_category, etc.) which match the 2026
     * canonical param keys directly - no legacy transformation needed.
     *
     * @return void Echoes JSON and exits.
     */
    function wpestate_custom_adv_ajax_filter_listings_search() {
        // check_ajax_referer( 'wpestate_ajax_filtering', 'security' );
        wp_suspend_cache_addition( true );

        // Step 1: read page context.
        $postid           = isset( $_POST['postid'] ) ? intval( $_POST['postid'] ) : 0;
        $wpestate_options = wpestate_page_details( $postid );

        // Step 2: handle half-map layout flag.
        $half_map = 0;
        if ( isset( $_POST['halfmap'] ) ) {
            $half_map                          = intval( $_POST['halfmap'] );
            $wpestate_options['content_class'] = 'col-md-9';
            $wpestate_options['sidebar_class'] = 'none';
        }

        // Step 3: pagination and posts per page.
        $paged   = isset( $_POST['newpage'] ) ? max( 1, intval( $_POST['newpage'] ) ) : 1;
        $prop_no = intval( wpresidence_get_option( 'wp_estate_prop_no_adv_search', '' ) );
        if ( $prop_no <= 0 ) {
            $prop_no = intval( wpresidence_get_option( 'wp_estate_prop_no', '' ) );
        }

        // Step 4: start building params for wpestate_query_2026_property.
        $params = array(
            'paged'          => $paged,
            'posts_per_page' => $prop_no,
            'order'          => isset( $_POST['order'] ) ? intval( $_POST['order'] ) : 0,
        );

        // Step 5: taxonomy params - JS sends full taxonomy names as keys,
        // matching 2026 canonical param keys directly.
        $taxonomy_keys = array(
            'property_category',
            'property_action_category',
            'property_city',
            'property_area',
            'property_county_state',
            'property_status',
            'property_features',
        );
        wpestate_ajax_extract_taxonomy_params( $params, $taxonomy_keys );

        // Step 6: numeric range and geo params - JS sends as flat POST keys
        // matching 2026 param names exactly.
        $float_keys = array(
            'price_min',    'price_max',
            'beds_min',     'beds_max',
            'baths_min',    'baths_max',
            'rooms_min',    'rooms_max',
            'size_min',     'size_max',
            'lot_size_min', 'lot_size_max',
            'geo_lat',      'geo_long',     'geo_rad',
        );
        wpestate_ajax_extract_float_params( $params, $float_keys );

        // Step 7: keyword search.
        if ( isset( $_POST['keyword_search'] ) && '' !== trim( $_POST['keyword_search'] ) ) {
            $params['keyword'] = sanitize_text_field( wp_unslash( $_POST['keyword_search'] ) );
        }

        // Step 8: read the canonical location filter and keep the legacy key as fallback.
        if ( isset( $_POST['location'] ) && '' !== trim( $_POST['location'] ) ) {
            $params['location'] = sanitize_text_field( wp_unslash( $_POST['location'] ) );
        } else if ( isset( $_POST['adv_location10'] ) && '' !== trim( $_POST['adv_location10'] ) ) {
            $params['location'] = sanitize_text_field( wp_unslash( $_POST['adv_location10'] ) );
        }

        // Step 9: meta_query - JS sends as array of {key, value, compare} objects,
        // passed straight through as raw WP_Query meta_query clauses.
        wpestate_ajax_extract_meta_query_params( $params );

        // Step 10: run query through the 2026 layer.
        $result         = wpestate_query_2026_property( 'directory', $params );
        $prop_selection = $result['query'];

        // Step 11: render property cards.
        $cards = wpestate_ajax_render_search_cards( $prop_selection, $wpestate_options, $paged );
        wp_suspend_cache_addition( false );

        // Step 12: saved search payload for half-map mode.
        $saved_search = null;
        $search_meta  = null;
        $show_save_search = wpresidence_get_option( 'wp_estate_show_save_search', '' );
        if ( 'yes' === $show_save_search ) {
            $custom_advanced_search = wpresidence_get_option( 'wp_estate_custom_advanced_search', '' );
            global $wpestate_included_ids;
            $search_meta = $wpestate_included_ids;

            $adv_search_what      = wpresidence_get_option( 'wp_estate_adv_search_what', '' );
            $adv_search_how       = wpresidence_get_option( 'wp_estate_adv_search_how', '' );
            $adv_search_label     = wpresidence_get_option( 'wp_estate_adv_search_label', '' );
            $adv_search_fields_no = floatval( wpresidence_get_option( 'wp_estate_adv_search_fields_no' ) );
            $term_counter         = isset( $_REQUEST['term_counter'] ) ? intval( $_REQUEST['term_counter'] ) : 0;

            $adv_search_what  = array_slice( $adv_search_what,  $term_counter * $adv_search_fields_no, $adv_search_fields_no );
            $adv_search_label = array_slice( $adv_search_label, $term_counter * $adv_search_fields_no, $adv_search_fields_no );
            $adv_search_how   = array_slice( $adv_search_how,   $term_counter * $adv_search_fields_no, $adv_search_fields_no );

            $saved_search = wpestate_show_search_params_new(
                $wpestate_included_ids,
                $result['args'],
                $custom_advanced_search,
                $adv_search_what,
                $adv_search_how,
                $adv_search_label
            );
        }

        // Step 13: JSON response.
        echo json_encode( array(
            'sent'         => true,
            'args'         => $result['args'],
            'opt'          => $wpestate_options,
            'search_meta'  => $search_meta,
            'saved_search' => $saved_search,
            'cards'        => $cards,
            'no_founs'     => $prop_selection->found_posts . ' ' . esc_html__( 'Listings', 'wpresidence' ),
        ) );
        die();
    }

endif; // end wpestate_custom_adv_ajax_filter_listings_search





add_action( $handler.'_nopriv_wpestate_custom_ondemand_pin_load', 'wpestate_custom_ondemand_pin_load' );
add_action( $handler.'_wpestate_custom_ondemand_pin_load', 'wpestate_custom_ondemand_pin_load' );

if ( ! function_exists( 'wpestate_custom_ondemand_pin_load' ) ) :

    /**
     * AJAX handler: load map pins on demand for the custom advanced search.
     *
     * Reads the same POST payload sent by wpestate_show_pins_custom_search()
     * in mapfunctions.js (full taxonomy names, price_min/price_max, geo,
     * beds/baths components, meta_query clauses). Runs the query through the
     * 2026 layer in 'map' context — which automatically limits results to
     * wp_estate_map_max_pins — then returns JSON with marker data.
     *
     * Response shape consumed by wpestate_load_on_demand_pins() in JS:
     *   { markers: [...], no_results: int }
     *
     * @return void Echoes JSON and exits.
     */
    function wpestate_custom_ondemand_pin_load() {
        // check_ajax_referer( 'wpestate_ajax_filtering', 'security' );
        wp_suspend_cache_addition( true );

        // Step 1: order param.
        $params = array(
            'paged' => 1,
            'order' => isset( $_POST['order'] ) ? intval( $_POST['order'] ) : 0,
        );

        // Step 2: taxonomy params — JS sends full taxonomy names as keys,
        // matching 2026 canonical param keys directly (no transform needed).
        $taxonomy_keys = array(
            'property_category',
            'property_action_category',
            'property_city',
            'property_area',
            'property_county_state',
            'property_status',
            'property_features',
        );
        wpestate_ajax_extract_taxonomy_params( $params, $taxonomy_keys );

        // Step 3: numeric range and geo params — JS sends as flat POST keys
        // matching 2026 param names exactly.
        $float_keys = array(
            'price_min',    'price_max',
            'beds_min',     'beds_max',
            'baths_min',    'baths_max',
            'rooms_min',    'rooms_max',
            'size_min',     'size_max',
            'lot_size_min', 'lot_size_max',
            'geo_lat',      'geo_long',     'geo_rad',
        );
        wpestate_ajax_extract_float_params( $params, $float_keys );

        // Step 4: keyword search.
        if ( isset( $_POST['keyword_search'] ) && '' !== trim( $_POST['keyword_search'] ) ) {
            $params['keyword'] = sanitize_text_field( wp_unslash( $_POST['keyword_search'] ) );
        }

        // Step 5: read the canonical location filter and keep the legacy key as fallback.
        if ( isset( $_POST['location'] ) && '' !== trim( $_POST['location'] ) ) {
            $params['location'] = sanitize_text_field( wp_unslash( $_POST['location'] ) );
        } else if ( isset( $_POST['adv_location10'] ) && '' !== trim( $_POST['adv_location10'] ) ) {
            $params['location'] = sanitize_text_field( wp_unslash( $_POST['adv_location10'] ) );
        }

        // Step 5: meta_query — JS sends array of {key, value, compare} objects
        // built from data-search-post-meta fields and beds/baths components.
        wpestate_ajax_extract_meta_query_params( $params );

        // Step 6: Build pin markers via 2026 params.
        // wpestate_listing_pins_on_demand now uses wpestate_query_2026_property('map')
        // internally — map context handles posts_per_page from wp_estate_map_max_pins.
        $on_demand_results = wpestate_listing_pins_on_demand( $params );

        // Step 8: return JSON — shape consumed by wpestate_load_on_demand_pins().
        wp_suspend_cache_addition( false );
        echo wp_json_encode( array(
            'markers'    => $on_demand_results['markers'],
            'no_results' => $on_demand_results['results'],
        ) );
        die();  
    }

endif; // end wpestate_custom_ondemand_pin_load




add_action( 'wp_ajax_nopriv_wpestate_ajax_filter_listings_search', 'wpestate_ajax_filter_listings_search' );
add_action( 'wp_ajax_wpestate_ajax_filter_listings_search', 'wpestate_ajax_filter_listings_search' );

if ( ! function_exists( 'wpestate_ajax_filter_listings_search' ) ) :

    /**
     * AJAX handler: standard search form filtering using the 2026 query layer.
     *
     * Reads POST data sent by wpestate_start_filtering_ajax() in ajaxcalls.js.
     * Taxonomy values arrive as term IDs resolved client-side. Rooms/baths
     * arrive as meta_query clauses. Prices are currency-converted from cookies
     * before being passed to the 2026 layer.
     *
     * Replaces the legacy wpestate_search_results_default('ajax') builder +
     * hardcoded order switch with a single wpestate_query_2026_property() call.
     *
     * @return void Echoes JSON and exits.
     */
    function wpestate_ajax_filter_listings_search() {
        // check_ajax_referer( 'wpestate_ajax_filtering', 'security' );

        // Step 1: Suspend cache and set up globals needed for card rendering.
        wp_suspend_cache_addition( true );

        global $post;
        global $wpestate_options;
        global $show_compare_only;
        global $wpestate_currency;
        global $where_currency;
        global $wpestate_property_unit_slider;
        global $is_col_md_12;
        global $wpestate_prop_unit;
        global $wpestate_no_listins_per_row;
        global $wpestate_uset_unit;
        global $wpestate_custom_unit_structure;

        $wpestate_custom_unit_structure = wpresidence_get_option( 'wpestate_property_unit_structure' );
        $wpestate_uset_unit             = intval( wpresidence_get_option( 'wpestate_uset_unit', '' ) );
        $wpestate_prop_unit             = esc_html( wpresidence_get_option( 'wp_estate_prop_unit', '' ) );

        // Step 2: Compare-only flag based on whether this is the front page.
        $show_compare_only = 'yes';
        if ( get_option( 'page_on_front' ) == intval( $_POST['postid'] ) ) {
            $show_compare_only = 'no';
        }

        // Step 3: Card type setup (used by the card renderer).
        $property_card_type        = intval( wpresidence_get_option( 'wp_estate_unit_card_type' ) );
        $property_card_type_string = '';
        if ( 0 !== $property_card_type ) {
            $property_card_type_string = '_type' . $property_card_type;
        }

        // Step 4: Currency, favorites, page details for card rendering.
        $current_user                  = wp_get_current_user();
        $curent_fav                    = wpestate_return_favorite_listings_per_user();
        $wpestate_currency             = esc_html( wpresidence_get_option( 'wp_estate_currency_symbol', '' ) );
        $where_currency                = esc_html( wpresidence_get_option( 'wp_estate_where_currency_symbol', '' ) );
        $wpestate_property_unit_slider = wpresidence_get_option( 'wp_estate_prop_list_slider', '' );
        $wpestate_options              = wpestate_page_details( intval( $_POST['postid'] ) );
        $wpestate_no_listins_per_row   = intval( wpresidence_get_option( 'wp_estate_listings_per_row', '' ) );

        // Step 5: Half-map flag (used downstream by card rendering).
        $half_map = 0;
        if ( isset( $_POST['halfmap'] ) ) {
            $half_map = intval( $_POST['halfmap'] );
        }

        // Step 6: Pagination, posts per page, and order code.
        $paged   = isset( $_POST['newpage'] ) ? max( 1, intval( $_POST['newpage'] ) ) : 1;
        $prop_no = intval( wpresidence_get_option( 'wp_estate_prop_no', '' ) );
        $order   = isset( $_POST['order'] ) ? intval( $_POST['order'] ) : 0;

        // Step 7: Seed 2026 params with pagination and order.
        $params = array(
            'paged'          => $paged,
            'posts_per_page' => $prop_no,
            'order'          => $order,
        );

        // Step 8: Taxonomy params — JS sends term IDs via full taxonomy names.
        $taxonomy_keys = array(
            'property_action_category',
            'property_category',
            'property_city',
            'property_county_state',
            'property_area',
            'property_features',
        );
        wpestate_ajax_extract_taxonomy_params( $params, $taxonomy_keys );

        // Step 9: Price params with multi-currency conversion.
        wpestate_ajax_convert_multicurrency_prices( $params );

        // Step 10: meta_query — JS sends array of {key, value, compare} objects
        // for rooms, bathrooms, and any other post-meta filters.
        wpestate_ajax_extract_meta_query_params( $params );

        // Step 11: Geo search params — lat, long, radius as floats.
        wpestate_ajax_extract_float_params( $params, array( 'geo_lat', 'geo_long', 'geo_rad' ) );

        // Step 12: Run query through the 2026 layer.
        $result         = wpestate_query_2026_property( 'directory', $params );
        $prop_selection = $result['query'];

        // Step 13: Render property cards.
        $cards = wpestate_ajax_render_search_cards( $prop_selection, $wpestate_options, $paged );
        wp_suspend_cache_addition( false );

        // Step 14: Return JSON response — same shape consumed by JS.
        echo json_encode( array(
            'sent'     => true,
            'args'     => $result['args'],
            'cards'    => $cards,
            'no_founs' => $prop_selection->found_posts . ' ' . esc_html__( 'Listings', 'wpresidence' ),
        ) );
        die();
    }

endif; // end wpestate_ajax_filter_listings_search





// on demand pins -

add_action( 'wp_ajax_nopriv_wpestate_classic_ondemand_pin_load', 'wpestate_classic_ondemand_pin_load' );
add_action( 'wp_ajax_wpestate_classic_ondemand_pin_load', 'wpestate_classic_ondemand_pin_load' );

if ( ! function_exists( 'wpestate_classic_ondemand_pin_load' ) ) :

    /**
     * AJAX handler: load map pins on demand for the classic (standard) search.
     *
     * Reads POST data sent by wpestate_show_pins() in mapfunctions.js.
     * Taxonomy values arrive as term IDs resolved client-side. Rooms/baths
     * arrive as meta_query clauses. Prices are currency-converted from cookies
     * before being passed to the 2026 layer.
     *
     * Response shape consumed by wpestate_load_on_demand_pins() in JS:
     *   { markers: [...], no_results: int }
     *
     * @return void Echoes JSON and exits.
     */
    function wpestate_classic_ondemand_pin_load() {
        // check_ajax_referer( 'wpestate_ajax_filtering', 'security' );
        wp_suspend_cache_addition( true );

        // Step 1: Seed params — pins always load page 1.
        $params = array(
            'paged' => 1,
            'order' => isset( $_POST['order'] ) ? intval( $_POST['order'] ) : 0,
        );

        // Step 2: Taxonomy params — JS sends canonical taxonomy names with term IDs.
        // Only caller is wpestate_show_pins() in mapfunctions.js.
        $taxonomy_keys = array(
            'property_action_category',
            'property_category',
            'property_city',
            'property_area',
            'property_county_state',
            'property_features',
        );
        wpestate_ajax_extract_taxonomy_params( $params, $taxonomy_keys );

        // Step 3: Price params with multi-currency conversion.
        // JS sends 'price_low' (not 'price_min') for the classic search endpoint.
        wpestate_ajax_convert_multicurrency_prices( $params, 'price_low', 'price_max' );

        // Step 4: meta_query — JS sends array of {key, value, compare} objects
        // for rooms, bathrooms, and any other post-meta filters.
        wpestate_ajax_extract_meta_query_params( $params );

        // Step 5: Geo search params — lat, long, radius as floats.
        wpestate_ajax_extract_float_params( $params, array( 'geo_lat', 'geo_long', 'geo_rad' ) );

        // Step 6: Build pin markers via 2026 params.
        // wpestate_listing_pins_on_demand now uses wpestate_query_2026_property('map')
        // internally — map context handles posts_per_page from wp_estate_map_max_pins.
        $on_demand_results = wpestate_listing_pins_on_demand( $params );

        // Step 8: Return JSON — shape consumed by wpestate_load_on_demand_pins().
        wp_suspend_cache_addition( false );
        echo wp_json_encode( array(
            'markers'    => $on_demand_results['markers'],
            'no_results' => $on_demand_results['results'],
        ) );
        die();
    }

endif; // end wpestate_classic_ondemand_pin_load




add_action( 'wp_ajax_nopriv_wpestate_classic_ondemand_pin_load_type2', 'wpestate_classic_ondemand_pin_load_type2' );
add_action( 'wp_ajax_wpestate_classic_ondemand_pin_load_type2', 'wpestate_classic_ondemand_pin_load_type2' );

if( !function_exists('wpestate_classic_ondemand_pin_load_type2') ):

    /**
     * Classic ondemand pin load type2 — migrated to 2026 params.
     *
     * Reads category, action, and location from $_POST and delegates to
     * wpestate_listing_pins_on_demand() which runs the query through
     * wpestate_query_2026_property('map').
     *
     * @return void Echoes JSON and exits.
     */
    function wpestate_classic_ondemand_pin_load_type2(){
        // check_ajax_referer( 'wpestate_ajax_filtering', 'security' );
        wp_suspend_cache_addition( true );

        $params = array(
            'paged' => 1,
            'order' => 0,
        );

        // Step 1: Category.
        if ( isset( $_POST['category_values'] ) && '' !== $_POST['category_values'] && 'all' !== $_POST['category_values'] ) {
            $params['property_category'] = sanitize_text_field( wp_unslash( $_POST['category_values'] ) );
        }

        // Step 2: Action.
        if ( isset( $_POST['action_values'] ) && '' !== $_POST['action_values'] && 'all' !== $_POST['action_values'] ) {
            $params['property_action_category'] = sanitize_text_field( wp_unslash( $_POST['action_values'] ) );
        }

        // Step 3: Location — single value searched across city/area/county_state with OR.
        if ( isset( $_POST['location'] ) && '' !== $_POST['location'] ) {
            $params['location'] = sanitize_text_field( wp_unslash( $_POST['location'] ) );
        }

        // Step 4: Build pin markers via 2026 params.
        $on_demand_results = wpestate_listing_pins_on_demand( $params );

        echo wp_json_encode( array(
            'markers'    => $on_demand_results['markers'],
            'no_results' => $on_demand_results['results'],
        ) );
        wp_suspend_cache_addition( false );
        die();
  }

 endif; // end   ajax_filter_listings



/*
 *
 *  On demand pins
 *
 *
 *
 *
*/





add_action( $handler.'_nopriv_wpestate_classic_ondemand_pin_load_type2_tabs', 'wpestate_classic_ondemand_pin_load_type2_tabs' );
add_action( $handler.'_wpestate_classic_ondemand_pin_load_type2_tabs', 'wpestate_classic_ondemand_pin_load_type2_tabs' );



if( !function_exists('wpestate_classic_ondemand_pin_load_type2_tabs') ):

    /**
     * Classic ondemand pin load type2 tabs — migrated to 2026 params.
     *
     * Reads category, action, location, and picked_tax from $_POST and
     * delegates to wpestate_listing_pins_on_demand() which runs the query
     * through wpestate_query_2026_property('map').
     *
     * @return void Echoes JSON and exits.
     */
    function wpestate_classic_ondemand_pin_load_type2_tabs(){
        // check_ajax_referer( 'wpestate_ajax_filtering', 'security' );
        wp_suspend_cache_addition( true );

        $allowed_html = array();
        $params       = array(
            'paged' => 1,
            'order' => 0,
        );

        // Step 1: Category.
        if ( isset( $_POST['category_values'] ) && '' !== $_POST['category_values'] && 'all' !== $_POST['category_values'] ) {
            $params['property_category'] = sanitize_text_field( wp_unslash( $_POST['category_values'] ) );
        }

        // Step 2: Action.
        if ( isset( $_POST['action_values'] ) && '' !== $_POST['action_values'] && 'all' !== $_POST['action_values'] ) {
            $params['property_action_category'] = sanitize_text_field( wp_unslash( $_POST['action_values'] ) );
        }

        // Step 3: Location — single value searched across city/area/county_state with OR.
        if ( isset( $_POST['location'] ) && '' !== $_POST['location'] ) {
            $params['location'] = sanitize_text_field( wp_unslash( $_POST['location'] ) );
        }

        // Step 4: Picked taxonomy (tab-specific extra taxonomy).
        $adv6_taxonomy = wpresidence_get_option( 'wp_estate_adv6_taxonomy' );
        if ( isset( $_POST['picked_tax'] ) && '' !== $_POST['picked_tax'] && 'all' !== $_POST['picked_tax'] ) {
            if ( ! empty( $adv6_taxonomy ) ) {
                $params[ $adv6_taxonomy ] = sanitize_text_field( wp_unslash( $_POST['picked_tax'] ) );
            }
        }

        // Step 5: Build pin markers via 2026 params.
        $on_demand_results = wpestate_listing_pins_on_demand( $params );

        echo wp_json_encode( array(
            'markers'    => $on_demand_results['markers'],
            'no_results' => $on_demand_results['results'],
        ) );
        wp_suspend_cache_addition( false );
        die();
  }

 endif; // end   ajax_filter_listings


