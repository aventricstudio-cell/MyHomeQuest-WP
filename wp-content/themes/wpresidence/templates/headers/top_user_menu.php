<?php
//MILLDONE
/**
 * Top User Menu Template
 *
 * This template handles the display of the top user menu in the WPResidence theme.
 * It includes user profile picture, login/logout functionality, and various user actions.
 */

// Retrieve current user information
$current_user = wp_get_current_user();

// Set default user picture and then override if a custom one exists
$user_small_picture = get_theme_file_uri('/img/default_user_small.png');

if (is_user_logged_in()) {
    $user_custom_picture_id = get_the_author_meta('small_custom_picture', $current_user->ID);
    if ($user_custom_picture_id) {
        $user_custom_picture = wp_get_attachment_image_src($user_custom_picture_id, 'user_thumb');
        if ($user_custom_picture) {
            $user_small_picture = $user_custom_picture[0];
        }
    }
}

// Initialize global payments object for WooCommerce integration
global $wpestate_global_payments;

// Retrieve top bar user login display option from theme settings
$show_top_bar_user_login = wpresidence_get_option('wp_estate_show_top_bar_user_login', '');

// Allow modification of user picture and show top bar login option via filters
$user_small_picture = apply_filters('wpresidence_user_small_picture', $user_small_picture, $current_user);

// Allow modification of the top bar user login display option via a filter
$show_top_bar_user_login = apply_filters('wpresidence_show_top_bar_user_login', $show_top_bar_user_login);

?>

<!-- User menu container with dynamic classes based on login status -->
<div class="user_menu d-flex align-items-center <?php echo is_user_logged_in() ? 'user_loged' : 'user_not_loged'; ?> wpestate-align-self-center wpestate-text-end" id="user_menu_u">
    <?php 
    // Display header phone number (if configured in theme settings)
    echo wpestate_header_phone();

    // Custom action before displaying the user menu elements
    do_action('wpresidence_before_user_menu', $show_top_bar_user_login, $current_user);
    
    // Display user menu elements if enabled in theme settings
    if ($show_top_bar_user_login === 'yes') {
        // Display WooCommerce cart icon if WooCommerce is active
        if (class_exists('WooCommerce')) {
            $wpestate_global_payments->show_cart_icon();
        }

        if (is_user_logged_in()) {
            // Display for logged-in users
            ?>
            <!-- User profile picture -->
            <div class="menu_user_picture" style="background-image: url('<?php echo esc_url($user_small_picture); ?>');"></div>
            <!-- Menu toggle button -->
            <a class="navicon-button x"><div class="navicon"></div></a>
            <?php
        } else {
            // Display for non-logged-in users
            ?>
            <!-- User icon for non-logged-in users -->
            <div class="submit_action">
                <?php
                // Load and display the user SVG icon
                $svg_path =  '/img/icons/user-icon.svg';
                include(locate_template('/img/icons/user-icon.svg'));  
                ?>
            </div>
            <!-- Menu toggle for non-logged-in users -->
          
            <?php
            // Display 'Add Listing' button if enabled in theme settings
            if (wpresidence_get_option('wp_estate_show_submit', '') === 'yes') {
                echo '<a href="' . esc_url(wpestate_get_template_link('page-templates/front_property_submit.php')) . '" class="submit_listing">' . esc_html__('Add Listing', 'wpresidence') . '</a>';
            }
        }
    }

    // Custom action after displaying the user menu elements
    do_action('wpresidence_after_user_menu', $show_top_bar_user_login, $current_user);
    ?>
</div>

<?php 
// Display extended user menu for logged-in users
if (is_user_logged_in()) {
    // Custom action before displaying the user dropdown menu for logged-in users
    do_action('wpresidence_before_user_dropdown_menu', $current_user);
    ?>
    <ul id="user_menu_open" class="dropdown-menu menulist topmenux" role="menu" aria-labelledby="user_menu_trigger"> 
        <?php 
        // Generate user menu items (profile, my properties, add new property, etc.)
        wpestate_generate_user_menu('top'); 
        ?>
    </ul>
    <?php
   // Custom action after displaying the user dropdown menu for logged-in users
    do_action('wpresidence_after_user_dropdown_menu', $current_user);
}

// Display WooCommerce cart if WooCommerce is active
if (class_exists('WooCommerce')) {
    $wpestate_global_payments->show_cart();
}
?>
