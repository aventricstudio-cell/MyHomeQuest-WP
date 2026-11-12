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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Property_Page_Detail_Section extends Widget_Base {

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
		return 'Detail_Section';
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
		return __( 'Detail Section', 'residence-elementor' );
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
		return 'wpresidence-note eicon-section';
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

        $details_list=array(
            'none'                      =>  'none',
            'Description'               =>  'Description',
            'Property Address'          =>  'Property Address',
            'Property Details'          =>  'Property Details',
            'Amenities and Features'    =>  'Amenities and Features',
            'Map'                       =>  'Map',
            'Virtual Tour'              =>  'Virtual Tour',
            'Walkscore'                 =>  'Walkscore',
            'Floor Plans'               =>  'Floor Plans',
            'Page Views'                =>  'Page Views',
            'What\'s Nearby'            =>  'What\'s Nearby',
            
            'Energy Certificate'        =>  'Energy Certificate',
            'Reviews'                   =>  'Reviews',
            'Video'                     =>  'Video',
            				'booking'                     =>  'Booking Shortcode'
        );


        $this->add_control(
			'detail',
			[
                'label' => __( 'Select section', 'residence-elementor' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'Description'  ,
                'options' => $details_list
			]
		);

                
       $this->end_controls_section();       
              

       $this->start_controls_section(
            'section_colors', [
                'label' => esc_html__('Colors', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_control(
            'unit_color', [
                'label' => esc_html__('Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}  .wpestate_estate_property_details_section' => 'background-color: {{VALUE}}',
                ],
                ]
        );

       
        $this->add_responsive_control(
            'property_content_padding', [
            'label' => esc_html__('Content Area Padding', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'default' => [
                'top' => '0',      // Default top padding
                'right' => '0',    // Default right padding
                'bottom' => '0',   // Default bottom padding
                'left' => '0',     // Default left padding
                'isLinked' => true, // Optional: If you want to link all sides together
            ],
            'selectors' => [
                '{{WRAPPER}}  .wpestate_estate_property_details_section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
                'border_radius', [
            'label' => esc_html__('Border Radius', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .wpestate_estate_property_details_section' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

	$this->end_controls_section();

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
            'selector' => '{{WRAPPER}} .wpestate_estate_property_details_section',
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

            $attributes['is_elementor']      =   1;
            $attributes['detail']            =   $settings['detail'];
            $attributes['columns']           =   $settings['columns'];




            echo  wpestate_estate_property_details_section($attributes);
	}


}
