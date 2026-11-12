<?php
/** MILLDONE
 * Footer template for WpResidence theme
 * src: footer.php
 * This template handles the closing of the main content area and the display
 * of the footer based on various conditions and theme options.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

// Close main content wrapper if not on a single property page
if (!is_singular('estate_property')) {
    echo '</main><!-- end content_wrapper started in header -->';
}
?>
</div> <!-- end class container -->

<?php
// Initialize post ID
$post_id = isset($post->ID) ? $post->ID : '';

// Get footer display option
$show_foot = wpresidence_get_option('wp_estate_show_footer', '');

// Get global studio object
global $wpestate_studio;

// Get logo header type
$logo_header_type = wpresidence_get_option('wp_estate_logo_header_type', '');
$show_half_map_footer = wpresidence_get_option('wp_estate_property_list_type_footer', '');

// Check if we should display the standard footer or studio footer
if (!wpestate_display_studio_footer()) {
    // Display standard footer if conditions are met
    if ($show_foot == 'yes' && (!wpestate_half_map_conditions($post_id) || $show_half_map_footer == 'yes')) {
        // Check if Elementor footer location exists, otherwise use default template
        if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('footer')) {
   
            get_template_part('templates/footers/footer_template');
          
        }
    }
} else {
    if((!wpestate_half_map_conditions($post_id) || $show_half_map_footer == 'yes') ){
        // Display custom Elementor footer for studio
        $wpestate_studio->header_footer_instance->display_custom_elementor_footer();
    }
 
}

// Close additional wrapper for specific header type
if ($logo_header_type == 'type4') {
    echo '</div><!-- end colophon-->';
}
?>

</div> <!-- end website wrapper -->

<?php
// WordPress footer action
wp_footer();

// Remove studio helper if it exists
if (isset($wpestate_studio) && is_object($wpestate_studio)) {
    $wpestate_studio->header_footer_instance->wpestate_helper_remove();
}


?>
</body>
</html>