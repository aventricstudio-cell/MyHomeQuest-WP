<?php
/** MILLDONE
 * Blog Unit 4 Template
 * src: templates\blog_card_templates\blog_unit4.php
 * This template is responsible for displaying individual blog post entries
 * in a specific layout (Blog Unit 4) throughout the WPResidence theme.
 * It's typically used in archive pages, search results, and custom blog list templates.
 * This layout focuses on a simple, clean design with the featured image at the top.
 *
 * @package WPResidence
 * @subpackage Templates
 * @since 1.0
 */

// Initialize variables for post preview, word limit, link, and title
$preview = array();
$preview[0] = '';
$words = 55;
$link = esc_url(get_permalink());


// Get the post ID
$postID = get_the_ID();
?>  

<div class="<?php echo esc_attr($blog_unit_class); ?> listing_wrapper  blog4v"> 
    <div class="property_listing_blog" data-link="<?php echo esc_attr($link); ?>">
        <?php
        // Get image URLs for different sizes
        $pinterest = wp_get_attachment_image_src(get_post_thumbnail_id(), 'property_full_map');
        $preview = wp_get_attachment_image_src(get_post_thumbnail_id(), 'property_listings');
        $compare = wp_get_attachment_image_src(get_post_thumbnail_id(), 'slider_thumb');
        
        // Set image attributes for lazy loading
        $extra = array(
            'class' => 'lazyload img-responsive',    
        );
        
        // Set the data-original attribute for the preview image
        if (isset($preview[0])) {
           $extra['data-original'] = $preview[0];
        } else {
            $extra['data-original'] = get_theme_file_uri('/img/defaults/default_blog_unit.jpg');
        }
     
        // Get the post thumbnail with the specified attributes
        $thumb_prop = get_the_post_thumbnail($postID, 'property_listings', $extra); 
  
        // Set default thumbnail if no post thumbnail is available
        if ($thumb_prop == '') {
            $thumb_prop_default = get_theme_file_uri('/img/defaults/default_property_listings.jpg');
            $thumb_prop = '<img src="' . esc_url($thumb_prop_default) . '" class="b-lazy img-responsive wp-post-image lazy-hidden" alt="' . esc_attr__('default image', 'wpresidence') . '" />';   
        }

        // Get the featured property meta value
        $featured = intval(get_post_meta($postID, 'prop_featured', true));
    
        // Display the thumbnail if it exists
        if ($thumb_prop != '') {
            echo '<div class="blog_unit_image">';
            echo '<a href="' . esc_url($link) . '">' . $thumb_prop . '</a>';
            echo '</div>'; 
        }
        ?>

        <h4>
            <a href="<?php the_permalink(); ?>" class="blog_unit_title">
                <?php 
                    echo  wpresidence_get_sanitized_truncated_title($postID,44);
                ?>
            </a> 
        </h4>
    
        <div class="listing_details the_grid_view">
            <?php   
            // Display excerpt based on whether the post has a thumbnail or not
            if (has_post_thumbnail()) {
               echo wpestate_strip_excerpt_by_char(get_the_excerpt(), 80, $postID, '...');
            } else {
                echo wpestate_strip_excerpt_by_char(get_the_excerpt(), 200, $postID, '...');
            } 
            ?>
        </div>

        <div class="blog_unit_meta">
            <span class="span_widemeta">
                <?php 
                echo esc_html__('published on', 'wpresidence') . ' ' . get_the_date();
                ?>
            </span>  
        </div>

    </div>          
</div>