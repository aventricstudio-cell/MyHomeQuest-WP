<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Wpresidence_Advanced_Search extends Widget_Base {

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
		return 'WpResidence_Advanced_Search';
	}

        public function get_categories() {
		return [ 'wpresidence' ];
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
		return __( 'Advanced Search', 'residence-elementor' );
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
		return 'wpresidence-note   eicon-search';
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

        $this->start_controls_section(
            'section_grid_colors',
            [
                'label' => esc_html__( 'Colors', 'residence-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'unit_color',
            [
                'label'     => esc_html__( 'Background Color', 'residence-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .advanced_search_shortcode' => 'background-color: {{VALUE}}',

                ],
            ]
        );
            
        $this->add_control(
            'font_color',
            [
                'label'     => esc_html__( 'Font Color', 'residence-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .advanced_search_shortcode' => 'color: {{VALUE}}',
                    '{{WRAPPER}} label' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .wpresidence_slider_price' => 'color: {{VALUE}}',
                    '{{WRAPPER}} #amount_sh' => 'color: {{VALUE}}!important',
                    '{{WRAPPER}} .adv_extended_options_text' => 'color: {{VALUE}}',
                    '{{WRAPPER}}  .residence_adv_extended_options_text' => 'color: {{VALUE}}',
                    '{{WRAPPER}}  .extended_search_check_wrapper .adv_extended_close_button' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .ui-widget-header' => 'background: {{VALUE}}!important',
                ],
            ]
        );
              
              
        $this->add_responsive_control(
            'image_border_radius', [
                'label' => __('Border Radius',  'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .advanced_search_shortcode' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        

        $this->end_controls_section();

        
         /*
              *-------------------------------------------------------------------------------------------------
              * End Spacing section
              */
        
         /*
              *-------------------------------------------------------------------------------------------------
              * Load more section
              */
              $this->start_controls_section(
                'section_load_more', [
            'label' => esc_html__('Search Button', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        $this->add_control(
            'load_more_bg_color', [
                'label' => esc_html__('Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_button' => 'background-color: {{VALUE}};background-image:linear-gradient(to right, transparent 50%, {{VALUE}} 50%);border-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'load_more_color', [
                'label' => esc_html__('Text Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_button' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'load_more_bg_color_hover', [
                'label' => esc_html__('Hover Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_button:hover' => 'background-color: {{VALUE}};border-color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'load_more_color_hover', [
                'label' => esc_html__('Hover Text Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_section();
              /*
              *-------------------------------------------------------------------------------------------------
              * End Load more section
              */
               
               
               
               
              /*
              *-------------------------------------------------------------------------------------------------
              * Start shadow section
              */
              $this->start_controls_section(
                'section_grid_box_shadow',
                [
                    'label' => esc_html__( 'Box Shadow', 'residence-elementor' ),
                    'tab'   => Controls_Manager::TAB_STYLE,
                ]
                );
                $this->add_group_control(
                    Group_Control_Box_Shadow::get_type(),
                    [
                        'name'     => 'box_shadow',
                        'label'    => esc_html__( 'Box Shadow', 'residence-elementor' ),
                        'selector' => '{{WRAPPER}} .advanced_search_shortcode',
                    ]
                );

                $this->end_controls_section();
              /*
              *-------------------------------------------------------------------------------------------------
              * End shadow section
              */
                
                
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

           $attributes=array();
            echo  wpestate_advanced_search_function($attributes);

            
    
        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) :   
            echo  "<script>  jQuery('.wpestate-selectpicker').selectpicker('destroy');wpestate_advnced_filters_bars();  </script>";
        endif;
            
	}

	
}
