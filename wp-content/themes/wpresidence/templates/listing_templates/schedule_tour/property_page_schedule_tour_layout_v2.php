<?php
/**MILLDONE
 * Property Tour Schedule Template
 * src: templates/listing_templates/schedule_tour/property_page_schedule_tour_layout_v2.php
 * This template handles the display of the property tour scheduling feature.
 * It checks if the feature should be displayed based on property categories
 * and theme settings, then outputs the appropriate scheduling form.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 */

 //set property id for contact forms
 //propertyID - set property id for contact forms already defined in the parent template


// Get property ID and tour scheduling settings
$use_schedule_tour = wpresidence_get_option('wp_estate_show_schedule_tour', '');
$exclude_categories = wpresidence_get_option('wp_estate_exclude_show_schedule_tour', '');
$context = 'schedule_section';

// Check if tour scheduling should be excluded for this property's categories
if (is_array($exclude_categories) && !empty($exclude_categories)) {
    $property_terms = array_merge(
        wp_get_post_terms($propertyID, 'property_category', ['fields' => 'ids']) ?: [],
        wp_get_post_terms($propertyID, 'property_action_category', ['fields' => 'ids']) ?: []
    );

    if (array_intersect($property_terms, $exclude_categories)) {
        $use_schedule_tour = 'no';
    }
}

// Display tour scheduling if enabled
if ($use_schedule_tour === 'yes') {
    $tour_type = intval(wpresidence_get_option('wp_estate_show_schedule_tour_type', ''));

    do_action('before_wpestate_schedule_tour');

    if ($tour_type === 0) {
        // Standard tour scheduling layout
        ?>
        <div class="wpestate_schedule_tour_wrapper_content wpestate_contact_form_parent">
            <?php
            include(locate_template('/templates/listing_templates/schedule_tour/schedule_tour_dates.php'));
            include(locate_template('/templates/listing_templates/schedule_tour/schedule_tour_options.php'));
            ?>
            <h5 class="wpestate_tour_info_headline"><?php esc_html_e('Your information', 'wpresidence'); ?></h5>
            <?php
            include(locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php'));
            ?>
        </div>
        <?php
    } else {
        // Alternative tour scheduling layout with image
        $main_image = wpestate_return_property_card_main_image($propertyID, 'property_featured_sidebar');
        ?>
        <div class="wpestate_shedule_tour_wrapper_type2 row wpestate_contact_form_parent">
            <div class="wpestate_shedule_tour_wrapper_type2_image col-md-6 d-none d-md-block" style="background-image:url(<?php echo esc_url($main_image); ?>)"></div>
            <div class="wpestate_shedule_tour_wrapper_type2_content  col-12 col-md-6">
                <?php
                include(locate_template('/templates/listing_templates/schedule_tour/schedule_tour_dates.php'));
                include(locate_template('/templates/listing_templates/schedule_tour/schedule_tour_options.php'));

    
                include(locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php'));
                ?>
            </div>
        </div>
        <?php
    }

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
