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


if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Properties Widget.
 * @since 2.0
 */

class Wpresidence_Testimonial_Slider extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 1.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Wpresidence_Testimonial_Slider';
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
        return esc_html__('Testimonial Slider', 'residence-elementor');
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

    public function get_categories() {
        return ['wpresidence'];
    }

    protected function register_controls() {

        
        
        $this->start_controls_section(
            'content_section', [
            'label' => esc_html__('Content', 'residence-elementor'),
                ]
        );
        
        $repeater = new Repeater();


        $repeater->add_control(
            'testimonial_title', [
            'label' => esc_html__('Review Title', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
                ]
        );
        $repeater->add_control(
                'testimonial_name', [
            'label' => esc_html__('Reviewer Name', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
                ]
        );
        
        $repeater->add_control(
                'testimonial_job', [
            'label' => esc_html__('Reviewer Job', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
                ]
        );
          
         $repeater->add_control(
                'testimonial_stars', [
            'label' => esc_html__('Stars (1 to 5)', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
                ]
        );
          
        
        $repeater->add_control(
            'testimonial_text', [
            'label' => esc_html__('Testimonial Text', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::WYSIWYG,

            'default' => '',
                ]
        );

        $repeater->add_control(
                'testimonial_image',
                [
                        'label' => __( 'Choose Image', 'plugin-domain' ),
                        'type' => \Elementor\Controls_Manager::MEDIA,
                        'default' => [
                                'url' => \Elementor\Utils::get_placeholder_image_src(),
                        ],
                ]
        );

        
        
        $this->add_control(
			'list',
			[
				'label' => __( 'Testimonials List', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'testimonial_title' => __( 'Testimonial #1', 'residence-elementor' ),
						'testimonial_text' => __( 'Testimonial content. Click the edit button to change this text.', 'residence-elementor' ),
					],
					[
						'testimonial_title' => __( 'Testimonial #2', 'residence-elementor' ),
						'testimonial_text' => __( 'Testimonial content. Click the edit button to change this text.', 'residence-elementor'),
					],
				],
				'title_field' => '{{{ testimonial_title }}}',
			]
		);
        
        
        
        
        
        
        




        $this->end_controls_section();
        
        
        
        
        
        /*
        * -------------------------------------------------------------------------------------------------
        * Start typography section
       */
        $this->start_controls_section(
            'typography_section', [
            'label' => esc_html__('Style', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        
         $this->add_control(
                'hide_image',
                [
                    'label' => esc_html__('Hide image?', 'residence-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Yes', 'residence-elementor'),
                    'label_off' => esc_html__('No', 'residence-elementor'),
                    'return_value' => 'none',
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}}  .wpestate_testimonial_slider .item_testimonal_image' => 'display: {{VALUE}};',
                        '{{WRAPPER}}  .wpestate_testimonial_slider .item_testimonial_content' => 'width:100%;',
                    ],
                ]
        );
         

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'testimonial_title',
            'label' => esc_html__('Title Typography', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .item_testimonial_title',
                ]
        );
        
          $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'testimonial_content',
            'label' => esc_html__('Content Typography', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .item_testimonial_text p',
                ]
        );

          
            $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'testimonial_name',
            'label' => esc_html__('Name Typography', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .item_testimonial_name',
                ]
        );

            
              $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'testimonial_postion',
            'label' => esc_html__('Position Typography', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .item_testimonial_job',
                ]
        );
     
            $this->add_responsive_control(
                'teext-align', [
            'label' => __('Text Alignment', 'rentals-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => __('Left', 'rentals-elementor'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => __('Center', 'rentals-elementor'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => __('Right', 'rentals-elementor'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .wpestate_testimonial_slider .item_testimonial_content' => '       text-align: {{VALUE}};',
                'body.rtl {{WRAPPER}} .wpestate_testimonial_slider .item_testimonial_content' => 'text-align: {{VALUE}};', 
                '{{WRAPPER}} .wpestate_testimonial_slider .item_testimonial_content .item_testimonial_text p' => '       text-align: {{VALUE}};',
                'body.rtl {{WRAPPER}} .wpestate_testimonial_slider .item_testimonial_content .item_testimonial_text p' => 'text-align: {{VALUE}};', 
                
            ],
                ]
        );
                      
                      
	$this->add_responsive_control(
            'item_width',
            [
                        'label' => esc_html__('Item width', 'rentals-elementor'),
                        'type' => Controls_Manager::SLIDER,
                        'range' => [
                                        'px' => [
                                                        'min' => 300,
                                                        'max' => 2000,
                                        ],
                        ],
                        'devices' => [ 'desktop', 'tablet', 'mobile' ],
                        'desktop_default' => [
                                        'size' => '770',
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
                                '{{WRAPPER}} .wpestate_testimonial_slider .item' => 'max-width: {{SIZE}}{{UNIT}}!important;,width: {{SIZE}}{{UNIT}}',
                                '{{WRAPPER}} .wpestate_testimonial_slider' => 'max-width: {{SIZE}}{{UNIT}}!important;width: {{SIZE}}{{UNIT}}',

                                ],
                    ]
            );

        
        
        $this->end_controls_section();

       

        
         /*
         * -------------------------------------------------------------------------------------------------
         * Start color section
         */
        $this->start_controls_section(
                'section_grid_colors', [
            'label' => esc_html__('Colors', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_control(
                'unit_backgorund', [
            'label' => esc_html__('Background', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .item_testimonial_content,
                {{WRAPPER}} .wpestate_testimonial_slider .item' => 'background-color: {{VALUE}}!important',
            ],
                ]
        );

        $this->add_control(
                'title_color', [
            'label' => esc_html__('Title Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .item_testimonial_title' => 'color: {{VALUE}}',
            ],
                ]
        );

        $this->add_control(
                'content_color', [
            'label' => esc_html__('Content Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .item_testimonial_text' => 'color: {{VALUE}}',
            ],
                ]
        );

        $this->add_control(
                'name_color', [
            'label' => esc_html__('Name Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .item_testimonial_name' => 'color: {{VALUE}}',
            ],
                ]
        );

        $this->add_control(
                'item_testimonial_job', [
            'label' => esc_html__('Position Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .item_testimonial_job' => 'color: {{VALUE}}',
            ],
                ]
        );


       

        $this->end_controls_section();
        /*
         * -------------------------------------------------------------------------------------------------
         * End color section
         */
        
       
         $this->start_controls_section(
                'arrow_section', [
            'label' => esc_html__('Arrows Style', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        
        
            $this->add_control(
                   'arrow_color',
                   [
                       'label'     => esc_html__( 'Arrow Color', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                           '{{WRAPPER}} .wpestate_testimonial_slider.owl-theme .owl-nav .owl-prev' => 'color: {{VALUE}}',
                           '{{WRAPPER}} .wpestate_testimonial_slider.owl-theme .owl-nav .owl-next' => 'color: {{VALUE}}',
                       ],
                   ]
            );
            
              $this->add_control(
                   'arrow_bck_color',
                   [
                       'label'     => esc_html__( 'Arrow Background Color', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                           '{{WRAPPER}} .wpestate_testimonial_slider.owl-theme .owl-nav .owl-prev' => 'background-color: {{VALUE}}',
                           '{{WRAPPER}} .wpestate_testimonial_slider.owl-theme .owl-nav .owl-next' => 'background-color: {{VALUE}}',
                       ],
                   ]
            );
           
           
            
            $this->add_control(
                   'arrow_color_hover',
                   [
                       'label'     => esc_html__( 'Arrow Color Hover', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                            '{{WRAPPER}} .wpestate_testimonial_slider.owl-theme .owl-nav .owl-prev:hover' => 'color: {{VALUE}}',
                            '{{WRAPPER}} .wpestate_testimonial_slider.owl-theme .owl-nav .owl-next:hover' => 'color: {{VALUE}}',
                       ],
                   ]
               );
            
             $this->add_control(
                   'arrow_bck_color_hover',
                   [
                       'label'     => esc_html__( 'Arrow Background Color Hover', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                            '{{WRAPPER}} .wpestate_testimonial_slider.owl-theme .owl-nav .owl-prev:hover' => 'background-color: {{VALUE}}',
                            '{{WRAPPER}} .wpestate_testimonial_slider.owl-theme .owl-nav .owl-next:hover' => 'background-color: {{VALUE}}',
                       ],
                   ]
               );

             
               $this->add_control(
                   'bullet_color',
                   [
                       'label'     => esc_html__( 'Dot Color', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                            '{{WRAPPER}} .owl-theme .owl-dots .owl-dot span' => 'background-color: {{VALUE}}',
                        
                       ],
                   ]
               );

               
                 $this->add_control(
                   'bullet_color_active',
                   [
                       'label'     => esc_html__( 'Dot Color Active', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                            '{{WRAPPER}} .wpestate_testimonial_slider.owl-theme .owl-dots .owl-dot.active span, .wpestate_testimonial_slider.owl-theme .owl-dots .owl-dot:hover span' => 'background: {{VALUE}}',

                       ],
                   ]
               );


           $this->end_controls_section();
        
        
        /*
         * -------------------------------------------------------------------------------------------------
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
            'selector' => '{{WRAPPER}} .wpestate_testimonial_slider .item',
                ]
        );

        $this->end_controls_section();
        /*
         * -------------------------------------------------------------------------------------------------
         * End shadow section
         */
     
    }

    protected function render() {
        global $post;
        $settings = $this->get_settings_for_display();
     

        print   wpestate_testimonial_slider( $settings);
        

    }

   

  

}

//end class
