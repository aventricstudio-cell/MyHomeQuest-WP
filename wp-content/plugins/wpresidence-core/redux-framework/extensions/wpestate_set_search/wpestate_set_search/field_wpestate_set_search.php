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
if( !class_exists( 'ReduxFramework_wpestate_set_search' ) ) {

    /**
     * Main ReduxFramework_wpestate_set_search class
     *
     * @since       1.0.0
     */
    class ReduxFramework_wpestate_set_search {
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

            $i=0;
            $j=0;
            $adv_search_fields_no               =   floatval( wpresidence_get_option('wp_estate_adv_search_fields_no') );
            $adv_search_what                    =   wpresidence_get_option('wp_estate_adv_search_what','');
            $adv_search_how                     =   wpresidence_get_option('wp_estate_adv_search_how','');
            $adv_search_label                   =   wpresidence_get_option('wp_estate_adv_search_label','');

            $tab_tax    = wpresidence_get_option('wp_estate_adv6_taxonomy','');
            $tab_terms  = wpresidence_get_option('wp_estate_adv6_taxonomy_terms','');
            $wp_estate_adv_search_type= wpresidence_get_option('wp_estate_adv_search_type','');

            $exclude_array=array('1','2','3','4','5','8','10','11','7-obsolete','9-obsolete');

            if(in_array($wp_estate_adv_search_type, $exclude_array)){
                $tab_style='no';
            }else{
                $tab_style='yes';
            }





            $name= $this->field['name'] . $this->field['name_suffix'] . '[unit_field_value][]';


            print '<div class="custom_fields_wrapper">';
            print '<div class="field_row">
            <div class="field_item"><strong>'.__('Place in advanced search form','wpresidence-core').'</strong></div>
            <div class="field_item"><strong>'.__('Search field','wpresidence-core').'</strong></div>
            <div class="field_item"><strong>'.__('How it will compare','wpresidence-core').'</strong></div>
            <div class="field_item"><strong>'.__('Label on Front end','wpresidence-core').'</strong></div>
            </div>';

            if($tab_style=='no'){
                $tab_terms=array('1');
            }
            if(is_array($tab_terms)):
                foreach ($tab_terms as $key=>$tab_value):
                        if($tab_style=='yes'){
                            $tabterm=get_term_by('id', $tab_value, $tab_tax);
                            $name='';
                            if(isset($tabterm->name)){
                                $name=$tabterm->name;
                            }
                            print '<div class="field_row"><strong>'.__('Search form for','wpresidence-core').' '.$name.'</strong></div>';
                        }

                            while($i< $adv_search_fields_no ){
                                $i++;
                                $j++;

                                print '<div class="field_row">
                                        <div class="field_item"> '.__('Spot no ','wpresidence-core').($i).'</div>';

                                        print '
                                        <div class="field_item">
                                            <select id="adv_search_what'.$i.'" name="' . $this->field['name'] . $this->field['name_suffix'] . '[adv_search_what][]"  >';
                                                print wpestate_show_advanced_search_options($j-1,$adv_search_what);
                                            print'</select>
                                        </div>';


                                        print '<div class="field_item">
                                            <select id="adv_search_how'.$i.'"  name="' . $this->field['name'] . $this->field['name_suffix'] . '[adv_search_how][]"  >';
                                                $to_send_how='';
                                                if(isset($this->value['adv_search_how'][$j])){
                                                    $to_send_how=$this->value['adv_search_how'][$j];
                                                }
                                                print    wpestate_show_advanced_search_how($j-1,$adv_search_how);;
                                            print '</select>
                                        </div>';


                                        $new_val='';
                                        if( isset($adv_search_label[$j-1]) ){
                                            $new_val=$adv_search_label[$j-1];
                                        }
                                        print '<div class="field_item"><input type="text" id="adv_search_label'.$i.'"  name="' . $this->field['name'] . $this->field['name_suffix'] . '[adv_search_label][]"  value="'.$new_val.'"></div>';
                                print '</div>';
                            }

                        $i=0;
                endforeach;
            endif;
            print'</div>';

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
                $this->extension_url . 'field_wpestate_set_search.js',
                array( 'jquery' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-icon-select-css',
                $this->extension_url . 'field_wpestate_set_search.css',
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
