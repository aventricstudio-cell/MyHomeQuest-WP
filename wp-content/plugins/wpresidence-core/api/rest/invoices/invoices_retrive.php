<?php



/**
 * Permission callback to validate user authentication token.
 *
 * @return true|WP_Error True if token is valid, error otherwise
 */
function wpresidence_check_auth_token() {
    // Verify the JWT token to ensure authenticated API access
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
    
    // If we get here, authentication was successful
    return true;
}



/**
 * Retrieve invoices for the currently authenticated user.
 *
 * @param WP_REST_Request $request The REST request containing filter parameters
 * @return WP_REST_Response|WP_Error Response with invoices or error
 */
function wpresidence_get_my_invoices(WP_REST_Request $request) {
    // Get current user ID from authentication
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    
    // Add user_id to request parameters
    $request->set_param('user_id', $user_id);
    
    // Use the existing function to get invoices
    return wpresidence_get_all_invoices($request);
}



/**
 * Retrieve all invoices with filtering and pagination support.
 * Can also filter by owner ID if provided.
 *
 * @param WP_REST_Request $request The REST request object containing:
 *     @type int    user_id        The ID of the owner to filter by (optional)
 *     @type string start_date     Start date for filtering (optional)
 *     @type string end_date       End date for filtering (optional)
 *     @type string type          Invoice type filter (optional)
 *     @type string status        Invoice status filter (optional)
 *     @type string billing_type  Billing type filter (optional)
 *     @type float  price_min     Minimum price filter (optional)
 *     @type float  price_max     Maximum price filter (optional)
 *     @type int    page          Page number (optional, default: 1)
 *     @type int    posts_per_page Number of posts per page (optional, default: 10)
 * 
 * @return WP_REST_Response|WP_Error Response containing filtered invoices data or error
 */
function wpresidence_get_all_invoices(WP_REST_Request $request) {
    // Parse and validate request parameters
    $params = wpresidence_parse_request_params($request);
    
    // Get owner user ID from URL parameter or request params
    $user_id = 0;
    if (isset($request['user_id'])) {
        $user_id = intval($request['user_id']);
    } elseif (isset($params['user_id'])) {
        $user_id = intval($params['user_id']);
    }
    
    // Verify the specified user exists if a user_id was provided
    if ($user_id > 0 && !get_user_by('id', $user_id)) {
        return new WP_Error(
            'invalid_user',
            'The specified user does not exist',
            array('status' => 404)
        );
    }
    
    // Set pagination defaults if not provided
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

    // Define invoice types with proper translation support
    $reservation_strings = array(
        'Upgrade to Featured'           => esc_html__('Upgrade to Featured', 'wpresidence-core'),
        'Publish Listing with Featured' => esc_html__('Publish Listing with Featured', 'wpresidence-core'),
        'Package'                       => esc_html__('Package', 'wpresidence-core'),
        'Listing'                       => esc_html__('Listing', 'wpresidence-core'),
        'Reservation fee'               => esc_html__('Reservation fee', 'wpresidence-core')
    );

    // Initialize meta query array for filtering
    $meta_query = array('relation' => 'AND');
    
    
    // Add invoice type filter if provided
    if (!empty($params['type'])) {
        // Use mapped value if exists, otherwise use provided value directly
        $type = $reservation_strings[$params['type']] ?? $params['type'];
        $meta_query[] = array(
            'key'     => 'invoice_type',
            'value'   => $type,
            'type'    => 'char',
            'compare' => 'LIKE'
        );
    }

    // Add invoice status filter if provided
    if (!empty($params['status'])) {
        $meta_query[] = array(
            'key'     => 'pay_status',
            'value'   => $params['status'],
            'type'    => 'char',
            'compare' => 'LIKE'
        );
    }
    
    // Add billing type filter if provided
    if (!empty($params['billing_type'])) {
        $meta_query[] = array(
            'key'     => 'biling_type',
            'value'   => $params['billing_type'],
            'type'    => 'char',
            'compare' => 'LIKE'
        );
    }
    
    // Add price range filters if provided
    if (!empty($params['price_min']) || !empty($params['price_max'])) {
        $price_query = array(
            'key'     => 'item_price',
            'type'    => 'NUMERIC',
        );
        
        if (!empty($params['price_min'])) {
            $price_query['value'] = floatval($params['price_min']);
            $price_query['compare'] = '>=';
        }
        
        if (!empty($params['price_max'])) {
            if (isset($price_query['value'])) {
                // If we already have a minimum price, create a range query
                $price_query['value'] = array(floatval($params['price_min']), floatval($params['price_max']));
                $price_query['compare'] = 'BETWEEN';
            } else {
                // Otherwise just check for maximum price
                $price_query['value'] = floatval($params['price_max']);
                $price_query['compare'] = '<=';
            }
        }
        
        $meta_query[] = $price_query;
    }

    // Build date range query if date parameters provided
    $date_query = array();
    
    // Add start date if provided
    if (!empty($params['start_date'])) {
        $date_query['after'] = sanitize_text_field($params['start_date']);
    }
    
    // Add end date if provided
    if (!empty($params['end_date'])) {
        $date_query['before'] = sanitize_text_field($params['end_date']);
    }
    
    // Make date range inclusive if dates specified
    if (!empty($date_query)) {
        $date_query['inclusive'] = true;
    }



    // Prepare complete query arguments
    $args = array(
        'post_type'      => 'wpestate_invoice', // Updated post type
        'post_status'    => 'publish',          // Only published invoices
        'posts_per_page' => $posts_per_page,    // Results per page
        'paged'          => $paged,             // Current page number
        'meta_query'     => $meta_query,        // Applied filters by meta fields
        'date_query'     => $date_query         // Applied date range filter
    );

    
    // Add owner filter if user_id is provided
    if ($user_id > 0) {
        $args [ 'author' ]        = $user_id; // Filter by post author only
    }



    // Execute the query to get matching invoices
    $invoice_query = new WP_Query($args);
    $invoices = array();
    
    // Process results if any invoices were found
    if ($invoice_query->have_posts()) {
        while ($invoice_query->have_posts()) {
            $invoice_query->the_post();
            $invoice_id = get_the_ID();
            
            // Get full invoice details
            $invoices[] = wpresidence_get_invoice_details($invoice_id);
        }
        // Reset post data to prevent conflicts
        wp_reset_postdata();
    }

    // Calculate summary totals for all retrieved invoices
    $totals = wpresidence_calculate_invoice_totals($invoices);
    
    // Prepare pagination metadata for navigation
    $pagination = array(
        'current_page'   => $paged,                        // Current page number
        'posts_per_page' => $posts_per_page,               // Items per page
        'total_posts'    => $invoice_query->found_posts,   // Total matching items
        'total_pages'    => $invoice_query->max_num_pages, // Total pages
        'has_previous'   => ($paged > 1),                  // Previous page available
        'has_next'       => ($paged < $invoice_query->max_num_pages) // Next page available
    );
    
    // Prepare response data
    $response_data = array(
        'status'     => 'success',      // Operation status
        'invoices'   => $invoices,      // Invoice data
        'pagination' => $pagination,    // Pagination info
        'totals'     => $totals         // Summary totals
    );
    
    // Add owner_id to response if it was filtered by owner
    if ($user_id > 0) {
        $response_data['owner_id'] = $user_id;
    }
    
    // Return structured response with all data
    return rest_ensure_response($response_data);
}









/**
 * Retrieve a single invoice via REST API.
 *
 * Gets detailed information about a specific invoice by ID.
 * Used for individual invoice viewing and operations.
 *
 * @param WP_REST_Request $request The REST request object containing:
 *     @type int id The invoice ID
 * 
 * @return WP_REST_Response|WP_Error Response containing invoice data or error
 */
function wpresidence_get_single_invoice(WP_REST_Request $request) {
    // Get invoice ID from URL parameter
    $invoice_id = intval($request['id']);
    
    // Verify the invoice exists and has the correct post type
    $invoice = get_post($invoice_id);
    if (!$invoice || $invoice->post_type !== 'wpestate_invoice') {
        return new WP_Error(
            'invalid_invoice',
            'Invalid invoice ID or incorrect post type',
            array('status' => 404)
        );
    }

    // Get detailed invoice information
    $invoice_details = wpresidence_get_invoice_details($invoice_id);
    if (!$invoice_details) {
        return new WP_Error(
            'invoice_retrieval_failed',
            'Failed to retrieve invoice details',
            array('status' => 500)
        );
    }
    
    // Return success response with invoice data
    return rest_ensure_response(array(
        'status' => 'success',
        'invoice' => $invoice_details
    ));
}