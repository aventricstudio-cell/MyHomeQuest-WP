<?php
/** MILLDONE
 * Property List Filters Template
 * src: templates\properties_list_templates\property_list_filters.php
 * This template generates and displays various filters for property listings.
 * It handles different scenarios such as shortcodes, taxonomies, and property list pages.
 *
 * @package WpResidence
 * @subpackage PropertyListings
 * @since WpResidence 1.0
 */

// Initialize variables
$listing_filter         = '';
$current_name           = '';
$current_slug           = '';
$listings_list          = '';
$show_filter_area       = '';
$selected_order_num     = '';

// Set default filter values
$current_adv_filter_search_meta     = 'Types';
$current_adv_filter_category_meta   = 'Categories';
$current_adv_filter_city_meta       = 'Cities';
$current_adv_filter_area_meta       = 'Areas';
$current_adv_filter_county_meta     = 'States';

$current_adv_filter_status_meta     = 'Statuses';
$current_adv_filter_features_meta   = 'Features';
$current_adv_filter_status_label    = esc_html__('Statuses', 'wpresidence');
$current_adv_filter_features_label  = esc_html__('Features', 'wpresidence');

$selected_order         = esc_html__('Sort by', 'wpresidence');
$listing_filter_array   = wpestate_listings_sort_options_array();

// Handle shortcode scenario
if (isset($is_shortcode)) {
    $show_filter_area = 'yes';
    
    // Set filter values from shortcode data
    $current_adv_filter_category_label  = $filter_data['category']['label'];
    $current_adv_filter_category_meta   = $filter_data['category']['meta'];
    $current_adv_filter_search_label    = $filter_data['types']['label'];
    $current_adv_filter_search_meta     = $filter_data['types']['meta'];
    $current_adv_filter_county_label    = $filter_data['county']['label'];
    $current_adv_filter_county_meta     = $filter_data['county']['meta'];
    $current_adv_filter_city_label      = $filter_data['city']['label'];
    $current_adv_filter_city_meta       = $filter_data['city']['meta'];
    $current_adv_filter_area_label      = $filter_data['area']['label'];
    $current_adv_filter_area_meta       = $filter_data['area']['meta'];
    if (isset($filter_data['status'])) {
        $current_adv_filter_status_label    = $filter_data['status']['label'];
        $current_adv_filter_status_meta     = $filter_data['status']['meta'];
    }
    if (isset($filter_data['features'])) {
        $current_adv_filter_features_label  = $filter_data['features']['label'];
        $current_adv_filter_features_meta   = $filter_data['features']['meta'];
    }
    $selected_order                     = $listing_filter_array[$filter_data['sort_by']];
    $selected_order_num                 = $filter_data['sort_by'];
}

// Check if filter area should be shown
if (isset($post->ID)) {
    $show_filter_area = get_post_meta($post->ID, 'show_filter_area', true);
}

// Handle taxonomy scenario
if (is_tax()) {
    $show_filter_area = 'yes';
    
    // Set default labels for taxonomy filters
    $current_adv_filter_search_label    = esc_html__('Types', 'wpresidence');
    $current_adv_filter_category_label  = esc_html__('Categories', 'wpresidence');
    $current_adv_filter_city_label      = esc_html__('Cities', 'wpresidence');
    $current_adv_filter_area_label      = esc_html__('Areas', 'wpresidence');
    $current_adv_filter_county_label    = esc_html__('States', 'wpresidence');
    $current_adv_filter_status_label    = esc_html__('Statuses', 'wpresidence');
    $current_adv_filter_features_label  = esc_html__('Features', 'wpresidence');

    $taxonomy = get_query_var('taxonomy');
    $term     = single_cat_title('', false);

    // Set filter values based on current taxonomy
    switch ($taxonomy) {
        case 'property_city':
            $current_adv_filter_city_label  = ucwords(str_replace('-', ' ', $term));
            $current_adv_filter_city_meta   = sanitize_title($term);
            break;
        case 'property_area':
            $current_adv_filter_area_label  = ucwords(str_replace('-', ' ', $term));
            $current_adv_filter_area_meta   = sanitize_title($term);
            break;
        case 'property_category':
            $current_adv_filter_category_label  = ucwords(str_replace('-', ' ', $term));
            $current_adv_filter_category_meta   = sanitize_title($term);
            break;
        case 'property_action_category':
            $current_adv_filter_search_label    = ucwords(str_replace('-', ' ', $term));
            $current_adv_filter_search_meta     = sanitize_title($term);
            break;
        case 'property_county_state':
            $current_adv_filter_county_label    = ucwords(str_replace('-', ' ', $term));
            $current_adv_filter_county_meta     = sanitize_title($term);
            break;
        case 'property_status':
            $current_adv_filter_status_label   = ucwords(str_replace('-', ' ', $term));
            $current_adv_filter_status_meta    = sanitize_title($term);
            break;
        case 'property_features':
            $current_adv_filter_features_label = ucwords(str_replace('-', ' ', $term));
            $current_adv_filter_features_meta  = sanitize_title($term);
            break;
    }
}

// Handle property list page scenario
if (is_page_template('page-templates/property_list.php')) {
    $current_adv_filter_search_action = get_post_meta($post->ID, 'adv_filter_search_action', true);
    $current_adv_filter_search_category = get_post_meta($post->ID, 'adv_filter_search_category', true);
    $current_adv_filter_area = get_post_meta($post->ID, 'current_adv_filter_area', true);
    $current_adv_filter_city = get_post_meta($post->ID, 'current_adv_filter_city', true);

    // Set filter values based on page meta
    $current_adv_filter_search_label = (empty($current_adv_filter_search_action[0]) || $current_adv_filter_search_action[0] == 'all') ? esc_html__('Types', 'wpresidence') : ucwords(str_replace('-', ' ', $current_adv_filter_search_action[0]));
    $current_adv_filter_search_meta = (empty($current_adv_filter_search_action[0]) || $current_adv_filter_search_action[0] == 'all') ? 'Types' : sanitize_title($current_adv_filter_search_action[0]);

    $current_adv_filter_category_label = (empty($current_adv_filter_search_category[0]) || $current_adv_filter_search_category[0] == 'all') ? esc_html__('Categories', 'wpresidence') : ucwords(str_replace('-', ' ', $current_adv_filter_search_category[0]));
    $current_adv_filter_category_meta = (empty($current_adv_filter_search_category[0]) || $current_adv_filter_search_category[0] == 'all') ? 'Categories' : sanitize_title($current_adv_filter_search_category[0]);

    $current_adv_filter_area_label = (empty($current_adv_filter_area[0]) || $current_adv_filter_area[0] == 'all') ? esc_html__('Areas', 'wpresidence') : ucwords(str_replace('-', ' ', $current_adv_filter_area[0]));
    $current_adv_filter_area_meta = (empty($current_adv_filter_area[0]) || $current_adv_filter_area[0] == 'all') ? 'Areas' : sanitize_title($current_adv_filter_area[0]);

    $current_adv_filter_city_label = (empty($current_adv_filter_city[0]) || $current_adv_filter_city[0] == 'all') ? esc_html__('Cities', 'wpresidence') : ucwords(str_replace('-', ' ', $current_adv_filter_city[0]));
    $current_adv_filter_city_meta = (empty($current_adv_filter_city[0]) || $current_adv_filter_city[0] == 'all') ? 'Cities' : sanitize_title($current_adv_filter_city[0]);
}

// Get listing filter
if (is_tax()) {
    $listing_filter = intval(wpresidence_get_option('wp_estate_property_list_type_tax_order', ''));
} elseif (isset($post->ID)) {
    if (is_page_template('page-templates/advanced_search_results.php')) {
        $listing_filter = intval(wpresidence_get_option('wp_estate_property_list_type_adv_order', ''));
    } else {
        $listing_filter = get_post_meta($post->ID, 'listing_filter', true);
    }
}

$args_local = wpestate_get_select_arguments();

// Generate listings list options
foreach ($listing_filter_array as $key => $value) {
    $listings_list .= '<li role="presentation" data-value="' . esc_html($key) . '">' . esc_html($value) . '</li>';
    if ($key == $listing_filter) {
        $selected_order = $value;
        $selected_order_num = $key;
    }
}

$order_class = '';
if ($show_filter_area != 'yes') {
    $order_class = ' order_filter_single ';
}

// Display filters if show_filter_area is 'yes'
if ($show_filter_area == 'yes') {
    if (is_tax()) {
        $curent_term    = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
        $current_slug   = $curent_term->slug;
        $current_name   = $curent_term->name;
        $current_tax    = $curent_term->taxonomy;
    }

    $action_select_list = wpestate_get_action_select_list($args_local);
    $categ_select_list  = wpestate_get_category_select_list($args_local);
    $select_county_list = wpestate_get_county_state_select_list($args_local);
    $select_city_list   = wpestate_get_city_select_list($args_local);
    $select_area_list   = wpestate_get_area_select_list($args_local);
    $status_select_list   = wpestate_get_status_select_list($args_local);
    $features_select_list = wpestate_get_features_select_list($args_local);
  
    if(!isset($post->ID)){
        global $post;
    }
    ?>
    
    <!-- Display filter dropdowns -->
    <div class="listing_filters_head">
     
        <input type="hidden" id="page_idx" value="<?php echo (!is_tax() && !is_category() && isset($post->ID)) ? intval($post->ID) : ''; ?>">
        <?php 
        // Generate and display filter dropdowns
        echo wpestate_build_dropdown_for_filters('a_filter_action', $current_adv_filter_search_meta, $current_adv_filter_search_label, $action_select_list);
        echo wpestate_build_dropdown_for_filters('a_filter_categ', $current_adv_filter_category_meta, $current_adv_filter_category_label, $categ_select_list);
        echo wpestate_build_dropdown_for_filters('a_filter_county', $current_adv_filter_county_meta, $current_adv_filter_county_label, $select_county_list);
        echo wpestate_build_dropdown_for_filters('a_filter_cities', $current_adv_filter_city_meta, $current_adv_filter_city_label, $select_city_list);
        echo wpestate_build_dropdown_for_filters('a_filter_areas', $current_adv_filter_area_meta, $current_adv_filter_area_label, $select_area_list);


        if($current_adv_filter_status_meta=='Statuses'){
            $current_adv_filter_status_meta='';
        }
  
        echo wpestate_build_dropdown_for_filters('a_filter_status', $current_adv_filter_status_meta, $current_adv_filter_status_label, $status_select_list);
       
        if($current_adv_filter_features_meta=='Features'){
            $current_adv_filter_features_meta='';
        }
       
        echo wpestate_build_dropdown_for_filters('a_filter_features', $current_adv_filter_features_meta, $current_adv_filter_features_label, $features_select_list);
        
     
        echo wpestate_build_dropdown_for_filters('a_filter_order', $selected_order_num, $selected_order, $listings_list);
        ?>

        <?php
        // Set up grid/list view toggles
        $prop_unit_list_class = '';
        $prop_unit_grid_class = 'icon_selected';
      
        ?>

        <?php wpestate_render_list_grid_toggle($prop_unit_grid_class, $prop_unit_list_class, $current_adv_filter_county_meta); ?>
    </div>
<?php
} else {
    // Display hidden filter values if show_filter_area is not 'yes'
    ?>
    <div data-toggle="dropdown" id="a_filter_action" class="" data-value="<?php echo esc_attr($current_adv_filter_search_meta); ?>"></div>
    <div data-toggle="dropdown" id="a_filter_categ" class="" data-value="<?php echo esc_attr($current_adv_filter_category_meta); ?>"></div>
    <div data-toggle="dropdown" id="a_filter_cities" class="" data-value="<?php echo esc_attr($current_adv_filter_city_meta); ?>"></div>
    <div data-toggle="dropdown" id="a_filter_areas" class="" data-value="<?php echo esc_attr($current_adv_filter_area_meta); ?>"></div>
    <div data-toggle="dropdown" id="a_filter_county" class="" data-value="<?php echo esc_attr($current_adv_filter_county_meta); ?>"></div>
<?php
}

/**
 * Helper function to set filter values
 *
 * @param string &$label Reference to the label variable
 * @param string &$meta Reference to the meta variable
 * @param string $value The value to check
 * @param string $default_label The default label to use
 */
if(!function_exists('set_filter_values')) {
    function set_filter_values(&$label, &$meta, $value, $default_label) {
        if ($value == 'all' || empty($value)) {
            $label = $default_label;
            $meta = $default_label;
        } else {
            $label = ucwords(str_replace('-', ' ', $value));
            $meta = sanitize_title($value);
        }
    }
}

?>
