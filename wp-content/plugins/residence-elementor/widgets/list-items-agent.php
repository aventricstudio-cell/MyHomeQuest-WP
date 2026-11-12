<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_ListItems_Agent extends Widget_Base {

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
        return 'WpResidence_List_Items_per_Agent';
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
        return __('List Items per Agent', 'residence-elementor');
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
        return ' wpresidence-note eicon-bullet-list';
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

        $featured_listings = array('no' => 'no', 'yes' => 'yes');
        $items_type = array('properties' => 'properties', 'articles' => 'articles');
        $alignment_type = array('vertical' => 'vertical', 'horizontal' => 'horizontal');
        $agent_array = wpestate_return_agent_array();
        $agent_array_elemetor = $this->elementor_transform($agent_array);

        $this->start_controls_section(
                'section_content',
                [
                    'label' => __('Content', 'residence-elementor'),
                ]
        );

        $this->add_control(
                'agentid',
                [
                    'label' => __('Type and Select Agent, Agency or Developer', 'residence-elementor'),
                    'label_block' => true,
                    'type' => \Elementor\Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => $agent_array_elemetor,
                ]
        );

        $this->add_control(
                'nooflisting',
                [
                    'label' => __('Number of Listings', 'residence-elementor'),
                    'type' => Controls_Manager::TEXT,
                    'default' => 3,
                    'Label Block'
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
                    'description' => esc_html__('There is no fixed number of units. The grid will auto adjust to display units with a minimum width set by the control below.?', 'residence-elementor'),
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
            'selector' => '{{WRAPPER}} .property_listing ',
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

        $attributes['agentid'] = $this->wpresidence_send_to_shortcode($settings['agentid']);
        $attributes['nooflisting'] = $settings['nooflisting'];

        $attributes['display_grid'] = $settings['display_grid'];

        echo wplistingsperagent_shortcode_function($attributes);
    }
}
