<?php


if(!function_exists('wpestate_generate_currency_dropdown')):
    /**
     * Generates the HTML for the currency dropdown menu.
     *
     * @return string The HTML for the currency dropdown menu.
     */
    function wpestate_generate_currency_dropdown() {

        // Retrieve currency options
        $multiple_cur   = wpresidence_get_option('wpestate_currency', array());
        $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
        $normal_cur     = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
        $normal_label   = wpresidence_get_option('wp_estate_currency_label_main', '');

        // Prepare dropdown options
        $dropdown_options = '<li role="presentation" data-curpos="' . esc_attr($where_currency) . '" data-coef="1" data-value="' . esc_attr($normal_label) . '" data-symbol="' . esc_attr($normal_label) . '" data-pos="-1">' . esc_html($normal_label) . '</li>';

        if (!empty($multiple_cur)) {  
            foreach ($multiple_cur as $i => $cur) {
                $dropdown_options .= '<li role="presentation" data-curpos="' . esc_attr($cur[3]) . '" data-coef="' . esc_attr($cur[2]) . '" data-value="' . esc_attr($cur[1]) . '" data-symbol="' . esc_attr($cur[0]) . '" data-pos="' . esc_attr($i) . '">' . esc_html($cur[1]) . '</li>';
            }
        }

        // Determine initial dropdown value
        $initial_value = isset($_COOKIE['my_custom_curr']) ? sanitize_text_field($_COOKIE['my_custom_curr']) : $normal_label;
        $hidden_input_name = 'filter_curr[]';
        $ul_list_class = 'list_sidebar_currency';

        // Generate dropdown HTML
        ob_start();
        if (function_exists('wpresidence_render_single_dropdown')) {
            wpresidence_render_single_dropdown(
                'currency',
                'sidebar_currency_list',
                $initial_value,
                $initial_value,
                '',
                $hidden_input_name ,
                $ul_list_class,
                $dropdown_options
            );
        }
        $dropdown_html = ob_get_clean();

        // Add hidden input for AJAX nonce
        $ajax_nonce = wp_create_nonce("wpestate_change_currency");
        $dropdown_html .= '<input type="hidden" id="wpestate_change_currency" value="' . esc_attr($ajax_nonce) . '" />';

        return $dropdown_html;




	}
endif;    




if(!function_exists('wpestate_generate_measure_unit_dropdown')):

/**
 * Generates the HTML for the measure unit dropdown menu.
 *
 * @return string The HTML for the measure unit dropdown menu.
 */
    function wpestate_generate_measure_unit_dropdown() {

        // Define the array of measurement units
        $measure_array = array(         
            array('name' => esc_html__('feet', 'wpresidence-core'), 'unit' => esc_html__('ft', 'wpresidence-core'), 'unit2' => 'ft', 'is_square' => 0),
            array('name' => esc_html__('meters', 'wpresidence-core'), 'unit' => esc_html__('m', 'wpresidence-core'), 'unit2' => 'm', 'is_square' => 0),
            array('name' => esc_html__('acres', 'wpresidence-core'), 'unit' => esc_html__('ac', 'wpresidence-core'), 'unit2' => 'ac', 'is_square' => 1),
            array('name' => esc_html__('yards', 'wpresidence-core'), 'unit' => esc_html__('yd', 'wpresidence-core'), 'unit2' => 'yd', 'is_square' => 0),
            array('name' => esc_html__('hectares', 'wpresidence-core'), 'unit' => esc_html__('ha', 'wpresidence-core'), 'is_square' => 1),
        );

        $selected_measure_unit = esc_html(wpresidence_get_option('wp_estate_measure_sys', ''));

        // Prepare dropdown options
        $dropdown_options = '';
        foreach ($measure_array as $single_unit) {
            $option_text = $single_unit['is_square'] === 1 
                ? $single_unit['name'] . ' - ' . $single_unit['unit']
                : esc_html__('square', 'wpresidence-core') . ' ' . $single_unit['name'] . ' - ' . $single_unit['unit'] . '<sup>2</sup>';
            $dropdown_options .= '<li role="presentation" data-value="' . esc_attr($single_unit['unit']) . '">' . $option_text . '</li>';
        }

        // Determine the initial label and value
        $initial_label = '';
        $initial_value = isset($_COOKIE['my_measure_unit']) ? sanitize_text_field($_COOKIE['my_measure_unit']) : $selected_measure_unit;

        foreach ($measure_array as $single_unit) {
            if ($initial_value === $single_unit['unit']) {
                $initial_label = $single_unit['is_square'] === 1 
                    ? $single_unit['name'] . ' - ' . $single_unit['unit']
                    : esc_html__('square', 'wpresidence-core') . ' ' . $single_unit['name'] . ' - ' . $single_unit['unit'] . '<sup>2</sup>';
                break;
            }
        }

        // Generate dropdown HTML
        ob_start();
        if (function_exists('wpresidence_render_single_dropdown')) {
            wpresidence_render_single_dropdown(
                'measure_unit',
                'sidebar_measure_unit_list',
                $initial_label,
                $initial_value,
                '',
                'filter_curr[]',
                'list_sidebar_measure_unit',
                $dropdown_options
            );
        }
        $dropdown_html = ob_get_clean();

        // Add hidden input for AJAX nonce
        $ajax_nonce = wp_create_nonce("wpestate_change_measure");
        $dropdown_html .= '<input type="hidden" id="wpestate_change_measure" value="' . esc_attr($ajax_nonce) . '" />';

        return $dropdown_html;



    }
endif;







// Check if the function wpestate_render_languages_dropdown is not already defined to avoid redeclaration errors.
if (!function_exists('wpestate_render_languages_dropdown')) {

    /**
     * Renders the language dropdown menu.
     *
     * This function retrieves the current language, flag, and available languages,
     * and outputs the HTML structure for the language dropdown menu.
     */
    function wpestate_render_languages_dropdown() {
        list($current_lang, $flag, $languages) = wpestate_get_languages();

        ?>
        <div class="wpestate_language_drop_wrapper">
            <a class="btn dropdown-toggle" href="#" role="button" id="languageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="wpestate-icon wpestate-icon-earth-1"></i>
                <?php echo $current_lang; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-left vertical-dropdown" aria-labelledby="languageDropdown">
                <?php echo wpestate_generate_language_dropdown($languages); ?>
            </div>
        </div>
        <?php
    }
}

// Check if the function wpestate_get_languages is not already defined to avoid redeclaration errors.
if (!function_exists('wpestate_get_languages')) {

    /**
     * Retrieves the current language, flag, and available languages.
     *
     * This function checks if Polylang or WPML is available and uses them to fetch
     * the current language, flag, and available languages. It returns these values
     * as an array.
     *
     * @return array Contains the current language, flag, and languages.
     */
    function wpestate_get_languages() {
        $current_lang = esc_html__('Languages','wpresidence');
        $flag = null;
        $languages = null;

        // Check if Polylang is available and retrieve languages.
        if (function_exists('pll_the_languages')) {
            $languages = pll_the_languages(array('raw' => 1));
            foreach ($languages as $lang) {
                if ($lang['current_lang']) {
                    $flag = '<i class="image-icon"><img src="' . $lang['flag'] . '" alt="' . $lang['name'] . '"/></i>';
                    $current_lang = $lang['name'];
                }
            }
        } elseif (function_exists('icl_get_languages')) {
            // Check if WPML is available and retrieve languages.
            $languages = icl_get_languages();
            foreach ($languages as $lang) {
                if ($lang['active']) {
                    $flag = '<i class="image-icon"><img src="' . $lang['country_flag_url'] . '" alt="' . $lang['native_name'] . '"/></i>';
                    $current_lang = $lang['native_name'];
                }
            }
        }

        return array($current_lang, $flag, $languages);
    }
}

// Check if the function wpestate_generate_language_dropdown is not already defined to avoid redeclaration errors.
if (!function_exists('wpestate_generate_language_dropdown')) {

    /**
     * Generates the HTML for the language dropdown menu.
     *
     * This function takes the languages array and generates the HTML for the
     * language dropdown menu, including the appropriate flags and language names.
     *
     * @param array $languages The array of languages.
     * @return string The HTML for the language dropdown menu.
     */
    function wpestate_generate_language_dropdown($languages) {
        $dropdown_html = '';

        // Check if Polylang is available and generate dropdown items.
        if ($languages && function_exists('pll_the_languages')) {
            foreach ($languages as $lang) {
                $current = $lang['current_lang'] ? 'class="active"' : '';
                $dropdown_html .= '<a class="dropdown-item" href="' . $lang['url'] . '" hreflang="' . $lang['slug'] . '"><i class="icon-image"><img src="' . $lang['flag'] . '" alt="' . $lang['name'] . '"/></i> ' . $lang['name'] . '</a>';
            }
        } elseif ($languages && function_exists('icl_get_languages')) {
            // Check if WPML is available and generate dropdown items.
            foreach ($languages as $lang) {
                $dropdown_html .= '<a class="dropdown-item" href="' . $lang['url'] . '" hreflang="' . $lang['language_code'] . '"><i class="icon-image"><img src="' . $lang['country_flag_url'] . '" alt="' . $lang['native_name'] . '"/></i> ' . $lang['native_name'] . '</a>';
            }
        } else {
            // Display message if neither Polylang nor WPML is available.
            $dropdown_html = '<a class="dropdown-item">You need Polylang or WPML plugin for this to work</a>';
        }

        return $dropdown_html;
    }
}




if(!function_exists('wpestate_get_social_icons_widgets_elementor')):
    // Function to generate the social icons HTML
    function wpestate_get_social_icons_widgets_elementor($instance) {
    // Array of default social media icons
    $defaults = array( 
        'facebook'      => '<i class="fab fa-facebook-f"></i>',
        'whatsup'       => '<i class="fab fa-whatsapp"></i>',
        'telegram'      => '<i class="fab fa-telegram-plane"></i>',
        'tiktok'        => '<i class="fab fa-tiktok"></i>',
        'rss'           => '<i class="fas fa-rss fa-fw"></i>',
        'twitter'       => '<i class="fa-brands fa-x-twitter"></i>',
        'dribbble'      => '<i class="fab fa-dribbble  fa-fw"></i>',
        'google'        => '<i class="fab fa-google"></i>',
        'linkedIn'      => '<i class="fab fa-linkedin-in"></i>',
        'tumblr'        => '<i class="fab fa-tumblr  fa-fw"></i>',
        'pinterest'     => '<i class="fab fa-pinterest-p  fa-fw"></i>',
        'youtube'       => '<i class="fab fa-youtube  fa-fw"></i>',
        'vimeo'         => '<i class="fab fa-vimeo-v  fa-fw"></i>',
        'instagram'     => '<i class="fab fa-instagram  fa-fw"></i>',
        'foursquare'    => '<i class="fab  fa-foursquare  fa-fw"></i>',
        'line'          => '<i class="fab fa-line"></i>',
        'wechat'        => '<i class="fab fa-weixin"></i>',
    );

    // Initialize the display variable with a wrapper div
    $display = '<div class="wpresidence_elementor_social_sidebar_internal social_sidebar_internal">';
    $items='';
    // Loop through each default social media icon
    foreach ($defaults as $key => $value) {
        // Check if the instance has a URL for this social media icon
        if (isset($instance[$key]) && $instance[$key]) {
            // Add the icon with the URL to the display variable
            $items .= '<a href="'.esc_url($instance[$key]).'" target="_blank" aria-label="'.esc_html($key).'" >'.trim($value).'</a>';
        }
    }

    if(trim($items)==''){
        $items .=esc_html('There are no social links','wpresidence');
    }

    // Close the wrapper div
    $display .= $items.'</div>';

    // Return the generated HTML
    return $display;
    }

endif;

if ( !function_exists( 'wpestate_display_categories_list' ) )   {
    /**
     * Generates a list of categories for a widget.
     *
     * @param array $attributes Optional attributes for the widget.
     * @return string HTML output of the categories list.
     */
    function wpestate_display_categories_list( $attributes = array() ) {
        

        extract( shortcode_atts( array(
            'taxonomy'     => 'property_category',
            'show_child'   => 'yes',
            'show_count'   => 'yes',
            'title'        => '',
            'icon'         => '',
            'show_icon'    => 'no',
        ), $attributes ) );
        
        $taxonomy = sanitize_text_field($taxonomy);
        $show_child = sanitize_text_field($show_child);
        $show_count = sanitize_text_field($show_count);

        $categories = wpestate_get_cached_terms(
            $taxonomy,
            array('parent' => 0)
        );
        
        $output = '<div class="category_list_widget">';
        if ( !empty($title) ) {
            $output .= '<h3 class="widget-title">' . esc_html($title) . '</h3>';
        }
        $output .= wpestate_build_category_list(
            $categories,
            $taxonomy,
            $show_child,
            $show_count,
            $show_icon === 'yes' ? esc_html($icon) : ''
        );
        $output .= '</div>';
        return $output;
    }
}

if ( !function_exists( 'wpestate_build_category_list' ) ) {
    /**
     * Builds the category list for a widget.
     *
     * @param array $attributes Optional attributes for the widget.
     * @return string HTML output of the category list.
     */
    function wpestate_build_category_list( $items, $taxonomy, $show_child, $show_count, $icon ) {

        if ( !is_array( $items ) || empty( $items ) ) {
            return '';
        }
        
        $return_string = '';
        if ( $show_child == 'yes' ) {
            $return_string = '<ul>';
        } else {
            $return_string = '<ul class="child_category">';
        }

        foreach ( $items as $item ) {
            if ( !empty($icon) ) {
                $return_string .= '<li class="category-icon"><i class="' . esc_attr($icon) . '"></i>';
            } else {
                $return_string .= '<li>';
            }
            $return_string .= '<a href="' . esc_url( get_term_link( $item->term_id, $taxonomy ) ) . '">' . esc_html( $item->name ) . '</a>';
            if ( $show_count == 'yes' ) {
                $return_string .= '<span class="category_no">(' . esc_attr($item->count) . ')</span>';
            }
            if ($show_child == 'yes') {
                // Get child terms using the cached terms function
                $child_categories = wpestate_get_cached_terms($taxonomy, array('parent' => $item->term_id));
                
                // Recursively add child terms if they exist
                if (!empty($child_categories) && !is_wp_error($child_categories)) {
                    $return_string .= wpestate_build_category_list($child_categories, $taxonomy, false, $show_count, $icon);
                }
            }
            $return_string .= '</li>';
        }
        $return_string .= '</ul>';

        return $return_string;
    }
}