<?php 


if( !function_exists('wpestate_set_transient_name_multilang')):

    /**
     * Append active language identifiers to a transient key.
     *
     * Keeps cached values separated per language when WPML and/or
     * WPR Translate are active.
     *
     * @param string $transient_name Base transient key.
     * @return string Language-aware transient key.
     */
    function    wpestate_set_transient_name_multilang( $transient_name){

        // Add WPML language code when available.
        if (defined('ICL_LANGUAGE_CODE')) {
            $transient_name .= '_' . ICL_LANGUAGE_CODE;
        }

        // Add WPR Translate language code (sanitized) when available.
        if ( function_exists( 'wpr_translate_get_current_language' ) ) {
            $lang = wpr_translate_get_current_language();
            if ( is_array( $lang ) && ! empty( $lang['code'] ) ) {
                $transient_name .= '_' . sanitize_key( $lang['code'] );
            }
        }

        return $transient_name;
    }

endif;

/**
 * Optimized template link retrieval function
 * 
 * Acts as a wrapper to either return a dashboard link from cache
 * or fall back to the original single template lookup
 * 
 * @since 4.0.0
 * @param string $template_name The template file name to look up
 * @param int    $bypass       Whether to bypass cache (0 or 1)
 * @return string URL of the template page or empty string if not found
 */
if (!function_exists('wpestate_get_template_link')):
    function wpestate_get_template_link($template_name, $bypass = 0) {
        // Get all dashboard links from cache or fresh
        $dashboard_links = wpestate_get_all_dashboard_template_links();

        // Return cached dashboard link if available
        if (isset($dashboard_links[$template_name])) {
            return $dashboard_links[$template_name];
        }
       
        // Fall back to original function for non-dashboard templates
        return wpestate_get_template_link_original($template_name, $bypass);
    }
endif;



/**
 * Gets all dashboard template links at once and caches them
 * 
 * This function optimizes template link retrieval by:
 * 1. Using static caching for the current request
 * 2. Using transient caching for persistent storage
 * 3. Batch loading all dashboard template links in a single query
 * 
 * @since 4.0.0
 * @return array Array of template paths as keys and their permalink URLs as values
 */


 if (!function_exists('wpestate_get_all_dashboard_template_links')):
    function wpestate_get_all_dashboard_template_links() {
        // Setup transient name with language support
       
        $transient_name = wpestate_set_transient_name_multilang('wpestate_dashboard_links');

        // Try to get from WordPress transient cache
        $dashboard_links = get_transient($transient_name);
 
        if ($dashboard_links !== false && !empty($dashboard_links)) {
      
            return $dashboard_links;
        }
        
        // Define all dashboard templates we need to fetch
        $templates = array(
            'page-templates/aag_search_results.php',
            'page-templates/advanced_search_results.php',
            'page-templates/agency_list.php',
            'page-templates/agents_list.php',
            'page-templates/auser_dashboard_search_result.php',
            'page-templates/blog_list.php',
            'page-templates/compare_listings.php',
            'page-templates/contact_page.php',
            'page-templates/developers_list.php',
            'page-templates/front_property_submit.php',
            'page-templates/gdpr_terms.php',
            'page-templates/page_property_design.php',
            'page-templates/property_list.php',
            'page-templates/property_list_directory.php',
            'page-templates/property_list_half.php',
            'page-templates/splash_page.php',
            'page-templates/terms_conditions.php',
            'page-templates/user_dashboard.php',
            'page-templates/user_dashboard_add.php',
            'page-templates/user_dashboard_add_agent.php',
            'page-templates/user_dashboard_agent_list.php',
            'page-templates/user_dashboard_analytics.php',
            'page-templates/user_dashboard_favorite.php',
            'page-templates/user_dashboard_inbox.php',
            'page-templates/user_dashboard_invoices.php',
            'page-templates/user_dashboard_main.php',
            'page-templates/user_dashboard_profile.php',
            'page-templates/user_dashboard_searches.php',
            'processor.php',
            'stripecharge.php',  


        );
        
        // Get all pages in one query
        $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => $templates,
            'number' => 0
        ));
  
        $dashboard_links = array();
        
        if (!empty($pages)) {
            // Prime the post meta cache
            $page_ids = wp_list_pluck($pages, 'ID');
            update_postmeta_cache($page_ids);
            
            
            // Build links array using cached meta
            foreach ($pages as $page) {
                 $template = get_post_meta($page->ID, '_wp_page_template', true); // Will use cache
     
         
         if ($template && in_array($template, $templates)) {
                    $dashboard_links[$template] = esc_url(get_permalink($page->ID));
                }
            }
            
            // Cache for 24 hours
            set_transient($transient_name, $dashboard_links, DAY_IN_SECONDS);
        }
       
        return $dashboard_links;
    }
endif;

// Hook to clear the transient when a new page is created
if (!function_exists('wpestate_clear_dashboard_links_cache')) :
    function wpestate_clear_dashboard_links_cache($post_id, $post) {
        if ( isset($post->post_type) && $post->post_type === 'page') {
            $transient_name = 'wpestate_dashboard_links';

            // Clear all language-based versions of the transient if WPML is enabled
            if (defined('ICL_LANGUAGE_CODE') && function_exists('icl_get_languages')) {
                $languages = icl_get_languages('skip_missing=0');
                if (is_array($languages)) {
                    foreach ($languages as $lang_code => $lang_data) {
                        delete_transient($transient_name . '_' . $lang_code);
                    }
                }
            }

            
            // Clear all language-based versions of the transient if WPR is enabled

            if (function_exists('wpestate_get_current_language')) {
                    $wpr_languages = wpr_translate_get_languages();
                    if (is_array($wpr_languages) && !empty($wpr_languages)) {
                        foreach ($wpr_languages as $lang) {
                             delete_transient($transient_name . '_' .  $lang['code']);
                        }
                    } 
               
            }





            // Delete the default transient as well
            delete_transient($transient_name);
        }
    }
endif;
add_action('save_post', 'wpestate_clear_dashboard_links_cache', 10, 2);

// Run transient clear when a page is deleted or trashed
add_action('before_delete_post', 'wpestate_clear_dashboard_links_cache', 10, 2);
add_action('wp_trash_post', 'wpestate_clear_dashboard_links_cache', 10, 2);


/**
 * Retrieve the URL for a specific page template.
 *
 * This function checks for a cached version of the URL first. If not found or bypass is set,
 * it queries the database for pages using the specified template and returns the URL of the first match.
 * The result is then cached for future use.
 *
 * @param string $template_name The filename of the page template.
 * @param int $bypass Optional. Set to 1 to bypass the cache. Default 0.
 * @return string The URL of the page using the specified template, or home URL if not found.
 */


 
    function wpestate_get_template_link_original($template_name, $bypass = 0) {
        // Generate a unique transient name, considering WPML if active
  

        $transient_name = wpestate_set_transient_name_multilang('wpestate_get_template_link_');
        

        // Try to get the cached template link
        if(function_exists('wpestate_request_transient_cache')){
            $template_link = ($bypass == 0) ? wpestate_request_transient_cache($transient_name) : false;
        }else{
            $template_link=false;
        }


        // If cache is empty or bypass is set, query for the template
        if ($template_link === false) {
            $args = array(
                'post_type'      => 'page',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'meta_key'       => '_wp_page_template',
                'meta_value'     => $template_name,
                'no_found_rows'  => true,
                'fields'         => 'ids'
            );
    
            $query = new WP_Query($args);
    
            if ($query->have_posts()) {
                $template_link = get_permalink($query->posts[0]);
            } else {
                $template_link = home_url('/');
            }
    
            // Cache the result for 24 hours
            if(function_exists('wpestate_set_transient_cache')){
                wpestate_set_transient_cache($transient_name, $template_link, DAY_IN_SECONDS);
            }
        }
    
        return esc_url($template_link);
    }




if (!function_exists('wpestate_get_template_name')):
 
    function wpestate_get_template_name($postID, $bypass = 0) {
      
          return basename( get_page_template($postID));


    }
endif;





/**
 * Get a sanitized and truncated post title, allowing only <a> tags.
 * Returns the full title if $length = 0.
 *
 * @param int    $post_id   The ID of the post. If 0, uses current post.
 * @param int    $length    The maximum length of the visible title (0 for full title).
 * @return string            The sanitized and truncated (or full) title with allowed links.
 */
function wpresidence_get_sanitized_truncated_title($post_id = 0, $length = 28) {
    // Get the title
    $title = ($post_id === 0) ? get_the_title() : get_the_title($post_id);

    // Allow only <a> tags with safe attributes
    $allowed_tags = array(
        'a' => array(
            'href'   => array(),
            'title'  => array(),
            'target' => array(),
            'rel'    => array(),
        ),
    );

    // Sanitize the title but keep allowed tags
    $safe_title = wp_kses($title, $allowed_tags);

    // If length is 0, return the full sanitized title
    if ($length === 0) {
        return $safe_title;
    }

    // Truncate the title (visible text) while keeping HTML safe
    $truncated_text = mb_substr(wp_strip_all_tags($safe_title), 0, $length);
    $output = esc_html($truncated_text);

    // Add ellipsis if original visible length is longer
    if (strlen(wp_strip_all_tags($safe_title)) > $length) {
        $output .= '...';
    }

    return $output;
}

