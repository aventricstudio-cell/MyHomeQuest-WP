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

class Wpresidence_Footer_Properties_By_Category extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Properties_By_Category';
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
        return esc_html__('Properties By Category', 'residence-elementor');
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

        // Available property taxonomies
        $taxonomies = array(
            'property_category'         => esc_html__('Property Category','wpresidence-core'),
            'property_action_category'  => esc_html__('Property Action','wpresidence-core'),
            'property_city'             => esc_html__('Property City','wpresidence-core'),
            'property_area'             => esc_html__('Property Area','wpresidence-core'),
            'property_county_state'     => esc_html__('Property County/State','wpresidence-core')
        );
        
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content',  'residence-elementor' ),
            ]
        );

        $this->add_control( 'Title', [
            'label' => __( 'Element Title', 'residence-elementor' ),
            'type' => Controls_Manager::TEXT,
            'label_block'=>true,
            'default' => 'Our Listings',
            ]
        );

        $this->add_control( 'taxonomy', [
            'label' => __( 'Select Taxonomy', 'residence-elementor' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'Title'  ,
            'options' => $taxonomies
            ]
        );

        $this->add_control( 'show_count', [
            'label' => __( 'Show Count', 'residence-elementor' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'yes',
            'options' => [
                'yes' => __( 'Yes', 'residence-elementor' ),
                'no' => __( 'No', 'residence-elementor' ),
            ],
        ]
        );

        $this->add_control( 'show_child', [
            'label' => __( 'Show Child', 'residence-elementor' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'yes',
            'options' => [
                'yes' => __( 'Yes', 'residence-elementor' ),
                'no' => __( 'No', 'residence-elementor' ),
            ],
        ]
        );
        $this->add_control( 'show_icon', [
            'label' => __( 'Show Icon', 'residence-elementor' ),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'no',
            'options' => [
                'yes' => __( 'Yes', 'residence-elementor' ),
                'no' => __( 'No', 'residence-elementor' ),
            ],
        ]
        );

        $this->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'residence-elementor' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-circle',
					'library' => 'fa-solid',
				],
                'condition' => [
                    'show_icon' => 'yes',
                ],
				'recommended' => [
					'fa-solid' => [
						'circle',
						'dot-circle',
						'square-full',
					],
					'fa-regular' => [
						'circle',
						'dot-circle',
						'square-full',
					],
				],
			]
		);
        
        $this->end_controls_section();
        
        $this->start_controls_section(
            'title_style_section',
            [
                'label' => esc_html__('Title Style', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Title Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .widget-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .widget-title',
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => esc_html__('Margin', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .widget-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - List Items
        $this->start_controls_section(
            'list_style_section',
            [
                'label' => esc_html__('List Style', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'list_item_color',
            [
                'label' => esc_html__('Link/Icon Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .category_list_widget ul li a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .category_list_widget ul li i' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'list_item_hover_color',
            [
                'label' => esc_html__('Link Hover Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .category_list_widget ul li a:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'list_typography',
                'selector' => '{{WRAPPER}} .category_list_widget ul li a',
            ]
        );

        $this->add_responsive_control(
            'list_item_padding',
            [
                'label' => esc_html__('Item Padding', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .category_list_widget ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Count Numbers
        $this->start_controls_section(
            'count_style_section',
            [
                'label' => esc_html__('Count Style', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'count_color',
            [
                'label' => esc_html__('Count Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .category_no' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'count_typography',
                'selector' => '{{WRAPPER}} .category_no',
            ]
        );

        $this->add_responsive_control(
            'count_margin',
            [
                'label' => esc_html__('Count Margin', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .category_no' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Container
        $this->start_controls_section(
            'container_style_section',
            [
                'label' => esc_html__('Container Style', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'container_background',
                'label' => esc_html__('Background', 'residence-elementor'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .category_list_widget',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .category_list_widget',
            ]
        );

        $this->add_responsive_control(
            'container_border_radius',
            [
                'label' => esc_html__('Border Radius', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .category_list_widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => esc_html__('Padding', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .category_list_widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'container_box_shadow',
                'selector' => '{{WRAPPER}} .category_list_widget',
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
        
        
        $instance = [
            'title' => $settings['Title'],
            'taxonomy' => $settings['taxonomy'],
            'show_count' => $settings['show_count'],
            'show_child' => $settings['show_child'],
            'show_icon' => $settings['show_icon'],
           
            'icon' => isset($settings['icon']['value']) ? $settings['icon']['value'] : '',
            'icon_library' => isset($settings['icon']['library']) ? $settings['icon']['library'] : 'fa-solid',
        ];

        // Call the function to display the categories list
        echo wpestate_display_categories_list($instance);
    }


}
