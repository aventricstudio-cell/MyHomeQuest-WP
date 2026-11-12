<?php
/**
 * WpResidence review Metaboxes
 *
 * This file contains functions that create and populate custom metaboxes
 * for the 'wpestate_review' custom post type in the WordPress admin area.
 * These metaboxes provide a user interface for viewing and managing review
 * details such as sender, recipient, and review status.
 *
 * @package    WpResidence
 * @subpackage Messaging
 * @version    1.0
 * @author     WpResidence
 */



 
/**
 * Registers the custom metabox for review details
 *
 * This function hooks into WordPress to add a custom metabox to the 'estate_review'
 * post type edit screen. The metabox displays review-specific information and allows
 * administrators to view and modify review properties.
 *
 * @uses add_meta_box() WordPress function to register a metabox
 * @return void
 */
if( !function_exists('wpestate_add_reviews_metaboxes') ):
    function wpestate_add_reviews_metaboxes() {
      add_meta_box(  'estate_reviews-sectionid', esc_html__(  'Review Options', 'wpresidence-core' ), 'wpestate_review_options', 'estate_review' ,'normal','default');
    }
endif; // end

if(!function_exists('wpestate_review_options')):
    function wpestate_review_options($post){

        // $review_title = get_comment_meta( $comment->comment_ID , 'review_title', true );
        $stars =  get_post_meta( $post->ID , 'reviewer_rating', true );
        $attached_to = get_post_meta( $post->ID , 'attached_to', true );
        $where_to_display_val = get_post_type( $attached_to );
        $author = get_post_meta( $post->ID , 'review_author', true );
        if ( ! $author ) {
            $author = 1; // Fallback to current user if no author is set
        }
        $authorObj = get_user_by( 'id', $author );
        $i=1;
        $starts_select='';

        $where_to_display = array( 'estate_property'  => esc_html__( 'Property', 'wpresidence-core' ),
                                   'estate_agent'     => esc_html__( 'Agent', 'wpresidence-core' ),
                                   'estate_agency'    => esc_html__( 'Agency', 'wpresidence-core' ),
                                   'estate_developer' => esc_html__( 'Developer', 'wpresidence-core' ) );

        if ( $where_to_display_val == '' || !array_key_exists($where_to_display_val, $where_to_display) ) {
            $where_to_display_val = 'estate_property'; // Default to property if not set or invalid
        }

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
            <table width="50%">
            <tr>
                <td width="33%" valign="top" align="left">
                    '.esc_html__( 'Author','wpresidence-core').'
                </td>
                <td width="50%" valign="top" align="left">
                    <input type="text" name="reviewer_name" class="wpresidence-2025-input" value="'.esc_attr($authorObj->data->user_nicename).'" style="width:100%;" />
                </td>
            </tr>
            <tr>
                <td width="33%" valign="top" align="left">
                    '.esc_html__( 'Where to display','wpresidence-core').'
                </td>
                <td width="50%" valign="top" align="left">
                    <select class="wpresidence-2025-select" name="where_to_display">';
                        foreach($where_to_display as $key=>$value){
                            $selected = '';
                            if($where_to_display_val == $key){
                                $selected = ' selected="selected" ';
                            }
                            print '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                        }
                    print '</select>
                </td>
            </tr>
            <tr>
                <td width="33%" valign="top" align="left">
                    '.esc_html__( 'Attached to','wpresidence-core').'
                </td>
                <td width="50%" valign="top" align="left">
                    <select class="wpresidence-2025-select" name="attached_to">
                        <option value="">' . esc_html__( 'Select', 'wpresidence-core' ) . '</option>';
                        if($attached_to){
                            $attached_to = intval($attached_to);
                            $post_title = get_the_title($attached_to);
                            print '<option value="'.$attached_to.'" selected="selected">'.$post_title.'</option>';
                        }
                    print '</select>
                </td>
            </tr>
            <tr>
                <td width="33%" valign="top" align="left">
                    '.esc_html__( 'Rating','wpresidence-core').'
                </td>
                <td width="50%" valign="top" align="left">

                    <select class="wpresidence-2025-select" name="review_stars">
                        '.$starts_select.'
                    </select>
                </td>
            </tr>

            </table>';
    }
endif;

/**
 * Saves the custom data for the review post type
 *
 * This function hooks into the 'save_post' action to save custom metadata
 * for the 'estate_review' post type when a review is created or updated.
 * It specifically saves the review stars rating.
 *
 * @param int $post_id The ID of the post being saved
 * @param WP_Post $post The post object being saved
 */
add_action('save_post', 'wpestate_handle_reviews_custom_data_save', 1, 2);
/*
 * This function is triggered when a post is saved.
 * It checks if the post type is 'estate_review' and if the nonce is valid.
 * If so, it updates the 'review_stars' meta field with the value from the form.
 */
if(!function_exists('wpestate_handle_reviews_custom_data_save')):
function wpestate_handle_reviews_custom_data_save( $post_id, $post ) {

    // Validate that we have a proper post object
    if(!is_object($post) || !isset($post->post_type)) {
        return;
    }

    // Only process estate_review post type
    if($post->post_type!='estate_review'){
        return;
    }

    if( ! isset( $_POST['extend_comment_update'] ) || ! wp_verify_nonce( $_POST['extend_comment_update'], 'extend_comment_update' ) ){
        return;
    }

    if ( ( isset( $_POST['review_stars'] ) ) && ( $_POST['review_stars'] != '') ) {
	    update_post_meta( $post_id, 'review_stars',  intval($_POST['review_stars']) );
    }
    if ( ( isset( $_POST['attached_to'] ) ) && ( $_POST['attached_to'] != '') ) {
	    update_post_meta( $post_id, 'attached_to',  $_POST['attached_to'] );
    }
    if ( ( isset( $_POST['where_to_display'] ) ) && ( $_POST['where_to_display'] != '') ) {
	    update_post_meta( $post_id, 'where_to_display',  $_POST['where_to_display'] );
    }

}
endif;