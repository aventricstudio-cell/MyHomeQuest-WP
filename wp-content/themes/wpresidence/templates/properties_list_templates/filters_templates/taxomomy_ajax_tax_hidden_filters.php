<?php
/** MILLDONE
 * Taxonomy Ajax Tax Hidden Filters Template
 * src: templates\properties_list_templates\filters_templates\taxomomy_ajax_tax_hidden_filters.php
 * This template generates hidden filter elements for AJAX-based property searches
 * specific to taxonomy pages in the WpResidence theme. It sets up filter values
 * based on the current taxonomy and outputs hidden div elements that are used
 * by JavaScript to maintain filter state during AJAX requests.
 *
 * @package WpResidence
 * @subpackage PropertySearch
 * @since WpResidence 1.0
 *
 * @uses get_query_var() to retrieve current taxonomy and term
 * @uses single_cat_title() to get the current category title
 */

// Set the filter area to be shown
$show_filter_area = 'yes';

// Initialize default labels and meta values for filters
$filter_data = array(
    'city'     => array('label' => esc_html__('Cities', 'wpresidence'), 'meta' => ''),
    'area'     => array('label' => esc_html__('Areas', 'wpresidence'), 'meta' => ''),
    'category' => array('label' => esc_html__('Categories', 'wpresidence'), 'meta' => ''),
    'action'   => array('label' => esc_html__('Types', 'wpresidence'), 'meta' => ''),
    'county'   => array('label' => esc_html__('States', 'wpresidence'), 'meta' => '')
);

// Function to process labels and meta based on taxonomy
function wpresidence_set_filter_label_meta(&$data, $term) {
    $data['label'] = ucwords(str_replace('-', ' ', $term));
    $data['meta'] = sanitize_title($term);
}

// Retrieve current taxonomy and term
$taxonomy = get_query_var('taxonomy');
$term = single_cat_title('', false);

// Set labels and meta based on taxonomy
switch ($taxonomy) {
    case 'property_city':
        wpresidence_set_filter_label_meta($filter_data['city'], $term);
        break;
    case 'property_area':
        wpresidence_set_filter_label_meta($filter_data['area'], $term);
        break;
    case 'property_category':
        wpresidence_set_filter_label_meta($filter_data['category'], $term);
        break;
    case 'property_action_category':
        wpresidence_set_filter_label_meta($filter_data['action'], $term);
        break;
    case 'property_county_state':
        wpresidence_set_filter_label_meta($filter_data['county'], $term);
        break;
}

// Output hidden div elements for each filter type
?>
<div data-toggle="dropdown" id="second_filter_action" class="d-none" data-value="<?php echo esc_attr($filter_data['action']['meta']); ?>">
    <?php echo esc_html($filter_data['action']['label']); ?>
</div>

<div data-toggle="dropdown" id="second_filter_categ" class="d-none" data-value="<?php echo esc_attr($filter_data['category']['meta']); ?>">
    <?php echo esc_html($filter_data['category']['label']); ?>
</div>

<div data-toggle="dropdown" id="second_filter_cities" class="d-none" data-value="<?php echo esc_attr($filter_data['city']['meta']); ?>">
    <?php echo esc_html($filter_data['city']['label']); ?>
</div>

<div data-toggle="dropdown" id="second_filter_areas" class="d-none" data-value="<?php echo esc_attr($filter_data['area']['meta']); ?>">
    <?php echo esc_html($filter_data['area']['label']); ?>
</div>

<div data-toggle="dropdown" id="second_filter_county" class="d-none" data-value="<?php echo esc_attr($filter_data['county']['meta']); ?>">
    <?php echo esc_html($filter_data['county']['label']); ?>
</div>