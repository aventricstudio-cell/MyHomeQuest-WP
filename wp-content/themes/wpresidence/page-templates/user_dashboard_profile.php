<?php
/** MILLDONE
 * Template Name: User Dashboard Profile Page
 * src: page-templates\user_dashboard_profile.php
 * This file is part of the WpResidence theme and handles the user dashboard profile page functionality.
 * 
 * @package WpResidence
 * @subpackage UserDashboard
 * @since WpResidence 1.0
 *
 * Dependencies:
 * - WordPress core functions
 * - WpResidence theme functions (wpestate_*)
 * - WpEstate Social Login class
 *
 * Usage:
 * This template is used to display and manage the user's profile in the WpResidence theme's dashboard.
 * It handles PayPal payments for membership packages and 3rd party login functionality.
 */

// Ensure user has necessary permissions to access the dashboard
wpestate_dashboard_header_permissions();

// Get current user information
$current_user = wp_get_current_user();
$userID = $current_user->ID;
$user_login = $current_user->user_login;

global $wpestate_social_login;
$current_user = wp_get_current_user();
$dash_profile_link = wpestate_get_template_link('page-templates/user_dashboard_profile.php');

// PayPal payments for membership packages
if (isset($_GET['token'])) {
    $allowed_html = array();
    $token = esc_html(wp_kses($_GET['token'], $allowed_html));
    $token_recursive = esc_html(wp_kses($_GET['token'], $allowed_html));

    // Get transfer data
    $save_data = get_option('paypal_pack_transfer', '');
    $payment_execute_url = $save_data[$current_user->ID]['paypal_execute'];
    $token = $save_data[$current_user->ID]['paypal_token'];
    $pack_id = $save_data[$current_user->ID]['pack_id'];
    $recursive = isset($save_data[$current_user->ID]['recursive']) ? $save_data[$current_user->ID]['recursive'] : 0;

    if (isset($_GET['PayerID'])) {
        $payerId = esc_html(wp_kses($_GET['PayerID'], $allowed_html));
        $payment_execute = array('payer_id' => $payerId);
        $json = json_encode($payment_execute);
        $json_resp = wpestate_make_post_call($payment_execute_url, $json, $token);

        $save_data[$current_user->ID] = array();
        update_option('paypal_pack_transfer', $save_data);

        if ($json_resp['state'] == 'approved') {
            if (wpestate_check_downgrade_situation($current_user->ID, $pack_id)) {
                wpestate_downgrade_to_pack($current_user->ID, $pack_id);
                wpestate_upgrade_user_membership($current_user->ID, $pack_id, 1, '');
            } else {
                wpestate_upgrade_user_membership($current_user->ID, $pack_id, 1, '');
            }
            wp_redirect($dash_profile_link);
            exit;
        }
    } else {
        $payment_execute = array();
        $json = json_encode($payment_execute);
        $json_resp = wpestate_make_post_call($payment_execute_url, $json, $token);

        if (isset($json_resp['state']) && $json_resp['state'] == 'Active') {
            if (wpestate_check_downgrade_situation($current_user->ID, $pack_id)) {
                wpestate_downgrade_to_pack($current_user->ID, $pack_id);
                wpestate_upgrade_user_membership($current_user->ID, $pack_id, 2, '');
            } else {
                wpestate_upgrade_user_membership($current_user->ID, $pack_id, 2, '');
            }

            // Cancel current agreement
            update_user_meta($current_user->ID, 'paypal_agreement', $json_resp['id']);

            wp_redirect($dash_profile_link);
            exit();
        }
    }

    update_option('paypal_pack_transfer', '');
}

// 3rd party login code
if (isset($_SESSION['wpestate_is_fb']) && $_SESSION['wpestate_is_fb'] == 'ison' && isset($_GET['code']) && isset($_GET['state'])) {
    $wpestate_social_login->facebook_authentificate_user();
} elseif (isset($_SESSION['wpestate_is_google']) && $_SESSION['wpestate_is_google'] == 'ison' && isset($_GET['code'])) {
    $wpestate_social_login->google_authentificate_user();
} elseif (isset($_SESSION['wpestate_is_twet']) && $_SESSION['wpestate_is_twet'] == 'ison' && isset($_REQUEST['oauth_verifier'])) {
    $wpestate_social_login->twiter_authentificate_user();
} else {
    if (!is_user_logged_in()) {
        wp_redirect(esc_url(home_url('/')));
        exit();
    }
}

// Clean up session variables
if (isset($_SESSION['token_tw'])) {
    unset($_SESSION['token_tw'], $_SESSION['token_secret_tw'], $_SESSION['wpestate_is_twet'], $_SESSION['wpestate_is_fb'], $_SESSION['wpestate_is_google']);
}

get_header();
?>

<div class="row row_user_dashboard">
    <?php       include( locate_template('templates/dashboard-templates/dashboard-left-col.php') ); ?>

    <div class="col-md-9 dashboard-margin row">
        <?php
        include( locate_template( 'templates/dashboard-templates/user_memebership_profile.php') ); 
        wpestate_show_dashboard_title(get_the_title());

      
        print '<div class="dashboard-wrapper-form row">';
          
            if (current_user_can(WPRESIDENCE_ROLE_AGENT)) {
                get_template_part('templates/dashboard-templates/user_profile_agent');
            } elseif (current_user_can(WPRESIDENCE_ROLE_AGENCY)) {
                get_template_part('templates/dashboard-templates/user_profile_agency');
            } elseif (current_user_can(WPRESIDENCE_ROLE_DEVELOPER)) {
                get_template_part('templates/dashboard-templates/user_profile_developer');
            } else {
                get_template_part('templates/dashboard-templates/user_profile');
            }
            
        print '</div>';
        ?>
    </div>
</div>

<?php get_footer(); ?>