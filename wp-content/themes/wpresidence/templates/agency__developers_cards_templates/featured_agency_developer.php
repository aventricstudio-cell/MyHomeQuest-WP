<?php
/**
 * Template for Featured Agency /Developer Card
 * This template displays a featured agency/developer role card with their details.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0.0
 *
 * @var array $args Template arguments passed from the shortcode function
 */

// Ensure $args is set to prevent errors
$args = isset($args) ? $args : array();

// Extract variables from $args, providing default values
$status = isset($args['status']) ? $args['status'] : '';
$thumbnail_url = isset($args['thumbnail_url']) ? $args['thumbnail_url'] : '';
$permalink = isset($args['permalink']) ? $args['permalink'] : '';
$title = isset($args['title']) ? $args['title'] : '';
$phone = isset($args['phone']) ? $args['phone'] : '';
$email = isset($args['email']) ? $args['email'] : '';
$content = isset($args['content']) ? $args['content'] : '';
$user_shortcode_imagelink = isset($args['user_shortcode_imagelink']) ? $args['user_shortcode_imagelink'] : '';
?>

<div class="user_role_unit  d-flex flex-column flex-sm-row">
    <div class="featured_user_role_unit_details col-12 col-sm-7  order-2 order-sm-0">
        <div class="user_role_status"><?php echo esc_html($status); ?></div>
        <div class="user_role_image" style="background-image:url(<?php echo esc_url($thumbnail_url); ?>)"></div>
        <h4><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a></h4>
        <div class="user_role_phone">
            <i class="fas fa-phone"></i> 
            <a href="tel:<?php echo esc_attr(urlencode($phone)); ?>"><?php echo esc_html($phone); ?></a>
        </div>
        <div class="user_role_email">
            <i class="far fa-envelope"></i>
            <?php
        
            $email_link    = antispambot(esc_attr($email));
            ?>
            <a href="mailto:<?php echo $email_link; ?>"><?php echo $email_link; ?></a>
        </div>
        <div class="user_role_content"><?php echo wp_kses_post($content); ?></div>
        <a class="wpresidence_button button_user_role" href="<?php echo esc_url($permalink); ?>">
            <?php esc_html_e('View Profile', 'wpresidence-core'); ?>
        </a>
    </div>
    <div class="user_role_featured_image col-12 col-sm-5  order-1 order-sm-0">
        <div class="user_role" style="background-image:url(<?php echo esc_url($user_shortcode_imagelink); ?>)"></div>
        <div class="prop_new_details"><div class="prop_new_details_back"></div></div>
    </div>
</div>