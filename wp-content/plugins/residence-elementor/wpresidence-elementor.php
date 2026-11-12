<?php
/**
 * Plugin Name: WPResidence Elementor Widgets
 * Description: Elementor Widgets for WPResidence Theme
 * Plugin URI:  https://themeforest.net/item/wp-residence-real-estate-wordpress-theme/7896392
 * Version:     5.5.0
 * Author:      WpEstate
 * Author URI:  https://wpestate.org/
 * Text Domain: residence-elementor
 * Domain Path: /languages
 */

// Run this as early as possible
add_action('after_setup_theme', 'wpresidence_elementor_check_core_plugin_dependency', 1);

function wpresidence_elementor_check_core_plugin_dependency() {
    $theme = wp_get_theme();
    $theme_version = $theme->get('Version');
    
    // Check if WPResidence Core plugin is active
    if (!defined('WPESTATE_PLUGIN_URL')) {
        // Block frontend
        if (!is_admin()) {
            add_action('template_redirect', function() {
                wp_die(
                    '<h1>Theme Dependency Error</h1><p>The <strong>WPResidence Core</strong> plugin is required. Please install and activate the plugin.</p>',
                    'Dependency Error',
                    array('response' => 500)
                );
            });
        }
        
        // Show admin notice
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p><strong>WPResidence Core plugin is required. Please install and activate the plugin.</strong></p></div>';
        });
        
        return;
    }
    
    // Version check
    if (version_compare($theme_version, '5.1.0', '>=') && !function_exists('wpresidence_core_510')) {
        // Block frontend
        if (!is_admin()) { ?>
           	<div class="error">
                        <p><?php _e('Warning: The WpResidence Elementor Plugin requires WpResidence Core plugin to be installed and activated.', 'wpresidence-elementor'); ?></p>
                </div>
                <?php
                exit();
             
        }
        
        // Show admin notice
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p><strong>WPResidence Core plugin must be version 5.1.0 or greater. Please update the plugin to match the theme version.</strong></p></div>';
        });
    }
}


// for theme update checks
function wpresidence_elementor_510(){
	
}


// Optional: Display an admin notice when the required plugin is missing
function wpresidence_elementor_missing_notice() {
    ?>
    <div class="error">
		<p><?php _e('Warning: The WpResidence Elementor Plugin requires WPResidence Core plugin to be installed and activated.', 'wpresidence-elementor'); ?></>
    </div>
    <?php
}



if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


final class WpResidence_Elementor {

	/**
	 * Plugin Version
	 *
	 * @since 1.2.0
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.2.0
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.2.0
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		// Load translation
		add_action( 'init', array( $this, 'i18n' ) );

		// Init Plugin
		add_action( 'plugins_loaded', array( $this, 'init' ) );
                
                // Enqueue editor styles
                add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_editor_styles' ) );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 * Fired by `init` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( 'residence-elementor' );
	}

        /**
        * Enqueue editor styles
        *
        * @since 1.0.0
        * @access public
        */
       public function enqueue_editor_styles() {
           wp_enqueue_style(
               'wpresidence-elementor-editor',
               plugins_url( '/assets/editor-styles.css', __FILE__ ),
               array(),
               self::VERSION
           );
       }

    
	/**
	 * Initialize the plugin
	 *
	 * Validates that Elementor is already loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed include the plugin class.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return;
		}

		// Once we get here, We have passed all validation checks so we can safely include our plugin
		require_once( 'plugin.php' );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'residence-elementor' ),
			'<strong>' . esc_html__( 'WpResidence Elementor', 'residence-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'residence-elementor' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'residence-elementor' ),
			'<strong>' . esc_html__( 'WpResidence Elementor ', 'residence-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'residence-elementor' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'residence-elementor' ),
			'<strong>' . esc_html__( 'WpResidence Elementor', 'residence-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'residence-elementor' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}
}

// Instantiate WpResidence_Elementor.
new WpResidence_Elementor();
