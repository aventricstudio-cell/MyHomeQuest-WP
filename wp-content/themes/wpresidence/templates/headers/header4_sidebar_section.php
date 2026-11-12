<?php
// MILLDONE
// Header type 4 template
// This is the main wrapper for the header type 4 template.
// It uses flexbox for layout and includes data attributes for logo and sticky logo.
?>
<div class="header_wrapper_inside header_wrapper_inside_type_4  d-none d-xl-flex exclude-rtl p-3"
     data-logo="<?php print esc_attr($header_classes['logo']);?>"
     data-sticky-logo="<?php print esc_attr($header_classes['stikcy_logo_image']); ?>">
    <?php
    // This section displays the logo
    // It uses a function called wpestate_display_logo to render the logo
    // The $classes variable is empty in this case, but could be used to add additional CSS classes
    $classes = " exclude-rtl ";
    print wpestate_display_logo($header_classes['logo'], $classes);          
    ?>
    <?php
    // This function call displays the primary navigation menu
    // The 'py-0' parameter sets specific padding classes for the menu (padding-top and padding-bottom set to 0)
    wpresidence_display_primary_nav_menu('py-0 exclude-rtl');
    ?>
   
    <!-- This div represents the footer section of the header type 4 -->
    <div id="header4_footer">
        <!-- This unordered list will contain the widgets for the header 4 footer area -->
        <ul class="xoxo">
            <?php
            // This function call displays the widgets that have been added to the 'header4-widget-area'
            // The widgets are rendered inside the <ul> element
            dynamic_sidebar('header4-widget-area');
            ?>
        </ul>
    </div>
</div>