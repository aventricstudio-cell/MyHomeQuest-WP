<?php
/**
 * Generate property details with inline text
 *
 * This function processes a string containing placeholders for property details
 * and replaces them with actual property data in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 1.0.0
 */

if (!function_exists('wpestate_estate_property_design_intext_details')) :
/**
 * Process and replace property detail placeholders with actual data
 *
 * @param array $attributes Shortcode attributes
 * @param string $content Shortcode content containing property detail placeholders
 * @return string Processed HTML string with property details
 */
function wpestate_estate_property_design_intext_details($attributes, $content = '') {
    $return_string = '';

    // Parse shortcode attributes
    $attributes = shortcode_atts(array(
        'css' => '',
        'is_elementor' => '',
        'content' => ''
    ), $attributes);

   // Retrieve property ID based on Elementor attributes
   $property_id = wpestate_return_property_id_elementor_builder($attributes);

 
    // Handle Elementor compatibility
    if (isset($attributes['is_elementor']) && intval($attributes['is_elementor']) == 1) {
  
        $content = $attributes['content'];
    }

    // Generate CSS class
    $css_class = '';
    if (function_exists('vc_shortcode_custom_css_class')) {
        $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($attributes['css'], ' '), '', $attributes);
    }

    // Get property features
    $feature_terms =wpestate_get_cached_terms('property_features');
    $features_details = array();
    if (is_array($feature_terms)) {
        foreach ($feature_terms as $term) {
            $features_details[$term->slug] = $term->name;
        }
    }

    // Find all placeholders in the content
    preg_match_all("/\{[^\}]*\}/", $content, $matches);

    $return_string = '<div class="wpestate_estate_property_design_intext_details ' . esc_attr($css_class) . '">';

    // Process each placeholder
    foreach ($matches[0] as $value) {
        $element = trim($value, '{}');
        $replace = wpestate_process_property_detail_element($element, $property_id, $features_details);
        
        // Ensure $replace is a string
        $replace = (is_string($replace) || is_numeric($replace)) ? (string)$replace : '';
        
        $content = str_replace($value, $replace, $content);
    }

    $return_string .= do_shortcode($content) . '</div>';

    // Reset query and post data
    wp_reset_query();
    wp_reset_postdata();
   

    // Return empty string if no content
    if (empty($replace) || $replace === esc_html__('Not Available', 'wpresidence-core')) {
        return '';
    }

    return $return_string;
}
endif;



/**
 * Process individual property detail element
 *
 * @param string $element Property detail element to process
 * @param int $propid Property ID
 * @param array $features_details Array of property features
 * @return string Processed property detail
 */
function wpestate_process_property_detail_element($element, $propid, $features_details) {
    switch ($element) {
        case 'prop_id':
            return $propid;
        case 'prop_url':
            return esc_url(get_permalink($propid));
        case 'title':
            return get_the_title($propid);
        case 'description':
            return estate_listing_content($propid);
        case 'property_price':
            $currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
            $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
            return wpestate_show_price($propid, $currency, $where_currency, 1);
        case 'property_second_price':
            $currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
            $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
            return wpestate_show_price($propid, $currency, $where_currency, 1,'yes');
        case 'property_size':
        case 'property_lot_size':
            return wpestate_get_converted_measure($propid, $element);
        case 'property_status':
            $status = get_the_term_list($propid, 'property_status', '', ',', '');
            return ($status === 'normal') ? '' : $status;
        case 'property_pdf':
            return wpestate_property_sh_download_pdf($propid);
        case 'favorite_action':
            return wpestate_generate_favorite_action($propid);
        case 'page_views':
            return wpestate_generate_page_views($propid);
        case 'print_action':
            return '<i class="fas fa-print" id="print_page" data-propid="' . esc_attr($propid) . '"></i>';
        case 'facebook_share':
        case 'twiter_share':
        case 'pinterest_share':
        case 'whatsapp_share':
        case 'email_share':
            return wpestate_generate_social_share($element, $propid);
        case 'listing_id':
            return wpestate_get_custom_field_value($propid, 'property_internal_id');
        default:
            if (in_array($element, $features_details)) {
                return has_term($element, 'property_features', $propid) ? 'yes' : 'no';
            } elseif (in_array($element, ['property_category', 'property_action_category', 'property_city', 'property_area', 'property_county_state'])) {
                return get_the_term_list($propid, $element, '', ', ', '');
            } else {
                return wpestate_get_custom_field_value($propid, $element);
            }
    }
}

/**
 * Generate favorite action HTML
 *
 * @param int $propid Property ID
 * @return string Favorite action HTML
 */
function wpestate_generate_favorite_action($propid) {
    $current_fav = wpestate_return_favorite_listings_per_user();
    $favorite_class = 'isnotfavorite';
    $favorite_text = esc_html__('add to favorites', 'wpresidence-core');
    if ($current_fav && in_array($propid, $current_fav)) {
        $favorite_class = 'isfavorite';
        $favorite_text = esc_html__('favorite', 'wpresidence-core');
    }
    return '<span id="add_favorites" class="' . esc_attr($favorite_class) . '" data-postid="' . esc_attr($propid) . '">' . $favorite_text . '</span>';
}

/**
 * Generate page views HTML
 *
 * @param int $propid Property ID
 * @return string Page views HTML
 */
function wpestate_generate_page_views($propid) {
    $views = intval(get_post_meta($propid, 'wpestate_total_views', true));
    return '<span class="no_views dashboad-tooltip" data-original-title="' . esc_attr__('Number of Page Views', 'wpresidence-core') . '"><i class="fas fa-eye-slash"></i>' . $views . '</span>';
}

/**
 * Generate social share HTML
 *
 * @param string $type Type of social share
 * @param int $propid Property ID
 * @return string Social share HTML
 */
function wpestate_generate_social_share($type, $propid) {
    $title = urlencode(get_the_title($propid));
    $url = urlencode(get_permalink($propid));
    switch ($type) {
        case 'facebook_share':
            return '<a href="http://www.facebook.com/sharer.php?u=' . $url . '&amp;t=' . $title . '" target="_blank" class="share_facebook"><i class="fab fa-facebook-f"></i></a>';
        case 'twiter_share':
            return '<a href="https://twitter.com/intent/tweet?text=' . $title . ' ' . $url . '" class="share_tweet" target="_blank"><i class="fa-brands fa-x-twitter"></i></a>';
        case 'pinterest_share':
            $pinterest = wp_get_attachment_image_src(get_post_thumbnail_id($propid), 'property_full_map');
            $pinterest_url = isset($pinterest[0]) ? esc_url($pinterest[0]) : '';
            return '<a href="http://pinterest.com/pin/create/button/?url=' . $url . '&amp;media=' . $pinterest_url . '&amp;description=' . $title . '" target="_blank" class="share_pinterest"> <i class="fab fa-pinterest-p"></i> </a>';
        case 'whatsapp_share':
            return '<a href="https://api.whatsapp.com/send?text=' . $title . ' ' . $url . '" class="social_email"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>';
        case 'email_share':
            $email_link = 'subject=' . $title . '&body=' . $url;
            return '<a href="mailto:email@email.com?' . trim(esc_html($email_link)) . '" data-action="share email" class="social_email"><i class="far fa-envelope"></i></a>';
    }
}


/**
 * Get custom field value for a property
 *
 * @param int $propid Property ID
 * @param string $element Custom field key
 * @return string Custom field value
 */
function wpestate_get_custom_field_value($propid, $element) {
    $meta_value = get_post_meta($propid, $element, true);

    // Convert meta value to string
    $meta_value = wpestate_array_to_string($meta_value);

    // Apply WPML translation if applicable
    if (!empty($meta_value)) {
        $meta_value = apply_filters('wpml_translate_single_string', $meta_value, 'wpresidence-core', 'wp_estate_property_custom_' . $element);
    }

    return ($meta_value !== esc_html__('Not Available', 'wpresidence-core')) ? $meta_value : '';
}

/**
 * Recursively convert array to string
 *
 * @param mixed $value The value to convert
 * @return string The converted string
 */
function wpestate_array_to_string($value) {
    if (is_array($value)) {
        $string_values = array();
        foreach ($value as $item) {
            $string_values[] = wpestate_array_to_string($item);
        }
        return implode(', ', array_filter($string_values));
    } elseif (is_object($value)) {
        return '[Object]';
    } elseif (is_bool($value)) {
        return $value ? 'true' : 'false';
    } elseif (is_null($value)) {
        return '';
    } else {
        return (string)$value;
    }
}
/**
 * Get formatted second price for a property
 *
 * @param int $propid Property ID
 * @return string Formatted second price
 */
function wpestate_get_second_price($propid) {
    $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
    $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
    $price = floatval(get_post_meta($propid, 'property_second_price', true));
    $th_separator = stripslashes(wpresidence_get_option('wp_estate_prices_th_separator', ''));
    $formatted_price = wpestate_format_number_price($price, $th_separator);
    $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');

    $price_label_before = get_post_meta($propid, 'property_label_before', true);
    if ($price_label_before != '') {
        $price_label_before = '<span class="price_label price_label_before">' . esc_html($price_label_before) . '</span>';
    }

    if (!empty($custom_fields) && isset($_COOKIE['my_custom_curr']) && isset($_COOKIE['my_custom_curr_pos']) && isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos'] != -1) {
        $i = intval($_COOKIE['my_custom_curr_pos']);
        $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
        if ($price != 0) {

            $price = $price * $custom_fields[$i][2];
            $price = wpestate_format_number_price($price, $th_separator);
            $wpestate_currency = $custom_fields[$i][0];

            if ($custom_fields[$i][3] == 'before') {
                $price = $wpestate_currency . $price;
            } else {
                $price = $price . $wpestate_currency;
            }
        } else {
            $price = '';
        }
    } else {
        if ($price != 0) {
            $price = wpestate_format_number_price($price, $th_separator);
            if ($where_currency == 'before') {
                $price = $wpestate_currency . $price;
            } else {
                $price = $price . $wpestate_currency;
            }
        } else {
            $price = '';
        }
    }



    return implode(' ', array_filter([$price_label_before, $price, $price_label], 'strlen'));
}






/**
 * Generate property agent details with inline text
 *
 * This function processes a string containing placeholders for agent details
 * and replaces them with actual agent data in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyAgentDetails
 * @since 1.0.0
 */

if (!function_exists('wpestate_estate_property_design_agent_details_intext_details')) :
/**
 * Process and replace agent detail placeholders with actual data
 *
 * @param array $attributes Shortcode attributes
 * @param string|null $content Shortcode content containing agent detail placeholders
 * @return string Processed HTML string with agent details
 */
function wpestate_estate_property_design_agent_details_intext_details($attributes, $content = null) {
    $return_string = '';
    $css_class = '';
    global $propid;

    // Parse shortcode attributes
    $attributes = shortcode_atts(array(
        'css' => '',
        'is_elementor' => '',
        'content' => '',
    ), $attributes);

    $original_content = $content;
    $original_content_elementor = isset($attributes['content']) ? $attributes['content'] : '';

    // Handle Elementor compatibility
    if (isset($attributes['is_elementor']) && intval($attributes['is_elementor']) == 1) {
        $propid = wpestate_return_property_id_elementor_builder($attributes);
        $original_content = $original_content_elementor;
    }

    $detail = $original_content;

    // Generate CSS class
    if (function_exists('vc_shortcode_custom_css_class')) {
        $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($attributes['css'], ' '), '', $attributes);
    }

    // Get agent ID and author ID
    $agent_id = intval(get_post_meta($propid, 'property_agent', true));
    $author_id = wpsestate_get_author($propid);

    // Define agent details mapping
    $agent_single_details = array(
        'Name' => 'name',
        'Description' => 'description',
        'Main Image' => 'image',
        'Page Link' => 'page_link',
        'Agent Skype' => 'agent_skype',
        'Agent Phone' => 'agent_phone',
        'Agent Mobile' => 'agent_mobile',
        'Agent email' => 'agent_email',
        'Agent position' => 'agent_position',
        'Agent Facebook' => 'agent_facebook',
        'Agent Twitter' => 'agent_twitter',
        'Agent Linkedin' => 'agent_linkedin',
        'Agent Pinterest' => 'agent_pinterest',
        'Agent Instagram' => 'agent_instagram',
        'Agent Website' => 'agent_website',
        'Agent Category' => 'property_category_agent',
        'Agent action category' => 'property_action_category_agent',
        'Agent city category' => 'property_city_agent',
        'Agent Area category' => 'property_area_agent',
        'Agent County/State category' => 'property_county_state_agent'
    );

    // Find all placeholders in the content
    preg_match_all("/\{[^\}]*\}/", $detail, $matches);

    $return_string = '<div class="wpestate_estate_property_design_agent_details_intext_details ' . esc_attr($css_class) . '">';

    // Process each placeholder
    foreach ($matches[0] as $value) {
        $element = trim($value, '{}');
        $replace = wpestate_process_agent_detail_element($element, $agent_id);
        $detail = str_replace($value, $replace, $detail);
    }

    $return_string .= do_shortcode($detail) . '</div>';
    return $return_string;
}
endif;

/**
 * Process individual agent detail element
 *
 * @param string $element Agent detail element to process
 * @param int $agent_id Agent ID
 * @return string Processed agent detail
 */
function wpestate_process_agent_detail_element($element, $agent_id) {
    switch ($element) {
        case 'name':
            return esc_html(get_the_title($agent_id));
        case 'description':
            $page_object = get_post($agent_id);
            return wp_kses_post($page_object->post_content);
        case 'image':
            return wpestate_get_agent_image($agent_id);
        case 'page_link':
            return esc_url(get_the_permalink($agent_id));
        case 'property_category_agent':
        case 'property_action_category_agent':
        case 'property_city_agent':
        case 'property_area_agent':
        case 'property_county_state_agent':
            return get_the_term_list($agent_id, $element, '', ', ', '');
        default:
            return esc_html(get_post_meta($agent_id, $element, true));
    }
}

/**
 * Get agent image HTML
 *
 * @param int $agent_id Agent ID
 * @return string Agent image HTML
 */
function wpestate_get_agent_image($agent_id) {
    $thumb_id = get_post_thumbnail_id($agent_id);
    $preview = wp_get_attachment_image_src($thumb_id, 'property_listings');
    $preview_img = $preview ? $preview[0] : WPESTATE_PLUGIN_DIR_URL . '/img/default_user.png';
    return '<img src="' . esc_url($preview_img) . '" alt="' . esc_attr(get_the_title($agent_id)) . '">';
}