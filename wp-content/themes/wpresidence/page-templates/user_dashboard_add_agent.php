<?php
/** MILLDONE
 * Template Name: User Dashboard Add Agent
 * src: page-templates\user_dashboard_add_agent.php
 * This file is part of the WpResidence theme and handles the functionality for adding a new agent
 * or editing an existing agent in the user dashboard.
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
 * This template is used to display the form for adding or editing an agent in the WpResidence theme's user dashboard.
 * It's accessible only to users with appropriate roles (agency or developer).
 */

// Ensure user has necessary permissions to access the dashboard
wpestate_dashboard_header_permissions();

// Get current user information
// Get current user information
$current_user = wp_get_current_user();
$userID = $current_user->ID;
$user_login = $current_user->user_login;

// Get user role and agent information
$user_role = get_user_meta($userID, 'user_estate_role', true);
$user_agent_id = intval(get_user_meta($userID, 'user_agent_id', true));
$status = get_post_status($user_agent_id);

// Redirect if user doesn't have appropriate role (agency or developer)
if (!is_wpresidence_developer_or_agency())  {
    wp_redirect(esc_url(home_url('/')));
    exit;
}

// Redirect if agent status is pending or disabled
if ($status === 'pending' || $status === 'disabled') {
    $redirect_url = add_query_arg('agent_status', 'not_approved', home_url('/dashboard-main/'));
    wp_redirect(esc_url($redirect_url));
    exit;
}

get_header();

// Get WpEstate options
$wpestate_options = get_query_var('wpestate_options');

// Check if we're editing an existing listing

$listing_edit = isset($_GET['listing_edit']) ? intval($_GET['listing_edit']) : 0;
$is_edit = $listing_edit !== 0;
?>

<div class="row row_user_dashboard">
    <?php       include( locate_template('templates/dashboard-templates/dashboard-left-col.php') );  ?>
    
    <div class="col-md-9 dashboard-margin row">
        
        <?php 
        include( locate_template( 'templates/dashboard-templates/user_memebership_profile.php') ); 
        wpestate_show_dashboard_title(get_the_title());
        ?>
        
        <div class="dashboard-wrapper-form row">
            <?php include(locate_template('templates/dashboard-templates/add_new_agent_template.php')); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>