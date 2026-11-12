<?php
/** MILLDONE
 * Agent Page Template - Version 2
 * src: templates\realtor_templates\agent_template_2.php
 * This template is responsible for displaying the main content of an individual agent's page
 * in the WPResidence theme, using a full-width layout. It includes the agent's details, 
 * listings, and reviews.
 *
 * @package WPResidence
 * @subpackage AgentProfile
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Set full-width layout
$wpestate_options['content_class'] = 'col-lg-12';
$wpestate_options['sidebar_class'] = '';
?>

<div class="row wpestate_agent_header2_content wpresidence-content-container-wrapper col-12 d-flex flex-wrap">
    <div class="p-0 p-lg-3 col-md-12">
        <?php
        while (have_posts()) : the_post();
            $agent_id = get_the_ID();
            $realtor_details = wpestate_return_agent_details('', $agent_id);
            ?>
            <div class="single-content single-agent">
                <?php
                // Include agent details template (version 2)
                $agent_context = "agent_page";
                include(get_theme_file_path('templates/realtor_templates/agent_details_2.php'));
                ?>
            </div>
        <?php 
        endwhile;

        // Display agent listings if enabled
        if (wpresidence_get_option('wp_estate_agent_page_show_my_listings', '') === 'yes') {
            include(locate_template('templates/realtor_templates/agent_listings.php'));
        }

        // Display agent reviews if enabled
        $wp_estate_show_reviews = wpresidence_get_option('wp_estate_show_reviews_block', '');
        if (is_array($wp_estate_show_reviews) && in_array('agent', $wp_estate_show_reviews)) {
            // include(locate_template('templates/realtor_templates/agent_reviews.php'));
            get_template_part('templates/reviews/reviews');
        }
        ?>
    </div>
</div>