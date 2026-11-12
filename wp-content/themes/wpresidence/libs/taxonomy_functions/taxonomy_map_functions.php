<?php
/**
 * Display map for taxonomy term.
 * Similar to wpestate_property_page_map_function but for terms.
 */
if ( ! function_exists( 'wpestate_get_term_map_data' ) ) :
function wpestate_get_term_map_data( $term, $custom_post_type = 'estate_property' ) {
    if ( is_wp_error( $term ) || ! $term ) {
        return [
            'markers2' => '',
            'taxonomy' => '',
            'term'     => '',
        ];
    }

    $taxonomy   = $term->taxonomy;
    $term_slug  = $term->slug;
    $paged      = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    $prop_no    = intval( wpresidence_get_option( 'wp_estate_prop_no', '' ) );
    $max_pins   = intval( wpresidence_get_option( 'wp_estate_map_max_pins' ) );

    $tax_query  = [
        [
            'taxonomy' => $taxonomy,
            'field'    => 'slug',
            'terms'    => $term_slug,
        ],
    ];

    $mapargs = [
        'post_type'      => $custom_post_type,
        'post_status'    => 'publish',
        'paged'          => $paged,
        'posts_per_page' => $max_pins,
        'offset'         => ( $paged - 1 ) * $prop_no,
        'tax_query'      => $tax_query,
        'fields'         => 'ids',
    ];

    if ( function_exists( 'wpestate_remove_sold_listings' ) ) {
        $mapargs = wpestate_remove_sold_listings( $mapargs );
    }

    $transient_appendix  = '_taxonomy_' . $taxonomy . '_' . $custom_post_type . '_' . $term_slug . '_prop_' . $prop_no . 'paged_' . $paged;
    $transient_appendix .= '_maxpins' . $max_pins . '_offset_' . ( ( $paged - 1 ) * $prop_no );

    $selected_pins = wpestate_listing_pins( $transient_appendix, 1, $mapargs, 1 );

    return [
        'markers2' => $selected_pins,
        'taxonomy' => $taxonomy,
        'term'     => $term_slug,
    ];
}
endif;

if ( ! function_exists( 'wpestate_term_page_map_function' ) ) :
function wpestate_term_page_map_function( $attributes, $content = null ) {
    $use_mimify   = wpresidence_get_option( 'wp_estate_use_mimify', '' );
    $mimify_prefix = '';
    if ( $use_mimify === 'yes' ) {
        $mimify_prefix = '.min';
    }

    if ( ! wp_script_is( 'googlemap', 'enqueued' ) ) {
        wpestate_load_google_map();
    }

    $attributes = shortcode_atts(
        array(
            'termid' => '',
            'istab'  => '',
        ),
        $attributes
    );

    $term_id = 0;
    if ( isset( $attributes['termid'] ) && $attributes['termid'] !== '' ) {
        $term_id = intval( $attributes['termid'] );
    } else {
        $queried = get_queried_object();
        if ( isset( $queried->term_id ) ) {
            $term_id = $queried->term_id;
        }
    }

    if ( ! $term_id ) {
       if (   \Elementor\Plugin::$instance->editor->is_edit_mode() || is_singular( 'wpestate-studio' ) ) {
            $latest_terms = get_terms([
                'taxonomy'   => 'property_city',
                'hide_empty' => false,
                'number'     => 1,
                'orderby'    => 'term_id',
                'order'      => 'DESC',
            ]);
            if ( ! empty( $latest_terms ) && ! is_wp_error( $latest_terms ) ) {
                $term_id = $latest_terms[0]->term_id;
            }
        }
    }

    $term        = get_term( $term_id );
    $term_meta   = get_option( "taxonomy_{$term_id}" );
    $term_lat    = isset( $term_meta['term_latitude'] ) ? $term_meta['term_latitude'] : '';
    $term_long   = isset( $term_meta['term_longitude'] ) ? $term_meta['term_longitude'] : '';
    $zoom_level  = isset( $term_meta['page_custom_zoom'] ) ? $term_meta['page_custom_zoom'] : '';
    $camera      = isset( $term_meta['google_camera_angle'] ) ? $term_meta['google_camera_angle'] : '';
    $term_geojson = isset( $term_meta['term_geojson'] ) ? $term_meta['term_geojson'] : '';

    $unique_id = rand( 1, 9999 );

    $data_attr  = ' data-term_id="' . intval( $term_id ) . '"';
    $data_attr .= ' data-cur_lat="' . esc_attr( $term_lat ) . '" data-cur_long="' . esc_attr( $term_long ) . '"';
    $data_attr .= ' data-title="' . esc_attr( $term->name ) . '" data-pin="single_pin"';
    $data_attr .= ' data-prop-zoom="' . intval( $zoom_level ) . '" data-google-angle="' . intval( $camera ) . '"';
    if ( $term_geojson ) {
        $data_attr .= ' data-geojson="' . esc_url( $term_geojson ) . '"';
    }

    $return_string  = '<div class="google_map_shortcode_wrapper ' . wpresidence_return_class_leaflet() . '">';
    $return_string .= '<div id="gmapzoomplus_sh_' . intval( $unique_id ) . '" class="smallslidecontrol gmapzoomplus_sh shortcode_control"><i class="fas fa-plus"></i> </div>';
    $return_string .= '<div id="gmapzoomminus_sh_' . intval( $unique_id ) . '" class="smallslidecontrol gmapzoomminus_sh shortcode_control"><i class="fas fa-minus"></i></div>';
    $return_string .= wpestate_show_poi_onmap( 'sh' );
    $return_string .= '<div id="slider_enable_street_sh_' . intval( $unique_id ) . '" class="slider_enable_street_sh" data-placement="bottom" data-original-title="' . esc_html__( 'Street View', 'wpresidence-core' ) . '"> <i class="fas fa-location-arrow"></i> </div>';
    $return_string .= '<div class="googleMap_term_shortcode_class" id="googleMap_term_shortcode_' . intval( $unique_id ) . '" ' . $data_attr . '></div></div>';

    $inline_target = wp_script_is( 'googlecode_regular', 'enqueued' )
        ? 'googlecode_regular'
        : 'mapfunctions';

    // Build markers for this term so JS always has access to them
    $term_map_data = wpestate_get_term_map_data( $term );

    wp_localize_script( $inline_target, 'googlecode_regular_vars2', $term_map_data );

    // Ensure the JS handler runs even when no other map scripts trigger it
    wp_add_inline_script(
        $inline_target,
        'jQuery(function(){ console.log("term map shortcode init"); console.log("term map data", window.googlecode_regular_vars2); wpestate_term_map_shortcode_function(); });'
    );

    return $return_string;
}
endif;
add_shortcode( 'term_page_map', 'wpestate_term_page_map_function' );
