<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WpResidence_Elementor_Admin_Notices {

    /**
     * Admin notice for missing main plugin
     *
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_missing_main_plugin() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'residence-elementor'),
            '<strong>' . esc_html__('WpResidence Elementor', 'residence-elementor') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'residence-elementor') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice for minimum Elementor version
     *
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_minimum_elementor_version() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'residence-elementor'),
            '<strong>' . esc_html__('WpResidence Elementor ', 'residence-elementor') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'residence-elementor') . '</strong>',
            WpResidence_Elementor_Design_Studio::MINIMUM_ELEMENTOR_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice for minimum PHP version
     *
     * @since 1.0.0
     * @access public
     */
    public function admin_notice_minimum_php_version() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'residence-elementor'),
            '<strong>' . esc_html__('WpResidence Elementor', 'residence-elementor') . '</strong>',
            '<strong>' . esc_html__('PHP', 'residence-elementor') . '</strong>',
            WpResidence_Elementor_Design_Studio::MINIMUM_PHP_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
}
