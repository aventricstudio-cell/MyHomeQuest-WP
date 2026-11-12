<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color; // For color schemes
use Elementor\Responsive; // For responsive controls
use Elementor\Units; // For units like px, em, etc.
use Elementor\Scheme_Color; // For additional color scheme management

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Property_Page_Overview_Section extends Widget_Base {

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
        return 'property_show_overview_section';
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
        return __('Overview Section', 'residence-elementor');
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
        return ' wpresidence-note eicon-product-meta';
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
        $repeater = new Repeater();
        $metadata_array22 = array(
            //   'property_type' => esc_html__( 'Property Type','residence-elementor' ),
            'property_rooms' => esc_html__('Rooms', 'residence-elementor'),
            'property_bedrooms' => esc_html__('Bedrooms', 'residence-elementor'),
            'property_bathrooms' => esc_html__('Bathrooms', 'residence-elementor'),
            'property_size' => esc_html__('Property  Size', 'residence-elementor'),
            'property_lot_size' => esc_html__('Lot size Area', 'residence-elementor'),
            'property-garage' => esc_html__('Garages', 'residence-elementor'),
            'property_year' => esc_html__('Built Year', 'residence-elementor'),
            'property_address' => esc_html__('Property Adress', 'residence-elementor'),
            'property_zip' => esc_html__('Property Zip', 'residence-elementor'),
            'property_country' => esc_html__('Property Country', 'residence-elementor'),
            'property_status' => esc_html__('Property Status', 'residence-elementor'),
            'property_price' => esc_html__('Property Price', 'residence-elementor'),
            'property_year_tax' => esc_html__('Property Year Tax', 'residence-elementor'),
            'property_hoa' => esc_html__('Property HOA', 'residence-elementor'),
            'energy_class' => esc_html__('Property Energy Class', 'residence-elementor'),
            'energy_index' => esc_html__('Property Energy Index', 'residence-elementor'),
            'property_id' => esc_html__('Property ID', 'residence-elementor'),
            'property_internal_id' => esc_html__('Listing ID', 'residence-elementor'),
            'property_city' => esc_html__('Property City', 'residence-elementor'),
            'property_area' => esc_html__('Property Area', 'residence-elementor'),
            'property_county_state' => esc_html__('Property County/State', 'residence-elementor'),
            'property_category' => esc_html__('Property Category', 'residence-elementor'),
            'property_action_category' => esc_html__('Property Action Category', 'residence-elementor'),
        );
        $custom_fields = wpresidence_get_option( 'wp_estate_custom_fields', '');
        
        foreach ($custom_fields as $key=>$custom_field):
            $name   =   $custom_field[0];
            $slug   =   wpestate_limit45(sanitize_title( $name ));
            $slug   =   sanitize_key($slug);
            $label  =   stripslashes($custom_field[1]);
            $metadata_array22[$slug]=$label;
        endforeach;



        $repeater->add_control(
                'field_type', [
            'label' => esc_html__('Field', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $metadata_array22,
                ]
        );


        $repeater->add_control(
                'label_singular', [
            'label' => esc_html__('Label', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
                ]
        );

        $repeater->add_control(
                'label_plural', [
            'label' => esc_html__('Label Plural', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'conditions' => [
                'terms' => [
                    [
                        'name' => 'field_type',
                        'operator' => '!in',
                        'value' => [
                        ],
                    ],
                ],
            ],
                ]
        );



        $repeater->add_control(
                'icon_type', [
            'label' => esc_html__('Icons From', 'residence-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'theme_options' => esc_html__('Theme Options ', 'residence-elementor'),
                'custom' => esc_html__('Custom Icon', 'residence-elementor'),
                'none' => esc_html__('No Icon', 'residence-elementor'),
            ],
            'default' => 'theme_options',
                ]
        );

        $repeater->add_control(
                'meta_icon', [
            'label' => esc_html__('upload Icon', 'text-domain'),
            'type' => Controls_Manager::ICONS,
            'condition' => [
                'icon_type' => 'custom'
            ],
                ]
        );



        $this->start_controls_section(
            'overview_content', [
                'label' => __('Content', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'align', [
                'label' => __('Fields Alignment', 'residence-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'residence-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'residence-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'residence-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'desktop_default' => 'left',  // Default for desktop
                'tablet_default' => 'left', // Default for tablet
                'mobile_default' => 'center',  // Default for mobile
                'selectors' => [
                    '{{WRAPPER}} .overview_element:first-of-type li' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .overview_element li' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .nav-tabs' => 'justify-content: {{VALUE}};', // Adjust for nav-tabs if needed
                ],
            ]
        );
       

        $this->add_control(
            'hide_section_title', [
                'label' => esc_html__('Hide Section Title', 'residence-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'residence-elementor'),
                'label_off' => esc_html__('No', 'residence-elementor'),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}  .panel-title' => 'display: {{VALUE}};',
                ],
            ]
        );

        // Title alignment control for panel title
        $this->add_responsive_control(
            'title_alignment', [
                'label' => __('Section Title Alignment', 'residence-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'residence-elementor'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'residence-elementor'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'residence-elementor'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left', // Set a default alignment if needed
                'selectors' => [
                    '{{WRAPPER}} .panel-title' => 'text-align: {{VALUE}};',
                ],
                'desktop_default' => 'left',
                'tablet_default' => 'left',
                'mobile_default' => 'center',
                'condition' => [
                    'hide_section_title!' => 'none', // Show only when 'Hide Section Title' is not set to "Yes"
                ],
            ]
        );
        

        $this->add_control(
            'section_title', [
                'label' => esc_html__('Section Title', 'residence-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => '',
               
            ]
        );


        $this->add_control(
            'hide_updated_on', [
                'label' => esc_html__('Hide Updated On', 'residence-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'residence-elementor'),
                'label_off' => esc_html__('No', 'residence-elementor'),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .overview_updatd_on' => 'display: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'overview_fields', [
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                    'id' => 'property_bedrooms',
                    'field_type' => 'property_bedrooms',
                    'label_singular' => esc_html__('Bedroom', 'residence-elementor'),
                    'label_plural' => esc_html__('Bedrooms', 'residence-elementor'),
                    'icon_type' => 'theme_options',
                    ],
                    [
                    'id' => 'property_bathrooms',
                    'field_type' => 'property_bathrooms',
                    'label_singular' => esc_html__('Bathroom', 'residence-elementor'),
                    'label_plural' => esc_html__('Bathrooms', 'residence-elementor'),
                    'icon_type' => 'theme_options',
                    ],
                    [
                    'id' => 'property_garage',
                    'field_type' => 'property-garage',
                    'label_singular' => esc_html__('Garage', 'residence-elementor'),
                    'label_plural' => esc_html__('Garages', 'residence-elementor'),
                    'icon_type' => 'theme_options',
                    ],
                    [
                    'id' => 'property_size',
                    'field_type' => 'property_size',
                    'label_singular' => esc_html__('Area Size', 'residence-elementor'),
                    'label_plural' => '',
                    'icon_type' => 'theme_options',
                    ],
                    [
                    'id' => 'property_year',
                    'field_type' => 'property_year',
                    'label_singular' => esc_html__('Year Built', 'residence-elementor'),
                    'label_plural' => '',
                    'icon_type' => 'theme_options',
                    ],
                ],
            'title_field' => '{{{ label_singular }}}',
            ]
        );


        $this->add_responsive_control(
            'item_size', [
                'label' => esc_html__('Detail Section Width', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'], // Added '%' here
                'range' => [
                    'px' => [
                        'min' => 75,
                        'max' => 200,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => [
                    'size' => '15%',
                    'unit' => '',
                ],
                'tablet_default' => [
                    'size' => '20',
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'size' => '40',
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} .overview_element' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->end_controls_section();


        /* -------------------------------------------------------------------------------------------------
         * Start shadow section
         */
        $this->start_controls_section(
                'section_grid_box_shadow', [
            'label' => esc_html__('Box Shadow', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'box_shadow',
            'label' => esc_html__('Box Shadow', 'residence-elementor'),
            'selector' => '{{WRAPPER}} .property-panel',
                ]
        );

        $this->end_controls_section();
        /*
         * -------------------------------------------------------------------------------------------------
         * End shadow section
         */
        $this->start_controls_section(
                'section_spacing_margin_section', [
            'label' => esc_html__('Spaces & Sizes', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_responsive_control(
                'property_title_margin_bottom', [
            'label' => esc_html__('Title Margin Bottom (px)', 'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'devices' => ['desktop', 'tablet', 'mobile'],
            'desktop_default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'mobile_default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .panel-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
                'property_content_padding', [
            'label' => esc_html__('Content Area Padding', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'default' => [
                'top' => '30',      // Default top padding
                'right' => '30',    // Default right padding
                'bottom' => '30',   // Default bottom padding
                'left' => '30',     // Default left padding
            ],
            'selectors' => [
                '{{WRAPPER}} .property-panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
                'border_radius', [
            'label' => esc_html__('Border Radius', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .property-panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->end_controls_section();
        /*
         * -------------------------------------------------------------------------------------------------
         * End shadow section
         */
        /*
         * -------------------------------------------------------------------------------------------------
         * Start typography section
         */
        $this->start_controls_section(
                'typography_section', [
            'label' => esc_html__('Typography', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'property_title',
            'label' => esc_html__('Property Title', 'residence-elementor'),
          
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .panel-title',
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'property_content',
            'label' => esc_html__('Property Labels', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .overview_element a, {{WRAPPER}}  .overview_element li',
                ]
        );

        $this->end_controls_section();
        /*
         * -------------------------------------------------------------------------------------------------
         * End shadow section
         */
        /*





          /*
         * -------------------------------------------------------------------------------------------------
         * Start color section
         */
        $this->start_controls_section(
                'section_colors', [
            'label' => esc_html__('Colors', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_control(
                'unit_color', [
            'label' => esc_html__('Background Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .property-panel' => 'background-color: {{VALUE}}',
            ],
                ]
        );
        $this->add_control(
                'title_color', [
            'label' => esc_html__('Section Title Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .panel-title' => 'color: {{VALUE}}',
            ],
                ]
        );
        $this->add_control(
                'unit_font_color', [
            'label' => esc_html__('Text Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .overview_element ' => 'color: {{VALUE}}',
                '{{WRAPPER}} .overview_element li' => 'color: {{VALUE}}',
                '{{WRAPPER}} .overview_element i' => 'color: {{VALUE}}',
                '{{WRAPPER}} .overview_element a' => 'color: {{VALUE}}',
                '{{WRAPPER}} .overview_element img' => 'color: {{VALUE}};fill: {{VALUE}}',
                '{{WRAPPER}} .overview_element path' => 'color: {{VALUE}};fill: {{VALUE}}',
                '{{WRAPPER}} h4' => 'color: {{VALUE}}',
            ],
                ]
        );
        $this->end_controls_section();
        /*
         * -------------------------------------------------------------------------------------------------
         * End shadow section
         */
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
        echo property_show_overview_section_function($attributes, $settings);
    }

}
