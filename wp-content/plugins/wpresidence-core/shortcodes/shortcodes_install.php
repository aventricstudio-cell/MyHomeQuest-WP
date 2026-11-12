<?php


/**
* register shortcodes
*
*
* @since    1.0
* @access   private
*/

function wpestate_shortcodes(){
    wpestate_register_shortcodes();
    wpestate_tiny_short_codes_register();
    add_filter('widget_text', 'do_shortcode');
}


/**
*register tiny plugins functions
*
*
* @since    1.0
* @access   private
*/
function wpestate_tiny_short_codes_register() {
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
        return;
    }

    if (get_user_option('rich_editing') == 'true') {
        add_filter('mce_external_plugins', 'wpestate_add_plugin');
        add_filter('mce_buttons_3', 'wpestate_register_button');
    }

}

/**
*push the code into Tiny buttons array
*
*
* @since    1.0
* @access   private
*/
function wpestate_register_button($buttons) {
    array_push($buttons, "|", "slider_recent_items");
    array_push($buttons, "|", "testimonials");
    array_push($buttons, "|", "testimonial_slider");
    array_push($buttons, "|", "recent_items");
    array_push($buttons, "|", "featured_agent");
    array_push($buttons, "|", "featured_article");
    array_push($buttons, "|", "featured_property");
    array_push($buttons, "|", "list_items_by_id");
    array_push($buttons, "|", "login_form");
    array_push($buttons, "|", "register_form");
    array_push($buttons, "|", "advanced_search");
    array_push($buttons, "|", "font_awesome");
    array_push($buttons, "|", "spacer");
    array_push($buttons, "|", "icon_container");
    array_push($buttons, "|", "list_agents");
    array_push($buttons, "|", "places_list");
    array_push($buttons, "|", "listings_per_agent");
    array_push($buttons, "|", "property_page_map");
    array_push($buttons, "|", "contact_us_form");
    array_push($buttons, "|", "estate_membership_packages");
    array_push($buttons, "|", "slider_properties");
    //slick function
    array_push($buttons, "|", "places_slider");
    array_push($buttons, "|", "full_map");
    return $buttons;
}




/**
*point to the right js
*
*
* @since    1.0
* @access   private
*/
function wpestate_add_plugin($plugin_array) {
    $plugin_array['slider_recent_items']        = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['testimonials']               = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['testimonial_slider']         = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['recent_items']               = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['featured_agent']             = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['featured_article']           = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['featured_property']          = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['login_form']                 = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['register_form']              = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['list_items_by_id']           = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['advanced_search']            = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['font_awesome']               = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['spacer']                     = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['icon_container']             = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['list_agents']                = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['places_list']                = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['listings_per_agent']         = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['property_page_map']          = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['estate_membership_packages'] = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['estate_featured_user_role']  = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['places_slider']              = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['slider_properties']          = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['taxonomy_list']              = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    $plugin_array['full_map']                   = WPESTATE_PLUGIN_DIR_URL . '/js/shortcodes.js';
    return $plugin_array;
}



/**
* register shortcodes
*
*
* @since    1.0
* @access   private
*/
function wpestate_register_shortcodes() {
    add_shortcode('contact_us_form', 'wpestate_contact_us_form');
    add_shortcode('slider_recent_items', 'wpestate_slider_recent_posts_pictures');
    add_shortcode('spacer', 'wpestate_spacer_shortcode_function');
    add_shortcode('recent-posts', 'wpestate_recent_posts_function');
    add_shortcode('testimonial', 'wpestate_testimonial_function');
    add_shortcode('testimonial_slider', 'wpestate_testimonial_slider_function');
    add_shortcode('recent_items', 'wpestate_recent_posts_pictures_new');
    add_shortcode('featured_agent', 'wpestate_featured_agent');
    add_shortcode('featured_article', 'wpestate_featured_article');
    add_shortcode('featured_property', 'wpestate_featured_property');
    add_shortcode('login_form', 'wpestate_login_form_function');
    add_shortcode('register_form', 'wpestate_register_form_function');
    add_shortcode('list_items_by_id', 'wpestate_list_items_by_id_function');
    add_shortcode('advanced_search', 'wpestate_advanced_search_function');
    add_shortcode('font_awesome', 'wpestate_font_awesome_function');
    add_shortcode('icon_container', 'wpestate_icon_container_function');
    add_shortcode('list_agents','wpestate_list_agents_function');
    add_shortcode('places_list', 'wpestate_places_list_function');
    add_shortcode('listings_per_agent', 'wplistingsperagent_shortcode_function' );
    add_shortcode('property_page_map', 'wpestate_property_page_map_function' );
    add_shortcode('test_sh', 'wpestate_test_sh' );
    add_shortcode('estate_membership_packages','wpestate_membership_packages_function');
    add_shortcode('estate_featured_user_role','wpestate_featured_agency_developer');
    add_shortcode('places_slider','wpestate_places_slider');
    add_shortcode('slider_properties','wpestate_slider_properties');
    add_shortcode('taxonomy_list','wpestate_taxonomy_list');
    add_shortcode('full_map','wpestate_full_map_shortcode');
    add_shortcode('filter_list','wpestate_filter_list_properties');


    add_shortcode('estate_property_page_tab', 'wpestate_property_page_design_tab' );
    add_shortcode('estate_property_page_acc', 'wpestate_property_page_design_acc' );
    add_shortcode('estate_property_simple_detail','wpestate_estate_property_simple_detail');
    add_shortcode('estate_property_details_section','wpestate_estate_property_details_section');
    add_shortcode('estate_property_slider_section','wpestate_estate_property_slider_section');
    add_shortcode('estate_property_design_agent','wpestate_estate_property_design_agent');
    add_shortcode('estate_property_design_agent_contact','wpestate_estate_property_design_agent_contact');
    add_shortcode('estate_property_design_related_listings','wpestate_estate_property_design_related_listings');
    add_shortcode('estate_property_design_intext_details','wpestate_estate_property_design_intext_details');
    add_shortcode('estate_property_design_gallery','wpestate_estate_property_design_gallery');
    add_shortcode('estate_property_design_agent_details_intext_details','wpestate_estate_property_design_agent_details_intext_details');

    add_shortcode('estate_property_design_masonary_gallery','wpestate_estate_property_design_masonary_gallery');
    add_shortcode('estate_property_design_masonary_gallery2','wpestate_estate_property_design_masonary_gallery_2');
    add_shortcode('estate_property_design_other_agents','wpestate_property_design_other_agents');


}

/**
* return all taxonomies array
*
*
* @since    1.0
* @access   private
*/
function wpestate_generate_all_tax(){
    global $all_tax;

    if(function_exists('wpestate_request_transient_cache')){
        $all_tax = wpestate_request_transient_cache ('wpestate_js_composer_all_tax');
    }

    return $all_tax;
}


/**
* return all taxonomies labels array
*
*
* @since    1.0
* @access   private
*/
function wpestate_generate_all_tax_labels(){
    global $all_tax_labels;

    if(function_exists('wpestate_request_transient_cache')){
        $all_tax_labels = wpestate_request_transient_cache ('wpestate_js_composer_all_tax_labels');
    }

    return $all_tax_labels;
}




/**
* hook to generate autocomplete data
*
*
* @since    1.0
* @access   private
*/
add_action( 'init', 'wpestate_autocomplete_populate',99 );
function wpestate_autocomplete_populate () {
    global $all_tax;
    global $all_tax_labels;
    $all_tax=array();
    $all_tax_labels=array();

    global $property_category_values;
    global $property_city_values;
    global $property_area_values;
    global $property_county_state_values;
    global $property_action_category_values;
    global $property_status_values;

    $property_category_values       =   wpestate_generate_category_values_shortcode();

    
    $property_city_values           =   wpestate_generate_city_values_shortcode();
    $property_area_values           =   wpestate_generate_area_values_shortcode();
    $property_county_state_values   =   wpestate_generate_county_values_shortcode();
    $property_action_category_values=   wpestate_generate_action_values_shortcode();
    $property_status_values         =   wpestate_generate_status_values_shortcode();
    $all_tax                        =   wpestate_generate_all_tax();
    $all_tax_labels                 =   wpestate_generate_all_tax_labels();
}


if ( ! function_exists( 'wpestate_vc_register_autocomplete_filters' ) ) {
    /**
     * Registers WPBakery (Visual Composer) autocomplete callbacks for taxonomy based fields.
     *
     * This ensures the autocomplete inputs use live search results even when the static
     * `values` array is empty (for example when transients are not yet populated) and
     * guarantees that already saved values are rendered with their human readable labels.
     *
     * @since 4.9.0
     * @return void
     */
    function wpestate_vc_register_autocomplete_filters() {
        if ( ! function_exists( 'vc_map' ) ) {
            return;
        }

        global $wpestate_vc_autocomplete_tax_map;
        $wpestate_vc_autocomplete_tax_map = array();

        $property_taxonomy_map = array(
            'category_ids'      => array( 'property_category' ),
            'action_ids'        => array( 'property_action_category' ),
            'city_ids'          => array( 'property_city' ),
            'area_ids'          => array( 'property_area' ),
            'state_ids'         => array( 'property_county_state' ),
            'status_ids'        => array( 'property_status' ),
            'control_terms_id'  => array(
                'property_category',
                'property_action_category',
                'property_city',
                'property_area',
                'property_county_state',
            ),
        );

        $property_shortcodes = array(
            'filter_list'         => array( 'category_ids', 'action_ids', 'city_ids', 'area_ids', 'state_ids', 'status_ids' ),
            'slider_recent_items' => array( 'category_ids', 'action_ids', 'city_ids', 'area_ids', 'state_ids', 'status_ids' ),
            'recent_items'        => array( 'category_ids', 'action_ids', 'city_ids', 'area_ids', 'state_ids', 'status_ids', 'control_terms_id' ),
            'full_map'            => array( 'category_ids', 'action_ids', 'city_ids', 'area_ids', 'state_ids' ),
        );
    
        if(function_exists('wpestate_vc_autocomplete_register_pair')){
            foreach ( $property_shortcodes as $shortcode => $params ) {
                foreach ( $params as $param_name ) {
                    if ( isset( $property_taxonomy_map[ $param_name ] ) ) {
                        wpestate_vc_autocomplete_register_pair( $shortcode, $param_name, $property_taxonomy_map[ $param_name ] );
                    }
                }
            }
        }

        $place_taxonomies = array( 'property_category', 'property_action_category', 'property_city', 'property_area', 'property_county_state' );
        
        if(function_exists('wpestate_vc_autocomplete_register_pair')){
            wpestate_vc_autocomplete_register_pair( 'places_list', 'place_list', $place_taxonomies );
            wpestate_vc_autocomplete_register_pair( 'places_slider', 'place_list', $place_taxonomies );
            wpestate_vc_autocomplete_register_pair( 'featured_place', 'id', $place_taxonomies );
        }
        
        $agent_taxonomy_map = array(
            'category_ids' => array( 'property_category_agent' ),
            'action_ids'   => array( 'property_action_category_agent' ),
            'city_ids'     => array( 'property_city_agent' ),
            'area_ids'     => array( 'property_area_agent' ),
        );
        
        if(function_exists('wpestate_vc_autocomplete_register_pair')){
            foreach ( $agent_taxonomy_map as $param_name => $taxonomies ) {
                wpestate_vc_autocomplete_register_pair( 'list_agents', $param_name, $taxonomies );
            }
        }
    }
}

if ( did_action( 'vc_after_init' ) ) {
    wpestate_vc_register_autocomplete_filters();
} else {
    add_action( 'vc_after_init', 'wpestate_vc_register_autocomplete_filters', 50 );
}

if ( ! function_exists( 'wpestate_vc_autocomplete_register_pair' ) ) {
    /**
     * Helper to attach the live search callbacks for a shortcode parameter.
     *
     * @param string $shortcode  Shortcode base name used in VC.
     * @param string $param_name Parameter name defined in vc_map.
     * @param array  $taxonomies List of taxonomies that should be queried.
     * @return void
     */
    function wpestate_vc_autocomplete_register_pair( $shortcode, $param_name, $taxonomies ) {
        global $wpestate_vc_autocomplete_tax_map;

        $taxonomies = (array) $taxonomies;
        if ( empty( $taxonomies ) ) {
            return;
        }

        $key = $shortcode . '_' . $param_name;
        $wpestate_vc_autocomplete_tax_map[ $key ] = $taxonomies;

        add_filter( 'vc_autocomplete_' . $key . '_callback', 'wpestate_vc_autocomplete_search_callback', 10, 1 );
        add_filter( 'vc_autocomplete_' . $key . '_render', 'wpestate_vc_autocomplete_render_callback', 10, 1 );
    }
}

if ( ! function_exists( 'wpestate_vc_autocomplete_search_callback' ) ) {
    /**
     * Provides search suggestions for VC autocomplete fields.
     *
     * @param array|string $search Search data coming from VC.
     * @return array
     */
    function wpestate_vc_autocomplete_search_callback( $search ) {
        global $wpestate_vc_autocomplete_tax_map;

        $current_filter = current_filter();
        $key            = str_replace( array( 'vc_autocomplete_', '_callback' ), '', $current_filter );

        if ( empty( $wpestate_vc_autocomplete_tax_map[ $key ] ) ) {
            return array();
        }

        $search_value = '';
        if ( is_array( $search ) && isset( $search['value'] ) ) {
            $search_value = $search['value'];
        } elseif ( is_string( $search ) ) {
            $search_value = $search;
        }

        $search_value = trim( sanitize_text_field( $search_value ) );

        if ( '' === $search_value ) {
            return array();
        }

        return wpestate_vc_autocomplete_fetch_terms( $search_value, $wpestate_vc_autocomplete_tax_map[ $key ] );
    }
}

if ( ! function_exists( 'wpestate_vc_autocomplete_render_callback' ) ) {
    /**
     * Renders the label for already selected VC autocomplete values.
     *
     * @param array|string $value Stored value from VC field.
     * @return array|false
     */
    function wpestate_vc_autocomplete_render_callback( $value ) {
        global $wpestate_vc_autocomplete_tax_map;

        $current_filter = current_filter();
        $key            = str_replace( array( 'vc_autocomplete_', '_render' ), '', $current_filter );

        if ( empty( $wpestate_vc_autocomplete_tax_map[ $key ] ) ) {
            return false;
        }

        $term_id = 0;
        if ( is_array( $value ) && isset( $value['value'] ) ) {
            $term_id = absint( $value['value'] );
        } elseif ( is_numeric( $value ) ) {
            $term_id = absint( $value );
        }

        if ( ! $term_id ) {
            return false;
        }

        foreach ( (array) $wpestate_vc_autocomplete_tax_map[ $key ] as $taxonomy ) {
            if ( ! taxonomy_exists( $taxonomy ) ) {
                continue;
            }

            $term = get_term( $term_id, $taxonomy );

            if ( $term && ! is_wp_error( $term ) ) {
                $label = $term->name;
                $type  = wpestate_vc_autocomplete_taxonomy_label( $taxonomy );
                if ( '' !== $type ) {
                    $label .= sprintf( ' (%s)', $type );
                }

                return array(
                    'value' => (string) $term->term_id,
                    'label' => $label,
                );
            }
        }

        return false;
    }
}

if ( ! function_exists( 'wpestate_vc_autocomplete_fetch_terms' ) ) {
    /**
     * Fetches term suggestions for VC autocomplete fields.
     *
     * @param string $search_value User search string.
     * @param array  $taxonomies   List of taxonomies to query.
     * @return array
     */
    function wpestate_vc_autocomplete_fetch_terms( $search_value, $taxonomies ) {
        $results = array();

        foreach ( (array) $taxonomies as $taxonomy ) {
            if ( ! taxonomy_exists( $taxonomy ) ) {
                continue;
            }

            $terms = get_terms(
                array(
                    'taxonomy'   => $taxonomy,
                    'hide_empty' => false,
                    'search'     => $search_value,
                    'number'     => 15,
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                )
            );

            if ( is_wp_error( $terms ) || empty( $terms ) ) {
                continue;
            }

            $taxonomy_label = wpestate_vc_autocomplete_taxonomy_label( $taxonomy );

            foreach ( $terms as $term ) {
                $label = $term->name;
                if ( '' !== $taxonomy_label ) {
                    $label .= sprintf( ' (%s)', $taxonomy_label );
                }

                $results[] = array(
                    'value' => (string) $term->term_id,
                    'label' => $label,
                );
            }
        }

        return $results;
    }
}

if ( ! function_exists( 'wpestate_vc_autocomplete_taxonomy_label' ) ) {
    /**
     * Returns a readable taxonomy label for autocomplete display.
     *
     * @param string $taxonomy Taxonomy slug.
     * @return string
     */
    function wpestate_vc_autocomplete_taxonomy_label( $taxonomy ) {
        $taxonomy_object = get_taxonomy( $taxonomy );

        if ( $taxonomy_object && isset( $taxonomy_object->labels->singular_name ) ) {
            return $taxonomy_object->labels->singular_name;
        }

        if ( $taxonomy_object && isset( $taxonomy_object->label ) ) {
            return $taxonomy_object->label;
        }

        return '';
    }
}




/**
* hook to generate visual composer shortcodes
*
*
* @since    1.0
* @access   private
*/

add_action( 'vc_before_init', 'wpestate_vc_shortcodes' );
if( function_exists('vc_map') ):

    if( !function_exists('wpestate_vc_shortcodes')):
        function wpestate_vc_shortcodes(){

            global $all_tax;
            global $all_tax_labels;

            global $property_category_values;
            global $property_city_values;
            global $property_area_values;
            global $property_county_state_values;
            global $property_action_category_values;
            global $property_status_values;
            wpestate_autocomplete_populate();

            $places                             =   array();
            $all_places                         =   array();
            $membership_packages                =   array();
            $agency_developers_array            =   array();
            $agent_array                        =   array();
            $article_array                      =   array();

            $out_agent_tax_array= wpestate_js_composer_out_agent_tax_array();
            // gettings agent packages
            $agent_array    = wpestate_return_agent_array();
            $article_array  = wpestate_return_article_array();




            // gettings agency/developers packages
            $agency_developers_array=false;
            if(function_exists('wpestate_request_transient_cache')){
                $agency_developers_array = wpestate_request_transient_cache ('wpestate_js_composer_article_agency_developers_array');
            }

            if($agency_developers_array===false){
                $args_inner = array(
                        'post_type' => array( 'estate_agency', 'estate_developer' ),
                        'showposts' => -1
                );
                $all_agency_dev_packages = get_posts( $args_inner );
                $agency_developers_array=array();
                if( count($all_agency_dev_packages) > 0 ){
                        foreach( $all_agency_dev_packages as $single_package ){
                                $temp_array=array();
                                $temp_array['label'] = $single_package->post_title;
                                $temp_array['value'] = $single_package->ID;

                                $agency_developers_array[] = $temp_array;
                        }
                }
                if(function_exists('wpestate_set_transient_cache')){
                    wpestate_set_transient_cache ('wpestate_js_composer_article_agency_developers_array',$agency_developers_array,60*60*4);
                }
            }
            // gettings agency/developers packages END

            // gettings membership packages
            $membership_packages = wpestate_generate_membershiop_packages_shortcodes();
            // gettings membership packages END


            $agent_single_details = array(
                'Name'          =>  'name',
                'Main Image'    =>  'image',
                'Description'   =>  'description',
                'Page Link'     =>  'page_link',
                'Agent Skype'   =>  'agent_skype',
                'Agent Phone'   =>  'agent_phone',
                'Agent Mobile'  =>  'agent_mobile',
                'Agent email'   =>  'agent_email',
                'Agent position'                =>  'agent_position',
                'Agent Facebook'                =>  'agent_facebook',
                'Agent Twitter'                 =>  'agent_twitter',
                'Agent Linkedin'                => 'agent_linkedin',
                'Agent Pinterest'               => 'agent_pinterest',
                'Agent Instagram'               => 'agent_instagram',
                'Agent website'                 => 'agent_website',
                'Agent category'                => 'property_category_agent',
                'Agent action category'         => 'property_action_category_agent',
                'Agent city category'           => 'property_city_agent',
                'Agent Area category'           => 'property_area_agent',
                'Agent County/State category'   => 'property_county_state_agent'
            );
            $agent_explanations='';
            foreach($agent_single_details as $key=>$value){
                $agent_explanations.=' for '.$key.' use this string: {'.$value.'}</br>';
            }


            vc_map(
                array(
                    "name" => esc_html__("Property Page Only - Text with Agent details ","wpresidence-core"),//done
                    "base" => "estate_property_design_agent_details_intext_details",
                    "class" => "",
                    "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                    'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                    'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                    'weight'=>99,
                    'icon'   =>'wpestate_vc_logo2',
                    'description'=>'',

                    "params" => array(
                        array(
                            "type"          =>  "textarea_html",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Text","wpresidence-core"),
                            "param_name"    =>  "content",
                            "value"         =>  '',
                            "description"   =>  $agent_explanations
                        ),
                          array(
                            'type' => 'css_editor',
                            'heading' => esc_html__( 'Css', 'wpresidence-core' ),
                            'param_name' => 'css',
                            'group' => esc_html__( 'Design options', 'wpresidence-core' ),
                        ),
                    )

                   )

                );





            $slider_details=array('horizontal','vertical');
            vc_map(
            array(
                "name" => esc_html__("Property Page Only - Property Gallery","wpresidence-core"),//done
                "base" => "estate_property_design_gallery",
                "class" => "",
                "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                'weight'=>99,
                'icon'   =>'wpestate_vc_logo2',
                'description'=>'',


                "params" => array(
                    array(
                        "type"          =>  "textfield",
                        "holder"        =>  "div",
                        "class"         =>  "",
                        "heading"       =>  esc_html__("Thumbnail max width in px","wpresidence-core"),
                        "param_name"    =>  "maxwidth",
                        "value"         =>  '200',
                        "description"   =>  esc_html__("Thumbnail max width in px (*height is auto calculated based on image ratio)","wpresidence-core")
                    ),
                    array(
                        "type"          =>  "textfield",
                        "holder"        =>  "div",
                        "class"         =>  "",
                        "heading"       =>  esc_html__("Thumbnail right & bottom margin in px","wpresidence-core"),
                        "param_name"    =>  "margin",
                        "value"         =>  '10',
                        "description"   =>  esc_html__("Thumbnail right & bottom margin in px","wpresidence-core")
                    ),
                    array(
                        "type"          =>  "textfield",
                        "holder"        =>  "div",
                        "class"         =>  "",
                        "heading"       =>  esc_html__("Maximum no of thumbs","wpresidence-core"),
                        "param_name"    =>  "image_no",
                        "value"         =>  '4',
                        "description"   =>  esc_html__("Maximum no of thumbs","wpresidence-core")
                    ),
                    array(
                        'type' => 'css_editor',
                        'heading' => esc_html__( 'Css', 'wpresidence-core' ),
                        'param_name' => 'css',
                        'group' => esc_html__( 'Design options', 'wpresidence-core' ),
                    ),
                )

               )

            );


            $slider_details=array('horizontal','vertical');
            vc_map(
            array(
                "name" => esc_html__("Property Page Only - Related Listings","wpresidence-core"),//done
                "base" => "estate_property_design_related_listings",
                "class" => "",
                "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                'weight'=>99,
                'icon'   =>'wpestate_vc_logo2',
                'description'=>'',

                "params" => ""


               )

            );







            $slider_details=array('horizontal','vertical');
            vc_map(
            array(
                "name" => esc_html__("Property Page Only - Contact Form Agent","wpresidence-core"),//done
                "base" => "estate_property_design_agent_contact",
                "class" => "",
                "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                'weight'=>99,
                'icon'   =>'wpestate_vc_logo2',
                'description'=>'',

                "params" => ""


               )

            );





            $slider_details=array('horizontal','vertical');

            $agent_card=array('one column','two columns');
            vc_map(
            array(
                "name" => esc_html__("Property Page Only - Agent Card","wpresidence-core"),//done
                "base" => "estate_property_design_agent",
                "class" => "",
                "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                'weight'=>99,
                'icon'   =>'wpestate_vc_logo2',
                'description'=>'',
                'params' => array(
                    array(
                        "type"          =>  "dropdown",
                        "holder"        =>  "div",
                        "class"         =>  "",
                        "heading"       =>  esc_html__("Colums : 1 or 2","wpresidence-core"),
                        "param_name"    =>  "columns",
                        "value"         =>  $agent_card,
                        "description"   =>  esc_html__("One column means that agent details go below the image, two columns means agent details are on the right side of the image.","wpresidence-core"),
                    ),
                    array(
                        'type' => 'css_editor',
                        'heading' => esc_html__( 'Css', 'wpresidence-core' ),
                        'param_name' => 'css',
                        'group' => esc_html__( 'Design options', 'wpresidence-core' ),
                    ),
                )




               )

            );











            $slider_details=array('horizontal','vertical');
            $yesno=array('no','yes');
            vc_map(
            array(
                "name" => esc_html__("Property Page Only - Property Images / Video Slider","wpresidence-core"),//done
                "base" => "estate_property_slider_section",
                "class" => "",
                "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                'weight'=>99,
                'icon'   =>'wpestate_vc_logo2',
                'description'=>'',

                "params" => array(
                    array(
                        "type"          =>  "dropdown",
                        "holder"        =>  "div",
                        "class"         =>  "",
                        "heading"       =>  esc_html__("Slider Type","wpresidence-core"),
                        "param_name"    =>  "detail",
                        "value"         =>  $slider_details,
                        "description"   =>  esc_html__("Slider Type","wpresidence-core")
                    ),
                     array(
                        "type"          =>  "dropdown",
                        "holder"        =>  "div",
                        "class"         =>  "",
                        "heading"       =>  esc_html__("Show map in slider type?","wpresidence-core"),
                        "param_name"    =>  "showmap",
                        "value"         =>  $yesno,
                        "description"   =>  esc_html__("Make sure you are using only 1 map per page in order to avoid conflicts","wpresidence-core")
                    ),
                    array(
                        'type' => 'css_editor',
                        'heading' => esc_html__( 'Css', 'wpresidence-core' ),
                        'param_name' => 'css',
                        'group' => esc_html__( 'Design options', 'wpresidence-core' ),
                    ),
                )

               )

            );









            $details_list=array(
                'none',
                'Description',
                'Property Address',
                'Property Details',
                'Amenities and Features',
                'Map',
                'Virtual Tour',
                'Walkscore',
                'Floor Plans',
                'Page Views',
                'What\'s Nearby',
                'Subunits',
                'Energy Certificate',
                'Reviews',
                'Video'
            );

            vc_map(
                array(
                    "name" => esc_html__("Property Page Only - Details Section","wpresidence-core"),//done
                    "base" => "estate_property_details_section",
                    "class" => "",
                    "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                    'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                    'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                    'weight'=>99,
                    'icon'   =>'wpestate_vc_logo2',
                    'description'=>'',

                    "params" => array(
                        array(
                            "type"          =>  "dropdown",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Select the property section","wpresidence-core"),
                            "param_name"    =>  "detail",
                            "value"         =>  $details_list,
                            "description"   =>  esc_html__("Select a property section from the theme default details elements.","wpresidence-core")
                        ),
                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Columns","wpresidence-core"),
                            "param_name"    =>  "columns",
                            "value"         =>  '3',
                            "description"   =>  esc_html__("Columns (*works only for address, property details and features and amenities)","wpresidence-core"),
                        ),
                        array(
                            'type' => 'css_editor',
                            'heading' => esc_html__( 'Css', 'wpresidence-core' ),
                            'param_name' => 'css',
                            'group' => esc_html__( 'Design options', 'wpresidence-core' ),
                        ),




                    )

                   )

                );







            $single_details = array(
                'none'          =>  'none',
                'Title'         =>  'title',
                'Description'   =>  'description',
                'Categories'    =>  'property_category',
                'Action'        =>  'property_action_category',
                'City'          =>  'property_city',
                'Neighborhood'  =>  'property_area',
                'County / State'=>  'property_county_state',
                'Address'       =>  'property_address',
                'Energy Certificate'=>'energy_certificate',
                'Zip'           =>  'property_zip',
                'Country'       =>  'property_country',
                'Status'        =>  'property_status',
                'Price'         =>  'property_price',
                'Price Label'   =>  'property_label',
                'Price Label before'=>  'property_label_before',
                'Size'              =>  'property_size',
                'Lot Size'          =>  'property_lot_size',
                'Rooms'             =>  'property_rooms',
                'Bedrooms'          =>  'property_bedrooms',
                'Bathrooms'         =>  'property_bathrooms',
                'Download Pdf'      =>  'property_pdf',
                'Agent'             =>  'property_agent',

            );

            $all_options_fields =  get_option('wpresidence_admin','' );

            if(isset($all_options_fields['wpestate_custom_fields_list'])){
                $custom_fields =  $all_options_fields['wpestate_custom_fields_list'];
            }

            if( !empty($custom_fields) ){
                $i=0;
                foreach($custom_fields['add_field_name'] as $i=>$value ) {
                    $name         =     $custom_fields['add_field_name'][$i];
                    $slug         =     wpestate_limit45(sanitize_title( $name ));
                    $slug         =     sanitize_key($slug);
                    $single_details[str_replace('-',' ',$name)]=     $slug;

               }
            }




            vc_map(
                array(
                    "name" => esc_html__("Property Page Only - Single Detail","wpresidence-core"),//done
                    "base" => "estate_property_simple_detail",
                    "class" => "",
                    "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                    'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                    'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                    'weight'=>99,
                    'icon'   =>'wpestate_vc_logo2',
                    'description'=>'',

                    "params" => array(
                        array(
                            "type"          =>  "dropdown",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Select single detail","wpresidence-core"),
                            "param_name"    =>  "detail",
                            "value"         =>  $single_details,
                            "description"   =>  esc_html__("Select one single detail from dropdown","wpresidence-core")
                        ),
                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Element Label","wpresidence-core"),
                            "param_name"    =>  "label",
                            "value"         =>  'Label:',
                            "description"   =>  esc_html__("Element Label","wpresidence-core"),
                        ),




                    )

                   )

                );


            $single_details['Favorite action']   =  'favorite_action';
            $single_details['Page_views']        =  'page_views';
            $single_details['Print Action']      =  'print_action';
            $single_details['Facebook share']    =  'facebook_share';
            $single_details['Twiter share']      =  'twiter_share';
            $single_details['Google+ share']     =  'google_share';
            $single_details['Pinterest share']   =  'pinterest_share';
            $single_details['Share by email']    =  'email_share';
            $single_details['Whatsapp Share ']   =  'whatsapp_share';


            $explanations=' for Wordpress property id use this string: {prop_id}</br>';
            $explanations.=' for Property url use this string: {prop_url}</br>';
            unset($single_details['none']);
            foreach($single_details as $key=>$value){
                $explanations.=' for '.$key.' use this string: {'.$value.'}</br>';

            }

             vc_map(
                array(
                    "name" => esc_html__("Property Page Only - Text with Details ","wpresidence-core"),//done
                    "base" => "estate_property_design_intext_details",
                    "class" => "",
                    "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                    'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                    'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                    'weight'=>99,
                    'icon'   =>'wpestate_vc_logo2',
                    'description'=>'',

                    "params" => array(
                        array(
                            "type"          =>  "textarea_html",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Text","wpresidence-core"),
                            "param_name"    =>  "content",
                            "value"         =>  '',
                            "description"   =>  $explanations
                        ),
                        array(
                            'type' => 'css_editor',
                            'heading' => esc_html__( 'Css', 'wpresidence-core' ),
                            'param_name' => 'css',
                            'group' => esc_html__( 'Design options', 'wpresidence-core' ),
                        ),
                    )

                   )

                );



             vc_map(
                array(
                    "name" => esc_html__("Property Page Only - Details as Accordion","wpresidence-core"),//done
                    "base" => "estate_property_page_acc",
                    "class" => "",
                    "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                    'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                    'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                    'weight'=>99,
                    'icon'   =>'wpestate_vc_logo2',
                    'description'=>'',

                    "params" => array(
                          array(
                            "type"          =>  "dropdown",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Accordion Open/Close Status","wpresidence-core"),
                            "param_name"    =>  "style",
                            "value"         =>  array(1=>esc_html__("all open","wpresidence-core"),2=>esc_html__("all closed","wpresidence-core"),3=>esc_html__("only the first one open","wpresidence-core"),),
                            "description"   =>  esc_html__("Accordion Open/Close status","wpresidence-core")
                        ),


                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Description","wpresidence-core"),
                            "param_name"    =>  "description",
                            "value"         =>  esc_html__("Description","wpresidence-core"),
                            "description"   =>  esc_html__("Description Label. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),

                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Property Address","wpresidence-core"),
                            "param_name"    =>  "property_address",
                            "value"         =>   esc_html__("Property Address","wpresidence-core"),
                            "description"   =>  esc_html__("Property Address Label. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),

                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Property Details","wpresidence-core"),
                            "param_name"    =>  "property_details",
                            "value"         =>   esc_html__("Property Details","wpresidence-core"),
                            "description"   =>  esc_html__("property_details Label. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),

                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Amenities and Features","wpresidence-core"),
                            "param_name"    =>  "amenities_features",
                            "value"         =>  esc_html__("Amenities and Features","wpresidence-core"),
                            "description"   =>  esc_html__("Amenities and Features Label. Set it blank if you don't want to appear.","wpresidence-core")
                        ),

                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Map","wpresidence-core"),
                            "param_name"    =>  "map",
                            "value"         =>  esc_html__("Map","wpresidence-core"),
                            "description"   =>  esc_html__("Map label. Set it blank if you don't want it to appear. Remember to have only one map per property page to avoid conflicts.","wpresidence-core")
                        ),
                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Walkscore","wpresidence-core"),
                            "param_name"    =>  "walkscore",
                            "value"         =>  esc_html__("Walkscore","wpresidence-core"),
                            "description"   =>  esc_html__("Walkscore Label. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),
                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Floor Plans","wpresidence-core"),
                            "param_name"    =>  "floor_plans",
                            "value"         => esc_html__("Floor Plans","wpresidence-core"),
                            "description"   =>  esc_html__("Floor Plans Label. Set it blank if you don't want to appear.","wpresidence-core")
                        ),
                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Page Views","wpresidence-core"),
                            "param_name"    =>  "page_views",
                            "value"         =>  esc_html__("Page Views","wpresidence-core"),
                            "description"   =>  esc_html__("Page Views. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),

                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Virtual Tour","wpresidence-core"),
                            "param_name"    =>  "virtual_tour",
                            "value"         =>  esc_html__("Virtual Tour","wpresidence-core"),
                            "description"   =>  esc_html__("Virtual Tour. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),

                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Yelp Details","wpresidence-core"),
                            "param_name"    =>  "yelp_details",
                            "value"         =>  esc_html__("Yelp Views","wpresidence-core"),
                            "description"   =>  esc_html__("Yelp Views. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),

                        array(
                            'type' => 'css_editor',
                            'heading' => esc_html__( 'Css', 'wpresidence-core' ),
                            'param_name' => 'css',
                            'group' => esc_html__( 'Design options', 'wpresidence-core' ),
                        ),
                    )

                   )

                );




            vc_map(
                array(
                    "name" => esc_html__("Property Page Only - Details as Tabs","wpresidence-core"),//done
                    "base" => "estate_property_page_tab",
                    "class" => "",
                    "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                    'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                    'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                    'weight'=>99,
                    'icon'   =>'wpestate_vc_logo2',
                    'description'=>'',

                    "params" => array(


                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Description","wpresidence-core"),
                            "param_name"    =>  "description",
                            "value"         =>  esc_html__("Description","wpresidence-core"),
                            "description"   =>  esc_html__("Description Label in the tab. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),

                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Property Address","wpresidence-core"),
                            "param_name"    =>  "property_address",
                            "value"         =>   esc_html__("Property Address","wpresidence-core"),
                            "description"   =>  esc_html__("Property Address Label in the tab. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),

                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Property Details","wpresidence-core"),
                            "param_name"    =>  "property_details",
                            "value"         =>   esc_html__("Property Details","wpresidence-core"),
                            "description"   =>  esc_html__("Property Details Label in the tab. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),

                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Amenities and Features","wpresidence-core"),
                            "param_name"    =>  "amenities_features",
                            "value"         =>  esc_html__("Amenities and Features","wpresidence-core"),
                            "description"   =>  esc_html__("Amenities and Features Label in the tab. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),

                         array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Map","wpresidence-core"),
                            "param_name"    =>  "map",
                            "value"         =>  esc_html__("Map","wpresidence-core"),
                            "description"   =>  esc_html__("Map label in the tab.  Set it blank if you don't want it to appear. Remember to have only one map per property page to avoid conflicts.","wpresidence-core")
                        ),
                          array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Walkscore","wpresidence-core"),
                            "param_name"    =>  "walkscore",
                            "value"         =>  esc_html__("Walkscore","wpresidence-core"),
                            "description"   =>  esc_html__("Walkscore Label in the tab. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),
                          array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Floor Plans","wpresidence-core"),
                            "param_name"    =>  "floor_plans",
                            "value"         => esc_html__("Floor Plans","wpresidence-core"),
                            "description"   =>  esc_html__("Floor Plans Label in the tab. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),
                          array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Page Views","wpresidence-core"),
                            "param_name"    =>  "page_views",
                            "value"         =>  esc_html__("Page Views","wpresidence-core"),
                            "description"   =>  esc_html__("Page Views in the tab. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),
                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Virtual Tour","wpresidence-core"),
                            "param_name"    =>  "virtual_tour",
                            "value"         =>  esc_html__("Virtual Tour","wpresidence-core"),
                            "description"   =>  esc_html__("Virtual Tour in the tab. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),

                        array(
                            "type"          =>  "textfield",
                            "holder"        =>  "div",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Yelp Details","wpresidence-core"),
                            "param_name"    =>  "yelp_details",
                            "value"         =>  esc_html__("Yelp Views","wpresidence-core"),
                            "description"   =>  esc_html__("Yelp Views in the tab. Set it blank if you don't want it to appear.","wpresidence-core")
                        ),
                    )

                   )

                );


            $random_pick=array('no','yes');
            $alignment_type=array('vertical','horizontal');
              $featured_listings=array('no','yes');
              $sort_options = array();
                        if( function_exists('wpestate_listings_sort_options_array')){
                          $sort_options			= wpestate_listings_sort_options_array();
                        }
            vc_map(
                array(
                    "name" => esc_html__("Recent properties with filters","wpresidence-core"),//done
                    'description'=>esc_html__('Recent properties with filters','wpresidence-core'),
                    "base" => "filter_list",
                    "class" => "",
                    "category" => esc_html__('Content','wpresidence-core'),
                    'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                    'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                    'weight'=>100,
                    'icon'   =>'wpestate_vc_logo',
             
                    "params" => array(
                        array(
                            "type" => "autocomplete",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Type Category names","wpresidence-core"),
                            "param_name" => "category_ids",
                            "value" => "",
                            "description" => esc_html__("list of category id's sepearated by comma","wpresidence-core")   ,

                            'settings' => array(
                                'multiple' => false,
                                'sortable' => false,
                                'min_length' => 1,
                                'no_hide' => true, // In UI after select doesn't hide an select list
                                'groups' => false, // In UI show results grouped by groups
                                'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                                'display_inline' => true, // In UI show results inline view
                                'values' => $property_category_values,
                            ),
                    ),
                    array(
                            "type" => "autocomplete",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Type Action names","wpresidence-core"),
                            "param_name" => "action_ids",
                            "value" => "",
                            "description" => esc_html__("list of action ids separated by comma (*only for properties)","wpresidence-core"),

                            'settings' => array(
                                'multiple' => true,
                                'sortable' => true,
                                'min_length' => 1,
                                'no_hide' => true, // In UI after select doesn't hide an select list
                                'groups' => false, // In UI show results grouped by groups
                                'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                                'display_inline' => true, // In UI show results inline view
                                'values' => $property_action_category_values,

                            ),
                    ),
                    array(
                            "type" => "autocomplete",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Type City names","wpresidence-core"),
                            "param_name" => "city_ids",
                            "value" => "",
                            "description" => esc_html__("list of city ids separated by comma (*only for properties)","wpresidence-core"),

                            'settings' => array(
                                'multiple' => true,
                                'sortable' => true,
                                'min_length' => 1,
                                'no_hide' => true, // In UI after select doesn't hide an select list
                                'groups' => false, // In UI show results grouped by groups
                                'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                                'display_inline' => true, // In UI show results inline view
                                'values' => $property_city_values,

                            ),
                    ) ,
                    array(
                            "type" => "autocomplete",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Type Area names","wpresidence-core"),
                            "param_name" => "area_ids",
                            "value" => "",
                            "description" => esc_html__("list of area ids separated by comma (*only for properties)","wpresidence-core"),

                            'settings' => array(
                                'multiple' => true,
                                'sortable' => true,
                                'min_length' => 1,
                                'no_hide' => true, // In UI after select doesn't hide an select list
                                'groups' => false, // In UI show results grouped by groups
                                'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                                'display_inline' => true, // In UI show results inline view
                                'values' => $property_area_values,
                            ),
                    ),

                array(
                            "type" => "autocomplete",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Type State names","wpresidence-core"),
                            "param_name" => "state_ids",
                            "value" => "",
                            "description" => esc_html__("list of state ids separated by comma (*only for properties)","wpresidence-core"),

                            'settings' => array(
                                'multiple' => true,
                                'sortable' => true,
                                'min_length' => 1,
                                'no_hide' => true, // In UI after select doesn't hide an select list
                                'groups' => false, // In UI show results grouped by groups
                                'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                                'display_inline' => true, // In UI show results inline view
                                'values' => $property_county_state_values,
                            ),
                    ),
                        
                    array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("No of items","wpresidence-core"),
                            "param_name" => "number",
                            "value" => 4,
                            "description" => esc_html__("how many items","wpresidence-core")
                    ) ,
                    array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("No of items per row","wpresidence-core"),
                            "param_name" => "rownumber",
                            "value" => 4,
                            "description" => esc_html__("The number of items per row","wpresidence-core")
                    ) ,
                    array(
                            "type" => "dropdown",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Vertical or horizontal ?","wpresidence-core"),
                            "param_name" => "align",
                            "value" => $alignment_type,
                            "description" => esc_html__("What type of alignment (vertical or horizontal) ?","wpresidence-core")
                    ),
                    array(
                            "type" => "dropdown",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Show featured listings only?","wpresidence-core"),
                            "param_name" => "show_featured_only",
                            "value" => $featured_listings,
                            "description" => esc_html__("Show featured listings only? (yes/no)","wpresidence-core")        ,

                    ),
                      array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Sort By","wpresidence-core"),
                        "param_name" => "sort_by",
                        "value" => array_flip($sort_options),

                        "description" => esc_html__("sort items by","wpresidence-core")
                    ),
                    )

                   )

                );






                $featured_prop_type         =   array(1=>1,2=>2);
                vc_map(
                    array(
                        "name" => esc_html__("Properties Slider","wpresidence-core"),//done
                        "base" => "slider_properties",
                        "class" => "",
                        "category" => esc_html__('Content','wpresidence-core'),
                        'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                        'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                        'weight'=>100,
                        'icon'   =>'wpestate_vc_logo',
                        'description'=>esc_html__('Properties Slider','wpresidence-core'),

                        "params" => array(

                            array(
                                "type" => "textfield",
                                "holder" => "div",
                                "class" => "",
                                "heading" => esc_html__("Properties IDs separated by commna","wpresidence-core"),
                                "param_name" => "propertyid",
                                "value" => "",
                                "description" =>esc_html__("Properties IDs separated by commna","wpresidence-core"),
                            ),

                            array(
                                "type" => "dropdown",
                                "holder" => "div",
                                "class" => "",
                                "heading" => esc_html__("Design Type","wpresidence-core"),
                                "param_name" => "design_type",
                                "value" =>  $featured_prop_type,
                                "description" => esc_html__("type 1 or 2","wpresidence-core")
                          )

                        )

                       )

            );


            vc_map(
            array(
                "name" => esc_html__("Google Map with Property Marker","wpresidence-core"),//done
                "base" => "property_page_map",
                "class" => "",
                "category" => esc_html__('Content','wpresidence-core'),
                'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                'weight'=>100,
                'icon'   =>'wpestate_vc_logo',
                'description'=>esc_html__('Google Map with Property Marker','wpresidence-core'),

                "params" => array(

                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Property ID","wpresidence-core"),
                        "param_name" => "propertyid",
                        "value" => "",
                        "description" => esc_html__("Add the ID of the property you want to show","wpresidence-core")
                    ),




                )

               )

            );

        vc_map(
            array(
                "name" => esc_html__("Listings per Agent, Agency or Developer","wpresidence-core"),//done
                "base" => "listings_per_agent",
                "class" => "",
                "category" => esc_html__('Content','wpresidence-core'),
                'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                'weight'=>100,
                'icon'   =>'wpestate_vc_logo',
                'description'=>esc_html__('Listings per Agent, Agency or Developer','wpresidence-core'),

                "params" => array(

                   array(
                        "type" => "autocomplete",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Type Agent, Agency or Developert name","wpresidence-core"),
                        "param_name" => "agentid",
                        "value" => "",
                        "description" => esc_html__("Select one Agent, Agency or Developer","wpresidence-core"),
                        'settings' => array(
                                        'multiple' => false,
                                        'sortable' => true,
                                        'min_length' => 1,
                                        'no_hide' => true,
                                        'groups' => false,
                                        'unique_values' => true,
                                        'display_inline' => true,
                                        'values' => $agent_array,

                                )  ,
                    ),



                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Number of Listings","wpresidence-core"),
                        "param_name" => "nooflisting",
                        "value" => "",
                         "description" => esc_html__("Number of Listings to display","wpresidence-core")

                    ),

                )

               )
             );




             $list_cities_or_areas=array(1,2);
             vc_map( array(
                "name" => esc_html__( "Display Categories","wpresidence-core"),//done
                "base" => "places_list",
                "class" => "",
                "category" => esc_html__( 'Content','wpresidence-core'),
                'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                'weight'=>100,
                'icon'   =>'wpestate_vc_logo',
                'description'=>esc_html__( 'Display Categories','wpresidence-core'),
                "params" => array(
                    array(
                        "type" => "autocomplete",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__( "Type Categories,Actions,Cities,Areas,Neighborhoods or County name you want to show","wpresidence-core"),
                        "param_name" => "place_list",
                        "value" => "",
                        "description" => esc_html__( "Type Categories,Actions,Cities,Areas,Neighborhoods or County name you want to show","wpresidence-core"),
                        'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true, // In UI after select doesn't hide an select list
                            'groups' => false, // In UI show results grouped by groups
                            'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                            'display_inline' => true, // In UI show results inline view
                            'values' => $all_tax,

                        )  ,
                    )  ,
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__( "Items per row","wpresidence-core"),
                        "param_name" => "place_per_row",
                        "value" => "4",
                        "description" => esc_html__( "How many items listed per row? For type 2 use only 1.","wpresidence-core")
                    ),
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__( "Type","wpresidence-core"),
                        "param_name" => "place_type",
                        "value" => $list_cities_or_areas,
                        "description" => esc_html__( "Select Item Type 1/2?","wpresidence-core")
                    ),
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__( "Item Height","wpresidence-core"),
                        "param_name" => "item_height",
                        "value" => "350",
                        "description" => esc_html__( "Extra Class Name","wpresidence-core")
                    ),
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__( "Extra Class Name","wpresidence-core"),
                        "param_name" => "extra_class_name",
                        "value" => "",
                        "description" => esc_html__( "Extra Class Name","wpresidence-core")
                    )
                )
            )
            );

            $property_category_agent='';
            if(isset($out_agent_tax_array['property_category_agent'])){
                $property_category_agent= $out_agent_tax_array['property_category_agent'];
            }

            $property_action_category_agent='';
            if( isset($out_agent_tax_array['property_action_category_agent'])){
                $property_action_category_agent=$out_agent_tax_array['property_action_category_agent'];
            }

            $property_city_agent='';
            if(isset( $out_agent_tax_array['property_city_agent'])){
                $property_city_agent= $out_agent_tax_array['property_city_agent'];
            }

            $property_area_agent='';
            if( isset( $out_agent_tax_array['property_area_agent'])){
               $property_area_agent= $out_agent_tax_array['property_area_agent'];
            }


            $random_pick=array('no','yes');
            vc_map(
            array(
               "name" => esc_html__("List Agents","wpresidence-core"),//done
               "base" => "list_agents",
               "class" => "",
               "category" => esc_html__('Content','wpresidence-core'),
               'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
               'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
               'weight'=>100,
               'icon'   =>'wpestate_vc_logo',
               'description'=>esc_html__('Agent Lists','wpresidence-core'),
               "params" => array(
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Title","wpresidence-core"),
                        "param_name" => "title",
                        "value" => "",
                        "description" => esc_html__("Section Title","wpresidence-core")
                    ),

                    array(
                        "type" => "autocomplete",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Type Category names","wpresidence-core"),
                        "param_name" => "category_ids",
                        "value" => "",
                        "description" => esc_html__("list of agent category names","wpresidence-core"),
                                        'settings' => array(
                                        'multiple' => true,
                                        'sortable' => true,
                                        'min_length' => 1,
                                        'no_hide' => true,
                                        'groups' => false,
                                        'unique_values' => true,
                                        'display_inline' => true,
                                        'values' => $property_category_agent,
                                ),
                  ),
                     array(
                     "type" => "autocomplete",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Type Action names","wpresidence-core"),
                     "param_name" => "action_ids",
                     "value" => "",
                     "description" => esc_html__("list of agent action names","wpresidence-core"),
					 'settings' => array(
						'multiple' => true,
						'sortable' => true,
						'min_length' => 1,
						'no_hide' => true,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'values' => $property_action_category_agent,

					),
                  ),
                   array(
                     "type" => "autocomplete",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Type City names","wpresidence-core"),
                     "param_name" => "city_ids",
                     "value" => "",
                     "description" => esc_html__("list of agent city names","wpresidence-core"),
					 'settings' => array(
						'multiple' => true,
						'sortable' => true,
						'min_length' => 1,
						'no_hide' => true,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'values' => $property_city_agent,

					),
                  ),
                    array(
                     "type" => "autocomplete",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Type Area names","wpresidence-core"),
                     "param_name" => "area_ids",
                     "value" => "",
                     "description" => esc_html__("list of agent area names","wpresidence-core"),
					 'settings' => array(
						'multiple' => true,
						'sortable' => true,
						'min_length' => 1,
						'no_hide' => true,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'values' => $property_area_agent,

					),
                  ),
                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("No of items","wpresidence-core"),
                     "param_name" => "number",
                     "value" => 4,
                     "description" => esc_html__("how many items","wpresidence-core")
                  ) ,
                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("No of items per row","wpresidence-core"),
                     "param_name" => "rownumber",
                     "value" => 4,
                     "description" => esc_html__("The number of items per row","wpresidence-core")
                  ),
                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Link to global listing","wpresidence-core"),
                     "param_name" => "link",
                     "value" => "",
                     "description" => esc_html__("link to global listing","wpresidence-core")
                  ) ,array(
                     "type" => "dropdown",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Random Pick (yes/no) ","wpresidence-core"),
                     "param_name" => "random_pick",
                     "value" => $random_pick,
                     "description" => esc_html__("Choose if agents should display randomly on page refresh. ","wpresidence-core")
                  )
               )
            )
            );
            $featured_listings=array('no','yes');
            $items_type=array('properties','articles','agents');
            $arrow_type=array('top','sideways');
            $sort_options = array();
            if( function_exists('wpestate_listings_sort_options_array')){
              $sort_options			= wpestate_listings_sort_options_array();
            }
            vc_map(
            array(
                "name" => esc_html__("Recent Items Slider","wpresidence-core"),//done
                "base" => "slider_recent_items",
                "class" => "",
                "category" => esc_html__('Content','wpresidence-core'),
                'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                'weight'=>100,
                'icon'   =>'wpestate_vc_logo',
                'description'=>esc_html__('Recent Items Slider Shortcode','wpresidence-core'),
                "params" => array(
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Title","wpresidence-core"),
                        "param_name" => "title",
                        "value" => "",
                        "description" => esc_html__("Section Title","wpresidence-core")
                   ),
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("What type of items","wpresidence-core"),
                        "param_name" => "type",
                        "value" => $items_type,
                        "description" => esc_html__("list properties or articles","wpresidence-core")
                    ),

                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Slider Navigation Arrows Position","wpresidence-core"),
                        "param_name" => "arrows",
                        "value" => $arrow_type,
                        "description" => esc_html__("list properties or articles","wpresidence-core")
                    ),


                    array(
                        "type" => "autocomplete",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Category Id's","wpresidence-core"),
                        "param_name" => "category_ids",
                        "value" => "",
                        "description" => esc_html__("list of category id's sepearated by comma (*only for properties)","wpresidence-core"),
            						"dependency" => array(
                  						"element" => "type",
                  						"value" => "properties"
                					),
						 'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true,
                            'groups' => false,
                            'unique_values' => true,
                            'display_inline' => true,
                            'values' => $property_category_values,

                        )  ,
                    ),
                    array(
                        "type" => "autocomplete",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Action Id's","wpresidence-core"),
                        "param_name" => "action_ids",
                        "value" => "",
                        "description" => esc_html__("list of action ids separated by comma (*only for properties)","wpresidence-core"),
						"dependency" => array(
      						"element" => "type",
      						"value" => "properties"
    					),
						 'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true,
                            'groups' => false,
                            'unique_values' => true,
                            'display_inline' => true,
                            'values' => $property_action_category_values,

						),
                    ),
                    array(
                        "type" => "autocomplete",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("City Id's ","wpresidence-core"),
                        "param_name" => "city_ids",
                        "value" => "",
                        "description" => esc_html__("list of city ids separated by comma (*only for properties)","wpresidence-core"),
            						"dependency" => array(
                  						"element" => "type",
                  						"value" => "properties"
                					),
						'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true,
                            'groups' => false,
                            'unique_values' => true,
                            'display_inline' => true,
                            'values' => $property_city_values,

						),
                    ),
                    array(
                       "type" => "autocomplete",
                       "holder" => "div",
                       "class" => "",
                       "heading" => esc_html__("Area Id's","wpresidence-core"),
                       "param_name" => "area_ids",
                       "value" => "",
                       "description" => esc_html__("list of area ids separated by comma (*only for properties)","wpresidence-core"),
					    "dependency" => array(
      						"element" => "type",
      						"value" => "properties"
    					),
						'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true,
                            'groups' => false,
                            'unique_values' => true,
                            'display_inline' => true,
                            'values' => $property_area_values,

						),
                    ),
                    array(
                       "type" => "autocomplete",
                       "holder" => "div",
                       "class" => "",
                       "heading" => esc_html__("County/State Id's","wpresidence-core"),
                       "param_name" => "state_ids",
                       "value" => "",
                       "description" => esc_html__("list of county/State ids separated by comma (*only for properties)","wpresidence-core"),
					   "dependency" => array(
      						"element" => "type",
      						"value" => "properties"
    					),
						'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true,
                            'groups' => false,
                            'unique_values' => true,
                            'display_inline' => true,
                            'values' => $property_county_state_values,

						),
                    ),
                      array(
                       "type" => "autocomplete",
                       "holder" => "div",
                       "class" => "",
                       "heading" => esc_html__("Type Property Status","wpresidence-core"),
                       "param_name" => "status_ids",
                       "value" => "",
                       "description" => esc_html__("list of property status ids separated by comma (*only for properties)","wpresidence-core"),
                       "dependency" => array(
        									"element" => "type",
        									"value" => "properties"
      								),
					                 'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true, // In UI after select doesn't hide an select list
                            'groups' => false, // In UI show results grouped by groups
                            'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                            'display_inline' => true, // In UI show results inline view
                            'values' => $property_status_values,

                    ),
                    ),


                    array(
                       "type" => "textfield",
                       "holder" => "div",
                       "class" => "",
                       "heading" => esc_html__("No of items","wpresidence-core"),
                       "param_name" => "number",
                       "value" => 4,
                       "description" => esc_html__("how many items","wpresidence-core")
                    ),

                    array(
                       "type" => "dropdown",
                       "holder" => "div",
                       "class" => "",
                       "heading" => esc_html__("Show featured listings only?","wpresidence-core"),
                       "param_name" => "show_featured_only",
                       "value" => $featured_listings,
                       "description" => esc_html__("Show featured listings only? (yes/no)","wpresidence-core"),
                       "dependency" => array(
                						"element" => "type",
                						"value" => "properties"
              					),
                    ),
                    array(
                       "type" => "textfield",
                       "holder" => "div",
                       "class" => "",
                       "heading" => esc_html__("Auto scroll period","wpresidence-core"),
                       "param_name" => "autoscroll",
                       "value" => "0",
                       "description" => esc_html__("Auto scroll period in seconds - 0 for manual scroll, 1000 for 1 second, 2000 for 2 seconds and so on.","wpresidence-core")
                    )
                    ,  array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Sort By","wpresidence-core"),
                        "param_name" => "sort_by",
                        "value" => array_flip($sort_options),
                        "dependency" => array(
                                        "element" => "type",
                                        "value" => "properties"
                                    ),
                        "description" => esc_html__("sort items by","wpresidence-core")
                    ),

                )
            )
            );







              $image_efect=array('yes','no');
              vc_map( array(
               "name" => esc_html__("Icon content box","wpresidence-core"),//done
               "base" => "icon_container",
               "class" => "",
               "category" => esc_html__('Content','wpresidence-core'),
               'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
               'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
               'weight'=>100,
                'icon'   =>'wpestate_vc_logo',
                'description'=>esc_html__('Icon Content Box Shortcode','wpresidence-core'),
               "params" => array(
                  array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Box Title","wpresidence-core"),
                     "param_name" => "title",
                     "value" => "Title",
                     "description" => esc_html__("Box Title goes here","wpresidence-core")
                  ),
                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Image url","wpresidence-core"),
                     "param_name" => "image",
                     "value" => "",
                     "description" => esc_html__("Image or Icon url","wpresidence-core")
                  ),
                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Content of the box","wpresidence-core"),
                     "param_name" => "content_box",
                     "value" => "Content of the box goes here",
                     "description" => esc_html__("Content of the box goes here","wpresidence-core")
                  )
                  ,
                   array(
                     "type" => "dropdown",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Image Effect","wpresidence-core"),
                     "param_name" => "image_effect",
                     "value" => $image_efect,
                     "description" => esc_html__("Use image effect? yes/no","wpresidence-core")
                  ) ,
                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Link","wpresidence-core"),
                     "param_name" => "link",
                     "value" => "",
                     "description" => esc_html__("The link with http:// in front","wpresidence-core")
                  )

               )
            ) );




              vc_map(
                   array(
                   "name" => esc_html__("Spacer","wpresidence-core"),
                   "base" => "spacer",
                   "class" => "",
                   "category" => esc_html__('Content','wpresidence-core'),
                   'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                   'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                   'weight'=>102,
                    'icon'   =>'wpestate_vc_logo',
                    'description'=>esc_html__('Spacer Shortcode','wpresidence-core'),
                   "params" => array(
                       array(
                         "type" => "textfield",
                         "holder" => "div",
                         "class" => "",
                         "heading" => esc_html__("Spacer Type","wpresidence-core"),
                         "param_name" => "type",
                         "value" => "1",
                         "description" => esc_html__("Space Type : 1 with no middle line, 2 with middle line","wpresidence-core")
                      )   ,
                       array(
                         "type" => "textfield",
                         "holder" => "div",
                         "class" => "",
                         "heading" => esc_html__("Space height","wpresidence-core"),
                         "param_name" => "height",
                         "value" => "40",
                         "description" => esc_html__("Space height in px","wpresidence-core")
                      )
                   )
                )
            );


            $featured_pack_sh=array('no','yes');
              vc_map(
                   array(
                   "name" => esc_html__("Membership Package","wpresidence-core"),
                   "base" => "estate_membership_packages",

                   "class" => "",
                   "category" => esc_html__('Content','wpresidence-core'),
                   'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                   'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                   'weight'=>102,
                   'icon'   =>'wpestate_vc_logo',
                   'description'=>esc_html__('Membership Package','wpresidence-core'),
                   "params" => array(
                       array(
                            "type" => "autocomplete",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Type Package name","wpresidence-core"),
                            "param_name" => 'package_id',
                            "value" => "",
                            "description" => esc_html__("Select only one Package","wpresidence-core"),
							'settings' => array(
								'multiple' => false,
								'sortable' => true,
								'min_length' => 1,
								'no_hide' => true,
								'groups' => false,
								'unique_values' => true,
								'display_inline' => true,
								'values' => $membership_packages,

							)  ,
                           ),

                        array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Content of the package box","wpresidence-core"),
                            "param_name" => "package_content",
                            "value" => "",
                            "description" => esc_html__("Add content for the package","wpresidence-core")
                       ),
                       array(
                            "type" => "dropdown",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Make package featured","wpresidence-core"),
                            "param_name" => "pack_featured_sh",
                            "value" => $featured_pack_sh,
                            "description" => esc_html__("Make package featured (no/yes)","wpresidence-core")
                        ),
                   )
                )
            );
                    vc_map(
                    array(
                    "name" => esc_html__("Featured Agency / Developer shortcode","wpresidence-core"),
                    "base" => "estate_featured_user_role",
                    "class" => "",
                    "category" => esc_html__('Content','wpresidence-core'),
                    'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                    'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                    'weight'=>102,
                    'icon'   =>'wpestate_vc_logo',
                    'description'=>esc_html__('Featured Developer / Agency','wpresidence-core'),
                    "params" => array(
                        array(
                            "type" => "autocomplete",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Type Agency or Developer name","wpresidence-core"),
                            "param_name" => 'user_role_id',
                            "value" => "",
                            "description" => esc_html__("Select one Agency or Developer","wpresidence-core"),
							'settings' => array(
								'multiple' => false,
								'sortable' => true,
								'min_length' => 1,
								'no_hide' => true,
								'groups' => false,
								'unique_values' => true,
								'display_inline' => true,
								'values' => $agency_developers_array,

							)  ,
                       ),
                        array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Status Text Label","wpresidence-core"),
                            "param_name" => "status",
                            "value" => "",
                            "description" => esc_html__("Status Text Label","wpresidence-core")
                        ),
                        array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Image","wpresidence-core"),
                            "param_name" => "user_shortcode_imagelink",
                            "value" => "",
                            "description" => esc_html__("Path to an image of your choice (best size 300px  x 300px) ","wpresidence-core")
                        ),
                    )
                )
            );





            $alignment_type=array('vertical','horizontal');
            vc_map( array(
               "name" => esc_html__("List items by ID","wpresidence-core"),//done
               "base" => "list_items_by_id",
               "class" => "",
               "category" => esc_html__('Content','wpresidence-core'),
               'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
               'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
               'weight'=>100,
                'icon'   =>'wpestate_vc_logo',
                'description'=>esc_html__('List Items by ID Shortcode','wpresidence-core'),
               "params" => array(
                    array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Title","wpresidence-core"),
                     "param_name" => "title",
                     "value" => "",
                     "description" => esc_html__("Section Title","wpresidence-core")
                  ),
                  array(
                     "type" => "dropdown",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("What type of items","wpresidence-core"),
                     "param_name" => "type",
                     "value" => $items_type,
                     "description" => esc_html__("List properties or articles","wpresidence-core")
                  ),
                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Items IDs","wpresidence-core"),
                     "param_name" => "ids",
                     "value" => "",
                     "description" => esc_html__("List of IDs separated by comma","wpresidence-core")
                  ),
                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("No of items","wpresidence-core"),
                     "param_name" => "number",
                     "value" => "3",
                     "description" => esc_html__("How many items do you want to show ?","wpresidence-core")
                  ) ,

                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("No of items per row","wpresidence-core"),
                     "param_name" => "rownumber",
                     "value" => 4,
                     "description" => esc_html__("The number of items per row","wpresidence-core")
                  ) ,
                   array(
                     "type" => "dropdown",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Vertical or horizontal ?","wpresidence-core"),
                     "param_name" => "align",
                     "value" => $alignment_type,
                     "description" => esc_html__("What type of alignment (vertical or horizontal) ?","wpresidence-core")
                  ),
                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Link to global listing","wpresidence-core"),
                     "param_name" => "link",
                     "value" => "#",
                     "description" => esc_html__("link to global listing with http","wpresidence-core")
                  )
               )
            ) );


            $testimonials_type=array(1,2,3,4);
            vc_map(
                array(
                    'name'              =>  esc_html__("Testimonial",'wpresidence-core'),
                    'base'              =>  "testimonial",
                    'class'             =>  "",
                    'category'          =>  esc_html__('Content','wpresidence-core'),
                    'admin_enqueue_js'  =>  array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                    'admin_enqueue_css' =>  array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                    'weight'            =>  102,
                    'icon'              =>  'wpestate_vc_logo',
                    'description'       =>  esc_html__('Testiomonial Shortcode','wpresidence-core'),
                    'params'            =>  array(
                        array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Client Name","wpresidence-core"),
                            "param_name" => "client_name",
                            "value" => "Name Here",
                            "description" => esc_html__("Client name here","wpresidence-core")
                        ),
                        array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Title Client","wpresidence-core"),
                            "param_name" => "title_client",
                            "value" => "happy client",
                            "description" => esc_html__("title or client postion ","wpresidence-core")
                        ),
                        array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Image","wpresidence-core"),
                            "param_name" => "imagelinks",
                            "value" => "",
                            "description" => esc_html__("Path to client picture, (best size 120px  x 120px) ","wpresidence-core")
                        ) ,
                        array(
                            "type" => "textarea",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Testimonial Text Here.","wpresidence-core"),
                            "param_name" => "testimonial_text",
                            "value" => "",
                            "description" => esc_html__("Testimonial Text Here. ","wpresidence-core")
                        ) ,
                        array(
                            "type" => "dropdown",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Testimonial Type","wpresidence-core"),
                            "param_name" => "testimonial_type",
                            "value" => $testimonials_type,
                            "description" => esc_html__("Select 1,2,3 or 4","wpresidence-core")
                        ), array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Stars","wpresidence-core"),
                            "param_name" => "stars_client",
                            "value" => "5",
                            "description" => esc_html__("Only for type3: no of stars for reviews (from 1 to 5, increment by 0.5) ","wpresidence-core")
                        ), array(
                            "type" => "textfield",
                            "holder" => "div",
                            "class" => "",
                            "heading" => esc_html__("Title","wpresidence-core"),
                            "param_name" => "testimonial_title",
                            "value" => "Great House",
                            "description" => esc_html__("Only for type3:Testimonial Title ","wpresidence-core")
                        ),
                    )
                )
            );
            $featured_listings=array('no','yes');

            $sort_options = array();
                      if( function_exists('wpestate_listings_sort_options_array')){
                        $sort_options			= wpestate_listings_sort_options_array();
                      }

            vc_map(
            array(
               "name" => esc_html__("Recent Items","wpresidence-core"),//done
               "base" => "recent_items",
               "class" => "",
               "category" => esc_html__('Content','wpresidence-core'),
               'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
               'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
               'weight'=>100,
               'icon'   =>'wpestate_vc_logo',
               'description'=>esc_html__('Recent Items Shortcode','wpresidence-core'),
               "params" => array(
                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Title","wpresidence-core"),
                     "param_name" => "title",
                     "value" => "",
                     "description" => esc_html__("Section Title","wpresidence-core")
                  ),
                  array(
                     "type" => "dropdown",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("What type of items","wpresidence-core"),
                     "param_name" => "type",
                     "value" => $items_type,
                     "description" => esc_html__("list properties or articles","wpresidence-core")
                  ),
                    array(
                     "type" => "autocomplete",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("List of terms for the filter bar (*applies only for properties, not articles/posts)","wpresidence-core"),
                     "param_name" => "control_terms_id",
                     "value" => "",
                     "description" => esc_html__("Terms are items in any property taxonomy(Category, Action, City, Area or State/County)","wpresidence-core"),
                     "dependency" => array(
      									"element" => "type",
      									"value" => "properties"
    								),
					 'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true, // In UI after select doesn't hide an select list
                            'groups' => false, // In UI show results grouped by groups
                            'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                            'display_inline' => true, // In UI show results inline view
                            'values' => $all_tax,

                        )  ,
                  ),
                   array(
                     "type" => "autocomplete",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Type Category names","wpresidence-core"),
                     "param_name" => "category_ids",
                     "value" => "",
                     "description" => esc_html__("list of category id's sepearated by comma","wpresidence-core")   ,
                     "dependency" => array(
      									"element" => "type",
      									"value" => "properties"
    								),
					 'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true, // In UI after select doesn't hide an select list
                            'groups' => false, // In UI show results grouped by groups
                            'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                            'display_inline' => true, // In UI show results inline view
                            'values' => $property_category_values,

                        )  ,
                  ),
                     array(
                     "type" => "autocomplete",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Type Action names","wpresidence-core"),
                     "param_name" => "action_ids",
                     "value" => "",
                     "description" => esc_html__("list of action ids separated by comma (*only for properties)","wpresidence-core"),
                     "dependency" => array(
      									"element" => "type",
      									"value" => "properties"
    								),
					 'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true, // In UI after select doesn't hide an select list
                            'groups' => false, // In UI show results grouped by groups
                            'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                            'display_inline' => true, // In UI show results inline view
                            'values' => $property_action_category_values,

                    ),
                  ),
                   array(
                     "type" => "autocomplete",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Type City names","wpresidence-core"),
                     "param_name" => "city_ids",
                     "value" => "",
                     "description" => esc_html__("list of city ids separated by comma (*only for properties)","wpresidence-core"),
                     "dependency" => array(
      									"element" => "type",
      									"value" => "properties"
    								),
					 'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true, // In UI after select doesn't hide an select list
                            'groups' => false, // In UI show results grouped by groups
                            'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                            'display_inline' => true, // In UI show results inline view
                            'values' => $property_city_values,

                    ),
                  ),
                    array(
                     "type" => "autocomplete",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Type Area names","wpresidence-core"),
                     "param_name" => "area_ids",
                     "value" => "",
                     "description" => esc_html__("list of area ids separated by comma (*only for properties)","wpresidence-core"),
                     "dependency" => array(
      									"element" => "type",
      									"value" => "properties"
    								),
					 'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true, // In UI after select doesn't hide an select list
                            'groups' => false, // In UI show results grouped by groups
                            'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                            'display_inline' => true, // In UI show results inline view
                            'values' => $property_area_values,

                    ),
                  ),
                    array(
                       "type" => "autocomplete",
                       "holder" => "div",
                       "class" => "",
                       "heading" => esc_html__("Type County/State names","wpresidence-core"),
                       "param_name" => "state_ids",
                       "value" => "",
                       "description" => esc_html__("list of county/State ids separated by comma (*only for properties)","wpresidence-core"),
                       "dependency" => array(
        									"element" => "type",
        									"value" => "properties"
      								),
					 'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true, // In UI after select doesn't hide an select list
                            'groups' => false, // In UI show results grouped by groups
                            'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                            'display_inline' => true, // In UI show results inline view
                            'values' => $property_county_state_values,

                    ),
                    ),

                     array(
                       "type" => "autocomplete",
                       "holder" => "div",
                       "class" => "",
                       "heading" => esc_html__("Type Property Status","wpresidence-core"),
                       "param_name" => "status_ids",
                       "value" => "",
                       "description" => esc_html__("list of property status ids separated by comma (*only for properties)","wpresidence-core"),
                       "dependency" => array(
        									"element" => "type",
        									"value" => "properties"
      								),
					 'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true, // In UI after select doesn't hide an select list
                            'groups' => false, // In UI show results grouped by groups
                            'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                            'display_inline' => true, // In UI show results inline view
                            'values' => $property_status_values,

                    ),
                    ),


                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("No of items","wpresidence-core"),
                     "param_name" => "number",
                     "value" => 4,
                     "description" => esc_html__("how many items","wpresidence-core")
                  ) ,
                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("No of items per row","wpresidence-core"),
                     "param_name" => "rownumber",
                     "value" => 4,
                     "description" => esc_html__("The number of items per row","wpresidence-core")
                  ) ,
                   array(
                     "type" => "dropdown",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Vertical or horizontal ?","wpresidence-core"),
                     "param_name" => "align",
                     "value" => $alignment_type,
                     "description" => esc_html__("What type of alignment (vertical or horizontal) ?","wpresidence-core")
                  ),
                  array(
                     "type" => "dropdown",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Show featured listings only?","wpresidence-core"),
                     "param_name" => "show_featured_only",
                     "value" => $featured_listings,
                     "description" => esc_html__("Show featured listings only? (yes/no)","wpresidence-core")        ,
                     "dependency" => array(
      									"element" => "type",
      									"value" => "properties"
    								)
                  ) ,array(
                     "type" => "dropdown",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Random Pick (yes/no) ","wpresidence-core"),
                     "param_name" => "random_pick",
                     "value" => $random_pick,
                     "description" => esc_html__("Will deactivate theme cache and increase loading time. (*only for properties)","wpresidence-core")
                  ),  
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Sort By","wpresidence-core"),
                        "param_name" => "sort_by",
                        "value" => array_flip($sort_options),
                        "dependency" => array(
                                "element" => "type",
                                "value" => "properties"
                        ),

                        "description" => esc_html__("sort items by","wpresidence-core")
                    ),




               )
            )
            );



            vc_map(
            array(
               "name" => esc_html__("Featured Agent","wpresidence-core"),
               "base" => "featured_agent",
               "class" => "",
               "category" => esc_html__('Content','wpresidence-core'),
               'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
               'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
               'weight'=>100,
               'icon'   =>'wpestate_vc_logo',
               'description'=>esc_html__('Featured Agent Shortcode','wpresidence-core'),
               "params" => array(
                  array(
                     "type" => "autocomplete",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Type Agent name","wpresidence-core"),
                     "param_name" => "id",
                     "value" => "",
                     "description" => esc_html__("Select one Agent","wpresidence-core"),
					 'settings' => array(
						'multiple' => false,
						'sortable' => true,
						'min_length' => 1,
						'no_hide' => true,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'values' => $agent_array,

					),
                  ),
                   array(
                     "type" => "textarea",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Notes","wpresidence-core"),
                     "param_name" => "notes",
                     "value" => "",
                     "description" => esc_html__("Notes for featured agent","wpresidence-core")
                  )
               )
            )
            );

            $featured_art_type=array(1,2);
            vc_map(
               array(
               "name" => esc_html__("Featured Article","wpresidence-core"),
               "base" => "featured_article",
               "class" => "",
               "category" => esc_html__('Content','wpresidence-core'),
               'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
               'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
               'weight'=>100,
               'icon'   =>'wpestate_vc_logo',
               'description'=>esc_html__('Featured Article Shortcode','wpresidence-core'),
               "params" => array(
                  array(
                     "type" => "autocomplete",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Type article name","wpresidence-core"),
                     "param_name" => "id",
                     "value" => "",
                     "description" => esc_html__("Select one article","wpresidence-core"),
					 'settings' => array(
						'multiple' => false,
						'sortable' => true,
						'min_length' => 1,
						'no_hide' => true,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'values' => $article_array,

					),
                  ),
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Featured text (for type1)","wpresidence-core"),
                        "param_name" => "second_line",
                        "value" => "",
                        "description" => esc_html__("featured text","wpresidence-core")
                   ),
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Design Type","wpresidence-core"),
                        "param_name" => "design_type",
                        "value" =>  $featured_art_type,
                        "description" => esc_html__("type 1 or 2","wpresidence-core")
                  )
               )
            )
            );
            $featured_prop_type=array(1,2,3,4,5);
            vc_map(
            array(
               "name" => esc_html__("Featured Property","wpresidence-core"),
               "base" => "featured_property",
               "class" => "",
               "category" => esc_html__('Content','wpresidence-core'),
               'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
               'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
               'weight'=>100,
               'icon'   =>'wpestate_vc_logo',
               'description'=>esc_html__('Featured Property Shortcode','wpresidence-core'),
               "params" => array(
                  array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Property id","wpresidence-core"),
                     "param_name" => "id",
                     "value" => "",
                     "description" => esc_html__("Property id","wpresidence-core")
                  ),
                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Second Line","wpresidence-core"),
                     "param_name" => "sale_line",
                     "value" => "",
                     "description" => esc_html__("Second line text","wpresidence-core")
                  )  ,
                    array(
                     "type" => "dropdown",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Design Type","wpresidence-core"),
                     "param_name" => "design_type",
                     "value" =>  $featured_prop_type,
                     "description" => esc_html__("type 1, 2, 3 or 4","wpresidence-core")
                  )
               )
            )
            );


            vc_map(array(
               "name" => esc_html__("Login Form","wpresidence-core"),
               "base" => "login_form",
               "class" => "",
               "category" => esc_html__('Content','wpresidence-core'),
               'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
               'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
               'weight'=>100,
               'icon'   =>'wpestate_vc_logo',
               'description'=>esc_html__('Login / Register Form Shortcode','wpresidence-core'),
            )
            );


            vc_map(
                array(
               "name" => esc_html__("Advanced Search","wpresidence-core"),
               "base" => "advanced_search",
               "class" => "",
               "category" => esc_html__('Content','wpresidence-core'),
               'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
               'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
               'weight'=>100,
               'icon'   =>'wpestate_vc_logo',
               'description'=>esc_html__('Advanced Search Shortcode','wpresidence-core'),
               "params" => array(
                   array(
                     "type" => "textfield",
                     "holder" => "div",
                     "class" => "",
                     "heading" => esc_html__("Title","wpresidence-core"),
                     "param_name" => "title",
                     "value" => "",
                     "description" => esc_html__("Section Title","wpresidence-core")
                  ))
            )


            );


            $text_align=array('left','right','center');
            $button_size=array('normal','full');
            vc_map(
                array(
               "name" => esc_html__("Contact Us","wpresidence-core"),
               "base" => "contact_us_form",
               "class" => "",
               "category" => esc_html__('Content','wpresidence-core'),
               'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
               'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
               'weight'=>100,
               'icon'   =>'wpestate_vc_logo',
               'description'=>esc_html__('Contact Form Shortcode','wpresidence-core'),
               "params" => array(
                   array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Text Align","wpresidence-core"),
                        "param_name" => "text_align",
                        "value" => $text_align,
                        "description" => esc_html__("Section Title","wpresidence-core")
                  ),
                    array(
                        "type" => "colorpicker",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Input Background Color","wpresidence-core"),
                        "param_name" => "form_back_color",

                        "description" => esc_html__("Input Background Color","wpresidence-core")
                    ),
                    array(
                        "type" => "colorpicker",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Input Text Color","wpresidence-core"),
                        "param_name" => "form_text_color",

                        "description" => esc_html__("Input text Color","wpresidence-core")
                    ),
                    array(
                        "type" => "colorpicker",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Border Color","wpresidence-core"),
                        "param_name" => "form_border_color",

                        "description" => esc_html__("Border Color","wpresidence-core")
                    ),
                    array(
                        "type" => "dropdown",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__("Button Size","wpresidence-core"),
                        "param_name" => "form_button_size",
                        "value" => $button_size,
                        "description" => esc_html__("Button Size","wpresidence-core")
                    ),

                   )
            )


            );





                // places slider

                vc_map( array(
                "name" => esc_html__( "Display Places Slider","wpresidence-core"),//done
                "base" => "places_slider",
                "class" => "",
                "category" => esc_html__( 'Content','wpresidence-core'),
                'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                'weight'=>100,
                'icon'   =>'wpestate_vc_logo',
                'description'=>esc_html__( 'Display Places Slider','wpresidence-core'),
                "params" => array(

                    array(
                        "type" => "autocomplete",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__( "Type Categories,Actions,Cities,Areas,Neighborhoods or County name you want to show","wpresidence-core"),
                        "param_name" => "place_list",
                        "value" => "",
                        "description" => esc_html__( "Type Categories,Actions,Cities,Areas,Neighborhoods or County name you want to show","wpresidence-core"),
                        'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'min_length' => 1,
                            'no_hide' => true, // In UI after select doesn't hide an select list
                            'groups' => false, // In UI show results grouped by groups
                            'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                            'display_inline' => true, // In UI show results inline view
                            'values' => $all_tax,

                        )  ,
                    )  ,
                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__( "Items per row","wpresidence-core"),
                        "param_name" => "place_per_row",
                        "value" => "3",
                        "description" => esc_html__( "How many items listed per row? For type 2 use only 1.","wpresidence-core")
                    ),


                    array(
                        "type" => "textfield",
                        "holder" => "div",
                        "class" => "",
                        "heading" => esc_html__( "Extra Class Name","wpresidence-core"),
                        "param_name" => "extra_class_name",
                        "value" => "",
                        "description" => esc_html__( "Extra Class Name","wpresidence-core")
                    )
                )
            )
            );




            vc_map(
                array(
                    "name" => esc_html__("Property Page Only - Masonry Gallery ","wpresidence-core"),//done
                    "base" => "estate_property_design_masonary_gallery",
                    "class" => "",
                    "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                    'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                    'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                    'weight'=>99,
                    'icon'   =>'wpestate_vc_logo2',
                    'description'=>'',


                    "params" => array(

                    )

                   )

            );
            
            
            vc_map(
                array(
                    "name" => esc_html__("Property Page Only - Masonry Gallery Type2 ","wpresidence-core"),//done
                    "base" => "estate_property_design_masonary_gallery2",
                    "class" => "",
                    "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                    'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                    'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                    'weight'=>99,
                    'icon'   =>'wpestate_vc_logo2',
                    'description'=>'',


                    "params" => array(

                    )

                   )

            );
            
            
            
            
            
            vc_map(
                array(
                    "name" => esc_html__("Property Page Only - Other Agents","wpresidence-core"),//done
                    "base" => "estate_property_design_other_agents",
                    "class" => "",
                    "category" => esc_html__('WpResidence - Property Page Design','wpresidence-core'),
                    'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                    'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                    'weight'=>99,
                    'icon'   =>'wpestate_vc_logo2',
                    'description'=>'',


                    "params" => array(

                    )

                   )

            );

            // taxonomy list
            $taxonomy_list_type         =   array('category','action category','city','area','county/state','status','features and ammenities');
            $taxonomy_list_type_show    =   array('yes','no');

            vc_map( array(
                "name" => esc_html__( "Category List","wpresidence-core"),//done
                "base" => "taxonomy_list",
                "class" => "",
                "category" => esc_html__( 'Content','wpresidence-core'),
                'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                'weight'=>100,
                'icon'   =>'wpestate_vc_logo',
                'description'=>esc_html__( 'display a list with items in a category','wpresidence-core'),
                "params" => array(

                    array(
                        "type"          =>  "dropdown",
                        "holder"        =>  "div",
                        "class"         =>  "",
                        "heading"       =>  esc_html__("Select category","wpresidence-core"),
                        "param_name"    =>  "taxonomy_list_type",
                        "value"         =>  $taxonomy_list_type,
                        "description"   =>  esc_html__("What type of slider do you use?","wpresidence-core")
                    ),
                    array(
                        "type"          =>  "dropdown",
                        "holder"        =>  "div",
                        "class"         =>  "",
                        "heading"       =>  esc_html__("Show number of listings","wpresidence-core"),
                        "param_name"    =>  "taxonomy_list_type_show",
                        "value"         =>  $taxonomy_list_type_show,
                        "description"   =>  esc_html__("What type of slider do you use?","wpresidence-core")
                    ),
                )
            )
            );

            $map_shortcode_for=array('listings','contact');
            $map_shorcode_show_contact_form=array('yes','no');




            vc_map(
                    array(
                        "name" => esc_html__("Map With Listings or Office location pin?","wpresidence-core"),//done
                        "base" => "full_map",
                        "class" => "",
                        "category" => esc_html__('Content','wpresidence-core'),
                        'admin_enqueue_js' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.js'),
                        'admin_enqueue_css' => array(WPESTATE_PLUGIN_DIR_URL.'/vc_extend/bartag.css'),
                        'weight'=>100,
                        'icon'   =>'wpestate_vc_logo',
                        'description'=>esc_html__('Map with Listings','wpresidence-core'),

                        "params" => array(

                            array(
                                "type"          =>  "dropdown",
                                "holder"        =>  "div",
                                "class"         =>  "",
                                "heading"       =>  esc_html__("Map With Listings or Office location pin?","wpresidence-core"),
                                "param_name"    =>  "map_shortcode_for",
                                "value"         =>  $map_shortcode_for,
                                "description"   =>  esc_html__("Display listings or Office contact location pin ?","wpresidence-core")
                            ),


                            array(
                                "type" => "dropdown",
                                "holder" => "div",
                                "class" => "",
                                "dependency" => array(
                                    "element" => "map_shortcode_for",
                                    "value" => "contact"
                                ),
                                "heading" => esc_html__("Show Contact Details over map ","wpresidence-core"),
                                "param_name" => "map_shorcode_show_contact_form",
                                "value" => $map_shorcode_show_contact_form,
                                "description" => esc_html__("Show Contact Details over map","wpresidence-core")
                            ),


                            array(
                                "type" => "textfield",
                                "holder" => "div",
                                "class" => "",
                                "heading" => esc_html__("Map Height","wpresidence-core"),
                                "param_name" => "map_height",
                                "value" => "",
                                "description" => esc_html__("Map Height","wpresidence-core")
                            ),



                            array(
                                "type" => "autocomplete",
                                "holder" => "div",
                                "class" => "",
                                "heading" => esc_html__("Category Id's","wpresidence-core"),
                                "param_name" => "category_ids",
                                "value" => "",
                                "dependency" => array(
                                    "element" => "map_shortcode_for",
                                    "value" => "listings"
                                ),
                                "description" => esc_html__("list of category id's sepearated by comma (*only for properties)","wpresidence-core"),

                                'settings' => array(
                                            'multiple' => true,
                                            'sortable' => true,
                                            'min_length' => 1,
                                            'no_hide' => true,
                                            'groups' => false,
                                            'unique_values' => true,
                                            'display_inline' => true,
                                            'values' => $property_category_values,
                                )
                            ),
                            array(
                                "type" => "autocomplete",
                                "holder" => "div",
                                "class" => "",
                                "heading" => esc_html__("Action Id's","wpresidence-core"),
                                "param_name" => "action_ids",
                                "value" => "",
                                "dependency" => array(
                                    "element" => "map_shortcode_for",
                                    "value" => "listings"
                                ),
                                "description" => esc_html__("list of action ids separated by comma (*only for properties)","wpresidence-core"),

                                'settings' => array(
                                    'multiple' => true,
                                    'sortable' => true,
                                    'min_length' => 1,
                                    'no_hide' => true,
                                    'groups' => false,
                                    'unique_values' => true,
                                    'display_inline' => true,
                                    'values' => $property_action_category_values,
                                )
                            ),
                            array(
                                "type" => "autocomplete",
                                "holder" => "div",
                                "class" => "",
                                "heading" => esc_html__("City Id's ","wpresidence-core"),
                                "param_name" => "city_ids",
                                "value" => "",
                                "dependency" => array(
                                    "element" => "map_shortcode_for",
                                    "value" => "listings"
                                ),
                                "description" => esc_html__("list of city ids separated by comma (*only for properties)","wpresidence-core"),

                                'settings' => array(
                                    'multiple' => true,
                                    'sortable' => true,
                                    'min_length' => 1,
                                    'no_hide' => true,
                                    'groups' => false,
                                    'unique_values' => true,
                                    'display_inline' => true,
                                    'values' => $property_city_values,
                                    ),
                                ),
                                array(
                                    "type" => "autocomplete",
                                    "holder" => "div",
                                    "class" => "",
                                    "heading" => esc_html__("Area Id's","wpresidence-core"),
                                    "param_name" => "area_ids",
                                    "value" => "",
                                    "dependency" => array(
                                        "element" => "map_shortcode_for",
                                        "value" => "listings"
                                    ),
                                   "description" => esc_html__("list of area ids separated by comma (*only for properties)","wpresidence-core"),

                                    'settings' => array(
                                        'multiple' => true,
                                        'sortable' => true,
                                        'min_length' => 1,
                                        'no_hide' => true,
                                        'groups' => false,
                                        'unique_values' => true,
                                        'display_inline' => true,
                                        'values' => $property_area_values,

                                    ),
                                ),
                                array(
                                    "type" => "autocomplete",
                                    "holder" => "div",
                                    "class" => "",
                                    "heading" => esc_html__("County/State Id's","wpresidence-core"),
                                    "param_name" => "state_ids",
                                    "value" => "",
                                    "dependency" => array(
                                        "element" => "map_shortcode_for",
                                        "value" => "listings"
                                    ),
                                    "description" => esc_html__("list of county/State ids separated by comma (*only for properties)","wpresidence-core"),

                                    'settings' => array(
                                        'multiple' => true,
                                        'sortable' => true,
                                        'min_length' => 1,
                                        'no_hide' => true,
                                        'groups' => false,
                                        'unique_values' => true,
                                        'display_inline' => true,
                                        'values' => $property_county_state_values,

                                    ),
                                ),
                                array(
                                        "type" => "autocomplete",
                                        "holder" => "div",
                                        "class" => "",
                                        "heading" => esc_html__("Type Property Status","wpresidence-core"),
                                        "param_name" => "status_ids",
                                        "value" => "",
                                        "dependency" => array(
                                            "element" => "map_shortcode_for",
                                            "value" => "listings"
                                        ),
                                        "description" => esc_html__("list of property status ids separated by comma (*only for properties)","wpresidence-core"),

                                        'settings' => array(
                                        'multiple' => true,
                                        'sortable' => true,
                                        'min_length' => 1,
                                        'no_hide' => true, // In UI after select doesn't hide an select list
                                        'groups' => false, // In UI show results grouped by groups
                                        'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
                                        'display_inline' => true, // In UI show results inline view
                                        'values' => $property_status_values,

                                        ),

                                ),
                                array(
                                   "type" => "textarea_raw_html",
                                   "holder" => "div",
                                   "class" => "",
                                   "heading" => esc_html__("Map Style","wpresidence-core"),
                                   "param_name" => "map_snazy",
                                   "value" => "",
                                   "description" => esc_html__("Map Style from snazy maps","wpresidence-core")
                               ),

                            )
                    )
            );

        }
    endif;

    if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
        class WPBakeryShortCode_testimonial_slider extends WPBakeryShortCodesContainer {
        }
    }

    if ( class_exists( 'WPBakeryShortCode' ) ) {
        class WPBakeryShortCode_testimonial extends WPBakeryShortCode {
        }
    }

endif;


/**
* custom css data for visual composer sh
*
*
* @since    1.0
* @access   private
*/

function custom_css_wpestate($class_string, $tag) {
  if ($tag =='vc_tabs' || $tag=='vc_tta_tabs' ) {
    $class_string .= ' wpestate_tabs';
  }

  if ($tag =='vc_tour' || $tag=='vc_tta_tour') {
    $class_string .= ' wpestate_tour';
  }

  if ($tag =='vc_accordion'|| $tag=='vc_tta_accordion' ) {
    $class_string .= ' wpestate_accordion';
  }

  if ($tag =='vc_accordion_tab' ) {
    $class_string .= ' wpestate_accordion_tab';
  }

  if ($tag =='vc_carousel' ) {
    $class_string .= ' wpestate_carousel';
  }

  if ($tag =='vc_progress_bar' ) {
    $class_string .= ' wpestate_progress_bar';
  }

  if ($tag =='vc_toggle' ) {
    $class_string .= ' wpestate_toggle';
  }

  if ($tag =='vc_message' ) {
    $class_string .= ' wpestate_message';
  }

  if ($tag =='vc_posts_grid' ) {
    $class_string .= ' wpestate_posts_grid';
  }

  if ($tag =='vc_cta_button' ) {
    $class_string .= ' wpestate_cta_button ';
  }

  if ($tag =='vc_cta_button2' ) {
    $class_string .= ' wpestate_cta_button2 ';
  }



  return $class_string.' '.$tag;
}


// Filter to Replace default css class for vc_row shortcode and vc_column
add_filter('vc_shortcodes_css_class', 'custom_css_wpestate', 10,2);


if( !function_exists('wpestate_limit64') ):
    function wpestate_limit64($stringtolimit){
        return mb_substr($stringtolimit,0,64);
    }
endif;


if( !function_exists('wpestate_limit54') ):
    function wpestate_limit54($stringtolimit){
        return mb_substr($stringtolimit,0,54);
    }
endif;

if( !function_exists('wpestate_limit50') ):
    function wpestate_limit50($stringtolimit){ // 14
        return mb_substr($stringtolimit,0,50);
    }
endif;

if( !function_exists('wpestate_limit45') ):
    function wpestate_limit45($stringtolimit){ // 19
        return mb_substr($stringtolimit,0,45);
    }
endif;

if( !function_exists('wpestate_limit27') ):
    function wpestate_limit27($stringtolimit){ // 27
        return mb_substr($stringtolimit,0,27);
    }
endif;





/**
* genereate membership data for autocomplete
*
*
* @since    1.0
* @access   private
*/

function wpestate_generate_membershiop_packages_shortcodes(){
    $membership_packages    = false;
    if(function_exists('wpestate_request_transient_cache')){
        $membership_packages = wpestate_request_transient_cache ('wpestate_js_composer_membership_packages');
    }


    $mem_labels             = array();
    if($membership_packages === false){
        $args_inner = array(
                'post_type' => 'membership_package',
                'showposts' => -1
        );
        $all_memb_packages = get_posts( $args_inner );
        if( count($all_memb_packages) > 0 ){
                $membership_packages=array();
                foreach( $all_memb_packages as $single_package ){
                    $temp_array=array();
                    $temp_array['label'] = $single_package->post_title;
                    $temp_array['value'] = $single_package->ID;

                    $membership_packages[] = $temp_array;
                    $mem_labels[$single_package->ID]=$single_package->post_title;
                }
        }

        if(function_exists('wpestate_set_transient_cache')){
            wpestate_set_transient_cache('wpestate_js_composer_pack_label',$mem_labels,60*60*4);
            wpestate_set_transient_cache ('wpestate_js_composer_membership_packages',$membership_packages,60*60*4);
        }
    }
    return $membership_packages;
}



/**
* generate category data for autocomplete
*
*
* @since    1.0
* @access   private
*/


function wpestate_generate_category_values_shortcode(){
        global $all_tax;
        global $all_tax_labels;
        if(!is_array($all_tax)){
            $all_tax=array();
            $all_tax_labels=array();
        }


  
        $property_category_values=false;
        if(function_exists('wpestate_request_transient_cache')){
            $property_category_values = wpestate_request_transient_cache ('wpestate_js_composer_property_category');
        }


        if($property_category_values===false){
            $terms_category =   wpestate_get_cached_terms('property_category');
            
            $property_category_values=array();

            foreach($terms_category as $term){

                $temp_array=array();
                $temp_array['label'] = $term->name;
                $temp_array['value'] = $term->term_id;

                $category_array[]=$temp_array;
                $global_categories[]=$temp_array;
                $all_tax[]=$temp_array;
                $all_tax_labels[$term->term_id]=  $term->name;

                // tax based_array
          
                $property_category_values[] = $temp_array;
                
                
            }
            if(function_exists('wpestate_set_transient_cache')){
                wpestate_set_transient_cache('wpestate_js_composer_all_tax',$all_tax,60*60*4);
                wpestate_set_transient_cache('wpestate_js_composer_all_tax_labels',$all_tax_labels,60*60*4);
                wpestate_set_transient_cache('wpestate_js_composer_property_category',$property_category_values,60*60*4);
            }
        }
        return $property_category_values;
}




/**
* generate action category data for autocomplete
*
*
* @since    1.0
* @access   private
*/
function wpestate_generate_action_values_shortcode(){
    global $all_tax;
    global $all_tax_labels;


    $property_action_category_values    = false;
    if(function_exists('wpestate_request_transient_cache')){
        $property_action_category_values = wpestate_request_transient_cache ('wpestate_js_composer_property_action');
    }


    if($property_action_category_values===false){
        $terms_category = wpestate_get_cached_terms('property_action_category');
        $property_action_category_values=array();

        foreach($terms_category as $term){

            $temp_array=array();
            $temp_array['label'] = $term->name;
            $temp_array['value'] = $term->term_id;

            $all_tax[]                      =   $temp_array;
            $all_tax_labels[$term->term_id] =   $term->name;
            $action_array[]                 =   $temp_array;

            // tax based_array
            $property_action_category_values[] = $temp_array;

        }
        if(function_exists('wpestate_set_transient_cache')){
            wpestate_set_transient_cache('wpestate_js_composer_all_tax',$all_tax,60*60*4);
            wpestate_set_transient_cache('wpestate_js_composer_all_tax_labels',$all_tax_labels,60*60*4);
            wpestate_set_transient_cache('wpestate_js_composer_property_action',$property_action_category_values,60*60*4);
        }
    }
    return $property_action_category_values;
}


/**
* generate city data for autocomplete
*
*
* @since    1.0
* @access   private
*/
function wpestate_generate_city_values_shortcode(){
    global $all_tax;
    global $all_tax_labels;
    $property_city_values    = false;
    if(function_exists('wpestate_request_transient_cache')){
        $property_city_values = wpestate_request_transient_cache ('wpestate_js_composer_property_city_values');
    }



    if($property_city_values===false){
        $terms_city =  wpestate_get_cached_terms('property_city');
        $property_city_values=array();

        foreach($terms_city as $term){
            $places[$term->name]= $term->term_id;
            $temp_array=array();
            $temp_array['label'] = $term->name;
            $temp_array['value'] = $term->term_id;
            $all_tax[]=$temp_array;
            $all_tax_labels[$term->term_id]=  $term->name;
            // tax based_array
            $property_city_values[] = $temp_array;
        }


        if(function_exists('wpestate_set_transient_cache')){
            wpestate_set_transient_cache('wpestate_js_composer_all_tax',$all_tax,60*60*4);
            wpestate_set_transient_cache('wpestate_js_composer_all_tax_labels',$all_tax_labels,60*60*4);
            wpestate_set_transient_cache('wpestate_js_composer_property_city_values',$property_city_values,60*60*4);
        }
    }
    return $property_city_values;
}


/**
* generate area data for autocomplete
*
*
* @since    1.0
* @access   private
*/
function wpestate_generate_area_values_shortcode(){
    global $all_tax;
    global $all_tax_labels;
    $property_area_values    = false;
    if(function_exists('wpestate_request_transient_cache')){
        $property_area_values = wpestate_request_transient_cache ('wpestate_js_composer_property_area_values');
    }


    if($property_area_values===false){
        $terms_city =  wpestate_get_cached_terms('property_area');
        $property_area_values=array();

        foreach($terms_city as $term){
            $places[$term->name]= $term->term_id;
            $temp_array=array();
            $temp_array['label'] = $term->name;
            $temp_array['value'] = $term->term_id;
            $all_places[]=$temp_array;
            $area_array[]=$temp_array;
            $all_tax[]=$temp_array;
            $all_tax_labels[$term->term_id]=  $term->name;
            // tax based_array
            $property_area_values[] = $temp_array;

        }
        if(function_exists('wpestate_set_transient_cache')){
            wpestate_set_transient_cache('wpestate_js_composer_all_tax',$all_tax,60*60*4);
            wpestate_set_transient_cache('wpestate_js_composer_all_tax_labels',$all_tax_labels,60*60*4);
            wpestate_set_transient_cache('wpestate_js_composer_property_area_values',$property_area_values,60*60*4);
        }
    }

    return $property_area_values;

}


/**
* generate county data for autocomplete
*
*
* @since    1.0
* @access   private
*/
function wpestate_generate_county_values_shortcode(){

    global $all_tax;
  global $all_tax_labels;
    $property_county_state_values    = false;
    if(function_exists('wpestate_request_transient_cache')){
        $property_county_state_values = wpestate_request_transient_cache ('wpestate_js_composer_property_county_state_values');
    }



    if($property_county_state_values===false){
        $terms_city =  wpestate_get_cached_terms('property_county_state');
        $property_county_state_values=array();
        
        foreach($terms_city as $term){
            $places[$term->name]= $term->term_id;
            $temp_array=array();
            $temp_array['label'] = $term->name;
            $temp_array['value'] = $term->term_id;
            $all_places[]=$temp_array;
            $area_array[]=$temp_array;
            $all_tax[]=$temp_array;
            $all_tax_labels[$term->term_id]=  $term->name;
            // tax based_array
            $property_county_state_values[] = $temp_array;

        }

        if(function_exists('wpestate_set_transient_cache')){
            wpestate_set_transient_cache('wpestate_js_composer_all_tax',$all_tax,60*60*4);
            wpestate_set_transient_cache('wpestate_js_composer_all_tax_labels',$all_tax_labels,60*60*4);
            wpestate_set_transient_cache('wpestate_js_composer_property_county_state_values',$property_county_state_values,60*60*4);
        }
    }

    return $property_county_state_values;

}



/**
* generate county data for autocomplete
*
*
* @since    1.0
* @access   private
*/
function wpestate_generate_status_values_shortcode(){

    global $all_tax;
    global $all_tax_labels;
    $property_status_values    = false;
    if(function_exists('wpestate_request_transient_cache')){
        $property_status_values = wpestate_request_transient_cache ('wpestate_js_composer_property_status_values');
    }



    if($property_status_values===false){
        $terms_city = wpestate_get_cached_terms('property_status');
        
        $property_status_values=array();

        foreach($terms_city as $term){
            $places[$term->name]= $term->term_id;
            $temp_array=array();
            $temp_array['label'] = $term->name;
            $temp_array['value'] = $term->term_id;
            $all_places[]=$temp_array;
            $area_array[]=$temp_array;
            $all_tax[]=$temp_array;
            $all_tax_labels[$term->term_id]=  $term->name;
            // tax based_array
            $property_status_values[] = $temp_array;

        }

        if(function_exists('wpestate_set_transient_cache')){
            wpestate_set_transient_cache('wpestate_js_composer_all_tax',$all_tax,60*60*4);
            wpestate_set_transient_cache('wpestate_js_composer_all_tax_labels',$all_tax_labels,60*60*4);
            wpestate_set_transient_cache('wpestate_js_composer_property_status_values',$property_status_values,60*60*4);
        }
    }

    return $property_status_values;

}


/**
* generate county data for autocomplete
*
*
* @since    1.0
* @access   private
*/
function wpestate_generate_features_values_shortcode(){

    global $all_tax;
    global $all_tax_labels;
    $property_status_values    = false;
    if(function_exists('wpestate_request_transient_cache')){
        $property_status_values = wpestate_request_transient_cache ('wpestate_js_composer_property_status_values');
    }



    if($property_status_values===false){
        $terms_city =  wpestate_get_cached_terms('property_features');
        $property_status_values=array();

        foreach($terms_city as $term){
            $places[$term->name]= $term->term_id;
            $temp_array=array();
            $temp_array['label'] = $term->name;
            $temp_array['value'] = $term->term_id;
            $all_places[]=$temp_array;
            $area_array[]=$temp_array;
            $all_tax[]=$temp_array;
            $all_tax_labels[$term->term_id]=  $term->name;
            // tax based_array
            $property_status_values[] = $temp_array;

        }

        if(function_exists('wpestate_set_transient_cache')){
            wpestate_set_transient_cache('wpestate_js_composer_all_tax',$all_tax,60*60*4);
            wpestate_set_transient_cache('wpestate_js_composer_all_tax_labels',$all_tax_labels,60*60*4);
            wpestate_set_transient_cache('wpestate_js_composer_property_features_values',$property_status_values,60*60*4);
        }
    }

    return $property_status_values;

}




/**
 * Generate agent data for autocomplete functionality.
 *
 * This function retrieves or generates an array of agents (and optionally agencies and developers)
 * for use in autocomplete features. It uses transient caching to improve performance.
 *
 * @since    1.0.0
 * @access   public
 *
 * @param    bool    $only_agents    Optional. Whether to return only agents or include agencies and developers. Default false.
 * @return   array   An array of agents with 'label' (post title) and 'value' (post ID).
 */
function wpestate_return_agent_array($only_agents = false) {
    $cache_key = 'wpestate_js_composer_agent_array';
    if ( function_exists( 'wpestate_set_transient_name_multilang' ) ) {
        $cache_key = wpestate_set_transient_name_multilang( 'wpestate_js_composer_agent_array' );
    }

    $agent_array = get_transient($cache_key);

    $post_types = $only_agents
        ? array('estate_agent')
        : array('estate_agent', 'estate_agency', 'estate_developer');

    if (false === $agent_array) {
        $args = array(
            'post_type'        => $post_types,
            'posts_per_page'   => -1,
            'fields'           => 'ids',
            'suppress_filters' => false,
        );

        $agent_array = array();
        $all_agent_ids = get_posts($args);

        foreach ($all_agent_ids as $post_id) {
            $agent_array[] = array(
                'label' => esc_html(get_the_title($post_id)),
                'value' => absint($post_id)
            );
        }

        set_transient($cache_key, $agent_array, 4 * HOUR_IN_SECONDS);
    }

    return $agent_array;
}

/**
 * Generate article data for autocomplete functionality.
 *
 * This function retrieves or generates an array of articles (posts) for use in
 * autocomplete features. It uses transient caching to improve performance.
 *
 * @since    1.0.0
 * @access   public
 *
 * @return   array    An array of articles with 'label' (post title) and 'value' (post ID).
 */
function wpestate_return_article_array() {
    // Attempt to retrieve cached article data
    $article_array = function_exists('wpestate_request_transient_cache')
        ? wpestate_request_transient_cache('wpestate_js_composer_article_array')
        : false;

    // If cache is empty or expired, generate new article data
    if (false === $article_array) {
        $args = array(
            'post_type'        => 'post',
            'posts_per_page'   => -1,
            'suppress_filters' => false,
            'fields'           => 'ids', // Retrieve only post IDs for better performance
        );

        $article_array = array();
        $all_article_ids = get_posts($args);

        // Generate array of article data
        foreach ($all_article_ids as $post_id) {
            $article_array[] = array(
                'label' => esc_html(get_the_title($post_id)),
                'value' => absint($post_id)
            );
        }

        // Cache the generated article data
        if (function_exists('wpestate_set_transient_cache')) {
            wpestate_set_transient_cache('wpestate_js_composer_article_array', $article_array, 4 * HOUR_IN_SECONDS);
        }
    }

    return $article_array;
}




/*
* Generate agent taxonomy data for autocomplete functionality.
*
* This function retrieves or generates an array of agent-related taxonomies
* for use in autocomplete features. It uses transient caching to improve performance.
*
* @since    1.0.0
* @access   public
*
* @return   array    An array of agent taxonomies with term data.
*/
function wpestate_js_composer_out_agent_tax_array() {
   // Attempt to retrieve cached agent taxonomy data
   $out_agent_tax_array = function_exists('wpestate_request_transient_cache')
       ? wpestate_request_transient_cache('wpestate_js_composer_out_agent_tax_array')
       : false;

   // If cache is empty or expired, generate new agent taxonomy data
   if (false === $out_agent_tax_array) {
       $out_agent_tax_array = array();
       $agent_tax_list = array(
           'property_category_agent',
           'property_action_category_agent',
           'property_city_agent',
           'property_area_agent',
           'property_county_state_agent'
       );

       foreach ($agent_tax_list as $single_agent_tax) {
            $terms = get_terms(array(
                'taxonomy'   => $single_agent_tax,
                'hide_empty' => false,
            ));
           if (!is_wp_error($terms)) {
               $out_agent_tax_array[$single_agent_tax] = array();
               $out_agent_tax_array[$single_agent_tax . '_label'] = array();

               foreach ($terms as $term) {
                   $out_agent_tax_array[$single_agent_tax][] = array(
                       'label' => esc_html($term->name),
                       'value' => absint($term->term_id)
                   );
                   $out_agent_tax_array[$single_agent_tax . '_label'][$term->term_id] = esc_html($term->name);
               }
           }
       }

       // Cache the generated agent taxonomy data
       if (function_exists('wpestate_set_transient_cache')) {
           wpestate_set_transient_cache('wpestate_js_composer_out_agent_tax_array', $out_agent_tax_array, 4 * HOUR_IN_SECONDS);
       }
   }

   return $out_agent_tax_array;
}
?>
