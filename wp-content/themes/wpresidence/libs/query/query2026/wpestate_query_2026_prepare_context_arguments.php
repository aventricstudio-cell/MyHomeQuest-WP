<?php
/**
 * Context arguments builder for the 2026 estate_property query layer.
 *
 * Convenience wrapper used by page templates. Merges page meta defaults,
 * $_REQUEST filters, and explicit param overrides into a single flat
 * $params array ready for wpestate_query_2026_property().
 *
 * File: libs/query/query2026/wpestate_query_2026_prepare_context_arguments.php
 *
 * @package WPEstate
 */

if ( ! function_exists( 'wpestate_query_2026_prepare_context_arguments' ) ) {
    /**
     * Builds canonical params for a page template by merging three sources.
     *
     * Priority order (highest wins):
     *   1. Explicit params passed via $input['params']  (caller overrides)
     *   2. $_REQUEST values matching known filter keys   (user form submission)
     *   3. Page meta read from $input['page_id']        (admin default settings)
     *   4. Context defaults from wpestate_query_2026_property_context_defaults()
     *
     * @param string $context  directory|map|dashboard|featured
     * @param array  $input {
     *     @type int   $page_id Optional WordPress page ID for reading page meta.
     *     @type array $request Optional superglobal snapshot; defaults to $_REQUEST.
     *     @type array $params  Optional explicit overrides (highest priority).
     * }
     * @return array           Flat canonical params array.
     */
    function wpestate_query_2026_prepare_context_arguments( $context, $input = array() ) {
        // Step 1: start from context defaults as the lowest-priority base.
        $params = wpestate_query_2026_property_context_defaults( $context );

        // Step 2: read page meta when a page_id is provided.
        if ( ! empty( $input['page_id'] ) ) {
            $page_meta = wpestate_query_2026_prepare_arguments( intval( $input['page_id'] ) );
            $params    = array_merge( $params, $page_meta );
        }

        // Step 3: merge $_REQUEST (or provided request snapshot) over page meta.
        $request = isset( $input['request'] ) && is_array( $input['request'] )
            ? $input['request']
            : array();

        if ( ! empty( $request ) ) {
            $request_params = wpestate_query_2026_extract_request_params( $request );
            foreach ( $request_params as $key => $value ) {
                // Only override when the request value is non-empty.
                if ( '' !== $value && array() !== $value && null !== $value ) {
                    $params[ $key ] = $value;
                }
            }
        }

        // Step 4: apply explicit caller overrides last (highest priority).
        if ( ! empty( $input['params'] ) && is_array( $input['params'] ) ) {
            foreach ( $input['params'] as $key => $value ) {
                $params[ $key ] = $value;
            }
        }

        return $params;
    }
}

if ( ! function_exists( 'wpestate_query_2026_extract_request_params' ) ) {
    /**
     * Extracts and sanitizes known filter keys from a raw request array.
     *
     * Reads the flat request array (typically $_REQUEST or $_GET) and maps
     * its values to the canonical params keys. Unknown keys are ignored.
     *
     * @param array $request Raw request data.
     * @return array         Sanitized params from the request.
     */
    function wpestate_query_2026_extract_request_params( $request ) {
        $params = array();

        // Step 1: numeric filter params.
        $int_keys = array(
            'order',
            'paged',
            'posts_per_page',
            'agent',
            'p',
        );
        foreach ( $int_keys as $key ) {
            if ( isset( $request[ $key ] ) && is_numeric( $request[ $key ] ) ) {
                $params[ $key ] = intval( $request[ $key ] );
            }
        }

        // Step 2: float filter params.
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
            if ( isset( $request[ $key ] ) && '' !== $request[ $key ] ) {
                $val = floatval( $request[ $key ] );
                if ( 0.0 !== $val ) {
                    $params[ $key ] = $val;
                }
            }
        }

        // Step 3: text filter params.
        $text_keys = array(
            'keyword',
            's',
            'country',
        );
        foreach ( $text_keys as $key ) {
            if ( isset( $request[ $key ] ) && '' !== trim( (string) $request[ $key ] ) ) {
                $params[ $key ] = sanitize_text_field( wp_unslash( $request[ $key ] ) );
            }
        }

        // Step 4: taxonomy params — may be string or array.
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
            if ( ! empty( $request[ $key ] ) ) {
                $val = $request[ $key ];
                if ( is_array( $val ) ) {
                    $params[ $key ] = array_map( 'sanitize_text_field', $val );
                } else {
                    $params[ $key ] = array( sanitize_text_field( wp_unslash( (string) $val ) ) );
                }
            }
        }

        return $params;
    }
}
