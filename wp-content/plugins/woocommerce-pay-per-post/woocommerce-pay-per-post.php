<?php

/**
 * Allows for the sale of a specific post/page in WordPress through WooCommerce.
 *
 * @link                    pramadillo.com
 * @since                   2.0.0
 * @package                 Woocommerce_pay_per_post
 * @wordpress-plugin
 * Plugin Name:             Pay For Post with WooCommerce
 * Plugin URI:              pramadillo.com/plugins/woocommerce-pay-per-post
 * Description:             Allows for the sale of a specific post/page in WordPress through WooCommerce.
 * Version:                 3.2.33
 * Requires Plugins:        woocommerce
 * WC requires at least:    2.6
 * WC tested up to:         10.4.3
 * Elementor tested up to: 3.34.1
 * Elementor Pro tested up to: 3.34.0
 * Author:                  Pramadillo
 * Author URI:              pramadillo.com
 * License:                 GPL-2.0+
 * License URI:             http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:             wc_pay_per_post
 * Domain Path:             /languages
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
const WC_PPP_VERSION = '3.2.33';
const WC_PPP_SLUG = 'wc_pay_per_post';
const WC_PPP_NAME = 'Pay For Post with WooCommerce';
const WC_PPP_TEMPLATE_PATH = 'woocommerce-pay-per-post/';
if ( apply_filters( 'wcppp_is_active', true ) === true ) {
    define( 'WC_PPP_PATH', plugin_dir_path( __FILE__ ) );
    define( 'WC_PPP_URL', plugin_dir_url( __FILE__ ) );
    define( 'WC_PPP_BASENAME', plugin_basename( __FILE__ ) );
    if ( function_exists( 'wcppp_freemius' ) ) {
        wcppp_freemius()->set_basename( false, __FILE__ );
    } else {
        if ( !function_exists( 'wcppp_freemius' ) ) {
            function wcppp_freemius() {
                global $wcppp_freemius;
                if ( !isset( $wcppp_freemius ) ) {
                    // Activate multisite network integration.
                    if ( !defined( 'WP_FS__PRODUCT_1664_MULTISITE' ) ) {
                        define( 'WP_FS__PRODUCT_1664_MULTISITE', true );
                    }
                    // Include Freemius SDK.
                    require_once plugin_dir_path( __FILE__ ) . 'freemius/wordpress-sdk/start.php';
                    try {
                        $wcppp_freemius = fs_dynamic_init( [
                            'id'             => '1664',
                            'slug'           => 'woocommerce-pay-per-post',
                            'type'           => 'plugin',
                            'public_key'     => 'pk_3421f16894101749f184e4e1535da',
                            'is_premium'     => false,
                            'premium_suffix' => 'Premium',
                            'has_addons'     => false,
                            'has_paid_plans' => true,
                            'trial'          => [
                                'days'               => 7,
                                'is_require_payment' => true,
                            ],
                            'menu'           => [
                                'slug'       => 'wc_pay_per_post',
                                'first-path' => 'admin.php?page=wc_pay_per_post-whats-new',
                            ],
                            'is_live'        => true,
                        ] );
                    } catch ( Freemius_Exception $e ) {
                        die( esc_html( $e->getMessage() ) );
                    }
                }
                return $wcppp_freemius;
            }

        }
        require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-pay-per-post.php';
        require plugin_dir_path( __FILE__ ) . 'vendor-prefixed/autoload.php';
        function activate_woocommerce_pay_per_post() {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-pay-per-post-activator.php';
            Woocommerce_Pay_Per_Post_Activator::activate( WC_PPP_VERSION );
        }

        function deactivate_woocommerce_pay_per_post() {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-pay-per-post-deactivator.php';
            Woocommerce_Pay_Per_Post_Deactivator::deactivate();
        }

        register_activation_hook( __FILE__, 'activate_woocommerce_pay_per_post' );
        register_deactivation_hook( __FILE__, 'deactivate_woocommerce_pay_per_post' );
        function run_woocommerce_pay_per_post() {
            wcppp_freemius();
            do_action( 'wcppp_freemius_loaded' );
            wcppp_freemius()->add_filter( 'plugin_icon', function () {
                return dirname( __FILE__ ) . '/admin/img/icon.png';
            } );
            // Register uninstall cleanup via Freemius hook (not WordPress uninstall.php)
            wcppp_freemius()->add_action( 'after_uninstall', function () {
                require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-pay-per-post-deactivator.php';
                Woocommerce_Pay_Per_Post_Deactivator::uninstall();
            } );
            $plugin = new Woocommerce_Pay_Per_Post();
            $plugin->run();
        }

        run_woocommerce_pay_per_post();
    }
    add_action( 'before_woocommerce_init', function () {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    } );
}