<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class Wpresidence_Recent_Items_SLider extends Widget_Base
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
        return 'WpResidenc_Items_Slider';
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
        return __('Items Slider', 'residence-elementor');
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
        return 'wpresidence-note eicon-post-slider';
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

        $property_category_values           =   $this->elementor_transform($property_category_values);
        $property_city_values               =   $this->elementor_transform($property_city_values);
        $property_area_values               =   $this->elementor_transform($property_area_values);
        $property_county_state_values       =   $this->elementor_transform($property_county_state_values);
        $property_action_category_values    =   $this->elementor_transform($property_action_category_values);
        $property_status_values             =   $this->elementor_transform($property_status_values);



        $featured_listings  =   array('no'=>'no','yes'=>'yes');
        $items_type         =   array('properties'=>'properties','agents'=>'agents','articles'=>'articles');
        $alignment_type     =   array('vertical'=>'vertical','horizontal'=>'horizontal');
        $arrow_type         =   array('top'=>'top','sideways'=>'sideways');

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
            'title_residence',
            [
                'label' => __('Title', 'residence-elementor'),
                              'type' => Controls_Manager::TEXT,
                                'Label Block'

            ]
        );

		$this->add_control(
			'type',
			[
				'label' => __('What type of items', 'residence-elementor'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'properties',
				'options' => $items_type
			]
	    );

	    $this->add_control(
			'arrows',
			[
				'label' => __('Slider Navigation Arrows Position', 'residence-elementor'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'top',
				'options' => $arrow_type
			]
	    );

		$this->add_control(
            'number',
            [
                'label' => __('No of items', 'residence-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => 5,
            ]
        );

        $this->add_control(
            'items_per_row_visible',
            [
                'label' => __('No of items visible', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    2=>2, 
                    3=>3,
                    4=>4,
                    5=>5                         
                ],
                    'default' => 3,
            ]
        );

		$this->add_control(
			'autoscroll',
			[
				'label' => __('Auto scroll period', 'residence-elementor'),
				'type' => Controls_Manager::TEXT,
				'Label Block',
				'default' => '',
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
                'condition' => [
                    'type' => 'properties'
                 ],                   
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 0,
				'options' => $sort_options
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
                'options' => $property_category_values,
				'default' => '',
            ]
        );

        $this->add_control(
            'action_ids',
            [
                'label' => __('List of action categories (*only for properties)', 'residence-elementor'),
                'label_block'=>true,
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $property_action_category_values,
				'default' => '',
            ]
        );

        $this->add_control(
            'city_ids',
            [
                'label' => __('List of city  (*only for properties)', 'residence-elementor'),
                'label_block'=>true,
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $property_city_values,
				'default' => '',
            ]
        );

        $this->add_control(
            'area_ids',
            [
                'label' => __('List of areas (*only for properties)', 'residence-elementor'),
                'label_block'=>true,
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $property_area_values,
				'default' => '',
            ]
        );

        $this->add_control(
            'state_ids',
            [
                'label' => __('List of Counties/States (*only for properties)', 'residence-elementor'),
                'label_block'=>true,
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $property_county_state_values,
                'default' => '',
            ]
        );

        $this->add_control(
            'status_ids',
            [
                'label' => __('List of Property Status (*only for properties)', 'residence-elementor'),
                'label_block'=>true,
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $property_status_values,
                'default' => '',
            ]
        );

		/*	$this->add_control(
						'price_min',
						[
														'label' => __('Minimum Price', 'residence-elementor'),
														'type' => Controls_Manager::TEXT,
														'default' => 0,
						]
				);
				$this->add_control(
						'price_max',
						[
														'label' => __('Maximum Price- use 0 to not filter by price', 'residence-elementor'),
														'type' => Controls_Manager::TEXT,
														'default' => 9999999999,
						]
				);
*/


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
            'selector' => '{{WRAPPER}} .property_listing,{{WRAPPER}} .slider_container .property_listing_blog,{{WRAPPER}} .listing_wrapper .agent_unit,{{WRAPPER}} .slider_container .listing_wrapper .property_listing',
                ]
        );
        
        $this->end_controls_section();
        
                /*
         * -------------------------------------------------------------------------------------------------
         * Start arrow section
         */
        
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
                           '{{WRAPPER}} button.slick-prev.slick-arrow' => 'color: {{VALUE}}',
                           '{{WRAPPER}} button.slick-next.slick-arrow' => 'color: {{VALUE}}',
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
                           '{{WRAPPER}} button.slick-prev.slick-arrow' => 'background-color: {{VALUE}}',
                           '{{WRAPPER}} button.slick-next.slick-arrow' => 'background-color: {{VALUE}}',
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
                            '{{WRAPPER}} button.slick-prev.slick-arrow:hover' => 'color: {{VALUE}}',
                            '{{WRAPPER}} button.slick-next.slick-arrow:hover' => 'color: {{VALUE}}',
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
                            '{{WRAPPER}} button.slick-prev.slick-arrow:hover' => 'background-color: {{VALUE}}',
                            '{{WRAPPER}} button.slick-next.slick-arrow:hover' => 'background-color: {{VALUE}}',
                       ],
                   ]
               );

             
               $this->add_control(
                   'bullet_color',
                   [
                       'label'     => esc_html__( 'Dot Color', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                            '{{WRAPPER}} .slider_container .slick-dots li button::before' => 'color: {{VALUE}}',
                        
                       ],
                   ]
               );

               
               $this->add_control(
                   'bullet_color_active',
                   [
                       'label'     => esc_html__( 'Dot Color Active', 'residence-elementor' ),
                       'type'      => Controls_Manager::COLOR,
                       'default'   => '',
                       'selectors' => [
                            '{{WRAPPER}} .slider_container .slick-dots li.slick-active button:before' => 'color: {{VALUE}}',

                       ],
                   ]
               );

               $this->end_controls_section();
               
              /*
              *-------------------------------------------------------------------------------------------------
              * Start typography section
              */
              $this->start_controls_section(
                         'typography_section',
                         [
                             'label'     => esc_html__( 'Typography', 'residence-elementor' ),
                             'tab'       => Controls_Manager::TAB_STYLE,
                         ]
                     );

                     $this->add_group_control(
                         Group_Control_Typography::get_type(),
                         [
                             'name'     => 'property_title',
                             'label'    => esc_html__( 'Title', 'residence-elementor' ),
                            'global'   => [
            'default' => Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                             'selector' => '{{WRAPPER}} .property_listing h4, {{WRAPPER}} .elementor-widget-container .blog4v .property_listing_blog h4, .blog4v .property_listing_blog h4,{{WRAPPER}} .blog2v h4, {{WRAPPER}} .agent_unit h4',
                         ]
                     );

                     $this->add_group_control(
                         Group_Control_Typography::get_type(),
                         [
                             'name'     => 'property_address',
                             'label'    => esc_html__( 'Address', 'residence-elementor' ),
                          'global'   => [
            'default' => Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                             'selector' => '{{WRAPPER}} .property_location_image',
                         ]
                     );

                     $this->add_group_control(
                         Group_Control_Typography::get_type(),
                         [
                             'name'     => 'property_info',
                             'label'    => esc_html__( 'Info Text', 'residence-elementor' ),
                           'global'   => [
            'default' => Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                             'selector' => '{{WRAPPER}} .property_listing_details span,{{WRAPPER}} .agent_card_2 .property_listing.places_listing .realtor_position, {{WRAPPER}} .blog_unit_meta, {{WRAPPER}} .agent_position',
                         ]
                     );

                     $this->add_group_control(
                         Group_Control_Typography::get_type(),
                         [
                             'name'     => 'propert_price',
                             'label'    => esc_html__( 'Price', 'residence-elementor' ),
                           'global'   => [
            'default' => Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                             'selector' => '{{WRAPPER}} .listing_unit_price_wrapper',
                         ]
                     );

                     $this->add_group_control(
                         Group_Control_Typography::get_type(),
                         [
                             'name'     => 'propert_price_label',
                             'label'    => esc_html__( 'Price Label', 'residence-elementor' ),
                        'global'   => [
            'default' => Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                             'selector' => '{{WRAPPER}} .price_label',
                         ]
                     );

                     $this->add_group_control(
                         Group_Control_Typography::get_type(),
                         [
                             'name'     => 'property_description',
                             'label'    => esc_html__( 'Description', 'residence-elementor' ),
                           'global'   => [
            'default' => Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                             'selector' => '{{WRAPPER}} .listing_details,{{WRAPPER}} .elementor-widget-container .blog4v .property_listing_blog .listing_details.the_grid_view, {{WRAPPER}} .blog4v .property_listing_blog .listing_details.the_grid_view, {{WRAPPER}} .agent_card_content, {{WRAPPER}} .property_listing_blog ',
                         ]
                     );



                     $this->add_group_control(
                         Group_Control_Typography::get_type(),
                         [
                             'name'     => 'details_button',
                             'label'    => esc_html__( 'Detail Link', 'residence-elementor' ),
                          'global'   => [
            'default' => Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                             'selector' => '{{WRAPPER}} .unit_details_x, {{WRAPPER}} .blog2v .read_more ' ,
                         ]
                     );

                     $this->add_group_control(
                         Group_Control_Typography::get_type(),
                         [
                             'name'     => 'property_agent',
                             'label'    => esc_html__( 'Agent', 'residence-elementor' ),
                            'global'   => [
            'default' => Global_Typography::TYPOGRAPHY_PRIMARY
        ],
                             'selector' => '{{WRAPPER}} .property_agent_wrapper a,.property_agent_wrapper',
                         ]
                     );

                    $this->end_controls_section();

              /*
              *-------------------------------------------------------------------------------------------------
              * End typography section
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
   
        $attributes['type']                 =   $settings['type'];
        $attributes['arrows']               =   $settings['arrows'];
        $attributes['category_ids']         =   $this -> wpresidence_send_to_shortcode($settings['category_ids']);
        $attributes['action_ids']           =   $this -> wpresidence_send_to_shortcode($settings['action_ids']);
        $attributes['city_ids']             =   $this -> wpresidence_send_to_shortcode($settings['city_ids']);
        $attributes['area_ids']             =   $this -> wpresidence_send_to_shortcode($settings['area_ids']);
        $attributes['state_ids']            =   $this -> wpresidence_send_to_shortcode($settings['state_ids']);
        $attributes['status_ids']           =   $this -> wpresidence_send_to_shortcode($settings['status_ids']);

        $attributes['number']               =   $settings['number'];
        $attributes['items_per_row_visible']=   $settings['items_per_row_visible'];
        $attributes['autoscroll']           =   $settings['autoscroll'];
        $attributes['show_featured_only']   =   $settings['show_featured_only'];
      
        $attributes['sort_by']       				=   $settings['sort_by'];
		//		$attributes['price_min']       			=   $settings['price_min'];
		//		$attributes['price_max']       			=   $settings['price_max'];


        echo  wpestate_slider_recent_posts_pictures($attributes);
            
     

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
