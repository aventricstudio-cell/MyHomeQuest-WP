<?php
/** MILLDONE
 * User Dashboard Menu Template
 * src: templates/dashboard-templates/user_menu.php
 * This file is part of the WpResidence theme and handles the user menu in the dashboard.
 *
 * @package WpResidence
 * @subpackage UserDashboard
 * @since WpResidence 1.0
 *
 * Dependencies:
 * - WordPress core functions
 * - WpResidence theme functions (wpestate_generate_user_menu)
 *
 * Usage:
 * This template displays user status messages and generates the user dashboard menu.
 */

// Get user agent ID
$user_agent_id = intval(get_user_meta($userID, 'user_agent_id', true));

// Check user agent status and display appropriate messages
if ($user_agent_id !== 0) {
    $agent_status = get_post_status($user_agent_id);
    
    if ($agent_status === 'pending') {
        echo '<div class="user_dashboard_app">' . 
             esc_html__('Your account is pending approval. Please wait for admin to approve it.', 'wpresidence') . 
             '</div>';
    } elseif ($agent_status === 'disabled') {
        echo '<div class="user_dashboard_app">' . 
             esc_html__('Your account is disabled.', 'wpresidence') . 
             '</div>';
    }
}
?>

<div class="user_tab_menu">
    <ul class="user_dashboard_links">
        <?php wpestate_generate_user_menu(); ?>
    </ul>
</div>