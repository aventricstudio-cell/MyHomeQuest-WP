<?php
/**MILLDONE
 * Featured Property Type 5 Display Template for WpResidence Theme
 * src: templates/featured_property_card_templates/featured_property_5.php
 * This template generates the HTML for displaying a featured property of type 5
 * in the WpResidence WordPress theme.
 *
 * @package WpResidence
 * @subpackage PropertyDisplay
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core functions (get_permalink, get_post_meta, get_the_title, get_the_excerpt)
 * - WpResidence theme functions (wpresidence_get_option, wpestate_return_agent_details,
 *   wpestate_get_converted_measure, wpestate_header_masonry_gallery, wpestate_show_price,
 *   wpestate_strip_excerpt_by_char)
 *
 * Usage:
 * This template is typically included within a loop or function that provides the $prop_id variable.
 * It displays a featured property with a masonry gallery, price, title, property details, and excerpt.
 */

// Retrieve property details
$link               = esc_url(get_permalink($prop_id));
$wpestate_currency  = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
$where_currency     = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
$realtor_details    = wpestate_return_agent_details($prop_id);
$property_size      = wpestate_get_converted_measure($prop_id, 'property_size');
$property_bedrooms  = get_post_meta($prop_id, 'property_bedrooms', true);
$property_bathrooms = get_post_meta($prop_id, 'property_bathrooms', true);

?>
<div class="featured_article_type2 featured_prop_type5">
    <?php wpestate_header_masonry_gallery($prop_id, 'listing_full_slider_1', 'listing_full_slider', 'yes'); ?>
    
    <div class="featured_gradient"></div>
    <div class="featured_article_type5_title_wrapper">
        <div class="featured_article_label">
            <?php wpestate_show_price($prop_id, $wpestate_currency, $where_currency); ?>
        </div>
        
        <a href="<?php echo esc_url($link); ?>">
            <h2><?php echo esc_html(get_the_title($prop_id)); ?></h2>
        </a>
        
        <div class="property_unit_type5_content_details_second_row">
            <?php
            if (!empty($property_bedrooms)) {
                echo '<div class="inforoom_unit_type5">' . esc_html($property_bedrooms) . ' ' . esc_html__('BD', 'wpresidence') . '</div>';
            }
            if (!empty($property_bathrooms)) {
                echo '<div class="inforoom_unit_type5">' . esc_html($property_bathrooms) . ' ' . esc_html__('BA', 'wpresidence') . '<span></span></div>';
            }
            if (!empty($property_size)) {
                echo '<div class="inforoom_unit_type5">' . wp_kses_post(trim($property_size)) . '</div>';
            }
            ?>
        </div>
        
        <div class="featured_type5_excerpt d-none d-md-block">
            <?php echo wpestate_strip_excerpt_by_char(get_the_excerpt($prop_id), 130, $prop_id, '...'); ?>
        </div>
        
        <div class="featured_read_more_5">
            <a href="<?php echo esc_url($link); ?>">
                <?php esc_html_e('discover more', 'wpresidence'); ?>
                <i class="fas fa-angle-right"></i>
            </a>
        </div>
    </div>
</div>