<div class="login_form wpestate_register_form_wrapper" >
    <div class="login_register_div_title"><?php esc_html_e('Create an account', 'wpresidence'); ?></div>
    <div class="loginalert wpestate_register_message_area createAccount"></div>

    <input type="text" name="user_login_register"  class="form-control wpestate_register_form_usenmame" placeholder="<?php esc_attr_e('Name', 'wpresidence'); ?>"/>
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
    
        // Fetch the visibility setting for user role dropdown
        $visible_user_role_dropdown = wpresidence_get_option('wp_estate_visible_user_role_dropdown', '');

        if ($visible_user_role_dropdown === 'yes') :
    ?>
        <select name="new_user_type_topbar" class="form-select wpestate_register_form_user_type">
            <option value="0"><?php esc_html_e('Select User Type', 'wpresidence'); ?></option>
            <!-- <option value="1"><?php esc_html_e('User', 'wpresidence'); ?></option> -->
            <option value="2"><?php esc_html_e('Agent , Property Owner, Estate Agent', 'wpresidence'); ?></option>
            <option value="3"><?php esc_html_e('Interior Designer, Architect, Surveyor', 'wpresidence'); ?></option>
            <!-- <option value="4"><?php esc_html_e('Interior Design', 'wpresidence'); ?></option> -->
        </select>
    <?php endif; ?>


    <div class="wpestate_register_form_agree_terms_label_wrapper">
        <input type="checkbox" name="terms" class="wpestate_register_form_agree_terms " />
        <label class="wpestate_register_form_agree_terms_label" for="user_terms_register_topbar">
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
        <p class="wpestate_register_form_request_password"><?php esc_html_e('A password will be e-mailed to you', 'wpresidence'); ?></p>
    <?php
    endif;
    ?>

    <input type="hidden" class="wpestate_register_form_security" name="security-register-topbar" value="<?php echo wp_create_nonce('register_ajax_nonce_topbar'); ?>">
    <button class="wpresidence_button wpestate_register_submit_button"><?php esc_html_e('Register', 'wpresidence'); ?></button>
</div>
