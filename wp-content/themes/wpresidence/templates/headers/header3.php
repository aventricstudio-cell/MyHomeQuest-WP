<?php
// MILLDONE
// Header type3 template

// This section determines the alignment classes for different elements of the header.
// It's based on theme options and affects the layout of the header components.
$user_menu_align    =   '';
$logo_align         =   'justify-content-center';
$menu_align         =   '';
if($logo_align_option=='right'){ 
    $header_classes['header_wrapper_inside_class'].=' wpestate-flex-row-reverse';
}
 
?>

<div class="header_wrapper_inside d-flex align-items-center justify-content-between  
    <?php echo esc_attr($header_classes['header_wrapper_inside_class']);?>"
                 data-logo="<?php print esc_attr($header_classes['logo']);?>"
                 data-sticky-logo="<?php print esc_attr($header_classes['stikcy_logo_image']); ?>">

            <?php
            // This section displays the logo.
            // It uses a custom function to generate the logo HTML with specific CSS classes.
            $classes="wpestate-flex align-items-center  wpestate-align-self-center ".esc_attr($logo_align);
            print wpestate_display_logo($header_classes['logo'],$classes);           
            ?>

            <!-- 
            This is the trigger for the mobile menu.
            It's styled as a hamburger icon and is used to toggle the mobile navigation.
            -->
            <a class="navicon-button header_type3_navicon " id="header_type3_trigger">
                <div class="navicon"></div>
            </a>                   
</div>