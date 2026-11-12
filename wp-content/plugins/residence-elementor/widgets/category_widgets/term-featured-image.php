<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Wpresidence_Term_Featured_Image extends Widget_Base {

    public function get_name() {
        return 'term_featured_image';
    }

    public function get_title() {
        return __('Term Featured Image', 'residence-elementor');
    }

    public function get_icon() {
        return 'wpresidence-note  eicon-image-bold';
    }

    public function get_categories() {
        return ['category_widgets'];
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'residence-elementor' ),
            ]
        );

        $this->add_control(
            'use_as_background',
            [
                'label' => __( 'Use as Background', 'residence-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', 'residence-elementor' ),
                'label_off' => __( 'No', 'residence-elementor' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Image Style', 'residence-elementor' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'image_height',
            [
                'label' => __( 'Height', 'residence-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 50, 'max' => 1000 ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-term-featured-image' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .wpresidence-term-featured-image img' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .wpresidence-term-featured-image, {{WRAPPER}} .wpresidence-term-featured-image img',
            ]
        );

        $this->add_responsive_control(
            'image_border_radius',
            [
                'label' => __( 'Border Radius', 'residence-elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-term-featured-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .wpresidence-term-featured-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'bg_position',
            [
                'label' => __( 'Background Position', 'residence-elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'center center',
                'options' => [
                    'left top' => __( 'Left Top', 'residence-elementor' ),
                    'left center' => __( 'Left Center', 'residence-elementor' ),
                    'left bottom' => __( 'Left Bottom', 'residence-elementor' ),
                    'center top' => __( 'Center Top', 'residence-elementor' ),
                    'center center' => __( 'Center Center', 'residence-elementor' ),
                    'center bottom' => __( 'Center Bottom', 'residence-elementor' ),
                    'right top' => __( 'Right Top', 'residence-elementor' ),
                    'right center' => __( 'Right Center', 'residence-elementor' ),
                    'right bottom' => __( 'Right Bottom', 'residence-elementor' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-term-featured-image' => 'background-position: {{VALUE}};',
                ],
                'condition' => [
                    'use_as_background' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'bg_repeat',
            [
                'label' => __( 'Background Repeat', 'residence-elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'no-repeat',
                'options' => [
                    'no-repeat' => __( 'No Repeat', 'residence-elementor' ),
                    'repeat' => __( 'Repeat', 'residence-elementor' ),
                    'repeat-x' => __( 'Repeat X', 'residence-elementor' ),
                    'repeat-y' => __( 'Repeat Y', 'residence-elementor' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-term-featured-image' => 'background-repeat: {{VALUE}};',
                ],
                'condition' => [
                    'use_as_background' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'bg_size',
            [
                'label' => __( 'Background Size', 'residence-elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'auto' => __( 'Auto', 'residence-elementor' ),
                    'cover' => __( 'Cover', 'residence-elementor' ),
                    'contain' => __( 'Contain', 'residence-elementor' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-term-featured-image' => 'background-size: {{VALUE}};',
                ],
                'condition' => [
                    'use_as_background' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_opacity',
            [
                'label' => __( 'Opacity', 'residence-elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.01 ],
                ],
                'default' => [
                    'size' => 1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-term-featured-image' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings   = $this->get_settings_for_display();
        $use_bg     = isset( $settings['use_as_background'] ) && 'yes' === $settings['use_as_background'];
        $image_html = '';
        $image_src  = '';
        $term       = get_queried_object();

        if ( (! $term instanceof \WP_Term && \Elementor\Plugin::$instance->editor->is_edit_mode()) || is_singular( 'wpestate-studio' ) ) {
            $latest_terms = get_terms([
                'taxonomy'   => 'property_city',
                'hide_empty' => false,
                'number'     => 1,
                'orderby'    => 'term_id',
                'order'      => 'DESC',
            ]);

            if ( ! empty( $latest_terms ) && ! is_wp_error( $latest_terms ) ) {
                $term = $latest_terms[0];
            }
        }

        if ( $term instanceof \WP_Term ) {
            // WpResidence stores taxonomy meta in an option named "taxonomy_{$term_id}"
            $term_meta = get_option( 'taxonomy_' . $term->term_id );

            $attach_id = isset( $term_meta['category_attach_id'] ) ? $term_meta['category_attach_id'] : '';
            $image_url = isset( $term_meta['category_featured_image'] ) ? $term_meta['category_featured_image'] : '';

            if ( $attach_id ) {
                $image_html = wp_get_attachment_image( $attach_id, 'full' );
                $image_src  = wp_get_attachment_url( $attach_id );
            } elseif ( $image_url ) {
                $image_html = '<img src="' . esc_url( $image_url ) . '" alt="" />';
                $image_src  = $image_url;
            } else {
                $image_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
                if ( ! $image_id ) {
                    $image_id = get_term_meta( $term->term_id, 'category_image', true );
                }
                if ( $image_id ) {
                    $image_html = wp_get_attachment_image( $image_id, 'full' );
                    $image_src  = wp_get_attachment_url( $image_id );
                }
            }
        }

        if ( $image_html ) {
            if ( $use_bg && $image_src ) {
                echo '<div class="wpresidence-term-featured-image" style="background-image: url(' . esc_url( $image_src ) . ');"></div>';
            } else {
                echo '<div class="wpresidence-term-featured-image">' . $image_html . '</div>';
            }
        } else {
            echo '<div class="wpresidence-term-featured-image">' . esc_html__( 'This term does not have a featured image.', 'residence-elementor' ) . '</div>';
        }
    }
}