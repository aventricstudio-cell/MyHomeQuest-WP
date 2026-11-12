<?php
/**
 * Property Status Taxonomy Management
 *
 * Handles the property status taxonomy including custom fields for:
 * - Associated page IDs
 * - Featured images
 * - Category taglines
 * 
 * Provides functionality for:
 * - Displaying custom fields in edit forms
 * - Displaying custom fields in add forms
 * - Saving custom field data
 * 
 * Property statuses are used to indicate listing states like
 * 'For Sale', 'For Rent', 'Sold', etc.
 *
 * @package WpResidence
 * @subpackage Taxonomies
 * @since 1.0
 */

// Register callbacks for managing custom fields in the taxonomy UI
// Use the generic property category callbacks for the status taxonomy
add_action('property_status_edit_form_fields', 'wpestate_property_category_callback_function', 10, 2);
add_action('property_status_add_form_fields', 'wpestate_property_category_callback_add_function', 10, 2);
add_action('created_property_status', 'wpestate_property_status_save_extra_fields_callback', 10, 2);
add_action('edited_property_status', 'wpestate_property_status_save_extra_fields_callback', 10, 2);

if (!function_exists('wpestate_property_status_callback_function')):
    /**
     * Displays custom fields when editing a property status term
     * 
     * Adds form fields for:
     * - Associated page ID (for linking to custom landing pages)
     * - Featured image upload and management
     * - Category tagline text
     * 
     * Retrieves existing values for editing if term exists.
     *
     * @param object $tag The term object being edited
     * @return void Outputs HTML form fields
     */
    function wpestate_property_status_callback_function($tag) {
        // Get existing values if editing a term
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
            // Set defaults for new terms
            $pagetax = '';
            $category_featured_image = '';
            $category_tagline = '';
            $category_attach_id = '';
        }

        // Render edit form fields
        echo '
        <table class="form-table">
        <tbody>
            <!-- Page ID association -->
            <tr class="form-field">...</tr>
            <!-- Featured image upload -->
            <tr valign="top">...</tr>
            <!-- Category tagline -->
            <tr valign="top">...</tr>
            <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_status" />
        </tbody>
        </table>';
    }
endif;

if (!function_exists('wpestate_property_status_callback_add_function')):
    /**
     * Displays custom fields when adding a new property status term
     * 
     * Creates form fields for new terms including:
     * - Page ID association
     * - Featured image upload
     * - Category tagline
     * 
     * Uses simplified form layout appropriate for add interface.
     *
     * @param object $tag The term object (empty for new terms)
     * @return void Outputs HTML form fields
     */
    function wpestate_property_status_callback_add_function($tag) {
        // Initialize empty values for new term
        $pagetax = $category_featured_image = $category_tagline = $category_attach_id = '';

        // Render add form fields
        echo '
        <!-- Page ID field -->
        <div class="form-field">...</div>
        <!-- Featured image upload -->
        <div class="form-field">...</div>
        <!-- Category tagline -->  
        <div class="form-field">...</div>
        <!-- Hidden taxonomy identifier -->
        <input id="category_tax" type="hidden" size="36" name="term_meta[category_tax]" value="property_status" />';
    }
endif;

if (!function_exists('wpestate_property_status_save_extra_fields_callback')):
    /**
     * Saves custom field data for property status terms
     * 
     * Handles saving metadata when terms are created or edited including:
     * - Sanitizing input data
     * - Updating term meta in options table
     * - Managing featured image associations
     *
     * @param int $term_id The ID of the term being saved
     * @return void
     */
    function wpestate_property_status_save_extra_fields_callback($term_id) {
        // Check if we have term meta to save
        if (isset($_POST['term_meta'])) {
            $t_id = $term_id;
            $term_meta = get_option("taxonomy_$t_id");
            $cat_keys = array_keys($_POST['term_meta']);
            $allowed_html = array();
            
            // Process and sanitize each meta field
            foreach ($cat_keys as $key) {
                $key = sanitize_key($key);
                if (isset($_POST['term_meta'][$key])) {
                    $term_meta[$key] = wp_kses($_POST['term_meta'][$key], $allowed_html);
                }
            }
            // Save updated term meta
            update_option("taxonomy_$t_id", $term_meta);
        }
    }
endif;