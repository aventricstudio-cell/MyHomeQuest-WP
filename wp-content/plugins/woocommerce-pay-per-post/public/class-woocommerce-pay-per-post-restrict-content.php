<?php

/** @noinspection PhpUnused */
namespace PRAMADILLO;

use Woocommerce_Pay_Per_Post_Helper;
use Pramadillo\PayForPost\Carbon\Carbon;
use Pramadillo\PayForPost\Carbon\CarbonInterface;
/**
 * Class Woocommerce_Pay_Per_Post_Restrict_Content
 */
class Woocommerce_Pay_Per_Post_Restrict_Content {
    public const RESTRICT_CONTENT_DEFAULT_MESSAGE = "<h1>Oops, Restricted Content</h1><p>We are sorry but this post is restricted to folks that have purchased this page.</p>[products ids='{{product_id}}']";

    public $protection_checks;

    public $protection_type;

    public $user_post_info;

    public $current_user;

    public $product_ids;

    public $product_count;

    protected $post_id;

    protected $integrations;

    protected $should_track_pageview;

    protected $available_templates;

    public final function __construct( $post_id = null, $should_track_pageview = true ) {
        global $product_ids;
        $this->product_ids = $product_ids;
        $this->should_track_pageview = $should_track_pageview;
        //The Post ID is null because we use this class for displaying shortcodes as well as within the loop
        if ( is_null( $post_id ) ) {
            $this->post_id = get_the_ID();
        } else {
            $this->post_id = $post_id;
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/__construct  POST ID Passed in, populating product_ids from get_product_ids_by_post_id();' );
            $this->product_ids = Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id( $post_id );
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/__construct  $this->product_ids = ' . print_r( $this->product_ids, true ) );
        }
        $this->current_user = wp_get_current_user();
        $this->protection_checks = [
            'check_if_logged_in',
            'check_if_protected',
            'check_if_should_show_paywall',
            'check_if_admin_call',
            'check_if_purchased',
            'check_if_admin_user_have_access',
            'check_if_user_role_has_access',
            'check_if_has_access',
            'check_if_post_contains_subscription_products'
        ];
        $this->available_templates = [
            'expiration-status' => 'expiration-status.php',
            'pageview-status'   => 'pageview-status.php',
        ];
        if ( is_array( $this->product_ids['product_ids'] ) ) {
            $this->product_count = count( $this->product_ids['product_ids'] );
        }
        $this->protection_type = $this->product_ids['protection_type'];
    }

    public function register_shortcodes() {
        add_shortcode( 'wc-pay-for-post-status', [$this, 'process_status_shortcode'] );
    }

    /**
     * Function used to set to not track page view
     *
     * @param bool $track
     */
    public function set_track_pageview( bool $track = true ) {
        $this->should_track_pageview = $track;
    }

    /*
    |--------------------------------------------------------------------------
    | Protection Checks
    |--------------------------------------------------------------------------
    |
    | Each of these functions go along with the $protection_checks
    | We loop through each one and test
    |
    */
    public function check_if_admin_call() : bool {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_admin_call  - BACKTRACE - Called From - ' . print_r( debug_backtrace()[1]['function'], true ) );
        if ( is_admin() || !$this->post_id ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_admin_call  - Is an admin call' );
            return true;
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_admin_call  - Is NOT an admin call' );
        return false;
    }

    public function check_if_admin_user_have_access() : bool {
        $admins_allowed_access = (bool) get_option( WC_PPP_SLUG . '_allow_admins_access_to_protected_posts', false );
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_admin_user_have_access  - BACKTRACE - Called From - ' . print_r( debug_backtrace()[1]['function'], true ) );
        // Check and see if admins are allowed to view protected content.
        if ( $admins_allowed_access && is_super_admin() ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_admin_user_have_access  - Administrators HAVE access to all protected posts via settings' );
            return true;
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_admin_user_have_access  - Administrators DO NOT HAVE access to all protected posts via settings' );
        return false;
    }

    public function check_if_user_role_has_access() : bool {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_user_role_has_access  - BACKTRACE - Called From - ' . print_r( debug_backtrace()[1]['function'], true ) );
        $allowed_user_roles = [];
        $current_user_roles = $this->get_current_user_roles();
        foreach ( $current_user_roles as $role ) {
            if ( in_array( $role, $allowed_user_roles ) ) {
                return true;
            }
        }
        return false;
    }

    public function check_if_purchased() : bool {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_purchased - Called.' );
        // Log associated products
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_purchased - Products associated with page = ' . print_r( $this->product_ids['product_ids'] ?? [], true ) );
        // Merge section product IDs if they exist
        $product_ids = (array) $this->product_ids['product_ids'] ?? [];
        if ( !empty( $this->product_ids['section_product_ids'] ) ) {
            $product_ids = array_merge( $product_ids, $this->product_ids['section_product_ids'] );
        }
        // If there are no product IDs, return false
        if ( empty( $product_ids ) ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_purchased - No products found for this page.' );
            return false;
        }
        // Loop through product IDs to check ownership
        foreach ( $product_ids as $id ) {
            $id = trim( $id );
            // ID sanitization
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_purchased - Checking product ID: ' . $id );
            // Check if this is a subscription product
            $is_subscription_product = Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_subscriptions() && isset( $this->integrations['woocommerce-subscriptions'] ) && $this->integrations['woocommerce-subscriptions']->is_subscription_product( $id );
            // Check for subscription product - must have active subscription
            if ( $is_subscription_product ) {
                if ( Woocommerce_Pay_Per_Post_Helper::customer_has_purchased_product( $this->current_user->user_email, $this->current_user->ID, $id ) && $this->check_if_is_subscriber() ) {
                    Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_purchased - User has active subscription for product ID: ' . $id );
                    return true;
                }
                Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_purchased - Subscription product ID: ' . $id . ' - user does not have active subscription, skipping' );
                continue;
                // Skip to next product - don't check as regular product
            }
            // Check for regular (non-subscription) product purchase
            if ( Woocommerce_Pay_Per_Post_Helper::customer_has_purchased_product( $this->current_user->user_email, $this->current_user->ID, $id ) ) {
                Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_purchased - User has purchased product ID: ' . $id );
                return true;
            }
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_purchased - User has NOT purchased product ID: ' . $id );
        }
        // If no purchases are found
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_purchased - User has not purchased any associated products.' );
        return false;
    }

    public function check_if_logged_in() : bool {
        $logged_in = is_user_logged_in();
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_logged_in  - ' . (( is_user_logged_in() ? 'true' : 'false' )) );
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_logged_in  - BACKTRACE - Called From - ' . print_r( debug_backtrace()[1]['function'], true ) );
        return $logged_in;
    }

    public function check_if_has_access() : bool {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_has_access  - has been called.' );
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_has_access  - BACKTRACE - Called From - ' . print_r( debug_backtrace()[1]['function'], true ) );
        switch ( $this->protection_type ) {
            case 'standard':
            // Since we already check to see if they purchased the product standard protection returns true all the time.
            case 'delay':
                // Delay protection is same protection as standard, just difference in when to display pay wall, we already checked to see if they purchased product we return true.
                Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_has_access  - Protection Type is Standard or Delayed' );
                Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_has_access  - Calling Woocommerce_Pay_Per_Post_Restrict_Content/check_if_purchased()' );
                return $this->check_if_purchased();
            case 'page-view':
                Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_has_access  - Protection Type is Page View Protection' );
                return $this->has_access_page_view_protection__premium_only();
            case 'expire':
                Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_has_access  - Protection Type is Expiration Protection' );
                return $this->has_access_expiry_protection__premium_only();
        }
        return true;
    }

    public function check_if_is_paid_memberships_pro_member() {
        //Is user a Paid Memberships Pro Member?
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_paid_membership_pro() && isset( $this->integrations['paid-memberships-pro'] ) ) {
            $is_member = $this->integrations['paid-memberships-pro']->is_member( $this->post_id );
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_is_paid_memberships_pro_member  - Is the user a Paid Memberships Pro Member? - ' . (( $is_member ? 'true' : 'false' )) );
            return $is_member;
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'User is NOT a Paid Memberships Pro member, as Paid Memberships Pro is not installed.' );
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_is_paid_memberships_pro_member  - User is NOT a Paid Memberships Pro member, as Paid Memberships Pro is not installed.' );
        return false;
    }

    public function check_if_is_member() {
        //Is user a WooCommerce Memberships Member?
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_memberships() && isset( $this->integrations['woocommerce-memberships'] ) ) {
            $is_member = $this->integrations['woocommerce-memberships']->is_member( $this->post_id );
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_is_member  - Is the user a WooMemberships Member? - ' . (( $is_member ? 'true' : 'false' )) );
            return $is_member;
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_is_member  - User is NOT a member, as WooMemberships is not installed.' );
        return false;
    }

    public function check_if_is_subscriber() {
        //Is user a WooCommerce Subscriptions Subscriber?
        if ( Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_subscriptions() && isset( $this->integrations['woocommerce-subscriptions'] ) ) {
            $is_subscriber = $this->integrations['woocommerce-subscriptions']->is_subscriber( $this->post_id );
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_is_subscriber  - Does the user have a valid subscription? - ' . (( $is_subscriber ? 'true' : 'false' )) );
            return $is_subscriber;
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_is_subscriber  - User is NOT a subscriber, as WooSubscriptions is not installed.' );
        return false;
    }

    public function check_if_protected() : bool {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_protected  - BACKTRACE - Called From - ' . print_r( debug_backtrace()[1]['function'], true ) );
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_protected  - product_ids - ' . var_export( $this->product_ids['product_ids'], true ) );
        if ( $this->protection_type || is_array( $this->product_ids['product_ids'] ) || !empty( $this->product_ids['product_ids'] ) ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_protected  - Page is NOT protected.' );
            return true;
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_protected  - Page IS protected.' );
        return false;
    }

    public function check_if_post_contains_subscription_products() {
        if ( !Woocommerce_Pay_Per_Post_Helper::can_use_woocommerce_subscriptions() || !isset( $this->integrations['woocommerce-subscriptions'] ) ) {
            return false;
        }
        $post_has_subscription_product = $this->integrations['woocommerce-subscriptions']->post_contains_subscription_products( $this->post_id );
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_post_contains_subscription_products  - Does Post/Page Contain Subscription Products? - ' . (( $post_has_subscription_product ? 'true' : 'false' )) );
        return $post_has_subscription_product;
    }

    public function check_if_post_contains_membership_products() {
        if ( !isset( $this->integrations['woocommerce-memberships'] ) ) {
            return false;
        }
        $post_has_membership_product = $this->integrations['woocommerce-memberships']->post_contains_membership_products( $this->post_id );
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_post_contains_membership_products  - Does Post Contain Membership Products? - ' . (( $post_has_membership_product ? 'true' : 'false' )) );
        return $post_has_membership_product;
    }

    public function check_if_post_contains_paid_memberships_pro_membership_products() {
        if ( !isset( $this->integrations['paid-memberships-pro'] ) ) {
            return false;
        }
        $post_has_membership_product = $this->integrations['paid-memberships-pro']->post_contains_membership_products( $this->post_id );
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_post_contains_paid_memberships_pro_membership_products  - Does Post Contain Paid Membership Pro Membership Products? - ' . (( $post_has_membership_product ? 'true' : 'false' )) );
        return $post_has_membership_product;
    }

    public function check_if_should_show_paywall() : bool {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/check_if_should_show_paywall  - BACKTRACE - Called From - ' . print_r( debug_backtrace()[1]['function'], true ) );
        switch ( $this->protection_type ) {
            case 'standard':
            case 'page-view':
            case 'expire':
                return true;
            case 'delay':
                return $this->enable_delay_protection_paywall__premium_only();
        }
        return true;
    }

    /**
     * @return bool
     * This is really $show_paywall.  So if return false that means to NOT show the paywll.  Return True to show paywall.
     */
    public function can_user_view_content() : bool {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/can_user_view_content  - BACKTRACE - Called From - ' . print_r( debug_backtrace()[1]['function'], true ) );
        $check_results = [];
        foreach ( $this->protection_checks as $check ) {
            $check_results[$check] = $this->{$check}();
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/can_user_view_content  Check Results ' . var_export( $check_results, true ) );
        if ( isset( $_GET['wc_ppp_debug'] ) && "true" === $_GET['wc_ppp_debug'] ) {
            echo '<pre>';
            echo '<h5>Post ID = ' . $this->post_id . '</h5>';
            var_dump( $check_results );
            echo '</pre>';
        }
        if ( $check_results['check_if_admin_call'] || !$check_results['check_if_protected'] || !$check_results['check_if_should_show_paywall'] || $check_results['check_if_admin_user_have_access'] || $check_results['check_if_user_role_has_access'] || $check_results['check_if_has_access'] && !$check_results['check_if_post_contains_subscription_products'] ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/can_user_view_content  Check Results FAILED returning FALSE' );
            return true;
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/can_user_view_content  Check Results PASSED returning TRUE' );
        if ( class_exists( 'Elementor\\Plugin' ) && !is_bool( \Elementor\Plugin::$instance->documents->get( $this->post_id ) ) && \Elementor\Plugin::$instance->documents->get( $this->post_id )->is_built_with_elementor() && !get_post_meta( $this->post_id, WC_PPP_SLUG . '_product_ids', true ) ) {
            return true;
        }
        return false;
    }

    /**
     * @param $unfiltered_content
     *
     * @return string
     */
    public function show_paywall( $unfiltered_content ) : string {
        //TODO document 3.1.2
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/show_paywall()  Called.' );
        return '<div class="wc_ppp_paywall wc_ppp_paywall_' . $this->post_id . '">' . $this->get_paywall_content( $unfiltered_content ) . '</div>';
    }

    /**
     * @param $unfiltered_content
     *
     * @return string
     */
    public function show_content( $unfiltered_content ) : string {
        $show_warnings = get_post_meta( $this->post_id, WC_PPP_SLUG . '_show_warnings', true );
        if ( 'expire' === $this->protection_type && !is_admin() && !$this->check_if_admin_user_have_access() && apply_filters( 'wc_pay_per_post_enable_javascript_expiration_refresh', true ) ) {
            $this->countdown_refresh();
        }
        if ( $show_warnings && !is_admin() ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Page/Post has show_warnings enabled' );
            $position = apply_filters( 'wc_pay_for_post_show_warnings_position', 'top' );
            switch ( $this->protection_type ) {
                case 'page-view':
                    $template_file = do_shortcode( '[wc-pay-for-post-status template="page-view"]' );
                    break;
                case 'expire':
                    $template_file = do_shortcode( '[wc-pay-for-post-status template="expiration-status"]' );
                    break;
                default:
                    return $unfiltered_content;
            }
            if ( 'top' === $position ) {
                return $template_file . $unfiltered_content;
            } else {
                return $unfiltered_content . $template_file;
            }
        }
        return $unfiltered_content;
    }

    public function is_expired( $post_id ) : bool {
        return !$this->has_access_expiry_protection__premium_only( $post_id );
    }

    public function process_status_shortcode( $atts ) {
        $template = 'pageview-status';
        if ( isset( $atts['template'] ) && array_key_exists( $atts['template'], $this->available_status_templates() ) ) {
            $template = $atts['template'];
        }
        switch ( $template ) {
            case 'pageview-status':
                return $this->shortcode_pageview_status( $template );
            case 'expiration-status':
                return $this->shortcode_expiration_status( $template );
        }
        return false;
        //invalid template
    }

    protected function get_current_user_roles() : array {
        return $this->current_user->roles;
    }

    /**
     * @param $unfiltered_content
     *
     * @return string
     */
    protected function get_paywall_content( $unfiltered_content ) : string {
        global $product_ids;
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/get_paywall_content  - has been called.' );
        // Checks if Divi or Beaver are on edit mode
        $is_frontend_edit = !empty( $_GET['et_fb'] ) || isset( $_GET['fl_builder'] );
        // This filter should check for plugins that edit the content in the frontend!!!
        $is_frontend_edit = apply_filters(
            'wc_pay_for_post_is_frontend_edit',
            $is_frontend_edit,
            10,
            1
        );
        if ( is_user_logged_in() && $is_frontend_edit ) {
            $user = wp_get_current_user();
            if ( in_array( 'administrator', $user->roles ) || in_array( 'author', $user->roles ) ) {
                Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/get_paywall_content  - DIVI is on edit mode. Returning unfiltered content.' );
                return $unfiltered_content;
            }
        }
        $default_paywall_content = get_option( WC_PPP_SLUG . '_restricted_content_default', _x( self::RESTRICT_CONTENT_DEFAULT_MESSAGE, 'wc_pay_per_post' ) );
        $override_paywall_content = get_post_meta( $this->post_id, WC_PPP_SLUG . '_restricted_content_override', true );
        $override_paywall_content = apply_filters(
            'wc_pay_for_post_override_paywall_content',
            $override_paywall_content,
            10,
            1
        );
        $paywall_content = ( empty( $override_paywall_content ) ? $default_paywall_content : $override_paywall_content );
        if ( isset( $product_ids['product_ids'] ) ) {
            //TODO add filter to remove do_shortcode
            return wpautop( do_shortcode( Woocommerce_Pay_Per_Post_Helper::replace_tokens( $paywall_content, $product_ids['product_ids'], $unfiltered_content ) ) );
        }
        return wpautop( do_shortcode( $paywall_content ) );
    }

    /**
     * @param $frequency
     * @param $date
     *
     * @return int
     */
    protected function get_time_difference( $frequency, $date ) : int {
        $current_time = Woocommerce_Pay_Per_Post_Helper::current_time();
        $diff_method = Woocommerce_Pay_Per_Post_Helper::carbon_diff_method( $frequency );
        return $date->copy()->{$diff_method}( $current_time, CarbonInterface::DIFF_RELATIVE_TO_NOW );
    }

    protected function countdown_refresh() {
        if ( !empty( $this->user_post_info['expiration_date'] ) ) {
            ?>
            <script>
              const countDownDate = new Date('<?php 
            echo $this->user_post_info['expiration_date']->format( Woocommerce_Pay_Per_Post_Helper::date_time_format() );
            ?>').getTime()
              const x = setInterval(function () {
                const now = new Date().getTime()
                const distance = countDownDate - now
                //console.log('remaining', Math.floor(distance / 1000 / 60));
                if (distance < 0) {
                  clearInterval(x)
                  location.reload()
                }
              }, 1000)
            </script>
		<?php 
        }
    }

    protected function shortcode_pageview_status( $template ) {
        ob_start();
        $user_info = $this->user_post_info;
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/shortcode_pageview_status - Showing Pageview Status Warnings' );
        $number_of_allowed_pageviews = get_post_meta( $this->post_id, WC_PPP_SLUG . '_page_view_restriction', true );
        require Woocommerce_Pay_Per_Post_Helper::locate_template( $this->available_templates[$template], '', WC_PPP_PATH . 'public/partials/' );
        return ob_get_clean();
    }

    protected function shortcode_expiration_status( $template ) {
        $user_info = $this->user_post_info;
        if ( isset( $user_info['last_purchase_date'] ) ) {
            ob_start();
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $this->post_id . ' - Woocommerce_Pay_Per_Post_Restrict_Content/shortcode_expiration_status - Showing Expiration Status Warnings' );
            require Woocommerce_Pay_Per_Post_Helper::locate_template( $this->available_templates[$template], '', WC_PPP_PATH . 'public/partials/' );
            return ob_get_clean();
        }
        return false;
    }

    /**
     * @return string[]
     */
    private function available_status_templates() : array {
        return [
            'pageview-status'   => 'pageview-status.php',
            'expiration-status' => 'expiration-status.php',
        ];
    }

}
