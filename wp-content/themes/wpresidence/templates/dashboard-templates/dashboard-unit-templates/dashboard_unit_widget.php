<?php
/** MILLDONE
 * Dashboard Widget Unit Template
 * src templates\dashboard-templates\dashboard-unit-templates\dashboard_unit_widget.php
 * 
 * Displays a compact property unit in dashboard widgets with image, title, and status.
 * Used for displaying individual property items in various dashboard widget contexts.
 *
 * @package WpResidence
 * @subpackage Dashboard/Widgets
 * @since 1.0
 * 
 * Template Variables Required:
 * - $action_status: String containing the status text to display
 */

// Get property data and sanitize
$item_id = get_the_ID();
$title = get_the_title($item_id);
$link = get_permalink($item_id);

// Get property thumbnail
$preview = wp_get_attachment_image_src(get_post_thumbnail_id($item_id), 'widget_thumb');

// Set default preview image if none exists
if (!isset($preview[0])) {
    $preview = array(get_theme_file_uri('/img/default_listing_105.png'));
}
?>

<div class="dashboard_widget_unit">
    <!-- Property Image -->
    <a class="dashbard_unit_image" href="<?php echo esc_url($link); ?>">
        <img src="<?php echo esc_url($preview[0]); ?>" 
             alt="<?php echo esc_attr($title); ?>"
             class="property-thumb" />
    </a>

    <!-- Property Details Wrapper -->
    <div class="property_dashboard_location_wrapper">
        <!-- Property Title -->
        <a class="dashbard_unit_title" href="<?php echo esc_url($link); ?>">
            <?php echo esc_html($title); ?>
        </a>

        <!-- Property Status -->
        <div class="property_dashboard_location">
            <?php echo esc_html($action_status); ?>
        </div>
    </div>
</div>