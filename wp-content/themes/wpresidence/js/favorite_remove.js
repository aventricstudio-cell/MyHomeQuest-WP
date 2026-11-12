/*global $, jQuery, wpestate_favorite_remove_vars*/
/*
*
* Add remove favorites button for card in front end favorite list
* 
*/

function wpestate_enable_front_end_remove_favorites(){

 

    jQuery('.wpestate_latest_listings_sh .listing_wrapper').each(function(event){

      

        var property_card=jQuery(this);
        var property_id = jQuery(this).attr('data-listid');

        var to_append='<div class="remove_fav_dash remove_favorite_fron_end remove_fav_dash" data-postid="'+property_id+'">'+wpestate_favorite_remove_vars.remove_from_favorites+'</div>';
        var new_node = jQuery(to_append);
        property_card.prepend(new_node);
        new_node.find('.remove_favorite_fron_end').click(function(event){
            event.preventDefault();
            alert('functi');
           
        })
    });

}
