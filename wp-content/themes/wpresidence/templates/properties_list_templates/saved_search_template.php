<?php
/**
 * Save Search Feature for WpResidence Theme
 *
 * This file handles the display of the save search feature on property listing pages.
 * It allows logged-in users to save their search parameters and displays a login prompt for non-logged-in users.
 *
 * @package WpResidence
 * @subpackage PropertySearch
 * @since 1.0.0
 */

// Check if the save search feature is enabled in theme options
$show_save_search = wpresidence_get_option('wp_estate_show_save_search', '');

if ($show_save_search === 'yes') {
    if (is_user_logged_in()) {
        // Display save search form for logged-in users
        ?>
        <div class="search_unit_wrapper advanced_search_notice">
            <div class="search_param">
                <strong><?php esc_html_e('Search Parameters: ', 'wpresidence'); ?></strong>
                <?php
                echo wpestate_show_search_params_new(
                    $wpestate_included_ids,
                    $args,
                    $custom_advanced_search,
                    $adv_search_what,
                    $adv_search_how,
                    $adv_search_label
                );
                ?>
            </div>
        </div>
        <div class="saved_search_wrapper">
            <span id="save_search_notice"><?php esc_html_e('Save this Search?', 'wpresidence'); ?></span>
            <input type="text" id="search_name" class="form-control" placeholder="<?php esc_attr_e('Search name', 'wpresidence'); ?>">
            <button class="wpresidence_button" id="save_search_button"><?php esc_html_e('Save Search', 'wpresidence'); ?></button>
            <input type="hidden" id="search_args" value="<?php echo esc_attr(wp_json_encode($args)); ?>">
            <input type="hidden" id="meta_args" value="<?php echo esc_attr(wp_json_encode($wpestate_included_ids)); ?>">
            <input type="hidden" name="save_search_nonce" id="wpestate_save_search_nonce" value="<?php echo wp_create_nonce('wpestate_save_search_nonce'); ?>">
        </div>
        <?php
    } else {
        // Display login prompt for non-logged-in users
        ?>
        <div class="vc_row wpb_row vc_row-fluid vc_row">
            <div class="vc_col-sm-12 wpb_column vc_column_container vc_column">
                <div class="wpb_wrapper">
                    <div class="wpb_alert wpb_content_element vc_alert_rounded wpb_alert-info wpestate_message vc_message">
                        <div class="messagebox_text">
                            <p>
                                <span id="login_trigger_modal"><?php esc_html_e('Login', 'wpresidence'); ?></span>
                                <?php esc_html_e('to save search and you will receive an email notification when new properties matching your search will be published.', 'wpresidence'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}