<?php
/** MILLDONE
 * Featured Property Type 4 Display Template for WpResidence Theme
 * src: templates/featured_property_card_templates/featured_property_4b.php
 * This template generates the HTML for displaying a featured property of type 4
 * in the WpResidence WordPress theme.
 *
 * @package WpResidence
 * @subpackage PropertyDisplay
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core functions (get_permalink, wp_get_attachment_image_src, get_post_thumbnail_id)
 * - WpResidence theme functions (wpestate_return_agent_details)
 *
 * Usage:
 * This template is typically included within a loop or function that provides the $prop_id variable.
 * It displays a featured property with a large background image, property title, and agent information.
 */

// Retrieve property details
$link = esc_url(get_permalink($prop_id));
$realtor_details = wpestate_return_agent_details($prop_id);
$property_featured_image_url = wpestate_get_property_featured_image($prop_id, 'full');


?>
<div class="featured_article_type2 featured_prop_type4">
    <div class="featured_img_type2" data-link="<?php echo esc_url($link); ?>" style="background-image:url(<?php echo esc_url($property_featured_image_url); ?>)">
        <div class="featured_gradient"></div>
        <div class="featured_article_type2_title_wrapper container-fluid px-3 px-lg-0 ">
            <div class="featured_article_label"><?php esc_html_e('Featured Property', 'wpresidence'); ?></div>
            <a href="<?php echo esc_url($link); ?>">
                <h2 class="col-md-7"><?php echo esc_html(get_the_title($prop_id)); ?></h2>
            </a>
            <div class="featured_read_more">
                <a href="<?php echo esc_url($link); ?>">
                    <?php esc_html_e('discover more', 'wpresidence'); ?>
                </a>
                <i class="fas fa-angle-right"></i>
            </div>
            
            <div class="featured_property_type4_agent_wrapper">
                <a href="<?php echo esc_url($realtor_details['link']); ?>" class="featured_property_type4_agent" style="background-image:url('<?php echo esc_url($realtor_details['realtor_image']); ?>');" aria-label="<?php echo esc_attr($realtor_details['realtor_name']); ?>"></a>
                <a href="<?php echo esc_url($realtor_details['link']); ?>" class="featured_property_type4_agent_name"><?php echo esc_html($realtor_details['realtor_name']); ?></a>
            </div>
        </div>        
    </div>
</div>