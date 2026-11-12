<?php
/* MILLDONE
* src: libs\property_page_functions\property_booking_shortcode_functions.php
*/

if ( ! function_exists( 'wpestate_property_booking_shortcode_v2' ) ) :
    /**
     * Display property booking shortcode content.
     *
     * Outputs the content stored in the property_booking_shortcode post meta, rendering
     * any embedded shortcode. The section can render as either a tab item or an accordion
     * panel depending on the context in which it is called.
     *
     * @param int    $postID           The ID of the current property.
     * @param string $is_tab           Optional. Whether the section is displayed as a tab. Default ''.
     * @param string $tab_active_class Optional. CSS class for marking the tab as active. Default ''.
     *
     * @return array|string|void Returns tab data when used in tabs; otherwise echoes the accordion HTML.
     */
    function wpestate_property_booking_shortcode_v2( $postID, $is_tab = '', $tab_active_class = '' ) {
        $raw_content = get_post_meta( $postID, 'property_booking_shortcode', true );

        if ( empty( $raw_content ) ) {
            return;
        }

        $content = do_shortcode( $raw_content );

        if ( trim( $content ) === '' ) {
            return;
        }

        $data  = wpestate_return_all_labels_data( 'booking_shortcode' );
        $label = wpestate_property_page_prepare_label( $data['label_theme_option'], $data['label_default'] );

        if ( 'yes' === $is_tab ) {
            return wpestate_property_page_create_tab_item( $content, $label, $data['tab_id'], $tab_active_class );
        }

        echo wpestate_property_page_create_acc(
            $content,
            $label,
            $data['accordion_id'],
            $data['accordion_id'] . '_collapse'
        );
    }
endif;
