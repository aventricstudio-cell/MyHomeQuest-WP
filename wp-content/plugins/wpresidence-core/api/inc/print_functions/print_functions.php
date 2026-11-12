<?php

/**
 * Property Functions
 * This file contains functions related to property management and display.
 */

/**
 * Print Property Overview
 *
 * @param int $post_id The ID of the property post
 */
function wpestate_property_overview_print( $post_id ) {

    setup_postdata( $post = get_post( $post_id ) );

    // Print Property Overview
    wpestate_property_print_wrapper( 'Overview', wpestate_property_overview_v2( $post_id ) );

    wp_reset_postdata();

}

/**
 * Print Property Features
 *
 * @param int $post_id The ID of the property post
 */
if( !function_exists('estate_listing_address_printing') ):
function estate_listing_address_printing($post_id){

    $property_address   = esc_html( get_post_meta($post_id, 'property_address', true) );
    $property_city      = strip_tags (  get_the_term_list($post_id, 'property_city', '', ', ', '') );
    $property_area      = strip_tags ( get_the_term_list($post_id, 'property_area', '', ', ', '') );
    $property_county    = strip_tags ( get_the_term_list($post_id, 'property_county_state', '', ', ', '')) ;
    $property_zip       = esc_html(get_post_meta($post_id, 'property_zip', true) );
    $property_country   = esc_html(get_post_meta($post_id, 'property_country', true) );

    $return_string='';

    if ($property_address != ''){
        $return_string.='<div class="listing_detail col-md-4"><strong>'.esc_html__('Address','wpresidence').':</strong> ' . $property_address . '</div>';
    }
    if ($property_city != ''){
        $return_string.= '<div class="listing_detail col-md-4"><strong>'.esc_html__('City','wpresidence').':</strong> ' .$property_city. '</div>';
    }
    if ($property_area != ''){
        $return_string.= '<div class="listing_detail col-md-4"><strong>'.esc_html__('Area','wpresidence').':</strong> ' .$property_area. '</div>';
    }
    if ($property_county != ''){
        $return_string.= '<div class="listing_detail col-md-4"><strong>'.esc_html__('State/County','wpresidence').':  </strong> ' . $property_county . '</div>';
    }
   
    if ($property_zip != ''){
        $return_string.= '<div class="listing_detail col-md-4"><strong>'.esc_html__('Zip','wpresidence').':</strong> ' . $property_zip . '</div>';
    }
    if ($property_country != '') {
        $return_string.= '<div class="listing_detail col-md-4"><strong>'.esc_html__('Country','wpresidence').':</strong> ' . $property_country . '</div>';
    }


    return  $return_string;
}
endif; // end   estate_listing_address

/*
 * Get content for print
 *
 *
 *
 *
 *
 */

function wpestate_print_get_custom_page_content($post_id) {
    // Store the current global post
    global $post;
    $original_post = $post;
    
    // Get the page content
    $page_object = get_post($post_id);
    $content = $page_object->post_content;
    
    // Set up the post data for your specific post ID
    $post = $page_object;
    setup_postdata($post);
    
    // Apply the filter
    $content = apply_filters('the_content', $content);
    $content = do_shortcode($content);
    
    // Restore the original post data
    $post = $original_post;
    if ($original_post) {
        setup_postdata($original_post);
    } else {
        wp_reset_postdata();
    }
    
    return $content;
}


/**
 * Print Multi Units
 *
 * @param int $post_id The ID of the property post
 */
function wpestate_print_multi_units( $post_id ) {
    
    global $property_subunits_master;
    $has_multi_units=intval(get_post_meta($post_id, 'property_has_subunits', true));
    $property_subunits_master=intval(get_post_meta($post_id, 'property_subunits_master', true));

    print '<div class="print_property_subunits_wrapper">';
    if ($has_multi_units==1) {
        wpestate_property_print_wrapper( esc_html__('Available Units', 'wpresidence-core'), wpestate_shortcode_multi_units($post_id, $property_subunits_master, 1) );
    } else {
        if ($property_subunits_master!=0) {
            print '<div class="print-content">';
            wpestate_shortcode_multi_units($post_id, $property_subunits_master, 1);
            print '</div>';
        }
    }
    print '</div>';


}


/**
 * Print Listing Agent
 *
 * @param int $post_id The ID of the property post
 * @return string HTML content for the listing agent
 */
function wpestate_listing_agent( $post_id ) {
    
    $author_id      =  wpsestate_get_author($post_id);

    $realtor_details = wpestate_return_agent_details($post_id);

    $return_string = '';
    $name = isset($realtor_details['realtor_name']) ? $realtor_details['realtor_name'] : '';
    $agent_phone = isset($realtor_details['realtor_phone']) ? $realtor_details['realtor_phone'] : '';
    $agent_mobile = isset($realtor_details['realtor_mobile']) ? $realtor_details['realtor_mobile'] : '';
    $agent_skype = isset($realtor_details['realtor_skype']) ? $realtor_details['realtor_skype'] : '';
    $agent_email = isset($realtor_details['email']) ? $realtor_details['email'] : '';
    $preview_img = isset($realtor_details['realtor_image']) ? $realtor_details['realtor_image'] : ''; 
    
    if ($preview_img!='') {
        $return_string .= '<div class="print-col-img agent_print_image"><img src="'.$preview_img.'"></div>';
    }
    $return_string .= '<div class="print_agent_wrapper">';
    if ($name!='') {
        $return_string .= '<div class="listing_detail_agent col-md-4 agent_name"><strong>'.esc_html__('Name', 'wpresidence-core').':</strong> '.$name.'</div>';
    }
    if ($agent_phone!='') {
        $return_string .= '<div class="listing_detail_agent col-md-4"><strong>'.esc_html__('Telephone', 'wpresidence-core').':</strong> '.$agent_phone.'</div>';
    }
    if ($agent_mobile!='') {
        $return_string .= '<div class="listing_detail_agent col-md-4"><strong>'.esc_html__('Mobile', 'wpresidence-core').':</strong> '.$agent_mobile.'</div>';
    }
    if ($agent_skype!='') {
        $return_string .= '<div class="listing_detail_agent col-md-4"><strong>'.esc_html__('Skype', 'wpresidence-core').':</strong> '.$agent_skype.'</div>';
    }
    if ($agent_email!='') {
        $return_string .= '<div class="listing_detail_agent col-md-4"><strong>'.esc_html__('Email', 'wpresidence-core').':</strong> '.$agent_email.'</div>';
    }
    $return_string .= '</div>';

    return $return_string;

}


/**
 * Print Listing Images
 *
 * @param int $post_id The ID of the property post
 */

function wpestate_print_listing_images( $post_id )  {

    $post_attachments = wpestate_generate_property_slider_image_ids($post_id,true);
    $return_string = '';

    foreach ($post_attachments as $attachment) {
        $original       =   wp_get_attachment_image_src($attachment, 'full');
        if(isset($original[0]) ){
            $return_string .= '<div class="print-col-img printimg"><img src="'. $original[0].'"></div>';
        }
    }

    return $return_string;

}


/**
 * Print Listing Other Agents
 *
 * @param int $post_id The ID of the property post
 */
function wpestate_print_listing_other_agents( $post_id )  {
    
    $agents_secondary = get_post_meta($post_id, 'property_agent_secondary', true);

    $return_string = '';

    if( is_array($agents_secondary) && !empty($agents_secondary) && $agents_secondary[0]!=''  ) {
        
        $agents_sec_list = implode(',',$agents_secondary);
        $args_other_agents = array(
            'post_type'         => 'estate_agent',
            'posts_per_page'    => -1 ,
            'post__in'         =>  $agents_secondary
        );
    
        $agent_selection = new WP_Query($args_other_agents);
    
        while ($agent_selection->have_posts()): $agent_selection->the_post();
            $agent_id   =   get_the_ID();
            $realtor_details    = wpestate_return_agent_details('',$agent_id);

            $return_string .= '<div class="print-col-img agent_print_image"><img src="'.$realtor_details['realtor_image'].'"></div>';
            $return_string .= '<div class="print_agent_wrapper">';
            $return_string .= '<div class="listing_detail_agent col-md-4 agent_name"><strong>'.esc_html__('Name', 'wpresidence-core').':</strong> '.$realtor_details['realtor_name'].'</div>';
            $return_string .= '<div class="listing_detail_agent col-md-4"><strong>'.esc_html__('Position', 'wpresidence-core').':</strong> '.$realtor_details['realtor_position'].'</div>';
            $return_string .= '</div>';
        endwhile;

        wp_reset_postdata();

    }

    return $return_string;

}

/**
 * Print Listing Reviews
 *
 * @param int $post_id The ID of the property post
 * @return string HTML content for the listing reviews
 */
function wpestate_print_listing_reviews( $post_id )   {
    
    $per_page = 5; // Set the number of reviews per page
    
    $reviews_content = wpestate_display_reviews_summary_paginated( $post_id, $per_page );

    return $reviews_content;

}

/**
 * Print Property Map
 *
 * @param int $post_id The ID of the property post
 * @return string HTML content for the property map
 */
function wpestate_print_display_map( $post_id ) {

    ob_start();
    wpestate_display_property_overview_map($post_id);
    $map_content = ob_get_clean();

    if ( !empty($map_content) ) {
        return '<div class="print_property_map_wrapper">' . $map_content . '</div>';
    }

    return '';
}

/**
 * Print Property Floor Plans
 *
 * @param int $post_id The ID of the property post
 * @return string HTML content for the property floor plans
 */
function wpestate_print_floor_plans( $post_id ) {

    ob_start();
    estate_floor_plan($post_id);
    $floor_plans = ob_get_clean();

    if ( !empty($floor_plans) ) {
        return '<div class="print_property_floor_plans_wrapper">' . $floor_plans . '</div>';
    }

    return '';
}

/**
 * Print Property Wrapper
 *
 * @param string $title The title for the print section
 * @param string $content The content to be printed
 */
function wpestate_property_print_wrapper( $title, $content )  {

    if ( empty( $content ) )
        return;

    print '<div class="print_header"><h2>'.wp_kses_post($title).'</h2></div>';
    print '<div class="print-content">';
    print $content;
    print ' </div>';

}