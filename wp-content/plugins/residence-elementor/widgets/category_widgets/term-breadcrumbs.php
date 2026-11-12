<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Term_Page_Breadcrumbs extends Widget_Base {

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
        return 'Term Breadcrumbs';
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
        return __('Term Breadcrumbs', 'residence-elementor');
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
        return 'wpresidence-note  eicon-ellipsis-h';
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
    protected function register_controls() {

        $this->start_controls_section(
                'typography_section', [
            'label' => esc_html__('Settings', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'term_title',
            'label' => esc_html__('Breadcrumbs Typography', 'residence-elementor'),
           'global' => [
            'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
        ],
            'selector' => '{{WRAPPER}} .breadcrumb,{{WRAPPER}} .breadcrumb li',
                ]
        );

        $this->add_responsive_control(
                'term_breadcrumb_margin_top', [
            'label' => esc_html__('Breadcrumbs Margin Top (px)', 'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'devices' => ['desktop', 'tablet', 'mobile'],
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
                '{{WRAPPER}} .breadcrumb' => 'margin-top: {{SIZE}}{{UNIT}};',
            ],
                ]
        );
        $this->add_responsive_control(
                'term_breadcrumb_margin_bottom', [
            'label' => esc_html__('Breadcrumbs Margin Bottom (px)', 'residence-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'devices' => ['desktop', 'tablet', 'mobile'],
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
                '{{WRAPPER}} .breadcrumb' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
                ]
        );
        $this->add_control(
                'breadcrumb_color', [
            'label' => esc_html__('Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .breadcrumb' => 'color: {{VALUE}}',
                '{{WRAPPER}} .breadcrumb a' => 'color: {{VALUE}}',
                '{{WRAPPER}} .breadcrumb > li + li:before' => 'color: {{VALUE}}',
            ],
                ]
        );

        $this->add_control(
                'breadcrumb_active_color', [
            'label' => esc_html__('Active Color', 'residence-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .breadcrumb .active' => 'color: {{VALUE}}',
            ],
                ]
        );

        $this->add_group_control(
                \Elementor\Group_Control_Text_Shadow::get_type(), [
            'name' => 'text_shadow',
            'label' => __('Text Shadow', 'plugin-domain'),
            'selector' => '{{WRAPPER}} .breadcrumb',
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
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $current_term = null;
        
        if (is_tax()) {
            $current_term = get_queried_object();
        } elseif (\Elementor\Plugin::$instance->editor->is_edit_mode() || is_singular( 'wpestate-studio' )  ) {
            $latest_terms = get_terms([
                'taxonomy'   => 'property_city',
                'hide_empty' => false,
                'number'     => 1,
                'orderby'    => 'term_id',
                'order'      => 'DESC',
            ]);
           
            if (!empty($latest_terms) && !is_wp_error($latest_terms)) {
                $current_term = $latest_terms[0];
            }
        }
        
        if (!$current_term) {
            return;
        }
        
        // Build term hierarchy
        $term_ancestors = get_ancestors($current_term->term_id, $current_term->taxonomy);
        $term_ancestors = array_reverse($term_ancestors);
        
        // Get taxonomy object for label
        $taxonomy_obj = get_taxonomy($current_term->taxonomy);
        $taxonomy_label = $taxonomy_obj ? $taxonomy_obj->labels->singular_name : ucfirst(str_replace('_', ' ', $current_term->taxonomy));
        
        ?>
        <div class="col-xs-12 col-md-12 breadcrumb_container">
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'wpresidence'); ?></a>
                </li>
                <li>
                    <a href="<?php echo esc_url(home_url('/' . $current_term->taxonomy . '/')); ?>"><?php echo esc_html($taxonomy_label); ?></a>
                </li>
                <?php
                // Display parent terms
                foreach ($term_ancestors as $ancestor_id) {
                    $ancestor = get_term($ancestor_id, $current_term->taxonomy);
                    if ($ancestor && !is_wp_error($ancestor)) {
                        echo '<li><a href="' . esc_url(get_term_link($ancestor)) . '">' . esc_html($ancestor->name) . '</a></li>';
                    }
                }
                ?>
                <li class="active">
                    <?php echo esc_html($current_term->name); ?>
                </li>
            </ol>
        </div>
        <?php
    }
}