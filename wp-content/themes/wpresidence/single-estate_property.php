<?php
/**MILLDONE
 * Single Estate Property Template
 * src: single-estate_property.php
 * This file handles the display of a single estate property page in the WpResidence theme.
 * It checks user permissions, loads appropriate templates, and sets up necessary data for property display.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since 1.0
 *
 * @uses get_header()
 * @uses get_footer()
 * @uses wp_get_current_user()
 * @uses get_post_custom()
 * @uses wp_estate_count_page_stats()
 * @uses wpresidence_get_option()
 * @uses get_post_meta()
 * @uses wpestate_load_property_page_layout()
 * @uses wpestate_listing_pins()
 * @uses wp_localize_script()
 *
 * Dependencies:
 * - WordPress core functions
 * - WpResidence theme-specific functions
 * 
 * Usage:
 * This file is typically used as a template for single property pages in the WpResidence theme.
 */

// Check post status and user permissions
$status = get_post_status($post->ID);
if (!is_user_logged_in()) {
    if ($status === 'expired') {
        wp_safe_redirect(home_url('/'));
        exit;
    }
} else {
    if (!current_user_can('administrator') && $status === 'expired') {
        wp_safe_redirect(home_url('/'));
        exit;
    }
}

get_header();

// Initialize variables
$show_compare_only = 'no';
$current_user      = wp_get_current_user();
$userID            = $current_user->ID;
$user_option       = 'favorites' . intval($userID);
$wpestate_options  = get_query_var('wpestate_options');

// Get property details and count page views

wp_estate_count_page_stats($post->ID);
global $propid;
$propid = $post->ID;

$use_default_template = true;
// Check if Elementor is being used to render this page
if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {

    if( did_action( 'elementor/loaded' ) && function_exists('wpestate_single_property_enabled') && wpestate_single_property_enabled()  ) {
      

        wpestate_render_single_property();

        $use_default_template = false;
    }
}
// if we use default template
if ($use_default_template):

    // Load custom template if set
    $wp_estate_global_page_template = intval(wpresidence_get_option('wp_estate_global_property_page_template'));
    $wp_estate_local_page_template  = intval(get_post_meta($post->ID, 'property_page_design_local', true));
    if ($wp_estate_local_page_template === 0) {
        $wp_estate_local_page_template = intval(get_post_meta($post->ID, 'property_page_desing_local', true));
    }

    if ($wp_estate_global_page_template != 0 || $wp_estate_local_page_template != 0) {
    
        $wpestate_wide_elementor_page_class = '';
        $full_width = get_post_meta($post->ID, 'wpestate_custom_full_width', true);


        if ($full_width=== 'yes') {
            $wpestate_wide_elementor_page_class = "wpestate_wide_elementor_page";
        }

        ?>
        
        <!-- Loading Custom template for property page -->
        <div class="container content_wrapper wpestate_content_wrapper_custom_template <?php echo esc_attr($wpestate_wide_elementor_page_class); ?>">
            <div class="wpestate_content_wrapper_custom_template_wrapper">
                <?php
                
               
                include(locate_template('templates/property_design_loader.php')); ?>
            </div>
        </div>
        <?php
    }

    // Load theme template if Elementor is not being used
    if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {
        $wp_estate_property_layouts = intval(wpresidence_get_option('wp_estate_property_layouts'));
        wpestate_load_property_page_layout($wp_estate_property_layouts,$post->ID);
    }
endif;

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
?>