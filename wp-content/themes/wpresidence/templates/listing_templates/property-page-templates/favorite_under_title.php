<?php
/** MILLDONE
 * WpResidence Property Favorite Button
 * src: templates\listing_templates\property-page-templates\favorite_under_title.php
 * This template file is responsible for rendering the favorite button
 * under the title of a property page in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyTemplates
 * @version 1.0
 * 
 * @uses wp_get_current_user()
 * @uses get_option()
 * 
 * Dependencies:
 * - WordPress core functions
 * 
 * Expected global variables:
 * - $post
 */


 //check if the template is loaded from property page or from custom property page template  
if(isset($property_id)){
    $selectedPropertyID=$property_id;
}else{
    $selectedPropertyID=$post->ID;
}



// Get current user and their favorites
$current_user = wp_get_current_user();
$user_option = 'favorites' . intval($current_user->ID);
$current_favorites = get_option($user_option);

// Set default values
$favorite_status = array(
    'class' => 'isnotfavorite',
    'message' => esc_html__('add to favorites', 'wpresidence'),
    'icon' => 'far fa-heart'
);

// Check if the current property is in favorites
if ($current_favorites && in_array($selectedPropertyID, $current_favorites)) {
    $favorite_status = array(
        'class' => 'isfavorite',
        'message' => esc_html__('remove from favorites', 'wpresidence'),
        'icon' => 'fas fa-heart'
    );
}
?>

<div id="add_favorites" 
     class="title_share single_property_action <?php echo esc_attr($favorite_status['class']); ?>" 
     data-postid="<?php echo esc_attr($selectedPropertyID); ?>" 
     data-bs-toggle="tooltip"
     title="<?php echo esc_attr($favorite_status['message']); ?>">
    <i class="<?php echo esc_attr($favorite_status['icon']); ?>"></i>
    <?php esc_html_e('Favorite', 'wpresidence'); ?>
</div>