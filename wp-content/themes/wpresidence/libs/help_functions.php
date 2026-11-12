<?php

/**
 * Sanitize iframe HTML using wp_kses to allow only specific attributes.
 * 
 * This function will sanitize iframe HTML code, allowing only the specified attributes 
 * to ensure security and prevent the injection of malicious code.
 * 
 * It first checks if the function already exists, and if not, it creates it.
 *
 * @param string $iframe_string The raw iframe HTML string to be sanitized.
 * @return string Sanitized iframe HTML string.
 */
if ( ! function_exists( 'wpestate_sanitize_iframe_html' ) ) :

    function wpestate_sanitize_iframe_html( $iframe_string ) {
        // Define allowed HTML tags and attributes for iframes
        $allowed_html = array(
            'iframe' => array(
                'src'             => array(),
                'width'           => array(),
                'height'          => array(),
                'frameborder'     => array(),
                'allowfullscreen' => array(),
                'allow'           => array(),
                'style'           => array(),
                'id'              => array(),
                'class'           => array(),
                'name'            => array(),
                'scrolling'       => array(),
                'marginwidth'     => array(),
                'marginheight'    => array(),
                'sandbox'         => array(),
                'align'           => array(),
                'loading'         => array(),
            ),
        );

        // Use wp_kses to sanitize the iframe based on the allowed HTML array
        $sanitized_iframe = wp_kses( $iframe_string, $allowed_html );

        // Return the sanitized iframe HTML string
        return $sanitized_iframe;
    }
endif;



/* 
 * Function to display the orderby dropdown menu for property listings.
 * It uses different sorting options depending on the context (search results, taxonomy, etc.).
 */
if (!function_exists('wpresidence_display_orderby_dropdown')):

    // Define the function if it doesn't already exist
    function wpresidence_display_orderby_dropdown($postID){

        // Retrieve available sorting options
        $sort_options_array = wpestate_listings_sort_options_array();

        // Get the current listing filter option from the post meta
        $listing_filter = get_post_meta($postID, 'listing_filter', true);

        // If an order search query parameter is present, override the listing filter
        if (isset($_GET['order_search'])) {
            $listing_filter = intval($_GET['order_search']);
        }

        // If the current page is using the advanced search results template, use the corresponding filter option
        if (is_page_template('page-templates/advanced_search_results.php')) {
            $listing_filter = intval(wpresidence_get_option('wp_estate_property_list_type_adv_order', ''));
        }

        // If we are on a taxonomy archive page, use the taxonomy-specific filter option
        if (is_tax()) {
            $listing_filter = intval(wpresidence_get_option('wp_estate_property_list_type_tax_order', ''));
        }

        // Initialize an empty string for the listings dropdown HTML
        $listings_list = '';

        // Loop through each sort option and build the dropdown list
        foreach ($sort_options_array as $key => $value) {
            // Add each option to the dropdown list
            $listings_list .= '<li role="presentation" data-value="' . esc_attr($key) . '">' . esc_html($value) . '</li>';
            
            // If the current sort option matches the listing filter, mark it as selected
            if ($key == $listing_filter) {
                $selected_order = $value; // Display text for the selected order
                $selected_order_num = $key; // Value for the selected order
            }
        }

        // Output the final dropdown menu using a helper function
        echo wpestate_build_dropdown_for_filters('a_filter_order', $selected_order_num, $selected_order, $listings_list);
    }
endif;













/*
 * Social links
 *
 *
 * 
 *
 */


 if (!function_exists('wpestate_return_social_links_icons')):

    function wpestate_return_social_links_icons() {
        
        $defaults = array( 
            'facebook'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_facebook_link',
                                    'icon'              =>  '<i class="fab fa-facebook-f"></i>'
                                ),

            'whatsup'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_whatsapp_link',
                                    'icon'              =>  '<i class="fab fa-whatsapp"></i>'
                                ),      
                                
            'telegram'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_telegram_link',
                                    'icon'              =>  '<i class="fab fa-telegram-plane"></i>'
                                ),

            'tiktok'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_tiktok_link',
                                    'icon'              =>  '<i class="fab fa-tiktok"></i>'
                                ),                          

            'rss'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  '',
                                    'icon'              =>  '<i class="fas fa-rss fa-fw"></i>'
                                ),      
                                
            'twitter'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_twitter_link',
                                    'icon'              =>  '<i class="fa-brands fa-x-twitter"></i>'
                                ),

            'dribbble'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_dribbble_link',
                                    'icon'              =>  '<i class="fab fa-dribbble  fa-fw"></i>'
                                ),                          

            'google'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_google_link',
                                    'icon'              =>  '<i class="fab fa-google"></i>'
                                ),      
                                
            'linkedIn'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_linkedin_link',
                                    'icon'              =>  '<i class="fab fa-linkedin-in"></i>'
                                ),

            'tumblr'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  '',
                                    'icon'              =>  '<i class="fab fa-tumblr  fa-fw"></i>'
                                ),                          

            'pinterest'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_pinterest_link',
                                    'icon'              =>  '<i class="fab fa-pinterest-p  fa-fw"></i>'
                                ),      
                                
            'youtube'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_youtube_link',
                                    'icon'              =>  '<i class="fab fa-youtube  fa-fw"></i>'
                                ),

            'vimeo'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_vimeo_link',
                                    'icon'              =>  '<i class="fab fa-vimeo-v  fa-fw"></i>'
                                ),        
            'instagram'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_instagram_link',
                                    'icon'              =>  '<i class="fab fa-instagram  fa-fw"></i>'
                                ),      
                                
            'foursquare'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_foursquare_link',
                                    'icon'              =>  '<i class="fab  fa-foursquare  fa-fw"></i>'
                                ),

            'line'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_line_link',
                                    'icon'              =>  '<i class="fab fa-line"></i>'
                                ),        
  
      
            'wechat'      =>  array(
                                    'developer_option'  =>  '',
                                    'agency_option'     =>  '',
                                    'agent_option'      =>  '',
                                    'contact_option'    =>  'wp_estate_wechat_link',
                                    'icon'              =>  '<i class="fab fa-weixin"></i>'
                                ),   


     
           
        );
        
        return $defaults;
    }
endif;

/*
 * check if map marker should be hidden
 *
 *
 *
 *
 */

if (!function_exists('wpestate_check_show_map_marker')):

    function wpestate_check_show_map_marker() {
    
        global $post;
        $property_hide_map_marker='';
        if( isset($post->ID) ){
            $property_hide_map_marker=  ( get_post_meta($post->ID, 'property_hide_map_marker',  true));
        }
        
        if($property_hide_map_marker==1){
            return false;
        }else if( wpresidence_get_option('wp_estate_hide_marker_pin')=='yes'){
            if(!is_singular('estate_property')){
                return true;
            }else{
                return false;
            }
        }
        return true;


    }

endif;

/*
 * return favoriters
 *
 *
 *
 *
 */
if (!function_exists('wpestate_return_favorite_listings_per_user')):

    function wpestate_return_favorite_listings_per_user() {
        $curent_fav='';
        if(wpresidence_get_option('wp_estate_favorites_login')=='yes'){
            $current_user = wp_get_current_user();
            $userID = $current_user->ID;
            $user_option = 'favorites' . $userID;
            $curent_fav = get_option($user_option);
        }else{
            if(isset($_COOKIE['wpestate_favorites'] )){
                $curent_fav_text = sanitize_text_field( $_COOKIE['wpestate_favorites'] );
                $curent_fav=explode(',',$curent_fav_text);
            }
        }

        $curent_fav_return='';
        if(is_array($curent_fav)){
            $curent_fav_return = array_filter( $curent_fav, "wpestate_favorites_array_filter" )  ;
        }


        return $curent_fav_return;

    }

endif;


function wpestate_favorites_array_filter($value){
    if(!is_null($value) && $value !== ''){
        return $value;
    }
}




/*
 * return agent list
 *
 *
 *
 *
 */

if (!function_exists('wpestate_return_agent_list')):

    function wpestate_return_agent_list() {
        $current_user = wp_get_current_user();
   

        // Administrators should be able to see all agents, regardless of author
        if (current_user_can('administrator')) {
            $args = array(
                'post_type'      => array('estate_agent', 'estate_agency', 'estate_developer'),
                'posts_per_page' => -1,
                'fields'         => 'ids'
            );

            $agent_list = get_posts($args);
            return array_map('intval', $agent_list);
        }

        $userID     = $current_user->ID;
        $agent_list = (array) get_user_meta($userID, 'current_agent_list', true);
        $agent_list[] = $userID;
        $agent_list = array_filter($agent_list);
      

        return array_map('intval', $agent_list);
    }

endif;

/*
 * return status data for wp_query properties
 *
 *
 *
 *
 */
if (!function_exists('wpestate_set_status_parameter_property')):

    function wpestate_set_status_parameter_property($status) {
        $status = intval($status);
        $return_status = array('any');

        switch ($status) {
            case 0:
                $return_status = array('any');
                break;
            case 1:
                $return_status = array('publish');
                break;
            case 2:
                $return_status = array('disabled');
                break;
            case 3:
                $return_status = array('expired');
                break;
            case 4:
                $return_status = array('draft');
                break;
            case 5:
                $return_status = array('pending');
                break;
        }

        return $return_status;
    }

endif;






/*
 * return orderby data for wp query properties
 *
 *
 *
 *
 */

if (!function_exists('wpestate_set_order_parameter_property')):

    function wpestate_set_order_parameter_property($order) {
        $order = intval($order);
        $return = array();
        $meta_order = 'prop_featured';
        $meta_directions = 'DESC';
        $order_by = 'meta_value_num';

        switch ($order) {
            case 0:
                $meta_order = 'prop_featured';
                $meta_directions = 'DESC';
                $order_by = 'meta_value_num';
                break;
            case 1:
                $meta_order = 'property_price';
                $meta_directions = 'DESC';
                $order_by = 'meta_value_num';
                break;
            case 2:
                $meta_order = 'property_price';
                $meta_directions = 'ASC';
                $order_by = 'meta_value_num';
                break;
            case 3:
                $meta_order = '';
                $meta_directions = 'DESC';
                $order_by = 'ID';
                break;
            case 4:
                $meta_order = '';
                $meta_directions = 'ASC';
                $order_by = 'ID';
                break;
            case 5:
                $meta_order = 'property_bedrooms';
                $meta_directions = 'DESC';
                $order_by = 'meta_value_num';
                break;
            case 6:
                $meta_order = 'property_bedrooms';
                $meta_directions = 'ASC';
                $order_by = 'meta_value_num';
                break;
            case 7:
                $meta_order = 'property_bathrooms';
                $meta_directions = 'DESC';
                $order_by = 'meta_value_num';
                break;
            case 8:
                $meta_order = 'property_bathrooms';
                $meta_directions = 'ASC';
                $order_by = 'meta_value_num';
                break;
            case 11:
                $meta_order = '';
                $meta_directions = 'DESC';
                $order_by = 'modified';
                break;
            case 12:
                $meta_order = '';
                $meta_directions = 'ASC';
                $order_by = 'modified';
                break;
            case 99:
                $meta_order = '';
                $meta_directions = 'ASC';
                $order_by = 'rand';
                break;
        }


        $return ['meta_key'] = $meta_order;
        $return ['orderby'] = $order_by;
        $return ['order'] = $meta_directions;

        return $return;
    }

endif;

/*
 * return custom ajax handler
 *
 *
 *
 *
 */


if (!function_exists('wpestate_return_ajax_handler')):

    function wpestate_return_ajax_handler() {

        if (get_option('wp_estate_use_custom_ajaxhandler') == 'no') {
            //$handler = get_admin_url().'admin-ajax.php';
            $handler = 'wp_ajax';
        } else {
            $handler = 'wpestate_ajax_handler';
        }

        return $handler;
    }

endif;







if (!function_exists('wpestate_filter_for_location_ajax')):

    function wpestate_filter_for_location_ajax($args, $adv_location10) {
        $args['tax_query'] = (array) wpestate_clear_tax($args['tax_query']);
        $allowed_html = array();
        $action_array = array();
        $location_array = array();

        if (isset($adv_location10) && $adv_location10 != '') {

            $value = stripslashes(sanitize_text_field($adv_location10));
            $location_array = array(
                'key' => 'hidden_address',
                'value' => $value,
                'compare' => 'LIKE',
                'type' => 'char',
            );
        }




        if (!empty($action_array)) {
            if (!is_array($args['tax_query'])) {
                $args['tax_query'] = array();
            }

            $args['tax_query'][] = $action_array;
        }

        if (!empty($location_array)) {
            if (!is_array($args['meta_query'])) {
                $args['meta_query'] = array();
            }

            $args['meta_query'][] = $location_array;
        }



        return ($args);
    }

endif;




if (!function_exists('wpestate_filter_for_location')):

    function wpestate_filter_for_location($args) {
        $args['tax_query'] = wpestate_clear_tax($args['tax_query']);
        $allowed_html = array();
        $action_array = array();
        $location_array = array();
        // Step 1: Read the canonical location key and keep the old key as fallback.
        $location_value = '';

        if (isset($_GET['location']) && $_GET['location'] != '') {
            $location_value = $_GET['location'];
        } else if (isset($_GET['adv_location']) && $_GET['adv_location'] != '') {
            $location_value = $_GET['adv_location'];
        }

        if ($location_value != '') {
            // Step 2: Match the location text against the stored hidden address.
            $value = stripslashes(sanitize_text_field($location_value));
            $location_array = array(
                'key' => 'hidden_address',
                'value' => $value,
                'compare' => 'LIKE',
                'type' => 'char',
            );
        }




        if (!empty($action_array)) {
            if (gettype($args['tax_query']) == 'string') {
                $args['tax_query'] = array();
            }
            $args['tax_query'][] = $action_array;
        }

        if (!empty($location_array)) {

            if (gettype($args['meta_query']) == 'string') {
                $args['meta_query'] = array();
            }
            $args['meta_query'][] = $location_array;
        }




        return $args;
    }

endif;











function wpestate_check_mandatory_fields($prop_category = '', $prop_action_category = '') {

    $all_submission_fields = wpestate_return_all_fields();
    $mandatory_fields = wpresidence_get_option('wp_estate_mandatory_page_fields', '');
    $errors = array();
    $custom_fields_array = array(); // Define empty to prevent array_key_exists error

    $prop_category = intval($_POST['prop_category']);
    $prop_action_category = intval($_POST['prop_action_category']);

    $property_county    = sanitize_text_field($_POST['property_county']);
    $property_area      = sanitize_text_field($_POST['property_area']);
    $property_city      = sanitize_text_field($_POST['property_city']);

    $i=0;
    $custom_fields      =   wpresidence_get_option( 'wp_estate_custom_fields', '');
    if( !empty($custom_fields)){
        while($i< count($custom_fields) ){
           $name    =   $custom_fields[$i][0];
           $type    =   $custom_fields[$i][1];
           $slug    =   str_replace(' ','_',$name);
           $slug    =   wpestate_limit45(sanitize_title( $name ));
           $slug    =   sanitize_key($slug);
       
            if (isset($_POST[$slug])) {
                $custom_fields_array[$slug] = sanitize_text_field($_POST[$slug]);
            }
                
        
           $i++;
        }
    }






  

    if (is_array($mandatory_fields)) {

        foreach ($mandatory_fields as $key => $value) {

            if (isset($all_submission_fields[$value]) && term_exists($all_submission_fields[$value], 'property_features')) {
                $value_post = strtolower(sanitize_key($value));
                $value_post = str_replace('%', '', $value_post);
            } else {
                $value_post = wpestate_limit45(sanitize_title($value));
                $value_post = str_replace('%', '', $value_post);
            }


            $check_categs = 0;
            if (($value_post == 'prop_category' && is_numeric($prop_category) && $prop_category == -1) || ($value_post == 'prop_action_category' && is_numeric($prop_action_category) && $prop_action_category == -1)) {
                $check_categs = 1;
            }

            if ( $value_post == 'property_county' && is_numeric($property_county) && $property_county == -1)  {
                $check_categs = 1;
            }

            if ( $value_post == 'property_city' && ( $property_city == 'none' || $property_city == 'all' || $property_city == esc_html__('all','wpresidence') ) )  {
                $check_categs = 1;
            }
            if ( $value_post == 'property_area' && ( $property_area == 'none' || $property_area == 'all' || $property_city == esc_html__('all','wpresidence') ) ) {
                $check_categs = 1;
            }
          
            if( array_key_exists($value_post, $custom_fields_array) && $_POST[$value_post]==esc_html__('Not Available','wpresidence')  ){
                $check_categs = 1;
            }



            if (!isset($_POST[$value_post]) || $_POST[$value_post] == '' || $check_categs == 1) {

                if (isset($all_submission_fields[$value])) {
                    $string = $all_submission_fields[$value] . ' ';
                } else {
                    $value_new = ( str_replace('-', '_', $value));
                    $string = $all_submission_fields[$value_new] . ' ';
                }
                
  

                $string = esc_html__('Please submit the', 'wpresidence') . ' ' . $string . ' ' . esc_html__('field', 'wpresidence');
                $errors[] = $string;
            }
        }
    }
    return $errors;
}



add_action('customize_save_after', 'wpresidence_customizer_savesettings', 10);

function wpresidence_customizer_savesettings() {
    if (has_site_icon()) {
        $values = array();
        $values['id'] = get_option('site_icon');
        $values['url'] = get_site_icon_url();
        if (function_exists('wpestate_residence_functionality_loaded')) {
            require_once WPESTATE_PLUGIN_PATH . 'admin/admin-init.php';
            Redux::init("wpresidence_admin");
            Redux::setOption('wpresidence_admin', 'wp_estate_favicon_image', $values); //front
        }
    }
}

if (!function_exists('wpestate_sorting_function')):

    function wpestate_sorting_function($a, $b) {
        return $a[3] - $b[3];
    }

endif;



/*
*
*
*
*/


if (!function_exists('wpresidence_return_class_leaflet')):

    function wpresidence_return_class_leaflet($tip = '') {
        $what_map = intval(wpresidence_get_option('wp_estate_kind_of_map'));
        if ($what_map == 2) {
            return ' with_open_street ';
        } else {
            return '';
        }
    }

endif;








/*
*
*
*
*/

if (!function_exists('wpestate_fields_type_select_redux')):

    function wpestate_fields_type_select_redux($name_drop, $real_value) {

        $select = '<select   name="' . $name_drop . '"  >';
        $values = array('short text', 'long text', 'numeric', 'date', 'dropdown');

        foreach ($values as $option) {
            $select .= '<option value="' . $option . '"';
            if ($option == $real_value) {
                $select .= ' selected="selected"  ';
            }
            $select .= ' > ' . $option . ' </option>';
        }
        $select .= '</select>';
        return $select;
    }

endif;





/*
*
*
*
*/

if (!function_exists('wpestate_replace_server_global')):

    function wpestate_replace_server_global($link) {
        return str_replace(array('http://', 'https://'), '', $link);
    }

endif;
/*
*
*
*
*/

if (!function_exists('wpestate_return_sending_email')):

    function wpestate_return_sending_email() {
        $name_email = wpresidence_get_option('wp_estate_send_name_email_from', '');
        $from_email = wpresidence_get_option('wp_estate_send_email_from', '');

        $return_string = $name_email.'  <'. $from_email.'>';
        return $return_string;
    }

endif;



if (!function_exists('wpestate_convert_meta_to_postin')):

    function wpestate_convert_meta_to_postin($meta_query) {
        global $table_prefix;
        global $wpdb;
        $searched = 0;

        $feature_list_array = array();
        $allowed_html = array();


        foreach ($meta_query as $checker => $query) {
            //if ($value != '') {
            //    $searched = 1;
            //}


            $input_name = wpestate_limit45(sanitize_title($query['key']));
            $input_name = sanitize_key($input_name);



            if ($query['compare'] == 'BETWEEN') {
                if (trim($input_name) != '') {
                    $min = 0;
                    if ($query['value'][0] != 0) {
                        $min = $query['value'][0];
                    }
                    $potential_ids[$checker] = array_unique(
                            wpestate_get_ids_by_query(
                                    $wpdb->prepare("
                            SELECT DISTINCT post_id
                            FROM " . $table_prefix . "postmeta
                            WHERE meta_key = '%s'
                            AND CAST(meta_value AS SIGNED)  BETWEEN '%f' AND '%f'
                        ", array($input_name, $min, $query['value'][1])))
                    ); //a
                }
            } else if ($query['compare'] == 'LIKE') {
                if (trim($input_name) != '') {
                    $potential_ids[$checker] = array_unique(
                            wpestate_get_ids_by_query(
                                    $wpdb->prepare("
                            SELECT DISTINCT post_id
                            FROM " . $table_prefix . "postmeta
                            WHERE meta_key = '%s'
                            AND meta_value LIKE %s
                            ", array($input_name, $query['value'])))
                    ); //a
                }
            }
        }

        $ids = [];

        foreach ($potential_ids as $key => $temp_ids) {
            if (count($ids) == 0) {
                $ids = $temp_ids;
            } else {
                $ids = array_intersect($ids, $temp_ids);
            }
        }


        if (empty($ids) && $searched == 1) {
            $ids[] = 0;
        }
        return $ids;
    }

endif;















/*
 * Display advanced search functionality for WPResidence theme
 *
 * This function determines whether to display the advanced search form
 * based on various conditions such as page type, theme options, and
 * specific property types.
 *
 * @package WPResidence
 * @subpackage Search
 * @since 1.0.0
 *
 * @param int $post_id The ID of the current post.
 */

 if ( ! function_exists( 'wpestate_show_advanced_search' ) ) :
    function wpestate_show_advanced_search( $post_id ) {
        // Check if we're on a category, taxonomy, or archive page
        if ( is_category() || is_tax() || is_archive() ) {
            // Special case for property list type 2
            if (    ( is_tax() && wpresidence_get_option( 'wp_estate_property_list_type' ) == 2) ) {
                return;
            }

            

            // Check if advanced search is enabled for general pages
            if ( wpresidence_get_option( 'wp_estate_show_adv_search_general', '' ) == 'yes' ) {
                // Display advanced search if not using float search or half map
                if ( ! wpestate_float_search_placement_new( ) && ! wpestate_half_map_conditions( '' ) ) {
                    include( locate_template( 'templates/advanced_search/advanced_search.php' ) );
                }
            }




        } else {
            // Not a category, taxonomy, or archive page
            if ( ! wpestate_float_search_placement_new( ) && ! wpestate_half_map_conditions( $post_id ) ) {
                // Skip for agency and developer single pages
                if ( is_singular( 'estate_agency' ) || is_singular( 'estate_developer' ) ) {
                    return;
                }

                // Display advanced search if not on user dashboard
                if ( ! wpestate_is_user_dashboard() ) {
                    include( locate_template( 'templates/advanced_search/advanced_search.php' ) );
                }
            }
        }
    }
endif;






if (!function_exists('wpestate_retrive_float_search_placement')):

    function wpestate_retrive_float_search_placement($post_id) {
        $page_use_float_search = '';
        if (isset($post_id)) {
            $page_use_float_search = get_post_meta($post_id, 'page_use_float_search', true);
        }
        if (is_404() || is_category() || is_tax() || is_archive() || is_search()) {
            return esc_html(wpresidence_get_option('wp_estate_use_float_search_form', ''));
        }
        if ($page_use_float_search == 'global') {
            return esc_html(wpresidence_get_option('wp_estate_use_float_search_form', ''));
        } else {
            return $page_use_float_search;
        }
    }

endif;




if (!function_exists('wpestate_search_float_position')):

    function wpestate_search_float_position($post_id) {
        $return = '';
        if (isset($post_id)) {
            $page_use_float_search = get_post_meta($post_id, 'page_use_float_search', true);
            if ($page_use_float_search == 'yes') {
                $return = ' style="top:' . get_post_meta($post_id, 'page_wp_estate_float_form_top', true) . ';" ';
            }
        }
        return $return;
    }

endif;





if (!function_exists('wpestate_show_poi_onmap')):

    function wpestate_show_poi_onmap($where = '') {
        global $post;
        // Step 1: allow POI markers only for property-related single/template contexts.
        $is_single_property_page = is_singular('estate_property');
        $is_custom_property_template = is_page_template('page-templates/page_property_design.php');
        $is_property_studio_template = is_singular('wpestate-studio');

        if (
            (!$is_single_property_page && !$is_custom_property_template && !$is_property_studio_template) ||
            wpresidence_get_option('wp_estate_kind_of_map') == 2
        ) {
            return;
        }


        // Step 2: define the POI categories and labels shown in the marker toolbar.
        $points = array(
            'transport'     => esc_html__('Transport', 'wpresidence'),
            'supermarkets'  => esc_html__('Supermarkets', 'wpresidence'),
            'schools'       => esc_html__('Schools', 'wpresidence'),
            'restaurant'    => esc_html__('Restaurants', 'wpresidence'),
            'pharma'        => esc_html__('Pharmacies', 'wpresidence'),
            'hospitals'     => esc_html__('Hospitals', 'wpresidence'),
        );

        $unique_id=rand(1,9999);
        $return_value = '<div class="google_map_poi_marker">';
        // Step 3: build each POI icon and return the final marker toolbar HTML.
        foreach ($points as $key => $value) {
            $return_value .= '<div class="google_poi' .esc_attr($where).'" data-value="'.esc_attr($key).'" id="'.esc_attr($key.'_'.$unique_id).'"><img src="' . get_theme_file_uri('/css/css-images/poi/' . $key . '_icon.png') . '" class="dashboad-tooltip" alt="' . esc_attr($value) . '"  data-bs-placement="right"  data-bs-toggle="tooltip" title="' . esc_attr($value) . '" ></div>';
        }
        $return_value .= '</div>';
        return $return_value;
    }

endif;









/*
*
* 
*
*/



if (!function_exists('wpestate_add_allowed_tags')):

    function wpestate_add_allowed_tags($tags) {

        $allowed_html_desc = array(
            'a' => array(
                'href' => array(),
                'title' => array()
            ),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
            'ul' => array('li'),
            'li' => array(),
            'code' => array(),
            'ol' => array('li'),
            'del' => array(
                'datetime' => array()
            ),
            'blockquote' => array(),
            'ins' => array(),
        );
        return $allowed_html_desc;
    }

endif;



if (!function_exists('wpestate_strip_array')):

    function wpestate_strip_array($key) {

        $string = htmlspecialchars(stripslashes(($key)), ENT_QUOTES);

        return wp_specialchars_decode($string);
    }

endif;







if (!function_exists('wpestate_calculate_distance_geo')):

    function wpestate_calculate_distance_geo($lat, $long, $start_lat, $start_long, $yelp_dist_measure) {

        $angle = $start_long - $long;
        $distance = sin(deg2rad($start_lat)) * sin(deg2rad($lat)) + cos(deg2rad($start_lat)) * cos(deg2rad($lat)) * cos(deg2rad($angle));
        $distance = acos($distance);
        $distance = rad2deg($distance);

        if ($yelp_dist_measure == 'miles') {
            $distance_miles = $distance * 60 * 1.1515;
            return '(' . round($distance_miles, 2) . ' ' . esc_html__('miles', 'wpresidence') . ')';
        } else {
            $distance_miles = $distance * 60 * 1.1515 * 1.6;
            return '(' . round($distance_miles, 2) . ' ' . esc_html__('km', 'wpresidence') . ')';
        }
    }

endif;





if (!function_exists('wpestate_sizes_no_format')):

    function wpestate_sizes_no_format($value, $return = 0) {
        $th_separator = wpresidence_get_option('wp_estate_prices_th_separator', '');
        $return = stripslashes(number_format((floatval($value)), 0, '.', $th_separator));
        return $return;
    }

endif;









if (!function_exists('wpestate_show_price_custom_invoice')):

    function wpestate_show_price_custom_invoice($price) {
        $price_label = '';
        $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_submission_curency', ''));
        $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
        $th_separator = wpresidence_get_option('wp_estate_prices_th_separator', '');
        $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');

        if ($price != 0) {
            $price = number_format($price, 2, '.', $th_separator);

            if ($where_currency == 'before') {
                $price = $wpestate_currency . $price;
            } else {
                $price = $price . $wpestate_currency;
            }
        } else {
            $price = '';
        }


        return $price . ' ' . $price_label;
    }

endif;

/////////////////////////////////////////////////////////////////////////////////
// datepcker_translate
///////////////////////////////////////////////////////////////////////////////////
if (!function_exists('wpestate_date_picker_translation')):

    function wpestate_date_picker_translation($selector) {
        $date_lang_status = esc_html(wpresidence_get_option('wp_estate_date_lang', ''));
        print '<script type="text/javascript">
                //<![CDATA[
                jQuery(document).ready(function(){
                        jQuery("#' . $selector . '").datepicker({
                                dateFormat : "yy-mm-dd"
                        },jQuery.datepicker.regional["' . $date_lang_status . '"]).datepicker("widget").wrap(\'<div class="ll-skin-melon"/>\');
                });
                //]]>
            </script>';
    }

endif;


if (!function_exists('wpestate_date_picker_translation_return')):

    function wpestate_date_picker_translation_return($selector) {
        $date_lang_status = esc_html(wpresidence_get_option('wp_estate_date_lang', ''));
        return '<script type="text/javascript">
                //<![CDATA[
                jQuery(document).ready(function(){
                        jQuery("#' . $selector . '").datepicker({
                                dateFormat : "yy-mm-dd",
                                changeMonth: true,
                                changeYear: true,
                                yearRange: "-100:+50",
                        },jQuery.datepicker.regional["' . $date_lang_status . '"]).datepicker("widget").wrap(\'<div class="ll-skin-melon"/>\');
                });
                //]]>
            </script>';
    }

endif;













/////////////////////////////////////////////////////////////////////////////////
// order by filter featured
///////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_get_measure_unit')):

    function wpestate_get_measure_unit() {
        $measure_sys = esc_html(wpresidence_get_option('wp_estate_measure_sys', ''));

        if ($measure_sys == 'feet') {
            return 'ft<sup>2</sup>';
        } else {
            return 'm<sup>2</sup>';
        }
    }

endif;


////////////////////////////////////////////////////////////////////////////////////////
/////// Pagination
/////////////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_pagination')):

    function wpestate_pagination($pages = '', $range = 2) {

        $showitems = ($range * 2) + 1;
        global $paged;
        if (empty($paged))
            $paged = 1;


        if ($pages == '') {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if (!$pages) {
                $pages = 1;
            }
        }

        if (1 != $pages && $pages != 0) {
            print '<ul class="pagination pagination_nojax">';
            print "<li class=\"roundleft\"><a href='" . get_pagenum_link($paged - 1) . "'><i class=\"fas fa-angle-left\"></i></a></li>";

            $last_page = get_pagenum_link($pages);
            for ($i = 1; $i <= $pages; $i++) {
                if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                    if ($paged == $i) {
                        print '<li class="active"><a href="' . esc_url(get_pagenum_link($i)) . '" >' . $i . '</a><li>';
                    } else {
                        print '<li><a href="' . esc_url(get_pagenum_link($i)) . '" >' . $i . '</a><li>';
                    }
                }
            }

            $prev_page = get_pagenum_link($paged + 1);
            if (($paged + 1) > $pages) {
                $prev_page = get_pagenum_link($paged);
            } else {
                $prev_page = get_pagenum_link($paged + 1);
            }


            print "<li class=\"roundright\"><a href='" . $prev_page . "'><i class=\"fas fa-angle-right\"></i></a><li>";

            print "<li class=\"roundright\"><a href='" . $last_page . "'><i class=\"fa fa-angle-double-right\"></i></a><li>";

            print "</ul>";
        }
    }

endif; // end   wpestate_pagination
////////////////////////////////////////////////////////////////////////////////////////
/////// Pagination Ajax
/////////////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_pagination_agent')):

    function wpestate_pagination_agent($pages = '', $range = 2) {

        $showitems = ($range * 2) + 1;
        $paged = (get_query_var('page')) ? get_query_var('page') : 1;
        if (empty($paged))
            $paged = 1;

        if (1 != $pages && $pages != 0) {
            $prev_pagex = str_replace('page/', '', get_pagenum_link($paged - 1));
            print '<ul class="pagination pagination_nojax">';
            print "<li class=\"roundleft\"><a href='" . $prev_pagex . "'><i class=\"fas fa-angle-left\"></i></a></li>";
            $last_page = get_pagenum_link($pages);
            for ($i = 1; $i <= $pages; $i++) {
                $cur_page = str_replace('page/', '', get_pagenum_link($i));
                if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                    if ($paged == $i) {
                        print '<li class="active"><a href="' . esc_url($cur_page) . '" >' . $i . '</a><li>';
                    } else {
                        print '<li><a href="' . esc_url($cur_page) . '" >' . $i . '</a><li>';
                    }
                }
            }

            $prev_page = str_replace('page/', '', get_pagenum_link($paged + 1));
            if (($paged + 1) > $pages) {
                $prev_page = str_replace('page/', '', get_pagenum_link($paged));
            } else {
                $prev_page = str_replace('page/', '', get_pagenum_link($paged + 1));
            }


            print "<li class=\"roundright\"><a href='" . $prev_page . "'><i class=\"fas fa-angle-right\"></i></a><li>";
            print "<li class=\"roundright\"><a href='" . $last_page . "'><i class=\"fa fa-angle-double-right\"></i></a><li>";
            print "</ul>";
        }
    }

endif; // end   wpestate_pagination
////////////////////////////////////////////////////////////////////////////////////////
/////// Pagination Custom
/////////////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_pagination_ajax_newver')):

    function wpestate_pagination_ajax_newver($pages, $range, $paged, $where, $order) {
        $showitems = ($range * 2) + 1;

        if (1 != $pages && $pages != 0) {
            print '<ul class="pagination c ' . $where . '">';
            if ($paged != 1) {
                $prev_page = $paged - 1;
            } else {
                $prev_page = 1;
            }

            $prev_link = get_pagenum_link($paged - 1);
            $prev_link = add_query_arg('order', $order, $prev_link);
            $last_page = get_pagenum_link($pages);
            $last_page = add_query_arg('order', $order, $last_page);
            print "<li class=\"roundleft\"><a href='" . $prev_link . "' data-future='" . esc_attr($prev_page) . "'><i class=\"fas fa-angle-left\"></i></a></li>";

            for ($i = 1; $i <= $pages; $i++) {
                $page_link = get_pagenum_link($i);
                $page_link = add_query_arg('order', $order, $page_link);
                if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                    if ($paged == $i) {
                        print '<li class="active"><a href="' . esc_url($page_link) . '" data-future="' . esc_attr($i) . '">' . esc_html($i) . '</a><li>';
                    } else {
                        print '<li><a href="' . esc_url($page_link) . '" data-future="' . esc_attr($i) . '">' . esc_html($i) . '</a><li>';
                    }
                }
            }

            $next_page = get_pagenum_link($paged + 1);
            if (($paged + 1) > $pages) {
                $next_page = get_pagenum_link($paged);
                $next_page = add_query_arg('order', $order, $next_page);
                print "<li class=\"roundright\"><a href='" . esc_url($next_page) . "' data-future='" . esc_attr($paged) . "'><i class=\"fas fa-angle-right\"></i></a><li>";
            } else {
                $next_page = get_pagenum_link($paged + 1);
                $next_page = add_query_arg('order', $order, $next_page);
                print "<li class=\"roundright\"><a href='" . esc_url($next_page) . "' data-future='" . esc_attr($paged + 1) . "'><i class=\"fas fa-angle-right\"></i></a><li>";
            }
            print "<li class=\"roundright\"><a href='" . $last_page . "'  data-future='" . esc_attr( $pages ) . "' ><i class=\"fa fa-angle-double-right\"></i></a><li>";
            print "</ul>\n";
        }
    }

endif; // end   wpestate_pagination

if (!function_exists('wpestate_pagination_ajax')):

    function wpestate_pagination_ajax($pages, $range, $paged, $where) {
        $showitems = ($range * 2) + 1;

        if (1 != $pages && $pages != 0) {
            print '<ul class="pagination c ' . $where . '">';
            if ($paged != 1) {
                $prev_page = $paged - 1;
            } else {
                $prev_page = 1;
            }
            print "<li class=\"roundleft\"><a href='" . esc_url(get_pagenum_link($paged - 1)) . "' data-future='" . esc_attr($prev_page) . "'><i class=\"fas fa-angle-left\"></i></a></li>";
            $last_page = get_pagenum_link($pages);
            for ($i = 1; $i <= $pages; $i++) {
                if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems )) {
                    if ($paged == $i) {
                        print '<li class="active"><a href="' . esc_url(get_pagenum_link($i)) . '" data-future="' . esc_attr($i) . '">' . esc_html($i) . '</a><li>';
                    } else {
                        print '<li><a href="' . esc_url(get_pagenum_link($i)) . '" data-future="' . esc_attr($i) . '">' . esc_html($i) . '</a><li>';
                    }
                }
            }

            $prev_page = get_pagenum_link($paged + 1);
            if (($paged + 1) > $pages) {
                $prev_page = get_pagenum_link($paged);
                print "<li class=\"roundright\"><a href='" . esc_url($prev_page) . "' data-future='" . esc_attr($paged) . "'><i class=\"fas fa-angle-right\"></i></a><li>";
            } else {
                $prev_page = get_pagenum_link($paged + 1);
                print "<li class=\"roundright\"><a href='" . esc_url($prev_page) . "' data-future='" . esc_attr($paged + 1) . "'><i class=\"fas fa-angle-right\"></i></a><li>";
            }

            print "<li class=\"roundright\"><a data-future='".esc_attr($pages)."' href='" . $last_page . "'><i class=\"fa fa-angle-double-right\"></i></a><li>";

            print "</ul>\n";
        }
    }

endif; // end   wpestate_pagination
////////////////////////////////////////////////////////////////////////////////
/// force html5 validation -remove category list rel atttribute
////////////////////////////////////////////////////////////////////////////////

add_filter('wp_list_categories', 'wpestate_remove_category_list_rel');
add_filter('the_category', 'wpestate_remove_category_list_rel');

if (!function_exists('wpestate_remove_category_list_rel')):

    function wpestate_remove_category_list_rel($output) {
        // Remove rel attribute from the category list
        return str_replace(' rel="category tag"', '', $output);
    }

endif; // end   wpestate_remove_category_list_rel
////////////////////////////////////////////////////////////////////////////////
/// avatar url
////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_get_avatar_url')):

    function wpestate_get_avatar_url($get_avatar) {
        preg_match("/src='(.*?)'/i", $get_avatar, $matches);
        return $matches[1];
    }

endif; // end   wpestate_get_avatar_url
////////////////////////////////////////////////////////////////////////////////
///  get current map height
////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_get_current_map_height')):

    function wpestate_get_current_map_height($post_id) {

        if ($post_id == '' || is_home()) {
            $min_height = intval(wpresidence_get_option('wp_estate_min_height', ''));
        } else {
            $min_height = intval((get_post_meta($post_id, 'min_height', true)));
            if ($min_height == 0) {
                $min_height = intval(wpresidence_get_option('wp_estate_min_height', ''));
            }
        }
        return $min_height;
    }

endif; // end   wpestate_get_current_map_height
////////////////////////////////////////////////////////////////////////////////
///  get  map open height
////////////////////////////////////////////////////////////////////////////////

if (!function_exists('get_map_open_height')):

    function wpestate_get_map_open_height($post_id) {

        if ($post_id == '' || is_home()) {
            $max_height = intval(wpresidence_get_option('wp_estate_max_height', ''));
        } else {
            $max_height = intval((get_post_meta($post_id, 'max_height', true)));
            if ($max_height == 0) {
                $max_height = intval(wpresidence_get_option('wp_estate_max_height', ''));
            }
        }

        return $max_height;
    }

endif; // end   get_map_open_height
////////////////////////////////////////////////////////////////////////////////
///  get  map open/close status
////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_get_map_open_close_status')):

    function wpestate_get_map_open_close_status($post_id) {
        if ($post_id == '' || is_home()) {
            $keep_min = esc_html(wpresidence_get_option('wp_estate_keep_min', ''));
        } else {
            $keep_min = esc_html((get_post_meta($post_id, 'keep_min', true)));
        }

        if ($keep_min == 'yes') {
            $keep_min = 1; // map is forced at closed
        } else {
            $keep_min = 0; // map is free for resize
        }

        return $keep_min;
    }

endif; // end   wpestate_get_map_open_close_status
////////////////////////////////////////////////////////////////////////////////
///  get  map  longitude
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('wpestate_get_page_long')):

    function wpestate_get_page_long($post_id) {
        $header_type = get_post_meta($post_id, 'header_type', true);
        if ($header_type == 5) {
            $page_long = esc_html(get_post_meta($post_id, 'page_custom_long', true));
        } else {
            $page_long = esc_html(wpresidence_get_option('wp_estate_general_longitude', ''));
        }
        return $page_long;
    }

endif; // end   wpestate_get_page_long
////////////////////////////////////////////////////////////////////////////////
///  get  map  lattitudine
////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_get_page_lat')):

    function wpestate_get_page_lat($post_id) {
        $header_type = get_post_meta($post_id, 'header_type', true);
        if ($header_type == 5) {
            $page_lat = esc_html(get_post_meta($post_id, 'page_custom_lat', true));
        } else {
            $page_lat = esc_html(wpresidence_get_option('wp_estate_general_latitude', ''));
        }
        return $page_lat;
    }

endif; // end   wpestate_get_page_lat
////////////////////////////////////////////////////////////////////////////////
///  get  map  zoom
////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_get_page_zoom')):

    function wpestate_get_page_zoom($post_id) {
        $header_type = get_post_meta($post_id, 'header_type', true);
        if ($header_type == 5) {
            $page_zoom = get_post_meta($post_id, 'page_custom_zoom', true);
        } else {
            $page_zoom = esc_html(wpresidence_get_option('wp_estate_default_map_zoom', ''));
        }
        return $page_zoom;
    }

endif; // end   wpestate_get_page_zoom


///////////////////////////////////////////////////////////////////////////////////////////
// return video divs for sliders
///////////////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_custom_vimdeo_video')):

    function wpestate_custom_vimdeo_video($video_id) {
        $protocol = is_ssl() ? 'https' : 'http';
        return $return_string = '
        <div style="max-width:100%;" class="video">
           <iframe id="player_1" src="' . $protocol . '://player.vimeo.com/video/' . $video_id . '?api=1&amp;player_id=player_1"      allowFullScreen></iframe>
        </div>';
    }

endif; // end   wpestate_custom_vimdeo_video


if (!function_exists('wpestate_custom_youtube_video')):

function wpestate_custom_youtube_video($video_id) {
    $protocol = is_ssl() ? 'https' : 'http';
    return '
    <div style="max-width:100%;" class="video">
        <iframe
            id="player_2"
            title="YouTube video player"
            width="560"
            height="315"
            src="' . $protocol . '://www.youtube.com/embed/' . esc_attr($video_id) . '?enablejsapi=1&rel=0"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen
        ></iframe>
    </div>';
}
endif; // end



if (!function_exists('wpestate_custom_tiktok_video')):

    function wpestate_custom_tiktok_video($video_id) {
        $protocol = is_ssl() ? 'https' : 'http';
        return $return_string = '
        <div style="max-width:100%;" class="video">
            <iframe id="player_3" src="' . $protocol . '://www.tiktok.com/embed/v2/' . $video_id . '" allowfullscreen ></iframe>
        </div>';
    }

endif; // end   wpestate_custom_tiktok_video


if (!function_exists('wpestate_get_video_thumb')):

    function wpestate_get_video_thumb($post_id) {
        $video_id   = esc_html(get_post_meta($post_id, 'embed_video_id', true));
        $video_type = strtolower( trim( esc_html( get_post_meta($post_id, 'embed_video_type', true) ) ) );
        $protocol = is_ssl() ? 'https' : 'http';
        if ($video_type == 'vimeo') {
            $hash2 = ( wp_remote_get($protocol . "://vimeo.com/api/v2/video/$video_id.php") );
            $pre_tumb = (unserialize($hash2['body']) );
            $video_thumb = $pre_tumb[0]['thumbnail_medium'];
        } elseif ($video_type == 'tiktok') {
            $video_thumb = get_the_post_thumbnail_url($post_id, 'listing_full_slider_1');
        } else {
            $video_thumb = $protocol . '://img.youtube.com/vi/' . $video_id . '/0.jpg';
        }
        return $video_thumb;
    }

endif;


if (!function_exists('wpestate_generateRandomString')):

    function wpestate_generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

endif;



///////////////////////////////////////////////////////////////////////////////////////////
/////// Return country list for adv search
///////////////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_country_list_adv_search')):

    function wpestate_country_list_adv_search($appendix, $slug) {
        $country_list = wpestate_country_list_search($slug);
        $allowed_html = array();
        if (isset($_GET['property_country']) && $_GET['property_country'] != '' && $_GET['property_country'] != 'all') {
            $advanced_country_value = esc_html(wp_kses($_GET['property_country'], $allowed_html));
            $advanced_country_value1 = '';
        } else {
            $advanced_country_value = esc_html__('All Countries', 'wpresidence');
            $advanced_country_value1 = 'all';
        }

        $return_string = wpestate_build_dropdown_adv_new($appendix, 'adv-search-country', 'advanced_country', $advanced_country_value, $advanced_country_value1, 'advanced_country', $country_list);
        return $return_string;
    }

endif;



/*
*
*
*
*
*/


if (!function_exists('wpestate_return_title_from_slug')):

    function wpestate_return_title_from_slug($get_var, $getval) {

        // Default result — used when no term is found
        $result = array(
            'name'    => $getval,
            'term_id' => 0,
        );

        // Map search field names to their WordPress taxonomy slugs
        $taxonomy_map = array(
            'property_category'   => 'property_category',
            'property_action_category' => 'property_action_category',
            'property_city'        => 'property_city',
            'property_area'        => 'property_area',
            'property_county_state'  => 'property_county_state',
            'property_status'      => 'property_status',
        );

        // If the field isn't a mapped taxonomy, return defaults
        if ( ! isset( $taxonomy_map[ $get_var ] ) ) {
            return $result;
        }

        // "All" means no specific term selected — return defaults
        if ( $getval === 'All' ) {
            return $result;
        }

        // Look up the term by slug in the matched taxonomy
        $term = get_term_by( 'slug', $getval, $taxonomy_map[ $get_var ] );

        // Guard against bad/deleted slugs returning false
        if ( ! $term || is_wp_error( $term ) ) {
            return $result;
        }

        // Build result with term data
        $result['term_id'] = intval( $term->term_id );

        // property_status applies ucwords to the name
        if ( $get_var === 'property_status' ) {
            $result['name'] = ucwords( $term->name );
        } else {
            $result['name'] = $term->name;
        }

        return $result;
    }

endif;
















///////////////////////////////////////////////////////////////////////////////////////////
/////// Show advanced search form - custom fileds
///////////////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_map_adv_search_field_to_meta_key')):
    /**
     * Map advanced search field labels to the canonical WP Residence field/meta keys.
     *
     * @param string $field_label Advanced search label/key (for example "property rooms").
     *
     * @return string Canonical key used by search payload attributes.
     */
    function wpestate_map_adv_search_field_to_meta_key($field_label) {
        // Step 1: Normalize input so lookups stay consistent.
        $normalized_field = '';
        if (is_string($field_label)) {
            $normalized_field = trim($field_label);
        }

        // Step 2: Map known admin submission fields to canonical keys.
        $field_map = array(
            'wpestate location'      => 'wpestate_location',
            'property price'         => 'property_price',
            'property-price-v2'      => 'property_price',
            'property-price-v3'      => 'property_price',
            'property size'          => 'property_size',
            'property lot size'      => 'property_lot_size',
            'property rooms'         => 'property_rooms',
            'property bedrooms'      => 'property_bedrooms',
            'property bathrooms'     => 'property_bathrooms',
            'beds-baths'             => 'beds_baths',
            'property address'       => 'property_address',
            'property zip'           => 'property_zip',
            'property country'       => 'property_country',
            'property status'        => 'property_status',
            'property id'            => 'property_id',
            'property internal id'   => 'property_internal_id',
            'keyword'                => 'keyword',
            'geolocation'            => 'geolocation',
            'geolocation_radius'     => 'geolocation_radius',
        );

        // Step 3: Return mapped value when available, otherwise keep original value.
        if (isset($field_map[$normalized_field])) {
            return $field_map[$normalized_field];
        }

        // Step 4: For custom fields, return the same normalized slug used when
        // the property meta is stored and when the front-end submit form posts values.
        if (function_exists('wpestate_normalize_custom_field_slug')) {
            return wpestate_normalize_custom_field_slug($normalized_field);
        }

        return $normalized_field;
    }
endif;

if (!function_exists('wpestate_show_search_field_with_tabs')):

  //                   wpestate_show_search_field($label, $position, $search_field, $action_select_list, $categ_select_list, $select_city_list, $select_area_list, $key, $select_county_state_list, $term_counter_elementor = '', $placeholder = '', $elementor_label = '', $item_field_how = '', $price_array_data = '') {
    function wpestate_show_search_field_with_tabs($label, $active, $position, $search_field, $action_select_list, $categ_select_list, $select_city_list, $select_area_list, $key, $select_county_state_list, $use_name, $term_id, $adv_search_fields_no, $term_counter) {
        $adv_search_what = wpresidence_get_option('wp_estate_adv_search_what', '');
        $adv_search_label = wpresidence_get_option('wp_estate_adv_search_label', '');
        $adv_search_how = wpresidence_get_option('wp_estate_adv_search_how', '');
        $adv6_max_price = wpresidence_get_option('wp_estate_adv6_max_price');
        $adv6_min_price = wpresidence_get_option('wp_estate_adv6_min_price');
 
        $adv6_taxonomy_terms = wpresidence_get_option('wp_estate_adv6_taxonomy_terms');

        $adv_search_what = array_slice($adv_search_what, ($term_counter * $adv_search_fields_no), $adv_search_fields_no);
        $adv_search_label = array_slice($adv_search_label, ($term_counter * $adv_search_fields_no), $adv_search_fields_no);
        $adv_search_how = array_slice($adv_search_how, ($term_counter * $adv_search_fields_no), $adv_search_fields_no);


        $allowed_html = array();
        if ($position == 'mainform') {
            $appendix = '';
        } else if ($position == 'sidebar') {
            $appendix = 'sidebar-';
        } else if ($position == 'shortcode') {
            $appendix = 'shortcode-';
        } else if ($position == 'mobile') {
            $appendix = 'mobile-';
        } else if ($position == 'half') {
            $appendix = 'half-';
        }

        $elementor_label='';


        $return_string = '';
        if ($search_field == 'none') {
            $return_string = '';
        } else if ($search_field == 'beds-baths') {          
            
            $return_string  .=  wpestate_show_beds_baths_component($appendix,$label,$elementor_label, $term_id,$position,$active);

        } else if ($search_field == 'property-price-v2') {
           
            $show_dropdowns = wpresidence_get_option('wp_estate_show_dropdowns', '');
            $string = wpestate_limit45(sanitize_title($adv_search_label[$key]));
            $slug = sanitize_key($string);

            $label = $adv_search_label[$key];
            if (function_exists('icl_translate')) {
                $label = icl_translate('wpestate', 'wp_estate_custom_search_' . $label, $label);
            }


          
          $return_string .= wpestate_show_price_v2_component_theme_search($position, $slug, $label, $use_name, $term_id, $adv6_taxonomy_terms, $adv6_min_price, $adv6_max_price,'yes');
        
        
        }else if ($search_field == 'property-price-v3') {
                $show_dropdowns = wpresidence_get_option('wp_estate_show_dropdowns', '');
                $string = wpestate_limit45(sanitize_title($adv_search_label[$key]));
                $slug = sanitize_key($string);

                $label = $adv_search_label[$key];
                if (function_exists('icl_translate')) {
                    $label = icl_translate('wpestate', 'wp_estate_custom_search_' . $label, $label);
                }

                $wp_estate_adv6_max_price_dropdown_values= wpresidence_get_option('wp_estate_adv6_max_price_dropdown_values');
                $wp_estate_adv6_min_price_dropdown_values= wpresidence_get_option('wp_estate_adv6_min_price_dropdown_values');
                $price_array_data=array();
                $price_array_data['term_id'] =$term_id;
                $price_key      =   array_search($term_id,$adv6_taxonomy_terms);


                $price_array_data['min_price_values'] =$wp_estate_adv6_min_price_dropdown_values[$price_key];
                $price_array_data['max_price_values'] =$wp_estate_adv6_max_price_dropdown_values[$price_key];

              $return_string .= wpestate_show_price_v3_component($appendix,$slug,$label,$label,$elementor_label, $term_id,$position,$price_array_data);
           
             
        } else if ($search_field == 'geolocation') {
            $return_string .= wpestate_show_geolocation_field($appendix,$label,'', $term_counter,$position);
        } else if ($search_field == 'geolocation_radius') {
            $return_string .= wpestate_show_geolocation_radius_field($appendix,'','', $term_counter);
        } else if ($search_field == 'wpestate location') {
            $return_string .= wpestate_show_location_field($appendix, $term_counter);
        } else if ($search_field == 'property status') {                    
            $return_string .= wpestate_show_dropdown_taxonomy_v21($search_field, $label, $appendix,$active);
      
        } else if ($search_field == 'types') {

            $return_string .= wpestate_show_dropdown_taxonomy_v21($search_field, $label, $appendix,$active);       
       
        } else if ($search_field == 'categories') {
   
            $return_string .= wpestate_show_dropdown_taxonomy_v21($search_field, $label, $appendix,$active);
       
       
        } else if ($search_field == 'cities') {
      
            $return_string .= wpestate_show_dropdown_taxonomy_v21($search_field, $label, $appendix,$active);
        
        
        } else if ($search_field == 'areas') {

                    
            $return_string .= wpestate_show_dropdown_taxonomy_v21($search_field, $label, $appendix,$active);
        
        
        } else if ($search_field == 'county / state') {
                    
            $return_string .= wpestate_show_dropdown_taxonomy_v21($search_field, $label, $appendix,$active);
    
        } else {
            $show_dropdowns = wpresidence_get_option('wp_estate_show_dropdowns', '');
            $string = wpestate_limit45(sanitize_title($adv_search_label[$key]));
            $slug = sanitize_key($string);

            $label = $adv_search_label[$key];
            if (function_exists('icl_translate')) {
                $label = icl_translate('wpestate', 'wp_estate_custom_search_' . $label, $label);
            }

            if ($adv_search_what[$key] == 'property country') {
                ////////////////////////////////  show country list
                $return_string = wpestate_country_list_adv_search($appendix, $slug);
            } else if ($adv_search_what[$key] == 'property price') {
                ////////////////////////////////  show price form
                // Step 1: Pass the configured compare rule so the plain text input
                // uses the same search behavior as the generic custom-field renderer.
                $price_search_compare = '';
                if (isset($adv_search_how[$key]) && is_string($adv_search_how[$key])) {
                    $price_search_compare = trim($adv_search_how[$key]);
                }

                $return_string = wpestate_price_form_adv_search_with_tabs($position, $slug, $label, $use_name, $term_id, $adv6_taxonomy_terms, $adv6_min_price, $adv6_max_price, '', $price_search_compare);
            } else if ($show_dropdowns == 'yes' && ( $adv_search_what[$key] == 'property rooms' || $adv_search_what[$key] == 'property bedrooms' || $adv_search_what[$key] == 'property bathrooms')) {
              
                if (function_exists('icl_translate')) {
                    $label = icl_translate('wpestate', 'wp_estate_custom_search_' . $adv_search_label[$key], $adv_search_label[$key]);
                } else {
                    $label = $adv_search_label[$key];
                }

                $rooms_select_list= wpestate_rooms_select_list_simple_dropdown($adv_search_what[$key], '',$label);
                $dropdown_meta_key = wpestate_map_adv_search_field_to_meta_key($adv_search_what[$key]);
                $dropdown_get_var = ($dropdown_meta_key !== '') ? $dropdown_meta_key : $slug;
                $dropdown_search_compare = '';
                if (isset($adv_search_how[$key]) && is_string($adv_search_how[$key])) {
                    $dropdown_search_compare = trim($adv_search_how[$key]);
                }

                
                $return_string = wpestate_build_dropdown_adv_new($appendix, 'search-' . $slug, $slug, $label, 'all', $dropdown_get_var, $rooms_select_list, $active, $dropdown_search_compare);
            } else {
                $custom_fields = wpresidence_get_option('wp_estate_custom_fields', '');

                $i = 0;
                $found_dropdown = 0;
                ///////////////////////////////// dropdown check
                if (!empty($custom_fields)) {
                    while ($i < count($custom_fields)) {
                        $name = $custom_fields[$i][0];

                        $slug_drop = str_replace(' ', '-', $name);

                        if ($slug_drop == $adv_search_what[$key] && $custom_fields[$i][2] == 'dropdown') {

                            $found_dropdown = 1;
                            $front_name = sanitize_title($adv_search_label[$key]);
                            $dropdown_post_meta_name = wpestate_map_adv_search_field_to_meta_key($adv_search_what[$key]);
                            if (function_exists('icl_translate')) {
                                $initial_key = apply_filters('wpml_translate_single_string', trim($adv_search_label[$key]), 'custom field value', 'custom_field_value' . $adv_search_label[$key]);
                                $action_select_list = ' <li role="presentation" data-value="all"> ' . $initial_key . '</li>';
                            } else {
                                $action_select_list = ' <li role="presentation" data-value="all">' . $adv_search_label[$key] . '</li>';
                            }

                            $dropdown_values_array = explode(',', $custom_fields[$i][4]);

                            foreach ($dropdown_values_array as $drop_key => $value_drop) {
                                $original_value_drop = $value_drop;
                                if (function_exists('icl_translate')) {

                                    $value_drop = apply_filters('wpml_translate_single_string', trim($value_drop), 'custom field value', 'custom_field_value' . $value_drop);
                                }
                                $action_select_list .= ' <li role="presentation" data-value="' . trim(esc_attr($original_value_drop)) . '">' . trim($value_drop) . '</li>';
                            }
                            $front_name = sanitize_title($adv_search_label[$key]);
                            $dropdown_request_key = ($dropdown_post_meta_name !== '') ? $dropdown_post_meta_name : $front_name;
                            if (isset($_GET[$dropdown_request_key]) && $_GET[$dropdown_request_key] != '' && $_GET[$dropdown_request_key] != 'all') {
                                $advanced_drop_value = esc_attr(wp_kses($_GET[$dropdown_request_key], $allowed_html));
                                $advanced_drop_value1 = '';
                            } else {
                                $advanced_drop_value = $label;
                                $advanced_drop_value1 = 'all';
                            }
                            $front_name = wpestate_limit45($front_name);
                            $dropdown_get_var = ($dropdown_post_meta_name !== '') ? $dropdown_post_meta_name : $front_name;
                            $dropdown_search_compare = '';
                            if (isset($adv_search_how[$key]) && is_string($adv_search_how[$key])) {
                                $dropdown_search_compare = trim($adv_search_how[$key]);
                            }
                            $return_string = wpestate_build_dropdown_adv_new($appendix, $front_name, $front_name, $advanced_drop_value, $advanced_drop_value1, $dropdown_get_var, $action_select_list, '', $dropdown_search_compare);
                        }
                        $i++;
                    }
                }
                ///////////////////// end dropdown check

                if ($found_dropdown == 0) {
                    //////////////// regular field
                    $return_string = '';
                    if ($position == 'half') {
                        // $return_string.='<div class="col-md-3">';
                        $appendix = '';
                    }

                    $data_search_post_meta = '';
                    if (isset($adv_search_what[$key]) && is_string($adv_search_what[$key])) {
                        $data_search_post_meta = wpestate_map_adv_search_field_to_meta_key($adv_search_what[$key]);
                    }
                    $input_name = ($data_search_post_meta !== '') ? $data_search_post_meta : $slug;
                    $data_search_compare = '';
                    if (isset($adv_search_how[$key]) && is_string($adv_search_how[$key])) {
                        $data_search_compare = trim($adv_search_how[$key]);
                    }
                    $data_search_attrs = ' data-search-post-meta="' . esc_attr($data_search_post_meta) . '" data-search-compare="' . esc_attr($data_search_compare) . '"';

                    $input_id = $appendix . $input_name;
                    if ($adv_search_how[$key] == 'date bigger' || $adv_search_how[$key] == 'date smaller') {
                        $input_id = $term_id . $appendix . $input_name;
                        $return_string .= '<input type="text" id="' . wp_kses($input_id, $allowed_html) . '"  name="' . esc_attr($input_name) . '"' . $data_search_attrs . ' placeholder="' . wp_kses($label, $allowed_html) . '" value="';
                    } else {
                        $return_string .= '<input type="text" id="' . wp_kses($input_id, $allowed_html) . '"  name="' . esc_attr($input_name) . '"' . $data_search_attrs . ' placeholder="' . wp_kses($label, $allowed_html) . '" value="';
                    }

                    if (isset($_GET[$input_name])) {
                        $return_string .= esc_attr($_GET[$input_name]);
                    } elseif (isset($_GET[$slug])) {
                        $return_string .= esc_attr($_GET[$slug]);
                    }
                    $return_string .= '" class="advanced_select form-control" />';

                    if ($position == 'half') {
                        //   $return_string.='</div>';
                    }
                    ////////////////// apply datepicker if is the case
                    if ($adv_search_how[$key] == 'date bigger' || $adv_search_how[$key] == 'date smaller') {
                        wpestate_date_picker_translation($input_id);
                    }
                }
            }
        }
        print trim($return_string);
    }

endif; //






function wpestate_rooms_select_list_simple_dropdown($element,$search_field,$label){
  
    if($element === 'property rooms' || $search_field==='property rooms' ){
        $option='wp_estate_rooms_component_values';
    }else if( $element === 'property bedrooms'   || $search_field==='property bedrooms' ){
        $option='wp_estate_beds_component_values';
    }else if($element === 'property bathrooms'   || $search_field==='property bathrooms' ){
        $option='wp_estate_baths_component_values';
    }

    $component_values     = wpresidence_get_option($option, '');
    $component_values_array = explode(',', $component_values);



    $rooms_select_list = ' <li role="presentation" data-value="all">' . $label . '</li>';
    foreach($component_values_array as $key=>$value){
        $rooms_select_list .= '<li data-value="' . floatval($value) . '"  value="' . floatval($value) . '">' . esc_html($value) . '</li>';
    }

    return $rooms_select_list;



}











if (!function_exists('wpestate_show_search_field_tab_inject')):

    function wpestate_show_search_field_tab_inject($label, $position, $search_field, $action_select_list, $categ_select_list, $select_city_list, $select_area_list, $key, $select_county_state_list) {
        $adv_search_what = wpresidence_get_option('wp_estate_adv_search_what', '');
        $adv_search_label = wpresidence_get_option('wp_estate_adv_search_label', '');
        $adv_search_how = wpresidence_get_option('wp_estate_adv_search_how', '');



        $allowed_html = array();
        if ($position == 'mainform') {
            $appendix = '';
        } else if ($position == 'sidebar') {
            $appendix = 'sidebar-';
        } else if ($position == 'shortcode') {
            $appendix = 'shortcode-';
        } else if ($position == 'mobile') {
            $appendix = 'mobile-';
        } else if ($position == 'half') {
            $appendix = 'half-';
        }

        $return_string = '';
        if ($search_field == 'none') {
            $return_string = '';
        } else if ($search_field == 'property status') {

            if (isset($_GET['property_status'][0]) && $_GET['property_status'] != '' && $_GET['property_status'] != 'all') {
                $full_name = get_term_by('slug', ( ( $_GET['property_status'][0] )), 'property_status');
                $adv_actions_value = $adv_actions_value1 = $full_name->name;
            } else {

                $adv_actions_value = $label;
                if ($label == '') {
                    $adv_actions_value = esc_html__('Property Status', 'wpresidence');
                }
                $adv_actions_value1 = 'all';
            }

            $status_select_list = wpestate_get_status_select_list(wpestate_get_select_arguments());
            $return_string .= wpestate_build_dropdown_adv_new($appendix, 'statuslist', 'adv_status', $adv_actions_value, $adv_actions_value1, 'property_status', $status_select_list);
        } else if ($search_field == 'types') {

            if (isset($_GET['property_action_category'][0]) && $_GET['property_action_category'][0] != '' && $_GET['property_action_category'][0] != 'all') {
                $full_name = get_term_by('slug', ( ( $_GET['property_action_category'][0] )), 'property_action_category');
                $adv_actions_value = $adv_actions_value1 = $full_name->name;
            } else {

                $adv_actions_value = $label;
                if ($label == '') {
                    $adv_actions_value = esc_html__('Types', 'wpresidence');
                }
                $adv_actions_value1 = 'all';
            }

            $return_string .= wpestate_build_dropdown_adv_new($appendix, 'actionslist', 'adv_actions', $adv_actions_value, $adv_actions_value1, 'property_action_category', $action_select_list);
        } else if ($search_field == 'categories') {

            if (isset($_GET['property_category'][0]) && $_GET['property_category'][0] != '' && $_GET['property_category'][0] != 'all') {
                $full_name = get_term_by('slug', esc_html(wp_kses($_GET['property_category'][0], $allowed_html)), 'property_category');
                $adv_categ_value = $adv_categ_value1 = $full_name->name;
            } else {

                $adv_categ_value = $label;
                if ($label == '') {
                    $adv_categ_value = esc_html__('Categories', 'wpresidence');
                }
                $adv_categ_value1 = 'all';
            }
            $return_string = wpestate_build_dropdown_adv_new($appendix, 'categlist', 'adv_categ', $adv_categ_value, $adv_categ_value1, 'property_category', $categ_select_list);
        } else if ($search_field == 'cities') {

            if (isset($_GET['property_city']) && $_GET['property_city'] != '' && $_GET['property_city'] != 'all') {
                $full_name = get_term_by('slug', esc_html(wp_kses($_GET['property_city'], $allowed_html)), 'property_city');
                $advanced_city_value = $advanced_city_value1 = $full_name->name;
            } else {

                $advanced_city_value = $label;
                if ($label == '') {
                    $advanced_city_value = esc_html__('Cities', 'wpresidence');
                }
                $advanced_city_value1 = 'all';
            }
            $return_string = wpestate_build_dropdown_adv_new($appendix, 'adv-search-city', 'property_city', $advanced_city_value, $advanced_city_value1, 'property_city', $select_city_list);
        } else if ($search_field == 'areas') {

            if (isset($_GET['property_area']) && $_GET['property_area'] != '' && $_GET['property_area'] != 'all') {
                $full_name = get_term_by('slug', esc_html(wp_kses($_GET['property_area'], $allowed_html)), 'property_area');
                $advanced_area_value = $advanced_area_value1 = $full_name->name;
            } else {

                $advanced_area_value = $label;
                if ($label == '') {
                    $advanced_area_value = esc_html__('Areas', 'wpresidence');
                }
                $advanced_area_value1 = 'all';
            }
            $return_string = wpestate_build_dropdown_adv_new($appendix, 'adv-search-area', 'property_area', $advanced_area_value, $advanced_area_value1, 'property_area', $select_area_list);
        } else if ($search_field == 'county / state') {

            if (isset($_GET['property_county_state']) && $_GET['property_county_state'] != '' && $_GET['property_county_state'] != 'all') {
                $full_name = get_term_by('slug', esc_html(wp_kses($_GET['property_county_state'], $allowed_html)), 'property_county_state');
                $advanced_county_value = $advanced_county_value1 = $full_name->name;
            } else {

                $advanced_county_value = $label;
                if ($label == '') {
                    $advanced_county_value = esc_html__('States', 'wpresidence');
                }
                $advanced_county_value1 = 'all';
            }
            $return_string = wpestate_build_dropdown_adv_new($appendix, 'adv-search-countystate', 'county-state', $advanced_county_value, $advanced_county_value1, 'property_county_state', $select_county_state_list);
        }

        var_dump($return_string);
        print trim($return_string);
    }

endif; //









if (!function_exists('wpestate_show_search_field')):

    function wpestate_show_search_field($label, $position, $search_field, $action_select_list, $categ_select_list, $select_city_list, $select_area_list, $key, $select_county_state_list, $term_counter_elementor = '', $placeholder = '', $elementor_label = '', $item_field_how = '', $price_array_data = '') {
        $adv_search_what = wpresidence_get_option('wp_estate_adv_search_what', '');
        $adv_search_label = wpresidence_get_option('wp_estate_adv_search_label', '');
        $adv_search_how = wpresidence_get_option('wp_estate_adv_search_how', '');
        $allowed_html = array();
        $args                       =   wpestate_get_select_arguments();
        $position_appendix_map = [
            'mainform'   => '',
            'sidebar'    => 'sidebar-',
            'shortcode'  => 'shortcode-',
            'mobile'     => 'mobile-',
            'half'       => 'half-',
        ];
        
        $appendix = isset($position_appendix_map[$position]) ? $position_appendix_map[$position] : '';
        
       $return_string = '';



        if ($search_field == 'none') {
            $return_string = '';
        } else if ($search_field == 'beds-baths') {
            $return_string .= wpestate_show_beds_baths_component($appendix,$placeholder,$elementor_label, $term_counter_elementor,$position,'active');
        } else if ($search_field == 'property-price-v2') {
            $string = '';
            if ($placeholder != '') {
                $string = wpestate_limit45(sanitize_title($elementor_label)); //is elementor
                $label = $placeholder;
            } else {
                if (isset($adv_search_label[$key])) {
                    $string = wpestate_limit45(sanitize_title($adv_search_label[$key]));
                    $label = $adv_search_label[$key];
                }
            }
            $slug = sanitize_key($string);
            $return_string .= wpestate_show_price_v2_component($appendix,$slug,$label,$placeholder,$elementor_label, $term_counter_elementor,$position,$price_array_data);
        
        }else if ($search_field == 'property-price-v3') {
                $string = '';
                if ($placeholder != '') {
                    $string = wpestate_limit45(sanitize_title($elementor_label)); //is elementor
                    $label = $placeholder;
                } else {
                    if (isset($adv_search_label[$key])) {
                        $string = wpestate_limit45(sanitize_title($adv_search_label[$key]));
                        $label = $adv_search_label[$key];
                    }
                }
                $slug = sanitize_key($string);
           
                if(!is_array($price_array_data)){
                    $price_array_data=array();
                    $price_array_data['min_price_values']      =   wpresidence_get_option('wp_estate_min_price_dropdown_values','');
                    $price_array_data['max_price_values']      =    wpresidence_get_option('wp_estate_max_price_dropdown_values','');
                }
                $return_string .= wpestate_show_price_v3_component($appendix,$slug,$label,$placeholder,$elementor_label, $term_counter_elementor,$position,$price_array_data);
           
        
        } else if ($search_field == 'geolocation') {
            $return_string .= wpestate_show_geolocation_field($appendix,$placeholder,$elementor_label, $term_counter_elementor,$position);
        } else if ($search_field == 'geolocation_radius') {
            $return_string .= wpestate_show_geolocation_radius_field($appendix,$placeholder,$elementor_label, $term_counter_elementor);
        } else if ($search_field == 'wpestate location') {
            $return_string .= wpestate_show_location_field($appendix, $term_counter_elementor);
        } else if ($search_field == 'property status') {
            $return_string .= wpestate_show_dropdown_taxonomy_v21($search_field, $label, $appendix,'active');
        } else if ($search_field == 'types') {
            $return_string .= wpestate_show_dropdown_taxonomy_v21($search_field, $label, $appendix,'active');
        } else if ($search_field == 'categories') {
            $return_string .= wpestate_show_dropdown_taxonomy_v21($search_field, $label, $appendix,'active');
        } else if ($search_field == 'cities') {
             $return_string .= wpestate_show_dropdown_taxonomy_v21($search_field, $label, $appendix,'active');
        } else if ($search_field == 'areas') {
            $return_string .= wpestate_show_dropdown_taxonomy_v21($search_field, $label, $appendix,'active');

        } else if ($search_field == 'county / state') {
            $return_string .= wpestate_show_dropdown_taxonomy_v21($search_field, $label, $appendix,'active');

        } else {

            $show_dropdowns = wpresidence_get_option('wp_estate_show_dropdowns', '');
            $string = '';
            if ($placeholder != '') {
                $string = wpestate_limit45(sanitize_title($elementor_label)); //is elementor
                $label = $placeholder;
            } else {
                if (isset($adv_search_label[$key])) {
                    $string = wpestate_limit45(sanitize_title($adv_search_label[$key]));
                    $label = $adv_search_label[$key];
                }
            }
            $slug = sanitize_key($string);


            if (function_exists('icl_translate')) {
                $label = icl_translate('wpestate', 'wp_estate_custom_search_' . $label, $label);
                if ($placeholder != '') {
                    $label = icl_translate('wpestate', 'wp_estate_custom_search_' . $placeholder, $placeholder); // from elementor
                }
            }

            $adv_search_what_key = '';
            if (isset($adv_search_what[$key])) {
                $adv_search_what_key = $adv_search_what[$key];
            }


            if ($adv_search_what_key == 'property country' || $search_field == 'property country') {
                ////////////////////////////////  show country list
                $return_string = wpestate_country_list_adv_search($appendix, $slug);
            } else if ($adv_search_what_key == 'property price' || $search_field == 'property price') {
                ////////////////////////////////  show price form
                $price_search_compare = '';
                if (isset($adv_search_how[$key]) && is_string($adv_search_how[$key])) {
                    $price_search_compare = trim($adv_search_how[$key]);
                }

                $return_string = wpestate_price_form_adv_search($position, $slug, $label);

                if ( is_array($price_array_data) && isset( $price_array_data['term_id'] ) ) {
                 
                    $return_string = wpestate_price_form_adv_search_with_tabs_elementor($position, $slug, $label, '', $price_array_data['term_id'], $price_array_data['term_slug'], $price_array_data['min_price'], $price_array_data['max_price'], '', $price_search_compare);
                } else {
             
                    $return_string = wpestate_price_form_adv_search($position, $slug, $label);
                }
            } else if ($show_dropdowns == 'yes' && ( $adv_search_what_key == 'property rooms' || $search_field == 'property rooms' || $adv_search_what_key == 'property bedrooms' || $search_field == 'property bedrooms' || $adv_search_what_key == 'property bathrooms' || $search_field == 'property bathrooms')) {
                $i = 0;
                if (function_exists('icl_translate')) {
                    $label = icl_translate('wpestate', 'wp_estate_custom_search_' . $adv_search_label[$key], $adv_search_label[$key]);
                    if ($placeholder != '') {
                        $label = icl_translate('wpestate', 'wp_estate_custom_search_' . $placeholder, $placeholder); // from elementor
                    }
                } else {
                    $label='';
                    if(isset($adv_search_label[$key])){
                        $label = $adv_search_label[$key];
                    }
                    if ($placeholder != '') {
                        $label = $placeholder; // from elementor
                    }
                }
                $rooms_select_list= wpestate_rooms_select_list_simple_dropdown($adv_search_what_key,$search_field, $label);
                $dropdown_search_compare = '';
                if (is_string($item_field_how) && trim($item_field_how) !== '') {
                    $dropdown_search_compare = trim($item_field_how);
                } else if (isset($adv_search_how[$key]) && is_string($adv_search_how[$key])) {
                    $dropdown_search_compare = trim($adv_search_how[$key]);
                }
                $return_string =wpestate_build_dropdown_adv_new($appendix, 'search-' . $slug, $slug, $label, 'all', $slug, $rooms_select_list, 'active', $dropdown_search_compare);
            } else {
                $return_string=''.wpestate_search_generate_custom_field( $adv_search_what_key, $search_field,$label, $adv_search_label, $placeholder, $position, $slug, $allowed_html, $appendix, $item_field_how, $elementor_label,$key);
                
            }
        }
        print trim($return_string);
    }

endif;








if (!function_exists('show_extended_search')):

    /**
     * Render the extended search feature checkbox list.
     *
     * Builds amenity checkboxes from theme options and attaches feature term ids
     * so AJAX filtering can use taxonomy term identifiers directly.
     *
     * @param string $tip     Context suffix used in generated input ids.
     * @param string $usename Additional suffix used in generated input ids.
     *
     * @return void
     */
    function show_extended_search($tip, $usename = '') {
        // Step 1: Render the extended search wrapper shell.
        print '<div class="residence_adv_extended_options_text" >' . esc_html__('More Search Options', 'wpresidence') . '</div>';
        print '<div class="extended_search_check_wrapper">';
        print '<span class="adv_extended_close_button" ><i class="fas fa-times"></i></span>';

        // Step 2: Load configured extended feature slugs from theme options.
        $advanced_exteded = wpresidence_get_option('wp_estate_advanced_exteded', '');
        if(function_exists('wpresidence_redux_advanced_exteded')){

            // Step 3: Load the slug => label map used for checkbox labels.
            $featured_terms = wpresidence_redux_advanced_exteded();


            if (is_array($advanced_exteded)):
                foreach ($advanced_exteded as $slug) {
                    if (isset($featured_terms[$slug])) {
                        // Step 4: Build stable input name/label values.
                        $input_name = str_replace('%', '', $slug);
                        $item_title = $featured_terms[$slug];

                        if ($slug != 'none') {
                            // Step 5: Preserve checked state from request vars.
                            $check_selected = '';
                            if (isset($_GET[$input_name]) && $_GET[$input_name] == '1') {
                                $check_selected = ' checked ';
                            }

                            // Step 6: Match the checkbox to a feature term id.
                            // Step 6.1: First try the original stored slug because non-Latin terms can keep percent-encoded slugs.
                            $feature_term_id = '';
                            $feature_term = get_term_by('slug', $slug, 'property_features');

                            // Step 6.2: Fall back to the rendered checkbox slug for older Latin-style entries.
                            if ((!$feature_term || is_wp_error($feature_term)) && $input_name !== '') {
                                $feature_term = get_term_by('slug', $input_name, 'property_features');
                            }

                            // Step 6.3: If both slug lookups fail, try the visible term name.
                            if ((!$feature_term || is_wp_error($feature_term)) && $item_title !== '') {
                                $feature_term = get_term_by('name', $item_title, 'property_features');
                            }

                            // Step 6.4: Store the resolved term id when a matching term exists.
                            if ($feature_term && !is_wp_error($feature_term)) {
                                $feature_term_id = intval($feature_term->term_id);
                            }

                            // Step 7: Render checkbox + label with data-term-id metadata.
                            print '<div class="extended_search_checker">
                                        <input type="checkbox" id="' . $input_name . $tip . $usename . '" name="' . $input_name . '" name-title="' . $item_title . '" data-search-taxonomy="property_features" data-term-id="' . esc_attr($feature_term_id) . '" value="1" ' . $check_selected . '>
                                        <label for="' . $input_name . $tip . $usename . '">' . esc_html($item_title) . '</label>
                                    </div>';
                        }
                    }
                }
            endif;

        }
        // Step 8: Close extended search wrapper.
        print '</div>';
    }

endif;






////////////////////////////////////////////////////////////////////////////////
/// get select arguments
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('wpestate_get_select_arguments')):

    function wpestate_get_select_arguments() {
        $args = array(
            'hide_empty' => true,
            'hierarchical' => false,
            'pad_counts ' => true,
            'parent' => 0
        );

        $show_empty_city_status = esc_html(wpresidence_get_option('wp_estate_show_empty_city', ''));
        if ($show_empty_city_status == 'yes') {
            $args = array(
                'hide_empty' => false,
                'hierarchical' => false,
                'pad_counts ' => true,
                'parent' => 0
            );
        }
        return $args;
    }

endif;

/**
 * Generates a select list of property status terms with caching
 *
 * This function builds an HTML list of property status terms for dropdown menus
 * in the advanced search feature. Uses WordPress native transient caching
 * for improved performance and includes support for WPML multilingual sites.
 *
 * @param array $args Arguments to pass to get_terms() for retrieving categories
 * @return string HTML markup for the dropdown options list
 * @since 4.0.0
 */
if (!function_exists('wpestate_get_status_select_list')):
    function wpestate_get_status_select_list($args) {
        // Create a transient key with language support for multilingual sites
       
    
     $transient_key = wpestate_set_transient_name_multilang('wpestate_get_status_select_list');
        
        

        // Try to get the cached select list
        $categ_select_list = get_transient($transient_key);
        
        // If no cache exists or it has expired, generate the list
        if ($categ_select_list === false) {
            // Define taxonomy and get terms based on provided arguments
            $taxonomy = 'property_status';
            $categories = get_terms($taxonomy, $args);
            
            // Get search label options from theme settings
            $adv_search_label = wpresidence_get_option('wp_estate_adv_search_label', '');
            $adv_search_what = wpresidence_get_option('wp_estate_adv_search_what', '');
           
            // Get the appropriate label for the dropdown
            $key = '';
            if(is_array($adv_search_what)){
                $key = intval(array_search('property status', $adv_search_what));
            }
            if ($key === '' || $adv_search_label[$key] == '') {
                $label = esc_html__('Statuses', 'wpresidence');
            } else {
                $label = $adv_search_label[$key];
            }
            
            // Generate the default "All" option
            $categ_select_list = ' <li role="presentation" data-value="all">' . $label . '</li>';
            
            // Add each status term to the list
            if (is_array($categories)) {
                foreach ($categories as $categ) {
                    // Get hierarchical children for this term
                    $received = wpestate_hierarchical_category_childen($taxonomy, $categ->term_id, $args);
                    
                    // Calculate total count including children
                    $counter = $categ->count;
                    if (isset($received['count'])) {
                        $counter = $counter + $received['count'];
                    }
                    
                    // Add this term to the list
                    $categ_select_list .= '<li role="presentation" data-value="' . esc_attr($categ->slug) . '" data-term-id="' . intval($categ->term_id) . '">' . ucwords(urldecode($categ->name))  . '</li>';
                    
                    // Add any children terms from the hierarchical function
                    if (isset($received['html'])) {
                        $categ_select_list .= $received['html'];
                    }
                }
            }
            
            // Cache the generated list for 4 hours
            set_transient($transient_key, $categ_select_list, 4 * 60 * 60);
        }
        
        // Return the HTML list (either from cache or freshly generated)
        return $categ_select_list;
    }
endif;
if (!function_exists('wpestate_get_features_select_list')):
    function wpestate_get_features_select_list($args) {
  
        $transient_key = wpestate_set_transient_name_multilang('wpestate_get_features_select_list');
     

        $categ_select_list = get_transient($transient_key);

        if ($categ_select_list === false) {
            $taxonomy = 'property_features';
            $categories = get_terms($taxonomy, $args);

            $label = esc_html__('Features', 'wpresidence');
            $categ_select_list = ' <li role="presentation" data-value="all">' . $label . '</li>';

            if (is_array($categories)) {
                foreach ($categories as $categ) {
                    $received = wpestate_hierarchical_category_childen($taxonomy, $categ->term_id, $args);

                    $counter = $categ->count;
                    if (isset($received['count'])) {
                        $counter = $counter + $received['count'];
                    }

                    $categ_select_list .= '<li role="presentation" data-value="' . esc_attr($categ->slug) . '" data-term-id="' . intval($categ->term_id) . '">' . ucwords(urldecode($categ->name)) . '</li>';

                    if (isset($received['html'])) {
                        $categ_select_list .= $received['html'];
                    }
                }
            }

            set_transient($transient_key, $categ_select_list, 4 * 60 * 60);
        }

        return $categ_select_list;
    }
endif;












/**
 * Generates a select list of property action categories with caching
 *
 * This function builds an HTML list of property action categories for dropdown menus
 * in the advanced search feature. It implements WordPress native transient caching
 * to improve performance and includes support for WPML multilingual sites.
 *
 * @param array $args Arguments to pass to get_terms() for retrieving categories
 * @return string HTML markup for the dropdown options list
 * @since 4.0.0
 * @uses get_terms() To retrieve property action categories
 * @uses wpresidence_get_option() To get theme options for labels
 */
function wpestate_get_action_select_list($args) {
    // Create a transient key with language support for multilingual sites
  
        
    $transient_key = wpestate_set_transient_name_multilang('wpestate_get_action_select_list');
     

    // Try to get the cached select list
    $categ_select_list = get_transient($transient_key);
    
    // If no cache exists or it has expired, generate the list
    if ($categ_select_list === false) {
        // Define taxonomy and get terms based on provided arguments
        $taxonomy = 'property_action_category';
        $categories = get_terms($taxonomy, $args);
        
        // Get search label options from theme settings
        $adv_search_label = wpresidence_get_option('wp_estate_adv_search_label', '');
        $adv_search_what = wpresidence_get_option('wp_estate_adv_search_what', '');
        
        // Generate the default "All Types" option
        $label = wpestate_return_default_label($adv_search_what, $adv_search_label, 'types', esc_html__('Types', 'wpresidence'));
        $categ_select_list = ' <li role="presentation" data-value="all">' . $label . '</li>';
        
        // Add each category to the list
        if (is_array($categories)) {
            foreach ($categories as $categ) {
                // Get hierarchical children for this category
                $received = wpestate_hierarchical_category_childen($taxonomy, $categ->term_id, $args);
                
                // Calculate total count including children
                $counter = $categ->count;
                if (isset($received['count'])) {
                    $counter = $counter + $received['count'];
                }
                
                // Add this category to the list
                $categ_select_list .= '<li role="presentation" data-value="' . esc_attr($categ->slug) . '" data-term-id="' . intval($categ->term_id) . '">' . ucwords(urldecode($categ->name)) . '</li>';
                
                // Add any children categories from the hierarchical function
                if (isset($received['html'])) {
                    $categ_select_list .= $received['html'];
                }
            }
        }
        
        // Cache the generated list for 4 hours
        set_transient($transient_key, $categ_select_list, 24 * 60 * 60);
    }
    
    // Return the HTML list (either from cache or freshly generated)
    return $categ_select_list;
}






////////////////////////////////////////////////////////////////////////////////
/// universal function to get taxonomy dropdown
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('wpestate_get_taxonomy_select_list')):

    function wpestate_get_taxonomy_select_list($args, $taxonomy, $non_option_title) {

        $data_value = array();

        $categories = get_terms($taxonomy, $args);
        $categ_select_list = '<li role="presentation" data-value="all">' . $non_option_title . '</li>';
        if (is_array($categories)) {
            foreach ($categories as $categ) {
                $data_value[] = array('slug' => $categ->slug, 'text' => ucwords(urldecode($categ->name)));
                $counter = $categ->count;
                $received = wpestate_hierarchical_category_childen($taxonomy, $categ->term_id, $args);

                if (isset($received['count'])) {
                    $counter = $counter + $received['count'];
                }

                $categ_select_list .= '<li role="presentation" data-value="' . esc_attr($categ->slug) . '" data-term-id="' . intval($categ->term_id) . '">' . ucwords(urldecode($categ->name)) . ' (' . $counter . ')' . '</li>';
                if (isset($received['html'])) {
                    $categ_select_list .= $received['html'];
                }
            }
        }
        return array('text' => $categ_select_list, 'values' => $data_value);
    }

endif;




function wpestate_return_default_label($adv_search_what, $adv_search_label, $taxonomy, $default_label) {
    $key='';
    if(is_array($adv_search_what)){
            $key = (array_search($taxonomy, $adv_search_what));
    }

    if ($key == '' || $adv_search_label[$key] == '') {
        $label = $default_label;
    } else {
        $label = $adv_search_label[$key];
    }
    return $label;
}



/**
 * Generates a select list of property categories with caching
 *
 * This function builds an HTML list of property categories for dropdown menus
 * in the advanced search feature. Uses WordPress native transient caching
 * for improved performance and includes support for WPML multilingual sites.
 *
 * @param array $args Arguments to pass to get_terms() for retrieving categories
 * @return string HTML markup for the dropdown options list
 * @since 4.0.0
 */
if (!function_exists('wpestate_get_category_select_list')):
    function wpestate_get_category_select_list($args) {
        // Create a transient key with language support for multilingual sites
        $transient_key = wpestate_set_transient_name_multilang('wpestate_get_category_select_list');
        
    
     
        // Try to get the cached select list
        $categ_select_list = get_transient($transient_key);
        
        // If no cache exists or it has expired, generate the list
        if ($categ_select_list === false) {
            // Define taxonomy and get terms based on provided arguments
            $taxonomy = 'property_category';
            $categories = get_terms($taxonomy, $args);
            
            // Get search label options from theme settings
            $adv_search_label = wpresidence_get_option('wp_estate_adv_search_label', '');
            $adv_search_what = wpresidence_get_option('wp_estate_adv_search_what', '');
            
            // Get the appropriate label for the dropdown
            $label = wpestate_return_default_label($adv_search_what, $adv_search_label, 'categories', esc_html__('Categories', 'wpresidence'));
            
            // Generate the default "All" option
            $categ_select_list = '<li role="presentation" data-value="all">' . $label . '</li>';
            
            // Add each category to the list
            if (is_array($categories)) {
                foreach ($categories as $categ) {
                    // Calculate total count
                    $counter = $categ->count;
                    
                    // Get hierarchical children for this category
                    $received = wpestate_hierarchical_category_childen($taxonomy, $categ->term_id, $args);
                    
                    // Add child counts if available
                    if (isset($received['count'])) {
                        $counter = $counter + $received['count'];
                    }
                    
                    // Add this category to the list
                    $categ_select_list .= '<li role="presentation" data-value="' . esc_attr($categ->slug) . '" data-term-id="' . intval($categ->term_id) . '">' . ucwords(urldecode($categ->name)) . '</li>';
                    
                    // Add any children categories from the hierarchical function
                    if (isset($received['html'])) {
                        $categ_select_list .= $received['html'];
                    }
                }
            }
            
            // Cache the generated list for 4 hours
            set_transient($transient_key, $categ_select_list, 4 * 60 * 60);
        }
        
        // Return the HTML list (either from cache or freshly generated)
        return $categ_select_list;
    }
endif;






////////////////////////////////////////////////////////////////////////////////
/// show hieracy categeg
////////////////////////////////////////////////////////////////////////////////
if (!function_exists('wpestate_hierarchical_category_childen')):

    function wpestate_hierarchical_category_childen($taxonomy, $cat, $args, $base = 1, $level = 1) {
        $level++;
        $args['parent'] = $cat;
        $children = get_terms($taxonomy, $args);
        $return_array = array();
        $total_main[$level] = 0;
        $children_categ_select_list = '';
        foreach ($children as $categ) {

            $area_addon = '';
            $city_addon = '';
            $county_addon='';

            if ($taxonomy == 'property_city') {

                $term_meta = get_option("taxonomy_$categ->term_id");

                $string_county = '';
                if (isset($term_meta['stateparent'])) {
                    $string_county = wpestate_limit45(sanitize_title($term_meta['stateparent']));
                }
                $slug_county = sanitize_key($string_county);


                $string = wpestate_limit45(sanitize_title($categ->slug));
                $slug = sanitize_key($string);
                $city_addon = '  data-parentcounty="' . esc_attr($slug_county) . '" data-value2="' . esc_attr($slug) . '" ';
            }

            if ($taxonomy == 'property_county_state') {

               

                $string = wpestate_limit45(sanitize_title($categ->slug));
                $slug = sanitize_key($string);
                $county_addon = '  data-value2="' . esc_attr($slug) . '" ';
            }



            if ($taxonomy == 'property_area') {
                $term_meta = get_option("taxonomy_$categ->term_id");
                $string = wpestate_limit45(sanitize_title($term_meta['cityparent']));
                $slug = sanitize_key($string);
                $area_addon = ' data-parentcity="' . esc_attr($slug) . '" ';
            }

            $hold_base = $base;
            $base_string = '';
            $base++;
            $hold_base = $base;

            if ($level == 2) {
                $base_string = '-';
            } else {
                $i = 2;
                $base_string = '';
                while ($i <= $level) {
                    $base_string .= '-';
                    $i++;
                }
            }


            if ($categ->parent != 0) {
                $received = wpestate_hierarchical_category_childen($taxonomy, $categ->term_id, $args, $base, $level);
            }


            $counter = $categ->count;
            if (isset($received['count'])) {
                $counter = $counter + $received['count'];
            }

            $children_categ_select_list .= '<li role="presentation" data-value="' . esc_attr($categ->slug) . '" data-term-id="' . intval($categ->term_id) . '"  '.$county_addon.' '.$city_addon.' '.$area_addon.'>' . $base_string . ' ' . ucwords(urldecode($categ->name)) . '</li>';

            if (isset($received['html'])) {
                $children_categ_select_list .= $received['html'];
            }

            $total_main[$level] = $total_main[$level] + $counter;

            $return_array['count'] = $counter;
            $return_array['html'] = $children_categ_select_list;
        }
        $return_array['count'] = $total_main[$level];


        return $return_array;
    }

endif;


/**
 * Generates a select list of property cities with caching
 *
 * This function builds an HTML list of property cities for dropdown menus
 * in the advanced search feature. Uses WordPress native transient caching
 * for improved performance and includes support for WPML multilingual sites.
 *
 * @param array $args Arguments to pass to get_terms() for retrieving cities
 * @return string HTML markup for the dropdown options list
 * @since 4.0.0
 */
if (!function_exists('wpestate_get_city_select_list')):
    function wpestate_get_city_select_list($args) {
        // Create a transient key with language support for multilingual sites
        $transient_key=wpestate_set_transient_name_multilang('wpestate_get_city_select_list');
        
        // Try to get the cached select list
        $categ_select_list = get_transient($transient_key);



        // If no cache exists or it has expired, generate the list
        if ($categ_select_list === false) {
            // Define taxonomy and get terms based on provided arguments
            $taxonomy = 'property_city';
            $categories = get_terms($taxonomy, $args);

            // Get search label options from theme settings
            $adv_search_label = wpresidence_get_option('wp_estate_adv_search_label', '');
            $adv_search_what = wpresidence_get_option('wp_estate_adv_search_what', '');
            
            // Get the appropriate label for the dropdown
            $label = wpestate_return_default_label($adv_search_what, $adv_search_label, 'cities', esc_html__('Cities', 'wpresidence'));
            
            // Generate the default "All" option
            $categ_select_list = '<li role="presentation" data-value="all" data-value2="all">' . $label . '</li>';
            
            // Add each city to the list
            if (is_array($categories)) {
                foreach ($categories as $categ) {
                    // Sanitize the slug for use in data attributes
                    $string = wpestate_limit45(sanitize_title($categ->slug));
                    $slug = sanitize_key($string);
                    
                    // Get hierarchical children for this city
                    $received = wpestate_hierarchical_category_childen($taxonomy, $categ->term_id, $args);
                    
                    // Calculate total count including children
                    $counter = $categ->count;
                    if (isset($received['count'])) {
                        $counter = $counter + $received['count'];
                    }
                    
                    // Get parent county information if available
                    $slug_county = '';
                    $term_meta = get_option("taxonomy_$categ->term_id");
                    if (isset($term_meta['stateparent'])) {
                        $string_county = wpestate_limit45(sanitize_title($term_meta['stateparent']));
                        $slug_county = sanitize_key($string_county);
                    }
                    
                    // Add this city to the list with parent county data
                    $categ_select_list .= '<li role="presentation" data-value="' . esc_attr($categ->slug) . '" data-term-id="' . intval($categ->term_id) . '" data-value2="' . esc_attr($slug) . '" data-parentcounty="' . $slug_county . '">' . ucwords(urldecode($categ->name)) . '</li>';
                    
                    // Add any children cities from the hierarchical function
                    if (isset($received['html'])) {
                        $categ_select_list .= $received['html'];
                    }
                }
            }
            
            // Cache the generated list for 4 hours
            set_transient($transient_key, $categ_select_list, 24 * 60 * 60);
        }
        
        // Return the HTML list (either from cache or freshly generated)
        return $categ_select_list;
    }
endif;


/**
 * Generates a select list of property counties/states with caching
 *
 * This function builds an HTML list of property counties/states for dropdown menus
 * in the advanced search feature. Uses WordPress native transient caching
 * for improved performance and includes support for WPML multilingual sites.
 *
 * @param array $args Arguments to pass to get_terms() for retrieving counties/states
 * @return string HTML markup for the dropdown options list
 * @since 4.0.0
 */
if (!function_exists('wpestate_get_county_state_select_list')):
    function wpestate_get_county_state_select_list($args) {
        // Create a transient key with language support for multilingual sites
      
        $transient_key = wpestate_set_transient_name_multilang('wpestate_get_county_state_select_list');
     


        // Try to get the cached select list
        $categ_select_list = get_transient($transient_key);
        
        // If no cache exists or it has expired, generate the list
        if ($categ_select_list === false) {
            // Define taxonomy and get terms based on provided arguments
            $taxonomy = 'property_county_state';
            $categories = get_terms($taxonomy, $args);
            
            // Get search label options from theme settings
            $adv_search_label = wpresidence_get_option('wp_estate_adv_search_label', '');
            $adv_search_what = wpresidence_get_option('wp_estate_adv_search_what', '');
            
            // Get the appropriate label for the dropdown
            $label = wpestate_return_default_label($adv_search_what, $adv_search_label, 'county / state', esc_html__('States', 'wpresidence'));
            
            // Generate the default "All" option
            $categ_select_list = '<li role="presentation" data-value="all" data-value2="all">' . $label . '</li>';
            
            // Add each county/state to the list
            if (is_array($categories)) {
                foreach ($categories as $categ) {
                    // Sanitize the slug for use in data attributes
                    $string = wpestate_limit45(sanitize_title($categ->slug));
                    $slug = sanitize_key($string);
                    
                    // Get hierarchical children for this county/state
                    $received = wpestate_hierarchical_category_childen($taxonomy, $categ->term_id, $args);
                    
                    // Calculate total count including children
                    $counter = $categ->count;
                    if (isset($received['count'])) {
                        $counter = $counter + $received['count'];
                    }
                    
                    // Add this county/state to the list
                    $categ_select_list .= '<li role="presentation" data-value="' . esc_attr($categ->slug) . '" data-term-id="' . intval($categ->term_id) . '" ax data-value2="' . esc_attr($slug) . '">' . ucwords(urldecode($categ->name)) . '</li>';
                    
                    // Add any children counties/states from the hierarchical function
                    if (isset($received['html'])) {
                        $categ_select_list .= $received['html'];
                    }
                }
            }
            
            // Cache the generated list for 4 hours
            set_transient($transient_key, $categ_select_list, 24 * 60 * 60);
        }
        
        // Return the HTML list (either from cache or freshly generated)
        return $categ_select_list;
    }
endif;

/**
 * Generates a select list of property areas with caching
 *
 * This function builds an HTML list of property areas for dropdown menus
 * in the advanced search feature. Uses WordPress native transient caching
 * for improved performance and includes support for WPML multilingual sites.
 *
 * @param array $args Arguments to pass to get_terms() for retrieving areas
 * @return string HTML markup for the dropdown options list
 * @since 4.0.0
 */
if (!function_exists('wpestate_get_area_select_list')):
    function wpestate_get_area_select_list($args) {
        // Create a transient key with language support for multilingual sites
       
         $transient_key = wpestate_set_transient_name_multilang('wpestate_get_area_select_list');
     
    
        // Try to get the cached select list
        $categ_select_list = get_transient($transient_key);
        
        // If no cache exists or it has expired, generate the list
        if ($categ_select_list === false) {
            // Define taxonomy and get terms based on provided arguments
            $taxonomy = 'property_area';
            $categories = get_terms($taxonomy, $args);
            
            // Get search label options from theme settings
            $adv_search_label = wpresidence_get_option('wp_estate_adv_search_label', '');
            $adv_search_what = wpresidence_get_option('wp_estate_adv_search_what', '');
            
            // Get the appropriate label for the dropdown
            $label = wpestate_return_default_label($adv_search_what, $adv_search_label, 'areas', esc_html__('Areas', 'wpresidence'));
            
            // Generate the default "All" option
            $categ_select_list = '<li role="presentation" data-value="all">' . $label . '</li>';
            
            // Add each area to the list
            if (is_array($categories)) {
                foreach ($categories as $categ) {
                    // Get parent city information if available
                    $term_meta = get_option("taxonomy_$categ->term_id");
                    $string = '';
                    if (isset($term_meta['cityparent'])) {
                        $string = wpestate_limit45(sanitize_title($term_meta['cityparent']));
                    }
                    $slug = sanitize_key($string);
                    
                    // Get hierarchical children for this area
                    $received = wpestate_hierarchical_category_childen($taxonomy, $categ->term_id, $args);
                    
                    // Calculate total count including children
                    $counter = $categ->count;
                    if (isset($received['count'])) {
                        $counter = $counter + $received['count'];
                    }
                    
                    // Add this area to the list with parent city data
                    $categ_select_list .= '<li role="presentation" data-value="' . esc_attr($categ->slug) . '" data-term-id="' . intval($categ->term_id) . '" data-parentcity="' . esc_attr($slug) . '">' . ucwords(urldecode($categ->name)) . '</li>';
                    
                    // Add any children areas from the hierarchical function
                    if (isset($received['html'])) {
                        $categ_select_list .= $received['html'];
                    }
                }
            }
            
            // Cache the generated list for 4 hours
            set_transient($transient_key, $categ_select_list, 4 * 60 * 60);
        }
        
        // Return the HTML list (either from cache or freshly generated)
        return $categ_select_list;
    }
endif;

////////////////////////////////////////////////////////////////////////////////
/// show name on saved searches
////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_get_custom_field_name')):

    function wpestate_get_custom_field_name($query_name, $adv_search_what, $adv_search_label) {
        // Step 1: Check the configured custom search fields and try to match
        // the saved-search meta key with the field slug used in the search form.
        if (is_array($adv_search_what) && !empty($adv_search_what)) {
            foreach ($adv_search_what as $key => $term) {
                $term = str_replace(' ', '_', $term);
                $slug = wpestate_limit45(sanitize_title($term));
                $slug = sanitize_key($slug);

                if ($slug == $query_name) {
                    // Step 2: Return the human label when it exists.
                    // If the label array is missing this index, fall back to the
                    // original field name to avoid PHP warnings in saved searches.
                    if (is_array($adv_search_label) && isset($adv_search_label[$key]) && trim((string) $adv_search_label[$key]) !== '') {
                        return $adv_search_label[$key];
                    }

                    return $term;
                }
            }
        }

        // Step 3: Check the extended features list used by checkbox-style fields.
        $advanced_exteded = wpresidence_get_option('wp_estate_advanced_exteded', '');
        if (is_array($advanced_exteded)) {
            foreach ($advanced_exteded as $checker => $value) {
                $post_var_name = str_replace(' ', '_', trim($value));
                $input_name = wpestate_limit45(sanitize_title($post_var_name));
                $input_name = sanitize_key($input_name);
                if ($input_name == $query_name) {
                    return $value;
                }
            }
        }

        // Step 4: Final fallback to the raw query name when no better label exists.
        return $query_name;
    }

endif;

////////////////////////////////////////////////////////////////////////////////
/// get author
////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpsestate_get_author')):

    function wpsestate_get_author($post_id = 0) {
   
        $post = get_post($post_id);
        wp_reset_postdata();
        wp_reset_query();
        if(isset($post->post_author) ){
            return $post->post_author;
        }else{
            return '';
        }
   
    }

endif;

////////////////////////////////////////////////////////////////////////////////
/// show stripe form per listing
////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_show_stripe_form_per_listing')):

    function wpestate_show_stripe_form_per_listing($stripe_class, $post_id, $price_submission, $price_featured_submission) {



        $processor_link = wpestate_get_template_link('stripecharge.php');
        $submission_curency_status = esc_html(wpresidence_get_option('wp_estate_submission_curency', ''));
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $user_email = $current_user->user_email;

        $price_submission_total = $price_submission + $price_featured_submission;
        $price_submission_total = $price_submission_total;
        $price_submission = $price_submission;


        print '<div class="stripe-wrapper ' . $stripe_class . '" id="stripe_form_simple"> ';
        global $wpestate_global_payments;
        $metadata = array(
            'listing_id' => $post_id,
            'user_id' => $userID,
            'featured_pay' => 0,
            'is_upgrade' => 0,
            'pay_type' => 2,
            'message' => esc_html__('Pay Submission Fee', 'wpresidence')
        );

        $wpestate_global_payments->stripe_payments->wpestate_show_stripe_form($price_submission, $metadata,'no_intent');
        print'
    </div>';
    }

endif;



////////////////////////////////////////////////////////////////////////////////
/// show stripe form membership
////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_show_stripe_form_membership')):

    function wpestate_show_stripe_form_membership() {

        $current_user = wp_get_current_user();
        //  get_currentuserinfo();
        $userID = $current_user->ID;
        $user_login = $current_user->user_login;
        $user_email = get_the_author_meta('user_email', $userID);

        $is_stripe_live = esc_html(wpresidence_get_option('wp_estate_enable_stripe', ''));
        if ($is_stripe_live == 'yes') {
            $stripe_secret_key = esc_html(wpresidence_get_option('wp_estate_stripe_secret_key', ''));
            $stripe_publishable_key = esc_html(wpresidence_get_option('wp_estate_stripe_publishable_key', ''));
        }
        $pay_ammout = '0';
        $pack_id = '0';

        $processor_link = wpestate_get_template_link('stripecharge.php');




        print '
        <form action="' . $processor_link . '" method="post" id="stripe_form">';
        wp_nonce_field('wpestate_stripe_payments', 'wpestate_stripe_payments_nonce');

        global $wpestate_global_payments;
        $metadata = array(
            'user_id' => $userID,
            'pay_type' => 3
        );
        $price_submission = '';


        $wpestate_global_payments->stripe_payments->wpestate_show_stripe_form($price_submission, $metadata);


        print'<input type="hidden" id="pack_id" name="pack_id" value="' . $pack_id . '">
            <input type="hidden" id="pack_title" name="pack_title" value="">
            <input type="hidden" name="userID" value="' . $userID . '">
            <input type="hidden" id="pay_ammout" name="pay_ammout" value="' . $pay_ammout . '">';
        print'
        </form>';
    }

endif;




if (!function_exists('wpestate_get_stripe_buttons')):

    function wpestate_get_stripe_buttons($stripe_pub_key, $user_email, $submission_curency_status) {
        wp_reset_query();
        $buttons = '';
        $args = array(
            'post_type' => 'membership_package',
            'meta_query' => array(
                array(
                    'key' => 'pack_visible',
                    'value' => 'yes',
                    'compare' => '=',
                )
            )
        );
        $pack_selection = new WP_Query($args);
        $i = 0;
        while ($pack_selection->have_posts()) {
            $pack_selection->the_post();
            $postid = get_the_ID();

            $pack_price = get_post_meta($postid, 'pack_price', true) * 100;
            $title = get_the_title();
            if ($i == 0) {
                $visible_stripe = " visible_stripe ";
            } else {
                $visible_stripe = '';
            }
            $i++;
            $buttons .= '
                        <div class="stripe_buttons ' . esc_attr($visible_stripe) . ' " id="' . sanitize_title($title) . '">
                            <script src="https://checkout.stripe.com/checkout.js" id="stripe_script"
                            class="stripe-button"
                            data-key="' . esc_attr($stripe_pub_key) . '"
                            data-amount="' . esc_attr($pack_price) . '"
                            data-email="' . esc_attr($user_email) . '"
                            data-currency="' . esc_attr($submission_curency_status) . '"
                            data-zip-code="true"
                            data-locale="auto"
                            data-billing-address="true"
                            data-label="' . esc_html__('Pay with Credit Card', 'wpresidence') . '"
                            data-description="' . esc_attr($title) . ' ' . esc_html__('Package Payment', 'wpresidence') . '">
                            </script>
                        </div>';
        }
        wp_reset_query();
        return $buttons;
    }

endif;





if (!function_exists('wpestate_email_to_admin')):

    function wpestate_email_to_admin($onlyfeatured) {


        $headers = 'From: '.wpestate_return_sending_email() . '>' . "\r\n";
        $message = esc_html__('Hi there,', 'wpresidence') . "\r\n\r\n";

        if ($onlyfeatured == 1) {

            $arguments = array();
            wpestate_select_email_type(get_option('admin_email'), 'featured_submission', $arguments);
        } else {

            $arguments = array();
            wpestate_select_email_type(get_option('admin_email'), 'paid_submissions', $arguments);
        }
    }

endif;



if (!function_exists('wpestate_show_stripe_form_upgrade')):

    function wpestate_show_stripe_form_upgrade($stripe_class, $post_id, $price_submission, $price_featured_submission) {
        $is_stripe_live = esc_html(wpresidence_get_option('wp_estate_enable_stripe', ''));
        if ($is_stripe_live == 'yes') {


            print '<div class="stripe_upgrade">';
            $current_user = wp_get_current_user();
            $userID = $current_user->ID;
            $user_email = $current_user->user_email;
            $submission_curency_status = esc_html(wpresidence_get_option('wp_estate_submission_curency', ''));
            $price_featured_submission = $price_featured_submission;

            global $wpestate_global_payments;
            $metadata = array(
                'listing_id' => $post_id,
                'user_id' => $userID,
                'featured_pay' => 0,
                'is_upgrade' => 1,
                'pay_type' => 2,
                'message' => esc_html__('Upgrade to Featured', 'wpresidence')
            );

            $wpestate_global_payments->stripe_payments->wpestate_show_stripe_form($price_featured_submission, $metadata,'no_intent');
            print '</div>';
        }
    }

endif;




///////////////////////////////////////////////////////////////////////////////////////////
// dasboaord search link
///////////////////////////////////////////////////////////////////////////////////////////



if (!function_exists('wpestate_get_dasboard_searches_link')):

    function wpestate_get_dasboard_searches_link() {
        $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'user_dashboard_search_result.php'
        ));

        if ($pages) {
            $dash_link = esc_url(get_permalink($pages[0]->ID));
        } else {
            $dash_link = esc_url(home_url('/'));
        }

        return $dash_link;
    }

endif; // end













if (!function_exists('wpestate_limit64')):

    function wpestate_limit64($stringtolimit) {
        return mb_substr($stringtolimit, 0, 64);
    }

endif;


if (!function_exists('wpestate_limit54')):

    function wpestate_limit54($stringtolimit) {
        return mb_substr($stringtolimit, 0, 54);
    }

endif;

if (!function_exists('wpestate_limit50')):

    function wpestate_limit50($stringtolimit) { // 14
        return mb_substr($stringtolimit, 0, 50);
    }

endif;

if (!function_exists('wpestate_limit45')):

    function wpestate_limit45($stringtolimit) { // 19
        return mb_substr($stringtolimit, 0, 45);
    }

endif;

if (!function_exists('wpestate_normalize_custom_field_slug')):

    function wpestate_normalize_custom_field_slug($raw_slug) {
        $slug = wpestate_limit45(sanitize_title($raw_slug));
        $slug = sanitize_key($slug);

        return trim($slug);
    }

endif;

if (!function_exists('wpestate_limit27')):

    function wpestate_limit27($stringtolimit) { // 27
        return mb_substr($stringtolimit, 0, 27);
    }

endif;






if (!function_exists('wpestate_show_advanced_search_options_redux')):

    function wpestate_show_advanced_search_options_redux($adv_search_what_value) {
        $return_string = '';

        $admin_submission_array = array('Location' => esc_html('Location', 'wpresidence'),
            'check_in' => esc_html('check_in', 'wpresidence'),
            'check_out' => esc_html('check_out', 'wpresidence'),
            'property_category' => esc_html('First Category', 'wpresidence'),
            'property_action_category' => esc_html('Second Category', 'wpresidence'),
            'property_city' => esc_html('Cities', 'wpresidence'),
            'property_area' => esc_html('Areas', 'wpresidence'),
            'guest_no' => esc_html('guest_no', 'wpresidence'),
            'property_price' => esc_html('Price', 'wpresidence'),
            'property_size' => esc_html('Size', 'wpresidence'),
            'property_rooms' => esc_html('Rooms', 'wpresidence'),
            'property_bedrooms' => esc_html('Bedroms', 'wpresidence'),
            'property_bathrooms' => esc_html('Bathrooms', 'wpresidence'),
            'property_address' => esc_html('Address', 'wpresidence'),
            'property_county' => esc_html('County', 'wpresidence'),
            'property_state' => esc_html('State', 'wpresidence'),
            'property_zip' => esc_html('Zip', 'wpresidence'),
            'property_country' => esc_html('Country', 'wpresidence'),
        );

        foreach ($admin_submission_array as $key => $value) {

            $return_string .= '<option value="' . $key . '" ';
            if ($adv_search_what_value == $key) {
                $return_string .= ' selected="selected" ';
            }
            $return_string .= '>' . $value . '</option>';
        }

        $i = 0;

        $custom_fields = get_option('wpestate_custom_fields_list');

        if (!empty($custom_fields)) {
            while ($i < count($custom_fields['add_field_name'])) {

                $data = wpresidence_prepare_non_latin($custom_fields['add_field_name'][$i], $custom_fields['add_field_label'][$i]);


                $return_string .= '<option value="' . $data['key'] . '" ';
                if ($adv_search_what_value == $data['key']) {
                    $return_string .= ' selected="selected" ';
                }
                $return_string .= '>' . $data['label'] . '</option>';
                $i++;
            }
        }




        $slug = 'none';
        $name = 'none';
        $return_string .= '<option value="' . $slug . '" ';
        if ($adv_search_what_value == $slug) {
            $return_string .= ' selected="selected" ';
        }
        $return_string .= '>' . $name . '</option>';


        return $return_string;
    }

endif; // end   wpestate_show_advanced_search_options


if (!function_exists('wpestate_show_advanced_search_how_redux')):

    function wpestate_show_advanced_search_how_redux($adv_search_how_value) {
        $return_string = '';
        $curent_value = '';

        $admin_submission_how_array = array('equal',
            'greater',
            'smaller',
            'like',
            'date bigger',
            'date smaller');

        foreach ($admin_submission_how_array as $value) {
            $return_string .= '<option value="' . $value . '" ';
            if ($adv_search_how_value == $value) {
                $return_string .= ' selected="selected" ';
            }
            $return_string .= '>' . $value . '</option>';
        }
        return $return_string;
    }

endif; // end   wpestate_show_advanced_search_how


if (!function_exists('wpestate_return_all_fields')):

    function wpestate_return_all_fields($is_mandatory = 0) {

        $submission_page_fields = ( get_option('wp_estate_submission_page_fields', '') );



        $all_submission_fields = $all_mandatory_fields = array(
            'wpestate_description' => esc_html__('Description', 'wpresidence'),
            'property_price' => esc_html__('Property Price', 'wpresidence'),
            'property_label' => esc_html__('Property Price Label', 'wpresidence'),
            'property_label_before' => esc_html__('Property Price Label Before', 'wpresidence'),

            'property_second_price' => esc_html__('Additional Price Info', 'wpresidence'),
            'property_second_price_label' => esc_html__('After Label for Additional Price info', 'wpresidence'),
            'property_label_before_second_price' => esc_html__('Before Label for Additional Price Info', 'wpresidence'),
            'prop_category' => esc_html__('Property Category Submit', 'wpresidence'),
            'prop_action_category' => esc_html__('Property Action Category', 'wpresidence'),
            'attachid' => esc_html__('Property Media', 'wpresidence'),
            'property_address' => esc_html__('Property Address', 'wpresidence'),
            'property_city' => esc_html__('Property City', 'wpresidence'),
            'property_area' => esc_html__('Property Area', 'wpresidence'),
            'property_zip' => esc_html__('Property Zip', 'wpresidence'),
            'property_county' => esc_html__('Property County', 'wpresidence'),
            'property_country' => esc_html__('Property Country', 'wpresidence'),
            'property_map' => esc_html__('Property Map', 'wpresidence'),
            'property_latitude' => esc_html__('Property Latitude', 'wpresidence'),
            'property_longitude' => esc_html__('Property Longitude', 'wpresidence'),
            'google_camera_angle' => esc_html__('Google Camera Angle', 'wpresidence'),
            'property_google_view' => esc_html__('Property Google View', 'wpresidence'),
            'property_hide_map_marker' => esc_html__('Hide Map Marker', 'wpresidence'),
            'property_size' => esc_html__('property Size', 'wpresidence'),
            'property_lot_size' => esc_html__('Property Lot Size', 'wpresidence'),
            'property_rooms' => esc_html__('Property Rooms', 'wpresidence'),
            'property_bedrooms' => esc_html__('Property Bedrooms', 'wpresidence'),
            'property_bathrooms' => esc_html__('Property Bathrooms', 'wpresidence'),
            'owner_notes' => esc_html__('Owner Notes', 'wpresidence'),
            'property_status' => esc_html__('property status', 'wpresidence'),
            'embed_video_id' => esc_html__('Embed Video Id', 'wpresidence'),
            'embed_video_type' => esc_html__('Embed Video Type', 'wpresidence'),
            'embed_virtual_tour' => esc_html__('Embed Virtual Tour/Meta Reels', 'wpresidence'),
            'property_subunits_list' => esc_html__('Property Subunits', 'wpresidence'),
            'energy_class' => esc_html__('Energy Class', 'wpresidence'),
            'energy_index' => esc_html__('Energy Index', 'wpresidence'),
            'co2_class' => esc_html__('Greenhouse gas emissions Class', 'wpresidence'),
            'co2_index' => esc_html__('Greenhouse gas emissions Index', 'wpresidence'),
            'renew_energy_index' => esc_html__('Renewable energy performance index', 'wpresidence'),
            'building_energy_index' => esc_html__('Energy performance of the building', 'wpresidence'),
            'epc_current_rating' => esc_html__('EPC current rating', 'wpresidence'),
            'epc_potential_rating' => esc_html__('EPC Potential Rating', 'wpresidence'),
            'property_internal_id'         =>  esc_html__('Listing ID', 'wpresidence-core'),
            'local_show_hide_price'         =>  esc_html__('Hide/Show Price', 'wpresidence-core'),
        );

        if ($is_mandatory == 1) {
            unset($all_submission_fields['property_subunits_list']);
        }



        $i = 0;

        $custom_fields = wpresidence_get_option('wp_estate_custom_fields', '');
        if (!empty($custom_fields)) {
            while ($i < count($custom_fields)) {
                $name = stripslashes($custom_fields[$i][0]);
                $slug = str_replace(' ', '_', $name);
                if ($is_mandatory == 1) {
                    $slug = str_replace(' ', '-', $name);
                    unset($all_submission_fields['property_map']);
                }
                $label = stripslashes($custom_fields[$i][1]);

                $slug = htmlspecialchars($slug, ENT_QUOTES);

                $all_submission_fields[$slug] = $label;
                $i++;
            }
        }

        $terms =wpestate_get_cached_terms('property_features') ; 
        foreach ($terms as $checker => $term) {
            if (isset($term->slug)) {
                $all_submission_fields[$term->slug] = $term->name;
            }
        }




        return $all_submission_fields;
    }

endif;







if (!function_exists('wpestate_header_phone')):

    function wpestate_header_phone() {
        $return = '';
        $phone_no = wpresidence_get_option('wp_estate_header_phone_no', '');
        if ($phone_no != '') {
            $return = ' <div class="header_phone">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" version="1.1" style="shape-rendering:geometricPrecision;text-rendering:geometricPrecision;image-rendering:optimizeQuality;" viewBox="0 0 295.64 369.5375" x="0px" y="0px" fill-rule="evenodd" clip-rule="evenodd"><defs></defs><g><path class="fil0" d="M231.99 189.12c18.12,10.07 36.25,20.14 54.37,30.21 7.8,4.33 11.22,13.52 8.15,21.9 -15.59,42.59 -61.25,65.07 -104.21,49.39 -87.97,-32.11 -153.18,-97.32 -185.29,-185.29 -15.68,-42.96 6.8,-88.62 49.39,-104.21 8.38,-3.07 17.57,0.35 21.91,8.15 10.06,18.12 20.13,36.25 30.2,54.37 4.72,8.5 3.61,18.59 -2.85,25.85 -8.46,9.52 -16.92,19.04 -25.38,28.55 18.06,43.98 55.33,81.25 99.31,99.31 9.51,-8.46 19.03,-16.92 28.55,-25.38 7.27,-6.46 17.35,-7.57 25.85,-2.85z"/></g></svg>
            <a href="tel:' . $phone_no . '" >' . $phone_no . '</a>
        </div>';
        }
        return $return;
    }

endif;

/*
 * fields for custom unit
 *
 *
 *
 *
 */
if (!function_exists('redux_wpestate_return_custom_unit_fields')):

    function redux_wpestate_return_custom_unit_fields($select_name, $selected_val, $for = '') {

        $all_fields = array(
            'none' => __('Leave Blank', 'wpresidence'),
            'property_category' => __('Category', 'wpresidence'),
            'property_action_category' => __('Action Category', 'wpresidence'),
            'property_city' => __('Property City', 'wpresidence'),
            'property_area' => __('Property Area', 'wpresidence'),
            'property_county_state' => __('Property County/State', 'wpresidence'),
            'second_price' => __('Additional Price', 'wpresidence'),
            'property_status' => __('Property Status', 'wpresidence'),
            'property_year_tax' => __('Yearly Tax Rate', 'wpresidence'),
            'property_hoa' => __('Homeowners Association Fee(Monthly)', 'wpresidence'),
            'property_size' => __('Property Size', 'wpresidence'),
            'property_lot_size' => __('Property Lot Size', 'wpresidence'),
            'property_rooms' => __('Property Rooms', 'wpresidence'),
            'property_bedrooms' => __('Property Bedrooms', 'wpresidence'),
            'property_bathrooms' => __('Property Bathrooms', 'wpresidence'),
            'property_address' => __('Property Address', 'wpresidence'),
            'property_zip' => __('Property Zip', 'wpresidence'),
            'property_country' => __('Property Country', 'wpresidence'),
            'property_internal_id' => __('Listing ID', 'wpresidence'),
            'energy_index' => __('Energy Index in kWh/m2a', 'wpresidence'),
            'energy_class' => __('Energy Class', 'wpresidence'),
            'co2_index' => __('Greenhouse gas emissions kgCO2/m2a', 'wpresidence'),
            'co2_class' => __('Greenhouse gas emissions index class ', 'wpresidence'),
            'renew_energy_index' => __('Renewable energy performance index ', 'wpresidence'),
            'building_energy_index' => __('Energy performance of the building', 'wpresidence'),
            'second_price' => __('Additional Price', 'wpresidence'),
            'epc_current_rating' => __('EPC current rating', 'wpresidence'),
            'epc_potential_rating' => __('EPC potential rating', 'wpresidence'),
        );

        if ($for == '_infobox') {
            unset($all_fields['property_category']);
            unset($all_fields['property_action_category']);
            unset($all_fields['property_price']);
       
        }

        if ($for == '_property') {
            unset($all_fields['property_price']);
        }


        $i = 0;
        $custom_fields = wpresidence_get_option('wpestate_custom_fields_list', '');

        if (!empty($custom_fields)) {
            while ($i < count($custom_fields)) {
                $name  = stripslashes($custom_fields[$i][0]);
                $label = stripslashes($custom_fields[$i][1]);
                $slug  = wpestate_normalize_custom_field_slug($name);

                if ($slug !== '') {
                    $all_fields[$slug] = $label;
                }

                $i++;
            }
        }

        $return_options = '<select id="unit_field_value" name="' . $select_name . '" style="width:170px;">';
        foreach ($all_fields as $key => $checker) {
            $return_options .= '<option value="' . $key . '" ';
            if ($key === htmlspecialchars(stripslashes($selected_val), ENT_QUOTES)) {
                $return_options .= ' selected ';
            }
            $return_options .= '>' . $checker . '</option>';
        }
        $return_options .= '</select>';
        return $return_options;
    }

endif;


add_action('wp_ajax_wpestate_create_payment_intent_stripe', 'wpestate_create_payment_intent_stripe');
if (!function_exists('wpestate_create_payment_intent_stripe')):
    function wpestate_create_payment_intent_stripe(){

        $current_user               =   wp_get_current_user();
        $userID                     =   $current_user->ID;
        $listingid                  =   intval($_POST['listingid']);
    
        $isfeatured                 =   intval($_POST['isfeatured']);

        global $wpestate_global_payments;
        if($isfeatured==1){
            $metadata = array(
                'listing_id' => $listingid,
                'user_id' => $userID,
                'featured_pay' => 0,
                'is_upgrade' => 1,
                'pay_type' => 2,
                'message' => esc_html__('Upgrade to Featured', 'wpresidence')
            );
            $price_featured_submission  =   floatval(wpresidence_get_option('wp_estate_price_featured_submission', ''));
            $wpestate_global_payments->stripe_payments->wpestate_create_simple_intent($price_featured_submission, $metadata);

        }else{
            $price_submission           =   floatval(wpresidence_get_option('wp_estate_price_submission', ''));
            $metadata = array(
                'listing_id' => $listingid,
                'user_id' => $userID,
                'featured_pay' => 0,
                'is_upgrade' => 0,
                'pay_type' => 2,
                'message' => esc_html__('Pay Submission Fee', 'wpresidence')
            );
            $wpestate_global_payments->stripe_payments->wpestate_create_simple_intent($price_submission, $metadata);
        }
  
        die();
    

    }
endif;

if ( !function_exists('wpestate_allow_script_tags') ):
/**
 * Allow script and style tags in post content
 *
 * This function modifies the allowed HTML tags in post content to include
 * script and style tags with specific attributes.
 *
 * @param array $allowedposttags The current allowed HTML tags.
 * @return array Modified allowed HTML tags.
 */
function wpestate_allow_script_tags( $allowedposttags ){
    $allowedposttags['script'] = array(
        'src' => true,
        'height' => true,
        'width' => true,
        'charset' => true,
        'async' =>true
    );
    $allowedposttags['style'] = array(
        'type' => true,
        'media' => true,
        'href' => true,
        'rel' => true,
    );
    return $allowedposttags;
}
endif;


