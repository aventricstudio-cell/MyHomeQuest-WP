<?php
/**
 * Display property overview section.
 *
 * This function generates the HTML for displaying a property overview section.
 * It can output the content either as a tab or as a regular section.
 *
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Optional. Whether to display as a tab. Default ''.
 * @param string $tab_active_class Optional. CSS class for active tab. Default ''.
 * @return string|void HTML output if $is_tab is 'yes', otherwise echoes the HTML.
 */
if ( ! function_exists( 'wpestate_property_overview_v2' ) ) :
    function wpestate_property_overview_v2( $postID, $is_tab = '', $tab_active_class = '' ) {
        global $post;

        if ( $postID ) {
            $post = get_post( $postID );
        }

        $data  = wpestate_return_all_labels_data( 'overview' );
        $label = wpestate_property_page_prepare_label( $data['label_theme_option'], $data['label_default'] );

        $template_path = locate_template( 'templates/listing_templates/property-page-templates/single-overview-section.php' );

        if ( $is_tab === 'yes' ) {
            ob_start();
            include $template_path;
            $content = ob_get_clean();
            return wpestate_property_page_create_tab_item( $content, $label, $data['tab_id'], $tab_active_class );
        } else {
            include $template_path;
        }
    }
endif;



/*
 *
 * Display ooverview ittem
 * 
 *
 */

 if (!function_exists('wpestate_display_overview_item')):
    function wpestate_display_overview_item($itemKey,$value){
        

        if($value=='' || $value=='Not Available' || $value == esc_html__( 'Not Available', 'wpresidence' )){
            return;
        }

        $overview_details = array(
    
    
            'bedrooms'    =>array(
                            'label' => esc_html__('Bedrooms','wpresidence'),
                            'icon'=>'templates/svg_icons/single_bedrooms.svg'
                            ),
            'bathrooms'    =>array(
                            'label' => esc_html__('Bathrooms','wpresidence'),
                             'icon'=>'templates/svg_icons/single_bath.svg'
                            ),
            'rooms'    =>array(
                            'label' => esc_html__('Rooms','wpresidence'),
                            'icon'=>'templates/svg_icons/single_rooms_overview.svg'
            ),
            'garages'=>array(
                            'label' => esc_html__('Garages','wpresidence'),
                            'icon'=>'templates/svg_icons/single_garage.svg'
            ),
            'size'       =>array(
                            'label' => '',
                            'label2' => esc_html__('Property Size','wpresidence'),
                            'icon'=>'templates/svg_icons/single_floor_plan.svg'
                            ),
            'lot_size'    =>array(
                            'label' => '',
                            'label2' => esc_html__('Property Lot Size','wpresidence'),
                            'icon'=>'templates/svg_icons/single_lot_size.svg'
            ),
            'year_built' =>array(
                            'label' => esc_html__('Year Built:','wpresidence'),
                            'icon'=>'templates/svg_icons/single_calendar.svg',
                            'reverse'=>'yes'
                        ),
            'property_category'=>array(
                            'label' => esc_html__('Category','wpresidence'),
                            'icon'=>'',
                            'class1'=>"first_overview_left",
                            'class2'=>'first_overview_date'
                           
                        ),
            'property_id'=>array(
                            'label' => esc_html__('Property ID','wpresidence'),
                            'icon'=>'',
                            'class1'=>"first_overview_left",
                            'class2'=>'first_overview_date'
                        ),
            'property_internal_id'=>array(
                            'label' => esc_html__('Listing ID','wpresidence'),
                            'icon'=>'',
                            'class1'=>"first_overview_left",
                            'class2'=>'first_overview_date'
                        ),
        
           
    
        );
    
        $designType=wpresidence_get_option('wp_estate_overview_elements_design');
    
        $return_string='';
        if( isset($overview_details[$itemKey]) ){
    
            $class1 =    $overview_details[$itemKey]['class1'] ?? '';
            $class2 =    $overview_details[$itemKey]['class2'] ?? '';
            $icon   =   '';
            if(isset($overview_details[$itemKey]['icon']) && '' !== $overview_details[$itemKey]['icon']){
                ob_start();
                include ( locate_template($overview_details[$itemKey]['icon'])   );
                $icon = ob_get_contents();
                ob_end_clean();
            }
           
            $label = $overview_details[$itemKey]['label']??'';
            $label2 = $overview_details[$itemKey]['label2']??'';
    
    
    
            switch ($designType) {
    
                case 'type1':
                    $return_string.='  <ul class="overview_element">';
                            $return_string.='<li class="first_overview '.esc_attr($class1).'">';
                            
                                if($icon!==''){
                                    $return_string.=$icon;
                                }
    
                                if('property_category' === $itemKey  || 'property_id'=== $itemKey  || 'property_internal_id'=== $itemKey  ){
                                    $return_string.=$overview_details[$itemKey]['label'];
                                }
    
                            $return_string.='</li>';
    
                            $return_string.='<li class="'.esc_attr($class2).'">';
                            if(!isset($overview_details[$itemKey]['reverse']) ){
                                $return_string.= $value;
                            }
                            if(isset($overview_details[$itemKey]['label']) && '' !== $overview_details[$itemKey]['label'] && 'property_category'!== $itemKey && 'property_internal_id'!== $itemKey && 'property_id'!== $itemKey ){
                                $return_string.= ' '.$label;
                            }   
                            if(isset($overview_details[$itemKey]['reverse']) && 'yes' === $overview_details[$itemKey]['reverse'] ){
                                $return_string.= $value;
                            }
                            
                            $return_string.='</li>';
                    $return_string.='</ul>';
                    break;
            case 'type2':
                $return_string .= '<ul class="overview_element overview_element_type2">';

                // Column 1: ICON
                $return_string .= '<li class="overview_icon_col '.esc_attr($class2).'">';
                    if ($icon !== '') {
                        $return_string .= $icon;
                    }
                $return_string .= '</li>';

                // Column 2: TITLE + VALUE
                $return_string .= '<li class="overview_content_col '.esc_attr($class1).'">';
                    
                    // Title
                    if ($label !== '') {
                        $return_string .= '<span class="overview_title">'.$label.'</span>';
                    }
                    if ($label2 !== '') {
                        $return_string .= '<span class="overview_title2">'.$label2.'</span>';
                    }

                    // Value
                    $return_string .= '<span class="overview_value">'.$value.'</span>';

                $return_string .= '</li>';

                $return_string .= '</ul>';
                break;
                case 'type3':
                    $return_string.='  <ul class="overview_element overview_element_type3">';
                            $return_string.='<li class="first_overview '.esc_attr($class1).'">';
                                if($icon!==''){
                                    $return_string.=$icon;
                                }
                                $return_string.=$value;
    
                               
                                
                            $return_string.='</li>';
    
                            $return_string.='<li class=" first_overview_date '.esc_attr($class2).'">';
                           
                            if($label!==''){
                                $return_string.=$label;
                            }
    
                            if($label2!==''){
                                $return_string.=$label2;
                            }
    
                            
                            $return_string.='</li>';
                    $return_string.='</ul>';
                    break;
                    case 'type4':
                        $return_string.='  <ul class="overview_element overview_element_type3">';
                                $return_string.='<li class="first_overview '.esc_attr($class1).'">';
                                  
                                    $return_string.=$value;
        
                                   
                                    
                                $return_string.='</li>';
        
                                $return_string.='<li class=" first_overview_date '.esc_attr($class2).'">';
                               
                                if($label!==''){
                                    $return_string.=$label;
                                }
        
                                if($label2!==''){
                                    $return_string.=$label2;
                                }
        
                                
                                $return_string.='</li>';
                        $return_string.='</ul>';
                        break;
                }   
        }
    
      return $return_string;
                   
    
    }
    endif;
    


    /**
 * Display the property map in the overview section.
 *
 * This function generates and displays a static map image for a property
 * in the overview section. It supports both Google Maps and Mapbox,
 * depending on the theme settings.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since 1.0.0
 *
 * @param int $post_id The ID of the property post.
 */
if(!function_exists('wpestate_display_property_overview_map')):
    function wpestate_display_property_overview_map($post_id) {
        // Retrieve latitude and longitude from post meta
        $property_latitude = get_post_meta($post_id, 'property_latitude', true);
        $property_longitude = get_post_meta($post_id, 'property_longitude', true);
    
        // Check if both latitude and longitude are available
        if ($property_latitude !== '' && $property_longitude != '') {
            // Get the marker image URL
            $marker_image = get_theme_file_uri('/css/css-images/idxpin.png');
            
            // Determine which map service to use (1 for Google Maps, other for Mapbox)
            $what_map = intval(wpresidence_get_option('wp_estate_kind_of_map'));
            
            // Get map dimensions from theme options
            $overview_map_width = intval(wpresidence_get_option('wpestate_overview_map_width'));
            $overview_map_height = intval(wpresidence_get_option('wpestate_overview_map_height'));
            
            echo '<div class="overview_element overview_element_map">';
            if ($what_map == 1) {
                // Google Maps
                $api_key = wpresidence_get_option('wp_estate_api_key');
                $map_url = "https://maps.googleapis.com/maps/api/staticmap?center={$property_latitude},{$property_longitude}&zoom=11&size={$overview_map_width}x{$overview_map_height}&scale=1&format=jpg&style=feature:administrative.land_parcel|visibility:off&style=feature:landscape.man_made|visibility:off&style=feature:transit.station|hue:0xffa200&markers=icon:{$marker_image}%7C{$property_latitude},{$property_longitude}&key={$api_key}";
                echo '<img id="overview_map" class="overview_map" src="' . esc_url($map_url) . '" alt="map-entry" style="width:' . intval($overview_map_width) . 'px;height:' . intval($overview_map_height) . 'px;" height="100%" width="100%">';
            } else {
                // Mapbox
                $encoded_marker_image_url = urlencode($marker_image);
                $mapbox_access_token = wpresidence_get_option('wp_estate_mapbox_api_key');
                $map_url = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/url-{$encoded_marker_image_url}({$property_longitude},{$property_latitude})/{$property_longitude},{$property_latitude},11/{$overview_map_width}x{$overview_map_height}?access_token={$mapbox_access_token}";
                echo '<img id="overview_map"  class="overview_map" src="' . esc_url($map_url) . '" alt="map-entry" height="100%" width="100%">';                
            }
            echo '</div>';
        }
        
        // Display the map modal
        echo wpestate_overview_map_modal($post_id);
    }
endif;




/**
 * WpResidence Theme - Overview Map Modal Function
 *
 * This file contains the wpestate_overview_map_modal function, which generates
 * a modal dialog containing a map for a specific property.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @since 1.0.0
 *
 * Dependencies:
 * - get_post_meta()
 * - get_the_term_list()
 * - do_shortcode()
 *
 * Usage:
 * $modal_content = wpestate_overview_map_modal($post_id);
 */

 if (!function_exists('wpestate_overview_map_modal')) :
    /**
     * Generate a modal dialog containing a map for a specific property.
     *
     * @param int $postID The ID of the property post.
     * @return string HTML markup for the modal dialog.
     */
    function wpestate_overview_map_modal($postID) {
        // Retrieve property address details
        $property_address = get_post_meta($postID, 'property_address', true);
        $property_city = get_the_term_list($postID, 'property_city', '', ', ', '');
        $property_area = get_the_term_list($postID, 'property_area', '', ', ', '');

        // Build the full address string
        $address_parts = array_filter([
            $property_address,
            wp_strip_all_tags($property_city),
            wp_strip_all_tags($property_area)
        ]);
        $property_address_show = implode(', ', $address_parts);

        // Generate map content using shortcode
        $map_content = do_shortcode('[property_page_map propertyid="' . $postID . '"][/property_page_map]');

        // HTML template for the modal
        $modal_template = <<<HTML
        <div id="wpestate_overview_map_modal" class="modal wpestate_overview_map_modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">    
                            <svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <rect width="24" height="24"></rect>
                                    <path d="M7 17L16.8995 7.10051" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M7 7.00001L16.8995 16.8995" stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round"></path>
                                </g>
                            </svg>
                        </span>
                    </button>
                    <div class="modal-body">
                        <h3>%s</h3>  
                        %s
                    </div>      
                </div>
            </div>
        </div>
        HTML;

        // Return the populated modal HTML
        return sprintf(
            $modal_template,
            esc_html(trim($property_address_show)),
            $map_content
        );
    }
endif;
