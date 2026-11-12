<?php
/**
 * Property Action Category Taxonomy Management
 *
 * This file handles the custom fields and UI for the property_action_category taxonomy.
 * It provides functionality for adding and editing custom fields like featured images,
 * taglines, and page associations for property action categories (e.g., for rent, for sale).
 *
 * @package WPResidence
 * @subpackage Core
 * @since 1.0
 */

// Hook into taxonomy form display and save actions
// Use the generic property category callbacks for the action category taxonomy
add_action('property_action_category_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_action_category_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_action_category', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_action_category', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

if (!function_exists('wpestate_property_action_category_callback_function')):
    /**
     * Renders the edit form fields for property action categories
     *
     * Displays custom fields in the edit taxonomy term form including:
     * - Page ID association
     * - Featured image upload
     * - Category tagline
     * - Hidden taxonomy identifier
     *
     * @param object $tag The term being edited
     * @return void
     */
    function wpestate_property_action_category_callback_function($tag) {
        // Get term metadata if editing existing term
        if (is_object($tag)) {
            $t_id = $tag->term_id;
            $term_meta = get_option("taxonomy_$t_id");
            $pagetax = $term_meta['pagetax'] ? $term_meta['pagetax'] : '';
            $category_featured_image = $term_meta['category_featured_image'] ? $term_meta['category_featured_image'] : '';
            $category_tagline = $term_meta['category_tagline'] ? $term_meta['category_tagline'] : '';
            $category_tagline = stripslashes($category_tagline);
            $category_attach_id = $term_meta['category_attach_id'] ? $term_meta['category_attach_id'] : '';
        } else {
            // Initialize empty values for new term
            $pagetax = '';
            $category_featured_image = '';
            $category_tagline = '';
            $category_attach_id = '';
        }

        // Output the form HTML structure
        print'
        <table class="form-table">
        <tbody>
            <tr class="form-field">
                <th scope="row" valign="top"><label for="term_meta[pagetax]">' . esc_html__('Page id for this term', 'wpresidence-core') . '</label></th>
                <td>
                    <input type="text" name="term_meta[pagetax]" class="postform wpresidence-2025-input" value="' . $pagetax . '">
                    <p class="description">' . esc_html__('Page id for this term', 'wpresidence-core') . '</p>
                </td>

                <tr valign="top">
                    <th scope="row"><label for="category_featured_image">' . esc_html__('Featured Image', 'wpresidence-core') . '</label></th>
                    <td>
                        <input id="category_featured_image" type="text" class="wpestate_landing_upload postform wpresidence-2025-input"  size="36" name="term_meta[category_featured_image]" value="' . $category_featured_image . '" />
                        <input id="category_featured_image_button" type="button"  class="upload_button button category_featured_image_button" value="' . esc_html__('Upload Image', 'wpresidence-core') . '" />
                        <input id="category_attach_id" type="hidden" size="36" class="wpestate_landing_upload_id" name="term_meta[category_attach_id]" value="' . $category_attach_id . '" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="term_meta[category_tagline]">' . esc_html__('Category Tagline', 'wpresidence-core') . '</label></th>
                    <td>
                        <input id="category_tagline" type="text" size="36" class="wpresidence-2025-input" name="term_meta[category_tagline]" value="' . $category_tagline . '" />
                    </td>
                </tr>

                <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_action_category" />

            </tr>
        </tbody>
        </table>';
    }
endif;

if (!function_exists('wpestate_property_action_category_callback_add_function')):
    /**
     * Renders the add new term form fields for property action categories
     *
     * Displays custom fields in the add new taxonomy term form including:
     * - Page ID association
     * - Featured image upload
     * - Category tagline
     * - Hidden taxonomy identifier
     *
     * @param object $tag The term being added
     * @return void
     */
    function wpestate_property_action_category_callback_add_function($tag) {
        // Initialize metadata values
        if (is_object($tag)) {
            $t_id = $tag->term_id;
            $term_meta = get_option("taxonomy_$t_id");
            $pagetax = $term_meta['pagetax'] ? $term_meta['pagetax'] : '';
            $category_featured_image = $term_meta['category_featured_image'] ? $term_meta['category_featured_image'] : '';
            $category_tagline = $term_meta['category_tagline'] ? $term_meta['category_tagline'] : '';
            $category_attach_id = $term_meta['category_attach_id'] ? $term_meta['category_attach_id'] : '';
        } else {
            $pagetax = '';
            $category_featured_image = '';
            $category_tagline = '';
            $category_attach_id = '';
        }

        // Output the form HTML structure for new terms
        print'
        <div class="form-field">
        <label for="term_meta[pagetax]">' . esc_html__('Page id for this term', 'wpresidence-core') . '</label>
            <input type="text" name="term_meta[pagetax]" class="postform wpresidence-2025-input" value="' . $pagetax . '">
        </div>

        <div class="form-field">
            <label for="term_meta[pagetax]">' . esc_html__('Featured Image', 'wpresidence-core') . '</label>
            <input id="category_featured_image" type="text" size="36" class="wpestate_landing_upload wpresidence-2025-input"  name="term_meta[category_featured_image]" value="' . $category_featured_image . '" />
            <input id="category_featured_image_button" type="button"  class="upload_button button category_featured_image_button" value="' . esc_html__('Upload Image', 'wpresidence-core') . '" />
           <input id="category_attach_id" type="hidden" size="36" class="wpestate_landing_upload_id" name="term_meta[category_attach_id]" value="' . $category_attach_id . '" />
        </div>

        <div class="form-field">
        <label for="term_meta[category_tagline]">' . esc_html__('Category Tagline', 'wpresidence-core') . '</label>
            <input id="category_tagline" type="text" size="36" class="wpresidence-2025-input" name="term_meta[category_tagline]" value="' . $category_tagline . '" />
        </div>
        <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_action_category" />
        ';
    }
endif;

if (!function_exists('wpestate_property_city_save_extra_fields_callback')):
    /**
     * Saves the custom fields data for property action categories
     *
     * Handles the saving of custom field data when a term is created or edited.
     * Sanitizes input data and updates the term metadata in the database.
     *
     * @param int $term_id The ID of the term being saved
     * @return void
     */
    function wpestate_property_city_save_extra_fields_callback($term_id) {
        // Check if term metadata was submitted
        if (isset($_POST['term_meta'])) {
            $t_id = $term_id;
            $term_meta = get_option("taxonomy_$t_id");
            $cat_keys = array_keys($_POST['term_meta']);
            $allowed_html = array();
            
            // Sanitize and save each submitted field
            foreach ($cat_keys as $key) {
                $key = sanitize_key($key);
                if (isset($_POST['term_meta'][$key])) {
                    $term_meta[$key] = wp_kses($_POST['term_meta'][$key], $allowed_html);
                }
            }

            // Remove deprecated property_* keys after migration
            $deprecated = array(
                'property_address',
                'property_zip',
                'property_country',
                'property_latitude',
                'property_longitude',
                'property_google_view',
            );
            foreach ($deprecated as $old_key) {
                if (isset($term_meta[$old_key])) {
                    unset($term_meta[$old_key]);
                }
            }
            
            // Save the updated metadata
            update_option("taxonomy_$t_id", $term_meta);
        }
    }
endif;