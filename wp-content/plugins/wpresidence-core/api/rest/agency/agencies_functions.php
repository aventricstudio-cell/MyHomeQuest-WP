<?php
/**
 * WPResidence API Functions for Agency Management
 *
 * Core functions for handling agency filtering, data processing, 
 * and response formatting for the WPResidence REST API.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

 
/**
 * Check if the current user has permissions to perform an action on an agency.
 * 
 * Verifies user role-based permissions for agency management actions.
 *
 * @param WP_REST_Request $request REST API request object.
 * @return bool|WP_Error True if the user has permission, otherwise a WP_Error.
 */
function wpresidence_check_permissions_for_agency(WP_REST_Request $request) {
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
            __('You must be logged in to update an agency.'),
            ['status' => 403]
        );
    }

    // Get the agency ID from the request
    $agency_id = $request->get_param('id');
    if (!$agency_id || !is_numeric($agency_id)) {
        return new WP_Error(
            'rest_invalid_agency',
            __('Invalid agency ID.'),
            ['status' => 400]
        );
    }

    // Validate the agency exists and is the correct post type
    $agency = get_post($agency_id);
    if (!$agency || $agency->post_type !== 'estate_agency') {
        return new WP_Error(
            'rest_agency_not_found',
            __('Agency not found.'),
            ['status' => 404]
        );
    }

    // Check if the current user is the author of the agency or has admin rights
    if (intval($agency->post_author) !== intval($user_id) && !current_user_can('edit_others_posts')) {
        return new WP_Error(
            'rest_forbidden',
            __('You do not have permission to update this agency.'),
            ['status' => 403]
        );
    }

    return true;
}



/**
 * Retrieve all agencies with specified filters and pagination.
 * 
 * Main function for agency listing API endpoint.
 * Processes complex filtering options and returns data in requested format.
 *
 * @param WP_REST_Request $request REST API request containing filter parameters.
 * @return WP_REST_Response Response containing filtered agency data.
 */
function wpresidence_get_all_agencies(WP_REST_Request $request) {
    // Parse parameters
    $params = wpresidence_parse_request_params($request);

    // Set defaults and extract main parameters
    $paged = isset($params['page']) ? intval($params['page']) : 1;
    $posts_per_page = isset($params['posts_per_page']) ? intval($params['posts_per_page']) : 10;
    
    // Parse fields parameter using the utility function
    $fields = wpresidence_parse_api_fields_param($params['fields'] ?? null);

    // Initialize query arrays
    $meta_input = [];
    $taxonomy_input = [];

    // Process taxonomy parameters
    if (isset($params['taxonomies']) && is_array($params['taxonomies'])) {
        $taxonomy_input = $params['taxonomies'];
    }

    // Process meta parameters
    if (isset($params['meta']) && is_array($params['meta'])) {
        foreach ($params['meta'] as $key => $meta_data) {
            // Skip if meta_data is not an array
            if (!is_array($meta_data)) {
                continue;
            }

            // Validate and set default values
            $meta_query = [
                'key' => $key,
                'compare' => '=',
                'type' => 'CHAR'
            ];

            // Add value if it exists
            if (isset($meta_data['value'])) {
                $meta_query['value'] = $meta_data['value'];
            }

            // Add compare if it's valid
            if (isset($meta_data['compare'])) {
                $valid_compare = ['=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS', 'NOT EXISTS'];
                if (in_array($meta_data['compare'], $valid_compare)) {
                    $meta_query['compare'] = $meta_data['compare'];
                }
            }

            // Add type if it's valid
            if (isset($meta_data['type'])) {
                $valid_types = ['NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'];
                if (in_array($meta_data['type'], $valid_types)) {
                    $meta_query['type'] = $meta_data['type'];
                }
            }

            // Handle special compare cases
            if (in_array($meta_query['compare'], ['IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'])) {
                // Ensure value is an array for these compare types
                if (!is_array($meta_query['value'])) {
                    $meta_query['value'] = array($meta_query['value']);
                }
            }

            // Handle EXISTS and NOT EXISTS
            if (in_array($meta_query['compare'], ['EXISTS', 'NOT EXISTS'])) {
                unset($meta_query['value']); // No value needed for EXISTS comparisons
            }

            $meta_input[] = $meta_query;
        }
    }

    $post_type = 'estate_agency';

    // Call the custom query function with API type
    $query_result = wpestate_api_custom_query(
        $post_type,
        $paged,
        $posts_per_page,
        $meta_input,
        $taxonomy_input,
        3, // Default order
        null, // No userID filter
        'api'
    );

    // Ensure we have valid results
    if (!$query_result || !isset($query_result['post_ids'])) {
        return new WP_REST_Response(
            [
                'status' => 'error',
                'message' => 'No query results found',
            ],
            404
        );
    }

    // Process results
    $agencies = [];
    foreach ($query_result['post_ids'] as $postID) {
        // Get full agency data
        $cached_data = wpestate_api_get_cached_post_data($postID, $post_type);
        $agencies[] = $cached_data;
    }

    // If specific fields are requested, filter the results using the utility function
    if ($fields) {
        $agencies = wpresidence_filter_api_collection($agencies, $fields);
    }

    // Return formatted response
    return new WP_REST_Response(
        [
            'status' => 'success',
            'query_args' => $query_result['args'],
            'data' => $agencies,
            'total' => $query_result['total_posts'],
            'pages' => $query_result['max_num_pages']
        ],
        200
    );
}

/**
 * Retrieve a single agency by its ID.
 * 
 * Fetches complete agency data for a specific agency.
 * Supports field filtering to return only requested data.
 *
 * @param WP_REST_Request $request REST API request containing the agency ID.
 * @return WP_REST_Response|WP_Error Response with agency data or error if not found.
 */
function wpresidence_get_single_agency(WP_REST_Request $request) {
    $params = wpresidence_parse_request_params($request);

    // Extract and validate essential parameters
    $id = $params['id'] ?? null;

    // Parse fields parameter using the utility function
    $fields = wpresidence_parse_api_fields_param($request->get_param('fields'));

    // Verify agency exists
    $post = get_post($id);
    if (!$post || $post->post_type !== 'estate_agency') {
        return new WP_Error('rest_agency_not_found', __('Agency not found'), ['status' => 404]);
    }

    // Get cached agency data
    $cached_data = wpestate_api_get_cached_post_data($id, 'estate_agency');
    
    // Filter response based on requested fields using the utility function
    if ($fields) {
        $response = wpresidence_filter_api_response_fields($cached_data, $fields);
    } else {
        $response = $cached_data;
    }

    return rest_ensure_response($response);
}