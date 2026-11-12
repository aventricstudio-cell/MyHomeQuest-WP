<?php
/** MILLDONE
 *  404 Page
 * src: 404.php
 * This is the template for displaying the 404 (Page Not Found) page in the WpResidence theme.
 * It provides a custom 404 page with latest listings and articles.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

get_header();
$wpestate_options = get_query_var('wpestate_options');
?>

<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) : ?>
    <div class="row wpresidence_page_content_wrapper">
        <?php get_template_part('templates/breadcrumbs'); ?>
        <div class="<?php echo esc_attr($wpestate_options['content_class']); ?>">
            <?php get_template_part('templates/ajax_container'); ?>
            
            <h1 class="entry-title"><?php esc_html_e('Page not found', 'wpresidence'); ?></h1>
            <div class="single-content row col-md-12">
                <p>
                    <?php esc_html_e('We\'re sorry. Your page could not be found, But you can check our latest listings & articles', 'wpresidence'); ?>
                </p>
                
                <div class="list404 col-12 col-md-6">
                    <h3><?php esc_html_e('Latest Listings', 'wpresidence'); ?></h3>
                    <?php echo wpestate_get_recent_posts('estate_property'); ?>
                </div>
                
                <div class="list404 col-12 col-md-6"">
                    <h3><?php esc_html_e('Latest Articles', 'wpresidence'); ?></h3>
                    <?php echo wpestate_get_recent_posts('post'); ?>
                </div>
            </div><!-- single content-->
        </div>
    </div>
<?php endif; ?>


<?php 
// Include the footer
get_footer(); 



/**
 * Get recent posts for a specific post type.
 *
 * @param string $post_type The post type to query.
 * @return string HTML list of recent posts.
 */
function wpestate_get_recent_posts($post_type) {
    $args = array(
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => 10,
    );
    $recent_posts = new WP_Query($args);
    
    ob_start();
    if ($recent_posts->have_posts()) :
        echo '<ul>';
        while ($recent_posts->have_posts()) : $recent_posts->the_post();
            printf('<li><a href="%s">%s</a></li>', esc_url(get_permalink()), esc_html(get_the_title()));
        endwhile;
        echo '</ul>';
    endif;
    wp_reset_postdata();
    
    return ob_get_clean();
}
?>