<?php
/**
 * WPResidence API Routes for Agencies
 *
 * Registers REST API endpoints for agency listing, viewing, creation, 
 * editing and deletion. These endpoints provide a complete agency 
 * management interface through the WP REST API.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Register all agency-related REST API routes
 */
add_action('rest_api_init', function() {
    
    /**
     * GET/POST multiple agencies with filtering capabilities
     * 
     * POST method is used to allow complex filtering via JSON body
     * Accessible to all users
     */
    register_rest_route('wpresidence/v1', '/agencies', [
        'methods' => 'POST',
        'callback' => 'wpresidence_get_all_agencies',
        'permission_callback' => '__return_true', // Available to all users
    ]);

    /**
     * GET a single agency by ID
     * 
     * Supports two response types: 'basic' (minimal data) and 'full' (complete agency data)
     * Optional 'fields' parameter allows requesting specific data fields only
     * Accessible to all users
     */
    register_rest_route('wpresidence/v1', '/agency/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'wpresidence_get_single_agency',
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
     * POST a new agency
     * 
     * Creates a new agency with all associated metadata and taxonomies
     * Requires user authentication and proper permissions
     */
    register_rest_route('wpresidence/v1', '/agency/add', [
        'methods' => 'POST',
        'callback' => 'wpresidence_create_agency',
        'permission_callback' => 'wpresidence_check_permissions_for_posting_agency',
    ]);
   
    /**
     * PUT (update) an existing agency
     * 
     * Updates agency data, metadata, taxonomies and related content
     * Requires user authentication and ownership of the agency
     */
    register_rest_route('wpresidence/v1', '/agency/edit/(?P<id>\d+)', [
        'methods' => 'PUT',
        'callback' => 'wpresidence_update_agency',
        'permission_callback' => 'wpresidence_check_permissions_for_agency',
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
     * DELETE an agency
     * 
     * Permanently removes an agency from the database
     * Requires user authentication and ownership of the agency
     */
    register_rest_route('wpresidence/v1', '/agency/delete/(?P<id>\d+)', [
        'methods' => 'DELETE',
        'callback' => 'wpresidence_delete_agency',
        'permission_callback' => 'wpresidence_check_permissions_for_agency',
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