<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Google_Map_Property extends Widget_Base {

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
		return 'WpResidence_Google_Map';
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
		return __( 'Google Map with Property Marker', 'residence-elementor' );
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
		return 'wpresidence-note eicon-info-box';
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



                $featured_listings  =   array('no'=>'no','yes'=>'yes');
                $items_type         =   array('properties'=>'properties','articles'=>'articles');
                $alignment_type     =   array('vertical'=>'vertical','horizontal'=>'horizontal');


                $agency_developers_array=false;
                if(function_exists('wpestate_request_transient_cache')){
                    $agency_developers_array = wpestate_request_transient_cache( 'wpestate_js_composer_article_agency_developers_array');
                }



				if($agency_developers_array === false){
					$agency_developers_array = array(); // Initialize as an empty array
					$args_inner = array(
						'post_type' => array( 'estate_agency', 'estate_developer' ),
						'showposts' => -1
					);
					$all_agency_dev_packages = get_posts( $args_inner );
					if( count($all_agency_dev_packages) > 0 ){
						foreach( $all_agency_dev_packages as $single_package ){
							$temp_array = array();
							$temp_array['label'] = $single_package->post_title;
							$temp_array['value'] = $single_package->ID;
				
							$agency_developers_array[] = $temp_array;
						}
					}
					if(function_exists('wpestate_set_transient_cache')){
						wpestate_set_transient_cache('wpestate_js_composer_article_agency_developers_array', $agency_developers_array, 60*60*4);
					}
				}
				
                $agency_developers_array_elementor      =   $this->elementor_transform($agency_developers_array);


			$this->start_controls_section(
				'section_content',
				[
					'label' => __( 'Content', 'residence-elementor' ),
				]
			);



                $this->add_control(
			'propertyid',
			[
                            'label' => __( 'Property ID', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
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
            'selector' => '{{WRAPPER}} .google_map_shortcode_wrapper ',
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

                $attributes['propertyid']    =   $settings['propertyid'];


              echo  wpestate_property_page_map_function($attributes);
	}


}
