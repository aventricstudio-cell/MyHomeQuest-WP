<?php
// register the custom post type
add_action( 'init', 'wpestate_crm_create_contact_type',20);

if( !function_exists('wpestate_crm_create_contact_type') ):
    function wpestate_crm_create_contact_type() {

        register_post_type( 'wpestate_crm_contact',
                array(
                        'labels' => array(
                                'name'          => esc_html__( 'Contacts','wpestate-crm'),
                                'singular_name' => esc_html__( 'Contact','wpestate-crm'),
                                'add_new'       => esc_html__('Add New Contact','wpestate-crm'),
                'add_new_item'          =>  esc_html__('Add Contact','wpestate-crm'),
                'edit'                  =>  esc_html__('Edit' ,'wpestate-crm'),
                'edit_item'             =>  esc_html__('Edit Contact','wpestate-crm'),
                'new_item'              =>  esc_html__('New Contact','wpestate-crm'),
                'view'                  =>  esc_html__('View','wpestate-crm'),
                'view_item'             =>  esc_html__('View Contact','wpestate-crm'),
                'search_items'          =>  esc_html__('Search Contact','wpestate-crm'),
                'not_found'             =>  esc_html__('No Contacts found','wpestate-crm'),
                'not_found_in_trash'    =>  esc_html__('No Contacts found','wpestate-crm'),
                'parent'                =>  esc_html__('Parent Contacts','wpestate-crm')
                        ),

                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                'show_in_menu'        => false,
                'show_in_admin_bar'   => false,
                'show_in_nav_menus'   => false,
                'can_export'          => false,
                'has_archive'         => false,
                'rewrite'             => false,
                'capability_type'     => 'post',
                'supports'            => array( 'title', 'thumbnail' ),

                'register_meta_box_cb' => 'wpestate_crm_contacts_metaboxes',
                 'menu_icon'=> WPESTATE_CRM_DIR_URL.'/img/agents.png'
                )
        );
        // add custom taxonomy

        register_taxonomy('wpestate-crm-contact-status', array('wpestate_crm_contact'), array(
            'labels' => array(
                'name'              => esc_html__('Contact Status','wpestate-crm'),
                'add_new_item'      => esc_html__('Add New Contact Status','wpestate-crm'),
                'new_item_name'     => esc_html__('New  Status','wpestate-crm')
            ),
            'hierarchical'  => true,
            'query_var'     => false,
            'publicly_queryable'  => false
            )
        );



    }
endif; // end   wpestate_create_agent_type


////////////////////////////////////////////////////////////////////////////////////////////////
// Add agent metaboxes
////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_crm_contacts_metaboxes') ):
function wpestate_crm_contacts_metaboxes() {
  add_meta_box(  'wpestate-crm-contacts-sectionid', esc_html__( 'Contacts Settings', 'wpestate-crm' ), 'wpestate_crm_contact_function', 'wpestate_crm_contact' ,'normal','default');
}
endif; // end   wpestate_add_agents_metaboxes



////////////////////////////////////////////////////////////////////////////////////////////////
// Agent details
////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('wpestate_crm_contact_function') ):
function wpestate_crm_contact_function( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'wpestate-crm_noncename' );
    global $post;
    $contact_post_array=wpestate_return_contact_post_array();

    print '<div class="crm_admin_wrapper">';
        print '<div class="crm_contacts_wrapper">';
            if(has_post_thumbnail($post->ID)){
                print get_the_post_thumbnail($post->ID,'agent_picture_thumb');
            }else{
                $post_thumbnail_url=wpestate_crm_get_user_picture($post->ID);
                if( $post_thumbnail_url==''){
                    $post_thumbnail_url    =   get_theme_file_uri('/img/default_user.png');
                }
                print'<img src="'.$post_thumbnail_url.'" alt="thumb">';
            }
            print wpestate_crm_return_tax($post->ID,'wpestate-crm-contact-status');
            print wpestate_crm_show_details($contact_post_array);
        print '</div>';


        print '<div class="crm_leads_wrapper">';
           print wpestate_show_lead_per_contact($post->ID);
           print wpestate_crm_display_add_note($post->ID);
           print wpestate_crm_show_notes($post->ID);
        print '</div>';
    print '</div>';




}
endif; // end   estate_agent




add_action('save_post', 'wpestate_crm_contact_save_function');

if( !function_exists('wpestate_crm_contact_save_function') ):
function wpestate_crm_contact_save_function($post_id){
    $contact_post_array=wpestate_return_contact_post_array();
    global $post;
    if(!is_object($post) || !isset($post->post_type)) {
        return;
    }

    if($post->post_type!='wpestate_crm_contact'){
       return;
    }

    foreach ($_POST as $key => $value) {

            if (isset( $contact_post_array[$key] ) ) {

                if( $contact_post_array[$key]['type'] != 'taxonomy' ){
                    $postmeta = sanitize_text_field( $value );
                    update_post_meta($post->ID, sanitize_key($key), $postmeta );
                }
            }

    }


}
endif;




function wpestate_crm_show_contact_details($lead_id,$is_front=''){
    $contact_id=  get_post_meta($lead_id,'lead_contact',true);
    $return='';
    print '<h2>'.esc_html__('Lead from Contact','wpestate-crm').'</h2>';
    $return .=  '<div class="contact_crm_wrapper">';
        if(has_post_thumbnail($contact_id)){
            print get_the_post_thumbnail($contact_id,'agent_picture_thumb');
        }else{
            $preview_img    =  wpestate_crm_get_user_picture($contact_id);
            if($preview_img==''){
                $preview_img    =   get_theme_file_uri('/img/default_user.png');
            }
            if($is_front==''){
              print '<img src="'.$preview_img.'" alt="agent">';
            }
        }
        if($is_front==''){
        print '<div class="contact_crm_detail"><label>'.esc_html__('Full Name','wpestate-crm').':</label> '.get_post_meta($contact_id,'crm_first_name',true).'</div>';
        }else{

          $edit_link       =   wpestate_get_template_link('wpestate-crm-dashboard_contacts.php');
          $edit_link       =   esc_url_raw(add_query_arg( 'contact_edit', $contact_id, $edit_link )) ;

          print '<div class="contact_crm_detail"><label>'.esc_html__('Full Name','wpestate-crm').':</label> <a href="'.esc_url($edit_link).'">'.get_post_meta($contact_id,'crm_first_name',true).'</a></div>';
        }
        print '<div class="contact_crm_detail"><label>'.esc_html__('Status','wpestate-crm').':</label>'.  strip_tags  ( get_the_term_list( $contact_id, 'wpestate-crm-lead-status', '', ', ', '') ).'</div>';
        print '<div class="contact_crm_detail"><label>'.esc_html__('Email','wpestate-crm').':</label> '.get_post_meta($contact_id,'crm_email',true).'</div>';
        print '<div class="contact_crm_detail"><label>'.esc_html__('Phone','wpestate-crm').':</label>'.get_post_meta($contact_id,'crm_mobile',true).'</div>';
        print '<div class="contact_crm_detail"><label>'.esc_html__('Address','wpestate-crm').':</label>'.get_post_meta($contact_id,'crm_address',true).'</div>';
        print '<div class="contact_crm_detail"><label>'.esc_html__('City','wpestate-crm').':</label>'.get_post_meta($contact_id,'crm_city',true).'</div>';
        print '<div class="contact_crm_detail"><label>'.esc_html__('County','wpestate-crm').':</label>'.get_post_meta($contact_id,'crm_county',true).'</div>';
        print '<div class="contact_crm_detail"><label>'.esc_html__('State','wpestate-crm').':</label>'.get_post_meta($contact_id,'crm_state',true).'</div>';

        if($is_front==''){
          print '<div class="contact_crm_detail"><a class="contact_full_details" href="'.get_edit_post_link($contact_id).'">'.esc_html__('full details','wpestate-crm').'</a></div>';
        }
    $return.= '</div>';

}







add_filter( 'manage_edit-wpestate_crm_contact_columns', 'wpestate_crm_contact_columns' );

if( !function_exists('wpestate_crm_contact_columns') ):
function wpestate_crm_contact_columns( $columns ) {
    $slice=array_slice($columns,2,2);
    unset( $columns['comments'] );
    unset( $slice['comments'] );
    $splice=array_splice($columns, 2);

//    $columns['contact_name']        = esc_html__('Name','wpestate-crm');
    $columns['contact_thumb']       = esc_html__('Photo','wpestate-crm');
    $columns['contact_email']       = esc_html__('Email','wpestate-crm');
    $columns['contact_phone']       = esc_html__( 'Phone','wpestate-crm');
    $columns['cotact_status']       = esc_html__('Status','wpestate-crm');


    return  array_merge($columns,array_reverse($slice));
}
endif; // end   wpestate_my_columns



?>
