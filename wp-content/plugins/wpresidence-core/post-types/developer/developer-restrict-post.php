<?php
/**
 * WP Estate Developer Taxonomy Filters
 *
 * This file implements admin list filtering for the estate_developer custom post type.
 * It creates and registers dropdown filters in the WordPress admin interface for
 * developer taxonomies, allowing quick filtering of developers by:
 * - Categories (property_category_developer)
 * - Actions (property_action_developer)
 * - Cities (property_city_developer)
 *
 * The implementation uses closure-based function factories to create reusable 
 * filter callbacks that are attached to WordPress hooks. This approach allows
 * for clean code reuse while maintaining proper scope isolation.
 *
 * @package WPResidence
 * @subpackage AdminFilters
 * @since 1.0.0
 */

/**
 * Taxonomy Dropdown Filter Factory
 * 
 * Creates a callback function that renders a taxonomy dropdown filter
 * in the WordPress admin post listing. The returned function checks if
 * we're on the correct post type page, then renders a dropdown with
 * all terms from the specified taxonomy.
 *
 * @param string $post_type The post type to add filters to (e.g., 'estate_developer')
 * @param string $taxonomy The taxonomy to create a filter for
 * @return callable A function that renders the filter dropdown when called
 */
$restrict_manage_posts = function($post_type, $taxonomy) {
    return function() use($post_type, $taxonomy) {
        global $typenow; // Current post type in admin
        // Only display the filter on the specified post type's admin page
        if($typenow == $post_type) {
            // Get the currently selected term from URL parameters, if any
            $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';

            if (taxonomy_exists($taxonomy)) {
                // Get taxonomy object to access its labels for display
                $info_taxonomy = get_taxonomy($taxonomy);

                // Create the dropdown using WordPress core function
                wp_dropdown_categories(array(
                    'show_option_all'   => esc_html__("Show All {$info_taxonomy->label}", 'wpresidence-core'),
                    'taxonomy'          => $taxonomy,
                    'name'              => $taxonomy,
                    'orderby'           => 'name',       // Sort terms alphabetically
                    'selected'          => $selected,    // Pre-select the active term
                    'show_count'        => TRUE,         // Show post counts next to terms
                    'hide_empty'        => TRUE,         // Only show terms with posts
                    'hierarchical'      => true          // Display as hierarchical list
                ));
            }
        }
    };
};

/**
 * Query Modifier Factory
 * 
 * Creates a callback function that converts term IDs to term slugs in
 * admin queries. This is necessary because WordPress taxonomy filters
 * use term IDs in URLs, but WP_Query expects term slugs.
 *
 * The returned function checks if we're on the right page with the right
 * post type and taxonomy filter, then converts any numeric term ID to
 * the corresponding slug.
 *
 * @param string $post_type The post type being filtered
 * @param string $taxonomy The taxonomy being used for filtering
 * @return callable A function that modifies the query when needed
 */
$parse_query = function($post_type, $taxonomy) {
    return function($query) use($post_type, $taxonomy) {
        global $pagenow; // Current admin page
        $q_vars = &$query->query_vars;
        // Only modify queries on the edit.php admin page for our specific post type
        // when a numeric taxonomy term has been selected
        if( $pagenow == 'edit.php'
            && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type
            && isset($q_vars[$taxonomy])
            && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0
        ) {
            // Convert the numeric term ID to a slug that WordPress can use in queries
            $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
            $q_vars[$taxonomy] = $term->slug;
        }
    };
};

/**
 * Register category filter for developers
 *
 * Adds a dropdown filter for developer categories and
 * modifies queries to handle the selected filter
 */
add_action('restrict_manage_posts', $restrict_manage_posts('estate_developer', 'property_category_developer') );
add_filter('parse_query', $parse_query('estate_developer', 'property_category_developer') );

/**
 * Register action filter for developers
 *
 * Adds a dropdown filter for developer action types and
 * modifies queries to handle the selected filter
 */
add_action('restrict_manage_posts', $restrict_manage_posts('estate_developer', 'property_action_developer') );
add_filter('parse_query', $parse_query('estate_developer', 'property_action_developer') );

/**
 * Register city filter for developers
 *
 * Adds a dropdown filter for developer cities and
 * modifies queries to handle the selected filter
 */
add_action('restrict_manage_posts', $restrict_manage_posts('estate_developer', 'property_city_developer') );
add_filter('parse_query', $parse_query('estate_developer', 'property_city_developer') );
?>