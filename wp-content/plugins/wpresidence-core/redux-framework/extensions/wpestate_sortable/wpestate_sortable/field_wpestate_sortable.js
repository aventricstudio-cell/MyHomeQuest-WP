/*global redux, redux_opts*/
/*
 * Field Sorter jquery function
 * Based on
 * [SMOF - Slightly Modded Options Framework](http://aquagraphite.com/2011/09/slightly-modded-options-framework/)
 * Version 1.4.2
 */


function wpestate_sorter(){
     "use strict";
    // Initialize redux.sorter if it doesn't exist
    redux.sorter = redux.sorter || {};

        // Get the opt_name from the Redux container
        var $reduxContainer = jQuery('.redux-container');
        var opt_name = $reduxContainer.data('opt-name') || 'wpresidence_admin'; // Fallback to your option name
  
        
             jQuery('.redux-container-wpestate_sortable').find( '.redux-sorter-wpestate' ).each(
                    function() {
                        var id = jQuery( this ).attr( 'id' );
                        var el = jQuery(this);
                        el.find( 'ul' ).sortable(
                            {
                                items: 'li',
                                placeholder: "placeholder",
                                connectWith: '.sortlist_' + id,
                                opacity: 0.8,
                                scroll: false,
                                out: function( event, ui ) {
                                    if ( !ui.helper ) return;
                                    if ( ui.offset.top > 0 ) {
                                        scroll = 'down';
                                    } else {
                                        scroll = 'up';
                                    }
                                    redux.field_objects.sorter.scrolling( jQuery( this ).parents( '.redux-field-container:first' ) );

                                },
                                over: function( event, ui ) {
                                    scroll = '';
                                },

                                deactivate: function( event, ui ) {
                                    scroll = '';
                                },

                                stop: function( event, ui ) {
                                    var sorter = redux.sorter[jQuery( this ).attr( 'data-id' )];
                                    var id = jQuery( this ).find( 'h3' ).text();
                                },

                                update: function( event, ui ) {
                                    var sorter = redux.sorter[jQuery( this ).attr( 'data-id' )];
                                    var id = jQuery( this ).find( 'h3' ).text();

 
                                    jQuery( this ).find( '.position' ).each(
                                        function() {
                                            //var listID = jQuery( this ).parent().attr( 'id' );
                                            var listID = jQuery( this ).parent().attr( 'data-id' );
                                            var parentID = jQuery( this ).parent().parent().attr( 'data-group-id' );

                                            redux_change( jQuery( this ) );

                                            var optionID = jQuery( this ).parent().parent().parent().attr( 'id' );

                                            jQuery( this ).prop(
                                                "name",
                                                 opt_name + '[' + optionID + '][' + parentID + '][' + listID + ']'
                                            );
                                        }
                                    );
                                }
                            }
                        );
                        el.find( ".redux-sorter-wpestate" ).disableSelection();
                    }
                );




}