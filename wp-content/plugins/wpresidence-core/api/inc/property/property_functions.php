<?php

add_action('wp_ajax_wp_estate_handle_property_action', 'wp_estate_handle_property_action');

/**
 * Handle AJAX requests for property actions like approve, disapprove, expire, on-hold, sold, featured, and duplicate.
 *
 * @return void
 */
/**
* Handle property actions via AJAX
* 
* Processes various property management actions including approve, disapprove, expire,
* on-hold, sold status toggle, featured toggle, and property duplication.
* 
* Expected POST parameters:
* - action_type: The action to perform (approve|disapprove|expire|on-hold|sold|featured|duplicate)
* - post_id: The ID of the property to modify
* 
* @since 1.0.0
* @return void Sends JSON response and exits
*/
function wp_estate_handle_property_action() {

   // Get and sanitize the action type from POST data
   $action_type = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';
   // Get and validate the post ID from POST data
   $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

   // Validate that we have both required parameters
   if (!$post_id || !$action_type) {
       wp_send_json_error(array('error' => __('Invalid request', 'wpresidence-core')));
   }

   // Retrieve the property post object
   $property = get_post($post_id);
   // Verify the property exists and is the correct post type
   if (!$property || $property->post_type !== 'estate_property') {
       wp_send_json_error(array('error' => __('Property not found', 'wpresidence-core')));
   }

   // Handle the action based on the action type
   switch ($action_type) {
       case 'approve':
           // Change post status to published
           wp_update_post(array(
               'ID' => $post_id,
               'post_status' => 'publish'
           ));
           break;
       case 'disapprove':
           // Change post status to disabled
           wp_update_post(array(
               'ID' => $post_id,
               'post_status' => 'disabled'
           ));
       
           break;
       case 'expire':
           // Change post status to expired
           wp_update_post(array(
               'ID' => $post_id,
               'post_status' => 'expired'
           ));
         
           break;
       case 'on-hold':
           // Change post status to pending
           wp_update_post(array(
               'ID' => $post_id,
               'post_status' => 'pending'
           ));
           break;
       case 'sold':
           // Get the sold term ID from plugin options
           $soldTermID = intval(wpresidence_get_option('wpestate_mark_sold_status'));
    
           // Check if the sold term exists in the property_status taxonomy
           if (term_exists($soldTermID, 'property_status')) {
               // If property doesn't have the sold term, add it
               if (!has_term($soldTermID, 'property_status', $post_id)) {
                   wp_set_post_terms($post_id, array($soldTermID), 'property_status', true);
               } else {
                   // If the property is already marked as sold, remove the term (toggle functionality)
                   wp_remove_object_terms($post_id, $soldTermID, 'property_status');
               }
           } 
           break;
       case 'featured':
           // Get current featured status
           $featured = get_post_meta($post_id, 'prop_featured', true);
           if ($featured) {
               // Unmark as featured
               update_post_meta($post_id, 'prop_featured', false);
           } else {
               // Mark as featured
               update_post_meta($post_id, 'prop_featured', true);
           }
           break;
       case 'duplicate':

           // Get current user and check payment/agency settings
           $current_user = wp_get_current_user();
           $paid_submission_status = esc_html(wpresidence_get_option('wp_estate_paid_submission', ''));
           $parent_userID = wpestate_check_for_agency($current_user->ID);
           // Duplicate the property by creating a new post
           $new_post_id = wp_insert_post(array(
               'post_title' => $property->post_title . ' ' . __('(Copy)', 'wpresidence-core'),
               'post_content' => $property->post_content,
               'post_status' => 'draft',
               'post_type' => 'estate_property',
               'post_author' => $property->post_author,
           ));
           /*
           * get all current post terms ad set them to the new post draft
           */
           // Get all taxonomies for this post type
           $taxonomies = get_object_taxonomies( $property->post_type ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
           if( $taxonomies ) {
               // Loop through each taxonomy and copy terms to new post
               foreach( $taxonomies as $taxonomy ) {
                   $post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
                   wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
               }
           }

           // duplicate all post meta
           $post_meta = get_post_meta( $post_id );
           if( $post_meta ) {

               // Loop through each meta key and copy to new post
               foreach ( $post_meta as $meta_key => $meta_values ) {
                   // we need to exclude some system meta keys
                   if( in_array( $meta_key, array( '_edit_lock', '_wp_old_slug' ) ) ) {
                       continue;
                   }
                   // do not forget that each meta key can have multiple values
                   foreach ( $meta_values as $meta_value ) {
                       add_post_meta( $new_post_id, $meta_key, $meta_value );
                   }
               }

               // update memberhip package
                   if ($paid_submission_status == 'membership') { // update pack status
                       // Update user's listing count for membership
                       wpestate_update_listing_no($parent_userID);
                   }
               //defaults
               // Set default values for duplicated property
               update_post_meta($new_post_id, 'sidebar_agent_option', 'global');
               update_post_meta($new_post_id, 'local_pgpr_slider_type', 'global');
               update_post_meta($new_post_id, 'local_pgpr_content_type', 'global');
               update_post_meta($new_post_id, 'prop_featured', 0);
               update_post_meta($new_post_id, 'pay_status', 'not paid');
               update_post_meta($new_post_id, 'page_custom_zoom', 16);
           }
           break;
       default:
           // Handle unknown actions
           wp_send_json_error(array('error' => __('Unknown action', 'wpresidence-core')));
           break;
   }

   // We need to replace the buttons based on new status
   $buttons = wp_estate_display_action_buttons($post_id);
   // Get the current post status
   $status = get_post_status($post_id);
   // Override status for specific actions that change display but not actual post status
   if ( $action_type == 'expired' ) {
       $status = 'expired';
   } elseif ( $action_type == 'disapprove' ) {
       $status = 'disabled';
   } 
   // Define status display text mapping
   $status_map = array(
       'expired'  => esc_html__('Expired', 'wpresidence'),
       'publish'  => esc_html__('Published', 'wpresidence'),
       'disabled' => esc_html__('Disabled', 'wpresidence'),
       'draft'    => esc_html__('Draft', 'wpresidence'),
       
       'default'  => esc_html__('Waiting for approval', 'wpresidence')
   );
   // Get the display text for current status
   $statusText = isset($status_map[$status]) ? $status_map[$status] : $status_map['default'];
   // Create CSS class from status text
   $status_class = sanitize_key(strtolower($statusText));


   // Special handling for sold action - get actual term name instead of generic status
   if( $action_type == 'sold'){
  
           // Get the sold term ID from options
           $soldTermID = intval(wpresidence_get_option('wpestate_mark_sold_status', '', 'sold'));
           // Check if property has the sold term assigned
           if ($soldTermID > 0 && has_term($soldTermID, 'property_status', $post_id)) {
               // Get the actual term object
               $soldTerm = get_term($soldTermID, 'property_status');
               // If term exists and is valid, use its name as status text
               if (!is_wp_error($soldTerm) && $soldTerm) {
                
                   $statusText = $soldTerm->name;
                       $status_class = sanitize_key(strtolower($statusText));
               }
           }
   }




   // Prepare response data array
   $responseArray = array(
    'soldTermID'=>$soldTermID,
       'action_type' => $action_type,
       'post_id' => $post_id,
       'status' => $status,
       'status_text' => $statusText,
       'status_class' => $status_class,
       'buttons' => $buttons
   );



   // Add featured status text for featured actions
   if ( $action_type == 'featured' ) {
       $responseArray['featured_text'] = !$featured ? esc_html__('Yes', 'wpresidence-core') : esc_html__('No', 'wpresidence-core');
   }

   // Send success response with data
   wp_send_json_success( $responseArray );
}







/* * Display action buttons for a property based on its status.
 *
 * @param int $postID The ID of the property post.
 * @return string HTML string containing action buttons.
 */
function wp_estate_display_action_buttons( $postID ) {

    $post = get_post( $postID );
    if ( ! $post ) {
        return;
    }

    $edit_link = get_edit_post_link($postID);
    $featured = get_post_meta($postID, 'prop_featured', true);
    $featuredIcon = $featured ? '<i class="el el-star"></i>' : '<i class="el el-star-empty"></i>';
    $featuredString = $featured ? esc_html__('Remove from featured', 'wpresidence-core') : esc_html__('Mark as featured', 'wpresidence-core');

    $status = get_post_status($postID);
    $soldTermID = intval(wpresidence_get_option('wpestate_mark_sold_status', ''));
    $soldTerm = get_term($soldTermID, 'property_status');
    $sold = false;
    if ( has_term($soldTermID, 'property_status', $postID) ) {
        $sold = true;
    }

    if (is_wp_error($soldTerm)) {
        $soldString = ''; // or handle the error appropriately
    } else {
        $soldString = ($sold === true) ? esc_html__('Remove from ', 'wpresidence-core') . $soldTerm->name : esc_html__('Mark as ', 'wpresidence-core') . $soldTerm->name;
    }


    if ( $status === 'draft' || $status === 'expired' || $status === 'pending'|| $status === 'disabled' ) {
        $goLive = true;
    } else {
        $goLive = false;
    }

    $returnString = '';

    if ( $goLive ) {
        $returnString .= '<a href="' . esc_url($edit_link) . '" class="button wpresidence_button wpresidence_properties_action_admin approve" data-action="approve" data-postid="' . esc_attr($postID) . '" title="' . esc_html__('Approve', 'wpresidence-core') . '"><i class="el el-thumbs-up"></i></a> ';
        $returnString .= '<a href="' . esc_url($edit_link) . '" class="button wpresidence_button wpresidence_properties_action_admin duplicate" data-action="duplicate" data-postid="' . esc_attr($postID) . '" title="' . esc_html__('Duplicate', 'wpresidence-core') . '"><i class="el el-view-mode"></i></a> ';
    } else {
        $returnString .= '<a href="' . esc_url($edit_link) . '" class="button wpresidence_button wpresidence_properties_action_admin disapprove" data-action="disapprove" data-postid="' . esc_attr($postID) . '" title="' . esc_html__('Disapprove', 'wpresidence-core') . '"><i class="el el-stop"></i></a> ';
        $returnString .= '<a href="' . esc_url($edit_link) . '" class="button wpresidence_button wpresidence_properties_action_admin featured" data-action="featured" data-postid="' . esc_attr($postID) . '" title="' . $featuredString . '">' . $featuredIcon . '</a> ';
        $returnString .= '<a href="' . esc_url($edit_link) . '" class="button wpresidence_button wpresidence_properties_action_admin expire" data-action="expire" data-postid="' . esc_attr($postID) . '" title="' . esc_html__('Expire', 'wpresidence-core') . '"><i class="el el-time"></i></a> ';
        $returnString .= '<a href="' . esc_url($edit_link) . '" class="button wpresidence_button wpresidence_properties_action_admin on-hold" data-action="on-hold" data-postid="' . esc_attr($postID) . '" title="' . esc_html__('On hold', 'wpresidence-core') . '"><i class="el el-pause"></i></a> ';
        if ( $soldString !== '' )   {
            $returnString .= '<a href="' . esc_url($edit_link) . '" class="button wpresidence_button wpresidence_properties_action_admin sold" data-action="sold" data-postid="' . esc_attr($postID) . '" title="' . $soldString . '"><i class="el el-ok"></i></a> ';
        }
        $returnString .= '<a href="' . esc_url($edit_link) . '" class="button wpresidence_button wpresidence_properties_action_admin duplicate" data-action="duplicate" data-postid="' . esc_attr($postID) . '" title="' . esc_html__('Duplicate', 'wpresidence-core') . '"><i class="el el-view-mode"></i></a> ';
    }

    return $returnString;

}

add_action('admin_footer', 'wpestate_append_post_status_list');

/**
 * Append custom post statuses to the post status dropdown in the admin area.
 *
 * This function adds "Expired" and "Disabled" options to the post status dropdown
 * for properties, allowing administrators to easily set these statuses.
 */
function wpestate_append_post_status_list() {
    global $post;
    $complete = '';
    $label = '';
    
    if ($post && $post->post_type == 'estate_property') { // Change 'post' to your post type if needed
        if ($post->post_status == 'expired') {
            $complete = ' selected="selected"';
            $label = '<span id="post-status-display">'.esc_html('Expired','wpresidence-core').'</span>';
        } elseif ($post->post_status == 'disabled') {
            $complete = ' selected="selected"';
            $label = '<span id="post-status-display">'.esc_html('Disabled','wpresidence-core').'</span>';
        }
        echo "<script>
        jQuery(document).ready(function($) {
            $('select#post_status').append('<option value=\"expired\" {$complete}>" . esc_html('Expired','wpresidence-core') . "</option>');
            $('select#post_status').append('<option value=\"disabled\" {$complete}>" . esc_html('Disabled','wpresidence-core') . "</option>');
            $('.misc-pub-section').append('{$label}');
        });
        </script>";
  
    }
}