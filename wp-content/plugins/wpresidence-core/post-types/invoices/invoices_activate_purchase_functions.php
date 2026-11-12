<?php 

/**
 * Admin Listing Purchase Activation Function
 * 
 * Handles the activation of purchased listings when an admin manually approves payment.
 * Updates listing status based on the payment type (standard listing, feature upgrade, 
 * or both) and notifies the user via email.
 *
 * @package WP Estate
 * @subpackage Payments
 * @return void
 */
add_action('wp_ajax_wpestate_activate_purchase_listing', 'wpestate_activate_purchase_listing');

if(!function_exists('wpestate_activate_purchase_listing')):
    function wpestate_activate_purchase_listing(){
    	// Verify security nonce
        check_ajax_referer('wpestate_activate_pack_listing', 'security');
        
        // Check if user is logged in
        if (!is_user_logged_in()) {
            exit('out pls');
        }

        // Verify administrator permissions
        if(!current_user_can('administrator')){
            exit('out pls');
        }

        // Get invoice and item details
        $item_id    = intval($_POST['item_id']);
        $invoice_id = intval($_POST['invoice_id']);
        $type       = intval($_POST['type']);
        $owner_id   = get_post_meta($invoice_id, 'buyer_id', true);

        // Get user information for email notification
        $user       = get_user_by('id', $owner_id);
        $user_email = $user->user_email;

        // Process based on payment type
        if ($type == 1) { // Standard Listing payment
            // Mark listing as paid and publish it
            update_post_meta($item_id, 'pay_status', 'paid');
            $post = array(
                'ID'          => $item_id,
                'post_status' => 'publish'
            );
            $post_id = wp_update_post($post);

        } elseif ($type == 2) { // Upgrade to Featured
            // Set listing as featured
            update_post_meta($item_id, 'prop_featured', 1);

        } elseif ($type == 3) { // Publish Listing with Featured status
            // Mark as paid, set as featured, and publish
            update_post_meta($item_id, 'pay_status', 'paid');
            update_post_meta($item_id, 'prop_featured', 1);
            $post = array(
                'ID'          => $item_id,
                'post_status' => 'publish'
            );
            $post_id = wp_update_post($post);
        }

        // Update invoice status to paid
        update_post_meta($invoice_id, 'pay_status', 1);
        
        // Send notification email to user
        $arguments = array();
        wpestate_select_email_type($user_email, 'purchase_activated', $arguments);
    }
endif;

/**
 * Membership Package Direct Payment Function
 * 
 * Processes a wire transfer payment request for a membership package.
 * Creates an invoice, marks it as unpaid, and sends notification emails
 * to both the user and the admin with payment instructions.
 *
 * @package WP Estate
 * @subpackage Payments
 * @return void
 */
add_action('wp_ajax_wpestate_direct_pay_pack', 'wpestate_direct_pay_pack');

if(!function_exists('wpestate_direct_pay_pack')):
    function wpestate_direct_pay_pack(){
        // Verify security nonce
        check_ajax_referer('wpresidence_simple_pay_actions_nonce', 'security');
        
        // Get current user and verify logged in status
        $current_user = wp_get_current_user();
        if (!is_user_logged_in()) {
            exit('out pls');
        }

        // Get user and payment information
        $userID         = $current_user->ID;
        $user_email     = $current_user->user_email;
        $selected_pack  = intval($_POST['selected_pack']);
        $total_price    = get_post_meta($selected_pack, 'pack_price', true);
        
        // Format total price with currency symbol
        $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
        $where_currency    = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));

        if ($total_price != 0) {
            if ($where_currency == 'before') {
                $total_price = $wpestate_currency . $total_price;
            } else {
                $total_price = $total_price . $wpestate_currency;
            }
        }

        // Create invoice
        $time          = time();
        $date          = date('Y-m-d H:i:s', $time);
        $is_featured   = 0;
        $is_upgrade    = 0;
        $paypal_tax_id = '';
     
        $invoice_no = wpestate_insert_invoice('Package', 'One Time', $selected_pack, $date, $userID, $is_featured, $is_upgrade, $paypal_tax_id);

        // Get payment instructions with translation support
        $headers = 'From: ' . wpestate_return_sending_email() . "\r\n";
        $message = esc_html__('Hi there,', 'wpresidence-core') . "\r\n\r\n";

        if (function_exists('icl_translate')) {
            $mes = strip_tags(wpresidence_get_option('wp_estate_direct_payment_details', ''));
            $payment_details = icl_translate('wpestate', 'wp_estate_property_direct_payment_text', $mes);
        } else {
            $payment_details = strip_tags(wpresidence_get_option('wp_estate_direct_payment_details', ''));
        }

        // Mark invoice as unpaid initially
        update_post_meta($invoice_no, 'pay_status', 0);
        
        // Prepare email arguments
        $arguments = array(
            'invoice_no'      => $invoice_no,
            'total_price'     => $total_price,
            'payment_details' => $payment_details,
        );

        // Send notification emails
        wpestate_select_email_type($user_email, 'new_wire_transfer', $arguments);
        $company_email = get_bloginfo('admin_email');
        wpestate_select_email_type($company_email, 'admin_new_wire_transfer', $arguments);
    }
endif;




/**
 * Per-Listing Payment Processing Function
 * 
 * Handles the direct payment process for individual property listings.
 * This function processes payment requests, generates invoices, and sends notification emails
 * when a user chooses to pay for listing a single property.
 *
 * @package WP Estate
 * @subpackage Payments
 * @return void
 */
add_action('wp_ajax_wpestate_direct_pay_pack_per_listing', 'wpestate_direct_pay_pack_per_listing');

if (!function_exists('wpestate_direct_pay_pack_per_listing')):
    function wpestate_direct_pay_pack_per_listing(){
        // Verify security nonce
        check_ajax_referer('wpresidence_simple_pay_actions_nonce', 'security');
        
        // Get current user and verify logged in status
        $current_user = wp_get_current_user();
        if (!is_user_logged_in()) {
            exit('out pls');
        }

        // Get user information
        $userID = $current_user->ID;
        $user_email = $current_user->user_email;
        
        // Get payment details from POST data
        $listing_id = intval($_POST['selected_pack']);
        $include_feat = intval($_POST['include_feat']);
        $pay_status = get_post_meta($listing_id, 'pay_status', true);
        
        // Get pricing from options
        $price_submission = floatval(wpresidence_get_option('wp_estate_price_submission', ''));
        $price_featured_submission = floatval(wpresidence_get_option('wp_estate_price_featured_submission', ''));

        // Initialize variables
        $total_price = 0;
        $time = time();
        $date = date('Y-m-d H:i:s', $time);

        // Process payment based on featured status and current payment status
        if ($include_feat == 1) {
            if ($pay_status == 'paid') {
                // Already paid listing, just upgrading to featured
                $invoice_no = wpestate_insert_invoice('Upgrade to Featured', 'One Time', $listing_id, $date, $current_user->ID, 0, 1, '');
                wpestate_email_to_admin(1);
                $total_price = $price_featured_submission;
            } else {
                // New listing with featured status
                $invoice_no = wpestate_insert_invoice('Publish Listing with Featured', 'One Time', $listing_id, $date, $current_user->ID, 1, 0, '');
                wpestate_email_to_admin(0);
                $total_price = $price_submission + $price_featured_submission;
            }
        } else {
            // Standard listing without featured status
            $invoice_no = wpestate_insert_invoice('Listing', 'One Time', $listing_id, $date, $current_user->ID, 0, 0, '');
            wpestate_email_to_admin(0);
            $total_price = $price_submission;
        }

        // Format total price with currency symbol
        $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
        $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
        if ($total_price != 0) {
            if ($where_currency == 'before') {
                $total_price = $wpestate_currency . $total_price;
            } else {
                $total_price = $total_price . $wpestate_currency;
            }
        }

        // Prepare email content with wire transfer instructions
        $headers = 'From: ' . wpestate_return_sending_email() . "\r\n";
        $message = esc_html__('Hi there,', 'wpresidence-core') . "\r\n\r\n";
        $message .= sprintf(esc_html__("We received your Wire Transfer payment request on %s ! Please follow the instructions below in order to start submitting properties as soon as possible.", 'wpresidence-core'), get_option('blogname')) . "\r\n\r\n";
        $message .= esc_html__('The invoice number is: ', 'wpresidence-core') . $invoice_no . " " . esc_html__('Amount:', 'wpresidence-core') . ' ' . $total_price . "\r\n\r\n";
        $message .= esc_html__('Instructions: ', 'wpresidence-core') . "\r\n\r\n";

        // Get payment instructions with translation support
        if (function_exists('icl_translate')) {
            $mes = strip_tags(wpresidence_get_option('wp_estate_direct_payment_details', ''));
            $payment_details = icl_translate('wpestate', 'wp_estate_property_direct_payment_text', $mes);
        } else {
            $payment_details = strip_tags(wpresidence_get_option('wp_estate_direct_payment_details', ''));
        }

        $message .= $payment_details;

        // Mark invoice as unpaid initially
        update_post_meta($invoice_no, 'pay_status', 0);

        // Send emails to user and admin
        $arguments = array(
            'invoice_no' => $invoice_no,
            'total_price' => $total_price,
            'payment_details' => $payment_details,
        );
        wpestate_select_email_type($user_email, 'new_wire_transfer', $arguments);
        $company_email = get_bloginfo('admin_email');
        wpestate_select_email_type($company_email, 'admin_new_wire_transfer', $arguments);

        die();
    }
endif;

/**
 * Activate Purchase for Membership Packages
 * 
 * Admin-only function to manually activate a membership package purchase.
 * This is typically used when manual payment verification is required,
 * such as with wire transfers or other offline payment methods.
 *
 * @package WP Estate
 * @subpackage Payments
 * @return void
 */
add_action('wp_ajax_wpestate_activate_purchase', 'wpestate_activate_purchase');

if (!function_exists('wpestate_activate_purchase')):
    function wpestate_activate_purchase(){
        // Verify security nonce
        check_ajax_referer('wpestate_activate_pack', 'security');
        
        // Check if user is logged in and has administrator permissions
        if (!is_user_logged_in()) {
            exit('out pls');
        }
        if (!current_user_can('administrator')) {
            exit('out pls');
        }

        // Get pack and invoice details
        $pack_id = intval($_POST['item_id']);
        $invoice_id = intval($_POST['invoice_id']);
        $userID = get_post_meta($invoice_id, 'buyer_id', true);

        // Handle downgrade situations if necessary
        if (wpestate_check_downgrade_situation($userID, $pack_id)) {
            wpestate_downgrade_to_pack($userID, $pack_id);
            wpestate_upgrade_user_membership($userID, $pack_id, 1, '', 1);
        } else {
            // Normal upgrade process
            wpestate_upgrade_user_membership($userID, $pack_id, 1, '', 1);
        }
        
        // Mark invoice as paid
        update_post_meta($invoice_id, 'pay_status', 1);
    }
endif;