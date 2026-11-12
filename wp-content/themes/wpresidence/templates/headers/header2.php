<?php
// MILLDONE
// Header type 2 template

// This is the main wrapper for the header type 2 template.
// It uses flexbox for layout and includes data attributes for logo and sticky logo.
?>
<div class="header_wrapper_inside d-flex mx-5 py-0 w-100 flex-wrap align-items-center" data-logo="<?php print esc_attr($header_classes['logo']);?>" data-sticky-logo="<?php print esc_attr($header_classes['stikcy_logo_image']); ?>">
    
    <?php
    // This div contains the logo display.
    // The logo is displayed using the wpestate_display_logo function.
    ?>
    <div class="w-100 my-0 pt-2 wpresidence-logo-container">
        <?php print wpestate_display_logo($header_classes['logo'],''); ?>
    </div>
    
    <?php
    // This div contains the menu and user menu wrapper.
    // It uses flexbox to justify content between the primary nav menu and user menu.
    ?>
    <div class="d-flex my-0 w-100 justify-content-between align-items-center wpresidence-header2-menu-wrapper ">
        <?php
        // This function call displays the primary navigation menu.
        // The 'px-0 py-0' classes are passed as arguments for additional styling.
        wpresidence_display_primary_nav_menu('px-0 py-0 ');
        ?>
        
        <?php
        // This div contains the user menu wrapper.
        // It includes the top user menu template part.
        ?>
        <div class="user_menu_wrapper">
            <?php get_template_part('templates/headers/top_user_menu'); ?>
        </div>
    </div>
</div>