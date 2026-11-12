<?php
namespace ElementorWpResidence\Widgets;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
class Wpresidence_Term_Description extends Widget_Base {
   
    public function get_name() {
        return 'term_description';
    }
   
    public function get_title() {
        return __('Term Description', 'residence-elementor');
    }
   
    public function get_icon() {
        return 'wpresidence-note  eicon-editor-paragraph';
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
                'name' => 'description_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .wpresidence-term-description',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __('Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-term-description' => 'color: {{VALUE}};',
                ],
            ]
        );
       
        $this->end_controls_section();
    }
   
    protected function render() {
        $description = '';
        $term        = get_queried_object();
       
        if ( $term instanceof \WP_Term ) {
            $description = term_description( $term->term_id, $term->taxonomy );
        }
       
        if ( (! $description && \Elementor\Plugin::$instance->editor->is_edit_mode()) || is_singular( 'wpestate-studio' ) ) {
            $latest_terms = get_terms([
                'taxonomy'   => 'property_city',
                'hide_empty' => false,
                'number'     => 1,
                'orderby'    => 'term_id',
                'order'      => 'DESC',
            ]);
           
            if ( ! empty( $latest_terms ) && ! is_wp_error( $latest_terms ) ) {
                $term        = $latest_terms[0];
                $description = term_description( $term->term_id, $term->taxonomy );
            }
        }
       
        if ($description) {
            echo '<div class="wpresidence-term-description">' . wp_kses_post($description) . '</div>';
        } else {
            echo '<div class="wpresidence-term-description">' . esc_html__( 'This term does not have a description.', 'residence-elementor' ) . '</div>';
        }
    }
}