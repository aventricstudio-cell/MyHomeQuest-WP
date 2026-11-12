<?php
/** MILLDONE
 * Related Posts Template
 * src: templates\blog_post\related_posts.php
 * This template displays related posts based on shared tags.
 * It's designed to show a specified number of related posts with thumbnails.
 *
 * @package WPEstate
 * @subpackage Templates
 * @since 1.0
 * @version 2.0
 */

// Ensure this file is only used as part of a WordPress theme
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post;
$wpestate_options = get_query_var('wpestate_options');



// Determine if we're using a full-width layout
$is_full_width = (strpos(sanitize_html_class($wpestate_options['content_class']), 'col-md-12') === false);

// Set the column class and number of posts to display based on layout and settings
$similar_posts_count = intval(wpresidence_get_option('wp_estate_similar_blog_per_row', '',2));
$order_by = wpresidence_get_option('wp_estate_similar_blog_listins_order_by');
$order = wpresidence_get_option('wp_estate_similar_blog_listins_order');
$selected_categ = wpresidence_get_option('wp_estate_simialar_blog_taxes');

$posts_to_show = $similar_posts_count == 3 ? ($is_full_width ? 3 : 2) : ($is_full_width ? 4 : 3);

// Fetch property taxonomies
$taxonomies = array(
    'category' => wp_get_post_categories($post->ID, array( 'fields' => 'ids' )),
    'post_tag' => wp_get_post_tags($post->ID, array( 'fields' => 'ids' )),
);
// Prepare taxonomy query arguments
// $tax_query = array('relation' => 'OR');
$selected_terms = array();
foreach ($taxonomies as $taxonomy => $terms) {
    if ($terms && (empty($selected_categ) || in_array($taxonomy, $selected_categ))) {
        $tax_query[] = array(
            'taxonomy' => $taxonomy,
            'field' => 'term_id',
            'terms' => $terms
        );
        $selected_terms[] = $terms;
    }
}

//Dermine which column class we will use
$blog_unit_class_request    = wpestate_blog_unit_column_selector($wpestate_options,'similar','');
$blog_unit_class            = $blog_unit_class_request['col_class'];

// Only proceed if the post has selected terms
if ($selected_terms) {

    // Set up the query arguments for related posts
    $args = array(
        'tax_query'         => $tax_query,
        'post__not_in'      => array($post->ID),
        'posts_per_page'    => $posts_to_show,
        'post_status'       => 'publish',
        'orderby'           => $order_by,
        'order'             => $order,
        'meta_query'        => array(
            array(
                'key'     => '_thumbnail_id',
                'compare' => 'EXISTS'
            ),
        )
    );

    // Reset the main query
    wp_reset_query();

    // Execute the query
    $related_query = new WP_Query($args);

?>

    <div class="related_posts row"> 
        <h3><?php esc_html_e('Similar Posts', 'wpresidence'); ?></h3>
        <?php wpresidence_display_blog_list_as_html( $related_query, $wpestate_options, 'similar' ); ?>
    </div>

<?php
} // End if tags
?>