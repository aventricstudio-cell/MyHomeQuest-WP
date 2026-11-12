<?php 
/*MILLDONE
* src: libs\property_page_functions\property_neary_by_yelp_functions.php
*/

/**
 * Display nearby amenities for a property.
 *
 * This function generates the HTML for displaying nearby amenities of a property.
 * It can output the content either as a tab or as an accordion item.
 *
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Optional. Whether to display as a tab. Default ''.
 * @param string $tab_active_class Optional. CSS class for active tab. Default ''.
 * @return string|void HTML output if $is_tab is 'yes', otherwise echoes the HTML.
 */
if ( ! function_exists( 'wpestate_property_nearby_v2' ) ) :
    function wpestate_property_nearby_v2( $postID, $is_tab = '', $tab_active_class = '' ) {
        // Retrieve label data for nearby amenities
        $data = wpestate_return_all_labels_data( 'nearby' );

        // Prepare the label for display
        $label = wpestate_property_page_prepare_label( $data['label_theme_option'], $data['label_default'] );

        // Generate nearby amenities content
        $content = wpresidence_get_nearby_content( $postID );

        // Determine whether to display as a tab or accordion
        if ( $is_tab === 'yes' ) {
            // Return the content as a tab item
            return wpestate_property_page_create_tab_item( $content, $label, $data['tab_id'], $tab_active_class );
        } else {
            // Echo the content as an accordion item
            echo wp_kses_post(
                wpestate_property_page_create_acc(
                    $content,
                    $label,
                    $data['accordion_id'],
                    $data['accordion_id'] . '_collapse'
                )
            );
        }
    }
endif;

/**
 * Generate nearby amenities content for a property.
 *
 * @param int $postID The ID of the property post.
 * @return string The HTML content for the nearby amenities.
 */
if ( ! function_exists( 'wpresidence_get_nearby_content' ) ) :
    function wpresidence_get_nearby_content( $postID ) {

        ob_start();
        wpestate_yelp_details( $postID );
        $content = ob_get_clean();
        $logo = get_theme_file_uri('/img/yelp_logo.webp');
        $content.=  '<img id="yelp_logo_image" src="' . esc_url($logo) . '" class="img-responsive retina_ready" alt="' . esc_html__('yelp logo', 'wpresidence') . '"/>';    
        return $content;

    }
endif;




/**
 * Display Yelp details for a property.
 *
 * This function retrieves and displays Yelp data for various categories near a property.
 * It caches the results to improve performance on subsequent calls.
 *
 * @param int $post_id The ID of the property post.
 */
if ( ! function_exists( 'wpestate_yelp_details' ) ) :
    function wpestate_yelp_details( $post_id ) {
        $yelp_terms_array = wpresidence_get_yelp_terms_array();
        $yelp_to_display = get_transient( 'wpestate_yelp_' . $post_id );
     //   $yelp_to_display =false;
        if ( false === $yelp_to_display ) {
            $yelp_to_display = wpresidence_generate_yelp_content( $post_id, $yelp_terms_array );
            set_transient( 'wpestate_yelp_' . $post_id, $yelp_to_display, DAY_IN_SECONDS );
        }

        echo ( trim( $yelp_to_display ) );
    }
endif;

/**
 * Generate Yelp content for a property.
 *
 * @param int   $post_id          The ID of the property post.
 * @param array $yelp_terms_array The array of Yelp terms and their details.
 * @return string The generated Yelp content.
 */
if ( ! function_exists( 'wpresidence_generate_yelp_content' ) ) :
    function wpresidence_generate_yelp_content( $post_id, $yelp_terms_array ) {
        $yelp_terms = wpresidence_get_option( 'wp_estate_yelp_categories', '' );
        $yelp_results_no = wpresidence_get_option( 'wp_estate_yelp_results_no', '' );
        $yelp_dist_measure = wpresidence_get_option( 'wp_estate_yelp_dist_measure', '' );

        if ( ! wpresidence_validate_yelp_api_key() ) {
            return '';
        }

        $location = wpresidence_get_property_location( $post_id );
        $start_coords = wpresidence_get_property_coordinates( $post_id );

        $yelp_content = '';

        foreach ( $yelp_terms as $term ) {
            $category_name = $yelp_terms_array[$term]['category'];
            $category_icon = $yelp_terms_array[$term]['category_sign'];

            $details = wpestate_query_api( $term, $location );

            if ( isset( $details->error ) ) {
                return esc_html__( 'There are no results found for this property address.', 'wpresidence' );
            } elseif ( isset( $details->businesses ) ) {
                $yelp_content .= wpresidence_generate_yelp_category_content( $details->businesses, $category_name, $category_icon, $start_coords, $yelp_dist_measure );
            }
        }

        return $yelp_content;
    }
endif;

/**
 * Generate Yelp content for a single category.
 *
 * @param array  $businesses      The array of businesses in the category.
 * @param string $category_name   The name of the category.
 * @param string $category_icon   The icon class for the category.
 * @param array  $start_coords    The starting coordinates [lat, long].
 * @param string $yelp_dist_measure The distance measure to use.
 * @return string The generated content for the category.
 */
function wpresidence_generate_yelp_category_content( $businesses, $category_name, $category_icon, $start_coords, $yelp_dist_measure ) {
    $content = '<div class="yelp_bussines_wrapper">' .
                    '<div class="wpresidence_yelp_title">'.
                        '<div class="yelp_icon">'.
                            '<i class="' . esc_attr($category_icon) . '"></i>' .
                        '</div>'.
                        '<h4 class="yelp_category">' . esc_html($category_name) . '</h4>' .
                    '</div>'; // Closing the title wrapper div

            foreach ( $businesses as $unit ) {
                $content .= wpresidence_generate_yelp_unit_content( $unit, $start_coords, $yelp_dist_measure );
            }

    $content .= '</div>'; // Closing the main wrapper div

    return $content;
}


/**
 * Generate Yelp content for a single business unit.
 *
 * @param object $unit              The business unit object.
 * @param array  $start_coords      The starting coordinates [lat, long].
 * @param string $yelp_dist_measure The distance measure to use.
 * @return string The generated content for the business unit.
 */
if ( ! function_exists( 'wpresidence_generate_yelp_unit_content' ) ) :
    function wpresidence_generate_yelp_unit_content( $unit, $start_coords, $yelp_dist_measure ) {
        $content = sprintf(
            '<div class="yelp_unit"><h5 class="yelp_unit_name">%s</h5>',
            esc_html( $unit->name )
        );

        if ( isset( $unit->coordinates->latitude ) && isset( $unit->coordinates->longitude ) ) {
            $content .= sprintf(
                '<span class="yelp_unit_distance">%s</span>',
                wpestate_calculate_distance_geo( $unit->coordinates->latitude, $unit->coordinates->longitude, $start_coords[0], $start_coords[1], $yelp_dist_measure )
            );
        }

        $image_path = str_replace( '.5', '_half', (string) $unit->rating );
        $content .= sprintf(
            '<img class="yelp_stars" src="%s" alt="%s">',
            esc_url( get_theme_file_uri( "/img/yelp_small/small_{$image_path}.png" ) ),
            esc_attr( $unit->name )
        );

        $content .= '</div>';

        return $content;
    }
endif;




/**
 * Get the complete Yelp terms array.
 *
 * This function returns an array of Yelp categories with their corresponding
 * display names and Font Awesome icon classes.
 *
 * @return array The Yelp terms array.
 */
if ( ! function_exists( 'wpresidence_get_yelp_terms_array' ) ) :
    function wpresidence_get_yelp_terms_array() {
        return array(
            'active' => array(
                'category' => esc_html__('Active Life', 'wpresidence'),
                'category_sign' => 'fa fa-bicycle'
            ),
            'arts' => array(
                'category' => esc_html__('Arts & Entertainment', 'wpresidence'),
                'category_sign' => 'fa fa-music'
            ),
            'auto' => array(
                'category' => esc_html__('Automotive', 'wpresidence'),
                'category_sign' => 'fa fa-car'
            ),
            'beautysvc' => array(
                'category' => esc_html__('Beauty & Spas', 'wpresidence'),
                'category_sign' => 'fa fa-female'
            ),
            'education' => array(
                'category' => esc_html__('Education', 'wpresidence'),
                'category_sign' => 'fa fa-graduation-cap'
            ),
            'eventservices' => array(
                'category' => esc_html__('Event Planning & Services', 'wpresidence'),
                'category_sign' => 'fa fa-birthday-cake'
            ),
            'financialservices' => array(
                'category' => esc_html__('Financial Services', 'wpresidence'),
                'category_sign' => 'fa fa-money'
            ),
            'food' => array(
                'category' => esc_html__('Food', 'wpresidence'),
                'category_sign' => 'fa fa-cutlery'
            ),
            'health' => array(
                'category' => esc_html__('Health & Medical', 'wpresidence'),
                'category_sign' => 'fa fa-medkit'
            ),
            'homeservices' => array(
                'category' => esc_html__('Home Services ', 'wpresidence'),
                'category_sign' => 'fa fa-wrench'
            ),
            'hotelstravel' => array(
                'category' => esc_html__('Hotels & Travel', 'wpresidence'),
                'category_sign' => 'fa fa-bed'
            ),
            'localflavor' => array(
                'category' => esc_html__('Local Flavor', 'wpresidence'),
                'category_sign' => 'fa fa-coffee'
            ),
            'localservices' => array(
                'category' => esc_html__('Local Services', 'wpresidence'),
                'category_sign' => 'fa fa-dot-circle-o'
            ),
            'massmedia' => array(
                'category' => esc_html__('Mass Media', 'wpresidence'),
                'category_sign' => 'fa fa-television'
            ),
            'nightlife' => array(
                'category' => esc_html__('Nightlife', 'wpresidence'),
                'category_sign' => 'fa fa-glass'
            ),
            'pets' => array(
                'category' => esc_html__('Pets', 'wpresidence'),
                'category_sign' => 'fa fa-paw'
            ),
            'professional' => array(
                'category' => esc_html__('Professional Services', 'wpresidence'),
                'category_sign' => 'fa fa-suitcase'
            ),
            'publicservicesgovt' => array(
                'category' => esc_html__('Public Services & Government', 'wpresidence'),
                'category_sign' => 'fa fa-university'
            ),
            'realestate' => array(
                'category' => esc_html__('Real Estate', 'wpresidence'),
                'category_sign' => 'fa fa-building-o'
            ),
            'religiousorgs' => array(
                'category' => esc_html__('Religious Organizations', 'wpresidence'),
                'category_sign' => 'fa fa-cloud'
            ),
            'restaurants' => array(
                'category' => esc_html__('Restaurants', 'wpresidence'),
                'category_sign' => 'fa fa-cutlery'
            ),
            'shopping' => array(
                'category' => esc_html__('Shopping', 'wpresidence'),
                'category_sign' => 'fa fa-shopping-bag'
            ),
            'transport' => array(
                'category' => esc_html__('Transportation', 'wpresidence'),
                'category_sign' => 'fa fa-bus'
            )
        );
    }
endif;




/**
 * Validate Yelp API key.
 *
 * @return bool True if API key is valid, false otherwise.
 */
if ( ! function_exists( 'wpresidence_validate_yelp_api_key' ) ) :
    function wpresidence_validate_yelp_api_key() {
        $yelp_client_id = wpresidence_get_option( 'wp_estate_yelp_client_id', '' );
        $yelp_client_api_key_2018 = wpresidence_get_option( 'wp_estate_yelp_client_api_key_2018', '' );
        return ! empty( $yelp_client_id ) && ! empty( $yelp_client_api_key_2018 );
    }
endif;

/**
 * Get property location.
 *
 * @param int $post_id The ID of the property post.
 * @return string The property location.
 */
if ( ! function_exists( 'wpresidence_get_property_location' ) ) :
    function wpresidence_get_property_location( $post_id ) {
        $property_address = esc_html( get_post_meta( $post_id, 'property_address', true ) );
        $property_city_array = get_the_terms( $post_id, 'property_city' );

        if ( empty( $property_city_array ) ) {
            return '';
        }

        $property_city = $property_city_array[0]->name;
        return $property_address . ',' . $property_city;
    }
endif;

/**
 * Get property coordinates.
 *
 * @param int $post_id The ID of the property post.
 * @return array The property coordinates [lat, long].
 */
if ( ! function_exists( 'wpresidence_get_property_coordinates' ) ) :
    function wpresidence_get_property_coordinates( $post_id ) {
        return array(
            get_post_meta( $post_id, 'property_latitude', true ),
            get_post_meta( $post_id, 'property_longitude', true )
        );
    }
endif;