<?php

add_action( 'wp_ajax_nopriv_wpestate_classic_ondemand_directory_query', 'wpestate_classic_ondemand_directory_query' );
add_action( 'wp_ajax_wpestate_classic_ondemand_directory_query', 'wpestate_classic_ondemand_directory_query' );


add_action( 'wp_ajax_nopriv_wpestate_classic_ondemand_directory', 'wpestate_classic_ondemand_directory' );
add_action( 'wp_ajax_wpestate_classic_ondemand_directory', 'wpestate_classic_ondemand_directory' );

if ( ! function_exists( 'wpestate_directory_get_request_post_id' ) ) :

    /**
     * Extracts the directory page ID from AJAX requests.
     *
     * Both `postid` and `postID` are used throughout the theme, so this helper
     * centralises the fallback logic and guarantees we always return a valid
     * integer.
     *
     * @return int Directory post ID or 0 when missing.
     */
    function wpestate_directory_get_request_post_id() {

        // Step 1: Prefer the lowercase `postid` parameter when present.
        if ( isset( $_POST['postid'] ) ) {
            return intval( $_POST['postid'] );
        }

        // Step 2: Accept the camelCase variant used by some older scripts.
        if ( isset( $_POST['postID'] ) ) {
            return intval( $_POST['postID'] );
        }

        // Step 3: Default to 0 to avoid undefined index notices downstream.
        return 0;
    }

endif; // end wpestate_directory_get_request_post_id

if ( ! function_exists( 'wpestate_directory_normalize_range' ) ) :

    /**
     * Compares a requested numeric range with its defaults and clears matches.
     *
     * Directory sliders often submit their full range even when the visitor
     * hasn't narrowed the search. This helper keeps the query lean by
     * returning zeroed values whenever both ends are still equal to their
     * configured defaults.
     *
     * @param float $value_min    Requested minimum value.
     * @param float $value_max    Requested maximum value.
     * @param float $default_min  Slider default minimum value.
     * @param float $default_max  Slider default maximum value.
     * @param float $epsilon      Tolerance for float comparisons.
     * @return array              Two-item array with normalised min and max.
     */
    function wpestate_directory_normalize_range( $value_min, $value_max, $default_min, $default_max, $epsilon = 0.0001 ) {

        // Step 1: Cast everything to floats so comparisons behave predictably.
        $value_min   = floatval( $value_min );
        $value_max   = floatval( $value_max );
        $default_min = floatval( $default_min );
        $default_max = floatval( $default_max );

        // Step 2: Only run the matching logic when at least one bound is set.
        if ( $value_min || $value_max ) {
            $min_matches_default = ( $value_min <= 0 && $default_min <= 0 );
            if ( ! $min_matches_default && $default_min > 0 ) {
                $min_matches_default = ( abs( $value_min - $default_min ) <= $epsilon );
            }

            $max_matches_default = ( $value_max <= 0 && $default_max <= 0 );
            if ( ! $max_matches_default && $default_max > 0 ) {
                $max_matches_default = ( $value_max >= ( $default_max - $epsilon ) );
            }

            // Step 3: Clear both values whenever the request still spans
            // the entire slider range.
            if ( $min_matches_default && $max_matches_default ) {
                $value_min = 0;
                $value_max = 0;
            }
        }

        return array( $value_min, $value_max );
    }

endif; // end wpestate_directory_normalize_range

if ( ! function_exists( 'wpestate_directory_normalize_price_range' ) ) :

    /**
     * Normalises the requested price range by removing currency effects and
     * default slider bounds.
     *
     * @param float $price_min Requested minimum price.
     * @param float $price_max Requested maximum price.
     * @param float $epsilon   Tolerance for float comparisons.
     * @return array           Two-item array with sanitised min and max.
     */
    function wpestate_directory_normalize_price_range( $price_min, $price_max, $epsilon = 0.0001 ) {

        // Step 1: Cast request values to floats for consistent math.
        $price_min = floatval( $price_min );
        $price_max = floatval( $price_max );

        // Step 2: Skip early when no price filters were provided.
        if ( ! $price_min && ! $price_max ) {
            return array( 0, 0 );
        }

        // Step 3: Convert from the user-selected currency back to the base
        // currency so comparisons against stored meta values are accurate.
        $custom_fields = wpresidence_get_option( 'wp_estate_multi_curr', '' );
        if ( ! empty( $custom_fields ) && isset( $_COOKIE['my_custom_curr_pos'] ) ) {
            $index = intval( $_COOKIE['my_custom_curr_pos'] );
            if ( $index >= 0 && isset( $custom_fields[ $index ][2] ) ) {
                $exchange_rate = floatval( $custom_fields[ $index ][2] );
                if ( $exchange_rate > 0 ) {
                    if ( $price_min ) {
                        $price_min = $price_min / $exchange_rate;
                    }
                    if ( $price_max ) {
                        $price_max = $price_max / $exchange_rate;
                    }
                }
            }
        }

        // Step 4: Compare the converted values against the global slider
        // defaults and clear them when the request still spans the full range.
        $default_price_min = floatval( wpresidence_get_option( 'wp_estate_show_slider_min_price', '' ) );
        $default_price_max = floatval( wpresidence_get_option( 'wp_estate_show_slider_max_price', '' ) );

        return wpestate_directory_normalize_range( $price_min, $price_max, $default_price_min, $default_price_max, $epsilon );
    }

endif; // end wpestate_directory_normalize_price_range

if ( ! function_exists( 'wpestate_classic_ondemand_directory_query' ) ) :

    function wpestate_classic_ondemand_directory_query() {

        // Step 1: Security check
        $nonce = isset( $_POST['security'] ) ? $_POST['security'] : '';
        if ( ! wp_verify_nonce( $nonce, 'wpestate_ajax_filtering' ) ) {
            wp_send_json_error( array( 'message' => 'Security check failed' ) );
            return;
        }



        
        // Step 2: temporarily disable cache additions and collect page options.
        wp_suspend_cache_addition( false );

        $allowed_html      = array();
        $directory_post_id = wpestate_directory_get_request_post_id();
        $wpestate_options  = wpestate_page_details( $directory_post_id );

        // Step 3: seed the parameter array with pagination defaults.
        $params = array(
            'paged'          => isset( $_POST['pagination'] ) ? intval( $_POST['pagination'] ) : 1,
            'posts_per_page' => intval( wpresidence_get_option( 'wp_estate_prop_no', '' ) ),
            'no_found_rows'  => false,
            'fields'         => '',
        );

        // Step 4: include ordering code when provided.
        if ( isset( $_POST['order'] ) ) {
            $params['order'] = intval( $_POST['order'] );
        }

        // Step 5: map basic location and taxonomy filters from the request.
        $map = array(
            'category_values' => 'category',
            'action_values'   => 'action',
            'city'            => 'city',
            'area'            => 'area',
            'county'          => 'county_state',
        );
        foreach ( $map as $request_key => $param_key ) {
            if ( isset( $_REQUEST[ $request_key ] ) && $_REQUEST[ $request_key ] !== '' && $_REQUEST[ $request_key ] !== 'all' ) {
                $params[ $param_key ] = sanitize_title( wp_kses( $_REQUEST[ $request_key ], $allowed_html ) );
            }
        }

        // Step 6: add property status when provided.
        if ( isset( $_POST['status'] ) && $_POST['status'] !== '' ) {
            $status           = html_entity_decode( esc_html( $_POST['status'] ), ENT_QUOTES );
            $params['status'] = sanitize_title( $status );
        }

        // Step 7: capture keyword searches.
        if ( isset( $_POST['keyword'] ) ) {
            $params['keyword'] = sanitize_text_field( $_POST['keyword'] );
        }

        // Step 8: parse selected feature checkboxes.
        if ( isset( $_POST['all_checkers'] ) && $_POST['all_checkers'] !== '' ) {
            $features = array_filter( array_map( 'sanitize_title', explode( ',', wp_kses( $_POST['all_checkers'], $allowed_html ) ) ) );
            if ( ! empty( $features ) ) {
                $params['features'] = $features;
            }
        }

        // Step 9: normalise the price range and drop default values.
        $epsilon = 0.0001;
        list( $price_min, $price_max ) = wpestate_directory_normalize_price_range(
            isset( $_POST['price_low'] ) ? $_POST['price_low'] : 0,
            isset( $_POST['price_max'] ) ? $_POST['price_max'] : 0,
            $epsilon
        );
        if ( $price_min > 0 ) {
            $params['price_min'] = $price_min;
        }
        if ( $price_max > 0 ) {
            $params['price_max'] = $price_max;
        }

        // Step 10: normalise size and lot size sliders to avoid redundant joins.
        $default_size_min = wpestate_convert_measure( floatval( get_post_meta( $directory_post_id, 'dir_min_size', true ) ), 1 );
        $default_size_max = wpestate_convert_measure( floatval( get_post_meta( $directory_post_id, 'dir_max_size', true ) ), 1 );
        $size_min_request = isset( $_POST['min_size'] ) ? wpestate_convert_measure( floatval( $_POST['min_size'] ), 1 ) : 0;
        $size_max_request = isset( $_POST['max_size'] ) ? wpestate_convert_measure( floatval( $_POST['max_size'] ), 1 ) : 0;
        list( $size_min, $size_max ) = wpestate_directory_normalize_range( $size_min_request, $size_max_request, $default_size_min, $default_size_max, $epsilon );
        if ( $size_min > 0 ) {
            $params['size_min'] = $size_min;
        }
        if ( $size_max > 0 ) {
            $params['size_max'] = $size_max;
        }

        $default_lot_min = wpestate_convert_measure( floatval( get_post_meta( $directory_post_id, 'dir_min_lot_size', true ) ), 1 );
        $default_lot_max = wpestate_convert_measure( floatval( get_post_meta( $directory_post_id, 'dir_max_lot_size', true ) ), 1 );
        $lot_min_request = isset( $_POST['min_lot_size'] ) ? wpestate_convert_measure( floatval( $_POST['min_lot_size'] ), 1 ) : 0;
        $lot_max_request = isset( $_POST['max_lot_size'] ) ? wpestate_convert_measure( floatval( $_POST['max_lot_size'] ), 1 ) : 0;
        list( $lot_size_min, $lot_size_max ) = wpestate_directory_normalize_range( $lot_min_request, $lot_max_request, $default_lot_min, $default_lot_max, $epsilon );
        if ( $lot_size_min > 0 ) {
            $params['lot_size_min'] = $lot_size_min;
        }
        if ( $lot_size_max > 0 ) {
            $params['lot_size_max'] = $lot_size_max;
        }

        // Step 11: normalise room, bedroom, and bathroom sliders.
        $default_rooms_min = floatval( get_post_meta( $directory_post_id, 'dir_rooms_min', true ) );
        $default_rooms_max = floatval( get_post_meta( $directory_post_id, 'dir_rooms_max', true ) );
        $rooms_min_request = isset( $_POST['min_rooms'] ) ? floatval( $_POST['min_rooms'] ) : 0;
        $rooms_max_request = isset( $_POST['max_rooms'] ) ? floatval( $_POST['max_rooms'] ) : 0;
        list( $rooms_min, $rooms_max ) = wpestate_directory_normalize_range( $rooms_min_request, $rooms_max_request, $default_rooms_min, $default_rooms_max, $epsilon );
        if ( $rooms_min > 0 ) {
            $params['rooms_min'] = $rooms_min;
        }
        if ( $rooms_max > 0 ) {
            $params['rooms_max'] = $rooms_max;
        }

        $default_beds_min = floatval( get_post_meta( $directory_post_id, 'dir_bedrooms_min', true ) );
        $default_beds_max = floatval( get_post_meta( $directory_post_id, 'dir_bedrooms_max', true ) );
        $beds_min_request  = isset( $_POST['min_bedrooms'] ) ? floatval( $_POST['min_bedrooms'] ) : 0;
        $beds_max_request  = isset( $_POST['max_bedrooms'] ) ? floatval( $_POST['max_bedrooms'] ) : 0;
        list( $beds_min, $beds_max ) = wpestate_directory_normalize_range( $beds_min_request, $beds_max_request, $default_beds_min, $default_beds_max, $epsilon );
        if ( $beds_min > 0 ) {
            $params['beds_min'] = $beds_min;
        }
        if ( $beds_max > 0 ) {
            $params['beds_max'] = $beds_max;
        }

        $default_baths_min = floatval( get_post_meta( $directory_post_id, 'dir_bathrooms_min', true ) );
        $default_baths_max = floatval( get_post_meta( $directory_post_id, 'dir_bathrooms_max', true ) );
        $baths_min_request = isset( $_POST['min_bathrooms'] ) ? floatval( $_POST['min_bathrooms'] ) : 0;
        $baths_max_request = isset( $_POST['max_bathrooms'] ) ? floatval( $_POST['max_bathrooms'] ) : 0;
        list( $baths_min, $baths_max ) = wpestate_directory_normalize_range( $baths_min_request, $baths_max_request, $default_baths_min, $default_baths_max, $epsilon );
        if ( $baths_min > 0 ) {
            $params['baths_min'] = $baths_min;
        }
        if ( $baths_max > 0 ) {
            $params['baths_max'] = $baths_max;
        }

        // Step 12: optionally apply geolocation radius filters.
        if ( isset( $_POST['geo_lat'], $_POST['geo_long'] ) && 'yes' === wpresidence_get_option( 'wp_estate_use_geo_location', '' ) && $_POST['geo_lat'] !== '' && $_POST['geo_long'] !== '' ) {
            $params['geo_lat']  = floatval( $_POST['geo_lat'] );
            $params['geo_long'] = floatval( $_POST['geo_long'] );
            $params['geo_rad']  = isset( $_POST['geo_rad'] ) ? floatval( $_POST['geo_rad'] ) : 0;
        }

        // Step 13: build final WP_Query arguments from collected parameters.
        $args = wpestate_build_property_query( $params );



        // Step 14: run the query with optional custom ordering when order=0.
        $prop_selection = wpestate_run_property_query( $params );



        // Step 15: render property cards and respond with JSON payload.
        ob_start();
        wpresidence_display_property_list_as_html( $prop_selection, $wpestate_options, 'shortcode_list' );
        $cards = ob_get_clean();

        echo wp_json_encode(
            array(
                'args'       => $args,
                'cards'      => $cards,
                'no_results' => $prop_selection->found_posts,
            )
        );

        // Step 16: restore cache behaviour and end request.
        wp_suspend_cache_addition( false );
        wp_die();
    }

endif; // end ajax_filter_listings

if ( ! function_exists( 'wpestate_classic_ondemand_directory' ) ) :

    /**
     * AJAX handler for the classic directory filtering UI.
     *
     * Called by wpresidence_universal_query_filtering_action() in
     * property_directory_control.js. JS sends full taxonomy names as term IDs
     * resolved client-side, so no slug-to-term lookup is needed here.
     *
     * Key mapping from JS payload to 2026 param names:
     *   property_action_category → property_action_category  (direct)
     *   property_category        → property_category         (direct)
     *   property_city            → property_city             (direct)
     *   property_area            → property_area             (direct)
     *   property_county_state    → property_county_state     (direct)
     *   status                   → property_status
     *   property_features        → property_features  (comma-separated → array)
     *   price_low                → price_min
     *   price_max                → price_max          (same)
     *   min_size / max_size      → size_min / size_max
     *   min_lot_size / max_lot_size → lot_size_min / lot_size_max
     *   min_rooms / max_rooms    → rooms_min / rooms_max
     *   min_bedrooms / max_bedrooms → beds_min / beds_max
     *   min_bathrooms / max_bathrooms → baths_min / baths_max
     *   keyword                  → keyword            (direct)
     *   order                    → order              (direct)
     *   pagination               → paged
     *   postID                   → directory page context (not a query param)
     */
    function wpestate_classic_ondemand_directory() {

        // Step 1: Security — verify nonce sent as 'security' by the JS.
        $nonce = isset( $_POST['security'] ) ? $_POST['security'] : '';
        if ( ! wp_verify_nonce( $nonce, 'wpestate_ajax_filtering' ) ) {
            wp_send_json_error( array( 'message' => 'Security check failed' ) );
            return;
        }

        // Step 2: Temporarily suspend cache additions and load directory page options.
        wp_suspend_cache_addition( false );

        $directory_post_id = wpestate_directory_get_request_post_id();
        $wpestate_options  = wpestate_page_details( $directory_post_id );

        // Step 3: Seed params with pagination and posts-per-page defaults.
        $params = array(
            'paged'          => isset( $_POST['pagination'] ) ? intval( $_POST['pagination'] ) : 1,
            'posts_per_page' => intval( wpresidence_get_option( 'wp_estate_prop_no', '' ) ),
            'no_found_rows'  => false,
            'fields'         => '',
        );

        // Step 4: Include ordering code when provided.
        if ( isset( $_POST['order'] ) ) {
            $params['order'] = intval( $_POST['order'] );
        }

        // Step 5: Taxonomy term IDs — JS sends full taxonomy names with term IDs
        // already resolved client-side via resolveDropdownTermId().
        $taxonomy_keys = array(
            'property_action_category',
            'property_category',
            'property_city',
            'property_area',
            'property_county_state',
        );
        foreach ( $taxonomy_keys as $key ) {
            $val = isset( $_POST[ $key ] ) ? sanitize_text_field( $_POST[ $key ] ) : '';
            if ( '' !== $val && 'all' !== strtolower( $val ) ) {
                $params[ $key ] = $val;
            }
        }

        // Step 6: Property status — JS key is 'status', 2026 param is 'property_status'.
        if ( isset( $_POST['status'] ) && '' !== $_POST['status'] ) {
            $params['property_status'] = sanitize_text_field( $_POST['status'] );
        }

        // Step 7: Features — JS sends a comma-separated string; 2026 layer expects an array.
        if ( isset( $_POST['property_features'] ) && '' !== $_POST['property_features'] ) {
            $features = array_filter(
                array_map( 'sanitize_text_field', explode( ',', $_POST['property_features'] ) )
            );
            if ( ! empty( $features ) ) {
                $params['property_features'] = $features;
            }
        }

        // Step 8: Keyword search.
        if ( isset( $_POST['keyword'] ) && '' !== $_POST['keyword'] ) {
            $params['keyword'] = sanitize_text_field( $_POST['keyword'] );
        }

        // Step 9: Price range — apply currency conversion and suppress slider defaults.
        $epsilon = 0.0001;
        list( $price_min, $price_max ) = wpestate_directory_normalize_price_range(
            isset( $_POST['price_low'] ) ? $_POST['price_low'] : 0,
            isset( $_POST['price_max'] ) ? $_POST['price_max'] : 0,
            $epsilon
        );
        if ( $price_min > 0 ) {
            $params['price_min'] = $price_min;
        }
        if ( $price_max > 0 ) {
            $params['price_max'] = $price_max;
        }

        // Step 10: Property size — apply measure conversion and suppress slider defaults.
        $default_size_min = wpestate_convert_measure( floatval( get_post_meta( $directory_post_id, 'dir_min_size', true ) ), 1 );
        $default_size_max = wpestate_convert_measure( floatval( get_post_meta( $directory_post_id, 'dir_max_size', true ) ), 1 );
        $size_min_req     = isset( $_POST['min_size'] ) ? wpestate_convert_measure( floatval( $_POST['min_size'] ), 1 ) : 0;
        $size_max_req     = isset( $_POST['max_size'] ) ? wpestate_convert_measure( floatval( $_POST['max_size'] ), 1 ) : 0;
        list( $size_min, $size_max ) = wpestate_directory_normalize_range( $size_min_req, $size_max_req, $default_size_min, $default_size_max, $epsilon );
        if ( $size_min > 0 ) {
            $params['size_min'] = $size_min;
        }
        if ( $size_max > 0 ) {
            $params['size_max'] = $size_max;
        }

        // Step 11: Lot size — apply measure conversion and suppress slider defaults.
        $default_lot_min = wpestate_convert_measure( floatval( get_post_meta( $directory_post_id, 'dir_min_lot_size', true ) ), 1 );
        $default_lot_max = wpestate_convert_measure( floatval( get_post_meta( $directory_post_id, 'dir_max_lot_size', true ) ), 1 );
        $lot_min_req     = isset( $_POST['min_lot_size'] ) ? wpestate_convert_measure( floatval( $_POST['min_lot_size'] ), 1 ) : 0;
        $lot_max_req     = isset( $_POST['max_lot_size'] ) ? wpestate_convert_measure( floatval( $_POST['max_lot_size'] ), 1 ) : 0;
        list( $lot_size_min, $lot_size_max ) = wpestate_directory_normalize_range( $lot_min_req, $lot_max_req, $default_lot_min, $default_lot_max, $epsilon );
        if ( $lot_size_min > 0 ) {
            $params['lot_size_min'] = $lot_size_min;
        }
        if ( $lot_size_max > 0 ) {
            $params['lot_size_max'] = $lot_size_max;
        }

        // Step 12: Rooms — suppress slider defaults.
        $default_rooms_min = floatval( get_post_meta( $directory_post_id, 'dir_rooms_min', true ) );
        $default_rooms_max = floatval( get_post_meta( $directory_post_id, 'dir_rooms_max', true ) );
        $rooms_min_req     = isset( $_POST['min_rooms'] ) ? floatval( $_POST['min_rooms'] ) : 0;
        $rooms_max_req     = isset( $_POST['max_rooms'] ) ? floatval( $_POST['max_rooms'] ) : 0;
        list( $rooms_min, $rooms_max ) = wpestate_directory_normalize_range( $rooms_min_req, $rooms_max_req, $default_rooms_min, $default_rooms_max, $epsilon );
        if ( $rooms_min > 0 ) {
            $params['rooms_min'] = $rooms_min;
        }
        if ( $rooms_max > 0 ) {
            $params['rooms_max'] = $rooms_max;
        }

        // Step 13: Bedrooms — suppress slider defaults.
        $default_beds_min = floatval( get_post_meta( $directory_post_id, 'dir_bedrooms_min', true ) );
        $default_beds_max = floatval( get_post_meta( $directory_post_id, 'dir_bedrooms_max', true ) );
        $beds_min_req     = isset( $_POST['min_bedrooms'] ) ? floatval( $_POST['min_bedrooms'] ) : 0;
        $beds_max_req     = isset( $_POST['max_bedrooms'] ) ? floatval( $_POST['max_bedrooms'] ) : 0;
        list( $beds_min, $beds_max ) = wpestate_directory_normalize_range( $beds_min_req, $beds_max_req, $default_beds_min, $default_beds_max, $epsilon );
        if ( $beds_min > 0 ) {
            $params['beds_min'] = $beds_min;
        }
        if ( $beds_max > 0 ) {
            $params['beds_max'] = $beds_max;
        }

        // Step 14: Bathrooms — suppress slider defaults.
        $default_baths_min = floatval( get_post_meta( $directory_post_id, 'dir_bathrooms_min', true ) );
        $default_baths_max = floatval( get_post_meta( $directory_post_id, 'dir_bathrooms_max', true ) );
        $baths_min_req     = isset( $_POST['min_bathrooms'] ) ? floatval( $_POST['min_bathrooms'] ) : 0;
        $baths_max_req     = isset( $_POST['max_bathrooms'] ) ? floatval( $_POST['max_bathrooms'] ) : 0;
        list( $baths_min, $baths_max ) = wpestate_directory_normalize_range( $baths_min_req, $baths_max_req, $default_baths_min, $default_baths_max, $epsilon );
        if ( $baths_min > 0 ) {
            $params['baths_min'] = $baths_min;
        }
        if ( $baths_max > 0 ) {
            $params['baths_max'] = $baths_max;
        }

        // Step 15: Execute query via the 2026 layer — single entry point.
        $result         = wpestate_query_2026_property( 'directory', $params );
        $prop_selection = $result['query'];

        // Step 16: Render property cards as HTML.
        ob_start();
        wpresidence_display_property_list_as_html( $prop_selection, $wpestate_options, 'shortcode_list' );
        $cards = ob_get_clean();

        // Step 17: Return JSON response to property_directory_control.js.
        echo wp_json_encode(
            array(
                'cards'      => $cards,
                'no_results' => $prop_selection->found_posts,
            )
        );

        wp_suspend_cache_addition( false );
        wp_die();
    }

endif; // end wpestate_classic_ondemand_directory


