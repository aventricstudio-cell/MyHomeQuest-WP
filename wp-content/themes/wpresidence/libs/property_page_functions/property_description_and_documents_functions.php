<?php  
/* MILLDONE
*  src: libs\property_page_functions\property_description_and_documents_functions.php
*/
/**
 * Property Description
 *
 * This function generates the content for displaying a property description,
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

if ( ! function_exists( 'wpestate_property_description_v2' ) ) :
    function wpestate_property_description_v2( $postID, $is_tab = '', $tab_active_class = '' ) {
        // Retrieve description data and labels
        $data  = wpestate_return_all_labels_data( 'description' );
        $label = wpestate_property_page_prepare_label( $data['label_theme_option'], $data['label_default'] );

        // Get and process the content
        $content = get_the_content( null, false, $postID );
        $content = apply_filters( 'the_content', $content );
        $content = str_replace( ']]>', ']]&gt;', $content );

        if ( 'yes' === $is_tab ) {
            // Return content as a tab item
            return wpestate_property_page_create_tab_item( $content, $label, $data['tab_id'], $tab_active_class );
        } else {
            // Output content as an accordion item
            ?>
            <div class="wpestate_property_description property-panel" id="<?php echo esc_attr( $data['accordion_id'] ); ?>">
                <h4 class="panel-title"><?php echo esc_html( $label ); ?></h4>
                <div class="panel-body">
                    <?php
                    $allowed = wp_kses_allowed_html( 'post' );
                    $allowed['iframe'] = [
                        'src'             => true,
                        'width'           => true,
                        'height'          => true,
                        'frameborder'     => true,
                        'allowfullscreen' => true,
                    ];
                    echo $content = wp_kses( $content, $allowed )

                    ?>
                </div>
            </div>
            <?php
        }
    }
endif;


/**
 * Generate the property documents section.
 *
 * This function creates either a tab item or an accordion section
 * for the property documents, based on the provided parameters.
 *
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Whether the documents section is part of a tab layout ('yes' or '').
 * @param string $tab_active_class The CSS class for active tabs (if applicable).
 * @return string|void HTML content of the documents section or void if not a tab.
 */
if (!function_exists('wpestate_property_documents_v2')) :
    function wpestate_property_documents_v2($postID, $is_tab = '', $tab_active_class = '') {
        // Retrieve label data for the documents section
        $data = wpestate_return_all_labels_data('documents');
        $label = wpestate_property_page_prepare_label($data['label_theme_option'], $data['label_default']);

        // Get the documents content
        $content = wpestare_return_documents($postID);

        if ($is_tab === 'yes') {
            // Return tab item if it's part of a tab layout
            return wpestate_property_page_create_tab_item($content, $label, $data['tab_id'], $tab_active_class);
        } else {
            // Output accordion section for documents
            echo wpestate_property_page_create_acc(
                $content,
                $label,
                $data['accordion_id'],
                $data['accordion_id'] . '_collapse'
            );
        }
    }
endif;



/**
 * Retrieve and format property documents.
 *
 * This function fetches PDF attachments for a given property and formats them for display.
 * It uses the wpestate_generate_property_slider_image_ids function to get attachment IDs
 * and filters for PDF documents.
 *
 * @param int $post_id The ID of the property post.
 * @return string Formatted HTML string containing links to PDF documents.
 */
if (!function_exists('wpestare_return_documents')):
    function wpestare_return_documents($post_id) {
        // Get all attachment IDs for the property
        $attachment_ids = wpestate_generate_property_slider_image_ids($post_id);
        
        $return = '';
        
        foreach ($attachment_ids as $attachment_id) {
            // Check if the attachment is a PDF
            if (get_post_mime_type($attachment_id) === 'application/pdf') {
                $attachment = get_post($attachment_id);
                
                // Start building the document link
                $return .= '<div class="document_down">';
                
                // Include PDF icon
                ob_start();
                include(locate_template('/templates/svg_icons/pdf_icon.svg'));
                $icon = ob_get_clean();
                
                // Add icon and link to the document
                $return .= $icon . '<a href="' . esc_url(wp_get_attachment_url($attachment_id)) . '" target="_blank">' . esc_html($attachment->post_title) . '</a></div>';
            }
        }
        
        return $return;
    }
endif;


