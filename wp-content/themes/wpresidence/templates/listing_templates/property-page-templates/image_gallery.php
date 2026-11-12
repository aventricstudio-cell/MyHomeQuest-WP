<?php
/** MILLDONE
 * Lightbox Property Slider
 * src: templates\listing_templates\property-page-templates\image_gallery.php
 * This template renders a lightbox for property images with optional contact form. 
 * It uses Owl Carousel for the image slider and handles cropping based on theme settings.
 * 
 * @package WpResidence
 * @since 3.0.3
 * 
 * Dependencies:
 * - Owl Carousel (script enqueued).
 * - Property contact form included if enabled in the theme settings.
 * 
 * Usage:
 * Include this template within a property page to show the lightbox slider for images.
 */

global $post;
//set property id for contact forms
$propertyID = $post->ID;
// Enqueue necessary Owl Carousel script for the slider functionality
wp_enqueue_script('owl_carousel');

// Get theme options for lightbox settings
$crop_images_lightbox       = esc_html(wpresidence_get_option('wp_estate_crop_images_lightbox', ''));
$show_lightbox_contact      = esc_html(wpresidence_get_option('wp_estate_show_lightbox_contact', ''));

// Define CSS class for the image wrapper based on settings
$class_image_wrapper        = 'col-md-10';
$class_image_wrapper_global = '';

// Adjust classes if contact form is not shown
if ($show_lightbox_contact === 'no') {
    $class_image_wrapper        = 'col-md-12 lightbox_no_contact';    
    $class_image_wrapper_global .= ' lightbox_wrapped_no_contact';
}

// Adjust classes if image cropping is disabled
if ($crop_images_lightbox === 'no') {
    $class_image_wrapper_global .= ' ligtbox_no_crop';
}

?>

<div class="lightbox_property_wrapper"> 
    <div class="lightbox_property_wrapper_level2 <?php echo esc_attr($class_image_wrapper_global); ?>">
        <div class="lightbox_property_content row">
            <div class="lightbox_property_slider <?php echo esc_attr($class_image_wrapper); ?>">
                <div id="owl-demo" class="owl-carousel owl-theme ">
                    <?php
                    // Initialize counter for image slides
                    $counter            = 0;
                   
                    // Fetch and loop through all additional image attachments
                    $post_attachments = wpestate_generate_property_slider_image_ids( $post->ID, true );

                


                    foreach ($post_attachments as $attachment_id) {
                        if (!wp_attachment_is_image($attachment_id)) {
                            continue; // Skip this attachment if it's not an image
                        }
            
                        $counter++;
                        $full_img = wp_get_attachment_image_src($attachment_id, $crop_images_lightbox === 'yes' ? 'listing_full_slider_1' : 'full');
                        $attachment_meta = wp_get_attachment($attachment_id);

                        echo '<div class="item" href="#' . esc_attr($counter) . '" ' . ($crop_images_lightbox === 'yes' ? 'style="background-image:url(' . esc_attr($full_img[0]) . ')"' : '') . '>';
                        if ($crop_images_lightbox !== 'yes') {
                            echo '<img src="' . esc_url($full_img[0]) . '" alt="' . esc_html__('image', 'wpresidence') . '">';
                        }
                        if (trim($attachment_meta['caption']) !== '') {
                            echo '<div class="owl_caption">' . esc_html($attachment_meta['caption']) . '</div>';
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

            <?php if ($show_lightbox_contact === 'yes') : ?>
                <div class="lightbox_property_sidebar col-md-2">
                    <div class="lightbox_property_header">
                        <div class="entry-title entry-prop"><?php the_title(); ?></div>
                    </div>
                    <h4 class="lightbox_enquire"><?php esc_html_e('Want to find out more?', 'wpresidence'); ?></h4>
                    <div class="agent_contanct_form wpestate_contact_form_parent">
                        <?php
                     
                        $context    = 'property_page_slider';
                        include locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php');
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Close button for the lightbox -->
        <div class="lighbox-image-close">
            <i class="fas fa-times" aria-hidden="true"></i>
        </div>
    </div>

    <!-- Overlay for the lightbox -->
    <div class="lighbox_overlay"></div>
</div>

<!-- JavaScript to start the lightbox functionality -->
<script type="text/javascript">
    //<![CDATA[
    jQuery(document).ready(function(){
        estate_start_lightbox();
    });
    //]]>
</script>
