<?php
/** MILLDONE
 * Template Name: User Dashboard
 * src: page-templates\user_dashboard.php
 * This file is part of the WpResidence theme and handles the user dashboard functionality.
 *
 * @package WpResidence
 * @subpackage UserDashboard
 * @since WpResidence 1.0
 *
 * Dependencies:
 * - WordPress core functions
 * - WpResidence theme functions (wpestate_*)
 *
 * Usage:
 * This template is used to display the user dashboard in the WpResidence theme.
 * It shows property listings, allows for property management, and displays user package information.
 */

// Ensure user has necessary permissions to access the dashboard
wpestate_dashboard_header_permissions();

// Get current user information
$current_user = wp_get_current_user();
$userID = $current_user->ID;
$user_login = $current_user->user_login;

// Retrieve user package and registration information
$user_pack = get_the_author_meta('package_id', $userID);
$user_registered = get_the_author_meta('user_registered', $userID);
$user_package_activation = get_the_author_meta('package_activation', $userID);

// Get submission settings
$paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));
$price_submission = floatval(wpresidence_get_option('wp_estate_price_submission', ''));
$submission_curency_status = esc_html(wpresidence_get_option('wp_estate_submission_curency', ''));

// Get template links
$edit_link = wpestate_get_template_link('page-templates/user_dashboard_add.php');
$processor_link = wpestate_get_template_link('processor.php');

// Get user's agent list and package information
$agent_list = (array)get_user_meta($userID, 'current_agent_list', true);
$parent_userID = wpestate_check_for_agency($userID);
$remaining_lists = wpestate_get_remain_listing_user($parent_userID, $user_pack);

// Check user agent status
$user_agent_id = intval(get_user_meta($userID, 'user_agent_id', true));
$status = get_post_status($user_agent_id);

// Redirect if agent status is pending or disabled
if ($status === 'pending' || $status === 'disabled') {
    wp_redirect(esc_url(home_url('/dashboard-main/')));
    exit;
}

// Handle GET actions
if (isset($_GET['featured_edit'])) {
    wpestate_make_featured_dashboard($_GET['featured_edit']);
}

if (isset($_GET['delete_id'])) {
    wpestate_delete_listing_dashboard($_GET['delete_id'], $current_user, $agent_list);
}

if (isset($_GET['duplicate']) && intval($_GET['duplicate']) != 0) {
    $can_duplicate = $paid_submission_status != 'membership' ||
                     ($paid_submission_status == 'membership' && $remaining_lists > 0) ||
                     ($paid_submission_status == 'membership' && $remaining_lists == -1);
    
    if ($can_duplicate) {
        wpestate_duplicate_listing($_GET['duplicate']);
    }
}

get_header();
?>

<div class="row row_user_dashboard">
    <?php  include( locate_template('templates/dashboard-templates/dashboard-left-col.php') );?>
    
    <div class="col-md-9 dashboard-margin row">
     
        <?php 
        include( locate_template( 'templates/dashboard-templates/user_memebership_profile.php') ); 
        wpestate_show_dashboard_title(get_the_title());
        $agent_list[] = $current_user->ID;
        ?>
        <div class="dashboard-wrapper-form row">
            <div class="col-md-12 wpestate_dash_coluns">
                <div class="wpestate_dashboard_content_wrapper dashboard_property_list">
                    <?php
                    $status_value = '';
                    $order_by = '3';
                    
                    if (isset($_GET['orderby'])) {
                        $order_by = intval($_GET['orderby']);
                    }
                    
                    if (isset($_GET['status'])) {
                        $status_value = intval($_GET['status']);
                    }
                    
                    wpestate_dashboard_property_list($agent_list, $order_by, $status_value);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Generate nonces for AJAX actions
$ajax_nonce = wp_create_nonce("wpestate_tab_stats");
$ajax_nonce1 = wp_create_nonce("wpestate_property_actions");
$ajax_nonce2 = wp_create_nonce("wpresidence_simple_pay_actions_nonce");

// Output hidden fields with nonces
?>
<input type="hidden" id="wpestate_tab_stats" value="<?php echo esc_attr($ajax_nonce); ?>" />
<input type="hidden" id="wpestate_property_actions" value="<?php echo esc_attr($ajax_nonce1); ?>" />
<input type="hidden" id="wpresidence_simple_pay_actions_nonce" value="<?php echo esc_attr($ajax_nonce2); ?>" />

<?php
get_footer();
?>