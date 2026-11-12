<?php
/** MILLDONE
 * WpResidence Theme - User Profile Template
 * src: page-templates\user_profile.php
 * This template handles the display and functionality of the user profile page
 * in the WpResidence theme, including contact information, social media links,
 * and profile picture management.
 *
 * @package WpResidence
 * @subpackage UserProfile
 * @since 1.0.0
 */

// Retrieve current user information
$current_user           = wp_get_current_user();
$userID                 = $current_user->ID;
$user_login             = $current_user->user_login;
$description            = get_the_author_meta('description', $userID);
$agent_custom_data      = get_the_author_meta('agent_custom_data', $userID);
$user_custom_picture    = get_the_author_meta('custom_picture', $userID);
$user_small_picture     = get_the_author_meta('small_custom_picture', $userID);
$image_id               = get_the_author_meta('small_custom_picture', $userID);
$about_me               = get_the_author_meta('description', $userID);

// Set default user picture if not available
if (empty($user_custom_picture)) {
    $user_custom_picture = get_theme_file_uri('/img/default_user.png');
}

// Define form fields for contact information
$contact_information_fields = array(
    'firstname'  => array('label' => esc_html__('First Name', 'wpresidence'), 'meta' => 'first_name', 'type' => 'user_meta'),
    'secondname' => array('label' => esc_html__('Last Name', 'wpresidence'), 'meta' => 'last_name', 'type' => 'user_meta'),
    'useremail'  => array('label' => esc_html__('Email', 'wpresidence'), 'meta' => 'user_email', 'type' => 'user_meta'),
    'userphone'  => array('label' => esc_html__('Phone', 'wpresidence'), 'meta' => 'phone', 'type' => 'user_meta'),
    'usermobile' => array('label' => esc_html__('Mobile', 'wpresidence'), 'meta' => 'mobile', 'type' => 'user_meta'),
    'userskype'  => array('label' => esc_html__('Skype', 'wpresidence'), 'meta' => 'skype', 'type' => 'user_meta'),
);

// Define form fields for social media information
$social_media_information_fields = array(
    'userfacebook'  => array('label' => esc_html__('Facebook Url', 'wpresidence'), 'meta' => 'facebook', 'type' => 'user_meta'),
    'usertwitter'   => array('label' => esc_html__('X - Twitter Url', 'wpresidence'), 'meta' => 'twitter', 'type' => 'user_meta'),
    'userlinkedin'  => array('label' => esc_html__('Linkedin Url', 'wpresidence'), 'meta' => 'linkedin', 'type' => 'user_meta'),
    'userinstagram' => array('label' => esc_html__('Instagram Url', 'wpresidence'), 'meta' => 'instagram', 'type' => 'user_meta'),
    'userpinterest' => array('label' => esc_html__('Pinterest Url', 'wpresidence'), 'meta' => 'pinterest', 'type' => 'user_meta'),
    'website'       => array('label' => esc_html__('Website Url (without http)', 'wpresidence'), 'meta' => 'website', 'type' => 'user_meta'),
);

// Define form fields for user details
$user_details_information_fields = array(
    'usertitle' => array('label' => esc_html__('Title/Position', 'wpresidence'), 'meta' => 'title', 'type' => 'user_meta'),
);

?>

<div class=" col-12 col-md-12 col-lg-8 user_profile_div">
    <div class="wpestate_dashboard_content_wrapper">
        <div id="profile_message"></div>

        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title"><?php esc_html_e('Contact Information', 'wpresidence'); ?></div>
            <?php echo wpestate_dashnoard_display_form_fields($contact_information_fields, $userID); ?>
            <?php wp_nonce_field('profile_ajax_nonce', 'security-profile'); ?>
        </div>

        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title"><?php esc_html_e('Social Media', 'wpresidence'); ?></div>
            <?php echo wpestate_dashnoard_display_form_fields($social_media_information_fields, $userID); ?>
        </div>

        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title"><?php esc_html_e('User Details', 'wpresidence'); ?></div>
            <?php echo wpestate_dashnoard_display_form_fields($user_details_information_fields, $userID); ?>
            
            <?php if ( !wp_is_mobile() ) {  ?>
                <p class="fullp-button">
                    <button class="wpresidence_button" id="update_profile"><?php esc_html_e('Update profile', 'wpresidence'); ?></button>
                    <button class="wpresidence_button" id="delete_profile"><?php esc_html_e('Delete profile', 'wpresidence'); ?></button>
                    <?php
                    $ajax_nonce = wp_create_nonce("wpestate_update_profile_nonce");
                    echo '<input type="hidden" id="wpestate_update_profile_nonce" value="' . esc_attr($ajax_nonce) . '" />';
                    ?>
                </p>
            <?php 
                } 
            ?>
        </div>
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

<?php   
    if ( wp_is_mobile() ) { 
?>
    <p class="fullp-button m-0 p-3 ">
        <button class="wpresidence_button w-100" id="update_profile"><?php esc_html_e('Update profile', 'wpresidence'); ?></button>
        <button class="wpresidence_button w-100" id="delete_profile"><?php esc_html_e('Delete profile', 'wpresidence'); ?></button>
        <?php
            $ajax_nonce = wp_create_nonce("wpestate_update_profile_nonce");
            echo '<input type="hidden" id="wpestate_update_profile_nonce" value="' . esc_attr($ajax_nonce) . '" />';
        ?>
    </p>
<?php
    } 
?>

<div class="col-12 col-md-12 col-lg-8 change_pass_wrapper">
    <div class="wpestate_dashboard_content_wrapper">
        <?php get_template_part('templates/dashboard-templates/change_pass_template'); ?>
    </div>
</div>