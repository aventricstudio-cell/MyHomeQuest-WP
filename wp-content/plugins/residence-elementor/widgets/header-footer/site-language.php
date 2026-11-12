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

class Wpresidence_Site_Language extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Site_Language';
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
        return esc_html__('Language Dropdown', 'residence-elementor');
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
                'label' => __( 'Content', 'residence-elementor' ),
            ]
        );

        $this->add_control(
            'important_note',
            [
                'type' => 'raw_html',
                'raw' => esc_html__('You need WPML or Polylang plugin for this to work', 'residence-elementor'),
                'content_classes' => 'elementor-control-field-description',
            ]
        );

        $this->end_controls_section();

        // Add Style Section
        $this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Style', 'residence-elementor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Typography Control
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'label' => __( 'Typography', 'residence-elementor' ),
                'selector' => '{{WRAPPER}} .wpresidence_language_dropdown a',
            ]
        );

        // Color Control
        $this->add_control(
            'color',
            [
                'label' => __( 'Color', 'residence-elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_language_dropdown a' => 'color: {{VALUE}}',
                ],
            ]
        );

        // Background Color Control
        $this->add_control(
            'background_color',
            [
                'label' => __( 'Background Color', 'residence-elementor' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_language_dropdown a' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        // Padding Control
        $this->add_responsive_control(
            'padding',
            [
                'label' => __( 'Padding', 'residence-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_language_dropdown a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        if (function_exists('wpestate_render_languages_dropdown')) {
            echo '<div class="wpresidence_language_dropdown">';
            wpestate_render_languages_dropdown();
            echo '</div>';
        }
    }
}