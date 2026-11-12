<?php
/**
 * WpResidence Message Metaboxes
 *
 * This file contains functions that create and populate custom metaboxes
 * for the 'wpestate_message' custom post type in the WordPress admin area.
 * These metaboxes provide a user interface for viewing and managing message
 * details such as sender, recipient, and message status.
 *
 * @package    WpResidence
 * @subpackage Messaging
 * @version    1.0
 * @author     WpResidence
 */



 
/**
 * Registers the custom metabox for message details
 *
 * This function hooks into WordPress to add a custom metabox to the 'wpestate_message'
 * post type edit screen. The metabox displays message-specific information and allows
 * administrators to view and modify message properties.
 *
 * @uses add_meta_box() WordPress function to register a metabox
 * @return void
 */
if( !function_exists('wpestate_add_message_metaboxes') ):
    function wpestate_add_message_metaboxes() {
      add_meta_box(  'estate_message-sectionid', esc_html__(  'Message Details', 'wpresidence-core' ), 'wpestate_message_meta_function', 'wpestate_message' ,'normal','default');
    }
endif; // end





/**
 * Renders the content for the message details metabox
 *
 * This callback function generates the HTML content displayed within the message
 * details metabox. It retrieves message metadata such as sender and recipient,
 * and creates form fields to display and potentially edit this information.
 *
 * Key features:
 * - Displays the message sender (from_user) as text
 * - Provides a dropdown for selecting/changing the message recipient (to_user)
 * - Includes hidden fields for message status and deletion flags
 * - Handles both new message creation and existing message editing
 *
 * @param WP_Post $post The current post object (message being edited)
 * @uses wp_nonce_field() To add security nonce
 * @uses get_post_meta() To retrieve message metadata
 * @uses wpestate_is_edit_page() To determine if creating new message
 * @uses wpestate_get_user_list() To generate recipient dropdown options
 * @return void Outputs HTML directly
 */
if( !function_exists('wpestate_message_meta_function') ):
    function wpestate_message_meta_function( $post ) {
        // Create security nonce
        wp_nonce_field( plugin_basename( __FILE__ ), 'estate_message_noncename' );
        global $post;
        
        // Get message sender ID from post meta
        $from_value=esc_html(get_post_meta($post->ID, 'message_from_user', true));
        
        // Check if this is the first message in a conversation
        $first_content=esc_html(get_post_meta($post->ID, 'first_content', true));
        
        // If creating a new message, set default sender as administrator
        if (wpestate_is_edit_page('new')){
            $from_value='administrator';
        }
        
        // Get message recipient ID
        $to_val=esc_html(get_post_meta($post->ID, 'message_to_user', true));
        
        // Begin output of metabox content
        print'
        <p class="meta-options">
            <label for="message_from_user">'.esc_html__( 'From User:','wpresidence-core').' </label><br />
            <input type="text" id="message_from_user" size="58" name="message_from_user" value="';
            // Convert sender ID to username for display
            // Note: The from_value variable is not used directly in the original code
            $user = get_user_by( 'id', $from_value );
            echo $user->user_login;
            print '">
        </p>
        <p class="meta-options">
            <label for="message_to_user">'.esc_html__( 'To User:','wpresidence-core').' </label><br />
            <select id="message_to_user" name="message_to_user">
                '.wpestate_get_user_list().'
            </select>
        
        <!-- Hidden fields for message status and deletion flags -->
        <input type="hidden" name="message_status" value="'.esc_html__( 'unread','wpresidence-core').'">
        <input type="hidden" name="delete_source" value="0">
        <input type="hidden" name="delete_destination" value="0">
        </p>';
    }
endif; // end