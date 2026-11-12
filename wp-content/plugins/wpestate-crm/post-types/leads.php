<?php
// register the custom post type
add_action( 'init', 'wpestate_crm_create_lead_type');

if( !function_exists('wpestate_crm_create_lead_type') ):
    function wpestate_crm_create_lead_type() {

        register_post_type( 'wpestate_crm_lead',
                array(
                        'labels' => array(
                                'name'          => esc_html__( 'Leads','wpestate-crm'),
                                'singular_name' => esc_html__( 'Lead','wpestate-crm'),
                                'add_new'       => esc_html__('Add New Lead','wpestate-crm'),
                'add_new_item'          =>  esc_html__('Add Lead','wpestate-crm'),
                'edit'                  =>  esc_html__('Edit' ,'wpestate-crm'),
                'edit_item'             =>  esc_html__('Edit Lead','wpestate-crm'),
                'new_item'              =>  esc_html__('New Lead','wpestate-crm'),
                'view'                  =>  esc_html__('View','wpestate-crm'),
                'view_item'             =>  esc_html__('View Lead','wpestate-crm'),
                'search_items'          =>  esc_html__('Search Lead','wpestate-crm'),
                'not_found'             =>  esc_html__('No Leads found','wpestate-crm'),
                'not_found_in_trash'    =>  esc_html__('No Leads found','wpestate-crm'),
                'parent'                =>  esc_html__('Parent Leads','wpestate-crm')
                        ),

                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                'rewrite'             => false,
                'show_in_menu'        => false,
                'show_in_admin_bar'   => false,
                'show_in_nav_menus'   => false,
                'can_export'          => false,
                'has_archive'         => false,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'supports'            => array( 'title','thumbnail' ),
                'capability_type'     => 'post',
                'map_meta_cap'        => true,

                'register_meta_box_cb' => 'wpestate_crm_lead_metaboxes',
                 'menu_icon'=> WPESTATE_CRM_DIR_URL.'/img/agents.png'
                )
        );

        register_taxonomy('wpestate-crm-lead-status', array('wpestate_crm_lead'), array(
            'labels' => array(
                'name'              => esc_html__('Lead Status','wpestate-crm'),
                'add_new_item'      => esc_html__('Add New Lead Status','wpestate-crm'),
                'new_item_name'     => esc_html__('New Status','wpestate-crm')
            ),
            'hierarchical'  => true,
            'query_var'     => false,
            'publicly_queryable'  => false
            )
        );


    }
endif; // end   wpestate_create_agent_type



/*
 * Add agent metaboxes
 *
 *
 *
 *
 */

if( !function_exists('wpestate_crm_lead_metaboxes') ):
function wpestate_crm_lead_metaboxes() {
  add_meta_box(  'wpestate-crm-leads-sectionid', esc_html__( 'Lead Settings', 'wpestate-crm' ), 'wpestate_crm_lead_function', 'wpestate_crm_lead' ,'normal','default');
}
endif; // end   wpestate_add_agents_metaboxes




/*
 * wpestate_crm_lead_function
 *
 *
 *
 *
 */


if( !function_exists('wpestate_crm_lead_function') ):
function wpestate_crm_lead_function( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'estate_agent_noncename' );
    global $post;
    $leads_post_array=wpestate_leads_post_array();
    print '<div class="crm_admin_wrapper">';
        print '<div class="crm_contacts_wrapper lead_section">';
            print wpestate_crm_return_tax($post->ID,'wpestate-crm-lead-status');
            print wpestate_crm_show_contact_details($post->ID);
            print wpestate_crm_show_details($leads_post_array);
        print '</div>';

        print '<div class="crm_leads_wrapper">';
           print '<div class="crm_lead_content">';
           print '<div class="crm_lead_date">'.esc_html__('Received on','westate-crm').' '.$post->post_date.'</div>';
           print $post->post_content.'</div>';
           print wpestate_crm_display_add_note($post->ID);
           print wpestate_crm_show_notes($post->ID);
        print '</div>';
    print '</div>';


}
endif; // end   estate_agent

/*
 *
 *
 *
 *
 *
 */


add_action('save_post', 'wpestate_crm_lead_save_function');
if( !function_exists('wpestate_crm_lead_save_function') ):
function wpestate_crm_lead_save_function($post_id){
    $leads_post_array=wpestate_leads_post_array();
    global $post;

    if(!is_object($post) || !isset($post->post_type)) {
        return;
    }

    if($post->post_type!='wpestate_crm_lead'){
       return;
    }

     foreach ($_POST as $key => $value) {

            if (isset( $leads_post_array[$key] ) ) {

                if( $leads_post_array[$key]['type'] != 'taxonomy' ){
                    $postmeta = sanitize_text_field( $value );
                    update_post_meta($post->ID, sanitize_key($key), $postmeta );
                }
            }

    }
}
endif;

$def_arguments=array(
   'source'            =>'',
    'page_source'       =>'',
    'title'             =>'',
    'details'       =>'',
    'contact_name'  =>'',
    'contact_email' =>'',
    'contact_mobile'=>'',

);



/*
 * add lead - from contact forms on agent,agency, property, contact page
 *
 *
 *
 *
 */

function wpestate_crm_add_lead($arguments){

    $lead_author_id = wpestate_crm_detect_lead_owner($arguments);

    $post = array(
                'post_content'	=> $arguments['details'],
                'post_status'	  => 'publish',
                'post_type'     => 'wpestate_crm_lead' ,
                'post_author'   =>  $lead_author_id
            );

    $post_id =  wp_insert_post($post );

    if ( ! is_wp_error( $post_id ) ) {
            $post   =  array(
                            'ID'           => $post_id,
                            'post_title'   => 'Lead message no: ' . $post_id,
                        );
            wp_update_post($post);
    }
    update_post_meta($post_id,'crm_lead_permalink',$arguments['page_source']);

    if( isset($arguments['agent_id']) && intval( $arguments['agent_id']) !=0 ){
        update_post_meta($post_id,'crm_handler', intval( $arguments['agent_id']) );
    }

    update_post_meta($post_id,'lead_from_email', sanitize_text_field( $arguments['contact_email'])  );
    wpestate_create_crm_contact($arguments,$post_id,$lead_author_id);
}


/*
 * add contact - triggered from add lead function
 *
 *
 *
 */


function   wpestate_create_crm_contact($arguments,$lead_id,$lead_author_id){

    // check if contact inside db- via email

    $post = array(
            'post_title'	=> sanitize_text_field($arguments['contact_name']),
            'post_status'	=> 'publish',
            'post_type'         => 'wpestate_crm_contact',
            'post_author'       =>  $lead_author_id

    );

    $post_id =  wp_insert_post($post );
    update_post_meta($post_id,'crm_email',      sanitize_text_field( $arguments['contact_email']) );
    update_post_meta($post_id,'crm_first_name', sanitize_text_field( $arguments['contact_name'] ) );
    update_post_meta($post_id,'crm_email',      sanitize_text_field( $arguments['contact_email'] ) );
    update_post_meta($post_id,'crm_mobile',     sanitize_text_field( $arguments['contact_mobile']));
    update_post_meta($post_id,'lead_contact',   intval($lead_id));
    update_post_meta($lead_id,'lead_contact',   intval($post_id));

    update_post_meta($post_id,'lead_contact_to_id',   intval($lead_id));

    //$lead_author_id
    wpestate_create_contact_hubspot($arguments, $lead_author_id,'');

}


/*
 * detect lead and contact author id
 *
 *
 *
 *
 */

function wpestate_crm_detect_lead_owner($arguments){

  $agent_id = $arguments['agent_id'];
    if( intval( $arguments['propid'] ) !=0 ){

        // on property page

        if($agent_id!=0){
          // on property with agent assigned
          $lead_author_id    =   intval(get_post_meta($agent_id,'user_meda_id',true));
          if(  $lead_author_id == 0 ){
            $lead_author_id     = wpestate_crm_return_admin_id();
          }
        }else{
          // on property - but we have user and not agent or agenyc
          $the_post           =   get_post(  $arguments['propid'] );
          $lead_author_id      =   $the_post->post_author;
        }

    }else if( $arguments['agent_id']!=0 ){
        //on agent/agency/developer page
        $lead_author_id    =   intval(get_post_meta($agent_id,'user_meda_id',true));
        if(  $lead_author_id == 0 ){
          $lead_author_id     = wpestate_crm_return_admin_id();
        }
    }else{
        // contact page
        $lead_author_id     = wpestate_crm_return_admin_id();
    }




    return $lead_author_id;
}

/*
 * get user id of $administrator_email
 *
 *
 *
 *
 */

function wpestate_crm_return_admin_id(){
  $administrator_email= get_bloginfo('admin_email');
  $admin_user         = get_user_by('email',$administrator_email);
  $lead_author_id     = $admin_user->ID;
  return $lead_author_id;
}

/*
 * get user is from all admins
 *
 *
 *
 *
 */


function wpestate_crm_return_all_admin_ids(){
  $blogusers  = get_users( array( 'role__in' => array( 'administrator' ) ) );
  $temp_array = array();
  foreach ( $blogusers as $user ) {
     $temp_array[]= $user->ID;
  }
  return $temp_array;
}




/*
 *
 *
 *
 *
 *
 */





/*
 * add list columns on wp admin
 *
 *
 *
 *
 */

add_filter( 'manage_edit-wpestate_crm_lead_columns', 'wpestate_crm_lead_columns' );

if( !function_exists('wpestate_crm_lead_columns') ):
function wpestate_crm_lead_columns( $columns ) {
    $slice=array_slice($columns,2,2);
    unset( $columns['comments'] );
    unset( $slice['comments'] );
    $splice=array_splice($columns, 2);

    $columns['lead_contact_name']       = esc_html__('From Name','wpestate-crm');
    $columns['lead_contact_thumb']      = esc_html__('Image','wpestate-crm');
    $columns['lead_contact_status']     = esc_html__('Status','wpestate-crm');
    $columns['lead_contact_agent']      = esc_html__('Agent in Charge','wpestate-crm');
    $columns['lead_excerpt']            = esc_html__('Details','wpestate-crm');

    return  array_merge($columns,array_reverse($slice));
}
endif; // end   wpestate_my_columns






/*
 * populate crm leads and contact on admin
 *
 *
 *
 *
 */

add_action( 'manage_posts_custom_column', 'wpestate_crm_populate_columns' );
if( !function_exists('wpestate_crm_populate_columns') ):
function wpestate_crm_populate_columns( $column ) {

    global $post;
    $lead_id= $post->ID;
    $contact_id = get_post_meta($lead_id,'lead_contact',true);

    if ( 'lead_contact_name' == $column ) {

        echo get_post_meta($contact_id , 'crm_first_name', true);

    }else if ( 'lead_contact_thumb' == $column ) {
        if(has_post_thumbnail($contact_id)){
            $thumb_id           =   get_post_thumbnail_id( $contact_id);
            $post_thumbnail_url =    wp_get_attachment_image_src($thumb_id, 'slider_thumb');
        }else{
            $post_thumbnail_url[0]   =  wpestate_crm_get_user_picture($contact_id);
            if( $post_thumbnail_url[0]==''){
                $post_thumbnail_url[0]    =  get_theme_file_uri('/img/default_user.png');
            }
        }
        echo '<img src="'.$post_thumbnail_url[0].'" style="width:40%;height:auto;">';


    } else if ( 'lead_contact_status' == $column ) {
      //
        //wpestate-crm-contact-status
        echo strip_tags  ( get_the_term_list( $lead_id, 'wpestate-crm-lead-status', '', ', ', ''));

    }else if ( 'lead_contact_agent' == $column ) {

        $agent_id=intval( get_post_meta($lead_id , 'crm_handler', true) );
        if($agent_id!=0){
            echo get_the_title($agent_id);
        }
    }else if('lead_excerpt'==$column){
        echo get_the_excerpt($lead_id);

    } else if ( 'contact_name' == $column ) {

        echo get_post_meta($post->ID , 'crm_first_name', true);


    } else if ( 'contact_thumb' == $column ) {
        if(has_post_thumbnail($post->ID)){
            $thumb_id           =   get_post_thumbnail_id( $post->ID);
            $post_thumbnail_url =    wp_get_attachment_image_src($thumb_id, 'slider_thumb');
        }else{
            $post_thumbnail_url[0]   =  wpestate_crm_get_user_picture($post->ID);
            if( $post_thumbnail_url[0]==''){
                $post_thumbnail_url[0]    =  get_theme_file_uri('/img/default_user.png');
            }
        }
        echo '<img src="'.$post_thumbnail_url[0].'" style="width:40%;height:auto;">';

    } else if ( 'contact_email' == $column ) {

        echo get_post_meta($post->ID , 'crm_email', true);

    } else if ( 'contact_phone' == $column ) {

        echo get_post_meta($post->ID , 'crm_mobile', true);

    }else if ( 'cotact_status' == $column ) {

        echo strip_tags( get_the_term_list( $post->ID , 'wpestate-crm-contact-status', '', ', ', '') );

    }
}
endif; // end   wpestate_populate_columns


?>
