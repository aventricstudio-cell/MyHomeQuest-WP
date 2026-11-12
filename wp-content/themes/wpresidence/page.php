<?php
/** MILLDONE
 * Page Template
 * src: page.php
 * This template handles the display of a page in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

global $post;
get_header();
$wpestate_options = get_query_var('wpestate_options');
?>

<div class="wpresidence-content-container-wrapper col-12 row flex-wrap">
    <?php get_template_part('templates/breadcrumbs'); ?>
    
    <div class="col-xs-12 <?php echo esc_attr($wpestate_options['content_class']); ?> single_width_page">
        <?php get_template_part('templates/ajax_container'); ?>
        
        <?php 
        while (have_posts()) : the_post(); 
            if (esc_html(get_post_meta($post->ID, 'page_show_title', true)) != 'no') : 
        ?>
                <h1 class="entry-title"><?php the_title(); ?></h1>
        <?php 
            endif; 
        ?>
            <div class="single-content"><?php the_content(); ?></div><!-- single content -->
        
        <?php
            if (comments_open() || get_comments_number()) :
                comments_template('', true);
            endif;
        
        endwhile; // end of the loop. 
        ?>
    </div>
 
    <?php include get_theme_file_path('sidebar.php'); ?>
</div>  

<?php get_footer(); ?>