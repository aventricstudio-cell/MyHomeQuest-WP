<?php
/** MILLDONE
 * Property Listing Template for classic listing - to be used with property_list.php file template
 * src:page-templates\property_list.php
 * This template controls the display of property listings in the WpResidence theme.
 * It includes configuration options, layout settings, and content display logic.
 *
 * @package WpResidence
 * @subpackage PropertyListing
 */




// Check if Elementor is handling this location
if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('archive')) {
    ?>
    <div class="row wpresidence_page_content_wrapper">
        <?php
        // Include breadcrumbs template
        get_template_part('templates/breadcrumbs');
        ?>
        <div class="p-0 wpestate_column_content p04mobile <?php print esc_html($wpestate_options['content_class']); ?>">
            <?php
            // Main content loop
            while (have_posts()) : the_post();
                // Check if page title should be displayed
                if (esc_html(get_post_meta($post->ID, 'page_show_title', true)) == 'yes') {
                    ?>
                    <h1 class="entry-title title_prop"><?php the_title(); ?></h1>
                    <?php
                }
                ?>
                <div class="single-content">
                    <?php 
                        $content = get_the_content();
                        if (!is_null($content)) {
                            $content = apply_filters('the_content', $content);
                            echo do_shortcode($content);
                        }
                    ?>
                </div>
            <?php
            endwhile;

        

            // Include property list filters
            include(locate_template('templates/properties_list_templates/filters_templates/property_list_filters_normal_map_core.php'));

            // Display loading spinner
            get_template_part('templates/spiner');

  
            ?>

            <!-- Property Listings Container -->
            <div id="listing_ajax_container" class="row">
                <?php
                    $show_compare_only = 'yes';
                    $counter = 0;

                    // Loop through property listings
                    wpresidence_display_property_list_as_html($prop_selection,$wpestate_options  ,'property_list');

                   
                ?>
            </div>

            <?php
            // Display pagination
            wpestate_pagination($prop_selection->max_num_pages, $range = 2);
            ?>

            <div class="single-content">
                <?php
                // Display additional content for non-taxonomy pages
                if (!is_tax()) {
                    $property_list_second_content = get_post_meta($post->ID, 'property_list_second_content', true);
                    echo do_shortcode($property_list_second_content);
                }
                ?>
            </div>
        </div>

        <?php
        // Include sidebar
        include get_theme_file_path('sidebar.php');
        ?>
    </div>
<?php
}
?>