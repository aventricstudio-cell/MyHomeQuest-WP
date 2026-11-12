<?php
/** MILLDONE
 * Archive Template
 * src: archive.php
 * This template handles the display of archive pages for various post types
 * in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Archive
 * @since WpResidence 1.0
 */

get_header();
$wpestate_options = get_query_var('wpestate_options');

// Exit if trying to access message or invoice archives
if (in_array(get_post_type(), ['wpestate_message', 'wpestate_invoice'])) {
    exit();
}

// Check if Elementor is handling the archive location
if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('archive')) :
    ?>
    <div class="row wpresidence_page_content_wrapper">
        <?php get_template_part('templates/breadcrumbs'); ?>
        <div class="<?php echo esc_attr($wpestate_options['content_class']); ?>">
            <?php get_template_part('templates/ajax_container'); ?>
            
            <h1 class="entry-title">
                <?php
                if (is_category()) {
                    printf(esc_html__('Category Archives: %s', 'wpresidence'), '<span>' . single_cat_title('', false) . '</span>');
                } elseif (is_day()) {
                    printf(esc_html__('Daily Archives: %s', 'wpresidence'), '<span>' . get_the_date() . '</span>');
                } elseif (is_month()) {
                    printf(esc_html__('Monthly Archives: %s', 'wpresidence'), '<span>' . get_the_date(_x('F Y', 'monthly archives date format', 'wpresidence')) . '</span>');
                } elseif (is_year()) {
                    printf(esc_html__('Yearly Archives: %s', 'wpresidence'), '<span>' . get_the_date(_x('Y', 'yearly archives date format', 'wpresidence')) . '</span>');
                } else {
                    esc_html_e('Blog Archives', 'wpresidence');
                }
                ?>
            </h1>
            
            <div class="blog_list_wrapper row">
                <?php wpresidence_display_blog_list_as_html($wp_query, $wpestate_options, 'archive_list'); ?>
            </div>
            
            <?php wpestate_pagination('', 2); ?>
        </div>
        
        <?php include get_theme_file_path('sidebar.php'); ?>
    </div>
    <?php
endif;

get_footer();
?>