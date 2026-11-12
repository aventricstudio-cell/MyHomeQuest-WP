<?php
// Global variable for payments
global $wpestate_global_payments;

// Get the current user object
$current_user = wp_get_current_user();

// Get user custom picture meta
$user_custom_picture = get_the_author_meta('small_custom_picture', $current_user->ID);
$user_small_picture_id = get_the_author_meta('small_custom_picture', $current_user->ID);

// Check if user has a custom picture, otherwise use default
if ($user_small_picture_id == '') {
    $user_small_picture[0] = get_theme_file_uri('/img/default_user_small.png');
} else {
    $user_small_picture = wp_get_attachment_image_src($user_small_picture_id, 'user_thumb');
}

// Trigger custom action before the mobile menu wrapper
do_action('wpresidence_before_mobilewrapper');
?>

<!-- Mobile wrapper container -->
<div class="mobilewrapper" id="mobilewrapper_links">
    <div class="snap-drawers">
        <!-- Left Sidebar for mobile menu -->
        <div class="snap-drawer snap-drawer-left">
            <div class="mobilemenu-close"><i class="fas fa-times"></i></div>
            <?php
           

            // Output the mobile header phone
            echo wpestate_header_phone();

            // Check if the "Add Listing" option is enabled and display the link
            if (esc_html(wpresidence_get_option('wp_estate_show_submit', '')) === 'yes') { ?>
                <a href="<?php print wpestate_get_template_link('page-templates/front_property_submit.php') ?>" class="submit_listing"><?php esc_html_e('Add Listing', 'wpresidence'); ?></a>
            <?php }

            // Print the mobile menu
            if(function_exists('wpestate_get_cached_mobile_menu')){
                echo wpestate_get_cached_mobile_menu();
            }
            ?>
        </div>
    </div>
</div>

<?php
// Trigger custom action after the mobile menu wrapper
do_action('wpresidence_after_mobilewrapper');

// Trigger custom action before the user mobile menu wrapper
do_action('wpresidence_before_mobilewrapper_user');
?>

<!-- User mobile wrapper container -->
<div class="mobilewrapper-user" id="mobilewrapperuser">
    <div class="snap-drawers">
        <!-- Right Sidebar for user mobile menu -->
        <div class="snap-drawer snap-drawer-right">
            <div class="mobilemenu-close-user"><i class="fas fa-times"></i></div>

            <?php
            // Check if user is logged in
            if (0 != $current_user->ID && is_user_logged_in()) { ?>

                <!-- User menu for logged in users -->
                <ul class="mobile_user_menu mobilex-menu" role="menu" aria-labelledby="user_menu_trigger">
                    <?php
                    // Display WooCommerce cart icon if WooCommerce is active
                    if (class_exists('WooCommerce')) {
                        $wpestate_global_payments->show_cart_icon_mobile();
                    }
                    // Generate user menu
                    wpestate_generate_user_menu();
                    ?>
                </ul>

                <?php
            } else {
                $wpestate_custom_auth = WpEstate_Custom_Auth::get_instance();
                echo $wpestate_custom_auth->display_auth_form('modal', 'all','mobile');
               
            } ?>
        </div>
    </div>
</div>

<?php
// Trigger custom action after the user mobile menu wrapper
do_action('wpresidence_after_mobilewrapper_user');
?>
