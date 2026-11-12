<?php
/**MILLDONE
 * Agent Unit V4 Template
 * src:templates\agent_card_templates\agent_unit_v4.php
 * This template renders a simplified agent card, including
 * the agent's image, name, position, and listing count.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

// Retrieve agent details
$agent_details = wpestate_return_agent_details('', get_the_ID());

$agent_thumbnail = wpestate_get_agent_thumbnail();
?>

<div class="wpresidence_agent_unit_wrapper agent_card_4 <?php echo esc_attr($agent_unit_col_class['col_class']); ?> listing_wrapper  ">         
    <div class="agent_unit agent_unit_type_4" data-link="<?php echo esc_url($agent_details['link']); ?>">
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
        printf(
            '<h4><a href="%s">%s</a></h4>',
            esc_url($agent_details['link']),
            esc_html($agent_details['realtor_name'])
        );
        printf(
            '<div class="agent_position">%s</div>',
            esc_html($agent_details['realtor_position'])
        );
        ?>
    </div>
</div>