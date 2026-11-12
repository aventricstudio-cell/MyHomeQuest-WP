<?php
/**
 * WPResidence API Utility Functions
 *
 * Common utility functions used across API endpoints for
 * data processing, field filtering, and response formatting.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

/**
 * Filter a response object to include only requested fields.
 * 
 * Handles nested data structures like meta fields and media,
 * with special case handling for common field types.
 *
 * @param array $data The complete data object to filter.
 * @param array|string $fields Fields to include in response (array or comma-separated string).
 * @return array Filtered data with only requested fields.
 */
function wpresidence_filter_api_response_fields($data, $fields) {
    // Handle string input (comma-separated fields)
    if (is_string($fields)) {
        $fields = array_map('trim', explode(',', $fields));
    }
    
    // Return original data if no fields specified or fields isn't an array
    if (empty($fields) || !is_array($fields)) {
        return $data;
    }
    
    $result = [];
    
    foreach ($fields as $field) {
        // Handle top-level fields
        if (isset($data[$field])) {
            $result[$field] = $data[$field];
        }
        // Handle meta fields (nested in 'meta' array)
        else if (isset($data['meta']) && isset($data['meta'][$field])) {
            $result[$field] = $data['meta'][$field];
        }
        // Special case for ID field (case insensitive)
        else if (strtolower($field) === 'id' && isset($data['ID'])) {
            $result['id'] = $data['ID'];
        }
        // Special case for featured_image
        else if ($field === 'featured_image' && isset($data['media']) && !empty($data['media'])) {
            // Handle media array that uses image IDs as keys
            $first_image_id = array_key_first($data['media']);
            if ($first_image_id && isset($data['media'][$first_image_id]['full'])) {
                $result[$field] = $data['media'][$first_image_id]['full'];
            } else {
                $result[$field] = '';
            }
        }
        // Special case for taxonomy terms - if requesting a taxonomy name directly
        else if (isset($data['terms']) && isset($data['terms'][$field])) {
            // Just return the term names as an array for simplicity
            $term_names = array_map(function($term) {
                return $term['name'];
            }, $data['terms'][$field]);
            $result[$field] = $term_names;
        }
    }
    
    // Always include ID field for reference if it exists
    if (!isset($result['id']) && !isset($result['ID']) && isset($data['ID'])) {
        $result['id'] = $data['ID'];
    }
    
    // If no matching fields were found, return the original data
    return !empty($result) ? $result : $data;
}

/**
 * Parse the fields parameter for API requests.
 * 
 * Accepts fields in various formats (string, array) and
 * normalizes to an array of field names.
 *
 * @param mixed $fields The fields parameter from the request.
 * @return array|null Array of field names or null if no valid fields.
 */
function wpresidence_parse_api_fields_param($fields) {
    // If already an array, just return it
    if (is_array($fields)) {
        return $fields;
    }
    
    // If string, parse it as comma-separated list
    if (is_string($fields) && !empty($fields)) {
        return array_map('trim', explode(',', $fields));
    }
    
    // If null or invalid, return null
    return null;
}

/**
 * Apply field filtering to a collection of items.
 * 
 * Helper function to filter multiple items with the same field set.
 *
 * @param array $items Array of data items to filter.
 * @param array|string $fields Fields to include (array or comma-separated string).
 * @return array Array of filtered items.
 */
function wpresidence_filter_api_collection($items, $fields) {
    if (empty($items) || empty($fields)) {
        return $items;
    }
    
    return array_map(function($item) use ($fields) {
        return wpresidence_filter_api_response_fields($item, $fields);
    }, $items);
}





/**
 * Process user registration during agent/agency/developer creation
 * 
 * Creates a WordPress user account associated with a property entity post.
 * Maps appropriate user roles based on the post type and syncs metadata
 * between the post and user account.
 *
 * @param int    $agent_id         The post ID of the entity (agent/agency/developer)
 * @param array  $user_registration Registration data containing username, password, email
 * @param string $post_type         The type of post ('estate_agent', 'estate_agency', 'estate_developer')
 * @param array  $input_data        Additional data to sync with the user account
 * 
 * @return mixed WP_Error on failure, user ID on success, void if validation fails
 */
function wpresidence_process_user_registration($agent_id, $user_registration, $post_type, $input_data){
    // Validate required registration fields
    if(
        !is_array($user_registration) ||
        !isset($user_registration['username']) ||
        !isset($user_registration['password']) ||
        !isset($user_registration['email'] )
         
    ){
        return;
    }

    // Define role mapping for different entity types
    $user_roles = array(
        'estate_agent'      =>  WPRESIDENCE_ROLE_AGENT,
        'estate_developer'  =>  WPRESIDENCE_ROLE_DEVELOPER,
        'estate_agency'     =>  WPRESIDENCE_ROLE_AGENCY
    );

    // Bail early if public registration is disabled
    if ( 'yes' !== wpresidence_get_option( 'wp_estate_allow_user_registration', 'yes' ) ) {
        return new WP_Error( 'registration_closed', esc_html__( 'User registration is disabled.', 'wpresidence' ) );
    }

    // Create the WordPress user
    $user_id = wp_create_user( $user_registration['username'], $user_registration['password'], $user_registration['email'] );

    if (is_wp_error($user_id)) {
        // Return the actual WP_Error from wp_create_user
        return $user_id;
    } else{
        // Assign the appropriate role based on post type
        $role_assigned = wpresidence_register_user_role($user_id, $user_roles[$post_type]);
        
        // Store connection between post and user
        update_post_meta($agent_id, 'user_meda_id', $user_id);

        // Sync entity data with user account based on post type
        if ($post_type === 'estate_agent') {
            wpresidence_sync_agent_with_user($post_id, $user_id, $input_data);
        } else if ($post_type === 'estate_agency') {
            wpresidence_sync_agency_with_user($post_id, $user_id, $input_data);
        }else if ($post_type === 'estate_developer') {
            wpresidence_sync_developer_with_user($post_id, $user_id, $input_data);
        }
    }
}



/**
 * Delete a WPResidence entity (agent, agency, or developer).
 * 
 * Permanently removes an entity listing and all its associated data.
 * Validates the entity existence before deletion and provides
 * appropriate error responses for failure cases.
 *
 * @param WP_REST_Request $request The REST API request containing the entity ID.
 * @param string $entity_type The type of entity ('agent', 'agency', or 'developer').
 * @return WP_REST_Response|WP_Error Success response or error details.
 */
function wpresidence_delete_entity(WP_REST_Request $request, $entity_type) {
    // Define entity-specific values
    $post_types = [
        'agent' => 'estate_agent',
        'agency' => 'estate_agency',
        'developer' => 'estate_developer'
    ];
    
    // Ensure valid entity type
    if (!isset($post_types[$entity_type])) {
        return new WP_Error(
            'rest_invalid_entity_type',
            __('Invalid entity type specified.'),
            ['status' => 400]
        );
    }
    
    // Parse and extract request parameters
    $input_data = wpresidence_parse_request_params($request);
    $entity_id = $input_data['id'];
    
    // Validate the entity exists and is the correct post type
    $entity = get_post($entity_id);
    if (!$entity || $entity->post_type !== $post_types[$entity_type]) {
        return new WP_Error(
            "rest_{$entity_type}_not_found",
            __(ucfirst($entity_type) . ' not found.'),
            ['status' => 404]
        );
    }
    
    // Before deletion, check if the entity is linked to a WordPress user
    $user_id = get_post_meta($entity_id, 'user_meda_id', true);
    
    // Get author ID (needed for agent-agency relationship)
   
    $author_id = get_post_field('post_author', $entity_id);
   
    
    // Get all media attachments associated with this entity
    $entity_attachments = wpresidence_get_entity_attachments($entity_id);
    
    // Attempt to delete the entity (true = force delete, skip trash)
    $result = wp_delete_post($entity_id, true);
    if (!$result) {
        return new WP_Error(
            'rest_cannot_delete',
            __('Failed to delete the ' . $entity_type . '.'),
            ['status' => 500]
        );
    }
    
    // Delete associated media attachments
    wpresidence_delete_attachments($entity_attachments);
    
    // If entity was linked to a user, delete the user and their media
    if ($user_id && is_numeric($user_id)) {
        // Delete user and their media
        wpresidence_delete_user_and_media(intval($user_id));
    }
    
    // If entity is an agent and belonged to an agency, update agency's agent list
    if ($entity_type === 'agent' && $author_id && is_numeric($author_id)) {
        wpresidence_update_agency_agent_list(intval($author_id), intval($user_id), 'remove');
    }
    
    // Return success response with confirmation details
    return rest_ensure_response([
        'status'    => 'success',
        $entity_type . '_id'  => $entity_id,
        'message'   => __(ucfirst($entity_type) . ' deleted successfully.'),
    ]);
}

/**
 * Delete a user and all media associated with them.
 *
 * @param int $user_id The ID of the WordPress user to delete.
 * @return void
 */
function wpresidence_delete_user_and_media($user_id) {
    // Make sure user exists
    $user = get_userdata($user_id);
    if (!$user) {
        return;
    }
    
    // Get all media attachments uploaded by this user
    $user_attachments = get_posts([
        'post_type' => 'attachment',
        'author' => $user_id,
        'posts_per_page' => -1,
        'post_status' => 'any',
        'fields' => 'ids'
    ]);
    
    // Delete user's media attachments
    wpresidence_delete_attachments($user_attachments);
    
    // Include the WordPress user administration functions
    require_once(ABSPATH . 'wp-admin/includes/user.php');

    // Delete the user (reassign posts to nobody)
    wp_delete_user($user_id);
}

/**
 * Get all media attachments associated with an entity.
 *
 * @param int $entity_id The post ID of the entity.
 * @return array Array of attachment IDs.
 */
function wpresidence_get_entity_attachments($entity_id) {
    // Get featured image/thumbnail
    $featured_image_id = get_post_thumbnail_id($entity_id);
    
    // Get attachments in post content and gallery
    $attachments = get_posts([
        'post_type' => 'attachment',
        'posts_per_page' => -1,
        'post_parent' => $entity_id,
        'fields' => 'ids'
    ]);
    
    // Add featured image if it exists and isn't already in the list
    if ($featured_image_id && !in_array($featured_image_id, $attachments)) {
        $attachments[] = $featured_image_id;
    }
    
    return $attachments;
}

/**
 * Delete an array of media attachments.
 *
 * @param array $attachment_ids Array of attachment post IDs.
 * @return void
 */
function wpresidence_delete_attachments($attachment_ids) {
    if (!is_array($attachment_ids) || empty($attachment_ids)) {
        return;
    }
    
    foreach ($attachment_ids as $attachment_id) {
        // Force delete the attachment (true = skip trash)
        wp_delete_attachment($attachment_id, true);
    }
}




/**
 * Recursively escape API response data for output.
 *
 * @param mixed $data The data to escape
 * @return mixed The escaped data
 */
function wpresidence_escape_api_response($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = wpresidence_escape_api_response($value);
        }
        return $data;
    } else if (is_object($data)) {
        foreach (get_object_vars($data) as $key => $value) {
            $data->$key = wpresidence_escape_api_response($value);
        }
        return $data;
    } else if (is_string($data)) {
        // Handle different data types appropriately
        if (filter_var($data, FILTER_VALIDATE_URL)) {
            return esc_url($data);
        } else if (preg_match('/<[^>]*>/', $data)) {
            // Contains HTML
            return wp_kses_post($data);
        } else {
            return esc_html($data);
        }
    } else {
        // Return non-string values as is (numbers, booleans, etc.)
        return $data;
    }
}
