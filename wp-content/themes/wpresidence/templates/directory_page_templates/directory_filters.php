<?php
/**
 * Directory Sidebar Template
 *
 * This template is part of the WpResidence theme and displays the sidebar for the directory page,
 * including various property filters and widgets.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0
 */

// Retrieve necessary variables and options
$sidebar_name = $wpestate_options['sidebar_name'];
$sidebar_class = $wpestate_options['sidebar_class'];
$args = wpestate_get_select_arguments();
$action_select_list = wpestate_get_action_select_list($args);
$categ_select_list = wpestate_get_category_select_list($args);
$select_city_list = wpestate_get_city_select_list($args);
$select_area_list = wpestate_get_area_select_list($args);
$select_county_state_list = wpestate_get_county_state_select_list($args);
$allowed_html = array();

// Function to get filter value
function get_filter_value($filter_array, $default_text, $taxonomy) {
    if (isset($filter_array[0]) && $filter_array[0] != '' && $filter_array[0] != 'all') {
        $term = get_term_by('slug', esc_html(wp_kses($filter_array[0], array())), $taxonomy);
        $value = $term->name;
        $value_slug = mb_strtolower(str_replace(' ', '-', $value));
        return array($value, $value_slug);
    }
    return array($default_text, 'all');
}

// Get filter values
list($adv_actions_value, $adv_actions_value1) = get_filter_value($current_adv_filter_search_action, esc_html__('Types', 'wpresidence'), 'property_action_category');
list($adv_categ_value, $adv_categ_value1) = get_filter_value($current_adv_filter_search_category, esc_html__('Categories', 'wpresidence'), 'property_category');
list($advanced_city_value, $advanced_city_value1) = get_filter_value($current_adv_filter_city, esc_html__('Cities', 'wpresidence'), 'property_city');
list($advanced_area_value, $advanced_area_value1) = get_filter_value($current_adv_filter_area, esc_html__('Areas', 'wpresidence'), 'property_area');
list($advanced_county_value, $advanced_county_value1) = get_filter_value($current_adv_filter_county, esc_html__('States', 'wpresidence'), 'property_county_state');

// Start of HTML output
?>
<div class="clearfix visible-xs"></div>
<div class="directory_sidebar col-xs-12 <?php echo esc_attr($sidebar_class); ?> widget-area-sidebar widget_directory_sidebar" id="primary">
    <div class="directory_sidebar_wrapper widget-container">
        <?php
        // Output filter dropdowns
        echo wpestate_build_dropdown_for_filters('sidebar-adv_actions', $adv_actions_value1, $adv_actions_value, $action_select_list);
        echo wpestate_build_dropdown_for_filters('sidebar-adv_categ', $adv_categ_value1, $adv_categ_value, $categ_select_list);
        echo wpestate_build_dropdown_for_filters('sidebar-adv_conty', $advanced_county_value1, $advanced_county_value, $select_county_state_list);
        echo wpestate_build_dropdown_for_filters('sidebar-advanced_city', $advanced_city_value1, $advanced_city_value, $select_city_list);
        echo wpestate_build_dropdown_for_filters('sidebar-adv_area', $advanced_area_value1, $advanced_area_value, $select_area_list);

        // Price range slider
        $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
        $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
        $min_price_slider = floatval(wpresidence_get_option('wp_estate_show_slider_min_price', ''));
        $max_price_slider = floatval(wpresidence_get_option('wp_estate_show_slider_max_price', ''));
        $price_slider_label = wpestate_show_price_label_slider($min_price_slider, $max_price_slider, $wpestate_currency, $where_currency);
        ?>
        <div class="adv_search_slider">
            <p>
                <label><?php esc_html_e('Price range:', 'wpresidence'); ?></label>
                <span id="amount_wd" class="wpresidence_slider_price"><?php echo esc_html($price_slider_label); ?></span>
            </p>
            <div id="slider_price_widget"></div>
            <?php
            $custom_fields = wpresidence_get_option('wp_estate_multi_curr', '');
            if (!empty($custom_fields) && isset($_COOKIE['my_custom_curr']) && isset($_COOKIE['my_custom_curr_pos']) && isset($_COOKIE['my_custom_curr_symbol']) && $_COOKIE['my_custom_curr_pos'] != -1) {
                $i = intval($_COOKIE['my_custom_curr_pos']);
                if (!isset($_GET['price_low']) && !isset($_GET['price_max'])) {
                    $min_price_slider *= $custom_fields[$i][2];
                    $max_price_slider *= $custom_fields[$i][2];
                }
            }
            ?>
            <input type="hidden" id="price_low_widget" name="price_low" value="<?php echo esc_attr($min_price_slider); ?>" />
            <input type="hidden" id="price_max_widget" name="price_max" value="<?php echo esc_attr($max_price_slider); ?>" />
        </div>

        <?php
        // Property size slider
        $measure_sys = wpestate_get_meaurement_unit_formated();
        $dir_min_size = wpestate_convert_measure(get_post_meta($post->ID, 'dir_min_size', true));
        $dir_max_size = wpestate_convert_measure(get_post_meta($post->ID, 'dir_max_size', true));
        ?>
        <div class="directory_slider adv_search_slider property_size_slider">
            <p>
                <label for="property_size"><?php esc_html_e('Size:', 'wpresidence'); ?></label>
                <span id="property_size"><?php echo wp_kses_post(trim($dir_min_size . ' ' . $measure_sys) . ' ' . esc_html__('to', 'wpresidence') . ' ' . trim($dir_max_size . ' ' . $measure_sys)); ?></span>
            </p>
            <div id="slider_property_size_widget"></div>
            <input type="hidden" id="property_size_low" name="property_size_low" value="<?php echo esc_attr(get_post_meta($post->ID, 'dir_min_size', true)); ?>" />
            <input type="hidden" id="property_size_max" name="property_size_max" value="<?php echo esc_attr(get_post_meta($post->ID, 'dir_max_size', true)); ?>" />
        </div>

        <?php
        // Lot size slider
        $dir_min_lot_size = wpestate_convert_measure(get_post_meta($post->ID, 'dir_min_lot_size', true));
        $dir_max_lot_size = wpestate_convert_measure(get_post_meta($post->ID, 'dir_max_lot_size', true));
        ?>
        <div class="directory_slider adv_search_slider property_lot_size_slider">
            <p>
                <label for="property_lot_size"><?php esc_html_e('Lot Size:', 'wpresidence'); ?></label>
                <span id="property_lot_size"><?php echo wp_kses_post(trim($dir_min_lot_size . ' ' . $measure_sys) . ' ' . esc_html__('to', 'wpresidence') . ' ' . trim($dir_max_lot_size . ' ' . $measure_sys)); ?></span>
            </p>
            <div id="slider_property_lot_size_widget"></div>
            <input type="hidden" id="property_lot_size_low" name="property_lot_size_low" value="<?php echo esc_attr(get_post_meta($post->ID, 'dir_min_lot_size', true)); ?>" />
            <input type="hidden" id="property_lot_size_max" name="property_lot_size_max" value="<?php echo esc_attr(get_post_meta($post->ID, 'dir_max_lot_size', true)); ?>" />
        </div>

        <?php
        // Rooms slider
        $dir_rooms_min = get_post_meta($post->ID, 'dir_rooms_min', true);
        $dir_rooms_max = get_post_meta($post->ID, 'dir_rooms_max', true);
        ?>
        <div class="directory_slider adv_search_slider property_rooms_slider">
            <p>
                <label for="property_rooms"><?php esc_html_e('Property Rooms:', 'wpresidence'); ?></label>
                <span id="property_rooms"><?php echo esc_html($dir_rooms_min . ' ' . esc_html__('to', 'wpresidence') . ' ' . $dir_rooms_max); ?></span>
            </p>
            <div id="slider_property_rooms_widget"></div>
            <input type="hidden" id="property_rooms_low" name="property_rooms_low" value="<?php echo esc_attr($dir_rooms_min); ?>" />
            <input type="hidden" id="property_rooms_max" name="property_rooms_max" value="<?php echo esc_attr($dir_rooms_max); ?>" />
        </div>

        <?php
        // Bedrooms slider
        $dir_bedrooms_min = get_post_meta($post->ID, 'dir_bedrooms_min', true);
        $dir_bedrooms_max = get_post_meta($post->ID, 'dir_bedrooms_max', true);
        ?>
        <div class="directory_slider adv_search_slider property_bedrooms_slider">
            <p>
                <label for="property_bedrooms"><?php esc_html_e('Property Bedrooms:', 'wpresidence'); ?></label>
                <span id="property_bedrooms"><?php echo esc_html($dir_bedrooms_min . ' ' . esc_html__('to', 'wpresidence') . ' ' . $dir_bedrooms_max); ?></span>
            </p>
            <div id="slider_property_bedrooms_widget"></div>
            <input type="hidden" id="property_bedrooms_low" name="property_bedrooms_low" value="<?php echo esc_attr($dir_bedrooms_min); ?>" />
            <input type="hidden" id="property_bedrooms_max" name="property_bedrooms_max" value="<?php echo esc_attr($dir_bedrooms_max); ?>" />
        </div>

        <?php
        // Bathrooms slider
        $dir_bathrooms_min = get_post_meta($post->ID, 'dir_bathrooms_min', true);
        $dir_bathrooms_max = get_post_meta($post->ID, 'dir_bathrooms_max', true);
        ?>
        <div class="directory_slider adv_search_slider property_bathrooms_slider">
            <p>
                <label for="property_bathrooms"><?php esc_html_e('Property Bathrooms:', 'wpresidence'); ?></label>
                <span id="property_bathrooms"><?php echo esc_html($dir_bathrooms_min . ' ' . esc_html__('to', 'wpresidence') . ' ' . $dir_bathrooms_max); ?></span>
            </p>
            <div id="slider_property_bathrooms_widget"></div>
            <input type="hidden" id="property_bathrooms_low" name="property_bathrooms_low" value="<?php echo esc_attr($dir_bathrooms_min); ?>" />
            <input type="hidden" id="property_bathrooms_max" name="property_bathrooms_max" value="<?php echo esc_attr($dir_bathrooms_max); ?>" />
        </div>

        <?php
        // Property status dropdown
        $property_status_terms = wpestate_get_cached_terms('property_status');
        $property_status_options = '';
        foreach ($property_status_terms as $term) {
            $property_status_options .= '<option value="' . esc_attr($term->name) . '">' . esc_html($term->name) . '</option>';
        }
        ?>
        <div class="property_status_wrapper">
            <label for="property_status"><?php esc_html_e('Property Status:', 'wpresidence'); ?></label><br />
            <select id="property_status" class="form-select" name="property_status">
                <option value=""><?php esc_html_e('Property Status', 'wpresidence'); ?></option>
                <?php echo $property_status_options; ?>
            </select>
        </div>

        <div class="property_keyword_wrapper">
            <label for="property_keyword"><?php esc_html_e('Property Keyword:', 'wpresidence'); ?></label><br />
            <?php
            // Keep this field out of browser autofill/history restore by using off + readonly-until-focus.
            ?>
            <input type="search" id="property_keyword" class="form-control"
               name="property_keyword_no_autofill"
               value=""
               autocomplete="off"
               autocorrect="off"
               autocapitalize="off"
               spellcheck="false"
               readonly
               onfocus="this.removeAttribute('readonly');"
               placeholder="<?php esc_attr_e('keyword', 'wpresidence'); ?>">
        </div>

        <div class="extended_search_check_wrapper_directory">
            <?php
            $advanced_exteded = wpresidence_get_option('wp_estate_advanced_exteded');
            $featured_terms = wpresidence_redux_advanced_exteded();
            if (is_array($advanced_exteded)) {
                foreach ($advanced_exteded as $checker => $slug) {
                    $input_name = str_replace('%', '', $slug);
                    $item_title = $featured_terms[$slug];
                    if ($input_name != 'none' && $item_title != '') {
                        // Step 1: Try the original stored slug because non-Latin terms can keep percent-encoded slugs.
                        $feature_term_id = '';
                        $feature_term = get_term_by('slug', $slug, 'property_features');

                        // Step 2: Fall back to the stripped checkbox slug for older Latin-style entries.
                        if ((!$feature_term || is_wp_error($feature_term)) && $input_name !== '') {
                            $feature_term = get_term_by('slug', $input_name, 'property_features');
                        }

                        // Step 3: Fall back to the visible term name so non-Latin labels also get a term id.
                        if ((!$feature_term || is_wp_error($feature_term)) && $item_title !== '') {
                            $feature_term = get_term_by('name', $item_title, 'property_features');
                        }

                        // Step 4: Print the resolved term id only when we found a real taxonomy term.
                        if ($feature_term && !is_wp_error($feature_term)) {
                            $feature_term_id = intval($feature_term->term_id);
                        }
                        ?>
                        <div class="extended_search_checker <?php echo esc_attr($input_name); ?>_directory">
                            <input type="checkbox" id="<?php echo esc_attr($input_name); ?>widget" name="<?php echo esc_attr($input_name); ?>" name-title="<?php echo esc_attr($item_title); ?>" data-term-id="<?php echo esc_attr($feature_term_id); ?>" value="1">
                            <label class="directory_checkbox" for="<?php echo esc_attr($input_name); ?>widget"><?php echo esc_html($item_title); ?></label>
                        </div>
                        <?php
                    }
                }
            }
            ?>
        </div>

        <input type="hidden" id="property_dir_per_page" value="<?php echo intval(wpresidence_get_option('wp_estate_prop_no', '')); ?>">
        <input type="hidden" id="property_dir_pagination" value="1">
        <input type="hidden" id="property_dir_done" value="0">
    </div>

    <?php if (is_active_sidebar($sidebar_name)) : ?>
        <ul class="xoxo">
            <?php dynamic_sidebar($sidebar_name); ?>
        </ul>
    <?php endif; ?>

</div>
