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
if( !class_exists( 'ReduxFramework_wpestate_custom_unit_design' ) ) {

    /**
     * Main ReduxFramework_wpestate_custom_unit_design class
     *
     * @since       1.0.0
     */
    class ReduxFramework_wpestate_custom_unit_design {

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




            print '<div id="property_page_design_wrapper">';
                print '<div id="property_page_content" class="elements_container">
                <div class="property_page_content_wrapper">'. html_entity_decode ( stripslashes(wpresidence_get_option('wpestate_property_page_content')  ) ).'</div>
                <div class="add_me_new_row">+</div></div>';
            print '</div>';
            $ajax_nonce = wp_create_nonce( "wpestate_save_prop_design" );
            print ' <div class="estate_option_row_submit">
            <div id="save_prop_design">'.esc_html__('Save Design','wpresidence-core').'</div>
            <input type="hidden" id="wpestate_save_prop_design" value="'.esc_html($ajax_nonce).'" />

            </div>';
            $extra_elements = array(
            'Icon'          =>  'icon',
            'Text'          =>  'text',
            'Featured'      =>  'featured_icon',
            'Status'        =>  'property_status',
            'Share'         =>  'share',
            'Favorite'      =>  'favorite',
            'Compare'       =>  'compare',
            'Custom Div'    =>  'custom_div',
            'Link to page'  =>  'link_to_page'

            );

             $design_elements=wpestate_all_prop_details_prop_unit();

    print '<div class="modal fade" tabindex="-1" role="dialog" id="modal_el_pick">
        <div id="modal_el_pick_close">x</div>';

    foreach($extra_elements as $key=>$value){
        print '<div class="prop_page_design_el_modal" data-tip="'.$value.'">'.$key.'</div>';
    }

    foreach($design_elements as $key=>$value){
        print '<div class="prop_page_design_el_modal" data-tip="'.$value.'">'.$key.'</div>';
    }

    print'
    </div><!-- /.modal -->';


    print '<div class="modal fade" tabindex="-1" role="dialog" id="modal_el_options">
        <div class="modal_el_options_content">

            <div class="modal_el_options_content_element" id="icon-image-row">
                <label for="icon-image">Icon/Image</label>
                <input type="text" id="icon-image" name="icon-image">
            </div>

            <div class="modal_el_options_content_element" id="custom-text-row">
                <label for="text">Text</label>
                <input type="text" id="custom-text" name="custom-text">
            </div>

            <div class="modal_el_options_content_element">
                <label for="margin-top">Margin Top/Top </label>
                <input type="text" id="margin-top" name="margin-top">
            </div>

            <div class="modal_el_options_content_element">
                <label for="margin-left">Margin Left/Left </label>
                <input type="text" id="margin-left" name="margin-left">
            </div>

            <div class="modal_el_options_content_element">
                <label for="margin-left">Margin Bottom/Bottom </label>
                <input type="text" id="margin-bottom" name="margin-bottom">
            </div>

            <div class="modal_el_options_content_element">
                <label for="margin-right">Margin Right/Right</label>
                <input type="text" id="margin-right" name="margin-right">
            </div>



            <div class="modal_el_options_content_element">
                <label for="position-absolute">Position absolute?</label>
                <input type="checkbox" id="position-absolute" value="1">
            </div>


            <div class="modal_el_options_content_element">
                <label for="font-size">Font Size</label>
                <input type="text" id="font-size" name="font-size">
            </div>

            <div class="modal_el_options_content_element">
                <label for="font-color">Font Color</label>
                <input type="text" id="font-color" name="font-color">
            </div>

            <div class="modal_el_options_content_element">
                <label for="font-color">Back Color</label>
                <input type="text" id="back-color" name="back-color">
            </div>



            <div class="modal_el_options_content_element">
                <label for="padding-top">Padding Top</label>
                <input type="text" id="padding-top" name="padding-top">
            </div>

            <div class="modal_el_options_content_element">
                <label for="padding-left">Padding Left </label>
                <input type="text" id="padding-left" name="padding-left">
            </div>

            <div class="modal_el_options_content_element">
                <label for="padding-bottom">Padding Bottom</label>
                <input type="text" id="padding-bottom" name="padding-bottom">
            </div>

            <div class="modal_el_options_content_element">
                <label for="padding-right">Padding Right</label>
                <input type="text" id="padding-right" name="padding-right">
            </div>

            <div class="modal_el_options_content_element">
                <label for="padding-right">Align</label>
                <select id="text-align" name="text-align">
                    <option value=""></option>
                    <option value="left">left</option>
                    <option value="right">right</option>
                    <option value="center">center</option>
                </select>
            </div>

            <div class="modal_el_options_content_element">
                <label for="extra_css">Extra Css Class</label>
                <input type="text" id="extra_css" name="extra_css">
            </div>

            <input type="button" id="save_custom_el_css" value="apply changes">




        </div>
    <div id="modal_el_options_close">x</div>';

    print'
    </div><!-- /.modal -->';

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
                $this->extension_url . 'field_wpestate_custom_unit_design.js',
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-icon-select-css',
                $this->extension_url . 'field_wpestate_custom_unit_design.css',
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
