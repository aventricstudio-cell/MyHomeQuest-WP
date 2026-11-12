<?php
// Custom Fields for Taxonomy Terms

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
* Register hooks for custom term fields.
* 
* Note: Form rendering hooks are not registered here as custom fields
* are rendered directly inside property_category callbacks elsewhere.
*/
function wpestate_register_term_custom_fields_hooks() {
   // Save custom field values when terms are created or edited
   add_action( 'edited_term', 'wpestate_save_term_custom_fields' , 20 , 3 );
   add_action( 'create_term', 'wpestate_save_term_custom_fields' , 20 , 3 );
   
   // AJAX handlers for field management
   add_action( 'wp_ajax_wpestate_save_term_custom_field_definition', 'wpestate_save_term_custom_field_definition' );
   add_action( 'wp_ajax_wpestate_delete_term_custom_field_definition', 'wpestate_delete_term_custom_field_definition' );
   add_action( 'wp_ajax_wpestate_assign_existing_field_to_term', 'wpestate_assign_existing_field_to_term' );
}
add_action( 'init', 'wpestate_register_term_custom_fields_hooks' );





/**
* Get fields assigned to a specific term.
* 
* @param int $term_id The term ID to get assigned fields for.
* @return array Array of field slugs assigned to the term.
*/
function wpestate_get_term_assigned_fields( $term_id ) {
   $assignments = get_option( 'wpestate_term_field_assignments', array() );
   return isset( $assignments[ $term_id ] ) ? $assignments[ $term_id ] : array();
}

/**
* Assign a field to a term.
* 
* Creates the assignment relationship between a custom field and a term.
* Prevents duplicate assignments and only updates the database if needed.
* 
* @param int    $term_id    The term ID to assign the field to.
* @param string $field_slug The field slug to assign.
*/
function wpestate_assign_field_to_term( $term_id, $field_slug ) {
   $assignments = get_option( 'wpestate_term_field_assignments', array() );
   
   // Initialize term assignments array if it doesn't exist
   if ( ! isset( $assignments[ $term_id ] ) ) {
       $assignments[ $term_id ] = array();
   }
   
   // Only add if not already assigned to avoid duplicates
   if ( ! in_array( $field_slug, $assignments[ $term_id ] ) ) {
       $assignments[ $term_id ][] = $field_slug;
       update_option( 'wpestate_term_field_assignments', $assignments );
   }
}




/**
 * Remove field assignment from a term.
 */
function wpestate_unassign_field_from_term( $term_id, $field_slug ) {
    $assignments = get_option( 'wpestate_term_field_assignments', array() );
    if ( isset( $assignments[ $term_id ] ) ) {
        $assignments[ $term_id ] = array_diff( $assignments[ $term_id ], array( $field_slug ) );
        if ( empty( $assignments[ $term_id ] ) ) {
            unset( $assignments[ $term_id ] );
        }
        update_option( 'wpestate_term_field_assignments', $assignments );
    }
}

/**
 * Render custom fields area and add-field form.
 *
 * Displays custom fields assigned to the current term and provides forms
 * for creating new fields or assigning existing fields to the term.
 *
 * @param WP_Term|string $term Optional term object when editing, empty string when adding new term.
 */
function wpestate_term_custom_fields_render( $term = '' ) {
    // Extract taxonomy and term information
    $taxonomy = '';
    $term_id = 0;
    
    if ( is_object( $term ) && isset( $term->taxonomy ) ) {
        // Editing existing term
        $taxonomy = $term->taxonomy;
        $term_id = $term->term_id;
    } elseif ( isset( $_REQUEST['taxonomy'] ) ) {
        // Adding new term
        $taxonomy = sanitize_key( $_REQUEST['taxonomy'] );
    }
 
    // Get all globally defined custom fields
    $all_fields = get_option( 'wpestate_custom_fields_for_terms', array() );
    
    // Get fields assigned to this specific term
    $assigned_field_slugs = $term_id ? wpestate_get_term_assigned_fields( $term_id ) : array();
    
    // Build array of field definitions for assigned fields only
    $assigned_fields = array();
    foreach ( $assigned_field_slugs as $slug ) {
        if ( isset( $all_fields[ $slug ] ) ) {
            $assigned_fields[ $slug ] = $all_fields[ $slug ];
        }
    }
    
    // Get saved field values for this term
    $values = array();
    if ( $term_id ) {
        $values = get_option( 'taxonomy_' . $term_id, array() );
    }
    
    // Build array of fields available for assignment (not yet assigned to this term)
    $unassigned_fields = array();


    foreach ( $all_fields as $slug => $field ) {
       
            $unassigned_fields[ $slug ] = $field;
       
    }
    ?>
    
    <!-- Hidden inputs for JavaScript to access current taxonomy and term ID -->
    <input type="hidden" id="wpestate-term-field-taxonomy" value="<?php echo esc_attr( $taxonomy ); ?>" />
    <input type="hidden" id="wpestate-term-field-term-id" value="<?php echo esc_attr( $term_id ); ?>" />
    
    <!-- Display assigned custom fields with their current values -->
    <div class="wpresidence_tcf-table" id="wpestate-term-custom-fields">
        <?php foreach ( $assigned_fields as $field ) :
            $slug   = esc_attr( $field['slug'] );
            $name   = esc_attr( $field['name'] );
            $type   = esc_attr( $field['type'] );
            $option = isset( $field['options'] ) ? $field['options'] : '';
            $val    = isset( $values[ $slug ] ) ? $values[ $slug ] : '';
            $editor_id = 'wpestate_term_custom_fields_' . sanitize_key( $slug );
            ?>
            <div class="wpresidence_tcf-row prop_full">
                <!-- Field label and hidden metadata -->
                <div class="wpresidence_tcf-col wpresidence_tcf-label">
                    <label class="wpresidence-label"><?php echo esc_html( $name ); ?></label>
                    <!-- Hidden inputs preserve field metadata for form submission -->
                    <input type="hidden" name="wpestate_term_custom_fields[<?php echo $slug; ?>][name]" value="<?php echo $name; ?>" />
                    <input type="hidden" name="wpestate_term_custom_fields[<?php echo $slug; ?>][type]" value="<?php echo $type; ?>" />
                    <input type="hidden" name="wpestate_term_custom_fields[<?php echo $slug; ?>][slug]" value="<?php echo $slug; ?>" />
                </div>
                
                <!-- Field input - type determines the input element -->
                <div class="wpresidence_tcf-col wpresidence_tcf-input">
                    <?php
                    switch ( $type ) {
                        case 'number':
                            echo '<input type="number" class="wpresidence-2025-input" name="wpestate_term_custom_fields[' . $slug . '][value]" value="' . esc_attr( $val ) . '" />';
                            break;
                        case 'textarea':
                            // Use WordPress editor for rich text editing
                            wp_editor(
                                $val,
                                $editor_id,
                                array(
                                    'textarea_name' => 'wpestate_term_custom_fields[' . $slug . '][value]',
                                    'textarea_rows' => 5,
                                    'media_buttons' => false,
                                    'teeny'         => true,
                                )
                            );
                            break;
                        case 'select':
                            // Parse comma-separated options and build select dropdown
                            $options = array_map( 'trim', explode( ',', $option ) );
                            echo '<select class="wpresidence-2025-select" name="wpestate_term_custom_fields[' . $slug . '][value]">';
                            echo '<option value="">' . esc_html__( 'Select...', 'wpresidence-core' ) . '</option>';
                            foreach ( $options as $op ) {
                                if ( '' === $op ) {
                                    continue;
                                }
                                echo '<option value="' . esc_attr( $op ) . '" ' . selected( $val, $op, false ) . '>' . esc_html( $op ) . '</option>';
                            }
                            echo '</select>';
                            break;
                        default:
                            // Default to text input for 'text' type and fallback
                            echo '<input type="text" class="wpresidence-2025-input" name="wpestate_term_custom_fields[' . $slug . '][value]" value="' . esc_attr( $val ) . '" />';
                            break;
                    }
                    ?>
                </div>
                
                <!-- Remove field assignment button -->
                <div class="wpresidence_tcf-col wpresidence_tcf-delete">
                    <button type="button" class="button secondary wpresidence-delete-term-field" data-slug="<?php echo $slug; ?>" data-nonce="<?php echo wp_create_nonce( 'wpestate_term_custom_fields' ); ?>">
                        <?php esc_html_e( 'Remove from Term', 'wpresidence-core' ); ?>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Form section for creating new fields and assigning existing ones -->
    <div class="form-table wpresidence_add_custom_field">
        <h3><?php esc_html_e( 'Add Custom Field', 'wpresidence-core' ); ?></h3>
        
        <!-- New field name input -->
        <div class="form-field">
            <div class="field-row">
                <div class="field-label">
                    <label class="wpresidence-label" for="wpestate-new-field-name"><?php esc_html_e( 'Name', 'wpresidence-core' ); ?></label>
                </div>
                <div class="field-input">
                    <input type="text" class="wpresidence-2025-input" id="wpestate-new-field-name" />
                </div>
            </div>
        </div>
        
        <!-- Field type selector -->
        <div class="form-field">
            <div class="field-row">
                <div class="field-label">
                    <label class="wpresidence-label" for="wpestate-new-field-type"><?php esc_html_e( 'Type', 'wpresidence-core' ); ?></label>
                </div>
                <div class="field-input">
                    <select id="wpestate-new-field-type" class="wpresidence-2025-select">
                        <option value="text">Small Text</option>
                        <option value="number">Number</option>
                        <option value="select">Select</option>
                        <option value="textarea">Textarea</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Options field (shown only for select type) -->
        <div class="form-field" id="wpestate-new-field-options-row" style="display:none;">
            <div class="field-row">
                <div class="field-label">
                    <label class="wpresidence-label" for="wpestate-new-field-options"><?php esc_html_e( 'Options (comma separated)', 'wpresidence-core' ); ?></label>
                </div>
                <div class="field-input">
                    <textarea id="wpestate-new-field-options" class="wpresidence-2025-input" rows="3" cols="40"></textarea>
                </div>
            </div>
        </div>
        
        <!-- Create field button -->
        <div class="form-field">
            <div class="field-row">
                <div class="field-label">&nbsp;</div>
                <div class="field-input">
                    <button type="button" class="button button-primary secondary" id="wpestate-save-new-field" data-nonce="<?php echo wp_create_nonce( 'wpestate_term_custom_fields' ); ?>">
                        <?php esc_html_e( 'Create Field', 'wpresidence-core' ); ?>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Section for assigning existing fields (only shown if unassigned fields exist) -->
        <?php 
        
       
        if ( ! empty( $unassigned_fields ) ) : ?>
        <hr style="margin: 20px 0;" />
        <h4><?php esc_html_e( 'Use Existing Field', 'wpresidence-core' ); ?></h4>
        
        <!-- Dropdown of available fields -->
        <div class="form-field">
            <div class="field-row">
                <div class="field-label">
                    <label class="wpresidence-label" for="wpestate-existing-term-fields"><?php esc_html_e( 'Fields created for other terms', 'wpresidence-core' ); ?></label>
                </div>
                <div class="field-input">
                    <select id="wpestate-existing-term-fields" class="wpresidence-2025-select">
                        <option value="" selected="selected"><?php esc_html_e( 'Select a field...', 'wpresidence-core' ); ?></option>
                        <?php foreach ( $unassigned_fields as $field ) : ?>
                            <option value="<?php echo esc_attr( $field['slug'] ); ?>">
                                <?php echo esc_html( $field['name'] . ' (' . ucfirst( $field['type'] ) . ')' ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Assign existing field button -->
        <div class="form-field">
            <div class="field-row">
                <div class="field-label">&nbsp;</div>
                <div class="field-input">
                    <button type="button" class="button secondary" id="wpestate-assign-existing-field" data-nonce="<?php echo wp_create_nonce( 'wpestate_term_custom_fields' ); ?>">
                        <?php esc_html_e( 'Add to Term', 'wpresidence-core' ); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
* Save custom field definition via AJAX.
*
* Creates a new custom field definition and optionally assigns it to the current term.
* The field is stored globally so it can be reused across multiple terms.
* Called when user clicks "Create Field" button.
*/
function wpestate_save_term_custom_field_definition() {
   // Verify nonce for security
   check_ajax_referer( 'wpestate_term_custom_fields', 'nonce' );

   // Sanitize and extract POST data
    $taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_key( $_POST['taxonomy'] ) : '';
    $term_id  = isset( $_POST['term_id'] ) ? intval( $_POST['term_id'] ) : 0;
    $name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( urldecode( $_POST['name'] ) ) ) : '';
    $type     = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : 'text';
    $options  = isset( $_POST['options'] ) ? sanitize_textarea_field( wp_unslash( urldecode( $_POST['options'] ) ) ) : '';

   // Validate required field name
   if ( '' === $name ) {
       wp_send_json_error( __( 'Name required', 'wpresidence-core' ) );
   }

   // Generate URL-safe slug from field name for consistent keys and HTML IDs
   $slug = sanitize_title( $name );

   if ( '' === $slug ) {
       $slug = sanitize_key( wp_unique_id( 'tcf_' ) );
   }
   $all_fields = get_option( 'wpestate_custom_fields_for_terms', array() );

   // Store field definition globally for reuse across terms
   $all_fields[ $slug ] = array(
       'name'    => $name,
       'slug'    => $slug,
       'type'    => $type,
       'options' => $options, // Comma-separated options for select fields
   );

   update_option( 'wpestate_custom_fields_for_terms', $all_fields );

   // Auto-assign field to current term if editing existing term
   // For new terms (term_id = 0), assignment happens during term save
   if ( $term_id ) {
       wpestate_assign_field_to_term( $term_id, $slug );
   }

   // Return field definition to JavaScript for UI update
   wp_send_json_success( $all_fields[ $slug ] );
}

/**
* Assign existing field to term via AJAX.
*
* Creates an assignment relationship between an existing field definition
* and the current term. Called when user selects from "existing fields" dropdown
* and clicks "Add to Term" button.
*/
function wpestate_assign_existing_field_to_term() {
   // Verify nonce for security
   check_ajax_referer( 'wpestate_term_custom_fields', 'nonce' );


    // Extract and validate POST data
    $term_id = isset( $_POST['term_id'] ) ? intval( $_POST['term_id'] ) : 0;
    $slug    = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( urldecode( $_POST['slug'] ) ) ) : '';
    
   // Validate required parameters
   if ( ! $term_id || '' === $slug ) {
       wp_send_json_error( __( 'Invalid term or field', 'wpresidence-core' ) );
   }

   // Verify field definition exists
   $all_fields = get_option( 'wpestate_custom_fields_for_terms', array() );
   if ( ! isset( $all_fields[ $slug ] ) ) {
       wp_send_json_error( __( 'Field not found', 'wpresidence-core' ) );
   }

   // Create assignment relationship
   wpestate_assign_field_to_term( $term_id, $slug );

   // Return field definition to JavaScript for UI update
   wp_send_json_success( $all_fields[ $slug ] );
}

/**
* Delete custom field definition via AJAX.
*
* Handles two different scenarios:
* 1. If term_id is provided: Remove field assignment from specific term (field stays available for other terms)
* 2. If no term_id: Permanently delete field definition (only if not used anywhere)
* 
* Called when user clicks "Remove from Term" button.
*/
function wpestate_delete_term_custom_field_definition() {
   // Verify nonce for security
   check_ajax_referer( 'wpestate_term_custom_fields', 'nonce' );

   // Extract and sanitize POST data
   $term_id = isset( $_POST['term_id'] ) ? intval( $_POST['term_id'] ) : 0;
   $slug    = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
   
   // Validate required field slug
   if ( '' === $slug ) {
       wp_send_json_error( __( 'Invalid field', 'wpresidence-core' ) );
   }

   // Scenario 1: Remove assignment from specific term (most common case)
   // Field definition remains available for other terms
   if ( $term_id ) {
        wpestate_unassign_field_from_term( $term_id, $slug );
      

        // Scenario 2: Permanent deletion (only if field not used anywhere)
        // Check if field is assigned to any term before allowing deletion
        if ( !wpestate_term_custom_field_is_used( $slug ) ) {
          
            // Safe to delete - remove from global field definitions
            $all_fields = get_option( 'wpestate_custom_fields_for_terms', array() );
            if ( isset( $all_fields[ $slug ] ) ) {
                unset( $all_fields[ $slug ] );
             
                update_option( 'wpestate_custom_fields_for_terms', $all_fields );
            }
        }



       
       
   }

  


   wp_send_json_success();
}

/**
* Check if a custom field slug exists in any term meta.
*
* Searches through all term field assignments to determine if a field
* is currently assigned to any term. Used to prevent deletion of fields
* that are still in use.
*
* @param string $slug Field slug to check for usage.
* @return bool True if field is assigned to at least one term, false otherwise.
*/
function wpestate_term_custom_field_is_used( $slug ) {
   // Get all term-to-field assignments
   $assignments = get_option( 'wpestate_term_field_assignments', array() );
   
   // Check each term's assigned fields
   foreach ( $assignments as $term_id => $field_slugs ) {
       if ( in_array( $slug, $field_slugs ) ) {
           return true; // Field found in at least one term
       }
   }
   
   return false; // Field not assigned to any term
}

/**
* Save term custom field values on term save.
*
* Processes custom field data submitted via the term edit form.
* Validates field definitions, sanitizes values, ensures field assignments,
* and stores values in term metadata. Called automatically when terms are
* created or updated via WordPress admin.
*
* @param int $term_id The ID of the term being saved.
*/
function wpestate_save_term_custom_fields( $term_id ) {
   // Exit early if no custom field data submitted
   if ( ! isset( $_POST['wpestate_term_custom_fields'] ) ) {
       return;
   }

   $values = array();
   $all_fields = get_option( 'wpestate_custom_fields_for_terms', array() );

   // Process each submitted field
   foreach ( (array) $_POST['wpestate_term_custom_fields'] as $slug => $data ) {
       // Validate field definition exists globally
       if ( ! isset( $all_fields[ $slug ] ) ) {
           continue; // Skip invalid fields
       }

       // Extract and prepare field value
       $value = isset( $data['value'] ) ? wp_unslash( $data['value'] ) : '';
       $type  = isset( $all_fields[ $slug ]['type'] ) ? $all_fields[ $slug ]['type'] : '';

       // Apply appropriate sanitization based on field type
       if ( 'textarea' === $type ) {
           // Allow basic HTML tags for rich text fields
           $values[ $slug ] = wp_kses_post( $value );
       } else {
           // Strip all HTML for other field types
           $values[ $slug ] = sanitize_text_field( $value );
       }

       // Ensure field assignment exists (handles case where field was created but not assigned)
       wpestate_assign_field_to_term( $term_id, $slug );
   }

   // Get existing term metadata to preserve other plugins' data
   $meta = get_option( 'taxonomy_' . $term_id, array() );
   if ( ! is_array( $meta ) ) {
       $meta = array();
   }
  
   // Merge custom field values with existing metadata
   // This preserves data from other features (e.g., featured images, other custom fields)
   $meta = array_merge( $meta, $values );
   update_option( 'taxonomy_' . $term_id, $meta );
}














/**
 * Allow GeoJSON file uploads for administrators only.
 *
 * @param array $existing_mimes Current allowed MIME types.
 * @return array Modified MIME types including geojson support for admins.
 */
function wpestate_allow_geojson_upload( array $existing_mimes ): array {
    if ( ! current_user_can( 'manage_options' ) ) {
        return $existing_mimes;
    }
    
    $existing_mimes['json']    = 'application/json';
    $existing_mimes['geojson'] = 'application/json';
   
    return $existing_mimes;
}

/**
 * Validate GeoJSON file extensions for administrators only.
 *
 * @param array  $data     File data array.
 * @param string $file     Full path to the file.
 * @param string $filename The name of the file.
 * @param array  $mimes    Array of allowed MIME types.
 * @param string $real_mime The actual MIME type of the file.
 * @return array Modified file data with proper extension/type mapping.
 */
function wpestate_check_geojson_filetype( $data, $file, $filename, $mimes, $real_mime ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return $data;
    }
    
    $wp_filetype = wp_check_filetype( $filename, $mimes );
   
    if ( $wp_filetype['ext'] === false ) {
        $file_ext = pathinfo( $filename, PATHINFO_EXTENSION );
        if ( $file_ext === 'geojson' ) {
            $data['ext'] = 'geojson';
            $data['type'] = 'application/json';
        }
    }
   
    return $data;
}

// Hook the functions to WordPress filters
add_filter( 'upload_mimes', 'wpestate_allow_geojson_upload' );
add_filter( 'wp_check_filetype_and_ext', 'wpestate_check_geojson_filetype', 10, 5 );