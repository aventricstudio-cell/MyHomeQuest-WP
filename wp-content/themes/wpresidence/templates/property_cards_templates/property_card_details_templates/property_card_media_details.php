<?php
/** MILLDONE
 * Property Card Media Details Template
 * src: templates\property_cards_templates\property_card_details_templates\property_card_media_details.php
 * This template is responsible for displaying media information (images and videos)
 * for a property card in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyCard
 * @since 1.0
 */

$post_attachments=wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID,  'media');
// Initialize media count

$image_counter = 0;
$featured_image = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID,  'featured_media');

if( $featured_image!==''    ){
    $image_counter=1;
}


if(is_array($post_attachments)){
    // Loop through each attachment ID
    foreach ($post_attachments as $attachment_id=>$details) {
        // Get the MIME type of the attachment

        $mime_type = get_post_mime_type($attachment_id);

        // Check if the MIME type indicates media (image, video, or audio)
        if (strpos($mime_type, 'image') !== false || 
            strpos($mime_type, 'video') !== false || 
            strpos($mime_type, 'audio') !== false) {
            $image_counter++;
        }
    }
}




// Check for embed video
$has_video = !empty(wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID,  'meta',  'embed_video_id') );

?>
<div class="property_media">
    <?php if ($has_video) : ?>
        <i class="fas fa-video"></i>
    <?php endif; ?>
    <i class="fas fa-camera"></i><?php printf(' %s', esc_html($image_counter)); ?>
</div>