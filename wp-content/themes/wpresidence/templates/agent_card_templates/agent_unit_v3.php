<?php
/**
 * MILLDONE
 * Agent Unit V3 Template
 * src templates\agent_card_templates\agent_unit_v3.php
 * This template renders a more detailed agent card, including
 * social media links, contact details, and a featured listing count.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

// Retrieve agent details
$agent_details = wpestate_return_agent_details('', get_the_ID());
$postID = get_the_ID();
// Set up the agent's thumbnail image
$thumb_id = get_post_thumbnail_id();
$preview = wp_get_attachment_image_src($thumb_id, 'property_listings');
$name = wpresidence_get_sanitized_truncated_title($postID,0);
$link = esc_url(get_permalink());

// Prepare agent notes/excerpt
$notes = wpestate_strip_excerpt_by_char(get_the_excerpt(), 120, get_the_ID(), '...');

$agent_thumbnail = wpestate_get_agent_thumbnail();

?>

<div class="wpresidence_agent_unit_wrapper agent_card_3 <?php echo esc_attr($agent_unit_col_class['col_class']); ?> listing_wrapper  ">         
    <div class="agent_unit agent_unit_featured" data-link="<?php echo esc_attr($link); ?>">
        <div class="agent-unit-img-wrapper">
            <div class="prop_new_details_back"></div>
            <?php echo $agent_thumbnail; ?>
        </div>    

        <div class="agent_unit_featured_details">
            <?php
            // Agent name and position
            printf('<h4><a href="%s">%s</a></h4>', esc_url($link), esc_html($name));
            printf('<div class="agent_position">%s</div>', esc_html($agent_details['realtor_position']));

            // Agent contact details
            echo '<div class="agent_featured_details">';?>

            <div class="featured_agent_notes">
                <?php echo wp_kses_post($notes);  ?>
            </div>

            <?php             
            if (!empty($agent_details['realtor_phone'])) {
                printf('<div class="agent_detail"><i class="fas fa-phone"></i>%s</div>', esc_html($agent_details['realtor_phone']));
            }
            if (!empty($agent_details['realtor_mobile'])) {
                printf('<div class="agent_detail"><i class="fas fa-mobile-alt"></i>%s</div>', esc_html($agent_details['realtor_mobile']));
            }
            if (!empty($agent_details['email'])) {
                $email_display = antispambot($agent_details['email']);
                printf('<div class="agent_detail"><i class="far fa-envelope"></i>%s</div>', $email_display);
            }
            echo '</div>';
            
            // Agent social media links
            echo '<div class="agent_unit_social">';
            echo wpestate_return_agent_share_social_icons($agent_details, 'social-wrapper featured_agent_social', '');
            echo '</div>';
            ?>
            
         
            
            <?php
            // "My Listings" button
            printf(
                '<a class="see_my_list_featured" href="%s" target="_blank"><span class="featured_agent_listings wpresidence_button">%s</span></a>',
                esc_url($link),
                esc_html__('My Listings', 'wpresidence')
            );
            ?>
        </div> 
    </div>
</div>