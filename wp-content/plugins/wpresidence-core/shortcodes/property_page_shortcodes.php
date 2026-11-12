<?php

/**
 * Include the template for displaying other agents associated with a property
 *
 * This function sets up the necessary variables and includes the template
 * for displaying other agents in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyAgents
 * @since 1.0.0
 */

if (!function_exists('wpestate_property_design_other_agents')) :
/**
 * Set up and include the template for other agents
 *
 * @param array $attributes Shortcode attributes
 * @param string|null $content Shortcode content (unused)
 * @param string $columns Column layout (unused)
 */
function wpestate_property_design_other_agents($attributes, $content = null, $columns = '') {
    // Initialize variables
    $propertyID = 0;
    $is_tab = '';
    $label = '';
    $property_page_context = 'custom_page_temaplate';

    // Handle Elementor compatibility
    if (isset($attributes['is_elementor']) && intval($attributes['is_elementor']) == 1) {
    $propertyID =   $property_id = wpestate_return_property_id_elementor_builder($attributes);
    }else{
        global $propid;
        $propertyID = $propid;
    }


    // Get WpEstate options
    $wpestate_options = get_query_var('wpestate_options');

    // Include the template file
    $template_path = locate_template('/templates/listing_templates/other_agents.php');
    if ($template_path) {
        include($template_path);
    } else {
        // Template not found, handle the error (e.g., log it or display a message)
       // error_log('Template not found: /templates/listing_templates/other_agents.php');
    }
}
endif;





/**
 * Masonry Gallery Functions for WPBakery Page Builder in WpResidence theme.
 *
 * These functions generate and return the HTML for different types of masonry
 * galleries used in property listings. They're designed to be used with
 * WPBakery Page Builder in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyGallery
 * @since 1.0.0
 */

 if (!function_exists('wpestate_estate_property_design_masonary_gallery')) :
    /**
     * Generate and return the HTML for a Type 2 masonry gallery.
     *
     * @param array  $attributes The shortcode attributes (unused in current implementation).
     * @param string $content    The enclosed content (unused in current implementation).
     * @return string The HTML markup for the Type 2 masonry gallery.
     */
    function wpestate_estate_property_design_masonary_gallery($attributes, $content = null) {
        global $propid;

        ob_start();
        wpestate_header_masonry_gallery_type2($propid);
        $gallery_html = ob_get_clean();

        return $gallery_html;
    }
endif;

if (!function_exists('wpestate_estate_property_design_masonary_gallery_2')) :
    /**
     * Generate and return the HTML for a standard masonry gallery.
     *
     * @param array  $attributes The shortcode attributes (unused in current implementation).
     * @param string $content    The enclosed content (unused in current implementation).
     * @return string The HTML markup for the standard masonry gallery.
     */
    function wpestate_estate_property_design_masonary_gallery_2($attributes, $content = null) {
        global $propid;

        ob_start();
        wpestate_header_masonry_gallery($propid);
        $gallery_html = ob_get_clean();

        return $gallery_html;
    }
endif;






/**
 * Generate property image gallery
 *
 * This function creates an image gallery for a property in the WpResidence theme.
 * It uses the wpestate_generate_property_slider_image_ids function to get image IDs.
 *
 * @package WpResidence
 * @subpackage PropertyGallery
 * @since 1.0.0
 */

if (!function_exists('wpestate_estate_property_design_gallery')) :
/**
 * Generate a property image gallery
 *
 * @param array $attributes Shortcode attributes
 * @param string|null $content Shortcode content (unused)
 * @return string HTML markup for the property image gallery
 */
function wpestate_estate_property_design_gallery($attributes, $content = null) {
    // Parse shortcode attributes
    $attributes = shortcode_atts(array(
        'css'          => '',
        // 'maxwidth'     => '200',
        'margin'       => '10',
        'image_no'     => '4',
        'is_elementor' => '1',
        'rownumber'   => '3',
        'rownumber_tablet' => '2',
        'rownumber_mobile' => '1',
        'id' => 0
    ), $attributes);

    // Handle Elementor compatibility
    $propid = isset($attributes['is_elementor']) && intval($attributes['is_elementor']) == 1 
        ? wpestate_return_elementor_id() 
        : get_the_ID();

    if (isset( $attributes['id'] ) && !empty( $attributes['id'] )) {
        $propid = intval($attributes['id']);
    }

    // Generate CSS class
    $css_class = '';
    if (function_exists('vc_shortcode_custom_css_class')) {
        $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($attributes['css'], ' '), '', $attributes);
    }

    // Prepare gallery parameters
    $image_no = intval($attributes['image_no']);
    $margin = intval($attributes['margin']);
    // $maxwidth = intval($attributes['maxwidth']);
    $rownumber = intval($attributes['rownumber']);
    $rownumberTablet = intval($attributes['rownumber_tablet']);

    $rownumberMobile = intval($attributes['rownumber_mobile']);

    $style = '<style>
    .wpestate_property_gallery_item {
        flex: 0 0 calc( (100% - (' . $margin . 'px * (' . $rownumber . ' - 1))) / ' . $rownumber . ' );
    }
    @media (min-width: 768px) and (max-width: 1024px) {
        .wpestate_property_gallery_item {
            flex: 0 0 calc( (100% - (' . $margin . 'px * (' . $rownumberTablet . ' - 1))) / ' . $rownumberTablet . ' );
        }
    }
    @media (max-width: 767px) {
        .wpestate_property_gallery_item {
            flex: 0 0 calc( (100% - (' . $margin . 'px * (' . $rownumberMobile . ' - 1))) / ' . $rownumberMobile . ' );
        }
    }
    </style>';

    // Get image IDs
    $attachment_ids = wpestate_generate_property_slider_image_ids($propid, true);

    // Start building gallery HTML
    $return_string = $style . sprintf(
        '<div class="wpestate_estate_property_design_gallery %s" style="gap: %dpx;">',
        esc_attr($css_class),
        $margin,
        // $margin / 2
    );

    $totalImages = count($attachment_ids);

    if ( count( $attachment_ids ) < $image_no ) {
        $image_no = count( $attachment_ids );
    }

    // Generate gallery items
    $counter_lightbox = 0;
    $leftImages = array();
    foreach ($attachment_ids as $attachment_id) {
        $counter_lightbox++;
        $preview = wp_get_attachment_image_src($attachment_id, 'property_listings');
        $full_prty = wp_get_attachment_image_src($attachment_id, 'full');
        $attachment = get_post($attachment_id);

        if ($preview && $full_prty) {
            $return_string .= wpestate_generate_gallery_item(
                $full_prty[0],
                $preview[0],
                $attachment->post_excerpt,
                $counter_lightbox,
                $margin,
                $rownumber,
                $totalImages
            );
        }

        // Break the loop if we've reached the desired number of images
        if ($counter_lightbox >= $image_no) {
            $leftImages = array_slice($attachment_ids, $counter_lightbox);
            break;
        }
    }

    
    // var_dump( $return_string );

    $return_string .= '</div>';
    if ( $image_no < $totalImages ) {
        $return_string .= '<div class="restofimages" width="0" height="0" style="width: 0px;height: 0px;display: none;">';
        foreach ( $leftImages as $attachment_id ) {
            $counter_lightbox++;
            $preview = wp_get_attachment_image_src($attachment_id, 'property_listings');
            $full_prty = wp_get_attachment_image_src($attachment_id, 'full');
            $attachment = get_post($attachment_id);

            if ($preview && $full_prty) {
                $return_string .= '<a href="'.$full_prty[0].'" data-slider-no="' . $counter_lightbox . '" class="lightbox_trigger" data-elementor-lightbox-slideshow="property-gallery-widf" title="'.$attachment->post_excerpt.'"></a>';
            }
        }
        $return_string .= '</div>';
    }
    $allowed = wp_kses_allowed_html('post');
    $allowed['style'] = array(
        'type' => true,
        'media' => true,
        'href' => true,
        'rel' => true,
        'style' => true,
    );
    $allowed['div'] = array(
        'class' => true,
        'style' => true,
        'id' => true,
    );
    // $allowed['script'] = array(
    //     'src' => true,
    //     'type' => true,
    // );
    add_filter('wp_kses_allowed_html','wpestate_allow_script_tags', 50);
    return $return_string;
    
}
endif;

/**
 * Generate a single gallery item HTML
 *
 * @param string $full_image_url URL of the full-size image
 * @param string $preview_url URL of the preview image
 * @param string $caption Image caption
 * @param int $counter_lightbox Counter for lightbox
 * @param int $margin Margin between images
 * @param int $maxwidth Maximum width of the image
 * @return string HTML for a single gallery item
 */
function wpestate_generate_gallery_item($full_image_url, $preview_url, $caption, $counter_lightbox, $margin, $columns, $total_pictures) {

    
    $seAll = esc_html( sprintf( __( 'See all %d photos', 'wpresidence' ), $total_pictures ) );

   return '<div class="wpestate_property_gallery_item">' .
    '<a href="' . esc_url($full_image_url) . '" class="lightbox_trigger" data-slider-no="' . $counter_lightbox . '" data-elementor-lightbox-slideshow="property-gallery-widf" title="' . esc_attr($caption) . '">' .
        '<img class="" src="' . esc_url($preview_url) . '" alt="' . esc_attr($caption) . '" />' .
        '<span class="img_listings_mes">' . $seAll . '</span>' .
    '</a>' .
'</div>';
}






/**
 * Generate related listings section for a property
 *
 * This function includes the template for displaying similar listings
 * on the property page in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyRelatedListings
 * @since 1.0.0
 */

if (!function_exists('wpestate_estate_property_design_related_listings')) :
/**
 * Include the related listings template for a property
 *
 * @param array $attributes Shortcode attributes
 * @param string|null $content Shortcode content (unused)
 * @return void This function does not return a value, it includes a template file
 */
function wpestate_estate_property_design_related_listings($attributes, $content = null) {
    // Handle Elementor compatibility
    if (isset($attributes['is_elementor']) && intval($attributes['is_elementor']) == 1) {
        $property_id =  wpestate_return_property_id_elementor_builder($attributes);
    }else{
        global $propid;
        $property_id = $propid;
    }

    // Set default value for $is_tab
    $is_tab = '';

    // Include the similar listings template
    include(locate_template('/templates/listing_templates/property-page-templates/similar_listings.php'));
}
endif;












/**
 * Include the template for scheduling a property tour
 *
 * This function sets up the necessary variables and includes the template
 * for scheduling a tour in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyTour
 * @since 1.0.0
 */

if (!function_exists('wpestate_estate_property_schedule_tour')) :
/**
 * Set up and include the template for scheduling a property tour
 *
 * @param array $attributes Shortcode attributes
 * @param string|null $content Shortcode content (unused)
 * @return string HTML output of the schedule tour template
 */
function wpestate_estate_property_schedule_tour($attributes, $content = null) {


    // Parse shortcode attributes
    $attributes = shortcode_atts(array(
        'css' => '',
        'is_elementor' => ''
    ), $attributes);

    // Generate CSS class
    $css_class = '';
    if (function_exists('vc_shortcode_custom_css_class')) {
        $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($attributes['css'], ' '), '', $attributes);
    }

    // Handle Elementor compatibility
    if (isset($attributes['is_elementor']) && intval($attributes['is_elementor']) == 1) {
        $propertyID = wpestate_return_property_id_elementor_builder($attributes);
    } 

    // Set up context and options
    $agent_context = 'property_page';
    $enable_global_property_page_agent_sidebar = esc_html(wpresidence_get_option('wp_estate_global_property_page_agent_sidebar', ''));

    // Include the template file
    $template_path = locate_template('/templates/listing_templates/schedule_tour/property_page_schedule_tour.php');
    
    // Capture the output of the included file
    ob_start();
   
    include($template_path);
   
    $output = ob_get_clean();

    // Return the captured output
    return $output;
}
endif;






/**
 * Generate property agent contact form
 *
 * This function includes the contact form template for the property agent
 * on the property page in the WpResidence theme.
 *
 * @package WpResidence
 * @subpackage PropertyAgentContact
 * @since 1.0.0
 */

if (!function_exists('wpestate_estate_property_design_agent_contact')) :
/**
 * Include the property agent contact form template
 *
 * @param array $attributes Shortcode attributes
 * @param string|null $content Shortcode content (unused)
 * @return void This function does not return a value, it includes a template file
 */
function wpestate_estate_property_design_agent_contact($attributes, $content = null) {
    // Set the context for the contact form
    $context = 'property_page_form';

    // Handle Elementor compatibility
    if (isset($attributes['is_elementor']) && intval($attributes['is_elementor']) == 1) {
         $propertyID =  wpestate_return_property_id_elementor_builder($attributes);
        // Include the contact form template
        include(locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php'));
    }else{
        // bakery / classic editor
        global $propid;
        $propertyID = $propid;
        print '<div class="wpestate_contact_form_parent" >';
            // Include the contact form template
            include(locate_template('/templates/listing_templates/contact_form/property_page_contact_form.php'));
        print '</div>';
    }



}
endif;





/**
 * Generate property agent details section
 *
 * This function creates the agent details section for a property in the WpResidence theme.
 * It handles the layout and display of agent information on the property page.
 *
 * @package WpResidence
 * @subpackage PropertyAgent
 * @since 1.0.0
 */

if (!function_exists('wpestate_estate_property_design_agent')) :
/**
 * Generate the property agent details section
 *
 * @param array $attributes Shortcode attributes
 * @param string|null $content Shortcode content (unused)
 * @return string Formatted HTML for the property agent details section
 */
function wpestate_estate_property_design_agent($attributes, $content = null) {
    global $propid;

    // Parse shortcode attributes
    $attributes = shortcode_atts(array(
        'css'         => '',
        'columns'     => 'one column',
        'is_elementor'=> ''
    ), $attributes);

    // Handle Elementor compatibility
    if (intval($propid) == 0 && isset($attributes['is_elementor']) && intval($attributes['is_elementor'] == 1)) {
          $propid = wpestate_return_elementor_id(); // is ok
    }
    
    // Generate CSS classes
    $css_class = '';
    if (function_exists('vc_shortcode_custom_css_class')) {
        $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($attributes['css'], ' '), '', $attributes);
    }

    // Add column-specific class
    $css_class .= ($attributes['columns'] === "one column") ? " property_desing_agent_one_col " : " property_desing_agent_two_col ";

    // Get agent details
    $agent_id = intval(get_post_meta($propid, 'property_agent', true));
    $agent_context = 'agent_card';
    $realtor_details = wpestate_return_agent_details($propid);
 
    $prop_id = $propid;
    $property_page_context='custom_page_temaplate';


    // Generate agent details content
    ob_start();
    include(locate_template('templates/realtor_templates/agentdetails.php'));
    $agent_content = ob_get_clean();

    // Construct the final HTML
    $return_string = sprintf(
        '<div class="wpestate_estate_property_design_agent wpestate_property_widget %s">%s</div>',
        esc_attr($css_class),
        $agent_content
    );

    return $return_string;
}
endif;










if( !function_exists('wpestate_estate_property_slider_section') ):
function wpestate_estate_property_slider_section($attributes,$content = null){
    global $post;
    global $propid ;

    $return_string  ='';
    $detail         ='';
    $label          ='';

    extract(shortcode_atts(array(
        'css'       =>  '',
        'detail'    =>  'horizontal',
        'showmap'   =>  'no',
        'is_elementor'=> '',
        ), $attributes));

    if(function_exists('vc_shortcode_custom_css_class')){
        $css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ),'', $attributes );
    }

    if(intval($propid)==0 && isset( $attributes['is_elementor']) && intval($attributes['is_elementor']==1) ){
        $propid = wpestate_return_elementor_id(); // nsuUndeE
    }

    if ( isset($attributes['detail']) ){
        $detail  = $attributes['detail'];
    }

    if($detail==='horizontal'){
        return '<div class="wpestate_estate_property_slider_section_wrapper '.$css_class.' ">'.wpestate_horizontal_slider($propid).'</div>';
    }else{
        return '<div class="wpestate_estate_property_slider_section_wrapper '.$css_class.'">'.wpestate_vertical_slider($propid).'</div>';
    }
}
endif;



























if (!function_exists('wpestate_virtual_tour_details')):

    function wpestate_virtual_tour_details($post_id) {
        print get_post_meta($post_id, 'embed_virtual_tour', true);
    }

endif;




/**
 * Generate property details section
 *
 * This function generates various sections of property details for the WpResidence theme.
 * It handles different types of property information and formats them for display.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 1.0.0
 */

if (!function_exists('wpestate_estate_property_details_section')) :
/**
 * Generate a property details section
 *
 * @param array $attributes Shortcode attributes
 * @param string|null $content Shortcode content (unused)
 * @return string Formatted HTML for the property details section
 */
function wpestate_estate_property_details_section($attributes, $content = null) {
    global $post;
    global $propid;

    // Initialize variables
    $return_string = '';
    $detail = '';
    $css_class = '';

    // Parse shortcode attributes
    $attributes = shortcode_atts(array(
        'css'          => '',
        'detail'       => 'none',
        'columns'      => '3',
        'is_elementor' => '',
    ), $attributes);

    // Generate CSS class for Visual Composer
    if (function_exists('vc_shortcode_custom_css_class')) {
        $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class($attributes['css'], ' '), '', $attributes);
    }

    // Handle Elementor compatibility
    if (intval($propid) == 0 && isset($attributes['is_elementor']) && intval($attributes['is_elementor'] == 1)) {
        $propid = wpestate_return_elementor_id();//ok
    }

    // Generate content based on detail type
    switch ($attributes['detail']) {
        case 'Energy Certificate':
            $return_string = '<div class="property_energy_saving_info">' . wpestate_energy_save_features($propid) . '</div>';
            break;

        case 'Description':
            $return_string = estate_listing_content($propid);
            break;

        case 'Property Address':
            $return_string = estate_listing_address($propid, '', $attributes['columns']);
            break;

        case 'Property Details':
            $wpestate_prop_all_details = get_post_custom($propid);
            $return_string = estate_listing_details($propid, $wpestate_prop_all_details, $attributes['columns']);
            break;

        case 'Amenities and Features':
            $return_string = '<div class="wpestate_estate_property_details_section">' . 
                             estate_listing_features($propid, 3, 0, $attributes['columns']) . 
                             '</div>';
            break;

        case 'booking':
            $raw_content = get_post_meta( $propid, 'property_booking_shortcode', true );
            $return_string =  do_shortcode( $raw_content );;
            break;
        case 'Map':
            $return_string = do_shortcode('[property_page_map propertyid="' . esc_attr($propid) . '" istab="1"][/property_page_map]');
            break;

        case 'Virtual Tour':
        case 'Walkscore':
        case 'Floor Plans':
        case 'What\'s Nearby':
        case 'Subunits':
        case 'Video':
            ob_start();
            switch ($attributes['detail']) {
                case 'Virtual Tour':
                    wpestate_virtual_tour_details($propid);
                    break;
                case 'Walkscore':
                    wpestate_walkscore_details($propid);
                    break;
                case 'Floor Plans':
                    estate_floor_plan($propid);
                    break;
                case 'What\'s Nearby':
                    wpestate_yelp_details($propid);
                    break;
                case 'Subunits':
                    wpestate_subunits_details($propid);
                    break;
                case 'Video':
                    echo wpestate_listing_video($propid);
                    break;
            }
            $return_string = ob_get_clean();
            break;

        case 'Reviews':
            ob_start();
            if (wpresidence_get_option('wp_estate_show_reviews_prop', '') == 'yes') {
                // include(locate_template('/templates/listing_templates/property-page-templates/property_reviews.php'));
                get_template_part('templates/reviews/reviews');
            }
            $return_string = ob_get_clean();
            break;

        case 'Page Views':
            $return_string = '<canvas id="myChart"></canvas>';
            $return_string .= '<script type="text/javascript">
                jQuery(document).ready(function(){
                    wpestate_show_stat_accordion();
                });
            </script>';
            break;
    }

    // Wrap the content in a div with appropriate classes
    return sprintf(
        '<div class="wpestate_estate_property_details_section %s">%s</div>',
        esc_attr($css_class),
        $return_string
    );
}
endif;






/**
 * Generate simple property detail
 *
 * This function generates a simple property detail for the WpResidence theme.
 * It handles various property attributes and custom fields, formatting them
 * for display on the front end.
 *
 * @package WpResidence
 * @subpackage PropertyDetails
 * @since 1.0.0
 */

if (!function_exists('wpestate_estate_property_simple_detail')) :
/**
 * Generate a simple property detail
 *
 * @param array $attributes Shortcode attributes
 * @param string|null $content Shortcode content (unused)
 * @return string Formatted HTML for the property detail
 */
function wpestate_estate_property_simple_detail($attributes, $content = null) {
    global $post;
    global $propid;

    $return_string = '';
    $detail = '';
    $label = '';

    // Get property features
    $features_details = array();
    $feature_terms =  wpestate_get_cached_terms('property_features');
    if (is_array($feature_terms)) {
        foreach ($feature_terms as $term) {
            $features_details[$term->slug] = $term->name;
        }
    }

    // Parse shortcode attributes
    $attributes = shortcode_atts(
        array(
            'detail' => 'none',
            'label' => 'Label:',
            'is_elementor' => ''
        ),
        $attributes
    );

    $detail = $attributes['detail'];
    $label = $attributes['label'];

    // Handle Elementor compatibility
    if (intval($propid) == 0 && isset($attributes['is_elementor']) && intval($attributes['is_elementor'] == 1)) {
        $propid = wpestate_return_elementor_id();//ok
    }

    // Generate detail content
    if (array_key_exists($detail, $features_details)) {
        $return_string = has_term($detail, 'property_features', $propid) ? 'yes' : 'no';
    } else {
        switch ($detail) {
            case 'title':
                $return_string = get_the_title($propid);
                break;
            case 'property_agent':
                $return_string = get_the_title(get_post_meta($propid, $detail, true));
                break;
            case 'property_price':
                $wpestate_currency = esc_html(wpresidence_get_option('wp_estate_currency_symbol', ''));
                $where_currency = esc_html(wpresidence_get_option('wp_estate_where_currency_symbol', ''));
                $return_string = wpestate_show_price($propid, $wpestate_currency, $where_currency, 1);
                break;
            case 'description':
                $return_string = estate_listing_content($propid);
                break;
            case 'energy_certificate':
                $return_string = '<div class="property_energy_saving_info">' . wpestate_energy_save_features($propid) . '</div>';
                break;
            case 'property_pdf':
                $return_string = wpestate_property_sh_download_pdf($propid);
                break;
            case 'property_status':
                $return_string = get_the_term_list($propid, 'property_status', '', ',', '');
                break;
            case 'property_size':
            case 'property_lot_size':
                $return_string = wpestate_get_converted_measure($propid, $detail);
                break;
            case 'property_category':
            case 'property_action_category':
            case 'property_city':
            case 'property_area':
            case 'property_county_state':
                $return_string = get_the_term_list($propid, $detail, '', ', ', '');
                break;
            case 'property_video':
                $return_string = wpestate_listing_video($propid);
                break;
            default:
                if($detail!==''){
                    $meta_value = get_post_meta($propid, $detail, true);
                    $meta_value = apply_filters('wpml_translate_single_string', $meta_value, 'wpresidence-core', 'wp_estate_property_custom_' . $meta_value);
                    if ($meta_value !== esc_html__('Not Available', 'wpresidence-core')) {
                        $return_string = $meta_value;
                    }
                }
                break;
        }
    }

    // Wrap and return the final string if not empty
    if ($return_string !== '') {
        $return_string = sprintf(
            '<div class="property_custom_detail_wrapper"><span class="property_custom_detail_label">%s </span>%s</div>',
            esc_html($label),
            trim($return_string)
        );
    }

    return $return_string;
}
endif;




if( !function_exists('wpestate_property_sh_download_pdf') ):
function wpestate_property_sh_download_pdf($prop_id){
    $args = array(
            'post_mime_type'    => 'application/pdf',
            'post_type'         => 'attachment',
            'numberposts'       => -1,
            'post_status'       => null,
            'post_parent'       => $prop_id
        );

    $return_string='';
    $attachments = get_posts($args);

    if ($attachments) {

        $return_string.= '<div class="download_docs">'.esc_html__('Documents','wpresidence-core').'</div>';
        foreach ( $attachments as $attachment ) {
                $return_string .= '<div class="document_down">';
                ob_start();
                include (locate_template('templates/svg_icons/pdf_icon.svg'));
                $icon=ob_get_contents();
                ob_clean();

                $return_string .= $icon.'<a href="' . esc_url(wp_get_attachment_url($attachment->ID)) . '" target="_blank">' . esc_html($attachment->post_title) . '</a></div>';
            
            
           // $return_string.=  '<div class="document_down"><a href="'. wp_get_attachment_url($attachment->ID).'" target="_blank">'.$attachment->post_title.'<i class="fas fa-download"></i></a></div>';
        }
    }
    return $return_string;
}
endif;





if( !function_exists('wpestate_test_sh') ):
function wpestate_test_sh( $attributes,$content = null) {
    global $post;
    global $propid ;
    $return_string='das is cxx '.$post->ID.' das is good '.$propid ;
    return $return_string;
}
endif;


if( !function_exists('wpestate_subunits_details') ):
function  wpestate_subunits_details($propid){
    
     $has_multi_units = intval(get_post_meta($propid, 'property_has_subunits', true));
    $property_subunits_master = intval(get_post_meta($propid, 'property_subunits_master', true));

    if ($has_multi_units == 1) {
        include(locate_template('/templates/listing_templates/property_multi_units.php'));
    } else {
        if ($property_subunits_master != 0) {
            include(locate_template('/templates/listing_templates/property_multi_units.php'));
        }
    }

  
    
//    
//    $has_multi_units=intval(get_post_meta($propid, 'property_has_subunits', true));
//    $property_subunits_master=intval(get_post_meta($propid, 'property_subunits_master', true));
//
//    if($has_multi_units==1){
//        wpestate_shortcode_multi_units($propid,$property_subunits_master);
//    }else{
//        if($property_subunits_master!=0){
//            wpestate_shortcode_multi_units($propid,$property_subunits_master);
//        }
//    }
}
endif;



if( !function_exists('wpestate_shortcode_multi_units') ):
function wpestate_shortcode_multi_units($propid,$property_subunits_master,$is_print=0){

    $wpestate_currency                   =   esc_html( wpresidence_get_option('wp_estate_currency_symbol', '') );
    $where_currency             =   esc_html( wpresidence_get_option('wp_estate_where_currency_symbol', '') );
    $prop_id=$propid;

    if (function_exists('icl_translate') ){
        $wp_estate_property_multi_text          =   icl_translate('wpresidence-core','wp_estate_property_multi_text', esc_html( wpresidence_get_option('wp_estate_property_multi_text') ) );
        $wp_estate_property_multi_child_text    =   icl_translate('wpresidence-core','wp_estate_property_multi_child_text', esc_html( wpresidence_get_option('wp_estate_property_multi_child_text') ) );
    }else{
        $wp_estate_property_multi_text          =   stripslashes ( esc_html( wpresidence_get_option('wp_estate_property_multi_text') ) );
        $wp_estate_property_multi_child_text    =   stripslashes ( esc_html( wpresidence_get_option('wp_estate_property_multi_child_text') ) );
    }

    $has_multi_units            =   intval(get_post_meta($prop_id, 'property_has_subunits', true));
    $property_subunits_master   =   intval(get_post_meta($prop_id, 'property_subunits_master', true));

    $display=0;
    if ($has_multi_units==1){
        $display=1;
    }else{
        if( intval($property_subunits_master)!=0 ){
            $has_multi_units=intval(get_post_meta($property_subunits_master, 'property_has_subunits', true));
            if ($has_multi_units==1){
                $display=1;
            }

        }else{
            $display=0;
        }
    }



    if( $display==1 ){
        print '<div class="multi_units_wrapper">';
        if( intval($property_subunits_master)!=0 && $property_subunits_master!=$propid){
            $prop_id=intval($property_subunits_master);

            print '<h4 class="panel-title">';
            if($wp_estate_property_multi_child_text!=''){
                echo $wp_estate_property_multi_child_text;
            }else{
               _e('Other units in','wpresidence-core');
            }
            echo ' <a href="'. esc_url( get_permalink($property_subunits_master) ).'" target="_blank">'.get_the_title($property_subunits_master).'</a>';
            print'</h4>';

        }else{

            print '<h4 class="panel-title">';

                if($wp_estate_property_multi_text!=''){
                    echo $wp_estate_property_multi_text;
                }else{
                    esc_html__('Available Units','wpresidence-core');
                }
            print '</h4>';

        }



        $measure_sys            = esc_html ( wpresidence_get_option('wp_estate_measure_sys','') );


        $property_subunits_list_manual =  get_post_meta($prop_id, 'property_subunits_list_manual', true);

        if($property_subunits_list_manual!=''){
            $property_subunits_list= explode(',', $property_subunits_list_manual);
        }else{
            $property_subunits_list   =  get_post_meta($prop_id, 'property_subunits_list', true);
        }

            if(is_array($property_subunits_list)){
                foreach($property_subunits_list as $prop_id_unit){
                    $status = get_post_status($prop_id);
                    if($prop_id!=$prop_id_unit && $status=='publish'){
                        print '<div class="subunit_wrapper">';
                        $compare                =   wp_get_attachment_image_src(get_post_thumbnail_id($prop_id_unit), 'slider_thumb');
                        $property_rooms         =   get_post_meta($prop_id_unit, 'property_rooms', true);
                        $property_bathrooms     =   get_post_meta($prop_id_unit, 'property_bathrooms', true) ;
                        $property_bedrooms         =   get_post_meta($prop_id_unit, 'property_bedrooms', true);
						/*
						$property_size          =   get_post_meta($prop_id_unit, 'property_size', true) ;
                        $property_size          =   wpestate_sizes_no_format(floatval($property_size));
						*/
						$property_size       = wpestate_get_converted_measure( $prop_id_unit, 'property_size' );

                        $property_type          =   get_the_term_list($prop_id_unit, 'property_category', '', ', ', '') ;


                        if($is_print==1){
                            $property_type_array  =   wp_get_object_terms($prop_id, 'property_category', '');
                            $property_type='';
                            foreach($property_type_array as $term){
                                if($term->name!=''){
                                    $property_type.=$term->name.' ' ;
                                }
                            }
                        }

                        $title                  =   get_the_title($prop_id_unit);
                        $link                   =    esc_url( get_permalink($prop_id_unit) );

                        if($is_print!=1){
                            print '<div class="subunit_thumb"><a href="'.$link.'" target="_blank"><img src="'.$compare[0].'" alt="'.$title.'" /></a></div>';
                        }else{
                              print '<div class="subunit_thumb"><img src="'.$compare[0].'" alt="'.$title.'" /></div>';
                        }
                            print '<div class="subunit_details">';

                                if($is_print==1){
                                    print '<img class="print_qrcode_subunit" src="https://qrcode.tec-it.com/API/QRCode?size=small&dpi=110&data='. urlencode( $link) .'&choe=UTF-8" title="'.urlencode($title).'"  />';
   
                                }
                                if($is_print!=1){
                                    print '<div class="subunit_title"><a a href="'.$link.'" target="_blank">'.$title.'</a>  ';
                                }else{
                                    print '<div class="subunit_title">'.$title;
                                }
                                print '<div class="subunit_price">'; wpestate_show_price($prop_id_unit,$wpestate_currency,$where_currency);
                                print '</div></div>';
                                print '<div class="subunit_type"><strong>'.esc_html__('Category: ','wpresidence-core').'</strong> '.$property_type.', </div>';
                                print '<div class="subunit_rooms"><strong>'.esc_html__('Rooms: ','wpresidence-core').'</strong> '.$property_rooms.', </div>';
                                 print '<div class="subunit_rooms"><strong>'.esc_html__('Bedrooms: ','wpresidence-core').'</strong> '.$property_bedrooms.', </div>';
                             
                                print '<div class="subunit_bathrooms"><strong>'.esc_html__('Baths: ','wpresidence-core').'</strong> '.$property_bathrooms.', </div>';
                                print '<div class="subunit_size"><strong>'.esc_html__('Size: ','wpresidence-core').'</strong> '.$property_size.'</div>';
                            print '</div>';


                        print '</div>';
                    }

                }
            }



        print '</div>';
        }
    }
endif;


function wpestate_return_elementor_id2(){
    $id = wpresidence_get_option('wp_estate_elementor_id');
    if ( intval($id) ==0){

        $latest_post = get_posts("post_type='estate_property'&numberposts=1&fields='ids'");
        $id=$latest_post[0]->ID;

    }

    return $id;

}
/**
 * If this function doesn't exist in your codebase, add it to helper_functions.php
 */
if (!function_exists('wpestate_return_elementor_id')) {
    function wpestate_return_elementor_id() {
        // Get latest property for Elementor edit mode preview
        $latest_property = get_posts([
            'post_type' => 'estate_property',
            'post_status' => 'publish',
            'numberposts' => 1,
            'orderby' => 'ID',
            'order' => 'DESC',
            'fields' => 'ids'
        ]);
        
        return !empty($latest_property) ? $latest_property[0] : 0;
    }
}