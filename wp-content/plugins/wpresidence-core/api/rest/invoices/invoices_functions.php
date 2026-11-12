<?php 


/**
 * Permission callback to validate access for viewing all invoices.
 *
 * This function ensures that only administrators can access the
 * comprehensive invoice listing endpoint.
 *
 * @return true|WP_Error True if the user has permission, otherwise a WP_Error object.
 */
function wpresidence_check_permissions_all_invoices() {
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
    
    // Only allow administrators to access all invoices
    if (current_user_can('administrator')) {
        return true;
    }
    
    // Return error for all non-administrator users
    return new WP_Error(
        'rest_forbidden',
        __('You do not have permission to view all invoices. Administrator access required.'),
        ['status' => 403]
    );
}



/**
 * WPResidence Invoice Helper Functions
 *
 * Core functions for invoice operations including permission validation,
 * invoice detail retrieval, and total calculations.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Permission callback to validate access to invoices for a user.
 *
 * Ensures that the requesting user is either the owner of the invoices
 * or an administrator with sufficient permissions.
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return true|WP_Error True if the user has permission, otherwise a WP_Error object.
 */
function wpresidence_check_permissions_invoices_for_user($request) {
    // Authenticate user via JWT token
    $userID = apply_filters('determine_current_user', null);
    if (!$userID) {
        return new WP_Error(
            'jwt_auth_failed',
            __('Invalid or missing JWT token.'),
            ['status' => 403]
        );
    }
    
    // Set WordPress context to the authenticated user
    wp_set_current_user($userID);

    // Get current user information
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;

    // Get requested user ID from URL parameters
    $requested_user_id = intval($request['user_id']);

    // Allow access if user is requesting their own invoices or is an administrator
    if ($userID === $requested_user_id || current_user_can('administrator')) {
        return true;
    }

    // Deny access for all other cases
    return new WP_Error(
        'rest_forbidden',
        __('You do not have permission to access these bookings.'),
        ['status' => 403]
    );
}

/**
 * Generate invoice details from an invoice post.
 * 
 * Extracts and organizes all relevant data from an invoice post and its meta fields
 * into a structured array format suitable for API responses.
 * 
 * @param int $invoice_id The ID of the invoice post
 * @return array Invoice details array with all relevant information
 */
/**
 * Generate invoice details from an invoice post.
 * 
 * Extracts and organizes all relevant data from an invoice post and its meta fields
 * into a structured array format suitable for API responses.
 * 
 * @param int $invoice_id The ID of the invoice post
 * @return array Invoice details array with all relevant information
 */
function wpresidence_get_invoice_details($invoice_id) {
    // Get basic invoice information from post meta
    $invoice_type = esc_html(get_post_meta($invoice_id, 'invoice_type', true));
    $bill_type = esc_html(get_post_meta($invoice_id, 'biling_type', true));
    $payment_status = esc_html(get_post_meta($invoice_id, 'pay_status', true));
    $price = floatval(get_post_meta($invoice_id, 'item_price', true));
    $purchase_date = esc_html(get_post_meta($invoice_id, 'purchase_date', true));
    $item_id = esc_html(get_post_meta($invoice_id, 'item_id', true));
    
    // Format payment status
    $status_text = ($payment_status == 0)
        ? esc_html__('Not Paid', 'wpresidence-core')
        : esc_html__('Paid', 'wpresidence-core');
    
    // Map invoice types to translatable strings
    $invoice_types = [
        'Listing' => esc_html__('Listing', 'wpresidence-core'),
        'Upgrade to Featured' => esc_html__('Upgrade to Featured', 'wpresidence-core'),
        'Publish Listing with Featured' => esc_html__('Publish Listing with Feature', 'wpresidence-core'),
        'Package' => esc_html__('Package', 'wpresidence-core'),
        'Reservation fee' => esc_html__('Reservation fee', 'wpresidence-core')
    ];
    
    // Map billing types to translatable strings
    $billing_types = [
        'One Time' => esc_html__('One Time', 'wpresidence-core'),
        'Recurring' => esc_html__('Recurring', 'wpresidence-core')
    ];
    
    // Set translated invoice type if available
    $invoice_type_display = isset($invoice_types[$invoice_type]) ? $invoice_types[$invoice_type] : $invoice_type;
    
    // Set translated billing type if available
    $bill_type_display = isset($billing_types[$bill_type]) ? $billing_types[$bill_type] : $bill_type;
    
    // Set default invoice period
    $invoice_period_saved = 'not aplicable';
    
    // Update invoice period for Package type
    if ($invoice_type == 'Package') {
        $invoice_period_saved = $bill_type;
    }
    
    // Get property ID from booking if applicable
    $property_id = 0;
    $booking_id = intval($item_id);
    if ($booking_id > 0 && $invoice_type == 'Reservation fee') {
        $property_id = intval(get_post_meta($booking_id, 'booking_id', true));
    }
    
    // Assemble complete invoice data structure
    return array(
        'id'                  => $invoice_id,                           // Invoice post ID
        'title'               => get_the_title($invoice_id),            // Invoice title
        'date'                => get_the_date('Y-m-d H:i:s', $invoice_id), // Creation date
        'status'              => $payment_status,                       // Payment status value
        'status_text'         => $status_text,                          // Formatted status text
        'type'                => $invoice_type,                         // Invoice type value
        'type_display'        => $invoice_type_display,                 // Translated invoice type
        'item_id'             => $item_id,                              // Related item ID
        'item_price'          => $price,                                // Item price
        'purchase_date'       => $purchase_date,                        // Purchase date
        'invoice_period'      => $invoice_period_saved,                 // Billing period
        'billing_type'        => $bill_type,                            // Billing type value
        'billing_type_display'=> $bill_type_display,                    // Translated billing type
        'author'              => get_post_field('post_author', $invoice_id), // Creator
        'property_id'         => $property_id,                          // Related property
    );
}

/**
 * Calculate invoice totals from an array of invoices.
 * 
 * Processes a collection of invoices to calculate totals based on status.
 * Different calculations are applied to reservation fees vs. other invoice types.
 * 
 * @param array $invoices Array of invoice details
 * @return array Totals array with confirmed and issued amounts
 */
function wpresidence_calculate_invoice_totals($invoices) {
    $total_confirmed = 0;
    $total_issued = 0;

    // Process each invoice to calculate totals by status
    foreach ($invoices as $invoice) {
        // Special handling for reservation fee invoices
        if (trim($invoice['type']) == 'Reservation fee' || 
            trim($invoice['type']) == esc_html__('Reservation fee', 'wpresidence-core')) {
            
            // Add to confirmed total if status is confirmed
            if ($invoice['status'] == 'confirmed') {
                $total_confirmed += $invoice['item_price'];
            }
            
            // Add to issued total if status is issued
            if ($invoice['status'] == 'issued') {
                $total_issued += $invoice['item_price'];
            }
        } else {
            // For all non-reservation invoices, add to confirmed total regardless of status
            // This is because these invoices represent completed transactions
            $total_confirmed += $invoice['item_price'];
        }
    }

    // Return the calculated totals
    return array(
        'total_confirmed' => $total_confirmed, // Total of confirmed invoices
        'total_issued'    => $total_issued     // Total of issued but not confirmed
    );
}

/**
 * Permission callback to validate access to a single invoice.
 *
 * This function ensures that the user requesting access to an invoice is either:
 * - An administrator
 * - The owner of the invoice
 * - The buyer associated with the invoice
 *
 * @param WP_REST_Request $request The REST API request object.
 * @return true|WP_Error True if the user has permission, otherwise a WP_Error object.
 */
function wpresidence_check_permissions_get_single_invoice($request) {
    // Authenticate via JWT token
    $userID = apply_filters('determine_current_user', null);
    if (!$userID) {
        return new WP_Error(
            'jwt_auth_failed',
            __('Invalid or missing JWT token.'),
            ['status' => 403]
        );
    }
    
    // Set WordPress context to the authenticated user
    wp_set_current_user($userID);

    // Get current user information
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;

    // Administrators can access any invoice
    if (current_user_can('administrator')) {
        return true;
    }

    // Get invoice ID from request URL parameter
    $invoice_id = intval($request['id']);
    
    // Validate invoice exists and is the correct post type
    $invoice = get_post($invoice_id);
    if (!$invoice || $invoice->post_type !== 'wpestate_invoice') {
        return new WP_Error(
            'invalid_invoice',
            __('Invalid invoice ID or incorrect post type.'),
            ['status' => 404]
        );
    }

    // Allow access if user is the invoice creator
    if ($invoice->post_author == $userID) {
        return true;
    }

    // Allow access if user is the buyer referenced in the invoice
    $buyer_id = get_post_meta($invoice_id, 'buyer_id', true);
    if ($buyer_id && $buyer_id == $userID) {
        return true;
    }

    // Deny access for all other cases
    return new WP_Error(
        'rest_forbidden',
        __('You do not have permission to access this invoice.'),
        ['status' => 403]
    );
}