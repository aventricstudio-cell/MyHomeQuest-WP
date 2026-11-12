<?php


if(!function_exists('wpestate_load_google_map')):
    function wpestate_load_google_map($force_places='no'){
        $show_map_on_property           =   esc_html ( wpresidence_get_option('wp_estate_show_map_prop_page2','') );

        if(is_singular('estate_property') && $show_map_on_property==='no'){
          return;
        }

        $what_map       =   intval( wpresidence_get_option('wp_estate_kind_of_map'));
        $use_mimify     =   wpresidence_get_option('wp_estate_use_mimify','');
        $mimify_prefix  =   '';
        if($what_map==1){
            if($use_mimify==='yes'){
                $mimify_prefix  =   '.min';
            }

            if (!wp_script_is( 'googlemap', 'enqueued' )) {

                $libraries  =   '';

                  if( intval(wpresidence_get_option('wp_estate_kind_of_map'))==1 ){

                    if (    wpresidence_get_option('wp_estate_show_g_search')=='yes' ||
                            is_singular('estate_property') ||
                            is_page_template('page-templates/property_list_half.php') ||
                            ( is_page_template('page-templates/advanced_search_results.php') && intval(wpresidence_get_option('wp_estate_property_list_type_adv','') )==2 ) ||
                            ( is_tax() &&  intval(wpresidence_get_option('wp_estate_property_list_type','') )==2 ) ||
                            ( is_tax() &&  intval(wpresidence_get_option('wp_estate_header_type_taxonomy','') )==4 ) ||
                            
                            is_page_template('page-templates/user_dashboard_add.php') ||
                            is_page_template('page-templates/front_property_submit.php') ||
                            $force_places=='yes'  ){
                              
                                $libraries  =   '&libraries=places';
                    }
                }


                if ( is_ssl() ) {
                    wp_enqueue_script('googlemap', 'https://maps-api-ssl.google.com/maps/api/js?v=quarterly'.$libraries.'&callback=wpestateInitMap&amp;key='.esc_html(wpresidence_get_option('wp_estate_api_key', '') ),array('jquery','wpestate_mapfunctions_base'), '1.0', true);              
                }else{
                    wp_enqueue_script('googlemap', 'http://maps.googleapis.com/maps/api/js?v=quarterly'.$libraries.'&callback=wpestateInitMap&amp;key='.esc_html(wpresidence_get_option('wp_estate_api_key', '') ),array('jquery','wpestate_mapfunctions_base'), '1.0', true);
                }
                wp_enqueue_script('infobox',  get_theme_file_uri('/js/infobox.min.js'),array('jquery'), '1.0', true);
                wp_enqueue_script('markerclusterer', get_theme_file_uri('/js/google_js/markerclusterer'.$mimify_prefix.'.js'),array('jquery'), '1.0', true);
                wp_enqueue_script('oms.min', get_theme_file_uri('/js/google_js/oms.min.js'),array('jquery'), '1.0', true);
                wp_enqueue_script('wpestate_marker_min', get_theme_file_uri('/js/google_js/wpestate_marker.js'),array('jquery'), '1.0', true);
            }
        }
    }
endif;
