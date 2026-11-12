<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Property_Page_Simple_Detail extends Widget_Base {

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
		return 'Property_Single_Detail';
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
		return __( 'Property Single Detail', 'residence-elementor' );
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
		return 'wpresidence-note eicon-product-price';
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

                $single_details = array(
                    'none'          =>  'none',
                    'Title'         =>  'title',
                    'Description'   =>  'description',
                    'Categories'    =>  'property_category',
                    'Action'        =>  'property_action_category',
                    'City'          =>  'property_city',
                    'Neighborhood'  =>  'property_area',
                    'County / State'=>  'property_county_state',
                    'Address'       =>  'property_address',
                    'Energy Certificate'=>'energy_certificate',
                    'Zip'           =>  'property_zip',
                    'Country'       =>  'property_country',
                    'Status'        =>  'property_status',
                    'Price'         =>  'property_price',
                    'Price Label'   =>  'property_label',
                    'Price Label before'=>  'property_label_before',
                    'Additional Price Info'         =>  'property_second_price',
                    'Additional Price Info Label'   =>  'property_label_before_second_price',
                    'Additional Price Info Label before'=>  'property_second_price_label',
                    'Size'              =>  'property_size',
                    'Lot Size'          =>  'property_lot_size',
                    'Rooms'             =>  'property_rooms',
                    'Bedrooms'          =>  'property_bedrooms',
                    'Bathrooms'         =>  'property_bathrooms',
                    'Download Pdf'      =>  'property_pdf',
                    'Agent'             =>  'property_agent',
                    'Video'             =>  'property_video'

            );

            $custom_fields = wpresidence_get_option( 'wp_estate_custom_fields', '');
            if( !empty($custom_fields)){
                $i=0;
                while($i< count($custom_fields) ){
                    $name =   $custom_fields[$i][0];
                    $slug         =     wpestate_limit45(sanitize_title( $name ));
                    $slug         =     sanitize_key($slug);
                    $single_details[str_replace('-',' ',$name)]=     $slug;
                    $i++;
               }
            }

            $feature_list       =   stripslashes( esc_html( get_option('wp_estate_feature_list') ) );
            $feature_list_array =   explode( ',',$feature_list);

            foreach($feature_list_array as $key => $value){
                $value                  =   stripslashes($value);
                $post_var_name          =   str_replace(' ','_', trim($value) );
                $input_name             =   wpestate_limit45(sanitize_title( $post_var_name ));
                $input_name             =   sanitize_key($input_name);
                $single_details[$value] =   $input_name;
            }





                $this->add_control(
			'detail',
			[
                            'label' => __( 'Select single detail', 'residence-elementor' ),
                            'type' => \Elementor\Controls_Manager::SELECT,
                            'default' => 'Title'  ,
                            'options' => array_flip($single_details)
			]
		);

                $this->add_control(
			'label',
			[
                            'label' => __( 'Element Label', 'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                            'label_block'=>true,
                            'default' => 'Description',
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
            $attributes['detail']            =   $settings['detail'];
            $attributes['label']             =   $settings['label'];




            echo  wpestate_estate_property_simple_detail($attributes);
	}


}
