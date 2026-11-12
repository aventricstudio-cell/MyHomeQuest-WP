<?php
/** MILLDONE
 * Template Name: User Dashboard Agent List
 * src: page-templates\user_dashboard_agent_list.php
 * This template handles the agent list dashboard functionality for the WpResidence theme.
 * It displays a list of agents associated with the current user and provides management capabilities.
 * 
 * Package: WpResidence
 * Version: 1.0
 * 
 * Dependencies:
 * - WordPress core
 * - WpResidence theme functions
 * - WpResidence dashboard templates
 * 
 * Required Capabilities:
 * - User must be logged in
 * - User must have role 3 or 4 (Agency or Developer)
 * - User's agent status must be active (not pending or disabled)
 */

// Security check - verify user permissions
wpestate_dashboard_header_permissions();

// Verify nonce for agent search
if (isset($_POST['dashboard_agent_search_nonce']) && !wp_verify_nonce($_POST['dashboard_agent_search_nonce'], 'dashboard_agent_search')) {
    esc_html_e('Sorry, your nonce did not verify.', 'wpresidence');
    exit;
}

// Initialize user variables
$current_user = wp_get_current_user();
$userID = (int)$current_user->ID;
$user_login = sanitize_user($current_user->user_login);
$user_registered = get_the_author_meta('user_registered', $userID);
$edit_link = wpestate_get_template_link('page-templates/user_dashboard_agent_list.php');

$user_agent_id = (int)get_user_meta($userID, 'user_agent_id', true);
$status = get_post_status($user_agent_id);


// Redirect if user doesn't have appropriate role (agency or developer)
if (!is_wpresidence_developer_or_agency() ) {
    wp_redirect(esc_url(home_url('/')));
    exit;
}

// Check agent status
if ($status === 'pending' || $status === 'disabled') {
    wp_redirect(esc_url(home_url('/dashboard-main/')));
    exit;
}

// Handle agent deletion
if (isset($_GET['delete_id'])) {
    // Validate delete_id is numeric
    if (!is_numeric($_GET['delete_id'])) {
        exit('you don\'t have the right to delete this');
    }
    
    $delete_id = (int)$_GET['delete_id'];
    $the_post = get_post($delete_id);
    $user_to_delete = get_post_meta($delete_id, 'user_meda_id', true);
    
    // Verify current user has permission to delete
    if ($current_user->ID != $the_post->post_author) {
        exit('you don\'t have the right to delete this');
    }
    
    // Process deletion
    $arguments = array(
        'numberposts' => -1,
        'post_type' => array('attachment', 'estate_property'),
        'author' => $user_to_delete,
        'post_status' => 'any'
    );
    
    $user_list = new WP_Query($arguments);
    $owner_id = get_user_meta($userID, 'user_agent_id', true);
    
    // Transfer properties to owner
    if ($user_list->have_posts()):
        while ($user_list->have_posts()):
            $user_list->the_post();
            $change_arg = array(
                'ID' => get_the_ID(),
                'post_author' => $userID,
            );
            wp_update_post($change_arg);
            update_post_meta(get_the_ID(), 'property_agent', $owner_id);
        endwhile;
    endif;
    
    // Include user deletion function if not available
    if (!function_exists('wp_delete_user')) {
        require_once(ABSPATH . 'wp-admin/includes/user.php');
    }
    
    // Delete user and their post
    wp_delete_user($user_to_delete);
    wp_delete_post($delete_id);
    wp_redirect(wpestate_get_template_link('page-templates/user_dashboard_agent_list.php'));
    exit;
}

// Load header
get_header();
$wpestate_options = get_query_var('wpestate_options');
?>

<!-- Dashboard Layout Structure -->
<div class="row row_user_dashboard">
    <?php 
    // Include dashboard sidebar
    include(locate_template('templates/dashboard-templates/dashboard-left-col.php')); 
    ?>

    <div class="col-md-9 dashboard-margin row">
        <?php
        // Include membership profile template
        include(locate_template('templates/dashboard-templates/user_memebership_profile.php'));
        
        // Display dashboard title
        wpestate_show_dashboard_title(get_the_title());
        
        // Initialize agent list array
        $agent_list[] = $current_user->ID;
        ?>
        <div class="dashboard-wrapper-form row">
            <div class="col-md-12 wpestate_dash_coluns">
                <div class="wpestate_dashboard_content_wrapper dashboard_agent_list">
                    <?php
                    // Handle ordering and status filtering
                    $order_by = isset($_GET['orderby']) ? (int)$_GET['orderby'] : '';
                    $status_value = isset($_GET['status']) ? (int)$_GET['status'] : '';
                    
                    // Display agent list
                    wpestate_dashboard_agent_list($status_value);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Create nonce for AJAX actions
$ajax_nonce = wp_create_nonce("wpestate_agent_actions");
printf('<input type="hidden" id="wpestate_agent_actions" value="%s" />', esc_attr($ajax_nonce));

// Load footer
get_footer();
?>