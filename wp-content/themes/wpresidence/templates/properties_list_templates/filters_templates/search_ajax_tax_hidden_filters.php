<?php
/** MILLDONE
 * Search Ajax Tax Hidden Filters Template
 * src: templates\properties_list_templates\filters_templates\search_ajax_tax_hidden_filters.php
 * This template generates hidden filter elements for AJAX-based property searches
 * in the WpResidence theme. It sets up default filter values and outputs hidden
 * div elements that are used by JavaScript to maintain filter state during AJAX requests.
 *
 * @package WpResidence
 * @subpackage PropertySearch
 * @since WpResidence 1.0
 *
 * @global object $post The current post object
 *
 * @uses get_post_meta() to retrieve custom field data
 */

// Initialize variables for filter labels and values
$current_name      = '';
$current_slug      = '';
$listings_list     = '';
$show_filter_area  = '';

// Check if we're on a post page and retrieve the filter area setting
if (isset($post->ID)) {
    $show_filter_area = get_post_meta($post->ID, 'show_filter_area', true);
}

// Set default values for filter meta fields
$current_adv_filter_search_meta   = 'Types';
$current_adv_filter_category_meta = 'Categories';
$current_adv_filter_city_meta     = 'Cities';
$current_adv_filter_area_meta     = 'Areas';
$current_adv_filter_county_meta   = 'States';       

// Set default labels for filter fields
$current_adv_filter_search_label   = esc_html__('Types', 'wpresidence');
$current_adv_filter_category_label = esc_html__('Categories', 'wpresidence');
$current_adv_filter_city_label     = esc_html__('Cities', 'wpresidence');
$current_adv_filter_area_label     = esc_html__('Areas', 'wpresidence');
$current_adv_filter_county_label   = esc_html__('States', 'wpresidence');

// Output hidden div elements for each filter type
?>
<div data-toggle="dropdown" id="second_filter_action" class="d-none" data-value="<?php echo esc_attr($current_adv_filter_search_meta); ?>">
    <?php echo esc_html($current_adv_filter_search_label); ?>
</div>

<div data-toggle="dropdown" id="second_filter_categ" class="d-none" data-value="<?php echo esc_attr($current_adv_filter_category_meta); ?>">
    <?php echo esc_html($current_adv_filter_category_label); ?>
</div>

<div data-toggle="dropdown" id="second_filter_cities" class="d-none" data-value="<?php echo esc_attr($current_adv_filter_city_meta); ?>">
    <?php echo esc_html($current_adv_filter_city_label); ?>
</div>

<div data-toggle="dropdown" id="second_filter_areas" class="d-none" data-value="<?php echo esc_attr($current_adv_filter_area_meta); ?>">
    <?php echo esc_html($current_adv_filter_area_label); ?>
</div>

<div data-toggle="dropdown" id="second_filter_county" class="d-none" data-value="<?php echo esc_attr($current_adv_filter_county_meta); ?>">
    <?php echo esc_html($current_adv_filter_county_label); ?>
</div>