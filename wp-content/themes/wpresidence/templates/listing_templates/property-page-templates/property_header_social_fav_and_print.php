<?php
/**
 * Property Social Actions Section
 *
 * This template part displays social action buttons (share, favorite, print)
 * for a property listing in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since 1.0.0
 */

// Ensure $display_options and $post are available in this context

//check if the template is loaded from property page or from custom property page template  
if(isset($property_id)){
    $selectedPropertyID=$property_id;
}else{
    $selectedPropertyID=$post->ID;
}
?>

<div class="prop_social">
    <?php
    // Share button
    if (isset($display_options['share']) && $display_options['share'] === 'yes') :
        echo wpestate_share_unit_desing($selectedPropertyID, 1); // Function to display share options
    ?>
        <div class="title_share share_list single_property_action" data-bs-toggle="tooltip" title="<?php esc_attr_e('share this page', 'wpresidence'); ?>">
            <?php esc_html_e('Share', 'wpresidence'); ?>
        </div>
    <?php
    endif;

    // Favorite button
    if (isset($display_options['favorite']) && $display_options['favorite'] === 'yes') :
        include(locate_template('templates/listing_templates/property-page-templates/favorite_under_title.php'));
    endif;

    // Print button
    if (isset($display_options['print']) && $display_options['print'] === 'yes') :
    ?>
        <div id="print_page" class="title_share single_property_action" data-propid="<?php echo esc_attr($selectedPropertyID); ?>" 
            data-bs-toggle="tooltip"
            title="<?php esc_attr_e('print page', 'wpresidence'); ?>">
            <i class="fas fa-print"></i><?php esc_html_e('Print', 'wpresidence'); ?>
        </div>
    <?php
    endif;
    ?>
</div>