<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Full_Map extends Widget_Base {

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
		return 'Wpresidence_Full_Map';
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
		return __( 'Map with Listings or Contact Details', 'residence-elementor' );
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
		return 'wpresidence-note eicon-post-slider';
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
                global $all_tax;

                $all_tax_elemetor=$this->elementor_transform($all_tax);



                $property_category_values       =   wpestate_generate_category_values_shortcode();
                $property_city_values           =   wpestate_generate_city_values_shortcode();
                $property_area_values           =   wpestate_generate_area_values_shortcode();
                $property_county_state_values   =   wpestate_generate_county_values_shortcode();
                $property_action_category_values=   wpestate_generate_action_values_shortcode();
                $property_status_values         =   wpestate_generate_status_values_shortcode();

                $property_category_values           =   $this->elementor_transform($property_category_values);
                $property_city_values               =   $this->elementor_transform($property_city_values);
                $property_area_values               =   $this->elementor_transform($property_area_values);
                $property_county_state_values       =   $this->elementor_transform($property_county_state_values);
                $property_action_category_values    =   $this->elementor_transform($property_action_category_values);
                $property_status_values             =   $this->elementor_transform($property_status_values);

                $map_shortcode_for=array('listings'=>'listings','contact'=>'contact');
                $map_shorcode_show_contact_form=array('yes'=>'yes','no'=>'no');

                $featured_listings  =   array('no'=>'no','yes'=>'yes');
                $items_type         =   array('properties'=>'properties','articles'=>'articles');
                $alignment_type     =   array('vertical'=>'vertical','horizontal'=>'horizontal');


		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'residence-elementor' ),
			]
		);


                $this->add_control(
			'map_shortcode_for',
			[
                            'label' => __( 'Map With Listings or Office location pin?', 'residence-elementor' ),
                            'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT,
                            'multiple'  =>   false,
                            'options'   =>  $map_shortcode_for,
                            'default'   =>  'listing'
			]
		);


                $this->add_control(
			'map_shorcode_show_contact_form',
			[
                            'label' => __( 'Show Contact Details over map ', 'residence-elementor' ),
                            'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT,
                            'options' => $map_shorcode_show_contact_form,
                            'default'   =>  'yes'
			]
		);





                $this->add_control(
			'map_height',
			[
				'label' => __( 'Map Height', 'residence-elementor' ),
                          	'type' => Controls_Manager::TEXT,
                                'Label Block'

			]
		);


                $this->add_control(
			'category_ids',
			[
                            'label' => __( 'List of categories (*only for properties)', 'residence-elementor' ),
                            'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_category_values,
			]
		);

                $this->add_control(
			'action_ids',
			[
                            'label' => __( 'List of action categories (*only for properties)', 'residence-elementor' ),
                             'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_action_category_values,
			]
		);

                $this->add_control(
			'city_ids',
			[
                            'label' => __( 'List of city  (*only for properties)', 'residence-elementor' ),
                             'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_city_values,
			]
		);
                 $this->add_control(
			'area_ids',
			[
                            'label' => __( 'List of areas (*only for properties)', 'residence-elementor' ),
                             'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_area_values,
			]
		);
                $this->add_control(
			'state_ids',
			[
                            'label' => __( 'List of Counties/States (*only for properties)', 'residence-elementor' ),
                            'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_county_state_values,
			]
		);

                $this->add_control(
			'status_ids',
			[
                            'label' => __( 'List of Property Status (*only for properties)', 'residence-elementor' ),
                            'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_status_values,
			]
		);





		$this->add_control(
			'map_snazy',
			[
				'label' => __( 'Map Style from Snazy Maps', 'residence-elementor' ),
				'type' => \Elementor\Controls_Manager::CODE,
				'language' => 'html',
				'rows' => 20,
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
            'selector' => '{{WRAPPER}} #gmap_wrapper ',
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
            if($input!=='' && is_array($input)){
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
                $attributes['map_shortcode_for']         =   $settings['map_shortcode_for'] ;
                $attributes['map_shorcode_show_contact_form']         =   $settings['map_shorcode_show_contact_form'] ;
                $attributes['map_height']         =   $settings['map_height'] ;
                $attributes['category_ids']         =   $this -> wpresidence_send_to_shortcode( $settings['category_ids'] );
                $attributes['action_ids']           =   $this -> wpresidence_send_to_shortcode( $settings['action_ids'] );
                $attributes['city_ids']             =   $this -> wpresidence_send_to_shortcode( $settings['city_ids'] );
                $attributes['area_ids']             =   $this -> wpresidence_send_to_shortcode( $settings['area_ids'] );
                $attributes['state_ids']            =   $this -> wpresidence_send_to_shortcode( $settings['state_ids'] );
                $attributes['status_ids']           =   $this -> wpresidence_send_to_shortcode( $settings['status_ids'] );
                $attributes['map_snazy']            =   $settings['map_snazy'] ;
                $attributes['is_elementor']         =   1 ;


              echo  wpestate_full_map_shortcode($attributes);
	}


}
