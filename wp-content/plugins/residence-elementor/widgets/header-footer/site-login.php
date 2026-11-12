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

class Wpresidence_Site_Login extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Site_Login';
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
        return esc_html__('Website Login & User Menu', 'residence-elementor');
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

        // Section for general settings
        $this->start_controls_section(
                'section_general_settings',
                [
                    'label' => __('Content', 'residence-elementor'),
                    'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        $this->add_control( 'label', [
            'label' => __( 'Element Label', 'residence-elementor' ),
            'type' => Controls_Manager::TEXT,
            'label_block'=>true,
            'default' => '',
            ]
        );
        
        $this->end_controls_section();

        // Section for UL styles
        $this->start_controls_section(
                'section_ul_style',
                [
                    'label' => __('Dropdown Menu', 'residence-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        
        $this->add_control(
            'show_user_menu',
            [
                'label' => __( 'Show User Menu Open (works only for edit mode) ', 'residence-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'residence-elementor' ),
                'label_off' => __( 'Hide', 'residence-elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'label_text_color', [
                'label'     => esc_html__( 'Label Color',  'residence-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'label'     => esc_html__( 'Label Typography',  'residence-elementor' ),
                'name' => 'wpresidence_label_typography',
                'selector' => '{{WRAPPER}} .label',
                'global'   => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
            ]
        );


        $this->add_control(
                'ul_background_color',
                [
                    'label' => __('Background Color', 'residence-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} #user_menu_open' => 'background-color: {{VALUE}}',
                        '{{WRAPPER}} #user_menu_open:before' => ' border-bottom-color: {{VALUE}}',
                        '{{WRAPPER}} #user_menu_open:after' => ' border-bottom-color: {{VALUE}}',
                        
                        
                        
                    ],
                ]
        );

        $this->add_control(
                'ul_padding',
                [
                    'label' => __('Padding', 'residence-elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%', 'em'],
                    'selectors' => [
                        '{{WRAPPER}} #user_menu_open' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );

        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'dropdown_box_shadow',
                    'exclude' => [
                        'box_shadow_position',
                    ],
                    'selector' => '{{WRAPPER}} #user_menu_open',
                ]
        );

        $this->end_controls_section();

        // Section for LI styles
        $this->start_controls_section(
                'section_li_style',
                [
                    'label' => __('Menu Items', 'residence-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_control(
                'li_font_color',
                [
                    'label' => __('Font Color', 'residence-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} #user_menu_open li' => 'color: {{VALUE}}',
                        '{{WRAPPER}} #user_menu_open>li>a' => 'color: {{VALUE}}',
                        '{{WRAPPER}} #user_menu_open a svg path' => 'stroke: {{VALUE}}',
                        '{{WRAPPER}} #user_menu_open a svg circle' => 'stroke: {{VALUE}}',
                    ],
                ]
        );
        
        $this->add_group_control(
                   Group_Control_Typography::get_type(),
                   [
                       'name'     => 'dropdown_typography',
                       'label'    => esc_html__( 'Tipography', 'residence-elementor' ),
                    'global'   => [
            'default' => Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                      'selector' => '{{WRAPPER}} #user_menu_open li, {{WRAPPER}} #user_menu_open li a',
                   ]
               );



        $this->add_control(
                'li_padding',
                [
                    'label' => __('Padding', 'residence-elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%', 'em'],
                    'selectors' => [
                        '{{WRAPPER}} #user_menu_open li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );

        $this->add_control(
                'li_background_color_hover',
                [
                    'label' => __('Background Color on Hover', 'residence-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} #user_menu_open>li>a:hover' => 'background-color: {{VALUE}}',
                        '{{WRAPPER}} #user_menu_open>li>a:focus' => 'background-color: {{VALUE}}',
        
                        
                    ],
                ]
        );

        $this->add_control(
                'li_font_color_hover',
                [
                    'label' => __('Font Color on Hover', 'residence-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}}  #user_menu_open>li>a:hover' => 'color: {{VALUE}}',
                        '{{WRAPPER}}  #user_menu_open>li>a:focus' => 'color: {{VALUE}}',
                        '{{WRAPPER}} #user_menu_open>li>a:hover svg path' => 'stroke: {{VALUE}}',
                        '{{WRAPPER}} #user_menu_open>li>a:hover svg circle' => 'stroke: {{VALUE}}',
                        
                    ],
                ]
        );

        $this->end_controls_section();

        // Section for user_menu styles
        $this->start_controls_section(
                'section_user_menu_style',
                [
                    'label' => __('User Icon', 'residence-elementor'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_control(
                'user_menu_background_color',
                [
                    'label' => __('Background Color', 'residence-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .user_menu' => 'background-color: {{VALUE}}',
                    ],
                ]
        );

        $this->add_control(
                'user_menu_color',
                [
                    'label' => __('Color', 'residence-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}}  .user_menu' => 'color: {{VALUE}}',
                        '{{WRAPPER}}  .navicon:before' => 'background: {{VALUE}}',
                        '{{WRAPPER}}  .navicon:after' => 'background: {{VALUE}}',
                        '{{WRAPPER}}  .navicon-button a' => 'color: {{VALUE}}',
                        '{{WRAPPER}}  .navicon' => 'background: {{VALUE}}',
                        '{{WRAPPER}}  .submit_action svg' => 'fill: {{VALUE}}',  
                    ],
                ]
        );

// Control for the hover background color of the user menu
$this->add_control(
    'user_menu_hover_background_color',
    [
        'label' => __('Hover Background Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .user_menu:hover' => 'background-color: {{VALUE}}',
        ],
    ]
);

// Control for the hover text color of the user menu
$this->add_control(
    'user_menu_hover_color',
    [
        'label' => __('Hover Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .user_menu:hover' => 'color: {{VALUE}}',
            '{{WRAPPER}} .user_menu:hover .navicon:before' => 'background: {{VALUE}}',
            '{{WRAPPER}} .user_menu:hover .navicon:after' => 'background: {{VALUE}}',
            '{{WRAPPER}} .user_menu:hover .navicon' => 'background: {{VALUE}}',
            '{{WRAPPER}} .user_menu:hover .navicon-button a' => 'color: {{VALUE}}',
            '{{WRAPPER}} .user_menu:hover .submit_action svg' => 'fill: {{VALUE}}',
        ],
    ]
);

        $this->add_control(
                'user_menu_padding',
                [
                    'label' => __('Padding', 'residence-elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%', 'em'],
                    'selectors' => [
                        '{{WRAPPER}} .user_menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );

        // Control for the hover border color of the menu user picture
        $this->add_control(
            'menu_user_picture_border_color',
            [
                'label' => __('Menu User Picture Border Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .menu_user_picture' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
                'user_menu_border_radius',
                [
                    'label' => esc_html__('Border Radius', 'residence-elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                    'selectors' => [
                        '{{WRAPPER}} .user_menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
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
        global $wpestate_global_payments;
        $settings = $this->get_settings_for_display();
        $user_menu_class = $settings['show_user_menu'] === 'yes' ? 'wpresidence-studio-show-menu' : 'wpresidence-studio-hide-menu';
        ?>
        <div class="wpresidence_header_elementor_user_wrap <?php echo esc_attr($user_menu_class); ?>">
            <?php
            ob_start();
            get_template_part('templates/elementor-header-templates/elementor-header-user-menu', '', ['settings' => $settings]);
            $template = ob_get_clean();
            echo $template;
            ?>
        </div>

        <style>
            .elementor-widget-Site_Login .label {
                margin-left: 0px;
            }
        </style>
        <?php
    }
}
