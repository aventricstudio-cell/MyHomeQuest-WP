<?php
/** MILLDONE
 * Template part for displaying post meta information
 * src: templates\blog_post\post_meta.php
 * This template displays meta information for blog posts including
 * the author, publication date, categories, and comment count.
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
?>

<div class="meta-info">
    <div class="meta-element">
        <i class="far fa-calendar-alt meta_icon firsof"></i>
        <?php
        printf(
            '%s %s %s %s',
            esc_html__('Posted by', 'wpresidence'),
            get_the_author(),
            esc_html__('on', 'wpresidence'),
            get_the_date()
        );
        ?>
    </div>
    <div class="meta-element">
        <i class="far fa-file meta_icon"></i>
        <?php the_category(', '); ?>
    </div>
    <div class="meta-element">
        <i class="far fa-comment meta_icon"></i>
        <?php 
        comments_number(
            esc_html__('0 Comments', 'wpresidence'),
            esc_html__('1 Comment', 'wpresidence'),
            esc_html__('% Comments', 'wpresidence')
        );
        ?>
    </div>
</div>