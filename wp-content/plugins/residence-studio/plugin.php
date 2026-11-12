<?php

namespace ElementorStudioWidgetsWpResidence;

/**
 * Class Plugin
 *
 * Main Plugin class
 * @since 1.2.0
 */
class Plugin {

    /**
     * Instance
     *
     * @since 1.2.0
     * @access private
     * @static
     *
     * @var Plugin The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.2.0
     * @access public
     *
     * @return Plugin An instance of the class.
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * widget_scripts
     *
     * Load required plugin core files.
     *
     * @since 1.2.0
     * @access public
     */
    public function widget_scripts() {
        
    }

    /**
     * Include Widgets files
     *
     * Load widgets files
     *
     * @since 1.2.0
     * @access private
     */
    private function include_widgets_files() {

        
        // require_once( __DIR__ . '/widgets/site-logo.php' );
        // require_once( __DIR__ . '/widgets/site-navigation.php' );
        // require_once( __DIR__ . '/widgets/site-login.php' );
        // require_once( __DIR__ . '/widgets/site-create-listing.php' );
        // require_once( __DIR__ . '/widgets/site-currency-changer.php' );
        // require_once( __DIR__ . '/widgets/site-measurement-unit-changer.php' );
        // require_once( __DIR__ . '/widgets/site-social.php' );
        // require_once( __DIR__ . '/widgets/site-phone.php' );     
        // require_once( __DIR__ . '/widgets/site-language.php' );


        // require_once( __DIR__ . '/widgets/single-post/single-post-title.php' );
    }

    /**
     * Register Widgets
     *
     * Register new Elementor widgets.
     *
     * @since 1.2.0
     * @access public
     */
    public function register_widgets() {
        // Its is now safe to include Widgets files
        $this->include_widgets_files();
        
        
        // Register Widgets
        // \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Logo());
        // \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Navigation_Menu());
        // \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Login());
        // \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Create_Listing());
        // \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Currency_Changer());
        // \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Measurement_Unit_Changer());
        // \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Social());
        // \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Phone());       
        // \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Site_Language());



        // single post
        // \Elementor\Plugin::instance()->widgets_manager->register(new Widgets\Wpresidence_Single_Post_Title());
        
        
    }

    /**
     *  Plugin class constructor
     *
     * Register plugin action hooks and filters
     *
     * @since 1.2.0
     * @access public
     */
    public function add_elementor_widget_categories($elements_manager) {
      
     
        
        $elements_manager->add_category(
            'wpresidence_single_post', [
            'title' => __('WpResidence Single Post Widgets', 'residence-elementor'),
            'icon' => 'fa fa-home',
            ]
        );
    }

    public function __construct() {

        // Register widget scripts
        add_action('elementor/frontend/after_register_scripts', [$this, 'widget_scripts']);

        // Register widgets
        add_action('elementor/widgets/register', [$this, 'register_widgets']);

        add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);
    }

}

// Instantiate Plugin Class
Plugin::instance();

