<?php

/**
 * WordPress AJAX handler for submitting property reviews
 * 
 * This function processes review submissions on property listings.
 * It validates the user is logged in, processes the review data,
 * and inserts the review as a comment with additional metadata for stars and title.
 * 
 * @package WP Estate
 * @subpackage Reviews
 * @return void
 */
add_action('wp_ajax_wpestate_post_review', 'wpestate_post_review');

if (!function_exists('old_wpestate_post_review')):
    function old_wpestate_post_review() {
        // Verify nonce for security
        check_ajax_referer('wpestate_review_nonce', 'security');
        
        // Get current user information
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $allowed_html = array();
        
        // Validate user is logged in
        if (!is_user_logged_in()) {
            exit('ko');
        }
        
        // Additional validation that user ID is not 0
        if ($userID === 0) {
            exit('out pls');
        }
        
        // Get user details for the comment
        $userID = $current_user->ID;
        $user_login = $current_user->user_login;
        $user_email = $current_user->user_email;
        
        // Get review data from POST request
        $listing_id = intval($_POST['listing_id']);
        $stars = intval($_POST['stars']);
        $content = wp_kses($_POST['content'], $allowed_html);
        $title = wp_kses($_POST['title'], $allowed_html);
        
        // Set up timestamp for the comment
        $time = time();
        $time = current_time('mysql');
        
        // Determine if review requires approval
        $comment_approved = 0; // Default: requires approval
        if (wpresidence_get_option('wp_estate_admin_approves_reviews', '') == 'no') {
            $comment_approved = 1; // Auto-approve if setting is 'no'
        }
        
        // Prepare comment data array
        $data = array(
            'comment_post_ID' => $listing_id,
            'comment_author' => $user_login,
            'comment_author_email' => $user_email,
            'comment_author_url' => '',
            'comment_content' => $content,
            'comment_type' => 'comment',
            'comment_parent' => 0,
            'user_id' => $userID,
            'comment_author_IP' => '127.0.0.1',
            'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
            'comment_date' => $time,
            'comment_approved' => $comment_approved,
        );
        
        // Insert the comment into the database
        $comment_id = wp_insert_comment($data);
        
        // Add review-specific metadata to the comment
        add_comment_meta($comment_id, 'review_title', $title);
        add_comment_meta($comment_id, 'review_stars', $stars);
        
        // Prepare notification email arguments
        $arguments = array(
            'agent_name' => get_the_title($listing_id),
            'user_post' => $user_login
        );
        
        // Send email notification about the new review
        wpestate_select_email_type(get_option('admin_email'), 'agent_review', $arguments);
        
        // End AJAX request
        die();
    }
endif;

if (!function_exists('wpestate_post_review')):
    function wpestate_post_review() {
        // Verify nonce for security
        check_ajax_referer('wpestate_review_nonce', 'security');
        
        // Get current user information
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $allowed_html = array();
        
        // Validate user is logged in
        if (!is_user_logged_in()) {
            exit('ko');
        }
        
        // Additional validation that user ID is not 0
        if ($userID === 0) {
            exit('out pls');
        }
        
        // Get user details for the comment
        // $userID = $current_user->ID;
        $user_login = $current_user->user_login;
        $user_email = $current_user->user_email;
        
        // Sanitize and validate input
        $review_title = sanitize_text_field($_POST['title']);
        $reviewer_rating = intval($_POST['stars']);
        $review_content = sanitize_textarea_field($_POST['content']);
        $attached_to = intval($_POST['listing_id']);
        
        // Validation
        $errors = array();
        
        if (empty($review_title)) {
            $errors[] = __('Review title is required');
        }
        
        if (empty($review_content)) {
            $errors[] = __('Review content is required');
        }
        
        if ($reviewer_rating < 1 || $reviewer_rating > 5) {
            $errors[] = __('Please select a rating');
        }
        
        if (!empty($errors)) {
            wp_send_json_error(implode(', ', $errors));
        }
        
        // Check for duplicate reviews (optional)
        $existing_review = get_posts(array(
            'post_type' => 'estate_review',
            'meta_query' => array(
                array(
                    'key' => 'review_author',
                    'value' => $userID,
                    'compare' => '='
                ),
                array(
                    'key' => 'attached_to',
                    'value' => $attached_to,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (!empty($existing_review)) {
            wp_send_json_error(__('You have already submitted a review for this property'));
        }

        $status = 'pending'; // Default status for new reviews
        if (wpresidence_get_option('wp_estate_admin_approves_reviews', '') == 'no') {
            $status = 'publish'; // Auto-approve if setting is 'no'
        }
        
        // Create the estate review post
        $post_data = array(
            'post_title'   => $review_title,
            'post_content' => $review_content,
            'post_status'  => $status,
            'post_type'    => 'estate_review',
            'post_author'  => get_current_user_id(),
            'meta_input'   => array(
                'review_author'     => $userID,
                'reviewer_rating'   => $reviewer_rating,
                'attached_to'       => $attached_to,
            )
        );
        
        $post_id = wp_insert_post($post_data);
        
        // Prepare notification email arguments
        $arguments = array(
            'agent_name' => get_the_title($attached_to),
            'user_post' => $user_login
        );
        
        // Send email notification about the new review
        wpestate_select_email_type(get_option('admin_email'), 'agent_review', $arguments);

        wp_send_json(array( 'status' => $status, 'message' => __('An administrator will approve the review.', 'wpresidence-core') ));
        
        // End AJAX request
        die();
    }
endif;



/**
 * WordPress AJAX handler for editing property reviews
 * 
 * This function processes edit requests for existing property reviews.
 * It validates the user is logged in and is the original author of the review,
 * updates the review content and metadata, and notifies admins of the edit.
 * 
 * @package WP Estate
 * @subpackage Reviews
 * @return void
 */
add_action('wp_ajax_wpestate_edit_review', 'wpestate_edit_review');

if (!function_exists('wpestate_edit_review')):
    function wpestate_edit_review() {
        // Verify nonce for security
        check_ajax_referer('wpestate_review_nonce', 'security');
        
        // Get current user information
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        $user_login = $current_user->user_login;
        $allowed_html = array();
        
        // Validate user is logged in
        if (!is_user_logged_in()) {
            exit('ko');
        }
        
        // Additional validation that user ID is not 0
        if ($userID === 0) {
            exit('out pls');
        }
        
        // Retrieve the review ID from the 'comment' field (correcting previous typo)

        $comment_ID = intval($_POST['comment']);
        $comment = get_post($comment_ID);
        
        // Debug output - prints current user ID and comment author ID
        // print intval($userID) . '/ ' . intval(get_post_meta($comment_ID, 'review_author', true));
        
        // Validate the current user is the author of the comment
        if (get_post_meta($comment_ID, 'review_author', true) != $userID) {
            exit('no');
        }
        
        // Get updated review data from POST request
        $listing_id = intval($_POST['listing_id']);
        $stars = intval($_POST['stars']);
        $content = wp_kses($_POST['content'], $allowed_html);
        $title = wp_kses($_POST['title'], $allowed_html);
        
        // Update review metadata
        
        update_post_meta($comment_ID, 'reviewer_rating', $stars);
        // update_comment_meta($comment_ID, 'comment_content', $content);
        
        // Prepare comment data array for update
        $commentarr = array();
        $commentarr['ID'] = $comment_ID;
        $commentarr['post_title'] = $title;
        $commentarr['post_content'] = $content;
        
        // Determine if edited review requires approval
        $status = 'pending'; // Default status for new reviews
        if (wpresidence_get_option('wp_estate_admin_approves_reviews', '') == 'no') {
            $status = 'publish'; // Auto-approve if setting is 'no'
        }
        $commentarr['post_status'] = $status;
        
        // Update the comment in the database
        wp_update_post($commentarr);
        
        // Prepare notification email arguments
        $arguments = array(
            'agent_name' => get_the_title($listing_id),
            'user_post' => $user_login
        );
        
        // Send email notification about the edited review
        wpestate_select_email_type(get_option('admin_email'), 'agent_review', $arguments);

        wp_send_json(array( 'status' => $status, 'message' => __('An administrator will approve the review.', 'wpresidence-core') ));
        
        // End AJAX request
        die();
    }
endif;