<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Wpresidence_Term_Map extends Widget_Base {
    
    public function get_name() {
        return 'term_maps';
    }
    
    public function get_title() {
        return __('Term Map', 'residence-elementor');
    }
    
    public function get_icon() {
        return 'wpresidence-note eicon-google-maps';
    }
    
    public function get_categories() {
        return ['category_widgets'];
    }
    
    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $this->add_control(
            'show_poi_controls',
            [
                'label' => esc_html__('Show POI Controls', 'residence-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'residence-elementor'),
                'label_off' => esc_html__('Hide', 'residence-elementor'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
        
        $this->end_controls_section();
        
        // Style Section
        $this->start_controls_section(
            'map_style_section',
            [
                'label' => esc_html__('Map Style', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
            'map_height',
            [
                'label' => esc_html__('Map Height', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 800,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 400,
                ],
                'selectors' => [
                    '{{WRAPPER}} .googleMap_term_shortcode_class' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .google_map_shortcode_wrapper' => 'height: {{SIZE}}{{UNIT}};',
                       
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'map_border',
                'label' => esc_html__('Border', 'residence-elementor'),
                'selector' => '{{WRAPPER}} .googleMap_term_shortcode_class',
            ]
        );
        
        $this->add_responsive_control(
            'map_border_radius',
            [
                'label' => esc_html__('Border Radius', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .googleMap_term_shortcode_class' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'map_box_shadow',
                'label' => esc_html__('Box Shadow', 'residence-elementor'),
                'selector' => '{{WRAPPER}} .googleMap_term_shortcode_class',
            ]
        );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $title = single_term_title('', false);

        if (  ( !$title && \Elementor\Plugin::$instance->editor->is_edit_mode()) || is_singular( 'wpestate-studio' ) ) {

            $latest_terms = get_terms([
                'taxonomy'   => 'property_city',
                'hide_empty' => false,
                'number'     => 1,
                'orderby'    => 'term_id',
                'order'      => 'DESC',
            ]);
            if ( ! empty( $latest_terms ) && ! is_wp_error( $latest_terms ) ) {
                $title = $latest_terms[0]->name;
            }
        }
        
        // Add CSS to hide POI controls if toggle is off
        if ($settings['show_poi_controls'] !== 'yes') {
            echo '<style>.google_map_shortcode_wrapper .wpestate_poi_wrapper { display: none !important; }</style>';
        }

         
        if (  \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            print '<div class="wpresidence_map_placholder googleMap_term_shortcode_class">'.esc_html__('The map will be loaded on live page','wpresidence-elementor').'</div>';
        }else{
            echo do_shortcode('[term_page_map]');
        }


        
     
    }
}