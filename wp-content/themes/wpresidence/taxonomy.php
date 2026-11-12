<?php
/** MILLDONE
 * Taxonomy Template
 * src: taxonomy.php
 * This template handles the display of taxonomy archives for various post types
 * including properties, agents, agencies, and developers.
 *
 * @package WpResidence
 * @subpackage Taxonomy
 * @since WpResidence 1.0
 */

get_header();

// Initialize variables
$wpestate_options   = get_query_var('wpestate_options');
$compare_submit     = wpestate_get_template_link('page-templates/compare_listings.php');
$current_user       = wp_get_current_user();
$curent_fav         = wpestate_return_favorite_listings_per_user();
$custom_post_type   = 'estate_property';
$col_class          = 4;

// Set up currency and pagination variables
$wpestate_currency  = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
$where_currency     = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
$prop_no            = intval(wpresidence_get_option('wp_estate_prop_no', ''));
$paged              = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Set up property unit display options
$wpestate_prop_unit = esc_html(wpresidence_get_option('wp_estate_prop_unit', ''));
$prop_unit_class    = $wpestate_prop_unit == 'list' ? "ajax12" : "";
$align_class        = $wpestate_prop_unit == 'list' ? 'the_list_view' : "";

// Adjust column class based on content width
if ($wpestate_options['content_class'] == 'col-md-12') {
    $col_class = 3;
}

// Set up taxonomy variables
$taxonomy   = get_query_var('taxonomy');
$term       = get_query_var('term');

// Determine custom post type based on taxonomy
if (in_array($taxonomy, ['property_category_agent', 'property_action_category_agent', 'property_city_agent', 'property_area_agent', 'property_county_state_agent'])) {
    $custom_post_type = 'estate_agent';
} elseif (in_array($taxonomy, ['category_agency', 'action_category_agency', 'city_agency', 'area_agency', 'county_state_agency'])) {
    $custom_post_type = 'estate_agency';
} elseif (in_array($taxonomy, ['property_county_state_developer', 'property_category_developer', 'property_action_developer', 'property_city_developer', 'property_area_developer'])) {
    $custom_post_type = 'estate_developer';
}

// Execute the query — 2026 layer for properties, raw WP_Query for agents/agencies/developers.
if ( 'estate_property' === $custom_post_type ) {
    // Build 2026 params from the current taxonomy term.
    $order  = intval( wpresidence_get_option( 'wp_estate_property_list_type_tax_order', '' ) );
    $params = array(
        'paged'          => $paged,
        'posts_per_page' => $prop_no,
        'order'          => $order,
        $taxonomy        => array( $term ),
    );

    // Run via 2026 layer — sold exclusion and ordering handled internally.
    $result         = wpestate_query_2026_property( 'directory', $params );
    $prop_selection = $result['query'];
    $mapargs        = $result['args'];
} else {
    // Agents, agencies, developers — raw WP_Query (2026 layer is property-only).
    $tax_query = array(
        'taxonomy' => $taxonomy,
        'field'    => 'slug',
        'terms'    => $term,
    );

    $args = array(
        'post_type'      => $custom_post_type,
        'post_status'    => 'publish',
        'paged'          => $paged,
        'posts_per_page' => $prop_no,
        'tax_query'      => array(
            'relation' => 'AND',
            $tax_query,
        ),
    );

    $prop_selection = new WP_Query( $args );

    $mapargs = array(
        'post_type'      => 'estate_property',
        'post_status'    => 'publish',
        'paged'          => $paged,
        'posts_per_page' => $prop_no,
        'meta_key'       => 'prop_featured',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    );
}

// Determine the property list type
$property_list_type_status = esc_html(wpresidence_get_option('wp_estate_property_list_type', ''));

// Include the appropriate template based on post type and list type
if (in_array($custom_post_type, ['estate_agent', 'estate_agency', 'estate_developer'])) {
    include(locate_template('templates/properties_list_templates/taxonomy_page_normal_map_core.php'));
} else {
    if ($property_list_type_status == 2) {
        include(locate_template('templates/properties_list_templates/taxonomy_page_half_map_core.php'));
    } else {
        include(locate_template('templates/properties_list_templates/taxonomy_page_normal_map_core.php'));
    }
}

wp_reset_query();
wp_reset_postdata();

// Handle Google Maps script if it's enqueued
$max_pins = intval(wpresidence_get_option('wp_estate_map_max_pins'));
$mapargs['posts_per_page'] = $max_pins;
$mapargs['offset'] = ($paged - 1) * $prop_no;
$mapargs['fields'] = 'ids';

$transient_appendix = '_taxonomy_' . $taxonomy . '_' . $custom_post_type . '_' . $term . '_prop_' . $prop_no . 'paged_' . $paged;
$transient_appendix .= '_maxpins' . $max_pins . '_offset_' . ($paged - 1) * $prop_no;

$term_object   = get_term_by('slug', $term, $taxonomy);
$term_map_data = wpestate_get_term_map_data( $term_object, $custom_post_type );

if (wp_script_is('googlecode_regular', 'enqueued')) {
    wp_localize_script('googlecode_regular', 'googlecode_regular_vars2', $term_map_data);
} else {
    wp_localize_script('mapfunctions', 'googlecode_regular_vars2', $term_map_data);
}

get_footer();