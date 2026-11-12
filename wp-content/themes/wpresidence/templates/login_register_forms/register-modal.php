
<div class="login_form wpestate_register_form_wrapper" >



    <?php
    // Bail early if site-wide registration is disabled.
    if ( 'no' === wpresidence_get_option( 'wp_estate_allow_user_registration', 'yes' ) ) {
        esc_html_e('Registration is disabled by Administrator','wpresidence');
       
    }else{
    ?>


    <div class="login_register_div_title"><?php esc_html_e('Create an account', 'wpresidence'); ?></div>
    <div class="loginalert wpestate_register_message_area"></div>
    
    <input type="text" name="user_login_register"  class="form-control wpestate_register_form_usenmame" placeholder="<?php esc_attr_e('Username', 'wpresidence'); ?>"/>
    <input type="email" name="user_email_register" class="form-control wpestate_register_form_email" placeholder="<?php esc_attr_e('Email', 'wpresidence'); ?>" />

    <?php
    $enable_user_pass_status = esc_html(wpresidence_get_option('wp_estate_enable_user_pass', ''));
    if ($enable_user_pass_status === 'yes') :
    ?>
        <div class="password_holder">
            <input type="password" name="user_password"  class="form-control wpestate_register_form_password" placeholder="<?php esc_attr_e('Password', 'wpresidence'); ?>"/>
            <i class="far fa-eye-slash show_hide_password"></i>
        </div>
        <div class="password_holder">
            <input type="password" name="user_password_retype"  class="form-control wpestate_register_form_password_retype" placeholder="<?php esc_attr_e('Retype Password', 'wpresidence'); ?>"/>
            <i class="far fa-eye-slash show_hide_password"></i>
        </div>
    <?php endif; ?>

    <?php
    // User type selection
    $user_types         = function_exists('wpresidence_rolemap') ? wpresidence_rolemap() : array();
    $permited_roles     = wpresidence_get_option('wp_estate_visible_user_role', '');
    $visible_user_role_dropdown = wpresidence_get_option('wp_estate_visible_user_role_dropdown', '');
    
    if ($visible_user_role_dropdown === 'yes' && is_array($permited_roles)) :
    ?>
        <select  name="new_user_type_topbar" class="form-select wpestate_register_form_user_type">
            <option value="0"><?php esc_html_e('Select User Type', 'wpresidence'); ?></option>
            <?php
            foreach ($user_types as $key => $name) :
                if (in_array($key, $permited_roles)) :
            ?>
                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($name); ?></option>
            <?php
                endif;
            endforeach;
            ?>
        </select>
    <?php endif;?>

    <div class="wpestate_register_form_agree_terms_label_wrapper">
        <input type="checkbox" name="terms" id="user_terms_register_topbar_<?php echo esc_attr($type2); ?>" class="wpestate_register_form_agree_terms " />
        <label class="wpestate_register_form_agree_terms_label" for="user_terms_register_topbar_<?php echo esc_attr($type2); ?>">
            <?php
            esc_html_e('I agree with ', 'wpresidence');
            $terms_link = wpestate_get_template_link('page-templates/terms_conditions.php');
            printf('<a href="%s" target="_blank" class="wpestate_register_form_agree_terms_link">%s</a>', esc_url($terms_link), esc_html__('terms & conditions', 'wpresidence'));
            ?>
        </label>
    </div>

    <?php
    if (wpresidence_get_option('wp_estate_use_captcha', '') === 'yes') :
    ?>
        <div class="wpestate_register_form_captcha" style="float:left;transform:scale(0.75);-webkit-transform:scale(0.75);transform-origin:0 0;-webkit-transform-origin:0 0;"></div>
    <?php
    endif;

    if ($enable_user_pass_status !== 'yes') :
    ?>
        <p class=wpestate_register_form_request_password"><?php esc_html_e('A password will be e-mailed to you', 'wpresidence'); ?></p>
    <?php
    endif;
    ?>

    <input type="hidden" class="wpestate_register_form_security" name="security-register-topbar" value="<?php echo wp_create_nonce('register_ajax_nonce_topbar'); ?>">
    <button class="wpresidence_button wpestate_register_submit_button"><?php esc_html_e('Register', 'wpresidence'); ?></button>

    <?php 
    }
    ?>
</div>