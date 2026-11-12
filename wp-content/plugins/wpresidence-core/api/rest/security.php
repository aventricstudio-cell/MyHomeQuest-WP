
<?php 

function wpresidence_check_api_request_security(WP_REST_Request $request) {
    // 1. Check JWT token (already implemented)
    $user_id = apply_filters('determine_current_user', null);
    if (!$user_id) {
        return new WP_Error(
            'jwt_auth_failed',
            __('Invalid or missing JWT token.'),
            ['status' => 403]
        );
    }
    
    // 2. Check request timestamp to prevent replay attacks
    $timestamp = $request->get_header('X-WP-Timestamp');
    if (!$timestamp) {
        return new WP_Error(
            'missing_timestamp',
            __('Request timestamp is required.'),
            ['status' => 403]
        );
    }
    
    // Reject requests older than 5 minutes
    $now = time();
    if (abs($now - intval($timestamp)) > 300) {
        return new WP_Error(
            'expired_request',
            __('Request has expired. Please check your device clock.'),
            ['status' => 403]
        );
    }
    
    // 3. Optionally verify request signature
    $signature = $request->get_header('X-WP-Signature');
    if (defined('WP_API_REQUIRE_SIGNATURE') && WP_API_REQUIRE_SIGNATURE && !$signature) {
        return new WP_Error(
            'missing_signature',
            __('Request signature is required.'),
            ['status' => 403]
        );
    }
    
    if ($signature) {
        // Get the shared secret for this user
        $user_api_key = get_user_meta($user_id, 'wpresidence_api_key', true);
        
        // Get request body
        $body = $request->get_body();
        
        // Calculate expected signature: HMAC-SHA256 of timestamp + body using user's API key
        $expected_signature = hash_hmac('sha256', $timestamp . $body, $user_api_key);
        
        // Timing-attack safe comparison
        if (!hash_equals($expected_signature, $signature)) {
            return new WP_Error(
                'invalid_signature',
                __('Invalid request signature.'),
                ['status' => 403]
            );
        }
    }
    
    return true;
}

// Usage: Combine with existing permission checks
function wpresidence_property_modification_permission(WP_REST_Request $request) {
    // First check API request security
    $security_check = wpresidence_check_api_request_security($request);
    if (is_wp_error($security_check)) {
        return $security_check;
    }
    
    // Then perform existing permission checks
    $permission_check = wpresidence_check_permissions_for_property($request);
    if (is_wp_error($permission_check)) {
        return $permission_check;
    }
    
    return true;
}