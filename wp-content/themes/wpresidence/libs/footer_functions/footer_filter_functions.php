<?php
/** MILLDONE
 * Footer includes for WpResidence theme
 * src: libs\footer_functions\footer_filter_functions.php
 * This file handles the addition of various elements to the footer,
 * including buttons, navigation, and security nonces.
 *
 * @package WpResidence
 * @subpackage Footer
 * @since WpResidence 1.0
 */

 add_action('wp_footer', 'wpresidence_footer_includes', 10);

 if (!function_exists('wpresidence_footer_includes')):
     function wpresidence_footer_includes() {
         // Create and output nonce for login/registration
         $ajax_nonce_log_reg = wp_create_nonce("wpestate_ajax_log_reg");
         echo '<input type="hidden" id="wpestate_ajax_log_reg" value="' . esc_attr($ajax_nonce_log_reg) . '" />';
 
         // Include footer buttons template
         get_template_part('templates/footers/footer_buttons');

         // Include navigation template if enabled in theme options
         if (wpresidence_get_option('wp_estate_show_prev_next', '') == 'yes') {
             include(locate_template('/templates/navigational.php'));
         }
 
         // Get WordPress cron schedules
         wp_get_schedules();
 
         // Get header logo type
         $logo_header_type = wpresidence_get_option('wp_estate_logo_header_type', '');
 
         // Include top bar sidebar for specific header type
         if ($logo_header_type == 'type3') {
            include(locate_template('templates/headers/top_bar_sidebar.php'));
         }
 
         // Include compare list template
         get_template_part('templates/compare_list');
 
         // Include additional templates for single property pages
         if (is_singular('estate_property')) {
             include(locate_template('/templates/listing_templates/property-page-templates/image_gallery.php'));
             include(locate_template('/templates/realtor_templates/mobile_agent_area.php'));
         }
 
         // Create and output nonce for AJAX filtering
         $ajax_nonce = wp_create_nonce("wpestate_ajax_filtering");
         echo '<input type="hidden" id="wpestate_ajax_filtering" value="' . esc_attr($ajax_nonce) . '" />';
 
         // Create and output nonce for payments
         $ajax_nonce_pay = wp_create_nonce("wpestate_payments_nonce");
         echo '<input type="hidden" id="wpestate_payments_nonce" value="' . esc_attr($ajax_nonce_pay) . '" />';
 
         // Include property details modal template if enabled
         if (wpestate_is_property_modal()) {
             get_template_part('templates/property_details_modal');
         }
     }
 endif;



/**
 * Check if we should display the WpEstate studio footer
 *
 * This function determines whether to display the custom studio footer
 * based on the presence of specific templates in the WpEstate studio object.
 *
 * @return bool True if the studio footer should be displayed, false otherwise.
 */
if (!function_exists('wpestate_display_studio_footer')):
    function wpestate_display_studio_footer() {
        global $wpestate_studio;
    
        // Define the footer template keys to check
        $footer_templates = array(
            'wpestate_template_footer',
            'wpestate_template_after_footer',
            'wpestate_template_before_footer',
        );
    
        // Check if the WpEstate studio object exists and is properly initialized
        if (isset($wpestate_studio) && is_object($wpestate_studio) && 
            isset($wpestate_studio->header_footer_instance->header_footer_templates) && 
            is_array($wpestate_studio->header_footer_instance->header_footer_templates)) {
    
            // Check if any of the footer templates are present in the studio object
            foreach ($footer_templates as $template) {
                if (in_array($template, $wpestate_studio->header_footer_instance->header_footer_templates)) {
                    return true;
                }
            }
        }
    
        // Return false if no studio footer templates are found
        return false;
    }
endif;