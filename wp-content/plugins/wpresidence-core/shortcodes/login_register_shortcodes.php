<?php 


///////////////////////////////////////////////////////////////////////////////////////////
// login form  function
///////////////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_login_form_function')):

    function wpestate_login_form_function($attributes, $content = null) {
        
        $attributes = shortcode_atts(
                array(
                    'register_label' => '',
                    'register_url' => '',
                ), $attributes);

        $wpestate_custom_auth = WpEstate_Custom_Auth::get_instance();
        $return_string= $wpestate_custom_auth->display_auth_form('modal', 'all');

        return $return_string;
    }

endif; // end   wpestate_login_form_function




///////////////////////////////////////////////////////////////////////////////////////////
// register form  function
///////////////////////////////////////////////////////////////////////////////////////////

if (!function_exists('wpestate_register_form_function')):

    function wpestate_register_form_function($attributes, $content = null) {
        $wpestate_custom_auth = WpEstate_Custom_Auth::get_instance();
        $return_string= $wpestate_custom_auth->display_auth_form('modal', 'all');

        return $return_string;
    }

endif; // end   wpestate_register_form_function

