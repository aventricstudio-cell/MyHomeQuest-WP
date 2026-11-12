<?php
/**
 * Property County/State Taxonomy Management
 *
 * Manages the county/state taxonomy terms for properties, including:
 * - Custom field handling for edit/add forms
 * - Term metadata storage and retrieval
 * - Featured image management
 * - Custom page associations
 *
 * This taxonomy represents the highest geographic level in the 
 * property location hierarchy (County/State > City > Area).
 *
 * @package WpResidence
 * @subpackage Taxonomies
 * @since 1.0
 */

// Hook into WordPress taxonomy actions to add custom fields
// Use the generic property category callbacks for the county/state taxonomy
add_action('property_county_state_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_county_state_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_county_state', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_county_state', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

if (!function_exists('wpestate_property_county_state_callback_function')):
    /**
     * Renders the edit form fields for county/state terms
     *
     * Displays form fields for editing county/state terms including:
     * - Associated page ID field
     * - Featured image upload/management
     * - Category tagline field
     * 
     * @param object $tag Term object being edited
     * @return void Outputs HTML form fields
     */
    function wpestate_property_county_state_callback_function($tag) {
        // Get existing values if editing an existing term
        if (is_object($tag)) {
            $t_id = $tag->term_id;
            $term_meta = get_option("taxonomy_$t_id");

            // Parse term meta into structured array
            $term_meta_array = wpestate_parse_category_term_array($term_meta);
            $pagetax = $term_meta_array['pagetax'];
            $category_featured_image = $term_meta_array['category_featured_image'];
            $category_tagline = $term_meta_array['category_tagline'];
            $category_attach_id = $term_meta_array['category_attach_id'];
        } else {
            // Set empty defaults for new terms
            $pagetax = '';
            $category_featured_image = '';
            $category_tagline = '';
            $category_attach_id = '';
        }

        // Output edit form HTML structure
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
                        <input id="category_featured_image" type="text" class="wpestate_landing_upload postform wpresidence-2025-input" size="36" name="term_meta[category_featured_image]" value="' . $category_featured_image . '" />
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

                <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_county_state" />

            </tr>
        </tbody>
        </table>';
    }
endif;

if (!function_exists('wpestate_property_county_state_callback_add_function')):
    /**
     * Renders the add new form fields for county/state terms
     *
     * Creates form fields for new county/state terms including:
     * - Page ID association
     * - Featured image upload
     * - Category tagline
     * 
     * Structures fields in a format appropriate for the add term form.
     *
     * @param object $tag Term object (empty for new terms)
     * @return void Outputs HTML form fields
     */
    function wpestate_property_county_state_callback_add_function($tag) {
        // Check for existing term data (rare for add form)
        if (is_object($tag)) {
            $t_id = $tag->term_id;
            $term_meta = get_option("taxonomy_$t_id");
            $term_meta_array = wpestate_parse_category_term_array($term_meta);
            $pagetax = $term_meta_array['pagetax'];
            $category_featured_image = $term_meta_array['category_featured_image'];
            $category_tagline = $term_meta_array['category_tagline'];
            $category_attach_id = $term_meta_array['category_attach_id'];
        } else {
            // Set empty defaults for new terms
            $pagetax = '';
            $category_featured_image = '';
            $category_tagline = '';
            $category_attach_id = '';
        }

        // Output add form HTML structure
        print'
        <div class="form-field">
        <label for="term_meta[pagetax]">' . esc_html__('Page id for this term', 'wpresidence-core') . '</label>
            <input type="text" name="term_meta[pagetax]" class="postform wpresidence-2025-input" value="' . $pagetax . '">
        </div>

        <div class="form-field">
            <label for="term_meta[pagetax]">' . esc_html__('Featured Image', 'wpresidence-core') . '</label>
            <input id="category_featured_image" type="text" size="36" class="wpestate_landing_upload wpresidence-2025-input"  name="term_meta[category_featured_image]" value="' . $category_featured_image . '" />
            <input id="category_featured_image_button" type="button"  class="upload_button button category_featured_image_button" value="' . esc_html__('Upload Image', 'wpresidence-core') . '" />
           <input id="category_attach_id" type="hidden" size="36" class="wpestate_landing_upload_id"  name="term_meta[category_attach_id]" value="' . $category_attach_id . '" />
        </div>

        <div class="form-field">
        <label for="term_meta[category_tagline]">' . esc_html__('Category Tagline', 'wpresidence-core') . '</label>
            <input id="category_tagline" type="text" size="36" class="wpresidence-2025-input" name="term_meta[category_tagline]" value="' . $category_tagline . '" />
        </div>
        <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_city" />
        ';
    }
endif;