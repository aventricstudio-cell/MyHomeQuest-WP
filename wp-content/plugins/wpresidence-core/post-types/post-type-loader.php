<?php 
/**
 * Combined Post Types Loader
 * 
 * This file serves as the central loader for all custom post types and related functionality
 * used within the WP Estate plugin. It includes files for properties, agents, agencies,
 * developers, invoices, memberships, messages, and searches.
 * 
 * @package WPEstate
 * @subpackage PostTypes
 * @version 1.0
 * @author WPEstate Team
 * @copyright Copyright (c) WPEstate
 * @license GPL2+
 */

/**
 * Security check to prevent direct file access
 * This prevents users from accessing the file directly, enhancing security
 * by only allowing WordPress to include this file
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Property Custom Post Type Files
 * 
 * These files handle everything related to the Property post type, including:
 * - Registration of the post type
 * - Multiple taxonomies (categories, actions, locations, features, etc.)
 * - Custom metaboxes for property details
 * - Helper functions specific to properties
 * - Admin customizations like custom columns
 */
require_once WPESTATE_PLUGIN_PATH . 'post-types/property/property-post-type.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/property/property-category-taxonomy.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/property/property-action-category-taxonomy.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/property/property-city-taxonomy.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/property/property-county-state-taxonomy.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/property/property-area-taxonomy.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/property/property-features-taxonomy.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/property/property-status-taxonomy.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/property/property-metaboxes.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/property/property-help-functions.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/property/property-manage-posts.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/property/property-admin-columns.php';


/**
 * Agent Custom Post Type Files
 * 
 * These files handle everything related to the Agent post type, including:
 * - Registration of the post type
 * - Admin column customizations
 * - Custom metaboxes for agent details
 * - Post restrictions and permissions
 */
require_once WPESTATE_PLUGIN_PATH . 'post-types/agents/agent-post-type.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/agents/agent-admin-columns.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/agents/agent-metaboxes.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/agents/agent-restrict-post.php';


/**
 * Agency Custom Post Type Files
 * 
 * These files handle everything related to the Agency post type, including:
 * - Registration of the post type
 * - Admin column customizations
 * - Custom metaboxes for agency details
 * - Post restrictions and permissions
 */
require_once WPESTATE_PLUGIN_PATH . 'post-types/agency/agency-post-type.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/agency/agency-admin-columns.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/agency/agency-metaboxes.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/agency/agency-restrict-posts.php';


/**
 * Developer Custom Post Type Files
 * 
 * These files handle everything related to the Developer post type, including:
 * - Registration of the post type
 * - Admin column customizations
 * - Custom metaboxes for developer details
 * - Post restrictions and permissions
 */
require_once WPESTATE_PLUGIN_PATH . 'post-types/developer/developer-post-type.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/developer/developer-admin-columns.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/developer/developer-meta-boxes.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/developer/developer-restrict-post.php';


/**
 * Invoices Custom Post Type Files
 * 
 * These files handle everything related to the Invoices post type, including:
 * - Registration of the post type
 * - Helper functions for invoice generation and processing
 * - Admin column customizations
 * - Custom metaboxes for invoice details
 */
require_once WPESTATE_PLUGIN_PATH . 'post-types/invoices/invoices-post-type.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/invoices/invoice-help-functions.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/invoices/invoices-admin-columns.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/invoices/invoices-meta-boxes.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/invoices/invoices_activate_purchase_functions.php';

/**
 * Membership Custom Post Type Files
 * 
 * These files handle everything related to the Membership post type, including:
 * - Registration of the post type
 * - Action handlers for membership-related operations
 * - Downgrade process handlers
 * - Helper functions for membership management
 * - Custom metaboxes for membership details
 * - Upgrade process handlers
 * - User information management related to memberships
 */
require_once WPESTATE_PLUGIN_PATH . 'post-types/membership/membership-post-type.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/membership/membership-actions-functions.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/membership/membership-downgrade-actions.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/membership/membership-help-functions.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/membership/membership-meta-boxes.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/membership/membership-upgrade-actions.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/membership/membership-user-info-functions.php';


/**
 * Messages Custom Post Type Files
 * 
 * These files handle everything related to the Messages post type, including:
 * - Registration of the post type
 * - Action handlers for message-related operations
 * - Admin column customizations
 * - Custom metaboxes for message details
 */
require_once WPESTATE_PLUGIN_PATH . 'post-types/messages/messages-post-type.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/messages/messages-actions.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/messages/messages-admin-columns.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/messages/messages-metaboxes.php';


/**
 * Searches Custom Post Type Files
 * 
 * These files handle everything related to the Searches post type, including:
 * - Registration of the post type
 * - Admin column customizations for saved searches
 * - Custom metaboxes for search details
 */
require_once WPESTATE_PLUGIN_PATH . 'post-types/searches/searches-post-type.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/searches/searches-add-columns.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/searches/searches-metaboxes.php';

/**
 * Review Custom Post Type Files
 * 
 * These files handle everything related to the Agent post type, including:
 * - Registration of the post type
 * - Admin column customizations
 * - Custom metaboxes for agent details
 * - Post restrictions and permissions
 */
require_once WPESTATE_PLUGIN_PATH . 'post-types/reviews/reviews-post-type.php';
require_once WPESTATE_PLUGIN_PATH . 'post-types/reviews/reviews-metaboxes.php';