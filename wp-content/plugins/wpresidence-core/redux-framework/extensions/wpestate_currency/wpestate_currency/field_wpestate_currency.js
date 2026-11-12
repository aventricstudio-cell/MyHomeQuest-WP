/* global confirm, redux, redux_change */

jQuery(document).ready(function() {
    jQuery('#add_curency2').click(function (event) {
        event.preventDefault();
        console.log('add_curency2');
        var newfield, field_name, field_value, field_order,field_label;
        newfield = '';
        field_name  =   jQuery('#currency_name').val();
        field_label =   jQuery('#currency_label').val();
        field_value =   jQuery('#currency_value').val();
        field_order =   jQuery('#where_cur').val();

        newfield =  '<div    class=field_row>';
        newfield += '<div    class=field_item><strong>Currency Name</strong></br> <input  type="text" name="wpresidence_admin[wpestate_currency][add_curr_name][]"   value="' + field_name + '"  ></div>';
        newfield += '<div    class=field_item><strong>Currency Label</strong></br><input  type="text" name="wpresidence_admin[wpestate_currency][add_curr_label][]"  value="' + field_label + '"  ></div>';     
        newfield += '<div    class=field_item><strong>Currency Value</strong></br><input  type="text" name="wpresidence_admin[wpestate_currency][add_curr_value][]"  value="' + field_value + '"  ></div>';
        newfield += '<div    class=field_item><strong>Currency Order</strong></br><input  type="text" name="wpresidence_admin[wpestate_currency][add_curr_order][]"  value="' + field_order + '"  ></div>';
        newfield += '<a class="deletefieldlink" href="#">delete</a>';
        newfield += '</div>';

        jQuery('#custom_fields').append(newfield);
        jQuery('#currency_name').val('');
        jQuery('#currency_label').val('');
        jQuery('#where_cur').val('');
        jQuery('#where_cur').val('');
        
        wpestate_delete_custom_fields_row();
            
    });
    
   
});
