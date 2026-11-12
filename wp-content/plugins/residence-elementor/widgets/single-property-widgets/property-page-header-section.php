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

class Wpresidence_Property_Page_Header_Section extends Widget_Base {

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
        return 'property_show_header_section';
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
        return __('Header Section', 'residence-elementor');
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
        return ' wpresidence-note eicon-header';
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
            'settings_section', [
                'label' => esc_html__('Settings', 'residence-elementor'),
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
                '{{WRAPPER}} .entry-prop' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
            ]
        );


        $this->add_responsive_control(
            'property_buttons', [
                'label' => esc_html__('Share / Print / PDF Buttons Margin Top', 'residence-elementor'),
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
                    '{{WRAPPER}} .prop_social' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'property_categories_buttons', [
                'label' => esc_html__('Categories Margin Top', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -50,
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
                    '{{WRAPPER}} .single_property_labels' => 'top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
        /*
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
            'selector' => '{{WRAPPER}} .entry-prop',
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'property_price',
            'label' => esc_html__('Property Price', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .price_area,{{WRAPPER}} .price_label',
                ]
        );

        // Add typography group control
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'property_location',
                'label' => esc_html__('Property Location', 'residence-elementor'),
               'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                'selector' => '{{WRAPPER}} .property_categs, {{WRAPPER}} .property_categs a',
            ]
        );

        $this->end_controls_section();


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

        // Existing controls
        $this->add_control(
            'title_color', [
                'label' => esc_html__('Section Title Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .entry-prop' => 'color: {{VALUE}}',
                ],
            ]
        );

        // New hover controls for title text color
        $this->add_control(
            'title_hover_color', [
                'label' => esc_html__('Section Title Hover Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .entry-prop:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'price_font_color', [
                'label' => esc_html__('Price Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .price_area' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .price_label' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'categories_font_color', [
                'label' => esc_html__('Address Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property_categs' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .property_categs a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .property_categs i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'category_label_font_color', [
                'label' => esc_html__('Category Label Font Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property_title_label' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .property_title_label a' => 'color: {{VALUE}}',
                ],
            ]
        );

        // New hover controls for category label font color
        $this->add_control(
            'category_label_hover_font_color', [
                'label' => esc_html__('Category Label Hover Font Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property_title_label:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .property_title_label a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'category_label_back_color', [
                'label' => esc_html__('Category Label Back Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property_title_label' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        // New hover controls for category label background color
        $this->add_control(
            'category_label_hover_back_color', [
                'label' => esc_html__('Category Label Hover Back Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property_title_label:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        // New controls for .single_property_action
        $this->add_control(
            'single_property_action_color', [
                'label' => esc_html__('Share / Print / Favorite Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .single_property_action' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'single_property_action_hover_color', [
                'label' => esc_html__('Share / Print / Favorite Hover Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .single_property_action:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'single_property_action_background_color', [
                'label' => esc_html__('Share / Print / Favorite Back Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .single_property_action' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'single_property_action_hover_background_color', [
                'label' => esc_html__('Share / Print / Favorite Hover Back Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .single_property_action:hover' => 'background-color: {{VALUE}}',
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
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $attributes['is_elementor_edit'] = 1;
        }


        wpestate_estate_property_page_header_section($attributes);
    }

}
