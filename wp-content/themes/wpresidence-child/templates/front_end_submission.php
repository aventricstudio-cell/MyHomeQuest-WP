<?php
/**
 * WpResidence Theme - Add/Edit Property Template
 * src: templates/front_end_submission.php
 */

// Check if user has the right to add/edit properties
$parent_userID      = wpestate_check_for_agency($userID);
$images_to_show     = '';
$remaining_listings = wpestate_get_remain_listing_user($parent_userID, $user_pack);

if ($remaining_listings === -1) {
    $remaining_listings = 11;
}

$paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));

if (!isset($_GET['listing_edit']) && $paid_submission_status == 'membership' && $remaining_listings != -1 && $remaining_listings < 1) {
    echo '<div class="user_profile_div not_allow_submit"><h4>' . esc_html__('Your current package doesn\'t let you publish more properties! You need to upgrade your membership.', 'wpresidence') . '</h4></div>';
} else {
    // Get mandatory fields
    $mandatory_fields = wpresidence_get_option('wp_estate_mandatory_page_fields', '');
    if (is_array($mandatory_fields)) {
        $mandatory_fields = array_map("wpestate_strip_array", $mandatory_fields);
    }
    if (is_array($mandatory_fields) && !empty($mandatory_fields)) {
        $all_mandatory_fields = wpestate_return_all_fields(1);
    }
    ?>

    <form id="new_post" name="new_post" method="post" action="" enctype="multipart/form-data" class="row add-estate">
        <?php wp_nonce_field('dashboard_property_front_action', 'dashboard_property_front_nonce'); ?>

                    <?php 
            session_start();
            if (!empty($_SESSION['property_error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['property_error'] . '</div>';
                unset($_SESSION['property_error']); 
            }
            ?>


        <div class="col-md-12 row_dasboard-prop-listing">
            <?php
            if ($wpestate_show_err) {
                echo '<div class="alert alert-danger">' . $wpestate_show_err . '</div>';
            }
            ?>
        </div>

        <div class="col-12 col-md-12 col-lg-12 wpestate_dash_coluns">
            <div class="wpestate_dashboard_content_wrapper">
                <?php
                echo '<div class="submit_mandatory">';
                esc_html_e('These fields are mandatory: Title', 'wpresidence');
                if (is_array($mandatory_fields)) {
                    foreach ($mandatory_fields as $value) {
                        echo ', ' . $all_mandatory_fields[$value];
                    }
                }
                echo '</div>';

                include(locate_template('templates/submit_templates/property_description.php'));
                include(locate_template('templates/submit_templates/property_categories.php'));
                include(locate_template('templates/submit_templates/property_location.php'));
                include(locate_template('templates/submit_templates/property_energy_effective.php'));
                include(locate_template('templates/submit_templates/property_details.php'));
                include(locate_template('templates/submit_templates/property_status.php'));
                include(locate_template('templates/submit_templates/property_amenities.php'));
                include(locate_template('templates/submit_templates/property_subunits.php'));
                ?>

                <div class="profile-onprofile row submitrow">
                    <input type="hidden" name="action" value="<?php echo esc_attr($action); ?>">
                    <?php if ($action == 'edit') : ?>
                        <input type="submit" class="wpresidence_button" id="form_submit_1" value="<?php esc_attr_e('Save Changes', 'wpresidence') ?>" />
                        <input type="submit" class="wpresidence_button" name="save_draft" id="form_submit_2" value="<?php esc_attr_e('Save as Draft', 'wpresidence') ?>" />
                    <?php else : ?>
                        <input type="submit" class="wpresidence_button" name="submit_prop" id="form_submit_1" value="<?php esc_attr_e('Add Property', 'wpresidence') ?>" />
                        <input type="submit" class="wpresidence_button" name="save_draft" id="form_submit_2" value="<?php esc_attr_e('Save as Draft', 'wpresidence') ?>" />
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <input type="hidden" name="edit_id" value="<?php echo intval($edit_id); ?>">
        <input type="hidden" name="images_todelete" id="images_todelete" value="">
        <?php wp_nonce_field('submit_new_estate', 'new_estate'); ?>
    </form>
<?php
}
?>
