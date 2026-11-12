<?php
/**
 * Optimized snippet to display property action and status using cached data.
 *
 * @param array $property_unit_cached_data Cached property data array.
 * @param int   $postID                    Property ID.
 */
?>
<div class="status-wrapper">
    <?php
    $property_action_terms = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'terms', 'property_action_category');
    if (!empty($property_action_terms)) {
        // Get the first term (either object or array)
        $first_term = $property_action_terms[0];
        
        // Check if term is an object (WP_Term) or an array
        $property_action_term = is_object($first_term) ? esc_html($first_term->name) : wp_kses_post($first_term['name']);
        
        echo '<div class="action_tag_wrapper ' . wp_strip_all_tags($property_action_term) . '">' . wp_strip_all_tags($property_action_term) . '</div>';
    }

    // echo wpestate_return_property_status_from_cache($property_unit_cached_data, $postID, 'unit');
    ?>
</div>
