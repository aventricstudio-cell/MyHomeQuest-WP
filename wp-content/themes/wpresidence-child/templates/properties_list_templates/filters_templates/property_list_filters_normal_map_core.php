<?php
/** MILLDONE
 * Property List Filters Normal Map Core Template
 * src: templates\properties_list_templates\filters_templates\property_list_filters_normal_map_core.php
 * This template handles the display of property filters for the normal map view.
 *
 * @package WpResidence
 * @subpackage PropertyListings
 * @since WpResidence 1.0
 */

$listings_list     =    '';
$current_adv_filter_search_meta     =   'Types';
$current_adv_filter_category_meta   =   'Categories';
$current_adv_filter_city_meta       =   'Cities';
$current_adv_filter_area_meta       =   'Areas';
$current_adv_filter_county_meta     =   'States';
$selected_order_num                 =   '';

$selected_order         =   esc_html__('Sort by','wpresidence');

$show_filter_area       =   get_post_meta($post->ID, 'show_filter_area', true);

if(is_page_template('page-templates/property_list.php')){
    $post_id = $post->ID;

    $filter_search_action = wpresidence_process_filter_labels($post_id, 'adv_filter_search_action', 'Types');
    $filter_search_category = wpresidence_process_filter_labels($post_id, 'adv_filter_search_category', 'Categories');
    $filter_county = wpresidence_process_filter_labels($post_id, 'current_adv_filter_county', 'States');
    $filter_area = wpresidence_process_filter_labels($post_id, 'current_adv_filter_area', 'Areas');
    $filter_city = wpresidence_process_filter_labels($post_id, 'current_adv_filter_city', 'Cities');

    $current_adv_filter_search_label = $filter_search_action['label'];
    $current_adv_filter_search_meta = $filter_search_action['meta'];
    $current_adv_filter_category_label = $filter_search_category['label'];
    $current_adv_filter_category_meta = $filter_search_category['meta'];
    $current_adv_filter_county_label = $filter_county['label'];
    $current_adv_filter_county_meta = $filter_county['meta'];
    $current_adv_filter_area_label = $filter_area['label'];
    $current_adv_filter_area_meta = $filter_area['meta'];
    $current_adv_filter_city_label = $filter_city['label'];
    $current_adv_filter_city_meta = $filter_city['meta'];
}

$args_local = wpestate_get_select_arguments();




if($show_filter_area == 'yes') {
    $action_select_list =   wpestate_get_action_select_list($args_local);
    $categ_select_list  =   wpestate_get_category_select_list($args_local);
    $select_county_list =   wpestate_get_county_state_select_list($args_local);
    $select_city_list   =   wpestate_get_city_select_list($args_local);
    $select_area_list   =   wpestate_get_area_select_list($args_local);

    ?>
    
    <div class="listing_filters_head">
        <input type="hidden" id="page_idx" value="<?php echo intval($post->ID); ?>">

        <?php 
        echo wpestate_build_dropdown_for_filters('a_filter_action', $current_adv_filter_search_meta, $current_adv_filter_search_label, $action_select_list);
        echo wpestate_build_dropdown_for_filters('a_filter_categ', $current_adv_filter_category_meta, $current_adv_filter_category_label, $categ_select_list);
        // echo wpestate_build_dropdown_for_filters('a_filter_county', $current_adv_filter_county_meta, $current_adv_filter_county_label, $select_county_list);
        echo wpestate_build_dropdown_for_filters('a_filter_cities', $current_adv_filter_city_meta, $current_adv_filter_city_label, $select_city_list);
        // echo wpestate_build_dropdown_for_filters('a_filter_areas', $current_adv_filter_area_meta, $current_adv_filter_area_label, $select_area_list);
       //display the orderby dropdown menu for property listings.
       wpresidence_display_orderby_dropdown($post->ID);

        $prop_unit_list_class = '';
        $prop_unit_grid_class = 'icon_selected';
        if(  esc_html ( wpresidence_get_option('wp_estate_prop_unit','') )== 'list'){
            $prop_unit_grid_class = "";
            $prop_unit_list_class = "icon_selected";
        }
        ?>
        <div class="wpestate_list_grid_filter_wiew_wrapper">
          

            <div class="listing_filter_select listing_filter_views grid_filter_wiew">
                <div id="grid_view" class="<?php echo esc_attr($prop_unit_grid_class); ?>">
                <i class="fa-solid fa-grip-vertical"></i>
                </div>
            </div>

            <div class="listing_filter_select listing_filter_views list_filter_wiew">
                <div id="list_view" class="<?php echo esc_attr($prop_unit_list_class); ?>">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </div>
        
    </div>

<?php 
} else {
?>
    <div data-toggle="dropdown" id="a_filter_action" class="" data-value="<?php echo esc_attr($current_adv_filter_search_meta); ?>"></div>
    <div data-toggle="dropdown" id="a_filter_categ" class="" data-value="<?php echo esc_attr($current_adv_filter_category_meta); ?>"></div>
    <div data-toggle="dropdown" id="a_filter_cities" class="" data-value="<?php echo esc_attr($current_adv_filter_city_meta); ?>"></div>
    <div data-toggle="dropdown" id="a_filter_areas" class="" data-value="<?php echo esc_attr($current_adv_filter_area_meta); ?>"></div>
    <div data-toggle="dropdown" id="a_filter_county" class="" data-value="<?php echo esc_attr($current_adv_filter_county_meta); ?>"></div>
<?php
}
?>