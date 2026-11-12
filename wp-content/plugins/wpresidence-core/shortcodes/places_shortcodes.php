<?php
/**
 * WPResidence Theme - Places/Categories Slider Function
 *
 * This file contains a function for displaying places or categories in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage Widgets
 * @since 1.0.0
 * 
 * @dependencies 
 * - WordPress core functions
 * - WPResidence core functions
 * - Slick slider JavaScript library
 */




 if (!function_exists('wpestate_places_slider')) :
    /**
     * Display Places/Categories Slider
     *
     * This function generates a slider for displaying places or categories.
     *
     * @param array $attributes Shortcode attributes
     * @param string|null $content Shortcode content (unused in this function)
     * @return string HTML markup for the slider
     */
    function wpestate_places_slider($attributes, $content = null) {
        // Set default attributes and merge with provided attributes
        $attributes = shortcode_atts(
            array(
                'place_list'        => '',
                'place_per_row'     => 3,
                'extra_class_name'  => '',
            ), 
            $attributes
        );

        // Enqueue necessary scripts
        wp_enqueue_script('slick.min');

        // Extract attributes
        $place_list = $attributes['place_list'];
        $place_per_row = intval($attributes['place_per_row']);
        $extra_class_name = esc_attr($attributes['extra_class_name']);

        // Generate slider content
        $slide_cont = '';
        $all_places_array = explode(',', $place_list);


        // reset classes for columns
        $places_class['col_class']='';
        $places_class['col_org']='';

        foreach ($all_places_array as $single_term) {
            $place_id = intval($single_term);
            ob_start();
            include(locate_template('templates/places_card_templates/places_unit_type2.php'));
            $slide_cont_tmp = ob_get_clean();
            if (!empty(trim($slide_cont_tmp))) {
                $slide_cont .= sprintf(
                    '<div class="single_slide_container">%s</div>',
                    $slide_cont_tmp
                );
            }
        }

        // Construct the slider container
        $slider_html = sprintf(
            '<div class="estate_places_slider %s" data-items-per-row="%d" data-auto="0">%s</div>',
            $extra_class_name,
            $place_per_row,
            $slide_cont
        );

        // Add initialization script
        $slider_html .= '<script type="text/javascript">
            jQuery(document).ready(function(){
                wpestate_enable_slick_places();
            });
        </script>';

        return $slider_html;
    }
endif;


/**
 * WPResidence Theme - Places/Categories List Function
 *
 * This file contains a function for displaying a list of places or categories in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage Widgets
 * @since 1.0.0
 * 
 * @dependencies 
 * - WordPress core functions
 * - WPResidence core functions (wpresidence_display_places_cateogories_list_as_html)
 */

if (!function_exists('wpestate_places_list_function')) :
    /**
     * Display Places/Categories List
     *
     * This function generates a list of places or categories based on provided attributes.
     *
     * @param array $attributes Shortcode attributes
     * @param string|null $content Shortcode content (unused in this function)
     * @return string HTML markup for the places/categories list
     */
    function wpestate_places_list_function($attributes, $content = null) {
        // Set default attributes and merge with provided attributes
        $attributes = shortcode_atts(
            array(
                'place_list'        => '',
                'item_height'       => '',
                'place_per_row'     => 4,
                'extra_class_name'  => '',
                'place_type'        => 1,
                'display_grid'      => 'no'
            ), 
            $attributes
        );

        // Parse place list into array
        $all_places_array = explode(',', $attributes['place_list']);

        // Determine wrapper class based on display_grid attribute
        $wrap_class = ($attributes['display_grid'] === 'yes') ? 'items_shortcode_wrapper_grid' : 'row m-0';

        // Generate places/categories list
        ob_start();
        if(function_exists('wpresidence_display_places_cateogories_list_as_html')):
            wpresidence_display_places_cateogories_list_as_html($all_places_array, array(), 'shortcode', $attributes);
        endif;
        $list_content = ob_get_clean();

        // Construct the final HTML
        $return_string = sprintf(
            '<div class="wpresidence_shortcode_listings_wrapper %s">%s</div>',
            esc_attr($wrap_class),
            $list_content
        );

        return $return_string;
    }
endif;




/**
 * WPResidence Theme - Places/Categories as Tabs Functions
 *
 * This file contains functions for displaying places/categories as tabs in the WPResidence theme.
 * It includes two main functions:
 * 1. wpestate_places_list_functionas_tabs() - Generates the main tab structure
 * 2. wpestate_show_tax_items() - Displays individual taxonomy items within each tab
 *
 * @package WPResidence
 * @subpackage Widgets
 * @since 1.0.0
 * 
 * @dependencies Elementor (for icon rendering), Bootstrap 5.3 (for tab functionality)
 */

if (!function_exists('wpestate_places_list_functionas_tabs')):
    /**
     * Display Places/Categories as Tabs
     *
     * This function generates a tabbed interface for displaying places or categories.
     *
     * @param array $attributes Shortcode attributes
     * @param string|null $content Shortcode content (unused in this function)
     * @return string HTML markup for the tabbed interface
     */
    function wpestate_places_list_functionas_tabs($attributes, $content = null) {
        // Define default attributes and merge with provided attributes
        $attributes = shortcode_atts(
            array(
                'form_fields'     => '',
                'place_per_row'   => 4,
                'show_zero_terms' => true
            ), 
            $attributes
        );

        // Determine number of columns based on places per row
        $row_number = isset($attributes['place_per_row']) ? min(6, intval($attributes['place_per_row'])) : 4;
        $row_number_col = 12 / max(1, $row_number); // Calculate column width

        // Adjust column width for vertical alignment
        if ($row_number == 1 && isset($attributes['align']) && $attributes['align'] == 'vertical') {
            $row_number_col = 0;
        }

        $all_places_array = $attributes['form_fields'];
        $tab_items = '<ul class="nav nav-tabs wpestate_categories_as_tabs_ul" role="tablist">';
        $tab_content = '<div class="tab-content">';
        $return_string = '<div role="tabpanel" class="wpestate_categories_as_tabs_wrapper">';
        $class_active = 'active';

        if (is_array($all_places_array)):
            foreach ($all_places_array as $key => $place_tax) {
                $tab_id = sanitize_title(trim($place_tax['field_type']));

                // Generate tab item
                $tab_items .= '<li role="presentation" class="wpestate_categories_as_tabs_item nav-item">';
                $item_icon = '';
                if (!empty($place_tax['icon'])) {
                    ob_start();
                    \Elementor\Icons_Manager::render_icon($place_tax['icon'], ['aria-hidden' => 'true']);
                    $item_icon = ob_get_clean();
                }
                $tab_items .= sprintf(
                    '<a class="nav-link %s" href="#%s" role="tab" data-bs-toggle="tab" aria-controls="%s" aria-selected="%s">%s%s</a>',
                    esc_attr($class_active),
                    esc_attr($tab_id),
                    esc_attr($tab_id),
                    $class_active ? 'true' : 'false',
                    $item_icon,
                    esc_html($place_tax['field_label'])
                );
                $tab_items .= '</li>';

                // Generate tab content
                $tab_content .= sprintf(
                    '<div role="tabpanel" class="wpestate_categories_as_tabs_panel  tab-pane fade %s" id="%s" aria-labelledby="%s-tab">%s</div>',
                    esc_attr($class_active ? 'show active' : ''),
                    esc_attr($tab_id),
                    esc_attr($tab_id),
                    wpestate_show_tax_items($place_tax['field_type'], $row_number_col, $attributes['show_zero_terms'])
                );

                $class_active = ''; // Only first tab is active
            }
        endif;

        $tab_items .= '</ul>';
        $tab_content .= '</div>';
        $return_string .= $tab_items . $tab_content . '</div>';

        return $return_string;
    }
endif;

if (!function_exists('wpestate_show_tax_items')):
    /**
     * Display Taxonomy Items
     *
     * This function generates HTML for displaying individual taxonomy items.
     *
     * @param string $taxonomy The taxonomy slug
     * @param string $row_number_col Column width for each item
     * @param bool $show_zero Whether to show terms with zero posts
     * @return string HTML markup for taxonomy items
     */
    function wpestate_show_tax_items($taxonomy, $row_number_col = "4", $show_zero = true) {
        $return_string = '<div class="row">';

        $terms = wpestate_get_cached_terms ($taxonomy ,array(
            'hide_empty' => !$show_zero,
        ));

        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $return_string .= sprintf(
                    '<div class="col-md-%s"><a class="wpestate_categories_as_tabs_term" href="%s">%s</a> <span class="places_list_tab_term-count">%d %s</span></div>',
                    esc_attr($row_number_col),
                    esc_url(get_term_link($term)),
                    esc_html($term->name),
                    $term->count,
                    esc_html__('properties', 'wpresidence-core')
                );
            }
        }
        $return_string .= '</div>';
        return $return_string;
    }
endif;