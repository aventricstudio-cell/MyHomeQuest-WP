<?php 
/** 
 * src: api\cache\cache_admin_interface.php
 * Purpose: Handles the admin interface functionality for the caching system
 * Manages the display and interaction with cached data in the WordPress admin interface
 * Provides user interface elements for viewing and managing cached data
 */




/**
 * Adds a meta box to display cached data in the admin post edit screen
 * @since 4.0.0
 * @uses wpestate_api_get_cached_post_types_and_data()
 * @hooks into add_meta_boxes
 */

if (!function_exists('wpestate_api_add_cached_data_meta_box')) {
    function wpestate_api_add_cached_data_meta_box() {
        $post_types = array_keys( wpestate_api_get_cached_post_types_and_data() );

        foreach ($post_types as $post_type) {
            add_meta_box(
                'wpestate_cached_data_meta_box',
                esc_html__('Cached Data', 'wpresidence-core'),
                'wpestate_render_cached_data_meta_box',
                $post_type,
                'normal',
                'low'
            );
        }
    }
  
}

/**
 * Initialize API cache metabox functionality
 * This action runs after Redux Framework has loaded (priority 15 vs Redux's priority 1)
 * It checks if the cache metabox display option is enabled and adds the metabox if necessary
 */
add_action('init', function() {
    // Access Redux options through the global variable
    global $wpresidence_admin;


    // Check if the cache metabox display option is set to 'yes'
    // wpresidence_get_option() is a helper function that safely gets Redux options
    if ( wpresidence_get_option('wp_estate_display_cache_metabox','') =='yes' ){
        // Register the metabox to display cached API data
        // This will be called during WordPress' meta box registration process

        add_action('add_meta_boxes', 'wpestate_api_add_cached_data_meta_box');
    }
}, 9999); // Priority 15 ensures Redux (priority 1) has loaded first and options are available





/**
 * Renders the content of the cached data meta box
 * @param WP_Post $post The current post object
 * @since 4.0.0
 * @displays Formatted view of cached data or "No cached data" message
 */
if (!function_exists('wpestate_render_cached_data_meta_box')) {
    function wpestate_render_cached_data_meta_box($post) {
        $post_type = get_post_type($post->ID);

        print 'post id '.$post->ID.'/'.$post_type.'</br>';
        $cached_data = wpestate_api_get_cached_post_data($post->ID, $post_type);

        if (empty($cached_data)) {
            echo '<p>' . __('No cached data available.', 'wpresidence-core') . '</p>';
            return;
        }

      
        echo '<div class="wpresidence-cached-data" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">';
        echo '<h4>' . __('Cached Data:', 'wpresidence-core') . '</h4>';
        echo '<pre>' . esc_html(print_r($cached_data, true)) . '</pre>';
        echo '</div>';
    }
}



/**
 * Adds a "Reset Cache" button to the post publish/update meta box
 * @since 4.0.0
 * @hooks into post_submitbox_misc_actions
 * @security Includes nonce verification
 */

add_action('post_submitbox_misc_actions', 'wpestate_add_reset_cache_button');
if(!function_exists('wpestate_add_reset_cache_button')){
	function wpestate_add_reset_cache_button(){
		global $post;

		if( wpestate_api_permit_cache_operations($post->post_type) ){
			$reset_cache_url = wp_nonce_url(admin_url('admin-post.php?action=wpestate_core_reset_post_cache&post_id=' . $post->ID), 'wpestate_purge_cache');

			?>
        <div class="misc-pub-section">
            <a href="<?php echo esc_url($reset_cache_url) ?>"
                class="button button-secondary"><?php esc_html_e('Reset WPresidence Cache', 'wpresidence-core'); ?></a>
        </div>
        <?php
		}
	}
}



/**
 * Adds a "Reset Cache" link to the post row actions
 * @param array $actions Existing row actions
 * @param WP_Post $post Current post object
 * @return array Modified row actions
 * @since 4.0.0
 */
add_filter('post_row_actions', 'wpestate_add_reset_cache_action', 10, 2);
if(!function_exists('wpestate_add_reset_cache_action')){
	function wpestate_add_reset_cache_action($actions, $post){

		if( in_array($post->post_type,   array_keys( wpestate_api_get_cached_post_types_and_data() ) ) ){

			$reset_cache_url = wp_nonce_url(admin_url('admin-post.php?action=wpestate_core_reset_post_cache&post_id=' . $post->ID), 'wpestate_purge_cache');

			$actions['reset_cache'] = '<a href="' . esc_url($reset_cache_url) . '">' . esc_html__('Reset WPresidence Cache', 'wpresidence-core') . '</a>';
		}

		return $actions;
	}
}