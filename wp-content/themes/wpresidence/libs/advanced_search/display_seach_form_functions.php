<?php
/**
 * Render advanced search fields
 *
 * @param array $adv_search_what Array of search fields
 * @param array $action_select_list Action select options
 * @param array $categ_select_list Category select options
 * @param array $select_city_list City select options
 * @param array $select_area_list Area select options
 * @param array $select_county_state_list County/State select options
 */
function wpestate_render_advanced_search_fields($adv_search_what, $action_select_list, $categ_select_list, $select_city_list, $select_area_list, $select_county_state_list, $search_type = 'type1') {
    $adv_search_fields_no_per_row   = floatval(wpresidence_get_option('wp_estate_search_fields_no_per_row'));
    $adv_search_label               = wpresidence_get_option('wp_estate_adv_search_label', '');

    foreach ($adv_search_what as $key => $search_field) {
        if($search_field!=='none'){
            wpestate_render_search_field($key, $search_field, $adv_search_label, $action_select_list, $categ_select_list, $select_city_list, $select_area_list, $select_county_state_list, $adv_search_fields_no_per_row, $search_type );
        }
    }


    if (wpresidence_get_option('wp_estate_show_adv_search_extended', '') == 'yes') {
        show_extended_search('adv');
    }
}

/**
 * Render individual search field
 *
 * @param int $key Field key
 * @param string $search_field Search field type
 * @param array $adv_search_label Search field labels
 * @param array $action_select_list Action select options
 * @param array $categ_select_list Category select options
 * @param array $select_city_list City select options
 * @param array $select_area_list Area select options
 * @param array $select_county_state_list County/State select options
 * @param int $adv_search_fields_no_per_row Number of fields per row
 */
function wpestate_render_search_field($key, $search_field, $adv_search_label, $action_select_list, $categ_select_list, $select_city_list, $select_area_list, $select_county_state_list, $adv_search_fields_no_per_row, $search_type = 'type1') {
    $search_col = 3;
    $search_col_price = 6;

    if ($adv_search_fields_no_per_row == 2) {
        $search_col = 6;
        $search_col_price = 12;
    } elseif ($adv_search_fields_no_per_row == 3) {
        $search_col = 4;
        $search_col_price = 8;
    }

    if( $search_type==='type3'){
        $search_col=6;
        $search_col_price=12;
    }

    if ($search_field == 'property price' && wpresidence_get_option('wp_estate_show_slider_price', '') == 'yes') {
        $search_col = $search_col_price;
    }

    echo '<div class="col-md-' . esc_attr($search_col) . ' ' . str_replace(" ", "_", $search_field) . '">';
    wpestate_show_search_field($adv_search_label[$key], 'mainform', $search_field, $action_select_list, $categ_select_list, $select_city_list, $select_area_list, $key, $select_county_state_list);
    echo '</div>';
}