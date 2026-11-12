<?php
/**
 * Template Name: WpEstate CRM Leads
 * 
 * This template handles the CRM leads management functionality in the WpEstate dashboard.
 * It allows users to view, add, and edit leads based on their permissions.
 *
 * @package WpResidence
 * @subpackage CRM
 * @since 1.0
 * 
 * Dependencies:
 * - wpestate_dashboard_header_permissions()
 * - wpestate_return_agent_list()
 * - wpestate_create_crm_lead_dashboard()
 * - wpestate_get_template_link()
 * - wpestate_page_details()
 * - wpestate_show_dashboard_title()
 */

// Security check and permissions
wpestate_dashboard_header_permissions();

// Initialize required variables
$current_user   = wp_get_current_user();
$userID         = absint($current_user->ID);
$agent_list     = wpestate_return_agent_list();
$user_agent_id  = absint(get_user_meta($userID, 'user_agent_id', true));
$status         = get_post_status($user_agent_id);

// Redirect if agent status is pending or disabled
if ($status === 'pending' || $status === 'disabled') {
    wp_safe_redirect(esc_url(home_url('/')));
    exit;
}

// Setup allowed HTML tags and submission fields
add_filter('wp_kses_allowed_html', 'wpestate_add_allowed_tags');
$allowed_html                    = array();
$wpestate_submission_page_fields = wpresidence_get_option('wp_estate_submission_page_fields', '');
$errors                         = array();

// Determine if we're editing or viewing
$action = 'view';
$edit_id = '';
if (isset($_GET['lead_edit']) && is_numeric($_GET['lead_edit'])) {
    $edit_id = absint($_GET['lead_edit']);
    $action  = 'edit';
}

// Process form submissions
if (isset($_POST['action'])) {
    $redirect = wpestate_get_template_link('wpestate-crm-dashboard.php');
    $redirect = add_query_arg('actions', 0, $redirect);
    
    if ($_POST['action'] === 'view') {
        // Handle new lead submission
        wpestate_create_crm_lead_dashboard($_POST, $agent_list, '');
        wp_reset_query();
        wp_safe_redirect($redirect);
        exit;
    } elseif ($_POST['action'] === 'edit' && isset($_GET['lead_edit'])) {
        // Handle lead edit submission
        $contact_edit = absint($_GET['lead_edit']);
        wpestate_create_crm_lead_dashboard($_POST, $agent_list, $contact_edit);
        wp_reset_query();
        wp_safe_redirect($redirect);
        exit;
    }
}

// Load header and get page details
get_header();
$wpestate_options = wpestate_page_details($post->ID);
?>

<div id="cover"></div>
<div class="row row_user_dashboard">
    <?php 
    // Load dashboard sidebar
    get_template_part('templates/dashboard-templates/dashboard-left-col'); 
    ?>
    
    <div class="col-md-9 dashboard-margin">
        <?php 
        // Display dashboard title
        wpestate_show_dashboard_title(get_the_title());
        ?>
        <div class="dashboard-wrapper-form row">
        <?php
        // Handle lead editing
        if (isset($_GET['lead_edit']) && is_numeric($_GET['lead_edit'])) {
            $contact_edit   = absint($_GET['lead_edit']);
            $post_author_id = absint(get_post_field('post_author', $contact_edit));
            
            if (in_array($post_author_id, $agent_list) || current_user_can('administrator')) {
                include(locate_template('crm_functions/templates/crm_add_lead.php'));
            } else {
                echo '<div class="col-12 col-md-12 col-lg-7  wpestate_dash_coluns">';
                echo '<div class="wpestate_dashboard_content_wrapper">';
                echo esc_html__("You are not allowed to edit this!", "wpestate-crm");
                echo '</div></div>';
            }
        } else {
            // Display add lead form
            include(locate_template('crm_functions/templates/crm_add_lead.php'));
        }
        ?>
        </div>
    </div>
</div>

<?php
// Clean up filters
if (function_exists('wpestate_disable_filtering')) {
    wpestate_disable_filtering('wp_kses_allowed_html', 'wpestate_add_allowed_tags');
}

get_footer();
?>