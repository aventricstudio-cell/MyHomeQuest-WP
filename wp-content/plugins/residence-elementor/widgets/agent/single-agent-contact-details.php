<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Agent_Contact_Details extends Widget_Base {

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
		return 'Wpresidence_Agent_Contact_Details';
	}

        public function get_categories() {
		return [ 'wpestate_single_agent_category' ];
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
		return __( 'Agent Contact Details', 'residence-elementor' );
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
		return 'wpresidence-note eicon-kit-details';
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
        // public function elementor_transform($input){
        //     $output=array();
        //     if( is_array($input) ){
        //         foreach ($input as $key=>$tax){
        //             $output[$tax['value']]=$tax['label'];
        //         }
        //     }
        //     return $output;
        // }
        // 
    
    protected function register_controls() {


        $this->start_controls_section(
            'section_style', [
                'label' => esc_html__('Style',  'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'label_text_color', [
                'label'     => esc_html__( 'Label Color',  'residence-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .agent_detail strong' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'label'     => esc_html__( 'Label Typography',  'residence-elementor' ),
                'name' => 'wpresidence_label_typography',
                'selector' => '{{WRAPPER}} .agent_detail strong',
                'global'   => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
            ]
        );

        $this->add_control(
            'detail_text_color', [
                'label'     => esc_html__( 'Detail Color',  'residence-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .agent_detail a, {{WRAPPER}} .agent_detail' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .agent_detail svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'label'     => esc_html__( 'Detail Typography',  'residence-elementor' ),
                'name' => 'wpresidence_detail_typography',
                'selector' => '{{WRAPPER}} .agent_detail a, {{WRAPPER}} .agent_detail',
                'global'   => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
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

        $post_id = get_the_ID();
    
 
    
        if (Plugin::instance()->editor->is_edit_mode() || 
            Plugin::instance()->preview->is_preview_mode() || 
            is_singular( 'wpestate-studio' ) ||
            is_preview()) {
            
            $post_id = wpestate_last_agent_id();
        
        }
        
        if ($post_id) {

            $details = wpestate_return_agent_details(0, $post_id);

            echo wpestate_return_agent_contact_details( $details );
        }
	}


}
