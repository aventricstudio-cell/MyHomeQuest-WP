<?php
// Template Name:Stripe Charge Page
// Wp Estate Pack

//test git final
$endpoint_secret    =  esc_html ( wpresidence_get_option('wp_estate_stripe_webhook','') ); 
$payload            = file_get_contents('php://input');
$sig_header         = sanitize_text_field( $_SERVER['HTTP_STRIPE_SIGNATURE']);
$event = null;
$current_user                   =   wp_get_current_user();

// Default metadata values to avoid undefined notices
$userId    = 0;
$packId    = 0;
$onetime   = 0;
$pay_type  = 0;
try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );
} catch(\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400); // PHP 5.4 or greater
    exit('');
} catch(\Stripe\Error\SignatureVerification $e) {
    // Invalid signature
    http_response_code(400); // PHP 5.4 or greater
    exit();
}
   
//error_log($event->type);

if ($event->type == "payment_intent.succeeded") {
        $intent = $event->data->object;

        // Ensure defaults
        $pay_type   = 0;
        $userId     = 0;
        $listing_id = 0;
        $is_featured= 0;
        $is_upgrade = 0;

        if(isset($event->data->object->charges->data[0]->metadata->pay_type)){
            $pay_type   = intval($event->data->object->charges->data[0]->metadata->pay_type);
            $userId     = intval($event->data->object->charges->data[0]->metadata->user_id);
        }elseif(isset($event->data->object->metadata->pay_type)){
            $pay_type   = intval($event->data->object->metadata->pay_type);
            $userId     = intval($event->data->object->metadata->user_id);
        }



        $depozit        =   intval($intent->amount);
        $user_data      =   get_userdata($userId);



        if($pay_type==2){

            if( isset($event->data->object->charges->data[0]->metadata->listing_id) ){
                $listing_id     =   intval($event->data->object->charges->data[0]->metadata->listing_id);
                $is_featured    =   intval($event->data->object->charges->data[0]->metadata->featured_pay);
                $is_upgrade     =   intval($event->data->object->charges->data[0]->metadata->is_upgrade);
            }elseif(isset($event->data->object->metadata->listing_id)){
                $listing_id     =   intval($event->data->object->metadata->listing_id);
                $is_featured    =   intval($event->data->object->metadata->featured_pay);
                $is_upgrade     =   intval($event->data->object->metadata->is_upgrade);
            }
           
            $time = time(); 
            $date = date('Y-m-d H:i:s',$time);

            if($is_upgrade==1){
                
          
                if( get_post_meta($listing_id, 'prop_featured',true )!==1){               
                    update_post_meta($listing_id, 'prop_featured', 1);
                    $invoice_id =   wpestate_insert_invoice('Upgrade to Featured','One Time',$listing_id,$date,$userId,0,1,'' );
                    wpestate_email_to_admin(1);
                    update_post_meta($invoice_id, 'pay_status', 1); 
                }
                
                
            }else{ // we make it pay
            
                if( get_post_meta($listing_id, 'pay_status',true )!=='paid'){
           
                        update_post_meta($listing_id, 'pay_status', 'paid');
                    
                        // if admin does not need to approve - make post status as publish
                        $admin_submission_status = esc_html ( wpresidence_get_option('wp_estate_admin_submission','') );
                        $paid_submission_status  = esc_html ( wpresidence_get_option('wp_estate_paid_submission','') );
                    
                        if($admin_submission_status=='no'  && $paid_submission_status=='per listing' ){
                            
                            $post = array(
                                'ID'            => $listing_id,
                                'post_status'   => 'publish'
                                );
                            $post_id =  wp_update_post($post ); 
                        }
                        // end make post publish
                    
                        if($is_featured==1){
                            update_post_meta($listing_id, 'prop_featured', 1);
                            $invoice_id = wpestate_insert_invoice('Publish Listing with Featured','One Time',$listing_id,$date,$userId,1,0,'' );
                        }else{
                            $invoice_id = wpestate_insert_invoice('Listing','One Time',$listing_id,$date,$userId,0,0,'' );
                        }
                        update_post_meta($invoice_id, 'pay_status', 1); 
                        wpestate_email_to_admin(0);
                 
                } 

            }
        
          
            http_response_code(200);
      
            exit();
        }
    
    

    
    
    
    
    
}elseif ($event->type == "invoice.payment_succeeded") {
            $customer_stripe_id =   $event->data->object->customer;
            $invoice            =   $event->data->object;
            $invoice_array      =   $invoice->toArray();

            // Look for membership metadata in multiple locations
            $meta_source = array();
            if(isset($invoice_array['lines']['data'][0]['metadata'])){
                $meta_source = $invoice_array['lines']['data'][0]['metadata'];
            }elseif(isset($invoice_array['subscription_details']['metadata'])){
                $meta_source = $invoice_array['subscription_details']['metadata'];
            }

            // Fallback: retrieve subscription for metadata when missing
            if(empty($meta_source) && isset($invoice_array['subscription_details']['subscription'])){
                $sub_id = $invoice_array['subscription_details']['subscription'];
                try {
                    $subscription = \Stripe\Subscription::retrieve($sub_id);
                    if(isset($subscription->metadata)){
                        $meta_source = $subscription->metadata->toArray();
                    }
                } catch (\Exception $e) {}
            }

            if(isset($meta_source['wpestate_user'])){
                $userId = intval($meta_source['wpestate_user']);
            }
            if(isset($meta_source['wpestate_packID'])){
                $packId = intval($meta_source['wpestate_packID']);
            }
            if(isset($meta_source['wpestate_onetime'])){
                $onetime = intval($meta_source['wpestate_onetime']);
            }

            // Payment type needs to mirror wpestate_upgrade_user_membership expectations:
            // 1 = one time membership (with expiration), 2 = recurring subscription.
            // Metadata stores onetime=1 for non-recurring packs, so map accordingly.
            $payment_type = ($onetime==1) ? 1 : 2;


            if($userId!=0 && $packId!=0){
                if( wpestate_check_downgrade_situation($userId,$packId) ){
                    wpestate_downgrade_to_pack( $userId, $packId );
                    wpestate_upgrade_user_membership($userId,$packId,$payment_type,'');
                }else{
                    wpestate_upgrade_user_membership($userId,$packId,$payment_type,'');
                }
            }


            http_response_code(200);
            exit();
    
            
    
}elseif ($event->type == "invoice.payment_failed" || $event->type=="customer.subscription.deleted") {
    
        $customer_stripe_id =$event->data->object->customer;
        $args   =   array(  'meta_key'      => 'stripe', 
                            'meta_value'    => $customer_stripe_id
                            );

        $customers  =   get_users( $args ); 
        foreach ( $customers as $user ) {
            update_user_meta( $user->ID, 'stripe', '' );
            //wpestate_downgrade_to_free($user->ID);
        }      
        http_response_code(200);
        exit();
    
    
}elseif ($event->type == "payment_intent.payment_failed") {
        $intent = $event->data->object;
        $error_message = '';
        if(isset($intent->last_payment_error) && isset($intent->last_payment_error->message)){
            $error_message = $intent->last_payment_error->message;
        }
        printf("Failed: %s, %s", $intent->id, $error_message);
        http_response_code(200);
        exit();
}elseif($event->type=="invoice.payment_action_required"){

        $pay_type       = 0;
        $userId         = 0;
        $user_email     = '';
        $payment_intent = '';

        if(isset($event->data->object->charges->data[0]->metadata->pay_type)){
            $pay_type   = intval($event->data->object->charges->data[0]->metadata->pay_type);
            $userId     = intval($event->data->object->charges->data[0]->metadata->user_id);
        }elseif(isset($event->data->object->metadata->pay_type)){
            $pay_type   = intval($event->data->object->metadata->pay_type);
            $userId     = intval($event->data->object->metadata->user_id);
        }

        if(isset($event->data->object->customer_email)){
            $user_email = $event->data->object->customer_email;
        }
        if(isset($event->data->object->payment_intent)){
            $payment_intent = $event->data->object->payment_intent;
        }

        if($user_email!=''){
            $user = get_user_by( 'email', $user_email );
            if($user){
                $userId = $user->ID;
                update_user_meta($userId,'wpestate_payment_intent_recurring',$payment_intent);
            }
            $arguments = array('payment_intent'=>$payment_intent);
            wpestate_select_email_type($user_email, 'payment_action_required', $arguments);
        }
}else{
    http_response_code(200);
    exit();
}
