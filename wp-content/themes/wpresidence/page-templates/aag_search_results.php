<?php
/**MILLDONE
 * Template Name: Agents Agencies Developers Search Results
 * src: page-templates/aag_search_results.php
 * This template handles the display of search results for agents, agencies, and developers
 * in the WpResidence theme. It processes search parameters and displays results.
 *
 * @package WpResidence
 * @subpackage AgentSearch
 * @since 1.0.0
 */

// Check for WpResidence Core Plugin
if (!function_exists('wpestate_residence_functionality')) {
    esc_html_e('This page will not work without WpResidence Core Plugin, Please activate it from the plugins menu!', 'wpresidence');
    exit();
}

get_header();
wp_suspend_cache_addition(true);
$wpestate_options = get_query_var('wpestate_options');
?>

<div class="row wpresidence_page_content_wrapper">
    <?php get_template_part('templates/breadcrumbs'); ?>
    <div class="p-0 p04mobile wpestate_column_content <?php echo esc_attr($wpestate_options['content_class']); ?>">
        <?php get_template_part('templates/ajax_container'); ?>
        
        <?php
        while (have_posts()) : the_post();
            if (esc_html(get_post_meta($post->ID, 'page_show_title', true)) != 'no') {
                echo '<h1 class="entry-title">' . get_the_title() . '</h1>';
            }
            echo '<div class="single-content">' . get_the_content() . '</div>';
        endwhile;
        ?>
        
        <div id="listing_ajax_container_agent" class="row col-12 <?php echo esc_attr(isset($_GET['_search_post_type']) ? sanitize_text_field($_GET['_search_post_type']) : ''); ?>">
            <?php
            $all_out_tax = array();
            $_search_post_type = isset($_GET['_search_post_type']) ? sanitize_text_field($_GET['_search_post_type']) : null;

            $args = array(
                'cache_results'  => false,
                'paged'          => $paged,
                'posts_per_page' => 10
            );

            // Set post type and taxonomies based on search type
            if ($_search_post_type) {
                $args['post_type'] = $_search_post_type;
                $taxonomy_mapping = array(
                    'estate_agent' => array('_property_city_agent', '_property_area_agent', '_property_category_agent', '_property_action_category_agent'),
                    'estate_agency' => array('_city_agency', '_area_agency', '_category_agency', '_action_category_agency'),
                    'estate_developer' => array('_property_city_developer', '_property_area_developer', '_property_category_developer', '_property_action_developer')
                );

                if (isset($taxonomy_mapping[$_search_post_type])) {
                    foreach ($taxonomy_mapping[$_search_post_type] as $tax_key) {
                        if (isset($_GET[$tax_key]) && $_GET[$tax_key] != 'all') {
                            $all_out_tax[] = array(
                                'taxonomy' => ltrim($tax_key, '_'),
                                'field'    => 'slug',
                                'terms'    => sanitize_text_field($_GET[$tax_key]),
                            );
                        }
                    }
                }
            }

            // Add taxonomies to query if set
            if (!empty($all_out_tax)) {
                $args['tax_query'] = $all_out_tax;
            }

            // Add keyword search if set
            if (isset($_GET['_keyword_search'])) {
                $args['s'] = sanitize_text_field($_GET['_keyword_search']);
            }

            // Perform the search query
            $agent_selection = new WP_Query($args);

            // Display results
            wpresidence_display_agent_list_as_html($agent_selection, $_search_post_type, $wpestate_options, 'agent_search');
            ?>
        </div>
        <?php wpestate_pagination($agent_selection->max_num_pages, $range = 2); ?>
    </div><!-- end 9col container-->
    
    <?php include get_theme_file_path('sidebar.php'); ?>
</div>   

<?php
wp_suspend_cache_addition(false);
get_footer();
?>