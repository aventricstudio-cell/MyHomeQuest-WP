<?php
/**
 * Template Name: Agents list
 * MILLDONE
 * src page-templates\agents_list.php
 * This template displays a list of agents in a grid format.
 * It includes functionality for pagination and dynamic column sizing.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */
// Check if the required functionality is available
if (!function_exists('wpestate_residence_functionality')) {
    wp_die(
        __('This page requires the WpResidence Core Plugin. Please activate it from the plugins menu.', 'wpresidence'),
        __('Plugin Activation Required', 'wpresidence'),
        array('back_link' => true)
    );
}
// Load the header
get_header();
// Suspend cache addition for performance
wp_suspend_cache_addition(true);
// Retrieve theme options
$wpestate_options = get_query_var('wpestate_options');

$order = (get_post_meta( $post->ID, 'realtor_order', true )) ?: 'desc';
$orderby = (get_post_meta( $post->ID, 'realtor_order_by', true )) ?: 'date';

// Action before main content
do_action('wpresidence_before_agents_list_content');
// Start the main content area
?>
<div class="row wpresidence_page_content_wrapper">
    <?php
    // Include breadcrumbs
    get_template_part('templates/breadcrumbs');
    ?>
    <div class="p-0 wpestate_column_content p04mobile <?php echo esc_attr($wpestate_options['content_class']); ?>">
        <?php
        // Include AJAX container
        get_template_part('templates/ajax_container');
        // Display page title and content if available
        while (have_posts()) : the_post();
            if (esc_html(get_post_meta($post->ID, 'page_show_title', true)) != 'no') :
                ?>
                <h1 class="entry-title"><?php the_title(); ?></h1>
                <?php
            endif;
            ?>
            <div class="single-content"><?php the_content(); ?></div>
            <?php
        endwhile;
        // Action after page content
        do_action('wpresidence_after_agents_list_page_content');
        ?>
        <div id="listing_ajax_container_agent" class="row">
            <?php
            // Set up the query arguments for fetching agents
            $args = array(
                'cache_results'  => false,
                'post_type'      => 'estate_agent',
                'paged'          => $paged,
                'order'          => $order,
                'orderby'        => $orderby,
                'posts_per_page' => apply_filters('wpresidence_agents_per_page', 10)
            );
            
            // Filter for modifying query arguments
            $args = apply_filters('wpresidence_agents_query_args', $args);

            // Create a new query to fetch agents
            $agent_selection = new WP_Query($args);
            // Action before agent loop
            do_action('wpresidence_before_agents_loop');
            // Loop through each agent and display their information


            wpresidence_display_agent_list_as_html($agent_selection,'estate_agent', $wpestate_options ,'agent_list');
	


            // Action after agent loop
            do_action('wpresidence_after_agents_loop');
            ?>
        </div>
        <?php
        // Display pagination
        wpestate_pagination($agent_selection->max_num_pages, $range = 2);
        ?>
    </div>
    <?php  
    // Include the sidebar
    include get_theme_file_path('sidebar.php');
    ?>
</div>  
<?php
// Action after main content
do_action('wpresidence_after_agents_list_content');
// Resume cache addition
wp_suspend_cache_addition(false);
// Load the footer
get_footer();
?>