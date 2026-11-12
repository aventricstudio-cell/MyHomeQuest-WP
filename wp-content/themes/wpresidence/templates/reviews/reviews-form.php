<?php
/** * Estate Review Form Template
 * src: templates/reviews/reviews-form.php
 * This template displays a form for users to submit reviews for a property.
 * It includes fields for review title, reviewer name, email, rating, and review content.
 * @package WPResidence
 * @subpackage Reviews
 * @since 1.0.0
 */

 global $post;

if (is_user_logged_in()) {
    wpestate_display_review_form($post->ID);
} else {
    echo '<h5 class="review_notice">' . esc_html__('You need to ', 'wpresidence') . 
         '<span id="login_trigger_modal">' . esc_html__('login', 'wpresidence') . '</span> ' .
         esc_html__('in order to post a review ', 'wpresidence') . '</h5>';
}
?>