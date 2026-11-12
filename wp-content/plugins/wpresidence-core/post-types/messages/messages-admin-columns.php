<?php
/**
 * WpResidence Message Admin Columns
 *
 * This file contains functions that customize the WordPress admin columns
 * for the custom post type 'wpestate_message'. It adds sender and recipient
 * columns to improve message management in the admin dashboard.
 *
 * @package    WpResidence
 * @subpackage Messaging
 * @version    1.0
 * @author     WpResidence
 */

/**
 * Modifies the columns displayed in the message list table
 *
 * This function customizes the admin columns for the 'wpestate_message' post type.
 * It removes the comments column and adds 'From' and 'To' columns to display
 * message sender and recipient information.
 *
 * @param array $columns The default WordPress admin columns
 * @return array Modified array of columns for the messages admin page
 */
add_filter( 'manage_edit-wpestate_message_columns', 'wpestate_my_mess_columns' );
if( !function_exists('wpestate_my_mess_columns') ):
    function wpestate_my_mess_columns( $columns ) {
        // Extract a portion of the columns array to preserve certain columns
        $slice=array_slice($columns,2,2);
        
        // Remove the comments column from both arrays
        unset( $columns['comments'] );
        unset( $slice['comments'] );
        
        // Split the array to insert new columns at specific position
        $splice=array_splice($columns, 2);
        
        // Add new custom columns for message sender and recipient
        $columns['mess_from_who']= esc_html__( 'From','wpresidence-core');
        $columns['mess_to_who']  = esc_html__( 'To','wpresidence-core');
        
        // Merge arrays to create final column structure
        // This preserves WordPress core columns while adding custom ones
        return  array_merge($columns,array_reverse($slice));
    }
endif; // end   wpestate_my_columns

/**
 * Populates the custom columns with message sender and recipient data
 *
 * This function retrieves and displays the username of message senders and
 * recipients in the respective admin columns. It converts user IDs stored in
 * post meta to readable usernames.
 *
 * @param string $column The current column being rendered
 * @return void Outputs column content directly
 */
add_action( 'manage_posts_custom_column', 'wpestate_populate_messages_columns' );
if( !function_exists('wpestate_populate_messages_columns') ):
    function wpestate_populate_messages_columns( $column ) {
    // Get the current post ID
    $the_id=get_the_ID();
    
    // Retrieve sender and recipient user IDs from post meta
    $from_value =   esc_html(get_post_meta($the_id, 'message_from_user', true));
    $to_val     =   esc_html(get_post_meta($the_id, 'message_to_user', true));
    
    // Handle the 'From' column
    if( 'mess_from_who' == $column){
     if(intval($from_value)!=0){
        // If sender is a registered user (has valid ID), display username
        $user = get_user_by( 'id', $from_value );
        echo $user->user_login;
     }else{
        // If sender is not a registered user, display the raw value
        // This handles cases where messages might come from external sources
        echo $from_value;
     }
    }
    
    // Handle the 'To' column - always display recipient username
    if( 'mess_to_who' == $column){
        $user = get_user_by( 'id', $to_val );
        echo $user->user_login;
    }
    }
endif;