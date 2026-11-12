<?php
/** MILLDONE
 * Places card functions
 * src: libs\places_categories_cards\places_categories_cards.php
 * 
 *
* */
                                            

if (!function_exists('wpresidence_display_places_cateogories_list_as_html')):
    /**
     * Display places categories as HTML
     *
     * This function generates HTML for displaying place categories based on various options and attributes.
     *
     * @param array $all_places_array Array of place IDs to display
     * @param array $wpestate_options Theme options
     * @param string $context Context in which the function is called
     * @param array $shortcode_attributes Attributes passed from the shortcode
     * @param array $pagination Pagination settings (currently unused in this function)
     */
    function wpresidence_display_places_cateogories_list_as_html($all_places_array, $wpestate_options = array(), $context = '', $shortcode_attributes = array(), $pagination = array()) {
        // Determine if grid display is enabled
        $display_grid = isset($shortcode_attributes['display_grid']) && $shortcode_attributes['display_grid'] === 'yes';

        // Select place card type based on display mode
        $place_type = $shortcode_attributes['place_type'] ?? 1;
        $place_card_type = wpestate_places_card_selector($place_type, $display_grid ? 1 : 0);

        // Get places unit class based on the number of columns
        $places_class = wpestate_places_unit_column_selector($wpestate_options, $context, $shortcode_attributes);

        $item_height ='';
        if(isset($shortcode_attributes['item_height'])){
            $item_height = $shortcode_attributes['item_height'];
        }
        

        // Open grid wrapper if grid display is enabled
        if ($display_grid) {
            echo '<div class="items_shortcode_wrapper_grid">';
            $places_class['col_class']= '';
            $places_class['col_org']= '';
        }

        // Loop through and display each place
        foreach ($all_places_array as $place_id) {
            $place_id = intval($place_id);
            if ($place_id !== 0) {
                if ($display_grid) {
                    echo '<div class="shortcode_wrapper_grid_item">';
                }

                // Include the appropriate place card template
                include ( locate_template($place_card_type) ) ;
                

                if ($display_grid) {
                    echo '</div>';
                }
            }
        }

        // Close grid wrapper if grid display is enabled
        if ($display_grid) {
            echo '</div>';
        }
    }
endif;




/**
 * WPResidence Theme - Places Card Selector
 *
 * This file contains the wpestate_places_card_selector function, which is responsible
 * for selecting the appropriate template for displaying place cards in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage Widgets
 * @since 1.0.0
 *
 * @uses wpestate_places_card_selector() - Selects the appropriate template based on place type and grid layout
 *
 * @param int $place_type - The type of place card to display (1, 2, 3, or 4)
 * @param int $is_grid - Whether the layout is a grid (1) or not (0)
 *
 * @return string The path to the selected template file
 */

 if ( ! function_exists( 'wpestate_places_card_selector' ) ) :
    /**
     * Selects the appropriate template for displaying place cards
     *
     * This function determines which template file to use based on the place type
     * and whether the layout is a grid or not. It supports both Elementor and
     * non-Elementor templates.
     *
     * @param int $place_type The type of place card (1, 2, 3, or 4)
     * @param int $is_grid Whether the layout is a grid (1) or not (0)
     * @return string The path to the selected template file
     */
    function wpestate_places_card_selector( $place_type, $is_grid = 0 ) {
        // Base path for all templates
        $base_path = 'templates/places_card_templates/';
        
        // Define template names based on place type and grid layout
        $templates = array(
            1 => array(
                0 => 'places_unit.php',
                1 => 'places_unit_elementor.php'
            ),
            2 => array(
                0 => 'places_unit_type2.php',
                1 => 'places_unit_type2_elementor.php'
            ),
            3 => array(
                0 => 'places_unit_type3.php',
                1 => 'places_unit_type3_elementor.php'
            ),
            4 => array(
                0 => 'places_unit_type3_elementor.php',
                1 => 'places_unit_type3_elementor.php'
            )
        );

        $templates = array(
            1 => array(
                0 => 'places_unit.php',
                1 => 'places_unit.php'
            ),
            2 => array(
                0 => 'places_unit_type2.php',
                1 => 'places_unit_type2.php'
            ),
            3 => array(
                0 => 'places_unit_type3.php',
                1 => 'places_unit_type3.php'
            ),
            4 => array(
                0 => 'places_unit_type4.php',
                1 => 'places_unit_type4.php'
            )
        );


        // Ensure place_type is within valid range
        $place_type =intval( $place_type ) ;
        
        // Determine if grid layout is used
        $is_grid = intval( $is_grid ) === 1 ? 1 : 0;

        // Select the appropriate template
        $template = isset( $templates[$place_type][$is_grid] ) ? $templates[$place_type][$is_grid] : $templates[1][0];

        // Return the full path to the selected template
        return $base_path . $template;
    }
endif;





/**
*
*
* Function return the no of columns for blog unit 
*
*/

if(!function_exists('wpestate_places_unit_column_selector')):
    function wpestate_places_unit_column_selector($wpestate_options='',$context='',$shortcode_params=''){
     
        // Get the number of blog listings per row from theme options
        // The second parameter '' provides a default value if the option is not set
        $wpestate_no_listins_per_row = 2;

        if($context=='shortcode'){
            if(isset($shortcode_params['place_per_row'])){
                $wpestate_no_listins_per_row=$shortcode_params['place_per_row'];
            }
        }

        // no more than 6 columns per row
        if($wpestate_no_listins_per_row>6){
            $wpestate_no_listins_per_row=6;
        }
        
     



        $options = array(
            '1'=> array(
                'col_class' =>  'col-md-12',
                'col_org'   =>  12
            ) ,
            
            '2'=> array(
                    'col_class' =>  'col-md-6',
                    'col_org'   =>  6
            ),   

            '3'=> array(
                    'col_class' =>  'col-md-4',
                    'col_org'   =>  4
            ), 
                
            '4'=> array(
                    'col_class' =>  'col-md-3',
                    'col_org'   =>  3
            ), 
            '5'=> array(
                'col_class' =>  'col-md-3',
                'col_org'   =>  3
                ), 
            '6'=> array(
                    'col_class' =>  'col-md-2',
                    'col_org'   =>  2
            ), 

           
        );

        return $options[$wpestate_no_listins_per_row];

    }

endif;
