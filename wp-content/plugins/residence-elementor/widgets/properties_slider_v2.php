<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Properties_Slider_v2 extends Widget_Base {

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
        return 'WpResidence_Property_Slider_v2';
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
        return __('Property Slider V2', 'residence-elementor');
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
        return ' wpresidence-note eicon-slider-album';
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
        return ['owl_carousel'];
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
        $featured_prop_type = array(1 => 1, 2 => 2);

        $property_category_values = wpestate_generate_category_values_shortcode();
        $property_city_values = wpestate_generate_city_values_shortcode();
        $property_area_values = wpestate_generate_area_values_shortcode();
        $property_county_state_values = wpestate_generate_county_values_shortcode();
        $property_action_category_values = wpestate_generate_action_values_shortcode();
        $property_status_values = wpestate_generate_status_values_shortcode();

        $property_category_values = $this->elementor_transform($property_category_values);
        $property_city_values = $this->elementor_transform($property_city_values);
        $property_area_values = $this->elementor_transform($property_area_values);
        $property_county_state_values = $this->elementor_transform($property_county_state_values);
        $property_action_category_values = $this->elementor_transform($property_action_category_values);
        $property_status_values = $this->elementor_transform($property_status_values);
        
        
        
        /*
         * Start filters
         */
        $this->start_controls_section(
                'filters_section', [
            'label' => esc_html__('Filters', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );







        $this->add_control(
                'category_ids', [
            'label' => __('List of categories ', 'residence-elementor'),
            'label_block' => true,
            'type' => \Elementor\Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => $property_category_values,
            'default' => '',
                ]
        );

        $this->add_control(
                'action_ids', [
            'label' => __('List of action categories ', 'residence-elementor'),
            'label_block' => true,
            'type' => \Elementor\Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => $property_action_category_values,
            'default' => '',
                ]
        );

        $this->add_control(
                'city_ids', [
            'label' => __('List of city  ', 'residence-elementor'),
            'label_block' => true,
            'type' => \Elementor\Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => $property_city_values,
            'default' => '',
                ]
        );
        $this->add_control(
                'area_ids', [
            'label' => __('List of areas ', 'residence-elementor'),
            'label_block' => true,
            'type' => \Elementor\Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => $property_area_values,
            'default' => '',
                ]
        );
        $this->add_control(
                'state_ids', [
            'label' => __('List of Counties/States ', 'residence-elementor'),
            'label_block' => true,
            'type' => \Elementor\Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => $property_county_state_values,
            'default' => '',
                ]
        );

        $this->add_control(
                'status_ids', [
            'label' => __('List of Property Status ', 'residence-elementor'),
            'label_block' => true,
            'type' => \Elementor\Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => $property_status_values,
            'default' => '',
                ]
        );




        $this->add_control(
                'show_featured_only', [
            'label' => __('Show featured listings only?', 'residence-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'no',
            'options' => $featured_listings
                ]
        );



      



        $this->add_control(
            'propertyid', [
            'label' => __('Items IDs - will override the above filters', 'residence-elementor'),
            'label_block' => true,
            'type' => Controls_Manager::TEXT,
            'Label Block'
                ]
        );





        $this->end_controls_section();


        $this->start_controls_section(
                'size_section', [
            'label' => esc_html__('Style', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        
        $this->add_control(
        'slider_orientation',
        [
                'label' => __( 'Slider Orientation', 'plugin-domain' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'right', 'residence-elementor'),
                'label_off' => __( 'Left', 'residence-elementor' ),
                'return_value' => 'right',
                'default' => 'right',
        ]
        );


          $this->add_group_control(
                Group_Control_Typography::get_type(), [
                    'name' => 'wpresidence_field_typography_title',
                    'label' => esc_html__('Title Typography', 'residence-elementor'),
                    'selector' => '{{WRAPPER}} .property_slider_carousel_elementor_v2_title',
                    'global'   => [
                        'default' => Global_Typography::TYPOGRAPHY_TEXT
                    ],
                ]
        );

          
            $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'wpresidence_field_typography_price',
                    'label' => esc_html__('Price Typography', 'residence-elementor'),
            'selector' => '{{WRAPPER}} .property_slider_carousel_elementor_v2_price,{{WRAPPER}} .property_slider_carousel_elementor_v2_price .price_label ',
            'global'   => [
                'default' => Global_Typography::TYPOGRAPHY_TEXT
            ],
                ]
        );

            
            
        
            $this->add_control(
                   'title_font_color',
                   [
                       'label'     => esc_html__( 'Title Font Color', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                           '{{WRAPPER}} .property_slider_carousel_elementor_v2_title' => 'color: {{VALUE}}',

                       ],
                   ]
               );

            $this->add_control(
                'price_bk_color',
                [
                    'label'     => esc_html__( 'Price Background Color', 'residence-elementor' ),
                    'type'      => Controls_Manager::COLOR,
                    'default'   => '',
                    'selectors' => [
                        '{{WRAPPER}} .property_slider_carousel_elementor_v2_price' => 'background-color: {{VALUE}};background-image:linear-gradient(to right, transparent 50%, {{VALUE}} 50%);border-color: {{VALUE}};',
                    ],
                ]
                );
                
                
               $this->add_control(
                   'price_font_color',
                   [
                       'label'     => esc_html__( 'Price Font Color', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                           '{{WRAPPER}} .property_slider_carousel_elementor_v2_price,.property_slider_carousel_elementor_v2_price .price_label' => 'color: {{VALUE}}',

                       ],
                   ]
               );

               
                      
           $this->add_responsive_control(
                'image_border_radius', [
            'label' => __('Border Radius',  'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .property_slider_carousel_elementor_v2_image_wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
           
             $this->add_responsive_control(
                'adv_search_tab_item_border_width', [
            'label' => esc_html__('Tab Item Border Width', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'placeholder' => '1',
            'size_units' => ['px'],
            'selectors' => [
                '{{WRAPPER}} .property_slider_carousel_elementor_v2_image_wrapper' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
              
            ],
                ]
        );
           
               $this->add_control(
                  'unit_border_color',
                  [
                      'label'     => esc_html__( 'Border Color', 'residence-elementor' ),
                      'type'      => Controls_Manager::COLOR,
                      'default'   => '',
                      'selectors' => [
                          '{{WRAPPER}} .property_slider_carousel_elementor_v2_image_wrapper' => 'border-color: {{VALUE}}',
                      ],
                  ]
              );


               
      

        $this->end_controls_section();
        
        
        
      


        $this->start_controls_section(
                'arrow_section', [
            'label' => esc_html__('Arrows Style', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        
        
            $this->add_control(
                   'arrow_color',
                   [
                       'label'     => esc_html__( 'Arrow Color', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                           '{{WRAPPER}} .property_slider_carousel_elementor_v2.owl-theme .owl-nav .owl-prev' => 'color: {{VALUE}}',
                           '{{WRAPPER}} .property_slider_carousel_elementor_v2.owl-theme .owl-nav .owl-next' => 'color: {{VALUE}}',
                       ],
                   ]
            );
            
              $this->add_control(
                   'arrow_bck_color',
                   [
                       'label'     => esc_html__( 'Arrow Background Color', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                           '{{WRAPPER}} .property_slider_carousel_elementor_v2.owl-theme .owl-nav .owl-prev' => 'background-color: {{VALUE}}',
                           '{{WRAPPER}} .property_slider_carousel_elementor_v2.owl-theme .owl-nav .owl-next' => 'background-color: {{VALUE}}',
                       ],
                   ]
            );
           
           
            
            $this->add_control(
                   'arrow_color_hover',
                   [
                       'label'     => esc_html__( 'Arrow Color Hover', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                            '{{WRAPPER}} .property_slider_carousel_elementor_v2.owl-theme .owl-nav .owl-prev:hover' => 'color: {{VALUE}}',
                            '{{WRAPPER}} .property_slider_carousel_elementor_v2.owl-theme .owl-nav .owl-next:hover' => 'color: {{VALUE}}',
                       ],
                   ]
               );
            
             $this->add_control(
                   'arrow_bck_color_hover',
                   [
                       'label'     => esc_html__( 'Arrow Background Color Hover', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                            '{{WRAPPER}} .property_slider_carousel_elementor_v2.owl-theme .owl-nav .owl-prev:hover' => 'background-color: {{VALUE}}',
                            '{{WRAPPER}} .property_slider_carousel_elementor_v2.owl-theme .owl-nav .owl-next:hover' => 'background-color: {{VALUE}}',
                       ],
                   ]
               );


           $this->end_controls_section();
        
        
          /*
              *-------------------------------------------------------------------------------------------------
              * Start shadow section
              */
              $this->start_controls_section(
                'section_grid_box_shadow',
                [
                    'label' => esc_html__( 'Box Shadow', 'residence-elementor' ),
                    'tab'   => Controls_Manager::TAB_STYLE,
                ]
                );
                $this->add_group_control(
                    Group_Control_Box_Shadow::get_type(),
                    [
                        'name'     => 'box_shadow',
                        'label'    => esc_html__( 'Box Shadow', 'residence-elementor' ),
                        'selector' => '{{WRAPPER}} .property_slider_carousel_elementor_v2_image_wrapper',
                    ]
                );

                $this->end_controls_section();
              /*
              *-------------------------------------------------------------------------------------------------
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
    
        $settings                           =   $this->get_settings_for_display();
        $attributes['propertyid']           =   $settings['propertyid'];
        $attributes['category_ids']         =   $this->wpresidence_send_to_shortcode($settings['category_ids']);
        $attributes['action_ids']           =   $this->wpresidence_send_to_shortcode($settings['action_ids']);
        $attributes['city_ids']             =   $this->wpresidence_send_to_shortcode($settings['city_ids']);
        $attributes['area_ids']             =   $this->wpresidence_send_to_shortcode($settings['area_ids']);
        $attributes['state_ids']            =   $this->wpresidence_send_to_shortcode($settings['state_ids']);
        $attributes['status_ids']           =   $this->wpresidence_send_to_shortcode($settings['status_ids']);
        $attributes['slider_orientation']   =   $settings['slider_orientation'];
        $attributes['show_featured_only']   =   $settings['show_featured_only'];
        
        $slider_id      =   'property_slider_carousel_elementor_v2_'.rand(1,99999);
        $slider_data    =   wpestate_slider_properties_v2($attributes,$slider_id);
        
        print trim($slider_data['return']);

        print '
            <script type="text/javascript">
                //<![CDATA[
                jQuery(document).ready(function(){
                   wpestate_property_slider_v2("'.$slider_id.'",'.$slider_data['items'].');
                });
                //]]>
            </script>';
        }
  

}
