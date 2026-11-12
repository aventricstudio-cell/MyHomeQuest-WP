<?php
/**
 * AJAX callbacks and response builders used across the theme.
 *
 * This file centralizes front-end and dashboard AJAX handlers for
 * filtering, profile updates, payments, modals, and utility actions.
 *
 * @package WpResidence
 */

// Resolve the configured AJAX hook prefix used by the theme.
$handler=wpestate_return_ajax_handler();

// Register modal details AJAX handlers for logged-in and guest visitors.
add_action($handler.'_wpestate_property_modal_listing_details_second','wpestate_property_modal_listing_details_second');
add_action($handler.'_nopriv_wpestate_property_modal_listing_details_second','wpestate_property_modal_listing_details_second');

if( !function_exists('wpestate_property_modal_listing_details_second') ):
    /**
     * Property modal listing details second.
     *
     * @return mixed
     */
    function wpestate_property_modal_listing_details_second(){
        // Step 1: Prepare request data and build the response payload.

        $post_id                    =   intval($_POST['postid']);
        $wpestate_prop_all_details  =   get_post_custom($post_id);

                $response_array =  array(
                    'response'      =>  true,
                    'yelp'          =>  wpestate_modal_yelp($post_id),
                    'floor_plans'   =>  wpestate_modal_floor($post_id,$wpestate_prop_all_details),
                   // 'walkscore'     =>  wpestate_modal_walscore($post_id,$wpestate_prop_all_details,''),

                    'map'           =>  '<h4 class="panel-title">'.esc_html__('Map', 'wpresidence').'</h4>'.wpestate_property_page_map_modal_function($post_id),
                    );
                $show_morgage_calculator= wpestate_check_category_for_morgage($post_id );
                if( $show_morgage_calculator=='yes' ) {
                    $response_array['mortgage']     =  wpestate_property_modal_mortgage($post_id,$wpestate_prop_all_details);
                }
                print json_encode( $response_array );
        die();

    }
endif;








add_action($handler.'_wpestate_property_modal_listing_details','wpestate_property_modal_listing_details');
add_action($handler.'_nopriv_wpestate_property_modal_listing_details','wpestate_property_modal_listing_details');

if( !function_exists('wpestate_property_modal_listing_details') ):
     /**
      * Property modal listing details.
      *
      * @return mixed
      */
     function wpestate_property_modal_listing_details(){
         // Step 1: Prepare request data and build the response payload.

        $post_id                    =   intval($_POST['postid']);
        $wpestate_prop_all_details  =   get_post_custom($post_id);


        $attachemnt_img     =   wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
        $images             =   array();
        $full_images        =   array();
      
        $post_attachments = wpestate_generate_property_slider_image_ids( $post_id, false );


        foreach ($post_attachments as $attachment) {
            if (!wp_attachment_is_image($attachment)) {
                continue; // Skip this attachment if it's not an image
            }

            $attachemnt_img =   wp_get_attachment_image_src($attachment, 'full');
            $full_images[]  =   $attachemnt_img[0];
            $attachemnt_img =   wp_get_attachment_image_src($attachment, 'property_listings');
            $images[]       =   $attachemnt_img[0];
        }



        if (function_exists('icl_translate') ){
            $where_currency             =   icl_translate('wpestate','wp_estate_where_currency_symbol', esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') ) );
            $property_description_text  =   icl_translate('wpestate','wp_estate_property_description_text', esc_html( wpresidence_get_option('wp_estate_property_description_text') ) );
            $property_details_text      =   icl_translate('wpestate','wp_estate_property_details_text', esc_html( wpresidence_get_option('wp_estate_property_details_text') ) );
            $property_features_text     =   icl_translate('wpestate','wp_estate_property_features_text', esc_html( wpresidence_get_option('wp_estate_property_features_text') ) );
            $property_adr_text          =   icl_translate('wpestate','wp_estate_property_adr_text', esc_html( wpresidence_get_option('wp_estate_property_adr_text') ) );
            $property_video_text        =   icl_translate('wpestate','wp_estate_property_video_text', esc_html( wpresidence_get_option('wp_estate_property_video_text') ) );

        }else{
            $where_currency             =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
            $property_description_text  =   esc_html( wpresidence_get_option('wp_estate_property_description_text') );
            $property_details_text      =   esc_html( wpresidence_get_option('wp_estate_property_details_text') );
            $property_features_text     =   esc_html( wpresidence_get_option('wp_estate_property_features_text') );
            $property_adr_text          =   stripslashes ( esc_html( wpresidence_get_option('wp_estate_property_adr_text') ) );
            $property_video_text        =   esc_html( wpresidence_get_option('wp_estate_property_video_text') );
        }

        $wpestate_currency          =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $current_user               =   wp_get_current_user();
        $userID                     =   $current_user->ID;
        $user_option                =   'favorites'.intval($userID);
        $curent_fav                 =   get_option($user_option);
        $favorite_class             =   'isnotfavorite';
        $favorite_text              =   esc_html__('add to favorites','wpresidence');
        $pinteres                   =   array();
        $property_city              =   get_the_term_list($post_id, 'property_city', '', ', ', '') ;
        $property_area              =   get_the_term_list($post_id, 'property_area', '', ', ', '');
        $property_category          =   get_the_term_list($post_id, 'property_category', '', ', ', '') ;
        $property_action            =   get_the_term_list($post_id, 'property_action_category', '', ', ', '');




        $property_address           =   esc_html(wpestate_return_custom_field( $wpestate_prop_all_details, 'property_address') );

        if($curent_fav){
            if ( in_array ($post_id,$curent_fav) ){
                $favorite_class =   'isfavorite';
                $favorite_text  =   esc_html__('favorite','wpresidence');
            }
        }

        if (has_post_thumbnail()){
            $pinterest = wp_get_attachment_image_src(get_post_thumbnail_id(),'property_full_map');
        }



        $price                 =   floatval ( wpestate_return_custom_field( $wpestate_prop_all_details,'property_price') );
        $price_label           =   esc_html ( wpestate_return_custom_field( $wpestate_prop_all_details,'price_label')  );
        $price_label_before    =   esc_html ( wpestate_return_custom_field( $wpestate_prop_all_details,'price_label_before') );

        if ($price != 0) {
            $price =wpestate_show_price_from_all_details($post_id,$wpestate_currency,$where_currency,1,$wpestate_prop_all_details);
        }else{
            $price='<span class="price_label price_label_before">'.esc_html($price_label_before).'</span><span class="price_label ">'.esc_html($price_label).'</span>';
        }

        print json_encode(
                array(
                    'response'=>true,

                    'title'         =>  get_the_title($post_id),
                    'link'          =>  get_permalink($post_id),
                    'price'         =>  $price,
                    'favorite'      =>  wpestate_modal_favorite($post_id),
                    'share'         =>  wpestate_modal_social_share($post_id),
                    'addr_section'  =>  wpestate_modal_compose_address($property_address,$property_city,$property_area),
                    'beds_section'  =>  wpestate_modal_beds_section($wpestate_prop_all_details,$post_id),
                    'agent_section' =>  wpestate_modal_agent_section($post_id),
                    'content'       =>  wpestate_modal_contant($post_id,$wpestate_prop_all_details,$property_description_text),
                    'address'       =>  wpestate_modal_address($post_id,$wpestate_prop_all_details,$property_adr_text),
                    'details'       =>  wpestate_modal_details($post_id,$wpestate_prop_all_details,$property_details_text),
                    'features'      =>  wpestate_modal_features($post_id,$wpestate_prop_all_details,$property_features_text),
                    'video'         =>  wpestate_modal_video($post_id,$wpestate_prop_all_details,$property_video_text),
                    'video_tour'    =>  wpestate_modal_video_tour($post_id,$wpestate_prop_all_details,''),
                    'images'        =>  $images,
                    'full_images'   =>  $full_images,
                    )
                );
        die();

    }

endif;





if( !function_exists('wpestate_return_custom_field') ):
/**
 * Return custom field.
 *
 * @param mixed $all_post_meta Parameter value.
 * @param mixed $field Parameter value.
 * @return mixed
 */
function wpestate_return_custom_field($all_post_meta,$field){
    // Step 1: Prepare request data and build the response payload.
    if( isset($all_post_meta[$field]) ){
        return $all_post_meta[$field][0];
    }else{
        return '';
    }
}
endif;


if( !function_exists('wpestate_modal_social_share') ):
/**
 * Modal social share.
 *
 * @param mixed $postid Parameter value.
 * @return mixed
 */
function wpestate_modal_social_share($postid){
    // Step 1: Prepare request data and build the response payload.
    if (has_post_thumbnail($postid)){
        $pinterest = wp_get_attachment_image_src(get_post_thumbnail_id($postid),'property_full_map');
    }
    $email_link     =   'subject='.urlencode ( get_the_title($postid) ) .'&body='. urlencode( esc_url(get_permalink($postid)));
    $whatsup_link       =  wpestate_return_agent_whatsapp_call($postid,'');
    $return ='<div class="prop_social">
                <div class="no_views dashboad-tooltip"     data-bs-toggle="tooltip" title="'. esc_attr__('Number of Page Views','wpresidence').'"><i class="fas fa-eye-slash"></i>'. intval( get_post_meta($postid, 'wpestate_total_views', true) ).'</div>
                <i class="fas fa-print" id="print_page" data-propid="'.$postid.'"></i>
                <a href="https://www.facebook.com/sharer.php?u='. get_permalink($postid).'&amp;t='.urlencode(get_the_title($postid)).'" target="_blank" rel="noreferrer"  class="share_facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com/intent/tweet?text='.urlencode(get_the_title($postid) .' '.  esc_url( get_permalink($postid) )).'" rel="noreferrer"  class="share_tweet" target="_blank"><i class="fa-brands fa-x-twitter"></i></a>';

                if (isset($pinterest[0])){
                   $return.='<a href="https://pinterest.com/pin/create/button/?url='.get_permalink($postid).'&amp;media='.esc_url($pinterest[0]).'&amp;description='. urlencode(get_the_title($postid)).'" target="_blank" rel="noreferrer"  class="share_pinterest"> <i class="fab fa-pinterest-p"></i> </a> ';
                }
                $return.='<a href="'.esc_url($whatsup_link).'" class="social_email" rel="noreferrer"  ><i class="fab fa-whatsapp" aria-hidden="true"></i></a>
                <a href="mailto:email@email.com?'. trim( esc_html($email_link) ).'"  class="social_email"> <i class="far fa-envelope"></i></a>';
    $return.='</div>';

    return $return;

}
endif;



if( !function_exists('wpestate_modal_favorite') ):
/**
 * Modal favorite.
 *
 * @param mixed $postid Parameter value.
 * @return mixed
 */
function wpestate_modal_favorite($postid){
    // Step 1: Prepare request data and build the response payload.
    $current_user               =   wp_get_current_user();
    $userID                     =   $current_user->ID;
    $user_option                =   'favorites'.intval($userID);
    $curent_fav                 =   get_option($user_option);
    $favorite_class             =   'isnotfavorite';
    $favorite_text              =   esc_html__('add to favorites','wpresidence');

    if($curent_fav){
        if ( in_array ($postid,$curent_fav) ){
            $favorite_class =   'isfavorite';
            $favorite_text  =   esc_html__('favorite','wpresidence');
        }
    }


    $return ='  <div id="add_favorites" class="'.esc_attr($favorite_class).'" data-postid="'.$postid.'">'.esc_html($favorite_text).'</div>';
    return $return;

}
endif;



if( !function_exists('wpestate_modal_agent_section') ):
/**
 * Modal agent section.
 *
 * @param mixed $post_id Parameter value.
 * @return mixed
 */
function wpestate_modal_agent_section($post_id){
    // Step 1: Prepare request data and build the response payload.
    ob_start();
    global $prop_id ;
        $is_modal           =   1;
        $modal_agent_id     =   intval( get_post_meta($post_id, 'property_agent', true) );
        $prop_id=$post_id;// for agent_area.php
        $property_page_context='modal_window';
        include( locate_template ('/templates/listing_templates/agent_section/agent_area.php' ) );
        $return = ob_get_contents();
    ob_end_clean();

    return $return;
}
endif;



if( !function_exists('wpestate_property_modal_mortgage') ):
/**
 * Property modal mortgage.
 *
 * @param mixed $post_id Parameter value.
 * @param mixed $wpestate_prop_all_details Parameter value.
 * @return mixed
 */
function wpestate_property_modal_mortgage($post_id,$wpestate_prop_all_details){
    // Step 1: Prepare request data and build the response payload.
    $return ='<h4 class="panel-title">'.  esc_html__('Payment Calculator', 'wpresidence').'</h4>';
    ob_start();
        wpestate_morgage_calculator($post_id,$wpestate_prop_all_details);
    $return.= ob_get_contents();
    ob_end_clean();

    return $return;

}
endif;




if(!function_exists('wpestate_modal_floor')):
    /**
     * Modal floor.
     *
     * @param mixed $post_id Parameter value.
     * @param mixed $wpestate_prop_all_details Parameter value.
     * @return mixed
     */
    function wpestate_modal_floor($post_id,$wpestate_prop_all_details){
        // Step 1: Prepare request data and build the response payload.
        $return         =   '';

        $return.='<div class="wpestate_property_modal_floor_plans"><h4 class="panel-title">';
        $return.= esc_html__('Floor Plans','wpresidence');
        $return.='</h4>';
        ob_start();
        estate_floor_plan($post_id,0,$wpestate_prop_all_details);
        $return2= ob_get_contents();
        ob_end_clean();
        $return.=$return2;
        if(trim($return2)==''){
            return'';
        }
        return $return;
    }
endif;



if(!function_exists('wpestate_modal_yelp')):
    /**
     * Modal yelp.
     *
     * @param mixed $post_id Parameter value.
     * @return mixed
     */
    function wpestate_modal_yelp($post_id){
        // Step 1: Prepare request data and build the response payload.
        $return         =   '';

        $return.='<div class="wpestate_property_modal_yelp"><h4 class="panel-title">';
        $return.= esc_html__('What\'s Nearby','wpresidence');
        $return.='</h4>';
        ob_start();
            wpestate_yelp_details($post_id);
            $return2= ob_get_contents();
        ob_end_clean();
        $return.=$return2;
        if(trim($return2)==''){
            return'';
        }
        return $return;
    }
endif;



if(!function_exists('wpestate_modal_walscore')):
    /**
     * Modal walscore.
     *
     * @param mixed $post_id Parameter value.
     * @param mixed $wpestate_prop_all_details Parameter value.
     * @param mixed $property_video_text Parameter value.
     * @return mixed
     */
    function wpestate_modal_walscore($post_id,$wpestate_prop_all_details,$property_video_text){
        // Step 1: Prepare request data and build the response payload.
        $return         =   '';
        $walkscore_api  = esc_html ( wpresidence_get_option('wp_estate_walkscore_api','') );
        if($walkscore_api!=''){


            $return.='<div class="wpestate_property_modal_walkscore"><h4 class="panel-title">';
            if($property_video_text!=''){
                $return.= esc_html($property_video_text);
            }else{
                $return.= esc_html__('WalkScore','wpresidence');
            }
            $return.='</h4>';
            ob_start();
                wpestate_walkscore_details($post_id);
              
                $return2= ob_get_contents();
            ob_end_clean();
            $return.=$return2;
            $return.='</div>';

            if(trim($return2)==''){
                return'';
            }

        }
        return $return;

    }
endif;




if(!function_exists('wpestate_modal_video_tour')):
    /**
     * Modal video tour.
     *
     * @param mixed $post_id Parameter value.
     * @param mixed $wpestate_prop_all_details Parameter value.
     * @return mixed
     */
    function wpestate_modal_video_tour($post_id,$wpestate_prop_all_details){
        // Step 1: Prepare request data and build the response payload.
        $return         =   '';

        $return.='<div class="wpestate_property_modal_videos"><h4 class="panel-title"><h4 class="panel-title">'.esc_html__('Virtual Tour','wpresidence').'</h4>';

        $return2=wpestate_return_custom_field( $wpestate_prop_all_details,'embed_virtual_tour');
        $return2 = do_shortcode( $return2 );
        $return.=$return2;
        $return.='</div>';


        if(trim($return2)==''){
            return'';
        }
        return $return;

    }
endif;








if(!function_exists('wpestate_modal_video')):
    /**
     * Modal video.
     *
     * @param mixed $post_id Parameter value.
     * @param mixed $wpestate_prop_all_details Parameter value.
     * @param mixed $property_video_text Parameter value.
     * @return mixed
     */
    function wpestate_modal_video($post_id,$wpestate_prop_all_details,$property_video_text){
        // Step 1: Prepare request data and build the response payload.
        $return         =   '';

        $return.='<div class="wpestate_property_modal_videos"><h4 class="panel-title">';
        if($property_video_text!=''){
            $return.= esc_html($property_video_text);
        }else{
            $return.= esc_html__('Video','wpresidence');
        }
        $return.='</h4>';

        $return2=wpestate_listing_video($post_id,$wpestate_prop_all_details);
        $return.=$return2;
        $return.='</div>';
        $full_img_path      =   esc_html ( wpestate_return_custom_field( $wpestate_prop_all_details,'property_custom_video') );
        if(trim($full_img_path)==''){
            return'';
        }

        return $return;

    }
endif;



if(!function_exists('wpestate_modal_features')):
    /**
     * Modal features.
     *
     * @param mixed $post_id Parameter value.
     * @param mixed $wpestate_prop_all_details Parameter value.
     * @param mixed $property_features_text Parameter value.
     * @return mixed
     */
    function wpestate_modal_features($post_id,$wpestate_prop_all_details,$property_features_text){
        // Step 1: Prepare request data and build the response payload.
        $return         =   '';

        $return.='<div class="wpestate_property_modal_features"><h4 class="panel-title">';
        if($property_features_text!=''){
            $return.= esc_html($property_features_text);
        }else{
            $return.= esc_html__('Amenities and Features','wpresidence');
        }
        $return.='</h4>';

        $return2=estate_listing_features($post_id,3,0,'',$wpestate_prop_all_details);
        $return.=$return2;
        $return.='</div>';

        if(trim($return2)==''){
            return'';
        }
        return $return;


    }
endif;






if(!function_exists('wpestate_modal_details')):
    /**
     * Modal details.
     *
     * @param mixed $post_id Parameter value.
     * @param mixed $wpestate_prop_all_details Parameter value.
     * @param mixed $property_details_text Parameter value.
     * @return mixed
     */
    function wpestate_modal_details($post_id,$wpestate_prop_all_details,$property_details_text){
        // Step 1: Prepare request data and build the response payload.
        $return         =   '';

        $return.='<div class="wpestate_property_modal_details"><h4 class="panel-title">';
        if($property_details_text!=''){
            $return.= esc_html($property_details_text);
        }else{
            $return.= esc_html__('Property Details','wpresidence');
        }
        $return.='</h4>';

        $return.=estate_listing_details($post_id,$wpestate_prop_all_details);
        $return.='</div>';

        return $return;


    }
endif;








if(!function_exists('wpestate_modal_address')):
    /**
     * Modal address.
     *
     * @param mixed $post_id Parameter value.
     * @param mixed $wpestate_prop_all_details Parameter value.
     * @param mixed $property_description_text Parameter value.
     * @return mixed
     */
    function wpestate_modal_address($post_id,$wpestate_prop_all_details,$property_description_text){
        // Step 1: Prepare request data and build the response payload.
        $return         =   '';

        $return.='<div class="wpestate_property_modal_addr"><h4 class="panel-title">';
        if($property_description_text!=''){
            $return.= esc_html($property_description_text);
        }else{
            $return.= esc_html__('Property Address','wpresidence');
        }
        $return.='</h4>';

        $return.=estate_listing_address($post_id,$wpestate_prop_all_details);
        $return.='</div>';

        return $return;


    }
endif;








if(!function_exists('wpestate_modal_contant')):
    /**
     * Modal contant.
     *
     * @param mixed $post_id Parameter value.
     * @param mixed $wpestate_prop_all_details Parameter value.
     * @param mixed $property_description_text Parameter value.
     * @return mixed
     */
    function wpestate_modal_contant($post_id,$wpestate_prop_all_details,$property_description_text){
        // Step 1: Prepare request data and build the response payload.
        $return         =   '';
        $content        =   get_post_field('post_content', $post_id);
        $content        =   apply_filters('the_content', $content);
        $content        =   str_replace(']]>', ']]&gt;', $content);

        if($content!=''){
            if($property_description_text!=''){
                $property_description_label = esc_html($property_description_text);
            }else{
               $property_description_label = esc_html__('Description','wpresidence');
            }


            $return.= '<div class="wpestate_property_description">
            <h4 class="panel-title">'.esc_html($property_description_label).'</h4>'.$content;

            $energy_index       =  wpestate_return_custom_field( $wpestate_prop_all_details,'energy_index');
            $energy_class       =  wpestate_return_custom_field( $wpestate_prop_all_details,'energy_class');

            if ( $energy_index != ''    || $energy_class != ''  ){ //  if energy data  exists

                $return.= '<div class="property_energy_saving_info">';
                $return.= wpestate_energy_save_features($post_id,$wpestate_prop_all_details);
                $return.= '</div>';
                $return.= wpestare_return_documents($post_id);
                $return.= '</div>';
            }
        }

        return $return;
    }
endif;








if(!function_exists('wpestate_modal_beds_section')):
    /**
     * Modal beds section.
     *
     * @param mixed $wpestate_prop_all_details Parameter value.
     * @param mixed $post_id Parameter value.
     * @return mixed
     */
    function wpestate_modal_beds_section($wpestate_prop_all_details,$post_id){
        // Step 1: Prepare request data and build the response payload.
        $return = '';
        $property_bedrooms  =    floatval ( wpestate_return_custom_field( $wpestate_prop_all_details,'property_bedrooms') );
        $property_bathrooms =    floatval ( wpestate_return_custom_field( $wpestate_prop_all_details,'property_bathrooms') );
        $property_size      =     wpestate_get_converted_measure( $post_id, 'property_size',$wpestate_prop_all_details );


        if($property_bedrooms!=0){
            $return.='<span class="modal_bedrooms">'.$property_bedrooms.' '.esc_html('bd','wpresidence').'</span>';
        }
        if($property_bedrooms!=0){
            $return.='<span class="modal_bathrooms">'.$property_bathrooms.' '.esc_html('ba','wpresidence').'</span>';
        }
        if($property_bedrooms!=0){
            $return.='<span class="modal_size">'.$property_size.'</span>';
        }

        return $return;

    }
endif;





if(!function_exists('wpestate_modal_compose_address')):
    /**
     * Modal compose address.
     *
     * @param mixed $property_address Parameter value.
     * @param mixed $property_city Parameter value.
     * @param mixed $property_area Parameter value.
     * @return mixed
     */
    function wpestate_modal_compose_address($property_address,$property_city,$property_area){
        // Step 1: Prepare request data and build the response payload.
        $return='';
        if($property_address!=''){
            $return.= esc_html($property_address);
        }

        if($property_city!=''){
            if($property_address!=''){
                $return.= ', ';
            }
            $return.= wp_kses_post($property_city);
        }

        if($property_area!=''){
            if($property_address!='' || $property_city!=''){
                $return.= ', ';
            }
            $return.= wp_kses_post($property_area);
        }
        return $return;
    }
endif;















/**
 * 
*  blog list widget handler
 * Handles AJAX requests for loading blog list widget content.
 *
 * This function validates the AJAX request, processes the input parameters,
 * calls the blog list widget function, and returns the result as a JSON response.
 *
 * @since 1.0.0
 * @access public
 *
 * @return void Sends a JSON response and terminates script execution.
 */
add_action( $handler.'_nopriv_wpestate_load_blog_list_widget_wrapper', 'wpestate_load_blog_list_widget_wrapper' );
add_action( $handler.'_wpestate_load_blog_list_widget_wrapper', 'wpestate_load_blog_list_widget_wrapper' );


/**
 * Load blog list widget wrapper.
 *
 * @return mixed
 */
function wpestate_load_blog_list_widget_wrapper(){
    // Step 1: Prepare request data and build the response payload.
    $attributes=array(
        'number'                =>  intval($_POST['number']),
        'rownumber'             =>  intval($_POST['rownumber']),
        'control_terms_id'      =>  sanitize_text_field($_POST['control_terms_id']),
        'sort_by'               =>  intval($_POST['sort_by']),
        'card_version'          =>  intval($_POST['card_version']),
        'page'                  =>  intval($_POST['page']),
        'display_grid'          =>  sanitize_text_field($_POST['display_grid']),  
        'display_grid_wrapper'  => 'no'          
                   
    );


    // Call the function and get the result
    $result = wpestate_blog_list_widget($attributes, 1);


    // Send the response
    wp_send_json($result);
}
























if( !function_exists('estate_property_page_design_tab_options') ):
/**
 * Property page design tab options.
 *
 * @return mixed
 */
function  estate_property_page_design_tab_options(){
    // Step 1: Prepare request data and build the response payload.
    $tab_options = array(
    'description'=>array(
                    'label'=>'Description'
                    ),
    'property_address'=>array(
                    'label'=>'Property Address'
                    ),
    'property_details'=>array(
                    'label'=>'Property Details'
                    ),
    'amenities_features'=>array(
                    'label'=>'Amenities and Features'
                    ),
    'map'=>array(
                    'label'=>'Map'
                    ),
    'walkscore'=>array(
                    'label'=>'Walkscore'
                    ),
    'floor_plans'=>array(
                    'label'=>'Floor Plans'
                    ),
    'page_view'=>array(
                    'label'=>'Page Views'
                    ),
    );
}
endif;


if( !function_exists('estate_property_page_design_accordion_options') ):
/**
 * Property page design accordion options.
 *
 * @return mixed
 */
function estate_property_page_design_accordion_options(){
    // Step 1: Prepare request data and build the response payload.

}
endif;

if( !function_exists('estate_property_page_design_detailsoptions') ):
/**
 * Property page design detailsoptions.
 *
 * @return mixed
 */
function estate_property_page_design_detailsoptions(){
    // Step 1: Prepare request data and build the response payload.

}
endif;


























add_action( 'wp_ajax_wpestate_save_property_page_design', 'wpestate_save_property_page_design' );

if( !function_exists('wpestate_save_property_page_design') ):
    /**
     * Save property page design.
     *
     * @return mixed
     */
    function wpestate_save_property_page_design(){
        // Step 1: Prepare request data and build the response payload.

        check_ajax_referer( 'wpestate_save_prop_design', 'security' );

        if( !current_user_can('administrator') ){
            exit('out pls');
        }

        $content=$_POST['content'];
        echo mb_detect_encoding($content);
        update_option('wpestate_property_page_content',$content);
        Redux::setOption('wpresidence_admin','wpestate_property_page_content', $content );



        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));


        $finder = new DomXPath($doc);
        $classname="prop_full_width";


        $divs = $doc->getElementsByTagName('div');
        $nodes = $finder->query("//*[contains(@class, '$classname')]");

        $structure_array = array();
        foreach ( $nodes as $div ) {

           $structure_array []= wpestate_unit_return_row($div);
        }
        update_option('wpestate_property_unit_structure',$structure_array);
        Redux::setOption('wpresidence_admin','wpestate_property_unit_structure',  $structure_array );
        die();


    }
 endif;



 if( !function_exists('wpestate_unit_return_row') ):
 /**
  * Unit return row.
  *
  * @param mixed $div Parameter value.
  * @return mixed
  */
 function  wpestate_unit_return_row($div){
     // Step 1: Prepare request data and build the response payload.



        $return_array=array();
        foreach ( $div->childNodes as $rows ) {


            $class  =   stripslashes( trim( (string) $rows->getAttribute('class') ) );
            $class  =   str_replace('"', '', $class);

            if($class=='prop-columns'){
                $columns_array=array();

                foreach ( $rows->childNodes as $elements ) {

                    $class  =    stripslashes( trim( (string) $elements->getAttribute('class') ) );
                    $class  =    str_replace('"', '', $class);


                    if($class==='design_element_col'){
                        $unit_element=array();

                        $class_name     = ( $elements->getAttribute('data-mystyle-class')   );
                        $class_content  = ( $elements->getAttribute('data-mystyle') );
                        $element_name   = ( $elements->getAttribute('data-tip') );
                        $element_text   = ( $elements->getAttribute('data-custom-text') );
                        $element_icon   = ( $elements->getAttribute('data-icon-image') );
                        $element_font_s = ( $elements->getAttribute('data-font-size') );
                        $element_color  = ( $elements->getAttribute('data-color') );
                        $element_align  = ( $elements->getAttribute('data-text-align') );
                        $element_extra  = ( $elements->getAttribute('data-extra_css') );

                        $unit_element['element_name']   =  wpestate_unit_data_cleam($element_name );
                        $unit_element['class_content']  =  wpestate_unit_data_cleam($class_content);
                        $unit_element['class_name']     =  wpestate_unit_data_cleam($class_name);
                        $unit_element['text']           =  wpestate_unit_data_cleam($element_text);
                        $unit_element['icon']           =  wpestate_unit_data_cleam($element_icon);
                        $unit_element['font']           =  wpestate_unit_data_cleam($element_font_s);
                        $unit_element['color']          =  wpestate_unit_data_cleam($element_color);
                        $unit_element['text-align']     =  wpestate_unit_data_cleam($element_align);
                        $unit_element['extra_class']    =  wpestate_unit_data_cleam($element_extra);

                        $columns_array[]=$unit_element;
                    }
                }
                $return_array[]=$columns_array;
            }
        }

    return $return_array;


}
endif;

if( !function_exists('wpestate_unit_data_cleam') ):
/**
 * Unit data cleam.
 *
 * @param mixed $element Parameter value.
 * @return mixed
 */
function wpestate_unit_data_cleam($element){
    // Step 1: Prepare request data and build the response payload.
    $element=(string)$element;
    $element  =   str_replace('\"', '', $element);
    return $element;
}
endif;

if( !function_exists('wpestate_go_home') ):
 /**
  * Go home.
  *
  * @param mixed $element Parameter value.
  * @return mixed
  */
 function wpestate_go_home($element){
     // Step 1: Prepare request data and build the response payload.
    $element=(string)$element;
    $element  =   str_replace('\"', '', $element);
    return $element;
 }
endif;










add_action( 'wp_ajax_nopriv_wpestate_load_stats_property', 'wpestate_load_stats_property' );
add_action( 'wp_ajax_wpestate_load_stats_property', 'wpestate_load_stats_property' );

if( !function_exists('wpestate_load_stats_property') ):
    /**
     * Load stats property.
     *
     * @return mixed
     */
    function wpestate_load_stats_property(){
        // Step 1: Prepare request data and build the response payload.
    check_ajax_referer( 'wpestate_tab_stats', 'security' );
    $listing_id     =   intval($_POST['postid']);

    $labels         =   wp_estate_return_traffic_labels($listing_id,30);
    $array_values   =   wp_estate_return_traffic_data($listing_id,30);

    echo json_encode( array('array_values'=>$array_values,'labels'=>$labels) );
    die();
    }
 endif;







add_action( 'wp_ajax_wpestate_cancel_stripe', 'wpestate_cancel_stripe' );

if( !function_exists('wpestate_cancel_stripe') ):
    /**
     * Cancel stripe.
     *
     * @return mixed
     */
    function wpestate_cancel_stripe(){
        // Step 1: Prepare request data and build the response payload.
        check_ajax_referer( 'wpestate_stripe_cancel_nonce', 'security' );
        $current_user   =   wp_get_current_user();
        if ( !is_user_logged_in() ) {
            exit('ko');
        }


        global $wpestate_global_payments;
        $wpestate_global_payments->stripe_payments->cancel_stripe_recurring();
        exit();

    }
endif;


/*
*
* Zillow estimation
*
*/
add_action( 'wp_ajax_nopriv_wpestate_zillow_estimation', 'wpestate_zillow_estimation' );
add_action( 'wp_ajax_wpestate_zillow_estimation', 'wpestate_zillow_estimation' );


if( !function_exists('wpestate_zillow_estimation') ):
/**
 * Zillow estimation.
 *
 * @return mixed
 */
function wpestate_zillow_estimation(){
    // Step 1: Prepare request data and build the response payload.
    check_ajax_referer( 'wpestate_zillow_nonce', 'security' );
    $zillow_adress              =   sanitize_text_field($_POST['zillow_adress']);
    $zillow_city                =   sanitize_text_field($_POST['zillow_city']);
    $zillow_state               =   sanitize_text_field($_POST['zillow_state']);
    $full_address               =   sanitize_text_field($_POST['full_address']);
    $zillow_api_key             =   esc_html ( wpresidence_get_option('wp_estate_zillow_api_key','') );
    
    $url =trim ('https://api.bridgedataoutput.com/api/v2/zestimates_v2/zestimates?access_token='.$zillow_api_key.'&limit=10&address.in='.urlencode($zillow_adress).'&city.in='.urlencode($zillow_city).'&state.in='.urlencode($zillow_state));


    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        $answer= array( 
            'success'=>false,
            'message'=> 'cURL Error: ' . curl_error($curl)
        );
    }else{
        $answer= json_decode($response);
    }


   
    curl_close($curl);
    print json_encode($answer);

   
    die();

}
endif;

/*
*
* set cookie for currency ajax
*
*/

add_action( 'wp_ajax_nopriv_wpestate_set_cookie_multiple_curr', 'wpestate_set_cookie_multiple_curr' );
add_action( 'wp_ajax_wpestate_set_cookie_multiple_curr', 'wpestate_set_cookie_multiple_curr' );

if( !function_exists('wpestate_set_cookie_multiple_curr') ):
    /**
     * Set cookie multiple curr.
     *
     * @return mixed
     */
    function wpestate_set_cookie_multiple_curr(){
        // Step 1: Prepare request data and build the response payload.
        check_ajax_referer( 'wpestate_change_currency', 'security' );
        $curr               =   sanitize_text_field($_POST['curr']);
        $pos                =   sanitize_text_field($_POST['pos']);
        $symbol             =   sanitize_text_field($_POST['symbol']);
        $coef               =   sanitize_text_field($_POST['coef']);
        $curpos             =   sanitize_text_field($_POST['curpos']);

        setcookie("my_custom_curr", $curr,time()+3600,"/");
        setcookie("my_custom_curr_pos", $pos,time()+3600,"/");
        setcookie("my_custom_curr_symbol", $symbol,time()+3600,"/");
        setcookie("my_custom_curr_coef", $coef,time()+3600,"/");
        setcookie("my_custom_curr_cur_post", $curpos,time()+3600,"/");

    }
endif;



// set measure unit cookies


add_action( 'wp_ajax_nopriv_wpestate_set_cookie_measure_unit', 'wpestate_set_cookie_measure_unit' );
add_action( 'wp_ajax_wpestate_set_cookie_measure_unit', 'wpestate_set_cookie_measure_unit' );

if( !function_exists('wpestate_set_cookie_measure_unit') ):
    /**
     * Set cookie measure unit.
     *
     * @return mixed
     */
    function wpestate_set_cookie_measure_unit(){
        // Step 1: Prepare request data and build the response payload.
        check_ajax_referer( 'wpestate_change_measure', 'security' );
        $value               =   sanitize_text_field($_POST['value']);

        setcookie("my_measure_unit", $value ,time()+3600,"/");
    }
endif;













add_action( 'wp_ajax_wpestate_delete_search', 'wpestate_delete_search' );
if( !function_exists('wpestate_delete_search') ):

    /**
     * Delete search.
     *
     * @return mixed
     */
    function wpestate_delete_search(){
        // Step 1: Prepare request data and build the response payload.
        check_ajax_referer( 'wpestate_searches_actions', 'security' );
        $current_user           = wp_get_current_user();
        $userID                 =   $current_user->ID;

        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }




        if( isset( $_POST['search_id'] ) ) {
            if( !is_numeric($_POST['search_id'] ) ){
                exit('you don\'t have the right to delete this');
            }else{
                $delete_id  =   intval($_POST['search_id'] );
                $the_post   =   get_post( $delete_id);
                if( $current_user->ID != $the_post->post_author ) {
                    esc_html_e("you don't have the right to delete this","wpresidence");
                    die();
                }else{
                    echo "deleted";
                    wp_delete_post( $delete_id );
                    die();
                }

            }
        }

    }

endif;



add_action( 'wp_ajax_wpestate_save_search_function', 'wpestate_save_search_function' );

if( !function_exists('wpestate_save_search_function') ):
    /**
     * Save search function.
     *
     * @return mixed
     */
    function wpestate_save_search_function(){
        // Step 1: Prepare request data and build the response payload.
        check_ajax_referer( 'wpestate_save_search_nonce', 'security' );
        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;
        $userEmail      =   $current_user->user_email;


        $allowed_html   =   array();


        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }



        $search_name    =   sanitize_text_field( wp_kses(    $_POST['search_name'],$allowed_html ) );
        $search         =    wp_kses ($_POST['search'],$allowed_html );
        $meta           =   sanitize_text_field( wp_kses(    $_POST['meta'],$allowed_html  ) );


        $new_post = array(
            'post_title'    =>  $search_name,
            'post_author'   =>  $userID,
            'post_type'     =>  'wpestate_search',

            );
        $post_id = wp_insert_post($new_post);
        update_post_meta($post_id, 'search_arguments', $search);
        update_post_meta($post_id, 'meta_arguments', $meta);
        update_post_meta($post_id, 'user_email', $userEmail);

        print esc_html__('Search has been saved. You will receive an email notification when new properties matching your search have been published','wpresidence');
        die();

    }
endif;







if( !function_exists('wpestate_wp_new_user_notification') ):

    /**
     * Wp new user notification.
     *
     * @param mixed $user_id Parameter value.
     * @param mixed $plaintext_pass Parameter value.
     * @return mixed
     */
    function wpestate_wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
        // Step 1: Prepare request data and build the response payload.

		$user = new WP_User( $user_id );

		$user_login = stripslashes( $user->user_login );
		$user_email = stripslashes( $user->user_email );

                $arguments=array(
                    'user_login_register'      =>  $user_login,
                    'user_email_register'      =>  $user_email
                );

                wpestate_select_email_type(get_option('admin_email'),'admin_new_user',$arguments);




		if ( empty( $plaintext_pass ) )
			return;

                 $arguments=array(
                    'user_login_register'      =>  $user_login,
                    'user_email_register'      =>  $user_email,
                    'user_pass_register'       => $plaintext_pass
                );
                wpestate_select_email_type($user_email,'new_user',$arguments);

	}

 endif; // end   wpestate_wp_new_user_notification








if( !function_exists('wpestate_tg_validate_url') ):

    /**
     * Tg validate url.
     *
     * @param mixed $post_id Parameter value.
     * @param mixed $type Parameter value.
     * @return mixed
     */
    function wpestate_tg_validate_url($post_id,$type) {
        // Step 1: Prepare request data and build the response payload.

        $page_url = esc_url(home_url('/'));
        $urlget = strpos($page_url, "?");
        if ($urlget === false) {
                $concate = "?";
        } else {
                $concate = "&";
        }
        return $page_url.$concate;
    }

endif; // end   wpestate_tg_validate_url





// for user
add_action( 'wp_ajax_wpestate_ajax_update_profile', 'wpestate_ajax_update_profile' );
if( !function_exists('wpestate_ajax_update_profile') ):
   /**
    * Ajax update profile.
    *
    * @return mixed
    */
   function wpestate_ajax_update_profile(){
       // Step 1: Prepare request data and build the response payload.
        check_ajax_referer( 'wpestate_update_profile_nonce', 'security' );
        $current_user           =   wp_get_current_user();
        $userID                 =   $current_user->ID;
        $user_login             =   $current_user->user_login;
        check_ajax_referer( 'profile_ajax_nonce', 'security-profile' );
        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }

        $acf_fields = array();
        if ( isset( $_POST['acf_fields'] ) )    {
            $acf_fields = json_decode( stripslashes($_POST['acf_fields']), true );
        }

        $allowed_html               =   array('</br>');
        $firstname                  =   sanitize_text_field ( wp_kses( $_POST['firstname'] ,$allowed_html) );
        $secondname                 =   sanitize_text_field ( wp_kses( $_POST['secondname'] ,$allowed_html) );
        $useremail                  =   sanitize_text_field ( wp_kses( $_POST['useremail'] ,$allowed_html) );
        $userphone                  =   sanitize_text_field ( wp_kses( $_POST['userphone'] ,$allowed_html) );
        $usermobile                 =   sanitize_text_field ( wp_kses( $_POST['usermobile'] ,$allowed_html) );
        $userskype                  =   sanitize_text_field ( wp_kses( $_POST['userskype'] ,$allowed_html) );
        $usertitle                  =   sanitize_text_field ( wp_kses( $_POST['usertitle'] ,$allowed_html) );
        $about_me                   =   wp_kses( $_POST['description'],$allowed_html );
        $profile_image_url_small    =   sanitize_text_field ( wp_kses($_POST['profile_image_url_small'],$allowed_html) );
        $profile_image_url          =   sanitize_text_field ( wp_kses($_POST['profile_image_url'],$allowed_html) );
        $userfacebook               =   sanitize_text_field ( wp_kses( $_POST['userfacebook'],$allowed_html) );
        $usertwitter                =   sanitize_text_field ( wp_kses( $_POST['usertwitter'],$allowed_html) );
        $userlinkedin               =   sanitize_text_field ( wp_kses( $_POST['userlinkedin'],$allowed_html) );
        $userpinterest              =   sanitize_text_field ( wp_kses( $_POST['userpinterest'],$allowed_html ) );
        $userinstagram              =   sanitize_text_field ( wp_kses( $_POST['userinstagram'],$allowed_html ) );
        $userurl                    =   sanitize_text_field ( wp_kses( $_POST['userurl'],$allowed_html ) );
        $agent_category_submit      =   sanitize_text_field ( wp_kses( $_POST['agent_category_submit'],$allowed_html ) );
        $agent_action_submit        =   sanitize_text_field ( wp_kses( $_POST['agent_action_submit'],$allowed_html ) );
        $agent_city                 =   sanitize_text_field ( wp_kses( $_POST['agent_city'],$allowed_html ) );
        $agent_county               =   sanitize_text_field ( wp_kses( $_POST['agent_county'],$allowed_html ) );
        $agent_area                 =   sanitize_text_field ( wp_kses( $_POST['agent_area'],$allowed_html ) );
        $agent_member               =   sanitize_text_field ( wp_kses( $_POST['agent_member'],$allowed_html ) );
        $agent_address              =   sanitize_text_field (  $_POST['agent_address']);   
        
        $useryoutube                =   sanitize_text_field (  $_POST['agent_youtube']);   
        $usertiktok                 =   sanitize_text_field (  $_POST['agent_tiktok']);   
        $usertelegram               =   sanitize_text_field (  $_POST['agent_telegram']);   
        $uservimeo                  =   sanitize_text_field (  $_POST['agent_vimeo']);   
        $userprivate_notes          =   sanitize_text_field (  $_POST['agent_private_notes']);   
        
 




        $hubspot_api                =   sanitize_text_field ( wp_kses( $_POST['hubspot_api'],$allowed_html ) );
        $agent_custom_label          =   $_POST['agent_custom_label'];
        $agent_custom_value          =   $_POST['agent_custom_value'];



        // prcess fields data
        $agent_fields_array = array();
        if(is_array($agent_custom_label) && is_array($agent_custom_value) ):
      		for( $i=0; $i<count( $agent_custom_label  ); $i++ ){
                $agent_fields_array[] = array(
                    'label' => sanitize_text_field( $agent_custom_label[$i] ),
                    'value' => sanitize_text_field( $agent_custom_value[$i] ) );
      		}
        endif;


        update_user_meta( $userID, 'first_name', $firstname ) ;
        update_user_meta( $userID, 'last_name',  $secondname) ;
        update_user_meta( $userID, 'phone' , $userphone) ;
        update_user_meta( $userID, 'skype' , $userskype) ;
        update_user_meta( $userID, 'title', $usertitle) ;
        update_user_meta( $userID, 'custom_picture',$profile_image_url);
        update_user_meta( $userID, 'small_custom_picture',$profile_image_url_small);
        update_user_meta( $userID, 'mobile' , $usermobile) ;
        update_user_meta( $userID, 'facebook' , $userfacebook) ;
        update_user_meta( $userID, 'twitter' , $usertwitter) ;
        update_user_meta( $userID, 'linkedin' , $userlinkedin) ;
        update_user_meta( $userID, 'pinterest' , $userpinterest) ;
        update_user_meta( $userID, 'instagram' , $userinstagram) ;
        update_user_meta( $userID, 'description' , $about_me) ;
        update_user_meta( $userID, 'website' , $userurl) ;
    
   
        $agent_id=get_user_meta( $userID, 'user_agent_id',true);
        update_post_meta( $agent_id, 'agent_custom_data' , $agent_fields_array) ;
        update_post_meta( $agent_id, 'hubspot_api' , $hubspot_api) ;

        // Update ACF fields
        $updated_fields = array();
        foreach ($acf_fields as $field_key => $field_value) {
            $field_key = sanitize_text_field($field_key);
            
            // Handle different field types
            if (is_array($field_value)) {
                // For multi-select, checkbox, etc.
                $field_value = array_map('sanitize_text_field', $field_value);
            } else {
                $field_value = sanitize_textarea_field($field_value);
            }
            
            // Update the field
            $result = update_field($field_key, $field_value, $agent_id);
            if ($result) {
                $updated_fields[$field_key] = $field_value;
            }
        }
    

        wpestate_update_user_agent ($agent_member,$agent_category_submit,$agent_action_submit,$agent_city,$agent_county,$agent_area,$userurl,$agent_id, $firstname ,$secondname ,$useremail,$userphone,$userskype,$usertitle,$profile_image_url,$usermobile,$about_me,$profile_image_url_small,$userfacebook,$usertwitter,$userlinkedin,$userpinterest,$userinstagram,$agent_address,
        $useryoutube,$usertiktok,$usertelegram,$uservimeo,$userprivate_notes) ;


        if( $current_user->user_email != $useremail ) {
            $user_id=email_exists( $useremail ) ;
            if ( $user_id){
                esc_html_e('The email was not saved because it is used by another user.','wpresidence');
            } else{
                $args = array(
                    'ID'         => $userID,
                    'user_email' => $useremail
                );
                wp_update_user( $args );
            }
        }

        $arguments=array(
            'user_profile'      =>  $user_login,
        );

        wpestate_select_email_type(get_option('admin_email'),'agent_update_profile',$arguments);
        esc_html_e('Profile updated','wpresidence');
        die();
   }
endif; // end   wpestate_ajax_update_profile





add_action( 'wp_ajax_wpestate_ajax_update_profile_agency', 'wpestate_ajax_update_profile_agency' );
if( !function_exists('wpestate_ajax_update_profile_agency') ):
   /**
    * Ajax update profile agency.
    *
    * @return mixed
    */
   function wpestate_ajax_update_profile_agency(){
       // Step 1: Prepare request data and build the response payload.
        check_ajax_referer( 'wpestate_update_profile_nonce', 'security' );
        $current_user           =   wp_get_current_user();
        $userID                 =   $current_user->ID;
        $user_login             =   $current_user->user_login;
        check_ajax_referer( 'profile_ajax_nonce', 'security-profile' );
        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }


        if(!is_wpresidence_agency()){
            exit('not the right role');
        }

        
        $acf_fields = array();
        if ( isset( $_POST['acf_fields'] ) )    {
            $acf_fields = json_decode( stripslashes($_POST['acf_fields']), true );
        }

        $agent_id=get_user_meta( $userID, 'user_agent_id',true);


        $allowed_html               =   array('</br>');
        $agency_name                =   sanitize_text_field ( wp_kses( $_POST['agency_name'] ,$allowed_html) );
        $useremail                  =   sanitize_text_field ( wp_kses( $_POST['useremail'] ,$allowed_html) );
        $userphone                  =   sanitize_text_field ( wp_kses( $_POST['userphone'] ,$allowed_html) );
        $usermobile                 =   sanitize_text_field ( wp_kses( $_POST['usermobile'] ,$allowed_html) );
        $userskype                  =   sanitize_text_field ( wp_kses( $_POST['userskype'] ,$allowed_html) );
        $usertitle                  =   sanitize_text_field ( wp_kses( $_POST['usertitle'] ,$allowed_html) );
        $about_me                   =   wp_kses( $_POST['description'],$allowed_html );
        $profile_image_url_small    =   sanitize_text_field ( wp_kses($_POST['profile_image_url_small'],$allowed_html) );
        $profile_image_url          =   sanitize_text_field ( wp_kses($_POST['profile_image_url'],$allowed_html) );
        $userfacebook               =   sanitize_text_field ( wp_kses( $_POST['userfacebook'],$allowed_html) );
        $usertwitter                =   sanitize_text_field ( wp_kses( $_POST['usertwitter'],$allowed_html) );
        $userlinkedin               =   sanitize_text_field ( wp_kses( $_POST['userlinkedin'],$allowed_html) );
        $userpinterest              =   sanitize_text_field ( wp_kses( $_POST['userpinterest'],$allowed_html ) );
        $userinstagram              =   sanitize_text_field ( wp_kses( $_POST['userinstagram'],$allowed_html ) );
        $userurl                    =   sanitize_text_field ( wp_kses( $_POST['userurl'],$allowed_html ) );
        $agency_languages           =   sanitize_text_field ( wp_kses( $_POST['agency_languages'],$allowed_html ) );
        $agency_website             =   sanitize_text_field ( wp_kses( $_POST['agency_website'],$allowed_html ) );
        $agency_taxes               =   sanitize_text_field ( wp_kses( $_POST['agency_taxes'],$allowed_html ) );
        $agency_category_submit     =   sanitize_text_field ( wp_kses( $_POST['agency_category_submit'],$allowed_html ) );
        $agency_action_submit       =   sanitize_text_field ( wp_kses( $_POST['agency_action_submit'],$allowed_html ) );
        $agency_city                =   sanitize_text_field ( wp_kses( $_POST['agency_city'],$allowed_html ) );
        $agency_county              =   sanitize_text_field ( wp_kses( $_POST['agency_county'],$allowed_html ) );
        $agency_area                =   sanitize_text_field ( wp_kses( $_POST['agency_area'],$allowed_html ) );
        $agency_address             =   sanitize_text_field ( wp_kses( $_POST['agency_address'],$allowed_html ) );
        $agency_lat                 =   sanitize_text_field ( wp_kses( $_POST['agency_lat'],$allowed_html ) );
        $agency_long                =   sanitize_text_field ( wp_kses( $_POST['agency_long'],$allowed_html ) );
        $agency_opening_hours       =   sanitize_text_field ( wp_kses( $_POST['agency_opening_hours'],$allowed_html ) );
        $agency_hubspot_api         =   sanitize_text_field ( wp_kses( $_POST['hubspot_api'],$allowed_html ) );

        $useryoutube                =   sanitize_text_field ( wp_kses( $_POST['useryoutube'],$allowed_html ) );
        $usertiktok                 =   sanitize_text_field ( wp_kses( $_POST['usertiktok'],$allowed_html ) );
        $usertelegram               =   sanitize_text_field ( wp_kses( $_POST['usertelegram'],$allowed_html ) );
        $uservimeo                  =   sanitize_text_field ( wp_kses( $_POST['uservimeo'],$allowed_html ) );
        $agency_private_notes       =   sanitize_text_field ( wp_kses( $_POST['agency_private_notes'],$allowed_html ) );





        $agent_id=get_user_meta( $userID, 'user_agent_id',true);


        update_user_meta( $userID, 'custom_picture',$profile_image_url);
        update_user_meta( $userID, 'small_custom_picture',$profile_image_url_small);



         if($firstname!=='' || $secondname!='' ){
            $post = array(
                    'ID'            => $agent_id,
                    'post_title'    => $agency_name,
                    'post_content'  => $about_me,
            );
            $post_id =  wp_update_post($post );
        }


        update_post_meta($agent_id, 'agent_email',   $useremail);
        update_post_meta($agent_id, 'agency_email',   $useremail);
        update_post_meta($agent_id, 'agency_phone',   $userphone);
        update_post_meta($agent_id, 'agency_mobile',  $usermobile);
        update_post_meta($agent_id, 'agency_skype',   $userskype);
        update_post_meta($agent_id, 'agency_opening_hours',   $agency_opening_hours);
        update_post_meta($agent_id, 'hubspot_api',   $agency_hubspot_api);

        update_post_meta($agent_id, 'agency_facebook',   $userfacebook);
        update_post_meta($agent_id, 'agency_twitter',   $usertwitter);
        update_post_meta($agent_id, 'agency_linkedin',   $userlinkedin);
        update_post_meta($agent_id, 'agency_pinterest',   $userpinterest);
        update_post_meta($agent_id, 'agency_instagram',   $userinstagram);
        update_post_meta($agent_id, 'agency_languages',   $agency_languages);
        update_post_meta($agent_id, 'agency_website',   $agency_website);
        update_post_meta($agent_id, 'agency_taxes',   $agency_taxes);
        update_post_meta($agent_id, 'agency_address',   $agency_address);
        update_post_meta($agent_id, 'agency_lat',   $agency_lat);
        update_post_meta($agent_id, 'agency_long',   $agency_long);




        update_post_meta($agent_id, 'agency_youtube',   $useryoutube);
        update_post_meta($agent_id, 'agency_tiktok',   $usertiktok);
        update_post_meta($agent_id, 'agency_telegram',   $usertelegram);
        update_post_meta($agent_id, 'agency_vimeo',   $uservimeo);
        update_post_meta($agent_id, 'agency_private_notes',   $agency_private_notes);

        $agency_category           =   get_term( $agency_category_submit, 'category_agency');
        if(isset($agency_category->term_id)){
            $agency_category_submit  =   $agency_category->name;
        }else{
            $agency_category_submit=-1;
        }

        if( isset($agency_category_submit) && $agency_category_submit!='none' ){
            wp_set_object_terms($agent_id,$agency_category_submit,'category_agency');
        }





        $agency_category           =   get_term( $agency_action_submit, 'action_category_agency');
        if(isset($agency_category->term_id)){
            $agency_action_submit  =   $agency_category->name;
        }else{
            $agency_action_submit=-1;
        }

        if( isset($agency_action_submit) && $agency_action_submit!='none' ){
            wp_set_object_terms($agent_id,$agency_action_submit,'action_category_agency');
        }


        if( isset($agency_city) && $agency_city!='none' ){
            wp_set_object_terms($agent_id,$agency_city,'city_agency');
        }

        if( isset($agency_area) && $agency_area!='none' ){
            wp_set_object_terms($agent_id,$agency_area,'area_agency');
        }

        if( isset($agency_county) && $agency_county!='none' ){
            wp_set_object_terms($agent_id,$agency_county,'county_state_agency');
        }




        set_post_thumbnail( $agent_id, $profile_image_url_small );



        // Update ACF fields
        $updated_fields = array();
        foreach ($acf_fields as $field_key => $field_value) {
            $field_key = sanitize_text_field($field_key);
            
            // Handle different field types
            if (is_array($field_value)) {
                // For multi-select, checkbox, etc.
                $field_value = array_map('sanitize_text_field', $field_value);
            } else {
                $field_value = sanitize_textarea_field($field_value);
            }
            
            // Update the field
            $result = update_field($field_key, $field_value, $agent_id);
            if ($result) {
                $updated_fields[$field_key] = $field_value;
            }
        }


        if( $current_user->user_email != $useremail ) {
            $user_id=email_exists( $useremail ) ;
            if ( $user_id){
                esc_html_e('The email was not saved because it is used by another user.','wpresidence');
            } else{
                $args = array(
                    'ID'         => $userID,
                    'user_email' => $useremail
                );
                wp_update_user( $args );
            }
        }

        $arguments=array(
            'user_profile'      =>  $user_login,
        );

        wpestate_select_email_type(get_option('admin_email'),'agent_update_profile',$arguments);
        esc_html_e('Profile updated','wpresidence');
        die();
   }
endif; // end   wpestate_ajax_update_profile agency



add_action( 'wp_ajax_wpestate_ajax_update_profile_developer', 'wpestate_ajax_update_profile_developer' );
if( !function_exists('wpestate_ajax_update_profile_developer') ):
   /**
    * Ajax update profile developer.
    *
    * @return mixed
    */
   function wpestate_ajax_update_profile_developer(){
       // Step 1: Prepare request data and build the response payload.
        check_ajax_referer( 'wpestate_update_profile_nonce', 'security' );
        $current_user           =   wp_get_current_user();
        $userID                 =   $current_user->ID;
        $user_login             =   $current_user->user_login;
        check_ajax_referer( 'profile_ajax_nonce', 'security-profile' );
        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }
      

        if( !is_wpresidence_developer() ){
            exit('not the right role');
        }

        $acf_fields = array();
        if ( isset( $_POST['acf_fields'] ) )    {
            $acf_fields = json_decode( stripslashes($_POST['acf_fields']), true );
        }

        $developer_id=get_user_meta( $userID, 'user_agent_id',true);


        $allowed_html               =   array('</br>');
        $developer_name             =   sanitize_text_field ( wp_kses( $_POST['developer_name'] ,$allowed_html) );
        $useremail                  =   sanitize_text_field ( wp_kses( $_POST['useremail'] ,$allowed_html) );
        $userphone                  =   sanitize_text_field ( wp_kses( $_POST['userphone'] ,$allowed_html) );
        $usermobile                 =   sanitize_text_field ( wp_kses( $_POST['usermobile'] ,$allowed_html) );
        $userskype                  =   sanitize_text_field ( wp_kses( $_POST['userskype'] ,$allowed_html) );
        $usertitle                  =   sanitize_text_field ( wp_kses( $_POST['usertitle'] ,$allowed_html) );
        $about_me                   =   wp_kses( $_POST['description'],$allowed_html );
        $profile_image_url_small    =   sanitize_text_field ( wp_kses($_POST['profile_image_url_small'],$allowed_html) );
        $profile_image_url          =   sanitize_text_field ( wp_kses($_POST['profile_image_url'],$allowed_html) );
        $userfacebook               =   sanitize_text_field ( wp_kses( $_POST['userfacebook'],$allowed_html) );
        $usertwitter                =   sanitize_text_field ( wp_kses( $_POST['usertwitter'],$allowed_html) );
        $userlinkedin               =   sanitize_text_field ( wp_kses( $_POST['userlinkedin'],$allowed_html) );
        $userpinterest              =   sanitize_text_field ( wp_kses( $_POST['userpinterest'],$allowed_html ) );
        $userinstagram              =   sanitize_text_field ( wp_kses( $_POST['userinstagram'],$allowed_html ) );
        $userurl                    =   sanitize_text_field ( wp_kses( $_POST['userurl'],$allowed_html ) );
        $developer_languages           =   sanitize_text_field ( wp_kses( $_POST['developer_languages'],$allowed_html ) );
        $developer_website             =   sanitize_text_field ( wp_kses( $_POST['developer_website'],$allowed_html ) );
        $developer_taxes               =   sanitize_text_field ( wp_kses( $_POST['developer_taxes'],$allowed_html ) );
        $developer_opening_hours       =   sanitize_text_field ( wp_kses( $_POST['developer_opening_hours'],$allowed_html ) );
        $developer_category_submit     =   sanitize_text_field ( wp_kses( $_POST['developer_category_submit'],$allowed_html ) );
        $developer_action_submit       =   sanitize_text_field ( wp_kses( $_POST['developer_action_submit'],$allowed_html ) );
        $developer_city                =   sanitize_text_field ( wp_kses( $_POST['developer_city'],$allowed_html ) );
        $developer_county              =   sanitize_text_field ( wp_kses( $_POST['developer_county'],$allowed_html ) );
        $developer_area                =   sanitize_text_field ( wp_kses( $_POST['developer_area'],$allowed_html ) );
        $developer_address             =   sanitize_text_field ( wp_kses( $_POST['developer_address'],$allowed_html ) );
        $developer_lat                 =   sanitize_text_field ( wp_kses( $_POST['developer_lat'],$allowed_html ) );
        $developer_long                =   sanitize_text_field ( wp_kses( $_POST['developer_long'],$allowed_html ) );
        $hubspot_api                   =   sanitize_text_field ( wp_kses( $_POST['hubspot_api'],$allowed_html ) );

        $useryoutube              =   sanitize_text_field ( wp_kses( $_POST['useryoutube'],$allowed_html ) ) ;
        $usertiktok              =   sanitize_text_field ( wp_kses( $_POST['usertiktok'],$allowed_html ) ) ;
        $usertelegram              =   sanitize_text_field ( wp_kses( $_POST['usertelegram'],$allowed_html ) ) ;
        $uservimeo              =   sanitize_text_field ( wp_kses( $_POST['uservimeo'],$allowed_html ) ) ;
        $developer_private_notes              =   sanitize_text_field ( wp_kses( $_POST['developer_private_notes'],$allowed_html ) ) ;



        $developer_id=get_user_meta($userID,'user_agent_id',true);

        update_user_meta( $userID, 'custom_picture',$profile_image_url);
        update_user_meta( $userID, 'small_custom_picture',$profile_image_url_small);



         if($firstname!=='' || $secondname!='' ){
            $post = array(
                    'ID'            => $developer_id,
                    'post_title'    => $developer_name,
                    'post_content'  => $about_me,
            );
            $post_id =  wp_update_post($post );
        }

              update_post_meta($developer_id, 'agent_email',   $useremail);
        update_post_meta($developer_id, 'developer_email',   $useremail);
        update_post_meta($developer_id, 'developer_phone',   $userphone);
        update_post_meta($developer_id, 'developer_mobile',  $usermobile);
        update_post_meta($developer_id, 'developer_skype',   $userskype);

        update_post_meta($developer_id, 'developer_facebook',   $userfacebook);
        update_post_meta($developer_id, 'developer_twitter',   $usertwitter);
        update_post_meta($developer_id, 'developer_linkedin',   $userlinkedin);
        update_post_meta($developer_id, 'developer_pinterest',   $userpinterest);
        update_post_meta($developer_id, 'developer_instagram',   $userinstagram);
        update_post_meta($developer_id, 'developer_languages',   $developer_languages);
        update_post_meta($developer_id, 'developer_opening_hours',   $developer_opening_hours);
        update_post_meta($developer_id, 'developer_website',   $developer_website);
        update_post_meta($developer_id, 'developer_taxes',   $developer_taxes);
        update_post_meta($developer_id, 'developer_address',   $developer_address);
        update_post_meta($developer_id, 'developer_lat',   $developer_lat);
        update_post_meta($developer_id, 'developer_long',   $developer_long);

        update_post_meta($developer_id, 'hubspot_api',   $hubspot_api);

        update_post_meta($developer_id, 'developer_youtube',   $useryoutube);
        update_post_meta($developer_id, 'developer_tiktok',   $usertiktok);
        update_post_meta($developer_id, 'developer_telegram',   $usertelegram);
        update_post_meta($developer_id, 'developer_vimeo',   $uservimeo);
        update_post_meta($developer_id, 'developer_private_notes',   $developer_private_notes);

        // Update ACF fields
        $updated_fields = array();
        foreach ($acf_fields as $field_key => $field_value) {
            $field_key = sanitize_text_field($field_key);
            
            // Handle different field types
            if (is_array($field_value)) {
                // For multi-select, checkbox, etc.
                $field_value = array_map('sanitize_text_field', $field_value);
            } else {
                $field_value = sanitize_textarea_field($field_value);
            }
            
            // Update the field
            $result = update_field($field_key, $field_value, $developer_id);
            if ($result) {
                $updated_fields[$field_key] = $field_value;
            }
        }


        $developer_category           =   get_term( $developer_category_submit, 'property_category_developer');
        if(isset($developer_category->term_id)){
            $developer_category_submit  =   $developer_category->name;
        }else{
            $developer_category_submit=-1;
        }

        if( isset($developer_category_submit) && $developer_category_submit!='none' ){
            wp_set_object_terms($developer_id,$developer_category_submit,'property_category_developer');
        }





        $developer_category           =   get_term( $developer_action_submit, 'property_action_developer');
        if(isset($developer_category->term_id)){
            $developer_action_submit  =   $developer_category->name;
        }else{
            $developer_action_submit=-1;
        }

        if( isset($developer_action_submit) && $developer_action_submit!='none' ){
            wp_set_object_terms($developer_id,$developer_action_submit,'property_action_developer');
        }


        if( isset($developer_city) && $developer_city!='none' ){
            wp_set_object_terms($developer_id,$developer_city,'property_city_developer');
        }

        if( isset($developer_area) && $developer_area!='none' ){
            wp_set_object_terms($developer_id,$developer_area,'property_area_developer');
        }

        if( isset($developer_county) && $developer_county!='none' ){
            wp_set_object_terms($developer_id,$developer_county,'property_county_state_developer');
        }




        set_post_thumbnail( $developer_id, $profile_image_url_small );






        if( $current_user->user_email != $useremail ) {
            $user_id=email_exists( $useremail ) ;
            if ( $user_id){
                esc_html_e('The email was not saved because it is used by another user.','wpresidence');
            } else{
                $args = array(
                    'ID'         => $userID,
                    'user_email' => $useremail
                );
                wp_update_user( $args );
            }
        }

        $arguments=array(
            'user_profile'      =>  $user_login,
        );

        wpestate_select_email_type(get_option('admin_email'),'agent_update_profile',$arguments);
        esc_html_e('Profile updated','wpresidence');
        die();
   }
endif; // end   wpestate_ajax_update_profile developer


//delete profile wpestate_delete_profile
add_action( 'wp_ajax_wpestate_delete_profile', 'wpestate_delete_profile' );

if( !function_exists('wpestate_delete_profile') ):
    /**
     * Delete profile.
     *
     * @return mixed
     */
    function wpestate_delete_profile(){
        // Step 1: Prepare request data and build the response payload.
        check_ajax_referer( 'wpestate_update_profile_nonce', 'security' );
        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;
        if ( !is_user_logged_in() ) {
            exit('ko');
        }

         $args = array(
                'post_type' => array('estate_property',
                                    'estate_agent',
                                    'estate_agency',
                                    'estate_developer',
                                    'post',
                                    'wpestate_message',
                                    'attachment'
                                    ),
                'author'           =>  $userID,
                'posts_per_page'    => -1,
            );


        $prop_selection = new WP_Query($args);


        while ($prop_selection->have_posts()): $prop_selection->the_post();
           wp_delete_post( get_the_ID()  );
        endwhile;

        //delete comments
        $args = array(
            'user_id' => $userID, // use user_id
        );

        $comments = get_comments($args);

        foreach($comments as $comment) :
            wp_delete_comment($comment->comment_ID);
        endforeach;

        $agent_page =   get_user_meta( $userID, 'user_agent_id' , true) ;
        wp_delete_post($agent_page);

        if(!function_exists('wp_delete_user')){
            require_once(ABSPATH.'wp-admin/includes/user.php');
        }
        wp_delete_user($userID);

        die();
    }
endif; // end   wpestate_delete_profile


if( !function_exists('wpestate_update_user_agent') ):
    /**
     * Synchronizes an existing `estate_agent` post with submitted profile data.
     *
     * The function updates core post content, agent post meta, profile image,
     * and all related agent taxonomies (category/action/city/county/area).
     *
     * @param mixed $agent_member Parameter value.
     * @param mixed $agent_category_submit Parameter value.
     * @param mixed $agent_action_submit Parameter value.
     * @param mixed $agent_city Parameter value.
     * @param mixed $agent_county Parameter value.
     * @param mixed $agent_area Parameter value.
     * @param mixed $userurl Parameter value.
     * @param mixed $agent_id Parameter value.
     * @param mixed $firstname Parameter value.
     * @param mixed $secondname Parameter value.
     * @param mixed $useremail Parameter value.
     * @param mixed $userphone Parameter value.
     * @param mixed $userskype Parameter value.
     * @param mixed $usertitle Parameter value.
     * @param mixed $profile_image_url Parameter value.
     * @param mixed $usermobile Parameter value.
     * @param mixed $about_me Parameter value.
     * @param mixed $profile_image_url_small Parameter value.
     * @param mixed $userfacebook Parameter value.
     * @param mixed $usertwitter Parameter value.
     * @param mixed $userlinkedin Parameter value.
     * @param mixed $userpinterest Parameter value.
     * @param mixed $userinstagram Parameter value.
     * @param mixed $agent_address Parameter value.
     * @param mixed $useryoutube Parameter value.
     * @param mixed $usertiktok Parameter value.
     * @param mixed $usertelegram Parameter value.
     * @param mixed $uservimeo Parameter value.
     * @param mixed $userprivate_notes Parameter value.
     * @return mixed
     */
    function    wpestate_update_user_agent ($agent_member,$agent_category_submit,$agent_action_submit,$agent_city,$agent_county,$agent_area,$userurl,$agent_id, $firstname ,$secondname ,$useremail,$userphone,$userskype,$usertitle,$profile_image_url,$usermobile,$about_me,$profile_image_url_small,$userfacebook,$usertwitter,$userlinkedin,$userpinterest,$userinstagram,$agent_address,$useryoutube,$usertiktok,$usertelegram,$uservimeo,$userprivate_notes) {
        // Stop early when there is no valid agent post to update.
        if (intval($agent_id)==0){
            return;
        }

        // Update post title/content when at least one name component is provided.
        if($firstname!=='' || $secondname!='' ){
            $post = array(
                'ID'            => $agent_id,
                'post_title'    => $firstname.' '.$secondname,
                'post_content'  => $about_me,
            );
            wp_update_post($post );
        }

        // Persist all agent profile meta fields.
        update_post_meta($agent_id, 'agent_member',$agent_member);
        update_post_meta($agent_id, 'agent_address',$agent_address);
        update_post_meta($agent_id, 'first_name',   $firstname);
        update_post_meta($agent_id, 'last_name',   $secondname);
        update_post_meta($agent_id, 'agent_email',   $useremail);
        update_post_meta($agent_id, 'agent_phone',   $userphone);
        update_post_meta($agent_id, 'agent_mobile',  $usermobile);
        update_post_meta($agent_id, 'agent_skype',   $userskype);
        update_post_meta($agent_id, 'agent_position',  $usertitle);
        update_post_meta($agent_id, 'agent_facebook',   $userfacebook);
        update_post_meta($agent_id, 'agent_twitter',   $usertwitter);
        update_post_meta($agent_id, 'agent_linkedin',   $userlinkedin);
        update_post_meta($agent_id, 'agent_pinterest',   $userpinterest);
        update_post_meta($agent_id, 'agent_instagram',   $userinstagram);

        update_post_meta($agent_id, 'agent_youtube',   $useryoutube);
        update_post_meta($agent_id, 'agent_tiktok',   $usertiktok);
        update_post_meta($agent_id, 'agent_telegram',   $usertelegram);
        update_post_meta($agent_id, 'agent_vimeo',   $uservimeo);
        update_post_meta($agent_id, 'agent_private_notes',   $userprivate_notes);



        update_post_meta($agent_id, 'agent_website',   $userurl);

        // Keep featured image in sync with selected profile image.
        set_post_thumbnail( $agent_id, $profile_image_url_small );


        // Resolve and assign agent category taxonomy.
        $agent_category           =   get_term( $agent_category_submit, 'property_category_agent');
        if(isset($agent_category->term_id)){
            $agent_category_submit  =   $agent_category->name;
        }else{
            $agent_category_submit=-1;
        }

        if( isset($agent_category_submit) && $agent_category_submit!='none' ){
            wp_set_object_terms($agent_id,$agent_category_submit,'property_category_agent');
        }

        //---

        // Resolve and assign agent action taxonomy.
        $agent_category           =   get_term( $agent_action_submit, 'property_action_category_agent');
        if(isset($agent_category->term_id)){
            $agent_action_submit  =   $agent_category->name;
        }else{
            $agent_action_submit=-1;
        }

        if( isset($agent_action_submit) && $agent_action_submit!='none' ){
            wp_set_object_terms($agent_id,$agent_action_submit,'property_action_category_agent');
        }

        // Assign optional location taxonomies if values were provided.
        if( isset($agent_city) && $agent_city!='none' ){
            wp_set_object_terms($agent_id,$agent_city,'property_city_agent');
        }

        if( isset($agent_county) && $agent_county!='none' ){
            wp_set_object_terms($agent_id,$agent_county,'property_county_state_agent');
        }

        if( isset($agent_area) && $agent_area!='none' ){
            wp_set_object_terms($agent_id,$agent_area,'property_area_agent');
        }


    }
endif; // end   ajax_update_profile





add_action( 'wp_ajax_wpestate_ajax_update_pass', 'wpestate_ajax_update_pass' );
if( !function_exists('wpestate_ajax_update_pass') ):

   /**
    * Ajax update pass.
    *
    * @return mixed
    */
   function wpestate_ajax_update_pass(){
       // Step 1: Prepare request data and build the response payload.
        check_ajax_referer( 'pass_ajax_nonce', 'security-pass' );
        $current_user   =   wp_get_current_user();
        $allowed_html   =   array();
        $userID         =   $current_user->ID;

        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }

        $oldpass        =  sanitize_text_field ( wp_kses( $_POST['oldpass'] ,$allowed_html) );
        $newpass        =  sanitize_text_field ( wp_kses( $_POST['newpass'] ,$allowed_html) );
        $renewpass      =  sanitize_text_field ( wp_kses( $_POST['renewpass'] ,$allowed_html) ) ;

        if($newpass=='' || $renewpass=='' ){
            esc_html_e('The new password is blank','wpresidence');
            die();
        }

        if($newpass != $renewpass){
            esc_html_e('Passwords do not match','wpresidence');
            die();
        }


        $user = get_user_by( 'id', $userID );
        if ( $user && wp_check_password( $oldpass, $user->data->user_pass, $user->ID) ){
            wp_set_password( $newpass, $user->ID );
            esc_html_e('Password Updated','wpresidence');
        }else{
            esc_html_e('Old Password is not correct','wpresidence');
        }

        die();
   }
endif; // end   wpestate_ajax_update_pass





add_action( 'wp_ajax_wpestate_ajax_add_fav', 'wpestate_ajax_add_fav' );

if( !function_exists('wpestate_ajax_add_fav') ):

    /**
     * Ajax add fav.
     *
     * @return mixed
     */
    function wpestate_ajax_add_fav(){
        //check_ajax_referer( 'wpestate_ajax_filtering', 'security' );
        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;
  
        $user_option        =   'favorites'.$userID;


        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }

        $post_id        =   intval( $_POST['post_id']);
        $curent_fav     =   wpestate_return_favorite_listings_per_user();


        if($curent_fav==''){ // if empy / first time
            $fav    =   array();
            $fav[]  =   $post_id;
            update_option($user_option,$fav);
            echo json_encode(array('added'=>true, 'response'=>esc_html__('addded','wpresidence')));
            die();
        }else{
            if ( ! in_array ($post_id,$curent_fav) ){
                $curent_fav[]=$post_id;
                update_option($user_option,$curent_fav);
                echo json_encode(array('added'=>true, 'response'=>esc_html__('addded','wpresidence')));
                die();
            }else{
                if(($key = array_search($post_id, $curent_fav)) !== false) {
                    unset($curent_fav[$key]);
                }
                update_option($user_option,$curent_fav);
                echo json_encode(array('added'=>false, 'response'=>esc_html__('removed','wpresidence')));
                die();
            }
        }
        die();
    }
endif; // end   wpestate_ajax_add_fav













if( !function_exists('wpestate_filter_query') ):


/**
 * Filter query.
 *
 * @param mixed $orderby Parameter value.
 * @return mixed
 */
function wpestate_filter_query( $orderby )
{
    // Step 1: Prepare request data and build the response payload.
    $orderby = " DD.prop_featured  DESC ";
    return $orderby;
}
endif;
// end   wpestate_filter_query


















add_action( 'wp_ajax_wpestate_ajax_listing_pay', 'wpestate_ajax_listing_pay' );

if( !function_exists('wpestate_ajax_listing_pay') ):

    /**
     * Ajax listing pay.
     *
     * @return mixed
     */
    function wpestate_ajax_listing_pay(){
        // Step 1: Prepare request data and build the response payload.
        check_ajax_referer( 'wpestate_payments_nonce', 'security' );
        $current_user   =   wp_get_current_user();
        $is_featured    =   intval($_POST['is_featured']);
        $prop_id        =   intval($_POST['propid']);
        $is_upgrade     =   intval($_POST['is_upgrade']);
        $userID         =   $current_user->ID;
        $post           =   get_post($prop_id);


        if ( !is_user_logged_in() ) {
            exit('ko');
        }

        if($userID === 0 ){
            exit('out pls');
        }

        if( $post->post_author != $userID){
            exit('get out of my cloud');
        }

        $paypal_status                  =   esc_html( wpresidence_get_option('wp_estate_paypal_api','') );
        $host                           =   'https://api.sandbox.paypal.com';
        $price_submission               =   floatval( wpresidence_get_option('wp_estate_price_submission','') );
        $price_featured_submission      =   floatval( wpresidence_get_option('wp_estate_price_featured_submission','') );
        $submission_curency_status      =   esc_html( wpresidence_get_option('wp_estate_submission_curency','') );
        $pay_description                =   esc_html__('Listing payment on ','wpresidence').esc_url( home_url('/') );

        if( $is_featured==0 ){
            $total_price =  number_format($price_submission, 2, '.','');
        }else{
            $total_price = $price_submission + $price_featured_submission;
            $total_price = number_format($total_price, 2, '.','');
        }


        if ($is_upgrade==1){
            $total_price        =  number_format($price_featured_submission, 2, '.','');
            $pay_description    =   esc_html__('Upgrade to featured listing on ','wpresidence').esc_url( home_url('/') );
        }

        $sandbox_profile = 'sandbox';
        if($paypal_status=='live'){
            $host               =   'https://api.paypal.com';
            $sandbox_profile    =   '';
            $createdProfile     =    get_option('paypal_web_profile_live','');
        }else{
            $createdProfile     =    get_option('paypal_web_profile_sandbox','');
        }
        $url                =   $host.'/v1/oauth2/token';
        $postArgs           =   'grant_type=client_credentials';

        $token='';
        if(function_exists('wpestate_get_access_token')){
            $token              =   wpestate_get_access_token($url,$postArgs);
        }

        $url                =   $host.'/v1/payment-experience/web-profiles';





        if($createdProfile === ''){
            // create profile for no shipiing
            $site_title = get_bloginfo();

            $profile = array (
                           "name" => $site_title,
                           "presentation" => array(
                                       "brand_name"  => $site_title.$sandbox_profile,

                                       ),
                           "input_fields" => array(
                                       "allow_note"=> true,
                                       "no_shipping"=> 0,
                                       "address_override"=> 1)

                           );
            $json           =   json_encode($profile);
            $json_resp      =   wpestate_make_post_call($url, $json,$token);
            $createdProfile =   $json_resp['id'];
            if( $paypal_status=='live' ){
                update_option( 'paypal_web_profile_live', $json_resp['id'] );
            }else{
                update_option( 'paypal_web_profile_sandbox', $json_resp['id'] );
            }
        }


        $url                =   $host.'/v1/payments/payment';
        $dash_link          =   wpestate_get_template_link('page-templates/user_dashboard.php');
        $processor_link     =   wpestate_get_template_link('processor.php');


        $payment = array(
                        'intent' => 'sale',
                        "experience_profile_id" =>  $createdProfile,
                        "redirect_urls"=>array(
                                "return_url"            =>  $processor_link,
                                "cancel_url"            =>  $dash_link
                            ),
                        'payer' => array("payment_method"=>"paypal"),

                    );


        $payment['transactions'][0] = array(
                                            'amount' => array(
                                                'total' => $total_price,
                                                'currency' => $submission_curency_status,
                                                'details' => array(
                                                    'subtotal' => $total_price,
                                                    'tax' => '0.00',
                                                    'shipping' => '0.00'
                                                    )
                                                ),
                                            'description' => $pay_description
                                           );
         // prepare individual items


        if ($is_upgrade==1){
                $payment['transactions'][0]['item_list']['items'][] = array(
                                                'quantity' => '1',
                                                'name' => esc_html__('Upgrade to Featured Listing','wpresidence'),
                                                'price' => $total_price,
                                                'currency' => $submission_curency_status,
                                                'sku' => 'Upgrade Featured Listing',
                                                );
        }else{
               if( $is_featured==0 ){
                    $payment['transactions'][0]['item_list']['items'][] = array(
                                                         'quantity' => '1',
                                                         'name' => esc_html__('Listing Payment','wpresidence'),
                                                         'price' => $total_price,
                                                         'currency' => $submission_curency_status,
                                                         'sku' => 'Paid Listing',

                                                        );
                  }
                  else{
                      $payment['transactions'][0]['item_list']['items'][] = array(
                                                         'quantity' => '1',
                                                         'name' => esc_html__('Listing Payment with Featured option','wpresidence'),
                                                         'price' => $total_price,
                                                         'currency' => $submission_curency_status,
                                                         'sku' => 'Featured Paid Listing',
                                                         );

                  } // end is featured
        } // end is upgrade




        $json       =   json_encode($payment);
        $json_resp  =   wpestate_make_post_call($url, $json,$token);
        foreach ($json_resp['links'] as $link) {
                if($link['rel'] == 'execute'){
                    $payment_execute_url    = $link['href'];
                    $payment_execute_method = $link['method'];
                } else if($link['rel'] == 'approval_url'){
                    $payment_approval_url       = $link['href'];
                    $payment_approval_method    = $link['method'];
                }
        }





        $executor['paypal_execute']     =   $payment_execute_url;
        $executor['paypal_token']       =   $token;
        $executor['listing_id']         =   $prop_id;
        $executor['is_featured']        =   $is_featured;
        $executor['is_upgrade']         =   $is_upgrade;
        $save_data[$current_user->ID]   =   $executor;
        update_option('paypal_transfer',$save_data);

        print trim($payment_approval_url);

        die();
    }
endif; // end   wpestate_ajax_listing_pay




add_action( 'wp_ajax_wpestate_ajax_resend_for_approval', 'wpestate_ajax_resend_for_approval' );
if( !function_exists('wpestate_ajax_resend_for_approval') ):

    /**
     * Ajax resend for approval.
     *
     * @return mixed
     */
    function wpestate_ajax_resend_for_approval(){
        // Step 1: Prepare request data and build the response payload.
        check_ajax_referer( 'wpestate_property_actions', 'security' );
        $current_user   =   wp_get_current_user();
        $prop_id        =   intval($_POST['propid']);
        $userID         =   $current_user->ID;
        $post           =   get_post($prop_id);

        if ( !is_user_logged_in() ) {
            exit('ko');
        }

        if($userID === 0 ){
            exit('out pls');
        }


        $accont_owner       = $userID;
        $agent_id           = get_user_meta($userID,'user_agent_id',true);


        $owner_author_id    = intval(get_post_field( 'post_author', $agent_id)  );


        if($owner_author_id!=0 && $owner_author_id!=1){
            $accont_owner=$owner_author_id;
        }

        $agent_list                     =   (array)get_user_meta($accont_owner,'current_agent_list',true);


        if( $post->post_author != $userID ){
            if(!in_array($post->post_author , $agent_list)){
                exit('get out of my cloud');
            }
        }




        $free_list  =   get_user_meta($accont_owner, 'package_listings',true);

        if( $free_list>=0 ||  $free_list==-1 ){

            $paid_submission_status     =   esc_html ( wpresidence_get_option('wp_estate_paid_submission','') );
            $new_status                 =   'pending';
            $admin_submission_status    =   esc_html ( wpresidence_get_option('wp_estate_admin_submission','') );
            if($admin_submission_status=='no' && $paid_submission_status!='per listing'){
                $new_status='publish';
            }

            $prop = array(
                'ID'            => $prop_id,
                'post_type'     => 'estate_property',
                'post_status'   =>  $new_status
            );

            wp_update_post($prop );
            update_post_meta($prop_id, 'prop_featured', 0);

            if($free_list!=-1){ // if !unlimited
                update_user_meta($accont_owner, 'package_listings',$free_list-1);
            }
            print esc_html__('Sent for approval','wpresidence');
            $submit_title   =   get_the_title($prop_id);
            $arguments=array(
                'submission_title'        =>    $submit_title,
                'submission_url'          =>    esc_url (get_permalink($prop_id))
            );

            wpestate_select_email_type(get_option('admin_email'),'admin_expired_listing',$arguments);

         


        }else{
            print  esc_html__('no listings available','wpresidence');
        }
        die();



   }

 endif; // end   wpestate_ajax_resend_for_approval





if( !function_exists('wpestate_insert_calendar') ):
    /**
     * Adds an appointment slot into the agent calendar meta.
     *
     * @param mixed $agent_id Parameter value.
     * @param mixed $name Parameter value.
     * @param mixed $email Parameter value.
     * @param mixed $phone Parameter value.
     * @param mixed $schedule_day Parameter value.
     * @param mixed $schedule_hour Parameter value.
     * @return mixed
     */
    function wpestate_insert_calendar($agent_id,$name,$email,$phone,$schedule_day,$schedule_hour){
        // Load existing calendar structure from agent meta.
        $calendar=get_post_meta($agent_id,'agent_calendar',true);

        // Initialize the structure when no previous entries exist.
        if(!is_array($calendar)){
            $calendar=array();
        }

        // Build the appointment payload saved under the timestamp key.
        $temp_array = array();
        $temp_array['status']=0;
        $temp_array['name']=$name;
        $temp_array['email']=$email;
        $temp_array['phone']=$phone;
        $date= strtotime ($schedule_day.' '.$schedule_hour);

        // Reject duplicate date-time slots.
        if( isset( $calendar[$date]) && $calendar[$date]!='' ){
            $response =$schedule_day.$schedule_hour. esc_html__('The date & time is already booked. Please choose a new one','wpresidence');
            $response.=implode('|',$calendar);
            echo json_encode(array('sent'=>true, 'response'=>$response ) );
            die();
        }else{
            // Save the new slot back to the agent calendar.
            $calendar[$date] = $temp_array;
            update_post_meta($agent_id,'agent_calendar',$calendar);
        }
    }

endif;







add_action( 'wp_ajax_wpestate_ajax_paypal_pack_generation', 'wpestate_ajax_paypal_pack_generation' );

if( !function_exists('wpestate_ajax_paypal_pack_generation') ):

/**
 * Ajax paypal pack generation.
 *
 * @return mixed
 */
function wpestate_ajax_paypal_pack_generation(){
    // Step 1: Prepare request data and build the response payload.
    check_ajax_referer( 'wpestate_payments_nonce', 'security' );
    $current_user   =   wp_get_current_user();
    $userID         =   $current_user->ID;

    if ( !is_user_logged_in() ) {
        exit('ko');
    }

    if($userID === 0 ){
        exit('out pls');
    }

    $allowed_html   =   array();
    $packName       =   esc_html(wp_kses($_POST['packName'],$allowed_html));
    $pack_id        =   intval($_POST['packId']);
    $is_pack        =   get_posts('post_type=membership_package&p='.$pack_id);


    if( !empty ( $is_pack ) ) {

            $pack_price                     =   get_post_meta($pack_id, 'pack_price', true);
            $submission_curency_status      =   esc_html( wpresidence_get_option('wp_estate_submission_curency','') );
            $paypal_status                  =   esc_html( wpresidence_get_option('wp_estate_paypal_api','') );

            $host                           =   'https://api.sandbox.paypal.com';
            if($paypal_status=='live'){
                $host   =   'https://api.paypal.com';
            }

            $url        = $host.'/v1/oauth2/token';
            $postArgs   = 'grant_type=client_credentials';
            $token='';
            if(function_exists('wpestate_get_access_token')){
                $token      = wpestate_get_access_token($url,$postArgs);
            }

            $url        = $host.'/v1/payments/payment';


           $dash_profile_link = wpestate_get_template_link('page-templates/user_dashboard_profile.php');


            $payment = array(
                            'intent' => 'sale',
                            "redirect_urls"=>array(
                                "return_url"=>$dash_profile_link,
                                "cancel_url"=>$dash_profile_link
                                ),
                            'payer' => array("payment_method"=>"paypal"),

                );


                    $payment['transactions'][0] = array(
                                        'amount' => array(
                                            'total' => $pack_price,
                                            'currency' => $submission_curency_status,
                                            'details' => array(
                                                'subtotal' => $pack_price,
                                                'tax' => '0.00',
                                                'shipping' => '0.00'
                                                )
                                            ),
                                        'description' => $packName.' '.esc_html__('membership payment on ','wpresidence').esc_url( home_url('/') )
                                       );

                    //
                    // prepare individual items
                    $payment['transactions'][0]['item_list']['items'][] = array(
                                                            'quantity' => '1',
                                                            'name' => esc_html__('Membership Payment','wpresidence'),
                                                            'price' => $pack_price,
                                                            'currency' => $submission_curency_status,
                                                            'sku' => $packName.' '.esc_html__('Membership Payment','wpresidence'),
                                                           );


                    $json = json_encode($payment);
                    $json_resp = wpestate_make_post_call($url, $json,$token);


                    foreach ($json_resp['links'] as $link) {
                            if($link['rel'] == 'execute'){
                                    $payment_execute_url = $link['href'];
                                    $payment_execute_method = $link['method'];
                            } else  if($link['rel'] == 'approval_url'){
                                        $payment_approval_url = $link['href'];
                                        $payment_approval_method = $link['method'];
                            }
                    }



                    $executor['paypal_execute']     =   $payment_execute_url;
                    $executor['paypal_token']       =   $token;
                    $executor['pack_id']            =   $pack_id;
                    $save_data[$current_user->ID ]  =   $executor;
                    update_option('paypal_pack_transfer',$save_data);
                    print trim($payment_approval_url);
       }
//       /die();
}

endif; // end   ajax_paypal_pack_generation  - de la ajax_upload






// front submit check login/pass
add_action( 'wp_ajax_nopriv_wpestate_front_property_submit', 'wpestate_wpestate_front_property_submit' );
add_action( 'wp_ajax_wpestate_front_property_submit', 'wpestate_wpestate_front_property_submit' );

if( !function_exists('wpestate_wpestate_front_property_submit') ):
    /**
     * Handle login or registration during front-end property submission.
     *
     * Validates a security nonce, optional reCAPTCHA and honours the
     * site-wide registration setting before logging in or registering a user.
     *
     * @return void Sends a JSON response and exits.
     */
    function wpestate_wpestate_front_property_submit(){
        // Verify the request to prevent CSRF attacks.
        check_ajax_referer( 'wpestate_front_property_submit', 'nonce' );

        // Gather and sanitize form input.
        $action_type      = sanitize_text_field( $_POST['action_type'] );
        $front_user_login = sanitize_text_field( $_POST['front_user_login'] );
        $front_user_name  = sanitize_text_field( $_POST['front_user_name'] );
        $front_user_pass  = sanitize_text_field( $_POST['front_user_pass'] );
        $front_user_email = sanitize_text_field( $_POST['front_user_email'] );


		if( $action_type == 'login' ){
			// Authenticate the existing user by username and password.
			$user = get_user_by( 'login', $front_user_login );
			if ( $user && wp_check_password( $front_user_pass, $user->data->user_pass, $user->ID) ){

				// On success, set the current user and auth cookies.
				//wp_clear_auth_cookie();
				wp_set_current_user ( $user->ID );
				wp_set_auth_cookie  ( $user->ID );

				echo json_encode( array( 'result' => 'success', 'redirect' => wpestate_get_template_link( 'page-templates/front_property_submit.php' ) ) );
			}else{
				// Return error if authentication fails.
				echo json_encode( array( 'result' => 'error', 'message' => esc_html__('Unable to login. Please, check login/password and try again', 'wpresidence') ) );
			}
		}
                if( $action_type == 'register' ){

                        // Abort if global registration is disabled.
                        if ( 'no' === wpresidence_get_option( 'wp_estate_allow_user_registration', 'yes' ) ) {
                                echo json_encode( array( 'result' => 'error', 'message' => esc_html__( 'User registration is disabled.', 'wpresidence-core' ) ) );
                                die();
                        }

                        // Abort if global registration is disabled (use default WP option).
                        if ( ! get_option( 'users_can_register' ) ) {
                            echo json_encode( array(
                                'result'  => 'error',
                                'message' => esc_html__( 'User registration is disabled.', 'wpresidence-core' ),
                            ) );
                            die();
                        }


                        if ( wpresidence_get_option( 'wp_estate_use_captcha', '' ) == 'yes' ) {
                                // Validate captcha when enabled.
                                if ( ! isset( $_POST['capthca'] ) || $_POST['capthca'] == '' ) {
                                        // Missing captcha token.
                                        echo json_encode( array( 'result' => 'error', 'message' => esc_html__( 'Wrong captcha', 'wpresidence' ) ) );
                                        die();
                                }

                                $secret  = wpresidence_get_option( 'wp_estate_recaptha_secretkey', '' );
                                // Send token for verification.
                                $cappval = $_POST['capthca'];

                                if ( class_exists( 'WpEstate_Custom_Auth' ) ) {
                                        $auth_instance = WpEstate_Custom_Auth::get_instance();
                                        $response      = $auth_instance->wpestate_return_recapthca( $secret, $cappval );
                                } else {
                                        $response = null;
                                }

                                if ( ! $response || ( isset( $response['success'] ) && $response['success'] === false ) ) {
                                        // Stop if reCAPTCHA validation fails.
                                        echo json_encode( array( 'result' => 'error', 'message' => esc_html__( 'Captcha Invalidated - Refresh and try again.', 'wpresidence' ) ) );
                                        die();
                                }
                        }

                        $user_id = username_exists( $front_user_name );

                        if( !is_email($front_user_email) ){
                                // Email is not in valid format.
                                echo json_encode( array( 'result' => 'error', 'message' => esc_html__('Please, enter correct email', 'wpresidence') ) );
                                die();
                        }


			if ( !$user_id and email_exists($front_user_email) == false ) {
                                // Create account with a random password.
				$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
				$user_id = wp_create_user( $front_user_name, $random_password, $front_user_email );
				if( $user_id ){
					//wp_clear_auth_cookie();
					// Log in the new user.
					wp_set_current_user ( $user_id );
					wp_set_auth_cookie  ( $user_id );
					echo json_encode( array( 'result' => 'success', 'redirect' => wpestate_get_template_link( 'page-templates/front_property_submit.php' ) ) );
				}else{
					echo json_encode( array( 'result' => 'error', 'message' => esc_html__('Can\t create user with this credentials', 'wpresidence') ) );
                                        // Inform the client of the failure.
				}

			} else {
				echo json_encode( array( 'result' => 'error', 'message' => esc_html__('Sorry, user already exists. Try different username/email', 'wpresidence') ) );
                                // Notify that the username/email is already taken.
			}


		}
		die();
    }
endif;



add_action( 'wp_ajax_wpestate_ajax_paypal_pack_recuring_generation_rest_api', 'wpestate_ajax_paypal_pack_recuring_generation_rest_api' );

if( !function_exists('wpestate_ajax_paypal_pack_recuring_generation_rest_api') ):

    /**
     * Ajax paypal pack recuring generation rest api.
     *
     * @return mixed
     */
    function wpestate_ajax_paypal_pack_recuring_generation_rest_api(){
        // Step 1: Prepare request data and build the response payload.
        check_ajax_referer( 'wpestate_payments_nonce', 'security' );
        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;

        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }

        $allowed_html   =   array();
        $packName       =   wp_kses($_POST['packName'],$allowed_html);
        $pack_id        =   intval($_POST['packId']);
        if(!is_numeric($pack_id)){
            exit();
        }


        $is_pack = get_posts('post_type=membership_package&p='.$pack_id);
        if( !empty ( $is_pack ) ) {
            $pack_price                     =   get_post_meta($pack_id, 'pack_price', true);
            $billing_period                 =   get_post_meta($pack_id, 'biling_period', true);
            $billing_freq                   =   intval(get_post_meta($pack_id, 'billing_freq', true));
            $pack_name                      =   get_the_title($pack_id);
            $submission_curency_status      =   esc_html( wpresidence_get_option('wp_estate_submission_curency','') );

            $host                           =   'https://api.sandbox.paypal.com';
            $paypal_status                  =   esc_html( wpresidence_get_option('wp_estate_paypal_api','') );
            if($paypal_status=='live'){
                $host   =   'https://api.paypal.com';
            }
            $url        = $host.'/v1/oauth2/token';
            $postArgs   = 'grant_type=client_credentials';

            $token='';
            if(function_exists('wpestate_get_access_token')){
                $token      = wpestate_get_access_token($url,$postArgs);
            }

            $payment_plan = get_post_meta($pack_id, 'paypal_payment_plan_'.$paypal_status, true);


            if( !is_array($payment_plan)|| $payment_plan['id']=='' || $payment_plan==''){
                wpestate_create_paypal_payment_plan($pack_id,$token);
                $payment_plan = get_post_meta($pack_id, 'paypal_payment_plan_'.$paypal_status, true);
            }

            $url        = $host.'/v1/payments/billing-plans/'.$payment_plan['id'];

            $json_resp  = wpestate_make_get_call($url,$token);



            if( $json_resp['state']!='ACTIVE' ){
                wpestate_activate_paypal_payment_plan( $json_resp['id'],$token);
            }

            echo wpestate_create_paypal_payment_agreement($pack_id,$token);
            die();


        }
    }


endif;


add_action( 'wp_ajax_wpestate_break_attachment', 'wpestate_break_attachment' );

if( !function_exists('wpestate_break_attachment') ):
    /**
     * Break attachment.
     *
     * @return mixed
     */
    function wpestate_break_attachment(){
        //check_ajax_referer( 'wpestate_activate_pack', 'security' );
        if ( !is_user_logged_in() ) {
            exit('out pls');
        }
        if( !current_user_can('administrator') ){
            exit('out pls');
        }


        $attachment_id        =   intval($_POST['attachmentID']);
      
        // Set the post_parent to 0 to detach the attachment from any parent post
        $attachment_data = array(
            'ID' => $attachment_id,
            'post_parent' => 0
        );
        
        // Update the attachment post
        wp_update_post($attachment_data);
        
        print json_encode(array('succes'=>true,'attid'=>$attachment_id));
        die();  
    }
endif;

add_action('wp_ajax_load_reviews_page', 'handle_reviews_pagination_ajax');
add_action('wp_ajax_nopriv_load_reviews_page', 'handle_reviews_pagination_ajax');

if (!function_exists('handle_reviews_pagination_ajax')) :
/* * Ajax handler for loading estate reviews with pagination
 */
/**
 * Handle reviews pagination ajax.
 *
 * @return mixed
 */
function handle_reviews_pagination_ajax() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'estate_review_nonce')) {
        wp_send_json_error('Security check failed');
    }
    
    $post_id = intval($_POST['post_id']);
    $page = intval($_POST['page']);
    $per_page = intval($_POST['per_page']);
    
    if ($post_id <= 0 || $page <= 0 || $per_page <= 0) {
        wp_send_json_error('Invalid parameters');
    }
    
    $reviews_html = wpestate_display_reviews_summary_paginated($post_id, $per_page, $page);
    
    wp_send_json_success(array(
        'html' => $reviews_html,
        'page' => $page
    ));
}
endif; // end handle_reviews_pagination_ajax
?>
