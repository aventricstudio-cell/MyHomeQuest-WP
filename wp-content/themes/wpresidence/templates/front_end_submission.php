<?php
/**
 * WpResidence Theme - Add/Edit Property Template
 * src: templates/front_end_submission.php
 * This template handles the form for adding a new property or editing an existing one
 * in the user dashboard of the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage UserDashboard
 * @since 1.0.0
 *
 * Dependencies:
 * - WpResidence theme functions
 * - WordPress core functions
 * - Various submit templates (property_description.php, property_categories.php, etc.)
 */

// Check if user has the right to add/edit properties
$parent_userID      = wpestate_check_for_agency($userID);
$images_to_show     = '';
$remaining_listings = wpestate_get_remain_listing_user($parent_userID, $user_pack);

if ($remaining_listings === -1) {
    $remaining_listings = 11;
}

$paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));

// Check if user can submit more properties
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
        
        <?php if (esc_html(wpresidence_get_option('wp_estate_paid_submission', '')) == 'yes') : ?>
            <br><?php esc_html_e('This is a paid submission. The listing will be live after payment is received.', 'wpresidence'); ?>
        <?php endif; ?>

        <div class="col-md-12 row_dasboard-prop-listing">
            <?php
            if ($wpestate_show_err) {
                echo '<div class="alert alert-danger">' . $wpestate_show_err . '</div>';
            }
            ?>
        </div>

        <?php     $get_listing_edit = isset($_GET['listing_edit']) ? intval($_GET['listing_edit']) : ''; ?>
       
            <div class="col-12 col-md-12 col-lg-7 wpestate_dash_coluns">
                <div class="wpestate_dashboard_content_wrapper">
                    <?php
                    // Display mandatory fields message
                    echo '<div class="submit_mandatory">';
                    esc_html_e('These fields are mandatory: Title', 'wpresidence');
                    if (is_array($mandatory_fields)) {
                        foreach ($mandatory_fields as $value) {
                            echo ', ' . $all_mandatory_fields[$value];
                        }
                    }
                    echo '</div>';

                    // Include property templates
                    include(locate_template('templates/submit_templates/property_description.php'));
                    include(locate_template('templates/submit_templates/property_categories.php'));
                    include(locate_template('templates/submit_templates/property_location.php'));
                    include(locate_template('templates/submit_templates/property_energy_effective.php'));
                    include(locate_template('templates/submit_templates/property_details.php'));
                    include(locate_template('templates/submit_templates/property_status.php'));
                    include(locate_template('templates/submit_templates/property_amenities.php'));
                    include(locate_template('templates/submit_templates/property_subunits.php'));
                    ?>

                    <?php 
                    if ( !wp_is_mobile() ) {  ?>
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
                    <?php 
                    } 
                    ?>
                </div>
            </div>

            <div class="col-12 col-md-12 col-lg-5  wpestate_dash_coluns">
                <div class="wpestate_dashboard_content_wrapper">
                    <?php include(locate_template('templates/submit_templates/property_images.php')); ?>
                </div>

                <div class="wpestate_dashboard_content_wrapper">
                    <?php include(locate_template('templates/submit_templates/property_floor_plans.php')); ?>
                </div>


                <div class="wpestate_dashboard_content_wrapper">
                    <?php
                    include(locate_template('templates/submit_templates/video_tour.php'));
                    include(locate_template('templates/submit_templates/property_video.php'));
                    ?>
                </div>

            </div>

           
     
        <?php   
        if ( wp_is_mobile() ) { ?>

            <div class="profile-onprofile row m-0 p-3  submitrow">
                <input type="hidden" name="action" value="<?php echo esc_attr($action); ?>">
                <?php if ($action == 'edit') : ?>
                    <input type="submit" class="wpresidence_button mx-0" style="width:100%;" id="form_submit_1" value="<?php esc_attr_e('Save Changes', 'wpresidence') ?>" />
                    <input type="submit" class="wpresidence_button mx-0" style="width:100%;" name="save_draft" id="form_submit_2" value="<?php esc_attr_e('Save as Draft', 'wpresidence') ?>" />
                <?php else : ?>
                    <input type="submit" class="wpresidence_button mx-0" style="width:100%;" name="submit_prop" id="form_submit_1" value="<?php esc_attr_e('Add Property', 'wpresidence') ?>" />
                    <input type="submit" class="wpresidence_button mx-0" style="width:100%;" name="save_draft" id="form_submit_2" value="<?php esc_attr_e('Save as Draft', 'wpresidence') ?>" />
                <?php endif; ?>
            </div>
        


            <?php
        } 
        ?>

        <input type="hidden" name="edit_id" value="<?php echo intval($edit_id); ?>">
        <input type="hidden" name="images_todelete" id="images_todelete" value="">
        <?php wp_nonce_field('submit_new_estate', 'new_estate'); ?>
    </form>
<?php
}
?>