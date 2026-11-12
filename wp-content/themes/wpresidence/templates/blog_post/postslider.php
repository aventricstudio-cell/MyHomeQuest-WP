<?php
/** MILLDONE
 * Post Slider Template (Bootstrap 5 with some Bootstrap 3 elements retained)
 * src: templates\blog_post\postslider.php
 * This template generates a carousel/slider for blog post images and videos.
 * It's updated for Bootstrap 5 but retains some Bootstrap 3 elements as specified.
 *
 * @package WPEstate
 * @subpackage Templates
 * @since 2.0
 */

// Check if group pictures are enabled for this post
if (esc_html(get_post_meta($post->ID, 'group_pictures', true)) != 'no') {    
    // Retrieve all image attachments for the current post
    $arguments = array(
        'numberposts' => -1,
        'post_type' => 'attachment',
        'post_parent' => $post->ID,
        'post_status' => null, 
        'orderby' => 'menu_order',
        'post_mime_type' => 'image',
        'order' => 'ASC'
    );
    $post_attachments = get_posts($arguments);
 
    // Get video information from post meta
    $video_id   = esc_html(get_post_meta($post->ID, 'embed_video_id', true));
    $video_type = strtolower( trim( esc_html( get_post_meta($post->ID, 'embed_video_type', true) ) ) );

    // Check if there are attachments, a featured image, or a video to display
    if ($post_attachments || has_post_thumbnail() || $video_id) {   
        ?>   
        <div id="wpresidence-blog-post-carousel-bootstrap" class="carousel slide post-carusel" >
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
                    } elseif ($video_type === 'tiktok') {
                        echo wpestate_custom_tiktok_video($video_id);
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
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>

            <button class="carousel-control-next wpresidence-carousel-control " type="button" data-bs-target="#wpresidence-blog-post-carousel-bootstrap" data-bs-slide="next">
                 <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
            
        </div>
        <?php
    }
}
?>