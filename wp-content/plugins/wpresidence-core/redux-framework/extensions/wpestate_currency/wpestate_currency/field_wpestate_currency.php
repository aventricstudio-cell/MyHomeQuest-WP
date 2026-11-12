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
if( !class_exists( 'ReduxFramework_wpestate_currency' ) ) {

    /**
     * Main ReduxFramework_wpestate_currency class
     *
     * @since       1.0.0
     */
    class ReduxFramework_wpestate_currency {
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

            if( !empty( $this->value)){
                while($i< count(     $this->value['add_curr_name']) ){
                    $add_curr_name='';
                    if(isset(  $this->value['add_curr_name'][$i])){
                        $add_curr_name= $this->value['add_curr_name'][$i];
                    }

                    $add_curr_label='';
                    if(isset($this->value['add_curr_label'][$i])){
                        $add_curr_label=$this->value['add_curr_label'][$i];
                    }

                    if(isset($this->value['add_curr_value'][$i])){
                        $add_curr_value=$this->value['add_curr_value'][$i];
                    }

                    if(isset($this->value['add_curr_order'][$i])){
                        $add_curr_order=$this->value['add_curr_order'][$i];
                    }

                    $current_fields.='
                        <div class=field_row>
                        <div    class="field_item"><strong>'.esc_html__( 'Currency Symbol','wpresidence-core').'</strong></br><input   type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_curr_name][]"    value="'.$add_curr_name.'"  ></div>
                        <div    class="field_item"><strong>'.esc_html__( 'Currency Label','wpresidence-core').'</strong></br><input  type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_curr_label][]"   value="'.$add_curr_label.'"  ></div>
                        <div    class="field_item"><strong>'.esc_html__( 'Currency Value','wpresidence-core').'</strong></br><input  type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_curr_value][]"   value="'.$add_curr_value.'"  ></div>
                        <div    class="field_item"><strong>'.esc_html__( 'Currency Position','wpresidence-core').'</strong></br><input  type="text"name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_curr_order][]" value="'.$add_curr_order.'"  ></div>

                        <a class="deletefieldlink button secondary " href="#">'.esc_html__( 'delete','wpresidence-core').'</a>
                    </div>';
                    $i++;
                }
            }




            print '
            <div id="custom_fields">

                '.$current_fields.'
               <input type="hidden" name="is_custom_cur" value="1">
            </div>
            <div class="add_curency" id="add_curency_wrapper">

            <div class="cur_explanations">'.esc_html__( 'Currency Symbol - Add a space manually if you want it to appear before / after the amount','wpresidence-core').'</div>
            <input  type="text" id="currency_name"  name="currency_name"   value="" size="40"/>

            <div class="cur_explanations">'.esc_html__( 'Currency Label - Must follow ISO currency standards.','wpresidence-core').'</div>
            <input  type="text" id="currency_label"  name="currency_label"   value="" size="40"/>

            <div class="cur_explanations">'.esc_html__( 'Currency Value compared to the base currency','wpresidence-core').'</div>
            <input  type="text" id="currency_value"  name="currency_value"   value="" size="40"/>

            <div class="cur_explanations">'.esc_html__( 'Show currency before or after price','wpresidence-core').'</div>
            <select id="where_cur" name="where_cur"  >
                <option value="before"> before </option>
                <option value="after">  after </option>
            </select>

        </div>
        <a href="#" id="add_curency2" class="button button-primary secondary">'.esc_html__( ' Click To Add Currency','wpresidence-core').'</a><br>';
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
                $this->extension_url . 'field_wpestate_currency.js',
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-icon-select-css',
                $this->extension_url . 'field_wpestate_currency.css',
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
