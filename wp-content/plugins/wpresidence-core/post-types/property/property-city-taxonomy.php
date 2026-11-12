<?php
/**
 * Property City Taxonomy Management
 *
 * Handles the creation and management of city taxonomy terms including their
 * relationships with states/counties. Provides functionality for:
 * - Adding/editing city terms
 * - Associating cities with states/counties
 * - Managing city meta data (featured images, taglines, etc)
 * - Saving city term data
 *
 * @package WpResidence
 * @subpackage PropertyCity
 * @since 1.0
 */

// Register action hooks for managing property city terms
add_action('property_city_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_city_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_city', 'wpestate_property_city_save_extra_fields_callback', 10, 2);
add_action('edited_property_city', 'wpestate_property_city_save_extra_fields_callback', 10, 2);

if (!function_exists('wpestate_property_city_callback_function')):
    /**
     * Displays fields for editing a property city term
     * 
     * Adds form fields to edit:
     * - Parent state/county association
     * - Page ID for the city term
     * - Featured image for the city
     * - City tagline
     *
     * @param object $tag The term being edited
     * @return void Outputs HTML form fields
     */
    function wpestate_property_city_callback_function($tag) {
        // Initialize default empty values
        $pagetax = '';
        $category_featured_image = '';
        $category_tagline = '';
        $category_attach_id = '';

        // Get existing values if editing an existing term
        if (is_object($tag)) {
            $t_id = $tag->term_id;
            $term_meta = get_option("taxonomy_$t_id");
            
            // Parse the term meta into an array of values
            $term_meta_array = wpestate_parse_category_term_array($term_meta);
            $pagetax = $term_meta_array['pagetax'];
            $category_featured_image = $term_meta_array['category_featured_image'];
            $category_tagline = $term_meta_array['category_tagline'];
            $category_attach_id = $term_meta_array['category_attach_id'];

            // Get and format state/county parent selection
            $stateparent = isset($term_meta['stateparent']) ? $term_meta['stateparent'] : '';
            $stateparent = wpestate_get_all_states($stateparent);
        } else {
            // Set defaults for new terms
            $pagetax = '';
            $stateparent = wpestate_get_all_states();
            $category_featured_image = '';
            $category_tagline = '';
            $category_attach_id = '';
        }

        // Output the edit form HTML
        print'
        <table class="form-table">
        <tbody>
            <tr class="form-field">
                <th scope="row" valign="top"><label for="term_meta[stateparent]">' . esc_html__('Which county / state has this city', 'wpresidence-core') . '</label></th>
                <td>
                    <select name="term_meta[stateparent]" class="postform wpresidence-2025-select">
                     ' . $stateparent . '
                        </select>
                    <p class="description">' . esc_html__('County / State that has this city', 'wpresidence-core') . '</p>
                </td>
            </tr>

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

                <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_city" />

            </tr>
        </tbody>
        </table>';
    }
endif;

if (!function_exists('wpestate_property_city_callback_add_function')):
    /**
     * Displays fields for adding a new property city term
     * 
     * Creates form fields for:
     * - Selecting parent state/county
     * - Setting page ID
     * - Uploading featured image
     * - Adding city tagline
     *
     * @param object $tag The term being created
     * @return void Outputs HTML form fields
     */
    function wpestate_property_city_callback_add_function($tag) {
        // Get existing values if term exists (rare for add form)
        if (is_object($tag)) {
            $t_id = $tag->term_id;
            $term_meta = get_option("taxonomy_$t_id");
            $term_meta_array = wpestate_parse_category_term_array($term_meta);
            $pagetax = $term_meta_array['pagetax'];
            $category_featured_image = $term_meta_array['category_featured_image'];
            $category_tagline = $term_meta_array['category_tagline'];
            $category_attach_id = $term_meta_array['category_attach_id'];

            $stateparent = isset($term_meta['stateparent']) ? $term_meta['stateparent'] : '';
        } else {
            // Set empty defaults for new terms
            $pagetax = '';
            $stateparent = wpestate_get_all_states();
            $category_featured_image = '';
            $category_tagline = '';
            $category_attach_id = '';
        }

        // Output the add form HTML
        print'
            <div class="form-field">
            <label for="term_meta[stateparent]">' . esc_html__('Which county / state has this city', 'wpresidence-core') . '</label>
                <select name="term_meta[stateparent]" class="postform wpresidence-2025-select">
                    ' . $stateparent . '
                </select>
            </div>
            ';

        print'
        <div class="form-field">
        <label for="term_meta[pagetax]">' . esc_html__('Page id for this term', 'wpresidence-core') . '</label>
            <input type="text" name="term_meta[pagetax]" class="postform wpresidence-2025-input" value="' . $pagetax . '">
        </div>

        <div class="form-field">
            <label for="term_meta[pagetax]">' . esc_html__('Featured Image', 'wpresidence-core') . '</label>
            <input id="category_featured_image" type="text" size="36" class="wpestate_landing_upload wpresidence-2025-input" name="term_meta[category_featured_image]" value="' . $category_featured_image . '" />
            <input id="category_featured_image_button" type="button"  class="upload_button button category_featured_image_button" value="' . esc_html__('Upload Image', 'wpresidence-core') . '" />
           <input id="category_attach_id" type="hidden" size="36" class="wpestate_landing_upload_id" name="term_meta[category_attach_id]" value="' . $category_attach_id . '" />
        </div>

        <div class="form-field">
        <label for="term_meta[category_tagline]">' . esc_html__('Category Tagline', 'wpresidence-core') . '</label>
            <input id="category_tagline" type="text" size="36" class="wpresidence-2025-input" name="term_meta[category_tagline]" value="' . $category_tagline . '" />
        </div>
        <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_city" />
        ';
    }
endif;