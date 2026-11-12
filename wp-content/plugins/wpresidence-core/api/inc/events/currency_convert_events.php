<?php 

// the event schedule for wpestate_load_exchange_action is setup in misc\redux_help_functions.php function wprentals_redux_on_save


/**
 * Process and update currency exchange rates
 * 
 * This function retrieves the main currency and all custom currencies defined in the theme options,
 * fetches current exchange rates using the currency converter API, and updates the stored values
 * in the Redux framework options.
 * 
 * @return void
 */
function estate_parse_curency() {

    // Get base currency symbol from theme options
    $base = esc_html( wpresidence_get_option('wp_estate_currency_label_main') );

    // Get custom currency fields from theme options
    $custom_fields = wpresidence_get_option('wpestate_currency');

    // Load conversion rates and round values
    $quotes = array_map(
        function($v){
            return round($v, 6);
        },
        wpestate_currencyconverterapi_load_data($base)
    );

    // Log the full quotes array for debugging
    error_log(print_r($quotes, true));

    // Stop execution if API returned nothing or invalid structure
    if ( empty($quotes) || !is_array($quotes) ) {
        return;
    }

    // Update conversion values in custom currency fields
    $i = 0;
    if ( !empty($custom_fields) ) {
        while ( $i < count($custom_fields) ) {

            // Currency code for the target currency
            $symbol = $custom_fields[$i][1];

            // Set converted value using base and target pair
            $custom_fields[$i][2] = $quotes[$base . $symbol];

            $i++;
        }
    }

    // Prepare Redux structure arrays
    $cur_code = array();
    $cur_label = array();
    $cur_value = array();
    $cur_positin = array();   // Note original typo preserved
    $redux_currency = array();

    // Extract each field into Redux format
    foreach ( $custom_fields as $field ) {
        $cur_code[] = $field[0];
        $cur_label[] = $field[1];
        $cur_value[] = $field[2];
        $cur_positin[] = $field[3];
    }

    // Build Redux option array
    $redux_currency['add_curr_name']  = $cur_code;
    $redux_currency['add_curr_label'] = $cur_label;
    $redux_currency['add_curr_value'] = $cur_value;
    $redux_currency['add_curr_order'] = $cur_positin;

    // Save updated currency settings into Redux
    Redux::set_option('wpresidence_admin', 'wpestate_currency', $redux_currency);
}


function estate_parse_curency2(){
    // Get the main currency code from theme options
    $base = esc_html( wpresidence_get_option('wp_estate_currency_label_main') );
    // Get all custom currencies defined in theme options
    $custom_fields = wpresidence_get_option( 'wpestate_currency', '');    
 
    $i=0;
    // Process each custom currency if any are defined
    if( !empty($custom_fields)){    
        while($i< count($custom_fields) ){
            // Get the symbol (currency code) for the current currency
            $symbol=$custom_fields[$i][0];
       
            // Fetch the current exchange rate from the API and store it in the custom fields array
            $custom_fields[$i][2]=  wpestate_currencyconverterapi_load_data($symbol);
           
            $i++;
        }
    }
    
    // Initialize arrays to hold currency data for Redux framework
    $cur_code=array();
    $cur_label=array();
    $cur_value=array();
    $cur_positin=array();
    $redux_currency=array();
   
    // Extract data from custom fields into separate arrays
    foreach($custom_fields as $field){
        $cur_code[]=$field[0];      // Currency code (e.g., USD, EUR)
        $cur_label[]=$field[1];     // Currency label (e.g., Dollar, Euro)
        $cur_value[]=$field[2];     // Exchange rate value
        $cur_positin[]=$field[3];   // Currency symbol position
    }
   
    // Prepare data for Redux framework in the expected format
    $redux_currency['add_curr_name']=$cur_code;
    $redux_currency['add_curr_label']=$cur_label;
    $redux_currency['add_curr_value']=$cur_value;  
    $redux_currency['add_curr_order']=$cur_positin;
    
    // Update the Redux framework option with new currency data
    Redux::setOption('wpresidence_admin','wpestate_currency', $redux_currency);
}


/**
 * Fetches currency conversion data from CurrencyLayer API.
 * Uses transient caching to avoid repeated remote calls.
 *
 * @param string $source Base currency code.
 * @return array Conversion quotes or empty array on failure.
 */
function wpestate_currencyconverterapi_load_data($source = 'USD') {

    // Attempt to read cached API response
    $quotes = get_transient('wpresidence_currency_remote_data');
   
    // Invalidate cache if stored base currency does not match requested one
    if ($quotes !== false && (!isset($quotes['source']) || $quotes['source'] !== $source)) {
        $quotes = false;
    }

    // Load fresh data if cache is missing or invalid
    if ($quotes === false) {

        // API key from theme options
        $apikey = trim(wpresidence_get_option('wp_estate_currencylayer_api',''));

        // Build request URL
        $link = 'https://api.currencylayer.com/live?access_key=' . $apikey . '&source=' . $source;


        // Execute remote API call
        $data = wp_remote_get($link);

        // Return empty array if request failed
        if (is_wp_error($data)) {
            return array();
        }

        // Decode JSON response
        $data = json_decode($data['body'], true);
     
        // Validate quotes field
        if (!isset($data['quotes']) || !is_array($data['quotes'])) {
            return array();
        }

        // Store the requested base currency
        $data['source'] = $source;

        // Cache full API response for one day
        set_transient('wpresidence_currency_remote_data', $data, DAY_IN_SECONDS);

        $quotes = $data;
    }

    // Final safety check
    if ($quotes === false) {
        return array();
    }

    // Return quotes array only
    return $quotes['quotes'];
}


/**
 * Fetch currency exchange rate data from currencyconverterapi.com
 * 
 * This function connects to the Currency Converter API service to get the current
 * exchange rate between two currencies.
 * 
 * @param string $base   The base currency code (e.g., USD)
 * @param string $symbol The target currency code (e.g., EUR)
 * @return float         The exchange rate value
 */

function wpestate_currencyconverterapi_load_data_old($base, $symbol){
    global $wp_filesystem;
    // Initialize WordPress filesystem if not already initialized
    if (empty($wp_filesystem)) {
        require_once (ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();
    }
     
    // Get API key and version from theme options
    $apikey     =  trim( wpresidence_get_option('wp_estate_currencyconverterapi_api',''));    
    $version    =  trim( wpresidence_get_option('wp_estate_currencyconverterapi_api_free',''));    
     
    // Build API URL based on whether using free or paid version
    // Note: Currently both branches use the same URL structure
    if($version=='free'){
        $link='https://api.currconv.com/api/v8/convert?q='.$base.'_'.$symbol.'&compact=ultra&apiKey='.$apikey;
    }else{
        $link='https://api.currconv.com/api/v8/convert?q='.$base.'_'.$symbol.'&compact=ultra&apiKey='.$apikey;
    }
   
    // Fetch and decode the API response
    $data = (array)json_decode($wp_filesystem->get_contents($link));
 
    // Return the exchange rate value
    // Format example: [USD_EUR] => 0.922375
    return( $data[$base.'_'.$symbol]);
}

/**
 * Schedule the daily exchange rate update
 * 
 * This function sets up a WordPress cron job to update currency exchange rates
 * on a daily basis if it's not already scheduled.
 * 
 * @return void
 */
function wp_estate_enable_load_exchange(){
     if ( ! wp_next_scheduled( 'wpestate_load_exchange_action' ) ) {
            // Schedule daily currency exchange rate updates
            wp_schedule_event( time(), 'daily', 'wpestate_load_exchange_action');
        }
}

// Hook the currency parsing function to the scheduled action
add_action( 'wpestate_load_exchange_action', 'estate_parse_curency' );