<?php
/**
 * Input parser for the 2026 estate_property query layer.
 *
 * Converts a page_id (reads directory page meta) or a complex nested input
 * array (per plan2.md schema) into a flat canonical $params array ready
 * for wpestate_query_2026_property().
 *
 * File: libs/query/query2026/wpestate_query_2026_prepare_arguments.php
 *
 * @package WPEstate
 */

if ( ! function_exists( 'wpestate_query_2026_prepare_arguments' ) ) {
    /**
     * Parses query input into canonical flat params.
     *
     * Accepts two different input formats:
     *
     * 1. Integer page_id: reads the directory page's postmeta (listing_filter,
     *    show_featured_only, etc.) and returns the corresponding flat params.
     *
     * 2. Nested array (plan2.md schema): flattens grouped keys (Author, Caching,
     *    meta, taxonomies, date, order, post__in, etc.) into canonical flat params.
     *
     * @param int|array $input   Page ID or nested input array.
     * @param string    $context Context hint (directory|map|dashboard|featured).
     * @return array             Flat canonical params array.
     */
    function wpestate_query_2026_prepare_arguments( $input, $context = '' ) {
        $params = array();

        // Step 1: route to the correct handler based on input type.
        if ( is_int( $input ) || ( is_string( $input ) && ctype_digit( $input ) ) ) {
            // Step 1a: integer path — read directory page meta.
            $params = wpestate_query_2026_prepare_arguments_from_page_id( intval( $input ) );
        } elseif ( is_array( $input ) ) {
            // Step 1b: array path — flatten nested schema into canonical params.
            $params = wpestate_query_2026_prepare_arguments_from_array( $input );
        }

        return $params;
    }
}

if ( ! function_exists( 'wpestate_query_2026_prepare_arguments_from_page_id' ) ) {
    /**
     * Reads directory page postmeta into canonical flat params.
     *
     * Reads the page meta keys used by WPResidence directory page templates
     * (listing_filter, adv_filter_*, show_featured_only, etc.) and maps
     * them to the canonical params format expected by wpestate_query_2026_property().
     *
     * @param int $page_id WordPress post ID of the directory page.
     * @return array       Flat canonical params.
     */
    function wpestate_query_2026_prepare_arguments_from_page_id( $page_id ) {
        $params = array();

        // Step 1: bail early for invalid page IDs.
        if ( $page_id <= 0 ) {
            return $params;
        }

        // Step 2: read default listing order from page meta.
        $listing_filter = get_post_meta( $page_id, 'listing_filter', true );
        if ( '' !== $listing_filter && is_numeric( $listing_filter ) ) {
            $params['order'] = intval( $listing_filter );
        }

        // Step 3: read posts_per_page from page meta (falls back to global option later).
        $prop_no = get_post_meta( $page_id, 'prop_no', true );
        if ( '' !== $prop_no && intval( $prop_no ) > 0 ) {
            $params['posts_per_page'] = intval( $prop_no );
        }

        // Step 4: read default action/buy/rent filter from page meta.
        $action = get_post_meta( $page_id, 'adv_filter_search_action', true );
        if ( ! empty( $action ) && 'all' !== $action ) {
            $params['property_action_category'] = is_array( $action ) ? $action : array( $action );
        }

        // Step 5: read default category filter from page meta.
        $category = get_post_meta( $page_id, 'adv_filter_search_category', true );
        if ( ! empty( $category ) && 'all' !== $category ) {
            $params['property_category'] = is_array( $category ) ? $category : array( $category );
        }

        // Step 6: read default city filter from page meta.
        $city = get_post_meta( $page_id, 'current_adv_filter_city', true );
        if ( ! empty( $city ) && 'all' !== $city ) {
            $params['property_city'] = is_array( $city ) ? $city : array( $city );
        }

        // Step 7: read default area filter from page meta.
        $area = get_post_meta( $page_id, 'current_adv_filter_area', true );
        if ( ! empty( $area ) && 'all' !== $area ) {
            $params['property_area'] = is_array( $area ) ? $area : array( $area );
        }

        // Step 8: read default county/state filter from page meta.
        $county = get_post_meta( $page_id, 'current_adv_filter_county', true );
        if ( ! empty( $county ) && 'all' !== $county ) {
            $params['property_county_state'] = is_array( $county ) ? $county : array( $county );
        }

        // Step 9: featured-only flag from page meta.
        $featured_only = get_post_meta( $page_id, 'show_featured_only', true );
        if ( 'yes' === $featured_only || '1' === (string) $featured_only ) {
            $params['featured_only'] = true;
        }

        return $params;
    }
}

if ( ! function_exists( 'wpestate_query_2026_prepare_arguments_from_array' ) ) {
    /**
     * Flattens the nested plan2.md input schema into canonical flat params.
     *
     * The plan2.md input schema groups keys under top-level categories:
     * - 'author'     => array( author, author__in, author__not_in )
     * - 'order'      => array( order, orderby, order_dir )
     * - 'caching'    => array( cache_results, no_found_rows, suppress_filters, ... )
     * - 'meta'       => array( meta_key => value ) — becomes meta_query clauses
     * - 'taxonomies' => array( taxonomy_name => array of term IDs / slugs )
     * - 'date'       => array( year, monthnum, day, hour, ... )
     * - Top-level keys (post__in, post__not_in, p, fields, posts_per_page, etc.)
     *   are passed through as-is.
     *
     * @param array $input Nested input array.
     * @return array       Flat canonical params.
     */
    function wpestate_query_2026_prepare_arguments_from_array( $input ) {
        $params = array();

        // Step 1: pass simple top-level keys through directly.
        $passthrough_keys = array(
            'p',
            'post__in',
            'post__not_in',
            'post_status',
            'fields',
            'posts_per_page',
            'paged',
            'offset',
            'nopaging',
            'no_found_rows',
            'keyword',
            's',
            'geo_lat',
            'geo_long',
            'geo_rad',
            'featured_only',
            'order',           // numeric code only — orderby/order_dir/meta_key are internal
            'meta_relation',
            'tax_relation',
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
            'country',
            'agent',
            'property_category',
            'property_action_category',
            'property_city',
            'property_area',
            'property_county_state',
            'property_status',
            'property_features',
            'custom_fields',
            'custom_fields_compare',
            'meta',
            'meta_query',
        );
        foreach ( $passthrough_keys as $key ) {
            if ( array_key_exists( $key, $input ) ) {
                $params[ $key ] = $input[ $key ];
            }
        }

        // Step 2: flatten the 'author' group.
        if ( isset( $input['author'] ) && is_array( $input['author'] ) ) {
            $author_group = $input['author'];
            if ( isset( $author_group['author'] ) && intval( $author_group['author'] ) > 0 ) {
                $params['author'] = intval( $author_group['author'] );
            }
            if ( ! empty( $author_group['author__in'] ) && is_array( $author_group['author__in'] ) ) {
                $params['author__in'] = array_map( 'intval', $author_group['author__in'] );
            }
            if ( ! empty( $author_group['author__not_in'] ) && is_array( $author_group['author__not_in'] ) ) {
                $params['author__not_in'] = array_map( 'intval', $author_group['author__not_in'] );
            }
        } elseif ( isset( $input['author'] ) && intval( $input['author'] ) > 0 ) {
            // Step 2b: plain scalar author value.
            $params['author'] = intval( $input['author'] );
        }

        // Step 3: flatten the 'order' group when it is a nested array.
        if ( isset( $input['order'] ) && is_array( $input['order'] ) ) {
            $order_group = $input['order'];
            if ( isset( $order_group['order'] ) ) {
                // 'order' inside the group is direction (ASC/DESC).
                $params['order_dir'] = strtoupper( sanitize_key( $order_group['order'] ) );
            }
            if ( isset( $order_group['orderby'] ) ) {
                $params['orderby'] = sanitize_key( $order_group['orderby'] );
            }
            if ( isset( $order_group['meta_key'] ) ) {
                $params['meta_key'] = sanitize_key( $order_group['meta_key'] );
            }
        }

        // Step 4: flatten the 'caching' group.
        if ( isset( $input['caching'] ) && is_array( $input['caching'] ) ) {
            $cache_keys = array(
                'cache_results',
                'no_found_rows',
                'suppress_filters',
                'update_post_meta_cache',
                'update_post_term_cache',
                'lazy_load_term_meta',
                'ignore_sticky_posts',
            );
            foreach ( $cache_keys as $key ) {
                if ( array_key_exists( $key, $input['caching'] ) ) {
                    $params[ $key ] = (bool) $input['caching'][ $key ];
                }
            }
        }

        // Step 5: flatten the 'meta' group (map of meta_key => value) into meta_query clauses.
        if ( isset( $input['meta'] ) && is_array( $input['meta'] ) ) {
            $existing = isset( $params['meta_query'] ) && is_array( $params['meta_query'] )
                ? $params['meta_query']
                : array();

            foreach ( $input['meta'] as $meta_key => $meta_value ) {
                if ( is_int( $meta_key ) ) {
                    // Numeric key — already a raw clause array, pass through.
                    if ( is_array( $meta_value ) ) {
                        $existing[] = $meta_value;
                    }
                    continue;
                }
                // Named key — build a simple equality clause.
                $existing[] = array(
                    'key'   => sanitize_key( $meta_key ),
                    'value' => $meta_value,
                );
            }
            $params['meta_query'] = $existing;
        }

        // Step 6: flatten the 'taxonomies' group (map of taxonomy => [term_ids/slugs/names]).
        if ( isset( $input['taxonomies'] ) && is_array( $input['taxonomies'] ) ) {
            // Map taxonomy slugs to the $params keys used by build_tax_query.
            // Param key IS the taxonomy name — identity mapping.
            $taxonomy_to_key = array(
                'property_category'        => 'property_category',
                'property_action_category' => 'property_action_category',
                'property_city'            => 'property_city',
                'property_area'            => 'property_area',
                'property_county_state'    => 'property_county_state',
                'property_status'          => 'property_status',
                'property_features'        => 'property_features',
            );

            // Build a raw tax_query for unknown taxonomies.
            $raw_tax_query = isset( $params['tax_query'] ) && is_array( $params['tax_query'] )
                ? $params['tax_query']
                : array();

            foreach ( $input['taxonomies'] as $taxonomy => $term_values ) {
                $term_values = (array) $term_values;
                if ( empty( $term_values ) ) {
                    continue;
                }

                if ( isset( $taxonomy_to_key[ $taxonomy ] ) ) {
                    // Step 6a: known taxonomy — store resolved term IDs in the named key.
                    $resolved_ids = array();
                    foreach ( $term_values as $term_value ) {
                        $term_value = trim( (string) $term_value );
                        if ( '' === $term_value ) {
                            continue;
                        }
                        if ( is_numeric( $term_value ) && intval( $term_value ) > 0 ) {
                            $resolved_ids[] = intval( $term_value );
                            continue;
                        }
                        $term = get_term_by( 'slug', $term_value, $taxonomy );
                        if ( ! $term || is_wp_error( $term ) ) {
                            $term = get_term_by( 'name', $term_value, $taxonomy );
                        }
                        if ( $term && ! is_wp_error( $term ) ) {
                            $resolved_ids[] = intval( $term->term_id );
                        }
                    }
                    if ( ! empty( $resolved_ids ) ) {
                        $param_key = $taxonomy_to_key[ $taxonomy ];
                        $existing  = isset( $params[ $param_key ] ) ? (array) $params[ $param_key ] : array();
                        $params[ $param_key ] = array_values( array_unique( array_merge( $existing, $resolved_ids ) ) );
                    }
                } else {
                    // Step 6b: unknown taxonomy — emit a raw tax_query clause.
                    $raw_tax_query[] = array(
                        'taxonomy' => sanitize_key( $taxonomy ),
                        'field'    => 'term_id',
                        'terms'    => array_map( 'intval', array_filter( $term_values, 'is_numeric' ) ),
                    );
                }
            }

            if ( ! empty( $raw_tax_query ) ) {
                $params['tax_query'] = $raw_tax_query;
            }
        }

        // Step 7: flatten the 'date' group into top-level WP_Query date keys.
        if ( isset( $input['date'] ) && is_array( $input['date'] ) ) {
            $date_keys = array( 'year', 'monthnum', 'w', 'day', 'hour', 'minute', 'second', 'm' );
            foreach ( $date_keys as $key ) {
                if ( array_key_exists( $key, $input['date'] ) ) {
                    $params[ $key ] = intval( $input['date'][ $key ] );
                }
            }
        }

        return $params;
    }
}
