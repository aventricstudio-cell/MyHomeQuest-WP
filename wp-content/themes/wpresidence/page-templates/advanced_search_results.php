<?php
/** MILLDONE
 * Template Name: Advanced Search Results
 * src: page-templates/advanced_search_results.php
 * This template handles the display of advanced search results for properties
 * in the WpResidence theme. It processes search parameters, queries properties,
 * and displays results in various formats (list, grid, half map).
 *
 * @package WpResidence
 * @subpackage PropertySearch
 * @since 1.0.0
 */

// Check for WpResidence Core Plugin
if (!function_exists('wpestate_residence_functionality')) {
    esc_html_e('This page will not work without WpResidence Core Plugin, Please activate it from the plugins menu!', 'wpresidence');
    exit();
}

// Verify nonce for security
if (!isset($_GET['wpestate_regular_search_nonce']) || !wp_verify_nonce($_GET['wpestate_regular_search_nonce'], 'wpestate_regular_search')) {
    // Handle nonce verification failure
}

// Initialize global variables and flush cache
global $wpestate_keyword, $wpestate_included_ids;
wp_cache_flush();

get_header();

// Initialize variables
$current_user = wp_get_current_user();
$wpestate_options = get_query_var('wpestate_options');
$show_compare = 1;
$area_array = $city_array = $action_array = $categ_array = $id_array = $countystate_array = '';

// Get theme options and settings
$compare_submit = wpestate_get_template_link('page-templates/compare_listings.php');
$wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
$where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
$prop_no = intval(wpresidence_get_option('wp_estate_prop_no', ''));
$show_compare_link = 'yes';
$userID = $current_user->ID;
$user_option = 'favorites' . intval($userID);
$curent_fav = get_option($user_option);
$custom_advanced_search = wpresidence_get_option('wp_estate_custom_advanced_search', '');
$custom_advanced_search = 'yes';
$meta_query = array();

// ── Search field configuration ──
// Read the search type setting (used by sub-templates for UI layout).
$adv_search_type = wpresidence_get_option( 'wp_estate_adv_search_type', '' );

// Property unit display class (list vs grid).
$wpestate_prop_unit = esc_html( wpresidence_get_option( 'wp_estate_prop_unit', '' ) );
$prop_unit_class    = ( 'list' === $wpestate_prop_unit ) ? 'ajax12' : '';
$align_class        = ( 'list' === $wpestate_prop_unit ) ? 'the_list_view' : '';

// Get search field arrays (what/how/label) — needed for Elementor override + property ID extraction.
$adv_search_what  = wpresidence_get_option( 'wp_estate_adv_search_what', '' );
$adv_search_how   = wpresidence_get_option( 'wp_estate_adv_search_how', '' );
$adv_search_label = wpresidence_get_option( 'wp_estate_adv_search_label', '' );

// ── Elementor form overrides ──
if ( isset( $_GET['elementor_form_id'] ) && intval( $_GET['elementor_form_id'] ) !== 0 ) {
    $elementor_post_id = intval( $_GET['elementor_form_id'] );
    $adv_search_what   = get_option( 'elementor_search_what_' . $elementor_post_id, true );
    $adv_search_how    = get_option( 'elementor_search_how_' . $elementor_post_id, true );
    $adv_search_label  = get_option( 'elementor_search_label_' . $elementor_post_id, true );

    // Handle Elementor tabs — slice into per-tab arrays.
    if ( isset( $_GET['term_id'] ) && intval( $_GET['term_id'] ) !== 0 && isset( $adv_search_what['use_tabs'] ) ) {
        $term_id_elementor = intval( $_GET['term_id'] );
        $adv_search_what   = $adv_search_what[ $term_id_elementor ];
        $adv_search_how    = $adv_search_how[ $term_id_elementor ];
        $adv_search_label  = $adv_search_label[ $term_id_elementor ];
    }
} elseif ( isset( $_GET['term_counter'] ) ) {
    // Term counter slicing for multi-tab non-Elementor forms.
    $adv_search_fields_no = floatval( wpresidence_get_option( 'wp_estate_adv_search_fields_no' ) );
    $term_counter         = intval( $_GET['term_counter'] );
    $adv_search_what      = array_slice( $adv_search_what, $term_counter * $adv_search_fields_no, $adv_search_fields_no );
    $adv_search_how       = array_slice( $adv_search_how, $term_counter * $adv_search_fields_no, $adv_search_fields_no );
    $adv_search_label     = array_slice( $adv_search_label, $term_counter * $adv_search_fields_no, $adv_search_fields_no );
}

// Build options for 2026 prepare function (Elementor override support).
$search_options = array();
if ( isset( $_GET['elementor_form_id'] ) && intval( $_GET['elementor_form_id'] ) !== 0 ) {
    $search_options['adv_search_what'] = $adv_search_what;
    $search_options['adv_search_how']  = $adv_search_how;
}

// ── Step 1: Extract property ID search (bypasses normal search) ──
$id_array = '';
if ( is_array( $adv_search_what ) ) {
    foreach ( $adv_search_what as $i => $what_field ) {
        if ( 'property id' === $what_field || 'id' === $what_field ) {
            $slug_name = sanitize_key( wpestate_limit45( sanitize_title( $adv_search_label[ $i ] ) ) );
            if ( isset( $_GET[ $slug_name ] ) && intval( $_GET[ $slug_name ] ) > 0 ) {
                $id_array = intval( $_GET[ $slug_name ] );
            }
        }
    }
}

// ── Step 2: Normalize non-canonical GET param names before 2026 prepare ──
$normalized = $_GET;

// is2: keep old adv_location links working, but normalize everything to location.
if ( empty( $normalized['location'] ) && ! empty( $normalized['adv_location'] ) && isset( $normalized['is2'] ) ) {
    $normalized['location'] = $normalized['adv_location'];
    unset( $normalized['adv_location'] );
}

// is11: keyword_search → keyword (2026 keyword handler).
if ( ! empty( $normalized['keyword_search'] ) ) {
    $normalized['keyword'] = $normalized['keyword_search'];
    unset( $normalized['keyword_search'] );
}

// ── Step 3: Build 2026 params from normalized request ──
$params = wpestate_query_2026_prepare_search_arguments( $normalized, $search_options );

// is10: location as hidden_address meta LIKE (address matching, not taxonomy).
$location_value = '';
if ( ! empty( $_GET['location'] ) ) {
    $location_value = $_GET['location'];
} else if ( ! empty( $_GET['adv_location'] ) ) {
    $location_value = $_GET['adv_location'];
}

if ( isset( $_GET['is10'] ) && intval( $_GET['is10'] ) === 10 && ! empty( $location_value ) ) {
    $params['meta_query']   = isset( $params['meta_query'] ) ? $params['meta_query'] : array();
    $params['meta_query'][] = array(
        'key'     => 'hidden_address',
        'value'   => sanitize_text_field( wp_unslash( $location_value ) ),
        'compare' => 'LIKE',
        'type'    => 'CHAR',
    );
}

// Override ordering from adv search theme option (may be overridden by GET param).
$order = intval( wpresidence_get_option( 'wp_estate_property_list_type_adv_order', '' ) );
if ( isset( $params['order'] ) ) {
    $order = $params['order'];
}
$params['order'] = $order;

// Override posts_per_page from adv search setting.
$params['posts_per_page'] = intval( wpresidence_get_option( 'wp_estate_prop_no_adv_search', '' ) );

// ── Step 4: Execute query ──
if ( ! empty( $id_array ) ) {
    // Property ID search — bypass normal query, return single post.
    $prop_selection = new WP_Query( array(
        'post_type' => 'estate_property',
        'p'         => $id_array,
    ) );
} else {
    // Run through the 2026 layer (handles sold exclusion, featured priority,
    // keyword filter, geo, ordering, and pagination internally).
    $result         = wpestate_query_2026_property( 'directory', $params );
    $prop_selection = $result['query'];
}

// Extract keyword for map pins (used by wpestate_listing_pins).
$wpestate_keyword = isset( $params['keyword'] ) ? $params['keyword'] : '';

// Expose WP_Query args for sub-templates (saved_search_template, property_list_filters_search).
$args = isset( $result ) ? $result['args'] : array( 'post_type' => 'estate_property', 'p' => $id_array );

// ── Results and template rendering ──

// Get the number of results.
$num = $prop_selection->found_posts;

// Determine the property list type.
$property_list_type_status = esc_html( wpresidence_get_option( 'wp_estate_property_list_type_adv', '' ) );
$half_map_results = 0;

// Include the appropriate template based on property list type.
if ( $property_list_type_status == 2 ) {
    include( locate_template( 'templates/properties_list_templates/search_half_map_core.php' ) );
    $half_map_results = 1;
} else {
    include( locate_template( 'templates/properties_list_templates/search_normal_map_core.php' ) );
}

// ── Map pins ──
if ( wp_script_is( 'googlecode_regular', 'enqueued' ) ) {
    // Use the WP_Query args from the 2026 result for map pin generation.
    $mapargs = isset( $result ) ? $result['args'] : array();
    $mapargs['posts_per_page'] = intval( wpresidence_get_option( 'wp_estate_prop_no_adv_search', '' ) );
    $mapargs['fields']         = 'ids';

    $selected_pins = wpestate_listing_pins( 'blank', 0, $mapargs, 1, $wpestate_keyword, $id_array );

    wp_localize_script( 'googlecode_regular', 'googlecode_regular_vars2',
        array(
            'markers2'         => $selected_pins,
            'half_map_results' => $half_map_results,
        )
    );
}

get_footer();
