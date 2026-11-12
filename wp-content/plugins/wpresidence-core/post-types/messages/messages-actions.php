<?php 
/**
 * WpResidence Message System Functions
 *
 * This file contains functions related to the WpResidence private messaging system.
 * Includes user list retrieval, message editing/checking functions, message reply
 * functionality, and notification management.
 *
 * @package    WpResidence
 * @subpackage Messaging
 * @author     WpResidence
 * @version    1.0
 */



/**
 * Retrieves and formats a list of WordPress users for dropdown selection
 *
 * Generates an HTML option list of all WordPress users with the ability to
 * mark a specific user as selected based on post meta. Used primarily in
 * message composition forms.
 *
 * @global object $post The current post object
 * @return string HTML string containing option elements for all users
 */
if( !function_exists('wpestate_get_user_list') ):
    function wpestate_get_user_list(){
        global $post;
        $selected=  get_post_meta($post->ID,'message_to_user',true);

        $return_string='';
        $blogusers = get_users();
        foreach ($blogusers as $user) {
           $return_string.= '<option value="'.$user->ID.'" ';
           if( $selected == $user->ID ){
                $return_string.=' selected="selected" ';
           }
           $return_string.= '>' . $user->user_nicename . '</option>';
        }
     return $return_string;
    }
endif;


/**
 * Determines if the current admin page is an edit or new post page
 *
 * Utility function that checks the current admin page to determine if we're
 * on a post editing or creation page. Helps with conditionally loading resources
 * or displaying UI elements.
 *
 * @param string|null $new_edit Optional parameter to specify page type check
 *                             'edit' - check if on post edit page only
 *                             'new' - check if on new post page only
 *                             null - check if on either page
 * @return boolean True if on specified page type, false otherwise
 */
if( !function_exists('wpestate_is_edit_page') ):
    function wpestate_is_edit_page($new_edit = null){
        global $pagenow;
        //make sure we are on the backend
        if (!is_admin()) return false;


        if($new_edit == "edit")
            return in_array( $pagenow, array( 'post.php',  ) );
        elseif($new_edit == "new") //check for new post page
            return in_array( $pagenow, array( 'post-new.php' ) );
        else //check for either new or edit
            return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
    }
endif;


/**
 * Displays all replies to a specific message
 *
 * Queries and displays titles of child messages (replies) for a given parent message ID.
 * Used to show message threads in admin or user dashboards.
 *
 * @param int $post_id The parent message ID to retrieve replies for
 * @return void Outputs HTML directly
 */
if( !function_exists('wpestate_show_mess_reply') ):
    function wpestate_show_mess_reply($post_id){
        $args = array(
                    'post_type'         => 'wpestate_message',
                    'post_status'       => 'publish',
                    'paged'             => 1,
                    'posts_per_page'    => 30,
                    'order'             => 'DESC',
                    'post_parent'       => $post_id,
                 );

        $message_selection = new WP_Query($args);
        while ($message_selection->have_posts()): $message_selection->the_post();
            print  get_the_title().'</br>';
        endwhile;
        wp_reset_query();
    }
endif;



/**
 * Handles the reply functionality for private messages in WpResidence
 * 
 * This function processes AJAX requests for message replies, creates new message posts,
 * updates message status, and handles notifications. Returns JSON response.
 * 
 * @package WpResidence
 * @subpackage Messages
 * @param int $_POST['messid'] ID of the parent message being replied to
 * @param string $_POST['title'] Title of the reply message
 * @param string $_POST['content'] Content of the reply message
 * @param string $_POST['security'] Nonce for security verification
 * @return json Response with status and message
 */

 add_action('wp_ajax_wpestate_message_reply', 'wpestate_message_reply');

 if (!function_exists('wpestate_message_reply')):
     function wpestate_message_reply() {
         // Set JSON header
         header('Content-Type: application/json');
         
         try {
             // Verify nonce and user authentication
             if (!check_ajax_referer('wpestate_inbox_actions', 'security', false)) {
                 wp_send_json_error(array(
                     'message' => esc_html__('Security check failed', 'wpresidence-core')
                 ));
             }
             
             // Get and validate current user
             $current_user = wp_get_current_user();
             $userID = $current_user->ID;
             
             if (!is_user_logged_in() || $userID === 0) {
                 wp_send_json_error(array(
                     'message' => esc_html__('Unauthorized access', 'wpresidence-core'),
                     'code' => 'unauthorized'
                 ));
             }
             
             // Clean up WordPress environment
             wp_reset_postdata();
             
             // Sanitize and validate input parameters
             $message_id = filter_input(INPUT_POST, 'messid', FILTER_VALIDATE_INT);
             $title = sanitize_text_field(wp_unslash($_POST['title']));
             $content = sanitize_textarea_field(wp_unslash($_POST['content']));
             
             if (!$message_id) {
                 wp_send_json_error(array(
                     'message' => esc_html__('Invalid message ID', 'wpresidence-core'),
                     'code' => 'invalid_id'
                 ));
             }
             
             // Get and validate message participants
             $receiver_id = wpsestate_get_author($message_id);
             $message_to_user = get_post_meta($message_id, 'message_to_user', true);
             
             // Verify user has permission to reply
             if ($current_user->ID != $message_to_user && $current_user->ID != $receiver_id) {
                 wp_send_json_error(array(
                     'message' => esc_html__('Permission denied', 'wpresidence-core'),
                     'code' => 'permission_denied'
                 ));
             }
             
             // Prepare new message post data
             $message_data = array(
                 'post_title'    => $title,
                 'post_content'  => $content,
                 'post_status'   => 'publish',
                 'post_type'     => 'wpestate_message',
                 'post_author'   => $userID,
                 'post_parent'   => $message_id
             );
             
             // Create reply message
             $post_id = wp_insert_post($message_data);
             
             if (is_wp_error($post_id)) {
                 wp_send_json_error(array(
                     'message' => $post_id->get_error_message(),
                     'code' => 'insert_failed'
                 ));
             }
             
             // Set message metadata
             $meta_data = array(
                 'delete_source'      => 0,
                 'delete_destination' => 0,
                 'message_to_user'    => $receiver_id,
                 'message_from_user'  => $userID
             );
             
             foreach ($meta_data as $key => $value) {
                 update_post_meta($post_id, $key, $value);
             }
             
             // Handle message status updates
             $mes_to = get_post_meta($message_id, 'message_to_user', true);
             $mess_from = get_post_meta($message_id, 'message_from_user', true);
             
             // Update unread status and increment message count
             if ($userID != $mes_to) {
                 wpestate_increment_mess_mo($mes_to);
             } else {
                 wpestate_increment_mess_mo($mess_from);
             }
             
             // Update message status for all participants
             $status_updates = array(
                 'message_status' . $mes_to     => 'unread',
                 'message_status' . $mess_from  => 'unread',
                 'message_status' . $userID     => 'read'
             );
             
             foreach ($status_updates as $meta_key => $status) {
                 update_post_meta($message_id, $meta_key, $status);
             }
             
             // Send success response
             wp_send_json_success(array(
                 'message' => esc_html__('Reply sent successfully', 'wpresidence-core'),
                 'post_id' => $post_id,
                 'receiver_id' => $receiver_id
             ));
             
         } catch (Exception $e) {
             wp_send_json_error(array(
                 'message' => $e->getMessage(),
                 'code' => 'system_error'
             ));
         }
     }
 endif;
 
 
 
 /**
  * Marks a message as read via AJAX
  *
  * Processes AJAX requests to update message status to 'read' when a user views it.
  * Updates user meta to track unread message count. Includes security checks to
  * ensure only authorized users can mark messages as read.
  * 
  * @uses check_ajax_referer() For security verification
  * @uses wp_get_current_user() To get current user data
  * @uses wpsestate_get_author() To get the message author
  * @uses update_post_meta() To update message read status
  * @uses update_user_meta() To update unread message count
  * 
  * @return void Outputs 'ko' for failure or dies silently on success
  */
 add_action('wp_ajax_wpestate_booking_mark_as_read', 'wpestate_booking_mark_as_read' );
 
 if( !function_exists('wpestate_booking_mark_as_read') ):
     function wpestate_booking_mark_as_read(){
         check_ajax_referer( 'wpestate_inbox_actions', 'security' );
         $current_user = wp_get_current_user();
         $userID                         =   $current_user->ID;
 
 
         if ( !is_user_logged_in() ) {
             exit('ko');
         }
         if($userID === 0 ){
             exit('out pls');
         }
 
         $messid             =   intval($_POST['messid']);
         $receiver_id        =   wpsestate_get_author($messid);
         $message_to_user    =   get_post_meta($messid,'message_to_user',true);
 
         if( $current_user->ID != $message_to_user && $current_user->ID != $receiver_id ) {
             exit('you don\'t have the right');
         }
 
         $mess_status =      get_post_meta($messid, 'message_status'.$current_user->ID, true);
         if($mess_status!=='read'){
             update_post_meta($messid, 'message_status'.$current_user->ID, 'read');
             $unread=abs(intval ( get_user_meta($current_user->ID,'unread_mess',true) - 1));
             update_user_meta($current_user->ID,'unread_mess',$unread);
         }
         die();
     }
 endif;
 
 


 
 /**
  * WpResidence Messaging System Core Functions
  *
  * This file contains core functions for the WpResidence messaging and inbox system.
  * Includes message deletion, front-end message creation, notification counting,
  * and message management utilities.
  *
  * @package    WpResidence
  * @subpackage Messaging
  * @version    1.0
  * @author     WpResidence
  */
  
  
 /**
  * AJAX handler for deleting messages from user inbox
  *
  * Handles secure deletion of messages, including checking permissions and managing
  * unread message counts. Uses a soft delete approach where messages are only
  * fully deleted when both sender and recipient have deleted them.
  *
  * @uses check_ajax_referer() For security verification
  * @uses wp_get_current_user() To verify current user
  * @uses update_post_meta() To mark messages as deleted
  * @uses update_user_meta() To update unread message counts
  * @uses wp_delete_post() To permanently delete messages when appropriate
  * 
  * @return void Exits with error messages or dies silently on success
  */
 add_action('wp_ajax_wpestate_booking_delete_mess', 'wpestate_booking_delete_mess');
  
 if( !function_exists('wpestate_booking_delete_mess') ):
     function wpestate_booking_delete_mess(){
         // Verify security nonce
         check_ajax_referer( 'wpestate_inbox_actions', 'security' );
         $current_user   =   wp_get_current_user();
         $userID         =   $current_user->ID;
  
         // Check if user is logged in
         if ( !is_user_logged_in() ) {
             exit('ko');
         }
  
         // Check if user ID is valid
         if($userID === 0 ){
             exit('out pls');
         }
  
         // Get message details and verify ownership/permissions
         $userID             =   $current_user->ID;
         $messid             =   intval($_POST['messid']);
         $receiver_id        =   wpsestate_get_author($messid);
         $message_to_user    =   get_post_meta($messid,'message_to_user',true);
  
         // Security check - only message participants can delete it
         if( $current_user->ID != $message_to_user && $current_user->ID != $receiver_id ) {
             exit('you don\'t have the right');
         }
  
         // Mark message as deleted for current user
         update_post_meta($messid, 'delete_destination'.$userID, 1);
  
         // If message was unread, update unread count
         $mess_status =      get_post_meta($messid, 'message_status'.$current_user->ID, true);
         if($mess_status!=='read'){
             $unread=abs(intval ( get_user_meta($current_user->ID,'unread_mess',true) - 1));
             update_user_meta($current_user->ID,'unread_mess',$unread);
         }
  
         // Get message participant IDs and deletion status
         $starter        =   get_post_meta($messid,'message_from_user',true);
         $destination    =   get_post_meta($messid,'message_to_user',true);
  
         $delete_start   =   get_post_meta($messid,'delete_destination'.$starter,true);
         $delete_dest    =   get_post_meta($messid,'delete_destination'.$destination,true);
  
         // If both users have deleted the message, permanently remove it and all replies
         if($delete_start ==1 && $delete_dest==1){
             // Query for all child messages (replies)
             $args_child = array(
                 'post_type'         => 'wpestate_message',
                 'posts_per_page'    => -1,
                 'post_parent'       => $messid,
             );
  
             // Delete all child messages first
             $message_selection_child = new WP_Query($args_child);
             while ($message_selection_child->have_posts()): $message_selection_child->the_post();
                 $delete_id=get_the_ID();
                 print 'delete '.$delete_id;
                 wp_delete_post($delete_id);
             endwhile;
             print 'end delete';
             print 'sss delete'.$messid;
             
             // Delete the parent message
             wp_delete_post($messid);
             wp_reset_query();
             wp_reset_post_data();
         }
  
         die();
     }
 endif;
  
  
  
 /**
  * AJAX handler for front-end message submission
  *
  * Processes messages sent from property listings, handling both regular messages
  * and booking inquiries. Creates message posts and sends notifications.
  *
  * @uses wp_get_current_user() To get sender information
  * @uses wpsestate_get_author() To determine property owner
  * @uses wpestate_add_to_inbox() To create the message record
  * 
  * @return void Sends confirmation message and dies
  */
 add_action('wp_ajax_nopriv_wpestate_mess_front_end', 'wpestate_mess_front_end');
 add_action('wp_ajax_wpestate_mess_front_end', 'wpestate_mess_front_end');
 if( !function_exists('wpestate_mess_front_end') ):
     function wpestate_mess_front_end(){
         // Security note: nonce check is commented out
         // check_ajax_referer( 'mess_ajax_nonce_front', 'security-register' );
         
         // Get current user information
         $current_user = wp_get_current_user();
         $allowed_html       =   array();
         $userID             =   $current_user->ID;
         $user_login         =   $current_user->user_login;
         $subject            =   esc_html__( 'Message from ','wpresidence-core').$user_login;
         
         // Get message content and property/agent information
         $message_from_user       =   esc_html($_POST['message']);
         $property_id        =   intval ( $_POST['agent_property_id']);
         $agent_id           =   intval ( $_POST['agent_id'] );
  
         // Determine message recipient (property owner or agent)
         if($agent_id === 0){
             $owner_id           =   wpsestate_get_author($property_id);
         }else{
             $owner_id           =   get_post_meta($agent_id, 'user_agent_id', true);
         }
  
         // Get recipient details
         $owner              =   get_userdata($owner_id);
         $owner_email        =   $owner->user_email;
         $owner_login        =   $owner->ID;
         $subject            =   esc_html__( 'Message from ','wpresidence-core').$user_login;
  
         // Get booking details if applicable
         $booking_guest_no   =   intval  ( $_POST['booking_guest_no'] );
         $booking_from_date  =   wp_kses ( $_POST['booking_from_date'],$allowed_html  );
         $booking_to_date    =   wp_kses ( $_POST['booking_to_date'],$allowed_html  );
  
         // Construct message content with property information if available
         if($property_id!=0 && get_post_type($property_id) === 'estate_property' ){
             $message_user .= esc_html__(' Sent for property ','wpresidence-core').get_the_title($property_id).', '.esc_html__('with the link:','wpresidence-core').' '. esc_url( get_permalink($property_id) ).' ';
         }
         
         // Add booking details to message
         $message_user .=    esc_html__( 'Selected dates: ','wpresidence-core').$booking_from_date.esc_html__( ' to ','wpresidence-core').$booking_to_date.", ".esc_html__( ' guests:','wpresidence-core').$booking_guest_no." ".esc_html__('Content','wpresidence-core').": ".$message_from_user;
  
         // Add message to inbox system
         wpestate_add_to_inbox($userID,$userID,$owner_login,$subject,$message_user,1);
  
         // Send success message
         esc_html_e('Your message was sent! You will be notified by email when a reply is received.','wpresidence-core');
         die();
     }
 endif;
  
  
  
 /**
  * Calculates and updates unread message count for current user
  *
  * Performs a complex query to find all unread messages for the current user
  * that haven't been deleted, and updates the user meta with the count.
  *
  * @global object $current_user The current WordPress user object
  * @uses WP_Query To query for unread messages
  * @uses update_user_meta() To save the unread message count
  * 
  * @return void Updates user meta with unread message count
  */
 if(!function_exists('wpestate_calculate_new_mess')):
     function wpestate_calculate_new_mess(){
         global $current_user;
         $current_user = wp_get_current_user();
         $userID                         =   $current_user->ID;
  
         // Complex query to find unread messages for current user
         $args_mess = array(
                   'post_type'         => 'wpestate_message',
                   'post_status'       => 'publish',
                   'posts_per_page'    => -1,
                   'order'             => 'DESC',
  
                   'meta_query' => array(
                                       'relation' => 'AND',
                                       array(
                                           'relation' => 'OR',
                                           array(
                                                   'key'       => 'message_to_user',
                                                   'value'     => $userID,
                                                   'compare'   => '='
                                           ),
                                           array(
                                                   'key'       => 'message_from_user',
                                                   'value'     => $userID,
                                                   'compare'   => '='
                                           ),
                                       ),
                                       array(
                                           'key'       => 'first_content',
                                           'value'     => 1,
                                           'compare'   => '='
                                       ),
                                       array(
                                           'key'       => 'delete_destination'.$userID,
                                           'value'     => 1,
                                           'compare'   => '!='
                                       ),
                                       array(
                                           'key'       =>  'message_status'.$userID,
                                           'value'     => 'unread',
                                           'compare'   => '=='
                                       ),
                               )
           );
  
      $args_mess_selection = new WP_Query($args_mess);
  
         // Update user meta with count of unread messages
         update_user_meta($userID,'unread_mess',$args_mess_selection->found_posts);
         //return $args_mess_selection->found_posts;
     }
 endif;
  
  
 /**
  * Increments the unread message counter for a user
  *
  * Helper function to increase the unread message count in user meta
  * when a new message is received. Used by message creation functions.
  *
  * @param int $userID The ID of the user receiving the message
  * @uses get_user_meta() To retrieve current count
  * @uses update_user_meta() To save the incremented count
  * 
  * @return void Updates the unread message count in user meta
  */
 if(!function_exists('wpestate_increment_mess_mo')):
     function wpestate_increment_mess_mo($userID){
        $unread =   intval ( get_user_meta($userID,'unread_mess',true)) +1;
         update_user_meta($userID,'unread_mess',$unread);
     }
 endif;
  
  
 /**
  * Creates a new message in the inbox system
  *
  * Core function for message creation that handles all metadata setup
  * for proper message tracking, status updates, and notification.
  *
  * @param int $userID User ID creating the message (typically current user)
  * @param int $from Sender user ID
  * @param int $to Recipient user ID
  * @param string $subject Message subject line
  * @param string $description Message content/body
  * @param string|int $first_content Whether this is first message (1) or reply
  * 
  * @uses wp_insert_post() To create the message post
  * @uses update_post_meta() To set message metadata
  * @uses wpestate_increment_mess_mo() To update unread counts
  * 
  * @return void Creates message post with appropriate metadata
  */
 if( !function_exists('wpestate_add_to_inbox') ):
     function wpestate_add_to_inbox($userID,$from,$to,$subject,$description,$first_content=''){
  
         // Format subject line with sender information
         if($subject!=''){
             $subject = $subject.' '.$from;
         }else{
             $subject = esc_html__( 'Message from ','wpresidence-core').$from;
         }
  
         // Get sender user object for display name
         $user = get_user_by( 'id',$from );
  
         // Create the message post
         $post = array(
             'post_title'	=> esc_html__( 'Message from ','wpresidence-core').$user->user_login,
             'post_content'	=> $description,
             'post_status'	=> 'publish',
             'post_type'         => 'wpestate_message' ,
             'post_author'       => $userID
         );
         $post_id =  wp_insert_post($post );
         
         // Set up standard message metadata
         update_post_meta($post_id, 'mess_status', 'new' );
         update_post_meta($post_id, 'message_from_user', $from );
         update_post_meta($post_id, 'message_to_user', $to );
         
         // Increase unread message count for recipient
         wpestate_increment_mess_mo($to);
         
         // Initialize deletion flags for both users
         update_post_meta($post_id, 'delete_destination'.$from, 0 );
         update_post_meta($post_id, 'delete_destination'.$to, 0 );
         update_post_meta($post_id, 'message_status', 'unread');
         update_post_meta($post_id, 'delete_source', 0);
         update_post_meta($post_id, 'delete_destination', 0);
         
         // Set first_content flag if this is a new conversation
         if($first_content!=''){
             update_post_meta($post_id, 'first_content', 1);
             update_post_meta($post_id, 'message_status'.$to, 'unread' );
         }
     }
 endif;

