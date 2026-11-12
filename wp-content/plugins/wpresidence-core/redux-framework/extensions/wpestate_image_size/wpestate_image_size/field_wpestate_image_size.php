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
if( !class_exists( 'ReduxFramework_wpestate_image_size' ) ) {

    /**
     * Main ReduxFramework_wpestate_image_size class
     *
     * @since       1.0.0
     */
    class ReduxFramework_wpestate_image_size {
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
        function __construct( $field , $value , $parent ) {


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
       

            $image_size =wpestate_return_default_image_size();
            
   
            
            $index=$this->field['id'];

            $index= str_replace('wp_estate_', '', $index);
            
            $image_details = $image_size[$index];
            
            $name   = $image_details['name'];
            $width  = $image_details['width'];
            $height = $image_details['height'];
            $crop   = $image_details['crop'];
            if(isset($this->value['add_field_width'])){
                   $width =  $this->value['add_field_width'];
            }
                  
            if(isset($this->value['add_field_height'])){
                $height= $this->value['add_field_height'];
            }
                
                
            $selected_yes=' selected ';
            $selected_no =  '';
            if( isset($this->value['add_field_crop'] ) && $this->value['add_field_crop']=='no' ){
                $selected_no = ' selected ';
                $selected_yes='';
            }

                
            
            print '
            <div class="wpestate_admin_image_size">
                
           
                <div class="wpestate_admin_image_size_explanations">'.__('Width','wpresidence-core').'
                    <input  type="text"   name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_width]"  value="'.intval($width).'"   />
                </div>

                <div class="wpestate_admin_image_size_explanations">'.__('Height','wpresidence-core').'
                    <input  type="text"   name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_height]"  value="'.intval($height).'"   />
                </div>
                

                <div class="wpestate_admin_image_size_explanations" style="width: 100px;">' . __('Crop ', 'wpresidence-core') . '
                    <select name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_crop]" >
                        <option value="yes" '.esc_attr($selected_yes).'>yes</option>
                        <option value="no" '.esc_attr($selected_no).'>no</option>

                    </select>
                </div>
            </div>';
           
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
                $this->extension_url . 'field_wpestate_custom_url_rewrite.js',
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-icon-select-css',
                $this->extension_url . 'field_wpestate_custom_url_rewrite.css',
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

    }
}
