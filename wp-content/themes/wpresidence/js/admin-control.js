/*global $, jQuery, document, window */
window.wpestateInitMapAdmin = function () {
}
 
jQuery(document).ready(function ($) {

    wpestate_upload_background_image_menu_wpadmin();
    wpestate_remove_backround_image_menu_wpadmin();
    wpestate_upload_images_in_wpadmin();
    wpestate_admin_upload_floor_buttons();
    wpestate_admin_add_new_floor_button_action();
    wpestate_admin_delete_floor_button_action();
    wpestate_activate_pack_wpadmin();
    wpestate_activate_pack_listings_wpadmin();
    wpestate_save_custom_unit_design_wpadmin();
    wpestate_add_wpestate_template();
    scrollToTopOnClick();
    // Initialize the toggle switches
    initializeToggleSwitches();

	// agent custom parameters processing

	jQuery('body').on('click', '.add_custom_parameter', function(){
		var cloned = jQuery('.cliche_row').clone();
		cloned.removeClass('cliche_row');
		jQuery('input', cloned).val();
		jQuery('.add_custom_data_cont').append( cloned );
	})


	jQuery('body').on('click', '.remove_parameter_button', function(){
		var pnt = jQuery(this).parents( '.single_parameter_row' );
		pnt.fadeOut(500, function(){
			pnt.replaceWith('');
		})

	})


    
	// agent custom parameters processing END
 
});


function scrollToTopOnClick() {
    // Get all elements with the class 'redux-group-tab-link-li'
    const targetElements = document.querySelectorAll('.redux-group-tab-link-li');
    
    targetElements.forEach(function(element) {
        element.addEventListener('click', function(event) {
            // Scroll to top of the page smoothly
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
}

 /**
 * Initialize toggle switch functionality
 */
function initializeToggleSwitches() {
    // Handle toggle switch changes
    jQuery('.wpresidence-toggle-input').on('change', function() {
        var jQueryThis = jQuery(this);
        var jQueryTextSpan = jQueryThis.closest('.wpresidence-toggle-wrapper').find('.wpresidence-toggle-text');
        
        // Update the text based on toggle state
        if (jQueryThis.is(':checked')) {
            jQueryTextSpan.text('Enabled');
            jQueryTextSpan.css('color', '#4CAF50');
        } else {
            jQueryTextSpan.text('Disabled');
            jQueryTextSpan.css('color', '#999');
        }
    });
    
    // Initialize text colors on page load
    jQuery('.wpresidence-toggle-input').each(function() {
        var jQueryThis = jQuery(this);
        var jQueryTextSpan = jQueryThis.closest('.wpresidence-toggle-wrapper').find('.wpresidence-toggle-text');
        
        if (jQueryThis.is(':checked')) {
            jQueryTextSpan.css('color', '#4CAF50');
        } else {
            jQueryTextSpan.css('color', '#999');
        }
    });
    
    // Add keyboard support for accessibility
    jQuery('.wpresidence-toggle-label').on('keydown', function(e) {
        // Toggle on Space or Enter key
        if (e.which === 32 || e.which === 13) {
            e.preventDefault();
            jQuery(this).prev('.wpresidence-toggle-input').click();
        }
    });
    
    // Make labels focusable for keyboard navigation
    jQuery('.wpresidence-toggle-label').attr('tabindex', '0');
}
        
      











/*
* return uploaded image
*
*/
function wpestate_admin_return_uploaded_image(){

    return new Promise(function(resolve, reject) {
        var mediaUploader = wp.media({
          frame: "post",
          state: "insert",
          multiple: false
        });
    
        mediaUploader.on("insert", function(){
          var image = mediaUploader.state().get("selection").first().toJSON();
          resolve(image);
        });
    
        mediaUploader.open();
      });
}


function wpesteate_admin_upload_image_gallery(){
    jQuery('#button_new_image').on( 'click', function(event) {
        e.preventDefault();
        var parent = jQuery(this).parent();
        
        wpestate_admin_return_uploaded_image().then(function(image) {        
            var imgurl = image.url;
            var thenum  =image.id;
            parent.find('#plan_image').val(imgurl);
            parent.find('#plan_image_attach').val(thenum);
          });


    });
}




/*
* Upload floor button
*
*/
function wpestate_admin_upload_floor_buttons(){
    jQuery('.floorbuttons').on( 'click', function(e) {
        e.preventDefault();
        var parent = jQuery(this).parent();
       
        wpestate_admin_return_uploaded_image().then(function(image) {        
            var imgurl = image.url;
            var thenum  =image.id;
            parent.find('#plan_image').val(imgurl);
            parent.find('#plan_image_attach').val(thenum);

            wpestate_break_attachment(thenum);
          });
    });
}


function wpestate_break_attachment(attachmentID){
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        dataType: 'json',
        data: {
            'action'        :   'wpestate_break_attachment',
            'attachmentID'       :   attachmentID,
        },
        success: function (data) {

        },
        error: function (errorThrown) {}
});//end ajax
    
}



/*
* Upload images in admin
*
*/
function wpestate_upload_images_in_wpadmin(){

    var idList          = ["category_featured_image_button","page_custom_image_button","property_custom_video_button", "page_custom_video_webbm_button","page_custom_video_button", "page_custom_video_cover_image_button", "page_custom_video_ogv_button"];

    for (var i = 0; i < idList.length; i++) {
        var currentId = idList[i];
        jQuery('#'+currentId).on( 'click', function(event) {
            var parent=jQuery(this).parent();
            wpestate_admin_return_uploaded_image().then(function(image) {      
                parent.find('.wpestate_landing_upload').val(image.url);
                parent.find('.wpestate_landing_upload_id').val(image.id);
                wpestate_break_attachment(image.id);
            });
          
        });

    }
}



/*
* Upload image for backgorund menu
*
*/
function wpestate_upload_background_image_menu_wpadmin(){

    jQuery('.load_back_menu').on( 'click', function() {

        var parent = jQuery(this).parent().parent();
        var item_id = this.id.replace('wpestate-media-upload-', '');
        wpestate_admin_return_uploaded_image().then(function(image) {      
            parent.find('#edit-menu-item-megamenu-background-'+item_id).val(image.url);
            parent.find( '#wpestate-media-img-'+item_id ).attr( 'src', image.url ).css( 'display', 'block' );
            
        });

    });
}



/*
* remove backgorund image in menu
*
*/


function wpestate_remove_backround_image_menu_wpadmin(){

    jQuery('.remove-megamenu-background').on( 'click', function(event) {
        event.preventDefault();
        var  item_id = this.id.replace( 'wpestate-media-remove-', '' );
        jQuery( '#edit-menu-item-megamenu-background-'+item_id ).val( '' );
        jQuery( '#wpestate-media-img-'+item_id ).attr( 'src', '' ).css( 'display', 'none' );
    });

}






/*
* Add new plan action
*
*/


function wpestate_admin_add_new_floor_button_action(){
    jQuery('#add_new_plan').on( 'click', function(event) {
        var to_insert;

        to_insert='<div class="plan_row"><i class=" deleter_floor far fa-trash-alt"></i><p class="meta-options floor_p">\n\
                <label for="plan_title">'+admin_control_vars.plan_title+'</label><br />\n\
                <input id="plan_title" type="text" size="36" name="plan_title[]" value="" />\n\
            </p>\n\
            \n\
            <p class="meta-options floor_p"> \n\
                <label for="plan_description">'+admin_control_vars.plan_desc+'</label><br /> \n\
                <textarea class="plan_description" type="text" size="36" name="plan_description[]" ></textarea> \n\
            </p>\n\
             \n\
            <p class="meta-options floor_p"> \n\
                <label for="plan_size">'+admin_control_vars.plan_size+'</label><br /> \n\
                <input id="plan_size" type="text" size="36" name="plan_size[]" value="" /> \n\
            </p> \n\
            \n\
            <p class="meta-options floor_p"> \n\
                <label for="plan_rooms">'+admin_control_vars.plan_rooms+'</label><br /> \n\
                <input id="plan_rooms" type="text" size="36" name="plan_rooms[]" value="" /> \n\
            </p> \n\
            <p class="meta-options floor_p"> \n\
                <label for="plan_bath">'+admin_control_vars.plan_bathrooms+'</label><br /> \n\
                <input id="plan_bath" type="text" size="36" name="plan_bath[]" value="" /> \n\
            </p> \n\
            <p class="meta-options floor_p"> \n\
                <label for="plan_price">'+admin_control_vars.plan_price+'</label><br /> \n\
                <input id="plan_price" type="text" size="36" name="plan_price[]" value="" /> \n\
            </p> \n\
            \n\<p class="meta-options floor_p image_plan"> \n\
                <label for="plan_image">'+admin_control_vars.plan_image+'</label><br /> \n\
                <input id="plan_image" type="text" size="36" name="plan_image[]" value="" /> \n\
                <input id="plan_image_button" type="button"   size="40" class="upload_button button floorbuttons" value="Upload Image" /> \n\
                <input type="hidden" id="plan_image_attach" name="plan_image_attach[]" value="">\n\
            </p> \n\
    </div>';

        jQuery('#plan_wrapper').append(to_insert);
        jQuery('.floorbuttons').unbind('click');
        wpestate_admin_upload_floor_buttons();
        wpestate_admin_delete_floor_button_action();
    });

}




/*
* Delete plan
*
*/

function wpestate_admin_delete_floor_button_action(){
    jQuery('.deleter_floor').unbind('click').on( 'click', function(event) {
        jQuery(this).parent().remove();
    });
}


/*
* Activate pack in admin
*
*/

function wpestate_activate_pack_wpadmin(){
    jQuery('#activate_pack').on( 'click', function(event) {
        var item_id, invoice_id,ajaxurl;
    
        item_id     = jQuery(this).attr('data-item');
        invoice_id  = jQuery(this).attr('data-invoice');
        ajaxurl     =   admin_control_vars.ajaxurl;
        var nonce = jQuery('#wpestate_activate_pack').val();
    
    
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
        data: {
            'action'        :   'wpestate_activate_purchase',
            'item_id'       :   item_id,
            'invoice_id'    :   invoice_id,
            'security'      :   nonce
    
        },
        success: function (data) {
            jQuery("#activate_pack").remove();
            jQuery("#invnotpaid").remove();
    
        },
        error: function (errorThrown) {}
    });//end ajax
    
    });
    
}


/*
* Activate pack in admin - per listing purchase
*
*/


function wpestate_activate_pack_listings_wpadmin(){

    jQuery('#activate_pack_listing').on( 'click', function(event) {
        var item_id, invoice_id,ajaxurl,type;

        item_id     = jQuery(this).attr('data-item');
        invoice_id  = jQuery(this).attr('data-invoice');
        type        = jQuery(this).attr('data-type');
        ajaxurl     =   admin_control_vars.ajaxurl;

        var nonce = jQuery('#wpestate_activate_pack_listing').val();

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action'        :   'wpestate_activate_purchase_listing',
                'item_id'       :   item_id,
                'invoice_id'    :   invoice_id,
                'type'          :   type,
                'security'      :   nonce,

        },
        success: function (data) {
            jQuery("#activate_pack_listing").remove();
            jQuery("#invnotpaid").remove();


        },
        error: function (errorThrown) {}
    });//end ajax

    });
}

/*
* add wpestate tempalted
*
*/
function wpestate_add_wpestate_template(){

     jQuery("#wpestate_add_head_foot_add_button").on("click", function () {

       var title = jQuery("#wpestate_add_head_foot_name").val();
       var template = jQuery("#wpestate_add_head_foot_template").val();
       var location = jQuery("#wpestate_add_head_foot_location").val();
       var nonce = jQuery("#wpestate_add_head_foot_nonce").val();

       
       // Check if any field is blank
       if (!title || !template || !location) {
         var notification = jQuery(
           "#wpestate_add_head_foot_wrapper_notification"
         );
         notification.html("Please fill in all fields.");
         notification.show();
         return;
       }

       jQuery.ajax({
         type: "post",
         url: ajaxurl,
         data: {
           action: "wpestate_add_head_foot",
           title: title,
           template: template,
           location: location,
           nonce: nonce,
         },
         success: function (response) {
           if (response.success) {
            window.location.reload(); // Reload the page on success
           } else {
            alert("Error! Please try again");
           }
         },
       });
     });
}

/*
* Activate pack in admin - per listing purchase
*
*/
function wpestate_save_custom_unit_design_wpadmin(){
    jQuery('#save_prop_design').on( 'click', function(event) {

        var acesta,content,sidebar_right,sidebar_left,content_to_parse,use_unit;

        use_unit =0;
        if( jQuery('#wpresidence_admin_wpestate_uset_unit').is(":checked") ){
            use_unit = 1;
        }
        content = jQuery('#property_page_content .property_page_content_wrapper').html();
        var nonce=jQuery('#wpestate_save_prop_design').val();


        acesta=jQuery(this);
        acesta.empty().text('saving....');
        jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
            data: {
                'action'        :   'wpestate_save_property_page_design',
                'content'       :   content,
                'use_unit'      :   use_unit,
                'security'      :   nonce,
            },
            success: function (data) {
                acesta.empty().text('saved....');
            },
            error: function (errorThrown) {

            }
        });//end ajax


    })

}