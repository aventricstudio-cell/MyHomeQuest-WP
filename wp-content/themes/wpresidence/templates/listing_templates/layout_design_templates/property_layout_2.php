<?php
/** MILLDONE
 * WpResidence Theme - Property Layout 2 Template
 * src  templates/listing_templates/layout_design_templates/property_layout_2.php
 * This file contains the layout structure for property layout 2 in the WpResidence theme.
 * It displays the property header, media, content, and sidebar.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since 1.0.0
 *
 * @uses wpestate_property_page_load_media()
 * @uses wpestate_property_disclaimer_section()
 *
 * Dependencies:
 * - templates/listing_templates/property-page-templates/property-page-breadcrumbs.php
 * - templates/listing_templates/property-page-templates/property_header_area_template.php
 * - templates/listing_templates/tabs-template.php
 * - templates/listing_templates/accordion-template.php
 * - sidebar.php
 *
 * Usage: This template is automatically loaded by WordPress when displaying
 * a property page with layout 2 selected.
 */

// Retrieve options passed to the template
$wpestate_options = get_query_var('wpestate_options');



?>

<div class="wpestate_property_header_extended">
    <?php
    // Load media like sliders, gallery, etc.
    wpestate_property_page_load_media($post->ID, $wpestate_options, 2);
    ?>
</div>

    <div class=" wpresidence-content-container-wrapper col-12 d-flex flex-wrap wpresidence_property_layout2"><!-- START ROW container -->
        <?php
        // Load breadcrumbs
        include(locate_template('/templates/listing_templates/property-page-templates/property-page-breadcrumbs.php'));
        
        // Load title section (not overview)
        include(locate_template('templates/listing_templates/property-page-templates/property_header_area_template.php'));
        ?>
        
        <div class="wpestate_column_content <?php echo esc_attr($wpestate_options['content_class']); ?>">
            <div class="single-content listing-content">
                <?php
                // Load content in tabs or accordion format
                if ($content_type === 'tabs') {
                    include(locate_template('/templates/listing_templates/tabs-template.php'));
                } else {
                    include(locate_template('/templates/listing_templates/accordion-template.php'));
                }
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