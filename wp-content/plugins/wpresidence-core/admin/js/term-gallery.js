jQuery(function($){
    function updateIds($container){
        var ids = [];
        $container.find('.uploaded_thumb').each(function(){
            ids.push($(this).data('imageid'));
        });
        $container.siblings('.wpestate_term_gallery_ids').val(ids.join(','));
    }

    $(document).on('click', '.term-gallery-upload-button', function(e){
        e.preventDefault();
        var $btn = $(this);
        var $wrap = $btn.siblings('.property_uploaded_thumb_wrapepr');
        var frame = wp.media({
            title: wpresidence_admin_gallery.title,
            button: { text: wpresidence_admin_gallery.button },
            multiple: true
        });
        frame.on('select', function(){
            var selection = frame.state().get('selection');
            selection.each(function(attachment){
                attachment = attachment.toJSON();
                var thumb = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                var html = '<div class="uploaded_thumb" data-imageid="'+attachment.id+'">'+
                           '<img src="'+thumb+'" alt="" />'+
                           '<span class="wpresidence_term_attach_delete dashicons dashicons-trash"></span></div>';
                $wrap.append(html);
            });
            updateIds($wrap);
        });
        frame.open();
    });

    $(document).on('click', '.property_uploaded_thumb_wrapepr .wpresidence_term_attach_delete', function(e){
        e.preventDefault();
        var $thumb     = $(this).parent('.uploaded_thumb');
        var $container = $(this).closest('.property_uploaded_thumb_wrapepr');
        $thumb.remove();
        updateIds($container);

        if (typeof wpresidence_admin_gallery !== 'undefined' && wpresidence_admin_gallery.term_id) {
            $.post(ajaxurl, {
                action: 'wpestate_delete_term_gallery_image',
                nonce: wpresidence_admin_gallery.nonce,
                term_id: wpresidence_admin_gallery.term_id,
                gallery_ids: $container.siblings('.wpestate_term_gallery_ids').val()
            });
        }
    });
});
