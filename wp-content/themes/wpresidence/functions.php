<?php
/**
 * WPResidencefunctions and definitions
 *
 * This file contains functions and definitions for the WPResidence theme.
 * It includes admin notices, license management, and various theme-specific settings.
 *
 * @package WPResidence
 * @subpackage AdminCustomization
 * @since 1.0.0
 */
add_action( 'plugins_loaded', 'wpresidence_check_core_plugin_dependency', 1 );

function wpresidence_check_core_plugin_dependency() {
    add_action( 'wp_loaded', function () {
        $theme = wp_get_theme();
        $theme_version = $theme->get( 'Version' );


        if ( version_compare( $theme_version, '5.1.0', '>=' ) && !function_exists( 'wpresidence_core_510' ) ) {

            // Block frontend
            if ( !is_admin() ) {
                add_action('wp_head', function() {
                    echo '<div style="background-color: #f8d7da; color: #721c24; padding: 10px; text-align: center; position: fixed; top: 0; left: 0; right: 0; z-index: 9999;">';
                    echo 'The site requires WPResidence Core plugin version 5.1.0 or greater. Some features may not work correctly. ';
                    
                    if (current_user_can('administrator')) {
                        echo '<a href="' . admin_url('plugins.php') . '">Update plugins</a>';
                    }
                    
                    echo '</div>';
                });
            }

            // Show admin notice
            add_action( 'admin_notices', function () {
                echo '<div class="notice notice-error"><p><strong>WPResidence Core plugin must be version 5.1.0 or greater. Please update the plugin to match the theme version.</strong></p></div>';
            });
        }
        
         if ( version_compare( $theme_version, '5.1.0', '>=' ) && !function_exists( 'wpresidence_elementor_510' ) ) {

            // Block frontend
            if ( !is_admin() ) {
                add_action('wp_head', function() {
                    echo '<div style="background-color: #f8d7da; color: #721c24; padding: 10px; text-align: center; position: fixed; top: 0; left: 0; right: 0; z-index: 9999;">';
                    echo 'The site requires WPResidence Core plugin version 5.1.0 or greater. Some features may not work correctly. ';
                    
                    if (current_user_can('administrator')) {
                        echo '<a href="' . admin_url('plugins.php') . '">Update plugins</a>';
                    }
                    
                    echo '</div>';
                });
            }

            // Show admin notice
            add_action( 'admin_notices', function () {
                echo '<div class="notice notice-error"><p><strong>WPResidence Elementor Widgets Plugin plugin must be version 5.1.0 or greater. Please update the plugin to match the theme version.</strong></p></div>';
            });
        }
    });
}


function wpresidence_early_plugin_check() {
    // Check if the core plugin is active but outdated
    if (defined('WPESTATE_PLUGIN_URL') && !function_exists('wpresidence_core_510')) {
     
        if ( !is_admin() ) {
               add_action('wp_head', function() {
                    echo '<div style="background-color: #f8d7da; color: #721c24; padding: 10px; text-align: center; position: fixed; top: 0; left: 0; right: 0; z-index: 9999;">';
                    echo 'The site requires WPResidence Core plugin version 5.1.0 or greater. Some features may not work correctly. ';
                    
                    if (current_user_can('administrator')) {
                        echo '<a href="' . admin_url('plugins.php') . '">Update plugins</a>';
                    }
                    
                    echo '</div>';
                });
        }else{
            // Show admin notice
            add_action( 'admin_notices', function () {
                echo '<div class="notice notice-error"><p><strong>WPResidence Elementor Widgets Plugin plugin must be version 5.1.0 or greater. Please update the plugin to match the theme version.</strong></p></div>';
            });
        }
    }
    
    // Check if Elementor plugin is active but outdated
    if (defined('WPESTATE_ELEMENTOR_URL') && !function_exists('wpresidence_elementor_510')) {
       
        if ( !is_admin() ) {
            
               add_action('wp_head', function() {
                      echo '<div style="background-color: #f8d7da; color: #721c24; padding: 10px; text-align: center; position: fixed; top: 0; left: 0; right: 0; z-index: 9999;">';
                    echo 'Please update the WPResidence Elementor Widgets plugin from WP-Admin → Appearance → Install Plugins';
                    
                    if (current_user_can('administrator')) {
                        echo '<a href="' . admin_url('plugins.php') . '">Update plugins</a>';
                    }
                    
                    echo '</div>';
                });
     
        }else{
            // Show admin notice
            add_action( 'admin_notices', function () {
                echo '<div class="notice notice-error"><p><strong>WPResidence Elementor Widgets Plugin plugin must be version 5.1.0 or greater. Please update the plugin to match the theme version.</strong></p></div>';
            });
        }
    }
}

// Run the check as early as possible
add_action('after_setup_theme', 'wpresidence_early_plugin_check', 1);




 /**
 * Get theme option with improved performance
 * 
 * Retrieves theme option values using lazy-loading cache
 * to reduce database queries while maintaining compatibility
 * with existing code.
 * 
 * @param string $theme_option  The option name to retrieve
 * @param mixed  $option        Optional sub-option key
 * @param mixed  $in_case_not   Default value if option not found
 * @return mixed                The option value
 */
if (!function_exists('wpresidence_get_option')):
    function wpresidence_get_option($theme_option, $option = false, $in_case_not = false) {
        // Get options using lazy-loading singleton
        $wpresidence_admin = wpresidence_get_admin_options();
        $theme_option = trim($theme_option);
        
        $post_id='';
        global $post;
        if(isset($post->ID)){
            $post_id=$post->ID;
        }



        // Handle special cases that need custom processing

        if($theme_option=='wp_estate_search_fields_no_per_row'){
            if (wpestate_half_map_conditions($post_id) &&  $wpresidence_admin['wp_estate_half_map_search_version'] ) {
                  return $wpresidence_admin['wp_estate_half_map_search_fields_no']; ;
            }

        } else if ($theme_option == 'wpestate_currency' || $theme_option == 'wp_estate_multi_curr') {
            // Currency requires special conversion
            $return = wpestate_reverse_convert_redux_wp_estate_multi_curr();
            return $return;
        } else if ($theme_option == 'wpestate_custom_fields_list' || $theme_option == 'wp_estate_custom_fields') {

            if(function_exists('wpestate_reverse_convert_redux_wp_estate_custom_fields')){
                // Custom fields require special conversion
                $return = wpestate_reverse_convert_redux_wp_estate_custom_fields();
                return $return;
            }
        } else if ($theme_option == 'wp_estate_url_rewrites') {
            // URL rewrites stored in separate option
            $return = get_option('wp_estate_url_rewrites', true);
            return $return;
        } else if ($theme_option == 'wp_estate_adv_search_what' || $theme_option == 'wp_estate_adv_search_label' || $theme_option == 'wp_estate_adv_search_how') {
            // Advanced search parameters need custom handling
            $custom_advanced_search = isset($wpresidence_admin['wp_estate_custom_advanced_search']) 
                ? $wpresidence_admin['wp_estate_custom_advanced_search'] 
                : null;
            if(function_exists('wpestate_return_search_parameters')){
                return wpestate_return_search_parameters($wpresidence_admin, $theme_option, $custom_advanced_search);
            }
        }
          
        
        // Standard option retrieval
        if (isset($wpresidence_admin[$theme_option]) && $wpresidence_admin[$theme_option] != '') {
            $return = $wpresidence_admin[$theme_option];
            
            // Handle nested options if requested
            if ($option && isset( $wpresidence_admin[$theme_option][$option])) {
                $return = $wpresidence_admin[$theme_option][$option];
            }
        } else {
            // Return default if option not found
            $return = $in_case_not;
        }
        
        return $return;
    }
endif;

/**
 * Lazy-loading singleton pattern for Redux options
 * 
 * This function retrieves theme options only when first needed
 * and caches them in memory for subsequent calls.
 * 
 * @return array The WPResidence theme options
 */

if(!function_exists('wpresidence_get_admin_options')): 
    function wpresidence_get_admin_options() {
        // Static variable persists between function calls
        // Will only query database on first call
        static $wpresidence_admin = null;
        
        // Load options from database only once
        if ($wpresidence_admin === null) {
            $wpresidence_admin = get_option('wpresidence_admin', array());
        }
        
        return $wpresidence_admin;
    }
endif;





// Include necessary files
require_once get_theme_file_path('/libs/index.php');

if (function_exists('wpestate_crm_top_level_menu')) {
    require_once get_theme_file_path('crm_functions/crm_functions.php');
}

// Define constants
define('ULTIMATE_NO_EDIT_PAGE_NOTICE', true);
define('ULTIMATE_NO_PLUGIN_PAGE_NOTICE', true);
define('BSF_6892199_CHECK_UPDATES', false);
define('BSF_6892199_NAG', false);


add_filter('is_rtl', '__return_true');


/**
 * Display the license deregistration form
 */
function wpestate_license_custom_menu_page() {
    if (class_exists('WpestateFunk')) {
        $WpestateFunk = WpestateFunk::get_instance();
        $WpestateFunk->show_deregister_license_form();
    }
}

// starting wordpress 6.7
add_action('after_setup_theme', function() {
    $domain = 'wpresidence';
    
    // Primary method - load from wp-content/languages/themes
    load_theme_textdomain($domain, WP_LANG_DIR . '/themes');
    
    // Fallback - load from theme directory during development
    $locale = get_locale();
    $mofile = get_template_directory() . "/languages/{$locale}.mo";
    
    if (file_exists($mofile)) {
        load_textdomain($domain, $mofile);
    }
   
});





/**
 * Display admin notices and handle various checks
 */
function wpestate_admin_notice() {
    global $pagenow, $typenow;

    // Display license form if WpestateFunk class exists
    if (class_exists('WpestateFunk')) {
        $WpestateFunk = WpestateFunk::get_instance();
        $WpestateFunk->show_license_form();
    }

    // Exit if on themes.php page
    if ($pagenow == 'themes.php') {
        return;
    }

    // Set $typenow for specific post types
    if (!empty($_GET['post'])) {
        $post = get_post(intval($_GET['post']));
        $typenow = $post->post_type;
    }

    $wpestate_notices = get_option('wp_estate_notices');

    // Display cache notice
    wpestate_display_notice('wp_estate_cache_notice', 'updated', 
        __('For better speed results, the theme offers a built-in caching system for properties and categories. Check this article on how to enable/disable theme cache: ', 'wpresidence') .
        '<a href="http://help.wpresidence.net/article/enable-or-disable-wp-residence-cache/">' . __('help article', 'wpresidence') . '</a>'
    );

    // Display Google Maps API key notice
    if (esc_html(wpresidence_get_option('wp_estate_api_key')) == '') {
        wpestate_display_notice('wp_estate_api_key', 'error',
            __('The Google Maps JavaScript API v3 REQUIRES an API key to function correctly. Get an APIs Console key and post the code in Theme Options. You can get it from', 'wpresidence') .
            ' <a href="https://developers.google.com/maps/documentation/javascript/tutorial#api_key" target="_blank">' . __('here', 'wpresidence') . '</a>'
        );
    }

    // Display memory limit notice
    if (intval(WP_MEMORY_LIMIT) < 96) {
        wpestate_display_notice('wp_estate_memory_notice', 'error',
            __('WordPress Memory Limit is set to ', 'wpresidence') . WP_MEMORY_LIMIT . '. ' .
            __('Recommended memory limit should be at least 96MB. Please refer to: ', 'wpresidence') .
            '<a href="http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' .
            __('Increasing memory allocated to PHP', 'wpresidence') . '</a>'
        );
    }

    // Display PHP version notice
    if (!defined('PHP_VERSION_ID')) {
        $version = explode('.', PHP_VERSION);
        define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
    }

    if (PHP_VERSION_ID < 50600) {
        $version = explode('.', PHP_VERSION);
        wpestate_display_notice('wp_estate_php_version', 'error',
            __('Your PHP version is ', 'wpresidence') . $version[0] . '.' . $version[1] . '.' . $version[2] . '. ' .
            __('We recommend upgrading the PHP version to at least 5.6.1. The upgrade should be done on your server by your hosting company.', 'wpresidence')
        );
    }

    // Display GD library notice
    if (!extension_loaded('gd') && !function_exists('gd_info')) {
        wpestate_display_notice('wp_estate_gd_info', 'error',
            __('PHP GD library is NOT installed on your web server and because of that the theme will not be able to work with images. Please contact your hosting company in order to activate this library.', 'wpresidence')
        );
    }

    // Display MbString extension notice
    if (!extension_loaded('mbstring')) {
        wpestate_display_notice('wp_estate_mb_string', 'error',
            __('MbString extension not detected. Please contact your hosting provider in order to enable it.', 'wpresidence')
        );
    }

    // Display notice for property list half template
    $page_template = isset($post->ID) ? get_post_meta($post->ID, '_wp_page_template', true) : '';
    if (is_admin() && $pagenow == 'post.php' && $typenow == 'page' && $page_template == 'page-templates/property_list_half.php') {
        $header_type = get_post_meta($post->ID, 'header_type', true);
        if ($header_type != 5) {
            wpestate_display_notice('wp_estate_header_half', 'error',
                __('Half Map Template - make sure your page has the "Hero Media Header" set as maps!', 'wpresidence')
            );
        }
    }

    // Display notice for property term slugs
    if (is_admin() && $pagenow == 'edit-tags.php' && $typenow == 'estate_property') {
        wpestate_display_notice('wp_estate_prop_slugs', 'error',
            __('Please do not manually change the slugs when adding new terms. If you need to edit a term name copy the new name in the slug field also.', 'wpresidence')
        );
    }

    // Output nonce for AJAX
    $ajax_nonce = wp_create_nonce("wpestate_notice_nonce");
    echo '<input type="hidden" id="wpestate_notice_nonce" value="' . esc_attr($ajax_nonce) . '"/>';
}





/**
 * Helper function to display admin notices
 *
 * @param string $notice_type The type of notice
 * @param string $class The CSS class for the notice
 * @param string $message The message to display
 */
function wpestate_display_notice($notice_type, $class, $message) {
    $wpestate_notices = get_option('wp_estate_notices');
    if (!is_array($wpestate_notices) || !isset($wpestate_notices[$notice_type]) || 
        (isset($wpestate_notices[$notice_type]) && $wpestate_notices[$notice_type] != 'yes')) {
        echo '<div data-notice-type="' . esc_attr($notice_type) . '" class="wpestate_notices ' . esc_attr($class) . ' settings-error notice is-dismissible">';
        echo '<p>' . wp_kses_post($message) . '</p>';
        echo '</div>';
    }
}

// Hook the admin notice function
add_action('admin_notices', 'wpestate_admin_notice');



// Hook the initialization function
add_action('after_setup_theme', 'wp_estate_init');

if (!function_exists('wp_estate_init')) :
    /**
     * Initialize the WPResidence theme
     */
    function wp_estate_init() {

        static $init_call_count = 0;
        $init_call_count++;
        
        // Log the call with backtrace
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $caller = isset($backtrace[1]) ? $backtrace[1]['function'] : 'unknown';
        
        global $content_width;
        // Set content width if not already set
        if (!isset($content_width)) {
            $content_width = 1200;
        }
        
        // Load theme textdomain for translations
        load_theme_textdomain('wpresidence', get_template_directory() . '/languages');
        
        // Set up theme supports
        set_post_thumbnail_size(940, 198, true);
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('automatic-feed-links');
        add_theme_support('custom-background');
        add_theme_support('align-wide');
        add_theme_support('rtl');
        add_theme_support('gutenberg', array('wide-images' => true));
        
        // Add Twitter oEmbed provider
        wp_oembed_add_provider(
            '#https?://twitter.com/\#!/[a-z0-9_]{1,20}/status/\d+#i',
            'https://api.twitter.com/1/statuses/oembed.json',
            true
        );
        
        // Set up image sizes
        wpestate_image_size();
        
        // Add filters and actions - This is where the scripts are hooked
        add_filter('excerpt_length', 'wp_estate_excerpt_length');
        add_filter('excerpt_more', 'wpestate_new_excerpt_more');
        add_action('tgmpa_register', 'wpestate_required_plugins');
        add_action('wp_enqueue_scripts', 'wpestate_scripts');
        add_action('wp_enqueue_scripts', 'wpestate_register_review_scripts');
        add_action('admin_enqueue_scripts', 'wpestate_admin');
        add_action('login_enqueue_scripts', 'wpestate_login_logo_function');
        
        // Set default link type for images
        update_option('image_default_link_type', 'file');
        
        // Run theme update function
        wpestate_theme_update();
    }
endif;






if ( ! function_exists( 'wpestate_theme_update' ) ) :
    /**
     * Handle theme updates
     *
     * This function performs necessary updates when the theme is activated or updated.
     */
    function wpestate_theme_update() {
        if ( null === get_option( 'wp_estate_submission_page_fields', null ) ) {
            $all_submission_fields = wpestate_return_all_fields();
            $default_val = array_keys( $all_submission_fields );
            add_option( 'wp_estate_submission_page_fields', $default_val );
        }
    }
endif;








/**
 * WPResidence Navigation  functions for displaying post navigation and
 * handling comments in the WPResidence theme.
 *
 * @package WPResidence
 * @subpackage Template
 * @since 1.0.0
 */

if ( ! function_exists( 'wp_estate_content_nav' ) ) :
    /**
     * Display navigation to next/previous set of posts when applicable.
     *
     * @param string $html_id The ID for the HTML to create.
     */
    function wp_estate_content_nav( $html_id ) {
        global $wp_query;

        if ( $wp_query->max_num_pages > 1 ) :
            ?>
            <nav id="<?php echo esc_attr( $html_id ); ?>">
                <h3 class="assistive-text"><?php esc_html_e( 'Post navigation', 'wpresidence' ); ?></h3>
                <div class="nav-previous"><?php next_posts_link( esc_html__( '<span class="meta-nav">&larr;</span> Older posts', 'wpresidence' ) ); ?></div>
                <div class="nav-next"><?php previous_posts_link( esc_html__( 'Newer posts <span class="meta-nav">&rarr;</span>', 'wpresidence' ) ); ?></div>
            </nav>
            <?php
        endif;
    }
endif;





if ( ! function_exists( 'wpestate_comment' ) ) :
    /**
     * Template for comments and pingbacks.
     *
     * @param WP_Comment $comment The comment object.
     * @param array      $args    An array of arguments.
     * @param int        $depth   The depth of the comment.
     */
    function wpestate_comment( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;

        switch ( $comment->comment_type ) :
            case 'pingback':
            case 'trackback':
                ?>
                <li class="post pingback">
                    <p>
                        <?php 
                        esc_html_e( 'Pingback:', 'wpresidence' ); 
                        comment_author_link(); 
                        edit_comment_link( esc_html__( 'Edit', 'wpresidence' ), '<span class="edit-link">', '</span>' ); 
                        ?>
                    </p>
                <?php
                break;
            default:
                ?>
                <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
                    <?php
                    $avatar = wpestate_get_avatar_url( get_avatar( $comment, 55 ) );
                    ?>
                    <div class="blog_author_image singlepage" style="background-image: url(<?php echo esc_url( $avatar ); ?>);">
                        <?php 
                        comment_reply_link( array_merge( $args, array(
                            'reply_text' => esc_html__( 'Reply', 'wpresidence' ),
                            'depth'      => $depth,
                            'max_depth'  => $args['max_depth']
                        ) ) ); 
                        ?>
                    </div>
                    <div id="comment-<?php comment_ID(); ?>" class="comment">
                        <?php edit_comment_link( esc_html__( 'Edit', 'wpresidence' ), '<span class="edit-link">', '</span>' ); ?>
                        <div class="comment-meta">
                            <div class="comment-author vcard">
                                <div class="comment_name"><?php comment_author_link(); ?></div>
                                <span class="comment_date"><?php echo esc_html__( ' on ', 'wpresidence' ) . get_comment_date(); ?></span>
                            </div>
                            <?php if ( '0' == $comment->comment_approved ) : ?>
                                <em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'wpresidence' ); ?></em>
                            <?php endif; ?>
                        </div>
                        <div class="comment-content"><?php comment_text(); ?></div>
                    </div>
                <?php
                break;
        endswitch;
    }
endif;



if( !current_user_can('activate_plugins') ) {
    function wpestate_admin_bar_render() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('edit-profile', 'user-actions');
    }

    add_action( 'wp_before_admin_bar_render', 'wpestate_admin_bar_render' );

    add_action( 'admin_init', 'wpestate_stop_access_profile' );
    if( !function_exists('wpestate_stop_access_profile') ):
    function wpestate_stop_access_profile() {
        global $pagenow;

        if( defined('IS_PROFILE_PAGE') && IS_PROFILE_PAGE === true ) {
            wp_die( esc_html__('Please edit your profile page from site interface.','wpresidence') );
        }

        if($pagenow=='user-edit.php'){
            wp_die( esc_html__('Please edit your profile page from site interface.','wpresidence') );
        }
    }
    endif; // end   wpestate_stop_access_profile

}// end user can activate_plugins



///////////////////////////////////////////////////////////////////////////////////////////
// prevent changing the author id when admin hit publish
///////////////////////////////////////////////////////////////////////////////////////////

add_action( 'transition_post_status', 'wpestate_correct_post_data',10,3 );

if( !function_exists('wpestate_correct_post_data') ):

function wpestate_correct_post_data( $strNewStatus,$strOldStatus,$post) {
    /* Only pay attention to posts (i.e. ignore links, attachments, etc. ) */
    if( $post->post_type !== 'estate_property' )
        return;

    if( $strOldStatus === 'new' ) {
        update_post_meta( $post->ID, 'original_author', $post->post_author );
    }


    /* If this post is being published, try to restore the original author */
      if( $strNewStatus === 'publish' ) {

            $originalAuthor_id =$post->post_author;
            $user = get_user_by('id',$originalAuthor_id);

            if( isset($user->user_email) ){
                $user_email=$user->user_email;
                if( $user->roles[0]=='subscriber'){
                    $arguments=array(
                        'post_id'           =>  $post->ID,
                        'property_url'      =>   esc_url ( get_permalink($post->ID) ),
                        'property_title'    =>  get_the_title($post->ID)
                    );
                    wpestate_select_email_type($user_email,'approved_listing',$arguments);
                }
            }

    }
}
endif; // end   wpestate_correct_post_data

add_action( 'transition_post_status', 'wpestate_send_agent_notification',10,3 );

if( !function_exists('wpestate_send_agent_notification') ):
/**
 * Send notification to agent when a agent is approved
 *
 * This function checks if the post type is 'estate_property' and sends a notification
 * to the agent when the property status changes from 'new' to 'publish'.
 *
 * @param string $strNewStatus The new status of the post.
 * @param string $strOldStatus The old status of the post.
 * @param WP_Post $post The post object.
 */
function wpestate_send_agent_notification( $strNewStatus,$strOldStatus,$post) {
    /* Only pay attention to approvable post types (i.e. ignore links, attachments, etc. ) */
    $permittered_user_roles = wpresidence_get_option('wp_estate_admin_submission_user_role', '');

    $permittered_post_types = array();
    
    if(function_exists('wpresidence_convert_user_roles_to_post_types')){
        $permittered_post_types = wpresidence_convert_user_roles_to_post_types( $permittered_user_roles );
    }

    // If the post type is not in the permitted list, exit
    if( !in_array($post->post_type, $permittered_post_types) )
        return;


    /* If this post is being published, try to restore the original author */
    if( $strNewStatus === 'publish' && $strOldStatus === 'pending' ) {

        $agentID = get_post_meta( $post->ID, 'user_meda_id', true );
        if( $agentID ) {
            $user = get_user_by('id', $agentID);
            if( isset($user->user_email) && !empty($user->user_email) ) {
                $user_email = $user->user_email;
                $tosend = false;
                foreach ($user->roles as $role) {
                    if( in_array($role, $permittered_user_roles) ) {
                        $tosend = true;
                        break;
                    }
                }
                if( $tosend ) {
                    $arguments = array(
                        'post_id'           =>  $post->ID,
                        'property_url'      =>   esc_url ( get_permalink($post->ID) ),
                        'property_title'    =>  get_the_title($post->ID)
                    );
                    wpestate_select_email_type($user_email, 'approved_user', $arguments);
                }
            }
        }

    }
}
endif; // end   wpestate_correct_post_data



///////////////////////////////////////////////////////////////////////////////////////////
// get attachment info
///////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('wp_get_attachment') ):
    function wp_get_attachment( $attachment_id ) {

            $attachment = get_post( $attachment_id );


            if($attachment){
                return array(
                        'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
                        'caption' => $attachment->post_excerpt,
                        'description' => $attachment->post_content,
                        'href' =>  esc_url ( get_permalink( $attachment->ID ) ),
                        'src' => $attachment->guid,
                        'title' => $attachment->post_title
                );
            }else{
                return array(
                        'alt' => '',
                        'caption' => '',
                        'description' => '',
                        'href' => '',
                        'src' => '',
                        'title' => ''
                );
            }
    }
endif;


add_action('get_header', 'wpestate_my_filter_head');

if( !function_exists('wpestate_my_filter_head') ):
    function wpestate_my_filter_head() {
      remove_action('wp_head', '_admin_bar_bump_cb');
    }
endif;



///////////////////////////////////////////////////////////////////////////////////////////
// forgot pass action
///////////////////////////////////////////////////////////////////////////////////////////




add_action('wpcf7_before_send_mail', 'wpcf7_update_email_body');
if( !function_exists('wpcf7_update_email_body') ):
function wpcf7_update_email_body($contact_form) {
    global $post;

    $submission =   WPCF7_Submission::get_instance();
    $url        =   $submission->get_meta( 'url' );
    $postid     =   url_to_postid( trim($url) );
    $post_type  =   get_post_type($postid);

    if( isset($postid) && $post_type == 'estate_property' ){

        $mail = $contact_form->prop('mail');
        $mail['recipient']  = wpestate_return_agent_email_listing($postid,$post_type);
        $mail['body'] .= esc_html__('Message sent from page: ','wpresidence'). esc_url ( get_permalink($postid) );
        $contact_form->set_properties(array('mail' => $mail));
    }

    if(isset($postid) && ( $post_type == 'estate_agent'||  $post_type == 'estate_agency' || $post_type == 'estate_developer' )){
        $mail = $contact_form->prop('mail');

        if( $post_type == 'estate_agency' ){
            $mail['recipient']  = esc_html( get_post_meta($postid, 'agency_email', true) );
        }else if(  $post_type == 'estate_developer'  ){
            $mail['recipient']  = esc_html( get_post_meta($postid, 'developer_email', true) );
        }else{
            $mail['recipient']  = esc_html( get_post_meta($postid, 'agent_email', true) );
        }

        $mail['body'] .= esc_html__('Message sent from page: ','wpresidence'). esc_url ( get_permalink($postid) );
        $contact_form->set_properties(array('mail' => $mail));
    }

}
endif;


if(!function_exists('wpestate_return_agent_email_listing')){
    function wpestate_return_agent_email_listing($postid,$post_type){

        $agent_id   = intval( get_post_meta($postid, 'property_agent', true) );
        $role_type  = get_post_type($agent_id);


        if( $role_type == 'estate_agency' ){
            $agent_email  = esc_html( get_post_meta($agent_id, 'agency_email', true) );
        }else if(  $role_type == 'estate_developer'  ){
            $agent_email  = esc_html( get_post_meta($agent_id, 'developer_email', true) );
        }else{
            if ($agent_id!=0){
                $agent_email = esc_html( get_post_meta($agent_id, 'agent_email', true) );
            }else{
                $author_id           =  wpsestate_get_author($postid);
                $agent_email         =  get_the_author_meta( 'user_email',$author_id  );
            }
        }

        return $agent_email;
    }
}





if ( !function_exists('wpestate_get_pin_file_path')):

    function wpestate_get_pin_file_path(){
        if (function_exists('icl_translate') ) {
            $path=get_template_directory().'/pins-'.apply_filters( 'wpml_current_language', 'en' ).'.txt';
        }else{
            $path=get_template_directory().'/pins.txt';
        }

        return $path;
    }

endif;





add_filter( 'redirect_canonical','wpestate_disable_redirect_canonical',10,2 );
function wpestate_disable_redirect_canonical( $redirect_url ,$requested_url){
    if ( is_page_template('page-templates/property_list.php') || is_page_template( 'page-templates/property_list_directory.php') 
    || is_page_template('page-templates/property_list_half.php') ){
       $redirect_url = false;
    }
    return $redirect_url;
}



if(!function_exists('convertAccentsAndSpecialToNormal')):
function convertAccentsAndSpecialToNormal($string) {
    $table = array(
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Ă'=>'A', 'Ā'=>'A', 'Ą'=>'A', 'Æ'=>'A', 'Ǽ'=>'A',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'ă'=>'a', 'ā'=>'a', 'ą'=>'a', 'æ'=>'a', 'ǽ'=>'a',

        'Þ'=>'B', 'þ'=>'b', 'ß'=>'Ss',

        'Ç'=>'C', 'Č'=>'C', 'Ć'=>'C', 'Ĉ'=>'C', 'Ċ'=>'C',
        'ç'=>'c', 'č'=>'c', 'ć'=>'c', 'ĉ'=>'c', 'ċ'=>'c',

        'Đ'=>'Dj', 'Ď'=>'D', 'Đ'=>'D',
        'đ'=>'dj', 'ď'=>'d',

        'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ĕ'=>'E', 'Ē'=>'E', 'Ę'=>'E', 'Ė'=>'E',
        'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ĕ'=>'e', 'ē'=>'e', 'ę'=>'e', 'ė'=>'e',

        'Ĝ'=>'G', 'Ğ'=>'G', 'Ġ'=>'G', 'Ģ'=>'G',
        'ĝ'=>'g', 'ğ'=>'g', 'ġ'=>'g', 'ģ'=>'g',

        'Ĥ'=>'H', 'Ħ'=>'H',
        'ĥ'=>'h', 'ħ'=>'h',

        'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'İ'=>'I', 'Ĩ'=>'I', 'Ī'=>'I', 'Ĭ'=>'I', 'Į'=>'I',
        'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'į'=>'i', 'ĩ'=>'i', 'ī'=>'i', 'ĭ'=>'i', 'ı'=>'i',

        'Ĵ'=>'J',
        'ĵ'=>'j',

        'Ķ'=>'K',
        'ķ'=>'k', 'ĸ'=>'k',

        'Ĺ'=>'L', 'Ļ'=>'L', 'Ľ'=>'L', 'Ŀ'=>'L', 'Ł'=>'L',
        'ĺ'=>'l', 'ļ'=>'l', 'ľ'=>'l', 'ŀ'=>'l', 'ł'=>'l',

        'Ñ'=>'N', 'Ń'=>'N', 'Ň'=>'N', 'Ņ'=>'N', 'Ŋ'=>'N',
        'ñ'=>'n', 'ń'=>'n', 'ň'=>'n', 'ņ'=>'n', 'ŋ'=>'n', 'ŉ'=>'n',

        'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ō'=>'O', 'Ŏ'=>'O', 'Ő'=>'O', 'Œ'=>'O',
        'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ō'=>'o', 'ŏ'=>'o', 'ő'=>'o', 'œ'=>'o', 'ð'=>'o',

        'Ŕ'=>'R', 'Ř'=>'R',
        'ŕ'=>'r', 'ř'=>'r', 'ŗ'=>'r',

        'Š'=>'S', 'Ŝ'=>'S', 'Ś'=>'S', 'Ş'=>'S',
        'š'=>'s', 'ŝ'=>'s', 'ś'=>'s', 'ş'=>'s',

        'Ŧ'=>'T', 'Ţ'=>'T', 'Ť'=>'T',
        'ŧ'=>'t', 'ţ'=>'t', 'ť'=>'t',

        'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ũ'=>'U', 'Ū'=>'U', 'Ŭ'=>'U', 'Ů'=>'U', 'Ű'=>'U', 'Ų'=>'U',
        'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ũ'=>'u', 'ū'=>'u', 'ŭ'=>'u', 'ů'=>'u', 'ű'=>'u', 'ų'=>'u',

        'Ŵ'=>'W', 'Ẁ'=>'W', 'Ẃ'=>'W', 'Ẅ'=>'W',
        'ŵ'=>'w', 'ẁ'=>'w', 'ẃ'=>'w', 'ẅ'=>'w',

        'Ý'=>'Y', 'Ÿ'=>'Y', 'Ŷ'=>'Y',
        'ý'=>'y', 'ÿ'=>'y', 'ŷ'=>'y',

        'Ž'=>'Z', 'Ź'=>'Z', 'Ż'=>'Z', 'Ž'=>'Z',
        'ž'=>'z', 'ź'=>'z', 'ż'=>'z', 'ž'=>'z',

        '“'=>'"', '”'=>'"', '‘'=>"'", '’'=>"'", '•'=>'-', '…'=>'...', '—'=>'-', '–'=>'-', '¿'=>'?', '¡'=>'!', '°'=>' degrees ',
        '¼'=>' 1/4 ', '½'=>' 1/2 ', '¾'=>' 3/4 ', '⅓'=>' 1/3 ', '⅔'=>' 2/3 ', '⅛'=>' 1/8 ', '⅜'=>' 3/8 ', '⅝'=>' 5/8 ', '⅞'=>' 7/8 ',
        '÷'=>' divided by ', '×'=>' times ', '±'=>' plus-minus ', '√'=>' square root ', '∞'=>' infinity ',
        '≈'=>' almost equal to ', '≠'=>' not equal to ', '≡'=>' identical to ', '≤'=>' less than or equal to ', '≥'=>' greater than or equal to ',
        '←'=>' left ', '→'=>' right ', '↑'=>' up ', '↓'=>' down ', '↔'=>' left and right ', '↕'=>' up and down ',
        '℅'=>' care of ', '℮' => ' estimated ',
        'Ω'=>' ohm ',
        '♀'=>' female ', '♂'=>' male ',
        '©'=>' Copyright ', '®'=>' Registered ', '™' =>' Trademark ',
    );

    $string = strtr($string, $table);
    // Currency symbols: £¤¥€  - we dont bother with them for now
    $string = preg_replace("/[^\x9\xA\xD\x20-\x7F]/u", "", $string);

    return $string;
}
endif;



function estate_create_onetime_nonce($action = -1) {
    $time = time();
    $nonce = wp_create_nonce($time.$action);
    return $nonce . '-' . $time;
}



function estate_verify_onetime_nonce( $_nonce, $action = -1) {
    $parts  =   explode( '-', $_nonce );
    $nonce  =   $toadd_nonce    = $parts[0];
    $generated = $parts[1];

    $nonce_life = 60*60;
    $expires    = (int) $generated + $nonce_life;
    $time       = time();

    if( ! wp_verify_nonce( $nonce, $generated.$action ) || $time > $expires ){
        return false;
    }

    $used_nonces = get_option('_sh_used_nonces');

    if( isset( $used_nonces[$nonce] ) ) {
        return false;
    }

    if(is_array($used_nonces)){
        foreach ($used_nonces as $nonce=> $timestamp){
            if( $timestamp > $time ){
                break;
            }
            unset( $used_nonces[$nonce] );
        }
    }

    $used_nonces[$toadd_nonce] = $expires;
    asort( $used_nonces );
    update_option( '_sh_used_nonces',$used_nonces );
    return true;
}




function estate_verify_onetime_nonce_login( $_nonce, $action = -1) {
    $parts = explode( '-', $_nonce );
    $nonce =$toadd_nonce= $parts[0];
    $generated = $parts[1];

    $nonce_life = 60*60;
    $expires    = (int) $generated + $nonce_life;
    $expires2   = (int) $generated + 120;
    $time       = time();

    if( ! wp_verify_nonce( $nonce, $generated.$action ) || $time > $expires ){
        return false;
    }

    //Get used nonces
    $used_nonces = get_option('_sh_used_nonces');

    if( isset( $used_nonces[$nonce] ) ) {
        return false;
    }

    if(is_array($used_nonces)){
        foreach ($used_nonces as $nonce=> $timestamp){
            if( $timestamp > $time ){
                break;
            }
            unset( $used_nonces[$nonce] );
        }
    }

    //Add nonce in the stack after 2min
    if($time > $expires2){
        $used_nonces[$toadd_nonce] = $expires;
        asort( $used_nonces );
        update_option( '_sh_used_nonces',$used_nonces );
    }
    return true;
}

function wpestate_file_upload_max_size() {
  static $max_size = -1;

  if ($max_size < 0) {
    // Start with post_max_size.
    $max_size = wpestate_parse_size(ini_get('post_max_size'));

    // If upload_max_size is less, then reduce. Except if upload_max_size is
    // zero, which indicates no limit.
    $upload_max = wpestate_parse_size(ini_get('upload_max_filesize'));
    if ($upload_max > 0 && $upload_max < $max_size) {
      $max_size = $upload_max;
    }
  }
  return $max_size;
}

function wpestate_parse_size($size) {
  $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
  $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
  if ($unit) {
    // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
    return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
  }
  else {
    return round($size);
  }
}


add_action('wp_head', 'wpestate_rand654_add_css');
function wpestate_rand654_add_css() {
    if ( is_singular('estate_property') ) {
        $local_id=get_the_ID();
        $wp_estate_global_page_template               =     intval( wpresidence_get_option('wp_estate_global_property_page_template') );
        $wp_estate_local_page_template                =     intval( get_post_meta($local_id, 'property_page_design_local', true));
        if ( $wp_estate_local_page_template === 0 ) {
            $wp_estate_local_page_template            =     intval( get_post_meta($local_id, 'property_page_desing_local', true));
        }
        if($wp_estate_global_page_template!=0 || $wp_estate_local_page_template!=0){

            if($wp_estate_local_page_template!=0){
                $id = $wp_estate_local_page_template;
            }else{
                $id = $wp_estate_global_page_template;
            }

            if ( $id ) {
                $shortcodes_custom_css = get_post_meta( $id, '_wpb_shortcodes_custom_css', true );
                if ( ! empty( $shortcodes_custom_css ) ) {
                    print '<style type="text/css" data-type="vc_shortcodes-custom-css-'.intval($id).'">';
                    print trim($shortcodes_custom_css);
                    print '</style>';
                }
            }
        }
    }
}




// Enable font size & font family selects in the editor
if ( ! function_exists( 'wpestate_mce_buttons' ) ) {
	function wpestate_mce_buttons( $buttons ) {
		array_unshift( $buttons, 'fontselect' ); // Add Font Select
		array_unshift( $buttons, 'fontsizeselect' ); // Add Font Size Select
		return $buttons;
	}
}
add_filter( 'mce_buttons_2', 'wpestate_mce_buttons' );







if(!function_exists('wpestate_all_prop_details_prop_unit')):
function wpestate_all_prop_details_prop_unit(){
    $single_details = array(

        'Image'         =>  'image',
        'Title'         =>  'title',
        'Description'   =>  'description',
        'Categories'    =>  'property_category',
        'Action'        =>  'property_action_category',
        'City'          =>  'property_city',
        'Neighborhood'  =>  'property_area',
        'County / State'=>  'property_county_state',
        'Address'       =>  'property_address',
        'Zip'           =>  'property_zip',
        'Country'       =>  'property_country',
        'Status'        =>  'property_status',
        'Price'         =>  'property_price',

        'Size'              =>  'property_size',
        'Lot Size'          =>  'property_lot_size',
        'Rooms'             =>  'property_rooms',
        'Bedrooms'          =>  'property_bedrooms',
        'Bathrooms'         =>  'property_bathrooms',
        'Agent'             =>  'property_agent',
        'Agent Picture'     =>  'property_agent_picture'

    );

    $custom_fields = wpresidence_get_option( 'wp_estate_custom_fields', '');
    if( !empty($custom_fields)){
        $i=0;
        while($i< count($custom_fields) ){
            $name =   $custom_fields[$i][0];
            $slug         =     wpestate_limit45(sanitize_title( $name ));
            $slug         =     sanitize_key($slug);
            $single_details[str_replace('-',' ',$name)]=     $slug;
            $i++;
       }
    }

    return $single_details;
}
endif;









function wpestate_search_delete_user( $user_id ) {

    $user_obj = get_userdata( $user_id );
    $email = $user_obj->user_email;

    $args = array(
    'post_type'        => 'wpestate_search',
    'post_status'      =>  'any',
    'posts_per_page'   => -1 ,
    'meta_query' => array(
            array(
                    'key'     => 'user_email',
                    'value'   => $email,
                    'compare' => '=',
            ),
        ),
    );
    $prop_selection = new WP_Query($args);

    while ($prop_selection->have_posts()): $prop_selection->the_post();
        $post_id        =   get_the_id();
        $user_email     =   get_post_meta($post_id, 'user_email', true) ;
        wp_delete_post($post_id,true);
    endwhile;

}
add_action( 'delete_user', 'wpestate_search_delete_user' );




if(!function_exists('wpestate_add_meta_post_to_search')):
function wpestate_add_meta_post_to_search($meta_array){
    global $table_prefix;
    global $wpdb;

    foreach($meta_array as $key=> $value){


        switch ($value['compare']) {
            case '=':

                  $potential_ids[$key]=
                            wpestate_get_ids_by_query(
                                $wpdb->prepare("
                                    SELECT post_id
                                    FROM ".$table_prefix."postmeta
                                    WHERE meta_key = %s
                                    AND CAST(meta_value AS UNSIGNED) = %f
                                ",array($value['key'],$value['value']) )
                        );
                break;
            case '>=':
                if($value['type']=='DATE'){
                    $potential_ids[$key]=
                        wpestate_get_ids_by_query(
                            $wpdb->prepare("
                                SELECT post_id
                                FROM ".$table_prefix."postmeta
                                WHERE meta_key = %s
                                AND CAST(meta_value AS DATE) >= %s
                            ",array($value['key'],( $value['value'] )) )
                    );
                }else{
                   $potential_ids[$key]=
                        wpestate_get_ids_by_query(
                            $wpdb->prepare("
                                SELECT post_id
                                FROM ".$table_prefix."postmeta
                                WHERE meta_key = %s
                                AND CAST(meta_value AS UNSIGNED) >= %f
                            ",array($value['key'],$value['value']) )
                        );
                }
                break;
            case '<=':
                if($value['type']=='DATE'){
                    $potential_ids[$key]=
                        wpestate_get_ids_by_query(
                            $wpdb->prepare("
                                SELECT post_id
                                FROM ".$table_prefix."postmeta
                                WHERE meta_key = %s
                                AND CAST(meta_value AS DATE) <= %s
                            ",array($value['key'],($value['value'])) )
                        );
                }else{
                    $potential_ids[$key]=
                            wpestate_get_ids_by_query(
                                $wpdb->prepare("
                                    SELECT post_id
                                    FROM ".$table_prefix."postmeta
                                    WHERE meta_key = %s
                                    AND CAST(meta_value AS UNSIGNED) <= %f
                                ",array($value['key'],$value['value']) )
                            );
                }

                break;
            case 'LIKE':

                $wild = '%';
                    $find = $value['value'];
                    $like = $wild . $wpdb->esc_like( $find ) . $wild;
                    $potential_ids[$key]=wpestate_get_ids_by_query(
                    $wpdb->prepare("
                        SELECT post_id
                        FROM ".$table_prefix."postmeta
                        WHERE meta_key =%s AND meta_value LIKE %s
                    ",array($value['key'],$like) ) );



                break;
            case 'BETWEEN':
                $potential_ids[$key]=
                    wpestate_get_ids_by_query(
                        $wpdb->prepare("
                            SELECT post_id
                            FROM ".$table_prefix."postmeta
                            WHERE meta_key = '%s'
                            AND CAST(meta_value AS SIGNED)  BETWEEN '%f' AND '%f'
                        ",array($value['key'],floatval( $value['value'][0] ),floatval( $value['value'][1] ) ) )
                );
                break;
        }

        $potential_ids[$key]=  array_unique($potential_ids[$key]);

    }



    $ids=[];
    if(!empty($potential_ids)){

        foreach($potential_ids[0] as $elements){
            $ids[]=$elements;
        }

        foreach($potential_ids as $key=>$temp_ids){
            $ids = array_intersect($ids,$temp_ids);
        }
    }

    $ids=  array_unique($ids);
    if(empty($ids)){
        $ids[]=0;
    }
    return $ids;

}
endif;


add_action ( 'admin_enqueue_scripts', function () {
    if (is_admin ())
        wp_enqueue_media ();
} );








function noo_enable_vc_auto_theme_update() {
	if( function_exists('vc_updater') ) {
		$vc_updater = vc_updater();
                if(function_exists('wpestate_disable_filtering')){
                    wpestate_disable_filtering( 'upgrader_pre_download', array( $vc_updater, 'preUpgradeFilter' ), 10 );
                }
                if( function_exists( 'vc_license' ) ) {
			if( !vc_license()->isActivated() ) {
                                if(function_exists('wpestate_disable_filtering')){
                                    wpestate_disable_filtering( 'pre_set_site_transient_update_plugins', array( $vc_updater->updateManager(), 'check_update' ), 10 );
                                }
                        }
		}
	}
}
add_action('vc_after_init', 'noo_enable_vc_auto_theme_update');



/*
 * 
 * Query vars filter 
 * 
 * 
 * 
 * 
 * */



function wpestate_add_query_vars_filter( $vars ){
  $vars[] = "packet";
  return $vars;
}
add_filter( 'query_vars', 'wpestate_add_query_vars_filter' );




/*
 * 
 * OCDI set import intro text 
 * 
 * 
 * 
 * 
 * */


function ocdi_plugin_intro_text( $default_text ) {
    $default_text = '<div class="ocdi__intro-text notice notice-warning intro-text_wpestate"> For speed purposes, demo images are not included in the import. '
            . 'Revolution Sliders are imported separately from demo_content/revolutions_sliders folder If the import doesn’t go through in 1-2 minutes, server limits are affecting the import. '
            . 'Please check the server requirements list <a href="http://help.wpresidence.net/article/theme-wordpress-server-requirements/" target="_blank">here</a>.  '
            . 'For our assistance with this process, please contact us through client support <a href="http://support.wpestate.org/" target="_blank">here.</a></div>';

    return $default_text;
}
add_filter( 'pt-ocdi/plugin_intro_text', 'ocdi_plugin_intro_text' );




/*
 * 
 * OCDI set import fiter 
 * 
 * 
 * 
 * 
 * */


add_filter( 'pt-ocdi/import_files', 'ocdi_import_files_new' );


function ocdi_import_files_new() {

   
    if(class_exists('WpestateFunk') ){
        $WpestateFunk = WpestateFunk::get_instance();
   
        $demos_data = $WpestateFunk->get_demos_data();
      
    }


    
    $demos=array(
            'main-demo' =>  array(
                'import_file_name'          =>  'Main Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/main-demo-preview.jpg')  ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://main.wpresidence.net',
                'import_redux'              =>  array(
                            array(
                            'file_url'      => '',                                                            
                            'option_name'   => 'wpresidence_admin',
                            ),
                        ),
            ),

            'nashville-demo' =>  array(
                'import_file_name'          =>  'Nashville Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/nashville-demo-preview.jpg')  ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://nashville.wpresidence.net',
                'import_redux'              =>  array(
                            array(
                            'file_url'      => '',                                                            
                            'option_name'   => 'wpresidence_admin',
                            ),
                        ),
            ), 

            'phoenix-demo' =>  array(
                'import_file_name'          =>  'Phoenix Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/phoenix-demo-preview.jpg')  ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://phoenix.wpresidence.net',
                'import_redux'              =>  array(
                            array(
                            'file_url'      => '',                                                            
                            'option_name'   => 'wpresidence_admin',
                            ),
                        ),
            ), 

    
            'emirates-demo' =>  array(
                'import_file_name'          =>  'Emirates Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/emirates-demo-preview.jpg')  ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://emirates.wpresidence.net',
                'import_redux'              =>  array(
                            array(
                            'file_url'      => '',                                                            
                            'option_name'   => 'wpresidence_admin',
                            ),
                        ),
            ),    

            'portland-demo' =>  array(
                'import_file_name'          =>  'Portland Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/portland-preview.jpg')  ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://portland.wpresidence.net',
                'import_redux'              =>  array(
                        array(
                        'file_url'      => '',                                                            
                        'option_name'   => 'wpresidence_admin',
                        ),
                    ),
            ),

            'napa-demo' =>  array(
                'import_file_name'          =>  'Napa Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/napa-preview.jpg')  ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://napa.wpresidence.net',
                'import_redux'              =>  array(
                        array(
                        'file_url'      => '',                                                            
                        'option_name'   => 'wpresidence_admin',
                        ),
                    ),
            ),

            'sanjose-demo'   =>  array(  
                'import_file_name'          =>  'San Jose Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/sanjose-demo-preview.jpg') ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://sanjose.wpresidence.net/',
                'import_redux'              =>  array(
                        array(
                            'file_url'      => '',                                                            
                            'option_name'   => 'wpresidence_admin',
                        ),
                    )
            ),		 

			'sandiego-demo'   =>  array(  
                'import_file_name'          =>  'San Diego Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/sandiego-demo-preview.jpg') ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://sandiego.wpresidence.net/',
                'import_redux'              =>  array(
                        array(
                            'file_url'      => '',                                                            
                            'option_name'   => 'wpresidence_admin',
                        ),
                    )
            ),


			'baltimore-demo'   =>  array(  
                'import_file_name'          =>  'Baltimore Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/baltimore-demo-preview.jpg') ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://baltimore.wpresidence.net/',
                'import_redux'              =>  array(
                        array(
                            'file_url'      => '',                                                            
                            'option_name'   => 'wpresidence_admin',
                        ),
                    )
            ),
			
			'denver-demo'   =>  array(  
                'import_file_name'          =>  'Denver Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/denver-demo-preview.jpg') ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://denver.wpresidence.net/',
                'import_redux'              =>  array(
                        array(
                            'file_url'      => '',                                                            
                            'option_name'   => 'wpresidence_admin',
                        ),
                    )
            ),

			'reno-demo'   =>  array(  
                'import_file_name'          =>  'Reno Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/reno-demo-preview.jpg') ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://reno.wpresidence.net/',
                'import_redux'              =>  array(
                    array(
                        'file_url'      => '',                                                            
                        'option_name'   => 'wpresidence_admin',
                    ),
                )
            ),	

            'lasvegas-demo'   =>  array(  
                'import_file_name'          =>  'Las Vegas Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/lasvegas-demo-preview.jpg') ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://lasvegas.wpresidence.net/',
                'import_redux'              =>  array(
                    array(
                        'file_url'      => '',                                                            
                        'option_name'   => 'wpresidence_admin',
                    ),
                )
            ),

            'orlando-demo'  =>    array(
                'import_file_name'              =>  'Orlando Demo',
                 'import_file_url'              =>  '',
                'import_widget_file_url'        =>  '',
                'import_preview_image_url'      =>  get_theme_file_uri('wpestate_templates/orlando-demo-preview.jpg')  ,
                'import_notice'                 =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'                   =>  'https://orlando.wpresidence.net',
                'import_redux'                  =>  array(
                        array(
                            'file_url'   => '',                                                            
                            'option_name' => 'wpresidence_admin',
                        ),
                    )
            ),
                     
            'oakland-demo'  =>    array(
                'import_file_name'              =>  'Oakland Demo',
                 'import_file_url'              =>  '',
                'import_widget_file_url'        =>  '',
                'import_preview_image_url'      =>  get_theme_file_uri('wpestate_templates/oakland-demo-preview.jpg')  ,
                'import_notice'                 =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'                   =>  'https://oakland.wpresidence.net',
                'import_redux'                  =>  array(
                        array(
                            'file_url'   => '',                                                            
                            'option_name' => 'wpresidence_admin',
                        ),
                    )
            ),

           'montana-demo'   =>  array(  
                'import_file_name'          =>  'Montana Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/montana-demo-preview.jpg') ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://montana.wpresidence.net/',
                'import_redux'              =>  array(
                                                    array(
                                                        'file_url'      => '',                                                            
                                                        'option_name'   => 'wpresidence_admin',
                                                    ),
                                                )
            ),
            'detroit-demo'   =>  array(  
                'import_file_name'          =>  'Detroit Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/detroit-demo-preview.jpg') ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://detroit.wpresidence.net/',
                'import_redux'              =>  array(
                                                    array(
                                                        'file_url'      => '',                                                            
                                                        'option_name'   => 'wpresidence_admin',
                                                    ),
                                                )
            ),
        
            'montreal-demo'   =>  array(  
                'import_file_name'          =>  'Montreal Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/montreal-demo-preview.jpg') ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://montreal.wpresidence.net/',
                'import_redux'              =>  array(
                                                    array(
                                                        'file_url'      => '',                                                            
                                                        'option_name'   => 'wpresidence_admin',
                                                    ),
                                                )
            ),

        
            'dubai-demo'   =>  array(  
                'import_file_name'          =>  'Dubai Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/dubai-demo-preview.jpg') ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://dubai.wpresidence.net/',
                'import_redux'              =>  array(
                                                    array(
                                                        'file_url'      => '',                                                            
                                                        'option_name'   => 'wpresidence_admin',
                                                    ),
                                                )
            ),


            'kyiv-demo'   =>  array(  
                'import_file_name'          =>  'Kyiv Demo',
                'import_file_url'           =>  '',
                'import_widget_file_url'    =>  '',
                'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/kyiv-demo-preview.jpg') ,
                'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'               =>  'https://kyiv.wpresidence.net/',
                'import_redux'              =>  array(
                                                    array(
                                                        'file_url'      => '',                                                            
                                                        'option_name'   => 'wpresidence_admin',
                                                    ),
                                                )
            ),


            'austin-demo'   =>  array(  
                            'import_file_name'          =>  'Austin Demo',
                            'import_file_url'           =>  '',
                            'import_widget_file_url'    =>  '',
                            'import_preview_image_url'  =>  get_theme_file_uri('wpestate_templates/austin-demo-preview.jpg') ,
                            'import_notice'             =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                            'preview_url'               =>  'https://austin.wpresidence.net/',
                            'import_redux'              =>  array(
                                                                array(
                                                                    'file_url'      => '',                                                            
                                                                    'option_name'   => 'wpresidence_admin',
                                                                ),
                                                            )
            ),
        
    'beijing-demo'=>array(
                     'import_file_name'                  =>  'Bejing Demo',
                     'import_file_url'                   =>  '',
                     'import_widget_file_url'            =>  '',
                     'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/beijing-demo-preview.jpg') ,
                     'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                     'preview_url'                       =>  'https://beijing.wpresidence.net/',
                     'import_redux'                      =>  array(
                                                                 array(
                                                                 'file_url'   => '',                                                            
                                                                 'option_name' => 'wpresidence_admin',
                                                                 ),
                                                             )

                 ),

    'madrid-demo'=>array(
                        'import_file_name'                  =>  'Madrid Demo',
                        'import_file_url'                   =>  '',
                        'import_widget_file_url'            =>  '',
                        'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/madrid-demo-preview.jpg') ,
                        'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                        'preview_url'                       =>  'https://madrid.wpresidence.net/',
                        'import_redux'                      =>  array(
                                                                    array(
                                                                    'file_url'   => '',                                                            
                                                                    'option_name' => 'wpresidence_admin',
                                                                    ),
                                                                )
                            
                    ),        

    'miami-demo'=>array(
                        'import_file_name'                  =>  'Miami Demo',
                        'import_file_url'                   =>  '',
                        'import_widget_file_url'            =>  '',
                        'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/miami-demo-preview.jpg') ,
                        'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                        'preview_url'                       =>  'https://miami.wpresidence.net/',
                        'import_redux'                      =>  array(
                                                                    array(
                                                                    'file_url'   => '',                                                            
                                                                    'option_name' => 'wpresidence_admin',
                                                                    ),
                                                                )
                            
                    ),        


    'toronto-demo'=>array(
                'import_file_name'                  =>  'Toronto Demo',
                'import_file_url'                   =>  '',
                'import_widget_file_url'            =>  '',
                'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/toronto-demo-preview.jpg') ,
                'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'                       =>  'https://toronto.wpresidence.net/',
                'import_redux'                      =>  array(
                                                            array(
                                                            'file_url'   => '',                                                            
                                                            'option_name' => 'wpresidence_admin',
                                                            ),
                                                        )

            ),
    'boston-demo'=>array(
                    'import_file_name'                  =>  'Boston Demo',
                    'import_file_url'                   =>  '',
                    'import_widget_file_url'            =>  '',
                    'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/boston-demo-preview.jpg') ,
                    'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                    'preview_url'                       =>  'https://boston.wpresidence.net/',
                    'import_redux'                      =>  array(
                                                                array(
                                                                'file_url'   => '',                                                            
                                                                'option_name' => 'wpresidence_admin',
                                                                ),
                                                            )

                ),  
        
    'houston-demo'=>array(
                'import_file_name'                  =>  'Houston Demo',
                'import_file_url'                   =>  '',
                'import_widget_file_url'            =>  '',
                'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/houston-demo-preview.jpg') ,
                'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                'preview_url'                       =>  'https://houston.wpresidence.net/',
                'import_redux'                      =>  array(
                                                            array(
                                                            'file_url'   => '',                                                            
                                                            'option_name' => 'wpresidence_admin',
                                                            ),
                                                        )

            ),  
        
     
        
    'dallas-demo'=>array(
                    'import_file_name'                  =>  'Dallas Demo',
                    'import_file_url'                   =>  '',
                    'import_widget_file_url'            =>  '',
                    'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/dallas-demo-preview.jpg') ,
                    'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                    'preview_url'                       =>  'https://dallas.wpresidence.net/',
                    'import_redux'                      =>  array(
                                                                array(
                                                                'file_url'   => '',                                                            
                                                                'option_name' => 'wpresidence_admin',
                                                                ),
                                                            )

                ),  
    'memphis-demo'=>array(
                    'import_file_name'                  =>  'Memphis Demo',
                    'import_file_url'                   =>  '',
                    'import_widget_file_url'            =>  '',
                    'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/memphis-demo-preview.jpg') ,
                    'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                    'preview_url'                       =>  'https://memphis.wpresidence.net/',
                    'import_redux'                      =>  array(
                                                                array(
                                                                'file_url'   => '',                                                            
                                                                'option_name' => 'wpresidence_admin',
                                                                ),
                                                            )

                ),  
    'seattle-demo'=>array(
                    'import_file_name'                  =>  'Seattle Demo',
                    'import_file_url'                   =>  '',
                    'import_widget_file_url'            =>  '',
                    'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/seattle-demo-preview.jpg') ,
                    'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                    'preview_url'                       =>  'https://seattle.wpresidence.net/',
                    

                ),          


    'mumbai-demo'=>array(
                    'import_file_name'                  =>  'Mumbai Demo',
                    'import_file_url'                   =>  '',
                    'import_widget_file_url'            =>  '',
                    'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/mumbai-demo-preview.jpg') ,
                    'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                    'preview_url'                       =>  'https://mumbai.wpresidence.net/',
                    'import_redux'                      =>  array(
                                                                array(
                                                                'file_url'   => '',                                                            
                                                                'option_name' => 'wpresidence_admin',
                                                                ),
                                                            )

                ),  
    
    'chicago-demo'  =>    array(
                            'import_file_name'              =>  'Chicago Demo',
                             'import_file_url'              =>  '',
                            'import_widget_file_url'        =>  '',
                            'import_preview_image_url'      =>  get_theme_file_uri('wpestate_templates/chicago-demo-preview.jpg')  ,
                            'import_notice'                 =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                            'preview_url'                   =>  'https://chicago.wpresidence.net',
                            'import_redux'                  =>  array(
                                                                    array(
                                                                        'file_url'   => '',                                                            
                                                                        'option_name' => 'wpresidence_admin',
                                                                    ),
                                                                )
                              ),
        
        
        
            'ny-demo'=>array(
                        'import_file_name'                  => 'New York Demo',
                        'import_file_url'                   =>  '',
                        'import_widget_file_url'            =>  '',
                        'import_preview_image_url'          => get_theme_file_uri( 'wpestate_templates/ny-demo-preview.jpg')  ,
                        'import_notice'                     => esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                        'preview_url'                       => 'https://newyork.wpresidence.net',
                        'import_redux'                      =>  array(
                                                                    array(
                                                                    'file_url'   => '',                                                            
                                                                    'option_name' => 'wpresidence_admin',
                                                                    ),
                                                                 )
                            
                    ),

            'losangeles-demo'=>    array(
                        'import_file_name'                  =>  'Los Angeles Demo',
                        'import_file_url'                   =>  '',
                        'import_widget_file_url'            =>  '',
                        'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/losangeles-demo-preview.jpg')  ,
                        'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                        'preview_url'                       => 'https://losangeles.wpresidence.net',
                        'import_redux'                      =>  array(
                                                                    array(
                                                                    'file_url'   => '',                                                            
                                                                    'option_name' => 'wpresidence_admin',
                                                                    )
                                                                 )
                    ),

        
        'rio-demo'=>array(
                        'import_file_name'                  =>  'Rio Demo',
                        'import_file_url'                   =>  '',
                        'import_widget_file_url'            =>  '',
                        'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                        'import_preview_image_url'          =>  get_theme_file_uri( 'wpestate_templates/rio-demo-preview.jpg')  ,
                          'preview_url'                     =>  'https://rio.wpresidence.net',
                        'import_redux'                      =>  array(
                                                                    array(
                                                                    'file_url'   => '',                                                            
                                                                    'option_name' => 'wpresidence_admin',
                                                                    ),
                                                                 )
                            
                    ),
        
        'tokyo-demo'=>array(
                        'import_file_name'                  => 'Tokyo Demo',
                        'import_file_url'                   =>  '',
                        'import_widget_file_url'            =>  '',
                        'import_preview_image_url'          => get_theme_file_uri('wpestate_templates/tokyo-demo-preview.jpg') ,
                        'import_notice'                     => esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                        'preview_url'                       => 'https://tokyo.wpresidence.net',
                        'import_redux'                      =>  array(
                                                                    array(
                                                                    'file_url'   => '',                                                            
                                                                    'option_name' => 'wpresidence_admin',
                                                                    ),
                                                                 )
                            
                    ),
        
        
        'paris-demo'=>array(
                        'import_file_name'                  => 'Paris Demo',
                        'import_file_url'                   =>  '',
                        'import_widget_file_url'            =>  '',
                        'import_preview_image_url'          => get_theme_file_uri('wpestate_templates/paris-demo-preview.jpg') ,
                        'import_notice'                     => esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                        'preview_url'                       => 'https://paris.wpresidence.net',
                        'import_redux'                      =>  array(
                                                                    array(
                                                                    'file_url'   => '',                                                            
                                                                    'option_name' => 'wpresidence_admin',
                                                                    ),
                                                                 )
                            
                    ),

        'london-demo'=>array(
                  'import_file_name'                  =>    'London Demo',
                  'import_file_url'                   =>    '',
                  'import_widget_file_url'            =>    '',
                  'import_preview_image_url'          =>    get_theme_file_uri( 'wpestate_templates/london-demo-preview.jpg') ,
                  'import_notice'                     =>    esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                  'preview_url'                       =>    'https://london.wpresidence.net',
                  'import_redux'                      =>    array(
                                                                array(
                                                                    'file_url'   => '',                                                            
                                                                    'option_name' => 'wpresidence_admin',
                                                                ),
                                                           )

              ),
        'sidney-demo'=>array(
                        'import_file_name'                  =>  'Sydney Demo',
                        'import_file_url'                   =>  '',
                        'import_widget_file_url'            =>  '',
                        'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/sidney-demo-preview.jpg') ,
                        'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                        'preview_url'                       =>  'https://sydney.wpresidence.net',
                        'import_redux'                      =>  array(
                                                                    array(
                                                                    'file_url'   => '',                                                            
                                                                    'option_name' => 'wpresidence_admin',
                                                                    ),
                                                                )
                            
                    ),
        'rome-demo'=>array(
                        'import_file_name'                  =>  'Rome Demo',
                        'import_file_url'                   =>  '',
                        'import_widget_file_url'            =>  '',
                        'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/rome-demo-preview.jpg'),
                        'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                        'preview_url'                       =>  'https://rome.wpresidence.net',
                        'import_redux'                      =>  array(
                                                                    array(
                                                                        'file_url'   => '',                                                            
                                                                        'option_name' => 'wpresidence_admin',
                                                                    ),
                                                                 )
                            
                    ),
        'demo1'=>array(
                        'import_file_name'                  =>  'Demo 1',
                        'import_file_url'                   =>  '',
                        'import_widget_file_url'            =>  '',
                        'import_preview_image_url'          =>  get_theme_file_uri( 'wpestate_templates/demo1-preview.jpg') ,
                        'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                        'preview_url'                       =>  'https://demo1.wpresidence.net',
                        'import_redux'                      =>  array(
                                                                    array(
                                                                    'file_url'   => '',                                                            
                                                                    'option_name' => 'wpresidence_admin',
                                                                    ),
                                                                )
                            
                    ),
        'demo2'=>array(
                        'import_file_name'                  =>  'Demo 2',
                        'import_file_url'                   =>  '',
                        'import_widget_file_url'            =>  '',
                        'import_preview_image_url'          =>  get_theme_file_uri( 'wpestate_templates/demo2-preview.jpg'),
                        'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                        'preview_url'                       =>  'https://demo2.wpresidence.net',
                        'import_redux'                      =>  array(
                                                                    array(
                                                                    'file_url'   => '',                                                            
                                                                    'option_name' => 'wpresidence_admin',
                                                                    ),
                                                                )
                            
                    ),
        'demo3'=>array(
                        'import_file_name'                  =>  'Demo 3',
                        'import_file_url'                   =>  '',
                        'import_widget_file_url'            =>  '',
                        'import_preview_image_url'          =>  get_theme_file_uri( 'wpestate_templates/demo3-preview.jpg'),
                        'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                        'preview_url'                       =>  'https://demo3.wpresidence.net',
                        'import_redux'                      =>  array(
                                                                    array(
                                                                    'file_url'   => '',                                                            
                                                                    'option_name' => 'wpresidence_admin',
                                                                    ),
                                                                )
                            
                    ),
        'demo5'=>array(
                        'import_file_name'                  =>  'Demo 5',
                        'import_file_url'                   =>  '',
                        'import_widget_file_url'            =>  '',
                        'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/demo5-preview.jpg') ,
                        'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                        'preview_url'                       =>  'https://demo5.wpresidence.net',
                        'import_redux'                      =>  array(
                                                                    array(
                                                                    'file_url'   => '',                                                            
                                                                    'option_name' => 'wpresidence_admin',
                                                                    ),
                                                                )
                            
                    ),    
        
    'pt-demo'=>array(
                    'import_file_name'                  =>  'Portuguese Demo',
                    'import_file_url'                   =>  '',
                    'import_widget_file_url'            =>  '',
                    'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/pt-demo-preview.jpg')  ,
                    'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                    'preview_url'                       =>  'https://pt.wpresidence.net/',
                    'import_redux'                      =>  array(
                                                                array(
                                                                'file_url'   => '',                                                            
                                                                'option_name' => 'wpresidence_admin',
                                                                ),
                                                            )

                ),  
    'fr-demo'=>array(
                    'import_file_name'                  =>  'French Demo',
                    'import_file_url'                   =>  '',
                    'import_widget_file_url'            =>  '',
                    'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/fr-demo-preview.jpg')  ,
                    'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                    'preview_url'                       =>  'https://fr.wpresidence.net/',
                    'import_redux'                      =>  array(
                                                                array(
                                                                'file_url'   => '',                                                            
                                                                'option_name' => 'wpresidence_admin',
                                                                ),
                                                            )

                ),          
        
        
    'esp-demo'=>array(
                    'import_file_name'                  =>  'Spanish Demo',
                    'import_file_url'                   =>  '',
                    'import_widget_file_url'            =>  '',
                    'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/esp-demo-preview.jpg')  ,
                    'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                    'preview_url'                       =>  'https://esp.wpresidence.net/',
                    'import_redux'                      =>  array(
                                                                array(
                                                                'file_url'   => '',                                                            
                                                                'option_name' => 'wpresidence_admin',
                                                                ),
                                                            )

                ),  
    'de-demo'=>array(
                    'import_file_name'                  =>  'German Demo',
                    'import_file_url'                   =>  '',
                    'import_widget_file_url'            =>  '',
                    'import_preview_image_url'          =>  get_theme_file_uri('wpestate_templates/de-demo-preview.jpg')  ,
                    'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                    'preview_url'                       =>   'https://de.wpresidence.net/',
                    'import_redux'                      =>  array(
                                                                array(
                                                                'file_url'   => '',                                                            
                                                                'option_name' => 'wpresidence_admin',
                                                                ),
                                                            )

                ),          
    'it-demo'=>array(
                    'import_file_name'                  =>  'Italian Demo',
                    'import_file_url'                   =>  '',
                    'import_widget_file_url'            =>  '',
                    'import_preview_image_url'          =>   get_theme_file_uri('wpestate_templates/it-demo-preview.jpg')  ,
                    'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                    'preview_url'                       =>  'https://it.wpresidence.net/',
                    'import_redux'                      =>  array(
                                                                array(
                                                                'file_url'   => '',                                                            
                                                                'option_name' => 'wpresidence_admin',
                                                                ),
                                                            )

                ),  

    'rtl-demo'=>array(
                    'import_file_name'                  =>  'RTL Demo',
                    'import_file_url'                   =>  '',
                    'import_widget_file_url'            =>  '',
                    'import_preview_image_url'          =>   get_theme_file_uri('wpestate_templates/rtl-demo-preview.jpg')  ,
                    'import_notice'                     =>  esc_html__( 'Clear theme cache after demo import is complete!', 'wpresidence' ),
                    'preview_url'                       =>   'https://rtl.wpresidence.net/',
                    'import_redux'                      =>  array(
                                                                array(
                                                                'file_url'   => '',                                                            
                                                                'option_name' => 'wpresidence_admin',
                                                                ),
                                                            )

                ),    
                   
  
        );

    
    
    $return_array=array();
    

    foreach($demos as $key=>$demo):
        if(isset($demos_data[$key])){
                $demo['import_file_url']                    =   $demos_data[$key]['theme_content'];
                $demo['import_widget_file_url']             =   $demos_data[$key]['widgets'];
                $demo['import_redux'][0]['file_url']           =   $demos_data[$key]['redux_options'];
                $demo['import_redux'][0]['option_name']        =    'wpresidence_admin';
            
                $return_array[]=$demo;
        }
    endforeach;


    return $return_array;
}











/**
 * Performs post-import setup tasks after One Click Demo Import completion
 * 
 * This function handles the configuration of menus, pages, and data conversion
 * processes that need to occur after importing demo content. It sets up
 * navigation menus, assigns the front page, and triggers various conversion
 * routines for comments and property pages.
 * 
 * @since 1.0.0
 * @return void
 */
function ocdi_after_import_setup() {
    // Get the main menu and footer menu terms from the database
    $main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
    $footer_menu = get_term_by( 'name', 'footer', 'nav_menu' );
    
    // Set theme modification for menu locations - assigns menus to their respective locations
    set_theme_mod( 'nav_menu_locations', array(
        'primary'       => $main_menu->term_id,    // Primary navigation menu
        'mobile'        => $main_menu->term_id,    // Mobile navigation menu (same as primary)
        'footer_menu'   => $footer_menu->term_id,  // Footer navigation menu
        )
    );
    
    // Assign front page and posts page (blog page).
    $front_page_id = get_page_by_title( 'Homepage' );  // Get the Homepage page object
    update_option( 'show_on_front', 'page' );          // Set WordPress to show a static page on front
    update_option( 'page_on_front', $front_page_id->ID );  // Set the Homepage as the front page
    
    // delete flag that mark conversion wpresidence_convert_comments_to_estate_review_done
    delete_option( 'wpresidence_convert_comments_to_estate_review_done' );
    
    // Check if the comment conversion function exists before calling it
    if (function_exists( 'wpresidence_convert_comments_to_estate_review' )) {
        // Convert existing comments to estate review format
        wpresidence_convert_comments_to_estate_review();
    }
    
    // delete flag that mark conversion wpresidence_convert_property_pages_to_studio_done
    delete_option( 'wpresidence_convert_property_pages_to_studio_done' );
    
    //call convertion
    // Check if the property pages conversion function exists before calling it
    if(function_exists('wpresidence_convert_property_pages_to_studio')){
        // Convert property pages to studio format
        wpresidence_convert_property_pages_to_studio();
    }
   
   
    // Clear all API cache after setup is complete
    wpestate_api_clear_all_cache();
}

// Hook the function to run after the One Click Demo Import process completes
add_action( 'pt-ocdi/after_import', 'ocdi_after_import_setup' );



function wpestate_my_export_option_keys( $keys ) {
     $export_options = array(
        'wp_estate_show_reviews_prop',
        'wp_estate_enable_direct_mess',
        'wp_estate_admin_approves_reviews',
        'wp_estate_header5_info_widget1_icon',
        'wp_estate_header5_info_widget1_text1',
        'wp_estate_header5_info_widget1_text2',
        'wp_estate_header5_info_widget2_icon',
        'wp_estate_header5_info_widget2_text1',
        'wp_estate_header5_info_widget2_text2',
        'wp_estate_header5_info_widget3_text2',
        'wp_estate_header5_info_widget3_text1',
        'wp_estate_header5_info_widget3_icon',
        'wp_estate_spash_header_type',
        'wp_estate_splash_image',
        'wp_estate_splash_slider_gallery',
        'wp_estate_splash_slider_transition',
        'wp_estate_splash_video_mp4',
        'wp_estate_splash_video_webm',
        'wp_estate_splash_video_ogv',
        'wp_estate_splash_video_cover_img',
        'wp_estate_splash_overlay_image',
        'wp_estate_splash_overlay_color',
        'wp_estate_splash_overlay_opacity',
        'wp_estate_splash_page_title',
        'wp_estate_splash_page_subtitle',
        'wp_estate_splash_page_logo_link',
        'wp_estate_theme_slider_height',
        'wp_estate_sticky_search',
        'wp_estate_use_geo_location',
        'wp_estate_geo_radius_measure',
        'wp_estate_initial_radius',
        'wp_estate_min_geo_radius',
        'wp_estate_max_geo_radius',
        'wp_estate_paralax_header',
        'wp_estate_keep_max',
        'wp_estate_adv_back_color_opacity',
        'wp_estate_search_on_start',
        'wp_estate_use_float_search_form',
        'wp_estate_float_form_top',
        'wp_estate_float_form_top_tax',
        'wp_estate_use_price_pins',
        'wp_estate_use_price_pins_full_price',
        'wp_estate_use_single_image_pin',
        'wpestate_export_theme_options',
        'wp_estate_mobile_header_background_color',
        'wp_estate_mobile_header_icon_color',
        'wp_estate_mobile_menu_font_color',
        'wp_estate_mobile_menu_hover_font_color',
        'wp_estate_mobile_item_hover_back_color',
        'wp_estate_mobile_menu_backgound_color',
        'wp_estate_mobile_menu_border_color',
        'wp_estate_crop_images_lightbox',
        'wp_estate_show_lightbox_contact',
        'wp_estate_submission_page_fields',
        'wp_estate_mandatory_page_fields',
        'wp_estate_url_rewrites',
        'wp_estate_print_show_subunits',
        'wp_estate_print_show_agent',
        'wp_estate_print_show_description',
        'wp_estate_print_show_adress',
        'wp_estate_print_show_details',
        'wp_estate_print_show_features',
        'wp_estate_print_show_floor_plans',
        'wp_estate_print_show_images',
        'wp_estate_show_header_dashboard',
        'wp_estate_user_dashboard_menu_color',
        'wp_estate_user_dashboard_menu_hover_color',
        'wp_estate_user_dashboard_menu_color_hover',
        'wp_estate_user_dashboard_menu_back',
        'wp_estate_user_dashboard_package_back',
        'wp_estate_user_dashboard_package_color',
        'wp_estate_user_dashboard_buy_package',
        'wp_estate_user_dashboard_package_select',
        'wp_estate_user_dashboard_content_back',
        'wp_estate_user_dashboard_content_button_back',
        'wp_estate_user_dashboard_content_color',
        'wp_estate_property_multi_text',
        'wp_estate_property_multi_child_text',
        'wp_estate_theme_slider_type',
        'wp_estate_adv6_taxonomy',
        'wp_estate_adv6_taxonomy_terms',
        'wp_estate_adv6_max_price',
        'wp_estate_adv6_min_price',
        'wp_estate_adv_search_fields_no',
        'wp_estate_search_fields_no_per_row',
        'wp_estate_property_sidebar',
        'wp_estate_property_sidebar_name',
        'wp_estate_show_breadcrumbs',
        'wp_estate_global_property_page_template',
        'wp_estate_p_fontfamily',
        'wp_estate_p_fontsize',
        'wp_estate_p_fontsubset',
        'wp_estate_p_lineheight',
        'wp_estate_p_fontweight',
        'wp_estate_h1_fontfamily',
        'wp_estate_h1_fontsize',
        'wp_estate_h1_fontsubset',
        'wp_estate_h1_lineheight',
        'wp_estate_h1_fontweight',
        'wp_estate_h2_fontfamily',
        'wp_estate_h2_fontsize',
        'wp_estate_h2_fontsubset',
        'wp_estate_h2_lineheight',
        'wp_estate_h2_fontweight',
        'wp_estate_h3_fontfamily',
        'wp_estate_h3_fontsize',
        'wp_estate_h3_fontsubset',
        'wp_estate_h3_lineheight',
        'wp_estate_h3_fontweight',
        'wp_estate_h4_fontfamily',
        'wp_estate_h4_fontsize',
        'wp_estate_h4_fontsubset',
        'wp_estate_h4_lineheight',
        'wp_estate_h4_fontweight',
        'wp_estate_h5_fontfamily',
        'wp_estate_h5_fontsize',
        'wp_estate_h5_fontsubset',
        'wp_estate_h5_lineheight',
        'wp_estate_h5_fontweight',
        'wp_estate_h6_fontfamily',
        'wp_estate_h6_fontsize',
        'wp_estate_h6_fontsubset',
        'wp_estate_h6_lineheight',
        'wp_estate_h6_fontweight',
        'wp_estate_menu_fontfamily',
        'wp_estate_menu_fontsize',
        'wp_estate_menu_fontsubset',
        'wp_estate_menu_lineheight',
        'wp_estate_menu_fontweight',
        'wp_estate_transparent_logo_image',
        'wp_estate_stikcy_logo_image',
        'wp_estate_logo_image',
        'wp_estate_sidebar_boxed_font_color',
        'wp_estate_sidebar_heading_background_color',
        'wp_estate_map_controls_font_color',
        'wp_estate_map_controls_back',
        'wp_estate_transparent_menu_hover_font_color',
        'wp_estate_transparent_menu_font_color',
        'wp_estate_top_menu_hover_back_font_color',
        'wp_estate_top_menu_hover_type',
        'wp_estate_top_menu_hover_font_color',
        'wp_estate_active_menu_font_color',
        'wp_estate_menu_item_back_color',
        'wp_estate_sticky_menu_font_color',
        'wp_estate_top_menu_font_size',
        'wp_estate_menu_item_font_size',
        'wpestate_uset_unit',
        'wp_estate_sidebarwidget_internal_padding_top',
        'wp_estate_sidebarwidget_internal_padding_left',
        'wp_estate_sidebarwidget_internal_padding_bottom',
        'wp_estate_sidebarwidget_internal_padding_right',
        'wp_estate_widget_sidebar_border_size',
        'wp_estate_widget_sidebar_border_color',
        'wp_estate_unit_border_color',
        'wp_estate_unit_border_size',
        'wp_estate_blog_unit_min_height',
        'wp_estate_agent_unit_min_height',
        'wp_estate_agent_listings_per_row',
        'wp_estate_blog_listings_per_row',
        'wp_estate_content_area_back_color',
        'wp_estate_contentarea_internal_padding_top',
        'wp_estate_contentarea_internal_padding_left',
        'wp_estate_contentarea_internal_padding_bottom',
        'wp_estate_contentarea_internal_padding_right',
        'wp_estate_property_unit_color',
        'wp_estate_propertyunit_internal_padding_top',
        'wp_estate_propertyunit_internal_padding_left',
        'wp_estate_propertyunit_internal_padding_bottom',
        'wp_estate_propertyunit_internal_padding_right',
        'wpestate_property_unit_structure',
        'wpestate_property_page_content',
        'wp_estate_main_grid_content_width',
        'wp_estate_main_content_width',
        'wp_estate_header_height',
        'wp_estate_sticky_header_height',
        'wp_estate_border_radius_corner',
        'wp_estate_cssbox_shadow',
        'wp_estate_prop_unit_min_height',
        'wp_estate_border_bottom_header',
        'wp_estate_sticky_border_bottom_header',
        'wp_estate_listings_per_row',
        'wp_estate_unit_card_type',
        'wp_estate_prop_unit_min_height',
        'wp_estate_main_grid_content_width',
        'wp_estate_header_height',
        'wp_estate_sticky_header_height',
        'wp_estate_border_bottom_header_sticky_color',
        'wp_estate_border_bottom_header_color',
        'wp_estate_show_top_bar_user_login',
        'wp_estate_show_top_bar_user_menu',
        'wp_estate_currency_symbol',
        'wp_estate_where_currency_symbol',
        'wp_estate_measure_sys',
        'wp_estate_facebook_login',
        'wp_estate_google_login',
        'wp_estate_yahoo_login',
        'wp_estate_wide_status',
        'wp_estate_header_type',
        'wp_estate_prop_no',
        'wp_estate_prop_image_number',
        'wp_estate_show_empty_city',
        'wp_estate_blog_sidebar',
        'wp_estate_blog_sidebar_name',
        'wp_estate_blog_unit',
        'wp_estate_general_latitude',
        'wp_estate_general_longitude',
        'wp_estate_default_map_zoom',
        'wp_estate_cache',
        'wp_estate_show_adv_search_map_close',
        'wp_estate_pin_cluster',
        'wp_estate_zoom_cluster',
        'wp_estate_hq_latitude',
        'wp_estate_hq_longitude',
        'wp_estate_idx_enable',
        'wp_estate_geolocation_radius',
        'wp_estate_min_height',
        'wp_estate_max_height',
        'wp_estate_keep_min',
        'wp_estate_paid_submission',
        'wp_estate_admin_submission',
        'wp_estate_admin_submission_user_role',
        'wp_estate_price_submission',
        'wp_estate_price_featured_submission',
        'wp_estate_submission_curency',
        'wp_estate_free_mem_list',
        'wp_estate_free_feat_list',
        'wp_estate_free_feat_list_expiration',
        'wp_estate_free_pack_image_included',
        'wp_estate_custom_advanced_search',
        'wp_estate_adv_search_type',
        'wp_estate_show_adv_search',
        'wp_estate_show_adv_search_map_close',
        'wp_estate_cron_run',
        'wp_estate_show_no_features',
        'wp_estate_property_features_text',
        'wp_estate_property_description_text',
        'wp_estate_property_details_text',
        'wp_estate_status_list',
        'wp_estate_slider_cycle',
        'wp_estate_show_save_search',
        'wp_estate_search_alert',
        'wp_estate_adv_search_type',
        'wp_estate_color_scheme',
        'wp_estate_main_color',
        'wp_estate_second_color',
        'wp_estate_background_color',
        'wp_estate_content_back_color',
        'wp_estate_header_color',
        'wp_estate_breadcrumbs_font_color',
        'wp_estate_font_color',
        'wp_estate_menu_items_color',
        'wp_estate_link_color',
        'wp_estate_headings_color',
        'wp_estate_sidebar_heading_boxed_color',
        'wp_estate_sidebar_widget_color',
        'wp_estate_footer_back_color',
        'wp_estate_footer_font_color',
        'wp_estate_footer_copy_color',
        'wp_estate_footer_copy_back_color',
        'wp_estate_menu_font_color',
        'wp_estate_menu_hover_back_color',
        'wp_estate_menu_hover_font_color',
        'wp_estate_menu_border_color',
        'wp_estate_top_bar_back',
        'wp_estate_top_bar_font',
        'wp_estate_adv_search_back_color',
        'wp_estate_adv_search_font_color',
        'wp_estate_box_content_back_color',
        'wp_estate_box_content_border_color',
        'wp_estate_hover_button_color',
        'wp_estate_show_g_search',
        'wp_estate_show_adv_search_extended',
        'wp_estate_readsys',
        'wp_estate_map_max_pins',
        'wp_estate_ssl_map',
        'wp_estate_enable_stripe',
        'wp_estate_enable_paypal',
        'wp_estate_enable_direct_pay',
        'wp_estate_global_property_page_agent_sidebar',
        'wp_estate_global_prpg_slider_type',
        'wp_estate_global_prpg_content_type',
        'wp_estate_logo_margin',
        'wp_estate_header_transparent',
        'wp_estate_default_map_type',
        'wp_estate_prices_th_separator',
        'wp_estate_multi_curr',
        'wp_estate_date_lang',
        'wp_estate_blog_unit',
        'wp_estate_blog_unit_card',
        'wp_estate_enable_autocomplete',
        'wp_estate_visible_user_role_dropdown',
        'wp_estate_visible_user_role',
        'wp_estate_enable_user_pass',
        'wp_estate_auto_curency',
        'wp_estate_status_list',
        'wp_estate_custom_fields',
        'wp_estate_subject_password_reset_request',
        'wp_estate_password_reset_request',
        'wp_estate_subject_password_reseted',
        'wp_estate_password_reseted',
        'wp_estate_subject_purchase_activated',
        'wp_estate_purchase_activated',
        'wp_estate_subject_approved_listing',
        'wp_estate_approved_listing',
        'wp_estate_subject_new_wire_transfer',
        'wp_estate_new_wire_transfer',
        'wp_estate_subject_admin_new_wire_transfer',
        'wp_estate_admin_new_wire_transfer',
        'wp_estate_subject_admin_new_user',
        'wp_estate_admin_new_user',
        'wp_estate_subject_new_user',
        'wp_estate_new_user',
        'wp_estate_subject_admin_expired_listing',
        'wp_estate_admin_expired_listing',
        'wp_estate_subject_matching_submissions',
        'wp_estate_subject_paid_submissions',
        'wp_estate_paid_submissions',
        'wp_estate_subject_featured_submission',
        'wp_estate_featured_submission',
        'wp_estate_subject_account_downgraded',
        'wp_estate_account_downgraded',
        'wp_estate_subject_membership_cancelled',
        'wp_estate_membership_cancelled',
        'wp_estate_subject_downgrade_warning',
        'wp_estate_downgrade_warning',
        'wp_estate_subject_membership_activated',
        'wp_estate_membership_activated',
        'wp_estate_subject_free_listing_expired',
        'wp_estate_free_listing_expired',
        'wp_estate_subject_new_listing_submission',
        'wp_estate_new_listing_submission',
        'wp_estate_subject_payment_action_required',
        'wp_estate_payment_action_required',
        'wp_estate_subject_listing_edit',
        'wp_estate_listing_edit',
        'wp_estate_subject_recurring_payment',
        'wp_estate_subject_recurring_payment',
        'wp_estate_custom_css',
        'wp_estate_company_name',
        'wp_estate_telephone_no',
        'wp_estate_mobile_no',
        'wp_estate_fax_ac',
        'wp_estate_skype_ac',
        'wp_estate_co_address',
        'wp_estate_facebook_link',
        'wp_estate_twitter_link',
        'wp_estate_pinterest_link',
        'wp_estate_instagram_link',
        'wp_estate_linkedin_link',
        'wp_estate_contact_form_7_agent',
        'wp_estate_contact_form_7_contact',
        'wp_estate_global_revolution_slider',
        'wp_estate_repeat_footer_back',
        'wp_estate_prop_list_slider',
        'wp_estate_agent_sidebar',
        'wp_estate_agent_sidebar_name',
        'wp_estate_property_list_type',
        'wp_estate_property_list_type_adv',
        'wp_estate_prop_unit',
        'wp_estate_general_font',
        'wp_estate_headings_font_subsetxxx111',
        'wp_estate_copyright_message',
        'wp_estate_show_graph_prop_page',
        'wp_estate_map_style',
        'wp_estate_submission_curency_custom',
        'wp_estate_free_mem_list_unl',
        'wp_estate_adv_search_what',
        'wp_estate_adv_search_how',
        'wp_estate_adv_search_label',
        'wp_estate_show_save_search',
        'wp_estate_show_adv_search_slider',
        'wp_estate_show_adv_search_visible',
        'wp_estate_show_slider_price',
        'wp_estate_show_dropdowns',
        'wp_estate_show_slider_min_price',
        'wp_estate_show_slider_max_price',
        'wp_estate_adv_back_color',
        'wp_estate_adv_font_color',
        'wp_estate_show_no_features',
        'wp_estate_advanced_exteded',
        'wp_estate_adv_search_what',
        'wp_estate_adv_search_how',
        'wp_estate_adv_search_label',
        'wp_estate_adv_search_type',
        'wp_estate_property_adr_text',
        'wp_estate_property_features_text',
        'wp_estate_property_description_text',
        'wp_estate_property_details_text',
        'wp_estate_new_status',
        'wp_estate_status_list',
        'wp_estate_theme_slider',
        'wp_estate_slider_cycle',
        'wp_estate_use_mimify',
        'wp_estate_currency_label_main',
        'wp_estate_footer_background',
        'wp_estate_wide_footer',
        'wp_estate_show_footer',
        'wp_estate_show_footer_copy',
        'wp_estate_footer_type',
        'wp_estate_logo_header_type',
        'wp_estate_wide_header',
        'wp_estate_logo_header_align',
        'wp_estate_text_header_align',
        'wp_estate_general_country'
        );

    foreach($export_options as $option){
         $keys[]=$option;
    }

    return $keys;
}
add_filter( 'cei_export_option_keys', 'wpestate_my_export_option_keys' );


if ( function_exists('icl_object_id') ) {
    add_action( 'add_attachment', 'sync_menu_order', 100 );
    add_action( 'edit_attachment', 'sync_menu_order', 100 );
    function sync_menu_order( $post_ID ) {
            $post = get_post( $post_ID );
            $menu_order = $post->menu_order;
            $trid = apply_filters( 'wpml_element_trid', false, $post_ID, 'post_attachment' );
            $translations = apply_filters( 'wpml_get_element_translations', false, $trid, 'post_attachment' );
            $translated_ids = wp_list_pluck( $translations, 'element_id' );
            if ( $menu_order !== null && (bool) $translated_ids !== false ) {
                    global $wpdb;
                    $query = $wpdb->prepare(
                            "UPDATE {$wpdb->posts}
                               SET menu_order=%s
                               WHERE ID IN (" . wpml_prepare_in( $translated_ids, '%d' ) . ")",
                            $menu_order
                    );
                    $wpdb->query( $query );
            }
    }
}




add_filter('weglot_active_translation_before_treat_page', 'ajax_weglot_active_translation');

function ajax_weglot_active_translation(){
    if ( isset($_POST) && isset($_POST['action']) && ( $_POST['action'] === 'wpestate_ajax_check_booking_valability' || $_POST['action'] === 'wpestate_ajax_google_login_oauth' || $_POST['action'] === 'wpestate_ajax_facebook_login') ) {
        return false;
    }
    return true;

}






/** REMOVE REDUX MESSAGES */
function remove_redux_messages() {
	if(class_exists('ReduxFramework')){
		remove_action( 'admin_notices', array( get_redux_instance('theme_options'), '_admin_notices' ), 99);
	}
}

/** HOOK TO REMOVE REDUX MESSAGES */
add_action('init', 'remove_redux_messages');



if(!function_exists('wpresidence_body_classes')):
    function wpresidence_body_classes( $classes ) {
        global $post;
        
        $page_template='';
        $postID= '';
        if(isset($post->ID)){
           $page_template = get_post_meta( $post->ID, '_wp_page_template', true );
           $page_template = ($page_template);
           $postID= $post->ID;
        }

        $show_header_dashboard      =  wpresidence_get_option('wp_estate_show_header_dashboard','');
        if( isset($post->ID) && wpestate_half_map_conditions ($post->ID) ){
            $classes[] =" half_map_body ";
        }

        // add extra class if website has top bar
        if(esc_html ( wpresidence_get_option('wp_estate_show_top_bar_user_menu','') )=="yes"){
            $classes[] =" wpresidece_has_top_bar ";
        }


        // add extra class if header is boxed like
        $wide_header = wpresidence_get_option('wp_estate_wide_header', '');        
        if ($wide_header == 'no' && !is_404() &&
            $page_template !==  'page-templates/splash_page.php' && 
            !wpestate_half_map_conditions ($post->ID) && 
            !wpestate_is_user_dashboard() ) {
            $classes[] = " wpresidence_boxed_header ";
        }

 
        if( wpestate_is_user_dashboard() && $show_header_dashboard=='no'){
            $classes[] =" dash_no_header ";
        }

        if( wpestate_is_user_dashboard() ){
            $classes[] =" wpresidence_dashboard_body ";
        }

  
      

        $show_half_map_footer = wpresidence_get_option('wp_estate_property_list_type_footer', '');

        if(wpestate_half_map_conditions($postID)  )   { 
            if($show_half_map_footer=='yes'){
            $classes[] =" wpresidence_half_map_body_class_with_footer ";
            }else{
                  $classes[] =" wpresidence_half_map_body_class ";
            }

        }


        // logo alignment
        $wpestate_logo_header_align = wpresidence_get_option('wp_estate_logo_header_align', '');
        $classes[] ='wpresidence_header_logo_align_'.$wpestate_logo_header_align;


        // header type 3 alignment
        $wp_estate_header3_sidebar_header_align= wpresidence_get_option('wp_estate_header3_sidebar_header_align', '');
        $classes[] ='wpresidence_header3_logo_align_'.$wp_estate_header3_sidebar_header_align;



        //menu align for header type1
        $wp_estate_menu_header_align=   esc_html ( wpresidence_get_option('wp_estate_menu_header_align','') );
        $classes[] ='wpresidence_header_menu_type1_align_'.$wp_estate_menu_header_align;

        //text align for header type3 and header type4
        $text_header_align_select = wpresidence_get_option('wp_estate_text_header_align', '');
        $classes[] ='wpresidence_header_menu_type3_4_text_align_'.$text_header_align_select;

        
        //add guttenberg class
        if (function_exists('the_gutenberg_project') && has_blocks( get_the_ID() ) )
            $classes[] = 'using-gutenberg';
    

        // if we use wpresidence template
        $wp_estate_global_page_template               = intval  ( wpresidence_get_option('wp_estate_global_property_page_template') );
        $wp_estate_local_page_template = 0;
  
        if(isset($post->ID)){
            $wp_estate_local_page_template                = intval  ( get_post_meta($post->ID, 'property_page_design_local', true));
            if ( $wp_estate_local_page_template === 0 ) {
                $wp_estate_local_page_template            = intval  ( get_post_meta($post->ID, 'property_page_desing_local', true));
            }
        }
        if($wp_estate_global_page_template!=0 || $wp_estate_local_page_template!=0 ){
            $classes[] = 'using-wpresidence-template';
        }
        
        // Add sticky footer class if enabled
        $show_sticky_footer = wpresidence_get_option('wp_estate_show_sticky_footer', '');
        if ($show_sticky_footer === 'yes'     && ( !wpestate_half_map_conditions($postID)  )      ) {
            $classes[] = 'wpestate_has_sticky_footer';
        }


        return $classes;
    }
endif;
add_filter( 'body_class','wpresidence_body_classes' );

/**
 * Build the body data-postid attribute only when a valid post/page ID exists.
 *
 * @return string
 */
if ( ! function_exists( 'wpresidence_get_body_data_postid_attr' ) ) :
    function wpresidence_get_body_data_postid_attr() {
        // Step 1: Start with no ID and try to read it from the global post.
        global $post;
        $post_id = 0;

        if ( isset( $post->ID ) ) {
            $post_id = absint( $post->ID );
        }

        // Step 2: Fallback for singular views if global post is not available.
        if ( $post_id === 0 && is_singular() ) {
            $post_id = absint( get_queried_object_id() );
        }

        // Step 3: Return the attribute only when we have a valid ID.
        if ( $post_id > 0 ) {
            return ' data-postid="' . esc_attr( $post_id ) . '"';
        }

        return '';
    }
endif;




/*
 * 
 * Check license
 * 
 * 
 * 
 * */

add_action( 'wp_ajax_wpestate_check_license_function', 'wpestate_check_license_function' );

if( !function_exists('wpestate_check_license_function') ):
    function wpestate_check_license_function(){
        check_ajax_referer( 'wpestate_license_ajax_nonce',  'security' );

        if( !current_user_can('administrator') ){
            exit('out pls');
        }

        $wpestate_license_key = esc_html($_POST['wpestate_license_key']);
        $enquire_answer = wpestate_enquire_license($wpestate_license_key);

        if( isset($enquire_answer['permited']) && $enquire_answer['permited']==="yes" ){
            print 'ok';
        }else{
            print 'nook';
        }
        die(); 
    }
endif;

/*
 * 
 * Enquire about license 
 * 
 * 
 */



function wpestate_enquire_license($wpestate_license_key){
        $data= array(
                        'license'   =>  trim($wpestate_license_key),
                        'action'    =>  'wpestate_envato_lic'
                    );

        $args=array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'sslverify' => false,
                'blocking' => true,
                'body' =>  $data,
                'headers' => [
                      'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
                ],
        );


        $url="https://support.wpestate.org/theme_license_check_wpresidence_cloud.php";
        $response = wp_remote_post( $url, $args );

        if ( is_wp_error( $response ) ) {
	    $error_message = $response->get_error_message();
            die($error_message);
	} else {

            $output = json_decode(wp_remote_retrieve_body( $response ),true);

      
            
            if( isset($output['permited']) && $output['permited']=="yes" ){
               
                update_option('is_theme_activated','is_active');
                update_option('envato_purchase_code_7896392',trim($wpestate_license_key));
                set_transient('envato_purchase_code_7896392_demos',$output['demos'],6600);
                return $output;
            }else{
               return false;
            }

	}
}

/*
 * 
 * Show support link 
 * 
 * 
 */

if( !function_exists('show_support_link') ):
function show_support_link(){

    if(wpresidence_get_option('wp_estate_support','')=='yes'){

        if( is_front_page() || is_tax() ){
            print '<a class="wpestate_support_link" href="https://wpestate.org" target="_blank">WpEstate</a>';
        }
    }
}
endif;









/**
 * Process filter labels from post meta
 *
 * Retrieves post meta value and converts it to a formatted label and meta slug.
 * Handles 'all' values and empty/non-array cases by returning defaults.
 *
 * @param int    $post_id       Post ID to retrieve meta from
 * @param string $meta_key      Meta key to retrieve
 * @param string $default_label Default label text to use as fallback
 * @return array Associative array with 'label' and 'meta' keys
 */
function wpresidence_process_filter_labels($post_id, $meta_key, $default_label) {
    $filter_value = get_post_meta($post_id, $meta_key, true);
    
    // Check if value is 'all' or use default
    if (is_array($filter_value) && !empty($filter_value) && $filter_value[0] == 'all') {
        $label = esc_html__($default_label, 'wpresidence');
        $meta = $default_label;
    } elseif (is_array($filter_value) && !empty($filter_value)) {
        // Format the first array value as label and meta slug
        $label = ucwords(str_replace('-', ' ', $filter_value[0]));
        $meta = sanitize_title($filter_value[0]);
    } else {
        // Fallback to default for empty/invalid values
        $label = esc_html__($default_label, 'wpresidence');
        $meta = $default_label;
    }
    
    return ['label' => $label, 'meta' => $meta];
}




/*
 * 
 *  
 * 
 * 
 */
function wpresidence_register_elementor_locations( $elementor_theme_manager ) {

	$elementor_theme_manager->register_location( 'header' );
	$elementor_theme_manager->register_location( 'footer' );
  $elementor_theme_manager->register_location( 'single' );
  $elementor_theme_manager->register_location( 'archive' );

}
add_action( 'elementor/theme/register_locations', 'wpresidence_register_elementor_locations' );



function wpresidence_add_page_templates($templates) {
    $templates['page-templates/blog_list.php'] = 'Blog list page';
    $templates['page-templates/agents_list.php'] = 'Agents list';
    return $templates;
}
//add_filter('theme_page_templates', 'wpresidence_add_page_templates');



/**
 * Update template paths and ensure it runs only once.
 *
 * This function updates the template paths in the database and uses a version
 * check to ensure it only runs once when the template paths are updated.
 */
function wpresidence_50_run_template_update() {
    // Check if the update has already been run
    $current_version = get_option('wpresidence_template_update_version', '0');
    $target_version = '1.0'; // Increment this when you need to run the update again

    if (version_compare($current_version, $target_version, '>=')) {
      //  return; // Update has already been run
    }

    global $wpdb;
    $template_updates = array(
        'aag_search_results.php'              => 'page-templates/aag_search_results.php',
        'advanced_search_results.php'         => 'page-templates/advanced_search_results.php',
        'agents_list.php'                     => 'page-templates/agents_list.php',
        'agency_list.php'                     => 'page-templates/agency_list.php',
        'auser_dashboard_search_result.php'   => 'page-templates/auser_dashboard_search_result.php',
        'blog_list.php'                       => 'page-templates/blog_list.php',
        'compare_listings.php'                => 'page-templates/compare_listings.php',
        'contact_page.php'                    => 'page-templates/contact_page.php',
        'developers_list.php'                 => 'page-templates/developers_list.php',
        'front_property_submit.php'           => 'page-templates/front_property_submit.php',
        'gdpr_terms.php'                      => 'page-templates/gdpr_terms.php',
        'page_property_design.php'            => 'page-templates/page_property_design.php',
        'property_list.php'                   => 'page-templates/property_list.php',
        'property_list_directory.php'         => 'page-templates/property_list_directory.php',
        'property_list_half.php'              => 'page-templates/property_list_half.php',
        'splash_page.php'                     => 'page-templates/splash_page.php',
        'terms_conditions.php'                => 'page-templates/terms_conditions.php',
        'user_dashboard.php'                  => 'page-templates/user_dashboard.php',
        'user_dashboard_add.php'              => 'page-templates/user_dashboard_add.php',
        'user_dashboard_add_agent.php'        => 'page-templates/user_dashboard_add_agent.php',
        'user_dashboard_agent_list.php'       => 'page-templates/user_dashboard_agent_list.php',
        'user_dashboard_analytics.php'        => 'page-templates/user_dashboard_analytics.php',
        'user_dashboard_favorite.php'         => 'page-templates/user_dashboard_favorite.php',
        'user_dashboard_inbox.php'            => 'page-templates/user_dashboard_inbox.php',
        'user_dashboard_invoices.php'         => 'page-templates/user_dashboard_invoices.php',
        'user_dashboard_main.php'             => 'page-templates/user_dashboard_main.php',
        'user_dashboard_profile.php'          => 'page-templates/user_dashboard_profile.php',
        'user_dashboard_searches.php'         => 'page-templates/user_dashboard_searches.php'
    );

    foreach ($template_updates as $old_template => $new_template) {
        $wpdb->update(
            $wpdb->postmeta,
            array('meta_value' => $new_template),
            array('meta_key' => '_wp_page_template', 'meta_value' => $old_template)
        );
    }

    // Update the version in the database to prevent running again
    update_option('wpresidence_template_update_version', $target_version);
}

// Hook the function to run on init, but only in the admin area
add_action('admin_init', 'wpresidence_50_run_template_update');




function wpestate_initialize_custom_auth() {
    global $wpestate_custom_auth;
    $wpestate_custom_auth = new WpEstate_Custom_Auth();
}
add_action('plugins_loaded', 'wpestate_initialize_custom_auth');

// to prevent fatal erros on update
function wpestate_show_advanced_search_options_for_elementor(){

}
