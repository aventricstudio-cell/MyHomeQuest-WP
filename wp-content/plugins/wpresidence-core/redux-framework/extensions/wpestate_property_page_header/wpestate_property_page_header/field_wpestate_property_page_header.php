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
if( !class_exists( 'ReduxFramework_wpestate_property_page_header' ) ) {

    /**
     * Main ReduxFramework_wpestate_property_page_header class
     *
     * @since       1.0.0
     */
    class ReduxFramework_wpestate_property_page_header {

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

            $i=0;

            $name= $this->field['name'] . $this->field['name_suffix'] . '[unit_field_value][]';


            print '<div class="custom_fields_wrapper">';
            while($i< 5 ){
                $property_unit_field_name='';
                if(isset($this->value['property_unit_field_name'][$i])){
                    $property_unit_field_name= $this->value['property_unit_field_name'][$i];
                }

                $property_unit_field_image='';
                if(isset($this->value['property_unit_field_image'][$i])){
                    $property_unit_field_image=$this->value['property_unit_field_image'][$i];
                }

                $property_unit_field_label='';
                if(isset($this->value['property_unit_field_label'][$i])){
                    $property_unit_field_label=$this->value['property_unit_field_label'][$i];
                }

                $unit_field_value='';
                if(isset($this->value['unit_field_value'][$i])){
                    $unit_field_value=$this->value['unit_field_value'][$i];
                }

                    print'
                        <div class=field_row>
                            <div    class="field_item_unit"><strong>'.__('Label(*if filled will not use icon)','wpresidence-core').'</strong></br>
                            <input  type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[property_unit_field_name][]"  '
                            . '  value="'.$property_unit_field_name.'"></div>

                            <div    class="field_item_unit"><strong>'.__('Image','wpresidence-core').'</strong></br>
                            <input  type="text"  name="' . $this->field['name'] . $this->field['name_suffix'] . '[property_unit_field_image][]"
                            value="'.$property_unit_field_image.'"></div>

                            <div    class="field_item_unit" ><strong>'.__('Icon','wpresidence-core').'</strong></br>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input data-placement="bottomRight"  name="' . $this->field['name'] . $this->field['name_suffix'] . '[property_unit_field_label][]"
                                            class="form-control icp icp-auto" value="'.$property_unit_field_label.'"
                                               type="text"/>
                                        <div class="input-group-addon">';
                                        if($property_unit_field_label!=''){
                                            print '<i class="'.$property_unit_field_label.'"></i>';
                                        }
                                        print'</div>
                                    </div>
                                </div>
                            </div>

                            <div class="field_item_unit"><strong>'.__('Field','wpresidence-core').'</strong></br>'.redux_wpestate_return_custom_unit_fields($name,$unit_field_value,'_property').'</div>
                    </div>';

                $i++;
            }
                   wpestate_font_awesome_list();
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
                $this->extension_url . 'field_wpestate_property_page_header.js',
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-icon-select-css',
                $this->extension_url . 'field_wpestate_property_page_header.css',
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
