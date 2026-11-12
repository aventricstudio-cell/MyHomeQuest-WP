<?php 

/**
 * Property Metabox Management
 *
 * Handles the creation and management of metaboxes for the property post type.
 * Implements a tabbed interface for organizing property data including:
 * - Basic property details
 * - Media and documents
 * - Custom fields
 * - Location/map data
 * - Energy certificates
 * - Agent associations
 * - Floor plans
 * - Payment information
 * - Subunit management
 *
 * @package WpResidence
 * @subpackage Metaboxes
 * @since 1.0
 */

if (!function_exists('wpestate_add_property_metaboxes')):
    /**
     * Registers the main property metabox
     * 
     * Adds a single metabox that contains the tabbed interface
     * for all property data management.
     */
    function wpestate_add_property_metaboxes() {
        add_meta_box('new_tabbed_interface', esc_html__('Property Details', 'wpresidence-core'), 'estate_tabbed_interface', 'estate_property', 'normal', 'default');
    }
endif;

if (!function_exists('wpestate_booking_shortcode_box')):
    /**
     * Renders the booking shortcode input section
     *
     * Provides a dedicated textarea where admins can paste a third-party
     * booking form shortcode to use on the property page.
     */
    function wpestate_booking_shortcode_box() {
        global $post;
        wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');
        $mypost = $post->ID;

        print '<div class="property_prop_half prop_half">
            <label for="property_booking_shortcode">' . esc_html__('Booking Shortcode', 'wpresidence-core') . '</label><br />
            <textarea id="property_booking_shortcode" name="property_booking_shortcode" placeholder="' . esc_html__('Enter the booking form shortcode. Example [booking]', 'wpresidence-core') . '">' . esc_textarea(get_post_meta($mypost, 'property_booking_shortcode', true)) . '</textarea>
        </div>';
    }
endif;

if (!function_exists('estate_tabbed_interface')):
    /**
     * Renders the tabbed interface for property metaboxes
     * 
     * Creates a tabbed UI with sections for:
     * - Property details
     * - Media management
     * - Custom fields
     * - Location data
     * - Energy information
     * - Agent assignment
     * - Floor plans
     * - Payment details
     * - Subunit management
     *
     * Each tab loads its specific content via separate functions.
     */
    function estate_tabbed_interface() {
        global $post;
        $available_tabs = array(
            'property_details',
            'property_media',
            'property_customs',
            'property_map',
            'property_energy',
            'property_agent',
            'property_floor',
            'property_paid',
            'property_booking_shortcode',
            'property_content_options',
            'property_subunits',
            'property_custom_template',
        );

        $active_tab = 'property_details';
        if ( isset( $_GET['property_tab'] ) && in_array( $_GET['property_tab'], $available_tabs, true ) ) {
            $active_tab = sanitize_key( $_GET['property_tab'] );
        }

        print'<div class="property_options_wrapper meta-options">

             <div class="property_options_wrapper_list">';
                print'<div class="property_tab_item'. ( $active_tab === 'property_details' ? ' active_tab' : '' ) .'" data-content="property_details">' . esc_html__('Property Details', 'wpresidence-core') . '</div>';
                print'<div class="property_tab_item'. ( $active_tab === 'property_media' ? ' active_tab' : '' ) .'" data-content="property_media">' . esc_html__('Property Media & Documents', 'wpresidence-core') . '</div>';
                print'<div class="property_tab_item'. ( $active_tab === 'property_customs' ? ' active_tab' : '' ) .'" data-content="property_customs">' . esc_html__('Property Custom Fields', 'wpresidence-core') . '</div>';
                print'<div class="property_tab_item'. ( $active_tab === 'property_map' ? ' active_tab' : '' ) .'" data-content="property_map" id="property_map_trigger">' . esc_html__('Address and Map Location', 'wpresidence-core') . '</div>';
                print'<div class="property_tab_item'. ( $active_tab === 'property_energy' ? ' active_tab' : '' ) .'" data-content="property_energy" id="property_energy_trigger">' . esc_html__('Energy Class', 'wpresidence-core') . '</div>';
                print'<div class="property_tab_item'. ( $active_tab === 'property_agent' ? ' active_tab' : '' ) .'" data-content="property_agent">' . esc_html__('Agent', 'wpresidence-core') . '</div>';
                print'<div class="property_tab_item'. ( $active_tab === 'property_floor' ? ' active_tab' : '' ) .'" data-content="property_floor">' . esc_html__('Floor Plans', 'wpresidence-core') . '</div>';
                print'<div class="property_tab_item'. ( $active_tab === 'property_paid' ? ' active_tab' : '' ) .'" data-content="property_paid">' . esc_html__('Paid Submission', 'wpresidence-core') . '</div>';
                print'<div class="property_tab_item'. ( $active_tab === 'property_booking_shortcode' ? ' active_tab' : '' ) .'" data-content="property_booking_shortcode">' . esc_html__('Booking Shortcode', 'wpresidence-core') . '</div>';
                print'<div class="property_tab_item'. ( $active_tab === 'property_subunits' ? ' active_tab' : '' ) .'" data-content="property_subunits">' . esc_html__('Property  Subunits', 'wpresidence-core') . '</div>';
                print'<div class="property_tab_item'. ( $active_tab === 'property_content_options' ? ' active_tab' : '' ) .'" data-content="property_content_options">' . esc_html__('Display options', 'wpresidence-core') . '</div>';
                print'<div class="property_tab_item'. ( $active_tab === 'property_custom_template' ? ' active_tab' : '' ) .'" data-content="property_custom_template">' . esc_html__('Custom Template', 'wpresidence-core') . '</div>';
            print'</div>
            <div class="property_options_content_wrapper">';
                print'<div class="property_tab_item_content'. ( $active_tab === 'property_details' ? ' active_tab' : '' ) .'" id="property_details"><h3>' . esc_html__('Property Details', 'wpresidence-core') . '</h3>';
        wpestate_estate_box();
        print'</div>

                <div class="property_tab_item_content'. ( $active_tab === 'property_media' ? ' active_tab' : '' ) .'" id="property_media"><h3>' . esc_html__('Property Media & Documents', 'wpresidence-core') . '</h3>';
        wpestate_property_add_media();
        print'</div>

                <div class="property_tab_item_content'. ( $active_tab === 'property_customs' ? ' active_tab' : '' ) .'" id="property_customs"><h3>' . esc_html__('Property Custom', 'wpresidence-core') . '</h3>';
        wpestate_custom_details_box();
        print'</div>
                <div class="property_tab_item_content'. ( $active_tab === 'property_map' ? ' active_tab' : '' ) .'" id="property_map"><h3>' . esc_html__('Map', 'wpresidence-core') . '</h3>';
        wpestate_map_estate_box();
        print'</div>

                <div class="property_tab_item_content'. ( $active_tab === 'property_energy' ? ' active_tab' : '' ) .'" id="property_energy"><h3>' . esc_html__('Energy Class', 'wpresidence-core') . '</h3>';
        wpestate_energy_box();
        print'</div>';

        print'<div class="property_tab_item_content'. ( $active_tab === 'property_agent' ? ' active_tab' : '' ) .'" id="property_agent"><h3>' . esc_html__('Responsible Agent / User', 'wpresidence-core') . '</h3>';
        wpestate_agentestate_box();
        print'</div>
                <div class="property_tab_item_content'. ( $active_tab === 'property_floor' ? ' active_tab' : '' ) .'" id="property_floor"><h3>' . esc_html__('Floor Plans', 'wpresidence-core') . '</h3>';
        wpestate_floorplan_box();
        print'</div>
                <div class="property_tab_item_content'. ( $active_tab === 'property_paid' ? ' active_tab' : '' ) .'" id="property_paid"><h3>' . esc_html__('Paid Submission', 'wpresidence-core') . '</h3>';
        wpestate_estate_paid_submission();
        print'</div>
                <div class="property_tab_item_content'. ( $active_tab === 'property_booking_shortcode' ? ' active_tab' : '' ) .'" id="property_booking_shortcode"><h3>' . esc_html__('Booking Shortcode', 'wpresidence-core') . '</h3>';
        wpestate_booking_shortcode_box();
        print'</div>
                <div class="property_tab_item_content'. ( $active_tab === 'property_subunits' ? ' active_tab' : '' ) .'" id="property_subunits"><h3>' . esc_html__('Property Subunits', 'wpresidence-core') . '</h3>';
        wpestate_propery_subunits();
        print'</div>
                <div class="property_tab_item_content'. ( $active_tab === 'property_content_options' ? ' active_tab' : '' ) .'" id="property_content_options"><h3>' . esc_html__('Content options', 'wpresidence-core') . '</h3>';
        estate_prpg_design_option();
        print'</div>
                <div class="property_tab_item_content'. ( $active_tab === 'property_custom_template' ? ' active_tab' : '' ) .'" id="property_custom_template"><h3>' . esc_html__('Custom Template', 'wpresidence-core') . '</h3>';
        wpestate_property_custom_template_box();
        print'</div>
            </div>

        </div>';
    }

endif;

if (!function_exists('wpestate_floorplan_box')):
    /**
     * Renders the floor plan management interface
     * 
     * Provides UI for:
     * - Enabling/disabling floor plans
     * - Adding multiple floor plans
     * - Managing floor plan details including:
     *   - Title
     *   - Description
     *   - Size
     *   - Room count
     *   - Bathroom count
     *   - Price
     *   - Plan image
     *
     * Handles both new floor plans and editing existing ones.
     *
     * @global WP_Post $post Current post object
     */
      
    function wpestate_floorplan_box() {
        global $post;
        $plan_title = '';
        $plan_image = '';
        $plan_description = '';
        $plan_bath = $plan_rooms = $plan_size = $plan_price = '';
        $use_floor_plans = get_post_meta($post->ID, 'use_floor_plans', true);
        print '<p class="meta-options">
              <input type="hidden" name="use_floor_plans" value="0">
              <input type="checkbox" id="use_floor_plans" class="wpresidence-admin-checkbox" name="use_floor_plans" value="1"';
        if ($use_floor_plans == 1) {
            print ' checked="checked" ';
        }
        print' >
              <label for="use_floor_plans">' . esc_html__('Use Floor Plans', 'wpresidence-core') . '</label>
          </p>';

        print '<div id="plan_wrapper">';

        $plan_title_array = get_post_meta($post->ID, 'plan_title', true);
        $plan_desc_array = get_post_meta($post->ID, 'plan_description', true);
        $plan_image_array = get_post_meta($post->ID, 'plan_image', true);
        $plan_image_attach_array = get_post_meta($post->ID, 'plan_image_attach', true);
        $plan_size_array = get_post_meta($post->ID, 'plan_size', true);
        $plan_rooms_array = get_post_meta($post->ID, 'plan_rooms', true);
        $plan_bath_array = get_post_meta($post->ID, 'plan_bath', true);
        $plan_price_array = get_post_meta($post->ID, 'plan_price', true);

        if (is_array($plan_title_array)) {
            foreach ($plan_title_array as $key => $plan_name) {

                if (isset($plan_desc_array[$key])) {
                    $plan_desc = $plan_desc_array[$key];
                } else {
                    $plan_desc = '';
                }

                if (isset($plan_image_attach_array[$key])) {
                    $plan_image_attach = $plan_image_attach_array[$key];
                } else {
                    $plan_image_attach = '';
                }


                if (isset($plan_image_array[$key])) {
                    $plan_img = $plan_image_array[$key];
                } else {
                    $plan_img = '';
                }

                if (isset($plan_size_array[$key])) {
                    $plan_size = $plan_size_array[$key];
                } else {
                    $plan_size = '';
                }

                if (isset($plan_rooms_array[$key])) {
                    $plan_rooms = $plan_rooms_array[$key];
                } else {
                    $plan_rooms = '';
                }

                if (isset($plan_bath_array[$key])) {
                    $plan_bath = $plan_bath_array[$key];
                } else {
                    $plan_bath = '';
                }

                if (isset($plan_price_array[$key])) {
                    $plan_price = $plan_price_array[$key];
                } else {
                    $plan_price = '';
                }


                print '

            <div class="plan_row">
            <i class=" deleter_floor far fa-trash-alt"></i>

            <p class="meta-options floor_p">
                <label for="plan_title">' . esc_html__('Plan Title', 'wpresidence-core') . '</label><br />
                <input id="plan_title" type="text" size="36" name="plan_title[]" value="' . $plan_name . '" />
           </p>

            <p class="meta-options floor_p">
                <label for="plan_description">' . esc_html__('Plan Description', 'wpresidence-core') . '</label><br />
                <textarea class="plan_description" type="text" size="36" name="plan_description[]" >' . $plan_desc . '</textarea>
            </p>



            <p class="meta-options floor_p">
                <label for="plan_size">' . esc_html__('Plan Size', 'wpresidence-core') . '</label><br />
                <input id="plan_size" type="text" size="36" name="plan_size[]" value="' . $plan_size . '" />
            </p>

            <p class="meta-options floor_p">
                <label for="plan_rooms">' . esc_html__('Plan Rooms', 'wpresidence-core') . '</label><br />
                <input id="plan_rooms" type="text" size="36" name="plan_rooms[]" value="' . $plan_rooms . '" />
            </p>

            <p class="meta-options floor_p">
                <label for="plan_bath">' . esc_html__('Plan Bathrooms', 'wpresidence-core') . '</label><br />
                <input id="plan_bath" type="text" size="36" name="plan_bath[]" value="' . $plan_bath . '" />
            </p>

            <p class="meta-options floor_p">
                <label for="plan_price">' . esc_html__('Plan Price', 'wpresidence-core') . '</label><br />
                <input id="plan_price" type="text" size="36" name="plan_price[]" value="' . $plan_price . '" />
            </p>


            <p class="meta-options floor_p image_plan">
                <label for="plan_image">' . esc_html__('Plan Image', 'wpresidence-core') . '</label><br />
                <input id="plan_image" type="text" size="36" name="plan_image[]" value="' . $plan_img . '" /> '
                        . '<input type="hidden" id="plan_image_attach" name="plan_image_attach[]" value="' . $plan_image_attach . '"/>
                <input id="plan_image_button" type="button"   size="40" class="upload_button button floorbuttons" value="' . esc_html__('Upload Image', 'wpresidence-core') . '" />


            </p>
            </div>';
            }
        }




        print '
    </div>
    <span id="add_new_plan">' . esc_html__('Add new plan', 'wpresidence-core') . '</span>
    ';
    }
endif;







/**
 * Property Metabox Field Functions
 *
 * Contains functions for rendering specific sections of the property metabox:
 * - Custom fields management
 * - Payment status display
 * - Google Maps integration
 * 
 * These functions handle the detailed implementation of each metabox section,
 * including form field generation and data handling.
 *
 * @package WpResidence
 * @subpackage MetaboxFields
 * @since 1.0
 */

if (!function_exists('wpestate_custom_details_box')):
    /**
     * Renders custom fields section in property metabox
     * 
     * Displays configured custom fields for the property, iterating through
     * field definitions from theme options and rendering appropriate inputs.
     *
     * @global WP_Post $post Current post object
     */
    function wpestate_custom_details_box() {
        global $post;
        $i = 0;
        $custom_fields = wpresidence_get_option('wp_estate_custom_fields', '');
        if (!empty($custom_fields)) {
            while ($i < count($custom_fields)) {
                if ($custom_fields[$i][0] != '') {
                    $name = $custom_fields[$i][0];
                    $label = $custom_fields[$i][1];
                    $type = $custom_fields[$i][2];
                    $order = $custom_fields[$i][3];
                    $dropdown_values = $custom_fields[$i][4];

                    $slug = wpestate_limit45(sanitize_title($name));
                    $slug = sanitize_key($slug);
                    $post_id = $post->ID;
                    $show = 1;
                    print ' <div class="property_prop_half">   ';
                    wpestate_show_custom_field($show, $slug, $name, $label, $type, $order, $dropdown_values, $post_id);
                    print '</div>';
                }
                $i++;
            }
        }
        print '<div style="clear:both"></div>';
    }


endif;






if (!function_exists('wpestate_show_custom_field')):
    /**
     * Generates HTML for a single custom field
     * 
     * Creates appropriate form field based on field type:
     * - Long text: textarea
     * - Short text: text input
     * - Numeric: number input
     * - Date: date picker
     * - Dropdown: select with options
     *
     * Handles value retrieval and formatting for each type.
     *
     * @param int $show Whether to output or return HTML
     * @param string $slug Field identifier
     * @param string $name Field name
     * @param string $label Field label
     * @param string $type Field type
     * @param int $order Display order
     * @param string $dropdown_values Comma-separated dropdown options
     * @param int $post_id Post ID to get value from
     * @param string $value Optional override value
     * @return string|void HTML string if $show=0, otherwise outputs directly
     */
  
    function wpestate_show_custom_field($show, $slug, $name, $label, $type, $order, $dropdown_values, $post_id, $value = '') {

        // get value
        if ($value == '') {
            $value = esc_html(get_post_meta($post_id, $slug, true));
            if ($type == 'numeric') {

                $value = (get_post_meta($post_id, $slug, true));
                if ($value !== '') {
                    $value = floatval($value);
                }
            } else {
                $value = esc_html(get_post_meta($post_id, $slug, true));
            }
        }





        $template = '';
        if ($type == 'long text' || $type == 'textarea'  || $type == 'wysiwyg') {
            $template .= '<label for="' . $slug . '">' . $label . ' ' . esc_html__('(*text)', 'wpresidence-core') . ' </label>';
            $template .= '<textarea type="text" id="' . $slug . '"  size="0" name="' . $slug . '" rows="3" cols="42">' . $value . '</textarea>';
        } else if ($type == 'short text' || $type == 'text') {
            $template .= '<label for="' . $slug . '">' . $label . ' ' . esc_html__('(*text)', 'wpresidence-core') . ' </label>';
            $template .= '<input type="text" id="' . $slug . '" size="40" name="' . $slug . '" value="' . $value . '">';
        } else if ($type == 'numeric' || $type=='number' ) {
            $template .= '<label for="' . $slug . '">' . $label . ' ' . esc_html__('(*numeric)', 'wpresidence-core') . ' </label>';
            $template .= '<input type="text" id="' . $slug . '" size="40" name="' . $slug . '" value="' . $value . '">';
        } else if ($type == 'date') {
            $template .= '<label for="' . $slug . '">' . $label . ' ' . esc_html__('(*date)', 'wpresidence-core') . ' </label>';
            $template .= '<input type="text" id="' . $slug . '" size="40" name="' . $slug . '" value="' . $value . '">';
            $template .= wpestate_date_picker_translation_return($slug);
       } else if ($type == 'select') {
  
            // Get ACF field object to access choices
            $field_object = acf_get_field($slug);



            if ($field_object && isset($field_object['choices'])) {
                $template .= '<label for="' . $slug . '">' . $label . ' </label>';
                $template .= '<select id="' . $slug . '"  name="' . $slug . '" >';
                
                // Add default "Not Available" option
                $template .= '<option value="">' . esc_html__('Not Available', 'wpresidence-core') . '</option>';
                
                // Loop through ACF field choices
                foreach ($field_object['choices'] as $choice_value => $choice_label) {
                    $template .= '<option value="' . esc_attr($choice_value) . '"';
                    
                    // Check if this option is selected
                    if ($value == $choice_value) {
                        $template .= ' selected ';
                    }
                    
                    // Apply WPML translation if available
                    if (function_exists('icl_translate')) {
                        $choice_label = apply_filters('wpml_translate_single_string', $choice_label, 'custom field value', 'custom_field_value' . $choice_label);
                    }
                    
                    $template .= '>' . esc_html($choice_label) . '</option>';
                }
                
                $template .= '</select>';
            } else {
                // Fallback if ACF field object not found
                $template .= '<label for="' . $slug . '">' . $label . ' </label>';
                $template .= '<input type="text" id="' . $slug . '" size="40" name="' . $slug . '" value="' . $value . '">';
            }
        
        } else if ($type == 'dropdown' ) {
            $dropdown_values_array = explode(',', $dropdown_values);

            $template .= '<label for="' . $slug . '">' . $label . ' </label>';
            $template .= '<select id="' . $slug . '"  name="' . $slug . '" >';

            array_unshift($dropdown_values_array, esc_html__('Not Available', 'wpresidence-core'));

            foreach ($dropdown_values_array as $key => $value_drop) {
                $template .= '<option value="' . trim($value_drop) . '"';
                if (trim(htmlspecialchars_decode($value)) === trim(htmlspecialchars_decode($value_drop))) {

                    $template .= ' selected ';
                }
                if (function_exists('icl_translate')) {
                    $value_drop = apply_filters('wpml_translate_single_string', $value_drop, 'custom field value', 'custom_field_value' . $value_drop);
                }

                $template .= '>' . trim($value_drop) . '</option>';
            }
            $template .= '</select>';
        }

        if ($show == 1) {
            print $template;
        } else {
            return $template;
        }
    }


endif;

if (!function_exists('wpestate_estate_paid_submission')):
    /**
     * Displays property payment status information
     * 
     * Shows payment-related details based on site configuration:
     * - Payment disabled notice
     * - Membership status
     * - Per listing payment status
     *
     * @global WP_Post $post Current post object
     */
    
     function wpestate_estate_paid_submission() {
        global $post;
        print ' <div class="property_prop_half">   ';
        $paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));
        if ($paid_submission_status == 'no') {
            esc_html_e('Paid Submission is disabled', 'wpresidence-core');
        }
        if ($paid_submission_status == 'membership') {
            esc_html_e('You are on membership mode. There are no details to show for this mode.', 'wpresidence-core');
        }
        if ($paid_submission_status == 'per listing') {
            esc_html_e('Pay Status: ', 'wpresidence-core');
            $pay_status = get_post_meta($post->ID, 'pay_status', true);
            if ($pay_status == 'paid') {
                esc_html_e('PAID', 'wpresidence-core');
            } else {
                esc_html_e('Not Paid', 'wpresidence-core');
            }
        }
        print'</div>';
    }
   
endif;

if (!function_exists('wpestate_map_estate_box')):
    /**
     * Renders Google Maps integration fields
     * 
     * Creates form fields for:
     * - Property address
     * - Map coordinates
     * - Street view settings
     * - Map display options
     * 
     * Includes interactive map for pin placement.
     *
     * @global WP_Post $post Current post object
     */
   
    function wpestate_map_estate_box() {
        wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');
        global $post;

        $mypost = $post->ID;
        $gmap_lat = floatval(get_post_meta($mypost, 'property_latitude', true));
        $gmap_long = floatval(get_post_meta($mypost, 'property_longitude', true));
        $google_camera_angle = intval(esc_html(get_post_meta($mypost, 'google_camera_angle', true)));
        $cache_array = array('yes', 'no');
        $keep_min_symbol = '';
        $keep_min_status = esc_html(get_post_meta($post->ID, 'keep_min', true));

        foreach ($cache_array as $value) {
            $keep_min_symbol .= '<option value="' . $value . '"';
            if ($keep_min_status == $value) {
                $keep_min_symbol .= ' selected="selected" ';
            }
            $keep_min_symbol .= '>' . $value . '</option>';
        }


        $page_custom_zoom = get_post_meta($mypost, 'page_custom_zoom', true);
        if ($page_custom_zoom == '') {
            $page_custom_zoom = 16;
        }

        wpestate_date_picker_translation('property_date');
        print'
    <p class="meta-options">
    <div id="googleMap" style="width:100%;height:380px;margin-bottom:30px;"></div>

    <p class="meta-options">
        <a class="button" href="#" id="admin_place_pin">' . esc_html__('Place Pin with Property Address', 'wpresidence-core') . '</a>
    </p>

    <div class="property_prop">
        <label for="property_address">' . esc_html__('Address (*only street name and building no): ', 'wpresidence-core') . '</label><br />
        <input type="text" type="text" id="property_address"  size="40" name="property_address" value="' . esc_html(get_post_meta($mypost, 'property_address', true)) . '" >
    </div>

    <div class="property_prop_half">
        <label for="property_zip">' . esc_html__('Zip: ', 'wpresidence-core') . '</label><br />
        <input type="text" id="property_zip" size="40" name="property_zip" value="' . esc_html(get_post_meta($mypost, 'property_zip', true)) . '">
    </div>

    <div class="property_prop_half">
        <label for="property_country">' . esc_html__('Country: ', 'wpresidence-core') . '</label><br />';
        print wpestate_country_list(esc_html(get_post_meta($mypost, 'property_country', true)));
        print '</div>





    <div class="property_prop_half">
        <label for="embed_video_id">' . esc_html__('Latitude:', 'wpresidence-core') . '</label> <br />
        <input type="text" id="property_latitude" style="margin-right:20px;" size="40" name="property_latitude" value="' . $gmap_lat . '">
    </div>

    <div class="property_prop_half">
        <label for="embed_video_id">' . esc_html__('Longitude:', 'wpresidence-core') . '</label> <br />
        <input type="text" id="property_longitude" style="margin-right:20px;" size="40" name="property_longitude" value="' . $gmap_long . '">
    </div>

   <div class="property_prop_half">
       <label for="page_custom_zoom">' . esc_html__('Zoom Level for map (1-20)', 'wpresidence-core') . '</label><br />
       <select name="page_custom_zoom" id="page_custom_zoom">';

        for ($i = 1; $i < 21; $i++) {
            print '<option value="' . $i . '"';
            if ($page_custom_zoom == $i) {
                print ' selected="selected" ';
            }
            print '>' . $i . '</option>';
        }

        print'
        </select>
    </div>


    <div class="property_prop_half">
        <label for="google_camera_angle" >' . esc_html__('Google View Camera Angle', 'wpresidence-core') . '</label>
        <input type="text" id="google_camera_angle" style="margin-right:0px;" size="5" name="google_camera_angle" value="' . $google_camera_angle . '">
    </div>


    <div class="property_prop_half" style="padding-top:20px;">
        <input type="hidden" name="property_google_view" value="">
        <input type="checkbox"  class="wpresidence-admin-checkbox" id="property_google_view" name="property_google_view" value="1" ';
        if (esc_html(get_post_meta($mypost, 'property_google_view', true)) == 1) {
            print'checked="checked"';
        }
        print' />
        <label class="checklabel" for="property_google_view">' . esc_html__('Enable Google Street View', 'wpresidence-core') . '</label>
    </div>
    
    <div class="property_prop_half" style="padding-top:20px;">
        <input type="hidden" name="property_hide_map_marker" value="">
        <input type="checkbox" class="wpresidence-admin-checkbox" id="property_hide_map_marker" name="property_hide_map_marker" value="1" ';
        if (esc_html(get_post_meta($mypost, 'property_hide_map_marker', true)) == 1) {
            print'checked="checked"';
        }
        print' />
        <label class="checklabel" for="property_hide_map_marker">' . esc_html__('Hide Map Marker ?', 'wpresidence-core') . '</label>
            
    </div>
    ';
    }



endif;






/**
 * Property Agent and Media Management
 *
 * Handles two major property management aspects:
 * 1. Agent/user associations with properties including:
 *    - Main agent selection
 *    - Secondary agents management
 *    - User assignment
 * 2. Property media management including:
 *    - Image and PDF uploads
 *    - Video embedding
 *    - Virtual tour integration
 *
 * @package WpResidence
 * @subpackage PropertyMedia
 * @since 1.0
 */

if (!function_exists('wpestate_agentestate_box')):
    /**
     * Renders the agent association interface
     * 
     * Creates form fields for:
     * - Primary agent selection (from agents, agencies, developers)
     * - Secondary agents selection (multiple agents possible)
     * - Property user assignment
     *
     * @global WP_Post $post Current post object
     * @uses WP_Query For agent/agency selection
     */
   

    function wpestate_agentestate_box() {
        global $post;
        wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');

        $mypost = $post->ID;
        $originalpost = $post;
        $agent_list = '';
        $agent_list_sec = '';
        $picked_agent = get_post_meta($mypost, 'property_agent', true);
        $agents_secondary = get_post_meta($mypost, 'property_agent_secondary', true);

        $args = array(
            'post_type' => array('estate_agent', 'estate_agency', 'estate_developer'),
            'post_status' => 'publish',
            'posts_per_page' => 150,
            'orderby' => 'title',
            'order' => 'ASC'
        );

        $agent_selection = new WP_Query($args);

        while ($agent_selection->have_posts()) {
            $agent_selection->the_post();
            $the_id = get_the_ID();

            $agent_list .= '<option value="' . $the_id . '"  ';
            if ($the_id == $picked_agent) {
                $agent_list .= ' selected="selected" ';
            }
            $agent_list .= '>' . get_the_title() . '</option>';
        }

        wp_reset_postdata();

        $args2 = array(
            'post_type' => array('estate_agent'),
            'post_status' => 'publish',
            'posts_per_page' => 150,
            'orderby' => 'title',
            'order' => 'ASC'
        );

        $agent_selection2 = new WP_Query($args2);
        while ($agent_selection2->have_posts()) {
            $agent_selection2->the_post();
            $the_id = get_the_ID();

            $agent_list_sec .= '<option value="' . $the_id . '"  ';
            if (is_array($agents_secondary) && in_array($the_id, $agents_secondary)) {
                $agent_list_sec .= ' selected="selected" ';
            }
            $agent_list_sec .= '>' . get_the_title() . '</option>';
        }
        wp_reset_postdata();

        $post = $originalpost;

        print '
        <div class="property_prop_half">
        <label for="property_agent">' . esc_html__('Main Agent: ', 'wpresidence-core') . '</label><br />
        <select id="property_agent"  name="property_agent">
            <option value="">none</option>
            <option value=""></option>
            ' . $agent_list . '
        </select>
        </div>';

        $originalpost = $post;
        $blog_list = '';
        $original_user = wpsestate_get_author();

        $blogusers = get_users( array( 'orderby' => 'nicename', 'role__in' => array_keys( wpresidence_rolemap() ) ) );

        foreach ($blogusers as $user) {

            $the_id = $user->ID;
            $blog_list .= '<option value="' . $the_id . '"  ';
            if ($the_id == $original_user) {
                $blog_list .= ' selected="selected" ';
            }
            $blog_list .= '>' . $user->user_login . '</option>';
        }




        print '
    <div class="property_prop_half">
        <label for="property_user">' . esc_html__('User: ', 'wpresidence-core') . '</label><br />
        <select id="property_user" name="property_user">
            <option value=""></option>
            <option value="1">admin</option>
            ' . $blog_list . '
        </select>
      </div>';

        print '
        <div class="property_prop_half">
        <label for="property_agent_secondary">' . esc_html__('Secondary Agents(*multiple selection): ', 'wpresidence-core') . '</label><br />
        <select id="property_agent_secondary" style="height:250px" multiple="multiple" name="property_agent_secondary[]">
            <option value="">none</option>
            <option value=""></option>
            ' . $agent_list_sec . '
        </select>
        </div>';
    }


endif;





if (!function_exists('wpestate_property_add_media')):
    /**
     * Renders the property media management interface
     * 
     * Provides UI for:
     * - Image/PDF upload and management
     * - Video embedding configuration
     * - Virtual tour integration
     * 
     * Handles multiple file types and provides preview/edit functionality.
     *
     * @global WP_Post $post Current post object
     */
  
    function wpestate_property_add_media() {


        global $post;
        $already_in = '';
  

        print '<div class="property_uploaded_thumb_wrapepr" id="property_uploaded_thumb_wrapepr">';
        $ajax_nonce = wp_create_nonce("wpestate_attach_delete");
        print'<input type="hidden" id="wpestate_attach_delete" value="' . esc_html($ajax_nonce) . '" />    ';

        $post_attachments_new = wpestate_generate_property_slider_image_ids($post->ID);


        foreach ($post_attachments_new as $attachment_id) {
            $attachment = get_post($attachment_id);
    
            if ($attachment && ($attachment->post_mime_type == 'image/jpeg' ||
                    $attachment->post_mime_type == 'application/pdf' ||
                    $attachment->post_mime_type == 'image/webp' ||
                    $attachment->post_mime_type == 'image/png')) {
    
                print '<div class="uploaded_thumb" data-imageid="' . $attachment_id . '">';
    
                if ($attachment->post_mime_type == 'application/pdf') {
                    print ' <img src="' . get_theme_file_uri('/img/pdf.png') . '" alt="' . esc_html__('user document', 'wpresidence-core') . '" />';
                } else {
                    $preview = wp_get_attachment_image_src($attachment_id, 'thumbnail');
                    print '<img src="' . $preview[0] . '" alt="slider" />';
                }
    
                $already_in .= $attachment_id . ',';
                print '<a target="_blank" href="' . esc_url(admin_url()) . 'post.php?post=' . $attachment_id . '&action=edit" class="attach_edit"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                <span class="attach_delete"><i class="far fa-trash-alt" aria-hidden="true"></i></span>';
    
                print '</div>';
            }
        }


        print '<input type="hidden" id="image_to_attach" name="image_to_attach" value="' . $already_in . '"/>';

        print '</div>';

        print '<button class="upload_button button" id="button_new_image" data-postid="' . $post->ID . '">' . esc_html__('Upload new file (Image or pdf)', 'wpresidence-core') . '</button>';

        $mypost = $post->ID;
        $option_video = '';
        $video_values = array('vimeo', 'youtube', 'tiktok');
        $video_type = get_post_meta($mypost, 'embed_video_type', true);
        $property_custom_video = get_post_meta($mypost, 'property_custom_video', true);

        foreach ($video_values as $value) {
            $option_video .= '<option value="' . $value . '"';
            if ($value == $video_type) {
                $option_video .= 'selected="selected"';
            }
            $option_video .= '>' . $value . '</option>';
        }




        print'
    <div class="property_prop_half" style="clear: both;">
        <label for="embed_video_id">' . esc_html__('Video From: ', 'wpresidence-core') . '</label> <br />
         <select id="embed_video_type" name="embed_video_type" >
                ' . $option_video . '
        </select>
    </div>


    <div class="property_prop_half">
        <label for="embed_video_id">' . esc_html__('Embed Video id: ', 'wpresidence-core') . '</label> <br />
        <input type="text" id="embed_video_id" name="embed_video_id" size="40" value="' . esc_html(get_post_meta($mypost, 'embed_video_id', true)) . '">
    </div>';

        print'<div class="property_prop_half prop_full">
            <label for="property_custom_video_button">' . esc_html__('Video Placeholder Image', 'wpresidence-core') . '</label><br />
            <input id="property_custom_video" type="text" size="36" class="wpestate_landing_upload" name="property_custom_video" value="' . esc_url($property_custom_video) . '" />
            <input id="property_custom_video_button" type="button"   size="40" class="upload_button button" value="' . esc_html__('Upload Image', 'wpresidence-core') . '" />
        </div>';

        print'
    <div class="property_prop_half">
        <label for="embed_video_type">' . esc_html__('Virtual Tour / Meta Reels ', 'wpresidence-core') . '</label><br />
        <textarea id="embed_virtual_tour" name="embed_virtual_tour">' . ( get_post_meta($mypost, 'embed_virtual_tour', true) ) . '</textarea>
    </div>';
    }


endif;








/**
 * Property Details Management Functions
 *
 * Provides functions for managing core property information including:
 * - Basic property details (price, size, rooms, etc)
 * - Energy efficiency data and certifications 
 * - Property subunit relationships and management
 *
 * These functions handle the rendering of form fields in the property
 * edit interface and manage associated metadata.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 1.0
 */









if (!function_exists('wpestate_estate_box')):
    /**
     * Renders the main property details form section
     * 
     * Creates form fields for core property information including:
     * - Price and pricing labels
     * - Property dimensions
     * - Room counts
     * - Tax and HOA fees
     * - Featured status
     * - Theme slider inclusion
     *
     * @global WP_Post $post Current post object
     */
   
     function wpestate_estate_box() {
        global $post;
        wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');
        $mypost = $post->ID;

        $price_options = array('global','no','yes');
        $show_price_select = '';
        $show_price = get_post_meta($post->ID, 'local_show_hide_price', true);

        foreach ($price_options as $key => $value) {
            $show_price_select .= '<option value="' . $value . '"';
            if ($value == $show_price) {
                $show_price_select .= ' selected="selected"';
            }
            $show_price_select .= '>' . $value . '</option>';
        }

        print'
    <div class="property_prop_half">
        <label for="property_price">' . esc_html__('Price (use only numbers): ', 'wpresidence-core') . '</label><br />
        <input type="number" step="any"  id="property_price" size="40" name="property_price" value="' . esc_html(get_post_meta($mypost, 'property_price', true)) . '">
    </div>
    <div class="property_prop_half"> 
        <label for="local_show_hide_price">'.esc_html__('Hide/Show Price ','wpresidence-core').' </label><br />
        <select id="local_show_hide_price" name="local_show_hide_price" >
            '.$show_price_select.'
        </select>
    </div>
    <div class="property_prop_half">
        <label for="property_label_before">' . esc_html__('Before Price Label (*for example "per month"): ', 'wpresidence-core') . '</label><br />
        <input type="text" id="property_label_before" size="40" name="property_label_before" value="' . esc_html(get_post_meta($mypost, 'property_label_before', true)) . '">
    </div>

    <div class="property_prop_half">
        <label for="property_label">' . esc_html__('After Price Label (*for example "per month"): ', 'wpresidence-core') . '</label><br />
        <input type="text" id="property_label" size="40" name="property_label" value="' . esc_html(get_post_meta($mypost, 'property_label', true)) . '">
    </div>

    <div class="property_prop_half">
        <label for="property_second_price">' . esc_html__('Additional Price Info (use only numbers):', 'wpresidence-core') . '</label><br />
        <input type="number" step="any"  id="property_second_price" size="40" name="property_second_price" value="' . esc_html(get_post_meta($mypost, 'property_second_price', true)) . '">
    </div>
    
    <div class="property_prop_half">
        <label for="property_label_before_second_price">' . esc_html__('Before Label for Additional Price Info (*for example "from"): ', 'wpresidence-core') . '</label><br />
        <input type="text" id="property_label_before_second_price" size="40" name="property_label_before_second_price" value="' . esc_html(get_post_meta($mypost, 'property_label_before_second_price', true)) . '">
    </div>

     <div class="property_prop_half">
        <label for="property_second_price_label">' . esc_html__('After Label for Additional Price info (*for example "per month"): ', 'wpresidence-core') . '</label><br />
        <input type="text" id="property_second_price_label" size="40" name="property_second_price_label" value="' . esc_html(get_post_meta($mypost, 'property_second_price_label', true)) . '">
    </div>   

    <div class="property_prop_half">
        <label for="property_year_tax">' . esc_html__('Yearly Tax Rate:', 'wpresidence-core') . '</label><br />
        <input type="text" id="property_year_tax" size="40" name="property_year_tax" value="' . floatval(get_post_meta($mypost, 'property_year_tax', true)) . '">
    </div>

    <div class="property_prop_half">
        <label for="property_hoa">' . esc_html__('Homeowners Association Fee (monthly): ', 'wpresidence-core') . '</label><br />
        <input type="text" id="property_hoa" size="40" name="property_hoa" value="' . floatval(get_post_meta($mypost, 'property_hoa', true)) . '">
    </div>

 
    <div class="property_prop_half">
        <label for="property_size">' . esc_html__('Size (*only numbers): ', 'wpresidence-core') . '</label><br />
        <input type="number" step="any" id="property_size" size="40" name="property_size" value="' . floatval(get_post_meta($mypost, 'property_size', true)) . '">
    </div>

    <div class="property_prop_half">
        <label for="property_lot_size">' . esc_html__('Lot Size (*only numbers): ', 'wpresidence-core') . '</label><br />
        <input type="number" step="any"  id="property_lot_size" size="40" name="property_lot_size" value="' . floatval(get_post_meta($mypost, 'property_lot_size', true)) . '">
    </div>

    <div class="property_prop_half">
        <label for="property_rooms">' . esc_html__('Rooms (*only numbers): ', 'wpresidence-core') . '</label><br />
        <input type="number" step="any"  id="property_rooms" size="40" name="property_rooms" value="' . floatval(get_post_meta($mypost, 'property_rooms', true)) . '">
    </div>

    <div class="property_prop_half">
        <label for="property_bedrooms">' . esc_html__('Bedrooms (*only numbers): ', 'wpresidence-core') . '</label><br />
        <input type="number" step="any"  id="property_bedrooms" size="40" name="property_bedrooms" value="' . floatval(get_post_meta($mypost, 'property_bedrooms', true)) . '">
    </div>

    <div class="property_prop_half">
        <label for="property_bedrooms">' . esc_html__('Bathrooms (*only numbers): ', 'wpresidence-core') . '</label><br />
        <input type="number" step="any"  id="property_bathrooms" size="40" name="property_bathrooms" value="' . floatval(get_post_meta($mypost, 'property_bathrooms', true)) . '">
    </div>

    <div class="property_prop_half prop_half">
        <label for="owner_notes">' . esc_html__('Owner/Agent notes (*not visible on front end): ', 'wpresidence-core') . '</label> <br />
        <textarea id="owner_notes" name="owner_notes" >' . esc_html(get_post_meta($mypost, 'owner_notes', true)) . '</textarea>
    </div>';

    print '<div class="property_prop_half prop_half">
        <label for="property_internal_id">' . esc_html__('Listing ID: ', 'wpresidence-core') . '</label><br />
        <input type="text" id="property_internal_id" name="property_internal_id" value="' . get_post_meta($mypost, 'property_internal_id', true) . '">
    </div>';

        print'

     <div class="property_prop_half" style="padding-top:20px;">
            <input type="hidden" name="prop_featured" value="0">
            <input type="checkbox" class="wpresidence-admin-checkbox"  id="prop_featured" name="prop_featured" value="1" ';
        if (intval(get_post_meta($mypost, 'prop_featured', true)) == 1) {
            print'checked="checked"';
        }
        print' />
            <label class="checklabel" for="prop_featured">' . esc_html__('Make it a Featured Property', 'wpresidence-core') . '</label>
    </div>  ';

        $theme_slider = wpresidence_get_option('wp_estate_theme_slider', '');
        print '
    <div class="property_prop_half" style="padding-top:20px;">
        <input type="hidden" name="property_theme_slider" value="0">
        <input type="checkbox" class="wpresidence-admin-checkbox"  id="property_theme_slider" name="property_theme_slider" value="1" ';

        if (is_array($theme_slider) && in_array($mypost, $theme_slider)) {
            print'checked="checked"';
        }
        print' />
        <label class="checklabel" for="property_theme_slider">' . esc_html__('Add Property to Theme Slider', 'wpresidence-core') . '</label>
    </div>





    ';
    }




endif;






if (!function_exists('wpestate_energy_box')):
    /**
     * Renders the energy efficiency data form section
     * 
     * Creates form fields for energy-related information including:
     * - Energy index and class
     * - CO2 emissions data
     * - Renewable energy metrics
     * - Building energy performance
     * - EPC ratings
     *
     * @global WP_Post $post Current post object
     */

     function wpestate_energy_box() {
        global $post;
        wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');
        $mypost = $post->ID;

        print' <div class="property_prop_half">
        <label for="energy_index">' . esc_html__('Energy Index in kWh/m2a: ', 'wpresidence-core') . '</label><br />
        <input type="text" id="energy_index" size="40" name="energy_index" value="' . esc_html(get_post_meta($mypost, 'energy_index', true)) . '">
    </div>';

        print'
    <div class="property_prop_half">
        <label for="energy_class">' . esc_html__('Energy Class:', 'wpresidence-core') . '</label><br />
            <select name="energy_class" id="energy_class">
                <option value="">' . esc_html__('Select Energy Class (EU regulation)', 'wpresidence-core');

        $wpestate_submission_page_fields = wpresidence_get_option('wpestate_energy_section_possible_grades', '');
        $energy_class_array = explode(",", $wpestate_submission_page_fields);

        foreach ($energy_class_array as $single_class) {
            print '<option value="' . $single_class . '" ' . ( get_post_meta($mypost, 'energy_class', true) == $single_class ? ' selected ' : '' ) . ' >' . $single_class;
        }
        print'
            </select>
    </div>';

        print' <div class="property_prop_half">
        <label for="co2_index">' . esc_html__('Greenhouse gas emissions kgCO2/m2a:', 'wpresidence-core') . '</label><br />
        <input type="text" id="co2_index" size="40" name="co2_index" value="' . esc_html(get_post_meta($mypost, 'co2_index', true)) . '">
    </div>';

        print'
    <div class="property_prop_half">
        <label for="co2_class">' . esc_html__('Greenhouse gas emissions index class', 'wpresidence-core') . '</label><br />
            <select name="co2_class" id="co2_class">
                <option value="">' . esc_html__('Select gas emissions index class', 'wpresidence-core');

        $wpestate_submission_page_fields = wpresidence_get_option('wpestate_co2_section_possible_grades', '');
        $energy_class_array = explode(",", $wpestate_submission_page_fields);

        foreach ($energy_class_array as $single_class) {
            print '<option value="' . $single_class . '" ' . ( get_post_meta($mypost, 'co2_class', true) == $single_class ? ' selected ' : '' ) . ' >' . $single_class;
        }
        print'
            </select>
    </div>';

        print' <div class="property_prop_half">
        <label for="renew_energy_index">' . esc_html__('Renewable energy performance index', 'wpresidence-core') . '</label><br />
        <input type="text" id="renew_energy_index" size="40" name="renew_energy_index" value="' . esc_html(get_post_meta($mypost, 'renew_energy_index', true)) . '">
    </div>';

        print' <div class="property_prop_half">
        <label for="building_energy_index">' . esc_html__('Energy performance of the building', 'wpresidence-core') . '</label><br />
        <input type="text" id="building_energy_index" size="40" name="building_energy_index" value="' . esc_html(get_post_meta($mypost, 'building_energy_index', true)) . '">
    </div>';

        print' <div class="property_prop_half">
        <label for="epc_current_rating">' . esc_html__('EPC current rating', 'wpresidence-core') . '</label><br />
        <input type="text" id="epc_current_rating" size="40" name="epc_current_rating" value="' . esc_html(get_post_meta($mypost, 'epc_current_rating', true)) . '">
    </div>';

        print' <div class="property_prop_half">
        <label for="epc_potential_rating">' . esc_html__('EPC Potential Rating', 'wpresidence-core') . '</label><br />
        <input type="text" id="epc_potential_rating" size="40" name="epc_potential_rating" value="' . esc_html(get_post_meta($mypost, 'epc_potential_rating', true)) . '">
    </div>';
    }

endif;










if (!function_exists('wpestate_propery_subunits')):
    /**
     * Renders the property subunit management interface
     * 
     * Provides functionality for:
     * - Enabling/disabling subunit support
     * - Selecting subunits from existing properties
     * - Manually entering subunit IDs
     * - Displaying parent unit relationship
     *
     * Handles the hierarchical relationship between properties and their subunits.
     *
     * @global WP_Post $post Current post object
     */
  

     function wpestate_propery_subunits() {
        global $post;
        wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');

        $mypost = $post->ID;
        print'
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td width="100%" valign="top" align="left">
            <p class="meta-options">';

        $property_subunits_master = intval(get_post_meta($mypost, 'property_subunits_master', true));

        if ($property_subunits_master != 0 && $property_subunits_master != $post->ID) {
            print '<span>' . esc_html__('Already Subunit for', 'wpresidence-core') . ' <a href="' . esc_url(get_permalink($property_subunits_master)) . '" target="_blank">' . get_the_title($property_subunits_master) . '</a></span></br></br>';
        }
        print'
            <input type="hidden" name="property_has_subunits" value="">
            <input type="checkbox"  id="property_has_subunits" class="wpresidence-admin-checkbox" name="property_has_subunits" value="1" ';
        if (intval(get_post_meta($mypost, 'property_has_subunits', true)) == 1) {
            print'checked="checked"';
        }
        print' />
            <label class="checklabel" for="property_has_subunits">' . esc_html__('Enable ', 'wpresidence-core') . '</label>
            </p>
        </td>
    </tr>
    <tr>
        <td width="100%" valign="top" align="left">
            <p class="meta-options">';

        print'<span>' . esc_html__('Due to speed & usability reasons we only show your first 50 properties. If the Listing you want to add as subunit is not in this list please add the id manually.', 'wpresidence-core') . '</span>
            <label for="property_subunits_list">' . esc_html__('Select Subunits From the list: ', 'wpresidence-core') . '</label><br />';
        // <input type="text" id="property_subunits_list" size="40" name="property_subunits_list" value="' . esc_html(get_post_meta($mypost, 'property_subunits_list', true)) . '">
        $property_subunits_list = get_post_meta($mypost, 'property_subunits_list', true);

        $post__not_in = array();
        $post__not_in[] = $mypost;
        $args = array(
            'post_type' => 'estate_property',
            'post_status' => 'publish',
            'nopaging' => 'true',
            'cache_results' => false,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'post__not_in' => $post__not_in,
        );

        $recent_posts = new WP_Query($args);
        print '<select name="property_subunits_list[]"  style="height:350px;" id="property_subunits_list"  multiple="multiple">';
        while ($recent_posts->have_posts()): $recent_posts->the_post();
            $theid = get_the_ID();
            print '<option value="' . $theid . '" ';
            if (is_array($property_subunits_list) && in_array($theid, $property_subunits_list)) {
                print ' selected="selected" ';
            }
            print'>' . get_the_title() . '</option>';
        endwhile;
        wp_reset_postdata();
        $recent_posts->reset_postdata();
        $post->ID = $mypost;
        print '</select>';
        print'
            </p>
        </td>
    </tr>
    <tr>

        <td width="100%" valign="top" align="left">
            <p class="meta-options">
            <label for="property_subunits_list_manual">' . esc_html__('Or add the ids separated by comma. ', 'wpresidence-core') . '</label><br />
            <textarea id="property_subunits_list_manual" size="40" name="property_subunits_list_manual" >' . esc_html(get_post_meta($mypost, 'property_subunits_list_manual', true)) . '</textarea>
            </p>
        </td>
    </tr>
    </table>
    ';
    }

endif;

if ( ! function_exists( 'wpestate_property_custom_template_box' ) ) :
    function wpestate_property_custom_template_box() {
        global $post;
        $pages    = wpestate_property_page_template_function();
        $options  = '<option value="">' . esc_html__( 'default', 'wpresidence-core' ) . '</option>';
        $selected = get_post_meta( $post->ID, 'property_page_desing_local', true );

        foreach ( $pages as $page_id => $page_name ) {
            $options .= '<option value="' . $page_id . '"';
            if ( $selected == $page_id ) {
                $options .= ' selected="selected"';
            }
            $options .= ' >' . $page_name . '</option>';
        }

        print '<div class="property_prop_half">
            <label for="property_page_desing_local">' . esc_html__( 'Use a custom property page template', 'wpresidence-core' ) . '</label></br>
            <select id="global_property_page_template" name="property_page_desing_local" >'
            . $options .
            '</select>
        </div>';
    }
endif;
