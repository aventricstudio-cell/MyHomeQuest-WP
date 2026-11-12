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

class Wpresidence_Single_Agent_Title_Position extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Single_agent_Title_Position';
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
        return esc_html__('Single Agent Title/Position', 'residence-elementor');
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
        return 'wpresidence-note eicon-product-title';
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
        return ['wpestate_single_agent_category'];
    }

    /**
     * Register widget controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {
          
        $this->start_controls_section(
            'section_style', [
                'label' => esc_html__('Style',  'residence-elementor'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'residence-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'residence-elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'residence-elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'residence-elementor' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'residence-elementor' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
                    '{{WRAPPER}} .agent_position' => 'text-align: {{VALUE}};',
                ],
				// 'separator' => 'after',
			]
		);

        $this->add_control(
            'text_color', [
                'label'     => esc_html__( 'Color',  'residence-elementor' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .agent_position' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .agent_position a' => 'color: {{VALUE}};',
                ],
            ]
        );

         $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'wpresidence_tab_item_typography',
                'selector' => '{{WRAPPER}} .agent_position',
                'global'   => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
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
        
        $post_id = wpestate_last_agent_id();
       
    }
    
    if ($post_id) {
        $settings = $this->get_settings_for_display();

        $details = wpestate_return_agent_details(0, $post_id);
        $author = get_post_field('post_author', $post_id);
        $agency_post = get_the_author_meta('user_agent_id', $author);
        $agency = '';
        if ($agency_post) {
            $agency = ', <a href="' . esc_url(get_permalink($agency_post)) . '">' . get_the_title($agency_post) . '</a>';
        }
        
        echo '<div class="agent_position">' . esc_html($details['realtor_position']) . $agency . '</div>';
    }

// use the above post_it to get all post details you need

}

}
