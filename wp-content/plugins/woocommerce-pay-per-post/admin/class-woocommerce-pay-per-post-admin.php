<?php

use PRAMADILLO\Woocommerce_Pay_Per_Post_Restrict_Content;
use PRAMADILLO\INTEGRATIONS\Elementor;
use PRAMADILLO\INTEGRATIONS\PaidMembershipsPro;
use PRAMADILLO\INTEGRATIONS\WooCommerceMemberships;
use PRAMADILLO\INTEGRATIONS\WooCommerceSubscriptions;
class Woocommerce_Pay_Per_Post_Admin {
    public $integrations;

    private $allowed_restriction_frequency = [
        'minute',
        'hour',
        'day',
        'week',
        'month',
        'year'
    ];

    public function __construct() {
        // Integrations are initialized in init_integrations() which is called from admin_init
        // This ensures other plugins (WooCommerce Subscriptions, Memberships, etc.) are fully loaded
    }

    /**
     * Initialize integrations after all plugins are loaded.
     * Called from admin_init hook to ensure WooCommerce extensions are available.
     */
    public function init_integrations() {
        // Only initialize once
        if ( $this->integrations !== null ) {
            return;
        }
        $this->integrations = [];
    }

    /** @noinspection PhpUnused */
    public function admin_init() {
        //Register Options
        $this->options_init();
        //Load Block Editor
        $this->load_block_editor();
    }

    public function load_block_editor() {
        if ( get_option( WC_PPP_SLUG . '_block_editor_enabled', true ) ) {
            if ( Woocommerce_Pay_Per_Post_Helper::allowed_roles_for_meta_box() ) {
                $this->check_requirements_for_selected_custom_post_types();
                $this->wp_editor_integration_register_post_meta();
                add_action( 'enqueue_block_editor_assets', [$this, 'enqueue_wp_editor_integration_scripts'] );
            }
        }
    }

    public function check_requirements_for_selected_custom_post_types() {
        $failed = [];
        foreach ( $this->get_post_types() as $post_type ) {
            if ( false === post_type_supports( $post_type, 'custom-fields' ) ) {
                $failed[] = $post_type;
            }
        }
        if ( count( $failed ) > 0 ) {
            $message = 'In order to utilize the Block Editor and Pay for Post on the <code>' . implode( ', ', $failed ) . '</code> custom post type(s), you must enable <a href="https://developer.wordpress.org/reference/functions/add_post_type_support/" target="_blank">custom field support</a> for the custom post types or utilize disable Block Editor Integration in the <a href="' . admin_url() . '/admin.php?page=wc_pay_per_post-settings">Settings</a>';
            add_action( 'admin_notices', function () use($message) {
                echo '<div class="notice notice-error is-dismissible"><p>' . $message . '</p></div>';
            } );
        }
    }

    public function edit_post_block_editor( $post_id ) {
        $ppp_document_settings_meta = get_post_meta( $post_id, '_ppp_document_settings_meta', true );
        $ppp_document_settings_meta = ( !empty( $ppp_document_settings_meta ) ? json_decode( $ppp_document_settings_meta ) : [] );
        $ppp_document_protected_ids = $ppp_document_settings_meta->product_ids ?? [];
        $protected_ids = [];
        foreach ( $ppp_document_protected_ids as $data ) {
            $protected_ids[] = $data->value;
        }
        $protected_ids = $this->sanitize_product_ids( $protected_ids );
        update_post_meta( $post_id, WC_PPP_SLUG . '_product_ids', $protected_ids );
    }

    public function enqueue_wp_editor_integration_scripts() {
        if ( 'post' === WP_Screen::get()->base ) {
            global $post;
            $id = $post->ID;
            $selected_product_ids = get_post_meta( $id, WC_PPP_SLUG . '_product_ids', true );
            $delay_restriction_enable = get_post_meta( $id, WC_PPP_SLUG . '_delay_restriction_enable', true );
            $delay_restriction = get_post_meta( $id, WC_PPP_SLUG . '_delay_restriction', true );
            $delay_restriction_frequency = get_post_meta( $id, WC_PPP_SLUG . '_delay_restriction_frequency', true );
            $page_view_restriction_enable = get_post_meta( $id, WC_PPP_SLUG . '_page_view_restriction_enable', true );
            $page_view_restriction = get_post_meta( $id, WC_PPP_SLUG . '_page_view_restriction', true );
            $page_view_restriction_frequency = get_post_meta( $id, WC_PPP_SLUG . '_page_view_restriction_frequency', true );
            $page_view_restriction_enable_time_frame = get_post_meta( $id, WC_PPP_SLUG . '_page_view_restriction_enable_time_frame', true );
            $page_view_restriction_time_frame = get_post_meta( $id, WC_PPP_SLUG . '_page_view_restriction_time_frame', true );
            $expire_restriction_enable = get_post_meta( $id, WC_PPP_SLUG . '_expire_restriction_enable', true );
            $expire_restriction = get_post_meta( $id, WC_PPP_SLUG . '_expire_restriction', true );
            $expire_restriction_frequency = get_post_meta( $id, WC_PPP_SLUG . '_expire_restriction_frequency', true );
            $show_warnings = get_post_meta( $id, WC_PPP_SLUG . '_show_warnings', true );
            $wc_products = Woocommerce_Pay_Per_Post_Helper::get_products();
            $productIds = [];
            $productTitles = [];
            foreach ( $wc_products as $product ) {
                $productIds[] = (string) $product['ID'];
                $productTitles[] = $product['post_title'] . ' - [#' . $product['ID'] . ']';
            }
            $wcppp_freemius_upgrade_url = ( wcppp_freemius()->is_not_paying() && !wcppp_freemius()->is_trial() ? wcppp_freemius()->get_upgrade_url() : '' );
            $can_use_premium_code = wcppp_freemius()->is__premium_only() && wcppp_freemius()->can_use_premium_code();
            wp_enqueue_script(
                WC_PPP_SLUG . '_wp-editor-integration-js',
                plugin_dir_url( __FILE__ ) . 'wp-editor-integration/build/index.js',
                null,
                null,
                true
            );
            wp_enqueue_style( WC_PPP_SLUG . '_admin' );
            wp_localize_script( WC_PPP_SLUG . '_wp-editor-integration-js', 'wpEditorIntegrationObj', [
                'selected_product_ids'                    => $selected_product_ids,
                'productIds'                              => $productIds,
                'productTitles'                           => $productTitles,
                'adminUrl'                                => get_admin_url(),
                'wcppp_freemius_upgrade_url'              => $wcppp_freemius_upgrade_url,
                'upgrade_url_image_base'                  => plugin_dir_url( __DIR__ ) . 'admin/img/',
                'can_use_premium_code'                    => $can_use_premium_code,
                'delay_restriction_enable'                => $delay_restriction_enable,
                'delay_restriction'                       => $delay_restriction,
                'delay_restriction_frequency'             => $delay_restriction_frequency,
                'page_view_restriction_enable'            => $page_view_restriction_enable,
                'page_view_restriction'                   => $page_view_restriction,
                'page_view_restriction_frequency'         => $page_view_restriction_frequency,
                'page_view_restriction_enable_time_frame' => $page_view_restriction_enable_time_frame,
                'page_view_restriction_time_frame'        => $page_view_restriction_time_frame,
                'expire_restriction_enable'               => $expire_restriction_enable,
                'expire_restriction'                      => $expire_restriction,
                'expire_restriction_frequency'            => $expire_restriction_frequency,
                'show_warnings'                           => $show_warnings,
                'post_types'                              => Woocommerce_Pay_Per_Post_Helper::get_post_types(),
            ] );
        }
    }

    public function wp_editor_integration_register_post_meta() {
        register_meta( 'post', '_ppp_document_settings_meta', [
            'auth_callback' => '__return_true',
            'default'       => '',
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
        ] );
    }

    /** @noinspection PhpUnused */
    public function add_plugin_options() {
        global $wp_version;
        add_menu_page(
            __( 'Pay For Post with WooCommerce', 'wc_pay_per_post' ),
            'Pay For Post',
            'manage_options',
            WC_PPP_SLUG,
            [$this, 'create_options_page'],
            'dashicons-cart',
            99
        );
        add_submenu_page(
            WC_PPP_SLUG,
            'Settings',
            'Settings',
            'manage_options',
            WC_PPP_SLUG . '-settings',
            [$this, 'create_options_page']
        );
        add_submenu_page(
            WC_PPP_SLUG,
            'What\'s New',
            'What\'s New',
            'manage_options',
            WC_PPP_SLUG . '-whats-new',
            [$this, 'create_whatsnew_page']
        );
        add_submenu_page(
            WC_PPP_SLUG,
            'Getting Started with Pay For Post with WooCommerce',
            'Getting Started',
            'manage_options',
            WC_PPP_SLUG . '-getting-started',
            [$this, 'create_getting_started_page']
        );
        add_submenu_page(
            WC_PPP_SLUG,
            'Pay For Post with WooCommerce Documentation',
            'Documentation',
            'manage_options',
            WC_PPP_SLUG . '-help',
            [$this, 'create_help_page']
        );
        if ( version_compare( $wp_version, '5.2.0', '>=' ) ) {
            add_submenu_page(
                WC_PPP_SLUG,
                'Debug',
                'Debug',
                'manage_options',
                WC_PPP_SLUG . '-debug',
                [$this, 'create_debug_page']
            );
        }
        if ( version_compare( $wp_version, '5.2.0', '>=' ) ) {
            add_submenu_page(
                WC_PPP_SLUG,
                'Tools',
                'Tools',
                'manage_options',
                WC_PPP_SLUG . '-tools',
                [$this, 'create_tools_page']
            );
        }
        add_submenu_page(
            WC_PPP_SLUG,
            '',
            '<span class="pramadillo-admin-separator-container"><span class="pramadillo-admin-separator-title">Account Info</span><span class="pramadillo-admin-separator"></span></span>',
            'manage_options',
            '#'
        );
        remove_submenu_page( WC_PPP_SLUG, WC_PPP_SLUG );
    }

    /** @noinspection PhpUnused */
    public function enqueue_scripts() {
        wp_register_script(
            WC_PPP_SLUG . '_admin',
            plugin_dir_url( __FILE__ ) . 'js/wc-ppp-admin.js',
            ['jquery'],
            WC_PPP_VERSION
        );
        wp_register_script(
            WC_PPP_SLUG . '_select2',
            plugin_dir_url( __FILE__ ) . 'js/select2.min.js',
            [],
            '4.0.6'
        );
    }

    /** @noinspection PhpUnused */
    public function enqueue_styles() {
        wp_register_style(
            WC_PPP_SLUG . '_admin',
            plugin_dir_url( __FILE__ ) . 'css/wc-ppp-admin.css',
            [],
            WC_PPP_VERSION
        );
        wp_register_style(
            WC_PPP_SLUG . '_select2',
            plugin_dir_url( __FILE__ ) . 'css/select2.min.css',
            [],
            WC_PPP_VERSION
        );
    }

    /** @noinspection PhpUnused */
    public function admin_menu_separator_styles() {
        ?>
        <style>
            .pramadillo-admin-separator-container {
                display: flex;
                height: 12px;
                align-items: center;
                margin: 0 -10px 0 0;
            }

            .pramadillo-admin-separator-container .pramadillo-admin-separator-title {
                font-size: .68em;
                text-transform: uppercase;
                font-weight: 700;
                margin-right: 10px;
                color: hsla(0, 0%, 100%, .25);
            }

            .pramadillo-admin-separator-container .pramadillo-admin-separator {
                display: block;
                flex: 1;
                padding: 0;
                height: 1px;
                line-height: 1px;
                background: hsla(0, 0%, 100%, .125);
            }
        </style>
		<?php 
    }

    public function ajax_post_types() : array {
        $post_types = [];
        foreach ( get_post_types( [
            'public' => true,
        ] ) as $post_type ) {
            $post_types[] = $post_type;
        }
        return $post_types;
    }

    public function options_init() {
        register_setting( WC_PPP_SLUG . '_settings', WC_PPP_SLUG . '_restricted_content_default' );
        register_setting( WC_PPP_SLUG . '_settings', WC_PPP_SLUG . '_custom_post_types' );
        register_setting( WC_PPP_SLUG . '_settings', WC_PPP_SLUG . '_only_show_virtual_products' );
        register_setting( WC_PPP_SLUG . '_settings', WC_PPP_SLUG . '_turn_off_comments_when_protected' );
        register_setting( WC_PPP_SLUG . '_settings', WC_PPP_SLUG . '_allow_admins_access_to_protected_posts' );
        register_setting( WC_PPP_SLUG . '_settings', WC_PPP_SLUG . '_enable_debugging' );
        register_setting( WC_PPP_SLUG . '_settings', WC_PPP_SLUG . '_delete_settings' );
        register_setting( WC_PPP_SLUG . '_settings', WC_PPP_SLUG . '_block_editor_enabled', [
            'type'    => 'boolean',
            'default' => 1,
        ] );
    }

    public function create_options_page() {
        $restricted_content_default = get_option( WC_PPP_SLUG . '_restricted_content_default', _x( Woocommerce_Pay_Per_Post_Restrict_Content::RESTRICT_CONTENT_DEFAULT_MESSAGE, 'Default restricted content', 'wc_pay_per_post' ) );
        $custom_post_types = get_option( WC_PPP_SLUG . '_custom_post_types', [] );
        $custom_post_types = ( empty( $custom_post_types ) ? [] : $custom_post_types );
        if ( !is_array( $custom_post_types ) ) {
            $custom_post_types = explode( ',', $custom_post_types );
        }
        $turn_off_comments_when_protected = get_option( WC_PPP_SLUG . '_turn_off_comments_when_protected', true );
        $allow_admins_access_to_protected_posts = (bool) get_option( WC_PPP_SLUG . '_allow_admins_access_to_protected_posts', false );
        $enable_debugging = (bool) get_option( WC_PPP_SLUG . '_enable_debugging', false );
        $delete_settings = (bool) get_option( WC_PPP_SLUG . '_delete_settings', false );
        $available_post_types = $this->ajax_post_types();
        $only_show_virtual_products = (bool) get_option( WC_PPP_SLUG . '_only_show_virtual_products', false );
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( esc_html( 'You do not have sufficient permissions to access this page.' ) );
        }
        wp_enqueue_style( WC_PPP_SLUG . '_admin' );
        wp_enqueue_style( WC_PPP_SLUG . '_select2' );
        wp_enqueue_script( WC_PPP_SLUG . '_select2' );
        wp_enqueue_script( WC_PPP_SLUG . '_admin' );
        //Delete Log Functionality
        if ( isset( $_GET['delete_log_nonce'] ) && wp_verify_nonce( $_GET['delete_log_nonce'], 'delete_log' ) ) {
            $log = new Woocommerce_Pay_Per_Post_Logger();
            $log->delete_log_file();
            //echo '<script>alert("Log File Deleted");</script>';
            wp_safe_redirect( admin_url( 'options-general.php?page=' . WC_PPP_SLUG ) );
        }
        require_once plugin_dir_path( __FILE__ ) . 'partials/settings.php';
    }

    public function create_help_page() {
        wp_enqueue_style( WC_PPP_SLUG . '_admin' );
        wp_enqueue_style( WC_PPP_SLUG . '_select2' );
        wp_enqueue_script( WC_PPP_SLUG . '_select2' );
        wp_enqueue_script( WC_PPP_SLUG . '_admin' );
        require_once plugin_dir_path( __FILE__ ) . 'partials/help.php';
    }

    public function create_protected_content_page() {
        if ( is_admin() ) {
            wp_enqueue_style( WC_PPP_SLUG . '_admin' );
            wp_enqueue_script( WC_PPP_SLUG . '_dataTables' );
            wp_enqueue_script( WC_PPP_SLUG . '_dataTables_content' );
            wp_enqueue_style( WC_PPP_SLUG . '_dataTables' );
            $posts = Woocommerce_Pay_Per_Post_Helper::get_protected_posts();
            $data = [];
            Woocommerce_Pay_Per_Post_Helper::logger( 'create_protected_content_page() about to loop through posts.' );
            foreach ( $posts as $post ) {
                Woocommerce_Pay_Per_Post_Helper::logger( 'Checking Post - ' . $post->ID . ' for products.' );
                $users = [];
                $get_products = $this->is_protected( $post->ID );
                $products = $get_products['product_ids'];
                Woocommerce_Pay_Per_Post_Helper::logger( 'Products - ' . print_r( $products, true ) );
                $data[$post->ID] = [
                    'title'           => $post->post_title,
                    'products'        => $products,
                    'protection_type' => $get_products['protection_type'],
                    'origin_type'     => $get_products['origin_type'],
                ];
                foreach ( $products as $product ) {
                    if ( !empty( $product ) ) {
                        $users[] = $this->get_users_by_product_id( $product );
                    }
                }
                $data[$post->ID]['users'] = call_user_func_array( 'array_merge', $users );
            }
            require_once plugin_dir_path( __FILE__ ) . 'partials/protected-content.php';
        }
    }

    /** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection
     */
    protected function get_users_by_product_id( $product_id ) : array {
        if ( empty( $product_id ) ) {
            return [];
        }
        global $wpdb;
        $statuses = array_map( 'esc_sql', wc_get_is_paid_statuses() );
        /** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */
        /** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */
        return $wpdb->get_col( "\n           SELECT DISTINCT pm.meta_value FROM {$wpdb->posts} AS p\n           INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id\n           INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON p.ID = i.order_id\n           INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id\n           WHERE p.post_status IN ( 'wc-" . implode( "','wc-", $statuses ) . "' )\n           AND pm.meta_key IN ( '_billing_email' )\n           AND im.meta_key IN ( '_product_id', '_variation_id' )\n           AND im.meta_value = {$product_id}\n        " );
    }

    public function create_debug_page() {
        wp_enqueue_style( WC_PPP_SLUG . '_admin' );
        $enable_debugging = (bool) get_option( WC_PPP_SLUG . '_enable_debugging', false );
        $transients = Woocommerce_Pay_Per_Post_Debug::get_transients();
        if ( isset( $_POST['wc_ppp_create_table_nonce'] ) && wp_verify_nonce( $_POST['wc_ppp_create_table_nonce'], 'wc_ppp_create_table' ) ) {
            require_once WC_PPP_PATH . '/includes/class-woocommerce-pay-per-post-activator.php';
            Woocommerce_Pay_Per_Post_Activator::create_table__premium_only();
        }
        if ( isset( $_POST['wc_ppp_delete_transients_nonce'] ) && wp_verify_nonce( $_POST['wc_ppp_delete_transients_nonce'], 'wc_ppp_delete_transients' ) ) {
            Woocommerce_Pay_Per_Post_Debug::delete_transients();
        }
        require_once plugin_dir_path( __FILE__ ) . 'partials/debug.php';
    }

    public function create_tools_page() {
        wp_enqueue_style( WC_PPP_SLUG . '_admin' );
        $transients = Woocommerce_Pay_Per_Post_Debug::get_transients();
        if ( isset( $_POST['wc_ppp_create_table_nonce'] ) && wp_verify_nonce( $_POST['wc_ppp_create_table_nonce'], 'wc_ppp_create_table' ) ) {
            require_once WC_PPP_PATH . '/includes/class-woocommerce-pay-per-post-activator.php';
            Woocommerce_Pay_Per_Post_Activator::create_table__premium_only();
        }
        if ( isset( $_POST['wc_ppp_delete_transients_nonce'] ) && wp_verify_nonce( $_POST['wc_ppp_delete_transients_nonce'], 'wc_ppp_delete_transients' ) ) {
            Woocommerce_Pay_Per_Post_Debug::delete_transients();
        }
        if ( isset( $_POST['wc_ppp_reset_data_nonce'] ) && wp_verify_nonce( $_POST['wc_ppp_reset_data_nonce'], 'wc_ppp_reset_data' ) ) {
            Woocommerce_Pay_Per_Post_Tools::reset_data();
        }
        if ( isset( $_POST['wc_ppp_reset_page_views_nonce'] ) && wp_verify_nonce( $_POST['wc_ppp_reset_page_views_nonce'], 'wc_ppp_reset_page_views' ) ) {
            Woocommerce_Pay_Per_Post_Tools::reset_page_views();
        }
        if ( isset( $_POST['wc_ppp_delete_log_nonce'] ) && wp_verify_nonce( $_POST['wc_ppp_delete_log_nonce'], 'wc_ppp_delete_log' ) ) {
            Woocommerce_Pay_Per_Post_Tools::delete_log();
        }
        require_once plugin_dir_path( __FILE__ ) . 'partials/tools.php';
    }

    public function create_getting_started_page() {
        wp_enqueue_style( WC_PPP_SLUG . '_admin' );
        require_once plugin_dir_path( __FILE__ ) . 'partials/getting-started.php';
    }

    public function create_whatsnew_page() {
        wp_enqueue_style( WC_PPP_SLUG . '_admin' );
        $needs_upgrade = get_option( WC_PPP_SLUG . '_needs_upgrade', 'true' );
        $custom_post_types = get_option( WC_PPP_SLUG . '_custom_post_types', [] );
        $custom_post_types = ( empty( $custom_post_types ) ? [] : $custom_post_types );
        if ( !is_array( $custom_post_types ) ) {
            $custom_post_types = explode( ',', $custom_post_types );
        }
        $old_products = new WP_Query([
            'post_type' => $custom_post_types,
            'meta_key'  => 'woocommerce_ppp_product_id',
            'nopaging'  => true,
        ]);
        if ( isset( $_POST['wc_ppp_upgrade_nonce'] ) && wp_verify_nonce( $_POST['wc_ppp_upgrade_nonce'], 'wc_ppp_upgrade' ) ) {
            $this->upgrade_database( $old_products );
        }
        $readme = file_get_contents( WC_PPP_PATH . 'README.txt' );
        $changelog = explode( '== Changelog ==', $readme );
        $changelog = explode( '== ', $changelog[1] );
        $full_change_log = $changelog[0];
        $last_change = explode( '=', $full_change_log );
        $last_change = $last_change[2];
        require_once plugin_dir_path( __FILE__ ) . 'partials/whats-new.php';
    }

    /** @noinspection PhpUnused */
    public function meta_box() {
        if ( Woocommerce_Pay_Per_Post_Helper::allowed_roles_for_meta_box() ) {
            $post_types = $this->get_post_types();
            foreach ( $post_types as $post_type ) {
                add_meta_box(
                    WC_PPP_SLUG . '_meta_box',
                    __( 'Pay For Post with WooCommerce', 'wc_pay_per_post' ),
                    [$this, 'output_meta_box'],
                    $post_type,
                    'normal',
                    'high',
                    [
                        '__block_editor_compatible_meta_box' => true,
                    ]
                );
            }
        }
    }

    public function get_post_types() : array {
        return Woocommerce_Pay_Per_Post_Helper::get_post_types();
    }

    public function output_meta_box() {
        ob_start();
        global $post;
        $id = $post->ID;
        $enable_block_editor_integration = (bool) get_option( WC_PPP_SLUG . '_block_editor_enabled' );
        if ( $enable_block_editor_integration && WP_Screen::get()->is_block_editor() ) {
            $meta_box_file_name = 'meta-box.php';
            $restricted_content_override = get_post_meta( $id, WC_PPP_SLUG . '_restricted_content_override', true );
        } else {
            $selected = get_post_meta( $id, WC_PPP_SLUG . '_product_ids', true );
            $delay_restriction_enable = get_post_meta( $id, WC_PPP_SLUG . '_delay_restriction_enable', true );
            $delay_restriction = get_post_meta( $id, WC_PPP_SLUG . '_delay_restriction', true );
            $delay_restriction_frequency = get_post_meta( $id, WC_PPP_SLUG . '_delay_restriction_frequency', true );
            $page_view_restriction_enable = get_post_meta( $id, WC_PPP_SLUG . '_page_view_restriction_enable', true );
            $page_view_restriction = get_post_meta( $id, WC_PPP_SLUG . '_page_view_restriction', true );
            $page_view_restriction_frequency = get_post_meta( $id, WC_PPP_SLUG . '_page_view_restriction_frequency', true );
            $page_view_restriction_enable_time_frame = get_post_meta( $id, WC_PPP_SLUG . '_page_view_restriction_enable_time_frame', true );
            $page_view_restriction_time_frame = get_post_meta( $id, WC_PPP_SLUG . '_page_view_restriction_time_frame', true );
            $expire_restriction_enable = get_post_meta( $id, WC_PPP_SLUG . '_expire_restriction_enable', true );
            $expire_restriction = get_post_meta( $id, WC_PPP_SLUG . '_expire_restriction', true );
            $expire_restriction_frequency = get_post_meta( $id, WC_PPP_SLUG . '_expire_restriction_frequency', true );
            $show_warnings = get_post_meta( $id, WC_PPP_SLUG . '_show_warnings', true );
            $restricted_content_override = get_post_meta( $id, WC_PPP_SLUG . '_restricted_content_override', true );
            $drop_down = $this->generate_products_dropdown( $selected );
            wp_enqueue_style( WC_PPP_SLUG . '_admin' );
            wp_enqueue_style( WC_PPP_SLUG . '_select2' );
            wp_enqueue_script( WC_PPP_SLUG . '_select2' );
            wp_enqueue_script( WC_PPP_SLUG . '_admin' );
            $meta_box_file_name = 'meta-box-base.php';
        }
        require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/' . $meta_box_file_name;
        echo ob_get_clean();
    }

    /** @noinspection PhpUnused */
    public function save_meta_box( $post_id ) {
        // Stop the script when doing autosave.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        // Verify the nonce. If isn't there, stop the script.
        if ( !isset( $_POST[WC_PPP_SLUG . '_nonce'] ) || !wp_verify_nonce( $_POST[WC_PPP_SLUG . '_nonce'], WC_PPP_SLUG . '_nonce' ) ) {
            return;
        }
        // Stop the script if the user does not have edit permissions.
        if ( !current_user_can( 'edit_posts' ) ) {
            return;
        }
        if ( !isset( $_POST[WC_PPP_SLUG . '_classic_editor'] ) ) {
            $this->edit_post_block_editor( $post_id );
            $clear_transients = apply_filters( 'wc_pay_per_post_clear_transients_on_post_edit', true );
            if ( $clear_transients ) {
                Woocommerce_Pay_Per_Post_Debug::delete_transients();
            }
            return;
        }
        // Boolean Values.
        $_delay_restriction_enable = 0;
        $_page_view_restriction_enable = 0;
        $_page_view_restriction_enable_time_frame = 0;
        $_expire_restriction_enable = 0;
        $_show_warnings = 0;
        // Save the product_id's associated with page/post.
        $product_ids = $_POST[WC_PPP_SLUG . '_product_ids'] ?? '';
        $product_ids = $this->sanitize_product_ids( $product_ids );
        update_post_meta( $post_id, WC_PPP_SLUG . '_product_ids', $product_ids );
        $clear_transients = apply_filters( 'wc_pay_per_post_clear_transients_on_post_edit', true );
        if ( $clear_transients ) {
            Woocommerce_Pay_Per_Post_Debug::delete_transients();
        }
    }

    /** @noinspection PhpUnused */
    public function manage_custom_column( $column, $post_id ) {
        if ( $column === WC_PPP_SLUG . '_protected' ) {
            $protected = $this->is_protected( $post_id );
            if ( $protected['is_protected'] ) {
                echo Woocommerce_Pay_Per_Post_Helper::protection_display_icon( $protected['protection_type'] );
            }
        }
    }

    /**
     * @param $post_id
     *
     * @return array
     */
    public function is_protected( $post_id ) : array {
        $is_protected = false;
        $protection_type = 'standard';
        $origin_type = 'post';
        $standard_product_ids = get_post_meta( $post_id, WC_PPP_SLUG . '_product_ids', true );
        if ( is_array( $standard_product_ids ) && count( $standard_product_ids ) > 0 && !empty( $standard_product_ids[0] ) ) {
            $is_protected = true;
            $delay_restriction_enable = (bool) get_post_meta( $post_id, WC_PPP_SLUG . '_delay_restriction_enable', true );
            $page_view_restriction_enable = (bool) get_post_meta( $post_id, WC_PPP_SLUG . '_page_view_restriction_enable', true );
            $expire_restriction_enable = (bool) get_post_meta( $post_id, WC_PPP_SLUG . '_expire_restriction_enable', true );
            if ( $delay_restriction_enable ) {
                $protection_type = 'delay';
            } else {
                if ( $page_view_restriction_enable ) {
                    $protection_type = 'page-view';
                } else {
                    if ( $expire_restriction_enable ) {
                        $protection_type = 'expire';
                    }
                }
            }
        }
        if ( isset( $combined_product_ids ) ) {
            return [
                'product_ids'     => $combined_product_ids,
                'is_protected'    => $is_protected,
                'origin_type'     => $origin_type,
                'protection_type' => $protection_type,
            ];
        } else {
            return [
                'product_ids'     => $standard_product_ids,
                'is_protected'    => $is_protected,
                'origin_type'     => $origin_type,
                'protection_type' => $protection_type,
            ];
        }
    }

    /** @noinspection PhpUnused */
    public function manage_columns( $columns ) {
        $columns[WC_PPP_SLUG . '_protected'] = 'Pay For Post';
        return $columns;
    }

    /** @noinspection PhpUnused */
    public function sortable_columns( $columns ) {
        $columns[WC_PPP_SLUG . '_protected'] = WC_PPP_SLUG . '_protected';
        return $columns;
    }

    /** @noinspection PhpUnused */
    public function plugin_settings_link( $links ) {
        $url = admin_url( 'options-general.php?page=' . WC_PPP_SLUG );
        $_link = '<a href="' . $url . '">' . __( 'Settings', 'wc_pay_per_post' ) . '</a>';
        $links[] = $_link;
        return $links;
    }

    /** @noinspection PhpUnused */
    public function prefix_plugin_update_message( $data ) {
        if ( isset( $data['upgrade_notice'] ) ) {
            printf( '<div class="update-message">%s</div>', esc_html( wpautop( $data['upgrade_notice'] ) ) );
        }
    }

    protected function upgrade_database( $products ) {
        foreach ( $products->posts as $post ) {
            // Get old meta key for product id's associated with posts.
            $post_meta = get_post_meta( $post->ID, 'woocommerce_ppp_product_id', true );
            if ( '' !== $post_meta ) {
                // Added in to account for fields that were there but with no products associated with them.
                $old_ppp_ids = explode( ',', $post_meta );
                update_post_meta( $post->ID, WC_PPP_SLUG . '_product_ids', $old_ppp_ids );
            }
        }
        update_option( 'wc_pay_per_post_needs_upgrade', 'false', false );
        update_option( 'wc_pay_per_post_db_version', WC_PPP_VERSION, false );
        $url = admin_url( 'admin.php?page=' . WC_PPP_SLUG . '-whats-new&upgrade_complete=true' );
        wp_safe_redirect( $url );
    }

    protected function generate_products_dropdown( $selected = [] ) : string {
        $products = Woocommerce_Pay_Per_Post_Helper::get_products();
        $drop_down = '<select id="' . WC_PPP_SLUG . '_product_ids" name="' . WC_PPP_SLUG . '_product_ids[]" style="width: 100%" multiple="multiple">';
        $drop_down .= '<optgroup label="Products">';
        foreach ( $products as $product ) {
            $drop_down .= '<option value="' . $product['ID'] . '"';
            if ( in_array( (string) $product['ID'], (array) $selected, true ) ) {
                $drop_down .= ' selected="selected"';
            }
            $drop_down .= '>' . $product['post_title'] . ' - [#' . $product['ID'] . ']</option>';
        }
        $drop_down .= '</optgroup>';
        $drop_down .= '</select>';
        return $drop_down;
    }

    /**
     * @param $product_ids
     *
     * @return array|string
     */
    private function sanitize_product_ids( $product_ids ) {
        if ( is_array( $product_ids ) ) {
            $return = [];
        } else {
            return '';
        }
        foreach ( $product_ids as $product_id ) {
            if ( is_numeric( $product_id ) ) {
                $return[] = $product_id;
            }
        }
        return $return;
    }

    /** @noinspection PhpUnused */
    public function admin_notices() {
        $enable_debugging = (bool) get_option( WC_PPP_SLUG . '_enable_debugging', false );
        if ( $enable_debugging ) {
            $class = 'notice notice-error is-dismissible';
            $message = __( 'You currently have the WooCommerce Pay for Post Debug enabled.  This can pose a performance and security risk.  Please disable if not actively debugging.', 'sample-text-domain' );
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
        }
    }

}
