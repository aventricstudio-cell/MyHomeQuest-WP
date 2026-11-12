<?php
/**
 * WP Estate Agent Taxonomy Filters
 *
 * This file implements admin list filtering for the estate_agent custom post type.
 * It adds dropdown filters in the WordPress admin for agent taxonomies:
 * - Categories (property_category_agent)
 * - Actions (property_action_category_agent)
 * - Cities (property_city_agent)
 *
 * The implementation uses closures to create reusable filter functions
 * that are then attached to the appropriate WordPress hooks.
 *
 * @package WPResidence
 * @subpackage AdminFilters
 * @since 1.0.0
 */

/**
 * Create a reusable function for adding taxonomy filters to admin list pages
 *
 * This closure returns a callback function that creates a dropdown filter
 * for the specified taxonomy on the specified post type's admin list page.
 * The returned function checks if we're on the correct post type page,
 * then renders a category dropdown filter with proper labeling.
 *
 * @param string $post_type The post type to add filters to (e.g., 'estate_agent')
 * @param string $taxonomy The taxonomy to create a filter for
 * @return callable A function that renders the dropdown when called by WordPress
 */
$restrict_manage_posts = function($post_type, $taxonomy) {
    return function() use($post_type, $taxonomy) {
        global $typenow;
        // Only show the filter on the specified post type admin page
        if($typenow == $post_type) {
            // Get the currently selected term if any
            $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';

            if (taxonomy_exists($taxonomy)) {
                // Get taxonomy object to access its labels
                $info_taxonomy = get_taxonomy($taxonomy);

                // Create the dropdown using WordPress core function
                wp_dropdown_categories(array(
                    'show_option_all'   => esc_html__("Show All {$info_taxonomy->label}", 'wpresidence-core'),
                    'taxonomy'          => $taxonomy,
                    'name'              => $taxonomy,
                    'orderby'           => 'name',
                    'selected'          => $selected,
                    'show_count'        => TRUE,    // Show post counts next to terms
                    'hide_empty'        => TRUE,    // Only show terms that have posts
                    'hierarchical'      => true     // Show terms in hierarchical format
                ));
            }
        }
    };
};

/**
 * Create a reusable function for converting term IDs to slugs in queries
 *
 * This closure returns a callback function that modifies WP_Query parameters
 * when a taxonomy filter is applied. It converts term IDs from the dropdown
 * into term slugs that WordPress query can use.
 *
 * @param string $post_type The post type being filtered
 * @param string $taxonomy The taxonomy being used for filtering
 * @return callable A function that modifies the query when called by WordPress
 */
$parse_query = function($post_type, $taxonomy) {
    return function($query) use($post_type, $taxonomy) {
        global $pagenow;
        $q_vars = &$query->query_vars;
        // Only modify queries on the edit.php admin page for our specific post type
        // when a numeric taxonomy term has been selected
        if( $pagenow == 'edit.php'
            && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type
            && isset($q_vars[$taxonomy])
            && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0
        ) {
            // Convert the term ID to a slug for the query
            $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
            $q_vars[$taxonomy] = $term->slug;
        }
    };
};

/**
 * Hook the category filter to the admin page
 * This adds a dropdown filter for agent categories
 */
add_action('restrict_manage_posts', $restrict_manage_posts('estate_agent', 'property_category_agent') );
add_filter('parse_query', $parse_query('estate_agent', 'property_category_agent') );

/**
 * Hook the action category filter to the admin page
 * This adds a dropdown filter for agent action types
 */
add_action('restrict_manage_posts', $restrict_manage_posts('estate_agent', 'property_action_category_agent') );
add_filter('parse_query', $parse_query('estate_agent', 'property_action_category_agent') );

/**
 * Hook the city filter to the admin page
 * This adds a dropdown filter for agent cities
 */
add_action('restrict_manage_posts', $restrict_manage_posts('estate_agent', 'property_city_agent') );
add_filter('parse_query', $parse_query('estate_agent', 'property_city_agent') );