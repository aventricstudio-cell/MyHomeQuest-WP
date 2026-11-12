<?php
/** MILLDONE
 * Agent Details Template
 * src: templates\realtor_templates\agentdetails.php
 * This template displays detailed information about a real estate agent or realtor.
 * It includes the agent's image, contact details, social icons, and additional information.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 3.0.3
 *
 * @global WP_Post $post Current post object.
 */

// Set column sizes for layout
$pict_size = 6;
$content_size = 6;

// Ensure $realtor_details array is available and contains necessary data
if (!isset($realtor_details) || !is_array($realtor_details)) {
    return;
}


if(isset($post->ID)){
    $propertyID=$post->ID;
}else if(isset($property_page_context) && ($property_page_context=='custom_page_temaplate'  || $property_page_context=='modal_window') ){
    $propertyID=$prop_id;
}


// Start of HTML output
?>

<div class="wpestate_contact_form_parent wpestate_agent_details_wrapper row wpestate_single_agent_details_wrapper" id="wpestate_single_agent_details_wrapper">
    <div class="col-md-<?php echo esc_attr($pict_size); ?> agentpic-wrapper">
        <div class="agent-listing-img-wrapper" data-link="<?php echo esc_attr($realtor_details['link']); ?>">
            <div class="agentpict" style="background-image:url(<?php echo esc_url($realtor_details['realtor_image']); ?>)"></div>
        </div>
        <?php echo wpestate_return_agent_share_social_icons($realtor_details, 'agent_unit_social_single'); ?>
    </div>  
    <div class="col-md-<?php echo esc_attr($content_size); ?> agent_details">    
        <?php
        $author = get_post_field('post_author', $propertyID);
        $agency_post = get_the_author_meta('user_agent_id', $author);
        ?>
        <h3><a href="<?php echo esc_url($realtor_details['link']); ?>"><?php echo esc_html($realtor_details['realtor_name']); ?></a></h3>
        <div class="agent_position">
            <?php 
            echo esc_html($realtor_details['realtor_position']);
            if (is_singular('estate_agent') && $agency_post) {
                echo ', <a href="' . esc_url(get_permalink($agency_post)) . '">' . esc_html(get_the_title($agency_post)) . '</a>';
            }
            ?>
        </div>
        <?php echo wpestate_return_agent_contact_details($realtor_details); ?>
    </div>
   
    <div class="custom_details_container">
        <?php
        if (!is_singular('estate_property')) {
            if (!(class_exists('Elementor\Plugin') && \Elementor\Plugin::$instance->editor->is_edit_mode())) {
                
                if( !isset($property_page_context) || $property_page_context!=='modal_window'){
                    include(locate_template('templates/realtor_templates/agent_contact_bar.php'));
               
                    get_template_part('templates/realtor_templates/agent_custom_data');
                    if (wpresidence_get_option('wp_estate_agent_page_show_speciality_service', '') === 'yes') {
                        get_template_part('templates/realtor_templates/agent_taxonomies');
                    }
                }
               
            }
        }
        ?>
    </div>
   
    <?php
    if (isset($agent_context) && $agent_context === 'property_page') {
        $context = 'property_page_form';
        include(locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php'));
    }
    ?>
</div>

<?php    
if ( !isset($property_page_context) || $property_page_context !== 'modal_window' ) {
    get_template_part('templates/realtor_templates/agent_about_me');
}
?>