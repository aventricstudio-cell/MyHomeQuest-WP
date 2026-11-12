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


class Wpresidence_Property_Page_Agent_Contact extends Widget_Base {

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
		return 'Agent_Contact_Form';
	}

        public function get_categories() {
		return [ 'wpresidence_property' ];
	}

    public function get_html_wrapper_class() {
        return parent::get_html_wrapper_class() . ' wpestate_contact_form_parent';
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
		return __( 'Agent Contact Form', 'residence-elementor' );
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
		return 'wpresidence-note eicon-form-horizontal';
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
            'hide_schedule_showing', [
                'label' => esc_html__('Hide Schedule a Showing Button', 'residence-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'residence-elementor'),
                'label_off' => esc_html__('No', 'residence-elementor'),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .schedule_meeting' => 'display: {{VALUE}};',
                ],
            ]
        );



		$this->end_controls_section();

        /*
        *-------------------------------------------------------------------------------------------------
        * End shadow section
        */
               
         
        $this->start_controls_section(
            'typography_section',
            [
                'label'     => esc_html__( 'Typography', 'residence-elementor' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'contact_form_title',
                'label' => esc_html__('Contact Form Title', 'residence-elementor'),
               'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                'selector' => '{{WRAPPER}} #show_contact',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'form_font',
                'label' => esc_html__('Form Font', 'residence-elementor'),
               'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                'selector' => '{{WRAPPER}} .form-control, {{WRAPPER}} .form-select', // Combine selectors as a single string
            ]
        );

      
     $this->end_controls_section();

        /*
        * -------------------------------------------------------------------------------------------------
        * Start color section
        */
        $this->start_controls_section(
            'section_colors', [
                'label' => esc_html__('Colors', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'unit_font_form_color', [
                'label' => esc_html__('Form Font Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .form-control' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .form-select' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .form-control::placeholder' => 'color: {{VALUE}}', // Apply to placeholder as well
                ],
            ]
        );

        $this->add_control(
            'contact_me_color', [
                'label' => esc_html__('Contact Me Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#222222',
                'selectors' => [
                    '{{WRAPPER}} #show_contact' => 'color: {{VALUE}}',
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
            $attributes['is_elementor']      =   1;


            echo  wpestate_estate_property_design_agent_contact($attributes);
	}


}
