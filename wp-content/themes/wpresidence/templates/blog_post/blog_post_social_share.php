<?php
/** MILLDONE
 * Social Sharing Buttons Template
 * src templates\blog_post\blog_post_social_share.php
 * This template generates social sharing buttons for blog posts.
 * It includes sharing options for Facebook, Twitter, Pinterest, WhatsApp, and Email.
 *
 * @package WPEstate
 * @subpackage Templates
 * @since 1.0
 * @version 2.0
 */

// Ensure this file is only used as part of a WordPress theme
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Get the current post URL and title
$share_url = get_permalink();
$share_title =   wpresidence_get_sanitized_truncated_title($post->ID,0);

// Prepare email sharing link
$email_link = wp_sprintf('subject=%s&body=%s', urlencode(get_the_title()), urlencode(get_permalink()));

 // Get WhatsApp sharing link
 $whatsapp_link = wpestate_return_agent_whatsapp_call($post->ID, '');

?>
<div class="prop_social_single">
    <!-- Facebook Share Button -->
    <a href="<?php echo esc_url('https://www.facebook.com/sharer.php?u=' . $share_url . '&t=' . urlencode($share_title)); ?>" 
       target="_blank" 
       class="share_facebook" 
       rel="nofollow noopener noreferrer" 
       title="<?php esc_attr_e('Share on Facebook', 'wpresidence'); ?>">
        <i class="fab fa-facebook-f"></i>
    </a>

    <!-- Twitter Share Button -->
    <a href="<?php echo esc_url('https://twitter.com/intent/tweet?text=' . urlencode($share_title . ' ' . $share_url)); ?>" 
       class="share_tweet" 
       target="_blank" 
       rel="nofollow noopener noreferrer" 
       title="<?php esc_attr_e('Share on Twitter', 'wpresidence'); ?>">
        <i class="fab fa-x-twitter"></i>
    </a>

    <!-- Pinterest Share Button (only if featured image exists) -->
    <?php if (!empty($pinterest[0])) : ?>
        <a href="<?php echo esc_url('https://pinterest.com/pin/create/button/?url=' . $share_url . '&media=' . $pinterest[0] . '&description=' . urlencode($share_title)); ?>" 
           target="_blank" 
           class="share_pinterest" 
           rel="nofollow noopener noreferrer" 
           title="<?php esc_attr_e('Share on Pinterest', 'wpresidence'); ?>">
            <i class="fab fa-pinterest-p"></i>
        </a>
    <?php endif; ?>

    <!-- WhatsApp Share Button -->
    <a href="<?php echo esc_url($whatsapp_link); ?>" 
       class="share_whatsapp" 
       rel="nofollow noopener noreferrer" 
       title="<?php esc_attr_e('Share on WhatsApp', 'wpresidence'); ?>">
        <i class="fab fa-whatsapp" aria-hidden="true"></i>
    </a>

    <!-- Email Share Button -->
    <a href="mailto:?<?php echo esc_attr($email_link); ?>" 
       class="social_email" 
       title="<?php esc_attr_e('Share by Email', 'wpresidence'); ?>">
        <i class="far fa-envelope"></i>
    </a>
</div>