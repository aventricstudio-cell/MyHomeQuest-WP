<!-- begin sidebar -->
<?php
/* MILLDONE
* Sidebar Template  
* src: sidebar.php
* This template handles the display of the sidebar in the WpResidence theme.
*/


// Retrieve the wpestate_options from the query vars
$wpestate_options = get_query_var('wpestate_options');

// Extract sidebar name and class from wpestate_options
$sidebar_name   =   $wpestate_options['sidebar_name'];
$sidebar_class  =   $wpestate_options['sidebar_class'];

// Check if a sidebar should be displayed
// This condition ensures that the sidebar is not 'no sidebar', not empty, and not 'none'
if( ('no sidebar' != $wpestate_options['sidebar_class']) &&
    ('' != $wpestate_options['sidebar_class'] ) &&
    ('none' != $wpestate_options['sidebar_class']) ){
    
    // Check if we're on a single property page
    if( is_singular('estate_property') ){
        // If we are, check if the property sidebar should be sticky
        if( "yes" ==  wpresidence_get_option('wp_estate_property_sidebar_sitcky' ) ){
            // Add sticky class to sidebar
            $wpestate_options['sidebar_class'] = $wpestate_options['sidebar_class'].' wpestate_sidebar_sticky  ';
        }
    }else{
        // If we're not on a single property page, check if global sticky sidebar is enabled
        if( "yes" ==  wpresidence_get_option('wp_estate_global_sticky_sidebar' ) ){
            // Add sticky class to sidebar
            $wpestate_options['sidebar_class'] = $wpestate_options['sidebar_class'].' wpestate_sidebar_sticky ';
        }
    }
?>    
<!-- sidebar html markup -->
<div class="p-0 added4mobile col-xs-12 <?php print esc_html($wpestate_options['sidebar_class']);?> widget-area-sidebar" id="primary" >
    <div id="primary_sidebar_wrapper">
        <?php
        // Check if we're on a single property page and not on a taxonomy page
        if(  'estate_property' == get_post_type() && !is_tax() ){
            // Get the sidebar agent option for this property
            $sidebar_agent_option_value = get_post_meta($post->ID, 'sidebar_agent_option', true);  
            if($sidebar_agent_option_value =='global'){
                // If the option is 'global', check the global setting
                $enable_global_property_page_agent_sidebar = esc_html ( wpresidence_get_option('wp_estate_global_property_page_agent_sidebar','') );
                if($enable_global_property_page_agent_sidebar=='yes'){
                    // Include the property list agent template
                    include( locate_template ('/templates/property_list_agent.php') );
                }
            }elseif ($sidebar_agent_option_value =='yes') {
                // If the option is 'yes', include the property list agent template
                include( locate_template ('/templates/property_list_agent.php') );
            }
        }
        ?>
        <?php    
        // Check if the sidebar is active and has widgets
        if ( isset($wpestate_options['sidebar_name']) && is_active_sidebar( $wpestate_options['sidebar_name'] ) ) { ?>
            <ul class="xoxo">
                <?php 
                // Display the sidebar widgets
                dynamic_sidebar( $wpestate_options['sidebar_name'] ); 
                ?>
            </ul>
        <?php
        }
        ?>
    </div>
</div>  
<?php
} // end first/main if
?>
<!-- end sidebar -->