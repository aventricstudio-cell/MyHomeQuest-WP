<?php
/**
 * WPResidence Review Update API Functions
 *
 * Functions for updating existing reviews via the REST API,
 * including permission verification and data processing.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Update an existing review.
 * 
 * Processes and applies updates to a review's data, including:
 * - Review content
 * - Rating stars
 * - Review title
 *
 * @param WP_REST_Request $request The REST API request containing the review data.
 * @return WP_REST_Response|WP_Error Response object or error.
 */
function wpresidence_update_review(WP_REST_Request $request) {
    // Get review ID from request
    $comment_id = $request->get_param('id');
    
    // Parse and sanitize input data
    $input_data = wpresidence_parse_request_params($request);

    // Validate the review ID
    $comment = get_post($comment_id);
    if (!$comment) {
        return new WP_Error(
            'rest_review_not_found',
            __('Review not found.'),
            ['status' => 404]
        );
    }

    // Get current user
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;
    
    // Prepare comment data for update
    $comment_data = array(
        'ID' => $comment_id
    );
    
    // Update content if provided
    if (isset($input_data['content'])) {
        $allowed_html = array();
        $comment_data['post_content'] = wp_kses($input_data['content'], $allowed_html);
    }
    
    // Update review title if provided
    if (isset($input_data['title'])) {
        $allowed_html = array();
        $comment_data['post_title'] = wp_kses($input_data['title'], $allowed_html);
    }

    // Update the comment in the database
    $result = wp_update_post($comment_data);
    
    if (false === $result) {
        return new WP_Error(
            'rest_update_failed',
            __('Failed to update review.'),
            ['status' => 500]
        );
    }
    
    // Update stars rating if provided
    if (isset($input_data['stars']) && is_numeric($input_data['stars'])) {
        $stars = intval($input_data['stars']);
        
        // Validate stars range
        if ($stars < 1 || $stars > 5) {
            return new WP_Error(
                'rest_invalid_param',
                __('Star rating must be between 1 and 5.'),
                ['status' => 400]
            );
        }
        
        update_post_meta($comment_id, 'reviewer_rating', $stars);
    }
    
    // Get the updated review data
    $updated_comment = get_post($comment_id);
    $review_data = wpresidence_format_review_data($updated_comment);
    
    // Return success response
    return rest_ensure_response([
        'status'     => 'success',
        'review_id'  => $comment_id,
        'message'    => __('Review updated successfully.'),
        'data'       => $review_data
    ]);
}