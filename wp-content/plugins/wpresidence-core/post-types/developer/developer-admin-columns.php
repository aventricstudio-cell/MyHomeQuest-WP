<?php
/**
 * Developer Columns Management for WP Estate
 * 
 * This file customizes the admin columns displayed in the estate_developer post type listing.
 * It removes default WordPress columns like 'comments' and adds custom columns specific 
 * to real estate developers including ID, image, location information, contact details, 
 * and classification data.
 * 
 * The custom columns are arranged in a specific order while preserving certain
 * WordPress default columns in their original positions.
 * 
 * @package WPResidence
 * @subpackage Admin
 * @since 1.0.0
 */

/**
 * Filter hook to modify the columns displayed in the estate_developer post type admin listing
 */
add_filter( 'manage_edit-estate_developer_columns', 'wpestate_my_columns_developer' );

if( !function_exists('wpestate_my_columns_developer') ):
/**
 * Custom function to modify the estate_developer columns in admin
 * 
 * This function handles the customization of columns for the developer listings
 * in the WordPress admin interface. It:
 * 1. Preserves certain default columns
 * 2. Removes unwanted columns (like comments)
 * 3. Adds custom estate_developer specific columns
 * 4. Reorders the columns in a logical sequence
 * 
 * The column manipulation uses array operations to achieve the desired order
 * while maintaining compatibility with potential changes to WordPress core.
 * 
 * @param array $columns The default columns array provided by WordPress
 * @return array Modified columns array with custom developer-specific columns
 */
    function wpestate_my_columns_developer( $columns ) {
        // Store a slice of columns (positions 2-3) to preserve some default columns
        $slice = array_slice($columns, 2, 2);
        
        // Remove the comments column from both the main columns array and the preserved slice
        unset( $columns['comments'] );
        unset( $slice['comments'] );
        
        // Remove all columns after position 2 and store them separately
        $splice = array_splice($columns, 2);
        
        // Add custom columns for real estate developers
        $columns['estate_ID']                   = esc_html__('ID','wpresidence-core');
        $columns['estate_developer_thumb']      = esc_html__('Image','wpresidence-core');
        $columns['estate_developer_city']       = esc_html__('City','wpresidence-core');
        $columns['estate_developer_action']     = esc_html__('Action','wpresidence-core');
        $columns['estate_developer_category']   = esc_html__( 'Category','wpresidence-core');
        $columns['estate_developer_email']      = esc_html__('Email','wpresidence-core');
        $columns['estate_developer_phone']      = esc_html__('Phone','wpresidence-core');
        
        // Merge the modified columns with the reversed preserved slice
        // This maintains some original columns in their respective positions
        return array_merge($columns, array_reverse($slice));
    }
endif; // end wpestate_my_columns