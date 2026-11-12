<?php
/** MILLDONE
 * Property List Ajax Tax Hidden Filters Template
 * src: templates\properties_list_templates\filters_templates\property_list_ajax_tax_hidden_filters.php
 * This template handles the hidden filters for property listings,
 * used in AJAX requests for property list pages.
 *
 * @package WpResidence
 * @subpackage PropertyListings
 * @since WpResidence 1.0
 */


 // Define filter types and their default values
$current_adv_filter_search_meta     = 'Types';
$current_adv_filter_category_meta   = 'Categories';
$current_adv_filter_city_meta       = 'Cities';
$current_adv_filter_area_meta       = 'Areas';
$current_adv_filter_county_meta       = 'States'; 

$current_adv_filter_search_label=esc_html__('Types','wpresidence');
$current_adv_filter_city_label=esc_html__('Cities','wpresidence');
$current_adv_filter_category_label=esc_html__('Categories','wpresidence');
$current_adv_filter_area_label=esc_html__('Areas','wpresidence');
$current_adv_filter_county_label=esc_html__('States','wpresidence');
?>


<?php
// Output hidden filter elements
?>

<div data-toggle="dropdown" id="second_filter_action" class="d-none" data-value="<?php print esc_attr($current_adv_filter_search_meta);?>"> 
    <?php echo  esc_html( $current_adv_filter_search_label);?>  
</div>           

<div data-toggle="dropdown" id="second_filter_categ" class="d-none" data-value="<?php print esc_attr($current_adv_filter_category_meta);?> "> 
    <?php  echo  esc_html($current_adv_filter_category_label);?>
</div>           

<div data-toggle="dropdown" id="second_filter_cities" class="d-none" data-value="<?php print esc_attr($current_adv_filter_city_meta);?>">
    <?php echo  esc_html($current_adv_filter_city_label);?>
</div>           

<div data-toggle="dropdown" id="second_filter_areas"  class="d-none" data-value="<?php print esc_attr($current_adv_filter_area_meta);?>">
    <?php echo  esc_html($current_adv_filter_area_label);?>
</div>           

<div data-toggle="dropdown" id="second_filter_county"  class="d-none" data-value="<?php print esc_attr($current_adv_filter_county_meta);?>">
    <?php echo  esc_html($current_adv_filter_county_label);?>
</div>              