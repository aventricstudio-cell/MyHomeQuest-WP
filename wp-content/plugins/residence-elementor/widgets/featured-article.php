<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Wpresidence_Featured_Article extends Widget_Base {

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
		return 'WpResidence_Featured_Article';
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
		return __( 'Featured Article', 'residence-elementor' );
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
		return 'wpresidence-note eicon-post';
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


        private function filter_article_array( $article_array ) {
                if ( ! is_array( $article_array ) ) {
                        return array();
                }

                $filtered_articles = array();

                foreach ( $article_array as $article_item ) {
                        if ( ! isset( $article_item['value'] ) ) {
                                continue;
                        }

                        $article_id   = intval( $article_item['value'] );
                        $article_post = get_post( $article_id );

                        if ( ! $article_post || 'post' !== $article_post->post_type || 'publish' !== $article_post->post_status ) {
                                continue;
                        }

                        $filtered_articles[] = array(
                                'label' => isset( $article_item['label'] ) ? $article_item['label'] : $article_post->post_title,
                                'value' => $article_post->ID,
                        );
                }

                return $filtered_articles;
        }

        protected function register_controls() {
                $items_type  = array( 1 => 1, 2 => 2 );
                $article_array = false;

                if ( function_exists( 'wpestate_request_transient_cache' ) ) {
                        $article_array = $this->filter_article_array( wpestate_request_transient_cache( 'wpestate_js_composer_article_array' ) );
                }

                if ( ( $article_array === false || empty( $article_array ) ) && function_exists( 'wpestate_return_article_array' ) ) {
                        $article_array = $this->filter_article_array( wpestate_return_article_array() );
                }

                if ( $article_array === false || empty( $article_array ) ) {
                        $query_args = array(
                                'post_type'   => array( 'post' ),
                                'showposts'   => -1,
                                'post_status' => 'publish',
                        );

                        $all_articles = get_posts( $query_args );
                        if ( count( $all_articles ) > 0 ) {
                                $article_array = array();
                                foreach ( $all_articles as $single_article ) {
                                        $article_array[] = array(
                                                'label' => $single_article->post_title,
                                                'value' => $single_article->ID,
                                        );
                                }
                        }
                }

                if ( function_exists( 'wpestate_set_transient_cache' ) ) {
                        wpestate_set_transient_cache( 'wpestate_js_composer_article_array', $article_array, 60 * 60 * 4 );
                }

                $article_array_elementor = $this->elementor_transform( $article_array );


		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'residence-elementor' ),
			]
		);

		$this->add_control(
			'idul',
			[
				'label' => __( 'Select article', 'residence-elementor' ),
				'label_block'=>true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $article_array_elementor,
						
			]
		);

                $this->add_control(
			'second_line',
			[
				'label' => __( 'Featured Text (for type1)', 'residence-elementor' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
			]
		);
                $this->add_control(
			'design_type',
			[
				'label' => __('Design Type', 'residence-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,

				'options' => $items_type,
				'default'=>1
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
            'selector' => '{{WRAPPER}} .featured_article ,{{WRAPPER}} .featured_article_type2 ',    

            ]
        );
		

		$this->end_controls_section();

		$this->start_controls_section(
			'section_radius', [
            'label' => esc_html__('Settings', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

				$this->add_responsive_control(
			'padding_type_2',[
				'label' => __( 'Content Padding', 'residence-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'top' => 60,
					'right' => 60,
					'bottom' => 60,
					'left' => 60,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .featured_article_type2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .featured_article_type2 .featured_article_type2_title_wrapper' => 'width: 100%',
					'{{WRAPPER}} .featured_article_type2 .featured_article_type2_title_wrapper' => 'bottom: {{BOTTOM}}{{UNIT}}',
				],
				'condition' => [
					'design_type' => '2',
				],
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label' => esc_html__( 'Content width', 'residence-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 1900,
					],
					'em' => [
						'max' => 20,
					],
					'rem' => [
						'max' => 20,
					],
				],
				'default' => [
					'size' => 1100,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .featured_article_type2 .featured_article_type2_title_wrapper' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_height_size',
			[
				'label' => esc_html__( 'Image Height', 'residence-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' =>1000,
					],
					'em' => [
						'max' => 20,
					],
					'rem' => [
						'max' => 20,
					],
				],
			'selectors' => [
				'{{WRAPPER}} .featured_article_type2' => 'height: {{SIZE}}{{UNIT}}',
			],
			'condition' => [
			'design_type' => '2',
			],
			]
		);

		$this->add_responsive_control(
			'content_border_radius', [
            'label' => esc_html__('Border Radius', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .featured_article_type2' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .featured_article_type2 .featured_img_type2' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .featured_article' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .featured_article .featured_img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',               
            ],
                ]
        );


		
		$this->end_controls_section();
		
		/*
		* -------------------------------------------------------------------------------------------------
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
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',  // Name for the control
				'label' => esc_html__('Title Typography', 'residence-elementor'),  // Label for the control
				'selector' => '{{WRAPPER}} .featured_type_2, {{WRAPPER}} .featured_article_type2 .h2, {{WRAPPER}} .featured_article_type2 h2',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT
				],
				'responsive' => true,  // Enable responsive typography
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'featured_text_typography',  // Name for the control
				'label' => esc_html__('Featured Text Typography', 'residence-elementor'),  // Label for the control
				'selector' => '{{WRAPPER}} .featured_article_secondline, .featured_article_type2 .featured_article_label',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT
				],
				'responsive' => true,  // Enable responsive typography
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'link_text_typography',  // Name for the control
				'label' => esc_html__('Read More Typography', 'residence-elementor'),  // Label for the control
				'selector' => '{{WRAPPER}} .featured_article_type2 .featured_read_more a',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT
				],
				'responsive' => true,  // Enable responsive typography
				'condition' => [
					'design_type' => '2',
				],
			]
		);

		$this->add_responsive_control(
			'link_icon_size',
			[
				'label' => esc_html__('Read More Icon Size', 'residence-elementor'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 100,
					],
					'em' => [
						'min' => 0.5,
						'max' => 10,
					],
					'rem' => [
						'min' => 0.5,
						'max' => 10,
					],
				],
				'default' => [
					'size' => 11,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .featured_article_type2 .featured_read_more i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'design_type' => '2',
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
            $attributes['id']             =   $this -> wpresidence_send_to_shortcode( $settings['idul'] );
            $attributes['second_line']    =   $settings['second_line'];
            $attributes['design_type']    =   $settings['design_type'];
            echo  wpestate_featured_article($attributes);
	}


}
