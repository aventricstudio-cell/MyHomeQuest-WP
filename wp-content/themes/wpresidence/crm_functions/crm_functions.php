<?php

/**
 * Displays lead details in the dashboard.
 * 
 * Outputs formatted lead information including handler, status, and custom fields
 * based on the leads post array configuration.
 *
 * @since 1.0.0
 * 
 * @param int $lead_id The ID of the lead post to display
 * @return void Outputs HTML directly
 */
if (!function_exists('wpestate_show_lead_details_dashboard')) {
    function wpestate_show_lead_details_dashboard($lead_id) {
     
        $leads_post_array = wpestate_leads_post_array();
        
        printf('<h2>%s</h2>', esc_html__('Lead Info', 'wpresidence'));
        
        foreach ($leads_post_array as $key => $item) {
            $value = '';
            
            // Get field value based on type
            switch ($key) {
                case 'crm_handler':
                    $handler_id = get_post_meta($lead_id, 'crm_handler', true);
                    $value = wpresidence_get_sanitized_truncated_title($handler_id,0);
                    break;
                    
                case 'status':
                    $status = strip_tags(get_the_term_list($lead_id, 'wpestate-crm-lead-status', '', ', ', ''));
                    $value = empty($status) ? esc_html__('New', 'wpresidence') : $status;
                    break;
                    
                case 'crm_lead_content':
                    $value = get_post_field('post_content', $lead_id);
                    break;
                    
                default:
                    $value = get_post_meta($lead_id, $key, true);
            }
            
            printf(
                '<div class="contact_crm_detail">
                    <label>%s: </label>%s
                </div>',
                esc_html($item['label']),
                wp_kses_post(trim($value))
            );
        }
    }
}

/**
 * Creates or updates a CRM lead from the dashboard.
 * 
 * Handles lead creation/update including metadata and taxonomy terms.
 * Validates user permissions and sanitizes input data.
 *
 * @since 1.0.0
 * 
 * @param array $arguments Array of lead data to save
 * @param array $agent_list Array of allowed agent IDs
 * @param int   $lead_id Optional. Lead ID when updating existing lead. Default empty.
 * @return void
 */
if (!function_exists('wpestate_create_crm_lead_dashboard')) {
    function wpestate_create_crm_lead_dashboard($arguments, $agent_list, $lead_id = '') {
        // Validate input
        if (!is_array($arguments) || !is_array($agent_list)) {
            wp_die(esc_html__('Invalid input data', 'wpresidence'));
        }
        
        $leads_post_array = wpestate_leads_post_array();
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        
        // Handle new lead creation
        if (empty($lead_id)) {
            $post = array(
                'post_content'   => '',
                'post_status'    => 'publish',
                'post_type'      => 'wpestate_crm_lead',
                'post_author'    => $userID,
            );
            
            $lead_id = wp_insert_post($post);
            
            if (is_wp_error($lead_id)) {
                wp_die($lead_id->get_error_message());
            }
            
            // Update post title
            wp_update_post(array(
                'ID'         => $lead_id,
                'post_title' => sprintf('Lead message no: %d', $lead_id)
            ));
        } 
        // Handle lead update
        else {
            $lead_id = intval($lead_id);
            $post_author_id = get_post_field('post_author', $lead_id);
            
            // Verify permissions
            if (!in_array($post_author_id, $agent_list) && !current_user_can('administrator')) {
                wp_die(esc_html__('You are not allowed to edit this!', 'wpresidence'));
            }
            
            // Clear existing terms
            wp_delete_object_term_relationships($lead_id, 'wpestate-crm-lead-status');
        }
        
        // Update lead data
        foreach ($leads_post_array as $key => $item) {
            if (!isset($arguments[$key])) {
                continue;
            }
            
            if ($item['type'] === 'taxonomy') {
                wp_set_object_terms($lead_id, $arguments[$key], 'wpestate-crm-lead-status');
            } elseif ($key !== 'crm_lead_permalink' && $key !== 'crm_lead_content') {
                update_post_meta($lead_id, $key, sanitize_text_field($arguments[$key]));
            }
        }
        
        // Handle contact relationship
        if (isset($_POST['wpestate_crm_manual_contact'])) {
            $contact_id = intval($_POST['wpestate_crm_manual_contact']);
            if ($contact_id > 0) {
                update_post_meta($contact_id, 'lead_contact', $lead_id);
                update_post_meta($lead_id, 'lead_contact', $contact_id);
                update_post_meta($contact_id, 'lead_contact_to_id', $lead_id);
            }
        }
    }
}




/**
 * Handles AJAX request to add a comment from the CRM dashboard.
 * 
 * Validates user permissions, sanitizes input, and creates a new comment.
 * Hooked to 'wp_ajax_wpestate_crm_add_comment_dashboard'.
 *
 * @since 1.0.0
 */
if (!function_exists('wpestate_crm_add_comment_dashboard')) {
    add_action('wp_ajax_wpestate_crm_add_comment_dashboard', 'wpestate_crm_add_comment_dashboard');
    function wpestate_crm_add_comment_dashboard() {
        try {
            // Verify nonce and user authentication
            check_ajax_referer('wpestate_crm_insert_note', 'security');
            
            if (!is_user_logged_in()) {
                wp_send_json_error(array(
                    'message' => esc_html__('Authentication required', 'wpresidence')
                ));
            }
            
            // Validate required parameters
            if (!isset($_POST['content']) || !isset($_POST['item_id'])) {
                wp_send_json_error(array(
                    'message' => esc_html__('Missing required parameters', 'wpresidence')
                ));
            }
            
            // Get and validate input
            $content = sanitize_textarea_field(wp_unslash($_POST['content']));
            $post_id = intval($_POST['item_id']);
            
            if (empty($content) || empty($post_id)) {
                wp_send_json_error(array(
                    'message' => esc_html__('Invalid input parameters', 'wpresidence')
                ));
            }
            
            // Check permissions
            $agent_list = wpestate_return_agent_list();
            $current_user = wp_get_current_user();
            $post_author_id = get_post_field('post_author', $post_id);
            
            if (!in_array($post_author_id, $agent_list) && !current_user_can('administrator')) {
                wp_send_json_error(array(
                    'message' => esc_html__('You do not have permission to add comments', 'wpresidence')
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
                    'message' => esc_html__('Failed to add comment', 'wpresidence')
                ));
            }
            
            // Prepare HTML response
            $comment_html = sprintf(
              '<div class="comment_item">
                  <i data-delete="%1$d" class="fa fa-trash-o wpestate-crm_delete" aria-hidden="true"></i>
                  <div class="comment_name">%2$s %3$s</div>
                  <div class="comment_date">%4$s %5$s</div>
                  <div class="comment_content">%6$s</div>
              </div>',
              intval($comment_id),
              esc_html__('from', 'wpresidence'),
              esc_html($current_user->user_nicename),
              esc_html__('on', 'wpresidence'),
              esc_html(current_time('mysql')),
              wp_kses_post($content)
          );

            wp_send_json_success(array(
                'message' => esc_html__('Comment added successfully', 'wpresidence'),
                'comment_id' => $comment_id,
                'html' => $comment_html
            ));
            
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => esc_html__('An error occurred while processing your request', 'wpresidence')
            ));
        }
    }
}

/**
 * Deletes a CRM contact and associated data.
 * 
 * Validates user permissions and deletes the specified contact.
 * Includes proper error handling and security checks.
 *
 * @since 1.0.0
 * 
 * @param int   $delete_contact_id The ID of the contact to delete
 * @param array $agent_list Array of allowed agent IDs
 * @return void
 * @throws WP_Error If deletion fails or permissions are invalid
 */
if (!function_exists('wpestate_crm_delete_contact')) {
    function wpestate_crm_delete_contact($delete_contact_id, $agent_list) {
        try {
            // Validate input
            $delete_contact_id = intval($delete_contact_id);
            if (empty($delete_contact_id)) {
                throw new Exception(esc_html__('Invalid contact ID', 'wpresidence'));
            }
            
            if (!is_array($agent_list)) {
                throw new Exception(esc_html__('Invalid agent list', 'wpresidence'));
            }
            
            // Verify contact exists
            $contact = get_post($delete_contact_id);
            if (!$contact) {
                throw new Exception(esc_html__('Contact not found', 'wpresidence'));
            }
            
            // Check permissions
            $post_author_id = get_post_field('post_author', $delete_contact_id);
            if (!in_array($post_author_id, $agent_list) && !current_user_can('administrator')) {
                throw new Exception(esc_html__('You are not allowed to delete this contact', 'wpresidence'));
            }
            
            // Delete associated meta and terms first
            $lead_id = get_post_meta($delete_contact_id, 'lead_contact', true);
            if ($lead_id) {
                delete_post_meta($lead_id, 'lead_contact');
                delete_post_meta($lead_id, 'lead_contact_to_id');
            }
            
            // Delete the contact
            $result = wp_delete_post($delete_contact_id, true);
            
            if (!$result) {
                throw new Exception(esc_html__('Failed to delete contact', 'wpresidence'));
            }
            
            return true;
            
        } catch (Exception $e) {
            if (wp_doing_ajax()) {
                wp_send_json_error(array(
                    'message' => $e->getMessage()
                ));
            } else {
                echo esc_html($e->getMessage());
                exit();
            }
        }
    }
}




/**
 * Creates or updates a CRM contact in the dashboard.
 * 
 * Handles contact creation/update including metadata and taxonomy terms.
 * Validates user permissions and sanitizes input data.
 *
 * @since 1.0.0
 * 
 * @param array $arguments Array of contact data to save
 * @param array $agent_list Array of allowed agent IDs
 * @param int   $lead_id Optional. Lead ID when updating existing contact. Default empty.
 * @return int|WP_Error Post ID on success, WP_Error on failure
 */
if (!function_exists('wpestate_create_crm_contact_dashboard')) {
    function wpestate_create_crm_contact_dashboard($arguments, $agent_list, $lead_id = '') {
        try {
            // Validate input parameters
            if (!is_array($arguments) || !isset($arguments['crm_first_name'])) {
                throw new Exception(esc_html__('Invalid contact data provided', 'wpresidence'));
            }
            
            if (!is_array($agent_list)) {
                throw new Exception(esc_html__('Invalid agent list', 'wpresidence'));
            }
            
            // Setup basic variables
            $contact_post_array = wpestate_return_contact_post_array();
            $current_user = wp_get_current_user();
            $userID = $current_user->ID;
            
            // Handle new contact creation
            if (empty($lead_id)) {
                $post_data = array(
                    'post_title'  => sanitize_text_field($arguments['crm_first_name']),
                    'post_status' => 'publish',
                    'post_type'   => 'wpestate_crm_contact',
                    'post_author' => $userID,
                );
                
                $post_id = wp_insert_post($post_data, true);
                if (is_wp_error($post_id)) {
                    throw new Exception($post_id->get_error_message());
                }
            }
            // Handle contact update
            else {
                $lead_id = intval($lead_id);
                $post_author_id = get_post_field('post_author', $lead_id);
                
                if (!in_array($post_author_id, $agent_list) && !current_user_can('administrator')) {
                    throw new Exception(esc_html__('You are not allowed to edit this contact', 'wpresidence'));
                }
                
                $post_data = array(
                    'post_title' => sanitize_text_field($arguments['crm_first_name']),
                    'ID'        => $lead_id,
                );
                
                $update_result = wp_update_post($post_data, true);
                if (is_wp_error($update_result)) {
                    throw new Exception($update_result->get_error_message());
                }
                
                $post_id = $lead_id;
                
                // Clear existing terms
                wp_delete_object_term_relationships($post_id, 'wpestate-crm-contact-status');
            }
            
            // Update contact meta and terms
            foreach ($contact_post_array as $key => $item) {
                if (!isset($arguments[$key])) {
                    continue;
                }
                
                if ($item['type'] !== 'taxonomy') {
                    update_post_meta($post_id, $key, sanitize_text_field($arguments[$key]));
                } else {
                    wp_set_object_terms($post_id, $arguments[$key], 'wpestate-crm-contact-status');
                }
            }
            
            // Update lead relationships
            if (!empty($lead_id)) {
                update_post_meta($post_id, 'lead_contact', intval($lead_id));
                update_post_meta($lead_id, 'lead_contact', intval($post_id));
            }
            
            return $post_id;
            
        } catch (Exception $e) {
            if (wp_doing_ajax()) {
                wp_send_json_error(array('message' => $e->getMessage()));
            } else {
                echo esc_html($e->getMessage());
                exit();
            }
        }
    }
}

/**
 * Routes CRM dashboard display based on action parameter.
 * 
 * Determines which CRM view to display (leads or contacts)
 * based on the 'actions' query parameter.
 *
 * @since 1.0.0
 * 
 * @param array $agent_list Array of allowed agent IDs
 * @return void
 */
if (!function_exists('wpestate_show_crm_data_split')) {
    function wpestate_show_crm_data_split($agent_list) {
        try {
            // Validate input
            if (!is_array($agent_list)) {
                throw new Exception(esc_html__('Invalid agent list provided', 'wpresidence'));
            }
            
            // Get and validate action
            $action = filter_input(INPUT_GET, 'actions', FILTER_VALIDATE_INT, 
                array('options' => array('default' => 0, 'min_range' => 0, 'max_range' => 1))
            );
            
            // Route to appropriate view
            switch ($action) {
                case 0:
                    wpestate_show_crm_data_leads($agent_list);
                    break;
                    
                case 1:
                    wpestate_show_crm_data_contacts($agent_list);
                    break;
                    
                default:
                    throw new Exception(esc_html__('Invalid action specified', 'wpresidence'));
            }
            
        } catch (Exception $e) {
            echo '<div class="notice notice-error">';
            echo esc_html($e->getMessage());
            echo '</div>';
        }
    }
}



/**
 * Displays the list of CRM contacts in the dashboard.
 * 
 * Shows a paginated list of contacts with their details and actions.
 * Includes filtering by agent permissions.
 *
 * @since 1.0.0
 * 
 * @param array $agent_list Array of allowed agent IDs
 * @return void
 */
if (!function_exists('wpestate_show_crm_data_contacts')) {
    function wpestate_show_crm_data_contacts($agent_list) {
        try {
            // Validate input
            if (!is_array($agent_list)) {
                throw new Exception(esc_html__('Invalid agent list provided', 'wpresidence'));
            }
            
            // Get pagination parameters
            $prop_no = intval(wpresidence_get_option('wp_estate_prop_no', ''));
            $paged = max(1, get_query_var('paged'));
            
            // Setup query arguments
            $args = array(
                'post_type'      => 'wpestate_crm_contact',
                'author__in'     => array_map('intval', $agent_list),
                'paged'          => $paged,
                'posts_per_page' => $prop_no,
                'orderby'        => 'date',
                'order'          => 'DESC'
            );
            
            // Execute query
            $contact_selection = new WP_Query($args);
            
            // Display section header and create button
            $header_markup = array(
                sprintf('<div class="wpestate_dashboard_section_title inbox_title">%s</div>', 
                    esc_html__('Your Contacts', 'wpresidence')
                ),
                sprintf('<a id="westate_crm_create_contact" class="wpresidence_button" href="%s">%s</a>',
                    esc_url(wpestate_get_template_link('wpestate-crm-dashboard_contacts.php')),
                    esc_html__('Create Contact', 'wpresidence')
                )
            );
            echo implode("\n", $header_markup);
            
            // Display table header
            ?>
            <div class="wpestate_dashboard_table_list_header d-none d-md-flex row">
                <div class="col-md-2"><?php esc_html_e('Name', 'wpresidence'); ?></div>
                <div class="col-md-2"><?php esc_html_e('Email', 'wpresidence'); ?></div>
                <div class="col-md-2"><?php esc_html_e('Phone', 'wpresidence'); ?></div>
                <div class="col-md-2"><?php esc_html_e('Added on', 'wpresidence'); ?></div>
                <div class="col-md-2"><?php esc_html_e('Status', 'wpresidence'); ?></div>
                <div class="col-md-2 wpestate_crm_lead_actions"><?php esc_html_e('Actions', 'wpresidence'); ?></div>
            </div>
            <?php
            
            // Display contacts or no results message
            if (!$contact_selection->have_posts()) {
                printf('<h4 class="no-results">%s</h4>', 
                    esc_html__('You don\'t have any Contacts!', 'wpresidence')
                );
            } else {
                while ($contact_selection->have_posts()): 
                    $contact_selection->the_post();
                    $post_id = get_the_ID();
                    include(locate_template('crm_functions/templates/dashboard_contact_unit.php'));
                endwhile;
                
                wpestate_pagination($contact_selection->max_num_pages, 2);
            }
            
            wp_reset_postdata();
            
        } catch (Exception $e) {
            printf('<div class="notice notice-error">%s</div>', esc_html($e->getMessage()));
        }
    }
}

/**
 * Displays the list of CRM leads in the dashboard.
 * 
 * Shows a paginated list of leads with their details and actions.
 * Includes filtering by agent permissions.
 *
 * @since 1.0.0
 * 
 * @param array $agent_list Array of allowed agent IDs
 * @return void
 */
if (!function_exists('wpestate_show_crm_data_leads')) {
    function wpestate_show_crm_data_leads($agent_list) {
        try {
            // Validate input
            if (!is_array($agent_list)) {
                throw new Exception(esc_html__('Invalid agent list provided', 'wpresidence'));
            }
            
            // Get pagination parameters
            $prop_no = intval(wpresidence_get_option('wp_estate_prop_no', ''));
            $paged = max(1, get_query_var('paged'));
            
            // Setup query arguments
            $args = array(
                'post_type'      => 'wpestate_crm_lead',
                'author__in'     => array_map('intval', $agent_list),
                'paged'          => $paged,
                'posts_per_page' => $prop_no,
                'orderby'        => 'date',
                'order'          => 'DESC'
            );
            
            // Execute query
            $lead_selection = new WP_Query($args);
            
            // Display section header and create button
            $header_markup = array(
                sprintf('<div class="wpestate_dashboard_section_title inbox_title">%s</div>', 
                    esc_html__('Your Leads/Deals', 'wpresidence')
                ),
                sprintf('<a id="westate_crm_create_lead" class="wpresidence_button" href="%s">%s</a>',
                    esc_url(wpestate_get_template_link('wpestate-crm-dashboard_leads.php')),
                    esc_html__('Add New Lead/Deal', 'wpresidence')
                )
            );
            echo implode("\n", $header_markup);
            
            // Display table header
            ?>
            <div class="wpestate_dashboard_table_list_header d-none d-md-flex row">
                <div class="col-md-2"><?php esc_html_e('Lead No', 'wpresidence'); ?></div>
                <div class="col-md-2"><?php esc_html_e('Request By', 'wpresidence'); ?></div>
                <div class="col-md-2"><?php esc_html_e('Agent in Charge', 'wpresidence'); ?></div>
                <div class="col-md-2"><?php esc_html_e('Date', 'wpresidence'); ?></div>
                <div class="col-md-2"><?php esc_html_e('Status', 'wpresidence'); ?></div>
                <div class="col-md-2 wpestate_crm_lead_actions"><?php esc_html_e('Actions', 'wpresidence'); ?></div>
            </div>
            <?php
            
            // Display leads or no results message
            if (!$lead_selection->have_posts()) {
                printf('<h4 class="no-results">%s</h4>', 
                    esc_html__('You don\'t have any Leads!', 'wpresidence')
                );
            } else {
                while ($lead_selection->have_posts()): 
                    $lead_selection->the_post();
                    $post_id = get_the_ID();
                    include(locate_template('crm_functions/templates/dashboard_lead_unit.php'));
                endwhile;
                
                wpestate_pagination($lead_selection->max_num_pages, 2);
            }
            
            wp_reset_postdata();
            
        } catch (Exception $e) {
            printf('<div class="notice notice-error">%s</div>', esc_html($e->getMessage()));
        }
    }
}
?>
