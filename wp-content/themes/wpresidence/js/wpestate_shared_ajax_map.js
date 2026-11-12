/*global window, jQuery*/
///////////////////////////////////////////////////////////////////////////////////////////
/**
 * wpestate_shared_ajax_map.js
 *
 * Step 1: Create one global namespace for functions shared by ajaxcalls.js and mapfunctions.js.
 * Step 2: Keep this file intentionally small so shared logic is easy to find and maintain.
 * Step 3: New shared functions should be attached to window.wpestateSharedAjaxMap.
 */
(function () {
    "use strict";

    if (!window.wpestateSharedAjaxMap) {
        window.wpestateSharedAjaxMap = {};
    }
})();

/**
 * Resolve selected filter values for start filtering.
 *
 * Step 1: Read control metadata (`data-search-taxonomy`, `data-search-post-meta`, compare).
 * Step 2: Resolve selected values from `data-term-id-selected` or current UI selection.
 * Step 3: Return normalized filter rows for taxonomy or post-meta processing.
 *
 * @param {string} filterSelector Control selector.
 * @returns {Array<Object>} Array of rows with: taxonomy, post_meta, value, compare.
 */
function wpestate_resolve_start_filtering_taxonomy_value(filterSelector) {
    "use strict";

    // Step 1: Resolve target filter control.
    var filterElement = jQuery(filterSelector).first();
    if (filterElement.length === 0) {
        return [];
    }

    // Step 2: Define a local extractor for term id attributes.
    var getTermIdFromElement = function (element) {
        var termIdAttributes = [
            'data-search-term',
            'data-termid',
            'data-term-id',
            'data-term_id',
        ];
        var attributeValue = '';
        var index = 0;

        for (index = 0; index < termIdAttributes.length; index++) {
            attributeValue = element.attr(termIdAttributes[index]);
            if (typeof attributeValue === 'string' && attributeValue.trim() !== '') {
                return attributeValue.trim();
            }
        }

        return '';
    };

    // Step 3: Decide if a value should be treated as empty.
    var isIgnoredValue = function (rawValue) {
        var normalizedValue = String(rawValue || '').trim().toLowerCase();
        return normalizedValue === '' || normalizedValue === 'all' || normalizedValue === '0';
    };

    // Step 4: Build output rows in the requested format.
    var buildFilterRows = function (values, taxonomyName, postMetaName, compareValue) {
        var outputRows = [];

        if (!jQuery.isArray(values)) {
            values = [values];
        }

        values.forEach(function (singleValue) {
            var cleanValue = String(singleValue || '').trim();
            if (isIgnoredValue(cleanValue)) {
                return;
            }

            outputRows.push({
                taxonomy: taxonomyName,
                post_meta: postMetaName,
                value: cleanValue,
                compare: compareValue
            });
        });

        return outputRows;
    };

    // Step 5: Read and normalize the selected term ids attribute from the control.
    var parseSelectedTermIds = function (attributeValue) {
        if (typeof attributeValue !== 'string' || attributeValue.trim() === '') {
            return [];
        }

        var parsedTermIds = [];
        attributeValue.split(',').forEach(function (rawTermId) {
            var cleanTermId = String(rawTermId || '').trim();
            if (!isIgnoredValue(cleanTermId)) {
                parsedTermIds.push(cleanTermId);
            }
        });

        parsedTermIds = parsedTermIds.filter(function (value, arrayIndex, arrayValues) {
            return arrayValues.indexOf(value) === arrayIndex;
        });

        return parsedTermIds;
    };

    // Step 6: Read metadata and selected ids from data attributes.
    var selectedTaxonomyName = String(filterElement.attr('data-search-taxonomy') || '').trim();
    var selectedPostMetaName = String(filterElement.attr('data-search-post-meta') || '').trim();
    var selectedTaxonomyCompare = String(filterElement.attr('data-search-compare') || '').trim();
    if (selectedTaxonomyCompare === '') {
        selectedTaxonomyCompare = '=';
    }
    var selectedTermIdsFromAttribute = parseSelectedTermIds(filterElement.attr('data-term-id-selected') || '');

    // Step 7: For multi-select controls, return selected values.
    if (filterElement.is('select') && filterElement.prop('multiple')) {
        if ((selectedTaxonomyName !== '' || selectedPostMetaName !== '') && selectedTermIdsFromAttribute.length > 0) {
            return buildFilterRows(
                selectedTermIdsFromAttribute,
                selectedTaxonomyName,
                selectedPostMetaName,
                selectedTaxonomyCompare
            );
        }

        var selectedTermIds = [];
        filterElement.find('option:selected').each(function () {
            var selectedOptionElement = jQuery(this);
            var selectedOptionTermId = getTermIdFromElement(selectedOptionElement);
            if (!isIgnoredValue(selectedOptionTermId)) {
                selectedTermIds.push(selectedOptionTermId);
            } else {
                var selectedOptionValue = String(selectedOptionElement.val() || '').trim();
                if (!isIgnoredValue(selectedOptionValue)) {
                    selectedTermIds.push(selectedOptionValue);
                }
            }
        });

        selectedTermIds = selectedTermIds.filter(function (value, arrayIndex, arrayValues) {
            return arrayValues.indexOf(value) === arrayIndex;
        });

        return buildFilterRows(
            selectedTermIds,
            selectedTaxonomyName,
            selectedPostMetaName,
            selectedTaxonomyCompare
        );
    }

    // Step 8: For single-value controls, prefer selected value from data-term-id-selected.
    if ((selectedTaxonomyName !== '' || selectedPostMetaName !== '') && selectedTermIdsFromAttribute.length > 0) {
        return buildFilterRows(
            [selectedTermIdsFromAttribute[0]],
            selectedTaxonomyName,
            selectedPostMetaName,
            selectedTaxonomyCompare
        );
    }

    // Step 9: Read selected raw value from single-value controls.
    var selectedValue = '';
    if (filterElement.is('select')) {
        selectedValue = filterElement.val();
        if (jQuery.isArray(selectedValue)) {
            selectedValue = selectedValue.length > 0 ? selectedValue[0] : '';
        }
    } else {
        selectedValue = filterElement.attr('data-value') || filterElement.val() || '';
    }

    selectedValue = String(selectedValue || '').trim();
    if (isIgnoredValue(selectedValue)) {
        return [];
    }

    // Step 10: Prefer term id directly from the control.
    var termIdValue = getTermIdFromElement(filterElement);
    if (termIdValue !== '') {
        return buildFilterRows(
            [termIdValue],
            selectedTaxonomyName,
            selectedPostMetaName,
            selectedTaxonomyCompare
        );
    }

    // Step 11: For selects, prefer selected option term id when available.
    if (filterElement.is('select')) {
        var selectedOption = filterElement.find('option:selected').first();
        if (selectedOption.length > 0) {
            termIdValue = getTermIdFromElement(selectedOption);
            if (termIdValue !== '') {
                return buildFilterRows(
                    [termIdValue],
                    selectedTaxonomyName,
                    selectedPostMetaName,
                    selectedTaxonomyCompare
                );
            }
        }
    }

    // Step 12: For dropdown buttons, resolve term id from matching list item.
    var selectedListItem = filterElement
        .closest('.wpresidence_dropdown')
        .find('li[data-value="' + selectedValue + '"]')
        .first();

    if (selectedListItem.length === 0) {
        var controlId = filterElement.attr('id') || '';
        if (controlId !== '') {
            selectedListItem = jQuery('ul[aria-labelledby="' + controlId + '"] li[data-value="' + selectedValue + '"]').first();
        }
    }

    if (selectedListItem.length > 0) {
        termIdValue = getTermIdFromElement(selectedListItem);
        if (termIdValue !== '') {
            return buildFilterRows(
                [termIdValue],
                selectedTaxonomyName,
                selectedPostMetaName,
                selectedTaxonomyCompare
            );
        }
    }

    // Step 13: For post-meta controls, fallback to visible selected value.
    if (selectedPostMetaName !== '') {
        return buildFilterRows(
            [selectedValue],
            selectedTaxonomyName,
            selectedPostMetaName,
            selectedTaxonomyCompare
        );
    }

    // Step 14: Do not fallback to slug/text values for taxonomy payloads.
    return [];
}



/** 
 * Collect selected property feature term ids from extended search checkboxes.
 *
 * Reads only checkboxes marked with `data-search-taxonomy="property_features"`
 * and returns unique feature identifiers. It prefers `data-term-id`, but for
 * translated or non-Latin terms that do not expose a term id it falls back to
 * the visible `name-title` value so PHP can resolve the term by name.
 *
 * @param {jQuery} searchWrapper Root search wrapper element.
 * @returns {Array} Selected property feature term-id values.
 */
function wpestate_collect_property_features_values(searchWrapper) {
    "use strict";

    // Step 1: Determine checkbox scope (active tab for type 6, full wrapper otherwise).
    var checkboxSelector = '.extended_search_check_wrapper input[type="checkbox"][data-search-taxonomy="property_features"]';
    var checkboxScope = searchWrapper;
    if (mapfunctions_vars.adv_search_type === '6') {
        checkboxScope = searchWrapper.find('.tab-pane.active');
    }
    if (checkboxScope.length === 0) {
        checkboxScope = searchWrapper;
    }

    // Step 2: Collect checked feature identifiers from the selected scope.
    var propertyFeaturesValues = [];
    checkboxScope.find(checkboxSelector).each(function () {
        if (jQuery(this).is(":checked")) {
            var featureTermId = jQuery(this).attr('data-term-id') || '';
            if (featureTermId !== '') {
                propertyFeaturesValues.push(featureTermId);
            } else {
                // Step 2.1: Non-Latin or translated feature checkboxes may not
                // carry a term id. Fall back to the display name so the backend
                // can resolve the property_features term by name.
                var featureName = jQuery(this).attr('name-title') || '';
                featureName = String(featureName || '').trim();
                if (featureName !== '') {
                    propertyFeaturesValues.push(featureName);
                }
            }
        }
    });

    // Step 3: Return unique feature identifiers only.
    propertyFeaturesValues = propertyFeaturesValues.filter(function (value, index, array) {
        return array.indexOf(value) === index;
    });

    return propertyFeaturesValues;
}

/**
 * Collect slider min/max values from advanced search controls.
 *
 * Supports regular and tabbed layouts, including the component v3
 * min/max dropdown fields when present.
 *
 * @returns {Object} Slider values object with `slider_min` and `slider_max`.
 */
function wpestate_collect_slider_values() {
    "use strict";

    // Step 1: Initialize return object.
    var sliderValues = {
        slider_min: '',
        slider_max: ''
    };

    // Step 2: Read regular slider fields.
    if (mapfunctions_vars.slider_price === 'yes') {
        sliderValues.slider_min = jQuery('#price_low').val();
        sliderValues.slider_max = jQuery('#price_max').val();
    }

    // Step 3: Read component v3 fields in regular scope.
    if (jQuery('.wpresidence-component3-min-price_input_class').length > 0) {
        sliderValues.slider_min = jQuery('.wpresidence-component3-min-price_input_class').val();
        sliderValues.slider_max = jQuery('.wpresidence-component3-max-price_input_class').val();
    }

    // Step 4: Override with active-tab values for advanced type 6/7.
    if (mapfunctions_vars.adv_search_type === '6' || mapfunctions_vars.adv_search_type === '7') {
        var termId = jQuery('.tab-pane.active .term_id_class').val();

        if (mapfunctions_vars.slider_price === 'yes') {
            sliderValues.slider_min = jQuery('#price_low_' + termId).val();
            sliderValues.slider_max = jQuery('#price_max_' + termId).val();
        }

        if (jQuery('.tab-pane.active .wpresidence-component3-min-price_input_class').length > 0) {
            sliderValues.slider_min = jQuery('.tab-pane.active .wpresidence-component3-min-price_input_class').val();
            sliderValues.slider_max = jQuery('.tab-pane.active .wpresidence-component3-max-price_input_class').val();
        }
    }

    return sliderValues;
}

/**
 * Collect geolocation filter values from search controls.
 *
 * Reads default geolocation fields and, when available, overrides them
 * with active-tab geolocation values.
 *
 * @returns {Object} Geolocation values object with `geo_lat`, `geo_long`, `geo_rad`.
 */
function wpestate_collect_geo_values() {
    "use strict";

    // Step 1: Initialize return object.
    var geoValues = {
        geo_lat: '',
        geo_long: '',
        geo_rad: ''
    };

    // Step 2: Read default geolocation values.
    if (jQuery("#geolocation_search").length > 0) {
        geoValues.geo_lat = jQuery('#geolocation_lat').val();
        geoValues.geo_long = jQuery('#geolocation_long').val();
        geoValues.geo_rad = jQuery('#geolocation_radius').val();
    }

    // Step 3: Override with active-tab values when tab-specific fields exist.
    if (jQuery('.tab-pane.active .geolocation_search_item').length > 0) {
        geoValues.geo_lat = jQuery('.tab-pane.active .geolocation_lat').val();
        geoValues.geo_long = jQuery('.tab-pane.active .geolocation_long').val();
        geoValues.geo_rad = jQuery('.tab-pane.active #geolocation_radius').val();
    }

    return geoValues;
}

/**
 * Collect beds/baths popup component values from search controls.
 *
 * Reads default component values first, then overrides with active-tab
 * values when tab-specific controls are present.
 *
 * @returns {Object} Component values object with `componentsbeds` and `componentsbaths`.
 */
function wpestate_collect_beds_baths_component_values() {
    "use strict";

    // Step 1: Initialize return values.
    var componentValues = {
        componentsbeds: '',
        componentsbaths: ''
    };

    // Step 2: Read default beds/baths component values.
    if (jQuery('.wpestate-beds-baths-popoup-component').length > 0) {
        componentValues.componentsbeds = jQuery('.wpresidence-componentsbeds').val();
        componentValues.componentsbaths = jQuery('.wpresidence-componentsbaths').val();
    }

    // Step 3: Override with active-tab component values when available.
    if (jQuery('.tab-pane.active .wpestate-beds-baths-popoup-component').length > 0) {
        componentValues.componentsbeds = jQuery('.tab-pane.active .wpresidence-componentsbeds').val();
        componentValues.componentsbaths = jQuery('.tab-pane.active .wpresidence-componentsbaths').val();
    }

    return componentValues;
}

/**
 * Extract a taxonomy term id from an element using known attribute names.
 *
 * @param {jQuery} element Search element that may contain term-id metadata.
 * @returns {string} Term id value or empty string.
 */
function wpestate_get_term_id_from_element(element) {
    "use strict";

    // Step 1: List supported term-id attributes used across templates.
    var candidateAttributes = [
        'data-search-term',
        'data-termid',
        'data-term-id',
        'data-term_id'
    ];
    var candidateValue = '';
    var attributeIndex = 0;

    // Step 2: Return the first non-empty term-id value.
    for (attributeIndex = 0; attributeIndex < candidateAttributes.length; attributeIndex++) {
        candidateValue = element.attr(candidateAttributes[attributeIndex]);
        if (typeof candidateValue === 'string' && candidateValue.trim() !== '') {
            return candidateValue.trim();
        }
    }

    return '';
}

/**
 * Check if an identical meta_query clause already exists.
 *
 * @param {Array} clauses Existing meta_query clauses.
 * @param {string} key Meta key.
 * @param {string|number} value Meta value.
 * @param {string} compare Meta compare operator.
 * @returns {boolean} True if clause exists, otherwise false.
 */
function wpestate_meta_clause_exists(clauses, key, value, compare) {
    "use strict";

    // Step 1: Compare each existing clause for exact key/value/compare match.
    var index = 0;
    for (index = 0; index < clauses.length; index++) {
        if (
            clauses[index].key === key &&
            String(clauses[index].value) === String(value) &&
            clauses[index].compare === compare
        ) {
            return true;
        }
    }

    return false;
}


/**
 * Prepare custom-search ajax data from taxonomy and post-meta controls.
 *
 * Step 1: Read control values from input/select/dropdown elements that expose `data-search-*`.
 * Step 2: Resolve taxonomy values (prefer term ids) and write them into ajaxData.
 * Step 3: Build and return meta_query clauses for post-meta fields.
 *
 * @param {Object} search_wrapper jQuery wrapper for the search form area.
 * @param {Object} ajaxData Mutable ajax payload object.
 * @param {string|number} componentsbeds Beds filter value from popup component.
 * @param {string|number} componentsbaths Baths filter value from popup component.
 * @returns {Array<Object>} Prepared meta_query array.
 */
function wpestate_prepare_custom_search_ajax_data(search_wrapper, ajaxData, componentsbeds, componentsbaths) {
    "use strict";

    // Step 1: Create local containers used while scanning form controls.
    var metaQueryPayload = [];
    var taxonomyKeyMap = {
        'property_category': 'category',
        'property_action_category': 'action',
        'property_city': 'city',
        'property_area': 'area',
        'property_county_state': 'county_state',
        'property_status': 'status',
        'property_features': 'features'
    };

    // Step 2: Scan supported controls and map their values to taxonomy/meta payloads.
    search_wrapper.find('input[data-search-taxonomy], select[data-search-taxonomy], button[data-search-taxonomy], div[data-search-taxonomy], input[data-search-post-meta], select[data-search-post-meta], button[data-search-post-meta], div[data-search-post-meta]').each(function () {
        // Step 2.1: Work with the current field element.
        var element = jQuery(this);

        // Step 2.2: Read custom search metadata from data-* attributes.
        // `data-search-taxonomy` marks taxonomy-driven fields.
        // `data-search-post-meta` marks post meta-driven fields.
        // `data-search-compare` tells us which compare operator should be used.
        var taxonomyAttr = element.attr('data-search-taxonomy');
        var postMetaAttr = element.attr('data-search-post-meta');
        var compareAttr = element.attr('data-search-compare');
        var inputValue = '';
        var taxonomyResolvedValue = '';

        // Step 2.3: Resolve the field value based on element type.
        // Buttons store selected value in `data-value`.
        // Select/input elements use their native `.val()`.
        if (element.is('button') || element.is('div')) {
            inputValue = element.attr('data-value') || '';
            // Fallback to hidden input value used by dropdown implementations.
            if ((inputValue === '' || inputValue === 'all') && typeof taxonomyAttr === 'string' && taxonomyAttr.trim() !== '') {
                var hiddenField = element.closest('.wpresidence_dropdown').find('input[type="hidden"][data-search-taxonomy="' + taxonomyAttr.trim() + '"]');
                if (hiddenField.length > 0) {
                    inputValue = hiddenField.first().val() || '';
                }
            }
        } else if (element.is('select')) {
            inputValue = element.val() || '';
        } else {
            if (element.is(':checkbox') || element.is(':radio')) {
                if (!element.is(':checked')) {
                    inputValue = '';
                } else {
                    inputValue = element.val() || '';
                }
            } else {
                inputValue = element.val() || '';
            }
        }

        // Step 2.4: If the field describes a taxonomy, set selected value on taxonomy key only.
        // Priority for taxonomy fields:
        // 1) selected term id (from data attributes),
        // 2) fallback to selected value/slug if no id exists.
        if (typeof taxonomyAttr === 'string' && taxonomyAttr.trim() !== '') {
            var taxonomyName = taxonomyAttr.trim();
            var termIdValue = '';

            // Step 2.4.1: For multi-select taxonomy controls, collect all selected values (term ids preferred).
            if (element.is('select') && element.prop('multiple')) {
                var multiValues = [];
                element.find('option:selected').each(function () {
                    var selectedMultiOption = jQuery(this);
                    var selectedMultiTermId = wpestate_get_term_id_from_element(selectedMultiOption);
                    var selectedMultiValue = selectedMultiOption.val() || '';
                    var resolvedMultiValue = (selectedMultiTermId !== '') ? selectedMultiTermId : selectedMultiValue;

                    if (resolvedMultiValue !== '' && String(resolvedMultiValue).toLowerCase() !== 'all') {
                        multiValues.push(resolvedMultiValue);
                    }
                });

                if (multiValues.length > 0) {
                    multiValues = multiValues.filter(function (value, index, array) {
                        return array.indexOf(value) === index;
                    });
                    ajaxData[taxonomyName] = multiValues;
                    if ((taxonomyKeyMap[taxonomyName] || '') === 'action') {
                        ajaxData.query_action = multiValues;
                    }
                }
                return;
            }

            // Step 2.4.2: Try direct term-id attributes on current element.
            termIdValue = wpestate_get_term_id_from_element(element);

            // Step 2.4.3: For dropdown button/div implementations, selected term metadata often lives on the selected <li>.
            if ((termIdValue === '') && (element.is('button') || element.is('div')) && inputValue !== '' && String(inputValue).toLowerCase() !== 'all') {
                var buttonId = element.attr('id') || '';
                if (buttonId !== '') {
                    var menuElement = search_wrapper.find('ul[aria-labelledby="' + buttonId + '"]').first();
                    if (menuElement.length > 0) {
                        var selectedOptionElement = menuElement.find('li[data-value="' + inputValue + '"]').first();
                        if (selectedOptionElement.length > 0) {
                            termIdValue = wpestate_get_term_id_from_element(selectedOptionElement);
                        }
                    }
                }
            }

            // Step 2.4.4: For select controls, selected option may store the term id.
            if ((termIdValue === '') && element.is('select')) {
                var selectedOption = element.find('option:selected').first();
                if (selectedOption.length > 0) {
                    termIdValue = wpestate_get_term_id_from_element(selectedOption);
                }
            }

            taxonomyResolvedValue = (termIdValue !== '') ? termIdValue : inputValue;

            if (inputValue !== null && String(inputValue).trim() !== '' && String(inputValue).toLowerCase() !== 'all') {
                // property_features is handled explicitly as an array from checkboxes.
                // Skip generic scalar assignment to avoid overriding the array payload.
                if (taxonomyName === 'property_features') {
                    return;
                }

                // Keep term-id values as priority.
                // If we already stored a value and the current element does not expose a term id
                // (for example hidden input mirrors), do not overwrite the existing value.
                var hasCurrentTermId = (termIdValue !== '');
                var hasExistingValue = (
                    typeof ajaxData[taxonomyName] !== 'undefined' &&
                    ajaxData[taxonomyName] !== null &&
                    String(ajaxData[taxonomyName]).trim() !== ''
                );
                if (hasExistingValue && !hasCurrentTermId) {
                    return;
                }

                // Keep raw taxonomy key in payload.
                ajaxData[taxonomyName] = taxonomyResolvedValue;

                // Keep non-reserved alias for action taxonomy only.
                if ((taxonomyKeyMap[taxonomyName] || '') === 'action') {
                    ajaxData.query_action = taxonomyResolvedValue;
                }
            }
        }

        // Step 2.5: If the field describes a post meta key, convert it to a meta_query clause.
        // We only add a clause when:
        // 1) the meta key exists and is not blank,
        // 2) the field has a non-empty value.
        //
        // Resulting clause format:
        // {
        //   key:     data-search-post-meta,
        //   value:   current input value,
        //   compare: data-search-compare (or empty string fallback)
        // }
        //
        // These clauses are collected into metaQueryPayload and later sent as ajaxData.meta_query.
        // Only value-carrying form fields should generate meta_query clauses.
        // Dropdown wrappers (button/div/ul) may repeat the same meta key.
        if (
            (element.is('input') || element.is('select') || element.is('textarea')) &&
            typeof postMetaAttr === 'string' &&
            postMetaAttr.trim() !== ''
        ) {
            if (inputValue !== null && String(inputValue).trim() !== '') {
                var clauseKey = postMetaAttr.trim();
                var clauseCompare = (typeof compareAttr === 'string') ? compareAttr : '';

                if (!wpestate_meta_clause_exists(metaQueryPayload, clauseKey, inputValue, clauseCompare)) {
                    metaQueryPayload.push({
                        'key': clauseKey,
                        'value': inputValue,
                        'compare': clauseCompare
                    });
                }
            }
        }
    });

    // Step 3: Add beds/baths popup component constraints to meta_query.
    // When componentsbeds/componentsbaths are present, enforce:
    // - property_bedrooms >= componentsbeds
    // - property_bathrooms >= componentsbaths
    if (componentsbeds !== null && String(componentsbeds).trim() !== '') {
        if (!wpestate_meta_clause_exists(metaQueryPayload, 'property_bedrooms', componentsbeds, '>=')) {
            metaQueryPayload.push({
                'key': 'property_bedrooms',
                'value': componentsbeds,
                'compare': '>='
            });
        }
    }
    if (componentsbaths !== null && String(componentsbaths).trim() !== '') {
        if (!wpestate_meta_clause_exists(metaQueryPayload, 'property_bathrooms', componentsbaths, '>=')) {
            metaQueryPayload.push({
                'key': 'property_bathrooms',
                'value': componentsbaths,
                'compare': '>='
            });
        }
    }

    // Step 4: Return the final meta_query payload to the caller.
    return metaQueryPayload;
}
