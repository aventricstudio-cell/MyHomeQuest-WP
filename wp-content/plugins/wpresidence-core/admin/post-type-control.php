<?php
/**
 * WpResidence Settings - Post Type Control
 * 
 * This file handles the admin interface for enabling/disabling 
 * custom post types and taxonomies in the WpResidence theme.
 * 
 * @package WpResidence Core
 * @version 1.0.0
 */

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Hook into WordPress admin initialization
add_action( 'admin_menu', 'wpresidence_settings_menu' );      // Create admin menu pages
add_action( 'admin_init', 'wpresidence_ptc_save' );          // Handle form submissions
add_action( 'init', 'wpresidence_ptc_apply', 100 );          // Apply post type/taxonomy settings
add_action( 'admin_post_wpresidence_feedback_submit', 'wpresidence_ptc_handle_feedback' ); // Handle feedback submissions

require_once WPESTATE_PLUGIN_PATH . "admin/ptc-tabs/post-types.php";
require_once WPESTATE_PLUGIN_PATH . "admin/ptc-tabs/taxonomies.php";
require_once WPESTATE_PLUGIN_PATH . "admin/ptc-tabs/plugins.php";
require_once WPESTATE_PLUGIN_PATH . "admin/ptc-tabs/feedback.php";
require_once WPESTATE_PLUGIN_PATH . "admin/ptc-tabs/import-locations.php";
require_once WPESTATE_PLUGIN_PATH . "admin/ptc-tabs/white-label.php";
require_once WPESTATE_PLUGIN_PATH . "admin/ptc-tabs/license.php";

require_once WPESTATE_PLUGIN_PATH . "admin/translator/translator-functions.php";

/**
 * Create the main settings menu and submenu pages in WordPress admin
 * 
 * Adds a main "WpResidence Settings" menu item and a "Post Type Control" 
 * submenu page for managing which post types and taxonomies are active.
 */
function wpresidence_settings_menu() {
    // Create main menu page
    $wpresidence_branding = wpresidence_theme_branding(); // Uses the filter system
    //   WPESTATE_PLUGIN_DIR_URL . '/img/residence_icon.png', 
    $menu_icon = wpresidence_get_theme_branding_logo_url(); // Just returns the URL
 

    add_menu_page(
        $wpresidence_branding .' Site Settings',  // Page title
        $wpresidence_branding .' Site Settings', // Menu title
        'manage_options',                                           // Required capability
        'wpresidence-settings',                                     // Menu slug
        'wpresidence_settings_main_page',                          // Callback function
        $menu_icon , // Just returns the URL
        3                                                           // Menu position
    );

    add_submenu_page(
        'wpresidence-settings',
        __( 'Post Types', 'wpresidence-core' ),
        __( 'Post Types', 'wpresidence-core' ),
        'manage_options',
        'wpresidence-post-type-control',
        'wpresidence_ptc_page'
    );

    add_submenu_page(
        'wpresidence-settings',
        __( 'Taxonomies', 'wpresidence-core' ),
        __( 'Taxonomies', 'wpresidence-core' ),
        'manage_options',
        'wpresidence-post-type-control-taxonomies',
        'wpresidence_ptc_page_taxonomies'
    );
    // Keep the page accessible via direct URL but hide it from the menu.
    remove_submenu_page( 'wpresidence-settings', 'wpresidence-post-type-control-taxonomies' );

    add_submenu_page(
        'wpresidence-settings',
        __( 'Plugins', 'wpresidence-core' ),
        __( 'Plugins', 'wpresidence-core' ),
        'manage_options',
        'wpresidence-post-type-control-plugins',
        'wpresidence_ptc_page_plugins'
    );

    add_submenu_page(
        'wpresidence-settings',
        __( 'Feedback', 'wpresidence-core' ),
        __( 'Feedback', 'wpresidence-core' ),
        'manage_options',
        'wpresidence-post-type-control-feedback',
        'wpresidence_ptc_page_feedback'
    );

    add_submenu_page(
        'wpresidence-settings',
        __( 'Import Locations', 'wpresidence-core' ),
        __( 'Import Locations', 'wpresidence-core' ),
        'manage_options',
        'wpresidence-post-type-control-import-locations',
        'wpresidence_ptc_page_import_locations'
    );

    add_submenu_page(
        'wpresidence-settings',
        __( 'WpResidence License', 'wpresidence-core' ),
        __( 'WpResidence License', 'wpresidence-core' ),
        'manage_options',
        'wpresidence-post-type-control-license',
        'wpresidence_ptc_page_license'
    );

    // add_submenu_page(
    //     'wpresidence-settings',
    //     __( 'WpResidence Translator', 'wpresidence-core' ),
    //     __( 'WpResidence Translator', 'wpresidence-core' ),
    //     'manage_options',
    //     'wpresidence-post-type-control-translator',
    //     'wpresidence_ptc_page_translator'
    // );

  // Show white label menu UNLESS the constant is defined as true
    if ( ! ( defined( 'HIDE_WHITE_LABEL_ACCESS' ) && HIDE_WHITE_LABEL_ACCESS === true ) ) {
        add_submenu_page(
            'wpresidence-settings',
            __( 'White Label', 'wpresidence-core' ),
            __( 'White Label', 'wpresidence-core' ),
            'manage_options',
            'wpresidence-post-type-control-white-label',
            'wpresidence_ptc_page_white_label'
        );
        remove_submenu_page('wpresidence-settings','wpresidence-settings');
    }
    remove_submenu_page('wpresidence-settings','wpresidence-settings');
}

/**
 * Get all available custom post types for WpResidence
 * 
 * Returns an array of post type slugs and their human-readable labels.
 * These are the core post types used by the real estate theme.
 * 
 * @return array Associative array of post type slug => label pairs
 */
function wpresidence_ptc_get_post_types() {
    return array(
        'estate_property'   => __( 'Properties', 'wpresidence-core' ),        // Real estate listings
        'estate_agent'      => __( 'Agents', 'wpresidence-core' ),            // Real estate agents
        'estate_agency'     => __( 'Agencies', 'wpresidence-core' ),          // Real estate agencies
        'estate_developer'  => __( 'Developers', 'wpresidence-core' ),        // Property developers
        'wpestate_invoice'  => __( 'Invoices', 'wpresidence-core' ),          // Billing invoices
        'membership_package' => __( 'Membership Packages', 'wpresidence-core' ), // Subscription plans
        'wpestate_message'  => __( 'Messages', 'wpresidence-core' ),          // User messages
        'wpestate_search'   => __( 'Searches', 'wpresidence-core' ),          // Saved searches
          'estate_review'  => __( 'Reviews', 'wpresidence-core' ),          // Billing invoices
    );
}

/**
 * Get all available taxonomies organized by post type
 * 
 * Returns a multi-dimensional array where each post type has its associated
 * taxonomies. These are used for categorizing and organizing content.
 * 
 * @return array Multi-dimensional array of post_type => [taxonomy_slug => label]
 */
function wpresidence_ptc_get_taxonomies() {
    return array(
        // Taxonomies for property listings
        'estate_property' => array(
            'property_category'           => __( 'Property Categories', 'wpresidence-core' ),     // House, Apartment, etc.
            'property_action_category'    => __( 'Property Types', 'wpresidence-core' ),          // For Sale, For Rent, etc.
            'property_city'               => __( 'Property City', 'wpresidence-core' ),           // Geographic location
            'property_area'               => __( 'Property Area', 'wpresidence-core' ),           // Neighborhood/district
            'property_county_state'       => __( 'Property County/State', 'wpresidence-core' ),   // Administrative division
            'property_features'           => __( 'Property Features', 'wpresidence-core' ),       // Pool, Garage, etc.
            'property_status'             => __( 'Property Status', 'wpresidence-core' ),         // Available, Sold, etc.
        ),
        
        // Taxonomies for real estate agents
        'estate_agent' => array(
            'property_category_agent'        => __( 'Agent Categories', 'wpresidence-core' ),     // Agent specializations
            'property_action_category_agent' => __( 'Agent Action', 'wpresidence-core' ),         // Types of transactions
            'property_city_agent'            => __( 'Agent City', 'wpresidence-core' ),           // Agent's operating city
            'property_area_agent'            => __( 'Agent Area', 'wpresidence-core' ),           // Agent's service area
            'property_county_state_agent'    => __( 'Agent County/State', 'wpresidence-core' ),   // Agent's region
        ),
        
        // Taxonomies for real estate agencies
        'estate_agency' => array(
            'category_agency'        => __( 'Agency Category', 'wpresidence-core' ),              // Agency type/size
            'action_category_agency' => __( 'Agency Action', 'wpresidence-core' ),                // Agency services
            'city_agency'            => __( 'Agency City', 'wpresidence-core' ),                  // Agency location
            'area_agency'            => __( 'Agency Area', 'wpresidence-core' ),                  // Agency service area
            'county_state_agency'    => __( 'Agency County/State', 'wpresidence-core' ),          // Agency region
        ),
        
        // Taxonomies for property developers
        'estate_developer' => array(
            'property_category_developer'        => __( 'Developer Category', 'wpresidence-core' ),     // Developer type
            'property_action_developer'          => __( 'Developer Action', 'wpresidence-core' ),       // Development type
            'property_city_developer'            => __( 'Developer City', 'wpresidence-core' ),         // Developer location
            'property_area_developer'            => __( 'Developer Area', 'wpresidence-core' ),         // Development areas
            'property_county_state_developer'    => __( 'Developer County/State', 'wpresidence-core' ), // Developer region
        ),
    );
}

/**
 * Generate default options for post type control settings
 * 
 * Creates the initial configuration where all post types and taxonomies 
 * are enabled by default. This ensures the theme works out of the box.
 * 
 * @return array Default settings array with all items enabled
 */
function wpresidence_ptc_default_options() {
    // Initialize empty arrays for settings
    $defaults = array(
        'post_types'  => array(),
        'taxonomies'  => array(),
    );

    // Enable all post types by default
    $post_types = wpresidence_ptc_get_post_types();
    foreach ( $post_types as $slug => $label ) {
        $defaults['post_types'][ $slug ] = 1; // 1 = enabled
    }

    // Enable all taxonomies by default
    $taxonomies = wpresidence_ptc_get_taxonomies();
    foreach ( $taxonomies as $type => $taxes ) {
        foreach ( $taxes as $tax_slug => $tax_label ) {
            $defaults['taxonomies'][ $tax_slug ] = 1; // 1 = enabled
        }
    }

    return $defaults;
}

/**
 * Retrieve saved settings merged with defaults.
 *
 * Ensures that every setting exists and defaults to enabled so new options
 * added in future updates remain active unless explicitly disabled.
 *
 * @return array Settings array with defaults applied.
 */
function wpresidence_ptc_get_settings() {
    $defaults = wpresidence_ptc_default_options();
    $options  = get_option( 'wpresidence_ptc_settings', array() );

    if ( ! is_array( $options ) ) {
        $options = array();
    }

    $changed = false;

    foreach ( $defaults as $section => $section_defaults ) {
        if ( ! isset( $options[ $section ] ) || ! is_array( $options[ $section ] ) ) {
            $options[ $section ] = array();
            $changed             = true;
        }

        foreach ( $section_defaults as $key => $value ) {
            if ( ! array_key_exists( $key, $options[ $section ] ) ) {
                $options[ $section ][ $key ] = $value;
                $changed                     = true;
            }
        }
    }

    if ( $changed ) {
        update_option( 'wpresidence_ptc_settings', $options );
    }

    return $options;
}

/**
 * Check if a post type is enabled in settings.
 *
 * @param string $slug Post type slug
 * @return bool True if enabled
 */
function wpresidence_ptc_is_post_type_enabled( $slug ) {
    $options = wpresidence_ptc_get_settings();
    if ( isset( $options['post_types'][ $slug ] ) && ! $options['post_types'][ $slug ] ) {
        return false;
    }
    return true;
}

/**
 * Check if a taxonomy is enabled in settings.
 *
 * @param string $slug Taxonomy slug
 * @return bool True if enabled
 */
function wpresidence_ptc_is_taxonomy_enabled( $slug ) {
    $options = wpresidence_ptc_get_settings();
    if ( isset( $options['taxonomies'][ $slug ] ) && ! $options['taxonomies'][ $slug ] ) {
        return false;
    }
    return true;
}

/**
 * Handle form submission and save post type control settings
 * 
 * Processes the admin form data, validates the nonce for security,
 * and updates the WordPress options table with the new settings.
 */
function wpresidence_ptc_save() {
    // Security check: verify nonce and user capability
    if ( isset( $_POST['wpresidence_ptc_nonce'] ) && wp_verify_nonce( $_POST['wpresidence_ptc_nonce'], 'wpresidence_ptc_save' ) ) {
        
        // Get current options or defaults if none exist
        $options = wpresidence_ptc_get_settings();
        
        $current_tab = 'post-types';
        if ( ( isset( $_GET['page'] ) && 'wpresidence-post-type-control-taxonomies' === $_GET['page'] ) ||
            ( isset( $_GET['tab'] ) && 'taxonomies' === $_GET['tab'] ) ) {
            $current_tab = 'taxonomies';
        }

        if ( 'post-types' === $current_tab ) {
            $post_types = wpresidence_ptc_get_post_types();
            foreach ( $post_types as $slug => $label ) {
                $options['post_types'][ $slug ] = isset( $_POST['post_types'][ $slug ] );
            }
        }

        if ( 'taxonomies' === $current_tab ) {
            $taxonomies = wpresidence_ptc_get_taxonomies();
            foreach ( $taxonomies as $type => $taxes ) {
                foreach ( $taxes as $tax_slug => $tax_label ) {
                    $options['taxonomies'][ $tax_slug ] = isset( $_POST['taxonomies'][ $tax_slug ] );
                }
            }
        }

        // Save updated options to database
        update_option( 'wpresidence_ptc_settings', $options );
        flush_rewrite_rules();
        
        // Show success message to user
        add_settings_error( 'wpresidence_ptc', 'settings_updated', __( 'Settings saved.', 'wpresidence-core' ), 'updated' );
    }
}

/**
 * Render the main post type control admin page
 * 
 * Creates the tabbed interface for managing post types and taxonomies.
 * Handles different tabs and renders the appropriate content for each.
 */

function wpresidence_ptc_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $options    = wpresidence_ptc_get_settings();
    $post_types = wpresidence_ptc_get_post_types();
    $taxonomies = wpresidence_ptc_get_taxonomies();
    $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'post-types';

    settings_errors( 'wpresidence_ptc' );

    echo '<div class="wrap wpresidence2025-content-wrapper ">';
    echo '<div class="wpresidence2025-content-wrapper-header"> ' . esc_html__( 'WPResidence Site Settings', 'wpresidence-core' ) . '</div>';
    echo '<hr>';
    echo '<div class="wpresidence2025-content-wrapper-inside-box"> ';

        echo '<div class="wpresidence2025-content-container">';

            echo '<div class="wpresidence-nav-tab-wrapper nav-tab-wrapper">';
            echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control&tab=post-types' ) . '" class="nav-tab ' . ( $active_tab === 'post-types' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Post Types', 'wpresidence-core' ) . '</a>';
            $show_taxonomies_tab = apply_filters( 'wpresidence_ptc_show_taxonomies_tab', false );
            if ( $show_taxonomies_tab ) {
                echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control&tab=taxonomies' ) . '" class="nav-tab ' . ( $active_tab === 'taxonomies' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Taxonomies', 'wpresidence-core' ) . '</a>';
            }
            echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control&tab=plugins' ) . '" class="nav-tab ' . ( $active_tab === 'plugins' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Plugins', 'wpresidence-core' ) . '</a>';
            echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control&tab=feedback' ) . '" class="nav-tab ' . ( $active_tab === 'feedback' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Feedback', 'wpresidence-core' ) . '</a>';
            echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control&tab=import-locations' ) . '" class="nav-tab ' . ( $active_tab === 'import-locations' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Import Locations', 'wpresidence-core' ) . '</a>';
            echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control&tab=white-label' ) . '" class="nav-tab ' . ( $active_tab === 'white-label' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'White Label', 'wpresidence-core' ) . '</a>';
            echo '<a href="' . admin_url( 'admin.php?page=wpresidence-post-type-control-license&tab=license' ) . '" class="nav-tab ' . ( $active_tab === 'license' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'WpResidence License', 'wpresidence-core' ) . '</a>';
            echo '</div>';

            echo '<hr>';

            if ( in_array( $active_tab, array( 'post-types', 'taxonomies' ), true ) ) {
                echo '<form method="post">';
                wp_nonce_field( 'wpresidence_ptc_save', 'wpresidence_ptc_nonce' );
                if ( 'taxonomies' === $active_tab ) {
                    wpresidence_ptc_render_taxonomies_tab( $taxonomies, $post_types, $options );
                } else {
                    wpresidence_ptc_render_post_types_tab( $post_types, $options );
                }
              submit_button(
                esc_html__( 'Save Changes', 'wpresidence-core' ),   // $text
                'primary wpresidence_button',             // $type — built-in + your class
                'submit',                                  // $name
                true,                                      // $wrap
                array( 'id' => 'submit' )                  // $other_attributes — only use for id, tabindex, etc.
                );



                echo '</form>';
            } elseif ( 'plugins' === $active_tab ) {
                wpresidence_ptc_render_plugins_tab();
            } elseif ( 'feedback' === $active_tab ) {
                wpresidence_ptc_render_feedback_tab();
            } elseif ( 'import-locations' === $active_tab ) {
                wpresidence_ptc_render_import_locations_tab();
            } elseif ( 'white-label' === $active_tab ) {
                wpresidence_ptc_render_white_label_tab();
            } elseif ( 'license' === $active_tab ) {
                wpresidence_ptc_render_license_tab();
            }

        echo '</div>';
    echo '</div>';
    echo '</div>';
}

/** Wrapper for Taxonomies tab */
function wpresidence_ptc_page_taxonomies() {
    $_GET['tab'] = 'taxonomies';
    wpresidence_ptc_page();
}

/** Wrapper for Plugins tab */
function wpresidence_ptc_page_plugins() {
    $_GET['tab'] = 'plugins';
    wpresidence_ptc_page();
}

/** Wrapper for Feedback tab */
function wpresidence_ptc_page_feedback() {
    $_GET['tab'] = 'feedback';
    wpresidence_ptc_page();
}

/** Wrapper for Import Locations tab */
function wpresidence_ptc_page_import_locations() {
    $_GET['tab'] = 'import-locations';
    wpresidence_ptc_page();
}

/** Wrapper for White Label tab */
function wpresidence_ptc_page_white_label() {
    $_GET['tab'] = 'white-label';
    wpresidence_ptc_page();
}

/** Wrapper for License tab */
function wpresidence_ptc_page_license() {
    $_GET['tab'] = 'license';
    wpresidence_ptc_page();
}

/**
 * Apply the post type and taxonomy settings by unregistering disabled items
 * 
 * This function runs on the 'init' hook and unregisters any post types or 
 * taxonomies that have been disabled in the admin settings. This effectively
 * removes them from the WordPress admin and frontend.
 * 
 * Note: unregister_post_type() and unregister_taxonomy() were added in WP 4.5
 */
function wpresidence_ptc_apply() {
    // Ensure settings stay in sync with defaults so new items are enabled automatically.
    wpresidence_ptc_get_settings();

    // Post types and taxonomies are now registered conditionally, so nothing to do here.
}

/**
 * Render the main settings page (parent menu page)
 * 
 * This is a simple landing page that directs users to choose from
 * the available submenu options. Currently just shows basic info.
 */
function wpresidence_settings_main_page() {
    // Security check: ensure user has proper permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Simple main page with basic information
    echo '<div class="wrap">';
    echo '<h1>' . esc_html__( 'WpResidence Settings', 'wpresidence-core' ) . '</h1>';
    echo '<p>' . esc_html__( 'Select an option from the submenu.', 'wpresidence-core' ) . '</p>';
    echo '</div>';
}
