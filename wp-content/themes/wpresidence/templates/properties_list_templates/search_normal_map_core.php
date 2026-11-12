<?php
/** MILLDONE
 * Template for displaying property search results with a normal map layout
 * src:templates\properties_list_templates\search_normal_map_core.php
 * This template is part of the WpResidence theme and handles the display of
 * property search results with a standard layout. It includes search filters,
 * property listings, and pagination.
 *
 * @package WpResidence
 * @subpackage PropertySearch
 * @since WpResidence 1.0
 */

// Fetch and set up global configuration options
$wpestate_uset_unit          = intval(wpresidence_get_option('wpestate_uset_unit', ''));
$wpestate_no_listins_per_row = intval(wpresidence_get_option('wp_estate_listings_per_row', ''));
$wpestate_custom_unit_structure = wpresidence_get_option('wpestate_property_unit_structure');
$wpestate_property_unit_slider  = wpresidence_get_option('wp_estate_prop_list_slider', '');
$property_card_type             = intval(wpresidence_get_option('wp_estate_unit_card_type'));

// Determine which column class we will use
$property_unit_class = wpestate_return_unit_class($wpestate_options);

// Open in new page option
$new_page_option = wpresidence_get_option('wp_estate_unit_card_new_page', '');


// Determine the property card type string for template inclusion
$property_card_type_string = ($property_card_type == 0) ? '' : '_type' . $property_card_type;

// Check if Elementor is handling the archive location
if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('archive')) {
?>
    <div class="row wpresidence_page_content_wrapper">
        <?php 
        // Include breadcrumbs template
        get_template_part('templates/breadcrumbs'); 
        ?>
        <div class="p-0 p04mobile wpestate_column_content <?php print esc_html($wpestate_options['content_class']); ?>">
            <?php
            // Check if we're on the advanced search results page
            if (is_page_template('page-templates/advanced_search_results.php')) {
                while (have_posts()) : the_post();
                    // Display page title if set to show
                    if (esc_html(get_post_meta($post->ID, 'page_show_title', true)) == 'yes') { 
                        ?>
                        <h1 class="entry-title title_prop"><?php the_title(); print " (".esc_html($num).")" ?></h1>
                    <?php } ?>
                    <div class="single-content">
                        <?php
                        // Display page content
                        the_content();
                        // Include saved search template
                        include(locate_template('templates/properties_list_templates/saved_search_template.php'));
                        ?>
                    </div>
                <?php 
                endwhile;

                // Include property list filters for search
                include(locate_template('templates/properties_list_templates/filters_templates/property_list_filters_search.php'));
            }
            ?>

            <!-- Property Listings Container -->
            <?php get_template_part('templates/spiner'); ?>
            <div id="listing_ajax_container" class="row">
                <?php
                $show_compare_only = 'yes';
                $counter = 0;
                
                $first = 0;
           
                
                wpresidence_display_property_list_as_html($prop_selection,$wpestate_options  ,'search_list');


                // Set base point for map (first property)
                if (isset($_GET['is2']) && $_GET['is2'] == 1 && $first == 0) {
                    $gmap_lat  = esc_html(get_post_meta($post->ID, 'property_latitude', true));
                    $gmap_long = esc_html(get_post_meta($post->ID, 'property_longitude', true));
                    if ($gmap_lat != '' && $gmap_long != '') {
                        print '<span style="display:none" id="basepoint" data-lat="'.esc_attr($gmap_lat).'" data-long="'.esc_attr($gmap_long).'"></span>';
                        $first = 1;
                    }
                }

                wp_reset_query();
                ?>
            </div>
            <!-- End Property Listings Container -->

            <!-- Pagination -->
            <?php wpestate_pagination($prop_selection->max_num_pages, $range = 2); ?>
            
            <!-- Additional Content -->
            <div class="single-content">
                <?php
                $property_list_second_content = get_post_meta($post->ID, 'property_list_second_content', true);
                echo do_shortcode($property_list_second_content);
                ?>
            </div>
        </div><!-- end 9col container-->

        <?php include get_theme_file_path('sidebar.php'); ?>
    </div>
<?php 
} // end elementor check
?>