<?php

/**
 * Generate single agent detail
 *
 * This function generates a simple property detail for the WpResidence theme.
 * It handles various property attributes and custom fields, formatting them
 * for display on the front end.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 1.0.0
 */

if (!function_exists('wpestate_estate_agent_single_detail')) :
/**
 * Generate siingle agent detail
 *
 * @param array $attributes Shortcode attributes
 * @param string|null $content Shortcode content (unused)
 * @return string Formatted HTML for the property detail
 */
function wpestate_estate_agent_single_detail($attributes, $content = null) {
    global $post;
    global $propid;

    $return_string = '';
    $detail = '';
    $label = '';

    // Get property features
    $features_details = array();
    // $feature_terms =  wpestate_get_cached_terms('property_features');
    // if (is_array($feature_terms)) {
    //     foreach ($feature_terms as $term) {
    //         $features_details[$term->slug] = $term->name;
    //     }
    // }

    $data_for = 'estate_agent';
    if ( isset($attributes['type']) && !empty( $attributes['type'] ) ) {
        $data_for = $attributes['type'];
    }

    // Parse shortcode attributes
    $attributes = shortcode_atts(
        array(
            'detail' => 'none',
            'label' => 'Label:',
            'is_elementor' => '',
            'id' => ''
        ),
        $attributes
    );

    $detail = $attributes['detail'];
    $label = $attributes['label'];

    if ( isset( $attributes['id'] ) )
        $agentID = intval($attributes['id']);

    // Handle Elementor compatibility
    // if (intval($propid) == 0 && isset($attributes['is_elementor']) && intval($attributes['is_elementor'] == 1)) {
    //     $agentID = wpestate_last_agent_id();//ok
    // }

    // // Apply WPML filter if the function exists
    if (function_exists('icl_translate')) {
        $agentID = apply_filters('wpml_object_id', $agentID, $data_for);
    }

    if(function_exists('wpestate_api_get_cached_post_data')){
        $property_agent_cached_data = wpestate_api_get_cached_post_data($agentID, $data_for);
    }else{
        $property_agent_cached_data =array();
    }

    $realtor_details = wpestate_return_agent_details_from_cache($property_agent_cached_data, $agentID);

    $return_string = array_key_exists($detail, $realtor_details) ? $realtor_details[$detail] : '';

    // Wrap and return the final string if not empty
    if ($return_string !== '') {
        $return_string = sprintf(
            '<div class="agent_custom_detail_wrapper"><span class="agent_custom_detail_label">%s </span>%s</div>',
            esc_html($label),
            trim($return_string)
        );
    }

    return $return_string;
}
endif;

/**
 * Get the latest wpestate-studio post ID.
 *
 * @return int Post ID or 0 if none found.
 */
function wpestate_last_agent_id($post_type = 'estate_agent') {
    $query = new WP_Query([
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'ID',
        'order'          => 'DESC',
        'fields'         => 'ids',
    ]);

    return !empty($query->posts) ? (int) $query->posts[0] : 0;
}

/**
 * Generate an empty wrapper for displaying content.
 *
 * This function creates a div with a specific class and styles to wrap content.
 * If content is provided, it will be displayed inside the wrapper; otherwise,
 * a default message will be shown.
 *
 * @param string $content Content to display inside the wrapper.
 * @return string HTML string for the empty wrapper.
 */
function wpestate_generate_empty_wrapper( $content = '' ) {
    if ( ! empty( $content ) ) {
        return '<div class="wpestate_empty_wrapper" style="height: 100%; width: 100%; display: flex;">' .  $content . '</div>';
    } else {
        return '<div class="wpestate_empty_wrapper" style="min-height: 1px;height: 100%; width: 100%;"><span class="wpestate_empty_message"></span></div>';
    }
}