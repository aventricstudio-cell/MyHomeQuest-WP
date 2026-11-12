<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Property_Page_Tab_Details extends Widget_Base {

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
		return 'Details_as_Tabs';
	}

        public function get_categories() {
		return [ 'wpresidence_property' ];
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
		return __( 'Details as Tabs', 'residence-elementor' );
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
		return 'wpresidence-note eicon-tabs';
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
                $text_align=array('left'=>'left','right'=>'right','center'=>'center');
                $this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'residence-elementor' ),
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __( 'Description Tab Label. Leave title blank if you don\'t want this tab to appear.', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block'=>true,
				'default' => 'Description',
			]
		);
		$this->add_control(
			'property_address',
			[
				'label' => __( 'Address Tab Label. Leave title blank if you don\'t want this tab to appear.', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block'=>true,
				'default' => 'Address',
			]
		);
		$this->add_control(
			'property_details',
			[
				'label' => __( 'Details Tab Label. Leave title blank if you don\'t want this tab to appear.', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block'=>true,
				'default' => 'Details',
			]
		);
		$this->add_control(
			'amenities_features',
			[
				'label' => __( 'Amenities and Features Tab Label. Leave title blank if you don\'t want this tab to appear.', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block'=>true,
				'default' => 'Amenities',
			]
		);
		$this->add_control(
			'map',
			[
				'label' => __( 'Maps Tab Label. Leave title blank if you don\'t want this tab to appear.', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block'=>true,
				'default' => 'Map',
			]
		);
		$this->add_control(
			'walkscore',
			[
				'label' => __( 'Walkscore Tab Label. Leave title blank if you don\'t want this tab to appear.', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block'=>true,
				'default' => 'Walkscore',
			]
		);
		$this->add_control(
			'floor_plans',
			[
				'label' => __( 'Floor Plans Tab Label. Leave title blank if you don\'t want this tab to appear.', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block'=>true,
				'default' => 'Floor Plans',
			]
		);
		$this->add_control(
			'page_views',
			[
				'label' => __( 'Page Views Tab Label. Leave title blank if you don\'t want this tab to appear.', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block'=>true,
				'default' => 'Page Views',
			]
		);

		$this->add_control(
			'yelp_details',
			[
				'label' => __( 'Yelp Tab Label. Leave title blank if you don\'t want this tab to appear.', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block'=>true,
				'default' => 'What\'s Nearby',
			]
		);

		$this->add_control(
			'virtual_tour',
			[
				'label' => __( 'Virtual Tour Tab Label. Leave title blank if you don\'t want this tab to appear.', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block'=>true,
				'default' => 'Virtual Tour',
			]
		);



		$this->end_controls_section();

		$this->start_controls_section(
			'section_grid_box_shadow',
			[
				'label' => esc_html__( 'Box Shadow', 'residence-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'residence-elementor' ),
				'selector' => '{{WRAPPER}} .elementor-widget-container #tab_prpg',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_colors',
			[
				'label' => esc_html__( 'Colors', 'residence-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
	

		// Background Color Control for #tab_prpg > ul
		$this->add_control(
			'tab_ul_background_color',
			[
				'label' => esc_html__('Tabs Background Color', 'residence-elementor'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#0073e1',
				'selectors' => [
					'{{WRAPPER}} #tab_prpg > ul' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .nav-tabs .nav-link' => 'border-color: {{VALUE}};',	
					'{{WRAPPER}} #tab_prpg > ul li' => 'border-color: {{VALUE}};',	
					
				],
			]
		);

		// Text Color Control
		$this->add_control(
			'tab_text_color',
			[
				'label' => esc_html__('Tabs Text Color', 'residence-elementor'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} #tab_prpg > ul li button' => 'color: {{VALUE}};',
				],
			]
		);

		// Background Color Control for Hover
		$this->add_control(
			'tab_button_hover_background_color',
			[
				'label' => esc_html__('Tab Item Hover & Active Background Color', 'residence-elementor'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff', // Default background color
				'selectors' => [
					'{{WRAPPER}} #tab_prpg > ul li button:hover' => 'background-color: {{VALUE}}; height: auto;',
					'{{WRAPPER}} #tab_prpg > ul li button.active' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .nav-tabs .nav-link:focus' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .nav-tabs .nav-link:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		// Text Color Control for Hover
		$this->add_control(
			'tab_button_hover_text_color',
			[
				'label' => esc_html__('Tab Item Hover & Active Text Color', 'residence-elementor'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#222222', // Default text color
				'selectors' => [
					'{{WRAPPER}} #tab_prpg > ul li button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} #tab_prpg > ul li button.active' => 'color: {{VALUE}};',
				],
			]
		);
	
		// Border Right Control
		$this->add_control(
			'tab_border_right_color',
			[
				'label' => esc_html__('Tab Item Border Color', 'residence-elementor'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#1489f9', // Default color
				'selectors' => [
					'{{WRAPPER}} #tab_prpg > ul li' => 'border-right-color: {{VALUE}};',
				],
			]
		);

		// Width Control
		$this->add_responsive_control(
			'tab_width',
			[
				'label' => esc_html__('Tab Item Width', 'residence-elementor'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['%', 'px'],
				'default' => [
					'unit' => '%',
					'size' => 20, // Default width is 20% on larger screens
				],
				'tablet_default' => [
					'unit' => '%',
					'size' => 25, // 25% width on tablet screens
				],
				'mobile_default' => [
					'unit' => '%',
					'size' => 100, // 100% width on mobile screens (full width)
				],
				'selectors' => [
					'{{WRAPPER}} #tab_prpg > ul li' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_typography',
			[
				'label' => esc_html__('Typography', 'residence-elementor'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		
		// Typography Control for Tab Text
		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'     => 'tab_text_typography',
				'label'    => esc_html__('Tabs Typography', 'residence-elementor'),
				'selector' => '{{WRAPPER}} #tab_prpg > ul li button',
				'global'   => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
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

            $attributes['is_elementor']                 =   1;
            $attributes['description']                  =   $settings['description'];
            $attributes['property_address']             =   $settings['property_address'];
            $attributes['property_details']             =   $settings['property_details'];
            $attributes['amenities_features']           =   $settings['amenities_features'];
            $attributes['map']                          =   $settings['map'];
            $attributes['walkscore']                    =   $settings['walkscore'];
            $attributes['floor_plans']                  =   $settings['floor_plans'];
            $attributes['page_views']                   =   $settings['page_views'];
            $attributes['virtual_tour']                 =   $settings['virtual_tour'];
            $attributes['yelp_details']                 =   $settings['yelp_details'];


            echo  wpestate_property_page_design_tab($attributes);
	}


}
