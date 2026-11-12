<?php
/** MILLDONE
 *  src: libs\property_page_functions\property_details_section_functions.php
 */

/**
 * Generate the property listing details section.
 *
 * This function creates either a tab item or an accordion section
 * for the property listing details, based on the provided parameters.
 *
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Whether the listing details section is part of a tab layout ('yes' or '').
 * @param string $tab_active_class The CSS class for active tabs (if applicable).
 * @return string|void HTML content of the listing details section or void if not a tab.
 */
if (!function_exists('wpestate_property_listing_details_v2')) :
    function wpestate_property_listing_details_v2($postID, $is_tab = '', $tab_active_class = '') {
        // Retrieve label data for the listing details section
        $data = wpestate_return_all_labels_data('listing_details');
        $label = wpestate_property_page_prepare_label($data['label_theme_option'], $data['label_default']);

        // Get the listing details content
        $content = estate_listing_details($postID);

        // Determine whether to return a tab item or print an accordion section
        if ($is_tab === 'yes') {
            return wpestate_property_page_create_tab_item($content, $label, $data['tab_id'], $tab_active_class);
        } else {
            $accordion_content = wpestate_property_page_create_acc(
                $content,
                $label,
                $data['accordion_id'],
                $data['accordion_id'] . '_collapse'
            );
            
            // Use echo for output, which is preferable in WordPress
            echo $accordion_content;
        }
    }
endif;


/**
 * Generate property listing details HTML.
 *
 * This function creates a formatted string of property details including price,
 * size, number of rooms, and custom fields. It's designed to be used within
 * the WpResidence theme for displaying property information.
 *
 * @param int   $post_id                  The ID of the property post.
 * @param array $wpestate_prop_all_details Optional. An array of all property details.
 * @param int   $columns                  Optional. The number of columns for the layout.
 * @return string The HTML markup for the property listing details.
 */
if ( ! function_exists( 'estate_listing_details' ) ) :
    function estate_listing_details( $post_id, $wpestate_prop_all_details = '', $columns = '' ) {
        // Retrieve theme options and settings
        $wpestate_currency  = esc_html( wpresidence_get_option( 'wp_estate_currency_symbol', '' ) );
        $where_currency     = esc_html( wpresidence_get_option( 'wp_estate_where_currency_symbol', '' ) );
        $measure_sys        = esc_html( wpresidence_get_option( 'wp_estate_measure_sys', '' ) );

        // Convert and format property size measurements
        $property_size      = wpestate_get_converted_measure( $post_id, 'property_size', $wpestate_prop_all_details );
        $property_lot_size  = wpestate_get_converted_measure( $post_id, 'property_lot_size', $wpestate_prop_all_details );

        // Determine column layout
        $colmd = wpestat_get_content_comuns( $columns, 'details' );

        // Retrieve property details
        $property_details = empty( $wpestate_prop_all_details ) 
            ? array(
                'property_rooms'     => get_post_meta( $post_id, 'property_rooms', true ),
                'property_bedrooms'  => get_post_meta( $post_id, 'property_bedrooms', true ),
                'property_bathrooms' => get_post_meta( $post_id, 'property_bathrooms', true ),
                'property_price'     => get_post_meta( $post_id, 'property_price', true ),
                'property_second_price' => get_post_meta( $post_id, 'property_second_price', true ),
                'energy_index'       => get_post_meta( $post_id, 'energy_index', true ),
                'energy_class'       => get_post_meta( $post_id, 'energy_class', true ),
                'property_internal_id' => get_post_meta( $post_id, 'property_internal_id', true )
            )
            : array(
                'property_rooms'     => wpestate_return_custom_field( $wpestate_prop_all_details, 'property_rooms' ),
                'property_bedrooms'  => wpestate_return_custom_field( $wpestate_prop_all_details, 'property_bedrooms' ),
                'property_bathrooms' => wpestate_return_custom_field( $wpestate_prop_all_details, 'property_bathrooms' ),
                'property_price'     => wpestate_return_custom_field( $wpestate_prop_all_details, 'property_price' ),
                'property_second_price' => wpestate_return_custom_field( $wpestate_prop_all_details, 'property_second_price' ),
                'energy_index'       => wpestate_return_custom_field( $wpestate_prop_all_details, 'energy_index' ),
                'energy_class'       => wpestate_return_custom_field( $wpestate_prop_all_details, 'energy_class' ),
                'property_internal_id' => wpestate_return_custom_field( $wpestate_prop_all_details, 'property_internal_id' )
            );

        $property_internal_id = !empty($property_details['property_internal_id']) ? $property_details['property_internal_id'] : $post_id;


        $property_id_label =     esc_html__( 'Property Id', 'wpresidence' ) ;
        if(!empty($property_details['property_internal_id'])){
            $property_id_label =     esc_html__( 'Listing Id', 'wpresidence' ) ;
        }



        // Convert property details to float values
        $property_details = array_map( 'floatval', $property_details );

        // Format prices
        $price = ( $property_details['property_price'] != 0 ) 
            ? wpestate_show_price_from_all_details( $post_id, $wpestate_currency, $where_currency, 1, $wpestate_prop_all_details )
            : '';

        $property_second_price = ( $property_details['property_second_price'] != 0 ) 
            ? wpestate_show_price_from_all_details( $post_id, $wpestate_currency, $where_currency, 1, $wpestate_prop_all_details, "yes" )
            : '';

        // Start building the HTML output
        $output = '<div class="row">';
        
        // Property ID
        $output .= wpresidence_generate_detail_row( $colmd, 'property_internal_id',$property_id_label, $property_internal_id );

        // Price
        if ( ! empty( $price ) ) {
            $output .= wpresidence_generate_detail_row( $colmd, 'property_default_price', esc_html__( 'Price', 'wpresidence' ), $price );
        }
        
        // Second Price
        if ( ! empty( $property_second_price ) ) {
            $output .= wpresidence_generate_detail_row( $colmd, 'property_default_second_price', esc_html__( 'Price Info', 'wpresidence' ), $property_second_price );
        }
        
        // Property Size
        if ( ! empty( $property_size ) ) {
            $output .= wpresidence_generate_detail_row( $colmd, 'property_default_property_size', esc_html__( 'Property Size', 'wpresidence' ), $property_size );
        }
        
        // Property Lot Size
        if ( ! empty( $property_lot_size ) ) {
            $output .= wpresidence_generate_detail_row( $colmd, 'property_default_lot_size', esc_html__( 'Property Lot Size', 'wpresidence' ), $property_lot_size );
        }
        
        // Rooms
        if ( ! empty( $property_details['property_rooms'] ) ) {
            $output .= wpresidence_generate_detail_row( $colmd, 'property_default_rooms', esc_html__( 'Rooms', 'wpresidence' ), $property_details['property_rooms'] );
        }
        
        // Bedrooms
        if ( ! empty( $property_details['property_bedrooms'] ) ) {
            $output .= wpresidence_generate_detail_row( $colmd, 'property_default_bedrooms', esc_html__( 'Bedrooms', 'wpresidence' ), $property_details['property_bedrooms'] );
        }
        
        // Bathrooms
        if ( ! empty( $property_details['property_bathrooms'] ) ) {
            $output .= wpresidence_generate_detail_row( $colmd, 'property_default_bathrooms', esc_html__( 'Bathrooms', 'wpresidence' ), $property_details['property_bathrooms'] );
        }

        // Custom Fields
        $output .= wpresidence_generate_custom_fields( $post_id, $colmd );

        
        // Start building the HTML output
        $output .= '</div>';
        return $output;
    }
endif;

/**
 * Generate a single detail row for the property listing.
 *
 * @param int    $colmd  The number of columns.
 * @param string $class  The CSS class for the detail.
 * @param string $label  The label for the detail.
 * @param mixed  $value  The value of the detail.
 * @return string The HTML for the detail row.
 */
if ( ! function_exists( 'wpresidence_generate_detail_row' ) ) :
    function wpresidence_generate_detail_row( $colmd, $class, $label, $value ) {
        return sprintf(
            '<div class="listing_detail col-md-%d %s"><strong>%s:</strong> %s</div>',
            esc_attr( $colmd ),
            wp_kses_post( $class ),
            wp_kses_post( $label ),
            wp_kses_post( $value )
        );
    }
endif;

/**
 * Generate custom fields for the property listing.
 *
 * @param int $post_id The ID of the property post.
 * @param int $colmd   The number of columns.
 * @return string The HTML for the custom fields.
 */
if ( ! function_exists( 'wpresidence_generate_custom_fields' ) ) :
    function wpresidence_generate_custom_fields( $post_id, $colmd ) {
        $output = '';
        $custom_fields = wpresidence_get_option( 'wp_estate_custom_fields', '' );
        
        if ( ! empty( $custom_fields ) ) {
            foreach ( $custom_fields as $field ) {
                if ( ! empty( $field[0] ) ) {
                    $name   = $field[0];
                    $label  = stripslashes( $field[1] );
                    $slug   = sanitize_key( wpestate_limit45( sanitize_title( $name ) ) );
                    $value  = get_post_meta( $post_id, $slug, true );

                    // Apply WPML translations if available
                    if ( function_exists( 'icl_translate' ) ) {
                        $label = icl_translate( 'wpestate', 'wp_estate_property_custom_' . $label, $label );
                        $value = icl_translate( 'wpestate', 'wp_estate_property_custom_' . $value, $value );
                    }

                    if ( ! empty( $value ) && $value !== esc_html__( 'Not Available', 'wpresidence' ) ) {
                        $output .= wpresidence_generate_detail_row( $colmd, $slug, trim( $label ), $value );
                    }
                }
            }
        }

        return $output;
    }
endif;