<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */


if ( !function_exists("wpestate_advanced_filter_list_properties") ):
function wpestate_advanced_filter_list_properties($attributes, $content = null){

    $category_ids='';
    if(isset($attributes['category_ids'])){
        $category_ids       =   $attributes['category_ids'];
    }

    $action_ids='';
    if(isset($attributes['action_ids'])){
        $action_ids       =   $attributes['action_ids'];
    }

    $city_ids='';
    if(isset($attributes['city_ids'])){
        $city_ids       =   $attributes['city_ids'];
    }

    $area_ids='';
    if(isset($attributes['area_ids'])){
        $area_ids       =   $attributes['area_ids'];
    }

    $state_ids='';
    if(isset($attributes['state_ids'])){
        $state_ids      =   $attributes['state_ids'];
    }
    
    $sort_by='';
    if(isset($attributes['sort_by'])){
        $sort_by    =   $attributes['sort_by'];
    }

    $align='';
    if(isset($attributes['align'])){
        $align=$attributes['align'];
    }

    $city       =   wpestate_return_first_term($city_ids,'property_city');
    $area       =   wpestate_return_first_term($area_ids,'property_area');
    $category   =   wpestate_return_first_term($category_ids,'property_category');
    $types      =   wpestate_return_first_term($action_ids,'property_action_category');
    $county     =   wpestate_return_first_term($state_ids,'property_county_state');

    // build filters
    $filter_selection=array(
        'city'      =>  $city,
        'area'      =>  $area,
        'types'     =>  $types,
        'category'  =>  $category,
        'county'    =>  $county,
        'sort_by'   =>  $sort_by

    );

    $filter_data                    =   wpestate_return_filter_data( $filter_selection );
    $filter_data['sort_by']=$sort_by;
    $filter_data['listing_filter']  =   0;
    $return_string                  =  'aaa'. wpestate_advanced_filter_bar($filter_data);
    $attributes['type']             =   'properties';

    $card_version='';
    if(isset($attributes['card_version']))$card_version=$attributes['card_version'];

    $return_string.= '<div class="wpestate_filter_list_properties_wrapper" data-ishortcode="1" data-number="'.$attributes['number'].'"  data-rownumber="'.$attributes['rownumber'].'" data-card_version="'.esc_attr($card_version).'" data-align="'.esc_attr($align).'">'.wpestate_shortcode_build_list($attributes).'
        <div class="spinner" id="listing_loader2">
            <div class="new_prelader"></div>
        </div>
    </div>';
    return $return_string;
}
endif;





/*
*
 * 
 * 
 * 
 * 
 **/



if( !function_exists('wpestate_advanced_filter_bar') ):
function wpestate_advanced_filter_bar($filter_data){
    $return_string='';
    ob_start();
    $is_shortcode='yes';

    include( locate_template('templates/advanced_property_list_filters.php') );
    $filters = ob_get_contents();
    ob_end_clean();

    $return_string.=$filters;
    return $return_string;
}
endif;
