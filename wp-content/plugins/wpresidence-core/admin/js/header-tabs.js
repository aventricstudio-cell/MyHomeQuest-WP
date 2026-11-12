(function($){
    'use strict';

    function setUrlParameter(tabId){
        var url = new URL(window.location.href);
        url.searchParams.set('header_tab', tabId);
        history.replaceState(null, '', url.toString());
    }

    function activateTab(tabId){
        var $wrapper = $('.header_options_wrapper');
        var $tabItems = $wrapper.find('.header_tab_item');
        var $contentItems = $wrapper.find('.header_tab_item_content');
        $tabItems.removeClass('active_tab');
        $contentItems.removeClass('active_tab');
        $tabItems.filter('[data-content="'+tabId+'"]').addClass('active_tab');
        $('#'+tabId).addClass('active_tab');
        setUrlParameter(tabId);
    }

})(jQuery);
