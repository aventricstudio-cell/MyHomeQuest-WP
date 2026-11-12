<?php
/**
 * Main orchestrator for the 2026 estate_property query layer.
 *
 * Single entry point for all estate_property WP_Query calls.
 * Always forces post_type = estate_property.
 *
 * File: libs/query/query2026/wpestate_query_2026_property.php
 *
 * @package WPEstate
 */

if ( ! function_exists( 'wpestate_query_2026_property_context_defaults' ) ) {
    /**
     * Returns default param values per query context.
     *
     * Provides sensible defaults for pagination, fields mode, and sorting
     * that differ by usage context so callers only need to override what
     * they need.
     *
     * @param string $context directory|map|dashboard|featured
     * @return array          Flat default params for this context.
     */
    function wpestate_query_2026_property_context_defaults( $context ) {
        // Step 1: start with universal defaults shared by all contexts.
        $defaults = array(
            'post_status'    => 'publish',
            'paged'          => 1,
            'posts_per_page' => 0,       // 0 = use WP site option
            'order'          => 0,       // 0 = featured-first (legacy code)
            'no_found_rows'  => false,
            'fields'         => '',      // empty = return full WP_Post objects
            'tax_relation'   => 'AND',
            'meta_relation'  => 'AND',
            'geo_lat'        => '',
            'geo_long'       => '',
            'geo_rad'        => 0,
        );

        // Step 2: apply per-context overrides.
        switch ( $context ) {
            case 'map':
                // Map queries fetch IDs only for fast pin rendering.
                $defaults['fields']         = 'ids';
                $defaults['no_found_rows']  = true;
                $defaults['posts_per_page'] = intval( wpresidence_get_option( 'wp_estate_map_max_pins', 200 ) );
                break;

            case 'dashboard':
                // Dashboard lists are paginated and need found_posts for pagination.
                $defaults['no_found_rows'] = false;
                break;

            case 'featured':
                // Featured widget queries are small and skip row count.
                $defaults['no_found_rows']  = true;
                $defaults['posts_per_page'] = 6;
                break;

            case 'directory':
            default:
                // Directory pages need found_posts for pagination.
                $defaults['no_found_rows'] = false;
                break;
        }

        // Allow child themes to customize context defaults (e.g. change posts_per_page for map).
        $defaults = apply_filters( 'wpestate_query_2026_context_defaults', $defaults, $context );

        return $defaults;
    }
}

if ( ! function_exists( 'wpestate_query_2026_property_normalize_params' ) ) {
    /**
     * Sanitizes and normalizes incoming params before query building.
     *
     * Merges caller params with context defaults and casts every value to
     * the correct type. Unknown keys are preserved so callers can pass
     * extra WP_Query args through $params directly.
     *
     * @param array  $params  Raw params from caller.
     * @param string $context Query context.
     * @return array          Sanitized, merged params array.
     */
    function wpestate_query_2026_property_normalize_params( $params, $context ) {
        // Step 1: merge with context defaults so every expected key exists.
        $defaults = wpestate_query_2026_property_context_defaults( $context );
        $params   = wp_parse_args( $params, $defaults );

        // Step 2: cast integer fields.
        $int_keys = array(
            'paged',
            'posts_per_page',
            'order',
            'offset',
            'agent',
            'p',
        );
        foreach ( $int_keys as $key ) {
            if ( isset( $params[ $key ] ) ) {
                $params[ $key ] = intval( $params[ $key ] );
            }
        }

        // Step 3: cast float fields.
        $float_keys = array(
            'price_min',
            'price_max',
            'beds_min',
            'beds_max',
            'baths_min',
            'baths_max',
            'rooms_min',
            'rooms_max',
            'size_min',
            'size_max',
            'lot_size_min',
            'lot_size_max',
            'geo_lat',
            'geo_long',
            'geo_rad',
        );
        foreach ( $float_keys as $key ) {
            if ( isset( $params[ $key ] ) ) {
                $params[ $key ] = floatval( $params[ $key ] );
            }
        }

        // Step 4: sanitize text fields.
        $text_keys = array(
            'country',
            'keyword',
            's',
            'location',
        );
        foreach ( $text_keys as $key ) {
            if ( isset( $params[ $key ] ) ) {
                $params[ $key ] = sanitize_text_field( $params[ $key ] );
            }
        }

        // Step 5: normalize taxonomy array params — ensure they are arrays.
        $tax_keys = array(
            'property_category',
            'property_action_category',
            'property_city',
            'property_area',
            'property_county_state',
            'property_status',
            'property_features',
        );
        foreach ( $tax_keys as $key ) {
            if ( isset( $params[ $key ] ) && ! is_array( $params[ $key ] ) ) {
                $params[ $key ] = '' !== $params[ $key ] ? array( $params[ $key ] ) : array();
            }
        }

        // Step 6: normalize boolean params.
        $bool_keys = array(
            'no_found_rows',
            'nopaging',
            'featured_only',
            'cache_results',
            'update_post_meta_cache',
            'update_post_term_cache',
            'suppress_filters',
        );
        foreach ( $bool_keys as $key ) {
            if ( isset( $params[ $key ] ) ) {
                $params[ $key ] = (bool) $params[ $key ];
            }
        }

        // Step 7: normalize post_status to a safe value.
        if ( isset( $params['post_status'] ) && is_string( $params['post_status'] ) ) {
            $params['post_status'] = sanitize_key( $params['post_status'] );
        }

        // Step 8: normalize tax_relation and meta_relation to AND or OR.
        foreach ( array( 'tax_relation', 'meta_relation' ) as $key ) {
            if ( isset( $params[ $key ] ) ) {
                $params[ $key ] = ( 'OR' === strtoupper( $params[ $key ] ) ) ? 'OR' : 'AND';
            }
        }

        return $params;
    }
}

if ( ! function_exists( 'wpestate_query_2026_property' ) ) {
    /**
     * Central entry point for all estate_property WP_Query calls.
     *
     * Orchestrates the full build chain: context defaults → normalize → pagination
     * → order → featured priority → tax_query → meta_query → custom fields →
     * sold exclusion → geo filter → identity filters → run → format result.
     *
     * Always forces post_type = estate_property.
     *
     * -----------------------------------------------------------------------
     * FULL SAMPLE CALL — all recognised $params keys
     * -----------------------------------------------------------------------
     *
     * $result = wpestate_query_2026_property(
     *     'directory',                // context: directory | map | dashboard | featured
     *     array(
     *
     *         // --- Pagination ---------------------------------------------------
     *         'paged'          => 1,          // current page number
     *         'posts_per_page' => 12,         // 0 = use WP site option
     *         'offset'         => 0,          // skip N posts (overrides paged math)
     *         'nopaging'       => false,      // true = return all posts (no limit)
     *         'no_found_rows'  => false,      // true = skip SQL_CALC_FOUND_ROWS
     *
     *         A+  // --- Order --------------------------------------------------------
     *         // Pass a numeric code; orderby/order/meta_key are resolved internally
     *         // by wpestate_set_order_parameter_property() inside build_order_args.
     *         //   0 = featured-first (default)  1 = newest   2 = price asc
     *         //   3 = price desc                4 = oldest   5 = bedrooms asc
     *         //   6 = bedrooms desc             7 = size asc 8 = size desc
     *         'order'          => 0,
     *
     *            A+  // --- Taxonomies ---------------------------------------------------
     *         // Each accepts a single slug/name/ID string or an array of them.
     *         // Non-Latin slugs are resolved via get_term_by('name') fallback.
     *         'property_action_category' => array( 'for-sale' ),
     *         'property_category'        => array( 'apartment' ),
     *         'property_city'            => array( 'new-york' ),
     *         'property_area'            => array( 'manhattan' ),
     *         'property_county_state'    => array( 'new-york-state' ),
     *         'property_status'          => array( 'active' ),
     *         'property_features'        => array( 'pool', 'garage' ),  // AND relation
     *         'tax_relation'   => 'AND',      // relation between taxonomy clauses: AND | OR
     *
     *         // --- Price / numeric range filters --------------------------------
     *         'price_min'      => 100000,
     *         'price_max'      => 500000,
     *         'beds_min'       => 2,
     *         'beds_max'       => 5,
     *         'baths_min'      => 1,
     *         'baths_max'      => 3,
     *         'rooms_min'      => 3,
     *         'rooms_max'      => 10,
     *         'size_min'       => 50,         // in theme units (sqft or sqm)
     *         'size_max'       => 300,
     *         'lot_size_min'   => 100,
     *         'lot_size_max'   => 2000,
     *
     *         // --- Text / location filters --------------------------------------
     *         'keyword'        => 'sea view',     // title-only search (wpestate_keyword_where)
     *         's'              => 'sea view',     // alias for keyword
     *         'country'        => 'Greece',       // LIKE match on property_country meta
     *
     *         // --- Agent / featured flags ---------------------------------------
     *         'agent'          => 42,             // author/agent user ID
     *         'featured_only'  => false,          // true = only prop_featured = 1 listings
     *
     *           A+   // --- Geolocation --------------------------------------------------
     *         'geo_lat'        => 37.9838,        // centre latitude
     *         'geo_long'       => 23.7275,        // centre longitude
     *         'geo_rad'        => 10,             // radius in theme unit (miles or km)
     *
     *         // --- Identity / targeting -----------------------------------------
     *         'p'              => 0,              // single post by ID
     *         'post__in'       => array( 1, 2 ), // include only these IDs
     *         'post__not_in'   => array( 3 ),    // exclude these IDs
     *         'author'         => 0,             // single author user ID
     *         'author__in'     => array( 5, 6 ),
     *         'author__not_in' => array( 7 ),
     *
     *         // --- Meta relation ------------------------------------------------
     *         'meta_relation'  => 'AND',          // relation between all meta clauses: AND | OR
     *
     *         // --- Custom fields (dynamic, from wp_estate_custom_fields option) --
     *         // Keys must match slugified custom field labels defined in theme options.
     *         'custom_fields'         => array( 'garage_spaces' => '2', 'pool' => 'yes' ),
     *         'custom_fields_compare' => array( 'garage_spaces' => 'greater', 'pool' => 'equal' ),
     *         // Supported compare values: equal | greater | smaller | like | date bigger | date smaller
     *
     *          A+  // --- Raw meta_query pass-through (advanced) -----------------------
     *         // A raw WP_Query meta_query array merged after all built clauses.
     *         'meta_query'     => array(
     *             array(
     *                 'key'     => 'my_custom_key',
     *                 'value'   => 'my_value',
     *                 'compare' => '=',
     *             ),
     *         ),
     *
     *         // --- Performance flags --------------------------------------------
     *         'fields'                  => '',     // '' = full objects | 'ids' | 'id=>parent'
     *         'cache_results'           => true,
     *         'update_post_meta_cache'  => true,
     *         'update_post_term_cache'  => true,
     *         'suppress_filters'        => false,
     *
     *     ),
     *     array(
     *         // --- Options (third argument) -------------------------------------
     *         'respect_custom_order_zero' => true, // false = skip wpestate_return_filtered_by_order
     *     )
     * );
     *
     * $prop_selection = $result['query'];   // WP_Query object — use in template loops
     * $args           = $result['args'];    // final WP_Query args (needed for map pins)
     * $total          = $result['total'];   // found_posts count (0 when no_found_rows=true)
     *
     * -----------------------------------------------------------------------
     *
     * @param string $context  directory|map|dashboard|featured
     * @param array  $params   Filter and query params (all keys shown in sample above).
     * @param array  $options  Caller options:
     *                          - respect_custom_order_zero (bool, default true)
     * @return array {
     *     @type WP_Query $query              Executed query object.
     *     @type array    $args               Final WP_Query args used.
     *     @type int      $total              Found posts total.
     *     @type string   $transient_appendix Cache-key segment.
     * }
     */
    function wpestate_query_2026_property( $context, $params = array(), $options = array() ) {
        // Step 1: normalize context to a known value.
        $valid_contexts = array( 'directory', 'map', 'dashboard', 'featured' );
        if ( ! in_array( $context, $valid_contexts, true ) ) {
            $context = 'directory';
        }

        // Step 2: sanitize and merge params with context defaults.
        $params = wpestate_query_2026_property_normalize_params( $params, $context );

        // Allow child themes to modify normalized params before query building.
        $params = apply_filters( 'wpestate_query_2026_params', $params, $context, $options );

        // Step 3: start with the fixed base args — always estate_property.
        $args = array(
            'post_type'   => 'estate_property',
            'post_status' => isset( $params['post_status'] ) ? $params['post_status'] : 'publish',
        );

        // Step 4: apply performance flags from params when explicitly set.
        $perf_keys = array(
            'fields',
            'cache_results',
            'update_post_meta_cache',
            'update_post_term_cache',
            'suppress_filters',
        );
        foreach ( $perf_keys as $key ) {
            if ( isset( $params[ $key ] ) && '' !== $params[ $key ] ) {
                $args[ $key ] = $params[ $key ];
            }
        }

        // Step 5: build and merge pagination arguments.
        $pagination_args = wpestate_query_2026_property_build_pagination_args( $params );
        $args            = array_merge( $args, $pagination_args );

        // Step 6: build and merge order arguments.
        $order_args = wpestate_query_2026_property_build_order_args( $params, $context );
        $args       = array_merge( $args, $order_args );

        // Step 7: apply featured-first priority (may override order when order=0).
        $args = wpestate_query_2026_property_apply_featured_priority( $args, $params, $context );

        // Step 8: build taxonomy query clauses.
        $tax_query = wpestate_query_2026_property_build_tax_query( $params );
        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query;
        }

        // Step 9: build standard meta query clauses.
        $meta_clauses = wpestate_query_2026_property_build_meta_query( $params );

        // Step 10: build custom fields meta query clauses and merge.
        $custom_clauses = wpestate_query_2026_property_build_custom_fields_meta_query( $params );
        if ( ! empty( $custom_clauses ) ) {
            $meta_clauses = array_merge( $meta_clauses, $custom_clauses );
        }

        // Allow child themes to add/modify meta query clauses before assembly.
        $meta_clauses = apply_filters( 'wpestate_query_2026_meta_query', $meta_clauses, $params, $context );

        // Step 11: assemble meta_query with relation when more than one clause exists.
        if ( ! empty( $meta_clauses ) ) {
            if ( count( $meta_clauses ) > 1 ) {
                $relation              = isset( $params['meta_relation'] ) ? $params['meta_relation'] : 'AND';
                $meta_clauses['relation'] = ( 'OR' === $relation ) ? 'OR' : 'AND';
            }
            $args['meta_query'] = $meta_clauses;
        }

        // Step 12: apply sold-listing exclusion (delegates to wpestate_remove_sold_listings).
        $args = wpestate_query_2026_property_apply_sold_exclusion( $args, $params );

        // Step 13: apply geolocation filter (delegates to wpestate_build_property_geo_query).
        $args = wpestate_query_2026_property_apply_geo_filter( $args, $params );

        // Step 14: apply identity and targeting filters (p, post__in, keyword, author).
        $args = wpestate_query_2026_property_apply_identity_filters( $args, $params );


        // Allow child themes to modify final WP_Query args before execution.
        $args = apply_filters( 'wpestate_query_2026_args', $args, $params, $context, $options );

        //do not remove
        //error_log(print_r($args,true));

        // Step 15: execute the query with temporary filter hook management.
        $query = wpestate_query_2026_property_run_wp_query( $args, $params, $options );

        // Step 16: format and return the standardized result.
        $result = wpestate_query_2026_property_format_result( $query, $args, $context, $options );

        // Allow child themes to modify/inspect the final result (logging, analytics, replacement).
        $result = apply_filters( 'wpestate_query_2026_result', $result, $context, $params, $options );

        return $result;
    }
}
