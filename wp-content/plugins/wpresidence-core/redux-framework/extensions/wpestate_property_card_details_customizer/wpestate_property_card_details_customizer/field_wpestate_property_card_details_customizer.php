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
if( !class_exists( 'ReduxFramework_wpestate_property_card_details_customizer' ) ) {

    /**
     * Main ReduxFramework_wpestate_property_card_details_customizer class
     *
     * @since       1.0.0
     */
    class ReduxFramework_wpestate_property_card_details_customizer {
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

            $name= $this->field['name'] . $this->field['name_suffix'] . '[unit_field_value][]';


            print '<div class="custom_fields_wrapper">';
            while($i< 5 ){
                $property_unit_field_name='';
                if(isset($this->value['property_unit_field_name'][$i])){
                    $property_unit_field_name= $this->value['property_unit_field_name'][$i];
                }

                $property_unit_field_image='';
                $property_unit_field_image_id='';
                $property_unit_field_image_width='';
                $property_unit_field_image_height='';
                $property_unit_field_image_thumbnail='';
                
                if(isset($this->value['property_unit_field_image'][$i])){
                    $property_unit_field_image=$this->value['property_unit_field_image'][$i];
                }

                $property_unit_field_icon='';
                if(isset($this->value['property_unit_field_icon'][$i])){
                    $property_unit_field_icon=$this->value['property_unit_field_icon'][$i];
                }

                $unit_field_value='';
                if(isset($this->value['unit_field_value'][$i])){
                    $unit_field_value=$this->value['unit_field_value'][$i];
                }

                    print'
                        <div class=field_row>
                            <div    class="field_item_unit"><strong>'.__('Label(*if filled will not use icon)','wpresidence-core').'</strong></br>
                            <input  type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[property_unit_field_name][]"  '
                            . '  value="'.$property_unit_field_name.'"></div>';



                            $hide='';
                            $buton_id= $this->field['id'].'-media'.$i;
                            $url_field_id='url_field_'.$i;
                            $preview_field_id='preview_field_'.$i;

                    print '<div    class="field_item_unit" ><strong>'.__('Icon (ex: "fa-solid fa-timer" ) ','wpresidence-core').'</strong></br>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input data-placement="bottomRight"  name="' . $this->field['name'] . $this->field['name_suffix'] . '[property_unit_field_icon][]"   class="form-control" value="'.$property_unit_field_icon.'" type="text"/>
                                      
                                    </div>
                                </div>
                    </div>';
          
                            
                    print'<div class="redux-field-container redux-field redux-container-media redux-container-media-card-details" >       <strong>'.__('Image','wpresidence-core').'</strong></br>';

                    print'<div class="field_item_unit wpestate_redux_custom_upload" data-type="media-custom">
                        <input  type="text" id="'.$url_field_id.'" class="upload large-text " 
                        name="'. $this->field['name'] . $this->field['name_suffix'] . '[property_unit_field_image][]"
                        value="'.$property_unit_field_image.'">';
                       
                        echo '<input type="hidden" class="data" data-mode="image" />';
                        echo '<input type="hidden" class="library-filter"   data-lib-filter="" />';
                        echo '<input type="hidden" class="upload-id "       name="' . $this->field['name'] . $this->field['name_suffix'] . '[property_unit_field_image][id]"        id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][id]"        value="' . $property_unit_field_image_id . '" />';
                        echo '<input type="hidden" class="upload-height"    name="' . $this->field['name'] . $this->field['name_suffix'] . '[property_unit_field_image][height]"    id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][height]"    value="' . $property_unit_field_image_width. '" />';
                        echo '<input type="hidden" class="upload-width"     name="' . $this->field['name'] . $this->field['name_suffix'] . '[property_unit_field_image][width]"     id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][width]"     value="' . $property_unit_field_image_height . '" />';
                        echo '<input type="hidden" class="upload-thumbnail" name="' . $this->field['name'] . $this->field['name_suffix'] . '[property_unit_field_image][thumbnail]" id="' . $this->parent->args['opt_name'] . '[' . $this->field['id'] . '][thumbnail]" value="' . $property_unit_field_image_thumbnail . '" />';


                        echo '<span class="button media_upload_button" id="' .$buton_id. '" data-attr-button-no="'.$i.'">' . __( 'Upload', 'redux-framework' ) . '</span>';
 echo '<img class="redux-option-image wpestate_property_card_details_customizer_preview_img" id="' . $preview_field_id. '" src="' . $property_unit_field_image . '" alt="" target="_blank" rel="external" />';
        

                            // echo '<span style="display:none;" class="button remove-image' . $hide . '" id="reset_' .$buton_id . '" rel="'. $buton_id . '">' . __( 'Remove', 'redux-framework' ) . '</span>';
                      print'</div>';  
                    print'</div>';

                  

              
                    
                    print'
                            <div class="field_item_unit"><strong>'.__('Field','wpresidence-core').'</strong></br>'.redux_wpestate_return_custom_unit_fields($name,$unit_field_value,'_property').'</div>
                    </div>';

                $i++;
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
           
            if ( function_exists( 'wp_enqueue_media' ) ) {
                wp_enqueue_media();
            } else {
                wp_enqueue_script( 'media-upload' );
            }

            wp_enqueue_script(
                'wpestate-redux-field-media-custom-js',
                ReduxFramework::$_url . 'assets/js/media/media-custom' . Redux_Functions::isMin() . '.js',
                array( 'jquery', 'redux-js' ),
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
