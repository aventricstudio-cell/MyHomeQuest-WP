<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Property_Page_Add_To_Favorites extends Widget_Base {

    /**
     * Retrieve the widget name.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'property_add_to_favorites';
    }

    public function get_categories() {
        return ['wpresidence_property'];
    }

    /**
     * Retrieve the widget title.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __(' Add To Favorites / Share / Print Buttons', 'residence-elementor');
    }

    /**
     * Retrieve the widget icon.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return ' wpresidence-note eicon-post-title';
    }

    /**
     * Retrieve the list of scripts the widget depended on.
     *
     * Used to set scripts dependencies required to run the widget.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return array Widget scripts dependencies.
     */
    public function get_script_depends() {
        return [''];
    }

    /**
     * Register the widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     *
     * @access protected
     */
    protected function register_controls() {



        $this->start_controls_section(
                'typography_section', [
            'label' => esc_html__('Settings', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'property_Address',
                'label' => esc_html__('Text Typography', 'residence-elementor'),
               'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                'selector' => '{{WRAPPER}} .single_property_action',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'icon_typography',
                'label' => esc_html__('Icon Size', 'residence-elementor'),
               'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                'selector' => '{{WRAPPER}} .single_property_action i:before, {{WRAPPER}} .title_share.share_list.single_property_action:before',
                'exclude' => ['font_weight', 'font_family'], // Exclude font weight and font family
            ]
        );

        $this->add_control(
                'item_color', [
            'label' => esc_html__('Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .single_property_action ' => 'color: {{VALUE}}',
                '{{WRAPPER}} .single_property_action i' => 'color: {{VALUE}}',
            ],
                ]
        );

        $this->add_control(
            'hover_item_color', [
                'label' => esc_html__('Hover Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .single_property_action:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .single_property_action i:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        

        $this->add_control(
            'item_background_color', [
            'label' => esc_html__('Background Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .single_property_action ' => 'background-color: {{VALUE}}',
            ],
                ]
        );

        $this->add_control(
            'hover_item_background_color', [
                'label' => esc_html__('Hover Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .single_property_action:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );


        $this->add_control(
                'hide_share', [
            'label' => esc_html__('Hide Share Button', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'residence-elementor'),
            'label_off' => esc_html__('No', 'residence-elementor'),
            'return_value' => 'none',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  .share_list' => 'display: {{VALUE}};',
            ],
                ]
        );

        $this->add_control(
                'hide_favorite', [
            'label' => esc_html__('Hide Add to Favorite Button', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'residence-elementor'),
            'label_off' => esc_html__('No', 'residence-elementor'),
            'return_value' => 'none',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  #add_favorites' => 'display: {{VALUE}};',
            ],
                ]
        );

        $this->add_control(
                'hide_print', [
            'label' => esc_html__('Hide Print Button', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'residence-elementor'),
            'label_off' => esc_html__('No', 'residence-elementor'),
            'return_value' => 'none',
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  #print_page' => 'display: {{VALUE}};',
            ],
                ]
        );

        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'box_shadow',
            'label' => esc_html__('Button Shadow', 'rentals-elementor'),
            'selector' => '{{WRAPPER}} .single_property_action',
                ]
        );


        $this->add_control(
            'padding', [
                'label' => __('Padding', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .single_property_action' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;', // Padding and height for .single_property_action
                    '{{WRAPPER}} .title_share.share_list.single_property_action' => 'height: auto;', // Height for .title_share.share_list.single_property_action
                ],
            ]
        );

        $this->add_responsive_control(
            'text_align', [
                'label' => __('Alignment', 'wpresidence-core'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'selectors' => [
                    '{{WRAPPER}} .prop_social' => 'display: flex; gap: 5px; justify-content: {{VALUE}};', // Added display: flex
                ],
                'options' => [
                    'left' => [
                        'title' => __('Left', 'wpresidence-core'),
                        'icon' => 'eicon-text-align-left',
                        'value' => 'flex-start', // Align to left using flex-start
                    ],
                    'center' => [
                        'title' => __('Center', 'wpresidence-core'),
                        'icon' => 'eicon-text-align-center',
                        'value' => 'center', // Align to center
                    ],
                    'right' => [
                        'title' => __('Right', 'wpresidence-core'),
                        'icon' => 'eicon-text-align-right',
                        'value' => 'flex-end', // Align to right using flex-end
                    ],
                ],
                'default' => 'left',
                'toggle' => true,
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
        $settings = $this->get_settings_for_display();
        $attributes['is_elementor'] = 1;
        if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $attributes['is_elementor_edit'] = 1;
        }
        echo wpestate_estate_property_page_add_to_favorites_section($attributes);
    }

}
