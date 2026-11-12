<?php
/** MILLDONE
 *  src: libs\property_page_functions\property_address_section_functions.php
 */

/**
 * Generate the property address section.
 *
 * This function creates either a tab item or an accordion section
 * for the property address information, based on the provided parameters.
 *
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Whether the address section is part of a tab layout ('yes' or '').
 * @param string $tab_active_class The CSS class for active tabs (if applicable).
 * @return string|void HTML content of the address section or void if not a tab.
 */
if (!function_exists('wpestate_property_address_v2')) :
    function wpestate_property_address_v2($postID, $is_tab = '', $tab_active_class = '') {
        // Retrieve label data for the address section
        $data = wpestate_return_all_labels_data('address');
        $label = wpestate_property_page_prepare_label($data['label_theme_option'], $data['label_default']);

        // Get the address content
        $content = estate_listing_address($postID);

        // Determine whether to return a tab item or print an accordion section
        if ($is_tab === 'yes') {
            return wpestate_property_page_create_tab_item($content, $label, $data['tab_id'], $tab_active_class);
        } else {
            $accordion_content = wpestate_property_page_create_acc(
                $content,
                $label,
                $data['accordion_id'],
                'accordion_property_address_collapse'
            );
            
            // Use echo for output, which is preferable in WordPress
            echo $accordion_content;
        }
    }
endif;



/**
 * Generate the listing address details.
 *
 * This function creates a formatted string containing various address details
 * for a property listing, including city, area, county, zip code, and country.
 *
 * @param int    $post_id                  The ID of the property post.
 * @param array  $wpestate_prop_all_details Optional. An array of property details.
 * @param string $columns                  Optional. The number of columns for layout.
 * @return string Formatted HTML string containing address details.
 */
if (!function_exists('estate_listing_address')) :
    function estate_listing_address($post_id, $wpestate_prop_all_details = '', $columns = '') {
        // Retrieve taxonomy terms
        $property_city   = get_the_term_list($post_id, 'property_city', '', ', ', '');
        $property_area   = get_the_term_list($post_id, 'property_area', '', ', ', '');
        $property_county = get_the_term_list($post_id, 'property_county_state', '', ', ', '');

        // Retrieve property details
        $property_details = wpresidence_get_property_address_details($post_id, $wpestate_prop_all_details);

        // Get column class
        $colmd = wpestat_get_content_comuns($columns, 'address');

        // Build address details string
        $return_string = '<div class="row">';
        $return_string .= wpresidence_build_address_details($property_details, $property_city, $property_area, $property_county, $colmd);

        // Add Google Maps link
        $return_string .= wpresidence_get_google_maps_link($property_details['address'], strip_tags($property_city));

        $return_string .= '</div>';
        return $return_string;
    }
endif;

/**
 * Retrieve property details from post meta or custom field.
 *
 * @param int   $post_id                  The ID of the property post.
 * @param array $wpestate_prop_all_details Optional. An array of property details.
 * @return array An array of property details.
 */
if(!function_exists('wpresidence_get_property_address_details')):
    function wpresidence_get_property_address_details($post_id, $wpestate_prop_all_details) {
        if (!empty($wpestate_prop_all_details)) {
            return array(
                'address' => esc_html(wpestate_return_custom_field($wpestate_prop_all_details, 'property_address')),
                'zip'     => esc_html(wpestate_return_custom_field($wpestate_prop_all_details, 'property_zip')),
                'country' => esc_html(wpestate_return_custom_field($wpestate_prop_all_details, 'property_country'))
            );
        } else {
            return array(
                'address' => esc_html(get_post_meta($post_id, 'property_address', true)),
                'zip'     => esc_html(get_post_meta($post_id, 'property_zip', true)),
                'country' => esc_html(get_post_meta($post_id, 'property_country', true))
            );
        }
    }
endif;


/**
 * Build the address details string.
 *
 * @param array  $details         Property details array.
 * @param string $property_city   The property city.
 * @param string $property_area   The property area.
 * @param string $property_county The property county.
 * @param string $colmd           The column class.
 * @return string Formatted HTML string of address details.
 */

if(!function_exists('wpresidence_build_address_details')):
    function wpresidence_build_address_details($details, $property_city, $property_area, $property_county, $colmd) {
        $return_string = '';
        $address_items = array(
            'address' => esc_html__('Address', 'wpresidence'),
            'city'    => esc_html__('City', 'wpresidence'),
            'area'    => esc_html__('Area', 'wpresidence'),
            'county'  => esc_html__('State/County', 'wpresidence'),
            'zip'     => esc_html__('Zip', 'wpresidence'),
            'country' => esc_html__('Country', 'wpresidence')
        );

        foreach ($address_items as $key => $label) {
            $value = isset($details[$key]) ? $details[$key] : ${$key === 'city' ? 'property_city' : "property_$key"};
            if (!empty($value)) {
                if ($key === 'country' && function_exists('icl_translate')) {
                    $value = wpresidence_translate_country($value);
                }
                 $return_string .= sprintf(
                    '<div class="listing_detail   wpresidence-detail-%s col-md-%s"><strong>%s:</strong> %s</div>',
                    esc_attr(sanitize_title($label)),
                    esc_attr($colmd),
                    esc_html($label),
                    wp_kses_post($value)
                );

            }
        }

        return $return_string;
    }
endif;




/**
 * Translate country name if WPML is active.
 *
 * @param string $country The country name.
 * @return string Translated country name.
 */

if(!function_exists('wpresidence_translate_country')):
    function wpresidence_translate_country($country) {
        do_action('wpml_register_single_string', 'WpResidence', "WpResidence - Country - $country", $country);
        return apply_filters('wpml_translate_single_string', $country, 'WpResidence', "WpResidence - Country - $country");
    }
endif;


/**
 * Generate Google Maps link for the property.
 *
 * @param string $address The property address.
 * @param string $city    The property city.
 * @return string HTML link to Google Maps.
 */

if(!function_exists('wpresidence_get_google_maps_link')):
    function wpresidence_get_google_maps_link($address, $city) {
        $url = urlencode("$address,$city");
        $google_map_url = "http://maps.google.com/?q=$url";
        return sprintf(
            '<div class="col-md-12 p-0"> <a href="%s" target="_blank" rel="noopener" class="acc_google_maps">%s</a></div>',
            esc_url($google_map_url),
            esc_html__('Open In Google Maps', 'wpresidence')
        );
    }
endif;
