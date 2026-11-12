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
if( !class_exists( 'ReduxFramework_wpestate_custom_fields_list' ) ) {

    /**
     * Main ReduxFramework_wpestate_custom_fields_list class
     *
     * @since       1.0.0
     */
    class ReduxFramework_wpestate_custom_fields_list {
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
        function __construct( $field, $value , $parent ) {


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
        

        $current_fields = '';
        $saved_field_names = array(); // Track which fields are already saved
        $acf_enabled = intval( wpresidence_get_option('wpestate_show_acf_fields', 1) );
        
        // STEP 1: First render all saved fields (both custom and ACF)
        if (!empty($this->value) && isset($this->value['add_field_name'])) {
            foreach ($this->value['add_field_name'] as $i => $field_name) {
                if ($field_name != '') {
                    // Track this field name as already saved
                    $saved_field_names[] = $field_name;
                    
                    $field_label = isset($this->value['add_field_label'][$i]) ? $this->value['add_field_label'][$i] : '';
                    $field_order = isset($this->value['add_field_order'][$i]) ? $this->value['add_field_order'][$i] : '';
                    $field_type = isset($this->value['add_field_type'][$i]) ? $this->value['add_field_type'][$i] : '';
                    $dropdown_values = isset($this->value['add_dropdown_order'][$i]) ? $this->value['add_dropdown_order'][$i] : '';
                    
                    // Check if this is an ACF field by trying to get ACF field data
                    $is_acf_field = false;
                    $acf_field_data = null;

                    if (function_exists('get_field')) {
                        $acf_field_data = acf_get_field($field_name);
                        if ($acf_field_data) {
                            $is_acf_field = true;
                        }
                    }

                    if ( !$acf_enabled && $is_acf_field ) {
                        continue;
                    }

                    if ($is_acf_field) {
                        // Render as ACF field (readonly)
                        $current_fields .= '
                        <div class="field_row acf_field_row">
                            <div class="field_item"><strong>'.esc_html__('ACF Field Name', 'wpresidence-core').'</strong></br>'
                                . '<input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_name][]" value="'.esc_attr($field_name).'" readonly></div>
                            <div class="field_item"><strong>'.esc_html__('ACF Field Label', 'wpresidence-core').'</strong></br>'
                                . '<input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_label][]" value="'.esc_attr($field_label).'" readonly></div>
                            <div class="field_item"><strong>'.esc_html__('Field Order', 'wpresidence-core').'</strong></br>'
                                . '<input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_order][]" value="'.esc_attr($field_order).'" ></div>
                            <div class="field_item"><strong>'.esc_html__('ACF Field Type', 'wpresidence-core').'</strong></br>'
                                . '<input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_type][]" value="'.esc_attr($field_type).'" readonly></div>
                            <div class="field_item">'
                                . '<input type="hidden" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_dropdown_order][]" value="'.esc_attr($dropdown_values).'"></div>
                            <div class="field_item"></div>
                        </div>';
                    } else {
                        // Render as custom field (editable)
                        $name_drop = $this->field['name'] . $this->field['name_suffix'] . '[add_field_type][]';
                        
                        $current_fields .= '
                        <div class="field_row custom_field_row">
                            <div class="field_item"><strong>'.__('Field Name','wpresidence-core').'</strong></br>'
                                . '<input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_name][]" value="'.stripslashes($field_name).'" ></div>
                            <div class="field_item"><strong>'.__('Field Label','wpresidence-core').'</strong></br>'
                                . '<input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_label][]" value="'.stripslashes($field_label).'" ></div>
                            <div class="field_item"><strong>'.__('Field Order','wpresidence-core').'</strong></br>'
                                . '<input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_order][]" value="'.esc_attr($field_order).'"></div>
                            <div class="field_item"><strong>'.__('Field Type','wpresidence-core').'</strong></br>'
                                . wpestate_fields_type_select_redux($name_drop, $field_type) . '</div>
                            <div class="field_item"><strong>'.__('Dropdown values','wpresidence-core').'</strong></br>'
                                . '<textarea name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_dropdown_order][]">' . stripslashes($dropdown_values) . '</textarea></div>
                            <a class="deletefieldlink button secondary" href="#">'.esc_html__('delete','wpresidence-core').'</a>
                        </div>';
                    }
                }
            }
        }
        
        // STEP 2: Now check for NEW ACF fields that are not already saved
        if (function_exists('get_field') && $acf_enabled) {
            $groups = acf_get_field_groups(array('post_type' => 'estate_property'));
            
            if (!empty($groups)) {
                foreach ($groups as $group) {
                    $fields = acf_get_fields($group);
                    if (!empty($fields)) {
                        foreach ($fields as $field) {
                            if (isset($field['name'])) {
                                // Check if this ACF field is NOT already in saved fields
                                if (!in_array($field['name'], $saved_field_names)) {
                                    // This is a NEW ACF field, render it with default order 0
                                    $current_fields .= '
                                    <div class="field_row acf_field_row">
                                        <div class="field_item"><strong>'.esc_html__('ACF Field Name', 'wpresidence-core').'</strong></br>'
                                            . '<input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_name][]" value="'.esc_attr($field['name']).'" readonly></div>
                                        <div class="field_item"><strong>'.esc_html__('ACF Field Label', 'wpresidence-core').'</strong></br>'
                                            . '<input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_label][]" value="'.esc_attr($field['label']).'" readonly></div>
                                        <div class="field_item"><strong>'.esc_html__('Field Order', 'wpresidence-core').'</strong></br>'
                                            . '<input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_order][]" value="0" ></div>
                                        <div class="field_item"><strong>'.esc_html__('ACF Field Type', 'wpresidence-core').'</strong></br>'
                                            . '<input type="text" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_field_type][]" value="'.esc_attr($field['type']).'" readonly></div>
                                        <div class="field_item"><strong>'.__('Dropdown values','wpresidence-core').'</strong></br>'
                                            . '<input type="hidden" name="' . $this->field['name'] . $this->field['name_suffix'] . '[add_dropdown_order][]" value=""></div>
                                        <div class="field_item"></div>
                                    </div>';
                                }
                            }
                        }
                    }
                }
            }
        }
        
        print '
        <div id="custom_fields_wrapper">
            '.$current_fields.'
        </div>';

        print '
        <div class="add_curency">
            <div class="cur_explanations">' . __('Field name', 'wpresidence-core') . '</div>
            <input type="text" id="field_name" name="field_name" value="" size="40"/>

            <div class="cur_explanations">' . __('Field Label', 'wpresidence-core') . '</div>
            <input type="text" id="field_label" name="field_label" value="" size="40" />

            <div class="cur_explanations">' . __('Field Type', 'wpresidence-core') . '</div>
            <select id="field_type" name="field_type">
                <option value="short text">short text</option>
                <option value="long text">long text</option>
                <option value="numeric">numeric</option>
                <option value="date">date</option>
                <option value="dropdown">dropdown</option>
            </select>

            <div class="cur_explanations">' . __('Dropdown values separated by "," (only for dropdown field type)','wpresidence-core') . '</div>
            <textarea cols="45" id="drodown_values" name="drodown_values"></textarea>

            <div class="cur_explanations">'.__(' Order in listing page','wpresidence-core').'</div>
            <input type="text" id="field_order" name="field_order" value="" size="40" />

        </div>
        <a href="#" class="button button-primary secondary " id="add_field2">' . __('Click To Add Field', 'wpresidence-core') . '</a>';
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
                'redux-field-wpestate-custom-fields-list-js',
                $this->extension_url . 'field_wpestate_custom_fields_list.js',
                array( 'jquery', 'jquery-ui-sortable' ),
                time(),
                true
            );

            wp_enqueue_style(
                'redux-field-wpestate-custom-fields-list-css',
                $this->extension_url . 'field_wpestate_custom_fields_list.css',
                array(),
                time()
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
