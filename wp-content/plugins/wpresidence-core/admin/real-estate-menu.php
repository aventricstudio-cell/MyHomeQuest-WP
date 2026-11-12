<?php
/**
 * WpResidence Real Estate menu and submenus
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_menu', 'wpresidence_real_estate_menu' );
add_action( 'admin_menu', 'wpresidence_remove_default_cpt_menus', 999 );

/**
 * Registers the "WpResidence Real Estate" admin menu with submenus
 */
function wpresidence_real_estate_menu() {
    $branding = wpresidence_theme_branding();
    $icon     = wpresidence_get_theme_branding_logo_url();

    add_menu_page(
        esc_html__( 'Properties, Agents & More', 'wpresidence-core' ),
        esc_html__( 'Properties, Agents & More', 'wpresidence-core' ),
        'manage_options',
        'wpresidence-real-estate',
        '',
        $icon,
        4.1
    );

    // Properties list
    add_submenu_page(
        'wpresidence-real-estate',
        __( 'Properties', 'wpresidence-core' ),
        __( 'Properties', 'wpresidence-core' ),
        'manage_options',
        'edit.php?post_type=estate_property'
    );

        remove_submenu_page('wpresidence-real-estate', 'wpresidence-real-estate');

    // Add Property
    if (post_type_exists('estate_property')) {
        add_submenu_page(
            'wpresidence-real-estate',
            __( 'Add Property', 'wpresidence-core' ),
            __( 'Add Property', 'wpresidence-core' ),
            'manage_options',
            'post-new.php?post_type=estate_property'
        );
    }

    // Agents
    if (post_type_exists('estate_agent')) {
        add_submenu_page(
            'wpresidence-real-estate',
            __( 'Agents', 'wpresidence-core' ),
            __( 'Agents', 'wpresidence-core' ),
            'manage_options',
            'edit.php?post_type=estate_agent'
        );
    }

    // Agencies
    if (post_type_exists('estate_agency')) {
        add_submenu_page(
            'wpresidence-real-estate',
            __( 'Agencies', 'wpresidence-core' ),
            __( 'Agencies', 'wpresidence-core' ),
            'manage_options',
            'edit.php?post_type=estate_agency'
        );
    }

    // Developers
    if (post_type_exists('estate_developer')) {
        add_submenu_page(
            'wpresidence-real-estate',
            __( 'Developers', 'wpresidence-core' ),
            __( 'Developers', 'wpresidence-core' ),
            'manage_options',
            'edit.php?post_type=estate_developer'
        );
    }

    // Categories page
    add_submenu_page(
        'wpresidence-real-estate',
        __( 'Categories', 'wpresidence-core' ),
        __( 'Categories', 'wpresidence-core' ),
        'manage_options',
        'wpresidence-real-estate-categories',
        'wpresidence_real_estate_categories_page'
    );

    // Reviews
    if (post_type_exists('estate_review')) {
        add_submenu_page(
            'wpresidence-real-estate',
            __( 'Reviews', 'wpresidence-core' ),
            __( 'Reviews', 'wpresidence-core' ),
            'manage_options',
            'edit.php?post_type=estate_review'
        );
    }

    // Invoices
    if (post_type_exists('wpestate_invoice')) {
        add_submenu_page(
            'wpresidence-real-estate',
            __( 'Invoices', 'wpresidence-core' ),
            __( 'Invoices', 'wpresidence-core' ),
            'manage_options',
            'edit.php?post_type=wpestate_invoice'
        );
    }

    // Membership Packages
    if (post_type_exists('membership_package')) {
        add_submenu_page(
            'wpresidence-real-estate',
            __( 'Membership Packages', 'wpresidence-core' ),
            __( 'Membership Packages', 'wpresidence-core' ),
            'manage_options',
            'edit.php?post_type=membership_package'
        );
    }

    // Searches
    if (post_type_exists('wpestate_search')) {
        add_submenu_page(
            'wpresidence-real-estate',
            __( 'Searches', 'wpresidence-core' ),
            __( 'Searches', 'wpresidence-core' ),
            'manage_options',
            'edit.php?post_type=wpestate_search'
        );
    }

    // Messages
    if (post_type_exists('wpestate_message')) {
        add_submenu_page(
            'wpresidence-real-estate',
            __( 'Messages', 'wpresidence-core' ),
            __( 'Messages', 'wpresidence-core' ),
            'manage_options',
            'edit.php?post_type=wpestate_message'
        );
    }
}
add_action('admin_menu', 'add_custom_menu_class', 999);

function add_custom_menu_class() {
    global $menu;
    
    // Loop through menu items to find your specific menu
    foreach ($menu as $key => $menu_item) {
        // Check if this is your menu item by slug
        if (isset($menu_item[2]) && $menu_item[2] === 'wpresidence-real-estate') {
            // Add custom class to the menu item
            $menu[$key][4] = (isset($menu[$key][4]) ? $menu[$key][4] . ' ' : '') . 'wpresidence-real-estate-custom-class';
            break;
        }
    }
}



/**
 * Render the Categories page listing taxonomy links in four columns
 */
function wpresidence_real_estate_categories_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $all_tax = wpresidence_ptc_get_taxonomies();
    $columns = array(
        'estate_property'  => __( 'Property Taxonomies', 'wpresidence-core' ),
        'estate_agent'     => __( 'Agent Taxonomies', 'wpresidence-core' ),
        'estate_agency'    => __( 'Agency Taxonomies', 'wpresidence-core' ),
        'estate_developer' => __( 'Developer Taxonomies', 'wpresidence-core' ),
    );

    echo '<div class=" wpresidence2025-content-wrapper" style="margin-top:15px;">';
    echo '<div class="wpresidence2025-content-wrapper-header">' . esc_html__( 'Categories', 'wpresidence-core' ) . '</div><hr>';
    
    echo '<div class="wpresidence2025-content-wrapper-inside-box" >';
    echo '<div class="wpresidence2025-content-container">';
       
    
    echo '<div class="wpresidence-admin-categories-wrapper">';
        foreach ( $columns as $type => $label ) {
            // Check if the post type exists
            if ( !post_type_exists( $type ) ) {
                continue;
            }
            
            echo '<div style="flex:1 1 200px;min-width:200px;">';
            echo '<h2>' . esc_html( $label ) . '</h2>';
            if ( isset( $all_tax[ $type ] ) ) {
                echo '<ul>';
                foreach ( $all_tax[ $type ] as $slug => $name ) {
                    // Check if the taxonomy exists
                    if ( taxonomy_exists( $slug ) ) {
                        $link = admin_url( 'edit-tags.php?taxonomy=' . $slug . '&post_type=' . $type );
                        echo '<li><a href="' . esc_url( $link ) . '">' . esc_html( $name ) . '</a></li>';
                    }
                }
                echo '</ul>';
            }
            echo '</div>';
        }
   echo '</div>';   echo '</div>';
    echo '</div></div>';
}

/**
 * Remove the original menu entries for custom post types now shown
 * under the "WpResidence Real Estate" menu.
 */
function wpresidence_remove_default_cpt_menus() {
    remove_menu_page( 'edit.php?post_type=estate_property' );
    remove_menu_page( 'edit.php?post_type=estate_agent' );
    remove_menu_page( 'edit.php?post_type=estate_agency' );
    remove_menu_page( 'edit.php?post_type=estate_developer' );
    remove_menu_page( 'edit.php?post_type=estate_review' );
    remove_menu_page( 'edit.php?post_type=wpestate_invoice' );
    remove_menu_page( 'edit.php?post_type=membership_package' );
    remove_menu_page( 'edit.php?post_type=wpestate_search' );
    remove_menu_page( 'edit.php?post_type=wpestate_message' );
}
