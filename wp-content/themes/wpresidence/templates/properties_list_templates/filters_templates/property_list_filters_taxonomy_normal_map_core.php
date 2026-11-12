<?php
/** MILLDONE
 * Property List Filters for Taxonomy Normal Map Core
 * src: templates\properties_list_templates\filters_templates\property_list_filters_taxonomy_normal_map_core.php
 * This template handles the display of property filters for taxonomy pages
 * with a normal map layout in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyListings
 * @since WpResidence 1.0
 *
 * @uses wpestate_get_select_arguments() to get select arguments for dropdowns
 * @uses wpestate_get_action_select_list() to get action select options
 * @uses wpestate_get_category_select_list() to get category select options
 * @uses wpestate_get_county_state_select_list() to get county/state select options
 * @uses wpestate_get_city_select_list() to get city select options
 * @uses wpestate_get_area_select_list() to get area select options
 * @uses wpestate_build_dropdown_for_filters() to build filter dropdowns
 * @uses wpresidence_display_orderby_dropdown() to display the orderby dropdown
 *
 * @global object $wp_query WordPress Query object
 * @global string $taxonmy Current taxonomy
 * @global string $term Current taxonomy term
 * @global string $wpestate_prop_unit Property unit display type
 */

// Initialize variables
$listings_list     =    '';
$current_adv_filter_search_meta     =   'Types';
$current_adv_filter_category_meta   =   'Categories';
$current_adv_filter_city_meta       =   'Cities';
$current_adv_filter_area_meta       =   'Areas';
$current_adv_filter_county_meta     =   'States';
$selected_order_num                 =   '';

$selected_order         =   esc_html__('Sort by','wpresidence');
$sort_options_array     =   wpestate_listings_sort_options_array();

// Set default labels for taxonomy filters
if( is_tax() ){
    $show_filter_area = 'yes';
    $current_adv_filter_search_label    =esc_html__('Types','wpresidence');
    $current_adv_filter_category_label  =esc_html__('Categories','wpresidence');
    $current_adv_filter_city_label      =esc_html__('Cities','wpresidence');
    $current_adv_filter_area_label      =esc_html__('Areas','wpresidence');
    $current_adv_filter_county_label    =esc_html__('States','wpresidence');

    $taxonmy = get_query_var('taxonomy');
    $term = single_cat_title('', false);

    // Set filter values based on current taxonomy
    switch ($taxonmy) {
        case 'property_city':
            $current_adv_filter_city_label  =   ucwords( str_replace('-',' ',$term) );
            $current_adv_filter_city_meta   =   sanitize_title($term);
            break;
        case 'property_area':
            $current_adv_filter_area_label  =   ucwords( str_replace('-',' ',$term) );
            $current_adv_filter_area_meta   =   sanitize_title($term);
            break;
        case 'property_category':
            $current_adv_filter_category_label  =   ucwords( str_replace('-',' ',$term) );
            $current_adv_filter_category_meta   =   sanitize_title($term);
            break;
        case 'property_action_category':
            $current_adv_filter_search_label    =   ucwords( str_replace('-',' ',$term) );
            $current_adv_filter_search_meta     =   sanitize_title($term);
            break;
        case 'property_county_state':
            $current_adv_filter_county_label    =   ucwords( str_replace('-',' ',$term) );
            $current_adv_filter_county_meta     =   sanitize_title($term);
            break;
    }
}

// Get select arguments for dropdowns
$args_local = wpestate_get_select_arguments();

// Get current term information for taxonomies
if ( is_tax() ){
    $curent_term    =   get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
    $current_slug   =   $curent_term->slug;
    $current_name   =   $curent_term->name;
    $current_tax    =   $curent_term->taxonomy;
}

// Get select options for various filters
$action_select_list =   wpestate_get_action_select_list($args_local);
$categ_select_list  =   wpestate_get_category_select_list($args_local);
$select_county_list =   wpestate_get_county_state_select_list($args_local);
$select_city_list   =   wpestate_get_city_select_list($args_local);
$select_area_list   =   wpestate_get_area_select_list($args_local);

?>

<div class="listing_filters_head">
    <input type="hidden" id="page_idx" value="">

    <?php
    // Build and display filter dropdowns
    echo wpestate_build_dropdown_for_filters('a_filter_action', $current_adv_filter_search_meta, $current_adv_filter_search_label, $action_select_list);
    echo wpestate_build_dropdown_for_filters('a_filter_categ', $current_adv_filter_category_meta, $current_adv_filter_category_label, $categ_select_list);
    echo wpestate_build_dropdown_for_filters('a_filter_county', $current_adv_filter_county_meta, $current_adv_filter_county_label, $select_county_list);
    echo wpestate_build_dropdown_for_filters('a_filter_cities', $current_adv_filter_city_meta, $current_adv_filter_city_label, $select_city_list);
    echo wpestate_build_dropdown_for_filters('a_filter_areas', $current_adv_filter_area_meta, $current_adv_filter_area_label, $select_area_list);

    // Display the orderby dropdown
    wpresidence_display_orderby_dropdown(0);
    ?>

    <?php
    // Set up classes for list and grid view toggles
    $prop_unit_list_class = '';
    $prop_unit_grid_class = 'icon_selected';
    if($wpestate_prop_unit == 'list'){
        $prop_unit_grid_class = "";
        $prop_unit_list_class = "icon_selected";
    }
    ?>
        <?php wpestate_render_list_grid_toggle($prop_unit_grid_class, $prop_unit_list_class, $current_adv_filter_county_meta, true); ?>
    </div>
