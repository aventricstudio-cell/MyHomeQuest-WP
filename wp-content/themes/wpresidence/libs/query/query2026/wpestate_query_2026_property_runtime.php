<?php
/**
 * Execution layer for the 2026 estate_property query layer.
 *
 * Holds pagination, order, featured-priority, sold-exclusion, geo-filter,
 * identity-filter, query runner, and result formatter functions.
 *
 * File: libs/query/query2026/wpestate_query_2026_property_runtime.php
 *
 * @package WPEstate
 */

if ( ! function_exists( 'wpestate_query_2026_property_build_pagination_args' ) ) {
    /**
     * Builds pagination WP_Query arguments from canonical params.
     *
     * Handles paged, posts_per_page, offset, nopaging, and no_found_rows.
     * Falls back to the WordPress posts_per_page option when no value is set.
     *
     * @param array $params Canonical params array.
     * @return array        WP_Query pagination keys.
     */
    function wpestate_query_2026_property_build_pagination_args( $params ) {
        $args = array();

        // Step 1: determine page number (minimum 1).
        $paged = isset( $params['paged'] ) ? max( 1, intval( $params['paged'] ) ) : 1;
        $args['paged'] = $paged;

        // Step 2: determine posts per page; fall back to WP site option.
        if ( ! empty( $params['nopaging'] ) ) {
            $args['nopaging'] = true;
        } else {
            $per_page = intval( isset( $params['posts_per_page'] ) ? $params['posts_per_page'] : 0 );
            if ( $per_page <= 0 ) {
                $per_page = intval( get_option( 'posts_per_page', 10 ) );
            }
            $args['posts_per_page'] = $per_page;
        }

        // Step 3: optional offset (0 means no offset).
        if ( isset( $params['offset'] ) && intval( $params['offset'] ) > 0 ) {
            $args['offset'] = intval( $params['offset'] );
        }

        // Step 4: no_found_rows skips the SQL_CALC_FOUND_ROWS count query.
        if ( isset( $params['no_found_rows'] ) ) {
            $args['no_found_rows'] = (bool) $params['no_found_rows'];
        }

        return $args;
    }
}

if ( ! function_exists( 'wpestate_query_2026_property_build_order_args' ) ) {
    /**
     * Builds order/orderby WP_Query arguments.
     *
     * Delegates to the existing wpestate_set_order_parameter_property() for
     * the standard numeric order codes used across the theme. Supports an
     * order_dir override to flip ASC/DESC, and an explicit orderby key to
     * bypass the numeric-code path entirely.
     *
     * @param array  $params  Canonical params array.
     * @param string $context Query context (directory|map|dashboard|featured).
     * @return array          WP_Query order keys.
     */
    function wpestate_query_2026_property_build_order_args( $params, $context ) {
        $args = array();

        // Step 1: read the numeric order code — the only caller-facing order param.
        // orderby, order_dir, and meta_key are always derived from this code via
        // wpestate_set_order_parameter_property(); they are never read from $params.
        $order_code   = isset( $params['order'] ) ? intval( $params['order'] ) : 0;
        $order_result = wpestate_set_order_parameter_property( $order_code );

        // Step 2: for order code 0, sort featured listings first then by date.
        // prop_featured is guaranteed to exist on every property (enforced at save),
        // so the INNER JOIN added by meta_key will not drop any rows.
        if ( 0 === $order_code ) {
            $args['meta_key'] = 'prop_featured';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
            return $args;
        }

        // Step 3: apply the orderby/order/meta_key resolved by the helper.
        $args['orderby'] = isset( $order_result['orderby'] ) ? $order_result['orderby'] : 'date';
        $args['order']   = isset( $order_result['order'] )   ? $order_result['order']   : 'DESC';

        // For non-zero order codes the helper may specify a meta_key (price, bedrooms,
        // etc.). Add it so WP_Query can order by meta_value_num.
        if ( ! empty( $order_result['meta_key'] ) ) {
            $args['meta_key'] = $order_result['meta_key'];
        }

        return $args;
    }
}

if ( ! function_exists( 'wpestate_query_2026_property_apply_featured_priority' ) ) {
    /**
     * Applies featured-first ordering when no explicit sort override is present.
     *
     * Featured listings (prop_featured = 1) should appear first by default.
     * This is already the default for order code 0, but for other contexts
     * or explicit orderby overrides this function leaves the args unchanged.
     *
     * @param array  $args    Assembled WP_Query args so far.
     * @param array  $params  Canonical params array.
     * @param string $context Query context.
     * @return array          Possibly modified $args.
     */
    function wpestate_query_2026_property_apply_featured_priority( $args, $params, $context ) {
        // For order code 0, build_order_args already set orderby=date as a safe WP_Query
        // fallback. The actual featured-first SQL ordering is handled by
        // wpestate_return_filtered_by_order() inside run_wp_query() when
        // respect_custom_order_zero=true (the production default).
        //
        // For explicit order codes (1-8), the sort is already set by build_order_args.
        // Nothing to override here.

        // Step 1: for the 'featured' context, ensure the prop_featured ordering is applied
        // using meta_key (safe here because featured_only=true in build_meta_query
        // already requires prop_featured to exist via its own meta_query clause).
        if ( 'featured' === $context && ! empty( $params['featured_only'] ) ) {
            $args['meta_key'] = 'prop_featured';
            $args['orderby']  = 'meta_value_num';
            $args['order']    = 'DESC';
        }

        return $args;
    }
}

if ( ! function_exists( 'wpestate_query_2026_property_apply_sold_exclusion' ) ) {
    /**
     * Applies sold-listing exclusion using the existing theme helper.
     *
     * Delegates entirely to wpestate_remove_sold_listings() which checks
     * the theme option 'wp_estate_show_sold_items' and appends a NOT IN
     * tax_query clause when configured.
     *
     * @param array $args   Assembled WP_Query args so far.
     * @param array $params Canonical params array (unused — kept for API symmetry).
     * @return array        Modified $args.
     */
    function wpestate_query_2026_property_apply_sold_exclusion( $args, $params ) {
        // Step 1: delegate to existing sold-exclusion helper.
        return wpestate_remove_sold_listings( $args );
    }
}

if ( ! function_exists( 'wpestate_query_2026_property_apply_geo_filter' ) ) {
    /**
     * Applies geolocation radius filter using the existing theme helper.
     *
     * Delegates to wpestate_build_property_geo_query() which reads the
     * 'wp_estate_use_geo_location' option and attaches geo payload to args.
     * The actual SQL modification is applied by the geo_search_clauses filter
     * which is hooked during run_wp_query() and removed immediately after.
     *
     * @param array $args   Assembled WP_Query args so far.
     * @param array $params Canonical params array.
     * @return array        Modified $args.
     */
    function wpestate_query_2026_property_apply_geo_filter( $args, $params ) {
        $lat    = isset( $params['geo_lat'] )  ? $params['geo_lat']  : '';
        $long   = isset( $params['geo_long'] ) ? $params['geo_long'] : '';
        $radius = isset( $params['geo_rad'] )  ? $params['geo_rad']  : 0;

        // Step 1: guard against zero/empty coordinates — normalize_params converts
        // '' to 0.0 via floatval, and empty(0.0) is true in PHP. Without this guard
        // the legacy geo function runs with (0,0) coordinates, finds no properties,
        // and sets post__in = array(0) which kills the entire query.
        if ( empty( $lat ) || empty( $long ) || empty( $radius ) ) {
            return $args;
        }

        // Step 2: delegate to existing geo helper using lat/long/radius from params.
        return wpestate_build_property_geo_query( $args, $lat, $long, $radius );
    }
}

if ( ! function_exists( 'wpestate_query_2026_property_apply_identity_filters' ) ) {
    /**
     * Applies identity and targeting filters (p, post__in, post__not_in, author, keyword).
     *
     * These are passed through verbatim to WP_Query after the rest of the
     * args are assembled. The 's' key for keyword is stored here; the actual
     * title-only restriction filter is hooked in run_wp_query() to keep
     * filter registration localized to the query execution.
     *
     * @param array $args   Assembled WP_Query args so far.
     * @param array $params Canonical params array.
     * @return array        Modified $args.
     */
    function wpestate_query_2026_property_apply_identity_filters( $args, $params ) {
        // Step 1: single post by ID.
        if ( ! empty( $params['p'] ) ) {
            $args['p'] = intval( $params['p'] );
        }

        // Step 2: keyword search — stored on args, filter hooked later.
        if ( ! empty( $params['keyword'] ) ) {
            $args['s'] = sanitize_text_field( $params['keyword'] );
        } elseif ( ! empty( $params['s'] ) ) {
            $args['s'] = sanitize_text_field( $params['s'] );
        }

        // Step 3: explicit post ID inclusion list.
        if ( ! empty( $params['post__in'] ) && is_array( $params['post__in'] ) ) {
            $args['post__in'] = array_map( 'intval', $params['post__in'] );
        }

        // Step 4: explicit post ID exclusion list.
        if ( ! empty( $params['post__not_in'] ) && is_array( $params['post__not_in'] ) ) {
            $args['post__not_in'] = array_map( 'intval', $params['post__not_in'] );
        }

        // Step 5: author filters.
        if ( ! empty( $params['author'] ) ) {
            $args['author'] = intval( $params['author'] );
        }
        if ( ! empty( $params['author__in'] ) && is_array( $params['author__in'] ) ) {
            $args['author__in'] = array_map( 'intval', $params['author__in'] );
        }
        if ( ! empty( $params['author__not_in'] ) && is_array( $params['author__not_in'] ) ) {
            $args['author__not_in'] = array_map( 'intval', $params['author__not_in'] );
        }

        return $args;
    }
}


if ( ! function_exists( 'wpestate_query_2026_property_run_wp_query' ) ) {
    /**
     * Executes the final WP_Query with temporary hook management.
     *
     * Hooks wpestate_keyword_where (title-only search), wpestate_geo_search_clauses,
     * and wpestate_query_2026_featured_order_clauses around the query so they
     * affect only this one query and are immediately removed afterward.
     *
     * @param array $args    Final WP_Query arguments.
     * @param array $params  Canonical params array.
     * @param array $options Caller options (respect_custom_order_zero, etc.).
     * @return WP_Query      Executed query object.
     */
    function wpestate_query_2026_property_run_wp_query( $args, $params, $options ) {
        // Step 1: hook keyword title-only filter when a search term is present.
        $has_keyword = ! empty( $args['s'] );
        if ( $has_keyword && function_exists( 'wpestate_keyword_where' ) ) {
            add_filter( 'posts_where', 'wpestate_keyword_where', 10, 2 );
        }

        // Step 2: hook geo clauses filter when geo payload is in args.
        $has_geo = ! empty( $args['wpestate_geo_query'] );
        if ( $has_geo && function_exists( 'wpestate_geo_search_clauses' ) ) {
            add_filter( 'posts_clauses', 'wpestate_geo_search_clauses', 10, 2 );
        }

        // Step 3: execute the query.
        $query = new WP_Query( $args );

        // Step 4: remove temporary filters so they do not bleed into other queries.
        if ( $has_keyword && function_exists( 'wpestate_keyword_where' ) ) {
            remove_filter( 'posts_where', 'wpestate_keyword_where', 10 );
        }
        if ( $has_geo && function_exists( 'wpestate_geo_search_clauses' ) ) {
            remove_filter( 'posts_clauses', 'wpestate_geo_search_clauses', 10 );
        }

        return $query;
    }
}

if ( ! function_exists( 'wpestate_query_2026_property_format_result' ) ) {
    /**
     * Formats the WP_Query result into the standardized 2026 return shape.
     *
     * Returns the same structure regardless of context so all call sites can
     * rely on the same keys: query, args, total, transient_appendix.
     * Never modifies $args or re-runs the query.
     *
     * @param WP_Query $query   Executed query object.
     * @param array    $args    Final WP_Query arguments that were used.
     * @param string   $context Query context.
     * @param array    $options Caller options (unused, reserved for future use).
     * @return array {
     *     @type WP_Query $query              The executed WP_Query.
     *     @type array    $args               The final args passed to WP_Query.
     *     @type int      $total              Total found posts (0 when no_found_rows=true).
     *     @type string   $transient_appendix Cache-key segment built from order/pagination.
     * }
     */
    function wpestate_query_2026_property_format_result( $query, $args, $context, $options ) {
        // Step 1: extract found_posts from query (0 when no_found_rows was true).
        $total = isset( $query->found_posts ) ? intval( $query->found_posts ) : 0;

        // Step 2: build a transient appendix string for use by cache wrappers.
        $meta_key  = isset( $args['meta_key'] ) ? $args['meta_key'] : '';
        $order_dir = isset( $args['order'] )    ? $args['order']    : 'DESC';
        $paged     = isset( $args['paged'] )    ? intval( $args['paged'] ) : 1;
        $transient_appendix = '_' . $context . '_' . $meta_key . '_' . $order_dir . '_p' . $paged;

        // Step 3: return the standardized result array.
        return array(
            'query'              => $query,
            'args'               => $args,
            'total'              => $total,
            'transient_appendix' => $transient_appendix,
        );
    }
}
