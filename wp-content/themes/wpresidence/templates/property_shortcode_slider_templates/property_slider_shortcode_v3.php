<?php
/**
 * WPEstate Property Slider Shortcode V3 Template
 *
 * This template file is used to display individual property slides in the property slider shortcode v3.
 *
 * @package WPEstate
 * @subpackage Templates
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core functions
 * - WPEstate theme functions (wpestate_get_converted_measure, wpestate_show_price, wpestate_strip_excerpt_by_char, wpestate_return_property_status)
 *
 * Usage:
 * This template is included within the wpestate_slider_properties_v3 function.
 * It expects several variables to be set before inclusion, including $prop_id, $counter, $active, etc.
 */

// Retrieve property details
$title = get_the_title($prop_id);
$link = get_permalink($prop_id);
$property_bathrooms = floatval(get_post_meta($prop_id, 'property_bathrooms', true));
$property_rooms = floatval(get_post_meta($prop_id, 'property_bedrooms', true));
$property_size = wpestate_get_converted_measure($prop_id, 'property_size');
$price = floatval(get_post_meta($prop_id, 'property_price', true));
$price_label = '<span class="price_label">' . esc_html(get_post_meta($prop_id, 'property_label', true)) . '</span>';
$price_label_before = '<span class="price_label price_label_before">' . esc_html(get_post_meta($prop_id, 'property_label_before', true)) . '</span>';

// Format the price
$price = ($price != 0) ? wpestate_show_price($prop_id, $wpestate_currency, $where_currency, 1) : $price_label_before . $price_label;

// Get the featured image
$property_featured_image_url = wpestate_get_property_featured_image($prop_id, 'property_featured_sidebar');


// Get property address and featured status
$property_address = get_post_meta($prop_id, 'property_address', true);
$featured = intval(get_post_meta($prop_id, 'prop_featured', true));

// Get agent details
$agent_id = intval(get_post_meta($prop_id, 'property_agent', true));
$agent_face_image = '';
if ($agent_id != 0) {
    $thumb_id = get_post_thumbnail_id($agent_id);
    $agent_face = wp_get_attachment_image_src($thumb_id, 'agent_picture_thumb');
    $agent_face_image = $agent_face ? $agent_face[0] : '';
}

// Fallback to author details if no agent is set
if ($agent_id == 0 || empty($agent_face_image)) {
    $author_id = get_post_field('post_author', $prop_id);
    $agent_face_image = get_the_author_meta('custom_picture', $author_id);
    if (empty($agent_face_image)) {
        $agent_face_image = get_theme_file_uri('/img/default-user_1.png');
    }
}

// Generate the HTML for the property slide
?>
<div class="item <?php echo esc_attr($active); ?>" data-number="<?php echo esc_attr($counter); ?>">
    <div class="property_slider_carousel_elementor_v3_image_wrapper">
        <div class="tag-wrapper">
            <?php if ($featured == 1) : ?>
                <div class="featured_div"><?php esc_html_e('Featured', 'wpresidence'); ?></div>
            <?php endif; ?>
            
            <div class="status-wrapper">
                <?php
                $property_action = get_the_terms($prop_id, 'property_action_category');
                if (isset($property_action[0])) {
                    $property_action_term = $property_action[0]->name;
                    echo '<div class="action_tag_wrapper ' . esc_attr($property_action_term) . '">' . wp_kses_post($property_action_term) . '</div>';
                }
                echo wpestate_return_property_status($prop_id, 'unit');
                ?>
            </div>
        </div>

        <div class="places_cover"></div>
        
        <div class="property_slider_carousel_elementor_v3_image_container" style="background-image:url('<?php echo esc_url($property_featured_image_url); ?>')">
        </div> 
    </div>

    <div class="property_slider_carousel_elementor_v3_content_wrapper">
        <div class="property_slider_carousel_elementor_v3_price">
            <?php echo wp_kses_post($price); ?>
        </div>
        
        <a href="<?php echo esc_url($link); ?>" class="property_slider_carousel_elementor_v3_title">
            <?php echo esc_html($title); ?>
        </a>
      
        <div class="property_slider_carousel_elementor_v3_address">
            <?php echo esc_html($property_address); ?>
        </div>

        <div class="property_slider_carousel_elementor_v3_excerpt">   
            <?php echo wpestate_strip_excerpt_by_char(get_the_excerpt(), 210, $prop_id); ?>
        </div>

        <div class="property_listing_details">
            <?php if ($property_rooms != '' && $property_rooms != 0) : ?>
                <span class="inforoom">
                    <?php include(locate_template('templates/svg_icons/single_bedrooms.svg')); ?>
                    <?php echo esc_html($property_rooms); ?>
                </span>
            <?php endif; ?>

            <?php if ($property_bathrooms != '' && $property_bathrooms != 0) : ?>
                <span class="infobath">
                    <?php include(locate_template('templates/svg_icons/single_bath.svg')); ?>
                    <?php echo esc_html($property_bathrooms); ?>
                </span>
            <?php endif; ?>

            <?php if ($property_size != '') : ?>
                <span class="infosize">
                    <?php include(locate_template('templates/svg_icons/single_floor_plan.svg')); ?>
                    <?php echo wp_kses_post(trim($property_size)); ?>
                </span>
            <?php endif; ?>
        </div>

        <div class="property_agent_wrapper">
            <div class="property_agent_image" style="background-image:url('<?php echo esc_url($agent_face_image); ?>')"></div> 
            <div class="property_agent_image_sign"><i class="far fa-user-circle"></i></div>
            <?php
            if ($agent_id != 0) {
                echo '<a href="' . esc_url(get_permalink($agent_id)) . '">' . esc_html(get_the_title($agent_id)) . '</a>';
            } else {
                echo esc_html(get_the_author_meta('first_name', $author_id) . ' ' . get_the_author_meta('last_name', $author_id));
            }
            ?>
        </div>
    </div>
</div>