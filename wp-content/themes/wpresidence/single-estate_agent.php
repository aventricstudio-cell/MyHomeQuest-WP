<?php
/**MILLDONE
 * WPResidence Agent Page Template
 * src: single-estate_agent.php
 * This file serves as the main template for displaying individual agent pages
 * within the WPResidence theme. It determines which specific agent template
 * to load based on theme options.
 *
 * @package    WPResidence
 * @subpackage Templates
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
$use_default_template=true;
// Check if Elementor is being used to render this page
if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {

    if( did_action( 'elementor/loaded' ) && function_exists('wpestate_single_agent_enabled') && wpestate_single_agent_enabled()  ) {
        wpestate_render_single_agent();
        $use_default_template=false;
    }
}




// Retrieve theme options and settings
$wpestate_options       = get_query_var('wpestate_options');
$show_compare           = 1; // This seems unused in this file, consider removing if not needed



// if we use default template
if($use_default_template):
    $wpestate_currency      = esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
    $where_currency         = esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
    $agent_page_template    = intval( wpresidence_get_option('wp_estate_agent_layouts', '') );

    // Load the appropriate agent template based on the theme option
    switch ($agent_page_template) {
        case 1:
            $template_path = 'templates/realtor_templates/agent_template_1.php';
            break;
        case 2:
            $template_path = 'templates/realtor_templates/agent_template_2.php';
            break;
        default:
            $template_path = 'templates/realtor_templates/agent_template_1.php';
            break;
    }

    // Include the selected template file
    if ( file_exists( get_theme_file_path( $template_path ) ) ) {
        include( get_theme_file_path( $template_path ) );
    } else {
        // Fallback in case the template file is missing
        esc_html_e( 'Error: Agent template file not found.', 'wpresidence' );
    }
endif;


get_footer();