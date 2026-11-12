<?php
/** MILLDONE
 * Schedule Tour Dates Template
 * src:templates\listing_templates\schedule_tour\schedule_tour_dates.php
 * This template generates and displays the date selection slider for scheduling property tours.
 * It creates a series of date options for the next 10 days, allowing users to select a preferred tour date.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * Dependencies:
 * - WordPress date_i18n() function for localized date formatting
 * - WpResidence theme actions and CSS classes
 */

// Generate a unique ID for the slider
$slider_id = 'wpestate_schedule_tour_slider_' . wp_rand(1, 99999);

// Allow for pre-slider content or modifications
do_action('before_wpestate_display_schedule_tour_dates');

// Determine the number of visible items based on context
$visible_items = isset($schedule_on_sidebar) && $schedule_on_sidebar === 'yes' ? 4 : 6;

// Start of HTML output
?>
<div class="wpestate_property_schedule_dates_wrapper" id="<?php echo esc_attr($slider_id); ?>" data-visible-items="<?php echo esc_attr($visible_items); ?>" data-auto="0">
    <?php
    // Initialize date variables
    $current_date = current_time('timestamp');
    $number_of_days_in_advance = 10;

    // Generate date options for the next 10 days
    for ($counter = 0; $counter < $number_of_days_in_advance; $counter++) {
        $date_timestamp = strtotime("+$counter days", $current_date);
        $day_name = date_i18n('D', $date_timestamp);
        $day_number = date_i18n('d', $date_timestamp);
        $month = date_i18n('M', $date_timestamp);

        // Determine if this date should be pre-selected (2nd day)
        $selected_class = ($counter === 1) ? 'shedule_day_option_selected' : '';

        // Generate HTML for each date option
        ?>
        <div data-scheudle-day="<?php echo esc_attr("$day_name $day_number $month"); ?>" class="wpestate_property_schedule_singledate_wrapper item <?php echo esc_attr($selected_class); ?>">
            <div class="wpestate_property_schedule_singledate_wrapper_display">
                <span class="wpestate_day_unit_day_name"><?php echo esc_html($day_name); ?></span>
                <span class="wpestate_day_unit_day_number"><?php echo esc_html($day_number); ?></span>
                <span class="wpestate_day_unit_day_month"><?php echo esc_html($month); ?></span>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<?php
// Allow for post-slider content or modifications
do_action('after_wpestate_display_schedule_tour_dates');
?>