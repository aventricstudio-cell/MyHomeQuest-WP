<div class="login_form wpestate_forgot_form_wrapper ">
    <div class="login_register_div_title"><?php esc_html_e('Reset Password', 'wpresidence'); ?></div>
    <div class="loginalert login_register_message_area" ></div>

    <input type="email" class="form-control wpestate_forgot_form_email " name="forgot_email" placeholder="<?php esc_attr_e('Enter Your Email Address', 'wpresidence'); ?>" size="20" />
  
    <?php wp_nonce_field('forgot_ajax_nonce-topbar', 'security-forgot-topbar'); ?>


    <input type="hidden" class="wpestate_forgot_form_security" name="security-login-topbar" 
            value="<?php echo wp_create_nonce('forgot_ajax_nonce_topbar'); ?>">

    
    <input type="hidden" class="wpestate_auth_postid" value="<?php echo get_the_ID(); ?>">
    <button class="wpresidence_button wpestate_forgot_password_submit_button" name="forgot"><?php esc_html_e('Reset Password', 'wpresidence'); ?></button>
</div>