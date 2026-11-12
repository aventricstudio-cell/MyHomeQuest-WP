<?php
/** MILLDONE
 * WpResidence Theme - Property Submission Form
 * src: templates/submit_templates/property_details.php
 * This code handles the rendering of custom fields and property details
 * in the property submission form.
 *
 * @package WpResidence
 * @subpackage PropertySubmission
 * @since 1.0.0
 */

$show_settings_value = 1;
$measure_sys = wpestate_get_meaurement_unit_formated($show_settings_value);
$measure_sys_lot_size = wpestate_get_meaurement_unit_formated_lot_size($show_settings_value);

$custom_fields_show = '';
$custom_fields = wpresidence_get_option('wp_estate_custom_fields', '');
$i = 0;

// Process custom fields
if (!empty($custom_fields)) {
    while ($i < count($custom_fields)) {
        $name = $custom_fields[$i][0];
        $type = $custom_fields[$i][1];
        $slug = wpestate_limit45(sanitize_title($name));
        $slug = sanitize_key($slug);
        if (isset($_POST[$slug])) {
            $custom_fields_array[$slug] = wp_kses(esc_html($_POST[$slug]), $allowed_html);
        }
        $i++;
    }
}

// Generate custom fields HTML
$i = 0;
if (!empty($custom_fields)):
    while ($i < count($custom_fields)) {
        $name = $custom_fields[$i][0];
        $label = stripslashes($custom_fields[$i][1]);
        $type = $custom_fields[$i][2];
        $order = $custom_fields[$i][3];
        $dropdown_values = $custom_fields[$i][4];
        $slug = wpestate_limit45(sanitize_title($name));
        $slug = sanitize_key($slug);
        $prslig = str_replace(' ', '_', $name);
        $prslig1 = htmlspecialchars(str_replace(' ', '_', trim($name)), ENT_QUOTES);
        $post_id = $post->ID;
        if (isset($get_listing_edit) && is_numeric($get_listing_edit)) {
            $post_id = intval($get_listing_edit);
        }
        $show = 1;
        $i++;

        // Translate label if WPML is active
        if (function_exists('icl_translate')) {
            $label = icl_translate('wpestate', 'wp_estate_property_custom_front_' . $label, $label);
        }

      
        $value = isset($custom_fields_array[$slug]) ? $custom_fields_array[$slug] : '';

        if (is_array($wpestate_submission_page_fields) && (in_array($prslig, $wpestate_submission_page_fields) || in_array($prslig1, $wpestate_submission_page_fields))) {
            $custom_fields_show .= '<div class="col-md-6">';
            $custom_fields_show .= wpestate_show_custom_field(0, $slug, $name, $label, $type, $order, $dropdown_values, $post_id, $value);
            $custom_fields_show .= '</div>';
        }

      
    }
endif;

// Check if property details section should be displayed
$show_property_details = is_array($wpestate_submission_page_fields) && (
    in_array('property_size', $wpestate_submission_page_fields) ||
    in_array('property_lot_size', $wpestate_submission_page_fields) ||
    in_array('property_rooms', $wpestate_submission_page_fields) ||
    in_array('property_bedrooms', $wpestate_submission_page_fields) ||
    in_array('property_bathrooms', $wpestate_submission_page_fields) ||
    in_array('owner_notes', $wpestate_submission_page_fields) ||
    $custom_fields_show != ''
);

if ($show_property_details) : ?>

<div class="profile-onprofile row">
    <div class="wpestate_dashboard_section_title"><?php esc_html_e('Listing Details', 'wpresidence'); ?></div>

    <?php
    // Display property size field
    if (is_array($wpestate_submission_page_fields) && in_array('property_size', $wpestate_submission_page_fields)) : ?>
        <div class="col-md-6">
            <label for="property_size"><?php echo esc_html__('Size in', 'wpresidence') . ' ' . wp_kses_post($measure_sys) . ' ' . esc_html__(' (*only numbers)', 'wpresidence'); ?></label>
            <input type="number" step="any" id="property_size" size="40" class="form-control" name="property_size"
                value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('property_size', $get_listing_edit, 'numeric'))); ?>">
        </div>
    <?php endif; ?>

    <?php
    // Display property lot size field
    if (is_array($wpestate_submission_page_fields) && in_array('property_lot_size', $wpestate_submission_page_fields)) : ?>
        <div class="col-md-6">
            <label for="property_lot_size"><?php echo esc_html__('Lot Size in', 'wpresidence') . ' ' . wp_kses_post($measure_sys_lot_size) . ' ' . esc_html__(' (*only numbers)', 'wpresidence'); ?></label>
            <input type="number" step="any" id="property_lot_size" size="40" class="form-control" name="property_lot_size"
                value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('property_lot_size', $get_listing_edit, 'numeric'))); ?>">
        </div>
    <?php endif; ?>

    <?php
    // Display property rooms field
    if (is_array($wpestate_submission_page_fields) && in_array('property_rooms', $wpestate_submission_page_fields)) : ?>
        <div class="col-md-6">
            <label for="property_rooms"><?php esc_html_e('Rooms (*only numbers)', 'wpresidence'); ?></label>
            <input type="number" step="any" id="property_rooms" size="40" class="form-control" name="property_rooms"
                value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('property_rooms', $get_listing_edit, 'numeric'))); ?>">
        </div>
    <?php endif; ?>

    <?php
    // Display property bedrooms field
    if (is_array($wpestate_submission_page_fields) && in_array('property_bedrooms', $wpestate_submission_page_fields)) : ?>
        <div class="col-md-6">
            <label for="property_bedrooms"><?php esc_html_e('Bedrooms (*only numbers)', 'wpresidence'); ?></label>
            <input type="number" step="any" id="property_bedrooms" size="40" class="form-control" name="property_bedrooms"
                value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('property_bedrooms', $get_listing_edit, 'numeric'))); ?>">
        </div>
    <?php endif; ?>

    <?php
    // Display property bathrooms field
    if (is_array($wpestate_submission_page_fields) && in_array('property_bathrooms', $wpestate_submission_page_fields)) : ?>
        <div class="col-md-6">
            <label for="property_bathrooms"><?php esc_html_e('Bathrooms (*only numbers)', 'wpresidence'); ?></label>
            <input type="number" step="any" id="property_bathrooms" size="40" class="form-control" name="property_bathrooms"
                value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('property_bathrooms', $get_listing_edit, 'numeric'))); ?>">
        </div>
    <?php endif; ?>

    <!-- Display custom fields -->
    <?php echo ($custom_fields_show); ?>

    <?php
    // Display property property_internal_id field
    if (is_array($wpestate_submission_page_fields) && in_array('property_internal_id', $wpestate_submission_page_fields)) : ?>
        <div class="col-md-6">
            <label for="property_internal_id"><?php esc_html_e('Listing ID', 'wpresidence'); ?></label>
            <input type="text" id="property_internal_id" size="40" class="form-control" name="property_internal_id"
                value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('property_internal_id', $get_listing_edit))); ?>">
        </div>
    <?php endif; ?>

    <?php
    // Display owner notes field
    if (is_array($wpestate_submission_page_fields) && in_array('owner_notes', $wpestate_submission_page_fields)) : ?>
        <div class="col-md-12">
            <label for="owner_notes"><?php esc_html_e('Owner/Agent notes (*not visible on front end)', 'wpresidence'); ?></label>
            <textarea id="owner_notes" class="form-control" name="owner_notes"><?php echo esc_textarea(wpestate_submit_return_value('owner_notes', $get_listing_edit, '')); ?></textarea>
        </div>
    <?php endif; ?>

</div>

<?php endif; ?>