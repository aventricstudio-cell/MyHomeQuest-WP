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
use Elementor\Plugin;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Site_Social extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Site_Social';
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
        return esc_html__('Social Networks Icons', 'residence-elementor');
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
        return 'wpresidence-note eicon-site-logo';
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
        return ['wpresidence_header'];
    }

    /**
     * Register widget controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {
       
            $this->start_controls_section(
                    'section_content',
                    [
                            'label' => __( 'Content',  'residence-elementor' ),
                    ]
            );
            $defaults = array(  
                                    'facebook'      => esc_html__('Facebook Link:','wpresidence_core'),
                                    'whatsup'       => esc_html__('WhatsApp Link:','wpresidence_core'),
                                    'telegram'      => esc_html__('Telegram Link:','wpresidence_core'),
                                    'tiktok'        => esc_html__('TikTok Link:','wpresidence_core'),
                                    'rss'           => esc_html__('Rss Link:','wpresidence_core'),
                                    'twitter'       => esc_html__('x - Twitter Link:','wpresidence_core'),
                                    'dribbble'      => esc_html__('Dribble Link:','wpresidence_core'),
                                    'google'        => esc_html__('Google+ Link:','wpresidence_core'),
                                    'linkedIn'      => esc_html__('LinkedIn Link:','wpresidence_core'),                            
                                    'tumblr'        => esc_html__('Tumblr Link:','wpresidence_core'),
                                    'pinterest'     => esc_html__('Pinterest Link:','wpresidence_core'),                               
                                    'youtube'       => esc_html__('Youtube Link:','wpresidence_core'),
                                    'vimeo'         => esc_html__('Vimeo Link:','wpresidence_core'),
                                    'instagram'     => esc_html__('Instagram Link:','wpresidence_core'),
                                    'foursquare'    => esc_html__('Foursquare Link:','wpresidence_core'),                  
                                    'line'          => esc_html__('Line Link:','wpresidence_core'),
                                    'wechat'        => esc_html__('WeChat Link:','wpresidence_core'),
                                    );
		
                
             foreach ($defaults as $key => $label) {
                $this->add_control(
                    $key . '_link',
                    [
                        'label' => $label,
                        'type' => \Elementor\Controls_Manager::TEXT,
                    ]
                );
            }

            $this->end_controls_section();
            
           $this->start_controls_section(
                'section_style', [
                'label' => esc_html__('Style',  'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                ]
            );
           
            $this->add_responsive_control(
                'wpersidence_form_column_gap', [
            'label' => esc_html__('Icon size',  'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 14,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .wpresidence_elementor_social_sidebar_internal a' => 'font-size:  {{SIZE}}{{UNIT}};',
           ],
                ]
        );
                  
                  
            $this->add_control(
                  'unit_color',
                  [
                      'label'     => esc_html__( 'Color',  'residence-elementor' ),
                      'type'      => Controls_Manager::COLOR,
                      'default'   => '',
                      'selectors' => [
                          '{{WRAPPER}} .wpresidence_elementor_social_sidebar_internal a' => 'color: {{VALUE}}',

                      ],
                  ]
              );
              
            
            $this->add_control(
            'unit_bck_color',
            [
                'label'     => esc_html__( 'Background Color',  'residence-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_elementor_social_sidebar_internal a' => 'background-color: {{VALUE}}',
                ],
            ]
            );
            

           // Add Hover Icon Color control
            $this->add_control(
                'hover_icon_color',
                [
                    'label' => __( 'Hover Icon Color', 'residence-elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wpresidence_elementor_social_sidebar_internal a:hover' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .wpresidence_elementor_social_sidebar_internal a:hover i' => 'color: {{VALUE}}',
                    ],
                ]
            );

            // Add Hover Background Color control
            $this->add_control(
                'hover_background_color',
                [
                    'label' => __( 'Hover Background Color', 'residence-elementor' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .wpresidence_elementor_social_sidebar_internal a:hover' => 'background-color: {{VALUE}}',
                    ],
                ]
            );
              
            $this->add_control(
                'margin_excerpt', [
            'label' => esc_html__('Margin between icons', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .wpresidence_elementor_social_sidebar_internal a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .wpresidence_elementor_social_sidebar_internal' => 'gap: 0;',
            
            ],
                ]
        );
            
             // Border right control
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_right',
                'label' => __( 'Border Right',  'residence-elementor' ),
                'selector' => '{{WRAPPER}} .social_sidebar_internal a',
            ]
        );

    // Add Hover Border Color control
    $this->add_control(
        'hover_border_color',
        [
            'label' => __( 'Hover Border Color', 'residence-elementor' ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .wpresidence_elementor_social_sidebar_internal a:hover' => 'border-color: {{VALUE}}',
            ],
        ]
    );   

    // Add Border Radius control
    $this->add_control(
        'border_radius',
        [
            'label' => __( 'Border Radius', 'residence-elementor' ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'selectors' => [
                '{{WRAPPER}} .wpresidence_elementor_social_sidebar_internal a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

       // Add Width control
       $this->add_responsive_control(
        'width',
        [
            'label' => __( 'Width', 'residence-elementor' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 200,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .wpresidence_elementor_social_sidebar_internal a' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    // Add Height control
    $this->add_responsive_control(
        'height',
        [
            'label' => __( 'Height', 'residence-elementor' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 200,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .wpresidence_elementor_social_sidebar_internal a' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    // Add Line-Height control
    $this->add_responsive_control(
        'line_height',
        [
            'label' => __( 'Line Height', 'residence-elementor' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 200,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .wpresidence_elementor_social_sidebar_internal a' => 'line-height: {{SIZE}}{{UNIT}}; padding: 0;',
            ],
        ]
    );

            $this->end_controls_section();
          

    }


  

/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 
	 * @since 1.0.0
	 *
	 * @access protected
	 */

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        
        // Create the $instance array
        $instance = array(
            'facebook'   => $settings['facebook_link'],
            'whatsup'    => $settings['whatsup_link'],
            'telegram'   => $settings['telegram_link'],
            'tiktok'     => $settings['tiktok_link'],
            'rss'        => $settings['rss_link'],
            'twitter'    => $settings['twitter_link'],
            'dribbble'   => $settings['dribbble_link'],
            'google'     => $settings['google_link'],
            'linkedIn'   => $settings['linkedIn_link'],
            'tumblr'     => $settings['tumblr_link'],
            'pinterest'  => $settings['pinterest_link'],
            'youtube'    => $settings['youtube_link'],
            'vimeo'      => $settings['vimeo_link'],
            'instagram'  => $settings['instagram_link'],
            'foursquare' => $settings['foursquare_link'],
            'line'       => $settings['line_link'],
            'wechat'     => $settings['wechat_link'],
        );

        // Call the function to display the social icons
        echo wpestate_get_social_icons_widgets_elementor($instance);
    }


}
