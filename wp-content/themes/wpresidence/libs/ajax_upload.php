<?php

// Register AJAX actions for file uploads and deletions
add_action('wp_ajax_nopriv_wpestate_me_upload', 'wpestate_me_upload');
add_action('wp_ajax_wpestate_me_upload', 'wpestate_me_upload');
add_action('wp_ajax_wpestate_delete_file', 'wpestate_delete_file');

/**
 * Delete File AJAX Handler
 * 
 * Handles the deletion of uploaded files with proper security checks
 * to ensure only authorized users can delete files.
 * 
 * @return void Outputs JSON response and exits
 */
if( !function_exists('wpestate_delete_file') ):
    function wpestate_delete_file(){

        // Verify security nonce based on admin status
        if(isset($_POST['isadmin']) && intval($_POST['isadmin'])==1 ){
            check_ajax_referer( 'wpestate_attach_delete', 'security' );
        }else{
            check_ajax_referer( 'wpestate_image_upload', 'security' );
        }

        // Get current user information
        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;

        // Check if user is logged in
        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        if($userID === 0 ){
            exit('out pls');
        }

        // Get attachment information
        $attach_id  = intval($_POST['attach_id']);
        $the_post   = get_post( $attach_id);

        // Verify user has permission to delete the file
        if (!current_user_can('manage_options') ){
            if( $userID != $the_post->post_author ) {
                exit('you don\'t have the right to delete this');;
            }
        }

        //wp_delete_attachment($attach_id, true);
        exit;
    }
endif;

/**
 * File Upload AJAX Handler
 * 
 * Processes file uploads from the frontend and prepares them
 * for further processing.
 * 
 * @return void Processes the uploaded file
 */
if( !function_exists('wpestate_me_upload') ):
    function wpestate_me_upload(){
        // Get current user information
        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;

        // Prepare file data
        $filename       =   convertAccentsAndSpecialToNormal($_FILES['aaiu_upload_file']['tmp_name']);
        $base           =   '';
        $allowed_html   =   array();

        // Get image dimensions
        list($width, $height) = getimagesize($filename);

        // Check for base parameter
        if(isset($_GET['base'])){
            $base   =   esc_html( wp_kses( $_GET['base'], $allowed_html ) );
        }
        $page_template='';
        if(isset($_GET['page_template'])){
            // Select appropriate image size based on template
            $page_template = isset($_GET['page_template']) ? sanitize_text_field($_GET['page_template']) : '';
        }

        // Create file data array
        $file = array(
            'name'      => convertAccentsAndSpecialToNormal($_FILES['aaiu_upload_file']['name']),
            'type'      => $_FILES['aaiu_upload_file']['type'],
            'tmp_name'  => $_FILES['aaiu_upload_file']['tmp_name'],
            'error'     => $_FILES['aaiu_upload_file']['error'],
            'size'      => $_FILES['aaiu_upload_file']['size'],
            'width'     =>  $width,
            'height'    =>  $height,
            'base'      =>  $base,
            'page_template'=>$page_template,
        );
        // Process the file upload
        $file = fileupload_process($file);
    }
endif;

/**
 * Process File Upload
 * 
 * Handles file validation, processing and returns appropriate response.
 * 
 * @param array $file File data array
 * @return void Outputs JSON response and exits
 */
if( !function_exists('fileupload_process') ):
    function fileupload_process($file){

    // Validate image dimensions (except for PDFs)
    if( $file['type']!='application/pdf'    ){
        if( intval($file['height'])<500 || intval($file['width']) <500 ){
            $response = array('success' => false,'image'=>true);
            print json_encode($response);
            exit;
        }
    }

    // Handle the file upload
    $attachment = handle_file($file);
    $attachment['page_template']=$file['page_template'];

    // If successful, prepare response with attachment data
    if (is_array($attachment)) {
        $html = getHTML($attachment);

        $response = array(
            'base' =>  $file['base'],
            'type'      =>  $file['type'],
            'height'      =>  $file['height'],
            'width'      =>  $file['width'],
            'success'   => true,
            'html'      => $html,
            'attach'    => $attachment['id'],
        );

        print json_encode($response);
        exit;
    }

    // Return error if attachment creation failed
    $response = array('success' => false);
    print json_encode($response);
    exit;
    }
endif;

/**
 * Handle File Upload
 * 
 * Uploads the file to WordPress media library and creates attachment.
 * 
 * @param array $upload_data File data to be uploaded
 * @return array|bool Attachment data or false on failure
 */
if( !function_exists('handle_file') ):
    function handle_file($upload_data){
        $return = false;

        // Use WordPress file upload handler
        $uploaded_file = wp_handle_upload($upload_data, array('test_form' => false));

        if (isset($uploaded_file['file'])) {
            $file_loc   =   $uploaded_file['file'];
            $file_name  =   basename($upload_data['name']);
            $file_type  =   wp_check_filetype($file_name);

            // Prepare attachment data
            $attachment = array(
                'post_mime_type'    => $file_type['type'],
                'post_title'        => preg_replace('/\.[^.]+$/', '', basename($file_name)),
                'post_content'      => '',
                'post_status'       => 'inherit'
            );

            // Insert attachment into media library
            $attach_id      =   wp_insert_attachment($attachment, $file_loc);
            $attach_data    =   wp_generate_attachment_metadata($attach_id, $file_loc);
            wp_update_attachment_metadata($attach_id, $attach_data);

            // Return attachment information
            $return = array('data' => $attach_data, 'id' => $attach_id);

            return $return;
        }

        return $return;
    }
endif;

/**
 * Get HTML for Attachment
 * 
 * Generates HTML output for the uploaded image based on attachment data.
 * 
 * @param array $attachment Attachment data
 * @return string HTML string with image URL
 */
if( !function_exists('getHTML') ):
    function getHTML($attachment){
        $attach_id  =   $attachment['id'];
        $file       =   '';
        $html       =   '';

        if( isset($attachment['data']['file'])){
            // Extract file path
            $file       =   explode('/', $attachment['data']['file']);
            $file       =   array_slice($file, 0, count($file) - 1);
            $path       =   implode('/', $file);
          

            if($attachment['page_template'] == 'page-templates/user_dashboard_add.php') {
                $image = $attachment['data']['sizes']['agent_picture_thumb']['file'];
            } else {
                $image = $attachment['data']['sizes']['property_listings']['file'];
            }
            // Build complete image URL
            $dir        =   wp_upload_dir();
            $path       =   $dir['baseurl'] . '/' . $path;
            $html   .=   $path.'/'.$image;
        }

        return $html;
    }
endif;


/**
 * Register AJAX actions for image caption functionality
 */
add_action('wp_ajax_wpestate_image_caption',  'wpestate_image_caption');

/**
 * Update Image Caption AJAX Handler
 * 
 * Handles updating the caption/excerpt for an attachment post.
 * Includes security checks to ensure only authorized users can edit captions.
 * 
 * @return void Exits with success or error message
 */
if( !function_exists('wpestate_image_caption') ):
    function wpestate_image_caption(){
        // Verify nonce for security
        check_ajax_referer( 'wpestate_image_upload', 'security' );
        
        // Get current user information
        $current_user   =   wp_get_current_user();
        $userID         =   $current_user->ID;
        
        // Check if user is logged in
        if ( !is_user_logged_in() ) {
            exit('ko');
        }
        
        // Additional check for valid user ID
        if($userID === 0 ){
            exit('out pls');
        }
        
        // Get attachment ID and caption from POST data
        $attach_id  =   intval($_POST['attach_id']);
        $caption    =   esc_html($_POST['caption']);
        
        // Get attachment post data
        $the_post   =   get_post( $attach_id);
        
        // Get list of agents associated with current user
        $agent_list                     =  (array) get_user_meta($userID,'current_agent_list',true);
        
        // Check permissions - only allow admin, post author, or associated agent to edit
        if (!current_user_can('manage_options') ){
            if( $userID != $the_post->post_author  &&  !in_array($the_post->post_author , $agent_list)) {
                exit('you don\'t have the right to edit this');;
            }
        }
        
        // Prepare post data for update
        $my_post = array(
            'ID'           => $attach_id,
            'post_excerpt' => $caption,
        );
        
      // Update the post into the database
        wp_update_post( $my_post );
        exit;
    }
endif;
