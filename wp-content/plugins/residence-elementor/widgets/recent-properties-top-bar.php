<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;
use Elementor\Repeater;
use Elementor\Group_Control_Border;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class Wpresidence_Properties_Top_Bar extends Widget_Base
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
        return 'Wpresidence_Properties_Top_Bar';
    }

    public function get_categories()
    {
        return [ 'wpresidence' ];
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
        return __('Properties List with Top Bar', 'residence-elementor');
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
        return 'wpresidence-note eicon-posts-masonry';
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

  
    protected function wpestate_get_terms(){
        $parms =array(
                    'hide_empty'=>false,
                    'orderby'=>'name',
                    'order'=>'ASC',
                    'taxonomy'  =>array(        
                        'property_action_category',
                        'property_city',
                        'property_area',
                        'property_county_state',
                        'property_features',
                        'property_status',
                        'property_category'),                           
        );

        $terms = get_terms(
                 $parms
        );
        
        $return_array=array();
        foreach($terms as $term){
            $return_array[$term->term_id]=$term->name;
        }

        return $return_array;
    }
       
    protected function register_controls()
    {
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
        $property_features_values             =   $this->elementor_transform($property_features_values);

        $featured_listings  =   array('no'=>'no','yes'=>'yes');
        $items_type         =   array('properties'=>'properties','articles'=>'articles','agents'=>'agents');
        $alignment_type     =   array('vertical'=>'vertical','horizontal'=>'horizontal');

        $sort_options = array();
        if( function_exists('wpestate_listings_sort_options_array')){
          $sort_options			= wpestate_listings_sort_options_array();
        }

$this->start_controls_section(
    'section_top_var_filters',
        [
        'label' => __('Top Bar Filters', 'residence-elementor'),
        'tab'       => Controls_Manager::TAB_CONTENT,
        ]
); 
         
    $repeater = new Repeater();

    $repeater->add_control(
        'field_type', [
        'label' => esc_html__('Category Terms', 'residence-elementor'),
        'type' => Controls_Manager::SELECT,
        'options' => $this->wpestate_get_terms(),
        'default' => '',
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
        'topbar_select_model',
        [
            'label' => esc_html__('Selection Mode', 'residence-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => 'multiple',
            'options' => [
                'multiple' => esc_html__('Multiple Select', 'residence-elementor'),
                'single'   => esc_html__('Single Select', 'residence-elementor'),
            ],
            'description' => esc_html__('Choose how the Top Bar filters behave.', 'residence-elementor'),
        ]
    );
 $this->end_controls_section();
        
    $this->start_controls_section(
        'section_content',
        [
            'label' => __('Content', 'residence-elementor'),
                'tab'       => Controls_Manager::TAB_CONTENT,
        ]
    );

    $this->add_control(
        'title_residence',
        [
            'label' => __('Title', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'Label Block'
        ]
    );

    $this->add_control(
        'number',
        [
            'label' => __('No of items', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'default'=>9,
        ]
    );

    $this->add_control(
        'rownumber',
        [
            'label' => __('No of items per row', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'default'=>3,
        ]
    );


    $this->add_control(
        'align',
        [
            'label' => __('"What type of alignment?', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'vertical',
            'options' => $alignment_type
        ]
    );

    $this->add_control(
        'random_pick',
        [
            'label' => __('Random Pick?', 'residence-elementor'),
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
                '{{WRAPPER}} .items_shortcode_wrapper_grid' => '  grid-template-columns: repeat(auto-fit, minmax({{SIZE}}{{UNIT}}, auto));',
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
    * Start filters
    */
$this->start_controls_section(
    'filters_section',
    [
        'label'     => esc_html__( 'Filters', 'residence-elementor' ),
        'tab'       => Controls_Manager::TAB_CONTENT,
    ]
);

    $this->add_control(
        'category_ids',
        [
            'label' => __('List of categories (*only for properties)', 'residence-elementor'),
            'label_block'=>true,
            'type' => \Elementor\Controls_Manager::SELECT2,
            'multiple' => true,
            'default'=>'',
            'options' => $property_category_values,
        ]
    );

    $this->add_control(
            'action_ids',
            [
                            'label' => __('List of action categories (*only for properties)', 'residence-elementor'),
                                'label_block'=>true,
                            'type' => \Elementor\Controls_Manager::SELECT2,
                            'multiple' => true,
                            'default'=>'',
                            'options' => $property_action_category_values,
            ]
    );

    $this->add_control(
        'city_ids',
        [
        'label' => __('List of city  (*only for properties)', 'residence-elementor'),
        'label_block'=>true,
        'type' => \Elementor\Controls_Manager::SELECT2,
        'multiple' => true,
        'default'=>'',
        'options' => $property_city_values,
        ]
    );

    $this->add_control(
        'area_ids',
        [
        'label' => __('List of areas (*only for properties)', 'residence-elementor'),
        'label_block'=>true,
        'type' => \Elementor\Controls_Manager::SELECT2,
        'multiple' => true,
        'default'=>'',
        'options' => $property_area_values,
        ]
    );

    $this->add_control(
        'state_ids',
        [
        'label' => __('List of Counties/States (*only for properties)', 'residence-elementor'),
        'label_block'=>true,
        'type' => \Elementor\Controls_Manager::SELECT2,
        'multiple' => true,
            'default'=>'',
        'options' => $property_county_state_values,
        ]   
    );

    $this->add_control(
        'status_ids',
        [
        'label' => __('List of Property Status (*only for properties)', 'residence-elementor'),
        'label_block'=>true,
        'type' => \Elementor\Controls_Manager::SELECT2,
        'multiple' => true,
        'default'=>'',
        'options' => $property_status_values,
        ]
    );

    $this->add_control(
        'features_ids',
        [
        'label' => __('List of Property Features (*only for properties)', 'residence-elementor'),
        'label_block'=>true,
        'type' => \Elementor\Controls_Manager::SELECT2,
        'multiple' => true,
        'default'=>'',
        'options' => $property_features_values,
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

$this->end_controls_section();
        
$this->start_controls_section(
    'tab_items_section', [
    'label' => esc_html__('Filters Settings', 'residence-elementor'),
    'tab' => Controls_Manager::TAB_STYLE,
    ]
);
         
    $this->add_responsive_control(
        'align2', [
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
            '{{WRAPPER}} .control_tax_wrapper' => '    justify-content: {{VALUE}};',
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
        'selector' => '{{WRAPPER}} .control_tax_sh' ,
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

    // Add Padding Control for .control_tax_wrapper
    $this->add_responsive_control(
        'control_tax_wrapper_padding', [
        'label' => esc_html__('Tab Section Padding', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', '%'],
        'selectors' => [
            '{{WRAPPER}} .control_tax_wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
    ]);

    // Add Margin Control for .control_tax_wrapper
    $this->add_responsive_control(
        'control_tax_wrapper_margin', [
        'label' => esc_html__('Tab Section Margin', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', '%'],
        'selectors' => [
            '{{WRAPPER}} .control_tax_wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
    ]);

    // Add Background Color Control for .control_tax_wrapper
    $this->add_control(
        'control_tax_wrapper_bg_color', [
        'label' => esc_html__('Tab Section Background Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .control_tax_wrapper' => 'background-color: {{VALUE}};',
        ],
    ]);

    // Add Border Control for .control_tax_wrapper
    $this->add_group_control(
        Group_Control_Border::get_type(), [
        'name' => 'control_tax_wrapper_border',
        'label' => esc_html__('Tab Item Border', 'residence-elementor'),
        'selector' => '{{WRAPPER}} .control_tax_wrapper',
    ]);

    $this->add_responsive_control(
        'form_wrapper-content_padding', [
        'label' => esc_html__('Tab Item Padding ', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', '%'],
        
        'selectors' => [
            '{{WRAPPER}} .control_tax_sh' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
        ]
    );
         
    $this->add_responsive_control(
        'tab_item_margin', [
        'label' => esc_html__('Tab Item Margin ', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', '%'],
        
        'selectors' => [
            '{{WRAPPER}} .control_tax_sh' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
            ]
    );

    $this->add_responsive_control(
        'tab_item_border_radius', [
        'label' => esc_html__('Tab Item Border Radius', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%'],
        'selectors' => [
            '{{WRAPPER}} .control_tax_sh' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

        ],
        ]
    );

    // Add border control for Tab Item
    $this->add_group_control(
        Group_Control_Border::get_type(), [
        'name' => 'tab_item_border',
        'label' => esc_html__('Tab Item Border', 'residence-elementor'),
        'selector' => '{{WRAPPER}} .control_tax_sh',
    ]);

    // Add hover border color control
    $this->add_control(
        'tab_item_border_hover_color', [
        'label' => esc_html__('Tab Item Border Hover Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .control_tax_sh:hover' => 'border-color: {{VALUE}};',
        ],
    ]);

    $this->add_control(
        'tab_item_font_color', [
        'label' => esc_html__('Tab Item Font Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .control_tax_sh' => 'color: {{VALUE}};',
        ],
        ]
    );

    // Add hover font color control for Tab Item
    $this->add_control(
        'tab_item_font_hover_color', [
        'label' => esc_html__('Tab Item Font Hover Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .control_tax_sh:hover' => 'color: {{VALUE}};',
        ],
    ]);

    $this->add_control(
        'tab_item_active_font_color', [
        'label' => esc_html__('Active Tab Item Font Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .control_tax_sh.tax_active' => 'color: {{VALUE}};',
        ],
        ]
    );
  
    $this->add_control(
        'tab_item_back_selected_color', [
        'label' => esc_html__('Tab Item Active Background Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .control_tax_sh.tax_active' => 'background-color: {{VALUE}};',
        ],
        ]
    );

    $this->add_control(
        'tab_item_back_color', [
        'label' => esc_html__('Tab Item Background Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .control_tax_sh' => 'background-color: {{VALUE}};',
        ],
        ]
    );

    // Add hover background color control for Tab Item
    $this->add_control(
        'tab_item_back_hover_color', [
        'label' => esc_html__('Tab Item Hover Background Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .control_tax_sh:hover' => 'background-color: {{VALUE}};',
        ],
    ]);

    $this->add_control(
        'icon_position', [
        'label' => __('Show Icon Above Label', 'residence-elementor'),
        'label_block'=>false,
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => __('Yes', 'residence-elementor'),
        'label_off' => __('no', 'residence-elementor'),
        'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}  .control_tax_sh' => 'flex-direction: column;',
                ],
        ]
    );
                    
    $this->add_control(
        'tab_item_icon_font_color', [
        'label' => esc_html__('Tab Item Icon Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .control_tax_sh i '   => 'color: {{VALUE}};',
            '{{WRAPPER}} .control_tax_sh svg ' => 'fill: {{VALUE}};',
        ],
        ]
    ); 

    // Add hover icon color control for Tab Item
    $this->add_control(
        'tab_item_icon_hover_font_color', [
        'label' => esc_html__('Tab Item Icon Hover Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .control_tax_sh:hover i'   => 'color: {{VALUE}};',
            '{{WRAPPER}} .control_tax_sh:hover svg' => 'fill: {{VALUE}};',
        ],
    ]);
              
    $this->add_control(
            'tab_item_icon_active_font_color', [
        'label' => esc_html__('Tab Item Active Icon Color', 'residence-elementor'),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .control_tax_sh.tax_active i' => 'color: {{VALUE}};',
            '{{WRAPPER}} .control_tax_sh.tax_active svg' => 'color: {{VALUE}}!important;fill: {{VALUE}}!important;',
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
            
                '{{WRAPPER}} .control_tax_sh i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .control_tax_sh svg' => 'height: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};',
            ],
        ]
    );
    
    $this->add_responsive_control(
        'tab_item_icon_margin', [
        'label' => esc_html__('Tab item Icon Margin ', 'residence-elementor'),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => ['px', 'em', '%'],
    
        'selectors' => [
            '{{WRAPPER}}    .control_tax_sh i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            '{{WRAPPER}}    .control_tax_sh svg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

$this->end_controls_section();

    
// Load More Button Styling Section
    $this->start_controls_section(
        'load_more_button_style_section',
        [
            'label' => esc_html__('Load More Button', 'residence-elementor'),
            'tab' => \Elementor\Controls_Manager::TAB_STYLE,
        ]
    );

    // Button Typography
    $this->add_group_control(
        \Elementor\Group_Control_Typography::get_type(),
        [
            'name' => 'button_typography',
            'label' => esc_html__('Typography', 'residence-elementor'),
            'selector' => '{{WRAPPER}} .wpresidence_button',
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
                '{{WRAPPER}} .wpresidence_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                '{{WRAPPER}} .wpresidence_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_control(
        'load_more_bg_color',
        [
        'label'     => esc_html__( 'Background Color', 'residence-elementor' ),
        'type'      => Controls_Manager::COLOR,
        'default'   => '',
        'selectors' => [
            '{{WRAPPER}} .wpresidence_button' => 'background-color: {{VALUE}};background-image:linear-gradient(to right, transparent 50%, {{VALUE}} 50%);border-color: {{VALUE}};',
        ],
        ]
    );

    $this->add_control(
        'load_more_color',
        [
        'label'     => esc_html__( 'Text Color', 'residence-elementor' ),
        'type'      => Controls_Manager::COLOR,
        'default'   => '',
        'selectors' => [
            '{{WRAPPER}} .wpresidence_button' => 'color: {{VALUE}}',
        ],
        ]
    );

    $this->add_control(
        'load_more_bg_color_hover',
        [
            'label'     => esc_html__( 'Hover Background Color', 'residence-elementor' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button:hover' => 'background-color: {{VALUE}};border-color: {{VALUE}};',
            ],
        ]
    );

    $this->add_control(
        'load_more_color_hover',
        [
            'label'     => esc_html__( 'Hover Text Color', 'residence-elementor' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '',
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button:hover' => 'color: {{VALUE}};',
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
        if ($input!=='') {
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


      
        if(isset($settings['title_residence'])){
            $attributes['title']                =   $settings['title_residence'];
        }
   
        $attributes['type']                 =   'properties';
        $attributes['category_ids']         =   $this -> wpresidence_send_to_shortcode($settings['category_ids']);
        $attributes['action_ids']           =   $this -> wpresidence_send_to_shortcode($settings['action_ids']);
        $attributes['city_ids']             =   $this -> wpresidence_send_to_shortcode($settings['city_ids']);
        $attributes['area_ids']             =   $this -> wpresidence_send_to_shortcode($settings['area_ids']);
        $attributes['state_ids']            =   $this -> wpresidence_send_to_shortcode($settings['state_ids']);
        $attributes['status_ids']           =   $this -> wpresidence_send_to_shortcode($settings['status_ids']);
        $attributes['features_ids']         =   $this -> wpresidence_send_to_shortcode($settings['features_ids']);

        $attributes['number']               =   $settings['number'];
        $attributes['rownumber']            =   $settings['rownumber'];
        $attributes['align']                =   $settings['align'];       
        $attributes['show_featured_only']   =   $settings['show_featured_only'];
        $attributes['random_pick']          =   $settings['random_pick'];
        $attributes['form_fields']          =   $settings['form_fields'];
        $attributes['sort_by']              =   $settings['sort_by'];
        $attributes['display_grid']         =   $settings['display_grid'];
        $attributes['topbar_select_model']   =   $settings['topbar_select_model'];


        echo  wpestate_recent_posts_pictures_new($attributes);
    }

    /**
     * Render the widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 1.0.0
     *
     * @access protected
     */

}
