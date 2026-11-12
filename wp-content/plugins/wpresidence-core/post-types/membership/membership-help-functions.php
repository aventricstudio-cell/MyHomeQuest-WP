<?php
/**
 * WP Estate Membership Package Management Functions
 * 
 * This file contains functions for retrieving, displaying, and managing
 * membership packages within the WP Estate real estate platform.
 * 
 * Functions include retrieving all visible packages, displaying package details
 * for users in different formats (top profile, dashboard), and determining
 * user roles for permission checks.
 * 
 * @package WP Estate
 * @subpackage Membership
 * @since 1.0.0
 */



////////////////////////////////////////////////////////////////////////////////
/// Get a list of all visible packages
////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_get_all_packs') ):
    /**
     * Retrieves all visible membership packages
     * 
     * Queries the database for membership packages that have been marked
     * as visible (pack_visible = 'yes') and formats them as options for
     * a select dropdown.
     *
     * @return string HTML string containing <option> elements for each visible package
     */
    function wpestate_get_all_packs(){
        $args = array(
                    'post_type'         => 'membership_package',
                    'posts_per_page'    => -1,
                    'meta_query'        => array(
                                                array(
                                                    'key' => 'pack_visible',
                                                    'value' => 'yes',
                                                    'compare' => '='
                                                )
    
                     )
    
             );
            $pack_selection = new WP_Query($args);
    
            while ($pack_selection->have_posts()): $pack_selection->the_post();
                $return_string.='<option value="'.$post->ID.'">'.get_the_title().'</option>';
            endwhile;
            wp_reset_query();
            return $return_string;
    }
    endif; // end   wpestate_get_all_packs
    
    
    ////////////////////////////////////////////////////////////////////////////////
    /// Get a package details from user top profile
    ////////////////////////////////////////////////////////////////////////////////
    if( !function_exists('wpestate_get_pack_data_for_user_top') ):
    /**
     * Displays membership package details in the user's top profile view
     * 
     * Generates a formatted HTML display of the user's current package information
     * for the top profile area, showing listings limits, featured listings,
     * expiration date, and other package details.
     *
     * @param int $userID User ID to retrieve package data for
     * @param int|string $user_pack Package ID the user is subscribed to (empty if free)
     * @param string $user_registered User registration date
     * @param string $user_package_activation Date when the package was activated
     * @return void Outputs HTML directly
     */
    function wpestate_get_pack_data_for_user_top($userID,$user_pack,$user_registered,$user_package_activation){
        print '<div class="pack_description">
                    <div class="pack-unit">';
    
                // Get remaining listings count, -1 means unlimited
                $remaining_lists=wpestate_get_remain_listing_user($userID,$user_pack);
                if($remaining_lists==-1){
                    $remaining_lists=esc_html__('unlimited','wpresidence-core');
                }
    
    
    
                if ($user_pack!=''){
                    // User has a paid package - collect package details
                    $title              = get_the_title($user_pack);
                    $pack_time          = get_post_meta($user_pack, 'pack_time', true);
                    $pack_list          = get_post_meta($user_pack, 'pack_listings', true);
                    $pack_featured      = get_post_meta($user_pack, 'pack_featured_listings', true);
                    $pack_price         = get_post_meta($user_pack, 'pack_price', true);
                    $unlimited_lists    = get_post_meta($user_pack, 'mem_list_unl', true);
                    $date               = strtotime ( get_user_meta($userID, 'package_activation',true) );
                    $biling_period      = get_post_meta($user_pack, 'biling_period', true);
                    $billing_freq       = intval(get_post_meta($user_pack, 'billing_freq', true));
    
    
                    // Calculate package expiration date based on billing period
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
    
                    $time_frame      =   $seconds*$billing_freq;
                    $expired_date    =   $date+$time_frame;
                    $date_format     =   get_option('date_format');
                    $expired_date    =   date($date_format,$expired_date);
                    $pack_image_included  =   get_post_meta($user_pack, 'pack_image_included', true);
                    if (intval($pack_image_included)==0){
                        $pack_image_included=esc_html__('Unlimited', 'wpresidence-core');
                    }
    
    
    
                    print '<div class="pack_description_unit_head"><h4>'.esc_html__('Your Current Package :','wpresidence-core').'</h4>
                           <span class="pack-name">'.$title.' </span></div> ';
    
                    // Show package details differently based on if it offers unlimited listings
                    if($unlimited_lists==1){
                        print '<div class="pack_description_unit pack_description_details">';
                        print esc_html__('  unlimited','wpresidence-core');
                        print '<p class="package_label">'.esc_html__('Listings Included','wpresidence-core').'</p></div>';
    
                        print '<div class="pack_description_unit pack_description_details">';
                        print esc_html__('  unlimited','wpresidence-core');
                        print '<p class="package_label">'.esc_html__('Listings Remaining','wpresidence-core').'</p></div>';
                    }else{
                        print '<div class="pack_description_unit pack_description_details">';
                        print ' '.$pack_list;
                        print '<p class="package_label">'.esc_html__('Listings Included','wpresidence-core').'</p></div>';
    
                        print '<div class="pack_description_unit pack_description_details">';
                        print '<span id="normal_list_no"> '.$remaining_lists.'</span>';
                        print '<p class="package_label">'.esc_html__('Listings Remaining','wpresidence-core').'</p></div>';
                    }
    
                    // Display featured listings info
                    print '<div class="pack_description_unit pack_description_details">';
                    print '<span id="normal_list_no"> '.$pack_featured.'</span>';
                    print '<p class="package_label">'.esc_html__('Featured Included','wpresidence-core').'</p></div>';
    
                    print '<div class="pack_description_unit pack_description_details">';
                    print '<span id="featured_list_no"> '.wpestate_get_remain_featured_listing_user($userID).'</span>';
                    print '<p class="package_label">'.esc_html__('Featured Remaining','wpresidence-core').'</p></div>';
    
                    // Display images per listing limit
                    print '<div class="pack_description_unit pack_description_details">';
                    print ' '.$pack_image_included;
                    print '<p class="package_label">'.esc_html__('Images / per listing','wpresidence-core').'</p></div>';
    
                    // Display package expiration date
                    print '<div class="pack_description_unit pack_description_details">';
                    print ' '.$expired_date;
                    print '<p class="package_label">'.esc_html__('Ends On','wpresidence-core').'</p></div>';
    
                }else{
                    // User has a free membership - get free package settings
                    $free_mem_list      =   esc_html( wpresidence_get_option('wp_estate_free_mem_list','') );
                    $free_feat_list     =   esc_html( wpresidence_get_option('wp_estate_free_feat_list','') );
                    $free_mem_list_unl  =   wpresidence_get_option('wp_estate_free_mem_list_unl', '' );
                    $free_pack_image_included  =  esc_html( wpresidence_get_option('wp_estate_free_pack_image_included ','') );
                    print '<div class="pack_description_unit_head"><h4>'.esc_html__('Your Current Package:','wpresidence-core').'</h4>
                          <span class="pack-name">'.esc_html__('Free Membership','wpresidence-core').'</span></div>';
    
                    // Display free membership details
                    print '<div class="pack_description_unit pack_description_details">';
                    if($free_mem_list_unl==1){
                        print esc_html__('  unlimited','wpresidence-core');
                    }else{
                        print ' '.$free_mem_list;
                    }
                    print '<p class="package_label">'.esc_html__('Listings Included','wpresidence-core').'</p></div>';
    
                    print '<div class="pack_description_unit pack_description_details">';
                    print '<span id="normal_list_no"> '.$remaining_lists.'</span>';
                    print '<p class="package_label">'.esc_html__('Listings Remaining','wpresidence-core').'</p></div>';
    
                    print '<div class="pack_description_unit pack_description_details">';
                    print '<span id="normal_list_no"> '.$free_feat_list.'</span>';
                    print '<p class="package_label">'.esc_html__('Featured Included','wpresidence-core').'</p></div>';
    
                    print '<div class="pack_description_unit pack_description_details">';
                    print '<span id="featured_list_no"> '.wpestate_get_remain_featured_listing_user($userID).'</span>';
                    print '<p class="package_label">'.esc_html__('Featured Remaining','wpresidence-core').'</p></div>';
    
                    print '<div class="pack_description_unit pack_description_details">';
                    print '<span id="free_pack_image_included"> '.$free_pack_image_included.'</span>';
                    print '<p class="package_label">'.esc_html__('Images / listing','wpresidence-core').'</p></div>';
    
                    // Free packages don't have an expiration date
                    print '<div class="pack_description_unit pack_description_details">';
                    print '&nbsp;<p class="package_label">'.esc_html__('Ends On: -','wpresidence-core').'</p></div>';
    
                }
                print '</div></div>';
    
    }
    endif; // end   wpestate_get_pack_data_for_user_top
    


    

////////////////////////////////////////////////////////////////////////////////
/// Get a package details from user
////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_get_pack_data_for_user') ):
    /**
     * Displays membership package details in the user dashboard
     * 
     * Generates a simplified HTML display of the user's current package information
     * for the dashboard area, showing basic package details with a more compact layout.
     *
     * @param int $userID User ID to retrieve package data for
     * @param int|string $user_pack Package ID the user is subscribed to (empty if free)
     * @param string $user_registered User registration date
     * @param string $user_package_activation Date when the package was activated
     * @return void Outputs HTML directly
     */
    function wpestate_get_pack_data_for_user($userID,$user_pack,$user_registered,$user_package_activation){
    
                if ($user_pack!=''){
                    // User has a paid package - collect basic package details
                    $title              = get_the_title($user_pack);
                    $pack_time          = get_post_meta($user_pack, 'pack_time', true);
                    $pack_list          = get_post_meta($user_pack, 'pack_listings', true);
                    $pack_featured      = get_post_meta($user_pack, 'pack_featured_listings', true);
                    $pack_price         = get_post_meta($user_pack, 'pack_price', true);
    
                    $unlimited_lists    = get_post_meta($user_pack, 'mem_list_unl', true);
                    print'<div class="user_dashboard_box">';
                    print '<strong>'.esc_html__('Your Current Package: ','wpresidence-core').'</strong></br><strong>'.$title.'</strong></br> ';
                    print '<p class="full_form-nob">';
                    
                    // Show different text for unlimited vs limited listings
                    if($unlimited_lists==1){
                        print esc_html__('  Unlimited listings','wpresidence-core');
                    }else{
                        print $pack_list.esc_html__(' Listings','wpresidence-core');
                        print ' - '.wpestate_get_remain_listing_user($userID,$user_pack).esc_html__(' remaining ','wpresidence-core').'</p>';
                    }
    
                    // Display featured listings info
                    print ' <p class="full_form-nob"> <span id="normal_list_no">'.$pack_featured.esc_html__(' Featured listings','wpresidence-core').'</span>';
                    print ' - <span id="featured_list_no">'.wpestate_get_remain_featured_listing_user($userID).'</span>'.esc_html__(' remaining','wpresidence-core').' </p>';
                    print'</div>';
    
                }else{
                    // User has a free membership - get free package settings
                    $free_mem_list      =   esc_html( wpresidence_get_option('wp_estate_free_mem_list','') );
                    $free_feat_list     =   esc_html( wpresidence_get_option('wp_estate_free_feat_list','') );
                    $free_mem_list_unl  =   wpresidence_get_option('wp_estate_free_mem_list_unl', '' );
                    print'<div class="user_dashboard_box">';
                    print '<strong>'.esc_html__('Your Current Package: ','wpresidence-core').'</strong></br><strong>'.esc_html__('Free Membership','wpresidence-core').'</strong>';
                    print '<p class="full_form-nob">';
                    
                    // Show different text for unlimited vs limited listings
                    if($free_mem_list_unl==1){
                         print esc_html__('Unlimited listings','wpresidence-core');
                    }else{
                         print $free_mem_list.esc_html__(' Listings','wpresidence-core');
                         print ' - <span id="normal_list_no">'.wpestate_get_remain_listing_user($userID,$user_pack).'</span>'.esc_html__(' remaining','wpresidence-core').'</p>';
    
                    }
                    
                    // Display featured listings info for free accounts
                    print '<p class="full_form-nob">';
                    print $free_feat_list.esc_html__(' Featured listings','wpresidence-core');
                    print ' - <span id="featured_list_no">'.wpestate_get_remain_featured_listing_user($userID).'</span>'.esc_html__('  remaining','wpresidence-core').' </p>';
                    print'</div>';
    
                }
    
    }
    endif; // end   wpestate_get_pack_data_for_user
    
    
    
    

  