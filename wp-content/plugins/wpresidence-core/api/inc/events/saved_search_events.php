<?php


/**
 * Schedule email alerts for saved searches
 * 
 * Sets up either daily or weekly schedule based on theme options
 * for sending property alerts to users with saved searches
 */
if( !function_exists('wp_estate_schedule_email_events') ): 
    function wp_estate_schedule_email_events(){
        $show_save_search            =   wpresidence_get_option('wp_estate_show_save_search','');
        $search_alert                =   intval( wpresidence_get_option('wp_estate_search_alert','') );
       
        //  update_option('wpestate_cron_saved_search','none');
        if($show_save_search=='yes'){
            // Schedule based on frequency setting (0=daily, 1=weekly)
            if ($search_alert==0){ // is daily
                wpestate_setup_daily_schedule();  
            }else {//is weekly
                wpestate_setup_weekly_schedule();
            }

        }else{
                // If save search is disabled, clear scheduled hook
                wp_clear_scheduled_hook('wpestate_check_for_new_listings_event');
                update_option('wpestate_cron_saved_search','none');

        }

    }
endif;






/**
 * Set up daily schedule for checking new listings
 * 
 * Creates a daily WordPress cron job to check for new property listings
 * Only creates if not already scheduled or if schedule type has changed
 */
if( !function_exists('wpestate_setup_daily_schedule') ): 
    function  wpestate_setup_daily_schedule(){
        $schedule =   get_option('wpestate_cron_saved_search',true);
        if ( ! wp_next_scheduled( 'wpestate_check_for_new_listings' ) && $schedule!='daily'  ) {
            wp_clear_scheduled_hook('wpestate_check_for_new_listings_event');
            wp_schedule_event( time(), 'daily', 'wpestate_check_for_new_listings_event');
            update_option('wpestate_cron_saved_search','daily');
        }
    }
endif;



/**
 * Set up weekly schedule for checking new listings
 * 
 * Creates a weekly WordPress cron job to check for new property listings
 * Only creates if not already scheduled or if schedule type has changed
 */
if( !function_exists('wpestate_setup_weekly_schedule') ): 
    function wpestate_setup_weekly_schedule(){
        $schedule =   get_option('wpestate_cron_saved_search',true);
        if ( ! wp_next_scheduled( 'wpestate_check_for_new_listings' ) && $schedule!='weekly' ) {
            //weekly hourly
            wp_clear_scheduled_hook('wpestate_check_for_new_listings_event');
            wp_schedule_event( time(), 'weekly', 'wpestate_check_for_new_listings_event');
            update_option('wpestate_cron_saved_search','weekly');
        }

    }
endif;
// Hook the new listings check function to the scheduled event
add_action( 'wpestate_check_for_new_listings_event', 'wpestate_check_for_new_listings' );





/**
 * Check for new property listings
 * 
 * Queries for new properties based on the alert period (daily/weekly)
 * If new properties are found, runs the saved search check function
 */
if( !function_exists('wpestate_check_for_new_listings') ): 
    function wpestate_check_for_new_listings(){
      
        // Get the date range for the alert period        
        $date_query_array=wpestate_get_alert_period();
        $args = array(
            'post_type'       => 'estate_property',
            'post_status'     => 'publish',
            'posts_per_page'  => -1,
            'date_query'      => $date_query_array

        );
        $prop_selection =   new WP_Query($args);

        if ($prop_selection->have_posts()){    
            // we have new listings - we should compare searches
            wpestate_saved_search_checks();
        }else{
            // No new listings found - nothing to do
        }
        
    }
endif;



/**
 * Process all saved searches and send email alerts
 * 
 * Gets all saved search post objects and compares them against new listings
 * Sends email notifications to users when new matching properties are found
 */
if( !function_exists('wpestate_saved_search_checks') ): 
    function wpestate_saved_search_checks(){
   
           $args = array(
                    'post_type'        => 'wpestate_search',
                    'post_status'      =>  'any',
                    'posts_per_page'   => -1 ,
                );
            $prop_selection = new WP_Query($args);

            if($prop_selection->have_posts()){ 
                // Process each saved search
                while ($prop_selection->have_posts()): $prop_selection->the_post(); 
                    $post_id=get_the_id();
                    $arguments      =   get_post_meta($post_id, 'search_arguments', true) ;
                    $meta_arguments =   get_post_meta($post_id, 'meta_arguments', true) ;
                    $user_email     =   get_post_meta($post_id, 'user_email', true) ;
                    $mail_content   =   wpestate_compose_send_email($arguments,$meta_arguments);
                 
                    // Clean up line breaks in HTML content
                    $mail_content = str_replace("\r\n", '', $mail_content); // For Windows
                    $mail_content = str_replace("\n", '', $mail_content);   // For Unix/Linux
                    $mail_content = str_replace("\r", '', $mail_content);   // For old Macs
                    
                    // Only send email if we have content and a valid email
                    if($user_email!='' && $mail_content!=''){
                        $arguments=array(
                            'matching_submissions' => $mail_content
                        );
                        
                        // Send the email notification
                       wpestate_select_email_type($user_email,'matching_submissions',$arguments);
                        
                    }

                endwhile;

            }

    }
endif;






/**
 * Compose the email content for search alerts
 * 
 * Takes search arguments and metadata, finds matching properties,
 * and generates HTML email content with property listings
 * 
 * @param string $args Search query arguments (JSON string)
 * @param string $meta_arguments Search meta arguments (JSON string)
 * @return string Generated HTML content for email
 */
if( !function_exists('wpestate_compose_send_email') ): 
    function wpestate_compose_send_email($args,$meta_arguments){
        $mail_content=''; 
        $arguments  = objectToArray( json_decode($args) );
        $metas      = objectToArray( json_decode($meta_arguments) );

        // Add date query to limit to recent properties
        $arguments['date_query']=     $date_query_array=wpestate_get_alert_period();

        // Reset post__in parameter
        unset($arguments['post__in']);
     
        // Process meta query arguments if present
        if(!empty($metas) ){
            $meta_ids = wpestate_add_meta_post_to_search($metas);
            if(!empty($meta_ids)){
                $arguments['post__in']=$meta_ids;
            }
        }
        
        // Query for matching properties
        $prop_selection = new WP_Query($arguments);
        if($prop_selection->have_posts()){ 
            // Build email content with each matching property
            while ($prop_selection->have_posts()): $prop_selection->the_post(); 
            
                ob_start();
                // Include property card template
                include( locate_template('templates/property_cards_templates/property_unit_saved_search.php') );
                $mail_content .= ob_get_contents();
                ob_end_clean();

            endwhile;
            $mail_content .='';    
        }else{
            $mail_content='';   
        }
        wp_reset_postdata();
        wp_reset_query();

        return $mail_content;
    }

endif;



/**
 * Recursively convert object to array
 * 
 * Utility function to convert nested objects to arrays
 * 
 * @param mixed $object Object or array to convert
 * @return mixed Converted array or original value if not object/array
 */
if( !function_exists('objectToArray') ): 
    function objectToArray ($object) {
        if(!is_object($object) && !is_array($object))
            return $object;

        return array_map('objectToArray', (array) $object);
    }
endif;




/**
 * Get the date query array for the alert period
 * 
 * Returns a WordPress date query array for either daily or weekly period
 * based on theme settings
 * 
 * @return array Date query array for WP_Query
 */
if( !function_exists('wpestate_get_alert_period') ): 
    function wpestate_get_alert_period(){
         $search_alert = wpresidence_get_option('wp_estate_search_alert','');

        // Set date range based on alert frequency setting
        if( $search_alert==0 ){ // is daily
            $today = getdate();
            $date_query_array=  array(
                                    'after' => '1 day ago'
                                );

        }else{ // is weekly
            $date_query_array=  array(
                                    'after' => '1 week ago'
                                );
        }

        return $date_query_array;
    }
endif;