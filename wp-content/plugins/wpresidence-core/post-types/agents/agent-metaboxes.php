<?php 
/**
 * WP Estate Agent Metaboxes
 *
 * This file contains functions for creating and populating the agent settings metabox
 * in the WP Estate real estate management system. It handles the creation of the metabox
 * and defines the form interface for managing agent information.
 *
 * The file provides:
 * - Registration of agent settings metabox
 * - Form UI for agent personal details
 * - Social media profile management
 * - Agency relationship settings
 * - Custom fields management with dynamic add/remove capabilities
 *
 * @package WPResidence
 * @subpackage Admin
 * @since 1.0.0
 */

////////////////////////////////////////////////////////////////////////////////////////////////
// Add agent metaboxes
////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_add_agents_metaboxes') ):
/**
 * Register metabox for agent settings
 * 
 * Creates a custom metabox on the estate_agent post type edit screen
 * to provide an interface for managing all agent-related information.
 * 
 * @since 1.0.0
 * @return void
 */
    function wpestate_add_agents_metaboxes() {
      add_meta_box( 'estate_agent-sectionid', esc_html__( 'Agent Settings', 'wpresidence-core' ), 'estate_agent_tabbed_interface', 'estate_agent', 'normal', 'default' );
    }
    endif; // end   wpestate_add_agents_metaboxes

if( !function_exists('estate_agent_tabbed_interface') ):
function estate_agent_tabbed_interface() {
    global $post;
    wp_nonce_field( plugin_basename( __FILE__ ), 'estate_agent_noncename' );

    $available_tabs = array(
        'agent_details',
        'agent_contact',
        'agency_profile',
        'agent_customs',
    );

    $active_tab = 'agent_details';
    if ( isset( $_GET['agent_tab'] ) && in_array( $_GET['agent_tab'], $available_tabs, true ) ) {
        $active_tab = sanitize_key( $_GET['agent_tab'] );
    }

    print '<div class="property_options_wrapper meta-options">'
        .'<div class="property_options_wrapper_list">';
            print '<div class="property_tab_item'.( $active_tab === 'agent_details' ? ' active_tab' : '' ).'" data-content="agent_details">'.esc_html__('Agent Details','wpresidence-core').'</div>';
            print '<div class="property_tab_item'.( $active_tab === 'agent_contact' ? ' active_tab' : '' ).'" data-content="agent_contact">'.esc_html__('Agent Contact','wpresidence-core').'</div>';
            print '<div class="property_tab_item'.( $active_tab === 'agency_profile' ? ' active_tab' : '' ).'" data-content="agency_profile">'.esc_html__('Agency Profile','wpresidence-core').'</div>';
            print '<div class="property_tab_item'.( $active_tab === 'agent_customs' ? ' active_tab' : '' ).'" data-content="agent_customs">'.esc_html__('Agent Custom Fields','wpresidence-core').'</div>';
    print '</div><div class="property_options_content_wrapper">';
            print '<div class="property_tab_item_content'.( $active_tab === 'agent_details' ? ' active_tab' : '' ).'" id="agent_details">';
                wpestate_agent_details_box( $post );
            print '</div>';

            print '<div class="property_tab_item_content'.( $active_tab === 'agent_contact' ? ' active_tab' : '' ).'" id="agent_contact">';
                wpestate_agent_contact_box( $post );
            print '</div>';

            print '<div class="property_tab_item_content'.( $active_tab === 'agency_profile' ? ' active_tab' : '' ).'" id="agency_profile">';
                wpestate_agent_agency_box( $post );
            print '</div>';

            print '<div class="property_tab_item_content'.( $active_tab === 'agent_customs' ? ' active_tab' : '' ).'" id="agent_customs">';
                wpestate_agent_custom_box( $post );
            print '</div>';
    print '</div></div>';
}
endif;
    
    
    
   

if( !function_exists('wpestate_agent_details_box') ):
function wpestate_agent_details_box( $post ) {
    print'
    <div class="property_prop_half">
        <label for="first_name">'.esc_html__('Agent First Name:','wpresidence-core').' </label><br />
        <input type="text" id="first_name" name="first_name" value="'.esc_html( get_post_meta($post->ID, 'first_name', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="last_name">'.esc_html__('Agent Last Name:','wpresidence-core').' </label><br />
        <input type="text" id="last_name" name="last_name" value="'.esc_html( get_post_meta($post->ID, 'last_name', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_position">'.esc_html__('Position:','wpresidence-core').' </label><br />
        <input type="text" id="agent_position" name="agent_position" value="'.esc_html( get_post_meta($post->ID, 'agent_position', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_website">'.esc_html__('Website (without http): ','wpresidence-core').'</label><br />
        <input type="text" id="agent_website" name="agent_website" value="'.esc_html( get_post_meta($post->ID, 'agent_website', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_private_notes">'.esc_html__('Private Notes','wpresidence-core').'</label><br />
        <input type="text" id="agent_private_notes" name="agent_private_notes" value="'.esc_html( get_post_meta($post->ID, 'agent_private_notes', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="user_meda_id">'.esc_html__('The user id for this profile:','wpresidence-core').'</label><br />
        <input type="text" id="user_meda_id" name="user_meda_id" value="'.intval( get_post_meta($post->ID, 'user_meda_id', true) ).'">
    </div>';
}
endif;

if( !function_exists('wpestate_agent_contact_box') ):
function wpestate_agent_contact_box( $post ) {
    print'
    <div class="property_prop_half">
        <label for="agent_email">'.esc_html__('Email:','wpresidence-core').'</label><br />
        <input type="text" id="agent_email" name="agent_email" value="'.esc_html( get_post_meta($post->ID, 'agent_email', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_phone">'.esc_html__('Phone:','wpresidence-core').'</label><br />
        <input type="text" id="agent_phone" name="agent_phone" value="'.esc_html( get_post_meta($post->ID, 'agent_phone', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_mobile">'.esc_html__('Mobile/Whatsapp:','wpresidence-core').'</label><br />
        <input type="text" id="agent_mobile" name="agent_mobile" value="'.esc_html( get_post_meta($post->ID, 'agent_mobile', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_skype">'.esc_html__('Skype:','wpresidence-core').'</label><br />
        <input type="text" id="agent_skype" name="agent_skype" value="'.esc_html( get_post_meta($post->ID, 'agent_skype', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_member">'.esc_html__('Member of:','wpresidence-core').'</label><br />
        <input type="text" id="agent_member" name="agent_member" value="'.esc_html( get_post_meta($post->ID, 'agent_member', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_address">'.esc_html__('Address:','wpresidence-core').'</label><br />
        <input type="text" id="agent_address" name="agent_address" value="'.esc_html( get_post_meta($post->ID, 'agent_address', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_facebook">'.esc_html__('Facebook:','wpresidence-core').'</label><br />
        <input type="text" id="agent_facebook" name="agent_facebook" value="'.esc_html( get_post_meta($post->ID, 'agent_facebook', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_twitter">'.esc_html__('Twitter:','wpresidence-core').'</label><br />
        <input type="text" id="agent_twitter" name="agent_twitter" value="'.esc_html( get_post_meta($post->ID, 'agent_twitter', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_linkedin">'.esc_html__('Linkedin:','wpresidence-core').'</label><br />
        <input type="text" id="agent_linkedin" name="agent_linkedin" value="'.esc_html( get_post_meta($post->ID, 'agent_linkedin', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_pinterest">'.esc_html__('Pinterest:','wpresidence-core').'</label><br />
        <input type="text" id="agent_pinterest" name="agent_pinterest" value="'.esc_html( get_post_meta($post->ID, 'agent_pinterest', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_instagram">'.esc_html__('Instagram:','wpresidence-core').'</label><br />
        <input type="text" id="agent_instagram" name="agent_instagram" value="'.esc_html( get_post_meta($post->ID, 'agent_instagram', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_youtube">'.esc_html__('Youtube:','wpresidence-core').'</label><br />
        <input type="text" id="agent_youtube" name="agent_youtube" value="'.esc_html( get_post_meta($post->ID, 'agent_youtube', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_tiktok">'.esc_html__('TikTok:','wpresidence-core').'</label><br />
        <input type="text" id="agent_tiktok" name="agent_tiktok" value="'.esc_html( get_post_meta($post->ID, 'agent_tiktok', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_telegram">'.esc_html__('Telegram:','wpresidence-core').'</label><br />
        <input type="text" id="agent_telegram" name="agent_telegram" value="'.esc_html( get_post_meta($post->ID, 'agent_telegram', true) ).'">
    </div>
    <div class="property_prop_half">
        <label for="agent_vimeo">'.esc_html__('Vimeo:','wpresidence-core').'</label><br />
        <input type="text" id="agent_vimeo" name="agent_vimeo" value="'.esc_html( get_post_meta($post->ID, 'agent_vimeo', true) ).'">
    </div>';
}
endif;

if( !function_exists('wpestate_agent_agency_box') ):
function wpestate_agent_agency_box( $post ) {
    $author = get_post_field( 'post_author', $post->ID );
    $agency_post = get_the_author_meta('user_agent_id', $author);
    print'

    <div class="property_prop_half">
        <label for="owner_author_id">'.esc_html__('The Agency id/Developer USER ID that has this agent:','wpresidence-core').'</label><br />
        <strong>'.esc_html__('Current Agency/Developer','wpresidence-core').':</strong>'.get_the_title($agency_post).'</br>
        <input type="text" id="owner_author_id"  name="owner_author_id" value="'.$author.'">
    </div>';
}
endif;

if( !function_exists('wpestate_agent_custom_box') ):
function wpestate_agent_custom_box( $post ) {
    print '<div class="add_custom_data_cont">
             
             
                <div class="property_prop_half">
                          <input type="button" class="button button-primary add_custom_parameter" value="'.esc_html__('Add Custom Field','wpresidence-core').'">
                </div>';

    print '
                <div class="single_parameter_row cliche_row">
                <div class="meta-options third-meta-options">
                    <label for="agent_custom_label">'.esc_html__('Field Label: ','wpresidence-core').'</label><br />
                    <input type="text" name="agent_custom_label[]" value="">
                </div>
                <div class="meta-options third-meta-options">
                    <label for="agent_custom_value">'.esc_html__('Field Value: ','wpresidence-core').'</label><br />
                    <input type="text" name="agent_custom_value[]" value="">
                </div>
                <div class="meta-options third-meta-options">
                    <label for="agent_website">&nbsp;</label><br />
                    <input type="button" class="button-primary deletefieldlink button secondary   remove_parameter_button" value="'.esc_html__('Remove','wpresidence-core').'">
                </div>
                </div>
                ';

    if ( function_exists( 'get_field' ) && wpresidence_get_option('wpestate_show_acf_fields', 1) )   {

        $groups = acf_get_field_groups(array('post_type' => 'estate_agent'));
        if (is_array($groups) && count($groups) > 0) {
            foreach ($groups as $group) {
                $fields = acf_get_fields($group['key']);
                if (is_array($fields) && count($fields) > 0) {
                    foreach ($fields as $field) {
                        if (isset($field['name']) && isset($field['label']) && isset($field['type'])) {
                            print '
                            <div class="single_parameter_row">
                                <div class="meta-options third-meta-options">
                                    <label for="agent_custom_label">'.esc_html($field['label']).':</label><br />
                                    <input type="text" name="acf_label[]" value="'.esc_html($field['name']).'" readonly>
                                </div>
                                <div class="meta-options third-meta-options">
                                    <label for="agent_custom_value">'.esc_html__('Field Value: ','wpresidence-core').'</label><br />
                                    <input type="text" name="'.esc_html($field['name']).'" value="'.get_post_meta( $post->ID, $field['name'], true ).'">
                                </div>
                                <!--<div class="meta-options third-meta-options">
                                    <label for="agent_website">&nbsp;</label><br />
                                    <input type="button" class="button-primary deletefieldlink button secondary remove_parameter_button" value="'.esc_html__('Remove','wpresidence-core').'">
                                </div>-->
                            </div>';
                        }
                    }
                }
            }
        }
    }

    $agent_custom_data = get_post_meta( $post->ID, 'agent_custom_data', true );

    if( is_array($agent_custom_data) && count( $agent_custom_data ) > 0 ){
        for( $i=0; $i<count( $agent_custom_data ); $i++ ){
            print '
            <div class="single_parameter_row  ">
            <div class="meta-options third-meta-options">
                <label for="agent_website">'.esc_html__('Field Label: ','wpresidence-core').'</label><br />
                <input type="text"   name="agent_custom_label[]" value="'.esc_html( $agent_custom_data[$i]['label'] ).'">
            </div>
            <div class="meta-options third-meta-options">
                <label for="agent_website">'.esc_html__('Field Value: ','wpresidence-core').'</label><br />
                <input type="text"    name="agent_custom_value[]" value="'.esc_html( $agent_custom_data[$i]['value'] ).'">
            </div>
            <div class="meta-options third-meta-options">
                <label for="agent_website">&nbsp;</label><br />
                <input type="button" class="button-primary deletefieldlink button secondary  remove_parameter_button" value="'.esc_html__('Remove','wpresidence-core').'">
            </div>
            </div>
            ';
        }
    }

    print '</div>';
}
endif;




    /**
     * WP Estate Agent Data Save Handler
     *
     * This file contains the function responsible for saving and updating agent data
     * when an estate_agent post is saved. It handles:
     * 
     * 1. Agent-agency relationship management
     * 2. Data sanitization and storage
     * 3. Synchronization between post meta and user meta
     * 4. Custom fields processing
     * 5. Email validation and update
     *
     * The function ensures that changes made in the agent settings interface
     * are properly saved to both the post meta and the linked WordPress user.
     *
     * @package WPResidence
     * @subpackage AgentManagement
     * @since 1.0.0
     */
    
    /**
     * Hook the update function to WordPress save_post action
     * Priority 1 ensures this runs early in the save process
     */
    add_action('save_post', 'wpsx_5688_update_post', 1, 2);
    
    if( !function_exists('wpsx_5688_update_post') ):
    /**
     * Save agent data when an estate_agent post is updated
     *
     * This comprehensive function handles all aspects of saving agent data:
     * - Validates post type and required fields
     * - Manages agent-agency relationships
     * - Sanitizes all input data
     * - Synchronizes data between post meta and user meta
     * - Processes custom fields
     * - Updates email with validation
     *
     * @since 1.0.0
     * @param int $post_id The ID of the post being saved
     * @param WP_Post $post The post object
     * @return void Returns early if validation fails
     */
    function wpsx_5688_update_post($post_id,$post){
    
        // Validate that we have a proper post object
        if(!is_object($post) || !isset($post->post_type)) {
            return;
        }
    
        // Only process estate_agent post type
        if($post->post_type!='estate_agent'){
           return;
        }
    
        // Ensure the form was submitted with expected fields
        if( !isset($_POST['agent_email']) ){
            return;
        }
    
        // Handle agency/owner changes if specified
        if( isset($_POST['owner_author_id']) ){
            // Temporarily remove hooks to prevent infinite recursion
            remove_action('save_post', 'estate_save_postdata', 1, 2);
            remove_action('save_post', 'wpsx_5688_update_post', 1, 2);
    
            $old_author =   get_post_field( 'post_author', $post->ID) ;
            $new_author =   intval($_POST['owner_author_id']);
            $agent_id = intval( get_post_meta($post->ID, 'user_meda_id',true ) );
            //echo  $agent_id.'$old_authocccr '.$old_author.' / '.$new_author;
    
            //$agency_post = get_the_author_meta('user_agent_id',$author);
            
            // Only process if agency has actually changed
            if( $old_author != $new_author){
                // Update post author to new agency/owner
                $arg = array(
                    'ID'            => $post_id,
                    'post_author'   => $new_author,
                );
                wp_update_post( $arg );
    
                // Remove agent from old agency's agent list
                $current_agent_list=(array)get_user_meta($old_author,'current_agent_list',true) ;
                $agent_list=array();
                if(is_array($current_agent_list)){
                    $agent_list     = array_unique ( $current_agent_list );
                }
    
                // Find and remove this agent from old agency's list
                if (is_array($agent_list) && ($key = array_search($agent_id, $agent_list)) !== false) {
                    unset($agent_list[$key]);
                }
    
                // Ensure list remains unique after modification
                if(is_array($agent_list)){
                   $agent_list= array_unique($agent_list);
                }
    
                // Save updated list for old agency
                update_user_meta($old_author,'current_agent_list',$agent_list);
    
                // Add agent to new agency's agent list
                $agent_list     =    ((array) get_user_meta($new_author,'current_agent_list',true) );
                if(is_array($agent_list)){
                   $agent_list= array_unique($agent_list);
                }
                $agent_list[]   =   $agent_id;
    
                // Save updated list for new agency
                update_user_meta($new_author,'current_agent_list',array_unique($agent_list) );
            }
    
            // Restore hooks after processing
            add_action('save_post', 'estate_save_postdata', 1, 2);
            add_action('save_post', 'wpsx_5688_update_post', 1, 2);
        }
    
        // Process main agent data
        // Note: The 'yes' == 'yes' condition is always true, likely a placeholder
        // for a future toggle option
        if('yes' ==  'yes' ){
                // Sanitize all input fields with wp_kses
                $allowed_html   =   array();
                $first_name    = wp_kses($_POST['first_name'],$allowed_html);
                $last_name    = wp_kses($_POST['last_name'],$allowed_html);
                $user_id    = get_post_meta($post_id, 'user_meda_id', true);
                $email      = wp_kses($_POST['agent_email'],$allowed_html);
                $phone      = wp_kses($_POST['agent_phone'],$allowed_html);
                $skype      = wp_kses($_POST['agent_skype'],$allowed_html);
                $position   = wp_kses($_POST['agent_position'],$allowed_html);
                $mobile     = wp_kses($_POST['agent_mobile'],$allowed_html);
                $desc       = wp_kses($_POST['content'],$allowed_html);
                $image_id   = get_post_thumbnail_id($post_id);
                $full_img   = wp_get_attachment_image_src($image_id, 'property_listings');
                $facebook   = wp_kses($_POST['agent_facebook'],$allowed_html);
                $twitter    = wp_kses($_POST['agent_twitter'],$allowed_html);
                $linkedin   = wp_kses($_POST['agent_linkedin'],$allowed_html);
                $pinterest  = wp_kses($_POST['agent_pinterest'],$allowed_html);
                $instagram  = wp_kses($_POST['agent_instagram'],$allowed_html);
                $youtube    = wp_kses($_POST['agent_youtube'],$allowed_html);
                $tiktok     = wp_kses($_POST['agent_tiktok'],$allowed_html);
                $telegram   = wp_kses($_POST['agent_telegram'],$allowed_html);
                $vimeo      = wp_kses($_POST['agent_vimeo'],$allowed_html);
                $private_notes = wp_kses($_POST['agent_private_notes'],$allowed_html);
                $agent_website  = wp_kses($_POST['agent_website'],$allowed_html);
                $agent_member   = wp_kses($_POST['agent_member'],$allowed_html);
                $agent_address  = wp_kses($_POST['agent_address'],$allowed_html);
                
                // Process custom fields with array_map for efficient sanitization
                $agent_custom_label    = array_map( 'esc_attr', $_POST['agent_custom_label']);
                $agent_custom_value    = array_map( 'esc_attr', $_POST['agent_custom_value']);
    
                // Process custom fields data into structured array
                $agent_fields_array = array();
    
                // Start from index 1 to skip template field
                for( $i=1; $i<count( $agent_custom_label  ); $i++ ){
                    $agent_fields_array[] = array( 'label' => sanitize_text_field($agent_custom_label[$i] ), 'value' => sanitize_text_field($agent_custom_value[$i] ) );
                }
                
                // Save custom fields to post meta (commented out line shows alternative approach)
                //update_post_meta($user_id, 'agent_custom_data',   $agent_fields_array);
                update_post_meta($post->ID, 'agent_custom_data',   $agent_fields_array);

                // Save ACF value in custom data form
                if ( function_exists( 'get_field' ) && wpresidence_get_option('wpestate_show_acf_fields', 1) )   {

                    $groups = acf_get_field_groups(array('post_type' => 'estate_agent'));
                    if (is_array($groups) && count($groups) > 0) {
                        foreach ($groups as $group) {
                            $fields = acf_get_fields($group['key']);
                            if (is_array($fields) && count($fields) > 0) {
                                foreach ($fields as $field) {
                                    if (isset($field['name']) && isset($field['label']) && isset($field['type'])) {
                                        // Save ACF field value to post meta
                                        $acf_value = isset($_POST[$field['name']]) ? sanitize_text_field($_POST[$field['name']]) : '';
                                        update_post_meta($post->ID, $field['name'], $acf_value);
                                    }
                                }
                            }
                        }
                    }
                }
    
                // Synchronize all data to WordPress user meta
                // Profile picture/image
                if(isset($full_img[0])){
                    update_user_meta( $user_id, 'aim', '/'.$full_img[0].'/') ;
                    update_user_meta( $user_id, 'custom_picture', $full_img[0]) ;
                }
             
                // Contact information
                update_user_meta( $user_id, 'phone' , $phone) ;
                update_user_meta( $user_id, 'mobile' , $mobile) ;
                update_user_meta( $user_id, 'description' , $desc) ;
                update_user_meta( $user_id, 'skype' , $skype) ;
                update_user_meta( $user_id, 'title', $position) ;
      
                // Social media profiles
                update_user_meta( $user_id, 'facebook', $facebook) ;
                update_user_meta( $user_id, 'twitter', $twitter) ;
                update_user_meta( $user_id, 'linkedin', $linkedin) ;
                update_user_meta( $user_id, 'pinterest', $pinterest) ;
                update_user_meta( $user_id, 'instagram', $instagram) ;
                update_user_meta( $user_id, 'website', $agent_website) ;
                // Professional details
                update_user_meta( $user_id, 'agent_member', $agent_member) ;
                update_user_meta( $user_id, 'agent_address', $agent_address) ;
                update_user_meta( $user_id, 'small_custom_picture', $image_id) ;
                // Personal details
                update_user_meta( $user_id, 'first_name', $first_name) ;
                update_user_meta( $user_id, 'last_name', $last_name) ;
                // Additional social platforms
                update_user_meta( $user_id, 'youtube', $youtube) ;
                update_user_meta( $user_id, 'tiktok', $tiktok) ;
                update_user_meta( $user_id, 'telegram', $telegram) ;
                update_user_meta( $user_id, 'vimeo', $vimeo) ;
                update_user_meta( $user_id, 'private_notes', $private_notes) ;
                // custom fields for agent cf reprocess
    
                // Email validation and update
                // Check if email already exists for another user
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