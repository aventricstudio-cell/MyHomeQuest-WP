<?php
/** MILLDONE - on blind
 * Schedule Tour Section for Property Listings
 * src: templates\listing_templates\schedule_tour\property_page_schedule_tour.php
 * This template part generates the schedule tour section for property listings
 * in the WpResidence theme. It checks theme options and property categories
 * to determine whether to display the section.
 *
 * @package WpResidence
 * @subpackage PropertyListings
 * @since 1.0
 */

 //propertyID - set property id for contact forms already defined in the parent template


// Ensure this file is being included within the WordPress framework
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve theme options
$use_schedule_tour = wpresidence_get_option('wp_estate_show_schedule_tour', '');
$exclude_categories = wpresidence_get_option('wp_estate_exclude_show_schedule_tour', '');
$section_title = wpresidence_get_option('wp_estate_property_schedule_tour_text');

// Set default section title if not defined
if (empty($section_title)) {
    $section_title = esc_html__('Schedule a tour', 'wpresidence');
}

// Check if the property belongs to excluded categories
if (is_array($exclude_categories) && !empty($exclude_categories)) {
    $property_terms = array_merge(
        (array) get_the_terms($propertyID, 'property_category'),
        (array) get_the_terms($propertyID, 'property_action_category')
    );

    foreach ($property_terms as $term) {
        if (in_array($term->term_id, $exclude_categories)) {
            $use_schedule_tour = 'no';
            break;
        }
    }
}

// Display the schedule tour section if enabled
if ($use_schedule_tour === 'yes') {
    do_action('before_wpestate_schedule_tour');
    ?>
    <div class="wpestate_schedule_tour_wrapper wpestate_contact_form_parent">
        <h4><?php echo esc_html($section_title); ?></h4>
        <?php
        // Include schedule tour components
        include(locate_template('/templates/listing_templates/schedule_tour/schedule_tour_dates.php'));
        include(locate_template('/templates/listing_templates/schedule_tour/schedule_tour_options.php'));
        ?>
        <h5 class="wpestate_tour_info_headline"><?php esc_html_e('Your information', 'wpresidence'); ?></h5>
        <?php
        $context = 'schedule_section';
        include(locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php'));
        ?>
    </div>
    <?php
    do_action('after_wpestate_schedule_tour');
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            wpestate_schedule_tour_slider();
        });
    </script>
    <?php
}
?>