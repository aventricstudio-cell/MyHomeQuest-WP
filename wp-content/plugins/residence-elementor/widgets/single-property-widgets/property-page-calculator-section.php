<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Property_Page_Calculator_Section extends Widget_Base {

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
        return 'property_show_calculator_section';
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
        return __(' Calculator Section', 'residence-elementor');
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
                'overview_content', [
            'label' => __('Content', 'wpresidence-core'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );


        $this->add_control(
                'hide_section_title', [
            'label' => esc_html__('Hide Section Title', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'residence-elementor'),
            'label_off' => esc_html__('No', 'residence-elementor'),
            'return_value' => 'none',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  .panel-title' => 'display: {{VALUE}};',
                '{{WRAPPER}}  .panel-heading' => 'padding-bottom:0px;',
            ],
                ]
        );

        $this->add_control(
                'section_title', [
            'label' => esc_html__('Section Title', 'wpresidence-core'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'description' => '',
                ]
        );



        $this->end_controls_section();


        /* -------------------------------------------------------------------------------------------------
         * Start shadow section
         */
        $this->start_controls_section(
            'section_grid_box_shadow', [
                'label' => esc_html__('Box Shadow', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(), [
                'name' => 'box_shadow',
                'label' => esc_html__('Box Shadow', 'residence-elementor'),
                'selector' => '{{WRAPPER}} #accordion_morgage',
            ]
        );

        $this->end_controls_section();
        /*
         * -------------------------------------------------------------------------------------------------
         * End shadow section
         */
        $this->start_controls_section(
            'section_spacing_margin_section', [
                'label' => esc_html__('Spaces & Sizes', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'property_title_margin_bottom', [
                'label' => esc_html__('Title Margin Bottom (px)', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .panel-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'property_content_padding', [
                'label' => esc_html__('Content Area Padding', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '30',      // Default top padding
                    'right' => '30',    // Default right padding
                    'bottom' => '30',   // Default bottom padding
                    'left' => '30',     // Default left padding
                ],
                'selectors' => [
                    '{{WRAPPER}} #accordion_morgage' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .property-panel .panel-heading' => 'padding: 0',
                    '{{WRAPPER}} .property-panel .panel-body' => 'padding: 0',
                ],
            ]
        );

        $this->add_responsive_control(
            'border_radius', [
                'label' => esc_html__('Border Radius', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} #accordion_morgage' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
        /*
         * -------------------------------------------------------------------------------------------------
         * End shadow section
         */
        /*
         * -------------------------------------------------------------------------------------------------
         * Start typography section
         */

        $this->start_controls_section(
            'typography_section', [
                'label' => esc_html__('Typography', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'property_title',
                'label' => esc_html__('Property Title', 'residence-elementor'),
               'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                'selector' => '{{WRAPPER}} .panel-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'property_content',
                'label' => esc_html__('Content', 'residence-elementor'),
               'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                'selector' => '{{WRAPPER}} .panel-default,{{WRAPPER}} .panel-default label',
            ]
        );

        $this->end_controls_section();
        /*
         * -------------------------------------------------------------------------------------------------
         * End typography section
         */
        /*

        /*
        * -------------------------------------------------------------------------------------------------
        * Start color section
        */
        $this->start_controls_section(
            'section_colors', [
                'label' => esc_html__('Colors', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'unit_color', [
                'label' => esc_html__('Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} #accordion_morgage' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .panel-heading ' => 'background-color: transparent',
                ],
            ]
        );

        $this->add_control(
            'unit_font_color', [
                'label' => esc_html__('Text Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .panel-default' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .panel-default a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .panel-default .morgage_chart_wrapper label' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .panel-default .morgage_data_wrapper label' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .morgage_chart_wrapper.onfirst #canvas-holder .morg_momth_pay .morg_month_wrap' => 'color: {{VALUE}}',          
                ],
            ]
        );

        $this->add_control(
            'title_color', [
                'label' => esc_html__('Section Title Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .panel-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    /*
    * -------------------------------------------------------------------------------------------------
    * End color section
    */
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
        echo property_page_calculator_section_function($attributes, $settings);
    }

}
