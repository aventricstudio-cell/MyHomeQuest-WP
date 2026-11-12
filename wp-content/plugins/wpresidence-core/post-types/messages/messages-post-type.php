<?php
/**
 * WpResidence Message System - Post Type Registration
 *
 * This file handles the registration and configuration of the custom post type
 * 'wpestate_message' used by the WpResidence messaging system. It defines the
 * post type properties, labels, capabilities, and admin UI modifications.
 *
 * The file accomplishes two main tasks:
 * 1. Registers the 'wpestate_message' custom post type with appropriate settings
 * 2. Modifies the WordPress admin menu to hide the "Add New" option for messages
 *    (since messages should typically be created through the front-end or programmatically)
 *
 * @package    WpResidence
 * @subpackage Messaging
 * @version    1.0
 * @author     WpResidence
 */

/**
 * Removes the "Add New" submenu item for the wpestate_message post type
 *
 * This function modifies the WordPress admin menu to hide the default "Add New"
 * option for the messages post type. This is done because messages should typically
 * be created through the application logic rather than manually by administrators.
 * The function uses the global $submenu array to unset the specific menu item.
 *
 * @global array $submenu The WordPress admin submenu global array
 * @return void
 */
function wpestate_hide_add_new_wpestate_message()
{
    global $submenu;
    // replace my_type with the name of your post type
    unset($submenu['edit.php?post_type=wpestate_message'][10]);
}
add_action('admin_menu', 'wpestate_hide_add_new_wpestate_message');

/**
 * Registers the 'wpestate_message' custom post type
 *
 * This function creates and configures the custom post type used for the messaging
 * system. It defines all post type properties including:
 * - UI labels for various contexts
 * - Visibility and access settings
 * - URL rewrite rules
 * - Supported features (title, editor)
 * - Custom icon
 * - Metabox callback association
 *
 * The post type is designed to store message content and metadata while providing
 * an appropriate admin interface for message management.
 *
 * @uses register_post_type() WordPress function to register custom post types
 * @uses esc_html__() For internationalization of UI text
 * @uses WPESTATE_PLUGIN_DIR_URL Constant for plugin directory URL
 * @return void
 */
// register the custom post type
add_action( 'init', 'wpestate_create_message_type' );
if( !function_exists('wpestate_create_message_type') ):
function wpestate_create_message_type() {
        if ( function_exists( 'wpresidence_ptc_is_post_type_enabled' ) && ! wpresidence_ptc_is_post_type_enabled( 'wpestate_message' ) ) {
            return;
        }

        $labels =  array(
                'name'          => esc_html__(  'Messages','wpresidence-core'),
                'singular_name' => esc_html__(  'Message','wpresidence-core'),
                'add_new'       => esc_html__( 'Add New Message','wpresidence-core'),
                'add_new_item'          =>  esc_html__( 'Add Message','wpresidence-core'),
                'edit'                  =>  esc_html__( 'Edit' ,'wpresidence-core'),
                'edit_item'             =>  esc_html__( 'Edit Message','wpresidence-core'),
                'new_item'              =>  esc_html__( 'New Message','wpresidence-core'),
                'view'                  =>  esc_html__( 'View','wpresidence-core'),
                'view_item'             =>  esc_html__( 'View Message','wpresidence-core'),
                'search_items'          =>  esc_html__( 'Search Message','wpresidence-core'),
                'not_found'             =>  esc_html__( 'No Message found','wpresidence-core'),
                'not_found_in_trash'    =>  esc_html__( 'No Message found','wpresidence-core'),
                'parent'                =>  esc_html__( 'Parent Message','wpresidence-core')
        );

        
        // Setup post type capabilities
        $message_capabilities = array(
                'edit_post'              => 'edit_wpestate_message',
                'read_post'              => 'read_wpestate_message',
                'delete_post'            => 'delete_wpestate_message',
                'edit_posts'             => 'edit_wpestate_messages',
                'edit_others_posts'      => 'edit_others_wpestate_messages',
                'publish_posts'          => 'publish_wpestate_messages',
                'read_private_posts'     => 'read_private_wpestate_messages',
                'create_posts'           => 'create_wpestate_messages',
                'delete_posts'           => 'delete_wpestate_messages',
                'delete_private_posts'   => 'delete_private_wpestate_messages',
                'delete_published_posts' => 'delete_published_wpestate_messages',
                'delete_others_posts'    => 'delete_others_wpestate_messages',
                'edit_private_posts'     => 'edit_private_wpestate_messages',
                'edit_published_posts'   => 'edit_published_wpestate_messages',
        );

        register_post_type( 'wpestate_message',
                array(
                'labels' =>$labels,
                'public' => true,       // Makes the post type publicly accessible
                'has_archive' => true,  // Enables post type archives
                'rewrite' => array('slug' => 'message'), // Sets custom URL structure
                'supports' => array('title', 'editor'),  // Basic WordPress features supported
                'can_export' => true,   // Allows exporting via WordPress tools
                'register_meta_box_cb' => 'wpestate_add_message_metaboxes', // Links to metabox registration function
                'menu_icon'=> WPESTATE_PLUGIN_DIR_URL.'/img/message.png',  // Custom admin menu icon
                'exclude_from_search'   => true , // Prevents messages from appearing in site searches,
                'map_meta_cap' => true,
                'capability_type' => array('wpestate_message', 'wpestate_messages'),
                'capabilities' => $message_capabilities
                )
        );
}
endif; // end   wpestate_message



/**
 * Ensures proper capabilities are set for the administrator role
 * This function runs early in the WordPress init process to set up required permissions
 * for managing messages
 *
 * @since 4.0.0
 * @return void
 */
add_action('init', 'wpestate_ensure_message_caps', 0);
if (!function_exists('wpestate_ensure_message_caps')):
    function wpestate_ensure_message_caps() {
        $admin = get_role('administrator');
        if ($admin) {
            // Add message editing capabilities
            $admin->add_cap('edit_wpestate_message');
            $admin->add_cap('read_wpestate_message');
            $admin->add_cap('delete_wpestate_message');
            $admin->add_cap('edit_wpestate_messages');
            $admin->add_cap('edit_others_wpestate_messages');
            $admin->add_cap('publish_wpestate_messages');
            $admin->add_cap('read_private_wpestate_messages');
            $admin->add_cap('create_wpestate_messages');
            $admin->add_cap('delete_wpestate_messages');
            $admin->add_cap('delete_private_wpestate_messages');
            $admin->add_cap('delete_published_wpestate_messages');
            $admin->add_cap('delete_others_wpestate_messages');
            $admin->add_cap('edit_private_wpestate_messages');
            $admin->add_cap('edit_published_wpestate_messages');
            
            // Add additional message management capabilities
            $admin->add_cap('manage_wpestate_messages');
            $admin->add_cap('view_wpestate_messages');
            $admin->add_cap('moderate_wpestate_messages');
        }
    }
endif;