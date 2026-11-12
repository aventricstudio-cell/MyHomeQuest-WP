<?php 
/**
 * Unit Conversion Function for WP Estate
 * 
 * This function converts property measurements between different units (feet, meters, acres, yards, hectares).
 * It uses a conversion table with predefined ratios to perform accurate conversions between any two units.
 * The function can work with both the default site measurement unit and user-preferred units (stored in cookies).
 * 
 * @param float $value        The value to be converted
 * @param string $reverse     Flag to indicate if the conversion should be reversed (0 or 1)
 * @return float              The converted measurement value
 */
if (!function_exists('wpestate_convert_measure')):

    function wpestate_convert_measure($value, $reverse = '') {
        $value=floatval($value); // Convert value to float to ensure proper calculation
        
        // First conversion table definition (appears to be redundant as it's redefined below)
        $recalculation_table = array(
            'ftft' => 1,
            'ftm' => 0.092903,
            'ftac' => 0.000022957,
            'ftyd' => 0.111111,
            'ftha' => 0.0000092903,
            'mm' => 1,
            'mft' => 10.7639,
            'mac' => 0.000247105,
            'myd' => 1.19599,
            'mha' => 0.0001,
            'acac' => 1,
            'acft' => 43560,
            'acm' => 4046.86,
            'acyd' => 4840,
            'acha' => 0.404686,
            'ydyd' => 1,
            'ydft' => 9,
            'ydm' => 0.836127,
            'ydac' => 0.000206612,
            'ydha' => 0.000083613,
            'haha' => 1,
            'haft' => 107639,
            'ham' => 10000,
            'haac' => 2.47105,
            'hayd' => 11959.9,
        );

        // Actual conversion table used, with translation-ready unit labels
        $recalculation_table = array(
            esc_html__('ft', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 1,
            esc_html__('ft', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 0.092903,
            esc_html__('ft', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 0.000022957,
            esc_html__('ft', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 0.111111,
            esc_html__('ft', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 0.0000092903,
            esc_html__('m', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 1,
            esc_html__('m', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 10.7639,
            esc_html__('m', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 0.000247105,
            esc_html__('m', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 1.19599,
            esc_html__('m', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 0.0001,
            esc_html__('ac', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 1,
            esc_html__('ac', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 43560,
            esc_html__('ac', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 4046.86,
            esc_html__('ac', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 4840,
            esc_html__('ac', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 0.404686,
            esc_html__('yd', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 1,
            esc_html__('yd', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 9,
            esc_html__('yd', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 0.836127,
            esc_html__('yd', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 0.000206612,
            esc_html__('yd', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 0.000083613,
            esc_html__('ha', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 1,
            esc_html__('ha', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 107639,
            esc_html__('ha', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 10000,
            esc_html__('ha', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 2.47105,
            esc_html__('ha', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 11959.9,
        );

        // Get the default measurement system from theme options
        $basic_measure = esc_html(wpresidence_get_option('wp_estate_measure_sys', ''));
        
        // Check if user has a preferred measurement unit stored in cookies
        if (isset($_COOKIE['my_measure_unit'])) {
            $selected_measure = esc_html($_COOKIE['my_measure_unit']);
        } else {
            $selected_measure = $basic_measure;
        }
        
        // Default conversion value if no conversion needed
        $size_value=1;
        
        // Check if the conversion exists in the table and apply conversion
        if( isset($recalculation_table[( $basic_measure . $selected_measure )]) ){
            $size_value = $value * $recalculation_table[( $basic_measure . $selected_measure )];
        }
     
        // If reverse conversion is requested, perform reverse calculation
        if ($reverse == 1) {
            $size_value = $value * $recalculation_table[$selected_measure . $basic_measure];
        }

        return $size_value;
    }

endif;





/**
 * Display Formatted Converted Measurements for Properties
 * 
 * This function retrieves property size measurements from meta fields,
 * converts them to the appropriate measurement unit based on system settings or user preference,
 * formats the number according to site standards, and returns the formatted value with the appropriate unit symbol.
 * 
 * @param int $post_id                          The ID of the property post
 * @param string $meta_key                      The meta key containing the measurement value
 * @param array $wpestate_prop_all_details      Optional pre-fetched property details
 * @return string|bool                          Formatted measurement with unit or false if no value found
 */
if (!function_exists('wpestate_get_converted_measure')):

    function wpestate_get_converted_measure($post_id, $meta_key, $wpestate_prop_all_details = '') {

        // Get the size value either from passed property details or from post meta
        if ($wpestate_prop_all_details == '') {
            $size_value = get_post_meta($post_id, $meta_key, true);
        } else {
            $size_value = wpestate_return_custom_field($wpestate_prop_all_details, $meta_key);
        }

        // Return false if no size value is found
        if ($size_value == '' || !$size_value) {
            return false;
        }
        
        $size_value = floatval($size_value); // Ensure size is a float for calculations
        
        // Array defining measurement units and their properties
        $measure_array = array(
            array('name' => esc_html__('feet', 'wpresidence-core'), 'unit' => esc_html__('ft', 'wpresidence-core'), 'is_square' => 0),
            array('name' => esc_html__('meters', 'wpresidence-core'), 'unit' => esc_html__('m', 'wpresidence-core'), 'is_square' => 0),
            array('name' => esc_html__('acres', 'wpresidence-core'), 'unit' => esc_html__('ac', 'wpresidence-core'), 'is_square' => 1),
            array('name' => esc_html__('yards', 'wpresidence-core'), 'unit' => esc_html__('yd', 'wpresidence-core'), 'is_square' => 0),
            array('name' => esc_html__('hectares', 'wpresidence-core'), 'unit' => esc_html__('ha', 'wpresidence-core'), 'is_square' => 1),
        );

        // Conversion ratios between different measurement units
        $recalculation_table = array(
            esc_html__('ft', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 1,
            esc_html__('ft', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 0.092903,
            esc_html__('ft', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 0.000022957,
            esc_html__('ft', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 0.111111,
            esc_html__('ft', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 0.0000092903,
            esc_html__('m', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 1,
            esc_html__('m', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 10.7639,
            esc_html__('m', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 0.000247105,
            esc_html__('m', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 1.19599,
            esc_html__('m', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 0.0001,
            esc_html__('ac', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 1,
            esc_html__('ac', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 43560,
            esc_html__('ac', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 4046.86,
            esc_html__('ac', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 4840,
            esc_html__('ac', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 0.404686,
            esc_html__('yd', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 1,
            esc_html__('yd', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 9,
            esc_html__('yd', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 0.836127,
            esc_html__('yd', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 0.000206612,
            esc_html__('yd', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 0.000083613,
            esc_html__('ha', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 1,
            esc_html__('ha', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 107639,
            esc_html__('ha', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 10000,
            esc_html__('ha', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 2.47105,
            esc_html__('ha', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 11959.9,
        );

        // Get the default measurement system from theme options
        $basic_measure = esc_html(wpresidence_get_option('wp_estate_measure_sys', ''));  
        
        // Get separate measurement system for lot size if defined
        $basic_measure_lot_size = esc_html(wpresidence_get_option('wp_estate_measure_sys_lot_size', ''));
        
        // Use lot size measurement for property_lot_size meta if specified
        if($meta_key=='property_lot_size' && $basic_measure_lot_size!=''){
            $basic_measure=$basic_measure_lot_size;
        }
        
        // Check if user has a preferred measurement unit stored in cookies
        if (isset($_COOKIE['my_measure_unit'])) {
            $selected_measure = esc_html($_COOKIE['my_measure_unit']);
        } else {
            $selected_measure = $basic_measure;
        }

        // Determine the appropriate unit display (with superscript for square units)
        $measure_unit = '';
        foreach ($measure_array as $single_unit) {
            if ($single_unit['unit'] == $selected_measure) {
                if ($single_unit['is_square'] === 1) {
                    $measure_unit = $single_unit['unit'];
                } else {
                    $measure_unit = $single_unit['unit'] . '<sup>2</sup>';
                }
            }
        }
        
        // Apply the conversion if available in the table
        if (isset($recalculation_table[$basic_measure . $selected_measure])) {
            $size_value = $size_value * $recalculation_table[$basic_measure . $selected_measure];
        }

        // Format the numeric value according to site standards
        $size_value = wpestate_property_size_number_format($size_value);

        // Return the formatted value with appropriate unit
        return '<span>'.$size_value . ' ' . $measure_unit.'</span>';
    }

endif;


/**
 * Display Formatted Converted Measurements for Properties
 * 
 * This function retrieves property size measurements from meta fields,
 * converts them to the appropriate measurement unit based on system settings or user preference,
 * formats the number according to site standards, and returns the formatted value with the appropriate unit symbol.
 * 
 * @param int $post_id                          The ID of the property post
 * @param string $meta_key                      The meta key containing the measurement value
 * @param array $wpestate_prop_all_details      Optional pre-fetched property details
 * @return string|bool                          Formatted measurement with unit or false if no value found
 */
if (!function_exists('wpestate_get_converted_measure_from_cache')):

    function wpestate_get_converted_measure_from_cache($property_unit_cached_data, $meta_key, $wpestate_prop_all_details = '') {

        // Get the size value either from passed property details or from post meta
        if ($wpestate_prop_all_details == '') {
            $size_value = $property_unit_cached_data['meta'][ $meta_key ] ;
          //  $size_value = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $prop_id, 'meta', $meta_key);
        } else {
            $size_value = wpestate_return_custom_field($wpestate_prop_all_details, $meta_key);
        }

        // Return false if no size value is found
        if ($size_value == '' || !$size_value) {
            return false;
        }
        
        $size_value = floatval($size_value); // Ensure size is a float for calculations
        
        // Array defining measurement units and their properties
        $measure_array = array(
            array('name' => esc_html__('feet', 'wpresidence-core'), 'unit' => esc_html__('ft', 'wpresidence-core'), 'is_square' => 0),
            array('name' => esc_html__('meters', 'wpresidence-core'), 'unit' => esc_html__('m', 'wpresidence-core'), 'is_square' => 0),
            array('name' => esc_html__('acres', 'wpresidence-core'), 'unit' => esc_html__('ac', 'wpresidence-core'), 'is_square' => 1),
            array('name' => esc_html__('yards', 'wpresidence-core'), 'unit' => esc_html__('yd', 'wpresidence-core'), 'is_square' => 0),
            array('name' => esc_html__('hectares', 'wpresidence-core'), 'unit' => esc_html__('ha', 'wpresidence-core'), 'is_square' => 1),
        );

        // Conversion ratios between different measurement units
        $recalculation_table = array(
            esc_html__('ft', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 1,
            esc_html__('ft', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 0.092903,
            esc_html__('ft', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 0.000022957,
            esc_html__('ft', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 0.111111,
            esc_html__('ft', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 0.0000092903,
            esc_html__('m', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 1,
            esc_html__('m', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 10.7639,
            esc_html__('m', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 0.000247105,
            esc_html__('m', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 1.19599,
            esc_html__('m', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 0.0001,
            esc_html__('ac', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 1,
            esc_html__('ac', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 43560,
            esc_html__('ac', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 4046.86,
            esc_html__('ac', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 4840,
            esc_html__('ac', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 0.404686,
            esc_html__('yd', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 1,
            esc_html__('yd', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 9,
            esc_html__('yd', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 0.836127,
            esc_html__('yd', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 0.000206612,
            esc_html__('yd', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 0.000083613,
            esc_html__('ha', 'wpresidence-core') . esc_html__('ha', 'wpresidence-core') => 1,
            esc_html__('ha', 'wpresidence-core') . esc_html__('ft', 'wpresidence-core') => 107639,
            esc_html__('ha', 'wpresidence-core') . esc_html__('m', 'wpresidence-core') => 10000,
            esc_html__('ha', 'wpresidence-core') . esc_html__('ac', 'wpresidence-core') => 2.47105,
            esc_html__('ha', 'wpresidence-core') . esc_html__('yd', 'wpresidence-core') => 11959.9,
        );

        // Get the default measurement system from theme options
        $basic_measure = esc_html(wpresidence_get_option('wp_estate_measure_sys', ''));  
        
        // Get separate measurement system for lot size if defined
        $basic_measure_lot_size = esc_html(wpresidence_get_option('wp_estate_measure_sys_lot_size', ''));
        
        // Use lot size measurement for property_lot_size meta if specified
        if($meta_key=='property_lot_size' && $basic_measure_lot_size!=''){
            $basic_measure=$basic_measure_lot_size;
        }
        
        // Check if user has a preferred measurement unit stored in cookies
        if (isset($_COOKIE['my_measure_unit'])) {
            $selected_measure = esc_html($_COOKIE['my_measure_unit']);
        } else {
            $selected_measure = $basic_measure;
        }

        // Determine the appropriate unit display (with superscript for square units)
        $measure_unit = '';
        foreach ($measure_array as $single_unit) {
            if ($single_unit['unit'] == $selected_measure) {
                if ($single_unit['is_square'] === 1) {
                    $measure_unit = $single_unit['unit'];
                } else {
                    $measure_unit = $single_unit['unit'] . '<sup>2</sup>';
                }
            }
        }
        
        // Apply the conversion if available in the table
        if (isset($recalculation_table[$basic_measure . $selected_measure])) {
            $size_value = $size_value * $recalculation_table[$basic_measure . $selected_measure];
        }

        // Format the numeric value according to site standards
        $size_value = wpestate_property_size_number_format($size_value);

        // Return the formatted value with appropriate unit
        return '<span>'.$size_value . ' ' . $measure_unit.'</span>';
    }

endif;

/**
 * Get Formatted Measurement Unit Display
 * 
 * This function returns the appropriate measurement unit for property sizes,
 * formatted with proper HTML (e.g., adding superscript for square units).
 * It considers both system default and user preferences stored in cookies.
 * 
 * @param int $show_default     Flag to force using the system default unit (1) instead of user preference (0)
 * @return string               The formatted measurement unit (e.g., "ft²", "ac", etc.)
 */
if (!function_exists('wpestate_get_meaurement_unit_formated')):

    function wpestate_get_meaurement_unit_formated($show_default = 0) {
        $measure_unit = '';
        // Get the default measurement system from theme options
        $basic_measure = esc_html(wpresidence_get_option('wp_estate_measure_sys', ''));
        
        // Check if user has a preferred measurement unit stored in cookies
        if (isset($_COOKIE['my_measure_unit'])) {
            $selected_measure = esc_html($_COOKIE['my_measure_unit']);
        } else {
            $selected_measure = $basic_measure;
        }

        // Override with default measure if show_default flag is set
        if ($show_default == 1) {
            $selected_measure = $basic_measure;
        }

        // Array defining measurement units and their properties
        $measure_array = array(
            array('name' => esc_html__('feet', 'wpresidence-core'), 'unit' => esc_html__('ft', 'wpresidence-core'), 'is_square' => 0),
            array('name' => esc_html__('meters', 'wpresidence-core'), 'unit' => esc_html__('m', 'wpresidence-core'), 'is_square' => 0),
            array('name' => esc_html__('acres', 'wpresidence-core'), 'unit' => esc_html__('ac', 'wpresidence-core'), 'is_square' => 1),
            array('name' => esc_html__('yards', 'wpresidence-core'), 'unit' => esc_html__('yd', 'wpresidence-core'), 'is_square' => 0),
            array('name' => esc_html__('hectares', 'wpresidence-core'), 'unit' => esc_html__('ha', 'wpresidence-core'), 'is_square' => 1),
        );


        // Find the matching unit and format it appropriately (with superscript for non-area units)
        foreach ($measure_array as $single_unit) {
            if ($single_unit['unit'] == $selected_measure) {
                if ($single_unit['is_square'] === 1) {
                    // Area units like acres or hectares don't need superscript
                    $measure_unit = $single_unit['unit'];
                } else {
                    // Add superscript for square feet, meters, yards
                    $measure_unit = $single_unit['unit'] . '<sup>2</sup>';
                }
            }
        }
        return $measure_unit;
    }

endif;

/**
 * Get Formatted Measurement Unit Display for Lot Size
 * 
 * This function returns the appropriate measurement unit for property lot sizes,
 * formatted with proper HTML (e.g., adding superscript for square units).
 * It's similar to wpestate_get_meaurement_unit_formated but uses the specific lot size
 * measurement setting if available.
 * 
 * @param int $show_default     Flag to force using the system default unit (1) instead of user preference (0)
 * @return string               The formatted measurement unit (e.g., "ft²", "ac", etc.)
 */
if (!function_exists('wpestate_get_meaurement_unit_formated_lot_size')):

    function wpestate_get_meaurement_unit_formated_lot_size($show_default = 0) {
        $measure_unit = '';
         // Get the lot size specific measurement system from theme options
         $basic_measure = esc_html(wpresidence_get_option('wp_estate_measure_sys_lot_size', ''));
        
        // Check if user has a preferred measurement unit stored in cookies
        if (isset($_COOKIE['my_measure_unit'])) {
        
            $selected_measure = esc_html($_COOKIE['my_measure_unit']);
        } else {
            $selected_measure = $basic_measure;
        }

        // Override with default measure if show_default flag is set
        if ($show_default == 1) {
            $selected_measure = $basic_measure;
        }

        // Array defining measurement units and their properties
        $measure_array = array(
            array('name' => esc_html__('feet', 'wpresidence-core'), 'unit' => esc_html__('ft', 'wpresidence-core'), 'is_square' => 0),
            array('name' => esc_html__('meters', 'wpresidence-core'), 'unit' => esc_html__('m', 'wpresidence-core'), 'is_square' => 0),
            array('name' => esc_html__('acres', 'wpresidence-core'), 'unit' => esc_html__('ac', 'wpresidence-core'), 'is_square' => 1),
            array('name' => esc_html__('yards', 'wpresidence-core'), 'unit' => esc_html__('yd', 'wpresidence-core'), 'is_square' => 0),
            array('name' => esc_html__('hectares', 'wpresidence-core'), 'unit' => esc_html__('ha', 'wpresidence-core'), 'is_square' => 1),
        );


        // Find the matching unit and format it appropriately (with superscript for non-area units)
        foreach ($measure_array as $single_unit) {
            if ($single_unit['unit'] == $selected_measure) {
                if ($single_unit['is_square'] === 1) {
                    // Area units like acres or hectares don't need superscript
                    $measure_unit = $single_unit['unit'];
                } else {
                    // Add superscript for square feet, meters, yards
                    $measure_unit = $single_unit['unit'] . '<sup>2</sup>';
                }
            }
        }
        return $measure_unit;
    }

endif;

/**
 * Return Appropriate Measurement System Display
 * 
 * This function returns the current measurement unit with proper formatting
 * based on either user preference (from cookies) or system default.
 * It adds appropriate superscript notation for area units when needed.
 * 
 * @return string    The formatted measurement unit with any required superscript notation
 */
if (!function_exists('wpestate_return_measurement_sys')):

    function wpestate_return_measurement_sys() {
        // Check if user has a preferred measurement unit stored in cookies
        if (isset($_COOKIE['my_measure_unit'])) {
            $to_return = ' ' . esc_html($_COOKIE['my_measure_unit']);
            
            // Add superscript for linear units (feet, meters, yards)
            if ($_COOKIE['my_measure_unit'] == 'ft' || $_COOKIE['my_measure_unit'] == 'm' || $_COOKIE['my_measure_unit'] == 'yd') {
                $to_return .= '<sup>2</sup>';
                return $to_return;
            }
            
            // Area units (acres, hectares) don't need superscript
            if ($_COOKIE['my_measure_unit'] == 'ac' || $_COOKIE['my_measure_unit'] == 'ha') {
                return $to_return;
            }
        } else {
            // Use system default if no cookie is set
            $measure = wpresidence_get_option('wp_estate_measure_sys', '');
            
            // Add superscript for linear units (feet, meters, yards)
            if ($measure == 'ft' || $measure == 'm' || $measure == 'yd') {
                $measure .= '<sup>2</sup>';
            }
            return $measure;
        }
    }

endif;



/**
 * Format Property Size Numbers According to Theme Settings
 * 
 * This function formats numeric property size values based on the theme's
 * configuration for thousand separators, decimal separators, and decimal places.
 * It ensures consistent numeric formatting across the entire site for all
 * property measurements.
 * 
 * @param float $value    The numeric value to be formatted
 * @return string         The formatted number as a string
 */
if( !function_exists('wpestate_property_size_number_format') ):
    function wpestate_property_size_number_format($value){
        // Get the thousand separator from theme options (e.g., ',', '.', or space)
        $th_separator   =  stripslashes(  wpresidence_get_option('wp_estate_size_thousand_separator','') );
        
        // Get the decimal separator from theme options (e.g., '.', ',')
        $dc_separator   =  stripslashes(  wpresidence_get_option('wp_estate_size_decimal_separator','') );
        
        // Get the number of decimal places to display from theme options
        $decimals       =  stripslashes(  intval(wpresidence_get_option('wp_estate_size_decimals','')) );
        
        // Format the number using PHP's number_format with the configured separators and decimal places
        $value = number_format($value,$decimals,$dc_separator,$th_separator);
        
        return $value;
    }
    endif;