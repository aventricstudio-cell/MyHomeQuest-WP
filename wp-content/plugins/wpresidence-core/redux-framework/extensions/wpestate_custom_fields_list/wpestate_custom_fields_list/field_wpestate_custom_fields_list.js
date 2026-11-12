/* global confirm, redux, redux_change */
jQuery(document).ready(function($) {
    var container = $('#custom_fields_wrapper');
    var addFieldButton = $('#add_field2');
    
    function updateOrderInputs() {
        container.find('.field_row').each(function(index){
            // Update both custom and ACF field order inputs
            $(this).find('input[name*="[add_field_order]"], input[name*="[acf_field_order]"]').val(index + 1);
        });
    }

    function bindDeleteFieldHandlers() {
        container.find('.deletefieldlink').off('click').on('click', function(event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            $(this).closest('.field_row').remove();
            updateOrderInputs();
        });
    }
    
    container.sortable({
        items: '.field_row',
        placeholder: 'sortable-placeholder',
        update: function(){
            updateOrderInputs();
        }
    });
    
    container.on('change', 'input[name*="[add_field_order]"], input[name*="[acf_field_order]"]', function(){
        var rows = container.children('.field_row').get();
        rows.sort(function(a,b){
            var orderInputA = $(a).find('input[name*="[add_field_order]"], input[name*="[acf_field_order]"]');
            var orderInputB = $(b).find('input[name*="[acf_field_order]"], input[name*="[add_field_order]"]');
            var valA = parseInt(orderInputA.val(), 10) || 0;
            var valB = parseInt(orderInputB.val(), 10) || 0;
            return valA - valB;
        });
        container.append(rows);
        updateOrderInputs();
    });

    addFieldButton.off('click.wpestateCustomFieldsListSync').on('click.wpestateCustomFieldsListSync', function() {
        // Let any add-field handlers run first, then rebind local delete logic.
        setTimeout(function() {
            bindDeleteFieldHandlers();
            updateOrderInputs();
        }, 0);
    });

    bindDeleteFieldHandlers();

    // Ensure local delete handler remains final after all document-ready scripts.
    setTimeout(function() {
        bindDeleteFieldHandlers();
        updateOrderInputs();
    }, 0);
});
