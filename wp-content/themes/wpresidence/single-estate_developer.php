<?php
/**MILLDONE
 * Single Estate Developer Template
 * src: single-estate_developer.php
 * This template displays the single view of an estate developer custom post type.
 * It includes developer details, listings, agents, contact form, map, and reviews.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0.0
 */

get_header();
$use_default_template=true;
// Check if Elementor is being used to render this page
if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {

    if( did_action( 'elementor/loaded' ) && function_exists('wpestate_single_developer_enabled') && wpestate_single_developer_enabled()  ) {
        wpestate_render_single_developer();
        $use_default_template=false;
    }
}

// Retrieve theme options
$wpestate_options        = get_query_var('wpestate_options');


// if we use default template
if($use_default_template):
    $wpestate_options['content_class'] = 'col-lg-12';
    // Start the main content wrapper
    ?>
    <div class="wpresidence-content-container-wrapper col-12 d-flex flex-wrap">
        <?php get_template_part('templates/breadcrumbs'); ?>
        
        <div class="<?php echo esc_attr($wpestate_options['content_class']); ?>">
            <?php
            // Get the current post data
            $post = get_post();
            
            if ($post) {
                // set the agentID for contact forms
                $agency_id =$agentID= $post->ID;

                $thumb_id  = get_post_thumbnail_id($agency_id);
                $preview   = wp_get_attachment_image_src($thumb_id, 'property_listings');
                $preview_img = isset($preview[0]) ? $preview[0] : get_theme_file_uri('/img/defaults/default_user_agent.png');
        
                $name = wpresidence_get_sanitized_truncated_title($agency_id,0);
                
                // Include developer listings template
                include(locate_template('templates/developer_templates/developer_listings.php'));
                
                // Include agency agents template
                include(locate_template('templates/agency_templates/agency_developer_agents.php'));
                ?>
                
                <div class="developer_contact_wrapper flex-column flex-md-row">   
                    <div class="col-md-6" id="agency_contact">       
                        <div class="agent_contanct_form wpestate_contact_form_parent">       
                            <?php
                            $context = 'developer_page';
                            include(locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php'));
                            ?>
                        </div>
                    </div>

                    <div class="col-md-6 developer_map">
                        <?php include(locate_template('templates/agency_templates/agency_map.php')); ?>
                    </div>
                </div>
                
                <?php
                // Check if reviews should be displayed
                $wp_estate_show_reviews = wpresidence_get_option('wp_estate_show_reviews_block', '');
                if (is_array($wp_estate_show_reviews) && in_array('developer', $wp_estate_show_reviews, true)) {
                    // include(locate_template('templates/agency_templates/agency_developer_reviews.php'));
                    get_template_part('templates/reviews/reviews');
                }
            } else {
                // No post found
                esc_html_e('No developer found.', 'wpresidence');
            }
            ?>
        </div>
    </div>

<?php
endif; 
get_footer(); 
?>