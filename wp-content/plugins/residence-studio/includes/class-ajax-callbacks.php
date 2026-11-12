<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WpResidence_Elementor_Ajax_Callbacks {

    /**
     * Ajax callback for adding a new header/footer template
     *
     * @since 1.0.0
     * @access public
     */
    public function add_head_foot_callback() {
        // Check if the user has the 'manage_options' capability (admin)
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission to perform this action.', 'wpresidence-core')));
            return;
        }

        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wpestate_add_head_foot_action')) {
            wp_send_json_error(array('message' => __('Invalid nonce', 'wpresidence-core')));
            return;
        }

        // Sanitize and save the form data
        $title = sanitize_text_field($_POST['title']);
        $template = sanitize_text_field($_POST['template']);
        $location = $_POST['location'];
        if (is_array($location)) {
            $location = array_map('sanitize_text_field', $location);
        } else {
            $location = array(sanitize_text_field($location));
        }

        $exclude_location = isset($_POST['exclude_location']) ? $_POST['exclude_location'] : array();
        if (is_array($exclude_location)) {
            $exclude_location = array_map('sanitize_text_field', $exclude_location);
        } else {
            $exclude_location = array(sanitize_text_field($exclude_location));
        }

        // Create the post
        $new_post = array(
            'post_title' => $title,
            'post_status' => 'publish',
            'post_type' => 'wpestate-studio',
        );

        $post_id = wp_insert_post($new_post);

        if ($post_id) {
            // Add post meta
            update_post_meta($post_id, 'wpestate_head_foot_template', $template);
            update_post_meta($post_id, 'wpestate_head_foot_positions', $location);
            update_post_meta($post_id, 'wpestate_head_foot_exclude_positions', $exclude_location);

            wp_send_json_success(array('message' => __('Template added successfully', 'wpresidence-core')));
        } else {
            wp_send_json_error(array('message' => __('Failed to add template', 'wpresidence-core')));
        }
    }
}
