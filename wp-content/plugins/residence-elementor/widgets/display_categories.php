<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Wpresidence_Display_Categories extends Widget_Base {

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
        return 'WpResidence_Display_Categories';
    }

    public function get_categories() {
        return ['wpresidence'];
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
        return __('Display Categories', 'residence-elementor');
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
        return ' wpresidence-note  eicon-product-categories';
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
    public function elementor_transform($input) {
        $output = array();
        if (is_array($input)) {
            foreach ($input as $key => $tax) {
                $output[$tax['value']] = $tax['label'];
            }
        }
        return $output;
    }

    protected function register_controls() {
        global $all_tax;

        $all_tax_elemetor = $this->elementor_transform($all_tax);

        $featured_listings = array('no' => 'no', 'yes' => 'yes');
        $items_type = array('properties' => 'properties', 'articles' => 'articles');
        $alignment_type = array('vertical' => 'vertical', 'horizontal' => 'horizontal');

        $list_cities_or_areas = array(
            1 => 'Design Type 1', 
            2 => 'Design Type 2', 
            3 => 'Design Type 3(inline)', 
            4 => 'Design Type 4'
        );

        $this->start_controls_section(
                'section_content', [
            'label' => __('Content', 'residence-elementor'),
                ]
        );

        $this->add_control(
                'place_list', [
            'label' => __('Type Categories,Actions,Cities,Areas,Neighborhoods or County name you want to show', 'residence-elementor'),
            'label_block' => true,
            'type' => \Elementor\Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => $all_tax_elemetor,
                ]
        );

        $this->add_control(
                'place_per_row', [
            'label' => __('Items per row (1,2,3,4 or 6)', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => 1,
                ]
        );



        $this->add_control(
                'place_type', [
            'label' => __('Type', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $list_cities_or_areas,
            'default' => 1,
                ]
        );




 $this->add_control(
                'display_grid',
                [
                    'label' => esc_html__('Display as grid?', 'residence-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => esc_html__('Yes', 'residence-elementor'),
                    'label_off' => esc_html__('No', 'residence-elementor'),
                    'return_value' => 'yes',
                    'default' => esc_html__('No', 'residence-elementor'),
                    'description'=> esc_html__('There is no fixed number of units. The grid will auto adjust to display units with a minimum width set by the control below.?', 'residence-elementor'),
                ]
        );

        $this->add_responsive_control(
                'display_grid_unit_width',
                [
                    'label' => esc_html__('Unit Minimum Width', 'residence-elementor'),
                    'condition' => [
                        'display_grid' => 'yes'
                    ],
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 220,
                            'max' => 400,
                        ],
                    ],
                    'devices' => ['desktop', 'tablet', 'mobile'],
                    'desktop_default' => [
                        'size' => '250',
                        'unit' => 'px',
                    ],
                    'tablet_default' => [
                        'size' => '250',
                        'unit' => 'px',
                    ],
                    'mobile_default' => [
                        'size' => '250',
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .items_shortcode_wrapper_grid' => '  grid-template-columns: repeat(auto-fit, minmax({{SIZE}}{{UNIT}}, 1fr));',
                    ],
                    
                ]
        );

        $this->add_responsive_control(
                'display_grid_unit_gap',
                [
                    'label' => esc_html__('Gap between units in px', 'residence-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'condition' => [
                        'display_grid' => 'yes'
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'devices' => ['desktop', 'tablet', 'mobile'],
                    'desktop_default' => [
                        'size' => '10',
                        'unit' => 'px',
                    ],
                    'tablet_default' => [
                        'size' => '10',
                        'unit' => 'px',
                    ],
                    'mobile_default' => [
                        'size' => '10',
                        'unit' => 'px',
                    ],
                    'default' => [
					'unit' => 'px',
					'size' => 10,
				],
                    'selectors' => [
                        '{{WRAPPER}} .items_shortcode_wrapper_grid' => 'gap: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );


        $this->end_controls_section();

        /*
         * -------------------------------------------------------------------------------------------------
         * Start Sizes
         */

        $this->start_controls_section(
                'size_section', [
            'label' => esc_html__('Item Settings', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );


        $this->add_responsive_control(
            'item_height', [
                'label' => esc_html__('Item Height', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 150,
                        'max' => 500,
                    ],
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'default' => [
					'unit' => 'px',
					'size' => 350,
				],
                'condition' => [
                    'place_type!' => '3',
                ],
                'selectors' => [
                    '{{WRAPPER}} .places_wrapper_type_2' => 'height: {{SIZE}}{{UNIT}}!important;',
                    '{{WRAPPER}} .places_wrapper_type_2 .places_background_image' => 'height: {{SIZE}}{{UNIT}}!important;',          
                    '{{WRAPPER}} .places_wrapper_type_3' => 'height: {{SIZE}}{{UNIT}}!important;',
                    '{{WRAPPER}} .property_listing.places_listing' => 'height: {{SIZE}}{{UNIT}}!important;',
                    '{{WRAPPER}} .property_listing_square' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .places_wrapper_type_4' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        


        $this->add_responsive_control(
                'item_height_square', [
            'label' => esc_html__('Item Size', 'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 50,
                    'max' => 500,
                ],
            ],
            'condition' => [
                'place_type' => '3',
            ],
            'devices' => ['desktop', 'tablet', 'mobile'],
            'desktop_default' => [
                'size' => 75,
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
                '{{WRAPPER}} .places_square_backgorund_image ' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
            ],
                ]
        );






        $this->add_responsive_control(
                'item_border_radius', [
            'label' => esc_html__('Border Radius', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .places_wrapper_type_3' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .places_wrapper_type_2' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .places_cover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .elementor_places_wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .listing_wrapper .property_listing' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .property_listing_square' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .places_background_image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .places_wrapper_type_3 .places_square_backgorund_image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
           
           
            ],
                ]
        );


        $this->end_controls_section();



        /*
         * -------------------------------------------------------------------------------------------------
         * Start Typografy
         */

        $this->start_controls_section(
                'typography_section', [
            'label' => esc_html__('Style', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'tax_title',
                'label' => esc_html__('Title Typography', 'residence-elementor'),
               'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                'selector' => '{{WRAPPER}} .places_wrapper_type_2 h4 a, {{WRAPPER}} .places_type_4_content h4 a, {{WRAPPER}} .places_type_4_content, {{WRAPPER}} .property_listing h4, {{WRAPPER}} .listing_wrapper_desgin_3 h4, {{WRAPPER}} .places_wrapper_type_1 h4 a, {{WRAPPER}} .places_wrapper_type_3 h4 a',
                'fields_options' => [
                    'font_weight' => [
                        'default' => '500',
                    ],
                    'font_family' => [
                        'default' => 'Roboto',
                    ],
                    'font_size' => [
                        'default' => ['unit' => 'px', 'size' => 18],
                    ],
                ],
            ]
        );
        
        $this->add_responsive_control(
                'property_title_margin_bottom', [
            'label' => esc_html__('Title Margin Bottom (px) - Design Type 2', 'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'condition' => [
                'place_type' => '2',
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
                '{{WRAPPER}} .places_wrapper_type_2 h4' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
                'property_tagline_margin_bottom', [
            'label' => esc_html__('Tagline Margin Bottom(px) - Design Type 2', 'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'condition' => [
                'place_type' => '2',
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
                '{{WRAPPER}} .places_type_2_tagline' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
                ]
        );
        
            $this->add_responsive_control(
                'property_detail_section_margin', [
            'label' => esc_html__('Details Section Margin Bottom (px) - Design Type 2', 'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'condition' => [
                'place_type' => '2',
            ],
            'devices' => ['desktop', 'tablet', 'mobile'],
            'desktop_default' => [
                'size' => '13',
                'unit' => '%',
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
                '{{WRAPPER}} .places_type_2_content' => 'bottom: {{SIZE}}{{UNIT}};',
            ],
                ]
        );


        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'tax_tagline',
            'label' => esc_html__('Tagline Typography - Design Type 2', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .places_type_2_tagline',
            'condition' => [
                'place_type' => '2',
            ],
            'fields_options' => [
                // Inner control name
                'font_weight' => [
                    // Inner control settings
                    'default' => '300',
                ],
                'font_family' => [
                    'default' => 'Roboto',
                ],
                'font_size' => ['default' => ['unit' => 'px', 'size' => 14]],
            ],
                ]
        );



        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'tax_listings',
            'label' => esc_html__('Listings Text Typography', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .places_type_2_listings_no,{{WRAPPER}} .places_wrapper_type_1 .property_location,{{WRAPPER}} .places_type_4_listings_no{{WRAPPER}} .property_listing.places_listing .property_location,{{WRAPPER}} .property_location_type_3',
            'fields_options' => [
                // Inner control name
                'font_weight' => [
                    // Inner control settings
                    'default' => '300',
                ],
                'font_family' => [
                    'default' => 'Roboto',
                ],
                'font_size' => ['default' => ['unit' => 'px', 'size' => 14]],
            ],
                ]
        );


        $this->add_control(
                'tax_title_color', [
            'label' => esc_html__('Title Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .places_wrapper_type_2 h4 a' => 'color: {{VALUE}}',
                '{{WRAPPER}} .property_listing h4' => 'color: {{VALUE}}',
                '{{WRAPPER}} .places_list_1 h4 a' => 'color: {{VALUE}}',
                '{{WRAPPER}} .listing_wrapper_desgin_3 h4 a' => 'color: {{VALUE}}',
                '{{WRAPPER}} .places_wrapper_type_1 h4 a' => 'color: {{VALUE}}',
                '{{WRAPPER}} .places_wrapper_type_3 h4 a' => 'color: {{VALUE}}',      
                '{{WRAPPER}} .places_type_4_content h4 a' => 'color: {{VALUE}}', 
                
            ],
                ]
        );

        // Control for Hover Title Color
        $this->add_control(
            'tax_title_hover_color', [
                'label' => esc_html__('Hover Title Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .places_wrapper_type_2 h4 a:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .property_listing h4:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .places_list_1 h4 a:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .listing_wrapper_desgin_3 h4 a:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .places_wrapper_type_1 h4 a:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .places_wrapper_type_3 h4 a:hover' => 'color: {{VALUE}}',      
                    '{{WRAPPER}} .places_type_4_content h4 a:hover' => 'color: {{VALUE}}', 
                ],
            ]
        );

        $this->add_control(
                'tax_tagline_color', [
            'label' => esc_html__('Subtitle Color - Design Type2', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'condition' => [
                'place_type' => '2',
            ],
            'selectors' => [
                '{{WRAPPER}}  .places_type_2_tagline' => 'color: {{VALUE}}',
            ],
                ]
        );

        $this->add_control(
                'tax_listings_color', [
            'label' => esc_html__('Listings text Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .places_type_2_listings_no' => 'color: {{VALUE}}',
                '{{WRAPPER}} .property_listing.places_listing .property_location' => 'color: {{VALUE}}',
                '{{WRAPPER}} .property_location_type_3' => 'color: {{VALUE}}',
                '{{WRAPPER}} .places_type_4_listings_no' => 'color: {{VALUE}}',
            ],
                ]
        );

        $this->add_control(
                'tax_listings_color_back', [
            'label' => esc_html__('Listings Background Color - Design Type 2', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'condition' => [
                'place_type' => '2',
            ],
            'selectors' => [
                '{{WRAPPER}}  .places_type_2_listings_no' => 'background: {{VALUE}}',
            ],
                ]
        );


        $this->add_control(
                'ovarlay_color_back', [
            'label' => esc_html__('Image Overlay Background Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .places_cover' => 'background: {{VALUE}};opacity: 1;',
            ],
                ]
        );

        $this->add_control(
                'ovarlay_color_back_hover', [
            'label' => esc_html__('Image Overlay Background Color Hover', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .places_cover:hover' => 'background: {{VALUE}};opacity: 1;',
                '{{WRAPPER}} .listing_wrapper_desgin_3:hover .places_cover' => 'background: {{VALUE}};opacity: 1;',
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
            'selector' => '{{WRAPPER}} .places_wrapper_type_2 .places_background_image,{{WRAPPER}} .places_wrapper_type_3,{{WRAPPER}} .places_listing',
           
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
    public function wpresidence_send_to_shortcode($input) {
        $output = '';
        if ($input !== '') {
            $numItems = count($input);
            $i = 0;

            foreach ($input as $key => $value) {
                $output .= $value;
                if (++$i !== $numItems) {
                    $output .= ', ';
                }
            }
        }
        return $output;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $attributes['place_list'] = $this->wpresidence_send_to_shortcode($settings['place_list']);
        $attributes['place_per_row'] = $settings['place_per_row'];
        $attributes['place_type'] = $settings['place_type'];

        if(isset($settings['item_height'])):
            $attributes['item_height'] = $settings['item_height'];
        endif;
        
        $attributes['display_grid']         =   $settings['display_grid'];

        echo wpestate_places_list_function($attributes);
    }

}
