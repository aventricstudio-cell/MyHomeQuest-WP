<?php

/**
 * Generates HTML markup for CRM detail fields based on a configuration array.
 * 
 * This function is part of the WP Residence theme's CRM system. It takes a source array
 * of field configurations and generates corresponding HTML form elements. Supports
 * different field types including input, textarea, post_type, taxonomy, and content fields.
 *
 * @since 1.0.0
 * 
 * @param array  $source_array Array of field configurations where each item contains:
 *                            - type: string (input|textarea|select|post_type|taxonomy|content)
 *                            - length: string optional (full) for full-width fields
 *                            - label: string Field label
 *                            Additional parameters vary by field type
 * @param string $edit_id     Optional. Post ID when editing an existing entry. Default empty.
 * @return string Generated HTML markup for the form fields
 */
if (!function_exists('wpestate_crm_show_details')) {
    function wpestate_crm_show_details($source_array, $edit_id = '') {
        // Initialize output variable
        $return = '';
        
        // Loop through each field configuration
        foreach ($source_array as $key => $item) {
            // Start field container div
            $return .= '<div class="half-content ';
            
            // Determine field width class
            if (isset($item['length']) && $item['length'] === 'full') {
                $return .= 'col-md-12 ';
            } else {
                $return .= 'col-md-6 ';
            }
            $return .= '">';
            
            // Generate field markup based on type
            switch ($item['type']) {
                case 'input':
                    $return .= wpestate_crm_return_input_metabox($key, $item, $edit_id);
                    break;
                    
                case 'textarea':
                    $return .= wpestate_crm_return_textarea_metabox($key, $item, $edit_id);
                    break;
                    
                case 'select':
                    // Placeholder for select field implementation
                    break;
                    
                case 'post_type':
                    $return .= wpestate_crm_return_post_type_items($key, $item, $edit_id);
                    break;
                    
                case 'taxonomy':
                    $return .= wpestate_crm_return_taxonomy_metabox($key, $item, $edit_id);
                    break;
                    
                case 'content':
                    if (trim($edit_id) !== '') {
                        $return .= sprintf(
                            '<label style="margin-top:10px;" for="content">%s</label>%s',
                            esc_html($item['label']),
                            wp_kses_post(get_post_field('post_content', $edit_id))
                        );
                    }
                    break;
            }
            
            // Close field container div
            $return .= '</div>';
        }
        
        return $return;
    }
}


/**
 * Generate a select dropdown for post type items in the CRM system.
 *
 * Creates an HTML select element populated with posts of a specified post type.
 * Special handling for CRM handlers (agents) with administrator restrictions.
 * 
 * @since 1.0.0
 *
 * @param string $key     The meta key for storing the selected value
 * @param array  $item    Configuration array containing:
 *                       - source: string The post type to query
 *                       - label: string The field label
 * @param string $edit_id Optional. Post ID when editing an existing entry. Default empty.
 * @return string HTML markup for the select field
 */
if (!function_exists('wpestate_crm_return_post_type_items')) {
    function wpestate_crm_return_post_type_items($key, $item, $edit_id = '') {
        global $post;
        
        // Validate inputs
        if (empty($key) || empty($item['source'])) {
            return '';
        }
        
        // Setup query arguments
        $args = array(
            'post_type'      => ($item['source']),
            'posts_per_page' => -1,
            'post_status'    => array('any')
        );
         
   
        // Special handling for CRM handlers (agents)
        if ($key === 'crm_handler') {
            $current_user = wp_get_current_user();
            if (!current_user_can('administrator')) {
                $args['author'] = $current_user->ID;
            }
        }
        
        // Get current value and available posts
        $agent_id = intval(get_post_meta($edit_id, 'crm_handler', true));
        $agent_selection = get_posts($args);
        
        // Build select options
        $select_options = array();
        $select_options[] = '<option value=""></option>';
        
        foreach ($agent_selection as $agent) {
            $selected = selected($agent_id, $agent->ID, false);
            $select_options[] = sprintf(
                '<option value="%d"%s>%s</option>',
                $agent->ID,
                $selected,
                esc_html($agent->post_title)
            );
        }
        
        // Build HTML markup
        $markup = array(
            sprintf(
                '<label for="%s">%s</label>',
                esc_attr($key),
                esc_html($item['label'])
            ),
            sprintf(
                '<select id="%1$s" class="form-control" name="%1$s">',
                esc_attr($key)
            ),
            implode("\n", $select_options),
            '</select>'
        );
        
        return implode("\n", $markup);
    }
}




/**
 * Generate an input field for CRM metabox.
 *
 * Creates an HTML input element or readonly text based on configuration.
 * Special handling for lead permalink fields.
 *
 * @since 1.0.0
 *
 * @param string $key     Meta key for the field
 * @param array  $item    Configuration array containing:
 *                       - label: string Field label
 *                       - editable: string Optional. Set 'false' for readonly
 * @param string $edit_id Optional. Post ID when editing. Default empty.
 * @return string HTML markup for the input field
 */
if (!function_exists('wpestate_crm_return_input_metabox')) {
    function wpestate_crm_return_input_metabox($key, $item, $edit_id = '') {
        global $post;
        
        // Get field value
        $value = '';
        if (intval($edit_id) !== 0) {
            $value = get_post_meta($edit_id, $key, true);
        } else {
            $value = get_post_meta($post->ID, $key, true);
        }
        
        // Build HTML markup
        $markup = array(
            sprintf('<label for="%s">%s</label>', esc_attr($key), esc_html($item['label']))
        );
        
        // Handle readonly fields
        if (isset($item['editable']) && $item['editable'] === 'false') {
            if ($key === 'crm_lead_permalink') {
                if (empty($value)) {
                    $markup[] = esc_html__('added manually', 'wpestate-crm');
                } else {
                    $markup[] = sprintf(
                        '<a href="%1$s" target="_blank">%1$s</a>',
                        esc_url($value)
                    );
                }
            } else {
                $markup[] = esc_html($value);
            }
        } else {
            // Regular input field
            $markup[] = sprintf(
                '<input type="text" id="%1$s" class="form-control" name="%1$s" value="%2$s">',
                esc_attr($key),
                esc_attr($value)
            );
        }
        
        return implode("\n", $markup);
    }
}

/**
 * Generate a textarea field for CRM metabox.
 *
 * Creates an HTML textarea element for longer text input.
 *
 * @since 1.0.0
 *
 * @param string $key     Meta key for the field
 * @param array  $item    Configuration array containing:
 *                       - label: string Field label
 * @param string $edit_id Optional. Post ID when editing. Default empty.
 * @return string HTML markup for the textarea field
 */
if (!function_exists('wpestate_crm_return_textarea_metabox')) {
    function wpestate_crm_return_textarea_metabox($key, $item, $edit_id = '') {
        global $post;
        
        // Get field value
        $value = '';
        if (intval($edit_id) !== 0) {
            $value = get_post_meta($edit_id, $key, true);
        } else {
            $value = get_post_meta($post->ID, $key, true);
        }
        
        // Build HTML markup
        $markup = array(
            sprintf('<label for="%s">%s</label>', esc_attr($key), esc_html($item['label'])),
            sprintf(
                '<textarea id="%1$s" name="%1$s">%2$s</textarea>',
                esc_attr($key),
                esc_textarea($value)
            )
        );
        
        return implode("\n", $markup);
    }
}

/**
 * Generate a taxonomy select field for CRM metabox.
 *
 * Creates an HTML select element populated with taxonomy terms.
 *
 * @since 1.0.0
 *
 * @param string $key     Meta key for the field
 * @param array  $item    Configuration array containing:
 *                       - label: string Field label
 *                       - source: string Taxonomy name
 * @param string $edit_id Optional. Post ID when editing. Default empty.
 * @return string HTML markup for the select field
 */
if (!function_exists('wpestate_crm_return_taxonomy_metabox')) {
    function wpestate_crm_return_taxonomy_metabox($key, $item, $edit_id = '') {
        // Get taxonomy terms
        $terms = get_terms(array(
            'taxonomy' => sanitize_key($item['source']),
            'hide_empty' => false,
        ));
        
        if (is_wp_error($terms)) {
            return '';
        }
        
        // Build select options
        $select_options = array('<option value=""></option>');
        
        foreach ($terms as $term) {
            $selected = '';
            if (intval($edit_id) !== 0 && has_term($term->name, $item['source'], $edit_id)) {
                $selected = ' selected';
            }
            
            $select_options[] = sprintf(
                '<option value="%s"%s>%s</option>',
                esc_attr($term->name),
                $selected,
                esc_html($term->name)
            );
        }
        
        // Build HTML markup
        $markup = array(
            sprintf('<label for="%s">%s</label>', esc_attr($key), esc_html($item['label'])),
            sprintf(
                '<select id="%1$s" name="%1$s">',
                esc_attr($key)
            ),
            implode("\n", $select_options),
            '</select>'
        );
        
        return implode("\n", $markup);
    }
}




/**
 * Displays the add note form in the CRM system.
 *
 * Generates HTML markup for a comment form including a textarea,
 * submit button, and necessary security fields.
 *
 * @since 1.0.0
 *
 * @param int $post_id The post ID to attach the note to
 * @return string HTML markup for the add note form
 */
if (!function_exists('wpestate_crm_display_add_note')) {
    function wpestate_crm_display_add_note($post_id) {
        $current_user = wp_get_current_user();
        $ajax_nonce = wp_create_nonce('wpestate_crm_insert_note');
        
        $markup = array(
            '<div class="add_comments_wrapper">',
            sprintf('<h2>%s</h2>', esc_html__('Add a new comment', 'wpestate-crm')),
            '<textarea id="crm_new_commnet" class="form-control"></textarea>',
            sprintf(
                '<div id="crm_insert_comment" class="wpresidence_button" data-who="%s" data-date="%s" data-postid="%d">%s</div>',
                esc_attr($current_user->user_nicename),
                esc_attr(current_time('mysql')),
                intval($post_id),
                esc_html__('Add Comment', 'wpestate-crm')
            ),
            sprintf(
                '<input type="hidden" id="wpestate_crm_insert_note" value="%s">',
                esc_attr($ajax_nonce)
            ),
            '</div>'
        );
        
        return implode("\n", $markup);
    }
}

/**
 * Displays the lead content associated with a contact.
 *
 * Retrieves and displays the lead post content linked to a specific contact.
 *
 * @since 1.0.0
 *
 * @param int $contact_id The contact post ID
 * @return string HTML markup displaying the lead content
 */
if (!function_exists('wpestate_show_lead_per_contact')) {
    function wpestate_show_lead_per_contact($contact_id) {
        $lead_id = get_post_meta($contact_id, 'lead_contact', true);
        
        if (empty($lead_id)) {
            return '';
        }
        
        $lead_content = get_post_field('post_content', $lead_id);
        
        $markup = array(
            '<div class="lead_content">',
            sprintf('<h2>%s</h2>', esc_html__('Lead', 'wpestate-crm')),
            wp_kses_post($lead_content),
            '</div>'
        );
        
        return implode("\n", $markup);
    }
}

/**
 * Displays all notes/comments for a specific post.
 *
 * Retrieves and displays all comments associated with a post,
 * including delete functionality for each comment.
 *
 * @since 1.0.0
 *
 * @param int $post_id The post ID to get comments for
 * @return string HTML markup displaying all comments
 */
if (!function_exists('wpestate_crm_show_notes')) {
    function wpestate_crm_show_notes($post_id) {
        $all_comments = get_comments(array(
            'post_id' => $post_id,
        ));
        
        $markup = array('<div id="show_notes_wrapper">');
        
        if (is_array($all_comments) && !empty($all_comments)) {
            foreach ($all_comments as $item) {
                $comment_markup = array(
                    '<div class="comment_item">',
                    sprintf(
                        '<i data-delete="%d" class="fa fa-trash-o wpestate-crm_delete" aria-hidden="true"></i>',
                        intval($item->comment_ID)
                    ),
                    sprintf(
                        '<div class="comment_name">%s %s</div>',
                        esc_html__('from', 'wpestate-crm'),
                        esc_html($item->comment_author)
                    ),
                    sprintf(
                        '<div class="comment_date">%s %s</div>',
                        esc_html__('on', 'wpestate-crm'),
                        esc_html($item->comment_date)
                    ),
                    sprintf(
                        '<div class="comment_content">%s</div>',
                        wp_kses_post($item->comment_content)
                    ),
                    '</div>'
                );
                $markup[] = implode("\n", $comment_markup);
            }
        }
        
        $markup[] = '</div>';
        
        return implode("\n", $markup);
    }
}




/**
 * Handles AJAX request to add a comment in the CRM system.
 * 
 * Validates user permissions, sanitizes input, and creates a new comment.
 * Hooked to 'wp_ajax_wpestate_crm_add_comment'.
 *
 * @since 1.0.0
 */
if (!function_exists('wpestate_crm_add_comment')) {
    add_action('wp_ajax_wpestate_crm_add_comment', 'wpestate_crm_add_comment');
    function wpestate_crm_add_comment() {
        // Verify nonce and user authentication
        check_ajax_referer('wpestate_crm_insert_note', 'security');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => esc_html__('Authentication required', 'wpestate-crm')
            ));
        }
        
        // Validate required parameters
        if (!isset($_POST['content']) || !isset($_POST['item_id'])) {
            wp_send_json_error(array(
                'message' => esc_html__('Missing required parameters', 'wpestate-crm')
            ));
        }
        
        // Get and sanitize input
        $current_user = wp_get_current_user();
        $content = sanitize_textarea_field(wp_unslash($_POST['content']));
        $post_id = intval($_POST['item_id']);
        
        // Verify post exists
        if (!get_post($post_id)) {
            wp_send_json_error(array(
                'message' => esc_html__('Invalid post ID', 'wpestate-crm')
            ));
        }
        
        // Prepare comment data
        $comment_arg = array(
            'comment_post_ID'      => $post_id,
            'user_id'             => $current_user->ID,
            'comment_author'      => $current_user->user_nicename,
            'comment_author_email' => $current_user->user_email,
            'comment_content'     => $content,
            'comment_date'        => current_time('mysql'),
            'comment_approved'    => 1,
        );
        
        // Insert comment
        $comment_id = wp_insert_comment($comment_arg);
        
        if (!$comment_id) {
            wp_send_json_error(array(
                'message' => esc_html__('Failed to add comment', 'wpestate-crm')
            ));
        }
        
        wp_send_json_success(array(
            'message' => esc_html__('Comment added successfully', 'wpestate-crm'),
            'comment_id' => $comment_id
        ));
    }
}

/**
 * Handles AJAX request to delete a comment in the CRM system.
 * 
 * Validates user permissions and deletes the specified comment.
 * Hooked to 'wp_ajax_wpestate_crm_delete_comment'.
 *
 * @since 1.0.0
 */
if (!function_exists('wpestate_crm_delete_comment')) {
    add_action('wp_ajax_wpestate_crm_delete_comment', 'wpestate_crm_delete_comment');
    function wpestate_crm_delete_comment() {
        // Verify nonce and user authentication
        check_ajax_referer('wpestate_crm_insert_note', 'security');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => esc_html__('Authentication required', 'wpestate-crm')
            ));
        }
        
        // Validate required parameters
        if (!isset($_POST['item_id'])) {
            wp_send_json_error(array(
                'message' => esc_html__('Missing comment ID', 'wpestate-crm')
            ));
        }
        
        $current_user = wp_get_current_user();
        $comment_id = intval($_POST['item_id']);
        
        // Get comment and verify ownership
        $comment = get_comment($comment_id);
        if (!$comment) {
            wp_send_json_error(array(
                'message' => esc_html__('Comment not found', 'wpestate-crm')
            ));
        }
        
        if ($comment->comment_author_email !== $current_user->user_email) {
            wp_send_json_error(array(
                'message' => esc_html__('Permission denied', 'wpestate-crm')
            ));
        }
        
        // Delete comment
        $result = wp_delete_comment($comment_id, true);
        
        if (!$result) {
            wp_send_json_error(array(
                'message' => esc_html__('Failed to delete comment', 'wpestate-crm')
            ));
        }
        
        wp_send_json_success(array(
            'message' => esc_html__('Comment deleted successfully', 'wpestate-crm')
        ));
    }
}

/**
 * Retrieves the profile picture URL for a CRM contact.
 * 
 * Attempts to get the custom picture for registered users,
 * falls back to Gravatar for non-registered users.
 *
 * @since 1.0.0
 * 
 * @param int $contact_id The contact post ID
 * @return string URL of the profile picture or empty string if not found
 */
if (!function_exists('wpestate_crm_get_user_picture')) {
    function wpestate_crm_get_user_picture($contact_id) {
        $email = get_post_meta($contact_id, 'crm_email', true);
        
        if (empty($email)) {
            return '';
        }
        
        $email = sanitize_email($email);
        $user = get_user_by('email', $email);
        
        if ($user && $user instanceof WP_User) {
            $custom_picture = get_the_author_meta('custom_picture', $user->ID);
            if (!empty($custom_picture)) {
                return esc_url($custom_picture);
            }
        }
        
        return esc_url(get_avatar_url($email));
    }
}





/**
 * Generate contact selection options list
 *
 * @param int $contact_edit ID of the contact being edited
 * @return string HTML string containing option elements
 */
function wpestate_list_select_contacts($contact_edit) {
    // Initialize variables
    $current_user = wp_get_current_user();
    $userID       = absint($current_user->ID);
    $agent_list   = wpestate_return_agent_list();
    
    // Setup query arguments
    $args = array(
        'post_type'      => 'wpestate_crm_contact',
        'author__in'     => $agent_list,
        'posts_per_page' => -1,
    );
    
    // Get selected contact
    $select_contact = absint(get_post_meta($contact_edit, 'lead_contact', true));
    
    // Build options HTML
    $options = '';
    $lead_selection = new WP_Query($args);
    
    while ($lead_selection->have_posts()): 
        $lead_selection->the_post();
        $post_id = get_the_ID();
        
        $options .= sprintf(
            '<option value="%1$d" %2$s>%3$s</option>',
            $post_id,
            selected($select_contact, $post_id, false),
            esc_html(get_the_title($post_id))
        );
    endwhile;
    
    wp_reset_postdata();
    return $options;
}