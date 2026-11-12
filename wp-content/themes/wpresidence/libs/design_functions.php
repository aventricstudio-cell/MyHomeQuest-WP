<?php




if(!function_exists('wpestate_build_unit_custom_structure')):
function wpestate_build_unit_custom_structure($wpestate_custom_unit_structure,$propID,$wpestate_property_unit_slider){

    $row_no=0;
    $wpestate_custom_unit_structure      =   wpresidence_get_option('wpestate_property_unit_structure');

    if(is_array($wpestate_custom_unit_structure)){
        foreach($wpestate_custom_unit_structure as $rows){

        $row_class=count ($rows);
        $col_md=12;
        if($row_class==2){
            $col_md=6;
        }else if($row_class==3){
            $col_md=4;
        }else if($row_class==4){
            $col_md=3;
        }

        $row_no++;
        foreach($rows as $columns){
            print '<div class="property_unit_custom row_no_'.$row_no.' col-md-'.$col_md.'  ">';
                foreach($columns as $elements){
                    print '<div class="property_unit_custom_element '.$elements['element_name'].' '.$elements['class_name'].' '.$elements['extra_class'];
                    if($elements['element_name']=='custom_div') {
                        print ' '. $elements['text'].' ';
                    }
                    print '"';
                    if($elements['text-align']!='' ) {
                        if( $col_md==12 || $elements['text-align']=='center'){
                            print ' style=" width:100%; " ';
                        } else{
                            print ' style=" float:'.$elements['text-align'].'; " ';
                        }

                    }

                    print '>';
                    wpestate_build_unit_show_detail($elements['element_name'],$propID,$wpestate_property_unit_slider,$elements['text'],$elements['icon']);
                    print '</div>';
                }
            print'</div>';
        }


    }
    }
}
endif;


if(!function_exists('wpestate_build_unit_show_detail')):
function wpestate_build_unit_show_detail($element,$propID,$wpestate_property_unit_slider,$text,$icon){
    $element = strtolower($element);


    switch ($element) {
        case 'share':
            $link=  esc_url (  get_permalink($propID) );
            if ( has_post_thumbnail() ){
                $pinterest = wp_get_attachment_image_src(get_post_thumbnail_id(), 'property_full_map');
            }
            $protocol = is_ssl() ? 'https' : 'http';

            print wpestate_share_unit_desing($propID);

            if($text==''){
                if($icon!=''){
                    if ( strpos($icon, 'fa-') !== false){
                        print '<span class="share_list text_share"  data-bs-toggle="tooltip" title="'.esc_attr__('share','wpresidence').'" ><i class="fa '.esc_attr($icon).'" aria-hidden="true"></i></span>';
                    }else{
                        print '<span class="share_list text_share"  data-bs-toggle="tooltip" title="'.esc_attr__('share','wpresidence').'" ><img src="'.esc_url($icon).'" alt="'.esc_html__('share','wpresidence').'"></span>';
                    }
                }else{
                    print '<span class="share_list"  data-bs-toggle="tooltip" title="'.esc_attr__('share','wpresidence').'" ></span>';
                }

            }else{
               print '<span class="share_list text_share"  data-bs-toggle="tooltip" title="'.esc_attr__('share','wpresidence').'" >'.$text.'</span>';
            }

        break;


        case 'link_to_page':

            $link=  esc_url ( get_permalink($propID));
            if($text==''){
                if ( strpos($icon, 'fa-') !== false){
                    print '<a href="'.esc_url($link).'" target="'.esc_attr(wpresidence_get_option('wp_estate_unit_card_new_page','')).'" ><i class="fa '.esc_attr($icon).'" aria-hidden="true"></i></a>';
                }else{
                    print '<a href="'.esc_url($link).'" target="'.esc_attr(wpresidence_get_option('wp_estate_unit_card_new_page','')).'" ><img src="'.esc_url($icon).'" alt="'.esc_html__('details','wpresidence').'"></a>';
                }
            }else{
               print '<a href="'.esc_url($link).'" target="'.esc_attr(wpresidence_get_option('wp_estate_unit_card_new_page','')).'" >'.str_replace('_',' ',$text).'</a>';

            }

        break;

        case 'favorite':
            $current_user   =   wp_get_current_user();
            $userID         =   $current_user->ID;
            $user_option    =   'favorites'.$userID;
            $favorite_class =   'icon-fav-off';
            $fav_mes        =   esc_html__('add to favorites','wpresidence');
            $user_option    =   'favorites'.$userID;
            $curent_fav     =   get_option($user_option);
            if($curent_fav){
                if ( in_array ($propID,$curent_fav) ){
                    $favorite_class =   'icon-fav-on';
                    $fav_mes        =   esc_html__('remove from favorites','wpresidence');
                }
            }
        print '<span class="icon-fav custom_fav '.esc_attr($favorite_class).'" data-bs-toggle="tooltip" title="'.esc_attr($fav_mes).'" data-postid="'.intval($propID).'"></span>';

        break;


        case 'compare':

          //
            $compare   = wp_get_attachment_image_src(get_post_thumbnail_id(), 'slider_thumb');
            if($text==''){

                if($icon!=''){
                    if ( strpos($icon, 'fa-') !== false){
                        print '<span class="compare-action text_compare" data-bs-toggle="tooltip" title="'.esc_attr__('compare','wpresidence').'" data-pimage="';
                        if( isset($compare[0])){print esc_html($compare[0]);}
                        print '" data-pid="'.intval($propID).'"><i class="fa '.esc_attr($icon).'" aria-hidden="true"></i></span>';

                    }else{
                        print '<span class="compare-action text_compare" data-bs-toggle="tooltip" title="'.esc_attr__('compare','wpresidence').'" data-pimage="';
                        if( isset($compare[0])){print esc_html($compare[0]);}
                        print '" data-pid="'.intval($propID).'"><img src="'.esc_url($icon).'" alt="'.esc_html__('featured icon','wpresidence').'"></span>';
                    }
                }else{
                    print '<span class="compare-action" data-bs-toggle="tooltip" title="'.esc_attr__('compare','wpresidence').'" data-pimage="';
                    if( isset($compare[0])){print esc_html($compare[0]);}
                    print '" data-pid="'.intval($propID).'"></span>';
                }

            }else{
               print '<span class="compare-action text_compare" data-bs-toggle="tooltip" title="'.esc_attr__('compare','wpresidence').'" data-pimage="';
               if( isset($compare[0])){print esc_html($compare[0]);}
               print '" data-pid="'.intval($propID).'">'.$text.'</span>';

            }

        break;


         case 'property_status':
            $prop_stat              =    get_the_terms( $propID, 'property_status');

            if(is_array($prop_stat)){
                foreach ($prop_stat as $key=>$term){
                    if($term->slug!='normal'){
                        print stripslashes($term->name) ;
                    }
                }
            }
        break;




        case 'icon':
            if ( strpos($icon, 'fa-') !== false){
                print '<i class="fa '.$icon.'" aria-hidden="true"></i>';
            }else{
                print '<img src="'.esc_url($icon).'" alt="'.esc_html__('featured icon','wpresidence').'">';
            }
        break;



        case 'featured_icon':
            if(intval  ( get_post_meta($propID, 'prop_featured', true) )==1){

                if($text!=''){
                    print esc_html($text);
                }else{
                    if ( strpos($icon, 'fa-') !== false){
                        print '<i class="fa '.$icon.'" aria-hidden="true"></i>';
                    }else{
                        print '<img src="'.esc_url($icon).'" alt="'.esc_html__('featured icon','wpresidence').'">';
                    }
                }


            }
        break;

        case 'text':
            if (function_exists('icl_translate') ){
                print stripslashes(str_replace('_',' ',$text));
            }else{
                $meta_value =stripslashes(str_replace('_',' ',$text));
                $meta_value = apply_filters( 'wpml_translate_single_string', $meta_value, 'wpestate', 'wp_estate_custom_unit_'.$meta_value );
                print esc_html($meta_value);
            }
        break;

        case 'image':
            wpestate_build_unit_show_detail_image($propID,$wpestate_property_unit_slider);
        break;

        case 'description':
            print wpestate_strip_excerpt_by_char(get_the_excerpt(),115,$propID);
        break;

        case 'title':
            print '<h4><a href="'.esc_url( get_permalink($propID) ).'" target="'.esc_attr(wpresidence_get_option('wp_estate_unit_card_new_page','')).'" >'.get_the_title($propID).'</a></h4>';
        break;

        case 'property_price':
            $wpestate_currency                   =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
            $where_currency             =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
            wpestate_show_price($propID,$wpestate_currency,$where_currency);
        break;

        case 'property_category';
            print get_the_term_list($propID, 'property_category', '', ', ', '') ;
        break;

        case 'property_action_category';
            print get_the_term_list($propID, 'property_action_category', '', ', ', '') ;
        break;

        case 'property_city';
            print get_the_term_list($propID, 'property_city', '', ', ', '') ;
        break;

        case 'property_area';
            print get_the_term_list($propID, 'property_area', '', ', ', '') ;
        break;

        case 'property_county_state';
            print  get_the_term_list($propID, 'property_county_state', '', ', ', '') ;
        break;

        case 'property_agent';
            $agent_id   = intval( get_post_meta($propID, 'property_agent', true) );
            print '<a href="'.esc_url ( get_permalink($agent_id) ).'">'.get_the_title($agent_id).'</a>';
        break;

        case 'property_agent_picture';
            $agent_id   = intval( get_post_meta($propID, 'property_agent', true) );
            $preview            = wp_get_attachment_image_src(get_post_thumbnail_id($agent_id), 'agent_picture_thumb');
            $preview_img         = $preview[0];
            print '<a href="'.esc_url( get_permalink($agent_id)).'" class="property_unit_custom_agent_face" style="background-image:url('.esc_url($preview_img).')"></a>';
        break;

        case 'custom_div';
            print '';
        break;
        case 'property_size';
            print wpestate_get_converted_measure( $propID, 'property_size' );
        break;
        case 'property_lot_size';
            print wpestate_get_converted_measure( $propID, 'property_lot_size' );
        break;
        default:

            if (function_exists('icl_translate') ){
                print  get_post_meta($propID, $element, true);
            }else{
                $meta_value = get_post_meta($propID, $element, true);;
                $meta_value = apply_filters( 'wpml_translate_single_string', $meta_value, 'wpestate', 'wp_estate_custom_unit_'.$meta_value );
                print esc_html($meta_value);
            }
    }


}
endif;


if (!function_exists('wpestate_build_unit_show_detail_image')):
function wpestate_build_unit_show_detail_image($propID,$wpestate_property_unit_slider){

    if ( has_post_thumbnail($propID) ){
        $link       =    esc_url ( get_permalink($propID));
        $title      =   get_the_title($propID);
        $pinterest  =   wp_get_attachment_image_src(get_post_thumbnail_id($propID), 'property_full_map');
        $preview    =   wp_get_attachment_image_src(get_post_thumbnail_id($propID), 'property_listings');
        $compare    =   wp_get_attachment_image_src(get_post_thumbnail_id($propID), 'slider_thumb');
        $extra= array(
            'data-original' =>  $preview[0],
            'class'         =>  'lazyload img-responsive',
        );


        $thumb_prop             =   get_the_post_thumbnail($propID, 'property_listings',$extra);

        if($thumb_prop ==''){
            $thumb_prop_default =  get_theme_file_uri('/img/defaults/default_property_listings.jpg');
            $thumb_prop         =  '<img src="'.esc_url($thumb_prop_default).'" class="b-lazy img-responsive wp-post-image  lazy-hidden" alt="'.esc_html__('icon','wpresidence').'" />';
        }

        print   '<div class="listing-unit-img-wrapper">';

            if(  $wpestate_property_unit_slider==1){
                $post_attachments=wpestate_generate_property_slider_image_ids($propID,false);

                $slides='';

                $no_slides = 0;
                foreach ($post_attachments as $attachment) {
                    $no_slides++;
                    $preview    =   wp_get_attachment_image_src($attachment, 'property_listings');

                    $slides     .= '<div class="item lazy-load-item">
                                        <a href="'.esc_url($link).'"><img  data-lazy-load-src="'.esc_attr($preview[0]).'" alt="'.esc_attr($title).'" class="img-responsive" /></a>
                                    </div>';

                }// end foreach
                $unique_prop_id=uniqid();
                print '
                <div id="property_unit_carousel_'.esc_attr($unique_prop_id).'" class="carousel property_unit_carousel slide " data-ride="carousel" data-interval="false">
                    <div class="carousel-inner">
                        <div class="item active">
                            <a href="'.esc_url($link).'">'.$thumb_prop.'</a>
                        </div>
                        '.$slides.'
                    </div>




                    <a href="'.esc_url($link).'"> </a>';

                    if( $no_slides>0){
                        print '<a class="left  carousel-control" href="#property_unit_carousel_'.$unique_prop_id.'" data-slide="prev">
                            <i class="fas fa-angle-left"></i>
                        </a>

                        <a class="right  carousel-control" href="#property_unit_carousel_'.$unique_prop_id.'" data-slide="next">
                            <i class="fas fa-angle-right"></i>
                        </a>';
                    }
                print'
                </div>';


            }else{
                print   '<a href="'.esc_url($link).'">'.$thumb_prop.'</a>';
                print   '<a href="'.esc_url($link).'"> </a>';
            }




            print   '</div>';


            }
}
endif;


if(!function_exists('wpestate_share_unit_desing')):
function wpestate_share_unit_desing($prop_id,$is_single=0){
    $protocol       =   is_ssl() ? 'https' : 'http';
    $pinterest      =   wp_get_attachment_image_src(get_post_thumbnail_id($prop_id), 'property_full_map');
    $link           =   esc_url ( get_permalink($prop_id) );
    $title          =   get_the_title($prop_id);
    $twiter_status  =   urlencode( $title.' '.$link);
    $email_link     =   'subject='.urlencode ( $title ) .'&body='. urlencode( esc_url($link));
    ob_start();


    $facebook_label     =   '';
    $twiter_label       =   '';
    $pinterest_label    =   '';
    $whatsup_label      =   '';
    $email_label        =   '';

    if(intval($is_single)==1){
        $facebook_label     =    esc_html__('Facebook','wpresidence');
        $twiter_label       =    esc_html__('X - Twitter','wpresidence');
        $pinterest_label    =    esc_html__('Pinterest','wpresidence');
        $whatsup_label      =    esc_html__('WhatsApp','wpresidence');
        $email_label        =    esc_html__('Email','wpresidence');
    }
    $whatsup_link       =  wpestate_return_agent_whatsapp_call($prop_id,'');

    ?>
    <div class="share_unit">
        <a href="<?php print esc_html($protocol);?>://www.facebook.com/sharer.php?u=<?php echo esc_url($link); ?>&amp;t=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noreferrer" class="social_facebook"><?php echo esc_html($facebook_label);?></a>
        <a href="<?php print esc_html($protocol);?>://twitter.com/intent/tweet?text=<?php echo esc_html($twiter_status); ?>" class="social_tweet" rel="noreferrer" target="_blank"><?php echo esc_html($twiter_label);?></a>
        <a href="<?php print esc_html($protocol);?>://pinterest.com/pin/create/button/?url=<?php echo esc_url($link); ?>&amp;media=<?php if (isset( $pinterest[0])){ echo esc_url($pinterest[0]); }?>&amp;description=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noreferrer" class="social_pinterest"><?php echo esc_html($pinterest_label);?></a>
        <a href="<?php print esc_url($whatsup_link); ?>" class="social_whatsup" rel="noreferrer" target="_blank"><?php echo esc_html($whatsup_label);?></a>

        <a href="mailto:email@email.com?<?php echo trim(esc_html($email_link));?>" data-action="share email"  class="social_email"><?php echo esc_html($email_label);?></a>

    </div>
    <?php

    $return = ob_get_contents();
    ob_end_clean();
    return   $return ;

}
endif;


/**
 * Optimized function to generate share buttons using cached property data.
 * Utilizes provided cached data for enhanced performance.
 */
if (!function_exists('wpestate_share_unit_desing_from_cache')):
    function wpestate_share_unit_desing_from_cache($property_unit_cached_data, $prop_id, $is_single = 0) {
        $protocol       = is_ssl() ? 'https' : 'http';
        $link           = esc_url($property_unit_cached_data['permalink'] ?? get_permalink($prop_id));
        $title          = esc_html($property_unit_cached_data['title'] ?? get_the_title($prop_id));
        $pinterest      = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $prop_id, 'featured_media', 'full');
        $twiter_status  = urlencode($title . ' ' . $link);
        $email_link     = 'subject=' . urlencode($title) . '&body=' . urlencode($link);
        $whatsup_link   = wpestate_return_agent_whatsapp_call($prop_id, '');
    
        ob_start();
    
        $labels = [
            'facebook'  => '',
            'twitter'   => '',
            'pinterest' => '',
            'whatsapp'  => '',
            'email'     => '',
        ];
    
        if (intval($is_single) == 1) {
            $labels['facebook']  = esc_html__('Facebook', 'wpresidence');
            $labels['twitter']   = esc_html__('X - Twitter', 'wpresidence');
            $labels['pinterest'] = esc_html__('Pinterest', 'wpresidence');
            $labels['whatsapp']  = esc_html__('WhatsApp', 'wpresidence');
            $labels['email']     = esc_html__('Email', 'wpresidence');
        }
    
        ?>
        <div class="share_unit">
            <a href="<?php echo esc_url("{$protocol}://www.facebook.com/sharer.php?u={$link}&t=" . urlencode($title)); ?>" target="_blank" rel="noreferrer" class="social_facebook"><?php echo $labels['facebook']; ?></a>
            <a href="<?php echo esc_url("{$protocol}://twitter.com/intent/tweet?text={$twiter_status}"); ?>" class="social_tweet" rel="noreferrer" target="_blank"><?php echo $labels['twitter']; ?></a>
            <a href="<?php echo esc_url("{$protocol}://pinterest.com/pin/create/button/?url={$link}&media={$pinterest}&description=" . urlencode($title)); ?>" target="_blank" rel="noreferrer" class="social_pinterest"><?php echo $labels['pinterest']; ?></a>
            <a href="<?php echo esc_url($whatsup_link); ?>" class="social_whatsup" rel="noreferrer" target="_blank"><?php echo $labels['whatsapp']; ?></a>
            <a href="mailto:?<?php echo antispambot(esc_attr($email_link)); ?>" data-action="share email" class="social_email"><?php echo $labels['email']; ?></a>
        </div>
        <?php
    
        $output = ob_get_contents();
        ob_end_clean();
    
        return $output;
    }
    endif;