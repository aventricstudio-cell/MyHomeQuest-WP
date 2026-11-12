<?php
// MILLDONE
// Retrieve and apply custom classes for the top bar
// This function considers various theme options and page-specific settings
$topbar_class = wpestate_topbar_classes();

// Allow modification of the top bar classes via a filter
$topbar_class = apply_filters('wpresidence_topbar_classes', $topbar_class);
?>

<div class="top_bar_wrapper d-none d-xl-block w-100 <?php echo esc_attr($topbar_class); ?>">

    <div class="top_bar d-flex w-100 px-5 justify-content-between align-items-center">      
        <?php 
        // Trigger action before the top bar content
        do_action('wpresidence_before_top_bar_content');

        // Check if the current page is not the user dashboard
        // Different widget areas are displayed for dashboard and non-dashboard pages
        if (!wpestate_is_user_dashboard()) { 
        ?>
        
            <?php
            // Non-dashboard pages: Left widget area
            // Typically used for contact information, social media links, etc.
            if (is_active_sidebar('top-bar-left-widget-area')) { ?>
                <div class="left-top-widet">
                    <ul class="xoxo">
                        <?php 
                        // Trigger action before the left top widget area
                        do_action('wpresidence_before_top_bar_left_widget');
                        
                        // WordPress function to display widgets in this area
                        dynamic_sidebar('top-bar-left-widget-area'); 

                        // Trigger action after the left top widget area
                        do_action('wpresidence_after_top_bar_left_widget');
                        ?>
                    </ul>    
                </div> 
            <?php } ?>

            <?php
            // Non-dashboard pages: Right widget area
            // Often used for user login, language switchers, or additional navigation
            if (is_active_sidebar('top-bar-right-widget-area')) { ?>
                <div class="right-top-widet">
                    <ul class="xoxo">
                        <?php 
                        // Trigger action before the right top widget area
                        do_action('wpresidence_before_top_bar_right_widget');
                        
                        // WordPress function to display widgets in this area
                        dynamic_sidebar('top-bar-right-widget-area'); 

                        // Trigger action after the right top widget area
                        do_action('wpresidence_after_top_bar_right_widget');
                        ?>
                    </ul>
                </div> 
            <?php } ?>
        
        <?php } else { 
        // User dashboard-specific top bar content
        ?>
        
            <?php
            // Dashboard pages: Left widget area
            // Could contain dashboard-specific quick links or user information
            if (is_active_sidebar('dashboard-top-bar-left-widget-area')) { ?>
                <div class="left-top-widet">
                    <ul class="xoxo"> 
                        <?php 
                        // Trigger action before the left dashboard top widget area
                        do_action('wpresidence_before_dashboard_top_bar_left_widget');
                        
                        // WordPress function to display widgets in this area
                        dynamic_sidebar('dashboard-top-bar-left-widget-area'); 

                        // Trigger action after the left dashboard top widget area
                        do_action('wpresidence_after_dashboard_top_bar_left_widget');
                        ?>
                    </ul>    
                </div>  
            <?php } ?>
        
            <?php
            // Dashboard pages: Right widget area
            // Might include user-specific actions or notifications
            if (is_active_sidebar('dashboard-top-bar-right-widget-area')) { ?>
                <div class="right-top-widet">
                    <ul class="xoxo">
                        <?php 
                        // Trigger action before the right dashboard top widget area
                        do_action('wpresidence_before_dashboard_top_bar_right_widget');
                        
                        // WordPress function to display widgets in this area
                        dynamic_sidebar('dashboard-top-bar-right-widget-area'); 

                        // Trigger action after the right dashboard top widget area
                        do_action('wpresidence_after_dashboard_top_bar_right_widget');
                        ?>
                    </ul>
                </div> 
             <?php } ?>
        
        <?php } 

        // Trigger action after the top bar content
        do_action('wpresidence_after_top_bar_content');
        ?>
    </div>    
</div>

<?php
/**
 * - The top bar's visibility and styling can be controlled through the theme's options panel
 * - Widget areas are registered in the theme's functions.php or a dedicated widget file
 * - The 'wpestate_topbar_classes' function likely considers options like transparency, borders, etc.
 * - For mobile responsiveness, consider how this top bar information is displayed on smaller screens
 */
?>
