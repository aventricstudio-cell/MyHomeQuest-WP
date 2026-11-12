<?php


/**
 * Calculate Bootstrap column width for an advanced search field
 *
 * Supports 1–6 fields per row. Each row is 12 grid units.
 * If the price slider is active, the price field gets a wider column.
 *
 * @param string $search_field                     Field slug
 * @param int    $adv_search_fields_no_per_row     Number of fields per row (1–6)
 * @return int                                      Column width (1–12)
 */
function wpresidence_get_search_form_col($search_field, $adv_search_fields_no_per_row){

    // Enforce sane limits between 1 and 6 fields per row
    if ($adv_search_fields_no_per_row < 1) {
        $adv_search_fields_no_per_row = 1;
    } elseif ($adv_search_fields_no_per_row > 6) {
        $adv_search_fields_no_per_row = 6;
    }

    // Special case for five per row
    // We cannot split 12 evenly by 5 so we use a custom class
    if ($adv_search_fields_no_per_row == 5) {

        // Custom token that will become class "col-md-five"
        $search_col = 'five';
        

        // For five per row we keep price field same width
        // If you want a wider price field here we must handle it with extra CSS
        return $search_col;
    }

    // All other counts divide evenly in the 12 column grid
    // 1 field  12
    // 2 fields 6
    // 3 fields 4
    // 4 fields 3
    // 6 fields 2
    $search_col = 12 / $adv_search_fields_no_per_row;

    // For the price field with slider enabled we double the width
    if (
        $search_field == 'property price' &&
        wpresidence_get_option('wp_estate_show_slider_price','') == 'yes'
    ) {
        $search_col = $search_col * 2;

        // Hard cap at a full row
        if ($search_col > 12) {
            $search_col = 12;
        }
    }

    return (int) $search_col;
}

/**
 * Filters property listings to remove sold properties based on site settings
 *
 * This function modifies the query arguments to exclude properties that have been marked
 * as sold when the site settings specify that sold properties should not be displayed.
 * It adds a tax_query parameter to filter out properties with the configured "sold" status.
 *
 * @param array $args The original WP_Query arguments for property listings
 * @return array Modified query arguments with sold properties filtered out if needed
 * @since 1.0.0
 */
if( !function_exists('wpestate_remove_sold_listings') ):
    function wpestate_remove_sold_listings($args){
        // Get the option that determines whether to show sold items or not
        $show_sold_items = wpresidence_get_option('wp_estate_show_sold_items','');
        
        // Only proceed with filtering if sold items should not be shown
        if($show_sold_items=='no'){
            // Get the term ID that represents the "sold" status from options
            $sold_status_id = wpresidence_get_option('wpestate_mark_sold_status','');
           
            // Create a taxonomy query array to exclude properties with the sold status
            $taxonomy_status=array(
                'taxonomy' => 'property_status',   // Using the property_status taxonomy
                'field'    => 'term_id',           // Match by term ID
                'terms'    => array(  $sold_status_id ),  // The "sold" status term ID
			    'operator' => 'NOT IN',            // Exclude properties with this status
            );

            // Add the taxonomy query to the existing query args
            if( isset($args['tax_query']) ){
                // If tax_query already exists, just append the new condition
                $args['tax_query'][]=$taxonomy_status;
            }else{
                // If tax_query doesn't exist yet, create it with AND relation
                $args['tax_query']['relation'] ='AND';
                $args['tax_query'][]=$taxonomy_status;
            }
        }
        // Return the modified query arguments
        return $args;
    }
endif;

/**
 * Returns custom dropdown label for search type 10
 *
 * This function retrieves the custom label for a given search element in the advanced
 * search type 10. It looks up the custom label from the site options, or returns a
 * default value if no custom label is found.
 *
 * @param string $element The search element key to find the label for
 * @return string The custom label for the element or a default value
 * @since 1.0.0
 */
if( !function_exists('wpresidence_return_custom_dropdown_label_type10') ):
function wpresidence_return_custom_dropdown_label_type10($element){
  // Set up default labels for common element types
  $defaults=array();
  $defaults['types']=esc_html__('Types','wpresidence');
  $defaults['categories']=esc_html__('Categories','wpresidence');

  // Get the advanced search configuration from options
  $adv_search_what = wpresidence_get_option('wp_estate_adv_search_what','');
  $adv_search_label = wpresidence_get_option('wp_estate_adv_search_label','');
  
  // Find the index of the requested element in the search configuration
  $key = array_search($element,$adv_search_what);
  
  // Convert key to integer if it exists
  if($key!='')$key=intval($key);
  
  // Return the custom label if found, otherwise return the default
  if($key!=''){
    return $adv_search_label[$key];
  } else {
    return $defaults[$element];
  }
}
endif;

/**
 * Generates the price filter form for advanced search with tabs
 *
 * This function creates either a price slider or a text input field based on site settings.
 * For sliders, it handles currency display, min/max values, and maintains the current 
 * search parameters when the form is submitted.
 *
 * @param string $position Position of the form ('half' or other)
 * @param string $slug Field slug for identification
 * @param string $label Display label for the field
 * @param string $use_name The name attribute to use for the field
 * @param int $term_id The term ID associated with this price filter
 * @param array $adv6_taxonomy_terms Array of taxonomy terms
 * @param array $adv6_min_price Array of minimum prices
 * @param array $adv6_max_price Array of maximum prices
 * @param string $fields_visible Whether fields should be visible or not
 * @return string HTML markup for the price filter form
 * @since 1.0.0
 */
if( !function_exists('wpestate_price_form_adv_search_with_tabs') ):
    function wpestate_price_form_adv_search_with_tabs($position,$slug,$label,$use_name,$term_id,$adv6_taxonomy_terms,$adv6_min_price,$adv6_max_price,$fields_visible='',$search_compare=''){
        // Check if slider price is enabled in site options
        $show_slider_price = wpresidence_get_option('wp_estate_show_slider_price','');
        
        // Find the price key in the taxonomy terms array
        $price_key = array_search($term_id,$adv6_taxonomy_terms);
        
        // Generate unique IDs for slider elements
        $slider_id = 'slider_price_'.$term_id.'_'.$position;
        $price_low_id = 'price_low_'.$term_id;
        $price_max_id = 'price_max_'.$term_id;
        $ammount_id = 'amount_'.$term_id.'_'.$position;

        // Check if a term_id is specified in the GET parameters
        $search_term_id=0;
        if(isset($_GET['term_id'])){
            $search_term_id=intval($_GET['term_id']);
        }

        // If the price slider is enabled, create a slider component
        if ($show_slider_price==='yes'){
                // Initialize default and current slider values
                $min_price_slider_default= $min_price_slider= floatval($adv6_min_price[$price_key] );
                $max_price_slider_default= $max_price_slider= floatval($adv6_max_price[$price_key] );
                $label_value='';

                // Override with GET values if they exist and match the current term
                if(isset($_GET['price_low_'.$search_term_id]) && $search_term_id==$term_id ){
                    $min_price_slider = floatval($_GET['price_low_'.$search_term_id]) ;
                }

                if(isset($_GET['price_max_'.$search_term_id]) && $search_term_id==$term_id ){
                    $max_price_slider = floatval($_GET['price_max_'.$search_term_id]) ;
                }
                
                if(isset($_GET['price_label_component_'.$search_term_id]) && $search_term_id==$term_id ){
                    $label_value = sanitize_text_field( $_GET['price_label_component_'.$search_term_id] );
                }

                // Get currency settings
                $where_currency = esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
                $wpestate_currency = esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );

                // Generate price labels for display
                $price_slider_label_data = wpestate_show_price_label_slider_v2($min_price_slider,$max_price_slider,$wpestate_currency,$where_currency);
                $price_slider_label = $price_slider_label_data['label'];
                $price_slider_label_min = $price_slider_label_data['label_min'];
                $price_slider_label_max = $price_slider_label_data['label_max'];
                
                // Generate default price labels for resetting
                $price_slider_label_data_default = wpestate_show_price_label_slider_v2($min_price_slider_default,$max_price_slider_default,$wpestate_currency,$where_currency);

                // Start building the output HTML
                $return_string='';
                $return_string.='<div class="adv_search_slider">';
         
                // Add editable price fields if visible
                if($fields_visible=='visible'){
                    $return_string.='<div class="wpestate_pricev2_component_adv_search_wrapper">
                    <input type="text" id="component_'.$price_low_id.'" class="component_adv_search_elementor_price_low price_active wpestate-price-popoup-field-low"   value="'.$price_slider_label_min.'" data-value="'.esc_attr($min_price_slider_default).'" />
                    <input type="text" id="component_'.$price_max_id.'" class="component_adv_search_elementor_price_max price_active wpestate-price-popoup-field-max"   value="'.$price_slider_label_max.'" data-value="'.esc_attr($max_price_slider_default).'" />
                </div>
                ';
                }

                // Add the slider display with label
                $return_string.='
                    <p>
                        <label>'. esc_html__('Price range:','wpresidence').'</label>
                        <span id="'.esc_attr($ammount_id).'"  class="wpresidence_slider_price" data-default="'.esc_attr($price_slider_label_data_default['label']).'"  >'.$price_slider_label.'</span>
                    </p>
                    <div id="'.$slider_id.'"></div>';
                
                // Handle currency conversion if needed
                $custom_fields = wpresidence_get_option( 'wp_estate_multi_curr', '');
                if( !empty($custom_fields) && isset($_COOKIE['my_custom_curr']) &&  isset($_COOKIE['my_custom_curr_pos']) &&  isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos']!=-1){
                    $i=intval($_COOKIE['my_custom_curr_pos']);

                    if( !isset($_GET['price_low_'.$search_term_id]) && !isset($_GET['price_max_'.$search_term_id])  ){
                        $min_price_slider = $min_price_slider * $custom_fields[$i][2];
                        $max_price_slider = $max_price_slider * $custom_fields[$i][2];
                    }
                }

                // Add hidden fields to store the actual values
                $return_string.='
                    <input type="hidden" id="'.$price_low_id.'" class="adv6_price_low wpestate_slider_in_tab price_active" name="'.$price_low_id.'"  value="'.$min_price_slider.'"  data-value="'.esc_attr($min_price_slider_default).'"/>
                    <input type="hidden" id="'.$price_max_id.'" class="adv6_price_max wpestate_slider_in_tab price_active" name="'.$price_max_id.'"  value="'.$max_price_slider.'"  data-value="'.esc_attr($max_price_slider_default).'"/>
                    <input type="hidden"  class="price_label_component" name="price_label_component_'.$term_id.'"  value="'.esc_html($label_value).'" />
                </div>';

        } else {
            // If slider is disabled, create a standard text input field
            $return_string='';
            
            // Add container div if position is 'half'
            if($position=='half'){
                //$return_string.='<div class="col-md-3">';
            }

            // Step 1: Use the original slug as the DOM id so existing JS/CSS selectors keep working.
            $input_id = $slug;

            // Step 2: Use distinct request names for non-slider min/max price fields.
            // This keeps the plain input pair readable and prevents the two inputs
            // from submitting the same request key.
            $input_name = 'property_price';
            if ($search_compare === 'greater') {
                $input_name = 'price_min';
            } else if ($search_compare === 'smaller') {
                $input_name = 'price_max';
            }

            // Step 3: Add the same search metadata attributes used by the generic text input renderer.
            $data_search_attrs = ' data-search-post-meta="property_price" data-search-compare="' . esc_attr($search_compare) . '"';

            // Step 4: Create the input field and preserve any existing value from the request.
            $return_string.='<input type="text" id="'.$input_id.'"  name="'.$input_name.'"'.$data_search_attrs.' placeholder="'.$label.'" value="';
            if (isset($_GET[$input_name])) {
                $allowed_html = array();
                $return_string.= esc_attr ( $_GET[$input_name] );
            } else if ($input_name === 'price_min' && isset($_GET['price_low'])) {
                $allowed_html = array();
                $return_string.= esc_attr ( $_GET['price_low'] );
            } else if (isset($_GET[$slug])) {
                $allowed_html = array();
                $return_string.= esc_attr ( $_GET[$slug] );
            }
            $return_string.='" class="advanced_select form-control" />';
        }
        
        return $return_string;
    }
endif;

/**
 * Generates the price filter form for advanced search with tabs in Elementor
 *
 * This function is similar to wpestate_price_form_adv_search_with_tabs but optimized
 * for Elementor integration. It creates price filters with proper field names and IDs
 * to work with Elementor search components.
 *
 * @param string $position Position of the form ('half' or other)
 * @param string $slug Field slug for identification
 * @param string $label Display label for the field
 * @param string $use_name The name attribute to use for the field
 * @param int $term_id The term ID associated with this price filter
 * @param array $adv6_taxonomy_terms Array of taxonomy terms
 * @param float $min_price Minimum price value
 * @param float $max_price Maximum price value
 * @param string $fields_visible Whether fields should be visible or not
 * @return string HTML markup for the price filter form
 * @since 1.0.0
 */
if( !function_exists('wpestate_price_form_adv_search_with_tabs_elementor') ):
    function wpestate_price_form_adv_search_with_tabs_elementor($position,$slug,$label,$use_name,$term_id,$adv6_taxonomy_terms,$min_price,$max_price,$fields_visible='',$search_compare=''){
        // Check if slider price is enabled in site options
        $show_slider_price = wpresidence_get_option('wp_estate_show_slider_price','');
        
        // Generate unique IDs for slider elements with Elementor-specific naming
        $slider_id = 'slider_price_'.$term_id.'_'.$position;
        $price_low_id = 'price_low_elementor_search_'.$term_id;
        $price_max_id = 'price_max_elementor_search_'.$term_id; 
        
        // Set field names based on term ID
        if(intval($term_id)===0){
            $price_low_name = 'price_low';
            $price_max_name = 'price_max';
        } else {
            $price_low_name = 'price_low_'.$term_id;
            $price_max_name = 'price_max_'.$term_id;
        }
        
        $ammount_id = 'amount_elementor_search_'.$term_id.'_'.$position;

        // Check if a term_id is specified in the GET parameters
        $search_term_id=0;
        if(isset($_GET['term_id'])){
            $search_term_id=intval($_GET['term_id']);
        }

        // If the price slider is enabled, create a slider component
        if ($show_slider_price==='yes'){
                // Initialize slider values
                $min_price_slider = floatval($min_price);
                $max_price_slider = floatval($max_price);
                $label_value = '';
                
                // Override with GET values if they exist and match the current term
                if(isset($_GET['price_low_'.$search_term_id]) && $search_term_id==$term_id ){
                    $min_price_slider = floatval($_GET['price_low_'.$search_term_id]);
                }

                if(isset($_GET['price_low_'.$search_term_id]) && $search_term_id==$term_id ){
                    $max_price_slider = floatval($_GET['price_max_'.$search_term_id]);
                }

                if(isset($_GET['price_label_component_'.$search_term_id]) && $search_term_id==$term_id ){
                    $label_value = sanitize_text_field($_GET['price_label_component_'.$search_term_id]);
                }

                // Get currency settings
                $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
                $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
                
                // Generate price labels for display
                $price_slider_label_data = wpestate_show_price_label_slider_v2($min_price_slider,$max_price_slider,$wpestate_currency,$where_currency);
                $price_slider_label = $price_slider_label_data['label'];
                $price_slider_label_min = $price_slider_label_data['label_min'];
                $price_slider_label_max = $price_slider_label_data['label_max'];

                // Handle currency conversion if needed
                $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
                if(!empty($custom_fields) && isset($_COOKIE['my_custom_curr']) && isset($_COOKIE['my_custom_curr_pos']) && isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos']!=-1){
                    $i=intval($_COOKIE['my_custom_curr_pos']);

                    if(!isset($_GET['price_low_'.$search_term_id]) && !isset($_GET['price_max_'.$search_term_id])){
                        $min_price_slider = $min_price_slider * $custom_fields[$i][2];
                        $max_price_slider = $max_price_slider * $custom_fields[$i][2];
                    }
                }

                // Start building the output HTML with Elementor-specific classes
                $return_string='<div class="adv_search_slider wpestate_elementor_search_tab_slider_wrapper ">';
                   
                // Add editable price fields if visible
                if($fields_visible=='visible'){
                    $return_string.='<div class="wpestate_pricev2_component_adv_search_wrapper">
                    <input type="text" id="component_'.$price_low_id.'" class="component_adv_search_elementor_price_low price_active wpestate-price-popoup-field-low"  value="'.$price_slider_label_min.'" data-value="'.esc_attr($price_slider_label_min).'" />
                    <input type="text" id="component_'.$price_max_id.'" class="component_adv_search_elementor_price_max price_active wpestate-price-popoup-field-max"  value="'.$price_slider_label_max.'" data-value="'.esc_attr($price_slider_label_max).'" />
                   </div> 
                ';
                }

                // Add the slider display with label
                $return_string.='
                    <p>
                        <label>'. esc_html__('Price range:','wpresidence').'</label>
                        <span id="'.esc_attr($ammount_id).'"  class="wpresidence_slider_price" data-default="'.esc_attr($price_slider_label).'" >'.$price_slider_label.'</span>
                    </p>
                    <div id="'.$slider_id.'" class="wpestate_elementor_search_tab_slider"></div>';

                // Add hidden fields to store the actual values with Elementor-specific field names
                $return_string.='
                <input type="hidden" id="'.$price_low_id.'" class="adv_search_elementor_price_low price_active" name="'.$price_low_name.'"  value="'.$min_price_slider.'" data-value="'.esc_attr($min_price_slider).'" />
                <input type="hidden" id="'.$price_max_id.'" class="adv_search_elementor_price_max price_active" name="'.$price_max_name.'"  value="'.$max_price_slider.'" data-value="'.esc_attr($max_price_slider).'" />
                <input type="hidden"  class="price_label_component" name="price_label_component_'.$term_id.'"   value="'.esc_html($label_value).'" />';
            
                $return_string.='</div>';

        } else {
            // If slider is disabled, create a standard text input field
            $return_string='';
            
            // Add container div if position is 'half'
            if($position=='half'){
                //$return_string.='<div class="col-md-3">';
            }

            // Step 1: Keep the field id based on the configured slug for frontend compatibility.
            $input_id = $slug;

            // Step 2: Use distinct request names for non-slider min/max price fields.
            // This keeps the plain input pair readable and prevents the two inputs
            // from submitting the same request key.
            $input_name = 'property_price';
            if ($search_compare === 'greater') {
                $input_name = 'price_min';
            } else if ($search_compare === 'smaller') {
                $input_name = 'price_max';
            }

            // Step 3: Attach the search metadata attributes used by AJAX field parsing.
            $data_search_attrs = ' data-search-post-meta="property_price" data-search-compare="' . esc_attr($search_compare) . '"';

            // Step 4: Render the plain text input and preserve any current request value.
            $return_string.='<input type="text" id="'.$input_id.'"  name="'.$input_name.'"'.$data_search_attrs.' placeholder="'.$label.'" value="';
            if (isset($_GET[$input_name])) {
                $allowed_html = array();
                $return_string.= esc_attr($_GET[$input_name]);
            } else if ($input_name === 'price_min' && isset($_GET['price_low'])) {
                $allowed_html = array();
                $return_string.= esc_attr($_GET['price_low']);
            } else if (isset($_GET[$slug])) {
                $allowed_html = array();
                $return_string.= esc_attr($_GET[$slug]);
            }
            $return_string.='" class="advanced_select form-control" />';

            if($position=='half'){
              //  $return_string.='</div>';
            }
        }
        return $return_string;
    }
endif;

/**
 * Generates the advanced search form for tab 6
 *
 * This function builds the complete advanced search form with the appropriate number of
 * fields per row, handling the different search field types and their labels. It processes
 * a slice of the total search fields based on the term counter to show only relevant fields.
 *
 * @param string $active Active tab identifier
 * @param string $position Form position ('sidebar' or other)
 * @param array $adv_search_what Array of search field types
 * @param int $adv_search_fields_no_per_row Number of fields per row
 * @param array $action_select_list Action dropdown options
 * @param array $categ_select_list Category dropdown options
 * @param array $select_city_list City dropdown options
 * @param array $select_area_list Area dropdown options
 * @param array $select_county_state_list County/state dropdown options
 * @param string $use_name Whether to use specific name attributes
 * @param int $term_id Term ID for this search form
 * @param int $adv_search_fields_no Total number of search fields
 * @param int $term_counter Counter to determine which slice of fields to show
 * @return string HTML markup for the complete search form
 * @since 1.0.0
 */
function wpestate_show_adv6_form($active,$position,$adv_search_what,$adv_search_fields_no_per_row,$action_select_list,$categ_select_list,$select_city_list,$select_area_list,$select_county_state_list,$use_name,$term_id,$adv_search_fields_no,$term_counter){
    $search_col_submit='';
    $return_string='';
      
    // Start output buffering to capture the HTML
    ob_start();
    
    // Validate that search fields are an array
    if(!is_array($adv_search_what)){
        return;
    }
    
    // Get the relevant slice of search fields for this term/tab
    $adv_search_what = array_slice($adv_search_what, ($term_counter*$adv_search_fields_no),$adv_search_fields_no);

    // Get the labels for search fields
    $adv_search_label = wpresidence_get_option('wp_estate_adv_search_label','');

    // Get the corresponding slice of labels if available
    if(is_array($adv_search_label)){
        $adv_search_label = array_slice($adv_search_label, ($term_counter*$adv_search_fields_no),$adv_search_fields_no);
    }

    // Loop through each search field to create the form elements
    foreach($adv_search_what as $key=>$search_field){
        // Determine column width based on fields per row setting
     

         // Determine correct column width for this field
                    $search_col =$search_col_submit=  wpresidence_get_search_form_col($search_field, $adv_search_fields_no_per_row);

                     if (
                        $search_field == 'property price' &&
                        wpresidence_get_option('wp_estate_show_slider_price','') == 'yes'
                    ) {
                        if(is_numeric( $search_col_submit )){
                            $search_col_submit=$search_col_submit/2;
                        }
                    }



    

        // Use full width for sidebar position
        if($position=='sidebar'){
            $search_col=12;
            $search_col_submit=12;
        }

        if($search_field!=='none'){
            // Create the field container div with appropriate classes
            print '<div class="col-md-'.esc_attr($search_col).' '.str_replace(" ","_",$search_field).' wpestate-field-on-'.$position.' ">';
            
            // Generate the search field content based on type
            wpestate_show_search_field_with_tabs(
                $adv_search_label[$key],
                $active,
                $position,
                $search_field,
                $action_select_list,
                $categ_select_list,
                $select_city_list,
                $select_area_list,
                $key,
                $select_county_state_list,
                $use_name,
                $term_id,
                $adv_search_fields_no,
                $term_counter
            );
            
            print '</div>';
        }

 
    }

    // Add the submit button container and button
    print '<div class="col-md-'.esc_attr($search_col_submit).' submit_container_half ">';
    print '<input name="submit" type="submit" class="wpresidence_button advanced_submit_4"  value="'.esc_html__('Search Properties','wpresidence').'">';
    print '</div>';

    // Get the buffered output
    $return_string = ob_get_contents();
    ob_end_clean();

    return $return_string;
}
