<?php
/**MILLDONE
 * Schedule Tour Options Template
 *src: templates\listing_templates\schedule_tour\schedule_tour_options.php
 * This template generates and displays the time slot selection and tour type options
 * for scheduling property tours in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * Dependencies:
 * - WordPress wpresidence_get_option() function for retrieving theme options
 * - WpResidence theme actions and CSS classes
 * - SVG icons located in the theme's templates/svg_icons/ directory
 */

// Retrieve time slot options from theme settings
$options = wpresidence_get_option('wp_estate_schedule_tour_timeslots', '');
$options_array = explode(',', $options);

// Allow for pre-options content or modifications
do_action('before_wpestate_display_schedule_tour_dates_options');

// Time slot selection dropdown
?>
<select name="wpestate_schedule_tour_time" class="form-select" id="wpestate_schedule_tour_time">
    <option value="0"><?php esc_html_e('Please select the time', 'wpresidence'); ?></option>
    <?php foreach ($options_array as $value) : ?>
        <option value="<?php echo esc_attr(trim($value)); ?>"><?php echo esc_html(trim($value)); ?></option>
    <?php endforeach; ?>
</select>

<?php
// Allow for post-time selection content or modifications
do_action('after_wpestate_display_schedule_tour_dates_option_time');

// Tour type options
?>
<div class="wpestate_display_schedule_tour_options_wrapper">
    <div class="wpestate_display_schedule_tour_option schedule_in_person shedule_option_selected">
        <?php include(locate_template('templates/svg_icons/person_icon.svg')); ?>
        <?php esc_html_e('In Person', 'wpresidence'); ?>
    </div>
   
    <div class="wpestate_display_schedule_tour_option schedule_video_chat">
        <?php include(locate_template('templates/svg_icons/video_camera.svg')); ?>
        <?php esc_html_e('Video Chat', 'wpresidence'); ?>
    </div>
</div>

<?php
// Allow for post-options content or modifications
do_action('after_wpestate_display_schedule_tour_dates_options');
?>