<?php
/**
 * WPEstate Invoice Column Management
 * 
 * This file manages the display and sorting of custom columns for
 * the wpestate_invoice custom post type in the WordPress admin area.
 * It handles three main tasks:
 * 1. Adding custom columns to the invoice list view
 * 2. Populating those columns with relevant invoice data
 * 3. Making those columns sortable in the admin interface
 * 
 * @package WPResidence
 * @subpackage Invoicing
 * @version 1.0
 */

/////////////////////////////////////////////////////////////////////////////////////
/// populate the invoice list with extra columns
/////////////////////////////////////////////////////////////////////////////////////

/**
 * Add custom columns to the invoice admin list view
 * 
 * This filter modifies the default WordPress columns for the wpestate_invoice
 * post type by adding custom columns specific to invoice management.
 * 
 * @param array $columns Default WordPress admin columns
 * @return array Modified column array with custom invoice columns
 */
add_filter( 'manage_edit-wpestate_invoice_columns', 'wpestate_invoice_my_columns' );
if( !function_exists('wpestate_invoice_my_columns') ):
function wpestate_invoice_my_columns( $columns ) {
    // Store a slice of the columns array to preserve some original columns
    $slice=array_slice($columns,2,2);
    
    // Remove the comments column as it's not needed for invoices
    unset( $columns['comments'] );
    unset( $slice['comments'] );
    
    // Remove columns after position 2 to insert our custom columns
    $splice=array_splice($columns, 2);
    
    // Add custom invoice columns
    $columns['invoice_price']   = esc_html__('Price','wpresidence-core');
    $columns['invoice_for']     = esc_html__('Billing For','wpresidence-core');
    $columns['invoice_type']    = esc_html__('Invoice Type','wpresidence-core');
    $columns['invoice_user']    = esc_html__('Purchased by User','wpresidence-core');
    $columns['invoice_status']  = esc_html__('Status','wpresidence-core');
    
    // Merge our columns with reversed slice to maintain column order
    return  array_merge($columns,array_reverse($slice));
}
endif; // end   wpestate_invoice_my_columns

/**
 * Populate custom invoice columns with data
 * 
 * This action callback retrieves and displays the appropriate data
 * for each custom column in the invoice admin list view.
 * 
 * @param string $column The current column being processed
 * @return void Outputs column content directly
 */
add_action( 'manage_posts_custom_column', 'wpestate_invoice_populate_columns' );
if( !function_exists('wpestate_invoice_populate_columns') ):
function wpestate_invoice_populate_columns( $column ) {
     $the_id=get_the_ID();
     
     // Display the invoice price
     if ( 'invoice_price' == $column ) {
        echo get_post_meta($the_id, 'item_price', true);
    }
    
    // Display what the invoice is for (package, listing, etc.)
    if ( 'invoice_for' == $column ) {
         echo get_post_meta($the_id, 'invoice_type', true);
    }
    
    // Display the billing type (one-time or recurring)
    if ( 'invoice_type' == $column ) {
        echo get_post_meta($the_id, 'biling_type', true);
    }
    
    // Display the username of the buyer
    if ( 'invoice_user' == $column ) {
        // Get user ID from invoice metadata
        $user_id= get_post_meta($the_id, 'buyer_id', true);
        
        // Get user data object
        $user_info = get_userdata($user_id);
        
        // Check if user exists before displaying username
        if(isset($user_info->user_login)){
            echo esc_html($user_info->user_login);
        }
    }
    
    // Display payment status (Paid/Not Paid)
    if ( 'invoice_status' == $column ) {
        $stat=get_post_meta($the_id, 'pay_status', 1);
        if($stat==0){
            esc_html_e('Not Paid','wpresidence-core');
        }else{
            esc_html_e('Paid','wpresidence-core');
        }
    }
}
endif; // end   wpestate_invoice_populate_columns

/**
 * Make invoice columns sortable
 * 
 * This filter adds sorting capability to the custom invoice columns
 * in the admin list view.
 * 
 * @param array $columns Default sortable columns
 * @return array Modified array with sortable invoice columns
 */
add_filter( 'manage_edit-wpestate_invoice_sortable_columns', 'wpestate_invoice_sort_me' );
if( !function_exists('wpestate_invoice_sort_me') ):
function wpestate_invoice_sort_me( $columns ) {
    // Define which custom columns should be sortable
    // The key is the column ID, the value is the meta_key to sort by
    $columns['invoice_price']   = 'invoice_price';
    $columns['invoice_user']    = 'invoice_user';
    $columns['invoice_for']     = 'invoice_for';
    $columns['invoice_type']    = 'invoice_type';
    $columns['invoice_status']  = 'invoice_status';
    return $columns;
}
endif; // end   wpestate_invoice_sort_me