<?php
namespace ElementorStudioWidgetsWpResidence\Widgets;

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

class Wpresidence_Single_Post_Title extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Single_post_title';
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
        return esc_html__('Single Post title', 'wpestate-studio-templates');
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
        return ['wpresidence_single_post'];
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
                            'label' => __( 'Content',  'wpestate-studio-templates' ),
                    ]
        );
        
     
        
        $this->end_controls_section();
        
          
        $this->start_controls_section(
                'section_style', [
                'label' => esc_html__('Style',  'wpestate-studio-templates'),
                'tab' => Controls_Manager::TAB_STYLE,
                ]
            );
           
            $this->add_responsive_control(
                'Icon_size', [
                    'label' => esc_html__('Icon size',  'wpestate-studio-templates'),
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
                        '{{WRAPPER}} .header_phone i' => 'font-size:  {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .header_phone svg' => 'height:  {{SIZE}}{{UNIT}};',
                   ],
                ]
         );
                $this->add_group_control(
                    Group_Control_Typography::get_type(), [
                        'name' => 'wpresidence_tab_item_typography',
                        'selector' => '{{WRAPPER}} a',
                       'global'   => [
            'default' => Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                    ]
                ); 
                   
                  
            $this->add_control(
                  'unit_color',
                  [
                      'label'     => esc_html__( 'Color',  'wpestate-studio-templates' ),
                      'type'      => Controls_Manager::COLOR,
                      'default'   => '',
                      'selectors' => [
                            '{{WRAPPER}} a' => 'color: {{VALUE}}',
                            '{{WRAPPER}} .header_phone i' => 'color: {{VALUE}}',
                            '{{WRAPPER}} .header_phone svg' => 'fill: {{VALUE}}',
                      ],
                  ]
              );
              
             // Add Hover Color Control
            $this->add_control(
                'unit_hover_color',
                [
                    'label'     => esc_html__( 'Hover Color',  'wpestate-studio-templates' ),
                    'type'      => Controls_Manager::COLOR,
                    'default'   => '',
                    'selectors' => [
                        '{{WRAPPER}} a:hover' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .header_phone i:hover' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .header_phone svg:hover' => 'fill: {{VALUE}}',
                    ],
                ]
            );
         
              
            $this->add_control(
                'margin_excerpt', [
            'label' => esc_html__('Margin for Phone no ', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .header_phone a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        is_preview()) {
        
        $post_id = wpestate_last_post_id();
       
    }
    
    if ($post_id) {
           print 'DEBUG: Using wpestate_last_post_id: ' . $post_id . '<br>';
        echo '<h1 class="wpresidence-single-post-title">' . esc_html(get_the_title($post_id)) . '</h1>';
    }

// use the above post_it to get all post details you need

}

}