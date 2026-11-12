jQuery(document).ready(function($){
    var frame;
    $('#wpresidence-upload-csv').on('click', function(e){
        e.preventDefault();
        if(frame){ frame.open(); return; }
        frame = wp.media({
            title: wpresidenceImportLocations.uploadTitle,
            button: { text: wpresidenceImportLocations.choose },
            library: { type: 'text/csv' },
            multiple: false
        });
        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            $('#wpresidence-import-file').val(attachment.url);
        });
        frame.open();
    });

    $('#wpresidence-run-import').on('click', function(e){
        e.preventDefault();
        var file = $('#wpresidence-import-file').val();
        var $status = $('#wpresidence-import-status');
        $status.removeClass('success error').text('');
        if(!file){
            $status.addClass('error').text(wpresidenceImportLocations.noFile);
            return;
        }
        $.post(ajaxurl, {
            action: 'wpresidence_import_locations',
            nonce: wpresidenceImportLocations.nonce,
            file: file
        }, function(response){
            if(response.success){
                $status.addClass('success').text(response.data);
            }else{
                $status.addClass('error').text(response.data);
            }
        });
    });
});
