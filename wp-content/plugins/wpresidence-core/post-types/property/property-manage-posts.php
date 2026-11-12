<?php
/**
 * Property Post Management Filters
 *
 * Handles the integration of taxonomy filters in the WordPress admin property list view.
 * Provides dropdown filters for:
 * - Property Categories
 * - Property Action Categories (e.g. for rent/sale)
 * - Property Cities
 *
 * Uses closure functions to generate reusable filter functionality for different taxonomies.
 *
 * @package WpResidence
 * @subpackage Admin
 * @since 1.0
 */

/**
 * Creates a closure that generates taxonomy dropdown filters
 * 
 * Returns a function that adds a category dropdown filter to the posts list table.
 * The returned function checks the current post type and adds appropriate dropdown
 * with hierarchical taxonomy terms.
 *
 * @param string $post_type The post type to filter (e.g. 'estate_property')
 * @param string $taxonomy The taxonomy to create a filter for
 * @return Closure Function that renders the dropdown filter
 */
$restrict_manage_posts = function ($post_type, $taxonomy) {
    // Return closure that has access to $post_type and $taxonomy
    return function () use ($post_type, $taxonomy) {
        global $typenow;
        
        // Only add filter to specified post type
        if ($typenow == $post_type) {
            // Get currently selected term if any
            $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';

            if (taxonomy_exists($taxonomy)) {
                $info_taxonomy = get_taxonomy($taxonomy);

                // Create dropdown using WordPress core function
                wp_dropdown_categories(array(
                    'show_option_all' => esc_html__("Show All {$info_taxonomy->label}", 'wpresidence-core'),
                    'taxonomy' => $taxonomy,
                    'name' => $taxonomy,
                    'orderby' => 'name',
                    'selected' => $selected,
                    'show_count' => TRUE,
                    'hide_empty' => TRUE,
                    'hierarchical' => true
                ));
            }
        }
    };
};

/**
 * Creates a closure that handles taxonomy term query parsing
 * 
 * Returns a function that converts taxonomy term IDs to slugs in the query.
 * This is necessary because WordPress category dropdowns use term IDs but
 * WP_Query expects term slugs.
 *
 * @param string $post_type The post type being filtered
 * @param string $taxonomy The taxonomy being used to filter
 * @return Closure Function that modifies the query vars
 */
$parse_query = function ($post_type, $taxonomy) {
    // Return closure with access to $post_type and $taxonomy
    return function ($query) use ($post_type, $taxonomy) {
        global $pagenow;
        $q_vars = &$query->query_vars;
        
        // Check if we're on the right screen and have taxonomy terms to convert
        if ($pagenow == 'edit.php' && 
            isset($q_vars['post_type']) && 
            $q_vars['post_type'] == $post_type && 
            isset($q_vars[$taxonomy]) && 
            is_numeric($q_vars[$taxonomy]) && 
            $q_vars[$taxonomy] != 0
        ) {
            // Convert term ID to slug
            $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
            $q_vars[$taxonomy] = $term->slug;
        }
    };
};

// Add filters for property categories
add_action('restrict_manage_posts', $restrict_manage_posts('estate_property', 'property_category'));
add_filter('parse_query', $parse_query('estate_property', 'property_category'));

// Add filters for property action categories (rent/sale)
add_action('restrict_manage_posts', $restrict_manage_posts('estate_property', 'property_action_category'));
add_filter('parse_query', $parse_query('estate_property', 'property_action_category'));

// Add filters for property cities
add_action('restrict_manage_posts', $restrict_manage_posts('estate_property', 'property_city'));
add_filter('parse_query', $parse_query('estate_property', 'property_city'));

// Add text filter for Listing ID
add_action('restrict_manage_posts', function () {
    global $typenow;

    if ($typenow === 'estate_property') {
        $listing_id = isset($_GET['property_listing_id']) ? esc_attr($_GET['property_listing_id']) : '';
        printf(
            '<input type="text" name="property_listing_id" placeholder="%s" value="%s" />',
            esc_attr__( 'Listing id', 'wpresidence-core' ),
            $listing_id
        );
    }
});

// Filter properties by Listing ID
add_action('pre_get_posts', function ($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    global $pagenow;

    if (
        $pagenow === 'edit.php' &&
        isset($_GET['post_type']) && $_GET['post_type'] === 'estate_property' &&
        !empty($_GET['property_listing_id'])
    ) {
        $listing_id = sanitize_text_field($_GET['property_listing_id']);
        $query->set('meta_key', 'property_internal_id');
        $query->set('meta_value', $listing_id);
    }
});
