<?php
/**
 * WPResidence API Data Query Module
 *
 * Main include file for the WPResidence API data query functionality.
 * Loads all required query builder components.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

// Include taxonomy query builder functions
require_once WPESTATE_PLUGIN_PATH . 'api/data_query/taxonomy_functions.php';

// Include meta query builder functions
require_once WPESTATE_PLUGIN_PATH . 'api/data_query/meta_functions.php';

// Include ordering functions
require_once WPESTATE_PLUGIN_PATH . 'api/data_query/order_functions.php';

// Include main query executor
require_once WPESTATE_PLUGIN_PATH . 'api/data_query/wp_query.php';