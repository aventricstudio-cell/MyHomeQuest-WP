<?php
/** MILLDONE
 * Realtor Contact Buttons
 * src: templates\realtor_templates\agent_contact_bar.php
 * This template displays contact buttons for realtors on agent and property pages.
 * It includes email, call, and WhatsApp buttons with appropriate contact details.
 *
 * @package WPResidence
 * @subpackage AgentProfile
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Reset Realtor details
if ( isset( $propertyID ) ) {
    $realtor_details = wpestate_return_agent_details($propertyID);
}

$whatsup_mess = '';
if(isset( $propertyID)){
    $whatsup_mess = wpestate_return_agent_whatsapp_call($propertyID, $realtor_details['realtor_mobile']);
}

// Determine page type and set appropriate realtor details
if (is_singular('estate_agent')) {
    $realtor_details = wpestate_return_agent_details('', $post->ID);
    $realtor_details['realtor_mobile'] = isset($realtor_details['realtor_mobile']) ? $realtor_details['realtor_mobile'] : '';
    $whatsup_mess = wpestate_return_agent_whatsapp_call(-1, $realtor_details['realtor_mobile']);
} elseif (is_singular('estate_property') && isset($is_modal) ) {
    $realtor_details = wpestate_return_agent_details($propertyID);
    $realtor_details['realtor_mobile'] = isset($realtor_details['realtor_mobile']) ? $realtor_details['realtor_mobile'] : '';
    $whatsup_mess = wpestate_return_agent_whatsapp_call($propertyID, $realtor_details['realtor_mobile']);
}

// Display contact buttons
?>
<div class="realtor-contact-buttons">
    <?php if (is_singular('estate_agent')) : ?>
        <a class="wpresidence_button send_email_agent" href="#agent_send_email">
            <?php esc_html_e('Send', 'wpresidence'); ?>
        </a>
    <?php endif; ?>

    <a class="wpresidence_button wpresidence_button_inverse realtor_call" href="tel:<?php echo esc_attr($realtor_details['realtor_phone']); ?>">
        <i class="fas fa-phone"></i>
        <?php 
        esc_html_e('Call', 'wpresidence');
        echo ' <span class="agent_call_no">' . esc_html($realtor_details['realtor_phone']) . '</span>';
        ?>
    </a>

    <a class="wpresidence_button wpresidence_button_inverse realtor_whatsapp" target="_blank" href="<?php echo esc_url($whatsup_mess); ?>">
        <i class="fab fa-whatsapp"></i>
        <?php esc_html_e('WhatsApp', 'wpresidence'); ?>
    </a>
</div>