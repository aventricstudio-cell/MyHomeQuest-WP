<?php
/**
 * Template part for displaying breadcrumbs in WpResidence theme
 *
 * Displays a Bootstrap 5.3 styled breadcrumb navigation trail showing the current page's
 * position in the site hierarchy. Supports properties, archives, and regular pages.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 4.0
 * 
 * Dependencies:
 * - Bootstrap 5.3 CSS
 * - WpResidence theme options
 * 
 * Theme Option Dependencies:
 * - wp_estate_show_breadcrumbs (yes/no)
 * 
 * Usage:
 * Include this template part using:
 * get_template_part('templates/breadcrumbs');
 */

global $post;

// Get breadcrumbs visibility setting from theme options
$enable_show_breadcrumbs = wpresidence_get_option('wp_estate_show_breadcrumbs');

if ($enable_show_breadcrumbs === 'yes') {

    // Check if referrer is search
    $referrer = wp_get_referer();
    $referrerID = 0;
    if ( $referrer )    {
        $referrerID = url_to_postid( $referrer );
    }
    $isSearch = false;
    if ( (get_option( 'page_on_front' ) == $referrerID && strpos( $referrer, '?s=' ) !== false) || strpos( $referrer, 'search' ) !== false || wpestate_is_search_page($referrerID) )  {
        $isSearch = true;
    }

    // Initialize post-specific variables
    $postid = $custom_image = $rev_slider = '';
    if (isset($post->ID)) {
        $postid = $post->ID;
        $custom_image = esc_html(get_post_meta($postid, 'page_custom_image', true));
        $rev_slider = esc_html(get_post_meta($postid, 'rev_slider', true));
    }

    // Get category information
    $category = '';
    if (is_singular('estate_property')) {
        $category = get_the_term_list($postid, 'property_category', '', ', ', '');
    }
    if (empty($category)) {
        $category = get_the_category_list(', ', '', $postid);
    }

    // Start breadcrumbs markup - using Bootstrap 5.3 classes
    $breadcrumb_html = '';
    
    // Only show breadcrumbs on regular pages (not 404, front page, or search)
    if (!is_404() && !is_front_page() && !is_search()) {
        // Custom arrow separator for Bootstrap breadcrumbs
        $separator_svg = 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'8\' height=\'8\'%3E%3Cpath d=\'M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z\' fill=\'%236c757d\'/%3E%3C/svg%3E';
        
        $breadcrumb_html .= sprintf(
            '<nav style="--bs-breadcrumb-divider: url(&#34;%s&#34;);" aria-label="%s">',
            esc_attr($separator_svg),
            esc_attr__('Breadcrumb', 'wpresidence')
        );
        
        $breadcrumb_html .= '<ol class="breadcrumb">';

        // Add Back to search if referrer is search results
        if ( $isSearch ) {
            $breadcrumb_html .= sprintf(
                '<li class=""><a href="%s">%s</a></li>',
                esc_url(wp_get_referer()),
                esc_html__('Back to Search', 'wpresidence')
            );
        }
        
        // Home link - always first
        $breadcrumb_html .= sprintf(
            '<li class=""><a href="%s">%s</a></li>',
            esc_url(home_url('/')),
            esc_html__('Home', 'wpresidence')
        );

        // Archive pages
        if (is_archive()) {
            if (is_category() || is_tax()) {
                $breadcrumb_html .= sprintf(
                    '<li class=" active" aria-current="page">%s</li>',
                    single_cat_title('', false)
                );
            } else {
                $breadcrumb_html .= sprintf(
                    '<li class=" active" aria-current="page">%s</li>',
                    esc_html__('Archives', 'wpresidence')
                );
            }
        } 
        // Regular pages and posts
        else {
            // Show category if available
            if (!empty($category)) {
                $breadcrumb_html .= sprintf(
                    '<li class="">%s</li>',
                    wp_kses_post($category)
                );
            }

            // Show page hierarchy
            if (!is_front_page() && isset($post)) {
                $parents = get_post_ancestors($post->ID);
                if ($parents) {
                    $id = ($parents) ? $parents[count($parents)-1] : $post->ID;
                    $parent = get_page($id);
                    $breadcrumb_html .= sprintf(
                        '<li class=""><a href="%s">%s</a></li>',
                        esc_url(get_permalink($parent)),
                        esc_html(get_the_title($parent))
                    );
                }
                
                // Current page
                $breadcrumb_html .= sprintf(
                    '<li class=" active" aria-current="page">%s</li>',
                    esc_html(get_the_title($post->ID))
                );
            }
        }

        $breadcrumb_html .= '</ol></nav>';
    } else {
        // Spacer for pages without breadcrumbs
        $breadcrumb_html .= '<div class="py-3"></div>';
    }

    // Output the breadcrumbs container with generated HTML
    printf(
        '<div class="col-12 breadcrumb_container">%s</div>',
        $breadcrumb_html
    );
} else {
    // When breadcrumbs are disabled, still output the container for spacing
    $dashboard_class = wpestate_is_user_dashboard() ? 'breabcrumb_dashboard' : '';
    printf(
        '<div class="col-12 breadcrumb_container %s"></div>',
        esc_attr($dashboard_class)
    );
}
?>