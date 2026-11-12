<?php
/* MILLDONE
*  src: libs\property_page_functions\property_mortgage_calculator_functions.php
*/

/** 
 * Property Page Views Function
 * This function generates the content for displaying property page views,
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

if ( ! function_exists( 'wpestate_property_page_views_v2' ) ) :
    function wpestate_property_page_views_v2( $postID, $is_tab = '', $tab_active_class = '' ) {
        // Retrieve page views data and labels
        $data  = wpestate_return_all_labels_data( 'page_views' );
        $label = wpestate_property_page_prepare_label( $data['label_theme_option'], $data['label_default'] );

        // Prepare the content with a canvas for the chart
        $content = '<canvas id="myChart"></canvas>';

        // JavaScript to initialize the chart
        $chart_init_script = "<script type=\"text/javascript\">
            //<![CDATA[
            jQuery(document).ready(function(){
                wpestate_show_stat_accordion();
            });
            //]]>
        </script>";

        if ( 'yes' === $is_tab ) {
            // Generate content for tab display
            $to_return = wpestate_property_page_create_tab_item( $content, $label, $data['tab_id'], $tab_active_class );
            $to_return['tab_panel'] .= $chart_init_script;
            return $to_return;
        } else {
            // Generate content for accordion display
            $accordion_content = wpestate_property_page_create_acc( $content, $label, $data['accordion_id'], $data['accordion_id'] . '_collapse' );
            
            // Output the accordion content and chart initialization script
            echo ( $accordion_content . $chart_init_script );
        }
    }
endif;