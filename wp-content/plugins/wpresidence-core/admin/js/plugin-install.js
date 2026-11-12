jQuery(document).ready(function($){
    $('.wpresidence-plugin-table').on('click', '.wpresidence-install-plugin', function(e){
        e.preventDefault();
        var $button = $(this);
        var slug = $button.data('slug');
        var source = $button.data('source');
        if(!slug || !source){
            return;
        }
        $button.prop('disabled', true).text(wpresidenceInstallPlugin.installing);
        $.post(ajaxurl, {
            action: 'wpresidence_install_plugin',
            nonce: wpresidenceInstallPlugin.nonce,
            slug: slug,
            source: source
        }, function(response){
            if(response.success){
                location.reload();
            }else{
                alert(response.data || wpresidenceInstallPlugin.error);
                location.reload();
            }
        });
    });
});
