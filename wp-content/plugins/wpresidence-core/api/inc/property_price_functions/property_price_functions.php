<?php 



/**
 * Format price of property
 *
 * Formats numeric property prices according to theme settings.
 * Handles different number formatting options including:
 * - Custom thousand separators
 * - Decimal points and separators
 * - Short price format (e.g., 1.2M instead of 1,200,000)
 * - Indian number format (lakhs and crores)
 *
 * @param float $price The price to format
 * @param string $th_separator Thousands separator character
 * @return string Formatted price (without currency symbol)
 * @since 1.0.0
 */
if (!function_exists('wpestate_format_number_price')):

    function wpestate_format_number_price($price, $th_separator) {
        // Ensure price is a floating point number
        $price = floatval($price);
        
        // Get theme settings for decimal points and separator
        $decimal_points       =  intval(  wpresidence_get_option('wp_estate_prices_decimal_poins','') );
        $decimal_separator    =  esc_html(  wpresidence_get_option('wp_estate_prices_decimal_poins_separator','') );
        
        // Check if short price format is enabled (e.g., 1.2M instead of 1,200,000)
        $use_short_price= wpresidence_get_option('wp_estate_use_short_like_price','');
         
           


        // Check if Indian price format is enabled (e.g., 10,00,000 instead of 1,000,000)
        $indian_format = esc_html(wpresidence_get_option('wp_estate_price_indian_format', ''));
        if ($indian_format == 'yes') {
            if($use_short_price=='no'){
                // Format in standard Indian number format (e.g., 10,00,000)
                $price = wpestate_moneyFormatIndia($price);
            }else{
                // Format in short Indian number format (e.g., 10 lakh)
                $price = wpestate_moneyFormatIndia_short($price);
            }
        } else {
            if($use_short_price=='no'){
                // Standard international number format
                if ($price == intval($price)) {
                    // If price is a whole number, don't show decimal points
                    $price = number_format($price, 0, '.', $th_separator);
                } else {
                    // If price has decimals, format according to settings
                    $price = number_format($price, $decimal_points,  $decimal_separator , $th_separator);
                }
                
            }else{
               // Use short price format (K, M, B)
                $price= wpestate_price_short_converter($price);
            }
        }

        return $price;
    }

endif;

/**
 * Convert price to Indian numbering format
 *
 * Formats numbers according to the Indian numbering system where:
 * - Last 3 digits are separated by a comma
 * - All other digits are grouped in pairs from right to left
 * Example: 10,00,000 instead of 1,000,000
 *
 * @param float|int $num The number to format
 * @return string Number formatted in Indian system
 * @since 1.0.0
 */
function wpestate_moneyFormatIndia($num) {
    $explrestunits = "";
    if (strlen($num) > 3) {
        // Extract the last three digits
        $lastthree = substr($num, strlen($num) - 3, strlen($num));
        
        // Extract all digits except the last three
        $restunits = substr($num, 0, strlen($num) - 3);
        
        // Add leading zero if necessary to maintain pairs
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits;
        
        // Split the remaining digits into pairs
        $expunit = str_split($restunits, 2);
        
        // Process each pair and add commas
        for ($i = 0; $i < sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if ($i == 0) {
                $explrestunits .= (int) $expunit[$i] . ","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i] . ",";
            }
        }
        
        // Combine the formatted parts
        $thecash = $explrestunits . $lastthree;
    } else {
        // For numbers with 3 or fewer digits, no formatting needed
        $thecash = $num;
    }
    
    return $thecash; // returns the formatted value
}

/**
 * Convert price to Indian short format
 *
 * Formats numbers according to the Indian numbering system using words:
 * - hundred (100s)
 * - thousand (1,000s)
 * - lakh (100,000s)
 * - crore (10,000,000s)
 *
 * @param float|int $number The number to format
 * @return string Number formatted with Indian words (e.g., "5.25 lakh")
 * @since 1.0.0
 */
function wpestate_moneyFormatIndia_short($number) {
 
    if($number == 0) {
        return ' ';
    }else {
        // Determine format based on number of digits
        $number_length =  strlen($number); 
        switch ($number_length) {
            // 3-digit number (hundreds)
            case 3:
                $val = $number/100;
                $val = round($val, 2);
                $finalval =  $val ." hundred";
                break;
                
            // 4-digit number (thousands)
            case 4:
                $val = $number/1000;
                $val = round($val, 2);
                $finalval =  $val ." thousand";
                break;
                
            // 5-digit number (tens of thousands)
            case 5:
                $val = $number/1000;
                $val = round($val, 2);
                $finalval =  $val ." thousand";
                break;
                
            // 6-digit number (lakhs)
            case 6:
                $val = $number/100000;
                $val = round($val, 2);
                $finalval =  $val ." lakh";
                break;
                
            // 7-digit number (tens of lakhs)
            case 7:
                $val = $number/100000;
                $val = round($val, 2);
                $finalval =  $val ." lakh";
                break;
                
            // 8-digit number (crores)
            case 8:
                $val = $number/10000000;
                $val = round($val, 2);
                $finalval =  $val ." crore";
                break;
                
            // 9-digit number (tens of crores)
            case 9:
                $val = $number/10000000;
                $val = round($val, 2);
                $finalval =  $val ." crore";
                break;
                
            // For larger numbers, continue using crores
            default:
                $val = $number/10000000;
                $val = round($val, 2);
                $finalval =  $val ." crore";
                break;
        }
        return $finalval;
    }
}

/**
 * Function to hide or show property price based on global and local settings
 *
 * Checks global hide/show price setting and local property meta to determine if the price should be displayed.
 * If hidden, it outputs a custom label instead of the price.
 *
 * @param int $post_id The property post ID
 * @param int $return Whether to return (1) or print (0) the price label
 * @return void|string Outputs or returns the formatted price label
 * @since 1.0.0
 */
if ( !function_exists( 'wpestate_hide_show_price' ) )   :
    function wpestate_hide_show_price( $post_id ) {

        // Get the global hide price setting
        $globalhideprice = wpresidence_get_option('wp_estate_show_hide_price', '');

        // Get the local hide price setting for the specific property
        $localhideprice = get_post_meta( $post_id, 'local_show_hide_price', true );

        // Determine if the price should be hidden
        $hidingprice = false;
        if ( $globalhideprice == 'yes' ) {
            $hidingprice = true;
            if ( $localhideprice == 'no' ) {
                $hidingprice = false;
            }
        } else if ( $globalhideprice == 'no' ) {
            $hidingprice = false;
            if ( $localhideprice == 'yes' ) {
                $hidingprice = true;
            }
        }

        return $hidingprice;

    }
endif;


/**
 * Function to display property price throughout the template
 *
 * Formats and displays property price with currency symbol and optional labels.
 * Handles multi-currency support via cookies and different currency positions.
 * Can display either primary or secondary property price based on parameters.
 *
 * @param int $post_id The property post ID
 * @param string $wpestate_currency Currency symbol to display
 * @param string $where_currency Position of currency symbol ('before' or 'after')
 * @param int $return Whether to return (1) or print (0) the price
 * @param string $second Whether to use secondary price ('yes' or 'no')
 * @return string|void Formatted price if $return=1, otherwise outputs directly
 * @since 1.0.0
 */
if (!function_exists('wpestate_show_price')):
    function wpestate_show_price($post_id, $wpestate_currency, $where_currency, $return = 0,$second="no") {

        $hidingprice = wpestate_hide_show_price( $post_id );

        if ( $hidingprice ) {
            $hiddenPriceLabel = wpresidence_get_option( 'wp_estate_property_hide_price_text', '', 'Price on application' );
            // If price is hidden, return empty string or print nothing
            if ($return == 0) {
                print '<span class="price_label">' . esc_html($hiddenPriceLabel) . '</span>';
                return;
            } else {
                return '<span class="price_label">' . esc_html($hiddenPriceLabel) . '</span>';
            }
        }

        // Get the price label from post meta (text displayed after the price)
        $price_label = '<span class="price_label">' . esc_html(get_post_meta($post_id, 'property_label', true)) . '</span>';
        
        // Get the "before price" label from post meta (text displayed before the price)
        $price_label_before = get_post_meta($post_id, 'property_label_before', true);
        if ($price_label_before != '') {
            $price_label_before = '<span class="price_label price_label_before">' . esc_html($price_label_before) . '</span>';
        }
        
        // Get the main property price and convert to float
        $price = floatval(get_post_meta($post_id, 'property_price', true));
        
        // If showing secondary price, get the secondary price and its labels
        if($second=='yes'){
            $price_label = '<span class="price_label">' . esc_html(get_post_meta($post_id, 'property_second_price_label', true)) . '</span>';
            $price_label_before = get_post_meta($post_id, 'property_label_before_second_price', true);
            if ($price_label_before != '') {
                $price_label_before = '<span class="price_label price_label_before">' . esc_html($price_label_before) . '</span>';
            }
            $price = floatval(get_post_meta($post_id, 'property_second_price', true));
        }
        
        // Get thousands separator from theme options
        $th_separator = stripslashes(wpresidence_get_option('wp_estate_prices_th_separator', ''));
        
        // Get multi-currency settings from theme options
        $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
        
        // Check if user has selected a custom currency (via cookies)
        if (!empty($custom_fields) && isset($_COOKIE['my_custom_curr']) && isset($_COOKIE['my_custom_curr_pos']) && isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos'] != -1) {
            // Get the position in the currency array
            $i = intval($_COOKIE['my_custom_curr_pos']);
            $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
            
            if ($price != 0) {
                // Convert price to selected currency using exchange rate
                $price = $price * $custom_fields[$i][2];
                
                // Format number with proper thousands separator
                $price = wpestate_format_number_price($price, $th_separator);
                
                // Use the selected currency symbol
                $wpestate_currency = $custom_fields[$i][0];
                
                // Position the currency symbol based on settings
                if ($custom_fields[$i][3] == 'before') {
                    $price = $wpestate_currency . $price;
                } else {
                    $price = $price . $wpestate_currency;
                }
            } else {
                $price = '';
            }
        } else {
            // Standard price display without currency conversion
            if ($price != 0) {
                // Format number with proper thousands separator
                $price = wpestate_format_number_price($price, $th_separator);
                
                // Position currency symbol based on parameter
                if ($where_currency == 'before') {
                    $price = $wpestate_currency . $price;
                } else {
                    $price = $price . $wpestate_currency;
                }
            } else {
                $price = '';
            }
        }
        
        // Combine all parts: before label, price, and after label
        $formatted_price = implode(' ', array_filter([$price_label_before, $price, $price_label], 'strlen'));
        
        // Either print or return the formatted price
        if ($return == 0) {
            print $formatted_price;
        } else {
            return $formatted_price;
        }
    }
endif;




/**
 * Displays property price using cached data
 *
 * This is the cached version of wpestate_show_price() that uses pre-fetched property data 
 * instead of making multiple database calls with get_post_meta(). It formats and displays 
 * property prices with proper currency symbols, labels, and supports multi-currency 
 * conversion through cookies.
 *
 * The function maintains the same output formatting as wpestate_show_price() but offers 
 * significant performance improvements when displaying multiple property listings by 
 * using the cached property data array.
 *
 * @param array $property_unit_cached_data Array of cached property data containing all meta values
 * @param string $wpestate_currency Currency symbol to display
 * @param string $where_currency Position of currency symbol ('before' or 'after')
 * @param int $return Whether to return (1) or print (0) the price
 * @param string $second Whether to use secondary price ('yes' or 'no')
 * @return string|void Formatted price if $return=1, otherwise outputs directly
 * @since 4.0.0
 * @see wpestate_show_price() The non-cached version of this function
 */
if (!function_exists('wpestate_show_price_from_cache')):
    function wpestate_show_price_from_cache($property_unit_cached_data, $wpestate_currency, $where_currency, $return = 0, $second="no") {

        // Get property ID from cached data or fallback to post ID if needed
        $prop_id = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, '', '', 'ID');

        $hidingprice = wpestate_hide_show_price( $prop_id );

        if ( $hidingprice ) {
            $hiddenPriceLabel = wpresidence_get_option( 'wp_estate_property_hide_price_text', '', 'Price on application' );
            // If price is hidden, return empty string or print nothing
            if ($return == 0) {
                print '<span class="price_label">' . esc_html($hiddenPriceLabel) . '</span>';
                return;
            } else {
                return '<span class="price_label">' . esc_html($hiddenPriceLabel) . '</span>';
            }
        }
       
        // Get price label from cache
        $price_label = '<span class="price_label">' . esc_html(wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $prop_id, 'meta', 'property_label')) . '</span>';
       
        // Get before price label from cache
        $price_label_before = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $prop_id, 'meta', 'property_label_before');
        if ($price_label_before != '') {
            $price_label_before = '<span class="price_label price_label_before">' . esc_html($price_label_before) . '</span>';
        }
       
        // Get price from cache
        $price = floatval(wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $prop_id, 'meta', 'property_price'));
       
        // Handle second price if requested
        if($second == 'yes'){
            $price_label = '<span class="price_label">' . esc_html(wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $prop_id, 'meta', 'property_second_price_label')) . '</span>';
            $price_label_before = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $prop_id, 'meta', 'property_label_before_second_price');
            if ($price_label_before != '') {
                $price_label_before = '<span class="price_label price_label_before">' . esc_html($price_label_before) . '</span>';
            }
            $price = floatval(wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $prop_id, 'meta', 'property_second_price'));
        }
       
        // Get thousands separator from theme options
        $th_separator = stripslashes(wpresidence_get_option('wp_estate_prices_th_separator', ''));
       
        // Handle custom currency from cookies if available
        $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
        if (!empty($custom_fields) && isset($_COOKIE['my_custom_curr']) && isset($_COOKIE['my_custom_curr_pos']) && isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos'] != -1) {
            $i = intval($_COOKIE['my_custom_curr_pos']);
            $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
            if ($price != 0) {
                $price = $price * $custom_fields[$i][2];
                $price = wpestate_format_number_price($price, $th_separator);
                $wpestate_currency = $custom_fields[$i][0];
                if ($custom_fields[$i][3] == 'before') {
                    $price = $wpestate_currency . $price;
                } else {
                    $price = $price . $wpestate_currency;
                }
            } else {
                $price = '';
            }
        } else {
            // Standard price formatting
            if ($price != 0) {
                $price = wpestate_format_number_price($price, $th_separator);
                if ($where_currency == 'before') {
                    $price = $wpestate_currency . $price;
                } else {
                    $price = $price . $wpestate_currency;
                }
            } else {
                $price = '';
            }
        }
       
        // Generate the final price display with labels
        $final_price = implode(' ', array_filter([$price_label_before, $price, $price_label], 'strlen'));
       
        // Either return or print the result
        if ($return == 0) {
            print $final_price;
        } else {
            return $final_price;
        }
    }
endif;




/**
 * Display property price with all details
 *
 * Formats and displays property price with labels, currency symbols, and proper formatting.
 * This function can handle both direct database queries and pre-fetched property details.
 * It supports both primary and secondary property prices based on parameters.
 *
 * @param int $post_id The property post ID
 * @param string $wpestate_currency Currency symbol to display
 * @param string $where_currency Position of currency symbol ('before' or 'after')
 * @param int $return Whether to return (1) or print (0) the price
 * @param array $wpestate_prop_all_details Optional pre-fetched property details array
 * @param string $second Whether to use secondary price ('yes' or 'no')
 * @return string|void Formatted price if $return=1, otherwise outputs directly
 * @since 1.0.0
 */
if (!function_exists('wpestate_show_price_from_all_details')):

    function wpestate_show_price_from_all_details($post_id,$wpestate_currency, $where_currency, $return = 0, $wpestate_prop_all_details = '',$second='no') {

        $hidingprice = wpestate_hide_show_price( $post_id );

        if ( $hidingprice ) {
            $hiddenPriceLabel = wpresidence_get_option( 'wp_estate_property_hide_price_text', '', 'Price on application' );
            // If price is hidden, return empty string or print nothing
            if ($return == 0) {
                print '<span class="price_label">' . esc_html($hiddenPriceLabel) . '</span>';
                return;
            } else {
                return '<span class="price_label">' . esc_html($hiddenPriceLabel) . '</span>';
            }
        }

        // Get price label and "before price" label from post meta
        $price_label            =   get_post_meta($post_id, 'property_label', true) ;
        $price_label_before     =   get_post_meta($post_id, 'property_label_before', true) ;
        $price              =  floatval(get_post_meta($post_id, 'property_price', true));
    
        // If showing secondary price, get the secondary price and its labels
        if($second=='yes'){
            $price_label            = get_post_meta($post_id, 'property_second_price_label', true);
            $price_label_before     = get_post_meta($post_id, 'property_label_before_second_price', true);
            $price = floatval(get_post_meta($post_id, 'property_second_price', true));
        }

        // Format price label with HTML span
        $price_label = '<span class="price_label">' . esc_html($price_label) . '</span>';

        // Format "before price" label with HTML span if it exists
        if ($price_label_before != '') {
            $price_label_before = '<span class="price_label price_label_before">' . esc_html($price_label_before) . '</span>';
        }

        // Get thousands separator from theme options
        $th_separator = stripslashes(wpresidence_get_option('wp_estate_prices_th_separator', ''));
        
        // Get multi-currency settings from theme options
        $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');

        // Check if user has selected a custom currency (via cookies)
        if (!empty($custom_fields) && isset($_COOKIE['my_custom_curr']) && isset($_COOKIE['my_custom_curr_pos']) && isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos'] != -1) {
            $i = intval($_COOKIE['my_custom_curr_pos']);
            $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
            if ($price != 0) {
                // Convert price to selected currency using exchange rate
                $price = $price * $custom_fields[$i][2];
                $price = wpestate_format_number_price($price, $th_separator);

                // Use the selected currency symbol
                $wpestate_currency = $custom_fields[$i][0];

                // Position currency symbol based on settings
                if ($custom_fields[$i][3] == 'before') {
                    $price = $wpestate_currency . $price;
                } else {
                    $price = $price . $wpestate_currency;
                }
            } else {
                $price = '';
            }
        } else {
            // Standard price display without currency conversion
            if ($price != 0) {
                // Format number with proper thousands separator
                $price = wpestate_format_number_price($price, $th_separator);

                // Position currency symbol based on parameter
                if ($where_currency == 'before') {
                    $price = $wpestate_currency . $price;
                } else {
                    $price = $price . $wpestate_currency;
                }
            } else {
                $price = '';
            }
        }

        // Combine all parts: before label, price, and after label
        $formatted_price = implode(' ', array_filter([$price_label_before, $price, $price_label], 'strlen'));
        
        // Either print or return the formatted price
        if ($return == 0) {
            print $formatted_price;
        } else {
            return $formatted_price;
        }
    }

endif;


/**
 * Display formatted price for floor plans
 *
 * Formats and displays a price value specifically for floor plans.
 * Unlike the main price display functions, this one doesn't include labels
 * and focuses only on the numeric value with currency.
 *
 * @param float $price The price value to format
 * @param string $wpestate_currency Currency symbol to display
 * @param string $where_currency Position of currency symbol ('before' or 'after')
 * @param int $return Whether to return (1) or print (0) the price
 * @return string|void Formatted price if $return=1, otherwise outputs directly
 * @since 1.0.0
 */
if (!function_exists('wpestate_show_price_floor')):

    function wpestate_show_price_floor($price, $wpestate_currency, $where_currency, $return = 0) {

        // Get thousands separator from theme options
        $th_separator = stripslashes(wpresidence_get_option('wp_estate_prices_th_separator', ''));
        
        // Get multi-currency settings from theme options
        $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');

        // Check if user has selected a custom currency (via cookies)
        if (!empty($custom_fields) && isset($_COOKIE['my_custom_curr']) && isset($_COOKIE['my_custom_curr_pos']) && isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos'] != -1) {
            $i = intval($_COOKIE['my_custom_curr_pos']);
            $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
            if ($price != 0) {
                // Convert price to selected currency using exchange rate
                $price = $price * $custom_fields[$i][2];

                // Format number with proper thousands separator
                $price = wpestate_format_number_price($price, $th_separator);
                
                // Use the selected currency symbol
                $wpestate_currency = $custom_fields[$i][0];

                // Position currency symbol based on settings
                if ($custom_fields[$i][3] == 'before') {
                    $price = $wpestate_currency . $price;
                } else {
                    $price = $price . $wpestate_currency;
                }
            } else {
                $price = '';
            }
        } else {
            // Standard price display without currency conversion
            if ($price != 0) {
                // Format number with proper thousands separator
                $price = wpestate_format_number_price($price, $th_separator);

                // Position currency symbol based on parameter
                if ($where_currency == 'before') {
                    $price = $wpestate_currency . $price;
                } else {
                    $price = $price . $wpestate_currency;
                }
            } else {
                $price = '';
            }
        }

        // Either print or return the formatted price
        if ($return == 0) {
            print $price;
        } else {
            return $price;
        }
    }

endif;




/**
 * Format and display price specifically for invoices
 *
 * This function formats a price value for invoice display, with simplified currency handling.
 * Unlike the main price functions, this does not support multi-currency conversion and
 * specifically formats numbers with 2 decimal places. Can optionally wrap the price in a 
 * special span with the "inv_data_value" class for invoice styling.
 *
 * @param float $price The numeric price value to format
 * @param string $wpestate_currency Currency symbol to display
 * @param string $where_currency Position of currency symbol ('before' or 'after')
 * @param int $has_data Whether to wrap price in special span (1) or not (0)
 * @param int $return Whether to return (1) or print (0) the result
 * @return string|void Formatted price if $return=1, otherwise outputs directly
 * @since 1.0.0
 */
if (!function_exists('wpestate_show_price_booking_for_invoice')):
    function wpestate_show_price_booking_for_invoice($price, $wpestate_currency, $where_currency, $has_data = 0, $return = 0) {
        // Initialize empty price label (invoices don't use labels like property listings)
        $price_label = '';
        
        // Get thousands separator from theme options
        $th_separator = wpresidence_get_option('wp_estate_prices_th_separator', '');
        
        // Get multi-currency settings (not used in this function but kept for consistency with other price functions)
        $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
        
        if ($price != 0) {
            // Convert to float to ensure proper number formatting
            $price = floatval($price);
            
            // Format with 2 decimal places and thousands separator
            $price = number_format(($price), 2, '.', $th_separator);
            
            // Wrap in special span if has_data parameter is set
            if ($has_data == 1) {
                $price = '<span class="inv_data_value">' . $price . '</span>';
            }
            
            // Position currency symbol based on parameter
            if ($where_currency == 'before') {
                $price = $wpestate_currency . $price;
            } else {
                $price = $price . $wpestate_currency;
            }
        } else {
            // Return empty string for zero price
            $price = '';
        }
        
        // Generate final price string with any label (empty in this case)
            $formatted_price = implode(' ', array_filter([$price, $price_label], 'strlen'));
        
        // Either print or return the formatted price
        if ($return == 0) {
            print $formatted_price;
        } else {
            return $formatted_price;
        }
    }
endif;




/**
 * Convert property price to short format for map pins
 *
 * Transforms numeric prices into abbreviated formats:
 * - Values between 10K and 1M are converted to K format (e.g., 500000 -> 500K)
 * - Values over 1M are converted to M format (e.g., 2500000 -> 2.5M)
 * This function is used for displaying compact prices on map pins.
 * 
 * Note: While this function loads multi-currency settings, it doesn't actually
 * apply the conversion factor (commented out code).
 *
 * @param float $pin_price The numeric price to convert
 * @return string Abbreviated price format with K or M suffix
 * @since 1.0.0
 */
if (!function_exists('wpestate_price_short_converter')):
    function wpestate_price_short_converter($pin_price) {
        // Get multi-currency settings
        $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
        
        // Check if user has selected a custom currency
        if (!empty($custom_fields) && isset($_COOKIE['my_custom_curr']) && isset($_COOKIE['my_custom_curr_pos']) && isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos'] != -1) {
            $i = intval($_COOKIE['my_custom_curr_pos']);
            $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
            if ($pin_price != 0) {
               // $pin_price = $pin_price * $custom_fields[$i][2];  // Note: This line is commented out in the original
                $wpestate_currency = $custom_fields[$i][0];
                $where_currency = $custom_fields[$i][3];
            } else {
                $pin_price = '';
            }
        }
        
        // Ensure price is a float for proper comparison
        $pin_price = floatval($pin_price);
        
        // For values between 10,000 and 1,000,000, convert to K format
        if (10000 < $pin_price && $pin_price < 1000000) {
            $pin_price = round($pin_price / 1000, 1);
            $pin_price = $pin_price . '' . esc_html__('K', 'wpresidence-core');
        } 
        // For values over 1,000,000, convert to M format
        else if ($pin_price >= 1000000) {
            $pin_price = round($pin_price / 1000000, 1);
            $pin_price = $pin_price . '' . esc_html__('M', 'wpresidence-core');
        }
        
        return $pin_price;
    }
endif;

/**
 * Convert property price to short format for map pins with currency
 *
 * Similar to wpestate_price_short_converter() but includes currency symbols
 * and fully supports multi-currency conversion. Transforms numeric prices 
 * into abbreviated formats (K, M) and positions the currency symbol according
 * to settings.
 *
 * @param float $pin_price The numeric price to convert
 * @param string $where_currency Position of currency symbol ('before' or 'after')
 * @param string $wpestate_currency Currency symbol to display
 * @param string $simple Whether to include currency symbol (empty) or not ('simple')
 * @return string Abbreviated price format with currency symbol and K or M suffix
 * @since 1.0.0
 */
if (!function_exists('wpestate_price_pin_converter')):
    function wpestate_price_pin_converter($pin_price, $where_currency, $wpestate_currency, $simple='') {
        // Get multi-currency settings
        $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
        
        // Check if user has selected a custom currency
        if (!empty($custom_fields) && isset($_COOKIE['my_custom_curr']) && isset($_COOKIE['my_custom_curr_pos']) && isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos'] != -1) {
            $i = intval($_COOKIE['my_custom_curr_pos']);
            $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
            if ($pin_price != 0) {
                // Convert price using exchange rate
                $pin_price = $pin_price * $custom_fields[$i][2];
                
                // Use the selected currency symbol and position
                $wpestate_currency = $custom_fields[$i][0];
                $where_currency = $custom_fields[$i][3];
            } else {
                $pin_price = '';
            }
        }
        
        // Ensure price is a float for proper comparison
        $pin_price = floatval($pin_price);
        
        // For values between 10,000 and 1,000,000, convert to K format
        if (10000 < $pin_price && $pin_price < 1000000) {
            $pin_price = round($pin_price / 1000, 1);
            $pin_price = $pin_price . '' . esc_html__('K', 'wpresidence-core');
        } 
        // For values over 1,000,000, convert to M format
        else if ($pin_price >= 1000000) {
            $pin_price = round($pin_price / 1000000, 1);
            $pin_price = $pin_price . '' . esc_html__('M', 'wpresidence-core');
        }
        
        // Add currency symbol if not in simple mode
        if($simple==''):
            // Position currency symbol based on settings
            if ($where_currency == 'before') {
                $pin_price = $wpestate_currency . $pin_price;
            } else {
                $pin_price = $pin_price . $wpestate_currency;
            }
        endif;
        
        return $pin_price;
    }
endif;