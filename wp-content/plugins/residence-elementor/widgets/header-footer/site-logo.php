<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;

use Elementor\Group_Control_Text_Shadow;
use Elementor\Plugin;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Site_Logo extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Site_Logo';
    }

    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__('Website Logo', 'residence-elementor');
    }

    /**
     * Get widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'wpresidence-note eicon-site-logo';
    }

    /**
     * Get widget categories.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return ['wpresidence_header'];
    }

    /**
     * Register widget controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {
        $this->wpresidence_site_logo_controls();
        $this->wpresidence_site_logo_styling_controls();
        $this->wpresidence_site_logo_caption_styling_controls();
    }

    /**
     * Register Site Logo Styling Controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function wpresidence_site_logo_styling_controls() {
        $this->start_controls_section(
            'section_style_wpresidence_site_logo',
            [
                'label' => esc_html__('Website Logo', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'padding_vertical_logo',
            [
                'label' => esc_html__('Vertical Padding', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-site-logo' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'logo_top',
            [
                'label' => __('Top', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-site-logo' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'label' => __('Width', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => ['%', 'px', 'vw'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-site-logo img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'space',
            [
                'label' => __('Max Width', 'residence-elementor') . ' (%)',
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wpresidence-site-logo' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'separator_panel_style',
            array(
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            )
        );

        $this->start_controls_tabs('image_effects');

        $this->start_controls_tab('normal',
            array(
                'label' => __('Normal', 'residence-elementor'),
            )
        );

        $this->add_control(
            'opacity',
            array(
                'label' => __('Opacity', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .wpresidence-site-logo img, {{WRAPPER}} .wpresidence-site-logo .text-logo' => 'opacity: {{SIZE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab('hover',
            array(
                'label' => __('Hover', 'residence-elementor'),
            )
        );

        $this->add_control(
            'opacity_hover',
            array(
                'label' => __('Opacity', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}}  .wpresidence-site-logo:hover img, {{WRAPPER}} .wpresidence-site-logo:hover .text-logo' => 'opacity: {{SIZE}};',
                ),
            )
        );



        $this->add_control(
            'background_hover_transition',
            array(
                'label' => __('Transition Duration', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'max' => 3,
                        'step' => 0.1,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .wpresidence-site-logo img, {{WRAPPER}} .wpresidence-site-logo .text-logo' => 'transition-duration: {{SIZE}}s',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .wpresidence-site-logo img, {{WRAPPER}} .wpresidence-site-logo .text-logo',
                'separator' => 'before',
            )
        );

        $this->add_responsive_control(
            'image_border_radius',
            array(
                'label' => __('Border Radius', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .wpresidence-site-logo img, {{WRAPPER}} .wpresidence-site-logo .text-logo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'image_box_shadow',
                'exclude' => array(
                    'box_shadow_position',
                ),
                'selector' => '{{WRAPPER}} .wpresidence-site-logo img, {{WRAPPER}} .wpresidence-site-logo .text-logo',
            )
        );

        $this->end_controls_section();
    }

    /**
     * Register Site Logo Content Controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function wpresidence_site_logo_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Logo', 'residence-elementor'),
            ]
        );

        $this->add_control(
            'logo_source',
            [
                'label' => esc_html__('Logo Source', 'residence-elementor'),
                'type' => 'select',
                'options' => [
                    'theme_option' => esc_html__('Theme Options', 'residence-elementor'),
                    'custom_logo' => esc_html__('Custom Logo', 'residence-elementor'),
                ],
                'default' => 'theme_option',
            ]
        );

        $this->add_control(
            'important_note',
            [
                'type' => 'raw_html',
                'raw' => esc_html__('Please select or upload your Logo in Theme Options.', 'residence-elementor'),
                 'content_classes' => 'elementor-control-field-description',
                'condition' => [
                    'logo_source' => 'theme_option',
                ],
            ]
        );

        $this->add_control(
            'custom_image',
            [
                'label' => esc_html__('Choose Image', 'residence-elementor'),
                'type' => 'media',
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'logo_source' => 'custom_logo',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'logo_size',
                'label' => __('Image Size', 'residence-elementor'),
                'default' => 'medium',
                'condition' => [
                    'logo_source' => 'custom_logo',
                ],
            ]
        );

       

        $this->add_control(
            'caption_source',
            [
                'label' => __('Caption', 'residence-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'no' => __('No', 'residence-elementor'),
                    'yes' => __('Yes', 'residence-elementor'),
                ],
                'default' => 'no',
                'condition' => [
                    'logo_source' => 'custom_logo',
                ],
            ]
        );

        $this->add_control(
            'caption',
            [
                'label' => __('Custom Caption', 'residence-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'placeholder' => __('Enter caption', 'residence-elementor'),
                'condition' => [
                    'caption_source' => 'yes',
                    'logo_source' => 'custom_logo',
                ],
                'label_block' => true,
            ]
        );

        $this->add_control(
            'link_to',
            [
                'label' => __('Link', 'residence-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Default', 'residence-elementor'),
                    'none' => __('None', 'residence-elementor'),
                    'custom' => __('Custom URL', 'residence-elementor'),
                ],
                'condition' => [
                    'logo_source' => 'custom_logo'
                ]
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => __('Link', 'residence-elementor'),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('https://your-link.com', 'residence-elementor'),
                'condition' => [
                    'link_to' => 'custom',
                ],
                'show_label' => false,
            ]
        );
        $this->end_controls_section();
    }

    /**
     * Register Site Logo Caption Styling Controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function wpresidence_site_logo_caption_styling_controls() {
        $this->start_controls_section(
            'section_style_caption',
            [
                'label' => __('Caption', 'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'caption_source!' => 'no',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'default' => '#7A7A7A',
                'selectors' => [
                    '{{WRAPPER}} .site-tagline' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'caption_background_color',
            [
                'label' => __('Background Color', 'residence-elementor'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .site-tagline' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'caption_typography',
                'selector' => '{{WRAPPER}} .site-tagline',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'caption_text_shadow',
                'selector' => '{{WRAPPER}} .site-tagline',
            ]
        );

        $this->add_responsive_control(
            'caption_padding',
            [
                'label' => __('Padding', 'residence-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .site-tagline' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'caption_space',
            [
                'label' => __('Spacing', 'residence-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'default' => [
                        'size' => 0,
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .site-tagline' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: 0px;',
                    ],
                ]
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Check if the logo has a caption.
     *
     * @param array $settings The widget settings.
     * @return bool True if the logo has a caption, false otherwise.
     */
    private function has_caption($settings) {
        return (!empty($settings['caption_source']) && 'no' !== $settings['caption_source'] );
    }

    /**
     * Get the caption text.
     *
     * @param array $settings The widget settings.
     * @return string The caption text.
     */
    private function get_caption($settings) {
        $caption = '';
        if ('yes' === $settings['caption_source']) {
            $caption = !empty($settings['caption']) ? $settings['caption'] : '';
        }
        return $caption;
    }

    /**
     * Get the site logo image URL.
     *
     * @param string $size The image size.
     * @return string The site logo image URL.
     */
    public function site_image_url($size) {
        $settings = $this->get_settings_for_display();
        if (!empty($settings['custom_image']['url'])) {
            $logo = wp_get_attachment_image_src($settings['custom_image']['id'], $size, true);
        } else {
            $logo = wp_get_attachment_image_src(get_theme_mod('custom_logo'), $size, true);
        }
        return $logo[0];
    }

    /**
     * Render the widget output on the frontend.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $has_caption = $this->has_caption($settings);
        $logo_source = $settings['logo_source'];

        if ('default' === $settings['link_to']) {
            $link = site_url();
            $this->add_render_attribute('link', 'href', $link);
        } else {
            $link = $this->get_link_url($settings);

            if ($link) {
                $this->add_link_attributes('link', $link);
            }
        }
        ?>
        <div class="wpresidence-site-logo">
        <?php if ($link) : ?>
                <a <?php echo $this->get_render_attribute_string('link'); ?>>
        <?php endif; ?>
        <?php
        if ('custom_logo' === $logo_source) {
            $this->custom_logo_render($settings);

            if ($has_caption) {
                $caption_text = $this->get_caption($settings);
                if (!empty($caption_text)) {

                    echo '<p class="site-tagline">' . wp_kses_post($caption_text) . '</p>';
                }
            }
        }

        if ('theme_option' === $logo_source) {
            $this->themeoption_logo_render();
        }
        ?>
                <?php if ($link) : ?>
                </a>
                <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render the custom logo.
     *
     * @param array $settings The widget settings.
     */
    protected function custom_logo_render($settings) {
        $has_caption = $this->has_caption($settings);
        $size = $settings['logo_size_size'];
        $site_image = $this->site_image_url($size);
        $img_animation = '';

        if (!empty($site_image)) {

            if ('custom' !== $size) {
                $image_size = $size;
            } else {
                require_once ELEMENTOR_PATH . 'includes/libraries/bfi-thumb/bfi-thumb.php';

                $image_dimension = $settings['logo_size_custom_dimension'];

                $image_size = [
                    0 => null, // Width.
                    1 => null, // Height.
                    'bfi_thumb' => true,
                    'crop' => true,
                ];

                $has_custom_size = false;
                if (!empty($image_dimension['width'])) {
                    $has_custom_size = true;
                    $image_size[0] = $image_dimension['width'];
                }

                if (!empty($image_dimension['height'])) {
                    $has_custom_size = true;
                    $image_size[1] = $image_dimension['height'];
                }

                if (!$has_custom_size) {
                    $image_size = 'full';
                }
            }

            $image_url = $site_image;

            if (!empty($settings['custom_image']['url'])) {
                $image_data = wp_get_attachment_image_src($settings['custom_image']['id'], $image_size, true);

                $site_image_class = 'elementor-animation-';

                if (!empty($settings['hover_animation'])) {
                    $img_animation = $settings['hover_animation'];
                }
                if (!empty($image_data)) {
                    $image_url = $image_data[0];
                }

                $class_animation = $site_image_class . $img_animation;

                echo '<img class="image-logo ' . esc_attr($class_animation) . '"  src="' . esc_url($image_url) . '" alt="' . esc_attr(Control_Media::get_image_alt($settings['custom_image'])) . '"/>';
            }
        }
    }

    /**
     * Render the theme option logo.
     */
    protected function themeoption_logo_render() {
        global $post;

        $logo_header_type = wpresidence_get_option('wp_estate_logo_header_type', '');
        $header_classes = wpestate_header_classes();

        if ($logo_header_type == 'type3' && wpestate_is_user_dashboard()) {
            $logo_header_type = 'type1';
        }
        $classes = '';
        print wpestate_display_logo($header_classes['logo'], $classes);
    }

    /**
     * Get the link URL.
     *
     * @param array $settings The widget settings.
     * @return mixed The link URL or false if not set.
     */
    private function get_link_url($settings) {
        if ('none' === $settings['link_to']) {
            return false;
        }

        if ('custom' === $settings['link_to']) {
            if (empty($settings['link']['url'])) {
                return false;
            }

            if (!empty($settings['is_external'])) {
                $this->add_render_attribute('link', 'target', '_blank');
            }

            if (!empty($settings['nofollow'])) {
                $this->add_render_attribute('link', 'rel', 'nofollow');
            }

            return $settings['link'];
        }

        if ('default' === $settings['link_to']) {
            if (empty($settings['link']['url'])) {
                return false;
            }
            return site_url();
        }
    }
}
