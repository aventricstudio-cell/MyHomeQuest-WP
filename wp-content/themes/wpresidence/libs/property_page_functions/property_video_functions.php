<?php
/* MILLDONE 
* src: libs\property_page_functions\property_video_functions.php
*/

/**
 * Display property video.
 *
 * This function generates the HTML for displaying a property video. It can output
 * the content either as a tab or as an accordion item.
 *
 * @since 3.0.3
 *
 * @param int    $postID           The ID of the property post.
 * @param string $is_tab           Optional. Whether to display as a tab. Default ''.
 * @param string $tab_active_class Optional. CSS class for active tab. Default ''.
 * @return string|void HTML output if $is_tab is 'yes', otherwise echoes the HTML.
 */
if ( ! function_exists( 'wpestate_property_video_v2' ) ) :
    function wpestate_property_video_v2( $postID, $is_tab = '', $tab_active_class = '' ) {
        // Check if the property has a video
        if ( empty( get_post_meta( $postID, 'embed_video_id', true ) ) ) {
            return;
        }

        // Retrieve label data for video
        $data = wpestate_return_all_labels_data( 'video' );

        // Prepare the label for display
        $label = wpestate_property_page_prepare_label( $data['label_theme_option'], $data['label_default'] );

        // Generate the video content
        $content = wpestate_listing_video( $postID );

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
 * Generate HTML for a property listing video.
 *
 * This function creates the HTML markup for displaying a property video,
 * including the video thumbnail and the link to play the video.
 *
 * @param int   $post_id                  The ID of the property post.
 * @param array $wpestate_prop_all_details Optional. An array of all property details.
 * @return string The HTML markup for the property listing video.
 */
if ( ! function_exists( 'wpestate_listing_video' ) ) :
    function wpestate_listing_video( $post_id, $wpestate_prop_all_details = '' ) {
        // Retrieve video details
        $video_details = wpresidence_get_video_details( $post_id, $wpestate_prop_all_details );

        // Get video thumbnail
        $full_img_path = wpresidence_get_video_thumbnail( $post_id, $video_details['custom_video'] );

        // Generate video link
        $video_link = wpresidence_generate_video_link( $video_details['type'], $video_details['id'] );

        // Determine Venobox type and max width based on video provider
        if ( $video_details['type'] === 'tiktok' ) {
            $vb_type   = 'iframe';
            $max_width = '325px';
        } else {
            $vb_type   = 'video';
            $max_width = '';
        }

        // Generate and return video HTML
        return wpresidence_generate_video_html( $video_link, $full_img_path, $vb_type, $max_width );
    }
endif;

/**
 * Retrieve video details for a property.
 *
 * @param int   $post_id                  The ID of the property post.
 * @param array $wpestate_prop_all_details Optional. An array of all property details.
 * @return array An array of video details.
 */
if ( ! function_exists( 'wpresidence_get_video_details' ) ) :
    function wpresidence_get_video_details( $post_id, $wpestate_prop_all_details ) {
        if ( empty( $wpestate_prop_all_details ) ) {
            return array(
                'custom_video' => get_post_meta( $post_id, 'property_custom_video', true ),
                'id'           => esc_html( get_post_meta( $post_id, 'embed_video_id', true ) ),
                'type'         => strtolower( trim( esc_html( get_post_meta( $post_id, 'embed_video_type', true ) ) ) ),
            );
        } else {
            return array(
                'custom_video' => esc_html( wpestate_return_custom_field( $wpestate_prop_all_details, 'property_custom_video' ) ),
                'id'           => esc_html( wpestate_return_custom_field( $wpestate_prop_all_details, 'embed_video_id' ) ),
                'type'         => strtolower( trim( esc_html( wpestate_return_custom_field( $wpestate_prop_all_details, 'embed_video_type' ) ) ) ),
            );
        }
    }
endif;

/**
 * Get the video thumbnail for a property.
 *
 * @param int    $post_id      The ID of the property post.
 * @param string $custom_video The custom video URL, if any.
 * @return string The URL of the video thumbnail.
 */
if ( ! function_exists( 'wpresidence_get_video_thumbnail' ) ) :
    function wpresidence_get_video_thumbnail( $post_id, $custom_video ) {
        if ( ! empty( $custom_video ) ) {
            return $custom_video;
        }

        $thumb_id = get_post_thumbnail_id( $post_id );
        $full_img = wp_get_attachment_image_src( $thumb_id, 'listing_full_slider_1' );

        return isset( $full_img[0] ) ? $full_img[0] : '';
    }
endif;

/**
 * Generate the video link based on the video type and ID.
 *
 * @param string $video_type The type of video (vimeo or youtube).
 * @param string $video_id   The ID of the video.
 * @return string The generated video link.
 */
if ( ! function_exists( 'wpresidence_generate_video_link' ) ) :
    function wpresidence_generate_video_link( $video_type, $video_id ) {
        $protocol = is_ssl() ? 'https' : 'http';

        if ( $video_type === 'vimeo' ) {
            return "{$protocol}://player.vimeo.com/video/{$video_id}?api=1&amp;player_id=player_1";
        } elseif ( $video_type === 'tiktok' ) {
            return "{$protocol}://www.tiktok.com/embed/v2/{$video_id}";
        } else {
            return "{$protocol}://www.youtube.com/embed/{$video_id}?wmode=transparent&amp;rel=0";
        }
    }
endif;

/**
 * Generate the HTML for the video player.
 *
 * @param string $video_link    The link to the video.
 * @param string $full_img_path The path to the video thumbnail image.
 * @return string The generated HTML for the video player.
 */
if ( ! function_exists( 'wpresidence_generate_video_html' ) ) :
    function wpresidence_generate_video_html( $video_link, $full_img_path, $vb_type = 'video', $max_width = '' ) {
        // Ensure TikTok URLs always open in an iframe with a constrained width
        if ( strpos( $video_link, 'tiktok.com' ) !== false ) {
            $vb_type   = 'iframe';
            $max_width = $max_width ? $max_width : '325px';
        }

        $maxwidth_attr = $max_width ? ' data-maxwidth="' . esc_attr( $max_width ) . '"' : '';

        return sprintf(
            '<div class="property_video_wrapper"><div id="property_video_wrapper_player"></div><a href="%s" data-autoplay="true" data-vbtype="%s"%s class="venobox vbox-item"><img src="%s" alt="%s" /></a></div>',
            esc_url( $video_link ),
            esc_attr( $vb_type ),
            $maxwidth_attr,
            esc_url( $full_img_path ),
            esc_attr__( 'video image', 'wpresidence' )
        );
    }
endif;