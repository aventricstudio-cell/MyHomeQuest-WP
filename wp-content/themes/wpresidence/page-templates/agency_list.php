<?php
/** MILLDONE
 * Template Name: Agency list
 * src: page-templates\agency_list.php
 * This template displays a list of agencies in a grid format.
 * It includes functionality for pagination and dynamic content loading.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

// Ensure the required functionality is available
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

?>

<div class="row wpresidence_page_content_wrapper">
    <?php get_template_part('templates/breadcrumbs'); ?>
    
    <div class="p-0 p04mobile wpestate_column_content <?php echo esc_attr($wpestate_options['content_class']); ?>">
        <?php get_template_part('templates/ajax_container'); ?>
        
        <?php
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
        ?>
        
        <div id="listing_ajax_container_agent" class="row"> 
            <?php
            // Set up the query arguments for fetching agencies
            $args = array(
                'post_type'      => 'estate_agency',
                'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
                'posts_per_page' => 10,
                'order'          => $order,
                'orderby'        => $orderby,
                'cache_results'  => false
            );

            // Create a new query to fetch agencies
            $agency_query = new WP_Query($args);
            
            // Loop through each agency and display their information
            while ($agency_query->have_posts()): $agency_query->the_post();
                $postID=get_the_ID();
                echo '<div class="agency_unit_wrap">';
                include( locate_template( 'templates/agency__developers_cards_templates/agency_developer_unit.php'));
                echo '</div>';
            endwhile;

            // Reset postdata
            wp_reset_postdata();
            ?>
        </div>
        
        <?php 
        // Display pagination
        wpestate_pagination($agency_query->max_num_pages, $range = 2); 
        ?>
    </div>

    <?php  
    // Include the sidebar
    include get_theme_file_path('sidebar.php');
    ?>
</div>   

<?php
// Resume cache addition
wp_suspend_cache_addition(false);

// Load the footer
get_footer();
?>