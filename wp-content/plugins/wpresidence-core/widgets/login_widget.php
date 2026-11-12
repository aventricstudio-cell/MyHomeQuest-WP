<?php
/**
 * WpResidence Login Widget
 *
 * This file contains the Login_Widget class, which implements a custom login and register widget
 * for the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage Widgets
 * @since 1.0.0
 *
 * Dependencies:
 * - WordPress core (wp-includes/widget.php)
 * - WpEstate_Custom_Auth class
 *
 * Usage:
 * This widget can be added to sidebars in the WordPress admin panel under Appearance > Widgets.
 * It provides a login form for non-logged-in users and a user menu for logged-in users.
 */

class Login_Widget extends WP_Widget {
    /**
     * Constructor for the Login_Widget class.
     *
     * Sets up the widget's name, description, and other options.
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'loginwd_sidebar boxed_widget',
            'description' => esc_html__('Put the login & register form on sidebar', 'wpresidence-core')
        );
        $control_ops = array('id_base' => 'login_widget');
        
        parent::__construct('login_widget', esc_html__('Wp Estate: Login & Register', 'wpresidence-core'), $widget_ops, $control_ops);
    }
   
    /**
     * Back-end widget form.
     *
     * @param array $instance Previously saved values from database.
     * @return void
     */
    public function form($instance) {
        // This widget doesn't have any options, so we leave this empty
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        return $old_instance; // No options to update
    }

    /**
     * Front-end display of the widget.
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     * @return void
     */
    public function widget($args, $instance) {
        extract($args);
        
        echo wp_kses_post($before_widget);

        $current_user = wp_get_current_user();

        if (is_user_logged_in()) {
            $this->display_logged_in_user($current_user);
        } else {
            $this->display_login_form();
        }

        echo wp_kses_post($after_widget);
    }

    /**
     * Display the login form for non-logged-in users.
     *
     * @return void
     */
    private function display_login_form() {
        echo '<div class="login_sidebar">';
        $wpestate_custom_auth = WpEstate_Custom_Auth::get_instance();
        echo $wpestate_custom_auth->display_auth_form('modal', 'all');
        echo '</div>';
    }

    /**
     * Display the user menu for logged-in users.
     *
     * @param WP_User $current_user The current logged-in user object.
     * @return void
     */
    private function display_logged_in_user($current_user) {
        $user_login = $current_user->user_login;
        $unread_messages = $this->get_unread_messages_count($current_user->ID);

        echo '<h3 class="widget-title-sidebar">' . sprintf(esc_html__('Hello %s', 'wpresidence-core'), esc_html($user_login)) . '</h3>';
        echo '<ul class="wd_user_menu">';
        ob_start();
        wpestate_generate_user_menu();
        $menu = ob_get_clean();
        echo wp_kses_post($menu);
        echo '</ul>';

      
    }

    /**
     * Get the number of unread messages for a user.
     *
     * @param int $user_id The ID of the user.
     * @return int The number of unread messages.
     */
    private function get_unread_messages_count($user_id) {
        return intval(get_user_meta($user_id, 'unread_mess', true));
    }
}