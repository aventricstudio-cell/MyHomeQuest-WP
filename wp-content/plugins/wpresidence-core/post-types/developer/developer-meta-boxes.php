<?php 
/**
 * WP Estate Developer Management
 *
 * This file handles the developer entity management in the WP Estate real estate system.
 * It provides functionality for:
 * 1. Creating custom metaboxes for the estate_developer post type
 * 2. Building the developer profile form with contact and social media fields
 * 3. Saving developer data and synchronizing with WordPress user metadata
 * 4. Managing location data with map integration
 *
 * Developers are distinct entities from agents in the WP Estate system, typically
 * representing property development companies or larger real estate businesses.
 *
 * @package WPResidence
 * @subpackage DeveloperManagement
 * @since 1.0.0
 */

////////////////////////////////////////////////////////////////////////////////////////////////
// Add agent metaboxes
////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_add_developer_metaboxes') ):
/**
 * Register metabox for developer settings
 *
 * Adds a tabbed interface metabox for the estate_developer post type.
 *
 * @since 1.0.0
 * @return void
 */
    function wpestate_add_developer_metaboxes() {
        add_meta_box( 'estate_developer-sectionid', esc_html__( 'Developer Settings', 'wpresidence-core' ), 'estate_developer_tabbed_interface', 'estate_developer', 'normal', 'default' );
    }
    endif; // end   wpestate_add_developer_metaboxes
    
    
    
    ////////////////////////////////////////////////////////////////////////////////////////////////
    // Developer details
    ////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('estate_developer_tabbed_interface') ):
function estate_developer_tabbed_interface() {
    global $post;
    wp_nonce_field( plugin_basename( __FILE__ ), 'estate_developer_noncename' );

    $available_tabs = array( 'developer_details', 'developer_contact', 'developer_location' );
    $active_tab = 'developer_details';
    if ( isset( $_GET['developer_tab'] ) && in_array( $_GET['developer_tab'], $available_tabs, true ) ) {
        $active_tab = sanitize_key( $_GET['developer_tab'] );
    }

    print '<div class="property_options_wrapper meta-options">'
        .'<div class="property_options_wrapper_list">';
            print '<div class="property_tab_item'.( $active_tab === 'developer_details' ? ' active_tab' : '' ).'" data-content="developer_details">'.esc_html__('Developer Details','wpresidence-core').'</div>';
            print '<div class="property_tab_item'.( $active_tab === 'developer_contact' ? ' active_tab' : '' ).'" data-content="developer_contact">'.esc_html__('Developer Contact / Social','wpresidence-core').'</div>';
            print '<div class="property_tab_item'.( $active_tab === 'developer_location' ? ' active_tab' : '' ).'" data-content="developer_location">'.esc_html__('Address & Map','wpresidence-core').'</div>';
    print '</div><div class="property_options_content_wrapper">';
            print '<div class="property_tab_item_content'.( $active_tab === 'developer_details' ? ' active_tab' : '' ).'" id="developer_details">';
                wpestate_developer_details_box( $post );
            print '</div>';
            print '<div class="property_tab_item_content'.( $active_tab === 'developer_contact' ? ' active_tab' : '' ).'" id="developer_contact">';
                wpestate_developer_contact_box( $post );
            print '</div>';
            print '<div class="property_tab_item_content'.( $active_tab === 'developer_location' ? ' active_tab' : '' ).'" id="developer_location">';
                wpestate_developer_location_box( $post );
            print '</div>';
    print '</div></div>';
}
endif;

if( !function_exists('wpestate_developer_details_box') ):
function wpestate_developer_details_box( $post ) {
    print '
    <div class="property_prop_half">
        <label for="developer_website">'.esc_html__('Website (without http): ','wpresidence-core').'</label><br />
        <input type="text" id="developer_website"  name="developer_website" value="'.esc_html( get_post_meta($post->ID, 'developer_website', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="developer_languages">'.esc_html__('Languages: ','wpresidence-core').'</label><br />
        <input type="text" id="developer_languages"  name="developer_languages" value="'.esc_html( get_post_meta($post->ID, 'developer_languages', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="developer_license">'.esc_html__('License: ','wpresidence-core').'</label><br />
        <input type="text" id="developer_license"  name="developer_license" value="'.esc_html( get_post_meta($post->ID, 'developer_license', true) ).'">
   </div>
    <div class="property_prop_half">
        <label for="developer_opening_hours">'.esc_html__('Developer opening hours: ','wpresidence-core').'</label><br />
        <input type="text" id="developer_opening_hours"  name="developer_opening_hours" value="'.esc_html( get_post_meta($post->ID, 'developer_opening_hours', true) ).'">
   </div>
    <div class="property_prop_half">
        <label for="developer_taxes">'.esc_html__('Taxes: ','wpresidence-core').'</label><br />
        <input type="text" id="developer_taxes"  name="developer_taxes" value="'.esc_html( get_post_meta($post->ID, 'developer_taxes', true) ).'">
   </div>
    <div class="property_prop_half">
        <label for="user_meda_id">'.esc_html__('The user id for this profile: ','wpresidence-core').'</label><br />
        <input type="text" id="user_meda_id"  name="user_meda_id" value="'.intval( get_post_meta($post->ID, 'user_meda_id', true) ).'">
    </div>';

    // Display custom fields section if acf is active
    if ( function_exists( 'get_field' ) && wpresidence_get_option('wpestate_show_acf_fields', 1) )   {

        $groups = acf_get_field_groups(array('post_type' => 'estate_developer'));
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

if( !function_exists('wpestate_developer_contact_box') ):
function wpestate_developer_contact_box( $post ) {
    print '
 
    <div class="property_prop_half">
        <label for="developer_email">'.esc_html__('Email: ','wpresidence-core').'</label><br />
        <input type="text" id="developer_email" name="developer_email" value="'.esc_html( get_post_meta($post->ID, 'developer_email', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="developer_phone">'.esc_html__('Phone: ','wpresidence-core').'</label><br />
        <input type="text" id="developer_phone" name="developer_phone" value="'.esc_html( get_post_meta($post->ID, 'developer_phone', true) ).'">
   </div>
    <div class="property_prop_half">
        <label for="developer_mobile">'.esc_html__('Mobile:','wpresidence-core').'</label><br />
        <input type="text" id="developer_mobile" name="developer_mobile" value="'.esc_html( get_post_meta($post->ID, 'developer_mobile', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="developer_skype">'.esc_html__('Skype: ','wpresidence-core').'</label><br />
        <input type="text" id="developer_skype"  name="developer_skype" value="'.esc_html( get_post_meta($post->ID, 'developer_skype', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="developer_facebook">'.esc_html__('Facebook: ','wpresidence-core').'</label><br />
        <input type="text" id="developer_facebook"  name="developer_facebook" value="'.esc_html( get_post_meta($post->ID, 'developer_facebook', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="developer_twitter">'.esc_html__('Twitter: ','wpresidence-core').'</label><br />
        <input type="text" id="developer_twitter"  name="developer_twitter" value="'.esc_html( get_post_meta($post->ID, 'developer_twitter', true) ).'">
   </div>
    <div class="property_prop_half">
        <label for="developer_linkedin">'.esc_html__('Linkedin: ','wpresidence-core').'</label><br />
        <input type="text" id="developer_linkedin"  name="developer_linkedin" value="'.esc_html( get_post_meta($post->ID, 'developer_linkedin', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="developer_pinterest">'.esc_html__('Pinterest: ','wpresidence-core').'</label><br />
        <input type="text" id="developer_pinterest"  name="developer_pinterest" value="'.esc_html( get_post_meta($post->ID, 'developer_pinterest', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="developer_instagram">'.esc_html__('Instagram: ','wpresidence-core').'</label><br />
        <input type="text" id="developer_instagram"  name="developer_instagram" value="'.esc_html( get_post_meta($post->ID, 'developer_instagram', true) ).'">
    </div>';
}
endif;

if( !function_exists('wpestate_developer_location_box') ):
function wpestate_developer_location_box( $post ) {
    print '
    <div class="property_prop_half">
        <label for="developer_address">'.esc_html__('Address:','wpresidence-core').' </label><br />
        <input type="text" id="developer_address"  name="developer_address" value="'.esc_html( get_post_meta($post->ID, 'developer_address', true) ).'">
    </div>
    <div class="property_prop_half prop_full">
        <label for="developer_website">'.esc_html__('Location on Map: ','wpresidence-core').'</label><br />

        <div id="googleMap" style="width:100%;height:380px;margin-bottom:30px;"></div>
        <a class="button" href="#" id="admin_place_pin">'.esc_html__('Place Pin with Property Address','wpresidence-core').'</a>
      
        <input type="hidden" name="developer_lat" id="developer_lat" value="'.esc_html( get_post_meta($post->ID, 'developer_lat', true) ).'">
        <input type="hidden" name="developer_long" id="developer_long"  value="'.esc_html( get_post_meta($post->ID, 'developer_long', true) ).'">
    </div>';
}
endif;
    
/**
 * Hook the update function to WordPress save_post action
 */
add_action('save_post', 'wpestate_update_developer_post', 1, 2);

if( !function_exists('wpestate_update_developer_post') ):
/**
 * Save developer data when an estate_developer post is updated
 *
 * This function handles saving developer data to both post meta and user meta
 * when a developer profile is saved. It:
 * 1. Validates the post type and required fields
 * 2. Checks WordPress settings to determine if user synchronization is enabled
 * 3. Sanitizes all input fields for security
 * 4. Updates the linked WordPress user with developer information
 * 5. Handles email updates with validation
 *
 * The function ensures consistency between the developer post and the associated
 * WordPress user account for front-end profile display.
 *
 * @since 1.0.0
 * @param int $post_id The ID of the post being saved
 * @param WP_Post $post The post object being saved
 * @return void Returns early if validation checks fail
 */
    function wpestate_update_developer_post($post_id,$post){
        // Verify this is a proper post object
        if(!is_object($post) || !isset($post->post_type)) {
            return;
        }

        // Only process estate_developer post type 
         if($post->post_type!='estate_developer'){
            return;    
         }

        // Ensure form was submitted with expected fields
         if( !isset($_POST['developer_email']) ){
             return;
         }
         
         // Check if user-developer sync is enabled in WP Estate settings
         if('yes' ==  esc_html ( wpresidence_get_option('wp_estate_user_developer','') )){  
                // Define empty array for allowed HTML tags in sanitization
                $allowed_html   =   array();
                
                // Get the WordPress user ID associated with this developer
                $user_id    = get_post_meta($post_id, 'user_meda_id', true);
                
                // Sanitize all form fields
                $email      = wp_kses($_POST['developer_email'],$allowed_html);
                $phone      = wp_kses($_POST['developer_phone'],$allowed_html);
                $skype      = wp_kses($_POST['developer_skype'],$allowed_html);
                $position   = wp_kses($_POST['developer_address'],$allowed_html);
                $mobile     = wp_kses($_POST['developer_mobile'],$allowed_html);
                $desc       = wp_kses($_POST['content'],$allowed_html);
                
                // Get featured image information
                $image_id   = get_post_thumbnail_id($post_id);
                $full_img   = wp_get_attachment_image_src($image_id, 'property_listings');           
                
                // Sanitize social media fields
                $facebook   = wp_kses($_POST['developer_facebook'],$allowed_html);
                $twitter    = wp_kses($_POST['developer_twitter'],$allowed_html);
                $linkedin   = wp_kses($_POST['developer_linkedin'],$allowed_html);
                $pinterest  = wp_kses($_POST['developer_pinterest'],$allowed_html);
                $instagram  = wp_kses($_POST['developer_instagram'],$allowed_html);
                
                // Sanitize business information
                $developer_website  = wp_kses($_POST['developer_website'],$allowed_html);
                $developer_license  = wp_kses($_POST['developer_license'],$allowed_html);
                
                // Update all user meta fields with developer information
                // Profile picture/image
                update_user_meta( $user_id, 'aim', '/'.$full_img[0].'/') ;
                // Contact information
                update_user_meta( $user_id, 'phone' , $phone) ;
                update_user_meta( $user_id, 'mobile' , $mobile) ;
                update_user_meta( $user_id, 'description' , $desc) ;
                update_user_meta( $user_id, 'skype' , $skype) ;
                update_user_meta( $user_id, 'title', $position) ;
                update_user_meta( $user_id, 'custom_picture', $full_img[0]) ;
                // Social media profiles
                update_user_meta( $user_id, 'facebook', $facebook) ;
                update_user_meta( $user_id, 'twitter', $twitter) ;
                update_user_meta( $user_id, 'linkedin', $linkedin) ;
                update_user_meta( $user_id, 'pinterest', $pinterest) ;
                update_user_meta( $user_id, 'instagram', $pinterest) ;
                // Business information
                update_user_meta( $user_id, 'website', $developer_website) ;
                update_user_meta( $user_id, 'developer_license', $developer_license) ;
                // Small version of profile picture for thumbnails
                update_user_meta( $user_id, 'small_custom_picture', $image_id) ;

                // Save ACF value in custom data form
                // if ( function_exists( 'get_field' ) )   {

                //     $groups = acf_get_field_groups(array('post_type' => 'estate_developer'));
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

                // Handle email updates - check if email already exists for another user
                $new_user_id    =   email_exists( $email ) ;
                if ( $new_user_id){
                    // Email already exists - no action taken
                } else{
                    // Update user email if it doesn't conflict with existing users
                    $args = array(
                         'ID'         => $user_id,
                         'user_email' => $email
                    ); 
                    wp_update_user( $args );
                } 
        }//end if
    }
endif;