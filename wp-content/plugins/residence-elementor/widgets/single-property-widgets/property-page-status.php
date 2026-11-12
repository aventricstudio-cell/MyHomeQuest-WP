<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Property_Page_Status extends Widget_Base {

    /**
     * Retrieve the widget name.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'property_show_status';
    }

    public function get_categories() {
        return ['wpresidence_property'];
    }

    /**
     * Retrieve the widget title.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __('Status Label', 'residence-elementor');
    }

    /**
     * Retrieve the widget icon.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return ' wpresidence-note eicon-post-title';
    }

    /**
     * Retrieve the list of scripts the widget depended on.
     *
     * Used to set scripts dependencies required to run the widget.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return array Widget scripts dependencies.
     */
    public function get_script_depends() {
        return [''];
    }

    /**
     * Register the widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     *
     * @access protected
     */
    protected function register_controls() {



        $this->start_controls_section(
                'typography_section', [
            'label' => esc_html__('Settings', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'item_typoggraphy',
            'label' => esc_html__('Item Typography', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .status-wrapper .ribbon-inside',
                ]
        );




        $this->add_control(
                'item_color', [
            'label' => esc_html__('Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .status-wrapper .ribbon-wrapper-default .ribbon-inside ' => 'color: {{VALUE}}',
            ],
                ]
        );

        $this->add_control(
            'item_hover_color', [
                'label' => esc_html__('Hover Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .status-wrapper .ribbon-wrapper-default .ribbon-inside:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'item_background_color', [
                'label' => esc_html__('Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .status-wrapper .ribbon-wrapper-default  .ribbon-inside' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'item_hover_background_color', [
                'label' => esc_html__('Hover Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .status-wrapper .ribbon-wrapper-default .ribbon-inside:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );


        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'box_shadow',
            'label' => esc_html__('Button Shadow', 'residence-elementor'),
            'selector' => '{{WRAPPER}} .status-wrapper .ribbon-wrapper-default',
                ]
        );


        $this->add_control(
                'margin', [
            'label' => __('Margin', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .status-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->add_control(
                'padding', [
            'label' => __('Padding', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .status-wrapper .ribbon-wrapper-default .ribbon-inside' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
            'status_radius', [
                'label' => __('Radius', 'elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .status-wrapper .ribbon-wrapper-default' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: auto;', // Add overflow property here
                ],
            ]
        );

        $this->add_responsive_control(
            'text_align', [
                'label' => __('Alignment', 'wpresidence-core'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'selectors' => [
                    '{{WRAPPER}} .status-wrapper' => 'justify-content: {{VALUE}};',
                ],
                'options' => [
                    'flex-start' => [
                        'title' => __('Left', 'wpresidence-core'),
                        'icon' => 'eicon-text-align-left', // Updated icon class
                    ],
                    'center' => [
                        'title' => __('Center', 'wpresidence-core'),
                        'icon' => 'eicon-text-align-center', // Updated icon class
                    ],
                    'flex-end' => [
                        'title' => __('Right', 'wpresidence-core'),
                        'icon' => 'eicon-text-align-right', // Updated icon class
                    ],
                ],
                'default' => 'left',
                'toggle' => true,
            ]
        );


        $this->end_controls_section();
    }

    /**
     * Render the widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     *
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $attributes['is_elementor'] = 1;
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $attributes['is_elementor_edit'] = 1;
        }
        echo wpestate_estate_property_page_status_section($attributes);
    }

}
