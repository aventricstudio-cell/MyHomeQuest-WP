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

class Wpresidence_Single_Post_Comments extends Widget_Base {

    /**
     * Get widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'Single_post_comments';
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
        return esc_html__('Single Post Comments', 'residence-elementor');
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
        return 'wpresidence-note eicon-comments';
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
				'label' => esc_html__( 'Comments', 'residence-elementor' ),
			]
		);

		$this->add_control(
			'_skin',
			[
				'type' => Controls_Manager::HIDDEN,
			]
		);

		$this->add_control(
			'skin_temp',
			[
				'label' => esc_html__( 'Skin', 'residence-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'Theme Comments', 'residence-elementor' ),
				],
				'description' => esc_html__( 'The Theme Comments skin uses the currently active theme comments design and layout to display the comment form and comments.', 'elementor-pro' ),
			]
		);
       
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

        Plugin::instance()->db->switch_to_post( $post_id );

        if ( ! comments_open() && ( Plugin::instance()->preview->is_preview_mode() || Plugin::instance()->editor->is_edit_mode() ) ) :
			?>
			<div class="elementor-alert elementor-alert-danger" role="alert">
				<span class="elementor-alert-title">
					<?php echo esc_html__( 'Comments are closed.', 'residence-elementor' ); ?>
				</span>
				<span class="elementor-alert-description">
					<?php echo esc_html__( 'Switch on comments from either the discussion box on the WordPress post edit screen or from the WordPress discussion settings.', 'elementor-pro' ); ?>
				</span>
			</div>
			<?php
		else :
			comments_template('', true);
		endif;

        Plugin::instance()->db->restore_current_post();
    }

// use the above post_it to get all post details you need

}

}