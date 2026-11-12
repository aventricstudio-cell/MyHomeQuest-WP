<?php
namespace ElementorWpResidence\Widgets;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
class Wpresidence_Term_Tagline extends Widget_Base {
   
    public function get_name() {
        return 'term_tagline';
    }
   
    public function get_title() {
        return __('Term Tagline', 'residence-elementor');
    }
   
    public function get_icon() {
        return 'wpresidence-note  eicon-archive-title';
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
                'name' => 'tagline_typography',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'selector' => '{{WRAPPER}} .wpresidence-term-tagline',
            ]
        );

        $this->add_control(
            'tagline_color',
            [
                'label' => __('Color', 'residence-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-term-tagline' => 'color: {{VALUE}};',
                ],
            ]
        );
       
        $this->end_controls_section();
    }
   
    protected function render() {
        $tagline = '';
        $term    = get_queried_object();
       
        if ( $term instanceof \WP_Term ) {
            // Legacy taxonomy meta stored as an option 'taxonomy_{$term_id}'.
            $term_meta = get_option( 'taxonomy_' . $term->term_id );
            if ( isset( $term_meta['category_tagline'] ) ) {
                $tagline = stripslashes( $term_meta['category_tagline'] );
            }
            if ( ! $tagline ) {
                $tagline = get_term_meta( $term->term_id, 'tax_tagline', true );
            }
            if ( ! $tagline ) {
                $tagline = get_term_meta( $term->term_id, 'tagline', true );
            }
        }
        
        
        if (  ( !$tagline && \Elementor\Plugin::$instance->editor->is_edit_mode()) || is_singular( 'wpestate-studio' ) ) {
    
            $latest_terms = get_terms([
                'taxonomy'   => 'property_city',
                'hide_empty' => false,
                'number'     => 1,
                'orderby'    => 'term_id',
                'order'      => 'DESC',
            ]);
           
            if ( ! empty( $latest_terms ) && ! is_wp_error( $latest_terms ) ) {
                $term = $latest_terms[0];
                if ( $term instanceof \WP_Term ) {
                    $term_meta = get_option( 'taxonomy_' . $term->term_id );
                    if ( isset( $term_meta['category_tagline'] ) ) {
                        $tagline = stripslashes( $term_meta['category_tagline'] );
                    }
                    if ( ! $tagline ) {
                        $tagline = get_term_meta( $term->term_id, 'tax_tagline', true );
                    }
                    if ( ! $tagline ) {
                        $tagline = get_term_meta( $term->term_id, 'tagline', true );
                    }
                }
            }
        }
       
        if ($tagline) {
            echo '<div class="wpresidence-term-tagline">' . esc_html($tagline) . '</div>';
        } else {
            echo '<div class="wpresidence-term-tagline">' . esc_html__( 'This term does not have a tagline.', 'residence-elementor' ) . '</div>';
        }
    }
}