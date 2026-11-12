<?php 
/**
 * WP Estate User Profile and Membership Management Functions
 *
 * This file contains functions related to user profile updates, membership management,
 * and property listing features for the WP Estate real estate plugin.
 * 
 * Functions include user profile updates on registration, recurring profile management,
 * featured property handling, free listing expiration checks, and listing count management.
 *
 * @package WP Estate
 * @subpackage Membership
 * @since 1.0.0
 */


if( !function_exists('wpestate_update_profile') ):
    /**
     * Updates a new user's profile with default package settings
     *
     * This function is triggered on user registration and assigns the free membership
     * package settings to the new user. It handles unlimited listings settings, featured
     * listings count, and package activation date.
     *
     * @param int $userID The ID of the user being registered
     * @return void
     */
    function wpestate_update_profile($userID){
        if(1==1){ // if membership is on
    
            if( wpresidence_get_option('wp_estate_free_mem_list_unl', '' ) ==1 ){
                // If unlimited listings option is enabled
                $package_listings =-1;
                $featured_package_listings  = esc_html( wpresidence_get_option('wp_estate_free_feat_list','') );
            }else{
                // Get limited number of listings from options
                $package_listings           = esc_html( wpresidence_get_option('wp_estate_free_mem_list','') );
                $featured_package_listings  = esc_html( wpresidence_get_option('wp_estate_free_feat_list','') );
    
                // Default to 0 if settings are empty
                if($package_listings==''){
                    $package_listings=0;
                }
                if($featured_package_listings==''){
                    $featured_package_listings=0;
                }
            }
            // Get number of images allowed in free package
            $cur_images    =   esc_html( wpresidence_get_option('free_pack_image_included','') );
    
            // Update user meta with package details
            update_user_meta( $userID, 'package_listings', $package_listings) ;
            update_user_meta( $userID, 'package_featured_listings', $featured_package_listings) ;
            update_user_meta( $userID, 'pack_image_included', $cur_images) ;
    
            // Set activation date to current time
            $time = time();
            $date = date('Y-m-d H:i:s',$time);
            update_user_meta( $userID, 'package_activation', $date);
            //package_id no id since the pack is free
    
        }
    
    }
    endif; // end   wpestate_update_profile
    
    
    

if( !function_exists('wpestate_update_user_recuring_profile') ):
    /**
     * Updates user meta with PayPal recurring profile ID
     *
     * This function stores the PayPal recurring profile ID in user meta after
     * subscription creation. It replaces hyphens with 'xxx' to prevent encoding issues.
     *
     * @param string $profile_id The PayPal recurring profile ID
     * @param int $user_id The WordPress user ID
     * @return void
     */
    function wpestate_update_user_recuring_profile( $profile_id,$user_id ){
          // Replace hyphens to prevent encoding issues
          $profile_id=  str_replace('-', 'xxx', $profile_id);
          $profile_id=  str_replace('%2d', 'xxx', $profile_id);
    
          update_user_meta( $user_id, 'profile_id', $profile_id);
    
    }
    endif; // end   wpestate_update_user_recuring_profile
    
    

    
    add_action( 'wp_ajax_wpestate_ajax_make_prop_featured', 'wpestate_ajax_make_prop_featured' );
    
    if( !function_exists('wpestate_ajax_make_prop_featured') ):
    /**
     * Ajax handler for making a property featured
     *
     * This function processes AJAX requests to mark a property as featured.
     * It verifies user permissions, checks if the user has remaining featured listings,
     * and updates the property meta accordingly.
     *
     * @return string Response status ('done', 'no places', or error message)
     */
    function  wpestate_ajax_make_prop_featured(){
        // Verify nonce for security
        check_ajax_referer( 'wpestate_property_actions', 'security' );
        $prop_id        =   intval($_POST['propid']);
        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;
        $parent_userID  =   wpestate_check_for_agency($userID);
        $post           =   get_post($prop_id);
    
        // Get list of agents if user is agency
        $agent_list                     =   (array)get_user_meta($parent_userID,'current_agent_list',true);
    
        // Check if user is logged in
        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }
    
        // Verify property ownership (either direct or through agency)
        if( $post->post_author != $userID && !in_array($post->post_author , $agent_list) ) {
            exit('get out of my cloud');
        }else{
            // Check if user has remaining featured listings
            if(wpestate_get_remain_featured_listing_user($parent_userID) >0 ){
                // Update featured listing count and mark property as featured
                wpestate_update_featured_listing_no($parent_userID);
                update_post_meta($prop_id, 'prop_featured', 1);
                print 'done';
                die();
            }else{
                print 'no places';
                die();
            }
        }
    
    }
    endif; // end   wpestate_ajax_make_prop_featured
    
    
    
    
    



if( !function_exists('wpestate_check_free_listing_expiration') ):
    /**
     * Checks for expired free listings and updates their status
     *
     * This function checks all free listings for expiration based on the 
     * configured expiration period. If a listing has expired, it changes
     * the post status to 'expired' and notifies the owner.
     *
     * @return void
     */
    function wpestate_check_free_listing_expiration(){
        // Get expiration period from settings (in days)
        $free_feat_list_expiration= intval ( wpresidence_get_option('wp_estate_free_feat_list_expiration','') );

        if($free_feat_list_expiration!=0 && $free_feat_list_expiration!=''){
            // Get all subscriber users
            $blogusers = get_users();
            $users_with_free='';
            $author_array=array();
            foreach ($blogusers as $user) {
                $user_id=$user->ID;
                $pack_id= get_user_meta ( $user_id, 'package_id', true);

                // Skip developers and agencies
                if( is_wpresidence_developer_or_agency() ){
                   continue;
                }

                // Check if user has free package (no package ID)
                if( $pack_id =='' ){ // if the pack is ! free
                    $author_array[]=$user_id;
                }
            }

        if (!empty($author_array)){
            // Query for properties belonging to users with free packages
            $args = array(
                'post_type'        =>  'estate_property',
                'author__in'           =>  $author_array,
                'post_status'      =>  'publish'
            );

            $prop_selection = new WP_Query($args);
            while ($prop_selection->have_posts()): $prop_selection->the_post();
                $the_id=get_the_ID();
                // Calculate expiration date based on publish date
                $pfx_date = strtotime ( get_the_date("Y-m-d",  $the_id ) );
                $expiration_date=$pfx_date+$free_feat_list_expiration*24*60*60;
                $today=time();

                // If property has expired, set its status to expired
                if ($expiration_date < $today){
                    wpestate_listing_set_to_expire($the_id);
                }

            endwhile;
        }


        }
    }
endif;




if( !function_exists('wpestate_listing_set_to_expire') ):
    /**
     * Sets a listing to expired status and notifies the owner
     *
     * This function changes a property's status to 'expired' and
     * sends an email notification to the property owner.
     *
     * @param int $post_id The ID of the property to expire
     * @return void
     */
    function wpestate_listing_set_to_expire($post_id){
        // Update post status to expired
        $prop = array(
                'ID'            => $post_id,
                'post_type'     => 'estate_property',
                'post_status'   => 'expired'
        );

        wp_update_post($prop );

        // Get property author details for notification
        $user_id    =   wpsestate_get_author( $post_id );
        $user       =   get_user_by('id',$user_id);
        $user_email =   $user->user_email;

        // Prepare email arguments
        $arguments=array(
            'expired_listing_url'  =>  esc_url( get_permalink($post_id) ),
            'expired_listing_name' => get_the_title($post_id)
        );
        wpestate_select_email_type($user_email,'free_listing_expired',$arguments);


    }
endif;


if(!function_exists('wpestate_check_for_agency')):
/**
 * Checks if a user belongs to an agency and returns the appropriate user ID
 *
 * This function determines if a user is associated with an agency.
 * If they are, it returns the agency owner's user ID for proper permission handling.
 * Otherwise, it returns the original user ID.
 *
 * @param int $user_id The user ID to check
 * @return int The appropriate user ID (agency owner or original)
 */
function wpestate_check_for_agency($user_id){
    // Get agent ID if user is an agent
    $agent_id  = intval ( get_user_meta($user_id,'user_agent_id',true) ) ;
    if($agent_id!=0){
        $post=get_post($agent_id);
        // Check if agent belongs to a valid agency (not admin or system)
        if(isset($post->post_author) && $post->post_author!=1 && $post->post_author!=0 && !user_can( $post->post_author, 'manage_options' )){
            return $post->post_author;
        }else{
            return $user_id;
        }
    }else{
        return $user_id;
    }

}
endif;


if( !function_exists('wpestate_update_featured_listing_no') ):
    /**
     * Decreases the number of featured listings available to a user
     *
     * This function is called when a property is marked as featured
     * and decrements the user's available featured listings count.
     *
     * @param int $userID The user ID to update
     * @return void
     */
    function wpestate_update_featured_listing_no($userID){
        $current  =   get_the_author_meta( 'package_featured_listings' , $userID );
    
        // Decrease count but never below zero
        if($current-1>=0){
            update_user_meta( $userID, 'package_featured_listings', $current-1) ;
        }else{
              update_user_meta( $userID, 'package_featured_listings', 0) ;
        }
    }
    endif; // end   wpestate_update_featured_listing_no
    
    
    

    if( !function_exists('wpestate_update_old_users') ):
    
    /**
     * Updates legacy users with missing membership details
     *
     * This function checks if a user has membership details and
     * adds default free package information if missing. Used for
     * backward compatibility after membership feature was added.
     *
     * @param int $userID The user ID to update
     * @return void
     */
    function wpestate_update_old_users($userID){
        $paid_submission_status    = esc_html ( wpresidence_get_option('wp_estate_paid_submission','') );
        if($paid_submission_status == 'membership' ){
    
            $curent_list   =   get_user_meta( $userID, 'package_listings', true) ;
            $cur_feat_list =   get_user_meta( $userID, 'package_featured_listings', true) ;
    
                // Check if user is missing membership details
                if($curent_list=='' || $cur_feat_list=='' ){
                    // Get default free package settings
                    $package_listings           = esc_html( wpresidence_get_option('wp_estate_free_mem_list','') );
                    $featured_package_listings  = esc_html( wpresidence_get_option('wp_estate_free_feat_list','') );
                        if($package_listings==''){
                            $package_listings=0;
                        }
                        if($featured_package_listings==''){
                            $featured_package_listings=0;
                        }
    
                    // Update user with default free package details
                    update_user_meta( $userID, 'package_listings', $package_listings) ;
                    update_user_meta( $userID, 'package_featured_listings', $featured_package_listings) ;
    
                    $time = time();
                    $date = date('Y-m-d H:i:s',$time);
                    update_user_meta( $userID, 'package_activation', $date);
                }
    
        }// end if memebeship
    
    }
    endif; // end   wpestate_update_old_users


    

if( !function_exists('wpestate_update_listing_no') ):
    /**
     * Decreases the number of regular listings available to a user
     *
     * This function is called when a new property is published
     * and decrements the user's available listings count. Handles
     * unlimited listings (-1) and prevents negative counts.
     *
     * @param int $userID The user ID to update
     * @return void
     */
    function wpestate_update_listing_no($userID){
        $current  =   get_the_author_meta( 'package_listings' , $userID );
        if($current==''){
            //do nothing
        }else if($current==-1){ // if unlimited
            //do noting
        }else if($current-1>=0){
            update_user_meta( $userID, 'package_listings', $current-1) ;
        }else if( $current==0 ){
             update_user_meta( $userID, 'package_listings', 0) ;
        }
    }
    endif; // end   wpestate_update_listing_no