<?php
/* MILLDONE
* src: libs\property_page_functions\property_walkscore_functions.php
*/


/**
 * Display property WalkScore.
 *
 * This function generates the HTML for displaying a property's WalkScore.
 * It can output the content either as a tab or as an accordion item.
 *
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Optional. Whether to display as a tab. Default ''.
 * @param string $tab_active_class Optional. CSS class for active tab. Default ''.
 * @return string|void HTML output if $is_tab is 'yes', otherwise echoes the HTML.
 */
if ( ! function_exists( 'wpestate_property_walkscore_v2' ) ) :
    function wpestate_property_walkscore_v2( $postID, $is_tab = '', $tab_active_class = '' ) {
        // Check if WalkScore API key is set
        if ( empty( wpresidence_get_option( 'wp_estate_walkscore_api', '' ) ) ) {
            return '';
        }

        // Retrieve label data for WalkScore
        $data = wpestate_return_all_labels_data( 'walkscore' );

        // Prepare the label for display
        $label = wpestate_property_page_prepare_label( $data['label_theme_option'], $data['label_default'] );

        // Generate WalkScore content
        $content = wpresidence_get_walkscore_content( $postID );

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
 * Generate WalkScore content for a property.
 *
 * @param int $postID The ID of the property post.
 * @return string The HTML content for the WalkScore.
 */
if ( ! function_exists( 'wpresidence_get_walkscore_content' ) ) :
    function wpresidence_get_walkscore_content( $postID ) {
        ob_start();
        wpestate_walkscore_details( $postID );
        return ob_get_clean();
    }
endif;




/**
 * Display WalkScore details for a property.
 *
 * This function generates the HTML and JavaScript necessary to display
 * WalkScore information for a given property.
 *
 * @since 1.0.0
 *
 * @param int   $post_id                 The ID of the property post.
 * @param array $wpestate_prop_all_details Optional. An array of all property details.
 */
if ( ! function_exists( 'wpestate_walkscore_details' ) ) :
    function wpestate_walkscore_details( $post_id, $wpestate_prop_all_details = '' ) {
        $walkscore_api = esc_html( wpresidence_get_option( 'wp_estate_walkscore_api', '' ) );
        $property_location = wpresidence_walkscore_get_property_location( $post_id );

        echo '<div id="ws-walkscore-tile"></div>';

        $walkscore_data = sprintf(
            "var ws_wsid    = '%s';
            var ws_address = '%s';
            var ws_format  = 'wide';
            var ws_width   = '100%%';
            var ws_height  = '400';",
            esc_js( $walkscore_api ),
            esc_js( $property_location )
        );

        wp_enqueue_script( 'wpestate-walkscore', 'https://www.walkscore.com/tile/show-walkscore-tile.php', array(), null, true );
        wp_add_inline_script( 'wpestate-walkscore', $walkscore_data, 'before' );
    }
endif;



/**
 * Get the full location string for a property.
 *
 * @param int $post_id The ID of the property post.
 * @return string The full location string.
 */
if ( ! function_exists( 'wpresidence_walkscore_get_property_location' ) ) :
    function wpresidence_walkscore_get_property_location( $post_id ) {
        $property_city  = wpresidence_walkscore_get_taxonomy_term( $post_id, 'property_city' );
        $property_state = wpresidence_walkscore_get_taxonomy_term( $post_id, 'property_county_state' );
        $property_address = esc_html( get_post_meta( $post_id, 'property_address', true ) );
        $property_zip     = esc_html( get_post_meta( $post_id, 'property_zip', true ) );

        return implode( ',', array_filter( array( $property_address, $property_zip, $property_city, $property_state ) ) );
    }
endif;



/**
 * Get a single taxonomy term for a post.
 *
 * @param int    $post_id     The ID of the post.
 * @param string $taxonomy    The taxonomy name.
 * @return string The term name, or an empty string if not found.
 */
if ( ! function_exists( 'wpresidence_get_taxonomy_term' ) ) :
    function wpresidence_walkscore_get_taxonomy_term( $post_id, $taxonomy ) {
        $terms = get_the_terms( $post_id, $taxonomy );
        if ( $terms && ! is_wp_error( $terms ) ) {
            $term = reset( $terms );
            return $term->name;
        }
        return '';
    }
endif;