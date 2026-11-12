<?php
/* Copyright (C) Wpestate/Sc Internet Viboo SRL, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by wpestate.org, March 2019
 */

class Wpestate_stripe_payments {


        private $stripe_secret_key;
        private $stripe_pub_key;
        private $payment_intent;
        private $payment_intent_secret;
        private $currency;
        private $userID;
        private $user_email;



        function __construct() {
            $current_user   =   wp_get_current_user();

            $this->userID                  =    $current_user->ID;
            $this->user_email              =    $current_user->user_email;
            $this->currency                =    esc_html( wpresidence_get_option('wp_estate_submission_curency','') );
            $this->stripe_secret_key       =    trim( wpresidence_get_option('wp_estate_stripe_secret_key','') );
            $this->stripe_pub_key          =    trim( wpresidence_get_option('wp_estate_stripe_publishable_key','') );

            if(class_exists('\Stripe\Stripe') &&  $this->stripe_secret_key!==''){
                \Stripe\Stripe::setApiKey(  $this->stripe_secret_key );
            }else{
                return;
            }
            add_action( 'wp_ajax_wpestate_stripe_recurring', array($this,'wpestate_stripe_recurring') );
            add_action( 'wp_ajax_wpestate_stripe_reccuring_3ds', array($this,'wpestate_stripe_reccuring_3ds') );
        }



        /**
        * Create a PaymentIntent
        *
        *
        * @since    2.7
        * @access   private
        */

        function wpestate_create_stripe_paymenet_intent($ammount,$metadata){
            $ammount=$ammount*100;
            $payment_intent = \Stripe\PaymentIntent::create([
                "amount"                => $ammount,
                "currency"              => $this->currency,
                "payment_method_types"  => ["card"],
                "receipt_email"         =>  $this->user_email,
                "metadata"              =>  $metadata
            ]);


            $this->payment_intent           =   $payment_intent['id'];
         print   $this->payment_intent_secret    =   $payment_intent['client_secret'];
         die();
            //create client
//            $this->stripe_create_customer($token);
        }





        /**
        * list all payment intents
        *
        *
        * @since    2.7
        * @access   private
        */
        function wpestate_list_all_stripe_paymenet_intent(){
            $list=  \Stripe\PaymentIntent::all(["limit" => 30]);
            return $list;
        }
        
        
        /**
        * create stripe intent simple pay
        *
        *
        * @since    2.7
        * @access   private
        */
        function wpestate_create_simple_intent($ammount,$metadata){
            if(  $this->stripe_secret_key  ==''){
                return;
            }

            if($metadata['pay_type']!=3){
                $this-> wpestate_create_stripe_paymenet_intent($ammount,$metadata);
            }
        }

        /**
        * create stripe card form
        *
        *
        * @since    2.7
        * @access   private
        */
        function wpestate_show_stripe_form($ammount,$metadata,$intent=''){
            if(  $this->stripe_secret_key  ==''){
                return;
            }

            if($metadata['pay_type']!=3 && $intent!='no_intent'){
                $this-> wpestate_create_stripe_paymenet_intent($ammount,$metadata);
            }

            $rand= rand(1, mt_getrandmax());

            if($metadata['pay_type']==1){
                print '<span id="wpestate_stripe_booking " data-modalid="modal-'.$rand.'" >'.esc_html__('Pay with Stripe','wpresidence-core').'</span>';
            }else  if($metadata['pay_type']==2){
                if( isset( $metadata['is_upgrade']) && $metadata['is_upgrade']==1){
                    print '<span class="wpestate_stripe_booking_prop wpes111" data-isfeatured="1" data-listingid="'.intval($metadata['listing_id']).'"  data-modalid="modal-'.$rand.'"></span>';
                }else{
                    print '<span class="wpestate_stripe_booking_prop wpes222" data-isfeatured="0" data-listingid="'.intval($metadata['listing_id']).'"  data-modalid="modal-'.$rand.'"></span>';
                }

            }else  if($metadata['pay_type']==3){
                print '<span id="wpestate_stripe_booking_recurring" data-modalid="modal-'.$rand.'" ></span>';
            }

            print '<div class="wpestate_stripe_form_wrapper" id="modal-'.$rand.'">';
            print '<div class="wpestate_stripe_form_wrapper_back"></div>';

            print'
                <div class="cell wpestate_stripe wpestate_stripe_form_1" >
                    <svg class="close_stripe_form" xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17">
                            <path class="base" fill="#000" d="M8.5,17 C3.80557963,17 0,13.1944204 0,8.5 C0,3.80557963 3.80557963,0 8.5,0 C13.1944204,0 17,3.80557963 17,8.5 C17,13.1944204 13.1944204,17 8.5,17 Z"></path>
                            <path class="glyph" fill="#FFF" d="M8.5,7.29791847 L6.12604076,4.92395924 C5.79409512,4.59201359 5.25590488,4.59201359 4.92395924,4.92395924 C4.59201359,5.25590488 4.59201359,5.79409512 4.92395924,6.12604076 L7.29791847,8.5 L4.92395924,10.8739592 C4.59201359,11.2059049 4.59201359,11.7440951 4.92395924,12.0760408 C5.25590488,12.4079864 5.79409512,12.4079864 6.12604076,12.0760408 L8.5,9.70208153 L10.8739592,12.0760408 C11.2059049,12.4079864 11.7440951,12.4079864 12.0760408,12.0760408 C12.4079864,11.7440951 12.4079864,11.2059049 12.0760408,10.8739592 L9.70208153,8.5 L12.0760408,6.12604076 C12.4079864,5.79409512 12.4079864,5.25590488 12.0760408,4.92395924 C11.7440951,4.59201359 11.2059049,4.59201359 10.8739592,4.92395924 L8.5,7.29791847 L8.5,7.29791847 Z"></path>
                    </svg>';

                    $logo = wpresidence_get_option('wp_estate_logo_image', 'url');
                    if ($logo != '') {
                        print '<img src="'.esc_url($logo).'" class="img-responsive retina_ready"  alt="'.esc_html__('logo','wpresidence-core').'"/>';
                    } else {
                        print '<img class="img-responsive retina_ready" src="' . get_template_directory_uri() . '/img/logo.png" alt="'.esc_html__('logo','wpresidence-core').'"/>';
                    }

                    print'

                    <div id="wpestate-stripe-paymentform-1">
                        <fieldset>
                            <div class="row">
                                <label for="wpestate_form_name" data-tid="elements_examples.form.name_label">'.esc_html__('Name','wpresidence-core').'</label>
                                <input id="wpestate_form_name"  data-tid="elements_examples.form.name_placeholder" type="text" placeholder="'.esc_html__('Jane Doe','wpresidence-core').'" required="" autocomplete="name">
                            </div>

                            <div class="row">
                                <label for="wpestate_form_email" data-tid="elements_examples.form.email_label">'.esc_html__('Email','wpresidence-core').'</label>
                                <input id="wpestate_form_email"  data-tid="elements_examples.form.email_placeholder" type="email" placeholder="'.esc_html__('janedoe@gmail.com','wpresidence-core').'" required="" autocomplete="email">
                            </div>

                            <div class="row">
                                <label for="example1-phone" data-tid="elements_examples.form.phone_label">'.esc_html__('Phone','wpresidence-core').'</label>
                                <input id="example1-phone" data-tid="elements_examples.form.phone_placeholder" type="tel" placeholder="'.esc_html__('999 999-999','wpresidence-core').'" required="" autocomplete="tel">
                            </div>
                        </fieldset>

                        <fieldset>
                            <div id="card-errors"></div>
                            <div class="row">

                                <div id="wpestate_form1-cardx" class="wpestate_form1-card StripeElement StripeElement--empty"></div>
                             </div>
                        </fieldset>

                        <div class="wpestate_stripe_pay_desc">';

                        if( isset( $metadata['message']) ){
                           print $metadata['message'];
                        }else{
                            esc_html_e('Payment for package Basic','wpresidence-core');
                        }

                        print'</div>

                        <button type="submit" id="wpestate_stripe_form_button_sumit" data-tid="elements_examples.form.pay_button" data-secret="'.$this->payment_intent_secret.'">'.esc_html__('Pay','wpresidence-core').' '.$ammount.' '.$this->currency.'</button>


                        <div class="error" role="alert">
                            <span id="wpestate_stripe_alert" class="message">'.esc_html__('Please wait while we confirm your payment!','wpresidence-core').'</span>
                        </div>
                    </div>


                    <div id="wpestate_stripe_alert_succes" class="success">
                        <div class="icon">
                            <svg width="84px" height="84px" viewBox="0 0 84 84" version="1.1" xmlns="http://www.w3.org/2000/svg" xlink="http://www.w3.org/1999/xlink">
                                <circle class="border" cx="42" cy="42" r="40" stroke-linecap="round" stroke-width="4" stroke="#000" fill="none"></circle>
                                <path class="checkmark" stroke-linecap="round" stroke-linejoin="round" d="M23.375 42.5488281 36.8840688 56.0578969 64.891932 28.0500338" stroke-width="4" stroke="#000" fill="none"></path>
                            </svg>
                        </div>

                        <h3 class="title" data-tid="elements_examples.success.title">Payment successful</h3>

                    </div>

                </div>';

            print '</div>';//end wpestate_stripe_form_wrapper
        }

      

       

         /**
        * Stripe create plan
        * returns planid
        *
        * @since    2.7
        * @access   private
        */
        private function stripe_create_plan($wprentals_product_id){


            $stripe_product     =   get_post_meta($wprentals_product_id,'pack_stripe_id',true);
            $nickaname          =   get_the_title($wprentals_product_id);
            $interval           =   esc_html(get_post_meta($wprentals_product_id, 'biling_period', true));
            $interval           =   trim(strtolower($interval) );
            $price              =   get_post_meta($wprentals_product_id, 'pack_price', true);
            $price              =   $price*100;


            $plan = \Stripe\Plan::create([
                'product'   => $stripe_product,
                'nickname'  => $nickaname,
                'interval'  => $interval,
                'currency'  => $this->currency,
                'amount'    => $price,
            ]);

            return $plan['id'];

        }

        private function stripe_create_subscription($client_id){
            $subscription = \Stripe\Subscriptions::create([
                 'customer' => $client_id,
                    'items' => [
                      ['price' => 'price_1MCzhBIuDzAtlaozINHX6fhQ'],
                    ],
            ]);
            return $subscription;
        }
        
           
        /**
        * Stripe create plan
        * returns customerid
        *
        * @since    2.7
        * @access   private
        */
        private function stripe_create_customer($token){

            $customer =\Stripe\Customer::create([
                'email'     =>   $this->user_email  ,
                'source'    =>   $token,
            ]);

            return $customer['id'];

        }


        /**
        * Stripe subscrive customer to plan
        * returns customerid
        *
        * @since    2.7
        * @access   private
        */

        function stripe_subscribe_customer_to_plan($customer_id,$plan_id,$packId,$onetime){


            if($onetime==0){

                $subscription = \Stripe\Subscription::create([
                    'customer' => $customer_id,
                    'items' => [
                        ['plan' => $plan_id,]
                        ],
                    'expand' => ['latest_invoice.payment_intent'],
                     'cancel_at_period_end'=>true,
                    'metadata' => [
                            'wpestate_user'     => $this->userID,
                            'wpestate_packID'   => $packId,
                            'wpestate_onetime'  => $onetime
                       ]
                ]);
                
                // Immediately update the subscription to cancel at the end of the period
                $updatedSubscription = \Stripe\Subscription::update($subscription->id, [
                    'cancel_at_period_end' => true,
                ]);

            }else{

                $subscription = \Stripe\Subscription::create([
                    'customer' => $customer_id,
                    'items' => [
                        ['plan' => $plan_id]
                        ],
                    'expand' => ['latest_invoice.payment_intent'],

                    'metadata' => [
                        'wpestate_user'     => $this->userID,
                        'wpestate_packID'   => $packId,
                        'wpestate_onetime'  => $onetime
                    ]
                    
                ]);
            }
            return $subscription;
        }




        /**
        * Stripe aajax processing
        *
        *
        * @since    2.7
        * @access   public
        */
        public function wpestate_stripe_recurring(){

            check_ajax_referer( 'wpestate_payments_nonce', 'security' );

            $packName   =   esc_html($_POST['packName']);
            $packId     =   intval($_POST['packId']);
            $token      =   esc_html($_POST['token']);
            $onetime    =   intval($_POST['onetime']);

          
            $customer_id =  $this->stripe_create_customer($token);
            $plan_id     =  $this->stripe_create_plan($packId);
            
            
   

            $response    =  $this->stripe_subscribe_customer_to_plan($customer_id,$plan_id,$packId,$onetime);




            if ($response['status']=='active' &&  $response['latest_invoice']['status']=='paid' &&
                $response['latest_invoice']['payment_intent']['status']=='succeeded'  ){

                $this->cancel_stripe_recurring_hard();
                update_user_meta(  $this->userID   , 'package_id'            ,  $packId);
                update_user_meta(  intval($this->userID),'stripe', strval( $customer_id)) ;
                update_user_meta(  $this->userID   , 'stripe_subscription_id',  $response['id'] );


                  echo json_encode(array(
                                 'answer'=>'complete',
                                ));
            
                
            }else if ($response['status']=='incomplete' && $response['latest_invoice']['payment_intent']['status']=='requires_action' ){ 
              
                $userid     =   intval($this->userID);
                $customerval=   strval($customer_id);
                $answer     =   update_user_meta(  $userid,'stripe', $customerval) ;
                
              
                echo json_encode(array(
                                'answer'        =>  '3ds',
                                'payment_intent_secret' =>  $response['latest_invoice']['payment_intent']['client_secret'],
                                'packId'        =>  $packId,
                                'customer_id'   =>  $customer_id,
                                'stripe_subscription_id'=>$response['id'],
                                'response'=>$response
                                ));
               
            }else{
                echo json_encode(array(
                    'answer'=>'failed'));
            }

            die();
        }



     /**
        * Stripe aajax processing recurring
        * 
        *
        * @since    2.7
        * @access   public
        */
        
        
        public function wpestate_stripe_reccuring_3ds(){
            $responseId     =   esc_html($_POST['stripe_subscription_id']);
            $packId         =   floatval($_POST['packId']);
            $customer_id    =   esc_html($_POST['customer_id']);
            $this->cancel_stripe_recurring_hard();
            update_user_meta(  $this->userID   , 'stripe'                ,  $customer_id );
            update_user_meta(  $this->userID   , 'package_id'            ,  $packId);
            update_user_meta(  $this->userID   , 'stripe_subscription_id',  $responseId );
        }
    

        /**
        * Cancel Stripe Hard
        *
        *
        * @since    2.7
        * @access   public
        */
        public function cancel_stripe_recurring_hard(){

            $stripe_subscription_id = get_user_meta(  $this->userID   , 'stripe_subscription_id' ,true);
            update_user_meta(  $this->userID   , 'package_id'            ,  '');
            update_user_meta(  $this->userID   , 'stripe'                ,  '' );
            update_user_meta(  $this->userID   , 'stripe_subscription_id',  '' );

            if($stripe_subscription_id!=''){
                $subscription = \Stripe\Subscription::retrieve($stripe_subscription_id);
                $subscription->cancel();
            }
            
            
        }


        /**
        * Cancel Stripe Hard
        *
        *
        * @since    2.7
        * @access   public
        */
        public function cancel_stripe_recurring(){
            $stripe_subscription_id = get_user_meta(  $this->userID   , 'stripe_subscription_id' ,true);
            update_user_meta( $this->userID , 'stripe_subscription_id', '' );
                try {

                    if($stripe_subscription_id!=''){
                        \Stripe\Subscription::update(
                            $stripe_subscription_id,
                            [
                              'cancel_at_period_end' => true,
                            ]
                        );
                    }


                } catch(\Stripe\Error\Card $e) {

                } catch (\Stripe\Error\RateLimit $e) {

                } catch (\Stripe\Error\InvalidRequest $e) {

                } catch (\Stripe\Error\Authentication $e) {

                } catch (\Stripe\Error\ApiConnection $e) {

                } catch (\Stripe\Error\Base $e) {

                } catch (Exception $e) {

                }

        }
}
