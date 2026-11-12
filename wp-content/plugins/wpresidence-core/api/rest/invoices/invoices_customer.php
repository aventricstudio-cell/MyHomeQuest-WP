<?php
/**
 * WPResidence Customer Invoice Functions
 *
 * This file contains functions to handle customer-specific invoice operations,
 * including permission validation and invoice retrieval with filtering and pagination.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Permission callback to validate access to customer invoices.
 *
 * This function ensures that the user requesting access to customer invoices
 * is either the customer themselves or an administrator.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return true|WP_Error True if the user has permission, otherwise a WP_Error object.
 */
function wpresidence_check_permissions_invoices_for_customer($request) {
    // Verify the JWT token is valid - ensures authenticated API access
    $userID = apply_filters('determine_current_user', null);
    if (!$userID) {
        return new WP_Error(
            'jwt_auth_failed',
            __('Invalid or missing JWT token.'),
            ['status' => 403]
        );
    }
    
    // Set current user context for WordPress permission checks
    wp_set_current_user($userID);

    // Get current user details
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;

    // Extract the requested user ID from the URL parameter
    $requested_user_id = intval($request['user_id']);

    // Check if the user is the same as the requested user ID (own invoices)
    // or if the user is an administrator (can access any user's invoices)
    if ($userID === $requested_user_id || current_user_can('administrator')) {
        return true;
    }

    // Return error if unauthorized - prevents users from accessing others' invoices
    return new WP_Error(
        'rest_forbidden',
        __('You do not have permission to access these invoices.'),
        ['status' => 403]
    );
}

/**
 * Retrieve all invoices for a customer with pagination support
 * 
 * Fetches invoices where the customer is the buyer, with options for filtering
 * by date range, invoice type, and status. Results are paginated for performance.
 * 
 * @param WP_REST_Request $request The REST request object containing:
 *     @type int    user_id        The ID of the customer whose invoices to retrieve
 *     @type string start_date     Start date for filtering (optional)
 *     @type string end_date       End date for filtering (optional)
 *     @type string type          Invoice type filter (optional)
 *     @type string status        Invoice status filter (optional)
 *     @type int    page          Page number (optional, default: 1)
 *     @type int    posts_per_page Number of posts per page (optional, default: 10)
 * 
 * @return WP_REST_Response|WP_Error Response containing filtered invoices data or error
 */
function wpresidence_get_customer_invoices(WP_REST_Request $request) {
    // Parse and validate request parameters
    $params = wpresidence_parse_request_params($request);
    
    // Get user ID from URL parameter
    $user_id = intval($request['user_id']);
    
    // Verify the requested user exists in the system
    if (!get_user_by('id', $user_id)) {
        return new WP_Error(
            'invalid_user',
            'The specified user does not exist',
            array('status' => 404)
        );
    }
    
    // Set pagination defaults if not provided in request
    $paged = isset($params['page']) ? intval($params['page']) : 1;
    $posts_per_page = isset($params['posts_per_page']) ? intval($params['posts_per_page']) : 10;
    
    // Validate page number is positive
    if ($paged < 1) {
        return new WP_Error(
            'invalid_page',
            'Page number must be greater than 0',
            array('status' => 400)
        );
    }
    
    // Validate posts per page is within reasonable limits
    if ($posts_per_page < 1 || $posts_per_page > 100) {
        return new WP_Error(
            'invalid_posts_per_page',
            'Posts per page must be between 1 and 100',
            array('status' => 400)
        );
    }

    // Build meta query to filter invoices by buyer_id
    // This ensures we only get invoices where this user is the buyer
    $meta_query = array(
        'relation' => 'AND',
        array(
            'key'     => 'buyer_id',
            'value'   => $user_id,
            'type'    => 'NUMERIC',
            'compare' => '='
        )
    );

    // Add invoice type filter if provided in request
    if (!empty($params['type'])) {
        $meta_query[] = array(
            'key'     => 'invoice_type',
            'value'   => $params['type'],
            'compare' => '='
        );
    }

    // Add invoice status filter if provided in request
    if (!empty($params['status'])) {
        $meta_query[] = array(
            'key'     => 'invoice_status',
            'value'   => $params['status'],
            'compare' => '='
        );
    }

    // Build date query for filtering by creation date range
    $date_query = array();
    
    // Add start date to query if provided
    if (!empty($params['start_date'])) {
        $date_query['after'] = sanitize_text_field($params['start_date']);
    }
    
    // Add end date to query if provided
    if (!empty($params['end_date'])) {
        $date_query['before'] = sanitize_text_field($params['end_date']);
    }
    
    // Make date range inclusive if dates are specified
    if (!empty($date_query)) {
        $date_query['inclusive'] = true;
    }

    // Setup complete WP_Query arguments
    $args = array(
        'post_type'      => 'wpestate_invoice', // Invoice custom post type
        'post_status'    => 'publish',             // Only published invoices
        'posts_per_page' => $posts_per_page,       // Pagination limit
        'paged'          => $paged,                // Current page
        'meta_query'     => $meta_query,           // Filter by meta fields
        'date_query'     => $date_query,           // Filter by date range
        'orderby'        => 'date',                // Sort by date
        'order'          => 'DESC'                 // Newest first
    );

    // Execute the query to get matching invoices
    $invoice_query = new WP_Query($args);
    $invoices = array();
    
    // Process results if invoices were found
    if ($invoice_query->have_posts()) {
        while ($invoice_query->have_posts()) {
            $invoice_query->the_post();
            // Get detailed information for each invoice
            $invoices[] = wpresidence_get_invoice_details(get_the_ID());
        }
        // Reset global post data to prevent conflicts
        wp_reset_postdata();
    }

    // Calculate invoice totals for summary information
    $totals = wpresidence_calculate_invoice_totals($invoices);
    
    // Prepare pagination metadata for client-side navigation
    $pagination = array(
        'current_page'   => $paged,
        'posts_per_page' => $posts_per_page,
        'total_posts'    => $invoice_query->found_posts,   // Total matching invoices
        'total_pages'    => $invoice_query->max_num_pages, // Total pages of results
        'has_previous'   => ($paged > 1),                  // Is there a previous page
        'has_next'       => ($paged < $invoice_query->max_num_pages) // Is there a next page
    );
    
    // Construct and return the complete response
    return rest_ensure_response(array(
        'status'     => 'success',                // Operation status
        'user_id'    => $user_id,                 // Customer ID
        'invoices'   => $invoices,                // Invoice details
        'pagination' => $pagination,              // Pagination metadata
        'totals'     => $totals,                  // Summary of invoice amounts
        'filters'    => array(                    // Applied filters for reference
            'type'       => $params['type'] ?? null,
            'status'     => $params['status'] ?? null,
            'start_date' => $params['start_date'] ?? null,
            'end_date'   => $params['end_date'] ?? null
        )
    ));
}