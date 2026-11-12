/*global $, jQuery, document, window,wpestate_crm_script_vars */
jQuery(document).ready(function ($) {
    
    $('#crm_insert_comment').on( 'click', function(event) {
        var item_id,ajaxurl;
        
        item_id     =   $(this).attr('data-postid');
        ajaxurl     =   wpestate_crm_script_vars.ajaxurl;
        content     =   jQuery('#crm_new_commnet').val();
        var who     =   $(this).attr('data-who');
        var date     =   $(this).attr('data-date');
        if(content===''){
            return;
        }
        var nonce   =   jQuery('#wpestate_crm_insert_note').val();
    
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action'        :   'wpestate_crm_add_comment',
                'item_id'       :   item_id,
                'content'       :   content,
                'security'      :   nonce,
           
        },
        success: function (data) {  
            var to_insert='<div class="comment_item"><div class="comment_name">'+who+'</div><div class="comment_date">on '+date+'</div><div class="comment_content">'+content+'</div></div>';
            jQuery('#show_notes_wrapper').prepend(to_insert);
            jQuery('#crm_new_commnet').val('');
        },
        error: function (errorThrown) {
            
        }
    });//end ajax  
        
    });
    
    
    
       
    $('.wpestate-crm_delete').on( 'click', function(event) {
        var item_id,ajaxurl;
        
        item_id     =   $(this).attr('data-delete');
        ajaxurl     =   wpestate_crm_script_vars.ajaxurl;
        var to_remove=   $(this).parent();
        var nonce   =   jQuery('#wpestate_crm_insert_note').val();
    
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action'        :   'wpestate_crm_delete_comment',
                'item_id'       :   item_id,
                'security'      :   nonce,
           
        },
        success: function (data) {  
            to_remove.remove();
        },
        error: function (errorThrown) {
            
        }
    });//end ajax  
        
    });
    
    
});