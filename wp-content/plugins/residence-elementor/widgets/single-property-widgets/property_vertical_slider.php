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

class Wpresidence_Property_Page_Vertical_Slider extends Widget_Base {

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
        return 'property_show_vertical_slider';
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
        return __('Vertical Slider', 'residence-elementor');
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
			'section_content',
			[
				'label' => __( 'Content', 'residence-elementor' ),
			]
		);

		$this->add_control(
			'gallery_type',
			[
				'label' => esc_html__('Photo Gallery Type', 'esidence-elementor'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__('Built In', 'esidence-elementor'),
					'elementor' => esc_html__('Photo Swipe', 'esidence-elementor'),
				],
				// 'description' => esc_html__('Select how many images to display per row', 'esidence-elementor'),
			]
		);

        $this->end_controls_section();



        $this->start_controls_section(
            'typography_section', [
                'label' => esc_html__('Settings', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );



        $this->add_responsive_control(
            'image_border_radius', [
                'label' => __('Border Radius',  'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} #property_slider_carousel img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} #carousel-indicators-vertical li img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .carousel-indicators-vertical img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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


        $this->add_control(
                'back_color', [
            'label' => esc_html__('Background Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .post-carusel' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .post-carusel' => 'background: {{VALUE}};',
                '{{WRAPPER}} #carousel-indicators-vertical' => 'background: {{VALUE}};',
                '{{WRAPPER}} .carousel-indicators li' => 'background:transparent;',
                '{{WRAPPER}} .owl-carousel .owl-item .item' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .carusel-back' => 'background-color: {{VALUE}};',
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


        $this->add_control(
                'hide_map_button', [
            'label' => esc_html__('Hide Map Button', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'residence-elementor'),
            'label_off' => esc_html__('No', 'residence-elementor'),
            'return_value' => 'none',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  #slider_enable_map' => 'display: {{VALUE}};',
            ],
                ]
        );

        $this->add_control(
                'hide_street_view', [
            'label' => esc_html__('Hide Street View Button', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'residence-elementor'),
            'label_off' => esc_html__('No', 'residence-elementor'),
            'return_value' => 'none',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  #slider_enable_street' => 'display: {{VALUE}};',
            ],
                ]
        );
        $this->add_control(
                'hide_gallery', [
            'label' => esc_html__('Hide Gallery Button', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'residence-elementor'),
            'label_off' => esc_html__('No', 'residence-elementor'),
            'return_value' => 'none',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  #slider_enable_slider' => 'display: {{VALUE}};',
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

        ob_start();
        wpestate_vertical_slider($property_id, 'listing_full_slider_1');

        $galleryHtml = ob_get_clean();
		
		if ( $settings['gallery_type'] === 'elementor' ) {
			$galleryHtml = str_replace('lightbox_trigger', 'elementor-lightbox', $galleryHtml);
		} else {
			$galleryHtml = preg_replace('/href=["\'][^"\']*["\']/', '', $galleryHtml);
		}

        print $galleryHtml;
    }

}
