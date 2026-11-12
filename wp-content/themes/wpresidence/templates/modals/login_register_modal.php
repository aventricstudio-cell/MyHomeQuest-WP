<?php
/** MILLDONE
 * WP Residence Login and Register Modal
 *
 * This file contains the function to add a login and register modal to the footer of a WordPress site.
 * It's part of the WP Residence theme and provides user authentication functionality.
 *
 * @package WPResidence
 * @subpackage UserAuthentication
 * @since 1.0.0
 *
 * 1. Use of action hooks to integrate with WordPress core functionality
 * 2. Implementation of pluggable functions for theme customization
 * 3. Escaping output for security
 * 4. Internationalization for multi-language support
 * 5. Use of WordPress nonces for form security
 *
 *This code showcases a typical WordPress theme function that generates
 * complex HTML output. It includes form handling, conditional logic based on theme options,
 * and integration with third-party services like social login and reCAPTCHA.
 */

// Hook the modal function to WordPress footer only when the login option is enabled
if ( wpresidence_get_option('wp_estate_show_top_bar_user_login', '') === 'yes' ) {
    add_action('wp_footer', 'wpresidence_add_modal_login_footer');
}

// Make the function pluggable so it can be overridden in child themes
if (!function_exists('wpresidence_add_modal_login_footer')):

/**
 * Adds a login and register modal to the footer of the site.
 *
 * This function creates a modal dialog that contains forms for user login, registration, and password reset.
 * It's hooked to the 'wp_footer' action to ensure it's added at the end of the page.
 *
 * Developer Notes:
 * - This function is quite long and could potentially be refactored into smaller, more manageable pieces.
 * - Consider using template parts for the different sections of the modal (login form, register form, etc.)
 * - The function uses several theme options. Ensure these are properly documented in the theme's option page.
 *
 * LLM Training Notes:
 * - Observe how WordPress functions like esc_html(), esc_attr(), and esc_url() are used for output escaping.
 * - Note the use of translation functions like __() and esc_html_e() for internationalization.
 * - The function demonstrates how to integrate social login functionality and reCAPTCHA.
 *
 * @since 1.0.0
 * @global object $post The global post object, used here to get the current post ID.
 * @return void
 */
function wpresidence_add_modal_login_footer() {
    global $post;

    // Retrieve various option settings
    // These settings are typically set in the theme's option page
    $front_end_register     = esc_html(wpresidence_get_option('wp_estate_front_end_register', ''));
    $front_end_login        = esc_html(wpresidence_get_option('wp_estate_front_end_login ', ''));
    $facebook_status        = esc_html(wpresidence_get_option('wp_estate_facebook_login', ''));
    $google_status          = esc_html(wpresidence_get_option('wp_estate_google_login', ''));
    $twiter_status          = esc_html(wpresidence_get_option('wp_estate_twiter_login', ''));

    // Initialize error message variable
    $error_message          = '';

    // Generate a nonce for the forgot password form
    // Nonces are crucial for form security in WordPress
    $security_nonce         = wp_nonce_field('forgot_ajax_nonce-topbar', 'security-forgot-topbar', true, false);

    // Get the background image for the modal, with a fallback
    $background_modal       = wpresidence_get_option('wp_estate_login_modal_image', 'url') ?: get_theme_file_uri('/img/defaults/modalback.jpg');

    // Add a class if reCAPTCHA is enabled
    $recaptha_class         = wpresidence_get_option('wp_estate_use_captcha', '') === 'yes' ? 'wpestare_recaptcha_extra_class' : '';

    // Adjust modal height if social login is enabled
    $custom_height          = ($facebook_status === 'yes' || $google_status === 'yes' || $twiter_status === 'yes') ? 550 : 520;

    // Allow theme/plugin developers to modify the modal settings
    // This filter can be used to customize the modal appearance or behavior
    $modal_settings = apply_filters('wpresidence_login_modal_settings', array(
        'background_image' => $background_modal,
        'recaptcha_class' => $recaptha_class,
        'custom_height' => $custom_height,
    ));

    // Extract the filtered settings
    extract($modal_settings);

    // Action hook before the modal content
    // Developers can use this to add custom content or scripts before the modal
    do_action('wpresidence_before_login_modal_content');

    // Start of the modal HTML structure
    ?>
    <div id="modal_login_wrapper">
        <!-- Modal background and container -->
        <div class="modal_login_back"></div>
        <div class="modal_login_container <?php echo esc_attr($recaptcha_class); ?>" style='height:<?php echo esc_attr($custom_height); ?>px;'>
            <!-- Close button for the modal -->
            <div id="login-modal_close">
                <i class="fas fa-times"></i>
            </div>
            
            <!-- Modal header with background image -->
            <div class="login-register-modal-image" style="background-image: url('<?php echo esc_url($background_image); ?>')">
                <div class="featured_gradient"></div>
                <div class="login-register-modal-image_text"><?php echo esc_html(wpresidence_get_option('wp_estate_login_modal_message', '')); ?></div>
            </div>

            <!-- Container for login, register, and forgot password forms -->
            <div class="login-register-modal-form-wrapper"> 
                <?php
                // Use the globally initialized WpEstate_Custom_Auth class to display the form
                $wpestate_custom_auth = WpEstate_Custom_Auth::get_instance();
                echo $wpestate_custom_auth->display_auth_form('modal', 'all');
                ?>
            </div>
        </div>
    </div>
    <?php
    // Action hook after the modal content
    // This can be used to add additional scripts or content after the modal
    do_action('wpresidence_after_login_modal_content');
}
endif;

/**
 *    This function provides several hooks for developers to extend its functionality:
 *    - Filters: 'wpresidence_login_modal_settings', 'wpresidence_user_types',
 *               'wpresidence_login_button_text', 'wpresidence_register_button_text',
 *               'wpresidence_reset_password_button_text'
 *    - Actions: 'wpresidence_before_login_modal_content', 'wpresidence_before_login_form',
 *               'wpresidence_after_login_form', 'wpresidence_before_register_form',
 *               'wpresidence_after_register_form', 'wpresidence_before_forgot_password_form',
 *               'wpresidence_after_forgot_password_form', 'wpresidence_after_login_modal_content'
 */

// End of the login modal function