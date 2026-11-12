<?php
/**
 * Template for displaying property card actions (Type 2)
 * src: templates\property_cards_templates\property_card_details_templates\property_card_actions_type2.php
 * This template renders action buttons (share and compare) using cached data.
 */
?>
<div class="listing_actions">
    <?php 
    // Display share button if enabled
    if (wpresidence_get_option('property_card_agent_show_share', '') == 'yes') {
        echo wpestate_share_unit_desing_from_cache($property_unit_cached_data,$postID);

        // Get property thumbnail from cache or fallback
        $compare_image = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'featured_media', 'slider_thumb');
        ?>
        <span class="share_list" data-bs-toggle="tooltip" title="<?php esc_attr_e('share', 'wpresidence'); ?>"></span>
    <?php 
    }

    // Display compare button if enabled
    if (wpresidence_get_option('property_card_agent_show_compare', '') == 'yes') {
        // Ensure compare image URL is available
        $compare_image = !empty($compare_image) ? esc_attr($compare_image) : '';
        ?>
        <span class="compare-action" 
            data-bs-toggle="tooltip"  
            title="<?php esc_attr_e('compare', 'wpresidence'); ?>" 
            data-pimage="<?php echo $compare_image; ?>" 
            data-pid="<?php echo intval($postID); ?>">
        </span>
    <?php 
    }
    ?>
</div>
