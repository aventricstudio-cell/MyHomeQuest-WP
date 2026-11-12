<?php
/** MILLDONE
 * Property Contact and Schedule Tour Section for sidebar
 * src: templates\property_list_agent.php
 * This template part generates the contact form and schedule tour sections
 * for property listings in the WpResidence theme. It handles different scenarios
 * based on user roles and theme options.
 *
 * @package WpResidence
 * @subpackage PropertyListings
 * @since 1.0
 */

// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

// Determine the property ID based on the context (Elementor or regular page)
if (class_exists( 'Elementor\Plugin')) {
    $prop_id = $propertyID = \Elementor\Plugin::$instance->editor->is_edit_mode() ? $property_id : $post->ID;
} else {
    $prop_id = $propertyID = $post->ID;
}

// Override property ID for specific page template
if (get_page_template_slug() == 'page-templates/page_property_design.php') {
    $prop_id = $propertyID = $property_id;
}

// Fetch realtor details and theme options
$realtor_details = wpestate_return_agent_details($prop_id);
$wp_estate_sidebar_contact_group = wpresidence_get_option('wp_estate_sidebar_contact_group', '');
$use_schedule_tour = wpresidence_get_option('wp_estate_show_schedule_tour', '');

// Reset query to ensure correct global post data
wp_reset_query();

$agent_wid = $agentID = $realtor_details['agent_id'];

// Generate contact sidebar content for non-admin users
$contact_sidebar = '';
if (get_the_author_meta('user_level', $agent_wid) != 10) {
    ob_start();
    ?>
    <div class="agent_contanct_form_sidebar widget-container wpestate_contact_form_parent">
        <?php
        include(locate_template('templates/agent_unit_widget_sidebar.php'));
        $context = 'sidebar_page';
        include(locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php'));
        ?>
    </div>
    <?php
    $contact_sidebar = ob_get_clean();
}

// Generate schedule tour content
ob_start();
$schedule_on_sidebar = 'yes';
include(locate_template('/templates/listing_templates/schedule_tour/property_page_schedule_tour.php'));
$contact_schedule = ob_get_clean();

// Display contact and schedule tour sections
if ($wp_estate_sidebar_contact_group == 'yes' && $use_schedule_tour == 'yes' && trim($contact_sidebar) != '') {
    ?>
    <div id="wpestate_sidebar_property_contact_tabs">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="sidebar-contact-tab" data-bs-toggle="tab" data-bs-target="#sidebar_contact" type="button" role="tab" aria-controls="sidebar_contact" aria-selected="true">
                    <?php esc_html_e('Request Info', 'wpresidence'); ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sidebar-schedule-tab" data-bs-toggle="tab" data-bs-target="#sidebar_schedule" type="button" role="tab" aria-controls="sidebar_schedule" aria-selected="false">
                    <?php esc_html_e('Schedule a tour', 'wpresidence'); ?>
                </button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="sidebar_contact" role="tabpanel" aria-labelledby="sidebar-contact-tab">
                <?php echo trim($contact_sidebar); ?>
            </div>
            <div class="tab-pane fade" id="sidebar_schedule" role="tabpanel" aria-labelledby="sidebar-schedule-tab">
                <?php echo trim($contact_schedule); ?>
            </div>
        </div>
    </div>
    <?php
} else {
    echo trim($contact_sidebar);
    echo trim($contact_schedule);
}
?>