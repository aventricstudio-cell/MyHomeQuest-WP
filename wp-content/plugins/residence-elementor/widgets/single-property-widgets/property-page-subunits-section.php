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

class Wpresidence_Property_Page_Subunits_Section extends Widget_Base {

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
        return 'property_show_subunits_section';
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
        return __('Subunits Section', 'residence-elementor');
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
                    '{{WRAPPER}} .accordion-button' => 'display: {{VALUE}};',
                  
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

        $this->add_control(
                'section_title2', [
            'label' => esc_html__('Section Title- Child', 'wpresidence-core'),
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
            'selector' => '{{WRAPPER}} #accordion_property_multi_units',
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
                    'size' => 15,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 15,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 15,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .accordion-item .accordion-header .accordion-button' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} #accordion_property_multi_units' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .accordion-item .accordion-body' => 'padding: 0px;',
                    '{{WRAPPER}} .accordion-item .accordion-header .accordion-button' => 'padding: 0px;',
                ],
            ]
        );

        $this->add_responsive_control(
                'border_radius', [
            'label' => esc_html__('Border Radius', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} #accordion_property_multi_units' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
            'label' => esc_html__('Section Title', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .accordion-item .accordion-header .accordion-button',
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'property_c_title',
            'label' => esc_html__('Property Title', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .subunit_title a',
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'property_c_price',
            'label' => esc_html__('Property Price', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .subunit_price,{{WRAPPER}} .subunit_title .price_label',
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'property_c_meta',
            'label' => esc_html__('Property Meta', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .subunit_size,{{WRAPPER}} .subunit_bathrooms,{{WRAPPER}} .subunit_rooms,{{WRAPPER}} .subunit_type,{{WRAPPER}} .subunit_type a',
                ]
        );

        $this->end_controls_section();
        /*
         * -------------------------------------------------------------------------------------------------
         * End shadow section
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
                '{{WRAPPER}} #accordion_property_multi_units' => 'background-color: {{VALUE}}',
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

        $this->add_control(
            'unit_back_color_1', [
                'label' => esc_html__('Unit Background Color - 1st element', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .multi_units_wrapper_v2 .subunit_wrapper' => 'background-color: {{VALUE}}',
                ],
            ]
    );

        $this->add_control(
            'unit_back_color', [
                'label' => esc_html__('Unit Background Color - 2nd element', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .subunit_wrapper:nth-child(even)' => 'background-color: {{VALUE}}',
                ],
            ]
        );


        $this->add_control(
            'unit_title_color', [
                'label' => esc_html__('Subunit Title Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .multi_units_wrapper_v2 .subunit_wrapper .subunit_details .subunit_title a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'unit_title_hover_color', [
                'label' => esc_html__('Subunit Title Hover Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .multi_units_wrapper_v2 .subunit_wrapper .subunit_details .subunit_title a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'unit_price_color', [
                'label' => esc_html__('Subunit Price Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .multi_units_wrapper_v2 .subunit_wrapper .subunit_details .subunit_title .subunit_price' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .price_label' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'unit_meta_color', [
                'label' => esc_html__('Subunit Meta Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .subunit_details' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .subunit_details a' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->end_controls_section();
        /*
         * -------------------------------------------------------------------------------------------------
         * End shadow section
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
        $attributes['section_title']=$settings['section_title'];
        $attributes['section_title2']=$settings['section_title2'];
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $attributes['is_elementor_edit'] = 1;
        }
         property_page_subunits_section_function($attributes, $settings);

        
    }

}
