<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;


if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Sliding_Box extends Widget_Base {

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
        return 'Wpresidence_Sliding_Box';
    }

    public function get_categories() {
        return ['wpresidence'];
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
        return __('Sliding Box', 'residence-elementor');
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
        return ' wpresidence-note    eicon-email-field';
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


        $repeater = new Repeater();

        $repeater->add_control(
                'title', [
            'label' => esc_html__('Title', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => '',
                ]
        );

        $repeater->add_control(
                'show_open', [
            'label' => __('Open Box', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'residence-elementor'),
            'label_off' => __('No', 'residence-elementor'),
            'return_value' => 'yes',
            'default' => 'no',
                ]
        );


        $repeater->add_control(
            'read_me', [
            'label' => esc_html__('Read Me Text', 'residence-elementor'),
				'type' => \Elementor\Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => isset( $args['button_default_text'] ) ? $args['button_default_text'] : esc_html__('Learn More', 'residence-elementor'),
				'placeholder' => isset( $args['button_default_text'] ) ? $args['button_default_text'] : esc_html__('Learn More', 'residence-elementor'),
				'condition' => isset( $args['section_condition'] ) ? $args['section_condition'] : [],
			]
        );
        

        $repeater->add_control(
            'read_me_link', [
                'label' => esc_html__('Read Me Link', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::URL,
                'options' => [ 'url', 'is_external', 'nofollow' ],
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
					// 'custom_attributes' => '',
				],
            ]
        );

        $repeater->add_control(
            'read_me_icon', [
                'label' => esc_html__('Icon', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [], // No icon selected by default
				'recommended' => [
					'fa-solid' => [
						'circle',
						'dot-circle',
						'square-full',
					],
					'fa-regular' => [
						'circle',
						'dot-circle',
						'square-full',
					],
				],
            ]
        );
        

        $repeater->add_control(
            'content',
            [
                'label' => esc_html__('Content', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => '',
                'rows' => 4, // Optional: Adjust the number of visible rows
            ]
        );

        $repeater->add_control(
            'image', [
            'label' => __('Choose Image', 'plugin-domain'),
            'type' => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
                ]
        );



        $this->start_controls_section(
            'wpresidence_area_form_fields', [
            'label' => esc_html__('Boxes', 'residence-elementor'),
                ]
        );

        $this->add_control(
            'form_fields', [
            'type' => \Elementor\Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                [
                    '_id' => 'name',
                    'title' => 'Title Here',
                    'read_me' => esc_html__('Learn More', 'residence-elementor'),
                    'content' => esc_html__('', 'residence-elementor'),
                    'image' => '',
                    'width' => '100',
                ],
            ],
            'title_field' => '{{{ title }}}',
            ]
        );


        $this->end_controls_section();


        $this->start_controls_section(
            'wpresidence_field_style', [
            'label' => esc_html__('Box Style', 'residence-elementor'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

       $this->add_responsive_control(
            'box_height',
            [
                'label' => esc_html__('Box Height', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vh'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 265,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpestate_sliding_box .sliding-image' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'responsive' => true, // allows different sizes per device (desktop, tablet, mobile)
            ]
        );

        $this->add_responsive_control(
            'image_box_width',
            [
                'label' => esc_html__('Image Box Width', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => ['min' => 50, 'max' => 1000, 'step' => 1],
                    '%' => ['min' => 10, 'max' => 100],
                    'vw' => ['min' => 10, 'max' => 100],
                ],
                'default' => ['unit' => 'px', 'size' => 280],
                'selectors' => [
                    '{{WRAPPER}} .wpestate_sliding_box .sliding-image' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wpestate_sliding_box' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wpestate_sliding_box .sliding-content-wrapper' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'responsive' => true,
            ]
        );

        $this->add_responsive_control(
            'image_box_content_left_position',
            [
                'label' => esc_html__('Content Box Left Position', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => ['min' => 0, 'max' => 1000],
                    '%' => ['min' => 0, 'max' => 100],
                    'vw' => ['min' => 0, 'max' => 100],
                ],
                'default' => ['unit' => 'px', 'size' => 280], // default for desktop
                'tablet_default' => ['unit' => 'px', 'size' => 280],
                'mobile_default' => ['unit' => 'px', 'size' => 0], // mobile left: 0
                'selectors' => [
                    '{{WRAPPER}} .wpestate_sliding_box .sliding-content-wrapper' => 'left: {{SIZE}}{{UNIT}};',
                ],
                'responsive' => true,
            ]
        );

        $this->add_responsive_control(
            'open_box_width',
            [
                'label' => esc_html__('Open Box Width', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => ['min' => 50, 'max' => 1500, 'step' => 1],
                    '%' => ['min' => 10, 'max' => 100],
                    'vw' => ['min' => 10, 'max' => 100],
                ],
                'default' => ['unit' => 'px', 'size' => 560],
                'selectors' => [
                    '{{WRAPPER}} .wpestate_sliding_box.active-element' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'responsive' => true,
            ]
        );

        $this->add_responsive_control(
            'sliding_box_gap',
            [
                'label' => esc_html__('Box Gap', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpestate_sliding_box_wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding', [
            'label' => esc_html__('Box Padding', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .sliding-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );



        $this->add_control(
            'wpresidence_back_color', [
            'label' => esc_html__('Box Background Color', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .sliding-content-wrapper' => 'color: {{VALUE}};',
            ],
            'default' => '#fff',
         
            ]
        );

        $this->add_control(
            'wpresidence_title_color', [
            'label' => esc_html__('Title Color', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .wpestate_sliding_box h4' => 'color: {{VALUE}};',
            ],
            'default' => '#222',
         
            ]
        );


        $this->add_control(
            'wpresidence_font_color', [
            'label' => esc_html__('Font Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#5c727d',
            'selectors' => [
                '{{WRAPPER}} .wpestate_sliding_box p' => 'color: {{VALUE}};',
            ],
          
            ]
        );

        $this->add_group_control(
        Group_Control_Border::get_type(),
            [
                'name' => 'sliding_box_border',
                'label' => esc_html__('Box Border', 'residence-elementor'),
                'selector' => '{{WRAPPER}} .wpestate_sliding_box',
            ]
        );

        $this->add_responsive_control(
            'content_border_radius', [
            'label' => esc_html__('Border Radius', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .wpestate_sliding_box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .wpestate_sliding_box .sliding-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',               
            ],
            ]
        );

        $this->end_controls_section();


                 /* -------------------------------------------------------------------------------------------------
         * Start shadow section
         */
        $this->start_controls_section(
            'section_typografy', [
            'label' => esc_html__('Typography', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_group_control(
        Group_Control_Typography::get_type(), [
            'name' => 'title_typo',
            'label' => esc_html__('Title', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .wpestate_sliding_box h4',
            'fields_options' => [
                // Inner control name
                'font_weight' => [
                    // Inner control settings
                    'default' => '300',
                ],
                'font_family' => [
                    'default' => 'Roboto',
                ],
                'font_size' => ['default' => ['unit' => 'px', 'size' => 14]],
            ],
            ]
        );
        
        $this->add_group_control(
        Group_Control_Typography::get_type(), [
            'name' => 'content_typo',
            'label' => esc_html__('Content', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .wpestate_sliding_box p',
            'fields_options' => [
                // Inner control name
                'font_weight' => [
                    // Inner control settings
                    'default' => '300',
                ],
                'font_family' => [
                    'default' => 'Roboto',
                ],
                'font_size' => ['default' => ['unit' => 'px', 'size' => 14]],
            ],
            ]
        );
        
$this->end_controls_section();

 $this->start_controls_section(
        'wpresidence_area_button_style', [
        'label' => esc_html__('Button', 'residence-elementor'),
        'tab' => Controls_Manager::TAB_STYLE,
        ]
    );

    $this->add_control(
        'search_button_use_hover_effect', [
        'label' => esc_html__('Use Hover Effect?', 'residence-elementor'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__('Yes', 'residence-elementor'),
        'label_off' => esc_html__('No', 'residence-elementor'),
        'return_value' => 'true',
        'default' => 'true',
        'separator' => 'before',
            ]
    );

    $this->start_controls_tabs('tabs_button_style');

    $this->start_controls_tab(
        'tab_button_normal', [
        'label' => esc_html__('Normal State', 'residence-elementor'),
        ]
    );

    $this->add_control(
        'submit_button_background_color', [
        'label' => esc_html__('Submit Button Background Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
    
        'default' => '#0073e6',
        'selectors' => [
            '{{WRAPPER}} .wpresidence_button' => 'background-image: linear-gradient(to right, transparent 50%, {{VALUE}} 50%);background-color:  {{VALUE}};',
        ],
        ]
    );

    $this->add_control(
        'submit_button_text_color', [
        'label' => esc_html__('Submit Button Text Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'default' => '#fff',
        'selectors' => [
            '{{WRAPPER}} .wpresidence_button' => 'color: {{VALUE}};',
        ],
        ]
    );

    $this->add_control(
        'icon_primary_color', [
        'label' => __('Icon Color', 'elementor'),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .elementor-icon, {{WRAPPER}} .elementor-icon:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
            '{{WRAPPER}} .elementor-icon, {{WRAPPER}} .elementor-icon:hover svg' => 'fill: {{VALUE}};',
        ],
        ]
    );

    $this->add_group_control(
        Group_Control_Typography::get_type(), [
            'name' => 'submit_button_typography',
            'global' => [
                'default' => Global_Typography::TYPOGRAPHY_ACCENT
            ],
                'selector' => '{{WRAPPER}} .wpresidence_button',
        ]
    );

        $this->add_group_control(
            Group_Control_Border::get_type(), [
            'name' => 'submit_button_border',
            'selector' => '{{WRAPPER}} .wpresidence_button',
            ]
        );

        $this->add_responsive_control(
            'submit_ button_border_radius', [
            'label' => esc_html__('Submit Button Border Radius', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            ]
        );

        $this->add_responsive_control(
            'submit_button_text_padding', [
            'label' => esc_html__('Submit Button Text Padding', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            ]
        );

    $this->add_responsive_control(
    'button_alignment',
        [
            'label' => esc_html__('Button Alignment', 'residence-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => esc_html__('Left', 'residence-elementor'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'residence-elementor'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => esc_html__('Right', 'residence-elementor'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button' => 'text-align: {{VALUE}};',
            ],
            'toggle' => true,
        ]
    );

    

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover', [
            'label' => esc_html__('Hover State', 'residence-elementor'),
            ]
        );

        $this->add_control(
            'submit_button_background_hover_color', [
            'label' => esc_html__('Submit Button Background Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#FFFFFF00',
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button:hover' => 'background-color: {{VALUE}};',
            ],
            ]
        );

        $this->add_control(
            'submit_button_hover_color', [
            'label' => esc_html__('Submit Button Text Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#0073ef',
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button:hover' => 'color: {{VALUE}};',
            ],
            ]
        );

        $this->add_control(
            'hover_icon_color', [
            'label' => __('Hover Color icon', 'elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button:hover .elementor-icon, {{WRAPPER}} .wpresidence_button:hover .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                '{{WRAPPER}} .wpresidence_button:hover .elementor-icon, {{WRAPPER}} .wpresidence_button:hover  .elementor-icon svg' => 'fill: {{VALUE}};',
            ],
            ]
        );

        $this->add_control(
            'submit_button_hover_border_color', [
            'label' => esc_html__('Submit Button Border Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button:hover' => 'border-color: {{VALUE}};',
            ],
            'condition' => [
                'button_border_border!' => '',
            ],
            ]
        );

        $this->end_controls_tab();


        $this->end_controls_tabs();
        

        $this->end_controls_section();
        
        /*
         * -------------------------------------------------------------------------------------------------
         * End shadow section
         */
        

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
            'selector' => '{{WRAPPER}} .wpestate_sliding_box',
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
        echo wpestate_sliding_box_shortcode($settings);
    }

}
