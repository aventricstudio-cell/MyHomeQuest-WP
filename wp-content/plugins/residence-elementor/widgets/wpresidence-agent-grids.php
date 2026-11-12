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

class Wpresidence_Agent_Grids extends Widget_Base {

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
        return 'Wpresidence_Agent_Grids';
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
        return __('Agent Grids', 'residence-elementor');
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
                'content_section', [
            'label' => esc_html__('Content', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        $this->add_control(
                'wpresidence_grid_type', [
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
                'grid_taxonomy', [
            'label' => esc_html__('Select Agents', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => get_list_agents_elementor(),
            'description' => '',
                ]
        );




        $this->add_control(
            'order_by', [
                'label' => esc_html__('Order by', 'residence-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'ID' => esc_html__('ID', 'residence-elementor'),
                    'title' => esc_html__('Title/Name', 'residence-elementor'),
                    'date' => esc_html__('Date', 'residence-elementor')
                ],
                'default' => 'ID',
            ]
        );
        
        $this->add_control(
            'order', [
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
                'items_no', [
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
            'desktop_default' => [
                'size' => 300,
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
                '{{WRAPPER}} .places_wrapper_type_2' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} 	.property_listing.places_listing' => 'height: {{SIZE}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
                'item_border_radius', [
            'label' => esc_html__('Border Radius', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .places_wrapper_type_2,
                {{WRAPPER}} .places_wrapper_type_2 .places_cover,
                {{WRAPPER}} .elementor_places_wrapper,
                {{WRAPPER}} .listing_wrapper .property_listing,
                {{WRAPPER}} .places_cover.agent_grid_elementor' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );



        $this->add_responsive_control(
                'wpersidence_item_column_gap', [
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
                '{{WRAPPER}} .elementor_residence_grid' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
                'wpersidence_item_row_gap', [
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
                '{{WRAPPER}} .elementor_places_wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
            'selector' => '{{WRAPPER}} .places_wrapper_type_2 h4 a,{{WRAPPER}} .property_listing h4',
            'fields_options' => [
                // Inner control name
                'font_weight' => [
                    // Inner control settings
                    'default' => '500',
                ],
                'font_family' => [
                    'default' => 'Roboto',
                ],
                'font_size' => ['default' => ['unit' => 'px', 'size' => 24]],
            ],
                ]
        );
        $this->add_responsive_control(
                'property_title_margin_bottom', [
            'label' => esc_html__('Title Margin Bottom', 'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 200,
                ],
            ],
            'devices' => ['desktop', 'tablet', 'mobile'],
            'desktop_default' => [
                'size' => '40',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'size' => '40',
                'unit' => 'px',
            ],
            'mobile_default' => [
                'size' => '40',
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .realtor_name' => 'bottom: {{SIZE}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
                'property_tagline_margin_bottom', [
            'label' => esc_html__('Agent Position Margin Bottom(px) ', 'residence-elementor'),
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
                '{{WRAPPER}} .property_location' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
                ]
        );







        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'tax_listings',
            'label' => esc_html__('Agent Position Typography', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .property_location.realtor_position',
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
                '{{WRAPPER}} .elementor_places_wrapper h4 a' => 'color: {{VALUE}}',
            ],
                ]
        );



        $this->add_control(
                'tax_listings_color', [
            'label' => esc_html__('Agent Position Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .property_location.realtor_position' => 'color: {{VALUE}}',
            ],
                ]
        );


        $this->start_controls_tabs(
                'style_tabs'
        );

        $this->start_controls_tab(
            'style_normal_tab', [
                'label' => __(' Image Overlay Color', 'plugin-name'),
            ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(), [
                'name' => 'overlay_back_background',
                'label' => __('Image Overlay Color', 'plugin-domain'),
                'types' => ['classic', 'gradient',],
                'selector' => '{{WRAPPER}} .property_listing.places_listing .places_cover ',
            ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
                'style_hover_tab', [
            'label' => __('Hover Overlay Color', 'plugin-name'),
                ]
        );

        $this->add_group_control(
                \Elementor\Group_Control_Background::get_type(), [
            'name' => 'overlay_back_background_hover',
            'label' => __('Hover Overlay Color', 'plugin-domain'),
            'types' => ['classic', 'gradient',],
            'selector' => '{{WRAPPER}} .property_listing.places_listing:hover .places_cover ',
                ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();

         /*
         * -------------------------------------------------------------------------------------------------
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
            'selector' => '{{WRAPPER}} .elementor_wpresidece_grid .listing_wrapper',
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
     
        $args['grid_taxonomy'] = $settings['grid_taxonomy'];

        $args['orderby'] = $settings['order_by'];
        $args['order'] = $settings['order'];
        $args['wpresidence_design_type'] = 1;
        $args['items_no'] = $settings['items_no'];



        echo wpresidence_display_agents_grid($args);
    }

}
