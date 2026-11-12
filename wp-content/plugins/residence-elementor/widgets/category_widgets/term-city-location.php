<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Wpresidence_Term_City_Location extends Widget_Base {

    public function get_name() {
        return 'term_city_location';
    }

    public function get_title() {
        return __('Term City or County/State', 'residence-elementor');
    }

    public function get_icon() {
        return 'wpresidence-note  eicon-map-pin';
    }

    public function get_categories() {
        return ['category_widgets'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'residence-elementor'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'city_location_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .wpresidence-term-city-location',
            ]
        );

        $this->add_control(
            'city_location_color',
            [
                'label' => __('Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-term-city-location' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->end_controls_section();
    }

    protected function render() {
        $location = '';
        $term     = get_queried_object();
     
     

        if ($term instanceof \WP_Term) {
            
            $term_meta = get_option("taxonomy_$term->term_id");

            if ($term->taxonomy === 'property_city') {
                 $location = isset( $term_meta['stateparent'] ) ? $term_meta['stateparent'] : '';
               
            } elseif ($term->taxonomy === 'property_area') {
                $location = isset( $term_meta['cityparent'] ) ? $term_meta['cityparent'] : '';

                
            }
        }

        if (( ! $location && \Elementor\Plugin::$instance->editor->is_edit_mode()) || is_singular( 'wpestate-studio' ) ) {
            $latest_terms = get_terms([
                'taxonomy'   => 'property_city',
                'hide_empty' => false,
                'number'     => 1,
                'orderby'    => 'term_id',
                'order'      => 'DESC',
            ]);

            if ( ! empty( $latest_terms ) && ! is_wp_error( $latest_terms ) ) {
                $term = $latest_terms[0];
                   $term_meta = get_option( 'taxonomy_' . $term->term_id );

                if ( $term instanceof \WP_Term ) {
                    if ( $term->taxonomy === 'property_city' ) {
                         $location = isset( $term_meta['stateparent'] ) ? $term_meta['stateparent'] : '';
                    } elseif ( $term->taxonomy === 'property_area' ) {
                        $location = isset( $term_meta['cityparent'] ) ? $term_meta['cityparent'] : '';
                    }
                }
            }
        }

        if ($location) {
            echo '<div class="wpresidence-term-city-location">' . esc_html($location) . '</div>';
        } else {
            echo '<div class="wpresidence-term-city-location">' . esc_html__( 'This term does not have a city location.', 'residence-elementor' ) . '</div>';
        }
    }
}