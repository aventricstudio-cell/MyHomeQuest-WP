<?php

namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Tabs extends Widget_Base {

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
        return 'Wpresidence_Tabs';
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
        return __('Tabs', 'residence-elementor');
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
        return ' wpresidence-note eicon-tabs';
    }

    public function get_keywords() {
        return ['tabs', 'accordion', 'toggle'];
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
                'section_tabs', [
            'label' => __('Tabs', 'elementor'),
                ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
                'tab_title', [
            'label' => __('Title & Description', 'elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Tab Title', 'elementor'),
            'placeholder' => __('Tab Title', 'elementor'),
            'label_block' => true,
                ]
        );

        $repeater->add_control(
                'tab_icon', [
            'label' => __('Icon', 'elementor'),
            'type' => \Elementor\Controls_Manager::ICONS,
            'default' => [
                'url' => \Elementor\Utils::get_placeholder_image_src(),
            ],
            'placeholder' => __('Tab Icon', 'elementor'),
            'label_block' => true,
                ]
        );


        $repeater->add_control(
                'template_tab', [
            'type' => Controls_Manager::SELECT,
            'options' => $this->wpestate_drop_posts('elementor_library'),
            'multiple' => false,
            'label' => esc_html__('Template', 'elementor'),
            'label_block' => true,
                ]
        );
        $repeater->add_control(
                'tab_content', [
            'label' => __('Content', 'elementor'),
            'default' => __('Tab Content', 'elementor'),
            'placeholder' => __('Tab Content', 'elementor'),
            'type' => Controls_Manager::WYSIWYG,
            'show_label' => false,
            'dynamic' => [
                'active' => false,
            ],
                ]
        );




        $this->add_control(
                'tabs', [
            'label' => __('Tabs Items', 'elementor'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'default' => [
                [
                    'tab_title' => __('Tab #1', 'elementor'),
                    'tab_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor'),
                ],
                [
                    'tab_title' => __('Tab #2', 'elementor'),
                    'tab_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor'),
                ],
            ],
            'title_field' => '{{{ tab_title }}}',
                ]
        );

        $this->add_control(
                'view', [
            'label' => __('View', 'elementor'),
            'type' => Controls_Manager::HIDDEN,
            'default' => 'traditional',
                ]
        );

        $this->add_control(
                'type', [
            'label' => __('Type', 'elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => 'horizontal',
            'options' => [
                'horizontal' => __('Horizontal', 'elementor'),
                'vertical' => __('Vertical', 'elementor'),
            ],
            'prefix_class' => 'elementor-tabs-view-',
            'separator' => 'before',
                ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
                'section_tabs_style', [
            'label' => __('Tabs', 'elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_control(
            'tab_font_color', [
                'label' => __('Tab Font Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#222', // Set the default color
                'selectors' => [
                    '{{WRAPPER}} .nav-link' => 'color: {{VALUE}};',
                ], 
            ]
        );

        $this->add_control(
            'tab_active_color', [
                'label' => __('Tab Active Font Color', 'elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #tab_prpg.wpestate_elementor_tabs a.active' => 'border-bottom: 3px solid {{VALUE}};color: {{VALUE}};',
                    '{{WRAPPER}} #tab_prpg.wpestate_elementor_tabs a.active svg' => 'fill:{{VALUE}};'
                ],   
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
    public function wpestate_drop_posts($post_type) {
        $args = array(
            'numberposts' => -1,
            'post_type' => $post_type
        );

        $posts = get_posts($args);
        $list = array();
        $list[''] = 'none';
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

  /**
 * Render Elementor widget tabs using Bootstrap 5.3 while preserving original structure
 */
protected function render() {
    $tabs = $this->get_settings_for_display('tabs');
    $tabs_align = $this->get_settings_for_display('type');
    $id_int = substr($this->get_id_int(), 0, 3);
    $widget_id = $this->get_id();
    ?>
    <div role="tabpanel" id="tab_prpg" class="wpestate_elementor_tabs wpestate_tab_class_<?php echo esc_attr($tabs_align); ?>">
        <div class="wpestate-elementor-nav-tabs">
            <ul class="nav nav-tabs  flex-column flex-md-row" role="tablist">
                <?php
                foreach ($tabs as $index => $item) :
                    $tab_count = $index + 1;
                    $tab_id = 'elementor-tab-title-' . $id_int . $tab_count;
                    $content_id = 'elementor-tab-content-' . $id_int . $tab_count;
                    $is_active = $index === 0;
                    ?>
                    <li role="presentation" class="nav-item">
                        <a href="#<?php echo esc_attr($content_id); ?>" 
                           id="<?php echo esc_attr($tab_id); ?>"
                           class="nav-link <?php echo $is_active ? 'active' : ''; ?>"
                           data-bs-toggle="tab"
                           role="tab"
                           aria-controls="<?php echo esc_attr($content_id); ?>"
                           aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>">
                            <?php 
                            \Elementor\Icons_Manager::render_icon($item['tab_icon'], ['aria-hidden' => 'true']);
                            echo esc_html($item['tab_title']); 
                            ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="tab-content">
            <?php
            foreach ($tabs as $index => $item) :
                $tab_count = $index + 1;
                $content_id = 'elementor-tab-content-' . $id_int . $tab_count;
                $is_active = $index === 0;
                ?>
                <div class="tab-pane fade <?php echo $is_active ? 'show active' : ''; ?>"
                     id="<?php echo esc_attr($content_id); ?>"
                     role="tabpanel"
                     aria-labelledby="<?php echo esc_attr($tab_id); ?>">
                    <?php
                    $pluginElementor = \Elementor\Plugin::instance();
                    echo $pluginElementor->frontend->get_builder_content($item['template_tab'], true);
                    echo $this->parse_text_editor($item['tab_content']);
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
}
        