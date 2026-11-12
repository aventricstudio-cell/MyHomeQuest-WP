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

class Wpresidence_Property_Page_Featured_Image extends Widget_Base {

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
        return 'property_show_featured_image';
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
        return __('Property Featured Image', 'residence-elementor');
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
                Group_Control_Image_Size::get_type(), [
            'name' => 'featured_thumb_size',
            'exclude' => ['custom', 'thumbnail'],
            'include' => [],
            'default' => 'full',
                ]
        );



        $this->add_control(
                'enable_fallback', [
            'label' => esc_html__('Enable Fallback',  'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes',  'residence-elementor'),
            'label_off' => esc_html__('No',  'residence-elementor'),
            'return_value' => 'yes',
            'default' => ''
                ]
        );

        $this->add_control(
                'fallback_image', [
            'label' => __('Fallback Image',  'residence-elementor'),
            'type' => Controls_Manager::MEDIA,
            'dynamic' => [
                'active' => false,
            ],
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
            'condition' => [
                'enable_fallback' => 'yes',
            ],
                ]
        );






        $this->add_control(
                'margin', [
            'label' => __('Margin', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .wpresidence-elementor-featured-image img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );




        $this->add_responsive_control(
                'text_align', [
            'label' => __('Alignment',  'residence-elementor'),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'selectors' => [
                '{{WRAPPER}} .wpresidence-elementor-featured-image' => 'text-align: {{VALUE}};',
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

        $this->start_controls_section(
                'section_style_image', [
            'label' => __('Image',  'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        $this->add_responsive_control(
                'width', [
            'label' => __('Width',  'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'unit' => '%',
            ],
            'tablet_default' => [
                'unit' => '%',
            ],
            'mobile_default' => [
                'unit' => '%',
            ],
            'size_units' => ['%', 'px', 'vw'],
            'range' => [
                '%' => [
                    'min' => 1,
                    'max' => 100,
                ],
                'px' => [
                    'min' => 1,
                    'max' => 1000,
                ],
                'vw' => [
                    'min' => 1,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .wpresidence-elementor-featured-image img' => 'width: {{SIZE}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
                'space', [
            'label' => __('Max Width',  'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'unit' => '%',
            ],
            'tablet_default' => [
                'unit' => '%',
            ],
            'mobile_default' => [
                'unit' => '%',
            ],
            'size_units' => ['%', 'px', 'vw'],
            'range' => [
                '%' => [
                    'min' => 1,
                    'max' => 100,
                ],
                'px' => [
                    'min' => 1,
                    'max' => 1000,
                ],
                'vw' => [
                    'min' => 1,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .wpresidence-elementor-featured-image img' => 'max-width: {{SIZE}}{{UNIT}};',
            ],
                ]
        );

        $this->add_control(
                'separator_panel_style', [
            'type' => Controls_Manager::DIVIDER,
            'style' => 'thick',
                ]
        );

        $this->start_controls_tabs('image_effects');

        $this->start_controls_tab('normal', [
            'label' => __('Normal',  'residence-elementor'),
                ]
        );

        $this->add_control(
                'opacity', [
            'label' => __('Opacity',  'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 1,
                    'min' => 0.10,
                    'step' => 0.01,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .wpresidence-elementor-featured-image img' => 'opacity: {{SIZE}};',
            ],
                ]
        );

        $this->add_group_control(
                Group_Control_Css_Filter::get_type(), [
            'name' => 'css_filters',
            'selector' => '{{WRAPPER}} .wpresidence-elementor-featured-image img',
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab('hover', [
            'label' => __('Hover',  'residence-elementor'),
                ]
        );

        $this->add_control(
                'opacity_hover', [
            'label' => __('Opacity',  'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 1,
                    'min' => 0.10,
                    'step' => 0.01,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .wpresidence-elementor-featured-image:hover img' => 'opacity: {{SIZE}};',
            ],
                ]
        );

        $this->add_group_control(
                Group_Control_Css_Filter::get_type(), [
            'name' => 'css_filters_hover',
            'selector' => '{{WRAPPER}} .wpresidence-elementor-featured-image:hover img',
                ]
        );

        $this->add_control(
                'background_hover_transition', [
            'label' => __('Transition Duration',  'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 3,
                    'step' => 0.1,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .wpresidence-elementor-featured-image img' => 'transition-duration: {{SIZE}}s',
            ],
                ]
        );

        $this->add_control(
                'hover_animation', [
            'label' => __('Hover Animation',  'residence-elementor'),
            'type' => Controls_Manager::HOVER_ANIMATION,
                ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'image_border',
            'selector' => '{{WRAPPER}} .wpresidence-elementor-featured-image img',
            'separator' => 'before',
                ]
        );

        $this->add_responsive_control(
                'image_border_radius', [
            'label' => __('Border Radius',  'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .wpresidence-elementor-featured-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'image_box_shadow',
            'exclude' => [
                'box_shadow_position',
            ],
            'selector' => '{{WRAPPER}} .wpresidence-elementor-featured-image img',
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

        $thumbnail_id = get_post_thumbnail_id($property_id);

        if ($thumbnail_id) {

            $featured_img = get_the_post_thumbnail($property_id, $settings['featured_thumb_size_size']);
        } else {
            if ($settings['enable_fallback'] == 'yes') {
                $featured_img = wp_get_attachment_image($settings['fallback']['id'], $settings['featured_thumb_size_size']);
            } else {
                $featured_img = '';
            }
        }




        echo '<div class="wpresidence-elementor-featured-image">';
        echo $featured_img;
        echo '</div>';
    }

}
