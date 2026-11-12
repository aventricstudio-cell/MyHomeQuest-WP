<?php
/**
 * Estate Reviews List Template
 * src: templates/reviews/reviews-list.php
 * This template displays a paginated list of reviews for a specific post (property).
 * It includes average rating, total count, and pagination controls.
 *
 * @package WPResidence
 * @subpackage Reviews
 * @since 1.0.0
 */

if (isset( $args['post_id'] ) )
    $post_id = $args['post_id'];

if (empty($post_id) || is_null($post_id)) {
    global $post;
    $post_id = $post->ID;
}

$per_page = intval(wpresidence_get_option('wp_estate_no_of_reviews', ''));

if (!isset( $per_page ) || $per_page < 1) {
    $per_page = 5; // Default to 5 if option is not set or invalid
}

echo wpestate_display_reviews_summary_paginated( $post_id, $per_page );