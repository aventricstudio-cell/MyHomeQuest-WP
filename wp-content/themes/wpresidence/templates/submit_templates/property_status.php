<?php
/** MILLDONE
 * WpResidence Property Status Section
 * src: templates/submit_templates/property_status.php
 * This file is part of the WpResidence theme and handles the display and selection
 * of property status in the property submission or edit form.
 *
 * @package WpResidence
 * @subpackage PropertySubmission
 * @since 1.0.0
 *
 * Dependencies:
 * - Global variables: $wpestate_submission_page_fields, $allowed_html
 *
 * Usage:
 * This file should be included in the property submission or edit form within the WpResidence theme.
 * It displays a dropdown for selecting the property status.
 */

// Fetch all property status terms
$property_status_array =  wpestate_get_cached_terms('property_status');

$prop_stat = '';

// Handle property edit scenario
if (isset($_GET['listing_edit']) && is_numeric($_GET['listing_edit'])) {
    $post_id = intval($_GET['listing_edit']);
    $property_status_values = get_the_terms($post_id, 'property_status');
    if (!empty($property_status_values) && !is_wp_error($property_status_values)) {
        $prop_stat = esc_html($property_status_values[0]->name);
    }
}

// Handle form submission
if (isset($_POST['property_status'])) {
    $prop_stat = wp_kses($_POST['property_status'], $allowed_html);
}

// Generate property status options
$property_status_options = '';
if (is_array($property_status_array) && !empty($property_status_array)) {
    foreach ($property_status_array as $term) {
        $property_status_options .= sprintf(
            '<option value="%1$s" %2$s>%3$s</option>',
            esc_attr($term->name),
            selected($term->name, $prop_stat, false),
            esc_html(stripslashes($term->name))
        );
    }
}

// Display the property status section if it's included in the submission fields
if (is_array($wpestate_submission_page_fields) && in_array('property_status', $wpestate_submission_page_fields, true)) :
?>
    <div class="profile-onprofile row">
        <div class="wpestate_dashboard_section_title"><?php esc_html_e('Select Property Status', 'wpresidence'); ?></div>
        <div class="col-md-6">
            <label for="property_status"><?php esc_html_e('Property Status', 'wpresidence'); ?></label>
            <select id="property_status" name="property_status" class="select-submit">
                <option value=""><?php esc_html_e('No status', 'wpresidence'); ?></option>
                <?php echo $property_status_options; ?>
            </select>
        </div>
    </div>
<?php
endif;
?>