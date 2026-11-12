<?php
/** MILLDONE
 * Taxonomy Agent Hidden Filters Template
 * src: templates\properties_list_templates\filters_templates\taxonomy_agent_hidden_filters.php
 * This template generates hidden filter elements for AJAX-based property searches
 * specific to agent taxonomy pages in the WpResidence theme. It sets up filter values
 * based on the current taxonomy and outputs hidden div elements that are used
 * by JavaScript to maintain filter state during AJAX requests.
 *
 * @package WpResidence
 * @subpackage PropertySearch
 * @since WpResidence 1.0
 *
 * @global string $current_adv_filter_search_label Global variable for search filter label
 * @global string $current_adv_filter_category_label Global variable for category filter label
 * @global string $current_adv_filter_city_label Global variable for city filter label
 * @global string $current_adv_filter_area_label Global variable for area filter label
 * @global string $wpestate_prop_unit Global variable for property unit type
 *
 * @uses get_post_meta() to retrieve custom field data
 * @uses get_query_var() to retrieve current taxonomy and term
 * @uses single_cat_title() to get the current category title
 */

global $current_adv_filter_search_label, $current_adv_filter_category_label, $current_adv_filter_city_label, $current_adv_filter_area_label, $wpestate_prop_unit;

// Initialize variables
$current_name      = '';
$current_slug      = '';
$listings_list     = '';
$show_filter_area  = '';

// Check if we're on a post page and retrieve the filter area setting
if (!is_tax()) {
    $show_filter_area = get_post_meta($post->ID, 'show_filter_area', true);
}

// Set default values for filter meta fields
$filter_data = array(
    'search'   => array('label' => esc_html__('Types', 'wpresidence'), 'meta' => 'Types'),
    'category' => array('label' => esc_html__('Categories', 'wpresidence'), 'meta' => 'Categories'),
    'city'     => array('label' => esc_html__('Cities', 'wpresidence'), 'meta' => 'Cities'),
    'area'     => array('label' => esc_html__('Areas', 'wpresidence'), 'meta' => 'Areas'),
    'county'   => array('label' => esc_html__('States', 'wpresidence'), 'meta' => 'States')
);

// Set filter values based on taxonomy if on a taxonomy page
if (is_tax()) {
    $show_filter_area = 'yes';
    $taxonomy = get_query_var('taxonomy');
    $term = single_cat_title('', false);

    switch ($taxonomy) {
        case 'property_city_agent':
            $filter_data['city']['label'] = ucwords(str_replace('-', ' ', $term));
            $filter_data['city']['meta'] = sanitize_title($term);
            break;
        case 'property_area_agent':
            $filter_data['area']['label'] = ucwords(str_replace('-', ' ', $term));
            $filter_data['area']['meta'] = sanitize_title($term);
            break;
        case 'property_category_agent':
            $filter_data['category']['label'] = ucwords(str_replace('-', ' ', $term));
            $filter_data['category']['meta'] = sanitize_title($term);
            break;
        case 'property_action_category_agent':
            $filter_data['search']['label'] = ucwords(str_replace('-', ' ', $term));
            $filter_data['search']['meta'] = sanitize_title($term);
            break;
        case 'property_county_state':
            $filter_data['county']['label'] = ucwords(str_replace('-', ' ', $term));
            $filter_data['county']['meta'] = sanitize_title($term);
            break;
    }
}

// Output hidden div elements for each filter type
?>
<div data-toggle="dropdown" id="second_filter_action" class="d-none" data-value="<?php echo esc_attr($filter_data['search']['meta']); ?>"><?php echo esc_html($filter_data['search']['label']); ?></div>
<div data-toggle="dropdown" id="second_filter_categ" class="d-none" data-value="<?php echo esc_attr($filter_data['category']['meta']); ?>"><?php echo esc_html($filter_data['category']['label']); ?></div>
<div data-toggle="dropdown" id="second_filter_cities" class="d-none" data-value="<?php echo esc_attr($filter_data['city']['meta']); ?>"><?php echo esc_html($filter_data['city']['label']); ?></div>
<div data-toggle="dropdown" id="second_filter_areas" class="d-none" data-value="<?php echo esc_attr($filter_data['area']['meta']); ?>"><?php echo esc_html($filter_data['area']['label']); ?></div>
<div data-toggle="dropdown" id="second_filter_county" class="d-none" data-value="<?php echo esc_attr($filter_data['county']['meta']); ?>"><?php echo esc_html($filter_data['county']['label']); ?></div>