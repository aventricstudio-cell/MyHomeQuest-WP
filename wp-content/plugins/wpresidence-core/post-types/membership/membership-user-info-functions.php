<?php
/**
 * WP Estate User Listings Management Functions
 *
 * This file contains functions for tracking, counting, and managing property listings
 * associated with users in the WP Estate real estate platform. These functions support
 * the membership package system by calculating:
 *  - Total listings counts (published, pending, expired)
 *  - Featured listings counts
 *  - Remaining available listings under current package
 *  - Membership status and expiration
 *
 * These functions are used throughout the platform to enforce listing limits,
 * check package expiration, and provide user statistics.
 *
 * @package WP Estate
 * @subpackage Membership
 * @since 1.0.0
 */


if( !function_exists('wpestate_get_user_curent_listings_published') ):
    /**
     * Counts the number of published listings for a user
     *
     * Retrieves the count of all published properties belonging to a specific user
     * or their affiliated agents (if the user is an agency with agents).
     *
     * @param int $userid The user ID to check
     * @return int Number of published listings
     */
    function wpestate_get_user_curent_listings_published($userid) {
        // Get list of agents associated with this user (if any)
        $agent_list     =  (array) get_user_meta($userid,'current_agent_list',true);
        $agent_list[]   =   $userid;
    
        // Query for published properties by this user and their agents
        $args = array(
            'post_type'     =>  'estate_property',
            'post_status'   =>  'publish',
            'author__in'    =>  $agent_list,
    
        );
        $posts = new WP_Query( $args );
        return $posts->found_posts;
        wp_reset_query();
    }
    endif; // end   get_user_curent_listings
    


    if( !function_exists('wpestate_get_user_curent_listings_no_exp') ):
        /**
         * Counts the number of non-expired listings for a user
         *
         * Retrieves the count of all pending and published properties belonging to a specific
         * user or their affiliated agents. Excludes expired listings.
         *
         * @param int $userid The user ID to check
         * @return int Number of active (non-expired) listings
         */
        function wpestate_get_user_curent_listings_no_exp($userid) {
            // Get list of agents associated with this user (if any)
            $agent_list     =   (array)get_user_meta($userid,'current_agent_list',true);
            $agent_list[]   =   $userid;
        
            // Query for pending and published properties (not expired)
            $args = array(
                'post_type'     =>  'estate_property',
                'post_status'   =>  array( 'pending', 'publish' ),
                'author__in'    =>  $agent_list,
        
            );
        
            $posts = new WP_Query( $args );
            return $posts->found_posts;
            wp_reset_query();
        
        }
        endif; // end   wpestate_get_user_curent_listings_no_exp


        

/////////////////////////////////////////////////////////////////////////////////////
/// get the number of featured listings
/////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('wpestate_get_user_curent_future_listings_no_exp') ):
    /**
     * Counts the number of active featured listings for a user
     *
     * Retrieves the count of all pending and published properties that are marked as
     * featured (prop_featured = 1) for a specific user or their affiliated agents.
     *
     * @param int $user_id The user ID to check
     * @return int Number of active featured listings
     */
    function wpestate_get_user_curent_future_listings_no_exp($user_id){
        // Get list of agents associated with this user (if any)
        $agent_list     =   (array)get_user_meta($user_id,'current_agent_list',true);
        $agent_list[]   =   $user_id;
    
        // Query for pending and published properties that are featured
        $args = array(
            'post_type'     =>  'estate_property',
            'post_status'   =>  array( 'pending', 'publish' ),
            'author__in'    =>  $agent_list,
            'meta_query'    =>  array(
                                    array(
                                        'key'   => 'prop_featured',
                                        'value' => 1,
                                        'meta_compare '=>'='
                                    )
                                )
        );
        $posts = new WP_Query( $args );
        return $posts->found_posts;
        wp_reset_query();
    
    }
endif; // end   wpestate_get_user_curent_future_listings_no_exp
    
    

if( !function_exists('wpestate_get_user_curent_future_listings') ):
    /**
     * Counts the total number of featured listings for a user (any status)
     *
     * Retrieves the count of all properties (regardless of status) that are marked
     * as featured (prop_featured = 1) for a specific user or their affiliated agents.
     *
     * @param int $user_id The user ID to check
     * @return int Total number of featured listings
     */
    function wpestate_get_user_curent_future_listings($user_id){
        // Get list of agents associated with this user (if any)
        $agent_list     =  (array) get_user_meta($user_id,'current_agent_list',true);
        $agent_list[]   =   $user_id;
        
        // Query for all properties (any status) that are featured
        $args = array(
            'post_type'     =>  'estate_property',
            'post_status'   =>  'any',
            'author__in'    =>  $agent_list,
            'meta_query'    =>  array(
                                    array(
                                        'key'   => 'prop_featured',
                                        'value' => 1,
                                        'meta_compare '=>'='
                                    )
                            )
        );
        $posts = new WP_Query( $args );
        return $posts->found_posts;
        wp_reset_query();
    
    }
    endif; // end   wpestate_get_user_curent_future_listings



    ////////////////////////////////////////////////////////////////////////////////
/// Check user status durin cron
////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_check_user_membership_status_function') ):
    /**
     * Cron function to check membership status of all subscribers
     *
     * This function runs periodically to check all subscriber users for membership
     * expiration. It sends warnings 2-3 days before expiration and downgrades expired
     * memberships to free accounts. Also checks for free listing expiration.
     *
     * @return void
     */
    function wpestate_check_user_membership_status_function(){
    
        // Get all users with subscriber role
        $blogusers = get_users();
        foreach ($blogusers as $user) {
    
            $user_id=$user->ID;
            $pack_id= get_user_meta ( $user_id, 'package_id', true);
    
            if( $pack_id !='' ){ // if the pack is ! free
                // Get package activation date
                $date =  strtotime ( get_user_meta($user_id, 'package_activation',true) );
    
                // Get billing period details
                $biling_period  =   get_post_meta($pack_id, 'biling_period', true);
                $billing_freq   =   get_post_meta($pack_id, 'billing_freq', true);
    
                // Calculate expiration in seconds
                $seconds=0;
                switch ($biling_period){
                   case 'Day':
                       $seconds=60*60*24;
                       break;
                   case 'Week':
                       $seconds=60*60*24*7;
                       break;
                   case 'Month':
                       $seconds=60*60*24*30;
                       break;
                   case 'Year':
                       $seconds=60*60*24*365;
                       break;
                }
                $time_frame=$seconds*$billing_freq;
    
                $now=time();
    
                // Send warning 2-3 days before expiration
                if( $now > ( $date+$time_frame-(60*60*24*3))  &&  ($now <$date+$time_frame-(60*60*24*2)) ){ // if this moment is bigger than pack activation + billing period
                    wpestate_downgrade_warning($user_id);
                }
    
                // Downgrade if package expired
                if( $now >$date+$time_frame ){ // if this moment is bigger than pack activation + billing period
                     wpestate_downgrade_to_free($user_id);
                }
    
            } // end if if pack !- free
    
        }// end foreach
    
        // Also check free listing expiration
        wpestate_check_free_listing_expiration();
    
    }
    endif; // end   wpestate_check_user_membership_status_function



        
    if( !function_exists('wpestate_get_remain_listing_user') ):
        /**
         * Calculates the number of remaining listings available for a user
         *
         * This function determines how many more listings a user can create based
         * on their current package. Returns -1 for unlimited listings.
         *
         * @param int $userID User ID to check
         * @param int|string $user_pack Package ID the user is subscribed to (empty if free)
         * @return int Number of remaining listings (-1 for unlimited)
         */
        function wpestate_get_remain_listing_user($userID,$user_pack){
            if ( $user_pack !='' ){
                // Get current listings for paid package
                $current_listings   = wpestate_get_current_user_listings($userID);
                $pack_listings      = get_post_meta($user_pack, 'pack_listings', true);
        
                return $current_listings;
            }else{
                // Calculate for free membership
                $free_mem_list      = esc_html( wpresidence_get_option('wp_estate_free_mem_list','') );
                $free_mem_list_unl  = wpresidence_get_option('wp_estate_free_mem_list_unl', '' );
                if($free_mem_list_unl==1){
                      return -1; // -1 indicates unlimited listings
                }else{
                    $current_listings=wpestate_get_current_user_listings($userID);
                    return $current_listings;
                }
              }
        }
        endif; // end   wpestate_get_remain_listing_user
        


        if( !function_exists('wpestate_get_remain_days_user') ):
            /**
             * Calculates the number of days remaining in the user's package
             *
             * This function determines how many days are left before the user's
             * current package expires. Handles both paid packages and free packages.
             *
             * @param int $userID User ID to check
             * @param int|string $user_pack Package ID the user is subscribed to (empty if free)
             * @param string $user_registered Date the user registered
             * @param string $user_package_activation Date the package was activated
             * @return int|void Number of days remaining (void for unlimited free packages)
             */
            function wpestate_get_remain_days_user($userID,$user_pack,$user_registered,$user_package_activation){
            
                if ($user_pack!=''){
                    // Calculate for paid package
                    $pack_time  = get_post_meta($user_pack, 'pack_time', true);
                    $now        = time();
            
                    $user_date  = strtotime($user_package_activation);
                    $datediff   = $now - $user_date;
                    if( floor($datediff/(60*60*24)) > $pack_time){
                        return 0;
                    }else{
                        return floor($pack_time-$datediff/(60*60*24));
                    }
            
            
                }else{
                    // Calculate for free package
                    $free_mem_days      = esc_html( wpresidence_get_option('wp_estate_free_mem_days','') );
                    $free_mem_days_unl  = wpresidence_get_option('wp_estate_free_mem_days_unl', '');
                    if($free_mem_days_unl==1){
                        return; // return nothing for unlimited days
                    }else{
                         $now = time();
                         $user_date = strtotime($user_registered);
                         $datediff = $now - $user_date;
                         if(  floor($datediff/(60*60*24)) >$free_mem_days){
                             return 0;
                         }else{
                            return floor($free_mem_days-$datediff/(60*60*24));
                         }
                    }
                }
            }
            endif; // end   wpestate_get_remain_days_user
            
            

  /////////////////////////////////////////////////////////////////////////////////////
/// get the number of listings
/////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_get_user_curent_listings') ):
    /**
     * Counts the total number of listings for a user (any status)
     *
     * Retrieves the count of all properties (regardless of status)
     * belonging directly to a specific user (not including agent listings).
     *
     * @param int $userid The user ID to check
     * @return int Total number of listings
     */
    function wpestate_get_user_curent_listings($userid) {
      $args = array(
            'post_type'     =>  'estate_property',
            'post_status'   =>  'any',
            'author'        =>  $userid,
    
        );
        $posts = new WP_Query( $args );
        return $posts->found_posts;
        wp_reset_query();
    }
    endif; // end   get_user_curent_listings
    
    
              


///////////////////////////////////////////////////////////////////////////////////////////
// return no of featuerd listings available for current pack
///////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_get_remain_featured_listing_user') ):
    /**
     * Retrieves the number of featured listings remaining for a user
     *
     * Gets the value from user meta indicating how many more properties
     * the user can mark as featured under their current package.
     *
     * @param int $userID User ID to check
     * @return int Number of featured listings remaining
     */
    function wpestate_get_remain_featured_listing_user($userID){
        $count  =   get_the_author_meta( 'package_featured_listings' , $userID );
        return $count;
    }
    endif; // end   wpestate_get_remain_featured_listing_user
    
    
    
    
    ///////////////////////////////////////////////////////////////////////////////////////////
    // return no of listings available for current pack
    ///////////////////////////////////////////////////////////////////////////////////////////
    
    if( !function_exists('wpestate_get_current_user_listings') ):
        /**
         * Retrieves the number of regular listings available for a user
         *
         * Gets the value from user meta indicating how many more properties
         * the user can create under their current package.
         *
         * @param int $userID User ID to check
         * @return int Number of listings available (-1 for unlimited)
         */
        function wpestate_get_current_user_listings($userID){
            $count  =   get_the_author_meta( 'package_listings' , $userID );
            return $count;
        }
    endif;