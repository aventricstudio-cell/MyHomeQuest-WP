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
use Elementor\Group_Control_Text_Stroke;
use Elementor\Plugin;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Wpresidence_Single_Post_Navigation extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Single_post_navigation';
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
        return esc_html__('Single Post Navigation', 'residence-elementor');
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
        return 'wpresidence-note eicon-post-navigation';
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
        return ['wpestate_single_post_category'];
    }

	public function get_style_depends() {
        return [ 'wpresidence-post-navigation-style' ];
    }

    /**
     * Register widget controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {
        
        $this->start_controls_section(
			'section_post_navigation_content',
			[
				'label' => esc_html__( 'Post Navigation', 'residence-elementor' ),
			]
		);

		$this->add_control(
			'show_label',
			[
				'label' => esc_html__( 'Label', 'residence-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'residence-elementor' ),
				'label_off' => esc_html__( 'Hide', 'residence-elementor' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'prev_label',
			[
				'label' => esc_html__( 'Previous Label', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'default' => esc_html__( 'Previous', 'residence-elementor' ),
				'condition' => [
					'show_label' => 'yes',
				],
			]
		);

		$this->add_control(
			'next_label',
			[
				'label' => esc_html__( 'Next Label', 'residence-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Next', 'residence-elementor' ),
				'condition' => [
					'show_label' => 'yes',
				],
				'ai' => [
					'active' => false,
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'show_arrow',
			[
				'label' => esc_html__( 'Arrows', 'residence-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'residence-elementor' ),
				'label_off' => esc_html__( 'Hide', 'residence-elementor' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'arrow',
			[
				'label' => esc_html__( 'Arrows Type', 'residence-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'fa fa-angle-left' => esc_html__( 'Angle', 'residence-elementor' ),
					'fa fa-angle-double-left' => esc_html__( 'Double Angle', 'residence-elementor' ),
					'fa fa-chevron-left' => esc_html__( 'Chevron', 'residence-elementor' ),
					'fa fa-chevron-circle-left' => esc_html__( 'Chevron Circle', 'residence-elementor' ),
					'fa fa-caret-left' => esc_html__( 'Caret', 'residence-elementor' ),
					'fa fa-arrow-left' => esc_html__( 'Arrow', 'residence-elementor' ),
					'fa fa-long-arrow-left' => esc_html__( 'Long Arrow', 'residence-elementor' ),
					'fa fa-arrow-circle-left' => esc_html__( 'Arrow Circle', 'residence-elementor' ),
					'fa fa-arrow-circle-o-left' => esc_html__( 'Arrow Circle Negative', 'residence-elementor' ),
				],
				'default' => 'fa fa-angle-left',
				'condition' => [
					'show_arrow' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label' => esc_html__( 'Post Title', 'residence-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'residence-elementor' ),
				'label_off' => esc_html__( 'Hide', 'residence-elementor' ),
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_borders',
			[
				'label' => esc_html__( 'Borders', 'residence-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'residence-elementor' ),
				'label_off' => esc_html__( 'Hide', 'residence-elementor' ),
				'default' => 'yes',
				'prefix_class' => 'wpresidence-post-navigation-borders-',
			]
		);

		// Filter out post type without taxonomies
		$post_type_options = [];
		$post_type_taxonomies = [];
		foreach ( get_post_types( [ 'public' => true ], 'objects' ) as $post_type => $post_type_object ) {
			$taxonomies = get_object_taxonomies( $post_type, 'objects' );
			if ( empty( $taxonomies ) ) {
				continue;
			}

			$post_type_options[ $post_type ] = $post_type_object->label;
			$post_type_taxonomies[ $post_type ] = [];
			foreach ( $taxonomies as $taxonomy ) {
				$post_type_taxonomies[ $post_type ][ $taxonomy->name ] = $taxonomy->label;
			}
		}

		$this->add_control(
			'in_same_term',
			[
				'label' => esc_html__( 'In same Term', 'residence-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $post_type_options,
				'default' => '',
				'multiple' => true,
				'label_block' => true,
				'description' => esc_html__( 'Indicates whether next post must be within the same taxonomy term as the current post, this lets you set a taxonomy per each post type', 'residence-elementor' ),
			]
		);

		foreach ( $post_type_options as $post_type => $post_type_label ) {
			$this->add_control(
				$post_type . '_taxonomy',
				[
					'label' => $post_type_label . ' ' . esc_html__( 'Taxonomy', 'residence-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => $post_type_taxonomies[ $post_type ],
					'default' => '',
					'condition' => [
						'in_same_term' => $post_type,
					],
				]
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'label_style',
			[
				'label' => esc_html__( 'Label', 'residence-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_label' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_label_style' );

		$this->start_controls_tab(
			'label_color_normal',
			[
				'label' => esc_html__( 'Normal', 'residence-elementor' ),
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Color', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} span.post-navigation__prev--label' => 'color: {{VALUE}};',
					'{{WRAPPER}} span.post-navigation__next--label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'label_color_hover',
			[
				'label' => esc_html__( 'Hover', 'residence-elementor' ),
			]
		);

		$this->add_control(
			'label_hover_color',
			[
				'label' => esc_html__( 'Color', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} span.post-navigation__prev--label:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} span.post-navigation__next--label:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'label_hover_color_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'residence-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms', 'custom' ],
				'default' => [
					'unit' => 'ms',
				],
				'selectors' => [
					'{{WRAPPER}} span.post-navigation__prev--label' => 'transition-duration: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} span.post-navigation__next--label' => 'transition-duration: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} span.post-navigation__prev--label, {{WRAPPER}} span.post-navigation__next--label',
				'exclude' => [ 'line_height' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style',
			[
				'label' => esc_html__( 'Title', 'residence-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_post_navigation_style' );

		$this->start_controls_tab(
			'tab_color_normal',
			[
				'label' => esc_html__( 'Normal', 'residence-elementor' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Color', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} span.post-navigation__prev--title, {{WRAPPER}} span.post-navigation__next--title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_color_hover',
			[
				'label' => esc_html__( 'Hover', 'residence-elementor' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label' => esc_html__( 'Color', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} span.post-navigation__prev--title:hover, {{WRAPPER}} span.post-navigation__next--title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_color_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'residence-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms', 'custom' ],
				'default' => [
					'unit' => 'ms',
				],
				'selectors' => [
					'{{WRAPPER}} span.post-navigation__prev--title' => 'transition-duration: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} span.post-navigation__next--title' => 'transition-duration: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} span.post-navigation__prev--title, {{WRAPPER}} span.post-navigation__next--title',
				'exclude' => [ 'line_height' ],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'arrow_style',
			[
				'label' => esc_html__( 'Arrow', 'residence-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_arrow' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_post_navigation_arrow_style' );

		$this->start_controls_tab(
			'arrow_color_normal',
			[
				'label' => esc_html__( 'Normal', 'residence-elementor' ),
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label' => esc_html__( 'Color', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post-navigation__arrow-wrapper' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'arrow_color_hover',
			[
				'label' => esc_html__( 'Hover', 'residence-elementor' ),
			]
		);

		$this->add_control(
			'arrow_hover_color',
			[
				'label' => esc_html__( 'Color', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .post-navigation__arrow-wrapper:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'arrow_hover_color_transition_duration',
			[
				'label' => esc_html__( 'Transition Duration', 'residence-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's', 'ms', 'custom' ],
				'default' => [
					'unit' => 'ms',
				],
				'selectors' => [
					'{{WRAPPER}} .post-navigation__arrow-wrapper' => 'transition-duration: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'arrow_size',
			[
				'label' => esc_html__( 'Size', 'residence-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
					],
					'em' => [
						'max' => 30,
					],
					'rem' => [
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .post-navigation__arrow-wrapper' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'arrow_padding',
			[
				'label' => esc_html__( 'Gap', 'residence-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'max' => 5,
					],
					'rem' => [
						'max' => 5,
					],
				],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} .post-navigation__arrow-prev' => 'padding-right: {{SIZE}}{{UNIT}};',
					'body:not(.rtl) {{WRAPPER}} .post-navigation__arrow-next' => 'padding-left: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}} .post-navigation__arrow-prev' => 'padding-left: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}} .post-navigation__arrow-next' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'borders_section_style',
			[
				'label' => esc_html__( 'Borders', 'residence-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_borders!' => '',
				],
			]
		);

		$this->add_control(
			'sep_color',
			[
				'label' => esc_html__( 'Color', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				//'default' => '#D4D4D4',
				'selectors' => [
					'{{WRAPPER}} .elemwpresidenceentor-post-navigation__separator' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .wpresidence-post-navigation' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'borders_width',
			[
				'label' => esc_html__( 'Size', 'residence-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wpresidence-post-navigation__separator' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .wpresidence-post-navigation' => 'border-top-width: {{SIZE}}{{UNIT}}; border-bottom-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .wpresidence-post-navigation__next.wpresidence-post-navigation__link' => 'width: calc(50% - ({{SIZE}}{{UNIT}} / 2))',
					'{{WRAPPER}} .wpresidence-post-navigation__prev.wpresidence-post-navigation__link' => 'width: calc(50% - ({{SIZE}}{{UNIT}} / 2))',
				],
			]
		);

		$this->add_control(
			'borders_spacing',
			[
				'label' => esc_html__( 'Spacing', 'residence-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wpresidence-post-navigation' => 'padding: {{SIZE}}{{UNIT}} 0;',
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
  protected function render() {
    $post_id = get_the_ID();
    
 
    
    if (Plugin::instance()->editor->is_edit_mode() || 
        Plugin::instance()->preview->is_preview_mode() || 
        is_singular( 'wpestate-studio' ) ||
        is_preview()) {
        
        $post_id = wpestate_last_post_id();
       
    }
    
    if ($post_id) {
        $settings = $this->get_settings_for_display();

		$prev_label = '';
		$next_label = '';
		$prev_arrow = '';
		$next_arrow = '';

		if ( 'yes' === $settings['show_label'] ) {
			$prev_label = '<span class="post-navigation__prev--label">' . $settings['prev_label'] . '</span>';
			$next_label = '<span class="post-navigation__next--label">' . $settings['next_label'] . '</span>';
		}

		if ( 'yes' === $settings['show_arrow'] ) {
			if ( is_rtl() ) {
				$prev_icon_class = str_replace( 'left', 'right', $settings['arrow'] );
				$next_icon_class = $settings['arrow'];
			} else {
				$prev_icon_class = $settings['arrow'];
				$next_icon_class = str_replace( 'left', 'right', $settings['arrow'] );
			}

			$prev_arrow = '<span class="post-navigation__arrow-wrapper post-navigation__arrow-prev"><i class="' . esc_attr( $prev_icon_class ) . '" aria-hidden="true"></i><span class="elementor-screen-only">' . esc_html__( 'Prev', 'elementor-pro' ) . '</span></span>';
			$next_arrow = '<span class="post-navigation__arrow-wrapper post-navigation__arrow-next"><i class="' . esc_attr( $next_icon_class ) . '" aria-hidden="true"></i><span class="elementor-screen-only">' . esc_html__( 'Next', 'elementor-pro' ) . '</span></span>';
		}

		$prev_title = '';
		$next_title = '';

		if ( 'yes' === $settings['show_title'] ) {
			$prev_title = '<span class="post-navigation__prev--title">%title</span>';
			$next_title = '<span class="post-navigation__next--title">%title</span>';
		}

		$in_same_term = false;
		$taxonomy = 'category';
		$post_type = get_post_type( $post_id );

		if ( ! empty( $settings['in_same_term'] ) && is_array( $settings['in_same_term'] ) && in_array( $post_type, $settings['in_same_term'] ) ) {
			if ( isset( $settings[ $post_type . '_taxonomy' ] ) ) {
				$in_same_term = true;
				$taxonomy = $settings[ $post_type . '_taxonomy' ];
			}
		}
		?>
		<div class="wpresidence-post-navigation">
			<div class="wpresidence-post-navigation__prev wpresidence-post-navigation__link">
				<?php previous_post_link( '%link', $prev_arrow . '<span class="wpresidence-post-navigation__link__prev">' . $prev_label . $prev_title . '</span>', $in_same_term, '', $taxonomy ); ?>
			</div>
			<?php if ( 'yes' === $settings['show_borders'] ) : ?>
				<div class="wpresidence-post-navigation__separator-wrapper">
					<div class="wpresidence-post-navigation__separator"></div>
				</div>
			<?php endif; ?>
			<div class="wpresidence-post-navigation__next wpresidence-post-navigation__link">
				<?php next_post_link( '%link', '<span class="wpresidence-post-navigation__link__next">' . $next_label . $next_title . '</span>' . $next_arrow, $in_same_term, '', $taxonomy ); ?>
			</div>
		</div>
		<?php
    }

// use the above post_it to get all post details you need

}

}