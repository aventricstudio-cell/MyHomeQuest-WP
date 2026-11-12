<?php

///////////////////////////////////////////////////////////////////////////////////////////
/////// Js & Css include on front site
///////////////////////////////////////////////////////////////////////////////////////////

require_once  get_theme_file_path('libs/css_js_include_helper.php');

if( !function_exists('wpestate_scripts') ):
/**
 * Enqueue and localize scripts and styles on the front end.
 *
 * Determines which assets to load based on the current page template and
 * theme options. Also passes dynamic data to scripts for AJAX operations.
 *
 * @return void
 */
function wpestate_scripts() {

   


    global $post;


   


    $custom_image               =   '';
    $use_idx_plugins            =   0;
    $header_type                =   '';
    $idx_status                 =   esc_html ( wpresidence_get_option('wp_estate_idx_enable','') );
    $adv_search_type_status     =   intval   ( wpresidence_get_option('wp_estate_adv_search_type',''));
    $home_small_map_status      =   esc_html ( wpresidence_get_option('wp_estate_home_small_map','') );

    $page_template='';
    if(isset($post->ID)){
        $page_template = get_post_meta( $post->ID, '_wp_page_template', true );
        $page_template = ($page_template);
    }
  

    $theme_object=wp_get_theme();

    $post_id ='';
    if( isset($post->ID) ){
        $post_id =$post->ID;
     }

    if($idx_status=='yes'){
        $use_idx_plugins=1;
    }

    if( isset($post->ID) ) {
        $header_type                =   get_post_meta ( $post->ID, 'header_type', true);
    }

    $global_header_type         =   wpresidence_get_option('wp_estate_header_type','');
    $listing_map                =   'internal';


    if( $header_type==0 ){
        if($global_header_type==4){
            $listing_map            =   'top';
        }
    }else if ( $header_type==5 ){
        $listing_map            =   'top';
    }

    if( is_tax() &&  intval(wpresidence_get_option('wp_estate_property_list_type','') )==2 ){
        $global_header_type=4;
    }

    $slugs=array();
    $hows=array();
    $show_price_slider          =   'no';
    $slider_price_position      =   0;

    $custom_advanced_search='yes';
    if ( $custom_advanced_search == 'yes'){



            $adv_search_what        =   wpresidence_get_option('wp_estate_adv_search_what','');
            $adv_search_label       =   wpresidence_get_option('wp_estate_adv_search_label','');
            $adv_search_how         =   wpresidence_get_option('wp_estate_adv_search_how','');
            $show_price_slider      =   wpresidence_get_option('wp_estate_show_slider_price','');


            $slider_price_position  =   0;
            $counter                =   0;
            if(is_array($adv_search_what)){

                foreach($adv_search_what as $key=>$search_field){
                    $counter++;
                    if($search_field=='types'){
                        $slugs[]='adv_actions';
                    }
                    else if($search_field=='categories'){
                        $slugs[]='adv_categ';
                    }
                    else if($search_field=='cities'){
                        $slugs[]='property_city';
                    }
                    else if($search_field=='areas'){
                        $slugs[]='property_area';
                    }
                    else if($search_field=='county / state'){
                        $slugs[]='county-state';
                    }
                    else if($search_field=='property country'){
                        $slugs[]='property-country';
                    }else if (  $search_field=='property price' && $show_price_slider=='yes' ){
                        $slugs[]='property_price';
                        $slider_price_position=$counter ;

                    } else if($search_field=='property status'){
                        $slugs[]='property_status';

                    } else if($search_field=='wpestate location'){
                        $slugs[]='location';

                    }
                    else {

                        $string       =    wpestate_limit45( sanitize_title ($adv_search_label[$key]) );
                        $slug         =   sanitize_key($string);
                        $slugs[]=$slug;
                     }
                }
            }
            if(is_array($adv_search_how)){
                foreach($adv_search_how as $key=>$search_field){
                    $hows[]= $adv_search_how[$key];

                }
            }

    }

    $use_mimify     =   wpresidence_get_option('wp_estate_use_mimify','');
    $mimify_prefix  =   '';
    if($use_mimify==='yes'){
        $mimify_prefix  =   '.min';
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    // load the css files
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

   // wp_enqueue_style('bootstrap.min',get_theme_file_uri('/css/bootstrap.min.css'), array(), $theme_object->Version, 'all');
   // wp_enqueue_style('bootstrap-theme.min',get_theme_file_uri('/css/bootstrap-theme.min.css'), array(), $theme_object->Version, 'all');

    if($mimify_prefix===''){
        wp_enqueue_style('wpestate_style',get_stylesheet_uri(), array(), $theme_object->Version, 'all');
    }else{
        wp_enqueue_style('wpestate_style',get_theme_file_uri('/style.min.css'), array(), $theme_object->Version, 'all');
    }



    wp_enqueue_style('theme-main-css', get_template_directory_uri() . '/public/css/main.css');
    $is_rtl=0;
    if( is_rtl() ){
        $is_rtl=1;
        wp_enqueue_style('theme-main-css-rtl-styles', 
            get_template_directory_uri() . '/public/css/rtl.css',
            array('theme-main-css'),  // Make RTL dependent on main styles
            wp_get_theme()->get('Version')
        );
    }

    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap-js', get_template_directory_uri() . '/public/js/bootstrap.bundle.min.js', array('jquery'), null, true);
    wp_enqueue_script('bootstrap-selectjs', get_template_directory_uri() . '/public/js/bootstrap-select1.min.js',array('jquery','bootstrap-js'), $theme_object->Version, true);
    wp_enqueue_script('control', get_theme_file_uri('/js/control.js'), array('jquery', 'slick.min', 'bootstrap-js'), $theme_object->Version, true);





    //wp_enqueue_script('bootstrap-selectjs', get_template_directory_uri() . '/public/js/bootstrap-select.min.js', array('jquery', 'bootstrap-js'), $theme_object->Version, true);
    // Enqueue your custom JS file (if any)
  //  wp_enqueue_script('theme-custom-js', get_template_directory_uri() . '/public/js/app.js', array('jquery'), null, true);

    wp_enqueue_style('bootstrap-selectcss.min', get_template_directory_uri().'/public/css/bootstrap-select.min.css', array(), $theme_object->Version, 'all');
   
  
    $json_string    =   '';
    $protocol       =   is_ssl() ? 'https' : 'http';
    $general_font   =   esc_html( get_option('wp_estate_general_font', '') );

    $headings_font_subset   =   esc_html ( get_option('wp_estate_headings_font_subset','') );
    if($headings_font_subset!=''){
        $headings_font_subset='&amp;subset='.$headings_font_subset;
    }

    // embed custom fonts from admin
    if($general_font && $general_font!='x'){
        $general_font =  str_replace(' ', '+', $general_font);
        wp_enqueue_style( 'wpestate-custom-font',"https://fonts.googleapis.com/css?family=$general_font:400,500,300$headings_font_subset&display=swap");
    }else{
        wp_enqueue_style( 'wpestate-roboto', "https://fonts.googleapis.com/css?family=Roboto:wght@300,400,500,700&amp;subset=latin,latin-ext&display=swap" );
    }
    

    $headings_font = esc_html( get_option('wp_estate_headings_font', '') );
    if($headings_font && $headings_font!='x'){
       $headings_font =  str_replace(' ', '+', $headings_font);
       wp_enqueue_style( 'wpestate-custom-secondary-font', "https://fonts.googleapis.com/css?family=$headings_font:400,500,300&display=swap" );
    }
    wp_enqueue_style( 'font-awesome6.4.2.min',  trailingslashit( get_template_directory_uri() ) . 'public/fontawesome/css/all.min.css' );
  

    $background_modal       =   wpresidence_get_option('wp_estate_login_modal_image','url');
    $css = '
        body::after{
            position:absolute;
            width:0;
            height:0;
            overflow:hidden;
            z-index:-1; // hide images
            content:url('.esc_url($background_modal).');   // load images
            }
        }';
      wp_add_inline_style( 'wpestate_style', $css );
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    // load the general js files
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    wp_enqueue_script("jquery-ui-draggable");
    wp_enqueue_script("jquery-ui-autocomplete");
    wp_enqueue_script("jquery-ui-slider");
    wp_enqueue_script("jquery-ui-datepicker");


    $control_dependencies = array('jquery','slick.min','bootstrap-js');
    if( wpresidence_get_option('wp_estate_use_optima','')!='yes'){
        $control_dependencies = array('jquery','slick.min','bootstrap');
    //    wp_enqueue_script('bootstrap', get_theme_file_uri('/js/bootstrap.min.js'),array(), $theme_object->Version, true);
    }


   // wp_enqueue_style('bootstrap-selectcss.min',get_template_directory_uri().'/css/bootstrap-select.min.css', array(), $theme_object->Version, 'all');
   // wp_enqueue_script('bootstrap-selectjs', get_template_directory_uri().'/js/bootstrap-select.min.js',array('jquery'), $theme_object->Version, true);

  




    wp_enqueue_script('jquery.fancybox.pack', get_template_directory_uri().'/js/jquery.fancybox.pack.js',array('jquery'), $theme_object->Version, true);
    wp_enqueue_script('jquery.fancybox-thumbs', get_template_directory_uri().'/js/jquery.fancybox-thumbs.js',array('jquery'), $theme_object->Version, true);
    wp_enqueue_script('dense.min', get_template_directory_uri().'/js/dense.min.js',array('jquery'), $theme_object->Version, true);
    wp_enqueue_script('placeholders.min', get_template_directory_uri().'/js/placeholders.min.js',array(), $theme_object->Version, true);

    wp_enqueue_script('slideout.min', get_template_directory_uri().'/js/slideout.min.js',array(), $theme_object->Version, true);


    wp_register_script('slick.min', get_template_directory_uri().'/js/slick.min.js',array(), $theme_object->Version, true);
    wp_register_script('owl_carousel', get_template_directory_uri().'/js/owl.carousel.min.js',array('jquery'),$theme_object->Version , true);



    wp_enqueue_script('modernizr.custom.62456', get_template_directory_uri().'/js/modernizr.custom.62456.js',array(), $theme_object->Version, false);


    if(wpresidence_get_option('wp_estate_use_captcha','')=='yes'){
        wp_enqueue_script('recaptcha', 'https://www.google.com/recaptcha/api.js?onload=wpestate_onloadCallback&render=explicit&hl=iw" async defer',array('jquery'), $theme_object->Version, true);
    }

    wp_register_script('venobox.min', get_template_directory_uri().'/js/venobox.min.js',array('jquery'), $theme_object->Version, true);

        
    if (    is_singular( 'estate_property') || 
            $page_template=='page-templates/user_dashboard_analytics.php'  || 
            $page_template=='page-templates/user_dashboard_main.php' || 
            wpestate_is_property_modal()  ||   
            $page_template=='page-templates/page_property_design.php' ){
        
        wp_enqueue_script('venobox.min');
        wp_enqueue_style('venobox');
        wp_enqueue_script('jquery.chart.min', get_theme_file_uri('/js/chart.bundle.min.js'),array('jquery'), $theme_object->Version, true);
       
    }



    $date_lang_status= esc_html ( wpresidence_get_option('wp_estate_date_lang','') );

    if($date_lang_status!='xx' && $date_lang_status!=''){
        $handle="datepicker-".$date_lang_status;
        $name="datepicker-".$date_lang_status.".js";
        wp_enqueue_script($handle, get_theme_file_uri('/js/i18n/'.$name),array('jquery'), $theme_object->Version, true);
    }

    //WPML Translation
    if (function_exists('icl_translate') ){
        if(ICL_LANGUAGE_CODE!='en'){
            $handle="datepicker-".ICL_LANGUAGE_CODE ;
            $name="datepicker-".ICL_LANGUAGE_CODE.".js";
            wp_enqueue_script($handle, get_theme_file_uri('/js/i18n/'.$name),array('jquery'), $theme_object->Version, true);
        }
        
        $date_lang_status=ICL_LANGUAGE_CODE;
    }

    //WPR translation
    if (function_exists('wpestate_get_current_language') ){
        $current_language =wpestate_get_current_language();
        if($current_language!='en'){
            $handle="datepicker-".$current_language ;
            $name="datepicker-".$current_language.".js";
            wp_enqueue_script($handle, get_theme_file_uri('/js/i18n/'.$name),array('jquery'), $theme_object->Version, true);
        }
        
        $date_lang_status=$current_language;
    }


    $enable_stripe_status   =   esc_html ( wpresidence_get_option('wp_estate_enable_stripe','') );
    if($enable_stripe_status==='yes' && wpestate_is_user_dashboard() ){
        wp_enqueue_script('stripe','https://js.stripe.com/v3/',array('jquery'), $theme_object->Version, true);
        wp_enqueue_script('wpestate-stripe', get_template_directory_uri().'/js/wpestate-stripe.js',array('stripe'), $theme_object->Version, true);
        wp_localize_script('wpestate-stripe', 'wpestate_stripe_vars',
                array(  'pub_key'         =>  esc_html( trim( wpresidence_get_option('wp_estate_stripe_publishable_key','') ) ),
                        'pay_failed'    =>  esc_html__('payment failed','wpresidence'),
                        'redirect'      =>  wpestate_get_template_link('user_dashboard_my_reservations.php'),
                        'redirect_book' =>  wpestate_get_template_link('user_dashboard_my_bookings.php'),
                        'redirect_list' =>  wpestate_get_template_link('page-templates/user_dashboard.php'),
                        'admin_url'     =>  get_admin_url(),
                ));
    }


    if ( $page_template == 'page-templates/user_dashboard_add_agent.php' || 
         $page_template == 'page-templates/user_dashboard_add.php' ||
         $page_template == 'page-templates/user_dashboard_profile.php' || 
         $page_template == 'page-templates/front_property_submit.php' ){
        
            wp_enqueue_script("jquery-ui-draggable");
            wp_enqueue_script("jquery-ui-sortable");
    }

    wp_enqueue_script('touch-punch',    get_theme_file_uri ('/js/jquery.ui.touch-punch.min.js'),array('jquery'), $theme_object->Version, true);
    wp_enqueue_style('jquery.ui.theme', get_theme_file_uri ('/public/css/jquery-ui.min.css') );

    $use_generated_pins =   0;
    $load_extra         =   0;
    $post_type          =   get_post_type();



    if( $page_template=='page-templates/advanced_search_results.php' ||
        $page_template=='page-templates/property_list.php' ||
        $page_template=='page-templates/property_list_half.php' ||
        is_singular('estate_agent') ||
        is_singular('estate_property') ||
        is_tax() ){    // search results -> pins are added  from template

        $use_generated_pins=1;
        $json_string=array();
        $json_string=json_encode($json_string);
    }else{
        // google maps pins

        if( $header_type==5 || ( $header_type==0  && $global_header_type==4) ){
            
       
            if ( wpresidence_get_option('wp_estate_readsys','') =='yes' ){

                $path=wpestate_get_pin_file_path();

                global $wp_filesystem;
                if (empty($wp_filesystem)) {
                    require_once (ABSPATH . '/wp-admin/includes/file.php');
                    WP_Filesystem();
                }

                $json_string = $wp_filesystem->get_contents($path);
            }else{
                
                $json_string= wpestate_listing_pins();
            }
        }
    }


    // load idx placing javascript
    if($idx_status=='yes'){
       wp_enqueue_script('idx', get_theme_file_uri( '/js/google_js/idx'.$mimify_prefix.'.js'),array('jquery'), $theme_object->Version, true);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    // load the Google Maps js files
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    $what_map               =   intval( wpresidence_get_option('wp_estate_kind_of_map') );
    $show_g_search_status   =   esc_html ( wpresidence_get_option('wp_estate_show_g_search','') );

    if($what_map==1){
        if( wpestate_check_google_maps_avalability($header_type,$global_header_type) ){
            wpestate_load_google_map();
        }
    }else{
        wp_enqueue_script('wpestate_leaflet',trailingslashit( get_template_directory_uri() ).'js/openstreet/leaflet.js',array('jquery'), $theme_object->Version, true);
        wp_enqueue_style('wpestate_leaflet_css', trailingslashit( get_template_directory_uri() ).'js/openstreet/leaflet.css', array(), $theme_object->Version, 'all');

        wp_enqueue_script('wpestate_leaflet_cluster',trailingslashit( get_template_directory_uri() ).'js/openstreet/leaflet.markercluster.js',array('jquery'), $theme_object->Version, true);
        wp_enqueue_style('wpestate_leaflet_css_markerCluster', trailingslashit( get_template_directory_uri() ).'js/openstreet/MarkerCluster.css', array(), $theme_object->Version, 'all');
        wp_enqueue_style('wpestate_leaflet_css_markerCluster_default', trailingslashit( get_template_directory_uri() ).'js/openstreet/MarkerCluster.Default.css', array(), $theme_object->Version, 'all');
    }


    $pin_images         =   wpestate_pin_images();
    $geolocation_radius =   esc_html ( wpresidence_get_option('wp_estate_geolocation_radius','') );

    if ($geolocation_radius==''){
        $geolocation_radius =1000;
    }

    $pin_cluster_status =   esc_html ( wpresidence_get_option('wp_estate_pin_cluster','') );
    $zoom_cluster       =   esc_html ( wpresidence_get_option('wp_estate_zoom_cluster ','') );
    $show_adv_search    =   esc_html ( wpresidence_get_option('wp_estate_show_adv_search_map_close','') );

    if( isset($post->ID) ){
        $page_lat           =   wpestate_get_page_lat($post->ID);
        $page_long          =   wpestate_get_page_long($post->ID);
        $page_custom_zoom   =   wpestate_get_page_zoom($post->ID);
        $page_custom_zoom_prop   =   get_post_meta($post->ID,'page_custom_zoom',true);
        $closed_height      =   wpestate_get_current_map_height($post->ID);
        $open_height        =   wpestate_get_map_open_height($post->ID);
        $open_close_status  =   wpestate_get_map_open_close_status($post->ID);
    }else{
        $page_lat           =   esc_html( wpresidence_get_option('wp_estate_general_latitude','') );
        $page_long          =   esc_html( wpresidence_get_option('wp_estate_general_longitude','') );
        $page_custom_zoom   =   esc_html( wpresidence_get_option('wp_estate_default_map_zoom','') );
        $page_custom_zoom_prop  =   15;
        $closed_height      =   intval (wpresidence_get_option('wp_estate_min_height',''));
        $open_height        =   wpresidence_get_option('wp_estate_max_height','');
        $open_close_status  =   esc_html( wpresidence_get_option('wp_estate_keep_min','' ) );
    }

    $show_map_on_property           =   esc_html ( wpresidence_get_option('wp_estate_show_map_prop_page2','') );
    if( get_post_type() === 'estate_property' && $show_map_on_property==='yes' && !is_tax() &&!is_search() ){

        $load_extra =   1;
        $google_camera_angle        =   intval( esc_html(get_post_meta($post->ID, 'google_camera_angle', true)) );
        $header_type                =   get_post_meta ( $post->ID, 'header_type', true);
        $global_header_type         =   wpresidence_get_option('wp_estate_header_type','');
        $small_map=0;
        if ( $header_type == 0 ){ // global
            if ($global_header_type != 4){
                $small_map=1;
            }
        }else{
            if($header_type!=5){
                $small_map=1;
            }
        }





        wp_enqueue_script('googlecode_property', get_theme_file_uri('/js/google_js/google_map_code_listing'.$mimify_prefix.'.js') ,array('jquery','wpestate_mapfunctions_base'), $theme_object->Version, true);
        wp_localize_script('googlecode_property', 'googlecode_property_vars',
              array(  'general_latitude'  =>  esc_html( wpresidence_get_option('wp_estate_general_latitude','') ),
                      'general_longitude' =>  esc_html( wpresidence_get_option('wp_estate_general_longitude','') ),
                      'path'              =>  get_theme_file_uri('/css/css-images'),
                      'markers'           =>  $json_string,
                      'camera_angle'      =>  $google_camera_angle,
                      'idx_status'        =>  $use_idx_plugins,
                      'page_custom_zoom'  =>  intval($page_custom_zoom_prop),
                      'current_id'        =>  $post->ID,
                      'generated_pins'    =>  1,
                      'small_map'          => $small_map,
                      'type'              =>  esc_html ( wpresidence_get_option('wp_estate_default_map_type','')),

                   )
          );




    }else if( $page_template=='page-templates/contact_page.php'  ){
        $load_extra =   1;
        if($custom_image    ==  ''){
          wp_enqueue_script('googlecode_contact', get_theme_file_uri('/js/google_js/google_map_code_contact'.$mimify_prefix.'.js'),array('jquery','wpestate_mapfunctions_base'), $theme_object->Version, true);

        }

    }else {
            $tax_header = wpresidence_get_option('wp_estate_header_type_taxonomy','');

            // force load of google map if we have half map tempalte or half map search
            if(    $page_template=='page-templates/property_list_half.php' ||
                    (   $page_template=='page-templates/advanced_search_results.php' && 
                        intval(wpresidence_get_option('wp_estate_property_list_type_adv','') )==2 ) ){
                  $header_type=5;
            }

            if($header_type==5 || $global_header_type==4 || $tax_header==4){
                $load_extra         =   1;
                $is_adv_search      =   0;
                $is_half_map_list   =   0;
                $is_normal_map_list =   0;
                if ( $page_template=='page-templates/advanced_search_results.php'  ){
                    $is_adv_search=1;
                }

                if ( $page_template=='page-templates/property_list_half.php' ||  
                    ( is_tax() &&  intval(wpresidence_get_option('wp_estate_property_list_type','') )==2 ) ){
                    $taxonmy    =   get_query_var('taxonomy');

                    if( $taxonmy == 'property_category' ||
                        $taxonmy == 'property_action_category' ||
                        $taxonmy == 'property_city' ||
                        $taxonmy == 'property_area' ||
                        $taxonmy == 'property_county_state'){

                        $is_half_map_list=1;

                    }

                }

                if( $page_template=='page-templates/advanced_search_results.php' &&  
                    intval(wpresidence_get_option('wp_estate_property_list_type_adv') )==2 ) {
                    $is_half_map_list=1;
                }



                if ( $page_template=='page-templates/property_list.php'  ){
                    $is_normal_map_list=1;
                }


                wp_enqueue_script('googlecode_regular', get_theme_file_uri('/js/google_js/google_map_code'.$mimify_prefix.'.js'),array('jquery','wpestate_mapfunctions_base'), $theme_object->Version, true);
                wp_localize_script('googlecode_regular', 'googlecode_regular_vars',
                    array(  'general_latitude'  =>  $page_lat,
                            'general_longitude' =>  $page_long,
                            'path'              =>  get_theme_file_uri('/css/css-images'),
                            'markers'           =>  $json_string,
                            'idx_status'        =>  $use_idx_plugins,
                            'page_custom_zoom'  =>  intval($page_custom_zoom),
                            'generated_pins'    =>  $use_generated_pins,
                            'page_custom_zoom'  =>  intval($page_custom_zoom),
                            'type'              =>  esc_html ( wpresidence_get_option('wp_estate_default_map_type','')),
                            'is_adv_search'     =>  $is_adv_search,
                            'is_half_map_list'  =>  $is_half_map_list,
                            'is_normal_map_list'=>  $is_normal_map_list

                         )
                );

            }
    }

   $custom_advanced_search  = wpresidence_get_option('wp_estate_custom_advanced_search','');
   $measure_sys             = wpresidence_get_option('wp_estate_measure_sys','');

    $is_half=0;
    if( $page_template=='page-templates/property_list_half.php' ){
        $is_half=1;
    }

    $property_list_type_status =    esc_html(wpresidence_get_option('wp_estate_property_list_type_adv',''));
    if( $page_template=='page-templates/advanced_search_results.php' &&  $property_list_type_status == 2  ){
        $is_half=1;
    }

    $property_list_type_tax =    esc_html(wpresidence_get_option('wp_estate_property_list_type',''));
    if( is_tax() &&  $property_list_type_tax == 2  ){
        $is_half=1;
    }
    if( is_tax('property_category_agent')  ||  is_tax('property_action_category_agent') ||
            is_tax('property_city_agent')||  is_tax('property_area_agent')|| is_tax('property_county_state_agent')){
        $is_half=0;
    }


    $is_prop_list=0;
    if( $page_template=='page-templates/property_list.php'){
        $is_prop_list=1;
    }
    $is_tax=0;
    if( is_tax() ){
        $is_tax=1;
    }

    if( $page_template=='page-templates/user_dashboard_add.php' || 
        $page_template=='page-templates/user_dashboard_profile.php' || 
        $page_template=='page-templates/property_list.php' ){
        $load_extra=1;
    }

    $local_pgpr_slider_type_status ='';
    if(isset($post->ID)){
        $local_pgpr_slider_type_status=  get_post_meta($post->ID, 'local_pgpr_slider_type', true);
    }
    if ($local_pgpr_slider_type_status=='global'){
        $small_slider_t= esc_html ( wpresidence_get_option('wp_estate_global_prpg_slider_type','') );
    }else{
        $small_slider_t=$local_pgpr_slider_type_status;
    }

    $is_adv_search      =   0;
    $is_half_map_list   =   0;
    $is_normal_map_list =   0;

    if ( $page_template=='page-templates/advanced_search_results.php' ){
        $is_adv_search=1;
    }
    if ( $page_template=='page-templates/property_list_half.php' ||  
            ( is_tax() &&  intval(wpresidence_get_option('wp_estate_property_list_type','') )==2 ) ){

        if( $taxonmy == 'property_category' ||
            $taxonmy == 'property_action_category' ||
            $taxonmy == 'property_city' ||
            $taxonmy == 'property_area' ||
            $taxonmy == 'property_county_state'){

            $is_half_map_list=1;

        }

    }



    if( $page_template=='page-templates/advanced_search_results.php' &&  intval(wpresidence_get_option('wp_estate_property_list_type_adv') )==2 ) {
        $is_half_map_list=1;
    }



    if ( $page_template=='page-templates/property_list.php' ){
        $is_normal_map_list=1;
    }



    wp_enqueue_script(
        'wpestate_shared_ajax_map',
        get_theme_file_uri('/js/wpestate_shared_ajax_map'.$mimify_prefix.'.js'),
        array('jquery'),
        $theme_object->Version,
        true
    );

    wp_enqueue_script(
        'mapfunctions',
        get_theme_file_uri('/js/google_js/mapfunctions'.$mimify_prefix.'.js'),
        array(
            'jquery',
            'wpestate_shared_ajax_map',
        ),
        $theme_object->Version,
        true
    );
    wp_localize_script('mapfunctions', 'mapfunctions_vars',
            array(      'path'                      =>  get_theme_file_uri('/css/css-images'),
                        'pin_images'                =>  $pin_images ,
                        'geolocation_radius'        =>  $geolocation_radius,
                        'adv_search'                =>  $adv_search_type_status,
                        'in_text'                   =>  esc_html__(' in ','wpresidence'),
                        'zoom_cluster'              =>  intval($zoom_cluster),
                        'user_cluster'              =>  $pin_cluster_status,
                        'open_close_status'         =>  $open_close_status,
                        'open_height'               =>  $open_height,
                        'closed_height'             =>  $closed_height,
                        'generated_pins'            =>  $use_generated_pins,
                        'geo_no_pos'                =>  esc_html__('The browser couldn\'t detect your position!','wpresidence'),
                        'geo_no_brow'               =>  esc_html__('Geolocation is not supported by this browser.','wpresidence'),
                        'geo_message'               =>  esc_html__('m radius','wpresidence'),
                        'show_adv_search'           =>  $show_adv_search,
                        'custom_search'             =>  $custom_advanced_search,
                        'listing_map'               =>  $listing_map,
                        'slugs'                     =>  $slugs,
                        'hows'                      =>  $hows,
                        'measure_sys'               =>  $measure_sys,
                        'close_map'                 =>  esc_html__('close map','wpresidence'),
                        'show_g_search_status'      =>  $show_g_search_status,
                        'slider_price'              =>  $show_price_slider,
                        'slider_price_position'     =>  $slider_price_position,
                        'adv_search_type'           =>  wpresidence_get_option('wp_estate_adv_search_type',''),
                        'is_half'                   =>  $is_half,
                        'map_style'                 =>  stripslashes (  wpresidence_get_option('wp_estate_map_style','') ),
                        'shortcode_map_style'       =>  stripslashes (  wpresidence_get_option('wp_estate_shortcode_map_style','') ),
                        'small_slider_t'            =>  $small_slider_t,
                        'is_prop_list'              =>  $is_prop_list,
                        'is_tax'                    =>  $is_tax,
                        'half_no_results'           =>  esc_html__('No results found!','wpresidence'),
                        'fields_no'                 =>  intval( wpresidence_get_option('wp_estate_adv_search_fields_no')),
                        'type'                      =>  esc_html ( wpresidence_get_option('wp_estate_default_map_type','')),
                        'useprice'                  =>  esc_html ( wpresidence_get_option('wp_estate_use_price_pins','')),
                        'use_price_pins_full_price' =>  esc_html ( wpresidence_get_option('wp_estate_use_price_pins_full_price','')),
                        'use_single_image_pin'      =>  esc_html ( wpresidence_get_option('wp_estate_use_single_image_pin','')),
                        'loading_results'           =>  esc_html__('loading results...','wpresidence'),
                        'geolocation_type'          =>  wpresidence_get_option('wp_estate_kind_of_map'),
                        'hidden_map'                =>  wpestate_check_show_map_marker(),
                        'is_half_map_list'          =>  $is_half_map_list,
                        'is_half_map_list'          =>  $is_half_map_list,
                        'is_normal_map_list'        =>  $is_normal_map_list,
                        'is_adv_search'             =>  $is_adv_search,
                        'ba'                        =>  esc_html__('BA','wpresidence'),
                        'bd'                        =>  esc_html__('BD','wpresidence'),
                 )
        );



    $hq_latitude =  esc_html( wpresidence_get_option('wp_estate_hq_latitude','') );
    $hq_longitude=  esc_html( wpresidence_get_option('wp_estate_hq_longitude','') );

    if($hq_latitude==''){
        $hq_latitude='40.781711';
    }

    if($hq_longitude==''){
        $hq_longitude='-73.955927';
    }

    wp_enqueue_script('wpestate_mapfunctions_base', trailingslashit( get_template_directory_uri() ).'js/google_js/maps_base.js',array('jquery','mapfunctions'), $theme_object->Version, true);
    wp_localize_script('wpestate_mapfunctions_base', 'mapbase_vars',
        array(  'wp_estate_kind_of_map'         =>  wpresidence_get_option('wp_estate_kind_of_map') ,
                'wp_estate_mapbox_api_key'      =>  wpresidence_get_option('wp_estate_mapbox_api_key'),
                'hq_latitude'       =>  $hq_latitude,
                'hq_longitude'      =>  $hq_longitude,
                'path'              =>  get_theme_file_uri('/css/css-images'),
                'markers'           =>  wpestate_contact_pin(),
                'page_custom_zoom'  =>  intval($page_custom_zoom),
                'address'           =>  esc_html(stripslashes( wpresidence_get_option('wp_estate_co_address', '') ) ),
                'logo'              =>  wpresidence_get_option('wp_estate_company_contact_image', ''),
                'type'              =>  esc_html ( wpresidence_get_option('wp_estate_default_map_type','')),
                'title'             =>  esc_html( wpresidence_get_option('wp_estate_company_name', '') )
            )
    );






    if( $page_template=='page-templates/user_dashboard_add.php' || 
        $page_template=='user-dashboard.php' ||
        $page_template=='page-templates/user_dashboard_profile.php' || 
        $page_template=='page-templates/front_property_submit.php' ){

        $page_lat   = '';
        $page_long  = '';

        $current_user = wp_get_current_user();
    

        if(is_wpresidence_agency()){
            $page_lat  =  esc_html(get_post_meta($post->ID, 'agency_lat', true));
            $page_long =  esc_html(get_post_meta($post->ID, 'agency_long', true));
        }

        if(is_wpresidence_developer()){
            $page_lat  =  esc_html(get_post_meta($post->ID, 'developer_lat', true));
            $page_long =  esc_html(get_post_meta($post->ID, 'developer_long', true));
        }

        if($page_lat=='' || $page_lat==''){
            $page_lat   = esc_html( wpresidence_get_option('wp_estate_general_latitude','') );
            $page_long  = esc_html( wpresidence_get_option('wp_estate_general_longitude','') );
        }


        wp_enqueue_script('google_map_submit', get_theme_file_uri('/js/google_js/google_map_submit'.$mimify_prefix.'.js'),array('jquery'), $theme_object->Version, true);
        wp_localize_script('google_map_submit', 'google_map_submit_vars',
            array(  'general_latitude'      =>  $page_lat,
                    'general_longitude'     =>  $page_long,
                    'geo_fails'             =>  esc_html__('Geolocation was not successful','wpresidence'),
                    'enable_auto'           =>  wpresidence_get_option('wp_estate_enable_autocomplete',''),
                    'limit_country'         =>  wpresidence_get_option('wp_estate_use_geo_location_limit_country',''),
                    'limit_country_select'  =>  wpresidence_get_option('wp_estate_use_geo_location_limit_country_selected','')
                    
                    
                 )
        );

    }

    $login_redirect                 =   wpresidence_get_option('wp_estate_login_redirect','');
    if($login_redirect==''){
        $login_redirect                 =   wpestate_get_template_link('page-templates/user_dashboard_profile.php');
    }

    $show_adv_search_map_close      =   esc_html ( wpresidence_get_option('wp_estate_show_adv_search_map_close','') );
    $max_file_size                  =   100 * 1000 * 1000;
    $current_user                   =   wp_get_current_user();
    $userID                         =   $current_user->ID;

    $argsx=array(
            'br' => array(),
            'em' => array(),
            'strong' => array()
    );
    //$direct_payment_details         =   wp_kses( get_option('wp_estate_direct_payment_details','') ,$argsx);
    if (function_exists('icl_translate') ){
        $mes =  wp_kses( wpresidence_get_option('wp_estate_direct_payment_details','') ,$argsx);
        $direct_payment_details      =   icl_translate('wpestate','wp_estate_property_direct_payment_text', $mes );
    }else{
        $direct_payment_details =  wp_kses( wpresidence_get_option('wp_estate_direct_payment_details','') ,$argsx);
    }

    $submission_curency = esc_html( wpresidence_get_option('wp_estate_submission_curency_custom', '') );
    if($submission_curency == ''){
        $submission_curency = esc_html( wpresidence_get_option('wp_estate_submission_curency', '') );
    }

    
        
    
    if( is_singular( 'estate_property')  || 
        is_singular('estate_agent')  || 
        is_singular('estate_agency') || 
        is_singular('estate_developer') || 
        wpestate_is_property_modal() || 
        $page_template=='page-templates/page_property_design.php'){

        $array_label    =   wp_estate_return_traffic_labels($post_id,14);
        $array_values   =   wp_estate_return_traffic_data_accordion($post_id,14);


        wp_enqueue_script('wpestate_property', get_theme_file_uri('/js/property'.$mimify_prefix.'.js'),array('jquery'), $theme_object->Version, true);
        wp_localize_script('wpestate_property', 'wpestate_property_vars',
            array   (
                        'singular_label'                 => json_encode ( $array_label ),
                        'singular_values'                => json_encode ( $array_values),
                        'label_principal'                => esc_html__('Principal and Interest','wpresidence'),
                        'label_hoo'                      => esc_html__('HOO fees','wpresidence'),
                        'label_property_tax'             => esc_html__('Property Tax','wpresidence'),
                        'label_pmi'                      => esc_html__('Private Mortgage Insurance','wpresidence'),
                        'property_views'        =>  esc_html__( 'Property Views','wpresidence'),
                    )
                    );

    }

    $scroll_trigger= intval ( wpresidence_get_option('wp_estate_header_height','') );
    if($scroll_trigger==0){
        $scroll_trigger=100;
    }
   

    $sticky_search = wpresidence_get_option('wp_estate_sticky_search');
    if( wpestate_is_user_dashboard() ){
        $sticky_search = 'no';
    }
    $control_dependencies_geo=array('jquery');
    wp_register_script('geolocation_search_radius', get_theme_file_uri('/js/geolocation_search_radius'.$mimify_prefix.'.js'),$control_dependencies_geo, $theme_object->Version, true);
    wp_localize_script('geolocation_search_radius', 'geolocation_search_radius_vars',
    array(  
        'geo_radius_measure'    =>  wpresidence_get_option('wp_estate_geo_radius_measure',''),
        'initial_radius'        =>  wpresidence_get_option('wp_estate_initial_radius',''),
        'min_geo_radius'        =>  wpresidence_get_option('wp_estate_min_geo_radius',''),
        'max_geo_radius'        =>  wpresidence_get_option('wp_estate_max_geo_radius',''),
        'miles'                 =>  esc_html__('miles','wpresidence'),
        'km'                    =>  esc_html__('km','wpresidence'),
        'limit_country'         =>  wpresidence_get_option('wp_estate_use_geo_location_limit_country',''),
        'limit_country_select'  =>  wpresidence_get_option('wp_estate_use_geo_location_limit_country_selected','')
    ));

    $content_type   ='';
    if(is_singular('estate_property') && isset($post->ID)){
        $local_pgpr_content_type_status     =   get_post_meta($post->ID, 'local_pgpr_content_type', true);
        $global_prpg_content_type_status    =   esc_html ( wpresidence_get_option('wp_estate_global_prpg_content_type','') );
        $content_type                       =   wpestate_property_page_load_content($local_pgpr_content_type_status ,  $global_prpg_content_type_status); 
    }

//    wp_enqueue_script('control', get_theme_file_uri('/js/control'.$mimify_prefix.'.js'), array('jquery','slick.min','bootstrap-js'), $theme_object->Version, true);

  
    wp_localize_script('control', 'control_vars',
            array(  'morg1'                 =>   esc_html__('Amount Financed:','wpresidence'),
                    'morg2'                 =>   esc_html__('Mortgage Payments:','wpresidence'),
                    'morg3'                 =>   esc_html__('Annual cost of Loan:','wpresidence'),
                    'searchtext'            =>   esc_html__('SEARCH','wpresidence'),
                    'searchtext2'           =>   esc_html__('Search here...','wpresidence'),
                    'path'                  =>   get_theme_file_uri(),
                    'search_room'           =>  esc_html__('Type Bedrooms No.','wpresidence'),
                    'search_bath'           =>  esc_html__('Type Bathrooms No.','wpresidence'),
                    'search_min_price'      =>  esc_html__('Type Min. Price','wpresidence'),
                    'search_max_price'      =>  esc_html__('Type Max. Price','wpresidence'),
                    'contact_name'          =>  esc_html__('Name','wpresidence'),
                    'contact_email'         =>  esc_html__('Email','wpresidence'),
                    'contact_phone'         =>  esc_html__('Phone','wpresidence'),
                    'contact_comment'       =>  esc_html__('Message','wpresidence'),
                    'adv_contact_name'      =>  esc_html__('Name','wpresidence'),
                    'adv_email'             =>  esc_html__('Email','wpresidence'),
                    'adv_phone'             =>  esc_html__('Phone','wpresidence'),
                    'adv_comment'           =>  esc_html__('Message','wpresidence'),
                    'adv_search'            =>  esc_html__('Send','wpresidence'),
                    'admin_url'             =>  get_admin_url(),
           
                 
                    'street_view_on'        =>  esc_html__('Street View','wpresidence'),
                    'street_view_off'       =>  esc_html__('Close Street View','wpresidence'),
                    'userid'                =>  $userID,
                    'show_adv_search_map_close'=>$show_adv_search_map_close,
                    'close_map'             =>  esc_html__('close map','wpresidence'),
                    'open_map'              =>  esc_html__('open map','wpresidence'),
                    'fullscreen'            =>  esc_html__('Fullscreen','wpresidence'),
                    'default'               =>  esc_html__('Default','wpresidence'),
                    'addprop'               =>  esc_html__('Please wait while we are processing your submission!','wpresidence'),
                    'deleteconfirm'         =>  esc_html__('Are you sure you wish to delete?','wpresidence'),
                    'terms_cond'            =>  esc_html__('You need to agree with terms and conditions !','wpresidence'),
                    'user_type_warn'        =>  esc_html__('Please select the user type !','wpresidence'),
                    'procesing'             =>  esc_html__('Processing...','wpresidence'),
                    'slider_min'            =>  floatval( wpresidence_get_option('wp_estate_show_slider_min_price','')),
                    'slider_max'            =>  floatval( wpresidence_get_option('wp_estate_show_slider_max_price','')),
                    'curency'               =>  esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') ),
                    'where_curency'         =>  esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') ),
                    'submission_curency'    =>  $submission_curency,
                    'to'                    =>  esc_html__('to','wpresidence'),
                    'direct_pay'            =>  $direct_payment_details,
                    'send_invoice'          =>  esc_html__('Send me the invoice','wpresidence'),
                    'direct_title'          =>  esc_html__('Direct payment instructions','wpresidence'),
                    'direct_price'          =>  esc_html__('To be paid','wpresidence'),
                    'price_separator'       =>  stripslashes ( esc_html( wpresidence_get_option('wp_estate_prices_th_separator', '') ) ),
                    'plan_title'            =>  esc_html__('Plan Title','wpresidence'),
                    'plan_image'            =>  esc_html__('Plan Image','wpresidence'),
                    'plan_desc'             =>  esc_html__('Plan Description','wpresidence'),
                    'plan_size'             =>  esc_html__('Plan Size','wpresidence'),
                    'plan_rooms'            =>  esc_html__('Plan Rooms','wpresidence'),
                    'plan_bathrooms'        =>  esc_html__('Plan Bathrooms','wpresidence'),
                    'plan_price'            =>  esc_html__('Plan Price','wpresidence'),
                    'readsys'               =>  wpresidence_get_option('wp_estate_readsys',''),
                    'datepick_lang'         =>  $date_lang_status,
                    'deleting'              =>  esc_html__('deleting...','wpresidence'),
                    'save_search'           =>  esc_html__('saving...','wpresidence'),
                    'captchakey'            =>  wpresidence_get_option('wp_estate_recaptha_sitekey',''),
                    'usecaptcha'            =>  wpresidence_get_option('wp_estate_use_captcha',''),
                    'scroll_trigger'        =>  $scroll_trigger,
                    'adv6_taxonomy_term'    =>  wpresidence_get_option('wp_estate_adv6_taxonomy_terms'),
                    'adv6_max_price'        =>  wpresidence_get_option('wp_estate_adv6_max_price'),
                    'adv6_min_price'        =>  wpresidence_get_option('wp_estate_adv6_min_price'),
                    'is_rtl'                =>  $is_rtl,
                    'sticky_footer'         =>  wpresidence_get_option('wp_estate_show_sticky_footer',''),       
                    'stiky_search'          =>  $sticky_search,
                    'sticky_bar'            =>  wpresidence_get_option('wp_estate_theme_sticky_sidebar' ),
                    'new_page_link'         =>  wpresidence_get_option('wp_estate_unit_card_new_page' ),
                    'stripe_pay'            =>  esc_html__('Pay','wpresidence'),
                    'stripe_pay_for'        =>  esc_html__('Payment for package','wpresidence'),
                    'property_modal'        =>  wpestate_is_property_modal(),
                    'location_animation'    =>  wpresidence_get_option('wp_estate_location_animation' ),
                    'location_animation_text'=>  wpresidence_get_option('wp_estate_location_animation_text' ),
                    'indian_format'         => esc_html ( wpresidence_get_option('wp_estate_price_indian_format','') ),
                    'content_type'          => $content_type,
                    'listings_per_row'      => intval(wpresidence_get_option('wp_estate_listings_per_row', '')),
                    'wp_estate_prop_unit'   => esc_html(wpresidence_get_option('wp_estate_prop_unit', '')),
                    'geo_no_results'        =>  esc_html__('there are no results','wpresidence'),
                    'wp_estate_lightbox_slider' => esc_html ( wpresidence_get_option('wp_estate_lightbox_slider','') ),
                    'wpestate_zillow_answer'=>  esc_html('The Zillow estimate value for address','wpresidence'),
                    'zillow_is'             =>  esc_html('is','wpresidence'),
                    'zillow_none'           =>  esc_html('There is no Zillow estimation for address','wpresidence'),
                    'zillow_wait'           =>  esc_html__('Please wait while we gather data!','wpresidence'),
                    'zillow_fields'         =>  esc_html__('Please complete the form!','wpresidence'),
                    'bd'                    =>  esc_html__('bd','wpresidence'),
                    'ba'                    =>  esc_html__('ba','wpresidence'),
                    'decimal_poins'         =>  wpresidence_get_option('wp_estate_prices_decimal_poins' ),
                    'decimal_poins_separator'=> wpresidence_get_option('wp_estate_prices_decimal_poins_separator' ),
                    'agent_field_name'      => esc_html__('Agent Field Name','wpresidence'),
                    'agent_field_value'=> esc_html__('Agent Field Value','wpresidence'),
                    'remove'            => esc_html__('Remove','wpresidence'),
                    'cities'            =>  esc_html__('Cities','wpresidence'),
                    'areas'            =>  esc_html__('Areas','wpresidence'),
                    )
     );

    if ( class_exists( 'WooCommerce' ) ) {
        global $woocommerce;
      //  $checkout_url = $woocommerce->cart->get_checkout_url();
        $checkout_url =wc_get_checkout_url();
    }else{
        $checkout_url='';
    }

    if(  get_option('wp_estate_use_custom_ajaxhandler') == 'no' ){
        $wpestate_ajax  =  get_admin_url() . 'admin-ajax.php';
    }else{
        $wpestate_ajax         =  get_theme_file_uri('ajax_handler.php');
    }

    wp_enqueue_script(
        'ajaxcalls',
        get_theme_file_uri('/js/ajaxcalls'.$mimify_prefix.'.js'),
        array(
            'jquery',
            'wpestate_shared_ajax_map',
        ),
        time(),
        true
    );
    wp_localize_script('ajaxcalls', 'ajaxcalls_vars',
            array(  'contact_name'          =>  esc_html__('Name','wpresidence'),
                    'contact_email'         =>  esc_html__('Email','wpresidence'),
                    'contact_phone'         =>  esc_html__('Phone','wpresidence'),
                    'contact_comment'       =>  esc_html__('Message','wpresidence'),
                    'adv_contact_name'      =>  esc_html__('Name','wpresidence'),
                    'adv_email'             =>  esc_html__('Email','wpresidence'),
                    'adv_phone'             =>  esc_html__('Phone','wpresidence'),
                    'adv_comment'           =>  esc_html__('Message','wpresidence'),
                    'adv_search'            =>  esc_html__('Send','wpresidence'),
                    'disabled'              =>  esc_html__('Disabled','wpresidence'),
                    'published'             =>  esc_html__('Published','wpresidence'),
                    'no_title'              =>  esc_html__('Please, enter property title','wpresidence'),
                    'admin_url'             =>  get_admin_url(),
                    'login_redirect'        =>  $login_redirect,
                    'login_loading'         =>  esc_html__('Sending user info, please wait...','wpresidence'),
                    'userid'                =>  $userID,
                    'prop_featured'         =>  esc_html__('Property is featured','wpresidence'),
                    'no_prop_featured'      =>  esc_html__('You have used all the "Featured" listings in your package.','wpresidence'),
                    'favorite'              =>  esc_html__('favorite','wpresidence'),
                    'add_favorite'          =>  esc_html__('add to favorites','wpresidence'),
                    'remove_fav'            =>   esc_html__('remove from favorites','wpresidence'),
                    'saving'                =>  esc_html__('saving..','wpresidence'),
                    'sending'               =>  esc_html__('sending message..','wpresidence'),
                    'error_field'           =>  esc_html__('Please, enter field:','wpresidence'),
                    'noimages'              =>  esc_html__('You need to upload at last one image','wpresidence'),
                    'notitle'               =>  esc_html__('Please, enter property title','wpresidence'),
                    'paypal'                =>  esc_html__('Connecting to Paypal! Please wait...','wpresidence'),
                    'stripecancel'          =>  esc_html__('subscription will be cancelled at the end of current period','wpresidence'),
                    'userpass'              =>  esc_html ( wpresidence_get_option('wp_estate_enable_user_pass','') ),
                    'disablelisting'        =>  esc_html__( 'Disable Listing','wpresidence'),
                    'enablelisting'         =>  esc_html__( 'Enable Listing','wpresidence'),
                    'disableagent'          =>  esc_html__( 'Disable Agent','wpresidence'),
                    'enableagent'           =>  esc_html__( 'Enable Agent','wpresidence'),
                    'agent_list'            =>  wpestate_get_template_link('page-templates/user_dashboard_agent_list.php'),
                    'use_gdpr'              =>  esc_html(wpresidence_get_option('wp_estate_use_gdpr','')),
                    'gdpr_terms'            =>  esc_html__( 'You must agree with GDPR terms','wpresidence'),
                    'delete_account'        =>  esc_html__('Confirm your ACCOUNT DELETION request! Clicking the button below will delete your account and data. This means you will no longer be able to login to your account and access your account information: My Profile, My Properties, Inbox, Saved Searches and Messages. This operation CAN NOT BE REVERSED!','wpresidence'),
                    'checkout_url'          =>  esc_url($checkout_url),
                    'wpestate_ajax'         =>  $wpestate_ajax,
                    'property_views'        =>  esc_html__( 'Property Views','wpresidence'),
                    'contact_agent'         =>  esc_html__( 'Contact Agent','wpresidence'),
                    'favorites_login'       =>  wpresidence_get_option('wp_estate_favorites_login'),
                     'wp_estate_prop_unit'   => esc_html(wpresidence_get_option('wp_estate_prop_unit', '')),

                )
     );


    wp_enqueue_script('wpestate_login_register', get_theme_file_uri('/js/wpestate_login_register'.$mimify_prefix.'.js'),array('jquery'), $theme_object->Version, true);
    wp_localize_script('wpestate_login_register', 'wpestate_login_register_vars',
        array(     'admin_url'             =>  get_admin_url(),
                    'procesing'=>  esc_html__('Processing...','wpresidence'),
                    'login_loading'         =>  esc_html__('Sending user info, please wait...','wpresidence'),
                    'login_redirect'        =>  $login_redirect,
                    'forgot_warning'        => esc_html('Please enter your email address.','wpresidence'),	
                    'generic_register_error'=> esc_html__('Registration failed. Please try again.','wpresidence'),
        )
    );

 


    if( wpestate_is_user_dashboard() ){
        wp_enqueue_script('dashboard', get_theme_file_uri('/js/dashboard'.$mimify_prefix.'.js'),array('jquery'), $theme_object->Version, true);
        wp_localize_script('dashboard', 'dashboard_vars',
            array(  
                'admin_url'             =>  get_admin_url(),
                'direct_thx'            =>  esc_html__('Thank you. Please check your email for payment instructions.','wpresidence'),
                'sending'                 =>   esc_html__('Sending','wpresidence'),
                'send_reply'                 =>   esc_html__('Send Reply','wpresidence') 
                ) 
        );
            
    }



    if( $page_template== 'page-templates/property_list_directory.php' ){
        wp_enqueue_script('directory', get_theme_file_uri('/js/property_directory_control'.$mimify_prefix.'.js'),array('jquery'), $theme_object->Version, true);

        wp_localize_script('directory', 'directory_vars',
            array(
                'dir_min_size'          =>  wpestate_convert_measure( get_post_meta($post->ID,'dir_min_size',true) ),
                'dir_max_size'          =>  wpestate_convert_measure( get_post_meta($post->ID,'dir_max_size',true) ),
                'dir_min_lot_size'      =>  wpestate_convert_measure( get_post_meta($post->ID,'dir_min_lot_size',true) ),
                'dir_max_lot_size'      =>  wpestate_convert_measure( get_post_meta($post->ID,'dir_max_lot_size',true) ),
                'dir_rooms_min'         =>  get_post_meta($post->ID,'dir_rooms_min',true),
                'dir_rooms_max'         =>  get_post_meta($post->ID,'dir_rooms_max',true),
                'dir_bedrooms_min'      =>  get_post_meta($post->ID,'dir_bedrooms_min',true),
                'dir_bedrooms_max'      =>  get_post_meta($post->ID,'dir_bedrooms_max',true),
                'dir_bathrooms_min'     =>  get_post_meta($post->ID,'dir_bathrooms_min',true),
                'dir_bathrooms_max'      =>  get_post_meta($post->ID,'dir_bedrooms_max',true),
                'measures_sys'          =>  wpestate_return_measurement_sys(),
                'no_more'               =>  esc_html__('No More Listings','wpresidence'),
                'post_id' => get_the_ID(),

            ) );
    }

    if( $page_template=='page-templates/front_property_submit.php' ){
        // Load the script that manages front-end property submissions.
        wp_enqueue_script('front_end_submit', get_theme_file_uri('/js/front_end_submit'.$mimify_prefix.'.js'),array('jquery'), $theme_object->Version, true);
        // Expose AJAX endpoint, nonce and reCAPTCHA keys to the script.
        wp_localize_script( 'front_end_submit', 'front_property_submit_vars', array(
                'ajaxurl'    => admin_url( 'admin-ajax.php' ),
                'nonce'      => wp_create_nonce( 'wpestate_front_property_submit' ),
                'captchakey' => wpresidence_get_option('wp_estate_recaptha_sitekey',''),
                'usecaptcha' => wpresidence_get_option('wp_estate_use_captcha',''),
        ) );

    }


    // if we are on dashboard load styles and js from separate files
    if( wpestate_is_user_dashboard() ){
        wpestate_enqueue_for_dashboard($mimify_prefix,$theme_object);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////file upload ajax - profile and user dashboard
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if( $page_template=='page-templates/user_dashboard_profile.php' ||
        $page_template=='page-templates/user_dashboard_add.php'  || 
        $page_template=='page-templates/user_dashboard_add_agent.php'  ||
        $page_template=='page-templates/front_property_submit.php' ){

        $is_profile=0;
        if ( $page_template=='page-templates/user_dashboard_profile.php' ){
            $is_profile=1;
        }

        $plup_url = add_query_arg( array(
            'action'    =>  'wpestate_me_upload',
            'base'      =>  $is_profile,
            'page_template'=>  $page_template,
            'nonce'     =>  wp_create_nonce('aaiu_allow'),
        ), admin_url('admin-ajax.php') );

        $max_images             =   intval   ( wpresidence_get_option('wp_estate_prop_image_number','') );

        $current_user           =   wp_get_current_user();
        $userID                 =   $current_user->ID;
        $parent_userID          =   wpestate_check_for_agency($userID);
        $paid_submission_status =   esc_html ( wpresidence_get_option('wp_estate_paid_submission','') );
        if( $paid_submission_status == 'membership'){
            $user_pack              =   get_the_author_meta( 'package_id' , $parent_userID );
            if($user_pack!=''){
                $max_images         =   get_post_meta($user_pack, 'pack_image_included', true);
            }else{
                 $max_images        = intval   ( wpresidence_get_option('wp_estate_free_pack_image_included','') );
            }
        }

        $plup_options= array(
                                            'runtimes'          => 'html5,flash,html4',
                                            'browse_button'     => 'aaiu-uploader',
                                            'container'         => 'aaiu-upload-container',
                                            'file_data_name'    => 'aaiu_upload_file',
                                            'max_file_size'     => $max_file_size . 'b',
                                            'url'               => $plup_url,
                                            'flash_swf_url'     => includes_url('js/plupload/plupload.flash.swf'),
                                            'filters'           => array(array('title' => esc_html__('Allowed Files','wpresidence'), 'extensions' => "jpeg,jpg,gif,png,pdf,webp")),
                                            'multipart'         => true,
                                            'urlstream_upload'  => true,
                                            );

        if($page_template=='page-templates/user_dashboard_add.php' ||   
            $page_template=='page-templates/front_property_submit.php' ){
            $plup_options['drop_element']      = 'drag-and-drop';
        }

        wp_enqueue_script('ajax-upload', get_theme_file_uri('/js/ajax-upload'.$mimify_prefix.'.js'),array('jquery','plupload-handlers'), $theme_object->Version, true);
        wp_localize_script('ajax-upload', 'ajax_vars',
            array(  'ajaxurl'           => esc_url(admin_url('admin-ajax.php')),
                    'nonce'             => wp_create_nonce('aaiu_upload'),
                    'remove'            => wp_create_nonce('aaiu_remove'),
                    'number'            => 1,
                    'warning'           =>  esc_html__('Image needs to be at least 500px height  x 500px wide!','wpresidence'),
                    'upload_enabled'    => true,
                    'path'              =>  get_theme_file_uri(),
                    'max_images'        =>  $max_images,
                    'warning_max'      =>  esc_html__('You cannot upload more than','wpresidence').' '.$max_images.' '.esc_html__('images','wpresidence'),
                    'confirmMsg'        => esc_html__('Are you sure you want to delete this?','wpresidence'),
                    'plupload'         => $plup_options,

                )
                );
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////file upload ajax - floor plans
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    if (  is_singular('post') && get_option( 'thread_comments' ) ){
        wp_enqueue_script( 'comment-reply' );
    }

}
endif; // end   wpestate_scripts


/* * Register scripts for reviews
 */
if( !function_exists('wpestate_register_review_scripts') ):
function wpestate_register_review_scripts() {

    $theme_object=wp_get_theme();

    wp_register_script( 'wpestate_reviews', get_theme_file_uri('/js/reviews.js'), array('jquery'), $theme_object->Version, true );
    wp_localize_script('wpestate_reviews', 'review_vars', array(
        'admin_url'             =>  get_admin_url(),
        'ajax_url'              => admin_url('admin-ajax.php'),
        'nonce'                 => wp_create_nonce('estate_review_nonce'),
        'posting'               =>  esc_html__('posting','wpresidence'),
        'review_posted'         =>  esc_html__('Review Sent ','wpresidence'),
        'review_edited'         =>  esc_html__('Review Edit Saved','wpresidence'),
    ));

}
endif; // end   wpestate_register_review_scripts





///////////////////////////////////////////////////////////////////////////////////////////
/////// Js & Css include on admin site
///////////////////////////////////////////////////////////////////////////////////////////


if( !function_exists('wpestate_admin') ):

function wpestate_admin($hook_suffix) {

    global $post;
    global $pagenow;
    global $typenow;
    $theme_object=wp_get_theme();

    wp_enqueue_media();
    wp_enqueue_script("jquery-ui-autocomplete");
    wp_enqueue_script("jquery-ui-draggable");
    wp_enqueue_script("jquery-ui-droppable");
    wp_enqueue_script("jquery-ui-sortable");
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_script('my-upload');
    wp_enqueue_style('thickbox');
    // /wp_enqueue_style('adminstyle', get_theme_file_uri('/public/admin/admin.css') );

    wp_enqueue_script('admin-control', get_theme_file_uri('/js/admin-control.js') , array('jquery'), $theme_object->Version, true);
    wp_localize_script('admin-control', 'admin_control_vars',
        array( 'ajaxurl'            => esc_url(admin_url('admin-ajax.php')),
                'plan_title'        =>  esc_html__('Plan Title','wpresidence'),
                'plan_image'        =>  esc_html__('Plan Image','wpresidence'),
                'plan_desc'         =>  esc_html__('Plan Description','wpresidence'),
                'plan_size'         =>  esc_html__('Plan Size(*only numbers)','wpresidence'),
                'plan_rooms'        =>  esc_html__('Plan Rooms','wpresidence'),
                'plan_bathrooms'    =>  esc_html__('Plan Bathrooms','wpresidence'),
                'plan_price'        =>  esc_html__('Plan Price','wpresidence'),
                'admin_url'         =>  get_admin_url(),

        )
    );




    if($hook_suffix=='post-new.php' || $hook_suffix=='post.php'){
        wp_enqueue_script("jquery-ui-datepicker");
        wp_enqueue_style( 'font-awesome-5.min',  get_theme_file_uri( '/public/fontawesome/css/all.css') );
        wp_enqueue_style('jquery.ui.theme', get_theme_file_uri( '/public/css/jquery-ui.min.css') );
    }

    if (empty($typenow) && !empty($_GET['post'])) {
        $allowed_html   =   array();
        $post = get_post( esc_html( wp_kses( $_GET['post'], $allowed_html) ) );
        $typenow = $post->post_type;
    }


    $allowed_taxonomies = [
        'property_category', 
        'property_action_category', 
        'estate_property_type',
        'property_category_agent', 
        'property_action_category_agent',
        'property_city_agent',
        'property_area_agent',
        'property_county_state_agent', 
        'category_agency',  
        'action_category_agency',
        'city_agency',
        'area_agency',  
        'county_state_agency',
        'property_category_developer',
        'property_action_developer',
        'property_city_developer',
        'property_area_developer',
        'property_county_state_developer'
    ];



   if (
        is_admin() &&
        (
            // Post editing/adding
            $pagenow == 'post-new.php' || 
            $pagenow == 'post.php' || 

            // Term editing/adding
            $pagenow == 'edit-tags.php' || 
            $pagenow == 'term.php'
        ) &&
        (
            // Post types
            $typenow == 'estate_property' || 
            $typenow == 'estate_agency' || 
            $typenow == 'estate_developer' ||

            // Taxonomies
            ( isset($_GET['taxonomy']) && in_array($_GET['taxonomy'],$allowed_taxonomies ) )
        )
    ) {



        $what_map = intval( wpresidence_get_option('wp_estate_kind_of_map') );


        $libraries  =   '';
        if($what_map==1){
            if ( is_ssl() ){
                wp_enqueue_script('googlemap',      'https://maps-api-ssl.google.com/maps/api/js?v=quarterly'.$libraries.'&callback=wpestateInitMapAdmin&key='.esc_html(wpresidence_get_option('wp_estate_api_key', '') ).'',array('jquery','admin-control'), $theme_object->Version, false); 
            }else{
                wp_enqueue_script('googlemap',      'http://maps.googleapis.com/maps/api/js?v=quarterly'.$libraries.'&callback=wpestateInitMapAdmin&key='.esc_html(wpresidence_get_option('wp_estate_api_key', '') ).'',array('jquery','admin-control'), $theme_object->Version, false);
            }

        }else{
            wp_enqueue_script('wpestate_leaflet',trailingslashit( get_template_directory_uri() ).'js/openstreet/leaflet.js',array('jquery'), $theme_object->Version, true);
            wp_enqueue_style('wpestate_leaflet_css', trailingslashit( get_template_directory_uri() ).'js/openstreet/leaflet.css', array(), $theme_object->Version, 'all');

        }
        wp_enqueue_script('admin_google',   get_theme_file_uri('/js/google_js/admin_google.js'),array('jquery'), $theme_object->Version, true);
    
        $wp_estate_general_latitude='';
        $wp_estate_general_longitude='';
    
        if(isset($post->ID)){
            $wp_estate_general_latitude  = esc_html(get_post_meta($post->ID, 'property_latitude', true));
            $wp_estate_general_longitude = esc_html(get_post_meta($post->ID, 'property_longitude', true));
        }
     

        if( $typenow=='estate_agency' ){
            $wp_estate_general_latitude  = esc_html(get_post_meta($post->ID, 'agency_lat', true));
            $wp_estate_general_longitude = esc_html(get_post_meta($post->ID, 'agency_long', true));
        }else if( $typenow=='estate_developer' ){
            $wp_estate_general_latitude  = esc_html(get_post_meta($post->ID, 'developer_lat', true));
            $wp_estate_general_longitude = esc_html(get_post_meta($post->ID, 'developer_long', true));
        }


        if (isset($_GET['tag_ID']) && is_admin()) {
            // We're on term edit page
            $t_id = intval($_GET['tag_ID']);
            $term_meta = get_option("taxonomy_$t_id");
            
            $wp_estate_general_latitude  = isset($term_meta['term_latitude']) ? esc_html($term_meta['term_latitude']) : '';
            $wp_estate_general_longitude = isset($term_meta['term_longitude']) ? esc_html($term_meta['term_longitude']) : '';
        }

        if ($wp_estate_general_latitude=='' || $wp_estate_general_longitude=='' ){
            $wp_estate_general_latitude    = esc_html( wpresidence_get_option('wp_estate_general_latitude','') ) ;
            $wp_estate_general_longitude   = esc_html( wpresidence_get_option('wp_estate_general_longitude','') );

            if($wp_estate_general_latitude==''){
               $wp_estate_general_latitude ='40.781711';
            }

            if($wp_estate_general_longitude==''){
               $wp_estate_general_longitude='-73.955927';
            }
        }

        $post_id = 0;
        if (isset( $post->ID) ) {
            $post_id = $post->ID;
        }


        wp_localize_script('admin_google', 'admin_google_vars',
        array(  'general_latitude'      =>  $wp_estate_general_latitude,
                'general_longitude'     =>  $wp_estate_general_longitude,
                'postId'                =>     $post_id ,
                'geo_fails'             =>  esc_html__('Geolocation was not successful','wpresidence'),
                'geolocation_type'      =>  wpresidence_get_option('wp_estate_kind_of_map'),
                'wpresidence_map_type'  =>  intval( wpresidence_get_option('wp_estate_kind_of_map')),
                'wp_estate_mapbox_api_key'  =>  wpresidence_get_option('wp_estate_mapbox_api_key'),
            )


        );
     }


        wp_enqueue_script('admin_js', get_theme_file_uri('/js/admin.js'),array('jquery'), $theme_object->Version, true);
        wp_localize_script('admin_js', 'admin_js_vars',
            array(  'ajaxurl'                   =>  esc_url(admin_url('admin-ajax.php')),
                    'admin_url'                 =>  get_admin_url(),
                    'number'                    =>  1,
                    'warning'                   =>  __('Warning !','wpresidence'),
                    'path'                      =>  get_template_directory(),
                    'path2'                     => get_template_directory_uri(),
                    'confirm_text'              =>  esc_html__(' Are you sure you want to remove this image?','wpresidence')

                )
        );
;
        wp_enqueue_script('admin_colorpicker', get_theme_file_uri('/js/admin_colorpicker.js'),array('jquery'), $theme_object->Version, true);
        wp_enqueue_script('config-property', get_theme_file_uri('/js/config-property.js'),array('jquery'), $theme_object->Version, true);


}

endif; // end   wpestate_admin


if(!function_exists('wpestate_login_logo_function')){
    function wpestate_login_logo_function(){
         wp_enqueue_style('loginstyle', get_theme_file_uri('public/admin/login.css') );
    }
}

?>
