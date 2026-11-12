<?php
















/**
 * WpEstate Slider Slide Generation
 *
 * This function generates slider components for a property, using attachment IDs.
 * The first attachment ID is considered as the post thumbnail.
 *
 * @param int $prop_id The ID of the property post.
 * @param string $slider_size The size of the slider images.
 * @param string $use_captions_on_slide Whether to use captions on slides.
 * @return array An array of slider components.
 */
if(!function_exists('wpestate_slider_slide_generation')):
    function wpestate_slider_slide_generation($post_attachments, $slider_size, $use_captions_on_slide = '') {
        $has_video          = 0;
        $indicators         = '';
        $round_indicators   = '';
        $slides             = '';
        $captions           = '';
        $counter            = 0;
        $slider_components  = array('has_info' => 0);

        if (empty($post_attachments)) {
            return $slider_components;
        }

        $slider_components['has_info'] = 1;

        foreach ($post_attachments as $attachment_id) {
            // Check if the attachment is an image
            if (!wp_attachment_is_image($attachment_id)) {
                continue; // Skip this attachment if it's not an image
            }

            $counter++;
            $active = ($counter == 1 && $has_video != 1) ? " active " : " ";
            
            $slide_content = generate_slide_content($attachment_id, $slider_size, $counter, $active, $use_captions_on_slide);
            $indicators .= $slide_content['indicator'];
            $round_indicators .= $slide_content['round_indicator'];
            $slides .= $slide_content['slide'];
            $captions .= $slide_content['caption'];
        }

        $slider_components['indicators'] = $indicators;
        $slider_components['round_indicators'] = $round_indicators;
        $slider_components['slides'] = $slides;
        $slider_components['captions'] = $captions;

        return $slider_components;
    }
endif;










/**
 * Generate content for a single slide
 *
 * @param int $attachment_id The ID of the attachment.
 * @param string $slider_size The size of the slider image.
 * @param int $counter The current slide number.
 * @param string $active The active class if this is the active slide.
 * @param string $use_captions_on_slide Whether to use captions on slides.
 * @return array An array of slide components.
 */
function generate_slide_content($attachment_id, $slider_size, $counter, $active, $use_captions_on_slide) {
    $preview = wp_get_attachment_image_src($attachment_id, 'slider_thumb');
    $full_img = wp_get_attachment_image_src($attachment_id, $slider_size);
    $full_prty = wp_get_attachment_image_src($attachment_id, 'full');
    $attachment_meta = wp_get_attachment($attachment_id);

    $captions_on_slide = '';
    if ($attachment_meta['caption'] != '' && $use_captions_on_slide == 'yes') {
        $captions_on_slide = '<div class="caption_on_slide">' . $attachment_meta['caption'] . '</div>';
    }

    $indicator = ' <li data-slide-to="' . esc_attr($counter - 1) . '" class="' . esc_attr($active) . '">
                    <a href="#item' . esc_attr($counter) . '">'
                    . '<img src="' . esc_url($preview[0]) . '" alt="' . esc_html__('image', 'wpresidence') . '" /></a>
                </li>';

    $round_indicator = '<a href="#item' . esc_attr($counter) . '" data-slide-to="' . esc_attr($counter - 1) . '" class="' . $active . '"></a>';

    $slide = '<div class="item ' . esc_attr($active) . '" data-number="' . $counter . '">
                <a href="' . esc_url($full_prty[0]) . '" title="' . esc_attr($attachment_meta['caption']) . '"  class="prettygalery">
                    <img src="' . esc_url($full_img[0]) . '" data-slider-no="' . esc_attr($counter) . '" alt="' . esc_attr($attachment_meta['alt']) . '" class="img-responsive lightbox_trigger" />
                    ' . $captions_on_slide . '
                </a>
            </div>';

    $caption_class = $active;
    if (trim($attachment_meta['caption'] == '')) {
        $caption_class .= ' blank_caption ';
    }

    $caption = '<span data-slide-to="' . esc_attr($counter) . '" class="' . esc_attr($caption_class) . '"> ' . $attachment_meta['caption'] . '</span>';

    return array(
        'indicator' => $indicator,
        'round_indicator' => $round_indicator,
        'slide' => $slide,
        'caption' => $caption
    );
}
    




/**
 * WpResidence Property Status Display
 *
 * This function retrieves and formats the property status for display in various contexts.
 *
 * @package WpResidence
 * @subpackage PropertyFunctions
 * @version 1.0
 * 
 * @uses get_the_terms()
 * 
 * @param int    $post_id     The ID of the property post.
 * @param string $return_type The type of display format ('pin', 'verticalstatus', 'horizontalstatus', 'unit', or default).
 * @return string Formatted property status HTML or comma-separated list.
 */

 if (!function_exists('wpestate_return_property_status')):
    function wpestate_return_property_status($post_id, $return_type = '') {
        $property_status = get_the_terms($post_id, 'property_status');
        
        if (empty($property_status)) {
            return '';
        }

        $output = '';

        switch ($return_type) {
            case 'pin':
                $status_names = wp_list_pluck($property_status, 'name');
                $output = esc_html(implode(',', $status_names));
                break;

            case 'verticalstatus':
            case 'horizontalstatus':
                foreach ($property_status as $term) {
                    if ($term->slug !== 'normal') {
                        $ribbon_class = str_replace(' ', '-', $term->name);
                        $output .= sprintf(
                            '<div class="slider-property-status %1$s ribbon-wrapper-%2$s %2$s">%3$s</div>',
                            esc_attr($return_type),
                            esc_attr($ribbon_class),
                            esc_html($term->name)
                        );
                    }
                }
                $output = sprintf('<div class="status-wrapper %s">%s</div>', esc_attr($return_type), $output);
                break;

            case 'unit':
                foreach ($property_status as $term) {
                    if ($term->slug !== 'normal') {
                        $ribbon_class = str_replace(' ', '-', $term->name);
                        $output .= sprintf(
                            '<div class="ribbon-inside %s">%s</div>',
                            esc_attr($ribbon_class),
                            esc_html($term->name)
                        );
                    }
                }
                break;

            default:
                foreach ($property_status as $term) {
                    if ($term->slug !== 'normal') {
                        $ribbon_class = str_replace(' ', '-', $term->name);
                        $output .= sprintf(
                            '<div class="ribbon-wrapper-default ribbon-wrapper-%1$s"><div class="ribbon-inside %1$s">%2$s</div></div>',
                            esc_attr($ribbon_class),
                            esc_html($term->name)
                        );
                    }
                }
                $output = sprintf('<div class="status-wrapper">%s</div>', $output);
                break;
        }

        return $output;
    }
endif;

/**
 * WpResidence Property Status Display
 *
 * This function retrieves and formats the property status for display in various contexts.
 *
 * @package WpResidence
 * @subpackage PropertyFunctions
 * @version 1.0
 * 
 * @uses get_the_terms()
 * 
 * @param int    $post_id     The ID of the property post.
 * @param string $return_type The type of display format ('pin', 'verticalstatus', 'horizontalstatus', 'unit', or default).
 * @return string Formatted property status HTML or comma-separated list.
 */



 /**
  * Optimized function to display property status using cached data.
  *
  * @param array  $property_unit_cached_data Cached property data array.
  * @param int    $post_id                   Property ID.
  * @param string $return_type               The return type ('pin', 'unit', 'verticalstatus', 'horizontalstatus', or default).
  *
  * @return string HTML markup for property status.
  */
 if (!function_exists('wpestate_return_property_status_from_cache')):
  
    function wpestate_return_property_status_from_cache($property_unit_cached_data, $post_id, $return_type = '') {
        $property_status = wpestate_return_data_from_cache_if_exists($property_unit_cached_data, $post_id, 'terms', 'property_status');
    
        if (empty($property_status)) {
            return '';
        }
    
        $output = '';
    
        switch ($return_type) {
            case 'pin':
                $status_names = array_map(function($term) {
                    return is_object($term) ? esc_html($term->name) : esc_html($term['name']);
                }, $property_status);
                $output = implode(',', $status_names);
                break;
    
            case 'verticalstatus':
            case 'horizontalstatus':
                foreach ($property_status as $term) {
                    $term_name = is_object($term) ? $term->name : $term['name'];
                    $term_slug = is_object($term) ? $term->slug : $term['slug'];
                    
                    if ($term_slug !== 'normal') {
                        $ribbon_class = str_replace(' ', '-', $term_name);
                        $output .= sprintf(
                            '<div class="slider-property-status %1$s ribbon-wrapper-%2$s %2$s">%3$s</div>',
                            esc_attr($return_type),
                            esc_attr($ribbon_class),
                            esc_html($term_name)
                        );
                    }
                }
                $output = sprintf('<div class="status-wrapper %s">%s</div>', esc_attr($return_type), $output);
                break;
    
            case 'unit':
                foreach ($property_status as $term) {
                    $term_name = is_object($term) ? $term->name : wp_strip_all_tags($term['name']);
                    $term_slug = is_object($term) ? $term->slug : $term['slug'];
                    
                    if ($term_slug !== 'normal') {
                        $ribbon_class = str_replace(' ', '-', $term_name);
                        $output .= sprintf(
                            '<div class="ribbon-inside %s">%s</div>',
                            esc_attr($ribbon_class),
                            esc_html($term_name)
                        );
                    }
                }
                break;
    
            default:
                foreach ($property_status as $term) {
                    $term_name = is_object($term) ? $term->name : $term['name'];
                    $term_slug = is_object($term) ? $term->slug : $term['slug'];
                    
                    if ($term_slug !== 'normal') {
                        $ribbon_class = str_replace(' ', '-', $term_name);
                        $output .= sprintf(
                            '<div class="ribbon-wrapper-default ribbon-wrapper-%1$s"><div class="ribbon-inside %1$s">%2$s</div></div>',
                            esc_attr($ribbon_class),
                            esc_html($term_name)
                        );
                    }
                }
                $output = sprintf('<div class="status-wrapper">%s</div>', $output);
                break;
        }
    
        return $output;
    }

 endif;




/**
 * WpResidence Control Media Buttons
 *
 * This function generates the HTML for media control buttons on property pages.
 *
 * @package WpResidence
 * @subpackage PropertyFunctions
 * @version 1.0
 * 
 * @uses wpresidence_get_option()
 * @uses get_post_meta()
 * @uses wpresidence_return_class_leaflet()
 * 
 * @param int $postID The ID of the property post.
 * @return string HTML for media control buttons.
 */

 if (!function_exists('wpestate_control_media_buttons')):
    function wpestate_control_media_buttons($postID) {
        $wp_estate_media_buttons_order_items = wpresidence_get_option('wp_estate_media_buttons_order_items', '');
        
        if (empty($wp_estate_media_buttons_order_items['enabled']) || count($wp_estate_media_buttons_order_items['enabled']) <= 1) {
            return '';
        }

        unset($wp_estate_media_buttons_order_items['enabled']['placebo']);
        unset($wp_estate_media_buttons_order_items['enabled'][0]);

        $output = '<div class="wpestate_control_media_buttons_wrapper">';
        $first_class = "slideron";

        $button_config = [
            'image' => [
                'id' => 'slider_enable_slider',
                'data_show' => 'wpestate_property_carousel',
                'title' => esc_attr__('Image Gallery', 'wpresidence'),
                'icon' => 'far fa-image',
                'condition' => true
            ],
            'map' => [
                'id' => 'slider_enable_map',
                'data_show' => 'google_map_slider_wrapper',
                'title' => esc_attr__('Map', 'wpresidence'),
                'icon' => 'fas fa-map-marker-alt',
                'condition' => true
            ],
            'street' => [
                'id' => 'slider_enable_street',
                'data_show' => 'google_map_slider_wrapper',
                'title' => esc_attr__('Street View', 'wpresidence'),
                'icon' => 'fas fa-location-arrow',
                'condition' => get_post_meta($postID, 'property_google_view', true) == 1,
                'extra_class' => wpresidence_return_class_leaflet()
            ],
            'video' => [
                'id' => 'slider_enable_video',
                'data_show' => 'wpestate_slider_enable_video_wrapper',
                'title' => esc_attr__('Video', 'wpresidence'),
                'icon' => 'fas fa-video',
                'condition' => get_post_meta($postID, 'embed_video_id', true) != ''
            ],
            'virtual_tour' => [
                'id' => 'slider_enable_virtual',
                'data_show' => 'wpestate_slider_enable_virtual_wrapper',
                'title' => esc_attr__('Virtual Tour', 'wpresidence'),
                'icon' => 'fas fa-photo-video',
                'condition' => get_post_meta($postID, 'embed_virtual_tour', true) != ''
            ]
        ];

        foreach ($wp_estate_media_buttons_order_items['enabled'] as $key => $value) {
            if (isset($button_config[$key]) && $button_config[$key]['condition']) {
                $config = $button_config[$key];
                $extra_class = isset($config['extra_class']) ? ' ' . $config['extra_class'] : '';
                
                $output .= sprintf(
                    '<div id="%s" data-show="%s" data-bs-placement="bottom" data-bs-toggle="tooltip" title="%s" class="wpestate_control_media_button %s%s"><i class="%s"></i></div>',
                    esc_attr($config['id']),
                    esc_attr($config['data_show']),
                    esc_attr($config['title']),
                    esc_attr($first_class),
                    esc_attr($extra_class),
                    esc_attr($config['icon'])
                );

                $first_class = '';
            }
        }

        $output .= '</div>';
        return $output;
    }
endif;
    
    