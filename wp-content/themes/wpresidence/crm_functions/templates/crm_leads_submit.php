<?php
/** MILLDONE
 * Template: CRM Leads Submit Form Fields
 * src: crm_functions\templates\crm_leads_submit.php
 * Displays the form fields for lead information in the WpEstate CRM system.
 * Includes lead details and contact selection functionality.
 * 
 * @package WpResidence
 * @subpackage CRM
 * @since 1.0
 */


?>

<!-- Lead Information Form Section -->
<h2><?php esc_html_e('Lead Information', 'wpresidence'); ?></h2>

<!-- Hidden Fields -->
<input type="hidden" 
       name="is_user_submit" 
       value="1">

<div class="profile-onprofile row">
    <?php
    // Display lead details fields
    $leads_post_array = wpestate_leads_post_array();

    echo wpestate_crm_show_details($leads_post_array, $contact_edit);
    ?>
    
    <!-- Contact Selection Section -->
    <div class="half-content col-md-6">
        <label for="wpestate_crm_manual_contact" 
               style="margin-top:10px;">
            <?php esc_html_e('Contact', 'wpresidence'); ?>
        </label>
        
        <select name="wpestate_crm_manual_contact" 
                id="wpestate_crm_manual_contact">
            <?php 
            // Display contact options
            if(function_exists('wpestate_list_select_contacts')){
              echo wpestate_list_select_contacts($contact_edit); 
            }

            ?>
        </select>
    </div>
</div>