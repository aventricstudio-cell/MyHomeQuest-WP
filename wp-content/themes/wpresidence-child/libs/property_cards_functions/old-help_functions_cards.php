<?php


/**
*
*
* Return sorting options for listings
*
*
*/
if(!function_exists('wpestate_listings_sort_options_array')):
function wpestate_listings_sort_options_array(){

    $listing_filter_array=array(
        "1"=>esc_html__('Price High to Low','wpresidence'),
        "2"=>esc_html__('Price Low to High','wpresidence'),
        "3"=>esc_html__('Newest first','wpresidence'),
        "4"=>esc_html__('Oldest first','wpresidence'),
        "11"=>esc_html__('Newest Edited','wpresidence'),
        "12"=>esc_html__('Oldest Edited ','wpresidence'),
        // "5"=>esc_html__('Bedrooms High to Low','wpresidence'),
        // "6"=>esc_html__('Bedrooms Low to high','wpresidence'),
        // "7"=>esc_html__('Bathrooms High to Low','wpresidence'),
        // "8"=>esc_html__('Bathrooms Low to high','wpresidence'),
        "0"=>esc_html__('Default','wpresidence')
    );
    return $listing_filter_array;
}
endif;








if( !function_exists('wpestate_interior_classes') ):
function wpestate_interior_classes($wpestate_uset_unit){
    $return='';
    if($wpestate_uset_unit==1) {
        $return= 'property_listing_custom_design';
    }
    return $return;
}
endif;





?>
