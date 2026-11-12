<?php
/**
 * src: api\cache\cache_actions.php
 * Purpose: Core cache manipulation functions
 * Handles reading, writing, and clearing of cache data
 * Supports both object cache and transients
 */




/**
 * Cache the post meta and taxonomy data for a given post.
 *
 * This function checks if the object cache is available (using wp_using_ext_object_cache()).
 * If not, it falls back to using transients.
 *
 * @param int    $post_id   The ID of the post to cache.
 * @param string $post_type The post type of the post.
 */
if(!function_exists('wpestate_api_set_cache_post_data')){

	function wpestate_api_set_cache_post_data($postID, $post_type) {
		$post_type_data = wpestate_api_get_cached_post_types_and_data();
		$post_type_data_structure = $post_type_data[$post_type];
		$post = get_post($postID);
		// Get all post meta in a single query
		$all_post_meta = get_post_meta($postID);
	
		$post_thumbnail_id = get_post_thumbnail_id($postID);
		$thumbnail_array = [$post_thumbnail_id];
		

		$cache_data = [
			'ID' => $postID,
			'title' => get_the_title($postID),
			'description' => $post->post_content,
			'excerpt' => get_the_excerpt($postID),
			'permalink'=>get_permalink($postID),
			'featured_media'=>wpestate_generate_array_image_urls( $thumbnail_array ),
			'media' => wpestate_generate_array_image_urls(wpestate_generate_property_slider_image_ids($postID, false)),
			'terms' => array(),
			'meta' => array(),
		];
	
		



		// Populate standard meta
		foreach ($post_type_data_structure['meta'] as $meta_key) {
			$cache_data['meta'][$meta_key] = isset($all_post_meta[$meta_key]) ? maybe_unserialize(   $all_post_meta[$meta_key][0] ): '';
			
		}

		if($post_type=='estate_property'){
			// Populate custom meta
			foreach ($post_type_data_structure['custom_meta'] as $meta_key) {
				$cache_data['custom_meta'][$meta_key] = isset($all_post_meta[$meta_key]) ? $all_post_meta[$meta_key][0] : '';
			}
		}
	
		foreach ($post_type_data_structure['taxonomies'] as $taxonomy) {
			$cache_data['terms'] = wpestate_api_get_optimized_terms_for_taxonomy($postID, $post_type_data_structure['taxonomies']);
		}
	
		$cache_key = wpestate_api_get_cache_key($post_type, $postID);
		
		if (wp_using_ext_object_cache()) {
			wp_cache_set($cache_key, $cache_data, '', 24 * HOUR_IN_SECONDS);
		} else {
			set_transient($cache_key, $cache_data, 24 * HOUR_IN_SECONDS);
		}
	}
}






/**
 * Retrieve cached data for a given post. If no cache is found, create it, store it, and return the data.
 *
 * @param int    $post_id   The ID of the post.
 * @param string $post_type The type of the post.
 *
 * @return array The cached data, including 'meta' and 'terms'.
 */
if(!function_exists('wpestate_api_get_cached_post_data')){
	function wpestate_api_get_cached_post_data($post_id, $post_type){
		// Generate cache key for the post

  		

		$cache_key = wpestate_api_get_cache_key($post_type, $post_id);

		// Try to retrieve the cached data
		if(wp_using_ext_object_cache()){
			// Fetch data from object cache
			$cached_data = wp_cache_get($cache_key);
		}else{
			// Fetch data from transient		
			$cached_data = get_transient($cache_key);
		}

 

		// If cache is empty, generate and store it
		if($cached_data === false){
            
			// Cache does not exist; generate the cache data
			wpestate_api_set_cache_post_data($post_id, $post_type);

			// Fetch the newly cached data
			if(wp_using_ext_object_cache()){
				$cached_data = wp_cache_get($cache_key);
			}else{
				$cached_data = get_transient($cache_key);
			}
		}

		return $cached_data;
	}
}



/**
 *
 * Clear the cache for a given post ID.
 * This function checks if the object cache is available. If not, it
 * falls back to clearing transients.
 *
 * @param int    $post_id   The ID of the post to clear.
 * @param string $post_type The post type of the post.
 *
 */
if(!function_exists('wpestate_api_clear_post_cache')){
	function wpestate_api_clear_post_cache($post_id, $post_type){
		// Generate cache key
		$cache_key = wpestate_api_get_cache_key($post_type, $post_id);
		// Check if the object cache is available
		if(wp_using_ext_object_cache()){
			// Object cache is available, use wp_cache_delete
			wp_cache_delete($cache_key);
		}else{
			// Object cache is not available, use delete_transient
			delete_transient($cache_key);
		}
	}
}


/**
 * Clear all cached data related to posts and terms.
 *
 * This function clears all cached data, both from the object cache
 * (using wp_cache_flush) and from transients associated with cached post data.
 */
if(!function_exists('wpestate_api_clear_all_cache')){
	function wpestate_api_clear_all_cache(){
		// 1. Clear all object cache
		if(wp_using_ext_object_cache()){
			wp_cache_flush(); // Flush the entire object cache
		}

		// 2. Clear all transients associated with cached post data
		global $wpdb;

		// Define the prefix used in the transient keys in the codebase
		$transient_prefix = 'wpestate_';

		// Delete all transients that match the prefix used for caching
		$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s", "_transient_{$transient_prefix}%", "_transient_timeout_{$transient_prefix}%"));
	}
}


/**
 * Handles manual cache reset requests
 * @since 4.0.0
 * @hooks into admin_post_wpestate_core_reset_post_cache
 * @security Includes nonce verification
 */
add_action('admin_post_wpestate_core_reset_post_cache', 'wpestate_core_reset_post_cache');
if(!function_exists('wpestate_core_reset_post_cache')){
	function wpestate_core_reset_post_cache(){
		if(isset($_GET['_wpnonce'], $_GET['post_id'])){

			if(!wp_verify_nonce($_GET['_wpnonce'], 'wpestate_purge_cache')){
				wp_nonce_ays('');
			}

			$post_id   = intval($_GET['post_id']);
			$post_type = get_post_type($post_id);

			wpestate_api_clear_post_cache($post_id, $post_type);

			$redirect_url = add_query_arg('cache_reset_success', '1', wp_get_referer());
			wp_redirect($redirect_url);
			die();
		}
	}
}




/**
 * WordPress Estate Theme - Property Data Retrieval Function
 * 
 * This function retrieves property data from a cache array if it exists,
 * otherwise falls back to retrieving data directly from the database.
 * It supports retrieving general post data, meta data, taxonomy terms,
 * featured images, and media galleries.
 * 
 * @param array  $property_unit_cached_data The cached property data array
 * @param int    $propID                    The property post ID
 * @param string $type                      The type of data to retrieve ('', 'meta', 'custom_meta', 'terms', 'featured_media', 'media')
 * @param string $data                      The specific data field to retrieve
 * @return mixed                            The requested property data or null if not found
 */



 
function wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $propID, $type = '', $data = '') {
    // General Data
    if ($type == '') {
        // Check if the requested general post field exists in cache
        if (isset($property_unit_cached_data[$data])) {
            return $property_unit_cached_data[$data];
        } else {
            // Fallback to database query for post field
            return get_post_field($data, $propID, 'raw');
        }
    }
   
    // Meta Data
    elseif ($type == 'meta' || $type == 'custom_meta') {
        // Special handling for property gallery meta
        if($data=='wpestate_property_gallery'){
            if (isset($property_unit_cached_data[$type][$data])) {
                // Convert comma-separated gallery string to array if needed
                if(is_string($property_unit_cached_data[$type][$data] )){
                    $gallery_meta = array_filter( explode(',', $property_unit_cached_data[$type][$data]  ));
                }
                return $property_unit_cached_data[$type][$data];
            } else {
                // Fallback to generating property slider images
                return wpestate_generate_property_slider_image_ids($propID,true);
            }
        }
        // Standard meta data handling
        if (isset($property_unit_cached_data[$type][$data])) {
            return $property_unit_cached_data[$type][$data];
        } else {
            // Fallback to database query for post meta
            return get_post_meta($propID, $data, true);
        }
    }
   
    // Terms Data (Taxonomies)
    elseif ($type == 'terms') {
        if (isset($property_unit_cached_data['terms'][$data])) {
            return $property_unit_cached_data['terms'][$data];
        } else {
            // Fallback to database query for taxonomy terms
            $terms = wp_get_post_terms($propID, $data, array('fields' => 'all'));
            // Return terms array or empty array if no terms or error
            return (!is_wp_error($terms) && !empty($terms)) ? $terms : array();
        }
    }
    // Featured Media (Only Featured Image)
    elseif ($type == 'featured_media') {
        if (!empty($property_unit_cached_data['featured_media'])) {
            // Get the first featured media item from cache
            $first_featured = reset($property_unit_cached_data['featured_media']);
			
			// If the requested size exists, return it
			if (isset($first_featured[$data])) {
				return $first_featured[$data];
			}
			// Default to full size if requested size doesn't exist
			return $first_featured['full'] ?? '';



        } else {
            // Fallback to database query for featured image
            $attachment_id = get_post_thumbnail_id($propID);
            $media = wp_get_attachment_image_src($attachment_id, $data);
            // Return image URL or empty string if no image
            return $media ? $media[0] : '';
        }
    }
    // Media (All Images in Gallery)
    elseif ($type == 'media') {
        if (!empty($property_unit_cached_data['media'])) {
            // Return all cached media images
            return $property_unit_cached_data['media'];
        } else {
            // Fallback to database query for gallery images
            $gallery_ids = get_post_meta($propID, 'wpestate_property_gallery', true);
            $images = array();
            if (!empty($gallery_ids) && is_array($gallery_ids)) {
                // Build array of image URLs in different sizes for each gallery image
                foreach ($gallery_ids as $attachment_id) {
                    $images[$attachment_id] = array(
                        'full' => wp_get_attachment_image_url($attachment_id, 'full'),
                        'thumbnail' => wp_get_attachment_image_url($attachment_id, 'thumbnail'),
                        'property_listings' => wp_get_attachment_image_url($attachment_id, 'property_listings'),
                    );
                }
            }
            // Return all fetched images or empty array if none found
            return !empty($images) ? $images : array();
        }
    }
    // Default return if no condition matches
    return null;
}