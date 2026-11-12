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

class Wpresidence_Single_Developer_Agents extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Single_developer_agents';
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
        return esc_html__('Single Developer Agents', 'residence-elementor');
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
        return 'wpresidence-note eicon-gallery-grid';
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
        return ['wpestate_single_developer_category'];
    }

    /**
     * Register widget controls.
     *
     * @since 1.0.0
     * @access protected
     */
 
    protected function register_controls() {
        
        
    // Content Section
    $this->start_controls_section(
        'content_section',
        [
            'label' => esc_html__('Content', 'residence-elementor'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]
    );

    $this->add_control( 'label', [
        'label' => __( 'Section Title', 'residence-elementor' ),
        'type' => Controls_Manager::TEXT,
        'label_block'=>true,
        'default' => 'Our Agents',
        ]
    );

   

    // $this->add_control(
    //     'rownumber',
    //     [
    //         'label' => esc_html__('Listings Per Row', 'residence-elementor'),
    //         'type' => \Elementor\Controls_Manager::SELECT,
    //         'default' => '3',
    //         'options' => [
    //             '1' => esc_html__('1 Column', 'residence-elementor'),
    //             '2' => esc_html__('2 Columns', 'residence-elementor'),
    //             '3' => esc_html__('3 Columns', 'residence-elementor'),
    //             '4' => esc_html__('4 Columns', 'residence-elementor'),
    //             '6' => esc_html__('6 Columns', 'residence-elementor'),
    //         ],
    //         'description' => esc_html__('Select how many listings to display per row', 'residence-elementor'),
    //     ]
    // );

    // $this->add_control(
    //     'listings_per_page',
    //     [
    //         'label' => esc_html__('Total Number of Listings', 'residence-elementor'),
    //         'type' => \Elementor\Controls_Manager::NUMBER,
    //         'default' => 6,
    //         'min' => 1,
    //         'max' => 50,
    //         'step' => 1,
    //         'description' => esc_html__('Total number of listings to display', 'residence-elementor'),
    //     ]
    // );

    $this->end_controls_section();

    // Style Section - Title
        $this->start_controls_section(
            'title_style',
            [
                'label' => __('Title', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Text Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent_listings_title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .agent_listings_title',
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __('Margin', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .agent_listings_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Agent Card
        $this->start_controls_section(
            'card_style',
            [
                'label' => __('Agent Card', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'card_background',
                'label' => __('Background', 'residence-elementor'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .agent_unit',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'label' => __('Border', 'residence-elementor'),
                'selector' => '{{WRAPPER}} .agent_unit',
            ]
        );

        $this->add_responsive_control(
            'card_border_radius',
            [
                'label' => __('Border Radius', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .agent_unit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'label' => __('Box Shadow', 'residence-elementor'),
                'selector' => '{{WRAPPER}} .agent_unit',
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label' => __('Padding', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .agent_unit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Agent Image
        $this->start_controls_section(
            'image_style',
            [
                'label' => __('Agent Image', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'image_width',
            [
                'label' => __('Width', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .agent-unit-img-wrapper img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_height',
            [
                'label' => __('Height', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .agent-unit-img-wrapper img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => __('Border Radius', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .agent-unit-img-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_margin',
            [
                'label' => __('Margin', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .agent-unit-img-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Agent Name
        $this->start_controls_section(
            'name_style',
            [
                'label' => __('Agent Name', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'name_color',
            [
                'label' => __('Text Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent_unit h4, {{WRAPPER}} .agent_unit h4 a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'name_hover_color',
            [
                'label' => __('Hover Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent_unit h4 a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'name_typography',
                'selector' => '{{WRAPPER}} .agent_unit h4',
            ]
        );

        $this->add_responsive_control(
            'name_margin',
            [
                'label' => __('Margin', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .agent_unit h4' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Position
        $this->start_controls_section(
            'position_style',
            [
                'label' => __('Position/Title', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'position_color',
            [
                'label' => __('Text Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent_position' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'position_typography',
                'selector' => '{{WRAPPER}} .agent_position',
            ]
        );

        $this->add_responsive_control(
            'position_margin',
            [
                'label' => __('Margin', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .agent_position' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Description
        $this->start_controls_section(
            'description_style',
            [
                'label' => __('Description', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __('Text Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent_card_content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .agent_card_content',
            ]
        );

        $this->add_responsive_control(
            'description_margin',
            [
                'label' => __('Margin', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .agent_card_content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Social Icons
        $this->start_controls_section(
            'social_style',
            [
                'label' => __('Social Icons', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'social_icon_size',
            [
                'label' => __('Icon Size', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 50,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'selectors' => [
                    '{{WRAPPER}} .agent_unit_social a i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'social_icon_color',
            [
                'label' => __('Icon Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent_unit_social a i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'social_icon_hover_color',
            [
                'label' => __('Icon Hover Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent_unit_social a:hover i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'social_icon_spacing',
            [
                'label' => __('Icon Spacing', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'selectors' => [
                    '{{WRAPPER}} .agent_unit_social a' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Listings Count
        $this->start_controls_section(
            'listings_count_style',
            [
                'label' => __('Listings Count', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'listings_count_background',
            [
                'label' => __('Background Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent_card_my_listings' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'listings_count_color',
            [
                'label' => __('Text Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .agent_card_my_listings' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'listings_count_typography',
                'selector' => '{{WRAPPER}} .agent_card_my_listings',
            ]
        );

        $this->add_responsive_control(
            'listings_count_padding',
            [
                'label' => __('Padding', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .agent_card_my_listings' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'listings_count_border_radius',
            [
                'label' => __('Border Radius', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .agent_card_my_listings' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        
        $post_id = wpestate_last_agent_id('estate_developer');
       
    }
    
    if ($post_id) {
        $settings = $this->get_settings_for_display();
       

         echo wpestate_display_agency_agents_html($post_id,  $settings);
    }

// use the above post_it to get all post details you need

}

}