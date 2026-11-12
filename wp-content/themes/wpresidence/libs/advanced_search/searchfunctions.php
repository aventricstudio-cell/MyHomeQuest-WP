<?php
/*
 *
 *
 *
 *
 *
 *
 */
function wpestate_show_geolocation_field($appendix, $placeholder,$elementor_label,$term_counter_elementor='',$position=''){
    if($placeholder==''){
        $placeholder=esc_html__('Location','wpresidence');
    }
    if($position=='mobile'){
        $term_counter_elementor=intval($term_counter_elementor)+10;
    }
    $return_string='<div class="col-md-12 w-100">
        <input type="text" id="geolocation_search'.$term_counter_elementor.'" class="form-control geolocation_search_item" name="geolocation_search"  placeholder="'.$placeholder.'" value="';
            if(isset($_GET['geolocation_search'])){
                $return_string.= esc_attr(stripslashes($_GET['geolocation_search']) );
            }
    $return_string.='">';                 


    $return_string.='<input type="hidden" id="geolocation_lat" class="geolocation_lat" name="geolocation_lat" value="'; 
        if(isset($_GET['geolocation_lat'])){
            $return_string.= esc_attr(stripslashes($_GET['geolocation_lat']) );
        }
    $return_string.='">';
   
    $return_string.='<input type="hidden" id="geolocation_long" class="geolocation_long" name="geolocation_long" value="';
        if(isset($_GET['geolocation_long'])){
            $return_string.= esc_attr(stripslashes($_GET['geolocation_long']) );
        }
    $return_string.='"></div>';
    
    

 

    if ( ! wp_script_is( 'geolocation_search_radius', 'enqueued' ) ) {
      
        wp_enqueue_script('geolocation_search_radius');
    }
    return $return_string;
}





/*
 *
 *
 *
 *
 *
 *
 */
function wpestate_show_geolocation_radius_field($appendix, $placeholder,$elementor_label, $term_counter_elementor=''){
    if($placeholder==''){
        $placeholder=esc_html__('Radius: ','wpresidence');
    }
    
    $radius_measure = wpresidence_get_option('wp_estate_geo_radius_measure','');
    if(  $radius_measure=='miles'){
      $radius_measure=esc_html('miles','wpresidence');
    }else{
        $radius_measure=esc_html('km','wpresidence');
    }
    $radius_value = wpresidence_get_option('wp_estate_initial_radius','');

    if(isset($_GET['geolocation_radius'])){
        $radius_value= intval( $_GET['geolocation_radius'] );
    }
    $return_string  =   '<div class="adv_search_geo_radius_wrapper">';
    $return_string .=   '<p class="label_radius_wrapper_p"><label class="label_radius label_radius_search">'. $placeholder.'<span class="radius_value">'.intval($radius_value).' '.esc_html($radius_measure).'</span></label></p>';
    $return_string .=   '<div class="wpestate_slider_radius_search"></div>';
    $return_string .=   '<input type="hidden" id="geolocation_radius" class="geolocation_radius" name="geolocation_radius" value="'.intval($radius_value).'">';
    $return_string .=   '</div>';
    

    
    if ( ! wp_script_is( 'geolocation_search_radius', 'enqueued' ) ) {
        wp_enqueue_script('geolocation_search_radius');
    }
    return $return_string;
}
/*
 *
 *
 *
 *
 *
 *
 */

if(!function_exists('wpestate_show_advanced_search_tabs')):
function wpestate_show_advanced_search_tabs($adv_submit,$position=''){
        $return='';
        $adv_search_type            =   wpresidence_get_option('wp_estate_adv_search_type','');
        $adv6_taxonomy                  =   wpresidence_get_option('wp_estate_adv6_taxonomy');
        $adv6_taxonomy_terms            =   wpresidence_get_option('wp_estate_adv6_taxonomy_terms');
        $adv_search_what                =   wpresidence_get_option('wp_estate_adv_search_what','');
      
        $adv_search_fields_no_per_row   =   floatval( wpresidence_get_option('wp_estate_search_fields_no_per_row') );
        $extended_search                =   wpresidence_get_option('wp_estate_show_adv_search_extended','');
        $args                       =   wpestate_get_select_arguments();
        $action_select_list         =   wpestate_get_action_select_list($args);
        $categ_select_list          =   wpestate_get_category_select_list($args);
        $select_city_list           =   wpestate_get_city_select_list($args);
        $select_area_list           =   wpestate_get_area_select_list($args);
        $select_county_state_list   =   wpestate_get_county_state_select_list($args);

        global $post;
        $post_id ='';
        if( isset($post->ID) ){
            $post_id =$post->ID;
         }


        $return.= '<div role="tabpanel" class="adv_search_tab '.wpestate_search_tab_align().' wpestate_show_advanced_search_tabs_function" >';

            $tab_items      =   '';
            $tab_content    =   '';
            $active         =   'active';
            $active_for_get=  'active';
            if(isset($_GET['adv6_search_tab']) && $_GET['adv6_search_tab']!=''){
                $active         =   '';
                $active_for_get=  '';
            }


            $term_counter=0;
            $adv_search_fields_no               =   floatval( wpresidence_get_option('wp_estate_adv_search_fields_no') );

            if(is_array($adv6_taxonomy_terms)):
                foreach ($adv6_taxonomy_terms as $term_id){
                    $term               =   get_term( $term_id, $adv6_taxonomy);
                 
                    $use_name           =   '';
                    if(isset($term->name)){
                        $use_name           =   sanitize_title($term->name);
                    }
                    $use_title_name     =  '';
                    if(isset(  $term->name)){
                        $use_title_name     =   $term->name;
                    }


                    if(isset($_GET['adv6_search_tab']) && ( $_GET['adv6_search_tab']==$use_name ||  $_GET['adv6_search_tab']==$term->slug ) ){
                        $active         =   'active';
                        $active_for_get=  'active';
                    }

                    $tab_items .= '<div 
                    class="nav-item" 
                    role="presentation">
                    <button 
                        class="nav-link adv_search_tab_item ' . esc_attr($active) . ' ' . esc_attr($use_name) . '" 
                        id="' . urldecode($use_name . $position) . '-tab"
                        data-bs-toggle="tab" 
                        data-bs-target="#' . urldecode($use_name . $position) . '-pane"
                        type="button"
                        role="tab" 
                        aria-controls="' . urldecode($use_name . $position) . '-pane"
                        aria-selected="' . ($active === 'active' ? 'true' : 'false') . '"
                        data-term="' . esc_attr($use_name) . '" 
                        data-termid="' . esc_attr($term_id) . '" 
                        data-tax="' . esc_attr($adv6_taxonomy) . '">
                        ' . urldecode(str_replace("-", " ", $use_title_name)) . '
                    </button>
                </div>';

                    $tab_class='';
                    if(isset($_GET['adv6_search_tab'])){
                        $tab_class=esc_html($_GET['adv6_search_tab']);
                    }

                    
                    if($active=='active'){
                        $active.=' show ';
                        $aria_selected_tag='aria-selected=true';    
                    }else{
                        $aria_selected_tag='aria-selected=false';  
                    }

                    $tab_content .= '<div 
                    class="tab-pane fade ' . esc_attr($tab_class) . ' ' . esc_attr($active) . '" 
                    id="' . urldecode($use_name . $position) . '-pane"
                    role="tabpanel"
                    aria-labelledby="' . urldecode($use_name . $position) . '-tab"
                    tabindex="0">';
            
                        if( !wpestate_half_map_conditions ($post_id)){
                            $tab_content.='<form class="row  gx-2 gy-2" serch5 role="search" method="get" action="'.esc_url($adv_submit).'" >';
                        }else{
                            $tab_content.='<div class="row  gx-2 gy-2">';
                        }


                                if($adv6_taxonomy=='property_category'){
                                    $tab_content.='<input type="hidden" name="property_category[]" value="'.esc_html($use_name).'" >';
                                }else if($adv6_taxonomy=='property_action_category'){
                                    $tab_content.='<input type="hidden" name="property_action_category[]" value="'.esc_html($use_name).'" >';
                                }else if($adv6_taxonomy=='property_city'){
                                    $tab_content.='<input type="hidden" name="property_city" value="'.esc_html($use_name).'" >';
                                }else if($adv6_taxonomy=='property_area'){
                                    $tab_content.='<input type="hidden" name="property_area" value="'.esc_html($use_name).'" >';
                                }else if($adv6_taxonomy=='property_county_state'){
                                    $tab_content.='<input type="hidden" name="property_county_state" value="'.esc_html($use_name).'" >';
                                }


                                $tab_content.='<input type="hidden" name="adv6_search_tab" value="'.esc_html($use_name).'">
                                <input type="hidden" name="term_id" class="term_id_class" value="'.esc_html($term_id).'">
                                <input type="hidden" name="term_counter" class="term_counter" value="'.intval($term_counter).'">

                                '.wpestate_show_adv6_form($active_for_get,$position,$adv_search_what,$adv_search_fields_no_per_row,$action_select_list,$categ_select_list,$select_city_list,$select_area_list,$select_county_state_list,$use_name,$term_id,$adv_search_fields_no,$term_counter);
                                if($extended_search=='yes'){
                                    ob_start();
                                if($position==''){$position='adv';}
                                    show_extended_search($position,$use_name);
                                    $tab_content.=ob_get_contents();
                                    ob_end_clean();
                                }
                        if( !wpestate_half_map_conditions ($post_id)){
                            $tab_content.='</form>';
                        }else{
                                $tab_content.=' </div>';
                        }
                    $tab_content.=' </div>';
                    $active='';
                    $active_for_get='';
                    $term_counter++;
                }
            endif;


            $return.=  '<div class="nav nav-tabs" role="tablist">'.$tab_items.'</div>';   //escaped above
            $return.=  '<div class="tab-content">'.$tab_content.'</div>';//escaped above
        $return.= '</div>';




        return  $return;
}
endif;




/*
 *
 * Show location field
 *
 *
 */




if(!function_exists('wpestate_show_location_field')):
function wpestate_show_location_field($position,$term_counter=''){
    // Step 1: Keep the existing input id for autocomplete hooks.
    $input_id = $position.'adv_location_'.$term_counter;
    // Step 2: Read the canonical location value and keep the old key as fallback for old URLs.
    $location_value = '';
    if(isset($_GET['location'])){
        $location_value = $_GET['location'];
    }else if(isset($_GET['adv_location'])){
        $location_value = $_GET['adv_location'];
    }
    $return_string='<div class="adv_locations_search_component_wrapper">
    <input type="text" id="'.esc_attr($input_id).'" class="form-control adv_locations_search" name="location"  placeholder="'. esc_html__('Enter an address, state, city, area or zip code','wpresidence').'" value="';
        
    
        if($location_value!==''){
            // Step 3: Only hydrate the value for the active tab when term counters are used.
            if( isset($_GET['term_counter'])   ){
                if(intval($_GET['term_counter'])==$term_counter ){
                    $return_string.= esc_attr(stripslashes($location_value) );
                }
               
            }else{
                $return_string.= esc_attr(stripslashes($location_value) );
            }
            
        }
    $return_string.='">';

    $availableTags  =  wpestate_return_data_for_location_autocomplete();

    $return_string .= '<script type="text/javascript">    //<![CDATA[
        jQuery(document).ready(function(){
            var availableTagsData = ' . $availableTags . ';
            wpresidenceInitializeAutocomplete(availableTagsData,"'.$input_id.'");
        });
    //]]>
    </script></div>';
    return $return_string;

}
endif;


/*
 *
 *
 *
 *
 *
 *
 */
if(!function_exists('wpestate_return_data_for_location_autocomplete')):
function wpestate_return_data_for_location_autocomplete(){

    $availableTags  =   '';
    $availableTags  =   wpestate_request_transient_cache('wpestate_search_location_tags');
    $availableTags  =   false;
    
    $availableTags_array=array();


    if($availableTags==false){
       $availableTags='';
       $args = array(
           'orderby' => 'count',
           'hide_empty' => 0,
       );

       $terms = get_terms( 'property_city', $args );
       foreach ( $terms as $term ) {
          $availableTags.= '"'. wp_specialchars_decode($term->name).'",';
          $availableTags_array[]=array(
            'category'  =>  esc_html__('City','wpresidence'),
            'labelcategory'  =>  'city',
            'label'     =>  wp_specialchars_decode($term->name),
            'value'     =>  wp_specialchars_decode($term->name),
          );
       }

       $terms = get_terms( 'property_area', $args );

       foreach ( $terms as $term ) {
          $availableTags.= '"'.wp_specialchars_decode($term->name).'",';
          $availableTags_array[]=array(
            'category'  =>   esc_html__('Area','wpresidence'),
            'labelcategory'  =>  'area',
            'label'     =>  wp_specialchars_decode($term->name),
            'value'     =>  wp_specialchars_decode($term->name),
          );
       }

       $terms = get_terms( 'property_county_state', $args );
       foreach ( $terms as $term ) {
          $availableTags.= '"'.wp_specialchars_decode($term->name).'",';
          $availableTags_array[]=array(
            'category'  =>   esc_html__('County or State','wpresidence'),
            'labelcategory'  =>  'countystate',
            'label'     =>  wp_specialchars_decode($term->name),
            'value'     =>  wp_specialchars_decode($term->name),
          );
       }

       wpestate_set_transient_cache('wpestate_search_location_tags',$availableTags,60*60*24);
   }
   return json_encode($availableTags_array);
  // return $availableTags;

}
endif;


/*
 *
 *
 *
 *
 *
 *
 */


if(!function_exists('wpestate_search_tab_align')):
function wpestate_search_tab_align(){

    $wp_estate_search_tab_align = wpresidence_get_option('wp_estate_search_tab_align','');
    if($wp_estate_search_tab_align=='center'){
        return 'wpestate_search_tab_align_center';
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
 */

if(!function_exists('wpestate_geo_search_filter_function')):
function wpestate_geo_search_filter_function($args,$center_lat,$center_long,$radius){
    global $wpdb;
    $radius_measure = wpresidence_get_option('wp_estate_geo_radius_measure','');
    $earth         = 3959;
    if( $radius_measure == 'km' ) {
       $earth = 6371;
    }


    $wpdb_query = $wpdb->prepare( "SELECT $wpdb->posts.ID,
            ( %s * acos(
                    cos( radians(%s) ) *
                    cos( radians( latitude.meta_value ) ) *
                    cos( radians( longitude.meta_value ) - radians(%s) ) +
                    sin( radians(%s) ) *
                    sin( radians( latitude.meta_value ) )
            ) )
            AS distance, latitude.meta_value AS latitude, longitude.meta_value AS longitude
            FROM $wpdb->posts
            INNER JOIN $wpdb->postmeta
                    AS latitude
                    ON $wpdb->posts.ID = latitude.post_id
            INNER JOIN $wpdb->postmeta
                    AS longitude
                    ON $wpdb->posts.ID = longitude.post_id
            WHERE 1=1

                    AND latitude.meta_key='property_latitude'
                    AND longitude.meta_key='property_longitude'
            HAVING distance < %s
            ORDER BY $wpdb->posts.menu_order ASC, distance ASC",
            $earth,
            $center_lat,
            $center_long,
            $center_lat,
            $radius
        );
        $listing_ids = $wpdb->get_results( $wpdb_query, OBJECT_K );

        if ( $listing_ids=='') {
            $listing_ids = array();
        }
        // return post ids for main wp_query

        $new_ids        =   array_keys(  $listing_ids );
        if(isset($args[ 'post__in' ])){
            $original_ids   =   $args[ 'post__in' ];
        }
        


        if ( !empty($new_ids) ){
            if( empty(  $args[ 'post__in' ] ) ){
                $args[ 'post__in' ] = $new_ids;
            }else if( $args[ 'post__in' ][0]==0 ){// no items on coustom
                $args[ 'post__in' ]=array(0);
            }else{
                $intersect   =   array_intersect ( $new_ids , $original_ids );
                if( empty($intersect) ){
                    $intersect=array(0);
                }
                $intersect = array_values($intersect);
                $args[ 'post__in' ] =$intersect;



            }
        }else{
            $args[ 'post__in' ]=array(0);
        }
        return $args;

}
endif;









/*
 *
 *
 *
 *
 *
 *
 */


if(!function_exists('wpestate_show_search_field_10')):
function wpestate_show_search_field_10($action_select_list,$label){
    $allowed_html               =   array();
    $appendix='half-';
    // Step 1: Read the canonical location value and keep old links working.
    $location_value='';
    if(isset($_GET['location'])){
        $location_value = $_GET['location'];
    }else if(isset($_GET['adv_location'])){
        $location_value = $_GET['adv_location'];
    }
    $return_string='  <div class="col-md-9">
            <input type="text" id="adv_location" class="form-control" name="location"  placeholder="'. esc_html__('Type address, state, city or area','wpresidence').'" value="';
                    if($location_value!==''){
                        $return_string.= esc_attr( wp_kses($location_value, $allowed_html) );
                    }
               $return_string.='">
        </div>';


        $availableTags  =  wpestate_return_data_for_location_autocomplete();

        $return_string.= '<script type="text/javascript">    //<![CDATA[
        jQuery(document).ready(function(){
            var availableTagsData = ' . $availableTags . ';
            wpresidenceInitializeAutocomplete(availableTagsData,"adv_location");
        });
        //]]>
        </script>';


        

        if(isset($_GET['property_action_category'][0]) && $_GET['property_action_category'][0]!='' && $_GET['property_action_category'][0]!='all'){
                $full_name          =   get_term_by('slug', ( ( $_GET['property_action_category'][0] ) ),'property_action_category');
                $adv_actions_value  =   $adv_actions_value1 = $full_name->name;
            }else{
                $adv_actions_value=$label;
                if($label==''){
                  $adv_actions_value  =   esc_html__('Types','wpresidence');
                }

                $adv_actions_value1 =   'all';
            }
            $return_string .= '  <div class="col-md-3">'.wpestate_show_dropdown_taxonomy_v21('types',$adv_actions_value , '').''.'</div>';
        





    $return_string.='<input type="hidden" name="is10" value="10">';
    return $return_string;
}
endif;





/*
 *
 *
 *
 *
 *
 *
 */

if(!function_exists('wpestate_show_search_field_11')):
function wpestate_show_search_field_11($action_select_list,$categ_select_list,$label=''){
    $allowed_html   =   array();
    $appendix       =   'half-';
    $return_string  =   ' <div class="col-md-6">
            <input type="text" id="keyword_search" class="form-control" name="keyword_search"  placeholder="'.esc_html__('Type Keyword','wpresidence').'" value="';

            if(isset($_GET['keyword_search'])){
                $return_string.= esc_attr( wp_kses($_GET['keyword_search'], $allowed_html) );
            }
            $return_string.='"></div>';


        if(isset($_GET['property_action_category'][0]) && $_GET['property_action_category'][0]!='' && $_GET['property_action_category'][0]!='all'){
                $full_name          =   get_term_by('slug', ( ( $_GET['property_action_category'][0] ) ),'property_action_category');
                $adv_actions_value  =   $adv_actions_value1 = $full_name->name;
            }else{

                $adv_actions_value=$label;
                if($label==''){
                  $adv_actions_value  =   esc_html__('Types','wpresidence');
                }

                $adv_actions_value1 =   'all';
            }

        
   
       



       if( isset($_GET['property_category'][0]) && $_GET['property_category'][0]!=''  && $_GET['property_category'][0]!='all' ){
                $full_name = get_term_by('slug', esc_html( wp_kses($_GET['property_category'][0], $allowed_html) ),'property_category');
                $adv_categ_value    =   $adv_categ_value1   =   $full_name->name;
            }else{

                $adv_categ_value=$label;
                if($label==''){
                  $adv_categ_value    =   esc_html__('Categories','wpresidence');
                }
                $adv_categ_value1   =   'all';
            }
            $return_string  .= '<div class="col-md-3">'
            .wpestate_show_dropdown_taxonomy_v21('categories',$adv_categ_value , '').
            '</div>';
        $return_string  .= '<div class="col-md-3">'
         .wpestate_show_dropdown_taxonomy_v21('types',$adv_actions_value , '').
        '</div>';


    $return_string.='<input type="hidden" name="is11" value="11">';
    return $return_string;
}
endif;




/*
 *
 *
 *
 *
 *
 *
 */

if(!function_exists('wpestated_advanced_search_tip11')):
function wpestated_advanced_search_tip11($args){

    $allowed_html       =   array();
    $taxcateg_include   =   array();
    $categ_array        =   array();
    $action_array       =   array();
    $type_name          =   'property_category';
    $type_name_value    =   wp_kses( $_REQUEST[$type_name][0] ,$allowed_html );
    $taxcateg_include   =   sanitize_title ( wp_kses( $type_name_value ,$allowed_html ) );


    if (isset($_GET['property_category']) && $_GET['property_category'][0]!='all' && trim($_GET['property_category'][0])!='' ){
        $taxcateg_include   =   array();

        foreach($_GET['property_category'] as $key=>$value){
            $taxcateg_include[]= sanitize_title (  esc_html( wp_kses($value, $allowed_html ) ) );
        }

        $categ_array=array(
            'taxonomy'     => 'property_category',
            'field'        => 'slug',
            'terms'        => $taxcateg_include
        );
    }

    if ( ( isset($_GET['property_action_category']) && $_GET['property_action_category'][0]!='all' && trim($_GET['property_action_category'][0])!='') ){
        $taxaction_include   =   array();

        foreach( $_GET['property_action_category'] as $key=>$value){
            $taxaction_include[]    = sanitize_title ( esc_html (  wp_kses($value, $allowed_html ) ) );
        }

        $action_array=array(
             'taxonomy'     => 'property_action_category',
             'field'        => 'slug',
             'terms'        => $taxaction_include
        );
    }
    $args['tax_query']      =   wpestate_clear_tax(   $args['tax_query'] );
    if( !empty($categ_array) ){
        $args['tax_query'][]    =   $categ_array;
    }
    if( !empty($action_array) ){
        $args['tax_query'][]    =   $action_array;
    }

    if (isset($_GET['keyword_search'])) {
        $keyword_search = trim(wp_strip_all_tags(wp_kses($_GET['keyword_search'], $allowed_html)));
        if ($keyword_search !== '') {
            $args['s'] = $keyword_search;
            if (isset($args['post__in'])) {
                unset($args['post__in']);
            }
        }
    }


    return ($args);

}
endif;





/*
 *
 *
 *
 *
 *
 *
 */

if(!function_exists('wpestated_advanced_search_tip11_ajax')):
function  wpestated_advanced_search_tip11_ajax($args,$keyword_search,$filter_search_action11,$filter_search_categ11) {

    $allowed_html       =   array();
    $taxcateg_include   =   array();
    $categ_array        =   array();
    $action_array       =   array();

    $filter_search_categ11  =   strtolower($filter_search_categ11);
    $filter_search_action11 =   strtolower($filter_search_action11);


    if (isset($filter_search_categ11) && $filter_search_categ11!='all' && trim($filter_search_categ11)!='' ){
        $taxcateg_include   =   array();
        $taxcateg_include[] =   sanitize_title (  esc_html( wp_kses($filter_search_categ11, $allowed_html ) ) );

        $categ_array=array(
            'taxonomy'     => 'property_category',
            'field'        => 'slug',
            'terms'        => $taxcateg_include
        );
    }

    if ( ( isset($filter_search_action11) && $filter_search_action11!='all' && trim($filter_search_action11)!='') ){
        $taxaction_include      =   array();
        $taxaction_include[]    =   sanitize_title ( esc_html (  wp_kses($filter_search_action11, $allowed_html ) ) );

        $action_array=array(
            'taxonomy'     => 'property_action_category',
            'field'        => 'slug',
            'terms'        => $taxaction_include
        );
    }

    $args['tax_query']      =   wpestate_clear_tax(   $args['tax_query'] );
    if( !empty($categ_array) ){
        $args['tax_query'][]    =   $categ_array;
    }
    if( !empty($action_array) ){
        $args['tax_query'][]    =   $action_array;
    }

    $keyword_search = trim(wp_strip_all_tags(wp_kses($keyword_search, $allowed_html)));

    if ($keyword_search !== '') {
        $args['s'] = $keyword_search;
        if (isset($args['post__in'])) {
            unset($args['post__in']);
        }
    }


    return ($args);

}
endif;



/*
 *
 *
 *
 *
 *
 *
 */



if(!function_exists('wpestated_advanced_search_tip10')):
function wpestated_advanced_search_tip10($args){
    $args['tax_query']  =   wpestate_clear_tax(   $args['tax_query'] );
    $allowed_html       =   array();
    $action_array       =   array();
    $location_array     =   array();





    if ( ( isset($_GET['property_action_category']) && $_GET['property_action_category'][0]!='all' && $_GET['property_action_category'][0]!='All' && trim($_GET['property_action_category'][0])!='') ){
        $taxaction_include   =   array();

        foreach( $_GET['property_action_category'] as $key=>$value){
            $taxaction_include[]    = sanitize_title ( esc_html (  wp_kses($value, $allowed_html ) ) );
        }

        $action_array=array(
            'taxonomy'     => 'property_action_category',
            'field'        => 'slug',
            'terms'        => $taxaction_include
        );
    }




    $location_value = '';
    if ( isset($_GET['location']) && $_GET['location']!='') {
        $location_value = $_GET['location'];
    } else if ( isset($_GET['adv_location']) && $_GET['adv_location']!='') {
        $location_value = $_GET['adv_location'];
    }

    if ( $location_value!='') {
        $value  =   stripslashes ( sanitize_text_field(  $location_value ) );
        $location_array = array(
                        'key'     => 'hidden_address',
                        'value'   =>  $value,
                        'compare' => 'LIKE',
                        'type'    => 'string',
                );

    }




    if( !empty($action_array) ){
        if(gettype(  $args['tax_query']) =='string' ){
            $args['tax_query']=array();
        }
        $args['tax_query'][]=$action_array;
    }

    if(!empty($location_array)){

        if(gettype(  $args['meta_query']) =='string' ){
            $args['meta_query']=array();
        }
        $args['meta_query'][]=$location_array;
    }



    return ($args);

}
endif;






/*
 *
 *
 *
 *
 *
 *
 */

if(!function_exists('wpestated_advanced_search_tip10_ajax')):
function wpestated_advanced_search_tip10_ajax($args,$filter_search_action10,$adv_location10){
    $args['tax_query']      = (array)  wpestate_clear_tax(   $args['tax_query'] );
    $allowed_html       =   array();
    $action_array       =   array();
    $location_array     =   array();

    $filter_search_action10 = strtolower($filter_search_action10);

    if (  isset($filter_search_action10) && $filter_search_action10!='all' && $filter_search_action10!='' ){
        $taxaction_include   =   array();


        $taxaction_include[]    = sanitize_title ( esc_html (  wp_kses($filter_search_action10, $allowed_html ) ) );

        $action_array=array(
            'taxonomy'     => 'property_action_category',
            'field'        => 'slug',
            'terms'        => $taxaction_include
        );
    }




    if ( isset($adv_location10) && $adv_location10!='') {
        $value  =   stripslashes ( sanitize_text_field( $adv_location10 ) );
        $location_array = array(
                        'key'     => 'hidden_address',
                        'value'   =>  $value,
                        'compare' => 'LIKE',
                        'type'    => 'string',
                );

    }




    if( !empty($action_array) ){
        if(!is_array( $args['tax_query'] )){
            $args['tax_query']=array();
        }

        $args['tax_query'][]=$action_array;
    }

    if(!empty($location_array)){
        if(!is_array( $args['meta_query'] )){
            $args['meta_query']=array();
        }

        $args['meta_query'][]=$location_array;
    }



    return ($args);

}
endif;





/*
 *
 *
 *
 *
 *
 *
 */


if(!function_exists('wpestate_clear_tax')):
function wpestate_clear_tax($tax_array){

    if( isset($tax_array[0]) ):
        if( !is_array($tax_array[0] ) ){
            unset( $tax_array[0] );
        }else{
            if(empty($tax_array[0])){
                unset( $tax_array[0] );
            }
        }
    endif;

    if(is_array($tax_array)):
        foreach($tax_array as $key=>$tax_ar){
            if( $key != 'relation' ){
                if( !is_array($tax_ar) ){
                    unset( $tax_array[$key] );
                }else{
                    if(empty($tax_ar)){
                        unset( $tax_array[$key] );
                    }
                }
            }
        }
    endif;

    return $tax_array;

}
endif;




/*
 *
 *
 *
 *
 *
 *
 */

if (!function_exists('wpestate_search_results_custom')):
function wpestate_search_results_custom($tip=''){
    global $wpestate_included_ids;
    global $amm_features;
    $real_custom_fields     =   wpresidence_get_option( 'wp_estate_custom_fields', '');
    $adv_search_what        =   wpresidence_get_option('wp_estate_adv_search_what','');
    $adv_search_how         =   wpresidence_get_option('wp_estate_adv_search_how','');
    $adv_search_label       =   wpresidence_get_option('wp_estate_adv_search_label','');
    $adv_search_type        =   wpresidence_get_option('wp_estate_adv_search_type','');
    $adv_search_fields_no   =   floatval( wpresidence_get_option('wp_estate_adv_search_fields_no') );

 
    if(isset($_GET['elementor_form_id']) && intval($_GET['elementor_form_id'])!=0 ){
        $elementor_post_id= intval($_GET['elementor_form_id']);
        $elementor_search_name_how = "elementor_search_how_" . $elementor_post_id;
        $elementor_search_name_what = "elementor_search_what_" . $elementor_post_id;
        $elementor_search_name_label = "elementor_search_label_" . $elementor_post_id;
            
        $adv_search_what=get_option($elementor_search_name_what,true);
        $adv_search_how=get_option($elementor_search_name_how,true);
        $adv_search_label=get_option($elementor_search_name_label,true);
        
     
        
        if(isset($_GET['term_id']) && intval($_GET['term_id'])!=0 && isset($adv_search_what['use_tabs'])){
            $term_id_elementor = intval($_GET['term_id']);
            $adv_search_what    =   $adv_search_what[$term_id_elementor];
            $adv_search_how     =   $adv_search_how[$term_id_elementor];
            $adv_search_label   =   $adv_search_label[$term_id_elementor];
            
         
        
        }
           
    }else{
        $term_counter=0;
        if( isset( $_REQUEST['term_counter']) ) {
            $term_counter=intval($_REQUEST['term_counter']);
        }


        $adv_search_what    = array_slice($adv_search_what, ($term_counter*$adv_search_fields_no),$adv_search_fields_no);
        $adv_search_label   = array_slice($adv_search_label, ($term_counter*$adv_search_fields_no),$adv_search_fields_no);
        $adv_search_how     = array_slice($adv_search_how, ($term_counter*$adv_search_fields_no),$adv_search_fields_no);
    
    }
    
  


     
       
        
        
    $wpestate_keyword                =   '';
    $area_array             =    array();
    $city_array             =   array();
    $action_array           =   array();
    $categ_array            =   array();
    $meta_query             =   '';
    $wpestate_included_ids  =   array();
    $id_array               =   '';
    $countystate_array      =    array();
    $allowed_html           =   array();
    $new_key                =   0;
    $features               =   array();
    $status_array           =    array();

    if($adv_search_type==6 || $adv_search_type==7 || $adv_search_type==8 || $adv_search_type==9){
        $adv6_taxonomy          =   wpresidence_get_option('wp_estate_adv6_taxonomy');
        if($adv6_taxonomy=='property_category'){
            $adv_search_what[]='categories';

        }else if($adv6_taxonomy=='property_action_category'){
            $adv_search_what[]='types';
        }else if($adv6_taxonomy=='property_city'){
            $adv_search_what[]='cities';
        }else if($adv6_taxonomy=='property_area'){
            $adv_search_what[]='areas';
        }else if($adv6_taxonomy=='property_county_state'){
            $adv_search_what[]='county / state';
        }
        $adv_search_how[]='like';
        $adv_search_label[]='';
    }


   $term_location_value='';



    foreach($adv_search_what as $key=>$term ){
        $new_key        =   intval($key)+1;
        $new_key        =   'val'.$new_key;


        if($term === 'none' || $term === 'keyword' || $term === 'property id' || is_array($term) ){
            // do nothng


        }else if( $term === 'wpestate location' ){
                if( $tip === 'ajax' ){
                    $term_location_value=  wp_kses($_POST['val_holder'][$key],$allowed_html);
                }
        }else if( $term === 'property status' ) {          
                $status_array = wpestate_process_taxonomy_search($tip,$term,$key);
        }else if( $term === 'categories' ) {
                $categ_array = wpestate_process_taxonomy_search($tip,$term,$key);
        }else if($term === 'types'){
                $action_array = wpestate_process_taxonomy_search($tip,$term,$key);
        }else if($term === 'cities'){
                $city_array = wpestate_process_taxonomy_search($tip,$term,$key);

        }else if($term === 'areas'){  
                $area_array = wpestate_process_taxonomy_search($tip,$term,$key);
        }else if($term === 'county / state'){
                $countystate_array = wpestate_process_taxonomy_search($tip,$term,$key);
        }else{

            $term         =   str_replace(' ', '_', $term);
            $slug         =   wpestate_limit45(sanitize_title( $term ));
            $slug         =   sanitize_key($slug);
            $string       =   wpestate_limit45 ( sanitize_title ($adv_search_label[$key]) );
            $slug_name    =   sanitize_key($string);
            $mapped_meta_key = '';
            if ( function_exists( 'wpestate_map_adv_search_field_to_meta_key' ) ) {
                $mapped_meta_key = wpestate_map_adv_search_field_to_meta_key( $adv_search_what[$key] );
            }

            $compare_array      =   array();
            $show_slider_price  =   wpresidence_get_option('wp_estate_show_slider_price','');


            if ( $adv_search_what[$key] === 'property country'){

                if( $tip === 'ajax' ){
                    $term_value=  wp_kses($_POST['val_holder'][$key],$allowed_html);
                }else{
                    if(isset($_GET['advanced_country'])){
                        $term_value=  esc_html( wp_kses( $_GET['advanced_country'], $allowed_html) );
                    }
                }


                
                if( $term_value!='' && $term_value!='all' && $term_value!='all' &&  $term_value != $adv_search_label[$key]){
                    $compare_array['key']        = 'property_country';
                    $compare_array['value']      =  wp_kses($term_value,$allowed_html);
                    $compare_array['type']       = 'CHAR';
                    $compare_array['compare']    = 'LIKE';
                    //$meta_query[]                = $compare_array;
                    $wpestate_included_ids[] = $compare_array;
                }


                //&& ( isset($_REQUEST['slider_min']) && isset($_REQUEST['slider_max']) )

            }else if ( ($adv_search_what[$key] === 'property price' && $show_slider_price ==='yes') || 
                        $adv_search_what[$key] === 'property-price-v2' ||
                        $adv_search_what[$key] === 'property-price-v3'  ){



                $compare_array['key']        = 'property_price';

                if( $tip === 'ajax' ){
                    $price_low=0;
                    if(isset($_POST['slider_min'])){
                        $price_low  = floatval($_POST['slider_min']);
                    }
                    $price_max  = 0;
                    if(isset($_POST['slider_max'])){
                        $price_max  = floatval($_POST['slider_max']);
                    }
                }else{
                    if( isset($_GET['term_id']) && isset($_GET['term_id'])!='' ){
                        $term_id    = intval($_GET['term_id']);
                        $price_low  = 0;
                        if (isset($_GET['price_low_'.$term_id])) {
                            $price_low = floatval($_GET['price_low_'.$term_id]);
                        }
                        $price_max  = 0;
                        if (isset($_GET['price_max_'.$term_id])) {
                            $price_max = floatval($_GET['price_max_'.$term_id]);
                        }

                    }else{
                        $price_low = 0;
                        if (isset($_GET['price_low'])) {
                            $price_low = floatval($_GET['price_low']);
                        }
                        $price_max = 0;
                        if (isset($_GET['price_max'])) {
                            $price_max = floatval($_GET['price_max']);
                        }
                    }

                }

                $custom_fields = wpresidence_get_option( 'wp_estate_multi_curr', '');
                if( !empty($custom_fields) && isset($_COOKIE['my_custom_curr']) &&  isset($_COOKIE['my_custom_curr_pos']) &&  isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos']!=-1){
                    $i=intval($_COOKIE['my_custom_curr_pos']);
                    $price_max       =   $price_max / $custom_fields[$i][2];
                    $price_low       =   $price_low / $custom_fields[$i][2];
                }


                if($price_max>0){
                    $compare_array['key']        = 'property_price';
                    $compare_array['value']      = array($price_low, $price_max);
                    $compare_array['type']       = 'numeric';
                    $compare_array['compare']    = 'BETWEEN';
                    $wpestate_included_ids[]= $compare_array;
                    //$meta_query[]                = $compare_array;
                }

      

            // end price    
            }else if ( $adv_search_what[$key] === 'beds-baths'){
                $componentsbeds=0;
                if(isset( $_REQUEST['componentsbeds'] )){
                    $componentsbeds=floatval( $_REQUEST['componentsbeds'] );
                }
                $componentsbaths=0;
                if(isset( $_REQUEST['componentsbaths'] )){
                    $componentsbaths=floatval( $_REQUEST['componentsbaths'] );
                }

                if($componentsbeds>0){
                    $compare_array=array();
                    $compare_array['key']        = 'property_bedrooms';
                    $compare_array['value']      =  $componentsbeds;
                    $compare_array['type']       = 'numeric';
                    $compare_array['compare']    = '>=';
                    $wpestate_included_ids[]= $compare_array;
                }

                if($componentsbaths>0){
                    $compare_array=array();
                    $compare_array['key']        = 'property_bathrooms';
                    $compare_array['value']      =  $componentsbaths;
                    $compare_array['type']       = 'numeric';
                    $compare_array['compare']    = '>=';
                    $wpestate_included_ids[]= $compare_array;
                }

       

            }else{
                if( $tip === 'ajax' ){
                    $term_value= wp_kses($_POST['val_holder'][$key],$allowed_html);
                }else{
                    $term_value='';
                    // Step 1: Read the dedicated non-slider price keys first.
                    if (
                        $adv_search_what[$key] === 'property price' &&
                        $show_slider_price !== 'yes'
                    ) {
                        if (
                            isset( $adv_search_how[$key] ) &&
                            trim( $adv_search_how[$key] ) === 'greater' &&
                            isset( $_GET['price_min'] )
                        ) {
                            $term_value = esc_html( wp_kses( $_GET['price_min'], $allowed_html ) );
                        } else if (
                            isset( $adv_search_how[$key] ) &&
                            trim( $adv_search_how[$key] ) === 'smaller' &&
                            isset( $_GET['price_max'] )
                        ) {
                            $term_value = esc_html( wp_kses( $_GET['price_max'], $allowed_html ) );
                        }
                    // Step 2: Read the normalized meta key first.
                    } else if ( $mapped_meta_key !== '' && isset( $_GET[ $mapped_meta_key ] ) ) {
                        $term_value = esc_html( wp_kses( $_GET[ $mapped_meta_key ], $allowed_html ) );
                    // Step 3: Keep older URLs working when they still use the label slug.
                    } else if(isset($_GET[$slug_name])){
                        $term_value =  (esc_html( wp_kses($_GET[$slug_name], $allowed_html) ));
                    // Step 4: Keep older URLs working when they still use the field slug.
                    } else if ( isset( $_GET[ $slug ] ) ) {
                        $term_value = esc_html( wp_kses( $_GET[ $slug ], $allowed_html ) );
                    // Step 5: Keep raw custom-field names working for non-Latin field URLs.
                    } else if ( isset( $_GET[ $adv_search_what[$key] ] ) ) {
                        $term_value = esc_html( wp_kses( $_GET[ $adv_search_what[$key] ], $allowed_html ) );
                    }
                }

   
                
                if( $adv_search_label[$key] != $term_value && $term_value != '' && strtolower($term_value) != 'all'){ // if diffrent than the default values
                    $compare        =   '';
                    $search_type    =   '';
                    $allowed_html   =   array();
                    $compare        =   $adv_search_how[$key];


                

                    if($compare === 'equal'){
                       $compare         =   '=';
                       $search_type     =   'numeric';
                       $term_value      =   floatval ($term_value );

                    }else if($compare === 'greater'){
                        $compare        = '>=';
                        $search_type    = 'numeric';
                        $term_value     =  floatval ( $term_value );

                    }else if($compare === 'smaller'){
                        $compare        ='<=';
                        $search_type    ='numeric';
                        $term_value     = floatval ( $term_value );

                    }else if($compare === 'like'){
                        $compare        = 'LIKE';
                        $search_type    = 'CHAR';
                        $term_value     = (wp_kses( $term_value ,$allowed_html));
                     //   $term_value     = str_replace(' ','%',$term_value);

                    }else if($compare === 'date bigger'){
                        $compare        ='>=';
                        $search_type    ='DATE';
                        $term_value     =  str_replace(' ', '-', $term_value);
                        $term_value     = wp_kses( $term_value,$allowed_html );

                    }else if($compare === 'date smaller'){
                        $compare        = '<=';
                        $search_type    = 'DATE';
                        $term_value     =  str_replace(' ', '-', $term_value);
                        $term_value     = wp_kses( $term_value,$allowed_html );
                    }

                    $compare_array['key']        = $slug;
                    $compare_array['value']      = $term_value;
                    $compare_array['type']       = $search_type;
                    $compare_array['compare']    = $compare;
                    $wpestate_included_ids[]= $compare_array;
                    //$meta_query[]                = $compare_array;
          
                }// end if diffrent
            }
        }////////////////// end last else
    } ///////////////////////////////////////////// end for each adv search term




    if($tip === 'search'){
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    }

    if($tip === 'ajax'){
        $paged      =   intval($_POST['newpage']);
        $prop_no    =   intval( wpresidence_get_option('wp_estate_prop_no', '') );
    }

    if($tip === 'ajax'){
        $features_array = wpestate_add_feature_to_search('ajax');
    }else{
        $features_array = wpestate_add_feature_to_search();
    }


   



    $taxonomy_array=array(  'relation' => 'AND');
    if(!empty($categ_array)){
        $taxonomy_array[]=$categ_array;
    }
    if(!empty($action_array)){
        $taxonomy_array[]=$action_array;
    }
    if(!empty($city_array)){
        $taxonomy_array[]=$city_array;
    }
    if(!empty($area_array)){
        $taxonomy_array[]=$area_array;
    }
    if(!empty($countystate_array)){
        $taxonomy_array[]=$countystate_array;
    }
    if(!empty($features_array)){
        $taxonomy_array[]=$features_array;
    }
    if(!empty($status_array)){
        $taxonomy_array[]=$status_array;
    }
/*
    array(
        'relation' => 'AND',
        $categ_array,
        $action_array,
        $city_array,
        $area_array,
        $countystate_array,
        $features_array,
        $status_array
    )
*/
    $args = array(
        'cache_results'             =>  false,
        'update_post_meta_cache'    =>  false,
        'update_post_term_cache'    =>  false,

        'post_type'       => 'estate_property',
        'post_status'     => 'publish',
        'paged'           => $paged,
        'posts_per_page'  => 30,
        'meta_key'        => 'prop_featured',
        'orderby'         => 'meta_value_num',
        'order'           => 'DESC',
        'meta_query'      => $meta_query,
        'tax_query'       => $taxonomy_array
    );


    
    $meta_ids=array();
    if(!empty($wpestate_included_ids)){
        $meta_ids = wpestate_add_meta_post_to_search($wpestate_included_ids);
       
        if(count(array_values($meta_ids))<600){
            $args['post__in']=(array_values($meta_ids));
        }else{
             $args['meta_query']=$wpestate_included_ids;
        }
       
    }


    if(!empty($features)){

    }

    if($adv_search_type==10 && $tip === 'ajax'){
        $args    =   wpestated_advanced_search_tip10_ajax($args,$_POST['filter_search_action10'],$_POST['adv_location10']);
    }


    if($adv_search_type==11 && $tip === 'ajax'){
        $args    =   wpestated_advanced_search_tip11_ajax($args,$_POST['keyword_search'],$_POST['filter_search_action11'],$_POST['filter_search_categ11']);
    }



    if( in_array('wpestate location', $adv_search_what)){
       $args = wpestate_filter_for_location($args);
    }

    //$term_location_value
    if( in_array('wpestate location', $adv_search_what) && $tip === 'ajax' ){
       $args = wpestate_filter_for_location_ajax($args,$term_location_value);
    }

   
    //wpresidence_get_option('wp_estate_use_geo_location','')=='yes' || 
    
    if(  isset($_POST['geo_lat']) && isset($_POST['geo_long']) && $_POST['geo_lat']!='' && $_POST['geo_long']!='' ) {

        $geo_lat    =   sanitize_text_field( $_POST['geo_lat']);
        $geo_long   =   sanitize_text_field( $_POST['geo_long']);
        $geo_rad    =   '';
        if( isset($_POST['geo_rad'])  ){
            $geo_rad = sanitize_text_field($_POST['geo_rad']);
        }else{
            $geo_rad    =   wpresidence_get_option('wp_estate_initial_radius','');
            
        }
 
        $args = wpestate_geo_search_filter_function($args, $geo_lat, $geo_long, $geo_rad);
    }


    
    return $args;

}
endif;







/*
 *
 *
 *
 *
 *
 *
 */




if(!function_exists('wpestate_search_results_default')):
function wpestate_search_results_default($tip=''){

    $area_array         =   '';
    $city_array         =   '';
    $action_array       =   '';
    $categ_array        =   '';
    $id_array           =   '';
    $countystate_array  =   '';
    $allowed_html       =   array();

    if($tip === 'ajax'){
        $type_name      =   isset( $_REQUEST['property_category'] ) ? 'property_category' : 'category_values';
        $type_name_value=   isset( $_REQUEST[$type_name] ) ? wp_kses( $_REQUEST[$type_name] ,$allowed_html ) : '';
        $action_name    =   isset( $_REQUEST['property_action_category'] ) ? 'property_action_category' : 'action_values';
        $action_name_value  = isset( $_REQUEST[$action_name] ) ? wp_kses( $_REQUEST[$action_name] ,$allowed_html ) : '';
        $city_name      =   isset( $_REQUEST['property_city'] ) ? 'property_city' : 'city';
        $area_name      =   isset( $_REQUEST['property_area'] ) ? 'property_area' : 'area';
        $rooms_name     =   'advanced_rooms';
        $bath_name      =   'advanced_bath';
        $price_low_name =   'price_low';
        $price_max_name =   'price_max';
    }else{
        $type_name          =   'property_category';

        $type_name_value    =   '';
        if(isset($_REQUEST[$type_name][0])){
            $type_name_value    =   wp_kses( $_REQUEST[$type_name][0] ,$allowed_html );
        }
        $action_name        =   'property_action_category';
        $action_name_value  =   '';
        if(isset( $_REQUEST[$action_name][0])){
            $action_name_value  =    wp_kses( $_REQUEST[$action_name][0],$allowed_html );
        }

        $city_name      =   'property_city';
        $area_name      =   'property_area';
        $rooms_name     =   'advanced_rooms';
        $bath_name      =   'advanced_bath';
        $price_low_name =   'price_low';
        $price_max_name =   'price_max';
    }

    if ( $type_name_value!='all' && $type_name_value!='' ){
        $taxcateg_include   =   array();
        $taxcateg_include   =   sanitize_title ( wp_kses( $type_name_value ,$allowed_html ) );

        $categ_array=array(
            'taxonomy'     => 'property_category',
            'field'        => 'slug',
            'terms'        => $taxcateg_include
        );
    }

    if ( $action_name_value !='all' && $action_name_value !='') {
        $taxaction_include   =   array();
        $taxaction_include   =   sanitize_title ( wp_kses( $action_name_value ,$allowed_html) );

        $action_array=array(
             'taxonomy'     => 'property_action_category',
             'field'        => 'slug',
             'terms'        => $taxaction_include
        );
     }



    if (isset($_REQUEST[$city_name]) and $_REQUEST[$city_name] != 'all' && $_REQUEST[$city_name] != '') {
        $taxcity[] = sanitize_title ( wp_kses ( $_REQUEST[$city_name],$allowed_html ) );
        $city_array = array(
            'taxonomy'     => 'property_city',
            'field'        => 'slug',
            'terms'        => $taxcity
         );
     }


    if (isset($_REQUEST[$area_name]) and $_REQUEST[$area_name] != 'all' && $_REQUEST[$area_name] != '') {
        $taxarea[] = sanitize_title ( wp_kses ($_REQUEST[$area_name],$allowed_html) );
        $area_array = array(
            'taxonomy'     => 'property_area',
            'field'        => 'slug',
            'terms'        => $taxarea
        );
     }



    $meta_query = $rooms = $baths = $price = array();
    if (isset($_REQUEST[$rooms_name]) && is_numeric($_REQUEST[$rooms_name])) {
        $rooms['key'] = 'property_bedrooms';
        $rooms['compare'] = '=';
        $rooms['value'] = floatval ($_REQUEST[$rooms_name]);
        $meta_query[] = $rooms;
    }

    if (isset($_REQUEST[$bath_name]) && is_numeric($_REQUEST[$bath_name])) {
        $baths['key'] = 'property_bathrooms';
        $baths['compare'] = '=';
        $baths['value'] = floatval ($_REQUEST[$bath_name]);
        $meta_query[] = $baths;
    }


    //////////////////////////////////////////////////////////////////////////////////////
    ///// price filters
    //////////////////////////////////////////////////////////////////////////////////////
    $price_low ='';
    if( isset($_REQUEST[$price_low_name])){
        $price_low = floatval($_REQUEST[$price_low_name]);
    } else if ( isset($_REQUEST['price_min']) ) {
        $price_low = floatval($_REQUEST['price_min']);
    }

    $price_max='';
    $custom_fields = wpresidence_get_option( 'wp_estate_multi_curr', '');


    if( isset($_REQUEST[$price_max_name])  && $_REQUEST[$price_max_name] && floatval($_REQUEST[$price_max_name])>0 ){
            $price_max          = floatval($_REQUEST[$price_max_name]);

            if( !empty($custom_fields) && isset($_COOKIE['my_custom_curr']) &&  isset($_COOKIE['my_custom_curr_pos']) &&  isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos']!=-1){
                $i=intval($_COOKIE['my_custom_curr_pos']);
                $price_max       =   $price_max / $custom_fields[$i][2];
                $price_low       =   $price_low / $custom_fields[$i][2];
            }


            $price['key']       = 'property_price';
            $price['value']     = array($price_low, $price_max);
            $price['type']      = 'numeric';
            $price['compare']   = 'BETWEEN';
            $meta_query[]       = $price;
    }else {

            if( !empty($custom_fields) && isset($_COOKIE['my_custom_curr']) &&  isset($_COOKIE['my_custom_curr_pos']) &&  isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos']!=-1){
                $i=intval($_COOKIE['my_custom_curr_pos']);
                $price_low       =   $price_low / $custom_fields[$i][2];
            }


            $price['key']       = 'property_price';
            $price['value']     =  $price_low;
            $price['type']      = 'numeric';
            $price['compare']   = '>=';
            $meta_query[]       = $price;
    }




    if($tip === 'ajax'){

        $features             = array();
        $features = wpestate_add_feature_to_search_ajax();
        if(!empty($features)){
            $args['post__in']=$features;
        }
    }


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





    if($tip === 'search'){
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    }

    if($tip === 'ajax'){
        $paged      =   intval($_POST['newpage']);
    }

    $prop_no    =   intval( wpresidence_get_option('wp_estate_prop_no', '') );

    if($tip === 'ajax'){
        $features_array = wpestate_add_feature_to_search('ajax');
    }else{
        $features_array = wpestate_add_feature_to_search();
    }

    $args = array(
        'cache_results'             =>  false,
        'update_post_meta_cache'    =>  false,
        'update_post_term_cache'    =>  false,

        'post_type'       => 'estate_property',
        'post_status'     => 'publish',
        'paged'           => $paged,
        'posts_per_page'  => $prop_no,
        'meta_key'        => $meta_order,
        'orderby'         => $order_by,
        'order'           => $meta_directions,
        'meta_query'      => $meta_query,
        'tax_query'       => array(
                                'relation' => 'AND',
                                    $categ_array,
                                    $action_array,
                                    $city_array,
                                    $area_array,
                                    $features_array,

                            )
    );
    if(!empty($features)){
        $args['post__in']=$features;
    }


    if( isset($_POST['geo_lat']) && wpresidence_get_option('wp_estate_use_geo_location','')=='yes' && $_POST['geo_lat']!='' && $_POST['geo_long']!='' ){
            $geo_lat    = $_POST['geo_lat'];
            $geo_long   = $_POST['geo_long'];
            $geo_rad    = $_POST['geo_rad'];
            $args       = wpestate_geo_search_filter_function( $args, $geo_lat, $geo_long, $geo_rad);
    }


    return $args;

}
endif;




/*
 *
 *
 *
 *
 *
 *
 */


if(!function_exists('wpestate_add_feature_to_search_ajax')):
function wpestate_add_feature_to_search_ajax(){
    global $table_prefix;
    global $wpdb;
    $searched=0;

    $feature_list_array =   array();
    $allowed_html       =   array();



    $all_checkers=explode(",", wp_kses($_POST['all_checkers'],$allowed_html) );

     $potential_ids=array();

    foreach($all_checkers as $checker => $value){
        if($value!=''){
            $searched       =   1;
        }
        $post_var_name  =   str_replace(' ','_', trim($value) );
        $input_name     =   wpestate_limit45(sanitize_title( $post_var_name ));
        $input_name     =   sanitize_key($input_name);
        if(trim($input_name)!=''){


            $potential_ids[$checker]=
                wpestate_get_ids_by_query(
                    $wpdb->prepare("
                        SELECT post_id
                        FROM ".$table_prefix."postmeta
                        WHERE meta_key = %s
                        AND CAST(meta_value AS UNSIGNED) = %f
                    ",$input_name,'1' )
            );

        }

    }

    $ids=[];
    foreach($potential_ids as $key=>$temp_ids){
        if(count($ids)==0){
            $ids=$temp_ids;
        }else{
            $ids=array_intersect($ids,$temp_ids);
        }
    }


    if(empty($ids) && $searched==1 ){
        $ids[]=0;
    }
    return $ids;


}
endif;





/*
 *
 *
 *
 *
 *
 *
 */


/**
 * Processes property features for advanced search queries
 *
 * This function collects selected property features from either AJAX requests
 * or direct form submissions and formats them into a WordPress tax_query array
 * that can be used in WP_Query. It handles sanitization and proper taxonomy structure.
 *
 * @param string $tip Optional. Pass 'ajax' to process AJAX-based feature selections.
 *                    Default is empty string for direct form submissions.
 * @return array|null A properly formatted tax_query array for property features, 
 *                    or null if no features were selected.
 * @since 4.0.0
 */
if(!function_exists('wpestate_add_feature_to_search')):
    function wpestate_add_feature_to_search($tip=''){
        $features       =   array();
        $allowed_html   =   array();
        // Retrieve property feature terms from cache for better performance
        $terms          =   wpestate_get_cached_terms( 'property_features' );
        
        if($tip=='ajax'){
            // Process AJAX request - features come as comma-separated string
            $all_checkers=explode(",", wp_kses($_POST['all_checkers'],$allowed_html) );
            foreach($all_checkers as $item_name){
                if($item_name!=''){
                   $features[]=$item_name;
                }
            }
        }else{
            // Process direct form submission - features come as individual form fields
            foreach($terms as $key => $term){
                $input_name     =   $term->slug;
                $input_name     =     str_replace('%','', $input_name); // Sanitize the slug
                if ( isset( $_REQUEST[$input_name] ) && $_REQUEST[$input_name]==1 ){
                   $features[]=$term->name;
                }
            }
        }
        
        // If features were selected, create the tax_query structure
        if( !empty($features)){
            $features_array=array();
            $features_array['relation']='AND'; // Require ALL selected features to be present
            foreach ($features as $term):
                $features_array[]=array(
                    'taxonomy' => 'property_features',
                    'field'    => 'name',      // Query by term name instead of ID or slug
                    'terms'    => $term,       // The feature name to search for
                );
            endforeach;
            return $features_array;
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
 */

if(!function_exists('get_ids_by_query')):
function wpestate_get_ids_by_query($query){
    global $wpdb;

    $data=$wpdb->get_results($query,'ARRAY_A');
    $results=[];
    foreach($data as $entry){
        $results[]=$entry['post_id'];
    }
    return $results;
}
endif;




/*
 *
 *
 *
 *
 *
 *
 */

if(!function_exists('wpestated_advanced_search_tip2')):
function wpestated_advanced_search_tip2(){
    $categ_array        =   '';
    $action_array       =   '';
    $area_array         =  '';
    $city_array         =  '';
    $countystate_array  =  '';
    $location_array     =  '';
    $meta_query         =   array();
    $allowed_html       =   array();

    if (isset($_GET['property_category']) && $_GET['property_category'][0]!='all' && trim($_GET['property_category'][0])!='' ){
        $taxcateg_include   =   array();

        foreach($_GET['property_category'] as $key=>$value){
            $taxcateg_include[]= sanitize_title (  esc_html( wp_kses($value, $allowed_html ) ) );
        }

        $categ_array=array(
            'taxonomy'     => 'property_category',
            'field'        => 'slug',
            'terms'        => $taxcateg_include
        );
    }

    if ( ( isset($_GET['property_action_category']) && $_GET['property_action_category'][0]!='all' && trim($_GET['property_action_category'][0])!='') ){
        $taxaction_include   =   array();

        foreach( $_GET['property_action_category'] as $key=>$value){
            $taxaction_include[]    = sanitize_title ( esc_html (  wp_kses($value, $allowed_html ) ) );
        }

        $action_array=array(
             'taxonomy'     => 'property_action_category',
             'field'        => 'slug',
             'terms'        => $taxaction_include
        );
    }

    $location_value = '';
    if ( isset($_GET['location']) && $_GET['location']!='') {
        $location_value = $_GET['location'];
    } else if ( isset($_GET['adv_location']) && $_GET['adv_location']!='') {
        $location_value = $_GET['adv_location'];
    }

    if ( $location_value!='') {
        $taxlocation[] = sanitize_title (  esc_html( wp_kses($location_value, $allowed_html) ) );
        $area_array = array(
            'taxonomy'     => 'property_area',
            'field'        => 'slug',
            'terms'        => $taxlocation
        );

        $city_array = array(
            'taxonomy'     => 'property_city',
            'field'        => 'slug',
            'terms'        => $taxlocation
        );

        $countystate_array = array(
            'taxonomy'      => 'property_county_state',
            'field'         => 'slug',
            'terms'         => $taxlocation
        );
        $location_array     = array(
                                    'relation' => 'OR',
                                    $city_array,
                                    $area_array,
                                    $countystate_array
                                );
        }

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;



    $args = array(
        'cache_results'             =>  false,
        'update_post_meta_cache'    =>  false,
        'update_post_term_cache'    =>  false,

        'post_type'       => 'estate_property',
        'post_status'     => 'publish',
        'paged'           => $paged,
        'posts_per_page'  => 30,
        'meta_key'        => 'prop_featured',
        'orderby'         => 'meta_value',
        'order'           => 'DESC',
        'meta_query'      => $meta_query,
        'tax_query'       => array(
                                    'relation' => 'AND',
                                    $categ_array,
                                    $action_array,
                                    $location_array
                               )
    );

    return ($args);

}
endif;




/*
 *
 *
 *
 *
 *
 *
 */


if(!function_exists('wpestated_advanced_search_tip2_ajax')):
function wpestated_advanced_search_tip2_ajax(){
    $categ_array        =   '';
    $action_array       =   '';
    $area_array         =  '';
    $city_array         =  '';
    $countystate_array  =  '';
    $location_array     =  '';
    $meta_query         =   array();
    $allowed_html       =   array();

    if ( isset($_POST['category_values']) && $_POST['category_values']!='' && $_POST['category_values']!='all' ){
        $taxcateg_include   =   array();
        $taxcateg_include[]= sanitize_title (  esc_html( wp_kses($_POST['category_values'], $allowed_html ) ) );

        $categ_array=array(
            'taxonomy'     => 'property_category',
            'field'        => 'slug',
            'terms'        => $taxcateg_include
        );
    }

    if ( isset($_POST['action_values'] ) && $_POST['action_values']!='' && $_POST['action_values']!='all' ) {
        $taxaction_include   =   array();
        $taxaction_include[]    = sanitize_title ( esc_html (  wp_kses($_POST['action_values'] , $allowed_html ) ) );

        $action_array=array(
             'taxonomy'     => 'property_action_category',
             'field'        => 'slug',
             'terms'        => $taxaction_include
        );
    }



    if ( isset($_POST['location']) && $_POST['location']!='' ) {
        $taxlocation[] = sanitize_title (  esc_html( wp_kses($_POST['location'], $allowed_html) ) );
        $area_array = array(
            'taxonomy'     => 'property_area',
            'field'        => 'slug',
            'terms'        => $taxlocation
        );

        $city_array = array(
            'taxonomy'     => 'property_city',
            'field'        => 'slug',
            'terms'        => $taxlocation
        );

        $countystate_array = array(
            'taxonomy'      => 'property_county_state',
            'field'         => 'slug',
            'terms'         => $taxlocation
        );
        $location_array     = array(
                                    'relation' => 'OR',
                                    $city_array,
                                    $area_array,
                                    $countystate_array
                                );
        }

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;



    $args = array(
        'cache_results'             =>  false,
        'update_post_meta_cache'    =>  false,
        'update_post_term_cache'    =>  false,

        'post_type'       => 'estate_property',
        'post_status'     => 'publish',
        'paged'           => $paged,
        'posts_per_page'  => -1,
        'meta_key'        => 'prop_featured',
        'orderby'         => 'meta_value',
        'order'           => 'DESC',
        'meta_query'      => $meta_query,
        'tax_query'       => array(
                                    'relation' => 'AND',
                                    $categ_array,
                                    $action_array,
                                    $location_array
                               )
    );

    return ($args);

}
endif;







/*
 *
 *
 *
 *
 *
 *
 */


if(!function_exists('wpestated_advanced_search_tip2_ajax_tabs')):
function wpestated_advanced_search_tip2_ajax_tabs(){
    $categ_array        =   '';
    $action_array       =   '';
    $area_array         =  '';
    $city_array         =  '';
    $countystate_array  =  '';
    $location_array     =  '';
    $meta_query         =   array();
    $allowed_html       =   array();

    if ( isset($_POST['category_values']) && $_POST['category_values']!='' && $_POST['category_values']!='all' ){
        $taxcateg_include   =   array();
        $taxcateg_include[]= sanitize_title (  esc_html( wp_kses($_POST['category_values'], $allowed_html ) ) );

        $categ_array=array(
            'taxonomy'     => 'property_category',
            'field'        => 'slug',
            'terms'        => $taxcateg_include
        );
    }

    if ( isset($_POST['action_values'] ) && $_POST['action_values']!='' && $_POST['action_values']!='all' ) {
        $taxaction_include   =   array();
        $taxaction_include[]    = sanitize_title ( esc_html (  wp_kses($_POST['action_values'] , $allowed_html ) ) );

        $action_array=array(
             'taxonomy'     => 'property_action_category',
             'field'        => 'slug',
             'terms'        => $taxaction_include
        );
    }



    if ( isset($_POST['location']) && $_POST['location']!='' ) {
        $taxlocation[] = sanitize_title (  esc_html( wp_kses($_POST['location'], $allowed_html) ) );
        $area_array = array(
            'taxonomy'     => 'property_area',
            'field'        => 'slug',
            'terms'        => $taxlocation
        );

        $city_array = array(
            'taxonomy'     => 'property_city',
            'field'        => 'slug',
            'terms'        => $taxlocation
        );

        $countystate_array = array(
            'taxonomy'      => 'property_county_state',
            'field'         => 'slug',
            'terms'         => $taxlocation
        );
        $location_array     = array(
                                    'relation' => 'OR',
                                    $city_array,
                                    $area_array,
                                    $countystate_array
                                );
        }

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;



    $adv6_taxonomy          =   wpresidence_get_option('wp_estate_adv6_taxonomy');
    if ( isset($_POST['picked_tax'] ) && $_POST['picked_tax']!='' && $_POST['picked_tax']!='all' ) {
        $taxaction_picked_tax  =   array();
        $taxaction_picked_tax[]    = sanitize_title ( esc_html (  wp_kses($_POST['picked_tax'] , $allowed_html ) ) );

        $taxaction_picked_tax=array(
             'taxonomy'     => $adv6_taxonomy,
             'field'        => 'slug',
             'terms'        => $taxaction_picked_tax
        );
    }


    $features_array = wpestate_add_feature_to_search('ajax');

    $args = array(
        'cache_results'             =>  false,
        'update_post_meta_cache'    =>  false,
        'update_post_term_cache'    =>  false,

        'post_type'       => 'estate_property',
        'post_status'     => 'publish',
        'paged'           => $paged,
        'posts_per_page'  => -1,
        'meta_key'        => 'prop_featured',
        'orderby'         => 'meta_value',
        'order'           => 'DESC',
        'tax_query'       => array(
                                    'relation' => 'AND',
                                    $categ_array,
                                    $action_array,
                                    $taxaction_picked_tax,
                                    $location_array,
                                    $features_array
                               )
    );


    $features             = array();
    $features = wpestate_add_feature_to_search_ajax();
    if(!empty($features)){
        $args['post__in']=$features;
    }

    if(!empty($features)){
        $args['post__in']=$features;
    }
    return ($args);

}
endif;



/*
 *
 *
 *
 *
 *
 *
 */
function wpestate_get_term_names_from_slugs($terms, $taxonomy) {
    $names = array();
    if (is_array($terms)) {
        foreach ($terms as $term_slug) {
            if ( $term_slug === '' || $term_slug === null ) {
                continue;
            }

            $term = false;
            if ( is_numeric( $term_slug ) ) {
                $term = get_term_by( 'id', intval( $term_slug ), $taxonomy );
            }

            if ( ! $term ) {
                $term = get_term_by('slug', $term_slug, $taxonomy);
            }

            if ($term) {
                $names[] = $term->name;
            }
        }
    } else {
        $term = false;
        if ( is_numeric( $terms ) ) {
            $term = get_term_by( 'id', intval( $terms ), $taxonomy );
        }
        if ( ! $term ) {
            $term = get_term_by('slug', $terms, $taxonomy);
        }
        if ($term) {
            $names[] = $term->name;
        }
    }
    return implode(', ', $names);
}
/*
 *
 *
 *
 *
 *
 *
 */
if(!function_exists('wpestate_show_search_params_new')):
function wpestate_show_search_params_new($wpestate_included_ids,$args,$custom_advanced_search, $adv_search_what,$adv_search_how,$adv_search_label){
    ob_start();
    global $amm_features;

    // Step 1: Track what we already printed so labels do not repeat.
    $printed_taxonomies = array();
    $location_label_printed = false;

    // Step 2: Print taxonomy filters and resolve the location OR group from nested term ids.
    if (isset($args['tax_query'])) {
        foreach ($args['tax_query'] as $query) {
            if (
                ! $location_label_printed &&
                isset($query['relation']) &&
                $query['relation'] === 'OR'
            ) {
                // Step 2.1: Resolve every nested OR clause to its human term name.
                $location_names = array();

                foreach ( $query as $location_clause ) {
                    if ( ! is_array( $location_clause ) || ! isset( $location_clause['taxonomy'], $location_clause['terms'] ) ) {
                        continue;
                    }

                    $resolved_names = wpestate_get_term_names_from_slugs( $location_clause['terms'], $location_clause['taxonomy'] );
                    if ( $resolved_names !== '' ) {
                        $location_names[] = $resolved_names;
                    }
                }

                // Step 2.2: Only print the location label when we resolved at least one real term name.
                if ( ! empty( $location_names ) ) {
                    $location_names = array_unique( $location_names );
                    print '<strong>' . esc_html__('County, City or Area is ', 'wpresidence') . ':</strong> ' . esc_html( implode( ', ', $location_names ) ) . ', ';
                    $location_label_printed = true;
                }
            }
    
            $taxonomyNames = array(
                'property_category'         => esc_html__('Category','wpresidence'),
                'property_action_category'  => esc_html__('For','wpresidence'),
                'property_city'             => esc_html__('City','wpresidence'),
                'property_area'             => esc_html__('Area','wpresidence'),
                'property_county_state'     => esc_html__ ('County / State','wpresidence'),
                'property_status'           => esc_html__('Status','wpresidence'),
                'property_features'         => esc_html__('Features and Amenities','wpresidence')
            );
    
            foreach ($taxonomyNames as $taxonomy => $label) {
                if (isset($query['taxonomy'], $query['terms']) && $query['taxonomy'] == $taxonomy) {
                    $termNames = wpestate_get_term_names_from_slugs($query['terms'], $taxonomy);
                    if(!empty($termNames)){
                        print '<strong>' . esc_html__($label, 'wpresidence') . ':</strong> ' . esc_html($termNames) . ', ';
                        $printed_taxonomies[ $taxonomy ] = true;
                    }
                }
            }
        }
    }

    // Step 2: for tab-based searches, print the active tab taxonomy even when it was not added to tax_query.
    $adv6_taxonomy = wpresidence_get_option('wp_estate_adv6_taxonomy');
    if (
        is_string( $adv6_taxonomy ) &&
        $adv6_taxonomy !== '' &&
        empty( $printed_taxonomies[ $adv6_taxonomy ] )
    ) {
        $request_term_id = isset( $_REQUEST['term_id'] ) ? intval( $_REQUEST['term_id'] ) : 0;
        $request_term    = false;

        if ( $request_term_id > 0 ) {
            $request_term = get_term( $request_term_id, $adv6_taxonomy );
        } elseif ( isset( $_REQUEST['adv6_search_tab'] ) && trim( (string) $_REQUEST['adv6_search_tab'] ) !== '' ) {
            $request_term = get_term_by( 'slug', sanitize_text_field( wp_unslash( $_REQUEST['adv6_search_tab'] ) ), $adv6_taxonomy );
        }

        if ( $request_term && ! is_wp_error( $request_term ) ) {
            $tab_labels = array(
                'property_category'     => esc_html__('Category','wpresidence'),
                'property_action_category' => esc_html__('For','wpresidence'),
                'property_city'         => esc_html__('City','wpresidence'),
                'property_area'         => esc_html__('Area','wpresidence'),
                'property_county_state' => esc_html__('County / State','wpresidence'),
            );
            $tab_label = isset( $tab_labels[ $adv6_taxonomy ] ) ? $tab_labels[ $adv6_taxonomy ] : esc_html__('Category','wpresidence');
            print '<strong>' . esc_html( $tab_label ) . ':</strong> ' . esc_html( $request_term->name ) . ', ';
        }
    }




    if(isset($args['meta_query']) && is_array($args['meta_query'])){
        wpestate_show_search_params_for_meta($args['meta_query'],$custom_advanced_search, $adv_search_what,$adv_search_how,$adv_search_label);
    }

    if(is_array($wpestate_included_ids)){
        wpestate_show_search_params_for_meta($wpestate_included_ids,$custom_advanced_search, $adv_search_what,$adv_search_how,$adv_search_label);
    }
    $return_string = ob_get_contents();
    ob_end_clean();

    $return_string = rtrim($return_string,','); 
    $return_string = rtrim($return_string,' ,');
    return $return_string;

}
endif;





/*
 *
 *
 *
 *
 *
 *
 */

if( !function_exists('wpestate_show_currency_save_search') ):
function wpestate_show_currency_save_search(){
    $custom_fields  = wpresidence_get_option( 'wp_estate_multi_curr', '');

    if( !empty($custom_fields) && isset($_COOKIE['my_custom_curr']) &&  isset($_COOKIE['my_custom_curr_pos']) &&  isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos']!=-1){
        $i=intval($_COOKIE['my_custom_curr_pos']);
        $wpestate_currency   =   $custom_fields[$i][0];
    }else{
        $wpestate_currency   =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
    }

    return $wpestate_currency;
}
endif;







/*
 *
 *
 *
 *
 *
 *
 */

if(!function_exists('wpestate_show_search_params_for_meta')):
function wpestate_show_search_params_for_meta($wpestate_included_ids,$custom_advanced_search, $adv_search_what,$adv_search_how,$adv_search_label){
    $admin_submission_array=array(
        'types'             =>esc_html__('types','wpresidence'),
        'categories'        =>esc_html__('categories','wpresidence'),
        'cities'            =>esc_html__('cities','wpresidence'),
        'areas'             =>esc_html__('areas','wpresidence'),
        'property price'    =>esc_html__('property price','wpresidence'),
        'property size'     =>esc_html__('property size','wpresidence'),
        'property lot size' =>esc_html__('property lot size','wpresidence'),
        'property rooms'    =>esc_html__('property rooms','wpresidence'),
        'property bedrooms' =>esc_html__('property bedrooms','wpresidence'),
        'property bathrooms'=>esc_html__('property bathrooms','wpresidence'),
        'property address'  =>esc_html__('property address','wpresidence'),
        'property county'   =>esc_html__('property county','wpresidence'),
        'property state'    =>esc_html__('property state','wpresidence'),
        'property zip'      =>esc_html__('property zip','wpresidence'),
        'property country'  =>esc_html__('property country','wpresidence'),
        'property status'   =>esc_html__('property status','wpresidence')
    );

    // Step 1: Collect price bounds first so we can print one clear price range.
    $price_min_value = '';
    $price_max_value = '';
    if ( is_array( $wpestate_included_ids ) ) {
        foreach ( $wpestate_included_ids as $search_parameter ) {
            if ( ! is_array( $search_parameter ) || ! isset( $search_parameter['key'], $search_parameter['compare'], $search_parameter['value'] ) ) {
                continue;
            }

            if ( $search_parameter['key'] !== 'property_price' ) {
                continue;
            }

            $price_compare_type = html_entity_decode( trim( $search_parameter['compare'] ) );
            $price_compare_type = preg_replace( '/[^[:print:]]/', '', $price_compare_type );

            if ( $price_compare_type === '>=' ) {
                $price_min_value = $search_parameter['value'];
            } else if ( $price_compare_type === '<=' ) {
                $price_max_value = $search_parameter['value'];
            } else if ( $price_compare_type === 'BETWEEN' && is_array( $search_parameter['value'] ) ) {
                if ( isset( $search_parameter['value'][0] ) ) {
                    $price_min_value = $search_parameter['value'][0];
                }
                if ( isset( $search_parameter['value'][1] ) ) {
                    $price_max_value = $search_parameter['value'][1];
                }
            }
        }
    }

    // Step 2: Print one combined property price range when both sides exist.
    if ( $price_min_value !== '' && $price_max_value !== '' ) {
        $where_currency = esc_html( wpresidence_get_option( 'wp_estate_where_currency_symbol', '' ) );
        $show_currency  = ' ' . wpestate_show_currency_save_search();
        $factor         = 1;

        if (
            isset( $_COOKIE['my_custom_curr_coef'] ) &&
            $_COOKIE['my_custom_curr_coef'] != 0 &&
            isset( $_COOKIE['my_custom_curr'] ) &&
            isset( $_COOKIE['my_custom_curr_pos'] ) &&
            isset( $_COOKIE['my_custom_curr_symbol'] ) &&
            $_COOKIE['my_custom_curr_pos'] != -1
        ) {
            $factor = $factor * $_COOKIE['my_custom_curr_coef'];
        }

        $price_min_value = floatval( $price_min_value ) * $factor;
        $price_max_value = floatval( $price_max_value ) * $factor;

        print '<strong>' . esc_html__( 'property price', 'wpresidence' ) . '  </strong> ';
        if ( $where_currency == 'before' ) {
            print ' ' . esc_html__( 'between ', 'wpresidence' ) . $show_currency . ' ' . $price_min_value . ' ' . esc_html__( 'and', 'wpresidence' ) . $show_currency . ' ' . $price_max_value . ', ';
        } else {
            print ' ' . esc_html__( 'between', 'wpresidence' ) . ' ' . $price_min_value . $show_currency . ' ' . esc_html__( 'and', 'wpresidence' ) . ' ' . $price_max_value . $show_currency . ', ';
        }
    }

    // Step 3: Render the remaining filters and skip the already-merged price clauses.
    if(is_array($wpestate_included_ids)){
        foreach($wpestate_included_ids as $search_parameter){
            if( !is_array($search_parameter) || !isset($search_parameter['key']) ){
                continue;
            }

            if (
                isset( $search_parameter['key'], $search_parameter['compare'] ) &&
                $search_parameter['key'] === 'property_price' &&
                ( $search_parameter['compare'] === '>=' || $search_parameter['compare'] === '<=' ) &&
                $price_min_value !== '' &&
                $price_max_value !== ''
            ) {
                continue;
            }
            $label=str_replace('_',' ',$search_parameter['key']);

            if(array_key_exists ($label, $admin_submission_array)){
                 $label=$admin_submission_array[$label];
            }else{
                if($custom_advanced_search==='yes'){
                    $label = wpestate_get_custom_field_name($search_parameter['key'],$adv_search_what,$adv_search_label);
                }
            }

            if($label=='hidden_address'){
                $label=esc_html__('address','wpresidence');
            }
            print '<strong>'.$label.'  </strong> ';

            $where_currency =   esc_html ( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
            $show_price_value = isset( $search_parameter['value'] ) ? $search_parameter['value'] : '';
            if (
                isset( $search_parameter['key'] ) &&
                $search_parameter['key'] === 'property_price' &&
                ! is_array( $show_price_value ) &&
                $show_price_value !== ''
            ) {
                $show_price_value = wpestate_show_price_floor( $show_price_value, wpestate_show_currency_save_search(), $where_currency, 1 );
            }

            if ( isset($search_parameter['compare']) ){

                $compare_type = html_entity_decode(trim($search_parameter['compare']));
                $compare_type = preg_replace('/[^[:print:]]/', '', $compare_type);
         
            

                        if ($compare_type=='BETWEEN'){
                            if($search_parameter['key']=='property_price'){
                                $show_currency= ' '.wpestate_show_currency_save_search();
                                $factor=1;
                                if( isset($_COOKIE['my_custom_curr_coef'])&& $_COOKIE['my_custom_curr_coef']!=0 && isset($_COOKIE['my_custom_curr']) &&  isset($_COOKIE['my_custom_curr_pos']) &&  isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos']!=-1){
                                    $factor=$factor*$_COOKIE['my_custom_curr_coef'];
                                }
                                if ($where_currency == 'before') {
                                    print ' '.esc_html__('between ','wpresidence').' '.$show_currency.' '.$search_parameter['value'][0]*$factor.' '.esc_html__('and','wpresidence').' '.$show_currency.' '.$search_parameter['value'][1]*$factor;
                                } else {
                                    print ' '.esc_html__('between','wpresidence').' '.$search_parameter['value'][0]*$factor.' '.$show_currency.' '.esc_html__('and','wpresidence').' '.$search_parameter['value'][1]*$factor.$show_currency;
                                }

                            }else{
                                print ' '.esc_html__('between','wpresidence').' '.$search_parameter['value'][0].' '.esc_html__('and','wpresidence').' '.$search_parameter['value'][1];
                            }
                            print', ';
                        }else if ($compare_type=='LIKE'){
                            print  esc_html__(' similar with ','wpresidence').' <strong>'.str_replace('_',' ',$search_parameter['value']).'</strong>, ';
                        }else if ($compare_type=='CHAR'){
                            print esc_html__(' has','wpresidence').' <strong>'.str_replace('_',' ',$custm_name).'</strong>, ';
                        }else if ($search_parameter['compare']=='='){
                            print  esc_html__(' equal with ','wpresidence').' '.$show_price_value.', ';
                        }else if ( $compare_type == '<=' ){
                            print esc_html__('smaller than ','wpresidence').' '.$show_price_value.', ';
                        }else if ( $compare_type == '>=' ){
                            print  esc_html__('bigger than ','wpresidence').' '.$show_price_value.', ';
                        }



            }
        }
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
 */


if(!function_exists('wpestate_show_search_params')):
function wpestate_show_search_params($args,$custom_advanced_search, $adv_search_what,$adv_search_how,$adv_search_label){


    if( isset($args['tax_query'] )){

        foreach($args['tax_query'] as $key=>$query ){



            if( isset($query['relation'] ) && $query['relation']==='OR' ){
                $value=$query[0]['terms'][0];
                $value=  ucwords(str_replace('-', ' ', $value));
                print '<strong>'.esc_html__('County, City or Area is ','wpresidence').':</strong> '.rawurldecode($value);
            }

          // had  $query['terms'][0]
            if ( isset($query['taxonomy']) && isset( $query['terms']) && $query['taxonomy'] == 'property_category'){

                if( is_array( $query['terms'] ) ){
                    $term = $query['terms'][0];
                }else{
                    $term=$query['terms'];
                }

                $page = get_term_by( 'slug',$term ,'property_category');
                if(isset($page->name)){
                    print '<strong>'.esc_html__('Category','wpresidence').':</strong> '. $page->name .', ';
                }
            }

            if ( isset($query['taxonomy']) && isset( $query['terms'] ) && $query['taxonomy']=='property_action_category' ){

                if( is_array( $query['terms'] ) ){
                   $term = $query['terms'][0];
                }else{
                    $term=$query['terms'];
                }


                $page = get_term_by( 'slug',$term,'property_action_category');

                if(isset($page->name)){
                    print '<strong>'.esc_html__('For','wpresidence').':</strong> '.$page->name.', ';
                }
            }

            if ( isset($query['taxonomy']) && isset($query['terms']) && $query['taxonomy']=='property_city'){
                $page = get_term_by( 'slug',$query['terms'][0] ,'property_city');
                if(isset($page->name)){
                    print '<strong>'.esc_html__('City','wpresidence').':</strong> '.$page->name.', ';
                }
            }

            if ( isset($query['taxonomy']) && isset($query['terms']) && $query['taxonomy']=='property_area'){
                $page = get_term_by( 'slug',$query['terms'][0] ,'property_area');
                if(isset($page->name)){
                    print '<strong>'.esc_html__('Area','wpresidence').':</strong> '.$page->name.', ';
                }
            }
            if ( isset($query['taxonomy']) && isset($query['terms']) && $query['taxonomy']=='property_county_state'){
                $page = get_term_by( 'slug',$query['terms'][0] ,'property_county_state');
                if(isset($page->name)){
                    print '<strong>'.esc_html__('County / State','wpresidence').':</strong> '.$page->name.', ';
                }
            }

        }
    }


    $wpestate_currency               =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
    $where_currency         =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );

    if( isset($args['meta_query'] ) && $args['meta_query']!='' ){
        foreach($args['meta_query'] as $key=>$query ){
            $admin_submission_array=array(
                        'types'             =>esc_html__('types','wpresidence'),
                        'categories'        =>esc_html__('categories','wpresidence'),
                        'cities'            =>esc_html__('cities','wpresidence'),
                        'areas'             =>esc_html__('areas','wpresidence'),
                        'property price'    =>esc_html__('property price','wpresidence'),
                        'property size'     =>esc_html__('property size','wpresidence'),
                        'property lot size' =>esc_html__('property lot size','wpresidence'),
                        'property rooms'    =>esc_html__('property rooms','wpresidence'),
                        'property bedrooms' =>esc_html__('property bedrooms','wpresidence'),
                        'property bathrooms'=>esc_html__('property bathrooms','wpresidence'),
                        'property address'  =>esc_html__('property address','wpresidence'),
                        'property county'   =>esc_html__('property county','wpresidence'),
                        'property state'    =>esc_html__('property state','wpresidence'),
                        'property zip'      =>esc_html__('property zip','wpresidence'),
                        'property country'  =>esc_html__('property country','wpresidence'),
                        'property status'   =>esc_html__('property status','wpresidence')
            );
            $label=str_replace('_',' ',$query['key']);

            if(array_key_exists ($label, $admin_submission_array)){
               $label=$admin_submission_array[$label];
            }

            if($custom_advanced_search==='yes'){
                $custm_name = wpestate_get_custom_field_name($query['key'],$adv_search_what,$adv_search_label);

                if ( isset($query['compare']) ){
                    if ($query['compare']=='BETWEEN'){

                            if($query['key']=='property_price'){
                                if($query['value'][0]==0){
                                    $min_val=0;
                                }else{
                                     $min_val=wpestate_show_price_floor($query['value'][0],$wpestate_currency,$where_currency,1);
                                }
                                print '<strong>'.esc_html__('price range from: ','wpresidence').'</strong> '. $min_val.' '.esc_html__('to','wpresidence').' '.wpestate_show_price_floor($query['value'][1],$wpestate_currency,$where_currency,1);
                            }else{
                                print '<strong>'.$custm_name.'</strong> '.esc_html__('bigger than','wpresidence').' '.$query['value'].', ';
                            }

                    }else if ($query['compare']=='LIKE'){
                        print esc_html($label). esc_html__(' similar with ','wpresidence').' <strong>'.str_replace('_',' ',$query['value']).'</strong>, ';


                    }else if ($query['compare']=='CHAR'){
                        print esc_html__(' has','wpresidence').' <strong>'.str_replace('_',' ',$custm_name).'</strong>, ';


                    }else if ($query['compare']=='='){
                        print '<strong>'.$custm_name.'</strong> '. esc_html__(' equal with ','wpresidence').' <strong>'.$query['value'].'</strong>, ';

                    }else if ( $query['compare'] == '<=' ){
                        if($query['key']=='property_price'){
                            if(isset($query['value'])){
                                print wpestate_show_price_floor($query['value'],$wpestate_currency,$where_currency,1);
                            }
                        }else{
                            print '<strong>'.$custm_name.'</strong> '.esc_html__('smaller than ','wpresidence').' '.$query['value'].', ';
                        }

                    }else{
                        if(isset($query['value'])){
                            if($query['key']=='property_price'){
                                print '<strong>'.esc_html__('price range from ','wpresidence').'</strong> '. wpestate_show_price_floor($query['value'],$wpestate_currency,$where_currency,1).' '.esc_html__('to','wpresidence').' ';
                            }else{
                                print '<strong>'.$custm_name.'</strong> '.esc_html__('bigger than','wpresidence').' '.$query['value'].', ';
                            }
                        }
                    }
                }else{
                    print '<strong>'.$custm_name.':</strong> '.$query['value'].', ';
                } //end elese query compare


            }else{
                if ( isset( $query['compare'] ) ){
                    $custm_name = wpestate_get_custom_field_name($query['key'],$adv_search_what,$adv_search_label);

                    if ( $query['compare'] == 'CHAR' ){
                        print esc_html__(' has','wpresidence').' <strong>'.str_replace('_',' ',$custm_name).'</strong>, ';
                    }else if ( $query['compare'] == '<=' ){
                        if($query['key']=='property_price'){

                            print wpestate_show_price_floor($query['value'],$wpestate_currency,$where_currency,1);

                        }else{
                            print '<strong>'.$custm_name.'</strong> '.esc_html__('smaller than ','wpresidence').' '. wpestate_show_price_floor($query['value'],$wpestate_currency,$where_currency,1) .', ';
                        }
                    } else{
                         if($query['key']=='property_price'){
                            if($query['value'][0]==0){
                                $min_val=0;
                            }else{
                                 $min_val=wpestate_show_price_floor($query['value'][0],$wpestate_currency,$where_currency,1);
                            }
                            print '<strong>'.esc_html__('price range from: ','wpresidence').'</strong> '. $min_val.' '.esc_html__('to','wpresidence').' '.wpestate_show_price_floor($query['value'][1],$wpestate_currency,$where_currency,1);
                        }else{
                            print '<strong>'.$custm_name.'</strong> '.esc_html__('bigger than','wpresidence').' '.$query['value'].', ';
                        }
                    }

                }else{
                    print '<strong>'.$label.':</strong> '.$query['value'].', ';
                } //end elese query compare

            }//end else if custom adv search

        }
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
 */

if( !function_exists('wpestate_search_with_keyword')):
function wpestate_search_with_keyword($adv_search_what,$adv_search_label ){
    $wpestate_keyword        =   '';
    $return_custom  =   array();
    $id_array       =   '';
    $allowed_html   =   array();
    

    
    foreach($adv_search_what as $key=>$term ){
        if( $term === 'keyword' ){

            $term         =     str_replace(' ', '_', $term);
            $slug         =     wpestate_limit45(sanitize_title( $term ));
            $slug         =     sanitize_key($slug);
            $string       =     wpestate_limit45 ( sanitize_title ($adv_search_label[$key]) );
            $slug_name    =     sanitize_key($string);
            $return_custom['keyword']      =    esc_attr(  wp_kses ( $_GET[$slug_name], $allowed_html) );

        }else if($term === 'property id' || $term === 'id'){

            $term         =     str_replace(' ', '_', $term);
            $slug         =     wpestate_limit45(sanitize_title( $term ));
            $slug         =     sanitize_key($slug);
            $string       =     wpestate_limit45 ( sanitize_title ($adv_search_label[$key]) );
            $slug_name    =     sanitize_key($string);

            if(isset($_GET[$slug_name])){
                $id_array     =     intval ($_GET[$slug_name]);
            }
            $return_custom['id_array'] = $id_array;

        }


    }
    return $return_custom;
}
endif;




/*
 *
 *
 *
 *
 *
 *
 */

if( !function_exists('wpestate_search_with_keyword_ajax')):
function wpestate_search_with_keyword_ajax($adv_search_what,$adv_search_label ){
    $wpestate_keyword        =   '';
    $return_custom  =   '';
    $id_array       =   '';
    $allowed_html   =   array();
    foreach($adv_search_what as $key=>$term ){
        if( $term === 'keyword' ){
            $return_custom  =   array();
            $term         =     str_replace(' ', '_', $term);
            $slug         =     wpestate_limit45(sanitize_title( $term ));
            $slug         =     sanitize_key($slug);
            $string       =     wpestate_limit45 ( sanitize_title ($adv_search_label[$key]) );
            $slug_name    =     sanitize_key($string);
            $return_custom['keyword']      =    esc_attr(    wp_kses($_POST['val_holder'][$key],$allowed_html) );

        }else if($term === 'property id' || $term === 'id' ){
            $return_custom  =   array();
            $term         =     str_replace(' ', '_', $term);
            $slug         =     wpestate_limit45(sanitize_title( $term ));
            $slug         =     sanitize_key($slug);
            $string       =     wpestate_limit45 ( sanitize_title ($adv_search_label[$key]) );
            $slug_name    =     sanitize_key($string);


                $id_array     =     intval ($_POST['val_holder'][$key]);

            $return_custom['id_array'] = $id_array;

        }


    }
    return $return_custom;
}
endif;




/*
 *
 *
 *
 *
 *
 *
 */

if( !function_exists('wpestate_search_with_keyword_ajax2')):
function wpestate_search_with_keyword_ajax2($adv_search_what,$adv_search_label ){
    $wpestate_keyword='';
    $allowed_html   =   array();
    $new_key        =   0;
    foreach($adv_search_what as $key=>$term ){
        if( $term === 'keyword' ){
            $new_key    =   $key+1;
            $new_key    =   'val'.$new_key;
            $wpestate_keyword= wp_kses( $_POST['val_holder'][$key],$allowed_html );
       }
    }
    return $wpestate_keyword;
}
endif;
