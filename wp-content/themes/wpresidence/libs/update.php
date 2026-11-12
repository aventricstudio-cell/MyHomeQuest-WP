<?php

/**
 * Rebuild `hidden_address` meta for all published properties.
 *
 * Iterates every `estate_property`, concatenates address-related fields
 * (address, zip, area, city, county/state), and stores the result in
 * `hidden_address` post meta.
 *
 * @return void
 */
if(function_exists('wpestate_update_hiddent_address')):
function wpestate_update_hiddent_address(){
    // Step 1: Disable cache additions during the bulk update.
    wp_suspend_cache_addition(true);    

    // Step 2: Query all published properties.
    $args = array(
             'post_type'                 =>    'estate_property',
             'post_status'               =>    'publish',
             'posts_per_page'            =>    -1,
             'cache_results'             =>    false,
             'update_post_meta_cache'    =>    false,
             'update_post_term_cache'    =>    false,
            );	

    $prop_selection =   new WP_Query( $args);
    $hidden_address='';

    // Step 3: Build hidden address for each property and persist it.

    while ( $prop_selection->have_posts() ): 
        $prop_selection->the_post();

        $edit_id            =   get_the_ID();
        $hidden_address     =   '';


        $property_address               =   esc_html( get_post_meta($edit_id, 'property_address', true) );
        $hidden_address.= $property_address.', ';

        $property_zip                   =   esc_html( get_post_meta($edit_id, 'property_zip', true) );
        $hidden_address.= $property_zip.', ';


        $property_area_array            =   get_the_terms($edit_id, 'property_area');
        // Use the first area term when available.
        if(isset($property_area_array [0])){
            $property_area                  =   $property_area_array [0]->name;
        }
        $hidden_address .=$property_area.', ';  

        $property_city_array            =   get_the_terms($edit_id, 'property_city');
        if(isset($property_city_array [0])){
            $property_city                  =   $property_city_array [0]->name;
        }
        $hidden_address .=$property_city.', ';



        $property_county_state_array            =   get_the_terms($edit_id, 'property_county_state');
        // Use the first county/state term when available.
        if(isset($property_county_state_array [0])){
            $property_county_state                 =   $property_county_state_array [0]->name;
        }
        $hidden_address .=$property_county_state.', ';

        echo ' </br>'.$edit_id.' '.$hidden_address;

        update_post_meta($edit_id, 'hidden_address', $hidden_address);


    endwhile;

    // Step 4: Reset WP query globals and re-enable cache additions.
    wp_reset_query();
    wp_reset_postdata();
    wp_suspend_cache_addition(false);



}
endif;



/**
 * Rebuild `hidden_address` meta for a single property.
 *
 * Concatenates address-related fields and all assigned terms from area, city,
 * and county/state taxonomies into a single `hidden_address` meta value.
 *
 * @param int $edit_id Property post ID.
 * @return void
 */
if(!function_exists('wpestate_update_hiddent_address_single')):
function wpestate_update_hiddent_address_single($edit_id){
        // Step 1: Normalize input and initialize output buffer.
        $edit_id= intval($edit_id);
        $hidden_address     =   '';

        // Step 2: Initialize local taxonomy placeholders.
        $property_area='';
        $property_city='';
        $property_county_state ='';
        
        // Step 3: Append base address fields.
        $property_address               =   esc_html( get_post_meta($edit_id, 'property_address', true) );
        $hidden_address.= $property_address.', ';

        $property_zip                   =   esc_html( get_post_meta($edit_id, 'property_zip', true) );
        $hidden_address.= $property_zip.', ';


        // Step 4: Append all assigned property area terms.
        $property_area_array            =   get_the_terms($edit_id, 'property_area');
        if(is_array($property_area_array)){
            foreach ($property_area_array as $item) {
              $hidden_address .=$item->name.', ';
            }
        }
      
        
        // Step 5: Append all assigned property city terms.
        $property_city_array            =   get_the_terms($edit_id, 'property_city');    
        if(is_array($property_city_array)){
            foreach ($property_city_array as $item) {
              $hidden_address .=$item->name.', ';
            }
        }



        // Step 6: Append all assigned county/state terms.
        $property_county_state_array            =   get_the_terms($edit_id, 'property_county_state');      
        if(is_array($property_county_state_array)){
            foreach ($property_county_state_array as $item) {
              $hidden_address .=$item->name.', ';
            }
        }
     

        // Step 7: Persist rebuilt hidden address meta.
        update_post_meta($edit_id, 'hidden_address', $hidden_address);




}
endif;
