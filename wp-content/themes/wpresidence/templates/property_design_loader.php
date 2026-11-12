<?php
/** MILLDONE
 * Property Design Loader Template
 * src: templates\property_design_loader.php
 * This file is responsible for loading and displaying the custom property design
 * in the WpResidence theme. It handles the layout and content of individual property pages.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since 1.0
 *
 * @uses get_post_meta()
 * @uses get_template_part()
 * @uses locate_template()
 * @uses get_theme_file_path()
 * @uses wpestate_listing_pins()
 * @uses wp_localize_script()
 *
 * Dependencies:
 * - WordPress core functions
 * - WpResidence theme-specific functions and variables
 * 
 * Usage:
 * This file is typically included by the single property template when a custom design is selected.
 */




// Determine which page template to use
$page_to = ($wp_estate_local_page_template != 0) ? $wp_estate_local_page_template : $wp_estate_global_page_template;

?>

<div class="row estate_property_first_row" data-prp-listingid="<?php echo esc_attr($post->ID); ?>">
    <?php include(locate_template('/templates/listing_templates/property-page-templates/property-page-breadcrumbs.php')); ?>
    
    <div class="col-xs-12 <?php echo esc_attr($wpestate_options['content_class']); ?> ">
        <?php get_template_part('templates/ajax_container'); ?>
        
        <?php while (have_posts()) : the_post();
   
            $post_title = get_post_meta($page_to, 'page_show_title', true);
            if ($post_title != 'no') : ?>
                <h1 class="entry-title"><?php the_title(); ?></h1>
            <?php endif; ?>
        
            <div class="single-content page_template_loader">
                <?php

            $post = get_post($page_to);

            if ($post && $post->post_status === 'publish') {

                setup_postdata($post);
                the_content();
                wp_reset_postdata();
            }
                ?>
            </div><!-- single content-->
        <?php endwhile; ?>
    </div>
    
    <?php include(get_theme_file_path('sidebar.php')); ?>
</div>

</div><!-- Closing div for container -->
</div><!-- Closing div for wrapper -->

<?php
// Set up map arguments
$mapargs = array(
    'post_type'   => 'estate_property',
    'post_status' => 'publish',
    'p'           => $post->ID,
    'fields'      => 'ids'
);

$selected_pins = wpestate_listing_pins('blank_single', 0, $mapargs, 1);

// Localize script for Google Maps
wp_localize_script('googlecode_property', 'googlecode_property_vars2', array(
    'markers2' => $selected_pins
));

get_footer();
exit();
?>