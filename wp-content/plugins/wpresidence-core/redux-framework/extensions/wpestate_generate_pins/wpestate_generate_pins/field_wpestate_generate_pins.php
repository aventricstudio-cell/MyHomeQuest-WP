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
if( !class_exists( 'ReduxFramework_wpestate_generate_pins' ) ) {

    /**
     * Main ReduxFramework_wpestate_generate_pins class
     *
     * @since       1.0.0
     */
    class ReduxFramework_wpestate_generate_pins {

        public $parent ;
        public $field;
        public $value;
        public $extension_url ;
        public $extension_dir;

        
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



            if ( wpresidence_get_option('wp_estate_readsys','') =='yes' ){

                $path=wpestate_get_pin_file_path();


                if ( file_exists ($path) && is_writable ($path) ){
               //     wpestate_listing_pins_for_file();
                    print'<div class="estate_option_row">
                    <div id="residence_generate_pins_mess" class="label_option_row"><div id="residence_generate_pins">'.esc_html__('Click "Save Changes" to generate file with map pins for the read from file map option set to YES.','wpresidence-core').'</div></div>
                    </div>';
                    $ajax_nonce = wp_create_nonce( "wpestate_residence_generate_pins" );
                    print'<input type="hidden" id="wpestate_residence_generate_pins" value="'.esc_html($ajax_nonce).'" />    ';
                }else{
                    print'<div class="estate_option_row">
                    <div class="label_option_row">'.esc_html__('the file Google map does NOT exist or is NOT writable','wpresidence-core') .'</div>
                    </div>';
                }

            }else{
                print'<div class="estate_option_row">
                <div class="label_option_row">'.  esc_html__('Pin Generation works only if the file reading option in Google Map setting is set to yes','wpresidence-core').'</div>
                </div>';
            }
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
                $this->extension_url . 'field_wpestate_generate_pins.js',
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-icon-select-css',
                $this->extension_url . 'field_wpestate_generate_pins.css',
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
