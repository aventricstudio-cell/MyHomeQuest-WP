<?php
/** MILLDONE
 * Dashboard Left Column Template
 * src: templates/dashboard-templates/dashboard-left-col.php
 * This file is part of the WpResidence theme and handles the left column of the user dashboard.
 *
 * @package WpResidence
 * @subpackage UserDashboard
 * @since WpResidence 1.0
 *
 * Dependencies:
 * - WordPress core functions
 * - WpResidence theme functions
 *
 * Usage:
 * This template displays the user's profile picture, welcome message, and the dashboard menu.
 */

// Get current user information
$current_user = wp_get_current_user();
$user_login = $current_user->user_login;
$userID       = absint($current_user->ID);
// Get user's custom profile picture
$user_small_picture_id = get_the_author_meta('small_custom_picture', $current_user->ID);

// Set default profile picture if custom picture is not set
if (empty($user_small_picture_id)) {
    $user_small_picture = [get_theme_file_uri('/img/default-user_1.png')];
} else {
    $user_small_picture = wp_get_attachment_image_src($user_small_picture_id, 'agent_picture_thumb');
}

// Ensure $user_small_picture is an array with at least one element
$profile_image_url = is_array($user_small_picture) && !empty($user_small_picture) ? $user_small_picture[0] : '';
?>

<div class="col-md-3 user_menu_wrapper  d-none d-xl-block">
    <div class="dashboard_menu_user_image">
        <div class="menu_user_picture" style="background-image: url('<?php echo esc_url($profile_image_url); ?>'); height: 60px; width: 60px;"></div>
        <div class="dashboard_username">
            <?php 
            /* translators: %s: user login name */
            printf(esc_html__('Welcome back, %s!', 'wpresidence'), esc_html($user_login)); 
            ?>
        </div>
    </div>
    <?php include( locate_template('templates/dashboard-templates/user_menu.php')); ?>
</div>