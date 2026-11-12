<?php 
/** 
 * src: api\cache\cache_get_data_functions.php
 * Purpose: Core functions for retrieving and formatting data for caching
 * Contains optimized methods for collecting post data, terms, and metadata
 * Provides helper functions for data formatting and organization
 */


/**
 * Retrieves and simplifies term data for caching
 * @param int $post_id Post ID
 * @param string $taxonomy Taxonomy name
 * @return array Simplified term data array
 * @since 4.0.0
 */
function wpestate_api_get_simplified_terms($post_id, $taxonomy) {
    $terms = wp_get_post_terms($post_id, $taxonomy);
    $simplified_terms = [];
    
    if (!is_wp_error($terms)) {
        foreach ($terms as $term) {
            $simplified_terms[] = [
                'term_id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'description' => $term->description
            ];
        }
    }
    
    return $simplified_terms;
}



/**
 * Batch retrieves terms for multiple taxonomies in single query
 * @param int $post_id Post ID
 * @param array $taxonomies Array of taxonomy names
 * @return array Terms organized by taxonomy
 * @since 4.0.0
 */


function wpestate_api_get_optimized_terms_for_taxonomy($post_id, $taxonomies) {
    $terms_data = array();
    $all_terms = wp_get_object_terms($post_id, $taxonomies, array('fields' => 'all'));
    
    if (!is_wp_error($all_terms)) {
        foreach ($all_terms as $term) {
            if (!isset($terms_data[$term->taxonomy])) {
                $terms_data[$term->taxonomy] = [];
            }
            
            $term_link = get_term_link($term);
            if (is_wp_error($term_link)) {
                $term_link = '';
            }

            $terms_data[$term->taxonomy][] = [
                'term_id' => $term->term_id,
                'name' => '<a href="'.esc_url( $term_link) .'" >'.$term->name.'</a>',
                'slug' => $term->slug,
                'description' => $term->description
            ];
        }
    }
    
    return $terms_data;
}




// Function: wpestate_generate_array_image_urls
/**
 * Creates array of image URLs for all registered sizes
 * @param array $media_ids Array of attachment IDs
 * @return array URLs organized by attachment ID and size
 * @since 4.0.0
 */

function wpestate_generate_array_image_urls($media_ids) {
    $result = array();

    foreach ($media_ids as $attachment_id) {
        // Get all available sizes for the attachment
        $sizes = array();
        $metadata = wp_get_attachment_metadata($attachment_id);

        if ($metadata && isset($metadata['sizes'])) {
            foreach ($metadata['sizes'] as $size_name => $size_data) {
                $sizes[$size_name] = wp_get_attachment_image_url($attachment_id, $size_name);
            }
        }

        // Include the full-size image
        $sizes['full'] = wp_get_attachment_image_url($attachment_id, 'full');

        $result[$attachment_id] = $sizes;
    }

    return $result;
}



/**
 * Generates an array of image attachment IDs for a given property.
 *
 * This function retrieves all image attachments associated with a specific property
 * and returns their IDs in an array. The images are ordered by their menu order in ascending order.
 *
 * @param int $propID The ID of the property for which to retrieve image attachment IDs.
 * @param bool $include_thumbnail Whether to include the featured image (thumbnail) in the result.
 * @return array An array of image attachment IDs.
 */
function wpestate_generate_property_slider_image_ids_b($propID, $include_thumbnail=false) {
    // Check if the wpestate_property_gallery meta exists
    $gallery_meta = get_post_meta($propID, 'wpestate_property_gallery', true);

    if (!empty($gallery_meta)) {
        if(is_string($gallery_meta)){
            $gallery_meta = array_filter(explode(',', $gallery_meta));
        }
    
        // If include_thumbnail is true, add the post thumbnail ID at the beginning of the array
        if ($include_thumbnail) {
            $thumbnail_id = get_post_thumbnail_id($propID);
            if ($thumbnail_id) {
                array_unshift($gallery_meta, $thumbnail_id);
            }
        }

        // enforce unique values
        $gallery_meta = array_unique($gallery_meta);
      
        // If meta exists, return it as an array
        return $gallery_meta;
    }

    // If meta doesn't exist, fetch attachments
    $arguments = array(
        'numberposts' => -1,
        'post_type' => 'attachment',
        'post_mime_type' => 'image',
        'post_parent' => $propID,
        'post_status' => null,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'fields' => 'ids' // Return only the IDs
    );

    $post_attachments = get_posts($arguments);

    // If we have attachments, save them as post meta
    if (!empty($post_attachments)) {
        $gallery_meta = implode(',', $post_attachments);
        update_post_meta($propID, 'wpestate_property_gallery', $gallery_meta);
    } else {
        // If no attachments found, save an empty string to prevent future queries
        update_post_meta($propID, 'wpestate_property_gallery', '');
        $post_attachments = array(); // Ensure we return an empty array

        // If include_thumbnail is true, add the post thumbnail ID at the beginning of the array
        if ($include_thumbnail) {
            $thumbnail_id = get_post_thumbnail_id($propID);
            if ($thumbnail_id) {
                array_unshift($post_attachments, $thumbnail_id);
            }
        }
    }

    return $post_attachments;
}