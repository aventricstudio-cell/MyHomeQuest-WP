<?php
/**
 * Optimized template for displaying Property Card Agent Details (Type 4) using cached agent data.
 * src: templates\property_cards_templates\property_card_details_templates\property_card_agent_details_type4.php
 * This file renders the agent details section of a property card for Type 4 layout.
 *
 * @param array $property_unit_cached_data Cached property data array.
 * @param int   $postID                    Property ID.
 */

// Retrieve agent ID associated with the property from cache
$agent_id = intval(wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'property_agent'));

// Apply WPML filter if the function exists
if (function_exists('icl_translate')) {
    $agent_id = apply_filters('wpml_object_id', $agent_id, 'estate_agent');
}

// Apply WPR filter if the function exists
if (function_exists('wpr_object_id')) {
    $agent_id = apply_filters('wpr_object_id', $agent_id, 'estate_agent');
}



// Retrieve cached agent data based on the agent ID
//$property_agent_cached_data = wpestate_api_get_cached_post_data($agent_id, 'estate_agent');
if(function_exists('wpestate_api_get_cached_post_data')){
    $property_agent_cached_data = wpestate_api_get_cached_post_data($agent_id, 'estate_property');
}else{
    $property_agent_cached_data =array();
}

// Fetch agent image from cached agent data
$agent_face_image = $property_agent_cached_data['featured_media'][array_key_first($property_agent_cached_data['featured_media'])]['agent_picture_thumb'] ?? '';

// Get post author ID from cached data
$post_author_id = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, '', 'post_author');

// Use author's picture if no agent image available
if (empty($agent_face_image)) {
    $author_picture_id = get_the_author_meta('user_meda_id', $post_author_id);
    $agent_face_image = $author_picture_id ? wp_get_attachment_image_url($author_picture_id, 'agent_picture_thumb') : get_theme_file_uri('/img/default-user_1.png');
}

// Check if agent image should be displayed
if (wpresidence_get_option('property_card_agent_section_tab_show_agent_image', '') == 'yes') {
    $agent_link = esc_url($property_agent_cached_data['permalink'] ?? get_permalink($agent_id));
?>
    <div class="property_agent_wrapper">
        <a href="<?php echo $agent_link; ?>">
            <div class="property_agent_image" style="background-image:url('<?php echo esc_attr($agent_face_image); ?>')"></div>
            <div class="property_agent_image_sign"><i class="far fa-user-circle"></i></div>
        </a>
    </div>
<?php
}
?>
