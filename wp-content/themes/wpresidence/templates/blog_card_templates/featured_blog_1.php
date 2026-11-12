<?php
/** MILLDONE
 * Template for Featured Article Type 1
 * src: templates/blog_card_templates/featured_blog_1.php
 * This template displays a featured article with image, title, and excerpt.
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
$preview     = isset( $args['preview'][0] ) ? $args['preview'][0] : '';
$avatar      = isset( $args['avatar'] ) ? $args['avatar'] : '';
$title       = isset( $args['title'] ) ? $args['title'] : '';
$link        = isset( $args['link'] ) ? $args['link'] : '';
$second_line = isset( $args['second_line'] ) ? $args['second_line'] : '';
$content     = isset( $args['content'] ) ? $args['content'] : '';
?>

<div class="featured_article">
    <div class="featured_img">
        <a href="<?php echo esc_url( $link ); ?>">
            <img src="<?php echo esc_url( $preview ); ?>" data-original="<?php echo esc_url( $preview ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="lazyload img-responsive" />
        </a>
    </div>

    <div class="featured_article_title" data-link="<?php echo esc_url( $link ); ?>">
        <div class="blog_author_image" style="background-image: url(<?php echo esc_url( $avatar ); ?>);"></div>
        <h2 class="featured_type_2">
            <a href="<?php echo esc_url( $link ); ?>">
                <?php echo esc_html( wp_trim_words( $title, 7, '...' ) ); ?>
            </a>
        </h2>
        <div class="featured_article_secondline"><?php echo esc_html( $second_line ); ?></div>
        <div class="featured_article_content">
            <?php echo wp_kses_post( $content ); ?>
        </div>
    </div>
</div>