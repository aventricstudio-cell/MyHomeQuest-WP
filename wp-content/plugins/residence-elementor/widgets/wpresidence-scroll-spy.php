<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Utils;

if (!defined('ABSPATH')) exit;

class WPresidence_Scroll_Spy_Widget extends Widget_Base {

    public function get_name() {
        return 'wpresidence_scroll_spy';
    }

    public function get_title() {
        return __('WPresidence Scroll Gallery', 'residence-elementor');
    }

    public function get_icon() {
        return 'wpresidence-note eicon-anchor';
    }

    public function get_categories() {
        return ['general', 'wpresidence'];
    }

    public function get_style_depends() {
        return [ 'wpresidence-scroll-spy-style' ];
    }

    public function get_script_depends() {
        return [ 'wpresidence-scroll-spy-script' ];
    }

    protected function _register_controls() {
        // Content Repeater
        $this->start_controls_section('content_section', [
            'label' => __('Scroll Items', 'residence-elementor'),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $repeater = new Repeater();

        $repeater->add_control('item_title', [
            'label' => __('Title', 'residence-elementor'),
            'type'  => Controls_Manager::TEXT,
            'default' => 'Sample Title',
        ]);

       $repeater->add_control( 'item_content', [
            'label'       => __( 'Content', 'residence-elementor' ),
            'type'        => \Elementor\Controls_Manager::WYSIWYG,
            'default'     => '<p>Sample <strong>content</strong> with <a href="#">a link</a>.</p>',
            'placeholder' => __( 'Type your content here…', 'residence-elementor' ),
            'dynamic'     => [
                'active' => true,
            ],
        ] );

        $repeater->add_control('item_image', [
            'label' => __('Image', 'residence-elementor'),
            'type'  => Controls_Manager::MEDIA,
            'default' => [
                'url' => Utils::get_placeholder_image_src(),
            ],
        ]);

        $repeater->add_control('image_title', [
            'label' => __('Image Title', 'residence-elementor'),
            'type'  => Controls_Manager::TEXT,
            'default' => 'Image Title',
        ]);

        $repeater->add_control('image_desc', [
            'label' => __('Image Description', 'residence-elementor'),
            'type'  => Controls_Manager::TEXTAREA,
            'default' => 'Image description goes here.',
        ]);

        $this->add_control('scroll_items', [
            'label' => __('Items', 'residence-elementor'),
            'type'  => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [],
            'title_field' => '{{{ item_title }}}',
        ]);

        $this->end_controls_section();

        // Style Controls
        $this->start_controls_section('style_section', [
            'label' => __('Text Styles', 'residence-elementor'),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

       $this->add_control('item_title_color', [
            'label' => __('Item Title Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#000000',
            'selectors' => [
                '.text-image-scroller .text .items .item h3' => 'color: {{VALUE}};',
                '.text-image-scroller .text .items .item h3 a' => 'color: {{VALUE}};',
            ],
        ]);

       
        $this->add_control('item_title_hover_color', [
            'label' => __('Item Title Hover Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#0073aa',
             'selectors' => [
                '.text-image-scroller .text .items .item h3:hover' => 'color: {{VALUE}};',
                '.text-image-scroller .text .items .item h3 a:hover' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'item_title_typography',
                'label' => __('Item Title Typography', 'residence-elementor'),
                'selectors' => '.text-image-scroller .text .items .item h3, .text-image-scroller .text .items .item h3 a',
            ]
        );
        
        
        $this->add_control('item_desc_color', [
            'label' => __('Item Paragraph Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#333333',
            'selectors' => [
                '.text-image-scroller .text .items .item-desc' => 'color: {{VALUE}};',
                '.text-image-scroller .text .items .item-desc p' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'item_desc_typography',
                'label' => __('Item Paragraph Typography', 'residence-elementor'),
                'selector' => '.text-image-scroller .text .items .item-desc, .text-image-scroller .text .items .item-desc p',
            ]
        );

        $this->add_control('item_active_before_color', [
            'label' => __('Bar & Active Item Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#0073e1',
            'selectors' => [
                '.text-image-scroller-container .text-image-scroller .text .items .item.active::before' => 'background-color: {{VALUE}};',
                '.text-image-scroller-container .text-image-scroller' => 'border-color: {{VALUE}};',
                '.text-image-scroller-container .text-image-scroller .text .items .item::before'=> 'border-color: {{VALUE}};',
                '.text-image-scroller-container .text-image-scroller .text .items .item::after' => 'background-color: {{VALUE}};',
            ],
        ]);

    
        $this->end_controls_section();

        // Image Style Section
$this->start_controls_section(
    'image_style_section',
    [
        'label' => esc_html__('Image Style', 'residence-elementor'),
        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
    ]
);

// Image Width
$this->add_responsive_control(
    'image_width',
    [
        'label' => esc_html__('Width', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'size_units' => ['px', '%'],
        'range' => [
            'px' => [
                'min' => 100,
                'max' => 1000,
            ],
            '%' => [
                'min' => 10,
                'max' => 100,
            ],
        ],
        'selectors' => [
            '{{WRAPPER}} .text-image-scroller-container .text-image-scroller .image .image-holder .image-wrap' => 'width: {{SIZE}}{{UNIT}};',
        ],
    ]
);
$this->add_responsive_control(
    'image_wrap_top_position',
    [
        'label' => esc_html__('Top Position', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::SLIDER,
        'size_units' => ['px'],
        'range' => [
            'px' => [
                'min' => -200,
                'max' => 200,
            ],
        ],
        'default' => [
            'unit' => 'px',
            'size' => 60,
        ],
        'selectors' => [
            '{{WRAPPER}} .image-wrap' => 'top: {{SIZE}}{{UNIT}};',
        ],
    ]
);



// Image Border Radius
$this->add_responsive_control(
    'image_border_radius',
    [
        'label' => esc_html__('Border Radius', 'residence-elementor'),
        'type' => \Elementor\Controls_Manager::DIMENSIONS,
        'size_units' => ['px', '%'],
        'selectors' => [
            '{{WRAPPER}} .image-wrap img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
              '{{WRAPPER}} .image-wrap ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
    ]
);

// Image Box Shadow
$this->add_group_control(
    \Elementor\Group_Control_Box_Shadow::get_type(),
    [
        'name' => 'image_box_shadow',
        'label' => esc_html__('Box Shadow', 'residence-elementor'),
        'selector' => '{{WRAPPER}} .image-wrap ',
        
    ]
);

$this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $items = $settings['scroll_items'] ?? [];

        if (empty($items)) return;

      
      
        ?>
        <div class="text-image-scroller-container">
     
            <div class="text-image-scroller">

                <div class="text ">
                    <div class="items">
                        <?php foreach ($items as $index => $item): ?>
                            <div class="item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <h3 <?php echo $this->get_render_attribute_string('h3_style'); ?>><a href="#item-<?php echo esc_attr($index); ?>"><?php echo esc_html($item['item_title']); ?></a></h3>
                                <div class="item-desc" <?php echo $index === 0 ? 'style="display: block;"' : ''; ?> <?php echo $this->get_render_attribute_string('p_style'); ?>>
                                    <p><?php 
                                    
                                    $allowed = wp_kses_allowed_html( 'post' );

                                    // now add/override whatever you like:
                                    $allowed['div'] = [
                                        'class' => true,
                                        'id'    => true,
                                        'style' => true,
                                    ];
                                    $allowed['img'] = [
                                        'src'    => true,
                                        'alt'    => true,
                                        'width'  => true,
                                        'height' => true,
                                        'class'  => true,
                                        'style'  => true,
                                    ];
                                 
                                    $allowed['a']['target'] = true;
                                    $allowed['a']['rel']    = true;
                                    
                                    echo wp_kses( $item['item_content'], $allowed );
                                 ?></p>
                                </div>
                          
                            
                                
                                <?php if (!empty($item['item_image']['url'])): ?>
                                    <div class="wpresidence_image_show_tablet image-wrap wpresidence-tablet-image-wrap">
                                      
                                        <img width="880" height="525" loading="lazy"
                                             src="<?php echo esc_url($item['item_image']['url']); ?>"
                                             data-src="<?php echo esc_url($item['item_image']['url']); ?>"
                                             alt="<?php echo esc_attr($item['image_title']); ?>">
                                    </div>
                                <?php endif; ?>
                            
                            </div>
                        
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="image hide-on-tablet-landscape">
                    <?php foreach ($items as $index => $item): ?>
                        <div class="image-holder <?php echo $index === 0 ? 'active' : ''; ?>">
                            <div class="text show-on-tablet-landscape">
                               
                            </div>
                            <div class="image-wrap">
                               
                                <?php if (!empty($item['item_image']['url'])): ?>
                                    <img width="880" height="525" loading="lazy"
                                         src="<?php echo esc_url($item['item_image']['url']); ?>"
                                         data-src="<?php echo esc_url($item['item_image']['url']); ?>"
                                         alt="<?php echo esc_attr($item['image_title']); ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
        <?php
    }
}
