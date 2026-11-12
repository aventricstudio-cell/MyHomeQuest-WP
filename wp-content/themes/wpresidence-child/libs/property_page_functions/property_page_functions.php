<?php


/**
* Property Prepare Label
*
* @since    3.0.3
*
*/
if( !function_exists('wpestate_property_page_prepare_label') ):
    function wpestate_property_page_prepare_label($theme_option,$default){
        if (function_exists('icl_translate') ){
            $section_label  =   icl_translate('wpestate',$theme_option, esc_html( wpresidence_get_option($theme_option) ) );
        }else{
            $section_label  =   esc_html( wpresidence_get_option($theme_option) );
 
        }
            
        if($section_label!=''){
            $section_label = esc_html($section_label);
        }else{
            $section_label =$default;
        }

        return $section_label;
    }
endif;





/**
 * Create a tab item for the property page
 *
 * This function generates the HTML for a single tab item and its corresponding content panel.
 * It's designed to work with Bootstrap 5.3's tab system.
 *
 * @since 3.0.3
 * @package WpResidence
 * @subpackage PropertyTabs
 *
 * @param string $content The content of the tab panel.
 * @param string $label The label for the tab.
 * @param string $tab_id The unique identifier for the tab.
 * @param string $class Additional CSS classes for the tab item (optional).
 * @return array An array containing the tab list item and the tab panel HTML.
 */
if (!function_exists('wpestate_property_page_create_tab_item')) :
    function wpestate_property_page_create_tab_item($content, $label, $tab_id, $class = '')
    {

    
        // Prepare the return array
        $return_array = array();

        // Generate the tab list item HTML
        $list = sprintf(
            '<li class="nav-item" role="presentation">
                <button class="nav-link %s" id="%s-tab" data-bs-toggle="tab" data-bs-target="#%s" type="button" role="tab" aria-controls="%s" aria-selected="%s">
                    %s
                </button>
            </li>',
            esc_attr($class),
            esc_attr($tab_id),
            esc_attr($tab_id),
            esc_attr($tab_id),
            $class === 'active' ? 'true' : 'false',
            esc_html(trim($label))
        );

        // Generate the tab panel HTML
        $tab_panel = sprintf(
            '<div class="tab-pane fade %s" id="%s" role="tabpanel" aria-labelledby="%s-tab">
                %s
            </div>',
            esc_attr($class) . ($class === 'active' ? ' show' : ''),
            esc_attr($tab_id),
            esc_attr($tab_id),
            trim($content)
        );

        // Assign the generated HTML to the return array
        $return_array['list'] = $list;
        $return_array['tab_panel'] = $tab_panel;

        return $return_array;
    }
endif;



/**
 * Create an accordion section for property details.
 *
 * This function generates an HTML structure for an accordion section
 * using Bootstrap 5.3 components. It's designed to display property
 * information in a collapsible format.
 *
 * @param string $content The content to be displayed in the accordion body.
 * @param string $label   The label for the accordion header.
 * @param string $id      The unique identifier for the accordion wrapper.
 * @param string $acc_id  The unique identifier for the accordion item.
 * @return string The HTML markup for the accordion section.
 */
if (!function_exists('wpestate_property_page_create_acc')) :
    function wpestate_property_page_create_acc($content, $label, $id, $acc_id) {
        // Return empty string if content is empty
        if (trim($content) === '') {
            return '';
        }

        // Prepare the variables for the HTML template
        $escaped_id = esc_attr($id);
        $escaped_acc_id = esc_attr($acc_id);
        $escaped_label = wp_kses_post(trim($label));
        $escaped_content = (trim($content));

        // HTML template for the accordion
        ob_start();
        ?>
        <div class="accordion property-panel" id="<?php echo $escaped_id; ?>">
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading_<?php echo $escaped_acc_id; ?>">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#<?php echo $escaped_acc_id; ?>" 
                            aria-expanded="true" aria-controls="<?php echo $escaped_acc_id; ?>">
                        <?php echo $escaped_label; ?>
                    </button>
                </h2>
                <div id="<?php echo $escaped_acc_id; ?>" class="accordion-collapse collapse show" 
                     aria-labelledby="heading_<?php echo $escaped_acc_id; ?>">
                    <div class="accordion-body">
                        <?php echo $escaped_content; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
endif;








/**
* Slider maps
*
* @since    3.0.3
*
*/
if( !function_exists('wpestate_slider_enable_maps') ):
function wpestate_slider_enable_maps($header_type,$global_header_type){

    global $post;
    $return = '';

    $show_map_on_property           =   esc_html ( wpresidence_get_option('wp_estate_show_map_prop_page2','') );

    if($show_map_on_property==='no'){
      return;
    }


    if ( $header_type == 0 ){ // global
        if ($global_header_type != 4){
                $gmap_lat                   =   esc_html( get_post_meta($post->ID, 'property_latitude', true));
                $gmap_long                  =   esc_html( get_post_meta($post->ID, 'property_longitude', true));
                $property_add_on            =   ' data-post_id="'.intval($post->ID).'" data-cur_lat="'.esc_attr($gmap_lat).'" data-cur_long="'.esc_attr($gmap_long).'" ';

                $return.=' <div id="slider_enable_map" data-bs-placement="bottom" data-bs-toggle="tooltip" title="'. esc_attr__('Map','wpresidence').'"> <i class="fas fa-map-marker-alt"></i> </div>';

                
                $no_street=' no_stret ';
                if ( get_post_meta($post->ID, 'property_google_view', true) ==1){
                    $return.= '<div id="slider_enable_street" class="'.wpresidence_return_class_leaflet().'" data-bs-placement="bottom" data-bs-toggle="tooltip" title="'.esc_attr__('Street View','wpresidence').'"> <i class="fas fa-location-arrow"></i>    </div>';
                    $no_street='';
                }

                $return.='
                <div id="slider_enable_slider" data-bs-placement="bottom" data-bs-toggle="tooltip" title="'.esc_attr__('Image Gallery','wpresidence').'" class="slideron '.esc_attr($no_street).'"> <i class="far fa-image"></i></div>

                <div id="gmapzoomplus"  class="smallslidecontrol"><i class="fas fa-plus"></i> </div>
                <div id="gmapzoomminus" class="smallslidecontrol"><i class="fas fa-minus"></i></div>
                '.wpestate_show_poi_onmap().'
                <div id="googleMapSlider"'.trim($property_add_on).' >
                </div>';

        }
    }else{
        if($header_type!=5){
                $gmap_lat                   =   esc_html( get_post_meta($post->ID, 'property_latitude', true));
                $gmap_long                  =   esc_html( get_post_meta($post->ID, 'property_longitude', true));
                $property_add_on            =   ' data-post_id="'.intval($post->ID).'" data-cur_lat="'.esc_attr($gmap_lat).'" data-cur_long="'.esc_attr($gmap_long).'" ';
                $return                     .=  '<div id="slider_enable_map" data-bs-placement="bottom" data-bs-toggle="tooltip" title="'.esc_attr__('Map','wpresidence').'"><i class="fas fa-map-marker-alt"></i></div>';

                $no_street=' no_stret ';
                if ( get_post_meta($post->ID, 'property_google_view', true) ==1){
                    $return     .=  '  <div id="slider_enable_street" class="'.wpresidence_return_class_leaflet().'"  data-bs-placement="bottom" data-bs-toggle="tooltip" title="'.esc_attr__('Street View','wpresidence').'" > <i class="fas fa-location-arrow"></i>    </div>';
                    $no_street  =   '';
                }
                $return .= '<div id="slider_enable_slider" data-bs-placement="bottom" data-bs-toggle="tooltip" title="'.esc_attr__('Image Gallery','wpresidence').'" class="slideron '.esc_attr($no_street).'"> <i class="far fa-image"></i>         </div>

                <div id="gmapzoomplus"  class="smallslidecontrol" ><i class="fas fa-plus"></i> </div>
                <div id="gmapzoomminus" class="smallslidecontrol" ><i class="fas fa-minus"></i></div>
                '.wpestate_show_poi_onmap().'
                <div id="googleMapSlider" '.trim($property_add_on).' >
                </div>';

        }
    }

    return $return;
}
endif;

/**
* Slider maps v2
*
* @since    3.0.3
*
*/
if( !function_exists('wpestate_slider_enable_maps_v2') ):
    function wpestate_slider_enable_maps_v2($postID,$style_css="none"){
    
        $return = '';
        $gmap_lat                   =   esc_html( get_post_meta($postID, 'property_latitude', true));
        $gmap_long                  =   esc_html( get_post_meta($postID, 'property_longitude', true));
        $property_add_on            =   ' data-post_id="'.intval($postID).'" data-cur_lat="'.esc_attr($gmap_lat).'" data-cur_long="'.esc_attr($gmap_long).'" ';
    
        $return.='<div class="google_map_slider_wrapper wpestate_property_slider_thing" style="display:'.esc_attr($style_css).'">
        <div id="gmapzoomplus"  class="smallslidecontrol"><i class="fas fa-plus"></i> </div>
        <div id="gmapzoomminus" class="smallslidecontrol"><i class="fas fa-minus"></i></div>
        '.wpestate_show_poi_onmap().'
        <div id="googleMapSlider"'.trim($property_add_on).' >
        </div>
        </div>';
    
    
        return $return;
    }
endif;
    
/**
* Slider maps v2
*
* @since    3.0.3
*
*/
if( !function_exists('wpestate_slider_enable_video') ):
    function wpestate_slider_enable_video($postID,$style_css="none"){
        $video_id           = esc_html( get_post_meta($postID, 'embed_video_id', true) );
        $video_type         = esc_html( get_post_meta($postID, 'embed_video_type', true) );

        $return_string='<div class="wpestate_slider_enable_video_wrapper wpestate_property_slider_thing" style="display:'.esc_attr($style_css).'">';

        if($video_id!=''){
            if($video_type=='vimeo'){
                $return_string.= wpestate_custom_vimdeo_video($video_id);
            }else{
                $return_string.= wpestate_custom_youtube_video($video_id);
            }    
        }
        $return_string.='</div>';

        return $return_string;
    }
endif;


/**
* Slider virtual v2
*
* @since    3.0.3
*
*/
if( !function_exists('wpestate_slider_enable_virtual') ):
    function wpestate_slider_enable_virtual($postID,$style_css="none"){

        $return_string='<div class="wpestate_slider_enable_virtual_wrapper wpestate_property_slider_thing" style="display:'.esc_attr($style_css).'" >';
        $return_string.=get_post_meta($postID, 'embed_virtual_tour', true);
        $return_string.='</div>';
        return $return_string;
    }
endif;

/**
* terms
*
* @since    3.0.3
*
*/


if( !function_exists('wpestate_return_first_term') ):
function wpestate_return_first_term($terms,$taxonomy){

    $terms_array=explode(",",$terms);

    if(isset($terms_array[0]) && $terms_array[0]!=''){
        $term = get_term_by('id',$terms_array[0],$taxonomy);
        return $term->name;
    }else{
        return 'all';
    }
}

endif;





/**
* Filter Bar
*
* @since    3.0.3
*
*/



if( !function_exists('wpestate_filter_bar') ):
function wpestate_filter_bar($filter_data){
    $return_string='';
    ob_start();
    $is_shortcode='yes';

    include( locate_template('templates/properties_list_templates/property_list_filters.php') );
    $filters = ob_get_contents();
    ob_end_clean();

    $return_string.=$filters;
    return $return_string;
}
endif;


/**
*
* add global details to transient name
*
*@since    3.0.7
*
*/
if( !function_exists('wpestate_add_global_details_transient') ):
function wpestate_add_global_details_transient($transient_name){
  if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
      $transient_name.='_'. ICL_LANGUAGE_CODE;
  }

  if ( isset($_COOKIE['my_custom_curr_symbol'] ) ){
      $transient_name.='_'.$_COOKIE['my_custom_curr_symbol'];
  }

  if(isset($_COOKIE['my_measure_unit'])){
      $transient_name.= $_COOKIE['my_measure_unit'];
  }
  return   $transient_name;
}
endif;




/**
*
* return what type of property card
*
*@since    3.0.3
*
*/

if( !function_exists('wpestate_return_property_card_type') ):
function wpestate_return_property_card_type($attributes){
  $property_card_type         =   intval(wpresidence_get_option('wp_estate_unit_card_type'));
  $property_card_type_string  =   '';

  if(isset( $attributes['card_version']) && is_numeric( $attributes['card_version']) ){
        $property_card_type  =  intval($attributes['card_version']);
  }

  if($property_card_type==0){
      $property_card_type_string='';
  }else{
      $property_card_type_string='_type'.$property_card_type;
  }

  return $property_card_type_string;

}
endif;












/*
 * Listing video
 *
 *
 *
 *
 */
if( !function_exists('wpestate_listing_video') ):
function wpestate_listing_video($post_id,$wpestate_prop_all_details=''){
    $full_img_path='';
    if($wpestate_prop_all_details==''){
        $full_img_path  = get_post_meta($post_id, 'property_custom_video', true);
        $video_id           =   esc_html( get_post_meta($post_id, 'embed_video_id', true) );
        $video_type         =   esc_html( get_post_meta($post_id, 'embed_video_type', true) );
    }else{
        $full_img_path      =   esc_html ( wpestate_return_custom_field( $wpestate_prop_all_details,'property_custom_video') );
        $video_id           =   esc_html ( wpestate_return_custom_field( $wpestate_prop_all_details,'embed_video_id') );
        $video_type         =   esc_html ( wpestate_return_custom_field( $wpestate_prop_all_details,'embed_video_type') );
    }

    if($full_img_path==''){
        $thumb_id           =   get_post_thumbnail_id($post_id);
        $full_img           =   wp_get_attachment_image_src( $thumb_id, 'listing_full_slider_1' );
        if(isset($full_img[0])){
            $full_img_path      =   $full_img[0];
        }
    }


    $video_link         =   '';
    $protocol           =   is_ssl() ? 'https' : 'http';
    if($video_type=='vimeo'){
        $video_link .=  $protocol.'://player.vimeo.com/video/' . $video_id . '?api=1&amp;player_id=player_1';
    }else{
        $video_link .=  $protocol.'://www.youtube.com/embed/' . $video_id  . '?wmode=transparent&amp;rel=0';
    }
    return '<div class="property_video_wrapper" ><div id="property_video_wrapper_player"></div><a href="'.esc_url($video_link).'"  data-autoplay="true" data-vbtype="video" class="venobox"><img  src="'.esc_url($full_img_path).'"  alt="'.esc_html__('video image','wpresidence').'" /></a></div>';

}
endif;


/*
 * 
 *
 *
 *
 *
 */


if( !function_exists('wpestate_build_terms_array') ):
    function wpestate_build_terms_array(){
        $parsed_features = wpestate_request_transient_cache( 'wpestate_get_features_array' );
        if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
            $parsed_features=false;
        }
            if($parsed_features===false){

                $parsed_features=array();
                $terms = get_terms( array(
                    'taxonomy' => 'property_features',
                    'hide_empty' => false,
                    'parent'=> 0

                ));


                foreach($terms as $key=>$term){
                    $temp_array=array();
                    $child_terms = get_terms( array(
                        'taxonomy' => 'property_features',
                        'hide_empty' => false,
                        'parent'=> $term->term_id
                    ));

                    $children=array();
                    if(is_array($child_terms)){
                        foreach($child_terms as $child_key=>$child_term){
                            $children[]=$child_term->name;
                        }
                    }

                    $temp_array['name']=$term->name;
                    $temp_array['childs']=$children;

                    $parsed_features[]=$temp_array;
                }
                if ( !defined( 'ICL_LANGUAGE_CODE' ) ) {
                    wpestate_set_transient_cache('wpestate_get_features_array',$parsed_features,60*60*4);
                }
            }

            return $parsed_features;
    }
endif;













if( !function_exists('estate_listing_content') ):
function estate_listing_content($post_id){
    $content='';
    $args= array(
        'post_type'         => 'estate_property',
        'post_status'       => 'publish',
        'p' => $post_id
    );
    $the_query = new WP_Query( $args);


       while ($the_query->have_posts()) :
            $the_query->the_post();

            $content= get_the_content();
        endwhile;

        wp_reset_postdata();

   // $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    $content = wpautop($content, false);
    $content=apply_filters( 'the_content', $content );
    $args = array(  'post_mime_type'    => 'application/pdf',
                'post_type'         => 'attachment',
                'numberposts'       => -1,
                'post_status'       => null,
                'post_parent'       => $post_id
        );

    $attachments = get_posts($args);

    if ($attachments) {

        $content.= '<div class="download_docs">'.esc_html__('Documents','wpresidence').'</div>';
        foreach ( $attachments as $attachment ) {
          
       
            $content .= '<div class="document_down">';
                ob_start();
                include (locate_template('templates/svg_icons/pdf_icon.svg'));
                $icon=ob_get_contents();
                ob_clean();

                $content .= $icon.'<a href="' . esc_url(wp_get_attachment_url($attachment->ID)) . '" target="_blank">' . esc_html($attachment->post_title) . '</a></div>';
           
        }
    }

    wp_reset_postdata();


    return $content;

}
endif;













if( !function_exists('estate_listing_address_printing') ):
function estate_listing_address_printing($post_id){

    $property_address   = esc_html( get_post_meta($post_id, 'property_address', true) );
    $property_city      = strip_tags (  get_the_term_list($post_id, 'property_city', '', ', ', '') );
    $property_area      = strip_tags ( get_the_term_list($post_id, 'property_area', '', ', ', '') );
    $property_county    = strip_tags ( get_the_term_list($post_id, 'property_county_state', '', ', ', '')) ;
    $property_zip       = esc_html(get_post_meta($post_id, 'property_zip', true) );
    $property_country   = esc_html(get_post_meta($post_id, 'property_country', true) );

    $return_string='';

    if ($property_address != ''){
        $return_string.='<div class="listing_detail col-md-4"><strong>'.esc_html__('Address','wpresidence').':</strong> ' . $property_address . '</div>';
    }
    if ($property_city != ''){
        $return_string.= '<div class="listing_detail col-md-4"><strong>'.esc_html__('City','wpresidence').':</strong> ' .$property_city. '</div>';
    }
    if ($property_area != ''){
        $return_string.= '<div class="listing_detail col-md-4"><strong>'.esc_html__('Area','wpresidence').':</strong> ' .$property_area. '</div>';
    }
    if ($property_county != ''){
        $return_string.= '<div class="listing_detail col-md-4"><strong>'.esc_html__('State/County','wpresidence').':  </strong> ' . $property_county . '</div>';
    }
   
    if ($property_zip != ''){
        $return_string.= '<div class="listing_detail col-md-4"><strong>'.esc_html__('Zip','wpresidence').':</strong> ' . $property_zip . '</div>';
    }
    if ($property_country != '') {
        $return_string.= '<div class="listing_detail col-md-4"><strong>'.esc_html__('Country','wpresidence').':</strong> ' . $property_country . '</div>';
    }


    return  $return_string;
}
endif; // end   estate_listing_address




/*
* Return column size for details, address and features section
*/



if( !function_exists('wpestat_get_content_comuns') ):
    function wpestat_get_content_comuns($columns,$where=''){

        if($columns==''){ // not custom template
            $colmd              =   intval( wpresidence_get_option('wp_estate_details_colum', '') );

            if($where=='details'){
                $colmd              =   intval( wpresidence_get_option('wp_estate_details_colum', '') );

            }else if($where=='address'){
                $colmd              =   intval( wpresidence_get_option('wp_estate_address_column', '') );

            }else if($where=='features'){
                $colmd              =   intval( wpresidence_get_option('wp_estate_features_colum', '') );

            }


            if($colmd=='') {
                $colmd=4;
            }
        }else{
            $col_args=array(
                '1' => '12',
                '2' => '6',
                '3' => '4',
                '4' => '3',
                '5' => '2',
                '6'=>'2',
            );
            $colmd=$col_args[$columns];

        }
        return $colmd;

    }
endif;





/*
*
* WpEstate Property section disclaimer
*
*/


if( !function_exists('wpestate_property_disclaimer_section') ):
    function wpestate_property_disclaimer_section($post_id){
        $wpestate_disclaimer_text  =   wpresidence_get_option('wp_estate_disclaiment_text', '') ;

        $to_replace=array(
            '%property_address' =>  esc_html(get_post_meta($post_id, 'property_address', true)),
            '%propery_id'       =>  $post_id
        );



        foreach ($to_replace as $key=>$value):

            $wpestate_disclaimer_text=str_replace($key,$value, $wpestate_disclaimer_text);
            
        endforeach;


        return '<div class="row wpestate_property_disclaimer">'.trim($wpestate_disclaimer_text).'</div>';
    }
endif;
    






?>
