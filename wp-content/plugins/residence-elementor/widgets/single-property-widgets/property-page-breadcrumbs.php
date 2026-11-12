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

class Wpresidence_Property_Page_Breadcrumbs extends Widget_Base {

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
        return 'Property Breadcrumbs';
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
        return __('Breadcrumbs', 'residence-elementor');
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
            'name' => 'property_title',
            'label' => esc_html__('Breadcrumbs Typography', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .breadcrumb,{{WRAPPER}} .breadcrumb li',
                ]
        );

       
        $this->add_control(
            'breadcrumb_color', [
            'label' => esc_html__('Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .breadcrumb' => 'color: {{VALUE}}',
                '{{WRAPPER}} .breadcrumb a' => 'color: {{VALUE}}',
                '{{WRAPPER}} .breadcrumb > li + li:before' => 'color: {{VALUE}}',
            ],
                ]
        );


        $this->add_control(
            'breadcrumb_active_color', [
            'label' => esc_html__('Active Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .breadcrumb .active' => 'color: {{VALUE}}',
            ],
                ]
        );

         $this->add_responsive_control(
            'property_breadcrumb_margin_top', [
            'label' => esc_html__('Breadcrumbs Margin Top (px)', 'residence-elementor'),
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
                '{{WRAPPER}} .breadcrumb' => 'margin-top: {{SIZE}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
            'property_breadcrumb_margin_bottom', [
            'label' => esc_html__('Breadcrumbs Margin Bottom (px)', 'residence-elementor'),
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
                '{{WRAPPER}} .breadcrumb' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
    'breadcrumb_text_align',
    [
        'label'   => esc_html__( 'Text Alignment', 'residence-elementor' ),
        'type'    => Controls_Manager::CHOOSE,
        'options' => [
            'left' => [
                'title' => esc_html__( 'Left', 'residence-elementor' ),
                'icon'  => 'eicon-text-align-left',
            ],
            'center' => [
                'title' => esc_html__( 'Center', 'residence-elementor' ),
                'icon'  => 'eicon-text-align-center',
            ],
            'right' => [
                'title' => esc_html__( 'Right', 'residence-elementor' ),
                'icon'  => 'eicon-text-align-right',
            ],
        ],
        'default' => 'left',
        'selectors' => [
            '{{WRAPPER}} .breadcrumb_container' => 'text-align: {{VALUE}};',
        ],
    ]
);



        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(), [
            'name' => 'text_shadow',
            'label' => __('Text Shadow', 'plugin-domain'),
            'selector' => '{{WRAPPER}} .breadcrumb',
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
        echo wpestate_estate_property_page_breadcrumb_section($attributes);
    }

}
