<?php
namespace ElementorWpResidence\Widgets;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
class Wpresidence_Term_Title extends Widget_Base {
   
    public function get_name() {
        return 'term_title';
    }
   
    public function get_title() {
        return __('Term Title', 'residence-elementor');
    }
   
    public function get_icon() {
        return 'wpresidence-note  eicon-editor-h1';
    }
   
    public function get_categories() {
        return ['category_widgets'];
    }
   
    protected function register_controls() {
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
                'name' => 'title_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
                'selector' => '{{WRAPPER}} .wpresidence-term-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-term-title' => 'color: {{VALUE}};',
                ],
            ]
        );
       
        $this->end_controls_section();
    }
   
    protected function render() {
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
       
        if ( $title ) {
            echo '<h1 class="wpresidence-term-title">' . esc_html( $title ) . '</h1>';
        } else  {
            echo '<div class="wpresidence-term-title">' . esc_html__( 'This term does not have a title.', 'residence-elementor' ) . '</div>';
        }
    }
}