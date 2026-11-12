<?php
/**
 * WPResidence API Functions for Review Management
 *
 * Core functions for handling review filtering, data processing, 
 * and response formatting for the WPResidence REST API.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

 
/**
 * Check if the current user has permissions to perform an action on a review.
 * 
 * Verifies user permissions for review management actions.
 *
 * @param WP_REST_Request $request REST API request object.
 * @return bool|WP_Error True if the user has permission, otherwise a WP_Error.
 */
function wpresidence_check_permissions_for_review(WP_REST_Request $request) {
    // Verify the JWT token
    $user_id = apply_filters('determine_current_user', null);
    if (!$user_id) {
        return new WP_Error(
            'jwt_auth_failed',
            __('Invalid or missing JWT token.'),
            ['status' => 403]
        );
    }
    wp_set_current_user($user_id);

    // Fetch the current user details
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    // Check if the user is logged in
    if (!$user_id || !is_user_logged_in()) {
        return new WP_Error(
            'rest_forbidden',
            __('You must be logged in to update a review.'),
            ['status' => 403]
        );
    }

    // Get the review ID from the request
    $comment_id = $request->get_param('id');
    if (!$comment_id || !is_numeric($comment_id)) {
        return new WP_Error(
            'rest_invalid_review',
            __('Invalid review ID.'),
            ['status' => 400]
        );
    }

    // Validate the review exists
    $comment = get_post($comment_id);
    if (!$comment) {
        return new WP_Error(
            'rest_review_not_found',
            __('Review not found.'),
            ['status' => 404]
        );
    }

    // Check if the current user is the author of the review or has admin rights

    if (intval(get_post_meta( $comment->ID , 'review_author', true )) !== intval($user_id) && !current_user_can('edit_estate_reviews')) {
        return new WP_Error(
            'rest_forbidden',
            __('You do not have permission to update this review.'),
            ['status' => 403]
        );
    }

    return true;
}

/**
 * Retrieve all reviews with specified filters and pagination.
 * 
 * Main function for review listing API endpoint.
 * Processes complex filtering options and returns data in requested format.
 *
 * @param WP_REST_Request $request REST API request containing filter parameters.
 * @return WP_REST_Response Response containing filtered review data.
 */
function wpresidence_get_all_reviews(WP_REST_Request $request) {
    // Parse parameters
    $params = wpresidence_parse_request_params($request);

    // Set defaults and extract main parameters
    $paged = isset($params['page']) ? intval($params['page']) : 1;
    $posts_per_page = isset($params['posts_per_page']) ? intval($params['posts_per_page']) : 10;
    
    // Parse fields parameter
    $fields = wpresidence_parse_api_fields_param($params['fields'] ?? null);

    // Build the query args
    $offset = ($paged - 1) * $posts_per_page;
    $args = array(
        'post_type'      => 'estate_review',
        'post_status'    => 'publish',
        'posts_per_page' => $posts_per_page,
        'offset'         => $offset,
        'orderby'        => 'date',
        'order'          => 'DESC'
    );

    // Collect meta-based filters

    // Prepare meta query filters

    $meta_query = array();

    // Filter by entity ID (property, agent, agency, or developer ID)
    if (isset($params['entity_id']) && is_numeric($params['entity_id'])) {
        $meta_query[] = array(
            'key'     => 'attached_to',
            'value'   => $params['entity_id'],
            'compare' => '='
        );
    }

    // Filter by star rating
    if (isset($params['stars']) && is_numeric($params['stars'])) {
        $meta_query[] = array(
            'key'     => 'reviewer_rating',
            'value'   => intval($params['stars']),
            'compare' => '=',
            'type'    => 'NUMERIC'
        );
    }

    // Filter by minimum star rating
    if (isset($params['min_stars']) && is_numeric($params['min_stars'])) {
        $meta_query[] = array(
            'key'     => 'reviewer_rating',
            'value'   => intval($params['min_stars']),
            'compare' => '>=',
            'type'    => 'NUMERIC'
        );
    }

    // Filter by user ID
    if (isset($params['user_id']) && is_numeric($params['user_id'])) {
        $meta_query[] = array(
            'key'     => 'review_author',
            'value'   => intval($params['user_id']),
            'compare' => '=',
            'type'    => 'NUMERIC'
        );
    }

    if (count($meta_query) > 1) {

        // Ensure all filters apply when multiple meta queries are present

        $meta_query['relation'] = 'AND';
    }

    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }


    // Execute the main query to fetch review posts
    $comments       = new WP_Query($args);
    // Use a secondary instance for count queries
    $comments_query = new WP_Query;

    // Get the comments
    $comments_query = new WP_Query;
    $comments       = new WP_Query($args);


    // Get total comments for pagination
    $args_for_count = $args;
    // unset($args_for_count['number']);
    unset($args_for_count['offset']);
    $args_for_count['posts_per_page'] = -1; // Get all comments for count
    $total_comments = count($comments_query->query($args_for_count));
    $total_pages = ceil($total_comments / $posts_per_page);

    // Process results
    $reviews = array();
    foreach ($comments->posts as $comment) {

        // Convert each review post into the standardized API structure

        $review_data = wpresidence_format_review_data($comment);
        $reviews[]   = $review_data;
    }

    // If specific fields are requested, filter the results
    if ($fields) {
        $reviews = wpresidence_filter_api_collection($reviews, $fields);
    }

    // Return formatted response
    return new WP_REST_Response(
        [
            'status' => 'success',
            'data' => $reviews,
            'total' => $total_comments,
            'pages' => $total_pages
        ],
        200
    );
}


/**
 * Retrieve a single review by its ID.
 * 
 * Fetches complete review data for a specific review comment.
 * Supports field filtering to return only requested data.
 *
 * @param WP_REST_Request $request REST API request containing the review ID.
 * @return WP_REST_Response|WP_Error Response with review data or error if not found.
 */
function wpresidence_get_single_review(WP_REST_Request $request) {
    $params = wpresidence_parse_request_params($request);

    // Extract and validate essential parameters
    $comment_id = $params['id'] ?? null;

    // Parse fields parameter
    $fields = wpresidence_parse_api_fields_param($request->get_param('fields'));

    // Verify review exists
    $comment = get_post($comment_id);
    if (!$comment) {
        return new WP_Error('rest_review_not_found', __('Review not found'), ['status' => 404]);
    }

    // Format the review data
    $review_data = wpresidence_format_review_data($comment);
    
    // Filter response based on requested fields
    if ($fields) {
        $response = wpresidence_filter_api_response_fields($review_data, $fields);
    } else {
        $response = $review_data;
    }

    return rest_ensure_response($response);
}

/**
 * Format a comment object into a standardized review data structure.
 * 
 * Extracts and structures all relevant data from a WP_Comment object
 * for use in API responses.
 *
 * @param WP_Comment $comment The comment object to format.
 * @return array Formatted review data.
 */
function wpresidence_format_review_data($comment) {
    // Get the entity details
    $entity_id = get_post_meta( $comment->ID , 'attached_to', true );
    $entity_title = get_the_title($entity_id);
    $entity_type = get_post_type($entity_id);
    
    // Get review meta
    $stars = get_post_meta($comment->ID, 'reviewer_rating', true);
    $title = get_the_title($comment->ID);
    

    // Retrieve the review author's user information


    $user_data = get_userdata(get_post_meta( $comment->ID , 'review_author', true ));
    $user_name = $user_data ? $user_data->display_name : $comment->post_author;
    
    // Format the date
    $date = mysql2date(get_option('date_format'), $comment->post_date);
    
    // Build the review data structure
    $review_data = array(
        'id' => $comment->ID,
        'entity_id' => $entity_id,
        'entity_title' => $entity_title,
        'entity_type' => $entity_type,
        'user_id' => get_post_meta( $comment->ID , 'review_author', true ),
        'user_name' => $user_name,
        'date' => $date,
        'timestamp' => strtotime($comment->post_date),
        'stars' => (int)$stars,
        'title' => $title,
        'content' => $comment->post_content,
        'approved' => ($comment->post_status == 'publish'),
        'parent_id' => $comment->post_parent,
    );
    
    return $review_data;
}