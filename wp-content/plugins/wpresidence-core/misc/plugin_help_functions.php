<?php


/*
 *
 *
 *
 *
 *
 */


function wpestate_safe_rewite()
{
    $rewrites   =   get_option('wp_estate_url_rewrites', true);
    $return     =   array();
    foreach ($rewrites as $key=>$value) {
        $return[$key] = str_replace('/', '', $value);
    }
    return $return;
}


/*
 *
 *
 *
 *
 *
 */

function wpestate_recaptcha_path($secret, $captcha)
{
    return "https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha."&remoteip=".esc_html($_SERVER['REMOTE_ADDR']);
}



/*
 *
 *
 *
 *
 *
 */

if (!function_exists('wpestate_my_order')):
function wpestate_disable_filtering($filter, $function)
{
    remove_filter($filter, $function);
}
endif;



/*
 *
 *
 *
 *
 *
 */

if (!function_exists('wpestate_return_filtered_by_order')):
function wpestate_return_filtered_by_order($args)
{
    add_filter('posts_orderby', 'wpestate_my_order');
    $prop_selection = new WP_Query($args);
    remove_filter('posts_orderby', 'wpestate_my_order');
    return $prop_selection;
}
endif;


/*
 *
 *
 *
 *
 *
 */

if (!function_exists('wpestate_my_order')):
function wpestate_my_order($orderby)
{
    global $wpdb;
    global $table_prefix;
    $orderby = $table_prefix.'postmeta.meta_value DESC, '.$table_prefix.'posts.ID DESC';
    return $orderby;
}

endif;


/*
 *
 *
 *
 *
 *
 */

if (!function_exists('wpestate_title_filter')):
function wpestate_title_filter($where, $wp_query)
{
    global $wpdb;
    global $table_prefix;
    global $wpestate_keyword;

    $wpestate_keyword   =   stripslashes($wpestate_keyword);
    $wpestate_keyword   =   htmlspecialchars_decode($wpestate_keyword, ENT_QUOTES);
    $search_term        =   addslashes($wpdb->esc_like($wpestate_keyword));
    $search_term        =   ' \'%' . $search_term . '%\'';
    $where .= ' AND ' . $wpdb->posts . '.post_title LIKE '.$search_term;

    return $where;
}

endif;



/*
 *
 *
 *
 *
 *
 */

if (!function_exists('wpestate_get_access_token')):
    function wpestate_get_access_token($url, $postdata)
    {
        $clientId       =   esc_html(wpresidence_get_option('wp_estate_paypal_client_id', ''));
        $clientSecret   =   esc_html(wpresidence_get_option('wp_estate_paypal_client_secret', ''));

        $access_token='';
        $args=array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'sslverify' => false,
                'blocking' => true,
                'body' =>  'grant_type=client_credentials',
                'headers' => [
                      'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
                      'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
                ],
        );



        $response = wp_remote_post($url, $args);


        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            die($error_message);
        } else {
            $body = wp_remote_retrieve_body($response);
            $body = json_decode($body, true);
            $access_token = $body['access_token'];
        }

        return $access_token;
    }
endif; // end   wpestate_get_access_token




/*
 *
 *
 *
 *
 *
 */

add_action('wp_ajax_wpresidence_generate_pins_redux', 'wpresidence_generate_pins_redux');
function wpresidence_generate_pins_redux()
{
    check_ajax_referer('wpestate_residence_generate_pins', 'security');
    wpestate_listing_pins_for_file();
}




/**
 * Generate pin icon fields for property map markers
 * 
 * This function creates pin icon fields for property map markers based on property
 * action categories and property categories. It uses cached taxonomy terms for improved performance.
 * 
 * @param array $pin_fields The existing pin fields array
 * @return array Updated pin fields array with all property category and action combinations
 */
function wpresidence_core_add_pins_icons($pin_fields) {
    // Get action categories using caching function
    $taxonomy = 'property_action_category';
    $tax_terms = wpestate_get_cached_terms($taxonomy);
    
    // Get property categories using caching function
    $taxonomy_cat = 'property_category';
    $categories = wpestate_get_cached_terms($taxonomy_cat);
    
    // Process action categories
    if (is_array($tax_terms)) {
        foreach ($tax_terms as $tax_term) {
            $post_name = sanitize_key(wpestate_limit54($tax_term->slug));
            $name = 'wp_estate_' . $post_name;
            $pin_fields[] = array(
                'id'       => $name,
                'type'     => 'media',
                'required' => array('wp_estate_use_single_image_pin', '!=', 'yes'),
                'title'    => esc_html__('For action ', 'wpresidence-core') . '<strong>' . $tax_term->name,
                'subtitle' => esc_html__('Image size must be 44px x 50px. ', 'wpresidence-core'),
                'default'  => 'no',
            );
        }
    }
    
    // Process property categories
    if (is_array($categories)) {
        foreach ($categories as $categ) {
            $post_name = sanitize_key(wpestate_limit54($categ->slug));
            $name = 'wp_estate_' . $post_name;
            $pin_fields[] = array(
                'id'       => $name,
                'type'     => 'media',
                'required' => array('wp_estate_use_single_image_pin', '!=', 'yes'),
                'title'    => esc_html__('For category ', 'wpresidence-core') . '<strong>' . $categ->name,
                'subtitle' => esc_html__('Image size must be 44px x 50px. ', 'wpresidence-core'),
                'default'  => 'no',
            );
        }
    }
    
    // Process combinations of action categories and property categories
    if (is_array($tax_terms) && is_array($categories)) {
        foreach ($tax_terms as $tax_term) {
            foreach ($categories as $categ) {
                // Generate a limited-length slug for the combination
                $limit54 = sanitize_key(wpestate_limit27($categ->slug)) . sanitize_key(wpestate_limit27($tax_term->slug));
                $name = 'wp_estate_' . $limit54;
                $pin_fields[] = array(
                    'id'       => $name,
                    'type'     => 'media',
                    'required' => array('wp_estate_use_single_image_pin', '!=', 'yes'),
                    'title'    => __('For action', 'wpresidence-core') . ' <strong>' . $tax_term->name . '</strong>, ' . __('category', 'wpresidence-core') . ': <strong>' . $categ->name . '</strong>',
                    'subtitle' => esc_html__('Image size must be 44px x 50px. ', 'wpresidence-core'),
                    'default'  => 'no',
                );
            }
        }
    }
    
    // Add static pin fields for user location and IDX properties
    $pin_fields[] = array(
        'id'       => 'wp_estate_userpin',
        'type'     => 'media',
        'title'    => esc_html__('Userpin in geolocation', 'wpresidence-core') . '<strong>',
        'subtitle' => esc_html__('Image size must be 44px x 50px. ', 'wpresidence-core'),
        'default'  => 'no',
    );
    
    $pin_fields[] = array(
        'id'       => 'wp_estate_idxpin',
        'type'     => 'media',
        'title'    => esc_html__('For IDX (if plugin is enabled)', 'wpresidence-core') . '<strong>',
        'subtitle' => esc_html__('For IDX (if plugin is enabled)', 'wpresidence-core'),
        'default'  => 'no',
    );
    
    return $pin_fields;
}


/*
 * Return user type list
 *
 *
 *
 *
 *
 */
function wpestate_user_types_list_array(){
 
    if (class_exists('sitepress')) {
        $user_type = array(
           
            esc_html__('User', 'admin_texts_wpresidence_admin'),
            esc_html__('Single Agent', 'admin_texts_wpresidence_admin'),
            esc_html__('Agency', 'admin_texts_wpresidence_admin'),
            esc_html__('Developer', 'admin_texts_wpresidence_admin'),
        );
    } else {
        $user_type = array(
   
            esc_html__('User', 'wpresidence-core'),
            esc_html__('Single Agent', 'wpresidence-core'),
            esc_html__('Agency', 'wpresidence-core'),
            esc_html__('Developer', 'wpresidence-core'),
        );
    }
  return   $user_type;

}
/*
 *
 * Return user list array (key adn value ) for redux dropdown
 *
 *
 *
 *
 */
 function wpestate_user_list_array_redux()
 {
     $user_type = wpestate_user_types_list_array();
     $user_type_redux = array_combine($user_type, $user_type);
   
     return $user_type_redux;
 }
/*
 *
 * Return country list array (key adn value ) for redux dropdown
 *
 *
 *
 *
 */


function wpestate_country_list_redux()
{
    $countries = wpestate_country_list_array();
    $countries_redux = array_combine($countries, $countries);
    return $countries_redux;
}




/*
 * Return country list
 *
 *
 *
 *
 *
 */

function wpestate_country_list_array()
{
    $countries = array(
    esc_html__("Afghanistan", "wpresidence-core"),
    esc_html__("Albania", "wpresidence-core"),
    esc_html__("Algeria", "wpresidence-core"),
    esc_html__("American Samoa", "wpresidence-core"),
    esc_html__("Andorra", "wpresidence-core"),
    esc_html__("Angola", "wpresidence-core"),
    esc_html__("Anguilla", "wpresidence-core"),
    esc_html__("Antarctica", "wpresidence-core"),
    esc_html__("Antigua and Barbuda", "wpresidence-core"),
    esc_html__("Argentina", "wpresidence-core"),
    esc_html__("Armenia", "wpresidence-core"),
    esc_html__("Aruba", "wpresidence-core"),
    esc_html__("Australia", "wpresidence-core"),
    esc_html__("Austria", "wpresidence-core"),
    esc_html__("Azerbaijan", "wpresidence-core"),
    esc_html__("Bahamas", "wpresidence-core"),
    esc_html__("Bahrain", "wpresidence-core"),
    esc_html__("Bangladesh", "wpresidence-core"),
    esc_html__("Barbados", "wpresidence-core"),
    esc_html__("Belarus", "wpresidence-core"),
    esc_html__("Belgium", "wpresidence-core"),
    esc_html__("Belize", "wpresidence-core"),
    esc_html__("Benin", "wpresidence-core"),
    esc_html__("Bermuda", "wpresidence-core"),
    esc_html__("Bhutan", "wpresidence-core"),
    esc_html__("Bolivia", "wpresidence-core"),
    esc_html__("Bosnia and Herzegowina", "wpresidence-core"),
    esc_html__("Botswana", "wpresidence-core"),
    esc_html__("Bouvet Island", "wpresidence-core"),
    esc_html__("Brazil", "wpresidence-core"),
    esc_html__("British Indian Ocean Territory", "wpresidence-core"),
    esc_html__("Brunei Darussalam", "wpresidence-core"),
    esc_html__("Bulgaria", "wpresidence-core"),
    esc_html__("Burkina Faso", "wpresidence-core"),
    esc_html__("Burundi", "wpresidence-core"),
    esc_html__("Cambodia", "wpresidence-core"),
    esc_html__("Cameroon", "wpresidence-core"),
    esc_html__("Canada", "wpresidence-core"),
    esc_html__("Cape Verde", "wpresidence-core"),
    esc_html__("Cayman Islands", "wpresidence-core"),
    esc_html__("Central African Republic", "wpresidence-core"),
    esc_html__("Chad", "wpresidence-core"),
    esc_html__("Chile", "wpresidence-core"),
    esc_html__("China", "wpresidence-core"),
    esc_html__("Christmas Island", "wpresidence-core"),
    esc_html__("Cocos (Keeling) Islands", "wpresidence-core"),
    esc_html__("Colombia", "wpresidence-core"),
    esc_html__("Comoros", "wpresidence-core"),
    esc_html__("Congo", "wpresidence-core"),
    esc_html__("Congo, the Democratic Republic of the", "wpresidence-core"),
    esc_html__("Cook Islands", "wpresidence-core"),
    esc_html__("Costa Rica", "wpresidence-core"),
    esc_html__("Cote d'Ivoire", "wpresidence-core"),
    esc_html__("Croatia (Hrvatska)", "wpresidence-core"),
    esc_html__("Cuba", "wpresidence-core"),
    esc_html__('Curacao', 'wpresidence-core'),
    esc_html__("Cyprus", "wpresidence-core"),
    esc_html__("Czech Republic", "wpresidence-core"),
    esc_html__("Denmark", "wpresidence-core"),
    esc_html__("Djibouti", "wpresidence-core"),
    esc_html__("Dominica", "wpresidence-core"),
    esc_html__("Dominican Republic", "wpresidence-core"),
    esc_html__("East Timor", "wpresidence-core"),
    esc_html__("Ecuador", "wpresidence-core"),
    esc_html__("Egypt", "wpresidence-core"),
    esc_html__("El Salvador", "wpresidence-core"),
    esc_html__("Equatorial Guinea", "wpresidence-core"),
    esc_html__("Eritrea", "wpresidence-core"),
    esc_html__("Estonia", "wpresidence-core"),
    esc_html__("Ethiopia", "wpresidence-core"),
    esc_html__("Falkland Islands (Malvinas)", "wpresidence-core"),
    esc_html__("Faroe Islands", "wpresidence-core"),
    esc_html__("Fiji", "wpresidence-core"),
    esc_html__("Finland", "wpresidence-core"),
    esc_html__("France", "wpresidence-core"),
    esc_html__("France Metropolitan", "wpresidence-core"),
    esc_html__("French Guiana", "wpresidence-core"),
    esc_html__("French Polynesia", "wpresidence-core"),
    esc_html__("French Southern Territories", "wpresidence-core"),
    esc_html__("Gabon", "wpresidence-core"),
    esc_html__("Gambia", "wpresidence-core"),
    esc_html__("Georgia", "wpresidence-core"),
    esc_html__("Germany", "wpresidence-core"),
    esc_html__("Ghana", "wpresidence-core"),
    esc_html__("Gibraltar", "wpresidence-core"),
    esc_html__("Greece", "wpresidence-core"),
    esc_html__("Greenland", "wpresidence-core"),
    esc_html__("Grenada", "wpresidence-core"),
    esc_html__("Guadeloupe", "wpresidence-core"),
    esc_html__("Guam", "wpresidence-core"),
    esc_html__("Guatemala", "wpresidence-core"),
    esc_html__("Guinea", "wpresidence-core"),
    esc_html__("Guinea-Bissau", "wpresidence-core"),
    esc_html__("Guyana", "wpresidence-core"),
    esc_html__("Haiti", "wpresidence-core"),
    esc_html__("Heard and Mc Donald Islands", "wpresidence-core"),
    esc_html__("Holy See (Vatican City State)", "wpresidence-core"),
    esc_html__("Honduras", "wpresidence-core"),
    esc_html__("Hong Kong", "wpresidence-core"),
    esc_html__("Hungary", "wpresidence-core"),
    esc_html__("Iceland", "wpresidence-core"),
    esc_html__("India", "wpresidence-core"),
    esc_html__("Indonesia", "wpresidence-core"),
    esc_html__("Iran (Islamic Republic of)", "wpresidence-core"),
    esc_html__("Iraq", "wpresidence-core"),
    esc_html__("Ireland", "wpresidence-core"),
    esc_html__("Israel", "wpresidence-core"),
    esc_html__("Italy", "wpresidence-core"),
    esc_html__("Jamaica", "wpresidence-core"),
    esc_html__("Japan", "wpresidence-core"),
    esc_html__("Jordan", "wpresidence-core"),
    esc_html__("Kazakhstan", "wpresidence-core"),
    esc_html__("Kenya", "wpresidence-core"),
    esc_html__("Kiribati", "wpresidence-core"),
    __("Korea, Democratic People's Republic of", "wpresidence-core"),
    esc_html__("Korea, Republic of", "wpresidence-core"),
    esc_html__("Kosovo", "wpresidence-core"),
    esc_html__("Kuwait", "wpresidence-core"),
    esc_html__("Kyrgyzstan", "wpresidence-core"),
    __("Lao, People's Democratic Republic", "wpresidence-core"),
    esc_html__("Latvia", "wpresidence-core"),
    esc_html__("Lebanon", "wpresidence-core"),
    esc_html__("Lesotho", "wpresidence-core"),
    esc_html__("Liberia", "wpresidence-core"),
    esc_html__("Libyan Arab Jamahiriya", "wpresidence-core"),
    esc_html__("Liechtenstein", "wpresidence-core"),
    esc_html__("Lithuania", "wpresidence-core"),
    esc_html__("Luxembourg", "wpresidence-core"),
    esc_html__("Macau", "wpresidence-core"),
    esc_html__("Macedonia (FYROM)", "wpresidence-core"),
    esc_html__("Madagascar", "wpresidence-core"),
    esc_html__("Malawi", "wpresidence-core"),
    esc_html__("Malaysia", "wpresidence-core"),
    esc_html__("Maldives", "wpresidence-core"),
    esc_html__("Mali", "wpresidence-core"),
    esc_html__("Malta", "wpresidence-core"),
    esc_html__("Marshall Islands", "wpresidence-core"),
    esc_html__("Martinique", "wpresidence-core"),
    esc_html__("Mauritania", "wpresidence-core"),
    esc_html__("Mauritius", "wpresidence-core"),
    esc_html__("Mayotte", "wpresidence-core"),
    esc_html__("Mexico", "wpresidence-core"),
    esc_html__("Micronesia, Federated States of", "wpresidence-core"),
    esc_html__("Moldova, Republic of", "wpresidence-core"),
    esc_html__("Monaco", "wpresidence-core"),
    esc_html__("Mongolia", "wpresidence-core"),
    esc_html__("Montserrat", "wpresidence-core"),
    esc_html__("Morocco", "wpresidence-core"),
    esc_html__("Mozambique", "wpresidence-core"),
    esc_html__("Montenegro", "wpresidence-core"),
    esc_html__("Myanmar", "wpresidence-core"),
    esc_html__("Namibia", "wpresidence-core"),
    esc_html__("Nauru", "wpresidence-core"),
    esc_html__("Nepal", "wpresidence-core"),
    esc_html__("Netherlands", "wpresidence-core"),
    esc_html__("Netherlands Antilles", "wpresidence-core"),
    esc_html__("New Caledonia", "wpresidence-core"),
    esc_html__("New Zealand", "wpresidence-core"),
    esc_html__("Nicaragua", "wpresidence-core"),
    esc_html__("Niger", "wpresidence-core"),
    esc_html__("Nigeria", "wpresidence-core"),
    esc_html__("Niue", "wpresidence-core"),
    esc_html__("Norfolk Island", "wpresidence-core"),
    esc_html__("Northern Mariana Islands", "wpresidence-core"),
    esc_html__("Norway", "wpresidence-core"),
    esc_html__("Oman", "wpresidence-core"),
    esc_html__("Pakistan", "wpresidence-core"),
    esc_html__("Palau", "wpresidence-core"),
    esc_html__("Panama", "wpresidence-core"),
    esc_html__("Papua New Guinea", "wpresidence-core"),
    esc_html__("Paraguay", "wpresidence-core"),
    esc_html__("Peru", "wpresidence-core"),
    esc_html__("Philippines", "wpresidence-core"),
    esc_html__("Pitcairn", "wpresidence-core"),
    esc_html__("Poland", "wpresidence-core"),
    esc_html__("Portugal", "wpresidence-core"),
    esc_html__("Puerto Rico", "wpresidence-core"),
    esc_html__("Qatar", "wpresidence-core"),
    esc_html__("Reunion", "wpresidence-core"),
    esc_html__("Romania", "wpresidence-core"),
    esc_html__("Russian Federation", "wpresidence-core"),
    esc_html__("Rwanda", "wpresidence-core"),
    esc_html__("Saint Kitts and Nevis", "wpresidence-core"),
    esc_html__("Saint Martin", "wpresidence-core"),
    esc_html__("Saint Lucia", "wpresidence-core"),
    esc_html__("Saint Helena","wpresidence-core"),
    esc_html__("Saint Vincent and the Grenadines", "wpresidence-core"),
    esc_html__("Samoa", "wpresidence-core"),
    esc_html__("San Marino", "wpresidence-core"),
    esc_html__("Sao Tome and Principe", "wpresidence-core"),
    esc_html__("Saudi Arabia", "wpresidence-core"),
    esc_html__("Senegal", "wpresidence-core"),
    esc_html__("Seychelles", "wpresidence-core"),
    esc_html__("Serbia", "wpresidence-core"),
    esc_html__("Sierra Leone", "wpresidence-core"),
    esc_html__("Singapore", "wpresidence-core"),
    esc_html__("Slovakia (Slovak Republic)", "wpresidence-core"),
    esc_html__("Slovenia", "wpresidence-core"),
    esc_html__("Solomon Islands", "wpresidence-core"),esc_html__("Somalia", "wpresidence-core"),esc_html__("South Africa", "wpresidence-core"),esc_html__("South Georgia and the South Sandwich Islands", "wpresidence-core"),esc_html__("Spain", "wpresidence-core"),esc_html__("Sri Lanka", "wpresidence-core"),esc_html__("St. Helena", "wpresidence-core"),esc_html__("St. Pierre and Miquelon", "wpresidence-core"),esc_html__("Sudan", "wpresidence-core"),
    esc_html__("Suriname", "wpresidence-core"),
    esc_html__("Svalbard and Jan Mayen Islands", "wpresidence-core"),
    esc_html__("Swaziland", "wpresidence-core"),
    esc_html__("Sweden", "wpresidence-core"),
    esc_html__("Switzerland", "wpresidence-core"),
    esc_html__("Syrian Arab Republic", "wpresidence-core"),
    esc_html__("Taiwan, Province of China", "wpresidence-core"),
    esc_html__("Tajikistan", "wpresidence-core"),
    esc_html__("Tanzania, United Republic of", "wpresidence-core"),
    esc_html__("Thailand", "wpresidence-core"),
    esc_html__("Togo", "wpresidence-core"),
    esc_html__("Tokelau", "wpresidence-core"),
    esc_html__("Tonga", "wpresidence-core"),
    esc_html__("Trinidad and Tobago", "wpresidence-core"),
    esc_html__("Tunisia", "wpresidence-core"),
    esc_html__("Türkiye", "wpresidence-core"),
    esc_html__("Turkmenistan", "wpresidence-core"),
    esc_html__("Turks and Caicos Islands", "wpresidence-core"),
    esc_html__("Tuvalu", "wpresidence-core"),
    esc_html__("Uganda", "wpresidence-core"),
    esc_html__("Ukraine", "wpresidence-core"),
    esc_html__("United Arab Emirates", "wpresidence-core"),
    esc_html__("United Kingdom", "wpresidence-core"),
    esc_html__("United States", "wpresidence-core"),
    esc_html__("United States Minor Outlying Islands", "wpresidence-core"),
    esc_html__("Uruguay", "wpresidence-core"),
    esc_html__("Uzbekistan", "wpresidence-core"),
    esc_html__("Vanuatu", "wpresidence-core"),
    esc_html__("Venezuela", "wpresidence-core"),
    esc_html__("Vietnam", "wpresidence-core"),
    esc_html__("Virgin Islands (British)", "wpresidence-core"),
    esc_html__("Virgin Islands (U.S.)", "wpresidence-core"),
    esc_html__("Wallis and Futuna Islands", "wpresidence-core"),
    esc_html__("Western Sahara", "wpresidence-core"),
    esc_html__("Yemen", "wpresidence-core"),
    esc_html__("Zambia", "wpresidence-core"),
    esc_html__("Zimbabwe", "wpresidence-core"));


    return $countries;
}


/*
 *
 *
 *
 *
 *
 *
 */


function wpestate_country_list_code(){
    $countries = array(
    'US' => 'United States',
    'CA' => 'Canada',
    'AU' => 'Australia',
    'FR' => 'France',
    'DE' => 'Germany',
    'IS' => 'Iceland',
    'IE' => 'Ireland',
    'IT' => 'Italy',
    'ES' => 'Spain',
    'SE' => 'Sweden',
    'AT' => 'Austria',
    'BE' => 'Belgium',
    'FI' => 'Finland',
    'CZ' => 'Czech Republic',
    'DK' => 'Denmark',
    'NO' => 'Norway',
    'GB' => 'United Kingdom',
    'CH' => 'Switzerland',
    'NZ' => 'New Zealand',
    'RU' => 'Russian Federation',
    'PT' => 'Portugal',
    'NL' => 'Netherlands',
    'IM' => 'Isle of Man',
    'AF' => 'Afghanistan',
    'AX' => 'Aland Islands ',
    'AL' => 'Albania',
    'DZ' => 'Algeria',
    'AS' => 'American Samoa',
    'AD' => 'Andorra',
    'AO' => 'Angola',
    'AI' => 'Anguilla',
    'AQ' => 'Antarctica',
    'AG' => 'Antigua and Barbuda',
    'AR' => 'Argentina',
    'AM' => 'Armenia',
    'AW' => 'Aruba',
    'AZ' => 'Azerbaijan',
    'BS' => 'Bahamas',
    'BH' => 'Bahrain',
    'BD' => 'Bangladesh',
    'BB' => 'Barbados',
    'BY' => 'Belarus',
    'BZ' => 'Belize',
    'BJ' => 'Benin',
    'BM' => 'Bermuda',
    'BT' => 'Bhutan',
    'BO' => 'Bolivia, Plurinational State of',
    'BQ' => 'Bonaire, Sint Eustatius and Saba',
    'BA' => 'Bosnia and Herzegovina',
    'BW' => 'Botswana',
    'BV' => 'Bouvet Island',
    'BR' => 'Brazil',
    'IO' => 'British Indian Ocean Territory',
    'BN' => 'Brunei Darussalam',
    'BG' => 'Bulgaria',
    'BF' => 'Burkina Faso',
    'BI' => 'Burundi',
    'KH' => 'Cambodia',
    'CM' => 'Cameroon',
    'CV' => 'Cape Verde',
    'KY' => 'Cayman Islands',
    'CF' => 'Central African Republic',
    'TD' => 'Chad',
    'CL' => 'Chile',
    'CN' => 'China',
    'CX' => 'Christmas Island',
    'CC' => 'Cocos (Keeling) Islands',
    'CO' => 'Colombia',
    'KM' => 'Comoros',
    'CG' => 'Congo',
    'CD' => 'Congo, the Democratic Republic of the',
    'CK' => 'Cook Islands',
    'CR' => 'Costa Rica',
    'CI' => 'Cote d\'Ivoire',
    'HR' => 'Croatia',
    'CU' => 'Cuba',
    'CW' => 'Curaçao',
    'CY' => 'Cyprus',
    'DJ' => 'Djibouti',
    'DM' => 'Dominica',
    'DO' => 'Dominican Republic',
    'EC' => 'Ecuador',
    'EG' => 'Egypt',
    'SV' => 'El Salvador',
    'GQ' => 'Equatorial Guinea',
    'ER' => 'Eritrea',
    'EE' => 'Estonia',
    'ET' => 'Ethiopia',
    'FK' => 'Falkland Islands (Malvinas)',
    'FO' => 'Faroe Islands',
    'FJ' => 'Fiji',
    'GF' => 'French Guiana',
    'PF' => 'French Polynesia',
    'TF' => 'French Southern Territories',
    'GA' => 'Gabon',
    'GM' => 'Gambia',
    'GE' => 'Georgia',
    'GH' => 'Ghana',
    'GI' => 'Gibraltar',
    'GR' => 'Greece',
    'GL' => 'Greenland',
    'GD' => 'Grenada',
    'GP' => 'Guadeloupe',
    'GU' => 'Guam',
    'GT' => 'Guatemala',
    'GG' => 'Guernsey',
    'GN' => 'Guinea',
    'GW' => 'Guinea-Bissau',
    'GY' => 'Guyana',
    'HT' => 'Haiti',
    'HM' => 'Heard Island and McDonald Islands',
    'VA' => 'Holy See (Vatican City State)',
    'HN' => 'Honduras',
    'HK' => 'Hong Kong',
    'HU' => 'Hungary',
    'IN' => 'India',
    'ID' => 'Indonesia',
    'IR' => 'Iran, Islamic Republic of',
    'IQ' => 'Iraq',
    'IL' => 'Israel',
    'JM' => 'Jamaica',
    'JP' => 'Japan',
    'JE' => 'Jersey',
    'JO' => 'Jordan',
    'KZ' => 'Kazakhstan',
    'KE' => 'Kenya',
    'KI' => 'Kiribati',
    'KP' => 'Korea, Democratic People\'s Republic of',
    'KR' => 'Korea, Republic of',
    'KV' => 'Kosovo',
    'KW' => 'Kuwait',
    'KG' => 'Kyrgyzstan',
    'LA' => 'Lao People\'s Democratic Republic',
    'LV' => 'Latvia',
    'LB' => 'Lebanon',
    'LS' => 'Lesotho',
    'LR' => 'Liberia',
    'LY' => 'Libyan Arab Jamahiriya',
    'LI' => 'Liechtenstein',
    'LT' => 'Lithuania',
    'LU' => 'Luxembourg',
    'MO' => 'Macao',
    'MK' => 'Macedonia',
    'MG' => 'Madagascar',
    'MW' => 'Malawi',
    'MY' => 'Malaysia',
    'MV' => 'Maldives',
    'ML' => 'Mali',
    'MT' => 'Malta',
    'MH' => 'Marshall Islands',
    'MQ' => 'Martinique',
    'MR' => 'Mauritania',
    'MU' => 'Mauritius',
    'YT' => 'Mayotte',
    'MX' => 'Mexico',
    'FM' => 'Micronesia, Federated States of',
    'MD' => 'Moldova, Republic of',
    'MC' => 'Monaco',
    'MN' => 'Mongolia',
    'ME' => 'Montenegro',
    'MS' => 'Montserrat',
    'MA' => 'Morocco',
    'MZ' => 'Mozambique',
    'MM' => 'Myanmar',
    'NA' => 'Namibia',
    'NR' => 'Nauru',
    'NP' => 'Nepal',
    'NC' => 'New Caledonia',
    'NI' => 'Nicaragua',
    'NE' => 'Niger',
    'NG' => 'Nigeria',
    'NU' => 'Niue',
    'NF' => 'Norfolk Island',
    'MP' => 'Northern Mariana Islands',
    'OM' => 'Oman',
    'PK' => 'Pakistan',
    'PW' => 'Palau',
    'PS' => 'Palestinian Territory, Occupied',
    'PA' => 'Panama',
    'PG' => 'Papua New Guinea',
    'PY' => 'Paraguay',
    'PE' => 'Peru',
    'PH' => 'Philippines',
    'PN' => 'Pitcairn',
    'PL' => 'Poland',
    'PR' => 'Puerto Rico',
    'QA' => 'Qatar',
    'RE' => 'Reunion',
    'RO' => 'Romania',
    'RW' => 'Rwanda',
    'BL' => 'Saint Barthélemy',
    'SH' => 'Saint Helena',
    'KN' => 'Saint Kitts and Nevis',
    'LC' => 'Saint Lucia',
    'MF' => 'Saint Martin (French part)',
    'PM' => 'Saint Pierre and Miquelon',
    'VC' => 'Saint Vincent and the Grenadines',
    'WS' => 'Samoa',
    'SM' => 'San Marino',
    'ST' => 'Sao Tome and Principe',
    'SA' => 'Saudi Arabia',
    'SN' => 'Senegal',
    'RS' => 'Serbia',
    'SC' => 'Seychelles',
    'SL' => 'Sierra Leone',
    'SG' => 'Singapore',
    'SX' => 'Sint Maarten (Dutch part)',
    'SK' => 'Slovakia',
    'SI' => 'Slovenia',
    'SB' => 'Solomon Islands',
    'SO' => 'Somalia',
    'ZA' => 'South Africa',
    'GS' => 'South Georgia and the South Sandwich Islands',
    'LK' => 'Sri Lanka',
    'SD' => 'Sudan',
    'SR' => 'Suriname',
    'SJ' => 'Svalbard and Jan Mayen',
    'SZ' => 'Swaziland',
    'SY' => 'Syrian Arab Republic',
    'TW' => 'Taiwan, Province of China',
    'TJ' => 'Tajikistan',
    'TZ' => 'Tanzania, United Republic of',
    'TH' => 'Thailand',
    'TL' => 'Timor-Leste',
    'TG' => 'Togo',
    'TK' => 'Tokelau',
    'TO' => 'Tonga',
    'TT' => 'Trinidad and Tobago',
    'TN' => 'Tunisia',
    'TR' => 'Turkey',
    'TM' => 'Turkmenistan',
    'TC' => 'Turks and Caicos Islands',
    'TV' => 'Tuvalu',
    'UG' => 'Uganda',
    'UA' => 'Ukraine',
    'AE' => 'United Arab Emirates',
    'UM' => 'United States Minor Outlying Islands',
    'UY' => 'Uruguay',
    'UZ' => 'Uzbekistan',
    'VU' => 'Vanuatu',
    'VE' => 'Venezuela, Bolivarian Republic of',
    'VN' => 'Viet Nam',
    'VG' => 'Virgin Islands, British',
    'VI' => 'Virgin Islands, U.S.',
    'WF' => 'Wallis and Futuna',
    'EH' => 'Western Sahara',
    'YE' => 'Yemen',
    'ZM' => 'Zambia',
    'ZW' => 'Zimbabwe'
);

return $countries;
}

/*
 *
 *
 *
 *
 *
 *
 */

function wpresidence_return_theme_slider_list()
{
    $return_array=array();
    wp_reset_postdata();
    wp_reset_query();


    $args = array(      'post_type'         =>  'estate_property',
                    'post_status'       =>  'publish',
                    'paged'             =>  0,
                    'posts_per_page'    =>  50,
                    'cache_results'           =>    false,
                    'update_post_meta_cache'  =>    false,
                    'update_post_term_cache'  =>    false,
                    'fields'                    => 'ids',

    );

    $recent_posts = new WP_Query($args);
    while ($recent_posts->have_posts()):
        $recent_posts->the_post();
    $theid            =   get_the_ID();
    $return_array[$theid]   =   get_the_title();
    endwhile;
    wp_reset_postdata();
    return   $return_array;
}



/*
 *
 *
 *
 *
 *
 *
 */


if (! function_exists('wpestate_admin_bar_menu')) {
    function wpestate_admin_bar_menu()
    {
        global $wp_admin_bar;
        $theme_data = wp_get_theme();

        if (! current_user_can('manage_options') || ! is_admin_bar_showing()) {
            return;
        }
        $wp_admin_bar->add_menu(
            array(
                            'title' => esc_html__('Clear WpResidence Cache', 'wpresidence-core'),
                            'id' => 'clear_cache',
                            'href' => wp_nonce_url(esc_url(admin_url('admin-post.php?action=wpestate_purge_cache')), 'theme_purge_cache'),
                    )
        );
    }
}
add_action('admin_bar_menu', 'wpestate_admin_bar_menu', 100);


/*
 * Print page function
 *
 *
 *
 *
 *
 */

add_action('wp_ajax_nopriv_wpestate_ajax_create_print', 'wpestate_ajax_create_print');
add_action('wp_ajax_wpestate_ajax_create_print', 'wpestate_ajax_create_print');

if ( isset( $_GET['me'] ) )   {
    add_action( 'template_redirect', 'wpestate_ajax_create_print' );
}

if (!function_exists('wpestate_ajax_create_print')):
  function wpestate_ajax_create_print()
  {
    //   check_ajax_referer('wpestate_ajax_filtering', 'security');
      if (!isset($_REQUEST['propid'])|| !is_numeric($_REQUEST['propid'])) {
          exit('out pls1');
      }

      $post_id            = intval($_REQUEST['propid']);

      $post= get_post($post_id);
      if ($post->post_type!='estate_property' || $post->post_status!='publish') {
          exit('out pls2');
      }

      $property_description_text  =   esc_html(wpresidence_get_option('wp_estate_property_description_text'));
      $property_details_text      =   esc_html(wpresidence_get_option('wp_estate_property_details_text'));
      $property_features_text     =   esc_html(wpresidence_get_option('wp_estate_property_features_text'));
      $property_adr_text          =   stripslashes(esc_html(wpresidence_get_option('wp_estate_property_adr_text')));
      $print_show_images          =   wpresidence_get_option('wp_estate_print_show_images', '');
      $print_show_floor_plans     =   wpresidence_get_option('wp_estate_print_show_floor_plans', '');
      $print_show_features        =   wpresidence_get_option('wp_estate_print_show_features', '');
      $print_show_details         =   wpresidence_get_option('wp_estate_print_show_details', '');
      $print_show_adress          =   wpresidence_get_option('wp_estate_print_show_adress', '');
      $print_show_description     =   wpresidence_get_option('wp_estate_print_show_description', '');
      $print_show_agent          =    wpresidence_get_option('wp_estate_print_show_agent', '');
      $print_show_subunits        =   wpresidence_get_option('wp_estate_print_show_subunits', '');

      $unit               = esc_html(wpresidence_get_option('wp_estate_measure_sys', ''));
      $wpestate_currency  = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
      $where_currency     = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
      $property_address   = esc_html(get_post_meta($post_id, 'property_address', true));
      $property_city      = strip_tags(get_the_term_list($post_id, 'property_city', '', ', ', ''));
      $property_area      = strip_tags(get_the_term_list($post_id, 'property_area', '', ', ', ''));
      $property_county    = esc_html(get_post_meta($post_id, 'property_county', true));
      $property_zip       = esc_html(get_post_meta($post_id, 'property_zip', true));
      $property_country   = esc_html(get_post_meta($post_id, 'property_country', true));
      $ref_code           = get_post_meta($post_id, 'reference_code', true);

      $property_size                  = wpestate_get_converted_measure($post_id, 'property_size');
      $property_bedrooms              = floatval(get_post_meta($post_id, 'property_bedrooms', true));
      $property_bathrooms             = floatval(get_post_meta($post_id, 'property_bathrooms', true));
      $property_year                  = floatval(get_post_meta($post_id, 'property_year', true));


      $image_id           = get_post_thumbnail_id($post_id);
      $full_img           = wp_get_attachment_image_src($image_id, 'full');
      $full_img           = $full_img [0];


      $title              = get_the_title($post_id);
      $content =  wpestate_print_get_custom_page_content($post_id);

      $price              = floatval(get_post_meta($post_id, 'property_price', true));

      if ($price != 0) {
          $price = wpestate_show_price($post_id, $wpestate_currency, $where_currency, 1);
      } else {
          $price='';
      }



    //   /////////////////////////////////////////////////////////////////////////////////////////////////////
    //   // end get agent details
    //   /////////////////////////////////////////////////////////////////////////////////////////////////////

      print  '<html><head><title>'.$title.'</title><link href="'.get_template_directory_uri().'/public/css/main.css" rel="stylesheet" type="text/css" />';
   
      print '<link href="'.get_template_directory_uri().'/public/fontawesome/css/all.min.css" rel="stylesheet" type="text/css" />';
      
      
      if (is_child_theme()) {
          print '<link href="'.get_template_directory_uri().'/style.css" rel="stylesheet" type="text/css" />';
      }

      if (is_rtl()) {
          print '<link href="'.get_stylesheet_directory_uri().'/rtl.css" rel="stylesheet" type="text/css" />';
      }
      print '</head>';
      $protocol = is_ssl() ? 'https' : 'http';
      print  '<body class="print_body row" >';

      $logo=wpresidence_get_option('wp_estate_print_logo_image', 'url');
      if ($logo!='') {
          print '<img src="'.$logo.'" class="img-responsive printlogo" alt="logo"/>';
      } else {
          print '<img class="img-responsive printlogo" src="'. get_theme_file_uri('/img/logo.png').'" alt="logo"/>';
      }

      print '<h1 class="print_title">'.wp_kses_post($title).'</h1>';
      print '<div class="print-price">'.esc_html__('Price', 'wpresidence-core').': '.$price.'</div>';
      print '<div class="print-addr">'. $property_address. ', ' . $property_city.', '.$property_area.'</div>';
      print '<div class="print-col-img"><img src="'.$full_img.'">';
  //    print '<img class="print_qrcode" src="https://chart.googleapis.com/chart?cht=qr&chs=110x110&chl='. urlencode(esc_url(get_permalink($post_id))) .'&choe=UTF-8" title="'.urlencode($title).'" />';
      print '<img class="print_qrcode" src="https://qrcode.tec-it.com/API/QRCode?size=small&dpi=110&data='. urlencode(esc_url(get_permalink($post_id))).'" />';
      print'</div>';

      $layout = wpresidence_get_option('wp_estate_property_page_print_order');

      foreach ($layout['enabled'] as $key => $label) :
          switch( $key ) {
            case 'overview':
                wpestate_property_overview_print( $post_id );
                break;
            case 'images':
                wpestate_property_print_wrapper( esc_html__('Property Images', 'wpresidence-core'), wpestate_print_listing_images($post_id) );
                break;
            case 'description':
                wpestate_property_print_wrapper( esc_html__('Property Description', 'wpresidence-core'), $content );
                break;
            case 'energy-savings':
                wpestate_property_print_wrapper( esc_html__('Energy Savings', 'wpresidence-core'), wpestate_energy_save_features( $post_id ) );
                break;
            case 'multi-units':
                wpestate_print_multi_units( $post_id );
                break;
            case 'address':
                wpestate_property_print_wrapper( $property_adr_text, estate_listing_address_printing($post_id) );
                break;
            case 'listing_details':
                wpestate_property_print_wrapper( $property_details_text, estate_listing_details($post_id, get_post_custom($post_id)) );
                break;
            case 'features':
                wpestate_property_print_wrapper( $property_features_text, estate_listing_features($post_id, 3, 1) );
                break;
            case 'map':
                wpestate_property_print_wrapper( esc_html__('Property Map', 'wpresidence-core'), wpestate_print_display_map($post_id) );
                break;
            case 'walkscore':
                // wpestate_property_print_wrapper( esc_html__('Walk Score', 'wpresidence-core'), estate_listing_walkscore($post_id) );
                break;
            case 'nearby':
                // wpestate_property_print_wrapper( esc_html__('Nearby Places', 'wpresidence-core'), estate_listing_nearby_places($post_id) );
                break;
            case 'floor_plans':
                wpestate_property_print_wrapper( esc_html__('Floor Plans', 'wpresidence-core'), wpestate_print_floor_plans($post_id) );
                break;
            case 'page_views':
                // wpestate_property_print_wrapper( esc_html__('Page Views', 'wpresidence-core'), estate_listing_page_views($post_id) );
                break;
            case 'agent_area':
                wpestate_property_print_wrapper( esc_html__('Agent', 'wpresidence-core'), wpestate_listing_agent($post_id) );
                break;
            case 'reviews':
                wpestate_property_print_wrapper( esc_html__('Reviews', 'wpresidence-core'), wpestate_print_listing_reviews($post_id) );
                break;
            case 'other_agents':
                wpestate_property_print_wrapper( esc_html__('Other Agents', 'wpresidence-core'), wpestate_print_listing_other_agents($post_id) );
                break;
          }
      endforeach;

      

      

      

/*
      if ($print_show_subunits == 'yes') {
          global $property_subunits_master;
          $has_multi_units=intval(get_post_meta($post_id, 'property_has_subunits', true));
          $property_subunits_master=intval(get_post_meta($post_id, 'property_subunits_master', true));

          print '<div class="print_property_subunits_wrapper">';
          if ($has_multi_units==1) {
              print '<div class="print_header"><h2>'.esc_html__('Available Units', 'wpresidence-core').'</h2></div>';
              print '<div class="print-content">';
              wpestate_shortcode_multi_units($post_id, $property_subunits_master, 1);
              print '</div>';
          } else {
              if ($property_subunits_master!=0) {
                  print '<div class="print-content">';
                  wpestate_shortcode_multi_units($post_id, $property_subunits_master, 1);
                  print '</div>';
              }
          }
          print '</div>';
      }


      if ($print_show_agent == 'yes') {
          print '<div class="print_header"><h2>'.esc_html__('Agent', 'wpresidence-core').'</h2></div>';
          print '<div class="print-content">';
          if ($preview_img!='') {
              print '<div class="print-col-img agent_print_image"><img src="'.$preview_img.'"></div>';
          }
          print '<div class="print_agent_wrapper">';
          if ($name!='') {
              print '<div class="listing_detail_agent col-md-4 agent_name"><strong>'.esc_html__('Name', 'wpresidence-core').':</strong> '.$name.'</div>';
          }
          if ($agent_phone!='') {
              print '<div class="listing_detail_agent col-md-4"><strong>'.esc_html__('Telephone', 'wpresidence-core').':</strong> '.$agent_phone.'</div>';
          }
          if ($agent_mobile!='') {
              print '<div class="listing_detail_agent col-md-4"><strong>'.esc_html__('Mobile', 'wpresidence-core').':</strong> '.$agent_mobile.'</div>';
          }
          if ($agent_skype!='') {
              print '<div class="listing_detail_agent col-md-4"><strong>'.esc_html__('Skype', 'wpresidence-core').':</strong> '.$agent_skype.'</div>';
          }
          if ($agent_email!='') {
              print '<div class="listing_detail_agent col-md-4"><strong>'.esc_html__('Email', 'wpresidence-core').':</strong> '.$agent_email.'</div>';
          }
          print '</div>';
          print '</div>';
          print '</div>';
          print '<div class="printbreak"></div>';
      }

      if ($print_show_description == 'yes') {
          print '<div class="print_header"><h2>'.esc_html__('Property Description', 'wpresidence-core').'</h2></div><div class="print-content">'.$content.wpestate_energy_save_features($post_id).'</div></div>';
      }

      if ($print_show_adress  == 'yes') {
          print '<div class="print_header"><h2>';
          if ($property_adr_text!='') {
              echo esc_html($property_adr_text);
          } else {
              esc_html_e('Property Address', 'wpresidence-core');
          }
          print '</h2></div>';

          print '<div class="print-content">';
          print estate_listing_address_printing($post_id);
          print ' </div>';
      }

      if ($print_show_details == 'yes') {
          print '<div class="print_header"><h2>';
          if ($property_adr_text!='') {
              echo esc_html($property_details_text);
          } else {
              esc_html_e('Property Details', 'wpresidence-core');
          }
          print '</h2></div>';

          print '<div class="print-content">';
          $wpestate_prop_all_details  =   get_post_custom($post_id);
          print estate_listing_details($post_id, $wpestate_prop_all_details);
          print ' </div>';
      }

      if ($print_show_features == 'yes') {
          print '<div class="print_header"><h2>';
          if ($property_adr_text!='') {
              echo esc_html($property_features_text);
          } else {
              esc_html_e('Features and Amenities', 'wpresidence-core');
          }
          print '</h2></div>';
          print '<div class="print-content wpestate-print-listing-features">';
          print estate_listing_features($post_id, 3, 1);
          print ' </div>';
      }

      if ($print_show_floor_plans  == 'yes') {
          print '<div class="print_header"><h2>'.esc_html__('Floor Plans', 'wpresidence-core').'</h2></div>';
          estate_floor_plan($post_id, 1);
          print '<div class="printbreak"></div>';
      }

      if ($print_show_images  == 'yes') {
            print '<div class="print_header"><h2>'.esc_html__('Images', 'wpresidence-core').'</h2></div>';
            foreach ($post_attachments as $attachment) {
                $original       =   wp_get_attachment_image_src($attachment, 'full');
                if(isset($original[0]) ){
                    print '<div class="print-col-img printimg"><img src="'. $original[0].'"></div>';
                }
            }
           
      }
*/
      print '<div class="print_spacer"></div>';
      print '</body></html>';
      die();
  }

endif;



/*
 * Add new profile fields
 *
 *
 *
 *
 *
 */


add_filter('user_contactmethods', 'wpestate_modify_contact_methods');
if (!function_exists('wpestate_modify_contact_methods')):
function wpestate_modify_contact_methods($profile_fields)
{

    // Add new fields
    $profile_fields['facebook']                     = 'Facebook';
    $profile_fields['twitter']                      = 'Twitter';
    $profile_fields['linkedin']                     = 'Linkedin';
    $profile_fields['pinterest']                    = 'Pinterest';
    $profile_fields['instagram']                    = 'Instagram';
    $profile_fields['website']                          = 'Website';
    $profile_fields['phone']                        = 'Phone';
    $profile_fields['mobile']                       = 'Mobile';
    $profile_fields['skype']                        = 'Skype';
    $profile_fields['title']                        = 'Title/Position';
    $profile_fields['custom_picture']               = 'Picture Url';
    $profile_fields['small_custom_picture']         = 'Small Picture Url';
    $profile_fields['package_id']                   = 'Package Id';
    $profile_fields['package_activation']           = 'Package Activation';
    $profile_fields['package_listings']             = 'Listings available';
    $profile_fields['package_featured_listings']    = 'Featured Listings available';
    $profile_fields['profile_id']                   = 'Paypal Recuring Profile';
    $profile_fields['user_agent_id']                = 'User Agent / Agency / Developer ID';
    $profile_fields['stripe']                       = 'Stripe Consumer Profile';
    $profile_fields['stripe_subscription_id']       = 'Stripe Subscription ID';
    $profile_fields['has_stripe_recurring']         = 'Has Stripe Recurring';
    $profile_fields['paypal_agreement']             = esc_html__('Paypal Recuring Profile- rest api', 'wpresidence-core');
    $profile_fields['user_estate_role']             = 'User Role (1, 2 , 3 or 4): 1 = simple user, 2 = agent, 3 = agency, 4 = developer';
    return $profile_fields;
}

endif;

function custom_remove_tag_box() {
    global $current_screen;
    $estate_post_types = array('estate_property', 'estate_agency', 'estate_agent', 'estate_developer');
    if ( in_array($current_screen->post_type, $estate_post_types) ) {

        if(function_exists('acf_get_field_groups') ){
            // Get all acf meta boxes
            $acf_groups = acf_get_field_groups();

            // Loop through each acf group and remove the tag box
            foreach ($acf_groups as $group) {
                if (isset($group['key']) ) {
                    // Remove the acf meta box for the specified post types
                    remove_meta_box('acf-'. $group['key'], 'estate_property', 'normal');
                    remove_meta_box('acf-'. $group['key'], 'estate_agency', 'normal');
                    remove_meta_box('acf-'. $group['key'], 'estate_agent', 'normal');
                    remove_meta_box('acf-'. $group['key'], 'estate_developer', 'normal');
                }
            }
        }
    }
}
//add_action('admin_head', 'custom_remove_tag_box');
