<?php

use Elementor\Plugin;
use PRAMADILLO\Woocommerce_Pay_Per_Post_Restrict_Content;
use Pramadillo\PayForPost\Carbon\Carbon;
use Automattic\WooCommerce\Utilities\OrderUtil;
/**
 * Class Woocommerce_Pay_Per_Post_General
 */
class Woocommerce_Pay_Per_Post_Helper extends Woocommerce_Pay_Per_Post {
    /**
     * @var array
     */
    public static $protection_types = [
        'standard',
        'delay',
        'page-view',
        'expire'
    ];

    public static function is_protected( $post_id = null ) : array {
        global $product_ids;
        if ( is_null( $post_id ) ) {
            $post_id = get_the_ID();
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::is_protected() is called' );
        if ( isset( $product_ids['is_protected'] ) && !empty( $product_ids['is_protected'] ) ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::is_protected() Global Product IDs set' );
            return [
                'is_protected'    => $product_ids['is_protected'],
                'origin_type'     => $product_ids['origin_type'],
                'protection_type' => $product_ids['protection_type'],
            ];
        } else {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::is_protected() Global Product IDs NOT SET pulling product_ids from Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id()' );
            return self::get_product_ids_by_post_id( $post_id );
        }
    }

    /**
     * Check if a Post have a product related
     *
     * @param $post_id
     *
     * @return bool
     * @noinspection PhpUnused
     */
    public static function have_post_products( $post_id ) : bool {
        $cache_key = WC_PPP_SLUG . '_have_post_products' . $post_id;
        $result = wp_cache_get( $cache_key, WC_PPP_SLUG );
        if ( $result === false ) {
            $selected = (array) get_post_meta( $post_id, WC_PPP_SLUG . '_product_ids' );
            $product_ids = array_filter( $selected, function ( $item ) {
                return !empty( $item );
            } );
            wp_cache_set( $cache_key, $product_ids, WC_PPP_SLUG );
        }
        return $result;
    }

    /** @noinspection PhpUnusedParameterInspection
     * @noinspection PhpUnused
     */
    public static function get_protection_type( $id, $type = 'post', $meta = null ) : string {
        $delay_restriction_enable = false;
        $page_view_restriction_enable = false;
        $expire_restriction_enable = false;
        switch ( $type ) {
            case 'post':
            case 'elementor':
                //TODO this will need to be updated to account for types of protection on individual elements
                $delay_restriction_enable = (bool) get_post_meta( $id, WC_PPP_SLUG . '_delay_restriction_enable', true );
                $page_view_restriction_enable = (bool) get_post_meta( $id, WC_PPP_SLUG . '_page_view_restriction_enable', true );
                $expire_restriction_enable = (bool) get_post_meta( $id, WC_PPP_SLUG . '_expire_restriction_enable', true );
                break;
        }
        $protection = 'standard';
        if ( $delay_restriction_enable ) {
            $protection = 'delay';
        }
        if ( $page_view_restriction_enable ) {
            $protection = 'page-view';
        }
        if ( $expire_restriction_enable ) {
            $protection = 'expire';
        }
        return $protection;
    }

    /**
     * @param null $post_id
     * @param bool $track_page_view *
     *
     * @return bool
     * The can_user_view_content function returns on whether the user should see the paywall.
     * For this that is why we are returning the inverse of the result.
     */
    public static function has_access( $post_id = null, bool $track_page_view = true ) : bool {
        $restrict = new Woocommerce_Pay_Per_Post_Restrict_Content($post_id, $track_page_view);
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Helper::has_access  - RETURNING - ' . var_export( !$restrict->can_user_view_content(), true ) );
        return $restrict->can_user_view_content();
    }

    /**
     * Checks if the user has purchased a product associated with the post
     *
     * @param null $post_id
     * @param bool $track_page_view
     *
     * @return bool
     */
    public static function has_purchased( $post_id = null, bool $track_page_view = true ) : bool {
        $restrict = new Woocommerce_Pay_Per_Post_Restrict_Content($post_id, $track_page_view);
        return $restrict->check_if_purchased();
    }

    /**
     * @return string
     */
    public static function get_no_access_content() : string {
        $restrict = new Woocommerce_Pay_Per_Post_Restrict_Content();
        return $restrict->show_paywall( get_the_content() );
    }

    /**
     * @param $type
     *
     * @return bool|string
     */
    public static function protection_display_icon( $type ) {
        if ( in_array( $type, self::$protection_types, true ) ) {
            switch ( $type ) {
                case 'standard':
                    return '<span class="dashicons dashicons-post-status" title="Standard Purchase Protection" style="color:green"></span>';
                case 'delay':
                    return '<span class="dashicons dashicons-clock" title="Delay Protection" style="color:green"></span>';
                case 'page-view':
                    return '<span class="dashicons dashicons-visibility" title="Page View Protection" style="color:green"></span>';
                case 'expire':
                    return '<span class="dashicons dashicons-backup" title="Expiry Protection" style="color:green"></span>';
            }
        }
        return false;
    }

    /**
     * @return Carbon
     */
    public static function current_time() : Carbon {
        return Carbon::now();
    }

    /**
     * @return string
     */
    public static function date_time_format() : string {
        return get_option( 'date_format', true ) . ' ' . get_option( 'time_format', true );
    }

    public static function logger( $message, $context = null ) {
        $logger = new Woocommerce_Pay_Per_Post_Logger();
        if ( !is_array( $context ) ) {
            $context = [];
        }
        $logger->log( $message, $context );
    }

    public static function logger_uri() : string {
        $logger = new Woocommerce_Pay_Per_Post_Logger();
        return $logger->get_log_uri();
    }

    public static function logger_url() : string {
        $logger = new Woocommerce_Pay_Per_Post_Logger();
        return $logger->get_log_url();
    }

    public static function get_protected_post_types() {
        $custom_post_types = get_option( WC_PPP_SLUG . '_custom_post_types', [] );
        return ( empty( $custom_post_types ) ? [] : $custom_post_types );
    }

    public static function get_protected_post_types_args() : array {
        $custom_post_types = self::get_protected_post_types();
        if ( !is_array( $custom_post_types ) ) {
            $custom_post_types = explode( ',', $custom_post_types );
        }
        return [
            'orderby'     => 'post_date',
            'order'       => 'DESC',
            'nopaging'    => true,
            'post_status' => 'publish',
            'post_type'   => $custom_post_types,
        ];
    }

    public static function get_protected_posts( $args = null, $transient = 'posts', $bypass_transient = false ) : array {
        $transient = WC_PPP_SLUG . '_' . $transient;
        if ( is_null( $args ) ) {
            $args = self::get_protected_post_types_args();
            $args['meta_query'] = [[
                'key'     => WC_PPP_SLUG . '_product_ids',
                'value'   => '',
                'compare' => '!=',
            ]];
        }
        if ( $bypass_transient ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Woocommerce_Pay_Per_Post_Helper::get_protected_posts() returning posts BYPASSING TRANSIENT' );
            $ppp_posts = get_posts( $args );
        } else {
            $ppp_posts = get_transient( $transient );
            Woocommerce_Pay_Per_Post_Helper::logger( 'Woocommerce_Pay_Per_Post_Helper::get_protected_posts() returning posts FROM ' . $transient . ' TRANSIENT' );
            if ( false === $ppp_posts ) {
                $ppp_posts = get_posts( $args );
                Woocommerce_Pay_Per_Post_Helper::logger( 'Woocommerce_Pay_Per_Post_Helper::get_protected_posts() returning posts FROM REGENERATING ' . $transient . ' TRANSIENT' );
                set_transient( $transient, $ppp_posts, apply_filters( 'wc_pay_per_post_posts_transient_time', HOUR_IN_SECONDS ) );
            }
        }
        return (array) $ppp_posts;
    }

    public static function get_posts_associated_with_product_id( $product_id ) : array {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Product ID: ' . $product_id . ' - Woocommerce_Pay_Per_Post_Helper::get_posts_associated_with_product_id() Called' );
        $args = self::get_protected_post_types_args();
        $args['meta_query'] = [[
            'key'     => WC_PPP_SLUG . '_product_ids',
            'value'   => sprintf( '^%1$s$|s:%2$u:"%1$s";', $product_id, strlen( $product_id ) ),
            'compare' => 'REGEXP',
        ]];
        $posts = get_posts( apply_filters( 'wc_pay_per_post_woocommerce_email_args', $args ) );
        $protected_content = [];
        if ( $posts ) {
            $interface = 'default';
            switch ( $interface ) {
                case "default":
                default:
                    $protected_content = self::get_posts_associated_with_product_id_interface( $posts );
                    break;
                case "polylang":
                    $protected_content = self::get_posts_associated_with_product_id_interface_polylang__premium_only( $posts );
                    break;
            }
        }
        return $protected_content;
    }

    public static function can_use_woocommerce_memberships() : bool {
        return false;
    }

    public static function can_use_woocommerce_subscriptions() : bool {
        return false;
    }

    public static function can_use_paid_membership_pro() : bool {
        return false;
    }

    public static function can_use_elementor() : bool {
        return false;
    }

    public static function md_array_diff( $arraya, $arrayb ) {
        foreach ( $arraya as $keya => $valuea ) {
            if ( in_array( $valuea, $arrayb ) ) {
                unset($arraya[$keya]);
            }
        }
        return $arraya;
    }

    public static function move_to_top( &$array, $key ) {
        $temp = [
            $key => $array[$key],
        ];
        unset($array[$key]);
        $array = $temp + $array;
    }

    protected static function get_posts_associated_with_product_id_interface( $posts ) : array {
        $protected_content = [];
        foreach ( $posts as $post ) {
            $protected_content[] = [
                'post_id'    => $post->ID,
                'post_title' => $post->post_title,
                'post_url'   => get_permalink( $post->ID ),
            ];
        }
        return $protected_content;
    }

    public static function locate_template( $template_name, $template_path = '', $default_path = '' ) {
        if ( !$template_path ) {
            $template_path = WC_PPP_TEMPLATE_PATH;
        }
        if ( !$default_path ) {
            $default_path = WC_PPP_PATH;
        }
        // Look within passed path within the theme - this is priority.
        $template = locate_template( [trailingslashit( $template_path ) . $template_name, $template_name] );
        // Get default template/.
        if ( !$template ) {
            $template = $default_path . $template_name;
        }
        // Return what we found.
        return apply_filters(
            'wc_pay_for_post_locate_template',
            $template,
            $template_name,
            $template_path
        );
    }

    public static function get_all_products() : array {
        $args = [
            'post_type'   => ['product'],
            'orderby'     => 'title',
            'post_status' => 'publish',
            'order'       => 'ASC',
            'nopaging'    => true,
        ];
        $products = get_posts( apply_filters( 'wc_pay_per_post_all_product_args', $args ) );
        $return = [];
        foreach ( $products as $product ) {
            $return[] = [
                'ID'         => $product->ID,
                'post_title' => $product->post_title,
            ];
        }
        return $return;
    }

    public static function get_virtual_products() : array {
        $args = [
            'post_type'   => ['product'],
            'post_status' => 'publish',
            'orderby'     => 'title',
            'order'       => 'ASC',
            'nopaging'    => true,
            'meta_query'  => [
                'relation' => 'OR',
                [
                    'key'     => '_downloadable',
                    'value'   => 'yes',
                    'compare' => '=',
                ],
                [
                    'key'     => '_virtual',
                    'value'   => 'yes',
                    'compare' => '=',
                ],
                [
                    'key'     => '_downloadable',
                    'value'   => '1',
                    'compare' => '=',
                ],
                [
                    'key'     => '_virtual',
                    'value'   => '1',
                    'compare' => '=',
                ],
            ],
        ];
        $products = get_posts( apply_filters( 'wc_pay_per_post_virtual_product_args', $args ) );
        $return = [];
        foreach ( $products as $product ) {
            $return[] = [
                'ID'         => $product->ID,
                'post_title' => $product->post_title,
            ];
        }
        return $return;
    }

    public static function get_products() {
        $only_show_virtual_products = (bool) get_option( WC_PPP_SLUG . '_only_show_virtual_products', false );
        if ( $only_show_virtual_products ) {
            return apply_filters( 'wc_pay_per_post_get_virtual_products', Woocommerce_Pay_Per_Post_Helper::get_virtual_products() );
        } else {
            return apply_filters( 'wc_pay_per_post_get_all_products', Woocommerce_Pay_Per_Post_Helper::get_all_products() );
        }
    }

    public static function replace_tokens( $paywall_content, $product_ids, $unfiltered_content = null ) {
        $parent_id = null;
        if ( isset( $product_ids[0] ) ) {
            $parent_id = wp_get_post_parent_id( $product_ids[0] );
        }
        if ( is_archive() || is_home() || is_front_page() ) {
            //Get Product IDs
            //TODO this needs to be updated to newest methods that include Elementor
            $product_ids = get_post_meta( get_the_ID(), WC_PPP_SLUG . '_product_ids', true );
        }
        if ( count( (array) $product_ids ) > 0 ) {
            $return_content = str_replace( '{{product_id}}', implode( ',', (array) $product_ids ), $paywall_content );
        } else {
            $return_content = str_replace( '{{product_id}}', reset( $product_ids ), $paywall_content );
        }
        if ( !is_null( $parent_id ) ) {
            $return_content = str_replace( '{{parent_id}}', $parent_id, $return_content );
        }
        return $return_content;
    }

    /**
     * @param $method
     *
     * @return string
     */
    public static function carbon_add_method( $method ) : string {
        $methods = [
            'minute' => 'addMinutes',
            'hour'   => 'addHours',
            'day'    => 'addDays',
            'week'   => 'addWeeks',
            'month'  => 'addMonths',
            'year'   => 'addYears',
        ];
        if ( "" === $method ) {
            return $methods['day'];
        }
        return $methods[$method];
    }

    /**
     * @param $method
     *
     * @return string
     */
    public static function carbon_diff_method( $method ) : string {
        $methods = [
            'minute' => 'diffInMinutes',
            'hour'   => 'diffInHours',
            'day'    => 'diffInDays',
            'week'   => 'diffInWeeks',
            'month'  => 'diffInMonths',
            'year'   => 'diffInYears',
        ];
        if ( "" === $method ) {
            return $methods['day'];
        }
        return $methods[$method];
    }

    /** @noinspection PhpUnused */
    public static function recursive_array_search( $needle, array $haystack ) {
        $matches = [];
        $iterator = new RecursiveArrayIterator($haystack);
        $recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
        foreach ( $recursive as $key => $value ) {
            if ( $key === $needle ) {
                $matches[] = $value;
            }
        }
        $return = [];
        foreach ( $matches as $match ) {
            $return = array_merge( $return, $match );
        }
        return $return ?? false;
    }

    /**
     * Updated Function for 3.1.0
     *
     * @param $post_id
     *
     * @return array
     */
    public static function get_elementor_product_ids( $post_id ) : array {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_elementor_product_ids() Called' );
        $product_ids = [];
        $elementor_data = get_post_meta( $post_id, '_elementor_data', true );
        $check_string = $elementor_data;
        if ( is_array( $elementor_data ) ) {
            $check_string = json_encode( $elementor_data );
        }
        if ( !is_null( $elementor_data ) && false !== strpos( $check_string, '"wc_pay_per_post_enable":"yes"' ) ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_elementor_product_ids() - wc_pay_per_post_enable":"yes" FOUND' );
            //Uncomment this for full Elementor Data Debug.  Log file grows QUICK, so use sparingly.
            //Woocommerce_Pay_Per_Post_Helper::logger('Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_elementor_product_ids() - Elementor Data ' . print_r($elementor_data, true));
            $document = Plugin::$instance->documents->get( $post_id );
            //Loop Through all Elementor elements to check for protected elements.
            Plugin::$instance->db->iterate_data( $document->get_elements_data(), function ( $element_data ) use(&$product_ids, $post_id) {
                $element = Plugin::$instance->elements_manager->create_element_instance( $element_data );
                //Uncomment this for full Elementor Data Debug.  Log file grows QUICK, so use sparingly.
                //Woocommerce_Pay_Per_Post_Helper::logger('Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_elementor_product_ids() - $element ' . print_r($element, true));
                // If the widget/element isn't exist, like a plugin that creates a widget but deactivated
                if ( !$element ) {
                    return null;
                }
                $data = $element->get_data( 'settings' );
                //Uncomment this for full Elementor Data Debug.  Log file grows QUICK, so use sparingly.
                //Woocommerce_Pay_Per_Post_Helper::logger('Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_elementor_product_ids() - $data = $element->get_data(); ' . print_r($data, true));
                if ( isset( $data['wc_pay_per_post_enable'] ) && $data['wc_pay_per_post_enable'] === 'yes' ) {
                    if ( isset( $data['wc_pay_per_post_select_products'] ) && (array) count( $data['wc_pay_per_post_select_products'] ) > 0 ) {
                        $product_ids = $data['wc_pay_per_post_select_products'];
                    }
                }
            } );
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_elementor_product_ids() - ACTUAL $product_ids = ' . print_r( $product_ids, true ) );
        } else {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_elementor_product_ids() - wc_pay_per_post_enable":"yes" NOT FOUND' );
        }
        return $product_ids;
    }

    public static function get_post_types() : array {
        $user_included_post_types = get_option( WC_PPP_SLUG . '_custom_post_types', [] );
        if ( empty( $user_included_post_types ) ) {
            $user_included_post_types = [];
        }
        return (array) $user_included_post_types;
    }

    public static function allowed_roles_for_meta_box() : bool {
        $allowed_roles = apply_filters( 'wc_pay_per_post_allowed_roles_for_meta_box', [] );
        if ( count( $allowed_roles ) == 0 ) {
            $allow_meta = true;
        } else {
            $allow_meta = false;
            $user = wp_get_current_user();
            if ( !is_null( $user ) ) {
                $user_roles = $user->roles;
                foreach ( $user_roles as $role ) {
                    if ( array_key_exists( $role, $allowed_roles ) ) {
                        $allow_meta = true;
                    }
                }
            }
        }
        return $allow_meta;
    }

    /**
     * @return bool
     */
    public static function is_an_allowed_protected_post_type() : bool {
        return in_array( get_post_type(), self::get_post_types() );
    }

    /**
     * @param null $post_id
     *
     * @return array
     */
    public static function get_product_ids_by_post_id( $post_id = null ) : array {
        $bypass_allowed_protected_post_types = false;
        if ( is_null( $post_id ) ) {
            $bypass_allowed_protected_post_types = true;
            $post_id = get_the_ID();
        }
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id() Called' );
        if ( $bypass_allowed_protected_post_types && !self::is_an_allowed_protected_post_type() ) {
            $product_ids = [
                'product_ids'         => [],
                'section_product_ids' => [],
                'is_protected'        => false,
                'origin_type'         => '',
                'protection_type'     => '',
            ];
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id() NOT in Protected Post Types Array.  Bailing.' );
            return $product_ids;
        }
        if ( self::can_use_elementor() ) {
            if ( Plugin::$instance->editor->is_edit_mode() || Plugin::$instance->preview->is_preview_mode() ) {
                $product_ids = [
                    'product_ids'         => '',
                    'section_product_ids' => '',
                    'is_protected'        => false,
                    'origin_type'         => '',
                    'protection_type'     => '',
                ];
                Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id() In Elementor Edit/Preview Mode.  Bailing.' );
                return $product_ids;
            }
        }
        $origin_type = 'post';
        $protection_type = '';
        $is_protected = false;
        $delay_restriction_enable = (bool) get_post_meta( $post_id, WC_PPP_SLUG . '_delay_restriction_enable', true );
        $page_view_restriction_enable = (bool) get_post_meta( $post_id, WC_PPP_SLUG . '_page_view_restriction_enable', true );
        $expire_restriction_enable = (bool) get_post_meta( $post_id, WC_PPP_SLUG . '_expire_restriction_enable', true );
        $standard_product_ids = get_post_meta( $post_id, WC_PPP_SLUG . '_product_ids', true );
        if ( is_array( $standard_product_ids ) ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id() Standard Post Product IDs ', $standard_product_ids );
        } else {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id() Standard Post Product IDs ' . print_r( $standard_product_ids, true ) );
        }
        if ( is_array( $standard_product_ids ) && count( $standard_product_ids ) > 0 && !empty( $standard_product_ids[0] ) ) {
            Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id() Product IDs $selected array contains items' );
            $is_protected = true;
            if ( $delay_restriction_enable ) {
                $protection_type = 'delay';
                Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id() Protection Type: DELAY' );
            } else {
                if ( $page_view_restriction_enable ) {
                    $protection_type = 'page-view';
                    Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id() Protection Type: PAGE VIEW' );
                } else {
                    if ( $expire_restriction_enable ) {
                        $protection_type = 'expire';
                        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id() Protection Type: EXPIRE' );
                    } else {
                        $protection_type = 'standard';
                        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id() Protection Type: STANDARD' );
                    }
                }
            }
        }
        $elementor_product_ids = [];
        $return = [
            'product_ids'         => $standard_product_ids,
            'section_product_ids' => $elementor_product_ids,
            'is_protected'        => $is_protected,
            'origin_type'         => $origin_type,
            'protection_type'     => $protection_type,
        ];
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id() Returned Product IDs array', $return );
        return $return;
    }

    /**
     * Determines if HOPS storage is enabled for WooCommerce
     * @return bool
     */
    public static function hops_enabled() {
        return OrderUtil::custom_orders_table_usage_is_enabled();
    }

    public static function is_admin_request() {
        $current_url = home_url( add_query_arg( null, null ) );
        $admin_url = strtolower( admin_url() );
        $referrer = strtolower( wp_get_referer() );
        $prefix = rest_get_url_prefix();
        $is_rest = defined( 'REST_REQUEST' ) && REST_REQUEST || isset( $_GET['rest_route'] ) && strpos( trim( $_GET['rest_route'], '\\/' ), $prefix ) === 0 || strpos( wp_parse_url( trailingslashit( rest_url() ) )['path'], wp_parse_url( add_query_arg( [] ) )['path'] ) === 0;
        $requestFromBackend = $is_rest && strpos( $admin_url, '/wp-admin/' ) > 0 && !strpos( $admin_url, '/wp-admin/admin-ajax.php' );
        if ( $requestFromBackend ) {
            return true;
        }
        if ( 0 === strpos( $current_url, $admin_url ) ) {
            if ( 0 === strpos( $referrer, $admin_url ) ) {
                return true;
            } else {
                if ( function_exists( 'wp_doing_ajax' ) ) {
                    return wp_doing_ajax();
                } else {
                    return !(defined( 'DOING_AJAX' ) && DOING_AJAX);
                }
            }
        } else {
            return false;
        }
    }

    public static function get_last_purchase_date( $post_id, $user_id = null ) {
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . get_the_ID() . ' - Woocommerce_Pay_Per_Post_Helper/get_last_purchase_date - Called' );
        // This feature requires premium
        if ( !wcppp_freemius()->is__premium_only() || !wcppp_freemius()->can_use_premium_code() ) {
            return null;
        }
        if ( empty( $user_id ) ) {
            $user_id = get_current_user_id();
        }
        // Call the secure version from Restrict_Content class
        return \PRAMADILLO\Woocommerce_Pay_Per_Post_Restrict_Content::get_last_purchase_date__premium_only( $post_id, $user_id );
    }

    public static function customer_has_purchased_product( $user_email, $user_id, $product_id_or_variation_id ) {
        if ( empty( $product_id_or_variation_id ) || empty( $user_id ) ) {
            return false;
        }
        // Use WooCommerce's built-in function for versions outside 9.6.0 - 9.6.1
        if ( !defined( 'WC_VERSION' ) || version_compare( WC_VERSION, '9.6.0', '<' ) || version_compare( WC_VERSION, '9.6.1', '>' ) ) {
            return wc_customer_bought_product( $user_email, $user_id, $product_id_or_variation_id );
        }
        // If WooCommerce is specifically 9.6.0 or 9.6.1, use custom SQL query
        global $wpdb;
        $user_id = absint( $user_id );
        $product_id_or_variation_id = absint( $product_id_or_variation_id );
        // Get paid order statuses
        $statuses = array_map( fn( $status ) => "wc-{$status}", wc_get_is_paid_statuses() );
        $status_sql = "'" . implode( "','", $statuses ) . "'";
        // Preformat for SQL
        if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
            // HOPS enabled - Query modern order tables
            $order_table = \Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore::get_orders_table_name();
            $query = $wpdb->prepare(
                "\n        SELECT COUNT(*)\n        FROM {$wpdb->prefix}wc_order_product_lookup\n        LEFT JOIN {$order_table} AS orders ON orders.id={$wpdb->prefix}wc_order_product_lookup.order_id\n        LEFT JOIN {$wpdb->prefix}wc_customer_lookup ON {$wpdb->prefix}wc_customer_lookup.customer_id={$wpdb->prefix}wc_order_product_lookup.customer_id\n        WHERE \n        {$wpdb->prefix}wc_customer_lookup.user_id= %d \n        AND ({$wpdb->prefix}wc_order_product_lookup.product_id = %d OR {$wpdb->prefix}wc_order_product_lookup.variation_id = %d)\n        AND orders.status IN ({$status_sql})\n        ",
                $user_id,
                $product_id_or_variation_id,
                $product_id_or_variation_id
            );
        } else {
            // Legacy WooCommerce - Query posts/meta tables
            $query = $wpdb->prepare( "\n        SELECT COUNT(*) \n        FROM {$wpdb->prefix}posts AS orders\n        INNER JOIN {$wpdb->prefix}postmeta AS meta ON orders.ID = meta.post_id\n        INNER JOIN {$wpdb->prefix}woocommerce_order_items AS items ON orders.ID = items.order_id\n        INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS itemmeta ON items.order_item_id = itemmeta.order_item_id\n        WHERE orders.post_type = 'shop_order'\n        AND orders.post_status IN ({$status_sql})\n        AND meta.meta_key = '_customer_user'\n        AND meta.meta_value = %d\n        AND (\n            itemmeta.meta_key IN ('_product_id', '_variation_id')\n            AND itemmeta.meta_value = %d\n        )\n        ", $user_id, $product_id_or_variation_id );
        }
        return $wpdb->get_var( $query ) > 0;
    }

}
