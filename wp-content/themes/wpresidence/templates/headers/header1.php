<?php
// MILLDONE
// Header type 1 template

// This is the main wrapper for the header type 1 template.
// It uses flexbox for layout and includes data attributes for logo and sticky logo.
// The wrapper class is dynamically set based on $header_classes.
?>
<div class="header_wrapper_inside mx-5 py-0 d-flex w-100 flex-wrap align-items-center justify-content-between <?php echo esc_attr($header_classes['header_wrapper_inside_class']);?>"
        data-logo="<?php print esc_attr($header_classes['logo']);?>"
        data-sticky-logo="<?php print esc_attr($header_classes['stikcy_logo_image']); ?>">
        <?php
        // Display the logo using the wpestate_display_logo function.
        // The logo image and any additional classes are passed as parameters.
        $classes="";
        print wpestate_display_logo($header_classes['logo'],$classes);          
        ?>
       <?php
        // Display the primary navigation menu.
        // Custom classes for padding are passed as parameters.
        wpresidence_display_primary_nav_menu('px-5 py-0');
        ?>
       
        <?php
        // This div contains the user menu wrapper.
        ?>
        <div class="user_menu_wrapper">          
            <?php
                // Include the top user menu template part.
                get_template_part('templates/headers/top_user_menu');
            ?>
        </div>
</div>