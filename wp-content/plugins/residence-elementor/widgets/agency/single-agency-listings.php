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

class Wpresidence_Single_Agency_Listings extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Single_agency_listings';
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
        return esc_html__('Single Agency Listings', 'residence-elementor');
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
        return 'wpresidence-note eicon-posts-grid';
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
        
        
    // Content Section
    $this->start_controls_section(
        'content_section',
        [
            'label' => esc_html__('Content', 'residence-elementor'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]
    );

    $this->add_control(
        'agent_listings_title',
        [
            'label' => esc_html__('Agent Listings Title', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => esc_html__('My Listings', 'residence-elementor'),
            'placeholder' => esc_html__('Enter title text', 'residence-elementor'),
            'label_block' => true,
        ]
    );

   

    $this->add_control(
        'rownumber',
        [
            'label' => esc_html__('Listings Per Row', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => '3',
            'options' => [
                '1' => esc_html__('1 Column', 'residence-elementor'),
                '2' => esc_html__('2 Columns', 'residence-elementor'),
                '3' => esc_html__('3 Columns', 'residence-elementor'),
                '4' => esc_html__('4 Columns', 'residence-elementor'),
                '6' => esc_html__('6 Columns', 'residence-elementor'),
            ],
            'description' => esc_html__('Select how many listings to display per row', 'residence-elementor'),
        ]
    );

    $this->add_control(
        'listings_per_page',
        [
            'label' => esc_html__('Total Number of Listings', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::NUMBER,
            'default' => 6,
            'min' => 1,
            'max' => 50,
            'step' => 1,
            'description' => esc_html__('Total number of listings to display', 'residence-elementor'),
        ]
    );

    $this->end_controls_section();

    // Term Bar Styling Section
    $this->start_controls_section(
        'term_bar_style_section',
        [
            'label' => esc_html__('Term Bar Items', 'residence-elementor'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]
    );

    // Term Bar Item Typography
    $this->add_group_control(
        \Elementor\Group_Control_Typography::get_type(),
        [
            'name' => 'term_bar_typography',
            'label' => esc_html__('Typography', 'residence-elementor'),
            'selector' => '{{WRAPPER}} .term_bar_item',
        ]
    );

    // Term Bar Item Padding
    $this->add_responsive_control(
        'term_bar_padding',
        [
            'label' => esc_html__('Padding', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .term_bar_item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    // Term Bar Item States
    $this->start_controls_tabs('term_bar_style_tabs');

    // Normal State
    $this->start_controls_tab(
        'term_bar_normal_tab',
        [
            'label' => esc_html__('Normal', 'residence-elementor'),
        ]
    );

    $this->add_control(
        'term_bar_color',
        [
            'label' => esc_html__('Text Color', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .term_bar_item' => 'color: {{VALUE}};',
            ],
        ]
    );

    $this->add_control(
        'term_bar_background',
        [
            'label' => esc_html__('Background Color', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .term_bar_item' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $this->end_controls_tab();

    // Hover State
    $this->start_controls_tab(
        'term_bar_hover_tab',
        [
            'label' => esc_html__('Hover', 'residence-elementor'),
        ]
    );

    $this->add_control(
        'term_bar_hover_color',
        [
            'label' => esc_html__('Text Color', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .term_bar_item:hover' => 'color: {{VALUE}};',
            ],
        ]
    );

    $this->add_control(
        'term_bar_hover_background',
        [
            'label' => esc_html__('Background Color', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .term_bar_item:hover' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $this->end_controls_tab();

    // Active/Selected State
    $this->start_controls_tab(
        'term_bar_active_tab',
        [
            'label' => esc_html__('Active', 'residence-elementor'),
        ]
    );

    $this->add_control(
        'term_bar_active_color',
        [
            'label' => esc_html__('Text Color', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .term_bar_item.active_term' => 'color: {{VALUE}};',
            ],
        ]
    );

    $this->add_control(
        'term_bar_active_background',
        [
            'label' => esc_html__('Background Color', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .term_bar_item.active_term' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

    $this->end_controls_section();

    // Load More Button Styling Section
    $this->start_controls_section(
        'load_more_button_style_section',
        [
            'label' => esc_html__('Load More Button', 'residence-elementor'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]
    );

    // Button Typography
    $this->add_group_control(
        \Elementor\Group_Control_Typography::get_type(),
        [
            'name' => 'button_typography',
            'label' => esc_html__('Typography', 'residence-elementor'),
            'selector' => '{{WRAPPER}} .wpresidence_button.listing_load_more',
        ]
    );

    // Button Padding
    $this->add_responsive_control(
        'button_padding',
        [
            'label' => esc_html__('Padding', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button.listing_load_more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    // Button Border Radius
    $this->add_responsive_control(
        'button_border_radius',
        [
            'label' => esc_html__('Border Radius', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button.listing_load_more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    // Button States
    $this->start_controls_tabs('button_style_tabs');

    // Normal State
    $this->start_controls_tab(
        'button_normal_tab',
        [
            'label' => esc_html__('Normal', 'residence-elementor'),
        ]
    );

    $this->add_control(
        'button_color',
        [
            'label' => esc_html__('Text Color', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button.listing_load_more' => 'color: {{VALUE}};',
            ],
        ]
    );

    $this->add_control(
        'button_background',
        [
            'label' => esc_html__('Background Color', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button.listing_load_more' => 'background-color: {{VALUE}}; background-image: none;',
            ],
        ]
    );

    $this->add_group_control(
        \Elementor\Group_Control_Border::get_type(),
        [
            'name' => 'button_border',
            'label' => esc_html__('Border', 'residence-elementor'),
            'selector' => '{{WRAPPER}} .wpresidence_button.listing_load_more',
        ]
    );

    $this->add_group_control(
        \Elementor\Group_Control_Box_Shadow::get_type(),
        [
            'name' => 'button_box_shadow',
            'label' => esc_html__('Box Shadow', 'residence-elementor'),
            'selector' => '{{WRAPPER}} .wpresidence_button.listing_load_more',
        ]
    );

    $this->end_controls_tab();

    // Hover State
    $this->start_controls_tab(
        'button_hover_tab',
        [
            'label' => esc_html__('Hover', 'residence-elementor'),
        ]
    );

    $this->add_control(
        'button_hover_color',
        [
            'label' => esc_html__('Text Color', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button.listing_load_more:hover' => 'color: {{VALUE}};',
            ],
        ]
    );

    $this->add_control(
        'button_hover_background',
        [
            'label' => esc_html__('Background Color', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button.listing_load_more:hover' => 'background-color: {{VALUE}};',
            ],
        ]
    );

    $this->add_group_control(
        \Elementor\Group_Control_Border::get_type(),
        [
            'name' => 'button_hover_border',
            'label' => esc_html__('Border', 'residence-elementor'),
            'selector' => '{{WRAPPER}} .wpresidence_button.listing_load_more:hover',
        ]
    );

    $this->add_group_control(
        \Elementor\Group_Control_Box_Shadow::get_type(),
        [
            'name' => 'button_hover_box_shadow',
            'label' => esc_html__('Box Shadow', 'residence-elementor'),
            'selector' => '{{WRAPPER}} .wpresidence_button.listing_load_more:hover',
        ]
    );

    $this->add_control(
        'button_hover_transition',
        [
            'label' => esc_html__('Transition Duration', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 3,
                    'step' => 0.1,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button.listing_load_more' => 'transition: all {{SIZE}}s ease;',
            ],
        ]
    );

    $this->end_controls_tab();

    $this->end_controls_tabs();

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
       

         echo wpresidence_display_realtor_listings($post_id,  $settings);
    }

// use the above post_it to get all post details you need

}

}