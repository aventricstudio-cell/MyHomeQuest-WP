<?php

/*
*
* slider - recent itenms
*
*
*
*
*/
if( !function_exists('wpestate_slider_recent_posts_pictures') ):

function wpestate_slider_recent_posts_pictures($attributes) {

    $return_string      =   '';
    $class              =   '';
    $templates          =   '';

    $attributes = shortcode_atts(
                array(
                    'title'                 =>  '',
                    'type'                  => 'properties',
                    'arrows'                =>  'top',
                    'category_ids'          =>  '',
                    'action_ids'            =>  '',
                    'city_ids'              =>  '',
                    'area_ids'              =>  '',
                    'state_ids'             =>  '',
                    'status_ids'            =>  '',
                    'number'                =>  4,
                    'items_per_row_visible' =>  3,
                    'show_featured_only'    =>  'no',
                    'random_pick'           =>  'no',
                    'autoscroll'            =>  0,
                    'featured_first'        =>  'no',
                    'systemx'               =>  '',
                    'sort_by'               =>   0,
                    'card_version'          =>   '',
                ), $attributes) ;

            
    wp_enqueue_script('slick.min');
    // Keep shortcode_arguments for UI elements (title, arrows, autoscroll, systemx)
    // and for the agents/blog branches that still use the legacy query path.
    $shortcode_arguments  =  wpestate_prepare_arguments_shortcode($attributes);
    $class = "nobutton";

    $return_string .= '<div class=" slider_container bottom-'.$shortcode_arguments['type'].' '.$class.' '.$shortcode_arguments['systemx'].' " >';
    if($shortcode_arguments['title']!=''){
         $return_string .= '<h2 class="shortcode_title title_slider">'.wp_kses_post($shortcode_arguments['title']).'</h2>';
    }

     $is_autoscroll  =   ' data-auto="'.$shortcode_arguments['autoscroll'].'" ';

    $return_string .=  '<div class="shortcode_slider_wrapper" >';

    $transient_name= 'wpestate_recent_posts_slider_' . $shortcode_arguments['type']. '_' . $shortcode_arguments['category'] . '_' . $shortcode_arguments['action'] . '_' . $shortcode_arguments['city'] . '_' . $shortcode_arguments['area']. '_' . $shortcode_arguments['state'] ;
    $transient_name.='_'.$shortcode_arguments['number'].'_'.$shortcode_arguments['featured_first'].'_'.$shortcode_arguments['show_featured_only'].'_'.$shortcode_arguments['autoscroll'];

    if ( function_exists( 'wpestate_set_transient_name_multilang' ) ) {
        $transient_name = wpestate_set_transient_name_multilang( $transient_name . '_' );
    } else {
        $transient_name .= '_';
    }

    if ( isset($_COOKIE['my_custom_curr_symbol'] ) ){
        $transient_name.='_'.$_COOKIE['my_custom_curr_symbol'];
    }
    if(isset($_COOKIE['my_measure_unit'])){
        $transient_name.= $_COOKIE['my_measure_unit'];
    }

    $templates=false;
    if(function_exists('wpestate_request_transient_cache')){
        $templates = wpestate_request_transient_cache( $transient_name);
    }

    if($templates === false ){
        if ($shortcode_arguments['type'] == 'properties') {
            // ── Build 2026 params directly from shortcode attributes ──
            $params = array();

            // Map shortcode taxonomy attrs to 2026 canonical param keys (all term IDs).
            $taxonomy_map = array(
                'category_ids' => 'property_category',
                'action_ids'   => 'property_action_category',
                'city_ids'     => 'property_city',
                'area_ids'     => 'property_area',
                'state_ids'    => 'property_county_state',
                'status_ids'   => 'property_status',
            );
            foreach ( $taxonomy_map as $attr_key => $param_key ) {
                if ( ! empty( $attributes[ $attr_key ] ) ) {
                    $ids = is_array( $attributes[ $attr_key ] )
                        ? $attributes[ $attr_key ]
                        : array_map( 'trim', explode( ',', $attributes[ $attr_key ] ) );
                    $ids = array_filter( $ids, function( $v ) { return '' !== $v; } );
                    if ( ! empty( $ids ) ) {
                        $params[ $param_key ] = array_map( 'intval', $ids );
                    }
                }
            }

            // Ordering — random pick overrides sort_by.
            $random_pick = isset( $attributes['random_pick'] ) ? $attributes['random_pick'] : 'no';
            $params['order'] = ( 'yes' === $random_pick ) ? 99 : intval( $attributes['sort_by'] );

            // Posts per page.
            $params['posts_per_page'] = intval( $attributes['number'] );

            // Featured-only filter via meta_query.
            if ( isset( $attributes['show_featured_only'] ) && 'yes' === $attributes['show_featured_only'] ) {
                $params['meta_query']   = isset( $params['meta_query'] ) ? $params['meta_query'] : array();
                $params['meta_query'][] = array(
                    'key'     => 'prop_featured',
                    'value'   => 1,
                    'compare' => '=',
                );
            }

            // Run query through the 2026 layer.
            $result       = wpestate_query_2026_property( 'directory', $params );
            $recent_posts = $result['query'];
        } else {
            // Agents and blog posts — legacy query path.
            $args = wpestate_recent_posts_shortocodes_create_arg($shortcode_arguments);
            $recent_posts = new WP_Query($args);
        }
        $count = 1;


        ob_start();
        $rand_class='slider_no'.rand(0,99999);

        print '<div class="shortcode_slider_list   '.$rand_class.' arrow_class_'.$shortcode_arguments['arrows'].'"   data-items-per-row="'.intval($attributes['items_per_row_visible']).'"  '.$is_autoscroll.'>';

        if ($shortcode_arguments['type'] == 'properties') {
            //display property list as html

            $attributes['rownumber']=$attributes['items_per_row_visible'];
            wpresidence_display_property_list_as_html($recent_posts,array()  ,'shortcode_slider_list',$attributes);
     
        }else if ($shortcode_arguments['type'] == 'agents') {
            //display agent list as html
            wpresidence_display_agent_list_as_html($recent_posts, 'estate_agent', array() ,'shortcode',$attributes);
                 
        } else{
            //display blog list as html
            wpresidence_display_blog_list_as_html($recent_posts, array(), $context = 'shortcode',$shortcode_arguments);
      
        }



        $templates = ob_get_contents();
        ob_end_clean();
        if(function_exists('wpestate_set_transient_cache')){
            wpestate_set_transient_cache ($transient_name,wpestate_html_compress($templates),4*60*60);
        }
    }



    $return_string .=$templates;
    $return_string .= '</div></div>';// end shrcode wrapper
    $return_string .= '</div>';

    $return_string .= '
    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready(function(){
           wpestate_enble_slick_slider_list();
        });
        //]]>
    </script>';


    return $return_string;


}
endif; // end   wpestate_slider_recent_posts_pictures
