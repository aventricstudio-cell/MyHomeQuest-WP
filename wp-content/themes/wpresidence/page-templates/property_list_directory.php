<?php
/** MILLDONE
 * Template Name: Properties List Directory
 * src: page-templates\property_list_directory.php
 * This file is part of the WpResidence theme and requires the WpResidence Core Plugin.
 * It displays a directory of property listings with various filtering options.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0
 *
 *
 */

// Exit if WpResidence Core Plugin is not active
if (!function_exists('wpestate_residence_functionality')) {
    esc_html_e('This page will not work without WpResidence Core Plugin. Please activate it from the plugins menu!', 'wpresidence');
    exit();
}

get_header();

// Retrieve options and initialize variables
$wpestate_options = get_query_var('wpestate_options');
$filtred = 0;
$compare_submit = wpestate_get_template_link('page-templates/compare_listings.php');
$prop_no = intval(wpresidence_get_option('wp_estate_prop_no', ''));
$wpestate_prop_unit                  =   esc_html ( wpresidence_get_option('wp_estate_prop_unit','') );
// Initialize taxonomy variables
$taxonomy = 'property_action_category';
$tax_terms = wpestate_get_cached_terms($taxonomy); 
$taxonomy_cat = 'property_category';
$categories = wpestate_get_cached_terms($taxonomy_cat);

// Retrieve filter values from post meta
$current_adv_filter_search_action = get_post_meta($post->ID, 'adv_filter_search_action', true);
$current_adv_filter_search_category = get_post_meta($post->ID, 'adv_filter_search_category', true);
$current_adv_filter_area = get_post_meta($post->ID, 'current_adv_filter_area', true);
$current_adv_filter_city = get_post_meta($post->ID, 'current_adv_filter_city', true);
$current_adv_filter_county = get_post_meta($post->ID, 'current_adv_filter_county', true);

$show_featured_only = get_post_meta($post->ID, 'show_featured_only', true);
$show_filter_area = get_post_meta($post->ID, 'show_filter_area', true);

// Initialize arrays for taxonomy queries
$area_array = $city_array = $action_array = $categ_array = $county_array = '';

// Process action filter
$tax_action_picked = '';
if (!empty($current_adv_filter_search_action) && $current_adv_filter_search_action[0] != 'all') {
    $taxcateg_include = array();

    foreach ($current_adv_filter_search_action as $value) {
        $taxcateg_include[] = sanitize_title($value);
        $tax_action_picked = empty($tax_action_picked) ? $value : $tax_action_picked . ',' . $value;
    }

    $categ_array = array(
        'taxonomy' => 'property_action_category',
        'field' => 'slug',
        'terms' => $taxcateg_include
    );
    
    $current_adv_filter_search_label = $current_adv_filter_search_action[0];
} else {
    $current_adv_filter_search_label = esc_html__('Types', 'wpresidence');
}

// Process category filter
$tax_categ_picked = '';
if (!empty($current_adv_filter_search_category) && $current_adv_filter_search_category[0] != 'all') {
    $taxaction_include = array();

    foreach ($current_adv_filter_search_category as $value) {
        $taxaction_include[] = sanitize_title($value);
        $tax_categ_picked = empty($tax_categ_picked) ? $value : $tax_categ_picked . ',' . $value;
    }

    $action_array = array(
        'taxonomy' => 'property_category',
        'field' => 'slug',
        'terms' => $taxaction_include
    );
    
    $current_adv_filter_category_label = $current_adv_filter_search_category[0];
} else {
    $current_adv_filter_category_label = esc_html__('Categories', 'wpresidence');
}

// Process city filter
$tax_city_picked = '';
if (!empty($current_adv_filter_city) && $current_adv_filter_city[0] != 'all') {
    $taxaction_include = array();

    foreach ($current_adv_filter_city as $value) {
        $taxaction_include[] = sanitize_title($value);
        $tax_city_picked = empty($tax_city_picked) ? $value : $tax_city_picked . ',' . $value;
    }

    $city_array = array(
        'taxonomy' => 'property_city',
        'field' => 'slug',
        'terms' => $taxaction_include
    );
    
    $current_adv_filter_city_label = $current_adv_filter_city[0];
} else {
    $current_adv_filter_city_label = esc_html__('Cities', 'wpresidence');
}

// Process area filter
$taxa_area_picked = '';
if (!empty($current_adv_filter_area) && $current_adv_filter_area[0] != 'all') {
    $taxaction_include = array();

    foreach ($current_adv_filter_area as $value) {
        $taxaction_include[] = sanitize_title($value);
        $taxa_area_picked = empty($taxa_area_picked) ? $value : $taxa_area_picked . ',' . $value;
    }

    $area_array = array(
        'taxonomy' => 'property_area',
        'field' => 'slug',
        'terms' => $taxaction_include
    );
    
    $current_adv_filter_area_label = $current_adv_filter_area[0];
} else {
    $current_adv_filter_area_label = esc_html__('Areas', 'wpresidence');
}

// Process county filter
if (!empty($current_adv_filter_county) && $current_adv_filter_county[0] != 'all') {
    $taxaction_include = array();
    $taxa_area_picked = '';

    foreach ($current_adv_filter_county as $value) {
        $taxaction_include[] = sanitize_title($value);
        $taxa_area_picked = empty($taxa_area_picked) ? $value : $taxa_area_picked . ',' . $value;
    }

    $county_array = array(
        'taxonomy' => 'property_county_state',
        'field' => 'slug',
        'terms' => $taxaction_include
    );
}

// Setup meta query for featured properties
$meta_query = array();
if ($show_featured_only == 'yes') {
    $meta_query[] = array(
        'key' => 'prop_featured',
        'value' => 1,
        'type' => 'numeric',
        'compare' => '='
    );
}

// Apply default slider filters for size, lot size, rooms, bedrooms, and bathrooms
$dir_min_size      = floatval(get_post_meta($post->ID, 'dir_min_size', true));
$dir_max_size      = floatval(get_post_meta($post->ID, 'dir_max_size', true));
$dir_min_lot_size  = floatval(get_post_meta($post->ID, 'dir_min_lot_size', true));
$dir_max_lot_size  = floatval(get_post_meta($post->ID, 'dir_max_lot_size', true));
$dir_rooms_min     = floatval(get_post_meta($post->ID, 'dir_rooms_min', true));
$dir_rooms_max     = floatval(get_post_meta($post->ID, 'dir_rooms_max', true));
$dir_bedrooms_min  = floatval(get_post_meta($post->ID, 'dir_bedrooms_min', true));
$dir_bedrooms_max  = floatval(get_post_meta($post->ID, 'dir_bedrooms_max', true));
$dir_bathrooms_min = floatval(get_post_meta($post->ID, 'dir_bathrooms_min', true));
$dir_bathrooms_max = floatval(get_post_meta($post->ID, 'dir_bathrooms_max', true));

if ($dir_min_size != 0 || $dir_max_size != 0) {
    $meta_query[] = array(
        'key'     => 'property_size',
        'value'   => array(
            wpestate_convert_measure($dir_min_size, 1),
            wpestate_convert_measure($dir_max_size, 1)
        ),
        'type'    => 'numeric',
        'compare' => 'BETWEEN'
    );
}

if ($dir_min_lot_size != 0 || $dir_max_lot_size != 0) {
    $meta_query[] = array(
        'key'     => 'property_lot_size',
        'value'   => array(
            wpestate_convert_measure($dir_min_lot_size, 1),
            wpestate_convert_measure($dir_max_lot_size, 1)
        ),
        'type'    => 'numeric',
        'compare' => 'BETWEEN'
    );
}

if ($dir_rooms_min != 0 || $dir_rooms_max != 0) {
    $meta_query[] = array(
        'key'     => 'property_rooms',
        'value'   => array($dir_rooms_min, $dir_rooms_max),
        'type'    => 'numeric',
        'compare' => 'BETWEEN'
    );
}

if ($dir_bedrooms_min != 0 || $dir_bedrooms_max != 0) {
    $meta_query[] = array(
        'key'     => 'property_bedrooms',
        'value'   => array($dir_bedrooms_min, $dir_bedrooms_max),
        'type'    => 'numeric',
        'compare' => 'BETWEEN'
    );
}

if ($dir_bathrooms_min != 0 || $dir_bathrooms_max != 0) {
    $meta_query[] = array(
        'key'     => 'property_bathrooms',
        'value'   => array($dir_bathrooms_min, $dir_bathrooms_max),
        'type'    => 'numeric',
        'compare' => 'BETWEEN'
    );
}

// Setup ordering
$meta_directions = 'DESC';
$meta_order = 'prop_featured';
$order = get_post_meta($post->ID, 'listing_filter', true);

if (isset($_GET['order']) && is_numeric($_GET['order'])) {
    $order = intval($_GET['order']);
}

$order_array = wpestate_create_query_order_by_array($order);

// Prepare WP_Query arguments
$args = array(
    'post_type' => 'estate_property',
    'post_status' => 'publish',
    'paged' => 1,
    'posts_per_page' => $prop_no,
    'orderby' => 'meta_value_num',
    'meta_key' => $meta_order,
    'order' => $meta_directions,
    'meta_query' => $meta_query,
    'tax_query' => array(
        'relation' => 'AND',
        $categ_array,
        $action_array,
        $city_array,
        $area_array,
        $county_array
    )
);

$args = array_merge($args, $order_array['order_array']);

// Execute the query
if ($order == 0) {
    $prop_selection = wpestate_return_filtered_by_order($args);
} else {
    $prop_selection = new WP_Query($args);
}

// Include the appropriate template file
include(locate_template('templates/directory_page_templates/normal_directory.php'));

get_footer();
?>