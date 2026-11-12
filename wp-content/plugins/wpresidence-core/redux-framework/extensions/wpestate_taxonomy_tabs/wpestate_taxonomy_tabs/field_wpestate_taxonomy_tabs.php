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
if( !class_exists( 'ReduxFramework_wpestate_taxonomy_tabs' ) ) {

    /**
     * Main ReduxFramework_wpestate_taxonomy_tabs class
     *
     * @since       1.0.0
     */
    class ReduxFramework_wpestate_taxonomy_tabs {
        public $parent ;
        public $field;
        public $value;
        public $extension_dir;
        public $extension_url; 
        /**
         * 
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
            global $wpresidence_admin;
            print'<input type="hidden" name="adv6_taxonomy_terms" />
            <select id="adv6_taxonomy_terms" name="'.$this->field['name'].'[]" multiple="multiple" style="';

            print 'height:200px;">';

            if(isset($wpresidence_admin['wp_estate_adv6_taxonomy']) && $wpresidence_admin['wp_estate_adv6_taxonomy'] !=='' ){
                $terms = wpestate_get_cached_terms(   
                    $wpresidence_admin['wp_estate_adv6_taxonomy'],
                 array(
                    
                        'hide_empty' => false,
                        'orderby'   =>'name',
                        'order'     =>'ASC'
                    ) 
                );


                foreach($terms as $term){
                    if(isset($term->term_id) ){
                        print '<option value="'.$term->term_id.'" ';
                        if(is_array($this->value) && in_array($term->term_id, $this->value) ){
                            print ' selected= "selected" ';
                        }

                        print' >';
                        if(isset($term->name)){
                            print $term->name;
                        }
                        print '</option>';
                    }
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
                $this->extension_url . 'field_wpestate_taxonomy_tabs.js',
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-icon-select-css',
                $this->extension_url . 'field_wpestate_taxonomy_tabs.css',
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
