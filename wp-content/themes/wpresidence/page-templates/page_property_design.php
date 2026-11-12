<?php
/** MILLDONE
 * Template Name: Custom Property Page Template
 * src: page-templates/page_property_design.php
 * This is the template for displaying custom property page designs in the WpResidence theme.
 * It provides a structure for displaying property information with sidebar support.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

// Check if WpResidence Core Plugin is active
if (!function_exists('wpestate_residence_functionality')) {
    esc_html_e('This page will not work without WpResidence Core Plugin. Please activate it from the plugins menu!', 'wpresidence');
    exit();
}

global $post;
get_header();
$wpestate_options = get_query_var('wpestate_options');
?>

<div class="row">
    <?php get_template_part('templates/breadcrumbs'); ?>
    
    <div class="col-xs-12 <?php echo esc_attr($wpestate_options['content_class']); ?>">
        <?php get_template_part('templates/ajax_container'); ?>
        
        <?php 
        while (have_posts()) : the_post(); 
            // Display the page title if not hidden
            if (esc_html(get_post_meta($post->ID, 'page_show_title', true)) != 'no') : 
        ?>
                <h1 class="entry-title"><?php the_title(); ?></h1>
        <?php 
            endif;
        ?>
            <div class="single-content"><?php the_content(); ?></div><!-- single content -->
        
        <?php 
        endwhile; 
        // Uncomment the following line to enable comments
        // comments_template('', true);
        ?>
    </div>
   
    <?php include get_theme_file_path('sidebar.php'); ?>
</div>  

<?php get_footer(); ?>