<?php
/** MILLDONE
 * WpResidence Theme - Property Location Template
 * stc: templates/submit_templates/property_location.php
 * This template handles the display of property location fields in the property submission form.
 *
 * @package WpResidence
 * @subpackage PropertySubmission
 * @since 1.0.0
 */

// Initialize variables to avoid undefined variable warnings
$property_city = '';
$property_area = '';
$property_county_state = '';
$country_selected = '';
$google_view_check = '';
$property_hide_map_marker_check = '';

$enable_autocomplete_status = esc_html(wpresidence_get_option('wp_estate_enable_autocomplete', ''));

// Check if we're editing an existing listing
if (isset($get_listing_edit) && is_numeric($get_listing_edit)) {
    $post_id = intval($get_listing_edit);
    
    // Get property city
    $property_city_values = get_the_terms($post_id, 'property_city');
    if (!empty($property_city_values) && !is_wp_error($property_city_values)) {
        $property_city = esc_html($property_city_values[0]->name);
    }
    
    // Get property area
    $property_area_values = get_the_terms($post_id, 'property_area');
    if (!empty($property_area_values) && !is_wp_error($property_area_values)) {
        $property_area = esc_html($property_area_values[0]->name);
    }
    
    // Get property county/state
    $property_county_values = get_the_terms($post_id, 'property_county_state');
    if (!empty($property_county_values) && !is_wp_error($property_county_values)) {
        $property_county_state = esc_html($property_county_values[0]->name);
    }
}

// Check if form is submitted and set values
if (isset($_POST['property_city'])) {
    $property_city = wp_kses(esc_html($_POST['property_city']), $allowed_html);
}
if (isset($_POST['property_area'])) {
    $property_area = wp_kses(esc_html($_POST['property_area']), $allowed_html);
}
if (isset($_POST['property_county'])) {
    $property_county_state = wp_kses(esc_html($_POST['property_county']), $allowed_html);
}

$country_selected = wpestate_submit_return_value('property_country', $get_listing_edit, '');
$google_view = wpestate_submit_return_value('property_google_view', $get_listing_edit, '');
$google_view_check = ($google_view == 1) ? ' checked="checked" ' : '';

$property_hide_map_marker = wpestate_submit_return_value('property_hide_map_marker', $get_listing_edit, '');
$property_hide_map_marker_check = ($property_hide_map_marker == 1) ? ' checked="checked" ' : '';

// Check if location fields should be displayed
$display_location = is_array($wpestate_submission_page_fields) && (
    in_array('property_address', $wpestate_submission_page_fields) ||
    in_array('property_city_submit', $wpestate_submission_page_fields) ||
    in_array('property_area', $wpestate_submission_page_fields) ||
    in_array('property_zip', $wpestate_submission_page_fields) ||
    in_array('property_county', $wpestate_submission_page_fields) ||
    in_array('property_country', $wpestate_submission_page_fields) ||
    in_array('property_map', $wpestate_submission_page_fields) ||
    in_array('property_latitude', $wpestate_submission_page_fields) ||
    in_array('property_longitude', $wpestate_submission_page_fields) ||
    in_array('google_camera_angle', $wpestate_submission_page_fields) ||
    in_array('property_google_view', $wpestate_submission_page_fields) ||
    in_array('property_hide_map_marker', $wpestate_submission_page_fields)
);

if ($display_location): ?>
    <div class="profile-onprofile row">
        <div class="wpestate_dashboard_section_title"><?php esc_html_e('Listing Location', 'wpresidence'); ?></div>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('property_address', $wpestate_submission_page_fields)): ?>
            <div class="col-md-12">
                <label for="property_address"><?php esc_html_e('*Address', 'wpresidence'); ?></label>
                <input type="text" id="property_address" class="form-control" name="property_address" placeholder="<?php esc_attr_e('Enter address', 'wpresidence') ?>" value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('property_address', $get_listing_edit, ''))); ?>">
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('property_county', $wpestate_submission_page_fields)): ?>
            <div class="col-md-6">
                <label for="property_county"><?php esc_html_e('County / State', 'wpresidence'); ?></label>
                <?php if ($enable_autocomplete_status == 'no'): ?>
                    <?php
                    wp_dropdown_categories(array(
                        'class' => 'select-submit2',
                        'hide_empty' => false,
                        'name' => 'property_county',
                        'id' => 'property_county',
                        'taxonomy' => 'property_county_state',
                        'selected' => $property_county_state,
                        'hierarchical' => true,
                        'show_option_none' => esc_html__('None', 'wpresidence'),
                        'value_field' => 'name'
                    ));
                    ?>
                <?php else: ?>
                    <input type="text" id="property_county" class="form-control" name="property_county" value="<?php echo esc_attr($property_county_state); ?>">
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('property_city', $wpestate_submission_page_fields)): ?>
            <div class="advanced_city_div col-md-6">
                <label for="property_city_submit"><?php esc_html_e('City', 'wpresidence'); ?></label>
                <?php if ($enable_autocomplete_status == 'no'): ?>
                    <?php
                    $taxonomy = 'property_city';
                    $args_tax = array('hide_empty' => false);
                    $tax_terms = wpestate_get_cached_terms($taxonomy);
                    // had                     <option data-parentcounty="all" value="all"><?php esc_html_e('Cities', 'wpresidence'); </option>
                    ?>
                    <select id="property_city_submit" name="property_city" class="cd-select">
                        <option data-parentcounty="none" value="none"><?php esc_html_e('None', 'wpresidence'); ?></option>
    
                        <?php
                        foreach ($tax_terms as $tax_term) {
                            $term_meta = get_option("taxonomy_$tax_term->term_id");
                            $parent_val = isset($term_meta['stateparent']) ? $term_meta['stateparent'] : '';
                            echo '<option value="' . esc_attr($tax_term->name) . '" data-parentcounty="' . esc_attr($parent_val) . '"' . selected($property_city, $tax_term->name, false) . '>' . esc_html($tax_term->name) . '</option>';
                        }
                        ?>
                    </select>
                <?php else: ?>
                    <input type="text" id="property_city_submit" name="property_city" class="form-control" placeholder="<?php esc_attr_e('Enter city', 'wpresidence') ?>" value="<?php echo esc_attr($property_city); ?>">
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('property_area', $wpestate_submission_page_fields)): ?>
            <div class="advanced_area_div col-md-6">
                <label for="property_area_submit"><?php esc_html_e('Neighborhood', 'wpresidence'); ?></label>
                <?php if ($enable_autocomplete_status == 'no'): ?>
                    <?php
                    $taxonomy = 'property_area';
                    $args_tax = array('hide_empty' => false);
                    $tax_terms = wpestate_get_cached_terms($taxonomy);
                    // had  <option data-parentcity="all" value="all"><?php esc_html_e('Areas', 'wpresidence'); </option>
                    ?>
                    <select id="property_area_submit" name="property_area" class="cd-select">
                        <option data-parentcity="none" value="none"><?php esc_html_e('None', 'wpresidence'); ?></option>
                       
                        <?php
                        foreach ($tax_terms as $tax_term) {
                            $term_meta = get_option("taxonomy_$tax_term->term_id");
                            $parent_city = isset($term_meta['cityparent']) ? $term_meta['cityparent'] : '';
                            echo '<option value="' . esc_attr($tax_term->name) . '" data-parentcity="' . esc_attr($parent_city) . '"' . selected($property_area, $tax_term->name, false) . '>' . esc_html($tax_term->name) . '</option>';
                        }
                        ?>
                    </select>
                <?php else: ?>
                    <input type="text" id="property_area_submit" name="property_area" class="form-control" value="<?php echo esc_attr($property_area); ?>">
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('property_zip', $wpestate_submission_page_fields)): ?>
            <div class="col-md-6">
                <label for="property_zip"><?php esc_html_e('Zip', 'wpresidence'); ?></label>
                <input type="text" id="property_zip" class="form-control" name="property_zip" value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('property_zip', $get_listing_edit, ''))); ?>">
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('property_country', $wpestate_submission_page_fields)): ?>
            <div class="col-md-6">
                <label for="property_country"><?php esc_html_e('Country', 'wpresidence'); ?></label>
                <?php print wpestate_country_list($country_selected, 'select-submit2'); ?>
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('property_map', $wpestate_submission_page_fields)): ?>
            <?php if ($enable_autocomplete_status == 'no'): ?>
                <div class="col-md-12" style="float:left;">
                    <button id="google_capture" class="wpresidence_button wpresidence_success"><?php esc_html_e('Place Pin with Property Address', 'wpresidence'); ?></button>
                </div>
            <?php endif; ?>
            <div class="col-md-12">
                <div id="googleMapsubmit"></div>
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('property_latitude', $wpestate_submission_page_fields)): ?>
            <div class="col-md-6">
                <label for="property_latitude"><?php esc_html_e('Latitude (for Maps Coordinates)', 'wpresidence'); ?></label>
                <input type="text" id="property_latitude" class="form-control" name="property_latitude" value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('property_latitude', $get_listing_edit, 'numeric'))); ?>">
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('property_longitude', $wpestate_submission_page_fields)): ?>
            <div class="col-md-6">
                <label for="property_longitude"><?php esc_html_e('Longitude (for Maps Coordinates)', 'wpresidence'); ?></label>
                <input type="text" id="property_longitude" class="form-control" name="property_longitude" value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('property_longitude', $get_listing_edit, 'numeric'))); ?>">
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('google_camera_angle', $wpestate_submission_page_fields)): ?>
            <div class="col-md-6">
                <label for="google_camera_angle"><?php esc_html_e('Google Street View - Camera Angle (value from 0 to 360)', 'wpresidence'); ?></label>
                <input type="text" id="google_camera_angle" class="form-control" name="google_camera_angle" value="<?php echo esc_attr(stripslashes(wpestate_submit_return_value('google_camera_angle', $get_listing_edit, ''))); ?>">
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('property_google_view', $wpestate_submission_page_fields)): ?>
            <div class="col-md-6" style="height:100px;"></div>
            <div class="col-md-6">
                <input type="hidden" name="property_google_view" value="">
                <input type="checkbox" id="property_google_view" name="property_google_view" value="1" <?php echo esc_attr($google_view_check); ?>>
                <label for="property_google_view" class="wpestate_check_label"><?php esc_html_e('Enable Google Street View', 'wpresidence'); ?></label>
            </div>
        <?php endif; ?>

        <?php if (is_array($wpestate_submission_page_fields) && in_array('property_hide_map_marker', $wpestate_submission_page_fields)): ?>
            <div class="col-md-6">
                <input type="hidden" name="property_hide_map_marker" value="">
                <input type="checkbox" id="property_hide_map_marker" name="property_hide_map_marker" value="1" <?php echo esc_attr($property_hide_map_marker_check); ?>>
                <label for="property_hide_map_marker" class="wpestate_check_label"><?php esc_html_e('Hide Map Marker', 'wpresidence'); ?></label>
            </div>
        <?php endif; ?>

    </div>
<?php endif; ?>