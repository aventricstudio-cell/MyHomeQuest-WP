<?php
/**
 * WPResidence API Includes
 *
 * This file includes all necessary API-related files for the WPResidence plugin.
 * It loads components for property and invoice management through the REST API.
 *
 * @package WPResidence
 * @subpackage API
 * @since 1.0.0
 */

// utility functions
 require_once WPESTATE_PLUGIN_PATH . 'api/rest/utilities-functions.php'; 


// Property API Files
// Core components for property CRUD operations via the REST API
require_once WPESTATE_PLUGIN_PATH . 'api/rest/properties/properties_routes.php';  // API route definitions
require_once WPESTATE_PLUGIN_PATH . 'api/rest/properties/properties_functions.php'; // Helper functions 
require_once WPESTATE_PLUGIN_PATH . 'api/rest/properties/property_create.php';    // Property creation
require_once WPESTATE_PLUGIN_PATH . 'api/rest/properties/property_update.php';    // Property updating
require_once WPESTATE_PLUGIN_PATH . 'api/rest/properties/property_delete.php';    // Property deletion


// Core components for agent CRUD operations via the REST API
require_once WPESTATE_PLUGIN_PATH . 'api/rest/agents/agent_routes.php';  // API route definitions
require_once WPESTATE_PLUGIN_PATH . 'api/rest/agents/agents_functions.php'; // Helper functions 
require_once WPESTATE_PLUGIN_PATH . 'api/rest/agents/agent_create.php';    // Agent creation
require_once WPESTATE_PLUGIN_PATH . 'api/rest/agents/agent_update.php';    // Agent updating
require_once WPESTATE_PLUGIN_PATH . 'api/rest/agents/agent_delete.php';    // Agent deletion

// Core components for agency CRUD operations via the REST API
require_once WPESTATE_PLUGIN_PATH . 'api/rest/agency/agency_routes.php';  // API route definitions
require_once WPESTATE_PLUGIN_PATH . 'api/rest/agency/agencies_functions.php'; // Helper functions 
require_once WPESTATE_PLUGIN_PATH . 'api/rest/agency/agency_create.php';    // Agency creation
require_once WPESTATE_PLUGIN_PATH . 'api/rest/agency/agency_update.php';    // Agency updating
require_once WPESTATE_PLUGIN_PATH . 'api/rest/agency/agency_delete.php';    // agency deletion



// Core components for developer CRUD operations via the REST API
require_once WPESTATE_PLUGIN_PATH . 'api/rest/developer/developer_routes.php';  // API route definitions
require_once WPESTATE_PLUGIN_PATH . 'api/rest/developer/developers_functions.php'; // Helper functions 
require_once WPESTATE_PLUGIN_PATH . 'api/rest/developer/developer_create.php';    // Developer creation
require_once WPESTATE_PLUGIN_PATH . 'api/rest/developer/developer_update.php';    // Developer updating
require_once WPESTATE_PLUGIN_PATH . 'api/rest/developer/developer_delete.php';    // Developer deletion



// Core components for developer CRUD operations via the REST API
require_once WPESTATE_PLUGIN_PATH . 'api/rest/reviews/reviews_routes.php';  // API route definitions
require_once WPESTATE_PLUGIN_PATH . 'api/rest/reviews/reviews_functions.php'; // Helper functions 
require_once WPESTATE_PLUGIN_PATH . 'api/rest/reviews/review_create.php';    // Developer creation
require_once WPESTATE_PLUGIN_PATH . 'api/rest/reviews/review_update.php';    // Developer updating
require_once WPESTATE_PLUGIN_PATH . 'api/rest/reviews/review_delete.php';    // Developer deletion


// Invoice API Files
// Components for invoice management including creation, retrieval, updating and deletion
require_once WPESTATE_PLUGIN_PATH . 'api/rest/invoices/invoices_routes.php';      // API route definitions
require_once WPESTATE_PLUGIN_PATH . 'api/rest/invoices/invoices_functions.php';   // Helper functions
require_once WPESTATE_PLUGIN_PATH . 'api/rest/invoices/invoices_retrive.php';     // Invoice retrieval
require_once WPESTATE_PLUGIN_PATH . 'api/rest/invoices/invoices_create.php';      // Invoice creation
require_once WPESTATE_PLUGIN_PATH . 'api/rest/invoices/invoices_update.php';      // Invoice updating
require_once WPESTATE_PLUGIN_PATH . 'api/rest/invoices/invoices_delete.php';      // Invoice deletion
require_once WPESTATE_PLUGIN_PATH . 'api/rest/invoices/invoices_customer.php';    // Customer-specific invoice functions



// Messages API Files
// Components for messages management including creation, retrieval, updating and deletion
require_once WPESTATE_PLUGIN_PATH . 'api/rest/messages/message_routes.php';      // API route definitions
require_once WPESTATE_PLUGIN_PATH . 'api/rest/messages/message_create.php';     // message creation
require_once WPESTATE_PLUGIN_PATH . 'api/rest/messages/message_delete.php';      // message delete
require_once WPESTATE_PLUGIN_PATH . 'api/rest/messages/message_functions.php';      // message functions
