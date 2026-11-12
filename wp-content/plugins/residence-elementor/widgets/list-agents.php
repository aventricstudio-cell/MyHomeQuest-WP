<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_List_Agents extends Widget_Base {

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
		return 'WpResidence_List_Agents';
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
		return __( 'List Agents', 'residence-elementor' );
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
		return 'wpresidence-note eicon-person';
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


            $property_category_agent='';
            $out_agent_tax_array= wpestate_js_composer_out_agent_tax_array();
            if(isset($out_agent_tax_array['property_category_agent'])){
                $property_category_agent= $out_agent_tax_array['property_category_agent'];
            }
            $property_category_agent_elementor=$this->elementor_transform($property_category_agent);


            $property_action_category_agent='';
            if( isset($out_agent_tax_array['property_action_category_agent'])){
                $property_action_category_agent=$out_agent_tax_array['property_action_category_agent'];
            }
            $property_action_category_agent_elementor=$this->elementor_transform($property_action_category_agent);


            $property_city_agent='';
            if(isset( $out_agent_tax_array['property_city_agent'])){
                $property_city_agent= $out_agent_tax_array['property_city_agent'];
            }
            $property_city_agent_elementor=$this->elementor_transform($property_city_agent);

            $property_area_agent='';
            if( isset( $out_agent_tax_array['property_area_agent'])){
               $property_area_agent= $out_agent_tax_array['property_area_agent'];
            }
            $property_area_agent_elementor=$this->elementor_transform($property_area_agent);




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
			'title',
			[
				'label' => __( 'Title', 'residence-elementor' ),
                          	'type' => Controls_Manager::TEXT,
                                'Label Block'

			]
		);



                $this->add_control(
			'category_ids',
			[
                            'label' => __( 'Type Category names', 'residence-elementor' ),
                            'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_category_agent_elementor,
			]
		);

                $this->add_control(
			'action_ids',
			[
                            'label' => __( 'Type Action name', 'residence-elementor' ),
                             'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_action_category_agent_elementor,
			]
		);

                $this->add_control(
			'city_ids',
			[
                            'label' => __( 'Type City names', 'residence-elementor' ),
                             'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_city_agent_elementor,
			]
		);
                 $this->add_control(
			'area_ids',
			[
                            'label' => __( 'Type Area names', 'residence-elementor' ),
                             'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_area_agent_elementor,
			]
		);


                $this->add_control(
			'number',
			[
                            'label' => __( 'No of items', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                            'default' => 6,
			]
		);

                $this->add_control(
			'rownumber',
			[
				'label' => __( 'No of items per row', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
                'default' => 3,
			]
		);
                $this->add_control(
			'link',
			[
				'label' => __( 'Link to global listing', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
			]
		);

        $this->add_control(
            'order_by', [
                'label' => esc_html__('Order by', 'residence-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'ID' => esc_html__('ID', 'residence-elementor'),
                    'title' => esc_html__('Title/Name', 'residence-elementor'),
                    'date' => esc_html__('Date', 'residence-elementor')
                ],
                'default' => 'ID',
            ]
        );
        
        $this->add_control(
            'order', [
                'label' => esc_html__('Order', 'residence-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'ASC' => esc_html__('ASC', 'residence-elementor'),
                    'DESC' => esc_html__('DESC', 'residence-elementor')
                ],
                'default' => 'ASC',
            ]
        );
        $this->add_control(
			'random_pick',
			[
                            'label' => __( 'Random Pick ?', 'residence-elementor' ),
                            'type' => \Elementor\Controls_Manager::SELECT,
                            'default' => 'no',
                            'options' => $featured_listings
			]
		);

$this->add_control(
                'display_grid',
                [
                    'label' => esc_html__('Display as grid ?', 'residence-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Yes', 'residence-elementor'),
                    'label_off' => esc_html__('No', 'residence-elementor'),
                    'return_value' => 'yes',
                    'default' => esc_html__('No', 'residence-elementor'),
                    'description'=> esc_html__('There is no fixed number of units. The grid will auto adjust to display units with a minimum width set by the control below.?', 'residence-elementor'),
                ]
        );

        $this->add_responsive_control(
                'display_grid_unit_width',
                [
                    'label' => esc_html__('Unit Minimum Width', 'residence-elementor'),
                    'condition' => [
                        'display_grid' => 'yes'
                    ],
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 220,
                            'max' => 400,
                        ],
                    ],
                    'devices' => ['desktop', 'tablet', 'mobile'],
                    'desktop_default' => [
                        'size' => '250',
                        'unit' => 'px',
                    ],
                    'tablet_default' => [
                        'size' => '250',
                        'unit' => 'px',
                    ],
                    'mobile_default' => [
                        'size' => '250',
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .items_shortcode_wrapper_grid' => '  grid-template-columns: repeat(auto-fit, minmax({{SIZE}}{{UNIT}}, 1fr));',
                    ],
                    
                ]
        );

        $this->add_responsive_control(
                'display_grid_unit_gap',
                [
                    'label' => esc_html__('Gap between units in px', 'residence-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'condition' => [
                        'display_grid' => 'yes'
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'devices' => ['desktop', 'tablet', 'mobile'],
                    'desktop_default' => [
                        'size' => '10',
                        'unit' => 'px',
                    ],
                    'tablet_default' => [
                        'size' => '10',
                        'unit' => 'px',
                    ],
                    'mobile_default' => [
                        'size' => '10',
                        'unit' => 'px',
                    ],
                    'default' => [
					'unit' => 'px',
					'size' => 10,
				],
                    'selectors' => [
                        '{{WRAPPER}} .items_shortcode_wrapper_grid' => 'gap: {{SIZE}}{{UNIT}};',
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
            'selector' => '{{WRAPPER}} .agent_unit ',
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


                $attributes['title']                =   $settings['title'];
                $attributes['category_ids']         =   $this -> wpresidence_send_to_shortcode( $settings['category_ids'] );
                $attributes['action_ids']           =   $this -> wpresidence_send_to_shortcode( $settings['action_ids'] );
                $attributes['city_ids']             =   $this -> wpresidence_send_to_shortcode( $settings['city_ids'] );
                $attributes['area_ids']             =   $this -> wpresidence_send_to_shortcode( $settings['area_ids'] );
                $attributes['number']               =   $settings['number'];
                $attributes['rownumber']            =   $settings['rownumber'];
                $attributes['link']                 =   $settings['link'];
                $attributes['random_pick']          =   $settings['random_pick'];
                $attributes['orderby']                =   $settings['order_by'];
                $attributes['order']                =   $settings['order'];
                $attributes['unit_type']            =  '';

                $attributes['display_grid']         =   $settings['display_grid'];
              echo  wpestate_list_agents_function($attributes);
	}

	
}
