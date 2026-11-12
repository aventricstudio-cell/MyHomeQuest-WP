<?php
namespace ElementorWpResidence\Widgets;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
class Wpresidence_Term_Parent extends Widget_Base {
   
    public function get_name() {
        return 'term_parent';
    }
   
    public function get_title() {
        return __('Term Parent', 'residence-elementor');
    }
   
    public function get_icon() {
        return 'wpresidence-note  eicon-upload-circle-o';
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
                'name' => 'parent_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .wpresidence-term-parent',
            ]
        );

        $this->add_control(
            'parent_color',
            [
                'label' => __('Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-term-parent' => 'color: {{VALUE}};',
                ],
            ]
        );
       
        $this->end_controls_section();
    }
   
    protected function render() {
        $parent_name = '';
        $term        = get_queried_object();
       
        if ( $term instanceof \WP_Term && $term->parent ) {
            $parent_term = get_term( $term->parent, $term->taxonomy );
            if ( $parent_term && ! is_wp_error( $parent_term ) ) {
                $parent_name = $parent_term->name;
            }
        }
      
      
        if (  ( !$parent_name && \Elementor\Plugin::$instance->editor->is_edit_mode()) || is_singular( 'wpestate-studio' ) ) {

            $latest_terms = get_terms([
                'taxonomy'   => 'property_city',
                'hide_empty' => false,
                'number'     => 1,
                'orderby'    => 'term_id',
                'order'      => 'DESC',
            ]);
           
            if ( ! empty( $latest_terms ) && ! is_wp_error( $latest_terms ) ) {
                $term = $latest_terms[0];
                if ( $term instanceof \WP_Term && $term->parent ) {
                    $parent_term = get_term( $term->parent, $term->taxonomy );
                    if ( $parent_term && ! is_wp_error( $parent_term ) ) {
                        $parent_name = $parent_term->name;
                    }
                }
            }
        }
       
        if ($parent_name) {
            echo '<div class="wpresidence-term-parent">' . esc_html($parent_name) . '</div>';
        } else {
            echo '<div class="wpresidence-term-parent">' . esc_html__( 'This term does not have a parent.', 'residence-elementor' ) . '</div>';
        }
    }
}