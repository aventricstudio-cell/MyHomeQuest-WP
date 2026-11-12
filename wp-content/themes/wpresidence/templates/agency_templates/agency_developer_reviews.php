<?php
/**MILLDONE
 * Developer/Agency Reviews Template
 * src: templates/agency_templates/agency_developer_reviews.php
 * This template handles the display and submission of reviews for both developers and agencies in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Reviews
 * @since 1.0.0
 */

// Determine if we're on a developer or agency page
$is_developer = is_singular('estate_developer');
$review_type = $is_developer ? 'Developer' : 'Agency';

// Fetch the most recent 15 comments for this post
$comments = get_comments(array(
    'number' => '15',
    'post_id' => get_the_ID(),
));

// Initialize counters and storage for review data
$comments_count = 0;
$stars_total = 0;
$review_templates = '';

// Process each comment
foreach ($comments as $comment) :
    // Only process approved comments
    if (wp_get_comment_status($comment->comment_ID) !== 'unapproved') {
        $comments_count++;
        
        // Fetch reviewer details
        $reviewer_name = wpresidence_agency_developer_get_reviewer_name($comment);
        $preview_img = wpresidence_agency_developer_get_reviewer_image($comment);
        
        // Get the rating for this review
        $rating = get_comment_meta($comment->comment_ID, 'review_stars', true);
        $stars_total += $rating;
        
        // Generate the HTML for this review and add it to our collection
        $review_templates .= wpresidence_agency_developer_generate_review_template($comment, $reviewer_name, $preview_img, $rating);
    }
endforeach;

// Start of the HTML output
?>
<div class="property_reviews_wrapper">
    <h4><?php echo esc_html(sprintf(__('%s Reviews', 'wpresidence'), $review_type)); ?></h4>
    
    <?php 
    // Only display the reviews section if there are approved comments
    if ($comments_count > 0) :
        // Calculate the average rating
        $average_rating = ceil($stars_total / $comments_count);
    ?>
        <div class="property_page_container for_reviews">
            <div class="listing_reviews_wrapper">
                <div class="listing_reviews_container">
                    <h3 id="listing_reviews" class="panel-title">
                        <?php
                            // Display the number of reviews
                            echo esc_html(sprintf(_n('%d Review', '%d Reviews', $comments_count, 'wpresidence'), $comments_count));
                        ?>
                        <span class="property_ratings">
                        <?php
                            // Display the average rating as stars
                            echo wpresidence_agency_developer_generate_star_rating($average_rating);
                        ?>
                        </span>
                    </h3>
                    <?php 
                    // Output all the processed review templates
                    echo wp_kses_post($review_templates); 
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php 
    // Display the review submission form
    wpresidence_agency_developer_display_review_form($review_type); 
    ?>
</div>

<?php wp_enqueue_script('wpestate_reviews'); ?>