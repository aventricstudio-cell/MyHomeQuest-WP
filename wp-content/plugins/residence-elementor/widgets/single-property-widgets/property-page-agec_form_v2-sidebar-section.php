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

class Wpresidence_Property_Agent_Form2_Section extends Widget_Base {

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
        return 'property_show_agent_form2_section';
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
        return __(' Agent Form v2 (for sidebar) Section', 'residence-elementor');
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
                'hide_picture', [
            'label' => esc_html__('Hide Picture & Name area', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'residence-elementor'),
            'label_off' => esc_html__('No', 'residence-elementor'),
            'return_value' => 'none',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  .agent_unit_widget_sidebar' => 'display: {{VALUE}};',
                '{{WRAPPER}}  h4' => 'display: {{VALUE}};',
                '{{WRAPPER}}  .agent_position' => 'display: {{VALUE}};',
            ],
                ]
        );


        $this->add_control(
                'hide_section_call_but', [
            'label' => esc_html__('Hide Call Button', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'residence-elementor'),
            'label_off' => esc_html__('No', 'residence-elementor'),
            'return_value' => 'none',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  .realtor_call' => 'display: {{VALUE}};',
            ],
                ]
        );
        $this->add_control(
                'hide_section_whats_but', [
            'label' => esc_html__('Hide WhatsApp Button', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'residence-elementor'),
            'label_off' => esc_html__('No', 'residence-elementor'),
            'return_value' => 'none',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  .realtor_whatsapp' => 'display: {{VALUE}};',
            ],
                ]
        );

        $this->add_control(
            'hide_schedule_showing', [
                'label' => esc_html__('Hide Schedule Showing Button', 'residence-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'residence-elementor'),
                'label_off' => esc_html__('No', 'residence-elementor'),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .schedule_meeting' => 'display: {{VALUE}};',
                ],
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
            'selector' => '{{WRAPPER}} .elementor_agent_wrapper',
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
                '{{WRAPPER}} .agent_contanct_form_sidebar.widget-container.wpestate_contact_form_parent' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .wpestate_schedule_tour_wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',  
            ],
                ]
        );

        $this->add_responsive_control(
            'border_radius', [
                'label' => esc_html__('Border Radius', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor_agent_wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'label' => esc_html__('Agent Name', 'residence-elementor'),
               'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                'selector' => '{{WRAPPER}} .agent_contanct_form_sidebar  h4 a, {{WRAPPER}} h4',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'agent_Details',
                'label' => esc_html__('Text Details', 'residence-elementor'),
               'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                'selector' => '{{WRAPPER}} .agent_position',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'form_font',
                'label' => esc_html__('Form Font', 'residence-elementor'),
               'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                'selector' => '{{WRAPPER}} .form-control, {{WRAPPER}} .form-select', // Combine selectors as a single string
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
                    '{{WRAPPER}} .wpestate_schedule_tour_wrapper' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .agent_contanct_form' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .elementor_agent_wrapper' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        $this->add_control(
                'title_color', [
            'label' => esc_html__('Agent Name Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .agent_contanct_form_sidebar  h4 a' => 'color: {{VALUE}}',
                '{{WRAPPER}} .agent_contanct_form_sidebar  h4' => 'color: {{VALUE}}',
            ],
                ]
        );

        $this->add_control(
            'title_hover_color', [
                'label' => esc_html__('Agent Name Hover Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .agent_contanct_form_sidebar  h4 a:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .agent_contanct_form_sidebar  h4:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'unit_font_color', [
                'label' => esc_html__('Text Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .agent_contanct_form_sidebar' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .agent_contanct_form_sidebar a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .agent_position' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'unit_font_form_color', [
                'label' => esc_html__('Form Font Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .form-control' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .form-select' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .form-control::placeholder' => 'color: {{VALUE}}', // Apply to placeholder as well
                ],
            ]
        );

        $this->add_control(
            'second_tab_background_color', [
                'label' => esc_html__('2nd Tab Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '#wpestate_sidebar_property_contact_tabs > ul li button' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'second_tab_font_color', [
                'label' => esc_html__('2nd Tab Font Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '#wpestate_sidebar_property_contact_tabs > ul li button' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_active_bg_color', [
                'label' => esc_html__('Active Tab Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '#wpestate_sidebar_property_contact_tabs > ul li button.active' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_color', [
                'label' => esc_html__('Active Font Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '#wpestate_sidebar_property_contact_tabs > ul li button.active' => 'color: {{VALUE}}',
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
        echo property_page_agent_form_v2_section_function($attributes, $settings);
    }

}
