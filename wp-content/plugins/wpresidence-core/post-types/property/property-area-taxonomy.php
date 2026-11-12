<?php
/**
 * Property Area Taxonomy Custom Fields
 *
 * This file handles the custom fields for the property_area taxonomy.
 * It manages the relationship between areas and cities, and handles additional
 * metadata like featured images, page associations, and taglines for areas.
 *
 * @package WPResidence
 * @subpackage Core
 * @since 1.0
 */

// Register callbacks for area taxonomy form fields and saving
add_action('property_area_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_area_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_area', 'wpestate_property_area_save_extra_fields_callback', 10, 2);
add_action('edited_property_area', 'wpestate_property_area_save_extra_fields_callback', 10, 2);

if (!function_exists('wpestate_property_area_callback_function')):
    /**
     * Renders the edit form fields for property areas
     * 
     * Displays the form fields when editing an existing property area, including:
     * - Parent city selection
     * - Page ID association
     * - Featured image upload
     * - Category tagline
     * - Hidden taxonomy identifier
     *
     * @param object $tag The term being edited
     * @return void
     */
    function wpestate_property_area_callback_function($tag) {
        if (is_object($tag)) {
            // Get existing term metadata
            $t_id = $tag->term_id;
            $term_meta = get_option("taxonomy_$t_id");
            
            // Get city parent relationship
            if (isset($term_meta['cityparent']) && $term_meta['cityparent'] != '') {
                $cityparent = $term_meta['cityparent'];
            } else {
                $cityparent = '';
            }
            
            // Initialize metadata fields
            $pagetax = '';
            $category_featured_image = '';
            $category_tagline = '';
            $category_attach_id = '';
            
            // Get page association
            if (isset($term_meta['pagetax'])) {
                $pagetax = $term_meta['pagetax'] ? $term_meta['pagetax'] : '';
            }

            // Get featured image
            if (isset($term_meta['category_featured_image'])) {
                $category_featured_image = $term_meta['category_featured_image'] ? $term_meta['category_featured_image'] : '';
            }

            // Get tagline
            if (isset($term_meta['category_tagline'])) {
                $category_tagline = $term_meta['category_tagline'] ? $term_meta['category_tagline'] : '';
            }

            $category_tagline = stripslashes($category_tagline);
            
            // Get image attachment ID
            if (isset($term_meta['category_attach_id'])) {
                $category_attach_id = $term_meta['category_attach_id'] ? $term_meta['category_attach_id'] : '';
            }

            // Get city selection dropdown
            $cityparent = wpestate_get_all_cities($cityparent);
        } else {
            // Initialize empty values for new term
            $cityparent = wpestate_get_all_cities();
            $pagetax = '';
            $category_featured_image = '';
            $category_tagline = '';
            $category_attach_id = '';
        }

        // Output form structure
        print'
            <table class="form-table">
            <tbody>
                    <tr class="form-field">
                            <th scope="row" valign="top"><label for="term_meta[cityparent]">' . esc_html__('Which city has this area', 'wpresidence-core') . '</label></th>
                            <td>
                                <select name="term_meta[cityparent]" class="postform wpresidence-2025-select">
                                 ' . $cityparent . '
                                    </select>
                                <p class="description">' . esc_html__('City that has this area', 'wpresidence-core') . '</p>
                            </td>
                    </tr>

                   <tr class="form-field">
                            <th scope="row" valign="top"><label for="term_meta[pagetax]">' . esc_html__('Page id for this term', 'wpresidence-core') . '</label></th>
                            <td>
                                <input type="text" name="term_meta[pagetax]" class="postform wpresidence-2025-input" value="' . $pagetax . '">
                                <p class="description">' . esc_html__('Page id for this term', 'wpresidence-core') . '</p>
                            </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="logo_image">' . esc_html__('Featured Image', 'wpresidence-core') . '</label></th>
                        <td>
                            <input id="category_featured_image" type="text" class="wpestate_landing_upload wpresidence-2025-input"  size="36" name="term_meta[category_featured_image]" value="' . $category_featured_image . '" />
                            <input id="category_featured_image_button" type="button"  class="upload_button button category_featured_image_button" value="' . esc_html__('Upload Image', 'wpresidence-core') . '" />
                            <input id="category_attach_id" type="hidden" class="wpestate_landing_upload_id" size="36" name="term_meta[category_attach_id]" value="' . $category_attach_id . '" />
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label for="term_meta[category_tagline]">' . esc_html__('Category Tagline', 'wpresidence-core') . '</label></th>
                        <td>
                          <input id="category_featured_image" type="text" size="36" class="wpresidence-2025-input" name="term_meta[category_tagline]" value="' . $category_tagline . '" />
                        </td>
                    </tr>


                    <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_area" />




              </tbody>
             </table>';
    }
endif;

if (!function_exists('wpestate_property_area_callback_add_function')):
    /**
     * Renders the add new form fields for property areas
     * 
     * Displays the form fields when adding a new property area, including:
     * - Parent city selection
     * - Page ID association
     * - Featured image upload
     * - Category tagline
     *
     * @param object $tag The term being added
     * @return void
     */
    function wpestate_property_area_callback_add_function($tag) {
        // Get metadata for existing term or initialize new values
        if (is_object($tag)) {
            $t_id = $tag->term_id;
            $term_meta = get_option("taxonomy_$t_id");
            $cityparent = $term_meta['cityparent'] ? $term_meta['cityparent'] : '';
            $pagetax = $term_meta['pagetax'] ? $term_meta['pagetax'] : '';
            $category_featured_image = $term_meta['category_featured_image'] ? $term_meta['category_featured_image'] : '';
            $category_tagline = $term_meta['category_tagline'] ? $term_meta['category_tagline'] : '';
            $category_attach_id = $term_meta['category_attach_id'] ? $term_meta['category_attach_id'] : '';
        } else {
            $cityparent = wpestate_get_all_cities();
            $pagetax = '';
            $category_featured_image = '';
            $category_tagline = '';
            $category_attach_id = '';
        }

        // Output form structure for new term
        print'
            <div class="form-field">
            <label for="term_meta[cityparent]">' . esc_html__('Which city has this area', 'wpresidence-core') . '</label>
                <select name="term_meta[cityparent]" class="postform wpresidence-2025-select">
                    ' . $cityparent . '
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
                <input id="category_featured_image" type="text" class="wpestate_landing_upload wpresidence-2025-input"  size="36" name="term_meta[category_featured_image]" value="' . $category_featured_image . '" />
                <input id="category_featured_image_button" type="button"  class="upload_button button category_featured_image_button" value="' . esc_html__('Upload Image', 'wpresidence-core') . '" />
                <input id="category_attach_id" type="hidden" class="wpestate_landing_upload_id" size="36" name="term_meta[category_attach_id]" value="' . $category_attach_id . '" />

            </div>


            <div class="form-field">
            <label for="term_meta[category_tagline]">' . esc_html__('Category Tagline', 'wpresidence-core') . '</label>
                <input id="category_featured_image" type="text" size="36" class="wpresidence-2025-input" name="term_meta[category_tagline]" value="' . $category_tagline . '" />
            </div>
            <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_area" />
            ';
    }
endif;

if (!function_exists('wpestate_property_area_save_extra_fields_callback')):
    /**
     * Saves the custom fields data for property areas
     * 
     * Handles the saving of custom field data when a term is created or edited.
     * Sanitizes input data and updates the term metadata in the database.
     *
     * @param int $term_id The ID of the term being saved
     * @return void
     */
    function wpestate_property_area_save_extra_fields_callback($term_id) {
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
            //save the option array
            update_option("taxonomy_$t_id", $term_meta);
        }
    }
endif;