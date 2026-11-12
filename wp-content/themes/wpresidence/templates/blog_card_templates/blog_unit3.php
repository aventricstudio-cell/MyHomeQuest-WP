<?php
/** MILLDONE
 * Blog Unit 3 Template
 * src templates\blog_card_templates\blog_unit3.php
 * This template is responsible for displaying individual blog post entries
 * in a specific layout (Blog Unit 3) throughout the WPResidence theme.
 * It's typically used in archive pages, search results, and custom blog list templates.
 * This layout focuses on a full-width image background with overlay text.
 *
 * @package WPResidence
 * @subpackage Templates
 * @since 1.0
 */

// Initialize variables for post preview, word limit, link, and title
$preview        = array();
$preview[0]     = '';
$words          = 55;
$link           = esc_url(get_permalink());

// Get the post ID
$postID = get_the_ID();

// Get the featured image URL
$thumb_prop = get_the_post_thumbnail_url($postID, 'property_listings'); 

// Set a default image if no featured image is available
if ($thumb_prop == '') {
    $thumb_prop = get_theme_file_uri('/img/defaults/default_property_listings.jpg');
}         
?>  

<div class="<?php echo esc_attr($blog_unit_class); ?> listing_wrapper  blog3v"> 
    <div class="property_listing_blog" data-link="<?php echo esc_attr($link); ?>">
        <?php
        // Add a gradient overlay and set the background image
        if ($thumb_prop != '') {
            echo '<div class="featured_gradient"></div>';
            echo '<div class="blog_unit_image" style="background-image:url(' . esc_url($thumb_prop) . ');"></div>';
        }
        ?>
        
        <div class="blog_unit_content_v3">
            <div class="blog_unit_meta">
                <?php echo get_the_date(); ?>
            </div>

            <h4>
               <a href="<?php the_permalink(); ?>" class="blog_unit_title">
                    <?php 
                    echo  wpresidence_get_sanitized_truncated_title($postID,44);
                    ?>
                </a> 
            </h4>
            <a class="read_more" href="<?php the_permalink(); ?>">
                <?php esc_html_e('Continue reading', 'wpresidence'); ?>
                <i class="fas fa-angle-right"></i>
            </a>
        </div>
    </div>          
</div>