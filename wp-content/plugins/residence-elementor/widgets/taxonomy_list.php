<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Taxonomy_List extends Widget_Base {

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
		return 'WpResidence_Featured_Agent';
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
		return __( 'Taxonomy List', 'residence-elementor' );
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
		return 'wpresidence-note eicon-bullet-list';
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


                $taxonomy_list_type         =   array(
                    'category'          =>  'category',
                    'action category'   =>  'action category',
                    'city'              =>  'city',
                    'area'              =>  'area',
                    'county/state'      =>  'county/state',
                    'status'            =>  'status',
                    'features and ammenities'=>'features and amenities');
                $taxonomy_list_type_show    =   array('yes'=>'yes','no'=>'no');



		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'residence-elementor' ),
			]
		);

		$this->add_control(
			'taxonomy_list_type',
			[
                            'label' => __( 'Select category', 'residence-elementor' ),
                            'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT,

                            'options' => $taxonomy_list_type,
                             'default' => 'category',
			]
		);


                $this->add_control(
			'taxonomy_list_type_show',
			[
                            'label' => __( 'Show number of listings?', 'residence-elementor' ),
                            'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT,

                            'options' => $taxonomy_list_type_show,
                             'default' => 'yes',
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
            $attributes['taxonomy_list_type']       =   $settings['taxonomy_list_type'];
            $attributes['taxonomy_list_type_show']    =   $settings['taxonomy_list_type_show'];

            echo  wpestate_taxonomy_list($attributes);
	}


}
