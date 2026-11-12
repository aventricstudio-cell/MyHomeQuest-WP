<?php
/** MILLDONE
 * Template Name: WpEstate CRM List
 * src: wpestate-crm-dashboard.php
 * This template handles the CRM list view in the WpEstate dashboard, displaying contacts 
 * and leads for agents and administrators. It provides functionality to view and delete 
 * CRM contacts/leads.
 *
 * @package WpResidence
 * @subpackage CRM
 * @since 1.0
 * 
 * Dependencies:
 * - wpestate_dashboard_header_permissions()
 * - wpestate_return_agent_list()
 * - wpestate_crm_delete_contact()
 * - wpestate_get_template_link()
 * - wpestate_show_dashboard_title()
 * - wpestate_show_crm_data_split()
 */

// Verify user permissions and redirect if not authorized
wpestate_dashboard_header_permissions();

// Initialize required variables
global $wpestate_social_login;
$current_user = wp_get_current_user();
$userID       = absint($current_user->ID);
$agent_list   = wpestate_return_agent_list();

// Special handling for administrators
if (function_exists('wpestate_crm_return_all_admin_ids') && current_user_can('administrator')) {
    $agent_list = array();
}

// Handle contact deletion
if (isset($_GET['delete_contact_id'])) {
    $contact_id = absint($_GET['delete_contact_id']);
    if ($contact_id !== 0) {
        wpestate_crm_delete_contact($contact_id, $agent_list);
    }
}

// Handle lead deletion with redirect
if (isset($_GET['delete_lead_id'])) {
    $lead_id = absint($_GET['delete_lead_id']);
    if ($lead_id !== 0) {
        wpestate_crm_delete_contact($lead_id, $agent_list);
        
        // Prepare redirect URL
        $base_crm      = wpestate_get_template_link('wpestate-crm-dashboard.php');
        $redirect_url  = esc_url_raw(add_query_arg('actions', '0', $base_crm));
        
        wp_safe_redirect($redirect_url);
        exit;
    }
}

// Load header
get_header();
?>

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
            <div class="col-md-12 wpestate_dash_coluns">
                <div class="wpestate_dashboard_content_wrapper dashboard_property_list">
                    <?php 
                    // Display CRM data
                    wpestate_show_crm_data_split($agent_list); 
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
get_footer(); 
?>