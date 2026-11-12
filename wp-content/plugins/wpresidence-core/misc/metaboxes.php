<?php
add_action('add_meta_boxes', 'estate_sidebar_meta');
add_action('save_post', 'estate_save_postdata', 99, 3);
add_action( 'edit_comment', 'extend_comment_edit_metafields' );

if( !function_exists('estate_sidebar_meta') ):

function estate_sidebar_meta() {
    global $post;
    if(isset($post->ID)){
        if( wpestate_get_template_name($post->ID)   == 'property_list.php' ||
            wpestate_get_template_name($post->ID)   == 'property_list_directory.php' ||
            wpestate_get_template_name($post->ID)   == 'property_list_half.php'){
            add_meta_box('wpestate-pro_list_adv',       esc_html__('Property List Advanced Options','wpresidence-core'), 'estate_prop_advanced_function', 'page', 'normal', 'low');
        }

        if( wpestate_get_template_name($post->ID)   == 'agents_list.php' ||
            wpestate_get_template_name($post->ID)   == 'agency_list.php' ||
            wpestate_get_template_name($post->ID)   == 'developers_list.php'){
            add_meta_box('wpestate-pro_realtor_order',       esc_html__('Realtor Options','wpresidence-core'), 'estate_realtor_order', 'page', 'normal', 'low');
        }

        if( wpestate_get_template_name($post->ID)== 'property_list_directory.php' ){
            add_meta_box('wpestate-pro_list_directory',       esc_html__('Property List Directory Filter Settings','wpresidence-core'), 'estate_prop_advanced_function_directory', 'page', 'normal', 'low');

        }
    }
    add_meta_box('wpestate-header',             esc_html__('Appearance Options','wpresidence-core'), 'estate_header_function', 'page', 'normal', 'low');
    add_meta_box('wpestate-header',             esc_html__('Appearance Options','wpresidence-core'), 'estate_header_function', 'post', 'normal', 'low');
    add_meta_box('wpestate-header',             esc_html__('Appearance Options','wpresidence-core'), 'estate_header_function', 'estate_agent', 'normal', 'low');
    add_meta_box('wpestate-header',             esc_html__('Appearance Options','wpresidence-core'), 'estate_header_function', 'estate_property', 'normal', 'low');


    add_meta_box('wpestate-header',             esc_html__('Appearance Options','wpresidence-core'), 'estate_header_function', 'estate_agency', 'normal', 'low');
    add_meta_box('wpestate-header',             esc_html__('Appearance Options','wpresidence-core'), 'estate_header_function', 'estate_developer', 'normal', 'low');
    add_meta_box('wpestate-header',             esc_html__('Title & Stars','wpresidence-core'), 'wpestate_comment_starts', 'comment', 'normal');
}
endif; // end   estate_sidebar_meta




///////////////////////////////////////////////////////////////////////////////////////////////////////////
/// Header Option
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('estate_header_function') ):
function estate_header_function(){
    global $post;

    $title_tab           = '';

    if ( 'post' === get_post_type( $post->ID ) ) {
        $option       = '';
        $title_values = array( 'yes', 'no' );
        $post_title   = get_post_meta( $post->ID, 'post_show_title', true );
        foreach ( $title_values as $value ) {
            $option .= '<option value="' . $value . '"';
            if ( $value == $post_title ) {
                $option .= ' selected="selected"';
            }
            $option .= '>' . $value . '</option>';
        }

        $title_tab .= '<div class="property_prop_half">'
                . '<label for="post_show_title">' . esc_html__( 'Show Title:', 'wpresidence-core' ) . ' </label><br />'
                . '<select id="post_show_title" name="post_show_title">'
                . $option
                . '</select><br />'
          . '</div>';
    } elseif ( 'page' === get_post_type( $post->ID ) ) {
        ob_start();
        estate_page_options_box( $post );
        $title_tab .= ob_get_clean();
    }
    if ( 'estate_property' === get_post_type( $post->ID ) ) {

    }







    $header_array   =   array(
                            'global',
                            'none',
                            'image',
                            'theme slider',
                            'revolution slider',
                            'maps',
                            'video header'

                            );

    $header_type    =   get_post_meta ( $post->ID, 'header_type', true);
    $header_select  =   '';
    $header_array['11']=  'virtual tour - only for properties';


    foreach($header_array as $key=>$value){
       $header_select.='<option value="'.$key.'" ';
       if($key==$header_type){
           $header_select.=' selected="selected" ';
       }
       $header_select.='>'.$value.'</option>';
    }
    ////////// search form
    $cache_array                        =   array('global','no','yes');
    $use_float_search_form_local_select =   '';
    $search_float_type                  =   get_post_meta ( $post->ID, 'use_float_search_form_local_set', true);
    foreach($cache_array as $key=>$value){
       $use_float_search_form_local_select.='<option value="'.$key.'" ';
       if($key==$search_float_type){
           $use_float_search_form_local_select.=' selected="selected" ';
       }
       $use_float_search_form_local_select.='>'.$value.'</option>';
    }


    ////////// end logo header
    $cache_array                =   array('global','no','yes');
    $header_transparent         =   get_post_meta ( $post->ID, 'header_transparent', true);
    $header_transparent_select  =   '';

    foreach($cache_array as $key=>$value){
       $header_transparent_select.='<option value="'.$value.'" ';
       if($value==$header_transparent){
           $header_transparent_select.=' selected="selected" ';
       }
       $header_transparent_select.='>'.$value.'</option>';
    }


    $topbar_transparent         =   get_post_meta ( $post->ID, 'topbar_transparent', true);
    $topbar_transparent_select  =   '';

    foreach($cache_array as $key=>$value){
       $topbar_transparent_select.='<option value="'.$value.'" ';
       if($value==$topbar_transparent){
           $topbar_transparent_select.=' selected="selected" ';
       }
       $topbar_transparent_select.='>'.$value.'</option>';
    }


    $topbar_border_transparent         =   get_post_meta ( $post->ID, 'topbar_border_transparent', true);
    $ttopbar_border_transparent_select  =   '';

    foreach($cache_array as $key=>$value){
       $ttopbar_border_transparent_select.='<option value="'.$value.'" ';
       if($value==$topbar_border_transparent){
           $ttopbar_border_transparent_select.=' selected="selected" ';
       }
       $ttopbar_border_transparent_select.='>'.$value.'</option>';
    }

    $header_tab  = '<div class="property_prop_half">
                <label for="header_transparent">' . esc_html__( 'Transparent Header', 'wpresidence-core' ) . '</label>
                <select name="header_transparent">' . $header_transparent_select . '</select>
            </div>
           <div class="property_prop_half">
                <label for="topbar_transparent">' . esc_html__( 'Transparent Top Bar', 'wpresidence-core' ) . '</label>
                <select name="topbar_transparent">' . $topbar_transparent_select . '</select>
            </div>
            <div class="property_prop_half">
                <label for="topbar_border_transparent">' . esc_html__( 'Borders for Transparent Top Bar ', 'wpresidence-core' ) . '</label>
                <select name="topbar_border_transparent">' . $ttopbar_border_transparent_select . '</select>
            </div>';

    $cache_array_rev             = array( 'global', 'yes', 'no' );
    $page_show_adv_search_select = wpestate_dropdowns_theme_admin_option_core( $post->ID, $cache_array_rev, 'page_show_adv_search' );
    $page_use_float_search_select = wpestate_dropdowns_theme_admin_option_core( $post->ID, $cache_array_rev, 'page_use_float_search' );

    $adv_tab  = '<div class="property_prop_half">';
    $adv_tab .= '<label for="page_show_adv_search">' . esc_html__( 'Show Advanced Search?', 'wpresidence-core' ) . '</label>';
    $adv_tab .= '<select id="page_show_adv_search" name="page_show_adv_search">' . $page_show_adv_search_select . '</select>';
    $adv_tab .= '</div>';
    $adv_tab .= '<div class="property_prop_half">';
    $adv_tab .= '<label for="page_use_float_search">' . esc_html__( 'Use Float Search Form?', 'wpresidence-core' ) . '</label>';
    $adv_tab .= '<select id="page_use_float_search" name="page_use_float_search">' . $page_use_float_search_select . '</select>';
    $adv_tab .= '</div>';
    $adv_tab .= '<div class="property_prop_half">';
    $adv_tab .= '<label for="page_wp_estate_float_form_top">' . esc_html__( 'Distance between search form and the top margin. Type for ex: 90% or 90px.', 'wpresidence-core' ) . '</label>';
    $adv_tab .= '<input type="text" id="page_wp_estate_float_form_top" name="page_wp_estate_float_form_top" value="' . get_post_meta( $post->ID, 'page_wp_estate_float_form_top', true ) . '">';
    $adv_tab .= '</div>';

    $header_media_tab  = '<h3 class="pblankh">' . esc_html__( 'Select The Hero Header Media Type', 'wpresidence-core' ) . '</h3>';
    $header_media_tab .= '<div class="property_prop_half"><select id="page_header_type" name="header_type" class="header_media">' . $header_select . '</select></div>';
    ob_start();
    estate_page_map_box( $post );
    estate_page_slider_box( $post );
    estate_page_video_box( $post );
    $header_media_tab .= ob_get_clean();

    ob_start();
    estate_sidebar_box( $post );
    $sidebar_tab = ob_get_clean();

    $post_settings_tab = '';
    if ( 'post' === get_post_type( $post->ID ) ) {

        $option         = '';
        $group_pictures = get_post_meta( $post->ID, 'group_pictures', true );
        foreach ( $title_values as $value ) {
            $option .= '<option value="' . $value . '"';
            if ( $value == $group_pictures ) {
                $option .= ' selected="selected"';
            }
            $option .= '>' . $value . '</option>';
        }
        $post_settings_tab .= '<div class="property_prop_half">'
            . '<label for="group_pictures">' . esc_html__( 'Display image slider? (*only for blog posts)', 'wpresidence-core' ) . ' </label>'
            . '<select id="group_pictures" name="group_pictures">'
            . $option
            . '</select>'
            . '</div><div class="property_prop_half" style="min-height:65px;"></div>';

        $post_settings_tab .= '<div class="property_prop_half">'
            . '<label for="embed_video_id">' . esc_html__( 'Use this video Embed Video id in slider:', 'wpresidence-core' ) . '</label>'
            . '<input type="text" id="embed_video_id" name="embed_video_id" value="' . esc_html( get_post_meta( $post->ID, 'embed_video_id', true ) ) . '">'
            . '</div>';

        $option_video  = '';
        $video_values  = array( 'vimeo', 'youtube', 'tiktok' );
        $video_type    = get_post_meta( $post->ID, 'embed_video_type', true );
        foreach ( $video_values as $value ) {
            $option_video .= '<option value="' . $value . '"';
            if ( $value == $video_type ) {
                $option_video .= ' selected="selected"';
            }
            $option_video .= '>' . $value . '</option>';
        }
        $post_settings_tab .= '<div class="property_prop_half">'
            . '<label for="embed_video_type">' . esc_html__( 'Video from', 'wpresidence-core' ) . '</label>'
            . '<select id="embed_video_type" name="embed_video_type">'
            . $option_video
            . '</select>'
            . '</div>';

        $slider_images_tab = '';
        ob_start();

        $already_in = '';
        print '<div class="property_uploaded_thumb_wrapepr" id="property_uploaded_thumb_wrapepr">';
        $ajax_nonce = wp_create_nonce("wpestate_attach_delete");
        print'<input type="hidden" id="wpestate_attach_delete" value="' . esc_html($ajax_nonce) . '" />    ';

        $arguments = array(
            'numberposts' => -1,
            'post_type' => 'attachment',
            'post_parent' => $post->ID,
            'post_status' => null, 
            'orderby' => 'menu_order',
            'post_mime_type' => 'image',
            'order' => 'ASC',
            'fields' => 'ids'
        );

        $post_attachments_new = get_posts($arguments);


        foreach ($post_attachments_new as $attachment_id) {
            $attachment = get_post($attachment_id);
    
            if ($attachment && ($attachment->post_mime_type == 'image/jpeg' ||
                    $attachment->post_mime_type == 'application/pdf' ||
                    $attachment->post_mime_type == 'image/webp' ||
                    $attachment->post_mime_type == 'image/png')) {
    
                print '<div class="uploaded_thumb" data-imageid="' . $attachment_id . '">';
    
                if ($attachment->post_mime_type == 'application/pdf') {
                    print ' <img src="' . get_theme_file_uri('/img/pdf.png') . '" alt="' . esc_html__('user document', 'wpresidence-core') . '" />';
                } else {
                    $preview = wp_get_attachment_image_src($attachment_id, 'thumbnail');
                    print '<img src="' . $preview[0] . '" alt="slider" />';
                }
    
                $already_in .= $attachment_id . ',';
                print '<a target="_blank" href="' . esc_url(admin_url()) . 'post.php?post=' . $attachment_id . '&action=edit" class="attach_edit"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                <span class="attach_delete"><i class="far fa-trash-alt" aria-hidden="true"></i></span>';
    
                print '</div>';
            }
        }


        print '<input type="hidden" id="image_to_attach" name="image_to_attach" value="' . $already_in . '"/>';

        print '</div>';

        print '<button class="upload_button button" id="button_new_image" data-postid="' . $post->ID . '">' . esc_html__('Upload new image(s)', 'wpresidence-core') . '</button>';
        $slider_images_tab .= ob_get_clean();
    }

    $show_title_tab = in_array( get_post_type( $post->ID ), array( 'post', 'page' ), true );

    $tabs = array();

    if ( $show_title_tab ) {
        $tabs['header_title'] = array(
            'label'   => esc_html__( 'Title Section', 'wpresidence-core' ),
            'content' => $title_tab,
        );
    }

    if ( 'post' === get_post_type( $post->ID ) ) {
        $tabs['header_post'] = array(
            'label'   => esc_html__( 'Post Settings', 'wpresidence-core' ),
            'content' => $post_settings_tab,
        );
        $tabs['media_post'] = array(
            'label'   => esc_html__( 'Image Slider', 'wpresidence-core' ),
            'content' => $slider_images_tab,
        );
    }


    $tabs['header_transparent'] = array(
        'label'   => esc_html__( 'Header', 'wpresidence-core' ),
        'content' => $header_tab,
    );

    $tabs['header_search'] = array(
        'label'   => esc_html__( 'Advanced Search', 'wpresidence-core' ),
        'content' => $adv_tab,
    );

    $tabs['header_sidebar'] = array(
        'label'   => esc_html__( 'Sidebar Settings', 'wpresidence-core' ),
        'content' => $sidebar_tab,
    );

    $tabs['header_media_type'] = array(
        'label'   => esc_html__( 'Header Media Type', 'wpresidence-core' ),
        'content' => $header_media_tab,
    );

    // 'Content Options' tab is now part of the Property Details metabox.

    $available_tabs = array_keys( $tabs );
    $active_tab    = $available_tabs[0];
    if ( isset( $_GET['header_tab'] ) && in_array( $_GET['header_tab'], $available_tabs, true ) ) {
        $active_tab = sanitize_key( $_GET['header_tab'] );
    }

    echo '<div class="header_options_wrapper meta-options">';
    echo '<div class="header_options_wrapper_list">';

    foreach ( $tabs as $tab_id => $tab_data ) {
        $active = ( $active_tab === $tab_id ) ? ' active_tab' : '';
        echo '<div class="header_tab_item' . $active . '" data-content="' . esc_attr( $tab_id ) . '">' . $tab_data['label'] . '</div>';
    }

    echo '</div><div class="header_options_content_wrapper">';

    foreach ( $tabs as $tab_id => $tab_data ) {
        $active = ( $active_tab === $tab_id ) ? ' active_tab' : '';
        echo '<div class="header_tab_item_content' . $active . '" id="' . esc_attr( $tab_id ) . '">' . $tab_data['content'] . '</div>';
    }

    echo '</div></div>';
    }

endif;



if(!function_exists('estate_prpg_design_option')):
    function estate_prpg_design_option(){
        global $post;
        if(   'estate_property' == get_post_type() ){
          

            $sidebar_agent_option='';
            $sidebar_option_values=array('global','no','yes');
            $sidebar_agent_option_value=    get_post_meta($post->ID, 'sidebar_agent_option', true);


            foreach ($sidebar_option_values as $key=>$value) {
                $sidebar_agent_option.='<option value="' . $value . '"';
                if ($value == $sidebar_agent_option_value) {
                    $sidebar_agent_option.=' selected="selected"';
                }
                $sidebar_agent_option.='>' . $value . '</option>';
            }

            // slider type
            $slider_type                    =   array('global','classic','vertical','horizontal','full width header','gallery','multi image slider','header masonry gallery');
            $slider_type = array(
                'global' => 'global',
                'classic' => 'classic',
                'vertical' => 'vertical',
                'horizontal' => 'horizontal',
                'full width header' => 'slider v4',
                'gallery' => 'masonry gallery v1',
                'multi image slider' => 'multi image slider',
                'header masonry gallery' => 'masonry gallery v2'
            );
            $local_pgpr_slider_type_symbol='';
            $local_pgpr_slider_type_status=  get_post_meta($post->ID, 'local_pgpr_slider_type', true);

            foreach($slider_type as $key => $value){
                $local_pgpr_slider_type_symbol.='<option value="'.$key.'"';
                if ($local_pgpr_slider_type_status==$key){
                    $local_pgpr_slider_type_symbol.=' selected="selected" ';
                }
                $local_pgpr_slider_type_symbol.='>'.$value.'</option>';
            }

            //  content
            $content_type                       =   array('global','accordion','tabs');
            $local_pgpr_content_type_symbol     =   '';
            $local_pgpr_content_type_status     =  get_post_meta($post->ID, 'local_pgpr_content_type', true);

            foreach($content_type as $value){
                $local_pgpr_content_type_symbol.='<option value="'.$value.'"';
                if ($local_pgpr_content_type_status==$value){
                    $local_pgpr_content_type_symbol.=' selected="selected" ';
                }
                $local_pgpr_content_type_symbol.='>'.$value.'</option>';
            }

            print '<div class="property_prop_half">'.esc_html__('Show Agent on Sidebar? ','wpresidence-core').' </label><br />
                <select id="sidebar_agent_option" name="sidebar_agent_option">
                ' . $sidebar_agent_option . '
                </select>
            </div>

            <div class="property_prop_half">
                <label for="local_pgpr_slider_type">'.esc_html__('Property Slider Type? ','wpresidence-core').' </label><br />
                <select id="local_pgpr_slider_type" name="local_pgpr_slider_type"  >
                    '.$local_pgpr_slider_type_symbol.'
                </select>
            </div>


            <div class="property_prop_half"> 
                <label for="local_pgpr_content_type">'.esc_html__('Show Property Content as ','wpresidence-core').' </label><br />
                <select id="local_pgpr_content_type" name="local_pgpr_content_type" >
                    '.$local_pgpr_content_type_symbol.'
                </select>
            </div>';
            
            }
    }
endif;






///////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Property Listing advanced options
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('estate_prop_advanced_function_directory') ):
function estate_prop_advanced_function_directory(){
    global $post;

    if( wpestate_get_template_name($post->ID)!= 'property_list_directory.php' ){
        esc_html_e('Only for "Properties List Directory" page template ! ','wpresidence-core');
        return;
    }

    print ' <table>
    <tr>
        <td width="33%" valign="top" align="left">
            <p class="meta-options">
                <label   for="dir_min_size">'.esc_html__("Min Size Value","wpresidence-core").'</label> </br>
                <input type="text" name="dir_min_size" id="dir_min_size" value="'.get_post_meta($post->ID,'dir_min_size',true).'">
            </p>
        </td>

        <td width="33%" valign="top" align="left">
            <p class="meta-options">
                <label   for="dir_max_size">'.esc_html__("Max Size Value","wpresidence-core").'</label> </br>
                <input type="text" name="dir_max_size" id="dir_max_size" value="'.get_post_meta($post->ID,'dir_max_size',true).'">
            </p>
        </td>


    </tr>
     <tr>
        <td width="33%" valign="top" align="left">
            <p class="meta-options">
                <label   for="dir_min_lot_size">'.esc_html__("Min Lot Size Value","wpresidence-core").'</label> </br>
                <input type="text" name="dir_min_lot_size" id="dir_min_lot_size" value="'.get_post_meta($post->ID,'dir_min_lot_size',true).'">
            </p>
        </td>

        <td width="33%" valign="top" align="left">
            <p class="meta-options">
                <label   for="dir_max_lot_size">'.esc_html__("Max Lot Size Value","wpresidence-core").'</label> </br>
                <input type="text" name="dir_max_lot_size" id="dir_max_lot_size" value="'.get_post_meta($post->ID,'dir_max_lot_size',true).'">
            </p>
        </td>


    </tr>

    <tr>
        <td width="33%" valign="top" align="left">
            <p class="meta-options">
                <label   for="dir_rooms_min">'.esc_html__("Min Rooms Value","wpresidence-core").'</label> </br>
                <input type="text" name="dir_rooms_min" id="dir_rooms_min" value="'.get_post_meta($post->ID,'dir_rooms_min',true).'">
            </p>
        </td>

        <td width="33%" valign="top" align="left">
            <p class="meta-options">
                <label   for="dir_rooms_max">'.esc_html__("Max Rooms Value","wpresidence-core").'</label> </br>
                <input type="text" name="dir_rooms_max" id="dir_rooms_max" value="'.get_post_meta($post->ID,'dir_rooms_max',true).'">
            </p>
        </td>
    </tr>

    <tr>
        <td width="33%" valign="top" align="left">
            <p class="meta-options">
                <label   for="dir_bedrooms_min">'.esc_html__("Min Bedrooms Value","wpresidence-core").'</label> </br>
                <input type="text" name="dir_bedrooms_min" id="dir_bedrooms_min" value="'.get_post_meta($post->ID,'dir_bedrooms_min',true).'">
            </p>
        </td>

        <td width="33%" valign="top" align="left">
            <p class="meta-options">
                <label   for="dir_bedrooms_max">'.esc_html__("Max Bedrooms Value","wpresidence-core").'</label> </br>
                <input type="text" name="dir_bedrooms_max" id="dir_bedrooms_max" value="'.get_post_meta($post->ID,'dir_bedrooms_max',true).'">
            </p>
        </td>
    </tr>


    <tr>
        <td width="33%" valign="top" align="left">
            <p class="meta-options">
                <label   for="dir_bathrooms_min">'.esc_html__("Min Bathrooms Value","wpresidence-core").'</label> </br>
                <input type="text" name="dir_bathrooms_min" id="dir_bathrooms_min" value="'.get_post_meta($post->ID,'dir_bathrooms_min',true).'">
            </p>
        </td>

        <td width="33%" valign="top" align="left">
            <p class="meta-options">
                <label   for="dir_bathrooms_max">'.esc_html__("Max Bathrooms Value","wpresidence-core").'</label> </br>
                <input type="text" name="dir_bathrooms_max" id="dir_bedrooms_max" value="'.get_post_meta($post->ID,'dir_bathrooms_max',true).'">
            </p>
        </td>
    </tr>



    </table>';


}
endif;










if( !function_exists('estate_prop_advanced_function') ):
function estate_prop_advanced_function(){
    global $post;

    if( wpestate_get_template_name($post->ID)!= 'property_list.php' && 
        wpestate_get_template_name($post->ID)!= 'property_list_directory.php' && 
        wpestate_get_template_name($post->ID)!= 'property_list_half.php' ){
        esc_html_e('Only for "Properties List" page template ! ','wpresidence-core');
        return;
    }

    $args = array(
        'hide_empty'    => false
    );

    $actions_select     =   '';
    $categ_select       =   '';
    $taxonomy           =   'property_action_category';
    $tax_terms          =   wpestate_get_cached_terms($taxonomy,$args);

    $current_adv_filter_search_action = get_post_meta ( $post->ID, 'adv_filter_search_action', true);
    if($current_adv_filter_search_action==''){
        $current_adv_filter_search_action=array();
    }


    $all_selected='';
    if (empty($current_adv_filter_search_action) || (isset($current_adv_filter_search_action[0]) && ($current_adv_filter_search_action[0] == 'all' || $current_adv_filter_search_action[0] === ''))) {
      $all_selected=' selected="selected" ';
    }

    $actions_select.='<option value="" '.$all_selected.'>'.esc_html__('all','wpresidence-core').'</option>';
    if( !empty( $tax_terms ) ){
        foreach ($tax_terms as $tax_term) {
            $actions_select.='<option value="'.$tax_term->name.'" ';
            if( in_array  ( $tax_term->name,$current_adv_filter_search_action) ){
              $actions_select.=' selected="selected" ';
            }
            $actions_select.=' />'.$tax_term->name.'</option>';
        }
    }



    //////////////////////////////////////////////////////////////////////////////////////////
    $taxonomy           =   'property_category';
    $tax_terms          =   wpestate_get_cached_terms($taxonomy,$args);

    $current_adv_filter_search_category = get_post_meta ( $post->ID, 'adv_filter_search_category', true);
    if($current_adv_filter_search_category==''){
        $current_adv_filter_search_category=array();
    }

    $all_selected='';
    if (empty($current_adv_filter_search_category) || (isset($current_adv_filter_search_category[0]) && ($current_adv_filter_search_category[0] == 'all' || $current_adv_filter_search_category[0] === ''))) {
      $all_selected=' selected="selected" ';
    }

    $categ_select.='<option value="" '.$all_selected.'>'.esc_html__('all','wpresidence-core').'</option>';
    if( !empty( $tax_terms ) ){
        foreach ($tax_terms as $tax_term) {
            $categ_select.='<option value="'.$tax_term->name.'" ';
            if( in_array  ( $tax_term->name, $current_adv_filter_search_category) ){
              $categ_select.=' selected="selected" ';
            }
            $categ_select.=' />'.$tax_term->name.'</option>';
        }
    }


 //////////////////////////////////////////////////////////////////////////////////////////

    $select_city='';
    $taxonomy = 'property_city';
    $tax_terms_city = wpestate_get_cached_terms($taxonomy,$args);
    $current_adv_filter_city = get_post_meta ( $post->ID, 'current_adv_filter_city', true);

    if($current_adv_filter_city==''){
        $current_adv_filter_city=array();
    }

    $all_selected='';
    if (empty($current_adv_filter_city) || (isset($current_adv_filter_city[0]) && ($current_adv_filter_city[0] == 'all' || $current_adv_filter_city[0] === ''))) {
      $all_selected=' selected="selected" ';
    }

    $select_city.='<option value="" '.$all_selected.' >'.esc_html__('all','wpresidence-core').'</option>';
    foreach ($tax_terms_city as $tax_term) {

        $select_city.= '<option value="' . $tax_term->name . '" ';
        if( in_array  ( $tax_term->name, $current_adv_filter_city) ){
              $select_city.=' selected="selected" ';
        }
        $select_city.= '>' . $tax_term->name . '</option>';
    }


 //////////////////////////////////////////////////////////////////////////////////////////

    $select_area='';
    $taxonomy = 'property_area';
    $tax_terms_area = wpestate_get_cached_terms($taxonomy,$args);
    $current_adv_filter_area = get_post_meta ( $post->ID, 'current_adv_filter_area', true);
    if($current_adv_filter_area==''){
        $current_adv_filter_area=array();
    }

    $all_selected='';
    if (empty($current_adv_filter_area) || (isset($current_adv_filter_area[0]) && ($current_adv_filter_area[0] == 'all' || $current_adv_filter_area[0] === ''))) {
      $all_selected=' selected="selected" ';
    }

    $select_area.='<option value="" '.$all_selected.'>'.esc_html__('all','wpresidence-core').'</option>';
    foreach ($tax_terms_area as $tax_term) {
        $term_meta=  get_option( "taxonomy_$tax_term->term_id");
        $select_area.= '<option value="' . $tax_term->name . '" ';
        if( in_array  ( $tax_term->name, $current_adv_filter_area) ){
              $select_area.=' selected="selected" ';
        }
        $select_area.= '>' . $tax_term->name . '</option>';
    }

//////////////////////////////////


 //////////////////////////////////////////////////////////////////////////////////////////

    $select_county='';
    $taxonomy = 'property_county_state';
    $tax_terms_county = wpestate_get_cached_terms($taxonomy,$args);
    $current_adv_filter_county = get_post_meta ( $post->ID, 'current_adv_filter_county', true);
    if($current_adv_filter_county==''){
        $current_adv_filter_county=array();
    }

    $all_selected='';
    if (empty($current_adv_filter_county) || (isset($current_adv_filter_county[0]) && ($current_adv_filter_county[0] == 'all' || $current_adv_filter_county[0] === ''))) {
      $all_selected=' selected="selected" ';
    }

    $select_county.='<option value="" '.$all_selected.'>'.esc_html__('all','wpresidence-core').'</option>';
    foreach ($tax_terms_county as $tax_term) {
        $term_meta=  get_option( "taxonomy_$tax_term->term_id");
        $select_county.= '<option value="' . $tax_term->name . '" ';
        if( in_array  ( $tax_term->name, $current_adv_filter_county) ){
              $select_county.=' selected="selected" ';
        }
        $select_county.= '>' . $tax_term->name . '</option>';
    }

//////////////////////////////////



    $show_filter_area_select='';
    $cache_array=array('yes','no');
    $show_filter_area  =   get_post_meta($post->ID, 'show_filter_area', true);

    foreach($cache_array as $value){
         $show_filter_area_select.='<option value="'.$value.'"';
         if ( $show_filter_area == $value ){
                 $show_filter_area_select.=' selected="selected" ';
         }
         $show_filter_area_select.='>'.$value.'</option>';
    }






    $show_featured_only_select='';
    $show_featured_only  =   get_post_meta($post->ID, 'show_featured_only', true);
    foreach($cache_array as $value){

         $show_featured_only_select.='<option value="'.$value.'" ';
         if ( $show_featured_only == $value ){
                 $show_featured_only_select.=' selected="selected" ';
         }
         $show_featured_only_select.='>'.$value.'</option>';
    }

    $listing_filter = get_post_meta($post->ID, 'listing_filter',true );

    $listing_filter_array=array(
                            "1"=>esc_html__('Price High to Low','wpresidence-core'),
                            "2"=>esc_html__('Price Low to High','wpresidence-core'),
                            "3"=>esc_html__('Newest first','wpresidence-core'),
                            "4"=>esc_html__('Oldest first','wpresidence-core'),
                            "11"=>esc_html__('Newest Edited','wpresidence-core'),
                            "12"=>esc_html__('Oldest Edited ','wpresidence-core'),
        
                            "5"=>esc_html__('Bedrooms High to Low','wpresidence-core'),
                            "6"=>esc_html__('Bedrooms Low to high','wpresidence-core'),
                            "7"=>esc_html__('Bathrooms High to Low','wpresidence-core'),
                            "8"=>esc_html__('Bathrooms Low to high','wpresidence-core'),
                            "0"=>esc_html__('Default','wpresidence-core')
                            );

    $property_list_second_content = get_post_meta($post->ID, 'property_list_second_content', true);

    $filter_tab = '';
    $filter_tab .= '<div class="property_prop_half">'
        .'<label for="property_action_category[]">Pick actions</label><br />'
        .'<select name="adv_filter_search_action[]" multiple="multiple" >'
        .$actions_select.'</select></div>';
    $filter_tab .= '<div class="property_prop_half">'
        .'<label for="adv_filter_search_category[]">Pick category</label><br />'
        .'<select name="adv_filter_search_category[]" multiple="multiple" >'
        .$categ_select.'</select></div>';
    $filter_tab .= '<div class="property_prop_half">'
        .'<label for="current_adv_filter_city[]">Pick City</label><br />'
        .'<select name="current_adv_filter_city[]" multiple="multiple" >'
        .$select_city.'</select></div>';
    $filter_tab .= '<div class="property_prop_half">'
        .'<label for="current_adv_filter_area[]">Pick Area</label><br />'
        .'<select name="current_adv_filter_area[]" multiple="multiple" >'
        .$select_area.'</select></div>';
    $filter_tab .= '<div class="property_prop_half">'
        .'<label for="current_adv_filter_county[]">Pick County/State</label><br />'
        .'<select name="current_adv_filter_county[]" multiple="multiple" >'
        .$select_county.'</select></div>';

    $order_tab = '<div class="property_prop_half">'
        .'<label for="listing_filter_div">Default sort ?</label><br />'
        .'<select id="listing_filter_div" name="listing_filter" >';
            foreach($listing_filter_array as $key=>$value){
                $order_tab.='<option value="'.$key.'" ';
                if($key==$listing_filter){
                    $order_tab.=' selected="selected" ';
                }
                $order_tab.='>'.$value.'</option>';
            }
    $order_tab .='</select></div>';
    $order_tab .= '<div class="property_prop_half">'
        .'<label for="show_featured_only">Show featured only </label><br />'
        .'<select id="show_featured_only" name="show_featured_only" >'
        .$show_featured_only_select.'</select></div>';
    $order_tab .= '<div class="property_prop_half">'
        .'<label for="show_filter_area">Show filter area</label><br />'
        .'<select id="show_filter_area" name="show_filter_area" style="width:250px;">'
        .$show_filter_area_select.'</select></div>';

    ob_start();
    echo '<label for="property_list_second_content">Content that comes after property list.If you want to use shortcodes just create your page in the original content area and them copy paste it from text mode.</label><br />';
    wp_editor(
        $property_list_second_content,
        'property_list_second_content',
        array(
            'textarea_rows' =>  6,
            'textarea_name' =>  'property_list_second_content',
            'wpautop'       =>  false,
            'media_buttons' =>  true,
            'tabindex'      =>  '',
            'editor_css'    =>  '',
            'editor_class'  => '',
            'teeny'         => false,
            'dfw'           => false,
            'tinymce'       => true,
            'quicktags'     => array('buttons'=>'strong,em,block,ins,ul,li,ol,close,more'),
        )
    );
    $content_editor = ob_get_clean();
    $content_tab = '<div class="property_prop_half prop_full">'.$content_editor.'</div>';

    $tabs = array(
        'pladv_filter' => array(
            'label'   => esc_html__('Filter Listings','wpresidence-core'),
            'content' => $filter_tab,
        ),
        'pladv_order' => array(
            'label'   => esc_html__('Order & Featured','wpresidence-core'),
            'content' => $order_tab,
        ),
        'pladv_content' => array(
            'label'   => esc_html__('Page Content','wpresidence-core'),
            'content' => $content_tab,
        ),
    );

    $available_tabs = array_keys($tabs);
    $active_tab = $available_tabs[0];
    if ( isset( $_GET['prolistadv_tab'] ) && in_array( $_GET['prolistadv_tab'], $available_tabs, true ) ) {
        $active_tab = sanitize_key( $_GET['prolistadv_tab'] );
    }

    echo '<div class="pladv_options_wrapper meta-options">';
    echo '<div class="pladv_options_wrapper_list">';
    foreach ( $tabs as $tab_id => $tab_data ) {
        $active = ( $active_tab === $tab_id ) ? ' active_tab' : '';
        echo '<div class="pladv_tab_item'.$active.'" data-content="'.$tab_id.'">'.$tab_data['label'].'</div>';
    }
    echo '</div><div class="pladv_options_content_wrapper">';
    foreach ( $tabs as $tab_id => $tab_data ) {
        $active = ( $active_tab === $tab_id ) ? ' active_tab' : '';
        echo '<div class="pladv_tab_item_content'.$active.'" id="'.$tab_id.'">'.$tab_data['content'].'</div>';
    }
    echo '</div></div>';
}

endif;
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///  Listing options
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('estate_listing_options') ):
  function estate_listing_options(){


    global $post;
    if ( 'property_list.php'            == wpestate_get_template_name($post->ID) || 
        'property_list_directory.php'   == wpestate_get_template_name($post->ID)  ){

        $listing_action  =   get_post_meta($post->ID, 'listing_action', true);
        $listing_categ   =   get_post_meta($post->ID, 'listing_categ', true);
        $listing_city    =   get_post_meta($post->ID, 'listing_city', true);
        $listing_area    =   get_post_meta($post->ID, 'listing_area', true);

        $args = array(
        'hide_empty'    => false
        );

        $taxonomy = 'property_action_category';
        $tax_terms = wpestate_get_cached_terms($taxonomy,$args);

        $taxonomy_categ = 'property_category';
        $tax_terms_categ = wpestate_get_cached_terms($taxonomy_categ,$args);

        $actions_select     =   '';
        $categ_select       =   '';


        ///////////////////////// actions
        if( !empty( $tax_terms ) ){
            foreach ($tax_terms as $tax_term) {
              $actions_select.='<option value="'.$tax_term->name.'" ';
              if ($tax_term->name == $listing_action ){
                   $actions_select.=' selected="selected" ';
              }
              $actions_select.=' >'.$tax_term->name.'</option>';
            }
        }


        /////////////////////////categ

        if( !empty( $tax_terms_categ ) ){
            foreach ($tax_terms_categ as $categ) {
              $categ_select.='<option value="'.$categ->name.'" ';
               if ($categ->name == $listing_categ ){
                   $categ_select.=' selected="selected" ';
              }
              $categ_select.='>'.$categ->name.'</option>';
            }
        }


        ///////////////////////// city
        $select_city='';
        $taxonomy = 'property_city';
        $tax_terms = wpestate_get_cached_terms($taxonomy,$args);
        foreach ($tax_terms as $tax_term) {
           $select_city.= '<option value="' . $tax_term->name . '" ';
           if ( $tax_term->name  == $listing_city ){
                   $select_city.=' selected="selected" ';
              }
           $select_city.='>' . $tax_term->name . '</option>';
        }

        if ($select_city==''){
              $select_city.= '<option value="">No Cities</option>';
        }



        /////////////////////////area
        $select_area='';
        $taxonomy = 'property_area';
        $tax_terms = wpestate_get_cached_terms($taxonomy,$args);

        foreach ($tax_terms as $tax_term) {
            $term_meta=  get_option( "taxonomy_$tax_term->term_id");
            $select_area.= '<option value="' . $tax_term->name . '" data-parentcity="' . $term_meta['cityparent'] . '" ';

             if ( $tax_term->name  == $listing_area ){
                   $select_area.=' selected="selected" ';
              }

            $select_area.= '>' . $tax_term->name . '</option>';

         }

        print '
        <p class="meta-options">
            <label for="listing_action">'.esc_html__('Action category','wpresidence-core').'</label><br />
            <select  name="listing_action" >
                <option value="all">'.esc_html__('All Listings','wpresidence-core').'</option>
                '.$actions_select.'
           </select>
        </p>


        <p class="meta-options">
        <label for="listing_categ">'.esc_html__('Pick Category','wpresidence-core').'</label><br />
            <select name="listing_categ"  >
                <option value="all">'.esc_html__('All Types','wpresidence-core').'</option>
                '. $categ_select.'
            </select>
        </p>

        <p class="meta-options">
            <label for="listing_city">'.esc_html__('Pick City','wpresidence-core').'</label><br />
            <select  name="listing_city"  >
                <option value="all">'.esc_html__('Cities','wpresidence-core').'</option>
                '. $select_city.'
             </select>
        </p>

        <p class="meta-options">
            <label for="listing_area">'.esc_html__('Pick Area','wpresidence-core').'</label><br />
            <select  name="listing_area">
                <option data-parentcity="*" value="all">'.esc_html__('Areas','wpresidence-core').'</option>
                '.$select_area.'
            </select>
        </p>
         ';


        }else{
            print esc_html_e('These Options are available for "Property list" page template only!','wpresidence-core');
        }

  }
endif; // end   estate_listing_options





////////////////////////////////////////////////////////////////////////////////////////////////
// Manage Revolution Slider
////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('estate_page_slider_box') ):
function estate_page_slider_box($post) {
    global $post;
    $rev_slider           = get_post_meta($post->ID, 'rev_slider', true);
    print '
    <div class="header_admin_options revolution_slider">
        <p class="meta-options pblank">
            <h3 class="pblankh">'.esc_html__('Options for Revolution Slider (if Hero Header Media Type "revolution slider" is Selected)','wpresidence-core').'</h3>
        </p>
        <div class="property_prop_half">
            <label for="page_custom_lat">'.esc_html__('Revolution Slider Name','wpresidence-core').'</label><br />
            <input type="text" id="rev_slider" name="rev_slider" size="40" value="'.$rev_slider.'">
        </div>
    </div>
    ';
}
endif; // end   estate_page_slider_box


if( !function_exists('estate_page_video_box') ):
function estate_page_video_box($post) {
    global $post;
    //page_custom_video


    $cache_array                        =   array('yes','no');
    $cache_array_reverse                =   array('no','yes');
    $cache_array_fix                    =   array('screen','auto');
    $page_custom_video                  =   get_post_meta($post->ID, 'page_custom_video', true);
    $page_custom_video_webbm            =   get_post_meta($post->ID, 'page_custom_video_webbm', true);
    $page_custom_video_ogv              =   get_post_meta($post->ID, 'page_custom_video_ogv', true);
    $page_custom_video_cover_image      =   get_post_meta($post->ID, 'page_custom_video_cover_image', true);
    $img_full_screen                    =   wpestate_dropdowns_theme_admin_option_core($post->ID,$cache_array_reverse,'page_header_video_full_screen');
    $page_header_title_over_video       =   stripslashes ( esc_html ( get_post_meta($post->ID, 'page_header_title_over_video', true) ) );
    $page_header_subtitle_over_video    =   stripslashes ( esc_html ( get_post_meta($post->ID, 'page_header_subtitle_over_video', true) ) );
    $page_header_video_height           =   esc_html ( get_post_meta($post->ID, 'page_header_video_height', true) );
    $page_header_overlay_color_video    =   esc_html ( get_post_meta($post->ID, 'page_header_overlay_color_video', true) );
    $page_header_overlay_val_video      =   esc_html ( get_post_meta($post->ID, 'page_header_overlay_val_video', true) );


    print '
    <div class="header_admin_options video_header">
        <p class="meta-options pblank">
            <h3 class="pblankh">'.esc_html__('Options for Video Header (if Hero Header Media Type "video" is Selected)','wpresidence-core').'</h3>
        </p>



        <div class="property_prop_half">
            <label for="page_custom_image">'.esc_html__('Video MP4 version','wpresidence-core').'</label><br />
            <input id="page_custom_video" type="text" size="36" name="page_custom_video" class="wpestate_landing_upload" value="'.$page_custom_video.'" />
            <input id="page_custom_video_button" type="button"   size="40" class="upload_button button media_upload_button" value="'.esc_html__('Upload Video','wpresidence-core').'" />
        </div>

        <div class="property_prop_half">
            <label for="page_custom_image">'.esc_html__('Video WEBM version','wpresidence-core').'</label><br />
            <input id="page_custom_video_webbm" type="text" size="36" name="page_custom_video_webbm"  class="wpestate_landing_upload" value="'.$page_custom_video_webbm.'" />
            <input id="page_custom_video_webbm_button" type="button"   size="40" class="upload_button button media_upload_button" value="'.esc_html__('Upload Video','wpresidence-core').'" />
        </div>

        <div class="property_prop_half">
            <label for="page_custom_image">'.esc_html__('Video OGV version','wpresidence-core').'</label><br />
            <input id="page_custom_video_ogv" type="text" size="36" name="page_custom_video_ogv"  class="wpestate_landing_upload" value="'.$page_custom_video_ogv.'" />
            <input id="page_custom_video_ogv_button" type="button"   size="40" class="upload_button button media_upload_button" value="'.esc_html__('Upload Video','wpresidence-core').'" />
        </div>

        <div class="property_prop_half">
            <label for="page_custom_video_cover_image">'.esc_html__('Cover Image','wpresidence-core').'</label><br />
            <input id="page_custom_video_cover_image" type="text" size="36" name="page_custom_video_cover_image"  class="wpestate_landing_upload" value="'.$page_custom_video_cover_image.'" />
            <input id="page_custom_video_cover_image_button" type="button" size="40" 
            class="upload_button button media_upload_button" value="'.esc_html__('Upload Image','wpresidence-core').'" />
        </div>

        <div class="property_prop_half">
            <label for="page_header_video_full_screen">'.esc_html__('Full Screen?','wpresidence-core').'</label><br />
            <select id="page_header_video_full_screen" name="page_header_video_full_screen">
                '.$img_full_screen.'
            </select>
        </div>

        <div class="property_prop_half">
            <label for="page_header_title_over_video">'.esc_html__('Title Over Image','wpresidence-core').'</label><br />
            <input id="page_header_title_over_video" type="text" size="36" name="page_header_title_over_video" value="'.$page_header_title_over_video.'" />
        </div>

        <div class="property_prop_half">
            <label for="page_header_subtitle_over_video">'.esc_html__('SubTitle Over Image','wpresidence-core').'</label><br />
            <input id="page_header_subtitle_over_video" type="text" size="36" name="page_header_subtitle_over_video" value="'.$page_header_subtitle_over_video.'" />
        </div>

        <div class="property_prop_half">
            <label for="page_header_video_height">'.esc_html__('Video Height(Ex:700, Default:580px)','wpresidence-core').'</label><br />
            <input id="page_header_video_height" type="text" size="36" name="page_header_video_height" value="'.$page_header_video_height.'" />
        </div>

        <div class="property_prop_half">
            <label for="page_header_overlay_color_video">'.esc_html__('Overlay Color','wpresidence-core').'</label><br />
            <div id="page_header_overlay_color_video" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$page_header_overlay_color_video.';"  ></div></div>  <input type="text" name="page_header_overlay_color_video" maxlength="7" class="inptxt " value="'.$page_header_overlay_color_video.'"/>
        </div>

        <div class="property_prop_half">
            <label for="page_header_overlay_val_video">'.esc_html__('Overlay Opacity (between 0 and 1, Ex:0.5, default 0.6)','wpresidence-core').'</label><br />
            <input id="page_header_overlay_val_video" type="text" size="36" name="page_header_overlay_val_video" value="'.$page_header_overlay_val_video.'" />
        </div>


        </div>';

}
endif; // end   estate_page_slider_box



if( !function_exists('estate_page_theme_slider') ):
function estate_page_theme_slider($post) {
    global $post;
    $rev_slider           = get_post_meta($post->ID, 'rev_slider', true);
    print '
    <div class="header_admin_options theme_slider">
        <p class="meta-options pblank">
            <h3 class="pblankh">'.esc_html__('Options for Theme Slider','wpresidence-core').'</h3>
        </p>
        <p class="meta-options">

        </p>
    </div>
    ';
}
endif; // end   estate_page_slider_box


////////////////////////////////////////////////////////////////////////////////////////////////
// Manage Google Maps
////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('estate_page_map_box') ):
function estate_page_map_box($post) {
    global $post;
    $page_lat           = get_post_meta($post->ID, 'page_custom_lat', true);
    $page_long          = get_post_meta($post->ID, 'page_custom_long', true);
    $page_custom_image  = get_post_meta($post->ID, 'page_custom_image', true);
    $page_custom_zoom   = get_post_meta($post->ID, 'page_custom_zoom', true);
    $min_height         = intval( esc_html(get_post_meta($post->ID, 'min_height', true)) );
    $max_height         = intval( esc_html(get_post_meta($post->ID, 'max_height', true)) );
    $cache_array        = array('yes','no');
    $keep_min_symbol    = '';
    $keep_min_status    = esc_html ( get_post_meta($post->ID, 'keep_min', true) );

    foreach($cache_array as $value){
            $keep_min_symbol.='<option value="'.$value.'"';
            if ($keep_min_status==$value){
                    $keep_min_symbol.=' selected="selected" ';
            }
            $keep_min_symbol.='>'.$value.'</option>';
    }



        $cache_array_r       = array('no','yes');

    $keep_max_symbol    =   '';
    $keep_max_status    = esc_html ( get_post_meta($post->ID, 'keep_max', true) );
    foreach($cache_array as $value){
        $keep_max_symbol.='<option value="'.$value.'"';
        if ($keep_max_status==$value){
                $keep_max_symbol.=' selected="selected" ';
        }
        $keep_max_symbol.='>'.$value.'</option>';
    }





    if ($page_custom_zoom==''){
        $page_custom_zoom=15;
    }
    print '<div class="header_admin_options google_map">
        <div class="meta-options pblank">
            <h3 class="pblankh">'.esc_html__('Options for Maps (if Hero Header Media Type "maps" is Selected)','wpresidence-core').'</h3>  
           
        </div>';

    if( get_post_type($post->ID)!="estate_property" ){
        print '

      

        <div class="property_prop_half">
            <label for="page_custom_lat">'.esc_html__('Maps - Center point Latitudine: ','wpresidence-core').'</label><br />
            <input type="text" id="page_custom_lat" name="page_custom_lat" size="40" value="'.$page_lat.'">
        </div>

         <div class="property_prop_half">
            <label for="page_custom_long">'.esc_html__('Maps - Center point Longitudine: ','wpresidence-core').'</label><br />
            <input type="text" id="page_custom_long" name="page_custom_long" size="40" value="'.$page_long.'">
        </div>



        <div class="property_prop_half">
            <label for="page_custom_zoom">'.esc_html__('Zoom Level for maps (1-20)','wpresidence-core').'</label><br />
            <select name="page_custom_zoom" id="page_custom_zoom">';

            for ($i=1;$i<21;$i++){
                print '<option value="'.$i.'"';
                if($page_custom_zoom==$i){
                    print ' selected="selected" ';
                }
                print '>'.$i.'</option>';
            }
            print'
            </select>
        </div>';
    }

    print'
        <div class="property_prop_half">
            <label for="min_height">'.esc_html__('Maps height when closed','wpresidence-core').'</label><br />
            <input id="min_height" type="text"   name="min_height" value="'.$min_height.'" />
        </div>

        <div class="property_prop_half">
           <label for="max_height">'.esc_html__('Maps height when open','wpresidence-core').'</label><br />
           <input id="max_height" type="text"  name="max_height" value="'.$max_height.'" />
        </div>

        <div class="property_prop_half">
            <label for="keep_min">'.esc_html__('Force maps at "closed" size? ','wpresidence-core').'</label><br />
            <select id="keep_min" name="keep_min">
            <option value=""></option>
               '.$keep_min_symbol.'
            </select>
        </div>

        <div class="property_prop_half">
            <label for="keep_max">'.esc_html__('Force maps at the full screen size? ','wpresidence-core').'</label><br />
            <select id="keep_max" name="keep_max">
            <option value=""></option>
               '.$keep_max_symbol.'
            </select>
        </div></div>';



    $cache_array        =   array('yes','no');
    $cache_array_rev    =   array('no','yes');
    $cache_array_fix    =   array('cover','contain');
    $img_full_screen                    = wpestate_dropdowns_theme_admin_option_core($post->ID,$cache_array_rev,'page_header_image_full_screen');
    $img_full_back_type                 = wpestate_dropdowns_theme_admin_option_core($post->ID,$cache_array_fix,'page_header_image_back_type');
    $page_header_title_over_image       = stripslashes ( esc_html ( get_post_meta($post->ID, 'page_header_title_over_image', true) ) );
    $page_header_subtitle_over_image    = stripslashes ( esc_html ( get_post_meta($post->ID, 'page_header_subtitle_over_image', true) ) );
    $page_header_image_height           = esc_html ( get_post_meta($post->ID, 'page_header_image_height', true) );
    $page_header_overlay_val            = esc_html ( get_post_meta($post->ID, 'page_header_overlay_val', true) );
    $page_header_overlay_color          = esc_html ( get_post_meta($post->ID, 'page_header_overlay_color', true) );

    print '
        <div class="header_admin_options image_header">
      
            <h3 class="pblankh">'.esc_html__('Options for Static Image (if Hero Header Media Type "image" is Selected)','wpresidence-core').'</h3>
   

        <div class="property_prop_half">
            <label for="page_custom_image">'.esc_html__('Header Image','wpresidence-core').'</label><br />
            <input id="page_custom_image" type="text" size="36" class="wpestate_landing_upload" name="page_custom_image" value="'.$page_custom_image.'" />
            <input id="page_custom_image_button" type="button"   size="40" class="upload_button button media_upload_button" value="'.esc_html__('Upload Image','wpresidence-core').'" />
        </div>

        <div class="property_prop_half">
            <label for="page_header_image_full_screen">'.esc_html__('Full Screen?','wpresidence-core').'</label><br />
            <select id="page_header_image_full_screen" name="page_header_image_full_screen">
                '.$img_full_screen.'
            </select>
        </div>

       <div class="property_prop_half">
            <label for="page_header_image_full_screen">'.esc_html__('Full Screen Background Type?','wpresidence-core').'</label><br />
            <select id="page_header_image_back_type" name="page_header_image_back_type">
                '.$img_full_back_type.'
            </select>
        </div>

      <div class="property_prop_half">
            <label for="page_header_title_over_image">'.esc_html__('Title Over Image','wpresidence-core').'</label><br />
            <input id="page_header_title_over_image" type="text" size="36" name="page_header_title_over_image" value="'.$page_header_title_over_image.'" />
        </div>

        <div class="property_prop_half">
            <label for="page_header_subtitle_over_image">'.esc_html__('Subtitle Over Image','wpresidence-core').'</label><br />
            <input id="page_header_subtitle_over_image" type="text" size="36" name="page_header_subtitle_over_image" value="'.$page_header_subtitle_over_image.'" />
        </div>

       <div class="property_prop_half">
            <label for="page_header_image_height">'.esc_html__('Image Height (Ex:700, Default:580px)','wpresidence-core').'</label><br />
            <input id="page_header_image_height" type="text" size="36" name="page_header_image_height" value="'.$page_header_image_height.'" />
        </div>

         <div class="property_prop_half">
            <label for="page_header_overlay_color">'.esc_html__('Overlay Color','wpresidence-core').'</label><br />

            <div id="page_header_overlay_color" class="colorpickerHolder"><div class="sqcolor" style="background-color:#'.$page_header_overlay_color.';"  ></div></div>  <input type="text" name="page_header_overlay_color" maxlength="7" class="inptxt " value="'.$page_header_overlay_color.'"/>
        </div>

        <div class="property_prop_half">
            <label for="page_header_overlay_val">'.esc_html__('Overlay Opacity (between 0 and 1, Ex:0.5, default 0.6)','wpresidence-core').'</label><br />
            <input id="page_header_overlay_val" type="text" size="36" name="page_header_overlay_val" value="'.$page_header_overlay_val.'" />
        </div>


       </div>';
}
endif; // end   estate_page_map_box


////////////////////////////////////////////////////////////////////////////////////////////////
// Manage Custom Header of the page
////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('estate_page_map_box_agent') ):
function estate_page_map_box_agent($post) {
    global $post;
    $page_lat           = get_post_meta($post->ID, 'page_custom_lat', true);
    $page_long          = get_post_meta($post->ID, 'page_custom_long', true);
    $page_custom_image  = get_post_meta($post->ID, 'page_custom_image', true);
    $page_custom_zoom  = get_post_meta($post->ID, 'page_custom_zoom', true);

    if ($page_custom_zoom==''){
        $page_custom_zoom=15;
    }

    print '

    <p class="meta-options">
        <label for="page_custom_image">'.esc_html__('Replace Maps with this image','wpresidence-core').'</label><br />
        <input id="page_custom_image" type="text" size="36" name="page_custom_image" value="'.$page_custom_image.'" />
	<input id="page_custom_image_button" type="button"   size="40" class=" button media_upload_button" value="'.esc_html__('Upload Image','wpresidence-core').'" />
     </p>

     <p class="meta-options">
       <label for="page_custom_zoom">'.esc_html__('Zoom Level for maps (1-20)','wpresidence-core').'</label><br />
       <select name="page_custom_zoom" id="page_custom_zoom">';

      for ($i=1;$i<21;$i++){
           print '<option value="'.$i.'"';
           if($page_custom_zoom==$i){
               print ' selected="selected" ';
           }
           print '>'.$i.'</option>';
       }

     print'
       </select>
     <p>
    ';

}
endif; // end   estate_page_map_box_agent


if( !function_exists('wpestate_dropdowns_theme_admin') ):
    function wpestate_dropdowns_theme_admin_option_core($post_id,$array_values,$option_name,$pre=''){

        $dropdown_return    =   '';
        $option_value       =   esc_html ( get_post_meta($post_id, $option_name, true) );
        foreach($array_values as $value){
            $dropdown_return.='<option value="'.$value.'"';
              if ( $option_value == $value ){
                $dropdown_return.='selected="selected"';
            }
            $dropdown_return.='>'.$pre.$value.'</option>';
        }

        return $dropdown_return;

    }
endif;

////////////////////////////////////////////////////////////////////////////////////////////////
// Manage page options
////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('estate_page_options_box') ):
function estate_page_options_box($post) {
    global $post;

    $page_title = get_post_meta($post->ID, 'page_show_title', true);
    $selected_no = $selected_yes = '';

    if ($page_title == 'no') {
        $selected_no = 'selected="selected"';
    } else {
        $selected_yes = 'selected="selected"';
    }

    if ($page_title != '') {
        $page_title_select = '<option value="' . $page_title . '" selected="selected">' . $page_title . '</option>';
    }

    print '

    <p class="meta-options">
    <label for="page_show_title">'.esc_html__('Show Title: ','wpresidence-core').'</label><br />
    <select id="page_show_title" name="page_show_title" style="width: 200px;">
            <option value="yes" ' . $selected_yes . '>yes</optionpage_show_title>
            <option value="no" ' . $selected_no . '>no</option>
    </select></p>';


}
endif; // end   estate_page_options_box





////////////////////////////////////////////////////////////////////////////////////////////////
// Manage Sidebars per posts/page
////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('estate_sidebar_box') ):
function estate_sidebar_box($post) {

    // Use nonce for verification
    wp_nonce_field(plugin_basename(__FILE__), 'wpestate_sidebar_noncename');
    global $post;
    global $wp_registered_sidebars ;
    $sidebar_name   = get_post_meta($post->ID, 'sidebar_select', true);
    $sidebar_option = get_post_meta($post->ID, 'sidebar_option', true);
    $allowed_global_post_types = array( 'estate_property', 'estate_agent' );

    $sidebar_values = array(   0=>'right',
                               1=>'left',
                               2=>'none');

    $option         = '';

    if( in_array( get_post_type ($post->ID), $allowed_global_post_types ) ){
     //   $sidebar_values[]='global';
        array_unshift($sidebar_values, "global");
    }

    foreach ($sidebar_values as $key=>$value) {
        $option.='<option value="' . $value . '"';
        if ($value == $sidebar_option) {
            $option.=' selected="selected"';
        }
        $option.='>' . $value . '</option>';
    }

    print '
    <div class="property_prop_half"> 
       <label for="sidebar_option">'.esc_html__('Where to Show the Sidebar: ','wpresidence-core').' </label><br />
        <select id="sidebar_option" name="sidebar_option" >
        ' . $option . '
        </select>
    </div>';

    print'
    <div class="property_prop_half">
    <label for="sidebar_select">'.esc_html__('Select the Sidebar: ','wpresidence-core').'</label><br />
        <select name="sidebar_select" id="sidebar_select" >';
        if( in_array( get_post_type ($post->ID), $allowed_global_post_types ) ){
            print '<option value="">global</option>';
        }
        foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) {
            print'<option value="' . ($sidebar['id'] ) . '"';
            if ($sidebar_name == $sidebar['id']) {
                print' selected="selected"';
            }
            print' >' . ucwords($sidebar['name']) . '</option>';
        }
        print '
        </select>
    </div>';



}
endif; // end   estate_sidebar_box





////////////////////////////////////////////////////////////////////////////////////////////////
// Saving of custom data
////////////////////////////////////////////////////////////////////////////////////////////////
if( !function_exists('estate_save_postdata') ):
function estate_save_postdata($post_id) {
    global $post;

 
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }


    if(isset($_POST['is_user_submit']) && intval($_POST['is_user_submit'])==1 ){
      return;
    }

    ///////////////////////////////////// Check permissions
    if(isset($_POST['post_type'])){
        if ('page' == $_POST['post_type'] or 'post' == $_POST['post_type'] or 'estate_property' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return;
        }
        else {
            if (!current_user_can('edit_post', $post_id))
                return;
        }
        if ('page' == $_POST['post_type'] ){
            wpestate_delete_cache_for_links();
        }

    }



    $allowed_keys=array(
        'agent_custom_label','agent_custom_value',
        'developer_email',
        'agency_email',
        'agency_long',
        'agency_lat',
        'dir_bathrooms_max',
        'dir_bedrooms_max',
        'dir_bathrooms_min',
        'dir_bedrooms_max',
        'dir_bedrooms_min',
        'dir_rooms_min',
        'dir_rooms_max',
        'dir_max_lot_size',
        'dir_min_lot_size',
        'dir_max_size',
        'dir_min_size',
        'review_stars',
        'review_title',
        'user_meda_id',
        'developer_taxes',
        'developer_address',
        'developer_email',
        'developer_phone',
        'developer_mobile',
        'developer_skype',
        'developer_facebook',
        'developer_twitter',
        'developer_linkedin',
        'developer_pinterest',
        'developer_instagram',
        'developer_website',
        'developer_lat',
        'developer_long',
        'developer_license',
        'developer_languages',
        'developer_opening_hours',
        'first_name',
        'last_name',
        'agency_address',
        'agency_email',
        'agency_phone',
        'agency_mobile',
        'agency_skype',
        'agency_facebook',
        'agency_twitter',
        'agency_linkedin',
        'agency_pinterest',
        'agency_instagram',
        'agency_website',
        'agency_languages',
        'agency_license',
        'agency_opening_hours',
        'agency_taxes',
        'agency_lat',
        'agency_long',
        'page_show_adv_search',
        'page_use_float_search',
        'page_wp_estate_float_form_top',
        'page_custom_video_webbm',
        'page_custom_video_ogv',
        'page_custom_video_cover_image',
        'page_header_overlay_val_video',
        'page_header_overlay_color_video',
        'page_header_video_height',
        'page_header_subtitle_over_video',
        'page_header_video_full_screen',
        'page_header_title_over_video',
        'page_custom_video',
        'page_header_image_back_type',
        'page_header_title_over_image',
        'page_header_subtitle_over_image',
        'page_header_image_height',
        'page_header_overlay_val',
        'page_header_overlay_color',
        'page_header_image_full_screen',
        'use_float_search_form_local_set',
        'property_theme_slider',
        'property_has_subunits',
        'property_subunits_list',
        'property_subunits_list_manual',
        'image_to_attach',
        'sidebar_option',
        'sidebar_select',
        'post_show_title',
        'group_pictures',
        'embed_video_id',
        'embed_virtual_tour',
        'embed_video_type',
        'page_show_title',
        'adv_filter_search_action',
        'adv_filter_search_category',
        'current_adv_filter_city',
        'current_adv_filter_area',
        'current_adv_filter_county',
        'listing_filter',
        'show_featured_only',
        'show_filter_area',
        'header_type',
        'header_transparent',
        'topbar_transparent',
        'topbar_border_transparent',
        'page_custom_lat',
        'page_custom_long',
        'page_custom_zoom',
        'min_height',
        'max_height',
        'keep_min',
        'keep_max',
        'page_custom_image',
        'rev_slider',
        'sidebar_agent_option',
        'local_pgpr_slider_type',
        'local_pgpr_content_type',
        'agent_position',
        'agent_email',
        'agent_phone',
        'agent_mobile',
        'agent_skype',
        'agent_facebook',
        'agent_twitter',
        'agent_linkedin',
        'agent_pinterest',
        'agent_instagram',
        'agent_youtube',
        'agent_tiktok',
        'agent_telegram',
        'agent_vimeo',
        'agent_website',
        'agent_private_notes',
        'agent_member',
        'agent_address',
        'item_id',
        'item_price',
        'purchase_date',
        'buyer_id',
        'biling_period',
        'billing_freq',
        'pack_listings',
        'mem_list_unl',
        'pack_featured_listings',
        'pack_image_included',
        'pack_price',
        'pack_visible',
        'pack_visible_user_role',
        'pack_stripe_id',
        'property_address',
        'property_zip',
        'property_state',
        'property_country',
        'property_status',
        'prop_featured',
        'property_price',
        'property_label',
        'property_label_before',
        'property_second_price_label',
        'property_second_price',
        'property_label_before_second_price',
        'property_year_tax',
        'property_hoa',
        'property_size',
        'property_lot_size',
        'property_rooms',
        'property_bedrooms',
        'property_bathrooms',
        'embed_video_type',
        'embed_video_id',
        'owner_notes',
        'property_latitude',
        'property_longitude',
        'property_google_view',
        'property_hide_map_marker',
        'google_camera_angle',
        'page_custom_zoom',
        'property_agent',
        'property_user',
        'property_agent_secondary',
        'use_floor_plans',
        'property_page_desing_local',
        'property_list_second_content',
	    'energy_class',
        'energy_index',
        'property_custom_video',
        'co2_index',
        'co2_class',
        'renew_energy_index',
        'building_energy_index',
        'epc_current_rating',
        'epc_potential_rating',
        'realtor_order',
        'realtor_order_by',
        'local_show_hide_price',
        'property_internal_id',
        'property_booking_shortcode',



    );

    if ( function_exists( 'get_field' ) && wpresidence_get_option('wpestate_show_acf_fields', 1) )   {

        // Get ACF fields for all post_types
        $estate_post_types = array('estate_property', 'estate_agency', 'estate_agent', 'estate_developer');
        foreach ($estate_post_types as $post_type) {
            $groups = acf_get_field_groups(array('post_type' => $post_type));
            if (is_array($groups) && count($groups) > 0) {
                foreach ($groups as $group) {
                    $fields = acf_get_fields($group['key']);
                    if (is_array($fields) && count($fields) > 0) {
                        foreach ($fields as $field) {
                            if (isset($field['name']) && isset($field['label']) && isset($field['type'])) {
                                // Save ACF field value to post meta
                                $allowed_keys[] = sanitize_key($field['name']);
                            }
                        }
                    }
                }
            }
        }
    }

    $custom_fields = wpresidence_get_option( 'wp_estate_custom_fields', '');

     if( !empty($custom_fields)){
        $i=0;
        while($i< count($custom_fields) ){
            $name =   $custom_fields[$i][0];
            $slug         =     wpestate_limit45(sanitize_title( $name ));
            $slug         =     sanitize_key($slug);
            $allowed_keys[]=     $slug;
            $i++;
       }
    }


    foreach ($_POST as $key => $value) {
        if( !is_array ($value) ){
            if (in_array ($key, $allowed_keys)) {
                $postmeta = wp_filter_kses( $value );
                update_post_meta($post_id, sanitize_key($key), $postmeta );
            }

        }
    }

    // Normalize featured flag for properties so new listings always persist 0/1.
    if (get_post_type($post_id) === 'estate_property') {
        $prop_featured = get_post_meta($post_id, 'prop_featured', true);
        if (intval($prop_featured) !== 1) {
            update_post_meta($post_id, 'prop_featured', 0);
        }
    }

      //
    global $allowedtags;
    $iframe = array( 'iframe' => array(

                            'width'             => array(),
                            'height'            => array(),
                            'frameborder'       => array(),
                            'style'             => array(),
                            'allowFullScreen'   => array(),
                            'allowfullscreen'   => array(),
                            'src'               => array(),
                            'allow'             => array(),
                            'scrolling'         => array(),// add any other attributes you wish to allow
                          )
    );
    $allowed_html = array_merge( $allowedtags, $iframe );

    if ( isset($_POST['embed_virtual_tour'])){
        $search="'";
        $replace='"';
        update_post_meta($post_id, 'embed_virtual_tour', (wp_kses (trim( str_replace($search,$replace,$_POST['embed_virtual_tour']) ),$allowed_html)) );
    }

    $allowed_html_desc=array(
        'a' => array(
            'href' => array(),
            'title' => array()
        ),
        'br'        =>  array(),
        'p'        =>  array(),
        'em'        =>  array(),
        'strong'    =>  array(),
        'ul'        =>  array('li'),
        'li'        =>  array(),
        'code'      =>  array(),
        'ol'        =>  array('li'),
        'del'       =>  array(
                        'datetime'=>array()
                        ),
        'iframe' => array(

                            'width'             => array(),
                            'height'            => array(),
                            'frameborder'       => array(),
                            'style'             => array(),
                            'allowFullScreen'   => array(),
                            'allowfullscreen'   => array(),
                            'src'               => array(),// add any other attributes you wish to allow
                          )


    );

    if ( isset($_POST['property_list_second_content'])){
        update_post_meta($post_id, 'property_list_second_content',wp_kses($_POST['property_list_second_content'],$allowed_html_desc)  );
    }



    //////////////////////////////////////////////////////////////////
    //// change listing author id
    //////////////////////////////////////////////////////////////////

    if ( isset($_POST['property_user']) && isset($post->ID)){

        $current_id =   wpsestate_get_author($post_id);
        $new_user   =   intval($_POST['property_user']);


        if($current_id != $new_user && $new_user!=0 ){
            // change author
           $postunit = array(
                'ID'            => $post_id,
                'post_author'   => $new_user
            );

            wp_update_post($postunit );

        }



        wp_reset_postdata();
        wp_reset_query();
    }
    ///////////////////////////// end change author id

    //////////////////////////////////////////////////////////////////
    /// save floor plan
    //////////////////////////////////////////////////////////////////

    if(isset($_POST['plan_title'])){
        update_post_meta($post_id, 'plan_title',wpestate_sanitize_array ( $_POST['plan_title'] ) );
    }else{
        if(isset($post->ID)){
            update_post_meta($post_id, 'plan_title','' );
        }
    }

    if(isset($_POST['plan_description'])){
            update_post_meta($post_id, 'plan_description',wpestate_sanitize_array ( $_POST['plan_description'] ) );
    }else{
        if(isset($post->ID)){
            update_post_meta($post_id, 'plan_description','' );
        }
    }

    if(isset($_POST['plan_image_attach'])){
            update_post_meta($post_id, 'plan_image_attach',wpestate_sanitize_array ( $_POST['plan_image_attach'] ) );
    }else{
        if(isset($post->ID)){
            update_post_meta($post_id, 'plan_image_attach','' );
        }
    }

    if(isset($_POST['plan_image'])){
            update_post_meta($post_id, 'plan_image',wpestate_sanitize_array ( $_POST['plan_image'] ) );
    }else{
        if(isset($post->ID)){
            update_post_meta($post_id, 'plan_image','' );
        }
    }

    if(isset($_POST['plan_size'])){
            update_post_meta($post_id, 'plan_size',wpestate_sanitize_array ( $_POST['plan_size'] ) );
    }else{
        if(isset($post->ID)){
            update_post_meta($post_id, 'plan_size','' );
        }
    }


      if(isset($_POST['plan_rooms'])){
            update_post_meta($post_id, 'plan_rooms',wpestate_sanitize_array ( $_POST['plan_rooms'] ) );
    }else{
        if(isset($post->ID)){
            update_post_meta($post_id, 'plan_rooms','' );
        }
    }

      if(isset($_POST['plan_bath'])){
            update_post_meta($post_id, 'plan_bath',wpestate_sanitize_array ( $_POST['plan_bath'] ) );
    }else{
        if(isset($post->ID)){
            update_post_meta($post_id, 'plan_bath','' );
        }
    }

      if(isset($_POST['plan_price'])){
            update_post_meta($post_id, 'plan_price',wpestate_sanitize_array ( $_POST['plan_price'] ) );
    }else{
        if(isset($post->ID)){
            update_post_meta($post_id, 'plan_price','' );
        }
    }


    //////////////////////////////////////// end save floor plan


    if(isset($_POST['property_subunits_list']) && isset($post->ID) ){
        $property_subunits_list = $_POST['property_subunits_list'];
        update_post_meta($post->ID, 'property_subunits_list', wpestate_sanitize_array( $property_subunits_list) );

        if(is_array($property_subunits_list)){
            foreach ($property_subunits_list as $key) {
                update_post_meta(intval($key), 'property_subunits_master',$post->ID );
            }
        }else{
            update_post_meta(intval($property_subunits_list), 'property_subunits_master',$post->ID );
        }


    }else{
        if( isset($post->ID) ){
            $already_childs = get_post_meta($post->ID, 'property_subunits_list', true) ;

            if(is_array($already_childs)){
                foreach ($already_childs as $key) {
                    delete_post_meta(intval($key), 'property_subunits_master','' );
                }
                update_post_meta($post->ID, 'property_subunits_list', '') ;
            }
        }

    }




    if(isset($_POST['adv_filter_search_action'])){
        $adv_filter_search_action = wpestate_sanitize_array($_POST['adv_filter_search_action']);
        if (is_array($adv_filter_search_action)) {
            $adv_filter_search_action = array_values(array_filter($adv_filter_search_action, 'strlen'));
        }

        if (empty($adv_filter_search_action)) {
            delete_post_meta($post_id, 'adv_filter_search_action');
        } else {
            update_post_meta($post_id, 'adv_filter_search_action', $adv_filter_search_action);
        }
    }else{
         if(isset($post->ID)){
            update_post_meta($post->ID, 'adv_filter_search_action','' );
         }
    }

    if(isset($_POST['adv_filter_search_category'])){
       $adv_filter_search_category = wpestate_sanitize_array($_POST['adv_filter_search_category']);
       if (is_array($adv_filter_search_category)) {
           $adv_filter_search_category = array_values(array_filter($adv_filter_search_category, 'strlen'));
       }

       if (empty($adv_filter_search_category)) {
           delete_post_meta($post_id, 'adv_filter_search_category');
       } else {
           update_post_meta($post_id, 'adv_filter_search_category', $adv_filter_search_category);
       }
    }else{
       if(isset($post->ID)){
           update_post_meta($post->ID, 'adv_filter_search_category','' );
       }
    }

    if(isset($_POST['current_adv_filter_city'])){
       $current_adv_filter_city = wpestate_sanitize_array($_POST['current_adv_filter_city']);
       if (is_array($current_adv_filter_city)) {
           $current_adv_filter_city = array_values(array_filter($current_adv_filter_city, 'strlen'));
       }

       if (empty($current_adv_filter_city)) {
           delete_post_meta($post_id, 'current_adv_filter_city');
       } else {
           update_post_meta($post_id, 'current_adv_filter_city', $current_adv_filter_city);
       }
    }else{
       if(isset($post->ID)){
           update_post_meta($post->ID, 'current_adv_filter_city','' );
       }
    }

    if(isset($_POST['current_adv_filter_county'])){
       $current_adv_filter_county = wpestate_sanitize_array($_POST['current_adv_filter_county']);
       if (is_array($current_adv_filter_county)) {
           $current_adv_filter_county = array_values(array_filter($current_adv_filter_county, 'strlen'));
       }

       if (empty($current_adv_filter_county)) {
           delete_post_meta($post_id, 'current_adv_filter_county');
       } else {
           update_post_meta($post_id, 'current_adv_filter_county', $current_adv_filter_county);
       }
    }else{
        if(isset($post->ID)){
            update_post_meta($post->ID, 'current_adv_filter_county','' );
        }
     }


     if(isset($_POST['current_adv_filter_area'])){
        $current_adv_filter_area = wpestate_sanitize_array($_POST['current_adv_filter_area']);
        if (is_array($current_adv_filter_area)) {
            $current_adv_filter_area = array_values(array_filter($current_adv_filter_area, 'strlen'));
        }

        if (empty($current_adv_filter_area)) {
            delete_post_meta($post_id, 'current_adv_filter_area');
        } else {
            update_post_meta($post_id, 'current_adv_filter_area', $current_adv_filter_area);
        }
    }else{
        if(isset($post->ID)){
           update_post_meta($post->ID, 'current_adv_filter_area','' );
        }
    }



    // theme slider
    if(isset($_POST['property_theme_slider']) ){
        $theme_slider         =   wpresidence_get_option( 'wp_estate_theme_slider', '');
        $theme_slider_manual  =   wpresidence_get_option( 'wp_estate_theme_slider_manual', '');
        $is_slider            =   intval ( $_POST['property_theme_slider'] );

        if( isset($post->ID) ){
            if( $is_slider==1){
                if( is_array($theme_slider) && !in_array($post->ID, $theme_slider) ){
                    $theme_slider[]=$post->ID;
                }else{
                     $theme_slider[]=$post->ID;
                }
            }else{
                if( is_array($theme_slider) ){
                    if(($key = array_search($post->ID, $theme_slider)) !== false) {
                        unset($theme_slider[$key]);
                    }
                }
            }
         //   update_option( 'wp_estate_theme_slider', $theme_slider);
            Redux::setOption('wpresidence_admin','wp_estate_theme_slider', $theme_slider );
            Redux::setOption('wpresidence_admin','wp_estate_theme_slider_manual', $theme_slider_manual );

        }
    }



    if(isset($_POST['property_agent_secondary'])){
        update_post_meta( $post_id, 'property_agent_secondary',$_POST['property_agent_secondary'] );
        foreach($_POST['property_agent_secondary']  as $key=>$value){
            $agent_listings_as_sec = get_post_meta($value,'secondary_listings',true);
            if( is_array($agent_listings_as_sec) ){
                $agent_listings_as_sec[]= $post_id;
            }else{
                $agent_listings_as_sec=array();
                $agent_listings_as_sec[]=$post_id;
            }
            update_post_meta($value,'secondary_listings',$agent_listings_as_sec);

        }
    }
    if( isset($post->ID) && isset($_POST['property_address'])){
        wpestate_update_hiddent_address_single($post->ID);
    }


    if(isset($_POST['pack_visible_user_role'])){
        update_post_meta($post->ID,'pack_visible_user_role',$_POST['pack_visible_user_role']);
    }


    if( isset($post->ID) && isset($_POST['image_to_attach']) ){
        if ( get_post_type( $post->ID ) == 'post' ) {
            $imageIds = array_filter( explode(',',$_POST['image_to_attach'] ) );
            $imageIds = array_map( 'intval', $imageIds );

            $imageIds = array_values( array_unique( $imageIds ) );

            $existing_attachments = get_posts(
                array(
                    'fields'         => 'ids',
                    'numberposts'    => -1,
                    'post_parent'    => $post->ID,
                    'post_type'      => 'attachment',
                    'post_status'    => null,
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                )
            );

            if ( ! empty( $existing_attachments ) ) {
                $keep_lookup = array_flip( $imageIds );

                foreach ( $existing_attachments as $existing_id ) {
                    if ( isset( $keep_lookup[ $existing_id ] ) ) {
                        continue;
                    }

                    wp_update_post(
                        array(
                            'ID'           => $existing_id,
                            'post_parent' => 0,
                        )
                    );

                    $existing_meta = wp_get_attachment_metadata( $existing_id );

                    if ( is_array( $existing_meta ) && isset( $existing_meta['post_id'] ) && intval( $existing_meta['post_id'] ) === intval( $post->ID ) ) {
                        unset( $existing_meta['post_id'] );
                        wp_update_attachment_metadata( $existing_id, $existing_meta );
                    }
                }
            }

            
            foreach ( $imageIds as $menu_order => $imageId ) {
                if ( ! wp_attachment_is_image( $imageId ) ) {
                    continue;
                }
                wp_update_post(
                    array(
                        'ID'           => $imageId,
                        'post_parent' => $post->ID,
                          'menu_order'   => intval( $menu_order ),
                    )
                );
                $image_meta = wp_get_attachment_metadata( $imageId );
                if ( ! $image_meta ) {
                    continue;
                }
                $image_meta['post_id'] = $post->ID;
                wp_update_attachment_metadata( $imageId, $image_meta );
            }
        } else {
            update_post_meta( $post->ID, 'wpestate_property_gallery', array_filter( explode(',',$_POST['image_to_attach'] ) ) );
        }

    }
    if(isset( $post->ID)){
        $post_type= get_post_type( $post->ID );
        wpestate_api_clear_post_cache($post->ID,$post_type);
    }

    wp_reset_postdata();


}
endif; // end   estate_save_postdata

if( !function_exists('wpestate_sanitize_array') ):
    function wpestate_sanitize_array($original){
        $new_Array=array();
        $allowed_html=array();
        foreach($original as $key=>$value){
            $new_Array[sanitize_key($key)]=  wp_kses(esc_html($value),$allowed_html);
        }
        return $new_Array;
    }
endif;



if(!function_exists('extend_comment_edit_metafields')):
function extend_comment_edit_metafields( $comment_id ) {

    if( ! isset( $_POST['extend_comment_update'] ) || ! wp_verify_nonce( $_POST['extend_comment_update'], 'extend_comment_update' ) ){
        return;
    }

    if ( ( isset( $_POST['review_stars'] ) ) && ( $_POST['review_stars'] != '') ) {
	update_comment_meta( $comment_id, 'review_stars',  intval($_POST['review_stars']) );
    }
    if ( ( isset( $_POST['review_title'] ) ) && ( $_POST['review_title'] != '') ) {
	update_comment_meta( $comment_id, 'review_title',  esc_html($_POST['review_title']) );
    }

}
endif;


if(!function_exists('wpestate_comment_starts')):
    function wpestate_comment_starts($comment){

        $review_title = get_comment_meta( $comment->comment_ID , 'review_title', true );
        $stars =  get_comment_meta( $comment->comment_ID , 'review_stars', true );
        $i=1;
        $starts_select='';

        while ($i<=5){
            $starts_select  .=  '<option value="'.$i.'"';
            if($stars==$i){
              $starts_select .=' selected="selected" ';
            }
            $starts_select  .=  '>'.$i.'</option>';
            $i++;
        }
        wp_nonce_field( 'extend_comment_update', 'extend_comment_update', false );

        print '
            <table>
            <tr>
                <td width="33%" valign="top" align="left">
                    '.esc_html__( 'Stars','wpresidence-core').'
                </td>
                <td width="33%" valign="top" align="left">

                    <select name="review_stars">
                        '.$starts_select.'
                    </select>
                </td>
            </tr>
            <tr>
                <td width="33%" valign="top" align="left">
                    '.esc_html__( 'Review Title','wpresidence-core').'
                </td>
                <td width="33%" valign="top" align="left">

                    <input type="text" name="review_title"  id="review_title" value ="'.$review_title.'" >
                </td>
            </tr>

             </table>';
    }
endif;

if ( !function_exists('estate_realtor_order') ) :
    function estate_realtor_order($post) {
        
        $agents_order = get_post_meta( $post->ID, 'realtor_order', true );
        $agents_orderby = get_post_meta( $post->ID, 'realtor_order_by', true );

        $orderArray = array( 'asc' => 'Ascending', 'desc' => 'Descending' );
        $orderByArray = array( 'ID'=> 'ID', 'title' => 'Name', 'date' => 'Date', 'rand' => 'Random' );

        $orderOptions = '<option value=""></option>';
        foreach ( $orderArray as $key => $value )   {
            $selected = '';
            if ( $agents_order == $key )
                $selected = 'selected';

            $orderOptions .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
        }

        $orderbyOptions = '<option value=""></option>';
        foreach ( $orderByArray as $key => $value )   {
            $selected = '';
            if ( $agents_orderby == $key )
                $selected = 'selected';

            $orderbyOptions .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
        }

        print '
        <table style="width: 100%;">
            <tr>
                <td width="50%" valign="top" align="left">
                <label class="wpresidence-label" for="page_header_title_over_image">'.esc_html__('Order','wpresidence-core').'</label><br />
                <select class="wpresidence-2025-select" name="realtor_order" style="width: 50%;">
                    '.$orderOptions.'
                </select>
                </td>
                <td width="50%" valign="top" align="left">
                <label class="wpresidence-label" for="page_header_title_over_image">'.esc_html__('Order By','wpresidence-core').'</label><br />
                <select class="wpresidence-2025-select" name="realtor_order_by" style="width: 50%;">
                    '.$orderbyOptions.'
                </select>
                </td>
            </tr>
        </table>
        ';

    }
endif;



?>
