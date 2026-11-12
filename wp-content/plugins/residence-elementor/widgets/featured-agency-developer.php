<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography; // Include this for typography controls

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Featured_Agency_Developer extends Widget_Base {

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
		return 'WpResidence_Featured_Agency_Developer';
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
                    return __( 'Featured Agency/Developer', 'residence-elementor' );
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


                if($agency_developers_array===false){
                    $args_inner = array(
                            'post_type' => array( 'estate_agency', 'estate_developer' ),
                            'showposts' => -1
                    );
                    $all_agency_dev_packages = get_posts( $args_inner );
                    if( count($all_agency_dev_packages) > 0 ){
                            $agency_developers_array=array();
                            foreach( $all_agency_dev_packages as $single_package ){
                                    $temp_array=array();
                                    $temp_array['label'] = $single_package->post_title;
                                    $temp_array['value'] = $single_package->ID;

                                    $agency_developers_array[] = $temp_array;
                            }
                    }

                    if(function_exists('wpestate_set_transient_cache')){
                       wpestate_set_transient_cache ('wpestate_js_composer_article_agency_developers_array',$agency_developers_array,60*60*4);
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
			'user_role_id',
			[
				'label' => __( 'Type Agency or Developer name', 'residence-elementor' ),
				'label_block'=>true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $agency_developers_array_elementor,
			]
		);

		$this->add_control(
			'status',
			[
				'label' => __( 'Status Text Label', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
			]
		);


		$this->add_control(
			'user_shortcode_imagelink',
			[
				'label' => __( 'Image ', 'residence-elementor' ),
				'type' => Controls_Manager::MEDIA,
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
				'selector' => '{{WRAPPER}} .user_role_unit ',
			]
        );

		$this->end_controls_section();

		/*
		* -------------------------------------------------------------------------------------------------
		* Start typography section
		*/
		
		$this->start_controls_section(
			'section_typography', [
				'label' => esc_html__('Typography', 'residence-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		// H4 Typography Control
		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name' => 'typography_h4',
				'label' => esc_html__('Title Typography', 'residence-elementor'),
				'selector' => '{{WRAPPER}} .user_role_unit .featured_user_role_unit_details h4 a',
			]
		);
		
		// User Role Typography Control for Phone and Email
		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name' => 'typography_contact_details',
				'label' => esc_html__('Contact Details Typography', 'residence-elementor'),
				'selector' => '{{WRAPPER}} .user_role_unit .featured_user_role_unit_details .user_role_phone a, 
							   {{WRAPPER}} .user_role_unit .featured_user_role_unit_details .user_role_email a',
			]
		);
		
		// User Role Content Typography Control
		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name' => 'typography_user_role_content',
				'label' => esc_html__('Description Typography', 'residence-elementor'),
				'selector' => '{{WRAPPER}} .user_role_unit .user_role_content',
			]
		);

		// Typography control
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'user_role_status_typography',
				'label' => __( 'Status Text Label Typography', 'residence-elementor' ),
				'selector' => '{{WRAPPER}} .user_role_unit .user_role_status',
			]
		);
	
		// Text color control
		$this->add_control(
			'user_role_status_color',
			[
				'label' => __( 'Status Text Label Color', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff', // Default color
				'selectors' => [
					'{{WRAPPER}} .user_role_unit .user_role_status' => 'color: {{VALUE}};',
				],
			]
		);
	
		// Background color control
		$this->add_control(
			'user_role_status_bg_color',
			[
				'label' => __( 'Status Background Color', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#69c17d', // Default background color
				'selectors' => [
					'{{WRAPPER}} .user_role_unit .user_role_status' => 'background-color: {{VALUE}};',
				],
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

                $attributes['user_role_id']         =   $this -> wpresidence_send_to_shortcode( $settings['user_role_id'] );
                $attributes['status']    =   $settings['status'];
                $attributes['user_shortcode_imagelink']   =   $settings['user_shortcode_imagelink']['url'];



              echo  wpestate_featured_agency_developer($attributes);
	}


}
