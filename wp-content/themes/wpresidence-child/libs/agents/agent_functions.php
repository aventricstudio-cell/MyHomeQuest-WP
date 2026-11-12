<?php


if(!function_exists('wpresidence_display_agent_list_as_html')):
    function wpresidence_display_agent_list_as_html($agent_selection,$agent_type, $wpestate_options = array(), $context = '', $shortcode_attributes = array(), $pagination = array()){

        // Calculate the column class
        $agent_unit_col_class = wpestate_get_agent_column_class($wpestate_options,$context,$shortcode_attributes);
        // Determine which agent unit template to use
        $agent_unit = wpestate_agent_card_selector();


        //open in new page option
        $new_page_option                     = wpresidence_get_option('wp_estate_unit_card_new_page', '');


        // Determine if grid display is enabled
        $display_grid = isset($shortcode_attributes['display_grid']) && $shortcode_attributes['display_grid'] === 'yes' ? 'yes' : 'no';

        // reset agent unit class for grid display
        if ($display_grid === 'yes' ){
            $agent_unit_col_class['col_class']='';
        }else{
            // add class for responsive when non grid
            $agent_unit_col_class['col_class'].=' col-12 col-sm-6 col-md-6 ';
        }

        if( $agent_selection->have_posts() ){
		        
            while ($agent_selection->have_posts()): $agent_selection->the_post();
                    $postID = get_the_ID();
                
                    if ($display_grid === 'yes') {
                        echo '<div class="shortcode_wrapper_grid_item">';
                    }

                    // if post type set - define taxonomies
                    switch( $agent_type ){
                            case "estate_agent":
                                include( locate_template($agent_unit) ) ;      
                            break;
                            case "estate_agency":
                                include( locate_template( 'templates/agency__developers_cards_templates/agency_developer_unit.php'));
                            break;
                            case "estate_developer": 
                                include( locate_template( 'templates/agency__developers_cards_templates/agency_developer_unit.php'));
                            break;
                    }
                
                        
                    if ($display_grid === 'yes') {
                        echo '</div>';
                    }
            
            endwhile;
        
                
        }else{

            switch($context){
                case 'shortcode':
                    print '<span class="no_results">'. esc_html__("There are no more agents.", "wpresidence").'</span>';
                    break;
                case 'agency_agents':
                    print '<h4 class="no_agents">'.esc_html__('We don\'t have any agents yet!','wpresidence').'</h4>';
                    break;
                default:
                    print '<span class="no_results">'. esc_html__("There are no agents listed on this page at this moment.", "wpresidence").'</>';
                    break;
            }
           
        }

        wp_reset_postdata();
        wp_reset_query();

    }
endif;






/**
 * Determine the appropriate column class based on content width and listings per row
 *
 * @param string $content_class The main content column class
 * @param int $listings_per_row Number of listings to display per row
 * @return string The calculated column class
 */

if(!function_exists('wpestate_get_agent_column_class')):
function wpestate_get_agent_column_class($wpestate_options='',$context='',$shortcode_params=''){
  
    $wpestate_no_listins_per_row = intval(wpresidence_get_option('wp_estate_agent_listings_per_row', ''));

    if($context=='shortcode' && isset($shortcode_params['rownumber']) ){
        $wpestate_no_listins_per_row = intval($shortcode_params['rownumber']);
    }

    // Further adjustment if content class is 'col-lg-12' or full width
    if(isset( $wpestate_options['content_class']) && !str_contains($wpestate_options['content_class'], 'col-lg-12') ){
        $wpestate_no_listins_per_row--;
    }

    // force maxim 6 columns
    if ($wpestate_no_listins_per_row > 4) {
        $wpestate_no_listins_per_row = 4;
    }   

    
    $options = array(
        '1'=> array(
            'col_class' =>  'col-lg-12 col-md-12',
            'col_org'   =>  12
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



/**
*
*
* Unit selector agents
*
*
*/
if(!function_exists('wpestate_agent_card_selector')):
    function wpestate_agent_card_selector($agent_unit_card_from_shortcode='') {
        
        $template = 'agent_unit.php';

        if( intval($agent_unit_card_from_shortcode)!=0){
            $agent_unit_card = intval($agent_unit_card_from_shortcode);
        }else{
            $agent_unit_card  = intval(wpresidence_get_option('wp_estate_agent_unit_card', ''));         
        }
      

        switch ($agent_unit_card) {
            case 1:
                $template = 'agent_unit.php';
                break;
            case 2:
                $template = 'agent_unit_v2.php';
                break;
            case 3:
                $template = 'agent_unit_v3.php';
                break;
            case 4:
                $template = 'agent_unit_v4.php';
                break;
        }


        return 'templates/agent_card_templates/'.$template;

    }
endif;



/**
 * Get Agent Thumbnail Image
 *
 * This function retrieves the agent's thumbnail image. If no thumbnail
 * is set, it returns a default user image.
 *
 * @param int    $agent_id   The ID of the agent post. Default is current post ID.
 * @param string $size       The size of the thumbnail. Default is 'property_listings'.
 * @param array  $attr       Additional attributes for the thumbnail. Default includes lazy loading class.
 *
 * @return string HTML img tag of the agent's thumbnail or default image.
 *
 * @since 1.0.0
 * @version 1.1.0 Added lazy loading class by default
 *
 * Usage example:
 * $agent_thumbnail = wpestate_get_agent_thumbnail();
 * echo $agent_thumbnail;
 */
function wpestate_get_agent_thumbnail($agent_id = 0, $size = 'property_listings', $attr = array()) {
    // If no agent ID is provided, use the current post ID
    if (!$agent_id) {
        $agent_id = get_the_ID();
    }

    // Set default attributes for the thumbnail
    $default_attr = array(
        'class' => 'lazyload img-responsive',
    );

    // Merge default attributes with any provided attributes
    $attr = wp_parse_args($attr, $default_attr);

    // Attempt to get the post thumbnail
    $thumb_prop = get_the_post_thumbnail($agent_id, $size, $attr);

    // If no thumbnail is set, use the default image
    if (empty($thumb_prop)) {
        $thumb_prop = sprintf(
            '<img src="%1$s" alt="%2$s" class="%3$s">',
            esc_url(get_theme_file_uri('/img/default_user.png')),
            esc_attr__('default user image', 'wpresidence'),
            esc_attr($attr['class'])
        );
    }

    return $thumb_prop;
}



/**
*  return what's up api call
*
* param   $propid = proeprty id
* @var
*/

if( !function_exists('wpestate_return_agent_whatsapp_call') ):
    function wpestate_return_agent_whatsapp_call($propId,$mobile_number){
        $whatsup_no = preg_replace("/[^0-9]/", "", $mobile_number);

        if( intval($propId)!=0 ){
         
            if( intval($propId)==-1 ){ //agent
                $text_whats       =   esc_html__('Hello, I\'m interested in one of your listings.','wpresidence');
            }else if( get_post_type($propId) == 'estate_property' and $mobile_number!=''){
                $text_whats= esc_html__('Hello, I\'m interested in ','wpresidence').'['.wpresidence_get_sanitized_truncated_title($propId,0).'] '.get_permalink($propId);
            }else{
                $text_whats=  wpresidence_get_sanitized_truncated_title($propId,0).' '. esc_url( get_permalink($propId) ) ; 
            }
        }else{
            $text_whats=  wpresidence_get_sanitized_truncated_title($propId,0).' '. esc_url( get_permalink($propId) ) ; 
        }
        
        
        $whatsup_mess='https://wa.me/'.esc_html($whatsup_no).'?text='.($text_whats);

        return $whatsup_mess;
    }
endif;



/**
*  return agent/user picture
*
* @since
* @var
*/



if( !function_exists('wpestate_agent_picture') ):
function wpestate_agent_picture($propid){
    $agent_id       =   intval( get_post_meta($propid, 'property_agent', true) );
    $thumb_id       =   get_post_thumbnail_id($agent_id);
    $preview        =   wp_get_attachment_image_src($thumb_id, 'property_listings');
    if(isset($preview[0])){
        return  $preview[0];
    }else{
      return '';
    }


}
endif;




/**
*  return agent/user details
*
* param   $propid = proeprty id
* @var
*/

if( !function_exists('wpestate_return_agent_details_from_cache') ):
    function wpestate_return_agent_details_from_cache($property_agent_cached_data,$agentID,$propertyID='') {       // Retrieve agent ID associated with the property from cache


        // Apply WPML filter if the function exists
        if (function_exists('icl_translate')) {
            $agentID = apply_filters('wpml_object_id', $agentID, 'estate_agent');
        }

        // Apply WPR filter if the function exists
        if (function_exists('wpr_object_id')) {
            $agentID = apply_filters('wpr_object_id', $agentID, 'estate_agent');
        }



    
        $user_id        =   0;
        $counter        =   0;
        $agent_member   =   '';
        $agent_face_img =   '';
    
        $one_id         =    $agentID;

        if($agentID!=0){
            // Set default images
            $preview_img = get_theme_file_uri('/img/default_user_agent.png');
            $agent_face_img = get_theme_file_uri('/img/default-user_1.png');

            // Check if image data exists in cache
            if(isset($property_agent_cached_data) && isset($property_agent_cached_data['featured_media']) && !empty($property_agent_cached_data['featured_media'])) {
                // Get the first entry from featured_media array
                $first_key = array_key_first($property_agent_cached_data['featured_media']);
                $image_data = $property_agent_cached_data['featured_media'][$first_key];
                
                // Get property_listings image
                if(isset($image_data['property_listings'])) {
                    $preview_img = $image_data['property_listings'];
                }
                
                // Get agent_picture_thumb
                if(isset($image_data['agent_picture_thumb'])) {
                    $agent_face_img = $image_data['agent_picture_thumb'];
                }
            }
            $title  =   $property_agent_cached_data['title'];
            $link   =   esc_url(   $property_agent_cached_data['permalink'] );
            $type   =   get_post_type($agentID);
    
            $agent_mobile       = '';
            $agent_email        = '';
            $agent_skype        = '';
            $agent_phone        = '';
            $agent_pitch        = '';
            $agent_facebook     = '';
            $agent_twitter      = '';
            $agent_linkedin     = '';
            $agent_pinterest    = '';
            $agent_instagram    = '';
            $agent_youtube      = '';
            $agent_tiktok       = '';
            $agent_urlc         = '';
            $agent_member       = '';
            $agent_address      = '';
            $agent_telegram     =   '';
            $agent_vimeo        =   '';
            $agent_private_notes=   '';
            $agent_opening_hours=   '';
            $agent_languages=   '';
            $agent_license=   '';
            $agent_taxes=   '';
            
    
            if( $type=='estate_agent' ){
                if(isset($property_agent_cached_data) && isset($property_agent_cached_data['meta'])) {
                    // Use cached data
                    $meta = $property_agent_cached_data['meta'];
                    
                    // Agent details from cached meta
                    $agent_mobile = isset($meta['agent_mobile']) ? esc_html($meta['agent_mobile']) : '';
                    $agent_email = isset($meta['agent_email']) ? esc_html($meta['agent_email']) : '';
                    $agent_skype = isset($meta['agent_skype']) ? esc_html($meta['agent_skype']) : '';
                    $agent_phone = isset($meta['agent_phone']) ? esc_html($meta['agent_phone']) : '';
                    $agent_facebook = isset($meta['agent_facebook']) ? esc_html($meta['agent_facebook']) : '';
                    $agent_twitter = isset($meta['agent_twitter']) ? esc_html($meta['agent_twitter']) : '';
                    $agent_linkedin = isset($meta['agent_linkedin']) ? esc_html($meta['agent_linkedin']) : '';
                    $agent_pinterest = isset($meta['agent_pinterest']) ? esc_html($meta['agent_pinterest']) : '';
                    $agent_instagram = isset($meta['agent_instagram']) ? esc_html($meta['agent_instagram']) : '';
                    $agent_urlc = isset($meta['agent_website']) ? esc_html($meta['agent_website']) : '';
                    $agent_member = isset($meta['agent_member']) ? esc_html($meta['agent_member']) : '';
                    $agent_address = isset($meta['agent_address']) ? esc_html($meta['agent_address']) : '';
                    $agent_youtube = isset($meta['agent_youtube']) ? esc_html($meta['agent_youtube']) : '';
                    $agent_tiktok = isset($meta['agent_tiktok']) ? esc_html($meta['agent_tiktok']) : '';
                    $agent_telegram = isset($meta['agent_telegram']) ? esc_html($meta['agent_telegram']) : '';
                    $agent_vimeo = isset($meta['agent_vimeo']) ? esc_html($meta['agent_vimeo']) : '';
                    $agent_private_notes = isset($meta['agent_private_notes']) ? esc_html($meta['agent_private_notes']) : '';
                    $agent_pitch = isset($meta['agent_pitch']) ? esc_html($meta['agent_pitch']) : '';
                    $agent_posit = isset($meta['agent_position']) ? esc_html($meta['agent_position']) : '';
                } else {
                    // Fallback to getting from post meta directly
                    $agent_mobile = esc_html(get_post_meta($agentID, 'agent_mobile', true));
                    $agent_email = esc_html(get_post_meta($agentID, 'agent_email', true));
                    $agent_skype = esc_html(get_post_meta($agentID, 'agent_skype', true));
                    $agent_phone = esc_html(get_post_meta($agentID, 'agent_phone', true));
                    $agent_pitch = esc_html(get_post_meta($agentID, 'agent_pitch', true));
                    $agent_facebook = esc_html(get_post_meta($agentID, 'agent_facebook', true));
                    $agent_twitter = esc_html(get_post_meta($agentID, 'agent_twitter', true));
                    $agent_linkedin = esc_html(get_post_meta($agentID, 'agent_linkedin', true));
                    $agent_pinterest = esc_html(get_post_meta($agentID, 'agent_pinterest', true));
                    $agent_instagram = esc_html(get_post_meta($agentID, 'agent_instagram', true));
                    $agent_urlc = esc_html(get_post_meta($agentID, 'agent_website', true));
                    $agent_member = esc_html(get_post_meta($agentID, 'agent_member', true));
                    $agent_address = esc_html(get_post_meta($agentID, 'agent_address', true));
                    $agent_youtube = esc_html(get_post_meta($agentID, 'agent_youtube', true));
                    $agent_tiktok = esc_html(get_post_meta($agentID, 'agent_tiktok', true));
                    $agent_telegram = esc_html(get_post_meta($agentID, 'agent_telegram', true));
                    $agent_vimeo = esc_html(get_post_meta($agentID, 'agent_vimeo', true));
                    $agent_private_notes = esc_html(get_post_meta($agentID, 'agent_private_notes', true));
                }
    
            }else if($type == 'estate_agency') {
                if(isset($property_agent_cached_data) && isset($property_agent_cached_data['meta'])) {
                    // Use cached data
                    $meta = $property_agent_cached_data['meta'];
                    
                    // Agency details from cached meta
                    $agent_mobile = isset($meta['agency_mobile']) ? esc_html($meta['agency_mobile']) : '';
                    $agent_email = isset($meta['agency_email']) ? esc_html($meta['agency_email']) : '';
                    $agent_skype = isset($meta['agency_skype']) ? esc_html($meta['agency_skype']) : '';
                    $agent_phone = isset($meta['agency_phone']) ? esc_html($meta['agency_phone']) : '';
                    $agent_facebook = isset($meta['agency_facebook']) ? esc_html($meta['agency_facebook']) : '';
                    $agent_twitter = isset($meta['agency_twitter']) ? esc_html($meta['agency_twitter']) : '';
                    $agent_linkedin = isset($meta['agency_linkedin']) ? esc_html($meta['agency_linkedin']) : '';
                    $agent_pinterest = isset($meta['agency_pinterest']) ? esc_html($meta['agency_pinterest']) : '';
                    $agent_instagram = isset($meta['agency_instagram']) ? esc_html($meta['agency_instagram']) : '';
                    $agent_urlc = isset($meta['agency_website']) ? esc_html($meta['agency_website']) : '';
                    $agent_address = isset($meta['agency_address']) ? esc_html($meta['agency_address']) : '';
                    $agent_youtube = isset($meta['agency_youtube']) ? esc_html($meta['agency_youtube']) : '';
                    $agent_tiktok = isset($meta['agency_tiktok']) ? esc_html($meta['agency_tiktok']) : '';
                    $agent_telegram = isset($meta['agency_telegram']) ? esc_html($meta['agency_telegram']) : '';
                    $agent_vimeo = isset($meta['agency_vimeo']) ? esc_html($meta['agency_vimeo']) : '';
                    $agent_private_notes = isset($meta['agency_private_notes']) ? esc_html($meta['agency_private_notes']) : '';
                    $agent_pitch = isset($meta['agency_pitch']) ? esc_html($meta['agency_pitch']) : '';
                    $agent_posit = isset($meta['agency_position']) ? esc_html($meta['agency_position']) : '';
                    $agent_member = isset($meta['agent_member']) ? esc_html($meta['agent_member']) : '';

                    $agent_opening_hours = isset($meta['agency_opening_hours']) ? esc_html($meta['agency_opening_hours']) : '';
                    $agent_languages = isset($meta['agency_languages']) ? esc_html($meta['agency_languages']) : '';
                    $agent_license = isset($meta['agency_license']) ? esc_html($meta['agency_license']) : '';
                    $agent_taxes = isset($meta['agency_taxes']) ? esc_html($meta['agency_taxes']) : '';
                } else {
                    // Fallback to getting from post meta directly
                    $agent_mobile = esc_html(get_post_meta($agentID, 'agency_mobile', true));
                    $agent_email = esc_html(get_post_meta($agentID, 'agency_email', true));
                    $agent_skype = esc_html(get_post_meta($agentID, 'agency_skype', true));
                    $agent_phone = esc_html(get_post_meta($agentID, 'agency_phone', true));
                    $agent_pitch = esc_html(get_post_meta($agentID, 'agency_pitch', true));
                    $agent_posit = esc_html(get_post_meta($agentID, 'agency_position', true));
                    $agent_facebook = esc_html(get_post_meta($agentID, 'agency_facebook', true));
                    $agent_twitter = esc_html(get_post_meta($agentID, 'agency_twitter', true));
                    $agent_linkedin = esc_html(get_post_meta($agentID, 'agency_linkedin', true));
                    $agent_pinterest = esc_html(get_post_meta($agentID, 'agency_pinterest', true));
                    $agent_instagram = esc_html(get_post_meta($agentID, 'agency_instagram', true));
                    $agent_urlc = esc_html(get_post_meta($agentID, 'agency_website', true));
                    $agent_member = esc_html(get_post_meta($agentID, 'agent_member', true));
                    $agent_address = esc_html(get_post_meta($agentID, 'agent_address', true));
                    $agent_youtube = esc_html(get_post_meta($agentID, 'agency_youtube', true));
                    $agent_tiktok = esc_html(get_post_meta($agentID, 'agency_tiktok', true));
                    $agent_telegram = esc_html(get_post_meta($agentID, 'agency_telegram', true));
                    $agent_vimeo = esc_html(get_post_meta($agentID, 'agency_vimeo', true));
                    $agent_private_notes = esc_html(get_post_meta($agentID, 'agency_private_notes', true));

                    $agent_opening_hours = esc_html(get_post_meta($agentID, 'agency_opening_hours', true));
                    $agent_languages = esc_html(get_post_meta($agentID, 'agency_languages', true));
                    $agent_license = esc_html(get_post_meta($agentID, 'agency_license', true));
                    $agent_taxes = esc_html(get_post_meta($agentID, 'agency_taxes', true));
                }
            }else if($type=='estate_developer'){
                if(isset($property_agent_cached_data) && isset($property_agent_cached_data['meta'])) {
                    // Use cached data
                    $meta = $property_agent_cached_data['meta'];
                    
                    // Developer details from cached meta
                    $agent_mobile = isset($meta['developer_mobile']) ? esc_html($meta['developer_mobile']) : '';
                    $agent_email = isset($meta['developer_email']) ? esc_html($meta['developer_email']) : '';
                    $agent_skype = isset($meta['developer_skype']) ? esc_html($meta['developer_skype']) : '';
                    $agent_phone = isset($meta['developer_phone']) ? esc_html($meta['developer_phone']) : '';
                    $agent_facebook = isset($meta['developer_facebook']) ? esc_html($meta['developer_facebook']) : '';
                    $agent_twitter = isset($meta['developer_twitter']) ? esc_html($meta['developer_twitter']) : '';
                    $agent_linkedin = isset($meta['developer_linkedin']) ? esc_html($meta['developer_linkedin']) : '';
                    $agent_pinterest = isset($meta['developer_pinterest']) ? esc_html($meta['developer_pinterest']) : '';
                    $agent_instagram = isset($meta['developer_instagram']) ? esc_html($meta['developer_instagram']) : '';
                    $agent_urlc = isset($meta['developer_website']) ? esc_html($meta['developer_website']) : '';
                    $agent_address = isset($meta['developer_address']) ? esc_html($meta['developer_address']) : '';
                    $agent_pitch = isset($meta['developer_pitch']) ? esc_html($meta['developer_pitch']) : '';
                    $agent_posit = isset($meta['developer_position']) ? esc_html($meta['developer_position']) : '';
                    $agent_member = isset($meta['agent_member']) ? esc_html($meta['agent_member']) : '';
                    $agent_youtube = isset($meta['agent_youtube']) ? esc_html($meta['agent_youtube']) : '';
                    $agent_tiktok = isset($meta['agent_tiktok']) ? esc_html($meta['agent_tiktok']) : '';
                    $agent_telegram = isset($meta['agent_telegram']) ? esc_html($meta['agent_telegram']) : '';
                    $agent_vimeo = isset($meta['agent_vimeo']) ? esc_html($meta['agent_vimeo']) : '';
                    $agent_private_notes = isset($meta['agent_private_notes']) ? esc_html($meta['agent_private_notes']) : '';

                    $agent_opening_hours = isset($meta['developer_opening_hours']) ? esc_html($meta['developer_opening_hours']) : '';
                    $agent_languages = isset($meta['developer_languages']) ? esc_html($meta['developer_languages']) : '';
                    $agent_license = isset($meta['developer_license']) ? esc_html($meta['developer_license']) : '';
                    $agent_taxes = isset($meta['developer_taxes']) ? esc_html($meta['developer_taxes']) : '';
                } else {
                    // Fallback to getting from post meta directly
                    $agent_mobile = esc_html(get_post_meta($agentID, 'developer_mobile', true));
                    $agent_email = esc_html(get_post_meta($agentID, 'developer_email', true));
                    $agent_skype = esc_html(get_post_meta($agentID, 'developer_skype', true));
                    $agent_phone = esc_html(get_post_meta($agentID, 'developer_phone', true));
                    $agent_pitch = esc_html(get_post_meta($agentID, 'developer_pitch', true));
                    $agent_posit = esc_html(get_post_meta($agentID, 'developer_position', true));
                    $agent_facebook = esc_html(get_post_meta($agentID, 'developer_facebook', true));
                    $agent_twitter = esc_html(get_post_meta($agentID, 'developer_twitter', true));
                    $agent_linkedin = esc_html(get_post_meta($agentID, 'developer_linkedin', true));
                    $agent_pinterest = esc_html(get_post_meta($agentID, 'developer_pinterest', true));
                    $agent_instagram = esc_html(get_post_meta($agentID, 'developer_instagram', true));
                    $agent_urlc = esc_html(get_post_meta($agentID, 'developer_website', true));
                    $agent_member = esc_html(get_post_meta($agentID, 'agent_member', true));
                    $agent_address = esc_html(get_post_meta($agentID, 'agent_address', true));
                    $agent_youtube = esc_html(get_post_meta($agentID, 'agent_youtube', true));
                    $agent_tiktok = esc_html(get_post_meta($agentID, 'agent_tiktok', true));
                    $agent_telegram = esc_html(get_post_meta($agentID, 'agent_telegram', true));
                    $agent_vimeo = esc_html(get_post_meta($agentID, 'agent_vimeo', true));
                    $agent_private_notes = esc_html(get_post_meta($agentID, 'agent_private_notes', true));

                    $agent_opening_hours = esc_html(get_post_meta($agentID, 'developer_opening_hours', true));
                    $agent_languages = esc_html(get_post_meta($agentID, 'developer_languages', true));
                    $agent_license = esc_html(get_post_meta($agentID, 'developer_license', true));
                    $agent_taxes = esc_html(get_post_meta($agentID, 'developer_taxes', true));
                }
            }
            $agent_posit        = isset( $property_agent_cached_data['meta']['agent_position'] ) ? esc_html( $property_agent_cached_data['meta']['agent_position']  ) : '';
            $user_for_id        = isset( $property_agent_cached_data['meta']['user_meda_id'] ) ? esc_html( $property_agent_cached_data['meta']['user_meda_id']  ) : 0;
      
            if($user_for_id!=0){
                $counter            =   count_user_posts($user_for_id,'estate_property',true);
            }
    
    
        }else{
            $user_id        =    get_post_field( 'post_author', $propertyID );
            $one_id         =    $user_id;
            $preview_img    =$agent_face_img=    get_the_author_meta( 'custom_picture',$user_id  );
    
            if($preview_img==''){
                $preview_img = $agent_face_img=get_theme_file_uri('/img/default-user.png');
            }
    
            $title               =  get_the_author_meta( 'first_name',$user_id ).' '.get_the_author_meta( 'last_name',$user_id);
            $link                =  '';
            $agent_posit         =  get_the_author_meta( 'title' ,$user_id );
            $agent_mobile        =  get_the_author_meta( 'mobile'  ,$user_id);
            $agent_skype         =  get_the_author_meta( 'skype',$user_id  );
            $agent_phone         =  get_the_author_meta( 'phone',$user_id  );
            $counter             =  count_user_posts($user_id,'estate_property',true);
            $agent_email         =  get_the_author_meta( 'user_email',$user_id  );
            $agent_pitch         =  '';
            $agent_facebook      =  get_the_author_meta( 'facebook',$user_id  );
            $agent_twitter       =  get_the_author_meta( 'twitter',$user_id  );
            $agent_linkedin      =  get_the_author_meta( 'linkedin',$user_id  );
            $agent_pinterest     =  get_the_author_meta( 'pinterest',$user_id  );
            $agent_instagram     =  get_the_author_meta( 'instagram',$user_id  );
            $agent_urlc          =  get_the_author_meta( 'website',$user_id  );
    
            $agent_youtube       =  get_the_author_meta( 'agent_youtube',$user_id  );
            $agent_tiktok        =  get_the_author_meta( 'agent_tiktok',$user_id  );
            $agent_telegram      =  get_the_author_meta( 'agent_telegram',$user_id  );
            $agent_vimeo         =  get_the_author_meta( 'agent_vimeo',$user_id  );
            $agent_private_notes =  get_the_author_meta( 'agent_private_notes',$user_id  );
            $agent_address       =  get_the_author_meta( 'agent_address',$user_id  );
            $agent_opening_hours =  get_the_author_meta( 'agent_opening_hours',$user_id  );
            $agent_languages     =  get_the_author_meta( 'agent_languages',$user_id  );
            $agent_license       =  get_the_author_meta( 'agent_license',$user_id  );
            $agent_taxes         =  get_the_author_meta( 'agent_taxes',$user_id  );
        }
    
    
    
        $all_details=array();
        $all_details['one_id']              =   $one_id;
        $all_details['agent_id']            =   $agentID;
        $all_details['user_id']             =   $user_id;
        $all_details['realtor_image']       =   $preview_img;
        $all_details['agent_face_img']      =   $agent_face_img;
        $all_details['realtor_name']        =   $title;
        $all_details['link']                =   $link;
        $all_details['email']               =   $agent_email;
        $all_details['realtor_position']    =   $agent_posit;
        $all_details['realtor_mobile']      =   $agent_mobile;
        $all_details['realtor_skype']       =   $agent_skype;
        $all_details['realtor_phone']       =   $agent_phone;
        $all_details['realtor_pitch']       =   $agent_pitch;
        $all_details['realtor_facebook']    =   $agent_facebook;
        $all_details['realtor_twitter']     =   $agent_twitter;
        $all_details['realtor_linkedin']    =   $agent_linkedin;
        $all_details['realtor_pinterest']   =   $agent_pinterest;
        $all_details['realtor_instagram']   =   $agent_instagram;
        $all_details['realtor_urlc']        =   $agent_urlc;
        $all_details['member_of']           =   $agent_member;
        $all_details['agent_address']       =   $agent_address;
    
        $all_details['realtor_youtube']       =   $agent_youtube;
        $all_details['realtor_tiktok']        =   $agent_tiktok;
        $all_details['realtor_telegram']      =   $agent_telegram;
        $all_details['realtor_vimeo']         =   $agent_vimeo;
        $all_details['realtor_private_notes'] =   $agent_private_notes;

        $all_details['realtor_opening_hours'] =   $agent_opening_hours;
        $all_details['realtor_languages'] =   $agent_languages;
        $all_details['realtor_license'] =   $agent_license;
        $all_details['realtor_taxes'] =   $agent_taxes;
        
        $all_details['counter']         =   $counter;
        return $all_details;
    }
endif;
    
    

















/**
*  return agent/user details
*
* param   $propid = proeprty id
* @var
*/

if( !function_exists('wpestate_return_agent_details') ):
function wpestate_return_agent_details($propid,$singular_agent_id=''){

    if($singular_agent_id==''){
         $agent_id       =   intval( get_post_meta($propid, 'property_agent', true) );
    }else{
        $agent_id=$singular_agent_id;
    }


if (function_exists('wpr_object_id')) { 
    $agent_id =  apply_filters('wpr_object_id', $agent_id ,get_post_type($agent_id));
}






    $user_id        =   0;
    $counter        =   0;
    $agent_member   =   '';
    $agent_face_img =   '';

    if($agent_id!=0){
        $one_id         =    $agent_id;
        $thumb_id       =    get_post_thumbnail_id($agent_id);
        if($thumb_id==''){
            $preview_img    =   get_theme_file_uri('/img/default_user_agent.png');
            $agent_face     =   get_theme_file_uri('/img/default-user_1.png');
        }else{
            $preview        =   wp_get_attachment_image_src($thumb_id, 'property_listings');
            $preview_img    =   get_theme_file_uri('/img/default_user_agent.png');
            if($preview){
                $preview_img    =   $preview[0];
            }
            $agent_face     =   wp_get_attachment_image_src($thumb_id, 'agent_picture_thumb');
            if($agent_face){
                $agent_face_img =   $agent_face[0];
            }
        }
        $title  =   get_the_title($agent_id);
        $link   =   esc_url( get_permalink($agent_id) );
        $type   =   get_post_type($agent_id);

        $agent_mobile       = '';
        $agent_email        = '';
        $agent_skype        = '';
        $agent_phone        = '';
        $agent_pitch        = '';
        $agent_facebook     = '';
        $agent_twitter      = '';
        $agent_linkedin     = '';
        $agent_pinterest    = '';
        $agent_instagram    = '';
        $agent_urlc         = '';
        $agent_member       = '';
        $agent_address      = '';
        $agent_youtube      = '';
        $agent_tiktok       = '';
        $agent_telegram     =   '';
        $agent_vimeo        =   '';
        $agent_private_notes=   '';
        $agent_opening_hours='';
        $agent_languages='';
        $agent_taxes='';
        $agent_license='';


        if( $type=='estate_agent' ){
            $agent_mobile       = esc_html( get_post_meta($agent_id, 'agent_mobile', true) );
            $agent_email        = esc_html( get_post_meta($agent_id, 'agent_email', true) );
            $agent_skype        = esc_html( get_post_meta($agent_id, 'agent_skype', true) );
            $agent_phone        = esc_html( get_post_meta($agent_id, 'agent_phone', true) );
            $agent_pitch        = esc_html( get_post_meta($agent_id, 'agent_pitch', true) );
            $agent_facebook     = esc_html( get_post_meta($agent_id, 'agent_facebook', true) );
            $agent_twitter      = esc_html( get_post_meta($agent_id, 'agent_twitter', true) );
            $agent_linkedin     = esc_html( get_post_meta($agent_id, 'agent_linkedin', true) );
            $agent_pinterest    = esc_html( get_post_meta($agent_id, 'agent_pinterest', true) );
            $agent_instagram    = esc_html( get_post_meta($agent_id, 'agent_instagram', true) );
            $agent_urlc         = esc_html( get_post_meta($agent_id, 'agent_website', true) );
            $agent_member       = esc_html(  get_post_meta( $agent_id, 'agent_member' , true) );
            $agent_address      = esc_html(  get_post_meta( $agent_id, 'agent_address' , true) );
           

            $agent_youtube       = esc_html(  get_post_meta( $agent_id, 'agent_youtube' , true) );
            $agent_tiktok        = esc_html(  get_post_meta( $agent_id, 'agent_tiktok' , true) );
            $agent_telegram      = esc_html(  get_post_meta( $agent_id, 'agent_telegram' , true) );
            $agent_vimeo         = esc_html(  get_post_meta( $agent_id, 'agent_vimeo' , true) );
            $agent_private_notes = esc_html(  get_post_meta( $agent_id, 'agent_private_notes' , true) );

            $agent_opening_hours = esc_html(get_post_meta($agent_id, 'developer_opening_hours', true));
            $agent_languages     = esc_html(get_post_meta($agent_id, 'developer_languages', true));
            $agent_license       = esc_html(get_post_meta($agent_id, 'developer_license', true));
            $agent_taxes         = esc_html(get_post_meta($agent_id, 'developer_taxes', true));

        }else if( $type=='estate_agency' ){
  
            $agent_mobile       = esc_html( get_post_meta($agent_id, 'agency_mobile', true) );
            $agent_email        = esc_html( get_post_meta($agent_id, 'agency_email', true) );
            $agent_skype         = esc_html( get_post_meta($agent_id, 'agency_skype', true) );
            $agent_phone         = esc_html( get_post_meta($agent_id, 'agency_phone', true) );
            $agent_pitch         = esc_html( get_post_meta($agent_id, 'agency_pitch', true) );
            $agent_posit         = esc_html( get_post_meta($agent_id, 'agency_position', true) );
            $agent_facebook      = esc_html( get_post_meta($agent_id, 'agency_facebook', true) );
            $agent_twitter       = esc_html( get_post_meta($agent_id, 'agency_twitter', true) );
            $agent_linkedin      = esc_html( get_post_meta($agent_id, 'agency_linkedin', true) );
            $agent_pinterest     = esc_html( get_post_meta($agent_id, 'agency_pinterest', true) );
            $agent_instagram     = esc_html( get_post_meta($agent_id, 'agency_instagram', true) );
            $agent_urlc          = esc_html( get_post_meta($agent_id, 'agency_website', true) );
            $agent_member        = esc_html(  get_post_meta( $agent_id, 'agent_member' , true) );
            $agent_address      = esc_html(  get_post_meta( $agent_id, 'agent_address' , true) );

            $agent_youtube       = esc_html(  get_post_meta( $agent_id, 'agency_youtube' , true) );
            $agent_tiktok        = esc_html(  get_post_meta( $agent_id, 'agency_tiktok' , true) );
            $agent_telegram      = esc_html(  get_post_meta( $agent_id, 'agency_telegram' , true) );
            $agent_vimeo         = esc_html(  get_post_meta( $agent_id, 'agency_vimeo' , true) );
            $agent_private_notes = esc_html(  get_post_meta( $agent_id, 'agency_private_notes' , true) );

            $agent_opening_hours = esc_html(get_post_meta($agent_id, 'agency_opening_hours', true));
            $agent_languages = esc_html(get_post_meta($agent_id, 'agency_languages', true));
            $agent_license = esc_html(get_post_meta($agent_id, 'agency_license', true));
            $agent_taxes = esc_html(get_post_meta($agent_id, 'agency_taxes', true));

        }else if($type=='estate_developer'){
            $agent_mobile       = esc_html( get_post_meta($agent_id, 'developer_mobile', true) );
            $agent_email        = esc_html( get_post_meta($agent_id, 'developer_email', true) );
            $agent_skype         = esc_html( get_post_meta($agent_id, 'developer_skype', true) );
            $agent_phone         = esc_html( get_post_meta($agent_id, 'developer_phone', true) );
            $agent_pitch         = esc_html( get_post_meta($agent_id, 'developer_pitch', true) );
            $agent_posit         = esc_html( get_post_meta($agent_id, 'developer_position', true) );
            $agent_facebook      = esc_html( get_post_meta($agent_id, 'developer_facebook', true) );
            $agent_twitter       = esc_html( get_post_meta($agent_id, 'developer_twitter', true) );
            $agent_linkedin      = esc_html( get_post_meta($agent_id, 'developer_linkedin', true) );
            $agent_pinterest     = esc_html( get_post_meta($agent_id, 'developer_pinterest', true) );
            $agent_instagram     = esc_html( get_post_meta($agent_id, 'developer_instagram', true) );
            $agent_urlc          = esc_html( get_post_meta($agent_id, 'developer_website', true) );
            $agent_member        = esc_html(  get_post_meta( $agent_id, 'agent_member' , true) );
            $agent_address      = esc_html(  get_post_meta( $agent_id, 'agent_address' , true) );

            $agent_youtube       = esc_html(  get_post_meta( $agent_id, 'agent_youtube' , true) );
            $agent_tiktok        = esc_html(  get_post_meta( $agent_id, 'agent_tiktok' , true) );
            $agent_telegram      = esc_html(  get_post_meta( $agent_id, 'agent_telegram' , true) );
            $agent_vimeo         = esc_html(  get_post_meta( $agent_id, 'agent_vimeo' , true) );
            $agent_private_notes = esc_html(  get_post_meta( $agent_id, 'agent_private_notes' , true) );
            $agent_opening_hours = esc_html(get_post_meta($agent_id, 'developer_opening_hours', true));
            $agent_languages     = esc_html(get_post_meta($agent_id, 'developer_languages', true));
            $agent_license       = esc_html(get_post_meta($agent_id, 'developer_license', true));
            $agent_taxes         = esc_html(get_post_meta($agent_id, 'developer_taxes', true));

        }
        $agent_posit        = esc_html( get_post_meta($agent_id, 'agent_position', true) );

        $user_for_id = intval(get_post_meta($agent_id,'user_meda_id',true));
        if($user_for_id!=0){
            $counter            =   count_user_posts($user_for_id,'estate_property',true);
        }


    }else{
        $user_id        =    get_post_field( 'post_author', $propid );
        $one_id         =    $user_id;
        $preview_img    =$agent_face_img=    get_the_author_meta( 'custom_picture',$user_id  );

        if($preview_img==''){
            $preview_img = $agent_face_img=get_theme_file_uri('/img/default-user.png');
        }

        $title               =  get_the_author_meta( 'first_name',$user_id ).' '.get_the_author_meta( 'last_name',$user_id);
        $link                =  '';
        $agent_posit         =  get_the_author_meta( 'title' ,$user_id );
        $agent_mobile        =  get_the_author_meta( 'mobile'  ,$user_id);
        $agent_skype         =  get_the_author_meta( 'skype',$user_id  );
        $agent_phone         =  get_the_author_meta( 'phone',$user_id  );
        $counter             =  count_user_posts($user_id,'estate_property',true);
        $agent_email         =  get_the_author_meta( 'user_email',$user_id  );
        $agent_pitch         =  '';
        $agent_facebook      =  get_the_author_meta( 'facebook',$user_id  );
        $agent_twitter       =  get_the_author_meta( 'twitter',$user_id  );
        $agent_linkedin      =  get_the_author_meta( 'linkedin',$user_id  );
        $agent_pinterest     =  get_the_author_meta( 'pinterest',$user_id  );
        $agent_instagram     =  get_the_author_meta( 'instagram',$user_id  );
        $agent_urlc          =  get_the_author_meta( 'website',$user_id  );

        $agent_youtube       =  get_the_author_meta( 'agent_youtube',$user_id  );
        $agent_tiktok        =  get_the_author_meta( 'agent_tiktok',$user_id  );
        $agent_telegram      =  get_the_author_meta( 'agent_telegram',$user_id  );
        $agent_vimeo         =  get_the_author_meta( 'agent_vimeo',$user_id  );
        $agent_private_notes =  get_the_author_meta( 'agent_private_notes',$user_id  );
        $agent_address       =  get_the_author_meta( 'agent_address',$user_id  );

        $agent_opening_hours = get_the_author_meta( 'developer_opening_hours',$user_id  );
        $agent_languages     = get_the_author_meta( 'developer_languages',$user_id  );
        $agent_license       = get_the_author_meta( 'developer_license',$user_id  );
        $agent_taxes         = get_the_author_meta( 'developer_taxes',$user_id  );
    }



    $all_details=array();
    $all_details['one_id']              =   $one_id;
    $all_details['agent_id']            =   $agent_id;
    $all_details['user_id']             =   $user_id;
    $all_details['realtor_image']       =   $preview_img;
    $all_details['agent_face_img']      =   $agent_face_img;
    $all_details['realtor_name']        =   $title;
    $all_details['link']                =   $link;
    $all_details['email']               =   $agent_email;
    $all_details['realtor_position']    =   $agent_posit;
    $all_details['realtor_mobile']      =   $agent_mobile;
    $all_details['realtor_skype']       =   $agent_skype;
    $all_details['realtor_phone']       =   $agent_phone;
    $all_details['realtor_pitch']       =   $agent_pitch;
    $all_details['realtor_facebook']    =   $agent_facebook;
    $all_details['realtor_twitter']     =   $agent_twitter;
    $all_details['realtor_linkedin']    =   $agent_linkedin;
    $all_details['realtor_pinterest']   =   $agent_pinterest;
    $all_details['realtor_instagram']   =   $agent_instagram;
    $all_details['realtor_urlc']        =   $agent_urlc;
    $all_details['member_of']           =   $agent_member;
    $all_details['agent_address']       =   $agent_address;

    $all_details['realtor_youtube']       =   $agent_youtube;
    $all_details['realtor_tiktok']        =   $agent_tiktok;
    $all_details['realtor_telegram']      =   $agent_telegram;
    $all_details['realtor_vimeo']         =   $agent_vimeo;
    $all_details['realtor_private_notes'] =   $agent_private_notes;

    $all_details['realtor_opening_hours'] =   $agent_opening_hours;
    $all_details['realtor_languages'] =   $agent_languages;
    $all_details['realtor_license'] =   $agent_license;
    $all_details['realtor_taxes'] =   $agent_taxes;
    
    $all_details['counter']         =   $counter;
    return $all_details;
}
endif;



/**
*  return agent/user details
*
* param   $propid = proeprty id
* @var
*/

if( !function_exists('wpestate_return_agent_share_social_icons') ):
    function wpestate_return_agent_share_social_icons($realtor_details,$class='',$element_class=''){

        $return_string= '<div class="'.esc_attr($class).'">';
                         
            if($realtor_details['realtor_facebook']!=''){
                $return_string.= '<a class="'.esc_attr($element_class).'" href="'. esc_url($realtor_details['realtor_facebook']).'" target="_blank"  rel="noopener" ><i class="fab fa-facebook-f"></i></a>';
            }

            if($realtor_details['realtor_twitter']!=''){
                $return_string.= '<a  class="'.esc_attr($element_class).'" href="'.esc_url($realtor_details['realtor_twitter']).'" target="_blank" rel="noopener" ><i class="fa-brands fa-x-twitter"></i></a>';
            }
            if($realtor_details['realtor_linkedin']!=''){
                $return_string.= '<a class="'.esc_attr($element_class).'" href="'.esc_url($realtor_details['realtor_linkedin']).'" target="_blank" rel="noopener" ><i class="fab fa-linkedin"></i></a>';
            }
            if($realtor_details['realtor_pinterest']!=''){
                $return_string.= '<a class="'.esc_attr($element_class).'" href="'. esc_url($realtor_details['realtor_pinterest']).'" target="_blank" rel="noopener" ><i class="fab fa-pinterest"></i></a>';
            }
            if($realtor_details['realtor_instagram']!=''){
                $return_string.= '<a class="'.esc_attr($element_class).'" href="'. esc_url($realtor_details['realtor_instagram']).'" target="_blank" rel="noopener" ><i class="fab fa-instagram"></i></a>';
            }
            
            if($realtor_details['realtor_youtube']!=''){
                $return_string.= '<a class="'.esc_attr($element_class).'"  href="'. esc_url($realtor_details['realtor_youtube']).'" target="_blank" rel="noopener" ><i class="fa-brands fa-youtube"></i></a>';
            }
             
            if($realtor_details['realtor_telegram']!=''){
                $return_string.= '<a class="'.esc_attr($element_class).'" href="'. esc_url($realtor_details['realtor_telegram']).'" target="_blank" rel="noopener"><i class="fa-brands fa-telegram"></i></a>';
            }
             
            if($realtor_details['realtor_vimeo']!=''){
                $return_string.= '<a class="'.esc_attr($element_class).'" href="'. esc_url($realtor_details['realtor_vimeo']).'" target="_blank" rel="noopener"><i class="fa-brands fa-vimeo-v"></i></a>';
            }
             
            if($realtor_details['realtor_tiktok']!=''){
                $return_string.= ' <a class="'.esc_attr($element_class).'"  href="'. esc_url($realtor_details['realtor_tiktok']).'" target="_blank" rel="noopener"><i class="fa-brands fa-tiktok"></i></a>';
            }
 
        $return_string.= '</div>';
        return $return_string;
    }

endif;




/**
*  return contact details
*
* param   $propid = proeprty id
* @var
*/



if( !function_exists('wpestate_return_agent_contact_details') ):
function wpestate_return_agent_contact_details($realtor_details){
    $return_string='';
    
    if ($realtor_details['realtor_phone']!='') {
        $return_string.= '<div class="agent_detail agent_phone_class">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M5.58989 5.35298L4.00124 2L6.84747 6.44072L5.73961 7.97254C6.33837 9.03062 7.29261 9.88975 8.46524 10.4265L9.14108 10.0188L10 11.3603L9.32929 11.7649L9.32758 11.7665C9.10866 11.8994 8.85535 11.9786 8.59246 11.9962C8.32948 12.0139 8.06602 11.9694 7.82787 11.867L7.82188 11.8647C6.2241 11.1662 4.93175 10.0029 4.15181 8.56125L4.14924 8.55506C4.03588 8.34114 3.98568 8.10428 4.00352 7.86758C4.02136 7.63088 4.10663 7.40238 4.25105 7.20431L5.58989 5.35298Z"/>
        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.86005 9.91043L6.71604 6.21123L6.92964 5.88483C7.05849 5.68786 7.13665 5.46208 7.15717 5.22761C7.17769 4.99312 7.13992 4.7572 7.04724 4.54084L5.47363 0.86806V0.86646C5.3526 0.582297 5.1425 0.34516 4.87498 0.19077C4.60747 0.0363812 4.29703 -0.026898 3.99042 0.0104649H3.98562C2.88403 0.151205 1.87154 0.688769 1.13785 1.52244C0.404157 2.35612 -0.000388779 3.4287 2.80363e-07 4.53924C0.000424591 7.57897 1.20826 10.494 3.35784 12.6433C5.50743 14.7926 8.42269 16 11.4625 16C12.5726 15.9998 13.6446 15.595 14.4778 14.8614C15.311 14.1278 15.8482 13.1156 15.9889 12.0144L15.9897 12.0096C16.0267 11.7031 15.9633 11.393 15.809 11.1256C15.6546 10.8583 15.4176 10.6483 15.1337 10.5272H15.1321L11.4625 8.95364C11.2442 8.86028 11.0062 8.82284 10.7698 8.8446C10.5335 8.86644 10.3063 8.94676 10.1089 9.07843L8.86086 9.91043H8.86005ZM9.66406 11.2976L11.4625 14.4C8.84677 14.4 6.33823 13.3609 4.48866 11.5114C2.63909 9.66179 1.60001 7.1533 1.60001 4.53764C1.59998 3.83989 1.84612 3.1645 2.29508 2.63036C2.74404 2.09624 3.36705 1.73762 4.05443 1.61766L6.71604 6.21123L8.86005 9.91043L9.66406 11.2976ZM9.66406 11.2976L11.4625 14.4C12.1602 14.4 12.8356 14.1539 13.3698 13.705C13.9039 13.2559 14.2625 12.633 14.3825 11.9456L10.9185 10.4616L9.66406 11.2976Z"/>
        </svg>        
        <a href="tel:'.esc_html($realtor_details['realtor_phone']).'">'.esc_html($realtor_details['realtor_phone']).'</a></div>';
    }
    if ($realtor_details['realtor_mobile']!='') {
        $return_string.= '<div class="agent_detail agent_mobile_class">
        <svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.68387 0.00976562H2.25812C1.02049 0.00976562 0.0303955 0.824015 0.0303955 1.84182V14.0556C0.0303955 15.0734 1.02049 15.8877 2.25812 15.8877H9.68387C10.9215 15.8877 11.9116 15.0734 11.9116 14.0556V1.84182C11.9116 0.824015 10.9215 0.00976562 9.68387 0.00976562ZM7.20861 13.6484H4.73337C4.31257 13.6484 3.99079 13.3839 3.99079 13.0377C3.99079 12.6917 4.31257 12.427 4.73337 12.427H7.20861C7.62941 12.427 7.95118 12.6917 7.95118 13.0377C7.95118 13.3839 7.62941 13.6484 7.20861 13.6484ZM10.4264 11.0021H1.51555V2.04539H10.4264V11.0021Z"/>
        </svg>
        <a href="tel:'.esc_html($realtor_details['realtor_mobile']). '">'.esc_html($realtor_details['realtor_mobile']).'</a></div>';
    }

    if ($realtor_details['email']!='') {
        $email_display = antispambot($realtor_details['email']);
        $return_string.= '<div class="agent_detail agent_email_class">
        <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13.7489 0.145508H2.2512C1.09771 0.145508 0.16748 1.0546 0.16748 2.18187V9.81823C0.16748 10.9455 1.09771 11.8546 2.2512 11.8546H13.7489C14.9024 11.8546 15.8326 10.9455 15.8326 9.81823V2.18187C15.8326 1.0546 14.9024 0.145508 13.7489 0.145508ZM13.7489 1.96369C13.7861 1.96369 13.8419 1.98187 13.8791 2.00005L8.13027 5.50914C8.05585 5.54551 7.96283 5.54551 7.88841 5.50914L2.13958 2.00005C2.17678 1.98187 2.21399 1.96369 2.26981 1.96369H13.7489ZM13.7489 10.0364H2.2512C2.12097 10.0364 2.02795 9.94551 2.02795 9.81823V4.07278L6.88376 7.0546C7.21865 7.2546 7.60934 7.36369 8.00004 7.36369C8.39074 7.36369 8.78144 7.2546 9.11632 7.0546L13.9721 4.07278V9.81823C13.9721 9.94551 13.8791 10.0364 13.7489 10.0364Z"/>
        </svg>
        <a href="mailto:' . antispambot( esc_attr( $realtor_details['email']) ) . '">' . $email_display . '</a></div>';
    }

    if ($realtor_details['realtor_skype']!='') {
        $return_string.= '<div class="agent_detail agent_skype_class">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.45455 4C1.45455 2.59418 2.59418 1.45455 4 1.45455C4.69933 1.45455 5.33161 1.73561 5.79241 2.19263L6.08897 2.48675L6.49245 2.37884C6.97244 2.25047 7.47767 2.18182 8 2.18182C11.2133 2.18182 13.8182 4.78671 13.8182 8C13.8182 8.52233 13.7495 9.02756 13.6212 9.50756L13.5132 9.91105L13.8073 10.2076C14.2644 10.6684 14.5455 11.3007 14.5455 12C14.5455 13.4058 13.4058 14.5455 12 14.5455C11.3007 14.5455 10.6684 14.2644 10.2076 13.8073L9.91105 13.5132L9.50756 13.6212C9.02756 13.7495 8.52233 13.8182 8 13.8182C4.78671 13.8182 2.18182 11.2133 2.18182 8C2.18182 7.47767 2.25047 6.97244 2.37884 6.49245L2.48675 6.08897L2.19263 5.79241C1.73561 5.33161 1.45455 4.69933 1.45455 4ZM4 0C1.79086 0 0 1.79086 0 4C0 4.94788 0.330422 5.81964 0.881265 6.50485C0.780269 6.98793 0.727273 7.48815 0.727273 8C0.727273 12.0166 3.98338 15.2727 8 15.2727C8.51185 15.2727 9.01207 15.2197 9.49513 15.1188C10.1804 15.6696 11.0521 16 12 16C14.2092 16 16 14.2092 16 12C16 11.0521 15.6696 10.1804 15.1188 9.49513C15.2197 9.01207 15.2727 8.51185 15.2727 8C15.2727 3.98338 12.0166 0.727273 8 0.727273C7.48815 0.727273 6.98793 0.780269 6.50485 0.881265C5.81964 0.330422 4.94788 0 4 0ZM7.01236 6.05834C7.1088 5.87436 7.43891 5.63636 7.95091 5.63636C8.82829 5.63636 9.31229 5.97973 9.53484 6.14959C9.85411 6.39326 10.3105 6.33194 10.5542 6.01263C10.7979 5.69333 10.7366 5.23695 10.4172 4.99327C10.0612 4.72156 9.27084 4.18181 7.95091 4.18182C7.028 4.18182 6.12729 4.6138 5.69035 5.44741C5.37457 6.04986 5.4257 6.70727 5.74648 7.24087C6.05799 7.75898 6.59862 8.12924 7.23564 8.3008L8.36553 8.60516C8.80073 8.72233 9.46175 9.12676 9.22371 9.67164C9.08022 10.0002 8.57156 10.3636 7.8224 10.3636C7.17484 10.3636 6.60342 10.2517 6.07761 9.8504C5.7583 9.60676 5.30192 9.66807 5.05825 9.98735C4.81457 10.3067 4.87589 10.7631 5.1952 11.0067C5.89969 11.5444 6.63309 11.8182 7.6616 11.8182C8.81004 11.8182 10.0632 11.3836 10.5567 10.2539C10.8471 9.58895 10.7513 8.89193 10.3884 8.33222C9.83004 7.47091 8.86815 7.22204 7.9368 6.98102C7.82858 6.95302 7.72065 6.92509 7.61389 6.89629C7.23135 6.79324 6.78407 6.4939 7.01236 6.05834Z"/>
        </svg>
    ' . esc_html($realtor_details['realtor_skype'] ). '</div>';
    }

    if ($realtor_details['realtor_urlc']!='') {
        $return_string.= '<div class="agent_detail agent_web_class">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g clip-path="url(#clip0_332_14)">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M16 8C16 12.4184 12.4184 16 8 16C3.5816 16 0 12.4184 0 8C0 3.5816 3.5816 0 8 0C12.4184 0 16 3.5816 16 8ZM7.3056 1.9456C7.5856 1.6792 7.8184 1.6 8 1.6C8.1816 1.6 8.4144 1.68 8.6944 1.9456C8.9776 2.2152 9.2696 2.6432 9.532 3.2336C10.04 4.3776 10.38 5.9984 10.3992 7.8336C9.6496 7.9408 8.844 8 8 8C7.156 8 6.3504 7.9408 5.6008 7.8328C5.62 5.9984 5.96 4.3776 6.468 3.2336C6.7304 2.6432 7.0224 2.2152 7.3056 1.9456ZM4.008 7.512C4.0608 5.6272 4.4232 3.8984 5.0072 2.584C5.05476 2.47641 5.10491 2.36998 5.1576 2.2648C4.3033 2.68909 3.55442 3.29864 2.96554 4.04902C2.37667 4.79941 1.96263 5.67172 1.7536 6.6024C2.3456 6.9608 3.1112 7.2736 4.008 7.512ZM1.608 8.34C2.3168 8.68 3.1384 8.96 4.0392 9.168C4.1512 10.7872 4.492 12.2616 5.0056 13.416C5.0536 13.5248 5.104 13.6312 5.156 13.7352C4.13954 13.231 3.27534 12.4658 2.6519 11.5178C2.02847 10.5697 1.66821 9.47304 1.608 8.34ZM5.668 9.456C6.416 9.5504 7.1976 9.6 7.9992 9.6C8.8008 9.6 9.5832 9.5504 10.3304 9.456C10.2048 10.7576 9.9168 11.8984 9.5304 12.7664C9.2688 13.3568 8.9768 13.7848 8.6936 14.0544C8.4136 14.3208 8.1808 14.4 7.9992 14.4C7.8176 14.4 7.5848 14.32 7.3048 14.0544C7.0216 13.7848 6.7296 13.3568 6.4672 12.7664C6.0816 11.8992 5.7936 10.7576 5.668 9.4568V9.456ZM11.9592 9.168C11.8472 10.7872 11.5064 12.2616 10.9928 13.416C10.9452 13.5236 10.8951 13.63 10.8424 13.7352C11.8589 13.231 12.723 12.4658 13.3465 11.5178C13.9699 10.5697 14.3302 9.47304 14.3904 8.34C13.6816 8.68 12.86 8.96 11.9592 9.168ZM14.2464 6.6024C14.0374 5.67172 13.6234 4.79941 13.0345 4.04902C12.4456 3.29864 11.6967 2.68909 10.8424 2.2648C10.8944 2.3688 10.9448 2.4752 10.9928 2.584C11.5768 3.8984 11.9384 5.6272 11.9928 7.512C12.8888 7.2736 13.6552 6.96 14.2464 6.6024Z"/>
            </g>
            <defs>
            <clipPath id="clip0_332_14">
            <rect width="16" height="16" fill="white"/>
            </clipPath>
            </defs>
        </svg>
        <a href="'.esc_url($realtor_details['realtor_urlc']).'" target="_blank">'.esc_html($realtor_details['realtor_urlc']).'</a></div>';
    }
    
    if($realtor_details['member_of']!=''){
        $return_string.= '<div class="agent_detail agent_web_member_of_class"><strong>'.esc_html__('Member of:','wpresidence').'</strong> '.esc_html($realtor_details['member_of']).'</div>';
  
    }

    return $return_string;
}
endif;


/**
*  return contact details
*
* param   $propid = proeprty id
* @var
*/



if( !function_exists('wpestate_return_agent_reviews_bar') ):
    function wpestate_return_agent_reviews_bar($post_id){

        $postID = $post_id;

        $return_string      = '';

        // Get total count for average rating and pagination
        $total_reviews = get_posts(array(
            'post_type' => 'estate_review',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'attached_to',
                    'value' => $post_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1,
            'fields' => 'ids'
        ));

        // Calculate average rating from all reviews
        $total_rating = 0;
        foreach ($total_reviews as $review_id) {
            $rating = get_post_meta($review_id, 'reviewer_rating', true);
            $total_rating += intval($rating);
        }
        
        $total_count = count($total_reviews);
        if ( $total_count === 0 ) {
            $average_rating = 0; // Avoid division by zero
        } else {
            $average_rating = round($total_rating / $total_count, 1);
        }

        ob_start();

        ?>
        <span class="property_ratings">
            <?php echo wpestate_display_rating_stars($average_rating); ?>
        </span>
        <?php
        echo esc_html($total_count . ' ' . __('Reviews', 'wpresidence'));
        ?>
        <?php
        if ( $total_count != 0 ) {
            $return_string .= ob_get_clean();
        } else {
            ob_get_clean();
        }


       return $return_string; 	
    }
endif;


/**
 * Display Agent Listings
 * 
 * Function to display the listings of an agent in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage AgentProfile
 * @since 1.0.0
 * 
 * @param int $postID The post ID of the agent
 * @return string The HTML output for agent listings
 */
function wpresidence_display_agent_listings($postID,$settings=array()) {


    // Validate input
    if (empty($postID) || !is_numeric($postID)) {
        return '';
    }
   

    // if we have section title values from elementor 
    $section_title= esc_html__('My Listings', 'wpresidence');

    if(isset($settings['agent_listings_title'])){
        $section_title=$settings['agent_listings_title'];
    }
            
      
    
    // Start output buffering
    ob_start();
    
    // Retrieve the number of properties to display
    $prop_no = intval(wpresidence_get_option('wp_estate_prop_no', ''));

    if(isset($settings['listings_per_page'])){
        $prop_no=intval($settings['listings_per_page']);
    }
    
    // Get the agent ID
    $agent_id = get_post_meta($postID, 'user_meda_id', true) ?: -1;

    // Check if property slider is enabled
    $wpestate_property_unit_slider = wpresidence_get_option('wp_estate_prop_list_slider', '');
  
    
    // Determine the current page
    $paged = isset($_GET['pagelist']) ? intval($_GET['pagelist']) : 1;

    
    // Prepare query arguments based on agent type
    $args = [
        'post_type'         => 'estate_property',
        'paged'             => $paged,
        'posts_per_page'    => $prop_no,
        'post_status'       => 'publish',
        'meta_key'          => 'prop_featured',
        'orderby'           => 'meta_value',
        'order'             => 'DESC',
    ];

    
    // Set query parameters based on agent type
    if ($agent_id === -1) {
        $args['meta_query'] = [
            [
                'key'     => 'property_agent',
                'value'   => $postID,
            ],
        ];
    
    } else {
        $args['author'] = $agent_id;

    }
    

    
    // Get filtered properties
    $prop_selection = wpestate_return_filtered_by_order($args);
    $found_posts = $prop_selection->found_posts ?? 0;
    $post_count = $prop_selection->post_count ?? 0;

    
    // Get property categories and prepare tab terms
    $tab_terms = [];
    $transient_agent_id = $agent_id === -1 ? "meta_property_agent_{$postID}" : "custom_post_{$agent_id}";
   
    $terms = wpestate_get_cached_terms('property_category');

    
    foreach ($terms as $term) {
        $term_args = [
            'post_type'         => 'estate_property',
            'posts_per_page'    => -1,
            'post_status'       => 'publish',
            'fields'            => 'ids',
            // Allow language filters so translated term counts stay accurate.
            'suppress_filters'  => false,
            'tax_query'         => [
                [
                    'taxonomy' => 'property_category',
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                ],
            ],
        ];
        
        if ($agent_id === -1) {
            $term_args['meta_query'] = $args['meta_query'];
        } else {
            $term_args['author'] = $agent_id;
        }
        
        $all_posts = get_posts($term_args);
        
        if (!empty($all_posts)) {
            $tab_terms[$term->term_id] = [
                'name'  => $term->name,
                'slug'  => $term->slug,
                'count' => count($all_posts)
            ];
          
        } else {
          
        }
    }
    
 
    $wpestate_options = array();


    // Create nonce for AJAX
    $ajax_nonce = wp_create_nonce("wpestate_developer_listing_nonce");
  
    ?>
    <input type="hidden" id="wpestate_developer_listing_nonce" value="<?php echo esc_attr($ajax_nonce); ?>" />
    
    
    <div class="wpresidence_realtor_listings_wrapper single_listing_block" data-listings_per_page="<?php echo intval($prop_no); ?>"  data-rownumber="<?php echo intval($settings['rownumber']); ?>">
    
    <?php
    
    // Display agent listings
    if ($prop_selection->have_posts()):
      
        ?>

        

            <h3 class="agent_listings_title"><?php  echo esc_html($section_title) ;?></h3>
            
            <?php
            // Include the term bar template
           
            get_template_part('templates/realtor_templates/agent-term-bar', null, [
                'prop_selection' => $prop_selection,
                'tab_terms' => $tab_terms
            ]);
            ?>
            
            <div class="agency_listings_wrapper row">
                <?php 
             
                wpresidence_display_property_list_as_html($prop_selection, $wpestate_options, 'shortcode',$settings); 
                ?>
            </div>
            
            <div class="spinner" id="listing_loader">
                <div class="new_prelader"></div>
            </div>
            
            <div class="load_more_ajax_cont">
                <input type="button" class="wpresidence_button listing_load_more" value="<?php esc_attr_e('Load More Properties', 'wpresidence'); ?>">
            </div>
        
        <?php
    else:

    endif;
    ?>


    </div>
    <?php
    // Get the output and clean buffer
    $output = ob_get_clean();
    $output_length = strlen($output);
  
    
    // Return the HTML output
    return $output;
}

/**
 * Display Realtor Listings
 * 
 * Function to display the listings of an realtor in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage RealtorProfile
 * @since 1.0.0
 * 
 * @param int $postID The post ID of the Realtor
 * @param array $settings Optional settings for the display
 * @return string The HTML output for Realtor listings
 */
function wpresidence_display_realtor_listings($postID,$settings=array()) {


    // Validate input
    if (empty($postID) || !is_numeric($postID)) {
        return '';
    }

    $realtor_type = get_post_type($postID);
   

    // if we have section title values from elementor 
    $section_title= esc_html__('My Listings', 'wpresidence');

    if(isset($settings['agent_listings_title'])){
        $section_title=$settings['agent_listings_title'];
    }

    // Retrieve realtor information
    $realtor_id = get_post_meta($postID, 'user_meda_id', true);
    $agent_list = (array)get_user_meta($realtor_id, 'current_agent_list', true);
    $agent_list[] = $realtor_id;
    $agent_list=array_filter($agent_list);
    $prop_no = intval(wpresidence_get_option('wp_estate_prop_no', ''));

    if ( !empty( $realtor_id ) ) {
        $agent_list = (array)get_user_meta($realtor_id, 'current_agent_list', true);
        $agent_list[] = $realtor_id;
        $agent_list = array_filter($agent_list);
    }
    
    // Start output buffering
    ob_start();
    
    // Retrieve the number of properties to display
    $prop_no = intval(wpresidence_get_option('wp_estate_prop_no', ''));

    if(isset($settings['listings_per_page'])){
        $prop_no=intval($settings['listings_per_page']);
    }
    
    // Determine the current page
    $paged = isset($_GET['pagelist']) ? intval($_GET['pagelist']) : 1;

    
    // Prepare query arguments based on agent type
    $args = [
        'post_type'         => 'estate_property',
        'paged'             => $paged,
        'posts_per_page'    => $prop_no,
        'post_status'       => 'publish',
        'meta_key'          => 'prop_featured',
        'orderby'           => 'meta_value',
        'order'             => 'DESC',
    ];

    // Adjust query based on agent list
    if ( empty( $realtor_id ) || empty( $agent_list ) ) {
        $args = array_merge($args, array(
            'meta_query' => array(
                array(
                    'key'   => 'property_agent',
                    'value' => $postID,
                ),
            ),
        ));
    } else {
        $args = array_merge($args, array(
            'author__in' => $agent_list,
        ));
    }

    // Get filtered properties
    $prop_selection = wpestate_return_filtered_by_order($args);
    $found_posts = $prop_selection->found_posts ?? 0;
    $post_count = $prop_selection->post_count ?? 0;

    
    // Get property categories and prepare tab terms
    $tab_terms = [];
    $transient_agent_id = $realtor_id === -1 ? "meta_property_agent_{$postID}" : "custom_post_{$realtor_id}";
    $taxonomy='property_category';
    if($realtor_type=='estate_agency' || $realtor_type=='estate_agency' ){
        $taxonomy='property_action_category';
    }
    $terms = wpestate_get_cached_terms($taxonomy);

    
    $term_filter_args = [
        'post_type'      => 'estate_property',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'post_status'    => 'publish',
        // Allow language filters so translated term counts stay accurate.
        'suppress_filters' => false,
    ];

    if ( empty( $realtor_id ) || empty( $agent_list ) ) {
        $term_filter_args['meta_query'] = [
            [
                'key'   => 'property_agent',
                'value' => $postID,
            ],
        ];
    } else {
        $term_filter_args['author__in'] = $agent_list;
    }

    foreach ($terms as $term) {
        $term_args = $term_filter_args;
        $term_args['tax_query'] = [
            [
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $term->term_id,
            ],
        ];

        $all_posts = get_posts($term_args);

        if (!empty($all_posts)) {
            $tab_terms[$term->term_id] = [
                'name'  => $term->name,
                'slug'  => $term->slug,
                'count' => count($all_posts)
            ];
          
        } else {
          
        }
    }
    
 
    $wpestate_options = array();


    // Create nonce for AJAX
    $ajax_nonce = wp_create_nonce("wpestate_developer_listing_nonce");
  
    ?>
    <input type="hidden" id="wpestate_developer_listing_nonce" value="<?php echo esc_attr($ajax_nonce); ?>" />
    
    
    <div class="wpresidence_realtor_listings_wrapper  single_listing_block" data-listings_per_page="<?php echo intval($prop_no); ?>"  data-rownumber="<?php echo intval($settings['rownumber']); ?>">
    
    <?php
    
    // Display agent listings
    if ($prop_selection->have_posts()):
      
        ?>

        

            <h3 class="agent_listings_title"><?php  echo esc_html($section_title) ;?></h3>
            
            <?php
            // Include the term bar template
           
            get_template_part('templates/realtor_templates/agent-term-bar', null, [
                'prop_selection' => $prop_selection,
                'tab_terms' => $tab_terms,
                'post_id' => $postID
            ]);
            ?>
            
            <div class="agency_listings_wrapper w-100 row">
                <?php 
             
                wpresidence_display_property_list_as_html($prop_selection, $wpestate_options, 'shortcode',$settings); 
                ?>
            </div>
            
            <div class="spinner" id="listing_loader">
                <div class="new_prelader"></div>
            </div>
            
            <div class="load_more_ajax_cont">
                <input type="button" class="wpresidence_button listing_load_more" value="<?php esc_attr_e('Load More Properties', 'wpresidence'); ?>">
            </div>
        
        <?php
    else:

    endif;
    ?>


    </div>
    <?php
    // Get the output and clean buffer
    $output = ob_get_clean();
    $output_length = strlen($output);
  
    
    // Return the HTML output
    return $output;
}


/**
 * Prepare agent Custom Fields for display
 *
 * @param string $agentID The Agent ID.
 * @return string The agent Custom fields html block.
 */
function wpresidence_display_agent_custom_fields($agentID)  {

    $agent_custom_data = get_post_meta( $agentID, 'agent_custom_data', true );
    $post_type = get_post_type($agentID);

    if ( !is_array($agent_custom_data) ) {
        $agent_custom_data = array();
    }

    if ( function_exists('get_field') ) :
        $groups = acf_get_field_groups(array('post_type' => $post_type));
        if (is_array($groups) && count($groups) > 0) {
            foreach ($groups as $group) {
                $fields = acf_get_fields($group['key']);
                if (is_array($fields) && count($fields) > 0) {
                    foreach ($fields as $field) {
                        $field_value = get_field($field['key'], $agentID);
                        if ($field_value) {
                            $agent_custom_data[] = array(
                                'type'  => $field['type'],
                                'label' => $field['label'],
                                'value' => $field_value
                            );
                        }
                    }
                }
            }
        }
    endif;

    ob_start();
    // Check if the custom data is an array and not empty
    if ( is_array( $agent_custom_data ) && ! empty( $agent_custom_data ) ) {
        echo '<div class="custom_parameter_wrapper row">';

        // Loop through each custom data item
        foreach ( $agent_custom_data as $data_item ) {
            // Ensure label and value keys exist
            if ( isset( $data_item['label'] ) && isset( $data_item['value'] ) ) {
                ?>
                <div class="col-md-4">
                    <span class="custom_parameter_label">
                        <?php echo esc_html( $data_item['label'] ); ?>
                    </span>
                    <span class="custom_parameter_value">
                        <?php echo esc_html( $data_item['value'] ); ?>
                    </span>
                </div>
                <?php
            }
        }

        echo '</div>';
    }

    $output = ob_get_clean();
    $output_length = strlen($output);

    return $output;

}

/**
 * Display Agent Contact Details
 *
 * @param int $agentID The Agent ID.
 * @return void Outputs the agent contact details.
 */
function wpestate_display_agency_contact_details( $agentID )   {

    $display_details = array(
        'address' => __('Address:', 'wpresidence'),
        'email'   => __('Email:', 'wpresidence'),
        'mobile'  => __('Mobile:', 'wpresidence'),
        'phone'   => __('Phone:', 'wpresidence'),
        'skype'   => __('Skype:', 'wpresidence')
    );

    $agency_details = array(
        'address' => get_post_meta($agentID, 'agency_address', true),
        'skype'   => get_post_meta($agentID, 'agency_skype', true),
        'phone'   => get_post_meta($agentID, 'agency_phone', true),
        'mobile'  => get_post_meta($agentID, 'agency_mobile', true),
        'email'   => get_post_meta($agentID, 'agency_email', true)
    );

    foreach ($display_details as $key => $label) {
        if (!empty($agency_details[$key])) {
            echo '<div class="agent_custom_detail_wrapper agency_' . esc_attr($key) . '">';
            echo '<strong>' . esc_html($label) . '</strong> ';
            
            if ($key === 'email' || $key === 'mobile' || $key === 'phone') {
                $href = ($key === 'email') ? 'mailto:' : 'tel:';
                echo '<a href="' .antispambot( esc_attr($href . $agency_details[$key]) ). '">';
                echo esc_html( antispambot( $agency_details[$key]) );
                echo '</a>';
            } else {
                echo '<span>' . esc_html($agency_details[$key]) . '</span>';
            }
            
            echo '</div>';
        }
    }

}

/**
 * Display Developer Contact Details
 *
 * @param int $agentID The Agent ID.
 * @return void Outputs the agent contact details.
 */
function wpestate_display_developer_contact_details( $agentID )   {

    $details = array(
        'address' => esc_html__('Address:', 'wpresidence'),
        'email'   => esc_html__('Email:', 'wpresidence'),
        'mobile'  => esc_html__('Mobile:', 'wpresidence'),
        'phone'   => esc_html__('Phone:', 'wpresidence')
    );

    $developer_meta = array(
        'skype'          => get_post_meta($agentID, 'developer_skype', true),
        'phone'          => get_post_meta($agentID, 'developer_phone', true),
        'mobile'         => get_post_meta($agentID, 'developer_mobile', true),
        'email'          => get_post_meta($agentID, 'developer_email', true),
        'position'       => get_post_meta($agentID, 'developer_position', true),
        'opening_hours'  => get_post_meta($agentID, 'developer_opening_hours', true),
        'address'        => get_post_meta($agentID, 'developer_address', true),
        'languages'      => get_post_meta($agentID, 'developer_languages', true),
        'license'        => get_post_meta($agentID, 'developer_license', true),
        'taxes'          => get_post_meta($agentID, 'developer_taxes', true),
        'website'        => get_post_meta($agentID, 'developer_website', true)
    );

   foreach ($details as $key => $label) {
        if (!empty($developer_meta[$key])) {
            $value = $developer_meta[$key];
            if ($key === 'email') {
                $email_display = $value;
                $email_link    = antispambot(esc_attr($value));
                $value = '<a href="mailto:' . $email_link . '">' . $email_display . '</a>';
            } elseif (in_array($key, array('mobile', 'phone'))) {
                $value = '<a href="tel:' . esc_attr($value) . '">' . esc_html($value) . '</a>';
            } else {
                $value = '<span>' . esc_html($value) . '</span>';
            }
            echo '<div class="agent_custom_detail_wrapper agency_' . esc_attr($key) . '"><strong>' . $label . '</strong> ' . $value . '</div>';
        }
    }

}

/**
 * Display Agent Taxonomies
 *
 * @param int $agentID The Agent ID.
 * @return string The agent taxonomies html block.
 */
function wpestate_display_agent_taxonomies( $agentID )    {

    $taxonomies = get_object_taxonomies( get_post_type( $agentID ) );

    ob_start();
?>
     <div class="agency_taxonomy">
            <?php
            // $taxonomies = array('county_state_agency', 'city_agency', 'area_agency', 'category_agency', 'action_category_agency');
            foreach ($taxonomies as $taxonomy) {
                echo get_the_term_list($agentID, $taxonomy, '', '', '');
            }
            ?>
    </div>

<?php
    $output = ob_get_clean();

    return $output;

}

/**
 * Display Agency Agents
 *
 * @param int $agencyID The Agency ID.
 * @param array $settings Optional settings for the display.
 * @return string The HTML output of the agency agents.
 */
function wpestate_display_agency_agents_html( $agencyID, $settings = array() )  {

    global $wpestate_options;

    // Get the agency user ID
    $user_agency = get_post_meta($agencyID, 'user_meda_id', true);
    $output = '';


    // Only proceed if a valid agency user ID is found
    if (!empty($user_agency)) {
        // Set up query arguments
        $args = array(
            'post_type'      => 'estate_agent',
            'author'         => $user_agency,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );

        // Run the query
        $agent_query = new WP_Query($args);

        // Start output buffering
        ob_start();

        // Check if there are agents to display
        if ($agent_query->have_posts()) :
            ?>
            <div class="wpresidence_realtor_listings_wrapper agency_agents_wrapper row">
                <h3 class="agent_listings_title"><?php esc_html_e($settings['label'], 'wpresidence'); ?></h3>
                <?php
                // Display agent list
                wpresidence_display_agent_list_as_html($agent_query, 'estate_agent', $wpestate_options, 'agency_agents');
                ?>
            </div>
            <?php
        endif;

        // End output buffering and echo the content
        $output = ob_get_clean();
        $output_length = strlen($output);

    }

    return $output;

}

/**
 * Load Agent Reviews Template
 *
 * @param array $settings Optional settings for the template.
 * @return string The HTML output of the agent reviews template.
 */
function wpestate_load_agent_reviews_template( $post_id, $settings = array() ) {

    global $post;

    ob_start();

    // include(locate_template('/templates/realtor_templates/agent_reviews.php'));
    get_template_part('templates/reviews/reviews');

    $output = ob_get_clean();
    $output_length = strlen($output);
    return $output;
}

/* * Load Agency Map Template
 *
 * @param array $settings Optional settings for the template.
 * @return string The HTML output of the agency map template.
 */
function wpestate_load_agenncy_map_template( $settings = array() ) {

    global $post;

    ob_start();

    include(locate_template('/templates/agency_templates/agency_map.php'));

    $output = ob_get_clean();
    $output_length = strlen($output);
    return $output;
}
