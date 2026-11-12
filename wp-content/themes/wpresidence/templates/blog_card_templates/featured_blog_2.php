<?php
/** MILLDONE
 * Template for Featured Article Type 2
 * src: templates/blog_card_templates/featured_blog_2.php
 * This template displays a featured article with a full-width background image and overlay text.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0.0
 *
 * @var array $args Template arguments passed from the shortcode function
 */

// Ensure $args is set to prevent errors
$args = isset( $args ) ? $args : array();

// Extract variables from $args, providing default values
$preview = isset( $args['preview'][0] ) ? $args['preview'][0] : '';
$title   = isset( $args['title'] ) ? $args['title'] : '';
$link    = isset( $args['link'] ) ? $args['link'] : '';
?>

<div class="featured_article_type2">
    <div class="featured_img_type2" style="background-image:url(<?php echo esc_url( $preview ); ?>)">
        <div class="featured_gradient"></div>
        <div class="featured_article_type2_title_wrapper container-fluid px-3 px-lg-0">
            <div class="featured_article_label"><?php esc_html_e( 'Featured Article', 'wpresidence-core' ); ?></div>
            <h2 class="col-md-7"><?php echo esc_html( $title ); ?></h2>
            <div class="featured_read_more">
                <a href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'read more', 'wpresidence-core' ); ?></a>
                <i class="fas fa-angle-right"></i>
            </div>
        </div>
    </div>
</div>