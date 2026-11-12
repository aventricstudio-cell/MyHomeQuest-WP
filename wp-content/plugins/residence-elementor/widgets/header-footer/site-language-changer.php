<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render the language dropdown using the WPResidence Translate widget renderer.
 *
 * @return string
 */
function wpestate_render_language_dropdown() {
    if ( ! function_exists( 'wpr_translate_language_switcher_widget_display' ) ) {
        return '';
    }

    // Use the existing widget renderer so logic stays centralized.
    ob_start();
    wpr_translate_language_switcher_widget_display(
        array(
            'widget_id' => 'wpr-language-switcher-elementor',
        ),
        array(
            'number' => 0,
        )
    );
    return ob_get_clean();
}

/**
 * Elementor widget to output the language dropdown.
 */
class Wpresidence_Site_Language_Changer extends Widget_Base {

    /**
     * Get widget name.
     *
     * @return string
     */
    public function get_name() {
        return 'Site_Language_Changer';
    }

    /**
     * Get widget title.
     *
     * @return string
     */
    public function get_title() {
        return esc_html__( 'Language Dropdown', 'residence-elementor' );
    }

    /**
     * Get widget icon.
     *
     * @return string
     */
    public function get_icon() {
        return 'wpresidence-note eicon-site-logo';
    }

    /**
     * Get widget categories.
     *
     * @return array
     */
    public function get_categories() {
        return array( 'wpresidence_header' );
    }

    /**
     * Register widget controls.
     *
     * @return void
     */
    protected function register_controls() {
        $this->start_controls_section(
            'section_notice',
            array(
                'label' => esc_html__( 'Info', 'residence-elementor' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'info_note',
            array(
                'type' => Controls_Manager::RAW_HTML,
                'raw'  => esc_html__( 'This widget outputs the WPResidence Translate language dropdown.', 'residence-elementor' ),
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_typography',
            array(
                'label' => esc_html__( 'Typography', 'residence-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'label_typography',
                'label'    => esc_html__( 'Typography', 'residence-elementor' ),
                'global'   => array(
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ),
                'selector' => '{{WRAPPER}} .wpr-language-switcher-label, {{WRAPPER}} .wpr-language-switcher-item-label',
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_colors',
            array(
                'label' => esc_html__( 'Colors', 'residence-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'label_color',
            array(
                'label' => esc_html__( 'Text Color', 'residence-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wpr-language-switcher-label, {{WRAPPER}} .wpr-language-switcher-item-label' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'toggle_background_color',
            array(
                'label' => esc_html__( 'Toggle Background', 'residence-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wpr-language-switcher-toggle' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'menu_background_color',
            array(
                'label' => esc_html__( 'Menu Background', 'residence-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wpr-language-switcher-menu' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'item_hover_color',
            array(
                'label' => esc_html__( 'Item Hover Text', 'residence-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wpr-language-switcher-item:hover .wpr-language-switcher-item-label' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'item_hover_background',
            array(
                'label' => esc_html__( 'Item Hover Background', 'residence-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .wpr-language-switcher-item:hover' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_spacing',
            array(
                'label' => esc_html__( 'Spacing', 'residence-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_responsive_control(
            'toggle_padding',
            array(
                'label' => esc_html__( 'Toggle Padding', 'residence-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors' => array(
                    '{{WRAPPER}} .wpr-language-switcher-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'menu_padding',
            array(
                'label' => esc_html__( 'Menu Padding', 'residence-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors' => array(
                    '{{WRAPPER}} .wpr-language-switcher-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'item_padding',
            array(
                'label' => esc_html__( 'Item Padding', 'residence-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', '%' ),
                'selectors' => array(
                    '{{WRAPPER}} .wpr-language-switcher-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_borders',
            array(
                'label' => esc_html__( 'Borders', 'residence-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'toggle_border',
                'label'    => esc_html__( 'Toggle Border', 'residence-elementor' ),
                'selector' => '{{WRAPPER}} .wpr-language-switcher-toggle',
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'menu_border',
                'label'    => esc_html__( 'Menu Border', 'residence-elementor' ),
                'selector' => '{{WRAPPER}} .wpr-language-switcher-menu',
            )
        );

        $this->add_control(
            'toggle_border_radius',
            array(
                'label' => esc_html__( 'Toggle Border Radius', 'residence-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%', 'em' ),
                'selectors' => array(
                    '{{WRAPPER}} .wpr-language-switcher-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'menu_border_radius',
            array(
                'label' => esc_html__( 'Menu Border Radius', 'residence-elementor' ),
                'type'  => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%', 'em' ),
                'selectors' => array(
                    '{{WRAPPER}} .wpr-language-switcher-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Render the widget output on the frontend.
     *
     * @return void
     */
    protected function render() {
        if ( function_exists( __NAMESPACE__ . '\\wpestate_render_language_dropdown' ) ) {
            echo wpestate_render_language_dropdown(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}
