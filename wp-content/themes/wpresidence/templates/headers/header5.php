<?php
// MILLDONE
// Header type 5 template

// This section retrieves all necessary options for the header
// It uses wpresidence_get_option to get values from the theme settings
$options = [
    'show_top_bar_user_login' => esc_html(wpresidence_get_option('wp_estate_show_top_bar_user_login', '')),
    'logo' => wpresidence_get_option('wp_estate_logo_image', 'url'),
    'sticky_logo' => esc_html(wpresidence_get_option('wp_estate_stikcy_logo_image', 'url')),
];

// This loop prepares data for the three info widgets in the header
// Each widget has an icon and two text fields
$widgets = [];
for ($i = 1; $i <= 3; $i++) {
    $widgets[] = [
        'icon' => wpresidence_get_option("wp_estate_header5_info_widget{$i}_icon", ''),
        'text1' => wpresidence_get_option("wp_estate_header5_info_widget{$i}_text1", ''),
        'text2' => wpresidence_get_option("wp_estate_header5_info_widget{$i}_text2", ''),
    ];
}

// Start of the HTML structure for the header
// This div represents the top row of the header
?>
<div class="header5_top_row mx-0 px-5 py-0 d-flex w-100 flex-wrap align-items-center justify-content-between" 
    data-logo="<?php echo esc_attr($options['logo']); ?>" 
    data-sticky-logo="<?php echo esc_attr($options['sticky_logo']); ?>">
    
    <?php
    // This section displays the logo
    // It uses a function called wpestate_display_logo to render the logo
    $classes = "wpestate-flex flex-wrap ";
    echo wpestate_display_logo($header_classes['logo'], $classes);           
    ?>

    <!-- This div contains the info widgets -->
    <div class="header_5_widget_wrap d-flex flex-wrap align-items-center justify-content-between">

        <?php 
        // Trigger action before displaying the header widgets
        // This action allows developers to add custom content or manipulate data before the widgets are displayed
        do_action('wpresidence_before_header5_widgets', $widgets);

        // This loop iterates through the widgets and displays them
        // Each widget is only displayed if it has either an icon or text content
        foreach ($widgets as $widget): ?>

            <?php 
            // Allow modification of individual widget data via filter
            $widget = apply_filters('wpresidence_header5_widget_data', $widget);
            
            if (!empty($widget['icon']) || !empty($widget['text1'])): ?>
                <div class="header_5_widget gap-2 d-flex align-items-center">
                    <div class="header_5_widget_icon">
                        <i class="<?php echo esc_attr($widget['icon']); ?>"></i>
                    </div>
                    <div class="header_5_widget_text_wrapper">
                        <div class="header_5_widget_text"><?php echo esc_html(trim($widget['text1'])); ?></div>
                        <div class="header_5_widget_text"><?php echo esc_html(trim($widget['text2'])); ?></div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php 
        // Trigger action after displaying the header widgets
        // This action allows developers to add custom content or manipulate data after the widgets are displayed
        do_action('wpresidence_after_header5_widgets', $widgets); 
        ?>

    </div>
 
</div>    

<?php
// This div represents the bottom row of the header
?>
<div class="header5_bottom_row  d-flex w-100 ">
    <div class="header5_bottom_row_internal mx-0 px-5 py-0 d-flex w-100 flex-wrap align-items-center justify-content-between">
        <?php 
        // This function call displays the primary navigation menu
        // The 'px-0 py-0' parameter sets specific padding classes for the menu
        wpresidence_display_primary_nav_menu('px-0 py-0');
        ?>
        
        <!-- This div contains the user menu -->
        <div class="user_menu_wrapper text-end">          
            <?php
            // This includes a separate template part for the top user menu
            get_template_part('templates/headers/top_user_menu');
            ?>
        </div>
    </div>
</div>