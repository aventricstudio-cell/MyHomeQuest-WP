<?php

if ( ! function_exists( 'wpestate_generate_theme_styles' ) ) :
    /**
     * Generate custom CSS styles for WPResidence theme
     *
     * This function takes an array of theme styles and generates
     * a CSS string with custom properties (CSS variables).
     *
     * @param array $theme_styles An array of theme styles grouped by type.
     * @return string A CSS string with custom properties.
     */
    function wpestate_generate_theme_styles( $theme_styles ) {
        $css_output = '';

        foreach ( $theme_styles as $type => $styles ) {
            $css_output .= "\n\t/* " . esc_html( $type ) . " variables START */\n";

            foreach ( $styles as $style ) {
                if ( ! empty( $style['value'] ) ) {
                    $css_output .= "\t" . esc_attr( $style['var'] ) . ': ' . 
                                   esc_attr( $style['value'] ) . 
                                   ( ! empty( $style['unit'] ) ? esc_attr( $style['unit'] ) : '' ) . 
                                   ";\n";
                }
            }

            $css_output .= "\t/* " . esc_html( $type ) . " variables END */\n";
        }

        return $css_output;
    }
endif;






/**
 * WPResidence Generate Options CSS
 *
 * This file contains functions for generating custom CSS based on theme options
 * for the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage Styling
 * @since 1.0.0
 */

 if ( ! function_exists( 'wpestate_generate_options_css' ) ) :
    /**
     * Generate and output custom CSS based on theme options
     */
    function wpestate_generate_options_css() {
        if(!function_exists('wpestate_request_transient_cache')){
            return;
        }
        $css_cache = wpestate_request_transient_cache( 'wpestate_custom_css' );

        if ( false === $css_cache ) {
            $theme_options_styles = wpestate_get_theme_options_styles();
            $custom_css = stripslashes( wpresidence_get_option( 'wp_estate_custom_css' ) );

            ob_start();
            ?>
            <style type='text/css'>
                :root{
                    <?php echo wpestate_generate_theme_styles( $theme_options_styles ); ?>
                }
                <?php
       
                echo wp_specialchars_decode( $custom_css );
              
            
                ?>
            </style>
            <?php
            $css_cache = ob_get_clean();
            $css_cache = wpestate_css_compress( $css_cache );
            wpestate_set_transient_cache( 'wpestate_custom_css', $css_cache, 60 * 60 * 24 );
        }

        echo trim( $css_cache );
    }
endif;


/**
 * WP Estate Theme Options Styles Function
 * 
 * This function returns an array of theme color and style variables used throughout the theme
 * Each array element contains:
 * - value: the actual value from theme options 
 * - var: the CSS variable name
 * - unit: optional unit (px, %, etc) to append to the value
 *
 * @return array Array of theme style variables and their values
 */
function wpestate_get_theme_options_styles() {
    return array(
        // Existing base colors section
        'base' => array(
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_main_color', '' ) ),
                'var'   => '--wp-estate-main-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_second_color', '' ) ),
                'var'   => '--wp-estate-second-color-option', 
            ),
        ),

        // Existing layout section  
        'layout' => array(
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_main_grid_content_width', '' ) ),
                'var'   => '--wp-estate-main-grid-content-width-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_main_content_width', '' ) ),
                'var'   => '--wp-estate-main-content-width-option',
                'unit'  => '%',
            ),
        ),

        // Existing logo section
        'logo' => array(
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_logo_max_height', '' ) ),
                'var'   => '--wp-estate-logo-max-height-option',
                'unit'  => 'px',
            ),

            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_logo_max_width', '' ) ),
                'var'   => '--wp-estate-logo-max-width-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_logo_margin', '' ) ),
                'var'   => '--wp-estate-logo-margin-option',
                'unit'  => 'px',
            ),
        ),

        // Existing header section
        'header' => array(
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_header_height', '' ) ),
                'var'   => '--wp-estate-header-height-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_sticky_header_height', '' ) ),
                'var'   => '--wp-estate-sticky-header-height-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_top_menu_font_size', '' ) ),
                'var'   => '--wp-estate-top-menu-font-size-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_menu_item_font_size', '' ) ),
                'var'   => '--wp-estate-menu-item-font-size-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_border_bottom_header', '' ) ),
                'var'   => '--wp_estate_border_bottom_header-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_sticky_border_bottom_header', '' ) ),
                'var'   => '--wp_estate_sticky_border_bottom_header-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_border_bottom_header_color', '' ) ),
                'var'   => '--wp_estate_border_bottom_header_color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_border_bottom_header_sticky_color', '' ) ),
                'var'   => '--wp_estate_border_bottom_header_sticky_color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_top_bar_back', '' ) ),
                'var'   => '--wp-estate-top-bar-back-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_top_bar_font', '' ) ),
                'var'   => '--wp-estate-top-bar-font-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_header_color', '' ) ),
                'var'   => '--wp-estate-header-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_sticky_menu_font_color', '' ) ),
                'var'   => '--wp-estate-sticky-menu-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_menu_font_color', '' ) ),
                'var'   => '--wp-estate-menu-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_top_menu_hover_font_color', '' ) ),
                'var'   => '--wp-estate-top-menu-hover-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_active_menu_font_color', '' ) ),
                'var'   => '--wp-estate-active-menu-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_top_menu_hover_back_font_color', '' ) ),
                'var'   => '--wp-estate-top-menu-hover-back-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_transparent_menu_font_color', '' ) ),
                'var'   => '--wp-estate-transparent-menu-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_transparent_menu_hover_font_color', '' ) ),
                'var'   => '--wp-estate-transparent-menu-hover-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_menu_item_back_color', '' ) ),
                'var'   => '--wp-estate-menu-item-back-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_menu_items_color', '' ) ),
                'var'   => '--wp-estate-menu-items-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_menu_hover_font_color', '' ) ),
                'var'   => '--wp-estate-menu-hover-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_menu_hover_back_color', '' ) ),
                'var'   => '--wp-estate-menu-hover-back-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_menu_border_color', '' ) ),
                'var'   => '--wp-estate-menu-border-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_header4_back_color', '' ) ),
                'var'   => '--wp-estate-header4-back-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_header4_font_color', '' ) ),
                'var'   => '--wp-estate-header4-font-color-option',
            ),

            
        ),

        // Existing mobile header section
        'mobile_header' => array(
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_mobile_header_background_color', '' ) ),
                'var'   => '--wp-estate-mobile-header-background-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_mobile_header_icon_color', '' ) ),
                'var'   => '--wp-estate-mobile-header-icon-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_mobile_menu_font_color', '' ) ),
                'var'   => '--wp-estate-mobile-menu-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_mobile_menu_hover_font_color', '' ) ),
                'var'   => '--wp-estate-mobile-menu-hover-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_mobile_item_hover_back_color', '' ) ),
                'var'   => '--wp-estate-mobile-item-hover-back-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_mobile_menu_backgound_color', '' ) ),
                'var'   => '--wp-estate-mobile-menu-background-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_mobile_menu_border_color', '' ) ),
                'var'   => '--wp-estate-mobile-menu-border-color-option',
            ),
        ),

        // New Search section
        'search' => array(
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_adv_back_color', '' ) ),
                'var'   => '--wp-estate-adv-back-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_adv_back_color_opacity', '' ) ),
                'var'   => '--wp-estate-adv-back-color-opacity-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_adv_font_color', '' ) ),
                'var'   => '--wp-estate-adv-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_adv_search_back_color', '' ) ),
                'var'   => '--wp-estate-adv-search-back-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_adv_search_font_color', '' ) ),
                'var'   => '--wp-estate-adv-search-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_adv_search_background_color', '' ) ),
                'var'   => '--wp_estate_adv_search_background_color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_adv_search_tab_font_color', '' ) ),
                'var'   => '--wp-estate-adv-search-tab-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_adv_search_tab_back_font_color', '' ) ),
                'var'   => '--wp-estate-adv-search-tab-back-font-color-option',
            ),
        ),

        // New General Colors section
        'general' => array(
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_background_color', '' ) ),
                'var'   => '--wp-estate-background-color-option', 
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_content_back_color', '' ) ),
                'var'   => '--wp_estate_content_back_color-option', 
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_content_area_back_color', '' ) ),
                'var'   => '--wp_estate_content_area_back_color-option', 
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_breadcrumbs_font_color', '' ) ),
                'var'   => '--wp_estate_breadcrumbs_font_color-option', 
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_font_color', '' ) ),
                'var'   => '--wp-estate-font-color-option', 
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_link_color', '' ) ),
                'var'   => '--wp_estate_link_color-option', 
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_headings_color', '' ) ),
                'var'   => '--wp_estate_headings_color-option', 
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_hover_button_color', '' ) ),
                'var'   => '--wp-estate-hover-button-color-option',
            ),
        ),

        // New Map Controls section
        'map' => array(
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_map_controls_back', '' ) ),
                'var'   => '--wp_estate_map_controls_back-option', 
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_map_controls_font_color', '' ) ),
                'var'   => '--wp_estate_map_controls_font_color-option', 
            ), 
        ),


        // Property Unit/Card section continued...
        'property_unit' => array(
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_property_unit_color', '' ) ),
                'var'   => '--wp-estate-property-unit-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_unit_border_color', '' ) ),
                'var'   => '--wp-estate-unit-border-color-option',
            ),

            //

            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_card_details_font_awsome_size', '' ) ),
                'var'   => '--wp-estate-card-details-font-awsome-size-option',
                'unit'  => 'px',
            ),

            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_card_details_image_icon_size', '' ) ),
                'var'   => '--wp-estate-card-details-image_icon-size-option',
                'unit'  => 'px',
            ),

            array(
                'value' => str_replace('+', ' ', wp_specialchars_decode(esc_html(wpresidence_get_option('wp_estate_card_details_font_size', 'font-family')), ENT_QUOTES)),
                'var'   => '--wp-estate-card-details-font-family-option',
            ),
            array(
                'value' => (wpresidence_get_option('wp_estate_card_details_font_size', 'font-size')),
                'var'   => '--wp-estate-card-details-font-size-option',
       
            ),
            array(
                'value' => (wpresidence_get_option('wp_estate_card_details_font_size', 'line-height')),
                'var'   => '--wp-estate-card-details-line-height-option',
            ),
            array(
                'value' => (wpresidence_get_option('wp_estate_card_details_font_size', 'font-weight')),
                'var'   => '--wp-estate-card-details-font-weight-option',
            ),
            array(
                'value' => (wpresidence_get_option('wp_estate_card_details_font_size', 'color')),
                'var'   => '--wp-estate-card-details-font-color-option',
            ),







            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_card_details_alignment', '' ) ),
                'var'   => '--wp-estate-card-details-alignment-option',
            ),

            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_card_details_image_position', '' ) ),
                'var'   => '--wp-estate-card-details-image-position-option',
            ),

            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_card_details_image_color', '' ) ),
                'var'   => '--wp-estate-card-details-image-color-option',
            ),

            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_card_details_gap', '' ) ) . 'px',
                'var'   => '--wp-estate-card-details-gap-option',
            ),

            
        ),

        // New Widget Colors section
        'widgets' => array(
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_sidebar_widget_color', '' ) ),
                'var'   => '--wp-estate-sidebar-widget-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_widget_sidebar_border_color', '' ) ),
                'var'   => '--wp-estate-widget-sidebar-border-color-option',
            ),
        ),

        // New Footer Colors section
        'footer' => array(
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_footer_back_color', '' ) ),
                'var'   => '--wp-estate-footer-back-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_footer_font_color', '' ) ),
                'var'   => '--wp-estate-footer-font-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_footer_copy_color', '' ) ),
                'var'   => '--wp-estate-footer-copy-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_footer_copy_back_color', '' ) ),
                'var'   => '--wp-estate-footer-copy-back-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_footer_heading_color', '' ) ),
                'var'   => '--wp-estate-footer-heading-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_footer_social_widget_back_color', '' ) ),
                'var'   => '--wp-estate-footer-social-widget-back-color-option',
            ),
            array(
                'value' => esc_html( wpresidence_get_option( 'wp_estate_footer_social_widget_color', '' ) ),
                'var'   => '--wp-estate-footer-social-widget-color-option',
            ),
        ),

        'content_area_padding' => array(
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_contentarea_internal_padding_top', '') ),
                'var'   => '--wp-estate-contentarea-internal-padding-top-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_contentarea_internal_padding_left', '') ),
                'var'   => '--wp-estate-contentarea-internal-padding-left-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_contentarea_internal_padding_bottom', '') ),
                'var'   => '--wp-estate-contentarea-internal-padding-bottom-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_contentarea_internal_padding_right', '') ),
                'var'   => '--wp-estate-contentarea-internal-padding-right-option',
                'unit'  => 'px',
            ),
        ),

        // New Property Unit/Card Padding section
        'property_unit_padding' => array(
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_propertyunit_internal_padding_top', '') ),
                'var'   => '--wp-estate-propertyunit-internal-padding-top-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_propertyunit_internal_padding_left', '') ),
                'var'   => '--wp-estate-propertyunit-internal-padding-left-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_propertyunit_internal_padding_bottom', '') ),
                'var'   => '--wp-estate-propertyunit-internal-padding-bottom-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_propertyunit_internal_padding_right', '') ),
                'var'   => '--wp-estate-propertyunit-internal-padding-right-option',
                'unit'  => 'px',
            ),
        ),

      
        // New Widget Padding section
        'widget_padding' => array(
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_sidebarwidget_internal_padding_top', '') ),
                'var'   => '--wp-estate-sidebarwidget-internal-padding-top-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_sidebarwidget_internal_padding_left', '') ),
                'var'   => '--wp-estate-sidebarwidget-internal-padding-left-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_sidebarwidget_internal_padding_bottom', '') ),
                'var'   => '--wp-estate-sidebarwidget-internal-padding-bottom-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_sidebarwidget_internal_padding_right', '') ),
                'var'   => '--wp-estate-sidebarwidget-internal-padding-right-option',
                'unit'  => 'px',
            ),
        ),

   


        // New Border & Unit Settings section
        'borders_and_units' => array(
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_border_radius_corner', '') ),
                'var'   => '--wp-estate-border-radius-corner-option',
                'unit'  => 'px',
            ),
   
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_unit_border_size', '') ),
                'var'   => '--wp-estate-unit-border-size-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_widget_sidebar_border_size', '') ),
                'var'   => '--wp-estate-widget-border-size-option',
                'unit'  => 'px',
            ),
        ),

        // New Min Heights section
        'min_heights' => array(
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_min_height_content_area', '') ),
                'var'   => '--wp-estate-min-height-content-area-option',
                'unit'  => 'px',
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_prop_unit_min_height', '') ),
                'var'   => '--wp-estate-prop-unit-min-height-option',
                'unit'  => 'px',
            ),
            
        ),

        // Dashboard Colors section
        'dashboard_colors' => array(
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_user_dashboard_menu_color', '') ),
                'var'   => '--wp-estate-user-dashboard-menu-color-option',
                'unit'  => '',
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_user_dashboard_menu_hover_color', '') ),
                'var'   => '--wp-estate-user-dashboard-menu-hover-color-option',
                'unit'  => '',
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_user_dashboard_menu_color_hover', '') ),
                'var'   => '--wp-estate-user-dashboard-menu-color-hover-option',
                'unit'  => '',
                //
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_user_dashboard_menu_back', '') ),
                'var'   => '--wp-estate-user-dashboard-menu-background-option',
                'unit'  => '',
                //
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_user_dashboard_package_back', '') ),
                'var'   => '--wp-estate-user-dashboard-package-background-option',
                'unit'  => '',
                //
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_user_dashboard_package_color', '') ),
                'var'   => '--wp-estate-user-dashboard-package-color-option',
                'unit'  => '',
                //
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_user_dashboard_buy_package', '') ),
                'var'   => '--wp-estate-user-dashboard-buy-package-background-option',
                'unit'  => '',
                //
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_user_dashboard_package_select', '') ),
                'var'   => '--wp-estate-user-dashboard-package-select-option',
                'unit'  => '',
                // 
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_user_dashboard_content_back', '') ),
                'var'   => '--wp-estate-user-dashboard-content-background-option',
                'unit'  => '',
                //1
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_user_dashboard_content_button_back', '') ),
                'var'   => '--wp-estate-user-dashboard-content-button-background-option',
                'unit'  => '',
                
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_user_dashboard_content_color', '') ),
                'var'   => '--wp-estate-user-dashboard-content-color-option',
                'unit'  => '',
               
            ),
        ),

     
        
        //hedings fonts
       'typography' => array(
                // H1
                array(
                    'value' => str_replace(['+', "'"], [' ', ''], wpresidence_get_option('h1_typo', 'font-family'))  ,
                    'var'   => '--wp-estate-h2-font-family-option',
                    'var'   => '--wp-estate-h1-font-family-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h1_typo', 'font-size')),
                    'var'   => '--wp-estate-h1-font-size-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h1_typo', 'line-height')),
                    'var'   => '--wp-estate-h1-line-height-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h1_typo', 'font-weight')),
                    'var'   => '--wp-estate-h1-font-weight-option',
                ),

                // H2
                array(
                    'value' => str_replace(['+', "'"], [' ', ''], wpresidence_get_option('h2_typo', 'font-family'))  ,
                    'var'   => '--wp-estate-h2-font-family-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h2_typo', 'font-size')),
                    'var'   => '--wp-estate-h2-font-size-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h2_typo', 'line-height')),
                    'var'   => '--wp-estate-h2-line-height-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h2_typo', 'font-weight')),
                    'var'   => '--wp-estate-h2-font-weight-option',
                ),

                // H3
                array(
                    'value' =>  str_replace(['+', "'"], [' ', ''], wpresidence_get_option('h3_typo', 'font-family'))  ,
                    'var'   => '--wp-estate-h2-font-family-option',
                    'var'   => '--wp-estate-h3-font-family-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h3_typo', 'font-size')),
                    'var'   => '--wp-estate-h3-font-size-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h3_typo', 'line-height')),
                    'var'   => '--wp-estate-h3-line-height-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h3_typo', 'font-weight')),
                    'var'   => '--wp-estate-h3-font-weight-option',
                ),

                // H4
                array(
                    'value' =>  str_replace(['+', "'"], [' ', ''], wpresidence_get_option('h4_typo', 'font-family'))  ,
                    'var'   => '--wp-estate-h4-font-family-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h4_typo', 'font-size')),
                    'var'   => '--wp-estate-h4-font-size-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h4_typo', 'line-height')),
                    'var'   => '--wp-estate-h4-line-height-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h4_typo', 'font-weight')),
                    'var'   => '--wp-estate-h4-font-weight-option',
                ),

                // H5
                array(
                    'value' =>  str_replace(['+', "'"], [' ', ''], wpresidence_get_option('h5_typo', 'font-family'))  ,
                    'var'   => '--wp-estate-h5-font-family-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h5_typo', 'font-size')),
                    'var'   => '--wp-estate-h5-font-size-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h5_typo', 'line-height')),
                    'var'   => '--wp-estate-h5-line-height-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h5_typo', 'font-weight')),
                    'var'   => '--wp-estate-h5-font-weight-option',
                ),

                // H6
                array(
                    'value' =>  str_replace(['+', "'"], [' ', ''], wpresidence_get_option('h6_typo', 'font-family'))  ,
                    'var'   => '--wp-estate-h6-font-family-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h6_typo', 'font-size')),
                    'var'   => '--wp-estate-h6-font-size-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h6_typo', 'line-height')),
                    'var'   => '--wp-estate-h6-line-height-option',
                ),
                array(
                    'value' => (wpresidence_get_option('h6_typo', 'font-weight')),
                    'var'   => '--wp-estate-h6-font-weight-option',
                ),

                // Paragraph
                array(

                    'value' =>  str_replace(['+', "'"], [' ', ''], wpresidence_get_option('paragraph_typo', 'font-family'))  ,
                    'var'   => '--wp-estate-paragraph-font-family-option',
                ),
                array(
                    'value' => (wpresidence_get_option('paragraph_typo', 'font-size')),
                    'var'   => '--wp-estate-paragraph-font-size-option',
                ),
                array(
                    'value' => (wpresidence_get_option('paragraph_typo', 'line-height')),
                    'var'   => '--wp-estate-paragraph-line-height-option',
                ),
                array(
                    'value' => (wpresidence_get_option('paragraph_typo', 'font-weight')),
                    'var'   => '--wp-estate-paragraph-font-weight-option',
                ),

                // Menu
                array(
                    'value' =>  str_replace(['+', "'"], [' ', ''], wpresidence_get_option('menu_typo', 'font-family'))  ,
                    'var'   => '--wp-estate-menu-font-family-option',
                ),
                array(
                    'value' => (wpresidence_get_option('menu_typo', 'font-size')),
                    'var'   => '--wp-estate-menu-font-size-option',
                ),
                array(
                    'value' => (wpresidence_get_option('menu_typo', 'line-height')),
                    'var'   => '--wp-estate-menu-line-height-option',
                ),
                array(
                    'value' => (wpresidence_get_option('menu_typo', 'font-weight')),
                    'var'   => '--wp-estate-menu-font-weight-option',
                ),
            ),







        'others'=>array(
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_cssbox_shadow', '') ),
                'var'   => '--wp-estate-cssbox-shadow-option',
                'unit'  => '',
                
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_float_form_top', '') ),
                'var'   => '--wp-estate-float-form-top-option',
                'unit'  => '',
                
            ),
            array(
                'value' => esc_html( wpresidence_get_option('wp_estate_float_form_top_tax', '') ),
                'var'   => '--wp-estate-float-form-top-tax-option',
                'unit'  => '',
                
            ),
     
        ),   




        );
    }
    














add_action( 'wp_head', 'wpestate_generate_options_css' );




/**
 * WPResidence Compression Functions
 *
 * This file contains functions for compressing CSS and HTML output
 * in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage Optimization
 * @since 1.0.0
 */

if ( ! function_exists( 'wpestate_css_compress' ) ) :
    /**
     * Compress CSS by removing whitespace
     *
     * @param string $buffer The CSS content to compress
     * @return string Compressed CSS content
     */
    function wpestate_css_compress( $buffer ) {
        $search = array(
            "/\r\n/",
            "/\r/",
            "/\n/",
            "/\t/",
            '/  /',
            '/    /',
            '/    /'
        );
        $replace = array('', '', '', '', '', '', '');
        return preg_replace($search, $replace, $buffer);
    }
endif;

if ( ! function_exists( 'wpestate_html_compress' ) ) :
    /**
     * Compress HTML by removing whitespace and comments
     *
     * @param string $buffer The HTML content to compress
     * @return string Compressed HTML content
     */
    function wpestate_html_compress( $buffer ) {
        $search = array(
            '/\>[^\S ]+/s',     // Strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // Strip whitespaces before tags, except space
            '/(\s)+/s',         // Shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        );
        $replace = array('>', '<', '\\1', '');
        return preg_replace($search, $replace, $buffer);
    }
endif;
