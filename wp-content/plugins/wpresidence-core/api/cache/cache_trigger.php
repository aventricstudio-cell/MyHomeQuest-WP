<?php 
/** 
 * src: api\cache\cache_trigger.php
 * Purpose: Manages automatic cache triggers and WordPress hooks
 * Handles cache lifecycle events (creation, updates, deletion)
 * Provides batch processing capabilities for term-related cache updates
 */



/**
 * Triggers cache updates when posts are saved
 * @param int $post_id The ID of the post being saved
 * @hooks into save_post
 * @since 4.0.0
 * @prevents recursion during autosave
 */
add_action('save_post', 'wpestate_api_on_save_post');
if(!function_exists('wpestate_api_on_save_post')){
        function wpestate_api_on_save_post($post_id){
                // Avoid recursion during autosave or quick edit
                if((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($post_id)){
                        return;
                }

                // Get the post type
                $post_type = get_post_type($post_id);
                // Check if the post type is one that should be cached
                if(!wpestate_api_permit_cache_operations($post_type)){
                        return;
                }

                // Reset global caches when a property post is saved
                if($post_type === 'estate_property' && function_exists('wpestate_delete_cache')){
                        wpestate_delete_cache();
                }

                // Cache the post data
                wpestate_api_set_cache_post_data($post_id, $post_type);
        }
}


/**
 * Manages cache cleanup when posts are deleted
 * @param int $post_id The ID of the post being deleted
 * @hooks into delete_post
 * @since 4.0.0
 */
add_action('delete_post', 'wpestate_api_on_delete_post');
if(!function_exists('wpestate_api_on_delete_post')){
	function wpestate_api_on_delete_post($post_id){
		// Get the post type
		$post_type = get_post_type($post_id);
		// Check if the post type is one that should be cached
        if(!wpestate_api_permit_cache_operations($post_type)){  
			return;
		}
		// Clear the cache
		wpestate_api_clear_post_cache($post_id, $post_type);
	}
}



/**
 * Update cache for posts associated with a term when the term is updated.
 * This optimized function processes posts in batches to avoid memory issues.
 *
 * @param int    $term_id          The ID of the updated term.
 * @param int    $term_taxonomy_id Term taxonomy ID.
 * @param string $taxonomy         The taxonomy of the term.
 */
if(!function_exists('wpestate_api_update_cache_on_term_change')){
	function wpestate_api_update_cache_on_term_change($term_id, $term_taxonomy_id, $taxonomy){
		$cached_post_types = wpestate_api_get_cached_post_types_and_data();
		$batch_size        = 50;

		// Map post types to their taxonomies once, outside the loop
		$taxonomy_map = [];
		foreach($cached_post_types as $post_type){
			$post_type_data = wpestate_api_get_cached_post_types_and_data($post_type);
			if(isset($post_type_data['taxonomies'])){
				foreach($post_type_data['taxonomies'] as $tax){
					$taxonomy_map[$tax][] = $post_type;
				}
			}
			
		}

		// Check if this taxonomy is in our cached taxonomy map
		if(isset($taxonomy_map[$taxonomy])){
			$paged = 1;
			do{
				$posts = get_posts([
					                   'post_type'      => array_unique($taxonomy_map[$taxonomy]),
					                   'tax_query'      => [
						                   [
							                   'taxonomy' => $taxonomy,
							                   'field'    => 'term_id',
							                   'terms'    => $term_id,
						                   ],
					                   ],
					                   'posts_per_page' => $batch_size,
					                   'fields'         => 'ids',
					                   'paged'          => $paged,
				                   ]);

				foreach($posts as $post_id){
					$post_type = get_post_type($post_id);
					wpestate_api_set_cache_post_data($post_id, $post_type);
				}

				$paged ++;
			}while(count($posts) === $batch_size);
		}
	}
}

// Hook the function to the edited_term action to update cache when a term is updated
add_action('edited_term', 'wpestate_api_update_cache_on_term_change', 10, 3);




/**
 * Handle clearing property widgets transients when properties are modified
 * 
 * This function clears all transients for the footer latest widget
 * when any property post is saved, updated, or deleted
 * 
 * @param int $post_id The ID of the modified post
 * @return void
 */
function wpestate_clear_property_widget_cache($post_id) {
    // Skip if this is an autosave or not a property post
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Only run for property post type
    if (get_post_type($post_id) !== 'estate_property') {
        return;
    }
    
    // Get all transients from the options table
    global $wpdb;
    
    // Define the prefix for our widget transients
    $transient_prefix = '%wpestate_widget_recent_query_output_%';
    
    // Delete all matching transients
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE %s 
             OR option_name LIKE %s",
            '_transient_' . $transient_prefix,
            '_transient_timeout_' . $transient_prefix
        )
    );
}

// Hook the cleanup function to property post events
add_action('save_post', 'wpestate_clear_property_widget_cache');
add_action('deleted_post', 'wpestate_clear_property_widget_cache');
add_action('edit_post', 'wpestate_clear_property_widget_cache');