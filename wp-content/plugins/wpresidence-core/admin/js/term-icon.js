/**
 * Handles SVG/icon upload for property feature terms.
 * Opens WordPress media frame and stores selection in the
 * dedicated icon URL and attachment ID fields.
 */
jQuery(function($){
    var icon_frame;
    $(document).on('click', '#category_featured_image_icon_button', function(e){
        e.preventDefault();
        if(icon_frame){
            icon_frame.open();
            return;
        }
        icon_frame = wp.media({
            title: wpresidence_admin_gallery && wpresidence_admin_gallery.title ? wpresidence_admin_gallery.title : 'Select Icon',
            button: { text: wpresidence_admin_gallery && wpresidence_admin_gallery.button ? wpresidence_admin_gallery.button : 'Use this media' },
            library: { type: 'image' },
            multiple: false
        });
        icon_frame.on('select', function(){
            var attachment = icon_frame.state().get('selection').first().toJSON();
            $('#category_featured_image_icon').val(attachment.url);
            $('#category_attach_id').val(attachment.id);
        });
        icon_frame.open();
    });
});
