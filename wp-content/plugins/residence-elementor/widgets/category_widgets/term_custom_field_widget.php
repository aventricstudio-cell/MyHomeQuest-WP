<?php
namespace ElementorWpResidence\Widgets;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Wpestate_Term_Custom_Field_Widget extends Widget_Base {
    /**
     * Widget identifier.
     *
     * @return string
     */
    public function get_name() {
        return 'wpestate_term_custom_field';
    }
    
    /**
     * Widget title displayed in Elementor.
     *
     * @return string
     */
    public function get_title() {
        return esc_html__( 'Term Custom Field', 'residence-elementor' );
    }
    
    /**
     * Widget icon.
     *
     * @return string
     */
    public function get_icon() {
        return 'wpresidence-note  eicon-post-list';
    }
    
    /**
     * Widget categories.
     *
     * @return array
     */
    public function get_categories() {
        return ['category_widgets'];
    }
    
    /**
     * Register widget controls.
     */
    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__( 'Content', 'residence-elementor' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $fields  = get_option( 'wpestate_custom_fields_for_terms', array() );
        $options = array();
        foreach ( $fields as $slug => $field ) {
            $name             = isset( $field['name'] ) ? $field['name'] : $slug;
            $options[ $slug ] = $name;
        }
        
        $this->add_control(
            'field_slug',
            [
                'label'   => esc_html__( 'Custom Field', 'residence-elementor' ),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => $options,
            ]
        );
        
        $this->end_controls_section();
        
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'custom_field_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .wpresidence-term-custom-field',
            ]
        );
        
        $this->end_controls_section();
    }
    
    /**
     * Render widget output.
     */
    protected function render() {
        $settings   = $this->get_settings_for_display();
        $field_slug = isset( $settings['field_slug'] ) ? $settings['field_slug'] : '';
        $term = get_queried_object();
        
        if ( (! ( $term instanceof \WP_Term ) && \Elementor\Plugin::$instance->editor->is_edit_mode()) || is_singular( 'wpestate-studio' ) ) {
            $latest_terms = get_terms([
                'taxonomy'   => 'property_city',
                'hide_empty' => false,
                'number'     => 1,
                'orderby'    => 'term_id',
                'order'      => 'DESC',
            ]);
            if ( ! empty( $latest_terms ) ) {
                $term = $latest_terms[0];
            }
        }
        $value = '';
        if ( $term instanceof \WP_Term && $field_slug ) {
            $meta  = get_option( 'taxonomy_' . $term->term_id, array() );
            $value = isset( $meta[ $field_slug ] ) ? $meta[ $field_slug ] : '';
        }
        
        if ( ('' === $value && \Elementor\Plugin::$instance->editor->is_edit_mode()) || ('' === $value && is_singular( 'wpestate-studio' )) ) {
            echo '<div class="wpresidence-term-custom-field">' . esc_html__( 'This term does not have value for the selected custom field.', 'residence-elementor' ) . '</div>';
        } else {


      // Use the global wp_embed object for embed shortcodes
        global $wp_embed;
        $processed_value = wpautop( $value );
        $processed_value = $wp_embed->run_shortcode( $processed_value );

        // Remove inline CSS and JavaScript that shouldn't be in content
        $processed_value = preg_replace('/\s*\.[\w\-\s,:;!()#\.\[\]]+\{\s*[^}]*\}\s*/', '', $processed_value);
        $processed_value = preg_replace('/\s*jQuery\([^;]*;\s*/', '', $processed_value);
        $processed_value = preg_replace('/\s*\}\);\s*/', '', $processed_value);

        // Clean up extra whitespace
        $processed_value = preg_replace('/\n\s*\n/', "\n\n", $processed_value);

        // Allow iframe and embed-related HTML tags
        $allowed_html = wp_kses_allowed_html('post');
        $allowed_html['iframe'] = array(
            'src' => array(),
            'width' => array(),
            'height' => array(),
            'frameborder' => array(),
            'allowfullscreen' => array(),
            'title' => array(),
            'loading' => array(),
            'sandbox' => array(),
        );

        echo '<div class="wpresidence-term-custom-field">' . wp_kses($processed_value, $allowed_html) . '</div>';
        }
    }
}