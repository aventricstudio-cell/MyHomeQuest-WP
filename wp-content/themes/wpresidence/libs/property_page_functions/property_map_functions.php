<?php
/* MILLDONE
*  src: libs\property_page_functions\property_map_functions.php
*/

/**
 * Display property map.
 *
 * This function generates the HTML for displaying a property map. It can output
 * the content either as a tab or as an accordion item.
 *
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Optional. Whether to display as a tab. Default ''.
 * @param string $tab_active_class Optional. CSS class for active tab. Default ''.
 * @return string|void HTML output if $is_tab is 'yes', otherwise echoes the HTML.
 */
if ( ! function_exists( 'wpestate_property_map_v2' ) ) :
    function wpestate_property_map_v2( $postID, $is_tab = '', $tab_active_class = '' ) {
        // Retrieve label data for map
        $data = wpestate_return_all_labels_data( 'map' );

        // Prepare the label for display
        $label = wpestate_property_page_prepare_label( $data['label_theme_option'], $data['label_default'] );

        // Generate the map content using shortcode
        $content = do_shortcode( sprintf( '[property_page_map propertyid="%d"][/property_page_map]', $postID ) );

        // Determine whether to display as a tab or accordion
        if ( $is_tab === 'yes' ) {
            // Return the content as a tab item
            return wpestate_property_page_create_tab_item( $content, $label, $data['tab_id'], $tab_active_class );
        } else {
            // Echo the content as an accordion item
            echo (
                wpestate_property_page_create_acc(
                    $content,
                    $label,
                    $data['accordion_id'],
                    $data['accordion_id'] . '_collapse'
                )
            );
        }
    }
endif;





/*
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * */


if (!function_exists('wpestate_property_page_map_function')):


  function wpestate_property_page_map_function($attributes, $content = null) {
        global $post;
        $use_mimify = wpresidence_get_option('wp_estate_use_mimify', '');
        $mimify_prefix = '';
        if ($use_mimify === 'yes') {
            $mimify_prefix = '.min';
        }

        if (!wp_script_is('googlemap', 'enqueued')) {
            wpestate_load_google_map();
        }


        $return_string = '';
        $istab = 0;
        $attributes = shortcode_atts(
                array(
                    'propertyid' => '',
                    'istab' => '',
                ), $attributes);

        if (isset($attributes['propertyid'])) {
            $the_id = $propertyid = $attributes['propertyid'];
        }

        if (isset($attributes['istab'])) {
            $istab = $attributes['istab'];
        }

        if (isset($attributes['single_marker'])) {
            $nooflisting = $attributes['single_marker'];
        }


        $wpestate_currency = wpresidence_get_option('wp_estate_currency_symbol', '');
        $where_currency = wpresidence_get_option('wp_estate_where_currency_symbol', '');
        $title_orig = get_the_title($the_id);
        $title_orig = str_replace('%', '', $title_orig);
        $types = get_the_terms($the_id, 'property_category');
        if ($types && !is_wp_error($types)) {
            foreach ($types as $single_type) {
                $prop_type[] = $single_type->name; //$single_type->slug;
                $prop_type_name[] = $single_type->name;
                $slug = $single_type->slug;
                $parent_term = $single_type->parent;
            }

            $single_first_type = $prop_type[0];
            $single_first_type_pin = $prop_type[0];
            if ($parent_term != 0) {
                $single_first_type = $single_first_type . wpestate_add_parent_infobox($parent_term, 'property_category');
            }
            $single_first_type_name = $prop_type_name[0];
        } else {
            $single_first_type = '';
            $single_first_type_name = '';
            $single_first_type_pin = '';
        }


        $types_act = get_the_terms($the_id, 'property_action_category');
        if ($types_act && !is_wp_error($types_act)) {
            foreach ($types_act as $single_type) {
                $prop_action[] = $single_type->name; //$single_type->slug;
                $prop_action_name[] = $single_type->name;
                $slug = $single_type->slug;
                $parent_term = $single_type->parent;
            }
            $single_first_action = $prop_action[0];
            $single_first_action_pin = $prop_action[0];

            if ($parent_term != 0) {
                $single_first_action = $single_first_action . wpestate_add_parent_infobox($parent_term, 'property_action_category');
            }
            $single_first_action_name = $prop_action_name[0];
        } else {
            $single_first_action = '';
            $single_first_action_name = '';
            $single_first_action_pin = '';
        }


        if ($single_first_action == '' || $single_first_action == '') {
            $pin = sanitize_key(wpestate_limit54($single_first_type_pin . $single_first_action_pin));
        } else {
            $pin = sanitize_key(wpestate_limit27($single_first_type_pin)) . sanitize_key(wpestate_limit27($single_first_action_pin));
        }

        $hidingprice = wpestate_hide_show_price( $the_id );

        if ( $hidingprice ) {
            $hiddenPriceLabel = wpresidence_get_option( 'wp_estate_property_hide_price_text', '', 'Price on application' );
            // If price is hidden, return empty string or print nothing
            $price = $hiddenPriceLabel;
            $pin_price = $hiddenPriceLabel;
            $clean_price = $hiddenPriceLabel;
        } else {
        // If price is not hidden, format it as usual

        //// get price
        $price = floatval(get_post_meta($the_id, 'property_price', true));
        $price_label = esc_html(get_post_meta($the_id, 'property_label', true));
        $price_label_before = esc_html(get_post_meta($the_id, 'property_label_before', true));
        $clean_price = floatval(get_post_meta($the_id, 'property_price', true));
        if ($price == 0) {
            $price = $price_label_before . '' . $price_label;
            $pin_price = '';
        } else {
            $th_separator = stripslashes(wpresidence_get_option('wp_estate_prices_th_separator', ''));
            $pin_price = $price;

            $price = wpestate_format_number_price($price, $th_separator);

            if ($where_currency == 'before') {
                $price = $wpestate_currency . $price;
            } else {
                $price = $price . $wpestate_currency;
            }

            if (wpresidence_get_option('wp_estate_use_price_pins_full_price', '') == 'no') {

                $pin_price = wpestate_price_pin_converter($pin_price, $where_currency, $wpestate_currency);
            } else {
                $pin_price == "<span class='infocur infocur_first'>" . $price_label_before . "</span>" . $price . "<span class='infocur'>" . $price_label . "</span>";
            }

            $price = "<span class='infocur infocur_first'>" . $price_label_before . "</span>" . $price . "<span class='infocur'>" . $price_label . "</span>";
        }
        }

        $rooms = get_post_meta($the_id, 'property_bedrooms', true);
        $bathrooms = get_post_meta($the_id, 'property_bathrooms', true);

        $size = wpestate_get_converted_measure($the_id, 'property_size');

        $gmap_lat = esc_html(get_post_meta($propertyid, 'property_latitude', true));
        $gmap_long = esc_html(get_post_meta($propertyid, 'property_longitude', true));
        $property_add_on = ' data-post_id="' . $propertyid . '" data-cur_lat="' . $gmap_lat . '" data-cur_long="' . $gmap_long . '" ';
        $property_add_on .= ' data-title="' . $title_orig . '"  data-pin="' . $pin . '" data-thumb="' . rawurlencode(get_the_post_thumbnail($the_id, 'agent_picture_thumb')) . '" ';
        $property_add_on .= ' data-price="' . rawurlencode($price) . '" ';
        $property_add_on .= ' data-single-first-type="' . rawurlencode($single_first_type) . '"  data-single-first-action="' . rawurlencode($single_first_action) . '" ';
        $property_add_on .= ' data-rooms="' . rawurlencode($rooms) . '" data-size="' . rawurlencode($size) . '" data-bathrooms="' . rawurlencode($bathrooms) . '" ';
        $property_add_on .= ' data-prop_url="' . rawurlencode(esc_url(get_permalink($the_id))) . '" ';
        $property_add_on .= ' data-pin_price="' . rawurlencode($pin_price) . '" ';
        $property_add_on .= ' data-clean_price="' . rawurlencode($clean_price) . '" ';

        wpestate_load_google_map();
        $unique_id=rand(1,9999);
        $return_string = '<div class="google_map_shortcode_wrapper  ' . wpresidence_return_class_leaflet() . '">
                <div id="gmapzoomplus_sh_'.intval($unique_id).'"  class="smallslidecontrol gmapzoomplus_sh shortcode_control" ><i class="fas fa-plus"></i> </div>
                <div id="gmapzoomminus_sh_'.intval($unique_id).'" class="smallslidecontrol gmapzoomminus_sh shortcode_control" ><i class="fas fa-minus"></i></div>';
        $return_string .= wpestate_show_poi_onmap('sh');
        $return_string .= '<div id="slider_enable_street_sh_'.intval($unique_id).'" class="slider_enable_street_sh" data-placement="bottom" data-original-title="' . esc_html__('Street View', 'wpresidence-core') . '"> <i class="fas fa-location-arrow"></i>    </div>';
        $return_string .= '<div class="googleMap_shortcode_class" id="googleMap_shortcode_'.intval($unique_id).'" ' . $property_add_on . ' ></div></div>';

        if ($istab != 1) {
            
        }
        return $return_string;
    }

endif;