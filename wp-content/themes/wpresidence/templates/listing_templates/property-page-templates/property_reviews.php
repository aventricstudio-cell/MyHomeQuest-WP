<?php
/** MILLDONE
 * Property Reviews Template
 * src: templates\listing_templates\property-page-templates\property_reviews.php
 * This template displays reviews for a property, including a list of existing reviews
 * and a form for logged-in users to submit or edit their review.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 */

 get_template_part('templates/reviews/reviews');

// Determine the property ID based on context
if(!isset($property_id)){
    $property_id = $post->ID;
}





// Display the reviews section header if not in tab mode
if ($is_tab !== 'yes') {
    echo '<div class="property_reviews_wrapper panel-group property-panel" id="property_reviews_area">';
    echo '<h4>' . esc_html($label) . '</h4>';
}

// Add to sticky menu array
$sticky_menu_array['property_reviews_area'] = esc_html__('Property Reviews', 'wpresidence');

// Fetch and display existing reviews
$wp_estate_no_of_reviews = wpresidence_get_option('wp_estate_no_of_reviews', -1);
$args = array(
    'number' => $wp_estate_no_of_reviews,
    'post_id' => $property_id,
);

$comments = get_comments($args);
$comments_count = 0;
$stars_total = 0;
$review_templates = '';

foreach ($comments as $comment) {
    if (wp_get_comment_status($comment->comment_ID) === 'approved') {
        $comments_count++;
        $review_templates .= wpestate_generate_review_template($comment, $property_id);
        $stars_total += floatval(get_comment_meta($comment->comment_ID, 'review_stars', true));
    }
}

// Display reviews summary if there are reviews
if ($comments_count > 0) {
    $average_rating = ceil($stars_total / $comments_count);
    wpestate_display_reviews_summary($comments_count, $average_rating, $review_templates);
}

// Display review form for logged-in users
if (is_user_logged_in()) {
    wpestate_display_review_form($property_id);
} else {
    echo '<h5 class="review_notice">' . esc_html__('You need to ', 'wpresidence') . 
         '<span id="login_trigger_modal">' . esc_html__('login', 'wpresidence') . '</span> ' .
         esc_html__('in order to post a review ', 'wpresidence') . '</h5>';
}

// Close the reviews wrapper if not in tab mode
if ($is_tab !== 'yes') {
    echo '</div>';
}

?>

<?php wp_enqueue_script('wpestate_reviews'); ?>