<?php
/**
 * Generate and display the property overview section for Elementor in WpResidence theme.
 *
 * This function retrieves property details and displays an overview section
 * with customizable fields. It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_show_overview_section_function')) :

function property_show_overview_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Define default SVG icons
    $default_svg = array(
        'property_bedrooms' => 'single_bedrooms.svg',
        'property_bathrooms' => 'single_bath.svg',
        'property_size' => 'single_floor_plan.svg',
        'property_year' => 'single_calendar.svg',
        'property_garage' => 'single_garage.svg',
        'property-garage' => 'single_garage.svg',
    );

    // Get currency settings
    $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
    $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));

    // Set section title
    $section_title = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('Overview', 'wpresidence-core');

    // Start output buffering
    ob_start();
    ?>

    <div class="single-overview-section panel-group property-panel">
        <h4 class="panel-title" id=""><?php echo esc_html($section_title); ?></h4>

        <ul class="overview_element overview_updatd_on">
            <li class="first_overview"><?php esc_html_e('Updated On:', 'wpresidence-core'); ?></li>
            <li class="first_overview_date"><?php echo get_the_modified_date('F j, Y', $property_id); ?></li>
        </ul>

        <?php
        foreach ($settings['overview_fields'] as $item) {
            $item_value = get_post_meta($property_id, $item['field_type'], true);
            
            if (empty($item['field_type'])) {
                continue; // Skip this iteration if field_type is blank
            }
            
            
            // Process special field types
            switch ($item['field_type']) {
                
                case 'property_size':
                case 'property_lot_size':
                    $item_value = wpestate_get_converted_measure($property_id, $item['field_type']);
                    break;
                case 'property_price':
                    $item_value = wpestate_show_price($property_id, $wpestate_currency, $where_currency, 1);
                    break;
                case 'property_id':
                    $item_value = $property_id;
                    break;
                case 'property_year':
                    $item_value = esc_html__('Year Built:', 'wpresidence-core') . ' ' . esc_html(get_post_meta($property_id, 'property-year', true));
                    break;
                case 'property_status':
                case 'property_city':
                case 'property_area':
                case 'property_county_state':
                case 'property_category':
                case 'property_action_category':
                    $item_value = get_the_term_list($property_id, $item['field_type'], '', ', ', '');
                    break;
                case 'property_internal_id':
                    $item_value = get_post_meta($property_id, 'property_internal_id', true);
                    break;
            }

            $label = (is_numeric($item_value) && $item_value == 1) || empty($item['label_plural']) ? $item['label_singular'] : $item['label_plural'];

            ?>

            <?php 
            if($item_value != '' && $item_value !== esc_html__('Not Available','wpresidence-core')  ){
            ?>
                <ul class="overview_element">
                    <?php if ($item['icon_type'] !== 'none'): ?>
                        <li class="first_overview">
                            <?php
                            if ($item['icon_type'] === 'theme_options') {
                                if ( isset( $default_svg[$item['field_type']]) ) {
                                    include(locate_template('templates/svg_icons/' . $default_svg[$item['field_type']]));
                                }
                            } elseif ($item['icon_type'] === 'custom') {
                                if ($item['meta_icon']['library'] === 'svg') {
                                    echo '<img src="' . esc_url($item['meta_icon']['value']['url']) . '" alt="' . esc_attr($item['field_type']) . '">';
                                } else {
                                    echo '<i class="' . esc_attr($item['meta_icon']['value']) . '"></i>';
                                }
                            }
                            ?>
                        </li>
                    <?php endif; ?>
                        
                        
                    <li><?php 
                    
                 
                    echo wp_kses_post(is_array($item_value) ? implode(', ', $item_value) : $item_value) . ' ' . esc_html($label); 

                  // echo ($item_value) . ' ' . esc_html($label); ?></li>
                </ul>
        <?php 
            }//end check if value is empty
        } // end foreach
        ?>
    </div>

    <?php
    $output = ob_get_clean();
    echo $output;
}

endif; // End of function_exists check