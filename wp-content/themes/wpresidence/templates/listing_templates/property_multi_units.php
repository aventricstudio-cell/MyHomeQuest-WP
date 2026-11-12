<?php
/**MILLDONE
 * WpResidence Theme - Property Multi-Units Template
 * src: templates\listing_templates\property_multi_units.php
 * This template displays information about multiple units associated with a property.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since 1.0.0
 *
 * Dependencies:
 * - wpresidence_get_option()
 * - wpestate_get_converted_measure()
 * - wpestate_show_price()
 *
 * Usage: This template is included in property pages to display multi-unit information.
 */

global $post;

// Determine the property ID
$prop_id = isset($property_id) && $property_id !== '' ? $property_id : $post->ID;

// Get currency settings
$wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
$where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));

// Check if property has multi-units
$has_multi_units = intval(get_post_meta($prop_id, 'property_has_subunits', true));
$property_subunits_master = intval(get_post_meta($prop_id, 'property_subunits_master', true));

// Determine if we should display multi-units
$display = wpresidence_determine_multi_unit_display($has_multi_units, $property_subunits_master, $prop_id);

if ($display) :
    ?>
    <div class="multi_units_wrapper_v2 row">
        <?php
        $prop_id = ($property_subunits_master != 0 && $property_subunits_master != $post->ID) ? $property_subunits_master : $prop_id;
        $property_subunits_list = wpresidence_get_property_subunits_list($prop_id);

        if (is_array($property_subunits_list)) :
            foreach ($property_subunits_list as $subunit_id) :
                if ($subunit_id != $post->ID && get_post_status($subunit_id) == 'publish') :
                    wpresidence_display_subunit_info($subunit_id, $wpestate_currency, $where_currency);
                endif;
            endforeach;
        endif;
        ?>
    </div>
<?php
endif;
?>