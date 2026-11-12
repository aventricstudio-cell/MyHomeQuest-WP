<?php
/** MILLDONE
 * Template Name: User Dashboard Inbox
 * src: page-templates\user_dashboard_inbox.php
 * Displays the user's message inbox in the WpResidence theme dashboard.
 * This template shows unread message count and lists all messages for the current user.
 * 
 * @package WpResidence
 * @subpackage Dashboard
 * @since WpResidence 1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Verify user permissions and redirect if necessary
wpestate_dashboard_header_permissions();

// Initialize current user data
$current_user = wp_get_current_user();
$userID = $current_user->ID;
$user_login = $current_user->user_login;


// Get template links
$dash_profile_link = wpestate_get_template_link('page-templates/user_dashboard_profile.php');

// Load header and get theme options
get_header();
$wpestate_options = get_query_var('wpestate_options');

// Start template output
?>
<div class="row row_user_dashboard">
    <?php
    // Include dashboard navigation sidebar
    include(locate_template('templates/dashboard-templates/dashboard-left-col.php'));
    ?>
    
    <div class="col-md-9 dashboard-margin row">
        <?php
        // Include membership profile section
        include(locate_template('templates/dashboard-templates/user_memebership_profile.php'));
        
        // Display dashboard title
        wpestate_show_dashboard_title(get_the_title());
        ?>
        <div class="dashboard-wrapper-form row">
            <div class="col-md-12 wpestate_dash_coluns">
                <div class="wpestate_dashboard_content_wrapper">
                    <div class="wpestate_dashboard_section_title inbox_title">
                        <?php
                        // Display unread message count
                        $no_unread = intval(get_user_meta($userID, 'unread_mess', true));
                        printf(
                            '%s %d %s',
                            esc_html__('You have', 'wpresidence'),
                            $no_unread,
                            esc_html__('unread messages', 'wpresidence')
                        );
                        ?>
                    </div>
                    
                    <?php
                    // Display inbox message list
                    echo wpestate_dashboard_inbox_list($userID);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Add security nonce for AJAX actions
$ajax_nonce = wp_create_nonce("wpestate_inbox_actions");
printf(
    '<input type="hidden" id="wpestate_inbox_actions" value="%s" />',
    esc_attr($ajax_nonce)
);

// Load footer
get_footer();
?>