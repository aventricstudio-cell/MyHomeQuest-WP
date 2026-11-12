<?php
/** MILLDONE
 * Template: CRM Contact Unit Actions
 * src: crm_functions\templates\crm_contact_unit_actions.php
 * Displays action buttons for individual contact units in the WpEstate CRM system.
 * Provides edit and delete functionality through a Bootstrap 5.3 dropdown menu.
 * 
 * @package WpResidence
 * @subpackage CRM
 * @since 1.0
 */

// Build URLs for actions
global $wp;

// Generate base list URL
$list_link = home_url($wp->request);
$list_link = esc_url_raw(
    add_query_arg('actions', 1, $list_link)
);

// Generate edit URL
$edit_link = wpestate_get_template_link('wpestate-crm-dashboard_contacts.php');
$edit_link = esc_url_raw(
    add_query_arg('contact_edit', absint($post_id), $edit_link)
);

// Generate delete URL
$delete_link = esc_url_raw(
    add_query_arg('delete_contact_id', absint($post_id), $list_link)
);

// Prepare confirmation message
$delete_confirm_msg = esc_html__('Are you sure you wish to delete ', 'wpresidence');
?>

<div class="btn-group   ">
    <button class="btn btn-default dropdown-toggle property_dashboard_actions_button" 
            type="button" 
            data-bs-toggle="dropdown"
            aria-expanded="false">
        <?php esc_html_e('Actions', 'wpresidence'); ?>
    </button>
    
    <ul class="dropdown-menu ">
        <li>
            <a class="dropdown-item" 
               href="<?php echo esc_url($edit_link); ?>">
                <i class="fas fa-edit me-2"></i>
                <?php esc_html_e('View/Edit Contact', 'wpresidence'); ?>
            </a>
        </li>
        
        <li><hr class="dropdown-divider"></li>
        
        <li>
            <a class="dropdown-item text-danger" 
               href="<?php echo esc_url($delete_link); ?>"
               onclick="return confirm('<?php echo esc_js($delete_confirm_msg); ?>')">
                <i class="fas fa-trash-alt me-2"></i>
                <?php esc_html_e('Delete Contact', 'wpresidence'); ?>
            </a>
        </li>
    </ul>
</div>