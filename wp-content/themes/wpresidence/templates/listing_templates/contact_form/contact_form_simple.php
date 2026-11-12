<?php
/** MILLDONE
 * Property Contact Form Template
 * src: templates/listing_templates/contact_form/contact_form_simple.php
 * This template handles the display of the contact form for property listings.
 * It includes fields for name, email, phone, and message, with additional options
 * for GDPR compliance and private messaging.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 */

do_action('before_wpestate_display_contact_form_simple');

// Prepare the textarea text based on context
$textarea_text = '';
if (is_singular('estate_property') ||  $context == 'theme_slider') {
    $textarea_text = sprintf(
        __("Hello,\nI'm interested in [ %s ]", "wpresidence"), // Use __() for translation without escaping
        esc_html(get_the_title($propertyID))           // Safely escape the dynamic content (property title)
    );
}
if ($context === 'schedule_section') {
    $textarea_text = sprintf(
        esc_html__('I would like to schedule a tour for [ %s ].', "wpresidence"),
        get_the_title($propertyID)
    );
}
?>

<div class="contact_form_flex_wrapper ">
    <div class="contact_form_flex_input_wrapper">
        <input name="contact_name" id="agent_contact_name" type="text" placeholder="<?php esc_attr_e('Name', 'wpresidence'); ?>" aria-required="true" class="form-control">
        <input type="text" name="email" class="form-control" id="agent_user_email" aria-required="true" placeholder="<?php esc_attr_e('Email', 'wpresidence'); ?>">
        <input type="text" name="phone" class="form-control" id="agent_phone" placeholder="<?php esc_attr_e('Phone', 'wpresidence'); ?>">
    </div>
   
    <textarea id="agent_comment" name="comment" class="form-control" cols="45" rows="8" aria-required="true"><?php echo ($textarea_text); ?></textarea>
    
    <?php echo wpestate_check_gdpr_case($context); ?>
    
    <input type="submit" class="wpresidence_button agent_submit_class" value="<?php esc_attr_e('Send', 'wpresidence'); ?>">
    
    <?php
    if (is_singular('estate_property')) {
        if (!class_exists('Elementor\Plugin') || !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if ($context !== 'schedule_section') {
                include(locate_template('templates/realtor_templates/agent_contact_bar.php'));
            }
        }
    }

    if (wpresidence_get_option('wp_estate_enable_direct_mess') === 'yes' && $context !== 'schedule_section') {
        ?>
        <input type="submit" class="wpresidence_button message_submit" value="<?php esc_attr_e('Send Private Message', 'wpresidence'); ?>">
        <div class="message_explaining"><?php esc_html_e('You can reply to private messages from "Inbox" page in your user account.', 'wpresidence'); ?></div>
        <?php
    }
    ?>
</div>

<input name="prop_id" type="hidden" id="agent_property_id" value="<?php echo esc_attr(intval($propertyID)); ?>">
<input name="prop_id" type="hidden" id="agent_id" value="<?php echo esc_attr(intval($agentID)); ?>">
<input type="hidden" name="contact_ajax_nonce" id="agent_property_ajax_nonce" value="<?php echo esc_attr(wp_create_nonce('ajax-property-contact')); ?>" />

<?php do_action('after_wpestate_display_contact_form_simple'); ?>