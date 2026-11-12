<?php
/**
 * Agent Details Template
 *
 * This template displays detailed information about a real estate agent,
 * including their photo, contact information, and a contact form.
 *
 * @package WpResidence
 * @subpackage AgentTemplates
 * @since 1.0.0
 */

// Ensure the template is not accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// set the agentID for contact forms
$agentID = $post->ID;


?>

<div class="wpestate_contact_form_parent wpestate_single_agent_details_wrapper wpestate_single_agent_details_wrapper_type2  flex-md-row flex-column">
    <div class="agentpic-wrapper">
        <div class="agent-listing-img-wrapper" data-link="<?php echo esc_attr($realtor_details['link']); ?>">
            <div class="agentpict" style="background-image:url(<?php echo esc_url($realtor_details['realtor_image']); ?>)"></div>
        </div>
    </div>
    
    <div class="agent_details">
        <?php
        // Fetch author and agency information
        $author = get_post_field('post_author', $post->ID);
        $agency_post = get_the_author_meta('user_agent_id', $author);


        // Display agent reviews
        echo wpestate_return_agent_reviews_bar($post->ID);

        // Display agent name and position
        ?>
        <h3><a href="<?php echo esc_url($realtor_details['link']); ?>"><?php echo esc_html($realtor_details['realtor_name']); ?></a></h3>
        <div class="agent_position">
            <?php
            echo esc_html($realtor_details['realtor_position']);
            if (is_singular('estate_agent') && $agency_post != '') {
                echo ', <a href="' . esc_url(get_permalink($agency_post)) . '">' . get_the_title($agency_post) . '</a>';
            }
            ?>
        </div>

        <?php
        // Display membership information if available
        if (!empty($realtor_details['member_of'])) {
            echo '<div class="agent_detail agent_web_member_of_class"><strong>' . esc_html__('Member of:', 'wpresidence') . '</strong> ' . esc_html($realtor_details['member_of']) . '</div>';
        }

        // Display agent address if available
        if (!empty($realtor_details['agent_address'])) {
            echo '<div class="agent_detail agent_address">' . esc_html($realtor_details['agent_address']) . '</div>';
        }

        // Display social sharing icons
        echo wpestate_return_agent_share_social_icons($realtor_details, 'agent_social_share_type2');

        // Include agent contact bar
        get_template_part('templates/realtor_templates/agent_contact_bar');
        ?>
    </div>
</div>

<div id="agent_send_email" class="wpestate_agent_details_container_wrapper wpestate_agent_details_container_wrapper_type_2 flex-md-row flex-column">
    <div class="wpestate_agent_details_container ">
        <h4><?php esc_html_e('About Me', 'wpresidence'); ?></h4>
        <?php
        $content = get_the_content();
        if (!empty($content)) {
            echo apply_filters('the_content', $content);
        }

        // Display agent specialties and services if enabled
        if (wpresidence_get_option('wp_estate_agent_page_show_speciality_service', '') === 'yes') {
            get_template_part('templates/realtor_templates/agent_taxonomies');
        }

        // Display custom agent data
        get_template_part('templates/realtor_templates/agent_custom_data');
        ?>
    </div>

    <div class="wpestate_agent_details_container wpestate_contact_form_parent">
        <h4><?php echo esc_html__('Contact Me', 'wpresidence'); ?></h4>
        <div class="wpestate_agent_contact_details_type2">
            <?php echo wpestate_return_agent_contact_details($realtor_details); ?>
        </div>
        <?php
        // Include the contact form
        $context = '';
        include(locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php'));
        ?>
    </div>
</div>