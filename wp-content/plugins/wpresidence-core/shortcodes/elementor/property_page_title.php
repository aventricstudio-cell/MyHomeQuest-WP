<?php



/**
 * Generate property title for Elementor.
 *
 * This function creates an HTML heading element containing the property title
 * for use with the Elementor page builder on property pages.
 *
 * @since 1.0.0
 * @param array $attributes An array of attributes passed from Elementor.
 * @return string An HTML string containing the property title wrapped in an h1 tag.
 *
 * @uses wpestate_return_property_id_elementor_builder() to get the property ID.
 * @uses get_the_title() to retrieve the property title.
 *
 * @example
 * $title_html = wpestate_estate_property_page_title_section($elementor_attributes);
 */
function wpestate_estate_property_page_title_section($attributes) {
    // Get the property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Retrieve the property title and wrap it in an h1 tag
    return '<h1 class="entry_prop">' . get_the_title($property_id) . '</h1>';
}


/**
 * Generate property breadcrumbs for Elementor.
 *
 * This function is responsible for creating the breadcrumb section
 * on a property page when using the Elementor page builder.
 *
 * @since 1.0.0
 * @param array $attributes An array of attributes passed from Elementor.
 * @return string An empty string, as the output is included directly.
 *
 * @uses wpestate_return_property_id_elementor_builder() to get the property ID.
 * @uses locate_template() to find and include the breadcrumbs template.
 *
 * @example
 * $breadcrumbs = wpestate_estate_property_page_breadcrumb_section($elementor_attributes);
 */
function wpestate_estate_property_page_breadcrumb_section($attributes) {
    // Get the property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Include the breadcrumbs template
    include(locate_template('/templates/listing_templates/property-page-templates/property-page-breadcrumbs.php'));

    // Return an empty string as the output is handled by the included template
    return '';
}


/**
 * Return property price for Elementor in WpResidence theme.
 *
 * This function generates the HTML markup for displaying a property's price
 * or price label, formatted according to the theme's settings. It's designed
 * to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @return string HTML markup for the property price section.
 */

if (!function_exists('wpestate_estate_property_page_price_section')) :

function wpestate_estate_property_page_price_section($attributes) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Fetch property price details
    $price = floatval(get_post_meta($property_id, 'property_price', true));
    $price_label = get_post_meta($property_id, 'property_label', true);
    $price_label_before = get_post_meta($property_id, 'property_label_before', true);

    // Get currency settings
    $wpestate_currency = wpresidence_get_option('wp_estate_currency_symbol', '');
    $where_currency = wpresidence_get_option('wp_estate_where_currency_symbol', '');

    // Determine whether to display price or label
    if ($price != 0) {
        $price_display = wpestate_show_price($property_id, $wpestate_currency, $where_currency, 1);
    } else {
        $price_display = sprintf(
            '<span class="price_label price_label_before">%s</span><span class="price_label">%s</span>',
            esc_html($price_label_before),
            esc_html($price_label)
        );
    }

    // Prepare the HTML output
    $output = sprintf(
        '<div class="price_area elementor-widget-container_price_area">%s</div>',
        wp_kses_post($price_display)
    );

    return $output;
}

endif; // End of function_exists check






/**
 * Return secondary property price information for Elementor in WpResidence theme.
 *
 * This function generates the HTML markup for displaying a property's secondary
 * price or price label, formatted according to the theme's settings. It's designed
 * to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @return string HTML markup for the secondary property price section.
 */

if (!function_exists('wpestate_estate_property_page_price_info_section')) :

function wpestate_estate_property_page_price_info_section($attributes) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Fetch secondary property price details
    $price = floatval(get_post_meta($property_id, 'property_second_price', true));
    $price_label = get_post_meta($property_id, 'property_second_price_label', true);
    $price_label_before = get_post_meta($property_id, 'property_label_before_second_price', true);

    // Get currency settings
    $wpestate_currency = wpresidence_get_option('wp_estate_currency_symbol', '');
    $where_currency = wpresidence_get_option('wp_estate_where_currency_symbol', '');

    // Determine whether to display secondary price or label
    if ($price != 0) {
        $price_display = wpestate_show_price($property_id, $wpestate_currency, $where_currency, 1, "yes");
    } else {
        $price_display = sprintf(
            '<span class="price_label price_label_before">%s</span><span class="price_label">%s</span>',
            esc_html($price_label_before),
            esc_html($price_label)
        );
    }

    // Prepare the HTML output
    $output = sprintf(
        '<div class="price_area elementor-widget-container_price_area">%s</div>',
        wp_kses_post($price_display)
    );

    return $output;
}

endif; // End of function_exists check



/**
 * Return property address for Elementor in WpResidence theme.
 *
 * This function generates the HTML markup for displaying a property's full address,
 * including the street address, city, and area. It's designed to be used with
 * Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @return string HTML markup for the property address section.
 */

if (!function_exists('wpestate_estate_property_page_address_section')) :

function wpestate_estate_property_page_address_section($attributes) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Fetch property address components
    $property_address = get_post_meta($property_id, 'property_address', true);
    $property_city = get_the_term_list($property_id, 'property_city', '', ', ', '');
    $property_area = get_the_term_list($property_id, 'property_area', '', ', ', '');

    // Build the full address string
    $address_components = array_filter([
        esc_html($property_address),
        wp_kses_post($property_city),
        wp_kses_post($property_area)
    ]);
    $property_address_show = implode(', ', $address_components);

    // Prepare the HTML output
    $output = sprintf(
        '<div class="property_categs property_categs_elementor">
            <i class="fas fa-map-marker-alt"></i> %s
        </div>',
        wp_kses_post($property_address_show)
    );

    return $output;
}

endif; // End of function_exists check





/**
 * Return property add to favorites section for Elementor in WpResidence theme.
 *
 * This function generates the HTML markup for displaying the add to favorites,
 * share, and print options for a property listing. It's designed to be used
 * with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @return string HTML markup for the property favorites and social actions section.
 */

if (!function_exists('wpestate_estate_property_page_add_to_favorites_section')) :

function wpestate_estate_property_page_add_to_favorites_section($attributes) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Set display options
    $display_options = array(
        'print'    => 'yes',
        'favorite' => 'yes',
        'share'    => 'yes',
        'address'  => 'yes',
    );

    // Start output buffering
    ob_start();

    // Include the template file
    include(locate_template('templates/listing_templates/property-page-templates/property_header_social_fav_and_print.php'));

    // Get the buffered content and clean the buffer
    $output = ob_get_clean();

    return $output;
}

endif; // End of function_exists check




/**
 * Return property status for Elementor in WpResidence theme.
 *
 * This function retrieves and returns the status of a property. It's designed
 * to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @return string HTML markup for the property status.
 */

 if (!function_exists('wpestate_estate_property_page_status_section')) :

    function wpestate_estate_property_page_status_section($attributes) {
        // Retrieve property ID based on Elementor attributes
        $property_id = wpestate_return_property_id_elementor_builder($attributes);
    
        // Get the property status
        $status = wpestate_return_property_status($property_id, '');
    
        return $status;
    }
    
    endif; // End of function_exists check

?>
