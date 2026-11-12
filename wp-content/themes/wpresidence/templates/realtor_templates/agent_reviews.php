<?php
/**MILLDONE
 * Agent Reviews Display
 * src: templates\realtor_templates\agent_reviews.php
 * This file is responsible for displaying agent reviews in the WPResidence theme.
 * It shows existing reviews, allows logged-in users to submit or edit reviews,
 * and displays a login prompt for non-logged-in users.
 *
 * @package WPResidence
 * @subpackage AgentProfile
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

global $post;

// Fetch reviews
$args = array(
    'number' => '15',
    'post_id' => $post->ID,
);
$comments = get_comments($args);
$comments_count = 0;
$stars_total = 0;
$review_templates = '';

// Process reviews
foreach ($comments as $comment) {
    if (wp_get_comment_status($comment->comment_ID) !== 'unapproved') {
        $comments_count++;
        $userId = $comment->user_id;

        // Determine reviewer name
        if ($userId == 1) {
            $reviewer_name = "admin";
            $userid_agent = get_user_meta($userId, 'user_agent_id', true);
        } else {
            $userid_agent = get_user_meta($userId, 'user_agent_id', true);
            $reviewer_name = $userid_agent ? get_the_title($userid_agent) : $comment->comment_author;
        }

        // Get reviewer image
        $preview_img = get_theme_file_uri('/img/default_user_agent.png');
        if ($userid_agent) {
            $thumb_id = get_post_thumbnail_id($userid_agent);
            $preview = wp_get_attachment_image_src($thumb_id, 'thumbnail');
        } else {
            $user_small_picture_id = get_the_author_meta('small_custom_picture', $comment->user_id, true);
            $preview = wp_get_attachment_image_src($user_small_picture_id, 'agent_picture_thumb');
        }
        if ($preview) {
            $preview_img = $preview[0];
        }

        // Get review details
        $review_title = get_comment_meta($comment->comment_ID, 'review_title', true);
        $rating = get_comment_meta($comment->comment_ID, 'review_stars', true);
        $stars_total += $rating;

        // Generate review HTML
        ob_start();
        ?>
        <div class="listing-review">
            <div class="review-list-content norightpadding">
                <div class="reviewer_image" style="background-image: url(<?php echo esc_url($preview_img); ?>);"></div>
                <div class="reviwer-name"><?php echo esc_html__('Posted by ', 'wpresidence') . esc_html($reviewer_name); ?></div>
                <div class="review-title"><?php echo esc_html($review_title); ?></div>
                <div class="property_ratings">
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                    }
                    ?>
                    <span class="ratings-star">(<?php echo esc_html($rating . ' ' . __('of', 'wpresidence') . ' 5'); ?>)</span>
                </div>
                <div class="review-date">
                    <?php echo esc_html__('Posted on ', 'wpresidence') . get_comment_date('j F Y', $comment->comment_ID); ?>
                </div>
                <div class="review-content">
                    <?php echo wp_kses_post($comment->comment_content); ?>
                </div>
            </div>
        </div>
        <?php
        $review_templates .= ob_get_clean();
    }
}

// Display reviews
?>
<div class="property_reviews_wrapper agent_reviews_wrapper">
    <h4><?php esc_html_e('Agent Reviews', 'wpresidence'); ?></h4>

    <?php if ($comments_count > 0) : 
        $list_rating = ceil($stars_total / $comments_count);
    ?>
        <div class="property_page_container for_reviews">
            <div class="listing_reviews_wrapper">
                <div class="listing_reviews_container">
                    <h3 id="listing_reviews" class="panel-title">
                        <?php
                        echo esc_html($comments_count . ' ' . __('Reviews', 'wpresidence'));
                        ?>
                        <span class="property_ratings">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $list_rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                            }
                            ?>
                        </span>
                    </h3>
                    <?php echo wp_kses_post($review_templates); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php
    // Review submission form for logged-in users
    if (is_user_logged_in()) :
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $user_review = get_comments(array(
            'user_id' => $userID,
            'post_id' => $post->ID,
            'number' => 1,
            'status' => 'approve'
        ));

        $review_title = '';
        $review_stars = '';
        $comment_content = '';
        $user_posted_comment = 0;

        if (!empty($user_review)) {
            $user_posted_comment = $user_review[0]->comment_ID;
            $review_title = get_comment_meta($user_posted_comment, 'review_title', true);
            $review_stars = get_comment_meta($user_posted_comment, 'review_stars', true);
            $comment_content = $user_review[0]->comment_content;
        }
    ?>
        <h5>
            <div class="review_tag">
                <?php
                if ($user_posted_comment) {
                    echo esc_html__('Update Review ', 'wpresidence');
                    if (wp_get_comment_status($user_posted_comment) == 'unapproved') {
                        echo ' - ' . esc_html__('pending approval', 'wpresidence');
                    }
                } else {
                    echo esc_html__('Write a Review ', 'wpresidence');
                }
                ?>
            </div>
        </h5>
        <div class="add_review_wrapper">
            <div class="rating">
                <span class="rating_legend"><?php esc_html_e('Your Rating & Review', 'wpresidence'); ?></span>
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    $star_class = $i <= $review_stars ? ' starselected starselected_click' : '';
                    echo '<span class="empty_star' . esc_attr($star_class) . '"></span>';
                }
                ?>
            </div>
            <input type="text" id="wpestate_review_title" name="wpestate_review_title" value="<?php echo esc_attr($review_title); ?>" class="form-control" placeholder="<?php esc_attr_e('Review Title', 'wpresidence'); ?>">
            <textarea rows="8" id="wpestare_review_content" name="wpestare_review_content" class="form-control" placeholder="<?php esc_attr_e('Your Review', 'wpresidence'); ?>"><?php echo esc_textarea($comment_content); ?></textarea>
            <?php if ($user_posted_comment) : ?>
                <input type="submit" class="wpresidence_button col-md-3" id="edit_review" data-coment_id="<?php echo esc_attr($user_posted_comment); ?>" data-listing_id="<?php echo esc_attr($post->ID); ?>" value="<?php esc_attr_e('Edit Review', 'wpresidence'); ?>">
            <?php else : ?>
                <input type="submit" class="wpresidence_button col-md-3" id="submit_review" data-listing_id="<?php echo esc_attr($post->ID); ?>" value="<?php esc_attr_e('Submit Review', 'wpresidence'); ?>">
            <?php endif; ?>
            <?php wp_nonce_field('wpestate_review_nonce', 'wpestate_review_nonce'); ?>
        </div>
    <?php else : ?>
        <h5 class="review_notice">
            <?php
            echo esc_html__('You need to ', 'wpresidence');
            echo '<span id="login_trigger_modal">' . esc_html__('login', 'wpresidence') . '</span> ';
            echo esc_html__('in order to post a review ', 'wpresidence');
            ?>
        </h5>
    <?php endif; ?>
</div>

<?php wp_enqueue_script('wpestate_reviews'); ?>