<?php
/*
 *  Plugin Name: WPResidence Theme Core Functionality
 *  Plugin URI:  https://themeforest.net/user/wpestate
 *  Description: Adds mandatory functionality to WPResidence Theme
 *  Version:     5.5.0
 *  Author:      wpestate
 *  Author URI:  https://wpestate.org
 *  License:     GPL2
 *  Text Domain: wpresidence-core
 *  Domain Path: /languages
 *
*/

define('WPESTATE_PLUGIN_URL',  plugins_url() );
define('WPESTATE_PLUGIN_DIR_URL',  plugin_dir_url(__FILE__) );
define('WPESTATE_PLUGIN_PATH',  plugin_dir_path(__FILE__) );
define('WPESTATE_PLUGIN_BASE',  plugin_basename(__FILE__) );

add_action( 'wp_enqueue_scripts', 'wpestate_residence_enqueue_styles' );
add_action( 'admin_enqueue_scripts', 'wpestate_residence_enqueue_styles_admin');
add_action( 'plugins_loaded', 'wpestate_residence_functionality_loaded' );
register_activation_hook( __FILE__, 'wpestate_residence_functionality' );
register_deactivation_hook( __FILE__, 'wpestate_residence_deactivate' );







function wpestate_residence_functionality_loaded(){
    $my_theme = wp_get_theme();
    $version = floatval( $my_theme->get( 'Version' ));
/*
    if($version< 1.4 && $version!=1){
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( 'This plugin requires  WpResidence 1.40 or higher.','wpresidence-core' );
    }
*/
    load_plugin_textdomain( 'wpresidence-core', false, dirname( WPESTATE_PLUGIN_BASE ) . '/languages' );
    wpestate_shortcodes();

    add_action('widgets_init', 'register_wpestate_widgets' );
    add_action('wp_footer', 'wpestate_core_add_to_footer');

}



function wpestate_residence_functionality(){
    wpresidence_create_helper_content();
}

function wpestate_residence_deactivate(){
}


function wpestate_residence_enqueue_styles() {
}


function wpestate_residence_enqueue_styles_admin(){
    wp_enqueue_style(
        'wpresidence-theme-admin',
        WPESTATE_PLUGIN_DIR_URL . 'css/theme-admin.css',
        array(),
        '1.0'
    );

    wp_enqueue_style(
        'redux-elusive-icon',
        Redux_Core::$url . 'assets/css/vendor/elusive-icons' . Redux_Functions::is_min() . '.css',
        array(),
        '2.0.0'
    );

    wp_enqueue_style(
        'wpresidence-theme-2025',
        WPESTATE_PLUGIN_DIR_URL . 'css/wpresidence2025.css',
        array(),
        '1.0'
    );

    wp_enqueue_style(
        'wpresidence-header-metabox',
        WPESTATE_PLUGIN_DIR_URL . 'css/header-metabox.css',
        array(),
        '1.0'
    );

    wp_enqueue_style(
        'wpresidence-pro-list-adv-metabox',
        WPESTATE_PLUGIN_DIR_URL . 'css/pro-list-adv-metabox.css',
        array(),
        '1.0'
    );

    wp_enqueue_style(
        'wpresidence-vc-icons',
        WPESTATE_PLUGIN_DIR_URL . 'vc_extend/bartag.css',
        array(),
        '1.0'
    );

    // Load script that remembers the last active tab for each metabox.
    wp_enqueue_script(
        'wpresidence-tabs-functionality',
        WPESTATE_PLUGIN_DIR_URL . 'admin/js/tabs-functionality.js',
        array('jquery'),
        '1.0',
        true
    );

    wp_enqueue_script(
        'wpresidence-property-actions',
        WPESTATE_PLUGIN_DIR_URL . 'admin/js/property-actions.js',
        array('jquery'),
        time(),
        true
    );

    $screen = get_current_screen();
    if ( isset( $screen->base ) && in_array( $screen->base, array( 'term', 'edit-tags' ), true ) ) {
        $taxonomies = array(
            'property_category',
            'property_action_category',
            'property_city',
            'property_county_state',
            'property_area',
            'property_features',
            'property_status',
            'property_category_agent',
            'property_action_category_agent',
            'property_city_agent',
            'property_area_agent',
            'property_county_state_agent',
            'category_agency',
            'action_category_agency',
            'city_agency',
            'area_agency',
            'county_state_agency',
            'property_category_developer',
            'property_action_developer',
            'property_city_developer',
            'property_area_developer',
            'property_county_state_developer'

        );
        $current_tax = isset( $_GET['taxonomy'] ) ? sanitize_key( $_GET['taxonomy'] ) : '';
        if ( in_array( $current_tax, $taxonomies, true ) ) {
            wp_enqueue_script(
                'wpestate-term-custom-fields',
                WPESTATE_PLUGIN_DIR_URL . 'admin/js/term-custom-fields.js',
                array( 'jquery' ),
                '1.0',
                true
            );

            wp_enqueue_script(
                'wpestate-term-gallery',
                WPESTATE_PLUGIN_DIR_URL . 'admin/js/term-gallery.js',
                array( 'jquery' ),
                '1.0',
                true
            );
            wp_enqueue_script(
                'wpestate-term-documents',
                WPESTATE_PLUGIN_DIR_URL . 'admin/js/term-documents.js',
                array( 'jquery' ),
                '1.0',
                true
            );
            wp_enqueue_script(
                'wpestate-term-icon',
                WPESTATE_PLUGIN_DIR_URL . 'admin/js/term-icon.js',
                array( 'jquery' ),
                '1.0',
                true
            );
            $term_id = isset( $_GET['tag_ID'] ) ? intval( $_GET['tag_ID'] ) : 0;
            wp_localize_script(
                'wpestate-term-gallery',
                'wpresidence_admin_gallery',
                array(
                    'title'  => esc_html__( 'Add Images', 'wpresidence-core' ),
                    'button' => esc_html__( 'Use images', 'wpresidence-core' ),
                    'term_id' => $term_id,
                    'nonce'   => wp_create_nonce( 'wpestate_term_gallery' ),
                )
            );

            wp_localize_script(
                'wpestate-term-documents',
                'wpresidence_admin_documents',
                array(
                    'title'  => esc_html__( 'Add Documents', 'wpresidence-core' ),
                    'button' => esc_html__( 'Use files', 'wpresidence-core' ),
                    'term_id' => $term_id,
                    'nonce'   => wp_create_nonce( 'wpestate_term_documents' ),
                )
            );

            wp_localize_script(
                'wpestate-term-custom-fields',
                'wpresidenceGeoJSON',
                array(
                    'title'  => esc_html__( 'Choose GeoJSON File', 'wpresidence-core' ),
                    'button' => esc_html__( 'Use file', 'wpresidence-core' ),
                )
            );
        }
    }
}





require_once(WPESTATE_PLUGIN_PATH . 'api/main.php');
require_once(WPESTATE_PLUGIN_PATH . 'misc/metaboxes.php');
require_once(WPESTATE_PLUGIN_PATH . 'misc/plugin_help_functions.php');
require_once(WPESTATE_PLUGIN_PATH . 'misc/redux_help_functions.php');
require_once(WPESTATE_PLUGIN_PATH . 'misc/webhookfunctions.php');
require_once(WPESTATE_PLUGIN_PATH . 'misc/emailfunctions.php');
require_once(WPESTATE_PLUGIN_PATH . 'misc/3rd_party_code.php');
require_once(WPESTATE_PLUGIN_PATH . 'misc/agent_functions.php');
require_once(WPESTATE_PLUGIN_PATH . 'misc/user_functions.php');
require_once(WPESTATE_PLUGIN_PATH . 'misc/update_functions.php');

require_once(WPESTATE_PLUGIN_PATH . 'widgets.php');
require_once(WPESTATE_PLUGIN_PATH . 'shortcodes/shortcodes_install.php');
require_once(WPESTATE_PLUGIN_PATH . 'shortcodes/shortcodes.php');
require_once(WPESTATE_PLUGIN_PATH . 'shortcodes/property_page_shortcodes.php');
require_once(WPESTATE_PLUGIN_PATH . 'shortcodes/property_page_shortcodes_tabs_functions.php');
require_once(WPESTATE_PLUGIN_PATH . 'shortcodes/property_page_shortcodes_accordion_functions.php');
require_once(WPESTATE_PLUGIN_PATH . 'shortcodes/property_page_shortcodes_single_details_as_text.php');


require_once(WPESTATE_PLUGIN_PATH . 'post-types/post-type-loader.php');
require_once(WPESTATE_PLUGIN_PATH . 'admin/post-type-control.php');
require_once(WPESTATE_PLUGIN_PATH . 'admin/white-label.php');
require_once WPESTATE_PLUGIN_PATH . 'admin/import-locations.php';
require_once WPESTATE_PLUGIN_PATH . 'admin/real-estate-menu.php';







require_once WPESTATE_PLUGIN_PATH.'classes/wpestate_func.php';
 


add_action('init','wpresidence_init_redux',30);

function wpresidence_init_redux(){


    //require_once WPESTATE_PLUGIN_PATH . 'admin/admin-init.php';
    // Redux::init("wpresidence_admin");
    if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/redux-framework/redux-core/framework.php' ) ) {
        require_once( dirname( __FILE__ ) . '/redux-framework/redux-core/framework.php' );
    }
   
   
    if ( !isset( $redux_demo ) && file_exists( dirname( __FILE__ ) . '/redux-framework/sample/options-init.php' ) ) {
            //require_once( dirname( __FILE__ ) . '/redux-framework/sample/options-init.php' );
            require_once( dirname( __FILE__ ) . '/redux-framework/admin-config.php' );
            Redux::init("wpresidence_admin");
    }
   




    $walkscore_api= esc_html ( wpresidence_get_option('wp_estate_walkscore_api','') );
    if($walkscore_api!=''){
        require_once(WPESTATE_PLUGIN_PATH.'resources/WalkScore.php');
    }


    $facebook_status    =   esc_html( wpresidence_get_option('wp_estate_facebook_login','') );
    if($facebook_status=='yes'){
        require_once WPESTATE_PLUGIN_PATH.'resources/facebook_sdk5/Facebook/autoload.php';
    }

    $enable_stripe_status   =   esc_html ( wpresidence_get_option('wp_estate_enable_stripe','') );

    if($enable_stripe_status==='yes'){
        require_once(WPESTATE_PLUGIN_PATH.'resources/stripe-php-master/init.php');
    }

    $yelp_client_id             =   wpresidence_get_option('wp_estate_yelp_client_id','');
    $yelp_client_secret         =   wpresidence_get_option('wp_estate_yelp_client_secret','');
    $yelp_client_api_key_2018   =   wpresidence_get_option('wp_estate_yelp_client_api_key_2018','');

    if($yelp_client_api_key_2018!=='' && $yelp_client_id!==''  ){
        require_once(WPESTATE_PLUGIN_PATH.'resources/yelp_fusion.php');
    }

    $yahoo_status       =   esc_html( wpresidence_get_option('wp_estate_yahoo_login','') );
    if($yahoo_status=='yes'){
        require_once(WPESTATE_PLUGIN_PATH.'resources/openid.php');
    }
    $google_status              = esc_html( wpresidence_get_option('wp_estate_google_login','') );

    $twiter_status       =   esc_html( wpresidence_get_option('wp_estate_twiter_login','') );
    if($twiter_status=='yes'){
        require_once WPESTATE_PLUGIN_PATH.'resources/twitteroauth/autoload.php';
    }

    if($facebook_status=='yes' ||$twiter_status=='yes' ||  $google_status =='yes'){
        require_once WPESTATE_PLUGIN_PATH.'classes/wpestate_social_login.php';
        global $wpestate_social_login;
        $wpestate_social_login =new Wpestate_Social_Login();

    }

    if( !class_exists('Google_Client') && wpresidence_get_option('wp_estate_google_login','')=='yes'  ){
      //  require_once WPESTATE_PLUGIN_PATH.'resources/src/Google_Client.php';
       // require_once WPESTATE_PLUGIN_PATH.'resources/src/contrib/Google_Oauth2Service.php';
    }



    require_once WPESTATE_PLUGIN_PATH.'classes/wpestate_global_payments.php';


    global $wpestate_global_payments;
    $wpestate_global_payments =new Wpestate_Global_Payments();

}



add_action('init', 'residence_redux_setup');
function residence_redux_setup() {
   


    if(class_exists('ReduxFramework')){
        remove_action( 'admin_notices', array( get_redux_instance('theme_options'), '_admin_notices' ), 99);
    }

    if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
        remove_filter( 'plugin_row_meta', array(
            ReduxFrameworkPlugin::instance(),
            'plugin_metalinks'
        ), null, 2 );

        // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
        remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );
    }
}
/*
*
* 
* 
*/
if ( ! function_exists( 'wpestate_return_search_parameters' ) ):
function wpestate_return_search_parameters($wpresidence_admin,$theme_option,$custom_advanced_search){


    if($custom_advanced_search=='yes'){
        //if custom search fields options are enabled
        if (isset($wpresidence_admin[$theme_option]) && $wpresidence_admin[$theme_option] != '') {
                $return = $wpresidence_admin[$theme_option];    
        }else{
            $return = array();
        }
  
    }else{
            $combined_search_array = [
                'wp_estate_adv_search_what' => [
                    0 => 'types',
                    1 => 'categories',
                    2 => 'county / state',
                    3 => 'cities',
                    4 => 'areas',
                    5 => 'beds-baths',
                    6 => 'property status',
                    7 => 'property-price-v2',
                    8 => 'types',
                    9 => 'categories',
                    10 => 'county / state',
                    11 => 'cities',
                    12 => 'areas',
                    13 => 'beds-baths',
                    14 => 'property status',
                    15 => 'property-price-v2',
                    16 => 'types',
                    17 => 'categories',
                    18 => 'county / state',
                    19 => 'cities',
                    20 => 'areas',
                    21 => 'beds-baths',
                    22 => 'property status',
                    23 => 'property-price-v2',
                ],
                'wp_estate_adv_search_label' => [
                    0 => esc_html__('Types', 'wpresidence-core'),
                    1 => esc_html__('Categories', 'wpresidence-core'),
                    2 => esc_html__('County', 'wpresidence-core'),
                    3 => esc_html__('City', 'wpresidence-core'),
                    4 => esc_html__('Area', 'wpresidence-core'),
                    5 => esc_html__('Beds&Baths', 'wpresidence-core'),
                    6 => esc_html__('Status', 'wpresidence-core'),
                    7 => esc_html__('Price', 'wpresidence-core'),
                    8 => esc_html__('Types', 'wpresidence-core'),
                    9 => esc_html__('Categories', 'wpresidence-core'),
                    10 => esc_html__('County', 'wpresidence-core'),
                    11 => esc_html__('City', 'wpresidence-core'),
                    12 => esc_html__('Area', 'wpresidence-core'),
                    13 => esc_html__('Beds&Baths', 'wpresidence-core'),
                    14 => esc_html__('Status', 'wpresidence-core'),
                    15 => esc_html__('Price', 'wpresidence-core'),
                    16 => esc_html__('Types', 'wpresidence-core'),
                    17 => esc_html__('Categories', 'wpresidence-core'),
                    18 => esc_html__('County', 'wpresidence-core'),
                    19 => esc_html__('City', 'wpresidence-core'),
                    20 => esc_html__('Area', 'wpresidence-core'),
                    21 => esc_html__('Beds&Baths', 'wpresidence-core'),
                    22 => esc_html__('Status', 'wpresidence-core'),
                    23 => esc_html__('Price', 'wpresidence-core'),
                ],          
                'wp_estate_adv_search_how' => [
                    0 => 'like',
                    1 => 'like',
                    2 => 'like',
                    3 => 'like',
                    4 => 'like',
                    5 => 'equal',
                    6 => 'like',
                    7 => 'equal',
                    8 => 'like',
                    9 => 'like',
                    10 => 'like',
                    11 => 'like',
                    12 => 'greater',
                    13 => 'like',
                    14 => 'equal',
                    15 => 'equal',
                    16 => 'like',
                    17 => 'like',
                    18 => 'like',
                    19 => 'like',
                    20 => 'greater',
                    21 => 'like',
                    22 => 'equal',
                    23 => 'equal',
                ]
            ];
            
            if ($wpresidence_admin['wp_estate_adv_search_type']==6){
                $return =  $combined_search_array[$theme_option];
            }else{
                $return = array_slice($combined_search_array[$theme_option], 0, 8, true);
            }


          
    }
    return $return ;
}
endif;



/*
*
* 
* 
*/

function wpestate_return_imported_data(){
    return  @unserialize(base64_decode( trim($_POST['import_theme_options']) ) );
}

/*
*
* 
* 
*/

function wpestate_return_imported_data_encoded($return_exported_data){
    return base64_encode( serialize( $return_exported_data) );
}

/*
*
* 
* 
*/

add_action( 'plugins_loaded', 'wpestate_check_current_user' );
function wpestate_check_current_user() {
    $current_user = wp_get_current_user();
    if (!current_user_can('manage_options') ) {
        show_admin_bar(false);
    }
}
/*
*
* 
* 
*/

if ( ! function_exists( 'wpestate_reverse_convert_redux_wp_estate_multi_curr' ) ):
function wpestate_reverse_convert_redux_wp_estate_multi_curr(){
    global $wpresidence_admin;
    $final_array = array();
    if(isset($wpresidence_admin['wpestate_currency']['add_curr_name'])){
        foreach ( $wpresidence_admin['wpestate_currency']['add_curr_name'] as $key=>$value ){
            $temp_array=array();
            $temp_array[0]= $wpresidence_admin['wpestate_currency']['add_curr_name'][$key];
            $temp_array[1]= $wpresidence_admin['wpestate_currency']['add_curr_label'][$key];
            $temp_array[2]= $wpresidence_admin['wpestate_currency']['add_curr_value'][$key];
            $temp_array[3]= $wpresidence_admin['wpestate_currency']['add_curr_order'][$key];

            $final_array[]=$temp_array;
        }
    }
    return $final_array;


}
endif;




/*
*
* 
* 
*/
if ( ! function_exists( 'wpestate_sorting_function_plugin' ) ):
function wpestate_sorting_function_plugin($a, $b) {
    return intval($a[3]) - intval($b[3]);
};
endif;


/**
 * Return all available property fields for the submission form
 * 
 * Optimized function that uses cached taxonomy terms to improve performance
 * 
 * @param int $is_mandatory Whether to return mandatory fields only
 * @return array Array of field slugs and labels
 */
if(!function_exists('wpestate_return_all_fields')):
    function wpestate_return_all_fields($is_mandatory=0){
        $submission_page_fields = get_option('wp_estate_submission_page_fields', '');
    
        $all_submission_fields = $all_mandatory_fields = array(
            'wpestate_description'          =>  esc_html__('Description','wpresidence-core'),
            'property_price'                =>  esc_html__('Property Price','wpresidence-core'),
            'property_year_tax'             =>  esc_html__('Yearly Tax Rate','wpresidence-core'),
            'property_hoa'                  =>  esc_html__('Homeowners Association Fee(monthly)','wpresidence-core'),
            'property_year_tax'             =>  esc_html__('Yearly Tax Rate','wpresidence-core'),
            'property_hoa'                  =>  esc_html__('Homeowners Association Fee','wpresidence-core'),
            'property_label'                =>  esc_html__('Property Price Label','wpresidence-core'),
            'property_label_before'         =>  esc_html__('Property Price Label Before','wpresidence-core'),        
            'property_second_price'         =>  esc_html__('Additional Price Info','wpresidence-core'),
            'property_second_price_label'   =>  esc_html__('After Label for Additional Price info','wpresidence-core'),
            'property_label_before_second_price' =>  esc_html__('Before Label for Additional Price Info','wpresidence-core'),       
            'prop_category'                 =>  esc_html__('Property Category Submit','wpresidence-core'),
            'prop_action_category'          =>  esc_html__('Property Action Category','wpresidence-core'),
            'attachid'                      =>  esc_html__('Property Media','wpresidence-core'),
            'property_address'              =>  esc_html__('Property Address','wpresidence-core'),
            'property_city'                 =>  esc_html__('Property City','wpresidence-core'),
            'property_area'                 =>  esc_html__('Property Area','wpresidence-core'),
            'property_zip'                  =>  esc_html__('Property Zip','wpresidence-core'),
            'property_county'               =>  esc_html__('Property County','wpresidence-core'),
            'property_country'              =>  esc_html__('Property Country','wpresidence-core'),
            'property_map'                  =>  esc_html__('Property Map','wpresidence-core'),
            'property_latitude'             =>  esc_html__('Property Latitude','wpresidence-core'),
            'property_longitude'            =>  esc_html__('Property Longitude','wpresidence-core'),
            'google_camera_angle'           =>  esc_html__('Google Camera Angle','wpresidence-core'),
            'property_google_view'          =>  esc_html__('Property Google View','wpresidence-core'),
            'property_hide_map_marker'      =>  esc_html__('Hide Map Marker','wpresidence-core'),
            'property_size'                 =>  esc_html__('property Size','wpresidence-core'),
            'property_lot_size'             =>  esc_html__('Property Lot Size','wpresidence-core'),
            'property_rooms'                =>  esc_html__('Property Rooms','wpresidence-core'),
            'property_bedrooms'             =>  esc_html__('Property Bedrooms','wpresidence-core'),
            'property_bathrooms'            =>  esc_html__('Property Bathrooms','wpresidence-core'),
            'owner_notes'                   =>  esc_html__('Owner Notes','wpresidence-core'),
            'property_status'               =>  esc_html__('property status','wpresidence-core'),
            'embed_video_id'                =>  esc_html__('Embed Video Id','wpresidence-core'),
            'embed_video_type'              =>  esc_html__('Embed Video Type','wpresidence-core'),
            'embed_virtual_tour'            =>  esc_html__('Embed Virtual Tour','wpresidence-core'),
            'property_subunits_list'        =>  esc_html__('Property Subunits','wpresidence-core'),
            'energy_class'                  =>  esc_html__('Energy Class','wpresidence-core'),
            'energy_index'                  =>  esc_html__('Energy Index','wpresidence-core'),
            'co2_class'                     =>  esc_html__('Greenhouse gas emissions Class', 'wpresidence-core'),
            'co2_index'                     =>  esc_html__('Greenhouse gas emissions Index', 'wpresidence-core'),
            'renew_energy_index'            =>  esc_html__('Renewable energy performance index', 'wpresidence-core'),
            'building_energy_index'         =>  esc_html__('Energy performance of the building', 'wpresidence-core'),
            'epc_current_rating'            =>  esc_html__('EPC current rating', 'wpresidence-core'),
            'epc_potential_rating'          =>  esc_html__('EPC Potential Rating', 'wpresidence-core'),
            'local_show_hide_price'         =>  esc_html__('Hide/Show Price', 'wpresidence-core'),
            'property_internal_id'          =>  esc_html__('Listing ID', 'wpresidence-core'),
        );
        
        if ($is_mandatory == 1) {
            unset($all_submission_fields['property_subunits_list']);
        }
        
        // Process custom fields
        $i = 0;
        $custom_fields = wpresidence_get_option('wp_estate_custom_fields', '');
        
        if(!empty($custom_fields)){
            while($i < count($custom_fields)){
                $name = stripslashes($custom_fields[$i][0]);
                $slug = str_replace(' ', '_', $name);
                
                if($is_mandatory == 1){
                    $slug = str_replace(' ', '-', $name);
                    unset($all_submission_fields['property_map']);
                }
                
                $label = stripslashes($custom_fields[$i][1]);
                $slug = htmlspecialchars($slug, ENT_QUOTES);
                
                $all_submission_fields[$slug] = $label;
                $i++;
            }
        }
    
        // Get property features from cache
        $terms = wpestate_get_cached_terms('property_features');
        
        if(is_array($terms) && !empty($terms)){
            foreach($terms as $term){
                $all_submission_fields[$term->slug] = $term->name;
            }
        }
        
        return $all_submission_fields;
    }
    endif;


/*
*
* 
* 
*/

function wpestate_show_license_form_plugin(){

    $theme_activated    =   get_option('is_theme_activated','');
    $ajax_nonce         =   wp_create_nonce( "my-check_ajax_license-string" );


    $return =1;


    if($theme_activated!='is_active'){

        $theme_active_time = get_option('activation_time','');
        if($theme_active_time==''){
            update_option('activation_time',time());
        }

        print '<div class="license_check_wrapper">';
            echo' <div class="activate_notice notice_here">'.esc_html__('Please activate the theme in the next 24h to validate the purchase and continue to have access to all theme options! See this ','wpresidence-core') .'<a href="http://help.wpresidence.net/article/how-to-get-your-buyer-license-code/" target="_blank">link</a> '.esc_html__('if you don\'t know how to get your license key. Thank you!','wpresidence-core').'</div>';
           print '<div class="license_form">
                <input type="text" id="wpestate_license_key" name="wpestate_license_key">
                <input type="submit" name="submit" id="check_ajax_license" class="new_admin_submit" value="Check License">
                <input type="hidden" id="license_ajax_nonce" name="license_ajax_nonce" value="'.$ajax_nonce.'">
            </div>';

            if( $theme_active_time +24*60*60 < time() ){
                print '<div class="activate_notice"> You cannot use the theme options until you activate the theme. </div>';
               // exit();
               $return=0;

            }
        print '</div>';

    }
    return $return;

}

/*
*
* 
* 
*/
function wpestate_check_license_plugin(){
    $theme_activated    =   get_option('is_theme_activated','');

    if($theme_activated!='is_active'){
        return false;
    }else{
        return true;
    }

}

/*
*
* 
* 
*/

function wpresidence_create_helper_content() {

     if ( get_option('wpresidence_theme_setup')!=='yes') {
        $page_creation=array(
                array(
                    'name'      =>'Advanced Search',
                    'template'  =>'page-templates/advanced_search_results.php',
                ),
                array(
                    'name'      =>'Compare Listings',
                    'template'  =>'page-templates/compare_listings.php',
                ),

                array(
                    'name'      =>'Dashboard - Property List',
                    'template'  =>'page-templates/user_dashboard.php',
                ),
                array(
                    'name'      =>'Dashboard - Add Property',
                    'template'  =>'page-templates/user_dashboard_add.php',
                ),
                array(
                    'name'      =>'Dashboard - Add Agent',
                    'template'  =>'page-templates/user_dashboard_add_agent.php',
                ),
                array(
                    'name'      =>'Dashboard - Agent List',
                    'template'  =>'page-templates/user_dashboard_agent_list.php',
                ),
                array(
                    'name'      =>'Dashboard - Favorite Properties',
                    'template'  =>'page-templates/user_dashboard_favorite.php',
                ),
                array(
                    'name'      =>'Dashboard - Inbox',
                    'template'  =>'page-templates/user_dashboard_inbox.php',
                ),
                array(
                    'name'      =>'Dashboard - Main',
                    'template'  =>'page-templates/user_dashboard_main.php',
                ),
                array(
                    'name'      =>'Dashboard - Invoices',
                    'template'  =>'page-templates/user_dashboard_invoices.php',
                ),
                array(
                    'name'      =>'Dashboard - Profile Page',
                    'template'  =>'page-templates/user_dashboard_profile.php',
                ),
                array(
                    'name'      =>'Dashboard - Search Results',
                    'template'  =>'page-templates/user_dashboard_search_result.php',
                ),
                array(
                    'name'      =>'Dashboard - Saved Searches',
                    'template'  =>'page-templates/user_dashboard_searches.php',
                ),
                array(
                    'name'      =>'Property Submit - Front',
                    'template'  =>'page-templates/front_property_submit.php',
                ),
                array(
                    'name'      =>'Dashboard -Analytics',
                    'template'  =>'page-templates/user_dashboard_analytics.php',
                ),


        );


        foreach($page_creation as $key=>$template){
            if ( function_exists('wpestate_get_template_link') && wpestate_get_template_link($template['template'],1 )==home_url('/') ){

                $my_post = array(
                    'post_title'    => $template['name'],
                    'post_type'     => 'page',
                    'post_status'   => 'publish',
                );
                $new_id = wp_insert_post($my_post);
                update_post_meta($new_id, '_wp_page_template', $template['template'] );
            }
        }



        ////////////////////  insert sales and rental categories
        $actions = array(   'Rentals',
                            'Sales'
                        );

        foreach ($actions as $key) {
            $my_cat = array(
                'description' => $key,
                'slug' => $key
            );

            if(!term_exists($key, 'property_action_category') ){
                wp_insert_term($key, 'property_action_category', $my_cat);
            }
        }

        ////////////////////  insert listings type categories
        $actions = array(   'Apartments',
                            'Houses',
                            'Land',
                            'Industrial',
                            'Offices',
                            'Retail',
                            'Condos',
                            'Duplexes',
                            'Villas'
                        );

        foreach ($actions as $key) {
            $my_cat = array(
                'description' => $key,
                'slug' => str_replace(' ', '-', $key)
            );

            if(!term_exists($key, 'property_category') ){
                wp_insert_term($key, 'property_category', $my_cat);
            }
        }

        $default_feature_list=array( 'attic', 'gas heat',' ocean view', 'wine cellar', 'basketball court', 'gym','pound', 'fireplace', 'lake view', 'pool',' back yard',
            'front yard', 'fenced yard', 'sprinklers',' washer and dryer', 'deck', 'balcony', 'laundry', 'concierge', 'doorman', 'private space', 'storage', 'recreation','roof deck');


        foreach ($default_feature_list as $key) {
            $my_cat = array(
                'description' => $key,
                'slug' =>sanitize_title($key)
            );

            if(!term_exists($key, 'property_features') ){
                wp_insert_term($key, 'property_features');
            }
        }

        add_option('wp_estate_cron_run', time());

        $default_status_list='open house, sold';
        add_option('wp_estate_status_list', $default_status_list);

        $all_rewrites=array('properties','listings','action','city','area','state','agents','agent_listings','agent-action','agent-city','agent-area','agent-state','agency-category','agency-action-category','agency-city','agency-area','agency-county','developer-category','developer-action-category', 'developer-city','developer-area','developer-county','agency','developer','features','status','review','review_category');
        add_option('wp_estate_url_rewrites',$all_rewrites);

        add_option('activation_time',time());
        update_option('wpresidence_theme_setup','yes');
    }
}

/*
*
* 
* 
*/
add_action('wp_head', 'wpestate_add_custom_meta_to_header');

function wpestate_add_custom_meta_to_header(){
    global $post;
    if( is_tax() ) {
        print '<meta name="description" content="'.strip_tags( term_description('', get_query_var( 'taxonomy' ) )).'" >';
    }

    if(is_singular('wpestate_invoice') || is_singular('wpestate_message')){
        print '<meta name="robots" content="noindex">';
    }


    if ( is_singular('estate_property') ){
        $image_id       =   get_post_thumbnail_id();
        $share_img      =   wp_get_attachment_image_src( $image_id, 'full');
        $the_post       =   get_post($post->ID); 
        
        $share_img_src='';
        if(isset($share_img[0])){
           $share_img_src= $share_img[0];
        }
        ?>

        <meta property="og:image" content="<?php print esc_url($share_img_src); ?>"/>
        <meta property="og:image:secure_url" content="<?php print esc_url($share_img_src); ?>" />
        <meta property="og:description"  content=" <?php print wp_strip_all_tags(do_shortcode( $the_post->post_content) );?>" />
    <?php }

    if(is_singular('wpestate_search') || is_singular('wpestate_invoice')){
        print '<meta name="robots" content="noindex">';
    }

}

/*
*
* 
* 
*/
/**
 * Allows posts to be searched by ID in the admin area.
 * 
 * @param WP_Query $query The WP_Query instance (passed by reference).
 */
add_action( 'pre_get_posts','wpestate_admin_search_include_ids' );

if (!function_exists('wpestate_admin_search_include_ids')):
function wpestate_admin_search_include_ids( $query ) {
    // Bail if we are not in the admin area
    if ( ! is_admin() ) {
        return;
    }

    // Bail if this is not the search query.
    if ( ! $query->is_main_query() && ! $query->is_search() ) {
        return;
    }   

    // Get the value that is being searched.
    $search_string = get_query_var( 's' );

    // Bail if the search string is not an integer.
    if ( ! filter_var( $search_string, FILTER_VALIDATE_INT ) ) {
        return;
    }

    // Set WP Query's p value to the searched post ID.
    $query->set( 'p', intval( $search_string ) );

    // Reset the search value to prevent standard search from being used.
    $query->set( 's', '' );
}
endif;



function wpml_compsupp6686_blacklisted_options($blacklisted_options) {
    $blacklisted_options[] = "wp_estate_submission_page_fields";
    return $blacklisted_options;
}

add_filter('wpml_st_blacklisted_options', 'wpml_compsupp6686_blacklisted_options', 999);



function wpestate_return_default_image_size(){
   $default_image_size = array(
    'user_picture_profile' => array(
        'name' => esc_html__('User profile picture', 'wpesidence-core'),
        'width' => 255,
        'height' => 143,
        'crop' => true,
    ),
    'agent_picture_thumb' => array(
        'name' => esc_html__('Agent picture thumb', 'wpesidence-core'),
        'width' => 120,
        'height' => 120,
        'crop' => true,
    ),
    'blog_thumb' => array(
        'name' => esc_html__('Blog thumb', 'wpesidence-core'),
        'width' => 272,
        'height' => 189,
        'crop' => true,
    ),
    'blog_unit' => array(
        'name' => esc_html__('Blog unit', 'wpesidence-core'),
        'width' => 1170,
        'height' => 405,
        'crop' => true,
    ),
    'slider_thumb' => array(
        'name' => esc_html__('Slider thumb', 'wpesidence-core'),
        'width' => 143,
        'height' => 83,
        'crop' => true,
    ),
    'property_featured_sidebar' => array(
        'name' => esc_html__('Property featured sidebar', 'wpesidence-core'),
        'width' => 768,
        'height' => 662,
        'crop' => true,
    ),
    'property_listings' => array(
        'name' => esc_html__('Property listings', 'wpesidence-core'),
        'width' => 525,
        'height' => 328,
        'crop' => true,
    ),
    'property_full' => array(
        'name' => esc_html__('Property full', 'wpesidence-core'),
        'width' => 980,
        'height' => 777,
        'crop' => true,
    ),
    'listing_full_slider' => array(
        'name' => esc_html__('Listing full slider', 'wpesidence-core'),
        'width' => 835,
        'height' => 467,
        'crop' => true,
    ),
    'listing_full_slider_1' => array(
        'name' => esc_html__('Listing full slider 1', 'wpesidence-core'),
        'width' => 1170,
        'height' => 660,
        'crop' => true,
    ),
    'property_featured' => array(
        'name' => esc_html__('Property featured', 'wpesidence-core'),
        'width' => 940,
        'height' => 390,
        'crop' => true,
    ),
    'property_full_map' => array(
        'name' => esc_html__('Property full map', 'wpesidence-core'),
        'width' => 1920,
        'height' => 790,
        'crop' => true,
    ),
    'widget_thumb' => array(
        'name' => esc_html__('Widget thumb', 'wpesidence-core'),
        'width' => 105,
        'height' => 70,
        'crop' => true,
    ),
    'user_thumb' => array(
        'name' => esc_html__('User thumb', 'wpesidence-core'),
        'width' => 45,
        'height' => 45,
        'crop' => true,
    ),
    'custom_slider_thumb' => array(
        'name' => esc_html__('Custom slider thumb', 'wpesidence-core'),
        'width' => 36,
        'height' => 36,
        'crop' => true,
    ),
    'post_thumbnail_size' => array(
        'name' => esc_html__('Post thumbnail size', 'wpesidence-core'),
        'width' => 250,
        'height' => 220,
        'crop' => true,
    ),
);

   return $default_image_size;
   
}

if (!function_exists('wpestate_get_template_name')):
 
    function wpestate_get_template_name($postID, $bypass = 0) {
      
          return basename(get_page_template($postID));


    }
endif;




add_filter('post_type_link', 'handle_agent_permalinks', 10, 2);
function handle_agent_permalinks($post_link, $post) {

    $rewrites_post_types=array(
        'estate_agent',
        'estate_agency',
        'estate_developer',
        'estate_property',
    );

    if ( in_array($post->post_type ,$rewrites_post_types) && is_admin()) {
        $rewrites = wpestate_safe_rewite();
        foreach ($rewrites as $value) {

            if($value!==''){
                if (strpos($post_link, $value) !== false) {
                    return str_replace($value, urlencode($value), $post_link);
                }
            }
        }
    }
    return $post_link;
}


// Role creation and deactivation
register_activation_hook(__FILE__, 'wpresidence_plugin_activated');

/**
 * Register deactivation hook
 */
register_deactivation_hook(__FILE__, function() {
    wpresidence_remove_user_role();
});



// do not delete - used to check versions for white label situations
function wpresidence_core_510(){

}



