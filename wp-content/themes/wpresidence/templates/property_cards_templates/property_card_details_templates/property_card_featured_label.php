<?php
/**
 * Display featured property label using cached data.
 *
 * @param array $property_unit_cached_data Cached property data array.
 * @param int   $postID                    Property ID.
 */
$featured = intval(wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $postID, 'meta', 'prop_featured'));
?>

<div class="tag-wrapper">
    <?php if ($featured === 1) : ?>
        <div class="featured_div"><?php esc_html_e('Featured', 'wpresidence'); ?></div>
    <?php endif; ?>
</div>
