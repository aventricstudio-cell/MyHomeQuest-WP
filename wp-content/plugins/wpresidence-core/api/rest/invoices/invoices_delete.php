<?php
/**
 * Delete an invoice via REST API
 *
 * @param WP_REST_Request $request The REST request object containing:
 *     @type int id The invoice ID
 *
 * @return WP_REST_Response|WP_Error Response confirming deletion or error
 */
function wpresidence_delete_invoice(WP_REST_Request $request) {
    // Get invoice ID from the request URL parameter
    $invoice_id = intval($request['id']);
   
    // Verify the invoice exists and is the correct post type
    $invoice = get_post($invoice_id);
    if (!$invoice || $invoice->post_type !== 'wpestate_invoice') {
        return new WP_Error(
            'invalid_invoice',
            'Invalid invoice ID or incorrect post type',
            array('status' => 400)
        );
    }
   
    // Store invoice details before deletion for inclusion in the response
    $invoice_details = wpresidence_get_invoice_details($invoice_id);
   
    // Permanently delete the invoice post and all its metadata
    $result = wp_delete_post($invoice_id, true);
   
    // Check if deletion was successful
    if (!$result) {
        return new WP_Error(
            'deletion_failed',
            'Failed to delete the invoice',
            array('status' => 500)
        );
    }
   
    // Return success response with details of the deleted invoice
    return rest_ensure_response(array(
        'status' => 'success',
        'message' => 'Invoice deleted successfully',
        'invoice_id' => $invoice_id,
        'deleted_invoice' => $invoice_details
    ));
}