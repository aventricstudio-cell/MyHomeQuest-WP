<?php
/** MILLDONE
 * Agent Page Template
 * src: templates\realtor_templates\agent_template_1.php
 * This template is responsible for displaying the main content of an individual agent's page
 * in the WPResidence theme. It includes the agent's details, contact form, listings, and reviews.
 *
 * @package WPResidence
 * @subpackage AgentProfile
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
// set the agentID for contact forms
$agentID= $post->ID;

?>

<div class="row wpresidence-content-container-wrapper col-12 d-flex flex-wrap">
    <?php 
    // Include breadcrumbs template
    get_template_part('templates/breadcrumbs'); 
    ?>

    <div class="p-0 p04mobile <?php echo esc_attr($wpestate_options['content_class']); ?> single_width_blog wpestate_column_content">
        <?php
        while (have_posts()) : the_post();
            $agent_id =$agentID= get_the_ID();

            $realtor_details = wpestate_return_agent_details('', $agent_id);
        ?>
            <div class="single-content single-agent">
                <?php
              
                // Include agent details template
                $agent_context = "agent_page";
                include(get_theme_file_path('templates/realtor_templates/agentdetails.php'));
             
                ?>

                <div class="agent_contanct_form row wpestate_contact_form_parent">
                    <?php
                    // Include contact form template
                    $context = 'agent_page';

                    // Unset propertyID to avoid conflicts
                    unset($propertyID);
                    include(locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php'));
                    ?>
                </div>
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

    <?php 
    // Include sidebar
    get_sidebar();
    ?>
</div>