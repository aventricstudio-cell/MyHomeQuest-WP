<?php

/**
 * WpResidence Theme - Layout Array Function
 *
 * This file contains the wpestate_return_layout_array function, which provides
 * layout data for the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Functions
 * @since 1.0.0
 *
 * @uses No external dependencies
 *
 * Usage:
 * $layouts = wpestate_return_layout_array();
 * or
 * $specific_layout = wpestate_return_layout_array(1);
 */

if ( ! function_exists( 'wpestate_return_layout_array' ) ) :
    /**
     * Return layouts data for WpResidence theme
     *
     * This function defines and returns layout configurations used in the theme.
     * It can return all layouts or a specific layout based on the input parameter.
     *
     * @param int|string $version Optional. The specific layout version to return.
     *                            If empty, returns all layouts.
     * @return array An array of layout configurations
     */
    function wpestate_return_layout_array( $version = '' ) {
        // Define the global layouts array
        $global_layouts = array(
            1 => array(
                'title'   => array( 'size' => 'col-md-12' ),
                'media'   => array( 'size' => 'col-md-9' ),
                'content' => array( 'size' => 'col-md-9' ),
                'sidebar' => array( 'size' => 'col-md-3' ),
            ),
            2 => array(
                'media'   => array( 'size' => 'col-md-12' ),
                'title'   => array( 'size' => 'col-md-12' ),
                'content' => array( 'size' => 'col-md-9' ),
                'sidebar' => array( 'size' => 'col-md-3' ),
            ),
        );

        // Check if a specific version is requested
        if ( '' === $version || 0 === intval( $version ) ) {
            return $global_layouts;
        } else {
            // Return the specific layout if it exists, otherwise return an empty array
            return isset( $global_layouts[ intval( $version ) ] ) ? $global_layouts[ intval( $version ) ] : array();
        }
    }
endif;




/**
 * Load and set up the property page layout
 *
 * This function prepares all necessary data and includes the appropriate layout template
 * for a property page based on the specified version.
 *
 * @param int $version The layout version to load
 * @param int $postID The ID of the property post
 */

if(!function_exists('wpestate_load_property_page_layout')):
function wpestate_load_property_page_layout($version,$postID){
    global $post;


    // data need down the line
    if(function_exists('wpestate_api_get_cached_post_data')){
        $property_unit_cached_data = wpestate_api_get_cached_post_data($postID, 'estate_property');
    }else{
        $property_unit_cached_data =array();
    }
    $property_city = wpestate_get_property_terms_from_cache($postID, 'property_city', $property_unit_cached_data);
    $property_area = wpestate_get_property_terms_from_cache($postID, 'property_area', $property_unit_cached_data);
    $property_category = wpestate_get_property_terms_from_cache($postID, 'property_category', $property_unit_cached_data);
    $property_action = wpestate_get_property_terms_from_cache($postID, 'property_action_category', $property_unit_cached_data);



    $wpestate_currency          =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );

 
    
    // content type(acc or tabs) variables
    $local_pgpr_content_type_status     =   get_post_meta($post->ID, 'local_pgpr_content_type', true);
    $global_prpg_content_type_status    =   esc_html ( wpresidence_get_option('wp_estate_global_prpg_content_type','') );
    $content_type                       =   wpestate_property_page_load_content($local_pgpr_content_type_status ,  $global_prpg_content_type_status); 

    if (function_exists('icl_translate') ){
        $where_currency             =   icl_translate('wpestate','wp_estate_where_currency_symbol', esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') ) );
    }else{
        $where_currency             =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
    }
    
   
    include ( locate_template('templates/listing_templates/layout_design_templates/property_layout_'.intval($version).'.php') ); 

}
endif;





/**
 * WpResidence Property Page Media Loader
 *
 * This file contains the wpestate_property_page_load_media function, which is responsible
 * for loading the appropriate media (sliders, gallery, etc.) for a property page.
 *
 * @package WpResidence
 * @subpackage PropertyMedia
 * @version 1.0
 * 
 * @uses get_post_meta()
 * @uses wpresidence_get_option()
 * 
 * Dependencies:
 * - WordPress core functions
 * - WpResidence theme-specific functions
 * 
 * Usage:
 * wpestate_property_page_load_media($postID, $wpestate_options, $layout_version);
 */

 if (!function_exists('wpestate_property_page_load_media')):
    function wpestate_property_page_load_media($postID, $wpestate_options, $layout_version = 1) {
        // Get media type
        $media_type = get_post_meta($postID, 'local_pgpr_slider_type', true);
        if ($media_type == 'global') {
            $media_type = esc_html(wpresidence_get_option('wp_estate_global_prpg_slider_type', ''));
        }

        // Set default sizes
        $slider_size = 'listing_full_slider';
        $main_image_masonry = 'property_listings';
        $second_image_masonry = 'blog_thumb';

        // Adjust sizes based on layout version
        if (    ($layout_version == 1 && str_contains($wpestate_options['content_class'], 'col-lg-12')) || 
                in_array($layout_version, [3, 4, 6])) {
                    $slider_size = 'listing_full_slider_1';
                    $main_image_masonry = 'listing_full_slider';
                    $second_image_masonry = 'property_listings';
        } elseif (in_array($layout_version, [2, 5])) {
            $slider_size = 'property_full_map';
            $main_image_masonry = 'listing_full_slider_1';
            $second_image_masonry = ($media_type == 'gallery') ? 'listing_full_slider' : 'property_listings';
        }
  
        // Load appropriate media type
        switch ($media_type) {
            case 'classic':
                wpestate_classic_slider($postID, $slider_size);
                break;
            case 'vertical':
                wpestate_vertical_slider($postID, $slider_size);
                break;
            case 'horizontal':
                wpestate_horizontal_slider($postID, $slider_size);
                break;
            case 'full width header':
                wpestate_listing_full_width_slider($postID, $slider_size);
                break;
            case 'multi image slider':
                wpestate_multi_image_slider($postID, $slider_size);
                break;
            case 'gallery':
                wpestate_header_masonry_gallery_type2($postID, $main_image_masonry, $second_image_masonry);
                break;
        
            case 'header masonry gallery':
                wpestate_header_masonry_gallery($postID, $main_image_masonry, $second_image_masonry);
                break;
        }
    }
endif;





/**
 * WpResidence Theme - Property Page Content Type Function
 *
 * This file contains the wpestate_property_page_load_content function, which determines
 * the content type (tabs or accordion) for property pages in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Functions
 * @since 1.0.0
 *
 * @uses No external dependencies
 *
 * Usage:
 * $content_type = wpestate_property_page_load_content($local_setting, $global_setting);
 */

 if ( ! function_exists( 'wpestate_property_page_load_content' ) ) :
    /**
     * Determine content type for property pages
     *
     * This function decides whether to use tabs or accordion for property page content
     * based on local and global settings.
     *
     * @param string $local_pgpr_content_type_status  The content type setting for the specific property.
     * @param string $global_prpg_content_type_status The global content type setting for all properties.
     * @return string The determined content type ('tabs' or 'accordion').
     */
    function wpestate_property_page_load_content( $local_pgpr_content_type_status, $global_prpg_content_type_status ) {
        // If local setting is set to 'global', use the global setting
        if ( 'global' === $local_pgpr_content_type_status ) {
            return $global_prpg_content_type_status;
        }
        
        // Otherwise, use the local setting
        return $local_pgpr_content_type_status;
    }
endif;

/** 
 * 
 */
if ( !function_exists( 'wpestate_is_search_page' ) )    {

    function wpestate_is_search_page( $pageID ) {

        if ( !$pageID )
            return false;

        $template = get_post_meta( $pageID, '_wp_page_template', true );
        if ( strpos( $template, 'search_results' ) !== false || strpos( $template, 'property_list_half' ) !== false )   {
            return true;
        }

        return false;

    }

}

?>