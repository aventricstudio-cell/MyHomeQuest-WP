<?php
/**
 * Property Features Management for WpResidence Theme
 *
 * This file contains the code for managing property features in the WpResidence WordPress theme.
 * It allows users to display and select amenities and features for real estate listings.
 *
 * @package WpResidence
 * @subpackage PropertyFeatures
 * @since 1.0.0
 */

// Initialize variables
$list_to_show = '';
$single_return_string = '';
$multi_return_string = '';

// Get all property feature terms
$terms =  wpestate_get_cached_terms('property_features');

// Build the terms array
$parsed_features = wpestate_build_terms_array();



// Process and display features
if (is_array($parsed_features)):
    foreach ($parsed_features as $item) {
        if (count($item['childs']) > 0) {
            // Process parent features with children
            $multi_return_string_part = '<div class="listing_detail row feature_block_' . esc_attr($item['name']) . '">';
            $multi_return_string_part .= '<div class="feature_chapter_name">' . esc_html($item['name']) . '</div>';
            
            $multi_return_string_part_check = '';
            if (is_array($item['childs'])) {
                foreach ($item['childs'] as $key=>$child) {
                    $escaped_child = str_replace('\\', '\\\\', $child);
                    $term = get_term_by('term_id', $key, 'property_features');
                    $temp = wpestate_display_feature_submit($edit_id, $moving_array, $term, $wpestate_submission_page_fields);
                    $multi_return_string_part .= $temp;
                    $multi_return_string_part_check .= $temp;
                }
            }
            
            $multi_return_string_part .= '</div>';

            if ($multi_return_string_part_check !== '') {
                $multi_return_string .= $multi_return_string_part;
            }
        } else {
            // Process individual features
            
            $escaped_child = str_replace('\\', '\\\\',  $item['name']);
                 
            $term = get_term_by('name', $escaped_child, 'property_features');
            $single_return_string .= wpestate_display_feature_submit($edit_id, $moving_array, $term, $wpestate_submission_page_fields);
        }
    }
endif;

// Combine multi-feature and single-feature strings
$list_to_show = $multi_return_string;
if ($single_return_string !== '') {
    $list_to_show .= '<div class="listing_detail row feature_block_others">';
    $list_to_show .= '<div class="feature_chapter_name col-md-12">' . esc_html__('Other Features', 'wpresidence') . '</div>';
    $list_to_show .= $single_return_string . '</div>';
}

/**
 * Display a single feature for submission
 *
 * @param int $edit_id The ID of the property being edited
 * @param array $moving_array An array of features being moved
 * @param object $term The term object for the feature
 * @param array $wpestate_submission_page_fields Array of fields allowed on the submission page
 * @return string HTML for the feature checkbox
 */
function wpestate_display_feature_submit($edit_id, $moving_array, $term, $wpestate_submission_page_fields) {
    $post_var_name = $term->slug;
    $post_var_name_with_id = 'wpresidence_feature_'.$term->term_id;

    $list_to_show = '';

    // WPML compatibility
    $original_term = $term;
    if (defined('ICL_SITEPRESS_VERSION')) {
        $current_language = apply_filters('wpml_current_language', NULL);
        $default_language = apply_filters('wpml_default_language', NULL);

        if ($current_language != $default_language) {
            $trid = apply_filters('wpml_element_trid', NULL, $term->term_id, 'tax_property_features');
            $term_translations = apply_filters('wpml_get_element_translations', NULL, $trid, 'tax_property_features');
            $original_term_id = $term_translations[$default_language]->element_id;
            do_action('wpml_switch_language', $default_language);
            $original_term = get_term($original_term_id);
            do_action('wpml_switch_language', $current_language);
        }
    }

    if (is_array($wpestate_submission_page_fields) && in_array($term->slug, $wpestate_submission_page_fields)) {
        $value_label = $term->name;
        $sanitized_name = sanitize_html_class($post_var_name);

        $list_to_show .= '<div class="col-md-4 ' . $sanitized_name . '_features_submit features_submit">';
        $list_to_show .= '<input type="hidden" name="' . esc_attr($post_var_name_with_id) . '" value="" style="display:block;">';
        $list_to_show .= '<input type="checkbox" class="feature_list_save" id="' . esc_attr(str_replace('%', '', $post_var_name_with_id)) . '" ';
        $list_to_show .= 'name="' . esc_attr($post_var_name_with_id) . '" value="1" data-feature="' . intval($term->term_id) . '" ';

        if (has_term(floatval($term->term_id), 'property_features', $edit_id) || 
            (isset($_POST[$post_var_name_with_id]) && intval($_POST[$post_var_name_with_id]) == 1) || 
            (is_array($moving_array) && in_array($post_var_name, $moving_array))) {
            $list_to_show .= 'checked="checked" ';
        }

        $list_to_show .= '>';
        
        $list_to_show .= '<label class="features_amm_label" for="' . esc_attr(str_replace('%', '', $post_var_name_with_id)) . '">';
        $list_to_show .= (stripslashes($value_label)) . '</label></div>';
    }

    return $list_to_show;
}

// Display the features if any are available
if ($list_to_show !== '') {
    ?>
    <div class="profile-onprofile row">
        <div class="wpestate_dashboard_section_title"><?php esc_html_e('Amenities and Features', 'wpresidence'); ?></div>
        <div class="col-md-12">
            <?php echo trim($list_to_show); ?>
        </div>
    </div>
    <?php
}
?>