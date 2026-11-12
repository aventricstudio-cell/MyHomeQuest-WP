<?php
/** MILLDONE
 * Template for displaying single studio
 *
 * This template is responsible for displaying individual blog posts in the WPEstate theme.
 * It includes various components such as breadcrumbs, post content, meta information,
 * social sharing, comments, and related posts.
 *
 * @package WPEstate
 * @subpackage Templates
 * @since 1.0
 * @version 2.0
 */

global $post;
get_header();
$wpestate_options = get_query_var('wpestate_options');
?>

<div class="wpresidence-content-container-wrapper col-12 row flex-wrap">
    <?php get_template_part('templates/breadcrumbs'); ?>
    
    <div class="col-xs-12 col-lg-12 p-0  single_width_page">
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
 
   
</div>  

<?php get_footer(); ?>