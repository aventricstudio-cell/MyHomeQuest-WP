<?php
$handler=wpestate_return_ajax_handler();

add_action($handler.'_wpestate_property_modal_listing_details_second','wpestate_property_modal_listing_details_second');
add_action($handler.'_nopriv_wpestate_property_modal_listing_details_second','wpestate_property_modal_listing_details_second');

if( !function_exists('wpestate_property_modal_listing_details_second') ):
    function wpestate_property_modal_listing_details_second(){

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
     function wpestate_property_modal_listing_details(){

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
function wpestate_return_custom_field($all_post_meta,$field){
    if( isset($all_post_meta[$field]) ){
        return $all_post_meta[$field][0];
    }else{
        return '';
    }
}
endif;


if( !function_exists('wpestate_modal_social_share') ):
function wpestate_modal_social_share($postid){
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
function wpestate_modal_favorite($postid){
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
function wpestate_modal_agent_section($post_id){
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
function wpestate_property_modal_mortgage($post_id,$wpestate_prop_all_details){
    $return ='<h4 class="panel-title">'.  esc_html__('Payment Calculator', 'wpresidence').'</h4>';
    ob_start();
        wpestate_morgage_calculator($post_id,$wpestate_prop_all_details);
    $return.= ob_get_contents();
    ob_end_clean();

    return $return;

}
endif;




if(!function_exists('wpestate_modal_floor')):
    function wpestate_modal_floor($post_id,$wpestate_prop_all_details){
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
    function wpestate_modal_yelp($post_id){
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
    function wpestate_modal_walscore($post_id,$wpestate_prop_all_details,$property_video_text){
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
    function wpestate_modal_video_tour($post_id,$wpestate_prop_all_details){
        $return         =   '';

        $return.='<div class="wpestate_property_modal_videos"><h4 class="panel-title"><h4 class="panel-title">'.esc_html__('Virtual Tour','wpresidence').'</h4>';

        $return2=wpestate_return_custom_field( $wpestate_prop_all_details,'embed_virtual_tour');
        $return.=$return2;
        $return.='</div>';


        if(trim($return2)==''){
            return'';
        }
        return $return;

    }
endif;








if(!function_exists('wpestate_modal_video')):
    function wpestate_modal_video($post_id,$wpestate_prop_all_details,$property_video_text){
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
    function wpestate_modal_features($post_id,$wpestate_prop_all_details,$property_features_text){
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
    function wpestate_modal_details($post_id,$wpestate_prop_all_details,$property_details_text){
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
    function wpestate_modal_address($post_id,$wpestate_prop_all_details,$property_description_text){
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
    function wpestate_modal_contant($post_id,$wpestate_prop_all_details,$property_description_text){
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
    function wpestate_modal_beds_section($wpestate_prop_all_details,$post_id){
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
    function wpestate_modal_compose_address($property_address,$property_city,$property_area){
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






// agent page property listing call
add_action( 'wp_ajax_nopriv_wpestate_agent_listings', 'wpestate_agent_listings' );
add_action( 'wp_ajax_wpestate_agent_listings', 'wpestate_agent_listings' );

if( !function_exists('wpestate_agent_listings') ):

    function wpestate_agent_listings(){
        check_ajax_referer( 'wpestate_agent_listings_nonce', 'security' );
        global $wpestate_options;
        global $wpestate_no_listins_per_row;
        global $wpestate_custom_unit_structure;
        global $show_remove_fav;
        global $prop_unit_class;
        global $wpestate_prop_unit;
        global $wpestate_property_unit_slider;
        global $custom_post_type;
        global $col_class;
        global $wpestate_custom_unit_structure;
        global $wpestate_no_listins_per_row;
        global $wpestate_uset_unit;
        global $wpestate_included_ids;

        $paged='';
        $wpestate_currency                   =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency                     =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );

        $term_name=esc_html($_POST['term_name']);
        $agent_id = esc_html($_POST['agent_id']);
        $post_id = esc_html($_POST['post_id']);


        $show_compare               =   1;
        $align_class                =   '';
        $wpestate_prop_unit                  =   esc_html ( wpresidence_get_option('wp_estate_prop_unit','') );
        $prop_unit_class            =   '';
        if($wpestate_prop_unit=='list'){
            $prop_unit_class="ajax12";
            $align_class=   'the_list_view';
        }


        $wpestate_uset_unit         =   intval ( wpresidence_get_option('wpestate_uset_unit','') );
        $wpestate_no_listins_per_row         =   intval( wpresidence_get_option('wp_estate_listings_per_row', '') );
        $wpestate_custom_unit_structure      =   wpresidence_get_option('wpestate_property_unit_structure');
        $taxonmy                    =   get_query_var('taxonomy');
        $term                       =   get_query_var( 'term' );
        $wpestate_property_unit_slider       =   wpresidence_get_option('wp_estate_prop_list_slider','');
        $property_card_type         =   intval(wpresidence_get_option('wp_estate_unit_card_type'));
        $property_card_type_string  =   '';
        if($property_card_type==0){
            $property_card_type_string='';
        }else{
            $property_card_type_string='_type'.$property_card_type;
        }

        if( is_tax() && $custom_post_type=='estate_agent'){
        global $wpestate_no_listins_per_row;
        $wpestate_no_listins_per_row       =   intval( wpresidence_get_option('wp_estate_agent_listings_per_row', '') );

        $col_class=4;
        if($wpestate_options['content_class']=='col-md-12'){
            $col_class=3;
        }

        if($wpestate_no_listins_per_row==3){
            $col_class  =   '6';
            $col_org    =   6;
            if($wpestate_options['content_class']=='col-md-12'){
                $col_class  =   '4';
                $col_org    =   4;
            }
        }else{
            $col_class  =   '4';
            $col_org    =   4;
            if($wpestate_options['content_class']=='col-md-12'){
                $col_class  =   '3';
                $col_org    =   3;
            }
        }

        }

        $page_id        =   get_user_meta($agent_id,'user_agent_id',true);
        $wpestate_options        =   wpestate_page_details($page_id);


        $show_remove_fav=0;
        wp_suspend_cache_addition(false);
        $wpestate_uset_unit                  =   intval ( wpresidence_get_option('wpestate_uset_unit','') );
        $wpestate_no_listins_per_row         =   intval( wpresidence_get_option('wp_estate_listings_per_row', '') );
	    $wpestate_custom_unit_structure      =   wpresidence_get_option('wpestate_property_unit_structure');
        $property_card_type                  =   intval(wpresidence_get_option('wp_estate_unit_card_type'));
        $property_card_type_string           =   '';
        $prop_no                             =   intval( wpresidence_get_option('wp_estate_prop_no', '') );


        if( isset($_POST['wpestate_no_listins_per_row']) && intval($_POST['wpestate_no_listins_per_row'])===4 ){
           $wpestate_options['content_class']='col-md-12';
        }


        if($property_card_type==0){
            $property_card_type_string='';
        }else{
            $property_card_type_string='_type'.$property_card_type;
        }


        $action_array=array(
			'taxonomy'     => 'property_category',
			'field'        => 'slug',
                        'terms'        => $term_name
        );


		if( $agent_id == '-1' ){
			$args = array(
				'post_type'         =>  'estate_property',

				'paged'             =>  $paged,
				'posts_per_page'    =>  $prop_no,
				'post_status'       => 'publish',
				'meta_key'          => 'prop_featured',
				'orderby'           => 'meta_value',
				'order'             => 'DESC',
				'tax_query'         =>  array(
                                                            'relation' => 'AND',
                                                            $action_array,
                                                            ),
				'meta_query'        =>  array(
                                                            array(
                                                                    'key'     => 'property_agent',
                                                                    'value'   => $post_id,
                                                            ),
                                                        ),
				);
		}else{
			$args = array(
				'post_type'         =>  'estate_property',
				'author'            =>  $agent_id,
				'paged'             =>  $paged,
				'posts_per_page'    =>  $prop_no,
				'post_status'       =>  'publish',
				'meta_key'          =>  'prop_featured',
				'orderby'           =>  'meta_value',
				'order'             =>  'DESC',
				'tax_query'         =>  array(
                                                            'relation' => 'AND',
                                                            $action_array,
                                                        )
				);
		}


        if( isset($_POST['loaded'])  ){
            $args['offset'] = (int)$_POST['loaded'];
        }

        if($term_name=='all'){
            unset($args['tax_query']);
        }

        $prop_selection = wpestate_return_filtered_by_order($args);

        wpresidence_display_property_list_as_html($prop_selection,$wpestate_options  ,'shortcode_list');

        die();
    }
endif;









/*
*
*  Ahac handler for display listings
*
*
*
*
*/

add_action( $handler.'_nopriv_wpestate_load_recent_items_sh', 'wpestate_load_recent_items_sh' );
add_action( $handler.'_wpestate_load_recent_items_sh', 'wpestate_load_recent_items_sh' );

if( !function_exists('wpestate_load_recent_items_sh') ):
    

    function wpestate_load_recent_items_sh(){
       // check_ajax_referer( 'wpestate_ajax_filtering', 'security' );

        // Step 1: Normalize taxonomy arrays from AJAX payload.
        $category_values = wpestate_sanitize_ajax_taxonomy_values( 'property_category' );
        $action_values   = wpestate_sanitize_ajax_taxonomy_values( 'property_action_category' );
        $city_values     = wpestate_sanitize_ajax_taxonomy_values( 'property_city' );
        $area_values     = wpestate_sanitize_ajax_taxonomy_values( 'property_area' );
        $state_values    = wpestate_sanitize_ajax_taxonomy_values( 'property_county_state' );
        $status_values   = wpestate_sanitize_ajax_taxonomy_values( 'property_status' );
        $feature_values  = wpestate_sanitize_ajax_taxonomy_values( 'property_features' );

        // Step 2: Build shortcode arguments used by existing render/query code.
        $shortcode_arguments['type']               = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
        $shortcode_arguments['property_category']        = $category_values;
        $shortcode_arguments['property_action_category'] = $action_values;
        $shortcode_arguments['property_city']               = $city_values;
        $shortcode_arguments['property_area']               = $area_values;
        $shortcode_arguments['property_county_state']       = $state_values;
        $shortcode_arguments['property_status']             = $status_values;
        $shortcode_arguments['property_features']           = $feature_values;
        $shortcode_arguments['number']             = isset( $_POST['number'] ) ? sanitize_text_field( wp_unslash( $_POST['number'] ) ) : '';
        $shortcode_arguments['sort_by']            = isset( $_POST['sort_by'] ) ? sanitize_text_field( wp_unslash( $_POST['sort_by'] ) ) : '';
        $shortcode_arguments['show_featured_only'] = isset( $_POST['show_featured_only'] ) ? sanitize_text_field( wp_unslash( $_POST['show_featured_only'] ) ) : '';
        $shortcode_arguments['random_pick']        = isset( $_POST['random_pick'] ) ? sanitize_text_field( wp_unslash( $_POST['random_pick'] ) ) : '';
        $shortcode_arguments['row_number']         = isset( $_POST['row_number'] ) ? sanitize_text_field( wp_unslash( $_POST['row_number'] ) ) : '';
        $shortcode_arguments['align']              = isset( $_POST['align'] ) ? sanitize_text_field( wp_unslash( $_POST['align'] ) ) : '';
        $shortcode_arguments['featured_first']     = isset( $_POST['featured_first'] ) ? sanitize_text_field( wp_unslash( $_POST['featured_first'] ) ) : '';
        $shortcode_arguments['paged']              = isset( $_POST['page'] ) ? sanitize_text_field( wp_unslash( $_POST['page'] ) ) : '';
        $shortcode_arguments['card_version']       = isset( $_POST['card_version'] ) ? sanitize_text_field( wp_unslash( $_POST['card_version'] ) ) : '';
        $shortcode_arguments['display_grid']       = isset( $_POST['display_grid'] ) ? sanitize_text_field( wp_unslash( $_POST['display_grid'] ) ) : '';
        $shortcode_arguments['agentid']            = isset( $_POST['agentid'] ) ? sanitize_text_field( wp_unslash( $_POST['agentid'] ) ) : '';

        // Step 3: Keep separate integer page variable used by display helper.
        $page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;

        // Step 4: Render response markup and return JSON payload.
         $html_response = wpestate_display_listings_sh($page, $shortcode_arguments);
         $answer = array(
             'success' => true,
             'html' => $html_response
         );
         
        if (strpos($html_response, '<span class="no_results">') !== false) {
            $answer['no_results'] = true;
        }


        print json_encode($answer);
        die();

    }

    function old_wpestate_load_recent_items_sh(){

       // check_ajax_referer( 'wpestate_ajax_filtering', 'security' );
        $shortcode_arguments['type']                =   sanitize_text_field($_POST['type']);
        $shortcode_arguments['category']            =   sanitize_text_field($_POST['category_ids']);
        $shortcode_arguments['action']              =   sanitize_text_field($_POST['action_ids']);
        $shortcode_arguments['city']                =   sanitize_text_field($_POST['city_ids']);
        $shortcode_arguments['area']                =   sanitize_text_field($_POST['area_ids']);
        $shortcode_arguments['state']               =   sanitize_text_field($_POST['state_ids']);
        $shortcode_arguments['status']              =   sanitize_text_field($_POST['status']);
        $shortcode_arguments['features']            =   sanitize_text_field($_POST['features']);
        $shortcode_arguments['number']              =   sanitize_text_field($_POST['number']);
        $shortcode_arguments['sort_by']             =   sanitize_text_field($_POST['sort_by']);
        $shortcode_arguments['show_featured_only']  =   sanitize_text_field($_POST['show_featured_only']);
        $shortcode_arguments['random_pick']         =   sanitize_text_field($_POST['random_pick']);
        $shortcode_arguments['row_number']          =   sanitize_text_field($_POST['row_number']);
        $shortcode_arguments['align']               =   sanitize_text_field($_POST['align']);
        $shortcode_arguments['featured_first']      =   sanitize_text_field($_POST['featured_first']);
        $shortcode_arguments['paged']               =   sanitize_text_field($_POST['page']);
        $shortcode_arguments['card_version']        =   sanitize_text_field($_POST['card_version']);
        $shortcode_arguments['display_grid']        =   sanitize_text_field($_POST['display_grid']);
        $shortcode_arguments['agentid']            =   sanitize_text_field($_POST['agentid']);
        $page                                       =   intval($_POST['page']);


         $html_response = wpestate_display_listings_sh($page, $shortcode_arguments);
         $answer = array(
             'success' => true,
             'html' => $html_response
         );
         
        if (strpos($html_response, '<span class="no_results">') !== false) {
            $answer['no_results'] = true;
        }


        print json_encode($answer);
        die();

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


function wpestate_load_blog_list_widget_wrapper(){
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






/*
*
*  Display listing shortcode function
*
*
*
*
*/

// function wpestate_display_listings_sh($page,$shortcode_arguments){
//     $orderby='';

//     // dirty fix for row_number
//     $shortcode_arguments['rownumber'] =  $shortcode_arguments['row_number'];
//     $args = wpestate_recent_posts_shortocodes_create_arg($shortcode_arguments);


    
//     if ($shortcode_arguments['type'] == 'estate_property') {
//           $recent_posts =wpestate_return_filtered_query($args,'' );
//     }else{
//         $recent_posts = new WP_Query($args);
//     }
  
//     ob_start();
//     $return_string='';
//     if($shortcode_arguments['type'] == 'estate_property'){
//         wpresidence_display_property_list_as_html($recent_posts,array()  ,'shortcode_list',$shortcode_arguments);
//     }else  if($shortcode_arguments['type'] == 'agents'){ 
//         wpresidence_display_agent_list_as_html($recent_posts, 'estate_agent',array(), 'shortcode',$shortcode_arguments);
//     }else{
//         wpresidence_display_blog_list_as_html($recent_posts, array(), 'shortcode',$shortcode_arguments);
//     }
    
//     $templates = ob_get_contents();
//     ob_end_clean();

//     $return_string .=$templates;
//     $is_shortcode       =   0;
//     return $return_string;

// }


/*
 *
 *  On demand pins
 *
 *
 *
 *
*/


add_action( $handler.'_nopriv_wpestate_custom_ondemand_pin_load', 'wpestate_custom_ondemand_pin_load' );
add_action( $handler.'_wpestate_custom_ondemand_pin_load', 'wpestate_custom_ondemand_pin_load' );

if( !function_exists('wpestate_custom_ondemand_pin_load') ):
    function wpestate_custom_ondemand_pin_load(){
        //check_ajax_referer( 'wpestate_ajax_filtering', 'security' );
        wp_suspend_cache_addition(false);
        global $wpestate_keyword;
        $args =  wpestate_search_results_custom ('ajax');
        
        $adv_search_how     =   wpresidence_get_option('wp_estate_adv_search_how','');
        $adv_search_what    =   wpresidence_get_option('wp_estate_adv_search_what','');
        $adv_search_label   =   wpresidence_get_option('wp_estate_adv_search_label','');



        $adv_search_fields_no       =   floatval( wpresidence_get_option('wp_estate_adv_search_fields_no') );
        $term_counter               =   intval($_REQUEST['term_counter']);
        $adv_search_what    =   array_slice($adv_search_what, ( $term_counter*$adv_search_fields_no),$adv_search_fields_no);
        $adv_search_how     =   array_slice($adv_search_how,    ($term_counter*$adv_search_fields_no),$adv_search_fields_no);


        $return_custom      =   wpestate_search_with_keyword_ajax($adv_search_what, $adv_search_label);

        if( isset( $return_custom['id_array']) ){
            $id_array       =   $return_custom['id_array'];
            if($id_array!=0){
                $args=  array(  'post_type'     =>    'estate_property',
                            'p'             =>    $id_array
                );
            }
        }

        if(isset($return_custom['keyword'])){
            $wpestate_keyword        =   $return_custom['keyword'];
        }
        if( isset($_POST['keyword_search']) && trim($_POST['keyword_search'])!='' ){
            $allowed_html       =   array();
            $wpestate_keyword            =   esc_attr(  wp_kses ( $_POST['keyword_search'], $allowed_html));

        }
        //kraka
        $args['page']=1;
        $args['posts_per_page']=intval( wpresidence_get_option('wp_estate_map_max_pins') );


        $args=wpestate_remove_sold_listings($args);

        $on_demand_results = wpestate_listing_pins_on_demand($args,0);


        echo json_encode( array(
                            'xxx'       =>  $return_custom,
                            'args'      =>  $args,
                            'markers'   =>  $on_demand_results['markers'],
                            'no_results'=>  $on_demand_results['results']
                        ));
       wp_suspend_cache_addition(false);
       die();
  }

 endif; // end   ajax_filter_listings




add_action( $handler.'_nopriv_wpestate_classic_ondemand_pin_load_type2_tabs', 'wpestate_classic_ondemand_pin_load_type2_tabs' );
add_action( $handler.'_wpestate_classic_ondemand_pin_load_type2_tabs', 'wpestate_classic_ondemand_pin_load_type2_tabs' );



if( !function_exists('wpestate_classic_ondemand_pin_load_type2_tabs') ):

    function wpestate_classic_ondemand_pin_load_type2_tabs(){
       // check_ajax_referer( 'wpestate_ajax_filtering', 'security' );
        wp_suspend_cache_addition(false);

        $args =  wpestated_advanced_search_tip2_ajax_tabs ();
        //krakau
        $args['page']=1;
        $args['posts_per_page']=intval( wpresidence_get_option('wp_estate_map_max_pins') );
        $on_demand_results = wpestate_listing_pins_on_demand($args);


        echo json_encode( array(
                            'args'      =>  $args,
                            'markers'   =>  $on_demand_results['markers'],
                            'no_results'=>  $on_demand_results['results']
                        ));
       wp_suspend_cache_addition(false);
       die();
  }

 endif; // end   ajax_filter_listings

add_action( 'wp_ajax_nopriv_wpestate_classic_ondemand_pin_load_type2', 'wpestate_classic_ondemand_pin_load_type2' );
add_action( 'wp_ajax_wpestate_classic_ondemand_pin_load_type2', 'wpestate_classic_ondemand_pin_load_type2' );

if( !function_exists('wpestate_classic_ondemand_pin_load_type2') ):

    function wpestate_classic_ondemand_pin_load_type2(){
       // check_ajax_referer( 'wpestate_ajax_filtering', 'security' );
        wp_suspend_cache_addition(false);

        $args =  wpestated_advanced_search_tip2_ajax ();

        //krakau
        $args['page']=1;
        $args['posts_per_page']=intval( wpresidence_get_option('wp_estate_map_max_pins') );
        $on_demand_results = wpestate_listing_pins_on_demand($args);


        echo json_encode( array(
                            'args'      =>  $args,
                            'markers'   =>  $on_demand_results['markers'],
                            'no_results'=>  $on_demand_results['results']
                        ));
       wp_suspend_cache_addition(false);
       die();
  }

 endif; // end   ajax_filter_listings



////////////////////////////////////////////////////////////////////////////////
// on demand pins -

add_action( 'wp_ajax_nopriv_wpestate_classic_ondemand_pin_load', 'wpestate_classic_ondemand_pin_load' );
add_action( 'wp_ajax_wpestate_classic_ondemand_pin_load', 'wpestate_classic_ondemand_pin_load' );

if( !function_exists('wpestate_classic_ondemand_pin_load') ):

    function wpestate_classic_ondemand_pin_load(){
       // check_ajax_referer( 'wpestate_ajax_filtering', 'security' );
        wp_suspend_cache_addition(false);

        $args                   =  wpestate_search_results_default ('ajax');
        $args['page']           =   1;
        $args['posts_per_page'] =   intval( wpresidence_get_option('wp_estate_map_max_pins') );

        $on_demand_results      =   wpestate_listing_pins_on_demand($args);


        echo json_encode( array(
                            'args'      =>  $args,
                            'markers'   =>  $on_demand_results['markers'],
                            'no_results'=>  $on_demand_results['results']
                        ));
       wp_suspend_cache_addition(false);
       die();
  }

 endif; // end   ajax_filter_listings






////////////////////////////////////////////////////////////////////////////////
/// Ajax  Filters
////////////////////////////////////////////////////////////////////////////////
add_action( 'wp_ajax_nopriv_wpestate_ajax_filter_listings_search', 'wpestate_ajax_filter_listings_search' );
add_action( 'wp_ajax_wpestate_ajax_filter_listings_search', 'wpestate_ajax_filter_listings_search' );

if( !function_exists('wpestate_ajax_filter_listings_search') ):

    function wpestate_ajax_filter_listings_search(){
        //check_ajax_referer( 'wpestate_ajax_filtering', 'security' );
        wp_suspend_cache_addition(false);
        global $post;
        global $wpestate_options;
        global $show_compare_only;
        global $wpestate_currency;
        global $where_currency;
        global $wpestate_property_unit_slider;
        global $is_col_md_12;
        global $wpestate_prop_unit;
        global $wpestate_no_listins_per_row;
        global $wpestate_uset_unit;
        global $wpestate_custom_unit_structure;

        $wpestate_custom_unit_structure      =   wpresidence_get_option('wpestate_property_unit_structure');
        $wpestate_uset_unit         =   intval ( wpresidence_get_option('wpestate_uset_unit','') );
        $wpestate_prop_unit                  =   esc_html ( wpresidence_get_option('wp_estate_prop_unit','') );
        $show_compare_only          =   'yes';
        if( get_option( 'page_on_front') == intval ( $_POST['postid']) ){
            $show_compare_only  =   'no';
        }

        $property_card_type         =   intval(wpresidence_get_option('wp_estate_unit_card_type'));
        $property_card_type_string  =   '';
        if($property_card_type  ==  0){
            $property_card_type_string  =   '';
        }else{
            $property_card_type_string  =   '_type'.$property_card_type;
        }



        $current_user               =   wp_get_current_user();
        $curent_fav                        =    wpestate_return_favorite_listings_per_user();
        $wpestate_currency                   =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency             =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
        $area_array                 =   '';
        $city_array                 =   '';
        $action_array               =   '';
        $categ_array                =   '';
        $wpestate_property_unit_slider       =   wpresidence_get_option('wp_estate_prop_list_slider','');
        $wpestate_options                    =   wpestate_page_details(intval($_POST['postid']));
        $allowed_html               =   array();
        $wpestate_no_listins_per_row         =   intval( wpresidence_get_option('wp_estate_listings_per_row', '') );
        $half_map =   0;
        if (isset($_POST['halfmap'])){
            $half_map = intval($_POST['halfmap']);
        }

        $args =  wpestate_search_results_default ('ajax');

        $order= intval($_POST['order']);
        $meta_order         =   'prop_featured';
        $meta_directions    =   'DESC';
        $order_by           =   'meta_value';

        if(isset($_POST['order'])) {
            $order=  wp_kses( $_POST['order'],$allowed_html );
            switch ($order){
                case 1:
                    $meta_order='property_price';
                    $meta_directions='DESC';
                    $order_by='meta_value_num';
                    break;
                case 2:
                    $meta_order='property_price';
                    $meta_directions='ASC';
                    $order_by='meta_value_num';
                    break;
                case 3:
                    $meta_order='';
                    $meta_directions='DESC';
                    $order_by='ID';
                    break;
                case 4:
                    $meta_order='';
                    $meta_directions='ASC';
                    $order_by='ID';
                    break;
                case 5:
                    $meta_order='property_bedrooms';
                    $meta_directions='DESC';
                    $order_by='meta_value_num';
                    break;
                case 6:
                    $meta_order='property_bedrooms';
                    $meta_directions='ASC';
                    $order_by='meta_value_num';
                    break;
                case 7:
                    $meta_order='property_bathrooms';
                    $meta_directions='DESC';
                    $order_by='meta_value_num';
                    break;
                case 8:
                    $meta_order='property_bedrooms';
                    $meta_directions='ASC';
                    $order_by='meta_value_num';
                    break;
            }
        }



        $args ['meta_key']        = $meta_order;
        $args ['orderby']         = $order_by;
        $args ['order']           = $meta_directions;



        if(isset($_POST['order'])) {
            $prop_selection = new WP_Query($args);
        }else{
           $prop_selection = wpestate_return_filtered_by_order($args);
        }

        ob_start();
        $counter          =   0;
        $compare_submit   =   wpestate_get_template_link('page-templates/compare_listings.php');
        print '<span id="scrollhere"></span>';


        $paged      =   intval($_POST['newpage']);
        $pagination_array =array(
            'display'=>'yes',
            'paged'=>$paged
        );
        wpresidence_display_property_list_as_html($prop_selection,$wpestate_options  ,'ajax_search_list',array(),$pagination_array);
        
        wp_suspend_cache_addition(false);


        $cards= ob_get_contents();
        ob_end_clean();
        echo json_encode( array('sent'=>true, 'args'=>$args,'cards'=>$cards,'no_founs'=> $prop_selection->found_posts.' '.esc_html__('Listings','wpresidence') ) );


       die();
  }

 endif; // end   ajax_filter_listings





















if( !function_exists('estate_property_page_design_tab_options') ):
function  estate_property_page_design_tab_options(){
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
function estate_property_page_design_accordion_options(){

}
endif;

if( !function_exists('estate_property_page_design_detailsoptions') ):
function estate_property_page_design_detailsoptions(){

}
endif;


























add_action( 'wp_ajax_wpestate_save_property_page_design', 'wpestate_save_property_page_design' );

if( !function_exists('wpestate_save_property_page_design') ):
    function wpestate_save_property_page_design(){

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
 function  wpestate_unit_return_row($div){



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
function wpestate_unit_data_cleam($element){
    $element=(string)$element;
    $element  =   str_replace('\"', '', $element);
    return $element;
}
endif;

if( !function_exists('wpestate_go_home') ):
 function wpestate_go_home($element){
    $element=(string)$element;
    $element  =   str_replace('\"', '', $element);
    return $element;
 }
endif;










////////////////////////////////////////////////////////////////////////////////
/// filter invoices
////////////////////////////////////////////////////////////////////////////////
add_action( 'wp_ajax_nopriv_wpestate_load_stats_property', 'wpestate_load_stats_property' );
add_action( 'wp_ajax_wpestate_load_stats_property', 'wpestate_load_stats_property' );

if( !function_exists('wpestate_load_stats_property') ):
    function wpestate_load_stats_property(){
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
    function wpestate_cancel_stripe(){
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
function wpestate_zillow_estimation(){
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
    function wpestate_set_cookie_multiple_curr(){
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



////////////////////////////////////////////////////////////////////////////////
// set measure unit cookies
////////////////////////////////////////////////////////////////////////////////


add_action( 'wp_ajax_nopriv_wpestate_set_cookie_measure_unit', 'wpestate_set_cookie_measure_unit' );
add_action( 'wp_ajax_wpestate_set_cookie_measure_unit', 'wpestate_set_cookie_measure_unit' );

if( !function_exists('wpestate_set_cookie_measure_unit') ):
    function wpestate_set_cookie_measure_unit(){
        check_ajax_referer( 'wpestate_change_measure', 'security' );
        $value               =   sanitize_text_field($_POST['value']);

        setcookie("my_measure_unit", $value ,time()+3600,"/");
    }
endif;


////////////////////////////////////////////////////////////////////////////////
/// activate purchase
////////////////////////////////////////////////////////////////////////////////


add_action( 'wp_ajax_wpestate_activate_purchase_listing', 'wpestate_activate_purchase_listing' );

if( !function_exists('wpestate_activate_purchase_listing') ):
    function wpestate_activate_purchase_listing(){
    	check_ajax_referer( 'wpestate_activate_pack_listing', 'security' );
        if ( !is_user_logged_in() ) {
            exit('out pls');
        }

        if( !current_user_can('administrator') ){
            exit('out pls');
        }

        $item_id            =   intval($_POST['item_id']);
        $invoice_id         =   intval($_POST['invoice_id']);
        $type               =   intval($_POST['type']);
        $owner_id           =   get_post_meta($invoice_id, 'buyer_id', true);

        $user               =   get_user_by('id',$owner_id);
        $user_email         =   $user->user_email;

        if ($type==1) { // Listing
            update_post_meta($item_id, 'pay_status', 'paid');
            $post = array(
                    'ID'            => $item_id,
                    'post_status'   => 'publish'
                    );
            $post_id =  wp_update_post($post );

        }elseif ($type==2) { //Upgrade to Featured
            update_post_meta($item_id, 'prop_featured', 1);

        }elseif ($type==3){ //Publish Listing with Featured
            update_post_meta($item_id, 'pay_status', 'paid');
            update_post_meta($item_id, 'prop_featured', 1);
            $post = array(
                    'ID'            => $item_id,
                    'post_status'   => 'publish'
                    );
            $post_id =  wp_update_post($post );

        }

        update_post_meta($invoice_id, 'pay_status', 1);
        $arguments=array();
        wpestate_select_email_type($user_email,'purchase_activated',$arguments);

    }


endif;

////////////////////////////////////////////////////////////////////////////////
/// activate purchase per listing
////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_wpestate_direct_pay_pack_per_listing', 'wpestate_direct_pay_pack_per_listing' );

if( !function_exists('wpestate_direct_pay_pack_per_listing') ):
    function wpestate_direct_pay_pack_per_listing(){
        check_ajax_referer( 'wpresidence_simple_pay_actions_nonce', 'security' );
        $current_user = wp_get_current_user();
        if ( !is_user_logged_in() ) {
            exit('out pls');
        }

        $userID                     =   $current_user->ID;
        $user_email                 =   $current_user->user_email ;
        $listing_id                 =   intval($_POST['selected_pack']);
        $include_feat               =   intval($_POST['include_feat']);
        $pay_status                 =   get_post_meta($listing_id, 'pay_status', true);
        $price_submission           =   floatval( wpresidence_get_option('wp_estate_price_submission','') );
        $price_featured_submission  =   floatval( wpresidence_get_option('wp_estate_price_featured_submission','') );



        $total_price    =   0;
        $time           =   time();
        $date           =   date('Y-m-d H:i:s',$time);

        if( $include_feat==1 ){
            if( $pay_status ==  'paid' ){
                $invoice_no = wpestate_insert_invoice('Upgrade to Featured','One Time',$listing_id,$date,$current_user->ID,0,1,'' );
                wpestate_email_to_admin(1);
                $total_price    =   $price_featured_submission;
            }else{
                $invoice_no = wpestate_insert_invoice('Publish Listing with Featured','One Time',$listing_id,$date,$current_user->ID,1,0,'' );
                wpestate_email_to_admin(0);
                $total_price    =   $price_submission + $price_featured_submission;
            }
        }else{
            $invoice_no = wpestate_insert_invoice('Listing','One Time',$listing_id,$date,$current_user->ID,0,0,'' );
            wpestate_email_to_admin(0);
            $total_price    =   $price_submission;
        }

        $wpestate_currency                   =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency             =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
        if ($total_price != 0) {
            if ($where_currency == 'before') {
                $total_price = $wpestate_currency . ' ' . $total_price;
            } else {
                $total_price = $total_price . ' ' . $wpestate_currency;
            }
        }


        // send email
        /**/
        $headers = 'From: '.wpestate_return_sending_email(). "\r\n";
        $message  = esc_html__('Hi there,','wpresidence') . "\r\n\r\n";
        $message .= sprintf( esc_html__("We received your  Wire Transfer payment request on %s ! Please follow the instructions below in order to start submitting properties as soon as possible.",'wpresidence'), get_option('blogname')) . "\r\n\r\n";
        $message .= esc_html__('The invoice number is: ','wpresidence').$invoice_no." ".esc_html__('Amount:','wpresidence').' '.$total_price."\r\n\r\n";
        $message .= esc_html__('Instructions: ','wpresidence'). "\r\n\r\n";

        if (function_exists('icl_translate') ){
            $mes =  strip_tags( wpresidence_get_option('wp_estate_direct_payment_details','') );
            $payment_details      =   icl_translate('wpestate','wp_estate_property_direct_payment_text', $mes );
        }else{
            $payment_details =  strip_tags( wpresidence_get_option('wp_estate_direct_payment_details','') );
        }

        $message .= $payment_details;


        update_post_meta($invoice_no, 'pay_status', 0);


        $arguments=array(
            'invoice_no'        =>  $invoice_no,
            'total_price'       =>  $total_price,
            'payment_details'   =>  $payment_details,
        );
        wpestate_select_email_type($user_email,'new_wire_transfer',$arguments);
        $company_email      =  get_bloginfo('admin_email');
        wpestate_select_email_type($company_email,'admin_new_wire_transfer',$arguments);

        die();
}
endif;



////////////////////////////////////////////////////////////////////////////////
/// activate purchase
////////////////////////////////////////////////////////////////////////////////



add_action( 'wp_ajax_wpestate_activate_purchase', 'wpestate_activate_purchase' );

if( !function_exists('wpestate_activate_purchase') ):
    function wpestate_activate_purchase(){
        check_ajax_referer( 'wpestate_activate_pack', 'security' );
        if ( !is_user_logged_in() ) {
            exit('out pls');
        }
        if( !current_user_can('administrator') ){
            exit('out pls');
        }


        $pack_id        =   intval($_POST['item_id']);
        $invoice_id     =   intval($_POST['invoice_id']);
        $userID         =   get_post_meta($invoice_id, 'buyer_id', true);

        if( wpestate_check_downgrade_situation($userID,$pack_id) ){
            wpestate_downgrade_to_pack( $userID, $pack_id );
            wpestate_upgrade_user_membership($userID,$pack_id,1,'',1);
        }else{
            wpestate_upgrade_user_membership($userID,$pack_id,1,'',1);
        }
        update_post_meta($invoice_id, 'pay_status', 1);
    }
endif;


////////////////////////////////////////////////////////////////////////////////
/// direct pay issue invoice
////////////////////////////////////////////////////////////////////////////////




add_action( 'wp_ajax_wpestate_direct_pay_pack', 'wpestate_direct_pay_pack' );

if( !function_exists('wpestate_direct_pay_pack') ):

    function wpestate_direct_pay_pack(){
        check_ajax_referer( 'wpresidence_simple_pay_actions_nonce', 'security' );
        $current_user = wp_get_current_user();

        if ( !is_user_logged_in() ) {
            exit('out pls');
        }

        $userID                   =   $current_user->ID;
        $user_email               =   $current_user->user_email ;
        $selected_pack            =   intval( $_POST['selected_pack'] );
        $total_price              =   get_post_meta($selected_pack, 'pack_price', true);
        $wpestate_currency        =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency           =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );

        if ($total_price != 0) {
            if ($where_currency == 'before') {
                $total_price = $wpestate_currency . ' ' . $total_price;
            }   else {
                $total_price = $total_price . ' ' . $wpestate_currency;
            }
        }


        // insert invoice
        $time           =   time();
        $date           =   date('Y-m-d H:i:s',$time);
        $is_featured    =   0;
        $is_upgrade     =   0;
        $paypal_tax_id  =   '';
     

        $invoice_no = wpestate_insert_invoice('Package','One Time',$selected_pack,$date,$userID,$is_featured,$is_upgrade,$paypal_tax_id);

        // send email
        $headers    = 'From: '.wpestate_return_sending_email() . "\r\n";
        $message    = esc_html__('Hi there,','wpresidence') . "\r\n\r\n";

        if (function_exists('icl_translate') ){
            $mes                  =     strip_tags( wpresidence_get_option('wp_estate_direct_payment_details','') );
            $payment_details      =     icl_translate('wpestate','wp_estate_property_direct_payment_text', $mes );
        }else{
            $payment_details      =     strip_tags( wpresidence_get_option('wp_estate_direct_payment_details','') );
        }

        update_post_meta($invoice_no, 'pay_status', 0);
        $arguments=array(
            'invoice_no'        =>  $invoice_no,
            'total_price'       =>  $total_price,
            'payment_details'   =>  $payment_details,
        );

        // email sending
        wpestate_select_email_type($user_email,'new_wire_transfer',$arguments);
        $company_email      =  get_bloginfo('admin_email');
        wpestate_select_email_type($company_email,'admin_new_wire_transfer',$arguments);


    }

endif;








////////////////////////////////////////////////////////////////////////////////
/// Ajax  Filters
////////////////////////////////////////////////////////////////////////////////
add_action( 'wp_ajax_nopriv_wpestate_advanced_search_filters', 'wpestate_advanced_search_filters' );
add_action( 'wp_ajax_wpestate_advanced_search_filters', 'wpestate_advanced_search_filters' );

if( !function_exists('wpestate_advanced_search_filters') ):

    function wpestate_advanced_search_filters(){
       // check_ajax_referer( 'wpestate_search_nonce', 'security' );
        wp_suspend_cache_addition(true);
        wp_reset_query();
        wp_reset_postdata();

        global $wpestate_currency;
        global $where_currency;
        global $post;
        global $wpestate_options;
        global $wpestate_prop_unit;
        global $prop_unit_class;
        global $wpestate_property_unit_slider;
        global $wpestate_no_listins_per_row;
        global $wpestate_uset_unit;
        global $wpestate_custom_unit_structure;

        $wpestate_custom_unit_structure    =   wpresidence_get_option('wpestate_property_unit_structure');
        $wpestate_uset_unit       =   intval ( wpresidence_get_option('wpestate_uset_unit','') );
        $current_user             =   wp_get_current_user();
        $userID                   =   $current_user->ID;
        $user_option              =   'favorites'.$userID;
        $curent_fav               =   get_option($user_option);
        $wpestate_currency                 =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency           =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
        $show_compare             =   1;
        $wpestate_options                  =   wpestate_page_details(intval($_POST['page_id']));
        $allowed_html             =   array();
        $wpestate_property_unit_slider     =   wpresidence_get_option('wp_estate_prop_list_slider','');
        $wpestate_no_listins_per_row       =   intval( wpresidence_get_option('wp_estate_listings_per_row', '') );
        $args1 = stripslashes($_POST['args']);

        $property_card_type         =   intval(wpresidence_get_option('wp_estate_unit_card_type'));
        $property_card_type_string  =   '';
        if($property_card_type==0){
            $property_card_type_string='';
        }else{
            $property_card_type_string='_type'.$property_card_type;
        }

        $args=  json_decode($args1,true);
        //$args = get_object_vars($args2);
        $wpestate_prop_unit          =   esc_html ( wpresidence_get_option('wp_estate_prop_unit','') );
        $prop_unit_class    =   '';
        if($wpestate_prop_unit=='list'){
            $prop_unit_class="ajax12";
        }


        //////////////////////////////////////////////////////////////////////////////////////
        ///// order details
        //////////////////////////////////////////////////////////////////////////////////////
        $order          =   esc_html(wp_kses($_POST['value'],$allowed_html));
        $order_array    =   wpestate_create_query_order_by_array($order);
        $args           =   array_merge($args,$order_array['order_array']);
               
       

        $args['cache_results']          =   false;
        $args['update_post_meta_cache'] =   false;
        $args['update_post_term_cache'] =   false;

        $prop_no    =   intval( wpresidence_get_option('wp_estate_prop_no', '') );

        // checks
        if ( $args['post_type']!='estate_property' || $args['post_status']!='publish'){
            exit('out pls');
        }



        $prop_selection = new WP_Query($args);
        print '<span id="scrollhere"></span>';
    
        wpresidence_display_property_list_as_html($prop_selection,$wpestate_options  ,'shortcode_list');
    
        wp_suspend_cache_addition(false);
        die();
  }

endif; // end   ajax_filter_listings_search












////////////////////////////////////////////////////////////////////////////////
/// delete search function
////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_wpestate_delete_search', 'wpestate_delete_search' );
if( !function_exists('wpestate_delete_search') ):

    function wpestate_delete_search(){
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

////////////////////////////////////////////////////////////////////////////////
/// save search function
////////////////////////////////////////////////////////////////////////////////


add_action( 'wp_ajax_wpestate_save_search_function', 'wpestate_save_search_function' );

if( !function_exists('wpestate_save_search_function') ):
    function wpestate_save_search_function(){
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






////////////////////////////////////////////////////////////////////////////////
/// New user notification
////////////////////////////////////////////////////////////////////////////////

if( !function_exists('wpestate_wp_new_user_notification') ):

    function wpestate_wp_new_user_notification( $user_id, $plaintext_pass = '' ) {

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

    function wpestate_tg_validate_url($post_id,$type) {

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





////////////////////////////////////////////////////////////////////////////////
/// Ajax  upadte profile
////////////////////////////////////////////////////////////////////////////////
// for user
add_action( 'wp_ajax_wpestate_ajax_update_profile', 'wpestate_ajax_update_profile' );
if( !function_exists('wpestate_ajax_update_profile') ):
   function wpestate_ajax_update_profile(){
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
    
        //update_user_meta($userID, 'user_agent_id',   $userID);
        $agent_id=get_user_meta( $userID, 'user_agent_id',true);

        update_post_meta( $agent_id, 'agent_custom_data' , $agent_fields_array) ;
        update_post_meta( $agent_id, 'hubspot_api' , $hubspot_api) ;
    

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





////////////////////////////////////////////////////////////////////////////////
/// Ajax  upadte profile agency
////////////////////////////////////////////////////////////////////////////////
add_action( 'wp_ajax_wpestate_ajax_update_profile_agency', 'wpestate_ajax_update_profile_agency' );
if( !function_exists('wpestate_ajax_update_profile_agency') ):
   function wpestate_ajax_update_profile_agency(){
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
        $user_role = intval (get_user_meta( $current_user->ID, 'user_estate_role', true) );

        if($user_role!=3){
            exit('not the right role');
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

        

        // update_user_meta($userID, 'user_agent_id',   $userID);

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



////////////////////////////////////////////////////////////////////////////////
/// Ajax  upadte profile developer
////////////////////////////////////////////////////////////////////////////////
add_action( 'wp_ajax_wpestate_ajax_update_profile_developer', 'wpestate_ajax_update_profile_developer' );
if( !function_exists('wpestate_ajax_update_profile_developer') ):
   function wpestate_ajax_update_profile_developer(){
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
        $user_role = intval (get_user_meta( $current_user->ID, 'user_estate_role', true) );

        if($user_role!=4){
            exit('not the right role');
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


        // update_user_meta($userID, 'user_agent_id',   $userID);
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


////////////////////////////////////
//delete profile wpestate_delete_profile
//////////////////////////////////
add_action( 'wp_ajax_wpestate_delete_profile', 'wpestate_delete_profile' );

if( !function_exists('wpestate_delete_profile') ):
    function wpestate_delete_profile(){
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

/////////////////////////////////////////////////// update user

if( !function_exists('wpestate_update_user_agent') ):
    function    wpestate_update_user_agent ($agent_member,$agent_category_submit,$agent_action_submit,$agent_city,$agent_county,$agent_area,$userurl,$agent_id, $firstname ,$secondname ,$useremail,$userphone,$userskype,$usertitle,$profile_image_url,$usermobile,$about_me,$profile_image_url_small,$userfacebook,$usertwitter,$userlinkedin,$userpinterest,$userinstagram,$agent_address,$useryoutube,$usertiktok,$usertelegram,$uservimeo,$userprivate_notes) {
       
      
        if (intval($agent_id)==0){
            return;
        }

        if($firstname!=='' || $secondname!='' ){
            $post = array(
                'ID'            => $agent_id,
                'post_title'    => $firstname.' '.$secondname,
                'post_content'  => $about_me,
            );
            wp_update_post($post );
        }
        


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
        set_post_thumbnail( $agent_id, $profile_image_url_small );


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

        $agent_category           =   get_term( $agent_action_submit, 'property_action_category_agent');
        if(isset($agent_category->term_id)){
            $agent_action_submit  =   $agent_category->name;
        }else{
            $agent_action_submit=-1;
        }

        if( isset($agent_action_submit) && $agent_action_submit!='none' ){
            wp_set_object_terms($agent_id,$agent_action_submit,'property_action_category_agent');
        }


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




////////////////////////////////////////////////////////////////////////////////
/// Ajax  Forgot Pass function
////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_wpestate_ajax_update_pass', 'wpestate_ajax_update_pass' );
if( !function_exists('wpestate_ajax_update_pass') ):

   function wpestate_ajax_update_pass(){
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




////////////////////////////////////////////////////////////////////////////////
/// Ajax  Upload   function
////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_wpestate_ajax_add_fav', 'wpestate_ajax_add_fav' );

if( !function_exists('wpestate_ajax_add_fav') ):

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








////////////////////////////////////////////////////////////////////////////////
/// Ajax  Filters
////////////////////////////////////////////////////////////////////////////////


add_action( $handler.'_nopriv_wpestate_ajax_filter_listings', 'wpestate_ajax_filter_listings' );
add_action( $handler.'_wpestate_ajax_filter_listings', 'wpestate_ajax_filter_listings' );

if( !function_exists('wpestate_ajax_filter_listings') ):

/**
 * WpResidence AJAX Filter Listings Function
 *
 * This function handles AJAX requests for filtering property listings in the WpResidence theme.
 * It processes various filter criteria, queries the database, and returns filtered property results.
 *
 * @package WpResidence
 * @subpackage AJAX
 * @since 1.0.0
 *
 * @dependencies 
 * - wp_suspend_cache_addition()
 * - wpestate_create_query_order_by_array()
 * - wpresidence_get_option()
 * - wpestate_return_filtered_by_order()
 * - wpresidence_display_property_list_as_html()
 * - wpestate_listing_pins_on_demand()
 */

function wpestate_ajax_filter_listings(){
    // Suspend cache addition for performance
    wp_suspend_cache_addition(true);

    // Initialize variables
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;
    $area_array = $city_array = $action_array = $categ_array = '';
    $show_compare = 1;
    $allowed_html = array();

    // page_id 
    $pageID=intval($_POST['page_id']);
    $wpestate_options='';
    if($pageID!=0){
       $wpestate_options = wpestate_page_details($pageID);
    }else{
        $wpestate_options = wpestate_page_details('');
    }

    // Initialize tax query array
    $tax_array = array('relation' => 'AND');

    // Process category filter
    if (isset($_POST['category_values']) && trim($_POST['category_values']) != 'Categories' && $_POST['category_values'] != '' && $_POST['category_values'] != 'all' && $_POST['category_values'] != 'all-types') {
        $taxcateg_include = sanitize_title(wp_kses($_POST['category_values'], $allowed_html));
        $categ_array = array(
            'taxonomy' => 'property_category',
            'field' => 'slug',
            'terms' => $taxcateg_include
        );
        $tax_array[] = $categ_array;
    }

    // Process action filter
    if (isset($_POST['action_values']) && trim($_POST['action_values']) != 'Types' && $_POST['action_values'] != '' && $_POST['action_values'] != 'all' && $_POST['action_values'] != 'all-actions') {
        $taxaction_include = sanitize_title(wp_kses($_POST['action_values'], $allowed_html));
        $action_array = array(
            'taxonomy' => 'property_action_category',
            'field' => 'slug',
            'terms' => $taxaction_include
        );
        $tax_array[] = $action_array;
    }

    // Process county filter
    if (isset($_POST['county']) && trim($_POST['county']) != 'States' && $_POST['county'] && trim($_POST['county']) != 'all') {
        $taxcounty[] = sanitize_title(wp_kses($_POST['county'], $allowed_html));
        $county_array = array(
            'taxonomy' => 'property_county_state',
            'field' => 'slug',
            'terms' => $taxcounty
        );
        $tax_array[] = $county_array;
    }

    // Process city filter
    if (isset($_POST['city']) && trim($_POST['city']) != 'Cities' && $_POST['city'] && trim($_POST['city']) != 'all' && trim($_POST['city']) != 'all-cities') {
        $taxcity[] = sanitize_title(wp_kses($_POST['city'], $allowed_html));
        $city_array = array(
            'taxonomy' => 'property_city',
            'field' => 'slug',
            'terms' => $taxcity
        );
        $tax_array[] = $city_array;
    }

    // Process area filter
    if (isset($_POST['area']) && trim($_POST['area']) != 'Areas' && $_POST['area'] && trim($_POST['area']) != 'all' && trim($_POST['area']) != 'all-areas') {
        $taxarea[] = sanitize_title(wp_kses($_POST['area'], $allowed_html));
        $area_array = array(
            'taxonomy' => 'property_area',
            'field' => 'slug',
            'terms' => $taxarea
        );
        $tax_array[] = $area_array;
    }

    // Process order details
    $order = intval($_POST['order']);
    $order_array = wpestate_create_query_order_by_array($order);

    $paged = intval($_POST['newpage']);
    $prop_no = intval(wpresidence_get_option('wp_estate_prop_no', ''));

    if (isset($_POST['ishortcode']) && $_POST['ishortcode'] == 1) {
        $prop_no = intval($_POST['number']);
    }

    // Prepare query arguments
    $args = array(
        'post_type' => 'estate_property',
        'post_status' => 'publish',
        'paged' => $paged,
        'posts_per_page' => $prop_no,
        'tax_query' => $tax_array,
    );
    $args = array_merge($args, $order_array['order_array']);

    // Execute query
    if (intval($order) === 0) {
        $prop_selection = wpestate_return_filtered_by_order($args);
    } else {
        $prop_selection = new WP_Query($args);
    }

    // Prepare output
    $to_show = '<span id="scrollhere"></span>';
    ob_start();

    $pagination_parameters = array(
        'display' => 'yes',
        'order' => $order,
        'paged' => $paged,
    );

    wpresidence_display_property_list_as_html($prop_selection, $wpestate_options, 'ajax_list_shortcode', $_POST, $pagination_parameters);

    $to_show .= ob_get_contents();
    ob_end_clean();

    // Resume cache addition
    wp_suspend_cache_addition(false);

    // Prepare on-demand results
    $args['page'] = 1;
    $args['posts_per_page'] = intval(wpresidence_get_option('wp_estate_map_max_pins'));
    $args['offset'] = ($paged - 1) * $prop_no;
    $on_demand_results = wpestate_listing_pins_on_demand($args);

    // Return JSON response
    echo json_encode(array(
        'wpestate_options'=>$wpestate_options,
        'args' => $args,
        'markers' => $on_demand_results['markers'],
        'no_results' => $on_demand_results['results'],
        'to_show' => $to_show,
    ));
    die();
}
endif;





 ////////////////////////////////////////////////////////////////////////////////
/// Ajax  Filters
////////////////////////////////////////////////////////////////////////////////
add_action( $handler.'_nopriv_wpestate_custom_adv_ajax_filter_listings_search', 'wpestate_custom_adv_ajax_filter_listings_search' );
add_action( $handler.'_wpestate_custom_adv_ajax_filter_listings_search', 'wpestate_custom_adv_ajax_filter_listings_search' );

if( !function_exists('wpestate_custom_adv_ajax_filter_listings_search') ):

    function wpestate_custom_adv_ajax_filter_listings_search(){
       // check_ajax_referer( 'wpestate_ajax_filtering', 'security' );
        wp_suspend_cache_addition(true);
       

        $id_array                   =   '';

        if( get_option( 'page_on_front') == intval($_POST['postid']) ){
            $show_compare_only  =   'no';
        }

      
        $area_array         =   '';
        $city_array         =   '';
        $action_array       =   '';
        $categ_array        =   '';
        $meta_query         =   array();
        $wpestate_options            =   wpestate_page_details(intval($_POST['postid']));

        $adv_search_what    =   wpresidence_get_option('wp_estate_adv_search_what','');
        $adv_search_how     =   wpresidence_get_option('wp_estate_adv_search_how','');
        $adv_search_label   =   wpresidence_get_option('wp_estate_adv_search_label','');
        $adv_search_type    =   wpresidence_get_option('wp_estate_adv_search_type','');


        $adv_search_fields_no               =   floatval( wpresidence_get_option('wp_estate_adv_search_fields_no') );
        $term_counter       = 0;
        if(isset($_REQUEST['term_counter'])):
            $term_counter       =   intval($_REQUEST['term_counter']);
        endif;
        $adv_search_what    = array_slice($adv_search_what, ($term_counter*$adv_search_fields_no),$adv_search_fields_no);
        $adv_search_label   = array_slice($adv_search_label, ($term_counter*$adv_search_fields_no),$adv_search_fields_no);
        $adv_search_how     = array_slice($adv_search_how, ($term_counter*$adv_search_fields_no),$adv_search_fields_no);



    

        $half_map =   0;
        if (isset($_POST['halfmap'])){
            $half_map = intval($_POST['halfmap']);
            $wpestate_options['content_class'] = 'col-md-9';
            $wpestate_options['sidebar_class'] = 'none';
        }
      
        $paged                  =   intval($_POST['newpage']);
        $prop_no                =   intval( wpresidence_get_option('wp_estate_prop_no', '') );
        $args                   =   wpestate_search_results_custom ('ajax');
        $args['posts_per_page'] =   intval( wpresidence_get_option('wp_estate_prop_no', '') );

        //////////////////////////////////////////////////// in case of slider search

        $return_custom      =   wpestate_search_with_keyword_ajax($adv_search_what, $adv_search_label);

        if( isset( $return_custom['id_array']) ){
            $id_array       =   $return_custom['id_array'];
            if( intval($id_array)!=0){
                $args=  array(  
                    'post_type'     =>    'estate_property',
                    'p'             =>    intval($id_array)
                );
            }
        }

        if(isset($return_custom['keyword'])){
            $wpestate_keyword        =   $return_custom['keyword'];
        }


        if( isset($_POST['keyword_search']) && trim($_POST['keyword_search'])!='' ){
            $allowed_html       =   array();
            $wpestate_keyword            =   esc_attr(  wp_kses ( $_POST['keyword_search'], $allowed_html));

        }


        if( (  isset($_POST['geo_lat']) && isset($_POST['geo_long']) && $_POST['geo_lat']!='' && $_POST['geo_long']!='') ){

            $geo_lat = sanitize_text_field($_POST['geo_lat']); 
            $geo_long = sanitize_text_field($_POST['geo_long']);
            if(isset($_POST['geo_rad'])){
                $geo_rad    =   sanitize_text_field($_POST['geo_rad']);
            }else{
                $geo_rad    =   wpresidence_get_option('wp_estate_initial_radius','');
            }

            $args = wpestate_geo_search_filter_function($args, $geo_lat, $geo_long, $geo_rad);
        }

        ////////////////////////////////////////////////////////// end in case of slider search



    

        $args['posts_per_page'] =   intval(wpresidence_get_option('wp_estate_prop_no_adv_search',''));
   
     
        if(isset($_POST['order'])) {
            $order=  intval( $_POST['order'] );
            $order_array=   wpestate_create_query_order_by_array($order);
            $args       =   array_merge($args,$order_array['order_array']);
        }
  
       
        // remove sold listings
        $args=wpestate_remove_sold_listings($args);


        if( intval($id_array) !=0 ){

            $prop_selection     = new WP_Query($args);
        }else{
            if($order==0){
                add_filter( 'posts_orderby', 'wpestate_my_order' );
            }

            if( !empty($wpestate_keyword) ){
                $wpestate_keyword    =  str_replace('-', ' ', $wpestate_keyword);
                add_filter( 'posts_where', 'wpestate_title_filter', 10, 2 );
                $prop_selection     = new WP_Query($args);
                if(function_exists('wpestate_disable_filtering')){
                    wpestate_disable_filtering( 'posts_where', 'wpestate_title_filter', 10, 2 );
                }
            }else{
               $prop_selection     = new WP_Query($args);
            }
            if($order==0){
                if(function_exists('wpestate_disable_filtering')){
                    wpestate_disable_filtering( 'posts_orderby', 'wpestate_my_order' );
                }
            }
        }


        $counter            =   0;
        $compare_submit     =   wpestate_get_template_link('page-templates/compare_listings.php');







        ob_start();
        print '<span id="scrollhere"></span>';

        $pagination_parameters = array(
            'display'=>'yes',
            'paged' => $paged,
        );        
        wpresidence_display_property_list_as_html($prop_selection,$wpestate_options  ,'ajax_search_list',array(),$pagination_parameters);


        wp_suspend_cache_addition(false);

        $cards= ob_get_contents();
        ob_end_clean();

        $saved_search=null;
        $search_meta=null;
        $show_save_search            =   wpresidence_get_option('wp_estate_show_save_search','');
        if($half_map==1 && $show_save_search=='yes'){
            $custom_advanced_search =   wpresidence_get_option('wp_estate_custom_advanced_search','');
            global $wpestate_included_ids;
            $search_meta=$wpestate_included_ids;
            $saved_search= wpestate_show_search_params_new($wpestate_included_ids,$args,$custom_advanced_search, $adv_search_what,$adv_search_how,$adv_search_label);
        }





        echo json_encode( array('sent'=>true,
                'args'=>$args,
                'opt' => $wpestate_options,
                'search_meta'=>$search_meta,
                'saved_search'=>$saved_search, 
                'cards'=>$cards,
                'no_founs'=> $prop_selection->found_posts.' '.esc_html__('Listings','wpresidence') ) );
        die();
  }

 endif; // end   ajax_filter_listings


 ////////////////////////////////////////////////////////////////////////////////
/// Ajax  Filters
////////////////////////////////////////////////////////////////////////////////
add_action( 'wp_ajax_nopriv_wpestate_custom_adv_get_filtering_ajax_result', 'wpestate_custom_adv_get_filtering_ajax_result' );
add_action( 'wp_ajax_wpestate_custom_adv_get_filtering_ajax_result', 'wpestate_custom_adv_get_filtering_ajax_result' );

if( !function_exists('wpestate_custom_adv_get_filtering_ajax_result') ):

    function wpestate_custom_adv_get_filtering_ajax_result(){
        wp_suspend_cache_addition(true);
        global $post;

        global $wpestate_options;
        global $show_compare_only;
        global $wpestate_currency;
        global $where_currency;
        global $wpestate_keyword;

        $show_compare_only          =   'no';
        $allowed_html               =   array();
        $curent_fav                        =    wpestate_return_favorite_listings_per_user();
        $wpestate_currency                   =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency             =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
        $area_array                 =   '';
        $city_array                 =   '';
        $action_array               =   '';
        $categ_array                =   '';
        $meta_query                 =   array();
        $wpestate_options                    =   wpestate_page_details(intval($_POST['postid']));
        $adv_search_what            =   wpresidence_get_option('wp_estate_adv_search_what','');
        $adv_search_how             =   wpresidence_get_option('wp_estate_adv_search_how','');
        $adv_search_label           =   wpresidence_get_option('wp_estate_adv_search_label','');
        $adv_search_type            =   wpresidence_get_option('wp_estate_adv_search_type','');



        $args       =   wpestate_search_results_custom ('ajax');
        $wpestate_keyword    =   wpestate_search_with_keyword_ajax($adv_search_what,$adv_search_label );

        ////////////////////////////////////////////////////////// end in case of slider search
        add_filter( 'posts_orderby', 'wpestate_my_order' );
        if( !empty($wpestate_keyword) ){
            $prop_selection = wpestate_return_filtered_by_order($args);
        }else{
            $prop_selection     = new WP_Query($args);
        }
        if(function_exists('wpestate_disable_filtering')){
            wpestate_disable_filtering( 'posts_orderby', 'wpestate_my_order' );
        }

        if( $prop_selection->have_posts() ){
            print intval($prop_selection->post_count);

        }else{
            print '0';
        }

        wp_suspend_cache_addition(false);
        die();
  }

 endif; // end   ajax_filter_listings




////////////////////////////////////////////////////////////////////////////////
/// wpestate_filter_query
////////////////////////////////////////////////////////////////////////////////

if( !function_exists('wpestate_filter_query') ):


function wpestate_filter_query( $orderby )
{
    $orderby = " DD.prop_featured  DESC ";
    return $orderby;
}
endif;
// end   wpestate_filter_query

















 ////////////////////////////////////////////////////////////////////////////////
/// pay via paypal - per listing
////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_wpestate_ajax_listing_pay', 'wpestate_ajax_listing_pay' );

if( !function_exists('wpestate_ajax_listing_pay') ):

    function wpestate_ajax_listing_pay(){
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



////////////////////////////////////////////////////////////////////////////////
/// pay via paypal - per listing
////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_wpestate_ajax_resend_for_approval', 'wpestate_ajax_resend_for_approval' );
if( !function_exists('wpestate_ajax_resend_for_approval') ):

    function wpestate_ajax_resend_for_approval(){
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
    function wpestate_insert_calendar($agent_id,$name,$email,$phone,$schedule_day,$schedule_hour){
        $calendar=get_post_meta($agent_id,'agent_calendar',true);

        if(!is_array($calendar)){
            $calendar=array();
        }
        $temp_array = array();
        $temp_array['status']=0;
        $temp_array['name']=$name;
        $temp_array['email']=$email;
        $temp_array['phone']=$phone;
        $date= strtotime ($schedule_day.' '.$schedule_hour);

        if( isset( $calendar[$date]) && $calendar[$date]!='' ){
            $response =$schedule_day.$schedule_hour. esc_html__('The date & time is already booked. Please choose a new one','wpresidence');
            $response.=implode('|',$calendar);
            echo json_encode(array('sent'=>true, 'response'=>$response ) );
            die();
        }else{
            $calendar[$date] = $temp_array;
            update_post_meta($agent_id,'agent_calendar',$calendar);
        }
    }

endif;






////////////////////////////////////////////////////////////////////////////////
/// Ajax  Package Paypal function
////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_wpestate_ajax_paypal_pack_generation', 'wpestate_ajax_paypal_pack_generation' );

if( !function_exists('wpestate_ajax_paypal_pack_generation') ):

function wpestate_ajax_paypal_pack_generation(){
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
    function wpestate_wpestate_front_property_submit(){


        $action_type               =   sanitize_text_field($_POST['action_type']);
        $front_user_login                =   sanitize_text_field($_POST['front_user_login']);
        $front_user_name             =   sanitize_text_field($_POST['front_user_name']);
        $front_user_pass               =   sanitize_text_field($_POST['front_user_pass']);
        $front_user_email             =   sanitize_text_field($_POST['front_user_email']);


		if( $action_type == 'login' ){
			$user = get_user_by( 'login', $front_user_login );
			if ( $user && wp_check_password( $front_user_pass, $user->data->user_pass, $user->ID) ){

				//wp_clear_auth_cookie();
				wp_set_current_user ( $user->ID );
				wp_set_auth_cookie  ( $user->ID );

				echo json_encode( array( 'result' => 'success', 'redirect' => wpestate_get_template_link( 'page-templates/front_property_submit.php' ) ) );
			}else{
				echo json_encode( array( 'result' => 'error', 'message' => esc_html__('Unable to login. Please, check login/password and try again', 'wpresidence') ) );
			}
		}
		if( $action_type == 'register' ){

			$user_id = username_exists( $front_user_name );

			if( !is_email($front_user_email) ){
				echo json_encode( array( 'result' => 'error', 'message' => esc_html__('Please, enter correct email', 'wpresidence') ) );
				die();
			}


			if ( !$user_id and email_exists($front_user_email) == false ) {
				$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
				$user_id = wp_create_user( $front_user_name, $random_password, $front_user_email );
				if( $user_id ){
					//wp_clear_auth_cookie();
					wp_set_current_user ( $user_id );
					wp_set_auth_cookie  ( $user_id );
					echo json_encode( array( 'result' => 'success', 'redirect' => wpestate_get_template_link( 'page-templates/front_property_submit.php' ) ) );
				}else{
					echo json_encode( array( 'result' => 'error', 'message' => esc_html__('Can\t create user with this credentials', 'wpresidence') ) );
				}

			} else {
				echo json_encode( array( 'result' => 'error', 'message' => esc_html__('Sorry, user already exists. Try different username/email', 'wpresidence') ) );
			}


		}
		die();
    }
endif;



////////////////////////////////////////////////////////////////////////////////
/// Ajax  Package Paypal function - recuring payments REST API
////////////////////////////////////////////////////////////////////////////////
add_action( 'wp_ajax_wpestate_ajax_paypal_pack_recuring_generation_rest_api', 'wpestate_ajax_paypal_pack_recuring_generation_rest_api' );

if( !function_exists('wpestate_ajax_paypal_pack_recuring_generation_rest_api') ):

    function wpestate_ajax_paypal_pack_recuring_generation_rest_api(){
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
?>