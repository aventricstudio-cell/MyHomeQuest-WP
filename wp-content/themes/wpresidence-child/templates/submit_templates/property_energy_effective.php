<?php
/** MILLDONE
 * WpResidence Energy Section
 * src: templates/submit_templates/property_energy_effective.php
 * This file is part of the WpResidence theme and handles the display and submission
 * of energy-related information for property listings.
 *
 * @package WpResidence
 * @subpackage PropertySubmission
 * @since 1.0.0
 *
 * Dependencies:
 * - WpResidence theme functions (wpresidence_get_option, wpestate_submit_return_value)
 * - Global variables: $wpestate_submission_page_fields, $get_listing_edit
 *
 * Usage:
 * This file should be included in the property submission or edit form within the WpResidence theme.
 * It displays input fields for energy class, CO2 class, and various energy performance indices.
 */

// Retrieve energy and CO2 class data from theme options
$energy_class_data = wpresidence_get_option('wpestate_energy_section_possible_grades', '');
$energy_class_array = explode(",", $energy_class_data);

$co2_class_data = wpresidence_get_option('wpestate_co2_section_possible_grades', '');
$co2_class_array = explode(",", $co2_class_data);

// Get current values for energy and CO2 class (if editing a listing)
$energy_class = wpestate_submit_return_value('energy_class', $get_listing_edit, '');
$co2_class = wpestate_submit_return_value('co2_class', $get_listing_edit, '');

// Check if any energy-related fields should be displayed
$display_energy_section = is_array($wpestate_submission_page_fields) && (
    in_array('energy_index', $wpestate_submission_page_fields) ||
    in_array('energy_class', $wpestate_submission_page_fields) ||
    in_array('co2_class', $wpestate_submission_page_fields) ||
    in_array('co2_index', $wpestate_submission_page_fields) ||
    in_array('renew_energy_index', $wpestate_submission_page_fields) ||
    in_array('building_energy_index', $wpestate_submission_page_fields) ||
    in_array('epc_current_rating', $wpestate_submission_page_fields) ||
    in_array('epc_potential_rating', $wpestate_submission_page_fields)
);

if ($display_energy_section) :
?>
    <div class="profile-onprofile row">
        <div class="wpestate_dashboard_section_title"><?php esc_html_e('Select Energy Class', 'wpresidence'); ?></div>
        
        <?php if (is_array($wpestate_submission_page_fields) && in_array('energy_class', $wpestate_submission_page_fields)) : ?>
            <div class="col-md-6">
                <label for="energy_class"><?php esc_html_e('Energy Class', 'wpresidence'); ?></label>
                <select name="energy_class" id="energy_class">
                    <option value=""><?php esc_html_e('Select Energy Class (EU regulation)', 'wpresidence'); ?></option>
                    <?php foreach ($energy_class_array as $single_class) : ?>
                        <option value="<?php echo esc_attr($single_class); ?>" <?php selected($energy_class, $single_class); ?>><?php echo esc_html($single_class); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('energy_index', $wpestate_submission_page_fields)) : ?>
            <div class="col-md-6">
                <label for="energy_index"><?php esc_html_e('Energy Index in kWh/m2a', 'wpresidence'); ?></label>
                <input type="text" id="energy_index" class="form-control" size="40" name="energy_index"
                       value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('energy_index', $get_listing_edit, ''))); ?>">
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('co2_class', $wpestate_submission_page_fields)) : ?>
            <div class="col-md-6">
                <label for="co2_class"><?php esc_html_e('Greenhouse gas emissions index class', 'wpresidence'); ?></label>
                <select name="co2_class" id="co2_class">
                    <option value=""><?php esc_html_e('Select greenhouse gas emissions index class', 'wpresidence'); ?></option>
                    <?php foreach ($co2_class_array as $single_class) : ?>
                        <option value="<?php echo esc_attr($single_class); ?>" <?php selected($co2_class, $single_class); ?>><?php echo esc_html($single_class); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('co2_index', $wpestate_submission_page_fields)) : ?>
            <div class="col-md-6">
                <label for="co2_index"><?php esc_html_e('Greenhouse Gas Emissions KgCO2/M2a', 'wpresidence'); ?></label>
                <input type="text" id="co2_index" class="form-control" size="40" name="co2_index"
                       value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('co2_index', $get_listing_edit, ''))); ?>">
            </div>
        <?php endif; ?>

        <?php
        // Additional energy-related fields
        $other_energy = array(
            'renew_energy_index' => esc_html__('Renewable energy performance index', 'wpresidence'),
            'building_energy_index' => esc_html__('Energy performance of the building', 'wpresidence'),
            'epc_current_rating' => esc_html__('EPC current rating', 'wpresidence'),
            'epc_potential_rating' => esc_html__('EPC Potential Rating', 'wpresidence'),
        );

        foreach ($other_energy as $key => $value) :
            if (is_array($wpestate_submission_page_fields) && in_array($key, $wpestate_submission_page_fields)) :
        ?>
                <div class="col-md-6">
                    <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($value); ?></label>
                    <input type="text" id="<?php echo esc_attr($key); ?>" class="form-control" size="40" name="<?php echo esc_attr($key); ?>"
                           value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value($key, $get_listing_edit, ''))); ?>">
                </div>
        <?php
            endif;
        endforeach;
        ?>
    </div>
<?php endif; ?>