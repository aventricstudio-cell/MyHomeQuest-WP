<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing


// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'REDUX_PLUGIN_FILE' ) ) {
	define( 'REDUX_PLUGIN_FILE', __FILE__ );
}

// This must be required before vendor/autoload.php so QM can serve its own message about PHP compatibility.
require_once __DIR__ . '/redux-core/inc/classes/class-redux-php.php';

if ( ! Redux_PHP::version_met() ) {
	add_action( 'all_admin_notices', 'Redux_PHP::php_version_nope' );
	return;
}

// Require the main plugin class.
require_once plugin_dir_path( __FILE__ ) . 'class-redux-framework-plugin.php';

// Register hooks that are fired when the plugin is activated and deactivated, respectively.
register_activation_hook( __FILE__, array( 'Redux_Framework_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Redux_Framework_Plugin', 'deactivate' ) );

// Get plugin instance.
Redux_Framework_Plugin::instance();
