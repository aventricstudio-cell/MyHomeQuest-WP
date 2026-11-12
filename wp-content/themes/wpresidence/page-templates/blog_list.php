<?php
/**
 * Template Name: Blog list page
 * MILLDONE
 * This template is used to display a list of blog posts in the WPResidence theme.
 * It provides a flexible structure for showcasing blog content with pagination.
 *
 * @package WPResidence
 * @subpackage Templates
 * @since 1.0
 */
 
// Include the header template
get_header();
// Retrieve theme options, with a default empty array if not set
$wpestate_options = get_query_var('wpestate_options', array());

?>

<div class="row wpresidence_page_content_wrapper">
    <?php 
    // Include the breadcrumbs template
    get_template_part('templates/breadcrumbs'); 
    ?>
    
    <div class="p-0 p04mobile wpestate_column_content <?php echo esc_attr(isset($wpestate_options['content_class']) ? $wpestate_options['content_class'] : ''); ?>">
        <?php 
        // Include the AJAX container template
        get_template_part('templates/ajax_container'); 
        
        // Start the WordPress loop
        while (have_posts()) : the_post(); 
            // Check if the page title should be displayed
            if (esc_html(get_post_meta($post->ID, 'page_show_title', true)) == 'yes') { 
                ?>
                <h1 class="entry-title title_prop"><?php the_title(); ?></h1>
            <?php 
            } 
            ?>
            <div class="single-content"><?php the_content(); ?></div>   
        <?php 
        endwhile; 
        ?>

        <?php
        // Hook for content before the blog list
        do_action('wpestate_before_blog_list');
        ?>


        <div class="row">
            <?php
            // Set up the query for blog posts
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 0;
            $args = array(
                'post_type' => 'post',
                'paged'     => $paged,
                'post_status'    => 'publish'
            );

            // Allow developers to modify the query arguments
            $args = apply_filters('wpestate_blog_list_query_args', $args);

            // Create a new WP_Query instance for blog posts
            $blog_selection = new WP_Query($args);
            wpresidence_display_blog_list_as_html($blog_selection, $wpestate_options, $context = 'blog_list'); 
            
            ?>
        </div>
   

        <?php
        // Hook for content after the blog list
        do_action('wpestate_after_blog_list');
        ?>

        <?php 
        // Display pagination if there are posts
        if ($blog_selection->have_posts()) :
            wpestate_pagination($blog_selection->max_num_pages, 2); 
        endif;
        ?>    
    </div><!-- end main content container -->
    
    <?php  
    // Include the sidebar
    include get_theme_file_path('sidebar.php'); 
    ?>
</div>   

<?php 
// Include the footer
get_footer(); 
?>