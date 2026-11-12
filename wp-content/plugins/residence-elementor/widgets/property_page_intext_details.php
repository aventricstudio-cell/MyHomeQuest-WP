<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Property_Page_Intext_Details extends Widget_Base {

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
		return 'Text_with_Property_Details ';
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
		return __( 'Text with Property Details ', 'residence-elementor' );
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
		return ' wpresidence-note eicon-animation-text';
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
                    'Listing ID'    =>  'listing_id',
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

            $single_details['Favorite action']   =  'favorite_action';
            $single_details['Page_views']        =  'page_views';
            $single_details['Print Action']      =  'print_action';
            $single_details['Facebook share']    =  'facebook_share';
            $single_details['Twiter share']      =  'twiter_share';
            $single_details['Google+ share']     =  'google_share';
            $single_details['Pinterest share']   =  'pinterest_share';
            $single_details['Share by email']    =  'email_share';
            $single_details['Whatsapp Share ']   =  'whatsapp_share';


            $explanations=' for Wordpress property id use this string: {prop_id}</br>';
            $explanations.=' for Property url use this string: {prop_url}</br>';
            unset($single_details['none']);
            foreach($single_details as $key=>$value){
                $explanations.=' for '.$key.' use this string: {'.$value.'}</br>';

            }




                $this->add_control(
			'content',
			[
                            'label' => $explanations,
                            'type' => Controls_Manager::WYSIWYG,

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
            echo wpestate_estate_property_design_intext_details($attributes);
	}


}
