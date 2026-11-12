<?php

if (!function_exists('wpestate_float_search_placement_new')):
    /**
     * Determines whether a floating search form should be displayed on the current page
     * 
     * This function evaluates various conditions to decide if and where a floating search form
     * should appear, based on global settings and page-specific overrides.
     * 
     * @global WP_Post $post WordPress post object
     * @return boolean Returns true if floating search should be displayed, false otherwise
     */
    function wpestate_float_search_placement_new() {
        global $post;

        // Check if we're on a singular agency or develoer page - don't show floating search
        if (isset($post) && in_array($post->post_type, ['estate_developer', 'estate_agency'])) {
            return false;
        }
        
        // Initialize post ID
        $post_id = '';
        if (isset($post->ID)) {
            $post_id = $post->ID;
        }
        
        // Variable to store floating form position (unused in current implementation)
        $float_form_top_local = '';
        
        // Retrieve the global floating search form setting for this post
        $float_search_form = wpestate_retrive_float_search_placement($post_id);
        
        // Initialize local search type override
        // 0 = use global settings
        // 1 = disable floating search (implied)
        // 2 = force enable floating search
        $search_float_type = 0;
        
        // Get page-specific override setting if we're on a post/page
        if (isset($post->ID)) {
            $search_float_type = intval(get_post_meta($post->ID, 'use_float_search_form_local_set', true));
        }
        
        // Special handling for non-singular pages (archives, search results, etc.)
        // Override floating search form setting with global option
        if (is_404() || is_category() || is_tax() || is_archive() || is_search()) {
            $float_search_form = esc_html(wpresidence_get_option('wp_estate_use_float_search_form', ''));
        }
        
        // Check if we're on a half map page - if so, don't show floating search
        if (wpestate_half_map_conditions($post_id)) {
            return false;
        }
        
        // Determine if floating search should be displayed based on settings:
        
        // Case 1: Using global settings ($search_float_type = 0) and floating search is enabled globally
        if ($search_float_type == 0 && $float_search_form == 'yes') {
            return true;
        } 
        // Case 2: Local override to force enable floating search ($search_float_type = 2)
        else if ($search_float_type == 2) {
            return true;
        } 
        // All other cases: don't show floating search
        // - When global settings are used ($search_float_type = 0) but floating search is disabled
        // - When local override disables floating search ($search_float_type = 1)
        else {
            return false;
        }
    }
endif;




/**
 * Determines if half map conditions are met for property listings
 *
 * This function checks various conditions to decide whether a half map
 * should be displayed on property listing pages in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage PropertyListings
 * @since 1.0.0
 *
 * @param int $pos_id The ID of the current post/page being checked
 * @return bool True if half map conditions are met, false otherwise
 */

if (!function_exists('wpestate_half_map_conditions')):
    function wpestate_half_map_conditions($pos_id) {
        // Get the page template for the current post/page



        $page_template = get_post_meta($pos_id, '_wp_page_template', true);
       
        // Check if it's a property list half page template
        if (!is_category() && !is_tax() && $page_template == 'page-templates/property_list_half.php') {
            return true;
        } 
        // Check for specific taxonomy conditions 
        elseif (is_tax() ) {
       
            $taxonomy = get_query_var('taxonomy');
            
            // List of taxonomies that should not display the half map
            $excluded_taxonomies = array(
                'property_category_agent', 
                'property_action_category_agent', 
                'property_city_agent',
                'property_area_agent', 
                'property_county_state_agent', 
                'category_agency',
                'action_category_agency', 
                'city_agency', 
                'area_agency', 
                'county_state_agency',
                'property_category_developer', 
                'property_action_developer', 
                'property_city_developer',
                'property_area_developer', 
                'property_county_state_developer'
            );

            // Return false if the current taxonomy is in the excluded list
            if (in_array($taxonomy, $excluded_taxonomies)) {
                return false;
            }
            
        

            if(  wpresidence_get_option('wp_estate_property_list_type', '')==2){
                return true;
            }
        
        } 
        // Check for advanced search results page
        elseif ($page_template == 'page-templates/advanced_search_results.php' && 
                wpresidence_get_option('wp_estate_property_list_type_adv', '') == 2) {
            return true;
        } 
        // Default case: do not display half map
        else {
            return false;
        }
    }
endif;




/**
 * Determines if property modal should be used
 *
 * This function checks theme options and current page conditions to decide
 * whether a property modal should be used for displaying property details
 * in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage PropertyDisplay
 * @since 1.0.0
 *
 * @return bool True if property modal should be used, false otherwise
 */

if (!function_exists('wpestate_is_property_modal')):
    function wpestate_is_property_modal() {
        global $post;

        // Check if property modal is enabled in theme options
        $use_modal = wpresidence_get_option('wp_estate_use_property_modal', '');
        
        if ($use_modal !== 'yes') {
            return false;
        }

        // Get the current page template
        $page_template = '';
        if (isset($post->ID)) {
            $page_template = get_post_meta($post->ID, '_wp_page_template', true);
        }

        // List of page templates that should use the modal
        $modal_templates = array(
            'page-templates/property_list.php',
            'page-templates/property_list_directory.php',
            'page-templates/property_list_half.php',
            'page-templates/advanced_search_results.php'
        );

        // Check if current page is a taxonomy, archive, or uses a modal template
        if (is_tax() || is_archive() || in_array($page_template, $modal_templates)) {
            return true;
        }

        return false;
    }
endif;




/**
 * Checks if the current page belongs to the user dashboard
 *
 * This function determines whether the current page is part of the user dashboard
 * by checking its page template against a list of known dashboard page templates
 * in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage UserDashboard
 * @since 1.0.0
 *
 * @return bool True if the current page is a dashboard page, false otherwise
 */

if (!function_exists('wpestate_is_user_dashboard')):
    function wpestate_is_user_dashboard() {
        global $post;

        // Get the current page template
        $page_template = '';
        if (isset($post->ID)) {
            $page_template = get_post_meta($post->ID, '_wp_page_template', true);
        }

        // List of page templates that belong to the user dashboard
        $dashboard_pages = array(
            'page-templates/user_dashboard_main.php',
            'page-templates/user_dashboard.php',
            'page-templates/user_dashboard_add.php',
            'page-templates/user_dashboard_profile.php',
            'page-templates/user_dashboard_favorite.php',
            'page-templates/user_dashboard_analytics.php',
            'page-templates/user_dashboard_searches.php',
            'user_dashboard_search_result.php',
            'page-templates/user_dashboard_invoices.php',
            'page-templates/user_dashboard_add_agent.php',
            'page-templates/user_dashboard_agent_list.php',
            'page-templates/user_dashboard_inbox.php',
            'wpestate-crm-dashboard.php',
            'wpestate-crm-dashboard_contacts.php',
            'wpestate-crm-dashboard_leads.php',
        );

        // Check if the current page template is in the list of dashboard pages
        return in_array($page_template, $dashboard_pages);
    }
endif;



/**
 * Checks if the map marker should be shown for a property
 *
 * This function determines whether a map marker should be displayed for a property
 * based on individual property settings and global theme options in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage PropertyMap
 * @since 1.0.0
 *
 * @return bool True if the map marker should be shown, false otherwise
 */

if (!function_exists('wpestate_check_show_map_marker')):
    function wpestate_check_show_map_marker() {
        global $post;

        // Check individual property setting
        $property_hide_map_marker = 0;
        if (isset($post->ID)) {
            $property_hide_map_marker = intval(get_post_meta($post->ID, 'property_hide_map_marker', true));
        }

        // If property is set to hide marker, return false
        if ($property_hide_map_marker === 1) {
            return false;
        }

        // Check global theme setting
        $hide_marker_pin = wpresidence_get_option('wp_estate_hide_marker_pin', '');

        // If global setting is to hide pins and not on a single property page, return true
        if ($hide_marker_pin === 'yes' && !is_singular('estate_property')) {
            return true;
        }

        // Default: show marker
        return true;
    }
endif;
