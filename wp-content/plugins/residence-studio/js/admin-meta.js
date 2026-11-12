jQuery(document).ready(function (jQuery) {
    function attachDeleteHandlers(scope) {
        scope.find('.wpestate-remove-location').off('click').on('click', function (e) {
            e.preventDefault();
            jQuery(this).closest('.wpestate-location-row').remove();
        });
    }

    function filterTaxOptions(row) {
        var taxonomy = row.find('.wpestate-selection_dropdown option:selected').data('taxonomy');
        var termSelect = row.find('.wpestate-tax-term');

        if (!termSelect.length) {
            return;
        }

        termSelect.find('option').each(function () {
            var optTax = jQuery(this).data('taxonomy');

            if (!taxonomy || !optTax || jQuery(this).val() === '' || taxonomy === optTax) {
                jQuery(this).show();
            } else {
                jQuery(this).hide();
            }
        });

        if (termSelect.find('option:selected').css('display') === 'none') {
            termSelect.val('');
        }
    }

    attachDeleteHandlers(jQuery('#wpestate-location-container'));
    attachDeleteHandlers(jQuery('#wpestate-exclude-location-container'));

    jQuery('#wpestate-add-location').on('click', function (e) {
        e.preventDefault();
        var container = jQuery('#wpestate-location-container');
        var firstRow = container.find('.wpestate-location-row').first();
        if (firstRow.length) {
            var clone = firstRow.clone();
            var selects = clone.find('select');
            if (selects.length) {
                selects.first().val('disabled');
                if (selects.length > 1) {
                    selects.last().val('');
                }
            }
            container.append(clone);
            attachDeleteHandlers(clone);
            filterTaxOptions(clone);
        }
    });

    jQuery('#wpestate-add-exclude-location').on('click', function (e) {
        e.preventDefault();
        var container = jQuery('#wpestate-exclude-location-container');
        var firstRow = container.find('.wpestate-location-row').first();
        if (firstRow.length) {
            var clone = firstRow.clone();
            var selects = clone.find('select');
            if (selects.length) {
                selects.first().val('disabled');
                if (selects.length > 1) {
                    selects.last().val('');
                }
            }
            container.append(clone);
            attachDeleteHandlers(clone);
            filterTaxOptions(clone);
        }
    });

    jQuery(document).on('change', '.wpestate-selection_dropdown', function () {
        var row = jQuery(this).closest('.wpestate-location-row');
        filterTaxOptions(row);
    });

    jQuery('.wpestate-location-row').each(function () {
        filterTaxOptions(jQuery(this));
    });


    jQuery('#template-type-select').change(function() {
        // Get data-template from selected option
        var selectedTemplate = jQuery(this).find('option:selected').data('template');
        
        // Loop through all location dropdown options
        jQuery('.wpestate-selection_dropdown option').each(function() {
            var optionTemplate = jQuery(this).data('template');
            var hasTaxonomy  = jQuery(this).data('taxonomy');

            if (selectedTemplate === 'category') {
                if (hasTaxonomy) {
                    jQuery(this).show();
                } else {
                    jQuery(this).hide();
                }
            } else if (!selectedTemplate || optionTemplate === selectedTemplate || !optionTemplate) {
                jQuery(this).show();
            } else {
                jQuery(this).hide();
            }
        });


            
        // Now handle optgroups
        jQuery('.wpestate-selection_dropdown optgroup').each(function() {
                var allOptionsHidden = true;

                jQuery(this).children('option').each(function() {
                    if (jQuery(this).css('display') !== 'none') {
                 
                        allOptionsHidden = false;
                
                    }
                });

                if (allOptionsHidden) {
                    jQuery(this).hide();
                } else {
                    jQuery(this).show();
                }
        });

        // Refresh term dropdowns visibility based on filtered taxonomies
        jQuery('.wpestate-location-row').each(function(){
            filterTaxOptions(jQuery(this));
        });





    });
    


    
    // Run on page load
    jQuery('#template-type-select').trigger('change');
});
