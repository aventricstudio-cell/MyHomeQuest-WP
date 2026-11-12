<?php
/**
 * WPResidence API Routes for Messages
 *
 * Registers REST API endpoints for message listing, viewing, creation, 
 * editing and deletion. These endpoints provide a complete message 
 * management interface through the WP REST API.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Register all message-related REST API routes
 */
add_action('rest_api_init', function() {
    
    /**
     * GET/POST multiple messages with filtering capabilities
     * 
     * POST method is used to allow complex filtering via JSON body
     * Permission is limited to authenticated users
     */
    register_rest_route('wpresidence/v1', '/messages', [
        'methods' => 'POST',
        'callback' => 'wpresidence_get_all_messages',
        'permission_callback' => 'wpresidence_check_authenticated_user',
    ]);

    /**
     * GET a single message by ID
     * 
     * Supports two response types: 'basic' (minimal data) and 'full' (complete message data)
     * Optional 'fields' parameter allows requesting specific data fields only
     * Permission is limited to authenticated users who are either sender or recipient
     */
    register_rest_route('wpresidence/v1', '/message/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'wpresidence_get_single_message',
        'args' => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
                'description' => 'The message ID (positive integer)',
            ],
            'fields' => [
                'validate_callback' => function ($param) {
                    return is_string($param); // Comma-separated field list
                }
            ]
        ],
        'permission_callback' => 'wpresidence_check_permissions_for_message',
    ]);
   
    /**
     * POST a new message
     * 
     * Creates a new message with all associated metadata
     * Requires user authentication
     */
    register_rest_route('wpresidence/v1', '/message/add', [
        'methods' => 'POST',
        'callback' => 'wpresidence_create_message',
        'permission_callback' => 'wpresidence_check_authenticated_user',
    ]);
   


    /**
     * DELETE a message
     * 
     * Marks a message as deleted for the current user
     * Requires user authentication and must be sender or recipient
     */
    register_rest_route('wpresidence/v1', '/message/delete/(?P<id>\d+)', [
        'methods' => 'DELETE',
        'callback' => 'wpresidence_delete_message',
        'permission_callback' => 'wpresidence_check_permissions_for_message',
        'args' => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
                'description' => 'The message ID (positive integer)',
            ],
        ],
    ]);
   
});