<?php
/** MILLDONE
 * Property Card Content Template
 * src: templates\property_cards_templates\property_card_details_templates\property_card_content.php
 * This template is responsible for displaying the content (excerpt) of a property
 * on property cards in the WpResidence theme. It supports both grid and list views.
 *
 * @package WpResidence
 * @subpackage PropertyCard
 * @since 1.0
 */


/**
 * Retrieve excerpt length settings for grid and list views
 * These values determine how many characters to display in each view
 */
$excerpt_length_grid = intval(wpresidence_get_option('wp_estate_unit_card_excerpt_grid', false,'90'));
$excerpt_length_list = intval(wpresidence_get_option('wp_estate_unit_card_excerpt_list', false,'160'));

/**
 * Determine which view style to display based on the global $align_class
 */
$is_list_view = ($wpresidence_property_cards_context['property_unit_class']['col_org'] ===12);


/**
 * Display grid view excerpt
 */
?>
<div class="listing_details the_grid_view" style="display:<?php echo $is_list_view ? 'none' : 'block'; ?>">
    <?php echo wpresidence_unit_card_generate_excerpt_html_from_cache($excerpt_length_grid, $property_unit_cached_data,$postID); ?>
</div>

<?php
/**
 * Display list view excerpt
 */
?>
<div class="listing_details the_list_view" style="display:<?php echo $is_list_view ? 'block' : 'none'; ?>">
    <?php echo wpresidence_unit_card_generate_excerpt_html_from_cache($excerpt_length_list, $property_unit_cached_data,$postID); ?>
</div>