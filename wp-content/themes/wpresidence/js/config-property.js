/*global $, jQuery, document */
//jslint checked 

jQuery(document).ready(function ($) {
    "use strict";
    var new_feature, current_features, new_status, current_status, parent_div, field_type;
    new_feature = null;
    current_features = null;
    new_status = null;
    current_status = null;


    field_type = 'short text';
    $('#field_type').on('change', function(){ 
        field_type = this.value;
    });
    
 
});