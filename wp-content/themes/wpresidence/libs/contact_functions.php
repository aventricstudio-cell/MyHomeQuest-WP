<?php
/*MILLDONE
* src: libs\contact_functions.php
*/


 /**
  * Display Simple Schedule Form
  *
  * This function generates a simple schedule form for property viewings.
  * It includes a date input and a time selection dropdown.
  *
  * @package WpResidence
  * @subpackage PropertyDetails
  * @since 3.0.3
  *
  * @return string HTML markup for the simple schedule form.
  */
 
 if (!function_exists('wpestate_display_simple_schedule_form')):
     function wpestate_display_simple_schedule_form() {
         ob_start();
         ?>
         <div class="schedule_wrapper" style="display: none;">
             <input name="schedule_day" class="schedule_day form-control" type="text" 
                    placeholder="<?php echo esc_attr__('Day', 'wpresidence'); ?>" 
                    aria-required="true">
            
             <select name="schedule_hour" class="schedule_hour form-select">
                 <option value="0"><?php echo esc_html__('Time', 'wpresidence'); ?></option>
                 <?php
                 // Generate time options from 7:00 to 19:45 in 15-minute intervals
                 for ($hour = 7; $hour <= 19; $hour++) {
                     for ($minute = 0; $minute <= 45; $minute += 15) {
                         $hour_formatted = str_pad($hour, 2, '0', STR_PAD_LEFT);
                         $minute_formatted = str_pad($minute, 2, '0', STR_PAD_LEFT);
                         $time = $hour_formatted . ':' . $minute_formatted;
                         ?>
                         <option value="<?php echo esc_attr($time); ?>"><?php echo esc_html($time); ?></option>
                         <?php
                     }
                 }
                 ?>
             </select>
         </div>
         <?php
         $return = ob_get_contents();
         ob_end_clean();
         return $return;
     }
 endif;

/**
 * Display Contact Form Title
 *
 * This function generates the title for the contact form based on the current page template
 * and theme settings. It also adds a "Schedule a showing" option if applicable.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * @param string $current_page_template The current page template being used.
 * @param string $contact_form_7_agent  The Contact Form 7 shortcode for agent contact form.
 *
 * @return string The HTML string containing the contact form title and optional scheduling text.
 */

 if (!function_exists('wpestate_display_contact_form_title')):
    function wpestate_display_contact_form_title($current_page_template, $contact_form_7_agent) {
        $return_string = '';

        if ($current_page_template !== 'page-templates/contact_page.php') {
            $return_string .= '<div class="wpestate_single_agent_details_header_wrapper ">';
            // Determine the appropriate title based on the post type
            $title = is_singular(['estate_agency', 'estate_developer']) 
                ? esc_html__('Contact Us', 'wpresidence') 
                : esc_html__('Contact Me', 'wpresidence');

            $return_string .= '<h4 id="show_contact">' . $title . '</h4>';

            // Add scheduling option if classic schedule is enabled and no Contact Form 7 is set
            if (empty($contact_form_7_agent) && wpresidence_get_option('wp_estate_use_classic_schedule', '') === 'yes') {
                $return_string .= '<div class="schedule_meeting">' . esc_html__('Schedule a showing?', 'wpresidence') . '</div>';
            }
            $return_string .= '</div>';
        } else {
            // Contact page title
            $return_string .= '<h4 id="show_contact">' . esc_html__('Contact Us', 'wpresidence') . '</h4>';
        }

        return $return_string;
    }
endif;



/**
 * Display GDPR Consent Checkbox
 *
 * This function generates a GDPR consent checkbox for forms if GDPR is enabled in theme options.
 * It allows for an optional extra identifier to be added to the checkbox ID.
 *
 * @package WpResidence
 * @subpackage Forms
 * @since 3.0.3
 *
 * @param string $extra Optional. An extra identifier to be added to the checkbox ID.
 * @return string HTML string containing the GDPR consent checkbox, or an empty string if GDPR is disabled.
 */

if (!function_exists('wpestate_check_gdpr_case')):
    function wpestate_check_gdpr_case($extra = '') {
        // Check if GDPR is enabled in theme options
        if (wpresidence_get_option('wp_estate_use_gdpr', '') !== 'yes') {
            return '';
        }

        // Prepare the extra identifier
        $extra_id = $extra ? '_' . $extra : '';

        // Prepare variables for HTML
        $checkbox_id = 'wpestate_agree_gdpr' . $extra_id;
        $gdpr_link = wpestate_get_template_link('page-templates/gdpr_terms.php');

        $consent_text = wpresidence_get_option('wp_estate_use_gdpr_text', '');
        if (empty($consent_text)) {
            $consent_text = esc_html__('I consent to the ', 'wpresidence');
        }

        $terms_text = wpresidence_get_option('wp_estate_use_gdpr_text_terms', '');
        if (empty($terms_text)) {
          $terms_text = esc_html__('GDPR Terms', 'wpresidence');
        }
     



        

        // Generate the GDPR consent checkbox HTML

        ob_start();
        ?>
        <div class="gpr_wrapper">
            <input type="checkbox" role="checkbox" aria-checked="false" 
                   id="<?php echo esc_attr($checkbox_id); ?>" 
                   class="wpestate_agree_gdpr" 
                   name="wpestate_agree_gdpr" />
            <label class="wpestate_gdpr_label" for="<?php echo esc_attr($checkbox_id); ?>">
                <?php echo $consent_text; ?> 
                <a target="_blank" href="<?php echo esc_url($gdpr_link); ?>">
                    <?php echo $terms_text; ?>
                </a>
            </label>
        </div>
        <?php
        return ob_get_clean();
    }
endif;

?>