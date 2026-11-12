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
if( !class_exists( 'ReduxFramework_wpestate_taxonomy_tabs_price' ) ) {

    /**
     * Main ReduxFramework_wpestate_taxonomy_tabs_price class
     *
     * @since       1.0.0
     */
    class ReduxFramework_wpestate_taxonomy_tabs_price {
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
            global $wpresidence_admin;

            $i=0;
            if(isset( $wpresidence_admin['wp_estate_adv6_taxonomy'])){
                $adv6_taxonomy  =    $wpresidence_admin['wp_estate_adv6_taxonomy'];
            }

            if( isset($wpresidence_admin['wp_estate_adv6_taxonomy_terms'] ) && is_array($wpresidence_admin['wp_estate_adv6_taxonomy_terms']) ){
                foreach ( $wpresidence_admin['wp_estate_adv6_taxonomy_terms'] as $term_id){
                $term = get_term( $term_id, $adv6_taxonomy);

                if(isset($term->name)){
                 
                    print '<div class="field_row">
                        <div class="field_item">';
                    
                    $lenght='';
                    if( $this->field['id'] == 'wp_estate_adv6_min_price_dropdown_values' ||  $this->field['id'] =='wp_estate_adv6_max_price_dropdown_values' ){
                       print  esc_html__('Price Drodown Values for ','wpresidence-core');
                         $lenght='style="width:400px;"';
                    }else{
                        print esc_html__('Price Slider Values for ','wpresidence-core');
                    }
                    print '<strong>'.$term->name.'</strong></div>

                        <div class="field_item">
                           <input type="text" id="adv6_min_price" '.$lenght.' name="'.$this->field['name'].'[]" value="';

                            if( isset( $this->value[$i]) ){
                                print $this->value[$i];
                            }
                            print'">
                        </div>


                    </div>';
                }
                $i++;

            }
            }
        print'</select>';
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
                $this->extension_url . 'field_wpestate_taxonomy_tabs_price.js',
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-icon-select-css',
                $this->extension_url . 'field_wpestate_taxonomy_tabs_price.css',
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
