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
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;

use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Plugin;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Single_Agency_Social extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Single_agency_social';
    }

    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__('Single Agency Social', 'residence-elementor');
    }

    /**
     * Get widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'wpresidence-note eicon-social-icons';
    }

    /**
     * Get widget categories.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return ['wpestate_single_agency_category'];
    }

    /**
     * Register widget controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {
        
        // $this->start_controls_section(
        //             'section_content',
        //             [
        //                     'label' => __( 'Agency Name',  'residence-elementor' ),
        //             ]
        // );

        // $this->add_control(
        //     'html_tag',
        //     [
        //         'label'     => esc_html__( 'HTML Tag',  'residence-elementor' ),
        //         'type'      => Controls_Manager::SELECT,
        //         'default'   => 'h1',
        //         'options'   => [
        //             'h1' => [
        //                 'title' => esc_html__( 'H1',  'residence-elementor' ),
        //                 'icon'  => 'eicon-editor-h1',
        //             ],
        //             'h2' => [
        //                 'title' => esc_html__( 'H2',  'residence-elementor' ),
        //                 'icon'  => 'eicon-editor-h2',
        //             ],
        //             'h3' => [
        //                 'title' => esc_html__( 'H3',  'residence-elementor' ),
        //                 'icon'  => 'eicon-editor-h3',
        //             ],
        //             'h4' => [
        //                 'title' => esc_html__( 'H4',  'residence-elementor' ),
        //                 'icon'  => 'eicon-editor-h4',
        //             ],
        //             'h5' => [
        //                 'title' => esc_html__( 'H5',  'residence-elementor' ),
        //                 'icon'  => 'eicon-editor-h5',
        //             ],
        //             'h6' => [
        //                 'title' => esc_html__( 'H6',  'residence-elementor' ),
        //                 'icon'  => 'eicon-editor-h6',
        //             ],
        //             'span' => [
        //                 'title' => esc_html__( 'span',  'residence-elementor' ),
        //                 'icon'  => 'eicon-editor-span',
        //             ],
        //             'div' => [
        //                 'title' => esc_html__( 'div',  'residence-elementor' ),
        //                 'icon'  => 'eicon-editor-div',
        //             ],
        //             'p' => [
        //                 'title' => esc_html__( 'p',  'residence-elementor' ),
        //                 'icon'  => 'eicon-editor-p',
        //             ],
        //         ],
        //         // 'selectors' => [
        //         //     '{{WRAPPER}} a' => 'color: {{VALUE}}',
        //         //     '{{WRAPPER}} .header_phone i' => 'color: {{VALUE}}',
        //         //     '{{WRAPPER}} .header_phone svg' => 'fill: {{VALUE}}',
        //         // ],
        //         ]
        //     );
        
        
        
        // $this->end_controls_section();
        
          
        $this->start_controls_section(
            'section_style', [
                'label' => esc_html__('Style',  'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'residence-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'residence-elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'residence-elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'residence-elementor' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'residence-elementor' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => '',
				'selectors' => '{{WRAPPER}} .agency_unit_social_single',
				// 'separator' => 'after',
			]
		);

        $this->add_control(
			'space_between',
            [
                'label' => esc_html__( 'Space Between Icons', 'residence-elementor' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .agency_unit_social_single a:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
		);

        $this->add_control(
			'icon_size',
			[
				'label' => esc_html__( 'Icon size', 'residence-elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 72,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 14,
				],
				'selectors' => [
					'{{WRAPPER}} .agency_elementor_icom' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
            'text_color', [
                'label'     => esc_html__( 'Color',  'residence-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    // '{{WRAPPER}} .wpresidence-single-post-title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .agency_elementor_icom' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color_hover', [
                'label'     => esc_html__( 'Color',  'residence-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    // '{{WRAPPER}} .wpresidence-single-post-title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .agency_elementor_icom:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'wpresidence_tab_item_typography',
                'selectors' => '{{WRAPPER}} .agency_elementor_icom',
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
  protected function render() {
    $post_id = get_the_ID();
    
 
    
    if (Plugin::instance()->editor->is_edit_mode() || 
        Plugin::instance()->preview->is_preview_mode() || 
        is_singular( 'wpestate-studio' ) ||
        is_preview()) {
        
        $post_id = wpestate_last_agent_id('estate_agency');
       
    }
    
    if ($post_id) {
        $settings = $this->get_settings_for_display();

        $details = wpestate_return_agent_details(0, $post_id);
        
        echo wpestate_generate_empty_wrapper( wpestate_return_agent_share_social_icons( $details, 'agency_unit_social_single', 'agency_elementor_icom') );
    }

// use the above post_it to get all post details you need

}

}