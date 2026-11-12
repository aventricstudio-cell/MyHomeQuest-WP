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


class Wpresidence_Agent_Reviews extends Widget_Base {

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
		return 'Wpresidence_Agent_Reviews';
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
		return __( 'Agent Reviews', 'residence-elementor' );
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
		return 'wpresidence-note eicon-review';
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
	return [ 'wpestate_property' ];
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



        protected function register_controls() {


        //         $taxonomy_list_type         =   array(
        //             'category'          =>  'category',
        //             'action category'   =>  'action category',
        //             'city'              =>  'city',
        //             'area'              =>  'area',
        //             'county/state'      =>  'county/state',
        //             'status'            =>  'status',
        //             'features and ammenities'=>'features and amenities');
        //         $taxonomy_list_type_show    =   array('yes'=>'yes','no'=>'no');



		// $this->start_controls_section(
		// 	'section_content',
		// 	[
		// 		'label' => __( 'Content', 'residence-elementor' ),
		// 	]
		// );

		// $this->add_control(
		// 	'taxonomy_list_type',
		// 	[
        //                     'label' => __( 'Select category', 'residence-elementor' ),
        //                     'label_block'=>true,
        //                     'type' => \Elementor\Controls_Manager::SELECT,

        //                     'options' => $taxonomy_list_type,
        //                      'default' => 'category',
		// 	]
		// );


        //         $this->add_control(
		// 	'taxonomy_list_type_show',
		// 	[
        //                     'label' => __( 'Show number of listings?', 'residence-elementor' ),
        //                     'label_block'=>true,
        //                     'type' => \Elementor\Controls_Manager::SELECT,

        //                     'options' => $taxonomy_list_type_show,
        //                      'default' => 'yes',
		// 	]
		// );






		// $this->end_controls_section();


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

            Plugin::instance()->db->switch_to_post( $post_id );

            $settings = $this->get_settings_for_display();

            echo wpestate_load_agent_reviews_template( $post_id, $settings );

            Plugin::instance()->db->restore_current_post();
        }
	}


}
