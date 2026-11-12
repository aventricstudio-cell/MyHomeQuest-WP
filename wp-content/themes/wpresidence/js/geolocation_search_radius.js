jQuery(document).ready(function ($) {
    "use strict";
    wpestate_enable_slider_radius('.wpestate_slider_radius_search',geolocation_search_radius_vars.min_geo_radius, geolocation_search_radius_vars.max_geo_radius, geolocation_search_radius_vars.initial_radius);
    wpestate_enable_geolocation_field('.wpestate_slider_radius_search',geolocation_search_radius_vars.min_geo_radius, geolocation_search_radius_vars.max_geo_radius, geolocation_search_radius_vars.initial_radius);
});







function wpestate_enable_slider_radius(slider_name,low_val, max_val, now_val){

    var parent,geolocation_radius ,radius_value;
    
    jQuery('.wpestate_slider_radius_search').each(function(event){
        selected_slider     =   jQuery(this);
        var  parent              =   selected_slider.parent();
        var geolocation_radius  =   parent.find('.geolocation_radius');
        var radius_value        =   parent.find('.radius_value');
        
        wpestate_enable_slider_radius_action(selected_slider,low_val, max_val, now_val,radius_value,geolocation_radius)  
    });

    jQuery('.wpestate_slider_radius_global').each(function(event){
        selected_slider     =   jQuery(this);
        var  parent              =   selected_slider.parent().parent();
        var geolocation_radius  =   parent.find('.geolocation_radius');
        var radius_value        =   parent.find('.radius_value');
        
        wpestate_enable_slider_radius_action(selected_slider,low_val, max_val, now_val,radius_value,geolocation_radius)  
    });
}

/*
*
*
*
*
*/


function wpestate_enable_slider_radius_action(selected_slider,low_val, max_val, now_val,radius_value,geolocation_radius){
    jQuery(selected_slider).slider({
        range: true,
        min: parseFloat(low_val),
        max: parseFloat(max_val),
        value: parseFloat(now_val),
        range: "max",
        slide: function (event, ui) {
        
            geolocation_radius.val( ui.value);
            if(geolocation_search_radius_vars.geo_radius_measure==='miles'){
                radius_value.text(ui.value+" "+geolocation_search_radius_vars.miles);
            }else{
                radius_value.text(ui.value+" "+geolocation_search_radius_vars.km);
            }

        },
        stop: function (event, ui) {
            if(placeCircle!=''){
                if(geolocation_search_radius_vars.geo_radius_measure==='miles'){
                    placeCircle.setRadius(ui.value*1609.34);
                }else{
                    placeCircle.setRadius(ui.value*1000);
                }

 
            if (typeof (wpestate_show_pins) !== "undefined") {
                first_time_wpestate_show_inpage_ajax_half=1;
                wpestate_show_pins();
            }
                        

            }
        }
    });
}


/*
*
*
*
*
*/

function wpestate_enable_geolocation_field(slider_name,low_val, max_val, now_val){

    var geolocation_lat         =   parent.find('.geolocation_lat');
    var geolocation_long        =   parent.find('.geolocation_long');

    jQuery('.geolocation_search_item').each(function(event){
 
        var geolocation_search_item =   jQuery(this);
        var input_id                =   geolocation_search_item.attr('id');
        var input                   =   document.getElementById(input_id);
        var parent                  =   jQuery(this).parent();
        var defaultBounds, autocomplete_normal;

        var geolocation_lat         =   parent.find('.geolocation_lat');
        var geolocation_long        =   parent.find('.geolocation_long');
    
        geolocation_search_item.on('change', function(){

            if( jQuery(this).val()==='' ){
                geolocation_lat.val('');
                geolocation_long.val('');
                if(placeCircle!=''){
                    placeCircle.setMap(null);
                    placeCircle='';
                }
            }
        });
    
        var iconSVG='<svg width="20px" height="20px" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#000000"><path d="M12 27.2C12 46.4 32 56 32 56s20-9.6 20-28.8C52 16.6 43.05 8 32 8s-20 8.6-20 19.2z"/><circle cx="32" cy="26.88" r="6.88"/></svg>';

        geolocation_search_item.autocomplete( {
                source: function ( request, response ) {
                        
                        var search_parameters={
                            format: 'json',
                            q: request.term,//was q                      
                            //addressdetails:'1',
                        };

                      
                        if(geolocation_search_radius_vars.limit_country==='yes'){
                            search_parameters.countrycodes=geolocation_search_radius_vars.limit_country_select;
                        }


                        jQuery.get( 'https://nominatim.openstreetmap.org/search',search_parameters, function( result ) {

                          
                                if ( !result.length ) {
                                    response( [ {
                                        value: '',
                                        label: control_vars.geo_no_results
                                    } ] );
                                    return;
                                }
                                response( result.map( function ( place ) {
                                        return {
                                                label:place.display_name,
                                                latitude: place.lat,
                                                longitude: place.lon,
                                                value: place.display_name,

                                        };
                                } ) );
                        }, 'json' );
                },
                select: function ( event, ui ) {
                    initialGeop=0;
                    geolocation_lat.val(ui.item.latitude);
                    geolocation_long.val(ui.item.longitude);
                    geolocation_search_used=1;
      
                    if (typeof (wpestate_show_pins) !== "undefined") {
                        first_time_wpestate_show_inpage_ajax_half=1;                  
                        wpestate_show_pins();
                    }

                }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return jQuery("<li>")
                 .append('<div class="wpresidence-geolocatiomarker">' + item.label + '</div>')

                .appendTo(ul);
        };


        geolocation_search_item.attr('autocomplete','on');


    
    });

}