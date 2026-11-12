<?php 
/**
 * Update invoice meta fields via REST API.
 *
 * @param WP_REST_Request $request The REST request object containing:
 *     @type int    id              The invoice ID
 *     @type int    pay_status      Payment status (0 = Not Paid, 1 = Paid)
 *     @type string invoice_type    Invoice type
 *     @type string biling_type     Billing type
 *     @type float  item_price      Price amount
 * 
 * @return WP_REST_Response|WP_Error Response confirming update or error
 */
function wpresidence_update_invoice(WP_REST_Request $request) {
    // Get and validate invoice ID from URL parameter
    $invoice_id = intval($request['id']);
    
    // Parse and validate parameters from request body
    $params = wpresidence_parse_request_params($request);
    
    // Verify invoice exists and is the correct post type
    $invoice = get_post($invoice_id);
    if (!$invoice || $invoice->post_type !== 'wpestate_invoice') {
        return new WP_Error(
            'invalid_invoice',
            'Invalid invoice ID or incorrect post type',
            array('status' => 400)
        );
    }

    // Get current values to track changes
    $current_pay_status = get_post_meta($invoice_id, 'pay_status', true);
    $current_invoice_type = get_post_meta($invoice_id, 'invoice_type', true);
    $current_biling_type = get_post_meta($invoice_id, 'biling_type', true);
    $current_item_price = floatval(get_post_meta($invoice_id, 'item_price', true));
    
    // Track changes for audit and response purposes
    $changes = array();
    
    // Process payment status update if provided
    if (isset($params['pay_status'])) {
        $new_status = intval($params['pay_status']);
        
        // Validate status is either 0 or 1
        if ($new_status !== 0 && $new_status !== 1) {
            return new WP_Error(
                'invalid_status',
                'Invalid pay_status. Allowed values: 0 (Not Paid), 1 (Paid)',
                array('status' => 400)
            );
        }
        
        // Only update if value has actually changed
        if ($new_status !== $current_pay_status) {
            update_post_meta($invoice_id, 'pay_status', $new_status);
            
            // Record change for response
            $changes['pay_status'] = array(
                'from' => $current_pay_status,
                'to' => $new_status
            );
        }
    }
    
    // Process invoice type update if provided
    if (isset($params['invoice_type'])) {
        $new_invoice_type = sanitize_text_field($params['invoice_type']);
        
        // Define allowed invoice types
        $allowed_types = array(
            'Listing', 
            'Upgrade to Featured', 
            'Publish Listing with Featured', 
            'Package', 
            'Reservation fee'
        );
        
        // Validate invoice type
        if (!in_array($new_invoice_type, $allowed_types)) {
            return new WP_Error(
                'invalid_invoice_type',
                'Invalid invoice_type. Allowed values: ' . implode(', ', $allowed_types),
                array('status' => 400)
            );
        }
        
        // Only update if value has actually changed
        if ($new_invoice_type !== $current_invoice_type) {
            update_post_meta($invoice_id, 'invoice_type', $new_invoice_type);
            
            // Record change for response
            $changes['invoice_type'] = array(
                'from' => $current_invoice_type,
                'to' => $new_invoice_type
            );
        }
    }
    
    // Process billing type update if provided
    if (isset($params['biling_type'])) {
        $new_biling_type = sanitize_text_field($params['biling_type']);
        
        // Define allowed billing types
        $allowed_biling_types = array('One Time', 'Recurring');
        
        // Validate billing type
        if (!in_array($new_biling_type, $allowed_biling_types)) {
            return new WP_Error(
                'invalid_biling_type',
                'Invalid biling_type. Allowed values: ' . implode(', ', $allowed_biling_types),
                array('status' => 400)
            );
        }
        
        // Only update if value has actually changed
        if ($new_biling_type !== $current_biling_type) {
            update_post_meta($invoice_id, 'biling_type', $new_biling_type);
            
            // Record change for response
            $changes['biling_type'] = array(
                'from' => $current_biling_type,
                'to' => $new_biling_type
            );
        }
    }
    
    // Process item price update if provided
    if (isset($params['item_price'])) {
        $new_item_price = floatval($params['item_price']);
        
        // Validate price is not negative
        if ($new_item_price < 0) {
            return new WP_Error(
                'invalid_price',
                'Item price cannot be negative',
                array('status' => 400)
            );
        }
        
        // Only update if value has actually changed
        if ($new_item_price !== $current_item_price) {
            update_post_meta($invoice_id, 'item_price', $new_item_price);
            
            // Record change for response
            $changes['item_price'] = array(
                'from' => $current_item_price,
                'to' => $new_item_price
            );
        }
    }
    
    // Process author update if provided
    if (isset($params['author_id'])) {
        $new_author_id = intval($params['author_id']);
        $current_author_id = intval($invoice->post_author);
        
        // Validate that the user exists
        if (!get_user_by('id', $new_author_id)) {
            return new WP_Error(
                'invalid_author',
                'The specified author does not exist',
                array('status' => 400)
            );
        }
        
        // Only update if value has actually changed
        if ($new_author_id !== $current_author_id) {
            // Update the post author
            $update_post = array(
                'ID' => $invoice_id,
                'post_author' => $new_author_id
            );
            
            wp_update_post($update_post);
            
            // Record change for response
            $changes['author_id'] = array(
                'from' => $current_author_id,
                'to' => $new_author_id
            );
        }
    }
    
    // If no fields were changed, return current values
    if (empty($changes)) {
        return rest_ensure_response(array(
            'status' => 'success',
            'message' => 'No changes were necessary',
            'invoice_id' => $invoice_id,
            'current_values' => array(
                'pay_status' => $current_pay_status,
                'invoice_type' => $current_invoice_type,
                'biling_type' => $current_biling_type,
                'item_price' => $current_item_price
            )
        ));
    }

    // Get complete updated invoice details for response
    $invoice_details = wpresidence_get_invoice_details($invoice_id);
    
    // Return success response with detailed change information
    return rest_ensure_response(array(
        'status' => 'success',
        'message' => 'Invoice updated successfully',
        'invoice_id' => $invoice_id,
        'changes' => $changes,
        'invoice' => $invoice_details
    ));
}