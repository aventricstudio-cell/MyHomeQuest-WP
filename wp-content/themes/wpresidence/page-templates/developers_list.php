<?php
/**MILLDONE
 * Template Name: Developer list
 * src: page-templates\developers_list.php
 * This template displays a list of developers in a grid format.
 * It includes functionality for pagination and dynamic content loading.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

// Ensure the required functionality is available
if(!function_exists('wpestate_residence_functionality')) {
    esc_html_e('This page will not work without WpResidence Core Plugin, Please activate it from the plugins menu!','wpresidence');
    exit();
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
    <div class="p-0 p04mobile wpestate_column_content <?php print esc_attr($wpestate_options['content_class']);?>">
        <?php get_template_part('templates/ajax_container'); ?>
        <?php 
        // Display page title and content if available
        while (have_posts()) : the_post(); 
            if (esc_html(get_post_meta($post->ID, 'page_show_title', true)) != 'no') { ?>
                <h1 class="entry-title"><?php the_title(); ?></h1>
            <?php } ?>
            <div class="single-content"><?php the_content();?></div>
            <?php
        endwhile; 
        ?>                 
        
        <div id="listing_ajax_container_agent" class="row"> 
        <?php
        // Set up the query arguments for fetching developers
        $args = array(
            'post_type'      => 'estate_developer',
            'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
            'posts_per_page' => 10,
            'order'          => $order,
            'orderby'        => $orderby,
            'cache_results'  => false
        );

        // Create a new query to fetch developers
        $developer_query = new WP_Query($args);
        
        // Loop through each developer and display their information
        while ($developer_query->have_posts()): $developer_query->the_post();
            $postID=get_the_ID();
            echo '<div class="dev_unit_wrapper">';
            include( locate_template( 'templates/agency__developers_cards_templates/agency_developer_unit.php'));
            echo '</div>';
        endwhile;
        
        // Reset postdata
        wp_reset_postdata();
        ?> 
        </div>
        <?php 
        // Display pagination
        wpestate_pagination($developer_query->max_num_pages, $range = 2); 
        ?>         
       
    </div><!-- end 9col container-->
    
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