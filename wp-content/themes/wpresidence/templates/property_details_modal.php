<?php
global $post;
  if ( !wp_script_is( 'googlemap', 'enqueued' )) {
        wpestate_load_google_map();
    }
?>

<div id="property_details_modal_wrapper">

    <div class="property_details_modal_back"></div>
    <div class="property_details_modal_container">
        <div id="property_details_modal_close">
            <i class="fas fa-times"></i>
        </div>
     
        <div id="property_modal_images">
           
        </div>  
        
        <div id="property_modal_header">
                <div id="property_modal_top_bar"></div>
                <h3 class="modal_property_title"></h3>
                
                <div class="modal_property_price"></div>
                <div class="modal_property_bed"></div>
                <div class="modal_property_addr"></div>
            
                <input type="submit" id="modal_contact_agent" class="wpresidence_button agent_submit_class "  value="<?php esc_html_e('Contact Agent','wpresidence')?>">
               
        </div>
        
        
        <div id="property_modal_content">
          
            <div id="modal_property_agent" class="modal_content_block"></div>
            <div class="modal_property_description modal_content_block"></div>
            <div class="modal_property_adress modal_content_block"></div>
            <div class="modal_property_details modal_content_block"></div>
            <div class="modal_property_features modal_content_block"></div>           
            <div class="modal_property_video modal_content_block"></div> 
            <div class="modal_property_video_tour modal_content_block"></div> 
            <div class="modal_property_walkscore  modal_content_block"></div> 
            <div class="modal_property_yelp modal_content_block"></div> 
            <div class="modal_property_floor_plans modal_content_block"></div> 
            <div id="modal_property_maps" class="modal_property_maps modal_content_block"></div>
            <div id="modal_property_mortgage" class="modal_content_block"></div>
            
        </div>
            
    </div>
    
</div>

<?php
include( locate_template ('/templates/image_gallery_modal.php') ); 
?>