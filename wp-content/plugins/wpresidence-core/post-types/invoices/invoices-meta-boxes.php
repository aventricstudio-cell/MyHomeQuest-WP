<?php
/**
 * WPEstate Invoice Metabox Management
 * 
 * This file contains functions that handle the display and management of
 * custom metaboxes for the wpestate_invoice custom post type. It allows
 * administrators to view and edit invoice details through the WordPress
 * admin interface.
 * 
 * The file provides:
 * - Registration of the invoice details metabox
 * - Display and formatting of invoice information
 * - Manual activation controls for wire payments
 * - Display of payment status
 * 
 * @package WPResidence
 * @subpackage Invoicing
 * @version 1.0
 */

////////////////////////////////////////////////////////////////////////////////////////////////
// Add Invoice metaboxes
////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_add_pack_invoices') ):
    /**
     * Register the invoice details metabox
     * 
     * This function hooks into WordPress metabox system to add a custom metabox
     * that displays invoice details for the wpestate_invoice post type.
     * 
     * @uses add_meta_box() WordPress function to register a metabox
     * @return void
     */
    function wpestate_add_pack_invoices() {
            add_meta_box(  'estate_invoice-sectionid',  esc_html__( 'Invoice Details', 'wpresidence-core' ),'wpestate_invoice_details','wpestate_invoice' ,'normal','default');
    }
    endif; // end   wpestate_add_pack_invoices
    
    
    
    ////////////////////////////////////////////////////////////////////////////////////////////////
    // Invoice Details
    ////////////////////////////////////////////////////////////////////////////////////////////////
    if( !function_exists('wpestate_invoice_details') ):
    
    /**
     * Display invoice details in the metabox
     * 
     * This callback function renders the content of the invoice details metabox.
     * It shows information about the invoice including:
     * - Invoice ID and payment status
     * - Controls for manual activation of purchases
     * - Billing details (type, period, item, price)
     * - Purchase date and buyer information
     * - PayPal transaction ID for recurring payments
     * 
     * @param WP_Post $post The current post object (invoice)
     * @return void Outputs HTML directly
     */
    function wpestate_invoice_details( $post ) {
        global $post;
        // Security nonce for verification
        wp_nonce_field( plugin_basename( __FILE__ ), 'estate_invoice_noncename' );

        // Define possible invoice types
        $invoice_types      = array( 'Listing', 'Upgrade to Featured', 'Publish Listing with Featured', 'Package' );
        $invoice_saved      = esc_html( get_post_meta( $post->ID, 'invoice_type', true ) );

        // Determine the numeric purchase type based on the invoice type
        $purchase_type = 0;
        if ( $invoice_saved == 'Listing' ) {
            $purchase_type = 1;
        } elseif ( $invoice_saved == 'Upgrade to Featured' ) {
            $purchase_type = 2;
        } elseif ( $invoice_saved == 'Publish Listing with Featured' ) {
            $purchase_type = 3;
        }

        // Define billing period options and get saved value
        $invoice_period       = array( 'One Time', 'Recurring' );
        $invoice_period_saved = esc_html( get_post_meta( $post->ID, 'biling_type', true ) );

        // Get PayPal transaction ID if available
        $txn_id = esc_html( get_post_meta( $post->ID, 'txn_id', true ) );

        $available_tabs = array( 'invoice_details' );
        $active_tab     = 'invoice_details';
        if ( isset( $_GET['invoice_tab'] ) && in_array( $_GET['invoice_tab'], $available_tabs, true ) ) {
            $active_tab = sanitize_key( $_GET['invoice_tab'] );
        }

        print '<div class="property_options_wrapper meta-options">'
            .'<div class="property_options_wrapper_list">';
                print '<div class="property_tab_item'.( $active_tab === 'invoice_details' ? ' active_tab' : '' ).'" data-content="invoice_details">'.esc_html__('Invoice Details','wpresidence-core').'</div>';
        print '</div><div class="property_options_content_wrapper">';

                print '<div class="property_tab_item_content'.( $active_tab === 'invoice_details' ? ' active_tab' : '' ).'" id="invoice_details">';

       

                if ( get_post_meta( $post->ID, 'pay_status', true ) == 0 ) {
                    if ( $invoice_saved == 'Package' ) {
                        print '    <div class="property_prop_half prop_full">'
                                    .'<div id="activate_pack" class="button button-primary" style="max-width:300px;" data-invoice="'.$post->ID.'" data-item="'.get_post_meta( $post->ID, 'item_id', true ).'"> Wire Payment Received - Activate the purchase</div>';
                        $ajax_nonce = wp_create_nonce( 'wpestate_activate_pack' );
                        print         '<input type="hidden" id="wpestate_activate_pack" value="'.esc_html( $ajax_nonce ).'" />'
                                .'</div>';
                    } else {
                        print '    <div class="property_prop_half">'
                                    .'<div id="activate_pack_listing" class="button button-primary" data-invoice="'.$post->ID.'" data-item="'.get_post_meta( $post->ID, 'item_id', true ).'" data-type="'.$purchase_type.'"> Wire Payment Received - Activate the purchase</div>';
                        $ajax_nonce = wp_create_nonce( 'wpestate_activate_pack_listing' );
                        print         '<input type="hidden" id="wpestate_activate_pack_listing" value="'.esc_html( $ajax_nonce ).'" />'
                                .'</div>';
                    }

                    print '    <div class="property_prop_half" id="invnotpaid">'
                                .'<strong>'.esc_html__('Invoice NOT paid','wpresidence-core').' </strong>'
                            .'</div>';
                } else {
                    print '    <div class="property_prop_half">'
                                .'<strong>'.esc_html__('Invoice PAID','wpresidence-core').' </strong>'
                            .'</div>';
                }

                print '    <div class="property_prop_half">'
                            .'<label for="biling_period">'.esc_html__('Billing For :','wpresidence-core').' <strong> '.$invoice_saved.' </strong> </label> '
                        .'</div>';

                print '    <div class="property_prop_half">'
                            .'<label for="biling_type">'.esc_html__('Billing Type :','wpresidence-core').' <strong>'.$invoice_period_saved.'</strong></label>'
                        .'</div>';

                print '    <div class="property_prop_half">'
                            .'<label for="item_id">'.esc_html__('Item Id (Listing or Package id)','wpresidence-core').'</label><br />'
                            .'<input type="text" id="item_id" name="item_id" value="'.esc_html( get_post_meta( $post->ID, 'item_id', true ) ).'">'
                        .'</div>';

                print '    <div class="property_prop_half">'
                            .'<label for="item_price">'.esc_html__('Item Price','wpresidence-core').'</label><br />'
                            .'<input type="text" id="item_price" name="item_price" value="'.esc_html( get_post_meta( $post->ID, 'item_price', true ) ).'">'
                        .'</div>';

                $purchase_date = esc_html( get_post_meta( $post->ID, 'purchase_date', true ) );
                $time_unix     = strtotime( $purchase_date );
                $formatted     = gmdate( 'Y-m-d H:i:s', ( $time_unix + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) );
                print '    <div class="property_prop_half">'
                            .'<label for="purchase_date">'.esc_html__('Purchase Date','wpresidence-core').'</label><br />'
                            .'<input type="text" id="purchase_date" name="purchase_date" value="'.$formatted.'">'
                        .'</div>';

                print '    <div class="property_prop_half">'
                            .'<label for="buyer_id">'.esc_html__('User Id','wpresidence-core').'</label><br />'
                            .'<input type="text" id="buyer_id" name="buyer_id" value="'.esc_html( get_post_meta( $post->ID, 'buyer_id', true ) ).'">'
                        .'</div>';

                if ( $txn_id != '' ) {
                    print '    <div class="property_prop_half">'
                                .esc_html__('Paypal - Reccuring Payment ID: ','wpresidence-core').$txn_id.
                            '</div>';
                }

                print '</div>';

        print '</div></div>';
    }

    endif; // end   wpestate_invoice_details
