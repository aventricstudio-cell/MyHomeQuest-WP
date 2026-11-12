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

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Grids extends Widget_Base {

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
        return 'Wpresidence_Grids';
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
        return __('Category Grid Builder', 'residence-elementor');
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
        return ' wpresidence-note eicon-posts-masonry';
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

        $this->start_controls_section(
                'content_section',
                [
                    'label' => esc_html__('Content', 'residence-elementor'),
                    'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        $this->add_control(
                'wpresidence_grid_type',
                [
                    'label' => esc_html__('Select Grid Type', 'residence-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        1 => esc_html__('Type 1', 'residence-elementor'),
                        2 => esc_html__('Type 2', 'residence-elementor'),
                        3 => esc_html__('Type 3', 'residence-elementor'),
                        4 => esc_html__('Type 4', 'residence-elementor'),
                        5 => esc_html__('Type 5', 'residence-elementor'),
                        6 => esc_html__('Type 6', 'residence-elementor'),
                    ],
                    'description' => '',
                    'default' => 1,
                ]
        );

    $this->add_control(
        'wpresidence_design_type',
        [
            'label' => esc_html__('Select Design Type', 'residence-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                1 => esc_html__('Design Type 1', 'residence-elementor'),
                2 => esc_html__('Design Type 2', 'residence-elementor'),
                4 => esc_html__('Design Type 4', 'residence-elementor'),
            ],
            'description' => '',
            'default' => 1,
        ]
    );

    $this->add_control(
        'grid_taxonomy',
        [
            'label' => esc_html__('Select Categories', 'residence-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'property_category' => 'Category',
                'property_action_category' => 'Type',
                'property_city' => 'City',
                'property_area' => 'Area',
                'property_county_state' => 'County/State'
            ],
            'description' => '',
            'default' => 'property_category',
        ]
    );

    $all_taxonomies = get_object_taxonomies('estate_property', 'objects');

    if (!empty($all_taxonomies) && !is_wp_error($all_taxonomies)) {
        foreach ($all_taxonomies as $taxonomy_item) {

            $options_array = array();
            $terms = get_terms(
                    $taxonomy_item->name,
                    array(
                        'hide_empty' => false,
                    )
            );

            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    $options_array[$term->slug] = $term->name;
                }
            }

            $this->add_control(
                $taxonomy_item->name,
                [
                    'label' => $taxonomy_item->label,
                    'type' => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'label_block' => true,
                    'options' => $options_array,
                    'condition' => [
                        'grid_taxonomy' => $taxonomy_item->name,
                    ],
                ]
            );
        }
    }

        $this->add_control(
            'hide_empty_taxonomy',
            [
                'label' => esc_html__('Hide Empty', 'residence-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '0' => esc_html__('No', 'residence-elementor'),
                    '1' => esc_html__('Yes', 'residence-elementor')
                ],
                'description' => '',
                'default' => '1',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('Order By', 'residence-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'name' => esc_html__('Name', 'residence-elementor'),
                    'id' => esc_html__('ID', 'residence-elementor'),
                    'count' => esc_html__('Count', 'residence-elementor'),
                ],
                'description' => '',
                'default' => 'name',
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'residence-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'ASC' => esc_html__('ASC', 'residence-elementor'),
                    'DESC' => esc_html__('DESC', 'residence-elementor')
                ],
                'default' => 'ASC',
            ]
        );

        $this->add_control(
            'items_no',
            [
                'label' => esc_html__(' Number of Items to Show', 'residence-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => 9,
            ]
        );
    $this->end_controls_section();

    /*
    * -------------------------------------------------------------------------------------------------
    * Start Sizes
    */

    $this->start_controls_section(
            'size_section',
            [
                'label' => esc_html__('Item Settings', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
    );

        $this->add_responsive_control(
            'item_height',
            [
                'label' => esc_html__('Item Height', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'default' => [
					'unit' => 'px',
					'size' => 350,
				],
                'selectors' => [
                    '{{WRAPPER}} .places_wrapper_type_1' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .places_wrapper_type_2' => 'height: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} .places_wrapper_type_3' => 'height: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} .places_wrapper_type_4' => 'height: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );
        

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .places_wrapper_type_3'             => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .places_wrapper_type_2'             => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}}  .places_cover'                     => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .elementor_places_wrapper'          => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .listing_wrapper .property_listing' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .places_background_image'           => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'wpersidence_item_column_gap',
            [
                'label' => esc_html__('Form Columns Gap', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 15,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor_residence_grid' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
                ],
            ]
        );

        $this->add_responsive_control(
                'wpersidence_item_row_gap',
                [
                    'label' => esc_html__('Rows Gap', 'residence-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 15,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .places_wrapper_type_1, 
                        {{WRAPPER}} .places_wrapper_type_2, 
                        {{WRAPPER}} .places_wrapper_type_3, 
                        {{WRAPPER}} .places_wrapper_type_4' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );

        $this->end_controls_section();

/*
* -------------------------------------------------------------------------------------------------
* Start Typografy
*/

$this->start_controls_section(
    'typography_section',
    [
        'label' => esc_html__('Style', 'residence-elementor'),
        'tab' => Controls_Manager::TAB_STYLE,
    ]
);

    $this->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'tax_title',
            'label' => esc_html__('Title Typography', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .places_wrapper_type_2 h4 a,{{WRAPPER}} .places_wrapper_type_1 h4 a,{{WRAPPER}} .property_listing h4 a,{{WRAPPER}} .places_wrapper_type_4 h4 a',
            'fields_options' => [
                // Inner control name
                'font_weight' => [
                    // Inner control settings
                    'default' => '500',
                ],
                'font_family' => [
                    'default' => 'Roboto',
                ],
                'font_size' => ['default' => ['unit' => 'px', 'size' => 18]],
            ],
        ]
    );

    $this->add_responsive_control(
        'property_title_margin_bottom',
        [
            'label' => esc_html__('Title Margin Bottom (px) - Design Type 2', 'residence-elementor'),
            'condition' => [
                'wpresidence_design_type' => '2', // Check if Design Type 2 is selected
            ],
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
                '{{WRAPPER}} .places_wrapper_type_2 h4' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'property_tagline_margin_bottom',
        [
            'label' => esc_html__('Tagline Margin Bottom (px) - Design Type 2', 'residence-elementor'),
            'condition' => [
                'wpresidence_design_type' => '2', // Check if Design Type 2 is selected
            ],
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
                '{{WRAPPER}} .places_type_2_tagline' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    $this->add_responsive_control(
        'property_listings_margin_bottom',
        [
            'label' => esc_html__('Listings Number Margin Bottom - Design Type 2', 'residence-elementor'),
            'condition' => [
                'wpresidence_design_type' => '2', // Check if Design Type 2 is selected
            ],
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => -30,
                    'max' => 200,
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
                '{{WRAPPER}} .places_type_2_listings_no' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    $this->add_group_control(
        Group_Control_Typography::get_type(),
        [
            'name' => 'tax_tagline',
            'label' => esc_html__('Tagline Typography - Design Type 2', 'residence-elementor'),
            'condition' => [
                'wpresidence_design_type' => '2', // Check if Design Type 2 is selected
            ],
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .places_type_2_tagline',
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
        Group_Control_Typography::get_type(),
        [
            'name' => 'tax_listings',
            'label' => esc_html__('Listings Text Typography', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .places_type_2_listings_no,{{WRAPPER}} .places_type_4_listings_no,{{WRAPPER}} .property_listing.places_listing .property_location',
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
        'tax_title_color',
        [
        'label' => esc_html__('Title Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .places_wrapper_type_2 h4 a' => 'color: {{VALUE}}',
            '{{WRAPPER}} .property_listing h4' => 'color: {{VALUE}}',
            '{{WRAPPER}} .elementor_places_wrapper h4 a' => 'color: {{VALUE}}',
            '{{WRAPPER}} .places_wrapper_type_1 h4 a' => 'color: {{VALUE}}',
        ],
        ]
    );

    // Control for Hover Title Color
    $this->add_control(
        'tax_title_hover_color',
        [
        'label' => esc_html__('Hover Title Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .places_wrapper_type_2 h4 a:hover' => 'color: {{VALUE}}',
            '{{WRAPPER}} .property_listing h4:hover' => 'color: {{VALUE}}',
            '{{WRAPPER}} .elementor_places_wrapper h4 a:hover' => 'color: {{VALUE}}',
            '{{WRAPPER}} .places_wrapper_type_1 h4 a:hover' => 'color: {{VALUE}}',
        ],
        ]
    );

    // Add Subtitle Color Control for Design Type 2
    $this->add_control(
        'tax_tagline_color',
        [
            'label' => esc_html__('Subtitle Color - Design Type 2', 'residence-elementor'),
            'condition' => [
                'wpresidence_design_type' => '2', // Check if Design Type 2 is selected
            ],
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  .places_type_2_tagline' => 'color: {{VALUE}}',
            ],
        ]
    );
    $this->add_control(
        'tax_listings_color',
        [
            'label' => esc_html__('Listings No Text Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  .places_type_2_listings_no' => 'color: {{VALUE}}',
                '{{WRAPPER}} .property_listing.places_listing .property_location' => 'color: {{VALUE}}',
                '{{WRAPPER}} .places_type_4_listings_no' => 'color: {{VALUE}}',   
            ],
        ]
    );

    $this->add_control(
        'tax_listings_color_back',
        [
            'label' => esc_html__('Listings No Background Color - Design Type 2', 'residence-elementor'),
            'condition' => [
                'wpresidence_design_type' => '2', // Check if Design Type 2 is selected
            ],
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}  .places_type_2_listings_no' => 'background: {{VALUE}}',
            ],
        ]
    );

    $this->add_control(
        'ovarlay_color_back',
        [
            'label' => esc_html__('Image Overlay Background Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .places_cover' => 'background: {{VALUE}};opacity: 1;',
            ],
        ]
    );

    $this->add_control(
        'ovarlay_color_back_hover',
        [
            'label' => esc_html__('Image Overlay Background Color Hover', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .places_cover:hover' => 'background: {{VALUE}};opacity: 1;',
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
            'selector' => '{{WRAPPER}} .places_wrapper_type_2,{{WRAPPER}} .places_wrapper_type_3,{{WRAPPER}} .places_listing',
            ]
    );
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
    public function wpestate_drop_posts($post_type) {
        $args = array(
            'numberposts' => -1,
            'post_type' => $post_type
        );

        $posts = get_posts($args);
        $list = array();
        foreach ($posts as $cpost) {

            $list[$cpost->ID] = $cpost->post_title;
        }
        return $list;
    }

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
        $args['type'] = $settings['wpresidence_grid_type'];
        $args['wpresidence_design_type'] = $settings['wpresidence_design_type'];
        $args['grid_taxonomy'] = $settings['grid_taxonomy'];
        $args[$settings['grid_taxonomy']] = $settings[$settings['grid_taxonomy']];
        $args['order'] = $settings['order'];
        $args['orderby'] = $settings['orderby'];
        $args['items_no'] = $settings['items_no'];
        $args['hide_empty_taxonomy'] = $settings['hide_empty_taxonomy'];

        echo wpresidence_display_grids($args);
    }
}
