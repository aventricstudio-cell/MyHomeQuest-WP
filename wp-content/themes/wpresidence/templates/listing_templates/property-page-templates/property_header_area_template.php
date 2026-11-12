<?php
/** MILLDONE
 * WpResidence Property Page Title Area
 * src: templates\listing_templates\property-page-templates\property_header_area_template.php
 * This template file is responsible for rendering the title area of a property page
 * in the WpResidence theme. It includes the title, address, and social action buttons.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @version 1.0
 * 
 * @uses wpresidence_get_option()
 * @uses locate_template()
 * @uses wpestate_share_unit_desing()
 * 
 * Dependencies:
 * - WordPress core functions
 * - WpResidence theme-specific functions and variables
 * 
 * Expected global variables:
 * - $post
 */

// Fetch display options
if(!isset($display_options)){
    $display_options = array(
        'print'    => wpresidence_get_option('wp_estate_show_hide_print_button', ''),
        'favorite' => wpresidence_get_option('wp_estate_show_hide_fav_button', ''),
        'share'    => wpresidence_get_option('wp_estate_show_hide_share_button', ''),
        'address'  => wpresidence_get_option('wp_estate_show_hide_address_details', '')
    );

    // Sanitize options
    foreach ($display_options as $key => $value) {
        $display_options[$key] = esc_html($value);
    }
}

 //check if the template is loaded from property page or from custom property page template  
 if(isset($property_id)){
    $selectedPropertyID=$property_id;
}else{
    $selectedPropertyID=$post->ID;
}




?>

<div class="wpresidence_property_page_title_area col-md-12 col-12 flex-column flex-md-row align-items-start align-items-md-center flex-nowrap flex-md-wrap">
    <?php include(locate_template('templates/listing_templates/property-page-templates/title_section.php')); ?>
    
    <?php if ($display_options['address'] === 'yes'): ?>
        <?php include(locate_template('templates/listing_templates/property-page-templates/address_under_title.php')); ?>
    <?php endif; ?>

    <?php include(locate_template('templates/listing_templates/property-page-templates/property_header_social_fav_and_print.php')); ?>
 
</div>