<?php
/** MILLDONE
 * WpResidence Theme - Agent Profile Template
 * src: templates/agent_profile.php
 * This template handles the display and functionality of the agent profile page
 * in the WpResidence theme, including contact information, social media links,
 * location details, and profile picture management.
 *
 * @package WpResidence
 * @subpackage AgentProfile
 * @since 1.0.0
 */

// Retrieve current user and agent information
$current_user = wp_get_current_user();
$userID = $current_user->ID;
$user_login = $current_user->user_login;
$agent_id = get_the_author_meta('user_agent_id', $userID);
$agent_custom_data = get_post_meta($agent_id, 'agent_custom_data', true);
$author = get_post_field('post_author', $agent_id);
$agency_post = get_the_author_meta('user_agent_id', $author);
$agency_post_type = get_post_type($agency_post);

// Define form fields for agent information
$agent_contact_information_fields = array(
    'firstname' => array('label' => esc_html__('First Name', 'wpresidence'), 'meta_name' => 'first_name', 'type' => 'post_meta'),
    'secondname' => array('label' => esc_html__('Last Name', 'wpresidence'), 'meta_name' => 'last_name', 'type' => 'post_meta'),
    'userphone' => array('label' => esc_html__('Phone', 'wpresidence'), 'meta_name' => 'agent_phone', 'type' => 'post_meta'),
    'useremail' => array('label' => esc_html__('Email', 'wpresidence'), 'bypass' => $userID, 'meta' => 'user_email', 'type' => 'user_meta'),
    'usermobile' => array('label' => esc_html__('Mobile', 'wpresidence'), 'meta_name' => 'agent_mobile', 'type' => 'post_meta'),
    'userskype' => array('label' => esc_html__('Skype', 'wpresidence'), 'meta_name' => 'agent_skype', 'type' => 'post_meta'),
    'agent_member' => array('label' => esc_html__('Member of', 'wpresidence'), 'meta_name' => 'agent_member', 'type' => 'post_meta'),
    'agent_address' => array('label' => esc_html__('Address', 'wpresidence'), 'meta_name' => 'agent_address', 'type' => 'post_meta'),
);

// Add HubSpot API field if enabled and not an agency or developer
if (wpresidence_get_option('wp_estate_enable_hubspot_integration_for_all', '') == 'yes' && $agency_post_type != 'estate_agency' && $agency_post_type != 'estate_developer') {
    $agent_contact_information_fields['hubspot_api'] = array(
        'label' => esc_html__('HubSpot Private Application Token', 'wpresidence'),
        'meta_name' => 'hubspot_api',
        'type' => 'post_meta',
    );
}

// Define form fields for social media information
$agent_social_information_fields = array(
    'userfacebook' => array('label' => esc_html__('Facebook Url', 'wpresidence'), 'meta_name' => 'agent_facebook', 'type' => 'post_meta'),
    'userinstagram' => array('label' => esc_html__('Instagram Url', 'wpresidence'), 'meta_name' => 'agent_instagram', 'type' => 'post_meta'),
    'usertwitter' => array('label' => esc_html__('X - Twitter Url', 'wpresidence'), 'meta_name' => 'agent_twitter', 'type' => 'post_meta'),
    'userpinterest' => array('label' => esc_html__('Pinterest Url', 'wpresidence'), 'meta_name' => 'agent_pinterest', 'type' => 'post_meta'),
    'userlinkedin' => array('label' => esc_html__('Linkedin Url', 'wpresidence'), 'meta_name' => 'agent_linkedin', 'type' => 'post_meta'),
    'useryoutube' => array('label' => esc_html__('Youtube Url', 'wpresidence'), 'meta_name' => 'agent_youtube', 'type' => 'post_meta'),
    'usertiktok' => array('label' => esc_html__('TikTok Url', 'wpresidence'), 'meta_name' => 'agent_tiktok', 'type' => 'post_meta'),
    'usertelegram' => array('label' => esc_html__('Telegram Url', 'wpresidence'), 'meta_name' => 'agent_telegram', 'type' => 'post_meta'),
    'uservimeo' => array('label' => esc_html__('Vimeo Url', 'wpresidence'), 'meta_name' => 'agent_vimeo', 'type' => 'post_meta'),
    'website' => array('label' => esc_html__('Website Url (without http)', 'wpresidence'), 'meta_name' => 'agent_website', 'type' => 'post_meta'),
);

// Define form fields for agent location
$agent_location_fields = array(
    'agent_city' => array('label' => esc_html__('City', 'wpresidence'), 'meta' => 'last_name', 'type' => 'taxonomy_name', 'tax_name' => 'property_city_agent'),
    'agent_county' => array('label' => esc_html__('State/County', 'wpresidence'), 'meta' => 'last_name', 'type' => 'taxonomy_name', 'tax_name' => 'property_county_state_agent'),
    'agent_area' => array('label' => esc_html__('Area', 'wpresidence'), 'meta' => 'last_name', 'type' => 'taxonomy_name', 'tax_name' => 'property_area_agent'),
);

// Define form fields for agent details
$agent_about_details_fields = array(
    'usertitle' => array('label' => esc_html__('Title/Position', 'wpresidence'), 'meta_name' => 'agent_position', 'type' => 'post_meta', 'bootstral_length' => 12),
    'about_me' => array('label' => esc_html__('About Me', 'wpresidence'), 'meta' => 'about_me', 'inputype' => 'textarea', 'type' => 'wpestate_description', 'bootstral_length' => 12),
    'agent_private_notes' => array('label' => esc_html__('Private Notes', 'wpresidence'), 'inputype' => 'textarea', 'meta_name' => 'agent_private_notes', 'type' => 'post_meta'),
);

// Get agent profile picture
$user_custom_picture = get_the_post_thumbnail_url($agent_id, 'user_picture_profile');
$image_id = get_post_thumbnail_id($agent_id);

if (empty($user_custom_picture)) {
    $user_custom_picture = get_theme_file_uri('/img/default_user.png');
}

$user_agent_id = intval(get_user_meta($userID, 'user_agent_id', true));

?>

<?php if (wp_is_mobile()): ?>
    <div class="add-estate profile-page profile-onprofile">
        <?php
        if ($user_agent_id != 0 && get_post_status($user_agent_id) == 'pending') {
            echo '<div class="user_dashboard_app">' . esc_html__('Your account is pending approval. Please wait for admin to approve it. ', 'wpresidence') . '</div>';
        }
        if ($user_agent_id != 0 && get_post_status($user_agent_id) == 'disabled') {
            echo '<div class="user_dashboard_app">' . esc_html__('Your account is disabled.', 'wpresidence') . '</div>';
        }
        ?>
    </div>
<?php endif; ?>


<div class=" col-12 col-md-12 col-lg-8  user_profile_div">
    <div class="wpestate_dashboard_content_wrapper">
        <div id="profile_message"></div>
        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title"><?php esc_html_e('Contact Information', 'wpresidence'); ?></div>
            <?php echo wpestate_dashnoard_display_form_fields($agent_contact_information_fields, $agent_id); ?>
            <?php wp_nonce_field('profile_ajax_nonce', 'security-profile'); ?>
        </div>
    </div>

    <div class="wpestate_dashboard_content_wrapper">
        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title"><?php esc_html_e('Social Information', 'wpresidence'); ?></div>
            <?php echo wpestate_dashnoard_display_form_fields($agent_social_information_fields, $agent_id); ?>
        </div>

        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title"><?php esc_html_e('Agent Area/Categories', 'wpresidence'); ?></div>
            <?php if (taxonomy_exists('property_category_agent')) : ?>
                <div class="col-md-6">
                    <p>
                        <label for="agent_category_submit"><?php esc_html_e('Category', 'wpresidence'); ?></label>
                        <?php
                        $agent_category_selected = '';
                        $agent_category_array = get_the_terms($agent_id, 'property_category_agent');
                        if (!is_wp_error($agent_category_array) && isset($agent_category_array[0])) {
                            $agent_category_selected = $agent_category_array[0]->term_id;
                        }

                        $args = array(
                            'class' => 'select-submit2',
                            'hide_empty' => false,
                            'selected' => $agent_category_selected,
                            'name' => 'agent_category_submit',
                            'id' => 'agent_category_submit',
                            'orderby' => 'NAME',
                            'order' => 'ASC',
                            'show_option_none' => esc_html__('None', 'wpresidence'),
                            'taxonomy' => 'property_category_agent',
                            'hierarchical' => true
                        );
                        $terms_check = get_terms(array('taxonomy' => 'property_category_agent', 'number' => 1, 'hide_empty' => false));
                        if (!is_wp_error($terms_check)) {
                        wp_dropdown_categories($args);
                        }
                        ?>
                    </p>
                </div>
            <?php endif; ?>
            <?php if (taxonomy_exists('property_action_category_agent')) : ?>
                <div class="col-md-6">
                    <p>
                        <label for="agent_action_submit"><?php esc_html_e('Action Category', 'wpresidence'); ?></label>
                        <?php
                        $agent_action_selected = '';
                        $agent_action_array = get_the_terms($agent_id, 'property_action_category_agent');
                        if (!is_wp_error($agent_action_array) && isset($agent_action_array[0])) {
                            $agent_action_selected = $agent_action_array[0]->term_id;
                        }

                        $args = array(
                            'class' => 'select-submit2',
                            'hide_empty' => false,
                            'selected' => $agent_action_selected,
                            'name' => 'agent_action_submit',
                            'id' => 'agent_action_submit',
                            'orderby' => 'NAME',
                            'order' => 'ASC',
                            'show_option_none' => esc_html__('None', 'wpresidence'),
                            'taxonomy' => 'property_action_category_agent',
                            'hierarchical' => true
                        );
                        $terms_check = get_terms(array('taxonomy' => 'property_action_category_agent', 'number' => 1, 'hide_empty' => false));
                        if (!is_wp_error($terms_check)) {
                        wp_dropdown_categories($args);
                        }
                        ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ( function_exists( 'get_field' ) ) { ?>
        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title"><?php esc_html_e('Custom Fields', 'wpresidence'); ?></div>
            <?php echo wpestate_display_acf_frontend_form($agent_id, 'estate_agent'); ?>
        </div>
        <?php } ?>

        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title"><?php esc_html_e('Agent Custom Data', 'wpresidence'); ?></div>
            <div class="add_custom_data_cont">
               

                <?php
                if (is_array($agent_custom_data) && count($agent_custom_data) > 0) {
                    foreach ($agent_custom_data as $custom_field) {
                        ?>
                        <div class="cliche_row row">
                            <div class="col-md-5">
                                <label for="agent_custom_label"><?php esc_html_e('Agent Field Name', 'wpresidence'); ?></label>
                                <input type="text" class="form-control agent_custom_label" value="<?php echo esc_attr($custom_field['label']); ?>">
                            </div>
                            <div class="col-md-5">
                                <label for="agent_custom_value"><?php esc_html_e('Agent Field Value', 'wpresidence'); ?></label>
                                <input type="text" class="form-control agent_custom_value" value="<?php echo esc_attr($custom_field['value']); ?>" name="agent_custom_value[]">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="wpresidence_button remove_parameter_button"><?php esc_html_e('Remove', 'wpresidence'); ?></button>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <button type="button" class="wpresidence_button add_custom_parameter"><?php esc_html_e('Add Custom Field', 'wpresidence'); ?></button>
            </div>
        </div>

        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title"><?php esc_html_e('Agent Location', 'wpresidence'); ?></div>
            <?php echo wpestate_dashnoard_display_form_fields($agent_location_fields, $agent_id); ?>
        </div>

        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title"><?php esc_html_e('Agent Details', 'wpresidence'); ?></div>
            <?php echo wpestate_dashnoard_display_form_fields($agent_about_details_fields, $agent_id); ?>
        </div>

        <?php if ( !wp_is_mobile() ) {  ?>
            <div class="add-estate profile-page profile-onprofile ">
                <button class="wpresidence_button" id="update_profile"><?php esc_html_e('Update profile', 'wpresidence'); ?></button>

                <?php
                $ajax_nonce = wp_create_nonce("wpestate_update_profile_nonce");
                echo '<input type="hidden" id="wpestate_update_profile_nonce" value="' . esc_attr($ajax_nonce) . '" />';

                if ($user_agent_id != 0 && get_post_status($user_agent_id) == 'publish') {
                    echo '<a href="' . esc_url(get_permalink($user_agent_id)) . '" class="wpresidence_button view_public_profile">' . esc_html__('View public profile', 'wpresidence') . '</a>';
                }
                ?>

                <button class="wpresidence_button" id="delete_profile"><?php esc_html_e('Delete profile', 'wpresidence'); ?></button>
            </div>
        <?php } ?>

    </div>
</div>

<div class="col-12 col-md-12 col-lg-4 user-profile-dashboard-wrapper">
<div class="wpestate_dashboard_content_wrapper">
<div class="add-estate profile-page profile-onprofile row">
<div class="wpestate_dashboard_section_title"><?php esc_html_e('Photo', 'wpresidence'); ?></div>
<div class="profile_div" id="profile-div">
    <img id="profile-image" src="<?php echo esc_url($user_custom_picture); ?>" alt="<?php esc_attr_e('user image', 'wpresidence'); ?>" data-profileurl="<?php echo esc_attr($user_custom_picture); ?>" data-smallprofileurl="<?php echo esc_attr($image_id); ?>" />

    <div id="upload-container">
        <div id="aaiu-upload-container">
            <button id="aaiu-uploader" type="button" class="wpresidence_button wpresidence_success"><?php esc_html_e('Upload profile image', 'wpresidence'); ?></button>
            <div id="aaiu-upload-imagelist">
                <ul id="aaiu-ul-list" class="aaiu-upload-list"></ul>
            </div>
        </div>
    </div>
    <div id="imagelist-profile"></div>
    <span class="upload_explain"><?php esc_html_e('*minimum 500px x 500px', 'wpresidence'); ?></span>
</div>
</div>
</div>
</div>


<?php if ( wp_is_mobile() ) {  ?>
    <div class="fullp-button m-0 p-3 ">
        <button class="wpresidence_button w-100" id="update_profile"><?php esc_html_e('Update profile', 'wpresidence'); ?></button>

        <?php
        $ajax_nonce = wp_create_nonce("wpestate_update_profile_nonce");
        echo '<input type="hidden" id="wpestate_update_profile_nonce" value="' . esc_attr($ajax_nonce) . '" />';

        if ($user_agent_id != 0 && get_post_status($user_agent_id) == 'publish') {
            echo '<a href="' . esc_url(get_permalink($user_agent_id)) . '" class="wpresidence_button text-center view_public_profile w-100">' . esc_html__('View public profile', 'wpresidence') . '</a>';
        }
        ?>

        <button class="wpresidence_button w-100" id="delete_profile"><?php esc_html_e('Delete profile', 'wpresidence'); ?></button>
    </div>
<?php } ?>





<div class="col-12 col-md-12 col-lg-8 change_pass_wrapper">
<div class="wpestate_dashboard_content_wrapper">
<?php get_template_part('templates/dashboard-templates/change_pass_template'); ?>
</div>
</div>