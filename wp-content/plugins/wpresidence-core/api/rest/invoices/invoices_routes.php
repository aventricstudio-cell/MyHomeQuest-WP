<?php 
/**
 * WPResidence Invoice API Routes
 *
 * Registers REST API endpoints for invoice operations including retrieving,
 * creating, updating, and deleting invoices with appropriate permission checks.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

// Register routes for wpestate_invoice post type
add_action('rest_api_init', function () {
    /**
     * Endpoint: GET/POST /wpresidence/v1/invoices
     * Description: Retrieve all invoices with filtering and pagination
     * Parameters: Various filter options in POST body
     * Permission: Administrator access required
     */
    register_rest_route('wpresidence/v1', '/invoices', [
        'methods' => 'POST',  // Using POST since we're sending JSON body for filters
        'callback' => 'wpresidence_get_all_invoices',
        'permission_callback' => 'wpresidence_check_permissions_all_invoices',
    ]);

    /**
     * Endpoint: GET /wpresidence/v1/invoice/{id}
     * Description: Retrieve a single invoice by ID
     * Parameters: invoice ID in URL
     * Permission: Administrator, invoice owner, or buyer
     */
    register_rest_route('wpresidence/v1', '/invoice/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'wpresidence_get_single_invoice',
        'permission_callback' => 'wpresidence_check_permissions_get_single_invoice',
        'args' => [
            'id' => [
                'required' => true,
                'validate_callback' => function($param) {
                    return is_numeric($param);  // Ensure ID is numeric
                }
            ]
        ]
    ]);

    /**
     * Endpoint: GET /wpresidence/v1/owner/{user_id}/invoices
     * Description: Retrieve invoices for a property owner
     * Parameters: owner user ID in URL, filter options as query params
     * Permission: Administrator or the owner themselves
     */
    register_rest_route('wpresidence/v1', '/my-invoices', [
        'methods' => 'GET',
        'callback' => 'wpresidence_get_my_invoices',
        'permission_callback' => 'wpresidence_check_auth_token',
    ]);
    
    

    /**
     * Endpoint: POST /wpresidence/v1/invoice/add
     * Description: Create a new invoice
     * Parameters: Invoice data in POST body
     * Permission: Administrator or property owner
     */
    register_rest_route('wpresidence/v1', '/invoice/add', [
        'methods' => 'POST',
        'callback' => 'wpresidence_create_invoice',
        'permission_callback' => 'wpresidence_check_permissions_create_invoice',
    ]);

    /**
     * Endpoint: PUT /wpresidence/v1/invoice/edit/{id}
     * Description: Update an existing invoice
     * Parameters: invoice ID in URL, update data in PUT body
     * Permission: Same as create invoice (admin or owner)
     */
    register_rest_route('wpresidence/v1', '/invoice/edit/(?P<id>\d+)', [
        'methods' => 'PUT',
        'callback' => 'wpresidence_update_invoice',
        'permission_callback' => 'wpresidence_check_permissions_create_invoice',
        'args' => [
            'id' => [
                'required' => true,
                'validate_callback' => function($param) {
                    return is_numeric($param);  // Ensure ID is numeric
                }
            ]
        ]
    ]);

    /**
     * Endpoint: DELETE /wpresidence/v1/invoice/delete/{id}
     * Description: Delete an invoice
     * Parameters: invoice ID in URL
     * Permission: Administrator or invoice owner
     */
    register_rest_route('wpresidence/v1', '/invoice/delete/(?P<id>\d+)', [
        'methods' => 'DELETE',
        'callback' => 'wpresidence_delete_invoice',
        'permission_callback' => 'wpresidence_check_permissions_create_invoice',
        'args' => [
            'id' => [
                'required' => true,
                'validate_callback' => function($param) {
                    return is_numeric($param);  // Ensure ID is numeric
                }
            ]
        ]
    ]);
});