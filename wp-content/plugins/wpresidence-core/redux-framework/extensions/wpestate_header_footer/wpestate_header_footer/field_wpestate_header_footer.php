<?php
/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @author      Dovy Paukstys
 * @version     3.1.5
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_wpestate_header_footer' ) ) {

    /**
     * Main ReduxFramework_wpestate_header_footer class
     *
     * @since       1.0.0
     */
    class ReduxFramework_wpestate_header_footer {
        public $parent ;
        public $field;
        public $value;
        
        public $extension_dir;
        public $extension_url;  
          
        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field , $value, $parent ) {


            $this->parent = $parent;
            $this->field = $field;
            $this->value = $value;

            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                $this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );
            }

            // Set default args for this field to avoid bad indexes. Change this to anything you use.
            $defaults = array(
                'options'           => array(),
                'stylesheet'        => '',
                'output'            => true,
                'enqueue'           => true,
                'enqueue_frontend'  => true
            );
            $this->field = wp_parse_args( $this->field, $defaults );

        }

        /**
         * Field Render Function.
         *
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {
            $i=0;
            $current_fields='';
           // $this->value['add_curr_name']
           // $this->value['add_curr_name'][$i])
            //$this->field['name'] . $this->field['name_suffix'] .
            
            $types  =   wpestate_desing_template_types();
            $location = wpestate_templates_selection_options();
          
           
            
            ?>
               
            <div class="wpestate_add_head_foot_wrapper">
                <div id="wpestate_add_head_foot_wrapper_notification"></div>
                <label><?php esc_html_e('Template Name','wpresidence-core'); ?></label>
                <input type="text" id="wpestate_add_head_foot_name">


                <label><?php esc_html_e('Template Type','wpresidence-core'); ?></label>
                <?php 
                    echo wpestate_template_create_select_field('wpestate_add_head_foot_template', $types); 
                ?>

                <label><?php esc_html_e('Display Location','wpresidence-core'); ?></label>           
                <?php 
                   echo wpestate_create_nested_select_field('wpestate_add_head_foot_location', $location); 
                ?>

                <?php $ajax_nonce_log_reg = wp_create_nonce( "wpestate_add_head_foot_action" );
                print'<input type="hidden" id="wpestate_add_head_foot_nonce" value="'.esc_html($ajax_nonce_log_reg).'" />    ';  



?>

                <button id="wpestate_add_head_foot_add_button" type="button"><?php esc_html_e('Add Template ','wpresidence-core');?></button>

            </div>


            <div class="wpestate_add_head_foot_list_wrapper">
              <?php 
              echo  $this->wpestate_get_all_head_foot_posts_list(); 
              ?>

            </div>

            <?php 


            // HTML output goes here

        }

        /**
         * Enqueue Function.
         *
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {
            wp_enqueue_script(
                'redux-field-icon-select-js',
                $this->extension_url . 'field_wpestate_header_footer.js',
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-icon-select-css',
                $this->extension_url . 'field_wpestate_header_footer.css',
                time(),
                true
            );

        }

        /**
         * Output Function.
         *
         * Used to enqueue to the front-end
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function output() {

            if ( $this->field['enqueue_frontend'] ) {

            }

        }
        
    private function wpestate_get_all_head_foot_posts_list() {
        $args = array(
            'post_type' => 'wpestate-studio',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        $types = wpestate_desing_template_types();
        $location = wpestate_templates_selection_options();
        $posts = get_posts($args);

        if (empty($posts)) {
            return '<p>' . __('No Templates found', 'wpresidence-core') . '</p>';
        }

        $output = '<div class="wpestate-head-foot-list">';
        $output .= '<div class="wpestate-head-foot-header">';
        $output .= '<div class="wpestate-head-foot-cell">' . __('Title', 'wpresidence-core') . '</div>';
        $output .= '<div class="wpestate-head-foot-cell">' . __('Template', 'wpresidence-core') . '</div>';
        $output .= '<div class="wpestate-head-foot-cell">' . __('Position', 'wpresidence-core') . '</div>';
        $output .= '<div class="wpestate-head-foot-cell">' . __('Edit with Elementor', 'wpresidence-core') . '</div>';
        $output .= '<div class="wpestate-head-foot-cell">' . __('Edit Item', 'wpresidence-core') . '</div>';
        $output .= '</div>'; // end header

        foreach ($posts as $post) {
            $template = get_post_meta($post->ID, 'wpestate_head_foot_template', true);
            $position = get_post_meta($post->ID, 'wpestate_head_foot_position', true);
            $edit_link = add_query_arg(
                array(
                    'post' => $post->ID,
                    'action' => 'elementor',
                ),
                admin_url('post.php')
            );
            $edit_item_link = get_edit_post_link($post->ID);

            // Get the position label from the $location array
            $position_label = '';
            foreach ($location as $group) {
                if (isset($group['value'][$position])) {
                    $position_label = $group['value'][$position];
                    break;
                }
            }

            $output .= '<div class="wpestate-head-foot-row">';
            $output .= '<div class="wpestate-head-foot-cell"><strong>Id: </strong>' .$post->ID.'</br><strong>Name: </strong>' . esc_html($post->post_title) . '</div>';
            $output .= '<div class="wpestate-head-foot-cell">'. esc_html($types[$template]) . '</div>';
            $output .= '<div class="wpestate-head-foot-cell">';
                if($position=='disabled'){
                    $output.=$position;
                }else{
                    $output.=esc_html($position_label);
                }
            
            $output.= '</div>';
            $output .= '<div class="wpestate-head-foot-cell"><a href="' . esc_url($edit_link) . '" target="_blank">' . __('Edit with Elementor', 'wpresidence-core') . '</a></div>';
            $output .= '<div class="wpestate-head-foot-cell"><a href="' . esc_url($edit_item_link) . '" target="_blank" class="edit-item" data-id="' . esc_attr($post->ID) . '">' . __('Edit Item', 'wpresidence-core') . '</a></div>';
            $output .= '</div>'; // end row
        }

        $output .= '</div>'; // end list

        return $output;
    }


    }
}
