<?php
/** MILLDONE
 * WpResidence Theme - Property Layout 5 Template
 * src templates/listing_templates/layout_design_templates/property_layout_5.php
 * This file contains the layout structure for property layout 5 in the WpResidence theme.
 * It includes media, overview, breadcrumbs, title section, content, and sidebar.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since 1.0.0
 *
 * Dependencies:
 * - wpestate_property_page_load_media()
 * - wpestate_property_overview_v2()
 * - wpestate_property_disclaimer_section()
 * - /templates/listing_templates/property-page-templates/property-page-breadcrumbs.php
 * - /templates/listing_templates/property-page-templates/property_header_area_template.php
 * - /templates/listing_templates/tabs-template.php
 * - /templates/listing_templates/accordion-template.php
 * - sidebar.php
 *
 * Usage: This template is automatically loaded by WordPress when displaying
 * a property page with layout 5 selected.
 */

// Retrieve options passed to the template
$wpestate_options = get_query_var('wpestate_options');



// Get media type for the property
$media_type = get_post_meta($post->ID, 'local_pgpr_slider_type', true);
if ($media_type === 'global') {
    $media_type = esc_html(wpresidence_get_option('wp_estate_global_prpg_slider_type', ''));
}
?>

<div class="wpestate_property_header_extended wpestate_lay6_<?php echo esc_attr(sanitize_title_with_dashes($media_type)); ?>">
    <?php
    // Load media like sliders, gallery, etc.
    wpestate_property_page_load_media($post->ID, $wpestate_options, 2);
    
    // Load property overview version 2
    wpestate_property_overview_v2($post->ID);
    ?>
</div>


    <div class=" wpresidence-content-container-wrapper  col-12 d-flex flex-wrap  wpresidence_property_layout5"><!-- START ROW container -->
        <?php
        // Load breadcrumbs
        include(locate_template('/templates/listing_templates/property-page-templates/property-page-breadcrumbs.php'));
        
        // Load title section (not overview)
        include(locate_template('templates/listing_templates/property-page-templates/property_header_area_template.php'));
        ?>
        
        <div class="<?php echo esc_attr($wpestate_options['content_class']); ?> wpestate_column_content full_width_prop">
            <div class="single-content listing-content">
                <?php
                // Load content in tabs or accordion format
                $template_path = '/templates/listing_templates/' . ($content_type === 'tabs' ? 'tabs-template.php' : 'accordion-template.php');
                include(locate_template($template_path));
                ?>
            </div><!-- end single-content listing-content -->
        </div><!-- end full_width_prop -->
        
        <?php
        // Load the sidebar
        include(locate_template('sidebar.php'));
        ?>
    </div><!-- end ROW container -->


<?php
// Display property disclaimer section
echo wpestate_property_disclaimer_section($post->ID);
?>