<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Widget_Image;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Wpresidence_Property_Full_Width_Slider extends Widget_Base {

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
        return 'property_full_width_slider';
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
        return __('Full Width Slider', 'residence-elementor');
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




        $this->add_responsive_control(
                'item_height', [
            'label' => esc_html__('Item Height', 'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 150,
                    'max' => 800,
                ],
            ],
            'devices' => ['desktop', 'tablet', 'mobile'],
            'desktop_default' => [
                'size' => 470,
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
                '{{WRAPPER}} .propery_listing_main_image' => 'height: {{SIZE}}{{UNIT}};',
            ],
                ]
        );


        $this->add_control(
            'arrow_hover_color', [
                'label' => esc_html__('Arrow Icon Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .carousel-control-prev' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .carousel-control-next' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'carousel_background_color',
            [
                'label' => esc_html__('Arrow Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-carousel-control' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_color', [
                'label' => esc_html__('Arrow Hover Icon Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .carousel-control-prev:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .carousel-control-next:hover' => 'color: {{VALUE}};',
        ],
            ]
        );

        // Hover color control
        $this->add_control(
            'carousel_hover_color',
            [
                'label' => esc_html__('Arrow Hover Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-carousel-control:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Control for active image border
        $this->add_control(
            'active_indicator_border_color',
            [
                'label' => esc_html__('Active Image Thumb Border Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#222', // Default border color
                'selectors' => [
                    '{{WRAPPER}} #carousel-property-page-header .carousel-indicators .active img' => 'border: 2px solid {{VALUE}};',
                ],
            ]
        );
        
        // Control for inactive image border
        $this->add_control(
            'inactive_indicator_border_color',
            [
                'label' => esc_html__('Inactive Image Thumbs Border Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff', // Default border color
                'selectors' => [
                    '{{WRAPPER}} #carousel-property-page-header .carousel-indicators img' => 'border: 2px solid {{VALUE}};',
                ],
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
        $settings = $this->get_settings();
        $attributes['is_elementor'] = 1;
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $attributes['is_elementor_edit'] = 1;
        }
        $property_id = wpestate_return_property_id_elementor_builder($attributes);

        wpestate_listing_full_width_slider($property_id);
    }

}
