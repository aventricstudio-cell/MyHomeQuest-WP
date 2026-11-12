<?php
/**
 * WPResidence API Functions for Message Management
 *
 * Core functions for handling message filtering, data processing, 
 * and response formatting for the WPResidence REST API.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Check if the current user is authenticated.
 * 
 * Verifies user authentication for basic message operations.
 *
 * @param WP_REST_Request $request REST API request object.
 * @return bool|WP_Error True if the user is authenticated, otherwise a WP_Error.
 */
function wpresidence_check_authenticated_user(WP_REST_Request $request) {
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

    // Check if the user is logged in
    if (!$user_id || !is_user_logged_in()) {
        return new WP_Error(
            'rest_forbidden',
            __('You must be logged in to access messages.'),
            ['status' => 403]
        );
    }

    return true;
}

/**
 * Check if the current user has permissions to perform an action on a message.
 * 
 * Verifies user is either the sender or recipient of the message.
 *
 * @param WP_REST_Request $request REST API request object.
 * @return bool|WP_Error True if the user has permission, otherwise a WP_Error.
 */
function wpresidence_check_permissions_for_message(WP_REST_Request $request) {
    // First check basic authentication
    $auth_check = wpresidence_check_authenticated_user($request);
    if (is_wp_error($auth_check)) {
        return $auth_check;
    }

    // Get the current user ID
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    // Get the message ID from the request
    $message_id = $request->get_param('id');
    if (!$message_id || !is_numeric($message_id)) {
        return new WP_Error(
            'rest_invalid_message',
            __('Invalid message ID.'),
            ['status' => 400]
        );
    }

    // Validate the message exists and is the correct post type
    $message = get_post($message_id);
    if (!$message || $message->post_type !== 'wpestate_message') {
        return new WP_Error(
            'rest_message_not_found',
            __('Message not found.'),
            ['status' => 404]
        );
    }

    // Get message sender and recipient
    $from_user = get_post_meta($message_id, 'message_from_user', true);
    $to_user = get_post_meta($message_id, 'message_to_user', true);

    // Check if current user is either sender or recipient
    if (intval($from_user) !== intval($user_id) && intval($to_user) !== intval($user_id)) {
        return new WP_Error(
            'rest_forbidden',
            __('You do not have permission to access this message.'),
            ['status' => 403]
        );
    }

    return true;
}

/**
 * Retrieve all messages for current user with specified filters and pagination.
 * 
 * Main function for message listing API endpoint.
 * Processes complex filtering options and returns data in requested format.
 *
 * @param WP_REST_Request $request REST API request containing filter parameters.
 * @return WP_REST_Response Response containing filtered message data.
 */
function wpresidence_get_all_messages(WP_REST_Request $request) {
    // Parse parameters
    $params = wpresidence_parse_request_params($request);

    // Get current user ID
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    // Set defaults and extract main parameters
    $paged = isset($params['page']) ? intval($params['page']) : 1;
    $posts_per_page = isset($params['posts_per_page']) ? intval($params['posts_per_page']) : 10;
    
    // Parse fields parameter
    $fields = wpresidence_parse_api_fields_param($params['fields'] ?? null);

    // Initialize query arrays
    $meta_input = [];
    
    // Set up meta query to get messages where user is sender or recipient
    $meta_input[] = [
        'relation' => 'OR',
        [
            'key' => 'message_from_user',
            'value' => $user_id,
            'compare' => '=',
            'type' => 'NUMERIC'
        ],
        [
            'key' => 'message_to_user',
            'value' => $user_id,
            'compare' => '=',
            'type' => 'NUMERIC'
        ]
    ];
    
    // Add filter for deleted messages
    $meta_input[] = [
        'relation' => 'OR',
        [
            'key' => 'delete_source',
            'value' => 0,
            'compare' => '=',
            'type' => 'NUMERIC'
        ],
        [
            'key' => 'delete_destination'.$user_id,
            'value' => 0,
            'compare' => '=',
            'type' => 'NUMERIC'
        ]
    ];

    // Simplified filtering for common message fields
    $simple_meta_fields = [
        'message_status' => 'CHAR',
        'mess_status' => 'CHAR'
    ];

    foreach ($simple_meta_fields as $field => $type) {
        if (isset($params[$field])) {
            $meta_query = [
                'key' => $field,
                'value' => $params[$field],
                'compare' => '=',
                'type' => $type
            ];
            $meta_input[] = $meta_query;
        }
    }

    // Look for other user filter - removing this from meta query and doing post-filtering instead
    // if (isset($params['other_user']) && is_numeric($params['other_user'])) {
    //     $other_user_id = intval($params['other_user']);
    //     $meta_input[] = [
    //         'relation' => 'AND',
    //         [
    //             'relation' => 'OR',
    //             [
    //                 'key' => 'message_from_user',
    //                 'value' => $other_user_id,
    //                 'compare' => '=',
    //                 'type' => 'NUMERIC'
    //             ],
    //             [
    //                 'key' => 'message_to_user',
    //                 'value' => $other_user_id,
    //                 'compare' => '=',
    //                 'type' => 'NUMERIC'
    //             ]
    //         ]
    //     ];
    // }

    // Process additional meta filters (keeping for backward compatibility)
    if (isset($params['meta']) && is_array($params['meta'])) {
        foreach ($params['meta'] as $key => $meta_data) {
            // Skip if already handled in simple_meta_fields
            if (array_key_exists($key, $simple_meta_fields)) {
                continue;
            }

            // Handle both simple and complex meta formats
            if (is_array($meta_data)) {
                // Complex format with compare and type
                $meta_query = [
                    'key' => $key,
                    'compare' => isset($meta_data['compare']) ? $meta_data['compare'] : '=',
                    'type' => isset($meta_data['type']) ? $meta_data['type'] : 'CHAR'
                ];

                if (isset($meta_data['value'])) {
                    $meta_query['value'] = $meta_data['value'];
                }
            } else {
                // Simple format with just value
                $meta_query = [
                    'key' => $key,
                    'value' => $meta_data,
                    'compare' => '=',
                    'type' => 'CHAR'
                ];
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

    $post_type = 'wpestate_message';

    // Call the custom query function with API type
    $query_result = wpestate_api_custom_query(
        $post_type,
        $paged,
        $posts_per_page,
        $meta_input,
        [], // No taxonomy input for messages
        3, // Default order
        null, // No userID filter since we filter in meta query
        'api'
    );

    // Ensure we have valid results
    if (!$query_result || !isset($query_result['post_ids'])) {
        return new WP_REST_Response(
            [
                'status' => 'error',
                'message' => 'No messages found',
            ],
            404
        );
    }

    // Process results
    $messages = [];
    foreach ($query_result['post_ids'] as $postID) {
        // Get full message data
        $message_data = wpestate_api_get_cached_post_data($postID, $post_type);
        
        // Get message metadata
        $message_status = get_post_meta($postID, 'message_status', true);
        $mess_status = get_post_meta($postID, 'mess_status', true);
        
        // Get message participants
        $from_user_id = get_post_meta($postID, 'message_from_user', true);
        $to_user_id = get_post_meta($postID, 'message_to_user', true);
        
        // Determine the other user (the one that's not the current user)
        $other_user = (intval($from_user_id) === intval($user_id)) ? intval($to_user_id) : intval($from_user_id);
        
        // Skip this message if other_user filter is set and doesn't match
        if (isset($params['other_user']) && intval($params['other_user']) !== $other_user) {
            continue;
        }
        
        // Clean up unnecessary fields
        $clean_message = [
            'ID' => $message_data['ID'],
            'title' => $message_data['title'],
            'description' => $message_data['description'],
            'message_status' => $message_status ?? 'unread',
            'mess_status' => $mess_status ?? 'new',
            'other_user' => $other_user
        ];
        
        // Add additional user data
        $from_user_id = get_post_meta($postID, 'message_from_user', true);
        $to_user_id = get_post_meta($postID, 'message_to_user', true);
        
        if ($from_user_id) {
            $user_data = get_userdata($from_user_id);
            if ($user_data) {
                $clean_message['from_user_data'] = [
                    'display_name' => $user_data->display_name,
                    'user_email' => $user_data->user_email,
                    'user_login' => $user_data->user_login
                ];
            }
        }
        
        if ($to_user_id) {
            $user_data = get_userdata($to_user_id);
            if ($user_data) {
                $clean_message['to_user_data'] = [
                    'display_name' => $user_data->display_name,
                    'user_email' => $user_data->user_email,
                    'user_login' => $user_data->user_login
                ];
            }
        }
        
        $messages[] = $clean_message;
    }

    // If specific fields are requested, filter the results
    if ($fields) {
        $messages = wpresidence_filter_api_collection($messages, $fields);
    }

    // Return formatted response
    return new WP_REST_Response(
        [
            'status' => 'success',
            'data' => $messages,
            'total' => count($messages),
            'pages' => ceil(count($messages) / $posts_per_page)
        ],
        200
    );
}

/**
 * Retrieve a single message by its ID.
 * 
 * Fetches complete message data for a specific message.
 * Supports field filtering to return only requested data.
 *
 * @param WP_REST_Request $request REST API request containing the message ID.
 * @return WP_REST_Response|WP_Error Response with message data or error if not found.
 */
function wpresidence_get_single_message(WP_REST_Request $request) {
    $params = wpresidence_parse_request_params($request);

    // Extract and validate essential parameters
    $id = $params['id'] ?? null;

    // Parse fields parameter
    $fields = wpresidence_parse_api_fields_param($request->get_param('fields'));

    // Get current user ID
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    // Verify message exists
    $post = get_post($id);
    if (!$post || $post->post_type !== 'wpestate_message') {
        return new WP_Error('rest_message_not_found', __('Message not found'), ['status' => 404]);
    }

    // Get message data
    $message_data = wpestate_api_get_cached_post_data($id, 'wpestate_message');
    
    // Get message metadata
    $message_status = get_post_meta($id, 'message_status', true);
    $mess_status = get_post_meta($id, 'mess_status', true);
    
    // Get message participants
    $from_user_id = get_post_meta($id, 'message_from_user', true);
    $to_user_id = get_post_meta($id, 'message_to_user', true);
    
    // Determine the other user (the one that's not the current user)
    $other_user = (intval($from_user_id) === intval($user_id)) ? intval($to_user_id) : intval($from_user_id);
    
    // Format the response to match the messages list format
    $clean_message = [
        'ID' => $message_data['ID'],
        'title' => $message_data['title'],
        'description' => $message_data['description'],
        'message_status' => $message_status ?? 'unread',
        'mess_status' => $mess_status ?? 'new',
        'other_user' => $other_user
    ];
    
    // Add user data
    if ($from_user_id) {
        $user_data = get_userdata($from_user_id);
        if ($user_data) {
            $clean_message['from_user_data'] = [
                'display_name' => $user_data->display_name,
                'user_email' => $user_data->user_email,
                'user_login' => $user_data->user_login
            ];
        }
    }
    
    if ($to_user_id) {
        $user_data = get_userdata($to_user_id);
        if ($user_data) {
            $clean_message['to_user_data'] = [
                'display_name' => $user_data->display_name,
                'user_email' => $user_data->user_email,
                'user_login' => $user_data->user_login
            ];
        }
    }
    
    // If recipient views the message, mark it as read
    if (intval($to_user_id) === intval($user_id)) {
        update_post_meta($id, 'message_status', 'read');
        update_post_meta($id, 'message_status'.$user_id, 'read');
    }
    
    // Apply field filtering if requested
    if ($fields) {
        $clean_message = wpresidence_filter_api_response_fields($clean_message, $fields);
    }

    return rest_ensure_response($clean_message);
}