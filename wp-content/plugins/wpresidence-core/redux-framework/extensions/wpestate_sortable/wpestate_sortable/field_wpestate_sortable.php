<?php

// Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'ReduxFramework_wpestate_sortable' ) ) {
        class ReduxFramework_wpestate_sortable {
        public $parent ;
        public $field;
        public $value;
            /**
             * Field Constructor.
             * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
             *
             * @since Redux_Options 1.0.0
             */
            function __construct( $field , $value, $parent ) {
              if($field==''){
                $field=array();
            }
                $this->parent = $parent;
                $this->field  = $field;
                $this->value  = $value;
            }

            private function replace_id_with_slug( $arr ) {
                $new_arr = array();
                if ( ! empty( $arr ) ) {
                    foreach ( $arr as $id => $name ) {

                        if ( is_numeric( $id ) ) {
                            $slug = strtolower( $name );
                            $slug = str_replace( ' ', '-', $slug );

                            $new_arr[ $slug ] = $name;
                        } else {
                            $new_arr[ $id ] = $name;
                        }
                    }
                }

                return $new_arr;
            }

            private function is_value_empty( $val ) {
                if ( ! empty( $val ) ) {
                    foreach ( $val as $section => $arr ) {
                        if ( ! empty( $arr ) ) {
                            return false;
                        }
                    }
                }


                return true;
            }

            /**
             * Field Render Function.
             * Takes the vars and outputs the HTML for the field in the settings
             *
             * @since 1.0.0
             */
            function render() {

                if ( ! is_array( $this->value ) && isset( $this->field['options'] ) ) {
                    $this->value = $this->field['options'];
                }

                if ( ! isset( $this->field['args'] ) ) {
                    $this->field['args'] = array();
                }

                if ( isset( $this->field['data'] ) ) {
                    $this->field['options'] = $this->parent->options_defaults[ $this->field['id'] ];
                }

                // Make sure to get list of all the default blocks first
                $all_blocks = ! empty( $this->field['options'] ) ? $this->field['options'] : array();
                $temp       = array(); // holds default blocks
                
               // $temp1=array();print 'added temp1';
                $temp2      = array(); // holds saved blocks

                
                
                foreach ( $all_blocks as $blocks ) {
                    $temp = array_merge( $temp, $blocks );
                }

                $temp = $this->replace_id_with_slug( $temp );

                if ( $this->is_value_empty( $this->value ) ) {
                    if ( ! empty( $this->field['options'] ) ) {
                        $this->value = $this->field['options'];
                    }
                }

                $sortlists = $this->value;
         
                
                if(!isset($sortlists['after'])){
                 $sortlists['after']=array();   
                }
                
                if($this->field['id']  == 'wp_estate_property_page_acc_lay6_order'){
                    if(!isset($sortlists['after_content'])){
                        $sortlists['after_content']=array();   
                    }
                }else{
                      unset($sortlists['after_content']);
                }
                
                
                $moving_temp = $sortlists['disabled'];
                unset($sortlists['disabled']);
                $sortlists['disabled'] = $moving_temp;
                
         
                
                
                $custom_labels=array(
                    'wp_estate_property_page_acc_lay6_order'=>array(
                                                                'enabled'=>esc_html__('First Column','wpresidence-core'),
                                                                'after'=>esc_html__('Second Column','wpresidence-core'),
                                                                'after_content'=>esc_html__('After Columns - Full row','wpresidence-core'),
                                                                'disabled'=>esc_html__('Not Visible','wpresidence-core'),
                                                           ),
                    
                    'wp_estate_property_page_tab_order'=>array(
                                                                'enabled'=>esc_html__('In Tab','wpresidence-core'),
                                                                'after'=>esc_html__('After Tab','wpresidence-core'),
                                                                'after_content'=>esc_html__('After Columns - Full row','wpresidence-core'),
                                                                'disabled'=>esc_html__('Not Visible','wpresidence-core'),
                                                           ),
                );
                
                
                
                
                
                if ( ! empty( $sortlists ) ) {
                    foreach ( $sortlists as $section => $arr ) {
                        $arr = $this->replace_id_with_slug( $arr );
                        $sortlists[ $section ] = $arr;
                        $this->value[$section] = $arr;
                    }
                }

                if ( is_array( $sortlists ) ) {
                    foreach ( $sortlists as $sortlist ) {
                        $temp2 = array_merge( $temp2, $sortlist );
                    }

                    // now let's compare if we have anything missing
                    foreach ( $temp as $k => $v ) {
                        // k = id/slug
                        // v = name

                        if ( ! empty( $temp2 ) ) {
                            if ( ! array_key_exists( $k, $temp2 ) ) {
                                if (isset($sortlists['Disabled'])) {
                                    $sortlists['Disabled'][ $k ] = $v;
                                } else {
                                    $sortlists['disabled'][ $k ] = $v;
                                }
                            }
                        }
                    }

                    // now check if saved blocks has blocks not registered under default blocks
                    foreach ( $sortlists as $key => $sortlist ) {
                        // key = enabled, disabled, backup
                        // sortlist = id => name

                        foreach ( $sortlist as $k => $v ) {
                            // k = id
                            // v = name
                            if ( ! array_key_exists( $k, $temp ) ) {
                                unset( $sortlist[ $k ] );
                            }
                        }
                        $sortlists[ $key ] = $sortlist;
                    }

                    // assuming all sync'ed, now get the correct naming for each block
                    foreach ( $sortlists as $key => $sortlist ) {
                        foreach ( $sortlist as $k => $v ) {
                            $sortlist[ $k ] = $temp[ $k ];
                        }
                        $sortlists[ $key ] = $sortlist;
                    }

                    if ( $sortlists ) {
                        echo '<fieldset id="' . esc_attr($this->field['id']) . '" class="redux-sorter-wpestate-container redux-sorter-wpestate">';

                        foreach ( $sortlists as $group => $sortlist ) {
                            $filled = "";

                            if ( isset( $this->field['limits'][ $group ] ) && count( $sortlist ) >= $this->field['limits'][ $group ] ) {
                                $filled = " filled";
                            }

                            echo '<ul id="' . esc_attr($this->field['id'] . '_' . $group) . '" class="sortlist_' . esc_attr($this->field['id'] . $filled) . '" data-id="' . esc_attr($this->field['id']) . '" data-group-id="' . esc_attr($group) . '">';
                            echo '<h3>';
                            
                            
                            print  $custom_labels[$this->field['id']][$group];
                            
                           
                            
                            print '</h3>';

                            if ( ! isset( $sortlist['placebo'] ) ) {
                                //array_unshift( $sortlist, array( "placebo" => "placebo" ) );
                                $sortlist = array_merge(
                                    array( 'placebo' => 'placebo' ),
                                    $sortlist
                                );
                            }

                            foreach ( $sortlist as $key => $list ) {

                                echo '<input class="sorter-placebo" type="hidden" name="' . esc_attr($this->field['name']) . '[' . $group . '][placebo]' . esc_attr($this->field['name_suffix']) . '" value="placebo">';

                                if ( $key != "placebo" ) {

                                    //echo '<li id="' . $key . '" class="sortee">';
                                    echo '<li id="sortee-' . esc_attr($key) . '" class="sortee" data-id="' . esc_attr($key) . '">';
                                    echo '<input class="position ' . esc_attr($this->field['class']) . '" type="hidden" name="' . esc_attr($this->field['name'] . '[' . $group . '][' . $key . ']' . $this->field['name_suffix']) . '" value="' . esc_attr($list) . '">';
                                    echo esc_html($list);
                                    echo '</li>';
                                }
                            }

                            echo '</ul>';
                        }
                        echo '</fieldset>';
                    }
                }
                
                print '<script type="text/javascript">
                //<![CDATA[
                jQuery(document).ready(function(){
                    wpestate_sorter();
                });
                //]]>
                </script>';
            }

            function enqueue() {
               
                wp_enqueue_style(
                    'redux-field-icon-wpestate_sortable-css',
                    WPESTATE_PLUGIN_URL. '/wpresidence-core/redux-framework/extensions/wpestate_sortable/wpestate_sortable/field_wpestate_sortable.css',
                    time(),
                    true
                );

               
                wp_enqueue_script(
                    'redux-field-icon-wpestate_sortable-js',
                    WPESTATE_PLUGIN_URL. '/wpresidence-core/redux-framework/extensions/wpestate_sortable/wpestate_sortable/field_wpestate_sortable.js',
                    array( 'jquery', 'redux-js', 'jquery-ui-sortable' ),
                    time(),
                    true
                );
            }

            /**
             * Functions to pass data from the PHP to the JS at render time.
             *
             * @return array Params to be saved as a javascript object accessable to the UI.
             * @since  Redux_Framework 3.1.5
             */
            function localize( $field, $value = "" ) {

                $params = array();

                if ( isset( $field['limits'] ) && ! empty( $field['limits'] ) ) {
                    $params['limits'] = $field['limits'];
                }

                if ( empty( $value ) ) {
                    $value = $this->value;
                }
                $params['val'] = $value;

                return $params;
            }
        }
    }
