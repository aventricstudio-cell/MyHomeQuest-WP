<?php
/** MILLDONE
 *  src: libs\property_page_functions\property_energy_savings_functions.php
 */

/**
 * Generate the property energy savings section.
 *
 * This function creates either a tab item or an accordion section
 * for the property energy savings information, based on the provided parameters.
 *
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Whether the energy savings section is part of a tab layout ('yes' or '').
 * @param string $tab_active_class The CSS class for active tabs (if applicable).
 * @return string|void HTML content of the energy savings section or void if not a tab.
 */
if (!function_exists('wpestate_property_energy_savings_v2')) :
    function wpestate_property_energy_savings_v2($postID, $is_tab = '', $tab_active_class = '') {
        // Retrieve label data for the energy savings section
        $data = wpestate_return_all_labels_data('energy-savings');
        $label = wpestate_property_page_prepare_label($data['label_theme_option'], $data['label_default']);

        // Get the energy savings content
        $content = wpestate_energy_save_features($postID);

        // Determine whether to return a tab item or print an accordion section
        if ($is_tab === 'yes') {
            return wpestate_property_page_create_tab_item($content, $label, $data['tab_id'], $tab_active_class);
        } else {
            $accordion_content = wpestate_property_page_create_acc(
                $content,
                $label,
                $data['accordion_id'],
                $data['accordion_id'] . '_collapse'
            );
            
            // Use echo for output, which is preferable in WordPress
            echo $accordion_content;
        }
    }
endif;



/**
 * Generate the energy savings features for a property (Obsolete).
 *
 * This function compiles various energy-related data for a property,
 * including renewable energy index, building energy performance,
 * and EPC ratings. It also includes energy and CO2 savings features.
 *
 * @param int    $post_id                  The ID of the property post.
 * @param string $wpestate_prop_all_details Additional property details (not used in current implementation).
 * @return string HTML content of all energy savings features.
 */
if (!function_exists('wpestate_energy_save_features')) :
    function wpestate_energy_save_features($post_id, $wpestate_prop_all_details = '') {
        $energy_fields = array(
            'renew_energy_index'    => esc_html__('Renewable energy performance index', 'wpresidence'),
            'building_energy_index' => esc_html__('Energy performance of the building', 'wpresidence'),
            'epc_current_rating'    => esc_html__('EPC current rating', 'wpresidence'),
            'epc_potential_rating'  => esc_html__('EPC Potential Rating', 'wpresidence'),
        );

        $energy_data = array();
        foreach ($energy_fields as $key => $label) {
            $value = get_post_meta($post_id, $key, true);
            if (!empty($value)) {
                $energy_data[] = sprintf(
                    '<div class="listing_detail col-md-6"><strong>%s:</strong> %s</div>',
                    esc_html($label),
                    esc_html($value)
                );
            }
        }

        $return_string = '<div class="row">'. implode('', $energy_data);
        $return_string .= wpestate_energy_save_features_v2($post_id, 'energy', $wpestate_prop_all_details);
        $return_string .= wpestate_energy_save_features_v2($post_id, 'co2', $wpestate_prop_all_details).'</div>';

        return $return_string;
    }
endif;




/**
 * Generate detailed energy or CO2 savings information for a property.
 *
 * This function creates a formatted display of energy or CO2 savings data,
 * including index, class, and a visual representation of energy grades.
 *
 * @param int    $post_id                  The ID of the property post.
 * @param string $type                     The type of data to display ('energy' or 'co2').
 * @param string $wpestate_prop_all_details Additional property details (not used in current implementation).
 * @return string HTML content of the energy or CO2 savings information.
 */
if (!function_exists('wpestate_energy_save_features_v2')) :
    function wpestate_energy_save_features_v2($post_id, $type = 'energy', $wpestate_prop_all_details = '') {
        $data = wpresidence_get_energy_data($post_id, $type);
        if (empty($data['class']) && empty($data['index'])) {
            return '';
        }

        $grades = wpresidence_generate_energy_grades($data['possible_grades'], $data['class'], $data['index'], $type);

        ob_start();
        ?>
        <div class="listing_detail col-md-12 listing_detail_energy">
            <div class="row class-energy">
                <?php if (!empty($data['index'])) : ?>
                    <div class="listing_detail col-md-6">
                        <strong><?php echo esc_html($data['index_label']); ?>:</strong> <?php echo esc_html($data['index']); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($data['class'])) : ?>
                    <div class="listing_detail col-md-6">
                        <strong><?php echo esc_html($data['class_label']); ?>:</strong> <?php echo esc_html($data['class']); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($data['class'])) : ?>
                    <div class="wpestate-energy_class_container">
                        <?php echo $grades; ?>
                    </div>
                <?php endif; ?>
            </div>   
        </div>
        <?php
        return ob_get_clean();
    }
endif;




/**
 * Retrieve energy or CO2 data for a property.
 *
 * @param int    $post_id The ID of the property post.
 * @param string $type    The type of data to retrieve ('energy' or 'co2').
 * @return array An array of energy or CO2 data.
 */
if(!function_exists('wpresidence_get_energy_data')) :
    function wpresidence_get_energy_data($post_id, $type) {
        $data = [
            'index' => get_post_meta($post_id, $type . '_index', true),
            'class' => get_post_meta($post_id, $type . '_class', true),
            'possible_grades' => wpresidence_get_option('wpestate_' . $type . '_section_possible_grades', ''),
        ];

        if ($type === 'co2') {
            $data['index_label'] = esc_html__('Greenhouse Gas Emissions kgCO2/m2a', 'wpresidence');
            $data['class_label'] = esc_html__('Greenhouse Gas Emissions index class', 'wpresidence');
            $data['indicator_text'] = esc_html__('Greenhouse gas emissions class', 'wpresidence');
        } else {
            $data['index_label'] = esc_html__('Energy Index in kWh/m2a', 'wpresidence');
            $data['class_label'] = esc_html__('Energy Class', 'wpresidence');
            $data['indicator_text'] = esc_html__('Energy class', 'wpresidence');
        }

        return $data;
    }
endif;





/**
 * Generate HTML for energy grades.
 *
 * @param string $possible_grades Comma-separated list of possible grades.
 * @param string $energy_class    The property's energy class.
 * @param string $energy_index    The property's energy index.
 * @param string $type            The type of energy information ('energy' or 'co2').
 * @return string HTML for energy grades.
 */
if (!function_exists('wpresidence_generate_energy_grades')) :
    function wpresidence_generate_energy_grades($possible_grades, $energy_class, $energy_index, $type) {
        $grades = '';
        $grades_array = explode(',', $possible_grades);
        
        foreach ($grades_array as $i => $value) {
            $i++; // Increment to start from 1
            $color_class = 'wp_estate_' . esc_attr($type) . '_class_colorb_' . $i;
            $grades .= sprintf(
                '<div class="wpestate-class-energy wpestate-class-%1$s_no_%2$d" style="background-color:%3$s">',
                esc_attr($type),
                intval($i),
                wpresidence_get_option($color_class, '')
            );
            
            if (strtolower($energy_class) === strtolower($value)) {
                $grades .= '<div class="indicator-energy" data-energyclass="' . esc_attr($value) . '">';
                if (!empty($energy_index)) {
                    $grades .= $energy_index . ' ' . ($type === 'energy' ? 'kWh/m²a' : 'kgCO2/m²a') . ' ';
                }
                $grades .= esc_html__($type === 'energy' ? 'Energy class' : 'Greenhouse gas emissions class', 'wpresidence') . ' ' . esc_html($energy_class) . '</div>';
            }
            
            $grades .= esc_html($value) . '</div>';
        }
        
        return $grades;
    }
endif;

?>