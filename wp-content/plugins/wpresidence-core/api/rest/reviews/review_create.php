<?php 
/**
 * Post a review for a property, agent, agency, or developer.
 * 
 * Submits a review for a given entity and handles validation and response.
 *
 * @param WP_REST_Request $request The REST API request.
 * @return WP_REST_Response|WP_Error Success response or error details.
 */
function wpresidence_post_review(WP_REST_Request $request) {
    // Get current user
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;
    
    // Check if user is logged in
    if ($userID === 0) {
        return new WP_Error(
            'rest_unauthorized',
            __('You must be logged in to post a review.'),
            ['status' => 401]
        );
    }
    
    // Get user info
    $user_login = $current_user->user_login;
    $user_email = $current_user->user_email;
    
    // Parse request parameters
    $params = $request->get_params();
    
    // Validation
    if (empty($params['listing_id']) || !is_numeric($params['listing_id'])) {
        return new WP_Error(
            'rest_invalid_param',
            __('Invalid listing ID.'),
            ['status' => 400]
        );
    }
    
    $listing_id = intval($params['listing_id']);
    
    // Verify post type
    $post_type = get_post_type($listing_id);
    $allowed_post_types = ['estate_property', 'estate_agent', 'estate_agency', 'estate_developer'];
    
    if (!in_array($post_type, $allowed_post_types)) {
        return new WP_Error(
            'rest_invalid_post_type',
            __('Reviews can only be posted for properties, agents, agencies, or developers.'),
            ['status' => 400]
        );
    }
    
    // Validate stars rating
    if (empty($params['stars']) || !is_numeric($params['stars']) || $params['stars'] < 1 || $params['stars'] > 5) {
        return new WP_Error(
            'rest_invalid_param',
            __('Star rating must be between 1 and 5.'),
            ['status' => 400]
        );
    }
    
    $stars = intval($params['stars']);
    
    // Validate content
    if (empty($params['content'])) {
        return new WP_Error(
            'rest_invalid_param',
            __('Review content is required.'),
            ['status' => 400]
        );
    }
    
    $allowed_html = array();
    $content = wp_kses($params['content'], $allowed_html);
    $title = !empty($params['title']) ? wp_kses($params['title'], $allowed_html) : '';

    // Prevent multiple reviews from the same user for the same listing
    $existing_review = get_posts(array(
        'post_type'      => 'estate_review',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'review_author',
                'value'   => $userID,
                'compare' => '='
            ),
            array(
                'key'     => 'attached_to',
                'value'   => $listing_id,
                'compare' => '='
            )
        )
    ));

    if (!empty($existing_review)) {
        return new WP_Error(
            'rest_duplicate_review',
            __('You have already submitted a review for this listing.'),
            ['status' => 400]
        );
    }

    // Reviews require manual approval by default
    $status = 'pending';
    // Allow auto-approval when the related option is disabled
    if (wpresidence_get_option('wp_estate_admin_approves_reviews', '') == 'no') {
        $status = 'publish';
    }
    
    // Create the estate review post
    $post_data = array(
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => $status,
        'post_type'    => 'estate_review',
        'post_author'  => get_current_user_id(),
        'meta_input'   => array(
            'review_author'     => $userID,
            'reviewer_rating'   => $stars,
            'attached_to'       => $listing_id,
        )
    );
    
    $post_id = wp_insert_post($post_data);
    
    // Check if comment was inserted successfully
    if (!$post_id || is_wp_error($post_id)) {
        return new WP_Error(
            'rest_comment_failed',
            __('Failed to post review.'),
            ['status' => 500]
        );
    }
    
    // Legacy hook: reviews were previously stored as comments.
    // These lines are left for backward compatibility/reference.
    // add_comment_meta($comment_id, 'review_title', $title);
    // add_comment_meta($comment_id, 'review_stars', $stars);
    
    // Send email notification
    $entity_name = get_the_title($listing_id);
    $arguments = array(
        'agent_name' => $entity_name,
        'user_post' => $user_login
    );
    
    wpestate_select_email_type(get_option('admin_email'), 'agent_review', $arguments);
    
    // Return success response
    $response_data = array(
        'status' => 'success',
        'comment_id' => $post_id,
        'message' => __('Review posted successfully.'),
        'approved' => ($status == 'publish'),
    );
    
    return rest_ensure_response($response_data);
}