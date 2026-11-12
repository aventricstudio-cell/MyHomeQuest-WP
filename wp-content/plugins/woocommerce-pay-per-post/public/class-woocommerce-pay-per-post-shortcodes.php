<?php

use Elementor\Plugin;
use PRAMADILLO\Woocommerce_Pay_Per_Post_Restrict_Content;
/**
 * Class Woocommerce_Pay_Per_Post_Shortcodes
 */
class Woocommerce_Pay_Per_Post_Shortcodes {
    public static function register_shortcodes() {
        add_shortcode( 'woocommerce-payperpost', [__CLASS__, 'process_shortcode'] );
        add_shortcode( 'wc-pay-for-post', [__CLASS__, 'process_shortcode'] );
    }

    public static function process_shortcode( $atts ) {
        $post_id = get_the_ID();
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/process_shortcode() called. - BACKTRACE - Called From - ' . print_r( debug_backtrace()[1]['function'], true ) );
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/process_shortcode() supplied attributes ', $atts );
        global $product_ids;
        $template = 'purchased';
        $orderby = 'date';
        $order = 'DESC';
        $transient = '';
        $transients = [];
        $bypass_transients = false;
        if ( isset( $atts['bypass_transients'] ) && ($atts['bypass_transients'] === 'TRUE' || $atts['bypass_transients'] === true || $atts['bypass_transients'] === 'true') ) {
            $bypass_transients = true;
        }
        if ( isset( $atts['template'] ) && array_key_exists( $atts['template'], self::available_templates() ) ) {
            $template = $atts['template'];
        }
        $custom_post_types = Woocommerce_Pay_Per_Post_Helper::get_protected_post_types();
        if ( !is_array( $custom_post_types ) ) {
            $custom_post_types = explode( ',', $custom_post_types );
        }
        $args = [
            'orderby'     => $orderby,
            'order'       => $order,
            'nopaging'    => true,
            'meta_query'  => [[
                'key'     => WC_PPP_SLUG . '_product_ids',
                'value'   => '',
                'compare' => '!=',
            ]],
            'post_status' => 'publish',
            'post_type'   => $custom_post_types,
        ];
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/process_shortcode() query args ', $args );
        $get_ppp_args = apply_filters( 'wc_pay_per_post_args', $args );
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/process_shortcode() query args AFTER FILTER ', $args );
        //			echo '<pre>'.print_r($get_ppp_args, true).'</pre>';
        $ppp_posts = Woocommerce_Pay_Per_Post_Helper::get_protected_posts( $get_ppp_args, $transient, $bypass_transients );
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/process_shortcode() $ppp_posts contains ' . count( $ppp_posts ) . ' posts.' );
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/process_shortcode() $ppp_posts contains ' . json_encode( $ppp_posts ) . ' posts.' );
        //			echo '<pre>'.print_r($ppp_posts, true).'</pre>';
        ob_start();
        switch ( $template ) {
            case 'has_access':
                self::shortcode_has_access( $template, $ppp_posts );
                break;
            case 'purchased':
                self::shortcode_purchased( $template, $ppp_posts );
                break;
            case 'purchased-datatables':
                self::shortcode_purchased_datatables( $template, $ppp_posts );
                break;
            case 'remaining':
                self::shortcode_remaining( $template, $ppp_posts );
                break;
            case 'all':
                self::shortcode_all( $template, $ppp_posts );
                break;
        }
        return ob_get_clean();
    }

    /**
     * @param $template
     * @param $ppp_posts
     */
    protected static function shortcode_purchased( $template, $ppp_posts ) {
        $post_id = get_the_ID();
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/shortcode_purchased() called. - BACKTRACE - Called From - ' . print_r( debug_backtrace()[1]['function'], true ) );
        $purchased = [];
        if ( is_user_logged_in() ) {
            foreach ( $ppp_posts as $post ) {
                if ( Woocommerce_Pay_Per_Post_Helper::has_purchased( $post->ID, false ) ) {
                    Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/shortcode_purchased() checking if ' . $post->ID . ' has been purchased.  HAS BEEN PURCHASED' );
                    /** @noinspection PhpArrayWriteIsNotUsedInspection */
                    $purchased[] = $post;
                } else {
                    Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/shortcode_purchased() checking if ' . $post->ID . ' HAS NOT BEEN PURCHASED' );
                }
            }
            require Woocommerce_Pay_Per_Post_Helper::locate_template( self::available_templates()[$template], '', WC_PPP_PATH . 'public/partials/' );
        }
    }

    /**
     * @param $template
     * @param $ppp_posts
     *
     * @noinspection PhpArrayAccessCanBeReplacedWithForeachValueInspection*/
    protected static function shortcode_purchased_datatables( $template, $ppp_posts ) {
        $post_id = get_the_ID();
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/shortcode_purchased_datatables() called. - BACKTRACE - Called From - ' . print_r( debug_backtrace()[1]['function'], true ) );
        $purchased = [];
        if ( is_user_logged_in() ) {
            foreach ( $ppp_posts as $post ) {
                if ( Woocommerce_Pay_Per_Post_Helper::has_purchased( $post->ID, false ) ) {
                    Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/shortcode_purchased_datatables() checking if ' . $post->ID . ' has been purchased.  HAS BEEN PURCHASED' );
                    $purchased[] = $post;
                } else {
                    Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/shortcode_purchased_datatables() checking if ' . $post->ID . ' HAS NOT BEEN PURCHASED' );
                }
            }
            foreach ( $purchased as $key => $post ) {
                $products = Woocommerce_Pay_Per_Post_Helper::get_product_ids_by_post_id( $post->ID );
                foreach ( $products['product_ids'] as $product ) {
                    $get_product_terms = get_the_terms( $product, 'product_cat' );
                    foreach ( $get_product_terms as $product_term ) {
                        $purchased[$key]->product_terms[] = $product_term->name;
                    }
                }
            }
            require Woocommerce_Pay_Per_Post_Helper::locate_template( self::available_templates()[$template], '', WC_PPP_PATH . 'public/partials/' );
        }
    }

    /**
     * @param $template
     * @param $ppp_posts
     */
    protected static function shortcode_has_access( $template, $ppp_posts ) {
        $post_id = get_the_ID();
        Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/shortcode_has_access() called. - BACKTRACE - Called From - ' . print_r( debug_backtrace()[1]['function'], true ) );
        $purchased = [];
        if ( is_user_logged_in() ) {
            foreach ( $ppp_posts as $post ) {
                if ( Woocommerce_Pay_Per_Post_Helper::has_access( $post->ID, false ) ) {
                    Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/shortcode_has_access() checking if ' . $post->ID . ' has access.  HAS Access' );
                    /** @noinspection PhpArrayWriteIsNotUsedInspection */
                    $purchased[] = $post;
                } else {
                    Woocommerce_Pay_Per_Post_Helper::logger( 'Post ID: ' . $post_id . ' - SHORTCODE: Woocommerce_Pay_Per_Post_Shortcodes/shortcode_has_access() checking if ' . $post->ID . ' has access.  DOES NOT HAVE Access' );
                }
            }
            require Woocommerce_Pay_Per_Post_Helper::locate_template( self::available_templates()[$template], '', WC_PPP_PATH . 'public/partials/' );
        }
    }

    /**
     * @param $template
     * @param $ppp_posts
     */
    protected static function shortcode_remaining( $template, $ppp_posts ) {
        $remaining = [];
        if ( is_user_logged_in() ) {
            foreach ( $ppp_posts as $post ) {
                if ( !Woocommerce_Pay_Per_Post_Helper::has_access( $post->ID, false ) ) {
                    /** @noinspection PhpArrayWriteIsNotUsedInspection */
                    $remaining[] = $post;
                }
            }
            require Woocommerce_Pay_Per_Post_Helper::locate_template( self::available_templates()[$template], '', WC_PPP_PATH . 'public/partials/' );
        }
    }

    /**
     * @param $template
     * @param $ppp_posts
     *
     * @noinspection PhpUnusedParameterInspection*/
    protected static function shortcode_all( $template, $ppp_posts ) {
        require Woocommerce_Pay_Per_Post_Helper::locate_template( self::available_templates()[$template], '', WC_PPP_PATH . 'public/partials/' );
    }

    private static function available_templates() : array {
        return [
            'purchased'            => 'shortcode-purchased.php',
            'purchased-datatables' => 'shortcode-purchased-datatables.php',
            'has_access'           => 'shortcode-has_access.php',
            'all'                  => 'shortcode-all.php',
            'remaining'            => 'shortcode-remaining.php',
        ];
    }

}
