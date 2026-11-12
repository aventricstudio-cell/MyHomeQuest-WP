<div class="login_form wpestate_login_form_wrapper">
    <div class="login_register_div_title"><?php esc_html_e('Sign into your account', 'wpresidence'); ?></div>
    <div class="loginalert login_register_message_area" ></div>
    <input type="text" class="form-control wpestate_login_form_username" name="log"  placeholder="<?php esc_attr_e('Username', 'wpresidence'); ?>"/>

    <div class="password_holder">
        <input type="password" class="form-control wpestate_login_form_password " name="pwd" placeholder="<?php esc_attr_e('Password', 'wpresidence'); ?>"/>
        <i class="far fa-eye-slash show_hide_password"></i>
    </div>
    <input type="hidden" name="loginpop" class="loginpop" value="0">
    <input type="hidden" class="wpestate_login_form_security" name="security-login-topbar" 
            value="<?php echo wp_create_nonce('login_ajax_nonce_topbar'); ?>">
    <button class="wpresidence_button wpestate_login_submit_button" ><?php esc_html_e('Login', 'wpresidence'); ?></button>
    <?php 
    $nonce = wp_create_nonce( 'wpestate_social_login_nonce' );
    ?>
    <input type="hidden" class="wpestate_social_login_nonce" value="<?php echo esc_attr($nonce);?>">

    
    <?php
  
    if($context!='modal'):
    ?>
        <div class="login-links">
            <a href="#" class="wpestate_login_form_switch_register"><?php esc_html_e('Need an account? Register here!', 'wpresidence'); ?></a>
            <a href="#" class="wpestate_login_form_switch_forgot "><?php esc_html_e('Forgot Password?', 'wpresidence'); ?></a>
        </div>
    <?php 
    endif;
    ?>
    

    <?php
    if (class_exists('Wpestate_Social_Login')) {
        global $wpestate_social_login;
        echo $wpestate_social_login->display_form('topbar', 0);
    }
    ?>
</div>