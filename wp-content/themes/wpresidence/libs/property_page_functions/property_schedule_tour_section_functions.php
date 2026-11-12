<?php
/* MILLDONE
*  src: libs\property_page_functions\property_schedule_tour_section_functions.php
*/
/**
 * Property Schedule Tour V2
 *
 * This function generates the content for displaying a property tour scheduling option,
 * either as a tab or as an accordion item in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Whether to display as a tab ('yes') or accordion (default '').
 * @param string $tab_active_class The CSS class for active tabs (optional).
 *
 * @return array|void Returns an array with tab content if it's a tab, otherwise echoes the content.
 */

if ( ! function_exists( 'wpestate_property_schedule_tour_v2' ) ) :
    function wpestate_property_schedule_tour_v2( $postID, $is_tab = '', $tab_active_class = '' ) {
        // Retrieve schedule tour data and labels
        $data  = wpestate_return_all_labels_data( 'schedule_tour' );
        $label = wpestate_property_page_prepare_label( $data['label_theme_option'], $data['label_default'] );
 
        $propertyID = $postID;
        // Generate content by including the tour schedule template
        ob_start();
        include( locate_template( '/templates/listing_templates/schedule_tour/property_page_schedule_tour_layout_v2.php' ) );
        $content = ob_get_clean();

        if(trim($content) == ''){
            return;
        }
        // Display content based on the $is_tab parameter
        if ( 'yes' === $is_tab ) {
            // Return content as a tab item
            return wpestate_property_page_create_tab_item( $content, $label, $data['tab_id'], $tab_active_class );
        } else {
            // Echo content as an accordion item
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