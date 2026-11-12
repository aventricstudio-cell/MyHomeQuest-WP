<?php

/**
 * Hook into the 'wp' action to set the wpestate_options global variable.
 *
 * The 'wp' action is fired once the WordPress environment is fully loaded,
 * but before any template files are included. This is a good place to set
 * up global variables that need to be available throughout the theme.
 */
add_action('wp', 'wpestate_set_page_options');

/**
 * Set the wpestate_options global variable.
 *
 * This function retrieves the page-specific options using the get_wpestate_options()
 * function and stores it in the global query variables. This makes the options
 * array accessible in all template files using get_query_var().
 */
function wpestate_set_page_options() {
    // Check if the current page is a singular post (post, page, or custom post type)


    $postID='';
    if (is_singular()) {
        // Access the global $post object to get the current post's ID
        global $post;
        $postID=$post->ID;

    }

    // Retrieve the options array for the current post using its ID
    $wpestate_options = wpestate_page_details($postID);

 

    // Store the options array in the global query variables
    set_query_var('wpestate_options', $wpestate_options);
}






if (!function_exists('wpestate_page_details')):

    /**
     * Retrieve the page details including sidebar configuration for a given post.
     *
     * This function determines the sidebar configuration (name and status) based on the post ID
     * and the post type. It returns an array containing CSS classes for content and sidebar
     * columns, as well as the sidebar name.
     *
     * @param int $post_id The ID of the post to retrieve details for.
     * @return array An array containing 'content_class', 'sidebar_class', and 'sidebar_name'.
     */
    function wpestate_page_details($post_id) {

        // Initialize the return array
        $return_array = array();

        // Check if a valid post ID is provided and if the current page is not home or a taxonomy archive
        if ($post_id != '' && !is_home() && !is_tax()) {
            // Get sidebar options from post meta
            $sidebar_name = esc_html(get_post_meta($post_id, 'sidebar_select', true));
            $sidebar_status = esc_html(get_post_meta($post_id, 'sidebar_option', true));
        } else {
            // Get default sidebar options from theme settings
            $sidebar_name = esc_html(wpresidence_get_option('wp_estate_blog_sidebar_name', ''));
            $sidebar_status = esc_html(wpresidence_get_option('wp_estate_blog_sidebar', ''));
        }
        
        
        // Override sidebar options for estate property post type if not set or global
        if ($post_id != '') {
            if ('estate_property' == get_post_type() && ($sidebar_status == '' || $sidebar_status == 'global' )) {
                $sidebar_status = esc_html(wpresidence_get_option('wp_estate_property_sidebar', ''));
                $sidebar_name = esc_html(wpresidence_get_option('wp_estate_property_sidebar_name', ''));
            }
            // Override sidebar options for estate agent post type if not set
            else if ('estate_agent' == get_post_type($post_id) ) {
  
                $sidebar_status = esc_html(get_post_meta($post_id, 'sidebar_option', true));
                $sidebar_name = esc_html(get_post_meta($post_id, 'sidebar_select', true));
            
                if($sidebar_status==''|| $sidebar_status == 'global'){
                    $sidebar_status = esc_html(wpresidence_get_option('wp_estate_agent_sidebar', ''));
                    $sidebar_name = esc_html(wpresidence_get_option('wp_estate_agent_sidebar_name', ''));
                }
                
            }


        }

        // Set default sidebar name and status if not already set
        if ('' == $sidebar_name) {
            $sidebar_name = 'primary-widget-area';
        }
        if ('' == $sidebar_status) {
            $sidebar_status = 'right';
        }

        // Set content and sidebar classes based on sidebar status
       


        if (is_rtl()) {
            // RTL layout
            if ('left' == $sidebar_status) {
                $return_array['content_class'] = 'col-12 col-lg-8 pe-lg-3 order-lg-2';
                $return_array['sidebar_class'] = 'col-12 col-lg-4 ps-lg-3 order-lg-1';
            } else if ($sidebar_status == 'right') {
                $return_array['content_class'] = 'col-12 col-lg-8 ps-lg-3 pe-lg-0 order-lg-1';
                $return_array['sidebar_class'] = 'col-12 col-lg-4 ps-lg-0 pe-lg-3 order-lg-2';
            } else {
                $return_array['content_class'] = 'col-lg-12 p-0 ';
                $return_array['sidebar_class'] = 'none';
            }
        } else {
            // LTR layout (original)
            if ('left' == $sidebar_status) {
                $return_array['content_class'] = 'col-12 col-lg-8 ps-lg-3 order-lg-2';
                $return_array['sidebar_class'] = 'col-12 col-lg-4 pe-lg-3 order-lg-1';
            } else if ($sidebar_status == 'right') {
                $return_array['content_class'] = 'col-12 col-lg-8 ps-lg-0 pe-lg-3 order-lg-1';
                $return_array['sidebar_class'] = 'col-12 col-lg-4 ps-lg-3 pe-lg-0 order-lg-2';
            } else {
                $return_array['content_class'] = 'col-lg-12 p-0 ';
                $return_array['sidebar_class'] = 'none';
            }
        }
        // Store the sidebar name in the return array
        $return_array['sidebar_name'] = $sidebar_name;
        
        // Check if half map should be displayed - if yes - we are not using sidebar
        if(wpestate_half_map_conditions($post_id)){
            $return_array['content_class'] = 'col-lg-12';
            $return_array['sidebar_class'] = 'none';
        }
        

        // Return the array containing page details
        return $return_array;
    }

endif; // end wpestate_page_details
