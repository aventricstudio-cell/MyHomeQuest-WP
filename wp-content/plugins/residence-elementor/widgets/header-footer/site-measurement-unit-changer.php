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

class Wpresidence_Site_Measurement_Unit_Changer extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Site_Measurement_Unit_Changer';
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
        return esc_html__('Measurement Unit Dropdown', 'residence-elementor');
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
        // Start a new section for typography controls
        $this->start_controls_section(
            'section_typography',
            [
                'label' => esc_html__('Typography', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );



        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'dropdown_typography',
                'label'    => esc_html__( 'Typography', 'residence-elementor' ),
              'global'   => [
            'default' => Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                'selector' => '{{WRAPPER}} .filter_menu li, {{WRAPPER}} .sidebar_filter_menu',
            ]
        );

        // End the typography section
        $this->end_controls_section();

        // Start a new section for color controls
        $this->start_controls_section(
            'section_colors',
            [
                'label' => esc_html__('Colors', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Text color control for the dropdown menu items
        $this->add_control(
            'color_dropdown_item',
            [
                'label' => esc_html__('Text Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle' => 'color: {{VALUE}}; fill: {{VALUE}};',
                    '{{WRAPPER}} .filter_menu' => 'color: {{VALUE}}; fill: {{VALUE}};',
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle::after' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle.show' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Background color control for the dropdown menu items
        $this->add_control(
            'background_color_dropdown_item',
            [
                'label' => esc_html__('Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .dropdown-menu' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .sidebar_filter_menu' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle.show' => 'background-color: {{VALUE}};',  
                ],
            ]
        );

        // End the colors section
        $this->end_controls_section();

        // Start a new section for spacing controls
        $this->start_controls_section(
            'section_spacing',
            [
                'label' => esc_html__('Spacing & Radius', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'border_radius_dropdown_toggle_show', [
                'label' => esc_html__('Border Radius', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '4',
                    'right' => '4',
                    'bottom' => '4',
                    'left' => '4',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle.show' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',     
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',     
                ],
            ]
        );

        // Control for the padding of sidebar filter menu
        $this->add_responsive_control(
            'sidebar_filter_menu_padding',
            [
                'label' => esc_html__('Dropdown Padding', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle::after' => 'right: {{RIGHT}}{{UNIT}};',
                ],
            ]    
        );

         // Padding control for the dropdown menu items
         $this->add_responsive_control(
            'dropdown_item_padding',
            [
                'label' => esc_html__('Dropdown Menu Items Padding', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .filter_menu li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
       
        // Margin control for the dropdown menu items
        $this->add_responsive_control(
            'dropdown_item_margin',
            [
                'label' => esc_html__('Dropdown Margin', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .dropdown' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};width:auto;',
                    '{{WRAPPER}}' => 'width:auto;',
                ],
            ]
        );



        // End the spacing section
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
        $settings = $this->get_settings_for_display();
        if(function_exists('wpestate_generate_measure_unit_dropdown')):
            print wpestate_generate_measure_unit_dropdown();
        endif;
         
    }


}
