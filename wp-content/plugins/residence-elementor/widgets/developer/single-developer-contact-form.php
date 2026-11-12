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

class Wpresidence_Single_Developer_Contact_Form extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Single_developer_contact_form';
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
        return esc_html__('Single Developer Contact Form', 'residence-elementor');
    }

    /**
     * Get widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon(): string {
        return 'wpresidence-note eicon-form-horizontal';
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

        // Header Style Section
        $this->start_controls_section(
            'header_style_section',
            [
                'label' => esc_html__('Header Style', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_header' => 'yes',
                ],
            ]
        );

        // Header Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'header_typography',
                'label' => esc_html__('Typography', 'residence-elementor'),
                'selector' => '{{WRAPPER}} .wpestate_single_agent_details_header_wrapper h4',
            ]
        );

        // Header Colors
        $this->add_control(
            'header_color',
            [
                'label' => esc_html__('Text Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpestate_single_agent_details_header_wrapper h4' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'header_bg_color',
            [
                'label' => esc_html__('Background Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpestate_single_agent_details_header_wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        // Header Padding
        $this->add_responsive_control(
            'header_padding',
            [
                'label' => esc_html__('Padding', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .wpestate_single_agent_details_header_wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Form Layout Section
        $this->start_controls_section(
            'form_layout_section',
            [
                'label' => esc_html__('Form Layout', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Main Form Wrapper Gap
        $this->add_responsive_control(
            'form_wrapper_gap',
            [
                'label' => esc_html__('Space between elements - vertical', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .contact_form_flex_wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Input Wrapper Gap
        $this->add_responsive_control(
            'input_wrapper_gap',
            [
                'label' => esc_html__('Space between elements - horizontal', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .contact_form_flex_input_wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Input Fields Style Section
        $this->start_controls_section(
            'input_fields_style_section',
            [
                'label' => esc_html__('Input Fields Style', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Input Typography
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'input_typography',
                'label' => esc_html__('Typography', 'residence-elementor'),
                'selector' => '{{WRAPPER}} .form-control',
            ]
        );

        // Input Padding
        $this->add_responsive_control(
            'input_padding',
            [
                'label' => esc_html__('Padding', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .form-control' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Input Border Radius
        $this->add_responsive_control(
            'input_border_radius',
            [
                'label' => esc_html__('Border Radius', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .form-control' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Input Colors - Normal State
        $this->add_control(
            'input_color_normal',
            [
                'label' => esc_html__('Text Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-control' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_bg_color_normal',
            [
                'label' => esc_html__('Background Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-control' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_border_color_normal',
            [
                'label' => esc_html__('Border Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-control' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        // Input Colors - Focus State
        $this->add_control(
            'input_color_focus',
            [
                'label' => esc_html__('Focus Text Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-control:focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_bg_color_focus',
            [
                'label' => esc_html__('Focus Background Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-control:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'input_border_color_focus',
            [
                'label' => esc_html__('Focus Border Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-control:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        // Placeholder Color
        $this->add_control(
            'input_placeholder_color',
            [
                'label' => esc_html__('Placeholder Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-control::placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-control::-webkit-input-placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-control::-moz-placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-control:-ms-input-placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Submit Button Style Section
        $this->start_controls_section(
            'submit_button_style_section',
            [
                'label' => esc_html__('Submit Button Style', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => esc_html__('Typography', 'residence-elementor'),
                'selector' => '{{WRAPPER}} .wpresidence_button.agent_submit_class, {{WRAPPER}} .wpresidence_button.message_submit',
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
                    '{{WRAPPER}} .wpresidence_button.agent_submit_class, {{WRAPPER}} .wpresidence_button.message_submit' => 
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .wpresidence_button.agent_submit_class, {{WRAPPER}} .wpresidence_button.message_submit' =>
                        'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Button Colors - Normal State
        $this->add_control(
            'button_color_normal',
            [
                'label' => esc_html__('Text Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_button.agent_submit_class, {{WRAPPER}} .wpresidence_button.message_submit' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color_normal',
            [
                'label' => esc_html__('Background Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_button.agent_submit_class, {{WRAPPER}} .wpresidence_button.message_submit' => 
                        'background-color: {{VALUE}}; background-image: none;',
                ],
            ]
        );

        $this->add_control(
            'button_border_color_normal',
            [
                'label' => esc_html__('Border Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_button.agent_submit_class, {{WRAPPER}} .wpresidence_button.message_submit' => 
                        'border-color: {{VALUE}};',
                ],
            ]
        );


        // Button Colors - Hover State
        $this->add_control(
            'button_color_hover',
            [
                'label' => esc_html__('Hover Text Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_button.agent_submit_class:hover, {{WRAPPER}} .wpresidence_button.message_submit:hover' => 
                        'color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'button_bg_color_hover',
            [
                'label' => esc_html__('Hover Background Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_button.agent_submit_class:hover, {{WRAPPER}} .wpresidence_button.message_submit:hover' => 
                        'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_color_hover',
            [
                'label' => esc_html__('Hover Border Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpresidence_button.agent_submit_class:hover, {{WRAPPER}} .wpresidence_button.message_submit:hover' => 'border-color: {{VALUE}};',
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
        
       
        $settings = $this->get_settings_for_display();
        $agentID =$post_id;
        $context = 'developer_page';
        unset($propertyID);
        print '<div class="wpestate_contact_form_parent wpresidence_builder_contact_form">';
            include(locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php'));
        print '</div>';   
    }

// use the above post_it to get all post details you need

}

}