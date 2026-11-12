<?php 
/**
 * Invoice Management Functions
 * 
 * This file contains functions related to invoice creation, management,
 * and printing for the WPResidence real estate plugin.
 * 
 * Functions include:
 * - wpestate_insert_invoice: Creates a new invoice in the system
 * - wpestate_ajax_create_print_invoice: Generates a printable invoice
 * 
 * @package WPResidence
 * @subpackage Invoicing
 * @version 1.0
 */


/////////////////////////////////////////////////////////////////////////////////////
/// insert invoice
/////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('wpestate_insert_invoice') ):
    /**
     * Creates a new invoice in the system
     * 
     * This function generates a new invoice post of type 'wpestate_invoice' with
     * associated metadata based on the billing type, package, and user information.
     * 
     * @param string $billing_for   What the invoice is for (e.g., 'Package', 'Listing')
     * @param int    $type          Billing type (1 for one-time, 2 for recurring)
     * @param int    $pack_id       ID of the package being purchased (if applicable)
     * @param string $date          Purchase date
     * @param int    $user_id       ID of the user making the purchase
     * @param int    $is_featured   Whether the listing is featured (1) or not (0)
     * @param int    $is_upgrade    Whether this is an upgrade (1) or not (0)
     * @param string $paypal_tax_id PayPal transaction ID
     * 
     * @return int ID of the newly created invoice post
     */
    function wpestate_insert_invoice($billing_for,$type,$pack_id,$date,$user_id,$is_featured,$is_upgrade,$paypal_tax_id){
       $post = array(
                  'post_title'	=> 'Invoice ',
                  'post_status'	=> 'publish',
                  'post_type'     => 'wpestate_invoice'
              );
       
       if(intval($user_id)!=0){
           $post['post_author']=intval($user_id);
       }
       
       
       $post_id =  wp_insert_post($post );
   
   
       if($type==2){
           $type='Recurring';
       }else{
           $type='One Time';
       }
   
       $price_submission               =   floatval( wpresidence_get_option('wp_estate_price_submission','') );
       $price_featured_submission      =   floatval( wpresidence_get_option('wp_estate_price_featured_submission','') );
   
       if($billing_for=='Package'){
           $price= get_post_meta($pack_id, 'pack_price', true);
       }else{
           if($is_upgrade==1){
                $price=$price_featured_submission;
           }else{
               if($is_featured==1){
                   $price=$price_featured_submission+$price_submission;
               }else{
                    $price=$price_submission;
               }
           }
   
   
       }
   
       update_post_meta($post_id, 'invoice_type', $billing_for);
       update_post_meta($post_id, 'biling_type', $type);
       update_post_meta($post_id, 'item_id', $pack_id);
       update_post_meta($post_id, 'item_price',$price);
       update_post_meta($post_id, 'purchase_date', $date);
       update_post_meta($post_id, 'buyer_id', $user_id);
       update_post_meta($post_id, 'txn_id', $paypal_tax_id);
       $my_post = array(
          'ID'             => $post_id,
          'post_title'     => esc_html__('Invoice','wpresidence-core').' '.$post_id,
       );
       wp_update_post( $my_post );
       return $post_id;
   }
   endif; // end   wpestate_insert_invoice
   
   
   
   
   
   add_action( 'wp_ajax_wpestate_ajax_create_print_invoice', 'wpestate_ajax_create_print_invoice' );
   
   if( !function_exists('wpestate_ajax_create_print_invoice') ):
   /**
    * AJAX handler for creating printable invoices
    * 
    * This function processes AJAX requests to generate a printable HTML invoice.
    * It verifies the invoice exists, loads user and company details, and outputs
    * a formatted HTML document that can be printed by the user.
    * 
    * The function includes security checks to validate the request and ensure
    * the requested invoice is legitimate.
    * 
    * @uses check_ajax_referer() Verifies the security nonce
    * @uses get_post() Retrieves the invoice post
    * @uses wp_get_current_user() Gets current user information
    * @uses get_user_meta() Retrieves user metadata for billing details
    * @uses wpresidence_get_option() Gets theme configuration options
    * 
    * @return void Outputs HTML directly and terminates execution with die()
    */
   function wpestate_ajax_create_print_invoice(){
       // Security check: verify nonce
       check_ajax_referer( 'wpestate_invoices_actions', 'security' );
       if(!isset($_POST['propid'])|| !is_numeric($_POST['propid'])){
               exit('out pls1');
       }
   
       $post_id	= intval($_POST['propid']);
       $the_post	= get_post( $post_id);
   
       // Verify this is a valid published invoice
       if($the_post->post_type!='wpestate_invoice' || $the_post->post_status!='publish'){
               exit('out pls2');
       }
       $title              = get_the_title($post_id);
   
   
       $current_user                   =   wp_get_current_user();
   
       /////////////////////////////////////////////////////////////////////////////////////////////////////
       // end get agent details
       /////////////////////////////////////////////////////////////////////////////////////////////////////
   
       // Begin HTML output with appropriate styles
       print  '<html><head><title>'.$title.'</title><link href="'.get_template_directory_uri().'/public/css/main.css" rel="stylesheet" type="text/css" />';
      
   
       if(is_child_theme()){
               print '<link href="'.get_template_directory_uri().'/css/dashboard/dashboard_style.css" rel="stylesheet" type="text/css" />';
       }
   
       if(is_rtl()){
               print '<link href="'.get_template_directory_uri().'/rtl.css" rel="stylesheet" type="text/css" />';
       }
       print '</head>';
       $protocol = is_ssl() ? 'https' : 'http';
       print  '<body class="print_body" >';
   
       // Output company logo
       $logo=wpresidence_get_option('wp_estate_logo_image','url');
       if ( $logo!='' ){
            print '<img src="'.$logo.'" class="img-responsive printlogo" alt="logo"/>';
       } else {
            print '<img class="img-responsive printlogo" src="'. get_theme_file_uri('/img/logo.png').'" alt="logo"/>';
       }
   
       // Get user billing information
       $invoce_to_name  =  get_user_meta(	$current_user->ID,'first_name',true).' '.get_user_meta(	$current_user->ID,'last_name',true);
       $invoce_to_email =  $current_user->user_email;
   
       // Get company billing information
       $invoce_company_name   =	esc_html( wpresidence_get_option('wp_estate_company_name', '') );
       $invoce_receiver_email =	esc_html( wpresidence_get_option('wp_estate_email_adr', '') );
       $invoce_receiver_phone =	esc_html( wpresidence_get_option('wp_estate_telephone_no', '') );
       $invoce_receiver_addres =	esc_html( wpresidence_get_option('wp_estate_co_address', '') );
   
   
           // Get invoice details
           $invoice_saved      		=   esc_html(get_post_meta($post_id, 'invoice_type', true));
           $invoice_period_saved           =   esc_html(get_post_meta($post_id, 'biling_type', true));
       $invoice_total 			=   esc_html(get_post_meta($post_id, 'item_price', true));
       $invoice_payment_method         =   '';
           
           
           // Translation array for invoice types
           $translations = array(
               'Upgrade to Featured'           =>  esc_html__('Upgrade to Featured','wpresidence-core'),
               'Publish Listing with Featured' =>  esc_html__('Publish Listing with Featured','wpresidence-core'),
               'Package'                       =>  esc_html__('Package','wpresidence-core'),
               'Listing'                       =>  esc_html__('Listing','wpresidence-core'),
               'One Time'                      =>  esc_html__('One Time','wpresidence-core'),
               'Recurring'                     =>  esc_html__('Recurring','wpresidence-core')
           );
           
           
      
   
       $invoice_exra_details =	esc_html( wpresidence_get_option('wp_estate_invoice_extra_details_print', '') );
   
       // Format purchase date with timezone adjustment
       $purchase_date  = esc_html(get_post_meta($post->ID, 'purchase_date', true));
       $time_unix      = strtotime($purchase_date);
       $print_date			= gmdate( 'Y-m-d H:i:s', ( $time_unix+ ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) );
   
       // Output invoice title and date
       print '<h1 class="invoice_print_title">'.$title.'</h1>';
       if($purchase_date!=''){
           print '<div class="wpestate_invoice_date">'.$print_date.'</div>';
       }
   
       // Output billing information - who the invoice is to
       print '<div class="wpestate_print_invoice_to_section">';
           print '<strong>'.esc_html__('To','wpresidence-core').':</strong> '.$invoce_to_name.'</br>';
           print '<strong>'.esc_html__('Email','wpresidence-core').':</strong> '.$invoce_to_email;
       print '</div>';
   
       // Output company information - who the invoice is from
       print '<div class="wpestate_print_invoice_from_whom_section">';
       print '<strong>'.esc_html__('Name','wpresidence-core').':</strong> '.$invoce_company_name.'</br>';
       print '<strong>'.esc_html__('Email','wpresidence-core').':</strong> '.$invoce_receiver_email.'</br>';
       print '<strong>'.esc_html__('Phone','wpresidence-core').':</strong> '.$invoce_receiver_phone.'</br>';
   
       print $invoce_receiver_addres;
   
       print '</div>';
   
   
       // Output invoice details
       print '<div class="wpestate_print_invoice_details_wrapper">';
           print '<div class="wpestate_print_invoice_details_detail"><label>'.esc_html__('Billing for','wpresidence-core').': </label>'.$translations[$invoice_saved]. '</div>';
           print '<div class="wpestate_print_invoice_details_detail"><label>'.esc_html__('Billing type','wpresidence-core').': </label>'.$translations[$invoice_period_saved].'</div>';
           //print '<div class="wpestate_print_invoice_details_detail"><label>'.esc_html__('Payment Method','wpresidence-core').': </label>'.$invoice_payment_method.'</div>';
           print '<div class="wpestate_print_invoice_details_detail"><label>'.esc_html__('Total Price','wpresidence-core').': </label>'.wpestate_show_price_custom_invoice($invoice_total).'</div>';
       print '<div>';
   
   
       // Output any extra details and thank you message
       print '<div class="wpestate_print_invoice_details_wrapperex_details">'.$invoice_exra_details.'</div>';
       print '<div class="wpestate_print_invoice_end">'.esc_html__('Thank you for your business!','wpresidence-core').'</div>';
       print'</div>';
   
       print '<div class="print_spacer"></div>';
       print '</body></html>';
       die();
   }
   
   endif;




/**
 * Invoice AJAX Filter Handler
 *
 * Handles AJAX requests to filter invoice listings based on date range,
 * invoice type, and payment status.
 *
 * @package WpResidence
 * @subpackage Dashboard/Invoices
 * @since 1.0
 */

add_action('wp_ajax_wpestate_ajax_filter_invoices', 'wpestate_ajax_filter_invoices');

if (!function_exists('wpestate_ajax_filter_invoices')):
    /**
     * Filter invoices based on AJAX parameters
     *
     * @return void Outputs JSON with filtered results
     */
    function wpestate_ajax_filter_invoices() {
        // Verify nonce and user authentication
        check_ajax_referer('wpestate_invoices_actions', 'security');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('unauthorized_access');
        }
        
        $current_user = wp_get_current_user();
        $userID = $current_user->ID;
        
        if ($userID === 0) {
            wp_send_json_error('invalid_user');
        }
        $date_query=null;
        // Sanitize input parameters
        $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : '';
        $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : '';
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';

        // Build meta query
        $meta_query = array();
        
        // Add invoice type filter
        if (!empty($type)) {
            $meta_query[] = array(
                'key'     => 'invoice_type',
                'value'   => $type,
                'type'    => 'char',
                'compare' => 'LIKE'
            );
        }
        
        // Add payment status filter
        if (!empty($status)) {
            $meta_query[] = array(
                'key'     => 'pay_status',
                'value'   => $status,
                'type'    => 'numeric',
                'compare' => '='
            );
        }

        // Build date query

        
        if (!empty($start_date)) {
            $date_query['after'] = $start_date;
        }
        
        if (!empty($end_date)) {
            $date_query['before'] = $end_date;
        }

        // Setup WP_Query arguments
        $args = array(
            'post_type'      => 'wpestate_invoice',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'author'         => $userID,
            'meta_query'     => $meta_query,
            'date_query'     => $date_query
        );

        // Get currency settings
        $wpestate_currency       =   esc_html ( wpresidence_get_option('wp_estate_currency_symbol', '') );
        $where_currency =   esc_html ( wpresidence_get_option('wp_estate_where_currency_symbol', '') );

        // Execute query and gather results
        $prop_selection = new WP_Query($args);
        $total_confirmed = 0;
        
        ob_start();
        
        while ($prop_selection->have_posts()): 
            $prop_selection->the_post();
            
            $invoiceID = get_the_ID();
            
            // Include invoice template
            include(locate_template('templates/dashboard-templates/invoice_listing_unit.php'));
            
            // Calculate totals
            $price = floatval(get_post_meta($invoiceID, 'item_price', true));
            $total_confirmed += $price;
        endwhile;
        
        $templates = ob_get_contents();
        ob_end_clean();
        
        wp_reset_postdata();

        // Prepare and send response
        $response = array(
            'args '=>  $args ,
            'results' => $templates,
            'invoice_confirmed' => wpestate_show_price_custom_invoice($total_confirmed)
        );

        wp_send_json_success($response);
    }
endif;
