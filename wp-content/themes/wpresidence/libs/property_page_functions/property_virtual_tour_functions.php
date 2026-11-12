<?php
/* MILLDONE
* src: libs\property_page_functions\property_virtual_tour_functions.php
*/

/**
 * Display property virtual tour.
 *
 * This function generates the HTML for displaying a property virtual tour.
 * It can output the content either as a tab or as an accordion item.
 *
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Optional. Whether to display as a tab. Default ''.
 * @param string $tab_active_class Optional. CSS class for active tab. Default ''.
 * @return string|void HTML output if $is_tab is 'yes', otherwise echoes the HTML.
 */
if ( ! function_exists( 'wpestate_property_virtual_tour_v2' ) ) :
    function wpestate_property_virtual_tour_v2( $postID, $is_tab = '', $tab_active_class = '' ) {
        // Retrieve virtual tour content
        $content = get_post_meta( $postID, 'embed_virtual_tour', true );

        // If no virtual tour content, return early
        if ( empty( $content ) ) {
            return;
        }
        // Render shortcodes in the content
        $content = do_shortcode( $content );
        // Retrieve label data for virtual tour
        $data = wpestate_return_all_labels_data( 'virtual_tour' );

        // Prepare the label for display
        $label = wpestate_property_page_prepare_label( $data['label_theme_option'], $data['label_default'] );

        // Determine whether to display as a tab or accordion
        if ( $is_tab === 'yes' ) {
            // Return the content as a tab item
            return wpestate_property_page_create_tab_item( $content, $label, $data['tab_id'], $tab_active_class );
        } else {
            // Echo the content as an accordion item
            echo (
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


