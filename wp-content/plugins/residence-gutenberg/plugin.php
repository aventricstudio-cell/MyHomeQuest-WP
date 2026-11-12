<?php
/*
 *  Plugin Name: Wpresidence - Gutenberg blocks
 *  Plugin URI:  https://themeforest.net/user/annapx
 *  Description: Adds functionality to WpResidence
 *  Version:     1.50.1
 *  Author:      wpestate
 *  Author URI:  https://wpestate.org
 *  License:     GPL2
 *  Text Domain: wpresidence-gutenberg
 *  Domain Path: /languages
 * 
*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/init.php';
