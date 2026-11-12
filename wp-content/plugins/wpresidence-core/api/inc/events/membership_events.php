<?php 

add_filter( 'cron_schedules', 'wpestate_add_weekly_cron_schedule' );

/**
 * Adds a weekly interval to WordPress cron schedules
 * 
 * @param array $schedules Existing WordPress cron schedules
 * @return array Modified schedules with weekly interval added
 */
if( !function_exists('wpestate_add_weekly_cron_schedule') ): 
    function wpestate_add_weekly_cron_schedule( $schedules ) {
        $schedules['weekly'] = array(
            'interval' => 604800, // 1 week in seconds
            'display'  => esc_html__( 'Once Weekly','wpresidence-core' ),
        );

        return $schedules;
    }
endif;



/**
 * Schedule membership status checks for users
 * 
 * Called when the paid submission setting is changed to membership
 * Clears any existing scheduled hooks and sets up daily user checks
 */
if( !function_exists('wp_estate_schedule_user_check') ): 
    function wp_estate_schedule_user_check(){
        $paid_submission_status    = esc_html ( wpresidence_get_option('wp_estate_paid_submission','') );
        if($paid_submission_status == 'membership' ){
            //  wpestate_check_user_membership_status_function();
            wp_clear_scheduled_hook('wpestate_check_for_users_event');
            wpestate_setup_daily_user_schedule();  
        }
    }
endif;



/**
 * Set up daily schedule for user membership status check
 * 
 * Creates a twice-daily WordPress cron job to check membership statuses
 */
if( !function_exists('wpestate_setup_daily_user_schedule') ): 
    function  wpestate_setup_daily_user_schedule(){
        if ( ! wp_next_scheduled( 'wpestate_check_for_users_event' ) ) {
            //daily
            wp_schedule_event( time(), 'twicedaily', 'wpestate_check_for_users_event');
        }
    }
endif;
// Hook the membership check function to the scheduled event
add_action( 'wpestate_check_for_users_event', 'wpestate_check_user_membership_status_function' );


