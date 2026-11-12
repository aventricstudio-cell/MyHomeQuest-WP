<?php
/**
 * Property Features/Amenities Taxonomy Management
 *
 * Manages the property features taxonomy including:
 * - SVG icon upload and association with feature terms
 * - Custom fields for feature term metadata
 * - Edit/Add form field customization
 * - Data saving and retrieval
 * 
 * Features represent amenities and characteristics of properties
 * that can be used for filtering and display purposes.
 *
 * @package WpResidence
 * @subpackage PropertyFeatures
 * @since 1.0
 */

// Hook into WordPress taxonomy actions for features
// Use the generic property category callbacks for the features taxonomy
add_action('property_features_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_features_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_features', 'wpestate_property_features_save_extra_fields_callback', 10, 2);
add_action('edited_property_features', 'wpestate_property_features_save_extra_fields_callback', 10, 2);

if (!function_exists('wpestate_property_features_callback_function')):
    /**
     * Displays custom fields on the property feature term edit form
     * 
     * Adds form fields for:
     * - SVG icon upload and management
     * - Icon attachment ID tracking
     * 
     * Includes link to documentation/tutorial for proper usage.
     *
     * @param object $tag The term being edited
     * @return void Outputs HTML form fields
     */
    function wpestate_property_features_callback_function($tag) {
        // Get existing values if editing a term
        if (is_object($tag)) {
            $t_id = $tag->term_id;
            $term_meta = get_option("taxonomy_$t_id");

            // Get icon data with fallbacks
            $category_featured_image = isset($term_meta['category_featured_image_icon']) ? $term_meta['category_featured_image_icon'] : '';
            $category_attach_id = isset($term_meta['category_attach_id']) ? $term_meta['category_attach_id'] : '';
        } else {
            // Default for new terms
            $category_attach_id = '';
        }

        // Output edit form HTML
        print'
        <table class="form-table">
        <tbody>
            <tr class="form-field">
                <tr valign="top">
                    <th scope="row"><label for="category_featured_image">' . esc_html__('SVG ICON - SVG ONLY!', 'wpresidence-core') . ' - <a target="_blank" href="https://help.wpresidence.net/article/listings-features-and-amenities-listings-labels/">' . esc_html__('Video Tutorial', 'wpresidence-core') . '</a>
                </label></th>
                    <td>
                        <input id="category_featured_image" type="text"  class="postform wpestate_landing_upload wpresidence-2025-input" size="36" name="term_meta[category_featured_image_icon]" value="' . $category_featured_image . '" />
                        <input id="category_featured_image_button" type="button"  class="upload_button button category_featured_image_button" value="' . esc_html__('Upload SVG', 'wpresidence-core') . '" />
                        <input id="category_attach_id" type="hidden" size="36" class="wpestate_landing_upload_id" name="term_meta[category_attach_id]" value="' . $category_attach_id . '" />
                    </td>
                </tr>
                <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_features" />
            </tr>
        </tbody>
        </table>';
    }
endif;

if (!function_exists('wpestate_property_features_callback_add_function')):
    /**
     * Displays custom fields on the add new property feature form
     * 
     * Creates form fields for new feature terms including:
     * - SVG icon upload functionality
     * - Icon attachment tracking
     * 
     * Provides help documentation link for proper usage.
     *
     * @param object $tag The term being created
     * @return void Outputs HTML form fields
     */
    function wpestate_property_features_callback_add_function($tag) {
        // Check for existing term data (rare for add form)
        if (is_object($tag)) {
            $t_id = $tag->term_id;
            $term_meta = get_option("taxonomy_$t_id");
            $category_featured_image = $term_meta['category_featured_image_icon'] ? $term_meta['category_featured_image_icon'] : '';
            $category_attach_id = $term_meta['category_attach_id'] ? $term_meta['category_attach_id'] : '';
        } else {
            // Set empty defaults for new terms
            $category_attach_id = '';
            $category_featured_image = '';
        }

        // Output add form HTML
        print'
        <div class="form-field">
        <div class="form-field">
            <label for="term_meta[pagetax]">' . esc_html__('SVG ICON - SVG ONLY!', 'wpresidence-core') . ' - <a target="_blank" href="https://help.wpresidence.net/article/listings-features-and-amenities-listings-labels/">' . esc_html__('Video Tutorial', 'wpresidence-core') . '</a></label>
            <input id="category_featured_image" type="text" size="36" class="wpestate_landing_upload wpresidence-2025-input" name="term_meta[category_featured_image_icon]" value="' . $category_featured_image . '" />
            <input id="category_featured_image_button" type="button"  class="upload_button button category_featured_image_button" value="' . esc_html__('Upload SVG', 'wpresidence-core') . '" />
            <input id="category_attach_id" type="hidden" size="36" class="wpestate_landing_upload_id" name="term_meta[category_attach_id]" value="' . $category_attach_id . '" />
        </div>
        <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_features" /></div>
        ';
    }
endif;

if (!function_exists('wpestate_property_features_save_extra_fields_callback')):
    /**
     * Saves custom fields data for property feature terms
     * 
     * Handles saving metadata when features are created/edited:
     * - Sanitizes input data
     * - Updates term meta in options table
     * - Manages SVG icon associations
     *
     * @param int $term_id The ID of the term being saved
     * @return void
     */
    function wpestate_property_features_save_extra_fields_callback($term_id) {
        // Check if we have term meta to save
        if (isset($_POST['term_meta'])) {
            $t_id = $term_id;
            $term_meta = get_option("taxonomy_$t_id");
            $cat_keys = array_keys($_POST['term_meta']);
            $allowed_html = array();
            
            // Save each meta field
            foreach ($cat_keys as $key) {
                $key = sanitize_key($key);
                if (isset($_POST['term_meta'][$key])) {
                    $value = wp_unslash($_POST['term_meta'][$key]);
                    $term_meta[$key] = wp_kses($value, $allowed_html);
                }
            }
            //save the option array
            update_option("taxonomy_$t_id", $term_meta);
        }
    }
endif;