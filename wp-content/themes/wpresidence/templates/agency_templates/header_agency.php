<?php
/** MILLDONE
 * Agency Header Template
 * src: templates/agency_templates/header_agency.php
 * This template displays the header information for a single agency in the WpResidence theme.
 * It includes the agency's image, contact details, and other relevant information.
 *
 * @package WpResidence
 * @subpackage Templates
 * @since 1.0.0
 */

 $use_default_template=true;
// Check if Elementor is being used to render this page
if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {

    if( did_action( 'elementor/loaded' ) && function_exists('wpestate_single_agency_enabled') && wpestate_single_agency_enabled()  ) {
       
        $use_default_template=false;
    }
}



// if we use default template
if($use_default_template):

    // Fetch agency details
    $thumb_id = get_post_thumbnail_id($postID);
    $preview = wp_get_attachment_image_src($thumb_id, 'property_listings');
    $preview_img = isset($preview[0]) ? $preview[0] : get_theme_file_uri('/img/default_user_agent.png');

    // Fetch agency meta information
    $agency_meta = array(
        'skype'          => get_post_meta($postID, 'agency_skype', true),
        'phone'          => get_post_meta($postID, 'agency_phone', true),
        'mobile'         => get_post_meta($postID, 'agency_mobile', true),
        'email'          => get_post_meta($postID, 'agency_email', true),
        'position'       => get_post_meta($postID, 'agency_position', true),
        'facebook'       => get_post_meta($postID, 'agency_facebook', true),
        'twitter'        => get_post_meta($postID, 'agency_twitter', true),
        'linkedin'       => get_post_meta($postID, 'agency_linkedin', true),
        'pinterest'      => get_post_meta($postID, 'agency_pinterest', true),
        'instagram'      => get_post_meta($postID, 'agency_instagram', true),
        'opening_hours'  => get_post_meta($postID, 'agency_opening_hours', true),
        'address'        => get_post_meta($postID, 'agency_address', true),
        'languages'      => get_post_meta($postID, 'agency_languages', true),
        'license'        => get_post_meta($postID, 'agency_license', true),
        'taxes'          => get_post_meta($postID, 'agency_taxes', true),
        'website'        => get_post_meta($postID, 'agency_website', true),
    );

    $name = wpresidence_get_sanitized_truncated_title($postID,0);
    $link = get_permalink($postID);

    // Ensure email is valid
    $agency_meta['email'] = is_email($agency_meta['email']) ? $agency_meta['email'] : '';

    // Start of HTML output
    ?>
    <div class="header_agency_wrapper">
        <div class="header_agency_container">
            <div class="row">
                <div class="col-md-4">
                    <a href="<?php echo esc_url($link); ?>">
                        <img src="<?php echo esc_url($preview_img); ?>" alt="<?php esc_attr_e('Agency Image', 'wpresidence'); ?>" class="img-responsive" />
                    </a>
                </div>
                
                <div class="col-md-8 row">
                    <h1 class="agency_title"><?php echo esc_html($name); ?></h1>
                    
                    <div class="col-md-6 agency_details">
                        <?php
                        $primary_details = array(
                            'address' => esc_html__('Address:', 'wpresidence'),
                            'email'   => esc_html__('Email:', 'wpresidence'),
                            'mobile'  => esc_html__('Mobile:', 'wpresidence'),
                            'phone'   => esc_html__('Phone:', 'wpresidence'),
                            'skype'   => esc_html__('Skype:', 'wpresidence')
                        );

                        foreach ($primary_details as $key => $label) {
                            if (!empty($agency_meta[$key])) {
                                $value = $agency_meta[$key];
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
                    </div>   
                    
                    <div class="col-md-6 agency_details">
                        <?php
                        $secondary_details = array(
                            'website'        => esc_html__('Website:', 'wpresidence'),
                            'languages'      => esc_html__('We Speak:', 'wpresidence'),
                            'opening_hours'  => esc_html__('Opening Hours:', 'wpresidence'),
                            'license'        => esc_html__('License:', 'wpresidence'),
                            'taxes'          => esc_html__('Our Taxes:', 'wpresidence')
                        );

                        foreach ($secondary_details as $key => $label) {
                            if (!empty($agency_meta[$key])) {
                                $value = $agency_meta[$key];
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
                    
                    <a href="#agency_contact" class="wpresidence_button agency_contact_but"><?php esc_html_e('Contact Us', 'wpresidence'); ?></a>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>