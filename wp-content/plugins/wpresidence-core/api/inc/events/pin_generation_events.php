<?php

/**
 * Set up daily schedule for map pin generation
 * 
 * Creates a daily WordPress cron job to generate map pins
 */
if( !function_exists('setup_wpestate_cron_generate_pins_daily') ): 
    function setup_wpestate_cron_generate_pins_daily() {
            if ( ! wp_next_scheduled( 'prefix_wpestate_cron_generate_pins_daily' ) ) {
                    wp_schedule_event( time(), 'daily', 'prefix_wpestate_cron_generate_pins_daily');
            }
    }
endif;
// Initialize the pin generation schedule
setup_wpestate_cron_generate_pins_daily();
// Hook the pin generation function to the scheduled event
add_action( 'prefix_wpestate_cron_generate_pins_daily', 'wpestate_cron_generate_pins' );



/**
 * Generate map pins for property listings
 * 
 * Called by the daily cron job to generate map pins if the map system is enabled
 */
if( !function_exists('wpestate_cron_generate_pins') ): 
    function wpestate_cron_generate_pins(){
        if ( wpresidence_get_option('wp_estate_readsys','') =='yes' ){
            // Get the path to the pin file
            $path=wpestate_get_pin_file_path();
            if ( file_exists ($path) && is_writable ($path) ){
                //  wpestate_listing_pins();
                   wpestate_listing_pins_for_file();
            }

        }
    }
endif;



