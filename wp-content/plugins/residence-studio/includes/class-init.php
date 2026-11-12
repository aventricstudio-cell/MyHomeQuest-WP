<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WpResidence_Elementor_Init {
    

    /**
     * Initialize the plugin
     *
     * Validates that Elementor is already loaded.
     * Checks for basic plugin requirements, if one check fail don't continue,
     * if all check have passed include the plugin class.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @since 1.2.0
     * @access public
     */
    public function init() {

        // Check if Elementor installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', array($this, 'admin_notice_missing_main_plugin'));
            return;
        }

        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, WpResidence_Elementor_Design_Studio::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', array($this, 'admin_notice_minimum_elementor_version'));
            return;
        }

        // Check for required PHP version
        if (version_compare(PHP_VERSION, WpResidence_Elementor_Design_Studio::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'admin_notice_minimum_php_version'));
            return;
        }

        // Once we get here, We have passed all validation checks so we can safely include our plugin
        require_once('plugin.php');
    }
}
