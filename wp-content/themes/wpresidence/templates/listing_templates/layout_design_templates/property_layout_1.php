<?php
/** MILLDONE
 * WpResidence Property Page Template
 * src: templates\listing_templates\layout_design_templates\property_layout_1.php
 * This template file is responsible for rendering the main content structure
 * of a property page in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @version 1.0
 * 
 * @uses locate_template()
 * @uses wpestate_property_page_load_media()
 * @uses wpestate_property_disclaimer_section()
 * 
 * Dependencies:
 * - WordPress core functions
 * - WpResidence theme-specific functions and variables
 * 
 */




$wpestate_options = get_query_var('wpestate_options');

?>

<div class="wpresidence-content-container-wrapper col-12 d-flex flex-wrap lay1">
    <?php
    // Load breadcrumbs
    include(locate_template('/templates/listing_templates/property-page-templates/property-page-breadcrumbs.php'));
    
    // Load title section (not overview)
    include(locate_template('templates/listing_templates/property-page-templates/property_header_area_template.php'));

    ?>
    
    <div class="<?php echo esc_attr($wpestate_options['content_class']); ?> wpestate_column_content full_width_prop">
        <div class="single-content listing-content">
            <?php
            // Load media (sliders, gallery, etc.)
            wpestate_property_page_load_media($post->ID, $wpestate_options, 1);
            
            // Load content in tabs or accordion format
            $template_path = ($content_type === 'tabs') 
                ? '/templates/listing_templates/tabs-template.php'
                : '/templates/listing_templates/accordion-template.php';
            include(locate_template($template_path));
            ?>
        </div>
    </div>
    
    <?php
    // Load the sidebar
    include(locate_template('sidebar.php'));
    ?>
</div>

<?php echo wpestate_property_disclaimer_section($post->ID); ?>