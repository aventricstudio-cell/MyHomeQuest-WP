<?php
/**
 * Property Similar Listings
 *
 * This function displays or returns a list of similar property listings
 * based on the current property page. It uses a template to render the listings.
 *
 * @package WpResidence
 * @since 3.0.3
 *
 * @param int    $postID          The ID of the current property post.
 * @param string $is_tab          Optional. Determines if the content should be rendered as a tab. Defaults to ''.
 * @param string $tab_active_class Optional. Adds an active class to the tab if set. Defaults to ''.
 *
 * @return string|void Returns the content as a tab item if $is_tab is 'yes', otherwise it prints the content.
 */

if ( ! function_exists( 'wpestate_property_similar_listings_v2' ) ) :
    function wpestate_property_similar_listings_v2( $postID, $is_tab = '', $tab_active_class = '' ) {
        global $post;

        // Get labels and data for the 'Similar Listings' section.
        $data  = wpestate_return_all_labels_data( 'similar' );

        // Prepare the label to be displayed, with a fallback to default if theme option is not set.
        $label = wpestate_property_page_prepare_label( $data['label_theme_option'], $data['label_default'] );

        // Start output buffering to capture the output of the included template.
        ob_start();

        // Include the template file that displays similar listings.
        // This keeps the HTML markup separate from the PHP logic.
        include locate_template( '/templates/listing_templates/property-page-templates/similar_listings.php' );

        // Get the content from the buffer and clean the buffer.
        $content = ob_get_clean();

        // If $is_tab is set to 'yes', return the content wrapped in a tab.
        if ( $is_tab === 'yes' ) {
            // Return the content as a tab item using the tab creation function.
            return wpestate_property_page_create_tab_item( ( $content ), esc_html( $label ), esc_attr( $data['tab_id'] ), esc_attr( $tab_active_class ) );
        } else {
            // Output the content directly, ensuring it is properly escaped.
            echo ( trim( $content ) );
        }
    }
endif;
