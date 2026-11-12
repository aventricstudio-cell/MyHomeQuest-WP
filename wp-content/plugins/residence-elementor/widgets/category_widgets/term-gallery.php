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

class Wpresidence_Term_Gallery extends Widget_Base {

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
        return 'term_gallery';
    }

    public function get_categories() {
        return ['category_widgets'];
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
        return __('Term Gallery', 'residence-elementor');
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
        return 'wpresidence-note  eicon-gallery-grid';
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

        // The gallery widget inherits the grid type control only. 
        // Other taxonomy and ordering controls from the regular grid widget are
        // excluded as the widget simply shows the images assigned to the current
        // term.

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
                    '{{WRAPPER}} .wpresidence_term_background_image'           => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        $term = get_queried_object();
        if ( ( ! ( $term instanceof \WP_Term ) && \Elementor\Plugin::$instance->editor->is_edit_mode()) || is_singular( 'wpestate-studio' ) ) {
            $latest_terms = get_terms([
                'taxonomy'   => 'property_city',
                'hide_empty' => false,
                'number'     => 1,
                'orderby'    => 'term_id',
                'order'      => 'DESC',
            ]);
            if ( ! empty( $latest_terms ) ) {
                $term = $latest_terms[0];
            }
        }

        $gallery = '';
        if ( $term instanceof \WP_Term ) {
            $t_id          = $term->term_id;
            $term_meta     = get_option( 'taxonomy_' . $t_id );
            $term_meta_arr = wpestate_parse_category_term_array( $term_meta );
            if ( isset( $term_meta_arr['category_gallery'] ) ) {
                $gallery = $term_meta_arr['category_gallery'];
            }

            if ( ! $gallery ) {
                $gallery = get_term_meta( $term->term_id, 'category_gallery', true );
            }
        }

$images = array_slice( array_filter( explode( ',', $gallery ) ), 0, intval( $settings['items_no'] ) );
        if ( ! empty( $images ) ) {
            $args = [
                'type'                   => $settings['wpresidence_grid_type'],
                'wpresidence_design_type' => $settings['wpresidence_design_type'] ?? 1,
                'grid_taxonomy'          => $settings['grid_taxonomy'] ?? '',
            ];

            // Set up grid display and query taxonomies
            $display_grids = wpresidence_display_grids_setup();
            $taxonomies    = wpresidence_query_taxonomies( $args );

            // Extract and sanitize arguments
            $type        = intval( $args['type'] );
            $place_type  = intval( $args['wpresidence_design_type'] );
            $use_grid    = $display_grids[ $type ];
            $category_tax = $args['grid_taxonomy'];

            // reset classes for columns
            $places_class['col_class'] = '';
            $places_class['col_org']   = '';

            // Determine grid pattern size
            $grid_pattern_size = is_array( $use_grid['position'] ) ? count( $use_grid['position'] ) : 1;

            // Initialize container
            $container = '<div class="row elementor_wpresidece_grid">';

            foreach ( $images as $key => $img_id ) {
                // Calculate position in grid pattern
                $key_position = ( $key >= $grid_pattern_size ) ? ( ( $key % $grid_pattern_size ) + 1 ) : ( intval( $key ) + 1 );
                $item_length  = $use_grid['position'][ $key_position ];

                $design_class = 'places_wrapper_type_' . $settings['wpresidence_grid_type'];
                $container   .= sprintf( '<div class="%s col-sm-12 elementor_residence_grid %s">', esc_attr( $item_length ), esc_attr( $design_class ) );

                $preview = wp_get_attachment_image_src( $img_id, 'large' );
                if ( $preview ) {
                $container   .= '<a href="' . esc_url( $preview[0] ) . '" data-elementor-open-lightbox="default" data-elementor-lightbox-slideshow="term-gallery" data-elementor-lightbox-index="' . esc_attr( $key ) . '" style="width:100%;height:100%;">';
                $container   .= '<div class="wpresidence_term_background_image prettygalery elementor-lightbox" style="background-image:url(' . esc_url( $preview[0] ) . ')">';
                $container   .= '<div class="places_cover"></div>';
                $container   .= '</div>';
                $container   .= '</a>';
                }

                $container .= '</div>';
            }

            $container .= '</div>';
            echo $container;
        } else {
            echo '<div class="wpresidence-term-gallery">' . esc_html__( 'This term does not have a gallery.', 'residence-elementor' ) . '</div>';
        }
    }
}
