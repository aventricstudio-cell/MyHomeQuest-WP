<?php
/** MILLDONE
 * Floor Plans Management for WpResidence Theme
 * src: templates/submit_templates/property_floor_plans.php
 * This file contains the code for managing floor plans in the WpResidence WordPress theme.
 * It allows users to add, edit, and display floor plan information for real estate listings.
 *
 * @package WpResidence
 * @subpackage FloorPlans
 * @since 1.0.0
 */

// Initialize variables
$images = '';
$thumbid = '';
$attachid = '';

// Start of the floor plans container
?>
<div class="submit_container">
    <div class="submit_container_header"><?php esc_html_e('Floor Plans', 'wpresidence'); ?></div>
    
    <?php
    // Check if we're adding a new plan or editing an existing one
    if ($action != 'edit') {
        // Form for adding a new floor plan
        ?>
        <div class="plan_row">
            <p class="full_form floor_p">
                <label for="plan_title"><?php esc_html_e('Plan Title', 'wpresidence'); ?></label><br />
                <input id="plan_title" type="text" size="36" name="plan_title[]" value="" />
            </p>

            <p class="full_form floor_p">
                <label for="plan_description"><?php esc_html_e('Plan Description', 'wpresidence'); ?></label><br />
                <textarea class="plan_description" name="plan_description[]"></textarea>
            </p>

            <p class="half_form floor_p">
                <label for="plan_size"><?php esc_html_e('Plan Size', 'wpresidence'); ?></label><br />
                <input id="plan_size" type="text" size="36" name="plan_size[]" value="" />
            </p>

            <p class="half_form floor_p half_form_last">
                <label for="plan_rooms"><?php esc_html_e('Plan Rooms', 'wpresidence'); ?></label><br />
                <input id="plan_rooms" type="text" size="36" name="plan_rooms[]" value="" />
            </p>

            <p class="half_form floor_p">
                <label for="plan_bath"><?php esc_html_e('Plan Bathrooms', 'wpresidence'); ?></label><br />
                <input id="plan_bath" type="text" size="36" name="plan_bath[]" value="" />
            </p>

            <p class="half_form floor_p half_form_last">
                <label for="plan_price"><?php esc_html_e('Plan Price', 'wpresidence'); ?></label><br />
                <input id="plan_price" type="text" size="36" name="plan_price[]" value="" />
            </p>
            
            <p class="full_form floor_p">
                <label for="plan_image"><?php esc_html_e('Plan Image', 'wpresidence'); ?></label><br />
                <input id="plan_image" type="text" size="36" name="plan_image[]" value="" />
                <input id="plan_image_button" type="button" class="upload_button button floorbuttons" value="<?php esc_attr_e('Upload Image', 'wpresidence'); ?>" />
            </p>

            <!-- Image upload container -->
            <div id="upload-container">
                <div id="aaiu-upload-container">
                    <div id="aaiu-upload-imagelist">
                        <ul id="aaiu-ul-list" class="aaiu-upload-list"></ul>
                    </div>

                    <div id="imagelist">
                        <?php
                        $ajax_nonce = wp_create_nonce("wpestate_image_upload");
                        echo '<input type="hidden" id="wpestate_image_upload" value="' . esc_attr($ajax_nonce) . '" />';
                        if ($images != '') {
                            echo wp_kses_post(trim($images));
                        }
                        ?>
                    </div>

                    <button type="button" id="aaiu-uploader-floor" class="wpresidence_button wpresidence_success"><?php esc_html_e('Select Images', 'wpresidence'); ?></button>
                    <input type="hidden" name="attachid" id="attachid" value="<?php echo esc_attr($attachid); ?>">
                    <input type="hidden" name="attachthumb" id="attachthumb" value="<?php echo esc_attr($thumbid); ?>">
                </div>
            </div>
        </div>
        <?php
    } else {
        // Form for editing existing floor plans
        if (is_array($plan_title_array)) {
            foreach ($plan_title_array as $key => $plan_name) {
                // Retrieve plan details or set defaults
                $plan_desc = isset($plan_desc_array[$key]) ? $plan_desc_array[$key] : '';
                $plan_img = isset($plan_image_array[$key]) ? $plan_image_array[$key] : '';
                $plan_size = isset($plan_size_array[$key]) ? $plan_size_array[$key] : '';
                $plan_rooms = isset($plan_rooms_array[$key]) ? $plan_rooms_array[$key] : '';
                $plan_bath = isset($plan_bath_array[$key]) ? $plan_bath_array[$key] : '';
                $plan_price = isset($plan_price_array[$key]) ? $plan_price_array[$key] : '';
                ?>
                <div class="plan_row">
                    <p class="meta-options floor_p">
                        <label for="plan_title"><?php esc_html_e('Plan Title', 'wpresidence'); ?></label><br />
                        <input id="plan_title" type="text" size="36" name="plan_title[]" value="<?php echo esc_attr($plan_name); ?>" />
                    </p>

                    <p class="meta-options floor_p">
                        <label for="plan_description"><?php esc_html_e('Plan Description', 'wpresidence'); ?></label><br />
                        <textarea class="plan_description" name="plan_description[]"><?php echo esc_textarea($plan_desc); ?></textarea>
                    </p>

                    <p class="meta-options floor_p">
                        <label for="plan_image"><?php esc_html_e('Plan Image', 'wpresidence'); ?></label><br />
                        <input id="plan_image" type="text" size="36" name="plan_image[]" value="<?php echo esc_url($plan_img); ?>" />
                        <input id="plan_image_button" type="button" class="upload_button button floorbuttons" value="<?php esc_attr_e('Upload Image', 'wpresidence'); ?>" />
                    </p>

                    <p class="meta-options floor_p">
                        <label for="plan_size"><?php esc_html_e('Plan Size', 'wpresidence'); ?></label><br />
                        <input id="plan_size" type="text" size="36" name="plan_size[]" value="<?php echo esc_attr($plan_size); ?>" />
                    </p>

                    <p class="meta-options floor_p">
                        <label for="plan_rooms"><?php esc_html_e('Plan Rooms', 'wpresidence'); ?></label><br />
                        <input id="plan_rooms" type="text" size="36" name="plan_rooms[]" value="<?php echo esc_attr($plan_rooms); ?>" />
                    </p>

                    <p class="meta-options floor_p">
                        <label for="plan_bath"><?php esc_html_e('Plan Bathrooms', 'wpresidence'); ?></label><br />
                        <input id="plan_bath" type="text" size="36" name="plan_bath[]" value="<?php echo esc_attr($plan_bath); ?>" />
                    </p>

                    <p class="meta-options floor_p">
                        <label for="plan_price"><?php esc_html_e('Plan Price', 'wpresidence'); ?></label><br />
                        <input id="plan_price" type="text" size="36" name="plan_price[]" value="<?php echo esc_attr($plan_price); ?>" />
                    </p>
                </div>
                <?php
            }
        }
    }
    ?>
</div>