<?php 
/**
 * WPResidence Invoice Creation Functions
 *
 * This file contains functions for creating invoices through the REST API.
 * It includes permission validation, invoice meta data generation for different
 * billing types (packages, properties, and bookings), and the main invoice creation function.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Permission callback to validate access for creating invoices.
 *
 * This function ensures that the user requesting to create an invoice
 * is logged in and has either administrator privileges or the owner role.
 * It first validates the JWT token, then checks user role permissions.
 *
 * @return true|WP_Error True if the user has permission, otherwise a WP_Error object.
 */
function wpresidence_check_permissions_create_invoice() {
    // Verify the JWT token to ensure authenticated access
    $userID = apply_filters('determine_current_user', null);
    if (!$userID) {
        return new WP_Error(
            'jwt_auth_failed',
            __('Invalid or missing JWT token.'),
            ['status' => 403]
        );
    }
    wp_set_current_user($userID);

    // Get current user object for role checking
    $current_user = wp_get_current_user();
    
    // Double check that user is logged in (belt and suspenders approach)
    if (!is_user_logged_in()) {
        return new WP_Error(
            'not_logged_in',
            __('You must be logged in to create invoices.'),
            ['status' => 403]
        );
    }

    // Check if user is administrator or has owner role - only these roles can create invoices
    if (current_user_can('administrator') ) {
        return true;
    }

    // Return error if user has neither required role
    return new WP_Error(
        'insufficient_permissions',
        __('Only administrators can create invoices.'),
        ['status' => 403]
    );
}

/**
 * Get invoice meta data for membership package-related billings.
 *
 * Extracts price information from a membership package post
 * to prepare the invoice meta data.
 *
 * @param int $item_id The membership package ID
 * @return array Meta data for the invoice containing price information
 */
function wpresidence_get_package_invoice_meta($item_id) {
    // Get the price from the package post meta
    $price = get_post_meta($item_id, 'pack_price', true);
    
    // Return a simple array with just the price for package invoices
    $meta_data = array(
        'item_price' => $price,   
    );
    return $meta_data;
}

/**
 * Get invoice meta data for property-related billings.
 *
 * Calculates pricing for different property-related billing types:
 * - Listing (basic submission)
 * - Upgrade to Featured
 * - Publish Listing with Featured (combined pricing)
 *
 * @param int    $property_id The property ID
 * @param string $billing_for Type of billing (Listing, Upgrade to Featured, Publish Listing with Featured)
 * @return array Meta data for the invoice with price calculations
 */
function wpresidence_get_property_invoice_meta($property_id, $billing_for) {
    // Get price settings from global options
    $price_submission = floatval(wpresidence_get_option('wp_estate_price_submission', ''));
    $price_featured_submission = floatval(wpresidence_get_option('wp_estate_price_featured_submission', ''));
    
    // Calculate price based on billing type
    $price = 0;
    switch ($billing_for) {
        case 'Listing':
            // Basic listing submission fee
            $price = $price_submission;
            break;
            
        case 'Upgrade to Featured':
            // Fee to upgrade an existing listing to featured status
            $price = $price_featured_submission;
            break;
            
        case 'Publish Listing with Featured':
            // Combined fee for listing submission with featured status
            $price = $price_submission + $price_featured_submission;
            break;
    }
    
    // Return price information for the invoice
    return array(
        'item_price' => $price, // The base price
        'to_be_paid' => $price, // Total amount to be paid (same as base for property listings)
    );
}

/**
 * Get invoice meta data for booking-related billings.
 *
 * This function calculates all the necessary meta data for a booking invoice including:
 * - Basic pricing details (base price, weekly/monthly rates)
 * - Additional fees (cleaning, city fees, security deposit)
 * - Service fees and taxes
 * - Early bird discounts
 * - Custom pricing if applicable
 *
 * @param int   $item_id         The booking ID
 * @param array $renting_details Array containing booking details (dates, guests, etc.)
 * @param array $manual_expenses Array of manual expenses to be added to the booking
 * @param string $invoice_status The status of the invoice (issued, confirmed, etc.)
 * 
 * @return array Meta data array containing all pricing and booking details
 */
function wpresidence_get_booking_invoice_meta($item_id, $renting_details, $manual_expenses, $invoice_status) {
    // Get booking basic information from post meta
    $booking_guests = get_post_meta($item_id, 'booking_guests', true);
    $wpresidence_book_from = get_post_meta($item_id, 'booking_from_date', true);
    $wpresidence_book_to = get_post_meta($item_id, 'booking_to_date', true);
    
    // Get and parse extra options selected for the booking
    $extra_options = esc_html(get_post_meta($item_id, 'extra_options', true));
    $extra_options_array = explode(',', $extra_options);

    // Get global settings for fees and payments from options
    $invoice_percent = floatval(wpresidence_get_option('wp_estate_book_down', '')); // Down payment percentage
    $invoice_percent_fixed_fee = floatval(wpresidence_get_option('wp_estate_book_down_fixed_fee', '')); // Fixed down payment
    $service_fee_fixed_fee = floatval(wpresidence_get_option('wp_estate_service_fee_fixed_fee', '')); // Fixed service fee
    $service_fee = floatval(wpresidence_get_option('wp_estate_service_fee', '')); // Percentage service fee

    // Get property details and associated costs
    $property_id = get_post_meta($item_id, 'booking_id', true); // Get the property ID from booking
    $rented_by = get_post_field('post_author', $item_id); // Get the user who made the booking
    
    // Get property pricing information from the property post meta
    $property_taxes = floatval(get_post_meta($property_id, 'property_taxes', true));
    $default_price = get_post_meta($property_id, 'property_price', true);
    $week_price = floatval(get_post_meta($property_id, 'property_price_per_week', true));
    $month_price = floatval(get_post_meta($property_id, 'property_price_per_month', true));

    // Get additional fees from property settings
    $cleaning_fee = floatval(get_post_meta($property_id, 'cleaning_fee', true));
    $city_fee = floatval(get_post_meta($property_id, 'city_fee', true));
    $cleaning_fee_per_day = floatval(get_post_meta($listing_id, 'cleaning_fee_per_day', true));
    $city_fee_per_day = floatval(get_post_meta($listing_id, 'city_fee_per_day', true));
    $city_fee_percent = floatval(get_post_meta($listing_id, 'city_fee_percent', true));

    // Get early bird discount settings from property
    $early_bird_percent = floatval(get_post_meta($listing_id, 'early_bird_percent', true));
    $early_bird_days = floatval(get_post_meta($listing_id, 'early_bird_days', true));

    // Calculate booking price using all components through the booking price function
    // This function handles calculations for nightly rates, weekly/monthly discounts,
    // custom seasonal pricing, extra guest fees, and additional options
    $booking_array = wpresidence_booking_price(
        $booking_guests,
        0, // invoice_id not yet created
        $listing_id,
        $wpresidence_book_from,
        $wpresidence_book_to,
        $item_id,
        $extra_options_array,
        $manual_expenses
    );

    // Build meta array with all calculated values for invoice
    $meta_array = array(
        'item_price' => $price, // Base price of the booking
        'to_be_paid' => $price, // Total amount to be paid
        'renting_details' => $renting_details, // Booking period and guest details
        'manual_expenses' => $manual_expenses, // Any additional expenses added manually

        // Payment and fee settings
        'invoice_percent' => $invoice_percent, // Deposit percentage
        'invoice_percent_fixed_fee' => $invoice_percent_fixed_fee, // Fixed deposit amount
        'service_fee_fixed_fee' => $service_fee_fixed_fee, // Fixed service fee
        'service_fee' => $service_fee, // Percentage service fee
        
        // Property information
        'for_property' => $property_id, // Property ID for the booking
        'rented_by' => $rented_by, // User ID who made the booking
        'prop_taxed' => $property_taxes, // Property tax rate
        'booking_taxes' => $booking_array['taxes'], // Calculated taxes for the booking
        
        // Price settings from property
        'default_price' => $default_price, // Standard nightly rate
        'week_price' => $week_price, // Weekly rate
        'month_price' => $month_price, // Monthly rate

        // Additional fees
        'cleaning_fee' => $cleaning_fee, // Fixed cleaning fee
        'city_fee' => $city_fee, // Fixed city/tourist tax
        'cleaning_fee_per_day' => $cleaning_fee_per_day, // Daily cleaning fee if applicable
        'city_fee_per_day' => $city_fee_per_day, // Daily city fee if applicable
        'city_fee_percent' => $city_fee_percent, // Percentage-based city fee if applicable
        
        // Security and early bird settings
        'security_deposit' => $booking_array['security_deposit'], // Security deposit amount
        'early_bird_percent' => $early_bird_percent, // Early booking discount percentage
        'early_bird_days' => $early_bird_days, // Days in advance for early booking discount

        // Calculated totals from booking price function
        'service_fee' => $booking_array['service_fee'], // Calculated service fee
        'youearned' => $booking_array['youearned'], // Amount earned by owner
        'depozit_to_be_paid' => $booking_array['deposit'], // Required deposit amount
        'balance' => $booking_array['balance'], // Remaining balance due
        'custom_price_array' => $booking_array['custom_price_array'], // Any custom pricing applied
    );

    // Add balance payment status if applicable
    // If there's a remaining balance, set status to waiting
    if ($booking_array['balance'] > 0) {
        $meta_array['invoice_status_full'] = 'waiting';
    }

    // Handle confirmed bookings - for confirmed status, reset deposit values
    if ($invoice_status == 'confirmed') {
        $meta_array['depozit_paid'] = 0;
        $meta_array['depozit_to_be_paid'] = 0;
    }

    return $meta_array;
}

/**
 * Create an invoice via REST API using the existing wpestate_insert_invoice function.
 *
 * @param WP_REST_Request $request The REST API request object containing:
 *     @type string billing_for     Type of billing (Listing, Package, etc.)
 *     @type int    type            Billing type (1 = One Time, 2 = Recurring)
 *     @type int    item_id         ID of booking/membership/property
 *     @type int    buyer_id        ID of the user being billed
 *     @type int    is_featured     Whether the listing is featured (0 or 1)
 *     @type int    is_upgrade      Whether this is an upgrade (0 or 1)
 *     @type string txn_id          Transaction ID (optional)
 * 
 * @return WP_REST_Response|WP_Error Response containing created invoice or error
 */
function wpresidence_create_invoice(WP_REST_Request $request) {
    // Get current logged-in user for tracking who created the invoice
    $current_user = wp_get_current_user();
    $current_user_id = $current_user->ID;

    // Get and validate parameters from the request
    $params = wpresidence_parse_request_params($request);
    
    // Validate required fields
    $required_fields = ['billing_for', 'type', 'item_id', 'buyer_id'];
    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (!isset($params[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        return new WP_Error(
            'missing_required_fields',
            sprintf(__('Missing required fields: %s'), implode(', ', $missing_fields)),
            ['status' => 400]
        );
    }

    // Extract and sanitize parameters
    $billing_for = sanitize_text_field($params['billing_for']);
    $type = intval($params['type']);
    $item_id = intval($params['item_id']);
    $buyer_id = intval($params['buyer_id']);
    $is_featured = isset($params['is_featured']) ? intval($params['is_featured']) : 0;
    $is_upgrade = isset($params['is_upgrade']) ? intval($params['is_upgrade']) : 0;
    $txn_id = isset($params['txn_id']) ? sanitize_text_field($params['txn_id']) : '';
    
    // Define allowed billing types
    $allowed_billing_types = array(
        'Listing',
        'Upgrade to Featured',
        'Publish Listing with Featured',
        'Package',
        'Reservation fee'
    );

    // Validate billing type
    if (!in_array($billing_for, $allowed_billing_types)) {
        return new WP_Error(
            'invalid_billing_type',
            sprintf(__('Invalid billing type. Allowed types are: %s'), implode(', ', $allowed_billing_types)),
            ['status' => 400]
        );
    }

    // Validate buyer exists
    if (!get_user_by('id', $buyer_id)) {
        return new WP_Error(
            'invalid_buyer',
            __('The specified buyer does not exist.'),
            ['status' => 400]
        );
    }

    // Validate item exists
    if (!get_post($item_id)) {
        return new WP_Error(
            'invalid_item',
            __('The specified item does not exist.'),
            ['status' => 404]
        );
    }
    
    // Current date for invoice
    $date = date('Y-m-d H:i:s');
    
    // Create invoice using the existing function
    $invoice_id = wpestate_insert_invoice(
        $billing_for,
        $type,
        $item_id,
        $date,
        $buyer_id,
        $is_featured,
        $is_upgrade,
        $txn_id
    );
    
    // Check if invoice was created successfully
    if (!$invoice_id) {
        return new WP_Error(
            'invoice_creation_failed',
            __('Failed to create invoice.'),
            ['status' => 500]
        );
    }
    
    // Get details of the newly created invoice
    $invoice_details = wpresidence_get_invoice_details($invoice_id);
    
    // Return success response
    return rest_ensure_response(array(
        'status'  => 'success',
        'message' => __('Invoice created successfully.'),
        'invoice' => $invoice_details
    ));
}