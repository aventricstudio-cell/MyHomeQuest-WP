<?php
/**MILLDONE
 * Template for displaying property listings on taxonomy pages with a normal map layout
 * src: templates\properties_list_templates\taxonomy_page_normal_map_core.php
 * @package WpResidence
 * @subpackage PropertyListings
 * @since WpResidence 1.0
 */

$taxonmy                             = get_query_var('taxonomy');
$term                                = get_query_var('term');

// Calculate the column class if we have agent post type
if (is_tax() && $custom_post_type === 'estate_agent') {
    $agent_unit_col_class = wpestate_get_agent_column_class($wpestate_options);
}

if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('archive')) {
?>
    <div class="row wpresidence_page_content_wrapper">
        <?php get_template_part('templates/breadcrumbs'); ?>
        <div class="p-0 p04mobile wpestate_column_content <?php echo esc_attr($wpestate_options['content_class']); ?>">
            <?php
            $page_tax   = '';
            $term_data  = get_term_by('slug', $term, $taxonmy);
            $place_id   = $term_data->term_id;
            $term_meta  = get_option("taxonomy_$place_id");

            if (isset($term_meta['pagetax'])) {
                $page_tax = $term_meta['pagetax'];
            }

            if ($page_tax !== '') {
                $content_post = get_post($page_tax);
                if (isset($content_post->post_content)) {
                    $content = $content_post->post_content;
                    $content = apply_filters('the_content', $content);
                    echo '<div class="single-content">' . $content . '</div>';
                }
            }
            ?>

            <h1 class="entry-title title_prop">
                <?php
                if ($custom_post_type === 'estate_agent') {
                    esc_html_e('Agents in ', 'wpresidence');
                } elseif ($custom_post_type === 'estate_agency') {
                    esc_html_e('Agencies in ', 'wpresidence');
                } elseif ($custom_post_type === 'estate_developer') {
                    esc_html_e('Developers in ', 'wpresidence');
                } else {
                    esc_html_e('Properties listed in ', 'wpresidence');
                }
                single_cat_title();
                ?>
            </h1>

            <?php
            if ($custom_post_type !== 'estate_agent' && $custom_post_type !== 'estate_agency' && $custom_post_type !== 'estate_developer') {
                include(locate_template('templates/properties_list_templates/filters_templates/property_list_filters_taxonomy_normal_map_core.php'));
            } else {
                include(locate_template('templates/properties_list_templates/filters_templates/taxonomy_agent_hidden_filters.php'));
            }
            ?>

            <?php get_template_part('templates/spiner'); ?>

            <?php if ($custom_post_type === 'estate_agent' || $custom_post_type === 'estate_agency' || $custom_post_type === 'estate_developer') { ?>
                <div id="listing_ajax_container_agent_tax" class="<?php echo esc_attr($prop_unit_class); ?>"></div>
            <?php } 
            
            $wrapper_class='row';
            if ( $custom_post_type === 'estate_agency' || $custom_post_type === 'estate_developer') {
                $wrapper_class='';
            }
            ?>






            
            <div id="listing_ajax_container" class="<?php print esc_attr($wrapper_class);?>">
                <?php
                $show_compare_only = 'yes';
                $counter = 0;


                if ($custom_post_type === 'estate_agent' || $custom_post_type === 'estate_agency' || $custom_post_type === 'estate_developer') {
                    wpresidence_display_agent_list_as_html($prop_selection, $custom_post_type, $wpestate_options ,'agent_list');
                }else{
                    wpresidence_display_property_list_as_html($prop_selection,$wpestate_options  ,'property_list');

                }
          
                ?>
            </div>

            <?php wpestate_pagination($prop_selection->max_num_pages, $range = 2); ?>
        </div>
        <?php include get_theme_file_path('sidebar.php'); ?>
    </div>
<?php
}
?>