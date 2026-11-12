<?php
/**
 * WPResidence API Routes for Reviews
 *
 * Registers REST API endpoints for review listing, viewing, creation, 
 * editing and deletion. These endpoints provide a complete review 
 * management interface through the WP REST API.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Register all review-related REST API routes
 */
add_action('rest_api_init', function() {
    
    /**
     * GET/POST multiple reviews with filtering capabilities
     * 
     * POST method is used to allow complex filtering via JSON body
     * Accessible to all users
     */
    register_rest_route('wpresidence/v1', '/reviews', [
        'methods' => 'POST',
        'callback' => 'wpresidence_get_all_reviews',
        'permission_callback' => '__return_true', // Available to all users
    ]);

    /**
     * GET a single review by ID
     * 
     * Supports two response types: 'basic' (minimal data) and 'full' (complete review data)
     * Optional 'fields' parameter allows requesting specific data fields only
     * Accessible to all users
     */
    register_rest_route('wpresidence/v1', '/review/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'wpresidence_get_single_review',
        'args' => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
                'description' => 'The review ID (positive integer)',
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
     * POST a new review
     * 
     * Creates a new review with all associated metadata
     * Requires user authentication
     */
    register_rest_route('wpresidence/v1', '/post-review', [
        'methods' => 'POST',
        'callback' => 'wpresidence_post_review',
        'permission_callback' => function () {
            return is_user_logged_in();
        }
    ]);
   
    /**
     * PUT (update) an existing review
     * 
     * Updates review data and metadata
     * Requires user authentication and ownership of the review
     */
    register_rest_route('wpresidence/v1', '/review/edit/(?P<id>\d+)', [
        'methods' => 'PUT',
        'callback' => 'wpresidence_update_review',
        'permission_callback' => 'wpresidence_check_permissions_for_review',
        'args' => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
                'description' => 'The review ID (positive integer)',
            ],
        ],
    ]);

    /**
     * DELETE a review
     * 
     * Permanently removes a review from the database
     * Requires user authentication and ownership of the review
     */
    register_rest_route('wpresidence/v1', '/review/delete/(?P<id>\d+)', [
        'methods' => 'DELETE',
        'callback' => 'wpresidence_delete_review',
        'permission_callback' => 'wpresidence_check_permissions_for_review',
        'args' => [
            'id' => [
                'validate_callback' => function($param) {
                    return is_numeric($param) && $param > 0;
                },
                'sanitize_callback' => 'absint',
                'description' => 'The review ID (positive integer)',
            ],
        ],
    ]);
   
});