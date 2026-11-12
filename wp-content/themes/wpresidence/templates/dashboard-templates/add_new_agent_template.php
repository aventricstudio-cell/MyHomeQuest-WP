<?php
/** MILLDONE
 * Agent Profile Edit/Add Template
 * src: templates\dashboard-templates\add_new_agent_template.php
 * This template handles both adding new agents and editing existing agent profiles
 * in the WpResidence theme's dashboard. It includes sections for basic information,
 * social media, location, and custom fields.
 *
 * @package WpResidence
 * @subpackage Dashboard
 * @since WpResidence 1.0
 * 
 * Dependencies:
 * - WordPress User functions
 * - WpResidence theme functions
 * - Bootstrap grid system
 */

$current_user = wp_get_current_user();
$userID = $current_user->ID;
$user_login = $current_user->user_login;
$agent_id = get_the_author_meta('user_agent_id', $userID);
$user_custom_picture = '';
$user_custom_picture_meta = get_the_author_meta('custom_picture', $userID);
$user_custom_picture_meta = wp_get_attachment_image_src(get_post_thumbnail_id($listing_edit), 'user_picture_profile');
if (isset($user_custom_picture_meta[0])) {
    $user_custom_picture = $user_custom_picture_meta[0];
}
if ($user_custom_picture == '') {
    $user_custom_picture = get_theme_file_uri('/img/default_user.png');
}

$agent_information_fields = array(
    'agent_password' => array(
        'label' => esc_html__('Agent Password', 'wpresidence'),
        'meta' => '',
        'type' => 'user_meta',
    ),
    'agent_repassword' => array(
        'label' => esc_html__('Re-type Password', 'wpresidence'),
        'meta' => '',
        'type' => 'user_meta',
    ),
    'firstname' => array(
        'label' => esc_html__('First Name', 'wpresidence'),
        'meta_name' => 'first_name',
        'type' => 'post_meta',
    ),
    'secondname' => array(
        'label' => esc_html__('Last Name', 'wpresidence'),
        'meta_name' => 'last_name',
        'type' => 'post_meta',
    ),
    'userphone' => array(
        'label' => esc_html__('Phone', 'wpresidence'),
        'meta_name' => 'agent_phone',
        'type' => 'post_meta',
    ),
    'usermobile' => array(
        'label' => esc_html__('Mobile', 'wpresidence'),
        'meta_name' => 'agent_mobile',
        'type' => 'post_meta',
    ),
    'useremail' => array(
        'label' => esc_html__('Email', 'wpresidence'),
        'meta_name' => 'agent_email',
        'type' => 'post_meta',
    ),
    'userskype' => array(
        'label' => esc_html__('Skype', 'wpresidence'),
        'meta_name' => 'agent_skype',
        'type' => 'post_meta',
    ),
    'agent_member' => array(
        'label' => esc_html__('Member of', 'wpresidence'),
        'meta_name' => 'agent_member',
        'type' => 'post_meta',
    ),
    'agent_address' => array(
        'label' => esc_html__('Address', 'wpresidence'),
        'meta_name' => 'agent_address',
        'type' => 'post_meta',
    ),
);

$agent_social_fields = array(
    'userfacebook' => array(
        'label' => esc_html__('Facebook Url', 'wpresidence'),
        'meta_name' => 'agent_facebook',
        'type' => 'post_meta',
    ),
    'userinstagram' => array(
        'label' => esc_html__('Instagram Url', 'wpresidence'),
        'meta_name' => 'agent_instagram',
        'type' => 'post_meta',
    ),
    'usertwitter' => array(
        'label' => esc_html__('X - Twitter Url', 'wpresidence'),
        'meta_name' => 'agent_twitter',
        'type' => 'post_meta',
    ),
    'userpinterest' => array(
        'label' => esc_html__('Pinterest Url', 'wpresidence'),
        'meta_name' => 'agent_pinterest',
        'type' => 'post_meta',
    ),
    'userlinkedin' => array(
        'label' => esc_html__('Linkedin Url', 'wpresidence'),
        'meta_name' => 'agent_linkedin',
        'type' => 'post_meta',
    ),
    'useryoutube' => array(
        'label' => esc_html__('Youtube Url', 'wpresidence'),
        'meta_name' => 'agent_youtube',
        'type' => 'post_meta',
    ),
    'usertiktok' => array(
        'label' => esc_html__('TikTok Url', 'wpresidence'),
        'meta_name' => 'agent_tiktok',
        'type' => 'post_meta',
    ),
    'usertelegram' => array(
        'label' => esc_html__('Telegram Url', 'wpresidence'),
        'meta_name' => 'agent_telegram',
        'type' => 'post_meta',
    ),
    'uservimeo' => array(
        'label' => esc_html__('Vimeo Url', 'wpresidence'),
        'meta_name' => 'agent_vimeo',
        'type' => 'post_meta',
    ),
    'website' => array(
        'label' => esc_html__('Website Url (without http)', 'wpresidence'),
        'meta_name' => 'agent_website',
        'type' => 'post_meta',
    ),
);

$agent_location_fields = array(
    'agent_city' => array(
        'label' => esc_html__('City', 'wpresidence'),
        'meta' => 'last_name',
        'type' => 'taxonomy_name',
        'tax_name' => 'property_city_agent'
    ),
    'agent_county' => array(
        'label' => esc_html__('State/County', 'wpresidence'),
        'meta' => 'last_name',
        'type' => 'taxonomy_name',
        'tax_name' => 'property_county_state_agent'
    ),
    'agent_area' => array(
        'label' => esc_html__('Area', 'wpresidence'),
        'meta' => 'last_name',
        'type' => 'taxonomy_name',
        'tax_name' => 'property_area_agent'
    ),
);

$agent_about_details_fields = array(
    'usertitle' => array(
        'label' => esc_html__('Title/Position', 'wpresidence'),
        'meta_name' => 'agent_position',
        'type' => 'post_meta',
        'bootstral_length' => 12,
    ),
    'about_me' => array(
        'label' => esc_html__('About Me', 'wpresidence'),
        'meta' => 'about_me',
        'inputype' => 'textarea',
        'type' => 'wpestate_description',
        'bootstral_length' => 12,
    ),
    'agent_private_notes' => array(
        'label' => esc_html__('Private Notes', 'wpresidence'),
        'inputype' => 'textarea',
        'meta_name' => 'agent_private_notes',
        'type' => 'post_meta',
    ),
);

$agent_thumb = get_theme_file_uri('/img/default_user.png');
$agent_category_selected = '';
$agent_action_selected = '';
$agent_city = '';
$agent_area = '';
$agent_county = '';
$agent_custom_data = '';
$user_to_edit = '';

if ($listing_edit != 0) {
    $user_to_edit = get_post_meta($listing_edit, 'user_meda_id', true);
    $user_for_agent = get_user_by('ID', $user_to_edit);
    $agent_first_name = get_post_meta($listing_edit, 'first_name', true);
    $agent_last_name = get_post_meta($listing_edit, 'last_name', true);
    $agent_phone = get_post_meta($listing_edit, 'agent_phone', true);
    $agent_skype = get_post_meta($listing_edit, 'agent_skype', true);
    $agent_posit = get_post_meta($listing_edit, 'agent_position', true);
    $agent_mobile = get_post_meta($listing_edit, 'agent_mobile', true);
    $agent_custom_data = get_post_meta($listing_edit, 'agent_custom_data', true);
    $agent_email = get_post_meta($listing_edit, 'agent_email', true);
    $agent_facebook = get_post_meta($listing_edit, 'agent_facebook', true);
    $agent_twitter = get_post_meta($listing_edit, 'agent_twitter', true);
    $agent_linkedin = get_post_meta($listing_edit, 'agent_linkedin', true);
    $agent_pinterest = get_post_meta($listing_edit, 'agent_pinterest', true);
    $agent_instagram = get_post_meta($listing_edit, 'agent_instagram', true);
    $agent_description = get_post_field('post_content', $listing_edit);
    $agent_website = get_post_meta($listing_edit, 'agent_website', true);
    $agent_member = get_post_meta($listing_edit, 'agent_member', true);
    $agent_address = get_post_meta($listing_edit, 'agent_address', true);
    $agent_youtube = get_post_meta($listing_edit, 'agent_youtube', true);
    $agent_tiktok = get_post_meta($listing_edit, 'agent_tiktok', true);
    $agent_telegram = get_post_meta($listing_edit, 'agent_telegram', true);
    $agent_vimeo = get_post_meta($listing_edit, 'agent_vimeo', true);
    $agent_private_notes = get_post_meta($listing_edit, 'agent_private_notes', true);

    if (taxonomy_exists('property_category_agent')) {
        $agent_category_array = get_the_terms($listing_edit, 'property_category_agent');
        if (!is_wp_error($agent_category_array) && isset($agent_category_array[0])) {
            $agent_category_selected = $agent_category_array[0]->term_id;
        }
    }

    if (taxonomy_exists('property_action_category_agent')) {
        $agent_action_array = get_the_terms($listing_edit, 'property_action_category_agent');
        if (!is_wp_error($agent_action_array) && isset($agent_action_array[0])) {
            $agent_action_selected = $agent_action_array[0]->term_id;
        }
    }

    if (taxonomy_exists('property_city_agent')) {
        $agent_city_array = get_the_terms($listing_edit, 'property_city_agent');
        if (!is_wp_error($agent_city_array) && isset($agent_city_array[0])) {
            $agent_city = $agent_city_array[0]->name;
        }
    }

    if (taxonomy_exists('property_area_agent')) {
        $agent_area_array = get_the_terms($listing_edit, 'property_area_agent');
        if (!is_wp_error($agent_area_array) && isset($agent_area_array[0])) {
            $agent_area = $agent_area_array[0]->name;
        }
    }

    if (taxonomy_exists('property_county_state_agent')) {
        $agent_county_array = get_the_terms($listing_edit, 'property_county_state_agent');
        if (!is_wp_error($agent_county_array) && isset($agent_county_array[0])) {
            $agent_county = $agent_county_array[0]->name;
        }
    }

    $agent_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($listing_edit), 'property_listings');
    if (isset($agent_thumb[0])) {
        $agent_thumb = $agent_thumb[0];
    }
    if ($agent_thumb == '') {
        $agent_thumb = get_theme_file_uri('/img/default_user.png');
    }
}
$user_agent_id = intval(get_user_meta($userID, 'user_agent_id', true));
?>

<div class=" col-12 col-md-12 col-lg-8 user_profile_div">
    <div class="wpestate_dashboard_content_wrapper">
        <div id="profile_message"></div>
        
        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title">
                <?php esc_html_e('Agent Details', 'wpresidence'); ?>
            </div>
            <div class="col-md-12">
                <?php if ($is_edit != 1): ?>
                    <label for="agent_username"><?php esc_html_e('Agent Username', 'wpresidence'); ?></label>
                    <input type="text" id="agent_username" class="form-control" value="" name="agent_username">
                <?php else: ?>
                    <p style="height:50px;">
                        <?php printf(
                            esc_html__('Username: %s is not editable', 'wpresidence'),
                            esc_html($user_for_agent->user_login)
                        ); ?>
                    </p>
                <?php endif; ?>
            </div>
            <?php echo wpestate_dashnoard_display_form_fields($agent_information_fields, $listing_edit); ?>
        </div>

        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title">
                <?php esc_html_e('Agent Social Details', 'wpresidence'); ?>
            </div>
            <?php echo wpestate_dashnoard_display_form_fields($agent_social_fields, $listing_edit); ?>
        </div>

            <?php if (taxonomy_exists('property_category_agent') || taxonomy_exists('property_action_category_agent')) : ?>
                <div class="add-estate profile-page profile-onprofile row">
                    <div class="wpestate_dashboard_section_title">
                        <?php esc_html_e('Agent Area/Categories', 'wpresidence'); ?>
                    </div>
                    <?php if (taxonomy_exists('property_category_agent')) : ?>
                        <div class="col-md-6">
                            <p>
                                <label for="agent_category_submit">
                                    <?php esc_html_e('Category', 'wpresidence'); ?>
                                </label>
                                <?php
                                $terms_check = get_terms(array('taxonomy' => 'property_category_agent', 'number' => 1, 'hide_empty' => false));
                                if (!is_wp_error($terms_check)) {
                                wp_dropdown_categories(array(
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
                                ));
                                }
                                ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    <?php if (taxonomy_exists('property_action_category_agent')) : ?>
                        <div class="col-md-6">
                            <p>
                                <label for="agent_action_submit">
                                    <?php esc_html_e('Action Category', 'wpresidence'); ?>
                                </label>
                                <?php
                                $terms_check = get_terms(array('taxonomy' => 'property_action_category_agent', 'number' => 1, 'hide_empty' => false));
                                if (!is_wp_error($terms_check)) {
                                wp_dropdown_categories(array(
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
                                ));
                                }
                                ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title">
                <?php esc_html_e('Agent Custom Data', 'wpresidence'); ?>
            </div>
            <div class="add_custom_data_cont">
            <?php
            if ( function_exists( 'get_field' ) )   {
                echo wpestate_display_acf_frontend_form( $listing_edit, 'estate_agent' ); 
            }
            ?>
                <div class="cliche_row row">
                    <div class="col-md-5">
                        <label for="agent_custom_label"><?php esc_html_e('Agent Field Name', 'wpresidence'); ?></label>
                        <input type="text" class="form-control agent_custom_label" value="" name="agent_custom_label[]">
                    </div>
                    <div class="col-md-5">
                        <label for="agent_custom_value"><?php esc_html_e('Agent Field Value', 'wpresidence'); ?></label>
                        <input type="text" class="form-control agent_custom_value" value="" name="agent_custom_value[]">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="wpresidence_button remove_parameter_button">
                            <?php esc_html_e('Remove', 'wpresidence'); ?>
                        </button>
                    </div>
                </div>

                <?php
                if (is_array($agent_custom_data) && count($agent_custom_data) > 0) {
                    for ($i = 0; $i < count($agent_custom_data); $i++) {
                        ?>
                        <div class="single_parameter_row">
                            <div class="col-md-5">
                                <label for="agent_custom_label"><?php esc_html_e('Agent Field Name', 'wpresidence'); ?></label>
                                <input type="text" class="form-control agent_custom_label" 
                                       value="<?php echo esc_attr($agent_custom_data[$i]['label']); ?>">
                            </div>
                            <div class="col-md-5">
                                <label for="agent_custom_value"><?php esc_html_e('Agent Field Value', 'wpresidence'); ?></label>
                                <input type="text" class="form-control agent_custom_value" 
                                       value="<?php echo esc_attr($agent_custom_data[$i]['value']); ?>" 
                                       name="agent_custom_value[]">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="wpresidence_button remove_parameter_button">
                                    <?php esc_html_e('Remove', 'wpresidence'); ?>
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <button type="button" class="wpresidence_button add_custom_parameter">
                    <?php esc_html_e('Add Custom Field', 'wpresidence'); ?>
                </button>
            </div>
        </div>

        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title">
                <?php esc_html_e('Agent Location', 'wpresidence'); ?>
            </div>
            <?php echo wpestate_dashnoard_display_form_fields($agent_location_fields, $listing_edit); ?>
        </div>

        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title">
                <?php esc_html_e('Agent Details', 'wpresidence'); ?>
            </div>
            <?php echo wpestate_dashnoard_display_form_fields($agent_about_details_fields, $listing_edit); ?>
        </div>

        <?php   
            if ( !wp_is_mobile() ) { 
        ?>
        <div class="add-estate profile-page profile-onprofile ">
            <button class="wpresidence_button" id="register_agent">
                <?php
                if ($is_edit != 1) {
                    esc_html_e('Add New Agent', 'wpresidence');
                } else {
                    esc_html_e('Edit Agent', 'wpresidence');
                }
                $ajax_nonce = wp_create_nonce("wpestate_register_agent_nonce");
                print '<input type="hidden" id="wpestate_register_agent_nonce" value="' . esc_attr($ajax_nonce) . '">';
                ?>
            </button>

            <input type="hidden" id="is_agent_edit" value="<?php echo esc_attr($is_edit); ?>">
            <input type="hidden" id="user_id" value="<?php echo esc_attr($user_to_edit); ?>">
            <input type="hidden" id="agent_id" value="<?php echo esc_attr($listing_edit); ?>">
        </div>
        <?php
            } 
        ?>
    </div>
</div>

<div class="col-12 col-md-12 col-lg-4  user-profile-dashboard-wrapper">
    <div class="wpestate_dashboard_content_wrapper">
        <div class="add-estate profile-page profile-onprofile row">
            <div class="wpestate_dashboard_section_title">
                <?php esc_html_e('Photo', 'wpresidence'); ?>
            </div>
            <div class="profile_div" id="profile-div">
                <img id="profile-image" 
                     src="<?php echo esc_url($user_custom_picture); ?>" 
                     alt="<?php esc_attr_e('user image', 'wpresidence'); ?>"
                     data-profileurl="<?php echo esc_attr($user_custom_picture); ?>"
                     data-smallprofileurl="">

                <div id="upload-container">
                    <div id="aaiu-upload-container">
                        <button type="button" id="aaiu-uploader" class="wpresidence_button wpresidence_success">
                            <?php esc_html_e('Upload profile image.', 'wpresidence'); ?>
                        </button>
                        <div id="aaiu-upload-imagelist">
                            <ul id="aaiu-ul-list" class="aaiu-upload-list"></ul>
                        </div>
                    </div>
                </div>
                <span class="upload_explain">
                    <?php esc_html_e('*minimum 500px x 500px', 'wpresidence'); ?>
                </span>
            </div>
        </div>
    </div>
</div>


<?php   
if ( wp_is_mobile() ) {  
?>
    <div class="fullp-button m-0 p-3 ">
        <button class="wpresidence_button w-100" id="register_agent">
            <?php
            if ($is_edit != 1) {
                esc_html_e('Add New Agent', 'wpresidence');
            } else {
                esc_html_e('Edit Agent', 'wpresidence');
            }
            $ajax_nonce = wp_create_nonce("wpestate_register_agent_nonce");
            print '<input type="hidden" id="wpestate_register_agent_nonce" value="' . esc_attr($ajax_nonce) . '">';
            ?>
        </button>

        <input type="hidden" id="is_agent_edit" value="<?php echo esc_attr($is_edit); ?>">
        <input type="hidden" id="user_id" value="<?php echo esc_attr($user_to_edit); ?>">
        <input type="hidden" id="agent_id" value="<?php echo esc_attr($listing_edit); ?>">
    </div>

<?php
} 
?>