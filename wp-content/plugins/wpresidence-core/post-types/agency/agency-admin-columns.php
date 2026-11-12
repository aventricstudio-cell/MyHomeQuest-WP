<?php
/**
 * Agency Admin Columns
 *
 * This file manages the custom columns displayed in the WordPress admin area
 * for the 'estate_agency' custom post type. It modifies the default WordPress
 * columns to add agency-specific information like agency image, city, action,
 * category, email, and phone.
 *
 * @package WPResidence
 * @subpackage AdminColumns
 * @version 1.0
 * @author WPResidence Team
 * @copyright Copyright (c) WPResidence
 * @license GPL2+
 */

/**
 * Filter to modify columns for estate_agency post type admin list
 *
 * WordPress hook: manage_edit-{post_type}_columns
 * This attaches our custom function to the filter that WordPress uses
 * when building the column headers for the post list table
 */
add_filter( 'manage_edit-estate_agency_columns', 'wpestate_my_columns_agency' );

/**
 * Custom column configuration for agency listings in admin
 *
 * This function modifies the default WordPress columns for the agency post type.
 * It removes unnecessary columns like 'comments' and adds custom columns specific
 * to agencies such as ID, thumbnail image, city, action, category, email, and phone.
 *
 * @since 1.0
 * @param array $columns Default WordPress admin columns
 * @return array Modified columns array with custom agency columns
 */
if( !function_exists('wpestate_my_columns_agency') ):
    function wpestate_my_columns_agency( $columns ) {
        // Store specific columns that we want to preserve
        $slice = array_slice($columns, 2, 2);
        
        // Remove the comments column from both arrays
        unset( $columns['comments'] );
        unset( $slice['comments'] );
        
        // Split the columns array at position 2 to insert our custom columns
        $splice = array_splice($columns, 2);
        
        // Add custom agency columns
        $columns['estate_ID']               = esc_html__('ID','wpresidence-core');
        $columns['estate_agency_thumb']      = esc_html__('Image','wpresidence-core');
        $columns['estate_agency_city']       = esc_html__('City','wpresidence-core');
        $columns['estate_agency_action']     = esc_html__('Action','wpresidence-core');
        $columns['estate_agency_category']   = esc_html__( 'Category','wpresidence-core');
        $columns['estate_agency_email']      = esc_html__('Email','wpresidence-core');
        $columns['estate_agency_phone']      = esc_html__('Phone','wpresidence-core');
        
        // Merge our custom columns with preserved columns to maintain proper order
        return array_merge($columns, array_reverse($slice));
    }
endif; // end wpestate_my_columns