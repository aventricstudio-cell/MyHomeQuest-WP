<?php
/**
 * Generate and display the property description section for Elementor in WpResidence theme.
 *
 * This function retrieves and formats the content of a property post, applies
 * WordPress filters, and outputs the result. It's designed to be used with
 * Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings (unused in current implementation).
 */

if (!function_exists('property_page_elementor_content_section_function')) :

function property_page_elementor_content_section_function2($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Get the property post object
    $property_post = get_post($property_id);

    if ($property_post) {
        // Get the post content
        $content = $property_post->post_content;

        // Apply paragraph formatting
        $content = wpautop($content, false);

        // Apply WordPress content filters
        $content = apply_filters('the_content', $content);

        // Output the formatted content
        echo wp_kses_post($content);
    } else {
        esc_html_e('Property not found.', 'wpresidence-core');
    }
}

endif; // End of function_exists check





function property_page_elementor_content_section_function($attributes, $settings) {
    $property_id = wpestate_return_property_id_elementor_builder($attributes);
   
    if (!$property_id) {
        echo '<p>Property not found.</p>';
        return;
    }
   
    $property_post = get_post($property_id);
   
    if ($property_post && !empty($property_post->post_content)) {
        $content = $property_post->post_content;
        $content = wpautop($content, false);
        
        // Only apply content filters if NOT in edit mode
        if (!isset($attributes['is_elementor_edit'])) {
            $content = apply_filters('the_content', $content);
        }
        
        echo wp_kses_post($content);
    } else {
        echo '<p>No content available for this property.</p>';
    }
}





/**
 * Generate and display the property agent details section (version 2) for Elementor in WpResidence theme.
 *
 * This function includes a template that displays the agent details and contact form in a different layout.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_agent_form_v2_section_function')) :

function property_page_agent_form_v2_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Check if Elementor is active and we're not in edit mode
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    if (is_plugin_active('elementor/elementor.php') && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
        global $post;
    }

    // Get global property page agent sidebar setting
    $enable_global_property_page_agent_sidebar = esc_html(wpresidence_get_option('wp_estate_global_property_page_agent_sidebar', ''));

    // Start output buffering
    ob_start();

    // Include the agent list template
    include(locate_template('/templates/property_list_agent.php'));

    // Get the buffered content and clean the buffer
    $output = ob_get_clean();

    // Wrap the output in a div and echo it
    echo '<div class="elementor_agent_wrapper property_page_agent_form_v2_section">' . $output . '</div>';
}

endif; // End of function_exists check





/**
 * Generate and display the property agent details section for Elementor in WpResidence theme.
 *
 * This function includes a template that displays the agent details and contact form.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

 if (!function_exists('property_page_agent_form_section_function')) :

    function property_page_agent_form_section_function($attributes, $settings) {
        // Retrieve property ID based on Elementor attributes
        $property_id = wpestate_return_property_id_elementor_builder($attributes);
    
        // Set the context for the agent area template
        $property_page_context = 'custom_page_temaplate';
    
        // Include the agent area template
        include(locate_template('/templates/listing_templates/agent_section/agent_area.php'));
    }
    
endif; // End of function_exists check
    





/**
 * Generate and display the property subunits section for Elementor in WpResidence theme.
 *
 * This function handles the display of property subunits or multi-units.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_subunits_section_function')) :

function property_page_subunits_section_function($attributes, $settings) {
    global $property_id, $post;

    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);
   
    print wpestate_property_multi_units_v2($property_id, $attributes);

}

endif; // End of function_exists check



/**
 * Generate and display the similar listings section for Elementor in WpResidence theme.
 *
 * This function includes a template that displays similar property listings.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

 if (!function_exists('property_page_similar_listings_section_function')) :

    function property_page_similar_listings_section_function($attributes, $settings) {
        // Retrieve property ID based on Elementor attributes
        $property_id = wpestate_return_property_id_elementor_builder($attributes);
 
        // Set section title
        $section_title = !empty($attributes['section_title']) ? $attributes['section_title'] : esc_html__('Similar Listings', 'wpresidence-core');
    
        // Set variables for the included template
        $is_tab = '';
    
    
        // Include the similar listings template
        include(locate_template('/templates/listing_templates/property-page-templates/similar_listings.php'));
    }
    
endif; // End of function_exists check










/**
 * Generate and display the property Yelp section for Elementor in WpResidence theme.
 *
 * This function retrieves and displays nearby places using Yelp API.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_yelp_section_function')) :

function property_page_yelp_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Get Yelp API credentials
    $yelp_client_id = wpresidence_get_option('wp_estate_yelp_client_id', '');
    $yelp_client_secret = wpresidence_get_option('wp_estate_yelp_client_secret', '');
    $yelp_client_api_key_2018 = wpresidence_get_option('wp_estate_yelp_client_api_key_2018', '');

    // Set section title
    $section_title = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('What\'s Nearby', 'wpresidence-core');

    // Start output
    ?>
    <div class="panel-group property-panel" id="accordion_yelp">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title" id="prop_ame"><?php echo esc_html($section_title); ?></h4>
            </div>
            <div class="panel-body">
                <?php
                if (!empty($yelp_client_api_key_2018) && !empty($yelp_client_id)) {
                    wpestate_yelp_details($property_id);
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}

endif; // End of function_exists check






/**
 * Generate and display the property reviews section for Elementor in WpResidence theme.
 *
 * This function retrieves and displays property reviews.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_review_section_function')) :

function property_page_review_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Set section title
    $label = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('Property Reviews', 'wpresidence-core');

    // Check if Elementor is active and we're not in edit mode
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    if (is_plugin_active('elementor/elementor.php') && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
        global $post;
    }

    // Set variables for the included template
    $is_tab = '';



    // Include the property reviews template
    // include(locate_template('/templates/listing_templates/property-page-templates/property_reviews.php'));
    get_template_part('templates/reviews/reviews');

 
}

endif; // End of function_exists check










/**
 * Generate and display the property statistics section for Elementor in WpResidence theme.
 *
 * This function displays a chart for property page view statistics.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_statistics_section_function')) :

function property_page_statistics_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Set section title
    $section_title = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('Page Views Statistics', 'wpresidence-core');

    // Output HTML and JavaScript
    ?>
    <div class="panel-group property-panel" id="accordion_prop_stat">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title"><?php echo esc_html($section_title); ?></h4>
            </div>
            <div class="panel-body">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    jQuery(document).ready(function(){
        wpestate_show_stat_accordion();
    });
    </script>
    <?php
}

endif; // End of function_exists check








/**
 * Generate and display the property floor plan section for Elementor in WpResidence theme.
 *
 * This function retrieves the property floor plan details and displays them in a structured format.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_floorplan_section_function')) :

function property_page_floorplan_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Set section title
    $section_title = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('Floor Plans', 'wpresidence-core');

    // Get all property details
    $wpestate_prop_all_details = get_post_custom($property_id);

    // Get floor plan titles
    $plan_title_array = unserialize(wpestate_return_custom_field($wpestate_prop_all_details, 'plan_title'));

    // Only display the section if there are floor plans
    if (is_array($plan_title_array) && !empty($plan_title_array)) {
        ?>
        <div class="panel-group property-panel" id="accordion_prop_floor_plans">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title" id="prop_ame"><?php echo esc_html($section_title); ?></h4>
                </div>
                <div class="panel-body">
                    <?php estate_floor_plan($property_id, 0, $wpestate_prop_all_details); ?>
                </div>
            </div>
        </div>
        <?php
    }
}

endif; // End of function_exists check








/**
 * Generate and display the property payment calculator section for Elementor in WpResidence theme.
 *
 * This function displays a mortgage calculator for the property.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_calculator_section_function')) :

function property_page_calculator_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Set section title
    $section_title = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('Payment Calculator', 'wpresidence-core');

    // Get all property details
    $wpestate_prop_all_details = get_post_custom($property_id);

    // Start output
    ?>
    <div class="panel-group property-panel" id="accordion_morgage">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title" id="prop_morg"><?php echo esc_html($section_title); ?></h4>
            </div>
            <div class="panel-body">
                <?php wpestate_morgage_calculator($property_id, $wpestate_prop_all_details); ?>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    jQuery(document).ready(function(){
        wpestate_show_morg_pie();
    });
    </script>
    <?php
}

endif; // End of function_exists check

/**
 * Generate and display the property WalkScore section for Elementor in WpResidence theme.
 *
 * This function retrieves the property WalkScore details and displays them in a structured format.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_walkscore_section_function')) :

function property_page_walkscore_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Set section title
    $section_title = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('WalkScore', 'wpresidence-core');

    // Get WalkScore API key
    $walkscore_api = esc_html(wpresidence_get_option('wp_estate_walkscore_api', ''));

    // Get all property details
    $wpestate_prop_all_details = get_post_custom($property_id);

    // Start output
    ?>
    <div class="panel-group property-panel" id="accordion_walkscore">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title"><?php echo esc_html($section_title); ?></h4>
            </div>
            <div class="panel-body">
                <?php
                if (!empty($walkscore_api)) {
                    echo wp_kses_post(wpestate_walkscore_details($property_id, $wpestate_prop_all_details));
                } else {
                    esc_html_e('Please add a Walkscore Api Key', 'wpresidence-core');
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}

endif; // End of function_exists check


/**
 * Generate and display the property virtual tour section for Elementor in WpResidence theme.
 *
 * This function retrieves the property virtual tour details and displays them in a structured format.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_virtual_tour_section_function')) :

function property_page_virtual_tour_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Set section title
    $section_title = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('Virtual Tour', 'wpresidence-core');

    // Get virtual tour code
    $virtual_tour_code = get_post_meta($property_id, 'embed_virtual_tour', true);

    // Only display the section if there's a virtual tour
    if (!empty($virtual_tour_code)) {
        ?>
        <div class="panel-group property-panel" id="accordion_virtual_tour">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title" id="prop_virtual"><?php echo esc_html($section_title); ?></h4>
                </div>
                <div class="panel-body">
                    <?php    print (  wpestate_sanitize_iframe_html( get_post_meta($property_id, 'embed_virtual_tour', true)) );  ?>
                </div>
            </div>
        </div>
        <?php
    }
}

endif; // End of function_exists check







/**
 * Generate and display the property map section for Elementor in WpResidence theme.
 *
 * This function displays a map for the property using a shortcode.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_map_section_function')) :

function property_page_map_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Set section title
    $section_title = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('Map', 'wpresidence-core');

    // Generate map content
    $map_content = do_shortcode('[property_page_map propertyid="' . esc_attr($property_id) . '"][/property_page_map]');

    // Output the HTML
    ?>
    <div class="panel-group property-panel" id="accordion_prop_map">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title" id="prop_ame"><?php echo esc_html($section_title); ?></h4>
            </div>
            <div class="panel-body">
                <?php echo wp_kses_post($map_content); ?>
            </div>
        </div>
    </div>
    <?php
}

endif; // End of function_exists check


/**
 * Generate and display the property video section for Elementor in WpResidence theme.
 *
 * This function retrieves the property video details and displays them in a structured format.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_video_section_function')) :

function property_page_video_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Get all property details
    $wpestate_prop_all_details = get_post_custom($property_id);

    // Set section title
    $section_title = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('Video', 'wpresidence-core');

    // Get embed video ID
    $embed_video_id = get_post_meta($property_id, 'embed_video_id', true);

    // Only display the section if there's a video
    if (!empty($embed_video_id)) {
        // Generate video content
        $video_content = wpestate_listing_video($property_id, $wpestate_prop_all_details);

        // Output the HTML
        ?>
        <div class="panel-group property-panel" id="accordion_video">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title" id="prop_video"><?php echo esc_html($section_title); ?></h4>
                </div>
                <div class="panel-body">
                    <?php echo wp_kses_post($video_content); ?>
                </div>
            </div>
        </div>
        <?php
    }
}

endif; // End of function_exists check


/**
 * Generate and display the property features section for Elementor in WpResidence theme.
 *
 * This function retrieves the property features and amenities and displays them in a structured format.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_features_section_function')) :

function property_page_features_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Get all property details
    $wpestate_prop_all_details = get_post_custom($property_id);

    // Set section title
    $section_title = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('Amenities and Features', 'wpresidence-core');

    // Get number of columns from settings
    $no_columns = isset($settings['no_colums']['size']) ? intval($settings['no_colums']['size']) : 1;

    // Get property features
    $property_features = get_the_terms($property_id, 'property_features');

    // Only display the section if there are features
    if ($property_features) {
        // Generate features content
        $features_content = estate_listing_features($property_id, $no_columns, 0, '', $wpestate_prop_all_details);

        // Output the HTML
        ?>
        <div class="panel-group property-panel" id="accordion_prop_features">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title" id="prop_ame"><?php echo esc_html($section_title); ?></h4>
                </div>
                <div class="panel-body">
                    <?php echo trim($features_content); ?>
                </div>
            </div>
        </div>
        <?php
    }
}

endif; // End of function_exists check




/**
 * Generate and display the property details section for Elementor in WpResidence theme.
 *
 * This function retrieves the property details and displays them in a structured format.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_details_section_function')) :

function property_page_details_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Get all property details
    $wpestate_prop_all_details = get_post_custom($property_id);

    // Set section title
    $section_title = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('Property Details', 'wpresidence-core');

    // Get number of columns from settings
    $no_columns = isset($settings['no_colums']['size']) ? intval($settings['no_colums']['size']) : 1;

    // Generate details content
    $details_content = estate_listing_details($property_id, $wpestate_prop_all_details, $no_columns);

    // Prepare the HTML output
    $output = sprintf(
        '<div class="panel-group property-panel" id="accordion_prop_details">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title" id="prop_det">%s</h4>
                </div>
                <div class="panel-body">
                    %s
                </div>
            </div>
        </div>',
        esc_html($section_title),
        wp_kses_post($details_content)
    );

    echo $output;
}

endif; // End of function_exists check




/**
 * Generate and display the property address section for Elementor in WpResidence theme.
 *
 * This function retrieves the property address details and displays them in a structured format.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_address_section_function')) :

function property_page_address_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Get all property details
    $wpestate_prop_all_details = get_post_custom($property_id);

    // Set section title
    $section_title = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('Property Address', 'wpresidence-core');

    // Get number of columns from settings
    $no_columns = isset($settings['no_colums']['size']) ? intval($settings['no_colums']['size']) : 1;

    // Generate address content
    $address_content = estate_listing_address($property_id, $wpestate_prop_all_details, $no_columns);

    // Prepare the HTML output
    $output = sprintf(
        '<div class="panel-group property-panel" id="accordion_prop_addr">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">%s</h4>
                </div>
                <div class="panel-body">
                    %s
                </div>
            </div>
        </div>',
        esc_html($section_title),
        wp_kses_post($address_content)
    );

    echo $output;
}

endif; // End of function_exists check








/**
 * Generate and display the property description section for Elementor in WpResidence theme.
 *
 * This function retrieves the property description, energy information, and related documents.
 * It's designed to be used with Elementor in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyElements
 * @since 1.0.0
 *
 * @param array $attributes Elementor widget attributes.
 * @param array $settings   Elementor widget settings.
 */

if (!function_exists('property_page_description_section_function')) :

function property_page_description_section_function($attributes, $settings) {
    // Retrieve property ID based on Elementor attributes
    $property_id = wpestate_return_property_id_elementor_builder($attributes);

    // Get property content
    $property_post = get_post($property_id);
    $content = $property_post->post_content;
    $content = wpautop($content, false);

    // Apply content filters if not in Elementor edit mode
    if (!(isset($attributes['is_elementor_edit']) && intval($attributes['is_elementor_edit']) == 1)) {
        $content = apply_filters('the_content', $content);
    }

    // Set section title
    $section_title = !empty($settings['section_title']) ? $settings['section_title'] : esc_html__('Description', 'wpresidence-core');

    // Start output buffering
    ob_start();
    ?>

    <div class="wpestate_property_description" id="wpestate_property_description_section">
        <h4 class="panel-title"><?php echo esc_html($section_title); ?></h4>
        <?php echo wp_kses_post($content); ?>

        <?php
        // Display energy saving info if available
        $energy_index = get_post_meta($property_id, 'energy_index', true);
        $energy_class = get_post_meta($property_id, 'energy_class', true);
        if (!empty($energy_index) || !empty($energy_class)) :
        ?>
            <div class="property_energy_saving_info">
                <?php echo wpestate_energy_save_features($property_id); ?>
            </div>
        <?php
        endif;

        // Display property documents
        echo wpestare_return_documents($property_id);
        ?>
    </div>

    <?php
    $output = ob_get_clean();
    echo $output;
}

endif; // End of function_exists check
?>
