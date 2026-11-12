<?php
/**
 * WP Estate User Membership Upgrade Functions
 * 
 * This file contains functionality for upgrading a user's membership package
 * within the WP Estate real estate platform. It handles the process of
 * transitioning users from one membership package to another, including
 * calculating new listing allowances, updating user meta, and generating
 * appropriate invoices and notifications.
 * 
 * @package WP Estate
 * @subpackage Membership
 * @since 1.0.0
 */

/////////////////////////////////////////////////////////////////////////////////////
/// upgrade user
/////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_upgrade_user_membership') ):
    /**
     * Upgrades a user to a new membership package
     * 
     * This function performs all necessary operations to upgrade a user to a new
     * membership package. It calculates the new listing allowances (considering
     * currently used listings), updates user meta with new package details,
     * sends notification emails, and generates an invoice for the transaction.
     * 
     * The function handles both upgrades and downgrades, with special logic to
     * prevent negative listing allowances when downgrading.
     *
     * @param int $user_id The ID of the user being upgraded
     * @param int $pack_id The ID of the new package
     * @param string $type The payment type (e.g., 'Paypal', 'Stripe', etc.)
     * @param string $paypal_tax_id The PayPal transaction ID (if applicable)
     * @param int $direct_pay Whether the payment was made directly (1) or via a gateway (0)
     * @return void
     */
    function wpestate_upgrade_user_membership($user_id,$pack_id,$type,$paypal_tax_id,$direct_pay=0){
   
            // Get new package limits
            $available_listings                  =   floatval( get_post_meta($pack_id, 'pack_listings', true) );
            $featured_available_listings         =   floatval( get_post_meta($pack_id, 'pack_featured_listings', true));
            $pack_unlimited_list                 =   get_post_meta($pack_id, 'mem_list_unl', true);
            $available_images                    =   floatval( get_post_meta($pack_id, 'pack_image_included', true));
   
        // Get current user package details
        $current_used_listings               =   get_user_meta($user_id, 'package_listings',true);
        $curent_used_featured_listings       =   get_user_meta($user_id, 'package_featured_listings',true);
        $curent_images                       =   get_user_meta($user_id, 'pack_image_included',true);
        $current_pack                        =   get_user_meta($user_id, 'package_id',true);
   
        // Count user's current active listings
        $user_curent_listings                   =   wpestate_get_user_curent_listings_no_exp ( $user_id );
        $user_curent_future_listings            =   wpestate_get_user_curent_future_listings_no_exp( $user_id );
   
        // Check if this is a downgrade situation
        if( wpestate_check_downgrade_situation($user_id,$pack_id) ){
            // If downgrading, simply set new limits from package (don't add to existing)
            $new_listings           =   $available_listings;
            $new_featured_listings  =   $featured_available_listings;
        }else{
            // If upgrading, calculate new limits by subtracting current usage from new package limits
            $new_listings            =  $available_listings - $user_curent_listings ;
            $new_featured_listings   =  $featured_available_listings-  $user_curent_future_listings ;
        }
   
        // Ensure we don't have negative values for listing allowances
        // in case of downgrade
        if($new_listings<0){
            $new_listings=0;
        }
   
        if($new_featured_listings<0){
            $new_featured_listings=0;
        }
   
        // Handle unlimited listings package
        if ($pack_unlimited_list==1){
            $new_listings=-1;  // -1 indicates unlimited listings
        }
   
        // Update user meta with new package details
        update_user_meta( $user_id, 'package_listings', $new_listings) ;
        update_user_meta( $user_id, 'package_featured_listings', $new_featured_listings);
        update_user_meta( $user_id, 'pack_image_included', $available_images);
   
        // Set package activation time
        $time = time();
        $date = date('Y-m-d H:i:s',$time);
        update_user_meta( $user_id, 'package_activation', $date);
        update_user_meta( $user_id, 'package_id', $pack_id);
        
        // Get user email for notification
        $user = get_user_by('id',$user_id);
        $user_email=$user->user_email;
   
        // Send membership activation email
        $arguments=array();
        wpestate_select_email_type($user_email,'membership_activated',$arguments);
   
        $billing_for='Package';
   
        // Create invoice if payment was through a gateway (not direct payment)
        if($direct_pay==0){
            $invoice_id=wpestate_insert_invoice($billing_for,$type,$pack_id,$date,$user_id,0,0,$paypal_tax_id);
            update_post_meta($invoice_id, 'pay_status', 1);
         
        }
    }
   
    endif; // end   wpestate_upgrade_user_membership