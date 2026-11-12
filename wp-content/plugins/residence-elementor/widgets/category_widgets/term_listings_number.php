<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH'))
    exit;

class Wpresidence_Term_Property_Count extends Widget_Base {

    public function get_name() {
        return 'term-property-count';
    }

    public function get_categories() {
        return ['category_widgets'];
    }

    public function get_title() {
        return __('Term Property Count', 'residence-elementor');
    }

    public function get_icon() {
        return 'wpresidence-note eicon-counter';
    }

    public function get_script_depends() {
        return [];
    }

    public function get_keywords() {
        return ['property', 'count', 'term', 'listing'];
    }

    protected function get_default_settings() {
        return [
            'count_label' => 'listings',
        ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'count_label',
            [
                'label' => esc_html__('Label', 'residence-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('listings', 'residence-elementor'),
                'placeholder' => esc_html__('Enter label text', 'residence-elementor'),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Style', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'count_typography',
                'label' => esc_html__('Count Typography', 'residence-elementor'),
                'selector' => '{{WRAPPER}} .property-count-number',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'label' => esc_html__('Label Typography', 'residence-elementor'),
                'selector' => '{{WRAPPER}} .property-count-label',
            ]
        );

        $this->add_control(
            'count_color',
            [
                'label' => esc_html__('Count Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .property-count-number' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'label_color',
            [
                'label' => esc_html__('Label Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .property-count-label' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_align',
            [
                'label' => esc_html__('Alignment', 'residence-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'residence-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'residence-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'residence-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .property-count-container' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'spacing',
            [
                'label' => esc_html__('Spacing Between Count and Label', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'size' => 5,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .property-count-number' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $current_term = null;
        $count = 0;
        
        if (is_tax()) {
            $current_term = get_queried_object();
        } elseif (\Elementor\Plugin::$instance->editor->is_edit_mode() || is_singular( 'wpestate-studio' ) ) {
            $latest_terms = get_terms([
                'taxonomy'   => 'property_city',
                'hide_empty' => false,
                'number'     => 1,
                'orderby'    => 'term_id',
                'order'      => 'DESC',
            ]);
           
            if (!empty($latest_terms) && !is_wp_error($latest_terms)) {
                $current_term = $latest_terms[0];
            }
        }
        
        if ($current_term) {
            $args = [
                'post_type' => 'estate_property',
                'post_status' => 'publish',
                'posts_per_page' => 1,
                'tax_query' => [
                    [
                        'taxonomy' => $current_term->taxonomy,
                        'field'    => 'term_id',
                        'terms'    => $current_term->term_id,
                    ],
                ],
                'fields' => 'ids',
                'no_found_rows' => false
            ];
            
            $query = new \WP_Query($args);
            $count = $query->found_posts;
        }
        
        $label = !empty($settings['count_label']) ? $settings['count_label'] : 'listings';
        
        ?>
        <div class="property-count-container">
            <span class="property-count-number"><?php echo esc_html($count); ?></span>
            <span class="property-count-label"><?php echo esc_html($label); ?></span>
        </div>
        <?php
    }

    protected function content_template() {
        ?>
        <div class="property-count-container">
            <span class="property-count-number">0</span>
            <span class="property-count-label">{{{ settings.count_label }}}</span>
        </div>
        <?php
    }
}