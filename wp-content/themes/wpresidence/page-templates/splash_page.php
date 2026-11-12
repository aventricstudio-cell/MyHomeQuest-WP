<?php
/** MILLDONE
 * Template Name: Splash Page
 * src: page-templates\splash_page.php
 * This is the template for displaying the splash page in the WpResidence theme.
 * It provides a minimal structure for a landing page with footer scripts and modals.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

// Check if WpResidence Core Plugin is active
if (!function_exists('wpestate_residence_functionality')) {
    esc_html_e('This page will not work without WpResidence Core Plugin. Please activate it from the plugins menu!', 'wpresidence');
    exit();
}

global $post;
get_header();
$wpestate_options = get_query_var('wpestate_options');
?>

</div><!-- end content_wrapper started in header -->
</div> <!-- end class container -->

<?php 
wp_footer(); 

// Output the closing tags for the main wrapper
?>
</div> <!-- end website wrapper -->

<?php
// Include login/register modal
include(locate_template('templates/modals/login_register_modal.php'));

// Create and output nonce for AJAX login/register
$ajax_nonce_log_reg = wp_create_nonce("wpestate_ajax_log_reg");
?>
<input type="hidden" id="wpestate_ajax_log_reg" value="<?php echo esc_attr($ajax_nonce_log_reg); ?>" />

<?php
// Check if the header type is 'type3' and include the top bar sidebar if it is
$logo_header_type = wpresidence_get_option('wp_estate_logo_header_type', '');
if ($logo_header_type == 'type3') {
    include(locate_template('templates/headers/top_bar_sidebar.php'));
}
?>

</body>
</html>