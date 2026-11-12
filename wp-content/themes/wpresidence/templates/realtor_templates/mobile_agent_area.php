<?php
/** MILLDONE
 * Mobile Agent Area
 * src:templates\realtor_templates\mobile_agent_area.php
 * This template displays the mobile agent area for a property listing.
 * It shows the agent's image, name, and contact options.
 *
 * @package WPResidence
 * @subpackage PropertyListing
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get the agent ID associated with the property
$agent_id = intval(get_post_meta($post->ID, 'property_agent', true));

// Get property and agent details
$prop_id = $post->ID;
$realtor_details = wpestate_return_agent_details($prop_id);
$whatsup_mess = wpestate_return_agent_whatsapp_call($prop_id, $realtor_details['realtor_mobile']);

// Prepare contact icons
$contact_icons = array(
    'email' => array(
        'condition' => !empty($realtor_details['email']),
        'icon' => 'far fa-envelope',
        'link' => '#agent_contact_name',
        'class' => 'wpestate_mobile_agent_show_contact'
    ),
    'phone' => array(
        'condition' => !empty($realtor_details['realtor_mobile']),
        'icon' => 'fas fa-phone',
        'link' => 'tel:' . esc_attr($realtor_details['realtor_mobile']),
        'class' => ''
    ),
    'whatsapp' => array(
        'condition' => !empty($realtor_details['realtor_mobile']),
        'icon' => 'fab fa-whatsapp',
        'link' => esc_url($whatsup_mess),
        'class' => ''
    )
);
?>

<div class="mobile_agent_area_wrapper d-block d-lg-none">
    <div class="agent-listing-img-wrapper" data-link="<?php echo esc_attr($realtor_details['link']); ?>">
        <div class="agentpict" style="background-image:url(<?php echo esc_url($realtor_details['agent_face_img']); ?>)"></div>
        <a href="<?php echo esc_url($realtor_details['link']); ?>"><?php echo esc_html($realtor_details['realtor_name']); ?></a>
    </div>
    <div class="mobile_agent_area_details_wrapper">
        <?php foreach ($contact_icons as $type => $icon) : ?>
            <?php if ($icon['condition']) : ?>
                <div class="agent_detail agent_<?php echo esc_attr($type); ?>_class">
                    <a href="<?php echo esc_attr($icon['link']); ?>" <?php echo !empty($icon['class']) ? 'class="' . esc_attr($icon['class']) . '"' : ''; ?>>
                        <i class="<?php echo esc_attr($icon['icon']); ?>"></i>
                    </a>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>