<?php
/**
 * Half map filter template
 *
 * Renders the half-map header controls, including map/list toggles and sort dropdowns.
 * Adjustments keep the map view selected by default while preserving grid/list synchrony.
 */
$order_class            =   ' order_filter_single ';
$selected_order         =   esc_html__('Sort by','wpresidence');
$listing_filter         =   '';

if( is_tax() ){
    $listing_filter =  intval(wpresidence_get_option('wp_estate_property_list_type_tax_order',''));
}else if( isset($post->ID) ){
    if(is_page_template( 'page-templates/advanced_search_results.php' ) ){
        $listing_filter =  intval(wpresidence_get_option('wp_estate_property_list_type_adv_order',''));
    }else{
        $listing_filter         = get_post_meta($post->ID, 'listing_filter',true );
    }
}
  



$listing_filter_array   = wpestate_listings_sort_options_array();
$half_map_default_view  = wpresidence_get_option('wp_estate_half_map_search_map_type', 'grid');
$is_half_map_list_view  = ($half_map_default_view === 'list');

$listings_list='';
$selected_order_num='';
foreach($listing_filter_array as $key=>$value){
    $listings_list.= '<li role="presentation" data-value="'.esc_html($key).'">'.esc_html($value).'</li>';//escaped above

    if($key==$listing_filter){
        $selected_order     =   $value;
        $selected_order_num =   $key;
    }
} 
?>

<div class="wpresidence_half_map_filter_wrapper row  gx-2 gy-2" data-half-map-default-view="<?php echo esc_attr($half_map_default_view); ?>">


    <div class="col-lg-6 col-md-6  col-sm-12 ">
        <div class="half_map_toggle_group">
        <div id="wperesidence_half_map_list_view" class="half_map_toggle <?php echo esc_attr($is_half_map_list_view ? 'half_map_selected' : ''); ?>"><?php esc_html_e('List View','wpresidence');?></div>
        <div id="wperesidence_half_map_view" class="half_map_toggle <?php echo esc_attr($is_half_map_list_view ? '' : 'half_map_selected'); ?>"><?php esc_html_e('Map View','wpresidence');?></div>
        </div>
    </div>

    
    <div class="col-lg-3 col-md-3  col-sm-12 ">
        <?php
        $prop_unit_list_class    =   '';
        $prop_unit_grid_class    =   'icon_selected';
        if($wpestate_prop_unit=='list'){
            $prop_unit_grid_class="";
            $prop_unit_list_class="icon_selected";
        }

        ?>

        <?php
        $current_adv_filter_county_meta = $current_adv_filter_county_meta ?? '';
        wpestate_render_list_grid_toggle($prop_unit_grid_class, $prop_unit_list_class, $current_adv_filter_county_meta);
        ?>
    </div>


    <div class="col-lg-3 col-md-3  col-sm-12   ">

        <?php
            print wpestate_build_dropdown_for_filters('a_filter_order',$selected_order_num,$selected_order,$listings_list );
        ?>

    </div>
</div>