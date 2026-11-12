<?php
/** MILLDONE
 * Custom AJAX Handler for WpResidence Theme
 * src: ajax_handler.php
 * This file mimics the WordPress admin-ajax functionality to handle custom AJAX requests
 * for the WpResidence theme. It processes specific actions related to property listings
 * and other theme-specific features.
 *
 * @package WpResidence
 * @subpackage AJAX
 * @since WpResidence 1.0
 */

// Define AJAX constant
define('DOING_AJAX', true);

// Verify AJAX request
if (!isset($_POST['action'])) {
    die('-1');
}

// Load WordPress environment
$wp_load_path = preg_replace('/wp-content(?!.*wp-content).*/', '', __DIR__) . 'wp-load.php';
require_once($wp_load_path);

// Set headers
header('Content-Type: text/html');
send_nosniff_header();
header('Cache-Control: no-cache');
header('Pragma: no-cache');

// Sanitize and validate the action
$action = isset($_POST['action']) ? sanitize_text_field($_POST['action']) : '';

// List of allowed actions
$allowed_actions = array(
    'wpestate_property_modal_listing_details',
    'wpestate_property_modal_listing_details_second',
    'wpestate_custom_ondemand_pin_load',
    'wpestate_load_recent_items_sh',
    'wpestate_ajax_filter_listings',
    'wpestate_custom_adv_ajax_filter_listings_search',
    'wpestate_classic_ondemand_pin_load_type2_tabs',
    'wpestate_load_blog_list_widget_wrapper',
);

// Process the action if it's allowed
if(in_array($action, $allowed_actions)){
    if(is_user_logged_in())
        do_action('wpestate_ajax_handler'.'_'.$action);
    else
        do_action('wpestate_ajax_handler_nopriv'.'_'.$action);
}
else{
    die('-1');
} 