<?php

/**
 * WPResidence Post Breadcrumbs Function
 * This function generates the HTML for post breadcrumbs.
 *
 * @package WPResidence Core
 * @since 1.0.0
 */
function wpestate_post_breadcrumbs_html()   {

    global $post;

    ob_start();

    include(locate_template('/templates/breadcrumbs.php'));

    $output = ob_get_clean();
    $output_length = strlen($output);
    return $output;

}

/**
 * Display Social Icons for a Post
 *
 * This function generates social media sharing icons for a given post.
 * It includes Facebook, Twitter, Pinterest, WhatsApp, and Email sharing options.
 *
 * @param int|null $postID The ID of the post. If null, uses the global post ID.
 * @return string HTML output of social icons.
 */
function wpestate_display_post_social_icons( $postID = null ) {
    global $post;

    if ( is_null( $postID ) ) {
        $postID = $post->ID;
    }

    ob_start();
    // Get the current post URL and title
    $share_url = get_permalink( $postID );
    $share_title =   wpresidence_get_sanitized_truncated_title($postID,0);

    // Prepare email sharing link
    $email_link = wp_sprintf('subject=%s&body=%s', urlencode(get_the_title()), urlencode(get_permalink()));

    // Get WhatsApp sharing link
    $whatsapp_link = wpestate_return_agent_whatsapp_call($postID, '');

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
<?php
    $output = ob_get_clean();

    return $output;
}

/* * Display Post Meta Information
 *
 * This function generates the HTML for displaying post meta information such as author, date, categories, and comments.
 *
 * @return string HTML output of post meta information.
 */
function wpestate_display_post_meta_info( ) {
    global $post;

    ob_start();
?>
    <div class="meta-info">
        <div class="meta-element">
            <i class="far fa-calendar-alt meta_icon firsof"></i>
            <?php
            printf(
                '%s %s %s %s',
                esc_html__('Posted by', 'wpresidence'),
                get_the_author(),
                esc_html__('on', 'wpresidence'),
                get_the_date()
            );
            ?>
        </div>
        <div class="meta-element">
            <i class="far fa-file meta_icon"></i>
            <?php the_category(', '); ?>
        </div>
        <div class="meta-element">
            <i class="far fa-comment meta_icon"></i>
            <?php 
            comments_number(
                esc_html__('0 Comments', 'wpresidence'),
                esc_html__('1 Comment', 'wpresidence'),
                esc_html__('% Comments', 'wpresidence')
            );
            ?>
        </div>
    </div>
<?php
    $output = ob_get_clean();
    return $output;

}

function wpestate_display_related_posts( $postID = null, $settings = array() ) {

    global $post, $wpestate_options;

    $wpestate_options = wpestate_page_details( $postID );

    // Determine if we're using a full-width layout
    $is_full_width = (sanitize_html_class($wpestate_options['content_class']) === 'col-md-12');

    // Set the column class and number of posts to display based on layout and settings
    $similar_posts_count = intval(wpresidence_get_option('wp_estate_similar_blog_post', '',3));

    $posts_to_show = $similar_posts_count == 3 ? ($is_full_width ? 3 : 2) : ($is_full_width ? 4 : 3);


    //Dermine which column class we will use
    $blog_unit_class_request    = wpestate_blog_unit_column_selector($wpestate_options,'similar','');
    $blog_unit_class            = $blog_unit_class_request['col_class'];

    ob_start();

    // Get the tags of the current post
    $tags = wp_get_post_tags($postID);

    // Only proceed if the post has tags
    if ($tags) {
        $tag_ids = wp_list_pluck($tags, 'term_id');

        // Set up the query arguments for related posts
        $args = array(
            'tag__in'           => $tag_ids,
            'post__not_in'      => array($postID),
            'posts_per_page'    => $posts_to_show,
            'post_status'       => 'publish',
            'orderby'           => 'rand',
            'meta_query'        => array(
                array(
                    'key'     => '_thumbnail_id',
                    'compare' => 'EXISTS'
                ),
            )
        );

        // Reset the main query
        wp_reset_query();

        // Execute the query
        $related_query = new WP_Query($args);

        // Check if there are related posts
        if ($related_query->have_posts()) : ?>

            <div class="related_posts row"> 
                <h3><?php esc_html_e($settings['title'], 'wpresidence'); ?></h3>   
                
            
                    <?php
                    // Loop through the related posts
                    while ($related_query->have_posts()) :
                        $related_query->the_post();

                        // Only display posts with thumbnails
                        if (has_post_thumbnail()) :
                            ?>
                        
                            <?php include(locate_template('templates/blog_card_templates/blog_unit2.php')); ?>
                            
                        <?php
                        endif;
                    endwhile;
                    ?>
            
            </div>		
        <?php
        endif; // End if have posts

        // Reset the query
        wp_reset_postdata();
    } // End if tags

    $output = ob_get_clean();
    // $output_length = strlen($output);
    return $output;
}

/**
 * Display Post Slider
 *
 * This function generates a slider for the post's images and video.
 * It includes indicators, navigation controls, and handles both image attachments and embedded videos.
 *
 * @param int|null $postID The ID of the post. If null, uses the global post ID.
 * @param array $settings Optional settings for the slider.
 * @return string HTML output of the post slider.
 */
if ( ! function_exists( 'wpestate_display_post_slider' ) ) :
function wpestate_display_post_slider( $postID = null, $settings = array() ) {

    global $post;

    if ( is_null( $postID ) ) {
        $postID = $post->ID;
    }

    ob_start();

    // Retrieve all image attachments for the current post
    $arguments = array(
        'numberposts' => -1,
        'post_type' => 'attachment',
        'post_parent' => $postID,
        'post_status' => null, 
        'orderby' => 'menu_order',
        'post_mime_type' => 'image',
        'order' => 'ASC'
    );
    $post_attachments = get_posts($arguments);
 
    // Get video information from post meta
    $video_id = esc_html(get_post_meta($postID, 'embed_video_id', true));
    $video_type = esc_html(get_post_meta($postID, 'embed_video_type', true));

    // Check if there are attachments, a featured image, or a video to display
    if ($post_attachments || has_post_thumbnail() || $video_id) {   
        ?>   
        <div id="wpresidence-blog-post-carousel-bootstrap" class="carousel slide post-carusel" style="width: 100%;">
            <!-- Carousel Indicators -->
            <ol class="carousel-indicators">
                <?php  
                $counter = 0;
                $has_video = 0;

                // Add indicator for video if present
                if ($video_id != '') {
                    $has_video = 1; 
                    $counter = 1;
                    echo '<button data-bs-target="#wpresidence-blog-post-carousel-bootstrap" data-bs-slide-to="0" class="active"></button>';
                }
                
                // Add indicators for each image attachment
                if (!empty($post_attachments)) {
                    foreach ($post_attachments as $attachment) {
                        $counter++;
                        $active = ($counter == 1 && $has_video != 1) ? "active" : "";
                        printf('<button  data-bs-target="#wpresidence-blog-post-carousel-bootstrap" data-bs-slide-to="%d" class="%s"></button>', 
                               $counter - 1, esc_attr($active));
                    }
                }
                ?>
            </ol>

            <!-- Carousel Items -->
            <div class="carousel-inner">
                <?php
                // Add video as first item if present  
                if ($video_id != '') {
                    echo '<div class="item carousel-item  active">'; 
                    if ($video_type === 'vimeo') {
                        echo wpestate_custom_vimdeo_video($video_id);
                    } else {
                        echo wpestate_custom_youtube_video($video_id);
                    }
                    echo '</div>';
                }
                
                // Add image attachments as carousel items
                if (!empty($post_attachments)) {
                    $counter = 0;
                    foreach ($post_attachments as $attachment) {
                        $counter++;
                        $active = ($counter == 1 && $has_video != 1) ? "active" : "";
                        $full_img = wp_get_attachment_image_src($attachment->ID, 'listing_full_slider');
                        $full_prty = wp_get_attachment_image_src($attachment->ID, 'full');
                        $attachment_meta = wp_get_attachment($attachment->ID);
                        ?>
                        <div class="item carousel-item  <?php echo esc_attr($active); ?>"> 
                            <a href="<?php echo esc_url($full_prty[0]); ?>"  title="<?php echo esc_attr($attachment_meta['caption']); ?>" class="prettygalery">
                                <img src="<?php echo esc_url($full_img[0]); ?>" alt="<?php echo esc_attr($attachment_meta['alt']); ?>" class="img-responsive lightbox_trigger" />
                            </a>
                            <?php if (!empty($attachment_meta['caption'])) : ?>
                                <div class="carousel-caption">
                                    <?php echo esc_html($attachment_meta['caption']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <!-- Carousel Navigation Controls -->
       

            <button class="carousel-control-prev wpresidence-carousel-control " type="button" data-bs-target="#wpresidence-blog-post-carousel-bootstrap" data-bs-slide="prev">
                <i class="demo-icon icon-left-open-big"></i>
                <span class="visually-hidden">Previous</span>
            </button>

            <button class="carousel-control-next wpresidence-carousel-control " type="button" data-bs-target="#wpresidence-blog-post-carousel-bootstrap" data-bs-slide="next">
                <i class="demo-icon icon-right-open-big"></i>
                <span class="visually-hidden">Next</span>
            </button>
            
        </div>
        <?php
    }
    $output = ob_get_clean();

    return $output;
}
endif;