<?php
/** MILLDONE
 * Template: CRM Contact Submit Form Fields
 * src: crm_functions\templates\crm_contact_submit.php
 * Displays the form fields for contact information in the WpEstate CRM system.
 * Used for both adding new contacts and editing existing ones.
 * 
 * @package WpResidence
 * @subpackage CRM
 * @since 1.0
 * 
 * Dependencies:
 * - wpestate_return_contact_post_array()
 * - wpestate_crm_show_details()
 */
?>

<!-- Contact Information Section Header -->
<div class="wpestate_dashboard_section_title">
    <?php esc_html_e('Contact Information', 'wpresidence'); ?>
</div>

<!-- Hidden Fields -->
<input type="hidden" 
       name="is_user_submit" 
       value="1">

<!-- Contact Details Form Section -->
<div class="profile-onprofile row">
    <?php
    // Initialize edit ID
    $edit_id_submit = isset($edit_id) ? absint($edit_id) : 0;
    
    // Get contact form fields array
    $contact_post_array = wpestate_return_contact_post_array();
    
    // Display contact form fields
    echo wpestate_crm_show_details(
        $contact_post_array,
        $edit_id_submit
    );
    ?>
</div>