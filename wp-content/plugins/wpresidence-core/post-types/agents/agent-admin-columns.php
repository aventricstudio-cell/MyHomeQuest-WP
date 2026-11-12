<?php
/**
 * Agent Columns Management for WP Estate
 * 
 * This file handles the customization of the admin columns for estate_agent post type.
 * It removes default columns like 'comments' and adds custom columns specific to real estate agents
 * such as ID, image, city, action, category, email, and phone number.
 * 
 * @package WPResidence
 * @subpackage Admin
 * @since 1.0.0
 */

/**
 * Filter hook to modify the columns displayed in the estate_agent post type admin listing
 */
add_filter( 'manage_edit-estate_agent_columns', 'wpestate_my_columns_agent' );

if( !function_exists('wpestate_my_columns_agent') ):
/**
 * Custom function to modify the estate_agent columns in admin
 * 
 * This function handles the rearrangement and addition of custom columns
 * for the estate_agent post type in the WordPress admin area.
 * 
 * @param array $columns The default columns array provided by WordPress
 * @return array Modified columns array with custom estate agent fields
 */
function wpestate_my_columns_agent( $columns ) {
    // Store a slice of the original columns (positions 2-3) for later use
    $slice = array_slice($columns, 2, 2);
    
    // Remove the comments column from both the main columns array and the slice
    unset( $columns['comments'] );
    unset( $slice['comments'] );
    
    // Remove all columns after position 2 and store them
    $splice = array_splice($columns, 2);
    
    // Add custom columns for estate agents
    $columns['estate_ID']               = esc_html__('ID','wpresidence-core');
    $columns['estate_agent_thumb']      = esc_html__('Image','wpresidence-core');
    $columns['estate_agent_city']       = esc_html__('City','wpresidence-core');
    $columns['estate_agent_action']     = esc_html__('Action','wpresidence-core');
    $columns['estate_agent_category']   = esc_html__( 'Category','wpresidence-core');
    $columns['estate_agent_email']      = esc_html__('Email','wpresidence-core');
    $columns['estate_agent_phone']      = esc_html__('Phone','wpresidence-core');
    
    // Merge the modified columns with the reversed slice to maintain certain original columns
    return array_merge($columns, array_reverse($slice));
}
endif; // end wpestate_my_columns