jQuery(document).ready(function ($) {
    var $ul = $('ul.subsubsub');
    if (!$ul.length) {
        return;
    }

    var $wrapper = $('<div class="nav-tab-wrapper"></div>');
    var $lis = $ul.find('li');

    $lis.each(function (i) {
        var $li = $(this);
        var $link = $li.find('a');

        // remove trailing separators for items converted to tabs
        if (i > 1) {
            $li.text('');
            $li.append($link);
        }

        var $tab = $link.clone().addClass('nav-tab');
        if ($link.hasClass('current')) {
            $tab.addClass('nav-tab-active');
        }
        $wrapper.append($tab);
    });

    $ul.before($wrapper);

    // Keep "All", "Published" and "Trash" in the list
    $ul.find('li').not('.all, .publish, .trash').remove();

    // Clean up separators for the remaining items
    var $remaining = $ul.find('li');
    $remaining.each(function (i) {
        var $li = $(this);
        var $link = $li.find('a');
        $li.text('').append($link);
        if (i < $remaining.length - 1) {
            $li.append(' |');
        }
    });
});
