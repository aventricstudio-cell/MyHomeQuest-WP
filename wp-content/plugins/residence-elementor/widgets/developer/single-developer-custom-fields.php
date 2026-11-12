<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Developer_Custom_Fields extends Widget_Base {

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
		return 'Developer_Custom_fields';
	}

        public function get_categories() {
		return [ 'wpestate_single_developer_category' ];
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
		return __( 'Developer Custom Fields', 'residence-elementor' );
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
        $this->start_controls_section( 'section_content', [
            'label' => __( 'Content', 'residence-elementor' ),
            ]
        );
        
        // $single_details = array(
        //     'none'          =>  'none',
        //     'Agent Name'         =>  'realtor_name',
        //     'Agent Mobile'         =>  'realtor_mobile',
        //     'Agent Email'   =>  'email',
        //     'Agent Skype'    =>  'realtor_skype',
        //     'Agent Phone'        =>  'realtor_phone',
        //     'Agent Facebook'          =>  'realtor_facebook',
        //     'Agent Twitter'  =>  'realtor_twitter',
        //     'Agent Linkedin'=>  'realtor_linkedin',
        //     'Agent Pinterest'       =>  'realtor_pinterest',
        //     'Agent Instagram'=>'realtor_instagram',
        //     'Agent Website'           =>  'realtor_urlc',
        //     'Agent Member'       =>  'member_of',
        //     'Agent Address'        =>  'agent_address',
        //     'Agent Youtube'         =>  'realtor_youtube',
        //     'Agent Tiktok'   =>  'realtor_tiktok',
        //     'Agent Telegram'=>  'realtor_telegram',
        //     'Agent Vimeo'         =>  'realtor_vimeo',
        //     'Agent Private Notes'   =>  'realtor_private_notes',
        //     'Agent Pitch'=>  'realtor_pitch',
        //     'Agent Position'              =>  'realtor_position',
        // );
        
        // $custom_fields = wpresidence_get_option( 'wp_estate_custom_fields', '');
        // if( !empty($custom_fields)){
        //     $i=0;
        //     while($i< count($custom_fields) ){
        //         $name =   $custom_fields[$i][0];
        //         $slug         =     wpestate_limit45(sanitize_title( $name ));
        //         $slug         =     sanitize_key($slug);
        //         $single_details[str_replace('-',' ',$name)]=     $slug;
        //         $i++;
        //     }
        // }
        
        // $feature_list       =   stripslashes( esc_html( get_option('wp_estate_feature_list') ) );
        // $feature_list_array =   explode( ',',$feature_list);
        
        // foreach($feature_list_array as $key => $value){
        //     $value                  =   stripslashes($value);
        //     $post_var_name          =   str_replace(' ','_', trim($value) );
        //     $input_name             =   wpestate_limit45(sanitize_title( $post_var_name ));
        //     $input_name             =   sanitize_key($input_name);
        //     $single_details[$value] =   $input_name;
        // }
        
        // $this->add_control( 'detail', [
        //     'label' => __( 'Select single detail', 'residence-elementor' ),
        //     'type' => \Elementor\Controls_Manager::SELECT,
        //     'default' => 'Title'  ,
        //     'options' => array_flip($single_details)
        //     ]
        // );
        
        // $this->add_control( 'label', [
        //     'label' => __( 'Element Label', 'residence-elementor' ),
        //     'type' => Controls_Manager::TEXT,
        //     'label_block'=>true,
        //     'default' => 'Description',
        //     ]
        // );
        
        $this->end_controls_section();

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
                    '{{WRAPPER}} .custom_parameter_label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'label'    => esc_html__( 'Label Typography', 'residence-elementor' ),
                'name'     => 'wpresidence_label_typography',
                'selector' => '{{WRAPPER}} .custom_parameter_label',
                'global'   => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
            ]
        );

        $this->add_control(
            'detail_text_color', [
                'label'     => esc_html__( 'Detail Color',  'residence-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .custom_parameter_value' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'label'     => esc_html__( 'Detail Typography',  'residence-elementor' ),
                'name' => 'wpresidence_detail_typography',
                'selector' => '{{WRAPPER}} .custom_parameter_value',
                'global'   => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
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

        if ( $settings['detail'] === 'none' || $settings['detail'] === '' ) {
            return;
        }

        $post_id = get_the_ID();
    
        if (Plugin::instance()->editor->is_edit_mode() || 
            Plugin::instance()->preview->is_preview_mode() || 
			is_singular( 'wpestate-studio' ) ||
            is_preview()) {
            
            $post_id = wpestate_last_agent_id();
        
        }
        
        if ($post_id) {
        
            // $attributes['is_elementor']      =   1;
            // $attributes['detail']            =   $settings['detail'];
            // $attributes['label']             =   $settings['label'];
            // $attributes['id']                =   $post_id;
            
            echo wpestate_generate_empty_wrapper( wpresidence_display_agent_custom_fields($post_id) );
        }
	}


}
