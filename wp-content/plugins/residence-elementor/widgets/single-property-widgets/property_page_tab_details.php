<?php
namespace ElementorWpResidence\Widgets;


use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
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
		return ' wpresidence-note eicon-tabs';
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
                            'label' => __( 'Description Label in the tab. Set it blank if you don\'t want it to appear.', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                            'label_block'=>true,
                            'default' => 'Description',
			]
		);
                $this->add_control(
			'property_address',
			[
                            'label' => __( 'Property Address  Label in the tab. Set it blank if you don\'t want it to appear.', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                            'label_block'=>true,
                            'default' => 'Address',
			]
		);
                $this->add_control(
			'property_details',
			[
                            'label' => __( 'Property Details Label in the tab. Set it blank if you don\'t want it to appear.', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                             'label_block'=>true,
                             'default' => 'Details',
			]
		);
                $this->add_control(
			'amenities_features',
			[
                            'label' => __( 'Amenities and Features Label in the tab. Set it blank if you don\'t want it to appear.', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                             'label_block'=>true,
                             'default' => 'Amenities',
			]
		);
                $this->add_control(
			'map',
			[
                            'label' => __( 'Map Label in the tab. Set it blank if you don\'t want it to appear.', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                             'label_block'=>true,
                            'default' => 'Map',
			]
		);
                $this->add_control(
			'walkscore',
			[
                            'label' => __( 'Walkscore Label in the tab. Set it blank if you don\'t want it to appear.', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                             'label_block'=>true,
                            'default' => 'Walkscore',
			]
		);
                $this->add_control(
			'floor_plans',
			[
                            'label' => __( 'Floor Plans Label in the tab. Set it blank if you don\'t want it to appear.', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                             'label_block'=>true,
                            'default' => 'Floor Plans',
			]
		);
                $this->add_control(
			'page_views',
			[
                            'label' => __( 'Page Views Label in the tab. Set it blank if you don\'t want it to appear.', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                            'label_block'=>true,
                            'default' => 'Page Views',
			]
		);
                $this->add_control(
			'virtual_tour',
			[
                            'label' => __( 'Virtual Tour Label in the tab. Set it blank if you don\'t want it to appear.', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                             'label_block'=>true,
                            'default' => 'Virtual Tour',
			]
		);
                $this->add_control(
			'yelp_details',
			[
                            'label' => __( 'Yelp Label in the tab. Set it blank if you don\'t want it to appear.', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                             'label_block'=>true,
                            'default' => 'What\'s near By',
			]
		);


		$this->end_controls_section();


		/*
		* Start typography section
		*/
		$this->start_controls_section(
							 'typography_section',
							 [
									 'label'     => esc_html__( 'Typography', 'residence-elementor' ),
									 'tab'       => Controls_Manager::TAB_STYLE,
							 ]
					 );

					 $this->add_group_control(
								Group_Control_Typography::get_type(),
								[
										'name'     => 'property_section_title',
										'label'    => esc_html__( 'Section Title', 'residence-elementor' ),
										'global'   => [
											'default' => Global_Typography::TYPOGRAPHY_PRIMARY
										],
										'selector' => '{{WRAPPER}} #tab_prpg li a',
								]
						);




				$this->end_controls_section();



				 /*
					 *-------------------------------------------------------------------------------------------------
					 * Start color section
					 */
					 $this->start_controls_section(
							 'section_colors',
							 [
									 'label' => esc_html__( 'Colors', 'residence-elementor' ),
									 'tab'   => Controls_Manager::TAB_STYLE,
							 ]
					 );


					 $this->add_control(
							'section_title_back_color',
							[
									'label'     => esc_html__( 'Section Title Back Color', 'residence-elementor' ),
									'type'      => Controls_Manager::COLOR,
									'default'   => '',
									'selectors' => [
											'{{WRAPPER}} #tab_prpg ul'=> 'background-color: {{VALUE}}',

									],
							]
					 );
					 $this->add_control(
							'section_title__color',
							[
									'label'     => esc_html__( 'Section Title Color', 'residence-elementor' ),
									'type'      => Controls_Manager::COLOR,
									'default'   => '',
									'selectors' => [
											'{{WRAPPER}} #tab_prpg li a'=> 'color: {{VALUE}}',

									],
							]
					 );




					 $this->end_controls_section();
				 /*
				 *-------------------------------------------------------------------------------------------------
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
