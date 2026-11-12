<?php
/** MILLDONE
 * WpResidence Theme - Property Layout 7 Template
 * src templates/listing_templates/layout_design_templates/property_layout_7.php
 * This file contains the layout structure for property layout 7 in the WpResidence theme.
 * It includes breadcrumbs, title section, overview, media, and a three-column content layout.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since 1.0.0
 *
 * Dependencies:
 * - wpestate_property_page_load_media()
 * - wpestate_property_overview_v2()
 * - wpestate_property_disclaimer_section()
 * - wpresidence_get_option()
 * - /templates/listing_templates/property-page-templates/property-page-breadcrumbs.php
 * - /templates/listing_templates/property-page-templates/property_header_area_template.php
 * - /templates/listing_templates/accordion-template_lay_6.php
 *
 * Usage: This template is automatically loaded by WordPress when displaying
 * a property page with layout 7 selected.
 */

// Retrieve options passed to the template
$wpestate_options = get_query_var('wpestate_options');

// Get column layout option
$wp_estate_col_layout = intval(wpresidence_get_option('wp_estate_col_layout', ''));
if (is_rtl()) {
    $class_column_1 = "col-12  ps-lg-0  ps-lg-3 order-lg-1 full_width_prop col-lg-" . $wp_estate_col_layout;
    $class_column_2 = "col-xs-12 col-12  pe-lg-3 pe-lg-0 order-lg-2 col-lg-" . (12 - $wp_estate_col_layout);
}else{
    $class_column_1 = "col-12  ps-lg-0 pe-lg-3 order-lg-1 full_width_prop col-lg-" . $wp_estate_col_layout;
    $class_column_2 = "col-xs-12 col-12  ps-lg-3 pe-lg-0 order-lg-2 col-lg-" . (12 - $wp_estate_col_layout);
}

?>


    <div class=" wpresidence-content-container-wrapper col-12 d-flex flex-wrap wpresidence_property_layout7"><!-- START ROW container -->
        <?php
        // Load breadcrumbs
        include(locate_template('/templates/listing_templates/property-page-templates/property-page-breadcrumbs.php'));
        
        // Load title section (not overview)
        include(locate_template('templates/listing_templates/property-page-templates/property_header_area_template.php'));
        ?>
       
        <div class="wpestate_lay3_media_wrapper col-12 col-md-12">
            <?php
            // Load property overview version 2
            wpestate_property_overview_v2($post->ID);
            
            // Load media like sliders, gallery, etc.
            wpestate_property_page_load_media($post->ID, $wpestate_options, 6);
            ?>
        </div>
        
        <div class="<?php echo esc_attr($class_column_1); ?>">
            <div class="single-content listing-content">
                <?php
                $use_column = 'enabled';
                include(locate_template('/templates/listing_templates/accordion-template_lay_6.php'));
                ?>
            </div><!-- end single-content listing-content -->
        </div><!-- end first column -->
        
        <div class="<?php echo esc_attr($class_column_2); ?>">
            <div class="single-content listing-content">
                <?php
                $use_column = 'after';
                include(locate_template('/templates/listing_templates/accordion-template_lay_6.php'));
                ?>
            </div><!-- end single-content listing-content -->
        </div><!-- end second column -->
    </div><!-- end ROW container -->
   
    <div class="col-12 col-md-12 wpresidence-content-container-wrapper">
        <?php
        $use_column = 'after_content';
        include(locate_template('/templates/listing_templates/accordion-template_lay_6.php'));
        ?>
    </div>


<?php
// Display property disclaimer section
echo wpestate_property_disclaimer_section($post->ID);
?>