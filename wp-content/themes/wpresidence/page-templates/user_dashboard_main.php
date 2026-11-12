<?php
/** MILLDONE
 * Template Name: User Dashboard Main
 * src: page-templates\user_dashboard_main.php
 * This file is part of the WpResidence theme and handles the main user dashboard functionality.
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
 * This template is used to display the main dashboard page in the WpResidence theme.
 * It shows various widgets and summaries related to the user's account and listings.
 */

// Ensure user has necessary permissions to access the dashboard
wpestate_dashboard_header_permissions();

get_header();

// Get current user information
$current_user = wp_get_current_user();
$userID = $current_user->ID;
$user_login = $current_user->user_login;

// Get the list of agents associated with the user
$agent_list = (array)get_user_meta($userID, 'current_agent_list', true);
$agent_list[] = $userID; // Include the current user in the agent list
?>

<div class="row row_user_dashboard">
    <?php include(locate_template('templates/dashboard-templates/dashboard-left-col.php')); ?>
    
    <div class="col-md-9 dashboard-margin row">
        <?php 
        include( locate_template( 'templates/dashboard-templates/user_memebership_profile.php') ); 
        wpestate_show_dashboard_title(get_the_title()); 
        ?>
             
        <div class="dashboard-wrapper-form  col-lg-12 row">
            <div class="col-12 col-md-12 col-lg-8   wpestate_dashboard_holder row">
                <?php
                // Display various dashboard widgets
                echo wpestate_dashboard_account_summary($userID, $agent_list);
                echo wpestate_dashboard_widget_top_ten($agent_list);
                echo wpestate_dashboard_widget_top_ten_contacted($agent_list);
                echo wpestate_display_total_visits_listings();
                ?>
            </div>
            
            <div class=" col-12 col-md-12 col-lg-4   wpestate_dashboard_holder">
                <?php echo wpestate_dashboard_widget_history($agent_list); ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>