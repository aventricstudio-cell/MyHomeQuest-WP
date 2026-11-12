<?php
/**
 * Search arguments builder for the 2026 estate_property query layer.
 *
 * Converts raw $_GET search params into canonical $params for
 * wpestate_query_2026_property( 'directory', $params ).
 *
 * File: libs/query/query2026/wpestate_query_2026_prepare_search_arguments.php
 * @package WPEstate
 */

if ( ! function_exists( 'wpestate_query_2026_resolve_search_compare' ) ) {
    /**
     * Maps an admin adv_search_how string to a WP_Query compare/type pair.
     *
     * @param string $how_value  Comparison label from theme option.
     * @return array { 'compare' => string, 'type' => string }
     */
    function wpestate_query_2026_resolve_search_compare( $how_value ) {
        $how = strtolower( trim( (string) $how_value ) );
        switch ( $how ) {
            case 'equal':
                return array( 'compare' => '=', 'type' => 'NUMERIC' );
            case 'greater':
                return array( 'compare' => '>=', 'type' => 'NUMERIC' );
            case 'smaller':
                return array( 'compare' => '<=', 'type' => 'NUMERIC' );
            case 'like':
                return array( 'compare' => 'LIKE', 'type' => 'CHAR' );
            case 'date bigger':
                return array( 'compare' => '>=', 'type' => 'DATE' );
            case 'date smaller':
                return array( 'compare' => '<=', 'type' => 'DATE' );
            default:
                return array( 'compare' => '>=', 'type' => 'NUMERIC' );
        }
    }
}

if ( ! function_exists( 'wpestate_query_2026_build_search_compare_map' ) ) {
    /**
     * Builds meta_key => compare/type lookup for all searchable fields.
     * Merges hardcoded defaults with admin-configured adv_search_what/how arrays.
     *
     * @param array|null $what_override  Optional adv_search_what override (Elementor forms).
     * @param array|null $how_override   Optional adv_search_how override (Elementor forms).
     * @return array  Keyed by meta_key, values are { 'compare', 'type' }.
     */
    function wpestate_query_2026_build_search_compare_map( $what_override = null, $how_override = null ) {
        // Step 1: hardcoded defaults for built-in property meta keys.
        $map = array(
            'property_bedrooms'  => array( 'compare' => '>=', 'type' => 'NUMERIC' ),
            'property_bathrooms' => array( 'compare' => '>=', 'type' => 'NUMERIC' ),
            'property_rooms'     => array( 'compare' => '>=', 'type' => 'NUMERIC' ),
            'property_size'      => array( 'compare' => '>=', 'type' => 'NUMERIC' ),
            'property_lot_size'  => array( 'compare' => '>=', 'type' => 'NUMERIC' ),
            'property_address'   => array( 'compare' => 'LIKE', 'type' => 'CHAR' ),
            'property_zip'       => array( 'compare' => '=', 'type' => 'CHAR' ),
            'property_country'   => array( 'compare' => 'LIKE', 'type' => 'CHAR' ),
        );
        // Step 2: use override arrays when provided (Elementor), else read theme options.
        $what = ( null !== $what_override ) ? $what_override : wpresidence_get_option( 'wp_estate_adv_search_what', '' );
        $how  = ( null !== $how_override )  ? $how_override  : wpresidence_get_option( 'wp_estate_adv_search_how', '' );
        // Step 3: bail early if options are empty or not arrays.
        if ( empty( $what ) || ! is_array( $what ) || empty( $how ) || ! is_array( $how ) ) {
            return $map;
        }
        // Step 4: types to skip — handled by dedicated logic, not generic meta.
        $skip_types = array_flip( array(
            'types', 'categories', 'cities', 'areas', 'county / state',
            'property status', 'keyword', 'property id', 'geolocation',
            'geolocation_radius', 'beds-baths', 'none', 'wpestate location',
            'property price', 'property-price-v2', 'property-price-v3',
        ) );
        // Step 5: iterate and build map entries for each admin-configured field.
        $count = count( $what );
        for ( $i = 0; $i < $count; $i++ ) {
            $field_label = trim( (string) $what[ $i ] );
            // Skip taxonomy, special, and price types.
            if ( isset( $skip_types[ $field_label ] ) ) {
                continue;
            }
            // Resolve the meta key from admin label.
            $meta_key = wpestate_map_adv_search_field_to_meta_key( $field_label );
            // For custom fields not in the built-in map, derive key same as legacy code.
            if ( $meta_key === $field_label && ! isset( $map[ $meta_key ] ) ) {
                $meta_key = sanitize_key(
                    wpestate_limit45( sanitize_title( str_replace( ' ', '_', $field_label ) ) )
                );
            }
            if ( '' === $meta_key ) {
                continue;
            }
            // Resolve compare/type from the parallel how array.
            $how_value         = isset( $how[ $i ] ) ? $how[ $i ] : '';
            $map[ $meta_key ]  = wpestate_query_2026_resolve_search_compare( $how_value );
        }
        return $map;
    }
}

if ( ! function_exists( 'wpestate_query_2026_convert_price_to_base' ) ) {
    /**
     * Converts a user-facing price value to base currency using cookie rate.
     *
     * @param float $value  The price value to convert.
     * @return float        The price in base currency.
     */
    function wpestate_query_2026_convert_price_to_base( $value ) {
        // Step 1: check for active multi-currency cookie — bail if not set.
        if ( ! isset( $_COOKIE['my_custom_curr'] ) || ! isset( $_COOKIE['my_custom_curr_pos'] )
            || ! isset( $_COOKIE['my_custom_curr_symbol'] ) || intval( $_COOKIE['my_custom_curr_pos'] ) === -1 ) {
            return $value;
        }
        // Step 2: read multi-currency rates and divide by exchange rate.
        $multi_curr = wpresidence_get_option( 'wp_estate_multi_curr', '' );
        if ( empty( $multi_curr ) || ! is_array( $multi_curr ) ) {
            return $value;
        }
        $pos  = intval( $_COOKIE['my_custom_curr_pos'] );
        $rate = isset( $multi_curr[ $pos ][2] ) ? floatval( $multi_curr[ $pos ][2] ) : 0;
        return ( $rate > 0 ) ? $value / $rate : $value;
    }
}

if ( ! function_exists( 'wpestate_query_2026_prepare_search_arguments' ) ) {
    /**
     * Converts raw search request params into 2026 canonical $params.
     *
     * @param array|null $request  Raw GET params. Defaults to $_GET when null.
     * @param array      $options  Optional overrides: 'adv_search_what', 'adv_search_how' (Elementor).
     * @return array               Canonical 2026 params with optional meta_query.
     */
    function wpestate_query_2026_prepare_search_arguments( $request = null, $options = array() ) {
        // Step 1: default to $_GET when no request array is provided.
        if ( null === $request ) {
            $request = $_GET;
        }
        // Step 2: keys to skip — internal/nonce/UI-only fields.
        $skip = array_flip( array(
            'wpestate_regular_search_nonce', 'is2', 'is10', 'is11',
            'elementor_form_id', 'term_counter', 'term_id',
            'half_map_search_version', 'geolocation_search',
            'adv6_search_tab', 'wpestate_nonce',
        ) );
        // Step 3: known taxonomy set for fast lookup.
        $known_taxonomies = array_flip( array(
            'property_category', 'property_action_category', 'property_city',
            'property_area', 'property_county_state', 'property_status',
        ) );
        // Step 4: build compare map, forwarding Elementor overrides if present.
        $what_override = isset( $options['adv_search_what'] ) ? $options['adv_search_what'] : null;
        $how_override  = isset( $options['adv_search_how'] )  ? $options['adv_search_how']  : null;
        $compare_map   = wpestate_query_2026_build_search_compare_map( $what_override, $how_override );
        // Step 5: load property_features terms and build slug lookup map.
        $feature_slugs = array();
        $feature_terms = wpestate_get_cached_terms( 'property_features' );
        if ( ! empty( $feature_terms ) && is_array( $feature_terms ) ) {
            foreach ( $feature_terms as $term ) {
                // Strip percent-encoding from slug for matching GET keys.
                $clean_slug = str_replace( '%', '', $term->slug );
                $feature_slugs[ $clean_slug ] = $term->slug;
            }
        }
        // Step 6: initialize output arrays.
        $params             = array();
        $meta_query_clauses = array();

        // ── Main loop ──
        foreach ( $request as $key => $raw_value ) {
            // A. Skip internal/nonce keys.
            if ( isset( $skip[ $key ] ) ) {
                continue;
            }
            // B. Feature term in slug=1 format (e.g. ?swimming-pool=1).
            if ( isset( $feature_slugs[ $key ] ) && intval( $raw_value ) === 1 ) {
                $params['property_features'][] = $feature_slugs[ $key ];
                continue;
            }
            // C. Known taxonomy param (e.g. property_city=new-york).
            if ( isset( $known_taxonomies[ $key ] ) ) {
                $values = is_array( $raw_value ) ? $raw_value : array( $raw_value );
                $clean  = array();
                foreach ( $values as $v ) {
                    $sanitized = sanitize_text_field( wp_unslash( (string) $v ) );
                    if ( '' !== $sanitized && 'all' !== strtolower( $sanitized ) ) {
                        $clean[] = $sanitized;
                    }
                }
                if ( ! empty( $clean ) ) {
                    $params[ $key ] = $clean;
                }
                continue;
            }
            // D. property_features in array format (e.g. ?property_features[]=pool).
            if ( 'property_features' === $key ) {
                $values = is_array( $raw_value ) ? $raw_value : array( $raw_value );
                foreach ( $values as $v ) {
                    $sanitized = sanitize_text_field( wp_unslash( (string) $v ) );
                    if ( '' !== $sanitized ) {
                        $params['property_features'][] = $sanitized;
                    }
                }
                continue;
            }
            // E. Price params (price_low, price_min, price_max, price_low_{N}, price_max_{N}).
            if ( preg_match( '/^price_(low|min|max)(?:_\d+)?$/', $key, $price_match ) ) {
                $price_val = floatval( $raw_value );
                if ( $price_val > 0 ) {
                    $converted = wpestate_query_2026_convert_price_to_base( $price_val );
                    // Step 1: Normalize the minimum-price aliases so both old and new URLs work.
                    $price_compare = $price_match[1];
                    if ( 'min' === $price_compare ) {
                        $price_compare = 'low';
                    }

                    // Step 2: Build the canonical property_price clause for the 2026 query layer.
                    $meta_query_clauses[] = array(
                        'key'     => 'property_price',
                        'value'   => $converted,
                        'compare' => ( 'low' === $price_compare ) ? '>=' : '<=',
                        'type'    => 'NUMERIC',
                    );
                }
                continue;
            }
            // F. Special params — keyword, geo, order, paged, location.
            $special_handled = true;
            switch ( $key ) {
                case 'keyword': // fall through
                case 's':
                    $kw = sanitize_text_field( wp_unslash( (string) $raw_value ) );
                    if ( '' !== $kw ) { $params['keyword'] = $kw; }
                    break;
                case 'geolocation_lat':
                    $lat = floatval( $raw_value );
                    if ( 0.0 !== $lat ) { $params['geo_lat'] = $lat; }
                    break;
                case 'geolocation_long':
                    $lng = floatval( $raw_value );
                    if ( 0.0 !== $lng ) { $params['geo_long'] = $lng; }
                    break;
                case 'geolocation_radius':
                    $rad = floatval( $raw_value );
                    if ( $rad > 0 ) { $params['geo_rad'] = $rad; }
                    break;
                case 'order_search': // fall through
                case 'order':   $params['order'] = intval( $raw_value ); break;
                case 'paged':   $params['paged'] = intval( $raw_value ); break;
                case 'location':
                    // Route to 2026 OR taxonomy search (city/area/county_state).
                    $loc = sanitize_text_field( wp_unslash( (string) $raw_value ) );
                    if ( '' !== $loc ) {
                        $params['location'] = $loc;
                    }
                    break;
                case 'componentsbeds':
                    // Beds-baths component UI → canonical meta key.
                    $beds = floatval( $raw_value );
                    if ( $beds > 0 ) {
                        $meta_query_clauses[] = array(
                            'key'     => 'property_bedrooms',
                            'value'   => $beds,
                            'compare' => '>=',
                            'type'    => 'NUMERIC',
                        );
                    }
                    break;
                case 'componentsbaths':
                    // Beds-baths component UI → canonical meta key.
                    $baths = floatval( $raw_value );
                    if ( $baths > 0 ) {
                        $meta_query_clauses[] = array(
                            'key'     => 'property_bathrooms',
                            'value'   => $baths,
                            'compare' => '>=',
                            'type'    => 'NUMERIC',
                        );
                    }
                    break;
                default: $special_handled = false; break;
            }
            if ( $special_handled ) { continue; }
            // G. Generic meta — only admin-configured fields pass through.
            // Skip array values — they are taxonomy/feature params handled above.
            if ( is_array( $raw_value ) ) {
                continue;
            }
            $meta_key = sanitize_key( $key );
            if ( '' === $meta_key ) {
                continue;
            }
            $value = sanitize_text_field( wp_unslash( (string) $raw_value ) );
            if ( '' === $value || 'all' === strtolower( $value ) ) {
                continue;
            }
            // Security gate: reject keys not in the compare map.
            if ( ! isset( $compare_map[ $meta_key ] ) ) {
                continue;
            }
            // Cast value by compare type.
            $comp = $compare_map[ $meta_key ];
            switch ( $comp['type'] ) {
                case 'NUMERIC':
                    $cast_value = floatval( $value );
                    break;
                case 'DATE':
                    $cast_value = str_replace( ' ', '-', $value );
                    break;
                default:
                    $cast_value = $value;
                    break;
            }
            $meta_query_clauses[] = array(
                'key'     => $meta_key,
                'value'   => $cast_value,
                'compare' => $comp['compare'],
                'type'    => $comp['type'],
            );
        }

        // ── Post-loop: attach meta_query clauses if any were built. ──
        if ( ! empty( $meta_query_clauses ) ) {
            $params['meta_query'] = $meta_query_clauses;
        }
        // Allow child themes to modify search params (e.g. map custom form fields to meta_query).
        $params = apply_filters( 'wpestate_query_2026_search_params', $params, $request, $options );

        return $params;
    }
}
