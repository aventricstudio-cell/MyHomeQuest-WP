jQuery(document).ready(function($){
    function bindUploader(buttonClass, inputSelector){
        $(buttonClass).on('click', function(e){
            e.preventDefault();
            var input = $(this).siblings(inputSelector);
            var frame = wp.media({
                title: wpresidenceWhiteLabel.uploadTitle,
                button: { text: wpresidenceWhiteLabel.uploadButton },
                multiple: false
            });
            frame.on('select', function(){
                var url = frame.state().get('selection').first().toJSON().url;
                input.val(url);
            });
            frame.open();
        });
    }
    bindUploader('.wl-screenshot-upload', '#wl-screenshot');
    bindUploader('.wl-logo-upload', '#wl-logo');
});
