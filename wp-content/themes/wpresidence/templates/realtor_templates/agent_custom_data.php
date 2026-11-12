<?php
/** MILLDONE
 * Agent Custom Data Display
 * src: templates\realtor_templates\agent_custom_data.php
 * This file is responsible for displaying custom data fields for an agent
 * in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage AgentProfile
 * @since 1.0.0
 */

// Retrieve the custom data for the agent
$agent_custom_data = get_post_meta( $post->ID, 'agent_custom_data', true );

echo wpresidence_display_agent_custom_fields( $post->ID );

/* Old Custom data display
// Check if the custom data is an array and not empty
if ( is_array( $agent_custom_data ) && ! empty( $agent_custom_data ) ) {
    echo '<div class="custom_parameter_wrapper row">';

    

    // Loop through each custom data item
    // foreach ( $agent_custom_data as $data_item ) {
    //     // Ensure label and value keys exist
    //     if ( isset( $data_item['label'] ) && isset( $data_item['value'] ) ) {
    //         ?>
    //         <div class="col-md-4">
    //             <span class="custom_parameter_label">
    //                 <?php echo esc_html( $data_item['label'] ); ?>
    //             </span>
    //             <span class="custom_parameter_value">
    //                 <?php echo esc_html( $data_item['value'] ); ?>
    //             </span>
    //         </div>
    //         <?php
    //     }
    // }

    echo '</div>';
}
*/
?>