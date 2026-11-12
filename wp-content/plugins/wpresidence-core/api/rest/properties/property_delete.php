<?php
/**
 * WPResidence Property Deletion API Functions
 *
 * Functions for permanently removing properties via the REST API
 * with proper validation and response handling.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Delete a property.
 * 
 * Permanently removes a property listing and all its associated data.
 * Validates the property existence before deletion and provides
 * appropriate error responses for failure cases.
 *
 * @param WP_REST_Request $request The REST API request containing the property ID.
 * @return WP_REST_Response|WP_Error Success response or error details.
 */
function wpresidence_delete_property(WP_REST_Request $request) {
    // Parse and extract request parameters
    $input_data = wpresidence_parse_request_params($request);
    $property_id = $input_data['id'];
    
    // Validate the property exists and is the correct post type
    $property = get_post($property_id);
    if (!$property || $property->post_type !== 'estate_property') {
        return new WP_Error(
            'rest_property_not_found',
            __('Property not found.'),
            ['status' => 404]
        );
    }
    
    // Attempt to delete the property (true = force delete, skip trash)
    $result = wp_delete_post($property_id, true);
    if (!$result) {
        return new WP_Error(
            'rest_cannot_delete',
            __('Failed to delete the property.'),
            ['status' => 500]
        );
    }
    
    // Return success response with confirmation details
    return rest_ensure_response([
        'status'      => 'success',
        'property_id' => $property_id,
        'message'     => __('Property deleted successfully.'),
    ]);
}