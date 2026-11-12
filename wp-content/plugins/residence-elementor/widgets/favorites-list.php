<?php
namespace ElementorWpResidence\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class Wpresidence_Favorite_List extends Widget_Base
{

   public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
        wp_register_script('wpestate_favorite_remove', get_template_directory_uri().'/js/favorite_remove.js',array('jquery'),'', true);
        wp_localize_script('wpestate_favorite_remove', 'wpestate_favorite_remove_vars',
                    array(  'remove_from_favorites'                 =>   esc_html__('Remove from Favorites','residence-elementor'))
                );
   }
    
  

      

    /**
     * Retrieve the widget name.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'Wpresidence_Favorite_List';
    }

    public function get_categories()
    {
        return [ 'wpresidence' ];
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
    public function get_title()
    {
        return __('Favorite List - Front End', 'residence-elementor');
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
    public function get_icon()
    {
        return 'wpresidence-note eicon-posts-masonry';
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
    public function get_script_depends()
    {
       return [ 'wpestate_favorite_remove' ];
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
    public function elementor_transform($input)
    {
        $output=array();
        if (is_array($input)) {
            foreach ($input as $key=>$tax) {
                $output[$tax['value']]=$tax['label'];
            }
        }
        return $output;
    }


    protected function register_controls()
    {
        


       

        $featured_listings  =   array('no'=>'no','yes'=>'yes');
        $items_type         =   array('properties'=>'properties','articles'=>'articles','agents'=>'agents');
        $alignment_type     =   array('vertical'=>'vertical','horizontal'=>'horizontal');

        $sort_options = array();
        if( function_exists('wpestate_listings_sort_options_array')){
          $sort_options			= wpestate_listings_sort_options_array();
        }

        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Content', 'residence-elementor'),
            ]
        );

        $this->add_control(
            'title_residence',
            [
                'label' => __('Title', 'residence-elementor'),
                              'type' => Controls_Manager::TEXT,
                                'Label Block'

            ]
        );

     

      
        $this->add_control(
            'number',
            [
                            'label' => __('No of items', 'residence-elementor'),
                            'type' => Controls_Manager::TEXT,
                            'default'=>9,
            ]
        );

        $this->add_control(
            'rownumber',
            [
                'label' => __('No of items per row', 'residence-elementor'),
                'type' => Controls_Manager::TEXT,
                'default'=>3,
            ]
        );


        $this->add_control(
            'align',
            [
                            'label' => __('"What type of alignment ?', 'residence-elementor'),
                            'type' => \Elementor\Controls_Manager::SELECT,
                            'default' => 'vertical',
                            'options' => $alignment_type
            ]
        );

        $this->end_controls_section();

        /*
         * -------------------------------------------------------------------------------------------------
         * Start shadow section
         */
        $this->start_controls_section(
                'section_grid_box_shadow', [
            'label' => esc_html__('Box Shadow', 'residence-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'box_shadow',
            'label' => esc_html__('Box Shadow', 'residence-elementor'),
            'selector' => '{{WRAPPER}} .property_listing ',
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

    public function wpresidence_send_to_shortcode($input)
    {
        $output='';
        if ($input!=='') {
            $numItems = count($input);
            $i = 0;

            foreach ($input as $key=>$value) {
                $output.=$value;
                if (++$i !== $numItems) {
                    $output.=', ';
                }
            }
        }
        return $output;
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();


   
        if(isset($settings['title_residence'])){
            $attributes['title']                =   $settings['title_residence'];
        }
   
        $attributes['type']                 =   'properties';
        $attributes['number']               =   $settings['number'];
        $attributes['rownumber']            =   $settings['rownumber'];
        $attributes['align']                =   $settings['align'];
        $attributes['show_featured_only']   =   '';
        $attributes['random_pick']          =   'no';
        $attributes['sort_by']       	    =   0;
        $attributes['only_favorites']       =   'yes';
 
        echo  wpestate_recent_posts_pictures_new($attributes);
        
         print '
            <script type="text/javascript">
                //<![CDATA[
                jQuery(document).ready(function(){
                   wpestate_enable_front_end_remove_favorites();
                });
                //]]>
            </script>';
        
    }

    /**
     * Render the widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     * @since 1.0.0
     *
     * @access protected
     */

}
