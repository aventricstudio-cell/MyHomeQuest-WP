<?php
/**
 * WPResidence API Routes for Properties
 *
 * Registers REST API endpoints for property listing, viewing, creation, 
 * editing and deletion. These endpoints provide a complete property 
 * management interface through the WP REST API.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Register all property-related REST API routes
 */
add_action('rest_api_init', function() {
    
    /**
     * GET/POST multiple properties with filtering capabilities
     * 
     * POST method is used to allow complex filtering via JSON body
     * Accessible to all users
     */
    register_rest_route('wpresidence/v1', '/properties', [
        'methods' => 'POST',
        'callback' => 'wpresidence_get_all_properties',
        'permission_callback' => '__return_true', // Available to all users
    ]);

    /**
     * GET a single property by ID
     * 
     * Supports two response types: 'basic' (minimal data) and 'full' (complete property data)
     * Optional 'fields' parameter allows requesting specific data fields only
     * Accessible to all users
     */
    register_rest_route('wpresidence/v1', '/property/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'wpresidence_get_single_property',
        'args' => [
            'response_type' => [
                'default' => 'full',
                'validate_callback' => function ($param) {
                    return in_array($param, ['basic', 'full']);
                }
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
     * POST a new property
     * 
     * Creates a new property with all associated metadata and taxonomies
     * Requires user authentication and proper permissions
     */
    register_rest_route('wpresidence/v1', '/property/add', [
        'methods' => 'POST',
        'callback' => 'wpresidence_create_property',
        'permission_callback' => 'wpresidence_check_permissions_for_posting',
    ]);
   
    /**
     * PUT (update) an existing property
     * 
     * Updates property data, metadata, taxonomies and related content
     * Requires user authentication and ownership of the property
     */
    register_rest_route('wpresidence/v1', '/property/edit/(?P<id>\d+)', [
        'methods' => 'PUT',
        'callback' => 'wpresidence_update_property',
        'permission_callback' => 'wpresidence_check_permissions_for_property',
        'args' => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
                'description' => 'The property ID (positive integer)',
            ],
        ]
    ]);

    /**
     * DELETE a property
     * 
     * Permanently removes a property from the database
     * Requires user authentication and ownership of the property
     */
    register_rest_route('wpresidence/v1', '/property/delete/(?P<id>\d+)', [
        'methods' => 'DELETE',
        'callback' => 'wpresidence_delete_property',
        'permission_callback' => 'wpresidence_check_permissions_for_property',

        'args' => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
                'description' => 'The property ID (positive integer)',
            ],
        ],

    ]);
   
});