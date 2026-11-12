<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enable TinyMCE editor for taxonomy term descriptions
 * and allow HTML content to be saved.
 */
function wpestate_enable_tinymce_term_description() {
    // Allow HTML in term descriptions
    remove_filter( 'pre_term_description', 'wp_filter_kses' );
    remove_filter( 'term_description', 'wp_kses_data' );

    $taxonomies = array(
        'property_category',
        'property_action_category',
        'property_city',
        'property_county_state',
        'property_area',
        'property_features',
        'property_status',
        'property_category_agent',
        'property_action_category_agent',
        'property_city_agent',
        'property_area_agent',
        'property_county_state_agent',
        'category_agency',
        'action_category_agency',
        'city_agency',
        'area_agency',
        'county_state_agency',
        'property_category_developer',
        'property_action_developer',
        'property_city_developer',
        'property_area_developer',
        'property_county_state_developer',
    );

    foreach ( $taxonomies as $taxonomy ) {
        // Output editor before custom tab fields.
        add_action( "{$taxonomy}_edit_form_fields", 'wpestate_taxonomy_description_editor', 5 );
        add_action( "{$taxonomy}_add_form_fields", 'wpestate_taxonomy_description_editor', 5 );
    }
}
add_action( 'init', 'wpestate_enable_tinymce_term_description', 20 );

/**
 * Output TinyMCE editor for term description.
 *
 * @param WP_Term|string $term Term object when editing, taxonomy slug when adding.
 */
function wpestate_taxonomy_description_editor( $term ) {
    $content = '';
    if ( is_object( $term ) && isset( $term->description ) ) {
        $content = html_entity_decode( $term->description );
    }
    $settings = array(
        'textarea_name' => 'description',
        'media_buttons' => true,
        'quicktags'     => true,
        'textarea_rows' => 5,
    );

    if ( is_object( $term ) ) {
        echo '<tr class="form-field term-description-wrap">';
        echo '<th scope="row"><label for="wpestate_term_description">' . esc_html__( 'Description', 'wpresidence-core' ) . '</label></th>';
        echo '<td>';
        wp_editor( $content, 'wpestate_term_description', $settings );
        echo '</td></tr>';
    } else {
        echo '<div class="form-field term-description-wrap">';
        echo '<label for="wpestate_term_description">' . esc_html__( 'Description', 'wpresidence-core' ) . '</label>';
        wp_editor( $content, 'wpestate_term_description', $settings );
        echo '</div>';
    }
}

/**
 * Remove the default plain description field to avoid duplicate inputs.
 */
function wpestate_remove_default_term_description() {
    $screen = get_current_screen();
    if ( ! isset( $screen->taxonomy ) ) {
        return;
    }

    $taxonomies = array(
        'property_category',
        'property_action_category',
        'property_city',
        'property_county_state',
        'property_area',
        'property_features',
        'property_status',
        'property_category_agent',
        'property_action_category_agent',
        'property_city_agent',
        'property_area_agent',
        'property_county_state_agent',
        'category_agency',
        'action_category_agency',
        'city_agency',
        'area_agency',
        'county_state_agency',
        'property_category_developer',
        'property_action_developer',
        'property_city_developer',
        'property_area_developer',
        'property_county_state_developer',
    );

    if ( in_array( $screen->taxonomy, $taxonomies, true ) ) {
        ?>
        <script type="text/javascript">
        jQuery( document ).ready( function( $ ) {
            $('#tag-description, #description').closest('.form-field').remove();
        } );
        </script>
        <?php
    }
}
add_action( 'admin_footer', 'wpestate_remove_default_term_description' );
