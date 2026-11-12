<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Wpresidence_Display_Categories_As_Tabs extends Widget_Base {

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
        return 'Wpresidence_Display_Categories_As_Tabs';
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
        return __('Categories As Tabs', 'residence-elementor');
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

    protected function register_controls() {
        $all_tax_elemetor=array(
            'property_category'         =>  esc_html__('Categories','residence_elementor'),
            'property_action_category'  =>  esc_html__('Action Categories','residence_elementor'),
            'property_city'             =>  esc_html__('Cities','residence_elementor'),            
            'property_area'             =>  esc_html__('Neighborhood','residence_elementor'),           
            'property_county_state'     =>  esc_html__('County / State','residence_elementor'),          
            'property_features'         =>  esc_html__('Amenities & Features','residence_elementor'),           
            'property_status'           =>  esc_html__('Status','residence_elementor'),
    );

    $this->start_controls_section(
                'section_content', [
            'label' => __('Content', 'residence-elementor'),
                ]
    );
 
    $repeater = new Repeater();

    $repeater->add_control(
                'field_type', [
            'label' => esc_html__('Form Fields', 'residence-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => $all_tax_elemetor,
            'default' => 'text',
                ]
    );
            
    $repeater->add_control(
                'field_label', [
            'label' => esc_html__('Form Fields Label', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
                ]
    );
             
    $repeater->add_control(
                'icon',
                [
                        'label' => __( 'Icon', 'text-domain' ),
                        'type' => \Elementor\Controls_Manager::ICONS,
                        'default' => [
                                'value' => 'fas fa-star',
                                'library' => 'solid',
                        ],
                ]
    );
             
    $this->add_control(
                'form_fields', [
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                [
                    '_id' => 'name',
                    'field_type' => 'property_category',
                    'field_label' => esc_html__('Categories', 'residence-elementor'),
                   
                ],
             
            ],
            'title_field' => '{{{ field_label }}}',
                ]
    );
           
    $this->add_control(
        'place_per_row', [
        'label' => __('Items per row (1, 2, 3, 4 or 6)', 'residence-elementor'),
        'type' => Controls_Manager::TEXT,
        'default' => 4,
        ]
    );

    $this->add_control(
        'show_zero_terms', [
        'label' => __('Show Terms with no listings', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Yes', 'residence-elementor'),
        'label_off' => __('no', 'residence-elementor'),
        'return_value' => true,
        'default' => true,
        ]
    );   

$this->end_controls_section();

    $this->start_controls_section(
        'tab_items_section', [
        'label' => esc_html__('Tab Items Settings', 'residence-elementor'),
        'tab' => Controls_Manager::TAB_STYLE,
        ]
    );
         
    $this->add_responsive_control(
        'align', [
        'label' => __('Alignment', 'residence-elementor'),
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
        'selectors' => [
            '{{WRAPPER}} .wpestate_categories_as_tabs_ul' => '    justify-content: {{VALUE}};',
        ],
        ]
    );
           
    $this->add_responsive_control(
        'form_wrapper-content_padding', [
        'label' => esc_html__('Tab item Content Padding ', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', '%'],
        
        'selectors' => [
            '{{WRAPPER}} .nav-tabs>li.wpestate_categories_as_tabs_item>a ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        ]
    );
         
    $this->add_responsive_control(
        'tab_item_margin', [
        'label' => esc_html__('Tab item Margin ', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', '%'],
        
        'selectors' => [
            '{{WRAPPER}} .nav-tabs>li.wpestate_categories_as_tabs_item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        ]
    );

    $this->add_responsive_control(
        'tab_item_border_radius', [
        'label' => esc_html__('Tab Item Border Radius', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%'],
        'selectors' => [
            '{{WRAPPER}} .nav-tabs>li.wpestate_categories_as_tabs_item>a ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

        ],
        ]
    );

         
    $this->add_control(
        'tab_item_back_color', [
        'label' => esc_html__('Tab Item Background Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .nav-tabs>li.wpestate_categories_as_tabs_item>a' => 'background-color: {{VALUE}};',
        ],
        
        ]
    );

    $this->add_control(
        'tab_item_font_color', [
        'label' => esc_html__('Tab Item Font Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .nav-tabs>li.wpestate_categories_as_tabs_item>a' => 'color: {{VALUE}};',
        ],
        
        ]
    );

    $this->add_control(
        'tab_item_back_selected_color', [
        'label' => esc_html__('Tab Item Active Background Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .wpestate_categories_as_tabs_item.nav-item> a.active' => 'background-color: {{VALUE}};',
        ],
        
        ]
    );
  
    $this->add_control(
        'tab_item_active_font_color', [
        'label' => esc_html__('Tab Item Active Font Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .nav-tabs>li.wpestate_categories_as_tabs_item.active>a' => 'color: {{VALUE}};',
            '{{WRAPPER}} .nav-tabs>li.wpestate_categories_as_tabs_item>a.active' => 'color: {{VALUE}};',
        ], 
        ]
    );

    // Add border control
    $this->add_group_control(
        Group_Control_Border::get_type(),
        [
            'name'      => 'tab_item_border',
            'label'     => esc_html__('Tab Item Border', 'residence-elementor'),
            'selector'  => '{{WRAPPER}} .nav-tabs>li.wpestate_categories_as_tabs_item>a',
        ]
    );

    // Add hover border color control
    $this->add_control(
        'tab_item_border_hover_color',
        [
            'label'     => esc_html__('Tab Item Border Hover Color', 'residence-elementor'),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .nav-tabs>li.wpestate_categories_as_tabs_item>a:hover' => 'border-color: {{VALUE}};',
            ],
        ]
    );

        
    $this->add_group_control(
        Group_Control_Typography::get_type(), [
        'name' => 'tab_item_typo',
        'label' => esc_html__('Tab Item Typography', 'residence-elementor'),
       'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
        'selector' => '{{WRAPPER}}  .nav-tabs>li.wpestate_categories_as_tabs_item>a' ,
        'fields_options' => [
            // Inner control name
            'font_weight' => [
                // Inner control settings
                'default' => '500',
            ],
            'font_family' => [
                'default' => 'Roboto',
            ],
            'font_size' => ['default' => ['unit' => 'px', 'size' => 16]],
        ],
        ]
    );
            
    $this->add_responsive_control(
        'tab_item_icon_margin', [
        'label' => esc_html__('Tab item Icon Margin ', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', '%'],
    
        'selectors' => [
            '{{WRAPPER}}    .wpestate_categories_as_tabs_item i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            '{{WRAPPER}}    .wpestate_categories_as_tabs_item svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        ]
    );
                         
    $this->add_control(
            'tab_item_icon_font_color', [
        'label' => esc_html__('Tab Item Icon Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .wpestate_categories_as_tabs_item i '   => 'color: {{VALUE}};',
            '{{WRAPPER}} .wpestate_categories_as_tabs_item svg ' => 'fill: {{VALUE}};',
        ],
        
        ]
    );
                       
    $this->add_control(
        'tab_item_icon_active_font_color', [
        'label' => esc_html__('Tab Item Active Icon Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .nav-tabs>li.wpestate_categories_as_tabs_item>a.active i' => 'color: {{VALUE}};',
            '{{WRAPPER}} .nav-tabs>li.wpestate_categories_as_tabs_item>a.active isvg' => 'fill: {{VALUE}}!important; color: {{VALUE}}!important;',
            '{{WRAPPER}} .wpestate_categories_as_tabs_wrapper .wpestate_categories_as_tabs_ul .wpestate_categories_as_tabs_item > a:hover i' => 'color: {{VALUE}};',
            '{{WRAPPER}} .wpestate_categories_as_tabs_wrapper .wpestate_categories_as_tabs_ul .wpestate_categories_as_tabs_item > a:hover isvg' => 'fill: {{VALUE}}!important; color: {{VALUE}}!important;',
        ],
        ]
    );
        
    $this->add_responsive_control(
        'item_icon_size',
        [
            'label' => esc_html__( 'Icon Size', 'residence-elementor' ),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'devices' => [ 'desktop', 'tablet', 'mobile' ],
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
            
                '{{WRAPPER}} .wpestate_categories_as_tabs_item i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .wpestate_categories_as_tabs_item svg' => 'height: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};',
            ],
        ]
    );      
        
    $this->add_control(
        'icon_position', [
        'label' => __('Put Icon above label', 'residence-elementor'),
        'label_block'=>false,
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Yes', 'residence-elementor'),
        'label_off' => __('no', 'residence-elementor'),
        'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}  .wpestate_categories_as_tabs_item a' => 'flex-direction: column;',
                ],
        ]
    );
         
$this->end_controls_section();

$this->start_controls_section(
'tab_content_items_section', [
'label' => esc_html__('Tab Content Settings', 'residence-elementor'),
'tab' => Controls_Manager::TAB_STYLE,
]
);
       
    $this->add_responsive_control(
        'tab-content_padding', [
        'label' => esc_html__('Tab Content Padding ', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', '%'],
        'selectors' => [
            '{{WRAPPER}} .wpestate_categories_as_tabs_panel ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        ]
    );

    $this->add_responsive_control(
        'wpersidence_tab_content_margin', [
        'label' => esc_html__('Tab Content Margin', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', '%'],
        'selectors' => [
            '{{WRAPPER}} .wpestate_categories_as_tabs_panel' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        ]
    );
    
    $this->add_responsive_control(
        'wpersidence_tab_content_element_margin', [
        'label' => esc_html__('List Element Margin', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', '%'],
        'selectors' => [
            '{{WRAPPER}} .wpestate_categories_as_tabs_panel>div' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        ]
    );
                
    $this->add_responsive_control(
        'tab_content_border_radius', [
        'label' => esc_html__('Tab Content Border Radius', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%'],
        'selectors' => [
            '{{WRAPPER}} .wpestate_categories_as_tabs_panel' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        ]
    );
          
    $this->add_control(
        'tab_content_back_color', [
        'label' => esc_html__('Tab Item Background Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .wpestate_categories_as_tabs_panel' => 'background-color: {{VALUE}};',
        ],
    ]
    );

    // Add border control for Tab Content
    $this->add_group_control(
        Group_Control_Border::get_type(),
        [
            'name' => 'tab_content_border',
            'label' => esc_html__('Tab Content Border', 'residence-elementor'),
            'selector' => '{{WRAPPER}} .wpestate_categories_as_tabs_panel',
        ]
    );

    $this->add_control(
        'tab_content_font_color', [
        'label' => esc_html__('Term Font Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .wpestate_categories_as_tabs_panel a' => 'color: {{VALUE}};',
            '{{WRAPPER}} .wpestate_categories_as_tabs_panel' => 'color: {{VALUE}};',
        ],
        ]
    );
        
    $this->add_control(
        'tab_content_sec_row_font_color', [
        'label' => esc_html__('Term Second row Font Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .places_list_tab_term-count a' => 'color: {{VALUE}};',
            '{{WRAPPER}} .places_list_tab_term-count' => 'color: {{VALUE}};',
        ],
    ]
    );

    $this->add_group_control(
        Group_Control_Typography::get_type(), [
        'name' => 'tax_title',
        'label' => esc_html__('Term Typography', 'residence-elementor'),
       'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
        'selector' => '{{WRAPPER}} .wpestate_categories_as_tabs_term',
        'fields_options' => [
            // Inner control name
            'font_weight' => [
            // Inner control settings
                'default' => '500',
            ],
            'font_family' => [
                'default' => 'Roboto',
            ],
            'font_size' => ['default' => ['unit' => 'px', 'size' => 16]],
            ],
        ]
    );
  
    $this->add_group_control(
        Group_Control_Typography::get_type(), [
        'name' => 'tax_title_Sec_row',
        'label' => esc_html__('Second Row Typography', 'residence-elementor'),
        'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
        'selector' => '{{WRAPPER}} .places_list_tab_term-count',
        'fields_options' => [
        // Inner control name
        'font_weight' => [
        // Inner control settings
            'default' => '400',
        ],
        'font_family' => [
            'default' => 'Roboto',
        ],
        'font_size' => ['default' => ['unit' => 'px', 'size' => 15]],
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
        'selector' => '{{WRAPPER}} .wpestate_categories_as_tabs_wrapper',
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
        $numItems=0;
        if ($input !== ''&& is_countable($input) ) {
            
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


        $attributes['form_fields'] = $settings['form_fields'];
        $attributes['place_per_row'] = $settings['place_per_row'];
        $attributes['show_zero_terms'] = $settings['show_zero_terms'];

        echo wpestate_places_list_functionas_tabs($attributes);
    }

}
