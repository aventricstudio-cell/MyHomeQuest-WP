<?php
/** MILLDONE
 * WPEstate Property Slider 2 Slide Template
 * src: templates\property_shortcode_slider_templates\property_slider_shortcode1_v2.php 
 * This template file is used to display individual property slides in the property slider 2 design.
 *
 * @package WPEstate
 * @subpackage Templates
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core functions
 * - WPEstate theme functions (wpestate_return_property_status, wpestate_show_price, wpestate_strip_excerpt_by_char)
 *
 * Usage:
 * This template is included within the wpestate_design_property_slider_2 function.
 * It expects several variables to be set before inclusion, including $theid, $preview, $active, etc.
 */
?>
<div class="item <?php echo esc_attr($active); ?>" data-hash="item<?php echo esc_attr($counter); ?>" data-href="<?php echo esc_url(get_permalink($theid)); ?>">
    <div class="image_div" style="background-image:url(<?php echo esc_url($preview[0]); ?>);">
        <div class="featured_gradient"></div>
        <?php if ($featured == 1) : ?>
            <div class="featured_div"><?php esc_html_e('Featured', 'wpresidence-core'); ?></div>
        <?php endif; ?>
        <?php echo wpestate_return_property_status($theid); ?>
        <div class="featured_secondline">
            <?php if ($agent_id != '') : ?>
                <div class="agent_face" style="background-image:url(<?php echo esc_url($agent_face); ?>)"></div>
            <?php endif; ?>
            <a href="<?php echo esc_url(get_permalink($agent_id)); ?>"><?php echo esc_html(get_the_title($agent_id)); ?></a>
        </div>
    </div>

    <div class="property_slider2_info_wrapper">
        <div class="property_slider2_info_price">
            <?php echo wpestate_show_price($theid, $wpestate_currency, $where_currency, 1); ?>
        </div>
        <a href="<?php echo esc_url(get_permalink($theid)); ?>" target="_blank"><h2><?php echo esc_html(get_the_title($theid)); ?></h2></a>
        <div class="property_slider_sec_row">
            <?php if ($property_bedrooms != '') : ?>
                <div class="inforoom_unit_type5"><?php echo esc_html($property_bedrooms) . ' ' . esc_html__('BD', 'wpresidence-core'); ?></div>
            <?php endif; ?>
            <?php if ($property_bathrooms != '') : ?>
                <div class="inforoom_unit_type5"><?php echo esc_html($property_bathrooms) . ' ' . esc_html__('BA', 'wpresidence-core'); ?><span></span></div>
            <?php endif; ?>
            <?php if ($property_size != '') : ?>
                <div class="inforoom_unit_type5"><?php echo wp_kses_post(trim($property_size)); ?></div>
            <?php endif; ?>
        </div>
        <div class="property_slider2_content">
            <?php echo wpestate_strip_excerpt_by_char(get_the_excerpt($theid), 170, $theid); ?>
        </div>
    </div>
</div>