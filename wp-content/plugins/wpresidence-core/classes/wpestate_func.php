<?php
class WpestateFunk {
    
//    const WPESTATE_THEMES_API        = 'https://nppff58eg3.execute-api.us-east-1.amazonaws.com/live/wpresidence/';
    
    const WPESTATE_THEMES_API        = 'https://nppff58eg3.execute-api.us-east-1.amazonaws.com/test/wpresidence';
    const ENVATO_ITEM_ID            = '7896392';
    const PURCHASE_CODE_OPTION_NAME = 'envato_purchase_code_7896392';
    const NONCE_ACTION              = 'wpestate_nonce_license';
    const EXPIRY_OPTION_NAME        = 'api_expiry';
    const ACCESS_TOKEN_OPTION_NAME   =  'wpestate_access_token';
    private static $instance;


    
    private function __construct() {
        // private constructor to prevent direct instantiation
        add_action( 'wp_ajax_wpestate_register_license', [ $this, 'register_license' ] );
        add_action( 'wp_ajax_wpestate_revoke_license', [ $this, 'revoke_license' ] );
    }

    
    
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function update_option( $name, $value ) {
        return update_option( $name, $value );
    }

    function sanitize_checkbox( $data ) {
		return ( true === $data || 'on' === $data || 'true' === $data ) ? 'on' : 'off';
    }
    
    private function is_sha256( $string = '' ) {
		return preg_match( '/^[a-f0-9]{64}$/', $string );
    }
        
    public function custom_menu_item(){
            $wpresidence_branding = wpresidence_theme_branding(); // Uses the filter system
            $menu_icon = wpresidence_get_theme_branding_logo_url(); // Just returns the URL
            add_menu_page(
                $wpresidence_branding .' License',
                $wpresidence_branding .' License',
                'manage_options', // Capability required to access this menu item
                'wpresidence-license',
                'wpestate_license_custom_menu_page',
                $menu_icon, // Icon for the menu item
                3 // Position before the Dashboard
            );
    }
    
    private function get_user_data() {
		$user = wp_get_current_user();

		$user_data = [
			'first_name' => $user->user_firstname,
			'last_name'  => $user->user_lastname,
		];

		return $user_data;
	}

    private function get_domain() {
            return get_site_url();
    }

    private function has_access_token() {
            return ! empty( $this->get_option( self::ACCESS_TOKEN_OPTION_NAME ) );
    } 
    
    private function has_purchase_code() {
            return ! empty( $this->get_option( self::PURCHASE_CODE_OPTION_NAME ) );
    }
        
    private function get_option( $name ) {
        return get_option( $name, false );
    }    
        
        
    public function show_deregister_license_form(){
     
        print '<div class="wpestate_register_license_wrapper">';
        print '<div id="wpestate_deregister_license_notification"></div>';
        
     
        if( $this->is_registered() ){
             print 'By Deactivating the license you will be able to use it on another Website. However you will not be allowed to use the theme on this website since the license is per domain. ';
      
            $ajax_nonce = wp_create_nonce("wprentals_activate_license_nonce");
            print '<input type="hidden" id="wprentals_activate_license_nonce" value="'.esc_html($ajax_nonce).'" />';
            print '<input type="submit" name="submit" id="wpestate_deregister_ajax_license" class="button wpresidence_button secondary" value="'.esc_html('Deactivate License','wpresidence-core').'">';

        }else{
           print esc_html('You do not have an active license','wpresidence-core');
        }
        
       
       
        print '</div>'; 
    }   
        
        
        
        
        
        
    public function show_license_form(){
           
        if($this->is_registered())return;
        
        print '<div class="wpestate_register_license_wrapper wrap wpresidence2025-content-wrapper ">';
        print '<div id="wpestate_register_license_notification"></div>';
        
        print '<div class="wpestate_register_license_notes">'.esc_html__('You need to register your license to be able to take advantage of lifetime auto-updates and import our ready-made demo content','wpresidence-core').'. <a href="https://help.wpresidence.net/article/where-is-my-purchase-code/" target="_blank">'.esc_html__('Check this help','wpresidence-core').'</a></div>';
        
        print '<input type="text"  id="wpestate_envato_username" class="wpresidence-2025-input" placeholder="'.__('ThemeForest Username','wpresidence-core').'">'; 
        print '<input type="text" id="wpestate_envato_code" class="wpresidence-2025-input" placeholder="'.__('Purchase Code','wpresidence-core').'">'; 
             
        print '<div class="wpestate_terms_wrapper"><input required="" name="terms" type="checkbox" id="wpestate_terms" class="custom-control-input">';
        
        $privacy_policy_link = '<a href="' . esc_url( 'https://wpestate.org/privacy-policy/' ) . '" target="_blank">' . esc_html__( 'Privacy policy', 'wpresidence-core' ) . '</a>';
        $terms_of_use_link = esc_html__( 'Terms of use', 'wpresidence-core' );

        $translated_text = sprintf(
            // Translatable string with placeholders
            esc_html__( 'I consent to Wpestate.org collecting my personal information according to its %1$s and %2$s.', 'wpresidence-core' ),
            $privacy_policy_link, // Replace %1$s
            $terms_of_use_link    // Replace %2$s
        );


        print '<label title="" for="terms" class="custom-control-label">'.$translated_text.'</label></div>';
        $ajax_nonce = wp_create_nonce("wprentals_activate_license_nonce");
        print '<input type="hidden" id="wprentals_activate_license_nonce" value="'.esc_html($ajax_nonce).'" />';
        print '<button type="submit" name="submit" id="wpestate_check_ajax_license" class="button wpresidence_button button-primary" > '.esc_html('Register License','wpresidence-core').'</button>';
        print '</div>';
    }
    
    
    
    
    
    
    public function register_license() {
        if ( !  check_ajax_referer( 'wprentals_activate_license_nonce',  'security' )  ) {
                wp_send_json_error( [
                        'success' => false,
                        'code'    => 'nonce_error',
                        'message' => __( 'Action is not allowed.', 'wpresidence-core' ),
                ] );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'You do not have access to this section.', 'wpresidence-core' );
        }

        $envato_username= isset( $_POST['wpestate_envato_username'] ) ? sanitize_text_field( $_POST['wpestate_envato_username'] ) : '';
        $email          = '***';
        $purchase_code  = isset( $_POST['wpestate_envato_code'] ) ? sanitize_text_field( $_POST['wpestate_envato_code'] ) : '';
       
        $verify = $this->verify_purchase_code( $email, $purchase_code,$envato_username);
		
        // Exit when verification throws an error.
        if ( is_wp_error( ( $verify ) ) ) {
                wp_send_json_error( [
                        'code'    => 'register_verify_error',
                        'message' => $verify->get_error_message(),
                ] );
        }

        update_option( 'envato_purchase_code_7896392', $purchase_code );
        update_option( self::ACCESS_TOKEN_OPTION_NAME, $verify['access_token'] );

            wp_send_json_success( [
                    'success'    => true,
                    'message' => __( 'You have successfully registered.', 'wpresidence-core' ),
            ] );
	}
        
        
        
        
        public function verify_purchase_code( $email, $purchase_code,$envato_username ) {
		

		if ( ! $purchase_code || $this->is_sha256( $purchase_code ) ) {
			return new WP_Error(
				'purchase_code_error',
				__( 'Purchase code is invalid.', 'wpresidence-core' )
			);
		}

		

		$data                     = $this->get_user_data();
		$data['website']          = $this->get_domain();
		$data['email']            = $email;
		$data['license_code']     = $purchase_code;
                $data['envato_username']  = $envato_username;
		$data['checktype']        = 'website';

          
                
		$request = wp_remote_post( self::WPESTATE_THEMES_API . '', [
			'body' => json_encode($data),
		] );
                $result = json_decode( wp_remote_retrieve_body( $request ), true );

		
		if ( is_wp_error( $request ) ) {
			return new WP_Error(
				'purchase_code_network_error',
				$request->get_error_message()
			);
		}

	
		if ( (  isset( $result['success'] ) &&  !$result['success'] )) {
			return new WP_Error(
				'purchase_code_verify_error',
				str_replace('"', '', $result['body'])
			);
		}

	

		return [
                        'success'       => true,
			'purchase_code' => $purchase_code,
			'access_token'  => $result['access_token'],
                        'message'=>$result['body']
		];
	}

        
        
        
        public function revoke_license() {
                if ( !  check_ajax_referer( 'wprentals_activate_license_nonce',  'security' )  ) {
                    wp_send_json_error( [
                            'success' => false,
                            'code'    => 'nonce_error',
                            'message' => __( 'Action is not allowed.', 'wpresidence-core' ),
                    ] );
                }

                if ( ! current_user_can( 'manage_options' ) ) {
                    wp_send_json_error( 'You do not have access to this section.', 'wpresidence-core' );
                }
		
		$data                 = [];
		$data['access_token'] = get_option( 'wpestate_access_token' );
                $data['license_code'] = get_option('envato_purchase_code_7896392');
                        
                        
                if( $data['access_token'] =='' || $data['license_code']=='' ){
                    	wp_send_json_error( [
				'code'    => 'revoke_verify_error',
                '$xxxxx '=>	$data ,
				'message' => 'License or token are empy',
			] );
                }

                $request = wp_remote_post( self::WPESTATE_THEMES_API . '', [
			'body' => json_encode($data),
		] );

		
		if ( is_wp_error( $request ) ) {
			wp_send_json_error( [
				'code'    => 'revoke_verify_network_error',
				'message' => $request->get_error_message(),
			] );
		}

		$result = json_decode( wp_remote_retrieve_body( $request ), true );

		if ( ! $result['success'] ) {
			wp_send_json_error( [
				'code'    => 'revoke_verify_error',
                '$xxxxx '=>	$data ,

                '$result'=>	$result,
				'message' => $result['message'],
			] );
		}

		delete_option('envato_purchase_code_7896392');
        delete_option('wpestate_access_token');
        delete_transient('envato_purchase_code_7896392_demos');


		wp_send_json_success( [
                        'success'=> true,
			'code'    => 'revoke_success',
			'message' => __( 'Your license has been successfully revoked.', 'wpresidence-core' ),
		] );
	}
        
        
        public function is_registered() {
       
            	$access_token = $this->has_access_token();
	        $purchase_code = $this->has_access_token() ;
                
                return (
			
			$access_token &&
			$purchase_code 
		);
	}
        
        
        public function get_demos_data(){
            if($this->is_registered() ){
              
                $demos_data= get_transient('envato_purchase_code_7896392_demos');
             
                if($demos_data==false){
                  
                    $enquire_answer  =  $this->wpestate_enquire_license(  $this->get_option( self::PURCHASE_CODE_OPTION_NAME)  );
                    if( isset($enquire_answer['demos']) ){
                     $demos_data      =  $enquire_answer['demos'];
                    }
                
                    return $demos_data;
                }else{
                     return $demos_data;
                }
            }else{
                return false;
            }
        }
        
        private function wpestate_enquire_license($wpestate_license_key){
            $data= array(
                        'license'   =>  trim($wpestate_license_key),
                        'action'    =>  'wpestate_envato_lic'
                    );

            $args=array(
                    'method' => 'POST',
                    'timeout' => 45,
                    'redirection' => 5,
                    'httpversion' => '1.1',
                    'sslverify' => true,
                    'blocking' => true,
                    'body' =>  $data,
                    'headers' => [
                          'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
                    ],
            );

        $url="https://support.wpestate.org/theme_license_check_wpresidence_cloud.php";
        $response = wp_remote_post( $url, $args );

        if ( is_wp_error( $response ) ) {
	        $error_message = $response->get_error_message();
            die($error_message);
	    } else {

            $output = json_decode(wp_remote_retrieve_body( $response ),true);

         
            
            if( isset($output['permited']) && $output['permited']=="yes" ){
               
              
                set_transient('envato_purchase_code_7896392_demos',$output['demos'],6600);
                return $output;
            }else{
               return false;
            }

	}
}

        
        
        
        
        
}WpestateFunk::get_instance();