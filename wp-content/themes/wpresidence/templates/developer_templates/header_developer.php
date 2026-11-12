<?php
/**MILLDONE
 * Developer Header Template
 * src: templates\developer_templates\header_developer.php
 * This template displays the header information for a single developer in the WpResidence theme.
 * It includes the developer's image, contact details, and associated taxonomies.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0.0
 */


 $use_default_template=true;
// Check if Elementor is being used to render this page
if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {

    if( did_action( 'elementor/loaded' ) && function_exists('wpestate_single_developer_enabled') && wpestate_single_developer_enabled()  ) {
        $use_default_template=false;
    }
}


// if we use default template
if($use_default_template):

    // Fetch developer details
    $thumb_id = get_post_thumbnail_id($postID);
    $preview = wp_get_attachment_image_src($thumb_id, 'property_listings');
    $preview_img = isset($preview[0]) ? $preview[0] : get_theme_file_uri('/img/default_user_agent.png');

    // Fetch developer meta information
    $developer_meta = array(
        'skype'          => get_post_meta($postID, 'developer_skype', true),
        'phone'          => get_post_meta($postID, 'developer_phone', true),
        'mobile'         => get_post_meta($postID, 'developer_mobile', true),
        'email'          => get_post_meta($postID, 'developer_email', true),
        'position'       => get_post_meta($postID, 'developer_position', true),
        'opening_hours'  => get_post_meta($postID, 'developer_opening_hours', true),
        'address'        => get_post_meta($postID, 'developer_address', true),
        'languages'      => get_post_meta($postID, 'developer_languages', true),
        'license'        => get_post_meta($postID, 'developer_license', true),
        'taxes'          => get_post_meta($postID, 'developer_taxes', true),
        'website'        => get_post_meta($postID, 'developer_website', true)
    );

    $name = get_the_title($postID);
    $link = get_permalink($postID);
    $realtor_details = wpestate_return_agent_details($postID, $postID);

    // Start of HTML output
    ?>
    <div class="header_agency_wrapper">
        <div class="header_agency_container">
            <div class="row">
                <div class="col-md-4">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($preview_img); ?>" alt="<?php esc_attr_e('Developer Image', 'wpresidence'); ?>" class="img-responsive" />
                    </a>
                </div>
                
                <div class="col-md-8 flex-wrap  d-flex  ">
                    <h1 class=" col-md-12 agency_title"><?php echo esc_html($name); ?></h1>
                    
                    <div class="col-md-6 agency_details">
                        <?php echo wpestate_return_agent_share_social_icons($realtor_details, 'agency_socialpage_wrapper', 'agency_social'); ?>
                        
                        <?php
                        // Display developer details
                        $details = array(
                            'address' => esc_html__('Address:', 'wpresidence'),
                            'email'   => esc_html__('Email:', 'wpresidence'),
                            'mobile'  => esc_html__('Mobile:', 'wpresidence'),
                            'phone'   => esc_html__('Phone:', 'wpresidence')
                        );

                        foreach ($details as $key => $label) {
                            if (!empty($developer_meta[$key])) {
                                $value = $developer_meta[$key];
                                if ($key === 'email') {
                                
                                    $email_link    = antispambot(esc_attr($value));
                                    $value = '<a href="mailto:' . $email_link . '">' . $email_link . '</a>';
                                } elseif (in_array($key, array('mobile', 'phone'))) {
                                    $value = '<a href="tel:' . esc_attr($value) . '">' . esc_html($value) . '</a>';
                                } else {
                                    $value = esc_html($value);
                                }
                                echo '<div class="agency_detail agency_' . esc_attr($key) . '"><strong>' . $label . '</strong> ' . $value . '</div>';
                            }
                        }
                        ?>
                        
                        <a href="#agency_contact" class="developer_contact_button wpresidence_button"><?php esc_html_e('Contact Us', 'wpresidence'); ?></a>
                    </div>   
                    
                    <div class="col-md-6 agency_details">
                        <div class="developer_taxonomy">
                            <?php
                            $taxonomies = array(
                                'property_county_state_developer',
                                'property_city_developer',
                                'property_area_developer',
                                'property_category_developer',
                                'property_action_developer'
                            );

                            $developer_term_list = '';
                            foreach ($taxonomies as $taxonomy) {
                                if (!taxonomy_exists($taxonomy)) {
                                    continue;
                                }

                                $term_list = get_the_term_list($postID, $taxonomy, '', '', '');
                                if (!is_wp_error($term_list) && $term_list) {
                                    $developer_term_list .= $term_list;
                                }
                            }

                            echo wp_kses_post($developer_term_list);
                            ?>
                        </div>                  
                    </div>
                </div>
                
                <div class="row developer_content">
                    <div class="col-md-8">
                        <?php
                        $content_post = get_post($postID);
                        $content = $content_post->post_content;
                        $content = apply_filters('the_content', $content);
                        $content = str_replace(']]>', ']]&gt;', $content);
                        echo wp_kses_post(trim($content));
                        echo wpresidence_display_agent_custom_fields( $postID );
                        ?>
                    </div>
                    
                    <div class="col-md-4">
                        <?php
                        $additional_details = array(
                            'website' => esc_html__('Website:', 'wpresidence'),
                            'skype'   => esc_html__('Skype:', 'wpresidence'),
                            'license' => esc_html__('License:', 'wpresidence'),
                            'taxes'   => esc_html__('Our Taxes:', 'wpresidence')
                        );

                        foreach ($additional_details as $key => $label) {
                            if (!empty($developer_meta[$key])) {
                                $value = $developer_meta[$key];
                                if ($key === 'website') {
                                    $value = '<a href="' . esc_url($value) . '" target="_blank">' . esc_html($value) . '</a>';
                                } else {
                                    $value = esc_html($value);
                                }
                                echo '<div class="agency_detail agency_' . esc_attr($key) . '"><strong>' . $label . '</strong> ' . $value . '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php 
endif;
?>