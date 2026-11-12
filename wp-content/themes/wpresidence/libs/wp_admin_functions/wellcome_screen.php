<?php
/**
 * WpResidence Welcome Panel
 *
 * This file contains functions to generate and display a custom welcome panel
 * for the WpResidence theme in the WordPress admin dashboard.
 *
 * @package WpResidence
 * @subpackage AdminInterface
 * @since WpResidence 1.0
 */








/**
 * WPResidence Admin Login Customization
 *
 * This file contains a function to customize the WordPress admin login page
 * for the WPResidence theme, including logo and styling.
 *
 * @package WPResidence
 * @subpackage AdminCustomization
 * @since 1.0.0
 */

if ( ! function_exists( 'wpestate_admin_login_logo' ) ) :
    /**
     * Customize the WordPress admin login page
     *
     * This function adds custom CSS to the login page to change the logo,
     * background, and overall styling to match the WPResidence theme.
     *
     * @since 1.0.0
     */
    function wpestate_admin_login_logo() {
        // Get the custom logo URL from theme options
        $logo_url = esc_url( wpresidence_get_option( 'wp_estate_logo_image', 'url' ) );
        
        // If no custom logo is set, use the default theme logo
        if ( empty( $logo_url ) ) {
            $logo_url = get_theme_file_uri( '/img/logo.png' );
        }
        ?>
        <style type="text/css">
            /* Custom login logo */
            body.login div#login h1 a {
                background-image: url(<?php echo esc_url( $logo_url ); ?>);
                background-size: 161px;
                background-position: center bottom;
                background-repeat: no-repeat;
                width: 192px;
                height: 85px;
                margin: 10px auto;
                padding-bottom: 30px;
            }

            /* Login page background */
            body.login {
                background: rgb(20,28,21);
                background: linear-gradient(43deg, rgba(20,28,21,1) 0%, rgba(57,108,223,1) 100%);
            }

            /* Login form container */
            #login {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                padding: 0;
                margin: 0;
                background-color: #fff;
                box-shadow: 0 1px 3px rgba(0,0,0,.13);
            }

            /* Login form */
            .login form {
                box-shadow: none;
                padding: 26px 24px;
                margin-top: 0;
            }

            /* Interim login adjustments */
            .interim-login #login {
                transform: translate(-50%, -56%);
            }

            .interim-login #login_error,
            .interim-login.login .message {
                margin: 0;
            }

            /* WordPress auth check modal */
            #wp-auth-check-wrap #wp-auth-check {
                max-height: 515px !important;
            }
        </style>
        <?php
    }
endif;

add_action( 'login_head', 'wpestate_admin_login_logo' );




/**
 * WPResidence Login Page Customizations
 *
 * This file contains functions to customize the WordPress login page for the WPResidence theme.
 * It includes customizations for the login logo URL, login logo title, and a function to disable license notifications.
 *
 * @package WPResidence
 * @subpackage LoginCustomization
 * @since 1.0.0
 */

if ( ! function_exists( 'wpestate_login_logo_url' ) ) :
    /**
     * Customize the login logo URL
     *
     * This function changes the URL of the logo on the login page to the home URL of the site.
     *
     * @since 1.0.0
     * @return string The URL for the login logo link
     */
    function wpestate_login_logo_url() {
        return esc_url( home_url( '/' ) );
    }
endif;
add_filter( 'login_headerurl', 'wpestate_login_logo_url' );







if ( ! function_exists( 'wpestate_login_logo_url_title' ) ) :
    /**
     * Customize the login logo title text
     *
     * This function changes the title attribute of the logo link on the login page.
     *
     * @since 1.0.0
     * @return string The title text for the login logo link
     */
    function wpestate_login_logo_url_title() {
        return sprintf(
            '%s %s',
            esc_html__( 'Powered by', 'wpresidence' ),
            esc_url( home_url( '/' ) )
        );
    }
endif;
add_filter( 'login_headertext', 'wpestate_login_logo_url_title' );






if ( ! function_exists( 'wpestate_disable_licence_notifications' ) ) :
    /**
     * Disable license notifications
     *
     * This function handles an AJAX request to disable license notifications for administrators.
     *
     * @since 1.0.0
     */
    function wpestate_disable_licence_notifications() {
        // Verify the AJAX request nonce for security
        check_ajax_referer( 'wpestate_close_notice_nonce', 'security' );

        // Check if the current user has administrator capabilities
        if ( current_user_can( 'administrator' ) ) {
            update_option( 'wp_estate_disable_notice', 'yes' );
        }

        wp_die(); // Proper way to end AJAX functions
    }
endif;
add_action( 'wp_ajax_wpestate_disable_licence_notifications', 'wpestate_disable_licence_notifications' );