<?php
/**
 * MILLDONE
 * Blog Unit Template Part
 * src templates\blog_card_templates\blog_unit.php
 * This template is responsible for displaying individual blog post entries - version 1
 * 
 *
 * @package WPResidence
 * @subpackage Templates
 * @since 1.0
 */

// Retrieve essential post information
$postID     = get_the_ID(); // Get the current post ID
$thumb_id   = get_post_thumbnail_id($postID); // Get the featured image ID
$preview    = wp_get_attachment_image_src(get_post_thumbnail_id(), 'agent_picture_thumb'); // Get the featured image URL for the agent thumbnail size
$link       = esc_url(get_permalink()); // Get the permalink and ensure it's properly escaped

// Start the blog unit wrapper
?>
<div class="listing_wrapper">
    <div class="blog_unit property_listing_blog blog_v1 col-md-12" data-link="<?php echo esc_attr($link); ?>"> 
        <?php 
        // Check if we're using a full-width layout
        if (isset($wpestate_options['content_class'] ) && $wpestate_options['content_class'] == 'col-md-12') {
            // If full-width, use a larger image size
            $preview = wp_get_attachment_image_src(get_post_thumbnail_id(), 'blog_unit');
        }

        // Prepare the image attributes for lazy loading
        $extra = array(
            'class' => 'lazyload img-responsive',    
        );
        if (isset($preview[0])) {
            $extra['data-original'] = $preview[0];
        } else {
            // Use a default image if no featured image is set
            $extra['data-original'] = get_theme_file_uri('/img/defaults/default_blog_unit.jpg');
        }

        $unit_class = "";
        // Get the post thumbnail with the extra attributes
        $thumb_prop = get_the_post_thumbnail($postID, 'blog_unit', $extra);
        
        // If no thumbnail is found, use the default image
        if ($thumb_prop == '') {
            $thumb_prop_default = get_theme_file_uri('/img/defaults/default_blog_unit.jpg');
            $thumb_prop = '<img src="' . esc_url($thumb_prop_default) . '" class="b-lazy img-responsive wp-post-image lazy-hidden" alt="' . esc_attr__('default image', 'wpresidence') . '" />';   
        }
        
        // Display the featured image if available
        if ($thumb_prop != '') {
            $unit_class = "has_thumb";
            ?>
            <div class="blog_unit_image">
                <?php echo '<a href="' . esc_url($link) . '">' . $thumb_prop . '</a>'; ?>
            </div>      
        <?php } ?>        
        
        <div class="blog_unit_content <?php echo esc_attr($unit_class); ?>">
            <h3>
                <a href="<?php the_permalink(); ?>">
                    <?php 
                        echo wpresidence_get_sanitized_truncated_title($postID,54)
                    ?>
                </a>
            </h3>
            
            <?php 
            // Display the post excerpt
            the_excerpt(); 
            ?>
            
            <div class="blog_unit_meta widemeta">
                <!-- Display post categories -->
                <span class="span_widemeta"><i class="far fa-copy"></i><?php the_category(', '); ?></span>
                <!-- Display post date -->
                <span class="span_widemeta"><i class="far fa-calendar-alt"></i><?php echo get_the_date(); ?></span>
                <!-- Display comment count -->
                <span class="span_widemeta"><i class="far fa-comment"></i><?php comments_number('0', '1', '%'); ?></span>
                <!-- "Continue Reading" link -->
                <a class="read_more" href="<?php the_permalink(); ?>"><?php esc_html_e('Continue Reading', 'wpresidence'); ?><i class="fas fa-angle-right"></i></a>
            </div>
        </div>
    </div>
</div>