<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Testimonial extends Widget_Base {

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
		return 'WpResidence_Testimonial';
	}

        public function get_categories() {
		return [ 'wpresidence' ];
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
		return __( 'Testimonial', 'residence-elementor' );
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
		return 'wpresidence-note eicon-testimonial';
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
	return [ '' ];
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
         public function elementor_transform($input){
            $output=array();
            if( is_array($input) ){
                foreach ($input as $key=>$tax){
                    $output[$tax['value']]=$tax['label'];
                }
            }
            return $output;
        }



        protected function register_controls() {
			$testimonials_types = array(1 => 1, 2 => 2, 3 => 3, 4 => 4);


		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'residence-elementor' ),
			]
		);



		$this->add_control(
			'client_name',
			[
				'label' => __( 'Client Name', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'title_client',
			[
				'label' => __( 'Client Title', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'imagelinks',
			[
				'label' => __( 'Client Image', 'residence-elementor' ),
				'type' => Controls_Manager::MEDIA,
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label' => esc_html__( 'Image Size', 'residence-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 200,
					],
					'em' => [
						'max' => 20,
					],
					'rem' => [
						'max' => 20,
					],
				],
			'selectors' => [
				'{{WRAPPER}} .testimonial-image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .testimonial-location-line' => 'width: calc(100% - calc({{SIZE}}{{UNIT}} + 30px));',
			],
			]
		);

		// Border radius control
		$this->add_control(
			'image_border_radius',
			[
				'label' => __( 'Image Border Radius', 'residence-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .testimonial-container.type_class_4 .testimonial-image' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .testimonial-image' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);



		$this->add_control(
			'testimonial_text',
			[
				'label' => __( 'Testimonial Text', 'residence-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
			]
		);

		$this->add_control(
			'testimonials_type',
			[
				'label' => __('Testimonial Type', 'residence-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,

				'options' => $testimonials_types
			]
		);

		$this->add_control(
			'stars_client',
			[
				'label' => __( 'Stars', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
			]
		);

   // Add the testimonial_title control conditionally
		$this->add_control(
			'testimonial_title',
			[
				'label' => __( 'Testimonial Title - Only for type 3', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'testimonials_type' => '3',
				],
			]
		);

		$this->end_controls_section();

        /*
         * Typography Controls
         */
        $this->start_controls_section(
            'section_typography',
            [
                'label' => esc_html__('Typography', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'client_name_typography',
				'label' => esc_html__('Client Name Typography', 'residence-elementor'),
				'selector' => '{{WRAPPER}} .testimonial-author, {{WRAPPER}} .testimonial-author-line, {{WRAPPER}} .type_class_4 .testimonial-author-line', // Include this line
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'client_title_typography',
				'label' => esc_html__('Client Title Typography', 'residence-elementor'),
				'selector' => '{{WRAPPER}} .testimonial-container.type_class_3 .testimonial-author-line, {{WRAPPER}} .testimonial-location-line, {{WRAPPER}} .testimonial-container.type_class_2 .testimonial-author-line',
			]
		);

		

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'testimonial_text_typography',
				'label' => esc_html__('Testimonial Text Typography', 'residence-elementor'),
				'selector' => '{{WRAPPER}} .testimonial-text',
			]
		);

			$this->add_responsive_control(
			'title_size',
			[
				'label' => esc_html__( 'Testimonial Title Margin', 'residence-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 200,
					],
					'em' => [
						'max' => 20,
					],
					'rem' => [
						'max' => 20,
					],
				],
			'default' => [
				'size' => 00,
				'unit' => 'px',
			],
			'selectors' => [
				'{{WRAPPER}} .type_class_4 .testimonial-author-line' => 'margin-top: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .testimonial-container.type_class_2 .testimonial-author-line' => 'margin-top: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}} .testimonial-container.type_class_3 .testimonial-author-line' => 'padding-top: {{SIZE}}{{UNIT}}',
			],
			]
		);

		$this->add_responsive_control(
			'padding_type_2',
			[
				'label' => __( 'Testimonial Padding', 'residence-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => 45,
					'right' => 45,
					'bottom' => 45,
					'left' => 45,
					'unit' => 'px',
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .testimonial-container.type_class_4' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			]
		);

		// Add the text alignment control for all types
		$this->add_responsive_control(
			'testimonial_text_alignment',
			[
				'label' => __( 'Text Alignment', 'residence-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'residence-elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'residence-elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'residence-elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .testimonial-text' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .type_class_4 .testimmonials_starts' => 'text-align: {{VALUE}};',
				],
			]
		);

		// Border radius control
		$this->add_control(
			'testimonial_border_radius',
			[
				'label' => __( 'Testimonial Border Radius', 'residence-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .testimonial-container' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .testimonial-text' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .testimonial-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

        /*
         * Color Controls
         */
        $this->start_controls_section(
            'section_color',
            [
                'label' => esc_html__('Colors', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_control(
			'client_name_color',
			[
				'label' => esc_html__('Client Name Color', 'residence-elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .testimonial-author' => 'color: {{VALUE}};',
					'{{WRAPPER}} .testimonial-author-line' => 'color: {{VALUE}};', 
					'{{WRAPPER}} .testimonial-author, .type_class_4 .testimonial-author-line' => 'color: {{VALUE}};',
					'{{WRAPPER}} .testimonial-container.type_class_3 .testimonial_title' => 'color: {{VALUE}};', 	
				],
			]
		);

		$this->add_control(
			'client_title_color',
			[
				'label' => esc_html__('Client Title Color', 'residence-elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .testimonial-author' => 'color: {{VALUE}};',
					'{{WRAPPER}} .type_class_4 .testimonial-location-line' => 'color: {{VALUE}};', 	
					'{{WRAPPER}} .testimonial-container.type_class_2 .testimonial-author-line' => 'color: {{VALUE}};', 	
					'{{WRAPPER}} .testimonial-container.type_class_3 .testimonial-author-line' => 'color: {{VALUE}};', 
					
				],
			]
		);

        $this->add_control(
            'testimonial_text_color',
            [
                'label' => esc_html__('Testimonial Text Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'testimonial_bg_color',
            [
                'label' => esc_html__('Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .testimonial-container' => 'background-color: {{VALUE}};',
                ],
            ]
        );

		$this->add_control(
			'testimonial_text_bg_color',
			[
				'label' => esc_html__('Testimonial Text Background Color', 'residence-elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .testimonial-text' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .testimonial-text:after' => 'border-right:10px {{VALUE}};',
					'{{WRAPPER}} .testimonial-text:before' => 'border-right:10px {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .testimonial-container.type_class_1 .testimonial-text, {{WRAPPER}} .testimonial-image, {{WRAPPER}} .testimonial-container.type_class_3, {{WRAPPER}} .testimonial-container.type_class_4 ',
			]
        );
		

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

         public function wpresidence_send_to_shortcode($input){
            $output='';
            if($input!==''){
                $numItems = count($input);
                $i = 0;

                foreach ($input as $key=>$value){
                    $output.=$value;
                    if(++$i !== $numItems) {
                      $output.=', ';
                    }
                }
            }
            return $output;
        }

	protected function render() {
		$settings = $this->get_settings_for_display();

                $attributes['client_name']          =   $settings['client_name'];
                $attributes['title_client']         =   $settings['title_client'];
                $attributes['imagelinks']           =   $settings['imagelinks']['url'];
                $attributes['testimonial_text']     =   $settings['testimonial_text'];
                $attributes['testimonial_type']    =   $settings['testimonials_type'];
                $attributes['testimonial_title']    =   $settings['testimonial_title'];
                $attributes['stars_client']         =   $settings['stars_client'];


              echo  wpestate_testimonial_function($attributes);
	}

	
}
