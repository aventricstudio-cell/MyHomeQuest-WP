<?php
/** MILLDONE
 * WpResidence Slider Option Section
 * src: templates/submit_templates/property_slider.php
 * This file is part of the WpResidence theme and handles the display of the slider option
 * in the property submission or edit form.
 *
 * @package WpResidence
 * @subpackage PropertySubmission
 * @since 1.0.0
 *
 * Dependencies:
 * - WpResidence theme functions (for generating $option_slider)
 *
 * Usage:
 * This file should be included in the property submission or edit form within the WpResidence theme.
 * It displays a dropdown for selecting the slider type for the property.
 */

// Ensure $option_slider is properly escaped before use
$option_slider = isset($option_slider) ? $option_slider : '';
$escaped_option_slider = wp_kses($option_slider, array(
    'option' => array(
        'value' => array(),
        'selected' => array()
    )
));
?>

<div class="submit_container">
    <div class="submit_container_header"><?php esc_html_e('Slider Option', 'wpresidence'); ?></div>
    <p class="full_form">
        <label for="prop_slider_type"><?php esc_html_e('Slider type', 'wpresidence'); ?></label>
        <select id="prop_slider_type" name="prop_slider_type" class="select-submit2">
            <?php echo $escaped_option_slider; ?>
        </select>
    </p>
</div>