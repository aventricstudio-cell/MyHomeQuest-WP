<?php
/** MILLDONE
 * WpResidence Theme - Single Property Overview Section Template
 * src: templates\listing_templates\property-page-templates\single-overview-section.php
 * This file contains the structure for displaying the overview section on a single property page.
 * It includes various property details such as bedrooms, bathrooms, size, and a map.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since 1.0.0
 *
 * Dependencies:
 * - wpresidence_get_option()
 * - wpestate_get_converted_measure()
 * - wpestate_display_overview_item()
 * - wpestate_overview_map_modal()
 *
 * Usage: This template is included in single property pages to display the property overview section.
 */

// Retrieve options from the database for property overview order and whether to show the title.
$wp_estate_property_overview_order = wpresidence_get_option('wp_estate_property_overview_order', '');
$wp_estate_show_overview_title = wpresidence_get_option('wp_estate_show_overview_title', '');




// Check if the display should not be in tab format.
if ( !isset($is_tab) || $is_tab != 'yes') : 
    ?>
    <div class="single-overview-section panel-group property-panel" id="single-overview-section">
    <?php 
    if ($wp_estate_show_overview_title == 'yes') :
        ?>
        <h4 class="panel-title" id=""><?php echo esc_html($label); ?></h4>
        <?php 
    endif;
endif; 
?>

<div class="property-page-overview-details-wrapper  justify-content-start justify-content-md-between">
    <?php
    if (is_array($wp_estate_property_overview_order['enabled'])):
        foreach ($wp_estate_property_overview_order['enabled'] as $key => $value):
            switch ($key) {
                case 'updated_on':
                    ?>
                    <ul class="overview_element">
                        <li class="first_overview first_overview_left">
                            <?php esc_html_e('Updated On:', 'wpresidence'); ?>
                        </li>
                        <li class="first_overview_date"><?php echo get_the_modified_date(); ?></li>
                    </ul>
                    <?php
                    break;

                case 'bedrooms':
                    $property_bedrooms = get_post_meta($post->ID, 'property_bedrooms', true);
                    if ($property_bedrooms != '' && $property_bedrooms != 0) { 
                        echo wpestate_display_overview_item('bedrooms', $property_bedrooms);
                    } 
                    break;

                case 'bathrooms':
                    $property_bathrooms = get_post_meta($post->ID, 'property_bathrooms', true);
                    if ($property_bathrooms != '' && $property_bathrooms != 0) { 
                        echo wpestate_display_overview_item('bathrooms', $property_bathrooms);   
                    }
                    break;

                case 'rooms':
                    $property_rooms = get_post_meta($post->ID, 'property_rooms', true);
                    if ($property_rooms != '' && $property_rooms != 0) {
                        echo wpestate_display_overview_item('rooms', $property_rooms);   
                    } 
                    break;

                case 'garages':
                    $property_garage = get_post_meta($post->ID, 'property-garage', true);
                    if ($property_garage != '' && $property_garage != 0) { 
                        echo wpestate_display_overview_item('garages', $property_garage);   
                    }
                    break;

                case 'size':
                    $property_size = wpestate_get_converted_measure($post->ID, 'property_size');
                    if ($property_size != '' && strval($property_size) != '0') { 
                        echo wpestate_display_overview_item('size', $property_size);   
                    }
                    break;

                case 'lot_size':
                    $property_lot_size = wpestate_get_converted_measure($post->ID, 'property_lot_size');
                    if ($property_lot_size != '' && strval($property_lot_size) != '0') {                
                        echo wpestate_display_overview_item('lot_size', $property_lot_size);   
                    } 
                    break;

                case 'year_built':
                    $property_year = get_post_meta($post->ID, 'property-year', true);
                    if ($property_year != '') { 
                        echo wpestate_display_overview_item('year_built', $property_year);   
                    } 
                    break;

                case 'property_category':
                    $property_card_type_string = get_the_term_list($post->ID, 'property_category', '', ', ', '');
                    echo wpestate_display_overview_item('property_category', $property_card_type_string);   
                    break;

                case 'property_id':
                    echo wpestate_display_overview_item('property_id', $post->ID);   
                    break;

                case 'property_internal_id':
                    $property_internal_id = get_post_meta($post->ID, 'property_internal_id', true);
                    echo wpestate_display_overview_item('property_internal_id', $property_internal_id);   
                    break;

                case 'map':
                    wpestate_display_property_overview_map($post->ID);
                    break;
            }
        endforeach;
    endif;
    ?>
</div>

<?php
if ( !isset($is_tab) || $is_tab != 'yes') : 
    ?>
    </div>
    <?php 
endif;


?>