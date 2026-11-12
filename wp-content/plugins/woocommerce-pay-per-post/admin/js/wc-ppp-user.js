(function ($) {
    'use strict';

    $(function () {

        $('#wc-ppp-page-view-table, .dataTables').DataTable( {
            "drawCallback": function( settings ) {
                $('.wc-ppp-delete-page-view').click(function (e) {
                    e.preventDefault();
                    const $current = $(this);
                    const page_view_id = $current.attr('href');
                    const $confirm = confirm('Are you sure you want to delete page view ' + page_view_id + '?');
                    const $page_view = $('#page-view-' + page_view_id);

                    if ($confirm === true) {
                        $.ajax({
                            url: wc_ppp.ajax_url,
                            type: 'post',
                            dataType: 'json',
                            data: {
                                action: 'delete_page_view',
                                data: page_view_id,
                                security: wc_ppp.delete_page_view_nonce
                            }
                        })
                            .done(function () {
                                $page_view.remove();
                            })
                            .fail(function (result) {
                                alert('Failed to remove page view ' + result);

                            });

                    }

                });
            }
        });

    });

})(jQuery);
