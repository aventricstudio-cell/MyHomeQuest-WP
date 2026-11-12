<?php
/** MILLDONE
 * Template Name: GDPR Terms
 * src: page-templates\gdpr_terms.php
 * This template handles the display of the GDPR Terms page in the WpResidence theme.
 * It provides a standard layout for displaying the page content with breadcrumbs and sidebar.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

get_header();

global $post;
$wpestate_options = get_query_var('wpestate_options');
?>

<div class="row wpresidence_page_content_wrapper">
    <?php
    // Display breadcrumbs
    get_template_part('templates/breadcrumbs');
    ?>

    <div class="p-0 p04mobile wpestate_column_content col-xs-12 <?php echo esc_attr($wpestate_options['content_class']); ?>">
        <?php
        // Display AJAX container
        get_template_part('templates/ajax_container');

        // Main content loop
        while (have_posts()) : the_post();
            // Check if page title should be displayed
            $show_title = esc_html(get_post_meta($post->ID, 'page_show_title', true));
            if ($show_title !== 'no') {
                ?>
                <h1 class="entry-title"><?php the_title(); ?></h1>
                <?php
            }
            ?>
            <div class="single-content">
                <?php the_content(); ?>
            </div>
        <?php
        endwhile;
        ?>
    </div>

    <?php
    // Include sidebar
    get_template_part('sidebar');
    ?>
</div>

<?php
get_footer();
?>