/*global  jQuery, document ,dashboard_vars*/

jQuery(document).ready(function () {
    "use strict";
    wpestate_start_direct_pay();
    wpestate_start_direct_paypment_per_listing();
    wpestate_enable_direct_pay();
    wpestate_enable_direct_pay_perlisting();
    wpestate_enable_stripe_recurring();
    wpestate_start_pay_via_woo();
    //wpestate_start_stripe_booking();
    wpestate_close_stripe_form();
    wpestate_enable_stripe_booking_prop();
    wpestate_enable_send_replay_button();
    wpestate_mark_message_as_read();
    wpestate_message_replay_inbox();
    wpestate_message_replay_inbox_2();
    wpestate_message_delete_inbox(); 

    // Initialize county -> city filter
    wpestate_filter_county_city_select('property_county', 'property_city_submit');

    // Initialize city -> area filter
    wpestate_filter_city_area_select('property_city_submit', 'property_area_submit');

});
 


 

/**
 * WpEstate City to Area Filter
 * Handles the dynamic filtering of area/neighborhood options based on city selection.
 * When a city is selected, only shows areas that belong to that city.
 * Special values 'all' and 'none' are always preserved.
 * 
 * Required HTML structure:
 * - Select element for cities with id="property_city_submit"
 * - Select element for areas with id="property_area_submit"
 * - Area options must have data-parentcity attribute matching city values
 * 
 * @since 5.0.5
 */
function wpestate_filter_city_area_select() {
    // Cache original area options on initialization for reset capability
    var originalAreaOptions = jQuery('#property_area_submit option').clone();
    
    jQuery('#property_city_submit').on('change', function() {
        var city_value = jQuery(this).val();
        
        // Reset area select to original state before filtering
        jQuery('#property_area_submit').empty().append(originalAreaOptions.clone());
        
        // Only filter if a specific city is selected
        if (city_value && city_value !== '-1' && city_value !== 'all' && city_value !== 'none') {
            jQuery('#property_area_submit option').each(function() {
                var area_parentcity = jQuery(this).attr('data-parentcity') || '';
                
                // Remove areas that don't match the selected city and aren't special values
                if (area_parentcity !== city_value && 
                    area_parentcity !== 'all' && 
                    area_parentcity !== 'none') {
                    jQuery(this).remove();
                }
            });
        }
    });
}

/**
 * WpEstate County/State to City Filter
 * Handles the dynamic filtering of city options based on county/state selection.
 * When a county is selected, only shows cities that belong to that county.
 * Special values 'all' and 'none' are always preserved.
 * 
 * Required HTML structure:
 * - Select element for counties with id="property_county"
 * - Select element for cities with id="property_city_submit"
 * - City options must have data-parentcounty attribute matching county values
 * 
 * @since 5.0.5
 */
function wpestate_filter_county_city_select() {
    // Cache original city options on initialization for reset capability
    var originalCityOptions = jQuery('#property_city_submit option').clone();
    
    jQuery('#property_county').on('change', function() {
        var county_value = jQuery(this).val();
        
        // Reset city select to original state before filtering
        jQuery('#property_city_submit').empty().append(originalCityOptions.clone());
        
        // Only filter if a specific county is selected
        if (county_value && county_value !== '-1') {
            jQuery('#property_city_submit option').each(function() {
                var city_parentcounty = jQuery(this).attr('data-parentcounty') || '';
                
                // Remove cities that don't match the selected county and aren't special values
                if (city_parentcounty !== county_value && 
                    city_parentcounty !== 'all' && 
                    city_parentcounty !== 'none') {
                    jQuery(this).remove();
                }
            });
        }
        
        // Reset city select to first option
        jQuery('#property_city_submit').val(jQuery('#property_city_submit option:first').val());
        
        // Trigger city change to update area options accordingly
        jQuery('#property_city_submit').trigger('change');
    });
}














/*
* Close stripe payment
*
*/


function wpestate_close_stripe_form(){

    jQuery('.close_stripe_form').on('click',function(){
        jQuery('.wpestate_stripe_form_wrapper').hide();
        jQuery('.wpestate_stripe_form_1').hide();
    });
}


/*
*
*
*/
function wpestate_enable_stripe_booking_prop(){

    jQuery('.wpestate_stripe_booking_prop').unbind();
    jQuery('.wpestate_stripe_booking_prop').on('click',function(){
        var parent=jQuery(this).parent();
        var ajaxurl     =   ajaxcalls_vars.admin_url + 'admin-ajax.php';
        var listingid=jQuery(this).attr('data-listingid');
        var isfeatured=jQuery(this).attr('data-isfeatured');
        var modalid=jQuery(this).attr('data-modalid');
        var modal = jQuery('#' + modalid);
        modal.appendTo('body');
        
        modal.show();
            
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            //  dataType: 'json',
            data: {
                'action'    :   'wpestate_create_payment_intent_stripe',
                'listingid' :   listingid,
                'isfeatured':   isfeatured
                
            },
            success: function (data) {
       
                jQuery('#'+modalid+' .wpestate_stripe_form_1').show();
                jQuery('#'+modalid).find('#wpestate_stripe_form_button_sumit').attr('data-secret',data);
                wpestate_start_stripe(0,modalid);
            },
            error: function (errorThrown) {

            }
        });//end ajax

        
    });









    jQuery('.close_stripe_form').on('click',function(){
        jQuery('.wpestate_stripe_form_wrapper').hide();
        jQuery('.wpestate_stripe_form_1').hide();
    });
}

/*
* Start stripe payment
*
*/

function wpestate_start_stripe_booking(){
    jQuery('#wpestate_stripe_booking').on('click',function(){
        var modalid=jQuery(this).attr('data-modalid');

        jQuery('#'+modalid).show();
        jQuery('#'+modalid+' .wpestate_stripe_form_1').show();

        wpestate_start_stripe(0,modalid);
    });

}

/*
* Start payment via woocommerce 
*
*/

function wpestate_start_pay_via_woo(){
    jQuery('.woo_pay').on('click',function () {
        var pay_paypal, prop_id, book_id, invoice_id, is_featured, is_upgrade,ajaxurl,depozit,is_submit;
        prop_id     =   jQuery(this).attr('data-propid');
        book_id     =   jQuery(this).attr('data-bookid');
        invoice_id  =   jQuery(this).attr('data-invoiceid');
        depozit     =   jQuery(this).attr('data-deposit');
        is_featured =   0;
        is_upgrade  =   0;
        is_submit   =   0;

        if(jQuery(this).hasClass('woo_pay_submit')){
            is_submit=1;
        }
        ajaxurl     =   ajaxcalls_vars.admin_url + 'admin-ajax.php';
         jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    'action'        :   'wpestate_woo_pay',
                    'invoiceid'     :   invoice_id,
                    'propid'        :   prop_id,
                    'book_id'       :   book_id,
                    'depozit'       :   depozit,
                    'is_submit'     :   is_submit
//                    'price_pack'    :   price_pack,
//                    'security'      :   nonce,
                },
                success: function (data) {
                    if(data!==false){
                     window.location.href= dashboard_vars.checkout_url;
                    }
                },
                error: function (errorThrown) {}
            });//end ajax
    });

}

/*
* 
*
*/

function wpestate_message_delete_inbox(){

    jQuery('.mess_delete').on( 'click', function(event) {

        var messid, ajaxurl, acesta, parent;
        event.stopPropagation();
        ajaxurl =   dashboard_vars.admin_url + 'admin-ajax.php';
        parent  =   jQuery(this).closest('.message_listing');	
        messid  =   parent.attr('data-messid');
        acesta  =   jQuery(this);

        jQuery(this).parent().parent().empty().addClass('delete_inaction').html(dashboard_vars.deleting);
        var nonce = jQuery('#wpestate_inbox_actions').val();
       
        parent.remove();
    


        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action'            :   'wpestate_booking_delete_mess',
                'messid'            :   messid,
                'security'          :   nonce
            },
            success: function (data) {
      
                jQuery('.mess_content, .mess_reply_form').hide();
            },
            error: function (errorThrown) {

            }
        });
    });
}

/*
* 
*
*/
function wpestate_message_replay_inbox_2(){

    jQuery('.mess_send_reply_button2').on( 'click', function(event) {

        var messid, ajaxurl, acesta, parent;
        event.stopPropagation();
        ajaxurl =   dashboard_vars.admin_url + 'admin-ajax.php';
        parent  =   jQuery(this).parent().parent().parent();
        acesta  =   jQuery(this);
        jQuery('.mess_content, .mess_reply_form').hide();
        parent.find('.mess_content').show();
        parent.find('.mess_reply_form').show();
    });

}


/*
* Message reply
*
*/

function wpestate_message_replay_inbox(){

    jQuery('.mess_reply').on( 'click', function(event) {

        var messid, ajaxurl, acesta, parent;
        event.stopPropagation();
        ajaxurl =   dashboard_vars.admin_url + 'admin-ajax.php';
        parent  =   jQuery(this).parent().parent().parent();
        messid  =   parent.attr('data-messid');
        acesta  =   jQuery(this);
        jQuery('.mess_content, .mess_reply_form').hide();
        parent.find('.mess_content').show();
        parent.find('.mess_reply_form').show();
        var nonce = jQuery('#wpestate_inbox_actions').val();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action'            :   'wpestate_booking_mark_as_read',
                'messid'            :   messid,
                 'security'          :   nonce
            },
            success: function (data) {
                parent.find('.mess_unread').remove();
            },
            error: function (errorThrown) {
            }
        });
    });

}

/*
* Mark Message as ready
*
*/

function wpestate_mark_message_as_read(){
    jQuery('.message_header').on( 'click', function() {

        var messid, ajaxurl, acesta, parent;
        ajaxurl =   dashboard_vars.admin_url + 'admin-ajax.php';
        parent  =   jQuery(this).parent();
        messid  =   parent.attr('data-messid');
        acesta  =   jQuery(this);
        jQuery(this).parents('.inbox_row').addClass('inbox_row_open');
        jQuery('.mess_content, .mess_reply_form').hide();
        jQuery(this).parent().find('.mess_content').show();
        var nonce = jQuery('#wpestate_inbox_actions').val();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action'            :   'wpestate_booking_mark_as_read',
                'messid'            :   messid,
                'security'          :   nonce
            },
            success: function (data) {
                parent.find('.mess_unread').remove();
            },
            error: function (errorThrown) {
            }
        });
    });

}



/*
* Enable message reply buttton
*
*/

function wpestate_enable_send_replay_button(){
    jQuery('.mess_send_reply_button').on( 'click', function(event) {

        var messid, ajaxurl, acesta, parent, title, content, container, mesage_container;
        ajaxurl    =   dashboard_vars.admin_url + 'admin-ajax.php';
        parent     =   jQuery(this).parent().parent();
        mesage_container = parent.find('.mess_content');
        container  =   jQuery(this).parent();
        messid     =   parent.attr('data-messid');
        acesta     =   jQuery(this);
        title      =   parent.find('.subject_reply').val();
        content    =   parent.find('.message_reply_content').val();
        parent.find('.mess_unread').remove();
        acesta.text(dashboard_vars.sending);
        var nonce = jQuery('#wpestate_inbox_actions').val();

  
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            dataType: 'json',
            data: {
                'action'            :   'wpestate_message_reply',
                'messid'            :   messid,
                'title'             :   title,
                'content'           :   content,
                'security'          :   nonce
            },
            success: function (data) {
     
                mesage_container.hide();
                container.hide();
                acesta.text(dashboard_vars.send_reply);
                content    =   parent.find('.message_reply_content').val('');
            },
            error: function (errorThrown) {

            }
        });
    });
}








/*
*  Enable stripe recuring
*/



function wpestate_enable_stripe_recurring(){
       
    jQuery('#pack_recuring').on( 'click', function(event) {
        if( jQuery(this).attr('checked') ) {
            jQuery('#stripe_form').append('<input type="hidden" name="stripe_recuring" id="stripe_recuring" value="1">');
        }else{
            jQuery('#stripe_recuring').remove();
        }
    });

}



/*
*  Direct pay 
*/
function wpestate_start_direct_pay() {
    jQuery('#direct_pay').on('click', function(event) {

        var selected_pack, selected_prop, include_feat, attr, price_pack, packName;
        packName = jQuery('.package_selected .pack-listing-title').text();
        selected_pack = jQuery('.package_selected .pack-listing-title').attr('data-packid');
        price_pack = jQuery('.package_selected .pack-listing-title').attr('data-packprice');
        
        if (control_vars.where_curency === 'after') {
            price_pack = price_pack + ' ' + control_vars.submission_curency;
        } else {
            price_pack = control_vars.submission_curency + ' ' + price_pack;
        }
        price_pack = control_vars.direct_price + ': ' + price_pack;
        
        if (selected_pack !== '') {
            window.scrollTo(0, 0);
            // Updated modal HTML for Bootstrap 5
            var direct_pay_modal = `
                <div class="modal fade" id="direct_pay_modal" tabindex="-1" aria-labelledby="directPayModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="directPayModalLabel">${control_vars.direct_title}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body listing-submit">
                                <span class="to_be_paid">${price_pack}</span>
                                <span>${control_vars.direct_pay}</span>
                                <div id="send_direct_bill" data-pack="${selected_pack}">${control_vars.send_invoice}</div>
                            </div>
                        </div>
                    </div>
                </div>`;

            jQuery('body').append(direct_pay_modal);
            
            // Initialize modal using Bootstrap 5 syntax
            var myModal = new bootstrap.Modal(document.getElementById('direct_pay_modal'));
            myModal.show();
            
            wpestate_enable_direct_pay();
        }
        
        // Updated event listener for modal hidden
        document.getElementById('direct_pay_modal').addEventListener('hidden.bs.modal', function() {
            jQuery('#direct_pay_modal').remove();
        });
    });
}








/*
*  Payment trigger for direct pay when per listing
*/

function wpestate_start_direct_paypment_per_listing(){
 
    jQuery('.perpack').on( 'click', function(event) {
        var direct_pay_modal, selected_pack,selected_prop,include_feat,attr;
        selected_prop   =   jQuery(this).attr('data-listing');

        var price_pack  =   jQuery(this).attr('data-price-submission');


        if (control_vars.where_curency === 'after'){
            price_pack = price_pack +' '+control_vars.submission_curency;
        }else{
            price_pack = control_vars.submission_curency+' '+price_pack;
        }

        price_pack=control_vars.direct_price+': '+price_pack;


        include_feat=' data-include-feat="0" ';
        jQuery('#send_direct_bill').attr('data-include-feat',0);
        jQuery('#send_direct_bill').attr('data-listing',selected_prop);

        if ( jQuery(this).parent().find('.extra_featured').attr('checked') ){
            include_feat=' data-include-feat="1" ';
            jQuery('#send_direct_bill').attr('data-include-feat',1);
        }

        attr = jQuery(this).attr('data-isupgrade');
        if (typeof attr !== typeof undefined && attr !== false) {
            include_feat=' data-include-feat="1" ';
            jQuery('#send_direct_bill').attr('data-include-feat',1);
        }


        direct_pay_modal = `
        <div class="modal fade" id="direct_pay_modal" tabindex="-1" aria-labelledby="directPayModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="directPayModalLabel">${control_vars.direct_title}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body listing-submit">
                <span class="to_be_paid">${price_pack}</span>
                <span>${control_vars.direct_pay}</span>
                <div id="send_direct_bill" ${include_feat} data-listing="${selected_prop}">
                  ${control_vars.send_invoice}
                </div>
              </div>
            </div>
          </div>
        </div>`;
        

        
        jQuery('body').append(direct_pay_modal);
        // Create new modal instance
        const directPayModal = new bootstrap.Modal(document.getElementById('direct_pay_modal'));
        directPayModal.show();
        
        wpestate_enable_direct_pay_perlisting();
        
        // Handle modal hidden event
        document.getElementById('direct_pay_modal').addEventListener('hidden.bs.modal', function (e) {
          jQuery('#direct_pay_modal').remove();
        });

    });

}




/*
*
* Enable direct pay per listing
*
*/
function  wpestate_enable_direct_pay_perlisting(){

    jQuery('#send_direct_bill').unbind('click');
    jQuery('#send_direct_bill').on( 'click', function(event) {
        jQuery('#send_direct_bill').unbind('click');
        var selected_pack,ajaxurl,include_feat;

        selected_pack   =   jQuery(this).attr('data-listing');
        include_feat    =   jQuery(this).attr('data-include-feat');
        ajaxurl         =   ajaxcalls_vars.admin_url + 'admin-ajax.php';
        jQuery('.paymentmodal').hide();
        var   nonce = jQuery('#wpresidence_simple_pay_actions_nonce').val();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action'            :   'wpestate_direct_pay_pack_per_listing',
                'selected_pack'     :   selected_pack,
                'include_feat'      :   include_feat,
                'security'          :   nonce,
            },
            success: function (data) {
                jQuery('#send_direct_bill').hide();
                jQuery('#direct_pay_modal .listing-submit span:nth-child(2)').empty().html(dashboard_vars.direct_thx);
            },
            error: function (errorThrown) {}
        });//end ajax

    });

}




/*
*
* Enable direct pay 
*
*/

function wpestate_enable_direct_pay(){
    jQuery('#send_direct_bill').on( 'click', function(event) {
        jQuery('#send_direct_bill').unbind('click');
        var selected_pack,ajaxurl;
        selected_pack=jQuery(this).attr('data-pack');
        ajaxurl     =   ajaxcalls_vars.admin_url + 'admin-ajax.php';
        var   nonce = jQuery('#wpresidence_simple_pay_actions_nonce').val();
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action'            :   'wpestate_direct_pay_pack',
                'selected_pack'     :   selected_pack,
                'security'          :   nonce
            },
            success: function (data) {
                jQuery('#send_direct_bill').hide();
                jQuery('#direct_pay_modal .listing-submit span:nth-child(2)').empty().html(dashboard_vars.direct_thx);

            },
            error: function (errorThrown) {}
        });//end ajax

    });

}
