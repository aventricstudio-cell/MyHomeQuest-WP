<?php
/*
 *  Plugin Name: WPEstate CRM
 *  Plugin URI: https://wpestate.org
 *  Description: WPEstate Customer Relationship Management PLugin for WPResidence Theme
 *  Version:     5.2.1
 *  Author:      wpestate
 *  Author URI:  https://wpestate.org
 *  License:     GPL2
 *  Text Domain: wpestate-crm
 *  Domain Path: /languages
 *
*/


define('WPESTATE_CRM_URL',  plugins_url() );
define('WPESTATE_CRM_DIR_URL',  plugin_dir_url(__FILE__) );
define('WPESTATE_CRM_PATH',  plugin_dir_path(__FILE__) );
define('WPESTATE_CRM_BASE',  plugin_basename(__FILE__) );


add_action( 'wp_enqueue_scripts',       'wpestate_crm_enqueue_styles' );
add_action( 'admin_enqueue_scripts',    'wpestate_crm_enqueue_styles_admin');
add_action( 'plugins_loaded',           'wpestate_crm_check_plugin_functionality_loaded' );
add_action( 'wp_loaded',           'wpresidence_create_crm_helper_content' );
register_activation_hook( __FILE__, 'wpestate_crm_functionality' );
register_deactivation_hook( __FILE__, 'wpestate_crm_deactivate' );

function wpestate_crm_enqueue_styles(){

}

function wpestate_crn_plugin_init() {
    load_plugin_textdomain( 'wpestate-crm', false, dirname( WPESTATE_CRM_BASE ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'wpestate_crn_plugin_init' );


function wpestate_crm_enqueue_styles_admin(){
    wp_enqueue_script('wpestate_crm_script',WPESTATE_CRM_DIR_URL.'js/crm_script.js', array('jquery'), '1.0', true);
    wp_localize_script('wpestate_crm_script', 'wpestate_crm_script_vars',
        array( 'ajaxurl'            => esc_url(admin_url('admin-ajax.php')),
                'admin_url'         =>  get_admin_url(),

        )
    );


    wp_enqueue_style('wpestate_crm_style',  WPESTATE_CRM_DIR_URL.'css/crm_style.css', array(), '1.0', 'all');
}

function wpestate_crm_check_plugin_functionality_loaded(){

}

function wpestate_crm_deactivate(){

}


function wpestate_crm_functionality(){

}

require_once(WPESTATE_CRM_PATH . 'post-types/leads.php');
require_once(WPESTATE_CRM_PATH . 'post-types/contacts.php');
require_once(WPESTATE_CRM_PATH . 'libs/metaboxes.php');
require_once(WPESTATE_CRM_PATH . 'libs/hubspot/hubspot.php');



add_action( 'admin_menu', 'wpestate_crm_top_level_menu',99 );
function wpestate_crm_top_level_menu(){
        global $submenu;
          
        // Check if the branding function exists, otherwise use default
        $branding = function_exists('wpresidence_theme_branding') ? wpresidence_theme_branding() : 'WpEstate';
         $icon =   WPESTATE_CRM_URL . '/wpestate-crm/images/crm.png';
        if (function_exists('wpresidence_get_theme_branding_logo_url')) {
            $branding_logo = wpresidence_get_theme_branding_logo_url();
            if (!empty($branding_logo)) {
                $icon = $branding_logo;
            }
        }


        add_menu_page(
            $branding . ' CRM',  // Page title
            $branding . ' CRM',  // Menu title  
            'edit_posts', 
            'wpestate-crm',
            '',   
            $icon,
            4.5
        ); 
        add_submenu_page('wpestate-crm', esc_html__('Leads','wpestate-crm'),   esc_html__('Leads','wpestate-crm'),'edit_posts','edit.php?post_type=wpestate_crm_lead','');
        add_submenu_page('wpestate-crm', esc_html__('Contacts','wpestate-crm'), esc_html__('Contacts','wpestate-crm'), 'edit_posts', 'edit.php?post_type=wpestate_crm_contact','');
        add_submenu_page('wpestate-crm', esc_html__('Add New Contact','wpestate-crm'), esc_html__('Add New Contact','wpestate-crm'), 'edit_posts', 'post-new.php?post_type=wpestate_crm_contact','');
        add_submenu_page('wpestate-crm', esc_html__('Contact Status','wpestate-crm'), esc_html__('Edit Contact Statuses','wpestate-crm'), 'edit_posts', 'edit-tags.php?taxonomy=wpestate-crm-contact-status&post_type=wpestate_crm_contact','');

        add_submenu_page('wpestate-crm', esc_html__('Add New Lead','wpestate-crm'), esc_html__('Add New Lead','wpestate-crm'), 'edit_posts', 'post-new.php?post_type=wpestate_crm_lead','');
        add_submenu_page('wpestate-crm', esc_html__('Lead Status','wpestate-crm'), esc_html__('Edit Lead Statuses','wpestate-crm'), 'edit_posts', 'edit-tags.php?taxonomy=wpestate-crm-lead-status&post_type=wpestate_crm_lead','');

        unset( $submenu['wpestate-crm'][0]);

}



function wpestate_return_contact_post_array(){
    $contact_post_array=array(


        'crm_first_name'  => array(
                                'type'      => 'input',
                                'label'     => esc_html__('Full Name','wpestate-crm'),
                                'defaults'  =>  '',
                            ) ,
        'status'  => array(
                                  'type'      => 'taxonomy',
                                  'source'    =>  'wpestate-crm-contact-status',
                                  'label'     => esc_html__('Status','wpestate-crm'),
                                  'defaults'  =>  '',
                              ) ,


        'crm_email'  => array(
                                'type'      => 'input',
                                'label'     => esc_html__('Email','wpestate-crm'),
                                'defaults'  =>  '',
                            ),
        'crm_address'  => array(
                                'type'      => 'textarea',
                                'label'     => esc_html__('Address','wpestate-crm'),
                                'defaults'  =>  '',
                                'length'    =>  'full'
                            ),
        'crm_city'  => array(
                                'type'      => 'input',
                                'label'     => esc_html__('City','wpestate-crm'),
                                'defaults'  =>  '',
                            ),
        'crm_county'  => array(
                                'type'      => 'input',
                                'label'     => esc_html__('County','wpestate-crm'),
                                'defaults'  =>  '',
                            ),
        'crm_state'  => array(
                                'type'      => 'input',
                                'label'     => esc_html__('State','wpestate-crm'),
                                'defaults'  =>  '',
                            ),
        'crm_mobile'  => array(
                                'type'      => 'input',
                                'label'     => esc_html__('Mobile','wpestate-crm'),
                                'defaults'  =>  '',
                            ),
        'crm_phone'  => array(
                                'type'      => 'input',
                                'label'     => esc_html__('Phone','wpestate-crm'),
                                'defaults'  =>  '',
                            ),

        'crm_private'=> array(
                                'type'      => 'textarea',
                                'label'     => esc_html__('Notes','wpestate-crm'),
                                'defaults'  =>  '',
                                'length'    =>  'full'
                            ),
        );
    
    return $contact_post_array;
}


function wpestate_leads_post_array(){
    $leads_post_array=array(
        'crm_handler'  => array(
                                'type'      => 'post_type',
                                'source'    =>  array('estate_agent','estate_agency','estate_developer'),
                                'label'     => esc_html__('Agent in Charge','wpestate-crm'),
                                'defaults'  =>  '',
                            ),

          'status'  => array(
                  'type'      => 'taxonomy',
                  'source'    =>  'wpestate-crm-lead-status',
                  'label'     => esc_html__('Status','wpestate-crm'),
                  'defaults'  =>  '',
              ) ,

        'crm_private'=> array(
                'type'      => 'textarea',
                'label'     => esc_html__('Notes','wpestate-crm'),
                'defaults'  =>  '',
                'length'    =>  'full'
            ),

        'crm_lead_permalink'=> array(
                'type'      => 'input',
                'label'     => esc_html__('Sent From','wpestate-crm'),
                'defaults'  =>  '',
                'editable'  =>  'false',
                'length'    =>  'full'
            ),
            'crm_lead_content'=> array(
                    'type'      => 'content',
                    'label'     => esc_html__('Message','wpestate-crm'),
                    'defaults'  =>  '',
                    'editable'  =>  'false',
                    'length'    =>  'full'
                ),

);
    return $leads_post_array;

}

//crm_first_name

function wpestate_crm_return_tax($postID,$tax){
    $return='';
    $return.= '<div class="crm_contact_status">';

            $return.=  esc_html__('Status','wpestate-crm').': ';
            $terms  =   get_the_terms($postID,$tax);
            $status =   '';
            if(is_array($terms)){
                foreach($terms as $term){
                    $status.=$term->name.',';
                }
                $status= rtrim($status,',');
            }
            if($status==''){
                $status=' - ';
            }
            $return.= $status;
        $return.='</div>';
        return $return;
}

/*
 *
 *
 *Add "Custom" template to page attirbute template section.
*
*
*
*/

add_filter( 'theme_page_templates', 'wpestat_crm_add_template_to_select', 1, 4 );
function wpestat_crm_add_template_to_select( $post_templates, $wp_theme, $post, $post_type ) {

    // Add custom template named template-custom.php to select dropdown
    $post_templates['wpestate-crm-dashboard.php'] = __('Wpestate CRM List');
    $post_templates['wpestate-crm-dashboard_contacts.php'] = __('Wpestate CRM Contacts');
    $post_templates['wpestate-crm-dashboard_leads.php'] = __('Wpestate CRM Leads');
    return $post_templates;
}


/*
 *
 *
 *  Handle Slugs for new templates
 *
 *
 *
 */


add_filter( 'page_template', 'wpestate_crm_page_template_slugs' ,1);
function wpestate_crm_page_template_slugs( $page_template ){

    if ( get_page_template_slug() == 'wpestate-crm-dashboard.php' ) {
        $page_template = dirname( __FILE__ ) . '/wpestate-crm-dashboard.php';
    }else if ( get_page_template_slug() == 'wpestate-crm-dashboard_contacts.php' ) {
        $page_template = dirname( __FILE__ ) . '/wpestate-crm-dashboard_contacts.php';
    }else if ( get_page_template_slug() == 'wpestate-crm-dashboard_leads.php' ) {
        $page_template = dirname( __FILE__ ) . '/wpestate-crm-dashboard_leads.php';
    }
    return $page_template;
}

/*
 *
 *
 *  Handle Slugs for new templates
 *
 *
 *
 */
 function wpresidence_create_crm_helper_content() {

      if ( get_option('wpresidence_crm_setup')!=='yes') {
         $page_creation=array(
                 array(
                     'name'      =>'WpEstate CRM',
                     'template'  =>'wpestate-crm-dashboard.php',
                 ),
                 array(
                     'name'      =>'WpEstate CRM Contacts',
                     'template'  =>'wpestate-crm-dashboard_contacts.php',
                 ),

                 array(
                     'name'      =>'WpEstate CRM Leads / Inquires',
                     'template'  =>'wpestate-crm-dashboard_leads.php',
                 ),


         );


         foreach($page_creation as $key=>$template){
             if ( wpestate_get_template_link_crm($template['template'],1 ) == home_url('/') ){

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
         $lead_statuses = array(   'New',
                             'Open',
                             'In progress',
                              'Open deal',
                              'Unqualified',
                              'Attempted to contact',
                              'Connected',
                              'Bad timing',
                         );

         foreach ($lead_statuses as $key) :
             $my_cat = array(
                 'description' => $key,
                 'slug' => $key
             );

             if(!term_exists($key, 'wpestate-crm-lead-status') ){
                 wp_insert_term($key, 'wpestate-crm-lead-status', $my_cat);
             }
             if(!term_exists($key, 'wpestate-crm-contact-statusus') ){
                 wp_insert_term($key, 'wpestate-crm-contact-status', $my_cat);
             }
         endforeach;

          update_option('wpresidence_crm_setup','yes');
     }
 }

 /*
  *
  *
  *  get template link crm
  *
  *
  *
  */


 function wpestate_get_template_link_crm( $template_name  ,$bypass=0){
         $pages = get_pages(array(
             'meta_key'      => '_wp_page_template',
             'meta_value'    => $template_name
         ));

         if( $pages ){
             $template_link =  esc_url (  get_permalink( $pages[0]->ID ) );
         }else{
             $template_link=esc_url( home_url('/') );
         }
         return esc_url($template_link);
 }
