<?php
/** MILLDONE
 * Agency/Developer Unit Template
 * src: templates\agency__developers_cards_templates\agency_developer_unit.php
 * This template is used to display individual agency or developer cards
 * in the agency list and developer list pages.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */



// Retrieve agency/developer details
$thumb_id       = get_post_thumbnail_id($postID);
$preview        = wp_get_attachment_image_src($thumb_id, 'property_listings');
$name           = wpresidence_get_sanitized_truncated_title($postID,0);
$link           = esc_url(get_permalink());
$user_id        = get_post_meta($postID, 'user_meda_id', true);


// Determine if it's an agency or developer and set contact details accordingly
if (is_wpresidence_agency() || get_post_type($postID) == 'estate_agency') {
    $contact_details = array(
        'phone'      => get_post_meta($postID, 'agency_phone', true),
        'mobile'     => get_post_meta($postID, 'agency_mobile', true),
        'email'      => get_post_meta($postID, 'agency_email', true),
        'skype'      => get_post_meta($postID, 'agency_skype', true),
        'facebook'   => get_post_meta($postID, 'agency_facebook', true),
        'twitter'    => get_post_meta($postID, 'agency_twitter', true),
        'linkedin'   => get_post_meta($postID, 'agency_linkedin', true),
        'pinterest'  => get_post_meta($postID, 'agency_pinterest', true),
        'instagram'  => get_post_meta($postID, 'agency_instagram', true),
        'youtube'    => get_post_meta($postID, 'agent_youtube', true),
        'tiktok'     => get_post_meta($postID, 'agent_tiktok', true),
        'telegram'   => get_post_meta($postID, 'agent_telegram', true),
        'vimeo'      => get_post_meta($postID, 'agent_vimeo', true),
        'address'    => get_post_meta($postID, 'agency_address', true),
    );
} else {
    $contact_details = array(
        'phone'      => get_post_meta($postID, 'developer_phone', true),
        'mobile'     => get_post_meta($postID, 'developer_mobile', true),
        'email'      => get_post_meta($postID, 'developer_email', true),
        'skype'      => get_post_meta($postID, 'developer_skype', true),
        'facebook'   => get_post_meta($postID, 'developer_facebook', true),
        'twitter'    => get_post_meta($postID, 'developer_twitter', true),
        'linkedin'   => get_post_meta($postID, 'developer_linkedin', true),
        'pinterest'  => get_post_meta($postID, 'developer_pinterest', true),
        'instagram'  => get_post_meta($postID, 'developer_instagram', true),
        'youtube'    => get_post_meta($postID, 'agent_youtube', true),
        'tiktok'     => get_post_meta($postID, 'agent_tiktok', true),
        'telegram'   => get_post_meta($postID, 'agent_telegram', true),
        'vimeo'      => get_post_meta($postID, 'agent_vimeo', true),
        'address'    => get_post_meta($postID, 'developer_address', true),
    );
}

$realtor_details = wpestate_return_agent_details($postID, $postID);

// Prepare thumbnail image
$thumb_prop = get_the_post_thumbnail_url($postID, 'property_listings', array('class' => 'lazyload img-responsive'));
if (empty($thumb_prop)) {
    $thumb_prop = get_theme_file_uri('/img/default_user_agent.png');
}

// Get property count for the agency/developer
$counter = 0;
if ($user_id != 0) {
    $counter = count_user_posts($user_id, 'estate_property', true);
}
?>

<div class="agency_unit d-flex flex-column flex-md-row" data-link="<?php echo esc_attr($link); ?>">
    <div class="agency_unit_img col-12 ">
        <div class="prop_new_details_back"></div>
        <img src="<?php echo esc_url($thumb_prop); ?>" alt="<?php echo esc_attr($name); ?>" />
    </div>

    <div class="agency_unit_wrapper ">
        <h4><a href="<?php echo esc_url($link); ?>"><?php echo esc_html($name); ?></a></h4>
        <div class="agent_address"><?php echo esc_html($contact_details['address']); ?></div>

        <?php echo wpestate_return_agent_share_social_icons($realtor_details, 'social-wrapper', ''); ?>

        <div class="agency_social-wrapper">
            <?php if (!empty($contact_details['phone'])): ?>
                <div class="agent_detail"><i class="fas fa-phone"></i><a href="tel:<?php echo esc_attr($contact_details['phone']); ?>"><?php echo esc_html($contact_details['phone']); ?></a></div>
            <?php endif; ?>

            <?php if (!empty($contact_details['mobile'])): ?>
                <div class="agent_detail"><i class="fas fa-mobile-alt"></i><a href="tel:<?php echo esc_attr($contact_details['mobile']); ?>"><?php echo esc_html($contact_details['mobile']); ?></a></div>
            <?php endif; ?>

            <?php if (!empty($contact_details['email'])): ?>
                <?php
             
                $email_link    = antispambot(esc_attr($contact_details['email']));
                ?>
                <div class="agent_detail"><i class="far fa-envelope"></i><a href="mailto:<?php echo $email_link; ?>"><?php echo $email_link; ?></a></div>
            <?php endif; ?>
        </div>

        <div class="agency_users">
            <?php
            $agent_list = (array)get_user_meta($user_id, 'current_agent_list', true);
            if (is_array($agent_list)) {
                $agent_list = array_unique($agent_list);
                foreach ($agent_list as $agent_user_id) {
                    $sub_agent_id = intval(get_user_meta($agent_user_id, 'user_agent_id', true));
                    if ($sub_agent_id != 0) {
                        $thumb_id = get_post_thumbnail_id($sub_agent_id);
                        $preview = wp_get_attachment_image_src($thumb_id, 'custom_slider_thumb');
                        $preview_url = $preview ? $preview[0] : get_theme_file_uri('/img/default-user_1.png');
                        echo '<a href="' . esc_url(get_permalink($sub_agent_id)) . '" class="sub_agent"><img src="' . esc_url($preview_url) . '" alt="Sub Agent" /></a>';
                    }
                }
            }
            ?>
        </div>

        <?php if ($user_id != 0 && $counter != 0): ?>
            <div class="agent_card_my_listings">
                <?php
                echo intval($counter) . ' ';
                echo $counter != 1 ? esc_html__('listings', 'wpresidence') : esc_html__('listing', 'wpresidence');
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>