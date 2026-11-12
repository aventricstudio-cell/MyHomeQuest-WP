<?php
/**
 * Get the featured image URL for a property
 *
 * This function retrieves the featured image URL for a given property.
 * If no featured image is set or if it's empty, it returns a default image URL.
 *
 * @param int    $property_id The ID of the property
 * @param string $size        The size of the image to retrieve (default: 'full')
 * @return string The URL of the featured image or default image
 */
function wpestate_get_property_featured_image($property_id, $size = 'full') {
    // Get the post thumbnail ID
    $attchment_id = get_post_thumbnail_id($property_id);

    return wpestate_get_property_image_src($attchment_id, $size);
    
}

/**
 * Get the  image URL for a property
 *
 * This function retrieves the  image URL for a given property.
 * If no featured image is set or if it's empty, it returns a default image URL.
 *
 * @param int    $attchment_id The ID of the image
 * @param string $size        The size of the image to retrieve (default: 'full')
 * @return string The URL of the featured image or default image
 */


function wpestate_get_property_image_src($attchment_id, $size = 'full') {

    // Get the image source
    $image_src = wp_get_attachment_image_src($attchment_id, $size);

    // Check if the image exists and is not empty
    if ($image_src && !empty($image_src[0])) {
        return esc_url($image_src[0]);
    }

    // Return the default image if no featured image is found
    return esc_url(get_theme_file_uri('/img/defaults/default_property_featured.jpg'));
}


/**
 * WpResidence Property List Display
 *
 * This file contains functions for displaying property listings in various contexts
 * within the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyListings
 * @since 1.0.0
 */

if (!function_exists('wpresidence_display_property_list_as_html')):
    /**
     * Display property list as HTML
     *
     * This function generates the HTML output for a list of properties based on
     * the provided WP_Query object and various options.
     *
     * @param WP_Query $prop_selection The WP_Query object containing the properties to display
     * @param array $wpestate_options Additional options for customizing the display
     * @param string $context The context in which the properties are being displayed
     * @param array $shortcode_attributes Attributes passed from a shortcode, if applicable
     * @param array $pagination Pagination settings
     */
    function wpresidence_display_property_list_as_html($prop_selection, $wpestate_options = array(), $context = '', $shortcode_attributes = array(), $pagination = array()) {
        
       
        //print 'in wpresidence_display_property_list_as_html '.PHP_EOL;


        // Fetch property card context data like card type, items per row, align, etc.
        $wpresidence_property_cards_context = wpresidence_return_property_cards_context($wpestate_options,$context, $shortcode_attributes);

        // Determine the appropriate message to display when no properties are found
        $no_listing_message = wpresidence_get_no_listing_message($context);

        // Determine if grid display is enabled
        $display_grid = isset($shortcode_attributes['display_grid']) && $shortcode_attributes['display_grid'] === 'yes' ? 'yes' : 'no';

        // Check if there are properties to display
        if ($prop_selection->have_posts()) {
            while ($prop_selection->have_posts()) : $prop_selection->the_post();
                $postID = get_the_ID();
                if(function_exists('wpestate_api_get_cached_post_data')){
                    $property_unit_cached_data = wpestate_api_get_cached_post_data($postID, 'estate_property');
                }else{
                    $property_unit_cached_data =array();
                }
               
                if ($display_grid === 'yes') {
                    echo '<div class="shortcode_wrapper_grid_item">';
                }

                // Include the appropriate property unit template
                include(locate_template('templates/property_cards_templates/property_unit' . $wpresidence_property_cards_context['property_card_type_string'] . '.php'));

                if ($display_grid === 'yes') {
                    echo '</div>';
                }
            endwhile;

            // Display pagination if enabled
            if (isset($pagination['display']) && $pagination['display'] === 'yes') {
                wpresidence_display_pagination($context, $prop_selection, $pagination);
            }

       
        } else {
            // Display message when no properties are found
            if (!in_array($context, ['related_listings', 'agent_listings'])) {
                echo '<span class="no_results">' . esc_html($no_listing_message) . '</span>';
            }
        }

        wp_reset_postdata();
        wp_reset_query();
    }

    
endif;







/**
 * Get the appropriate message for when no listings are found
 *
 * @param string $context The context in which the properties are being displayed
 * @return string The message to display
 */
function wpresidence_get_no_listing_message($context) {
    $messages = [
        'property_list' => esc_html__('There are no properties listed on this page at this moment. Please try again later.', 'wpresidence'),
        'search_list' => esc_html__('We didn\'t find any results. Please try again with different search parameters.', 'wpresidence'),
        'directory_list' => esc_html__('There are no properties listed on this page at this moment. Please try again later.', 'wpresidence'),
        'shortcode_list' => esc_html__('There are no more listings.', 'wpresidence'),
        'ajax_search_list' => esc_html__('We didn\'t find any result', 'wpresidence'),
        'ajax_search_list' => esc_html__('We didn\'t find any result', 'wpresidence'),
        'term_ajax_listing' => esc_html__('We didn\'t find any result', 'wpresidence'),
    ];

    return $messages[$context] ?? $messages['property_list'];
}






/**
 * Display pagination for property listings
 *
 * @param string $context The context in which the properties are being displayed
 * @param WP_Query $prop_selection The WP_Query object containing the properties
 * @param array $pagination Pagination settings
 */
function wpresidence_display_pagination($context, $prop_selection, $pagination) {
    if ($context === 'ajax_search_list') {
        wpestate_pagination_ajax($prop_selection->max_num_pages, 2, $pagination['paged'], 'pagination_ajax_search');
    } elseif ($context === 'ajax_list' || $context === 'ajax_list_shortcode') {
        wpestate_pagination_ajax_newver($prop_selection->max_num_pages, 2, $pagination['paged'], 'pagination_ajax', $pagination['order']);
    }
}


  // Display message when no properties are found
        //   <h4 class="nothing">
        // <?php print esc_html($no_listing_message); 
        //</h4>









/**
 * Unit selector property
 *
 * @param array $shortcode_attributes Shortcode attributes
 * @return array Context data for property cards
 */
if (!function_exists('wpresidence_return_property_cards_context')):
    function wpresidence_return_property_cards_context($wpestate_options = array(),$context_html='',$shortcode_attributes = array() ) {
        $context = array();

        // Display property as grid or list
        $context['wpestate_uset_unit'] = intval(wpresidence_get_option('wpestate_uset_unit', ''));

        // No of property listings per row when the page is without sidebar
        $context['wpestate_no_listins_per_row'] = intval(wpresidence_get_option('wp_estate_listings_per_row', ''));

        // Custom unit structure
        $context['wpestate_custom_unit_structure'] = wpresidence_get_option('wpestate_property_unit_structure');

        // Get currency symbol
        $context['wpestate_currency'] = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));

        // Get position of currency symbol
        $context['where_currency'] = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));

        // Check if we show slider in property card
        $context['wpestate_property_unit_slider'] = wpresidence_get_option('wp_estate_prop_list_slider', '');

        //No of images in slider 
        $context['wp_estate_prop_list_slider_image_number'] = wpresidence_get_option('wp_estate_prop_list_slider_image_number', '');

        // Open in new page option
        $context['new_page_option'] = wpresidence_get_option('wp_estate_unit_card_new_page', '');

        // Get the column class for property unit
        $context['property_unit_class'] = wpestate_return_unit_class($wpestate_options, $context_html, $shortcode_attributes);


        // Determine if grid display is enabled
        if ( isset($shortcode_attributes['display_grid']) && $shortcode_attributes['display_grid'] === 'yes' ){
            $context['property_unit_class']['col_class']='';
            $context['property_unit_class']['col_org']='';
        }else{
            $context['property_unit_class']['col_class'].=' col-12 col-sm-6 col-md-6  ';
            
        }



        // Get user favorites
        $context['curent_fav'] = wpestate_return_favorite_listings_per_user();

        // Determine property card type
        if (isset($shortcode_attributes['card_version']) && ($shortcode_attributes['card_version'] != '')) {
            // If we have a card version set in the shortcode
            $context['property_card_type_string'] = ($shortcode_attributes['card_version'] == 0) ? '' : '_type' . $shortcode_attributes['card_version'];
        } else {
            // If not, we use the global version
            $property_card_type = intval(wpresidence_get_option('wp_estate_unit_card_type'));
            $context['property_card_type_string'] = ($property_card_type == 0) ? '' : '_type' . $property_card_type;
        }

        return $context;
    }
endif;


/**
*
*   Unit selector property
*
* 
*
*/

if(!function_exists('wpestate_return_unit_class')):
    function wpestate_return_unit_class($wpestate_options=array(),$context='',$shortcode_params=''){

        

    
        // Get the number of blog listings per row from theme options
        // The second parameter '' provides a default value if the option is not set
        $wpestate_no_listins_per_row =   $wpestate_no_listins_per_row_for_switch= intval(wpresidence_get_option('wp_estate_listings_per_row', ''));


        // test to check if we have property list full width or not
        $wp_estate_prop_unit= (wpresidence_get_option('wp_estate_prop_unit', ''));
        if($wp_estate_prop_unit=='list'){   
            $wpestate_no_listins_per_row=1;
        }

     

        // if we have similar posts
        if($context=='similar' || $context=='related_listings'){
            // Get the option for similar blog post count (default to 3 if not set)
            $wpestate_no_listins_per_row = intval(wpresidence_get_option('wp_estate_similar_prop_per_row', '',3));
            if(isset($shortcode_params['post_number_per_row'])){
                $wpestate_no_listins_per_row=$shortcode_params['post_number_per_row'];
            }

            if (wpresidence_get_option('wp_estate_unit_md_similar') === "list") {
                $wpestate_no_listins_per_row=1;
                $wp_estate_prop_unit='list';
            }
        
        }else if($context=='shortcode' || $context=='shortcode_list' || $context=='ajax_list_shortcode' || $context=='term_ajax_listing'){
            // if we have shortcode  or builder blogs get the number of listings per row from shortcode params
      
            if(isset($shortcode_params['align']) && $shortcode_params['align']=='horizontal'){
                // if we have horizontal align we need to set the number of listings per row to 1
                $wpestate_no_listins_per_row=1;
   
            }else if(isset($shortcode_params['rownumber'])){
             
                $wpestate_no_listins_per_row=$shortcode_params['rownumber'];
            }
         
        }else if($context=='shortcode_slider_list'){
            if(isset($shortcode_params['rownumber'])){
                $wpestate_no_listins_per_row=$shortcode_params['rownumber'];
            }
        }
       

        // Further adjustment if content class is 'col-lg-12' or full width
        if(isset($wpestate_options['content_class']) && 
            !str_contains($wpestate_options['content_class'], 'col-lg-12') && 
            $wp_estate_prop_unit!=='list' &&
            $context!=='term_ajax_listing'){   // make sure we are not substract from shortcocde 
                 
                $wpestate_no_listins_per_row--;
                
        }

        // Further adjustment if page is half map
        global $post;
        $postID='';
        if(isset($post->ID) && !is_category() && !is_tax()  ){
            $postID= $post->ID;
        }


        if (is_page_template('page-templates/property_list_half.php')  || wpestate_half_map_conditions($postID) ) {
            if($wp_estate_prop_unit!=='list'){
                $wpestate_no_listins_per_row--;
            }
        }

        // force maxim 6 columns
        if ($wpestate_no_listins_per_row > 6) {
            $wpestate_no_listins_per_row = 6;
        }   




        // setting up the class for the property unit when swithc from list to grid
        if(isset($wpestate_options['content_class']) && 
            !str_contains($wpestate_options['content_class'], 'col-lg-12') && 
            $wp_estate_prop_unit==='list'){
                $wpestate_no_listins_per_row_for_switch--;
        }

        if ($wpestate_no_listins_per_row_for_switch==2){
            $wpestate_no_listins_per_row_for_switch=6;
        }else if($wpestate_no_listins_per_row_for_switch==3){
            $wpestate_no_listins_per_row_for_switch=4;
        }else if($wpestate_no_listins_per_row_for_switch==4){
            $wpestate_no_listins_per_row_for_switch=3;
        } 




        $options = array(
            '1'=> array(
                'col_class' =>  'col-lg-12 col-md-12 ',
                'col_org'   =>  $wpestate_no_listins_per_row_for_switch
            ) ,
            
            '2'=> array(
                    'col_class' =>  'col-lg-6',
                    'col_org'   =>  6
            ),   

            '3'=> array(
                    'col_class' =>  'col-lg-4',
                    'col_org'   =>  4
            ), 
                
            '4'=> array(
                    'col_class' =>  'col-lg-3',
                    'col_org'   =>  3
            ), 
            '5'=> array(
                'col_class' =>  'col-lg-3',
                'col_org'   =>  3
            ), 

            '6'=> array(
                'col_class' =>  'col-lg-2',
                'col_org'   =>  2
             ), 
           
        );


        return $options[$wpestate_no_listins_per_row];

    }

endif;


























function wpresidence_create_arguments_for_property_list($propertyListPageID) {
    // Retrieve filter values from post meta
    $filter_values = array(
        'adv_filter_search_action'  => get_post_meta($propertyListPageID, 'adv_filter_search_action', true),
        'adv_filter_search_category'=> get_post_meta($propertyListPageID, 'adv_filter_search_category', true),
        'current_adv_filter_area'   => get_post_meta($propertyListPageID, 'current_adv_filter_area', true),
        'current_adv_filter_city'   => get_post_meta($propertyListPageID, 'current_adv_filter_city', true),
        'current_adv_filter_county' => get_post_meta($propertyListPageID, 'current_adv_filter_county', true),
        'show_featured_only'        => get_post_meta($propertyListPageID, 'show_featured_only', true),
        'show_filter_area'          => get_post_meta($propertyListPageID, 'show_filter_area', true),
    );

    // Get listing order
    $order = get_post_meta($propertyListPageID, 'listing_filter', true);
    
    // Override order if set in GET parameters
    if (isset($_GET['order']) && is_numeric($_GET['order'])) {
        $order = intval($_GET['order']);
    }

    // Get number of properties to show
    $property_number_to_show = intval(wpresidence_get_option('wp_estate_prop_no', ''));

    // Determine current page
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    if (is_front_page()) {
        $paged = (get_query_var('page')) ? get_query_var('page') : 1;
    }

    // Initialize meta query and transient appendix
    $meta_query = array();
    $transient_appendix='';

    // Add featured properties filter if needed
    if ($filter_values['show_featured_only'] == 'yes') {
        $meta_query[] = array(
            'key'     => 'prop_featured',
            'value'   => 1,
            'type'    => 'numeric',
            'compare' => '='
        );
        $transient_appendix .= '_show_featured';
    }

    // Set up taxonomy arguments
    $tax_arguments = array(
        'property_action_category' => $filter_values['adv_filter_search_action'],
        'property_category'        => $filter_values['adv_filter_search_category'],
        'property_city'            => $filter_values['current_adv_filter_city'],
        'property_area'            => $filter_values['current_adv_filter_area'],
        'property_county_state'    => $filter_values['current_adv_filter_county'],
    );

    // Construct final query arguments
    $temp_arguments = array(
        'post_type'      => 'estate_property',
        'post_status'    => 'publish',
        'order'          => $order,
        'paged'          => $paged,
        'posts_per_page' => $property_number_to_show,
        'tax_arguments'  => $tax_arguments,
        'meta_query'     => $meta_query
    );

    return $temp_arguments;
}



/**
 * Helper function to generate excerpt HTML
 *
 * @param int $length The maximum length of the excerpt
 * @return string The HTML for the excerpt
 */
if (!function_exists('wpresidence_unit_card_generate_excerpt_html')) :
    function wpresidence_unit_card_generate_excerpt_html($length, $postID) {
        // Check if post has an excerpt, fallback to empty string if not
        $post_content = get_post_field('post_content', $postID);
        
        if (!empty($post_content)) {
            $processed_content = do_shortcode($post_content);
            $the_excerpt = wp_trim_words($processed_content, 55);
        } else {
            $the_excerpt = '';
        }
 
        if ($length > 0) {
            return wpestate_strip_excerpt_by_char($the_excerpt, $length, $postID);
        } else {
            return $the_excerpt;
        }
    }
endif;


/**
 * Helper function to generate excerpt HTML using cache data with fallback
 *
 * @param int   $length                    The maximum length of the excerpt
 * @param array $property_unit_cached_data The cached property data array
 * @param int   $propID                    The property post ID
 * @return string The HTML for the excerpt
 */
if (!function_exists('wpresidence_unit_card_generate_excerpt_html_from_cache')) :
    function wpresidence_unit_card_generate_excerpt_html_from_cache($length, $property_unit_cached_data, $propID) {
        // Get description using cache function with fallback
        $post_content = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $propID, '', 'description');
       
        if (!empty($post_content)) {
            $processed_content = do_shortcode($post_content);
            $the_excerpt = wp_trim_words($processed_content, 55);
        } else {
            $the_excerpt = '';
        }
 
        if ($length > 0) {
            return wpestate_strip_excerpt_by_char_from_cache($the_excerpt, $length, $property_unit_cached_data, $propID);
        } else {
            return $the_excerpt;
        }
    }
endif;


    
    
    
/*
*
* Return card content
*
*/
if(!function_exists('wpestate_card7_call_content')):
    function wpestate_card7_call_content($postID,$realtor_details ){
        $wp_estate_call_text_unit7= wpresidence_get_option('wp_estate_call_text_unit7','');
        $replace=array(
            'property_id'                =>  $postID,
            'title'             =>  wpresidence_get_sanitized_truncated_title($postID,0),
            'realtor_name'      =>  $realtor_details['realtor_name'],
            'realtor_phone'     =>  $realtor_details['realtor_phone'],
            'realtor_mobile'    =>  $realtor_details['realtor_mobile'],
            
        );


        foreach($replace as $key=>$value):
            $wp_estate_call_text_unit7=str_replace('%'.$key, $value,$wp_estate_call_text_unit7);
        endforeach;

        return  $wp_estate_call_text_unit7;


    }
endif;

    
/*
*
* Return card content
*
*/

if(!function_exists('wpestate_return_property_card_content')):
    function wpestate_return_property_card_content($postID,$property_unit_cached_data,$wpresidence_property_cards_context,$unit_type=''){
        $wp_estate_property_card_rows = wpresidence_get_option('wp_estate_property_card_rows', '');
        $wp_estate_card_details_text_align =wpresidence_get_option('wp_estate_card_details_text_align','');
   

        unset($wp_estate_property_card_rows['enabled'] ['placebo']);
        unset($wp_estate_property_card_rows['disabled'] ['placebo']);
        print '<div class="wpresidence_card_composer_wrapper_align_'.esc_attr($wp_estate_card_details_text_align).'">';
            foreach ($wp_estate_property_card_rows['enabled'] as $key=>$value):
                switch ($key) {
                    case 'title':
                        include( locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_title.php'));
                        break;
                    
                    case 'price':
                        include( locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_price.php') );
                        break;

                    case 'second_price':
                        include( locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_second_price.php') );
                        break;

                    case 'excerpt':
                        include( locate_template('templates/property_cards_templates/property_card_details_templates/property_card_content.php'));
                        break;   

                    case 'details':
                        print  wpestate_return_property_card_details($postID);
                        break;

                    case 'address':
                        print wpestate_return_property_card_address($postID);
                        break;
                            
                    case 'categories':
                        print  wpestate_return_property_card_categories($postID);
                        break;

                    case 'contact':
                        if($unit_type!==7):
                            include( locate_template( 'templates/property_cards_templates/property_card_details_templates/property_card_contact.php') );
                        endif;
                        break;
                    
                    case 'mlsdata':
                            print  wpestate_return_property_card_mls_data($postID);
                            break;    
                        
                        
                }
            endforeach;
        print '</div>';



    }
endif;

    
    
/*
*
* Return card details content
*
*/

if(!function_exists('wpestate_return_property_card_details')):
    function wpestate_return_property_card_details($postID,$unit_type=''){
        $wp_estate_property_card_rows_details = wpresidence_get_option('wp_estate_property_card_rows_details', '');
        $return_string = '<div class="property_listing_details_v2">';

        $i=0;
        while($i<5):
        
            if( $wp_estate_property_card_rows_details['unit_field_value'][$i] !='none' ){
            

                $value_to_show = wpestate_return_property_card_details_value_to_show($wp_estate_property_card_rows_details['unit_field_value'][$i],$postID);

                if($value_to_show!='' || ( is_numeric($value_to_show) && floatval($value_to_show)!=0)  ){
                    $return_string .= '<div class="property_listing_details_v2_item">';
                        $return_string .= '<div class="icon_label">';
                            if( $wp_estate_property_card_rows_details['property_unit_field_name'][$i]!=''   ){
                                $return_string .=   esc_html( $wp_estate_property_card_rows_details['property_unit_field_name'][$i] );
                            }else{
                                if(  $wp_estate_property_card_rows_details['property_unit_field_icon'][$i]!='' ){
                                    $return_string .=  '<i class="'.esc_attr($wp_estate_property_card_rows_details['property_unit_field_icon'][$i] ).'"></i>';
                                }else if( $wp_estate_property_card_rows_details['property_unit_field_image'][$i]!='' ){
                                    $return_string .= '<img src="'. $wp_estate_property_card_rows_details['property_unit_field_image'][$i].'" >'; 
                                }
                            }  
                        $return_string .= '</div>';
                        $return_string .=  $value_to_show;
                    $return_string .= '</div>';
                }



            }

        
            $i++;
        endwhile;

        $return_string .= '</div>';

        return $return_string;
    }
endif;
    
    
    
    
    
    
    
/*
*
* Return detail value
*
*/

if(!function_exists('wpestate_return_property_card_details_value_to_show')):
    function wpestate_return_property_card_details_value_to_show($field,$postID){

        $value='';
        if(
            $field=='property_category' ||
            $field=='property_action_category' ||
            $field=='property_city' ||
            $field=='property_area' ||
            $field=='property_county_state' ||
            $field=='property_status'   ){
                $value = get_the_term_list($postID, $field, '', ', ', '');

        }else if(
            $field=='property_size' ||
            $field=='property_lot_size'  ){
            $value = wpestate_get_converted_measure( $postID,$field );

        }else if ( $field=='second_price' ){

            $wpestate_currency = wpresidence_get_option('wp_estate_currency_symbol', '');
            $where_currency    = wpresidence_get_option('wp_estate_where_currency_symbol', '');

            $second_price      = floatval( get_post_meta( $postID, 'property_second_price', true ) );
            $label_after       = esc_html( get_post_meta( $postID, 'property_second_price_label', true ) );
            $label_before      = esc_html( get_post_meta( $postID, 'property_label_before_second_price', true ) );

            if ( $second_price != 0 ) {
                $value = wpestate_show_price( $postID, $wpestate_currency, $where_currency, 1, 'yes' );
            } else {
                $value = '';
                if ( $label_before !== '' ) {
                    $value .= $label_before . ' ';
                }
                $value .= $label_after;
            }

        }else{
            $value = get_post_meta($postID, $field, true);

            if ($value === '' && function_exists('wpestate_normalize_custom_field_slug')) {
                $normalized_field = wpestate_normalize_custom_field_slug($field);

                if ($normalized_field !== '' && $normalized_field !== $field) {
                    $value = get_post_meta($postID, $normalized_field, true);
                }
            }
        }

        return $value;
    }
endif;

    
    
    
    
    
/*
*
* Return address field
*
*/

if(!function_exists('wpestate_return_property_card_address')):
    function wpestate_return_property_card_address($postID){
        $wp_estate_property_card_rows_address = wpresidence_get_option('wp_estate_property_card_rows_address', '');

        unset($wp_estate_property_card_rows_address['enabled']['placebo']);
        $address_to_show    =   '';
        $separator          =   ', ';

        
        foreach ( $wp_estate_property_card_rows_address['enabled'] as $key=>$value ):
            switch ($key) {
                case 'property_address':
                    $property_address   =   get_post_meta($postID,'property_address',true);
                    if($property_address!=''){
                        $address_to_show    =   $address_to_show.$property_address.$separator;
                    }
                    break;                
                
                case 'property_country':
                    $property_country   =   get_post_meta($postID,'property_country',true);
                    if($property_country!=''){
                        $address_to_show    =   $address_to_show.$property_country.$separator;
                    }
                    
                    break; 

                case 'property_zip':                 
                    $property_zip   =   get_post_meta($postID,'property_zip',true);
                    if($property_zip!=''){
                        $address_to_show    =   $address_to_show.$property_zip.$separator;
                    }
                    break;                

                case 'property_city':
                    $property_city      =   get_the_term_list($postID, 'property_city', '', ', ', '') ;
                    if( $property_city !=''){
                        $address_to_show    =   $address_to_show.$property_city.$separator;
                    }
                    break;
                
                case 'property_county_state':
                    $property_county_state      =   get_the_term_list($postID, 'property_county_state', '', ', ', '') ;
                    if($property_county_state!=''){
                        $address_to_show            =   $address_to_show.$property_county_state.$separator;
                    }
                    break;
                
                case 'property_area':
                    $property_area      =   get_the_term_list($postID, 'property_area', '', ', ', '');
                    if($property_area!=''){
                        $address_to_show    =   $address_to_show.$property_area.$separator;
                    }                   
                    break;
            }

        endforeach;

        $return_address_to_show='<div class="property_card_categories_wrapper">'.rtrim($address_to_show,$separator).'</div>';
        return $return_address_to_show;


    }
endif;
    
    
    
/*
*
* Return categories 
*
*/


if(!function_exists('wpestate_return_property_card_categories')):
    function wpestate_return_property_card_categories($postID,$separator_type=''){
        $wp_estate_property_card_rows_categories = wpresidence_get_option('wp_estate_property_card_rows_categories', '');
    
        unset($wp_estate_property_card_rows_categories['enabled']['placebo']);
        $categories_to_show    =   '';
        $separator          =   ', ';
        if($separator_type==1){
            $separator          =   ' '.trim('&#183;').' ';
        }


        foreach ( $wp_estate_property_card_rows_categories['enabled'] as $key=>$value ):
            switch ($key) {
                case 'property_category':
                    $property_category      =   get_the_term_list($postID, 'property_category', '', $separator, '');
                    if($property_category!=''){
                        $categories_to_show    =   $categories_to_show.$property_category.$separator;
                    }                   
                    break;

                case 'property_action_category':
                    $property_action_category      =   get_the_term_list($postID, 'property_action_category', '', $separator, '');
                    if($property_action_category!=''){
                        $categories_to_show    =   $categories_to_show.$property_action_category.$separator;
                    }                   
                    break;

                case 'property_status':
                    $property_status      =   get_the_term_list($postID, 'property_status', '', $separator, '');
                    if($property_status!=''){
                        $categories_to_show    =   $categories_to_show.$property_status.$separator;
                    }                   
                    break;


            }
        endforeach;    

        $return_categories_to_show='<div class="property_card_categories_wrapper">'.rtrim($categories_to_show,$separator).'</div>';
        return $return_categories_to_show;
    }
endif;
    
/*
*
* Return mls data 
*
*/


if(!function_exists('wpestate_return_property_card_mls_data')):
    function wpestate_return_property_card_mls_data(){
        $return_string= '<div class="wpestate_property_card_mls_data_wrapper">';
        $mls_logo   = wpresidence_get_option('wp_estate_property_card_rows_mls_logo', 'url');
        $mls_name   = wpresidence_get_option('wp_estate_property_card_rows_mls_name', '');
        if($mls_logo!==''){
            $return_string.='<img src="'.esc_attr($mls_logo).'" alt="mls_logo">';
        }
        if($mls_name!=''){
            $return_string.=esc_html($mls_name);
        }

        $return_string.='</div>';

        return $return_string;
    }
endif;
    
/*
*
* Return card unit title
*
*/

if(!function_exists('wpestate_return_property_card_title')):
    function wpestate_return_property_card_title($postID){
        $title           = wpresidence_get_sanitized_truncated_title($postID,0);
        $title_length   = mb_strlen($title);
        $substr_value   = intval(wpresidence_get_option('wp_estate_unit_card_title', ''));
        $display_title = $substr_value > 0 ? mb_substr($title, 0, $substr_value) . ($title_length > $substr_value ? '...' : '') : esc_html($title);

        return $display_title;
    }
endif;
    
    
/**
 * Returns the property card title with optional length limitation
 * Uses the wpestate_return_data_from_cache_if_exists function to retrieve title with fallback
 *
 * @param array $property_unit_cached_data The cached property data array
 * @param int   $propID                    The property post ID
 * @return string The formatted property title
 */
if(!function_exists('wpestate_return_property_card_title_from_cache')):
    function wpestate_return_property_card_title_from_cache($property_unit_cached_data, $propID){
        // Get title using cache function with fallback
        $title = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $propID, '', 'title');
        
        $title_length   = mb_strlen($title);
        $substr_value   = intval(wpresidence_get_option('wp_estate_unit_card_title', ''));
        $display_title = $substr_value > 0 ? mb_substr($title, 0, $substr_value) . ($title_length > $substr_value ? '...' : '') : esc_html($title);
        
        return $display_title;
    }
endif;





/*
*
* Helper to get property card placeholder image
* 
*/
if(!function_exists('wpestate_get_property_card_placeholder_image')):
    function wpestate_get_property_card_placeholder_image(){
        $thumb_prop_default = wpresidence_get_option('wp_estate_prop_list_slider_image_palceholder','url');
        if($thumb_prop_default==''){
            $thumb_prop_default = get_theme_file_uri('/img/defaults/default_property_listings.jpg');
        }

        return $thumb_prop_default;
    }
endif;

/*
*
* Return card unit thumb
* 
*/
if(!function_exists('wpestate_return_property_card_thumb')):
    function wpestate_return_property_card_thumb($postID,$size='property_listings'){
        $preview_src    =   '';
        $preview        =    wp_get_attachment_image_src($postID, 'property_listings');
        if(isset($preview[0])){
            $preview_src=$preview[0];
        }

        $extra= array(
            'data-original' =>  $preview_src,
            'class'         =>  'lazyload img-responsive',
        );

        
        $thumb_prop             =   get_the_post_thumbnail($postID, $size,$extra);
        if($thumb_prop ==''){
            $thumb_prop_default =  wpestate_get_property_card_placeholder_image();

            $thumb_prop         =  '<img src="'.esc_url($thumb_prop_default).'" class="b-lazy img-responsive wp-post-image  lazy-hidden" alt="'.esc_html__('user image','wpresidence').'" />';
        }
        
        return $thumb_prop;
    }
endif;

    
        
/**
 * Returns the property card thumbnail HTML with fallback to default image
 * Uses the wpestate_return_data_from_cache_if_exists function to retrieve image data with fallback
 *
 * @param array  $property_unit_cached_data The cached property data array
 * @param int    $propID                    The property post ID
 * @param string $size                      The desired image size
 * @return string The HTML for the property thumbnail
 */
if (!function_exists('wpestate_return_property_card_thumb_cache')):
    function wpestate_return_property_card_thumb_cache($property_unit_cached_data, $propID, $size = 'property_listings') {
        $preview_src = '';
        $thumb_prop = ''; // Initialize variable to avoid undefined errors
        $title = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $propID, '', 'title');
        // Get featured media using cache function with fallback
        $featured_media = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $propID, 'featured_media', $size);
        
        if (!empty($featured_media)) {
            $preview_src = $featured_media;
            
            // Construct the extra attributes array
            $extra = array(
                'data-original' => esc_url($preview_src),
                'class'         => 'lazyload img-responsive',
                'alt'           => esc_attr( $title )
            );
            
            // Generate the image HTML using the $extra attributes
            $thumb_prop = '<img src="' . esc_url($preview_src) . '"';
            foreach ($extra as $key => $value) {
                $thumb_prop .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
            }
            $thumb_prop .= ' />';
        }
        
        // If no image was found, use the default placeholder
        if (empty($thumb_prop)) {
            $thumb_prop_default = wpestate_get_property_card_placeholder_image();
            $thumb_prop = '<img src="' . esc_url($thumb_prop_default) . '" class="b-lazy img-responsive wp-post-image lazy-hidden" alt="' . esc_attr__('Property Image', 'wpresidence') . '" />';
        }
        
        return $thumb_prop;
    }
endif;






/*
*
* Return card unit main image
* 
*/
if(!function_exists('wpestate_return_property_card_main_image')):
    function wpestate_return_property_card_main_image($postID,$size='property_listings'){
        $main_image     =   wp_get_attachment_image_src(get_post_thumbnail_id($postID), 'listing_full_slider');
        if(isset( $main_image [0] )){
            return $main_image[0];
        }else{
            return wpestate_get_property_card_placeholder_image();
        }
    }
endif;
        

/*
*
* Return card unit thumb email	
* 
*/
if(!function_exists('wpestate_return_property_card_thumb_email')):
    function wpestate_return_property_card_thumb_email($postID,$size='property_listings'){
        $main_image     =   wp_get_attachment_image_src(get_post_thumbnail_id($postID), $size);
        if(isset( $main_image [0] )){
            return $main_image[0];
        }else{
            return wpestate_get_property_card_placeholder_image();
        }
    }
endif;
      





/**
 * Generate HTML for the featured property carousel
 *
 * @param int $propID The property ID
 * @param string $preview_url The URL of the main preview image
 * @param string $link The permalink of the property
 * @param array $post_attachments Array of attachment posts
 * @param string $slider_content Pre-generated HTML for slider items
 * @return string The generated HTML for the carousel
 */
function wpestate_featured_property_carousel($propID, $preview_url, $link, $post_attachments, $slider_content) {
   
}






/**
 * Generate slider content for a property
 *
 * @param int $propID The property ID
 * @param string $link The permalink of the property
 * @param string $title The title of the property
 * @return string The generated slider content
 */
function wpestate_generate_property_slider_content($propID, $link, $title,$context='',$image_as_background=false) {


    $slider_content = '';
    $active_class= 'active';

    $post_attachments=wpestate_generate_property_slider_image_ids($propID,true);


    if(is_array($post_attachments) && !empty($post_attachments)):
        foreach ($post_attachments as $attachment_id) {
                $property_featured_image_url = wpestate_get_property_image_src($attachment_id, 'listing_full_slider');
         
           
                
                if($image_as_background){
                    $slider_content .= '<div class=" carousel-item item  '.esc_attr($active_class).' "  style="background-image:url('.esc_url($property_featured_image_url).');"></div>';
                }else{

                    $slider_content .= '<div class=" carousel-item item  '.esc_attr($active_class).' " >
                       
                        <a href="'.esc_url($link).'"><img src="'.esc_url($property_featured_image_url).'" alt="'.esc_attr($title).'" class="img-responsive" /></a>
                    </div>';

                }


                $active_class='';
               
        }
    endif;

    ob_start();
    $carousel_id= 'property_unit_'.esc_attr($context).'_'.intval($propID).'_'.(rand(1,1000)); 

    ?>
    <div id="<?php echo esc_attr( $carousel_id);?>" class="carousel property_unit_featured_carousel slide" data-bs-interval="false">
        <div class="carousel-inner">
            <div class="featured_gradient"></div>
            <?php echo wp_kses_post($slider_content);  ?>
        </div>



        <?php 
        // slider controls
        if (is_array($post_attachments) && count($post_attachments) >= 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo esc_attr( $carousel_id);?>" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden"><?php esc_html_e('Previous', 'wpresidence'); ?></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#<?php echo esc_attr( $carousel_id);?>" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden"><?php esc_html_e('Next', 'wpresidence'); ?></span>
            </button>
        <?php endif; ?>


    </div>
    <?php
    return ob_get_clean();






}


/**
 * Generate HTML for a single property image
 *
 * @param string $preview_url The URL of the preview image
 * @param string $link The permalink of the property
 * @return string The generated HTML for the single image
 */
function wpestate_featured_property_single_image($preview_url, $link) {
    ob_start();
    ?>
    <a href="<?php echo esc_url($link); ?>">
        <div class="featured_gradient"></div>
        <img src="<?php echo esc_url($preview_url); ?>" data-src="<?php echo esc_attr($preview_url); ?>" class="d-block w-100 lazyload" alt="<?php esc_attr_e('Property Image', 'wpresidence'); ?>"/>
    </a>
    <?php
    return ob_get_clean();
}







?>
