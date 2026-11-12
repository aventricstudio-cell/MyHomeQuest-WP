<?php
/** MILLDONE
 * Template for displaying the favorite button on property cards
 * src: templates\property_cards_templates\property_card_details_templates\property_card_favorite.php
 * This template is responsible for rendering the favorite button on property cards,
 * including the logic for determining if a property is already favorited by the current user.
 */ 

// Get the current user's ID
$current_user = wp_get_current_user();
$userID = $current_user->ID;

// Construct the user-specific option name for favorites
$user_option = 'favorites' . intval($userID);

// Retrieve the user's current favorites
$current_favorites = get_option($user_option, array());

// Set default values for favorite class and message
$favorite_class = 'icon-fav-off';
$fav_message = esc_html__('add to favorites', 'wpresidence');

// Check if the current property is in the user's favorites
if (is_array($current_favorites) && in_array($postID, $current_favorites)) {
    $favorite_class = 'icon-fav-on';
    $fav_message = esc_html__('remove from favorites', 'wpresidence');
}

// Load the heart SVG icon
ob_start();
include(locate_template('css/css-images/icons/heart.svg'));
$heart_icon = ob_get_clean();

// Output the favorite button
printf(
    '<span class="icon-fav %1$s" data-bs-placement="left" data-bs-toggle="tooltip" title="%2$s" data-postid="%3$d">%4$s</span>',
    esc_attr($favorite_class),
    esc_attr($fav_message),
    intval($postID),
    trim($heart_icon)
);

// Display remove from favorites text if necessary
if (isset($show_remove_fav) && $show_remove_fav == 1) {
    printf(
        '<span class="icon-fav icon-fav-on-remove" data-postid="%1$d">%2$s</span>',
        esc_attr($postID),
        esc_html($fav_message)
    );
}