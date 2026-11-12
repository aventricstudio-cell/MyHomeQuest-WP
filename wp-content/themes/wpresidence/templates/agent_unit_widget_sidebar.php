<?php



$col_class=4;
if(isset($wpestate_options['content_class']) && $wpestate_options['content_class']=='col-md-12'){
    $col_class=3;
}

?>
<div class="agent_unit_widget_sidebar_wrapper">
  
    <div class="agent_unit_widget_sidebar_wrapper_unit">
        <div class="agent_unit_widget_sidebar" style="background-image: url(<?php echo esc_url($realtor_details['realtor_image']);?>)">
        </div>
        
        <div class="agent_unit_widget_sidebar_details_wrapper">
            <h4> <a href="<?php echo esc_url($realtor_details['link']); ?>"><?php echo esc_html($realtor_details['realtor_name']); ?></a></h4>
            <div class="agent_position"><?php echo esc_html($realtor_details['realtor_position']); ?></div>
        </div>
    </div>


    <?php   
    include( locate_template ('/templates/listing_templates/other_agents_sidebar.php') );
    ?>

</div>
