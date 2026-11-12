<?php

/**
 * Get design template types.
 *
 * @return array Array of template types.
 */
function wpestate_desing_template_types() {
    $types = array(
        ''                                  => esc_html__('Select Option', 'wpresidence-core'),
        'wpestate_template_header'          => esc_html__('Header', 'wpresidence-core'),
        'wpestate_template_before_header'   => esc_html__('Before Header', 'wpresidence-core'),
        'wpestate_template_after_header'    => esc_html__('After Header', 'wpresidence-core'),
        'wpestate_template_footer'          => esc_html__('Footer', 'wpresidence-core'),
        'wpestate_template_after_footer'    => esc_html__('After Footer', 'wpresidence-core'),
        'wpestate_template_before_footer'   => esc_html__('Before Footer', 'wpresidence-core'),
        'wpestate_single_property_page'    => esc_html__('Single Property Page', 'wpresidence-core'),
        'wpestate_single_agent'            => esc_html__('Single Agent', 'wpresidence-core'),
        'wpestate_single_agency'           => esc_html__('Single Agency', 'wpresidence-core'),
        'wpestate_single_developer'        => esc_html__('Single Developer', 'wpresidence-core'),
        'wpestate_single_post'             => esc_html__('Single Post', 'wpresidence-core'),
        'wpestate_category_page'           => esc_html__('Category Template', 'wpresidence-core'),
        'wpestate_template_custom_block'   => esc_html__('Block', 'wpresidence-core'),
    );
 
    return $types;
}

/**
 * Get template selection options.
 *
 * @return array Array of selection options.
 */



function wpestate_templates_selection_options() {
    // Limit post types to the ones used by the theme.
    $allowed_post_types = array(
        'post',
        'estate_property',
        'estate_agent',
        'estate_agency',
        'estate_developer',
    );

    $post_types = array();
    foreach ($allowed_post_types as $post_type) {
        $obj = get_post_type_object($post_type);
        if ($obj) {
            $post_types[$post_type] = $obj;
        }
    }

    $special_pages = wpestate_templates_special_pages();

    $selection_options = array(
        'standard' => array(
            'label' => __('Standard', 'wpresidence-core'),
            'value' => array(
                'standard-global'    => __('Entire Website', 'wpresidence-core'),
                'standard-singulars' => __('All Singulars', 'wpresidence-core'),
                'standard-archives'  => __('All Archives', 'wpresidence-core'),
            ),
        ),
        'special' => array(
            'label' => __('Special Pages', 'wpresidence-core'),
            'value' => $special_pages,
        ),
        'post_types' => array(
            'label' => __('Post Types', 'wpresidence-core'),
            'value' => array(),
        ),
        'taxonomies' => array(
            'label' => __('Taxonomies', 'wpresidence-core'),
            'value' => array(),
        ),
    );

    // Add post types to the selection options
    foreach ($post_types as $post_type) {
        $selection_options['post_types']['value'][$post_type->name] = esc_html__('All', 'wpresidence-core') . ' ' . $post_type->label;
    }

    // Add taxonomies to the selection options
    $taxonomies = get_taxonomies(['public' => true], 'objects');

    unset($taxonomies['wpestate-crm-contact-status']);
    unset($taxonomies['wpestate-crm-lead-status']);

    foreach ($taxonomies as $taxonomy) {
        $object_type_labels = array();
        foreach ($taxonomy->object_type as $object_type) {
            if (isset($post_types[$object_type])) {
                $object_type_labels[] = $post_types[$object_type]->label;
            }
        }

        if ($taxonomy->name !== 'product' && $taxonomy->name !== 'wpestate-crm-lead') {
            $selection_options['taxonomies']['value'][$taxonomy->name] = esc_html__('All', 'wpresidence-core') . ' ' . $taxonomy->label . ' (' . implode(', ', $object_type_labels) . ')';
        }
    }

    return $selection_options;
}








/**
 * Get special pages for templates.
 *
 * @return array Array of special pages.
 */
function wpestate_templates_special_pages() {
    $special_pages = array(
        '404'    => __('404 Page', 'wpresidence-core'),
        'front_page'  => __('Front Page', 'wpresidence-core'),
    );

    return $special_pages;
}

/**
 * Retrieve all public taxonomy terms.
 *
 * @return array Associative array of term options.
 */
function wpestate_get_all_taxonomy_terms() {
    $taxonomies = get_taxonomies(['public' => true], 'objects');

    // Remove CRM related taxonomies if they exist
    unset($taxonomies['wpestate-crm-contact-status']);
    unset($taxonomies['wpestate-crm-lead-status']);

    $options = array(
        '' => __('Select Term', 'wpresidence-core'),
    );

    foreach ($taxonomies as $tax) {
        $terms = get_terms(array(
            'taxonomy'   => $tax->name,
            'hide_empty' => false,
        ));

        if (is_wp_error($terms)) {
            continue;
        }

        foreach ($terms as $term) {
            $key = $tax->name . ':' . $term->term_id;
            $label = $tax->label . ' - ' . $term->name;
            $options[$key] = $label;
        }
    }

    return $options;
}
/**
 * Create a select field for templates.
 *
 * @param string $name The name attribute for the select field.
 * @param array $options The options for the select field.
 * @param string $selected_value The selected value.
 *
 * @return string The HTML output for the select field.
 */
function wpestate_template_create_select_field($name, $options, $selected_value = '') {
    $html = '<select name="' . esc_attr($name) . '" id="' . esc_attr($name) . '">';
    foreach ($options as $value => $label) {
        $selected = ($value == $selected_value) ? ' selected="selected"' : '';
        $html .= '<option value="' . esc_attr($value) . '"' . $selected . '>' . esc_html($label) . '</option>';
    }
    $html .= '</select>';

    return $html;
}

/**
 * Create a nested select field for templates.
 *
 * @param string $name The name attribute for the select field.
 * @param array $options The options for the select field.
 * @param string $selected_value The selected value.
 * @param string $tax_name       Optional taxonomy term field name.
 * @param array  $tax_options    Optional taxonomy term options.
 * @param string $selected_tax   Selected taxonomy term value.
 *
 * @return string The HTML output for the nested select field.

 * STEP 1: Just modify helper_functions.php
 * Add data attributes to the existing function
 */



function wpestate_create_nested_select_field($name, $options, $selected_value = '', $tax_name = '', $tax_options = array(), $selected_tax = '') {
    $html = '<div class="wpestate-location-row">';
    $html .= '<select name="' . esc_attr($name) . '" class="wpestate-selection_dropdown wpresidence-2025-select">';
    $html .= '<option value="disabled">' . esc_html__('Disabled', 'wpresidence-core') . '</option>';
    
    foreach ($options as $group_key => $group) {
        if (isset($group['label']) && isset($group['value'])) {
            $html .= '<optgroup label="' . esc_attr($group['label']) . '">';
            
            foreach ($group['value'] as $value => $label) {
                $selected = ($value == $selected_value) ? ' selected="selected"' : '';
                
                // Add data-template attribute
                $data_template = '';
                
                // Post types
                if ($value == 'estate_property'){ 
                    $data_template = 'data-template="property"';
                }else if ($value == 'estate_agent') {
                    $data_template = 'data-template="agent"';
                }else if ($value == 'estate_agency') {
                    $data_template = 'data-template="agency"';
                }else if ($value == 'estate_developer') {
                    $data_template = 'data-template="developer"';
                }else  if ($value == 'post') {
                    $data_template = 'data-template="post"';
                }else{
                    $data_template = 'data-template="general"';
                }

                
                // Taxonomies - detect by name patterns
                if ($group_key == 'taxonomies') {
                    if (strpos($value, 'agent') !== false) {
                        $data_template = 'data-template="agent"';
                    } elseif (strpos($value, 'agency') !== false) {
                        $data_template = 'data-template="agency"';
                    } elseif (strpos($value, 'developer') !== false) {
                        $data_template = 'data-template="developer"';
                    } elseif (in_array($value, ['category', 'post_tag'])) {
                        $data_template = 'data-template="post"';
                    } elseif (strpos($value, 'property_') === 0) {
                        $data_template = 'data-template="property"';
                    }
                }
                
                $data_taxonomy = '';
                if ($group_key == 'taxonomies') {
                    $data_taxonomy = ' data-taxonomy="' . esc_attr($value) . '"';
                }

                $html .= '<option value="' . esc_attr($value) . '" ' . $data_template . $data_taxonomy . $selected . '>' . esc_html($label) . '</option>';
            }
            $html .= '</optgroup>';
        }
    }

    $html .= '</select>';

    if ($tax_name) {
        $html .= '<select name="' . esc_attr($tax_name) . '" class="wpestate-tax-term wpresidence-2025-select">';
        $html .= '<option value="">' . esc_html__('Any Term', 'wpresidence-core') . '</option>';
        foreach ($tax_options as $value => $label) {
            $selected = ($value == $selected_tax) ? ' selected="selected"' : '';
            $data_taxonomy = '';
            if (strpos($value, ':') !== false) {
                list($tax_slug,) = explode(':', $value, 2);
                $data_taxonomy = ' data-taxonomy="' . esc_attr($tax_slug) . '"';
            }
            $html .= '<option value="' . esc_attr($value) . '"' . $data_taxonomy . $selected . '>' . esc_html($label) . '</option>';
        }
        $html .= '</select>';
    }

    $html .= '<button type="button" class="button wpresidence_button secondary wpestate-remove-location" aria-label="' . esc_attr__('Delete', 'wpresidence-core') . '">×</button>';
    $html .= '</div>';

    return $html;
}






/**
 * Sideload an image from a given URL and attach it to a post.
 *
 * @param string $url The URL of the image to download.
 * @param int $post_id The ID of the post to attach the image to (default: 0).
 * @param string|null $description Optional. The description of the image.
 *
 * @return array|WP_Error The attachment ID and URL of the uploaded image, or a WP_Error object on failure.
 */
function wpestate_sideload_image($url, $post_id = 0, $description = null) {
    // Include necessary WordPress files for handling media
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Download the image from the provided URL
    $tmp = download_url($url);

    // Handle any errors that occur during the download
    if (is_wp_error($tmp)) {
        @unlink($file_array['tmp_name']);
        return $tmp;
    }

    // Set up an array with the file attributes
    preg_match('/[^\?]+\.(jpg|jpeg|jpe|gif|png|svg|webp)\b/i', $url, $matches);
    $file_array['name'] = basename($matches[0]);
    $file_array['tmp_name'] = $tmp;

    // Handle errors that occur during temporary storage
    if (is_wp_error($tmp)) {
        @unlink($file_array['tmp_name']);
        return $tmp;
    }

    // Validate and store the file
    $id = media_handle_sideload($file_array, $post_id, $description);

    // Handle errors that occur during permanent storage
    if (is_wp_error($id)) {
        @unlink($file_array['tmp_name']);
        return $id;
    }

    // Get the URL of the uploaded image
    $src = wp_get_attachment_url($id);

    // Return the ID and URL of the uploaded image
    return array('id' => $id, 'url' => $src);
}

/**
 * Handle AJAX request to sideload multiple images and return their new URLs.
 *
 * This function checks user permissions, verifies the nonce, processes the provided image URLs,
 * and returns the results as a JSON response.
 */
function wpestate_sideload_images() {
    // Verify the AJAX nonce for security
    check_ajax_referer('wpestate_nonce', 'nonce'); 

    // Check if the current user has permission to upload files
    if (!current_user_can('upload_files')) {
        wp_send_json_error('You do not have permission to upload files');
    }

    // Check if images were provided in the request
    if (!isset($_POST['images']) || !is_array($_POST['images'])) {
        wp_send_json_error('No images provided');
    }

    // Get the array of image URLs
    $images = $_POST['images'];
    $updated_images = [];

    // Loop through each image URL and process it
    foreach ($images as $image_url) {
        // Sideload the image and sanitize the URL
        $result = wpestate_sideload_image(sanitize_text_field($image_url));

        // Check if the image was uploaded successfully
        if (!is_wp_error($result)) {
            $updated_images[$image_url] = array(
                'url' => $result['url'],
                'id' => $result['id']
            );
        } else {
            // If there was an error, use the original URL as a fallback
            $updated_images[$image_url] = array(
                'url' => $image_url,
                'id' => 0
            ); // Fallback to original if error
        }
    }

    // Return the results as a JSON response
    wp_send_json_success($updated_images);
}

// Register an AJAX handler to sideload images and return new URLs
add_action('wp_ajax_wpestate_sideload_images', 'wpestate_sideload_images');
