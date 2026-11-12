<?php 
/**
 * WordPress Admin Action Handler for Cache Purging
 *
 * This function handles the admin-post action for purging the WP Estate cache.
 * It verifies the nonce for security, calls the cache deletion function,
 * and redirects back to the referring page.
 *
 * @package WP Estate
 * @subpackage Cache
 * @return void
 */
add_action('admin_post_wpestate_purge_cache', 'wpestate_purge_cache');
if (!function_exists('wpestate_purge_cache')):
    function wpestate_purge_cache() {
        // Check if this is a valid action request with nonce
        if (isset($_GET['action'], $_GET['_wpnonce'])) {
            // Verify the security nonce
            if (!wp_verify_nonce($_GET['_wpnonce'], 'theme_purge_cache')) {
                // If nonce verification fails, terminate with error
                wp_nonce_ays('');
            }
            
            // Call the function that handles the actual cache deletion
            wpestate_delete_cache();
            
            // Redirect back to the referring page
            wp_redirect(wp_get_referer());
            
            // Terminate execution
            die();
        }
    }
endif;

/**
 * WordPress Admin Sidebar Cache Purging Handler
 *
 * This function provides an alternative entry point for cache purging,
 * specifically designed for sidebar or admin bar actions.
 * Unlike the main function, it doesn't verify the nonce (which could be a security concern).
 * It purges the cache and redirects to the admin dashboard.
 *
 * @package WP Estate
 * @subpackage Cache
 * @return void
 */
if (!function_exists('wpestate_purge_cache_sidebar')):
    function wpestate_purge_cache_sidebar() {
        // Check if this is a valid action request
        if (isset($_GET['action'], $_GET['_wpnonce'])) {
            // Call the function that handles the actual cache deletion
            wpestate_delete_cache();
            
            // Redirect to the admin dashboard
            wp_redirect(esc_url(admin_url()));
            
            // Terminate execution
            die();
        }
    }
endif;


/**
 * Main Cache Deletion Function for WP Estate
 *
 * This comprehensive function handles the deletion of various transient caches
 * used throughout the WP Estate theme/plugin. It targets three specific types of caches:
 * 1. Standard WP Estate transients (prefixed with 'wpestate')
 * 2. API cache transients (prefixed with 'wpestate_api_')
 * 3. WordPress object cache (if an external object cache is in use)
 * 
 * Additionally, it clears the Envato purchase code demos cache.
 *
 * @package WP Estate
 * @subpackage Cache
 * @return void
 */
if (!function_exists('wpestate_delete_cache')):
    function wpestate_delete_cache() {
        global $wpdb;
        
        // SQL query template to find transients in the options table
        $sql = "SELECT `option_name` AS `name`, `option_value` AS `value`
            FROM  $wpdb->options
            WHERE `option_name` LIKE %s
            ORDER BY `option_name`";
       
        // SECTION 1: Delete standard wpestate transients
        $wild = '%';
        $find = 'transient_';
        $like = $wild . $wpdb->esc_like($find) . $wild;
        $results = $wpdb->get_results($wpdb->prepare($sql, $like));
        $transients = array();
        foreach ($results as $result) {
            // Target only WP Estate transients
            if (0 === strpos($result->name, '_transient_wpestate')) {
                // Extract the actual transient name without the '_transient_' prefix
                $transient_name = str_replace('_transient_', '', $result->name);
                delete_transient($transient_name);
            }
        }
       
        // SECTION 2: Delete API cache transients (wpestate_api_{post_type}_{post_id}_cache)
        $wild = '%';
        $find = 'transient_wpestate_api_';
        $like = $wild . $wpdb->esc_like($find) . $wild;
        $api_results = $wpdb->get_results($wpdb->prepare($sql, $like));
        foreach ($api_results as $result) {
            // Target only WP Estate API transients
            if (0 === strpos($result->name, '_transient_wpestate_api_')) {
                // Extract the actual transient name without the '_transient_' prefix
                $transient_name = str_replace('_transient_', '', $result->name);
                delete_transient($transient_name);
            }
        }
       
        // SECTION 3: If object cache is in use, clear that as well
        if (wp_using_ext_object_cache()) {
            wp_cache_flush();
        }
       
        // SECTION 4: Delete specific Envato demos cache
        delete_transient('envato_purchase_code_7896392_demos');
    }
endif;

/**
 * Template Link Cache Deletion Function
 *
 * This specialized function clears cached template links used by WP Estate.
 * Template links are URLs for various template pages used throughout the theme,
 * and they're stored as transients for performance.
 * 
 * This function ensures template links are refreshed when needed, for example
 * after changing permalinks or modifying template pages.
 *
 * @package WP Estate
 * @subpackage Cache
 * @return void
 */
if (!function_exists('wpestate_delete_cache_for_links')):
    function wpestate_delete_cache_for_links() {
        global $wpdb;
        
        // SQL query to find template link transients in the options table
        $sql = "SELECT `option_name` AS `name`, `option_value` AS `value`
            FROM  $wpdb->options
            WHERE `option_name` LIKE %s
            ORDER BY `option_name`";
            
        // Target the specific template link transient pattern
        $wild = '%';
        $find = 'wpestate_get_template_link_';
        $like = $wild . $wpdb->esc_like($find) . $wild;
        $results = $wpdb->get_results($wpdb->prepare($sql, $like));
        
        foreach ($results as $result) {
            // Verify this is a template link transient
            if (0 === strpos($result->name, '_transient_wpestate_get_template_link_')) {
                // Extract the actual transient name without the '_transient_' prefix
                $transient_name = str_replace('_transient_', '', $result->name);
                delete_transient($transient_name);
            }
        }
    }
endif;

/**
 * WordPress Cache Refresh Setup
 *
 * This function initializes cache refresh hooks during the WordPress admin initialization.
 * It registers the template link cache deletion function to be triggered when a post is moved to trash.
 * This ensures that any cached links are refreshed when content changes occur to maintain consistency
 * across the site.
 *
 * @package WP Estate
 * @subpackage Cache
 * @return void
 */
add_action('admin_init', 'wpestate_cache_refresh');

/**
 * Setup Cache Refresh Hooks
 * 
 * This function is called during admin initialization and sets up WordPress hooks
 * to automatically clear cache when certain actions occur in the admin area.
 * Currently, it hooks into the post trashing event to clear template link caches,
 * which prevents stale links from being served after content changes.
 *
 * @package WP Estate
 * @subpackage Cache
 * @return void
 */
function wpestate_cache_refresh() {
    // When a post is moved to trash, clear the template link cache
    // This ensures any links related to that post are regenerated when needed
    add_action('wp_trash_post', 'wpestate_delete_cache_for_links', 10);
}

/**
 * Reset transient cache when estate-related post types are saved, updated, or deleted
 *
 * This function clears the agent array cache stored as a transient whenever
 * relevant real estate post types (agents, agencies, developers) are modified.
 * The cache clearing ensures that any changes to these entities are immediately
 * reflected in components that use the cached data.
 *
 * @package WP Estate
 * @subpackage Cache
 * 
 * @param int $post_id The ID of the post being saved/deleted
 * @param WP_Post|null $post Optional. Post object if available (provided on save_post, not on before_delete_post)
 * @return void
 */
function wpestate_clear_agent_array_cache($post_id, $post = null) {
    // Define which post types should trigger cache clearing
    $relevant_post_types = array('estate_agent', 'estate_agency', 'estate_developer');
    
    // Determine the post type - use different methods depending on whether post object is provided
    $post_type = $post instanceof WP_Post ? $post->post_type : get_post_type($post_id);
    
    // Early return if this post type doesn't need cache clearing
    if (!in_array($post_type, $relevant_post_types)) {
        return;
    }
    
    // Clear the transient cache used by Visual Composer (WPBakery Page Builder) for agent data
    delete_transient('wpestate_js_composer_agent_array');
}

// Hook function to post save events (both creation and update)
add_action('save_post', 'wpestate_clear_agent_array_cache', 10, 2);

// Hook function to post deletion events
add_action('before_delete_post', 'wpestate_clear_agent_array_cache');