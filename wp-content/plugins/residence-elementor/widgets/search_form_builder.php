<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Properties Widget.
 * @since 2.0
 */

class Wpresidence_Search_Form_Builder extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 1.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'WpResidence_Search_Form_Builder';
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
        return esc_html__('Search Form Builder', 'residence-elementor');
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
        return ' wpresidence-note eicon-site-search';
    }

    public function get_categories() {
        return ['wpresidence'];
    }




    public  function wpestate_show_advanced_search_options_for_elementor() {


        $base_array = array('types' => esc_html__('Types', 'wpresidence'),
            'categories' => esc_html__('Categories', 'wpresidence'),
            'county / state' => esc_html__('County', 'wpresidence'),
            'cities' => esc_html__('Cities', 'wpresidence'),
            'areas' => esc_html__('Areas', 'wpresidence'),
            'wpestate location' => esc_html__('WpEstate Location', 'wpresidence'),
            'property price' => esc_html__('Price', 'wpresidence'),
            'property-price-v2' => esc_html__('Price v2', 'wpresidence'),
            'property-price-v3' => esc_html__('Price v3', 'wpresidence'),
            'property size' => esc_html__('Size', 'wpresidence'),
            'property lot size' => esc_html__('Lot Size', 'wpresidence'),
            'property rooms' => esc_html__('Rooms', 'wpresidence'),
            'property bedrooms' => esc_html__('Bedrooms', 'wpresidence'),
            'property bathrooms' => esc_html__('Bathrooms', 'wpresidence'),
            'beds-baths' => esc_html__('Beds&Baths', 'wpresidence'),
            'property address' => esc_html__('Address', 'wpresidence'),
            'property zip' => esc_html__('Zip', 'wpresidence'),
            'property country' => esc_html__('Country', 'wpresidence'),
            'property status' => esc_html__('Status', 'wpresidence'),
            'property id' => esc_html__('Id', 'wpresidence'),
            'property internal id' => esc_html__('Listing id', 'wpresidence'),
            'keyword' => esc_html__('Keyword', 'wpresidence'),
            'geolocation'=>esc_html__('Geolocation', 'wpresidence'),
            'geolocation_radius'=>esc_html__('Radius for geolocation', 'wpresidence'),
        );

       //return $base_array;
        $custom_fields = wpresidence_get_option('wp_estate_custom_fields', '');
        
        $i=0;
        if (!empty($custom_fields)) {
            while ($i < count($custom_fields)) {
                $name = $custom_fields[$i][0];
                $type = $custom_fields[$i][1];
                $slug = str_replace(' ', '-', $name);


                $base_array[$slug] = $name;
                  $i++;
            }
        }

        return $base_array;
    }


 
        
    public function wpestate_elementor_prepare_to_save_tax($data,$tax_data){
        $return_array = array();
        if(is_array($data)){
            foreach($data as $key=>$value):
                if(isset($tax_data[$value])):
                    $return_array[$value]=$tax_data[$value];
                endif;
            endforeach;
        }
        return $return_array;
        
    }


    protected function register_controls() {

        $property_action_category = wpestate_return_taxonomy_terms_elementor('property_action_category');
        $property_category = wpestate_return_taxonomy_terms_elementor('property_category');
        $property_city = wpestate_return_taxonomy_terms_elementor('property_city');
        $property_area = wpestate_return_taxonomy_terms_elementor('property_area');
        $property_county = wpestate_return_taxonomy_terms_elementor('property_county_state');
        $property_status = wpestate_return_taxonomy_terms_elementor('property_status');

        $this->start_controls_section(
                'content_section', [
            'label' => esc_html__('Tabs', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        $this->add_control(
                'form_field_use_tabs', [
            'label' => esc_html__('Use Multiple Tabs?', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'residence-elementor'),
            'label_off' => esc_html__('No', 'residence-elementor'),
            'return_value' => 'true',
            'default' => 'false',
            'separator' => 'before',
                ]
        );



        $this->add_control(
                'tabs_field', [
            'label' => esc_html__('Tab Type', 'residence-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => array(
                'property_action_category' => esc_html__('Type', 'residence-elementor'),
                'property_category' => esc_html__('Category', 'residence-elementor'),
                'property_city' => esc_html__('City', 'residence-elementor'),
                'property_area' => esc_html__('Area', 'residence-elementor'),
                'property_county_state' => esc_html__('County/State', 'residence-elementor'),
                'property_status' => esc_html__('Status', 'residence-elementor'),
            ),
            'description' => '',
            'default' => 'property_action_category',
            'condition' => [
                'form_field_use_tabs' => 'true',
            ],
                ]
        );


        $this->add_control(
                'action_data', [
            'label' => esc_html__('Select Items - Property Types (Elementor needs you to Update and Refresh the page aftter selecting the categories for tabs, before you start adding fields for each tab)', 'residence-elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => $property_action_category,
            'description' => '',
            'multiple' => true,
            'label_block' => true,
            'default' => '',
            'condition' => [
                'tabs_field' => 'property_action_category',
                'form_field_use_tabs' => 'true',
            ],
                ]
        );

        $this->add_control(
                'category_data', [
            'label' => esc_html__('Select Items - Property Category (Elementor needs you to Update and Refresh the page aftter selecting the categories for tabs, before you start adding fields for each tab)', 'residence-elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => $property_category,
            'description' => '',
            'multiple' => true,
            'label_block' => true,
            'default' => '',
            'condition' => [
                'tabs_field' => 'property_category',
                'form_field_use_tabs' => 'true',
            ],
                ]
        );



        $this->add_control(
                'city_data', [
            'label' => esc_html__('Select Items - Property City (Elementor needs you to Update and Refresh the page aftter selecting the categories for tabs, before you start adding fields for each tab)', 'residence-elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => $property_city,
            'description' => '',
            'multiple' => true,
            'label_block' => true,
            'default' => '',
            'condition' => [
                'tabs_field' => 'property_city',
                'form_field_use_tabs' => 'true',
            ],
                ]
        );



        $this->add_control(
                'area_data', [
            'label' => esc_html__('Select Items - Property Area (Elementor needs you to Update and Refresh the page aftter selecting the categories for tabs, before you start adding fields for each tab)', 'residence-elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => $property_area,
            'description' => '',
            'multiple' => true,
            'label_block' => true,
            'default' => '',
            'condition' => [
                'tabs_field' => 'property_area',
                'form_field_use_tabs' => 'true',
            ],
                ]
        );



        $this->add_control(
                'county_data', [
            'label' => esc_html__('Select Items - Property County (Elementor needs you to Update and Refresh the page aftter selecting the categories for tabs, before you start adding fields for each tab)', 'residence-elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => $property_county,
            'description' => '',
            'multiple' => true,
            'label_block' => true,
            'default' => '',
            'condition' => [
                'tabs_field' => 'property_county_state',
                'form_field_use_tabs' => 'true',
            ],
                ]
        );

        $this->add_control(
                'status_data', [
            'label' => esc_html__('Select Items - Property Status (Elementor needs you to Update and Refresh the page aftter selecting the categories for tabs, before you start adding fields for each tab)', 'residence-elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => $property_status,
            'description' => '',
            'multiple' => true,
            'label_block' => true,
            'default' => '',
            'condition' => [
                'tabs_field' => 'property_status',
                'form_field_use_tabs' => 'true',
            ],
                ]
        );

    $this->add_control(
                'tabs_order_by', [
            'label' => esc_html__('Order by', 'residence-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => array(
                '1' => esc_html__('Order by Term Id Asc', 'residence-elementor'),
                '2' => esc_html__('Order by Term Id Desc', 'residence-elementor'),
                '3' => esc_html__('Order by Term Name Asc', 'residence-elementor'),
                '4' => esc_html__('Order by Term Name Desc', 'residence-elementor'),
              
            ),
            'description' => '',
            'default' => '1',
            'condition' => [
                'form_field_use_tabs' => 'true',
            ],
                ]
        );



        $this->end_controls_section();








        $repeater = new Repeater();
   
    
        $form_fields =$this->wpestate_show_advanced_search_options_for_elementor();
     


        /**
         * Forms field types.
         */
        $repeater->add_control(
                'field_type', [
            'label' => esc_html__('Form Fields', 'residence-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => $form_fields,
            'default' => 'text',
                ]
        );

        $repeater->add_control(
                'field_how', [
            'label' => esc_html__('Select field compare', 'residence-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => array(
                'equal' => 'equal',
                'greater' => 'greater',
                'smaller' => 'smaller',
                'like' => 'like',
                'date bigger' => 'date bigger',
                'date smaller' => 'date smaller',
            ),
            'default' => 'like',
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
                'placeholder', [
            'label' => esc_html__('Form Fields Placeholder', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
                ]
        );

        $repeater->add_control(
            'min_price', [
                'label' => esc_html__('Slider min price', 'residence-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                'field_type' => [ 'property price', 'property-price-v2' ]
                ],
            ]
        );

        $repeater->add_control(
            'max_price', [
                'label' => esc_html__('Slider max price', 'residence-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'field_type' => [ 'property price', 'property-price-v2' ]
                ],
            ]
        );
        
        $repeater->add_control(
            'min_price_values', [
                'label' => esc_html__('Dropdown min price values', 'residence-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                'field_type' => [  'property-price-v3' ]
                ],
            ]
        );

        $repeater->add_control(
            'max_price_values', [
                'label' => esc_html__('Dropdown max price values', 'residence-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'field_type' => [ 'property-price-v3' ]
                ],
            ]
        );

        $repeater->add_responsive_control(
                'width', [
            'label' => esc_html__('Column Width', 'residence-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '' => esc_html__('Default', 'residence-elementor'),
                '100' => '100%',
                '80' => '80%',
                '75' => '75%',
                '66' => '66%',
                '60' => '60%',
                '50' => '50%',
                '40' => '40%',
                '33' => '33%',
                '25' => '25%',
                '20' => '20%',
            ],
            'default' => '33',
                ]
        );


        $repeater->add_control(
                'tab_holder', [
            'label' => esc_html__('In what Tab', 'residence-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => $this->custom_serve(),
            'default' => '',
                ]
        );


        $this->start_controls_section(
                'wpresidence_area_form_fields', [
            'label' => esc_html__('Form Fields', 'residence-elementor'),
                ]
        );



        $this->add_control(
                'form_fields', [
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                [
                    '_id' => 'name',
                    'field_type' => 'categories',
                    'field_label' => esc_html__('Property Category', 'residence-elementor'),
                    'placeholder' => esc_html__('Property Category', 'residence-elementor'),
                    'width' => '50',
                ],
                [
                    '_id' => 'message',
                    'field_type' => 'cities',
                    'field_label' => esc_html__('Property City', 'residence-elementor'),
                    'placeholder' => esc_html__('Property City', 'residence-elementor'),
                    'width' => '50',
                ],
            ],
            'title_field' => '{{{ field_label }}}',
                ]
        );



        $this->add_control(
                'form_field_show_labels', [
            'label' => esc_html__('Show Labels', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'residence-elementor'),
            'label_off' => esc_html__('Hide', 'residence-elementor'),
            'return_value' => 'true',
            'default' => 'true',
            'separator' => 'before',
                ]
        );

        $this->add_control(
                'form_field_show_exra_details', [
            'label' => esc_html__('Show Amenities and Features fields?', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'residence-elementor'),
            'label_off' => esc_html__('Hide', 'residence-elementor'),
            'return_value' => 'true',
            'default' => 'true',
            'separator' => 'before',
                ]
        );



        $this->add_control(
                'form_field_show_section_title', [
            'label' => esc_html__('Show Section Title', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'residence-elementor'),
            'label_off' => esc_html__('Hide', 'residence-elementor'),
            'return_value' => 'true',
            'default' => 'true',
            'separator' => 'before',
                ]
        );

        $this->add_control(
                'form_field_section_title_text', [
            'label' => esc_html__('Section Title Text', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Advanced Search', 'residence-elementor'),
            'label_block' => false,
            'description' => esc_html__('Search form Title', 'residence-elementor'),
            'separator' => 'before',
            'condition' => [
                'form_field_show_section_title' => 'true'
            ],
                ]
        );

        $this->end_controls_section();


        /*
         * -------------------------------------------------------------------------------------------------
         * Button settings
         */


        $this->start_controls_section(
                'wpresidence_area_submit_button', [
            'label' => esc_html__('Submit Button', 'residence-elementor'),
                ]
        );

        $this->add_control(
                'submit_button_text', [
            'label' => esc_html__('Text', 'residence-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Search Properties', 'residence-elementor'),
            'placeholder' => esc_html__('Search Properties', 'residence-elementor'),
                ]
        );

        ;

        $this->add_responsive_control(
                'submit_button_width', [
            'label' => esc_html__('Submit Button Width', 'residence-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '' => esc_html__('Default', 'residence-elementor'),
                '100' => '100%',
                '80' => '80%',
                '75' => '75%',
                '66' => '66%',
                '60' => '60%',
                '50' => '50%',
                '40' => '40%',
                '33' => '33%',
                '25' => '25%',
                '20' => '20%',
                '10' => '10%',
                '1' => 'auto'
            ],
            'default' => '100',
                ]
        );


        $this->add_control(
                'search_icon_button', [
            'label' => __('Icon', 'text-domain'),
            'type' => \Elementor\Controls_Manager::ICONS,
           
                ]
        );


        $this->end_controls_section();


        /*
         * -------------------------------------------------------------------------------------------------
         * END Button settings
         */





        $this->start_controls_section(
                'wpresidence_area_form_style', [
            'label' => esc_html__('Form', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_responsive_control(
                'wpersidence_form_column_gap', [
            'label' => esc_html__('Form Columns Gap', 'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 10,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .elementor-field-group' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
                '{{WRAPPER}} .elementor-form-fields-wrapper' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
            ],
                ]
        );

        $this->add_responsive_control(
            'wpersidence_form_row_gap', [
            'label' => esc_html__('Rows Gap', 'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 10,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .elementor-field-group' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .elementor-form-fields-wrapper' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
            ],
                ]
        );

        $this->add_control(
                'wpresidence_form_heading_label', [
            'label' => esc_html__('Form Label', 'residence-elementor'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
                ]
        );

        $this->add_responsive_control(
                'wpresidence_form_label_spacing', [
            'label' => esc_html__('Form Label Margin Bottom', 'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 0,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .search_wr_elementor .elementor-field-label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .adv_search_slider  .wpresidence_slider_price' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .adv_search_slider  label' => 'margin-bottom: {{SIZE}}{{UNIT}}; padding-top: 0; line-height: 1em;',
                '{{WRAPPER}} .adv_search_geo_radius_wrapper  .radius_value' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .adv_search_geo_radius_wrapper  label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .adv_search_slider  p' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
                ]
        );

        $this->add_control(
                'wpresidence_form_label_color', [
            'label' => esc_html__('Label Text Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .search_wr_elementor .elementor-field-label' => 'color: {{VALUE}};',
                '{{WRAPPER}} .extended_search_checker label' => 'color: {{VALUE}};',
                '{{WRAPPER}} .adv_search_slider  .wpresidence_slider_price' => 'color: {{VALUE}};',
                '{{WRAPPER}} .adv_search_slider  label' => 'color: {{VALUE}};',
                '{{WRAPPER}} .adv_search_geo_radius_wrapper  .radius_value' => 'color: {{VALUE}};',
                '{{WRAPPER}} .adv_search_geo_radius_wrapper  label' => 'color: {{VALUE}};',
                '{{WRAPPER}} .residence_adv_extended_options_text' => 'color: {{VALUE}};',
            ],
           
                ]
        );



        $this->add_group_control(
                Group_Control_Typography::get_type(), [
                    'name' => 'wpresidence_form_label_typography',
                    'selector' => ' {{WRAPPER}} .elementor-field-group > label,
                                    {{WRAPPER}} .adv_search_slider  .wpresidence_slider_price,
                                    {{WRAPPER}} .adv_search_slider  label,
                                    {{WRAPPER}} .adv_search_geo_radius_wrapper  .radius_value,
                                    {{WRAPPER}} .extended_search_checker label,
                                    {{WRAPPER}} .adv_extended_options_text,
                                    {{WRAPPER}} .adv_search_geo_radius_wrapper  label',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_TEXT
                    ]
            ]
        );


        $this->add_control(
                'wpresidence_form_back_color', [
            'label' => esc_html__('Background Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#fff',
            'selectors' => [
                '{{WRAPPER}} .search_wr_elementor' => 'background-color: {{VALUE}};',
            ],
            
                ]
        );


      
        $this->add_responsive_control(
            'form_wrapper-content_padding', [
            'label' => esc_html__('Tab Content Padding', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
           
            'selectors' => [
                '{{WRAPPER}} .search_wr_elementor' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        
           $this->add_responsive_control(
                'form_border_radius', [
            'label' => esc_html__('Form Border Radius', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .search_wr_elementor' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
  
            ],
                ]
        );

        $this->add_control(
                'icon_padding', [
            'label' => __('Padding', 'elementor'),
            'type' => Controls_Manager::SLIDER,
            'selectors' => [
                '{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
            ],
            'range' => [
                'em' => [
                    'min' => 0,
                    'max' => 5,
                ],
            ],
            'condition' => [
                'view!' => 'default',
            ],
                ]
        );



        $this->end_controls_section();

        /* -------------------------------------------------------------------------------------------------
         * End Form  settings
         */

        /*

         * -------------------------------------------------------------------------------------------------
         * Start shadow section
         * {{WRAPPER}} .adv_search_tab_item 
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
            'selector' => '{{WRAPPER}} .tab-content,{{WRAPPER}} .search_wr_elementor_shadow_false ',
                ]
        );
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'box_shadow_form',
            'label' => esc_html__('Box Shadow Form (no tabs)', 'residence-elementor'),
            'selector' => '{{WRAPPER}} .search_wr_elementor ',
                ]
        );
 
        $this->end_controls_section();
        /*
         * -------------------------------------------------------------------------------------------------
         * End shadow section
         */

        $this->start_controls_section(
                'wpresidence_area_tabs_style', [
            'label' => esc_html__('Tabs', 'residence-elementor'),
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
                '{{WRAPPER}} .nav-tabs' => 'justify-content: {{VALUE}};',
            ],
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'wpresidence_tab_item_typography',
            'selector' => '{{WRAPPER}} .adv_search_tab_item , {{WRAPPER}} .adv_search_tab_item a',
            'global' => [
            'default' => Global_Typography::TYPOGRAPHY_TEXT
        ]
                ]
        );


        $this->add_control(
                'wpresidence_form_tab_item_back_color', [
            'label' => esc_html__('Tab Item Background Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .adv_search_tab_item' => 'background-color: {{VALUE}};',
            ],
            'default' => '#ebba7c',
            
                ]
        );

        $this->add_control(
                'wpresidence_form_tab_item_font_color', [
            'label' => esc_html__('Tab Item Font Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#fff',
            'selectors' => [
                '{{WRAPPER}} .adv_search_tab_item a' => 'color: {{VALUE}};',
            ],
            
                ]
        );

        $this->add_control(
                'wpresidence_form_tab_item__active back_color', [
            'label' => esc_html__('Tab Item Active Background  Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#0073e1',
            'selectors' => [
                '{{WRAPPER}} .adv_search_tab_item.active' => 'background-color: {{VALUE}};',
            ],
           
                ]
        );

        $this->add_control(
                'wpresidence_tab_item_underline_active', [
            'label' => __('Underline Active Tab Item', 'plugin-domain'),
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'your-plugin'),
            'label_off' => __('no', 'your-plugin'),
            'return_value' => 'yes',
            'default' => 'no',
                ]
        );


        $this->add_control(
                'wpresidence_form_tab_item_active_font_color', [
            'label' => esc_html__('Tab Item Active Font Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#fff',
            'selectors' => [
                '{{WRAPPER}} .adv_search_tab_item.active a' => 'color: {{VALUE}};',
            ],
          
                ]
        );


        $this->add_control(
                'wpresidence_form_tab_item_active_font_color_underline', [
            'label' => esc_html__('Tab Item Active Underline color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#fff',
            'selectors' => [
                '{{WRAPPER}} .item_underline_active_yes.active a:after' => 'background-color: {{VALUE}};',
            ],
           
                ]
        );



        $this->add_control(
                'wpresidence_form_tab_back_color', [
            'label' => esc_html__('Tab Content Background  Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#fff',
            'selectors' => [
                '{{WRAPPER}} .tab-content' => 'background-color: {{VALUE}};',
            ],
          
                ]
        );

        
        $this->add_control(
                'wpersidence_tab_item_min_width', [
            'label' => esc_html__('Minimum Width', 'residence-elementor'),
            'type' => Controls_Manager::NUMBER,
            'min' => 5,
            'max' => 300,
            'step' => 1,
            'default' => 10,

            'selectors' => [
                '{{WRAPPER}} .adv_search_tab_item' => 'min-width: {{VALUE}}px;',
                ],
            ]
        );

        $this->add_responsive_control(
                'wpersidence_tab_item_margin', [
            'label' => esc_html__('Tab Item Margin', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .adv_search_tab_item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
                'tab_item_padding', [
            'label' => esc_html__('Tab Item Padding', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .adv_search_tab_item a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );


        $this->add_control(
                'tab_item_border_color', [
            'label' => esc_html__('Tab Item Border Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#fff',
            'selectors' => [
                '{{WRAPPER}} .adv_search_tab_item' => 'border-color: {{VALUE}};',
            ],
          
                ]
        );



        $this->add_responsive_control(
                'tab_item_border_radius', [
            'label' => esc_html__('Tab Item Border Radius', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .adv_search_tab_item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .adv_search_tab_item:last-of-type' => 'border-top-right-radius:{{RIGHT}}{{UNIT}};',
                '{{WRAPPER}} .adv_search_tab_item:last-of-type' => 'border-bottom-right-radius:{{BOTTOM}}{{UNIT}};',
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
                    '{{WRAPPER}} .adv_search_tab_item' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .adv_search_tab_item:last-of-type' => 'border-right-width:{{RIGHT}}{{UNIT}};',
                ],
            ]
        );




        $this->add_responsive_control(
                'tab-content_padding', [
            'label' => esc_html__('Tab Content Padding', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->add_control(
                'tab_content_border_color', [
            'label' => esc_html__('Tab Content Border Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#fff',
            'selectors' => [
                '{{WRAPPER}} .tab-content' => 'border-color: {{VALUE}};',
            ],
           
                ]
        );




        $this->add_responsive_control(
                'tab_content_border_radius', [
            'label' => esc_html__('Tab Content Border Radius', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .tab-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
                'adv_search_tab_content_border_width', [
            'label' => esc_html__('Tab Content Border Width', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'placeholder' => '1',
            'size_units' => ['px'],
            'selectors' => [
                '{{WRAPPER}} .tab-content' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );




        $this->end_controls_section();


        /* -------------------------------------------------------------------------------------------------
         *  Form Fields settings
         */



        $this->start_controls_section(
                'wpresidence_field_style', [
            'label' => esc_html__('Field Style', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_control(
            'wpresidence_field_text_color', [
                'label' => esc_html__('Field Text Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-group .elementor-field' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .filter_menu_trigger' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bootstrap-select .dropdown-menu>li>a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bootstrap-select .dropdown-menu>li>a:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bootstrap-select .dropdown-menu>li>a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bootstrap-select .dropdown-menu>.active>a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bootstrap-select .dropdown-menu>.active>a:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bootstrap-select .dropdown-menu>.active>a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bootstrap-select >' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wpestate-multiselect-custom-style.dropdown-toggle.bs-placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wpestate-multiselect-custom-style.dropdown-toggle.bs-placeholder:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .wpestate-multiselect-custom-style' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .btn.wpestate-multiselect-custom-style' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form_control' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .search_wr_elementor .form-control::placeholder ' => 'color: {{VALUE}}!important;',
                    '{{WRAPPER}} .filter_menu' => 'color:{{VALUE}}',
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle' => 'color:{{VALUE}}',
                    '{{WRAPPER}} button.wpestate-multiselect-custom-style.dropdown-toggle.bs-placeholder.btn-light.show' => 'color:{{VALUE}}',
                    '{{WRAPPER}} button.btn.btn-default.dropdown-toggle.wpestate-multiselect-custom-style.show' => 'color:{{VALUE}}',         
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'wpresidence_field_typography',
                'selector' => '{{WRAPPER}} .form-control, {{WRAPPER}} .btn, {{WRAPPER}} input.form-control,{{WRAPPER}} .wpestate-multiselect-custom-style,{{WRAPPER}} .btn.wpestate-multiselect-custom-style',
                'global' => [
            'default' => Global_Typography::TYPOGRAPHY_TEXT
        ]
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'wpresidence_field_typography_dropdown',
                'label' => esc_html__('Dropdown Typography', 'residence-elementor'),
                'selector' => '{{WRAPPER}} .ui-menu.ui-autocomplete li.ui-menu-item .ui-menu-item-wrapper, {{WRAPPER}} .filter_menu li, {{WRAPPER}} .ui-menu .ui-menu-item, {{WRAPPER}} .bootstrap-select .dropdown-menu>li>a,{{WRAPPER}} .dropdown-menu>li>a,{{WRAPPER}} .dropdown.bootstrap-select.show-tick .dropdown-menu>li>a:focus ',      
                'global' => [
            'default' => Global_Typography::TYPOGRAPHY_TEXT
        ]
            ]
        );


        $this->add_control(
            'wpresidence_field_background_color', [
                'label' => esc_html__('Field Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .form-control,
                    {{WRAPPER}} .wpestate-multiselect-custom-style,
                    {{WRAPPER}} .btn.wpestate-multiselect-custom-style,
                    {{WRAPPER}} .dropdown.bootstrap-select.show-tick.form-control.wpestate-.bs3.open,
                    {{WRAPPER}} .dropdown.bootstrap-select.show-tick.form-control.wpestate-.bs3.open button.actions-btn.bs-select-all.btn.btn-default:hover,
                    {{WRAPPER}} .dropdown.bootstrap-select.show-tick.form-control.wpestate-.bs3.open button.actions-btn.bs-deselect-all.btn.btn-default:hover,
                    {{WRAPPER}} .dropdown.bootstrap-select.show-tick.form-control.wpestate-.bs3.open .btn-default,
                    {{WRAPPER}} .btn-group.wpestate-beds-baths-popoup-component.open,
                    {{WRAPPER}} .wpresidence_dropdown .dropdown-toggle.show,
                    {{WRAPPER}} .btn:not(:disabled):not(.disabled):active:focus,
                    {{WRAPPER}} .wpresidence_dropdown .dropdown-toggle,
                    {{WRAPPER}} button.wpestate-multiselect-custom-style.dropdown-toggle.bs-placeholder.btn-light.show,
                    {{WRAPPER}} button.btn.btn-default.dropdown-toggle.wpestate-multiselect-custom-style.show,
                    {{WRAPPER}} .wpestate-beds-baths-popoup-component.open>.dropdown-toggle.btn-default ' => 'background-color: {{VALUE}};',  
                
                
                ],
                
            'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'tab-wpresidence_field_padding-color', [
                'label' => esc_html__('Field Padding', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .form-control' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .wpestate-multiselect-custom-style' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .btn.wpestate-multiselect-custom-style' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .caret::after' => 'right:{{RIGHT}}{{UNIT}};left:auto;',
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle::after' => 'right:{{RIGHT}}{{UNIT}};left:auto;',
                    '{{WRAPPER}} .dropdown.bootstrap-select.show-tick.form-control.wpestate-.bs3' => 'padding:0px;',
                    '{{WRAPPER}} .dropdown.bootstrap-select.show-tick.form-control.wpestatemultiselect' => 'padding:0px;',
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',      
                ],
            ]
        );

        $this->add_control(
                'wpresidence_field_slider_color', [
            'label' => esc_html__('Slider Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#0073e6',
            'selectors' => [
                '{{WRAPPER}} .search_wr_elementor .ui-widget-header' => 'background-color: {{VALUE}}!important;',
                '{{WRAPPER}} .search_wr_elementor .wpresidence_slider_price' => 'color: {{VALUE}};',
                '{{WRAPPER}} .search_wr_elementor .radius_value' => 'color: {{VALUE}};',
            ],
            'separator' => 'before',
                ]
        );

        $this->add_control(
                'wpresidence_field_slider_track_color', [
            'label' => esc_html__('Slider Track Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#0073e6',
            'selectors' => [
                '{{WRAPPER}} .search_wr_elementor .ui-widget-content' => 'background-color: {{VALUE}}!important;',
            ],
            'separator' => 'before',
                ]
        );



        $this->add_control(
            'wpresidence_field_border_color', [
                'label' => esc_html__('Border Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#e7e7e7',
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-group .elementor-select-wrapper::before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-control' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .search_wr_elementor .btn-group.wpestate-beds-baths-popoup-component.open' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .search_wr_elementor .wpestate-beds-baths-popoup-component.open>.dropdown-toggle.btn-default' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .btn.wpestate-multiselect-custom-style' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .wpestate-multiselect-custom-style' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} button.wpestate-multiselect-custom-style.dropdown-toggle.bs-placeholder.btn-light.show' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} button.btn.btn-default.dropdown-toggle.wpestate-multiselect-custom-style.show' => 'border-color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'field_border_width', [
                'label' => esc_html__('Border Width', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'placeholder' => '1',
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .btn:not(:disabled):not(.disabled):active' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .btn:not(:disabled):not(.disabled).active' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} button.wpestate-multiselect-custom-style.dropdown-toggle.bs-placeholder.btn-light.show' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} button.btn.btn-default.dropdown-toggle.wpestate-multiselect-custom-style.show' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .form-control, .wpestate-beds-baths-popoup-component.open>.dropdown-toggle.btn-default, .search_wr_elementor .wpestate-multiselect-custom-style' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'field_border_radius', [
                'label' => esc_html__('Border Radius', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .search_wr_elementor .wpestate-multiselect-custom-style' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .search_wr_elementor .wpestate-beds-baths-popoup-component.open>.dropdown-toggle.btn-default' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .form-control' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .search_wr_elementor .btn-group.wpestate-beds-baths-popoup-component.open' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .wpresidence_dropdown .dropdown-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        /* -------------------------------------------------------------------------------------------------
         *  END Form Fields settings
         */



        $this->start_controls_section(
                'wpresidence_area_button_style', [
            'label' => esc_html__('Button', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_control(
                'search_button_use_hover_effect', [
            'label' => esc_html__('Use Hover Effect ?', 'residence-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'residence-elementor'),
            'label_off' => esc_html__('No', 'residence-elementor'),
            'return_value' => 'true',
            'default' => 'true',
            'separator' => 'before',
                ]
        );



        $this->start_controls_tabs('tabs_button_style');

        $this->start_controls_tab(
                'tab_button_normal', [
            'label' => esc_html__('Normal State', 'residence-elementor'),
                ]
        );

        $this->add_control(
                'submit_button_background_color', [
            'label' => esc_html__('Submit Button Background Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
        
            'default' => '#0073e6',
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button' => 'background-image: linear-gradient(to right, transparent 50%, {{VALUE}} 50%);background-color:  {{VALUE}};',
            ],
                ]
        );

        $this->add_control(
                'submit_button_text_color', [
            'label' => esc_html__('Submit Button Text Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#fff',
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button' => 'color: {{VALUE}};',
            ],
                ]
        );
        $this->add_control(
                'icon_primary_color', [
            'label' => __('icon Color', 'elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .elementor-icon, {{WRAPPER}} .elementor-icon:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                '{{WRAPPER}} .elementor-icon, {{WRAPPER}} .elementor-icon:hover svg' => 'fill: {{VALUE}};',
            ],
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
                'name' => 'submit_button_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT
                ],
                    'selector' => '{{WRAPPER}} .wpresidence_button',
                ]
        );

        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'submit_button_border',
            'selector' => '{{WRAPPER}} .wpresidence_button',
                ]
        );

        $this->add_responsive_control(
                'submit_ button_border_radius', [
            'label' => esc_html__('Submit Button Border Radius', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->add_responsive_control(
                'submit_button_text_padding', [
            'label' => esc_html__('Submit Button Text Padding', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
                'tab_button_hover', [
            'label' => esc_html__('Hover State', 'residence-elementor'),
                ]
        );

        $this->add_control(
                'submit_button_background_hover_color', [
            'label' => esc_html__('Submit Button Background Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#FFFFFF00',
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button:hover' => 'background-color: {{VALUE}};',
            ],
                ]
        );

        $this->add_control(
                'submit_button_hover_color', [
            'label' => esc_html__('Submit Button Text Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#0073e6',
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button:hover' => 'color: {{VALUE}};',
            ],
                ]
        );

        $this->add_control(
                'hover_icon_color', [
            'label' => __('Hover Color icon', 'elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button:hover .elementor-icon, {{WRAPPER}} .wpresidence_button:hover .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                '{{WRAPPER}} .wpresidence_button:hover .elementor-icon, {{WRAPPER}} .wpresidence_button:hover  .elementor-icon svg' => 'fill: {{VALUE}};',
            ],
                ]
        );

        $this->add_control(
                'submit_button_hover_border_color', [
            'label' => esc_html__('Submit Button Border Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .wpresidence_button:hover' => 'border-color: {{VALUE}};',
            ],
            'condition' => [
                'button_border_border!' => '',
            ],
                ]
        );

        $this->end_controls_tab();


        $this->end_controls_tabs();
        /* -------------------------------------------------------------------------------------------------
         *  End Button Style settings
        */

        $this->add_responsive_control(
                'size', [
            'label' => __('Icon Size', 'elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 6,
                    'max' => 300,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .wpresidence_button svg'=> 'height: {{SIZE}}{{UNIT}};',
            ],
            'separator' => 'before',
                ]
        );

        $this->add_responsive_control(
                'search_icon_padding', [
            'label' => esc_html__('Icon Size Padding', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .elementor-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );


        $this->end_controls_section();
    }

    /*

     *  return option for tabs dropdown
     * 
     * 
     * 
     *  */

    protected function custom_serve() {

         global $post;

        $return = get_post_meta($post->ID,'wpstream_elementor_search_form', true);
        return $return;
    }

    /*

     *  Render the shortcode 
     * 
     * 
     * 
     *  */

    protected function render() {
        global $post;
        $settings = $this->get_settings_for_display();



        $property_action_category = wpestate_return_taxonomy_terms_elementor('property_action_category');
        $property_category = wpestate_return_taxonomy_terms_elementor('property_category');
        $property_city = wpestate_return_taxonomy_terms_elementor('property_city');
        $property_area = wpestate_return_taxonomy_terms_elementor('property_area');
        $property_county = wpestate_return_taxonomy_terms_elementor('property_county_state');
        $property_status = wpestate_return_taxonomy_terms_elementor('property_status');

        

        if (is_array($settings['action_data'])) {
            $to_save =$this->wpestate_elementor_prepare_to_save_tax($settings['action_data'], $property_action_category);
            update_post_meta($post->ID,'wpstream_elementor_search_form', $to_save);
        } else if (is_array($settings['category_data'])) {
            $to_save = $this->wpestate_elementor_prepare_to_save_tax($settings['category_data'], $property_category);
            update_post_meta($post->ID,'wpstream_elementor_search_form', $to_save);
        } else if (is_array($settings['city_data'])) {
            $to_save =$this->wpestate_elementor_prepare_to_save_tax($settings['city_data'], $property_city);
            update_post_meta($post->ID,'wpstream_elementor_search_form', $to_save);
        } else if (is_array($settings['area_data'])) {
            $to_save = $this->wpestate_elementor_prepare_to_save_tax($settings['area_data'], $property_area);
            update_post_meta($post->ID,'wpstream_elementor_search_form', $to_save);
        } else if (is_array($settings['county_data'])) {
            $to_save = $this->wpestate_elementor_prepare_to_save_tax($settings['county_data'], $property_county);
            update_post_meta($post->ID,'wpstream_elementor_search_form', $to_save);
        } else if (is_array($settings['status_data'])) {
            $to_save = $this->wpestate_elementor_prepare_to_save_tax($settings['status_data'], $property_status);
            update_post_meta($post->ID,'wpstream_elementor_search_form', $to_save);
        } else if ($settings['form_field_use_tabs'] == 'false' || $settings['form_field_use_tabs'] == false) {
            update_post_meta($post->ID,'wpstream_elementor_search_form', '');
        }



        $allowed_html = array(
            'a' => array(
                'href' => array(),
                'title' => array(),
                'target' => array()
            ),
            'strong' => array(),
            'th' => array(),
            'td' => array(),
            'span' => array(),
        );






        /*
          /	add attributes to html classes
         */

        $this->add_render_attribute(
                [
                    'wrapper' => [
                        'class' => [
                            'elementor-form-fields-wrapper',
                            'elementor-labels-above',
                        ],
                    ],
                    'wpresidence_submit_wrapper' => [
                        'class' => [
                            'elementor-field-group',
                            'elementor-column',
                            'elementor-field-type-submit',
                        ],
                    ],
                    'button' => [
                        'class' => [
                            'agent_submit_class_elementor',
                            'wpresidence_button',
                            'wpresidence_button_elementor',
                            'elementor-button',
                        ]
                    ],
                ]
        );

        if (empty($settings['submit_button_width'])) {
            $settings['submit_button_width'] = '100';
        }
        $this->add_render_attribute('wpresidence_submit_wrapper', 'class', 'elementor-col-' . $settings['submit_button_width']);
        //$this->add_render_attribute( 'wpresidence_submit_wrapper', 'class', ' elementor-button-align-' . $settings['submit_button_align'] );

        if (!empty($settings['submit_button_width_tablet'])) {
            $this->add_render_attribute('wpresidence_submit_wrapper', 'class', 'elementor-md-' . $settings['submit_button_width_tablet']);
        }

        if (!empty($settings['submit_button_width_mobile'])) {
            $this->add_render_attribute('wpresidence_submit_wrapper', 'class', 'elementor-sm-' . $settings['submit_button_width_mobile']);
        }

        if (!empty($settings['submit_button_size'])) {
            $this->add_render_attribute('button', 'class', 'elementor-size-' . $settings['submit_button_size']);
        }

        if (!empty($settings['button_type'])) {
            $this->add_render_attribute('button', 'class', 'elementor-button-' . $settings['button_type']);
        }


        if (!empty($settings['form_id'])) {
            $this->add_render_attribute('form', 'id', $settings['form_id']);
        }


        if (!empty($settings['wpresidence_submit_button_elementor'])) {
            $this->add_render_attribute('button', 'id', $settings['wpresidence_submit_button_elementor']);
        }

        /*
          /	END add attributes to html classes
         */


        if (!empty($settings['wpresidence_form_id'])) {
            $wpresidence_form_id = $settings['wpresidence_form_id'];
        }




        $temp_what = array();
        $temp_how = array();
        $temp_label = array();

        foreach ($settings['form_fields'] as $key => $item):
            if($settings['form_field_use_tabs']=='true'){
                $temp_what[$item['tab_holder']][] = $item['field_type'];
                $temp_what['use_tabs']='yes';
                
                $temp_how[$item['tab_holder']][]  = $item['field_how'];
                $temp_how['use_tabs']='yes';
                
                $temp_label[$item['tab_holder']][]  = $item['field_label'];
                $temp_label['use_tabs']='yes';
            }else{
                $temp_what[] = $item['field_type'];
                $temp_how[] = $item['field_how'];
                $temp_label[] = $item['field_label'];
            }
           
        endforeach;

        $elementor_search_name_how = "elementor_search_how_" . $post->ID;
        $elementor_search_name_what = "elementor_search_what_" . $post->ID;
        $elementor_search_name_label = "elementor_search_label_" . $post->ID;

        
        update_option($elementor_search_name_how, $temp_how);
        update_option($elementor_search_name_what, $temp_what);
        update_option($elementor_search_name_label, $temp_label);



        if(function_exists('wpestate_render_elementor_search')){
            $render_output = wpestate_render_elementor_search($settings, $this,$post->ID);
        }
        


        echo $render_output;
        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) :   
            echo  "<script>  jQuery('.wpestate-selectpicker').selectpicker('destroy');wpestate_advnced_filters_bars();  </script>";
        endif;
            
    }

    /*
     * 
     * 	Render fields attributes
     * 
     * 
     * 
     * 
     * 
     */

    public function residence_render_attributes($key, $item, $settings) {

        $this->add_render_attribute(
                [
                    'field-group' . $key => [
                        'class' => [
                            'elementor-field-group',
                            'elementor-column',
                            'form-group',
                            'elementor-field-group-' . $item['_id'],
                        ],
                    ],
                    'input' . $key => [
                        'name' => $item['field_type'],
                        'id' => 'form-field-' . $item['_id'],
                        'class' => [
                            'elementor-field',
                            'form-control',
                            'elementor-size',
                        ],
                    ],
                    'label' . $key => [
                        'for' => 'form-field-' . $item['_id'],
                        'class' => 'elementor-field-label',
                    ],
                ]
        );

        if (empty($item['width'])) {
            $item['width'] = '100';
        }



        $this->add_render_attribute('field-group' . $key, 'class', 'elementor-col-' . $item['width']);

        if (!empty($item['width_tablet'])) {
            $this->add_render_attribute('field-group' . $key, 'class', 'elementor-md-' . $item['width_tablet']);
        }

        if (!empty($item['width_mobile'])) {
            $this->add_render_attribute('field-group' . $key, 'class', 'elementor-sm-' . $item['width_mobile']);
        }

        if (!empty($item['placeholder'])) {
            $this->add_render_attribute('input' . $key, 'placeholder', $item['placeholder']);
        }

        if (!empty($item['field_value'])) {
            $this->add_render_attribute('input' . $key, 'value', $item['field_value']);
        }

        if (!$settings['form_field_show_labels']) {
            $this->add_render_attribute('label' . $key, 'class', 'elementor-screen-only');
        }
    }

}

//end class


