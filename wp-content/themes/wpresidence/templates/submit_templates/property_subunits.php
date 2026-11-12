<?php
/** MILLDONE
 * WpResidence Theme - Property Subunits Template
 * src: templates/submit_templates/property_subunits.php
 * This template handles the display of property subunits in the property submission form.
 *
 * @package WpResidence
 * @subpackage PropertySubmission
 * @since 1.0.0
 */

// Initialize variables to avoid undefined variable warnings
$edit_id = 0;
$property_has_subunits = 0;
$property_subunits_list = array();

// Check if we're editing an existing listing
if (isset($_GET['listing_edit']) && is_numeric($_GET['listing_edit'])) {
    $edit_id = intval($_GET['listing_edit']);
    $property_has_subunits = intval(get_post_meta($edit_id, 'property_has_subunits', true));
    $property_subunits_list = get_post_meta($edit_id, 'property_subunits_list', true);
}

// Check if form is submitted and set values
if (isset($_POST['property_subunits_list'])) {
    $property_subunits_list = $_POST['property_subunits_list'];
}
if (isset($_POST['property_has_subunits'])) {
    $property_has_subunits = intval($_POST['property_has_subunits']);
}

// Ensure $property_subunits_list is always an array
if (!is_array($property_subunits_list)) {
    $property_subunits_list = array();
}

// Check if subunits field should be displayed
if (is_array($wpestate_submission_page_fields) && in_array('property_subunits_list', $wpestate_submission_page_fields)):
?>
    <div class="profile-onprofile row">
        <div class="wpestate_dashboard_section_title"><?php esc_html_e('Subunits', 'wpresidence'); ?></div>
        <div class="col-md-3">
            <input type="hidden" name="property_has_subunits" value="0">
            <input type="checkbox" id="property_has_subunits" name="property_has_subunits" value="1" <?php checked($property_has_subunits, 1); ?>>
            <label class="checklabel" for="property_has_subunits"><?php esc_html_e('Enable', 'wpresidence'); ?></label>
        </div>
        <div class="col-md-9">
            <label for="property_subunits_list"><?php esc_html_e('Select Subunits From the list:', 'wpresidence'); ?></label>
            <?php
            $current_user = wp_get_current_user();
            $userID = $current_user->ID;
            $post__not_in = array($edit_id);
            $args = array(
                'post_type' => 'estate_property',
                'post_status' => 'publish',
                'nopaging' => true,
                'post__not_in' => $post__not_in,
                'author' => $userID,
                'cache_results' => false,
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
            );
            $recent_posts = new WP_Query($args);
            ?>
            <select name="property_subunits_list[]" id="property_subunits_list" multiple="multiple" style="height:350px;">
                <?php
                while ($recent_posts->have_posts()): $recent_posts->the_post();
                    $theid = get_the_ID();
                    ?>
                    <option value="<?php echo esc_attr($theid); ?>" <?php selected(in_array($theid, $property_subunits_list), true); ?>>
                        <?php echo esc_html(get_the_title()); ?>
                    </option>
                <?php endwhile;
                wp_reset_postdata();
                ?>
            </select>
        </div>
    </div>
<?php endif; ?>