<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

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
		return __( 'Detail Section - Deprecated', 'residence-elementor' );
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
                    'Subunits'                  =>  'Subunits',
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

                $this->add_control(
			'columns',
			[
                            'label' => __( 'Columns (*works only for address, property details and features and amenities)', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                            'label_block'=>true,
                            'default' => '3',
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
