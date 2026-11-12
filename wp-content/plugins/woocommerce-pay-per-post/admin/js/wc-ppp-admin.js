(function ($) {
    'use strict';

    $(function () {

        $(".post-types-button").click(function (e) {
            e.preventDefault();
            $(".current-post-types").toggle("fast");
        });

        $('.wc_pay_per_post_display_on_my_account').click(function(e){
           $('.my-account-shortcode-container').toggle("fast");
        });


        $(".wc_pay_per_post_delete_settings").change(function () {

            if ($(this).prop('checked')) {
                alert('Are you sure you want to do this?  If you deactivate this will delete all settings and records created by this plugin.  You have been warned');
            }

        });

        $(".wc_pay_per_post_display_on_my_account").change(function () {

            if ($(this).prop('checked')) {
                alert('After saving these options, you may have to save your permalinks again if the link on the My Account pages goes to a 404 page.  You can easily do this by going to Settings->Permalinks and just pressing the save button.  ');
            }

        });

        $("#wc_pay_per_post_custom_post_types").select2({
            tokenSeparators: [',', ' '],
            width: 'resolve' // need to override the changed default
        });

        $("#wc_pay_per_post_product_ids").select2({
            tokenSeparators: [',', ' '],
            width: 'resolve' // need to override the changed default
        });

        $("#wc_pay_per_post_woocommerce_membership_ids").select2({
            tokenSeparators: [',', ' '],
            width: 'resolve' // need to override the changed default
        });

        $("#wc_pay_per_post_page_view_restriction_enable_time_frame").click(function () {
            $("#wc_pay_per_post-page-view-restriction-time-frame-container").toggle("fast");
        });


        //Only allow one type of restriction to be set
        $('.wc_pay_per_post_restriction-type').on('change', function () {
            $('.wc_pay_per_post_restriction-type').not(this).prop('checked', false);
        });


        $('.wcppp-tab-bar a').click(function (event) {
            event.preventDefault();

            // Limit effect to the container element.
            const context = $(this).closest('.wcppp-tab-bar').parent();
            $('.wcppp-tab-bar li', context).removeClass('wcppp-tab-active');
            $(this).closest('li').addClass('wcppp-tab-active');
            $('.wcppp-tab-panel', context).hide();
            $($(this).attr('href'), context).show();
        });

        // Make setting wcppp-tab-active optional.
        $('.wcppp-tab-bar').each(function () {
            if ($('.wcppp-tab-active', this).length)
                $('.wcppp-tab-active', this).click();
            else
                $('a', this).first().click();
        });



        // Help Page
        $('#wc-ppp-help-nav-tabs a').click(function (event) {
            event.preventDefault();

            $('#wc-ppp-help-nav-tabs a').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.wc-ppp-help-tab').hide();
            $($(this).attr('href')).show();

        });

    });

})(jQuery);
