<?php
/** MILLDONE
 * Template for displaying single blog posts
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

// Load the header template
get_header();

// Retrieve theme options
$wpestate_options = get_query_var('wpestate_options');


$use_default_template=true;
// Check if Elementor is being used to render this page
if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {

    if( did_action( 'elementor/loaded' ) && function_exists('wpestate_single_post_enabled') && wpestate_single_post_enabled()  ) {
        wpestate_render_single_post();
        $use_default_template=false;
    }
}



// if we use default template
if($use_default_template):
    ?>
    <div id="post" <?php post_class('wpresidence-content-container-wrapper col-12 d-flex flex-wrap'); ?>>
        <?php
        // Load breadcrumbs template
        get_template_part('templates/breadcrumbs');
        ?>
        <div class="<?php echo esc_attr($wpestate_options['content_class']); ?> single_width_blog wpestate_column_content">
            <?php
            // Load AJAX container template
            get_template_part('templates/ajax_container');

            // Start the WordPress loop
            while (have_posts()) : the_post();

                // Check for featured image and get Pinterest-friendly image if available
                $pinterest = has_post_thumbnail() ? wp_get_attachment_image_src(get_post_thumbnail_id(), 'property_full_map') : null;

          
                ?>
                <article class="single-content single-blog">
                    <?php
                    // Load post slider template
                    get_template_part('templates/blog_post/postslider');

                    // Display post title if enabled in post meta
                    if (get_post_meta($post->ID, 'post_show_title', true) != 'no') {
                        printf('<h1 class="entry-title single-title">%s</h1>', get_the_title());
                    }

                    // Load post meta information template
                    get_template_part('templates/blog_post/post_meta');

                    // Display post content
                    the_content();

                    // Display pagination links for multi-page posts
                    wp_link_pages([
                        'before'           => '<nav class="page-links"><p>' . esc_html__('Pages:', 'wpresidence'),
                        'after'            => '</p></nav>',
                        'next_or_number'   => 'number',
                        'nextpagelink'     => esc_html__('Next page', 'wpresidence'),
                        'previouspagelink' => esc_html__('Previous page', 'wpresidence'),
                    ]);

                   

                    // Load social sharing template
                    get_template_part('templates/blog_post/blog_post_social_share');
                    ?>
                </article>

                <?php
                // Display comments if they're open or if there are comments
                if (comments_open() || get_comments_number()) {
                    comments_template('', true);
                }

                // Load related posts template
                get_template_part('templates/blog_post/related_posts');

            endwhile; // End of the WordPress loop
            ?>
        </div>
        <?php
        // Load sidebar template
        get_sidebar();
        ?>
    </div>
    <?php
endif;


// Load footer template
get_footer();
?>