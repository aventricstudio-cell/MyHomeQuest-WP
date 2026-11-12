<?php
/** MILLDONE
 * WpEstate Custom Authentication Class
 * src: libs\classes\WpEstate_Custom_Auth.php
 * This file contains the WpEstate_Custom_Auth class which handles custom login,
 * registration, and password reset functionality for the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage UserAuthentication
 * @since 1.0.0
 *
 * @dependencies
 * - WordPress core
 * - WpResidence theme options
 * - AJAX functionality
 *
 * Usage:
 * This class should be instantiated once on theme initialization.
 * Example: 
 *     function wpestate_init_custom_auth() {
 *         $auth = new WpEstate_Custom_Auth();
 *     }
 *     add_action('init', 'wpestate_init_custom_auth');
 */

class WpEstate_Custom_Auth {
    private $options;
    private static $instance = null;

    /**
     * Constructor
     * Initializes the class, sets up options and hooks
     */
    public function __construct() {
        $this->options = get_option('wpestate_auth_options');
        $this->setup_hooks();
    }

    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }



    /**
     * Set up WordPress hooks and filters
     */
    private function setup_hooks() {
        // AJAX hooks for login, register, and forgot password
        add_action('wp_ajax_nopriv_wpestate_ajax_login_user', array($this, 'process_login'));
        add_action('wp_ajax_wpestate_ajax_login_user', array($this, 'process_login'));

        add_action('wp_ajax_nopriv_wpestate_to_register_user', array($this, 'process_registration'));
        add_action('wp_ajax_wpestate_to_register_user', array($this, 'process_registration'));
       
    
        add_action('wp_ajax_nopriv_wpestate_ajax_forgot_pass_user', array($this, 'process_password_reset'));
        add_action('wp_ajax_wpestate_ajax_forgot_pass_user', array($this, 'process_password_reset'));
        add_action('wp_logout',array($this,'wpestate_go_home'));
        add_action('wp_head',array($this,'wpestate_hook_javascript'));
    }



    /**
     * WpResidence AJAX Login Functionality
     *
     * This file contains the AJAX login functionality for the WpResidence theme.
     * It handles user authentication and provides JSON responses for the login process.
     *
     * @package WpResidence
     * @subpackage UserAuthentication
     * @since 1.0.0
     *
     * @dependencies
     * - WordPress core
     * - WpResidence theme functions (wpestate_update_old_users)
     *
     * @uses add_action() To hook the login function to WordPress AJAX actions
     * @uses wp_verify_nonce() For security checks
     * @uses wp_signon() To authenticate users
     * @uses wp_send_json() To send JSON responses
     *
     * This file should be included in the theme's functions.php or as part of a larger authentication module.
     */

    public function process_login() {
        // Verify nonce for security
        if ( ! isset( $_POST['security_nonce'] ) || ! check_ajax_referer( 'wpestate_social_login_nonce', 'security_nonce', false ) ) {
            wp_send_json_error( array(
                'loggedin' => false,
                'message' => __( 'Security check failed.', 'wpresidence' ) ) );
             wp_die();
         }



        // Check if user is already logged in
        if ( is_user_logged_in() ) {
            wp_send_json(
                array(
                    'loggedin' => true,
                    'message'  => esc_html__( 'You are already logged in! Redirecting...', 'wpresidence' ),
                )
            );
        }

        // Sanitize and validate input
        $login_user = isset( $_POST['login_user'] ) ? sanitize_user( wp_unslash( $_POST['login_user'] ) ) : '';
        $login_pwd  = isset( $_POST['login_pwd'] ) ? $_POST['login_pwd'] : ''; // Passwords should not be sanitized
        $ispop      = isset( $_POST['ispop'] ) ? intval( $_POST['ispop'] ) : 0;

        // Check for empty username or password
        if ( empty( $login_user ) || empty( $login_pwd ) ) {
            wp_send_json(
                array(
                    'loggedin' => false,
                    'message'  => esc_html__( 'Username and/or Password field is empty!', 'wpresidence' ),
                )
            );
        }

        // Ensure session is started
        if ( ! session_id() ) {
            session_name( 'PHPSESSID' );
            session_start();
        }

        // Clear any existing auth cookies
        wp_clear_auth_cookie();

        // Attempt user authentication
        $user_signon = wp_signon(
            array(
                'user_login'    => $login_user,
                'user_password' => $login_pwd,
                'remember'      => false,
            ),
            is_ssl()
        );

        // Check for login errors
        if ( is_wp_error( $user_signon ) ) {
            wp_send_json(
                array(
                    'loggedin' => false,
                    'message'  => esc_html__( 'Wrong username or password!', 'wpresidence' ),
                )
            );
        } else {
            // Set current user and perform any necessary actions
            wp_set_current_user( $user_signon->ID );
            do_action( 'set_current_user' );

          
            
            // Get user profile picture
            $user_small_picture_id = get_the_author_meta( 'small_custom_picture', $user_signon->ID, true );
            if ( empty( $user_small_picture_id ) ) {
                $user_small_picture = get_theme_file_uri( '/img/default_user_small.png' );
            } else {
                $user_small_picture = wp_get_attachment_image_src( $user_small_picture_id, 'user_thumb' );
                $user_small_picture = $user_small_picture[0] ?? '';
            }


        

            // Send success response
            wp_send_json(
                array(
                    'loggedin' => true,
                    'ispop'    => $ispop,
                    'newuser'  => $user_signon->ID,
                    'message'  => esc_html__( 'Login successful, redirecting...', 'wpresidence' ),
                    'picture'        => $user_small_picture,
                    'menu'           => $this->generate_user_menus(),
                    'nonce_contact'  => wp_create_nonce( 'ajax-property-contact' )
                )
            );

            // Update user data if necessary
            wpestate_update_old_users( $user_signon->ID );
        }
    }



    /**
     * Generate user menus (desktop and mobile)
     *
     * This function generates both the desktop and mobile user menus
     * using a single output buffer operation.
     *
     * @return array An array containing the desktop and mobile menu HTML
     */
    private function generate_user_menus() {
        ob_start();
        ?>
        <ul id="user_menu_open" class="dropdown-menu menulist topmenux" role="menu" aria-labelledby="user_menu_trigger" style="display: none;">
            <?php wpestate_generate_user_menu(); ?>
        </ul>
        <?php
        $menu = ob_get_clean();

        ob_start();
        ?>
        <ul class="mobile_user_menu mobilex-menu" role="menu" aria-labelledby="user_menu_trigger">
            <?php
            if (class_exists('WooCommerce')) {
                global $wpestate_global_payments;
                $wpestate_global_payments->show_cart_icon_mobile();
            }
            wpestate_generate_user_menu();
            ?>
        </ul>
        <?php
        $menu_mobile = ob_get_clean();

        return array(
            'desktop' => $menu,
            'mobile' => $menu_mobile
        );
    }

    /**
     * WpResidence User Registration Process
     *
     * This file contains the process_registration method of the WpEstate_Custom_Auth class,
     * which handles the user registration process in the WpResidence theme.
     *
     * @package WpResidence
     * @subpackage UserManagement
     * @since 1.0.0
     *
     * @dependencies WordPress core, WpResidence theme functions, reCAPTCHA API
     *
     * Usage:
     * This method is typically called via an AJAX request when a user submits
     * the registration form. It performs various checks and validations before
     * creating a new user account.
     *
     * @return void
    */
    public function process_registration() {
        // Verify the nonce for security

        if ( ! isset( $_POST['security_nonce'] ) || ! check_ajax_referer( 'register_ajax_nonce_topbar', 'security_nonce', false ) ) {
           wp_send_json_error( array( 'message' => __( 'Security check failed.', 'wpresidence' ) ) );
            wp_die();
        }

        // Respect the site-wide registration setting
        if ( 'no' === wpresidence_get_option( 'wp_estate_allow_user_registration', '' ) ) {
            wp_send_json_error( array( 'message' => __( 'User registration is disabled.', 'wpresidence-core' ) ) );
            wp_die();
        }

        $type = intval( $_POST['type'] );

        // Determine the requested user role and ensure it is permitted.
        $permited_roles             = wpresidence_get_option( 'wp_estate_visible_user_role', '' );
        $visible_user_role_dropdown = wpresidence_get_option( 'wp_estate_visible_user_role_dropdown', '' );
        $user_roles                 = function_exists( 'wpresidence_rolemap' ) ? wpresidence_rolemap() : array();
        $new_user_type              = isset( $_POST['new_user_type'] ) ? sanitize_text_field( wp_unslash( $_POST['new_user_type'] ) ) : '';

        // Abort if no roles are allowed for public registration.
        if ( ! is_array( $permited_roles ) || empty( $permited_roles ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Admin: Select the roles that can register.', 'wpresidence' ) ) );
            wp_die();
        }

        // When the role selector is hidden, ignore any user-supplied value and use the first permitted role.
        if ( 'no' === $visible_user_role_dropdown ) {
            $new_user_type = $permited_roles[0];
        }

        // Validate the requested role against the whitelist and known role map.
        if ( empty( $new_user_type ) || ! in_array( $new_user_type, $permited_roles, true ) || ! array_key_exists( $new_user_type, $user_roles ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid user type.', 'wpresidence' ) ) );
            wp_die();
        }

        // Validate reCAPTCHA if it's enabled
        if (wpresidence_get_option('wp_estate_use_captcha', '') == 'yes') {
            if (!isset($_POST['capthca']) || $_POST['capthca'] == '') {
                echo json_encode(array(
                    'register' => false,
                    'message' => esc_html__('Wrong captcha', 'wpresidence')
                ));
                exit();
            }

            $secret = wpresidence_get_option('wp_estate_recaptha_secretkey', '');
            $cappval = $_POST['capthca'];

            $response = $this->wpestate_return_recapthca($secret, $cappval);

            if ($response['success'] === false) {
                echo json_encode(array(
                    'register' => false,
                    'message' => esc_html__('Captcha Invalidated - Refresh and try again.', 'wpresidence')
                ));
                exit();
            }
        }

        // Sanitize and validate user input
     
        $user_email = trim( sanitize_email( $_POST['user_email_register'] ) );
        $user_name  = trim( sanitize_text_field( $_POST['user_login_register'] ) );
        $enable_user_pass_status = esc_html( wpresidence_get_option( 'wp_estate_enable_user_pass', '' ) );

        // Validate username format
        if (preg_match("/^[0-9A-Za-z_]+$/", $user_name) == 0) {
            echo json_encode(array(
                'register' => false,
                'message' => esc_html__('Invalid username (do not use special characters or spaces)!', 'wpresidence')
            ));
            die();
        }

        // Check for empty username or email
        if ($user_email == '' || $user_name == '') {
            echo json_encode(array(
                'register' => false,
                'message' => esc_html__('Username and/or Email field is empty!', 'wpresidence')
            ));
            exit();
        }

        // Validate email format
        if (filter_var($user_email, FILTER_VALIDATE_EMAIL) === false) {
            echo json_encode(array(
                'register' => false,
                'message' => esc_html__('The email doesn\'t look right!', 'wpresidence')
            ));
            exit();
        }

        // Validate email domain
        $domain = mb_substr(strrchr($user_email, "@"), 1);
        if ($domain != '' && !checkdnsrr($domain)) {
            echo json_encode(array(
                'register' => false,
                'message' => esc_html__('The email\'s domain doesn\'t look right.', 'wpresidence')
            ));
            exit();
        }

        // Check if username already exists
        $user_id = username_exists($user_name);
        if ($user_id) {
            echo json_encode(array(
                'register' => false,
                'message' => esc_html__('Username already exists. Please choose a new one.', 'wpresidence')
            ));
            exit();
        }

        // Handle password validation if user-defined passwords are enabled
        if ($enable_user_pass_status == 'yes') {
            $user_pass = trim(sanitize_text_field(($_POST['user_pass'])));
            $user_pass_retype = trim(sanitize_text_field(($_POST['user_pass_retype'])));

            if ($user_pass == '' || $user_pass_retype == '') {
                echo json_encode(array(
                    'register' => false,
                    'message' => esc_html__('One of the password fields is empty!', 'wpresidence')
                ));
                exit();
            }

            if ($user_pass !== $user_pass_retype) {
                echo json_encode(array(
                    'register' => false,
                    'message' => esc_html__('Passwords do not match', 'wpresidence')
                ));
                exit();
            }
        }

        // Create new user if email doesn't exist
        if (!$user_id && email_exists($user_email) == false) {
            if ($enable_user_pass_status == 'yes') {
                $user_password = $user_pass;
            } else {
                $user_password = wp_generate_password(12, false);
            }

            $user_id = wp_create_user($user_name, $user_password, $user_email);

            if (is_wp_error($user_id)) {
                
                echo json_encode(array(
                    'register'=>false,
                    'message'=>esc_html__('Error creating the user', 'wpresidence'))
                );
                die();

            } else {

                // Assign the validated role to the new user.
                $role_assigned = wpresidence_register_user_role( $user_id, $new_user_type );
                if ( ! $role_assigned ) {
                    echo json_encode(
                        array(
                            'register' => false,
                            'message'  => esc_html__( 'Role Assignment Failed.', 'wpresidence' ),
                        )
                    );
                    die();
                }
           

                if ($enable_user_pass_status == 'yes') {
                    echo json_encode(array(
                        'register' => true,
                        'message' => esc_html__('Your account was created and you can login now!', 'wpresidence')
                    ));
                } else {
                    echo json_encode(array(
                        'register' => true,
                        'message' => esc_html__('An email with the generated password was sent!', 'wpresidence')
                    ));
                }

                // Update user profile, store the selected role, and create the related post type if applicable.
                wpestate_update_profile( $user_id );
                wpestate_wp_new_user_notification( $user_id, $user_password );
                update_user_meta( $user_id, 'cc', $new_user_type );
                $this->wpestate_register_as_user( $user_name, $user_id, $new_user_type );
            }
        } else {
            echo json_encode(array(
                'register' => false,
                'message' => esc_html__('Email already exists. Please choose a new one.', 'wpresidence')
            ));
            exit();
        }
        die();
    }
  


    /**
     * WpResidence reCAPTCHA Verification Handler
     *
     * This file contains the wpestate_return_recaptcha function, which is responsible for
     * verifying reCAPTCHA responses in the WpResidence theme.
     *
     * @package WpResidence
     * @subpackage Security
     * @since 1.0.0
     *
     * @dependencies WordPress core, Google reCAPTCHA API

     * Verify a reCAPTCHA response with Google's API.
     *
     * @param string $secret  The reCAPTCHA secret key.
     * @param string $captcha The reCAPTCHA response token to verify.
     * 
     * @return array|null The decoded JSON response from Google, or null on failure.
     */
    function wpestate_return_recapthca($secret, $captcha) {
        // Sanitize the remote IP address
        $remoteip = sanitize_text_field($_SERVER['REMOTE_ADDR']);

        // Google reCAPTCHA API endpoint
        $url = 'https://www.google.com/recaptcha/api/siteverify';

        // Prepare the POST data
        $post_data = http_build_query([
            'secret'   => $secret,
            'response' => $captcha,
            'remoteip' => $remoteip
        ]);

        // Set up the options for the HTTP request
        $options = [
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            ]
        ];

        // Create a stream context
        $context = stream_context_create($options);

        // Make the API request
        $result_json = file_get_contents($url, false, $context);

        // Check if the request was successful
        if ($result_json === false) {
            return null;
        }

        // Decode the JSON response
        $result = json_decode($result_json, true);

        // Check if JSON decoding was successful
        if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $result;
    }



    /**
    * WpResidence User Registration Handler
    *
    * This file contains the wpestate_register_as_user function, which is responsible for
    * registering users as agents, agencies, or developers in the WpResidence theme.
    *
    * @package WpResidence
    * @subpackage UserManagement
    * @since 1.0.0
    *
    * @dependencies WordPress core, WpResidence theme functions
    *
    * Usage:
    * This function is typically called after a new user registration when the user
    * selects a specific role (agent, agency, or developer). It creates a corresponding
    * custom post type for the user and sets up the necessary metadata.
    */

    function wpestate_register_as_user($user_name, $user_id, $new_user_type = 0, $first_name = '', $last_name = '') {
        // Initialize variables
        $post_type               = '';
        $app_type                = '';
        $app_type_no_translation = '';
        $email_meta_key          = '';

        // Determine the post type and application type based on the new user type
        switch ($new_user_type) {
            case 'wpresidence_agent_role':
                $post_type               = 'estate_agent';
                $app_type                = esc_html__('Agent', 'wpresidence');
                $app_type_no_translation = 'wpresidence_agent_role';
                $email_meta_key          = 'agent_email';
                break;
            case 'wpresidence_agency_role':
                $post_type               = 'estate_agency';
                $app_type                = esc_html__('Agency', 'wpresidence');
                $app_type_no_translation = 'wpresidence_agency_role';
                $email_meta_key          = 'agency_email';
                break;
            case 'wpresidence_developer_role':
                $post_type               = 'estate_developer';
                $app_type                = esc_html__('Developer', 'wpresidence');
                $app_type_no_translation = 'wpresidence_developer_role';
                $email_meta_key          = 'developer_email';
                break;
        }

        // Get the admin submission user role option
        $admin_submission_user_role = wpresidence_get_option('wp_estate_admin_submission_user_role', '');
        $admin_submission_user_role = empty($admin_submission_user_role) ? array() : $admin_submission_user_role;

        // Determine the post status based on admin submission settings
        $post_approve = in_array($app_type_no_translation, $admin_submission_user_role, true) ? 'pending' : 'publish';

        // Create a new post for the user if a valid post type is determined
        if (!empty($post_type)) {
            $post = array(
                'post_title'  => sanitize_text_field($user_name),
                'post_status' => $post_approve,
                'post_type'   => $post_type,
            );
            $post_id = wp_insert_post($post);

            // Update post meta and user meta
            update_post_meta($post_id, 'user_meda_id', $user_id);
            update_user_meta($user_id, 'user_agent_id', $post_id);

            // Get user email and update post meta
            $user_email = get_the_author_meta('user_email', $user_id);
            if (!empty($email_meta_key)) {
                update_post_meta($post_id, $email_meta_key, sanitize_email($user_email));
            }
        }

        // Update user meta with first and last name if provided
        if (!empty($first_name)) {
            update_user_meta($user_id, 'first_name', sanitize_text_field($first_name));
        }
        if (!empty($last_name)) {
            update_user_meta($user_id, 'last_name', sanitize_text_field($last_name));
        }
    }

 
    
    
    
    /**
     * Display authentication forms
     *
     * @param string $context The context in which the form is being displayed 
     *                        (e.g., 'modal', 'widget', 'shortcode', 'mobile')
     * @param string $type    The type of form to display ('login', 'register', 'forgot')
     */
    
    public function display_auth_form($context = 'modal', $type = 'all',$type2='') {
        $output = '';
        
        // Login Form
        if ($type === 'all' || $type === 'login') {
            $output .= $this->get_template_part('login', $context);
        }
        
        // Register Form (only if user registration is allowed)
        if ( ($type === 'all' || $type === 'register')  ) {
            $output .=''. $this->get_template_part('register', $context, $type2);
        }
        
        // Forgot Password Form
        if ($type === 'all' || $type === 'forgot') {
            $output .= $this->get_template_part('forgot', $context);
        }
        
        $output .= $this->get_template_part('modal-control', $context);
        
        return $output;
    }
    
    private function get_template_part($slug, $context,$type2='') {
        $template_path = get_template_directory() . "/templates/login_register_forms/{$slug}-{$context}.php";

        if (file_exists($template_path)) {
            ob_start();
            include $template_path;
            return ob_get_clean();
        }
        
        return '';
    }
    
    private function get_user_type_selection($context) {
        $output = '';
        $user_types = function_exists('wpresidence_rolemap') ? wpresidence_rolemap() : array();
        $permited_roles = wpresidence_get_option('wp_estate_visible_user_role', '');
        $visible_user_role_dropdown = wpresidence_get_option('wp_estate_visible_user_role_dropdown', '');
        
        if ($visible_user_role_dropdown === 'yes' && is_array($permited_roles)) {
            $output .= '<select id="new_user_type_' . $context . '" name="new_user_type_' . $context . '" class="form-control">';
            $output .= '<option value="0">' . esc_html__('Select User Type', 'wpresidence') . '</option>';
            foreach ($user_types as $key => $name) {
                if (in_array($key, $permited_roles)) {
                    $output .= '<option value="' . esc_attr($key) . '">' . esc_html($name) . '</option>';
                }
            }
            $output .= '</select>';
        }
        
        return $output;
    }







    /**
     * Process password reset request
     */
    public function process_password_reset() {
        // Verify nonce for security
        if ( ! isset( $_POST['security_nonce'] ) || ! check_ajax_referer( 'forgot_ajax_nonce_topbar', 'security_nonce', false ) ) {
            wp_send_json_error( array(
                'loggedin' => false,
                'message' => __( 'Security check failed.', 'wpresidence' ) ) );
             wp_die();
        }

        global $wpdb;
        $post_id      = isset($_POST['postid']) ? intval($_POST['postid']) : 0;
        $forgot_email = isset($_POST['forgot_email']) ? sanitize_email($_POST['forgot_email']) : '';
    
        if (empty($forgot_email)) {
            wp_send_json([
                'reset' => false,
                'message' => esc_html__('Email field is empty!', 'wpresidence')
            ]);
        }

        $user_data = $this->get_user_data($forgot_email);

        if (empty($user_data) || isset($user_data->caps['administrator'])) {
            wp_send_json([
                'reset' => false,
                'message' => esc_html__('Invalid email address or username!', 'wpresidence')
            ]);
        }

        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;

        $key = $this->get_or_generate_reset_key($user_login);

        $this->send_reset_password_email($user_email, $user_login, $key, $post_id, $type);

        wp_send_json([
            'reset' => true,
            'message' => esc_html__('We have just sent you an email with Password reset instructions.', 'wpresidence')
        ]);

    }



    /**
     * Get user data by email or username
     *
     * @param string $user_input
     * @return WP_User|false
     */
    private function get_user_data($user_input) {
        if (strpos($user_input, '@')) {
            return get_user_by('email', $user_input);
        } else {
            return get_user_by('login', $user_input);
        }
    }

    /**
     * Get existing or generate new reset key
     *
     * @param string $user_login
     * @return string
     */
    private function get_or_generate_reset_key($user_login) {
        global $wpdb;
        $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
        
        if (empty($key)) {
            $key = wp_generate_password(20, false);
            $wpdb->update($wpdb->users, ['user_activation_key' => $key], ['user_login' => $user_login]);
        }

        return $key;
    }

    /**
     * Send reset password email
     *
     * @param string $user_email
     * @param string $user_login
     * @param string $key
     * @param int $post_id
     * @param int $type
     */
    private function send_reset_password_email($user_email, $user_login, $key, $post_id, $type) {
        $headers = 'From: ' . wpestate_return_sending_email() . "\r\n";
        $arguments = [
            'reset_link' => wpestate_tg_validate_url($post_id, $type) . "action=reset_pwd&key=$key&login=" . rawurlencode($user_login)
        ];
        wpestate_select_email_type($user_email, 'password_reset_request', $arguments);
    }

  
 
    /*
    * logout user
    */
    public function wpestate_go_home(){
        wp_redirect( esc_url( home_url('/') ) );
        exit();
    }
 
    /**
     * Handle password reset requests
     *
     * This function processes password reset requests, generates a new password,
     * and sends it to the user via email. It should be hooked to an appropriate
     * WordPress action, likely an init or template_redirect hook.
     *
     * @since 1.0.0
     * @global wpdb $wpdb WordPress database abstraction object.
     *
     * @todo Rename function to better reflect its purpose (e.g., wpestate_process_password_reset)
     * @todo Consider separating presentation logic from business logic
     * @todo Implement nonce verification for additional security
     * @todo Use wp_rand_hash() instead of wp_generate_password() for reset key
     */
    public function wpestate_hook_javascript() {
        global $wpdb;

        // Check if this is a password reset request
        if (isset($_GET['key'], $_GET['action']) && $_GET['action'] === "reset_pwd") {
            $reset_key = sanitize_text_field($_GET['key']);
            $user_login = sanitize_user($_GET['login']);

            // Retrieve user data based on reset key and login
            $user_data = $wpdb->get_row($wpdb->prepare(
                "SELECT ID, user_login, user_email FROM $wpdb->users
                WHERE user_activation_key = %s AND user_login = %s",
                $reset_key,
                $user_login
            ));

            if (!empty($user_data)) {
                $user_login = $user_data->user_login;
                $user_email = $user_data->user_email;

                // Generate and set new password
                $new_password = wp_generate_password(12, false);
                wp_set_password($new_password, $user_data->ID);

                // Prepare email message
                $message = sprintf(
                    esc_html__('Your new password for the account at: %s', 'wpresidence'),
                    get_bloginfo('name')
                ) . "\r\n\r\n";
                $message .= sprintf(esc_html__('Username: %s', 'wpresidence'), $user_login) . "\r\n";
                $message .= sprintf(esc_html__('Password: %s', 'wpresidence'), $new_password) . "\r\n\r\n";
                $message .= sprintf(
                    esc_html__('You can now login with your new password at: %s', 'wpresidence'),
                    home_url('/')
                );

                // Set email headers
                $headers = array(
                    'From: ' . wpestate_return_sending_email(),
                    'Reply-To: ' . wpestate_return_sending_email(),
                    'X-Mailer: PHP/' . phpversion()
                );

                // Send password reset email
                $arguments = array('user_pass' => $new_password);
                wpestate_select_email_type($user_email, 'password_reseted', $arguments);

                // Display success message
                $mess = '<div class="login-alert">' . esc_html__('A new password was sent via email!', 'wpresidence') . '</div>';
            } else {
                // Invalid reset key or user login
                wp_die(esc_html__('Not a Valid Key.', 'wpresidence'), esc_html__('Password Reset Error', 'wpresidence'), 400);
            }

            // Display final message
            echo '<div class="login_alert_full">' . esc_html__('We have just sent you a new password. Please check your email!', 'wpresidence') . '</div>';
        }
    }

    // Add more helper methods as needed
}WpEstate_Custom_Auth::get_instance();