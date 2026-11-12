<?php
/**
 * WPResidence Review Deletion API Functions
 *
 * Functions for permanently removing reviews via the REST API
 * with proper validation and response handling.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Delete a review.
 * 
 * Permanently removes a review comment and all its associated meta data.
 * Validates the review existence before deletion and provides
 * appropriate error responses for failure cases.
 *
 * @param WP_REST_Request $request The REST API request containing the review ID.
 * @return WP_REST_Response|WP_Error Success response or error details.
 */
function wpresidence_delete_review(WP_REST_Request $request) {
    // Parse and extract request parameters
    $input_data = wpresidence_parse_request_params($request);
    $comment_id = $input_data['id'];
    
    // Validate the review exists
    $comment = get_post($comment_id);
    if (!$comment) {
        return new WP_Error(
            'rest_review_not_found',
            __('Review not found.'),
            ['status' => 404]
        );
    }
    
    // Get entity information (what was being reviewed)
    $entity_id = get_post_meta( $comment->ID , 'attached_to', true );
    $entity_title = get_the_title($entity_id);
    
    // Attempt to delete the review (force = true permanently deletes it)
    $result = wp_delete_post($comment_id, true);
    if (!$result) {
        return new WP_Error(
            'rest_cannot_delete',
            __('Failed to delete the review.'),
            ['status' => 500]
        );
    }
    
    // Return success response with confirmation details
    return rest_ensure_response([
        'status'    => 'success',
        'review_id' => $comment_id,
        'entity_id' => $entity_id,
        'entity_title' => $entity_title,
        'message'   => __('Review deleted successfully.'),
    ]);
}