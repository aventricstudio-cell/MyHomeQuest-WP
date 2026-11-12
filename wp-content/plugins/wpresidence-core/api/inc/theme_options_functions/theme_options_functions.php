<?php






/*
 * Convert Redux currency settings into a structured array format.
 *
 * This function retrieves currency data from the WPResidence Redux options (`$wpresidence_admin`)
 * and formats it into an array of currencies with their name, label, value, and order.
 *
 * @return array $final_array - An array containing currency details (name, label, value, order).
 */

 if ( ! function_exists( 'wpestate_reverse_convert_redux_wp_estate_multi_curr' ) ):
    function wpestate_reverse_convert_redux_wp_estate_multi_curr() {
        global $wpresidence_admin; // Access the global WPResidence Redux options array
        $final_array = array(); // Initialize the final array that will hold formatted currency data
    
        // Check if the Redux option 'wpestate_currency' and 'add_curr_name' exist
        if (isset($wpresidence_admin['wpestate_currency']['add_curr_name'])) {
            
            // Loop through each currency entry
            foreach ($wpresidence_admin['wpestate_currency']['add_curr_name'] as $key => $value) {
                $temp_array = array(); // Temporary array to store a single currency entry
    
                // Assign values from Redux options to the temporary array
                $temp_array[0] = $wpresidence_admin['wpestate_currency']['add_curr_name'][$key];  // Currency Name
                $temp_array[1] = $wpresidence_admin['wpestate_currency']['add_curr_label'][$key]; // Currency Label
                $temp_array[2] = $wpresidence_admin['wpestate_currency']['add_curr_value'][$key]; // Conversion Value
                $temp_array[3] = $wpresidence_admin['wpestate_currency']['add_curr_order'][$key]; // Display Order
    
                // Add the formatted currency entry to the final array
                $final_array[] = $temp_array;
            }
        }
    
        return $final_array; // Return the formatted currency data array
    }
    endif;
    


    /*
 * Returns the search parameters for the advanced property search.
 *
 * This function determines which search fields should be displayed based on 
 * theme settings and whether custom search fields are enabled.
 *
 * @param array  $wpresidence_admin        The global theme options array.
 * @param string $theme_option             The specific search option key to retrieve.
 * @param string $custom_advanced_search   'yes' if custom search fields are enabled, otherwise 'no'.
 *
 * @return array $return                   The search parameters based on settings.
 */

if ( ! function_exists( 'wpestate_return_search_parameters' ) ) :
    function wpestate_return_search_parameters($wpresidence_admin, $theme_option, $custom_advanced_search) {
    
        // Check if custom advanced search is enabled
        if ($custom_advanced_search == 'yes') {
            
            // If custom search fields are defined in the theme options, use them
            if (isset($wpresidence_admin[$theme_option]) && $wpresidence_admin[$theme_option] != '') {
                $return = $wpresidence_admin[$theme_option];    
            } else {
                $return = array(); // Default to an empty array if no settings are found
            }
      
        } else {
            // Default search parameters when custom search is not enabled
            $combined_search_array = [
                // Specifies what search fields to display
                'wp_estate_adv_search_what' => [
                    0 => 'types',
                    1 => 'categories',
                    2 => 'county / state',
                    3 => 'cities',
                    4 => 'areas',
                    5 => 'beds-baths',
                    6 => 'property status',
                    7 => 'property-price-v2',
                    8 => 'types',
                    9 => 'categories',
                    10 => 'county / state',
                    11 => 'cities',
                    12 => 'areas',
                    13 => 'beds-baths',
                    14 => 'property status',
                    15 => 'property-price-v2',
                    16 => 'types',
                    17 => 'categories',
                    18 => 'county / state',
                    19 => 'cities',
                    20 => 'areas',
                    21 => 'beds-baths',
                    22 => 'property status',
                    23 => 'property-price-v2',
                ],
                // Labels for the search fields
                'wp_estate_adv_search_label' => [
                    0 => esc_html__('Types', 'wpresidence-core'),
                    1 => esc_html__('Categories', 'wpresidence-core'),
                    2 => esc_html__('County', 'wpresidence-core'),
                    3 => esc_html__('City', 'wpresidence-core'),
                    4 => esc_html__('Area', 'wpresidence-core'),
                    5 => esc_html__('Beds&Baths', 'wpresidence-core'),
                    6 => esc_html__('Status', 'wpresidence-core'),
                    7 => esc_html__('Price', 'wpresidence-core'),
                    8 => esc_html__('Types', 'wpresidence-core'),
                    9 => esc_html__('Categories', 'wpresidence-core'),
                    10 => esc_html__('County', 'wpresidence-core'),
                    11 => esc_html__('City', 'wpresidence-core'),
                    12 => esc_html__('Area', 'wpresidence-core'),
                    13 => esc_html__('Beds&Baths', 'wpresidence-core'),
                    14 => esc_html__('Status', 'wpresidence-core'),
                    15 => esc_html__('Price', 'wpresidence-core'),
                    16 => esc_html__('Types', 'wpresidence-core'),
                    17 => esc_html__('Categories', 'wpresidence-core'),
                    18 => esc_html__('County', 'wpresidence-core'),
                    19 => esc_html__('City', 'wpresidence-core'),
                    20 => esc_html__('Area', 'wpresidence-core'),
                    21 => esc_html__('Beds&Baths', 'wpresidence-core'),
                    22 => esc_html__('Status', 'wpresidence-core'),
                    23 => esc_html__('Price', 'wpresidence-core'),
                ],
                // Defines how search parameters are compared
                'wp_estate_adv_search_how' => [
                    0 => 'like',     // Partial match
                    1 => 'like',
                    2 => 'like',
                    3 => 'like',
                    4 => 'like',
                    5 => 'equal',    // Exact match for beds & baths
                    6 => 'like',
                    7 => 'equal',    // Exact match for price
                    8 => 'like',
                    9 => 'like',
                    10 => 'like',
                    11 => 'like',
                    12 => 'greater', // Greater than for certain fields
                    13 => 'like',
                    14 => 'equal',
                    15 => 'equal',
                    16 => 'like',
                    17 => 'like',
                    18 => 'like',
                    19 => 'like',
                    20 => 'greater',
                    21 => 'like',
                    22 => 'equal',
                    23 => 'equal',
                ]
            ];
            
            // Determine the number of search fields to return
            if ( isset($wpresidence_admin['wp_estate_adv_search_type'] ) && $wpresidence_admin['wp_estate_adv_search_type'] == 6) {
                $return = $combined_search_array[$theme_option]; // Return full array for type 6
            } else {
                $return = array_slice($combined_search_array[$theme_option], 0, 8, true); // Return only first 8 elements
            }
        }
    
        return $return; // Return the determined search parameters
    }
    endif;
    


    /*
 * Converts Redux custom field settings into a structured array format.
 *
 * This function retrieves custom field settings from the WPResidence Redux options (`$wpresidence_admin`)
 * and formats them into an array, which includes field name, label, type, order, and dropdown order.
 * The resulting array is sorted before returning.
 *
 * @return array $final_array - A sorted array containing custom field details.
 */

if ( ! function_exists( 'wpestate_reverse_convert_redux_wp_estate_custom_fields' ) ) :
    function wpestate_reverse_convert_redux_wp_estate_custom_fields() {
        global $wpresidence_admin; // Access the global WPResidence Redux options array
        $final_array = array(); // Initialize the final array that will hold formatted custom field data
    
        // Check if the Redux option for custom fields exists and has field names
        if (isset($wpresidence_admin['wpestate_custom_fields_list']['add_field_name'])) {
            
            // Loop through each custom field entry
            foreach ($wpresidence_admin['wpestate_custom_fields_list']['add_field_name'] as $key => $value) {
                $temp_array = array(); // Temporary array to store a single custom field entry
    
                // Assign values from Redux options to the temporary array
                $temp_array[0] = $wpresidence_admin['wpestate_custom_fields_list']['add_field_name'][$key];  // Custom Field Name
                $temp_array[1] = $wpresidence_admin['wpestate_custom_fields_list']['add_field_label'][$key]; // Field Label
                $temp_array[3] = $wpresidence_admin['wpestate_custom_fields_list']['add_field_order'][$key]; // Display Order
                $temp_array[2] = $wpresidence_admin['wpestate_custom_fields_list']['add_field_type'][$key];  // Field Type
    
                // Check if dropdown order exists and add it if available
                if (isset($wpresidence_admin['wpestate_custom_fields_list']['add_dropdown_order'][$key])) {
                    $temp_array[4] = $wpresidence_admin['wpestate_custom_fields_list']['add_dropdown_order'][$key]; // Dropdown Order
                }
    
                // Add the formatted custom field entry to the final array
                $final_array[] = $temp_array;
            }
        }

        // Add Acf Fields in array
        if ( function_exists( 'get_field' ) && wpresidence_get_option('wpestate_show_acf_fields', 1) )   {
            $groups = acf_get_field_groups(array('post_type' => 'estate_property'));
            if (is_array($groups) && count($groups) > 0) {
                foreach ($groups as $group) {
                    $fields = acf_get_fields($group['key']);
                    if (is_array($fields) && count($fields) > 0) {
                        foreach ($fields as $field) {
                            if (isset($field['name']) && isset($field['label']) && isset($field['type'])) {
                                if ( !array_search( $field['label'], array_column($final_array, 1) ) )   {
                                    $temp_array[0] = $field['name'];
                                    $temp_array[1] = $field['label'];
                                    $temp_array[3] = 0;
                                    $temp_array[2] = $field['type'];
                                    $temp_array[4] = 0;

                                    $final_array[] = $temp_array;
                                }
                            }
                        }
                    }
                }
            }
        }

       
        // Sort the array using the WPResidence sorting function
        usort($final_array, "wpestate_sorting_function_plugin");
    
        return $final_array; // Return the sorted custom field data array
    }
    endif;
    



