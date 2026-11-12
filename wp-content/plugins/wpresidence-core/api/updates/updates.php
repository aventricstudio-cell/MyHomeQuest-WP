<?php 

/**
 * WpResidence Updates function
 * This file handles functions used in data updates betwen versions
 */

   
/**
 * Hook the migration to plugin update
 */
function wpresidence_check_version_and_migrate() {
    $current_version = get_option('wpresidence_version');

    if ($current_version && version_compare($current_version, '5.1.0', '<')) {
        wpresidence_migrate_user_roles_to_510();
    }

    if ($current_version && version_compare($current_version, '5.2.0', '<')) {
        wpresidence_migrate_saved_user_roles_to_520();
    }
    // Update version after migration
    update_option('wpresidence_version', '5.1.0');
}

add_action('admin_init', 'wpresidence_migrate_user_roles_to_510',9999);
add_action('admin_init', 'wpresidence_migrate_saved_user_roles_to_520',9999);



/** 
 * One-time migration for saved data regarding user roles
 * Run during plugin update to version 5.2.0
 */
function wpresidence_migrate_saved_user_roles_to_520()  {
    // Verify admin privileges
    if (!current_user_can('manage_options')) {
        return;
    }



    $user_types = function_exists('wpresidence_rolemap') ? wpresidence_rolemap() : array();
    $flipped_user_types = array_flip($user_types);
    $permited_roles = wpresidence_get_option('wp_estate_visible_user_role', '');
    $admin_submission_roles = wpresidence_get_option('wp_estate_admin_submission_user_role', '');

    
    if (!is_array($permited_roles)) {
        $permited_roles = array();
    }

    // Update Theme options 
    if ( !empty( $permited_roles ) )    {
        $new_permited_roles = array();
        foreach ($permited_roles as $role) {
            if (isset($flipped_user_types[$role])) {
                $new_permited_roles[] = $flipped_user_types[$role];
            }
        }
        $permited_roles = $new_permited_roles;

        if ( !empty($permited_roles) && is_array($permited_roles) ) {
            $wpresidenceOptions = wpresidence_get_admin_options();
            if (isset($wpresidenceOptions['wp_estate_visible_user_role'])) {
                $wpresidenceOptions['wp_estate_visible_user_role'] = $permited_roles;
                update_option('wpresidence_admin', $wpresidenceOptions);
            }
        }
       
    }

    // Update admin submission roles
    if ( !empty( $admin_submission_roles ) ) {
        $new_admin_submission_roles = array();
        foreach ($admin_submission_roles as $role) {
            if (isset($flipped_user_types[$role])) {
                $new_admin_submission_roles[] = $flipped_user_types[$role];
            }
        }
        if ( !empty( $new_admin_submission_roles ) ) {
            $wpresidenceOptions = wpresidence_get_admin_options();
            if (isset($wpresidenceOptions['wp_estate_admin_submission_user_role'])) {
                $wpresidenceOptions['wp_estate_admin_submission_user_role'] = $new_admin_submission_roles;
                update_option('wpresidence_admin', $wpresidenceOptions);
            }
        }
    }

    // Update user roles data on membership package post type
    $args = array(
        'post_type' => 'membership_package',
        'posts_per_page' => -1,
        'post_status' => 'any',
        'fields' => 'ids',
    );
    $packages = get_posts($args);

    

    foreach ($packages as $package_id) {
        $user_roles = get_post_meta($package_id, 'pack_visible_user_role', true);
        if (is_array($user_roles)) {
            $new_user_roles = array();
            foreach ($user_roles as $role) {
                if (isset($flipped_user_types[$role])) {
                    $new_user_roles[] = $flipped_user_types[$role];
                }
            }
            if ( !empty( $new_user_roles ) )    {
                update_post_meta($package_id, 'pack_visible_user_role', $new_user_roles);
            }
        }
    }


    // Mark migration as complete
    update_option('wpresidence_migrate_saved_user_roles_to_520', true, false);
}
 /**
 * One-time migration for user roles based on user_type meta
 * Run during plugin update to version 3.13
 * 
 * @return void
 */
function wpresidence_migrate_user_roles_to_510() {


    // Verify admin privileges
    if (!current_user_can('manage_options')) {
     
        return;
    }
    // Check if migration already ran
    if (get_option('wpresidence_migrate_user_roles_to_510')) {
     
        return;
    }
     // Create roles if needed
    if (!get_role(WPRESIDENCE_ROLE_AGENT) || !get_role(WPRESIDENCE_ROLE_AGENCY)  || !get_role(WPRESIDENCE_ROLE_DEVELOPER)  ) {
        wpresidence_create_custom_roles();
    }
   
    // Get all users with 'user_type' meta
    $users = get_users(array(
        'meta_key' => 'user_estate_role',
        'compare' => 'EXISTS',
        'fields' => array('ID'),
        'role__not_in' => array('administrator', 'editor', 'author', 'contributor'),
    ));

    foreach ($users as $user) {
      
        $user_type = intval(get_user_meta($user->ID, 'user_estate_role', true));
        $user_obj = new WP_User($user->ID);
        $user_roles = [
            1 => 'subscriber',
            2 => defined('WPRESIDENCE_ROLE_AGENT') ? WPRESIDENCE_ROLE_AGENT : 'wpresidence_agent_role',
            3 => defined('WPRESIDENCE_ROLE_AGENCY') ? WPRESIDENCE_ROLE_AGENCY : 'wpresidence_agency_role',
            4 => defined('WPRESIDENCE_ROLE_DEVELOPER') ? WPRESIDENCE_ROLE_DEVELOPER : 'wpresidence_developer_role'
        ];


        // Determine role based on user_type
        $new_role = isset($user_roles[$user_type]) ? $user_roles[$user_type] : 'subscriber';
          
        // Store existing roles except administrator
        $existing_roles = array_diff($user_obj->roles, array('administrator', 'editor', 'author', 'contributor'));

        // Add new role without removing existing ones
        $user_obj->add_role($new_role);


    }
    
    // Mark migration as complete
    update_option('wpresidence_migrate_user_roles_to_510', true, false);
}



/**
 * Hook the migration to property template from page temaplte to post type wpestate-studio
 */
function wpresidence_check_version_and_migrate_520() {
    $current_version = get_option('wpresidence_version');
   
    if ($current_version && version_compare($current_version, '5.2.0', '<=')) {
        wpresidence_convert_property_pages_to_studio();
        wpresidence_convert_comments_to_estate_review();
    }

    // Update version after migration
    update_option('wpresidence_version', '5.2.0');
}
add_action('admin_init', 'wpresidence_check_version_and_migrate_520', 9999);




/**
 * One-time migration: Convert specific page templates to wpestate-studio
 */
function wpresidence_convert_property_pages_to_studio() {


    // Only allow admin to run this
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Prevent re-running
    if ( get_option( 'wpresidence_convert_property_pages_to_studio_done' ) ) {
        return;
    }

    $args = array(
        'post_type'      => 'page',
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'page-templates/page_property_design.php',
        'posts_per_page' => -1,
        'no_found_rows'  => true,
        'fields'         => 'ids',
    );

    $page_ids = get_posts( $args );

    if ( empty( $page_ids ) ) {

    } else {
        foreach ( $page_ids as $page_id ) {
            set_post_type( $page_id, 'wpestate-studio' );
            update_post_meta( $page_id, 'wpestate_head_foot_template', 'wpestate_single_property_page' );
          
        }
    }



    // Handle the wpestate_wide_elememtor_page option
    $wide_elementor_page_id = wpresidence_get_option('wp_estate_global_property_page_template', 0);
    if ( $wide_elementor_page_id && $wide_elementor_page_id > 0 ) {
        // Set meta for the wide elementor page
        update_post_meta( $wide_elementor_page_id, 'wpestate_head_foot_template', 'wpestate_single_property_page' );
        update_post_meta( $wide_elementor_page_id, 'wpestate_head_foot_positions', 'estate_property' );
        update_post_meta( $wide_elementor_page_id, 'wpestate_custom_full_width', wpresidence_get_option('wpestate_wide_elememtor_page') );
        

    }

    update_option( 'wpresidence_convert_property_pages_to_studio_done', true, false );
}



/**
 * Converts existing WordPress comments to custom 'estate_review' post type
 * 
 * This function migrates all comments in the database to a custom post type called 'estate_review'.
 * It preserves comment metadata like review title, content, stars rating, and approval status.
 * The function includes safety checks to prevent unauthorized access and duplicate executions.
 * After conversion, original comments are deleted and URL rewrite rules are updated.
 * 
 * @return void
 */
function wpresidence_convert_comments_to_estate_review() {
    // Only allow admin to run this
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    // Prevent re-running
    if ( get_option( 'wpresidence_convert_comments_to_estate_review_done' ) ) {
        return;
    }
    
    // Get all comments with type 'review'
    $args = array(
        // 'status' => 'approve',
        'fields' => 'ids', // Only retrieve comment IDs for performance
    );
    $comments = get_comments( $args );
    
    // Check if any comments exist to process
    if ( empty( $comments ) ) {
        // No comments found, nothing to convert
        return;
    }
    
    // Loop through each comment and convert it to estate_review post type
    foreach ( $comments as $comment_id ) {
        // Convert comment to estate_review post type
       
        // Retrieve the full comment object
        $comment = get_comment( $comment_id );
        
        // Extract comment metadata for the review
        $review_title = get_comment_meta( $comment_id, 'review_title', true );
        $review_content = $comment->comment_content;
        $review_stars = get_comment_meta( $comment_id, 'review_stars', true );
        $attached_to = $comment->comment_post_ID; // The post this comment was attached to
        $approved = $comment->comment_approved;
        

         // Skip this comment if it doesn't have review metadata (not a review comment)
        if ( empty( $review_title ) && empty( $review_stars ) ) {
            continue;
        }

        
        // Convert comment approval status to post status
        $status = $approved === '1' ? 'publish' : 'draft';
        
        // Use original comment author ID, fallback to admin (ID 1) if not set
        $userID = !empty($comment->user_id) ? $comment->user_id : 1;
        
        // Prepare arguments for creating the new estate_review post
        $review_args = array(
            'post_title'   => $review_title,
            'post_content' => $review_content,
            'post_status'  => $status,
            'post_type'    => 'estate_review',
            'post_author'  => get_current_user_id(), // Current user creating the post
            'meta_input'   => array(
                'review_author'     => $userID,        // Original comment author
                'reviewer_rating'   => $review_stars,  // Star rating from comment meta
                'attached_to'       => $attached_to,   // Original post ID
            ),
        );
        
        // Create the new estate_review post
        $post_id = wp_insert_post( $review_args );
        
        // Handle post creation result
        if ( is_wp_error( $post_id ) ) {
            // Handle error if needed
            continue; // Skip to next comment if creation failed
        } else {
            // Delete the original comment after successful conversion
            wp_delete_comment( $comment_id, true ); // Delete the comment after conversion
        }
    }
    
    // Update rewrite_urls_option
    $rewrite_urls_option = get_option('wp_estate_url_rewrites', array());
    
    // Ensure the option is an array
    if ( !is_array($rewrite_urls_option) ) {
        $rewrite_urls_option = array();
    }
    
    // Add URL rewrite rules for the new post type
    $rewrite_urls_option[26] = 'review';          // Single review page slug
    $rewrite_urls_option[27] = 'review_category'; // Review category archive slug
    
    // Save the updated URL rewrite options
    update_option('wp_estate_url_rewrites', $rewrite_urls_option);
    
    // Mark this conversion as completed to prevent re-running
    update_option( 'wpresidence_convert_comments_to_estate_review_done', true, false );
}