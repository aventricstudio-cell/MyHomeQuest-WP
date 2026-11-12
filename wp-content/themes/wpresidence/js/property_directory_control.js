/**
 * Directory Filtering JavaScript
 *
 * This script handles the filtering functionality for the property directory
 * in the WpResidence theme. It manages slider interactions, checkbox filters,
 * and AJAX requests for dynamic content loading.
 *
 * @package WpResidence
 * @subpackage JavaScript
 * @since 1.0 
 */
 
// Reserved timer/delay pair for directory scroll-triggered filtering behavior.
let sliderTimerScroll;
const sliderAjaxDelayScroll = 400;
// Flag that controls whether current filtering should reset pagination.
let with_clear = 1;

/**
 * Increase directory pagination and trigger a new filter request.
 *
 * @returns {void}
 */
function wpresidence_directory_increase_pagination() {
    const paginationEl = jQuery('#property_dir_pagination');
    let page = parseInt(paginationEl.val(), 10);
    paginationEl.val(++page);
    wpestate_directory_filtering();
}

jQuery(document).ready(function() {
    // Cache the initial text of the load more button
    const loadMoreButton = jQuery('#directory_load_more');
    loadMoreButton.data('default-text', loadMoreButton.text());

    // Load more button click handler
    loadMoreButton.on('click', function(event) {
        const isDone = parseInt(jQuery('#property_dir_done').val(), 10);
        if (isDone === 0) {
            with_clear = 0;
            wpresidence_directory_increase_pagination();
        }
    });

    // Initialize sliders
    wpresidence_directory_initializeSliders();

    // Checkbox change handler
    jQuery('.extended_search_check_wrapper_directory input[type="checkbox"]').on('click', wpestate_directory_filtering);

    // Dropdown selection handler
    jQuery('.listing_filters_head_directory li, .directory_sidebar li').on('click', function() {
        const pick = jQuery(this).text();
        const value = jQuery(this).attr('data-value');
        const parent = jQuery(this).parent().parent();
        
        parent.find('.filter_menu_trigger')
            .text(pick)
            .append('<span class="caret caret_filter"></span>')
            .attr('data-value', value);
        
        parent.find('input:hidden').val(value);
        wpestate_directory_filtering();
    });

    // Status and keyword change handler
    jQuery('#property_status, #property_keyword').on('change', wpestate_directory_filtering);
});

let sliderTimer;
const sliderAjaxDelay = 500;

/**
 * Debounce directory filtering and delegate to the universal AJAX handler.
 *
 * @returns {void}
 */
function wpestate_directory_filtering() {
    // Debounce rapid UI updates (sliders/dropdowns/checkboxes).
    if (sliderTimer) {
        clearTimeout(sliderTimer);
    }

    // Resolve source (filters) and target (cards) containers.
    filter_container    =   jQuery('.directory_sidebar_wrapper');
    cards_container     =   jQuery('#listing_ajax_container');
    do_clear_container  = parseInt(with_clear, 10);
    // Trigger AJAX filtering after debounce delay.
    sliderTimer = setTimeout(function(){
        wpresidence_universal_query_filtering_action(filter_container,cards_container,do_clear_container);
    }, sliderAjaxDelay);

}



/**
 * Initialize directory sliders and bind each to filtering.
 *
 * @returns {void}
 */
function wpresidence_directory_initializeSliders() {
    const sliders = [
        { id: '#slider_price_widget', change: wpestate_directory_filtering },
        { id: '#slider_property_size_widget', config: wpresidence_directory_getSizeSliderConfig() },
        { id: '#slider_property_lot_size_widget', config: wpresidence_directory_getLotSizeSliderConfig() },
        { id: '#slider_property_rooms_widget', config: wpresidence_directory_getRoomsSliderConfig('rooms') },
        { id: '#slider_property_bedrooms_widget', config: wpresidence_directory_getRoomsSliderConfig('bedrooms') },
        { id: '#slider_property_bathrooms_widget', config: wpresidence_directory_getRoomsSliderConfig('bathrooms') }
    ];

    sliders.forEach(slider => {
        if (slider.change) {
            jQuery(slider.id).slider({ change: slider.change });
        } else {
            jQuery(slider.id).slider(slider.config);
        }
    });
}

/**
 * Build jQuery UI slider config for property size range.
 *
 * @returns {Object}
 */
function wpresidence_directory_getSizeSliderConfig() {
    return {
        range: true,
        min: parseFloat(directory_vars.dir_min_size),
        max: parseFloat(directory_vars.dir_max_size),
        values: [directory_vars.dir_min_size, directory_vars.dir_max_size],
        slide: function(event, ui) {
            jQuery("#property_size_low").val(ui.values[0]);
            jQuery("#property_size_max").val(ui.values[1]);
            jQuery("#property_size").html(ui.values[0] + " " + directory_vars.measures_sys + " " + control_vars.to + " " + ui.values[1] + " " + directory_vars.measures_sys);
            wpestate_directory_filtering();
        }
    };
}

/**
 * Build jQuery UI slider config for lot size range.
 *
 * @returns {Object}
 */
function wpresidence_directory_getLotSizeSliderConfig() {
    return {
        range: true,
        min: parseFloat(directory_vars.dir_min_lot_size),
        max: parseFloat(directory_vars.dir_max_lot_size),
        values: [directory_vars.dir_min_lot_size, directory_vars.dir_max_lot_size],
        slide: function(event, ui) {
            jQuery("#property_lot_size_low").val(ui.values[0]);
            jQuery("#property_lot_size_max").val(ui.values[1]);
            jQuery("#property_lot_size").html(ui.values[0] + " " + directory_vars.measures_sys + " " + control_vars.to + " " + ui.values[1] + " " + directory_vars.measures_sys);
            wpestate_directory_filtering();
        }
    };
}

/**
 * Build jQuery UI slider config for rooms/bedrooms/bathrooms range.
 *
 * @param {string} type Field suffix: rooms, bedrooms, or bathrooms.
 * @returns {Object}
 */
function wpresidence_directory_getRoomsSliderConfig(type) {
    return {
        range: true,
        min: parseFloat(directory_vars[`dir_${type}_min`]),
        max: parseFloat(directory_vars[`dir_${type}_max`]),
        values: [directory_vars[`dir_${type}_min`], directory_vars[`dir_${type}_max`]],
        slide: function(event, ui) {
            jQuery(`#property_${type}_low`).val(ui.values[0]);
            jQuery(`#property_${type}_max`).val(ui.values[1]);
            jQuery(`#property_${type}`).text(ui.values[0] + " " + control_vars.to + " " + ui.values[1]);
            wpestate_directory_filtering();
        }
    };
}


/**
 * Collect checked feature term IDs for directory filtering.
 *
 * @param {jQuery} [container] Optional scope wrapper for checkbox lookup.
 * @returns {string} Comma-separated term-id list.
 */
function wpresidence_directory_getCheckedFilters(container) {
    // Scope to the provided container when available, otherwise use document scope.
    const scope = (container && container.length) ? container : jQuery(document);

    // Read checked feature term ids from data-term-id and normalize duplicates.
    const featureTermIds = scope.find('.extended_search_check_wrapper_directory input[type="checkbox"]:checked')
        .map(function() {
            return (jQuery(this).attr('data-term-id') || '').toString().trim();
        })
        .get()
        .filter(function(termId, index, array) {
            return termId !== '' && array.indexOf(termId) === index;
        });

    // Keep CSV format expected by the directory AJAX parser.
    return featureTermIds.join(',');
}










/**
 * Handle classic directory AJAX success response.
 *
 * @param {Object} data AJAX JSON payload.
 * @returns {void}
 */
function wpresidence_directory_handleAjaxSuccess(data) {
    const totalResults = parseInt(data.no_results, 10);
    const perPage = parseInt(jQuery('#property_dir_per_page').val(), 10);
    const listingContainer = jQuery('#listing_ajax_container');

    // Always replace skeleton placeholders with returned cards or messages
    wpestate_replaceSkeletons(listingContainer, data.cards);

    const noMore = totalResults < perPage || listingContainer.find('.no_results').length;
    wpresidence_directory_updateLoadMoreButton(noMore);

    jQuery('#listing_loader').hide();
    wpestate_restart_js_after_ajax();
}

/**
 * Update "Load More" button text/state and completion flag.
 *
 * @param {boolean} noMore True when there are no more pages/results.
 * @returns {void}
 */
function wpresidence_directory_updateLoadMoreButton(noMore) {
    const loadMoreButton = jQuery('#directory_load_more');
    const defaultText = loadMoreButton.data('default-text') || loadMoreButton.text();
    if (noMore) {
        jQuery('#property_dir_done').val('1');
        loadMoreButton.text(directory_vars.no_more).addClass('no_more_list');
    } else {
        jQuery('#property_dir_done').val('0');
        loadMoreButton.text(defaultText).removeClass('no_more_list');
    }
}




/*===============================================================================================================================================*/

/**
 * Perform universal directory filtering AJAX request.
 *
 * @param {jQuery} filter_container Sidebar wrapper containing filter controls.
 * @param {jQuery} cards_container Cards wrapper to replace/append content.
 * @param {number|string} do_clear_container Flag to reset pagination or append.
 * @returns {void}
 */
//cards_container = listing_ajax_container

function wpresidence_universal_query_filtering_action(filter_container,cards_container,do_clear_container) {

    console.log('wpresidence_universal_query_filtering_action');


    // Normalize mode flag (reset vs append mode).
    do_clear_container = parseInt(do_clear_container, 10);
    if (do_clear_container === 1) {
        filter_container.find('#property_dir_pagination').val('1');
        wpresidence_directory_updateLoadMoreButton(false);
    }
    
    // Build endpoint and payload for universal directory request.
    const ajaxurl = ajaxcalls_vars.admin_url + 'admin-ajax.php';
    const filterData = wpresidence_universal_getFilterData(filter_container);

    // Ensure the order value from the dedicated dropdown is always included
    const orderDropdown = jQuery('.wpresidence_wrap_a_filter_order_directory #a_filter_order_directory');
    if (orderDropdown.length) {
        const dropdownValue = orderDropdown.attr('data-value');
        if (typeof dropdownValue !== 'undefined') {
            filterData.order = dropdownValue;
        }
    }

    // Show skeleton placeholders instead of generic loader.
   if (do_clear_container === 1) {
        // Remove only real cards, keep skeletons
        wpestate_createSkeletons(cards_container);
    }else{
        wpestate_createSkeletons(cards_container,'true');
    }

    jQuery('.pagination_nojax').hide();
    //jQuery('#listing_loader').show();
    
    const nonce = jQuery('#wpestate_ajax_filtering').val();
    
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        dataType: 'json',
        data: {
            'action': 'wpestate_classic_ondemand_directory',
            ...filterData,
            'security': nonce
        },
        success: function(data) {
            wpresidence_directory_handleAjaxSuccess(data);
        },
        error: function(errorThrown) {
             jQuery('.skeleton-placeholder').remove();
            jQuery('#listing_loader').hide();
            console.error('AJAX error:', errorThrown);
        }
    });
    
    with_clear = 1;
}

/**
 * Collect universal directory filter payload from a given container.
 *
 * @param {jQuery} container Sidebar wrapper containing filter controls.
 * @returns {Object}
 */
function wpresidence_universal_getFilterData(container) {
    /**
     * Read a term-id value from supported element attributes.
     *
     * @param {jQuery} element Element that may carry a term-id.
     * @returns {string} Term id or empty string.
     */
    const readTermId = function(element) {
        const termIdAttributes = ['data-term-id', 'data-termid', 'data-term_id', 'data-search-term'];

        if (!element || element.length === 0) {
            return '';
        }

        for (let i = 0; i < termIdAttributes.length; i++) {
            const attributeValue = (element.attr(termIdAttributes[i]) || '').toString().trim();
            if (attributeValue !== '') {
                return attributeValue;
            }
        }

        return '';
    };

    /**
     * Resolve selected taxonomy term-id from a dropdown trigger element.
     *
     * @param {string} triggerSelector Dropdown trigger selector.
     * @returns {string} Selected term id or empty string.
     */
    const resolveDropdownTermId = function(triggerSelector) {
        const trigger = container.find(triggerSelector).first();

        if (trigger.length === 0) {
            return '';
        }

        // Step 1: If trigger already stores a term-id, use it directly.
        let termId = readTermId(trigger);
        if (termId !== '') {
            return termId;
        }

        // Step 2: Find selected option value and match it inside the linked <ul>.
        const selectedValue = (trigger.attr('data-value') || '').toString().trim();
        if (selectedValue === '' || selectedValue.toLowerCase() === 'all') {
            return '';
        }

        const triggerId = (trigger.attr('id') || '').toString().trim();
        if (triggerId !== '') {
            const menu = container.find('ul[aria-labelledby="' + triggerId + '"]').first();
            if (menu.length > 0) {
                const option = menu.find('li[data-value="' + selectedValue + '"]').first();
                termId = readTermId(option);
                if (termId !== '') {
                    return termId;
                }
            }
        }

        // Step 3: Accept numeric selected values as term ids.
        if (/^\d+$/.test(selectedValue)) {
            return selectedValue;
        }

        return '';
    };

    /**
     * Resolve the selected property status term-id.
     *
     * @returns {string} Status term id or empty string.
     */
    const resolveStatusTermId = function() {
        const statusField = container.find('#property_status, select[name="property_status"]').first();

        if (statusField.length === 0) {
            return '';
        }

        // Step 1: Select-based status control.
        if (statusField.is('select')) {
            const selectedOption = statusField.find('option:selected').first();
            let termId = readTermId(selectedOption);
            if (termId !== '') {
                return termId;
            }

            termId = readTermId(statusField);
            if (termId !== '') {
                return termId;
            }

            const selectedValue = (statusField.val() || '').toString().trim();
            if (/^\d+$/.test(selectedValue)) {
                return selectedValue;
            }

            if (selectedValue !== '' && selectedValue.toLowerCase() !== 'all') {
                return selectedValue;
            }

            return '';
        }

        // Step 2: Dropdown-button style status control.
        let termId = readTermId(statusField);
        if (termId !== '') {
            return termId;
        }

        const selectedValue = (statusField.attr('data-value') || statusField.val() || '').toString().trim();
        if (/^\d+$/.test(selectedValue)) {
            return selectedValue;
        }

        if (selectedValue !== '' && selectedValue.toLowerCase() !== 'all') {
            return selectedValue;
        }

        return '';
    };

    const data = {
        // Taxonomy filters must use term IDs (not slug or label values).
        'property_action_category' : resolveDropdownTermId('#sidebar-adv_actions'),
        'property_category'        : resolveDropdownTermId('#sidebar-adv_categ'),
        'property_city'            : resolveDropdownTermId('#sidebar-advanced_city'),
        'property_area'            : resolveDropdownTermId('#sidebar-adv_area'),
        'property_county_state'    : resolveDropdownTermId('#sidebar-adv_conty'),
        'price_low'         : parseFloat(container.find('#price_low_widget').val(), 10),
        'price_max'         : parseFloat(container.find('#price_max_widget').val(), 10),
        'postID'            : directory_vars.post_id,
        'min_size'          : parseFloat(container.find('#property_size_low').val(), 10),
        'max_size'          : parseFloat(container.find('#property_size_max').val(), 10),
        'min_lot_size'      : parseFloat(container.find('#property_lot_size_low').val(), 10),
        'max_lot_size'      : parseFloat(container.find('#property_lot_size_max').val(), 10),
        'min_rooms'         : parseFloat(container.find('#property_rooms_low').val(), 10),
        'max_rooms'         : parseFloat(container.find('#property_rooms_max').val(), 10),
        'min_bedrooms'      : parseFloat(container.find('#property_bedrooms_low').val(), 10),
        'max_bedrooms'      : parseFloat(container.find('#property_bedrooms_max').val(), 10),
        'min_bathrooms'     : parseFloat(container.find('#property_bathrooms_low').val(), 10),
        'max_bathrooms'     : parseFloat(container.find('#property_bathrooms_max').val(), 10),
        'status'            : resolveStatusTermId(),
        'property_status'   : resolveStatusTermId(),
        'keyword'           : container.find('#property_keyword').val(),
        'order'             : container.find('#a_filter_order_directory').attr('data-value'),
        'pagination'        : container.find('#property_dir_pagination').val(),
        'property_features' : wpresidence_directory_getCheckedFilters(container)
    };

    return data;
}

