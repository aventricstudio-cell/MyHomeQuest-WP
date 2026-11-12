<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Term_Header extends Widget_Base {

    public function get_name() {
        return 'term_header';
    }

    public function get_categories() {
        return ['category_widgets'];
    }

    public function get_title() {
        return __('Term Header', 'residence-elementor');
    }

    public function get_icon() {
        return 'wpresidence-note  eicon-header';
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'container_height',
            [
                'label' => esc_html__('Container Height', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 800,
                    ],
                    'vh' => [
                        'min' => 20,
                        'max' => 100,
                    ],
                ],
                'size_units' => ['px', 'vh'],
                'default' => [
                    'unit' => 'px',
                    'size' => 400,
                ],
                'selectors' => [
                    '{{WRAPPER}} .term-featured-container' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Title Styling
        $this->start_controls_section(
            'title_style',
            [
                'label' => esc_html__('Title Style', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .term-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Title Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .term-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow',
                'selector' => '{{WRAPPER}} .term-title',
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => esc_html__('Title Margin', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .term-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Tagline Styling
        $this->start_controls_section(
            'tagline_style',
            [
                'label' => esc_html__('Tagline Style', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'tagline_typography',
                'selector' => '{{WRAPPER}} .term-tagline',
            ]
        );

        $this->add_control(
            'tagline_color',
            [
                'label' => esc_html__('Tagline Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .term-tagline' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'tagline_text_shadow',
                'selector' => '{{WRAPPER}} .term-tagline',
            ]
        );

        $this->add_responsive_control(
            'tagline_margin',
            [
                'label' => esc_html__('Tagline Margin', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .term-tagline' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Overlay Styling
        $this->start_controls_section(
            'overlay_style',
            [
                'label' => esc_html__('Overlay', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'show_overlay',
            [
                'label' => esc_html__('Show Overlay', 'residence-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'overlay_color',
            [
                'label' => esc_html__('Overlay Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0,0.4)',
                'selectors' => [
                    '{{WRAPPER}} .term-overlay' => 'background-color: {{VALUE}};',
                ],
                'condition' => [
                    'show_overlay' => 'yes',
                ],
            ]
        );

            $this->end_controls_section();
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Border', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        // Border
        $this->add_group_control(
        \Elementor\Group_Control_Border::get_type(),
        [
            'name' => 'container_border',
            'selector' => '{{WRAPPER}} .term-featured-container',
        ]
        );

        // Border Radius
        $this->add_responsive_control(
        'container_border_radius',
        [
            'label' => esc_html__('Border Radius', 'residence-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em', 'rem'],
            'selectors' => [
                '{{WRAPPER}} .term-featured-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                  '{{WRAPPER}} .term-overlay'=> 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
           
            ],
        ]
        );
            

        $this->end_controls_section();

        // Content Position
        $this->start_controls_section(
            'content_position',
            [
                'label' => esc_html__('Content Position', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_responsive_control(
        'content_align',
        [
            'label' => esc_html__('Content Alignment', 'residence-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [
                    'title' => esc_html__('Start', 'residence-elementor'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'residence-elementor'),
                        'icon' => 'eicon-text-align-center',
                ],
                'flex-end' => [
                    'title' => esc_html__('End', 'residence-elementor'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'default' => 'center',
            'selectors' => [
                '{{WRAPPER}} .term-content' => 'align-items: {{VALUE}};',
                '{{WRAPPER}} .term-title' => 'text-align: {{VALUE}};',
            ],
        ]
        );

        $this->add_responsive_control(
            'content_vertical_align',
            [
                'label' => esc_html__('Vertical Alignment', 'residence-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => esc_html__('Top', 'residence-elementor'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => esc_html__('Middle', 'residence-elementor'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => esc_html__('Bottom', 'residence-elementor'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .term-content' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
     
  
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $term = get_queried_object();
        if ( (! ( $term instanceof \WP_Term ) && \Elementor\Plugin::$instance->editor->is_edit_mode()) ||  is_singular( 'wpestate-studio' ) ) {
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

        if ( ! ( $term instanceof \WP_Term ) ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="term-featured-container fallback-preview">';
                echo '<p>' . esc_html__('No term found for preview', 'residence-elementor') . '</p>';
                echo '</div>';
            }
            return;
        }

        $term_meta = get_option( 'taxonomy_' . $term->term_id );
        $tagline = '';
        if ( isset( $term_meta['category_tagline'] ) ) {
            $tagline = stripslashes( $term_meta['category_tagline'] );
        }

        // Get featured image
        $attach_id = isset( $term_meta['category_attach_id'] ) ? $term_meta['category_attach_id'] : '';
        $image_url = isset( $term_meta['category_featured_image'] ) ? $term_meta['category_featured_image'] : '';

        $background_image = '';
        if ( $image_url ) {
            $background_image = 'background-image: url(' . esc_url( $image_url ) . ');';
        } elseif ( $attach_id ) {
            $attachment_url = wp_get_attachment_image_src( $attach_id, 'full' );
            if ( $attachment_url ) {
                $background_image = 'background-image: url(' . esc_url( $attachment_url[0] ) . ');';
            }
        }

        ?>
        <div class="term-featured-container" style="<?php echo $background_image; ?>">
            <?php if ( 'yes' === $settings['show_overlay'] ) : ?>
                <div class="term-overlay"></div>
            <?php endif; ?>
            
            <div class="term-content">
                <h1 class="term-title"><?php echo esc_html( $term->name ); ?></h1>
                <?php if ( $tagline ) : ?>
                    <h2 class="term-tagline"><?php echo esc_html( $tagline ); ?></h2>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}