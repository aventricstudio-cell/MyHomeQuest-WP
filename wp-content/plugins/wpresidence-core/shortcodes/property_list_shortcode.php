<?php
/*
*
*  blog list widget
*
*
*
*
*/



if( !function_exists('wpestate_blog_list_widget') ):
function wpestate_blog_list_widget($attributes,$is_ajax){


    $return_string  =   '';
    $is_ajax        =   intval($is_ajax);  
    $attributes = shortcode_atts(
                array(
                    'title'                 =>  '',
                    'number'                =>  4,
                    'rownumber'             =>  4,
                    'control_terms_id'      =>  '',
                    'sort_by'               =>   0,
                    'card_version'          =>   '',
                    'page'                  =>  1,
                    'display_grid'          =>  'no',
                    'display_grid_wrapper'  =>  '',
                ), $attributes) ;


      
    $orderby    =   'ID';
    $order      =   'DESC';
      
    if( isset( $attributes['sort_by']) && $attributes['sort_by']!=0 ) {
          
        switch( $attributes['sort_by']){
            case 1:
                $orderby='title';
                $order='ASC';
                break;
            case 2:
               $orderby='title';
               $order='DESC';
               break;
            case 3:
               $orderby='ID';
               $order='DESC';
               break;
            case 4:
               $orderby='ID';
               $order='ASC';
               break;
        }
        
    }
      
    $posts_per_page=$attributes['number'];
    $paged=1;
    if(isset($attributes['page'])){
        $paged=intval($attributes['page']);
    }
    

   

    $args=array(
        'post_type'     => 'post',
        'post_status'   =>  'publish',
        'orderby'       => $orderby,
        'order'         => $order,
        'posts_per_page'=>$posts_per_page,
        'paged'         => $paged
    );


  if( isset( $attributes['control_terms_id']) and $attributes['control_terms_id']!=''){
     
        $args['cat']  =   $attributes['control_terms_id'];
        
    } 

    $display_grid_class=' row ';
    if($attributes['display_grid']=='yes'){
        $display_grid_class=' wpestate_grid_view_wrapper ';
    }
 
      
    $button = '<div class="listinglink-wrapper_sh_listings exclude-rtl">
        <span class="wpresidence_button wpestate_item_list_sh blog_list_loader">  '.esc_html__('load articles','wpresidence-core').' </span>
        </div>';
       
    if($is_ajax==0){    
        $return_string .= '<div class="wpresidence_shortcode_listings_wrapper '.esc_attr($display_grid_class).' wpestate_latest_listings_sh bottom-post "  '

            . 'data-category_ids="'.esc_attr($attributes['control_terms_id']).'" '     
            . 'data-number="'.intval($posts_per_page).'" '
            . 'data-row-number="'.intval($attributes['rownumber']).'" '
            . 'data-card-version="'.esc_attr($attributes['card_version']).'" '
            . 'data-sort-by="'.esc_attr($attributes['sort_by']).'"'
            . 'data-display-grid="'.esc_attr($attributes['display_grid']).'"'
            . 'data-page="'.intval($paged).'" >';

        if($attributes['title']!=''){
            $return_string .= '<h2 class="shortcode_title">'.wp_kses_post($attributes['title']).'</h2>';
        }
    }




    $recent_posts=new Wp_Query($args);
 

    if($attributes['display_grid']=='yes'){
        $return_string .='<div class="items_shortcode_wrapper_grid"   data-row-number="'.intval($attributes['rownumber']).'" >';
    }else{
        $return_string .='<div class="blog25_shortcode_wrapper '.esc_attr($display_grid_class).'"   data-row-number="'.intval($attributes['rownumber']).'" >';
    }
    ob_start();
    wpresidence_display_blog_list_as_html($recent_posts, array(), $context = 'shortcode',$attributes);
    $templates = ob_get_contents();
    ob_end_clean();
   
    if(isset($attributes['display_grid_wrapper']) && $attributes['display_grid_wrapper']=='no' ){
        
        if($is_ajax==0){  
            return $templates;
        }else{
            return array(
                'success' => true,
                'html'=> $templates,
                'results'=>$recent_posts->post_count
            );
        }
        
    }
    
    
        
        
    $return_string .=$templates;
    

           $return_string .= '</div>';
   
 
    
   

    if($is_ajax==0){  
        $return_string.='<div class="wpestate_listing_sh_loader">
           <div class="new_prelader"></div>
        </div>';
        $return_string .=$button;
        $return_string .= '</div>';
        return $return_string;

    }else{
        return array(
            'success' => true,
            'html'=> $return_string,
            'results'=>$recent_posts->found_posts
        );
    }

  
}
endif;







