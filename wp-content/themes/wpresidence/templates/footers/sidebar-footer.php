<?php
/** MILLDONE
 * Footer Sidebar Template for WpResidence theme
 * src: templates\footers\sidebar-footer.php
 * This template handles the display of footer widget areas based on the
 * selected footer type and active sidebars.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since WpResidence 1.0
 */

// Check if any footer widget areas are active
$footer_widget_areas = [
    'first-footer-widget-area',
    'second-footer-widget-area',
    'third-footer-widget-area',
    'fourth-footer-widget-area'
];

$active_widget_areas = array_filter($footer_widget_areas, 'is_active_sidebar');

// Exit if no widget areas are active
if (empty($active_widget_areas)) {
    return;
}

// Get footer type from theme options
$footer_type = wpresidence_get_option('wp_estate_footer_type', '');
$footer_type = $footer_type ?: '1';  // Default to type 1 if not set

// Define column classes for different footer types
$footer_layouts = [
    '1' => ['col-12 col-md-6 col-lg-3', 'col-12 col-md-6 col-lg-3', 'col-12 col-md-6 col-lg-3', 'col-12 col-md-6 col-lg-3'],
    '2' => ['col-12 col-md-6 col-lg-4', 'col-12 col-md-6 col-lg-4', 'col-12 col-md-6 col-lg-4', ''],
    '3' => ['col-12 col-md-6 col-lg-6', 'col-12 col-md-6 col-lg-6', '', ''],
    '4' => ['col-12 col-md-12 col-lg-12', '', '', ''],
    '5' => ['col-12 col-md-6 col-lg-6', 'col-12 col-md-6 col-lg-3', 'col-12 col-md-6 col-lg-3', ''],
    '6' => ['col-12 col-md-6 col-lg-3', 'col-12 col-md-6 col-lg-6', 'col-12 col-md-6 col-lg-3', ''],
    '7' => ['col-12 col-md-6 col-lg-3', 'col-12 col-md-6 col-lg-3', 'col-12 col-md-6 col-lg-6', ''],
    '8' => ['col-12 col-md-12 col-lg-8', 'col-12 col-md-6 col-lg-4', '', ''],
    '9' => ['col-12 col-md-6 col-lg-4', 'col-12 col-md-12 col-lg-8', '', '']
];

$classes = $footer_layouts[$footer_type] ?? $footer_layouts['1'];

// Display active widget areas
foreach ($footer_widget_areas as $index => $widget_area) {
    $class = $classes[$index];
    if (is_active_sidebar($widget_area) && $class) {
        $area_id = explode('-', $widget_area)[0];  // Extract 'first', 'second', etc.
        ?>
        <div id="<?php echo esc_attr($area_id); ?>" class="widget-area <?php echo esc_attr($class); ?>">
            <ul class="xoxo">
                <?php dynamic_sidebar($widget_area); ?>
            </ul>
        </div>
        <?php
    }
}
?>