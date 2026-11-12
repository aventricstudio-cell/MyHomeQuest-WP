<?php
/** MILLDONE
 * WpResidence Property Address Display
 * src templates\listing_templates\property-page-templates\address_under_title.php
 * This template file is responsible for rendering the address section
 * under the title of a property page in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @version 1.0
 * 
 * @uses get_post_meta()
 * 
 * Dependencies:
 * - WordPress core functions
 * - WpResidence theme-specific variables
 * 
 * Expected global variables:
 * - $post
 * - $property_city
 * - $property_area
 */

// Fetch property address details
$property_address = get_post_meta($selectedPropertyID, 'property_address', true);

// Initialize array to store address components
$address_components = array();

// Add address components if they exist
if (!empty($property_address)) {
    $address_components[] = esc_html($property_address);
}
if (!empty($property_city)) {
    $address_components[] = wp_kses_post($property_city);
}
if (!empty($property_area)) {
    $address_components[] = wp_kses_post($property_area);
}

// Join address components with commas
$property_address_show = implode(', ', $address_components);

// Only display the address section if there's content to show
if (!empty($property_address_show)):
?>
<div class="property_categs">
    <i class="fas fa-map-marker-alt"></i>
    <?php echo wp_kses_post($property_address_show); ?>
</div>
<?php
endif;
?>