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

class Wpresidence_Single_Agency_Description extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Single_agency_description';
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
        return esc_html__('Single Agency Description', 'wpresidence-studio-templates');
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
        return 'wpresidence-note eicon-text';
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
        return ['wpestate_single_agency_category'];
    }

    /**
     * Register widget controls.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {
        
        $this->start_controls_section(
                    'section_content',
                    [
                            'label' => __( 'Content',  'wpestate-studio-templates' ),
                    ]
        );
        
     
        
        $this->end_controls_section();
        
          
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
                    '{{WRAPPER}} .wpresidence-post-content' => 'text-align: {{VALUE}};',
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
                    '{{WRAPPER}} .wpresidence-post-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'wpresidence_tab_item_typography',
                'selectors' => '{{WRAPPER}} .wpresidence-post-content',
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
            
            $post_id = wpestate_last_agent_id('estate_agency');
        
        }
        
        if ($post_id) {

            Plugin::instance()->db->switch_to_post( $post_id );
            
            $post = get_post($post_id);
            echo '<div class="wpresidence-post-content">' . apply_filters('the_content', $post->post_content) . '</div>';

            Plugin::instance()->db->restore_current_post();
        }

        // use the above post_it to get all post details you need

    }

}