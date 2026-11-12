<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class Wpresidence_Term_Listings extends Widget_Base
{

    /**
     * Retrieve the widget name.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'Wpresidence_Term_List_Properties';
    }

    public function get_categories()
    {
        return [ 'category_widgets' ];
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
    public function get_title()
    {
        return __('Listings per Term', 'residence-elementor');
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
    public function get_icon()
    {
        return 'wpresidence-note eicon-posts-grid';
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
    public function get_script_depends()
    {
        return [ '' ];
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
    public function elementor_transform($input)
    {
        $output=array();
        if (is_array($input)) {
            foreach ($input as $key=>$tax) {
                $output[$tax['value']]=$tax['label'];
            }
        }
        return $output;
    }


    protected function register_controls()
    {
        global $all_tax;

        $all_tax_elemetor=$this->elementor_transform($all_tax);



        $property_category_values       =   wpestate_generate_category_values_shortcode();
        $property_city_values           =   wpestate_generate_city_values_shortcode();
        $property_area_values           =   wpestate_generate_area_values_shortcode();
        $property_county_state_values   =   wpestate_generate_county_values_shortcode();
        $property_action_category_values=   wpestate_generate_action_values_shortcode();
        $property_status_values         =   wpestate_generate_status_values_shortcode();
        $property_features_values       =   wpestate_generate_features_values_shortcode();


        $property_category_values           =   $this->elementor_transform($property_category_values);
        $property_city_values               =   $this->elementor_transform($property_city_values);
        $property_area_values               =   $this->elementor_transform($property_area_values);
        $property_county_state_values       =   $this->elementor_transform($property_county_state_values);
        $property_action_category_values    =   $this->elementor_transform($property_action_category_values);
        $property_status_values             =   $this->elementor_transform($property_status_values);
        $property_features_values           =   $this->elementor_transform($property_features_values);



        $featured_listings  =   array('no'=>'no','yes'=>'yes');
        $items_type         =   array('properties'=>'properties','articles'=>'articles');
        $alignment_type     =   array('vertical'=>'vertical','horizontal'=>'horizontal');


        $sort_options = array();
        if( function_exists('wpestate_listings_sort_options_array')){
          $sort_options			= wpestate_listings_sort_options_array();
        }

        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Content', 'residence-elementor'),
            ]
        );





        $this->add_control(
            'category_ids',
            [
                'label' => __('Select Property Categories', 'residence-elementor'),
                'label_block'=>true,
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $property_category_values,
]
        );

        $this->add_control(
            'action_ids',
            [
                            'label' => __('Select Property Types', 'residence-elementor'),
                             'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_action_category_values,
            ]
        );

        $this->add_control(
            'city_ids',
            [
                            'label' => __('Select Property Cities', 'residence-elementor'),
                             'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_city_values,
            ]
        );
        $this->add_control(
            'area_ids',
            [
                            'label' => __('Select Property Areas', 'residence-elementor'),
                             'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_area_values,
            ]
        );
        $this->add_control(
            'state_ids',
            [
                            'label' => __('Select Property Counties / States', 'residence-elementor'),
                            'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_county_state_values,
            ]
        );

        $this->add_control(
            'features_ids',
            [
                            'label' => __('Select Property Features', 'residence-elementor'),
                            'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_features_values,
            ]
        );

        $this->add_control(
            'status_ids',
            [
                            'label' => __('Select Property Status', 'residence-elementor'),
                            'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'options' => $property_status_values,
            ]
        );




        $this->add_control(
            'number',
            [
                            'label' => __('No of items', 'residence-elementor'),
                            'type' => Controls_Manager::TEXT,
                            'default' => 9,
            ]
        );

        $this->add_control(
            'rownumber',
            [
                'label' => __('No of items per row', 'residence-elementor'),
                'type' => Controls_Manager::TEXT,
                                'default' => 3,
            ]
        );


       $this->add_control(
    'align',
    [
        'label' => __('Property Card Alignment', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'vertical',
        'options' => [
            'vertical' => 'Vertical',
            'horizontal' => 'Horizontal'
        ]
    ]
);
        $this->add_control(
            'show_featured_only',
            [
                            'label' => __('Show featured listings only?', 'residence-elementor'),
                            'type' => \Elementor\Controls_Manager::SELECT,
                            'default' => 'no',
                            'options' => $featured_listings
            ]
        );



        $this->add_control(
            'sort_by',
            [
                            'label' => __('Sort By?', 'residence-elementor'),
                            'type' => \Elementor\Controls_Manager::SELECT,
                            'default' => 0,
                            'options' => $sort_options
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

        $this->end_controls_section();

/*
* -------------------------------------------------------------------------------------------------
* Filters Border section
*/
        $this->start_controls_section(
            'section_filters_border',
            [
                'label' => esc_html__( 'Filters Border', 'residence-elementor' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // Border width (responsive)
        $this->add_responsive_control(
            'filters_border_width',
            [
                'label' => esc_html__( 'Border Width', 'residence-elementor' ),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                        'step'=> 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .listing_filters_head' =>
                        'border-style: solid; border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // Border color
        $this->add_control(
            'filters_border_color',
            [
                'label' => esc_html__( 'Border Color', 'residence-elementor' ),
                'type'  => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .listing_filters_head' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();


       // Start Pagination Position Section
    $this->start_controls_section(
        'section_pagination_position',
        [
            'label' => esc_html__('Pagination Position', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]
    );

    // Add Control for Pagination Position
    $this->add_control(
        'pagination_position',
        [
            'label' => __('Pagination Position', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'flex-start' => __('Left', 'residence-elementor'),
                'center' => __('Center', 'residence-elementor'),
                'flex-end' => __('Right', 'residence-elementor'),
            ],
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .pagination_ajax' => 'justify-content: {{VALUE}};',
                '{{WRAPPER}} .pagination' => 'justify-content: {{VALUE}};',
            ],
            'render_type' => 'ui',
        ]
    );

    // End Pagination Position Section
    $this->end_controls_section();

    // show hide
    // Add this section after your existing sections in register_controls()

$this->start_controls_section(
    'section_filter_controls',
    [
        'label' => __('Filter Controls', 'residence-elementor'),
    ]
);

$this->add_control(
    'hide_filters_bar',
    [
        'label' => __('Hide Filters Bar', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Hide', 'residence-elementor'),
        'label_off' => __('Show', 'residence-elementor'),
        'return_value' => 'yes',
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .listing_filters_head' => 'display: none !important;',
        ],
    ]
);

$this->add_control(
    'hide_types_filter',
    [
        'label' => __('Hide Types Filter', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Hide', 'residence-elementor'),
        'label_off' => __('Show', 'residence-elementor'),
        'return_value' => 'yes',
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .wpresidence_wrap_a_filter_action' => 'display: none !important;',
        ],
        'condition' => [
            'hide_filters_bar!' => 'yes',
        ],
    ]
);

$this->add_control(
    'hide_categories_filter',
    [
        'label' => __('Hide Categories Filter', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Hide', 'residence-elementor'),
        'label_off' => __('Show', 'residence-elementor'),
        'return_value' => 'yes',
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .wpresidence_wrap_a_filter_categ' => 'display: none !important;',
        ],
        'condition' => [
            'hide_filters_bar!' => 'yes',
        ],
    ]
);

$this->add_control(
    'hide_states_filter',
    [
        'label' => __('Hide States Filter', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Hide', 'residence-elementor'),
        'label_off' => __('Show', 'residence-elementor'),
        'return_value' => 'yes',
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .wpresidence_wrap_a_filter_county' => 'display: none !important;',
        ],
        'condition' => [
            'hide_filters_bar!' => 'yes',
        ],
    ]
);

$this->add_control(
    'hide_cities_filter',
    [
        'label' => __('Hide Cities Filter', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Hide', 'residence-elementor'),
        'label_off' => __('Show', 'residence-elementor'),
        'return_value' => 'yes',
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .wpresidence_wrap_a_filter_cities' => 'display: none !important;',
        ],
        'condition' => [
            'hide_filters_bar!' => 'yes',
        ],
    ]
);

$this->add_control(
    'hide_areas_filter',
    [
        'label' => __('Hide Areas Filter', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Hide', 'residence-elementor'),
        'label_off' => __('Show', 'residence-elementor'),
        'return_value' => 'yes',
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .wpresidence_wrap_a_filter_areas' => 'display: none !important;',
        ],
        'condition' => [
            'hide_filters_bar!' => 'yes',
        ],
    ]
);

$this->add_control(
    'hide_features_filter',
    [
        'label' => __('Hide Features Filter', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Hide', 'residence-elementor'),
        'label_off' => __('Show', 'residence-elementor'),
        'return_value' => 'yes',
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .wpresidence_wrap_a_filter_features' => 'display: none !important;',
        ],
        'condition' => [
            'hide_filters_bar!' => 'yes',
        ],
    ]
);

$this->add_control(
    'hide_status_filter',
    [
        'label' => __('Hide Status Filter', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Hide', 'residence-elementor'),
        'label_off' => __('Show', 'residence-elementor'),
        'return_value' => 'yes',
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .wpresidence_wrap_a_filter_status' => 'display: none !important;',
        ],
        'condition' => [
            'hide_filters_bar!' => 'yes',
        ],
    ]
);

$this->add_control(
    'hide_sort_filter',
    [
        'label' => __('Hide Sort Filter', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Hide', 'residence-elementor'),
        'label_off' => __('Show', 'residence-elementor'),
        'return_value' => 'yes',
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .wpresidence_wrap_a_filter_order' => 'display: none !important;',
        ],
        'condition' => [
            'hide_filters_bar!' => 'yes',
        ],
    ]
);

$this->add_control(
    'hide_view_toggles',
    [
        'label' => __('Hide Grid/List View Toggles', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Hide', 'residence-elementor'),
        'label_off' => __('Show', 'residence-elementor'),
        'return_value' => 'yes',
        'default' => '',
        'selectors' => [
            '{{WRAPPER}} .wpestate_list_grid_filter_wiew_wrapper' => 'display: none !important;',
        ],
        'condition' => [
            'hide_filters_bar!' => 'yes',
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

    public function wpresidence_send_to_shortcode($input)
    {
        $output='';
        if ($input!=='' && is_array($input)) {
            $numItems = count($input);
            $i = 0;

            foreach ($input as $key=>$value) {
                $output.=$value;
                if (++$i !== $numItems) {
                    $output.=', ';
                }
            }
        }
        return $output;
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();



        $attributes['category_ids']         =   $this -> wpresidence_send_to_shortcode($settings['category_ids']);
        $attributes['action_ids']           =   $this -> wpresidence_send_to_shortcode($settings['action_ids']);
        $attributes['city_ids']             =   $this -> wpresidence_send_to_shortcode($settings['city_ids']);
        $attributes['area_ids']             =   $this -> wpresidence_send_to_shortcode($settings['area_ids']);
        $attributes['state_ids']            =   $this -> wpresidence_send_to_shortcode($settings['state_ids']);
        $attributes['features_ids']         =   $this -> wpresidence_send_to_shortcode($settings['features_ids']);
        $attributes['status_ids']           =   $this -> wpresidence_send_to_shortcode($settings['status_ids']);
        $attributes['number']               =   $settings['number'];
        $attributes['rownumber']            =   $settings['rownumber'];
        $attributes['align']                =   $settings['align'];
        $attributes['show_featured_only']   =   $settings['show_featured_only'];
        $attributes['sort_by']       	    =   $settings['sort_by'];
              $attributes['context']       	    =   'term_ajax_listing';

        echo  '<input type="hidden" value="1" id="wpresidence_is_custom_category_template">'. wpestate_filter_list_properties($attributes);
    }
}
