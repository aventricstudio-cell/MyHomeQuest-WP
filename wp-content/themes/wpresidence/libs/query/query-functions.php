<?php
/**
 * Property query helper for taxonomy and meta filtering.
 *
 * This file centralizes all helper functions for querying the
 * `estate_property` custom post type. To perform a property query and keep
 * filters consistent across the theme:
 *
 * 1. Assemble a `$params` array describing the desired filters (paged,
 *    order, city, price ranges, etc.). The keys mirror WP_Query arguments
 *    plus the custom meta/taxonomy shortcuts supported by
 *    `wpestate_build_property_query()`.
 * 2. Build the final arguments:
 *    `$args = wpestate_build_property_query( $params );`
 * 3. Run the query either manually with `new WP_Query( $args )` or via the
 *    convenience wrapper:
 *    `$query = wpestate_run_property_query( $params );`
 *    The wrapper temporarily adds keyword and geolocation filters and then
 *    cleans them up to avoid side effects.
 * 4. Loop through `$query->posts` as with any `WP_Query` result.
 *
 * Refactor existing `estate_property` queries to follow this pattern for
 * consistent behaviour and easier maintenance across the theme.
 *
 * Example `$params` arrays:
 *
 * Basic pagination:
 *
 * ```
 * $params = array(
 *     'paged'          => 1,
 *     'posts_per_page' => 10,
 * );
 * ```
 *
 * Keyword and city filter:
 *
 * ```
 * $params = array(
 *     'keyword' => 'sea view',
 *     'city'    => 'San Francisco',
 *     'paged'   => get_query_var( 'paged', 1 ),
 * );
 * ```
 *
 * Taxonomy and meta filtering:
 *
 * ```
 * $params = array(
 *     'category'  => array( 'apartments', 'condos' ),
 *     'price_min' => 200000,
 *     'price_max' => 500000,
 *     'beds_min'  => 2,
 *     'order'     => 1,
 * );
 * ```
 *
 * Unknown keys are forwarded directly to `WP_Query`, allowing any additional
 * arguments such as `post__in` or `orderby` to be specified as needed.
 *
 * @package WPEstate
 */

if ( ! function_exists( 'wpestate_parse_adv_search_meta' ) ) {
    /**
     * Builds meta query clauses from advanced search definitions.
     *
     * The function expects the search values to be provided as an
     * associative array keyed by the sanitized slug of each field.
     * Only custom post meta fields are parsed; taxonomy filters are
     * ignored. Supported comparison operators mirror the legacy
     * advanced search implementation.
     *
     * @param array $values          Raw search values keyed by field slug.
     * @param array $adv_search_what Field identifiers.
     * @param array $adv_search_how  Comparison operators per field.
     * @param array $adv_search_label Default labels to skip empty values.
     * @return array Array of meta_query clauses.
     */
    function wpestate_parse_adv_search_meta( $values, $adv_search_what, $adv_search_how, $adv_search_label ) {
        $meta = array();
        // Step 1: bail early if no advanced definitions exist.
        if ( empty( $adv_search_what ) ) {
            return $meta;
        }

        // Step 2: iterate through each defined field and build a clause.
        foreach ( $adv_search_what as $key => $term ) {
            if ( empty( $term ) || is_array( $term ) ) {
                continue;
            }

            $term = strtolower( $term );
            // Step 3: skip fields handled elsewhere (taxonomies, special cases).
            $skip = array( 'property status', 'categories', 'types', 'cities', 'areas', 'county / state', 'wpestate location', 'keyword', 'property id', 'none', 'property price', 'property-price-v2', 'property-price-v3' );
            if ( in_array( $term, $skip, true ) ) {
                continue;
            }

            // Step 4: compute a safe meta key slug and make sure we have a value.
            $slug = sanitize_key( substr( sanitize_title( str_replace( ' ', '_', $term ) ), 0, 45 ) );
            if ( ! isset( $values[ $slug ] ) ) {
                continue;
            }

            $term_value = $values[ $slug ];
            // Step 5: skip empty values or ones identical to their default label.
            if ( isset( $adv_search_label[ $key ] ) ) {
                $default_label = $adv_search_label[ $key ];
                if ( $default_label === $term_value || '' === $term_value || 'all' === strtolower( $term_value ) ) {
                    continue;
                }
            }

            // Step 6: determine comparison operator and cast value accordingly.
            $compare = isset( $adv_search_how[ $key ] ) ? $adv_search_how[ $key ] : '';
            $clause  = array( 'key' => $slug );

            switch ( $compare ) {
                case 'equal':
                    $clause['compare'] = '=';
                    $clause['type']    = 'NUMERIC';
                    $clause['value']   = floatval( $term_value );
                    break;
                case 'greater':
                    $clause['compare'] = '>=';
                    $clause['type']    = 'NUMERIC';
                    $clause['value']   = floatval( $term_value );
                    break;
                case 'smaller':
                    $clause['compare'] = '<=';
                    $clause['type']    = 'NUMERIC';
                    $clause['value']   = floatval( $term_value );
                    break;
                case 'like':
                    $clause['compare'] = 'LIKE';
                    $clause['type']    = 'CHAR';
                    $clause['value']   = sanitize_text_field( $term_value );
                    break;
                case 'date bigger':
                    $clause['compare'] = '>=';
                    $clause['type']    = 'DATE';
                    $clause['value']   = sanitize_text_field( str_replace( ' ', '-', $term_value ) );
                    break;
                case 'date smaller':
                    $clause['compare'] = '<=';
                    $clause['type']    = 'DATE';
                    $clause['value']   = sanitize_text_field( str_replace( ' ', '-', $term_value ) );
                    break;
                default:
                    $clause['compare'] = '=';
                    $clause['value']   = sanitize_text_field( $term_value );
            }

            // Step 7: append clause to the final list.
            $meta[] = $clause;
        }

        return $meta;
    }
}

if ( ! function_exists( 'wpestate_parse_adv_search_price' ) ) {
    /**
     * Builds a property_price range clause for slider-based advanced search fields.
     *
     * Handles legacy identifiers 'property price', 'property-price-v2' and
     * 'property-price-v3'. Depending on the request context it reads min/max
     * values from either POST (ajax) or GET variables and converts them from
     * any selected custom currency.
     *
     * @param array  $adv_search_what  Field identifiers present in the form.
     * @param string $tip              Request context: 'ajax' for POST requests.
     * @param string $show_slider_price Whether the main price field uses a slider.
     * @return array                   Meta query clause or empty array.
     */
    function wpestate_parse_adv_search_price( $adv_search_what, $tip = '', $show_slider_price = '' ) {
        // Step 1: detect if a slider-based price field exists in the search form.
        $needs_price = false;
        foreach ( (array) $adv_search_what as $field ) {
            $field = strtolower( $field );
            if ( ( 'property price' === $field && 'yes' === $show_slider_price ) ||
                'property-price-v2' === $field ||
                'property-price-v3' === $field ) {
                $needs_price = true;
                break;
            }
        }
        if ( ! $needs_price ) {
            return array();
        }

        // Step 2: pull slider values from POST or GET based on request type.
        $price_low = 0;
        $price_max = 0;
        if ( 'ajax' === $tip ) {
            if ( isset( $_POST['slider_min'] ) ) {
                $price_low = floatval( $_POST['slider_min'] );
            }
            if ( isset( $_POST['slider_max'] ) ) {
                $price_max = floatval( $_POST['slider_max'] );
            }
        } else {
            if ( isset( $_GET['term_id'] ) && '' !== $_GET['term_id'] ) {
                $term_id   = intval( $_GET['term_id'] );
                $price_low = isset( $_GET[ 'price_low_' . $term_id ] ) ? floatval( $_GET[ 'price_low_' . $term_id ] ) : 0;
                $price_max = isset( $_GET[ 'price_max_' . $term_id ] ) ? floatval( $_GET[ 'price_max_' . $term_id ] ) : 0;
            } else {
                $price_low = isset( $_GET['price_low'] ) ? floatval( $_GET['price_low'] ) : 0;
                $price_max = isset( $_GET['price_max'] ) ? floatval( $_GET['price_max'] ) : 0;
            }
        }

        // Step 3: convert values from custom currency if one is selected.
        $custom_fields = wpresidence_get_option( 'wp_estate_multi_curr', '' );
        if ( ! empty( $custom_fields ) &&
            isset( $_COOKIE['my_custom_curr_pos'], $_COOKIE['my_custom_curr'], $_COOKIE['my_custom_curr_symbol'] ) &&
            intval( $_COOKIE['my_custom_curr_pos'] ) !== -1 ) {
            $i          = intval( $_COOKIE['my_custom_curr_pos'] );
            $multiplier = isset( $custom_fields[ $i ][2] ) ? $custom_fields[ $i ][2 ] : 1;
            if ( $multiplier > 0 ) {
                $price_low = $price_low / $multiplier;
                $price_max = $price_max / $multiplier;
            }
        }

        // Step 4: return a BETWEEN clause when we have a valid max price.
        if ( $price_max > 0 ) {
            return array(
                'key'     => 'property_price',
                'value'   => array( $price_low, $price_max ),
                'type'    => 'NUMERIC',
                'compare' => 'BETWEEN',
            );
        }

        // Step 5: otherwise return an empty array so no clause is added.
        return array();
    }
}

if ( ! function_exists( 'wpestate_build_property_tax_query' ) ) {
    /**
     * Builds a tax_query array for estate_property searches.
     *
     * Accepted parameters mirror those used by wpestate_build_property_query
     * but only taxonomy-related keys are honored.
     *
     * @param array $params Search parameters.
     * @return array        Tax query clauses.
     */
    function wpestate_build_property_tax_query( $params = array() ) {
        // Step 1: establish defaults and normalise input.
        $defaults = array(
            'category'     => array(),
            'action'       => array(),
            'city'         => array(),
            'area'         => array(),
            'county_state' => array(),
            'status'       => array(),
            'features'     => array(),
            'tax_relation' => 'AND',
        );

        $params    = wp_parse_args( $params, $defaults );
        $tax_query = array();

        // Step 2: map simple location filters to their taxonomies.
        $mapping = array(
            'category'     => 'property_category',
            'action'       => 'property_action_category',
            'city'         => 'property_city',
            'area'         => 'property_area',
            'county_state' => 'property_county_state',
            'status'       => 'property_status',
        );

        foreach ( $mapping as $key => $taxonomy ) {
            if ( ! empty( $params[ $key ] ) ) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => (array) $params[ $key ],
                );
            }
        }

        // Step 3: require all selected features by nesting them with AND relation.
        if ( ! empty( $params['features'] ) ) {
            $feature_array = array( 'relation' => 'AND' );
            foreach ( (array) $params['features'] as $term ) {
                $feature_array[] = array(
                    'taxonomy' => 'property_features',
                    'field'    => 'slug',
                    'terms'    => $term,
                );
            }
            $tax_query[] = $feature_array;
        }

        // Step 4: define relation between multiple taxonomy clauses.
        if ( count( $tax_query ) > 1 ) {
            $tax_query['relation'] = ( 'OR' === strtoupper( $params['tax_relation'] ) ) ? 'OR' : 'AND';
        }

        return $tax_query;
    }
}

if ( ! function_exists( 'wpestate_build_property_order_array' ) ) {
    /**
     * Builds orderby parameters for estate_property queries.
     *
     * @param int $order Order code matching legacy implementation.
     * @return array { 'order_array': [], 'transient_appendix': string }
     */
    function wpestate_build_property_order_array( $order ) {
        // Step 1: default to featured properties first.
        $meta_key  = 'prop_featured';
        $orderby   = 'meta_value_num';
        $direction = 'DESC';

        // Step 2: adjust meta key and direction based on incoming code.
        switch ( intval( $order ) ) {
            case 1:
                $meta_key  = 'property_price';
                $direction = 'DESC';
                $orderby   = 'meta_value_num';
                break;
            case 2:
                $meta_key  = 'property_price';
                $direction = 'ASC';
                $orderby   = 'meta_value_num';
                break;
            case 3:
                $meta_key  = '';
                $direction = 'DESC';
                $orderby   = 'ID';
                break;
            case 4:
                $meta_key  = '';
                $direction = 'ASC';
                $orderby   = 'ID';
                break;
            case 5:
                $meta_key  = 'property_bedrooms';
                $direction = 'DESC';
                $orderby   = 'meta_value_num';
                break;
            case 6:
                $meta_key  = 'property_bedrooms';
                $direction = 'ASC';
                $orderby   = 'meta_value_num';
                break;
            case 7:
                $meta_key  = 'property_bathrooms';
                $direction = 'DESC';
                $orderby   = 'meta_value_num';
                break;
            case 8:
                $meta_key  = 'property_bathrooms';
                $direction = 'ASC';
                $orderby   = 'meta_value_num';
                break;
            case 11:
                $meta_key  = '';
                $direction = 'DESC';
                $orderby   = 'modified';
                break;
            case 12:
                $meta_key  = '';
                $direction = 'ASC';
                $orderby   = 'modified';
                break;
            case 99:
                $meta_key  = '';
                $direction = 'ASC';
                $orderby   = 'rand';
                break;
        }

        // Step 3: build an appendix string for transient keys.
        $appendix = '_' . $meta_key . '_' . $direction;
        if ( 0 === intval( $order ) ) {
            $appendix .= '_myorder';
        }

        // Step 4: assemble the order array expected by WP_Query.
        $order_array = array(
            'orderby' => $orderby,
            'order'   => $direction,
        );
        if ( $meta_key ) {
            $order_array['meta_key'] = $meta_key;
        }

        return array(
            'order_array'        => $order_array,
            'transient_appendix' => $appendix,
        );
    }
}

if ( ! function_exists( 'wpestate_build_property_pagination_array' ) ) {
    /**
     * Builds pagination arguments for estate_property queries.
     *
     * @param int $paged         Current page number (1-based).
     * @param int $posts_per_page Number of posts per page.
     * @return array             Pagination arguments.
     */
    function wpestate_build_property_pagination_array( $paged = 1, $posts_per_page = 0 ) {
        // Step 1: ensure we have a valid page number.
        $paged = max( 1, intval( $paged ) );

        // Step 2: fall back to global posts_per_page option when empty.
        $per_page = intval( $posts_per_page );
        if ( $per_page <= 0 ) {
            $per_page = intval( get_option( 'posts_per_page', 10 ) );
        }

        // Step 3: return the pagination arguments expected by WP_Query.
        return array(
            'paged'          => $paged,
            'posts_per_page' => $per_page,
        );
    }
}

if ( ! function_exists( 'wpestate_build_property_meta_query' ) ) {
    /**
     * Builds a meta_query array for common property filters.
     *
     * Accepted parameters mirror those used by wpestate_build_property_query
     * but only meta-related keys are honored.
     *
     * @param array $params Search parameters.
     * @return array        Meta query clauses.
     */
    function wpestate_build_property_meta_query( $params = array() ) {
        // Step 1: establish defaults for supported meta filters.
        $defaults = array(
            'price_min'    => 0,
            'price_max'    => 0,
            'beds_min'     => 0,
            'beds_max'     => 0,
            'baths_min'    => 0,
            'baths_max'    => 0,
            'rooms_min'    => 0,
            'rooms_max'    => 0,
            'size_min'     => 0,
            'size_max'     => 0,
            'lot_size_min' => 0,
            'lot_size_max' => 0,
            'country'      => '',
            'agent'        => 0,
        );

        $params     = wp_parse_args( $params, $defaults );
        $meta_query = array();

        // Step 2: price range filtering.
        $price_min = floatval( $params['price_min'] );
        $price_max = floatval( $params['price_max'] );
        if ( $price_min || $price_max ) {
            if ( $price_min && $price_max ) {
                $meta_query[] = array(
                    'key'     => 'property_price',
                    'value'   => array( $price_min, $price_max ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            } elseif ( $price_min ) {
                $meta_query[] = array(
                    'key'     => 'property_price',
                    'value'   => $price_min,
                    'type'    => 'NUMERIC',
                    'compare' => '>=',
                );
            } else {
                $meta_query[] = array(
                    'key'     => 'property_price',
                    'value'   => $price_max,
                    'type'    => 'NUMERIC',
                    'compare' => '<=',
                );
            }
        }

        // Step 3: bedroom range.
        $beds_min = floatval( $params['beds_min'] );
        $beds_max = floatval( $params['beds_max'] );
        if ( $beds_min || $beds_max ) {
            if ( $beds_min && $beds_max ) {
                $meta_query[] = array(
                    'key'     => 'property_bedrooms',
                    'value'   => array( $beds_min, $beds_max ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            } elseif ( $beds_min ) {
                $meta_query[] = array(
                    'key'     => 'property_bedrooms',
                    'value'   => $beds_min,
                    'type'    => 'NUMERIC',
                    'compare' => '>=',
                );
            } else {
                $meta_query[] = array(
                    'key'     => 'property_bedrooms',
                    'value'   => $beds_max,
                    'type'    => 'NUMERIC',
                    'compare' => '<=',
                );
            }
        }

        // Step 4: bathroom range.
        $baths_min = floatval( $params['baths_min'] );
        $baths_max = floatval( $params['baths_max'] );
        if ( $baths_min || $baths_max ) {
            if ( $baths_min && $baths_max ) {
                $meta_query[] = array(
                    'key'     => 'property_bathrooms',
                    'value'   => array( $baths_min, $baths_max ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            } elseif ( $baths_min ) {
                $meta_query[] = array(
                    'key'     => 'property_bathrooms',
                    'value'   => $baths_min,
                    'type'    => 'NUMERIC',
                    'compare' => '>=',
                );
            } else {
                $meta_query[] = array(
                    'key'     => 'property_bathrooms',
                    'value'   => $baths_max,
                    'type'    => 'NUMERIC',
                    'compare' => '<=',
                );
            }
        }

        // Step 5: room range.
        $rooms_min = floatval( $params['rooms_min'] );
        $rooms_max = floatval( $params['rooms_max'] );
        if ( $rooms_min || $rooms_max ) {
            if ( $rooms_min && $rooms_max ) {
                $meta_query[] = array(
                    'key'     => 'property_rooms',
                    'value'   => array( $rooms_min, $rooms_max ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            } elseif ( $rooms_min ) {
                $meta_query[] = array(
                    'key'     => 'property_rooms',
                    'value'   => $rooms_min,
                    'type'    => 'NUMERIC',
                    'compare' => '>=',
                );
            } else {
                $meta_query[] = array(
                    'key'     => 'property_rooms',
                    'value'   => $rooms_max,
                    'type'    => 'NUMERIC',
                    'compare' => '<=',
                );
            }
        }

        // Step 6: property size range.
        $size_min = floatval( $params['size_min'] );
        $size_max = floatval( $params['size_max'] );
        if ( $size_min || $size_max ) {
            if ( $size_min && $size_max ) {
                $meta_query[] = array(
                    'key'     => 'property_size',
                    'value'   => array( $size_min, $size_max ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            } elseif ( $size_min ) {
                $meta_query[] = array(
                    'key'     => 'property_size',
                    'value'   => $size_min,
                    'type'    => 'NUMERIC',
                    'compare' => '>=',
                );
            } else {
                $meta_query[] = array(
                    'key'     => 'property_size',
                    'value'   => $size_max,
                    'type'    => 'NUMERIC',
                    'compare' => '<=',
                );
            }
        }

        // Step 7: lot size range.
        $lot_min = floatval( $params['lot_size_min'] );
        $lot_max = floatval( $params['lot_size_max'] );
        if ( $lot_min || $lot_max ) {
            if ( $lot_min && $lot_max ) {
                $meta_query[] = array(
                    'key'     => 'property_lot_size',
                    'value'   => array( $lot_min, $lot_max ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            } elseif ( $lot_min ) {
                $meta_query[] = array(
                    'key'     => 'property_lot_size',
                    'value'   => $lot_min,
                    'type'    => 'NUMERIC',
                    'compare' => '>=',
                );
            } else {
                $meta_query[] = array(
                    'key'     => 'property_lot_size',
                    'value'   => $lot_max,
                    'type'    => 'NUMERIC',
                    'compare' => '<=',
                );
            }
        }

        // Step 8: country and agent filters.
        if ( ! empty( $params['country'] ) ) {
            $meta_query[] = array(
                'key'     => 'property_country',
                'value'   => $params['country'],
                'compare' => 'LIKE',
            );
        }

        if ( $params['agent'] ) {
            $meta_query[] = array(
                'key'     => 'property_agent',
                'value'   => intval( $params['agent'] ),
                'type'    => 'NUMERIC',
                'compare' => '=',
            );
        }

        return $meta_query;
    }
}

if ( ! function_exists( 'wpestate_geo_search_filter_function' ) ) {
    /**
     * Registers a geolocation constraint that will be applied via `posts_clauses`.
     *
     * Instead of pre-fetching post IDs and injecting them through `post__in`,
     * this helper stores the latitude, longitude, and radius in the query
     * arguments. When `wpestate_run_property_query()` executes, it hooks into
     * the SQL clauses so the distance calculation is performed directly in the
     * main query.
     *
     * @param array $args        Existing WP_Query arguments.
     * @param float $center_lat  Latitude of the center point.
     * @param float $center_long Longitude of the center point.
     * @param float $radius      Search radius around the point.
     * @return array             Modified query arguments with geo data.
     */
    function wpestate_geo_search_filter_function( $args, $center_lat, $center_long, $radius ) {
        // Store the geolocation parameters for later use. The actual SQL
        // modification is performed by `wpestate_geo_search_clauses`, which is
        // attached within `wpestate_run_property_query()` to keep filters
        // localized to the query execution.
        $args['wpestate_geo_query'] = array(
            'lat'    => $center_lat,
            'long'   => $center_long,
            'radius' => $radius,
        );

        return $args;
    }
}

if ( ! function_exists( 'wpestate_geo_search_clauses' ) ) {
    /**
     * Modifies SQL clauses to restrict results to a radius around a point.
     *
     * @param array    $clauses Existing SQL clauses.
     * @param WP_Query $query   Current query instance.
     * @return array            Updated SQL clauses with geolocation filter.
     */
    function wpestate_geo_search_clauses( $clauses, $query ) {
        $geo = $query->get( 'wpestate_geo_query' );
        if ( empty( $geo['lat'] ) || empty( $geo['long'] ) || empty( $geo['radius'] ) ) {
            return $clauses;
        }

        global $wpdb;

        // Determine earth radius based on measurement option.
        $radius_measure = wpresidence_get_option( 'wp_estate_geo_radius_measure', '' );
        $earth          = ( 'km' === $radius_measure ) ? 6371 : 3959;

        $lat    = floatval( $geo['lat'] );
        $long   = floatval( $geo['long'] );
        $radius = floatval( $geo['radius'] );

        // Join latitude and longitude meta.
        $clauses['join'] .= " INNER JOIN {$wpdb->postmeta} AS latitude ON {$wpdb->posts}.ID = latitude.post_id AND latitude.meta_key='property_latitude'";
        $clauses['join'] .= " INNER JOIN {$wpdb->postmeta} AS longitude ON {$wpdb->posts}.ID = longitude.post_id AND longitude.meta_key='property_longitude'";

        // Calculate distance and filter by radius.
        $distance_sql = $wpdb->prepare(
            "%f * acos( cos( radians(%f) ) * cos( radians( latitude.meta_value ) ) * cos( radians( longitude.meta_value ) - radians(%f) ) + sin( radians(%f) ) * sin( radians( latitude.meta_value ) ) )",
            $earth,
            $lat,
            $long,
            $lat
        );

        $clauses['fields']  .= ", $distance_sql AS distance";
        $clauses['where']   .= $wpdb->prepare( " AND ($distance_sql) < %f", $radius );
        $clauses['orderby']  = "distance ASC, {$clauses['orderby']}";

        // Remove the filter so it doesn't affect other queries.
        remove_filter( 'posts_clauses', __FUNCTION__, 10 );

        return $clauses;
    }
}

if ( ! function_exists( 'wpestate_build_property_geo_query' ) ) {
    /**
     * Applies a geolocation radius filter to query arguments.
     *
     * @param array       $args   Existing WP_Query arguments.
     * @param float|string $lat    Latitude of the center point.
     * @param float|string $long   Longitude of the center point.
     * @param float|string $radius Search radius around the point.
     * @return array               Modified WP_Query arguments.
     */
    function wpestate_build_property_geo_query( $args, $lat = '', $long = '', $radius = 0 ) {
        // Step 1: confirm geolocation is enabled and coordinates were provided.
        if ( 'yes' !== wpresidence_get_option( 'wp_estate_use_geo_location', '' ) || '' === $lat || '' === $long ) {
            return $args;
        }

        // Step 2: sanitize numeric inputs and delegate to the SQL based helper.
        $lat    = floatval( $lat );
        $long   = floatval( $long );
        $radius = floatval( $radius );

        return wpestate_geo_search_filter_function( $args, $lat, $long, $radius );
    }
}

if ( ! function_exists( 'wpestate_keyword_where' ) ) {
    /**
     * Restrict default search to post titles only.
     *
     * Added automatically by `wpestate_run_property_query()` when the
     * `keyword` parameter is present. The filter removes itself after the
     * first query to avoid side effects on subsequent queries.
     *
     * @param string   $where  The current WHERE clause.
     * @param WP_Query $query  The query instance.
     * @return string Modified WHERE clause.
     */
    function wpestate_keyword_where( $where, $query ) {
        global $wpdb;

        $search = $query->get( 's' );
        if ( ! empty( $search ) ) {
            $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_title LIKE %s", '%' . $wpdb->esc_like( $search ) . '%' );
        }

        remove_filter( 'posts_where', __FUNCTION__, 10 );
        return $where;
    }
}
if ( ! function_exists( 'wpestate_remove_sold_listings' ) ) {
    /**
     * Excludes properties marked as sold when the theme option hides them.
     *
     * The function appends a `tax_query` clause that omits the configured
     * "sold" status term so search results only include available listings.
     *
     * @param array $args Existing WP_Query arguments.
     * @return array Modified arguments with sold properties excluded.
     */
    function wpestate_remove_sold_listings( $args ) {
        // Step 1: check whether sold items should be displayed.
        $show_sold = wpresidence_get_option( 'wp_estate_show_sold_items', '' );
        if ( 'no' !== $show_sold ) {
            return $args;
        }

        // Step 2: fetch the term ID representing the "sold" status.
        $sold_id = intval( wpresidence_get_option( 'wpestate_mark_sold_status', '' ) );
        if ( ! $sold_id ) {
            return $args;
        }

        // Step 3: build an exclusion clause for the sold status taxonomy term.
        $exclude = array(
            'taxonomy' => 'property_status',
            'field'    => 'term_id',
            'terms'    => array( $sold_id ),
            'operator' => 'NOT IN',
        );

        // Step 4: merge the clause into any existing tax_query array.
        if ( isset( $args['tax_query'] ) ) {
            $args['tax_query'][] = $exclude;
        } else {
            $args['tax_query'] = array(
                'relation' => 'AND',
                $exclude,
            );
        }

        return $args;
    }
}

if ( ! function_exists( 'wpestate_build_property_query' ) ) {
    /**
     * Constructs WP_Query args for searches on estate_property.
     *
     * Supported taxonomy parameters (string or array of slugs):
     * - category       => property_category
     * - action         => property_action_category
     * - city           => property_city
     * - area           => property_area
     * - county_state   => property_county_state
     * - status         => property_status
     * - features       => property_features (requires all selected features)
     * - tax_relation   => relation between taxonomies (AND|OR, default AND)
     *
     * Supported meta parameters:
     * - price_min / price_max    (numeric range on property_price)
     * - beds_min  / beds_max     (range on property_bedrooms)
     * - baths_min / baths_max    (range on property_bathrooms)
     * - rooms_min / rooms_max    (range on property_rooms)
     * - size_min / size_max      (range on property_size)
     * - lot_size_min / lot_size_max (range on property_lot_size)
     * - country             (LIKE match on property_country)
       * - agent               (= property_agent ID)
       * - meta                (extra raw meta_query clauses)
       * - meta_relation       (AND|OR relation between meta clauses, default AND)
       * - fields              (fields to return, defaults to 'ids')
       * - order               (ordering code handled by wpestate_build_property_order_array)
       * - paged               (current page number, default 1)
       * - posts_per_page      (results per page, falls back to WP option)
       * - include             (array of specific property IDs to query)
       * - no_found_rows       (skip row count query when true, default true)
       * - keyword             (search term matched against post titles)
       *
       * @param array $params Search parameters.
       * @return array WP_Query arguments ready for execution.
       */
    function wpestate_build_property_query( $params = array() ) {
        // Step 1: define defaults for all supported filters.
        $defaults = array(
            // taxonomy filters
            'category'      => array(),
            'action'        => array(),
            'city'          => array(),
            'area'          => array(),
            'county_state'  => array(),
            'status'        => array(),
            'features'      => array(),
            'tax_relation'  => 'AND',

            // meta filters
            'price_min'     => 0,
            'price_max'     => 0,
            'beds_min'      => 0,
            'beds_max'      => 0,
            'baths_min'     => 0,
            'baths_max'     => 0,
            'rooms_min'     => 0,
            'rooms_max'     => 0,
            'size_min'      => 0,
            'size_max'      => 0,
            'lot_size_min'  => 0,
            'lot_size_max'  => 0,
            'country'       => '',
            'agent'         => 0,
            'meta'          => array(),
            'meta_relation' => 'AND',

            // advanced search definitions
            'adv_search_what'  => array(),
            'adv_search_how'   => array(),
            'adv_search_label' => array(),
            'adv_values'       => array(),
            'show_slider_price'=> '',
            'tip'             => '',

            // geolocation
            'geo_lat'       => '',
            'geo_long'      => '',
            'geo_rad'       => 0,

            // query behaviour
            'keyword'       => '',
            'fields'        => 'ids',
            'order'         => 0,
            'paged'         => 1,
            'posts_per_page'=> 0,
            'include'       => array(),
            'no_found_rows' => true,
        );

        $params = wp_parse_args( $params, $defaults );

        // Step 2: build taxonomy clauses.
        $tax_query  = wpestate_build_property_tax_query( $params );

        // Step 3: build meta clauses for standard property fields.
        $meta_query = wpestate_build_property_meta_query( $params );

        // Step 4: parse advanced search meta definitions and merge.
        $adv_meta = wpestate_parse_adv_search_meta(
            $params['adv_values'],
            $params['adv_search_what'],
            $params['adv_search_how'],
            $params['adv_search_label']
        );
        if ( ! empty( $adv_meta ) ) {
            $meta_query = array_merge( $meta_query, $adv_meta );
        }

        // Step 5: include price range from slider-based advanced search fields.
        $price_clause = wpestate_parse_adv_search_price(
            $params['adv_search_what'],
            $params['tip'],
            $params['show_slider_price']
        );
        if ( ! empty( $price_clause ) ) {
            $meta_query[] = $price_clause;
        }

        // Step 6: merge any additional raw meta clauses supplied by caller.
        if ( ! empty( $params['meta'] ) && is_array( $params['meta'] ) ) {
            foreach ( $params['meta'] as $clause ) {
                if ( is_array( $clause ) ) {
                    $meta_query[] = $clause;
                }
            }
        }

        // Step 7: define relation between meta clauses if needed.
        if ( count( $meta_query ) > 1 ) {
            $meta_query['relation'] = ( 'OR' === strtoupper( $params['meta_relation'] ) ) ? 'OR' : 'AND';
        }

        // Step 8: prepare lean WP_Query arguments for performance.
        $args = array(
            'post_type'              => 'estate_property',
            'post_status'            => 'publish',
            'fields'                 => $params['fields'],
            'no_found_rows'          => (bool) $params['no_found_rows'],
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
            'cache_results'          => false,
        );

        // Step 9: merge pagination parameters.
        $args = array_merge(
            $args,
            wpestate_build_property_pagination_array( $params['paged'], $params['posts_per_page'] )
        );

        // Step 10: attach taxonomy and meta queries if present.
        if ( ! empty( $tax_query ) ) {
            $args['tax_query'] = $tax_query;
        }

        if ( ! empty( $meta_query ) ) {
            $args['meta_query'] = $meta_query;
        }

        // Step 11: apply ordering preferences.
        $order_parts = wpestate_build_property_order_array( $params['order'] );
        $args        = array_merge( $args, $order_parts['order_array'] );

        // Step 12: search by keyword in post titles only. The keyword filter is
        // attached later by `wpestate_run_property_query()` so that it can be
        // removed immediately after the query executes.
        if ( ! empty( $params['keyword'] ) ) {
            $args['s'] = sanitize_text_field( $params['keyword'] );
        }

        // Step 13: filter by geolocation if coordinates were provided.
        $args = wpestate_build_property_geo_query(
            $args,
            $params['geo_lat'],
            $params['geo_long'],
            $params['geo_rad']
        );

        // Step 14: limit results to specific property IDs when requested.
        if ( ! empty( $params['include'] ) ) {
            $args['post__in'] = array_map( 'intval', (array) $params['include'] );
        }

        // Step 15: exclude sold listings when the setting hides them.
        $args = wpestate_remove_sold_listings( $args );

        return $args;
    }
}

if ( ! function_exists( 'wpestate_run_property_query_classic' ) ) {
    /**
     * Executes a WP_Query for estate_property based on provided parameters.
     *
     * This wrapper builds the query arguments via wpestate_build_property_query(),
     * attaches any temporary filters (such as keyword or geolocation filters),
     * and ensures they are cleaned up after the query runs.
     *
     * @param array $params Search parameters understood by wpestate_build_property_query().
     * @return WP_Query      The resulting query object.
     */
     function wpestate_run_property_query_classic( $params = array() ) {
        $args = wpestate_build_property_query( $params );

        // Attach temporary filters required for keywords or geolocation.
        if ( ! empty( $params['keyword'] ) ) {
            add_filter( 'posts_where', 'wpestate_keyword_where', 10, 2 );
        }
        if ( ! empty( $args['wpestate_geo_query'] ) ) {
            add_filter( 'posts_clauses', 'wpestate_geo_search_clauses', 10, 2 );
        }

        // Handle special order case for custom ordering
        if ( isset( $params['order'] ) && 0 === intval( $params['order'] ) && function_exists( 'wpestate_return_filtered_by_order' ) ) {
            $query = wpestate_return_filtered_by_order( $args );
        } else {
            $query = new WP_Query( $args );
        }

        // Remove filters after the query to avoid affecting other queries.
        if ( ! empty( $params['keyword'] ) ) {
            remove_filter( 'posts_where', 'wpestate_keyword_where', 10 );
        }
        if ( ! empty( $args['wpestate_geo_query'] ) ) {
            remove_filter( 'posts_clauses', 'wpestate_geo_search_clauses', 10 );
        }

        return $query;
    }
}

if ( ! function_exists( 'wpestate_run_property_query' ) ) {
	/**
	 * Main query entrypoint with classic/index dispatcher.
	 *
	 * @param array $params Search parameters understood by wpestate_build_property_query().
	 *
	 * @return WP_Query
	 */
	function wpestate_run_property_query( $params = array() ) {
		return wpestate_run_property_query_classic( $params );
	}
}








if ( ! function_exists( 'wpestate_return_filtered_by_order' ) ) {
    /**
     * Runs a property query with the custom order filter applied.
     *
     * @param array $args Query arguments for WP_Query.
     * @return WP_Query The resulting query object.
     */
    function wpestate_return_filtered_by_order( $args ) {
        // Step 1: attach our custom SQL ORDER BY filter.
        add_filter( 'posts_orderby', 'wpestate_my_order' );

        // Step 2: execute the query with ordering in place.
        $prop_selection = new WP_Query( $args );

        // Step 3: remove the filter to avoid affecting other queries.
        remove_filter( 'posts_orderby', 'wpestate_my_order' );

        // Step 4: return the query results.
        return $prop_selection;
    }
}
