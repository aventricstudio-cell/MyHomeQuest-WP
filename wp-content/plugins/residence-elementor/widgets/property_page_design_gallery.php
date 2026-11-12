<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Property_Page_Design_Gallery extends Widget_Base {

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
		return 'Property_Gallery';
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
		return __( 'Property Gallery', 'residence-elementor' );
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
		return 'wpresidence-note eicon-gallery-grid';
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
			'gallery_type',
			[
				'label' => esc_html__('Photo Gallery Type', 'residence-elementor'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__('Built In', 'residence-elementor'),
					'elementor' => esc_html__('Photo Swipe', 'residence-elementor'),
				],
				// 'description' => esc_html__('Select how many images to display per row', 'residence-elementor'),
			]
		);

		$this->add_responsive_control(
			'rownumber',
			[
				'label' => esc_html__('Images Per Row', 'residence-elementor'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '3', // desktop default
				'tablet_default' => '3',
				'mobile_default' => '2',
				'options' => [
					'1' => esc_html__('1 Column', 'residence-elementor'),
					'2' => esc_html__('2 Columns', 'residence-elementor'),
					'3' => esc_html__('3 Columns', 'residence-elementor'),
					'4' => esc_html__('4 Columns', 'residence-elementor'),
					'6' => esc_html__('6 Columns', 'residence-elementor'),
				],
				'description' => esc_html__('Select how many images to display per row', 'residence-elementor'),
			]
		);

		// $this->add_control(
		// 	'maxwidth',
		// 	[
		// 		'label' => __( 'Thumbnail max width in px (*height is auto calculated based on image ratio)', 'residence-elementor' ),
		// 		'type' => Controls_Manager::TEXT,
		// 		'label_block'=>true,
		// 			'default' => '200',
		// 	]
		// );


		$this->add_control(
		'margin',
			[
				'label' => __( 'Thumbnail right & bottom margin in px', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block'=>true,
				'default' => '10',
			]
		);


		$this->add_control(
			'image_no',
			[
				'label' => __( 'Maximum no of thumbs', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block'=>true,
				'default' => '4',
			]
		);

	$this->add_group_control(
		\Elementor\Group_Control_Typography::get_type(),
		[
			'name'     => 'img_listings_mes_typography',
			'label'    => esc_html__( 'Typography', 'residence-elementor' ),
			'selector' => '{{WRAPPER}} .img_listings_mes',
			'global'   => [
				'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
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

		$post_id = get_the_ID();
    
        if (Plugin::instance()->editor->is_edit_mode() || 
            Plugin::instance()->preview->is_preview_mode() || 
			is_singular( 'wpestate-studio' ) ||
            is_preview()) {
            
            $post_id = wpestate_return_elementor_id();
        
        }

	

		$attributes['is_elementor']      =   1;
		// $attributes['maxwidth']          =   $settings['maxwidth'];
		$attributes['margin']            =   $settings['margin'];
		$attributes['image_no']          =   $settings['image_no'];
		$attributes['rownumber']         =   $settings['rownumber'];
		$attributes['rownumber_tablet']  =   isset($settings['rownumber_tablet']) ? $settings['rownumber_tablet'] : $settings['rownumber'];
		$attributes['rownumber_mobile']  =   isset($settings['rownumber_mobile']) ? $settings['rownumber_mobile'] : $settings['rownumber'];
		$attributes['image_no'] 		 =   $settings['image_no'];
		$attributes['id']                =   $post_id;
		$attributes['gallery_type']      =   $settings['gallery_type'];

		$galleryHtml = wpestate_estate_property_design_gallery($attributes);
		
		if ( $settings['gallery_type'] === 'elementor' ) {
			$galleryHtml = str_replace('lightbox_trigger', 'elementor-lightbox', $galleryHtml);
			echo $galleryHtml;
		} else {

			$galleryHtml = preg_replace('/href=["\'][^"\']*["\']/', '', $galleryHtml);
				echo $galleryHtml;

		  print '
            <script type="text/javascript">
                //<![CDATA[
                jQuery(document).ready(function(){
                estate_start_lightbox_modal();
                });
                //]]>
            </script>';
		}




	}

}
