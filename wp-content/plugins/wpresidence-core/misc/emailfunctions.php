<?php
/*
* Add message in inbox
*
*
*
*/

if( !function_exists('wpestate_process_private_message') ):
    function wpestate_process_private_message($author_id,$message){
        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;
        if ( !is_user_logged_in() ) {
            exit('you need to be logged in');
        }

        $agent_id   =   intval($_POST['agent_id']);
        $from   =   $userID;
        $to     =   get_post_meta($agent_id,'user_meda_id',true);
        
        if( intval ($to) ==0 ){
            $to = $author_id;
        }

        $subject_new =  esc_html__('New Message on ','wpresidence-core') . esc_url( home_url('/') ) ;
        
        wpestate_add_to_inbox($userID,$from,$to,$subject_new,$message,1);

    }
endif;


/*
 * Ajax adv search contact function 
 * 
 * 
 * */
add_action( 'wp_ajax_nopriv_wpestate_ajax_agent_contact_form', 'wpestate_ajax_agent_contact_form' );
add_action( 'wp_ajax_wpestate_ajax_agent_contact_form', 'wpestate_ajax_agent_contact_form' );

if( !function_exists('wpestate_ajax_agent_contact_form') ):
    function wpestate_ajax_agent_contact_form(){

        // check for POST vars   
        $allowed_html                   =  array();
        $is_elementor_contact_builder   =  intval($_POST['is_elementor']);
        $is_private_message             =  esc_html($_POST['is_private_message']);
        $email_type                     =  wpresidence_get_option('wpestate_email_type','');
        $extra_template                 =   '';
        $formType = 'contact';

        if ( !wp_verify_nonce( $_POST['nonce'], 'ajax-property-contact')) {
            exit("No naughty business please");
        }

        
        if($is_elementor_contact_builder==0){
            
             if ( isset($_POST['is_private_message']) ) {
                if( trim($_POST['is_private_message']) =='yes' && !is_user_logged_in() ){
                    echo json_encode(array(
                        'sent'      =>  false, 
                        'response'  =>  esc_html__('You need to be logged in in order to send a private message!','wpresidence-core') ));
                    exit();
                }
            }
            
            
            // check name
            if ( isset($_POST['name']) ) {
                if( trim($_POST['name']) =='' || trim($_POST['name']) ==esc_html__('Name','wpresidence-core') ){
                    echo json_encode(array('sent'=>false, 'response'=>esc_html__('The name field is empty!','wpresidence-core') ));
                    exit();
                }else {
                    $name = sanitize_text_field (wp_kses( trim($_POST['name']),$allowed_html) );
                }
            }

            //Check email
            if ( isset($_POST['email']) || trim($_POST['name']) ==esc_html__('Email','wpresidence-core') ) {
                if( trim($_POST['email']) ==''){
                    echo json_encode(  
                            array(  'sent'      =>  false, 
                                    'response'  =>  esc_html__('The email field is empty!','wpresidence-core' ) ) );
                    exit();
                } else if( filter_var($_POST['email'],FILTER_VALIDATE_EMAIL) === false) {
                    echo json_encode(   
                            array(  'sent'      =>  false, 
                                    'response'  =>  esc_html__('The email doesn\'t look right!','wpresidence-core') ) );
                    exit();
                } else {
                    $email = sanitize_text_field ( wp_kses( trim($_POST['email']),$allowed_html) );
                }
            }

            $phone   =  sanitize_text_field( trim( $_POST['phone'])  );
            $subject =  esc_html__('Inquiry from','wpresidence-core') .' '. esc_url( home_url('/') ) ;

            //Check comments
            if ( isset($_POST['comment']) ) {
                if( trim($_POST['comment']) =='' || trim($_POST['comment']) ==esc_html__('Message','wpresidence-core')){
                    echo json_encode(   array(
                                        'sent'      =>  false, 
                                        'response'  =>  esc_html__('Your message is empty!','wpresidence-core') 
                    ) );
                    exit();
                }else {
                    $comment = sanitize_textarea_field( trim( $_POST['comment'] ));
                }
            }
        }else{
            $comment  = sanitize_textarea_field(  trim ($_POST['comment']) );
            $phone    = sanitize_text_field(  trim ($_POST['phone']) );
            $email    = sanitize_text_field(  trim ($_POST['email']) );
            $name     = sanitize_text_field(  trim ($_POST['name']) );
            $extra_template='_elementor';
        }

     

        $message        =   '';
        $schedule_mesaj =   '';
        $propid         =   intval($_POST['propid']);
        $agent_id       =   intval($_POST['agent_id']);
        $schedule_hour  =   esc_html($_POST['schedule_hour']);
        $schedule_day   =   esc_html($_POST['schedule_day']);
        $schedule_mode   =  esc_html($_POST['schedule_mode']);

        if($schedule_hour!='' && $schedule_day!=''){
            $formType = 'schedule_tour';
            $schedule_mesaj = sprintf (esc_html__('I would like to schedule a viewing on %s at %s. Please confirm the meeting via email or private message.','wpresidence-core'),$schedule_day,$schedule_hour);
        }
        if($schedule_mode!=''){
            if($schedule_mode=='in_person'){
                $schedule_mode=esc_html__('In Person','wpresidence-core');
            }else{
                $schedule_mode=esc_html__('Via Video Chat','wpresidence-core');
            }
            $schedule_mesaj =$schedule_mesaj. sprintf (esc_html__('I would like to meet ','wpresidence-core'),$schedule_mode);      
        }
        
        
        if($propid!=0){
            $permalink  = esc_url( get_permalink(  $propid ));

            if($agent_id!=0){
                $agent_agency_dev_id    =   intval(get_post_meta($agent_id,'user_meda_id',true));
                
                $receiver_email ='';            
                if($agent_agency_dev_id!=0){
                    $receiver_email         =   get_the_author_meta( 'user_email' , $agent_agency_dev_id );
                }


                if($receiver_email==''){
                    $receiver_email =   esc_html( get_post_meta($agent_id, 'agent_email', true) );
                }
                if( get_post_type($agent_id) == 'estate_agency' ){
                    $receiver_email =   esc_html(get_post_meta($agent_id, 'agency_email', true));
                 
                }
                if( get_post_type($agent_id) == 'estate_developer' ){
                    $receiver_email =   esc_html(get_post_meta($agent_id, 'developer_email', true));
                }

            }else{
                $the_post       =   get_post( $propid);
                $author_id      =   $the_post->post_author;
                $receiver_email =   get_the_author_meta( 'user_email' ,$author_id );
            }
            wp_estate_count_page_contacts($propid);
            $formType = 'property';
           
        }else if($agent_id!=0){
            $permalink      =    esc_url( get_permalink(  $agent_id ) );

            $agent_agency_dev_id    =   intval(get_post_meta($agent_id,'user_meda_id',true));
                
            $receiver_email ='';            
            if($agent_agency_dev_id!=0){
                $receiver_email         =   get_the_author_meta( 'user_email' , $agent_agency_dev_id );
            }


            $formType = 'agent_contact';


            if($agent_agency_dev_id==0 && $receiver_email==''){
                $receiver_email =   esc_html( get_post_meta($agent_id, 'agent_email', true) );
            }
            
             if( get_post_type($agent_id) == 'estate_agency' ){
                    $receiver_email =   esc_html(get_post_meta($agent_id, 'agency_email', true));
                    $formType = 'agency_contact';
                }
                if( get_post_type($agent_id) == 'estate_developer' ){
                    $receiver_email =   esc_html(get_post_meta($agent_id, 'developer_email', true));
                    $formType = 'developer_contact';
                }
            
            
        }else{
            $permalink      =   esc_html('contact page','wpresidence-core');
            $receiver_email =   esc_html( wpresidence_get_option('wp_estate_email_adr', ''));
        }

        if( isset($_POST['is_footer']) && $_POST['is_footer']=='yes' ){
             $permalink      =   esc_html__('footer form','wpresidence-core');
        }
            
        
     
        // if we have contact form elementor
        if(isset($_POST['elementor_wpresidence_form_id'])){
            $wpresidence_form_id =  sanitize_text_field( $_POST['elementor_wpresidence_form_id'] );
            $form_email_options= get_option($wpresidence_form_id);
            if(!$form_email_options){
                exit('no form data');
            }
            $receiver_email = $form_email_options['email_to'];
            
        }
      
        if(isset($_POST['elementor_email_subject'])){
          $subject        = sanitize_text_field( $_POST['elementor_email_subject']);
        }

        
        
        
        $extra_headers = '';
        if(isset($form_email_options['send_copy_to'])){
            $email_cc       = sanitize_text_field($form_email_options['send_copy_to']);
            $extra_headers    .=   "CC:".   $email_cc    . "\r\n";
        }
        
        if(isset($form_email_options['send_ccopy_to'])){
            $email_cc       = sanitize_text_field( $form_email_options['send_ccopy_to']);
            $extra_headers    .=   "BCC:".   $email_cc    . "\r\n";
        }
        

        
        //preparing emails
 
        if($email_type=='html'){
            $comment_striped  =  preg_replace("/\r\n|\r|\n/", '<br>', $comment);
            $comment_striped = str_replace('/n', '<br>', $comment_striped);
            $comment_striped = str_replace('\\n', '<br>', $comment_striped);
            $tip_email        = 'contact_email_template'.$extra_template;
            $attributes=array(
                'name'      =>  $name,
                'email'     =>  $email,
                'phone'     =>  $phone,
                'subject'   =>  stripslashes( $subject ),
                'content'   =>  stripslashes( $comment_striped ),
                'sent_from' =>  $permalink
            );
            
            if($schedule_hour!='' && $schedule_day!='' ){
                $attributes['schedule_hour']    =   $schedule_hour;
                $attributes['schedule_day']     =   $schedule_day;
                $attributes['schedule_mode']    =   $schedule_mode;
                $attributes['schedule_mesaj']   =   $schedule_mesaj;
                $tip_email                      =   'schedule_tour_email_template';
                $subject =  esc_html__('Schedule Tour request from','wpresidence-core') .' '.esc_url( home_url('/') ) ;

            }
        
     
            ob_start();
                include(locate_template('templates/email_templates/'.$tip_email.'.php'));
                $email_message =    ob_get_contents();
            ob_end_clean();


            $prepared_email_message =   wpestate_prepare_email_message($email_message,$attributes);
        }else{
            $message    .=  esc_html__('Subject','wpresidence-core').": " . $subject . PHP_EOL;
            $message    .=  esc_html__('Client Name','wpresidence-core').": " . $name . PHP_EOL;
            $message    .=  esc_html__('Email','wpresidence-core').": " . $email . PHP_EOL;
            $message    .=  esc_html__('Phone','wpresidence-core').": " . $phone . PHP_EOL;
        
            
            
            if($schedule_hour!='' && $schedule_day!=''){
                $message    .=  esc_html__('Tour Schedule on','wpresidence-core').": " . $schedule_hour.' - '. $schedule_day. PHP_EOL;
                $message    .=  esc_html__('Tour Mode ','wpresidence-core').": " . $schedule_mode. PHP_EOL;
                $subject    =  esc_html__('Schedule Tour request from','wpresidence-core') .' '.esc_url( home_url('/') ) ;
            }
              $comment = str_replace('/n', PHP_EOL, $comment);
            $message    .=  esc_html__('Content','wpresidence-core').": \n " . $comment. PHP_EOL;
            
         
            $prepared_email_message = $message. esc_html__('Message sent from ','wpresidence-core').$permalink. PHP_EOL;
        }

        //reply to header
        $reply_to_header  =$name.'  <'. $email.'>';

        // Send to webhook
        if ( function_exists('wpestate_webhook_send') ) {
            $webhook_data = array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $comment,
                'permalink' => $permalink,
                'schedule_hour' => $schedule_hour,
                'schedule_day' => $schedule_day,
                'schedule_mode' => $schedule_mode
            );
            wpestate_webhook_send($webhook_data, $formType);
        }
    
        // sending emails
        wpestate_send_emails($receiver_email, $subject, $prepared_email_message,$email_type,$reply_to_header,$extra_headers );
       
        
        // send private message if is the case
        if ( isset($_POST['is_private_message']) ) {
            if( trim($_POST['is_private_message']) =='yes' && is_user_logged_in() ){
                $internal_message    .=  esc_html__('Subject','wpresidence-core').": " . $subject . PHP_EOL;
                $internal_message    .=  esc_html__('Client Name','wpresidence-core').": " . $name . PHP_EOL;
                $internal_message    .=  esc_html__('Email','wpresidence-core').": " . $email . PHP_EOL;
                $internal_message    .=  esc_html__('Phone','wpresidence-core').": " . $phone . PHP_EOL;



                if($schedule_hour!='' && $schedule_day!=''){
                    $internal_message    .=  esc_html__('Tour Schedule on','wpresidence-core').": " . $schedule_hour.' - '. $schedule_day. PHP_EOL;
                    $internal_message    .=  esc_html__('Tour Mode ','wpresidence-core').": " . $schedule_mode. PHP_EOL;
                }

                $internal_message    .=  esc_html__('Content','wpresidence-core').": \n " . $comment. PHP_EOL;
                $internal_message = $internal_message. esc_html__('Message sent from ','wpresidence-core').$permalink. PHP_EOL;
                wpestate_process_private_message($author_id,$internal_message);
            }
        }
        
        // duplicate emails
        $duplicate_email_adr        =   esc_html ( wpresidence_get_option('wp_estate_duplicate_email_adr','') );
        if( $duplicate_email_adr!='' ){
            $message = $message.' '.esc_html__('Message was also sent to ','wpresidence-core').$receiver_email;
            wpestate_send_emails($duplicate_email_adr, $subject, $prepared_email_message,$email_type,$reply_to_header,$extra_headers );
        }

        
        if($propid!=0){
            $agents_secondary   =   get_post_meta($propid, 'property_agent_secondary', true);
            if(is_array($agents_secondary)):
                foreach($agents_secondary  as $key=>$value){
                    $receiver_email_sec= esc_html( get_post_meta($value, 'agent_email', true) );
                    wpestate_send_emails($receiver_email_sec, $subject, $prepared_email_message,$email_type,$reply_to_header,$extra_headers );
                }
            endif;

        }

        $response   = esc_html__('The message was sent!' ,'wpresidence-core');
        if( $schedule_mesaj!=''){
            $response.=' '.esc_html__('Your request will be confirmed via email or private message.','wpresidence-core');
        }

        echo json_encode(array(
                    'sent'      =>  true,
                    '$receiver_email'=>$receiver_email,
                    '$comment'=>json_encode($comment),
                    'response'  =>  $response) );
        die();
    }
endif; // end   wpestate_ajax_agent_contact_form




/*
 * 
 * Prepare email template
 * 
*/

function wpestate_prepare_email_message($email_message,$attributes){
    
    foreach ($attributes as $key=>$value){
        $email_message= str_replace('{'.$key.'}', $value, $email_message);
    }
    
    return $email_message;
    
}


/*
*  Send to CRM
*
*
*
*/



add_action( 'wp_ajax_nopriv_wpestate_ajax_agent_contact_form_forcrm', 'wpestate_ajax_agent_contact_form_forcrm' );
add_action( 'wp_ajax_wpestate_ajax_agent_contact_form_forcrm', 'wpestate_ajax_agent_contact_form_forcrm' );

if( !function_exists('wpestate_ajax_agent_contact_form_forcrm') ):
    function wpestate_ajax_agent_contact_form_forcrm(){

        if(!function_exists('wpestate_crm_add_lead')){
          die();
        }
        // check for POST vars
        $hasError       =   false;
        $allowed_html   =   array();
        $to_print       =   '';

        if ( !wp_verify_nonce( $_POST['nonce'], 'ajax-property-contact')) {
            exit("No naughty business please");
        }


        if ( isset($_POST['name']) ) {
            if( trim($_POST['name']) =='' || trim($_POST['name']) ==esc_html__('Name','wpresidence-core') ){
                exit();
            }else {
                $name = sanitize_text_field (wp_kses( trim($_POST['name']),$allowed_html) );
            }
        }

        //Check email
        if ( isset($_POST['email']) || trim($_POST['name']) ==esc_html__('Email','wpresidence-core') ) {
            if( trim($_POST['email']) ==''){
                exit();
            } else if( filter_var($_POST['email'],FILTER_VALIDATE_EMAIL) === false) {
                exit();
            } else {
                $email = sanitize_text_field ( wp_kses( trim($_POST['email']),$allowed_html) );
            }
        }



        $phone   = sanitize_text_field(wp_kses( trim($_POST['phone']),$allowed_html) );
        $subject =esc_html__('Enquiry from ','wpresidence-core') . esc_url( home_url('/') ) ;

        //Check comments
        if ( isset($_POST['comment']) ) {
            if( trim($_POST['comment']) =='' || trim($_POST['comment']) ==esc_html__('Your Message','wpresidence-core')){
                exit();
            }else {
                $comment = sanitize_textarea_field ( trim( $_POST['comment']  ));
            }
        }

        $message    =   '';
        $propid     =   intval($_POST['propid']);
        $agent_id   =   intval($_POST['agent_id']);

        $schedule_mesaj =   '';
        $schedule_hour  =   sanitize_text_field($_POST['schedule_hour']);
        $schedule_day   =   sanitize_text_field($_POST['schedule_day']);
        $schedule_mode   =  esc_html($_POST['schedule_mode']);
        if($schedule_mode=='in_person'){
            $schedule_mode=esc_html__('In Person','wpresidence-core');
        }else{
            $schedule_mode=esc_html__('Via video chat','wpresidence-core');
        }
  
        if($schedule_hour!='' && $schedule_day!=''){
            $schedule_mesaj =   sprintf (esc_html__('I would like to schedule a viewing on %s at %s. The meeting will be %s . Please confirm the meeting via email or private message. ','wpresidence-core'),$schedule_day,$schedule_hour,$schedule_mode);
            $comment =   $schedule_mesaj .'<br>'.$comment;
        }


        if($propid!=0){
            $permalink  = esc_url( get_permalink(  $propid ));

        }else if($agent_id!=0){
            $permalink      =    esc_url( get_permalink(  $agent_id ) );

        }else{
            $permalink      =   'contact page';

        }



       $def_arguments=array(
           'source'            =>'',
           'page_source'       =>$permalink,
           'title'             =>$subject,
           'details'           =>$comment,
           'contact_name'      =>$name,
           'contact_email'     =>$email,
           'contact_mobile'    =>$phone,
           'agent_id'          =>$agent_id,
           'propid'            =>$propid
       );
       wpestate_crm_add_lead($def_arguments);

       echo json_encode(array('sent'=>true,'crm_records'=>$def_arguments) );
        die();
    }
endif; // end   wpestate_ajax_agent_contact_form




/*
* CRM function for footer contact form
*
*
*
*/
if (!function_exists('wpestate_select_email_type')):
    function wpestate_select_email_type($user_email,$type,$arguments){
        $value          =   wpresidence_get_option('wp_estate_'.$type,'');
        $value_subject  =   wpresidence_get_option('wp_estate_subject_'.$type,'');

        if (function_exists('icl_translate') ){
            $value          =  icl_translate('wpresidence-core','wp_estate_email_'.$value, $value ) ;
            $value_subject  =  icl_translate('wpresidence-core','wp_estate_email_subject_'.$value_subject, $value_subject ) ;
        }

        if( trim($value_subject)=='' || trim($value)=='' ){
            return;
        }

        wpestate_emails_filter_replace($user_email,$value,$value_subject,$arguments);
    }
endif;





/*
* Relace placeholders with content in email
*
*
*
*/

if( !function_exists('wpestate_emails_filter_replace')):
    function  wpestate_emails_filter_replace($user_email,$message,$subject,$arguments){
        $email_type                 =  wpresidence_get_option('wpestate_email_type','');
        $arguments ['website_url']  = get_option('siteurl');
        $arguments ['website_name'] = get_option('blogname');
        $arguments ['user_email']   = $user_email;



        $user= get_user_by('email',$user_email);
	if( isset($user->user_login) ){
            $arguments ['username'] = $user->user_login;
        }


        foreach($arguments as $key_arg=>$arg_val){
            $subject = str_replace('%'.$key_arg, $arg_val, $subject);
            $message = str_replace('%'.$key_arg, $arg_val, $message);
        }

        $prepared_email_message=$message;
        
        $email_type= wpresidence_get_option('wpestate_email_type','');
        if($email_type=='html'){
            $message  = preg_replace("/\r\n|\r|\n/", '<br>', $message);
            $tip_email='base_email_template';

            $attributes=array(
                    'content'=>$message,
                    'subject'=>$subject
                    );

            ob_start();
                include(locate_template('templates/email_templates/'.$tip_email.'.php'));
                $email_message =    ob_get_contents();
            ob_end_clean();

            $prepared_email_message =   wpestate_prepare_email_message($email_message,$attributes);
        }
        
        
        wpestate_send_emails($user_email, $subject, $prepared_email_message,$email_type,'',$email_type );
    }
endif;






/*
*  Actual Send email
*
*
*
*/

if( !function_exists('wpestate_send_emails') ):
    function wpestate_send_emails($user_email, $subject, $message,$email_type ,$reply_to='',$extra_headers=''){
        if($reply_to==''){
            $reply_to=wpestate_return_sending_email() ;
        }
    
        $headers = 'From: '. wpestate_return_sending_email () . "\r\n" .
                    'Reply-To:'. $reply_to . "\r\n" .
                    'Content-Type: text/html; charset="UTF-8"'."\r\n" .
                   // 'MIME-Version: 1.0'."\r\n" .
                    'X-Mailer: PHP/' . phpversion();
      
        if($email_type=='text'){
            $headers = 'From: '. wpestate_return_sending_email () . "\r\n" .
                    'Reply-To:'. $reply_to . "\r\n" .
                    'Content-Type: text/plain ; charset="UTF-8"'."\r\n" .
                  //  'MIME-Version: 1.0'."\r\n" .
                    'X-Mailer: PHP/' . phpversion();
        }
        
        $headers=$headers.$extra_headers;
        
        @wp_mail(
            $user_email,
            stripslashes($subject),
            stripslashes($message),
            $headers
            );
    };
endif;




/*
*  Email Management
*
*
*
*/
if( !function_exists('wpestate_email_management') ):
    function wpestate_email_management(){
        print '<div class="wpestate-tab-container">';
        print '<h1 class="wpestate-tabh1">'.esc_html__('Email Management','wpresidence-core').'</h1>';
        print '<a href="http://help.wpresidence.net/#" target="_blank" class="help_link">'.esc_html__('help','wpresidence-core').'</a>';


        $emails=array(
            'new_user'                  =>  esc_html__('New user  notification','wpresidence-core'),
            'admin_new_user'            =>  esc_html__('New user admin notification','wpresidence-core'),
            'purchase_activated'        =>  esc_html__('Purchase Activated','wpresidence-core'),
            'password_reset_request'    =>  esc_html__('Password Reset Request','wpresidence-core'),
            'password_reseted'          =>  esc_html__('Password Reseted','wpresidence-core'),
            'agent_review'              =>  esc_html__('Agent Review Posted','wpresidence-core'),
            'purchase_activated'        =>  esc_html__('Purchase Activated','wpresidence-core'),
            'approved_listing'          =>  esc_html__('Approved Listings','wpresidence-core'),
            'approved_user'             =>  esc_html__('Approved User','wpresidence-core'),
            'new_wire_transfer'         =>  esc_html__('New wire Transfer','wpresidence-core'),
            'admin_new_wire_transfer'   =>  esc_html__('Admin - New wire Transfer','wpresidence-core'),
            'admin_expired_listing'     =>  esc_html__('Admin - Expired Listing','wpresidence-core'),
            'matching_submissions'      =>  esc_html__('Matching Submissions','wpresidence-core'),
            'paid_submissions'          =>  esc_html__('Paid Submission','wpresidence-core'),
            'featured_submission'       =>  esc_html__('Featured Submission','wpresidence-core'),
            'account_downgraded'        =>  esc_html__('Account Downgraded','wpresidence-core'),
            'membership_cancelled'      =>  esc_html__('Membership Cancelled','wpresidence-core'),
            'downgrade_warning'         =>  esc_html__('Downgrade Warning','wpresidence-core'),
            'free_listing_expired'      =>  esc_html__('Free Listing Expired','wpresidence-core'),
            'new_listing_submission'    =>  esc_html__('New Listing Submission','wpresidence-core'),
            'listing_edit'              =>  esc_html__('Listing Edit','wpresidence-core'),
            'recurring_payment'         =>  esc_html__('Recurring Payment','wpresidence-core'),
            'membership_activated'      =>  esc_html__('Membership Activated','wpresidence-core'),
            'agent_update_profile'      =>  esc_html__('Update Profile','wpresidence-core'),
            'agent_added'               =>  esc_html__('New Agent','wpresidence-core'),
            'payment_action_required'   =>  esc_html__('Payment Action Required','wpresidence-core'),


        );


        print '<div class="email_row">'.esc_html__('Global variables: %website_url as website url,%website_name as website name, %user_email as user_email, %username as username','wpresidence-core').'</div>';


        foreach ($emails as $key=>$label ){

            print '<div class="email_row">';
            $value          = stripslashes( wpresidence_get_option('wp_estate_'.$key,'') );
            $value_subject  = stripslashes( wpresidence_get_option('wp_estate_subject_'.$key,'') );

            print '<label for="subject_'.$key.'">'.esc_html__('Subject for','wpresidence-core').' '.$label.'</label>';
            print '<input type="text" name="subject_'.$key.'" value="'.$value_subject.'" />';

            print '<label for="'.$key.'">'.esc_html__('Content for','wpresidence-core').' '.$label.'</label>';
            print '<textarea rows="10" cc="111" name="'.$key.'">'.$value.'</textarea>';
            print '<div class="extra_exp"> '.wpestate_emails_extra_details($key).'</div>';
            print '</div>';

        }

        print'<p class="submit">
               <input type="submit" name="submit" id="submit" class="button-primary"  value="'.esc_html__('Save Changes','wpresidence-core').'" />
            </p>';

        print '</div>';
    }
endif;


/*
*  Email Management Extra details
*
*
*
*/

if( !function_exists('wpestate_emails_extra_details') ):
    function wpestate_emails_extra_details($type){
        $return_string='';
        switch ($type) {
            case "new_user":
                    $return_string=esc_html__('%user_login_register as new username, %user_pass_register as user password, %user_email_register as new user email' ,'wpresidence-core');
                    break;

            case "admin_new_user":
                    $return_string=esc_html__('%user_login_register as new username and %user_email_register as new user email' ,'wpresidence-core');
                    break;

            case "password_reset_request":
                    $return_string=esc_html__('%reset_link as reset link','wpresidence-core');
                    break;

            case "password_reseted":
                    $return_string=esc_html__('%user_pass as user password','wpresidence-core');
                    break;
            case "agent_review":
                    $return_string=esc_html__('%agent_name as agent name, %user_post as username(the one who posted)','wpresidence-core');
                    break;
            case "purchase_activated":
                    $return_string='';
                    break;

            case "approved_listing":
                    $return_string=esc_html__('* you can use %post_id as listing id, %property_url as property url and %property_title as property name','wpresidence-core');
                    break;

            case "new_wire_transfer":
                    $return_string=  esc_html__('* you can use %invoice_no as invoice number, %total_price as $totalprice and %payment_details as  $payment_details','wpresidence-core');
                    break;

            case "admin_new_wire_transfer":
                    $return_string=  esc_html__('* you can use %invoice_no as invoice number, %total_price as $totalprice and %payment_details as  $payment_details','wpresidence-core');
                    break;

            case "admin_expired_listing":
                    $return_string=  esc_html__('* you can use %submission_title as property title number, %submission_url as property submission url','wpresidence-core');
                    break;

            case "matching_submissions":
                    $return_string=  esc_html__('* you can use %matching_submissions as matching submissions list','wpresidence-core');
                    break;

            case "paid_submissions":
                    $return_string= '';
                    break;

            case  "featured_submission":
                    $return_string=  '';
                    break;

            case "account_downgraded":
                    $return_string=  '';
                    break;

            case "free_listing_expired":
                    $return_string=  esc_html__('* you can use %expired_listing_url as expired listing url and %expired_listing_name as expired listing name','wpresidence-core');
                    break;

            case "new_listing_submission":
                    $return_string=  esc_html__('* you can use %new_listing_title as new listing title and %new_listing_url as new listing url','wpresidence-core');
                    break;

            case "listing_edit":
                    $return_string=  esc_html__('* you can use %editing_listing_title as editing listing title and %editing_listing_url as editing listing url','wpresidence-core');
                    break;

            case "recurring_payment":
                    $return_string=  esc_html__('* you can use %recurring_pack_name as recurring packacge name and %merchant as merchant name','wpresidence-core');
                    break;

            case "membership_activated":
                    $return_string=  '';
                    break;

            case "payment_action_required":
                    $return_string=  '';
                    break;

        }
        return $return_string;
    }
endif;
