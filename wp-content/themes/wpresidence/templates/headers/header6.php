<?php
// MILLDONE
// Header type 6 template
// This template represents a specific header layout with primary menu, logo, secondary menu, and user menu.
?>
<div class="header_wrapper_inside header_wrapper_inside mx-5  py-0 d-flex w-100 flex-wrap align-items-center justify-content-between "
    data-logo="<?php print esc_attr($header_classes['logo']);?>"
    data-sticky-logo="<?php print esc_attr($header_classes['stikcy_logo_image']); ?>">

    <?php
        // Display the primary navigation menu
        // The function is called with specific classes for styling and positioning
        // 'wpresidence_header_6_firs_menu' is likely a custom class for this header type
        wpresidence_display_primary_nav_menu('px-0 py-0 wpresidence_header_6_firs_menu ');
    ?>

    <?php
        // Display the site logo
        // The function wpestate_display_logo is used to render the logo
        // It uses the logo URL stored in $header_classes['logo']
        print wpestate_display_logo($header_classes['logo']);
    ?>
     
    <div class="wpresidence_header_6_secondary_menu d-flex px-0 py-0 justify-content-end">      
        <?php
        // Display the secondary navigation menu
        // This is specific to header type 6, providing an additional menu area
        wpresidence_display_secondary_nav_menu('px-0 py-0');
        ?>

        <?php
        // Include the user menu template
        // This typically contains login/logout links and user-specific options
        get_template_part('templates/headers/top_user_menu');
        ?>
    </div>
       
</div>