<?php
/**
 * WPResidence API Routes for Agents
 *
 * Registers REST API endpoints for agent listing, viewing, creation, 
 * editing and deletion. These endpoints provide a complete agent 
 * management interface through the WP REST API.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Register all agent-related REST API routes
 */
add_action('rest_api_init', function() {
    
    /**
     * GET/POST multiple agents with filtering capabilities
     * 
     * POST method is used to allow complex filtering via JSON body
     * Accessible to all users
     */
    register_rest_route('wpresidence/v1', '/agents', [
        'methods' => 'POST',
        'callback' => 'wpresidence_get_all_agents',
        'permission_callback' => '__return_true', // Available to all users
    ]);

    /**
     * GET a single agent by ID
     * 
     * Supports two response types: 'basic' (minimal data) and 'full' (complete agent data)
     * Optional 'fields' parameter allows requesting specific data fields only
     * Accessible to all users
     */
    register_rest_route('wpresidence/v1', '/agent/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'wpresidence_get_single_agent',
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
     * POST a new agent
     * 
     * Creates a new agent with all associated metadata and taxonomies
     * Requires user authentication and proper permissions
     */
    register_rest_route('wpresidence/v1', '/agent/add', [
        'methods' => 'POST',
        'callback' => 'wpresidence_create_agent',
        'permission_callback' => 'wpresidence_check_permissions_for_posting_agent',
    ]);
   
    /**
     * PUT (update) an existing agent
     * 
     * Updates agent data, metadata, taxonomies and related content
     * Requires user authentication and ownership of the agent
     */
    register_rest_route('wpresidence/v1', '/agent/edit/(?P<id>\d+)', [
        'methods' => 'PUT',
        'callback' => 'wpresidence_update_agent',
        'permission_callback' => 'wpresidence_check_permissions_for_agent', 
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
     * DELETE an agent
     * 
     * Permanently removes an agent from the database
     * Requires user authentication and ownership of the agent
     */
    register_rest_route('wpresidence/v1', '/agent/delete/(?P<id>\d+)', [
        'methods' => 'DELETE',
        'callback' => 'wpresidence_delete_agent',
        'permission_callback' => 'wpresidence_check_permissions_for_agent',
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