<?php
/**
 * WPResidence API Functions for Property Management
 *
 * Core functions for handling property filtering, data processing, 
 * and response formatting for the WPResidence REST API.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Retrieve filter settings for taxonomies and meta fields.
 * 
 * Defines comparison operators for different property field types
 * to be used in query filtering.
 *
 * @return array Filter settings for property taxonomies and meta fields.
 */
function wpresidence_get_filter_settings() {
    return [
        'property_price' => '=',
        'property_city' => 'LIKE',
        'property_features' => 'IN',
    ];
}

/**
 * Generate basic response for a property.
 * 
 * Creates a simplified property data array with only essential information.
 * Used for list views and summaries.
 *
 * @param int $postID The ID of the property.
 * @return array Basic property data with ID, title, price and location.
 */
function wpestate_generate_basic_response($postID) {
    $cached_data = wpestate_api_get_cached_post_data($postID, 'estate_property');

    return [
        'id' => $cached_data['ID'],
        'title' => $cached_data['title'],
        'price' => $cached_data['meta']['property_price'] ?? '',
        'location' => $cached_data['meta']['property_address'] ?? ''
    ];
}

/**
 * Determine and retrieve sanitized parameters from a REST request or internal call.
 * 
 * Extracts and sanitizes request parameters regardless of source format.
 * Special handling for HTML content like virtual tours.
 *
 * @param WP_REST_Request|array $request REST API request or internal array.
 * @return array Sanitized parameters.
 */
function wpresidence_parse_request_params($request) {
    // Extract parameters based on request type
    if ($request instanceof WP_REST_Request) {
        $params = $request->get_json_params();
        if (empty($params)) {
            $params = $request->get_params();
        }
    } else {
        $params = $request;
    }

    // Special handling for virtual tour HTML content
    $virtual_tour = null;
    if (!empty($params['embed_virtual_tour'])) {
        $allowed_html = [
            'iframe' => [
                'src'             => ['pattern' => '#^https:\/\/.*$#'],
                'width'           => ['pattern' => '#^\d+$|(^auto$)|(^\d+%$)|(^\d+px$)#'],
                'height'          => ['pattern' => '#^\d+$|(^auto$)|(^\d+%$)|(^\d+px$)#'],
                'frameborder'     => ['pattern' => '#^[0-1]$#'],
                'style'           => ['pattern' => '#^([\w-]+:\s?[^;]+;(\s+)?)*$#'],
                'allow'           => ['pattern' => '#^[\w-]+(;\s*[\w-]+)*$#'],
                'allowfullscreen' => ['pattern' => '#^(true|false|"true"|"false"|0|1|"")?$#'],
                'scrolling'       => ['pattern' => '#^(yes|no|auto)$#'],
            ],
        ];
        $virtual_tour = wp_kses($params['embed_virtual_tour'], $allowed_html);
    }

    // Type-specific sanitization
    $return_array = wpresidence_sanitize_params_by_type($params);
    
    // Re-add the virtual tour HTML if it exists
    if ($virtual_tour) {
        $return_array['embed_virtual_tour'] = $virtual_tour;
    }
    
    return $return_array;
}





/**
* Recursively sanitize parameters based on their data types.
* 
* Applies type-specific sanitization to maintain data integrity while ensuring security:
* - Arrays: Recursively sanitizes each element
* - Numbers: Preserves numeric format including decimals
* - Booleans: Preserves boolean type
* - Null values: Preserves null state
* - Dates: Handles date strings appropriately
* - Other strings: Applies text field sanitization
*
* @param mixed $params Input parameters (scalar or array)
* @return mixed Sanitized parameters with preserved data types
*/
function wpresidence_sanitize_params_by_type($params) {
    if (!is_array($params)) {
        return sanitize_text_field($params);
    }
    
    $sanitized = [];
    foreach ($params as $key => $value) {
        $safe_key = sanitize_key($key);
        
        if (is_array($value)) {
            $sanitized[$safe_key] = wpresidence_sanitize_params_by_type($value);
        } else if ($safe_key === 'property_description') {
            $sanitized[$safe_key] = wp_kses_post($value);
        } else if (is_numeric($value)) {
            $sanitized[$safe_key] = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        } else if (is_bool($value)) {
            $sanitized[$safe_key] = (bool)$value;
        } else if ($value === null) {
            $sanitized[$safe_key] = null;
        } else if (strtotime($value) !== false) {
            // Date handling
            $sanitized[$safe_key] = sanitize_text_field($value);
        } else {
            $sanitized[$safe_key] = sanitize_text_field($value);
        }
    }
    
    return $sanitized;
}




/**
 * Recursively sanitize array values.
 * 
 * Applies a sanitization callback to each value in a nested array structure.
 *
 * @param callable $callback Callback function to apply (e.g., 'sanitize_text_field').
 * @param mixed $value The value to sanitize (array or scalar).
 * @return mixed Sanitized value.
 */
function array_map_recursive($callback, $value) {
    if (is_array($value)) {
        return array_map(function ($item) use ($callback) {
            return array_map_recursive($callback, $item);
        }, $value);
    }
    return call_user_func($callback, $value);
}

/**
 * Retrieve all properties with specified filters and pagination.
 * 
 * Main function for property listing API endpoint with secure parameter handling.
 * Implements proper sanitization and validation for meta queries.
 *
 * @param WP_REST_Request $request REST API request containing filter parameters.
 * @return WP_REST_Response Response containing filtered property data.
 */
function wpresidence_get_all_properties(WP_REST_Request $request) {
    // Parse parameters
    $params = wpresidence_parse_request_params($request);

    // Set defaults and extract main parameters
    $paged = isset($params['page']) ? absint($params['page']) : 1;
    $posts_per_page = isset($params['posts_per_page']) ? absint($params['posts_per_page']) : 10;
    $order = isset($params['order']) ? absint($params['order']) : 0;
    $response_type = isset($params['response_type']) && in_array($params['response_type'], ['basic', 'full'], true) ? $params['response_type'] : 'basic';
    $userID = isset($params['userID']) ? absint($params['userID']) : null;
    $fields = wpresidence_parse_fields_param($params['fields'] ?? null);

    // Initialize query arrays
    $meta_input = [];
    $taxonomy_input = [];

    // Process taxonomy parameters (sanitized elsewhere)
    if (isset($params['taxonomies']) && is_array($params['taxonomies'])) {
        $taxonomy_input = $params['taxonomies'];
    }

    // Process meta parameters with secure handling
    if (isset($params['meta']) && is_array($params['meta'])) {
        foreach ($params['meta'] as $key => $meta_data) {
            // Skip if meta_data is not an array
            if (!is_array($meta_data)) {
                continue;
            }

            // Sanitize meta key (only allow alphanumeric, dash and underscore)
            $safe_key = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
            if (empty($safe_key) || $safe_key !== $key) {
                continue; // Skip if key contained invalid characters
            }

            // Valid compare operators with strong typing
            $valid_compare = [
                '=' => '=', 
                '!=' => '!=', 
                '>' => '>', 
                '>=' => '>=', 
                '<' => '<', 
                '<=' => '<=', 
                'LIKE' => 'LIKE', 
                'NOT LIKE' => 'NOT LIKE', 
                'IN' => 'IN', 
                'NOT IN' => 'NOT IN', 
                'BETWEEN' => 'BETWEEN', 
                'NOT BETWEEN' => 'NOT BETWEEN', 
                'EXISTS' => 'EXISTS', 
                'NOT EXISTS' => 'NOT EXISTS'
            ];
            
            // Valid data types with strong typing
            $valid_types = [
                'NUMERIC' => 'NUMERIC', 
                'BINARY' => 'BINARY', 
                'CHAR' => 'CHAR', 
                'DATE' => 'DATE', 
                'DATETIME' => 'DATETIME', 
                'DECIMAL' => 'DECIMAL', 
                'SIGNED' => 'SIGNED', 
                'TIME' => 'TIME', 
                'UNSIGNED' => 'UNSIGNED'
            ];

            // Initialize meta query with safe defaults
            $meta_query = [
                'key' => $safe_key,
                'compare' => '=',
                'type' => 'CHAR'
            ];

            // Validate and sanitize value based on specified type
            if (isset($meta_data['value'])) {
                $specified_type = isset($meta_data['type']) && 
                                 isset($valid_types[$meta_data['type']]) ? 
                                 $valid_types[$meta_data['type']] : 'CHAR';
                
                // Type-specific sanitization
                if (is_array($meta_data['value'])) {
                    $compare = isset($meta_data['compare']) ? $meta_data['compare'] : '=';
                    // Array values need element-by-element sanitization
                    $sanitized_values = [];
                    foreach ($meta_data['value'] as $val) {
                        $sanitized_values[] = wpresidence_sanitize_meta_value($val, $specified_type,$compare);
                    }
                    $meta_query['value'] = $sanitized_values;
                } else {
                    $meta_query['value'] = wpresidence_sanitize_meta_value($meta_data['value'], $specified_type,$compare);
                }
            }

            // Validate and set compare operator
            if (isset($meta_data['compare']) && isset($valid_compare[$meta_data['compare']])) {
                $meta_query['compare'] = $valid_compare[$meta_data['compare']];
            }

            // Validate and set type
            if (isset($meta_data['type']) && isset($valid_types[$meta_data['type']])) {
                $meta_query['type'] = $valid_types[$meta_data['type']];
            }

            // Additional validation for specific compare operators
            if (in_array($meta_query['compare'], ['IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'], true)) {
                // Ensure value is an array for these compare types
                if (!isset($meta_query['value']) || !is_array($meta_query['value'])) {
                    // Default to empty array if not set or not an array
                    $meta_query['value'] = [];
                }
            }

            // Handle EXISTS and NOT EXISTS (remove value)
            if (in_array($meta_query['compare'], ['EXISTS', 'NOT EXISTS'], true)) {
                unset($meta_query['value']);
            }

            $meta_input[] = $meta_query;
        }
    }

    $post_type = 'estate_property';

    // Call the custom query function with API type
    $query_result = wpestate_api_custom_query(
        $post_type,
        $paged,
        $posts_per_page,
        $meta_input,
        $taxonomy_input,
        $order,
        $userID,
        'api'
    );

    // Ensure we have valid results
    if (!$query_result || !isset($query_result['post_ids'])) {
        return new WP_REST_Response(
            [
                'status' => 'error',
                'message' => 'No query results found',
            ],
            404
        );
    }

    // Process results based on response_type
    $properties = [];
    foreach ($query_result['post_ids'] as $postID) {
        if ($response_type === 'basic') {
            $properties[] = wpestate_generate_basic_response($postID);
        } else {
            // Full response
            $cached_data = wpestate_api_get_cached_post_data($postID, $post_type);
            $properties[] = $cached_data;
        }
    }

    // If specific fields are requested, filter the results
    if ($fields) {
        $properties = array_map(function ($property) use ($fields) {
            return filter_response_fields($property, $fields);
        }, $properties);
    }

    //escape answer
    $properties = wpresidence_escape_api_response($properties);

    // Return formatted response
    return new WP_REST_Response(
        [
            'status' => 'success',
            'query_args' => $query_result['args'],
            'data' => $properties,
            'total' => $query_result['total_posts'],
            'pages' => $query_result['max_num_pages']
        ],
        200
    );
}

/**
 * Sanitize meta value based on specified type.
 * 
 * Applies appropriate sanitization based on the declared data type
 * to prevent SQL injection and ensure data integrity.
 *
 * @param mixed $value The value to sanitize
 * @param string $type The declared data type (NUMERIC, CHAR, etc.)
 * @return mixed Sanitized value
 */
function wpresidence_sanitize_meta_value($value, $type, $compare = '=') {
    // Handle array values for IN, NOT IN, BETWEEN, NOT BETWEEN
    if (is_array($value)) {
        $sanitized = [];
        foreach ($value as $val) {
            $sanitized[] = wpresidence_sanitize_meta_value($val, $type, $compare);
        }
        return $sanitized;
    }
    
    switch ($type) {
        case 'NUMERIC':
        case 'SIGNED':
        case 'UNSIGNED':
        case 'DECIMAL':
            // For numeric types, ensure it's a valid number
            return is_numeric($value) ? floatval($value) : 0;
           
        case 'DATE':
        case 'DATETIME':
        case 'TIME':
            // For date types, ensure it's a valid date string
            return preg_match('/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$/', $value) ? $value : '';
           
        case 'BINARY':
            // For binary data, ensure it's a valid string
            return sanitize_text_field($value);
           
        case 'CHAR':
        default:
            // Special handling for LIKE comparisons
            if (in_array($compare, ['LIKE', 'NOT LIKE'])) {
                return esc_sql(esc_like($value));
            }
            // Default to text field sanitization
            return sanitize_text_field($value);
    }
}

/**
 * Parse and sanitize the 'fields' parameter.
 * 
 * Converts a comma-separated string of field names into an array of sanitized field names.
 *
 * @param string|null $fields Comma-separated list of fields or null.
 * @return array|null Array of sanitized fields or null.
 */
function wpresidence_parse_fields_param($fields) {
    if (!$fields) {
        return null;
    }
    return array_map('sanitize_text_field', array_map('trim', explode(',', $fields)));
}

/**
 * Retrieve a single property by its ID.
 * 
 * Fetches complete or basic property data for a specific property.
 * Supports field filtering to return only requested data.
 *
 * @param WP_REST_Request $request REST API request containing the property ID.
 * @return WP_REST_Response|WP_Error Response with property data or error if not found.
 */
function wpresidence_get_single_property(WP_REST_Request $request) {
    $params = wpresidence_parse_request_params($request);

    // Extract and validate essential parameters
    $id = $params['id'] ?? null;
    $response_type = isset($params['response_type']) && $params['response_type'] === 'basic' ? 'basic' : 'full';

    // Parse fields parameter
    $fields = $request->get_param('fields');
    $fields = $fields ? array_map('trim', explode(',', $fields)) : null;
    $fields = wpresidence_parse_fields_param($params['fields'] ?? null);

    // Verify property exists
    $post = get_post($id);
    if (!$post || $post->post_type !== 'estate_property') {
        return new WP_Error('rest_property_not_found', __('Property not found'), ['status' => 404]);
    }

    // Get cached property data
    $cached_data = wpestate_api_get_cached_post_data($id, 'estate_property');

    // Generate appropriate response based on type
    if ($response_type === 'basic') {
        $response = wpestate_generate_basic_response($id);
    } else {
        $response = $cached_data;
    }

    // Filter response based on requested fields
    if ($fields && is_array($fields)) {
        $response = filter_response_fields($cached_data, $fields);
    }

    return rest_ensure_response($response);
}

/**
 * Check if the current user has permissions to perform an action.
 * 
 * Verifies user role-based permissions for property management actions.
 *
 * @param WP_REST_Request $request REST API request object.
 * @return bool|WP_Error True if the user has permission, otherwise a WP_Error.
 */
function wpresidence_check_permissions(WP_REST_Request $request) {
    $user = wp_get_current_user();
    if (in_array('owner', $user->roles) || current_user_can('administrator')) {
        return true;
    }
    return new WP_Error('rest_forbidden', __('You do not have permission to perform this action'), ['status' => 403]);
}

/**
 * Filter the response based on requested fields.
 * 
 * Processes an array of field names (including nested fields using dot notation)
 * and returns only the requested data while preserving the structure.
 *
 * @param array $data The original response data.
 * @param array $fields The requested fields.
 * @return array Filtered data matching the requested fields.
 */
function filter_response_fields($data, $fields) {
    $filtered = [];

    foreach ($fields as $field) {
        // Handle nested fields with dot notation (e.g., 'meta.property_price')
        $keys = explode('.', $field);
        $current = &$filtered;
        $source = $data;

        // Navigate through nested keys
        foreach ($keys as $key) {
            if (isset($source[$key])) {
                if (!isset($current[$key])) {
                    $current[$key] = [];
                }
                $current = &$current[$key];
                $source = $source[$key];
            } else {
                // If the key does not exist in the source, skip
                $current = null;
                break;
            }
        }

        // Set the value if we found a match
        if ($current !== null) {
            $current = $source;
        }
    }

    return $filtered;
}