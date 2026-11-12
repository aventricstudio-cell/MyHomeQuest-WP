<?php
/** MILLDONE
 * WpResidence Theme - Property Layout 3 Template
 * src  templates/listing_templates/layout_design_templates/property_layout_3.php
 * This file contains the layout structure for property layout 3 in the WpResidence theme.
 * It displays the property header, media, content, and sidebar.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since 1.0.0
 *
 * Dependencies:
 * - wpestate_property_page_load_media()
 * - wpestate_property_disclaimer_section()
 * - /templates/listing_templates/property-page-templates/property-page-breadcrumbs.php
 * - /templates/listing_templates/property-page-templates/property_header_area_template.php
 * - /templates/listing_templates/tabs-template.php
 * - /templates/listing_templates/accordion-template.php
 * - sidebar.php
 *
 * Usage: This template is automatically loaded by WordPress when displaying
 * a property page with the appropriate layout selected.
 */

// Retrieve options passed to the template
$wpestate_options = get_query_var('wpestate_options');


?>


    <div class=" wpresidence-content-container-wrapper col-12 d-flex flex-wrap  wpresidence_property_layout3"><!-- START ROW container -->
        <?php
        // Load breadcrumbs
        include(locate_template('/templates/listing_templates/property-page-templates/property-page-breadcrumbs.php'));
        
        // Load title section (not overview)
        include(locate_template('templates/listing_templates/property-page-templates/property_header_area_template.php'));
        ?>
       
        <div class="wpestate_lay3_media_wrapper col-12 col-md-12">
            <?php
            // Load media like sliders, gallery, etc.
            wpestate_property_page_load_media($post->ID, $wpestate_options, 3);
            ?>
        </div>
        
        <div class="wpestate_column_content <?php echo esc_attr($wpestate_options['content_class']); ?> full_width_prop">
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