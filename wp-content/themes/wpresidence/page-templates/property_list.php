<?php
/**MILLDONE
 * Template Name: Properties list
 * src:page-templates\property_list.php
 * This template handles the display of property listings in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyListing
 * @since WpResidence 1.0
 */

// Check for WpResidence Core Plugin
if (!function_exists('wpestate_residence_functionality')) {
    wp_die(esc_html__('This page requires the WpResidence Core Plugin. Please activate it from the plugins menu!', 'wpresidence'));
}

get_header();

// Initialize variables
$wpestate_options        = get_query_var('wpestate_options');
$compare_submit          = wpestate_get_template_link('page-templates/compare_listings.php');
$property_number_to_show = intval(wpresidence_get_option('wp_estate_prop_no', ''));

// Read the property list order — page meta default, overridable via GET.
$order = get_post_meta($post->ID, 'listing_filter', true);
if (isset($_GET['order']) && is_numeric($_GET['order'])) {
    $order = intval($_GET['order']);
}

// Read current page number.
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
if (is_front_page()) {
    $paged = (get_query_var('page')) ? get_query_var('page') : 1;
}

$show_compare = 1;

// Build params from page meta defaults and pagination/order values.
$params = array(
    'paged'                      => $paged,
    'posts_per_page'             => $property_number_to_show,
    'order'                      => intval( $order ),
    'property_action_category'   => get_post_meta( $post->ID, 'adv_filter_search_action', true ),
    'property_category'          => get_post_meta( $post->ID, 'adv_filter_search_category', true ),
    'property_city'              => get_post_meta( $post->ID, 'current_adv_filter_city', true ),
    'property_area'              => get_post_meta( $post->ID, 'current_adv_filter_area', true ),
    'property_county_state'      => get_post_meta( $post->ID, 'current_adv_filter_county', true ),
    'featured_only'              => get_post_meta( $post->ID, 'show_featured_only', true ) === 'yes',
);

// Cache key derived from params so every unique combination gets its own transient.
$transient_appendix = wpestate_set_transient_name_multilang(
    'wpestate_prop_list' . md5( wp_json_encode( $params ) )
);

// Attempt to get cached payload (query + args).
$cached_payload = get_transient( $transient_appendix );
$cached_payload= false;
if ( false !== $cached_payload ) {
    $prop_selection = $cached_payload['query'];
    $args           = $cached_payload['args'];
} else {
    $result         = wpestate_query_2026_property( 'directory', $params );
    $prop_selection = $result['query'];
    $args           = $result['args'];
    set_transient( $transient_appendix, array( 'query' => $prop_selection, 'args' => $args ), 60 * 60 * 4 );
}

// Include template for property list display
include(locate_template('templates/properties_list_templates/property_list_page_normal_map_core.php'));

// Handle Maps script if enqueued
if (wp_script_is('googlecode_regular', 'enqueued')) {
    $mapargs                    = $args;
    $max_pins                   = intval(wpresidence_get_option('wp_estate_map_max_pins'));
    $mapargs['posts_per_page']  = $max_pins;
    $mapargs['offset']          = ($paged - 1) * $property_number_to_show;
    $mapargs['fields']          = 'ids';

    $map_transient = $transient_appendix . 'normal_list_maxpins' . $max_pins . '_offset_' . ($paged - 1) * $property_number_to_show;
    $selected_pins = wpestate_listing_pins($map_transient, 1, $mapargs, 1);
    wp_localize_script('googlecode_regular', 'googlecode_regular_vars2',
        array('markers2' => $selected_pins)
    );
}

get_footer();
?>