<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WpResidence_Render_Template {
    public $version = '1.0.0';
    public static $_instance;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        add_action( 'wpestate_header_studio', [ $this, 'render_header' ], 10 );
        add_action( 'wpestate_before_header', [ $this, 'render_before_header' ], 10 );
        add_action( 'wpestate_after_header', [ $this, 'render_after_header' ], 10 );

        add_action( 'wpestate_footer_studio', [ $this, 'render_footer' ], 10 );
        add_action( 'wpestate_before_footer', [ $this, 'render_before_footer' ], 10 );
        add_action( 'wpestate_after_footer', [ $this, 'render_after_footer' ], 10 );
    }

    public function render_header() {
        wpestate_render_header();
    }

    public function render_before_header() {
        wpestate_render_before_header();
    }

    public function render_after_header() {
        wpestate_render_after_header();
    }

    public function render_footer() {
        wpestate_render_footer();
    }

    public function render_before_footer() {
        wpestate_render_before_footer();
    }

    public function render_after_footer() {
        wpestate_render_after_footer();
    }

    public function fetch_plugin_settings( $setting = '', $default = '' ) {
        $template_id = $this->fetch_template_id( $setting );
     
        return apply_filters( "wpestate_fetch_plugin_settings_{$setting}", $template_id, $default );
    }

    public static function fetch_template_id( $type ) {
        global $wpestate_studio;

        if ( empty( $wpestate_studio->header_footer_instance->header_footer_templates ) ) {
          
            return '';
        }

        foreach ( $wpestate_studio->header_footer_instance->header_footer_templates as $id => $template_type ) {
            if ( $template_type === $type ) {
               
                return $id;
            }
        }

      
        return '';
    }

    public static function get_elementor_template( $id = null ) {
        $id = ! empty( $id ) ? intval( $id ) : 0;
        if ( ! $id ) {
            return '';
        }

        $post = get_post( $id );

      //  if ( did_action( 'elementor/loaded' ) ) {
       //     return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id );
        //}


        if ( did_action( 'elementor/loaded' ) && get_post_meta( $id, '_elementor_edit_mode', true ) === 'builder' ) {
            return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id );
        } elseif ( get_post_meta( $id, '_wpb_vc_js_status', true ) === 'true' ) {
            setup_postdata( $post );
            ob_start();
            the_content();
            wp_reset_postdata();
            return ob_get_clean();
        } else {
            return apply_filters( 'the_content', $post->post_content );
        }


        
    }
}

WpResidence_Render_Template::instance();
