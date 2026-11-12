<?php 
/**
 * WP Estate Membership Downgrade Functions
 *
 * This file contains functions related to membership downgrade processes
 * for the WP Estate real estate plugin. Includes functions to handle package
 * downgrades, free account conversions, and agency/agent relationship management
 * during downgrade scenarios.
 *
 * Functions include checking for potential downgrade issues, converting accounts
 * to free status, handling agency-agent relationships during downgrades, and
 * downgrading to specific packages with appropriate notifications.
 *
 * @package WP Estate
 * @subpackage Membership
 * @since 1.0.0
 */



/////////////////////////////////////////////////////////////////////////////////////
/// check for downgrade
/////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_check_downgrade_situation') ):
    /**
     * Checks if changing to a new package would cause a downgrade situation
     *
     * This function determines if switching to a new membership package would
     * result in the user having fewer listings or featured listings than they
     * currently have active, which would cause problems with existing properties.
     *
     * @param int $user_id The ID of the user being checked
     * @param int $new_pack_id The ID of the new package being considered
     * @return bool True if downgrade situation exists, False otherwise
     */
    function  wpestate_check_downgrade_situation($user_id,$new_pack_id){
    
        // Get package limits from the new package
        $future_listings                  =   get_post_meta($new_pack_id, 'pack_listings', true);
        $future_featured_listings         =   get_post_meta($new_pack_id, 'pack_featured_listings', true);
        $unlimited_future                 =   get_post_meta($new_pack_id, 'mem_list_unl', true);
        $curent_list                      =   get_user_meta( $user_id, 'package_listings', true) ;
    
        // If new package has unlimited listings, no downgrade situation exists
        if($unlimited_future==1){
            return false;
        }
    
        // Special case: moving from unlimited to limited listings
        if ($curent_list == -1 && $unlimited_future!=1 ){ // if is unlimited and go to non unlimited pack
            return true;
        }
    
        // Check if user has more active/featured listings than new package allows
        if ( (wpestate_get_user_curent_listings_published($user_id) > $future_listings ) || ( wpestate_get_user_curent_future_listings($user_id) > $future_featured_listings ) ){
            return true;
        }else{
            return false;
        }
    
    
    }
    endif; // end   wpestate_check_downgrade_situation
    


/**
 * Expires all agents associated with an agency user
 * 
 * When an agency's membership expires, this function is called to downgrade
 * all associated agents to free accounts as well.
 *
 * @param int $user_id The ID of the agency user
 * @return void
 */
function wpestate_expire_agents_for_agencies($user_id){
    // Query all agents belonging to this agency
    $args = array(
            'post_type'        =>  'estate_agent',
            'author'           =>  $user_id,
            'post_status'      =>  array( 'any' )
            );

    $prop_selection = new WP_Query($args);

    // Loop through each agent and downgrade them to free
    while ($prop_selection->have_posts()): $prop_selection->the_post();
        $agent_id=get_post_meta(get_the_ID(), 'user_meda_id',true );
        wpestate_downgrade_to_free($agent_id);
    endwhile;
}




/////////////////////////////////////////////////////////////////////////////////////
/// downgrade to free
/////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('wpestate_downgrade_to_free') ):
    /**
     * Downgrades a user's account to free status
     *
     * This function is called when a membership expires or is cancelled.
     * It resets the user's package to free, adjusts listing allowances based on settings,
     * expires all active listings, and sends a notification email.
     * For agency accounts, it also downgrades all associated agents.
     *
     * @param int $user_id The ID of the user to downgrade
     * @return void
     */
    function wpestate_downgrade_to_free($user_id){
       global $post;
       
       // Determine listing limits based on admin settings
       if( wpresidence_get_option( 'wp_estate_downgraded_to_free_values')== 'free' ){
           // Use free package values if setting is "free"
           $free_pack_listings        = esc_html( wpresidence_get_option('wp_estate_free_mem_list','') );
           $free_pack_feat_listings   = esc_html( wpresidence_get_option('wp_estate_free_feat_list','') );
       }else{        
           // Set to 0 if setting is not "free"
           $free_pack_listings        = 0;
           $free_pack_feat_listings   = 0;
       }
       // Get free package image limit
       $free_pack_images          = esc_html( wpresidence_get_option('free_pack_image_included','') );
       
       // Update user meta with free package settings
       update_user_meta( $user_id, 'package_id', '') ;
       update_user_meta( $user_id, 'package_listings', $free_pack_listings) ;
       update_user_meta( $user_id, 'package_featured_listings', $free_pack_feat_listings);
       update_user_meta( $user_id, 'pack_image_included', $free_pack_images);
       
       // Clear subscription data
       update_user_meta( $user_id, 'stripe_subscription_id', '' );
       update_user_meta( $user_id, 'stripe'                ,  '' );
       
       // Query all properties by this user
       $args = array(
               'post_type' => 'estate_property',
               'author'    => $user_id,
               'post_status'   => 'any'
       );
   
       // Mark all properties as expired
       $query = new WP_Query( $args );
       while( $query->have_posts()){
               $query->the_post();
   
               $prop = array(
                       'ID'            => $post->ID,
                       'post_type'     => 'estate_property',
                       'post_status'   => 'expired'
               );
   
               wp_update_post($prop );
         }
       wp_reset_query();
   
       // Handle agency accounts - expire all agents as well
       if(is_wpresidence_developer_or_agency()){
           wpestate_expire_agents_for_agencies($user_id);
       }
   
       // Send notification email
       $user       =   get_user_by('id',$user_id);
       $user_email =   $user->user_email;
   
       $arguments=array();
       wpestate_select_email_type($user_email,'membership_cancelled',$arguments);
   
    }
    endif; // end   wpestate_downgrade_to_free
   
   
   

if( !function_exists('wpestate_downgrade_warning') ):
    /**
     * Sends a downgrade warning email to a user
     *
     * This function is typically called before a membership is about to expire
     * to warn the user about the upcoming downgrade. The email template contains
     * the specific warning details.
     *
     * @param int $user_id The ID of the user to warn
     * @return void
     */
    function wpestate_downgrade_warning($user_id){
    
        // Get user email
        $user       =   get_user_by('id',$user_id);
        $user_email =   $user->user_email;
    
        // Send warning email
        $arguments=array();
        wpestate_select_email_type($user_email,'downgrade_warning',$arguments);
    
    
    }
    endif;
    
    

/////////////////////////////////////////////////////////////////////////////////////
/// downgrade to pack
/////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_downgrade_to_pack') ):
    /**
     * Downgrades a user to a specific package
     *
     * This function is called when a user is being moved to a lower-tier package,
     * either due to payment issues or manual downgrade. It updates the user's
     * package limits, expires all active properties, unfeatures all featured
     * properties, and sends a notification email.
     *
     * @param int $user_id The ID of the user to downgrade
     * @param int $pack_id The ID of the package to downgrade to
     * @return void
     */
    function wpestate_downgrade_to_pack( $user_id, $pack_id ){
    
        // Get new package limits
        $future_listings                =   get_post_meta($pack_id, 'pack_listings', true);
        $future_featured_listings       =   get_post_meta($pack_id, 'pack_featured_listings', true);
        $future_images                  =   get_post_meta($pack_id, 'pack_image_included', true);
        
        // Update user meta with new package limits
        update_user_meta( $user_id, 'package_listings', $future_listings) ;
        update_user_meta( $user_id, 'package_featured_listings', $future_featured_listings);
        update_user_meta( $user_id, 'pack_image_included', $future_featured_listings);
    
        // Get list of all agents associated with this user (if agency)
        $agent_list     =  (array) get_user_meta($user_id,'current_agent_list',true);
        $agent_list[]   =   $user_id;
    
        // Query all properties by this user and their agents
        $args = array(
            'post_type'     => 'estate_property',
            'author__in'    =>  $agent_list,
            'post_status'   => 'any'
        );
    
        // Mark all properties as expired and remove featured status
        $query = new WP_Query( $args );
        global $post;
        while( $query->have_posts()){
                $query->the_post();
    
                $prop = array(
                        'ID'            => $post->ID,
                        'post_type'     => 'estate_property',
                        'post_status'   => 'expired',
                        'post_per_page' => -1,
                );
    
                wp_update_post($prop );
                update_post_meta($post->ID, 'prop_featured', 0);
          }
        wp_reset_query();
    
        // Send downgrade notification email
        $user = get_user_by('id',$user_id);
        $user_email=$user->user_email;
    
        $arguments=array();
    
        wpestate_select_email_type($user_email,'account_downgraded',$arguments);
    
    
    }
    endif; // end   wpestate_downgrade_to_pack