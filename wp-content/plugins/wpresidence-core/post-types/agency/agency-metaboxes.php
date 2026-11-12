<?php 
/**
 * Agency Metaboxes
 *
 * This file handles the creation and management of custom metaboxes for the 'estate_agency' 
 * custom post type in the WPResidence real estate plugin. It defines the form fields for 
 * agency details in the WordPress admin and synchronizes agency post data with related 
 * user profile data when applicable.
 *
 * @package WPResidence
 * @subpackage CustomPostTypes
 * @version 1.0
 * @author WPResidence Team
 * @copyright Copyright (c) WPResidence
 * @license GPL2+
 */

////////////////////////////////////////////////////////////////////////////////////////////////
// Add agent metaboxes
////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Registers the agency settings metabox
 *
 * This function adds a custom metabox to the 'estate_agency' post type edit screen
 * that contains all agency-specific settings fields such as contact details,
 * social profiles, and location information.
 *
 * @since 1.0
 * @hook add_meta_box
 * @return void
 */
if( !function_exists('wpestate_add_agency_metaboxes') ):
    function wpestate_add_agency_metaboxes() {
        add_meta_box( 'estate_agency-sectionid', esc_html__( 'Agency Settings', 'wpresidence-core' ), 'estate_agency_tabbed_interface', 'estate_agency', 'normal', 'default' );
    }
    endif; // end   wpestate_add_agency_metaboxes  
    
    
    
    ////////////////////////////////////////////////////////////////////////////////////////////////
    // Agency details
    ////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Renders the agency settings metabox content
     *
     * This function outputs the HTML form fields for all agency settings including:
     * - Contact information (address, email, phone, mobile, Skype)
     * - Social media profiles (Facebook, Twitter, LinkedIn, Pinterest, Instagram)
     * - Business details (website, languages, license, opening hours, taxes)
     * - User account connection
     * - Map location with coordinates
     *
     * Each field is pre-populated with existing meta values when editing an agency.
     *
     * @since 1.0
     * @param WP_Post $post The current post object (agency being edited)
     * @return void Outputs HTML directly
     */
if( !function_exists('estate_agency_tabbed_interface') ):
function estate_agency_tabbed_interface() {
    global $post;
    wp_nonce_field( plugin_basename( __FILE__ ), 'estate_agency_noncename' );

    $available_tabs = array( 'agency_details', 'agency_contact', 'agency_location' );
    $active_tab = 'agency_details';
    if ( isset( $_GET['agency_tab'] ) && in_array( $_GET['agency_tab'], $available_tabs, true ) ) {
        $active_tab = sanitize_key( $_GET['agency_tab'] );
    }

    print '<div class="property_options_wrapper meta-options">'
        .'<div class="property_options_wrapper_list">';
            print '<div class="property_tab_item'.( $active_tab === 'agency_details' ? ' active_tab' : '' ).'" data-content="agency_details">'.esc_html__('Agency Details','wpresidence-core').'</div>';
            print '<div class="property_tab_item'.( $active_tab === 'agency_contact' ? ' active_tab' : '' ).'" data-content="agency_contact">'.esc_html__('Agency Contact & Social','wpresidence-core').'</div>';
            print '<div class="property_tab_item'.( $active_tab === 'agency_location' ? ' active_tab' : '' ).'" data-content="agency_location">'.esc_html__('Address & Map','wpresidence-core').'</div>';
    print '</div><div class="property_options_content_wrapper">';
            print '<div class="property_tab_item_content'.( $active_tab === 'agency_details' ? ' active_tab' : '' ).'" id="agency_details">';
                wpestate_agency_details_box( $post );
            print '</div>';
            print '<div class="property_tab_item_content'.( $active_tab === 'agency_contact' ? ' active_tab' : '' ).'" id="agency_contact">';
                wpestate_agency_contact_box( $post );
            print '</div>';
            print '<div class="property_tab_item_content'.( $active_tab === 'agency_location' ? ' active_tab' : '' ).'" id="agency_location">';
                wpestate_agency_location_box( $post );
            print '</div>';
    print '</div></div>';
}
endif;

if( !function_exists('wpestate_agency_details_box') ):
function wpestate_agency_details_box( $post ) {
    print '
   
    
    <div class="property_prop_half">
        <label for="agency_website">'.esc_html__('Website (without http): ','wpresidence-core').'</label><br />
        <input type="text" id="agency_website"  name="agency_website" value="'.esc_html( get_post_meta($post->ID, 'agency_website', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agency_languages">'.esc_html__('Languages: ','wpresidence-core').'</label><br />
        <input type="text" id="agency_languages"  name="agency_languages" value="'.esc_html( get_post_meta($post->ID, 'agency_languages', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agency_license">'.esc_html__('License: ','wpresidence-core').'</label><br />
        <input type="text" id="agency_license"  name="agency_license" value="'.esc_html( get_post_meta($post->ID, 'agency_license', true) ).'">
   </div>
    <div class="property_prop_half">
        <label for="agency_opening_hours">'.esc_html__('Agency opening hours: ','wpresidence-core').'</label><br />
        <input type="text" id="agency_opening_hours"  name="agency_opening_hours" value="'.esc_html( get_post_meta($post->ID, 'agency_opening_hours', true) ).'">
     </div>
    <div class="property_prop_half">
        <label for="agency_taxes">'.esc_html__('Taxes: ','wpresidence-core').'</label><br />
        <input type="text" id="agency_taxes"  name="agency_taxes" value="'.esc_html( get_post_meta($post->ID, 'agency_taxes', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="user_meda_id">'.esc_html__('The user id for this profile: ','wpresidence-core').'</label><br />
        <input type="text" id="user_meda_id"  name="user_meda_id" value="'.intval( get_post_meta($post->ID, 'user_meda_id', true) ).'">
    </div>';

    // Display custom fields section if acf is active
    if ( function_exists( 'get_field' ) && wpresidence_get_option('wpestate_show_acf_fields', 1) )   {

        $groups = acf_get_field_groups(array('post_type' => 'estate_agency'));
        if (is_array($groups) && count($groups) > 0) {
            print '<h3>'.esc_html__('Custom Fields','wpresidence-core').'</h3>';
            foreach ($groups as $group) {
                $fields = acf_get_fields($group['key']);
                if (is_array($fields) && count($fields) > 0) {
                    foreach ($fields as $field) {
                        if (isset($field['name']) && isset($field['label']) && isset($field['type'])) {
                            $field_value = get_post_meta($post->ID, $field['name'], true);
                            $field_value = is_array($field_value) ? implode(', ', $field_value) : esc_html($field_value);
                            
                            print '<div class="property_prop_half">';
                            print '<label for="'.$field['name'].'">'.esc_html($field['label']).':</label><br />';
                            if ($field['type'] === 'wysiwyg') {
                                print '<textarea id="'.$field['name'].'" name="'.$field['name'].'" rows="4" cols="50">'.esc_html($field_value).'</textarea>';
                            } elseif ($field['type'] === 'select') {
                                print '<select id="'.$field['name'].'" name="'.$field['name'].'">';
                                if (isset($field['choices']) && is_array($field['choices'])) {
                                    foreach ($field['choices'] as $choice_value => $choice_label) {
                                        $selected = ($field_value === $choice_value) ? ' selected="selected"' : '';
                                        print '<option value="'.esc_attr($choice_value).'"'.$selected.'>'.esc_html($choice_label).'</option>';
                                    }
                                }
                                print '</select>';
                            } elseif ($field['type'] === 'checkbox') {
                                $checkbox_values = is_array($field_value) ? $field_value : explode(',', $field_value);
                                if (isset($field['choices']) && is_array($field['choices'])) {
                                    foreach ($field['choices'] as $choice_value => $choice_label) {
                                        $checked = in_array($choice_value, $checkbox_values) ? ' checked="checked"' : '';
                                        print '<label><input type="checkbox" name="'.$field['name'].'[]" value="'.esc_attr($choice_value).'"'.$checked.'> '.esc_html($choice_label).'</label><br />';
                                    }
                                }
                            } elseif ($field['type'] === 'radio') {
                                if (isset($field['choices']) && is_array($field['choices'])) {
                                    foreach ($field['choices'] as $choice_value => $choice_label) {
                                        $checked = ($field_value === $choice_value) ? ' checked="checked"' : '';
                                        print '<label><input type="radio" name="'.$field['name'].'" value="'.esc_attr($choice_value).'"'.$checked.'> '.esc_html($choice_label).'</label><br />';
                                    }
                                }
                            } else {
                                print '<input type="text" id="'.$field['name'].'" name="'.$field['name'].'" value="'.esc_html($field_value).'">';
                            }
                            print '</div>';
                        }
                    }
                }
            }
        }
    }
    
}
endif;

if( !function_exists('wpestate_agency_contact_box') ):
function wpestate_agency_contact_box( $post ) {
    print '
    <div class="property_prop_half">
        <label for="agency_email">'.esc_html__('Email: ','wpresidence-core').'</label><br />
        <input type="text" id="agency_email" name="agency_email" value="'.esc_html( get_post_meta($post->ID, 'agency_email', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agency_phone">'.esc_html__('Phone: ','wpresidence-core').'</label><br />
        <input type="text" id="agency_phone" name="agency_phone" value="'.esc_html( get_post_meta($post->ID, 'agency_phone', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agency_mobile">'.esc_html__('Mobile:','wpresidence-core').'</label><br />
        <input type="text" id="agency_mobile" name="agency_mobile" value="'.esc_html( get_post_meta($post->ID, 'agency_mobile', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agency_skype">'.esc_html__('Skype: ','wpresidence-core').'</label><br />
        <input type="text" id="agency_skype"  name="agency_skype" value="'.esc_html( get_post_meta($post->ID, 'agency_skype', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agency_facebook">'.esc_html__('Facebook: ','wpresidence-core').'</label><br />
        <input type="text" id="agency_facebook"  name="agency_facebook" value="'.esc_html( get_post_meta($post->ID, 'agency_facebook', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agency_twitter">'.esc_html__('Twitter: ','wpresidence-core').'</label><br />
        <input type="text" id="agency_twitter"  name="agency_twitter" value="'.esc_html( get_post_meta($post->ID, 'agency_twitter', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agency_linkedin">'.esc_html__('Linkedin: ','wpresidence-core').'</label><br />
        <input type="text" id="agency_linkedin"  name="agency_linkedin" value="'.esc_html( get_post_meta($post->ID, 'agency_linkedin', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agency_pinterest">'.esc_html__('Pinterest: ','wpresidence-core').'</label><br />
        <input type="text" id="agency_pinterest"  name="agency_pinterest" value="'.esc_html( get_post_meta($post->ID, 'agency_pinterest', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agency_instagram">'.esc_html__('Instagram: ','wpresidence-core').'</label><br />
        <input type="text" id="agency_instagram"  name="agency_instagram" value="'.esc_html( get_post_meta($post->ID, 'agency_instagram', true) ).'">
    </div>';
}
endif;

if( !function_exists('wpestate_agency_location_box') ):
function wpestate_agency_location_box( $post ) {
    print '
    <div class="property_prop_half">
        <label for="agency_address">'.esc_html__('Address:','wpresidence-core').' </label><br />
        <input type="text" id="agency_address"  name="agency_address" value="'.esc_html( get_post_meta($post->ID, 'agency_address', true) ).'">
    </div>
    <div class="property_prop_half prop_full">
        <label for="agency_website">'.esc_html__('Location on Map: ','wpresidence-core').'</label><br />
        <div id="googleMap" style="width:100%;height:380px;margin-bottom:30px;"></div>
        <a class="button" href="#" id="admin_place_pin">'.esc_html__('Place Pin with Property Address','wpresidence-core').'</a>
        
        <input type="hidden" name="agency_lat" id="agency_lat" value="'.esc_html( get_post_meta($post->ID, 'agency_lat', true) ).'">
        <input type="hidden" name="agency_long" id="agency_long"  value="'.esc_html( get_post_meta($post->ID, 'agency_long', true) ).'">
    </div>';
}
endif;




/**
 * Save post action hook for saving and synchronizing agency data
 *
 * This hook registers the function that will handle saving agency metabox data
 * when an agency post is created or updated in the WordPress admin.
 */  
add_action('save_post', 'wpestate_update_agency_post', 1, 2);

/**
 * Saves agency metabox data and syncs with user profile
 *
 * This function handles:
 * 1. Saving all agency metabox fields when an agency post is updated
 * 2. Synchronizing the agency data with the associated WordPress user account
 *    when the user-agency connection feature is enabled in settings
 * 3. Updating user meta fields to match agency data
 * 4. Managing profile images
 * 5. Handling email updates with validation
 *
 * @since 1.0
 * @param int $post_id The ID of the post being saved
 * @param WP_Post $post The post object being saved
 * @return void|bool Returns early if conditions aren't met
 */
if( !function_exists('wpestate_update_agency_post') ):
    function wpestate_update_agency_post($post_id,$post){
        // Verify we're working with an agency post type
        if(!is_object($post) || !isset($post->post_type)) {
            return;
        }

         if($post->post_type!='estate_agency'){
            return;    
         }

         // Check if form fields are present
         if( !isset($_POST['agency_email']) ){
             return;
         }
         
         // Only proceed if user-agency connection is enabled in theme options
         if('yes' ==  esc_html ( wpresidence_get_option('wp_estate_user_agency','') )){  
                $allowed_html   =   array();
                // Get form field values with sanitization
                $user_id    = get_post_meta($post_id, 'user_meda_id', true);
                $email      = wp_kses($_POST['agency_email'],$allowed_html);
                $phone      = wp_kses($_POST['agency_phone'],$allowed_html);
                $skype      = wp_kses($_POST['agency_skype'],$allowed_html);
                $position   = wp_kses($_POST['agency_address'],$allowed_html);
                $mobile     = wp_kses($_POST['agency_mobile'],$allowed_html);
                $desc       = wp_kses($_POST['content'],$allowed_html);
                $image_id   = get_post_thumbnail_id($post_id);
                $full_img   = wp_get_attachment_image_src($image_id, 'property_listings');           
                $facebook   = wp_kses($_POST['agency_facebook'],$allowed_html);
                $twitter    = wp_kses($_POST['agency_twitter'],$allowed_html);
                $linkedin   = wp_kses($_POST['agency_linkedin'],$allowed_html);
                $pinterest  = wp_kses($_POST['agency_pinterest'],$allowed_html);
                $instagram  = wp_kses($_POST['agency_instagram'],$allowed_html);
                $agency_website  = wp_kses($_POST['agency_website'],$allowed_html);
                $agency_opening_hours  = wp_kses($_POST['agency_opening_hours'],$allowed_html);
              
                // Update user meta fields with agency data
                update_user_meta( $user_id, 'aim', '/'.$full_img[0].'/') ;
                update_user_meta( $user_id, 'phone' , $phone) ;
                update_user_meta( $user_id, 'mobile' , $mobile) ;
                update_user_meta( $user_id, 'description' , $desc) ;
                update_user_meta( $user_id, 'skype' , $skype) ;
                update_user_meta( $user_id, 'title', $position) ;
                update_user_meta( $user_id, 'custom_picture', $full_img[0]) ;
                update_user_meta( $user_id, 'facebook', $facebook) ;
                update_user_meta( $user_id, 'twitter', $twitter) ;
                update_user_meta( $user_id, 'linkedin', $linkedin) ;
                update_user_meta( $user_id, 'pinterest', $pinterest) ;
                update_user_meta( $user_id, 'instagram', $pinterest) ;
                update_user_meta( $user_id, 'website', $agency_website) ;
                update_user_meta( $user_id, 'agency_opening_hours', $agency_opening_hours) ;
                update_user_meta( $user_id, 'small_custom_picture', $image_id) ;

                // Save ACF value in custom data form
                // if ( function_exists( 'get_field' ) )   {

                //     $groups = acf_get_field_groups(array('post_type' => 'estate_agency'));
                //     if (is_array($groups) && count($groups) > 0) {
                //         foreach ($groups as $group) {
                //             $fields = acf_get_fields($group['key']);
                //             if (is_array($fields) && count($fields) > 0) {
                //                 foreach ($fields as $field) {
                //                     if (isset($field['name']) && isset($field['label']) && isset($field['type'])) {
                //                         // Save ACF field value to post meta
                //                         $acf_value = isset($_POST[$field['name']]) ? sanitize_text_field($_POST[$field['name']]) : '';
                //                         update_post_meta($post->ID, $field['name'], $acf_value);
                //                     }
                //                 }
                //             }
                //         }
                //     }
                // }

                // Handle email updates - check if email exists before updating
                $new_user_id    =   email_exists( $email ) ;
                if ( $new_user_id){
                    // Email already exists for another user, so don't update
                } else{
                    // Update user email when it doesn't conflict with existing users
                    $args = array(
                         'ID'         => $user_id,
                         'user_email' => $email
                    ); 
                    wp_update_user( $args );
                } 
        }//end if
    }
endif;