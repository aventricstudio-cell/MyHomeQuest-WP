<?php
/** MILLDONE
 * Agent Unit Template
 * src templates\agent_card_templates\agent_unit.php
 * This template is responsible for rendering individual agent cards
 * in various agent listings throughout the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

// Ensure this file is not accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Retrieve agent details
 * 
 * @uses wpestate_return_agent_details() Located in /libs/agent_functions.php
 */
$agent_details = wpestate_return_agent_details('', $postID);
$agent_thumbnail = wpestate_get_agent_thumbnail();
?>

<div class="wpresidence_agent_unit_wrapper <?php echo esc_attr($agent_unit_col_class['col_class']); ?> listing_wrapper   ">         
    <div class="agent_unit" data-link="<?php echo esc_url($agent_details['link']); ?>">
        <div class="agent-unit-img-wrapper">
            <?php 
            // Display the number of listings if the agent has any
            if ($agent_details['counter'] != 0) :
            ?>
                <div class="agent_card_my_listings">
                    <?php 
                    echo intval($agent_details['counter']) . ' ';
                    echo ($agent_details['counter'] != 1) ? esc_html__('listings', 'wpresidence') : esc_html__('listing', 'wpresidence');
                    ?>
                </div>
            <?php 
            endif; 
            
            // Display the agent's thumbnail
            echo $agent_thumbnail; 
            ?>
        </div>    

        <?php
        // Display agent name and position
        printf('<h4><a href="%s">%s</a></h4>', esc_url($agent_details['link']), esc_html($agent_details['realtor_name']));
        printf('<div class="agent_position">%s</div>', esc_html($agent_details['realtor_position']));

        // Display a short excerpt about the agent
        $excerpt = get_the_excerpt($postID);
        if (!is_null($excerpt) && $excerpt !== '') {
            $excerpt = wpestate_strip_excerpt_by_char($excerpt, 90, $postID, '...');
            printf('<div class="agent_card_content">%s</div>', wp_kses_post($excerpt));
        }
        ?>

        <div class="agent_unit_social agent_list">
            <?php
            // Display social icons
            echo wpestate_return_agent_share_social_icons($agent_details, 'wpestate_agent_unit_social', '');
            
            // Display phone icon if phone number is available
            if (!empty($agent_details['realtor_phone'])) :
                printf(
                    '<div class="agent_unit_phone"><a href="tel:%s" rel="noopener"><i class="fas fa-phone"></i></a></div>',
                    esc_attr($agent_details['realtor_phone'])
                );
            endif;
            
            // Display email icon if email is available
            if (!empty($agent_details['email'])) :
                $email_link = antispambot(esc_attr($agent_details['email']));
                printf(
                    '<div class="agent_unit_email"><a href="mailto:%s" rel="noopener"><i class="fas fa-envelope"></i></a></div>',
                    $email_link
                );
            endif;
            ?>
        </div>
    </div>
</div>