<?php
/**
 * WPResidence API Routes for Developers
 *
 * Registers REST API endpoints for developer listing, viewing, creation, 
 * editing and deletion. These endpoints provide a complete developer 
 * management interface through the WP REST API.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Register all developer-related REST API routes
 */
add_action('rest_api_init', function() {
    
    /**
     * GET/POST multiple developers with filtering capabilities
     * 
     * POST method is used to allow complex filtering via JSON body
     * Accessible to all users
     */
    register_rest_route('wpresidence/v1', '/developers', [
        'methods' => 'POST',
        'callback' => 'wpresidence_get_all_developers',
        'permission_callback' => '__return_true', // Available to all users
    ]);

    /**
     * GET a single developer by ID
     * 
     * Supports two response types: 'basic' (minimal data) and 'full' (complete developer data)
     * Optional 'fields' parameter allows requesting specific data fields only
     * Accessible to all users
     */
    register_rest_route('wpresidence/v1', '/developer/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'wpresidence_get_single_developer',
        'args' => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
                'description' => 'The agent ID (positive integer)',
            ],
            'fields' => [
                'validate_callback' => function ($param) {
                    return is_string($param); // Comma-separated field list
                }
            ]
        ],
        'permission_callback' => '__return_true', // Available to all users
    ]);
   
    /**
     * POST a new developer
     * 
     * Creates a new developer with all associated metadata and taxonomies
     * Requires user authentication and proper permissions
     */
    register_rest_route('wpresidence/v1', '/developer/add', [
        'methods' => 'POST',
        'callback' => 'wpresidence_create_developer',
        'permission_callback' => 'wpresidence_check_permissions_for_posting_developer',
    ]);
   
    /**
     * PUT (update) an existing developer
     * 
     * Updates developer data, metadata, taxonomies and related content
     * Requires user authentication and ownership of the developer
     */
    register_rest_route('wpresidence/v1', '/developer/edit/(?P<id>\d+)', [
        'methods' => 'PUT',
        'callback' => 'wpresidence_update_developer',
        'permission_callback' => 'wpresidence_check_permissions_for_developer',
        'args' => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
                'description' => 'The agent ID (positive integer)',
            ],
        ],
    ]);

    /**
     * DELETE a developer
     * 
     * Permanently removes a developer from the database
     * Requires user authentication and ownership of the developer
     */
    register_rest_route('wpresidence/v1', '/developer/delete/(?P<id>\d+)', [
        'methods' => 'DELETE',
        'callback' => 'wpresidence_delete_developer',
        'permission_callback' => 'wpresidence_check_permissions_for_developer',
        'args' => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
                'description' => 'The agent ID (positive integer)',
            ],
        ],
    ]);
   
});