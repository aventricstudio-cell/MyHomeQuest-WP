<?php
/** MILLDONE
 * Property Reviews Helper Functions
 * src : libs/property_page_functions/property_reviews_section_functions.php
 * These functions support the property reviews template by generating
 * individual review templates, displaying the reviews summary, and
 * rendering the review submission form.
 */


/**
 * Property Reviews Function
 *
 * This function generates and displays the reviews section for a property.
 * It can be used to create either a tab or a standalone section on a property page.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Optional. Whether to display as a tab ('yes') or not.
 * @param string $tab_active_class Optional. The CSS class for active tabs.
 *
 * @return string|void Returns tab content if $is_tab is 'yes', otherwise echoes the content.
 */

if (!function_exists('wpestate_property_reviews_v2')):
    function wpestate_property_reviews_v2($postID, $is_tab = '', $tab_active_class = '') {
        global $post;

        // Retrieve label data for reviews section
        $data = wpestate_return_all_labels_data('reviews');
        $label = wpestate_property_page_prepare_label($data['label_theme_option'], $data['label_default']);

        // Generate content for reviews
        ob_start();
        // include(locate_template('/templates/listing_templates/property-page-templates/property_reviews.php'));
        get_template_part('templates/reviews/reviews');
        $content = ob_get_clean();

        // Display content based on whether it's a tab or not
        if ($is_tab === 'yes') {
            return wpestate_property_page_create_tab_item($content, $label, $data['tab_id'], $tab_active_class);
        } else {
            echo (trim($content));
        }
    }
endif;





/**
 * Generates the HTML for a single review
 *
 * @param WP_Comment $comment     The comment object
 * @param int        $property_id The ID of the property being reviewed
 * @return string    The HTML for the review
 */
function wpestate_generate_review_template($review, $property_id) {

    $author = get_post_meta( $review->ID, 'review_author', true );
    if (empty($author)) {
        $author = $review->post_author; // Fallback to post author if no specific author meta
    }
    $reviewer_name = wpestate_get_reviewer_name($author);
    $preview_img = wpestate_get_reviewer_image($author);
    $review_title = $review->post_title;
    $rating = get_post_meta($review->ID, 'reviewer_rating', true);

    ob_start();
    ?>
    <div class="listing-review">
        <div class="review-list-content norightpadding">
            <div class="reviewer_image" style="background-image: url(<?php echo esc_url($preview_img); ?>);"></div>
            <div class="reviwer-name"><?php echo esc_html__('Posted by ', 'wpresidence') . esc_html($reviewer_name); ?></div>
            <div class="review-title"><?php echo esc_html($review_title); ?></div>
            <div class="property_ratings">
                <?php echo wpestate_display_rating_stars($rating); ?>
                <span class="ratings-star">(<?php echo esc_html($rating . ' ' . __('of', 'wpresidence') . ' 5'); ?>)</span>
            </div>
            <div class="review-date">
                <?php echo esc_html__('Posted on ', 'wpresidence') . get_the_date('j F Y', $review->ID); ?>
            </div>
            <div class="review-content">
                <?php echo wp_kses_post($review->post_content); ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}




/**
 * Displays the reviews summary
 *
 * @param int    $comments_count   The number of reviews
 * @param int    $average_rating   The average rating
 * @param string $review_templates The HTML for all reviews
 */
function wpestate_display_reviews_summary($comments_count, $average_rating, $review_templates) {
    ?>
    <div class="property_page_container for_reviews">
        <div class="listing_reviews_wrapper">
            <div class="listing_reviews_container">
                <h3 id="listing_reviews" class="panel-title">
                    <?php
                    echo esc_html($comments_count . ' ' . __('Reviews', 'wpresidence'));
                    ?>
                    <span class="property_ratings">
                        <?php echo wpestate_display_rating_stars($average_rating); ?>
                    </span>
                </h3>
                <?php echo wp_kses_post($review_templates); ?>
            </div>
        </div>
    </div>
    <?php
}

function wpestate_display_reviews_summary_paginated( $post_id = null, $per_page = 5, $page = 1 )   {

    if (is_null($post_id) || empty($post_id)) {
        global $post;
        $post_id = $post->ID;
    }

    // Get total count and average rating without loading all reviews
    global $wpdb;
    $stats = $wpdb->get_row(
        $wpdb->prepare(
            "
            SELECT COUNT(p.ID) AS review_count,
                   AVG( CAST( pm_rating.meta_value AS DECIMAL(10,2) ) ) AS avg_rating
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm_attached
                ON p.ID = pm_attached.post_id AND pm_attached.meta_key = 'attached_to'
            INNER JOIN {$wpdb->postmeta} pm_rating
                ON p.ID = pm_rating.post_id AND pm_rating.meta_key = 'reviewer_rating'
            WHERE p.post_type = 'estate_review'
              AND p.post_status = 'publish'
              AND pm_attached.meta_value = %d
            ",
            $post_id
        )
    );

    $total_count   = $stats ? intval( $stats->review_count ) : 0;
    $average_rating = $total_count ? round( (float) $stats->avg_rating, 1 ) : 0;
    

    // Get paginated reviews
    $offset = ($page - 1) * $per_page;
    $reviews = get_posts(array(
        'post_type' => 'estate_review',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'attached_to',
                'value' => $post_id,
                'compare' => '='
            )
        ),
        'posts_per_page' => $per_page,
        'offset' => $offset,
        'orderby' => 'date',
        'order' => 'DESC'
    ));

    ob_start();
    

    if ( !empty( $reviews ) )   {
        $review_templates = '';
        foreach ($reviews as $review) {
            $review_templates .= wpestate_generate_review_template($review, $post_id);
        }
    ?>
    <div class="estate-reviews" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-per-page="<?php echo esc_attr( $per_page ); ?>">
    <div class="property_page_container for_reviews">
        <div class="listing_reviews_wrapper">
            <div class="listing_reviews_container">
                <h3 id="listing_reviews" class="panel-title">
                    <?php
                    echo esc_html($total_count . ' ' . __('Reviews', 'wpresidence'));
                    ?>
                    <span class="property_ratings">
                        <?php echo wpestate_display_rating_stars($average_rating); ?>
                    </span>
                </h3>
                <?php echo wp_kses_post($review_templates); ?>
                <?php
                // Pagination
                $total_pages = ceil($total_count / $per_page);
                if ($total_pages > 1) {
                ?>
                <div class="half-pagination">
                <ul class="pagination" style="margin-top: 20px; text-align: center;">
                    <!-- <div class="pagination-info" style="margin-bottom: 10px; color: #666;">
                        <?php echo 'Showing ' . (($page - 1) * $per_page + 1) . '-' . min($page * $per_page, $total_count) . ' of ' . $total_count . ' reviews'; ?>
                    </div> -->
                    <?php
                    // Previous button
                    if ($page > 1) {
                        echo '<li class="roundleft"><a href="#" class="pagination-btn prev-btn" data-page="' . esc_attr( $page - 1 ) . '"><i class="fas fa-angle-left"></i></a></li>';
                    }
                    // Page numbers
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    if ($start_page > 1) {
                        echo '<li><a href="#" class="pagination-btn page-btn" data-page="1">1</a></li>';
                        if ($start_page > 2) {
                            echo '<li><span>...</span></li>';
                        }
                    }
        
                    for ($i = $start_page; $i <= $end_page; $i++) {
                        $active_class = ($i == $page) ? 'active' : '';
                        echo '<li class="' . esc_attr( $active_class ) . '"><a href="#" class="pagination-btn page-btn" data-page="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</a></li>';
                    }
        
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo '<li><span>...</span></li>';
                        }
                        echo '<li><a href="#" class="pagination-btn page-btn" data-page="' . esc_attr( $total_pages ) . '">' . esc_html( $total_pages ) . '</a></li>';
                    }
        
                    // Next button
                    if ($page < $total_pages) {
                        echo '<li><a href="#" class="pagination-btn next-btn" data-page="' . esc_attr( $page + 1 ) . '"><i class="fas fa-angle-right"></i></a></li>';
                    }
                    ?>
                </ul>
                </div>
                <?php } // End pagination ?>
            </div>
        </div>
    </div>
    </div>
    <?php 
    } else {
        echo '<h5><div class="review_tag">' . esc_html__('No reviews found.', 'wpresidence') . '</div></h5>';
    }
    $return = ob_get_clean();
    return $return;
}




/**
 * Displays the review form for logged-in users
 *
 * @param int $property_id The ID of the property being reviewed
 */
function wpestate_display_review_form($property_id) {
    $current_user = wp_get_current_user();
    $user_review = wpestate_get_user_review($current_user->ID, $property_id);

    $review_title = $user_review ? $user_review->post_title : '';
    $review_stars = $user_review ? get_post_meta($user_review->ID, 'reviewer_rating', true) : '';
    $comment_content = $user_review ? $user_review->post_content : '';
    ?>
    <?php if ($user_review && $user_review->post_status == 'pending') { ?>
        <div class="review_notice">
            <?php esc_html_e('Your review is pending approval.', 'wpresidence'); ?>
        </div>
    <?php } else { ?>
    <h5>
        <?php
        if ($user_review) {
            echo '<div class="review_tag">' . esc_html__('Update Review ', 'wpresidence');
            if ($user_review->post_status == 'pending') {
                echo ' - ' . esc_html__('pending approval', 'wpresidence');
            }
            echo '</div>';
        } else {
            echo '<div class="review_tag">' . esc_html__('Write a Review ', 'wpresidence') . '</div>';
        }
        ?>
    </h5>
    
    <div class="add_review_wrapper">
        <div class="rating">
            <span class="rating_legend"><?php esc_html_e('Your Rating & Review', 'wpresidence'); ?></span>
            <?php echo wpestate_display_rating_stars($review_stars, true); ?>
        </div>
        <input type="text" id="wpestate_review_title" name="wpestate_review_title" value="<?php echo esc_attr($review_title); ?>" class="form-control" placeholder="<?php esc_attr_e('Review Title', 'wpresidence'); ?>">
        <textarea rows="8" id="wpestare_review_content" name="wpestare_review_content" class="form-control" placeholder="<?php esc_attr_e('Your Review', 'wpresidence'); ?>"><?php echo esc_textarea($comment_content); ?></textarea>
        <?php
        if ($user_review) {
            echo '<input type="submit" class="wpresidence_button col-md-3" id="edit_review" data-coment_id="' . esc_attr($user_review->ID) . '" data-listing_id="' . intval($property_id) . '" value="' . esc_attr__('Edit Review', 'wpresidence') . '">';
        } else {
            echo '<input type="submit" class="wpresidence_button col-md-3" id="submit_review" data-listing_id="' . intval($property_id) . '" value="' . esc_attr__('Submit Review', 'wpresidence') . '">';
        }

        $ajax_nonce = wp_create_nonce("wpestate_review_nonce");
        echo '<input type="hidden" id="wpestate_review_nonce" value="' . esc_attr($ajax_nonce) . '" />';
        ?>
    </div>
    <?php
    }
}

/**
 * Helper function to get the reviewer's name
 *
 * @param int $user_id The user ID of the reviewer
 * @return string The reviewer's name
 */
function wpestate_get_reviewer_name($user_id) {
    if ($user_id == 1) {
        return "admin";
    }

    $userid_agent = get_user_meta($user_id, 'user_agent_id', true);
    if ($userid_agent) {
        return get_the_title($userid_agent);
    }

    return get_the_author_meta('display_name', $user_id);
}

/**
 * Helper function to get the reviewer's image
 *
 * @param int $user_id The user ID of the reviewer
 * @return string URL of the reviewer's image
 */
function wpestate_get_reviewer_image($user_id) {
    $userid_agent = get_user_meta($user_id, 'user_agent_id', true);
    
    if ($userid_agent) {
        $thumb_id = get_post_thumbnail_id($userid_agent);
        $preview = wp_get_attachment_image_src($thumb_id, 'thumbnail');
    } else {
        $user_small_picture_id = get_the_author_meta('small_custom_picture', $user_id, true);
        $preview = wp_get_attachment_image_src($user_small_picture_id, 'agent_picture_thumb');
    }

    return isset($preview[0]) ? $preview[0] : get_theme_file_uri('/img/default_user_agent.png');
}

/**
 * Helper function to display rating stars
 *
 * @param int  $rating    The rating value
 * @param bool $is_input  Whether this is for input (default false)
 * @return string HTML for the rating stars
 */
function wpestate_display_rating_stars($rating, $is_input = false) {
    $output = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($is_input) {
            $class = $i <= $rating ? 'starselected starselected_click' : '';
            $output .= '<span class="empty_star ' . $class . '"></span>';
        } else {
            $icon = $i <= $rating ? 'fas fa-star' : 'far fa-star';
            $output .= '<i class="' . $icon . '"></i>';
        }
    }
    return $output;
}

/**
 * Helper function to get the user's review for a property
 *
 * @param int $user_id     The user ID
 * @param int $property_id The property ID
 * @return WP_Comment|null The user's review or null if not found
 */
function wpestate_get_user_review($user_id, $property_id) {
    $args = array(
            'post_type' => 'estate_review',
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => 'review_author',
                    'value' => $user_id,
                    'compare' => '='
                ),
                array(
                    'key' => 'attached_to',
                    'value' => $property_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        );
    $comments = get_posts($args);
    return !empty($comments) ? $comments[0] : null;
}