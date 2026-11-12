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

class Wpresidence_Property_Page_Similar_Section extends Widget_Base {

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
        return 'property_show_similar_section';
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
        return __('Similar Listings Section', 'residence-elementor');
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
                    '{{WRAPPER}}  .agent_listings_title_similar' => 'display: {{VALUE}};',
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
            'post_number', [
                'label' => __('No of items', 'residence-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => 2,
            ]
        );

        $this->add_control(
            'post_number_per_row', [
                'label' => __('No of items per row (2 or 3)', 'residence-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => 2,
            ]
        );

        $unit_options = array(
            '0' => __('default', 'wpresidence-core'),
            '1' => __('type 1', 'wpresidence-core'),
            '2' => __('type 2', 'wpresidence-core'),
            '3' => __('type 3', 'wpresidence-core'),
            '4' => __('type 4', 'wpresidence-core'),
            '5' => __('type 5', 'wpresidence-core'),
            '6' => __('type 6', 'wpresidence-core'),
            '7' => __('type 7', 'wpresidence-core'),
            '8' => __('type 8', 'wpresidence-core'),
        );
        $this->add_control(
                'unit_type', [
            'label' => __('Card unit type?', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 0,
            'options' => $unit_options
                ]
        );


        $this->end_controls_section();



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
                    '{{WRAPPER}} .agent_listings_title_similar' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'property_content_padding', [
                'label' => esc_html__('Content Area Padding', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} #property_similar_listings' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'border_radius', [
                'label' => esc_html__('Content Area Border Radius', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} #property_similar_listings' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'selector' => '{{WRAPPER}} .agent_listings_title_similar',
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
                '{{WRAPPER}} #property_similar_listings' => 'background-color: {{VALUE}}',
            ],
                ]
        );

        $this->add_control(
                'title_color', [
            'label' => esc_html__('Section Title Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .agent_listings_title_similar' => 'color: {{VALUE}}',
            ],
                ]
        );
        $this->end_controls_section();
        
        $this->start_controls_section(

                'section_grid_box_shadow',
                [
                    'label' => esc_html__( 'Box Shadow', 'residence-elementor' ),
                    'tab'   => Controls_Manager::TAB_STYLE,
                ]
                );
                $this->add_group_control(
                    Group_Control_Box_Shadow::get_type(),
                    [
                        'name'     => 'box_shadow',
                        'label'    => esc_html__( 'Box Shadow', 'residence-elementor' ),
                        'selector' => '{{WRAPPER}} .elementor-widget-container .listing_wrapper .property_listing',
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
        $attributes['is_elementor']         = 1;
        $attributes['post_number']          = $settings['post_number'];
        $attributes['card_version']            = $settings['unit_type'];
        $attributes['post_number_per_row']  = $settings['post_number_per_row'];
        $attributes['section_title']        = $settings['section_title'];

        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $attributes['is_elementor_edit'] = 1;
        }
        echo property_page_similar_listings_section_function($attributes, $settings);
    }

}
