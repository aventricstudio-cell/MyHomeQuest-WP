<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

use Elementor\Group_Control_Text_Shadow;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Single_Post_Slider extends Widget_Base {

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
		return 'Single_post_Slider';
	}

        public function get_categories() {
		return [ 'wpestate_single_post_category' ];
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
		return __( 'Single Post Slider', 'residence-elementor' );
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
		return 'wpresidence-note  eicon-slider-album ';
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
        $post_id = get_the_ID();
    
        if (Plugin::instance()->editor->is_edit_mode() || 
            Plugin::instance()->preview->is_preview_mode() || 
            is_singular( 'wpestate-studio' ) ||
            is_preview()) {
            
            $post_id = wpestate_last_post_id();
        
        }
        
        if ($post_id) {
            $settings = $this->get_settings_for_display();

            // $attributes['is_elementor']      =   1;
            // $attributes['detail']            =   $settings['detail'];
            // $attributes['showmap']           =   $settings['showmap'];


			$content = wpestate_display_post_slider($post_id, $settings);

			if (empty($content)) {
				$content = esc_html__('Slider does not have any images', 'residence-elementor');
			}

			   // Echo inline CSS
        	echo '<style>
            .item.carousel-item {
                text-align: center;
            }
        	</style>';

			echo wpestate_generate_empty_wrapper($content);
        }
	}

	
}
