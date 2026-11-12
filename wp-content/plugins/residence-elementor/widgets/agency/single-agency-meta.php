<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class Wpresidence_Agency_Single_Detail extends Widget_Base {

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
		return 'Agency_Single_Detail';
	}

        public function get_categories() {
		return [ 'wpestate_single_agency_category' ];
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
		return __( 'Agency Single Detail', 'residence-elementor' );
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
        
        $single_details = array(
            'none'          =>  'none',
            'Agency Name'         =>  'realtor_name',
            'Agency Mobile'         =>  'realtor_mobile',
            'Agency Email'   =>  'email',
            'Agency Skype'    =>  'realtor_skype',
            'Agency Phone'        =>  'realtor_phone',
            'Agency Facebook'          =>  'realtor_facebook',
            'Agency Twitter'  =>  'realtor_twitter',
            'Agency Linkedin'=>  'realtor_linkedin',
            'Agency Pinterest'       =>  'realtor_pinterest',
            'Agency Instagram'=>'realtor_instagram',
            'Agency Website'           =>  'realtor_urlc',
            // 'Agency Member'       =>  'member_of',
            'Agency Address'        =>  'agent_address',
            'Agency Youtube'         =>  'realtor_youtube',
            // 'Agency Tiktok'   =>  'realtor_tiktok',
            // 'Agency Telegram'=>  'realtor_telegram',
            'Agency Vimeo'         =>  'realtor_vimeo',
            'Agency Private Notes'   =>  'realtor_private_notes',
            // 'Agency Pitch'=>  'realtor_pitch',
            'Agency Position'              =>  'realtor_position',
        );
        
        $this->add_control( 'detail', [
            'label' => __( 'Select single detail', 'residence-elementor' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'Title'  ,
            'options' => array_flip($single_details)
            ]
        );
        
        $this->add_control( 'label', [
            'label' => __( 'Element Label', 'residence-elementor' ),
            'type' => Controls_Manager::TEXT,
            'label_block'=>true,
            'default' => 'Description',
            ]
        );
        
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
                    '{{WRAPPER}} .agent_custom_detail_label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'label'     => esc_html__( 'Label Typography',  'residence-elementor' ),
                'name' => 'wpresidence_label_typography',
                'selector' => '{{WRAPPER}} .agent_custom_detail_label',
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
                    '{{WRAPPER}} .agent_custom_detail_wrapper' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'label'     => esc_html__( 'Detail Typography',  'residence-elementor' ),
                'name' => 'wpresidence_detail_typography',
                'selector' => '{{WRAPPER}} .agent_custom_detail_wrapper',
                'global'   => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
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
    
    public function wpresidence_send_to_shortcode($input)   {
        $output='';
        if($input!=='') {
            $numItems = count($input);
            $i = 0;
            
            foreach ($input as $key=>$value)    {
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
            
            $post_id = wpestate_last_agent_id('estate_agency');
        
        }
        
        $attributes['is_elementor']      =   1;
        $attributes['detail']            =   $settings['detail'];
        $attributes['label']             =   $settings['label'];
        $attributes['id']                =   $post_id;
        $attributes['type']              =   'estate_agency';
        
        echo wpestate_generate_empty_wrapper( wpestate_estate_agent_single_detail($attributes) );
	}


}
