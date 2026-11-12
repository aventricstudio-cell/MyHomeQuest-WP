<?php
/**
 * Clause builders for the 2026 estate_property query layer.
 *
 * Holds three pure-builder functions that convert flat $params into
 * WP_Query tax_query and meta_query clause arrays. No side effects.
 *
 * File: libs/query/query2026/wpestate_query_2026_property_placeholders.php
 *
 * @package WPEstate
 */

if ( ! function_exists( 'wpestate_query_2026_property_build_tax_query' ) ) {
    /**
     * Builds the tax_query array from canonical params.
     *
     * Supports: status, category, action, city, area, county_state, features,
     * and tax_relation. Uses get_term_by() for non-Latin safe slug/name
     * resolution so slugs with non-ASCII characters resolve correctly.
     *
     * @param array $params Canonical params array.
     * @return array        WP_Query tax_query clauses.
     */
    function wpestate_query_2026_property_build_tax_query( $params ) {
        $tax_query = array();

        // Step 1: map flat param keys to their WordPress taxonomy slugs.
        // Param key IS the taxonomy name, so key === value for all entries.
        $mapping = array(
            'property_category'        => 'property_category',
            'property_action_category' => 'property_action_category',
            'property_city'            => 'property_city',
            'property_area'            => 'property_area',
            'property_county_state'    => 'property_county_state',
            'property_status'          => 'property_status',
        );

        // Step 2: for each taxonomy, resolve each value to a term ID using
        // get_term_by() so non-Latin slugs and display names both work.
        foreach ( $mapping as $key => $taxonomy ) {
            if ( empty( $params[ $key ] ) ) {
                continue;
            }

            $term_ids = array();
            foreach ( (array) $params[ $key ] as $term_value ) {
                $term_value = trim( (string) $term_value );
                if ( '' === $term_value ) {
                    continue;
                }

                // Step 2a: if a numeric value was provided, treat it as a term ID directly.
                if ( is_numeric( $term_value ) && intval( $term_value ) > 0 ) {
                    $term_ids[] = intval( $term_value );
                    continue;
                }

                // Step 2b: try by slug first (most common case).
                $term = get_term_by( 'slug', $term_value, $taxonomy );

                // Step 2c: fall back to name lookup for non-Latin safe resolution.
                if ( ! $term || is_wp_error( $term ) ) {
                    $term = get_term_by( 'name', $term_value, $taxonomy );
                }

                if ( $term && ! is_wp_error( $term ) ) {
                    $term_ids[] = intval( $term->term_id );
                    //error_log( '[wq2026 tax] ' . $taxonomy . ' | "' . $term_value . '" => term_id ' . $term->term_id . ' (' . $term->slug . ')' );
                } else {
                    //error_log( '[wq2026 tax] ' . $taxonomy . ' | "' . $term_value . '" => NOT FOUND' );
                }
            }

            if ( ! empty( $term_ids ) ) {
                $tax_query[] = array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term_ids,
                );
            }
        }

        // Step 3: features use a single clause with operator=AND so the post must
        // carry every selected feature term (WP_Query native AND operator support).
        if ( ! empty( $params['property_features'] ) ) {
            $feature_term_ids = array();
            foreach ( (array) $params['property_features'] as $term_value ) {
                $term_value = trim( (string) $term_value );
                if ( '' === $term_value ) {
                    continue;
                }

                // Numeric values are used directly as term IDs.
                if ( is_numeric( $term_value ) && intval( $term_value ) > 0 ) {
                    $feature_term_ids[] = intval( $term_value );
                } else {
                    // Non-Latin safe: try slug first, then name.
                    $term = get_term_by( 'slug', $term_value, 'property_features' );
                    if ( ! $term || is_wp_error( $term ) ) {
                        $term = get_term_by( 'name', $term_value, 'property_features' );
                    }
                    if ( $term && ! is_wp_error( $term ) ) {
                        $feature_term_ids[] = intval( $term->term_id );
                    }
                }
            }

            // Build a single clause with operator=AND — post must have all features.
            if ( ! empty( $feature_term_ids ) ) {
                $tax_query[] = array(
                    'taxonomy' => 'property_features',
                    'field'    => 'term_id',
                    'terms'    => $feature_term_ids,
                    'operator' => 'AND',
                );
            }
        }

        // Step 4: location param — searches the same value across city, area, and
        // county_state with an OR relation (nested group inside the outer AND).
        // Used by type2 search forms that have a single "location" input field.
        if ( ! empty( $params['location'] ) ) {
            $location_value = trim( (string) $params['location'] );
            $location_taxonomies = array(
                'property_city',
                'property_area',
                'property_county_state',
            );

            $location_group = array( 'relation' => 'OR' );

            foreach ( $location_taxonomies as $loc_tax ) {
                // Resolve term: numeric ID, then slug, then name fallback.
                $term_id = 0;
                if ( is_numeric( $location_value ) && intval( $location_value ) > 0 ) {
                    $term_id = intval( $location_value );
                } else {
                    $term = get_term_by( 'slug', $location_value, $loc_tax );
                    if ( ! $term || is_wp_error( $term ) ) {
                        $term = get_term_by( 'name', $location_value, $loc_tax );
                    }
                    if ( $term && ! is_wp_error( $term ) ) {
                        $term_id = intval( $term->term_id );
                    }
                }

                if ( $term_id > 0 ) {
                    $location_group[] = array(
                        'taxonomy' => $loc_tax,
                        'field'    => 'term_id',
                        'terms'    => array( $term_id ),
                    );
                }
            }

            // Only add the group if at least one taxonomy matched.
            if ( count( $location_group ) > 1 ) {
                $tax_query[] = $location_group;
            }
        }

        // Step 5: merge raw tax_query clauses passed in $params['tax_query'].
        // Used when callers forward pre-built WP_Query args (e.g. from a prior
        // 2026 query result) that already contain tax_query clauses.
        if ( ! empty( $params['tax_query'] ) && is_array( $params['tax_query'] ) ) {
            foreach ( $params['tax_query'] as $key => $clause ) {
                // Skip the 'relation' key — we set our own below.
                if ( 'relation' === $key ) {
                    continue;
                }
                if ( is_array( $clause ) && ! empty( $clause ) ) {
                    $tax_query[] = $clause;
                }
            }
        }

        // Step 6: set relation between multiple taxonomy clauses when more than one exists.
        if ( count( $tax_query ) > 1 ) {
            $relation            = isset( $params['tax_relation'] ) ? strtoupper( $params['tax_relation'] ) : 'AND';
            $tax_query['relation'] = ( 'OR' === $relation ) ? 'OR' : 'AND';
        }

        // Allow child themes to add/modify taxonomy clauses (e.g. custom taxonomies).
        $tax_query = apply_filters( 'wpestate_query_2026_tax_query', $tax_query, $params );

        return $tax_query;
    }
}

if ( ! function_exists( 'wpestate_query_2026_property_build_meta_query' ) ) {
    /**
     * Builds meta_query clauses for standard property meta fields.
     *
     * Supports price, bedrooms, bathrooms, rooms, size, lot_size, country,
     * and agent. Returns an array of clause arrays (no relation key added here
     * — the orchestrator merges all meta clause arrays before setting relation).
     *
     * @param array $params Canonical params array.
     * @return array        Meta query clause arrays.
     */
    function wpestate_query_2026_property_build_meta_query( $params ) {
        $meta_query = array();

        // Step 1: helper to add a numeric BETWEEN / >= / <= clause.
        $add_range = function ( $key, $min_val, $max_val ) use ( &$meta_query ) {
            if ( ! $min_val && ! $max_val ) {
                return;
            }
            if ( $min_val && $max_val ) {
                $meta_query[] = array(
                    'key'     => $key,
                    'value'   => array( $min_val, $max_val ),
                    'type'    => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            } elseif ( $min_val ) {
                $meta_query[] = array(
                    'key'     => $key,
                    'value'   => $min_val,
                    'type'    => 'NUMERIC',
                    'compare' => '>=',
                );
            } else {
                $meta_query[] = array(
                    'key'     => $key,
                    'value'   => $max_val,
                    'type'    => 'NUMERIC',
                    'compare' => '<=',
                );
            }
        };

        // Step 2: price range.
        $add_range(
            'property_price',
            floatval( isset( $params['price_min'] ) ? $params['price_min'] : 0 ),
            floatval( isset( $params['price_max'] ) ? $params['price_max'] : 0 )
        );

        // Step 3: bedroom range.
        $add_range(
            'property_bedrooms',
            floatval( isset( $params['beds_min'] ) ? $params['beds_min'] : 0 ),
            floatval( isset( $params['beds_max'] ) ? $params['beds_max'] : 0 )
        );

        // Step 4: bathroom range.
        $add_range(
            'property_bathrooms',
            floatval( isset( $params['baths_min'] ) ? $params['baths_min'] : 0 ),
            floatval( isset( $params['baths_max'] ) ? $params['baths_max'] : 0 )
        );

        // Step 5: room range.
        $add_range(
            'property_rooms',
            floatval( isset( $params['rooms_min'] ) ? $params['rooms_min'] : 0 ),
            floatval( isset( $params['rooms_max'] ) ? $params['rooms_max'] : 0 )
        );

        // Step 6: property size range.
        $add_range(
            'property_size',
            floatval( isset( $params['size_min'] ) ? $params['size_min'] : 0 ),
            floatval( isset( $params['size_max'] ) ? $params['size_max'] : 0 )
        );

        // Step 7: lot size range.
        $add_range(
            'property_lot_size',
            floatval( isset( $params['lot_size_min'] ) ? $params['lot_size_min'] : 0 ),
            floatval( isset( $params['lot_size_max'] ) ? $params['lot_size_max'] : 0 )
        );

        // Step 8: country LIKE filter.
        if ( ! empty( $params['country'] ) ) {
            $meta_query[] = array(
                'key'     => 'property_country',
                'value'   => sanitize_text_field( $params['country'] ),
                'compare' => 'LIKE',
            );
        }

        // Step 9: agent exact match.
        $agent = intval( isset( $params['agent'] ) ? $params['agent'] : 0 );
        if ( $agent > 0 ) {
            $meta_query[] = array(
                'key'     => 'property_agent',
                'value'   => $agent,
                'type'    => 'NUMERIC',
                'compare' => '=',
            );
        }

        // Step 10: featured_only mode adds a clause requiring prop_featured = 1.
        if ( ! empty( $params['featured_only'] ) ) {
            $meta_query[] = array(
                'key'     => 'prop_featured',
                'value'   => '1',
                'compare' => '=',
                'type'    => 'NUMERIC',
            );
        }

        // Step 11: merge raw meta_query clauses passed directly in $params['meta'].
        if ( ! empty( $params['meta'] ) && is_array( $params['meta'] ) ) {
            foreach ( $params['meta'] as $clause ) {
                if ( is_array( $clause ) && ! empty( $clause ) ) {
                    $meta_query[] = $clause;
                }
            }
        }

        // Step 12: merge raw meta_query clauses passed in $params['meta_query'].
        if ( ! empty( $params['meta_query'] ) && is_array( $params['meta_query'] ) ) {
            foreach ( $params['meta_query'] as $clause ) {
                if ( is_array( $clause ) && ! empty( $clause ) && ! isset( $clause['relation'] ) ) {
                    $meta_query[] = $clause;
                }
            }
        }

        // Step 13: remove clauses where value is blank or 'all' (no-op placeholders from search forms).
        $meta_query = array_filter( $meta_query, function ( $clause ) {
            if ( ! isset( $clause['value'] ) ) {
                return false;
            }
            $val = $clause['value'];
            // Skip array values (e.g. BETWEEN ranges) — those are always intentional.
            if ( is_array( $val ) ) {
                return true;
            }
            // Remove empty string or 'all' placeholder values.
            if ( '' === (string) $val || 'all' === strtolower( trim( (string) $val ) ) ) {
                return false;
            }
            return true;
        } );

        return array_values( $meta_query );
    }
}

if ( ! function_exists( 'wpestate_query_2026_property_build_custom_fields_meta_query' ) ) {
    /**
     * Builds meta_query clauses from dynamic custom fields.
     *
     * Reads the theme option 'wp_estate_custom_fields' to know which custom
     * field slugs are valid. Values to filter by come from $params['custom_fields']
     * (map of slug => value) and compare modes from $params['custom_fields_compare']
     * (map of slug => compare string: 'equal', 'greater', 'smaller', 'like',
     * 'date bigger', 'date smaller'). Unknown field slugs are silently ignored.
     *
     * @param array $params Canonical params array.
     * @return array        Meta query clause arrays for matching custom fields.
     */
    function wpestate_query_2026_property_build_custom_fields_meta_query( $params ) {
        $meta_query = array();

        // Step 1: bail if no custom field filter values were supplied.
        if ( empty( $params['custom_fields'] ) || ! is_array( $params['custom_fields'] ) ) {
            return $meta_query;
        }

        // Step 2: load configured custom field definitions from theme option.
        $custom_fields_option = wpresidence_get_option( 'wp_estate_custom_fields', '' );
        if ( empty( $custom_fields_option ) ) {
            return $meta_query;
        }

        // Step 3: build a set of allowed slugs from the theme option.
        $allowed_slugs = array();
        foreach ( (array) $custom_fields_option as $field_data ) {
            $raw_slug = '';
            if ( is_array( $field_data ) && isset( $field_data['slug'] ) ) {
                $raw_slug = $field_data['slug'];
            } elseif ( is_array( $field_data ) && isset( $field_data['field'] ) ) {
                $raw_slug = $field_data['field'];
            } elseif ( is_array( $field_data ) && isset( $field_data[0] ) ) {
                $raw_slug = $field_data[0];
            } elseif ( is_string( $field_data ) ) {
                $raw_slug = $field_data;
            }
            $slug = wpestate_normalize_custom_field_slug( (string) $raw_slug );
            if ( '' !== $slug ) {
                $allowed_slugs[ $slug ] = true;
            }
        }

        // Step 4: iterate each requested custom field and build a meta clause.
        $compare_map = isset( $params['custom_fields_compare'] ) && is_array( $params['custom_fields_compare'] )
            ? $params['custom_fields_compare']
            : array();

        foreach ( $params['custom_fields'] as $slug => $value ) {
            $slug = wpestate_normalize_custom_field_slug( (string) $slug );

            // Step 4a: skip slugs not configured in the theme.
            if ( ! isset( $allowed_slugs[ $slug ] ) ) {
                continue;
            }

            // Step 4b: skip empty / default-label values.
            if ( '' === (string) $value || 'all' === strtolower( (string) $value ) ) {
                continue;
            }

            // Step 4c: determine compare mode and build clause.
            $compare_mode = isset( $compare_map[ $slug ] ) ? strtolower( $compare_map[ $slug ] ) : 'equal';
            $clause       = array( 'key' => $slug );

            switch ( $compare_mode ) {
                case 'greater':
                    $clause['compare'] = '>=';
                    $clause['type']    = 'NUMERIC';
                    $clause['value']   = floatval( $value );
                    break;
                case 'smaller':
                    $clause['compare'] = '<=';
                    $clause['type']    = 'NUMERIC';
                    $clause['value']   = floatval( $value );
                    break;
                case 'like':
                    $clause['compare'] = 'LIKE';
                    $clause['type']    = 'CHAR';
                    $clause['value']   = sanitize_text_field( (string) $value );
                    break;
                case 'date bigger':
                    $clause['compare'] = '>=';
                    $clause['type']    = 'DATE';
                    $clause['value']   = sanitize_text_field( str_replace( ' ', '-', (string) $value ) );
                    break;
                case 'date smaller':
                    $clause['compare'] = '<=';
                    $clause['type']    = 'DATE';
                    $clause['value']   = sanitize_text_field( str_replace( ' ', '-', (string) $value ) );
                    break;
                case 'equal':
                default:
                    $clause['compare'] = '=';
                    $clause['type']    = 'NUMERIC';
                    $clause['value']   = floatval( $value );
                    break;
            }

            $meta_query[] = $clause;
        }

        return $meta_query;
    }
}
