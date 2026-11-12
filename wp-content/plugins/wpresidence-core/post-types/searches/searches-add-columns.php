<?php
/**
 * WpResidence Search Admin Columns
 *
 * This file extends the WordPress admin interface for the 'wpestate_search' custom post type
 * by adding and populating custom columns. It enhances the search management experience by
 * displaying recipient email addresses and saved search parameters directly in the list view.
 *
 * The file contains two main functions:
 * 1. A filter to add custom columns to the admin search list
 * 2. An action to populate those columns with relevant data from post meta
 *
 * @package    WpResidence
 * @subpackage Search
 * @version    1.0
 * @author     WpResidence
 */

/////////////////////////////////////////////////////////////////////////////////////
/// populate the invoice list with extra columns
/////////////////////////////////////////////////////////////////////////////////////

/**
 * Adds custom columns to the wpestate_search post type admin list
 *
 * This function filters the default WordPress columns for the search management
 * page and adds two custom columns:
 * - 'by_email': Displays the email address the search results were sent to
 * - 'parameters': Shows the saved search parameters in a readable format
 *
 * @param array $columns The default WordPress admin columns
 * @return array Modified array of columns with custom additions
 */
add_filter( 'manage_edit-wpestate_search_columns', 'wpestate_search_my_columns' );
if( !function_exists('wpestate_search_my_columns') ):
function wpestate_search_my_columns( $columns ) {
 
    $columns['by_email']        = esc_html__('Email to','wpresidence-core');
    $columns['parameters']      = esc_html__('Search parameters','wpresidence-core');
 
    return  $columns;
}
endif; // end   wpestate_invoice_my_columns  

/**
 * Populates the custom columns with data from post meta
 *
 * This function retrieves and displays the appropriate data for each custom column:
 * - For 'by_email' column: Displays the recipient email address stored in post meta
 * - For 'parameters' column: Retrieves, decodes, and formats the saved search parameters
 *   using a helper function to present them in a human-readable format
 *
 * The search parameters are stored as JSON-encoded arrays and require decoding before display.
 * The function also retrieves global search configuration settings to properly format the parameters.
 *
 * @param string $column The current column being processed
 * @return void Outputs column content directly
 */
add_action( 'manage_posts_custom_column', 'wpestate_search_populate_columns' );
if( !function_exists('wpestate_search_populate_columns') ):
function wpestate_search_populate_columns( $column ) {
    $the_id=get_the_ID();
    
    // Display recipient email address for the 'by_email' column
    if ( 'by_email' == $column ) {
        echo get_post_meta($the_id, 'user_email', true);
    }
    
    // Display formatted search parameters for the 'parameters' column
    if ( 'parameters' == $column ) {
        // Retrieve and decode the main search arguments
        $search_arguments           =  get_post_meta($the_id, 'search_arguments', true) ;
        $search_arguments_decoded   = (array)json_decode($search_arguments,true);
        
        // Retrieve and decode the meta arguments (advanced search criteria)
        $meta_arguments             =  get_post_meta($the_id, 'meta_arguments', true) ;
        $meta_arguments             = (array)json_decode($meta_arguments,true);
        
        // Get global search configuration settings
        $custom_advanced_search         =   'yes';
        $adv_search_what                =   wpresidence_get_option('wp_estate_adv_search_what','');
        $adv_search_how                 =   wpresidence_get_option('wp_estate_adv_search_how','');
        $adv_search_label               =   wpresidence_get_option('wp_estate_adv_search_label','');    
        
        // Use helper function to format and display search parameters
        print wpestate_show_search_params_new($meta_arguments,$search_arguments_decoded,$custom_advanced_search, $adv_search_what,$adv_search_how,$adv_search_label);
    }
   
}  
endif;