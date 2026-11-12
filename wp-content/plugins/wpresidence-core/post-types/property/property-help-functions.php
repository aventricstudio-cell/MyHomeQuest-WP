<?php
/**
 * Property Helper Functions
 *
 * Collection of utility functions for managing property-related data including:
 * - Country selection lists and dropdowns
 * - City and state data retrieval
 * - Category term metadata parsing
 * - Location hierarchy management
 *
 * These functions support various property management features throughout
 * the WpResidence theme and plugin.
 *
 * @package WpResidence
 * @subpackage Helpers
 * @since 1.0
 */

/**
 * Generates an HTML select element with a list of countries
 *
 * Creates a dropdown menu of countries with proper selection handling
 * and class assignment. Uses theme options for default country.
 *
 * @param string $selected The currently selected country
 * @param string $class    Additional CSS classes for the select element
 * @param string $name     The name attribute for the select element
 * @return string HTML select element with country options
 */
if (!function_exists('wpestate_country_list')):
    function wpestate_country_list($selected, $class = '', $name = 'property_country') {
        // Get array of countries
        $countries = wpestate_country_list_array();
        // Build select with dynamic name attribute
        $country_select = '<select id="property_country"  name="' . $name . '" class="' . $class . '">';

        // Get default country from theme options if none selected
        if ($selected == '') {
            $selected = wpresidence_get_option('wp_estate_general_country');
        }

        // Build options list
        foreach ($countries as $country) {
            $country_select .= '<option value="' . $country . '"';
            if ($selected == $country) {
                $country_select .= 'selected="selected"';
            }
            $country_select .= '>' . $country . '</option>';
        }

        $country_select .= '</select>';
        return $country_select;
    }
endif;

/**
 * Generates list items for country search dropdown
 *
 * Creates li elements formatted for a custom dropdown search interface.
 * Used in property search forms.
 *
 * @param string $selected Currently selected country
 * @return string HTML li elements for country options
 */
if (!function_exists('wpestate_country_list_search')):
    function wpestate_country_list_search($selected) {
        $countries = wpestate_country_list_array();
        $country_select_list = '';
        foreach ($countries as $country) {
            $country_select_list .= '<li role="presentation" data-value="' . $country . '">' . $country . '</li>';
        }
        return $country_select_list;
    }
endif;

/**
 * Agent list function (placeholder)
 * 
 * @param mixed $mypost Post object or ID
 * @return mixed Agent list data
 */
if (!function_exists('wpestate_agent_list')):
    function wpestate_agent_list($mypost) {
        return $agent_list;
    }
endif;

/**
 * Retrieves all property cities as option elements
 *
 * Gets all property_city taxonomy terms and formats them as
 * HTML option elements for use in select dropdowns.
 *
 * @param string $selected Currently selected city name
 * @return string HTML option elements for cities
 */
if (!function_exists('wpestate_get_all_cities')):
    function wpestate_get_all_cities($selected = '') {
        // Query city taxonomy terms
        $taxonomy = 'property_city';
        $args = array(
            'hide_empty' => false
        );
        $tax_terms = wpestate_get_cached_terms($taxonomy, $args);
        $select_city = '';

        // Build option elements
        foreach ($tax_terms as $tax_term) {
            $select_city .= '<option value="' . $tax_term->name . '" ';
            if ($tax_term->name == $selected) {
                $select_city .= ' selected="selected" ';
            }
            $select_city .= ' >' . $tax_term->name . '</option>';
        }
        return $select_city;
    }
endif;

/**
 * Retrieves all property states/counties as option elements
 *
 * Gets all property_county_state taxonomy terms and formats them as
 * HTML option elements for select dropdowns.
 *
 * @param string $selected Currently selected state name
 * @return string HTML option elements for states
 */
if (!function_exists('wpestate_get_all_states')):
    function wpestate_get_all_states($selected = '') {
        // Query county/state taxonomy terms
        $taxonomy = 'property_county_state';
        $args = array(
            'hide_empty' => false
        );
        $tax_terms = wpestate_get_cached_terms($taxonomy, $args);
        $select_city = '';

        // Build option elements
        foreach ($tax_terms as $tax_term) {
            $select_city .= '<option value="' . $tax_term->name . '" ';
            if ($tax_term->name == $selected) {
                $select_city .= ' selected="selected" ';
            }
            $select_city .= ' >' . $tax_term->name . '</option>';
        }
        return $select_city;
    }
endif;

/**
 * Parses property category term metadata
 *
 * Processes raw term metadata into a structured array with proper defaults.
 * Handles featured images, taglines, and other term meta fields.
 *
 * @param array $term_meta Raw term metadata array
 * @return array Processed term metadata with defaults
 */
function wpestate_parse_category_term_array($term_meta) {
    $term_meta_return = array();
    
    // Process page association
    if (isset($term_meta['pagetax']) && $term_meta['pagetax'] != '') {
        $term_meta_return['pagetax'] = $term_meta['pagetax'];
    } else {
        $term_meta_return['pagetax'] = '';
    }

    // Process featured image
    if (isset($term_meta['category_featured_image']) && $term_meta['category_featured_image'] != '') {
        $term_meta_return['category_featured_image'] = $term_meta['category_featured_image'];
    } else {
        $term_meta_return['category_featured_image'] = '';
    }

    // Process SVG icon for property features
    if (isset($term_meta['category_featured_image_icon']) && $term_meta['category_featured_image_icon'] != '') {
        $term_meta_return['category_featured_image_icon'] = $term_meta['category_featured_image_icon'];
    } else {
        $term_meta_return['category_featured_image_icon'] = '';
    }

    // Process category tagline
    if (isset($term_meta['category_tagline']) && $term_meta['category_tagline'] != '') {
        $term_meta_return['category_tagline'] = stripslashes($term_meta['category_tagline']);
    } else {
        $term_meta_return['category_tagline'] = '';
    }

    // Process attachment ID
    if (isset($term_meta['category_attach_id']) && $term_meta['category_attach_id'] != '') {
        $term_meta_return['category_attach_id'] = $term_meta['category_attach_id'];
    } else {
        $term_meta_return['category_attach_id'] = '';
    }

    // Process gallery images
    if (isset($term_meta['category_gallery']) && $term_meta['category_gallery'] != '') {
        $term_meta_return['category_gallery'] = $term_meta['category_gallery'];
    } else {
        $term_meta_return['category_gallery'] = '';
    }

    // Process attached documents
    if (isset($term_meta['category_documents']) && $term_meta['category_documents'] != '') {
        $term_meta_return['category_documents'] = $term_meta['category_documents'];
    } else {
        $term_meta_return['category_documents'] = '';
    }

    // Process geojson file path
    if ( isset( $term_meta['term_geojson'] ) && $term_meta['term_geojson'] !== '' ) {
        $term_meta_return['term_geojson'] = $term_meta['term_geojson'];
    } else {
        $term_meta_return['term_geojson'] = '';
    }

    // Map related fields. Support both legacy property_* keys and new term_* keys.
    $map_fields = array(
        'term_address'    => array( 'term_address', 'property_address' ),
        'term_zip'        => array( 'term_zip', 'property_zip' ),
        'term_country'    => array( 'term_country', 'property_country' ),
        'term_latitude'   => array( 'term_latitude', 'property_latitude' ),
        'term_longitude'  => array( 'term_longitude', 'property_longitude' ),
        'google_camera_angle' => array( 'google_camera_angle' ),
        'term_google_view'=> array( 'term_google_view', 'property_google_view' ),
    );

    foreach ( $map_fields as $return_key => $possible_keys ) {
        $value = '';
        foreach ( $possible_keys as $key ) {
            if ( isset( $term_meta[ $key ] ) && $term_meta[ $key ] !== '' ) {
                $value = $term_meta[ $key ];
                break;
            }
        }
        $term_meta_return[ $return_key ] = $value;
    }

    if ( isset( $term_meta['page_custom_zoom'] ) && $term_meta['page_custom_zoom'] !== '' ) {
        $term_meta_return['page_custom_zoom'] = $term_meta['page_custom_zoom'];
    } else {
        $term_meta_return['page_custom_zoom'] = 16;
    }

    return $term_meta_return;
}

add_action('wp_ajax_wpestate_delete_term_gallery_image', 'wpestate_delete_term_gallery_image');
/**
 * Ajax handler to remove an image from a term gallery.
 */
function wpestate_delete_term_gallery_image() {
    check_ajax_referer('wpestate_term_gallery', 'nonce');

    $term_id     = isset($_POST['term_id']) ? intval($_POST['term_id']) : 0;
    $gallery_ids = isset($_POST['gallery_ids']) ? sanitize_text_field($_POST['gallery_ids']) : '';

    if (!$term_id) {
        wp_send_json_error();
    }

    $option_key = 'taxonomy_' . $term_id;
    $term_meta  = get_option($option_key, array());

    if ($gallery_ids !== '') {
        $term_meta['category_gallery'] = $gallery_ids;
        update_option($option_key, $term_meta);
    }

    wp_send_json_success();
}

add_action('wp_ajax_wpestate_delete_term_document', 'wpestate_delete_term_document');
/**
 * Ajax handler to remove a document from a term.
 */
function wpestate_delete_term_document() {
    check_ajax_referer('wpestate_term_documents', 'nonce');

    $term_id      = isset($_POST['term_id']) ? intval($_POST['term_id']) : 0;
    $document_ids = isset($_POST['document_ids']) ? sanitize_text_field($_POST['document_ids']) : '';

    if (!$term_id) {
        wp_send_json_error();
    }

    $option_key = 'taxonomy_' . $term_id;
    $term_meta  = get_option($option_key, array());

    if ($document_ids !== '') {
        $term_meta['category_documents'] = $document_ids;
        update_option($option_key, $term_meta);
    }

    wp_send_json_success();
}