<?php
/** MILLDONE
 * WpResidence Theme - Property Images Template
 * src: templates/submit_templates/property_images.php
 * This template handles the display of property images in the property submission form.
 *
 * @package WpResidence
 * @subpackage PropertySubmission
 * @since 1.0.0
 */

// Initialize variables
$images = '';
$thumbid = '';
$attachid = '';
$post_attachments = array();
$post_attachments_ids = '';
$edit_id = isset($_GET['listing_edit']) ? intval($_GET['listing_edit']) : 0;
$use_floor_plans = get_post_meta($edit_id, 'use_floor_plans', true);

if (is_array($wpestate_submission_page_fields) && in_array('attachid', $wpestate_submission_page_fields)) {
    // Handle attachments
    if (isset($attchs) && is_array($attchs)) {
        $attachid = implode(',', $attchs);
    }

    if ( isset($action) && $action == 'edit') {
        wp_reset_postdata();
        wp_reset_query();
        $max_images = intval(wpresidence_get_option('wp_estate_prop_image_number', ''));

        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $parent_userID = wpestate_check_for_agency($userID);
        $paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));
        
        if ($paid_submission_status == 'membership') {
            $user_pack = get_the_author_meta('package_id', $parent_userID);
            $max_images = get_post_meta($user_pack, 'pack_image_included', true);
        }

        $max_images = ($max_images == 0) ? 100 : $max_images;

        $post_attachments = wpestate_generate_property_slider_image_ids($edit_id);
    }

    if (isset($_POST['attachid'])) {
        $attachid = trim($_POST['attachid'], ',');
    }

    if (isset($_POST['attachthumb'])) {
        $thumbid = intval($_POST['attachthumb']);
    }
    ?>

    <div class="profile-onprofile row">
        <div class="wpestate_dashboard_section_title"><?php esc_html_e('Listing Media', 'wpresidence'); ?></div>

        <div class="submit_container wpestate_dashboard_add_images">
            <div id="upload-container">
                <div id="aaiu-upload-container">
                    <div id="aaiu-upload-imagelist">
                        <ul id="aaiu-ul-list" class="aaiu-upload-list"></ul>
                    </div>

                    <div id="imagelist">
                        <?php
                        $ajax_nonce = wp_create_nonce("wpestate_image_upload");
                        echo '<input type="hidden" id="wpestate_image_upload" value="' . esc_attr($ajax_nonce) . '" />';
                        
                        if (!empty($images)) {
                            echo trim($images);
                        }

                        if (!empty($post_attachments)) {
                            foreach ($post_attachments as $att_id) {
                                if ($att_id != '' && is_numeric($att_id)) {
                                    $post_attachments_ids .= $att_id . ',';
                                    $preview = wp_get_attachment_image_src($att_id, 'agent_picture_thumb');

                                    if (wp_attachment_is_image($att_id)) {
                                        $images .= '<div class="uploaded_images" data-imageid="' . esc_attr($att_id) . '">';
                                        $images .= '<img src="' . esc_url($preview[0]) . '" alt="' . esc_attr__('user image', 'wpresidence') . '" />';
                                        $images .= '<i class="far fa-trash-alt"></i>';
                                        $images .= '<i class="fas fa-font image_caption_button"></i>';
                                        $images .= '<div class="image_caption_wrapper">';
                                        $images .= '<input data-imageid="' . esc_attr($att_id) . '" type="text" class="image_caption form_control" name="image_caption" value="' . esc_attr(wp_get_attachment_caption($att_id)) . '">';
                                        $images .= '</div>';
                                        
                                        if ($thumbid == $att_id) {
                                            $images .= '<i class="fa thumber fa-star"></i>';
                                        }
                                        
                                        $images .= '</div>';
                                    } else {
                                        $images .= '<div class="uploaded_images" data-imageid="' . esc_attr($att_id) . '">';
                                        $images .= '<img src="' . esc_url(get_theme_file_uri('/img/pdf.png')) . '" alt="' . esc_attr__('user image', 'wpresidence') . '" />';
                                        $images .= '<i class="far fa-trash-alt"></i>';
                                        $images .= '</div>';
                                    }
                                }
                            }
                            echo trim($images);
                        }
                        ?>
                    </div>

                    <div id="drag-and-drop" class="rh_drag_and_drop_wrapper">
                        <div class="drag-drop-msg"><i class="fas fa-cloud-upload-alt"></i><?php esc_html_e('Drag and Drop Images or ', 'wpresidence'); ?></div>
                        <button id="aaiu-uploader" type="button" class="wpresidence_button wpresidence_success">
                            <?php esc_html_e('Select Media', 'wpresidence'); ?>
                        </button>
                    </div>

                    <p class="full_form full_form_image">
                        <?php
                        esc_html_e('* At least 1 image is required for a valid submission. Minimum size is 500/500px.', 'wpresidence');
                        echo '<br>';

                        $current_user = wp_get_current_user();
                        $userID = $current_user->ID;
                        $parent_userID = wpestate_check_for_agency($userID);

                        $max_images = intval(wpresidence_get_option('wp_estate_prop_image_number', ''));
                        $paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));
                        
                        if ($paid_submission_status == 'membership') {
                            $user_pack = get_the_author_meta('package_id', $parent_userID);
                            if ($user_pack != '') {
                                $max_images = get_post_meta($user_pack, 'pack_image_included', true);
                            } else {
                                $max_images = intval(wpresidence_get_option('wp_estate_free_pack_image_included', ''));
                            }
                        }

                        if ($max_images != 0) {
                            printf(esc_html__('You can upload maximum %s images', 'wpresidence'), $max_images);
                        }
                        echo '<br>';
                        esc_html_e('** Double click on the image to select featured.', 'wpresidence');
                        echo '<br>';
                        esc_html_e('*** Change images order with Drag & Drop.', 'wpresidence');
                        echo '<br>';
                        esc_html_e('**** PDF files upload supported as well.', 'wpresidence');
                        echo '<br>';
                        esc_html_e('***** Images might take longer to be processed.', 'wpresidence');
                        ?>
                    </p>

                    <input type="hidden" name="attachid" id="attachid" value="<?php echo esc_attr($post_attachments_ids); ?>">
                    <input type="hidden" name="attachthumb" id="attachthumb" value="<?php echo esc_attr($thumbid); ?>">
                </div>
            </div>
        </div>
    </div>

<?php
}
?>