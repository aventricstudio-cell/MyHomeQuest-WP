<?php
/**
* WpResidence API Bootstrap File
* 
* This file serves as the main entry point for the WpResidence API system.
* It loads all API-related components and initializes the REST API if enabled.
*
* @package WpResidence
* @subpackage API
* @since 4.0
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
   exit;
}

/**
* Load API Components
* Each component handles a specific aspect of the API functionality
*/

// User Management Components
require_once WPESTATE_PLUGIN_PATH . 'api/user/user.php';             // Core user functionality
require_once WPESTATE_PLUGIN_PATH . 'api/user/capabilities.php';      // User capability definitions
require_once WPESTATE_PLUGIN_PATH . 'api/user/user-role-assignment.php';  // Role assignment handling
require_once WPESTATE_PLUGIN_PATH . 'api/user/capability-management.php';  // Capability management functions

// Updates functions
require_once WPESTATE_PLUGIN_PATH . 'api/updates/updates.php';        // Update system functionality


//Cache functions
require_once WPESTATE_PLUGIN_PATH . 'api/cache/cache.php';           // Caching system implementation

//data query
require_once WPESTATE_PLUGIN_PATH . 'api/data_query/index.php';      // Data query handling


// loading functions

require_once WPESTATE_PLUGIN_PATH . 'api/inc/taxonomies_functions/taxonomy_transients_functions.php';// taxonmies transients functions
require_once WPESTATE_PLUGIN_PATH . 'api/inc/taxonomies_functions/taxonomies_functions.php';// taxonmies functions
require_once WPESTATE_PLUGIN_PATH . 'api/inc/taxonomies_functions/term-custom-fields.php';// term custom fields
require_once WPESTATE_PLUGIN_PATH . 'api/inc/taxonomies_functions/term-description-editor.php';// term description editor
require_once WPESTATE_PLUGIN_PATH . 'api/inc/property_reviews_functions/property_reviews_functions.php'; // reviews functions

require_once WPESTATE_PLUGIN_PATH . 'api/inc/menu_functions/menu_functions.php';// menu functions
require_once WPESTATE_PLUGIN_PATH . 'api/inc/theme_options_functions/theme_options_functions.php'; // theme options functions
require_once WPESTATE_PLUGIN_PATH . 'api/inc/property_price_functions/property_price_functions.php';// property price functions
require_once WPESTATE_PLUGIN_PATH . 'api/inc/property_price_functions/property_price_functions_in_forms.php';// property price forms functions
require_once WPESTATE_PLUGIN_PATH . 'api/inc/print_functions/print_functions.php';// print functions
require_once WPESTATE_PLUGIN_PATH . 'api/inc/property/property_functions.php';// property functions

require_once WPESTATE_PLUGIN_PATH . 'api/inc/convert_functions/convert_functions.php';//convert functions
// require_once WPESTATE_PLUGIN_PATH . 'api/inc/translation_functions/translation_functions.php';//convert functions

//event = cron jobs functions
require_once WPESTATE_PLUGIN_PATH . 'api/inc/events/currency_convert_events.php';// convert currency events
require_once WPESTATE_PLUGIN_PATH . 'api/inc/events/membership_events.php';// convert currency events
require_once WPESTATE_PLUGIN_PATH . 'api/inc/events/saved_search_events.php';// convert currency events
require_once WPESTATE_PLUGIN_PATH . 'api/inc/events/pin_generation_events.php';// convert currency events

//duplicate functions
require_once WPESTATE_PLUGIN_PATH . 'api/inc/duplicate_functions.php';// convert currency events
require_once WPESTATE_PLUGIN_PATH . 'api/inc/template_functions.php';         // template functions

require_once WPESTATE_PLUGIN_PATH . 'api/global-widget-functions/single-post-functions.php';         // template functions
require_once WPESTATE_PLUGIN_PATH . 'api/global-widget-functions/single-agent-functions.php';         // template functions


// only for developers
require_once WPESTATE_PLUGIN_PATH . 'api/developer/main.php';         // Developer tools and utilities


/**
* Initialize REST API if enabled
* 
* Hooks into WordPress init action to ensure proper timing for Redux Framework
* initialization before checking API settings.
*
* @since 4.0
*/


add_action('init', function() {
   
   // Access Redux options
   global $wprentals_admin;
   
   // Check if API is enabled in Redux settings
   // Default to 'no' if setting not found
   $api_enabled = wpresidence_get_option('wp_estate_enable_api', '', 'no');
   
   // Load REST API components if enabled
   if($api_enabled === 'yes') {

      require_once WPESTATE_PLUGIN_PATH . 'api/rest/index.php';
   }
}, 9999); // Priority 15 ensures Redux (priority 1) has loaded first

