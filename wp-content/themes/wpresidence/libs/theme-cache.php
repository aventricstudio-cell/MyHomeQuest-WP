<?php
if( !function_exists('wpestate_clear_cache_theme') ):
function wpestate_clear_cache_theme() {  
    wpestate_delete_cache();
    print'<div class="wrap wpresidence_clear_cache">'.esc_html__('Cache was cleared','wpresidence').'</div>';
    exit();
}
endif;



if( !function_exists('wpestate_check_google_maps_avalability') ):
    function wpestate_check_google_maps_avalability($header_type,$global_header_type,$postid=''){
        $header_type        =   intval($header_type);
        $global_header_type =   intval($global_header_type);
        $to_return          =   false; // no g maps
       
        global $post;
        $page_template='';
       
        if(isset($post->ID)){
           $page_template = get_post_meta( $post->ID, '_wp_page_template', true );
           $page_template = ($page_template);
        }
      
        if( $page_template=='page-templates/splash_page.php' ){
            $to_return          =   false;
        }else if(  is_tax() ){
            if( wpestate_check_google_map_tax()  ){  
                $to_return  =   false;
            }else{
          
                $to_return  =   true;
            }
            
        }else if( $header_type==5 ||                                      // if local header type 
                ( $header_type==0 
                    && $global_header_type==4 
                    && $page_template!=='user_dashboard_add_adgent.php' 
                    && $page_template!=='page-templates/user_dashboard_analytics.php'  
                    && $page_template!=='page-templates/user_dashboard_agent_list.php'  
                    && $page_template!=='page-templates/user_dashboard_favorite.php'  
                    && $page_template!=='page-templates/user_dashboard_inbox.php'  
                    && $page_template!=='page-templates/user_dashboard_invoices.php'
                    && $page_template!=='page-templates/user_dashboard_searches.php' )  ||        //  if  local is set to global and global is google
                $page_template=='page-templates/user_dashboard_add.php' ||           //  if add property page
               // $page_template=='page-templates/front_property_submit.php' ||        //  if frint add property page
                $page_template=='page-templates/user_dashboard_profile.php' ||       //  for cases when you are agengy
                $page_template=='page-templates/property_list_half.php' ||           //  for half map 
                is_singular('estate_agency')  ||                        //  check if agency page
                is_singular('estate_developer')  ||                     //  check if developer page
                is_singular('estate_property')                           //  for cases when property page  
        ){
      
            $to_return=true; // we have g maps
        }else if($page_template=='page-templates/front_property_submit.php'){

            $wpestate_submission_page_fields=   wpresidence_get_option('wp_estate_submission_page_fields','');
            if( in_array('property_map', $wpestate_submission_page_fields) ) {
                return true; // we have g maps
            }else if( wpresidence_get_option('wp_estate_enable_autocomplete','')=='yes'){
                return true; // we have g maps
            }


        }


        return $to_return;


    }
endif;






