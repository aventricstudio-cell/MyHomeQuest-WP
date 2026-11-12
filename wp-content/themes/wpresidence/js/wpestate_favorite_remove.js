/*
*
* Add remove favorites button for card in front end favorite list
* 
*/

function wpestate_enable_front_end_remove_favorites(){

    jQuery('.wpestate_latest_listings_sh .listing_wrapper').each(function(event){

        var property_card=jQuery(this);
        var property_id = jQuery(this).attr('data-listid');

        var to_append='<div class="remove_fav_dash  wpresidence_button " data-postid="'+property_id+'">1'+wpestate_favorite_remove_vars.remove_from_favorites+'/div>';

        property_card.prepend(to_append);
        
    });

}
  