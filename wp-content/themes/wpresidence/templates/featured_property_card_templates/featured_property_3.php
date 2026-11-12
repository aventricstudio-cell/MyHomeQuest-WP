<?php
/** MILLDONE
 * Featured Property Template 3 for WpResidence Theme
 * src: templates/featured_property_card_templates/featured_property_3.php
 * This template generates the HTML for a featured property listing
 * in the WpResidence WordPress theme.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core functions (get_post_thumbnail_id, wp_get_attachment_image_src, etc.)
 * - WpResidence theme functions (wpestate_strip_words, wpestate_get_converted_measure, etc.)
 * - WpResidence theme options (wpresidence_get_option)
 *
 * Usage:
 * This template is typically called from within a widget or shortcode in the WpResidence theme.
 * It expects a $prop_id variable to be set with the ID of the property to display.
 */

// Start output buffering
ob_start();


// Set default preview image if not available
$property_featured_image_url = wpestate_get_property_featured_image($prop_id, 'property_listings');


// Get property details
$link = esc_url(get_permalink($prop_id));
$title = get_the_title($prop_id);
$price = floatval(get_post_meta($prop_id, 'property_price', true));
$price_label = esc_html(get_post_meta($prop_id, 'property_label', true));
$price_label_before = esc_html(get_post_meta($prop_id, 'property_label_before', true));
$wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
$where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));

$featured = intval(get_post_meta($prop_id, 'prop_featured', true));

$property_bathrooms = get_post_meta($prop_id, 'property_bathrooms', true);
$property_rooms = get_post_meta($prop_id, 'property_bedrooms', true);
$property_size = wpestate_get_converted_measure($prop_id, 'property_size');

// Format price
if ($price != 0) {
    $price = wpestate_show_price($prop_id, $wpestate_currency, $where_currency, 1);
} else {
    $price = '<span class="price_label price_label_before">' . $price_label_before . '</span><span class="price_label">' . $price_label . '</span>';
}

// Get favorite status
$current_user = wp_get_current_user();
$curent_fav = wpestate_return_favorite_listings_per_user();
$favorite_class = 'icon-fav-off';
$fav_mes = esc_html__('add to favorites', 'wpresidence');
if ($curent_fav && in_array($prop_id, $curent_fav)) {
    $favorite_class = 'icon-fav-on';
    $fav_mes = esc_html__('remove from favorites', 'wpresidence');
}

// Get realtor details
$realtor_details = wpestate_return_agent_details($prop_id);

// Prepare image content
$wpestate_property_unit_slider = 1; // Assuming this is defined elsewhere
if ($wpestate_property_unit_slider == 1) {
    $image_content = wpestate_generate_property_slider_content($prop_id, $link, $title, 'featured_property_3', true);
} else {
    $image_content = wpestate_featured_property_single_image($property_featured_image_url, $link);
}

// Get property status
$property_status = wpestate_return_property_status($prop_id);

// Get SVG icons
ob_start();
include(locate_template('templates/svg_icons/inforoom_unit_card_default.svg'));
$svg_rooms = ob_get_clean();

ob_start();
include(locate_template('templates/svg_icons/infobath_unit_card_default.svg'));
$svg_bathrooms = ob_get_clean();

ob_start();
include(locate_template('templates/svg_icons/infosize_unit_card_default.svg'));
$svg_size = ob_get_clean();

// Get share unit design
$share_unit = wpestate_share_unit_desing($prop_id);

// Get property excerpt
$excerpt = wpestate_strip_excerpt_by_char(get_the_excerpt($prop_id), 70, $prop_id);

?>





<div class="featured_property featured_property_type3 d-flex flex-column flex-sm-row ">
    <div class="featured_img col-12 col-lg-6">
        <a href="<?php echo esc_url($realtor_details['link']); ?>" class="featured_property_type3_agent" style="background-image:url(<?php echo esc_url($realtor_details['realtor_image']); ?>);"></a>
        
        <div class="tag-wrapper">
            <?php if ($featured == 1) : ?>
                <div class="featured_div"><?php esc_html_e('Featured', 'wpresidence'); ?></div>
            <?php endif; ?>
            <?php echo $property_status; ?>
        </div>

        <?php echo $image_content; ?>
    </div>

    <div class="featured_secondline col-12 col-lg-6" data-link="<?php echo esc_url($link); ?>">
        <h2>
            <a href="<?php echo esc_url($link); ?>">
                <?php echo mb_substr($title, 0, 27) . (mb_strlen($title) > 27 ? '...' : ''); ?>
            </a>
        </h2>

        <div class="featured_prop_price"><?php echo $price; ?></div>

        <div class="listing_details the_grid_view">
            <?php echo $excerpt; ?>
        </div>

        <div class="listing_actions">
            <?php echo $share_unit; ?>
            <span class="share_list" data-bs-toggle="tooltip" title="<?php esc_attr_e('share', 'wpresidence'); ?>"></span>
            <span class="icon-fav <?php echo esc_attr($favorite_class); ?>" 
            data-bs-toggle="tooltip" title="<?php echo esc_attr($fav_mes); ?>" data-postid="<?php echo intval($prop_id); ?>"></span>
        </div>

        <div class="property_listing_details">
            <?php if (!empty($property_rooms)) : ?>
                <span class="inforoom"><?php echo $svg_rooms . esc_html($property_rooms); ?></span>
            <?php endif; ?>

            <?php if (!empty($property_bathrooms)) : ?>
                <span class="infobath"><?php echo $svg_bathrooms . esc_html($property_bathrooms); ?></span>
            <?php endif; ?>

            <?php if (!empty($property_size)) : ?>
                <span class="infosize"><?php echo $svg_size . wp_kses_post($property_size); ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// End output buffering and clean it
ob_end_flush();
?>