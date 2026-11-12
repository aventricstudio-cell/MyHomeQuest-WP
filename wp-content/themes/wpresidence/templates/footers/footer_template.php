<?php
/** MILLDONE
 * Footer template for WpResidence theme
 * src: templates\footers\footer_template.php
 * This template handles the display of the footer, including its background,
 * sticky behavior, and copyright information.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

// Retrieve footer-related options
$show_sticky_footer = wpresidence_get_option('wp_estate_show_sticky_footer', '');
$footer_background = wpresidence_get_option('wp_estate_footer_background', 'url');
$repeat_footer_back_status = wpresidence_get_option('wp_estate_repeat_footer_back', '');
$logo_header_type = wpresidence_get_option('wp_estate_logo_header_type', '');
$show_foot = wpresidence_get_option('wp_estate_show_footer', '');
$wide_footer = wpresidence_get_option('wp_estate_wide_footer', '');
$wide_status = esc_html(wpresidence_get_option('wp_estate_wide_status', ''));

// Initialize footer style and class variables
$footer_style = '';
$footer_back_class = '';
$wide_footer_class = '';

// Set footer background image if available
if ($footer_background !== '') {
    $footer_style = 'style="background-image: url(' . esc_url($footer_background) . ')"';
}

// Set footer background repeat class
$repeat_classes = [
    'repeat' => 'footer_back_repeat',
    'repeat x' => 'footer_back_repeat_x',
    'repeat y' => 'footer_back_repeat_y',
    'no repeat' => 'footer_back_repeat_no'
];
if (isset($repeat_classes[$repeat_footer_back_status])) {
    $footer_back_class = $repeat_classes[$repeat_footer_back_status];
}

// Add sticky footer class if enabled
if ($show_sticky_footer === 'yes') {
    $footer_back_class .= ' sticky_footer';
}

// Add header type class if applicable
if ($logo_header_type === 'type4') {
    $footer_back_class .= ' footer_header4';
}

// Set default value for show_foot if not set
$show_foot = $show_foot ?: 'yes';

// Add boxed footer class if applicable
if ($wide_status == 2 || $wide_status == '') {
    $footer_back_class .= " boxed_footer";
}

// Set wide footer class if enabled
if ($wide_footer === 'yes') {
    $wide_footer_class = "wide_footer";
}

// Get current post ID if available
$post_id = isset($post->ID) ? $post->ID : '';
?>

<footer id="colophon" <?php echo wp_kses_post($footer_style); ?> class="<?php echo esc_attr($footer_back_class); ?>">
    <div id="footer-widget-area" class="row footer-widget-area <?php echo esc_attr($wide_footer_class); ?>">
        <?php get_template_part('templates/footers/sidebar', 'footer'); ?>
    </div>

    <?php
    // Display subfooter if enabled
    $show_footer_copy = wpresidence_get_option('wp_estate_show_footer_copy', '');
    if ($show_footer_copy === 'yes') :
    ?>
        <div class="sub_footer">
            <div class="sub_footer_content flex-column flex-sm-row   align-items-start 
            align-items-sm-center  <?php echo esc_attr($wide_footer_class); ?>">
                <span class="copyright">
                    <?php
                    $copyright_message = stripslashes(esc_html(wpresidence_get_option('wp_estate_copyright_message', '')));
                    if (function_exists('icl_translate')) {
                        $copyright_message = icl_translate('wpestate', 'wp_estate_copyright_message', $copyright_message);
                    }
                    echo esc_html($copyright_message);
                    ?>
                </span>
                <div class="subfooter_menu">
                    <?php
                    show_support_link();
                    if (has_nav_menu('footer_menu')) {
                        wp_nav_menu(['theme_location' => 'footer_menu']);
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</footer><!-- #colophon -->