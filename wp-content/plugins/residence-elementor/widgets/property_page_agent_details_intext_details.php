<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Property_Page_Agent_Details_Intext extends Widget_Base {

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
		return 'Text_with_Agent_Details  ';
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
		return __( 'Text with Agent Details ', 'residence-elementor' );
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
		return 'wpresidence-note eicon-animation-text';
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

                 $agent_single_details = array(
                    'Name'          =>  'name',
                    'Main Image'    =>  'image',
                    'Description'   =>  'description',
                    'Page Link'     =>  'page_link',
                    'Agent Skype'   =>  'agent_skype',
                    'Agent Phone'   =>  'agent_phone',
                    'Agent Mobile'  =>  'agent_mobile',
                    'Agent email'   =>  'agent_email',
                    'Agent position'                =>  'agent_position',
                    'Agent Facebook'                =>  'agent_facebook',
                    'Agent Twitter'                 =>  'agent_twitter',
                    'Agent Linkedin'                => 'agent_linkedin',
                    'Agent Pinterest'               => 'agent_pinterest',
                    'Agent Instagram'               => 'agent_instagram',
                    'Agent website'                 => 'agent_website',
                    'Agent category'                => 'property_category_agent',
                    'Agent action category'         => 'property_action_category_agent',
                    'Agent city category'           => 'property_city_agent',
                    'Agent Area category'           => 'property_area_agent',
                    'Agent County/State category'   => 'property_county_state_agent'
                );
                $agent_explanations='';
                foreach($agent_single_details as $key=>$value){
                    $agent_explanations.=' for '.$key.' use this string: {'.$value.'}</br>';
                }



                $this->add_control(
			'content',
			[
                            'label' => $agent_explanations,
                            'type' => Controls_Manager::WYSIWYG ,
                            'label_block'=>true,
                            'default' => '',
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
            $attributes['content']            =   $settings['content'];

            echo  wpestate_estate_property_design_agent_details_intext_details($attributes);
	}


}
