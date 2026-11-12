<?php 
/**
 * Generate price form elements for advanced search
 *
 * Creates either a price slider or a text input field for price filtering
 * in various positions within the theme (main search, sidebar, mobile, etc.)
 * Handles different IDs, labels, and structures based on the position parameter.
 * Supports price slider with min/max values and custom currency conversion.
 *
 * @param string $position Where the form will be displayed ('mainform', 'sidebar', 'shortcode', 'mobile', 'half', 'elementor')
 * @param string $slug Input field name/ID when not using slider
 * @param string $label Text label for the input field when not using slider
 * @param string $fields_visible Whether to show additional input fields ('visible' or empty)
 * @return string HTML markup for the price form element
 * @since 1.0.0
 */
if (!function_exists('wpestate_price_form_adv_search')):

    function wpestate_price_form_adv_search($position, $slug, $label,$fields_visible='') {
        // Check if price slider is enabled in theme options
        $show_slider_price = wpresidence_get_option('wp_estate_show_slider_price', '');

        // Set appropriate IDs based on form position
        if ($position == 'mainform' || $position == 'elementor') {
            $slider_id = 'slider_price';
            $price_low_id = 'price_low';
            $price_max_id = 'price_max';
            $ammount_id = 'amount';
        } else if ($position == 'sidebar') {
            $slider_id = 'slider_price_widget';
            $price_low_id = 'price_low_widget';
            $price_max_id = 'price_max_widget';
            $ammount_id = 'amount_wd';
        } else if ($position == 'shortcode') {
            $slider_id = 'slider_price_sh';
            $price_low_id = 'price_low_sh';
            $price_max_id = 'price_max_sh';
            $ammount_id = 'amount_sh';
        } else if ($position == 'mobile') {
            $slider_id = 'slider_price_mobile';
            $price_low_id = 'price_low_mobile';
            $price_max_id = 'price_max_mobile';
            $ammount_id = 'amount_mobile';
        } else if ($position == 'half') {
            $slider_id = 'slider_price';
            $price_low_id = 'price_low';
            $price_max_id = 'price_max';
            $ammount_id = 'amount';
        }


        if ($show_slider_price === 'yes') {
            // If using price slider, get min/max values from theme options
            $min_price_slider = ( floatval(wpresidence_get_option('wp_estate_show_slider_min_price', '')) );
            $max_price_slider = ( floatval(wpresidence_get_option('wp_estate_show_slider_max_price', '')) );
            $label_value='';

            // Override with URL parameters if available (for persistence after form submit)
            if (isset($_GET['price_low'])) {
                $min_price_slider = floatval($_GET['price_low']);
            }

            if (isset($_GET['price_low'])) {
                $max_price_slider = floatval($_GET['price_max']);
            }

            // Get price label component from URL if available
            if(isset($_GET['price_label_component']) ){
                $label_value=sanitize_text_field( $_GET['price_label_component']);
            }

            // Get currency settings
            $where_currency     = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
            $wpestate_currency  = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
          
            // Generate price slider labels with formatted currency
            $price_slider_label_data=   wpestate_show_price_label_slider_v2($min_price_slider,$max_price_slider,$wpestate_currency,$where_currency);

            // Extract the formatted price labels
            $price_slider_label         =   $price_slider_label_data['label'];
            $price_slider_label_min     =   $price_slider_label_data['label_min'];
            $price_slider_label_max     =   $price_slider_label_data['label_max'];


            $return_string = '';
            // Start container with appropriate class based on position
            if ($position == 'half') {
                $return_string .= '<div class="col-md-6 adv_search_slider">';
            } else {
                $return_string .= '<div class="adv_search_slider">';
            }

            // Handle multi-currency conversion if user has selected a custom currency
            $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
            if (!empty($custom_fields) && isset($_COOKIE['my_custom_curr']) && isset($_COOKIE['my_custom_curr_pos']) && isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos'] != -1) {
                $i = intval($_COOKIE['my_custom_curr_pos']);

                // Convert slider values to selected currency if not already set in URL
                if (!isset($_GET['price_low']) && !isset($_GET['price_max'])) {
                    $min_price_slider = $min_price_slider * $custom_fields[$i][2];
                    $max_price_slider = $max_price_slider * $custom_fields[$i][2];
                }
            }


            // Add visible input fields if specified
            if(isset($fields_visible) && $fields_visible=='visible'){
                $return_string.='<div class="wpestate_pricev2_component_adv_search_wrapper">
                <input type="text" id="component_'.$price_low_id.'" class="component_adv_search_elementor_price_low price_active wpestate-price-popoup-field-low"   value="'.$price_slider_label_min.'" data-value="'.esc_attr($price_slider_label_min).'" />
                <input type="text" id="component_'.$price_max_id.'" class="component_adv_search_elementor_price_max price_active wpestate-price-popoup-field-max"   value="'.$price_slider_label_max.'" data-value="'.esc_attr($price_slider_label_max).'" />
                </div>
            ';
            }


            // Add price range label and slider element
            $return_string .= '
                    <p>
                        <label>' . esc_html__('Price range:', 'wpresidence-core') . '</label>
                        <span id="' . esc_attr($ammount_id) . '" class="wpresidence_slider_price"  data-default="'.esc_attr($price_slider_label).'">' . $price_slider_label . '</span>
                    </p>
                    <div id="' . $slider_id . '"></div>';
          
            // Add hidden inputs to store the actual values
            $return_string .= '
                    <input type="hidden" id="' . $price_low_id . '"  name="price_low"  class="single_price_low" data-value="' . floatval($min_price_slider) . '" value="' . floatval($min_price_slider) . '"/>
                    <input type="hidden" id="' . $price_max_id . '"  name="price_max"  class="single_price_max" data-value="' . floatval($max_price_slider) . '" value="' . floatval($max_price_slider) . '"/>
                    <input type="hidden"  class="price_label_component" name="price_label_component"   value="'.esc_html($label_value).'" />';
              
            $return_string .= '   </div>';
        } else {
            // If not using slider, create a simple text input field
            $return_string = '';
            if ($position == 'half') {
                $return_string .= '<div class="col-md-3">';
            }

            // Create text input with proper attributes and preserve value from URL if available
            $return_string .= '<input type="text" id="' . $slug . '"  name="' . $slug . '" placeholder="' . $label . '" value="';
            if (isset($_GET[$slug])) {
                $allowed_html = array();
                $return_string .= esc_attr($_GET[$slug]);
            }
            $return_string .= '" class="advanced_select form-control" />';

            if ($position == 'half') {
                $return_string .= '</div>';
            }
        }
        return $return_string;
    }
  
    
endif;




/**
 * Format Price Range Label for Slider
 * 
 * This function generates a properly formatted price range label for price sliders,
 * handling currency position, formatting, and multi-currency support.
 * It creates a text label like "$1,000 to $5,000" based on provided min/max values.
 * 
 * @param float $min_price_slider  The minimum price value from the slider
 * @param float $max_price_slider  The maximum price value from the slider
 * @param string $wpestate_currency  The currency symbol to display
 * @param string $where_currency  Currency position ('before' or 'after')
 * @return string  Formatted price range label
 */
if( !function_exists('wpestate_show_price_label_slider') ):
    function wpestate_show_price_label_slider($min_price_slider,$max_price_slider,$wpestate_currency,$where_currency){
    
        // Get the thousand separator from theme options
        $th_separator       =  stripslashes(  wpresidence_get_option('wp_estate_prices_th_separator','') );
    
        // Ensure the price values are floats for proper calculation
        $min_price_slider=floatval($min_price_slider);
        $max_price_slider=floatval($max_price_slider);
    
        // Get multi-currency options if configured
        $custom_fields = wpresidence_get_option( 'wp_estate_multi_curr', '');
    
        // Handle multi-currency conversion if user has selected a different currency
        if( !empty($custom_fields) && isset($_COOKIE['my_custom_curr']) &&  isset($_COOKIE['my_custom_curr_pos']) &&  isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos']!=-1){
            $i=intval($_COOKIE['my_custom_curr_pos']);
    
            // Convert prices using exchange rate if not already set via URL parameters
            if( !isset($_GET['price_low']) && !isset($_GET['price_max'])  ){
                $min_price_slider       =   $min_price_slider * $custom_fields[$i][2];
                $max_price_slider       =   $max_price_slider * $custom_fields[$i][2];
            }
    
            // Use the selected currency symbol
            $wpestate_currency               =   $custom_fields[$i][0];
            
            // Format numbers for display
            $min_price_slider    =   wpestate_format_number_price($min_price_slider,$th_separator);
            $max_price_slider    =   wpestate_format_number_price($max_price_slider,$th_separator);
    
            // Generate label with currency in the correct position (before or after)
            if ($custom_fields[$i][3] == 'before') {
                $price_slider_label = $wpestate_currency .' '. $min_price_slider.' '.esc_html__('to','wpresidence-core').' '.$wpestate_currency .' '. $max_price_slider;
            } else {
                $price_slider_label =  $min_price_slider.' '.$wpestate_currency.' '.esc_html__('to','wpresidence-core').' '.$max_price_slider.' '.$wpestate_currency;
            }
    
        }else{
            // Standard currency handling when no multi-currency is used
            
            // Format numbers for display
            $min_price_slider    =   wpestate_format_number_price($min_price_slider,$th_separator);
            $max_price_slider    =   wpestate_format_number_price($max_price_slider,$th_separator);
    
            // Generate label with currency in the correct position (before or after)
            if ($where_currency == 'before') {
                $price_slider_label = $wpestate_currency .' '.($min_price_slider).' '.esc_html__('to','wpresidence-core').' '.$wpestate_currency .' ' .$max_price_slider;
            } else {
                $price_slider_label =  $min_price_slider.' '.$wpestate_currency.' '.esc_html__('to','wpresidence-core').' '.$max_price_slider.' '.$wpestate_currency;
            }
        }
    
        return $price_slider_label;
    
    
    }
    endif;
    
    /**
     * Format Price Range Labels for Slider (Version 2)
     * 
     * Enhanced version of the price label function that returns individual min/max labels
     * in addition to the combined label. This allows more flexibility in UI presentation.
     * Handles currency position, formatting, and multi-currency support.
     * 
     * @param float $min_price_slider  The minimum price value from the slider
     * @param float $max_price_slider  The maximum price value from the slider
     * @param string $wpestate_currency  The currency symbol to display
     * @param string $where_currency  Currency position ('before' or 'after')
     * @return array  Array containing three formatted labels: combined range, min only, and max only
     */
    if( !function_exists('wpestate_show_price_label_slider_v2') ):
        function wpestate_show_price_label_slider_v2($min_price_slider,$max_price_slider,$wpestate_currency,$where_currency){
        
            // Get the thousand separator from theme options
            $th_separator       =  stripslashes(  wpresidence_get_option('wp_estate_prices_th_separator','') );
        
            // Ensure the price values are floats for proper calculation
            $min_price_slider=floatval($min_price_slider);
            $max_price_slider=floatval($max_price_slider);
        
            // Get multi-currency options if configured
            $custom_fields = wpresidence_get_option( 'wp_estate_multi_curr', '');
        
            // Handle multi-currency conversion if user has selected a different currency
            if( !empty($custom_fields) && isset($_COOKIE['my_custom_curr']) &&  isset($_COOKIE['my_custom_curr_pos']) &&  isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos']!=-1){
                $i=intval($_COOKIE['my_custom_curr_pos']);
        
                // Convert prices using exchange rate if not already set via URL parameters
                if( !isset($_GET['price_low']) && !isset($_GET['price_max'])  ){
                    $min_price_slider       =   $min_price_slider * $custom_fields[$i][2];
                    $max_price_slider       =   $max_price_slider * $custom_fields[$i][2];
                }
        
                // Use the selected currency symbol
                $wpestate_currency               =   $custom_fields[$i][0];
                
                // Format numbers for display
                $min_price_slider    =   wpestate_format_number_price($min_price_slider,$th_separator);
                $max_price_slider    =   wpestate_format_number_price($max_price_slider,$th_separator);
        
                // Generate labels with currency in the correct position (before or after)
                if ($custom_fields[$i][3] == 'before') {
                    $price_slider_label     = $wpestate_currency . $min_price_slider.' '.esc_html__('to','wpresidence-core').' '.$wpestate_currency . $max_price_slider;
                    $price_slider_label_min = $wpestate_currency . $min_price_slider;
                    $price_slider_label_max = $wpestate_currency . $max_price_slider;
                    
                } else {
                    $price_slider_label     =  $min_price_slider.$wpestate_currency.' '.esc_html__('to','wpresidence-core').' '.$max_price_slider.$wpestate_currency;
                    $price_slider_label_min =  $min_price_slider.$wpestate_currency;
                    $price_slider_label_max =  $max_price_slider.$wpestate_currency;
                }
        
            }else{
                // Standard currency handling when no multi-currency is used
                
                // Format numbers for display
                $min_price_slider    =   wpestate_format_number_price($min_price_slider,$th_separator);
                $max_price_slider    =   wpestate_format_number_price($max_price_slider,$th_separator);
        
                // Generate labels with currency in the correct position (before or after)
                if ($where_currency == 'before') {
                    $price_slider_label     = $wpestate_currency . $min_price_slider.' '.esc_html__('to','wpresidence-core').' '.$wpestate_currency .$max_price_slider;
                    $price_slider_label_min = $wpestate_currency . $min_price_slider;
                    $price_slider_label_max = $wpestate_currency . $max_price_slider;
                } else {
                    $price_slider_label     =  $min_price_slider.$wpestate_currency.' '.esc_html__('to','wpresidence-core').' '.$max_price_slider.$wpestate_currency;
                    $price_slider_label_min =  $min_price_slider.$wpestate_currency;
                    $price_slider_label_max =  $max_price_slider.$wpestate_currency;
                }
            }
    
            // Return an array with all three label variations
            $return_array=array(
                'label'     =>  $price_slider_label,
                'label_min' =>  $price_slider_label_min,
                'label_max' =>  $price_slider_label_max
            );
        
            return $return_array;
        
        
        }
    endif;