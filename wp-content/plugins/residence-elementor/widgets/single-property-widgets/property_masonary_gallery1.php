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

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Property_Page_Masonary_Gallery1 extends Widget_Base {

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
        return 'property_show_masonary_gallery_1';
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
        return __('Masonry Gallery - V1', 'residence-elementor');
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
                    'min' => 350,
                    'max' => 800,
                ],
            ],
            'devices' => ['desktop', 'tablet', 'mobile'],
            'desktop_default' => [
                'size' => 650,
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
                '{{WRAPPER}} .property_header_gallery_wrapper .col-md-6.image_gallery' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}}  .property_header_gallery_wrapper .gallery-left-column.col-md-6 .col-md-6.image_gallery' => 'height: calc( {{SIZE}}{{UNIT}}/2 ) ;',
          
  
            ],
                ]
        );

        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'label' => __('Image Border',  'residence-elementor'),
            'name' => 'image_border',
            'selector' => '{{WRAPPER}} .image_gallery',
            'separator' => 'before',
                ]
        );

        $this->add_responsive_control(
                'image_border_radius', [
            'label' => __('Border Radius',  'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .image_gallery' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'label' => __('Image Shadow',  'residence-elementor'),
            'name' => 'image_box_shadow',
            'exclude' => [
                'box_shadow_position',
            ],
            'selector' => '{{WRAPPER}} .image_gallery',
                ]
        );

        $this->add_control(
                'hover_color', [
            'label' => esc_html__('Hover Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .img_listings_overlay:hover' => 'background-color: {{VALUE}};opacity:1;',
            ],
                ]
        );


        $this->add_control(
                'hide_icond', [
            'label' => esc_html__('Hide Status', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'residence-elementor'),
            'label_off' => esc_html__('No', 'residence-elementor'),
            'return_value' => 'none',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  .status-wrapper' => 'display: {{VALUE}};',
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

        print  wpestate_header_masonry_gallery($property_id);
    }

}
