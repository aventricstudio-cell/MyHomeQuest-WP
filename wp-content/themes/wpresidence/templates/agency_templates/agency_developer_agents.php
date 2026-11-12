<?php
/** MILLDONE
 * Agency Agents Query and Display
 * src: templates\agency_templates\agency_developer_agents.php	
 * This file handles the query and display of agent listings for an agency in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage AgencyAgents
 * @since 1.0.0
 */

// Get the agency user ID
$user_agency = get_post_meta($post->ID, 'user_meda_id', true);

// Only proceed if a valid agency user ID is found
if (!empty($user_agency)) {
    // Set up query arguments
    $args = array(
        'post_type'      => 'estate_agent',
        'author'         => $user_agency,
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );

    // Run the query
    $agent_query = new WP_Query($args);

    // Start output buffering
    ob_start();

    // Check if there are agents to display
    if ($agent_query->have_posts()) :
        ?>
        <div class="wpresidence_realtor_listings_wrapper agency_agents_wrapper row">
            <h3 class="agent_listings_title"><?php esc_html_e('Our Agents', 'wpresidence'); ?></h3>
            <?php
            // Display agent list
            wpresidence_display_agent_list_as_html($agent_query, 'estate_agent', $wpestate_options, 'agency_agents');
            ?>
        </div>
        <?php
    endif;

    // End output buffering and echo the content
    echo ob_get_clean();

}
?>