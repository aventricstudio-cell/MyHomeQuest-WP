/**
 * Handles tab persistence across various admin metaboxes.
 *
 * The script stores the last active tab of a metabox in a cookie. When the
 * page is reloaded the previously selected tab is automatically activated
 * without any URL parameters or redirect logic.
 */
(function ($) {
    'use strict';

    /**
     * Save a value into a cookie scoped to the entire site.
     *
     * @param {string} name  Cookie name.
     * @param {string} value Value to store.
     */
    function setCookie(name, value) {
        document.cookie = name + '=' + encodeURIComponent(value) + '; path=/';
    }

    /**
     * Retrieve a cookie value if present.
     *
     * @param {string} name Cookie name.
     * @returns {string|null} Stored value or null when missing.
     */
    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i].trim();
            if (c.indexOf(nameEQ) === 0) {
                return decodeURIComponent(c.substring(nameEQ.length));
            }
        }
        return null;
    }

    /**
     * Attach tab behaviour and persistence to a metabox.
     *
     * @param {string} boxSelector   jQuery selector for the metabox container.
     * @param {string} tabClass      CSS class used for the tab items.
     * @param {string} contentClass  CSS class used for the tab content items.
     */
    function initTabs(boxSelector, tabClass, contentClass) {
        var $box = $(boxSelector);
        if (!$box.length) {
            return; // Metabox not present on this screen.
        }

        // Each metabox gets its own cookie based on the container ID.
        var cleanId = boxSelector.replace('#', '').replace('.', '');
        var cookieName = 'wpestate_tab_' + cleanId;

        /**
         * Activate a tab and show its associated content.
         *
         * @param {string} tabId Identifier of the tab to activate.
         */
        function activate(tabId) {
            var $tabs = $box.find('.' + tabClass);
            var $contents = $box.find('.' + contentClass);
            $tabs.removeClass('active_tab');
            $contents.removeClass('active_tab');
            $tabs.filter('[data-content="' + tabId + '"]').addClass('active_tab');
            $('#' + tabId).addClass('active_tab');
        }

        // Store selected tab into the cookie whenever a tab is clicked.
        $box.on('click', '.' + tabClass, function () {
            var tabId = $(this).data('content');
            setCookie(cookieName, tabId);
            activate(tabId);
        });

        // On page load attempt to restore the stored tab.
        var stored = getCookie(cookieName);
        if (stored && $('#' + stored).length) {
            activate(stored);
        }
    }

    $(document).ready(function () {
        // Property metabox
        initTabs('#new_tabbed_interface', 'property_tab_item', 'property_tab_item_content');

        // Agent metabox
        initTabs('#estate_agent-sectionid', 'property_tab_item', 'property_tab_item_content');

        // Agency metabox
        initTabs('#estate_agency-sectionid', 'property_tab_item', 'property_tab_item_content');

        // Developer metabox
        initTabs('#estate_developer-sectionid', 'property_tab_item', 'property_tab_item_content');

        // Membership metabox
        initTabs('#Forestate_membership-sectionid', 'property_tab_item', 'property_tab_item_content');

        // Header options metabox
        initTabs('.header_options_wrapper', 'header_tab_item', 'header_tab_item_content');

        // Property category term page
        initTabs('#property_category_options_wrapper', 'property_tab_item', 'property_tab_item_content');

        // Page Directory listings
        initTabs('.pladv_options_wrapper', 'pladv_tab_item', 'pladv_tab_item_content');
    });
})(jQuery);
