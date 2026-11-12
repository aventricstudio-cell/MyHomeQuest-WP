<div id="header_type3_wrapper" class="header_type3_menu_sidebar">
    
   <?php
   // Display widgets before the menu in the sidebar
   if (is_active_sidebar('sidebar-menu-widget-area-before')) { ?>
        <ul class="xoxo">
            <?php dynamic_sidebar('sidebar-menu-widget-area-before'); ?>
        </ul>
    <?php } ?>
    
    <nav class="wpresidence-navigation-menu">
        <?php 
        // Display the primary navigation menu
        wp_nav_menu( 
            array(
                'theme_location' => 'primary',
                'walker'         => new wpestate_custom_walker
            ) 
        ); 
        ?>
    </nav><!-- end .wpresidence-navigation-menu -->
    
    
    <?php
    // Display widgets after the menu in the sidebar
    if (is_active_sidebar('sidebar-menu-widget-area-after')) { ?>
        <ul class="xoxo">
            <?php dynamic_sidebar('sidebar-menu-widget-area-after'); ?>
        </ul>
    <?php } ?>
</div>