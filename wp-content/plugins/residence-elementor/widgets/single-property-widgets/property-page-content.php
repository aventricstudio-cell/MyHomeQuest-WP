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

class Wpresidence_Property_Page_Content extends Widget_Base {

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
        return 'property_show_content';
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
        return __('Content', 'residence-elementor');
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
            'selector' => '{{WRAPPER}} .elementor-widget-container',
                ]
        );




        $this->add_control(
                'item_color', [
            'label' => esc_html__('Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .elementor-widget-container ' => 'color: {{VALUE}}',
            ],
                ]
        );

        $this->add_control(
                'item_background_color', [
            'label' => esc_html__('Background Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .elementor-widget-container' => 'background-color: {{VALUE}}',
            ],
                ]
        );


        $this->add_responsive_control(
                'text_align', [
            'label' => __('Alignment', 'wpresidence-core'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'selectors' => [
                '{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};',
            ],
            'options' => [
                'left' => [
                    'title' => __('Left', 'wpresidence-core'),
                    'icon' => 'eicon-text-align-left', // Updated icon class
                ],
                'center' => [
                    'title' => __('Center', 'wpresidence-core'),
                    'icon' => 'eicon-text-align-center', // Updated icon class
                ],
                'right' => [
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
        //  $property_id    =   wpestate_return_property_id_elementor_builder($attributes);
        // echo apply_filters('the_content', get_post_field('post_content', $property_id));
        property_page_elementor_content_section_function($attributes, $settings);
    }

}
