<?php
/** MILLDONE
 * Template: CRM Contact Form
 * src: crm_functions\templates\crm_add_contact.php
 * Displays the form for adding or editing contacts in the WpEstate CRM system.
 * Supports both new contact creation and existing contact editing functionality.
 * 
 * @package WpResidence
 * @subpackage CRM
 * @since 1.0
 */
?>

<form id="new_post" 
      name="new_post" 
      method="post" 
      action="" 
      enctype="multipart/form-data" 
      class="add-estate add-vrm-contact row">
    
    <?php 
    // Security nonce field
    wp_nonce_field('dashboard_property_front_crm', 'dashboard_property_front_crm_nonce'); 
    ?>
    
    <!-- Error Messages Section -->
    <div class="col-md-12 row_dasboard-prop-listing">
        <?php
        $wpestate_show_err = false;
        if ($wpestate_show_err) {
            printf(
                '<div class="alert alert-danger">%s</div>',
                esc_html($wpestate_show_err)
            );
        }
        ?>
    </div>
    
    <?php
    // Initialize contact edit ID
    $contact_edit = isset($_GET['contact_edit']) ? absint($_GET['contact_edit']) : 0;
    
    // Main Form Content Section
    echo '<div class="col-12 col-md-12 col-lg-7  wpestate_dash_coluns">';
    echo '<div class="wpestate_dashboard_content_wrapper">';
    
    // Include the contact submission form fields
    include(locate_template('crm_functions/templates/crm_contact_submit.php'));
    ?>
    
    <!-- Form Submit Section -->
    <div class="profile-onprofile col-md-12 submitrow">
        <input type="hidden" 
               name="action" 
               value="<?php echo esc_attr($action); ?>">
        
        <?php if ($action === 'edit'): ?>
            <input type="submit" 
                   class="wpresidence_button" 
                   id="form_submit_1" 
                   value="<?php esc_attr_e('Save Changes', 'wpresidence'); ?>">
        <?php else: ?>
            <input type="submit" 
                   class="wpresidence_button" 
                   name="submit_prop" 
                   id="form_submit_1" 
                   value="<?php esc_attr_e('Add Contact', 'wpresidence'); ?>">
        <?php endif; ?>
    </div>
    
    <?php
    echo '</div></div>';
    
    // Additional Information Sections for Editing
    if ($contact_edit !== 0) {
        // Lead Details Section
        echo '<div class="col-md-5">';
          echo '<div class="  wpestate_crm_list_leads">';
          echo '<div class="wpestate_dashboard_content_wrapper">';
          $lead_id = absint(get_post_meta($contact_edit, 'lead_contact', true));
          wpestate_show_lead_details_dashboard($lead_id);
          echo '</div>';
        
          // Contact Notes Section
          echo '<div class=" wpestate_crm_add_coment_contact_wrapper">';
          echo '<div class="wpestate_dashboard_content_wrapper">';
          echo wpestate_crm_show_notes($contact_edit);
          echo wpestate_crm_display_add_note($contact_edit);
          echo '</div>';
        echo '</div>';
    }
    ?>
    
    </div><!-- end row-->
    
    <!-- Hidden Fields -->
    <input type="hidden" 
           name="edit_id" 
           value="<?php echo isset($edit_id) ? absint($edit_id) : ''; ?>">
           
    <input type="hidden" 
           name="images_todelete" 
           id="images_todelete" 
           value="">
    
    <?php 
    // Security nonce field for estate submission
    wp_nonce_field('submit_new_estate', 'new_estate'); 
    ?>
</form>