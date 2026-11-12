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

class Wpresidence_Property_Page_Schedule_Tour extends Widget_Base {

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
        return 'schedule_tour';
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
        return __('Schedule a Tour Section', 'residence-elementor');
    }
    public function get_html_wrapper_class() {
        return parent::get_html_wrapper_class() . ' wpestate_contact_form_parent';
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
        return ' wpresidence-note eicon-form-horizontal';
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
    public function elementor_transform($input) {
        $output = array();
        if (is_array($input)) {
            foreach ($input as $key => $tax) {
                $output[$tax['value']] = $tax['label'];
            }
        }
        return $output;
    }

    protected function register_controls() {
        $text_align = array('left' => 'left', 'right' => 'right', 'center' => 'center');
        $this->start_controls_section(
                'section_content',
                [
                    'label' => __('Content', 'residence-elementor'),
                ]
        );

        $this->end_controls_section();

        /* -------------------------------------------------------------------------------------------------
         * Start shadow section
         */
        $this->start_controls_section(
                'section_grid_box_shadow',
                [
                    'label' => esc_html__('Box Shadow', 'residence-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'box_shadow',
                    'label' => esc_html__('Box Shadow', 'residence-elementor'),
                    'selector' => '{{WRAPPER}} .wpestate_schedule_tour_wrapper',
                ]
        );

        $this->end_controls_section();
        /*
         * -------------------------------------------------------------------------------------------------
         * End shadow section
         */
        $this->start_controls_section(
                'section_spacing_margin_section',
                [
                    'label' => esc_html__('Spaces & Sizes', 'residence-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_responsive_control(
                'property_content_padding',
                [
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
                        '{{WRAPPER}} .wpestate_schedule_tour_wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );

        $this->add_responsive_control(
                'border_radius',
                [
                    'label' => esc_html__('Border Radius', 'residence-elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .wpestate_schedule_tour_wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
         * Start color section
         */
        $this->start_controls_section(
                'section_colors',
                [
                    'label' => esc_html__('Colors', 'residence-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_control(
                'unit_color',
                [
                    'label' => esc_html__('Background Color', 'residence-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .wpestate_agent_details_wrapper' => 'background-color: {{VALUE}}',
                        '{{WRAPPER}} .wpestate_schedule_tour_wrapper  ' => 'background-color: {{VALUE}}',
                        '{{WRAPPER}} .elementor_agent_wrapper  ' => 'background-color: {{VALUE}}',
                    ],
                ]
        );
        $this->add_control(
                'title_color',
                [
                    'label' => esc_html__(' Title Color', 'residence-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} h4' => 'color: {{VALUE}}',
                    ],
                ]
        );
        $this->add_control(
                'unit_font_color',
                [
                    'label' => esc_html__('Text Color', 'residence-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .wpestate_schedule_tour_wrapper' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .wpestate_schedule_tour_wrapper h5' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .wpestate_schedule_tour_wrapper a' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .wpestate_schedule_tour_wrapper .wpestate_day_unit_day_number' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .wpestate_schedule_tour_wrapper .wpestate_property_schedule_singledate_wrapper' => 'color: {{VALUE}}',
                      
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
    public function wpresidence_send_to_shortcode($input) {
        $output = '';
        if ($input !== '') {
            $numItems = count($input);
            $i = 0;

            foreach ($input as $key => $value) {
                $output .= $value;
                if (++$i !== $numItems) {
                    $output .= ', ';
                }
            }
        }
        return $output;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $attributes['is_elementor'] = 1;

        echo wpestate_estate_property_schedule_tour($attributes);
    }

}
