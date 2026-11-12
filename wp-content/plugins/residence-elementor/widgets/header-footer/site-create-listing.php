<?php
// Namespace and use statements
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

// Prevent direct access to the file
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


/**
 * Class representing the custom Elementor widget.
 * This widget creates a button that allows users to add new listings.
 */
class Wpresidence_Site_Create_Listing extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Site_Create_Listing';
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
        return esc_html__('Add New Listing Button', 'residence-elementor');
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
     * Define the different sections and fields for the widget in the Elementor editor.
     */
    protected function register_controls() {
        $this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'residence-elementor' ),
			]
		);

        
          $this->add_control(
			'button_label',
			[
                            'label' => __( 'Button label',  'residence-elementor' ),
                            'type' => Controls_Manager::TEXT,
                            'default'=>  esc_html__('Add Listing','residence-elementor')
			]
		);
          
         $this->add_control(
			'show_custom',
			[
				'label' => __( 'Custom Link', 'residence-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'residence-elementor' ),
				'label_off' => __( 'No', 'residence-elementor' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

         	$this->add_control(
			'custom_link',
			[
				'label' => __( 'Link', 'residence-elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://link.com', 'residence-elementor' ),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
				'condition' => [
					'show_custom' => 'yes'
				]
			]
		);

         
        $this->end_controls_section();
        
        
        $this->start_controls_section(
        'style_section', [
        'label' => esc_html__('Style', 'residence-elementor'),
        'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
                $this->add_responsive_control(
			'buton_padding',
			[
				'label' => __( 'Padding', 'residence-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .submit_listing ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label' => __( 'Margin', 'residence-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .submit_listing' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

	

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'buttontypography',
			   'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY
				],
				'selector' => '{{WRAPPER}} .submit_listing',
			]
		);

		$this->add_control(
			'submit_listing_color',
			[
				'label' => __( 'Button Color', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .submit_listing' => 'color: {{VALUE}}',
				],
				'default' => '#ffffff'
			]
		);
                $this->add_control(
			'submit_listing_bg_color',
			[
				'label' => __( 'Button Background Color', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .submit_listing' => 'background: {{VALUE}}',
				],
				'default' => '#0073e1'
			]
		);
        
        
		$this->add_control(
			'submit_listing_color_hover',
			[
				'label' => __( 'Button Color Hover', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .submit_listing:hover, .submit_listing:active' => 'color: {{VALUE}}',
				],
				'default' => '#fffffffc',
				'alpha' => true,
			]
		);

		$this->add_control(
			'submit_listing_bg_color_hover',
			[
				'label' => __( 'Button Background Color hover', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .submit_listing:hover' => 'background-color: {{VALUE}}',
				],
				'default' => '#0073e1'
			]
		);

		$this->add_control(
			'submit_listing_border_color',
			[
				'label' => __( 'Button Border Color', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .submit_listing' => 'border-color: {{VALUE}}',
				],
				
			]
		);

		$this->add_control(
			'create_btn_border_color_hover',
			[
				'label' => __( 'Button Border Color Hover', 'residence-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .submit_listing:hover, .submit_listing:active' => 'border-color: {{VALUE}}',
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
        $settings = $this->get_settings_for_display();

        $label = !empty($settings['button_label']) ? $settings['button_label'] : esc_html__('Add Listing', 'residence-elementor');

        $link = wpestate_get_template_link('page-templates/front_property_submit.php');
        $show_custom = !empty($settings['show_custom']) ? $settings['show_custom'] : 'no';

        if ($show_custom === 'yes' && !empty($settings['custom_link']['url'])) {
            $link = $settings['custom_link']['url'];
        }

        $target = !empty($settings['custom_link']['is_external']) ? ' target="_blank"' : '';
        $nofollow = !empty($settings['custom_link']['nofollow']) ? ' rel="nofollow"' : '';

        ?>
        <div class="wpresidence_submit_button_wrapper">
            <a href="<?php echo esc_url($link); ?>" <?php echo esc_attr($target); ?> <?php echo esc_attr($nofollow); ?> class="submit_listing wpresidence_studio_submit_listing"><?php echo esc_html($label); ?></a>
        </div>
        <?php
    }


}
