(function(jQuery){
    /**
     * WordPress Term Custom Fields JavaScript Handler
     * 
     * Manages the dynamic interface for creating, assigning, and removing
     * custom fields from taxonomy terms. Handles AJAX operations and
     * real-time UI updates.
     */
    function wpestateTermCustomFields(){
        // Cache frequently used jQuery elements
        var typeSelect = jQuery('#wpestate-new-field-type');
        var optionsRow = jQuery('#wpestate-new-field-options-row');
        var ajaxNonce  = jQuery('#wpestate-save-new-field').data('nonce');
        var taxonomy   = jQuery('#wpestate-term-field-taxonomy').val();
        var termId     = parseInt(jQuery('#wpestate-term-field-term-id').val()) || 0;

        /**
         * Apply consistent styling to WordPress admin interface
         * Wraps content and applies theme-specific CSS classes
         */
        function applyBaseStyles(){
            var wrap = jQuery('.wrap');

            // Add main wrapper class for consistent styling
            wrap.addClass('wpresidence2025-content-wrapper');

            // Create header structure if it doesn't exist
            if(!wrap.children('.wpresidence2025-content-wrapper-header').length){
                var title = wrap.children('h1').first();
                if(title.length){
                    // Wrap title in header div and add separator
                    title.wrap('<div class="wpresidence2025-content-wrapper-header"></div>');
                    title.parent().after('<hr>');
                    // Wrap remaining content in content box
                    title.parent().nextAll().wrapAll('<div class="wpresidence2025-content-wrapper-inside-box"></div>');
                }
            }

            // Apply consistent form element styling
            wrap.find('label').addClass('wpresidence-label');
            wrap.find('select').addClass('wpresidence-2025-select');
            wrap.find('input[type="text"], input[type="number"], input[type="password"], textarea').addClass('wpresidence-2025-input');
            jQuery('#parent').addClass('wpresidence-2025-select'); // WordPress parent term selector
        }

        // Initialize styling
        applyBaseStyles();

        /**
         * Show/hide options field based on selected field type
         * Options are only needed for 'select' type fields
         */
        function toggleOptionsRow(){
            if(typeSelect.val() === 'select'){
                optionsRow.show();
            }else{
                optionsRow.hide();
            }
        }

        // Initialize options row visibility and bind change handler
        toggleOptionsRow();
        typeSelect.on('change', toggleOptionsRow);

        /**
         * Handle new field creation
         * Validates input, sends AJAX request, updates UI on success
         */
        jQuery('#wpestate-save-new-field').on('click', function(e){
            e.preventDefault();
            
            // Gather form data
            var name    = jQuery('#wpestate-new-field-name').val();
            var type    = typeSelect.val();
            var options = jQuery('#wpestate-new-field-options').val();
            var nonce   = jQuery(this).data('nonce');

            // Validate required field name
            if(!name.trim()){
                alert('Field name is required');
                return;
            }

            // Send AJAX request to create field definition
            jQuery.post(ajaxurl, {
                action: 'wpestate_save_term_custom_field_definition',
                nonce: nonce,
                taxonomy: taxonomy,
                term_id: termId,
                name: name,
                type: type,
                options: options
            }, function(response){
                if(response.success){
                    // Add new field to the form
                    appendFieldRow(response.data);
                    clearNewFieldForm();
                    
                    // Remove field from existing fields dropdown since it's now assigned
                    jQuery('#wpestate-existing-term-fields option[value="'+response.data.slug+'"]').remove();
                    
                    // Hide existing fields section if no more fields available
                    if(jQuery('#wpestate-existing-term-fields option').length <= 1){
                        hideExistingFieldsSection();
                    }
                }else{
                    alert(response.data || 'An error occurred');
                }
            });
        });

        /**
         * Handle assignment of existing field to current term
         * Validates selection, sends AJAX request, updates UI
         */
        jQuery('#wpestate-assign-existing-field').on('click', function(e){
            e.preventDefault();
            
            var slug = jQuery('#wpestate-existing-term-fields').val();
            var nonce = jQuery(this).data('nonce');

            // Validate field selection
            if(!slug){
                alert('Please select a field');
                return;
            }

            // Can't assign fields to unsaved terms
            if(!termId){
                alert('Cannot assign field to new term. Please save the term first.');
                return;
            }

            // Send AJAX request to assign existing field
            jQuery.post(ajaxurl, {
                action: 'wpestate_assign_existing_field_to_term',
                nonce: nonce,
                term_id: termId,
                slug: slug
            }, function(response){
                if(response.success){
                    // Add field to form
                    appendFieldRow(response.data);
                    
                    // Remove from dropdown and reset selection
                    jQuery('#wpestate-existing-term-fields option[value="'+slug+'"]').remove();
                    jQuery('#wpestate-existing-term-fields').val('');
                    
                    // Hide section if no more fields available
                    if(jQuery('#wpestate-existing-term-fields option').length <= 1){
                        hideExistingFieldsSection();
                    }
                }else{
                    alert(response.data || 'An error occurred');
                }
            });
        });

        /**
         * Handle field removal from current term
         * Uses event delegation since fields are added dynamically
         */
        jQuery('#wpestate-term-custom-fields').on('click', '.wpresidence-delete-term-field', function(e){
            e.preventDefault();
            
            var btn      = jQuery(this);
            var slug     = btn.data('slug');
            var nonce    = btn.data('nonce');
            var row      = btn.closest('.wpresidence_tcf-row');
            var fieldName = row.find('label').text();

            // Confirm before removing
            if(!confirm('Remove this field from the term?')){
                return;
            }

            // Send AJAX request to remove field assignment
            jQuery.post(ajaxurl, {
                action: 'wpestate_delete_term_custom_field_definition',
                nonce: nonce,
                term_id: termId,
                slug: slug
            }, function(response){
                if(response.success){
                    // Remove field row from form
                    row.remove();
                    
                    // Add field back to existing fields dropdown for reuse
                    addFieldToExistingDropdown(slug, fieldName);
                }else{
                    alert(response.data || 'An error occurred');
                }
            });
        });

        /**
         * Reset the new field creation form to default state
         */
        function clearNewFieldForm(){
            jQuery('#wpestate-new-field-name').val('');
            jQuery('#wpestate-new-field-options').val('');
            typeSelect.val('text');
            toggleOptionsRow();
        }

        /**
         * Hide the existing fields section when no fields are available
         */
        function hideExistingFieldsSection(){
            var existingSelect = jQuery('#wpestate-existing-term-fields');
            existingSelect.closest('.form-field').prev('hr').hide();
            existingSelect.closest('.form-field').prev('h4').hide();
            existingSelect.closest('.form-field').hide();
            jQuery('#wpestate-assign-existing-field').closest('.form-field').hide();
        }

        /**
         * Show the existing fields section when fields become available
         */
        function showExistingFieldsSection(){
            var existingSelect = jQuery('#wpestate-existing-term-fields');
            existingSelect.closest('.form-field').prev('hr').show();
            existingSelect.closest('.form-field').prev('h4').show();
            existingSelect.closest('.form-field').show();
            jQuery('#wpestate-assign-existing-field').closest('.form-field').show();
        }

        /**
         * Add a field back to the existing fields dropdown
         * Used when a field is removed from a term
         * 
         * @param {string} slug - Field slug value
         * @param {string} fieldName - Display name for the field
         */
        function addFieldToExistingDropdown(slug, fieldName){
            var existingSelect = jQuery('#wpestate-existing-term-fields');
            if(existingSelect.length){
                // Show the existing fields section if it was hidden
                showExistingFieldsSection();
                
                // Add option back to select
                existingSelect.append('<option value="'+slug+'">'+fieldName+'</option>');
                
                // Sort options alphabetically (excluding first "Select..." option)
                var options = existingSelect.find('option:not(:first)').sort(function(a, b) {
                    return jQuery(a).text().localeCompare(jQuery(b).text());
                });
                existingSelect.find('option:not(:first)').remove();
                existingSelect.append(options);
            }
        }

        /**
         * Dynamically create and append a field row to the form
         * Generates appropriate input element based on field type
         * 
         * @param {Object} field - Field definition object with name, slug, type, options
         */
        function appendFieldRow(field){
            var table = jQuery('#wpestate-term-custom-fields');
            var row = jQuery('<div class="wpresidence_tcf-row prop_full">');
            
            // Create label column with field name and hidden metadata
            var labelCol = jQuery('<div class="wpresidence_tcf-col wpresidence_tcf-label">');
            labelCol.append(jQuery('<label class="wpresidence-label">').text(field.name));
            labelCol.append('<input type="hidden" name="wpestate_term_custom_fields['+field.slug+'][name]" value="'+escapeHtml(field.name)+'" />');
            labelCol.append('<input type="hidden" name="wpestate_term_custom_fields['+field.slug+'][type]" value="'+escapeHtml(field.type)+'" />');
            labelCol.append('<input type="hidden" name="wpestate_term_custom_fields['+field.slug+'][slug]" value="'+escapeHtml(field.slug)+'" />');

            // Generate input HTML based on field type
            var inputHtml = '';
            if(field.type === 'number'){
                inputHtml = '<input type="number" class="wpresidence-2025-input" name="wpestate_term_custom_fields['+field.slug+'][value]" />';
            }else if(field.type === 'textarea'){
                // Simple textarea for dynamically added fields (wp_editor not available via JS)
                inputHtml = '<textarea class="wpresidence-2025-input" name="wpestate_term_custom_fields['+field.slug+'][value]" rows="4" cols="40"></textarea>';
            }else if(field.type === 'select'){
                // Build select dropdown from comma-separated options
                inputHtml = '<select class="wpresidence-2025-select" name="wpestate_term_custom_fields['+field.slug+'][value]">';
                inputHtml += '<option value="">Select...</option>';
                var options = field.options ? field.options.split(',') : [];
                jQuery.each(options, function(i, op){
                    op = jQuery.trim(op);
                    if(op !== ''){
                        inputHtml += '<option value="'+escapeHtml(op)+'">'+escapeHtml(op)+'</option>';
                    }
                });
                inputHtml += '</select>';
            }else{
                // Default to text input
                inputHtml = '<input type="text" class="wpresidence-2025-input" name="wpestate_term_custom_fields['+field.slug+'][value]" />';
            }
            
            // Create input column
            var inputCol = jQuery('<div class="wpresidence_tcf-col wpresidence_tcf-input">');
            inputCol.append(inputHtml);

            // Create delete button column
            var deleteCol = jQuery('<div class="wpresidence_tcf-col wpresidence_tcf-delete">');
            var btn = jQuery('<button type="button" class="button secondary wpresidence-delete-term-field">Remove from Term</button>');
            btn.attr('data-slug', field.slug).attr('data-nonce', ajaxNonce);
            deleteCol.append(btn);

            // Assemble and append row
            row.append(labelCol).append(inputCol).append(deleteCol);
            table.append(row);
        }

        /**
         * Escape HTML characters to prevent XSS attacks
         * Used when inserting user-generated content into HTML
         * 
         * @param {string} text - Text to escape
         * @return {string} HTML-escaped text
         */
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    }

    // Expose function globally and initialize when DOM is ready
window.wpestateTermCustomFields = wpestateTermCustomFields;
jQuery(wpestateTermCustomFields);
})(jQuery);

jQuery(function($){
    var frame;
    $(document).on('click', '.term-geojson-upload-button', function(e){
        e.preventDefault();
        if(frame){
            frame.open();
            return;
        }
        frame = wp.media({
            title: wpresidenceGeoJSON.title,
            button: { text: wpresidenceGeoJSON.button },
            library: { type: 'application/json' },
            multiple: false,
            uploader: {
                params: {
                    wpestate_geojson_upload: 1
                }
            }
        });
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            $('#term_geojson').val(attachment.url);
        });
        frame.open();
    });
});