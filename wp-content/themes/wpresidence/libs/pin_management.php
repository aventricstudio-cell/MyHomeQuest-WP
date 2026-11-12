<?php



/**
 * Determines the flex direction class for half map layout based on map position setting
 * 
 * This function checks the WordPress theme option 'wp_estate_half_map_position'
 * and returns the appropriate Bootstrap flex direction class. When the map 
 * position is set to 'left', it returns the default row direction. Otherwise,
 * it returns the reversed row direction.
 * 
 * @since 1.0.0
 * @access public
 * 
 * @uses wpresidence_get_option() To get the half map position theme setting
 * 
 * @return string Bootstrap flex direction class ('flex-md-row' or 'flex-md-row-reverse')
*/

if( !function_exists('wpresidence_map_map_orientation_class') ):
function wpresidence_map_map_orientation_class(){
    $half_map_position = wpresidence_get_option('wp_estate_half_map_position');
    if($half_map_position=='left'){
        return 'flex-md-row';
    }else{
        return 'flex-md-row-reverse';
    }
}

endif;

/**
 * Legacy pin payload builder used by older map flows.
 *
 * Builds one marker array for a single property by reading post meta/terms
 * directly from WordPress APIs. Kept for backward compatibility.
 *
 * @param int    $the_id Property post ID.
 * @param string $wpestate_currency Currency symbol.
 * @param string $where_currency Currency position.
 * @param string $unit Measurement system label.
 * @param int    $counter Marker index.
 * @return array Marker payload consumed by front-end map scripts.
 */

if( !function_exists('wpestate_pin_unit_creation') ):
    function wpestate_pin_unit_creation2($the_id,$wpestate_currency,$where_currency,$unit,$counter){

        // Step 1: Read core geolocation fields for the current property.
        $gmap_lat    =      floatval(get_post_meta($the_id, 'property_latitude', true));
        $gmap_long   =      floatval(get_post_meta($the_id, 'property_longitude', true));

        if($gmap_lat==0){
            $gmap_lat='';
        }

        if($gmap_long==0){
            $gmap_long='';
        }

        $slug        =   array();
        $prop_type   =   array();

        $types       =   get_the_terms($the_id,'property_category' );
        $types_act   =   get_the_terms($the_id,'property_action_category' );

        $prop_type_name=array();
        if ( $types && ! is_wp_error( $types ) ) {
            foreach ($types as $single_type) {
                $prop_type[]      = $single_type->slug;
                $prop_type_name[] = $single_type->name;
                $slug             = $single_type->slug;
                $parent_term      = $single_type->parent;

            }

            $single_first_type      = $prop_type[0];
            $single_first_type_pin  = $prop_type[0];
            if($parent_term!=0){
                $single_first_type=$single_first_type.wpestate_add_parent_infobox($parent_term,'property_category');
            }
            $single_first_type_name= $prop_type_name[0];
        }else{
            $single_first_type        ='';
            $single_first_type_name   ='';
            $single_first_type_pin    ='';
        }


        // Step 2: Read action taxonomy terms and compute first selected value.
        $prop_action        =   array();
        $prop_action_name   =   array();
        if ( $types_act && ! is_wp_error( $types_act ) ) {
              foreach ($types_act as $single_type) {
                $prop_action[]      =   $single_type->slug;
                $prop_action_name[] =   $single_type->name;
                $slug               =   $single_type->slug;
                $parent_term        =   $single_type->parent;
               }
        $single_first_action        = $prop_action[0];
        $single_first_action_pin    = $prop_action[0];

        if($parent_term!=0){
            $single_first_action=$single_first_action.wpestate_add_parent_infobox($parent_term,'property_action_category');
        }
        $single_first_action_name   = $prop_action_name[0];
        }else{
            $single_first_action        ='';
            $single_first_action_name   ='';
            $single_first_action_pin    ='';
        }

        // composing name of the pin
        if($single_first_action=='' || $single_first_action ==''){
              $pin                   =  sanitize_key(wpestate_limit54($single_first_type_pin.$single_first_action_pin));
        }else{
              $pin                   =  sanitize_key(wpestate_limit27($single_first_type_pin)).sanitize_key(wpestate_limit27($single_first_action_pin));
        }
        $counter++;


        // Step 3: Build price and pin-price labels.
        $price              =   floatval    ( get_post_meta($the_id, 'property_price', true) );
        $price_label        =   esc_html    ( get_post_meta($the_id, 'property_label', true) );
        $price_label_before =   esc_html    ( get_post_meta($the_id, 'property_label_before', true) );
        $clean_price        =   floatval    ( get_post_meta($the_id, 'property_price', true) );

        $hidingprice = wpestate_hide_show_price( $the_id );

        if ( $hidingprice ) {
            $hiddenPriceLabel = wpresidence_get_option( 'wp_estate_property_hide_price_text', '', 'Price on application' );
            // If price is hidden, return empty string or print nothing
            $price = $hiddenPriceLabel;
            $pin_price = $hiddenPriceLabel;
        } else {
        // If price is not hidden, format it as usual
        

        if($price==0){
            $price      =   $price_label_before.''.$price_label;
            $pin_price  =   '';
        }else{
            $th_separator   =   stripslashes ( wpresidence_get_option('wp_estate_prices_th_separator','') );

            $pin_price      =   $price;
             $price    =   wpestate_format_number_price($price,$th_separator);

            if($where_currency=='before'){
                $price=$wpestate_currency.$price;
            }else{
                $price=$price.$wpestate_currency;
            }


            $indian_format = esc_html(wpresidence_get_option('wp_estate_price_indian_format', ''));
    
            if( wpresidence_get_option('wp_estate_use_price_pins_full_price','')=='no'){
                
                if($indian_format=='no'){
                    $pin_price  =   wpestate_price_pin_converter($pin_price,$where_currency,$wpestate_currency);
                }else{
                   $pin_price  =   wpestate_moneyFormatIndia_short($pin_price);
                }



            }else{
               $pin_price  =='<span class="infocur infocur_first">'.$price_label_before.'</span>'.$price.'<span class="infocur">'.$price_label.'</span>';
         
              
            }

            $price='<span class="infocur infocur_first">'.$price_label_before.'</span>'.$price.'<span class="infocur">'.$price_label.'</span>';
        }

        }



        // Step 4: Add remaining property stats used by map infobox rendering.
        $rooms      =   get_post_meta($the_id, 'property_bedrooms', true);
        $bathrooms  =   get_post_meta($the_id, 'property_bathrooms', true);
        $size       =   wpestate_get_converted_measure( $the_id, 'property_size' );

        // Step 5: Compose marker payload in the order expected by JS.
        $place_markers  =   array();
        $title_orig     =   wpresidence_get_sanitized_truncated_title($the_id,0);
        $title_orig     =   str_replace('%','', $title_orig);

        $place_markers[]    =   rawurlencode ($title_orig);//0
        $place_markers[]    =   $gmap_lat;//1
        $place_markers[]    =   $gmap_long;//2
        $place_markers[]    =   $counter;//3
        $place_markers[]    =   rawurlencode ( get_the_post_thumbnail($the_id,'agent_picture_thumb') );////4
        $place_markers[]    =   rawurlencode ( $price); //5
        $place_markers[]    =   rawurlencode ($single_first_type);//6
        $place_markers[]    =   rawurlencode ($single_first_action);//7
        $place_markers[]    =   rawurlencode ($pin);//8
        $place_markers[]    =   rawurlencode (  esc_url ( get_permalink($the_id) ) );//9
        $place_markers[]    =   $the_id;//10
        $place_markers[]    =   $clean_price;//11
        $place_markers[]    =   $rooms;//12
        $place_markers[]    =   $bathrooms;//13
        $place_markers[]    =   $size;//14
        $place_markers[]    =   rawurlencode ( $single_first_type_name);//15
        $place_markers[]    =   rawurlencode  ( $single_first_action_name);//16
        $place_markers[]    =   rawurlencode($pin_price);
        $place_markers[]    =   rawurlencode(get_the_post_thumbnail($the_id,'widget_thumb') );
        return $place_markers;



    }
endif;

/**
 * Create map pin data for a property
 * 
 * Optimized function to generate pin data for property map markers, using cached property data
 * to reduce database queries and improve performance.
 * 
 * @param int    $the_id          Property ID
 * @param string $wpestate_currency Currency symbol
 * @param string $where_currency  Currency position (before/after)
 * @param string $unit            Unit of measurement
 * @param int    $counter         Pin counter
 * @return array Array of pin data for map markers
 */
if(!function_exists('wpestate_pin_unit_creation')):
    function wpestate_pin_unit_creation($the_id, $wpestate_currency, $where_currency, $unit, $counter){
        if(!function_exists('wpestate_api_get_cached_post_data')){
            return;
        }
        // Get cached property data
        $cached_data = wpestate_api_get_cached_post_data($the_id, 'estate_property');
        
        // Extract latitude and longitude from cached meta
        $gmap_lat = floatval($cached_data['meta']['property_latitude'] ?? 0);
        $gmap_long = floatval($cached_data['meta']['property_longitude'] ?? 0);
        
        // Set empty string for zero coordinates
        if($gmap_lat == 0) {
            $gmap_lat = '';
        }
        
        if($gmap_long == 0) {
            $gmap_long = '';
        }
        
        // Initialize variables
        $slug = array();
        $prop_type = array();
        $prop_type_name = array();
        
        // Get property categories from cached terms
        $types = $cached_data['terms']['property_category'] ?? array();
        $types_act = $cached_data['terms']['property_action_category'] ?? array();
        
        // Process property categories
        if(!empty($types)) {
            foreach($types as $single_type) {
                $prop_type[] = $single_type['slug'];
                $prop_type_name[] = $single_type['name'];
                $slug = $single_type['slug'];
                $parent_term = isset($single_type['parent']) ? $single_type['parent'] : 0;
            }
            
            $single_first_type = $prop_type[0];
            $single_first_type_pin = $prop_type[0];
            
            if(isset($parent_term) && $parent_term != 0) {
                $single_first_type = $single_first_type.wpestate_add_parent_infobox($parent_term, 'property_category');
            }
            
            $single_first_type_name = $prop_type_name[0];
        } else {
            $single_first_type = '';
            $single_first_type_name = '';
            $single_first_type_pin = '';
        }
        
        // Process property actions
        $prop_action = array();
        $prop_action_name = array();
        
        if(!empty($types_act)) {
            foreach($types_act as $single_type) {
                $prop_action[] = $single_type['slug'];
                $prop_action_name[] = $single_type['name'];
                $slug = $single_type['slug'];
                $parent_term = isset($single_type['parent']) ? $single_type['parent'] : 0;
            }
            
            $single_first_action = $prop_action[0];
            $single_first_action_pin = $prop_action[0];
            
            if(isset($parent_term) && $parent_term != 0) {
                $single_first_action = $single_first_action.wpestate_add_parent_infobox($parent_term, 'property_action_category');
            }
            
            $single_first_action_name = $prop_action_name[0];
        } else {
            $single_first_action = '';
            $single_first_action_name = '';
            $single_first_action_pin = '';
        }
        
        // Compose pin name
        if($single_first_action == '' || $single_first_type == '') {
            $pin = sanitize_key(wpestate_limit54($single_first_type_pin.$single_first_action_pin));
        } else {
            $pin = sanitize_key(wpestate_limit27($single_first_type_pin)).sanitize_key(wpestate_limit27($single_first_action_pin));
        }
        
        $counter++;
        
        // Get price information from cached meta
        $price = floatval($cached_data['meta']['property_price'] ?? 0);
        $price_label = esc_html($cached_data['meta']['property_label'] ?? '');
        $price_label_before = esc_html($cached_data['meta']['property_label_before'] ?? '');
        $clean_price = $price;

        $hidingprice = wpestate_hide_show_price( $the_id );

        if ( $hidingprice ) {
            $hiddenPriceLabel = wpresidence_get_option( 'wp_estate_property_hide_price_text', '', 'Price on application' );
            // If price is hidden, return empty string or print nothing
            $price = $hiddenPriceLabel;
            $pin_price = $hiddenPriceLabel;
        } else {
        // If price is not hidden, format it as usual
        
        // Format price
        if($price == 0) {
            $price = $price_label_before.''.$price_label;
            $pin_price = '';
        } else {
            $th_separator = stripslashes(wpresidence_get_option('wp_estate_prices_th_separator', ''));
            $pin_price = $price;
            $price = wpestate_format_number_price($price, $th_separator);
            
            if($where_currency == 'before') {
                $price = $wpestate_currency.$price;
            } else {
                $price = $price.$wpestate_currency;
            }
            
            $indian_format = esc_html(wpresidence_get_option('wp_estate_price_indian_format', ''));
            
            if(wpresidence_get_option('wp_estate_use_price_pins_full_price', '') == 'no') {
                if($indian_format == 'no') {
                    $pin_price = wpestate_price_pin_converter($pin_price, $where_currency, $wpestate_currency);
                } else {
                    $pin_price = wpestate_moneyFormatIndia_short($pin_price);
                }
            } else {
                $pin_price = '<span class="infocur infocur_first">'.$price_label_before.'</span>'.$price.'<span class="infocur">'.$price_label.'</span>';
            }
            
            $price = '<span class="infocur infocur_first">'.$price_label_before.'</span>'.$price.'<span class="infocur">'.$price_label.'</span>';
        }
        }
        
        // Get property details from cached meta
        $rooms = $cached_data['meta']['property_bedrooms'] ?? '';
        $bathrooms = $cached_data['meta']['property_bathrooms'] ?? '';
        $size = wpestate_get_converted_measure($the_id, 'property_size');
        
        // Get property title
        $title_orig = $cached_data['title'] ?? wpresidence_get_sanitized_truncated_title($the_id,0);
        $title_orig = str_replace('%', '', $title_orig);
        
        // Get property thumbnail
        if(!empty($cached_data['media'])) {
            // Need to recreate the HTML markup since we have only image URL in cache
            $first_media_id = array_key_first($cached_data['media']);
            if(isset($cached_data['media'][$first_media_id]['agent_picture_thumb'])) {
                $img_url = $cached_data['media'][$first_media_id]['agent_picture_thumb'];
                $thumbnail = '<img width="120" height="120" src="' . esc_url($img_url) . '" class="attachment-agent_picture_thumb size-agent_picture_thumb wp-post-image" alt="" loading="lazy">';
            } else {
                $thumbnail = get_the_post_thumbnail($the_id, 'agent_picture_thumb');
            }
            
            if(isset($cached_data['media'][$first_media_id]['widget_thumb'])) {
                $img_url = $cached_data['media'][$first_media_id]['widget_thumb'];
                $widget_thumb = '<img width="105" height="70" src="' . esc_url($img_url) . '" class="attachment-widget_thumb size-widget_thumb wp-post-image" alt="" loading="lazy">';
            } else {
                $widget_thumb = get_the_post_thumbnail($the_id, 'widget_thumb');
            }
        } else {
            $thumbnail = get_the_post_thumbnail($the_id, 'agent_picture_thumb');
            $widget_thumb = get_the_post_thumbnail($the_id, 'widget_thumb');
        }
        
        // Prepare marker data array
        $place_markers = array();
        $place_markers[] = rawurlencode($title_orig); // 0
        $place_markers[] = $gmap_lat; // 1
        $place_markers[] = $gmap_long; // 2
        $place_markers[] = $counter; // 3
        $place_markers[] = rawurlencode($thumbnail); // 4
        $place_markers[] = rawurlencode($price); // 5
        $place_markers[] = rawurlencode($single_first_type); // 6
        $place_markers[] = rawurlencode($single_first_action); // 7
        $place_markers[] = rawurlencode($pin); // 8
        $place_markers[] = rawurlencode(esc_url(get_permalink($the_id))); // 9
        $place_markers[] = $the_id; // 10
        $place_markers[] = $clean_price; // 11
        $place_markers[] = $rooms; // 12
        $place_markers[] = $bathrooms; // 13
        $place_markers[] = $size; // 14
        $place_markers[] = rawurlencode($single_first_type_name); // 15
        $place_markers[] = rawurlencode($single_first_action_name); // 16
        $place_markers[] = rawurlencode($pin_price); // 17
        $place_markers[] = rawurlencode($widget_thumb); // 18
        
        return $place_markers;
    }
endif;

/**
 * Build marker payloads from a WP _Query result set.
 *
 * Iterates over queried properties and assembles marker arrays using direct
 * meta/term reads. This helper is used by map pin generation workflows.
 *
 * @param WP _Query|string $prop_selection Property query object.
 * @return array Marker payload list.
 */


if( !function_exists('wpestate_listing_pins_marker_creation') ):
    function wpestate_listing_pins_marker_creation($prop_selection=''){
        // Step 1: Initialize marker-generation context.
        $counter                    =   0;
        $unit                       =   wpresidence_get_option('wp_estate_measure_sys','');
        $wpestate_currency                   =   wpresidence_get_option('wp_estate_currency_symbol','');
        $where_currency             =   wpresidence_get_option('wp_estate_where_currency_symbol', '');
        $markers                    =   array();

        // Step 2: Iterate over properties and build one marker payload per post.
        while($prop_selection->have_posts()): $prop_selection->the_post();

            $the_id      =   get_the_ID();
            // Step 2.1: Read geolocation coordinates for the current property.
            $gmap_lat    =      floatval(get_post_meta($the_id, 'property_latitude', true));
            $gmap_long   =      floatval(get_post_meta($the_id, 'property_longitude', true));

            if($gmap_lat==0){
                $gmap_lat='';
            }

            if($gmap_long==0){
                $gmap_long='';
            }

            $slug        =   array();
            $prop_type   =   array();

            $types       =   get_the_terms($the_id,'property_category' );
            $types_act   =   get_the_terms($the_id,'property_action_category' );

            $prop_type_name=array();
            if ( $types && ! is_wp_error( $types ) ) {
                foreach ($types as $single_type) {
                    $prop_type[]      = $single_type->slug;
                    $prop_type_name[] = $single_type->name;
                    $slug             = $single_type->slug;
                    $parent_term      = $single_type->parent;

                }

                $single_first_type      = $prop_type[0];
                $single_first_type_pin  = $prop_type[0];
                if($parent_term!=0){
                    $single_first_type=$single_first_type.wpestate_add_parent_infobox($parent_term,'property_category');
                }
                $single_first_type_name= $prop_type_name[0];
            }else{
                $single_first_type        ='';
                $single_first_type_name   ='';
                $single_first_type_pin    ='';
            }


            // Step 2.2: Read action taxonomy values for pin naming/rendering.
            $prop_action        =   array();
            $prop_action_name   =   array();
            if ( $types_act && ! is_wp_error( $types_act ) ) {
                  foreach ($types_act as $single_type) {
                    $prop_action[]      =   $single_type->slug;
                    $prop_action_name[] =   $single_type->name;
                    $slug               =   $single_type->slug;
                    $parent_term        =   $single_type->parent;
                   }
            $single_first_action        = $prop_action[0];
            $single_first_action_pin    = $prop_action[0];

            if($parent_term!=0){
                $single_first_action=$single_first_action.wpestate_add_parent_infobox($parent_term,'property_action_category');
            }
            $single_first_action_name   = $prop_action_name[0];
            }else{
                $single_first_action        ='';
                $single_first_action_name   ='';
                $single_first_action_pin    ='';
            }

            // composing name of the pin
            if($single_first_action=='' || $single_first_action ==''){
                  $pin                   =  sanitize_key(wpestate_limit54($single_first_type_pin.$single_first_action_pin));
            }else{
                  $pin                   =  sanitize_key(wpestate_limit27($single_first_type_pin)).sanitize_key(wpestate_limit27($single_first_action_pin));
            }
            $counter++;


            // Step 2.3: Build visible price and compact pin price labels.
            $price              =   floatval    ( get_post_meta($the_id, 'property_price', true) );
            $price_label        =   esc_html    ( get_post_meta($the_id, 'property_label', true) );
            $price_label_before =   esc_html    ( get_post_meta($the_id, 'property_label_before', true) );
            $clean_price        =   floatval    ( get_post_meta($the_id, 'property_price', true) );

            $hidingprice = wpestate_hide_show_price( $the_id );

            if ( $hidingprice ) {
                $hiddenPriceLabel = wpresidence_get_option( 'wp_estate_property_hide_price_text', '', 'Price on application' );
                // If price is hidden, return empty string or print nothing
                $price = $hiddenPriceLabel;
                $pin_price = $hiddenPriceLabel;
            } else {
            // If price is not hidden, format it as usual

            if($price==0){
                $price      =   $price_label_before.''.$price_label;
                $pin_price  =   '';
            }else{
                $th_separator   =   stripslashes ( wpresidence_get_option('wp_estate_prices_th_separator','') );
                $pin_price      =   $price;
                 $price    =   wpestate_format_number_price($price,$th_separator);

                if($where_currency=='before'){
                    $price=$wpestate_currency.$price;
                }else{
                    $price=$price.$wpestate_currency;
                }

                $indian_format = esc_html(wpresidence_get_option('wp_estate_price_indian_format', ''));
     
                if( wpresidence_get_option('wp_estate_use_price_pins_full_price','')=='no'){
                   
                    if($indian_format=='no'){
                        $pin_price  =   wpestate_price_pin_converter($pin_price,$where_currency,$wpestate_currency);
                    }else{
                       $pin_price  =   wpestate_moneyFormatIndia_short($pin_price);
                    }


                }else{
                    $pin_price  =='<span class="infocur infocur_first">'.$price_label_before.'</span>'.$price.'<span class="infocur">'.$price_label.'</span>';
                   
                }

                $price='<span class="infocur infocur_first">'.$price_label_before.'</span>'.$price.'<span class="infocur">'.$price_label.'</span>';
            }
            }



            $rooms      =   get_post_meta($the_id, 'property_bedrooms', true);
            $bathrooms  =   get_post_meta($the_id, 'property_bathrooms', true);
            $size       =   wpestate_get_converted_measure( $the_id, 'property_size' );

            // Step 2.4: Compose marker payload in the exact order expected by JS.
            $place_markers  =   array();
            $title_orig     =   get_the_title();
            $title_orig     =   str_replace('%','', $title_orig);

            $place_markers[]    =   rawurlencode ($title_orig);//0
            $place_markers[]    =   $gmap_lat;//1
            $place_markers[]    =   $gmap_long;//2
            $place_markers[]    =   $counter;//3
            $place_markers[]    =   rawurlencode ( get_the_post_thumbnail($the_id,'agent_picture_thumb') );////4
            $place_markers[]    =   rawurlencode (  $price); //5
            $place_markers[]    =   rawurlencode ($single_first_type);//6
            $place_markers[]    =   rawurlencode ($single_first_action);//7
            $place_markers[]    =   rawurlencode ($pin);//8
            $place_markers[]    =   rawurlencode (  esc_url ( get_permalink() ) );//9
            $place_markers[]    =   $the_id;//10
            $place_markers[]    =   $clean_price;//11
            $place_markers[]    =   $rooms;//12
            $place_markers[]    =   $bathrooms;//13
            $place_markers[]    =   $size;//14
            $place_markers[]    =   rawurlencode ( $single_first_type_name);//15
            $place_markers[]    =   rawurlencode  ( $single_first_action_name);//16
            $place_markers[]    =   rawurlencode($pin_price);
            $place_markers[]    =   rawurlencode(get_the_post_thumbnail($the_id,'widget_thumb') );
            $markers[]=$place_markers;
        endwhile;

        // Step 3: Return full markers list for the caller.
        return $markers;
    }
endif;


/**
 * Run an on-demand pin query via the 2026 layer and return marker payload + result count.
 *
 * Delegates property querying to wpestate_query_2026_property() which handles
 * keyword filtering, featured ordering, geo filtering, and sold-property
 * exclusion internally. This function only builds the marker payloads.
 *
 * @param array  $params 2026-style params (taxonomy names, price_min/max, geo_lat, etc.).
 * @param int    $jump   Kept for backward compatibility (unused).
 * @return array Array with `markers` and `results` keys.
 */

if( !function_exists('wpestate_listing_pins_on_demand') ):
    function wpestate_listing_pins_on_demand( $params = array(), $jump = 0 ){
        // Step 1: Suppress object cache during marker generation.
        wp_suspend_cache_addition( true );

        // Step 2: Force settings needed by marker builder.
        // Marker creation uses the_post() loop so we need full post objects (not IDs).
        // We also need found_posts for the result count.
        $params['no_found_rows'] = false;
        $params['fields']        = '';

        // Step 3: Run the query through the 2026 layer (map context).
        // Keyword, featured ordering, geo, and sold exclusion are all handled inside.
        $result = wpestate_query_2026_property( 'map', $params );

        $prop_selection = $result['query'];

        // Step 4: Build markers from the query result set.
        $markers = wpestate_listing_pins_marker_creation( $prop_selection );

        // Step 5: Restore cache and clean up.
        wp_suspend_cache_addition( false );
        wp_reset_query();

        // Step 6: Return markers and total result count.
        return array(
            'markers' => $markers,
            'results' => $result['total'],
        );
    }
endif; // end   wpestate_listing_pins_on_demand

/**
 * Build the fallback company/contact marker payload.
 *
 * @return string JSON-encoded marker array used for the contact map.
 */



if( !function_exists('wpestate_contact_pin') ):
    function wpestate_contact_pin(){
            // Step 1: Resolve company label with default fallback.
            $place_markers=array();
            $company_name=esc_html(stripslashes( wpresidence_get_option('wp_estate_company_name','') ) );
            if($company_name==''){
                $company_name='Company Name';
            }

            // Step 2: Compose contact marker payload and return encoded JSON.
            $place_markers[0]    =   $company_name;
            $place_markers[1]    =   '';
            $place_markers[2]    =   '';
            $place_markers[3]    =   1;
            $place_markers[4]    =   (wpresidence_get_option('wp_estate_company_contact_image', '') );
            $place_markers[5]    =   '0';
            $place_markers[6]    =   'address';
            $place_markers[7]    =   'none';
            $place_markers[8]    =   '';
            return json_encode($place_markers);
    }
endif; // end   wpestate_contact_pin




/**
 * Build dot-prefixed parent taxonomy path used by marker infobox CSS classes.
 *
 * @param int    $parent_term Parent term ID.
 * @param string $taxonomy Taxonomy slug.
 * @return string|null Dot-prefixed parent slug chain.
 */


if( !function_exists('wpestate_add_parent_infobox') ):
    function wpestate_add_parent_infobox($parent_term,$taxonomy){
        // Step 1: Load the current parent term object.
        $parent_term = get_term_by( 'id', $parent_term, $taxonomy);
       
        if( isset($parent_term) ){
            // Step 2: Recursively build parent path from top to leaf parent.
            if( isset($paren_term->parent) && $parent_term->parent!=0){
                return  '.'.$parent_term->slug.wpestate_add_parent_infobox($parent_term->parent,$taxonomy);
            }else if( isset($paren_term->slug) ) {
                return '.'.$parent_term->slug;
            }
        }


}
endif;



/**
 * Build full map marker payloads with optional transient caching.
 *
 * Delegates the property query to wpestate_query_2026_property('map') which
 * handles keyword filtering, featured ordering, geo, and sold exclusion.
 * Supports default map queries, single-ID loads, and optional marker file writing.
 *
 * Callers may pass raw WP _Query $args (legacy) — the function converts them
 * to 2026 params internally. tax_query and meta_query are forwarded as raw
 * pass-through so pre-built clauses are preserved.
 *
 * @param string       $transient_appendix Cache key suffix.
 * @param int          $with_cache Whether transient cache should be used.
 * @param array|string $args Raw WP _Query args from callers (converted to 2026 params internally).
 * @param int          $jump When 0 and readsys enabled, writes markers file.
 * @param string       $wpestate_keyword Keyword used by title filter.
 * @param int|string   $id_array Single property ID override.
 * @return string|null JSON marker payload when not writing directly to file.
 */

if( !function_exists('wpestate_listing_pins') ):
function wpestate_listing_pins($transient_appendix='',$with_cache=1,$args='',$jump=0,$wpestate_keyword='',$id_array=''){
    // Step 1: Initialize map marker generation context.
    wp_suspend_cache_addition(true);
    $counter                    =   0;
    $unit                       =   wpresidence_get_option('wp_estate_measure_sys','');
    $wpestate_currency          =   wpresidence_get_option('wp_estate_currency_symbol','');
    $where_currency             =   wpresidence_get_option('wp_estate_where_currency_symbol', '');
    $markers        =   array();

    // Step 2: Set default transient appendix when no args provided.
    if ( $args == '' ) {
        $transient_appendix = 'default_pins';
    }

    // Step 3: Build cache-key appendix based on query variant.
    if ( intval( $id_array ) != 0 ) {
        $transient_appendix = 'default_pins_with_ids';
    } else {
        if ( ! empty( $wpestate_keyword ) ) {
            $transient_appendix .= 'prop_title_filter';
        }

        if ( isset( $args['meta_key'] ) && $args['meta_key'] == 'prop_featured' ) {
            $transient_appendix .= '_prop_featured';
        }
    }

    $transient_appendix = wpestate_set_transient_name_multilang( $transient_appendix );

    if ( isset( $_COOKIE['my_custom_curr_symbol'] ) ) {
        $transient_appendix .= '_' . $_COOKIE['my_custom_curr_symbol'];
    }
    if ( isset( $_COOKIE['my_measure_unit'] ) ) {
        $transient_appendix .= $_COOKIE['my_measure_unit'];
    }

    $show_sold = wpresidence_get_option( 'wp_estate_show_sold_items', '' );
    if ( $show_sold === 'no' ) {
        $transient_appendix .= '_nosold';
    }

    // Step 4: Read markers from transient cache when enabled.
    if ( $with_cache == 1 ) {
        $markers = wpestate_request_transient_cache( 'wpestate_markers' . $transient_appendix );
    } else {
        $markers = false;
    }

    // Step 5: Run query via 2026 layer and build markers when cache miss occurs.
    if ( $markers === false ) {
        $markers = array();

        // Step 5.1: Convert raw $args to 2026 $params.
        $params = array();

        if ( intval( $id_array ) != 0 ) {
            // Single property by ID.
            $params['p'] = intval( $id_array );
        } elseif ( is_array( $args ) ) {
            // Forward pagination/limit keys.
            $forward_keys = array(
                'posts_per_page',
                'paged',
                'offset',
                'fields',
                'cache_results',
                'update_post_meta_cache',
                'update_post_term_cache',
            );
            foreach ( $forward_keys as $key ) {
                if ( isset( $args[ $key ] ) ) {
                    $params[ $key ] = $args[ $key ];
                }
            }

            // Forward single-post ID.
            if ( isset( $args['p'] ) && intval( $args['p'] ) > 0 ) {
                $params['p'] = intval( $args['p'] );
            }

            // Forward pre-built tax_query as raw pass-through.
            if ( ! empty( $args['tax_query'] ) ) {
                $params['tax_query'] = $args['tax_query'];
            }

            // Forward pre-built meta_query as raw pass-through.
            if ( ! empty( $args['meta_query'] ) ) {
                $params['meta_query'] = $args['meta_query'];
            }
        }

        // Forward keyword search.
        if ( ! empty( $wpestate_keyword ) ) {
            $params['keyword'] = $wpestate_keyword;
        }

        // The 2026 'map' context handles featured ordering, sold exclusion,
        // and geo filtering automatically — no manual filter hooks needed.
        $result         = wpestate_query_2026_property( 'map', $params );
        $prop_selection = $result['query'];

        // Step 5.2: Build one marker payload for each property ID.
        foreach ( $prop_selection->posts as $key => $value ) {
            $counter++;
            $markers[] = wpestate_pin_unit_creation( $value, $wpestate_currency, $where_currency, $unit, $counter );
        }

        // Step 5.3: Cache generated marker payloads for later requests.
        if ( $with_cache == 1 ) {
            wpestate_set_transient_cache( 'wpestate_markers' . $transient_appendix, $markers, 4 * 60 * 60 );
        }
    }

    wp_suspend_cache_addition(false);
    wp_reset_query();

    // Step 6: Either persist marker JSON to file or return JSON directly.
    if ( wpresidence_get_option( 'wp_estate_readsys', '' ) == 'yes' && $jump == 0 ) {
        global $wp_filesystem;
        if ( empty( $wp_filesystem ) ) {
            require_once ( ABSPATH . '/wp-admin/includes/file.php' );
            WP_Filesystem();
        }

        $path = wpestate_get_pin_file_path();
        $wp_filesystem->put_contents( $path, json_encode( $markers ), FS_CHMOD_FILE );
    } else {
        return json_encode( $markers );
    }
}
endif; // end   wpestate_listing_pins




/**
 * Build marker JSON directly from an existing WP_ Query object.
 *
 * @param WP_ Query $prop_selection Query with property posts.
 * @param int      $jump Compatibility argument (unused here).
 * @return string JSON-encoded marker payload.
 */

if( !function_exists('wpestate_listing_pins_query_based') ):
function wpestate_listing_pins_query_based($prop_selection,$jump=0){
    // Step 1: Convert query result set to marker payload list.
    $markers    =   wpestate_listing_pins_marker_creation($prop_selection);
    // Step 2: Return JSON-encoded marker payloads.
    wp_suspend_cache_addition(false);
    return json_encode($markers);

}
endif; // end   wpestate_listing_pins



/**
 * Generate marker file for all published properties.
 *
 * @return void Writes marker JSON to the configured pin file path.
 */



if( !function_exists('wpestate_listing_pins_for_file') ):
function wpestate_listing_pins_for_file(){
    // Step 1: Build full-property query without cache-heavy WP _Query options.
    wp_suspend_cache_addition(true);


    $place_markers=$markers     =   array();

    $args = array(
        'post_type'                 =>    'estate_property',
        'post_status'               =>    'publish',
        'posts_per_page'            =>    -1,
        'cache_results'             =>    false,
        'update_post_meta_cache'    =>    false,
        'update_post_term_cache'    =>    false,
       );

    // Step 2: Execute query and generate marker payloads.
    $prop_selection =   new WP_Query($args);
    $path           =   wpestate_get_pin_file_path();

    $markers    =   wpestate_listing_pins_marker_creation($prop_selection);
    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once (ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();
    }

    $path=wpestate_get_pin_file_path();

    // Step 3: Persist marker payloads to disk.
    $wp_filesystem->put_contents($path, json_encode($markers), FS_CHMOD_FILE );


}
endif;





/**
 * Optimized map pin images function
 * 
 * Creates and caches pin image URLs for Google Maps with improved performance
 * using wpestate_get_cached_terms from taxonomy_transients_functions.php
 * 
 * @return string JSON-encoded array of pin image URLs
 * @since 4.0.0
 */
if( !function_exists('wpestate_pin_images') ):
function wpestate_pin_images(){
    // Try to get cached pin images
    $pins = get_transient('wpestate_pin_images');
    
    if($pins === false){
        $pins = array();
        
        // Get property categories and actions using the cached terms function
        $tax_terms = wpestate_get_cached_terms('property_action_category', array('hide_empty' => 0));
        $categories = wpestate_get_cached_terms('property_category', array('hide_empty' => 0));
        
        if(wpresidence_get_option('wp_estate_use_single_image_pin','') != 'yes'){
            if(is_array($tax_terms) && !is_wp_error($tax_terms)){
                foreach ($tax_terms as $tax_term) {
                    $name = sanitize_key(wpestate_limit64('wp_estate_'.$tax_term->slug));
                    $limit54 = sanitize_key(wpestate_limit54($tax_term->slug));
                    $pins[$limit54] = esc_html(wpresidence_get_option($name,'url'));
                }
            }
            
            if(is_array($categories) && !is_wp_error($categories)){
                foreach ($categories as $categ) {
                    $name = sanitize_key(wpestate_limit64('wp_estate_'.$categ->slug));
                    $limit54 = sanitize_key(wpestate_limit54($categ->slug));
                    $pins[$limit54] = esc_html(wpresidence_get_option($name,'url'));
                }
            }
            
            if(is_array($categories) && !is_wp_error($categories) && is_array($tax_terms) && !is_wp_error($tax_terms)){
                foreach ($tax_terms as $tax_term) {
                    foreach ($categories as $categ) {
                        $limit54 = sanitize_key(wpestate_limit27($categ->slug)).sanitize_key(wpestate_limit27($tax_term->slug));
                        $name = 'wp_estate_'.$limit54;
                        $pins[$limit54] = esc_html(wpresidence_get_option($name,'url'));
                    }
                }
            }
        }
        
        $pins['idxpin'] = esc_html(wpresidence_get_option('wp_estate_idxpin','url'));
        if($pins['idxpin'] == ''){
            $pins['idxpin'] = get_theme_file_uri('/css/css-images').'/sale.png';
        }
        
        $pins['single_pin'] = esc_html(wpresidence_get_option('wp_estate_single_pin','url'));
        if($pins['single_pin'] == ''){
            $pins['single_pin'] = get_theme_file_uri('/css/css-images').'/single.png';
        }
        
        $pins['cloud_pin'] = esc_html(wpresidence_get_option('wp_estate_cloud_pin','url'));
        if($pins['cloud_pin'] == ''){
            $pins['cloud_pin'] = get_theme_file_uri('/css/css-images').'/cloud.png';
        }
        
        $pins['userpin'] = esc_html(wpresidence_get_option('wp_estate_userpin','url'));
        if($pins['userpin'] == ''){
            $pins['userpin'] = get_theme_file_uri('/css/css-images').'/userpin.png';
        }
        
        // Set cache with direct transient function
        set_transient('wpestate_pin_images', $pins, 4*60*60);
    }
    
    return json_encode($pins);
}
endif;

/**
 * Reset pin images cache when taxonomy terms are modified
 * 
 * @param int $term_id The term ID that was modified
 * @param int $tt_id Term taxonomy ID
 * @param string $taxonomy Taxonomy name
 */
function wpestate_reset_pin_images_cache($term_id, $tt_id, $taxonomy) {
    if($taxonomy == 'property_action_category' || $taxonomy == 'property_category') {
        delete_transient('wpestate_pin_images');
    }
}

// Hook into term actions to clear pin cache
add_action('edited_property_action_category', 'wpestate_reset_pin_images_cache', 10, 3);
add_action('edited_property_category', 'wpestate_reset_pin_images_cache', 10, 3);
add_action('created_property_action_category', 'wpestate_reset_pin_images_cache', 10, 3);
add_action('created_property_category', 'wpestate_reset_pin_images_cache', 10, 3);
add_action('delete_property_action_category', 'wpestate_reset_pin_images_cache', 10, 3);
add_action('delete_property_category', 'wpestate_reset_pin_images_cache', 10, 3);


?>
