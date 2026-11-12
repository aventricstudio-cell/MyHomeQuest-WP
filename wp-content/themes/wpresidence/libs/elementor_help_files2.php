<?php
/*
*
*
*
*/
if(!function_exists('wpestate_show_dropdown_taxonomy_v21')):
function wpestate_show_dropdown_taxonomy_v21($search_field, $label, $appendix,$active='') {
    $field_options = [
        'categories' => [
            'option_name' => 'wp_estate_categ_select_list_multiple', 
            'select_list_function' => 'wpestate_get_category_select_list',
            'term' => 'property_category', 
            'default_label' => esc_html__('Categories','wpresidence'),
            'ul_id'=>'categlist',
            'toogle_id'=>'adv_categ',
            'get_var'=>'property_category'
        ],
        'types' => [
            'option_name' => 'wp_estate_action_select_list_multiple', 
            'select_list_function' => 'wpestate_get_action_select_list',
            'term' => 'property_action_category', 
            'default_label' => esc_html__('Types','wpresidence'),
            'ul_id'=>'actionslist',
            'toogle_id'=>'adv_actions',
            'get_var'=>'property_action_category'
        ],
        'cities' => [
            'option_name' => 'wp_estate_city_select_list_multiple', 
            'select_list_function' => 'wpestate_get_city_select_list',
            'term' => 'property_city', 
            'default_label' => esc_html__('Cities','wpresidence'),
            'ul_id'=>'adv-search-city',
            'toogle_id'=>'property_city',
            'get_var'=>'property_city',
            'is_array' => false
        ],
        'areas' => [
            'option_name' => 'wp_estate_area_select_list_multiple', 
            'select_list_function' => 'wpestate_get_area_select_list',
            'term' => 'property_area', 
            'default_label' => esc_html__('Areas','wpresidence'),
            'ul_id'=>'adv-search-area',
            'toogle_id'=>'property_area',
            'get_var'=>'property_area',
            'is_array' => false
        ],
        'county / state' => [
            'option_name' => 'wp_estate_county_select_state_list_multiple', 
            'select_list_function' => 'wpestate_get_county_state_select_list',
            'term' => 'property_county_state', 
            'default_label' => esc_html__('States','wpresidence'),
            'ul_id'=>'adv-search-countystate',
            'toogle_id'=>'county-state',
            'get_var'=>'property_county_state',
            'is_array' => false
        ],
        'property status' => [
            'option_name' => 'wp_estate_status_select_list_multiple', 
            'select_list_function' => 'wpestate_get_status_select_list',
            'term' => 'property_status', 
            'default_label' => esc_html__('Status','wpresidence'),
            'ul_id'=>'statuslist',
            'toogle_id'=>'adv_status',
            'get_var'=>'property_status'
        ]
    ];

    $args                       =   wpestate_get_select_arguments();
    if (!array_key_exists($search_field, $field_options)) {
        return ''; // Return empty string if search field is not defined
    }

    $options        = $field_options[$search_field];
    $getField       = $options['get_var'];
    $term           = $options['term'];
    $optionName     = $options['option_name'];
    $defaultLabel   = $options['default_label'];
    $ulId           = $options['ul_id'];
    $toggleId       = $options['toogle_id'];

    $is_array = isset($options['is_array']) ? $options['is_array'] : true;

    $value = $value1 = 'all';
   
    $multiple_selected_values=null;



    if(isset($_GET[$getField]) && is_array( $_GET[$getField]) && $active=='active' ){

        $multiple_selected_values=wpestate_sanitize_text_array ($_GET[$getField]);
  


        if(isset($multiple_selected_values[0])){
            $full_name = get_term_by('slug', sanitize_text_field( $multiple_selected_values[0] ), $term);
            if($full_name){
                $value = $value1 = $full_name->name;
            }
        }

    }
        
     
    if( isset($_GET[$getField]) && !is_array( $_GET[$getField]) && trim($_GET[$getField]) != '' && $_GET[$getField] != 'all' && $active=='active') {
    

        $full_name = get_term_by('slug', sanitize_text_field($_GET[$getField]), $term);
        if( isset( $full_name->name)) {
            $value = $value1 = $full_name->name;
        }
       
    } else{
        $value = $label;
        if ($label == '') {
                $value =$defaultLabel;
        }

    }

    

    $value = $label == '' ? $defaultLabel : $label;
    $selectListFunc = $field_options[$search_field]['select_list_function'];

    if (wpresidence_get_option($optionName, '') == 'yes') {
        $select_list = wpestate_get_taxonomy_select_list_for_dropdown($args, $search_field, 'yes', 'maca',$multiple_selected_values);
        
        return wpestate_build_dropdown_multiple($appendix, $ulId, $toggleId, $value, $value1, $getField, $select_list);
    } else {
      
        $select_list = call_user_func($selectListFunc, $args);
  
        
        return  wpestate_build_dropdown_adv_new($appendix, $ulId, $toggleId, $value, $value1, $getField, $select_list,$active);
    }
}
endif;

/*
*
*
*
*/
function wpestate_search_generate_custom_field( $adv_search_what_key, $search_field,$label, $adv_search_label, $placeholder, $position, $slug, $allowed_html, $appendix, $item_field_how, $elementor_label,$key) {
    $custom_fields = wpresidence_get_option('wp_estate_custom_fields', '');
    $i = 0;
    $found_dropdown = 0;
    $return_string = '';


    if (!empty($custom_fields)) {
        while ($i < count($custom_fields)) {
            $name = $custom_fields[$i][0];
            $slug_drop = str_replace(' ', '-', $name);
           
           
        
            if (($slug_drop == $adv_search_what_key || $slug_drop == $search_field) && $custom_fields[$i][2] == 'dropdown') {
                $found_dropdown = 1;
                $front_name = '';
                if (isset($adv_search_label[$key])) {
                    $front_name = sanitize_title($adv_search_label[$key]);
                }

                // Step A: Resolve the exact post-meta name for custom dropdown fields.
                $dropdown_post_meta_name = '';
                if (is_string($adv_search_what_key) && trim($adv_search_what_key) !== '') {
                    $dropdown_post_meta_name = trim($adv_search_what_key);
                } else if (is_string($search_field) && trim($search_field) !== '') {
                    $dropdown_post_meta_name = trim($search_field);
                }
                if (function_exists('wpestate_map_adv_search_field_to_meta_key')) {
                    $dropdown_post_meta_name = wpestate_map_adv_search_field_to_meta_key($dropdown_post_meta_name);
                }

                if (function_exists('icl_translate')) {
                    if ($placeholder != '') {
                        $initial_key = apply_filters('wpml_translate_single_string', trim($placeholder), 'custom field value', 'custom_field_value' . $placeholder);
                    } else {
                        $initial_key = apply_filters('wpml_translate_single_string', trim($adv_search_label[$key]), 'custom field value', 'custom_field_value' . $adv_search_label[$key]);
                    }
                    $action_select_list = ' <li role="presentation" data-value="all"> ' . $initial_key . '</li>';
                } else {
                    if ($placeholder != '') {
                        $action_select_list = ' <li role="presentation" data-value="all">' . $placeholder . '</li>';
                    } else {
                        $action_select_list = ' <li role="presentation" data-value="all">';
                        if( isset($adv_search_label[$key] ) ){
                            $action_select_list .= $adv_search_label[$key] ;
                        }
                        $action_select_list .=  '</li>';
                    }
                }

                $dropdown_values_array = explode(',', $custom_fields[$i][4]);
                foreach ($dropdown_values_array as $drop_key => $value_drop) {
                    $original_value_drop = $value_drop;
                    if (function_exists('icl_translate')) {
                        $value_drop = apply_filters('wpml_translate_single_string', trim($value_drop), 'custom field value', 'custom_field_value' . $value_drop);
                    }
                    $action_select_list .= '<li role="presentation" data-value="' . trim(esc_attr($original_value_drop)) . '">' . trim($value_drop) . '</li>';
                }

                if ($placeholder != '') {
                    $front_name = wpestate_limit45(sanitize_title($elementor_label));
                }

                $dropdown_request_key = ($dropdown_post_meta_name !== '') ? $dropdown_post_meta_name : $front_name;
                if (isset($_GET[$dropdown_request_key]) && $_GET[$dropdown_request_key] != '' && $_GET[$dropdown_request_key] != 'all') {
                    $advanced_drop_value = esc_attr(wp_kses($_GET[$dropdown_request_key], $allowed_html));
                    $advanced_drop_value1 = '';
                } else {
                    $advanced_drop_value = $label;
                    $advanced_drop_value1 = 'all';
                    if ($placeholder != '') {
                        $advanced_drop_value = $placeholder;
                    }
                }
                $front_name = wpestate_limit45($front_name);
                $dropdown_get_var = ($dropdown_post_meta_name !== '') ? $dropdown_post_meta_name : $front_name;
                $dropdown_search_compare = '';
                if (is_string($item_field_how) && trim($item_field_how) !== '') {
                    $dropdown_search_compare = trim($item_field_how);
                }
                $return_string = wpestate_build_dropdown_adv_new($appendix, $front_name, $front_name, $advanced_drop_value, $advanced_drop_value1, $dropdown_get_var, $action_select_list, '', $dropdown_search_compare);
            }
            $i++;
        }
    }

    if ($found_dropdown == 0) {
        $return_string = '';
        if ($position == 'half') {
            $return_string .= '<div class="col-md-3">';
            $appendix = '';
        }

            // Step 1: Resolve the source field that should generate the meta key.
            // Elementor forms may not have $adv_search_what_key populated, so fallback to $search_field.
            $data_search_post_meta_source = '';
            if (is_string($adv_search_what_key) && trim($adv_search_what_key) !== '') {
                $data_search_post_meta_source = trim($adv_search_what_key);
            } else if (is_string($search_field) && trim($search_field) !== '') {
                $data_search_post_meta_source = trim($search_field);
            }

            // Step 2: Convert known labels to canonical meta keys (ex: "property size" => "property_size").
            $data_search_post_meta = $data_search_post_meta_source;
            if (function_exists('wpestate_map_adv_search_field_to_meta_key')) {
                $data_search_post_meta = wpestate_map_adv_search_field_to_meta_key($data_search_post_meta_source);
            }

            // Step 3: Keep the selected compare operator from Elementor field settings.
            $data_search_compare = trim($item_field_how);
       

        if ($placeholder != '') {
            $label = $placeholder;
        }

        // Step 4: Build a safe input name/id so the value is always submitted in GET.
        $resolved_input_name = '';
        if (is_string($data_search_post_meta) && trim($data_search_post_meta) !== '') {
            // Name must be exactly the post-meta key.
            $resolved_input_name = trim($data_search_post_meta);
        }
        if ($resolved_input_name === '') {
            $resolved_input_name = trim($slug);
        }
        if ($resolved_input_name === '' && is_string($elementor_label) && trim($elementor_label) !== '') {
            $resolved_input_name = sanitize_key(wpestate_limit45(sanitize_title($elementor_label)));
        }
        if ($resolved_input_name === '' && is_string($search_field) && trim($search_field) !== '') {
            $resolved_input_name = sanitize_key(str_replace(' ', '_', trim($search_field)));
        }
        $resolved_input_id = $appendix . $resolved_input_name;

        // Step 5: Render the text input with resolved attributes and preserve request value.
        $return_string .= '<input type="text" id="' . esc_attr($resolved_input_id) . '"  name="' . esc_attr($resolved_input_name) . '" data-search-post-meta="' . esc_attr($data_search_post_meta) . '" data-search-compare="' . esc_attr($data_search_compare) . '" placeholder="' . wp_kses($label, $allowed_html) . '" value="';
        if (isset($_GET[$resolved_input_name])) {
            $return_string .= esc_attr($_GET[$resolved_input_name]);
        } else if (isset($_GET[$slug])) {
            $return_string .= esc_attr($_GET[$slug]);
        }
        $return_string .= '" class="advanced_select form-control" >';

        if ($position == 'half') {
            $return_string .= '</div>';
        }

        if (isset($adv_search_how[$i]) || $item_field_how != '') {
            if (isset($adv_search_how[$i]) && ($adv_search_how[$i] == 'date bigger' || $adv_search_how[$i] == 'date smaller' || $item_field_how == 'date bigger' || $item_field_how == 'date smaller')) {
                wpestate_date_picker_translation($resolved_input_id);
            }
        }
    }

    return $return_string;
}

/*
*
*
*/


function wpestate_get_select_list($args, $is_multiple, $field_type, $placeholder) {
 
    $select_list = wpestate_get_taxonomy_select_list_for_dropdown($args,$field_type, $is_multiple,$placeholder);

    return $select_list;
}





/**
 * Generates HTML dropdown or list options for property taxonomies with caching
 *
 * This function builds HTML options for either select dropdowns or unordered lists
 * based on property taxonomies. Supports both single and multiple selection modes,
 * and implements WordPress native transient caching with WPML support.
 *
 * @param array  $args                   Arguments for get_terms()
 * @param string $field_type             Type of field ('categories', 'types', 'cities', etc.)
 * @param string $multiple               Whether multiple selection is enabled ('yes' or 'no')
 * @param string $placeholder            Custom placeholder text (optional)
 * @param array  $multiple_selected_values Pre-selected values for multiple mode
 * @return string HTML markup for dropdown options or list items
 * @since 4.0.0
 */
function wpestate_get_taxonomy_select_list_for_dropdown($args, $field_type, $multiple, $placeholder, $multiple_selected_values) {
    // Create language-specific transient key for WPML compatibility
    $transient_key=wpestate_set_transient_name_multilang('wpestate_get_dropdown_multiple_select_list_');

    // Define field options mapping taxonomy types to their properties
    $field_options = [
        'categories' => [         
            'taxonomy' => 'property_category', 
            'label' => esc_html__('Categories', 'wpresidence')
        ],
        'types' => [          
            'taxonomy' => 'property_action_category', 
            'label' => esc_html__('Types', 'wpresidence')           
        ],
        'cities' => [         
            'taxonomy' => 'property_city', 
            'label' => esc_html__('Cities', 'wpresidence')
        ],
        'areas' => [
            'taxonomy' => 'property_area', 
            'label' => esc_html__('Areas', 'wpresidence')
        ],
        'county / state' => [
            'taxonomy' => 'property_county_state', 
            'label' => esc_html__('States', 'wpresidence')
        ],
        'property status' => [
            'taxonomy' => 'property_status', 
            'label' => esc_html__('Status', 'wpresidence')
        ]
    ];

    // Get taxonomy and label for the requested field type
    $taxonomy = $field_options[$field_type]['taxonomy'];
    $label = $field_options[$field_type]['label'];
    
    // Use custom placeholder if provided
    if($placeholder != '') {
        $label = $placeholder;
    }

    // Complete the transient key with taxonomy
    $transient_key .= $taxonomy;

    // When we have pre-selected values we need fresh markup so we avoid
    // caching in order to correctly mark the options as selected.
    $use_cache = !( is_array($multiple_selected_values) && !empty($multiple_selected_values) );

    // Try to get cached selection list only if caching is allowed
    if ( $use_cache ) {
        $selection_list = get_transient( $transient_key );
    } else {
        $selection_list = false;
    }
       $selection_list = false;
    // If no cache exists or it has expired, generate the list
    if ($selection_list === false) {
        // Get terms for the specified taxonomy
        $categories = get_terms($taxonomy, $args);

        // Get search label options from theme settings
        $adv_search_label = wpresidence_get_option('wp_estate_adv_search_label', '');
        $adv_search_what = wpresidence_get_option('wp_estate_adv_search_what', '');

        // Get the appropriate label for the dropdown
        $label = wpestate_return_default_label($adv_search_what, $adv_search_label, 'types', $label);

        // Initialize selection list with default "All" option based on selection mode
        if($multiple == 'yes') {
            // For multiple select, we don't add a default "all" option
            $selection_list = '';
        } else {
            // For single select, add the default "all" option
            $selection_list = ' <li role="presentation" data-value="all">' . $label . '</li>';
        }

        // Add each term to the list
        if (is_array($categories)) {
            foreach ($categories as $categ) {
                // Get hierarchical children with support for multiple selection
                $received = wpestate_hierarchical_category_childen_v2($multiple_selected_values, $taxonomy, $multiple, $categ->term_id, $args);
                
                // Calculate total count including children
                $counter = $categ->count;
                if (isset($received['count'])) {
                    $counter = $counter + $received['count'];
                }

                // Generate HTML for multiple or single selection
                if($multiple == 'yes') {
                    // Get parent value for hierarchical taxonomies
                    $parent_value = '';
                    if($field_type == 'cities') {
                        $term_meta = get_option("taxonomy_$categ->term_id");
                        if (isset($term_meta['stateparent'])) {
                            $parent_value = sanitize_title($term_meta['stateparent']);
                        }
                    } else if($field_type == 'areas') {
                        $term_meta = get_option("taxonomy_$categ->term_id");
                        if (isset($term_meta['cityparent'])) {
                            $parent_value = sanitize_title($term_meta['cityparent']);
                        }
                    }

                    // Build option tag for multiple select
                    $selection_list .= '<option role="presentation" value="'. esc_attr($categ->slug).'" data-term-id="' . intval($categ->term_id) . '" data-taxonomy="'.esc_attr($field_type).'" 
                                       data-parent-value="'.esc_attr($parent_value).'" data-value="'. esc_attr($categ->slug).'" ';
                    
                    // Mark as selected if in the pre-selected values
                    if( is_array($multiple_selected_values) && in_array($categ->slug, $multiple_selected_values) ||
                        is_array($multiple_selected_values) && in_array(urldecode($categ->slug), $multiple_selected_values)                     
                    ) {
                        $selection_list .= 'selected';
                    }
                    $selection_list .= '>' . ucwords(urldecode($categ->name)) . '</option>';
                } else {
                    // Build list item for single select
                    $selection_list .= '<li role="presentation" data-value="' . esc_attr($categ->slug) . '">' . ucwords(urldecode($categ->name)) . '</li>';
                }
                
                // Add any children from the hierarchical function
                if (isset($received['html'])) {
                    $selection_list .= $received['html'];
                }
            }
        }
        
        // Cache the generated list for 24 hours only when no
        // pre-selected values were provided. This avoids caching
        // personalised lists which would otherwise miss the selected
        // attributes on subsequent requests.
        if ( $use_cache ) {
            set_transient( $transient_key, $selection_list, 24 * 60 * 60 );
        }
    }
    
    return $selection_list;
}




/*
*
*
*
*/




if (!function_exists('wpestate_hierarchical_category_childen_v2')):

    function wpestate_hierarchical_category_childen_v2($multiple_selected_values,$taxonomy,$multiple, $cat, $args, $base = 1, $level = 1) {
        $level++;
        $args['parent'] = $cat;
        $children = get_terms($taxonomy, $args);
        $return_array = array();
        $total_main[$level] = 0;
        $children_categ_select_list = '';
        foreach ($children as $categ) {

            $area_addon = '';
            $city_addon = '';
            $county_addon='';

            if ($taxonomy == 'property_city') {

                $term_meta = get_option("taxonomy_$categ->term_id");

                $string_county = '';
                if (isset($term_meta['stateparent'])) {
                    $string_county = wpestate_limit45(sanitize_title($term_meta['stateparent']));
                }
                $slug_county = sanitize_key($string_county);


                $string = wpestate_limit45(sanitize_title($categ->slug));
                $slug = sanitize_key($string);
                $city_addon = '  data-parentcounty="' . esc_attr($slug_county) . '" data-value2="' . esc_attr($slug) . '" ';
            }

            if ($taxonomy == 'property_county_state') {

               

                $string = wpestate_limit45(sanitize_title($categ->slug));
                $slug = sanitize_key($string);
                $county_addon = '  data-value2="' . esc_attr($slug) . '" ';
            }



            if ($taxonomy == 'property_area') {
                $term_meta = get_option("taxonomy_$categ->term_id");
                $string = wpestate_limit45(sanitize_title($term_meta['cityparent']));
                $slug = sanitize_key($string);
                $area_addon = ' data-parentcity="' . esc_attr($slug) . '" ';
            }

            $hold_base = $base;
            $base_string = '';
            $base++;
            $hold_base = $base;

            if ($level == 2) {
                $base_string = '-';
            } else {
                $i = 2;
                $base_string = '';
                while ($i <= $level) {
                    $base_string .= '-';
                    $i++;
                }
            }


            if ($categ->parent != 0) {
                $received = wpestate_hierarchical_category_childen_v2($multiple_selected_values,$taxonomy, $multiple,$categ->term_id, $args, $base, $level);
            }


            $counter = $categ->count;
            if (isset($received['count'])) {
                $counter = $counter + $received['count'];
            }

            $children_categ_select_list .= '<option role="presentation" value="' . esc_attr($categ->slug) . '" data-term-id="' . intval($categ->term_id) . '"   data-value="' . esc_attr($categ->slug) . '"  '.$county_addon.' '.$city_addon.' '.$area_addon;
            
            if( is_array($multiple_selected_values) && in_array($categ->slug,$multiple_selected_values) ||
            is_array($multiple_selected_values) && in_array(urldecode($categ->slug),$multiple_selected_values) ){
                $children_categ_select_list.= 'selected';
            }


            $children_categ_select_list .= '>' . $base_string . ' ' . ucwords(urldecode($categ->name)) . '</option>';

            if (isset($received['html'])) {
                $children_categ_select_list .= $received['html'];
            }

            $total_main[$level] = $total_main[$level] + $counter;

            $return_array['count'] = $counter;
            $return_array['html'] = $children_categ_select_list;
        }
        $return_array['count'] = $total_main[$level];


        return $return_array;
    }

endif;




/**
 * Generates the HTML markup for the beds and baths selection component
 *
 * This function creates a dropdown component that allows users to select
 * the number of beds and baths for property searches in the WPEstate theme.
 *
 * @param string $appendix               Determines if the component should be half-width
 * @param string $placeholder            Custom placeholder text for the dropdown
 * @param string $elementor_label        Label for Elementor (not used in this function)
 * @param string $term_counter_elementor Counter for Elementor terms (not used in this function)
 * @param string $position               Position of the component (not used in this function)
 * @param string $active                 Whether the component is in an active state
 *
 * @return string HTML markup for the beds and baths component
 */
if (!function_exists('wpestate_show_beds_baths_component')):
function wpestate_show_beds_baths_component($appendix, $placeholder, $elementor_label, $term_counter_elementor, $position, $active='') {
    // Retrieve bed and bath values from WordPress options
    $beds_values     = wpresidence_get_option('wp_estate_beds_component_values', '');
    $baths_values    = wpresidence_get_option('wp_estate_baths_component_values', '');

    // Generate the selection markup for beds and baths
    $beds_selection  = wpestate_get_component_selection($beds_values, 'wp_estate_beds_component', $active);
    $baths_selection = wpestate_get_component_selection($baths_values, 'wp_estate_baths_component', $active);

    // Set default value for the dropdown
    $default_value = esc_html__('Beds/Baths', 'wpresidence');
    if($placeholder != '') $default_value = $placeholder;

    $return_string = '';
    $is_half = null;

    // Handle different appendix cases for layout
    switch ($appendix) {
        case 'half-':  
            $appendix = '';
            $is_half = 1;
            $return_string = '<div class="col-md-3">'; // Start a column for half-width layout
            break;
    }

    // Check the advanced search type
    $adv_search_type = wpresidence_get_option('wp_estate_adv_search_type', '');
    if($adv_search_type == 6){
        $return_string = ''; // Reset return string for specific search type
    }
   
    // Initialize beds and baths component values
    $componentsbeds = '';
    $componentsbaths = '';

    // Handle active state and process request parameters
    if($active == 'active' && (isset($_REQUEST['componentsbeds']) || isset($_REQUEST['componentsbaths']))) {
        if(isset($_REQUEST['componentsbeds'])) {
            $componentsbeds = floatval($_REQUEST['componentsbeds']);
        }
        if(isset($_REQUEST['componentsbaths'])) {
            $componentsbaths = floatval($_REQUEST['componentsbaths']);
        }      
        // Update default value with selected beds and baths
        $default_value = floatval($componentsbeds) . '+ ' . esc_html__('bd', 'wpresidence') . '/' . floatval($componentsbaths) . '+ ' . esc_html__('ba', 'wpresidence');
    }
 
    // Get values from request if set
    $componentsbeds_value = '';
    if(isset($_REQUEST['componentsbeds'])) {
        $componentsbeds_value = $_REQUEST['componentsbeds'];
    }
    $componentsbaths_value = '';
    if(isset($_REQUEST['componentsbaths'])) {
        $componentsbeds_value = $_REQUEST['componentsbaths'];
    }

    // Generate HTML markup for the component
    $return_string .= '
        <div class="btn-group wpestate-beds-baths-popoup-component" style="width:100%;">
            <button type="button" class="btn btn-default dropdown-toggle wpestate-multiselect-custom-style"
            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width:100%;">
                ' . esc_html($default_value) . '
            </button>
            <div class="dropdown-menu wpestate-beds-baths-popoup-wrapper">
                <h3>' . esc_html__('Beds', 'wpresidence') . '</h3>
                <div>' . $beds_selection . '</div>
                <h3>' . esc_html__('Baths', 'wpresidence') . '</h3>
                <div>' . $baths_selection . '</div>
                <div>
                    <div class="wpestate-beds-baths-popoup-reset" data-default-value="' . esc_attr($default_value) . '">' . esc_html__('Reset', 'wpresidence') . '</div>
                    <div class="wpestate-beds-baths-popoup-done">' . esc_html__('Done', 'wpresidence') . '</div>
                </div>
                <input type="hidden" name="componentsbeds" class="wpresidence-componentsbeds" value="' . esc_html($componentsbeds_value) . '">
                <input type="hidden" name="componentsbaths" class="wpresidence-componentsbaths" value="' . esc_html($componentsbaths_value) . '">
            </div>
        </div>';

    // Close the column div for half-width layout if necessary
    if($is_half == 1 && $adv_search_type != 6) {
        $return_string .= '</div>';
    }

    return $return_string;
}
endif;



/**
 * Generate the HTML markup for bed or bath selection items
 *
 * This function creates a series of div elements representing the available
 * options for beds or baths in the WPEstate theme's search component.
 *
 * @param string $component_values A comma-separated string of numeric values for beds or baths
 * @param string $class_prefix     The CSS class prefix to use for the generated items ('wp_estate_beds_component' or 'wp_estate_baths_component')
 * @param string $active           Indicates whether the component is in an active state
 *
 * @return string HTML markup for the bed or bath selection items
 */
if (!function_exists('wpestate_get_component_selection')):
function wpestate_get_component_selection($component_values, $class_prefix, $active) {
    // Convert the comma-separated string of values into an array
    $component_values_array = explode(',', $component_values);
 
    // Use array_map to create an array of HTML elements
    $component_selection = array_map(function($value) use ($class_prefix, $active) {
            $selected_class = '';
            
            // Check if the component is active and if the current value matches the selected value from the request
            if ($active == 'active'):
                if ($class_prefix === 'wp_estate_beds_component' && isset($_REQUEST['componentsbeds']) && $value == $_REQUEST['componentsbeds']) {
                    $selected_class = ' wp_estate_component_item_selected';
                } elseif ($class_prefix === 'wp_estate_baths_component' && isset($_REQUEST['componentsbaths']) && $value == $_REQUEST['componentsbaths']) {
                    $selected_class = ' wp_estate_component_item_selected';
                }
            endif;
            
            // Generate the HTML for each selection item
            return '<div class="' . esc_attr($class_prefix) . '_item' . $selected_class . '" data-value="' . floatval($value) . '">' . esc_html($value) . '</div>';
        }, $component_values_array);
    
    // Combine all the generated HTML elements into a single string
    return implode('', $component_selection);
}
endif;



/**
 * Generate the HTML markup for the price selection component (version 2)
 *
 * This function creates a dropdown component that allows users to select
 * a price range for property searches in the WPEstate theme.
 *
 * @param string $appendix               Determines if the component should be half-width
 * @param string $slug                   Slug for the price component
 * @param string $label                  Label for the price component
 * @param string $placeholder            Custom placeholder text for the dropdown
 * @param string $elementor_label        Label for Elementor (not used in this function)
 * @param string $term_counter_elementor Counter for Elementor terms (not used in this function)
 * @param string $position               Position of the component in the search form
 * @param array  $price_array_data       Array containing price-related data for taxonomy terms
 *
 * @return string HTML markup for the price selection component
 */
if (!function_exists('wpestate_show_price_v2_component')):
    function wpestate_show_price_v2_component($appendix, $slug, $label, $placeholder, $elementor_label, $term_counter_elementor, $position, $price_array_data) {
        // Set default value for the dropdown
        $default_value = esc_html__('Price', 'wpresidence');
        if ($placeholder != '') $default_value = $placeholder;
        if ($label != '') $default_value = $label;

        $return_string = '';
        $is_half = null;

        // Handle different appendix cases for layout
        switch ($appendix) {
            case 'half-':  
                $appendix = '';
                $is_half = 1;
                $return_string = '<div class="col-md-3">'; // Start a column for half-width layout
                break;
        }

        // Check if a price label is set in the request and update the default value
        if (isset($_REQUEST['price_label_component']) && $_REQUEST['price_label_component'] != '') {
            $default_value = sanitize_text_field($_REQUEST['price_label_component']);
        }

        // Determine the label name based on whether a term ID is provided
        if (isset($price_array_data['term_id'])) {
            $label_name = 'price_label_component_' . $price_array_data['term_id'];
        } else {
            $label_name = 'price_label_component';
        }

        // Check if a specific label is set in the request and update the default value
        if (isset($_REQUEST[$label_name]) && $_REQUEST[$label_name] != '') {
            $default_value = sanitize_text_field($_REQUEST[$label_name]);
        }

        // Start building the HTML markup for the component
        $return_string .= '

       

        <div class="btn-group wpestate-beds-baths-popoup-component" style="width:100%;">
            <button type="button" class="btn btn-default dropdown-toggle wpestate-multiselect-custom-style" 
            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-default-value="' . esc_attr($default_value) . '" style="width:100%;">
            ' . esc_html($default_value) . '
            </button>
            <div class="dropdown-menu wpestate-price-popoup-wrapper wpestate-beds-baths-popoup-wrapper">
                <h3>' . esc_html__('Price selector', 'wpresidence') . '</h3>';

        // Generate the price form based on whether price array data is provided
        if (is_array($price_array_data)) {
            $return_string .= wpestate_price_form_adv_search_with_tabs_elementor($position, $slug, $label, '', $price_array_data['term_id'], $price_array_data['term_slug'], $price_array_data['min_price'], $price_array_data['max_price'], 'visible');
        } else {
            $return_string .= wpestate_price_form_adv_search($position, $slug, $label, 'visible');
        }

        // Add reset and done buttons
        $return_string .= '
                <div class="wpestate-price-component-popoup-reset">' . esc_html__('Reset', 'wpresidence') . '</div>
                <div class="wpestate-price-component-popoup-done">' . esc_html__('Done', 'wpresidence') . '</div>
            </div>
        </div>';

        // Close the column div for half-width layout if necessary
        if ($is_half == 1) {
            $return_string .= '</div>';
        }

        return $return_string;
    }
endif;



/**
 * Generate the HTML markup for the price selection component (version 2) for theme search
 *
 * This function creates a dropdown component that allows users to select
 * a price range for property searches in the WPEstate theme's search functionality.
 *
 * @param string $position            Position of the component in the search form
 * @param string $slug                Slug for the price component
 * @param string $label               Label for the price component
 * @param string $use_name            Name to be used for form elements (not used in this function)
 * @param int    $term_id             ID of the taxonomy term (if applicable)
 * @param array  $adv6_taxonomy_terms Array of taxonomy terms for advanced search (not used in this function)
 * @param float  $adv6_min_price      Minimum price for advanced search
 * @param float  $adv6_max_price      Maximum price for advanced search
 * @param string $is_tabs             Whether the component is used in tabs ('yes' or '')
 *
 * @return string HTML markup for the price selection component
 */
if (!function_exists('wpestate_show_price_v2_component_theme_search')):
    function wpestate_show_price_v2_component_theme_search($position, $slug, $label, $use_name, $term_id, $adv6_taxonomy_terms, $adv6_min_price, $adv6_max_price, $is_tabs='') {
        // Set default value for the dropdown
        $default_value = esc_html__('Price', 'wpresidence');
        if ($label != '') $default_value = $label;

        // Determine the label name based on whether tabs are being used
        if ($is_tabs == 'yes') {
            $label_name = 'price_label_component_' . $term_id;
        } else {
            $label_name = 'price_label_component';
        }

        // Check if a specific label is set in the request and update the default value
        if (isset($_REQUEST[$label_name]) && $_REQUEST[$label_name] != '') {
            $default_value = sanitize_text_field($_REQUEST[$label_name]);
        }

        // Start building the HTML markup for the component
        $return_string = '
        <div class="btn-group wpestate-beds-baths-popoup-component" style="width:100%;">
            <button type="button" class="btn btn-default dropdown-toggle wpestate-multiselect-custom-style" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false" data-default-value="' . esc_attr($default_value) . '" style="width:100%;">
            ' . esc_html($default_value) . '
            </button>
            <div class="dropdown-menu wpestate-price-popoup-wrapper">
                <h3>' . esc_html__('Price selector', 'wpresidence') . '</h3>';

        // Generate the price form based on whether tabs are being used
        if ($is_tabs == 'yes') {
            $return_string .= wpestate_price_form_adv_search_with_tabs($position, $slug, $label, $use_name, $term_id, $adv6_taxonomy_terms, $adv6_min_price, $adv6_max_price, 'visible');
        } else {
            $return_string .= wpestate_price_form_adv_search($position, $slug, $label);
        }

        // Add reset and done buttons
        $return_string .= '
                <div class="wpestate-price-component-popoup-reset">' . esc_html__('Reset', 'wpresidence') . '</div>
                <div class="wpestate-price-component-popoup-done">' . esc_html__('Done', 'wpresidence') . '</div>
            </div>
        </div>';

        return $return_string;
    }
endif;




/**
 * Generate the HTML markup for the price selection component (version 3)
 *
 * This function creates a dropdown component that allows users to select
 * a price range for property searches in the WPEstate theme, with advanced options.
 *
 * @param string $appendix               Determines if the component should be half-width
 * @param string $slug                   Slug for the price component (not used in this function)
 * @param string $label                  Label for the price component
 * @param string $placeholder            Custom placeholder text for the dropdown
 * @param string $elementor_label        Label for Elementor
 * @param string $term_counter_elementor Counter for Elementor terms (not used in this function)
 * @param string $position               Position of the component (not used in this function)
 * @param array  $price_array_data       Array containing price-related data
 *
 * @return string HTML markup for the price selection component
 */
if (!function_exists('wpestate_show_price_v3_component')):
    function wpestate_show_price_v3_component($appendix, $slug, $label, $placeholder, $elementor_label, $term_counter_elementor, $position, $price_array_data) {
        // Set default value for the dropdown
        $default_value = esc_html__('Price', 'wpresidence');
        if ($placeholder != '') $default_value = $placeholder;
        if ($label != '') $default_value = $label;
       
        $original_value = $default_value;
       
        // Generate a unique string for the component
        $string = '';
        if ($placeholder != '') {
            $string = wpestate_limit45(sanitize_title($elementor_label)); // For Elementor
            $label = $placeholder;
        }
        $slug = sanitize_key($string);

        $return_string = '';
        $is_half = null;

        // Handle different appendix cases for layout
        switch ($appendix) {
            case 'half-':  
                $appendix = '';
                $is_half = 1;
                $return_string = '<div class="col-md-3">'; // Start a column for half-width layout
                break;
        }

        // Check the advanced search type
        $adv_search_type = wpresidence_get_option('wp_estate_adv_search_type', '');
        if ($adv_search_type == 6) {
            $return_string = '';
        }

        // Determine the names for price inputs based on term ID
        if (!isset($price_array_data['term_id']) || intval($price_array_data['term_id']) === 0) {
            $price_low_name = 'price_low';
            $price_max_name = 'price_max';
            $label_name     = 'price_label_component';
        } else {
            $price_low_name = 'price_low_' . $price_array_data['term_id'];
            $price_max_name = 'price_max_' . $price_array_data['term_id'];
            $label_name     = 'price_label_component_' . $price_array_data['term_id'];
        }

        // Check if a label value is set in the request and update the default value
        $label_value = '';
        if (isset($_REQUEST[$label_name]) && $_REQUEST[$label_name] !== '') {
            $label_value = sanitize_text_field($_REQUEST[$label_name]);
            if ($label_value != '') {
                $default_value = $label_value;
            }
        }

        // Start building the HTML markup for the component
        $return_string .= '
        <div class="btn-group wpestate-beds-baths-popoup-component" style="width:100%;">
            <button type="button" class="btn btn-default dropdown-toggle  wpestate-beds-baths-popoup-component-toggle wpestate-multiselect-custom-style" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-default-value="' . esc_attr($default_value) . '" style="width:100%;">
            ' . esc_html($default_value) . '
            </button>
            <div class="dropdown-menu wpestate-price-popoup-wrapper_v3">
                <h3>' . esc_html__('Price selector', 'wpresidence') . '</h3>';

        // Generate the price selection dropdowns
        $return_string .= '<div class="wpestate_pricev3_component_adv_search_wrapper">';
        $return_string .= wpresidence_generate_2nlevel_dropdown($price_array_data['min_price_values'], $price_low_name, 'wpresidence-component3-min-price');  
        $return_string .= wpresidence_generate_2nlevel_dropdown($price_array_data['max_price_values'], $price_max_name, 'wpresidence-component3-max-price');
        $return_string .= '</div>';

        // Add reset and done buttons
        $return_string .= '
                <div class="wpestate-price-component-popoup-reset_v3" data-default-value2="' . esc_html('No Value', 'wpresidence') . '" data-default-value="' . esc_html($original_value) . '">' . esc_html__('Reset', 'wpresidence') . '</div>
                <div class="wpestate-price-component-popoup-done_v3">' . esc_html__('Done', 'wpresidence') . '</div>
                <input type="hidden" class="price_label_component" name="' . esc_attr($label_name) . '" value="' . esc_html($label_value) . '" />
            </div>
        </div>';

        // Close the column div for half-width layout if necessary
        if ($is_half == 1 && $adv_search_type != 6) {
            $return_string .= '</div>';
        }

        return $return_string;
    }
endif;





/**
 * Generate a select dropdown HTML for price ranges
 *
 * This function creates a select dropdown element with options based on a string of numbers.
 * It's used in the WPResidence theme to create price range selectors for property searches.
 *
 * @param string $numbersString A comma-separated string of numbers representing price values
 * @param string $name          The name attribute for the select element
 * @param string $class         Additional CSS classes for the select element
 *
 * @return string HTML markup for the select dropdown
 */
function wpresidence_generateSelectDropdown($numbersString, $name, $class) {
    // Split the input string into an array of numbers
    $numbers = explode(',', $numbersString);

    // Initialize the selected value
    $selected_value = '';

    // Check if a value for this dropdown is set in the request
    if (isset($_REQUEST[$name])) {
        $selected_value = floatval($_REQUEST[$name]);
    }

    // Start building the HTML for the select dropdown
    $dropdownHtml = '<select name="' . esc_attr($name) . '" class="wpestate-price-component-select ' . esc_attr($class) . '">';
    
    // Add the default "No Value" option
    $dropdownHtml .= '<option value="">' . esc_html('No Value', 'wpresidence') . '</option>';

    // Loop through each number to create option elements
    foreach ($numbers as $number) {
        // Format the number for display
        $label = wpresidence_formatNumberLabel($number);

        // Start the option element
        $dropdownHtml .= '<option value="' . floatval($number) . '"';

        // If this number matches the selected value, mark it as selected
        if ($selected_value == $number) {
            $dropdownHtml .= ' selected ';
        }

        // Close the option tag with the formatted label
        $dropdownHtml .= '>' . esc_html($label) . '</option>';
    }

    // Close the select element
    $dropdownHtml .= '</select>';

    // Return the complete HTML for the dropdown
    return $dropdownHtml;
}



/**
 * Generate a two-level dropdown HTML for price ranges
 *
 * This function creates a custom dropdown element with a hidden input and a list of options.
 * It's used in the WPResidence theme to create advanced price range selectors for property searches.
 *
 * @param string $numbersString A comma-separated string of numbers representing price values
 * @param string $name          The name attribute for the hidden input element and base for IDs
 * @param string $class         Additional CSS classes for the dropdown elements
 *
 * @return string HTML markup for the two-level dropdown
 */
function wpresidence_generate_2nlevel_dropdown($numbersString, $name, $class) {
    // Split the input string into an array of numbers
    $numbers = explode(',', $numbersString);

    // Initialize the selected value
    $selected_value = esc_html__('No Value', 'wpresidence');

    // Check if a value for this dropdown is set in the request
    if (isset($_REQUEST[$name])) {
        $selected_value = floatval($_REQUEST[$name]);
    }

    // Start building the HTML for the two-level dropdown
    $dropdownHtml = '<div class="dropdown wpresidence_dropdown  ">
   
    <button type="button" id="' . esc_attr($name) . '_wrapper" class="wpestate_child_dropdown_item  btn btn-default dropdown-toggle wpestate-multiselect-custom-style ' . esc_attr($class) . '" data-value="all" aria-expanded="true">' 
    . esc_html($selected_value) . '
    </button>';

    // Add hidden input to store the selected value
    $dropdownHtml .= '<input type="hidden" name="' . esc_attr($name) . '" class="' . esc_attr($class) . '_input_class wpresidence-component3_input_class" value="' . esc_attr($selected_value) . '">';

    // Start the unordered list for dropdown options
    $dropdownHtml .= '<ul id="' . esc_attr($name) . '" class="dropdown-menu filter_menu ' . esc_attr($class) . '_class" role="menu" aria-labelledby="' . esc_attr($name) . '_wrapper">
    <li class="wpestate_prevent_ajax" data-value="' . esc_html__('No Value', 'wpresidence') . '">' . esc_html__('No Value', 'wpresidence') . '</li>';

    // Loop through each number to create list item elements
    foreach ($numbers as $number) {
        // Format the number for display
        $label = wpresidence_formatNumberLabel($number);

        // Start the list item element
        $dropdownHtml .= '<li class="wpestate_prevent_ajax" data-value="' . floatval($number) . '"';

        // If this number matches the selected value, mark it as selected
        if ($selected_value == $number) {
            $dropdownHtml .= ' selected ';
        }

        // Close the list item with the formatted label
        $dropdownHtml .= '>' . esc_html($label) . '</li>';
    }

    // Close the unordered list and dropdown div
    $dropdownHtml .= '   </ul>
    </div>';

    // Return the complete HTML for the two-level dropdown
    return $dropdownHtml;
}




/*
 * Format number label  
 * This function formats a number by adding 'M' for millions and 'K' for thousands.
 * It is used to display large numbers in a more readable format on the WpResidence theme.
 *
 * @param int|float $number - The number to format (can be an integer or a float).
 * @return string|int - The formatted number with a suffix ('M' for millions or 'K' for thousands), or the original number if it's less than 1000.
 */

function wpresidence_formatNumberLabel($number) {
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . esc_html__('M','wpresidence');
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) .  esc_html__('K','wpresidence');
    }
    return $number;
}


/*
*
*
*
*/


function wpestate_process_taxonomy_search($tip,$term,$key){
    $taxonomies_array=array(
        'types'         =>  array(
                            'input_name'=>'property_action_category',
                            'taxonomy'=>'property_action_category'
        ),
        'categories'    =>  array(
                                'input_name'=>'property_category',
                                'taxonomy'=>'property_category'
        ),
        'cities'        =>  array(
                                'input_name'=>'property_city',
                                'taxonomy'=>'property_city'
        ),
        'areas'        =>  array(
                                'input_name'=>'property_area',
                                'taxonomy'=>'property_area'
        ),
        'property status'=>  array(
                                'input_name'=>'property_status',
                                'taxonomy'=>'property_status'
        ),
        'county / state'=>  array(
                                'input_name'=>'property_county_state',
                                'taxonomy'=>'property_county_state'
        ),
    );



    $categ_array=array();

    $input_name         =   $taxonomies_array[$term]['input_name'];
    $taxonomy           =   $taxonomies_array[$term]['taxonomy'];
    
    
    if( $tip === 'ajax' ){
        $input_value        =    ($_POST['val_holder'][$key]);
    }else{ 
        if( isset($_REQUEST[$input_name]) ){
            $input_value        =  $_REQUEST[$input_name];
        }
    }

    if ( (isset($_REQUEST[$input_name]) || isset($_POST['val_holder'][$key]) )  ){
        $taxcateg_include   =   array();
   
        $taxcateg_include=  wpestate_sanitize_text_array( $input_value);
        if( (   is_array($taxcateg_include)  && !empty($taxcateg_include)) ||
            (   is_string($taxcateg_include) &&  $taxcateg_include!='' ) ){

                
                    $categ_array=array(
                        'taxonomy'  => $taxonomy,
                        'field'     => 'slug',
                        'terms'     => $taxcateg_include
                    );
        }
        
    }

    return $categ_array;


}


/*
*
*
*
*/

function wpestate_sanitize_text_array($input_array) {
    if(is_array($input_array)){
        $sanitized_array = array();
     

        foreach ($input_array as $key => $value) {
            // Sanitize each element of the array
            $decoded_value = urldecode($value);
            if($decoded_value!=='' && strtolower($decoded_value)!=='all'  ){
                $sanitized_array[$key] = sanitize_text_field($decoded_value);
            }        
        }

        return $sanitized_array;
    }else{
        if( strtolower($input_array)=='all'){
            return '';
        }
        $decoded_value = urldecode($input_array);
        return sanitize_text_field($decoded_value);

    }
}
/*
*
*
*
*/


/*
*
*
*
*/


/*
*
*
*
*/


/*
*
*
*
*/


/*
*
*
*
*/


/*
*
*
*
*/


/*
*
*
*
*/










?>
