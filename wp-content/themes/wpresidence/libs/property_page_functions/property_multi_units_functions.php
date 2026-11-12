<?php
/** MILLDONE
 *  src: libs\property_page_functions\property_multi_units_functions.php
 */


/**
 * Generate the property multi-units section.
 *
 * This function creates either a tab item or an accordion section
 * for the property multi-units information, based on the provided parameters.
 *
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Whether the multi-units section is part of a tab layout ('yes' or '').
 * @param string $tab_active_class The CSS class for active tabs (if applicable).
 * @return string|void HTML content of the multi-units section or void if not a tab.
 */
if (!function_exists('wpestate_property_multi_units_v2')) :
    function wpestate_property_multi_units_v2($postID,$shortcode_settings=array(), $is_tab = '', $tab_active_class = '') {
        // Prepare labels
        $default = esc_html__('Available Units', 'wpresidence');
        $default_child = esc_html__('Other units in', 'wpresidence').' ';
        $label = wpestate_property_page_prepare_label('wp_estate_property_multi_text', $default);
        $label_child = wpestate_property_page_prepare_label('wp_estate_property_multi_child_text', $default_child);
       
        if ( isset($shortcode_settings['section_title']) && $shortcode_settings['section_title'] !== '') {
            $label = $shortcode_settings['section_title'];
        }

        if ( isset($shortcode_settings['section_title2']) && $shortcode_settings['section_title2'] !== '') {
            $label_child = $shortcode_settings['section_title2'];
        }



        // Get property meta data
        $has_multi_units = intval(get_post_meta($postID, 'property_has_subunits', true));
        $property_subunits_master = intval(get_post_meta($postID, 'property_subunits_master', true));

        // Adjust label for subunits
        if ($property_subunits_master != 0 && $property_subunits_master != $postID) {
            $master_title = get_the_title($property_subunits_master);
            $master_url = get_permalink($property_subunits_master);
            $label = sprintf(
                '%s&nbsp;<a href="%s" class="nested-link" target="_blank">%s</a>',
                esc_html($label_child),
                esc_url($master_url),
                esc_html($master_title)
            );
        }

        // Get multi-units data
        $data = wpestate_return_all_labels_data('multi-units');

        $property_id=$postID;
        // Generate content
        ob_start();
        if ($has_multi_units == 1 || $property_subunits_master != 0) {
            include(locate_template('/templates/listing_templates/property_multi_units.php'));
        }
        $content = ob_get_clean();

        // Return content based on display type
        if ($is_tab === 'yes') {
            return wpestate_property_page_create_tab_item($content, $label, $data['tab_id'], $tab_active_class);
        } else {
            echo wpestate_property_page_create_acc($content, $label, $data['accordion_id'], $data['accordion_id'] . '_collapse');
        }
    }
endif;


/**
 * Determine if multi-unit display should be shown.
 *
 * @param int $has_multi_units
 * @param int $property_subunits_master
 * @param int $prop_id
 * @return bool
 */
if(!function_exists('wpresidence_determine_multi_unit_display')):
    function wpresidence_determine_multi_unit_display($has_multi_units, $property_subunits_master, $prop_id) {
        if ($has_multi_units == 1) {
            return true;
        }

        if ($property_subunits_master != 0) {
            $master_has_multi_units = intval(get_post_meta($property_subunits_master, 'property_has_subunits', true));
            if ($master_has_multi_units == 1) {
                $property_subunits_list_master = get_post_meta($property_subunits_master, 'property_subunits_list', true);
                if (is_array($property_subunits_list_master)) {
                    $key = array_search($prop_id, $property_subunits_list_master);
                    if ($key !== false) {
                        unset($property_subunits_list_master[$key]);
                    }
                    return count($property_subunits_list_master) > 0;
                }
            }
        }

        return false;
    }
endif;

/**
 * Get the list of subunits for a property.
 *
 * @param int $prop_id
 * @return array
 */
if(!function_exists('wpresidence_get_property_subunits_list')):
function wpresidence_get_property_subunits_list($prop_id) {
    $property_subunits_list_manual = get_post_meta($prop_id, 'property_subunits_list_manual', true);
    if ($property_subunits_list_manual != '') {
        return explode(',', $property_subunits_list_manual);
    }
    return get_post_meta($prop_id, 'property_subunits_list', true);
}
endif;

/**
 * Display information for a single subunit.
 *
 * @param int $subunit_id
 * @param string $wpestate_currency
 * @param string $where_currency
 */
if(!function_exists('wpresidence_display_subunit_info')):
    function wpresidence_display_subunit_info($subunit_id, $wpestate_currency, $where_currency) {
        $compare = wp_get_attachment_image_src(get_post_thumbnail_id($subunit_id), 'slider_thumb');
        $property_details = wpresidence_get_subunit_details($subunit_id);
        $title = get_the_title($subunit_id);
        $link = esc_url(get_permalink($subunit_id));

        ?>
        <div class="subunit_wrapper flex-column flex-sm-row">
            <div class="subunit_thumb">
                <a href="<?php echo $link; ?>" target="_blank">
                    <img src="<?php echo esc_url($compare[0]); ?>" alt="<?php echo esc_attr($title); ?>" />
                </a>
            </div>
            <div class="subunit_details">
                <div class="subunit_title">
                    <a href="<?php echo $link; ?>" target="_blank"><?php echo esc_html($title); ?></a>
                    <div class="subunit_price">
                        <?php wpestate_show_price($subunit_id, $wpestate_currency, $where_currency); ?>
                    </div>
                </div>
                <?php wpresidence_display_subunit_details($property_details); ?>
            </div>
        </div>
    <?php
}
endif;







/**
 * Get details for a subunit.
 *
 * @param int $subunit_id
 * @return array
 */
if(!function_exists('wpresidence_get_subunit_details')):
 function wpresidence_get_subunit_details($subunit_id) {
    return array(
        'type' => get_the_term_list($subunit_id, 'property_category', '', ', ', ''),
        'rooms' => floatval(get_post_meta($subunit_id, 'property_rooms', true)),
        'bedrooms' => floatval(get_post_meta($subunit_id, 'property_bedrooms', true)),
        'bathrooms' => floatval(get_post_meta($subunit_id, 'property_bathrooms', true)),
        'size' => wpestate_get_converted_measure($subunit_id, 'property_size'),
        'size_simple' => floatval(get_post_meta($subunit_id, 'property_size', true))
    );
}
endif;





/**
 * Display details for a subunit.
 *
 * @param array $details
 */
if(!function_exists('wpresidence_display_subunit_details')):
    function wpresidence_display_subunit_details($details) {
        $detail_items = array(
            'type' => array('label' => esc_html__('Category: ', 'wpresidence'), 'value' => $details['type']),
            'rooms' => array('label' => esc_html__('Rooms: ', 'wpresidence'), 'value' => $details['rooms']),
            'bedrooms' => array('label' => esc_html__('Bedrooms: ', 'wpresidence'), 'value' => $details['bedrooms']),
            'bathrooms' => array('label' => esc_html__('Baths: ', 'wpresidence'), 'value' => $details['bathrooms']),
            'size' => array('label' => esc_html__('Size: ', 'wpresidence'), 'value' => $details['size'])
        );
        echo '<div class="subunit_extra_details_wrapper">';
        foreach ($detail_items as $key => $item) {
            if ($key === 'type' || ($key === 'size' && $details['size_simple'] > 0) || $item['value'] > 0) {
                echo '<div class="subunit_' . esc_attr($key) . '"><strong>' . $item['label'] . '</strong> ' . wp_kses_post($item['value']) . '</div>';
            }
        }
        echo '</div>';
    }
endif;